/**
 * Decke (Feature A) — additiv/kein 422, Commands (max 1/Level, mm-Invariante), Treppendurchbruch (aus
 * Grundriss), Slab-Nettofläche (Loch reduziert), Etagen-Stapel (eine Ableitung).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { applyCommand } from '../commands/applyCommand';
import { sceneDocumentSchema } from '../domain/validation';
import { deckenNettoFlaecheM2, naechsteEtageElevationMm } from '../renderers/three-d/deckenMesh';
import { treppeZuParametern } from '../geometry/treppeObjekt';
import { polygonFlaecheM2 } from '../geometry/polygonFlaeche';
import { teil, ohneKommentare } from './_zerlegteApp';
import type { SceneDocument, CeilingNode, ObjectNode, Level } from '../domain/scene.types';

const ISO = '2026-07-23T00:00:00.000Z';
const LEVEL: Level = { id: 'l1', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 };
const UMRISS = [{ x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 }];

function baseDoc(): SceneDocument {
  return {
    id: 'd', projectId: 1, schemaVersion: 3, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [LEVEL], nodes: [], materials: [], roofs: [], metadata: { createdAt: ISO, updatedAt: ISO },
  };
}
function decke(over: Partial<CeilingNode> = {}): CeilingNode {
  return {
    id: 'c1', type: 'ceiling', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, polygon: UMRISS, dickeMm: 200,
    // Z-06-N1: Pflichtfelder ab v3. `over` steht danach, damit eine Zusage sie überschreiben kann.
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt', ...over,
  };
}
function treppe(): ObjectNode {
  return {
    id: 's1', type: 'object', objectType: 'stair', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, catalogItemId: 'stair',
    transform: { position: { x: 0, y: 0, z: 0 }, rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 } },
    parameters: treppeZuParametern({ startX: 2000, startY: 2000, endX: 5000, endY: 2000, laufbreite: 1000, geschosshoehe: 2600, bereich: 'wohnung' }),
  };
}

test('additiv: Dokument OHNE ceilings validiert (kein 422); MIT ceilings ebenfalls', () => {
  assert.equal(sceneDocumentSchema.safeParse(baseDoc()).success, true);
  const doc = baseDoc();
  doc.ceilings = [decke()];
  assert.equal(sceneDocumentSchema.safeParse(doc).success, true);
});

test('ADD_CEILING legt eine Decke an; zweite je Level wird abgelehnt (max. 1)', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke() }, ISO);
  assert.equal(doc.ceilings?.length, 1);
  assert.throws(() => applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke({ id: 'c2' }) }, ISO), /bereits eine Decke/);
});

test('mm-Invariante: nicht-ganzzahlige Deckendicke wird abgelehnt', () => {
  const doc = baseDoc();
  assert.throws(() => applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke({ dickeMm: 200.5 }) }, ISO), /ganzen Millimetern/);
});

test('Treppendurchbruch (aus Grundriss): Treppe im Level ⇒ automatische Öffnung in der Decke', () => {
  const doc = baseDoc();
  doc.nodes = [treppe()];
  applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke() }, ISO); // keine oeffnungen gesetzt
  const c = doc.ceilings?.[0];
  assert.ok(c?.oeffnungen && c.oeffnungen.length >= 1, 'Treppe erzeugt einen Durchbruch');
  // Der Durchbruch reduziert die Netto-Deckenfläche.
  assert.ok(deckenNettoFlaecheM2(c!) < deckenNettoFlaecheM2(decke()), 'Loch verkleinert die Slab-Fläche');
});

test('deckenNettoFlaecheM2: Umriss minus Durchbrüche', () => {
  const brutto = deckenNettoFlaecheM2(decke());
  assert.ok(Math.abs(brutto - 80) < 0.01, 'Umriss 10×8 = 80 m²');
  const mitLoch = deckenNettoFlaecheM2(decke({ oeffnungen: [{ polygon: [{ x: 1000, y: 1000 }, { x: 3000, y: 1000 }, { x: 3000, y: 2000 }, { x: 1000, y: 2000 }] }] }));
  assert.ok(Math.abs(mitLoch - 78) < 0.01, 'minus 2×1 = 78 m²');
});

test('UPDATE/REMOVE_CEILING', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke() }, ISO);
  applyCommand(doc, { type: 'UPDATE_CEILING', ceilingId: 'c1', changes: { dickeMm: 250 } }, ISO);
  assert.equal(doc.ceilings?.[0].dickeMm, 250);
  applyCommand(doc, { type: 'REMOVE_CEILING', ceilingId: 'c1' }, ISO);
  assert.equal(doc.ceilings?.length, 0);
  assert.throws(() => applyCommand(doc, { type: 'REMOVE_CEILING', ceilingId: 'x' }, ISO), /existiert nicht/);
});

test('Etagen-Stapel: nächste Elevation = Elevation + Wandhöhe + Deckendicke (eine Ableitung)', () => {
  assert.equal(naechsteEtageElevationMm(LEVEL, decke()), 0 + 2500 + 200);
  // ohne Decke: Rückfall auf floorThickness (kein Rateswert der Höhe)
  assert.equal(naechsteEtageElevationMm(LEVEL, undefined), 0 + 2500 + 200);
});

// --- Z-06: die Decke nimmt die gezeichnete Kontur ------------------------------------------------
//
// **Die Mutationsprobe VOR diesen Zusagen — 8 Mutationen, 8 kamen durch:**
//
// ```text
// Kontur wird ignoriert              keine Zusage rot
// Kontur und Umriss vertauscht       keine Zusage rot
// Umlaufsinn gedreht                 keine Zusage rot
// Hinweis auch MIT Kontur            keine Zusage rot
// Hinweis NIE                        keine Zusage rot
// Mindestpunktzahl faellt weg        keine Zusage rot
// Hinweis an null statt true         keine Zusage rot
// Decke nimmt nur den ersten Punkt   keine Zusage rot
// ```
//
// **Acht von acht, bei 1645 gruenen Zusagen.** Kein Zufall: die Klasse dieser Scheibe ist
// *„falsch, aber sieht richtig aus"*. In jedem der acht Faelle ERSCHEINT eine Decke — sie hat
// nur die falsche Flaeche, und die sieht im Bild niemand. *Genau deshalb prueft K-02 die
// FLAECHE und nicht die Punktliste: eine Punktliste friert den gebauten Zustand ein (F-06),
// die Flaeche prueft die Aussage.*

/** L-Form 10×8 m mit ausgespartem Eck 3×4 m ⇒ 80 − 12 = 68 m². */
const L_KONTUR = [
  { x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 4000 },
  { x: 7000, y: 4000 }, { x: 7000, y: 8000 }, { x: 0, y: 8000 },
];

