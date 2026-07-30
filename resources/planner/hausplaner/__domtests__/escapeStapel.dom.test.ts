/**
 * AUF-83-T5 / K-03 — der Beleg am gedrückten Escape, nicht am Quelltext.
 *
 * **Der gemessene Mangel, reproduziert:** zwei unabhängige `document`-Listener, beide auf
 * Escape — ohne den Stapel schlössen bei gleichzeitig offener Palette und offenem Menü BEIDE.
 * Diese Datei drückt Escape wirklich und zählt, was passiert ist.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { useEscapeEbene, anzahlAktiv } from '../app/dashboard/escapeStapel';
import React, { act } from 'react';
import { createRoot, type Root } from 'react-dom/client';

(globalThis as unknown as { IS_REACT_ACT_ENVIRONMENT: boolean }).IS_REACT_ACT_ENVIRONMENT = true;

/** Eine winzige Test-Komponente: registriert genau eine Ebene, solange `offen` gilt. */
function Ebene({ art, offen, onSchliessen }: { art: Parameters<typeof useEscapeEbene>[0]; offen: boolean; onSchliessen: () => void }): null {
  useEscapeEbene(art, offen, onSchliessen);
  return null;
}

function tasteEscape(): void {
  document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
}

async function mounten(kinder: React.ReactElement): Promise<{ wurzel: Root; behaelter: HTMLElement }> {
  const behaelter = document.createElement('div');
  document.body.appendChild(behaelter);
  const wurzel = createRoot(behaelter);
  await act(async () => { wurzel.render(kinder); });
  return { wurzel, behaelter };
}

test('K-03 (DOM): eine einzelne offene Ebene schließt auf Escape', async () => {
  let geschlossen = 0;
  const { wurzel, behaelter } = await mounten(
    React.createElement(Ebene, { art: 'menue', offen: true, onSchliessen: () => { geschlossen += 1; } }),
  );
  try {
    tasteEscape();
    assert.equal(geschlossen, 1);
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-03 (DOM): Palette UND Menü gleichzeitig offen — Escape schließt NUR die Palette', async () => {
  // **Genau der Befund, für den `escapeStapel.ts` gebaut wurde.** Ohne Rangfolge schlössen beide
  // unabhängigen Listener auf denselben Tastendruck.
  let paletteZu = 0;
  let menueZu = 0;
  const { wurzel, behaelter } = await mounten(
    React.createElement(React.Fragment, null,
      React.createElement(Ebene, { art: 'palette', offen: true, onSchliessen: () => { paletteZu += 1; } }),
      React.createElement(Ebene, { art: 'menue', offen: true, onSchliessen: () => { menueZu += 1; } }),
    ),
  );
  try {
    tasteEscape();
    assert.equal(paletteZu, 1, 'die Palette hätte schließen müssen');
    assert.equal(menueZu, 0, 'das Menü ist rangniedriger — es hätte NICHT schließen dürfen');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-03 (DOM): schließt sich die obere Ebene, gewinnt beim NÄCHSTEN Escape die darunter', async () => {
  let paletteOffen = true;
  let menueZu = 0;
  function Szene(): React.ReactElement {
    const [offen, setOffen] = React.useState(true);
    paletteOffen = offen;
    useEscapeEbene('palette', offen, () => setOffen(false));
    useEscapeEbene('menue', true, () => { menueZu += 1; });
    return React.createElement(React.Fragment, null);
  }
  const { wurzel, behaelter } = await mounten(React.createElement(Szene));
  try {
    // Escape löst `setOffen(false)` aus — der Tastendruck selbst muss in `act` liegen, nicht nur
    // ein leerer Nachlauf danach, sonst warnt React zu Recht vor einem Update außerhalb von `act`.
    await act(async () => { tasteEscape(); });
    assert.equal(paletteOffen, false, 'die Palette hätte beim ersten Escape schließen müssen');
    assert.equal(menueZu, 0, 'das Menü war beim ersten Escape noch nicht dran');

    await act(async () => { tasteEscape(); });
    assert.equal(menueZu, 1, 'das Menü hätte beim zweiten Escape dran sein müssen');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});

test('K-03 (DOM): eine geschlossene Ebene meldet sich ab — der Registrierstand fällt auf 0 zurück', async () => {
  const vorher = anzahlAktiv();
  const { wurzel, behaelter } = await mounten(
    React.createElement(Ebene, { art: 'schiene', offen: true, onSchliessen: () => {} }),
  );
  assert.equal(anzahlAktiv(), vorher + 1, 'die Ebene hat sich nicht angemeldet');
  await act(async () => { wurzel.unmount(); });
  behaelter.remove();
  assert.equal(anzahlAktiv(), vorher, 'die Ebene hat sich beim Unmount nicht abgemeldet — sie bliebe fälschlich aktiv');
});

test('K-03 (DOM, Gegenprobe): eine NICHT aktive Ebene reagiert nicht auf Escape', async () => {
  let geschlossen = 0;
  const { wurzel, behaelter } = await mounten(
    React.createElement(Ebene, { art: 'menue', offen: false, onSchliessen: () => { geschlossen += 1; } }),
  );
  try {
    tasteEscape();
    assert.equal(geschlossen, 0, 'eine geschlossene Ebene darf nicht auf Escape reagieren');
  } finally {
    await act(async () => { wurzel.unmount(); });
    behaelter.remove();
  }
});
