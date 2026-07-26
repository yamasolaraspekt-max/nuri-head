/**
 * AUF-52 Scheibe A — **die erste der vier dach-zimmerei-Engines am abgenommenen Muster.**
 *
 * **Die Eigenschaft, die dieser Test schuetzt, ist AUF-33 §3a:** im Panel entsteht **keine Zahl**.
 * Kein Grenzwert, keine Formel, keine Rundung — jede Zahl kommt aus `berechneSparren`. Ein Defekt
 * hier vervielfachte sich ueber L3 zwoelffach.
 *
 * **Drei der vier Engines dieser Gruppe sind begruendet ZURUECKGEGEBEN**, nicht behelfsmaessig
 * angeschlossen (§4 des Auftrags: *nicht mit Platzhaltern fuellen*). Auch das steht hier als Zusage,
 * damit niemand sie spaeter still nachtraegt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { enginePanel, startwerte, fehlendePflichtfelder, alsSparrenEingabe, ENGINE_PANELS } from '../app/dashboard/enginePanels';
import { berechneSparren } from '../geometry/sparrenBerechnung';
import { FAEHIGKEITEN } from '../app/tools/faehigkeiten';

const hier = dirname(fileURLToPath(import.meta.url));
const panel = enginePanel('engine-sparren')!;

// --- K4: Wertgleichheit gegen die Engine ------------------------------------------------------------
const faelle: Array<[string, Record<string, string>]> = [
  ['Vorgabe', startwerte(panel)],
  ['knapper Querschnitt', { ...startwerte(panel), breiteMm: '60', hoeheMm: '140', gebaeudebreiteM: '12' }],
  ['hohe Schneelast', { ...startwerte(panel), schneezone: '3', gelaendehoeheM: '800' }],
];

for (const [name, werte] of faelle) {
  test(`K4 ${name}: die Flaeche zeigt genau das, was die Engine rechnet`, () => {
    const ausPanel = panel.berechne(werte) as unknown as Record<string, number | boolean>;
    const direkt = berechneSparren(alsSparrenEingabe(werte)) as unknown as Record<string, number | boolean>;
    assert.deepEqual(ausPanel, direkt, 'das Panel rechnet nicht nach — es reicht durch');
    for (const f of panel.ergebnisFelder) {
      assert.ok(f.schluessel in direkt, `${f.schluessel} kommt in der Engine gar nicht vor`);
    }
  });
}

/**
 * **Der eigentliche Gegenbeweis — und er hat gefehlt.**
 *
 * Mein erster K4-Test verglich `panel.berechne(werte)` mit
 * `berechneSparren(alsSparrenEingabe(werte))`. Beide Seiten liefen durch **denselben** Uebersetzer:
 * eine verfaelschte Feldzuordnung blieb damit gruen — die Mutation aus Kriterium 8 biss nicht.
 * *Ein Vergleich, der beide Seiten durch denselben Defekt schickt, beweist nichts.*
 *
 * Deshalb steht die Engine-Eingabe hier **von Hand geschrieben** daneben. Wandert ein Feld auf den
 * falschen Schluessel, weichen die Zahlen ab, und der Test faellt.
 */
test('K8-Gegenbeweis: die Feldzuordnung wird gegen eine HANDGESCHRIEBENE Engine-Eingabe geprueft', () => {
  const werte = {
    gebaeudebreiteM: '10', neigungGrad: '38', sparrenabstandM: '0.8',
    breiteMm: '80', hoeheMm: '200', schneezone: '2', gelaendehoeheM: '300',
    eigenlastKnM2: '', holzklasse: 'C24',
  };
  // Von Hand, nicht durch `alsSparrenEingabe` — genau darin liegt der Beweiswert.
  const erwartet = berechneSparren({
    gebaeudebreiteM: 10, neigungGrad: 38, sparrenabstandM: 0.8,
    breiteMm: 80, hoeheMm: 200, schneezone: '2', gelaendehoeheM: 300, holzklasse: 'C24',
  });
  assert.deepEqual(panel.berechne(werte) as unknown, erwartet as unknown);
});

test('K8-Gegenbeweis, zweiter Satz: andere Werte, damit keine Zufallsgleichheit traegt', () => {
  const werte = {
    gebaeudebreiteM: '12', neigungGrad: '25', sparrenabstandM: '0.6',
    breiteMm: '60', hoeheMm: '140', schneezone: '3', gelaendehoeheM: '800',
    eigenlastKnM2: '1.2', holzklasse: 'C30',
  };
  const erwartet = berechneSparren({
    gebaeudebreiteM: 12, neigungGrad: 25, sparrenabstandM: 0.6,
    breiteMm: 60, hoeheMm: 140, schneezone: '3', gelaendehoeheM: 800,
    eigenlastKnM2: 1.2, holzklasse: 'C30',
  });
  assert.deepEqual(panel.berechne(werte) as unknown, erwartet as unknown);
});

