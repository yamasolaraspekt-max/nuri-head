/**
 * AUF-71 — eine Beschreibung für „gesperrt", app-weit.
 *
 * **Der Befund (Zustands-Inventur des Evaluators):** vier Stellen beschrieben denselben Zustand
 * verschieden. Der auffälligste Bruch war der leiseste — **Deckkraft 0,6 gegen 0,4**. Beim Bauen
 * kam eine **fünfte** Stelle mit **0,45** dazu, die in der Inventur fehlte.
 *
 * **Kein offener Defekt:** überall unterschied sich gesperrt bereits messbar von frei. Es geht um
 * Einheitlichkeit und um die WCAG-Härtung.
 *
 * **Das Kriterium, das diesen Posten trägt, ist der Mutations-Gegenbeweis (K8):** wird die
 * gemeinsame Beschreibung so verändert, dass gesperrt wie frei aussieht, müssen Zusagen auf
 * **allen** Flächen rot werden — nicht nur auf einer. **Nur das beweist, dass sie wirklich aus
 * einer Quelle lesen** und nicht bloß zufällig dieselben Zahlen tragen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  GESPERRT_DECKKRAFT, GESPERRT_ZEIGER, GESPERRT_GRUND, GESPERRT_ICON, GESPERRT_BESCHRIFTUNG,
} from '../app/dashboard/gesperrtStil';
import { opKnopfBild } from '../app/dashboard/opKnopfZustand';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const lies = (p: string): string => ohneKommentare(readFileSync(join(hier, '../app/', p), 'utf8'));

/** Die fünf Flächen, die den gesperrten Zustand darstellen — Stelle für Stelle aus der Inventur. */
const FLAECHEN = [
  { name: 'Werkzeug-Navi', datei: 'HausplanerApp.tsx', muster: /opacity: GESPERRT_DECKKRAFT, cursor: GESPERRT_ZEIGER/ },
  { name: 'Palette-Eintrag', datei: 'HausplanerApp.tsx', muster: /eintrag\.enabled \? FARBEN\.text : GESPERRT_BESCHRIFTUNG/ },
  { name: 'Geschoss löschen', datei: 'dashboard/GeschossFlaeche.tsx', muster: /GESPERRT_DECKKRAFT : 1, cursor: s\.anzahl <= 1 \? GESPERRT_ZEIGER/ },
  { name: 'Menü-Eintrag', datei: 'dashboard/WerkzeugGruppenMenue.tsx', muster: /zustand\.enabled \? 1 : GESPERRT_DECKKRAFT/ },
  { name: 'Berechnen', datei: 'EngineFlaeche.tsx', muster: /background: fehlt\.length > 0 \? GESPERRT_GRUND/ },
  { name: 'Fachfeld', datei: 'FachFlaeche.tsx', muster: /background: GESPERRT_GRUND/ },
  // Sechste Fläche: stand nicht in der Inventur, beim Messen im Browser aufgefallen.
  { name: 'Speichern', datei: 'HausplanerApp.tsx', muster: /background: anzeige\.gesperrt \? GESPERRT_GRUND : T\.brand/ },
] as const;

// --- K3: eine Quelle ----------------------------------------------------------------------------
test('K3: die Werte stammen aus `opKnopfBild` — diese Datei erfindet keinen einzigen', () => {
  const bild = opKnopfBild(false, true);
  assert.equal(GESPERRT_DECKKRAFT, bild.deckkraft);
  assert.equal(GESPERRT_ZEIGER, bild.cursor);
  assert.equal(GESPERRT_DECKKRAFT, 0.6, 'die eine Zahl — vorher standen 0,4 · 0,45 · 0,6 nebeneinander');
  assert.equal(GESPERRT_ZEIGER, 'not-allowed');
});

test('K3: keine Fläche legt Deckkraft, Grund oder Textfarbe für den gesperrten Zustand selbst fest', () => {
  for (const f of FLAECHEN) {
    const q = lies(f.datei);
    assert.match(q, f.muster, `${f.name}: liest nicht aus der Quelle`);
  }
  // Und die alten Zahlen sind nirgends mehr im Spiel.
  for (const datei of ['HausplanerApp.tsx', 'dashboard/GeschossFlaeche.tsx', 'dashboard/WerkzeugGruppenMenue.tsx']) {
    assert.doesNotMatch(lies(datei), /opacity: [^,}]*0\.4\d?\b/, `${datei}: alte Sperr-Deckkraft`);
  }
});

test('K5: die 0,4-gegen-0,6-Spaltung ist aufgelöst — und die 0,45 gleich mit', () => {
  // Die fünfte Stelle stand nicht in der Inventur; sie ist beim Messen aufgefallen.
  assert.doesNotMatch(lies('dashboard/WerkzeugGruppenMenue.tsx'), /0\.45/);
  assert.doesNotMatch(lies('dashboard/GeschossFlaeche.tsx'), /0\.4\b/);
  assert.doesNotMatch(lies('HausplanerApp.tsx'), /opacity: 0\.4\b/);
});

