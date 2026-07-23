import { test } from "node:test";
import assert from "node:assert/strict";
import { analysiereAuswechslung, sparrenPositionenU } from "../geometry/auswechslung";

const FLAECHE = { breiteM: 8, hoeheM: 5 };
const DIST = 0.7;
// Sparren-u bei 8 m / 0,7 m: u_i = 0,04 + i*0,72 -> 0,04 0,76 1,48 2,20 2,92 3,64 4,36 5,08 5,80 6,52 7,24 7,96

test("Sparrenpositionen identisch zur Engine-Formel (rafterW/2 + i*((b-rafterW)/n))", () => {
  const u = sparrenPositionenU(8, 0.7, 0.08);
  assert.ok(Math.abs(u[0] - 0.04) < 1e-9);
  assert.equal(u.length, 12); // numRafters=floor(8/0.7)=11 -> 12 Sparren
  for (const x of u) assert.ok(Number.isFinite(x) && x >= 0);
});

test("Dachfenster mittig schneidet mehrere Sparren -> Auswechslung erforderlich", () => {
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.5, yRel: 0.5, breiteM: 0.78, hoeheM: 1.18 }, DIST);
  assert.ok(r.betroffeneSparren >= 2);
  assert.equal(r.wechselErforderlich, true);
  assert.equal(r.spanntMehrereFelder, true);
});

test("Kamin schneidet genau einen Sparren -> sichere Wechselhölzer (oben+unten)", () => {
  // mittig auf Sparren u=3,64 (xRel=0,455), 0,6 m breit, mittig in v (nicht randnah)
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.455, yRel: 0.5, breiteM: 0.6, hoeheM: 0.6 }, DIST);
  assert.equal(r.betroffeneSparren, 1);
  assert.equal(r.wechselErforderlich, true);
  assert.equal(r.pruefpflichtig, false);
  assert.equal(r.wechselAnzahl, 2); // oben + unten
  assert.ok(r.wechselLaengeM > 0 && Number.isFinite(r.wechselLaengeM));
});

test("Kamin liegt zwischen zwei Sparren -> kein Schnitt, keine Auswechslung", () => {
  // schmale Öffnung mittig zwischen 3,64 und 4,36 (u=4,0)
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.5, yRel: 0.5, breiteM: 0.1, hoeheM: 0.1 }, DIST);
  assert.equal(r.betroffeneSparren, 0);
  assert.equal(r.wechselErforderlich, false);
  assert.equal(r.wechselAnzahl, 0);
});

test("Öffnung nahe First, die einen Sparren schneidet -> prüfpflichtig, KEINE erfundenen Wechselhölzer", () => {
  // xRel 0.455 = auf Sparren u=3,64 (schneidet), yRel 0.95 = nahe First
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.455, yRel: 0.95, breiteM: 0.6, hoeheM: 0.6 }, DIST);
  assert.equal(r.wechselErforderlich, true);
  assert.equal(r.naheRandzone, true);
  assert.ok(r.randzonen.includes("First"));
  assert.equal(r.pruefpflichtig, true);
  assert.equal(r.wechselAnzahl, 0);
  assert.equal(r.wechselLaengeM, 0);
});

test("Öffnung nahe Traufe, die einen Sparren schneidet -> prüfpflichtig", () => {
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.455, yRel: 0.05, breiteM: 0.6, hoeheM: 0.6 }, DIST);
  assert.equal(r.wechselErforderlich, true);
  assert.ok(r.randzonen.includes("Traufe"));
  assert.equal(r.pruefpflichtig, true);
});

test("Öffnung nahe First zwischen den Sparren -> naheRandzone, aber keine Auswechslung (kein Prüffall)", () => {
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.5, yRel: 0.95, breiteM: 0.1, hoeheM: 0.1 }, DIST);
  assert.equal(r.naheRandzone, true);
  assert.equal(r.wechselErforderlich, false);
  assert.equal(r.pruefpflichtig, false); // kein Sparren geschnitten -> keine Auswechslung nötig
});

test("Öffnung auf nicht vorhandener Fläche (breite 0) -> nichts verarbeiten", () => {
  const r = analysiereAuswechslung({ breiteM: 0, hoeheM: 0 }, { xRel: 0.5, yRel: 0.5, breiteM: 0.6, hoeheM: 0.6 }, DIST);
  assert.equal(r.wechselErforderlich, false);
  assert.equal(r.betroffeneSparren, 0);
});

test("gültige Öffnung erzeugt keine NaN/Infinity/negativen Werte", () => {
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.455, yRel: 0.5, breiteM: 0.6, hoeheM: 0.6 }, DIST);
  for (const v of [r.betroffeneSparren, r.wechselAnzahl, r.wechselLaengeM]) {
    assert.ok(Number.isFinite(v) && v >= 0);
  }
});

test("ungültige Eingaben (NaN-Maße) -> robust 0, kein Crash", () => {
  const r = analysiereAuswechslung(FLAECHE, { xRel: NaN as any, yRel: 0.5, breiteM: NaN as any, hoeheM: 0.6 }, DIST);
  assert.equal(r.wechselErforderlich, false);
  assert.ok(Number.isFinite(r.wechselLaengeM));
});

test("keine Doppelzählung: Funktion erzeugt nur Wechsel, KEINE Sparren-Teilstücke", () => {
  // Die Auswechslung-Analyse liefert wechselAnzahl (0/2), aber keine 'sparren'-Mengen.
  const r = analysiereAuswechslung(FLAECHE, { xRel: 0.455, yRel: 0.5, breiteM: 0.6, hoeheM: 0.6 }, DIST);
  assert.ok(!("sparrenTeilstuecke" in r)); // bewusst nicht vorhanden
  assert.ok(r.wechselAnzahl === 0 || r.wechselAnzahl === 2);
});
