/**
 * Heizkörper-Leistungsumrechnung: Norm → Betrieb, Deckung.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { betriebsLeistung, benoetigteNormleistung, bewerteDeckung, uebertemperatur } from '../geometry/heizkoerperLeistung';

const std = { vorlauf: 75, ruecklauf: 65, raumtemp: 20 }; // Δθ = 50 K = Normbedingung

test('bei Normbedingung (Δθ=50) bleibt die Leistung gleich', () => {
  assert.equal(uebertemperatur(std), 50);
  assert.equal(betriebsLeistung(1000, std), 1000);
});

test('niedrigere Spreizung (Wärmepumpe 40/33/20) reduziert die Leistung deutlich', () => {
  const wp = { vorlauf: 40, ruecklauf: 33, raumtemp: 20 }; // Δθ = 16,5 K
  const q = betriebsLeistung(1000, wp);
  assert.ok(q < 1000 && q > 0, `q=${q}`);
  // grob: (16.5/50)^1.3 ≈ 0.235 → ~235 W
  assert.ok(Math.abs(q - 235) < 20, `q=${q}`);
});

test('Vorlauf ≤ Raumtemperatur → keine Leistung', () => {
  assert.equal(betriebsLeistung(1000, { vorlauf: 20, ruecklauf: 18, raumtemp: 22 }), 0);
});

test('benoetigteNormleistung ist Umkehrung von betriebsLeistung', () => {
  const wp = { vorlauf: 45, ruecklauf: 38, raumtemp: 21 };
  const q = betriebsLeistung(1500, wp);
  const norm = benoetigteNormleistung(q, wp);
  assert.ok(Math.abs(norm - 1500) < 2, `norm=${norm}`);
});

test('Deckung: ausreichend vs Unterdeckung', () => {
  const ok = bewerteDeckung(1200, 1000, std);
  assert.equal(ok.ausreichend, true);
  assert.ok(ok.deckungsgrad >= 100);
  const unter = bewerteDeckung(800, 1000, std);
  assert.equal(unter.ausreichend, false);
  assert.match(unter.hinweis, /Unterdeckung/);
});
