/**
 * AUF-48 Scheibe 2 — die ausgelagerten Ableitungen, jetzt verriegelt.
 *
 * **Der Befund aus Scheibe 1 hat sich wiederholt, und das Blatt hat ihn vorhergesagt:** vor dem
 * Schreiben dieser Datei wurden alle acht Ableitungen einzeln mutiert — **acht von acht ohne eine
 * einzige rote Zusage** unter 1456 Tests. Die Rechenwege waren in `HausplanerApp.tsx` vollständig
 * ungeprüft, und sie wären es nach dem Umzug geblieben.
 *
 * **Was hier NICHT geprüft wird:** die `useMemo`-Hüllen und ihre Abhängigkeitslisten. Die sind
 * React-Bindung und stehen weiterhin in der Komponente; `ansichtBereit.test.ts` und `rechte.test.ts`
 * wachen dort über sie.
 *
 * **Die Fachlogik steht nicht zur Debatte.** Diese Zusagen halten fest, was die Funktionen HEUTE
 * tun — sie sind beim Umzug unverändert geblieben.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  knotenImGeschoss, waendeAus, raeumeAus, leisteMitAngehefteten, werkzeugKontextAus,
  ermittleWegweiser, fremderBereichVon, palettenGruppenFuer,
} from '../app/ableitungen';
import type { Level, SceneDocument, SceneNode, WallNode } from '../domain/scene.types';
// **Die Konstanten importiert, nicht die Zeichenketten getippt.** Mein erster Entwurf schrieb
// `'level.hasWalls'` hin — die Konstante heisst `hostWall.exists`. Eine Zusage, die den Namen
// abschreibt statt ihn zu lesen, prueft die eigene Erinnerung.
import {
  FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_WAND_DA, FAEHIGKEIT_ANSICHT_BEREIT,
} from '../app/tools/vorbedingungen';

// --- Fixtures -----------------------------------------------------------------------------------

const BASIS = {
  visible: true, locked: false, tags: [],
  createdAt: '2026-07-30T00:00:00.000Z', updatedAt: '2026-07-30T00:00:00.000Z',
} as const;

const geschoss = (id = 'L'): Level => ({
  id, name: 'EG', elevation: 0, sortOrder: 0, defaultWallHeight: 2500, floorThickness: 200,
});

const wand = (id: string, levelId: string, sx: number, sy: number, ex: number, ey: number): WallNode => ({
  ...BASIS, id, type: 'wall', levelId, start: { x: sx, y: sy }, end: { x: ex, y: ey },
  thickness: 240, height: 2500,
});

/** Ein Quadrat 5×5 m im Geschoss `L` — vier Wände, also ein geschlossener Raum. */
const VIER_WAENDE: WallNode[] = [
  wand('u', 'L', 0, 0, 5000, 0),
  wand('r', 'L', 5000, 0, 5000, 5000),
  wand('o', 'L', 5000, 5000, 0, 5000),
  wand('l', 'L', 0, 5000, 0, 0),
];

const szene = (nodes: SceneNode[]): SceneDocument => ({
  id: 'doc-1', projectId: 1, schemaVersion: 2, units: 'mm', revision: 1,
  settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
  levels: [geschoss('L'), geschoss('L2')],
  nodes, materials: [], roofs: [],
  metadata: { createdAt: '2026-07-30T00:00:00.000Z', updatedAt: '2026-07-30T00:00:00.000Z' },
} as SceneDocument);

const eingaben = (ueber: Partial<Parameters<typeof werkzeugKontextAus>[0]> = {}) => ({
  workspace: 'architektur', view: '2d', selectedNodeIds: [], nodes: [], rechte: [],
  hatSzene: true, hatGeschoss: true, wandZahl: 4, stageBreite: 800, ...ueber,
});

// --- knotenImGeschoss ----------------------------------------------------------------------------

