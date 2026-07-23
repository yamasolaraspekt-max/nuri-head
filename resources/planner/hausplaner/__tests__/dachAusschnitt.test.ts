/**
 * Tests für die maßhaltige Dachöffnungs-/Ausschnittlogik (EA18). Rein, THREE-frei.
 * Stufe B (echter Rechteck-Ausschnitt) nur bei einfacher Rechteckfläche + einfacher Durchdringung;
 * Walm/Trapez/L-T-U und Gauben bleiben Prüffeld. Material-/Flächenmengen werden geschützt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  berechneAusschnitt,
  flaechenBilanz,
  istAchsenRechteck,
  istEinfacheDurchdringung,
  istKonvexesViereck,
  istSichereTrapezflaeche,
  istSichereKonvexeFlaeche,
  istGaubeDurchdringung,
  konvexeTeilflaechenSicher,
  rechteckKantenAbstandOk,
  KANTEN_RAND_M,
  sichereLoecher,
  type AusschnittBefund,
} from '../geometry/dachAusschnitt';
import { polygonFlaecheM2 } from '../geometry/polygonFlaeche';
import { grundrissPolygon } from '../geometry/grundriss';

// Rechteckige Satteldachfläche (10.6 m breit, 5.49 m geneigte Höhe)
const W = 10.6, H = 5.493;
const rect = [{ x: 0, y: 0 }, { x: W, y: 0 }, { x: W, y: H }, { x: 0, y: H }];
// Walm-Trapezfläche (unten breit, oben schmal)
const trapez = [{ x: 0, y: 0 }, { x: W, y: 0 }, { x: W - 3, y: H }, { x: 3, y: H }];
// L-Form (zusammengesetzt, >4 Ecken)
const lform = [{ x: 0, y: 0 }, { x: 10, y: 0 }, { x: 10, y: 3 }, { x: 4, y: 3 }, { x: 4, y: 8 }, { x: 0, y: 8 }];

const fenster = (over = {}) => ({ art: 'window', surfaceId: 'main_S', xRel: 0.5, yRel: 0.5, breiteM: 0.78, hoeheM: 1.18, tiefeM: 0.1, ...over });
const kamin = (over = {}) => ({ art: 'chimney', surfaceId: 'main_S', xRel: 0.5, yRel: 0.4, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.6, ...over });
const gaube = (over = {}) => ({ art: 'schleppgaube', surfaceId: 'main_S', xRel: 0.5, yRel: 0.42, breiteM: 2.5, hoeheM: 1.5, tiefeM: 2.5, ...over });

// --- Form-Erkennung ------------------------------------------------------------------------------
test('istAchsenRechteck: Rechteck=true, Trapez=false, L-Form=false', () => {
  assert.equal(istAchsenRechteck(rect), true);
  assert.equal(istAchsenRechteck(trapez), false);
  assert.equal(istAchsenRechteck(lform), false);
});

test('istEinfacheDurchdringung: Fenster/Kamin/Lüfter/Lichtkuppel=true, Gauben=false', () => {
  for (const a of ['window', 'chimney', 'vent', 'lichtkuppel']) assert.equal(istEinfacheDurchdringung(a), true);
  for (const a of ['schleppgaube', 'giebelgaube', 'trapezgaube', 'flachgaube', 'spitzgaube']) assert.equal(istEinfacheDurchdringung(a), false);
});

// --- Maßhaltigkeit -------------------------------------------------------------------------------
test('Öffnung maßhaltig: Breite=u (0.78), Höhe=v (Dachfenster=Länge 1.18); Tiefe NICHT verwechselt', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, fenster());
  assert.equal(b.breiteM, 0.78);
  assert.equal(b.hoeheM, 1.18); // v-Ausdehnung = Fensterlänge (hoeheM), nicht tiefeM(0.1)
});

test('Kamin: v-Ausdehnung = tiefeM (Footprint), nicht die Körperhöhe', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, kamin({ tiefeM: 0.6, hoeheM: 0.6 }));
  assert.equal(b.hoeheM, 0.6);
});

test('Öffnungsfläche ist positiv und kleiner als Bruttofläche', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, fenster());
  assert.ok(b.oeffnungFlaecheM2 > 0);
  assert.ok(b.oeffnungFlaecheM2 < b.bruttoFlaecheM2);
  assert.ok(Math.abs(b.bruttoFlaecheM2 - polygonFlaecheM2(rect)) < 1e-3);
});

// --- Stufe B: echter Ausschnitt nur auf sicherer Rechteckfläche ----------------------------------
test('Dachfenster auf Rechteckfläche: echter Ausschnitt (Stufe B), Netto = Brutto − Öffnung', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, fenster());
  assert.equal(b.echterAusschnitt, true);
  assert.equal(b.status, 'sicher');
  assert.ok(b.nettoFlaecheM2 < b.bruttoFlaecheM2);
  assert.ok(Math.abs((b.bruttoFlaecheM2 - b.oeffnungFlaecheM2) - b.nettoFlaecheM2) < 1e-2); // 3-Stellen-Rundung je Wert
  assert.ok(b.restPolygone.length >= 1);
});

test('Kamin auf Rechteckfläche: echter Ausschnitt (Stufe B)', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, kamin());
  assert.equal(b.echterAusschnitt, true);
  assert.equal(b.status, 'sicher');
});

test('Restflächen-Summe + Öffnung = Bruttofläche (keine Doppelzählung, keine negative Fläche)', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, fenster());
  const restSumme = b.restPolygone.reduce((s, p) => s + polygonFlaecheM2(p), 0);
  assert.ok(restSumme >= 0);
  assert.ok(Math.abs(restSumme + b.oeffnungFlaecheM2 - b.bruttoFlaecheM2) < 1e-2, `rest ${restSumme} + öffnung ${b.oeffnungFlaecheM2} != brutto ${b.bruttoFlaecheM2}`);
});

// --- Stufe A bleibt: komplexe Flächen + Gauben -> Prüffeld, kein Abzug ---------------------------
test('Gaube auf Rechteckfläche: KEIN echter Ausschnitt (Prüffeld), Netto = Brutto', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, gaube());
  assert.equal(b.echterAusschnitt, false);
  assert.equal(b.status, 'teilweise');
  assert.equal(b.nettoFlaecheM2, b.bruttoFlaecheM2); // kein Abzug
});

test('EA20: Dachfenster mittig auf sicherer Walm-Trapezfläche -> echtes Loch (Netto < Brutto)', () => {
  const b = berechneAusschnitt('west', trapez, W, H, fenster());
  assert.equal(b.echterAusschnitt, true);
  assert.equal(b.status, 'sicher');
  assert.ok(b.nettoFlaecheM2 < b.bruttoFlaecheM2 && b.nettoFlaecheM2 > 0);
});

test('L-Form: Öffnung über die Innenkante (reentrant) bleibt Prüffeld (EA22-Schutz)', () => {
  // Öffnung mittig über der einspringenden Ecke (4,3) -> teils Bein, teils außerhalb -> kein Loch
  const b = berechneAusschnitt('main', lform, 10, 8, fenster({ xRel: 0.4, yRel: 0.5 }));
  assert.equal(b.echterAusschnitt, false);
});

// --- Außerhalb / prüfpflichtig -------------------------------------------------------------------
test('Öffnung außerhalb der Dachfläche -> prüfpflichtig, kein Ausschnitt, Netto = Brutto', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, fenster({ xRel: 0.99, yRel: 0.99, breiteM: 2, hoeheM: 2 }));
  assert.equal(b.innerhalb, false);
  assert.equal(b.status, 'pruefpflichtig');
  assert.equal(b.echterAusschnitt, false);
  assert.equal(b.nettoFlaecheM2, b.bruttoFlaecheM2);
});

// --- Flächenbilanz -------------------------------------------------------------------------------
test('flaechenBilanz: nur echte Ausschnitte mindern Netto; Prüffelder separat geführt', () => {
  const f = berechneAusschnitt('main_S', rect, W, H, fenster());        // echt
  const g = berechneAusschnitt('main_S', rect, W, H, gaube());          // prüffeld
  const bil = flaechenBilanz(polygonFlaecheM2(rect), [f, g]);
  assert.ok(bil.oeffnungEchtM2 > 0);
  assert.ok(bil.oeffnungPrueffeldM2 > 0);
  assert.ok(Math.abs(bil.nettoM2 - (bil.bruttoM2 - bil.oeffnungEchtM2)) < 1e-3);
  assert.ok(bil.nettoM2 >= 0);
});

// --- Robustheit ----------------------------------------------------------------------------------
test('Robustheit: keine NaN/Infinity/negative Flächen bei Extremeingaben', () => {
  for (const o of [fenster({ breiteM: 0, hoeheM: 0 }), fenster({ breiteM: 1e6, hoeheM: 1e6 }), fenster({ xRel: NaN as any, yRel: NaN as any })]) {
    const b = berechneAusschnitt('main_S', rect, W, H, o as any);
    for (const v of [b.oeffnungFlaecheM2, b.bruttoFlaecheM2, b.nettoFlaecheM2, b.breiteM, b.hoeheM]) {
      assert.ok(Number.isFinite(v) && v >= 0, `ungültig: ${v}`);
    }
  }
});

test('Robustheit: leeres/zu kleines Polygon -> Brutto via width*height, kein Absturz', () => {
  const b = berechneAusschnitt('main_S', [], W, H, fenster());
  assert.ok(Number.isFinite(b.bruttoFlaecheM2) && b.bruttoFlaecheM2 > 0);
  assert.equal(b.echterAusschnitt, false); // ohne gültiges Polygon kein sicherer Ausschnitt
});

// --- EA19: sichere Dachhaut-Löcher (Mehrloch + Überlappung + Netto) -------------------------------
const mitId = (id: string, o: any) => ({ id, ...o });

test('sichereLoecher: ein Dachfenster auf Rechteckfläche -> ein echtes Loch, Netto < Brutto', () => {
  const s = sichereLoecher('main_S', rect, W, H, [mitId('f1', fenster())]);
  assert.equal(s.rechteckigeFlaeche, true);
  assert.equal(s.loecher.length, 1);
  assert.deepEqual(s.echteIds, ['f1']);
  assert.ok(s.nettoM2 < s.bruttoM2 && s.nettoM2 > 0);
  assert.ok(Math.abs(s.nettoM2 - (s.bruttoM2 - s.oeffnungEchtM2)) < 1e-2);
});

test('sichereLoecher: zwei NICHT überlappende Dachfenster -> zwei echte Löcher', () => {
  const s = sichereLoecher('main_S', rect, W, H, [
    mitId('a', fenster({ xRel: 0.3, yRel: 0.5 })),
    mitId('b', fenster({ xRel: 0.7, yRel: 0.5 })),
  ]);
  assert.equal(s.loecher.length, 2);
  assert.equal(s.echteIds.length, 2);
  assert.equal(s.prueffeldIds.length, 0);
});

test('sichereLoecher: zwei ÜBERLAPPENDE Öffnungen -> erstes Loch, zweites fällt auf Prüffeld zurück', () => {
  const s = sichereLoecher('main_S', rect, W, H, [
    mitId('a', fenster({ xRel: 0.5, yRel: 0.5, breiteM: 1.2, hoeheM: 1.2 })),
    mitId('b', fenster({ xRel: 0.52, yRel: 0.5, breiteM: 1.2, hoeheM: 1.2 })),
  ]);
  assert.equal(s.loecher.length, 1);
  assert.deepEqual(s.echteIds, ['a']);
  assert.deepEqual(s.prueffeldIds, ['b']);
  assert.ok(s.warnungen.length >= 1);
});

test('sichereLoecher: Gaube -> KEIN Loch (Prüffeld), Netto = Brutto', () => {
  const s = sichereLoecher('main_S', rect, W, H, [mitId('g', gaube())]);
  assert.equal(s.loecher.length, 0);
  assert.deepEqual(s.prueffeldIds, ['g']);
  assert.equal(s.nettoM2, s.bruttoM2);
});

test('EA20: sichereLoecher auf Walm-Trapez -> ein Loch (Netto < Brutto)', () => {
  const s = sichereLoecher('west', trapez, W, H, [mitId('f', fenster())]);
  assert.equal(s.rechteckigeFlaeche, false); // kein Achsenrechteck, aber sichere Trapezfläche
  assert.equal(s.loecher.length, 1);
  assert.ok(s.nettoM2 < s.bruttoM2);
});

test('sichereLoecher: L-Form-Öffnung über Innenkante -> KEIN Loch (kein Abzug) (EA22-Schutz)', () => {
  const s = sichereLoecher('main', lform, 10, 8, [mitId('f', fenster({ xRel: 0.4, yRel: 0.5 }))]);
  assert.equal(s.loecher.length, 0);
  assert.equal(s.nettoM2, s.bruttoM2);
});

test('sichereLoecher: Öffnung außerhalb -> KEIN Loch (Prüffeld), keine negative Fläche', () => {
  const s = sichereLoecher('main_S', rect, W, H, [mitId('f', fenster({ xRel: 0.99, yRel: 0.99, breiteM: 2, hoeheM: 2 }))]);
  assert.equal(s.loecher.length, 0);
  assert.ok(s.nettoM2 >= 0);
});

test('sichereLoecher: gemischt (Fenster echt + Gaube Prüffeld) -> nur Fenster mindert Netto', () => {
  const s = sichereLoecher('main_S', rect, W, H, [mitId('f', fenster()), mitId('g', gaube())]);
  assert.equal(s.echteIds.length, 1);
  assert.equal(s.prueffeldIds.length, 1);
  assert.ok(s.oeffnungEchtM2 > 0 && s.oeffnungPrueffeldM2 > 0);
  assert.ok(Math.abs(s.nettoM2 - (s.bruttoM2 - s.oeffnungEchtM2)) < 1e-2);
});

test('sichereLoecher: robust gegen NaN/leer (keine NaN/Infinity/negativ)', () => {
  const s = sichereLoecher('main_S', rect, W, H, [mitId('x', fenster({ breiteM: NaN as any, hoeheM: NaN as any }))]);
  for (const v of [s.bruttoM2, s.oeffnungEchtM2, s.oeffnungPrueffeldM2, s.nettoM2]) assert.ok(Number.isFinite(v) && v >= 0);
});

// --- EA20: echte Löcher auf konvexen Trapez-(Walm-)Flächen ---------------------------------------
// symmetrisches Walm-Trapez (Traufe breit, First schmal), isosceles
const walmTrapez = [{ x: 0, y: 0 }, { x: 10.6, y: 0 }, { x: 7.3, y: 5.0 }, { x: 3.3, y: 5.0 }];
// konkaves (nicht konvexes) Viereck
const konkav = [{ x: 0, y: 0 }, { x: 10, y: 0 }, { x: 3, y: 2 }, { x: 0, y: 8 }];
// selbstüberschneidendes „Viereck" (Sanduhr)
const sanduhr = [{ x: 0, y: 0 }, { x: 10, y: 8 }, { x: 10, y: 0 }, { x: 0, y: 8 }];
// Dreieck (Walm-Hüftende, oben zusammengelaufen) — als 4-Punkt mit doppelter Spitze
const dreieck = [{ x: 0, y: 0 }, { x: 10, y: 0 }, { x: 5, y: 5 }, { x: 5, y: 5 }];

test('istKonvexesViereck: Trapez/Rechteck=true; konkav/Sanduhr/Dreieck=false', () => {
  assert.equal(istKonvexesViereck(walmTrapez), true);
  assert.equal(istKonvexesViereck(rect), true);
  assert.equal(istKonvexesViereck(konkav), false);
  assert.equal(istKonvexesViereck(sanduhr), false);
  assert.equal(istKonvexesViereck(dreieck), false); // doppelte Ecke
});

test('istSichereTrapezflaeche: Walm-Trapez=true (Traufe∥First); Dreieck/konkav=false', () => {
  assert.equal(istSichereTrapezflaeche(walmTrapez), true);
  assert.equal(istSichereTrapezflaeche(dreieck), false);
  assert.equal(istSichereTrapezflaeche(konkav), false);
});

test('rechteckKantenAbstandOk: mittige Öffnung hält Abstand; randnahe Öffnung nicht', () => {
  const mittig = [{ x: 4.5, y: 2 }, { x: 6.1, y: 2 }, { x: 6.1, y: 3 }, { x: 4.5, y: 3 }];
  assert.equal(rechteckKantenAbstandOk(mittig, walmTrapez, KANTEN_RAND_M), true);
  const randnah = [{ x: 0.05, y: 0.05 }, { x: 1.0, y: 0.05 }, { x: 1.0, y: 0.6 }, { x: 0.05, y: 0.6 }];
  assert.equal(rechteckKantenAbstandOk(randnah, walmTrapez, KANTEN_RAND_M), false);
});

test('EA20: Kamin/Lüfter/Lichtkuppel mittig auf Walm-Trapez -> echtes Loch', () => {
  for (const art of ['chimney', 'vent', 'lichtkuppel']) {
    const b = berechneAusschnitt('west', walmTrapez, 10.6, 5.0, { art, surfaceId: 'west', xRel: 0.5, yRel: 0.4, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.6 });
    assert.equal(b.echterAusschnitt, true, `${art} kein Loch`);
  }
});

test('EA20: Öffnung nahe schräger Walmkante bleibt Prüffeld (kein Loch)', () => {
  // hoch + seitlich -> dort ist das Trapez schmal, Öffnung ragt Richtung schräge Kante
  const b = berechneAusschnitt('west', walmTrapez, 10.6, 5.0, { art: 'window', surfaceId: 'west', xRel: 0.8, yRel: 0.85, breiteM: 1.2, hoeheM: 1.0, tiefeM: 0.1 });
  assert.equal(b.echterAusschnitt, false);
  assert.equal(b.nettoFlaecheM2, b.bruttoFlaecheM2);
});

test('EA20: Gaube auf Walm-Trapez -> KEIN Loch (Prüffeld)', () => {
  const b = berechneAusschnitt('west', walmTrapez, 10.6, 5.0, { art: 'schleppgaube', surfaceId: 'west', xRel: 0.5, yRel: 0.4, breiteM: 2.5, hoeheM: 1.5, tiefeM: 2.5 });
  assert.equal(b.echterAusschnitt, false);
});

test('EA20: Dreiecks-Walmende -> KEIN Loch (kein konvexes Viereck)', () => {
  const b = berechneAusschnitt('east', dreieck, 10, 5, { art: 'window', surfaceId: 'east', xRel: 0.5, yRel: 0.3, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.1 });
  assert.equal(b.echterAusschnitt, false);
});

test('EA20: zwei getrennte Fenster auf Walm-Trapez -> zwei Löcher; überlappende -> Rückfall', () => {
  const zwei = sichereLoecher('west', walmTrapez, 10.6, 5.0, [
    mitId('a', { art: 'window', surfaceId: 'west', xRel: 0.38, yRel: 0.4, breiteM: 0.7, hoeheM: 0.8, tiefeM: 0.1 }),
    mitId('b', { art: 'window', surfaceId: 'west', xRel: 0.62, yRel: 0.4, breiteM: 0.7, hoeheM: 0.8, tiefeM: 0.1 }),
  ]);
  assert.equal(zwei.loecher.length, 2);
  const ueber = sichereLoecher('west', walmTrapez, 10.6, 5.0, [
    mitId('a', { art: 'window', surfaceId: 'west', xRel: 0.5, yRel: 0.4, breiteM: 1.0, hoeheM: 1.0, tiefeM: 0.1 }),
    mitId('b', { art: 'window', surfaceId: 'west', xRel: 0.52, yRel: 0.4, breiteM: 1.0, hoeheM: 1.0, tiefeM: 0.1 }),
  ]);
  assert.equal(ueber.loecher.length, 1);
  assert.equal(ueber.prueffeldIds.length, 1);
});

test('EA20: Brutto bleibt Polygonfläche (Reparatur 6) auch bei Trapez-Loch', () => {
  const b = berechneAusschnitt('west', walmTrapez, 10.6, 5.0, { art: 'window', surfaceId: 'west', xRel: 0.5, yRel: 0.4, breiteM: 0.78, hoeheM: 1.18, tiefeM: 0.1 });
  assert.ok(Math.abs(b.bruttoFlaecheM2 - polygonFlaecheM2(walmTrapez)) < 1e-3);
  assert.ok(b.nettoFlaecheM2 >= 0);
});

// --- EA21: echte Löcher auf beliebigen sicheren KONVEXEN Mehr-Eck-Flächen ------------------------
const fuenfeck = [{ x: 1, y: 0 }, { x: 5, y: 0 }, { x: 6, y: 3 }, { x: 3, y: 5 }, { x: 0, y: 3 }];           // konvexes Fünfeck
const sechseck = [{ x: 2, y: 0 }, { x: 6, y: 0 }, { x: 8, y: 3 }, { x: 6, y: 6 }, { x: 2, y: 6 }, { x: 0, y: 3 }]; // konvexes Sechseck
const konkavFuenf = [{ x: 0, y: 0 }, { x: 6, y: 0 }, { x: 6, y: 6 }, { x: 3, y: 3 }, { x: 0, y: 6 }];        // Pfeil/Dart -> (3,3) einspringend
const uForm = [{ x: 0, y: 0 }, { x: 8, y: 0 }, { x: 8, y: 6 }, { x: 6, y: 6 }, { x: 6, y: 2 }, { x: 2, y: 2 }, { x: 2, y: 6 }, { x: 0, y: 6 }];
const tForm = [{ x: 0, y: 0 }, { x: 9, y: 0 }, { x: 9, y: 2 }, { x: 6, y: 2 }, { x: 6, y: 6 }, { x: 3, y: 6 }, { x: 3, y: 2 }, { x: 0, y: 2 }];
const selbstschnitt = [{ x: 0, y: 0 }, { x: 6, y: 6 }, { x: 6, y: 0 }, { x: 0, y: 6 }, { x: 3, y: 8 }]; // gemischt -> Vorzeichenwechsel
const nullkante = [{ x: 0, y: 0 }, { x: 5, y: 0 }, { x: 5, y: 0.00001 }, { x: 5, y: 4 }, { x: 0, y: 4 }];
const doppelpunkt = [{ x: 0, y: 0 }, { x: 5, y: 0 }, { x: 5, y: 4 }, { x: 5, y: 4 }, { x: 0, y: 4 }];

test('istSichereKonvexeFlaeche: konvexes Fünf-/Sechseck/Rechteck/Trapez = true', () => {
  assert.equal(istSichereKonvexeFlaeche(fuenfeck), true);
  assert.equal(istSichereKonvexeFlaeche(sechseck), true);
  assert.equal(istSichereKonvexeFlaeche(rect), true);
  assert.equal(istSichereKonvexeFlaeche(walmTrapez), true);
});

test('istSichereKonvexeFlaeche: konkav/L/T/U/Selbstschnitt/Doppelpunkt/Nullkante/Dreieck = false', () => {
  assert.equal(istSichereKonvexeFlaeche(konkavFuenf), false);
  assert.equal(istSichereKonvexeFlaeche(lform), false);
  assert.equal(istSichereKonvexeFlaeche(tForm), false);
  assert.equal(istSichereKonvexeFlaeche(uForm), false);
  assert.equal(istSichereKonvexeFlaeche(selbstschnitt), false);
  assert.equal(istSichereKonvexeFlaeche(doppelpunkt), false);
  assert.equal(istSichereKonvexeFlaeche(nullkante), false);
  assert.equal(istSichereKonvexeFlaeche(dreieck), false); // doppelte Ecke / Dreieck
});

test('EA21: Dachfenster mittig im konvexen Fünfeck -> echtes Loch', () => {
  const b = berechneAusschnitt('poly5', fuenfeck, 6, 5, { art: 'window', surfaceId: 'poly5', xRel: 0.5, yRel: 0.45, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.1 });
  assert.equal(b.echterAusschnitt, true);
  assert.equal(b.status, 'sicher');
  assert.ok(b.nettoFlaecheM2 < b.bruttoFlaecheM2 && b.nettoFlaecheM2 > 0);
});

test('EA21: Kamin/Lüfter/Lichtkuppel im konvexen Sechseck -> echtes Loch', () => {
  for (const art of ['chimney', 'vent', 'lichtkuppel']) {
    const b = berechneAusschnitt('poly6', sechseck, 8, 6, { art, surfaceId: 'poly6', xRel: 0.5, yRel: 0.5, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.6 });
    assert.equal(b.echterAusschnitt, true, `${art} kein Loch`);
  }
});

test('EA21: Öffnung nahe diagonaler Kante des Fünfecks bleibt Prüffeld', () => {
  const b = berechneAusschnitt('poly5', fuenfeck, 6, 5, { art: 'window', surfaceId: 'poly5', xRel: 0.92, yRel: 0.55, breiteM: 0.9, hoeheM: 0.9, tiefeM: 0.1 });
  assert.equal(b.echterAusschnitt, false);
  assert.equal(b.nettoFlaecheM2, b.bruttoFlaecheM2);
});

test('EA21: Gaube im konvexen Fünfeck -> KEIN echtes Loch (Prüffeld)', () => {
  const b = berechneAusschnitt('poly5', fuenfeck, 6, 5, { art: 'schleppgaube', surfaceId: 'poly5', xRel: 0.5, yRel: 0.45, breiteM: 1.5, hoeheM: 1.0, tiefeM: 1.2 });
  assert.equal(b.echterAusschnitt, false);
});

test('EA21: konkaves Fünfeck (Dart) erzeugt KEIN echtes Loch', () => {
  const b = berechneAusschnitt('dart', konkavFuenf, 6, 6, { art: 'window', surfaceId: 'dart', xRel: 0.5, yRel: 0.2, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.1 });
  assert.equal(b.echterAusschnitt, false);
});

test('EA22: L-/T-/U-Öffnung über Innenkante/Innenhof/einspringenden Winkel bleibt Prüffeld', () => {
  // L: Öffnung über reentrant-Ecke (4,3) -> teils im Bein, teils außerhalb -> kein Loch
  assert.equal(berechneAusschnitt('main', lform, 10, 8, { art: 'window', surfaceId: 'main', xRel: 0.4, yRel: 0.5, breiteM: 1.2, hoeheM: 1.2, tiefeM: 0.1 }).echterAusschnitt, false);
  // T: Öffnung über Innenkante des Stiels (x=3) -> kein Loch
  assert.equal(berechneAusschnitt('main', tForm, 9, 6, { art: 'window', surfaceId: 'main', xRel: 1 / 3, yRel: 0.417, breiteM: 1.2, hoeheM: 1.2, tiefeM: 0.1 }).echterAusschnitt, false);
  // U: Öffnung im Innenhof -> kein Loch
  assert.equal(berechneAusschnitt('main', uForm, 8, 6, { art: 'window', surfaceId: 'main', xRel: 0.5, yRel: 2 / 3, breiteM: 1.0, hoeheM: 1.0, tiefeM: 0.1 }).echterAusschnitt, false);
});

test('EA21: sichereLoecher im Fünfeck — zwei getrennt -> zwei Löcher; überlappend -> Rückfall', () => {
  const zwei = sichereLoecher('poly5', fuenfeck, 6, 5, [
    mitId('a', { art: 'window', surfaceId: 'poly5', xRel: 0.38, yRel: 0.42, breiteM: 0.5, hoeheM: 0.6, tiefeM: 0.1 }),
    mitId('b', { art: 'window', surfaceId: 'poly5', xRel: 0.6, yRel: 0.42, breiteM: 0.5, hoeheM: 0.6, tiefeM: 0.1 }),
  ]);
  assert.equal(zwei.loecher.length, 2);
  const ueber = sichereLoecher('poly5', fuenfeck, 6, 5, [
    mitId('a', { art: 'window', surfaceId: 'poly5', xRel: 0.5, yRel: 0.45, breiteM: 0.8, hoeheM: 0.8, tiefeM: 0.1 }),
    mitId('b', { art: 'window', surfaceId: 'poly5', xRel: 0.52, yRel: 0.45, breiteM: 0.8, hoeheM: 0.8, tiefeM: 0.1 }),
  ]);
  assert.equal(ueber.loecher.length, 1);
  assert.equal(ueber.prueffeldIds.length, 1);
});

test('EA21: Brutto = Polygonfläche (Reparatur 6) auch bei n-Eck; Netto nie negativ', () => {
  const b = berechneAusschnitt('poly6', sechseck, 8, 6, { art: 'window', surfaceId: 'poly6', xRel: 0.5, yRel: 0.5, breiteM: 0.78, hoeheM: 1.0, tiefeM: 0.1 });
  assert.ok(Math.abs(b.bruttoFlaecheM2 - polygonFlaecheM2(sechseck)) < 1e-3);
  assert.ok(b.nettoFlaecheM2 >= 0);
});

// --- EA22: konkave L-/T-/U-Flachdachflächen via sichere konvexe Zerlegung ------------------------
// echte Engine-Grundrisse (0-basiert) wie buildFlat sie registriert
const lPoly = grundrissPolygon('l-form', 10, 8, 4, 4);
const tPoly = grundrissPolygon('t-form', 10, 8, 4, 4);
const uPoly = grundrissPolygon('u-form', 10, 8, 4, 4);

test('EA22 Zerlegung: L=2, T=3, U=3 Teilrechtecke; Summe = Polygonfläche (keine Doppelzählung/Lücke)', () => {
  const lT = konvexeTeilflaechenSicher(lPoly);
  const tT = konvexeTeilflaechenSicher(tPoly);
  const uT = konvexeTeilflaechenSicher(uPoly);
  assert.equal(lT.length, 2);
  assert.equal(tT.length, 3);
  assert.equal(uT.length, 3);
  for (const [poly, teile] of [[lPoly, lT], [tPoly, tT], [uPoly, uT]] as const) {
    const summe = teile.reduce((s, r) => s + polygonFlaecheM2(r), 0);
    assert.ok(Math.abs(summe - polygonFlaecheM2(poly)) < 1e-3, `Teilflächen-Summe != Polygonfläche`);
  }
});

test('EA22 Zerlegung: konvexe/Trapez/Dreieck/Selbstschnitt/schräge Fläche -> KEINE Zerlegung ([])', () => {
  assert.equal(konvexeTeilflaechenSicher(rect).length, 0);       // konvex -> separat behandelt
  assert.equal(konvexeTeilflaechenSicher(walmTrapez).length, 0); // konvex
  assert.equal(konvexeTeilflaechenSicher(dreieck).length, 0);
  assert.equal(konvexeTeilflaechenSicher(selbstschnitt).length, 0);
  assert.equal(konvexeTeilflaechenSicher(konkavFuenf).length, 0); // schräge Kanten -> nicht rektilinear
});

test('EA22: L-Fläche wird als konkav erkannt (kein konvexes Polygon)', () => {
  assert.equal(istSichereKonvexeFlaeche(lPoly), false);
  assert.equal(istSichereKonvexeFlaeche(tPoly), false);
  assert.equal(istSichereKonvexeFlaeche(uPoly), false);
});

test('EA22: Dachfenster vollständig in einem L-Teilbereich -> echtes Loch (Netto < Brutto)', () => {
  // Basisriegel von L (x 0..10, y 0..4) -> Öffnung bei (7,2)
  const b = berechneAusschnitt('main', lPoly, 10, 8, { art: 'window', surfaceId: 'main', xRel: 0.7, yRel: 0.25, breiteM: 0.78, hoeheM: 1.0, tiefeM: 0.1 });
  assert.equal(b.echterAusschnitt, true, b.grund);
  assert.equal(b.status, 'sicher');
  assert.ok(b.nettoFlaecheM2 < b.bruttoFlaecheM2 && b.nettoFlaecheM2 > 0);
});

test('EA22: Kamin in einem T-Teilbereich + Lüfter in einem U-Teilbereich -> echte Löcher', () => {
  const t = berechneAusschnitt('main', tPoly, 10, 8, { art: 'chimney', surfaceId: 'main', xRel: 0.5, yRel: 0.7, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.6 }); // Stiel (mittig oben)
  assert.equal(t.echterAusschnitt, true, t.grund);
  const u = berechneAusschnitt('main', uPoly, 10, 8, { art: 'vent', surfaceId: 'main', xRel: 0.2, yRel: 0.7, breiteM: 0.5, hoeheM: 0.5, tiefeM: 0.5 }); // linker Flügel
  assert.equal(u.echterAusschnitt, true, u.grund);
});

test('EA22: Öffnung im U-Innenhof bleibt Prüffeld (außerhalb der Fläche)', () => {
  const b = berechneAusschnitt('main', uPoly, 10, 8, { art: 'window', surfaceId: 'main', xRel: 0.5, yRel: 0.8, breiteM: 0.6, hoeheM: 0.6, tiefeM: 0.1 });
  assert.equal(b.echterAusschnitt, false);
  assert.equal(b.nettoFlaecheM2, b.bruttoFlaecheM2);
});

test('EA22: Gaube auf L-Fläche bleibt Prüffeld (keine echte Hauptdachöffnung)', () => {
  const b = berechneAusschnitt('main', lPoly, 10, 8, { art: 'schleppgaube', surfaceId: 'main', xRel: 0.7, yRel: 0.25, breiteM: 1.5, hoeheM: 1.0, tiefeM: 1.2 });
  assert.equal(b.echterAusschnitt, false);
});

test('EA22: sichereLoecher auf L — zwei getrennte Fenster in Teilbereichen -> zwei Löcher; überlappend -> Rückfall', () => {
  const zwei = sichereLoecher('main', lPoly, 10, 8, [
    mitId('a', { art: 'window', surfaceId: 'main', xRel: 0.65, yRel: 0.25, breiteM: 0.6, hoeheM: 0.8, tiefeM: 0.1 }),
    mitId('b', { art: 'window', surfaceId: 'main', xRel: 0.85, yRel: 0.25, breiteM: 0.6, hoeheM: 0.8, tiefeM: 0.1 }),
  ]);
  assert.equal(zwei.loecher.length, 2);
  const ueber = sichereLoecher('main', lPoly, 10, 8, [
    mitId('a', { art: 'window', surfaceId: 'main', xRel: 0.7, yRel: 0.25, breiteM: 1.0, hoeheM: 1.0, tiefeM: 0.1 }),
    mitId('b', { art: 'window', surfaceId: 'main', xRel: 0.73, yRel: 0.25, breiteM: 1.0, hoeheM: 1.0, tiefeM: 0.1 }),
  ]);
  assert.equal(ueber.loecher.length, 1);
  assert.equal(ueber.prueffeldIds.length, 1);
});

test('EA22: Brutto = Polygonfläche (Reparatur 6) bei L/T/U; Netto = Brutto − echte Öffnung; nie negativ', () => {
  const b = berechneAusschnitt('main', lPoly, 10, 8, { art: 'window', surfaceId: 'main', xRel: 0.7, yRel: 0.25, breiteM: 0.78, hoeheM: 1.0, tiefeM: 0.1 });
  assert.ok(Math.abs(b.bruttoFlaecheM2 - polygonFlaecheM2(lPoly)) < 1e-3);
  assert.ok(b.nettoFlaecheM2 >= 0);
  assert.ok(Math.abs((b.bruttoFlaecheM2 - b.oeffnungFlaecheM2) - b.nettoFlaecheM2) < 1e-2);
});

test('EA22: keine NaN/Infinity bei L/T/U-Zerlegung mit Extremeingaben', () => {
  for (const poly of [lPoly, tPoly, uPoly]) {
    const teile = konvexeTeilflaechenSicher(poly);
    for (const r of teile) for (const p of r) assert.ok(Number.isFinite(p.x) && Number.isFinite(p.y));
  }
});

// --- EA23: echte Gaubenöffnung (Hauptdach-Loch) auf sicheren Rechteck-Sattel/Pultflächen ----------
test('istGaubeDurchdringung: 5 Gaubentypen true; window/chimney/tonnengaube/fledermaus false', () => {
  for (const a of ['schleppgaube', 'flachgaube', 'trapezgaube', 'giebelgaube', 'spitzgaube']) assert.equal(istGaubeDurchdringung(a), true);
  for (const a of ['window', 'chimney', 'vent', 'lichtkuppel', 'tonnengaube', 'fledermausgaube', 'walmgaube']) assert.equal(istGaubeDurchdringung(a), false);
});

test('EA23: Schleppgaube auf rechteckiger Satteldachfläche (gaubeErlaubt) -> echtes Loch, Netto < Brutto', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, gaube(), 0.1, true);
  assert.equal(b.echterAusschnitt, true, b.grund);
  assert.equal(b.status, 'sicher');
  assert.ok(b.nettoFlaecheM2 < b.bruttoFlaecheM2 && b.nettoFlaecheM2 > 0);
});

test('EA23: Giebel-/Spitz-/Flach-/Trapezgaube auf Rechteckfläche (gaubeErlaubt) -> echtes Loch', () => {
  for (const art of ['giebelgaube', 'spitzgaube', 'flachgaube', 'trapezgaube']) {
    const b = berechneAusschnitt('main_S', rect, W, H, gaube({ art, breiteM: 2.0, hoeheM: 1.2, tiefeM: 2.0 }), 0.1, true);
    assert.equal(b.echterAusschnitt, true, `${art}: ${b.grund}`);
  }
});

test('EA23: Gaube OHNE Freigabe (z. B. Flachdach) bleibt Prüffeld (kein Abzug)', () => {
  const b = berechneAusschnitt('main', rect, W, H, gaube(), 0.1, false);
  assert.equal(b.echterAusschnitt, false);
  assert.equal(b.nettoFlaecheM2, b.bruttoFlaecheM2);
});

test('EA23: Gaube auf Walm-Trapez / konvexem n-Eck / L-Form bleibt Prüffeld (auch mit gaubeErlaubt)', () => {
  assert.equal(berechneAusschnitt('west', walmTrapez, 10.6, 5.0, gaube(), 0.1, true).echterAusschnitt, false);
  assert.equal(berechneAusschnitt('poly5', fuenfeck, 6, 5, gaube({ breiteM: 1.2, hoeheM: 0.8, tiefeM: 1.2 }), 0.1, true).echterAusschnitt, false);
  assert.equal(berechneAusschnitt('main', lPoly, 10, 8, gaube({ breiteM: 1.5, tiefeM: 1.5 }), 0.1, true).echterAusschnitt, false);
});

test('EA23: Gaubenloch liegt vollständig innen mit Randabstand (nicht an Traufe/First/Ortgang)', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, gaube(), 0.1, true);
  assert.ok(b.rechteckM.uMin >= KANTEN_RAND_M - 1e-9 && b.rechteckM.uMax <= W - KANTEN_RAND_M + 1e-9);
  assert.ok(b.rechteckM.vMin >= KANTEN_RAND_M - 1e-9 && b.rechteckM.vMax <= H - KANTEN_RAND_M + 1e-9);
});

test('EA23: Gaube zu nah am First -> Prüffeld (Randabstand verletzt)', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, gaube({ yRel: 0.99 }), 0.1, true);
  assert.equal(b.echterAusschnitt, false);
});

test('EA23: Netto = Brutto − echte Gaubenöffnung; nie negativ; Brutto = Polygonfläche (Reparatur 6)', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, gaube(), 0.1, true);
  assert.ok(Math.abs(b.bruttoFlaecheM2 - polygonFlaecheM2(rect)) < 1e-3);
  assert.ok(Math.abs((b.bruttoFlaecheM2 - b.oeffnungFlaecheM2) - b.nettoFlaecheM2) < 1e-2);
  assert.ok(b.nettoFlaecheM2 >= 0);
});

test('EA23: sichereLoecher (gaubeErlaubt) — zwei getrennte Gauben -> zwei Löcher; überlappend -> Rückfall', () => {
  const zwei = sichereLoecher('main_S', rect, W, H, [
    mitId('a', gaube({ xRel: 0.28, yRel: 0.45, breiteM: 2.0, tiefeM: 2.0 })),
    mitId('b', gaube({ xRel: 0.72, yRel: 0.45, breiteM: 2.0, tiefeM: 2.0 })),
  ], true);
  assert.equal(zwei.loecher.length, 2);
  const ueber = sichereLoecher('main_S', rect, W, H, [
    mitId('a', gaube({ xRel: 0.5, yRel: 0.45 })),
    mitId('b', gaube({ xRel: 0.53, yRel: 0.45 })),
  ], true);
  assert.equal(ueber.loecher.length, 1);
  assert.equal(ueber.prueffeldIds.length, 1);
});

test('EA23: sichereLoecher OHNE gaubeErlaubt -> Gauben bleiben Prüffeld (kein Abzug)', () => {
  const s = sichereLoecher('main_S', rect, W, H, [mitId('g', gaube())], false);
  assert.equal(s.loecher.length, 0);
  assert.equal(s.nettoM2, s.bruttoM2);
});

test('EA23: Dachfenster/Kamin auf Rechteckfläche brauchen kein gaubeErlaubt (einfache Durchdringung weiterhin)', () => {
  assert.equal(berechneAusschnitt('main_S', rect, W, H, fenster(), 0.1, false).echterAusschnitt, true);
  assert.equal(berechneAusschnitt('main_S', rect, W, H, kamin(), 0.1, false).echterAusschnitt, true);
});

// --- EA25: realer Gauben-Fußabdruck als Polygon-Loch (Pentagon) statt Rechteck -------------------
const giebelE = (over = {}) => ({ art: 'giebelgaube', surfaceId: 'main_S', xRel: 0.5, yRel: 0.42, breiteM: 2.5, hoeheM: 1.5, tiefeM: 2.5, pitch: 35, ...over });
const a35 = (35 * Math.PI) / 180;

test('EA25: Giebelgaube auf Rechteck-Sattel mit aRad -> Polygon-Loch (poly 5 Punkte), Netto = Brutto − Polygonfläche', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, giebelE(), 0.1, true, a35);
  assert.equal(b.echterAusschnitt, true, b.grund);
  assert.ok(Array.isArray(b.poly) && b.poly!.length === 5, `poly=${b.poly && b.poly.length}`);
  assert.ok(Math.abs(b.oeffnungFlaecheM2 - polygonFlaecheM2(b.poly!)) < 1e-2);
  assert.ok(b.oeffnungFlaecheM2 > 8 && b.oeffnungFlaecheM2 < 9, `area=${b.oeffnungFlaecheM2}`); // ≈ 8.445
  assert.ok(Math.abs((b.bruttoFlaecheM2 - b.oeffnungFlaecheM2) - b.nettoFlaecheM2) < 1e-2);
});

test('EA25: ohne aRad (0) -> Rechteck-Loch (poly undefined), Rückfall EA23', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, giebelE(), 0.1, true, 0);
  assert.equal(b.echterAusschnitt, true);
  assert.equal(b.poly, undefined);
});

test('EA25: Pultgaube (Schlepp) mit aRad -> Polygon-Loch (4 Punkte), v-tiefer als depth', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, gaube(), 0.1, true, a35);
  assert.equal(b.echterAusschnitt, true);
  assert.ok(Array.isArray(b.poly) && b.poly!.length === 4);
  assert.ok(b.oeffnungFlaecheM2 > 7 && b.oeffnungFlaecheM2 < 8, `area=${b.oeffnungFlaecheM2}`); // ≈ 7.63
});

test('EA25: Giebel zu nah am First -> kein Polygon-Loch (Rückfall Rechteck/Prüffeld), Brutto unverändert wenn Prüffeld', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, giebelE({ yRel: 0.95 }), 0.1, true, a35);
  assert.equal(b.poly, undefined); // Loch-Spitze würde über H-Rand ragen -> kein Pentagon
});

test('EA25: Dachfenster mit aRad -> weiterhin Rechteck (kein Polygon, einfache Durchdringung)', () => {
  const b = berechneAusschnitt('main_S', rect, W, H, fenster(), 0.1, false, a35);
  assert.equal(b.echterAusschnitt, true);
  assert.equal(b.poly, undefined);
});

test('EA25: sichereLoecher (gaubeErlaubt + aRad) -> Loch mit poly, oeffnungEcht = Polygonfläche', () => {
  const s = sichereLoecher('main_S', rect, W, H, [mitId('g', giebelE())], true, a35);
  assert.equal(s.loecher.length, 1);
  assert.ok(Array.isArray(s.loecher[0].poly) && s.loecher[0].poly!.length === 5);
  assert.ok(s.oeffnungEchtM2 > 8 && s.oeffnungEchtM2 < 9);
  assert.ok(Math.abs(s.nettoM2 - (s.bruttoM2 - s.oeffnungEchtM2)) < 1e-2);
});

test('EA25: Gaube auf Walm-Trapez bleibt Prüffeld auch mit aRad (kein Polygon-Loch)', () => {
  const b = berechneAusschnitt('west', trapez, W, H, giebelE(), 0.1, true, a35);
  assert.equal(b.echterAusschnitt, false);
  assert.equal(b.poly, undefined);
});
