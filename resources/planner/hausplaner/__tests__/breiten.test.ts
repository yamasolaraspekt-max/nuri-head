/**
 * AUF-46 — die vier Pflichtbreiten 390 · 768 · 1024 · 1440.
 *
 * **Gemessen war:** Bei 375/390 px lief die Seite um 283 px über (`scrollWidth 656` bei 390), 47
 * Elemente ragten über den rechten Rand. Ursache waren **feste Breiten**, die nicht ausweichen
 * konnten: eine Kopfzeile ohne Umbruch, `repeat(3, 1fr)` auf der Startseite und — der harte Fall —
 * `gridTemplateColumns: '1fr 320px'` in der geführten Planung. Dort legte sich das Aufgaben-`aside`
 * über den Inhalt und **fing die Zeigerereignisse ab**: eine sichtbare, aber tote Schaltfläche.
 *
 * **Warum Quelltext-Prüfungen:** Breiten entstehen im Browser, und die Testumgebung hat keinen.
 * Geprüft wird deshalb die **Ursache** — die feste Breite, die es nicht mehr geben darf. Die
 * Wirkung steht als Messung im Bericht (`scrollWidth` je Breite, vorher/nachher).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const lies = (p: string): string => ohneKommentare(readFileSync(join(hier, '../app/', p), 'utf8'));

test('die geführte Planung hat KEINE feste zweite Spalte mehr — das war die tote Schaltfläche', () => {
  const guided = lies('GuidedView.tsx');
  assert.doesNotMatch(guided, /gridTemplateColumns: '1fr 320px'/, 'die feste Spalte ist zurück');
  assert.match(guided, /gridTemplateColumns: 'repeat\(auto-fit, minmax\(280px, 1fr\)\)'/);
});

test('der Konfigurator ebenso — dieselbe Ursache, dieselbe Behebung', () => {
  const wizard = lies('ConfigWizard.tsx');
  assert.doesNotMatch(wizard, /gridTemplateColumns: '1fr 300px'/);
  assert.match(wizard, /gridTemplateColumns: 'repeat\(auto-fit, minmax\(260px, 1fr\)\)'/);
});

test('die Startseite legt so viele Spalten an, wie passen — nicht drei um jeden Preis', () => {
  const start = lies('StartView.tsx');
  assert.doesNotMatch(start, /gridTemplateColumns: 'repeat\(3, 1fr\)'/, 'drei feste Spalten passen bei 390 px nicht');
  assert.match(start, /gridTemplateColumns: 'repeat\(auto-fit, minmax\(230px, 1fr\)\)'/);
});

test('die Kopfzeile bricht um, statt zu schieben', () => {
  const studio = lies('HausplanerStudio.tsx');
  const kopf = studio.match(/<header style=\{\{[^}]*\}\}>/);
  assert.ok(kopf, 'Kopfzeile nicht gefunden');
  assert.match(kopf[0], /flexWrap: 'wrap'/, 'ohne Umbruch schiebt sie die ganze Seite über den Rand');
  assert.match(kopf[0], /minHeight: 62/, 'Mindesthöhe statt fester Höhe — sonst überlappt die zweite Zeile');
  assert.doesNotMatch(kopf[0], /height: 62,/, 'die feste Höhe war die halbe Ursache');
});

test('keine der vier Flächen trägt noch eine feste Spaltenbreite in der Grid-Vorlage', () => {
  // Der Regressionsschutz: eine neue `1fr 320px`-Zeile wäre derselbe Fehler an anderer Stelle.
  for (const datei of ['GuidedView.tsx', 'ConfigWizard.tsx', 'StartView.tsx', 'HausplanerStudio.tsx']) {
    const q = lies(datei);
    assert.doesNotMatch(q, /gridTemplateColumns: '[^']*\b\d{3}px'/, `${datei}: feste Spaltenbreite in der Vorlage`);
  }
});
