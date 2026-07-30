/**
 * AUF-83-T3 / K-05 — **der Beleg am gerenderten Reiter, nicht am Quelltext.**
 *
 * **Warum diese Datei existiert:** Das Votum vom 30.07. lautete `NICHT PRÜFBAR` — nicht wegen des
 * Baus, sondern weil K-05 einen **DOM-Auszug** als Beleg verlangt und die Browser-Werkzeugkette
 * des Evaluators dreimal blockierte, sobald er Reiter-Beschriftungen mitlas. Die eine Messung, die
 * durchkam (*„12 role=tab, keiner gedämpft"*), war mehrdeutig, **und er hat sie als mehrdeutig
 * ausgewiesen statt als Befund.**
 *
 * Von seinen zwei Wegen ist dies der zweite und der haltbarere, mit seinen Worten: *„eine
 * DOM-Zusage, die die gedämpfte Deckkraft am gerenderten Reiter festhält statt am Quelltext; dann
 * braucht es meinen Browser nicht mehr — sie misst weiter, wenn keiner hinsieht."*
 *
 * **Was hier NICHT geprüft wird:** jede Geometrie. jsdom hat keine Layout-Engine, und
 * `dom-register.mjs` bricht ab, wenn es jemand versucht. Deckkraft und Text sind Stilwerte und
 * Textknoten — die kann jsdom sehen. **Die Sichtprobe bleibt trotzdem Teil der Abnahme:** ob ein
 * gedämpfter Reiter *auffällt*, sagt kein Testlauf.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import React, { act } from 'react';
import { createRoot, type Root } from 'react-dom/client';
import { ReiterLeiste, type ReiterEintrag } from '../app/dashboard/ReiterLeiste';
import { ARBEITSBEREICHE } from '../app/dashboard/arbeitsbereiche';
import { GESPERRT_DECKKRAFT } from '../app/dashboard/gesperrtStil';

(globalThis as unknown as { IS_REACT_ACT_ENVIRONMENT: boolean }).IS_REACT_ACT_ENVIRONMENT = true;

async function mounten(reiter: readonly ReiterEintrag[]): Promise<{ wurzel: Root; behaelter: HTMLElement }> {
  const behaelter = document.createElement('div');
  document.body.appendChild(behaelter);
  const wurzel = createRoot(behaelter);
  await act(async () => {
    wurzel.render(
      React.createElement(ReiterLeiste, {
        reiter, aktiv: reiter[0]!.id, setAktiv: () => {},
        ariaLabel: 'Probe', panelId: 'p', reiterId: (id: string) => `t-${id}`,
      }),
    );
  });
  return { wurzel, behaelter };
}

/** Genau die Reiter, die die Arbeitszeile im Betrieb bekommt — dieselbe Abbildung wie `HausplanerApp:105`. */
const BEREICHS_REITER: readonly ReiterEintrag[] = ARBEITSBEREICHE.map((b) => ({
  id: b.id, label: b.label, hinweis: b.hinweis, nochNicht: b.nochNicht,
}));

// --- Der Auszug, den das Kriterium verlangt -------------------------------------------------------

