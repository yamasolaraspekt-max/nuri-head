import { test } from "node:test";
import assert from "node:assert/strict";
import { polygonFlaecheM2 } from "../geometry/polygonFlaeche";

test("Rechteck 10×8 -> 80 m² (Satteldach-Fläche bleibt wie width×height)", () => {
  const rect = [{ x: 0, y: 0 }, { x: 10, y: 0 }, { x: 10, y: 8 }, { x: 0, y: 8 }];
  assert.equal(polygonFlaecheM2(rect), 80);
});

test("Dreieck (Walm-Stirnfläche) = halbe Bounding-Box", () => {
  // Basis 10, Höhe 4 -> 20 m² (Bounding-Box wäre 40 -> -50%)
  const dreieck = [{ x: 0, y: 0 }, { x: 10, y: 0 }, { x: 5, y: 4 }];
  assert.equal(polygonFlaecheM2(dreieck), 20);
});

test("Trapez (Walm-Hauptfläche) < Bounding-Box", () => {
  // Parallele Seiten 10 (unten) und 6 (oben), Höhe 4 -> (10+6)/2*4 = 32 m²
  const trapez = [{ x: 0, y: 0 }, { x: 10, y: 0 }, { x: 8, y: 4 }, { x: 2, y: 4 }];
  assert.equal(polygonFlaecheM2(trapez), 32);
  // Bounding-Box wäre 10*4 = 40 -> Polygon ist realistischer (kleiner)
  assert.ok(polygonFlaecheM2(trapez) < 10 * 4);
});

test("Unregelmäßiges Viereck (L-/T-Kehlfläche) plausibel", () => {
  const quad = [{ x: 0, y: 0 }, { x: 6, y: 0 }, { x: 6, y: 3 }, { x: 0, y: 5 }];
  // Shoelace: |(0*0-6*0)+(6*3-6*0)+(6*5-0*3)+(0*0-0*5)|/2 = |0+18+30+0|/2 = 24
  assert.equal(polygonFlaecheM2(quad), 24);
});

test("umgekehrte Punktreihenfolge -> identisches (positives) Ergebnis", () => {
  const cw = [{ x: 0, y: 0 }, { x: 0, y: 8 }, { x: 10, y: 8 }, { x: 10, y: 0 }];
  assert.equal(polygonFlaecheM2(cw), 80);
});

test("leeres Polygon / < 3 Punkte -> 0 (kein falscher Wert)", () => {
  assert.equal(polygonFlaecheM2([]), 0);
  assert.equal(polygonFlaecheM2([{ x: 0, y: 0 }]), 0);
  assert.equal(polygonFlaecheM2([{ x: 0, y: 0 }, { x: 1, y: 1 }]), 0);
  assert.equal(polygonFlaecheM2(undefined), 0);
  assert.equal(polygonFlaecheM2(null), 0);
});

test("ungültige Zahlenwerte -> 0, niemals NaN/Infinity", () => {
  const bad = [{ x: 0, y: 0 }, { x: NaN, y: 0 }, { x: 10, y: 8 }];
  const r = polygonFlaecheM2(bad);
  assert.equal(r, 0);
  assert.ok(Number.isFinite(r));
  const bad2 = [{ x: 0, y: 0 }, { x: Infinity, y: 0 }, { x: 10, y: 8 }];
  assert.equal(polygonFlaecheM2(bad2), 0);
});

test("THREE.Vector2-ähnliche Objekte (x/y) funktionieren", () => {
  const vec = [
    { x: 0, y: 0, clone() { return this; } },
    { x: 4, y: 0, clone() { return this; } },
    { x: 4, y: 3, clone() { return this; } },
    { x: 0, y: 3, clone() { return this; } },
  ];
  assert.equal(polygonFlaecheM2(vec as any), 12);
});
