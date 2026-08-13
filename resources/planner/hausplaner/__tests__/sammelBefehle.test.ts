/**
 * A-31 — **eine Operation ist EIN Undo-Schritt.**
 *
 * ---
 *
 * **Was hier geprüft wird und warum es rot werden kann.** Vor A-31 rief jede der fünf Operationen
 * `executeCommand` in einer Schleife, und jeder Durchgang schrieb einen eigenen Historien-Eintrag.
 * Ein Undo nach dem Spiegeln drehte **eine** Wand zurück. Jede Zusage hier fährt die echte
 * Befehlsliste gegen den echten Store und misst danach **die Zahl der Historien-Einträge** und
 * **den Zustand nach genau EINEM Undo** — nicht den Text der Hauptansicht.
 *
 * **Jede Operation wird mit MINDESTENS ZWEI betroffenen Knoten gefahren.** *Bei einem Knoten wäre
 * die alte Fassung zufällig auch richtig, und die Zusage bewiese nichts* (A-31-1, wörtlich).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { useHausplanerStore } from '../store/hausplanerStore';
import {
  befehleLoeschen,
  befehleDuplizieren,
  befehleSpiegeln,
  befehleGeschossDuplizieren,
} from '../app/sammelBefehle';
import type { SceneDocument, WallNode, OpeningNode, SceneNode, Level } from '../domain/scene.types';

const JETZT = '2026-08-13T12:00:00.000Z';

function wand(id: string, x1: number, x2: number, y = 0): WallNode {
  return {
    id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start: { x: x1, y }, end: { x: x2, y }, thickness: 240, height: 2500,
  };
}

function fenster(id: string, wallId: string, offset = 1000): OpeningNode {
  return {
    id, type: 'window', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    hostWallId: wallId, offsetFromWallStart: offset, width: 1200, height: 1400, sillHeight: 900,
  };
}

function szene(nodes: SceneNode[]): SceneDocument {
  return {
    id: 'a31', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes, roofs: [], materials: [], metadata: { createdAt: JETZT, updatedAt: JETZT },
  } as unknown as SceneDocument;
}

const store = useHausplanerStore;

/**
 * **Startet jede Zusage mit LEERER Historie und frischer Szene.**
 *
 * *Der Store hält seine `Historie` als Modul-Singleton, und `init` leert sie NICHT — es merkt sich
 * nur den Speicherstand.* **Ohne diesen Schnitt wäre jede Zusage vom Ausgang der vorigen abhängig**,
 * und der Zähler unten zählte fremde Einträge mit. *Aufgefallen ist es an der Fangprobe: bei
 * mutiertem Store wurden auch zwei Zusagen rot, die die Mutation gar nicht berührt — ein Test, der
 * aus dem falschen Grund rot wird, wird auch aus dem falschen Grund grün.*
 *
 * Erst leeren, dann `init` — die inversen Patches der Vorgänger dürfen nie auf die neue Szene
 * treffen.
 */
function frischerStand(scene: SceneDocument): void {
  while (store.getState().kannUndo()) {
    store.getState().undo();
  }
  store.getState().init(scene, '', '');
}

/**
 * **Die Zahl der Historien-Einträge — am Zähler erhoben, nicht am Gefühl** (A-31-3).
 *
 * `Historie` gibt ihren Zähler nicht heraus, und A-31 fasst `history.ts` ausdrücklich nicht an.
 * Also wird gezählt, wie viele Undo-Schritte verfügbar sind, und anschließend **derselbe Weg
 * zurückgefahren**. *Das ist verlustfrei: `redo` legt jeden Eintrag zurück, und dazwischen wird
 * nichts ausgeführt — nur ein neuer Befehl würde den Redo-Stapel verwerfen.* Nach
 * {@link frischerStand} sind es ausschließlich die Einträge dieser einen Zusage.
 */
function historienTiefe(): number {
  let n = 0;
  while (store.getState().kannUndo()) {
    store.getState().undo();
    n += 1;
  }
  for (let i = 0; i < n; i += 1) {
    store.getState().redo();
  }

  return n;
}