test('knotenImGeschoss liefert NUR die Knoten des aktiven Geschosses', () => {
  // Der Kern: ein Knoten aus einem anderen Geschoss darf nicht mitkommen — sonst zeichnete der
  // Planer fremde Wände in den Grundriss.
  const fremd = wand('fremd', 'L2', 0, 0, 100, 0);
  const knoten = knotenImGeschoss(szene([...VIER_WAENDE, fremd]), geschoss('L'));
  assert.deepEqual(knoten.map((n) => n.id), ['u', 'r', 'o', 'l']);
});

test('knotenImGeschoss: ohne Szene oder ohne Geschoss ist es leer, nie null', () => {
  // Ein `null` hier zwänge jeden Aufrufer zu einer eigenen Prüfung — und der erste, der sie
  // vergisst, bekommt einen Absturz statt einer leeren Fläche.
  assert.deepEqual(knotenImGeschoss(null, geschoss('L')), []);
  assert.deepEqual(knotenImGeschoss(szene(VIER_WAENDE), null), []);
});

// --- waendeAus -----------------------------------------------------------------------------------

test('waendeAus filtert Wände heraus — und nur Wände', () => {
  const objekt = { ...BASIS, id: 'obj', type: 'object', levelId: 'L' } as unknown as SceneNode;
  const gemischt = [...VIER_WAENDE, objekt];
  assert.deepEqual(waendeAus(gemischt).map((w) => w.id), ['u', 'r', 'o', 'l']);
});

// --- raeumeAus -----------------------------------------------------------------------------------

test('raeumeAus erkennt aus vier geschlossenen Wänden EINEN Raum', () => {
  const raeume = raeumeAus(VIER_WAENDE, geschoss('L'));
  assert.equal(raeume.length, 1);
  // 5 m × 5 m = 25 m² = 25 000 000 mm² (Achsmaß, wie die Raumerkennung es liefert).
  assert.equal(Math.round(raeume[0].flaecheMm2 / 1_000_000), 25);
});

test('raeumeAus: ohne Geschoss keine Räume — die Erkennung braucht eine Wandhöhe', () => {
  assert.deepEqual(raeumeAus(VIER_WAENDE, null), []);
});

// --- leisteMitAngehefteten ------------------------------------------------------------------------

test('leisteMitAngehefteten: die festen Werkzeuge stehen VORNE, Angeheftetes dahinter', () => {
  // Die Reihenfolge ist die Aussage: wer ein Werkzeug anheftet, schiebt es nicht vor die
  // Pflichtwerkzeuge — sonst wandert die Leiste unter der Hand des Nutzers.
  const ohne = leisteMitAngehefteten(new Set());
  const mit = leisteMitAngehefteten(new Set(['bemassen']));
  assert.deepEqual(mit.slice(0, ohne.length).map((w) => w.id), ohne.map((w) => w.id));
  assert.equal(mit.length, ohne.length + 1);
  assert.equal(mit[mit.length - 1].id, 'bemassen');
});

test('leisteMitAngehefteten: ein bereits festes Werkzeug wird NICHT verdoppelt', () => {
  const ohne = leisteMitAngehefteten(new Set());
  const doppelt = leisteMitAngehefteten(new Set([ohne[0].id]));
  assert.equal(doppelt.length, ohne.length, `${ohne[0].id} steht zweimal in der Leiste`);
});

// --- werkzeugKontextAus ---------------------------------------------------------------------------

test('werkzeugKontextAus: jede der vier Fähigkeiten hängt an ihrer EIGENEN Bedingung', () => {
  // Je Fähigkeit: einmal da, einmal fort — und die drei anderen bleiben davon unberührt. Eine
  // Bedingung, die zwei Fähigkeiten gleichzeitig umlegt, wäre ein verkabelter Schalter.
  const faelle = [
    { faehigkeit: FAEHIGKEIT_PROJEKT_OFFEN, aus: { hatSzene: false } },
    { faehigkeit: FAEHIGKEIT_GESCHOSS_DA, aus: { hatGeschoss: false } },
    { faehigkeit: FAEHIGKEIT_WAND_DA, aus: { wandZahl: 0 } },
    { faehigkeit: FAEHIGKEIT_ANSICHT_BEREIT, aus: { stageBreite: 0 } },
  ];
  const alle = werkzeugKontextAus(eingaben()).capabilities;
  for (const f of faelle) {
    assert.ok(alle.includes(f.faehigkeit), `${f.faehigkeit} fehlt, obwohl alle Bedingungen erfüllt sind`);
  }
  for (const f of faelle) {
    const ohne = werkzeugKontextAus(eingaben(f.aus)).capabilities;
    assert.ok(!ohne.includes(f.faehigkeit), `${f.faehigkeit} steht trotz abgeschalteter Bedingung`);
    for (const andere of faelle.filter((x) => x !== f)) {
      assert.ok(ohne.includes(andere.faehigkeit), `${f.faehigkeit} hat ${andere.faehigkeit} mit umgelegt`);
    }
  }
});

