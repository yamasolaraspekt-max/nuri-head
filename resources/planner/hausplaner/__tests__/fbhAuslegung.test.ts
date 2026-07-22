/**
 * Fußbodenheizung-Auslegung: Nutzfläche, Rohrlänge, Heizkreise.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { fbhAuslegung } from '../geometry/fbhAuslegung';

test('20 m², VA 150 mm: Rohrlänge ~133 m, mind. 2 Kreise', () => {
  const r = fbhAuslegung({ flaeche: 20, heizlast: 1400, verlegeabstand: 150 });
  // Verlegt = 20 * 1000/150 = 133,3 m
  assert.ok(Math.abs(r.rohrlaengeGesamt - (133.3 + r.anzahlHeizkreise * 5)) < 1);
  assert.ok(r.anzahlHeizkreise >= 2, `Kreise ${r.anzahlHeizkreise}`);
  assert.ok(r.rohrProKreis <= 100.5, `längster Kreis ${r.rohrProKreis}`);
});

test('kein Kreis überschreitet die maximale Kreislänge', () => {
  const r = fbhAuslegung({ flaeche: 45, heizlast: 3000, verlegeabstand: 100, maxKreisLaenge: 100 });
  assert.ok(r.rohrProKreis <= 100.5);
  assert.ok(r.pruefungen.find((x) => x.id === 'kreislaenge')!.bestanden);
});

test('Sperrfläche reduziert die Nutzfläche', () => {
  const r = fbhAuslegung({ flaeche: 20, heizlast: 1400, sperrflaeche: 5 });
  assert.equal(r.nutzflaeche, 15);
});

test('Sperrfläche ≥ Fläche → Fehler, keine Nutzfläche', () => {
  const r = fbhAuslegung({ flaeche: 10, heizlast: 800, sperrflaeche: 10 });
  assert.equal(r.nutzflaeche, 0);
  assert.equal(r.bestanden, false);
});

test('spezifische Leistung > 100 W/m² → Warnung (kein Fehler)', () => {
  const r = fbhAuslegung({ flaeche: 10, heizlast: 1200 }); // 120 W/m²
  const sp = r.pruefungen.find((x) => x.id === 'spez-leistung')!;
  assert.equal(sp.bestanden, false);
  assert.equal(sp.schwere, 'warnung');
  assert.ok(r.bestanden, 'Warnung blockiert nicht');
});

test('Determinismus', () => {
  const e = { flaeche: 24, heizlast: 1600, verlegeabstand: 125 };
  assert.deepEqual(fbhAuslegung(e), fbhAuslegung(e));
});
