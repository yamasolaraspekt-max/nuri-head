/**
 * AUF-83-T5 / K-04 — der Klappzustand überlebt einen Neuladen, je Arbeitsbereich.
 *
 * **Kein `localStorage` im Testlauf** (`test:hausplaner` läuft in plain Node, nicht jsdom) — die
 * „ohne Browser"-Fälle sind deshalb der NORMALFALL dieser Datei, nicht ein Sonderfall. Für die
 * Fälle mit echten gespeicherten Werten wird `globalThis.localStorage` mit einem winzigen
 * In-Memory-Mock belegt und danach wieder entfernt — dieselbe Technik, die ein Browser dort
 * bereitstellt, nur lokal nachgebaut.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  ladeSchienen, speichereSchienen, SCHIENEN_STANDARD, SCHIENEN_SCHLUESSEL,
} from '../app/state/schienenSpeicher';

function mitMockSpeicher<T>(lauf: () => T): T {
  const speicher = new Map<string, string>();
  const mock: Pick<Storage, 'getItem' | 'setItem'> = {
    getItem: (k: string) => (speicher.has(k) ? speicher.get(k)! : null),
    setItem: (k: string, v: string) => { speicher.set(k, v); },
  };
  const vorher = (globalThis as { localStorage?: unknown }).localStorage;
  (globalThis as { localStorage?: unknown }).localStorage = mock;
  try {
    return lauf();
  } finally {
    (globalThis as { localStorage?: unknown }).localStorage = vorher;
  }
}

// --- Ohne Browser: der Normalfall in diesem Testlauf -----------------------------------------------

test('K-04: ohne `localStorage` gilt der Standard — kein Wurf', () => {
  assert.deepEqual(ladeSchienen('architektur'), SCHIENEN_STANDARD);
  assert.doesNotThrow(() => speichereSchienen('architektur', { links: false, rechts: true }));
});

test('K-04: der Standard ist „beide offen" — das heutige, einzige Verhalten', () => {
  assert.deepEqual(SCHIENEN_STANDARD, { links: true, rechts: true });
});

// --- Mit echtem Speicher: die Weißliste und der Rundweg --------------------------------------------

test('K-04: geschrieben und gelesen ergibt denselben Zustand', () => {
  mitMockSpeicher(() => {
    speichereSchienen('elektro-pv', { links: false, rechts: true });
    assert.deepEqual(ladeSchienen('elektro-pv'), { links: false, rechts: true });
  });
});

test('K-04: der Zustand hängt am Arbeitsbereich — „Elektro · PV" zu, „Architektur" bleibt unverändert', () => {
  mitMockSpeicher(() => {
    speichereSchienen('elektro-pv', { links: false, rechts: false });
    assert.deepEqual(ladeSchienen('architektur'), SCHIENEN_STANDARD,
      'ein fremder Arbeitsbereich hat den Standard verloren');
  });
});

test('K-04: zwei Arbeitsbereiche unter demselben Schlüssel stören sich nicht — Read-Modify-Write', () => {
  mitMockSpeicher(() => {
    speichereSchienen('architektur', { links: false, rechts: true });
    speichereSchienen('elektro-pv', { links: true, rechts: false });
    assert.deepEqual(ladeSchienen('architektur'), { links: false, rechts: true },
      'das Schreiben für „elektro-pv" hat „architektur" überschrieben');
    assert.deepEqual(ladeSchienen('elektro-pv'), { links: true, rechts: false });
  });
});

test('K-04 (Gegenprobe): ein kaputter Eintrag liefert den Standard, nicht den rohen Text', () => {
  mitMockSpeicher(() => {
    localStorage.setItem(SCHIENEN_SCHLUESSEL, '{"architektur": "kaputt"}');
    assert.deepEqual(ladeSchienen('architektur'), SCHIENEN_STANDARD);
  });
});

test('K-04 (Gegenprobe): kein valides JSON überhaupt liefert den Standard', () => {
  mitMockSpeicher(() => {
    localStorage.setItem(SCHIENEN_SCHLUESSEL, 'kein json');
    assert.deepEqual(ladeSchienen('architektur'), SCHIENEN_STANDARD);
  });
});

test('K-04 (Gegenprobe): ein Feld fehlt oder hat den falschen Typ ⇒ Standard', () => {
  mitMockSpeicher(() => {
    localStorage.setItem(SCHIENEN_SCHLUESSEL, JSON.stringify({ architektur: { links: false } }));
    assert.deepEqual(ladeSchienen('architektur'), SCHIENEN_STANDARD, 'rechts fehlt — trotzdem angenommen?');
    localStorage.setItem(SCHIENEN_SCHLUESSEL, JSON.stringify({ architektur: { links: 'nein', rechts: true } }));
    assert.deepEqual(ladeSchienen('architektur'), SCHIENEN_STANDARD, 'links ist kein boolean — trotzdem angenommen?');
  });
});

test('K-04: schreibt kaputten Altbestand NICHT fort, sondern ersetzt ihn', () => {
  mitMockSpeicher(() => {
    localStorage.setItem(SCHIENEN_SCHLUESSEL, 'kein json');
    speichereSchienen('architektur', { links: false, rechts: false });
    assert.deepEqual(ladeSchienen('architektur'), { links: false, rechts: false });
  });
});
