/**
 * AUF-72 — die Bühnenhöhe.
 *
 * **Zweimal unabhängig gemessen, ein Befund:** Die Bühne ragte 227 px (1440 × 900) bzw. 273 px
 * (1440 × 813) **unter das Fenster**, und der Rest war nicht wegzuscrollen — 28 bis 38 % der
 * Zeichenfläche waren unerreichbar.
 *
 * **Die Ursache war eine Zahl, die einmal gestimmt hat:** die Fensterhöhe minus einer festen 96.
 * Die 96 war richtig, als **eine** Leiste über der Bühne stand. Dann kamen drei dazu (AUF-34,
 * AUF-68, AUF-70) — jede hat die Konstante still verstellt, und keine hätte es merken können.
 *
 * **Deshalb prüft dieser Test vor allem eines:** dass die Zahl **weg** ist und keine neue an ihre
 * Stelle getreten ist. Eine Messung, die man wieder durch eine Schätzung ersetzen kann, hat das
 * Problem nicht gelöst, sondern nur verschoben.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { buehnenHoehe, sichtbareHoehe, ERSATZ_HOEHE, MIN_HOEHE } from '../app/dashboard/buehnenHoehe';
import { standardPan, panAus } from '../app/dashboard/pan';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const quelle = (readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8')
  // AUF-48 Scheibe 4a: der Kopfrahmen (Werkzeugzeile, Arbeitsbereich-Waehler,
  // Bedien-Werkzeugleiste) ist nach `dashboard/Kopfrahmen.tsx` ausgezogen. **Beide Dateien
  // werden gelesen** — die geprueften Eigenschaften sind unveraendert, und eine Absenz-Zusage
  // darf nicht dadurch gruen werden, dass Inhalt eine Datei weiter gewandert ist.
  + readFileSync(join(hier, '../app/dashboard/Kopfrahmen.tsx'), 'utf8'));
const regel = readFileSync(join(hier, '../app/dashboard/buehnenHoehe.ts'), 'utf8');

// --- K3: die Konstante ist weg -----------------------------------------------------------------
test('K3: die feste Subtraktion ist restlos verschwunden — auch aus den Kommentaren', () => {
  // Bewusst zusammengesetzt: stünde die Marke hier als Literal, fände `grep` sie in dieser Datei.
  const marke = 'innerHeight' + ' - 96';
  assert.equal((quelle.match(new RegExp(marke, 'g')) ?? []).length, 0);
  // `window.innerHeight` kommt für die Höhe überhaupt nicht mehr vor.
  assert.doesNotMatch(ohneKommentare(quelle), /window\.innerHeight/);
});

test('K3: keine andere feste Zahl ist an ihre Stelle getreten', () => {
  const q = ohneKommentare(quelle);
  const zeile = q.match(/const hoehe = [^;]*;/);
  assert.ok(zeile, '`hoehe` nicht gefunden');
  assert.match(zeile[0], /buehnenHoehe\(gemesseneHoehe\)/, 'die Höhe kommt aus der Messung');
  assert.doesNotMatch(zeile[0], /\d{2,}/, 'eine Zahl in dieser Zeile wäre die nächste Konstante');
  // Und die Messung hängt wirklich am tragenden Element.
  assert.match(q, /const gemesseneHoehe = useGemesseneHoehe\(inhaltRef\);/);
  // AUF-83-T5: `position: 'relative'` ist dazugekommen — die Reihe trägt jetzt den Anker für eine
  // Schiene, die bei schmalem Fenster als Overlay darüber liegt (K-05). Dieselben drei Werte wie
  // vorher, an derselben Stelle, nur nicht mehr die einzigen drei.
  assert.match(q, /<div ref=\{inhaltRef\} style=\{\{ flex: 1, position: 'relative', overflow: 'hidden', display: 'flex' \}\}>/);
});

// --- K9: die Kante beim ersten Rendern ----------------------------------------------------------
test('K9: eine gemessene Höhe von 0 führt NICHT zur leeren Bühne', () => {
  // Beim ersten Rendern steht das Element noch nicht — die Messung liefert 0. Eine Bühne mit
  // Höhe 0 wäre ein leerer Bildschirm.
  assert.equal(buehnenHoehe(0), ERSATZ_HOEHE);
  assert.equal(buehnenHoehe(null), ERSATZ_HOEHE, 'noch nicht gemessen ist etwas anderes als 0 gemessen');
  assert.equal(buehnenHoehe(-50), ERSATZ_HOEHE, 'eine negative Höhe gibt es nicht');
  assert.equal(ERSATZ_HOEHE, 700, 'unverändert — der Testlauf ohne Fenster rechnet weiter damit');
});

test('K5: eine echte Messung gilt — aber nie unter dem benannten Mindestwert', () => {
  assert.equal(buehnenHoehe(578), 578, 'die Messung ist die Wahrheit über den vorhandenen Platz');
  assert.equal(buehnenHoehe(445), 445);
  assert.equal(buehnenHoehe(120), MIN_HOEHE, 'aus einem winzigen Fenster wird kein unbenutzbarer Streifen');
  assert.equal(MIN_HOEHE, 200, 'ein benannter Wert, kein Zufall');
  // In allen Fällen: größer als null.
  for (const m of [null, 0, -1, 1, 120, 445, 900]) {
    assert.ok(buehnenHoehe(m) >= MIN_HOEHE, `${m} ⇒ ${buehnenHoehe(m)}`);
  }
});

// --- AUF-73: der sichtbare Teil, nicht der beanspruchte -----------------------------------------
test('AUF-73 K3: der beanspruchte Platz wird auf das begrenzt, was man sieht', () => {
  // Der Regelfall: die Reihe passt ins Fenster — dann gilt sie unverändert.
  assert.equal(sichtbareHoehe(323, 462, 813), 462);
  // Der gemessene Fehlerfall (Studio-Blatt): sie beansprucht mehr, als unter ihr Platz ist.
  assert.equal(sichtbareHoehe(359, 462, 813), 454, '813 − 359 = 454; die 8 px Überstand entfallen');
  assert.equal(sichtbareHoehe(359, 549, 900), 541);
});

test('AUF-73: abgerundet, nicht gerundet — ein aufgerundetes Pixel steht unten wieder heraus', () => {
  assert.equal(sichtbareHoehe(359.6, 462, 813), 453, 'gerundet wären es 454 und damit 1 px zu viel');
  assert.equal(sichtbareHoehe(0, 462.9, 813), 462);
});

test('AUF-73: eine gescrollte oder abwesende Oberkante bringt die Rechnung nicht durcheinander', () => {
  // Negative Oberkante heißt „nach oben aus dem Bild gescrollt" — dann ist das ganze Fenster
  // verfügbar, nicht mehr.
  assert.equal(sichtbareHoehe(-100, 900, 813), 813);
  // Liegt das Element ganz unterhalb des Fensters, bleibt nichts sichtbar — und das ist kein
  // negativer Wert, sondern null. `buehnenHoehe` fängt es danach mit der Ersatzhöhe ab.
  assert.equal(sichtbareHoehe(900, 400, 813), 0);
  assert.equal(buehnenHoehe(sichtbareHoehe(900, 400, 813)), ERSATZ_HOEHE);
});

test('AUF-73 K6: keine feste Zahl zur Höhenkorrektur', () => {
  // Wer einen festen Betrag abzöge, hätte die alte Konstante nur durch eine kleinere ersetzt.
  const fn = regel.match(/export function sichtbareHoehe[\s\S]*?\n\}/);
  assert.ok(fn, '`sichtbareHoehe` nicht gefunden');
  assert.doesNotMatch(fn[0], /[-+]\s*\d{2,}/, 'eine Pixelkonstante in dieser Rechnung wäre der alte Fehler');
  assert.match(fn[0], /fenster - Math\.max\(0, oben\)/, 'gerechnet wird aus Gemessenem');
});

test('AUF-73: der Hook benutzt die reine Rechnung — die Messstelle bleibt dieselbe', () => {
  assert.match(regel, /sichtbareHoehe\(r\.top, r\.height, window\.innerHeight\)/);
  // Es bleibt bei EINER Messstelle; AUF-72s Beobachter ist unverändert.
  assert.equal((regel.match(/getBoundingClientRect\(\)/g) ?? []).length, 1);
});

// --- K6/K7: der Verschub ------------------------------------------------------------------------
test('K6: der Verschub des Nutzers überlebt eine Höhenänderung — unverändert', () => {
  // Das ist das Kriterium, an dem ein „einfach neu berechnen" auffliegt: wer die Lage bei jeder
  // Messung neu setzt, wirft die Arbeit des Nutzers weg.
  const eigener = { x: 315, y: 402 };
  assert.deepEqual(panAus(eigener, 700), eigener);
  assert.deepEqual(panAus(eigener, 445), eigener, 'die Höhe hat sich geändert, der Verschub nicht');
  assert.deepEqual(panAus(eigener, 1200), eigener);
});

test('K7: ohne eigenen Verschub folgt die Standardlage der Höhe', () => {
  assert.deepEqual(panAus(null, 700), standardPan(700));
  assert.deepEqual(panAus(null, 445), standardPan(445));
  assert.notDeepEqual(standardPan(700), standardPan(445), 'sonst folgte sie nicht');
  // `pan.ts` beschreibt genau das als Grund für den Startwert `null` — hier wird es festgehalten.
  assert.equal(standardPan(445).y, 445 - 80);
});

// --- K10-Vorbereitung / die Regel selbst --------------------------------------------------------
test('die Messung reagiert auf das ELEMENT, nicht nur auf das Fenster', () => {
  // Erscheint eine Zeile über der Bühne, ändert sich das Fenster nicht. Ein reiner
  // `resize`-Zuhörer bemerkt genau den Fall nicht, der den Fehler erzeugt hat.
  assert.match(regel, /new ResizeObserver\(messen\)/);
  assert.match(regel, /beobachter\.observe\(knoten\)/);
  assert.match(regel, /window\.addEventListener\('resize', messen\)/, 'zusätzlich, nicht stattdessen');
  assert.match(regel, /beobachter\.disconnect\(\)/, 'ohne Abmelden bliebe der Beobachter am toten Knoten');
  assert.match(regel, /removeEventListener\('resize', messen\)/);
});

test('kein Flackern: ein unveränderter Wert löst kein Rendern aus', () => {
  // Messen ⇒ Zustand ⇒ Layout ⇒ Messen wäre die Schleife. Sie kann nicht entstehen, weil der
  // Zustand nur bei echter Änderung gesetzt wird — und weil die gemessene Reihe `overflow: hidden`
  // trägt, also von der Bühne in ihr nicht wächst.
  assert.match(regel, /setHoehe\(\(alt\) => \(alt === h \? alt : h\)\)/);
  assert.match(ohneKommentare(quelle), /overflow: 'hidden', display: 'flex' \}\}>/);
});

test('die Regel kennt weder Store noch Szene — sie misst nur', () => {
  for (const verboten of ['useHausplanerStore', 'scene', 'executeCommand', 'zoom']) {
    assert.ok(!ohneKommentare(regel).includes(verboten), `${verboten} gehört nicht in ein Maßband`);
  }
});
