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
  // **Nachgezogen in AUF-38 Scheibe 6.** Die Regel stand als Inline-Stil und steht jetzt als
  // `.hp-gf-board` in `hausplaner.css`. **Die Absicht ist unveraendert:** so viele Spalten, wie
  // passen — bei 390 px stapeln statt ueberlagern, sonst faengt das `aside` die Zeigerereignisse
  // ab und wird zur toten Schaltflaeche. Geprueft wird die **Eigenschaft dort, wo sie wohnt.**
  const beides = lies('GuidedView.tsx')
    + readFileSync(new URL('../hausplaner.css', import.meta.url), 'utf8');
  assert.doesNotMatch(beides, /1fr 320px/, 'die feste Spalte ist zurück');
  assert.match(beides, /repeat\(auto-fit, ?minmax\(280px, ?1fr\)\)/);
});

test('der Konfigurator ebenso — dieselbe Ursache, dieselbe Behebung', () => {
  // **Nachgezogen in AUF-38 Scheibe 5** — dieselbe Nachfuehrung wie bei der Startseite unten.
  // Die Regel stand als Inline-Stil in `ConfigWizard.tsx` und steht jetzt als `.hp-kw-koerper`
  // in `hausplaner.css`. **Die Absicht ist unveraendert:** so viele Spalten, wie passen, damit
  // bei 390 px gestapelt statt ueberlagert wird. Geprueft wird deshalb die **Eigenschaft**,
  // unabhaengig davon, wo sie wohnt — die alte Fassung las den Inline-Stil und waere gruen
  // geblieben, wenn die Regel ersatzlos verschwunden waere.
  const beides = lies('ConfigWizard.tsx')
    + readFileSync(new URL('../hausplaner.css', import.meta.url), 'utf8');
  assert.doesNotMatch(beides, /1fr 300px/, 'die feste zweite Spalte ist zurueck');
  assert.match(beides, /repeat\(auto-fit, ?minmax\(260px, ?1fr\)\)/);
});

test('die Startseite legt so viele Spalten an, wie passen — nicht drei um jeden Preis', () => {
  // **Nachgezogen in AUF-38 Scheibe 2:** die Regel stand als Inline-Stil in `StartView.tsx` und
  // steht jetzt als Klasse in `hausplaner.css`. **Die Absicht ist unveraendert** — so viele Spalten,
  // wie passen. Geprueft wird deshalb die Eigenschaft, unabhaengig davon, wo sie wohnt.
  const beides = lies('StartView.tsx')
    + readFileSync(new URL('../hausplaner.css', import.meta.url), 'utf8');
  assert.doesNotMatch(beides, /repeat\(3, ?1fr\)/, 'drei feste Spalten passen bei 390 px nicht');
  assert.match(beides, /repeat\(auto-fit, ?minmax\(230px, ?1fr\)\)/);
});

test('die Kopfzeile bricht um, statt zu schieben', () => {
  // **Nachgezogen in AUF-38 Scheibe 4:** die Kopfzeile trug ihren Stil inline, jetzt traegt sie die
  // Klasse `.hp-studio-kopf`. **Die Absicht ist unveraendert** — umbrechen statt schieben, und
  // Mindesthoehe statt fester Hoehe. Geprueft wird die Eigenschaft dort, wo sie heute wohnt.
  const css = readFileSync(new URL('../hausplaner.css', import.meta.url), 'utf8');
  const kopf = css.match(/\.hp-studio-kopf \{([^}]*)\}/);
  assert.ok(kopf, 'Kopfzeilen-Regel nicht gefunden');
  assert.match(kopf[1]!, /flex-wrap: wrap/, 'ohne Umbruch schiebt sie die ganze Seite über den Rand');
  assert.match(kopf[1]!, /min-height: 62px/, 'Mindesthöhe statt fester Höhe — sonst überlappt die zweite Zeile');
  assert.doesNotMatch(kopf[1]!, /[^-]height: 62px/, 'die feste Höhe war die halbe Ursache');
});

test('keine der vier Flächen trägt noch eine feste Spaltenbreite in der Grid-Vorlage', () => {
  // Der Regressionsschutz: eine neue `1fr 320px`-Zeile wäre derselbe Fehler an anderer Stelle.
  for (const datei of ['GuidedView.tsx', 'ConfigWizard.tsx', 'StartView.tsx', 'HausplanerStudio.tsx']) {
    const q = lies(datei);
    assert.doesNotMatch(q, /gridTemplateColumns: '[^']*\b\d{3}px'/, `${datei}: feste Spaltenbreite in der Vorlage`);
  }
});