/** Der Szeneninhalt als Text — für „byte-identisch wiederhergestellt". */
function abbild(): string {
  return JSON.stringify(store.getState().scene);
}

test('A-31-1/3 spiegeleGrundriss: ZWEI Wände, EIN Historien-Eintrag, EIN Undo stellt beides her', () => {
  frischerStand(szene([wand('w1', 0, 4000), wand('w2', 6000, 9000)]));
  const vorher = abbild();
  const tiefeVorher = historienTiefe();

  const waende = store.getState().scene!.nodes.filter((n): n is WallNode => n.type === 'wall');
  assert.equal(waende.length, 2, 'die Zusage braucht ZWEI Wände, sonst prüft sie nichts');
  const befehle = befehleSpiegeln(waende, 'vertikal');
  assert.equal(befehle.length, 2, 'eine Befehlsliste über beide Wände');
  assert.equal(store.getState().executeCommands(befehle), true);

  // BEIDE Wände sind bewegt — sonst misst die Zusage die alte Schleife nicht.
  const nachher = store.getState().scene!.nodes.filter((n): n is WallNode => n.type === 'wall');
  assert.notEqual(nachher[0].start.x, 0, 'erste Wand gespiegelt');
  assert.notEqual(nachher[1].start.x, 6000, 'zweite Wand gespiegelt');

  assert.equal(historienTiefe() - tiefeVorher, 1, 'GENAU EIN Historien-Eintrag für zwei Wände');
  store.getState().undo();
  assert.equal(abbild(), vorher, 'EIN Undo stellt den ganzen Grundriss wieder her');
});

test('A-31-1/3 loescheAuswahl: ZWEI Wände, EIN Historien-Eintrag, EIN Undo bringt beide zurück', () => {
  frischerStand(szene([wand('w1', 0, 4000), wand('w2', 6000, 9000)]));
  const vorher = abbild();
  const tiefeVorher = historienTiefe();

  const nodes = store.getState().scene!.nodes;
  const befehle = befehleLoeschen(['w1', 'w2'], nodes);
  assert.equal(befehle.length, 2);
  assert.equal(store.getState().executeCommands(befehle), true);
  assert.equal(store.getState().scene!.nodes.length, 0, 'beide Wände weg');

  assert.equal(historienTiefe() - tiefeVorher, 1, 'GENAU EIN Historien-Eintrag');
  store.getState().undo();
  assert.equal(abbild(), vorher, 'EIN Undo bringt beide Wände zurück');
});

test('A-31 Kante: Wand UND ihr Fenster ausgewählt — die Löschung läuft durch, statt am Kaskaden-Rest zu scheitern', () => {
  // DER FALL, DEN „alles oder nichts" ERST ERZEUGT: REMOVE_NODE auf eine Wand nimmt ihre
  // Öffnungen mit (applyCommand.ts:168). Ein zweiter REMOVE_NODE auf genau diese Öffnung
  // trifft einen Knoten, den es nicht mehr gibt -> CommandAbgelehnt 'node_unbekannt'.
  // VORHER fing das jeder Aufruf einzeln ab und die Schleife lief weiter.
  // MIT der Klammer wuerde derselbe Fall die GANZE Loeschung verwerfen — der Benutzer drueckt
  // Entf und es passiert nichts. Ohne den Filter in befehleLoeschen wird diese Zusage ROT.
  frischerStand(szene([wand('w1', 0, 4000), fenster('f1', 'w1')]));
  const vorher = abbild();
  const tiefeVorher = historienTiefe();

  const nodes = store.getState().scene!.nodes;
  const befehle = befehleLoeschen(['w1', 'f1'], nodes);
  assert.equal(befehle.length, 1, 'das Fenster faellt heraus — die Wand nimmt es ohnehin mit');

  assert.equal(store.getState().executeCommands(befehle), true, 'die Loeschung wird NICHT abgelehnt');
  assert.equal(store.getState().scene!.nodes.length, 0, 'Wand UND Fenster sind weg');
  assert.equal(store.getState().letzteAblehnung, null, 'keine Ablehnung');

  assert.equal(historienTiefe() - tiefeVorher, 1);
  store.getState().undo();
  assert.equal(abbild(), vorher, 'EIN Undo bringt Wand und Fenster zurück');
});

