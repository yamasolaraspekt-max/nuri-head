/**
 * I3 (AUF-21) — die sechs Werkzeug-Zustände aus `dashboard-tools-v1.html`.
 *
 * Geprüft wird vor allem die **Reihenfolge** der Zustände. Sie ist der eigentliche Inhalt: ob ein
 * angehefteter, aber unbenutzbarer Knopf den Stern oder den Grund zeigt, entscheidet darüber, ob
 * der Nutzer versteht, warum nichts passiert.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  werkzeugAnzeige, darfAngeheftetWerden, istPflichtwerkzeug, sperrGrund,
  ANZEIGE_ZEICHEN, ANZEIGE_TEXT, type WerkzeugAnzeige, type ZustandKontext,
} from '../app/tools/werkzeugZustand';
import { toolNach } from '../app/tools/toolRegistry';
import { katalogTool } from '../app/tools/toolCatalog';
import { TOOL_PRESENTATION_RULES } from '../app/tools/toolPresentation';

const AN = { enabled: true, reason: null };
const AUS = { enabled: false, reason: 'Wände werden im Architektur-Arbeitsbereich gezeichnet.' };
const leer = (): ZustandKontext => ({ aktivId: null, angeheftet: new Set(), empfohlen: new Set(), aktivierung: AN });

test('alle sechs Zustände haben Zeichen UND Klartext — nie nur ein Symbol', () => {
  const alle: WerkzeugAnzeige[] = ['system', 'aktiv', 'gesperrt', 'angeheftet', 'empfohlen', 'weitere'];
  for (const z of alle) {
    assert.ok(ANZEIGE_ZEICHEN[z].length > 0, `${z}: Zeichen fehlt`);
    assert.ok(ANZEIGE_TEXT[z].length > 10, `${z}: Klartext fehlt oder ist zu dünn`);
  }
  assert.equal(new Set(Object.values(ANZEIGE_ZEICHEN)).size, 6, 'zwei Zustände mit demselben Zeichen');
});

test('aktiv schlägt alles — auch Anheftung und Pflicht', () => {
  const wand = toolNach('wand')!;
  const k: ZustandKontext = { ...leer(), aktivId: 'wand', angeheftet: new Set(['wand']) };
  assert.equal(werkzeugAnzeige(wand, k), 'aktiv');
});

test('gesperrt schlägt angeheftet — der Grund ist wichtiger als der Stern', () => {
  const wand = toolNach('wand')!;
  const k: ZustandKontext = { ...leer(), angeheftet: new Set(['wand']), aktivierung: AUS };
  assert.equal(werkzeugAnzeige(wand, k), 'gesperrt');
  assert.equal(sperrGrund(k), AUS.reason, 'der Grund muss abrufbar sein, nicht nur die Farbe');
});

test('ein Pflichtwerkzeug ohne Voraussetzung ist gesperrt, nicht stumm', () => {
  const wand = toolNach('wand')!;
  assert.equal(werkzeugAnzeige(wand, { ...leer(), aktivierung: AUS }), 'gesperrt');
});

test('system schlägt angeheftet — Pflicht bleibt Pflicht', () => {
  const auswahl = toolNach('auswahl')!;
  assert.equal(werkzeugAnzeige(auswahl, { ...leer(), angeheftet: new Set(['auswahl']) }), 'system');
});

test('angeheftet schlägt empfohlen — die eigene Entscheidung vor dem Vorschlag', () => {
  const wall = katalogTool('raum')!;
  const k: ZustandKontext = { ...leer(), angeheftet: new Set(['raum']), empfohlen: new Set(['raum']) };
  assert.equal(werkzeugAnzeige(wall, k), 'angeheftet');
});

test('ohne alles: weitere — im Überlauf, über die Befehlspalette erreichbar', () => {
  const room = katalogTool('gaube')!;
  assert.equal(werkzeugAnzeige(room, leer()), 'weitere');
});

test('Pflichtwerkzeuge lassen sich nicht anheften — ein Stern ohne Wirkung wäre gelogen', () => {
  for (const id of ['auswahl', 'wand', 'dach']) {
    assert.equal(darfAngeheftetWerden(id), false, `${id} ist ohnehin dauerhaft sichtbar`);
    assert.equal(istPflichtwerkzeug(id), true);
  }
});

test('Paket-Werkzeuge sind anheftbar — canPin kommt aus dem Paket, nicht aus einer Annahme', () => {
  assert.equal(darfAngeheftetWerden('gaube'), true);
  const mitFlag = TOOL_PRESENTATION_RULES.filter((r) => r.anheftbar === true).length;
  assert.equal(mitFlag, 101, 'alle 101 Paket-Regeln tragen das Flag aus dem Paket');
});

test('die fünf primary-Werkzeuge des Pakets sind Pflichtwerkzeuge', () => {
  const primary = TOOL_PRESENTATION_RULES.filter((r) => r.prioritaet === 'primary');
  // Nach AUF-31 sind drei der fünf primary-Werkzeuge in die Registry zusammengeführt
  // (Auswahl, Wand, …) — sie sind dort ohnehin Pflicht. Übrig: 2 im Katalog.
  assert.equal(primary.length, 2, 'gemessen nach der Zusammenführung');
  for (const r of primary) assert.equal(istPflichtwerkzeug(r.toolId), true);
});

test('unbekannte id: keine Anheftung, keine Pflicht, kein Wurf', () => {
  assert.equal(darfAngeheftetWerden('gibt-es-nicht'), false);
  assert.equal(istPflichtwerkzeug('gibt-es-nicht'), false);
});
