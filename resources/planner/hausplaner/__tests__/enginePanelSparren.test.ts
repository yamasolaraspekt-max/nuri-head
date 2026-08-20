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
/**
 * **BERICHTIGT 20.08. — dieser Test ist in genau die Falle gelaufen, die er beschreibt.**
 * Der Kopf oben sagt richtig: *ein Vergleich, der beide Seiten durch denselben Defekt schickt,
 * beweist nichts.* Die handgeschriebene Engine-Eingabe trug trotzdem `schneezone: '2'` als
 * ZEICHENKETTE — dieselbe Form, die der Panel-Pfad lieferte. Beide Seiten fielen in den
 * Zone-3-Zweig, die Zahlen stimmten ueberein, und der Test blieb gruen, waehrend die Flaeche
 * 'Zone 1' anzeigte und Zone 3 rechnete. Jetzt steht hier eine ZAHL, wie der Typ es verlangt.
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
    breiteMm: 80, hoeheMm: 200, schneezone: 2, gelaendehoeheM: 300, holzklasse: 'C24',
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
    breiteMm: 60, hoeheMm: 140, schneezone: 3, gelaendehoeheM: 800,
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
  assert.equal(verfuegbar.length, 8, 'nach Scheibe C: acht von dreizehn angeschlossen');
});

// --- K7: die drei Rueckgaben sind benannt und schlafen weiter -------------------------------------------
test('K7: die drei zurueckgegebenen Engines bleiben `in_entwicklung` — mit Grund, ohne Vertroestung', () => {
  for (const id of ['engine-holzmengen', 'engine-holzbauteile', 'engine-schifter', 'engine-heizkreis', 'engine-uwert']) {
    const e = FAEHIGKEITEN.find((f) => f.id === id)!;
    assert.equal(e.zustand, 'in_entwicklung', `${id} darf nicht auf Vorrat verfuegbar stehen`);
    assert.equal(enginePanel(id), undefined, `${id} hat keine Flaeche`);
    assert.ok((e.funktion ?? '').length > 10, `${id}: Grund zu duenn`);
    for (const wort of ['folgt', 'in Kürze', 'demnächst']) {
      assert.ok(!(e.funktion ?? '').includes(wort), `${id}: „${wort}" ist eine Vertroestung`);
    }
  }
});

/**
 * **WAECHTER zum Befund vom 20.08. — die drei Zonen muessen sich unterscheiden.**
 *
 * Der Defekt war nicht, dass eine Zahl falsch war, sondern dass **alle drei Zonen dieselbe Zahl
 * ergaben**: das Auswahlfeld liefert Zeichenketten, `Schneezone` ist `1|2|3`, der Vergleich in
 * `sparrenBerechnung.ts:36` ist `===`. Jede Auswahl fiel in den letzten Zweig.
 *
 * Deshalb prueft dieser Test nicht einen Wert, sondern die **Unterscheidbarkeit** — das ist die
 * Eigenschaft, die verloren ging. Ein Test, der nur Zone 3 geprueft haette, waere gruen geblieben.
 *
 * Die Zahlen sind von Hand nach DIN EN 1991-1-3/NA gerechnet, A = 300 m:
 *   t = (300+140)/760 = 0,578947 · t^2 = 0,335180
 *   Zone 1: 0,19 + 0,91*t^2 = 0,495 -> Mindestwert 0,65 greift
 *   Zone 2: 0,25 + 1,91*t^2 = 0,890
 *   Zone 3: 0,31 + 2,91*t^2 = 1,285
 */
test('Waechter: die drei Schneelastzonen ergeben DREI verschiedene Ergebnisse', () => {
  const basis = { ...startwerte(panel), gebaeudebreiteM: '10', neigungGrad: '38',
    sparrenabstandM: '0.8', breiteMm: '80', hoeheMm: '200', gelaendehoeheM: '300' };
  const eins  = alsSparrenEingabe({ ...basis, schneezone: '1' });
  const zwei  = alsSparrenEingabe({ ...basis, schneezone: '2' });
  const drei  = alsSparrenEingabe({ ...basis, schneezone: '3' });

  // Erst die Wandlung selbst: der Cast hat sie vorgetaeuscht.
  assert.equal(eins.schneezone, 1, 'Zone 1 muss als ZAHL 1 ankommen, nicht als Text');
  assert.equal(zwei.schneezone, 2);
  assert.equal(drei.schneezone, 3);

  // Dann die Wirkung — die eigentliche Zusage.
  const s1 = berechneSparren(eins).bodenschneelastKnM2;
  const s2 = berechneSparren(zwei).bodenschneelastKnM2;
  const s3 = berechneSparren(drei).bodenschneelastKnM2;
  assert.equal(s1, 0.65, 'Zone 1 bei A=300 m: Mindestwert 0,65 kN/m2');
  assert.ok(s1 < s2 && s2 < s3, `drei Zonen muessen streng steigen, gemessen ${s1}/${s2}/${s3}`);
});

/**
 * **Gegenprobe zum Waechter:** das Auswahlfeld bietet nur Zonen an, die auch gerechnet werden.
 * 1a und 2a wurden am 20.08. entfernt — ihr NA-Zuschlag ist ein Normwert, der nicht gesetzt wurde.
 * Ohne diese Zusage koennte jemand sie zurueckstellen, und sie fielen still auf Zone 3.
 */
test('Waechter: jede angebotene Schneelastzone wird auch gerechnet', () => {
  const feld = panel.felder.find((f) => f.schluessel === 'schneezone')!;
  const angeboten = (feld.auswahl ?? []).map((a) => a.wert);
  assert.deepEqual(angeboten, ['1', '2', '3']);
  for (const w of angeboten) {
    const zone = alsSparrenEingabe({ ...startwerte(panel), schneezone: w }).schneezone;
    assert.equal(String(zone), w, `angeboten '${w}', gerechnet ${zone}`);
  }
});