test('A-31-1/3 dupliziere: ZWEI Knoten, EIN Historien-Eintrag, EIN Undo entfernt beide Kopien', () => {
  frischerStand(szene([wand('w1', 0, 4000), wand('w2', 6000, 9000)]));
  const vorher = abbild();
  const tiefeVorher = historienTiefe();

  const nodes = store.getState().scene!.nodes;
  const waende = nodes.filter((n): n is WallNode => n.type === 'wall');
  let zaehler = 0;
  const { befehle, neueIds } = befehleDuplizieren(['w1', 'w2'], nodes, waende, () => `kopie-${++zaehler}`, JETZT);
  assert.deepEqual(neueIds, ['kopie-1', 'kopie-2']);
  assert.equal(store.getState().executeCommands(befehle), true);
  assert.equal(store.getState().scene!.nodes.length, 4, 'zwei Kopien entstanden');

  assert.equal(historienTiefe() - tiefeVorher, 1, 'GENAU EIN Historien-Eintrag für zwei Kopien');
  store.getState().undo();
  assert.equal(abbild(), vorher, 'EIN Undo entfernt beide Kopien');
});

test('A-31-1/3 dupliziereGeschoss: Geschoss mit ZWEI Wänden, EIN Historien-Eintrag, EIN Undo', () => {
  frischerStand(szene([wand('w1', 0, 4000), wand('w2', 6000, 9000)]));
  const vorher = abbild();
  const tiefeVorher = historienTiefe();

  const neuesLevel: Level = {
    id: 'og', name: 'OG', elevation: 2700, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 1,
  };
  const befehle = befehleGeschossDuplizieren({
    level: neuesLevel,
    nodes: [{ ...wand('k1', 0, 4000), levelId: 'og' }, { ...wand('k2', 6000, 9000), levelId: 'og' }],
    roof: null,
  });
  // Vorher waren das N+2 Schritte fuer EIN Geschoss; hier ist N=2, ohne Dach also 3 Befehle.
  assert.equal(befehle.length, 3);
  assert.equal(befehle[0].type, 'ADD_LEVEL', 'das Geschoss zuerst — die Knoten zeigen darauf');
  assert.equal(store.getState().executeCommands(befehle), true);
  assert.equal(store.getState().scene!.levels.length, 2);

  assert.equal(historienTiefe() - tiefeVorher, 1, 'GENAU EIN Historien-Eintrag für Geschoss + zwei Wände');
  store.getState().undo();
  assert.equal(abbild(), vorher, 'EIN Undo nimmt Geschoss UND Wände zurück');
});

test('A-31-2 alles oder nichts: der dritte Befehl wird abgelehnt — die Szene bleibt UNVERÄNDERT', () => {
  frischerStand(szene([wand('w1', 0, 4000), wand('w2', 6000, 9000)]));
  const vorher = abbild();
  const tiefeVorher = historienTiefe();

  const ok = store.getState().executeCommands([
    { type: 'MOVE_NODE', nodeId: 'w1', position: { start: { x: 100, y: 0 }, end: { x: 4100, y: 0 } } },
    { type: 'MOVE_NODE', nodeId: 'w2', position: { start: { x: 6100, y: 0 }, end: { x: 9100, y: 0 } } },
    { type: 'MOVE_NODE', nodeId: 'gibt-es-nicht', position: { start: { x: 0, y: 0 }, end: { x: 1, y: 0 } } },
  ]);

  assert.equal(ok, false, 'Rückgabewert false');
  assert.equal(abbild(), vorher, 'KEIN Zwischenzustand — auch die ersten zwei sind nicht gewandert');
  assert.equal(historienTiefe(), tiefeVorher, 'kein Historien-Eintrag für einen verworfenen Durchlauf');
  assert.match(store.getState().letzteAblehnung ?? '', /existiert nicht/, 'letzteAblehnung ist gesetzt');
});

