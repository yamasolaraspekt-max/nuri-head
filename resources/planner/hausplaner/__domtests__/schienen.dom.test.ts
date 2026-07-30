/**
 * AUF-83-T5 / K-01 — der Beleg am gerenderten Schalter: erreichbarer Name, `aria-expanded`,
 * unabhängige Bedienung.
 *
 * **Was hier NICHT geprüft wird:** die tatsächliche Breite der Schiene oder der Bühnengewinn
 * (K-02) — jsdom hat keine Layout-Engine (`dom-register.mjs`). Das ist Sache der Sichtprobe im
 * echten Browser. Was hier steht: dass der Schalter jeder Schiene für sich bedienbar ist und den
 * Zustand ehrlich anzeigt — die Voraussetzung dafür, dass „unabhängig" überhaupt etwas heißt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import React, { act } from 'react';
import { createRoot, type Root } from 'react-dom/client';
import { SchienenSchalter } from '../app/dashboard/SchienenSchalter';

(globalThis as unknown as { IS_REACT_ACT_ENVIRONMENT: boolean }).IS_REACT_ACT_ENVIRONMENT = true;

async function mounten(kinder: React.ReactElement): Promise<{ wurzel: Root; behaelter: HTMLElement }> {
  const behaelter = document.createElement('div');
  document.body.appendChild(behaelter);
  const wurzel = createRoot(behaelter);
  await act(async () => { wurzel.render(kinder); });
  return { wurzel, behaelter };
}

test('K-01 (DOM): der Schalter trägt `aria-expanded` und einen erreichbaren Namen', async () => {
  const { wurzel, behaelter } = await mounten(
    React.createElement(SchienenSchalter, { seite: 'links', offen: true, onClick: () => {}, label: 'Planer-Bereiche' }),
  );
  try {
    const knopf = behaelter.querySelector('button') as HTMLButtonElement;
    assert.ok(knopf, 'kein Knopf im DOM');
    assert.equal(knopf.getAttribute('aria-expanded'), 'true');
    assert.match(knopf.title, /Planer-Bereiche/, 'der erreichbare Name nennt nicht, was die Schiene zeigt');
    assert.match(knopf.title, /einklappen/, 'offen sollte „einklappen" anbieten');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-01 (DOM): geschlossen bietet der Titel „ausklappen" an, und `aria-expanded` ist false', async () => {
  const { wurzel, behaelter } = await mounten(
    React.createElement(SchienenSchalter, { seite: 'rechts', offen: false, onClick: () => {}, label: 'Eigenschaften' }),
  );
  try {
    const knopf = behaelter.querySelector('button') as HTMLButtonElement;
    assert.equal(knopf.getAttribute('aria-expanded'), 'false');
    assert.match(knopf.title, /ausklappen/);
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-01 (DOM): ein Klick löst genau EINMAL aus — kein doppelter Griff', async () => {
  let klicks = 0;
  const { wurzel, behaelter } = await mounten(
    React.createElement(SchienenSchalter, { seite: 'links', offen: true, onClick: () => { klicks += 1; }, label: 'Planer-Bereiche' }),
  );
  try {
    const knopf = behaelter.querySelector('button') as HTMLButtonElement;
    knopf.dispatchEvent(new MouseEvent('click', { bubbles: true }));
    assert.equal(klicks, 1);
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-01 (DOM): zwei Schalter mit UNABHÄNGIGEM Zustand — der eine klicken rührt den anderen nicht', async () => {
  // **Der Kern von K-01, am gerenderten Ergebnis.** Zwei Instanzen derselben Komponente mit
  // eigenem `onClick` — nur der angeklickte darf feuern.
  let linksKlicks = 0;
  let rechtsKlicks = 0;
  const { wurzel, behaelter } = await mounten(
    React.createElement(React.Fragment, null,
      React.createElement(SchienenSchalter, { seite: 'links', offen: true, onClick: () => { linksKlicks += 1; }, label: 'Planer-Bereiche' }),
      React.createElement(SchienenSchalter, { seite: 'rechts', offen: true, onClick: () => { rechtsKlicks += 1; }, label: 'Eigenschaften' }),
    ),
  );
  try {
    const knoepfe = [...behaelter.querySelectorAll('button')] as HTMLButtonElement[];
    assert.equal(knoepfe.length, 2, 'zwei Schienen, zwei Schalter erwartet');
    knoepfe[0]!.dispatchEvent(new MouseEvent('click', { bubbles: true }));
    assert.equal(linksKlicks, 1);
    assert.equal(rechtsKlicks, 0, 'der rechte Schalter hat auf den Klick links reagiert');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-01 (Auflage): der Schalter bleibt bedienbar — kein `disabled`, kein `aria-disabled`', async () => {
  // Dieselbe Grenze wie bei den gedämpften Arbeitsbereichs-Reitern (K-05 aus T3): ein Zustand ist
  // eine Aussage, keine Entziehung der Bedienung.
  const { wurzel, behaelter } = await mounten(
    React.createElement(SchienenSchalter, { seite: 'links', offen: false, onClick: () => {}, label: 'Planer-Bereiche' }),
  );
  try {
    const knopf = behaelter.querySelector('button') as HTMLButtonElement;
    assert.equal(knopf.disabled, false);
    assert.notEqual(knopf.getAttribute('aria-disabled'), 'true');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});
