/**
 * Numerische Verifikation der STEHENDEN Dachaufbau-Geometrie (Pult-/Giebel-/Spitzgaube, Kamin) und
 * ihres Anschlusses an das Hauptdach — OHNE 3D-Render. Spiegelt die Engine-Formeln und prüft die
 * physikalischen Bedingungen (kein Vertex über First, Anschlusskante auf Hauptdach, Front lotrecht,
 * Fuß eingebettet). Dies ist das Regressionsgate des „Dach-3D-Geometrie-Prüfer"-Agenten.
 *
 * Test-Runner: node:test + node:assert/strict (wie dachformVorlagen.test.ts). Rein, THREE-frei.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  pultGaubeGeometrie,
  giebelGaubeGeometrie,
  fussabdruckUV,
  kaminGeometrie,
  pruefeAufbau,
  surfacePointRein,
  aufbauBasis,
  weltAusLokal,
  signierterAbstand,
  hauptdachAusFrame,
  neigungAusFrame,
  MIN_PULT_GRAD,
  type SurfaceFrame,
  type GaubeEingabe,
} from '../geometry/gaubeGeometrie';

const RT = (x: number) => Math.round(x * 1e6) / 1e6;

/** Baut den main_S-Flächenrahmen exakt wie buildSattel (Satteldach-Südfläche). */
function makeSattelFrameS(L = 10, W = 8, height = 5, pitchGrad = 35, oh = 0.5, ohG = 0.3, rafterHeight = 18): { frame: SurfaceFrame; yRidge: number } {
  const rad = (pitchGrad * Math.PI) / 180;
  const slopeLen = (W / 2 + oh) / Math.cos(rad);
  const uMaxMain = L + 2 * ohG;
  const kerve = (rafterHeight / 100) * 0.25;
  const hPivot = height + 0.14 + (rafterHeight / 100 / 2 - kerve) * Math.cos(rad);
  const yEaveEdge = hPivot - oh * Math.tan(rad);
  const yRidge = hPivot + (slopeLen - oh / Math.cos(rad)) * Math.sin(rad);
  const frame: SurfaceFrame = {
    origin: { x: -L / 2 - ohG, y: yEaveEdge, z: W / 2 + oh },
    vRight: { x: 1, y: 0, z: 0 },
    vDown: { x: 0, y: Math.sin(rad), z: -Math.cos(rad) },
    vNormal: { x: 0, y: Math.cos(rad), z: Math.sin(rad) },
    width: uMaxMain, height: slopeLen,
  };
  return { frame, yRidge };
}

const schlepp = (over: Partial<GaubeEingabe> = {}): GaubeEingabe => ({
  type: 'schleppgaube', x: 0.5, y: 0.42, width: 2.5, height: 1.5, depth: 2.5, pitch: 15, ...over,
});

// --- Grundlagen / Engine-Spiegelung ---------------------------------------------------------------
test('Frame main_S: Referenzpunkt der Default-Schleppgaube ≈ (0, 6.166, 2.621); First ≈ 7.978', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const ref = surfacePointRein(frame, 0.5, 0.42, 0.02);
  assert.equal(RT(ref.x), 0);
  assert.ok(Math.abs(ref.y - 6.166) < 0.01, `ref.y=${ref.y}`);
  assert.ok(Math.abs(ref.z - 2.621) < 0.01, `ref.z=${ref.z}`);
  assert.ok(Math.abs(yRidge - 7.978) < 0.01, `yRidge=${yRidge}`);
});

test('neigungAusFrame liefert die Dachneigung zurück (35°)', () => {
  const { frame } = makeSattelFrameS();
  assert.ok(Math.abs(neigungAusFrame(frame) - (35 * Math.PI) / 180) < 1e-9);
});

