/**
 * AUF-63 — **Fokusfalle, Fokus-Rückgabe und Leertaste, zum ersten Mal im Gate.**
 *
 * **Kein Layout. Geometrie wird im Browser gemessen (iframe fester Breite), nicht hier.**
 *
 * Bis heute waren diese drei Dinge mit **null** Tests gedeckt und hingen an einer Person mit
 * offenem Browser. AUF-49 hat die Fokusfalle gebaut — geprüft wurde damals nur die reine
 * Indexrechnung (`naechsterIndex`), *„den DOM-Teil kann die Testumgebung nicht sehen"*, so steht es
 * wörtlich im Kopf von `dialogFokus.ts`. Jetzt kann sie ihn sehen.
 *
 * **Was hier NICHT geprüft wird und auch nicht kann:** irgendeine Breite, Höhe, Kante oder ein
 * Überlauf. jsdom hat keine Layout-Engine; der Testlauf bricht ab, wenn es jemand versucht
 * (`dom-register.mjs`). **Die Sichtprobe bleibt Teil jeder `sichtbar`-Abnahme** — ein grünes
 * DOM-Gate ist kein Grund, sie zu vertagen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import React, { act } from 'react';
import { createRoot, type Root } from 'react-dom/client';
import { useDialogFokus } from '../app/dashboard/dialogFokus';
import { istAusloeser } from '../app/dashboard/dialogFokus';

/**
 * **`act`, nicht `setTimeout`.**
 *
 * Der erste Anlauf dieser Datei wartete mit `setTimeout(0)` auf React — und **die Ergebnisse
 * schwankten von Lauf zu Lauf**: mal fielen vier Zusagen, mal zwei, jedes Mal andere. Eine
 * Nebenläufigkeit, die mal grün und mal rot ist, ist schlimmer als gar kein Test: sie bringt einen
 * Testlauf in Verruf, den man danach nicht mehr ernst nimmt. React 19 rendert nebenläufig; nur
 * `act` sagt zu, dass Rendern **und** Effekte durch sind.
 */
(globalThis as unknown as { IS_REACT_ACT_ENVIRONMENT: boolean }).IS_REACT_ACT_ENVIRONMENT = true;

async function mounten(element: React.ReactElement): Promise<{ wurzel: Root; behaelter: HTMLElement }> {
  const behaelter = document.createElement('div');
  document.body.appendChild(behaelter);
  const wurzel = createRoot(behaelter);
  await act(async () => { wurzel.render(element); });
  return { wurzel, behaelter };
}

/** Abbauen und die Aufräum-Effekte (Fokus-Rückgabe!) wirklich laufen lassen. */
async function abbauen(wurzel: Root, behaelter: HTMLElement): Promise<void> {
  await act(async () => { wurzel.unmount(); });
  behaelter.remove();
}

function tasteDruecken(key: string, shiftKey = false): void {
  document.dispatchEvent(new KeyboardEvent('keydown', { key, shiftKey, bubbles: true }));
}

/** Ein minimaler Dialog mit drei Zielen — dieselbe Hülle, die `FachFlaeche` benutzt. */
function Dialog({ onSchliessen }: { onSchliessen: () => void }): React.ReactElement {
  const huelle = React.useRef<HTMLDivElement>(null);
  useDialogFokus(huelle, onSchliessen);
  return React.createElement('div', { ref: huelle, role: 'dialog', 'aria-modal': true },
    React.createElement('button', { id: 'eins' }, 'eins'),
    React.createElement('button', { id: 'zwei' }, 'zwei'),
    React.createElement('button', { id: 'drei' }, 'drei'));
}

const aktiv = (): string => (document.activeElement as HTMLElement | null)?.id ?? '';

// --- Beim Öffnen wandert der Fokus hinein --------------------------------------------------------
test('der Fokus wandert beim Öffnen in den Dialog — auf das erste Ziel', async () => {
  const { wurzel, behaelter } = await mounten(React.createElement(Dialog, { onSchliessen: () => {} }));
  assert.equal(aktiv(), 'eins');
  await abbauen(wurzel, behaelter);
});

// --- Die Fokusfalle -------------------------------------------------------------------------------
test('Tab am ENDE springt an den Anfang — nicht hinter den Dialog', async () => {
  // **Genau der Fehler, den AUF-49 beseitigt hat**, und der bis heute nur im Browser messbar war:
  // der erste Tab-Sprung landete in einer Oberfläche, die der Nutzer gar nicht sieht.
  const { wurzel, behaelter } = await mounten(React.createElement(Dialog, { onSchliessen: () => {} }));
  tasteDruecken('Tab'); assert.equal(aktiv(), 'zwei');
  tasteDruecken('Tab'); assert.equal(aktiv(), 'drei');
  tasteDruecken('Tab'); assert.equal(aktiv(), 'eins', 'am Ende im Kreis, nicht hinaus');
  await abbauen(wurzel, behaelter);
});

test('Shift+Tab am ANFANG springt ans Ende — die Falle gilt in beide Richtungen', async () => {
  // WCAG 2.1.2 gilt in beide Richtungen: gefangen sein ist falsch, ungefangen ebenso.
  const { wurzel, behaelter } = await mounten(React.createElement(Dialog, { onSchliessen: () => {} }));
  assert.equal(aktiv(), 'eins');
  tasteDruecken('Tab', true);
  assert.equal(aktiv(), 'drei');
  await abbauen(wurzel, behaelter);
});

