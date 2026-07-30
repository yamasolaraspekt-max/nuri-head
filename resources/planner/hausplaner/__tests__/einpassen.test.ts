/**
 * AUF-62 — „Ansicht einpassen".
 *
 * **Was dieser Test anders macht als eine Sichtprobe:** Er **rechnet nach**. „Der ganze Grundriss
 * ist im Bild" ist eine Aussage über Schirmkoordinaten — deshalb rechnet jeder Fall hier jeden
 * Weltpunkt über `aufSchirm` um und prüft, dass er innerhalb der Bühne liegt. Ein Screenshot
 * könnte dasselbe behaupten, aber nicht belegen.
 *
 * **Die Kanten, an denen so etwas erfahrungsgemäß bricht** (alle aus dem Auftrag, alle geprüft):
 * leeres Geschoss · Split-Ansicht · das Vorzeichen der y-Achse · Nullfläche · die Maßstabsgrenzen ·
 * der Rand.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  einpassen, aufSchirm, knotenPunkte,
  EINPASS_RAND, ZOOM_MIN, ZOOM_MAX, ZOOM_STANDARD,
} from '../app/dashboard/einpassen';
import { standardPan } from '../app/dashboard/pan';
import type { SceneNode, WallNode } from '../domain/scene.types';
// AUF-48: die Hauptansicht ist zerlegt — diese Zusage liest ALLE ihre Teile.
import { zerlegteApp } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const quelle = zerlegteApp();

const BREITE = 900;
const HOEHE = 700;

/** Liegt jeder Punkt nach dem Einpassen wirklich auf der Bühne? Die Frage, um die es geht. */
function alleSichtbar(punkte: Array<{ x: number; y: number }>, breite: number, hoehe: number, rand = EINPASS_RAND): void {
  const e = einpassen(punkte, breite, hoehe, rand);
  for (const p of punkte) {
    const s = aufSchirm(p, e);
    assert.ok(s.x >= -0.001 && s.x <= breite + 0.001, `x=${s.x} liegt außerhalb von 0…${breite}`);
    assert.ok(s.y >= -0.001 && s.y <= hoehe + 0.001, `y=${s.y} liegt außerhalb von 0…${hoehe}`);
  }
}

// --- K3: alles im Bild --------------------------------------------------------------------------
test('K3: nach dem Einpassen liegt jeder Punkt im sichtbaren Bereich — nachgerechnet', () => {
  const rechteck = [{ x: 0, y: 0 }, { x: 12000, y: 0 }, { x: 12000, y: 8000 }, { x: 0, y: 8000 }];
  alleSichtbar(rechteck, BREITE, HOEHE);
});

test('K3: auch ein Grundriss, der nicht im Ursprung liegt', () => {
  // Ein Plan weit draußen im Koordinatenraum — der Verschub muss ihn holen, nicht nur der Maßstab.
  alleSichtbar([{ x: 500000, y: -300000 }, { x: 512000, y: -292000 }], BREITE, HOEHE);
});

test('K6: ein deutlich HÖHERER und ein deutlich BREITERER Grundriss — beide ganz im Bild', () => {
  // Das ist der Test, der ein vertauschtes Vorzeichen fängt: bei quadratischen Grundrissen sieht
  // ein falsches Vorzeichen richtig aus.
  //
  // **25 m und nicht 40 m — mit Grund:** bei 40 m Höhe auf 700 px bräuchte es Maßstab 0,0155, und
  // der liegt UNTER der Grenze 0,02. Dann gewinnt die Grenze (K8) und der Plan passt bewusst nicht
  // ganz hinein. Dieser Fall prüft das Vorzeichen, nicht die Grenze — deshalb Maße, die innerhalb
  // der Grenzen liegen. (Gefunden hat das der Test selbst, beim ersten Lauf.)
  const hoch = [{ x: 0, y: 0 }, { x: 2000, y: 25000 }];
  const breit = [{ x: 0, y: 0 }, { x: 25000, y: 2000 }];
  for (const p of [hoch, breit]) {
    assert.ok(einpassen(p, BREITE, HOEHE).zoom > ZOOM_MIN, 'sonst prüft dieser Fall die Grenze statt des Vorzeichens');
  }
  alleSichtbar(hoch, BREITE, HOEHE);
  alleSichtbar(breit, BREITE, HOEHE);
});