// --- Pultgaube: Kernkorrektur -------------------------------------------------------------------
test('Pultgaube: Pultneigung wird abgeleitet (tan b = tan a − h/d), NICHT obs.pitch', () => {
  const a = (35 * Math.PI) / 180;
  const g = pultGaubeGeometrie(schlepp(), a);
  assert.ok(Math.abs(Math.tan(g.pultPitchRad) - (Math.tan(a) - 1.5 / 2.5)) < 1e-9, `b=${g.pultPitchRad}`);
  assert.ok(g.pultPitchRad > 0 && g.pultPitchRad < a);
  assert.notEqual(RT(g.pultPitchRad), RT((15 * Math.PI) / 180)); // nicht der advisorische pitch
});

test('Pultgaube: Machbarkeit d·tan a > h (Default erfüllt, keine Klemmung)', () => {
  const a = (35 * Math.PI) / 180;
  assert.ok(2.5 * Math.tan(a) > 1.5);
  const g = pultGaubeGeometrie(schlepp(), a);
  assert.equal(g.feasible, true);
  assert.equal(g.angepasst, false);
  assert.equal(RT(g.h), 1.5);
});

test('Pultgaube: zu hohe Front auf flachem Dach wird geklemmt (angepasst, b ≥ Mindestneigung)', () => {
  const a = (35 * Math.PI) / 180;
  const g = pultGaubeGeometrie(schlepp({ height: 5 }), a); // h viel zu groß
  assert.equal(g.angepasst, true);
  assert.ok(g.h < 5 && g.h > 0);
  assert.ok(g.pultPitchRad >= (MIN_PULT_GRAD * Math.PI) / 180 - 1e-9);
});

test('Pultgaube: KEIN Vertex über First (Welt) — Kernbug behoben', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const a = neigungAusFrame(frame);
  const ref = surfacePointRein(frame, 0.5, 0.42, 0.02);
  const basis = aufbauBasis(frame);
  const g = pultGaubeGeometrie(schlepp(), a);
  const verts = [...Object.values(g.verts)];
  for (const v of verts) {
    const P = weltAusLokal(ref, basis, v);
    assert.ok(P.y <= yRidge - 1e-3, `Vertex über First: y=${P.y} > ${yRidge}`);
  }
});

test('Pultgaube: Anschlusskante hinten liegt auf der Hauptdachebene (s ≈ 0.02), Welt-Y ≈ 7.04', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const a = neigungAusFrame(frame);
  const ref = surfacePointRein(frame, 0.5, 0.42, 0.02);
  const basis = aufbauBasis(frame);
  const hd = hauptdachAusFrame(frame, yRidge);
  const g = pultGaubeGeometrie(schlepp(), a);
  for (const v of [g.verts.bL, g.verts.bR]) {
    const P = weltAusLokal(ref, basis, v);
    assert.ok(Math.abs(signierterAbstand(P, hd) - 0.02) <= 2e-2, `Anschluss nicht auf Dach: s=${signierterAbstand(P, hd)}`);
    assert.ok(Math.abs(P.y - 7.04) < 0.05, `Anschluss-Welt-Y=${P.y}`);
  }
});

test('Pultgaube: KEINE Rückwand — höchster Vertex liegt auf dem Dach (nicht als Wand darüber)', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const a = neigungAusFrame(frame);
  const ref = surfacePointRein(frame, 0.5, 0.42, 0.02);
  const basis = aufbauBasis(frame);
  const hd = hauptdachAusFrame(frame, yRidge);
  const g = pultGaubeGeometrie(schlepp(), a);
  const welt = Object.values(g.verts).map((v) => weltAusLokal(ref, basis, v));
  const hi = welt.reduce((m, p) => (p.y > m.y ? p : m), welt[0]);
  assert.ok(signierterAbstand(hi, hd) <= 0.02 + 1e-2, `höchster Punkt steht als Wand über Dach: s=${signierterAbstand(hi, hd)}`);
});

