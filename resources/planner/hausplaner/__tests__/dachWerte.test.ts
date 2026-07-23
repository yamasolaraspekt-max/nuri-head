import { test } from "node:test";
import assert from "node:assert/strict";
import {
  cmZuMFloor,
  dachstuhlMasseM,
  dachstuhlHinweise,
  effektivCm,
  istUngueltig,
  sichererCos,
  DACH_FLOOR_M,
  DACH_FLOOR_CM,
} from "../geometry/dachWerte";

// --- cmZuMFloor: identisch zur Engine-Formel Math.max(floor, (v||0)/100) ---
test("cmZuMFloor: gültiger Wert wird korrekt in Meter umgerechnet", () => {
  assert.equal(cmZuMFloor(70, DACH_FLOOR_M.rafterDist), 0.7);
  assert.equal(cmZuMFloor(34, DACH_FLOOR_M.battenDist), 0.34);
});

test("cmZuMFloor: 0 / leer(NaN) / negativ ergeben den Mindestwert (kein 0/NaN)", () => {
  assert.equal(cmZuMFloor(0, 0.05), 0.05);
  assert.equal(cmZuMFloor(NaN, 0.05), 0.05);
  assert.equal(cmZuMFloor(-10, 0.05), 0.05);
  // Ergebnis ist immer endlich und > 0 -> keine Division durch null möglich
  for (const v of [0, NaN, -5, undefined as unknown as number]) {
    const m = cmZuMFloor(v, 0.05);
    assert.ok(Number.isFinite(m) && m > 0, `Floor verletzt für ${String(v)}`);
  }
});

test("cmZuMFloor reproduziert die Engine-Formel exakt", () => {
  const engine = (v: number, floor: number) => Math.max(floor, (v || 0) / 100);
  for (const v of [70, 34, 8, 16, 0, -3, 5]) {
    assert.equal(cmZuMFloor(v, 0.05), engine(v, 0.05));
    assert.equal(cmZuMFloor(v, 0.02), engine(v, 0.02));
  }
});

// --- dachstuhlMasseM: die EINE Wahrheit für Geometrie + Liste ---
test("dachstuhlMasseM: Standardwerte korrekt", () => {
  const m = dachstuhlMasseM({ rafterSpacing: 70, battenDist: 34, rafterWidth: 8, rafterHeight: 16 });
  assert.deepEqual(m, { rafterDist: 0.7, battenDist: 0.34, rafterW: 0.08, rafterH: 0.16 });
});

test("dachstuhlMasseM: ungültige Eingaben -> alle Floors, BOM-Division bleibt endlich", () => {
  const m = dachstuhlMasseM({ rafterSpacing: 0, battenDist: NaN, rafterWidth: -1, rafterHeight: 0 });
  assert.equal(m.rafterDist, 0.05);
  assert.equal(m.battenDist, 0.05);
  assert.equal(m.rafterW, 0.02);
  assert.equal(m.rafterH, 0.02);
  // genau der zuvor kaputte BOM-Ausdruck: Math.floor(width / rafterDist)
  const rCount = Math.floor(8 / m.rafterDist) + 1;
  assert.ok(Number.isFinite(rCount), "rCount muss endlich sein (vorher Infinity)");
  // und der Holz-Volumen-Ausdruck: laenge * rafterW * rafterH
  const vol = 100 * m.rafterW * m.rafterH;
  assert.ok(Number.isFinite(vol) && !Number.isNaN(vol), "Holzvolumen darf nicht NaN sein");
});

// --- Hinweise ---
test("dachstuhlHinweise: keine Hinweise bei plausiblen Werten", () => {
  assert.deepEqual(
    dachstuhlHinweise({ rafterSpacing: 70, battenDist: 34, rafterWidth: 8, rafterHeight: 16 }),
    [],
  );
});

test("dachstuhlHinweise: meldet jedes geklemmte Feld", () => {
  const h = dachstuhlHinweise({ rafterSpacing: 0, battenDist: NaN, rafterWidth: 1, rafterHeight: -2 });
  assert.equal(h.length, 4);
  assert.ok(h[0].includes("Sparrenabstand"));
  assert.ok(h.some((m) => m.includes("Lattenabstand")));
});

// --- Anzeige + Hilfen ---
test("effektivCm: zeigt den tatsächlich verwendeten Wert (konsistent zur Berechnung)", () => {
  assert.equal(effektivCm(70, DACH_FLOOR_CM.rafterSpacing), 70);
  assert.equal(effektivCm(0, DACH_FLOOR_CM.rafterSpacing), 5);
  assert.equal(effektivCm(NaN, DACH_FLOOR_CM.battenDist), 5);
});

test("istUngueltig erkennt leer/NaN/0/negativ", () => {
  for (const v of [0, -1, NaN, undefined, null, "5"]) assert.equal(istUngueltig(v as number), true);
  assert.equal(istUngueltig(70), false);
});

test("sichererCos: verhindert Division durch null bei ~90°", () => {
  assert.ok(sichererCos(90) >= 1e-3, "cos(90°) darf nicht 0 sein");
  assert.ok(Math.abs(sichererCos(35) - Math.cos((35 * Math.PI) / 180)) < 1e-9);
});