test('K-05 (DOM): fünf Reiter, und GENAU EINER kommt gedämpft an', async () => {
  const { wurzel, behaelter } = await mounten(BEREICHS_REITER);
  try {
    const reiter = [...behaelter.querySelectorAll('[role="tab"]')] as HTMLElement[];
    assert.equal(reiter.length, 5, 'die Arbeitszeile rendert nicht fünf Reiter');

    // **Die Zuordnung, die dem Evaluator gefehlt hat:** Beschriftung UND Deckkraft in einem Zug.
    const auszug = reiter.map((el) => ({
      text: (el.textContent ?? '').trim(),
      deckkraft: el.style.opacity === '' ? 1 : Number(el.style.opacity),
    }));
    const gedaempft = auszug.filter((r) => r.deckkraft < 1);
    assert.equal(gedaempft.length, 1,
      `${gedaempft.length} gedämpfte Reiter — genau einer war gemeint: ${JSON.stringify(auszug)}`);
    assert.match(gedaempft[0]!.text, /^Import/, `der gedämpfte ist nicht Import: ${gedaempft[0]!.text}`);
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-05 (DOM): der Wert ist der aus `gesperrtStil`, nicht irgendein kleinerer', async () => {
  const { wurzel, behaelter } = await mounten(BEREICHS_REITER);
  try {
    const importReiter = [...behaelter.querySelectorAll('[role="tab"]')]
      .find((el) => (el.textContent ?? '').startsWith('Import')) as HTMLElement | undefined;
    assert.ok(importReiter, 'kein Import-Reiter im DOM');
    assert.equal(Number(importReiter.style.opacity), GESPERRT_DECKKRAFT);
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-05 (DOM): „sagt das auch" — der Reiter trägt den Zusatz und den Grund im Titel', async () => {
  // WCAG 1.4.1: die Aussage darf nicht allein an der Dämpfung hängen. **Genau das war am
  // Quelltext nicht belegbar** — dort steht der Zusatz in einer Bedingung, hier steht er im DOM.
  const { wurzel, behaelter } = await mounten(BEREICHS_REITER);
  try {
    const el = [...behaelter.querySelectorAll('[role="tab"]')]
      .find((e) => (e.textContent ?? '').startsWith('Import')) as HTMLElement;
    assert.match(el.textContent ?? '', /noch nicht/, 'der Zusatz kommt nicht im DOM an');
    const titel = el.getAttribute('title') ?? '';
    assert.match(titel, /Import & Nachzeichnen — /, 'der Titel nennt den Bereich nicht');
    assert.ok(titel.length > 40, `der Grund fehlt im Titel: „${titel}"`);
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

// --- Die Gegenrichtung: ohne das Merkmal ist alles wie vorher --------------------------------------

test('K-05 (DOM, Auflage): ohne `nochNicht` ist KEIN Reiter gedämpft und keiner trägt einen Zusatz', async () => {
  // **Der Beleg für die beiden anderen Nutzer der geteilten Leiste** — Panel-Reiter und
  // Schienen-Reiter setzen das Merkmal nicht. Am Quelltext war das nur *„sie enthalten das Wort
  // nicht"*; hier ist es das gerenderte Ergebnis.
  const ohne = BEREICHS_REITER.map(({ id, label, hinweis }) => ({ id, label, hinweis }));
  const { wurzel, behaelter } = await mounten(ohne);
  try {
    const reiter = [...behaelter.querySelectorAll('[role="tab"]')] as HTMLElement[];
    assert.equal(reiter.length, 5);
    assert.deepEqual(reiter.filter((el) => el.style.opacity !== '').map((el) => el.textContent), [],
      'ein Reiter ist gedämpft, obwohl niemand das Merkmal gesetzt hat');
    assert.deepEqual(reiter.filter((el) => (el.textContent ?? '').includes('noch nicht')).length, 0,
      'ein Zusatz erscheint ohne Merkmal');
    // Und der gewöhnliche Tooltip ist unverändert der Hinweis, nicht der Grund.
    assert.equal(reiter[0]!.getAttribute('title'), ARBEITSBEREICHE[0]!.hinweis);
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-05 (DOM): der gedämpfte Reiter bleibt bedienbar — kein `disabled`, Fokus erreichbar', async () => {
  // Die Grenze aus dem Bau, jetzt am gerenderten Element statt am Quelltext: „ausgegraut" ist eine
  // Aussage über den Inhalt, keine Entziehung der Bedienung.
  const { wurzel, behaelter } = await mounten(BEREICHS_REITER);
  try {
    const el = [...behaelter.querySelectorAll('[role="tab"]')]
      .find((e) => (e.textContent ?? '').startsWith('Import')) as HTMLButtonElement;
    assert.equal(el.disabled, false, 'der Reiter ist gesperrt — beauftragt war ausgegraut');
    assert.notEqual(el.getAttribute('aria-disabled'), 'true', 'für Screenreader gilt er als gesperrt');
    assert.equal(el.getAttribute('tabindex'), '0', 'der aktive Reiter ist nicht fokussierbar');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});
