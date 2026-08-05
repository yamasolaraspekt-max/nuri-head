/**
 * A-01 — **Eine nicht-rechteckige Kontur bekommt eine Absage, kein unsichtbares Objekt.**
 *
 * ---
 *
 * **Der Zustand vor dieser Scheibe, im Browser gemessen (Evaluator, 03.08.):** eine L-Kontur
 * erzeugte ein Dach mit `geometrieHerkunft: 'manuell'` und `freigabe: 'bestaetigt'` — der
 * hoechsten Vertrauensstufe, die B10 kennt —, und die 3D-Ansicht blieb LEER. *Ein Bauteil, das
 * nicht existiert und trotzdem bestaetigt ist.*
 *
 * **Die Ursache lag nie in der Geometrie.** `geometry/dachGeometrie.ts` wirft seit je bei jeder
 * Kontur, die um mehr als 1 % von ihrer Bounding-Box abweicht — mit dem Kommentar *„sonst kein
 * stilles Falschdach"*. Beide Faenger im Renderer (`szene.ts` `continue` / `return`) machen daraus
 * ein stilles FEHLENDES Dach. **Die Schranke funktioniert; sie wurde nur nicht gehoert.**
 *
 * ---
 *
 * **WELCHE Funktion gefragt wird, ist der Kern des Auftrags — und es gibt ZWEI mit demselben Namen:**
 *
 * ```text
 * geometry/dachGeometrie.ts:105   dachFlaechen(roof: RoofNode): DachFlaeche[]   WIRFT
 * app/tools/teilKennung.ts:112    dachFlaechen(dach: RoofNode & {flaechen?})    wirft NIE
 * ```
 *
 * *Wer den falschen Import zieht, baut eine Absage, die nie absagt — und alle uebrigen Kriterien
 * blieben gruen.* Deshalb prueft K-05 den Import selbst.
 *
 * **Und warum nicht `istAchsenRechteck` (dachAusschnitt.ts:72), was naeher laege:** es widerspricht
 * dem Renderer. Gemessen an HEAD:
 *
 * ```text
 *                              istAchsenRechteck   dachFlaechen (der Renderer)
 * Rechteck                     true                DURCH, 2 Flaechen
 * Rechteck + Zwischenpunkt     FALSE               DURCH, 2 Flaechen    <- WIDERSPRUCH
 * L-Form                       false               WIRFT
 * ```
 *
 * **Ein Rechteck mit kollinearem Zwischenpunkt** — vier Ecken, fuenf Punkte, weil jemand
 * zwischendurch geklickt hat — *wuerde abgewiesen, obwohl sein Dach entstehen koennte.* Genau
 * dagegen steht A-01-6.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { dachFlaechen, DachGeometrieUngueltig } from '../geometry/dachGeometrie';
import { nichtDarstellbareDaecher } from '../renderers/three-d/nichtDarstellbar';
import { readFileSync } from 'node:fs';
import { teil, ohneKommentare } from './_zerlegteApp';
import type { RoofNode } from '../domain/scene.types';

const ISO = '2026-08-05T00:00:00.000Z';

/** L-Form 10×8 m mit ausgespartem Eck 3×4 m ⇒ 68 m², nicht die 80 der Bounding-Box. */
const L_KONTUR = [
  { x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 4000 },
  { x: 7000, y: 4000 }, { x: 7000, y: 8000 }, { x: 0, y: 8000 },
];
const RECHTECK = [{ x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 }];
/** Vier Ecken, FÜNF Punkte: der kollineare Zwischenpunkt auf der Oberkante. */
const RECHTECK_MIT_ZWISCHENPUNKT = [
  { x: 0, y: 0 }, { x: 5000, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 },
];

function dach(polygon: Array<{ x: number; y: number }>): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, polygon,
    roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0,
    ueberstandMm: 500, traufhoeheMm: 2500,
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt',
  };
}

/** Fragt die Entscheidung so, wie der Anlege-Pfad sie fragt: wirft sie, entsteht nichts. */
function wuerdeEntstehen(polygon: Array<{ x: number; y: number }>): { entsteht: boolean; grund?: string } {
  try {
    dachFlaechen(dach(polygon));

    return { entsteht: true };
  } catch (fehler) {
    if (fehler instanceof DachGeometrieUngueltig) return { entsteht: false, grund: fehler.message };
    throw fehler;
  }
}

