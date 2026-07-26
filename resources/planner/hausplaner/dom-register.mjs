// AUF-63 — **DOM-Testlauf: Fokus und Tastatur, NICHT Geometrie.**
//
// Registriert dieselben Hooks wie `test-register.mjs` (die `esbuild`-Übersetzung aus AUF-30 wird
// **wiederverwendet, nicht ersetzt**) und stellt zusätzlich ein jsdom-Fenster bereit.
//
// **Kein Layout. Geometrie wird im Browser gemessen (iframe fester Breite), nicht hier.**
//
// jsdom hat **keine Layout-Engine**: jede Breite ist dort `0`. Ein Test, der `getBoundingClientRect`
// prüft, ist deshalb *immer* grün oder *immer* rot — beides wertlos, und beides sieht aus wie eine
// Messung. **Diese Datei setzt die Grenze selbst durch**, statt sie in ein Dokument zu schreiben,
// das niemand liest: die fünf Geometrie-Zugänge werfen hier einen Fehler mit Begründung.
//
// Warum ein **zweiter** Lauf und kein umgebauter erster: ein DOM für alle 125 Testdateien zu stellen
// macht 125 Dateien langsamer, damit ein Dutzend etwas prüfen kann.
import { register } from 'node:module';
import { JSDOM } from 'jsdom';

register('./test-hooks.mjs', import.meta.url);

const fenster = new JSDOM('<!doctype html><html><body></body></html>', {
  url: 'http://localhost/',
  pretendToBeVisual: true,
});

/** Was aus dem jsdom-Fenster global werden muss, damit React darin rendert. */
const DURCHREICHEN = [
  'window', 'document', 'navigator', 'HTMLElement', 'Element', 'Node', 'Event',
  'KeyboardEvent', 'MouseEvent', 'CustomEvent', 'getComputedStyle', 'requestAnimationFrame',
  'cancelAnimationFrame', 'DocumentFragment', 'SVGElement',
];

for (const name of DURCHREICHEN) {
  const wert = name === 'window' ? fenster.window : fenster.window[name];
  if (wert !== undefined && globalThis[name] === undefined) {
    Object.defineProperty(globalThis, name, { value: wert, writable: true, configurable: true });
  }
}

/**
 * **Die Grenze, und sie greift zur Laufzeit.**
 *
 * Ein Test, der hier Geometrie abfragt, bekommt keinen `0`-Wert, sondern einen Abbruch mit dem
 * Grund. *Lieber ein Testlauf, der seine Grenze selbst durchsetzt, als eine Zeile in einem
 * Dokument.*
 */
const GRENZE = 'AUF-63: Im DOM-Testlauf gibt es kein Layout — jede Breite waere 0. '
  + 'Geometrie wird im Browser gemessen (iframe fester Breite), nicht hier.';

const El = fenster.window.Element;
const HtmlEl = fenster.window.HTMLElement;

El.prototype.getBoundingClientRect = function verboten() {
  throw new Error(`${GRENZE} (getBoundingClientRect)`);
};
El.prototype.getClientRects = function verboten() {
  throw new Error(`${GRENZE} (getClientRects)`);
};
for (const [ziel, name] of [[HtmlEl, 'offsetWidth'], [HtmlEl, 'offsetHeight'],
  [El, 'scrollWidth'], [El, 'scrollHeight'], [El, 'clientWidth'], [El, 'clientHeight']]) {
  Object.defineProperty(ziel.prototype, name, {
    configurable: true,
    get() { throw new Error(`${GRENZE} (${name})`); },
  });
}
