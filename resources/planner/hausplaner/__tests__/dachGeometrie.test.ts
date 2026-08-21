/**
 * D-b — Dach-Geometrie: empirische Proben gegen die Handrechnung der Spec
 * (docs/hausplaner/dach-andock-spec.md §4.1 Fläche, §4.2 Azimut) + Kanten 1–3.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import type { RoofNode } from '../domain/scene.types';
import { dachFlaechen, DachGeometrieUngueltig } from '../geometry/dachGeometrie';

const JETZT = '2026-07-17T12:00:00.000Z';
const cosG = (g: number) => Math.cos((g * Math.PI) / 180);
const EPS = 1e-6;

/** Rechteck breiteX × tiefeY (mm), Ecke bei 0,0. */
function rechteck(breiteX: number, tiefeY: number) {
  return [
    { x: 0, y: 0 },
    { x: breiteX, y: 0 },
    { x: breiteX, y: tiefeY },
    { x: 0, y: tiefeY },
  ];
}

function dach(over: Partial<RoofNode> & Pick<RoofNode, 'roofType' | 'neigungGrad' | 'firstAzimutGrad'>): RoofNode {
  return {
    id: 'r1', type: 'roof', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    polygon: rechteck(8000, 10000),
    ueberstandMm: 0,
    traufhoeheMm: 2500,
    ...over,
  };
}

const azSet = (fl: ReturnType<typeof dachFlaechen>) =>
  fl.map((f) => f.azimut_grad).sort((a, b) => Number(a) - Number(b));

// ---- §4.1 Fläche + §4.2 Azimut: Satteldach ----

test('Sattel 8×10 m, 35°, First N–S (parallel zur 10-m-Seite): je Fläche (8/2)/cos35·10 m², Azimute {90,270}', () => {
  const fl = dachFlaechen(dach({ roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0, polygon: rechteck(8000, 10000) }));
  assert.equal(fl.length, 2);
  const erwartet = (4 / cosG(35)) * 10; // (8/2)/cos35 · 10
  for (const f of fl) {
    assert.ok(Math.abs(f.flaeche_m2 - erwartet) < EPS, `Fläche ${f.flaeche_m2} ≠ ${erwartet}`);
    assert.equal(f.neigung_grad, 35);
    assert.equal(f.first_laenge_mm, 10000);
  }
  assert.deepEqual(azSet(fl), [90, 270]);
});

test('Sattel First O–W (parallel zur 10-m-Seite): Azimute {0,180} (Drehprobe §4.2)', () => {
  const fl = dachFlaechen(dach({ roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 90, polygon: rechteck(10000, 8000) }));
  const erwartet = (4 / cosG(35)) * 10;
  for (const f of fl) assert.ok(Math.abs(f.flaeche_m2 - erwartet) < EPS);
  assert.deepEqual(azSet(fl), [0, 180]);
});

// ---- Walm: Summe = Grundfläche / cos (Erhaltung), 4 Flächen, Firstlänge L−B ----

test('Walm 8×12 m, 30°: 4 Flächen, Summe = Grundfläche/cos30, Firstlänge 4000 mm, Azimute {0,90,180,270}', () => {
  const fl = dachFlaechen(dach({ roofType: 'walm', neigungGrad: 30, firstAzimutGrad: 0, polygon: rechteck(8000, 12000) }));
  assert.equal(fl.length, 4);
  const summe = fl.reduce((s, f) => s + f.flaeche_m2, 0);
  assert.ok(Math.abs(summe - (8 * 12) / cosG(30)) < EPS, `Summe ${summe} ≠ ${(8 * 12) / cosG(30)}`);
  for (const f of fl) assert.equal(f.first_laenge_mm, 4000); // L−B = 12−8 = 4 m
  assert.deepEqual(azSet(fl), [0, 90, 180, 270]);
});

// ---- Pult: eine Fläche = Grundfläche/cos ----

test('Pult 8×10 m, 20°: eine Fläche = Grundfläche/cos20, Azimut = First', () => {
  const fl = dachFlaechen(dach({ roofType: 'pult', neigungGrad: 20, firstAzimutGrad: 180, polygon: rechteck(8000, 10000) }));
  assert.equal(fl.length, 1);
  assert.ok(Math.abs(fl[0].flaeche_m2 - (8 * 10) / cosG(20)) < EPS);
  assert.equal(fl[0].azimut_grad, 180);
});

