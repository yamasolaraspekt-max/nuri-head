/**
 * AUF-48 Scheibe 3 / K-03 — **die vier Zustandsfunktionen, erstmals verriegelt.**
 *
 * `waehleBereich` · `klappeSchiene` · `oeffnePalette` · `schliessePalette` bleiben in der
 * Komponente — sie lesen und schreiben Zustand, das war die Meldung aus Scheibe 2 und der Planner
 * hat sie bestätigt. **Was ihnen fehlte, sind Zusagen.**
 *
 * ---
 *
 * **Was diese Datei erreichen kann, und was nicht — offen gesagt, statt es zu verschweigen:**
 *
 * Die vier sind **inline in `HausplanerApp` und nicht exportiert**. Ein echter Aufruf bräuchte eine
 * gemountete Komponente mit Konva — und `dom-register.mjs` verbietet Geometrie ausdrücklich. Die
 * Zusagen hier greifen deshalb auf zwei Ebenen:
 *
 * 1. **Der EFFEKT der Bausteine**, aus denen sie bestehen — der gespeicherte Zustand, den der
 *    Auftrag ausdrücklich verlangt (*„sie muss den gespeicherten Zustand prüfen"*). Für
 *    `arbeitsbereichSpeicher` gab es **bis heute keinen Rundweg-Test**; er steht jetzt hier.
 * 2. **Die VERDRAHTUNG** der vier — dass sie ihren Parameter durchreichen, statt einen festen Wert
 *    zu setzen, und dass die beiden Palettenfunktionen entgegengesetzt wirken.
 *
 * **Jede der Zusagen unter (2) ist gegen genau die Mutation geprüft, die der Auftrag nennt** —
 * gemessen, nicht behauptet. *Eine Verdrahtungs-Zusage ist schwächer als ein Effekt-Test; sie hier
 * als gleichwertig auszugeben wäre die Sorte Behauptung, gegen die dieses Projekt seine Regeln hat.*
 *
 * **Der gemessene Befund vor dem Schreiben:** von den vier war `waehleBereich` unverriegelt,
 * `klappeSchiene` unverriegelt, `oeffnePalette` nur durch einen Quelltext-Zähler in
 * `arbeitszeileSuche.test.ts` gefangen. *Dritte Scheibe in Folge mit demselben Ergebnis.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  ladeArbeitsbereich, speichereArbeitsbereich, ARBEITSBEREICH_SCHLUESSEL,
} from '../app/state/arbeitsbereichSpeicher';
import { ladeSchienen, speichereSchienen, SCHIENEN_STANDARD } from '../app/state/schienenSpeicher';
// AUF-48: die Hauptansicht ist zerlegt — diese Zusage liest ALLE ihre Teile.
import { zerlegteApp } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const app = zerlegteApp()
  .replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');

/** Ein In-Memory-`localStorage` — dieselbe Technik wie in `schienenSpeicher.test.ts`. */
function mitSpeicher<T>(lauf: () => T): T {
  const daten = new Map<string, string>();
  const vorher = (globalThis as { localStorage?: unknown }).localStorage;
  (globalThis as { localStorage?: unknown }).localStorage = {
    getItem: (k: string) => (daten.has(k) ? daten.get(k)! : null),
    setItem: (k: string, v: string) => { daten.set(k, v); },
  };
  try {
    return lauf();
  } finally {
    (globalThis as { localStorage?: unknown }).localStorage = vorher;
  }
}

// --- waehleBereich: der gespeicherte Zustand ------------------------------------------------------

test('K-03 (Effekt): der gewählte Arbeitsbereich überlebt den Neuladen — Rundweg', () => {
  // **Diese Zusage gab es bis heute nicht.** `arbeitsbereiche.test.ts` prüft nur den Fall OHNE
  // Browser; dass Speichern und Laden zusammenpassen, hat niemand gemessen.
  mitSpeicher(() => {
    speichereArbeitsbereich('heizung');
    assert.equal(ladeArbeitsbereich(), 'heizung');
    speichereArbeitsbereich('elektro-pv');
    assert.equal(ladeArbeitsbereich(), 'elektro-pv', 'der zweite Wechsel kommt nicht an');
  });
});

test('K-03 (Effekt): ein unbekannter Bereich wird beim Lesen verworfen, nicht durchgereicht', () => {
  // Ein alter Eintrag aus einer früheren Fassung stellte die Leiste sonst auf einen Bereich, den
  // es nicht mehr gibt — sichtbar wäre dann nur noch das Durchgängige.
  mitSpeicher(() => {
    localStorage.setItem(ARBEITSBEREICH_SCHLUESSEL, 'gibt-es-nicht-mehr');
    assert.equal(ladeArbeitsbereich(), undefined);
  });
});

test('K-03 (Verdrahtung): `waehleBereich` speichert DEN GEWÄHLTEN Bereich, nicht einen festen', () => {
  // Gegen die Mutation aus dem Auftrag: `speichereArbeitsbereich("architektur")` statt `(id)`.
  const block = app.match(/const waehleBereich = React\.useCallback\(\(id: string\) => \{[\s\S]{0,300}?\}, \[\]\);/);
  assert.ok(block, '`waehleBereich` wurde nicht gefunden — die Zusage misst Leere');
  assert.match(block[0], /setActiveWorkspace\(id\)/, 'der UI-Zustand bekommt nicht die gewählte id');
  assert.match(block[0], /speichereArbeitsbereich\(id\)/, 'gespeichert wird nicht die gewählte id');
});

