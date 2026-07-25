/**
 * AUF-53 — das Import-Recht.
 *
 * **Das eigentliche Sicherheitskriterium ist K4:** die Aktion `import` darf **nirgends** als
 * Berechtigungsaktion auftauchen. `User::hasPermission()` bildet auf genau vier feste Spalten ab
 * (`is_read`/`is_add`/`is_update`/`is_delete`) und schickt jede unbekannte Aktion in den
 * `default`-Zweig — also auf **`is_read`**. Eine so geschützte Route sähe geschützt aus und wäre
 * für jeden Leseberechtigten offen. Das ist schlimmer als kein Recht.
 *
 * Deshalb bildet die Vorbedingung auf **`Hausplaner,add`** ab: ein eigenes Recht, seit 2023
 * vorhanden, von keiner Route benutzt, in der Rechteverwaltung gepflegt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { VORBEDINGUNGEN, RECHT_IMPORTIEREN, RECHT_BEARBEITEN, offeneLuecken } from '../app/tools/vorbedingungen';
import { WERKZEUG_VERTRAEGE, vertrag } from '../app/tools/werkzeugVertrag';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { resolveToolState } from '../app/tools/activation';
import { baueAktivierungsKontext } from '../app/tools/toolContext';

const hier = dirname(fileURLToPath(import.meta.url));
const wurzel = join(hier, '../../../..');

const ALLE = [...TOOL_DEFINITIONS, ...TOOL_KATALOG];
/** Der Kontext des Import-Bereichs: alles da außer dem strittigen Recht. */
const kontext = (rechte: string[]) => baueAktivierungsKontext({
  workspace: 'import', view: '2d', selectionTypes: [], permissions: rechte,
  capabilities: ['project.open', 'viewport.ready', 'activeLevel.exists'],
});
const gesperrt = (rechte: string[]): string[] =>
  ALLE.filter((t) => !resolveToolState(t, kontext(rechte)).enabled).map((t) => t.id);

/** Die acht Werkzeuge, deren Vertrag `permission.import` nennt. */
const IMPORT_WERKZEUGE = WERKZEUG_VERTRAEGE
  .filter((v) => v.vorbedingungen.includes('permission.import'))
  .map((v) => v.werkzeugId);

// --- K4: das Sicherheitskriterium ---------------------------------------------------------------
test('K4: die Aktion `import` taucht NIRGENDS als Berechtigungsaktion auf', () => {
  // Gesucht wird die Zeichenkette, die eine Route schützen würde — sie fiele in `hasPermission`
  // auf `is_read` durch und wäre für jeden Leseberechtigten offen.
  const gesucht = ['permission:Hausplaner', ',import'].join('');
  const treffer: string[] = [];
  const sammle = (pfad: string): void => {
    for (const e of readdirSync(pfad)) {
      if (['node_modules', '.git', 'vendor', 'storage', 'public'].includes(e)) continue;
      const p = join(pfad, e);
      if (statSync(p).isDirectory()) sammle(p);
      else if (/\.(php|ts|tsx)$/.test(e) && readFileSync(p, 'utf8').includes(gesucht)) treffer.push(p);
    }
  };
  sammle(join(wurzel, 'app'));
  sammle(join(wurzel, 'routes'));
  sammle(join(wurzel, 'resources/planner'));
  assert.deepEqual(treffer, [], 'eine so geschützte Route täuschte Sicherheit vor');
});

test('K3: `hasPermission` kennt weiterhin genau vier Aktionen — nichts daran wurde angefasst', () => {
  const user = readFileSync(join(wurzel, 'app/Models/User.php'), 'utf8');
  for (const spalte of ['is_read', 'is_add', 'is_update', 'is_delete']) {
    assert.ok(user.includes(spalte), `${spalte} fehlt — die Abbildung wurde verändert`);
  }
  assert.doesNotMatch(user, /'import'/, 'keine fünfte Aktion eingeführt');
});

