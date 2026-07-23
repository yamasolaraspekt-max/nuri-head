/**
 * EA28 — ABNAHME-GATE für die Schiftsparren-Klassifikation + -Stückliste. Spiegelt die
 * Sparren-Schleife aus DachplanerProPage.buildRoofFace (getLineIntersections + 0.05-Clip-Filter)
 * und prüft die Fachregeln (Zimmermann-/Statik- + Dachdecker-Meister-Agent):
 *  - Rechteck-Sattel/Pult → 0 Schifter (Nullfall, verbindlich).
 *  - Kehle (L/T/U-Notch) → Kehlschifter, unten angeschnitten, Länge < vMax, lineare Staffelung.
 *  - Grat (Walm-Dreieck) → Gratschifter, oben angeschnitten, arithmetische Staffelung.
 *  - Σ Schifter-lfm < Σ Sparren-lfm (Anti-Doppelzählung), Spiegelsymmetrie, keine NaN.
 *
 * Rein (KEIN three/React). KEINE Statik/Eindeckung/Querschnitt — nur Lage/Länge/Anzahl.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  klassifiziereSchifter, schifterAusFlaeche, schifterMengen, schifterMengenAusListe,
  type Punkt2D, type SchifterSparren,
} from '../geometry/schifterListe';
import { mainSDoppelNotchPoly, uFormKonstanten, type UFormEingabe } from '../geometry/dachUForm';

const U: UFormEingabe = { length: 10, width: 8, widthB: 4, lengthB: 4, overhang: 0.5, overhangGable: 0.3, pitchGrad: 35, height: 5, rafterHeight: 18 };
const rechteck = (uMax: number, vMax: number): Punkt2D[] => [{ x: 0, y: 0 }, { x: uMax, y: 0 }, { x: uMax, y: vMax }, { x: 0, y: vMax }];

// ── 1) Einzel-Klassifikation ────────────────────────────────────────────────────────────────────
test('EA28: klassifiziereSchifter — voll / kehle / grat / beidseitig, ungültig→voll (nie NaN-Schifter)', () => {
  assert.equal(klassifiziereSchifter(0, 5, 5), 'voll');
  assert.equal(klassifiziereSchifter(0.5, 5, 5), 'kehle');   // unten angeschnitten
  assert.equal(klassifiziereSchifter(0, 4.5, 5), 'grat');    // oben angeschnitten
  assert.equal(klassifiziereSchifter(0.5, 4.5, 5), 'beidseitig');
  assert.equal(klassifiziereSchifter(NaN, 5, 5), 'voll');
  assert.equal(klassifiziereSchifter(3, 3, 5), 'voll');      // Nulllänge → kein Schifter
});

// ── 2) Nullfall: Rechteck-Satteldachfläche hat KEINE Schifter (Fachregel verbindlich) ────────────
test('EA28: Rechteckfläche (Sattel/Pult) → 0 Schifter, alle Sparren voll', () => {
  const liste = schifterAusFlaeche(rechteck(10, 5.5), 10, 5.5, 0.7, 0.08);
  assert.ok(liste.length > 5, 'es werden überhaupt Sparren erzeugt');
  assert.ok(liste.every((s) => s.art === 'voll'), 'kein Sparren ist Schifter');
  assert.equal(schifterMengen(liste).anzahl, 0);
});

// ── 3) Kehle: U/L/T-Notch erzeugt Kehlschifter (unten angeschnitten) ─────────────────────────────
test('EA28: U-Notch (main_S) → Kehlschifter; alle vStart>0, Länge<vMax, art=kehle', () => {
  const k = uFormKonstanten(U);
  const poly = mainSDoppelNotchPoly(U);
  const liste = schifterAusFlaeche(poly, k.uMaxMain, k.vMaxMain, 0.7, 0.08);
  const schifter = liste.filter((s) => s.art !== 'voll');
  assert.ok(schifter.length > 0, 'Kehle erzeugt Schifter');
  for (const s of schifter) {
    assert.equal(s.art, 'kehle', `unten angeschnitten erwartet, ist ${s.art} (vStart=${s.vStart})`);
    assert.ok(s.vStart > 0.05, `vStart=${s.vStart} > 0`);
    assert.ok(s.laenge3D > 0 && s.laenge3D < k.vMaxMain, `0 < ${s.laenge3D} < ${k.vMaxMain}`);
  }
});

test('EA28: U-Notch spiegelsymmetrisch — Schifterlängen links == rechts (gleiche Multimenge)', () => {
  const k = uFormKonstanten(U);
  const liste = schifterAusFlaeche(mainSDoppelNotchPoly(U), k.uMaxMain, k.vMaxMain, 0.7, 0.08).filter((s) => s.art !== 'voll');
  const mitte = k.uMaxMain / 2;
  const links = liste.filter((s) => s.u < mitte).map((s) => +s.laenge3D.toFixed(3)).sort((a, b) => a - b);
  const rechts = liste.filter((s) => s.u > mitte).map((s) => +s.laenge3D.toFixed(3)).sort((a, b) => a - b);
  assert.equal(links.length, rechts.length);
  for (let i = 0; i < links.length; i++) assert.ok(Math.abs(links[i] - rechts[i]) < 1e-2, `${links[i]} ≈ ${rechts[i]}`);
});

// ── 4) Grat: Walm-Dreieck erzeugt Gratschifter mit arithmetischer Staffelung ─────────────────────
test('EA28: Walm-Dreieck → Gratschifter (oben angeschnitten), Länge linear gestaffelt (Δ konstant)', () => {
  // Rechtwinkliges Halb-Walm-Dreieck: Traufe v=0 (u 0..6), Hypotenuse (Grat) (6,0)->(0,6).
  const tri: Punkt2D[] = [{ x: 0, y: 0 }, { x: 6, y: 0 }, { x: 0, y: 6 }];
  const liste = schifterAusFlaeche(tri, 6, 6, 0.6, 0.08);
  const grat = liste.filter((s) => s.art !== 'voll').sort((a, b) => a.u - b.u);
  assert.ok(grat.length >= 4, `genug Gratschifter (${grat.length})`);
  for (const s of grat) {
    assert.equal(s.art, 'grat', `oben angeschnitten erwartet (vEnd=${s.vEnd})`);
    assert.ok(s.vEnd < 6 - 0.05 && s.laenge3D < 6, `vEnd=${s.vEnd} < vMax`);
  }
  // Länge = 6 − u → arithmetisch: zweite Differenz ≈ 0.
  for (let i = 2; i < grat.length; i++) {
    const d2 = (grat[i].laenge3D - grat[i - 1].laenge3D) - (grat[i - 1].laenge3D - grat[i - 2].laenge3D);
    assert.ok(Math.abs(d2) < 1e-6, `arithmetische Staffelung verletzt @${i}: ${d2}`);
  }
});

// ── 5) Aggregation + Anti-Doppelzählung ──────────────────────────────────────────────────────────
test('EA28: schifterMengen — Grat/Kehle-Split, min≤max, Σ konsistent; leere Liste → Nullen', () => {
  const liste: SchifterSparren[] = [
    { u: 1, vStart: 0.5, vEnd: 5, laenge3D: 4.5, art: 'kehle' },
    { u: 2, vStart: 1.0, vEnd: 5, laenge3D: 4.0, art: 'kehle' },
    { u: 3, vStart: 0, vEnd: 4.0, laenge3D: 4.0, art: 'grat' },
    { u: 4, vStart: 0, vEnd: 5, laenge3D: 5, art: 'voll' }, // wird ignoriert
  ];
  const m = schifterMengen(liste);
  assert.equal(m.anzahl, 3);
  assert.ok(Math.abs(m.laengeGesamt - 12.5) < 1e-9);
  assert.equal(m.kehleAnzahl, 2); assert.ok(Math.abs(m.kehleLaenge - 8.5) < 1e-9);
  assert.equal(m.gratAnzahl, 1); assert.ok(Math.abs(m.gratLaenge - 4.0) < 1e-9);
  assert.ok(m.laengeMin <= m.laengeMax && m.laengeMin === 4.0 && m.laengeMax === 4.5);
  assert.equal(schifterMengen([]).anzahl, 0);
  assert.equal(schifterMengen(null).laengeGesamt, 0);
});

test('EA28: schifterMengenAusListe — nur type=sparren&istSchifter; Σ Schifter-lfm < Σ aller Sparren-lfm', () => {
  const holz = [
    { type: 'sparren', name: 'Sparren main_S', laenge: 5.5, istSchifter: false, schifter: 'voll' },
    { type: 'sparren', name: 'Schiftsparren Kehle main_S', laenge: 4.0, istSchifter: true, schifter: 'kehle' },
    { type: 'sparren', name: 'Schiftsparren Kehle main_S', laenge: 3.0, istSchifter: true, schifter: 'kehle' },
    { type: 'gratsparren', name: 'Gratsparren', laenge: 6.0 },     // anderes Bauteil → ignorieren
    { type: 'latte', name: 'Traglatte', laenge: 9.0, istSchifter: true }, // kein Sparren → ignorieren
  ];
  const m = schifterMengenAusListe(holz);
  assert.equal(m.anzahl, 2);
  assert.ok(Math.abs(m.laengeGesamt - 7.0) < 1e-9);
  const sparrenGesamt = holz.filter((h) => h.type === 'sparren').reduce((a, h) => a + h.laenge, 0);
  assert.ok(m.laengeGesamt < sparrenGesamt, `Schifter ${m.laengeGesamt} < Sparren ${sparrenGesamt} (Teilmenge)`);
  assert.equal(schifterMengenAusListe(null).anzahl, 0);
});

// ── 6) Robustheit ────────────────────────────────────────────────────────────────────────────────
test('EA28: ungültige Flächen → leere Liste, keine NaN', () => {
  assert.equal(schifterAusFlaeche([], 10, 5, 0.7, 0.08).length, 0);
  assert.equal(schifterAusFlaeche(rechteck(10, 5), 0, 5, 0.7, 0.08).length, 0);
  assert.equal(schifterAusFlaeche(rechteck(10, 5), 10, 5, 0, 0.08).length, 0);
  for (const s of schifterAusFlaeche(mainSDoppelNotchPoly(U), uFormKonstanten(U).uMaxMain, uFormKonstanten(U).vMaxMain, 0.5, 0.08)) {
    assert.ok(Number.isFinite(s.vStart) && Number.isFinite(s.vEnd) && Number.isFinite(s.laenge3D));
  }
});
