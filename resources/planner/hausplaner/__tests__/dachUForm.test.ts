/**
 * EA27 — ABNAHME-GATE für den geneigten U-Bau (zwei spiegelsymmetrische Anbau-Satteldächer = „Flügel"
 * + Hauptdach-Satteldach). SSOT: src/utils/dachUForm.ts liefert dieselben Konstanten/Polygone/Flächen,
 * die die Engine (DachplanerProPage.buildCompoundPitchedU) zeichnet — geprüft wird die Util, der U-Bau
 * MUSS exakt deren Soll erzeugen. Kehl-Soll zusätzlich aus dachVerschneidung.ts (form:'u').
 *
 * Rein (KEIN three/React). KEINE Statik/Schneelast/Eindeckung/Produktwahl — nur Lage/Höhe/Länge.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { verschneidungslinien, type VerschneidungEingabe } from '../geometry/dachVerschneidung';
import {
  uFormKonstanten, mainSDoppelNotchPoly, uBauGueltig, uFormFlaechen, uFormKehlsparren,
  type UFormEingabe, type Punkt2D, type Vec3,
} from '../geometry/dachUForm';

const DEF: UFormEingabe = { length: 10, width: 8, widthB: 4, lengthB: 4, overhang: 0.5, overhangGable: 0.3, pitchGrad: 35, height: 5, rafterHeight: 18 };

function shoelace(poly: Punkt2D[]): number {
  let a = 0;
  for (let i = 0; i < poly.length; i++) { const p = poly[i], q = poly[(i + 1) % poly.length]; a += p.x * q.y - q.x * p.y; }
  return a / 2;
}
function ccw(A: Punkt2D, B: Punkt2D, C: Punkt2D): number { return (B.x - A.x) * (C.y - A.y) - (B.y - A.y) * (C.x - A.x); }
function istEinfach(poly: Punkt2D[]): boolean {
  const n = poly.length;
  for (let i = 0; i < n; i++) for (let j = i + 1; j < n; j++) {
    if (j === i || j === (i + 1) % n || i === (j + 1) % n) continue;
    const A = poly[i], B = poly[(i + 1) % n], C = poly[j], D = poly[(j + 1) % n];
    const d1 = ccw(C, D, A), d2 = ccw(C, D, B), d3 = ccw(A, B, C), d4 = ccw(A, B, D);
    if (((d1 > 0 && d2 < 0) || (d1 < 0 && d2 > 0)) && ((d3 > 0 && d4 < 0) || (d3 < 0 && d4 > 0))) return false;
  }
  return true;
}
const cross = (a: Vec3, b: Vec3): Vec3 => ({ x: a.y * b.z - a.z * b.y, y: a.z * b.x - a.x * b.z, z: a.x * b.y - a.y * b.x });
const vEin = (e: UFormEingabe): VerschneidungEingabe => ({
  form: 'u', length: e.length, width: e.width, lengthB: e.lengthB, widthB: e.widthB,
  overhang: e.overhang, overhangGable: e.overhangGable, pitchGrad: e.pitchGrad, height: e.height, rafterHeight: e.rafterHeight,
});

// ── 1) Default-Anker (Abnahme-Referenz EA26-U-Soll) ─────────────────────────────────────────────
test('EA27: Default — yRidge≈7.978, yRidgeExt≈6.577, cxWing=±3, apexZ=2', () => {
  const k = uFormKonstanten(DEF);
  assert.ok(Math.abs(k.yRidge - 7.9777) < 1e-3, `yRidge=${k.yRidge}`);
  assert.ok(Math.abs(k.yRidgeExt - 6.5773) < 1e-3, `yRidgeExt=${k.yRidgeExt}`);
  assert.ok(Math.abs(k.cxWing - 3) < 1e-9 && Math.abs(k.apexZ - 2) < 1e-9);
});

test('EA27: beide Anbaufirste = yRidgeExt < yRidge (Flügel niedriger als Hauptfirst)', () => {
  const k = uFormKonstanten(DEF);
  assert.ok(k.yRidgeExt < k.yRidge - 1e-3);
});

// ── 2) Zwei Kehlspitzen treffen das EA26-U-Soll ─────────────────────────────────────────────────
test('EA27: zwei spiegelsymmetrische Kehlen; Kehlspitzen == EA26-U-Soll (x=±3, y=yRidgeExt, z=2)', () => {
  const k = uFormKonstanten(DEF);
  const u = verschneidungslinien(vEin(DEF));
  assert.equal(u.length, 2);
  assert.equal(u.filter((l) => l.art === 'kehle').length, 2);
  assert.ok(Math.abs(u[0].laenge3D - u[1].laenge3D) < 1e-9);
  assert.ok(Math.abs(u[0].laenge3D - 3.9452) < 5e-4, `laenge3D=${u[0].laenge3D}`);
  for (const l of u) {
    assert.ok(Math.abs(Math.abs(l.ende.x) - k.cxWing) < 1e-9, `Kehlspitze x=${l.ende.x}`);
    assert.ok(Math.abs(l.ende.z - k.apexZ) < 1e-9, `Kehlspitze z=${l.ende.z}`);
    assert.ok(Math.abs(l.endeDeck.y - k.yRidgeExt) < 1e-3, `Kehlspitze y=${l.endeDeck.y}`);
  }
});

test('EA27: Kehl-START liegt INNEN (hofseitig) bei |x|=cxWing−(W_b/2+oh)=0.5', () => {
  const u = verschneidungslinien(vEin(DEF));
  for (const l of u) assert.ok(Math.abs(Math.abs(l.start.x) - 0.5) < 1e-9, `start.x=${l.start.x}`);
});

// ── 3) main_S Doppelnotch ───────────────────────────────────────────────────────────────────────
test('EA27: main_S-Doppelnotch positiv (Shoelace>0) und einfach (keine Selbstüberschneidung), 8 Punkte', () => {
  const poly = mainSDoppelNotchPoly(DEF);
  assert.equal(poly.length, 8);
  assert.ok(shoelace(poly) > 0, `Fläche=${shoelace(poly)}`);
  assert.ok(istEinfach(poly));
});

test('EA27: main_S — zwei Kehlspitzen bei v_peak (u≈2.3/8.3), Innen-Gap>0, Außen gekappt, kein v>First', () => {
  const k = uFormKonstanten(DEF);
  const poly = mainSDoppelNotchPoly(DEF);
  const peaks = poly.filter((p) => Math.abs(p.y - k.vPeak) < 1e-6);
  assert.equal(peaks.length, 2);
  assert.ok(Math.abs(peaks[0].x - 2.3) < 1e-3 && Math.abs(peaks[1].x - 8.3) < 1e-3);
  const innen = poly.filter((p) => Math.abs(p.y) < 1e-9).map((p) => p.x).sort((a, b) => a - b);
  assert.equal(innen.length, 2);
  assert.ok(innen[1] - innen[0] > 1e-6, `Innen-Gap ${innen[1] - innen[0]}`);
  for (const p of poly) assert.ok(p.y <= k.vMaxMain + 1e-9);
});

// ── 4) Flächen: Handedness + Kehl-Koinzidenz ────────────────────────────────────────────────────
test('EA27: alle 6 U-Flächen normal == uDir×vDir mit normal.y=cos a>0 (kein Backface)', () => {
  const k = uFormKonstanten(DEF);
  const flaechen = uFormFlaechen(DEF);
  assert.equal(flaechen.length, 6);
  for (const f of flaechen) {
    const cn = cross(f.uDir, f.vDir);
    assert.ok(Math.abs(cn.x - f.normal.x) < 1e-9 && Math.abs(cn.y - f.normal.y) < 1e-9 && Math.abs(cn.z - f.normal.z) < 1e-9, `${f.id}: normal != uDir×vDir`);
    assert.ok(cn.y > 0 && Math.abs(cn.y - k.cos) < 1e-9, `${f.id}: normal.y=${cn.y} (Backface)`);
  }
});

test('EA27: Kehlsparren — zwei, je Länge (W_b/2+oh)√(2+tan²a)≈3.945, START innen bei |x|=0.5, Apex z=2', () => {
  const k = uFormKonstanten(DEF);
  const ks = uFormKehlsparren(DEF);
  assert.equal(ks.length, 2);
  const soll = (k.W_b / 2 + k.oh) * Math.sqrt(2 + k.tan ** 2);
  for (const s of ks) {
    const len = Math.hypot(s.ende.x - s.start.x, s.ende.y - s.start.y, s.ende.z - s.start.z);
    assert.ok(Math.abs(len - soll) < 1e-6, `len=${len} soll=${soll}`);
    assert.ok(Math.abs(Math.abs(s.start.x) - 0.5) < 1e-9, `start.x=${s.start.x}`);
    assert.ok(Math.abs(s.ende.z - k.apexZ) < 1e-9);
    assert.ok(Math.abs(Math.abs(s.ende.x) - k.cxWing) < 1e-9);
  }
  assert.ok(Math.abs(soll - 3.9452) < 5e-4);
});

// ── 5) Gültigkeit / Guards ──────────────────────────────────────────────────────────────────────
test('EA27-Guard: Default-U gültig; überbreite Flügel / entartet → ungültig (buildFlat-Fallback)', () => {
  assert.equal(uBauGueltig(DEF), true);
  assert.equal(uBauGueltig({ ...DEF, widthB: 6 }), false); // 2·6+1=13 > 10 -> Notches überschneiden
  for (const bad of [{ ...DEF, pitchGrad: 0 }, { ...DEF, widthB: 0 }, { ...DEF, widthB: 9 }]) {
    assert.equal(uBauGueltig(bad), false);
    for (const l of verschneidungslinien(vEin(bad))) assert.equal(l.pruefpflichtig, true);
  }
});

// ── 6) Sweep-Stabilität ─────────────────────────────────────────────────────────────────────────
test('EA27: Sweep pitch {15,30,35,45}° × W_b {2,3,4} — keine NaN, yRidgeExt<yRidge, Polygon gültig', () => {
  for (const pitchGrad of [15, 30, 35, 45]) for (const widthB of [2, 3, 4]) {
    const e = { ...DEF, pitchGrad, widthB };
    const k = uFormKonstanten(e);
    assert.ok(Number.isFinite(k.yRidge) && Number.isFinite(k.yRidgeExt));
    assert.ok(k.yRidgeExt < k.yRidge - 1e-6);
    if (uBauGueltig(e)) {
      const poly = mainSDoppelNotchPoly(e);
      assert.ok(shoelace(poly) > 0 && istEinfach(poly), `Polygon ungültig @ ${pitchGrad}/${widthB}`);
      for (const f of uFormFlaechen(e)) for (const p of f.poly) assert.ok(Number.isFinite(p.x) && Number.isFinite(p.y));
      for (const s of uFormKehlsparren(e)) for (const v of [s.start, s.ende]) assert.ok(Number.isFinite(v.x) && Number.isFinite(v.y) && Number.isFinite(v.z));
    }
  }
});
