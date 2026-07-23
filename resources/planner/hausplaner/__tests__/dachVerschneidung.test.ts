/**
 * EA26 Teilstufe 1: Regressionsschloss + SSOT-Prüfung der Kehl-/Gratlinien geneigter L-/T-/U-
 * Verschneidungen. Spiegelt die Engine-Formeln aus buildCompoundPitchedFaces unabhängig nach und
 * verifiziert, dass die reine Util dieselben Sparren-Endpunkte/-Längen liefert. Rein, THREE-frei.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  verschneidungslinien,
  kehlGratKennwerte,
  type VerschneidungEingabe,
} from '../geometry/dachVerschneidung';

const E = (over: Partial<VerschneidungEingabe> = {}): VerschneidungEingabe => ({
  form: 't', length: 10, width: 8, lengthB: 4, widthB: 4, overhang: 0.5, overhangGable: 0.3,
  pitchGrad: 35, height: 5, rafterHeight: 18, ...over,
});

/** Unabhängige Nachbildung der Engine-Konstanten (DachplanerProPage.buildCompoundPitchedFaces). */
function eng(e: VerschneidungEingabe) {
  const rad = e.pitchGrad * Math.PI / 180;
  const L = Math.max(0.1, e.length), W = Math.max(0.1, e.width), W_b = Math.max(0.1, e.widthB);
  const oh = e.overhang, ohG = e.overhangGable, rh = e.rafterHeight / 100;
  const cosR = Math.cos(rad), sinR = Math.sin(rad), tanR = Math.tan(rad);
  const hPivot = e.height + 0.14 + (rh / 2 - rh * 0.25) * cosR;
  const yEaveEdge = hPivot - oh * tanR;
  const hRC = (-rh / 2 + rh * 0.25) * cosR;
  const vPeak = (W_b / 2 + oh) / cosR;
  const yRidge = hPivot + ((W / 2 + oh) / cosR - oh / cosR) * sinR;
  const cx = e.form === 't' ? 0 : L / 2 - W_b / 2;
  const pEndPeak = { x: cx, y: yEaveEdge + vPeak * sinR + hRC, z: W / 2 - W_b / 2 };
  const pStartL = { x: cx - W_b / 2 - oh, y: yEaveEdge + hRC, z: W / 2 + oh };
  const pStartR = { x: cx + W_b / 2 + oh, y: yEaveEdge + hRC, z: W / 2 + oh };
  const d = (a: any, b: any) => Math.hypot(a.x - b.x, a.y - b.y, a.z - b.z);
  return { rad, W, W_b, oh, cx, hPivot, yEaveEdge, yRidge, tanR, pEndPeak, pStartL, pStartR, d };
}
const gleich = (a: any, b: any, t = 1e-9) => Math.abs(a.x - b.x) < t && Math.abs(a.y - b.y) < t && Math.abs(a.z - b.z) < t;

test('EA26: T-Form -> 2 Kehlen; Sparren-Endpunkte == Engine pStartL/pStartR -> pEndPeak (≤1e-9)', () => {
  const e = E({ form: 't' });
  const k = eng(e);
  const linien = verschneidungslinien(e);
  assert.equal(linien.length, 2);
  assert.equal(linien.filter((l) => l.art === 'kehle').length, 2);
  const links = linien.find((l) => l.seite === 'links')!, rechts = linien.find((l) => l.seite === 'rechts')!;
  assert.ok(gleich(links.start, k.pStartL), `start L ${JSON.stringify(links.start)}`);
  assert.ok(gleich(links.ende, k.pEndPeak), `ende L`);
  assert.ok(gleich(rechts.start, k.pStartR), `start R`);
  assert.ok(gleich(rechts.ende, k.pEndPeak));
});

test('EA26: L-Form -> 1 Kehle (links) + 1 Grat (rechts); Endpunkte == Engine', () => {
  const e = E({ form: 'l' });
  const k = eng(e);
  const linien = verschneidungslinien(e);
  assert.equal(linien.filter((l) => l.art === 'kehle').length, 1);
  assert.equal(linien.filter((l) => l.art === 'grat').length, 1);
  const links = linien.find((l) => l.seite === 'links')!, rechts = linien.find((l) => l.seite === 'rechts')!;
  assert.equal(links.art, 'kehle'); assert.equal(rechts.art, 'grat');
  assert.ok(gleich(links.start, k.pStartL) && gleich(links.ende, k.pEndPeak));
  assert.ok(gleich(rechts.start, k.pStartR) && gleich(rechts.ende, k.pEndPeak));
});

test('EA26: laenge3D == Engine-Distanz UND geschlossene Form (W_b/2+oh)·√(2+tan²a)', () => {
  const e = E({ form: 't' });
  const k = eng(e);
  const l = verschneidungslinien(e)[0];
  assert.ok(Math.abs(l.laenge3D - k.d(k.pStartL, k.pEndPeak)) < 1e-6, `util ${l.laenge3D} != eng ${k.d(k.pStartL, k.pEndPeak)}`);
  assert.ok(Math.abs(l.laenge3D - (k.W_b / 2 + k.oh) * Math.sqrt(2 + k.tanR ** 2)) < 1e-9);
});

