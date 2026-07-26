import { test } from 'node:test';
import assert from 'node:assert/strict';
import { treppenTyp, type TreppenTyp } from '../geometry/treppenTypen';
import { treppeAlsSvg } from '../geometry/treppeSvg';
// AUF-54: die Farben kommen aus der aufrufenden Schicht — `geometry/` kennt keine mehr.
import { TREPPE_FARBEN } from '../app/studioDaten';

const typen: TreppenTyp[] = ['gerade', 'l-podest', 'u-podest', 'spindel'];

test('jeder Typ liefert eine wohlgeformte Zeichnung + DIN-Stufung', () => {
  for (const typ of typen) {
    const r = treppenTyp({ typ, geschosshoehe: 2800, laufbreite: 1000, durchmesser: 1800 });
    assert.ok(r.anzahlSteigungen >= 10, `${typ} Steigungen`);
    assert.ok(r.zeichnung.umriss.length >= 4, `${typ} Umriss`);
    assert.ok(r.zeichnung.stufenlinien.length >= 1, `${typ} Stufen`);
    assert.ok(r.zeichnung.lauflinie.length >= 2, `${typ} Lauflinie`);
    assert.ok(r.grundflaeche.breiteMm > 0 && r.grundflaeche.tiefeMm > 0, `${typ} Grundflaeche`);
  }
});

test('gerade: Lauflaenge = Auftritte × Auftritt, Stufenlinien = Auftritte−1', () => {
  const r = treppenTyp({ typ: 'gerade', geschosshoehe: 2800, laufbreite: 1000 });
  assert.equal(r.zeichnung.stufenlinien.length, r.anzahlAuftritte - 1);
  assert.ok(Math.abs(r.grundflaeche.breiteMm - r.anzahlAuftritte * r.auftritt) < 2);
});

test('L-Podest ist kompakter (kleinere Lauflänge) als die gerade Treppe', () => {
  const g = treppenTyp({ typ: 'gerade', geschosshoehe: 2800, laufbreite: 1000 });
  const l = treppenTyp({ typ: 'l-podest', geschosshoehe: 2800, laufbreite: 1000 });
  assert.ok(l.grundflaeche.breiteMm < g.grundflaeche.breiteMm);
});

test('Spindel passt in ein Quadrat ≈ Durchmesser', () => {
  const r = treppenTyp({ typ: 'spindel', geschosshoehe: 2800, laufbreite: 800, durchmesser: 1800 });
  assert.ok(r.grundflaeche.breiteMm <= 1810 && r.grundflaeche.tiefeMm <= 1810);
});

test('treppeAlsSvg liefert gültigen SVG-String mit Stufen + Lauflinie', () => {
  const r = treppenTyp({ typ: 'gerade', geschosshoehe: 2800, laufbreite: 1000 });
  const svg = treppeAlsSvg(r.zeichnung, { farben: TREPPE_FARBEN, titel: 'Gerade' });
  assert.ok(svg.startsWith('<svg') && svg.endsWith('</svg>'));
  assert.ok(svg.includes('<polygon'));   // Umriss
  assert.ok(svg.includes('<line'));      // Stufen
  assert.ok(svg.includes('<polyline')); // Lauflinie
  assert.ok(svg.includes('Gerade'));
});

test('SVG numeriert Stufen (Text 1)', () => {
  const r = treppenTyp({ typ: 'gerade', geschosshoehe: 2800, laufbreite: 1000 });
  assert.ok(treppeAlsSvg(r.zeichnung, { farben: TREPPE_FARBEN }).includes('>1</text>'));
});
