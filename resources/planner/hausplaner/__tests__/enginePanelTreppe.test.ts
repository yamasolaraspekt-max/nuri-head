/**
 * AUF-33 / L2 — die Treppen-Fläche ist das Muster für die übrigen zwölf.
 *
 * Der Auftrag sagt, warum dieser Test streng sein muss: *„Eine Fläche, die dreizehnmal
 * wiederverwendet wird. Ein Muster, das hier schief steht, steht dreizehnmal schief."*
 *
 * Geprüft wird deshalb vor allem eines: **die Fläche rechnet nicht.** Sie reicht Werte an die
 * Engine und zeigt deren Rückgabe. Wertgleichheit (K4), alle drei Schweregrade mit einem
 * Unterschied, der nicht nur farblich ist (K5), kein dynamischer Import (K6), genau eine
 * verfügbare Engine (K7) — und keine Rechnung, kein Grenzwert, keine Rundung in der Fläche (K3).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  ENGINE_PANELS, enginePanel, startwerte, fehlendePflichtfelder, alsTreppenEingabe,
} from '../app/dashboard/enginePanels';
import { berechneTreppe } from '../geometry/treppenBerechnung';
import { FAEHIGKEITEN } from '../app/tools/faehigkeiten';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const flaecheQuelle = ohneKommentare(readFileSync(join(hier, '../app/EngineFlaeche.tsx'), 'utf8'));
const panelQuelle = ohneKommentare(readFileSync(join(hier, '../app/dashboard/enginePanels.ts'), 'utf8'));

const TREPPE = enginePanel('engine-treppe')!;

// --- L2 ist L2: genau ein Panel -----------------------------------------------------------------
test('L2: genau EIN Engine-Panel — das Muster wird erst abgenommen, dann kopiert', () => {
  assert.equal(ENGINE_PANELS.length, 1);
  assert.equal(ENGINE_PANELS[0].engineId, 'engine-treppe');
  assert.equal(enginePanel('engine-fbh'), undefined, 'L3 ist nicht Teil dieses Auftrags');
});

test('K7: genau eine der 13 Engines steht auf `verfuegbar`, und sie hat auch ein Panel', () => {
  const engines = FAEHIGKEITEN.filter((f) => f.art === 'engine');
  const verfuegbar = engines.filter((e) => e.zustand === 'verfuegbar');
  assert.equal(verfuegbar.length, 1);
  assert.ok(enginePanel(verfuegbar[0].id), 'eine verfügbare Engine ohne Fläche wäre ein falsches Versprechen');
  // und umgekehrt: kein Panel ohne verfügbare Engine
  for (const p of ENGINE_PANELS) {
    assert.equal(FAEHIGKEITEN.find((f) => f.id === p.engineId)?.zustand, 'verfuegbar');
  }
});

// --- K4: Wertgleichheit gegen die Engine --------------------------------------------------------
const FAELLE: Array<{ name: string; werte: Record<string, string> }> = [
  { name: 'Standardfall Wohnung', werte: { geschosshoehe: '2800', gewuenschteSteigung: '175', laufbreite: '1000', durchgangshoehe: '2000', bereich: 'wohnung', verfuegbareLauflaenge: '' } },
  { name: 'enge Lauflänge', werte: { geschosshoehe: '2600', gewuenschteSteigung: '180', verfuegbareLauflaenge: '2400', laufbreite: '900', durchgangshoehe: '2000', bereich: 'wohnung' } },
  { name: 'Außentreppe, scharfe Grenzmaße', werte: { geschosshoehe: '3000', gewuenschteSteigung: '190', laufbreite: '800', durchgangshoehe: '1900', bereich: 'aussen', verfuegbareLauflaenge: '' } },
];

for (const fall of FAELLE) {
  test(`K4 Wertgleichheit — ${fall.name}: die Fläche zeigt exakt, was die Engine liefert`, () => {
    const ausDerFlaeche = TREPPE.berechne(fall.werte);
    const zahl = (k: string): number | undefined => {
      const roh = (fall.werte[k] ?? '').trim();
      return roh === '' ? undefined : Number(roh);
    };
    const direkt = berechneTreppe({
      geschosshoehe: Number(fall.werte.geschosshoehe),
      ...(zahl('gewuenschteSteigung') !== undefined ? { gewuenschteSteigung: zahl('gewuenschteSteigung') } : {}),
      ...(zahl('verfuegbareLauflaenge') !== undefined ? { verfuegbareLauflaenge: zahl('verfuegbareLauflaenge') } : {}),
      ...(zahl('laufbreite') !== undefined ? { laufbreite: zahl('laufbreite') } : {}),
      ...(zahl('durchgangshoehe') !== undefined ? { durchgangshoehe: zahl('durchgangshoehe') } : {}),
      ...(fall.werte.bereich ? { bereich: fall.werte.bereich as 'wohnung' | 'gebaeude' | 'aussen' } : {}),
    });
    assert.deepEqual(ausDerFlaeche, direkt, 'die Fläche darf nichts nachrechnen und nichts nachrunden');
    // jede der acht Ergebniszahlen ist auch wirklich vorhanden
    for (const f of TREPPE.ergebnisFelder) {
      assert.ok(f.schluessel in ausDerFlaeche, `${f.schluessel} fehlt im Ergebnis`);
    }
  });
}

test('K4: mindestens einer der Fälle ist `bestanden: false` — sonst prüft der Test nur den Sonnenschein', () => {
  const ergebnisse = FAELLE.map((f) => TREPPE.berechne(f.werte));
  assert.ok(ergebnisse.some((e) => e.bestanden === false), 'kein Fall verletzt eine Prüfung');
  // und dort bleiben die Zahlen stehen: `bestanden: false` ist kein Fehlerbildschirm
  const durchgefallen = ergebnisse.find((e) => e.bestanden === false)!;
  assert.ok(durchgefallen.anzahlSteigungen > 0);
  assert.ok(durchgefallen.schrittmass > 0, 'auch bei nicht bestanden liefert die Engine Zahlen');
});

// --- K5: alle drei Schweregrade, Unterschied nicht nur farblich ---------------------------------
test('K5: die Fläche kennt alle drei Schweregrade — Fehler/Warnung tragen Zeichen UND Wort', () => {
  const alle = FAELLE.flatMap((f) => TREPPE.berechne(f.werte).pruefungen);
  const schweren = new Set(alle.map((p) => p.schwere));
  // GEMESSEN, und im Bericht zurückgegeben: `berechneTreppe` liefert ausschliesslich `fehler` und
  // `warnung`. `info` ist im Typ vorgesehen, kommt in DIESER Engine aber nicht vor. Der Test
  // behauptet deshalb nicht, alle drei kämen aus den Daten — er prüft, dass beide auftretenden
  // Grade wirklich auftreten UND dass die Fläche auch den dritten darstellen könnte.
  for (const s of ['warnung', 'fehler']) {
    assert.ok(schweren.has(s as 'warnung' | 'fehler'), `Schweregrad ${s} kommt in keinem Fall vor`);
  }
  assert.ok(!schweren.has('info'), 'wenn die Engine eines Tages `info` liefert, gehört dieser Test angepasst');
  // Die Fläche unterscheidet sie nicht nur über Farbe (WCAG 1.4.1): Zeichen + Wort stehen im Code.
  assert.match(flaecheQuelle, /fehler:\s*\{\s*zeichen:\s*'✕',\s*wort:\s*'Fehler'/);
  assert.match(flaecheQuelle, /warnung:\s*\{\s*zeichen:\s*'⚠',\s*wort:\s*'Warnung'/);
  assert.match(flaecheQuelle, /info:\s*\{\s*zeichen:\s*'ℹ',\s*wort:\s*'Hinweis'/);
  assert.match(flaecheQuelle, /\{a\.zeichen\} \{a\.wort\}/, 'Zeichen und Wort werden auch gerendert');
});

// --- K3: keine Rechnung in der Fläche -----------------------------------------------------------
test('K3: in der Fläche steht keine Rechnung — kein Grenzwert, keine Formel, keine Rundung', () => {
  for (const [name, quelle] of [['EngineFlaeche.tsx', flaecheQuelle], ['enginePanels.ts', panelQuelle]] as const) {
    assert.doesNotMatch(quelle, /Math\.round|Math\.floor|Math\.ceil|Math\.max|Math\.min/, `${name}: Rundung/Begrenzung gehört in die Engine`);
    assert.doesNotMatch(quelle, /\bGRENZEN\b|DURCHGANG_MIN|steigungMax|auftrittMin|laufbreiteMin/, `${name}: Grenzmaß in der Fläche`);
    assert.doesNotMatch(quelle, /\* 2 \+|\/ 10\b/, `${name}: Formel in der Fläche`);
  }
  // Die Fläche ruft die Engine — sie ersetzt sie nicht.
  assert.match(flaecheQuelle, /panel\.berechne\(werte\)/);
  assert.equal((panelQuelle.match(/berechneTreppe\(/g) ?? []).length, 1, 'genau ein Aufruf der Engine');
});

test('K6: kein dynamischer Import — der Bundler sieht die Engine', () => {
  assert.doesNotMatch(panelQuelle, /import\s*\(/, 'import(variable) überlebt das Bundling nicht zuverlässig');
  assert.doesNotMatch(flaecheQuelle, /import\s*\(/);
  assert.match(panelQuelle, /^import \{ berechneTreppe/m, 'statischer Import');
});

// --- Operanden-Gate: ohne Pflichtangabe wird nicht gerechnet -------------------------------------
test('ohne Geschosshöhe wird nicht gerechnet — und es steht da, was fehlt', () => {
  const leer = { ...startwerte(TREPPE), geschosshoehe: '' };
  const fehlt = fehlendePflichtfelder(TREPPE, leer);
  assert.deepEqual(fehlt.map((f) => f.schluessel), ['geschosshoehe']);
  assert.deepEqual(fehlendePflichtfelder(TREPPE, startwerte(TREPPE)), [], 'mit Vorbelegung fehlt nichts');
  // Der Knopf ist dann gesperrt, und der Grund steht als Text in der Fläche, nicht nur im Tooltip.
  assert.match(flaecheQuelle, /disabled=\{fehlt\.length > 0\}/);
  assert.match(flaecheQuelle, /Ohne diese Angabe wird nicht gerechnet/);
});

test('leere Felder werden weggelassen, nicht auf 0 gesetzt — eine 0 wäre eine erfundene Angabe', () => {
  // Gemessen am Ergebnis wäre das hier NICHT sichtbar: `berechneTreppe` behandelt 0 selbst als
  // „nicht angegeben" (`e.verfuegbareLauflaenge && > 0`). Geprüft wird deshalb die Abbildung —
  // dort entscheidet sich, ob eine Angabe erfunden wird. Bei einer Engine ohne diesen Schutz
  // wäre der Unterschied fachlich real.
  const eingabe = alsTreppenEingabe({ ...startwerte(TREPPE), verfuegbareLauflaenge: '' });
  assert.ok(!('verfuegbareLauflaenge' in eingabe), 'ein leeres Feld darf nicht als Wert ankommen');
  const mitWert = alsTreppenEingabe({ ...startwerte(TREPPE), verfuegbareLauflaenge: '2400' });
  assert.equal(mitWert.verfuegbareLauflaenge, 2400);
  // Unsinn („abc") ist ebenfalls keine Angabe, kein NaN.
  const unsinn = alsTreppenEingabe({ ...startwerte(TREPPE), laufbreite: 'abc' });
  assert.ok(!('laufbreite' in unsinn), 'NaN darf nie in die Engine gelangen');
});

// --- Die Beschriftungen: Klartext, nicht Feldnamen ----------------------------------------------
test('jede Ergebniszahl trägt Klartext — `schrittmass` heißt nicht „schrittmass"', () => {
  for (const f of TREPPE.ergebnisFelder) {
    assert.ok(f.label.length > 2, `${f.schluessel}: Label fehlt`);
    assert.notEqual(f.label, f.schluessel, `${f.schluessel}: der Feldname ist keine Beschriftung`);
  }
  const schrittmass = TREPPE.ergebnisFelder.find((f) => f.schluessel === 'schrittmass');
  assert.match(schrittmass?.label ?? '', /Schrittmaß/);
  assert.match(schrittmass?.label ?? '', /Steigung/, 'die Formel steht im Klartext-Label, nicht im Code');
});

test('jedes Eingabefeld nennt Einheit oder Auswahl und trägt einen Schlüssel, den die Engine kennt', () => {
  const bekannt = ['geschosshoehe', 'gewuenschteSteigung', 'verfuegbareLauflaenge', 'laufbreite', 'durchgangshoehe', 'bereich'];
  for (const f of TREPPE.felder) {
    assert.ok(bekannt.includes(f.schluessel), `${f.schluessel} kennt die Engine nicht`);
    assert.ok(f.einheit || f.auswahl, `${f.schluessel}: weder Einheit noch Auswahl`);
  }
  assert.equal(TREPPE.felder.filter((f) => f.pflicht).length, 1, 'genau eine Pflichtangabe, wie in der Engine');
});

/**
 * Befund aus der eigenen Sichtprobe: Die erste Fassung zeigte **jede** Prüfzeile mit ihrem
 * Schweregrad — auch die bestandenen. In der Außentreppe stand dann „✕ Fehler Laufbreite 1000 mm
 * ≥ Mindestmaß 1000 mm", obwohl genau diese Prüfung **bestanden** war. Der Schweregrad sagt, wie
 * schwer eine Verletzung wöge; ob sie vorliegt, sagt `bestanden`. Beides zu vermischen macht aus
 * einer erfüllten Anforderung einen Fehler — im Muster, das L3 zwölfmal kopiert.
 */
test('eine bestandene Prüfung zeigt „✓ erfüllt", nicht ihren Schweregrad', () => {
  assert.match(flaecheQuelle, /p\.bestanden\s*\?\s*\{ zeichen: '✓', wort: 'erfüllt'/);
  // und die Daten geben den Fall wirklich her: bestandene UND verletzte Prüfung im selben Ergebnis
  const aussen = TREPPE.berechne(FAELLE[2].werte);
  assert.ok(aussen.pruefungen.some((p) => p.bestanden), 'kein bestandener Fall zum Anzeigen');
  assert.ok(aussen.pruefungen.some((p) => !p.bestanden), 'kein verletzter Fall zum Anzeigen');
});