// --- klappeSchiene: der gespeicherte Zustand je Arbeitsbereich -------------------------------------

test('K-03 (Effekt): der Klappzustand hängt am Arbeitsbereich, nicht am Planer', () => {
  mitSpeicher(() => {
    speichereSchienen('elektro-pv', { links: false, rechts: true });
    assert.deepEqual(ladeSchienen('elektro-pv'), { links: false, rechts: true });
    assert.deepEqual(ladeSchienen('architektur'), SCHIENEN_STANDARD, 'ein fremder Bereich hat den Standard verloren');
  });
});

test('K-03 (Verdrahtung): `klappeSchiene` setzt den übergebenen Wert — ungedreht', () => {
  // Gegen die Mutation aus dem Auftrag: `[seite]: !offen`.
  const block = app.match(/const klappeSchiene = React\.useCallback\([\s\S]{0,400}?\}, \[activeWorkspace\]\);/);
  assert.ok(block, '`klappeSchiene` wurde nicht gefunden — die Zusage misst Leere');
  assert.match(block[0], /\{ \.\.\.alt, \[seite\]: offen \}/, 'der Klappzustand wird verdreht oder anders gebildet');
  assert.doesNotMatch(block[0], /\[seite\]: !offen/, 'der übergebene Wert wird umgekehrt');
  assert.match(block[0], /speichereSchienen\(activeWorkspace, neu\)/, 'gespeichert wird nicht der neue Zustand');
});

// --- oeffnePalette / schliessePalette: entgegengesetzt --------------------------------------------

test('K-03 (Verdrahtung): die beiden Palettenfunktionen wirken ENTGEGENGESETZT', () => {
  // Gegen die Mutation aus dem Auftrag: die beiden vertauschen.
  const auf = app.match(/const oeffnePalette = React\.useCallback\([\s\S]{0,300}?\}, \[\]\);/);
  const zu = app.match(/const schliessePalette = React\.useCallback\([\s\S]{0,300}?\}, \[\]\);/);
  assert.ok(auf && zu, 'eine der beiden Funktionen wurde nicht gefunden');
  assert.match(auf[0], /setPaletteOffen\(true\)/, '`oeffnePalette` öffnet nicht');
  assert.match(auf[0], /paletteOffenRef\.current = true/, 'der Spiegel wird beim Öffnen nicht gesetzt');
  assert.match(zu[0], /setPaletteOffen\(false\)/, '`schliessePalette` schliesst nicht');
  assert.match(zu[0], /paletteOffenRef\.current = false/, 'der Spiegel wird beim Schliessen nicht gesetzt');
});

test('K-03 (Verdrahtung): Öffnen setzt den Filter zurück — sonst stünde die alte Suche noch da', () => {
  const auf = app.match(/const oeffnePalette = React\.useCallback\([\s\S]{0,300}?\}, \[\]\);/);
  assert.ok(auf);
  assert.match(auf[0], /setPaletteFilter\(''\)/, 'der Filter bleibt beim Öffnen stehen');
  assert.match(auf[0], /setPaletteIndex\(0\)/, 'die Markierung bleibt auf der alten Zeile');
});

// --- K-05: die vier localStorage-Zugriffe bleiben, wo sie sind ------------------------------------

test('K-05: `HausplanerApp` fasst `localStorage` GAR NICHT an — die Speicher-Module tun es', () => {
  // **Befund gegen die Prämisse des Blattes, gemessen statt übernommen (P-04):**
  // Das Blatt nennt „vier localStorage-Zugriffe" in `HausplanerApp.tsx`, gestützt auf
  // `grep -c 'localStorage'` → 4. **Alle vier stehen in KOMMENTAREN** (Z299, 314, 371, 387);
  // im Code sind es **null**. Der Zähler misst Prosa, nicht Zugriffe.
  //
  // **Die wahre Eigenschaft ist stärker als die behauptete:** die Komponente kennt den Speicher
  // überhaupt nicht — sie ruft `speichereArbeitsbereich` / `speichereSchienen` / `speichereAngeheftet`,
  // und NUR die fassen `localStorage` an. *Genau das soll die Zerlegung erhalten, und genau das
  // hält diese Zusage fest.*
  assert.equal((app.match(/localStorage/g) ?? []).length, 0,
    'HausplanerApp greift direkt auf den Speicher zu — das gehört in ein Speicher-Modul');
  // presence-Partner nach R2: die Zugriffe gibt es, nur woanders.
  for (const modul of ['arbeitsbereichSpeicher', 'schienenSpeicher', 'angeheftet']) {
    const quelle = readFileSync(join(hier, `../app/state/${modul}.ts`), 'utf8');
    assert.match(quelle, /localStorage/, `${modul} greift nicht mehr auf den Speicher zu`);
  }
});