test('Z-06/K-02: die Decke einer L-Form hat 68 m² — NICHT die 80 des umschliessenden Rechtecks', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_CEILING', ceiling: decke({ polygon: L_KONTUR }) }, ISO);
  const gebaut = doc.ceilings?.[0];
  assert.ok(gebaut, 'keine Decke angelegt — die Zusage wuerde Leere messen');

  const flaeche = polygonFlaecheM2(gebaut.polygon) / 1e6; // polygonFlaecheM2 rechnet in mm²
  assert.equal(Math.round(flaeche), 68, `Deckenflaeche ${flaeche.toFixed(1)} m² statt 68`);
  assert.notEqual(Math.round(flaeche), 80, 'die Decke traegt die Flaeche der Bounding-Box');
});

test('Z-06/K-02: der Umlaufsinn aendert die Flaeche nicht — sonst haengt sie an der Klickrichtung', () => {
  // **Die Mutation „Umlaufsinn gedreht" kam durch.** Wer eine L-Form gegen den Uhrzeigersinn
  // klickt, bekommt eine negative signierte Flaeche; ohne Betrag waere die Decke dann 0 oder
  // negativ gross. *Der Fehler haenge damit an der Reihenfolge der Klicks — unauffindbar.*
  const rueckwaerts = [...L_KONTUR].reverse();
  assert.equal(
    Math.round(polygonFlaecheM2(rueckwaerts) / 1e6),
    Math.round(polygonFlaecheM2(L_KONTUR) / 1e6),
    'die Flaeche haengt am Umlaufsinn',
  );
});

