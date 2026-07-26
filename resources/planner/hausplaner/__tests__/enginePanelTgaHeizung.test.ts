/**
 * AUF-52 Scheibe B (tga-heizung) — **zwei von drei angeschlossen.**
 *
 * Wie in Scheibe A steht der Gegenbeweis im Mittelpunkt: die Engine-Eingabe wird **von Hand**
 * geschrieben und gegen das gestellt, was die Flaeche liefert. *Ein Vergleich, der beide Seiten
 * durch denselben Uebersetzer schickt, beweist nichts* — genau daran ist mein erster Anlauf in
 * Scheibe A gescheitert.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { enginePanel, startwerte, fehlendePflichtfelder, alsFbhEingabe, ENGINE_PANELS } from '../app/dashboard/enginePanels';
import { fbhAuslegung } from '../geometry/fbhAuslegung';
import { bewerteDeckung } from '../geometry/heizkoerperLeistung';
import { FAEHIGKEITEN } from '../app/tools/faehigkeiten';

const hier = dirname(fileURLToPath(import.meta.url));
const fbh = enginePanel('engine-fbh')!;
const hk = enginePanel('engine-heizkoerper')!;

// --- K8-Gegenbeweis: handgeschriebene Engine-Eingabe -------------------------------------------------
test('K8 FBH: die Feldzuordnung gegen eine HANDGESCHRIEBENE Eingabe', () => {
  const werte = { flaeche: '20', heizlast: '1400', verlegeabstand: '100', sperrflaeche: '2', maxKreisLaenge: '80', anbindungProKreis: '4' };
  const erwartet = fbhAuslegung({ flaeche: 20, heizlast: 1400, verlegeabstand: 100, sperrflaeche: 2, maxKreisLaenge: 80, anbindungProKreis: 4 });
  assert.deepEqual(fbh.berechne(werte) as unknown, erwartet as unknown);
});

test('K8 FBH, zweiter Satz mit Vorgaben der Engine — leere Felder werden weggelassen', () => {
  const werte = { flaeche: '35', heizlast: '2600', verlegeabstand: '', sperrflaeche: '', maxKreisLaenge: '', anbindungProKreis: '' };
  const erwartet = fbhAuslegung({ flaeche: 35, heizlast: 2600 });
  assert.deepEqual(fbh.berechne(werte) as unknown, erwartet as unknown);
  assert.ok(!('verlegeabstand' in alsFbhEingabe(werte)), 'eine 0 waere eine erfundene Angabe');
});

test('K8 Heizkoerper: dieselbe Probe, drei Temperaturen und der Exponent', () => {
  const werte = { normLeistung: '1500', raumheizlast: '1200', vorlauf: '55', ruecklauf: '45', raumtemp: '20', n: '1.3', normUebertemperatur: '50' };
  const roh = bewerteDeckung(1500, 1200, { vorlauf: 55, ruecklauf: 45, raumtemp: 20, n: 1.3, normUebertemperatur: 50 });
  const ausPanel = hk.berechne(werte) as unknown as Record<string, unknown>;
  assert.equal(ausPanel.betriebsLeistung, roh.betriebsLeistung);
  assert.equal(ausPanel.deckungsgrad, roh.deckungsgrad);
  assert.equal(ausPanel.hinweis, roh.hinweis);
});

// --- Die Umbenennung, offen ausgewiesen ---------------------------------------------------------------
test('`ausreichend` wird zu `bestanden` UMBENANNT — der Wert bleibt unveraendert', () => {
  // Die Huelle liest `bestanden`, die Engine nennt es `ausreichend`. Durchgereicht, nicht entschieden.
  for (const [normLeistung, raumheizlast] of [[1500, 1200], [800, 2000]] as const) {
    const werte = { normLeistung: String(normLeistung), raumheizlast: String(raumheizlast), vorlauf: '55', ruecklauf: '45', raumtemp: '20', n: '', normUebertemperatur: '' };
    const roh = bewerteDeckung(normLeistung, raumheizlast, { vorlauf: 55, ruecklauf: 45, raumtemp: 20 });
    const ausPanel = hk.berechne(werte) as unknown as { bestanden: boolean; ausreichend: boolean };
    assert.equal(ausPanel.bestanden, roh.ausreichend, 'derselbe Wert unter dem Namen der Huelle');
    assert.equal(ausPanel.ausreichend, roh.ausreichend, 'und das Original bleibt stehen');
  }
});

test('ein zu kleiner Heizkoerper faellt durch — sonst prueft die Zusage nur den guten Tag', () => {
  const werte = { normLeistung: '800', raumheizlast: '2000', vorlauf: '55', ruecklauf: '45', raumtemp: '20', n: '', normUebertemperatur: '' };
  const r = hk.berechne(werte) as unknown as { bestanden: boolean; deckungsgrad: number };
  assert.equal(r.bestanden, false);
  assert.ok(r.deckungsgrad < 100, `Deckungsgrad ${r.deckungsgrad}`);
});

test('FBH: ein Fall mit verletztem Pruefpunkt bleibt ein gueltiges Ergebnis mit Zahlen', () => {
  // `bestanden: false` ist kein Fehlerbildschirm — die Zahlen bleiben stehen.
  const r = fbh.berechne({ flaeche: '60', heizlast: '9000', verlegeabstand: '300', sperrflaeche: '', maxKreisLaenge: '', anbindungProKreis: '' }) as unknown as { bestanden: boolean; pruefungen: unknown[]; rohrlaengeGesamt: number };
  assert.ok(Array.isArray(r.pruefungen) && r.pruefungen.length > 0, 'die Engine liefert Pruefpunkte');
  assert.ok(r.rohrlaengeGesamt > 0, 'die Zahlen bleiben da');
});

// --- Operanden-Gate ---------------------------------------------------------------------------------
test('beide Flaechen rechnen nicht, solange ein Pflichtfeld fehlt', () => {
  assert.deepEqual(fehlendePflichtfelder(fbh, { ...startwerte(fbh), heizlast: '' }).map((f) => f.schluessel), ['heizlast']);
  assert.deepEqual(fehlendePflichtfelder(hk, { ...startwerte(hk), vorlauf: '' }).map((f) => f.schluessel), ['vorlauf']);
});

// --- K3 / K5 ------------------------------------------------------------------------------------------
test('K3: in den beiden neuen Flaechen steht keine Rechnung', () => {
  const roh = readFileSync(join(hier, '../app/dashboard/enginePanels.ts'), 'utf8')
    .replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '')
    .replace(/'(?:[^'\\]|\\.)*'/g, "''").replace(/"(?:[^"\\]|\\.)*"/g, '""');
  assert.doesNotMatch(roh, /Math\.(round|ceil|floor|pow|sqrt|cos|sin|tan)/);
  assert.doesNotMatch(roh, /import\(/);
});

// --- K6 / K7 ------------------------------------------------------------------------------------------
test('K6: verfuegbare Engines und Flaechen — exakt gleich', () => {
  const verfuegbar = FAEHIGKEITEN.filter((f) => f.art === 'engine' && f.zustand === 'verfuegbar');
  assert.equal(verfuegbar.length, ENGINE_PANELS.length);
  assert.equal(verfuegbar.length, 8);
});

test('K7: `engine-heizkreis` ist zurueckgegeben — Listeneingang, wie die zwei Holz-Engines', () => {
  const e = FAEHIGKEITEN.find((f) => f.id === 'engine-heizkreis')!;
  assert.equal(e.zustand, 'in_entwicklung');
  assert.equal(enginePanel('engine-heizkreis'), undefined);
});
