/**
 * AUF-76 — **die Wand bekommt ihre Schichten** (Mengenermittlung M0).
 *
 * Gemessener Anlass: `WallNode` trug **eine einzige** `thickness`, während `CeilingNode.schichten`
 * längst existierte. Damit war „fertig" für Wände nicht berechenbar — niemand wusste, welcher Teil
 * der 300 mm Konstruktion ist und welcher Putz.
 *
 * **Dieser Posten legt ein Feld an und rechnet nichts.** Wer damit rechnet, ist AUF-77.
 *
 * **Die Eigenschaft, die dieser Test schützt, ist die Rückwärtsverträglichkeit:** jedes bereits
 * gespeicherte Dokument bleibt gültig. Ein Pflichtfeld hätte auf einen Schlag jeden Bestand
 * ungültig gemacht — deshalb steht der Mutations-Gegenbeweis (K8) genau darauf.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { sceneDocumentSchema, wallNodeSchema } from '../domain/validation';
import { ladeFixture } from '../fixtures/studioFixtures';

const hier = dirname(fileURLToPath(import.meta.url));
const schemaDatei = join(hier, '../domain/scene-document-v2.schema.json');
const jsonSchema = JSON.parse(readFileSync(schemaDatei, 'utf8')) as Record<string, unknown>;

const wand = (zusatz: Record<string, unknown> = {}): Record<string, unknown> => ({
  id: 'w1', levelId: 'l1', visible: true, locked: false, tags: [],
  createdAt: '2026-07-26T10:00:00Z', updatedAt: '2026-07-26T10:00:00Z',
  type: 'wall', start: { x: 0, y: 0 }, end: { x: 5000, y: 0 },
  thickness: 300, height: 2500, ...zusatz,
});

// --- K4: der Bestand bleibt gültig ---------------------------------------------------------------
test('K4: eine Wand OHNE das neue Feld bleibt gültig — kein 422, keine Migration', () => {
  const ergebnis = wallNodeSchema.safeParse(wand());
  assert.equal(ergebnis.success, true, 'jedes bereits gespeicherte Dokument muss weiterhin laden');
});

test('K4: das Feld ist im abgelegten Schema NICHT verlangt', () => {
  // Der PHP-Validator liest diese Datei — steht das Feld dort unter `required`, wird jedes
  // Bestandsdokument beim naechsten Speichern abgewiesen.
  const wandTeil = wandSchemaAusDatei();
  assert.ok(Array.isArray(wandTeil.required), 'die Wand hat eine Pflichtliste');
  assert.ok(!(wandTeil.required as string[]).includes('schichten'), 'und `schichten` steht nicht darin');
  assert.ok('schichten' in (wandTeil.properties as Record<string, unknown>), 'bekannt ist es trotzdem');
});

// --- K5: Rundlauf ---------------------------------------------------------------------------------
test('K5: zwei Schichten überstehen Speichern und Laden unverändert', () => {
  const schichten = [{ materialId: 'kalksandstein', dickeMm: 240 }, { dickeMm: 15 }];
  const ergebnis = wallNodeSchema.safeParse(wand({ schichten }));
  assert.equal(ergebnis.success, true);
  // Der Rundlauf durch die Ablage — nicht nur durch die Prüfung.
  const zurueck = wallNodeSchema.safeParse(JSON.parse(JSON.stringify(wand({ schichten }))));
  assert.equal(zurueck.success, true);
  assert.deepEqual((zurueck as { data: { schichten: unknown } }).data.schichten, schichten,
    'kein Wert wird unterwegs gerundet, ergänzt oder umbenannt');
});

test('K5: `thickness` bleibt daneben stehen und unverändert — beide Bezugsmaße', () => {
  const ergebnis = wallNodeSchema.safeParse(wand({ schichten: [{ dickeMm: 240 }, { dickeMm: 15 }] }));
  assert.equal((ergebnis as { data: { thickness: number } }).data.thickness, 300,
    'die Rohbau-Wahrheit wird von der Schichtung nicht angetastet');
});

test('K5: eine Wand mit 300 mm und 320 mm Schichten wird NICHT abgelehnt', () => {
  // **Bewusst nicht erzwungen.** Ob das ein Fehler oder eine zulässige Überdeckung ist, ist eine
  // Fachfrage — sie ist an Yama zurückgegeben. Bis zur Antwort wird nichts entschieden; eine still
  // eingebaute Regel wäre eine Fachentscheidung, die sich niemand ausgesucht hat.
  const ergebnis = wallNodeSchema.safeParse(wand({ schichten: [{ dickeMm: 200 }, { dickeMm: 120 }] }));
  assert.equal(ergebnis.success, true);
});

// --- K6: ganze mm, größer als null ----------------------------------------------------------------
for (const [wert, warum] of [[0, 'eine Schicht ohne Dicke ist keine Schicht'],
  [-5, 'negative Dicke gibt es am Bau nicht'],
  [12.5, 'das übrige Schema führt ganze mm — halbe wären eine zweite Einheit']] as const) {
  test(`K6: dickeMm = ${wert} wird abgelehnt (${warum})`, () => {
    const ergebnis = wallNodeSchema.safeParse(wand({ schichten: [{ dickeMm: wert }] }));
    assert.equal(ergebnis.success, false);
  });
}

test('K6: unbekannte Felder in einer Schicht werden abgelehnt', () => {
  // `.strict()` wie bei der Decke: ein Tippfehler („dicke" statt „dickeMm") soll auffallen und
  // nicht stillschweigend als Zusatzfeld mitlaufen.
  const ergebnis = wallNodeSchema.safeParse(wand({ schichten: [{ dickeMm: 240, dicke: 240 }] }));
  assert.equal(ergebnis.success, false);
});

// --- K7: feldgleich mit der Decke -----------------------------------------------------------------
function wandSchemaAusDatei(): Record<string, unknown> {
  const knoten = (jsonSchema.properties as Record<string, { items: { anyOf: Array<Record<string, never>> } }>)
    .nodes.items.anyOf as unknown as Array<{ properties: Record<string, { const?: string }> }>;
  const treffer = knoten.find((k) => k.properties?.type?.const === 'wall');
  assert.ok(treffer, 'die Wand steht in der Knoten-Union');
  return treffer as unknown as Record<string, unknown>;
}

function deckeSchemaAusDatei(): Record<string, unknown> {
  return (jsonSchema.properties as Record<string, { items: Record<string, unknown> }>).ceilings.items;
}

test('K7: Wand und Decke tragen dasselbe Teilschema — Zeichen für Zeichen', () => {
  // **Nicht „ähnlich", sondern gleich.** Zwei Schreibweisen für dieselbe Sache wären der Anfang der
  // zweiten Wahrheit — und der Vergleich läuft gegen die ERZEUGTE Datei, nicht gegen den Quelltext:
  // gleich aussehender Zod-Code kann verschiedenes Schema erzeugen.
  const w = (wandSchemaAusDatei().properties as Record<string, unknown>).schichten;
  const d = (deckeSchemaAusDatei().properties as Record<string, unknown>).schichten;
  assert.ok(w, 'die Wand hat das Feld');
  assert.ok(d, 'die Decke hat es weiterhin');
  assert.equal(JSON.stringify(w, Object.keys(w as object).sort()), JSON.stringify(d, Object.keys(d as object).sort()));
  assert.deepEqual(w, d);
});

test('K7: die Feldnamen selbst sind identisch', () => {
  const felder = (s: Record<string, unknown>): string[] =>
    Object.keys(((s.items as Record<string, unknown>).properties) as object).sort();
  const w = (wandSchemaAusDatei().properties as Record<string, Record<string, unknown>>).schichten;
  const d = (deckeSchemaAusDatei().properties as Record<string, Record<string, unknown>>).schichten;
  assert.deepEqual(felder(w), ['dickeMm', 'materialId']);
  assert.deepEqual(felder(w), felder(d));
});

// --- K3: kein persistierter Wert umbenannt --------------------------------------------------------
test('K3: kein bestehender Wert ist umbenannt worden', () => {
  const roh = readFileSync(join(hier, '../domain/scene.types.ts'), 'utf8');
  for (const name of ['thickness', 'height', 'type', 'objectType', 'zoneType', 'routeType']) {
    assert.ok(roh.includes(`${name}:`), `${name} steht unverändert im Modell`);
  }
  // Und im abgelegten Schema ebenso — dort haengt der Bestand daran.
  const wandTeil = wandSchemaAusDatei().properties as Record<string, unknown>;
  for (const name of ['thickness', 'height', 'type', 'start', 'end']) {
    assert.ok(name in wandTeil, `${name} fehlt im erzeugten Wand-Schema`);
  }
});

// --- Das ganze Dokument, an einer ECHTEN Szene ---------------------------------------------------
/**
 * **Nicht an einer selbst gebauten Szene.** Mein erster Versuch hier war ein von Hand
 * zusammengeschriebenes Dokument — und es fiel durch, weil ihm `units`, `settings` und
 * `sortOrder` fehlten. Genau das ist der Wert des Falls: *eine erfundene Szene beweist nur, dass
 * ich das Schema erraten habe.* Die Fixture ist ein Dokument, das die Anwendung wirklich lädt.
 */