// --- K4: die Icon-Zeile bleibt Wort für Wort ----------------------------------------------------
test('K4: die vier gemessenen Werte der Icon-Zeile sind unverändert', () => {
  // Sie ist zweimal abgenommen (AUF-59, AUF-70). Ändert sich EIN Wert, ist dieser Posten rot.
  const b = opKnopfBild(false, true);
  assert.deepEqual(
    { deckkraft: b.deckkraft, cursor: b.cursor, grund: b.grundToken, icon: b.iconToken, rahmen: b.rahmenToken },
    { deckkraft: 0.6, cursor: 'not-allowed', grund: 'hair2', icon: 'faint', rahmen: null },
  );
  // Und der freie Zustand ebenso — sonst verschöbe sich der Unterschied.
  const f = opKnopfBild(false, false);
  assert.deepEqual({ d: f.deckkraft, c: f.cursor, g: f.grundToken, i: f.iconToken }, { d: 1, c: 'pointer', g: 'surface', i: 'ink' });
});

test('K4: `opKnopfZustand` bleibt token-rein — die Übersetzung liegt woanders', () => {
  const regel = lies('dashboard/opKnopfZustand.ts');
  assert.doesNotMatch(regel, /#[0-9a-fA-F]{3,8}\b|rgba?\(/);
  assert.doesNotMatch(regel, /\bT\./, 'sonst wäre die Beschreibung an eine Farbtabelle gekettet');
});

// --- Die begründete Variante --------------------------------------------------------------------
test('zwei Textfarben, und die zweite ist gemessen begründet — nicht erfunden', () => {
  // Ein Bildzeichen darf verblassen; eine Beschriftung muss lesbar bleiben.
  assert.equal(GESPERRT_ICON, '#a7aeb7', 'faint — für Bildzeichen');
  assert.equal(GESPERRT_BESCHRIFTUNG, '#697079', 'muted — für Text');
  assert.notEqual(GESPERRT_ICON, GESPERRT_BESCHRIFTUNG);

  const kontrast = (a: string, b: string): number => {
    const lum = (h: string): number => {
      const v = [1, 3, 5].map((i) => parseInt(h.slice(i, i + 2), 16) / 255)
        .map((c) => (c <= 0.03928 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4));
      return 0.2126 * v[0] + 0.7152 * v[1] + 0.0722 * v[2];
    };
    const [hi, lo] = [lum(a), lum(b)].sort((x, y) => y - x);
    return (hi + 0.05) / (lo + 0.05);
  };
  // Genau die Messung, die die Variante rechtfertigt.
  assert.ok(kontrast(GESPERRT_ICON, GESPERRT_GRUND) < 2.5, 'faint auf hair2 ist zum Lesen zu schwach');
  assert.ok(kontrast(GESPERRT_BESCHRIFTUNG, GESPERRT_GRUND) > 4.4, 'muted trägt eine Beschriftung');
});

test('die Variante kommt aus derselben Datei — es gibt keine zweite Quelle', () => {
  const quelle = lies('dashboard/gesperrtStil.ts');
  assert.match(quelle, /import \{ opKnopfBild \} from '\.\/opKnopfZustand'/, 'sie liest die eine Beschreibung');
  assert.match(quelle, /const GESPERRT = opKnopfBild\(false, true\)/);
  // Keine Fläche darf sich ihre eigene Sperrfarbe bauen.
  for (const f of FLAECHEN) {
    assert.doesNotMatch(lies(f.datei), /cursor: 'not-allowed'/, `${f.name}: eigener Zeiger statt der Quelle`);
  }
});

// --- K6: nicht allein über Farbe ----------------------------------------------------------------
test('K6: jede gesperrte Fläche trägt ein nicht-farbliches, nicht zeigerabhängiges Merkmal', () => {
  // Ein Mauszeiger existiert für Tastatur- und Touch-Bedienung nicht.
  const belege: Array<[string, string, RegExp]> = [
    ['Werkzeug-Navi', 'HausplanerApp.tsx', /aria-disabled=\{!zustand\.enabled\}/],
    ['Palette-Eintrag', 'HausplanerApp.tsx', /aria-disabled=\{!eintrag\.enabled\}/],
    ['Geschoss löschen', 'dashboard/GeschossFlaeche.tsx', /disabled=\{s\.anzahl <= 1\}/],
    ['Menü-Eintrag', 'dashboard/WerkzeugGruppenMenue.tsx', /aria-disabled=\{!zustand\.enabled\}/],
    ['Berechnen', 'EngineFlaeche.tsx', /disabled=\{fehlt\.length > 0\}/],
    ['Fachfeld', 'FachFlaeche.tsx', /readOnly disabled/],
    ['Speichern', 'HausplanerApp.tsx', /disabled=\{anzeige\.gesperrt\}/],
  ];
  for (const [name, datei, muster] of belege) {
    assert.match(lies(datei), muster, `${name}: kein Zustandsattribut — nur Farbe und Zeiger`);
  }
});

test('K6: der Menü-Eintrag hat sein Attribut in diesem Posten BEKOMMEN', () => {
  // Er war die einzige der sechs Flächen ohne Zustandsattribut; seine Sperre stand nur im Text
  // („◌ gesperrt") und in der Deckkraft. Beides bleibt — das Attribut kommt dazu.
  const q = lies('dashboard/WerkzeugGruppenMenue.tsx');
  assert.match(q, /role="menuitem" aria-disabled=\{!zustand\.enabled\}/);
  assert.match(q, /gesperrt/, 'der Text bleibt — er war nie das Problem');
});