// --- Escape und die Fokus-Rückgabe ----------------------------------------------------------------
test('Escape schließt — der Aufrufer entscheidet, was das heißt', async () => {
  let geschlossen = 0;
  const { wurzel, behaelter } = await mounten(
    React.createElement(Dialog, { onSchliessen: () => { geschlossen++; } }));
  tasteDruecken('Escape');
  assert.equal(geschlossen, 1);
  await abbauen(wurzel, behaelter);
});

test('beim Schließen kehrt der Fokus dorthin zurück, wo er herkam', async () => {
  // **Der Fall, den der Playwright-Lauf am 25.07. als fehlend gemeldet hat.** Er ist seit AUF-49
  // gebaut — aber bis heute war er im Gate unsichtbar.
  const ausloeser = document.createElement('button');
  ausloeser.id = 'ausloeser';
  document.body.appendChild(ausloeser);
  ausloeser.focus();
  assert.equal(aktiv(), 'ausloeser', 'Ausgangslage');

  const { wurzel, behaelter } = await mounten(React.createElement(Dialog, { onSchliessen: () => {} }));
  assert.equal(aktiv(), 'eins', 'der Dialog hat ihn geholt');

  await abbauen(wurzel, behaelter);
  assert.equal(aktiv(), 'ausloeser', 'und wieder zurückgegeben');
  ausloeser.remove();
});

test('steht der Auslöser nicht mehr im Dokument, wird der Fokus nicht irgendwohin gesetzt', async () => {
  const ausloeser = document.createElement('button');
  document.body.appendChild(ausloeser);
  ausloeser.focus();
  const { wurzel, behaelter } = await mounten(React.createElement(Dialog, { onSchliessen: () => {} }));
  ausloeser.remove();
  await abbauen(wurzel, behaelter);
  // Kein Wurf, keine Fokus-Wanderung an eine fremde Stelle.
  assert.ok(document.activeElement === document.body || document.activeElement === null);
});

// --- Die Leertaste auf selbstgebauten Schaltflächen ------------------------------------------------
test('die Leertaste löst aus — und verhindert dabei das Scrollen', async () => {
  // AUF-49, gemessen: acht `role="button"`-Flächen, davon reagierten SIEBEN nur auf Enter.
  let verhindert = 0;
  const e = { key: ' ', preventDefault: () => { verhindert++; } };
  assert.equal(istAusloeser(e), true);
  assert.equal(verhindert, 1, 'ohne preventDefault scrollt die Seite, während sie auslöst');
});

test('Enter löst aus, ohne etwas zu verhindern; andere Tasten lösen nicht aus', async () => {
  let verhindert = 0;
  const zaehler = { preventDefault: () => { verhindert++; } };
  assert.equal(istAusloeser({ key: 'Enter', ...zaehler }), true);
  assert.equal(verhindert, 0);
  for (const key of ['a', 'Escape', 'Shift', 'ArrowDown']) {
    assert.equal(istAusloeser({ key, ...zaehler }), false, `„${key}" darf nicht auslösen`);
  }
});

test('eine echte `role="button"`-Fläche reagiert im DOM auf die Leertaste', async () => {
  // Nicht die reine Funktion, sondern der Weg durch ein echtes Tastenereignis.
  let ausgeloest = 0;
  function Flaeche(): React.ReactElement {
    return React.createElement('span', {
      id: 'flaeche', role: 'button', tabIndex: 0,
      onKeyDown: (ev: React.KeyboardEvent) => { if (istAusloeser(ev)) ausgeloest++; },
    }, 'Fläche');
  }
  const { wurzel, behaelter } = await mounten(React.createElement(Flaeche));
  const knoten = document.getElementById('flaeche')!;
  knoten.focus();
  await act(async () => {
    knoten.dispatchEvent(new KeyboardEvent('keydown', { key: ' ', bubbles: true }));
  });
  assert.equal(ausgeloest, 1);
  await abbauen(wurzel, behaelter);
});

// --- Die Grenze setzt sich selbst durch -----------------------------------------------------------
test('K4: Geometrie im DOM-Lauf schlägt fehl — mit Grund, nicht mit einer 0', async () => {
  // **Der Kern der Grenze.** Ohne diese Sperre lieferte jsdom hier eine 0, und ein Test darauf wäre
  // immer grün oder immer rot — beides sieht aus wie eine Messung.
  const knoten = document.createElement('div');
  document.body.appendChild(knoten);
  for (const zugriff of [
    () => knoten.getBoundingClientRect(),
    () => knoten.getClientRects(),
    () => knoten.offsetWidth,
    () => knoten.scrollWidth,
    () => knoten.clientHeight,
  ]) {
    assert.throws(zugriff, /kein Layout/, 'dieser Zugang muss gesperrt sein');
  }
  knoten.remove();
});

test('was jsdom KANN, steht ausdrücklich daneben: Fokus, getComputedStyle, Tastatur', () => {
  const knoten = document.createElement('button');
  document.body.appendChild(knoten);
  knoten.focus();
  assert.equal(document.activeElement, knoten, 'Fokus: ja');
  assert.equal(typeof getComputedStyle(knoten).display, 'string', 'getComputedStyle: ja');
  assert.equal(new KeyboardEvent('keydown', { key: 'Tab' }).key, 'Tab', 'Tastatur: ja');
  knoten.remove();
});