test('A-01-1: L-Kontur erzeugt KEIN Dach-Objekt und keinen Status', () => {
  const r = wuerdeEntstehen(L_KONTUR);
  assert.equal(r.entsteht, false, 'die L-Kontur wuerde ein Dach erzeugen — die Absage greift nicht');
  assert.ok(r.grund && r.grund.length > 0, 'die Absage hat keinen Grund');
});

test('A-01-2 KONTROLLE: Rechteck-Kontur erzeugt ein Dach mit DIESER Kontur (must_preserve)', () => {
  // **Ohne dieses Kriterium waere „gar kein Dach mehr" eine vollstaendig gruene Loesung.**
  // Es war an der Basis bereits erfuellt und bleibt es — es haelt fest, dass die Absage den
  // funktionierenden Fall nicht mitreisst.
  assert.equal(wuerdeEntstehen(RECHTECK).entsteht, true, 'auch das Rechteck wird abgewiesen');

  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));
  assert.match(app, /polygon: ausKontur \? letzteKontur : gebaeudeUmriss\(\)/,
    'das Dach nimmt die gezeichnete Kontur nicht mehr');
});

test('A-01-6: Rechteck MIT Zwischenpunkt erzeugt ein Dach — die FALSCHE Absage', () => {
  // *Ohne dieses Kriterium bestuende ein Bau, der `istAchsenRechteck` fragt, alle uebrigen
  // Zusagen — L-Form rot, glattes Rechteck gruen — und wiese trotzdem Daecher ab, die der
  // Renderer zeichnen wuerde.* **Die Anlege-Entscheidung MUSS dasselbe sagen wie der Renderer.**
  const r = wuerdeEntstehen(RECHTECK_MIT_ZWISCHENPUNKT);
  assert.equal(r.entsteht, true,
    'ein Rechteck mit kollinearem Zwischenpunkt wird abgewiesen — gefragt wurde der falsche Zeuge');

  // Und die Gegenrichtung: die drei Faelle duerfen nicht alle gleich ausgehen, sonst misst
  // die Zusage nichts.
  assert.notEqual(wuerdeEntstehen(L_KONTUR).entsteht, r.entsteht,
    'L-Form und Rechteck-mit-Zwischenpunkt gehen gleich aus — die Entscheidung trennt nicht');
});

test('A-01-5/K-05: die App RUFT die Entscheidung aus geometry/, nicht die gleichnamige aus app/tools', () => {
  // **Zwei exportierte Funktionen heissen `dachFlaechen`.** Die eine wirft, die andere liest
  // Teil-Kennungen und wirft nie. *Ein Bau mit dem falschen Import erfuellt jede Verhaltens-
  // zusage oben — weil die Absage dann schlicht nie ausgeloest wird.*
  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

  assert.match(app, /from '\.\.\/geometry\/dachGeometrie'/,
    'die App importiert die Entscheidung nicht aus geometry/');
  assert.doesNotMatch(app, /dachFlaechen.*from '\.\.\/tools\/teilKennung'/,
    'die App zieht die gleichnamige Funktion aus app/tools — sie wirft nie');
  assert.match(app, /catch \(fehler\)[\s\S]{0,200}DachGeometrieUngueltig/,
    'der Wurf wird nicht behandelt');
});

test('A-01-3: der Grund ist LESBAR — er steht in der Fussleiste, nicht nur in der Konsole', () => {
  // *Ein `console.error` erfuellt A-01-3 ausdruecklich NICHT (Blatt, Pruefbefehle).* Die
  // Browserabnahme misst die Sichtbarkeit; diese Zusage haelt fest, dass der Weg dorthin gebaut ist.
  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

  assert.match(app, /setDachAbsage\(fehler\.message\)/,
    'der Grund der Absage wird nicht uebernommen — der Nutzer bekaeme eine leere Meldung');
  assert.match(app, /dachAbsage !== null\n\s*\? dachAbsage/,
    'die Fussleiste zeigt die Absage nicht');
});