// ---- Flach: Fläche = Grundfläche, kein Azimut ----

test('Flachdach: Fläche = Grundfläche (8×10), Azimut null, Neigung 0', () => {
  const fl = dachFlaechen(dach({ roofType: 'flach', neigungGrad: 0, firstAzimutGrad: 0 }));
  assert.equal(fl.length, 1);
  assert.ok(Math.abs(fl[0].flaeche_m2 - 80) < EPS);
  assert.equal(fl[0].azimut_grad, null);
  assert.equal(fl[0].neigung_grad, 0);
});

// ---- Kante 1: nicht-rechteckige (L-förmige) Kontur wird abgelehnt (kein stilles Falschdach) ----

test('Kante 1: L-förmige Traufkontur → DachGeometrieUngueltig', () => {
  const lform = [
    { x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 4000 },
    { x: 4000, y: 4000 }, { x: 4000, y: 10000 }, { x: 0, y: 10000 },
  ];
  assert.throws(
    () => dachFlaechen(dach({ roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0, polygon: lform })),
    (e: unknown) => e instanceof DachGeometrieUngueltig && e.grund === 'kontur_nicht_rechteckig',
  );
});

// ---- Kante 2: steile Neigung kippt nicht (sichererCos), Fläche endlich/positiv ----

test('Kante 2: Sattel 89° bleibt endlich & positiv (sichererCos), 0° via Flach stabil', () => {
  const steil = dachFlaechen(dach({ roofType: 'sattel', neigungGrad: 89, firstAzimutGrad: 0 }));
  for (const f of steil) {
    assert.ok(Number.isFinite(f.flaeche_m2) && f.flaeche_m2 > 0);
  }
  const flach = dachFlaechen(dach({ roofType: 'flach', neigungGrad: 0, firstAzimutGrad: 0 }));
  assert.ok(Number.isFinite(flach[0].flaeche_m2));
});

// ---- Kante 3: Überstand wirkt an Traufe UND Giebel ----

test('Kante 3: Überstand 500 mm vergrößert Fläche und First-Länge (Traufe + Giebel)', () => {
  const ohne = dachFlaechen(dach({ roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0, ueberstandMm: 0 }));
  const mit = dachFlaechen(dach({ roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0, ueberstandMm: 500 }));
  assert.ok(mit[0].flaeche_m2 > ohne[0].flaeche_m2);
  assert.equal(mit[0].first_laenge_mm, 11000); // 10000 + 2·500
});

// ---- Z1-W1-2: Walmdach mit Giebelbreite > Gebäudelänge wird ABGELEHNT (Y-1) -------------------
//
// Vorher lieferte `dachFlaechen()` hier stumm eine zu große Fläche, weil die Firstlänge auf 0
// geklemmt wurde, während die Walm-Dreiecke auf voller Giebelbreite blieben. Die Zahlen unten sind
// von Hand gegen den Erhaltungssatz (Σ Facetten = Grundriss / cos α) gerechnet.
// `firstAzimutGrad: 0` ⇒ First entlang y, also `laenge` = y-Ausdehnung, `spann` = x-Ausdehnung.

test('Z1-W1-2 A: Walm 6×8 m, 30° (Giebel breiter als Länge) wirft statt 64,66 m² zu liefern', () => {
  assert.throws(
    () => dachFlaechen(dach({ roofType: 'walm', neigungGrad: 30, firstAzimutGrad: 0, polygon: rechteck(8000, 6000) })),
    (e: unknown) => e instanceof DachGeometrieUngueltig && e.grund === 'walm_giebelbreite_ueber_laenge',
    'erwartet: Absage. Vorher: 64,66 m² statt 55,43 m² (+16,7 %)',
  );
});

test('Z1-W1-2 B: Walm 4×10 m, 30° wirft statt 80,83 m² zu liefern (+75 %)', () => {
  assert.throws(
    () => dachFlaechen(dach({ roofType: 'walm', neigungGrad: 30, firstAzimutGrad: 0, polygon: rechteck(10000, 4000) })),
    (e: unknown) => e instanceof DachGeometrieUngueltig && e.grund === 'walm_giebelbreite_ueber_laenge',
    'erwartet: Absage. Vorher: 80,83 m² statt 46,19 m² (+75,0 %)',
  );
});

