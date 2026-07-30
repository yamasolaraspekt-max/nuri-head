/**
 * AUF-88-P1 / K-04 — die Kalibrier-Rechnung, isoliert vom Renderer.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { abstand, berechneMassstab, MASSSTAB_STANDARD } from '../app/unterlage/kalibrierung';

test('abstand: Pythagoras, nichts weiter', () => {
  assert.equal(abstand({ x: 0, y: 0 }, { x: 3, y: 4 }), 5);
  assert.equal(abstand({ x: 10, y: 10 }, { x: 10, y: 10 }), 0);
});

test('K-04: eine bekannte Strecke ergibt den erwarteten Maßstab', () => {
  // 100 Szenen-mm gemessen, echte Länge 5000 mm ⇒ der alte Maßstab (1) wird ×50.
  const massstab = berechneMassstab(MASSSTAB_STANDARD, { x: 0, y: 0 }, { x: 100, y: 0 }, 5000);
  assert.equal(massstab, 50);
});

test('K-04 (Gegenprobe): die Strecke halbieren ⇒ der Maßstab MUSS sich verdoppeln', () => {
  // Wörtlich die Gegenprobe aus dem Auftragsblatt — eine Zusage, die nur einen Wert festhält,
  // ginge auch bei einer konstanten Rückgabe grün.
  const voll = berechneMassstab(MASSSTAB_STANDARD, { x: 0, y: 0 }, { x: 200, y: 0 }, 5000);
  const halb = berechneMassstab(MASSSTAB_STANDARD, { x: 0, y: 0 }, { x: 100, y: 0 }, 5000);
  assert.ok(voll !== null && halb !== null);
  assert.equal(halb, voll! * 2);
});

test('K-04: eine zweite Kalibrierung korrigiert den ZULETZT gültigen Maßstab, nicht den Standard', () => {
  // Erste Kalibrierung: ×50. Zweite Kalibrierung auf demselben (jetzt bereits skalierten) Bild
  // ergibt exakt denselben Endwert wie eine einzige Kalibrierung mit der wahren Strecke —
  // die Korrektur ist verlustfrei verkettbar.
  const ersteRunde = berechneMassstab(MASSSTAB_STANDARD, { x: 0, y: 0 }, { x: 100, y: 0 }, 5000)!;
  // Auf der jetzt ×50 skalierten Unterlage entspräche derselben realen Strecke ein gemessener
  // Abstand von 100 (unverändert, weil die Punkte in Szenen-mm bereits mitskaliert wären) — hier
  // wird stattdessen eine ABWEICHUNG simuliert (Nutzer hat ungenau geklickt, 90 statt 100):
  const zweiteRunde = berechneMassstab(ersteRunde, { x: 0, y: 0 }, { x: 90, y: 0 }, 5000)!;
  assert.equal(Math.round(zweiteRunde), Math.round(ersteRunde * (5000 / 90)));
});

test('K-04 (Kante): identische Punkte liefern null, keine Division durch 0', () => {
  assert.equal(berechneMassstab(MASSSTAB_STANDARD, { x: 5, y: 5 }, { x: 5, y: 5 }, 5000), null);
});

test('K-04 (Kante): eine Länge von 0 oder darunter liefert null', () => {
  assert.equal(berechneMassstab(MASSSTAB_STANDARD, { x: 0, y: 0 }, { x: 10, y: 0 }, 0), null);
  assert.equal(berechneMassstab(MASSSTAB_STANDARD, { x: 0, y: 0 }, { x: 10, y: 0 }, -5), null);
});

test('K-04 (Kante): ein alter Maßstab von 0 oder darunter liefert null', () => {
  assert.equal(berechneMassstab(0, { x: 0, y: 0 }, { x: 10, y: 0 }, 5000), null);
});