test('A-01-1 ROT: nach der Absage wird KEIN Kommando abgesetzt', () => {
  // **Die tragende Zusage.** Ohne sie waere eine Fassung gruen, die absagt UND trotzdem anlegt —
  // der Nutzer laese den Grund und haette das unsichtbare Dach trotzdem in der Datei.
  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));
  const zeilen = app.split('\n');

  const absage = zeilen.findIndex((z) => /setDachAbsage\(fehler\.message\)/.test(z));
  const rueckkehr = zeilen.findIndex((z, i) => i > absage && /^\s*return;/.test(z));
  // **Das ZUGEHOERIGE Kommando, nicht das erste im Modul.** Die Datei traegt ZWEI ADD_ROOF-
  // Stellen; die erste liegt weit vor dem Klick-Handler. *Meine erste Fassung nahm sie und
  // meldete rot an einem Code, der stimmt — ein Messgeraet, das die falsche Stelle misst.*
  const anlegen = zeilen.findIndex((z, i) => i > absage && /executeCommand\(\{ type: 'ADD_ROOF'/.test(z));

  assert.ok(absage > 0, 'die Absage steht nicht mehr im Code');
  assert.ok(anlegen > 0, 'das ADD_ROOF-Kommando ist verschwunden — dann misst die Zusage Leere');
  assert.ok(rueckkehr > absage && rueckkehr < anlegen,
    `zwischen Absage (${absage}) und ADD_ROOF (${anlegen}) steht kein return — das Dach entsteht trotz Absage`);
});

test('A-01-2 ROT: der Klick-Handler FRAGT die Entscheidung — er faellt sie nicht selbst', () => {
  // **Aus der Mutationsprobe: „Rechteck mitgesperrt" kam durch.** Alle Verhaltenszusagen oben
  // fragen `dachFlaechen` DIREKT — sie merken nicht, wenn der Klick-Handler die Antwort gar nicht
  // mehr einholt und stattdessen selbst wirft. *Dann entstuende nie wieder ein Dach, und die
  // Suite bliebe vollstaendig gruen.*
  const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

  assert.match(app, /dachFlaechen\(dach\)/,
    'der Klick-Handler ruft die Entscheidung nicht mehr — er entscheidet selbst');
  assert.doesNotMatch(app, /throw new DachGeometrieUngueltig/,
    'der Klick-Handler wirft die Absage selbst, statt sie zu erfragen — ein zweiter Rechtecks-Begriff');
});

test('A-01-4: ein BESTANDSDOKUMENT mit L-Dach traegt die Kontur, die kein Bild zeigen kann', () => {
  // **Das Fixture ist echt, nicht erfunden** (`faca1a7a`): ueber die Speicher-Route erzeugt,
  // vom Servervalidator angenommen (HTTP 200, revision 1 -> 2), aus ticket_testing gelesen.
  // *Die Absage aus A-01-1 wirkt erst beim ANLEGEN — Bestandsdaten wie dieses Dokument gibt es
  // trotzdem, und sie muessen gemeldet werden statt leer zu bleiben.*
  const roh = readFileSync(new URL('./fixtures/a01-bestandsdokument-l-dach.json', import.meta.url), 'utf8');
  const doc = JSON.parse(roh) as { schemaVersion: number; roofs: RoofNode[] };

  assert.equal(doc.schemaVersion, 3, 'das Fixture ist nicht auf dem aktuellen Schema');
  assert.equal(doc.roofs.length, 1, 'das Fixture traegt kein Dach — dann misst die Zusage Leere');

  const bestand = doc.roofs[0];
  assert.equal(bestand.polygon.length, 6, 'die Kontur ist keine L-Form mehr');
  assert.equal(bestand.freigabe, 'bestaetigt',
    'das Fixture traegt nicht den Status, der den Fall gefaehrlich macht');

  // **Der Kern: genau dieses Dach kann der Renderer nicht zeichnen.** Deshalb braucht es eine
  // Meldung — sonst steht `bestaetigt` ueber einer leeren Ansicht.
  assert.throws(() => dachFlaechen(bestand), DachGeometrieUngueltig,
    'das Bestandsdach ist darstellbar — dann ist es nicht der Fall, den A-01-4 meint');
});

