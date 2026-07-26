/**
 * AUF-63 — **was der DOM-Testlauf ist und was er nicht ist.**
 *
 * Dieser Test läuft im **schnellen** Lauf (ohne DOM) und prüft die Rahmenbedingungen des zweiten:
 * dass jsdom eine Entwicklungs-Abhängigkeit bleibt, nicht im Bündel landet, und dass die
 * Geometrie-Grenze im Bootstrap wirklich verdrahtet ist.
 *
 * **Warum hier und nicht im DOM-Lauf:** eine Zusicherung über den DOM-Lauf, die nur im DOM-Lauf
 * gilt, fällt mit ihm zusammen aus. Diese hier greift auch dann.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const wurzel = join(hier, '../../../..');
const paket = JSON.parse(readFileSync(join(wurzel, 'package.json'), 'utf8')) as {
  dependencies?: Record<string, string>;
  devDependencies?: Record<string, string>;
  scripts: Record<string, string>;
};

test('K3: jsdom ist devDependency — nicht Laufzeit-Abhaengigkeit', () => {
  assert.ok(paket.devDependencies?.jsdom, 'steht unter devDependencies');
  assert.ok(!paket.dependencies?.jsdom, 'und NICHT unter dependencies');
});

test('K3: jsdom kommt im ausgelieferten Buendel nicht vor', () => {
  // Eine Test-Abhaengigkeit, die im Buendel landet, waere 25 MB, die jeder Kunde laedt.
  const buendel = readFileSync(join(wurzel, 'public/hausplaner/hausplaner.js'), 'utf8');
  const nadel = 'js' + 'dom';
  assert.ok(!buendel.includes(nadel), 'das Buendel kennt es nicht');
});

test('zwei Testlaeufe, nicht ein umgebauter — der schnelle bleibt ohne DOM', () => {
  // Ein DOM fuer alle 125 Dateien zu stellen macht 125 Dateien langsamer, damit ein Dutzend etwas
  // pruefen kann.
  assert.ok(paket.scripts['test:hausplaner'], 'der schnelle Lauf existiert weiter');
  assert.ok(!paket.scripts['test:hausplaner']!.includes('dom-register'), 'und bleibt ohne DOM');
  assert.match(paket.scripts['test:hausplaner:dom']!, /dom-register\.mjs/);
  assert.match(paket.scripts['test:hausplaner:dom']!, /__domtests__/, 'eigenes Verzeichnis');
});

test('K4: die Geometrie-Grenze ist im Bootstrap verdrahtet — alle sechs Zugaenge', () => {
  const roh = readFileSync(join(hier, '../dom-register.mjs'), 'utf8');
  for (const zugang of ['getBoundingClientRect', 'getClientRects', 'offsetWidth', 'offsetHeight',
    'scrollWidth', 'scrollHeight', 'clientWidth', 'clientHeight']) {
    assert.ok(roh.includes(zugang), `${zugang} ist nicht gesperrt`);
  }
  assert.match(roh, /kein Layout/, 'und die Sperre nennt den Grund');
});

test('die Uebersetzung aus AUF-30 wird wiederverwendet, nicht ersetzt', () => {
  const roh = readFileSync(join(hier, '../dom-register.mjs'), 'utf8');
  assert.match(roh, /register\('\.\/test-hooks\.mjs'/, 'dieselben Hooks wie der schnelle Lauf');
  const schnell = readFileSync(join(hier, '../test-register.mjs'), 'utf8');
  assert.match(schnell, /register\('\.\/test-hooks\.mjs'/, 'und der schnelle Lauf ist unberuehrt');
});

test('der Satz fuer den naechsten, der die DOM-Testdatei oeffnet, steht in ihrem Kopf', () => {
  const roh = readFileSync(join(hier, '../__domtests__/dialogFokus.dom.test.ts'), 'utf8');
  assert.match(roh, /Kein Layout\. Geometrie wird im Browser gemessen \(iframe fester Breite\), nicht hier\./);
});