test('K6: die y-Achse ist gespiegelt — ein höherer Weltpunkt liegt WEITER OBEN auf dem Schirm', () => {
  const punkte = [{ x: 0, y: 0 }, { x: 1000, y: 20000 }];
  const e = einpassen(punkte, BREITE, HOEHE);
  const unten = aufSchirm({ x: 0, y: 0 }, e);
  const oben = aufSchirm({ x: 0, y: 20000 }, e);
  assert.ok(oben.y < unten.y, 'die Welt wächst nach oben, der Schirm nach unten — hier kippt es sonst');
});

test('die Mitte der Box landet auf der Mitte der Bühne', () => {
  const e = einpassen([{ x: 0, y: 0 }, { x: 10000, y: 6000 }], BREITE, HOEHE);
  const m = aufSchirm({ x: 5000, y: 3000 }, e);
  assert.ok(Math.abs(m.x - BREITE / 2) < 0.001);
  assert.ok(Math.abs(m.y - HOEHE / 2) < 0.001);
});

// --- K4: das leere Geschoss ---------------------------------------------------------------------
test('K4: leeres Geschoss ⇒ kein Sprung, kein Fehler — Standardmaßstab und Standardlage', () => {
  const e = einpassen([], BREITE, HOEHE);
  assert.equal(e.zoom, ZOOM_STANDARD, 'der Maßstab beim Laden');
  assert.deepEqual(e.pan, standardPan(HOEHE), 'die Standardlage folgt weiter der Bühnenhöhe');
  assert.equal(ZOOM_STANDARD, 0.12);
});

// --- K5: Split ----------------------------------------------------------------------------------
test('K5: in Split wird in die HALBE Fläche eingepasst — dort liegt der Grundriss vollständig', () => {
  const punkte = [{ x: 0, y: 0 }, { x: 20000, y: 12000 }];
  const halb = Math.floor(BREITE / 2);
  alleSichtbar(punkte, halb, HOEHE);

  // Und die Gegenprobe: die volle Breite genommen, aber in der halben angezeigt ⇒ es steht draußen.
  const falsch = einpassen(punkte, BREITE, HOEHE);
  const rechts = aufSchirm({ x: 20000, y: 0 }, falsch);
  assert.ok(rechts.x > halb, `bei voller Breite gerechnet liegt der rechte Rand bei ${rechts.x} — außerhalb von ${halb}`);
});

// --- K7: Nullfläche und Einzelknoten ------------------------------------------------------------
test('K7: ein einzelner Punkt ⇒ kein Infinity, kein NaN — Standardmaßstab', () => {
  const e = einpassen([{ x: 4000, y: 4000 }], BREITE, HOEHE);
  assert.ok(Number.isFinite(e.zoom) && Number.isFinite(e.pan.x) && Number.isFinite(e.pan.y));
  assert.equal(e.zoom, ZOOM_STANDARD, 'ohne Ausdehnung gibt es nichts einzupassen');
  // Er landet trotzdem in der Mitte — der Verschub arbeitet auch ohne Ausdehnung.
  const s = aufSchirm({ x: 4000, y: 4000 }, e);
  assert.ok(Math.abs(s.x - BREITE / 2) < 0.001 && Math.abs(s.y - HOEHE / 2) < 0.001);
});

test('K7: eine Wand ohne Ausdehnung in EINER Achse — die andere bestimmt den Maßstab', () => {
  const e = einpassen([{ x: 0, y: 0 }, { x: 0, y: 10000 }], BREITE, HOEHE);
  assert.ok(Number.isFinite(e.zoom));
  assert.ok(e.zoom >= ZOOM_MIN && e.zoom <= ZOOM_MAX);
  alleSichtbar([{ x: 0, y: 0 }, { x: 0, y: 10000 }], BREITE, HOEHE);
});

// --- K8: die Grenzen gewinnen -------------------------------------------------------------------
test('K8: ein winziger Grundriss wird NICHT über den Höchstmaßstab hinaus vergrößert', () => {
  // 10 mm Kantenlänge: rechnerisch bräuchte es Maßstab 62 — die Grenze ist 1.
  const e = einpassen([{ x: 0, y: 0 }, { x: 10, y: 10 }], BREITE, HOEHE);
  assert.equal(e.zoom, ZOOM_MAX, 'die Grenze gewinnt, sie weicht nicht auf');
});

test('K8: ein riesiger Grundriss wird NICHT unter den Kleinstmaßstab gedrückt — er passt dann nicht ganz', () => {
  const riesig = [{ x: 0, y: 0 }, { x: 5000000, y: 5000000 }];
  const e = einpassen(riesig, BREITE, HOEHE);
  assert.equal(e.zoom, ZOOM_MIN);
  // Und das wird hier ausdrücklich festgehalten: er liegt dann NICHT vollständig im Bild.
  // Das ist die bewusste Antwort des Postens — nicht ein Fehler, den jemand später „behebt",
  // indem er die Grenze lockert.
  const ecke = aufSchirm({ x: 5000000, y: 0 }, e);
  assert.ok(ecke.x > BREITE, 'bei 0,02 passt er nicht — die Grenze steht über der Vollständigkeit');
});