test('werkzeugKontextAus: die Schwelle der Zeichenfläche ist ECHT grösser 0, nicht grösser-gleich', () => {
  // AUF-42: „Null ist die einzige Grenze, die nicht ausgedacht ist." Bei 0 px gibt es keine Fläche.
  assert.ok(!werkzeugKontextAus(eingaben({ stageBreite: 0 })).capabilities.includes(FAEHIGKEIT_ANSICHT_BEREIT));
  assert.ok(werkzeugKontextAus(eingaben({ stageBreite: 1 })).capabilities.includes(FAEHIGKEIT_ANSICHT_BEREIT));
});

test('werkzeugKontextAus: dieselbe Schwelle gilt für die Wandzahl', () => {
  assert.ok(!werkzeugKontextAus(eingaben({ wandZahl: 0 })).capabilities.includes(FAEHIGKEIT_WAND_DA));
  assert.ok(werkzeugKontextAus(eingaben({ wandZahl: 1 })).capabilities.includes(FAEHIGKEIT_WAND_DA));
});

test('werkzeugKontextAus reicht die Rechte durch, ohne eines zu setzen', () => {
  assert.deepEqual(werkzeugKontextAus(eingaben({ rechte: ['Hausplaner,update'] })).permissions, ['Hausplaner,update']);
  assert.deepEqual(werkzeugKontextAus(eingaben()).permissions, []);
});

test('werkzeugKontextAus liest die Auswahltypen aus den Knoten — nicht aus den ids', () => {
  // Eine id, zu der es keinen Knoten gibt, darf keinen Typ erzeugen: sonst entstünde eine
  // Auswahl-Art aus einer Karteileiche.
  const mitWand = werkzeugKontextAus(eingaben({ selectedNodeIds: ['u'], nodes: VIER_WAENDE }));
  assert.deepEqual(mitWand.selection.types, ['wall']);
  const gespenst = werkzeugKontextAus(eingaben({ selectedNodeIds: ['gibt-es-nicht'], nodes: VIER_WAENDE }));
  assert.deepEqual(gespenst.selection.types, []);
});

// --- ermittleWegweiser ----------------------------------------------------------------------------

test('ermittleWegweiser schweigt, wenn alles da ist — er drängt nicht ohne Anlass', () => {
  // Mit allen vier Fähigkeiten und einer Auswahl gibt es keinen benannten nächsten Schritt.
  const kontext = werkzeugKontextAus(eingaben({ selectedNodeIds: ['u'], nodes: VIER_WAENDE }));
  assert.equal(ermittleWegweiser(kontext, VIER_WAENDE), null);
});

