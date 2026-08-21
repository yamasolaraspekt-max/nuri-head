/**
 * Treppen-Berechnung (K3): Stufenzahl, Schrittmaß, Grenzmaße.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { berechneTreppe } from '../geometry/treppenBerechnung';

test('Standard-Wohnungstreppe 2600 mm: 15 Steigungen, bequemes Schrittmaß, bestanden', () => {
  const r = berechneTreppe({ geschosshoehe: 2600, gewuenschteSteigung: 175 });
  assert.equal(r.anzahlSteigungen, 15);
  assert.equal(r.anzahlAuftritte, 14);
  assert.ok(Math.abs(r.steigungshoehe - 173.3) < 0.2, `Steigung ${r.steigungshoehe}`);
  assert.ok(r.schrittmass >= 590 && r.schrittmass <= 650, `Schrittmaß ${r.schrittmass}`);
  assert.equal(r.bestanden, true);
});

test('zu steile Treppe wird als Fehler erkannt (Steigung > Grenze)', () => {
  const r = berechneTreppe({ geschosshoehe: 3000, gewuenschteSteigung: 215, bereich: 'wohnung' });
  const steig = r.pruefungen.find((x) => x.id === 'steigung-max')!;
  assert.equal(steig.bestanden, false);
  assert.equal(r.bestanden, false);
});

test('verfügbare Lauflänge bestimmt den Auftritt', () => {
  const r = berechneTreppe({ geschosshoehe: 2600, gewuenschteSteigung: 175, verfuegbareLauflaenge: 4200 });
  // 15 Steigungen -> 14 Auftritte -> 4200/14 = 300
  assert.equal(r.anzahlAuftritte, 14);
  assert.equal(r.auftritt, 300);
  assert.equal(r.lauflaenge, 4200);
});

test('Lauflänge ≈ Auftritte × Auftritt (Rundung ≤ 2 mm)', () => {
  const r = berechneTreppe({ geschosshoehe: 2600, gewuenschteSteigung: 175 });
  assert.ok(Math.abs(r.lauflaenge - r.anzahlAuftritte * r.auftritt) <= 2, `lauflaenge ${r.lauflaenge}`);
  assert.ok(r.lauflaenge > 0);
});

test('Laufbreite unter Mindestmaß → Fehler', () => {
  const r = berechneTreppe({ geschosshoehe: 2600, laufbreite: 700, bereich: 'wohnung' });
  const lb = r.pruefungen.find((x) => x.id === 'laufbreite')!;
  assert.equal(lb.bestanden, false);
  assert.equal(r.bestanden, false);
});

test('Durchgangshöhe unter 2000 mm → Fehler', () => {
  const r = berechneTreppe({ geschosshoehe: 2600, durchgangshoehe: 1950 });
  assert.equal(r.pruefungen.find((x) => x.id === 'durchgangshoehe')!.bestanden, false);
});

test('strengeres Auftritt-Minimum außen: gleicher Auftritt, andere Bewertung', () => {
  // 2000/150 → 13 Steigungen → 12 Auftritte; Lauflänge 3000 → Auftritt 250 mm.
  const opt = { geschosshoehe: 2000, gewuenschteSteigung: 150, verfuegbareLauflaenge: 3000 } as const;
  const w = berechneTreppe({ ...opt, bereich: 'wohnung' });
  const a = berechneTreppe({ ...opt, bereich: 'aussen' });
  assert.equal(w.auftritt, 250);
  assert.equal(a.auftritt, 250);
  assert.ok(w.pruefungen.find((x) => x.id === 'auftritt-min')!.bestanden, 'wohnung: 250 ≥ 230 ok');
  assert.ok(!a.pruefungen.find((x) => x.id === 'auftritt-min')!.bestanden, 'außen: 250 < 300 Fehler');
});

test('Determinismus: gleiche Eingabe → gleiches Ergebnis', () => {
  const e = { geschosshoehe: 2750, gewuenschteSteigung: 180, laufbreite: 1000 };
  assert.deepEqual(berechneTreppe(e), berechneTreppe(e));
});

// ---- Z1-W1-1: die Bedingung, auf der das ehrliche Badge steht --------------------------------
//
// `berechneTreppe` prüft die lichte Durchgangshöhe **nur, wenn sie übergeben wird**
// (`treppenBerechnung.ts:97`). Der reale Aufruf im Eigenschaften-Panel übergibt sie nicht — es gibt
// kein Eingabefeld dafür. Das Badge sagte trotzdem „DIN 18065 erfüllt".
//
// Diese Zusage hält die **Bedingung** fest, auf der der neue Vorbehalt im Panel steht: das Fehlen
// einer `durchgangshoehe`-Prüfung im Ergebnis. Die Rechnung selbst wird NICHT verändert
// (Nicht-Ziel des Auftrags) — es geht allein darum, dass die Anzeige diese Lücke erkennen kann.
test('Z1-W1-1: ohne Durchgangshöhe fehlt die Prüfung im Ergebnis — darauf steht der Vorbehalt', () => {
  const ohne = berechneTreppe({ geschosshoehe: 2800, laufbreite: 1000, bereich: 'wohnung' });
  assert.equal(
    ohne.pruefungen.some((p) => p.id === 'durchgangshoehe'), false,
    'ohne Übergabe darf keine Durchgangshöhen-Prüfung im Ergebnis stehen',
  );
  assert.ok(ohne.pruefungen.length > 0, 'die übrigen Kriterien werden sehr wohl geprüft');
});

test('Z1-W1-1 (Gegenprobe): mit Durchgangshöhe < 2000 mm steht die Prüfung drin und ist verletzt', () => {
  const mit = berechneTreppe({ geschosshoehe: 2800, laufbreite: 1000, bereich: 'wohnung', durchgangshoehe: 1900 });
  const pr = mit.pruefungen.find((p) => p.id === 'durchgangshoehe');
  assert.ok(pr, 'mit Übergabe MUSS die Prüfung im Ergebnis stehen');
  assert.equal(pr!.bestanden, false, '1900 mm < 2000 mm ⇒ verletzt');
  assert.equal(mit.bestanden, false, 'und das Gesamturteil kippt mit');
});