test('Z-06/K-01: die Insel nimmt die Kontur — und nur das Dach behaelt den Umriss bedingungslos', () => {
  // **Quelltext-Zusage, und das ist hier eine Schwaeche, keine Wahl.** Die Entscheidung lebt in
  // der React-Funktion; der Scope dieser Scheibe nennt genau EINE Produktivdatei, also kann sie
  // nicht in ein pruefbares Modul wandern. *Gemeldet an den Planner: die Extraktion in eine
  // reine Funktion ist eine eigene Scheibe wert — dann faellt diese Zusage weg und eine echte
  // Verhaltenszusage tritt an ihre Stelle.*
  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

  assert.match(app, /polygon: ausKontur \? letzteKontur : gebaeudeUmriss\(\)/,
    'die Decke nimmt die Kontur nicht mehr — oder nimmt sie verdreht');
  assert.match(app, /const ausKontur = letzteKontur !== null && letzteKontur\.length >= KONTUR_MIN_PUNKTE/,
    'die Bedingung fuer „aus Kontur" ist veraendert — eine Kontur mit zwei Punkten waere eine Decke');
  // **Z-07 hat diese Zahl auf 0 gedreht, und das ist der Fortschritt.** Bis heute stand hier
  // eine 1: das Dach nahm den Umriss bedingungslos, und diese Zusage hielt fest, dass es
  // GENAU EINE solche Stelle gibt. *Jetzt gibt es keine mehr — Decke UND Dach fragen dieselbe
  // Bedingung.* Die Zusage bleibt scharf: sie verbietet ab jetzt jeden bedingungslosen Umriss.
  const umriss = (app.match(/polygon: gebaeudeUmriss\(\)/g) ?? []).length;
  assert.equal(umriss, 0, `${umriss} Bauteil(e) nehmen den Umriss bedingungslos — seit Z-07 keines mehr`);

  // Und die Gegenrichtung, sonst waere die 0 auch mit gaenzlich fehlendem Umriss-Rueckfall erfuellt:
  const bedingt = (app.match(/polygon: ausKontur \? letzteKontur : gebaeudeUmriss\(\)/g) ?? []).length;
  assert.equal(bedingt, 2, `${bedingt} bedingte Umriss-Bauteile statt zwei (Decke und Dach)`);
});

test('Z-06/K-03: ohne Kontur meldet die Fussleiste eine Naeherung — mit Kontur schweigt sie', () => {
  // Die drei Hinweis-Mutationen (immer / nie / auch bei `null`) kamen alle durch. **Ein Hinweis,
  // der immer steht, ist so wertlos wie keiner** — er trennt die Naeherung nicht von der exakten
  // Decke, und genau das ist der heutige Fehler mit besserem Gewissen.
  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

  assert.match(app, /setDeckeNaeherung\(!ausKontur\)/,
    'der Melder haengt nicht mehr an der Entscheidung — er meldet immer oder nie');
  assert.match(app, /: deckeNaeherung === true\n/,
    'der Hinweis prueft nicht mehr auf `true` — bei `null` (noch keine Decke) staende er schon da');
  assert.match(app, /Näherung aus dem Gebäude-Umriss/,
    'der Hinweistext ist weg — K-03 verlangt Text, kein Symbol allein');
});

