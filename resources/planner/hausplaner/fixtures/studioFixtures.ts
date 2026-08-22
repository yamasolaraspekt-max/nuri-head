/**
 * Studio-Fixtures — deterministische, benannte SceneDocuments für die Sicht-Abnahme (`?fixture=<name>`).
 *
 * Zweck: Der Evaluator lädt eine feste Szene reproduzierbar (kein manuelles Klicken), kombiniert mit
 * `?capture=1` → `window.__hausplanerSnapshot3d()` → deterministischer 3D-Frame. Damit ist die visuelle
 * U-Platzierung (und künftig Decke-Slab/Navi) SELBST-BELEGBAR statt behauptet.
 *
 * Rein Daten (kein three/WebGL, keine Date.now → feste ISO-Zeit) ⇒ unit-testbar durch die volle
 * Pipeline (migriereSzene → Schema → Integrität). Additiv/hinter Flag: ohne `?fixture=` unverändert.
 */
import type { SceneDocument, WallNode, RoofNode, CeilingNode, FoundationSlabNode, ObjectNode, Level } from '../domain/scene.types';
// Z1-E4-1: die Fixtures schrieben die Versionszahl hart und brachen beim Sprung 3→4 alle fünf.
// Ab hier lesen sie die Konstante — dieselbe Wahrheit, die das Schema prüft.
import { SCHEMA_VERSION } from '../domain/scene.types';
import { treppeZuParametern } from '../geometry/treppeObjekt';

/** U-Grundriss (mm): Außenrechteck 12×10 m, Kerbe/Innenhof 5×4 m aus der oberen Kante. Ganze mm. */
const U_UMRISS: Array<{ x: number; y: number }> = [
  { x: 0, y: 0 }, { x: 12000, y: 0 }, { x: 12000, y: 10000 }, { x: 8500, y: 10000 },
  { x: 8500, y: 6000 }, { x: 3500, y: 6000 }, { x: 3500, y: 10000 }, { x: 0, y: 10000 },
];

/** Feste Zeit — Fixtures müssen bit-stabil sein (kein Date.now, das bräche Determinismus). */
const ISO = '2026-01-01T00:00:00.000Z';

const EG: Level = {
  id: 'lvl-eg',
  name: 'EG',
  elevation: 0,
  defaultWallHeight: 2800,
  floorThickness: 200,
  sortOrder: 0,
};

/** Umriss → geschlossener Wandring (ein Segment je Kante). Reine Ableitung, ganze mm. */
function umrissZuWaenden(umriss: Array<{ x: number; y: number }>, levelId: string): WallNode[] {
  return umriss.map((p, i) => {
    const q = umriss[(i + 1) % umriss.length];
    return {
      id: `wall-${i}`,
      type: 'wall',
      levelId,
      visible: true,
      locked: false,
      tags: [],
      createdAt: ISO,
      updatedAt: ISO,
      start: { x: p.x, y: p.y },
      end: { x: q.x, y: q.y },
      thickness: 240,
      height: 2800,
    };
  });
}

/** Rechteck-Grundriss (mm) für die Decke-Slab-Sicht: 10×8 m. */
const RECHTECK_UMRISS: Array<{ x: number; y: number }> = [
  { x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 },
];

/**
 * Decke-Slab-Fixture: Rechteck-Grundriss + Treppe + Geschossdecke mit Treppenauge-Durchbruch. Der
 * Durchbruch ist als Loch-Polygon GESETZT (Fixtures umgehen den ADD_CEILING-Reducer, der es sonst aus
 * der Lauflinie ableitet) — Rechteck um den Lauf (± halbe Laufbreite), deckungsgleich zur Reducer-Regel.
 */