test('K4: mindestens ein Fall verletzt einen Nachweis — sonst prueft die Zusage nur den guten Tag', () => {
  const knapp = panel.berechne(faelle[1]![1]) as unknown as { bestanden: boolean; ausnutzungBiegung: number };
  assert.equal(knapp.bestanden, false, 'ein 60x140 ueber 12 m muesste durchfallen');
  assert.ok(knapp.ausnutzungBiegung > 1, `Ausnutzung ${knapp.ausnutzungBiegung}`);
});

// --- K3: keine Rechnung im Panel ---------------------------------------------------------------------
/**
 * **Gemessen wird CODE — ohne Kommentare und ohne Zeichenketten.**
 *
 * Beide Zusagen unten schlugen im ersten Lauf falsch an: einmal auf die Norm-Angabe
 * *„Durchbiegung L/300"* in einem Anzeigetext, einmal auf das Wort `import(` in einem Kommentar,
 * der genau erklaert, warum es keinen dynamischen Import gibt. **Ein Verbot, das den Beleg fuer
 * das Verbot trifft, prueft den Text und nicht den Code** — dieselbe Falle wie sechsmal zuvor in
 * diesem Zyklus, diesmal doppelt.
 */
const nurCode = (pfad: string): string =>
  readFileSync(join(hier, pfad), 'utf8')
    .replace(/\/\*[\s\S]*?\*\//g, '')
    .replace(/^\s*\/\/.*$/gm, '')
    .replace(/'(?:[^'\\]|\\.)*'/g, "''")
    .replace(/"(?:[^"\\]|\\.)*"/g, '""');

test('K3: im Panel steht keine Rechnung — kein Grenzwert, keine Formel, keine Rundung', () => {
  const roh = nurCode('../app/dashboard/enginePanels.ts');
  // Der Uebersetzer darf lesen und weiterreichen — mehr nicht.
  assert.doesNotMatch(roh, /Math\.(round|ceil|floor|pow|sqrt|cos|sin|tan)/, 'eine Rechnung im Panel');
  assert.doesNotMatch(roh, /[*/]\s*1000\b/, 'eine Einheitenumrechnung im Panel');
  // Zahlen duerfen nur als `vorgabe` vorkommen (Vorbelegung aus der Engine-Doku), nicht in Formeln.
  const zeilenMitZahl = roh.split('\n').filter((z) => /[^a-zA-Z_]\d+(\.\d+)?\s*[*+/-]\s*\d/.test(z));
  assert.deepEqual(zeilenMitZahl, [], `gerechnete Zahlen: ${zeilenMitZahl.join(' | ')}`);
});

test('K5: kein dynamischer Import — der Bundler muss das Ziel sehen', () => {
  assert.doesNotMatch(nurCode('../app/dashboard/enginePanels.ts'), /import\(/,
    'ein dynamischer Import ueberlebt das Bundling nicht zuverlaessig');
  assert.match(readFileSync(join(hier, '../app/dashboard/enginePanels.ts'), 'utf8'),
    /^import \{ berechneSparren/m, 'statisch importiert');
});

// --- Operanden-Gate ---------------------------------------------------------------------------------
test('leere Felder werden weggelassen, nicht auf 0 gesetzt — eine 0 waere eine erfundene Angabe', () => {
  const e = alsSparrenEingabe({ ...startwerte(panel), eigenlastKnM2: '' });
  assert.ok(!('eigenlastKnM2' in e), 'die Engine setzt ihre eigene Vorgabe');
});

test('die Flaeche rechnet nicht, solange ein Pflichtfeld fehlt', () => {
  const fehlt = fehlendePflichtfelder(panel, { ...startwerte(panel), gebaeudebreiteM: '' });
  assert.deepEqual(fehlt.map((f) => f.schluessel), ['gebaeudebreiteM']);
});

// --- K6: verfuegbar genau fuer das Gebaute -----------------------------------------------------------
test('K6: die Zahl der verfuegbaren Engines ist EXAKT die Zahl der angeschlossenen', () => {
  const verfuegbar = FAEHIGKEITEN.filter((f) => f.art === 'engine' && f.zustand === 'verfuegbar');
  assert.equal(verfuegbar.length, ENGINE_PANELS.length);
  assert.equal(verfuegbar.length, 4, 'nach Scheibe B: Treppe + Sparren + FBH + Heizkoerper');
});

// --- K7: die drei Rueckgaben sind benannt und schlafen weiter -------------------------------------------
test('K7: die drei zurueckgegebenen Engines bleiben `in_entwicklung` — mit Grund, ohne Vertroestung', () => {
  for (const id of ['engine-holzmengen', 'engine-holzbauteile', 'engine-schifter', 'engine-heizkreis']) {
    const e = FAEHIGKEITEN.find((f) => f.id === id)!;
    assert.equal(e.zustand, 'in_entwicklung', `${id} darf nicht auf Vorrat verfuegbar stehen`);
    assert.equal(enginePanel(id), undefined, `${id} hat keine Flaeche`);
    assert.ok((e.funktion ?? '').length > 10, `${id}: Grund zu duenn`);
    for (const wort of ['folgt', 'in Kürze', 'demnächst']) {
      assert.ok(!(e.funktion ?? '').includes(wort), `${id}: „${wort}" ist eine Vertroestung`);
    }
  }
});