test('Pultgaube: Front lotrecht (Unter-/Oberkante teilen lx,lz; nur ly unterscheidet)', () => {
  const g = pultGaubeGeometrie(schlepp(), (35 * Math.PI) / 180);
  assert.equal(RT(g.verts.fTL.lx), RT(g.verts.fBL.lx));
  assert.equal(RT(g.verts.fTL.lz), RT(g.verts.fBL.lz));
  assert.ok(g.verts.fTL.ly - g.verts.fBL.ly > 0);
});

test('Pultgaube: Front-Unterkante liegt auf dem Dach (Fuß sitzt auf, kein Schweben)', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const a = neigungAusFrame(frame);
  const ref = surfacePointRein(frame, 0.5, 0.42, 0.02);
  const basis = aufbauBasis(frame);
  const hd = hauptdachAusFrame(frame, yRidge);
  const g = pultGaubeGeometrie(schlepp(), a);
  for (const v of [g.verts.fBL, g.verts.fBR]) {
    assert.ok(Math.abs(signierterAbstand(weltAusLokal(ref, basis, v), hd) - 0.02) <= 2e-2);
  }
});

test('Pultgaube: Pultdach fällt zur Traufe (Front-Oberkante tiefer als Anschlusskante)', () => {
  const g = pultGaubeGeometrie(schlepp(), (35 * Math.PI) / 180);
  assert.ok(g.verts.fTL.ly < g.verts.bL.ly);
});

test('Pultgaube: Anschlusslinien hinten/links/rechts/front sind definiert und endlich', () => {
  const g = pultGaubeGeometrie(schlepp(), (35 * Math.PI) / 180);
  for (const lin of [g.anschluss.hinten, g.anschluss.links, g.anschluss.rechts, g.anschluss.front]) {
    for (const p of lin) {
      assert.ok(Number.isFinite(p.lx) && Number.isFinite(p.ly) && Number.isFinite(p.lz));
    }
  }
});

test('Trapezgaube: Front schmaler als Rückanschluss (Wangen spreizen up-slope)', () => {
  const g = pultGaubeGeometrie(schlepp({ type: 'trapezgaube', width: 3 }), (35 * Math.PI) / 180);
  const frontBreite = g.verts.fBR.lx - g.verts.fBL.lx;
  const backBreite = g.verts.bR.lx - g.verts.bL.lx;
  assert.ok(frontBreite < backBreite, `front ${frontBreite} >= back ${backBreite}`);
});

// --- pruefeAufbau (Ampel) ------------------------------------------------------------------------
test('pruefeAufbau: Default-Schleppgaube ist GRÜN (alle kritischen Kriterien erfüllt)', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const b = pruefeAufbau(frame, schlepp(), yRidge);
  assert.equal(b.ampel, 'gruen', `Ampel=${b.ampel}: ${b.kernbefund}`);
  assert.ok(b.kriterien.find((k) => k.id.startsWith('AK1'))!.ok);
  assert.ok(b.kriterien.find((k) => k.id.startsWith('AK2'))!.ok);
});

test('pruefeAufbau: alle Pulttypen GRÜN über Neigungs-Sweep 15/30/35/45° (feasible)', () => {
  for (const deg of [15, 30, 35, 45]) {
    const { frame, yRidge } = makeSattelFrameS(10, 8, 5, deg);
    for (const type of ['schleppgaube', 'flachgaube', 'trapezgaube']) {
      const b = pruefeAufbau(frame, schlepp({ type, height: 1.2, depth: 2.6, width: 2.4 }), yRidge);
      assert.notEqual(b.ampel, 'rot', `${type}@${deg}°: ROT — ${b.kernbefund}`);
    }
  }
});

test('pruefeAufbau: Giebelgaube bleibt unter First (Apex geklemmt) — nicht ROT', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const b = pruefeAufbau(frame, schlepp({ type: 'giebelgaube', pitch: 35 }), yRidge);
  assert.notEqual(b.ampel, 'rot', `Giebel ROT: ${b.kernbefund}`);
  assert.ok(b.kriterien.find((k) => k.id.startsWith('AK1'))!.ok, 'Apex über First');
});

