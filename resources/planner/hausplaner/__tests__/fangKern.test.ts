/**
 * L3b Fang-Kern: Priorität Endpunkt > Ortho > Raster, Toleranz, Toggle.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { fange, wandFangpunkte, FANG_TEXT, lotAufGerade } from '../geometry/fangKern';

test('Fang aus → Rohpunkt (gerundet), art keiner', () => {
  const r = fange({ x: 1234.6, y: 5678.4 }, [{ x: 1235, y: 5678 }], { toleranzMm: 50, aktiv: false });
  assert.deepEqual(r.punkt, { x: 1235, y: 5678 });
  assert.equal(r.art, 'keiner');
});

test('Endpunkt in Toleranz → snappt auf den Kandidaten', () => {
  const r = fange({ x: 1020, y: 30 }, [{ x: 1000, y: 0 }, { x: 5000, y: 0 }], { toleranzMm: 50 });
  assert.deepEqual(r.punkt, { x: 1000, y: 0 });
  assert.equal(r.art, 'endpunkt');
});

test('Endpunkt außerhalb Toleranz → kein Endpunkt-Fang', () => {
  const r = fange({ x: 1200, y: 0 }, [{ x: 1000, y: 0 }], { toleranzMm: 50, raster: 100 });
  assert.notEqual(r.art, 'endpunkt');
});

test('nächster Endpunkt gewinnt bei mehreren in Toleranz', () => {
  const r = fange({ x: 1010, y: 0 }, [{ x: 1000, y: 0 }, { x: 1030, y: 0 }], { toleranzMm: 60 });
  assert.deepEqual(r.punkt, { x: 1000, y: 0 });
});

test('Ortho waagerecht: nahe der x-Achse durch Referenz → y auf Referenz', () => {
  const r = fange({ x: 5000, y: 40 }, [], { toleranzMm: 50, ortho: { x: 0, y: 0 }, orthoToleranzMm: 100 });
  assert.deepEqual(r.punkt, { x: 5000, y: 0 });
  assert.equal(r.art, 'ortho');
});

test('Ortho senkrecht: nahe der y-Achse → x auf Referenz', () => {
  const r = fange({ x: 40, y: 5000 }, [], { toleranzMm: 50, ortho: { x: 0, y: 0 }, orthoToleranzMm: 100 });
  assert.deepEqual(r.punkt, { x: 0, y: 5000 });
  assert.equal(r.art, 'ortho');
});

test('Priorität: Endpunkt schlägt Ortho und Raster', () => {
  const r = fange({ x: 12, y: 12 }, [{ x: 0, y: 0 }], { toleranzMm: 50, ortho: { x: 0, y: 0 }, raster: 100 });
  assert.equal(r.art, 'endpunkt');
  assert.deepEqual(r.punkt, { x: 0, y: 0 });
});

test('Raster als Fallback rundet beide Achsen', () => {
  const r = fange({ x: 1234, y: 5678 }, [], { toleranzMm: 20, raster: 100 });
  assert.deepEqual(r.punkt, { x: 1200, y: 5700 });
  assert.equal(r.art, 'raster');
});

test('kein Kandidat, kein Ortho, kein Raster → keiner', () => {
  const r = fange({ x: 333, y: 444 }, [], { toleranzMm: 20 });
  assert.equal(r.art, 'keiner');
  assert.deepEqual(r.punkt, { x: 333, y: 444 });
});

test('Determinismus', () => {
  const args = [{ x: 1010, y: 20 }, [{ x: 1000, y: 0 }], { toleranzMm: 50, raster: 100, ortho: { x: 0, y: 0 } }] as const;
  assert.deepEqual(fange(...args), fange(...args));
});

test('wandFangpunkte: Endpunkte + Mittelpunkt je Wand', () => {
  const pts = wandFangpunkte([{ start: { x: 0, y: 0 }, end: { x: 1000, y: 0 } }]);
  assert.equal(pts.length, 3);
  assert.deepEqual(pts[0], { x: 0, y: 0 });
  assert.deepEqual(pts[1], { x: 1000, y: 0 });
  assert.deepEqual(pts[2], { x: 500, y: 0 });
});

test('wandFangpunkte: leer -> leer', () => {
  assert.deepEqual(wandFangpunkte([]), []);
});

// ═══════════════════════════════════════════════════════════════════════════════════════════════
// Z-04 — die drei neuen Fangarten und ihre RANGFOLGE.
//
// **Mutationsprobe vor diesen Zusagen — 8 Mutationen, SIEBEN kamen durch:**
//
//   blind (7)  Mittelpunkt schlaegt Endpunkt · Achse schlaegt Mittelpunkt
//              Lot auf die STRECKE statt auf die Gerade · entartete Achse liefert NaN
//              Fangart wird wieder weggeworfen · Fangart ohne Flacker-Schutz
//              'keiner' zeigt das Wort statt nichts
//   gefangen   die Fussflaeche zeigt die Art nicht mehr (2 rot)
//
// *Die Rangfolge war komplett ungedeckt — und sie ist der Teil, der bei dichten Grundrissen
// darueber entscheidet, ob der Fang wie eine Entscheidung wirkt oder wie ein Wackelkontakt.*
// ═══════════════════════════════════════════════════════════════════════════════════════════════

const WAND_A = { x: 0, y: 0 };
const WAND_B = { x: 4000, y: 0 };
const MITTE = { x: 2000, y: 0 };

test('Z-04 (Rangfolge): der Endpunkt schlaegt die Wandmitte', () => {
  // Mutation „Mittelpunkt schlaegt Endpunkt" kam durch. **Bei einer kurzen Wand liegen beide in
  // derselben Toleranz** — dann entscheidet allein die Reihenfolge, und sie muss die sein, die
  // der Mensch erwartet: die Ecke ist der staerkere Bezug.
  const kurz = { x: 0, y: 0 };
  const kurzMitte = { x: 100, y: 0 };
  const e = fange({ x: 40, y: 0 }, [kurz], { toleranzMm: 300, mitten: [kurzMitte] });
  assert.equal(e.art, 'endpunkt', 'die Wandmitte gewinnt gegen den Endpunkt');
  assert.deepEqual(e.punkt, kurz);
  // presence-Partner nach R2: OHNE Endpunkt in Reichweite faengt die Mitte sehr wohl.
  const m = fange({ x: 90, y: 0 }, [], { toleranzMm: 300, mitten: [kurzMitte] });
  assert.equal(m.art, 'mittelpunkt', 'die Wandmitte faengt gar nicht — die Zusage misst Leere');
});

test('Z-04 (Rangfolge): die Wandmitte schlaegt die Wandflucht', () => {
  // Mutation „Achse schlaegt Mittelpunkt" kam durch. Die Achse durch A und B laeuft GENAU durch
  // die Mitte — ohne feste Reihenfolge liefert derselbe Zeiger mal das eine, mal das andere.
  const t = fange({ x: 2000, y: 60 }, [], {
    toleranzMm: 300, mitten: [MITTE], achsen: [[WAND_A, WAND_B]],
  });
  assert.equal(t.art, 'mittelpunkt', 'die Wandflucht gewinnt gegen die Wandmitte');
  assert.deepEqual(t.punkt, MITTE);
  // presence-Partner: ohne Mitte in Reichweite faengt die Achse.
  const a = fange({ x: 3500, y: 60 }, [], { toleranzMm: 300, mitten: [MITTE], achsen: [[WAND_A, WAND_B]] });
  assert.equal(a.art, 'achse', 'die Wandflucht faengt gar nicht');
});

test('Z-04: die Wandflucht faengt auf der VERLAENGERTEN Geraden, nicht nur auf der Strecke', () => {
  // Mutation „Lot auf die STRECKE" kam durch — und sie ist die heimtueckischste: auf der Strecke
  // faellt der Fang mit dem Endpunkt-Fang zusammen, den es schon gibt. **Der ganze Zweck der
  // Wandflucht ist der Bereich AUSSERHALB der Wand:** dort richtet man die Nachbarwand aus.
  const draussen = fange({ x: 9000, y: 60 }, [], { toleranzMm: 300, achsen: [[WAND_A, WAND_B]] });
  assert.equal(draussen.art, 'achse', 'ausserhalb der Wandstrecke faengt die Flucht nicht mehr');
  assert.deepEqual(draussen.punkt, { x: 9000, y: 0 }, 'der Lotfusspunkt liegt nicht auf der Geraden');
});

test('Z-04: eine entartete Achse faengt gar nicht — statt NaN zu liefern', () => {
  // Mutation „entartete Achse liefert NaN" kam durch. `NaN <= toleranz` ist `false`, der Fang
  // faellt also durch — **aber der Punkt waere `{NaN, NaN}`, wenn ihn jemand doch benutzt.**
  // Durch EINEN Punkt geht keine Gerade; das ist keine Rundungsfrage, sondern ein fehlender Operand.
  // *Ohne Raster-Option faellt der Fang bis `keiner` durch — mein erster Entwurf erwartete hier
  // `raster` und war schlicht falsch: ich hatte die Option gar nicht uebergeben.*
  // **Direkt am Vertrag geprueft, nicht ueber `fange()`.** Ueber `fange()` blieb das Entfernen
  // des Waechters in der Probe BLIND: `{NaN, NaN}` faellt durch die Abstandspruefung, und von
  // aussen sieht alles gleich aus. Das ist kein Loch in der Zusage, sondern eine Eigenschaft der
  // Lage — *eine Zusage kann nicht halten, was nach aussen nicht sichtbar ist.*
  assert.equal(lotAufGerade({ x: 10, y: 10 }, WAND_A, WAND_A), null,
    'durch EINEN Punkt wird eine Gerade gelegt — das Ergebnis ist NaN, nicht null');
  // presence-Partner nach R2: mit einer echten Achse liefert dieselbe Funktion einen Punkt.
  const echt = lotAufGerade({ x: 10, y: 10 }, WAND_A, WAND_B);
  assert.ok(echt && Number.isFinite(echt.x) && Number.isFinite(echt.y), 'die Funktion rechnet gar nicht mehr');

  const e = fange({ x: 10, y: 10 }, [], { toleranzMm: 300, achsen: [[WAND_A, WAND_A]] });
  assert.equal(e.art, 'keiner', 'eine entartete Achse hat trotzdem gefangen');
  assert.ok(Number.isFinite(e.punkt.x) && Number.isFinite(e.punkt.y), `NaN im Ergebnis: ${JSON.stringify(e.punkt)}`);
});

test('Z-04: die Verlaengerung setzt den laufenden Weg fort — und ist nicht Ortho', () => {
  // Der Weg laeuft schraeg (45°). `ortho` koennte das nie liefern: es richtet auf 0/90° aus.
  const e = fange({ x: 3060, y: 2940 }, [], {
    toleranzMm: 300, weg: [{ x: 0, y: 0 }, { x: 1000, y: 1000 }],
  });
  assert.equal(e.art, 'verlaengerung', 'der laufende Weg wird nicht mehr fortgesetzt');
  assert.deepEqual(e.punkt, { x: 3000, y: 3000 }, 'die Fortsetzung liegt nicht auf der Geraden des Wegs');
});

test('Z-03: `keiner` ergibt eine LEERE Zeichenkette, kein Wort', () => {
  // Mutation „'keiner' zeigt das Wort" kam durch. Eine Statuszeile, die dauernd „keiner" sagt,
  // ist Rauschen — sie soll sich melden, wenn etwas passiert, nicht wenn nichts passiert.
  assert.equal(FANG_TEXT.keiner, '', 'die Fussflaeche meldet dauerhaft „kein Fang"');
  // presence-Partner nach R2: jede ANDERE Art hat einen lesbaren Text.
  for (const art of ['endpunkt', 'mittelpunkt', 'achse', 'verlaengerung', 'ortho', 'raster'] as const) {
    assert.ok(FANG_TEXT[art].length >= 5, `\`${art}\` hat keinen lesbaren Text`);
  }
});
