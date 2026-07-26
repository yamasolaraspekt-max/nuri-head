/**
 * AUF-56 — **zwei Elevation-Rollen statt vierzehn roher Schatten.**
 *
 * Gemessener Anlass: `rgba(28,40,48,.05)` und `rgba(28,50,55,.10)` waren die zwei häufigsten
 * Schattenwerte der Insel — jeder an jeder Stelle einzeln ausgeschrieben.
 *
 * **Eine Rolle ist die ganze Aussage „wie hoch schwebt diese Fläche"** — Versatz, Weichzeichnung
 * und Farbwert zusammen. Ein reiner Farb-Token hätte die Geometrie weiterhin überall einzeln
 * stehen lassen; die zweite Wahrheit wäre nur kleiner geworden.
 *
 * **Dieser Posten ändert keinen gerenderten Wert.** Er ändert, woher er kommt. Genau das ist leicht
 * zu behaupten und schwer zu belegen — deshalb prüft dieser Test nicht den Quelltext, sondern das
 * **erzeugte Markup**: dort muss zeichengleich dasselbe stehen wie vorher.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { renderToStaticMarkup } from 'react-dom/server';
import { createElement } from 'react';
import { T } from '../app/studioDaten';
import { StartView } from '../app/StartView';

const hier = dirname(fileURLToPath(import.meta.url));
const appWurzel = join(hier, '../app');

/** Die abgelösten Rohwerte — zusammengesetzt, damit diese Datei nicht selbst zum Treffer wird. */
const ROH_FLACH = '0 1px 2px rgba(28,' + '40,48,.05)';
const ROH_GEHOBEN = '0 10px 34px rgba(28,' + '50,55,.10)';

/**
 * **Kommentare zählen nicht mit — zeilentreu entfernt.**
 * Beim ersten Lauf meldete diese Funktion vier statt drei Fundstellen der gehobenen Rolle. Die
 * vierte war **mein eigener Kommentar**, der den Token beim Namen nennt. *Ein Zähler, der
 * Erklärungen für Code hält, zählt Erklärungen.* Die Zeilennummern bleiben erhalten, damit ein
 * Treffer weiterhin auffindbar ist.
 */
