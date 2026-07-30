/**
 * AUF-16 / Befund B1 — Regressionsschutz: die Kontext-Options-Leiste darf NICHT im Rumpf von
 * `HausplanerApp` definiert sein.
 *
 * Warum kein Render-Test: `HausplanerApp.tsx` zieht React, react-konva und three nach; die
 * Testumgebung hier hat **kein DOM**. Die Messung des Evaluators (Typ-Identität über zwei Renders:
 * `false` vorher) ist damit hier nicht reproduzierbar — das ist ausdrücklich benannt und nicht
 * behauptet. Was hier geprüft wird, ist die **Ursache** statt der Wirkung: das Muster, das die
 * neue Identität erzeugt. Wer die Komponente zurück in den Rumpf schiebt, macht diesen Test rot.
 *
 * Der erste Fall dokumentiert zusätzlich den Mechanismus selbst — eine im Funktionsrumpf erzeugte
 * Komponente ist bei jedem Aufruf eine andere; genau das riss den Teilbaum bei jeder Mausbewegung ab.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

/**
 * **AUF-48 Scheibe 4b: die Leiste ist mit ihrer Zeile umgezogen** — nach
 * `rahmen/GruppenzeileUndSchiene.tsx`. **Die geprüfte Eigenschaft ist unverändert und sie ist der
 * Kern dieses Tests:** die Komponente steht auf MODULEBENE, nicht im Rumpf. *Genau deshalb ist der
 * Umzug hierher der Prüfort — nicht die Datei, aus der sie kam.*
 */
const quelle = readFileSync(
  join(dirname(fileURLToPath(import.meta.url)), '../app/rahmen/GruppenzeileUndSchiene.tsx'),
  'utf8',
);
/** Für die Absenz-Zusage: im Rumpf von `HausplanerApp` darf sie erst recht nicht stehen. */
const hauptfunktion = readFileSync(
  join(dirname(fileURLToPath(import.meta.url)), '../app/HausplanerApp.tsx'),
  'utf8',
);

test('Mechanismus: eine im Rumpf erzeugte Komponente hat je Aufruf eine ANDERE Identität', () => {
  const rumpfRender = (): (() => null) => {
    const Inner = (): null => null; // genau das Muster aus Befund B1
    return Inner;
  };
  assert.notEqual(rumpfRender(), rumpfRender(), 'Rumpf-Definition erzeugt neue Identität je Render');
});

test('KontextOptionenLeiste ist auf Modulebene deklariert (Spalte 0)', () => {
  assert.match(quelle, /^function KontextOptionenLeiste\(/m);
});

test('KontextOptionenLeiste ist NICHT im Rumpf von HausplanerApp deklariert', () => {
  // Rumpf-Deklarationen sind eingerückt; genau die sind hier verboten.
  for (const wo of [quelle, hauptfunktion]) {
    assert.doesNotMatch(wo, /^\s+const KontextOptionenLeiste\s*=/m);
    assert.doesNotMatch(wo, /^\s+function KontextOptionenLeiste\(/m);
  }
});

test('die Werte kommen als explizite Props, nicht über Closure (Kante 3: keine vergessene Prop)', () => {
  const aufruf = quelle.match(/<KontextOptionenLeiste[\s\S]*?\/>/);
  assert.ok(aufruf, 'Aufrufstelle nicht gefunden');
  for (const prop of ['werkzeug', 'fensterTypWahl', 'tuerTypWahl', 'setFensterTypWahl', 'setTuerTypWahl']) {
    assert.ok(aufruf[0].includes(`${prop}=`), `Prop ${prop} fehlt am Aufrufort — stille undefined-Anzeige`);
  }
});

test('die Options-Quellen sind unverändert (byte-treuer Umzug, Kriterium 6)', () => {
  assert.match(quelle, /istFenster \? FENSTER_TYPEN : TUER_TYPEN/);
  assert.match(quelle, /\{v\.label\} · \{v\.breite\}×\{v\.hoehe\} mm/);
  assert.match(quelle, /value=\{istFenster \? fensterTypWahl : tuerTypWahl\}/);
});
