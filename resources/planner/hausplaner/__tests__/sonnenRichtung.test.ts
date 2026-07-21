/**
 * Render-Welle 1 — sonnenRichtung() (Spec SPEC-RENDER-WELLE-1, §3.2 + Kantenliste §5).
 * Reine Mathematik, kein WebGL: Fallback Süd/35°, 0°/360°-Identität, NaN-Sicherheit,
 * Schatten-folgt-northAngle-Vertrag (90°-Drehung dreht die Richtung mit).
 */
import { strict as assert } from 'node:assert';
import { test } from 'node:test';
import { sonnenRichtung } from '../renderers/three-d/szene';

const nah = (a: number, b: number, eps = 1e-12) => Math.abs(a - b) < eps;

test('Fallback ohne Werte: Süd (three +z) in 35° Höhe, normiert', () => {
  const r = sonnenRichtung();
  // Süd = Welt −y = three +z; Höhe 35° ⇒ y = sin(35°), z = cos(35°); kein Ost-/West-Anteil.
  assert.ok(nah(r.x, 0), `x muss 0 sein, ist ${r.x}`);
  assert.ok(nah(r.y, Math.sin((35 * Math.PI) / 180)), `y muss sin(35°) sein, ist ${r.y}`);
  assert.ok(nah(r.z, Math.cos((35 * Math.PI) / 180)), `z muss cos(35°) sein, ist ${r.z}`);
  assert.ok(nah(r.length(), 1), 'Richtung muss normiert sein');
});

test('northAngle 0 und 360 liefern die identische Richtung (Kante §5)', () => {
  const r0 = sonnenRichtung(0);
  const r360 = sonnenRichtung(360);
  assert.ok(nah(r0.x, r360.x) && nah(r0.y, r360.y) && nah(r0.z, r360.z));
});

test('Sonne folgt northAngle: 90°-Drehung dreht die Horizontal-Richtung um 90°, Höhe bleibt', () => {
  const r0 = sonnenRichtung(0);
  const r90 = sonnenRichtung(90);
  // Höhenanteil (three y) unverändert …
  assert.ok(nah(r0.y, r90.y));
  // … Horizontalanteil um 90° gedreht: aus Süd (0, +z) wird West (−x, 0).
  assert.ok(nah(r90.x, -Math.cos((35 * Math.PI) / 180)), `x muss −cos(35°) sein, ist ${r90.x}`);
  assert.ok(nah(r90.z, 0), `z muss 0 sein, ist ${r90.z}`);
  // Gegen-Beweis-Anker: r90 darf NICHT gleich r0 sein (eine verstümmelte Konstante fiele hier).
  assert.ok(!nah(r0.x, r90.x) || !nah(r0.z, r90.z), 'Richtung muss sich mit northAngle ändern');
});

test('NaN-/Unendlich-Eingaben fallen auf den Fallback zurück — nie NaN im Licht (Kante §5)', () => {
  for (const kaputt of [Number.NaN, Number.POSITIVE_INFINITY, Number.NEGATIVE_INFINITY]) {
    const r = sonnenRichtung(kaputt, { latitude: Number.NaN });
    assert.ok(Number.isFinite(r.x) && Number.isFinite(r.y) && Number.isFinite(r.z));
    const fallback = sonnenRichtung();
    assert.ok(nah(r.x, fallback.x) && nah(r.y, fallback.y) && nah(r.z, fallback.z));
  }
});

test('geolocation verfeinert nur die Höhe: höhere Breite ⇒ flachere Sonne, geklemmt 10–75°', () => {
  const nordkap = sonnenRichtung(0, { latitude: 71 });   // 90−71=19°
  const aequator = sonnenRichtung(0, { latitude: 0 });   // 90−0=90 ⇒ Klemme 75°
  const de = sonnenRichtung(0, { latitude: 50 });        // 90−50=40°
  assert.ok(nah(nordkap.y, Math.sin((19 * Math.PI) / 180)));
  assert.ok(nah(aequator.y, Math.sin((75 * Math.PI) / 180)), 'Obergrenze 75° muss klemmen');
  assert.ok(nah(de.y, Math.sin((40 * Math.PI) / 180)));
  // Azimut bleibt Süd in allen Fällen.
  assert.ok(nah(nordkap.x, 0) && nah(aequator.x, 0) && nah(de.x, 0));
});