// --- K5: die acht sind zugeordnet ---------------------------------------------------------------
test('K5: genau ACHT Verträge tragen `permission.import` — und sie bilden auf `Hausplaner,add` ab', () => {
  assert.equal(IMPORT_WERKZEUGE.length, 8, 'acht, nicht sieben, nicht neun');
  assert.deepEqual([...IMPORT_WERKZEUGE].sort(), [
    'beschneiden', 'bild-importieren', 'datei-importieren', 'erkennung-bestaetigen',
    'grundriss-erkennen', 'kalibrieren', 'ki-assistent', 'nordrichtung-setzen',
  ]);
  assert.equal(RECHT_IMPORTIEREN, 'Hausplaner,add');
  assert.equal(VORBEDINGUNGEN['permission.import'].regel.value, 'Hausplaner,add');
  assert.notEqual(RECHT_IMPORTIEREN, RECHT_BEARBEITEN, 'ein EIGENES Recht, nicht `update`');
});

test('K5: die Vorbedingung ist jetzt erfüllbar — sie hängt an einem Recht, das es gibt', () => {
  assert.equal(VORBEDINGUNGEN['permission.import'].heuteErfuellbar, true);
  assert.ok(!offeneLuecken().some((l) => l.vorbedingung === 'permission.import'));
});

// --- K6 / K7: ohne Recht gesperrt, mit Recht genau diese acht ------------------------------------
test('K6: ohne das Recht bleiben alle acht gesperrt — mit unverändertem Grund', () => {
  const ohne = new Set(gesperrt([RECHT_BEARBEITEN]));
  for (const id of IMPORT_WERKZEUGE) {
    assert.ok(ohne.has(id), `${id} müsste ohne Recht gesperrt sein`);
    const t = ALLE.find((x) => x.id === id)!;
    const z = resolveToolState(t, kontext([RECHT_BEARBEITEN]));
    assert.equal(z.reason, 'Keine Berechtigung zum Importieren.', `${id}: der Grund hat sich geändert`);
  }
});

test('K7: mit dem Recht sinkt die Sperrzahl um GENAU die acht — gemessen, nicht angenommen', () => {
  const ohne = gesperrt([RECHT_BEARBEITEN]);
  const mit = gesperrt([RECHT_BEARBEITEN, RECHT_IMPORTIEREN]);
  assert.equal(ohne.length - mit.length, 8, `Differenz ${ohne.length} → ${mit.length}`);
  // und es sind wirklich diese acht, keine anderen
  const befreit = ohne.filter((id) => !mit.includes(id)).sort();
  assert.deepEqual(befreit, [...IMPORT_WERKZEUGE].sort());
});

test('K7: das Recht wirkt NUR auf diese acht — kein anderes Werkzeug ändert seinen Zustand', () => {
  const unbeteiligt = ALLE.filter((t) => !IMPORT_WERKZEUGE.includes(t.id));
  for (const t of unbeteiligt) {
    const a = resolveToolState(t, kontext([RECHT_BEARBEITEN]));
    const b = resolveToolState(t, kontext([RECHT_BEARBEITEN, RECHT_IMPORTIEREN]));
    assert.equal(a.enabled, b.enabled, `${t.id}: das Import-Recht darf hier nichts ändern`);
    assert.equal(a.reason, b.reason, `${t.id}: auch der Grund muss gleich bleiben`);
  }
});

test('das Recht schaltet keine Import-FUNKTION frei — die acht bleiben ohne Handler', () => {
  // Dieser Posten vergibt ein Recht; er baut kein Werkzeug. Der Vertrag nennt die Dienstmethode,
  // aufgerufen wird sie nirgends (verriegelt in werkzeugVertrag.test.ts).
  for (const id of IMPORT_WERKZEUGE) {
    assert.ok(vertrag(id)?.dienstMethode, `${id}: Vertrag ohne Dienstmethode`);
  }
});