test('Giebelgaube: Apex wird unter den Hauptfirst geklemmt (lyApexMax)', () => {
  const a = (35 * Math.PI) / 180;
  const ohneClamp = giebelGaubeGeometrie(schlepp({ type: 'giebelgaube' }), a);
  const mitClamp = giebelGaubeGeometrie(schlepp({ type: 'giebelgaube' }), a, ohneClamp.apex.ly - 0.5);
  assert.ok(mitClamp.apex.ly <= ohneClamp.apex.ly - 0.5 + 1e-9);
  assert.equal(mitClamp.angepasst, true);
});

// --- Kamin ---------------------------------------------------------------------------------------
test('Kamin: Sockeltiefe an Neigung gekoppelt (max(0.8, (d/2)·tan a + 0.15))', () => {
  const a = (45 * Math.PI) / 180;
  const k = kaminGeometrie(schlepp({ type: 'chimney', width: 1.2, depth: 1.2, height: 1.0 }), a);
  assert.equal(RT(k.sockel), RT(Math.max(0.8, 0.6 * Math.tan(a) + 0.15)));
});

test('pruefeAufbau: Kamin lotrecht + Sockel spaltfrei (nicht ROT)', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const b = pruefeAufbau(frame, schlepp({ type: 'chimney', width: 0.6, depth: 0.6, height: 1.0 }), yRidge);
  assert.notEqual(b.ampel, 'rot', `Kamin ROT: ${b.kernbefund}`);
  assert.ok(b.kriterien.find((k) => k.id.startsWith('AK5'))!.ok, 'Kaminsockel nicht spaltfrei');
});

// --- Robustheit ----------------------------------------------------------------------------------
test('Robustheit: keine NaN/Infinity/negativen Maße bei Extremwerten', () => {
  const a = (30 * Math.PI) / 180;
  for (const e of [schlepp({ width: 0, height: 0, depth: 0 }), schlepp({ width: 1e6, height: 1e6, depth: 1e6 }), schlepp({ height: -5 })]) {
    const g = pultGaubeGeometrie(e, a);
    for (const v of Object.values(g.verts)) {
      assert.ok(Number.isFinite(v.lx) && Number.isFinite(v.ly) && Number.isFinite(v.lz), `NaN/Inf in ${JSON.stringify(v)}`);
    }
    assert.ok(g.h >= 0 && g.d >= 0 && g.w >= 0);
  }
});

test('Nordfläche main_N: Anschlusskante liegt ebenfalls auf der Hauptdachebene (Basis-Vorzeichen korrekt)', () => {
  const { frame, yRidge } = makeSattelFrameS();
  // Nordfläche: vDown/Normal mit gekipptem z-Vorzeichen
  const rad = (35 * Math.PI) / 180;
  const fN: SurfaceFrame = {
    origin: { x: 5.3, y: frame.origin.y, z: -4.5 },
    vRight: { x: -1, y: 0, z: 0 },
    vDown: { x: 0, y: Math.sin(rad), z: Math.cos(rad) },
    vNormal: { x: 0, y: Math.cos(rad), z: -Math.sin(rad) },
    width: frame.width, height: frame.height,
  };
  const b = pruefeAufbau(fN, schlepp(), yRidge);
  assert.notEqual(b.ampel, 'rot', `Nordfläche ROT: ${b.kernbefund}`);
});

// --- EA24: planare Giebel-/Satteldachgaube + echte Plane∩Plane-Kehlschnittlinien -----------------
const giebel = (over: Partial<GaubeEingabe> = {}): GaubeEingabe => ({
  type: 'giebelgaube', x: 0.5, y: 0.42, width: 2.5, height: 1.5, depth: 2.5, pitch: 35, ...over,
});

