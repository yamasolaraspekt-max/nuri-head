/**
 * **Z1-E4-1 (g) — der Beleg am gerenderten Panel, nicht am Quelltext.**
 *
 * `bodenplatte.test.ts` prüft die Wortprobe an der Datei. **Das reicht nicht**, und die Lehre ist
 * teuer bezahlt: beim U-Dach waren `tsc` und Suite grün, und die Insel riss trotzdem ab — *ein
 * Wurf während des Renders sieht keiner von beiden.* Diese Datei rendert das Panel wirklich.
 *
 * Sie prüft zusätzlich den Fall, den die Quelltextprobe gar nicht sehen kann: **ohne erfassten
 * Aufbau steht der Vermerk da und KEINE Zahl** — dieselbe Komponente, zwei Zustände.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import React, { act } from 'react';
import { createRoot, type Root } from 'react-dom/client';
import { BodenplattenPanel } from '../app/rahmen/BodenplattenPanel';
import type { FoundationSlabNode } from '../domain/scene.types';

(globalThis as unknown as { IS_REACT_ACT_ENVIRONMENT: boolean }).IS_REACT_ACT_ENVIRONMENT = true;

const ISO = '2026-08-22T00:00:00.000Z';
function platte(over: Partial<FoundationSlabNode> = {}): FoundationSlabNode {
  return {
    id: 'b1', type: 'foundation_slab', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO,
    polygon: [{ x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 10000 }, { x: 0, y: 10000 }],
    dickeMm: 250, oberkanteMm: -180, erdberuehrt: true,
    schichten: [{ dickeMm: 120 }, { dickeMm: 60 }],
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt', ...over,
  };
}

function rendere(p: FoundationSlabNode, aendere: (c: Record<string, unknown>) => void = () => {}): { el: HTMLElement; wurzel: Root } {
  const el = document.createElement('div');
  document.body.appendChild(el);
  const wurzel = createRoot(el);
  act(() => { wurzel.render(React.createElement(BodenplattenPanel, { platte: p, aendere })); });
  return { el, wurzel };
}

test('Z1-E4-1-g: das Panel rendert ohne Wurf und zeigt Dicke, Kote und erdberuehrt', () => {
  const { el, wurzel } = rendere(platte());
  const text = el.textContent ?? '';
  assert.match(text, /Bodenplatte/);
  // **Die negative Kote steht wirklich im Bild** — mit Komma und Vorzeichen, wie im Bauplan.
  assert.match(text, /−0,18 m|-0,18 m/, `Kote fehlt im gerenderten Panel: ${text}`);
  assert.match(text, /erdberührt/);
  assert.equal((el.querySelector('[data-feld="dickeMm"]') as HTMLInputElement | null)?.value, '250');
  assert.equal((el.querySelector('[data-feld="oberkanteMm"]') as HTMLInputElement | null)?.value, '-180');
  assert.equal((el.querySelector('[data-feld="erdberuehrt"]') as HTMLInputElement | null)?.checked, true);
  // Der Aufbau ist erfasst ⇒ Summe statt Vermerk.
  assert.match(text, /180 mm/);
  assert.equal(el.querySelector('[data-zustand="nicht-erfasst"]'), null);
  act(() => wurzel.unmount());
  el.remove();
});

test('Z1-E4-1-g/e: ohne erfassten Aufbau steht der Vermerk — und KEINE Zahl', () => {
  const { el, wurzel } = rendere(platte({ schichten: undefined }));
  const text = el.textContent ?? '';
  assert.match(text, /Aufbau nicht erfasst/);
  assert.ok(el.querySelector('[data-zustand="nicht-erfasst"]'), 'der Zustand ist nicht ausgezeichnet');
  // **Eine 0 hier wäre die Lüge, um die es geht:** sie sähe aus wie eine Messung.
  const aufbau = el.querySelector('[data-feld="aufbau"]')?.textContent ?? '';
  assert.doesNotMatch(aufbau, /0 mm/, `der leere Aufbau zeigt eine Zahl: ${aufbau}`);
  act(() => wurzel.unmount());
  el.remove();
});

test('Z1-E4-1-g: das Wort „geprueft" steht in keinem der beiden Zustaende im Bild', () => {
  for (const p of [platte(), platte({ schichten: undefined })]) {
    const { el, wurzel } = rendere(p);
    assert.doesNotMatch(el.textContent ?? '', /geprüft|geprueft/i, 'das Panel behauptet eine Pruefung');
    assert.doesNotMatch(el.textContent ?? '', /bewehr/i, 'das Panel legt eine Bemessung nahe');
    act(() => wurzel.unmount());
    el.remove();
  }
});

test('Z1-E4-1-g: eine Aenderung geht als Command hinaus, nicht in einen eigenen Zustand', () => {
  const gesehen: Record<string, unknown>[] = [];
  const { el, wurzel } = rendere(platte(), (c) => gesehen.push(c));
  const schalter = el.querySelector('[data-feld="erdberuehrt"]') as HTMLInputElement;
  act(() => { schalter.click(); });
  assert.deepEqual(gesehen, [{ erdberuehrt: false }]);
  // **Der Schalter steht danach WEITER auf true** — das Panel führt keinen eigenen Zustand,
  // die Wahrheit kommt aus dem Dokument zurück. Ein Panel, das sich selbst umstellt, zeigt
  // etwas anderes an als das Modell, sobald der Command abgelehnt wird.
  assert.equal((el.querySelector('[data-feld="erdberuehrt"]') as HTMLInputElement).checked, true);
  act(() => wurzel.unmount());
  el.remove();
});
