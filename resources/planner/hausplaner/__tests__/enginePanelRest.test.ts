/**
 * AUF-52 Scheibe C (der Rest) — **vier von fuenf angeschlossen.**
 *
 * Wie in A und B steht der Gegenbeweis auf **handgeschriebenen** Engine-Eingaben: beide Seiten des
 * Vergleichs laufen getrennt, sonst beweist er nichts.
 *
 * **`engine-uwert` ist zurueckgegeben** — `berechneUWert` nimmt eine **Liste von Schichten mit
 * Lambda**. Das Modell fuehrt seit AUF-76 zwar `WallNode.schichten`, aber als `{materialId, dickeMm}`
 * **ohne Lambda**: genau die Nichtdeckung, die ich in AUF-77 gemeldet habe. Aus Feldern eine
 * Schichtliste zu bauen hiesse, Waermeleitfaehigkeiten zu erfinden.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { enginePanel, startwerte, fehlendePflichtfelder, ENGINE_PANELS } from '../app/dashboard/enginePanels';
import { berechneUw } from '../geometry/fensterProdukt';
import { pruefeAbwasser } from '../geometry/abwassergefaelle';
import { bewerteArbeitsdreieck } from '../geometry/kuecheArbeitsdreieck';
import { pvSchnellBelegung } from '../geometry/pvBelegung';
import { FAEHIGKEITEN } from '../app/tools/faehigkeiten';

const uw = enginePanel('engine-fensterprodukt')!;
const abw = enginePanel('engine-abwasser')!;
const kueche = enginePanel('engine-kueche')!;
const pv = enginePanel('engine-pv')!;

// --- K8: handgeschriebene Eingaben, je Engine zwei Saetze ---------------------------------------------
test('K8 Fenster-Uw: gegen eine handgeschriebene Eingabe', () => {
  const werte = { breiteMm: '1200', hoeheMm: '1400', uf: '1.3', ug: '0.6', ansichtsbreiteMm: '70', psiRandverbund: '0.04' };
  assert.deepEqual(uw.berechne(werte) as unknown,
    berechneUw({ breiteMm: 1200, hoeheMm: 1400, uf: 1.3, ug: 0.6, ansichtsbreiteMm: 70, psiRandverbund: 0.04 }) as unknown);
  const ohnePsi = { ...werte, psiRandverbund: '' };
  assert.deepEqual(uw.berechne(ohnePsi) as unknown,
    berechneUw({ breiteMm: 1200, hoeheMm: 1400, uf: 1.3, ug: 0.6, ansichtsbreiteMm: 70 }) as unknown,
    'leer ⇒ die Engine setzt ihre Vorgabe');
});

test('K8 Abwasser: mit und ohne Gefaelle-Angabe', () => {
  assert.deepEqual(abw.berechne({ dn: '100', laenge: '8', gefaelle: '1.5' }) as unknown,
    pruefeAbwasser({ dn: 100, laenge: 8, gefaelle: 1.5 }) as unknown);
  assert.deepEqual(abw.berechne({ dn: '100', laenge: '8', gefaelle: '' }) as unknown,
    pruefeAbwasser({ dn: 100, laenge: 8 }) as unknown, 'ohne Angabe rechnet die Engine mit dem Mindestgefaelle');
});

test('K8 Kueche: sechs Koordinaten werden zu drei Punkten — nicht zu drei Annahmen', () => {
  const werte = { spueleX: '0', spueleY: '0', kochenX: '1800', kochenY: '0', kuehlenX: '900', kuehlenY: '2200' };
  assert.deepEqual(kueche.berechne(werte) as unknown,
    bewerteArbeitsdreieck({ spuele: { x: 0, y: 0 }, kochen: { x: 1800, y: 0 }, kuehlen: { x: 900, y: 2200 } }) as unknown);
});

test('K8 PV: gegen eine handgeschriebene Eingabe', () => {
  const werte = { dachLaenge: '10000', dachBreite: '6000', modulBreite: '1134', modulHoehe: '1762', modulLeistung: '440', randabstand: '300', modulabstand: '20' };
  assert.deepEqual(pv.berechne(werte) as unknown,
    pvSchnellBelegung({ dachLaenge: 10000, dachBreite: 6000, modulBreite: 1134, modulHoehe: 1762, modulLeistung: 440, randabstand: 300, modulabstand: 20 }) as unknown);
});

// --- Engines ohne Bestehens-Merkmal ------------------------------------------------------------------
test('Uw und PV liefern KEIN `bestanden` — und es wird auch keines erfunden', () => {
  // Sie rechnen Werte aus, sie bestehen nichts. Eine Plakette waere eine erfundene Bewertung.
  for (const p of [uw, pv]) {
    const r = p.berechne(startwerte(p)) as unknown as Record<string, unknown>;
    assert.equal('bestanden' in r, false, `${p.engineId} traegt ein erfundenes bestanden`);
  }
});

test('Abwasser und Kueche liefern eines — dort zeigt die Huelle es auch', () => {
  for (const p of [abw, kueche]) {
    const r = p.berechne(startwerte(p)) as unknown as Record<string, unknown>;
    assert.equal(typeof r.bestanden, 'boolean', `${p.engineId} sollte ein bestanden liefern`);
    assert.ok(Array.isArray(r.pruefungen));
  }
});

test('ein verletzter Pruefpunkt bleibt ein gueltiges Ergebnis mit Zahlen', () => {
  const r = abw.berechne({ dn: '100', laenge: '8', gefaelle: '0.2' }) as unknown as { bestanden: boolean; hoehenverlust: number };
  assert.equal(r.bestanden, false, '0,2 % unterschreitet das Mindestgefaelle');
  assert.ok(Number.isFinite(r.hoehenverlust), 'die Zahlen bleiben stehen');
});

// --- Operanden-Gate + Umfang ---------------------------------------------------------------------------
test('jede der vier rechnet nicht, solange ein Pflichtfeld fehlt', () => {
  for (const p of [uw, abw, kueche, pv]) {
    const erstesPflicht = p.felder.find((f) => f.pflicht)!;
    const fehlt = fehlendePflichtfelder(p, { ...startwerte(p), [erstesPflicht.schluessel]: '' });
    assert.deepEqual(fehlt.map((f) => f.schluessel), [erstesPflicht.schluessel], p.engineId);
  }
});

test('K6: acht verfuegbare Engines, acht Flaechen — exakt gleich', () => {
  const verfuegbar = FAEHIGKEITEN.filter((f) => f.art === 'engine' && f.zustand === 'verfuegbar');
  assert.equal(verfuegbar.length, ENGINE_PANELS.length);
  assert.equal(verfuegbar.length, 8);
});

test('K7: `engine-uwert` bleibt `in_entwicklung` — Schichtliste mit Lambda, die es im Modell nicht gibt', () => {
  const e = FAEHIGKEITEN.find((f) => f.id === 'engine-uwert')!;
  assert.equal(e.zustand, 'in_entwicklung');
  assert.equal(enginePanel('engine-uwert'), undefined);
});