test('EA24: Giebelgaube-Dachflächen sind EBEN (planar) — links & rechts', () => {
  const a = (35 * Math.PI) / 180;
  const g = giebelGaubeGeometrie(giebel(), a);
  assert.equal(g.dachPlanar, true);
  const m = Math.tan(a);
  const lyApex = g.verts.apex.ly;
  for (const v of [g.verts.fWL, g.verts.kehleFrontL, g.verts.firstEnde, g.verts.apex]) {
    assert.ok(Math.abs(v.ly - (lyApex + m * v.lx)) <= 1e-9, `links nicht planar: ${JSON.stringify(v)}`);
  }
  for (const v of [g.verts.fWR, g.verts.kehleFrontR, g.verts.firstEnde, g.verts.apex]) {
    assert.ok(Math.abs(v.ly - (lyApex - m * v.lx)) <= 1e-9, `rechts nicht planar: ${JSON.stringify(v)}`);
  }
});

test('EA24: First horizontal (apex.ly == firstEnde.ly, beide lx=0)', () => {
  const g = giebelGaubeGeometrie(giebel(), (35 * Math.PI) / 180);
  assert.ok(Math.abs(g.verts.apex.ly - g.verts.firstEnde.ly) <= 1e-9);
  assert.equal(g.verts.apex.lx, 0);
  assert.equal(g.verts.firstEnde.lx, 0);
});

test('EA24: Kehl-/Firstpunkte liegen auf der Hauptdach-Ebene (cosA·ly + sinA·lz = 0)', () => {
  const a = (35 * Math.PI) / 180;
  const g = giebelGaubeGeometrie(giebel(), a);
  for (const P of [g.verts.kehleFrontL, g.verts.kehleFrontR, g.verts.firstEnde]) {
    assert.ok(Math.abs(Math.cos(a) * P.ly + Math.sin(a) * P.lz) <= 1e-9, `nicht auf Dach: ${JSON.stringify(P)}`);
  }
});

test('EA24: echteKehle=true + Returns; Kehle = [kehleFront, firstEnde]', () => {
  const g = giebelGaubeGeometrie(giebel(), (35 * Math.PI) / 180);
  assert.equal(g.echteKehle, true);
  assert.deepEqual(g.echteKehleLinks, [g.verts.kehleFrontL, g.verts.firstEnde]);
  assert.deepEqual(g.echteKehleRechts, [g.verts.kehleFrontR, g.verts.firstEnde]);
  assert.equal(g.wangeTris.length, 2); // hWall>0 -> zwei Wangen
});

test('EA24: Kehlrichtung == Plane∩Plane (nMain×nL parallel zur Sehne)', () => {
  const a = (35 * Math.PI) / 180;
  const g = giebelGaubeGeometrie(giebel(), a);
  const halfW = 1.25, rG = halfW * Math.tan(a);
  const nMain = { x: 0, y: Math.cos(a), z: Math.sin(a) };
  const nL = { x: -rG, y: halfW, z: 0 };
  const tL = { x: nMain.y * nL.z - nMain.z * nL.y, y: nMain.z * nL.x - nMain.x * nL.z, z: nMain.x * nL.y - nMain.y * nL.x };
  const ch = { x: g.verts.kehleFrontL.lx - g.verts.firstEnde.lx, y: g.verts.kehleFrontL.ly - g.verts.firstEnde.ly, z: g.verts.kehleFrontL.lz - g.verts.firstEnde.lz };
  const nn = (v: any) => { const L = Math.hypot(v.x, v.y, v.z) || 1; return { x: v.x / L, y: v.y / L, z: v.z / L }; };
  const u = nn(tL), w2 = nn(ch);
  const cr = Math.hypot(u.y * w2.z - u.z * w2.y, u.z * w2.x - u.x * w2.z, u.x * w2.y - u.y * w2.x);
  assert.ok(cr <= 1e-6, `Kehlrichtung != Plane-Schnitt: ${cr}`);
});

test('EA24: Wangen-Unterkante (fBL→kehleFront) liegt auf der Hauptdach-Ebene', () => {
  const a = (35 * Math.PI) / 180;
  const g = giebelGaubeGeometrie(giebel(), a);
  const mid = {
    ly: (g.verts.fBL.ly + g.verts.kehleFrontL.ly) / 2,
    lz: (g.verts.fBL.lz + g.verts.kehleFrontL.lz) / 2,
  };
  assert.ok(Math.abs(Math.cos(a) * mid.ly + Math.sin(a) * mid.lz) <= 1e-9);
});