const echteSzene = (): Record<string, unknown> =>
  JSON.parse(JSON.stringify(ladeFixture('u-dach'))) as Record<string, unknown>;

test('K4 am echten Dokument: die Fixture ohne Schichten bleibt gültig', () => {
  const dokument = echteSzene();
  const waende = (dokument.nodes as Array<{ type: string }>).filter((n) => n.type === 'wall');
  assert.ok(waende.length > 0, 'die Fixture trägt Wände');
  assert.ok(waende.every((w) => !('schichten' in w)), 'und keine davon hat heute Schichten');
  assert.equal(sceneDocumentSchema.safeParse(dokument).success, true);
});

test('K5 am echten Dokument: eine geschichtete Wand in einer echten Szene wird angenommen', () => {
  const dokument = echteSzene();
  const schichten = [{ materialId: 'kalksandstein', dickeMm: 240 }, { dickeMm: 15 }];
  const erste = (dokument.nodes as Array<Record<string, unknown>>).find((n) => n.type === 'wall')!;
  erste.schichten = schichten;

  const ergebnis = sceneDocumentSchema.safeParse(dokument);
  assert.equal(ergebnis.success, true,
    ergebnis.success ? '' : JSON.stringify(ergebnis.error.issues.slice(0, 4)));

  const geladen = (ergebnis as { data: { nodes: Array<Record<string, unknown>> } }).data.nodes
    .find((n) => n.type === 'wall')!;
  assert.deepEqual(geladen.schichten, schichten, 'Rundlauf ohne Verlust');
  assert.equal(geladen.thickness, erste.thickness, '`thickness` bleibt, was sie war');
});