test('Z-07/K-06: das DACH meldet seine Naeherung ebenso — der Melder haengt an der Entscheidung', () => {
  // **UMBENANNT auf den Befund des Evaluators (26747678), und die Ruege ist berechtigt.** Diese
  // Zusage hiess bis eben `Z-07/K-04` — ein Name, der im Blatt schon vergeben war, und zwar an
  // ein P1-Kriterium ueber die DACHFLAECHE, das ich nicht gebaut hatte. *Wer einen Kriteriumsnamen
  // weitergibt, macht das Fehlende unsichtbar: der Lauf meldet K-04 als gedeckt, waehrend die
  // gedeckte Sache eine andere ist.* Der Melder ist eine gute Ergaenzung — er ist nur nicht K-04.
  // **Nachtrag aus der Mutationsprobe von Z-07: `setDachNaeherung(false)` kam durch.** Die
  // Decken-Zusage oben prueft `setDeckeNaeherung(!ausKontur)` — fuer das Dach gab es nichts.
  // *Ein Bauteil, das seine Naeherung verschweigt, ist genau der Fehler mit besserem Gewissen,
  // gegen den Z-06 geschrieben wurde — nur eine Etage hoeher.*
  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

  assert.match(app, /setDachNaeherung\(!ausKontur\)/,
    'der Dach-Melder haengt nicht mehr an der Entscheidung — er meldet immer oder nie');
  assert.match(app, /: dachNaeherung === true\n/,
    'der Dach-Hinweis prueft nicht auf `true` — bei `null` (noch kein Dach) staende er schon da');
  assert.match(app, /Dach als Näherung aus dem Gebäude-Umriss/,
    'der Hinweistext fuers Dach ist weg');

  // Und die Trennung: Decke und Dach fuehren GETRENNTE Melder. Ein gemeinsamer haette gezeigt,
  // was zuletzt angelegt wurde — nicht, was gerade gilt.
  assert.notEqual(
    (app.match(/setDeckeNaeherung/g) ?? []).length,
    0,
    'der Decken-Melder ist verschwunden — dann meldet das Dach fuer beide',
  );
});

test('Z-07/K-04: die L-Form bekommt ein L-DACH — 68 m², nicht die 80 der Bounding-Box', () => {
  // **Das eigentliche K-04 des Blattes, beim ersten Bau NICHT gebaut.** Gemeldet hat es der
  // Evaluator; ich hatte den Namen an eine andere Zusage vergeben. *Die Flaeche ist der
  // Pruefgegenstand, nicht die Punktliste: eine Punktliste friert den gebauten Zustand ein
  // (F-06), die Flaeche prueft die Aussage.*
  const dach = (polygon: Array<{ x: number; y: number }>) => ({
    id: 'r1', type: 'roof' as const, levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, polygon,
    roofType: 'sattel' as const, neigungGrad: 35, firstAzimutGrad: 0,
    ueberstandMm: 500, traufhoeheMm: 2500,
    geometrieHerkunft: 'manuell' as const, freigabe: 'bestaetigt' as const,
  });

  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_ROOF', roof: dach(L_KONTUR) }, ISO);
  const gebaut = doc.roofs?.[0];
  assert.ok(gebaut, 'kein Dach angelegt — die Zusage wuerde Leere messen');

  const flaeche = polygonFlaecheM2(gebaut.polygon) / 1e6;
  assert.equal(Math.round(flaeche), 68, `Dachflaeche ${flaeche.toFixed(1)} m² statt 68`);
  assert.notEqual(Math.round(flaeche), 80, 'das Dach traegt die Flaeche der Bounding-Box');

  // **Die Kontrolle (B4):** beim Rechteck sind Kontur und Box gleich — erst der Unterschied im
  // L-Fall bedeutet etwas. Ohne sie koennte die Zusage auch mit einer kaputten Flaechenrechnung
  // gruen sein, die zufaellig 68 liefert.
  const doc2 = baseDoc();
  applyCommand(doc2, { type: 'ADD_ROOF', roof: dach(UMRISS) }, ISO);
  const rechteck = polygonFlaecheM2(doc2.roofs![0].polygon) / 1e6;
  assert.equal(Math.round(rechteck), 80, `Rechteck-Kontrolle ${rechteck.toFixed(1)} m² statt 80`);
  assert.notEqual(Math.round(rechteck), Math.round(flaeche),
    'L-Form und Rechteck liefern dieselbe Flaeche — dann misst die Zusage nicht die Kontur');
});
