/**
 * AUF-83-T1a — **die Breite lernt, was die Höhe schon kann.**
 *
 * Der Aufbau folgt `buehnenHoehe.test.ts`: erst die Kanten der reinen Rechnung, dann die Regel,
 * die sich das Modul selbst gibt — **keine Pixelkonstante für eine Schiene** —, geprüft am
 * Quelltext. Dieselbe Form, weil es dieselbe Lösung auf der anderen Achse ist.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  buehnenBreite, freieBreite, ERSATZ_BREITE, MIN_BREITE, SCHIENEN_MERKMAL,
} from '../app/dashboard/buehnenBreite';

const hier = dirname(fileURLToPath(import.meta.url));
const modul = readFileSync(join(hier, '../app/dashboard/buehnenBreite.ts'), 'utf8');
const app = readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8');

// --- K-01: die Fensterkonstante ist fort ---------------------------------------------------------

test('K-01: die Breitenrechnung nennt kein `innerWidth` mehr', () => {
  // **Die Wirkung, nicht die Gestalt:** nicht „die neue Zeile existiert", sondern „die alte
  // Rechnung gibt es nicht mehr". Eine Zusage, die nur das Neue prüft, bleibt grün, wenn das Alte
  // danebenstehen bleibt.
  const codeZeilen = app.split('\n').filter((z) => !z.trim().startsWith('*') && !z.trim().startsWith('//'));
  const rechnung = codeZeilen.filter((z) => z.includes('innerWidth') && z.includes('const'));
  assert.deepEqual(rechnung, [], `die Fensterrechnung steht noch:\n${rechnung.join('\n')}`);
});

test('K-01 (presence-Partner): die Bühne bezieht ihre Breite aus dem Modul', () => {
  // Ohne diesen Partner wäre die Zusage oben auch grün, wenn jemand die Zeile ersatzlos löscht.
  assert.match(app, /const breite = buehnenBreite\(gemesseneBreite\)/);
  assert.match(app, /useGemesseneBreite\(inhaltRef\)/, 'gemessen wird die Inhaltsreihe');
});

// --- K-02: dasselbe Muster wie die Höhe ----------------------------------------------------------

test('K-02: das Modul trägt KEINE Pixelkonstante für eine Schiene', () => {
  // **Die Regel, die `buehnenHoehe.ts` sich selbst gibt** — hier wörtlich übernommen. Stünde eine
  // Schienenbreite im Modul, wäre die alte Konstante nur umgezogen.
  const ohneKommentare = modul.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
  for (const zahl of ['220', '268', '488']) {
    assert.doesNotMatch(ohneKommentare, new RegExp(`\\b${zahl}\\b`),
      `${zahl} steht im Modul — das ist die alte Konstante an neuem Ort`);
  }
});

test('K-02: die Schienen melden sich selbst, statt gezählt zu werden', () => {
  assert.equal(SCHIENEN_MERKMAL, 'data-schiene');
  const treffer = [...app.matchAll(/data-schiene/g)];
  assert.ok(treffer.length >= 2, `nur ${treffer.length} Schiene(n) markiert — links und rechts erwartet`);
});

test('K-02: ohne Messung gilt die Ersatzbreite, nicht die Null', () => {
  // Die Kante aus `buehnenHoehe`: beim ersten Rendern ist gemessen 0. Eine Bühne mit Breite 0 ist
  // ein leerer Bildschirm.
  assert.equal(buehnenBreite(null), ERSATZ_BREITE);
  assert.equal(buehnenBreite(0), ERSATZ_BREITE);
  assert.equal(buehnenBreite(-5), ERSATZ_BREITE);
});

test('K-02: eine echte Messung gilt — nur unter der Mindestbreite wird angehoben', () => {
  assert.equal(buehnenBreite(900), 900);
  assert.equal(buehnenBreite(MIN_BREITE), MIN_BREITE);
  assert.equal(buehnenBreite(MIN_BREITE - 1), MIN_BREITE, 'zu schmal wird angehoben, nicht durchgereicht');
});

test('K-02: die Ersatzbreite ist die alte Rechnung ohne Fenster — nichts verschiebt sich', () => {
  assert.equal(ERSATZ_BREITE, 1200 - 220 - 268, 'sonst wandern bestehende Testwerte');
});

// --- freieBreite: die Rechnung selbst ------------------------------------------------------------

test('freieBreite zieht ab, was die Schienen wirklich einnehmen', () => {
  assert.equal(freieBreite(1440, [220, 268]), 952);
  assert.equal(freieBreite(900, [220, 268]), 412, 'ein schmalerer Behälter ergibt eine schmalere Bühne');
});

test('freieBreite: eine zugeklappte Schiene gibt ihren Platz frei', () => {
  // **Der Fall, für den dieses Modul gebaut ist.** Klappt das rechte Panel zu, ändert sich das
  // Fenster nicht — die alte Rechnung hätte es nie bemerkt.
  assert.equal(freieBreite(1440, [220, 0]), 1220);
  assert.equal(freieBreite(1440, []), 1440, 'ohne Schienen gehört die ganze Reihe der Bühne');
});

test('freieBreite rundet ab und wird nie negativ', () => {
  assert.equal(freieBreite(1000.9, [220.4]), 780, 'ein aufgerundetes Pixel steht rechts wieder heraus');
  assert.equal(freieBreite(300, [220, 268]), 0, 'kein negativer Wert, auch wenn die Schienen breiter sind');
});
