/**
 * AUF-38 Scheibe 1 — **das Grundgeruest der Stilschicht, ohne eine einzige Umstellung.**
 *
 * Die Scheibe beweist die Mechanik, **bevor** irgendetwas umgebaut wird: CSS entsteht, das Blade
 * zieht sie, die Variablen kommen an. Geht dabei etwas schief, ist nichts umgestellt.
 *
 * **Die Eigenschaft, die dieser Test schuetzt:** `T` bleibt die **einzige** Farbwahrheit. Ein
 * Farbwert in der CSS stuende neben `T` und altert dort still — genau die zweite Wahrheit, die T1
 * beseitigt hat.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { T } from '../app/studioDaten';
import { tokenVariablen, variablenName, setzeTokenVariablen, HP_PRAEFIX } from '../app/stil/tokenVariablen';

const hier = dirname(fileURLToPath(import.meta.url));
const quelle = readFileSync(join(hier, '../hausplaner.css'), 'utf8');
const gebaut = join(hier, '../../../../public/hausplaner/hausplaner.css');

// --- K5: die Variablen stammen aus T ---------------------------------------------------------------
test('K5: jede Variable traegt einen Wert aus `studioDaten.ts` — keine Konstante daneben', () => {
  const paare = tokenVariablen();
  assert.equal(paare.length, Object.keys(T).length, 'genau die Tokens aus T, keiner mehr');
  const werte = new Set<string>(Object.values(T).map(String));
  for (const [name, wert] of paare) {
    assert.ok(name.startsWith(HP_PRAEFIX), `${name} traegt nicht das Praefix`);
    assert.ok(werte.has(wert), `${name}: „${wert}" steht nicht in T`);
  }
});

test('K5: aendert sich T, aendert sich die Variable — sie ist abgeleitet, nicht abgeschrieben', () => {
  const paar = tokenVariablen().find(([n]) => n === '--hp-accent')!;
  assert.equal(paar[1], T.accent, 'derselbe Wert, aus derselben Quelle');
});

test('camelCase wird kebab-case — eine Schreibweise, nicht zwei', () => {
  assert.equal(variablenName('accent'), '--hp-accent');
  assert.equal(variablenName('accentSoft'), '--hp-accent-soft');
  assert.equal(variablenName('canvasGridStrong'), '--hp-canvas-grid-strong');
});

test('setzen ohne DOM tut nichts, statt zu werfen', () => {
  // Der Testlauf hat kein Fenster; ein Wurf waere ein Fehler ueber eine Lage, die keiner ist.
  assert.doesNotThrow(() => { setzeTokenVariablen(null); });
});

test('setzen schreibt genau die Paare — an ein Ziel, das mitschreibt', () => {
  const geschrieben: Array<[string, string]> = [];
  setzeTokenVariablen({ style: { setProperty: (n: string, w: string) => { geschrieben.push([n, w]); } } });
  assert.deepEqual(geschrieben, tokenVariablen());
});

// --- K4: kein roher Farbwert in der CSS -------------------------------------------------------------
test('K4: die CSS-Quelle enthaelt in KEINER Regel einen Farbwert', () => {
  // Kommentare erklaeren, warum keiner drinsteht — sie sind kein Code.
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const treffer = ohneKommentare.match(/#[0-9a-fA-F]{3,8}\b|rgba?\(/g) ?? [];
  assert.deepEqual(treffer, [], `Farbwerte in der CSS: ${treffer.join(', ')}`);
});

test('K4: und auch die GEBAUTE Datei traegt keinen', () => {
  assert.ok(existsSync(gebaut), 'die gebaute CSS fehlt — dann greift das Blade nicht');
  const treffer = readFileSync(gebaut, 'utf8').match(/#[0-9a-fA-F]{3,8}\b|rgba?\(/g) ?? [];
  assert.deepEqual(treffer, [], `Farbwerte in der gebauten CSS: ${treffer.join(', ')}`);
});

// --- Scheibe 1 stellt NICHTS um ----------------------------------------------------------------------
test('Scheibe 1 aendert keine einzige Stelle — die CSS ist absichtlich wirkungslos', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '').trim();
  // Genau eine Regel, genau eine Eigenschaft, und die liest niemand.
  assert.match(ohneKommentare, /^:root \{\s*--hp-stilschicht: 1;\s*\}$/);
  // Eine leere Datei wuerde der Bau verwerfen; eine eigene Eigenschaft kann kein Pixel verschieben.
});

test('die Stilschicht wird genau einmal importiert — in `main.tsx`', () => {
  const einstieg = readFileSync(join(hier, '../main.tsx'), 'utf8');
  assert.equal((einstieg.match(/import '\.\/hausplaner\.css';/g) ?? []).length, 1);
  assert.match(einstieg, /setzeTokenVariablen\(\);/, 'und die Variablen werden beim Start gesetzt');
});

test('das Blade bindet die CSS bewacht ein — es brauchte keine Aenderung', () => {
  const blade = readFileSync(join(hier, '../../../views/admin/hausplaner/objekt.blade.php'), 'utf8');
  assert.match(blade, /@if \(file_exists\(public_path\('hausplaner\/hausplaner\.css'\)\)\)/);
});