test('A-01-4 (Wirkung): der Renderer MELDET das Bestandsdach — an genau einer Stelle', () => {
  // **Der Evaluator-Befund, der diese Zusage erzwungen hat:** die Zusage darueber prueft nur das
  // Fixture. Sie war gruen, waehrend `szene.ts` unangetastet war und die 3D eine leere Stelle
  // zeigte. *Eine Zusage, die die Eingabe prueft statt die Wirkung, misst den eigenen Aufbau.*
  const roh = readFileSync(new URL('./fixtures/a01-bestandsdokument-l-dach.json', import.meta.url), 'utf8');
  const doc = JSON.parse(roh) as { roofs: RoofNode[] };

  const gemeldet = nichtDarstellbareDaecher(doc.roofs);

  assert.equal(gemeldet.length, 1, 'das Bestandsdach wird nicht gemeldet — die leere Stelle bleibt unerklaert');
  assert.equal(gemeldet[0].nodeId, doc.roofs[0].id, 'gemeldet wird ein anderer Knoten');
  assert.ok(gemeldet[0].grund.length > 0, 'die Meldung traegt keinen Grund — dann steht dort nur, DASS etwas fehlt');
});

test('A-01-4 GEGENPROBE: ein rechteckiges Dach wird NICHT gemeldet', () => {
  // Ohne diese Gegenprobe waere `return daecher.map(...)` — *alles melden* — eine gruene Loesung,
  // und jede 3D-Ansicht truege einen Hinweis ueber einem einwandfrei gezeichneten Dach.
  const roh = readFileSync(new URL('./fixtures/a01-bestandsdokument-l-dach.json', import.meta.url), 'utf8');
  const doc = JSON.parse(roh) as { roofs: RoofNode[] };

  const rechteck: RoofNode = {
    ...doc.roofs[0],
    id: 'kontrolle-rechteck',
    polygon: [{ x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 6000 }, { x: 0, y: 6000 }],
  };

  assert.deepEqual(nichtDarstellbareDaecher([rechteck]), [],
    'ein rechteckiges Dach wird gemeldet — dann meldet die Funktion nicht den Fall, sondern alles');
});

test('A-01-4 EIN ORT: die Faenger in szene.ts entscheiden NICHT selbst', () => {
  // *Zwei Orte, die dieselbe Frage beantworten, laufen auseinander* — und der eine waere ein
  // Faenger im Renderer, der ohne WebGL nicht zu fahren und damit nicht zu pruefen ist. Genau so
  // ist der geschluckte Wurf ueberhaupt entstanden.
  const szene = readFileSync(new URL('../renderers/three-d/szene.ts', import.meta.url), 'utf8');
  const code = szene.split('\n').filter((z) => !/^\s*(\/\/|\*|\/\*)/.test(z.trim())).join('\n');

  assert.match(code, /this\.nichtGezeichnet = nichtDarstellbareDaecher\(/,
    'die Szene holt die Liste nicht aus der pruefbaren Funktion');
  assert.doesNotMatch(code, /nichtGezeichnet\.push\(/,
    'ein Faenger entscheidet wieder selbst, was gemeldet wird — zweite Wahrheit, und die ungeprueft');
});

test('A-01-4 OBERFLAECHE: die Meldung erreicht den 3D-Bereich', () => {
  // **Grenze dieser Zusage, offen gesagt:** sie liest Quelltext. Ob der Hinweis im Bild wirklich
  // LESBAR steht, belegt allein die Browserabnahme — eine React-Komponente mit `three` ist im
  // Node-Test nicht zu fahren. Sie deckt den Fall, den ein Test decken kann: *dass die Verbindung
  // still wieder entfernt wird.*
  const bereich = readFileSync(new URL('../app/DreiDBereich.tsx', import.meta.url), 'utf8');

  assert.match(bereich, /nichtDarstellbar\(\)/, 'der Bereich fragt die Szene nicht nach nicht darstellbaren Daechern');
  assert.match(bereich, /nichtDarstellbar\.length > 0 &&/, 'die Meldung wird nicht mehr abhaengig vom Befund gezeigt');
  assert.match(bereich, /nichtDarstellbar\[0\]\.grund/, 'der Grund wird nicht angezeigt — dann steht dort nur, DASS etwas fehlt');
  assert.match(bereich, /role="status"/, 'die Meldung ist fuer Screenreader nicht als Statusmeldung ausgezeichnet');
});
