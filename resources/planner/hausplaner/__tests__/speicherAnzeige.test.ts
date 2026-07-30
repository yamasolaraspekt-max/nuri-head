/**
 * AUF-47 — der Speichern-Knopf und die Statusplakette sagen die Wahrheit.
 *
 * **Der Widerspruch war sichtbar und stand nebeneinander:** In derselben Kopfzeile warnte
 * „Testfläche — wird NICHT gespeichert", während die Plakette **„Gespeichert"** meldete und der
 * grüne Knopf unbedingt klickbar war. Der Klick sendete nichts und blieb „erfolgreich".
 *
 * Geprüft wird die Regel (rein) **und** die Verdrahtung — ohne die zweite bliebe die erste folgenlos.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { speicherAnzeige } from '../app/dashboard/speicherAnzeige';
import type { SpeicherStatus } from '../store/hausplanerStore';
// AUF-48: die Hauptansicht ist zerlegt — diese Zusage liest ALLE ihre Teile.
import { zerlegteApp } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const app = ohneKommentare(zerlegteApp());
const regel = ohneKommentare(readFileSync(join(hier, '../app/dashboard/speicherAnzeige.ts'), 'utf8'));

const ALLE: SpeicherStatus[] = ['gespeichert', 'ungespeichert', 'speichert', 'konflikt', 'fehler'];

// --- Der Kern: ohne Ziel wird nichts behauptet --------------------------------------------------
test('ohne Speicherziel sagt die Plakette die Wahrheit — in JEDEM Zustand', () => {
  for (const status of ALLE) {
    const a = speicherAnzeige(status, false);
    assert.equal(a.text, 'Testfläche — wird nicht gespeichert', `${status}: falsche Aussage`);
    assert.equal(a.gesperrt, true, `${status}: der Knopf verspricht sonst etwas, das nicht passiert`);
    assert.ok(a.knopfTitel.length > 30, `${status}: der Grund fehlt im Tooltip`);
  }
});

test('„Gespeichert" steht NIE auf einer Fläche, die nicht speichern kann', () => {
  // Genau das war der Widerspruch: „Gespeichert · Rev. 1" neben „wird NICHT gespeichert".
  assert.notEqual(speicherAnzeige('gespeichert', false).text, 'Gespeichert');
  assert.equal(speicherAnzeige('gespeichert', true).text, 'Gespeichert', 'mit Ziel bleibt es dabei');
});

// --- Mit Ziel bleibt alles, wie es war ----------------------------------------------------------
test('mit Speicherziel sind die fünf Zustände unverändert', () => {
  assert.deepEqual(
    ALLE.map((s) => speicherAnzeige(s, true).text),
    [
      'Gespeichert',
      'Ungespeicherte Änderungen',
      'Wird gespeichert …',
      'Konflikt: Plan wurde von anderer Seite geändert (Revision ?) — Seite neu laden',
      'Speichern fehlgeschlagen — erneut versuchen',
    ],
  );
  assert.match(speicherAnzeige('konflikt', true, 7).text, /Revision 7/, 'die Revision steht drin');
});

test('gesperrt ist der Knopf nur dort, wo Drücken schaden oder täuschen würde', () => {
  assert.equal(speicherAnzeige('gespeichert', true).gesperrt, false);
  assert.equal(speicherAnzeige('ungespeichert', true).gesperrt, false);
  assert.equal(speicherAnzeige('fehler', true).gesperrt, false, 'einen Fehler darf man erneut versuchen');
  assert.equal(speicherAnzeige('speichert', true).gesperrt, true, 'zweimal senden hilft niemandem');
  assert.equal(speicherAnzeige('konflikt', true).gesperrt, true, 'sonst überschreibt man den fremden Stand');
});

test('jeder Zustand trägt eine Gewichtung und einen Knopf-Tooltip — nie nur Farbe, nie leer', () => {
  for (const kann of [true, false]) {
    for (const status of ALLE) {
      const a = speicherAnzeige(status, kann);
      assert.match(a.art, /^(ok|warnung|neutral|fehler)$/);
      assert.ok(a.text.length > 8, `${status}/${kann}: Text zu dünn`);
      assert.ok(a.knopfTitel.length > 10, `${status}/${kann}: Tooltip zu dünn`);
      assert.doesNotMatch(a.text, /folgt|in Kürze|demnächst/i);
    }
  }
});

test('die Regel kennt keine Farbwerte — die Oberfläche bildet die Gewichtung auf Token ab', () => {
  assert.doesNotMatch(regel, /#[0-9a-fA-F]{3,8}\b|rgba?\(/);
  assert.doesNotMatch(regel, /\bT\./, 'kein Token-Zugriff in der reinen Regel');
  assert.match(app, /const ANZEIGE_TOKEN: Record<AnzeigeArt/);
});

// --- Die Verdrahtung ----------------------------------------------------------------------------
test('die App liest die Fähigkeit aus dem Store — sie rät sie nicht', () => {
  assert.match(app, /const kannSpeichern = useHausplanerStore\(\(s\) => Boolean\(s\.speichernUrl\)\);/);
  assert.match(app, /speicherAnzeige\(speicherStatus, kannSpeichern, konfliktRevision\)/);
});

test('der Knopf ist wirklich sperrbar — vorher hatte er KEIN disabled', () => {
  const knopf = app.match(/<button\n\s*type="button"\n\s*onClick=\{\(\) => void store\.getState\(\)\.save\(\)\}[\s\S]*?>/);
  assert.ok(knopf, 'Speichern-Knopf nicht gefunden');
  assert.match(knopf[0], /disabled=\{anzeige\.gesperrt\}/);
  assert.match(knopf[0], /title=\{anzeige\.knopfTitel\}/);
});

test('der `save()`-No-Op bleibt unangetastet — er war gewollt', () => {
  const store = ohneKommentare(readFileSync(join(hier, '../store/hausplanerStore.ts'), 'utf8'));
  assert.match(store, /if \(!scene \|\| !speichernUrl\) \{\s*return;\s*\}/);
});

/**
 * Befund aus der eigenen Sichtprobe: Es gibt **zwei** Statusanzeigen. Der Knopf und die Plakette im
 * Planer waren nach der ersten Fassung ehrlich — die Kopfzeile des Studios sagte weiter
 * „Gespeichert · Rev. 1". Genau die hatte Yama gesehen, und sie stand direkt neben dem Hinweis
 * „Testfläche — wird NICHT gespeichert". Zwei Anzeigen, eine Regel: beide lesen jetzt
 * `speicherAnzeige`.
 */
test('auch die Studio-Kopfzeile liest die Regel — nicht ihre eigene Tabelle', () => {
  const studio = ohneKommentare(readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8'));
  assert.match(studio, /speicherAnzeige\(speicherStatus, kannSpeichern, konfliktRevision\)/);
  assert.doesNotMatch(studio, /gespeichert: \{ label: 'Gespeichert'/, 'die eigene Tabelle ist weg');
  // „· Rev. 1" hängt an der Fähigkeit: ohne Speicherziel gibt es keine gespeicherte Revision.
  assert.match(studio, /scene && kannSpeichern \? ` · Rev\. \$\{scene\.revision\}` : ''/);
});