// **Die Gegenrichtung — und der Grund, warum `walmIstKonsistent` NICHT allein die Sperre stellt.**
// Die Funktion verlangt `L > B` und würde damit auch `L === B` abweisen. Das ist aber ein gültiges
// **Zeltdach**: vier gleiche Dreiecke, Firstlänge 0, und die Fläche stimmt exakt. Ohne diesen Test
// könnte jemand die Bedingung auf `!walmIstKonsistent(...)` verkürzen und dabei einen
// funktionierenden Fall sperren, ohne dass etwas rot wird.
test('Z1-W1-2 C: Zeltdach 8×8 m wird NICHT abgewiesen und erfüllt den Erhaltungssatz', () => {
  for (const grad of [30, 35, 45]) {
    const fl = dachFlaechen(dach({ roofType: 'walm', neigungGrad: grad, firstAzimutGrad: 0, polygon: rechteck(8000, 8000) }));
    assert.equal(fl.length, 4, `Zeltdach ${grad}°: vier Flächen`);
    const summe = fl.reduce((s, f) => s + f.flaeche_m2, 0);
    const soll = (8 * 8) / cosG(grad);
    assert.ok(Math.abs(summe - soll) < EPS, `Zeltdach ${grad}°: Summe ${summe} ≠ ${soll}`);
    for (const f of fl) assert.equal(f.first_laenge_mm, 0, 'Zeltdach hat Firstlänge 0');
  }
});

// ---- Z1-W1-3 · Kriterium B: NaN-Verhalten, VORHER und NACHHER am echten Modul gemessen -------
//
// **Ergebnis: unverändert — die NaN-Kontur wird in BEIDEN Fassungen abgewiesen.** Gemessen, indem
// dieselbe Zusage einmal gegen die alte private Shoelace-Kopie und einmal gegen `polygonFlaecheM2`
// gefahren wurde; beide Male wirft `pruefeRechteckigeKontur` mit `kontur_nicht_rechteckig`.
//
// **Und das ist eine Berichtigung meiner eigenen Annahme.** Ich hatte an einem NACHBAU der Formeln
// gerechnet und daraus geschlossen, die Prüfung lasse NaN durch, weil `bboxM2` selbst NaN werde und
// jeder Vergleich mit NaN falsch sei. Am echten Modul trifft das nicht zu. Der Nachbau war ein
// schlechtes Modell — die Lehre steht hier, weil sie sonst niemand sieht: **eine Verhaltensaussage
// gehört an das Objekt, nicht an eine Nachbildung davon.**
//
// Die Zusage bleibt trotzdem stehen: sie hält fest, dass der Formeltausch die Abweisung NICHT
// geschwächt hat — genau das, was Kriterium B belegen soll.
test('Z1-W1-3 B: NaN-Kontur wird abgewiesen — vor und nach dem Formeltausch gleich', () => {
  const nanPoly = [{ x: 0, y: 0 }, { x: NaN, y: 0 }, { x: 8000, y: 12000 }, { x: 0, y: 12000 }];
  assert.throws(
    () => dachFlaechen(dach({ roofType: 'sattel', neigungGrad: 30, firstAzimutGrad: 0, polygon: nanPoly })),
    (e: unknown) => e instanceof DachGeometrieUngueltig && e.grund === 'kontur_nicht_rechteckig',
    'die Abweisung darf durch den Formeltausch nicht verloren gehen',
  );
});

// Gegenprobe zur Zusage darüber: die gültige Kontur läuft weiterhin durch und liefert dieselbe
// Fläche wie vor dem Tausch (8×12 m ⇒ 96 m² Grundfläche). Ohne sie wäre „wirft immer" auch grün.
test('Z1-W1-3 B (Gegenprobe): gültige Kontur läuft durch und liefert unveränderte Fläche', () => {
  const fl = dachFlaechen(dach({ roofType: 'flach', neigungGrad: 0, firstAzimutGrad: 0, polygon: rechteck(8000, 12000) }));
  const summe = fl.reduce((s, f) => s + f.flaeche_m2, 0);
  assert.ok(Math.abs(summe - 96) < EPS, `Grundfläche ${summe} ≠ 96 m²`);
});