test('EA26: 45°-Winkelhalbierende (|Δx| == |Δz|) und Kehlneigung tan β = tan a/√2', () => {
  const l = verschneidungslinien(E())[0];
  assert.ok(Math.abs(Math.abs(l.ende.x - l.start.x) - Math.abs(l.ende.z - l.start.z)) < 1e-9);
  assert.ok(Math.abs(l.grundrissWinkelGrad - 45) < 1e-6);
  assert.ok(Math.abs(Math.tan(l.neigungGrad * Math.PI / 180) - Math.tan(35 * Math.PI / 180) / Math.SQRT2) < 1e-9);
});

test('EA26: Ridge-Meet — endeDeck.y = hPivot + (W_b/2)·tan a = yRidgeExt < yRidge; ende.z = W/2−W_b/2', () => {
  const e = E();
  const k = eng(e);
  const l = verschneidungslinien(e)[0];
  assert.ok(Math.abs(l.endeDeck.y - (k.hPivot + (k.W_b / 2) * k.tanR)) < 1e-3);
  assert.ok(l.endeDeck.y < k.yRidge - 1e-3);
  assert.ok(Math.abs(l.ende.z - (k.W / 2 - k.W_b / 2)) < 1e-9);
});

test('EA26: kein Linien-Vertex über First (Welt)', () => {
  const e = E();
  const k = eng(e);
  for (const l of verschneidungslinien(e)) {
    for (const p of [l.start, l.ende, l.startDeck, l.endeDeck]) assert.ok(p.y <= k.yRidge + 1e-3, `Vertex über First: ${p.y}`);
  }
});

test('EA26: Default-Anker L10/W8/W_b4/oh0.5/35° -> laenge3D ≈ 3.9452 m, Neigung ≈ 26.34°', () => {
  const l = verschneidungslinien(E())[0];
  assert.ok(Math.abs(l.laenge3D - 3.9452) < 5e-4, `laenge=${l.laenge3D}`);
  assert.ok(Math.abs(l.neigungGrad - 26.34) < 0.01, `neigung=${l.neigungGrad}`);
});

test('EA26: kehlGratKennwerte geschlossene Form (Kreuzcheck)', () => {
  const a = 35 * Math.PI / 180;
  const kw = kehlGratKennwerte(2.5, a);
  assert.ok(Math.abs(kw.horizProjektion - 2.5 * Math.SQRT2) < 1e-9);
  assert.ok(Math.abs(kw.laenge3D - 2.5 * Math.sqrt(2 + Math.tan(a) ** 2)) < 1e-9);
  assert.ok(Math.abs(kw.neigungBeta - Math.atan(Math.tan(a) / Math.SQRT2)) < 1e-9);
});

test('EA26: U-Form Soll -> genau 2 spiegelsymmetrische Kehlen (gleiche Länge), Apex z=W/2−W_b/2', () => {
  const e = E({ form: 'u' });
  const k = eng(e);
  const u = verschneidungslinien(e);
  assert.equal(u.length, 2);
  assert.equal(u.filter((l) => l.art === 'kehle').length, 2);
  assert.ok(Math.abs(u[0].laenge3D - u[1].laenge3D) < 1e-9);
  for (const l of u) assert.ok(Math.abs(l.ende.z - (k.W / 2 - k.W_b / 2)) < 1e-9);
});

test('EA26: Sweep-Stabilität (pitch 15/30/35/45/60, W_b 2/4/6) -> keine NaN/Infinity', () => {
  for (const pitchGrad of [15, 30, 35, 45, 60]) for (const widthB of [2, 4, 6]) for (const form of ['l', 't', 'u'] as const) {
    for (const l of verschneidungslinien(E({ form, pitchGrad, widthB }))) {
      for (const p of [l.start, l.ende, l.startDeck, l.endeDeck]) {
        assert.ok(Number.isFinite(p.x) && Number.isFinite(p.y) && Number.isFinite(p.z), `NaN @ ${form}/${pitchGrad}/${widthB}`);
      }
      assert.ok(Number.isFinite(l.laenge3D) && Number.isFinite(l.neigungGrad));
    }
  }
});

test('EA26: entartete Eingaben (pitch≈0, widthB≤0, widthB≥W) -> pruefpflichtig=true, keine NaN', () => {
  for (const e of [E({ pitchGrad: 0 }), E({ widthB: 0 }), E({ widthB: 9 })]) {
    const linien = verschneidungslinien(e);
    for (const l of linien) {
      assert.equal(l.pruefpflichtig, true);
      for (const p of [l.start, l.ende, l.startDeck, l.endeDeck]) assert.ok(Number.isFinite(p.x) && Number.isFinite(p.y) && Number.isFinite(p.z));
    }
  }
});