test('EA24: Spitzgaube (hWall=0) — kehleFront≡fBL, keine Wangen, echte Kehle [fBL, firstEnde]', () => {
  const a = (35 * Math.PI) / 180;
  const sp = giebelGaubeGeometrie(giebel({ type: 'spitzgaube' }), a);
  assert.ok(Math.abs(sp.verts.kehleFrontL.lx - sp.verts.fBL.lx) <= 1e-9
    && Math.abs(sp.verts.kehleFrontL.ly - sp.verts.fBL.ly) <= 1e-9
    && Math.abs(sp.verts.kehleFrontL.lz - sp.verts.fBL.lz) <= 1e-9, 'kehleFront != fBL bei hWall=0');
  assert.equal(sp.wangeTris.length, 0);
  for (const t of sp.dachTris) for (const p of t) assert.ok(Number.isFinite(p.lx) && Number.isFinite(p.ly) && Number.isFinite(p.lz));
});

test('EA24: Rückfall Flachdach (tanA→0) — echteKehle=false, keine NaN, Legacy-Dach', () => {
  const flach = giebelGaubeGeometrie(giebel(), 1e-9);
  assert.equal(flach.echteKehle, false);
  assert.equal(flach.echteKehleLinks, undefined);
  for (const t of flach.dachTris) for (const p of t) assert.ok(Number.isFinite(p.ly), 'NaN im Rückfall');
});

test('EA24: Rückfall flache Gaubenneigung (pitch≈0) — echteKehle=false, keine NaN', () => {
  const flachG = giebelGaubeGeometrie(giebel({ pitch: 0.001 }), (35 * Math.PI) / 180);
  assert.equal(flachG.echteKehle, false);
  for (const t of flachG.dachTris) for (const p of t) assert.ok(Number.isFinite(p.ly));
});

test('EA24: pruefeAufbau Giebelgaube bleibt nicht-ROT über Neigungs-Sweep (planar + Kehle)', () => {
  for (const deg of [15, 30, 35, 45]) {
    const fr = makeSattelFrameS(10, 8, 5, deg);
    const b = pruefeAufbau(fr.frame, giebel({ width: 2.4, height: 1.2, depth: 2.6 }), fr.yRidge);
    assert.notEqual(b.ampel, 'rot', `giebel@${deg}: ${b.kernbefund}`);
  }
});

test('EA24: kein Vertex über First (Welt) inkl. Wangen, Apex unter First geklemmt', () => {
  const { frame, yRidge } = makeSattelFrameS();
  const a = neigungAusFrame(frame);
  const ref = surfacePointRein(frame, 0.5, 0.42, 0.02);
  const basis = aufbauBasis(frame);
  const g = giebelGaubeGeometrie(giebel(), a, yRidge - 1e-3 - ref.y);
  const pts = [...g.koerperTris, ...g.dachTris].flat();
  for (const p of pts) {
    const Pw = weltAusLokal(ref, basis, p);
    assert.ok(Pw.y <= yRidge - 1e-3, `Vertex über First: ${Pw.y} > ${yRidge}`);
  }
});

// --- EA25: Gauben-Fußabdruck als (u,v)-Polygon (Pentagon/Trapez) für das echte Hauptdach-Loch -----
const shoelace = (poly: { x: number; y: number }[]) => {
  let a = 0;
  for (let i = 0; i < poly.length; i++) { const p = poly[i], q = poly[(i + 1) % poly.length]; a += p.x * q.y - q.x * p.y; }
  return Math.abs(a) / 2;
};