function ohneKommentare(s: string): string {
  const leeren = (t: string): string => t.replace(/[^\n]/g, ' ');
  return s
    .replace(/\/\*[\s\S]*?\*\//g, leeren)
    .replace(/(^|[^:])\/\/[^\n]*/g, (treffer, vor: string) => vor + leeren(treffer.slice(vor.length)));
}

function zaehleInApp(nadel: string, ohneStudioDaten = true): string[] {
  const treffer: string[] = [];
  const gehe = (verzeichnis: string): void => {
    for (const name of readdirSync(verzeichnis)) {
      if (name === 'node_modules') continue;
      const pfad = join(verzeichnis, name);
      if (statSync(pfad).isDirectory()) { gehe(pfad); continue; }
      if (!/\.tsx?$/.test(pfad)) continue;
      if (ohneStudioDaten && pfad.endsWith('studioDaten.ts')) continue;
      const inhalt = ohneKommentare(readFileSync(pfad, 'utf8'));
      let i = inhalt.indexOf(nadel);
      while (i !== -1) {
        treffer.push(`${pfad.slice(appWurzel.length + 1)}:${inhalt.slice(0, i).split('\n').length}`);
        i = inhalt.indexOf(nadel, i + 1);
      }
    }
  };
  gehe(appWurzel);
  return treffer;
}

// --- K1: wertgleich, Zeichen für Zeichen ----------------------------------------------------------
test('K1: beide Rollen tragen exakt den abgelösten Rohwert', () => {
  assert.equal(T.schattenFlach, ROH_FLACH);
  assert.equal(T.schattenGehoben, ROH_GEHOBEN);
});

test('K1: und das erzeugte Markup zeigt denselben Wert wie vorher', () => {
  // **Der eigentliche Beweis.** Der Quelltext könnte einen Token nennen und trotzdem etwas anderes
  // ausgeben; das Markup kann es nicht.
  const html = renderToStaticMarkup(createElement(StartView, {
    onGuided: () => {}, onKonfigurator: () => {}, projekte: [],
  }));
  assert.ok(html.includes(`box-shadow:${ROH_FLACH}`), 'die flache Rolle erscheint unverändert');
  assert.ok(!html.includes('schattenFlach'), 'der Token-Name landet nicht im Markup');
});

// --- K2: die Vorkommen sind abgelöst --------------------------------------------------------------
test('K2: der flache Wert steht in `app/` nirgends mehr roh — null Treffer', () => {
  const treffer = zaehleInApp(ROH_FLACH);
  assert.deepEqual(treffer, [], `noch roh: ${treffer.join(', ')}`);
});

test('K2: der gehobene Wert ebenfalls null — in seiner abgelösten Form', () => {
  const treffer = zaehleInApp(ROH_GEHOBEN);
  assert.deepEqual(treffer, [], `noch roh: ${treffer.join(', ')}`);
});

test('K2: die Rollen werden benutzt, der Rohwert nirgends — die Eigenschaft, nicht die Zahl', () => {
  // **Nachgezogen in AUF-38 Scheibe 2:** die Zusage nagelte **10 und 3** Fundstellen fest. Scheibe 2
  // hat zwei davon in die Stilschicht verschoben (`var(--hp-schatten-flach)` in der CSS), und die
  // Zusage ging rot — **obwohl die geschuetzte Eigenschaft unberuehrt ist**: der Rohwert steht
  // nirgends, die Rolle wird ueberall benutzt. *Eine Zusage, die eine Anzahl festhaelt statt der
  // Eigenschaft, bricht bei jeder Verschiebung.* Das ist in diesem Zyklus das vierte Mal.
  const flach = zaehleInApp('T.schattenFlach');
  const gehoben = zaehleInApp('T.schattenGehoben');
  assert.ok(flach.length > 0, 'die flache Rolle wird nirgends mehr benutzt?');
  assert.ok(gehoben.length > 0, 'die gehobene Rolle wird nirgends mehr benutzt?');
  // Und in der Stilschicht steht sie als Variable, nicht als Wert.
  const css = readFileSync(join(hier, '../hausplaner.css'), 'utf8');
  assert.match(css, /var\(--hp-schatten-flach\)/, 'die Rolle fehlt in der CSS');
  assert.doesNotMatch(css.replace(/\/\*[\s\S]*?\*\//g, ''), /rgba?\(/, 'ein roher Schatten in der CSS');
});

// --- Der eine Wert, der bleibt --------------------------------------------------------------------
test('der 30-px-Ausreißer bleibt roh — und wird benannt, nicht verschwiegen', () => {
  // **Der Auftrag nannte zwölf Vorkommen, gemessen waren es vierzehn.** Die zwei zusätzlichen
  // stammen aus AUF-66, also von mir. Dreizehn passten in die zwei Rollen; einer nicht: er trägt
  // **30 px** Weichzeichnung statt 34 — dieselbe Farbe, andere Geometrie. Ihn anzugleichen wäre
  // eine **sichtbare** Änderung und bleibt Yamas Entscheidung, wie bei den acht anderen
  // „nah dran"-Werten. *Er ist hier festgehalten, damit er nicht als Versehen durchgeht.*
  const rest = zaehleInApp('0 10px 30px rgba(28,' + '50,55,.10)');
  assert.equal(rest.length, 1, `erwartet genau einer, gefunden: ${rest.join(', ')}`);
  assert.match(rest[0]!, /^StartView\.tsx:/, 'und zwar in der Projektkachel aus AUF-66');
});

// --- K3: kein weiterer Wert angefasst -------------------------------------------------------------
test('K3: die selteneren Schatten bleiben unangetastet — Zahl für Zahl', () => {
  // Ein Token für einen einzigen Aufruf ist keine Rolle, sondern eine Umbenennung.
  assert.equal(zaehleInApp('rgba(28,' + '50,55,.18)').length, 2);
  assert.equal(zaehleInApp('rgba(24,' + '34,38,.30)').length, 2);
  assert.equal(zaehleInApp('rgba(255,' + '255,255,.7)').length, 1);
});

test('K3: die Rollen stehen im Token-Modul und sonst nirgends', () => {
  const daten = readFileSync(join(appWurzel, 'studioDaten.ts'), 'utf8');
  assert.ok(daten.includes(ROH_FLACH), 'der Wert lebt jetzt hier');
  assert.ok(daten.includes(ROH_GEHOBEN));
  // Und keine zweite Quelle: die Rohwerte tauchen dort je genau einmal auf.
  assert.equal(daten.split(ROH_FLACH).length - 1, 1);
  assert.equal(daten.split(ROH_GEHOBEN).length - 1, 1);
});

test('K3: keine neue Farbe entstanden — die Rollen sind Schatten, keine Flächenfarben', () => {
  assert.match(T.schattenFlach, /^0 /, 'ein Schatten beginnt mit seinem Versatz');
  assert.match(T.schattenGehoben, /^0 /);
});
