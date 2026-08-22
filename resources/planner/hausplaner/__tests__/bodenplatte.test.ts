/**
 * **Z1-E4-1 — Die Bodenplatte als eigenes Bauteil.**
 *
 * Eine Zusage je Abnahmekriterium (a) bis (g), plus die Gegenproben, die das Blatt in seinen
 * Absage-Regeln ausdrücklich verlangt. *Eine Zusage ohne Gegenprobe misst nur, dass etwas
 * passiert — nicht, dass das Falsche nicht passiert.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { applyCommand } from '../commands/applyCommand';
import { sceneDocumentSchema, migriereSzene, validateSceneIntegrity } from '../domain/validation';
import { CommandAbgelehnt } from '../domain/commands.types';
import { TOOL_PRESENTATION_RULES } from '../app/tools/toolPresentation';
import { toolNach } from '../app/tools/toolRegistry';
import { bodenplatteOberkanteMm, fussbodenaufbauDickeMm, fussbodenaufbauErfasst } from '../geometry/hoehenkette';
import { hinweisBodenplatte, istUntersteEtage, istErdberuehrtVorbelegung } from '../geometry/bodenplatte';
import { projiziereRaum } from '../projection/raumProjektion';
import { erkenneRaeume } from '../geometry/roomDetection';
import { STUDIO_FIXTURES } from '../fixtures/studioFixtures';
import { teil, ohneKommentare } from './_zerlegteApp';
import { readFileSync } from 'node:fs';
import { SCHEMA_VERSION } from '../domain/scene.types';
import type { SceneDocument, FoundationSlabNode, Level, WallNode } from '../domain/scene.types';

const ISO = '2026-08-22T00:00:00.000Z';
const EG: Level = { id: 'l1', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 };
const OG: Level = { id: 'l2', name: 'OG', elevation: 2700, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 1 };
const UMRISS = [{ x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 }];

function baseDoc(levels: Level[] = [EG]): SceneDocument {
  return {
    id: 'd', projectId: 1, schemaVersion: SCHEMA_VERSION, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels, nodes: [], materials: [], roofs: [], metadata: { createdAt: ISO, updatedAt: ISO },
  };
}
function platte(over: Partial<FoundationSlabNode> = {}): FoundationSlabNode {
  return {
    id: 'b1', type: 'foundation_slab', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, polygon: UMRISS, dickeMm: 250, oberkanteMm: -180,
    erdberuehrt: true,
    // **Pflicht seit der Entscheidung vom 22.08. 23:12** — eine erdberuehrte Platte OHNE Aufbau
    // lehnt der Command ab. Die Fabrik liefert deshalb den Normalfall; die Zusagen, die das
    // Fehlen pruefen, setzen `schichten: undefined` ausdruecklich.
    schichten: [{ dickeMm: 120 }, { dickeMm: 60 }],
    geometrieHerkunft: 'manuell', freigabe: 'bestaetigt', ...over,
  };
}

// --- (a) Die Leiste beginnt mit „Bodenplatte" ------------------------------------------------

test('Z1-E4-1-a: `bodenplatte` steht auf Platz 1 der Fix-Zone', () => {
  const fix = TOOL_PRESENTATION_RULES.filter((r) => r.zone === 'fix').sort((x, y) => x.ordnung - y.ordnung);
  assert.equal(fix[0].toolId, 'bodenplatte', 'die Leiste beginnt nicht mit der Bodenplatte');
  assert.equal(fix[0].ordnung, 1);
  // Gegenprobe: die acht Bestandswerkzeuge sind nur nachgerückt, ihre Folge zueinander steht.
  assert.deepEqual(
    fix.slice(1).map((r) => r.toolId),
    ['auswahl', 'wand', 'fenster', 'tuer', 'treppe', 'decke', 'kontur', 'dach'],
  );
});

test('Z1-E4-1-a Absage-Regel: der Eintrag traegt NICHT bauteilKind ceiling', () => {
  const t = toolNach('bodenplatte');
  assert.ok(t, 'kein Registry-Eintrag');
  // **Das ist die Absage-Regel des Blattes, wortgetreu geprüft.** Ein Eintrag auf `ceiling` würde
  // die Zwischendecke desselben Geschosses sperren — der Blocker, der Z1-W2-8-b gestrichen hat.
  assert.equal(t.bauteilKind, 'foundation_slab');
  assert.notEqual(t.bauteilKind, 'ceiling');
  // und die Decke ist unberührt geblieben
  assert.equal(toolNach('decke')?.bauteilKind, 'ceiling');
});

// --- (b) Zweite Platte im selben Geschoss --------------------------------------------------

test('Z1-E4-1-b: zweite Bodenplatte im selben Level wird abgelehnt — mit Grund', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte() }, ISO);
  assert.equal(doc.foundationSlabs?.length, 1);
  assert.throws(
    () => applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte({ id: 'b2' }) }, ISO),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'bodenplatte_pro_level_vorhanden',
  );
});

test('Z1-E4-1-b Absage-Regel: eine Platte auf einem ANDEREN Level laeuft durch (Keller bleibt moeglich)', () => {
  // **Die entscheidende Gegenprobe.** Eine gebäudeweite Sperre wäre hier grün geblieben und hätte
  // den Keller mit eigener Sohle verbaut — genau deshalb hat Yama „je Geschoss" entschieden.
  const doc = baseDoc([EG, OG]);
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte() }, ISO);
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte({ id: 'b2', levelId: 'l2' }) }, ISO);
  assert.equal(doc.foundationSlabs?.length, 2);
});

test('Z1-E4-1-b: die Zwischendecke desselben Geschosses bleibt moeglich — beide nebeneinander', () => {
  // Der Kern des Blattes: Platte und Decke im SELBEN Geschoss, keine sperrt die andere.
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte() }, ISO);
  applyCommand(doc, {
    type: 'ADD_CEILING',
    ceiling: {
      id: 'c1', type: 'ceiling', levelId: 'l1', visible: true, locked: false, tags: [],
      createdAt: ISO, updatedAt: ISO, polygon: UMRISS, dickeMm: 240,
      geometrieHerkunft: 'manuell', freigabe: 'bestaetigt',
    },
  }, ISO);
  assert.equal(doc.foundationSlabs?.length, 1);
  assert.equal(doc.ceilings?.length, 1);
});

test('Z1-E4-1-b: mm-Invariante greift, und zwar OHNE das Vorzeichen zu verbieten', () => {
  assert.throws(
    () => applyCommand(baseDoc(), { type: 'ADD_FOUNDATION_SLAB', slab: platte({ oberkanteMm: -180.5 }) }, ISO),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'nicht_ganzzahlig',
  );
  // Gegenprobe: −180 ist ganzzahlig und muss durchgehen — sonst hätte die Invariante den
  // Normalfall der erdberührten Platte abgelehnt.
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte({ oberkanteMm: -180 }) }, ISO);
  assert.equal(doc.foundationSlabs?.length, 1);
});

test('Z1-E4-1-b: UPDATE und REMOVE melden bodenplatte_unbekannt statt still zu schweigen', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte() }, ISO);
  assert.throws(
    () => applyCommand(doc, { type: 'UPDATE_FOUNDATION_SLAB', slabId: 'gibt-es-nicht', changes: { dickeMm: 300 } }, ISO),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'bodenplatte_unbekannt',
  );
  assert.throws(
    () => applyCommand(doc, { type: 'REMOVE_FOUNDATION_SLAB', slabId: 'gibt-es-nicht' }, ISO),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'bodenplatte_unbekannt',
  );
  // Und der Normalfall wirkt wirklich: UPDATE aendert, REMOVE entfernt.
  applyCommand(doc, { type: 'UPDATE_FOUNDATION_SLAB', slabId: 'b1', changes: { dickeMm: 300 } }, ISO);
  assert.equal(doc.foundationSlabs?.[0].dickeMm, 300);
  applyCommand(doc, { type: 'REMOVE_FOUNDATION_SLAB', slabId: 'b1' }, ISO);
  assert.equal(doc.foundationSlabs?.length, 0);
});

test('Z1-E4-1-b: KEINE automatischen Durchbrueche — die Abgrenzung zur Decke ist gewollt', () => {
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte() }, ISO);
  assert.equal(doc.foundationSlabs?.[0].durchbrueche, undefined);
});

// --- Nachbesserung 23:12 — der Fussbodenaufbau ist PFLICHT ----------------------------------

test('Z1-E4-1: erdberuehrte Platte OHNE Fussbodenaufbau wird abgelehnt — mit lesbarem Grund', () => {
  // **Posten 25.6 ist aufgehoben** (Dirigent 22.08. 23:12:40, in Yamas Namen): „Aufbau nicht
  // erfasst → 0 mit Vermerk" wuerde genau die Null erzeugen, die Yamas Operand ausschliesst.
  assert.throws(
    () => applyCommand(baseDoc(), { type: 'ADD_FOUNDATION_SLAB', slab: platte({ schichten: undefined, oberkanteMm: 0 }) }, ISO),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'bodenplatte_ohne_aufbau',
  );
  assert.throws(
    () => applyCommand(baseDoc(), { type: 'ADD_FOUNDATION_SLAB', slab: platte({ schichten: [], oberkanteMm: 0 }) }, ISO),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'bodenplatte_ohne_aufbau',
  );
  // Der Grund muss lesbar sein, nicht nur maschinell: die Meldung nennt die Bezugshoehe.
  try {
    applyCommand(baseDoc(), { type: 'ADD_FOUNDATION_SLAB', slab: platte({ schichten: undefined, oberkanteMm: 0 }) }, ISO);
    assert.fail('nicht abgelehnt');
  } catch (e) {
    assert.match((e as Error).message, /Fußbodenaufbau/);
    assert.match((e as Error).message, /Fertigfußboden/);
  }
});

test('Z1-E4-1: oberkanteMm >= 0 bei erdberuehrt=true wird abgelehnt — NULL EINGESCHLOSSEN', () => {
  // **Die Absage-Regel, auf die der Plan-Pruefer gezeigt hat:** „null ist nicht positiv", also
  // fiel der Wert 0 durch die alte Fassung hindurch und verletzte (e) trotzdem.
  for (const ok of [0, 1, 250]) {
    assert.throws(
      () => applyCommand(baseDoc(), { type: 'ADD_FOUNDATION_SLAB', slab: platte({ oberkanteMm: ok }) }, ISO),
      (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'bodenplatte_oberkante_nicht_negativ',
      `oberkanteMm ${ok} kam durch`,
    );
  }
  // Gegenprobe: negativ geht durch.
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte({ oberkanteMm: -1 }) }, ISO);
  assert.equal(doc.foundationSlabs?.length, 1);
});

test('Z1-E4-1: beide Regeln haengen an der ERDBERUEHRUNG, nicht am Bauteil', () => {
  // Eine Platte ueber einer Tiefgarage hat keine Bezugshoehe zum Erdreich: kein Aufbau noetig,
  // Oberkante darf >= 0 sein. Wer die Regel ans Bauteil haengt statt an erdberuehrt, verbietet
  // diesen Fall — und er ist baulich normal.
  const doc = baseDoc();
  applyCommand(doc, {
    type: 'ADD_FOUNDATION_SLAB',
    slab: platte({ erdberuehrt: false, schichten: undefined, oberkanteMm: 0 }),
  }, ISO);
  assert.equal(doc.foundationSlabs?.length, 1);
});

test('Z1-E4-1: auch UPDATE darf den Aufbau nicht wegnehmen', () => {
  // Sonst waere die Pflicht nur beim Anlegen wirksam und liesse sich in zwei Schritten umgehen.
  const doc = baseDoc();
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte() }, ISO);
  assert.throws(
    () => applyCommand(doc, { type: 'UPDATE_FOUNDATION_SLAB', slabId: 'b1', changes: { schichten: [] } }, ISO),
    (e: unknown) => e instanceof CommandAbgelehnt && e.grund === 'bodenplatte_ohne_aufbau',
  );
});

test('Z1-E4-1: die Referenzhaus-Fixture haelt die verschaerfte Regel aus', () => {
  const doc = STUDIO_FIXTURES['bodenplatte']();
  const b = doc.foundationSlabs![0];
  assert.ok(b.oberkanteMm < 0, 'die Fixture wuerde jetzt abgelehnt');
  assert.equal(fussbodenaufbauDickeMm(b.schichten), 180, 'Aufbau 180 mm, wie 23:12 verfuegt');
  assert.equal(b.dickeMm, 250);
  assert.equal(b.oberkanteMm, -180);
  // UK Platte = -180 - 250 = -430, die Rechnung des Plan-Pruefers.
  assert.equal(b.oberkanteMm - b.dickeMm, -430);
  // Und der Aufbau ist NICHT level.floorThickness — das war seine eigene Berichtigung 23:15.
  assert.notEqual(fussbodenaufbauDickeMm(b.schichten), doc.levels[0].floorThickness);
});

// --- (c) Geschoss darunter → Hinweis, kein Zwang -------------------------------------------

test('Z1-E4-1-c: liegt ein Geschoss darunter, kommt ein HINWEIS — und die Platte entsteht', () => {
  const hinweis = hinweisBodenplatte([EG, OG], 'l2');
  assert.ok(hinweis, 'kein Hinweis auf dem OG');
  assert.match(hinweis, /EG/, 'der Hinweis nennt das Geschoss nicht beim Namen');
  // **Absage-Regel: eine Ablehnung erfüllt (c) NICHT.** Der Command lässt die Platte durch.
  const doc = baseDoc([EG, OG]);
  applyCommand(doc, { type: 'ADD_FOUNDATION_SLAB', slab: platte({ levelId: 'l2' }) }, ISO);
  assert.equal(doc.foundationSlabs?.length, 1, 'die Platte wurde abgelehnt statt bemerkt');
});

test('Z1-E4-1-c Gegenprobe: auf der untersten Etage schweigt der Hinweis', () => {
  assert.equal(hinweisBodenplatte([EG, OG], 'l1'), null);
  assert.equal(hinweisBodenplatte([EG], 'l1'), null);
  // sonst wäre ein Hinweis, der immer steht, so wertlos wie keiner (A-03).
});

test('Z1-E4-1-c: unterste Etage wird ueber elevation bestimmt, nicht ueber sortOrder', () => {
  // Ein Keller, der als letzter angelegt wurde und deshalb die höchste sortOrder trägt:
  const keller: Level = { id: 'l0', name: 'KG', elevation: -2800, defaultWallHeight: 2400, floorThickness: 200, sortOrder: 9 };
  assert.equal(istUntersteEtage([EG, OG, keller], 'l0'), true, 'die sortOrder hat die Antwort verfälscht');
  assert.equal(istUntersteEtage([EG, OG, keller], 'l1'), false);
  assert.equal(istErdberuehrtVorbelegung([EG, OG, keller], 'l0'), true);
  assert.equal(istErdberuehrtVorbelegung([EG, OG, keller], 'l1'), false);
});

// --- (d) Speichern und Laden, Bestand unberuehrt -------------------------------------------

test('Z1-E4-1-d: Dokument MIT Platte validiert; Dokument OHNE Platte ebenfalls (kein 422)', () => {
  const ohne = baseDoc();
  assert.equal(sceneDocumentSchema.safeParse(ohne).success, true, 'Bestand ohne Platte faellt durch');
  const mit = { ...ohne, foundationSlabs: [platte()] };
  assert.equal(sceneDocumentSchema.safeParse(mit).success, true, 'Dokument mit Platte faellt durch');
});

test('Z1-E4-1-d: negative oberkanteMm ist schema-gueltig — mmPos waere hier falsch', () => {
  const mit = { ...baseDoc(), foundationSlabs: [platte({ oberkanteMm: -180 })] };
  assert.equal(sceneDocumentSchema.safeParse(mit).success, true);
  // Gegenprobe: eine gebrochene Zahl bleibt abgelehnt (mm-Invariante gilt weiter).
  const krumm = { ...baseDoc(), foundationSlabs: [platte({ oberkanteMm: -180.5 })] };
  assert.equal(sceneDocumentSchema.safeParse(krumm).success, false);
});

test('Z1-E4-1-d: Bestandsdokument v3 wird auf v4 gehoben — NUR Version und leeres Feld', () => {
  const v3 = {
    id: 'alt', projectId: 7, schemaVersion: 3, revision: 5, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [EG], nodes: [], materials: [], roofs: [], ceilings: [],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
  const m = migriereSzene(v3) as Record<string, unknown>;
  assert.equal(m.schemaVersion, 4);
  assert.deepEqual(m.foundationSlabs, []);
  // **Kein stilles Umschreiben:** jedes andere Feld bleibt, wie es war.
  assert.equal(m.id, v3.id);
  assert.equal(m.projectId, v3.projectId);
  assert.equal(m.revision, v3.revision);
  assert.deepEqual(m.levels, v3.levels);
  assert.deepEqual(m.nodes, v3.nodes);
  assert.deepEqual(m.materials, v3.materials);
  assert.deepEqual(m.roofs, v3.roofs);
  assert.deepEqual(m.ceilings, v3.ceilings);
  assert.deepEqual(m.settings, v3.settings);
  assert.deepEqual(m.metadata, v3.metadata);
  // Original unangetastet (reine Funktion)
  assert.equal(v3.schemaVersion, 3);
  assert.equal((v3 as Record<string, unknown>).foundationSlabs, undefined);
  // und das Migrat ist auch wirklich ladbar
  assert.equal(sceneDocumentSchema.safeParse(m).success, true);
});

test('Z1-E4-1-d: das JSON-Schema ist NACHGEZOGEN — erzeugt, nicht von Hand gepflegt', () => {
  // **Absage-Regel des Blattes:** ein von Hand gepflegtes Schema erfüllt (d) nicht. Diese Zusage
  // misst das Ergebnis des Generators; `schema:hausplaner:check` im npm-Skript misst die Gleichheit.
  const roh = readFileSync(new URL('../domain/scene-document-v2.schema.json', import.meta.url), 'utf8');
  const schema = JSON.parse(roh) as Record<string, unknown>;
  const props = (schema.properties ?? {}) as Record<string, unknown>;
  assert.ok(props.foundationSlabs, 'foundationSlabs fehlt im erzeugten JSON-Schema');
  assert.deepEqual((props.schemaVersion as Record<string, unknown>).const, 4);
  // additionalProperties bleibt zu — sonst wäre die Sammlung auch ohne Schema-Eintrag „gültig".
  assert.equal(schema.additionalProperties, false);
});

test('Z1-E4-1-d: validateSceneIntegrity meldet eine Platte auf unbekanntem Level', () => {
  const fehler = validateSceneIntegrity({
    levels: [{ id: 'l1' }], nodes: [],
    foundationSlabs: [{ id: 'b1', levelId: 'gibt-es-nicht' }],
  });
  assert.equal(fehler.length, 1);
  assert.match(fehler[0], /Bodenplatte b1/);
  // Gegenprobe: bekanntes Level ⇒ kein Fehler.
  assert.deepEqual(
    validateSceneIntegrity({ levels: [{ id: 'l1' }], nodes: [], foundationSlabs: [{ id: 'b1', levelId: 'l1' }] }),
    [],
  );
});

// --- (e) Die Hoehenkette kennt das untere Ende ---------------------------------------------

test('Z1-E4-1-e: oberkanteMm ist NEGATIV, sobald ein Fussbodenaufbau erfasst ist', () => {
  const schichten = [{ dickeMm: 120 }, { dickeMm: 60 }];
  assert.equal(fussbodenaufbauDickeMm(schichten), 180);
  assert.equal(bodenplatteOberkanteMm(schichten), -180);
  // **Absage-Regel: eine POSITIVE Oberkante bei erdberuehrt=true erfüllt (e) nicht.**
  assert.ok(bodenplatteOberkanteMm(schichten) < 0);
});

test('Z1-E4-1-e: ohne erfassten Aufbau ist 0 kein Messwert — die zweite Frage beantwortet das', () => {
  assert.equal(fussbodenaufbauDickeMm(undefined), 0);
  assert.equal(fussbodenaufbauDickeMm([]), 0);
  assert.equal(fussbodenaufbauErfasst(undefined), false);
  assert.equal(fussbodenaufbauErfasst([]), false);
  // Ein Aufbau der Dicke 0 IST eine Angabe — und muss von „nicht erfasst" unterscheidbar sein.
  assert.equal(fussbodenaufbauErfasst([{ dickeMm: 0 }]), true);
  assert.equal(fussbodenaufbauDickeMm([{ dickeMm: 0 }]), 0);
});

test('Z1-E4-1-e: ein Keller reicht seine eigene Bezugshoehe herein', () => {
  // Ohne den Parameter wäre die Funktion auf das EG festgelegt und „eine Platte je Geschoss"
  // nicht abbildbar.
  assert.equal(bodenplatteOberkanteMm([{ dickeMm: 180 }], -2800), -2980);
});

test('Z1-E4-1-e: die Referenzhaus-Fixture ist unterscheidungsfaehig und traegt die negative Kote', () => {
  const doc = STUDIO_FIXTURES['bodenplatte']();
  const b = doc.foundationSlabs?.[0];
  assert.ok(b, 'die Fixture fuehrt keine Bodenplatte');
  assert.equal(b.erdberuehrt, true);
  assert.ok(b.oberkanteMm < 0, `oberkanteMm ${b.oberkanteMm} ist nicht negativ`);
  assert.equal(b.oberkanteMm, -fussbodenaufbauDickeMm(b.schichten), 'Kote und Aufbau widersprechen sich');
  // **Der vierte Befund des Plan-Prüfers, als Zusage:** keine zwei Groessen duerfen gleich sein,
  // sonst kann eine Verwechslung zufaellig gruen werden.
  const werte = [b.dickeMm, fussbodenaufbauDickeMm(b.schichten), doc.levels[0].floorThickness, doc.levels[0].defaultWallHeight];
  assert.equal(new Set(werte).size, werte.length, `nicht unterscheidungsfaehig: ${werte.join(' · ')}`);
  assert.equal(sceneDocumentSchema.safeParse(doc).success, true, 'die Fixture ist nicht speicherbar');
});

// --- (f) Grenzflaeche Erdreich ---------------------------------------------------------------

function waendeAusUmriss(): WallNode[] {
  return UMRISS.map((p, i) => ({
    id: `w${i}`, type: 'wall' as const, levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO,
    start: p, end: UMRISS[(i + 1) % UMRISS.length], thickness: 240, height: 2500,
  }));
}

test('Z1-E4-1-f: die Projektion liefert die Grenzflaeche erdreich', () => {
  const waende = waendeAusUmriss();
  const raeume = erkenneRaeume(waende);
  assert.ok(raeume.length > 0, 'kein Raum erkannt — die Zusage haette Leere geprueft');
  const p = projiziereRaum(raeume[0], raeume, waende, 0, 2500, { erdberuehrt: true });
  assert.deepEqual(p.boden, { grenzflaeche: 'erdreich', bauteil_typ: 'boden' });
  // **Absage-Regel: kein aus der Decke abgeleiteter Wert.** Die Decke bleibt unbestimmt.
  assert.equal(p.decke, null);
});

test('Z1-E4-1-f Gegenprobe: ohne Platte bleibt boden null — und ohne Erdberuehrung auch', () => {
  const waende = waendeAusUmriss();
  const raeume = erkenneRaeume(waende);
  assert.equal(projiziereRaum(raeume[0], raeume, waende, 0, 2500).boden, null, 'boden erfunden');
  assert.equal(
    projiziereRaum(raeume[0], raeume, waende, 0, 2500, { erdberuehrt: false }).boden, null,
    'eine nicht erdberuehrte Platte hat eine Grenzflaeche erfunden',
  );
});

// --- (g) Das Panel behauptet nichts, was nicht geprueft ist ---------------------------------

test('Z1-E4-1-g: im Bodenplatten-Panel kommt das Wort „geprueft" NICHT vor', () => {
  const quelle = ohneKommentare(teil('app/rahmen/BodenplattenPanel.tsx'));
  // Wortprobe auf den ANGEZEIGTEN Text, Kommentare abgezogen — ein Kommentar ist keine Behauptung
  // gegenüber dem Benutzer, und die Datei erklärt dort ausdrücklich, warum das Wort fehlt.
  assert.doesNotMatch(quelle, /geprüft|geprueft/i, 'das Panel behauptet eine Pruefung');
  // Gegenprobe, damit die Zusage nicht Leere misst: die Datei wurde wirklich gelesen.
  assert.match(quelle, /Bodenplatte/);
});

test('Z1-E4-1-g: das Panel fuehrt Dicke, Hoehenlage, erdberuehrt und den Aufbau — mit Herkunft', () => {
  const quelle = teil('app/rahmen/BodenplattenPanel.tsx');
  for (const feld of ['dickeMm', 'oberkanteMm', 'erdberuehrt', 'aufbau']) {
    assert.match(quelle, new RegExp(`data-feld="${feld}"`), `${feld} fehlt im Panel`);
  }
  assert.match(quelle, /Aufbau nicht erfasst/, 'der Vermerk aus Kriterium (e) fehlt');
  assert.match(quelle, /hp-feld-herkunft/, 'kein Wert nennt seine Herkunft');
  // **Bewehrung kommt nicht vor** — kein Feld, kein Platzhalter. Ein leeres Bewehrungsfeld
  // sähe nach Bemessung aus, und die ist nicht Gegenstand dieses Blattes.
  assert.doesNotMatch(ohneKommentare(quelle), /bewehr/i);
});