test('EA25: Giebelgaube-Fußabdruck = 5-Eck (Pentagon) mit erwarteten (u,v)-Punkten', () => {
  const a = (35 * Math.PI) / 180;
  const fp = fussabdruckUV(giebel(), a, 0, 0); // uRef=vRef=0
  assert.equal(fp.length, 5);
  const erwartet = [[-1.25, -1.525968], [1.25, -1.525968], [1.25, 1.089202], [0, 2.615170], [-1.25, 1.089202]];
  fp.forEach((p, i) => {
    assert.ok(Math.abs(p.x - erwartet[i][0]) < 1e-3, `x[${i}]=${p.x}`);
    assert.ok(Math.abs(p.y - erwartet[i][1]) < 1e-3, `y[${i}]=${p.y}`);
  });
});

test('EA25: Giebel-Fußabdruckfläche ≈ 8.445 m² (Pentagon, Shoelace) — größer als altes Rechteck', () => {
  const fp = fussabdruckUV(giebel(), (35 * Math.PI) / 180, 0, 0);
  assert.ok(Math.abs(shoelace(fp) - 8.4454) < 0.01, `area=${shoelace(fp)}`);
});

test('EA25 Regression Z-Fighting: Giebel-Footprint reicht bis v≈2.615 (alte EA23-Oberkante war ~1.35)', () => {
  const fp = fussabdruckUV(giebel(), (35 * Math.PI) / 180, 0, 0);
  const vMax = Math.max(...fp.map((p) => p.y));
  assert.ok(vMax > 2.5, `firstEnde v=${vMax} (deckt jetzt den hinteren Gaubenteil)`);
});

test('EA25: Pultgaube (Schlepp) -> 4-Eck, v-Extent = d/cos a ≈ 3.052 (nicht depth=2.5), Fläche ≈ 7.63', () => {
  const a = (35 * Math.PI) / 180;
  const fp = fussabdruckUV(schlepp(), a, 0, 0);
  assert.equal(fp.length, 4);
  const vExt = Math.max(...fp.map((p) => p.y)) - Math.min(...fp.map((p) => p.y));
  assert.ok(Math.abs(vExt - 2.5 / Math.cos(a)) < 1e-2, `vExt=${vExt}`);
  assert.ok(Math.abs(shoelace(fp) - 7.6298) < 0.02, `area=${shoelace(fp)}`);
});

test('EA25: Trapezgaube -> 4-Eck mit zwei verschiedenen u-Breiten (Front schmaler als hinten)', () => {
  const fp = fussabdruckUV(schlepp({ type: 'trapezgaube', width: 2.5 }), (35 * Math.PI) / 180, 0, 0);
  assert.equal(fp.length, 4);
  const frontBreite = Math.abs(fp[1].x - fp[0].x); // fBL,fBR
  const backBreite = Math.abs(fp[2].x - fp[3].x);  // bR,bL
  assert.ok(frontBreite < backBreite - 0.1, `front ${frontBreite} >= back ${backBreite}`);
});

test('EA25: Spitzgaube (hWall=0) -> 3-Eck (koinzidente Ecken dedupliziert)', () => {
  const fp = fussabdruckUV(giebel({ type: 'spitzgaube' }), (35 * Math.PI) / 180, 0, 0);
  assert.equal(fp.length, 3);
});

test('EA25: Footprint folgt uRef/vRef (Translation)', () => {
  const a = (35 * Math.PI) / 180;
  const fp0 = fussabdruckUV(giebel(), a, 0, 0);
  const fp1 = fussabdruckUV(giebel(), a, 5, 3);
  fp0.forEach((p, i) => { assert.ok(Math.abs(fp1[i].x - (p.x + 5)) < 1e-9 && Math.abs(fp1[i].y - (p.y + 3)) < 1e-9); });
});

test('EA25: flache Gaube/flaches Dach -> kein Polygon (Rückfall, [])', () => {
  assert.equal(fussabdruckUV(giebel({ pitch: 0.001 }), (35 * Math.PI) / 180, 0, 0).length, 0);
  assert.equal(fussabdruckUV(giebel(), 1e-9, 0, 0).length, 0);
});
