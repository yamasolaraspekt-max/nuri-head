/**
 * AUF-83-T3-N1 / N-03 — **der Beleg am geöffneten Menü, nicht am Quelltext.**
 *
 * K-08 war rot: Zeile 1 kostete 20 px, mehr als der Wegfall der alten Blade-Leiste zurückgab.
 * Übernehmen-Knopf, Staleness-Pille und Speicherstatus stehen seither hinter einem Überlauf-Knopf
 * (`ObjektkopfUeberlauf.tsx`). **Diese Datei hält fest, dass dabei nichts verloren ging** — alle
 * drei sind im geöffneten Menü erreichbar, mit demselben `action`, demselben Status und demselben
 * Verbot einer zweiten Statusquelle.
 *
 * **Gegenprobe, mit Worten statt Mutation:** Entfernte man den Übernehmen-`<form>` aus dem
 * Menü-JSX, würde `uebernehmenKnopf` unten `null` bleiben und der erste Assert fiele. Eine echte
 * Mutation am Quelltext ist für einen DOM-Test kein sauberes Werkzeug — das Muster ist dasselbe wie
 * bei `reiterLeiste.dom.test.ts`: die Zusage prüft das gerenderte Ergebnis, nicht den Quelltext.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import React, { act } from 'react';
import { createRoot, type Root } from 'react-dom/client';
import { ObjektkopfUeberlauf } from '../app/dashboard/ObjektkopfUeberlauf';
import type { Objektkopf } from '../app/state/objektkopf';

(globalThis as unknown as { IS_REACT_ACT_ENVIRONMENT: boolean }).IS_REACT_ACT_ENVIRONMENT = true;

const KOPF: Objektkopf = {
  // Ziel bewusst abstrakt, keine reale Objektseiten-Route — `projektKlick.test.ts`/K4 verbietet
  // zusammengesetzte Hausplaner-Objekt-Pfade außerhalb von `__tests__/`, damit die ausgelieferte
  // Insel nie selbst ein Routing-Ziel zusammensetzt.
  name: 'EVALUATOR-MESSWELLE', adresse: '', uebernehmenUrl: '/x/uebernehmen',
  status: 'veraltet', revision: 9, szeneLeer: false,
};

const SPEICHERSTATUS = { text: 'Gespeichert', farbe: '#1a9e5f', grund: '#e6f6ee' };

async function mounten(offen: boolean): Promise<{ wurzel: Root; behaelter: HTMLElement; setOffen: (o: boolean) => void }> {
  const behaelter = document.createElement('div');
  document.body.appendChild(behaelter);
  const wurzel = createRoot(behaelter);
  let aktuellOffen = offen;
  const render = (): void => {
    wurzel.render(
      React.createElement(ObjektkopfUeberlauf, {
        objektkopf: KOPF, speicherstatus: SPEICHERSTATUS, csrfToken: 'probe-token',
        offen: aktuellOffen, setOffen: (o: boolean) => { aktuellOffen = o; render(); },
      }),
    );
  };
  await act(async () => { render(); });
  return { wurzel, behaelter, setOffen: (o) => { aktuellOffen = o; } };
}

test('N-03 (DOM): geschlossen zeigt Zeile 1 nur den Umschalt-Knopf — kein Menü im DOM', async () => {
  const { wurzel, behaelter } = await mounten(false);
  try {
    assert.equal(behaelter.querySelectorAll('[role="dialog"]').length, 0, 'das Menü steht offen, obwohl niemand es geöffnet hat');
    const knopf = behaelter.querySelector('.hp-ok-ueberlauf-knopf');
    assert.ok(knopf, 'kein Umschalt-Knopf im DOM');
    assert.equal(knopf!.getAttribute('aria-expanded'), 'false');
    assert.equal(knopf!.getAttribute('aria-haspopup'), 'dialog');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('N-03 (DOM): geöffnet trägt das Menü alle drei Elemente — mit demselben `action` und Status', async () => {
  const { wurzel, behaelter } = await mounten(true);
  try {
    const dialog = behaelter.querySelector('[role="dialog"]');
    assert.ok(dialog, 'das Menü fehlt im geöffneten Zustand');
    assert.equal(dialog!.getAttribute('aria-label'), 'Übernahme und Speicherstatus');

    // Übernehmen-Knopf: derselbe `action`, ein `form`-Submit, kein `fetch`-Pfad.
    const form = dialog!.querySelector('form.hp-ok-form') as HTMLFormElement | null;
    assert.ok(form, 'der Übernehmen-Knopf ist auf dem Weg ins Menü verloren gegangen');
    assert.equal(form!.getAttribute('action'), KOPF.uebernehmenUrl, 'die Übernahme zeigt auf eine andere Route');
    assert.equal(form!.method, 'post');
    const uebernehmenKnopf = form!.querySelector('button[type="submit"]') as HTMLButtonElement | null;
    assert.ok(uebernehmenKnopf, 'kein Submit-Knopf im Formular');
    assert.equal(uebernehmenKnopf!.disabled, KOPF.szeneLeer, 'der Sperrzustand folgt nicht `szeneLeer`');
    assert.match(form!.innerHTML, /probe-token/, 'der CSRF-Token wird nicht durchgereicht');

    // Staleness-Pille: derselbe Status, keine zweite Berechnung.
    const pille = dialog!.querySelector('[class*="hp-ok-pille"]');
    assert.ok(pille, 'die Staleness-Pille ist auf dem Weg ins Menü verloren gegangen');
    assert.match(pille!.className, /hp-ok-pille--veraltet/, 'der Status im Menü weicht vom übergebenen Kopf ab');
    assert.match(pille!.textContent ?? '', /VERALTET/);

    // Speicherstatus: derselbe Text, dieselbe Quelle (Prop, nicht neu berechnet).
    assert.match(dialog!.textContent ?? '', /Gespeichert/, 'der Speicherstatus ist auf dem Weg ins Menü verloren gegangen');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('N-03 (DOM): der gesperrte Übernehmen-Knopf nennt den Grund — Hinweis nicht verloren', async () => {
  const behaelter = document.createElement('div');
  document.body.appendChild(behaelter);
  const wurzel = createRoot(behaelter);
  await act(async () => {
    wurzel.render(
      React.createElement(ObjektkopfUeberlauf, {
        objektkopf: { ...KOPF, status: 'nie', szeneLeer: true },
        speicherstatus: SPEICHERSTATUS, csrfToken: 'probe-token', offen: true, setOffen: () => {},
      }),
    );
  });
  try {
    const knopf = behaelter.querySelector('form.hp-ok-form button[type="submit"]') as HTMLButtonElement;
    assert.equal(knopf.disabled, true, 'bei leerer Szene ist der Knopf frei');
    assert.match(knopf.title, /Keine Szene vorhanden/, 'der Grund fehlt im Titel');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});