// --- Der Rand -----------------------------------------------------------------------------------
test('ein Rand bleibt: der Grundriss klebt nicht an der Kante', () => {
  const punkte = [{ x: 0, y: 0 }, { x: 40000, y: 30000 }];
  const e = einpassen(punkte, BREITE, HOEHE);
  const links = aufSchirm({ x: 0, y: 30000 }, e);
  const rechts = aufSchirm({ x: 40000, y: 0 }, e);
  assert.ok(links.x >= EINPASS_RAND - 0.001, `linker Rand ${links.x} < ${EINPASS_RAND}`);
  assert.ok(rechts.x <= BREITE - EINPASS_RAND + 0.001);
  assert.equal(EINPASS_RAND, 40, 'ein benannter Wert, kein Zufall');
});

// --- Die Punkte der Knoten ----------------------------------------------------------------------
function wand(id: string, x1: number, y1: number, x2: number, y2: number): WallNode {
  return {
    id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: '', updatedAt: '', start: { x: x1, y: y1 }, end: { x: x2, y: y2 },
    thickness: 240, height: 2500,
  } as WallNode;
}

test('Wände tragen beide Endpunkte bei', () => {
  assert.deepEqual(knotenPunkte([wand('w1', 0, 0, 5000, 3000)]),
    [{ x: 0, y: 0 }, { x: 5000, y: 3000 }]);
});

test('ein unbekannter Knotentyp wird ÜBERSPRUNGEN, nicht geraten', () => {
  // Öffnungen liegen als Versatz auf einer Wand und haben keine eigenen Weltkoordinaten. Sie zu
  // schätzen hieße, eine zweite Platzierungsrechnung neben der vorhandenen aufzumachen.
  const oeffnung = { id: 'o1', type: 'window', levelId: 'eg', wallId: 'w1', offset: 1000, width: 1010 } as unknown as SceneNode;
  assert.deepEqual(knotenPunkte([oeffnung]), []);
  // Die Wand, auf der sie sitzt, wird ohnehin gezählt — die Ausdehnung geht also nicht verloren.
  assert.equal(knotenPunkte([wand('w1', 0, 0, 5000, 0), oeffnung]).length, 2);
});

// --- K9/K10: nichts wird gespeichert, der Knopf ist frei ----------------------------------------
test('K9: die Einpassung berührt das Dokument nicht — kein Befehl, kein Speicherstatus', () => {
  const regel = ohneKommentare(readFileSync(join(hier, '../app/dashboard/einpassen.ts'), 'utf8'));
  for (const verboten of ['executeCommand', 'store', 'speicherStatus', 'useHausplanerStore', 'revision']) {
    assert.ok(!regel.includes(verboten), `${verboten} gehört nicht in eine Anzeige-Rechnung`);
  }
  // Und der Aufrufer setzt genau zwei Anzeige-Zustände, sonst nichts.
  const handler = ohneKommentare(quelle).match(/function passeAnsichtEin\(\): void \{[\s\S]*?\n  \}/);
  assert.ok(handler, '`passeAnsichtEin` nicht gefunden');
  assert.match(handler[0], /setZoom\(e\.zoom\);/);
  assert.match(handler[0], /setPan\(e\.pan\);/);
  assert.doesNotMatch(handler[0], /executeCommand|markiereUngespeichert|setSpeicher/);
});

test('K5 an der Verdrahtung: eingepasst wird in `stageBreite`, nicht in `breite`', () => {
  const handler = ohneKommentare(quelle).match(/function passeAnsichtEin\(\): void \{[\s\S]*?\n  \}/);
  assert.match(handler![0], /einpassen\(knotenPunkte\(nodes\), stageBreite, hoehe\)/);
});

test('K10: der Knopf ist nicht mehr `geplant`', () => {
  const q = ohneKommentare(quelle);
  const knopf = q.match(/<OpBtn title="Ansicht einpassen[^/]*\/>/);
  assert.ok(knopf, 'der Knopf ist verschwunden');
  assert.doesNotMatch(knopf[0], /geplant/, 'ein Knopf, der etwas tut, ist nicht mehr geplant');
  assert.match(knopf[0], /onClick=\{passeAnsichtEin\}/);
  assert.doesNotMatch(knopf[0], /disabled/, 'auch bei leerem Geschoss tut er etwas Definiertes');
});