test('A-31 leere Liste: nichts passiert, kein Undo-Schritt, Rückgabe true', () => {
  frischerStand(szene([wand('w1', 0, 4000)]));
  const vorher = abbild();
  const tiefeVorher = historienTiefe();

  assert.equal(store.getState().executeCommands([]), true);
  assert.equal(abbild(), vorher);
  assert.equal(historienTiefe(), tiefeVorher, 'eine Operation ohne Auswahl hinterlässt keinen Undo-Schritt');
});

test('A-31-6 executeCommand bleibt der Sonderfall EINER Liste — ein Befehl, ein Schritt', () => {
  frischerStand(szene([wand('w1', 0, 4000)]));
  const tiefeVorher = historienTiefe();

  assert.equal(store.getState().executeCommand({ type: 'ADD_NODE', node: wand('w2', 6000, 9000) }), true);
  assert.equal(historienTiefe() - tiefeVorher, 1, 'ein einzelner Befehl schreibt weiterhin genau EINEN Eintrag');
  assert.equal(store.getState().scene!.nodes.length, 2);

  // Und die Ablehnung eines einzelnen Befehls verhaelt sich wie vorher.
  assert.equal(store.getState().executeCommand({ type: 'REMOVE_NODE', nodeId: 'gibt-es-nicht' }), false);
  assert.match(store.getState().letzteAblehnung ?? '', /existiert nicht/);
});

test('A-31-6 dupliziere: die Öffnungs-Versetzung bleibt erhalten und klemmt am Wandende', () => {
  // A-31-6 nennt diese Rechnung ausdruecklich: offsetFromWallStart + width + 100, geklemmt auf
  // [0, laenge - width]. Sie ist mit der Befehlsliste umgezogen und darf sich dabei nicht
  // veraendert haben — deshalb steht sie hier mit Zahlen und nicht als Verweis.
  const w = wand('w1', 0, 6000);
  const f = fenster('f1', 'w1', 1000); // width 1200
  const nodes: SceneNode[] = [w, f];

  const { befehle } = befehleDuplizieren(['f1'], nodes, [w], () => 'kopie', JETZT);
  assert.equal(befehle.length, 1);
  const neu = (befehle[0] as { node: OpeningNode }).node;
  assert.equal(neu.offsetFromWallStart, 2300, '1000 + 1200 + 100 — unveraendert weitergerueckt');
  assert.equal(neu.hostWallId, 'w1', 'die Kopie haengt an derselben Wand');
  assert.equal(neu.id, 'kopie');

  // Und am Wandende wird geklemmt, nicht darueber hinaus geschoben.
  const spaet = fenster('f2', 'w1', 5000);
  const { befehle: b2 } = befehleDuplizieren(['f2'], [w, spaet], [w], () => 'kopie2', JETZT);
  const neu2 = (b2[0] as { node: OpeningNode }).node;
  assert.equal(neu2.offsetFromWallStart, 4800, 'geklemmt auf laenge - width = 6000 - 1200');
});

test('A-31 befehleLoeschen: eine unbekannte id wird NICHT stillschweigend geschluckt', () => {
  // Sie war auch vorher eine Ablehnung. Sie hier herauszufiltern hiesse, einen Fehler zu
  // verstecken — der Filter gilt AUSSCHLIESSLICH der Kaskade Wand -> eigene Oeffnung.
  const nodes: SceneNode[] = [wand('w1', 0, 4000), fenster('f1', 'w1')];
  assert.deepEqual(
    befehleLoeschen(['w1', 'gibt-es-nicht'], nodes).map((b) => (b as { nodeId: string }).nodeId),
    ['w1', 'gibt-es-nicht'],
  );
  // Und eine Oeffnung, deren Wand NICHT mitgeloescht wird, bleibt in der Liste.
  assert.deepEqual(
    befehleLoeschen(['f1'], nodes).map((b) => (b as { nodeId: string }).nodeId),
    ['f1'],
  );
});