test('ermittleWegweiser SPRICHT, wenn es etwas Benanntes zu sagen gibt', () => {
  // **Nachgebessert, und der Grund gehört hierher.** Meine erste Fassung schrieb
  // `if (w !== null) { … }` — sie hätte auch dann gehalten, wenn der Wegweiser IMMER schweigt.
  // Genau diese Mutation (`if (w) return null`) lief durch. *Eine Zusage unter Vorbehalt ist
  // keine Zusage.*
  //
  // Die Lage ist gemessen, nicht geraten: mit Bearbeiten-Recht und vorhandenen Bauteilen, aber
  // **ohne Auswahl**, bleibt „Dafür muss zuerst etwas ausgewählt sein" als Sperrgrund mit
  // benannter Handlung übrig — der einzige Grund im Bestand, der einen `ort` trägt.
  const kontext = werkzeugKontextAus(eingaben({ rechte: ['Hausplaner,update'], nodes: VIER_WAENDE, wandZahl: 4 }));
  const w = ermittleWegweiser(kontext, VIER_WAENDE);
  assert.notEqual(w, null, 'der Wegweiser schweigt, obwohl ein benannter Schritt offen ist');
  assert.ok(w!.satz.length > 0, 'ein Wegweiser ohne Satz ist keiner');
  assert.equal(w!.ort, 'schiene', 'der Ort kommt aus der Handlung, nicht aus einer Annahme');
  // Die Zahl im Satz ist die GEMESSENE Differenz, nicht eine Formulierung — sie belegt, dass
  // `resolveToolState` wirklich ein zweites Mal gefragt wurde.
  assert.match(w!.satz, /\d+ Werkzeuge frei/, 'der Satz nennt keine gemessene Wirkung');
});

test('ermittleWegweiser schweigt, wenn kein Grund eine benannte Handlung hat', () => {
  // Ohne jedes Recht bleiben nur „Keine Berechtigung"-Gründe — die tragen keine Handlung, und
  // der Wegweiser rät dann nicht, sondern schweigt. **Das ist die Gegenrichtung zur Zusage oben:**
  // beide zusammen zeigen, dass er unterscheidet, statt immer dasselbe zu tun.
  const kontext = werkzeugKontextAus(eingaben({ rechte: [], nodes: VIER_WAENDE, wandZahl: 4 }));
  assert.equal(ermittleWegweiser(kontext, VIER_WAENDE), null);
});

// --- fremderBereichVon ----------------------------------------------------------------------------

test('fremderBereichVon: im eigenen Bereich schweigt es', () => {
  assert.equal(fremderBereichVon('wand', 'architektur'), undefined);
});

test('fremderBereichVon: im fremden Bereich nennt es den LABEL des richtigen Bereichs', () => {
  // Nicht die id — der Nutzer liest „Architektur", nicht „architektur".
  assert.equal(fremderBereichVon('wand', 'heizung'), 'Architektur');
});

test('fremderBereichVon: ein unbekanntes Werkzeug ergibt undefined statt eines Wurfs', () => {
  assert.equal(fremderBereichVon('gibt-es-nicht', 'architektur'), undefined);
});

// --- palettenGruppenFuer --------------------------------------------------------------------------

test('palettenGruppenFuer reicht den Filter durch — er wirkt', () => {
  const kontext = werkzeugKontextAus(eingaben());
  const ohne = palettenGruppenFuer({ kontext, stapel: null, baum: [], schritt: null, filter: '' });
  const mit = palettenGruppenFuer({ kontext, stapel: null, baum: [], schritt: null, filter: 'zzzz-gibt-es-nicht' });
  const zaehle = (g: typeof ohne): number => g.reduce((n, gr) => n + gr.eintraege.length, 0);
  assert.ok(zaehle(ohne) > 0, 'ohne Filter ist die Palette leer — dann prüft das hier nichts');
  assert.equal(zaehle(mit), 0, 'ein Filter ohne Treffer liefert trotzdem Einträge');
});

test('palettenGruppenFuer reicht den Wegweiser durch — er erscheint als Eintrag', () => {
  const kontext = werkzeugKontextAus(eingaben());
  const ohne = palettenGruppenFuer({ kontext, stapel: null, baum: [], schritt: null, filter: '' });
  const mit = palettenGruppenFuer({
    kontext, stapel: null, baum: [], schritt: { satz: 'Zieh eine Wand', ort: 'schiene' }, filter: '',
  });
  const zaehle = (g: typeof ohne): number => g.reduce((n, gr) => n + gr.eintraege.length, 0);
  assert.ok(zaehle(mit) > zaehle(ohne), 'der Wegweiser kommt in der Palette nicht an');
});
