import { test } from "node:test";
import assert from "node:assert/strict";
import { sparrenTeilstuecke, istSicherTrennbar } from "../geometry/sparrenTrennung";

test("Öffnung voll innerhalb -> unteres + oberes Teilstück mit echten Längen", () => {
  // Sparren v 0..5, Öffnung v 2..3
  const t = sparrenTeilstuecke(0, 5, 2, 3, 0.1);
  assert.equal(t.length, 2);
  assert.deepEqual(t[0], { vStart: 0, vEnd: 2, laengeM: 2, lage: "unten" });
  assert.deepEqual(t[1], { vStart: 3, vEnd: 5, laengeM: 2, lage: "oben" });
  // Summe der Teilstücke < voller Sparren (das fehlende Stück = Öffnung) -> keine Doppelzählung
  assert.ok(t[0].laengeM + t[1].laengeM < 5);
});

test("Öffnung berührt First-Ende -> NICHT sicher trennbar (voller Sparren bleibt)", () => {
  assert.deepEqual(sparrenTeilstuecke(0, 5, 4.5, 5.2, 0.1), []);
  assert.equal(istSicherTrennbar(0, 5, 4.5, 5.2), false);
});

test("Öffnung berührt Trauf-Ende -> NICHT sicher trennbar", () => {
  assert.deepEqual(sparrenTeilstuecke(0, 5, -0.2, 0.5, 0.1), []);
});

test("zu kurzes Reststück (< Mindestmaß) -> keine Trennung", () => {
  // unteres Reststück 0.05 m < 0.1 m
  assert.deepEqual(sparrenTeilstuecke(0, 5, 0.05, 2, 0.1), []);
  // oberes Reststück 0.05 m < 0.1 m
  assert.deepEqual(sparrenTeilstuecke(0, 5, 2, 4.95, 0.1), []);
});

test("Öffnung außerhalb des Sparrens -> keine Trennung", () => {
  assert.deepEqual(sparrenTeilstuecke(2, 5, 0, 1, 0.1), []); // ganz unterhalb
  assert.deepEqual(sparrenTeilstuecke(0, 3, 4, 5, 0.1), []); // ganz oberhalb
});

test("ungültige Werte (NaN/Infinity) -> [], kein Crash", () => {
  assert.deepEqual(sparrenTeilstuecke(NaN, 5, 2, 3), []);
  assert.deepEqual(sparrenTeilstuecke(0, 5, Infinity, 3), []);
  assert.deepEqual(sparrenTeilstuecke(5, 0, 2, 3), []); // vEnd<=vStart
});

test("Teilstück-Längen niemals negativ/NaN/Infinity", () => {
  const t = sparrenTeilstuecke(0, 6, 2.5, 3.5, 0.1);
  for (const s of t) {
    assert.ok(Number.isFinite(s.laengeM) && s.laengeM > 0);
  }
});