function deckeTreppe(): SceneDocument {
  const treppe: ObjectNode = {
    id: 'stair-1', type: 'object', objectType: 'stair', levelId: EG.id,
    visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO, catalogItemId: 'stair',
    transform: { position: { x: 0, y: 0, z: 0 }, rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 } },
    parameters: treppeZuParametern({ startX: 2000, startY: 2000, endX: 5000, endY: 2000, laufbreite: 1000, geschosshoehe: 2800, bereich: 'wohnung' }),
  };
  const decke: CeilingNode = {
    id: 'ceiling-1', type: 'ceiling', levelId: EG.id,
    visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon: RECHTECK_UMRISS, dickeMm: 200,
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt',
    // Treppenauge: Rechteck um die Lauflinie (2000..5000) ± laufbreite/2 (1500..2500).
    oeffnungen: [{ polygon: [{ x: 2000, y: 1500 }, { x: 5000, y: 1500 }, { x: 5000, y: 2500 }, { x: 2000, y: 2500 }] }],
  };
  return {
    id: 'fixture-decke-treppe',
    projectId: 1,
    schemaVersion: SCHEMA_VERSION,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [EG],
    nodes: [...umrissZuWaenden(RECHTECK_UMRISS, EG.id), treppe],
    materials: [],
    roofs: [],
    ceilings: [decke],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

/** Registry aller benannten Fixtures. Decke-Branch: nur decke-treppe (u-dach lebt auf der Dach-Linie,
 *  die das anbau/u-shape-Roofmodell trägt — kein Fixture-Import aus einer fremden Modell-Welt). */
/** U-Dach-Fixture: U-Grundriss + u-shape-Dach mit ALLEN vier Maßen. firstAzimut 270: Engine-Hoföffnung (+z)
 *  fällt auf die Polygon-Kerbe (+y) — Konsistenz Polygon↔anbau↔Azimut (sonst 90°-Fehlplatzierung). */
function uDach(): SceneDocument {
  const dach: RoofNode = {
    id: 'roof-u', type: 'roof', levelId: EG.id, visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon: U_UMRISS, roofType: 'u-shape', neigungGrad: 35, firstAzimutGrad: 270, ueberstandMm: 500, traufhoeheMm: 2800,
    anbau: { length: 12000, width: 10000, lengthB: 5000, widthB: 4000 },
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt',
  };
  return {
    id: 'fixture-u-dach', projectId: 1, schemaVersion: SCHEMA_VERSION, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [EG], nodes: umrissZuWaenden(U_UMRISS, EG.id), materials: [], roofs: [dach],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

/**
 * **Prüfmittel für Z1-W2-5-(b)** — eine Szene, in der das Bezugsmaß `fertig` etwas ANDERES ergibt
 * als `roh`.
 *
 * **Warum es sie braucht:** `wandMengen` rechnet `fertig` einzig über `wand.schichten` (AUF-76) auf
 * die Dicke, also aufs Volumen. **Gemessen am Bestand:** keine Fixture trägt `schichten` (0), und
 * kein Ort im App-Code setzt sie (0) — es gibt keinen Erzeugungsweg. Ohne Schichten ist `fertig`
 * gleich `roh`, und das Modul sagt das auch ehrlich an. **Der Zahlenwechsel war damit nicht
 * zeigbar**, obwohl die Wahl längst wirkt. Dirigenten-Entscheidung 19:05, Punkt 3.
 *
 * **Alle vier Wände tragen dieselben Schichten**, damit die Abnahme nicht davon abhängt, welche
 * Wand angeklickt wird. *Den Gegenfall „ohne Schichten" liefern `u-dach` und `decke-treppe`
 * unverändert mit — er muss nicht in dieselbe Fixture.*
 *
 * ```text
 * Dicke roh     240 mm                    Schichten 15 + 25 = 40 mm
 * Dicke fertig  200 mm                    Wand 10 m × 2,8 m = 28,00 m²
 * Volumen roh   28,00 × 0,240 = 6,720 m³  Volumen fertig  28,00 × 0,200 = 5,600 m³
 * ```
 * **Die Fläche bleibt gleich, das Volumen ändert sich** — das ist fachlich richtig und genau der
 * Unterschied, den (b) beziffert sehen will.
 */
function wandSchichten(): SceneDocument {
  const waende = umrissZuWaenden(RECHTECK_UMRISS, EG.id).map((w) => ({
    ...w,
    schichten: [
      { materialId: 'putz-innen', dickeMm: 15 },
      { materialId: 'putz-aussen', dickeMm: 25 },
    ],
  }));
  return {
    id: 'fixture-wand-schichten', projectId: 1, schemaVersion: SCHEMA_VERSION, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [EG], nodes: waende, materials: [], roofs: [],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

/**
 * **Prüfmittel für Z1-W2-6** — ein Dach mit Aufbauten, damit die Auswechslungs-Anzeige einen
 * Gegenstand hat.
 *
 * **Warum es sie braucht:** `analysiereAuswechslung` setzt einen `RoofAufbau` voraus, und **den
 * kann heute niemand erzeugen** — es gibt kein Werkzeug, das `ADD_ROOF_AUFBAU` auslöst. Das ist
 * der Befund, auf den der Dirigent 18:33 mit Weg A geantwortet hat: *Fixture als Prüfmittel, und
 * der Reifegrad bleibt höchstens `ABGENOMMEN (CODE, Fixture)`, bis ein Nutzer den Aufbau selbst
 * setzen kann.*
 *
 * **Zwei Aufbauten, wie beauftragt — einer in der Flächenmitte, einer in der Randzone:**
 * ```text
 * dachfenster-mitte   window       x 0.50  y 0.50   1000 × 1200 × 1200 mm   -> trennbar erwartet
 * gaube-rand          giebelgaube  x 0.90  y 0.85   2000 × 1800 × 2500 mm   -> Randzone (Probefall b)
 * ```
 * Die **art-abhängige Achsenregel** greift damit an beiden: `oeffnungVTiefeM` nimmt bei `window`
 * die Höhe, sonst die Tiefe (`dachOeffnung.ts:52-54`) — die Fixture belegt beide Zweige.
 *
 * ⚠ **Die Kontur ist RECHTECKIG und die Form `sattel`** — nicht aus Bequemlichkeit: `projiziereDach`
 * wirft bei nicht-rechteckiger Traufkontur, und `dachVorlage` kennt nur die vier echten `DachForm`.
 * Damit ist dieselbe Fixture auch das Prüfmittel für die **Positivfälle** der Z1-V1-1-Module 2, 3
 * und 4, die an `u-dach` (u-shape, ohne Aufbauten) nur ihren Meldungsfall zeigen konnten.
 */
function dachAufbauten(): SceneDocument {
  const dach: RoofNode = {
    id: 'roof-rechteck', type: 'roof', levelId: EG.id, visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO,
    polygon: RECHTECK_UMRISS, roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 90,
    ueberstandMm: 500, traufhoeheMm: 2800,
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt',
    aufbauten: [
      { id: 'dachfenster-mitte', typ: 'window', x: 0.5, y: 0.5, breiteMm: 1000, hoeheMm: 1200, tiefeMm: 1200 },
      { id: 'gaube-rand', typ: 'giebelgaube', x: 0.9, y: 0.85, breiteMm: 2000, hoeheMm: 1800, tiefeMm: 2500 },
    ],
  };
  return {
    id: 'fixture-dach-aufbauten', projectId: 1, schemaVersion: SCHEMA_VERSION, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [EG], nodes: umrissZuWaenden(RECHTECK_UMRISS, EG.id), materials: [], roofs: [dach],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

/**
 * **Prüfmittel für Z1-E0-1-(b)** — das Geschoss mit einer Decke, die NICHT so dick ist wie der
 * Fußbodenaufbau.
 *
 * **Warum es sie braucht:** Die Höhenkette unterscheidet sich vom alten Rechenweg nur dort, wo
 * `decke.dickeMm` von `level.floorThickness` abweicht. **Diesen Fall kann heute niemand über die
 * Bedienung herstellen** — das Deckenwerkzeug setzt `dickeMm: level.floorThickness`
 * (`HausplanerApp.tsx:1070`), ein Panel-Feld für die Deckendicke gibt es nicht (`dickeMm` in
 * `EigenschaftenPanel.tsx`: 0), und `UPDATE_CEILING` ruft im App-Code niemand.
 *
 * ```text
 * EG   elevation 0 · defaultWallHeight 2500 · floorThickness 200 · Decke dickeMm 240
 * alt  0 + 2500 + 200  =  2700      (floorThickness, die Decke wird nicht gelesen)
 * neu  0 + 2500 + 240  =  2740      (die Decke geht ein)
 * ```
 * Das sind genau die beiden Zahlen, die das Blatt nennt. *An den vier übrigen Fixtures ändert die
 * Höhenkette nichts — dort ist die Deckendicke gleich `floorThickness`, und das ist der Beleg für
 * Kriterium (c).*
 */
function etagenHoehenkette(): SceneDocument {
  const eg: Level = { ...EG, defaultWallHeight: 2500 };
  const decke: CeilingNode = {
    id: 'ceiling-hk', type: 'ceiling', levelId: eg.id,
    visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon: RECHTECK_UMRISS, dickeMm: 240,
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt',
  };
  return {
    id: 'fixture-etagen-hoehenkette', projectId: 1, schemaVersion: SCHEMA_VERSION, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [eg], nodes: umrissZuWaenden(RECHTECK_UMRISS, eg.id), materials: [], roofs: [],
    ceilings: [decke],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

/**
 * **Z1-E4-1 — das Referenzhaus mit Bodenplatte.**
 *
 * Die Fixture, gegen die Kriterium (e) misst. **Die Zahlen sind so gewählt, dass sie
 * unterscheidungsfähig sind** — die Lehre aus der Höhenketten-Fixture, wo `floorThickness` und
 * Deckendicke beide 200 waren und deshalb beide Zweige dasselbe lieferten:
 *
 * ```text
 *   Fussbodenaufbau  60 Estrich + 120 Dämmung  = 180 mm
 *   ±0,00 = OK Fertigfussboden EG
 *   oberkanteMm = 0 − 180                      = −180   ← NEGATIV (Yama 22.08. 22:08)
 *   dickeMm (tragende Platte)                  =  250
 * ```
 *
 * **180 ≠ 250 ≠ 240 ≠ 200** — keine zwei Größen dieser Fixture sind gleich, also kann keine
 * Verwechslung zufällig grün werden. *Genau das war der vierte Befund des Plan-Prüfers.*
 *
 * Die Schichtfolge ist **erdseitig zuerst** (`SCHICHT_REIHENFOLGE`): Dämmung liegt auf der Platte,
 * der Estrich darüber.
 */
function bodenplatteReferenz(): SceneDocument {
  const eg: Level = { ...EG, defaultWallHeight: 2500 };
  const platte: FoundationSlabNode = {
    id: 'slab-ref', type: 'foundation_slab', levelId: eg.id,
    visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon: RECHTECK_UMRISS,
    dickeMm: 250,
    oberkanteMm: -180,
    erdberuehrt: true,
    schichten: [
      { materialId: 'daemmung', dickeMm: 120 },  // erdseitig zuerst
      { materialId: 'estrich', dickeMm: 60 },
    ],
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt',
  };
  return {
    id: 'fixture-bodenplatte', projectId: 1, schemaVersion: SCHEMA_VERSION, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [eg], nodes: umrissZuWaenden(RECHTECK_UMRISS, eg.id), materials: [], roofs: [],
    foundationSlabs: [platte],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

export const STUDIO_FIXTURES: Record<string, () => SceneDocument> = {
  'u-dach': uDach,
  'decke-treppe': deckeTreppe,
  'wand-schichten': wandSchichten,
  'dach-aufbauten': dachAufbauten,
  'etagen-hoehenkette': etagenHoehenkette,
  'bodenplatte': bodenplatteReferenz,
};

/** Fixture-Name aus dem Query-String (`?fixture=u-dach`). Reine Funktion ⇒ testbar. */
export function fixtureNameAusSearch(search: string | null | undefined): string | null {
  try {
    return new URLSearchParams(search ?? '').get('fixture');
  } catch {
    return null;
  }
}

/** Benannte Fixture bauen; unbekannter/leerer Name ⇒ null (Boot fällt auf die eingebettete Szene zurück). */
export function ladeFixture(name: string | null | undefined): SceneDocument | null {
  if (!name) {
    return null;
  }
  const bauer = STUDIO_FIXTURES[name];
  return bauer ? bauer() : null;
}
