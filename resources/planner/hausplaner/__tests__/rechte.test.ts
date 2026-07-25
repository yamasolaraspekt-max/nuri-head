/**
 * AUF-60 — die Insel kennt die Rechte des Nutzers.
 *
 * **Der gemessene Mangel (Rückgabe aus AUF-53 §4):** `permissions: [RECHT_BEARBEITEN]` stand als
 * Wert im Quelltext. Die Insel kannte **genau ein** Recht, und es stammte **nicht aus dem
 * angemeldeten Nutzer** — sie gab es sich selbst. Damit war die Zuordnung `import ⇒ Hausplaner,add`
 * aus AUF-53 richtig und wirkungslos zugleich.
 *
 * **Das wichtigste Kriterium dieses Postens ist K5:** fehlt das Attribut, gilt das **Minimum**.
 * Ein fehlender Wert darf nie „darf alles" bedeuten — sonst öffnet ausgerechnet der Fehlerfall
 * (alte Blade, Testfläche, Tippfehler im Attributnamen) alle Werkzeuge. Deshalb steht dieser Fall
 * hier zuerst und wird mit dem Mutations-Gegenbeweis geführt.
 *
 * **Was hier NICHT geprüft wird, weil es hier nicht hingehört:** ob der Nutzer das Recht wirklich
 * hat. Das entscheidet `CheckUserPermission` auf dem Server, und dabei bleibt es. Die Insel
 * **zeigt** Rechte, sie **entscheidet** sie nicht.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { leseRechte, RECHTE_ATTRIBUT } from '../app/state/rechte';
import { usePlannerUiStore } from '../app/state/uiState';
import { baueAktivierungsKontext } from '../app/tools/toolContext';
import { resolveToolState } from '../app/tools/activation';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { RECHT_BEARBEITEN, RECHT_IMPORTIEREN } from '../app/tools/vorbedingungen';

const hier = dirname(fileURLToPath(import.meta.url));
const wurzel = join(hier, '../../../..');
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '').replace(/\{\{--[\s\S]*?--\}\}/g, '');
const lies = (p: string): string => ohneKommentare(readFileSync(join(hier, '../', p), 'utf8'));

const app = lies('app/HausplanerApp.tsx');
const einstieg = lies('main.tsx');
const blade = ohneKommentare(readFileSync(join(wurzel, 'resources/views/admin/hausplaner/objekt.blade.php'), 'utf8'));

// --- K5: fehlender Wert ⇒ Minimum, nicht Maximum ------------------------------------------------
test('K5: fehlt das Attribut, ist die Rechte-Liste LEER — nicht voll', () => {
  assert.deepEqual(leseRechte(undefined), [], 'undefined ist kein Freibrief');
  assert.deepEqual(leseRechte(null), []);
  assert.deepEqual(leseRechte(''), [], 'ein leeres Attribut ebenso');
  assert.deepEqual(leseRechte('   '), [], 'und eines, das nur Leerraum enthält');
});

test('K5: der Grundzustand des UI-State ist das Minimum', () => {
  usePlannerUiStore.getState().setRechte([]);
  assert.deepEqual(usePlannerUiStore.getState().rechte, []);
  // Und `reset()` (Bedien-Reset beim Mount) stellt keine Rechte her, die niemand gelesen hat.
  usePlannerUiStore.getState().setRechte([RECHT_BEARBEITEN]);
  usePlannerUiStore.getState().reset();
  assert.deepEqual(usePlannerUiStore.getState().rechte, [RECHT_BEARBEITEN],
    'reset() ist ein Bedien-Reset — es darf gelesene Rechte weder löschen noch erfinden');
  usePlannerUiStore.getState().setRechte([]);
});

// --- K6: durchgereicht, nicht abgeleitet --------------------------------------------------------
test('K6: genau das, was das Attribut liefert — kein Eintrag mehr, keiner weniger', () => {
  assert.deepEqual(leseRechte(`${RECHT_BEARBEITEN} ${RECHT_IMPORTIEREN}`), [RECHT_BEARBEITEN, RECHT_IMPORTIEREN]);
  // Kein „wer schreiben darf, darf auch lesen": ein einzelnes Recht bleibt ein einzelnes.
  assert.deepEqual(leseRechte(RECHT_BEARBEITEN), [RECHT_BEARBEITEN]);
});

test('getrennt wird am Leerraum, NICHT am Komma — ein Recht enthält selbst eines', () => {
  // Am Komma zu trennen zerlegte genau die Marken, die gelesen werden sollen: aus
  // `Hausplaner,update` würden zwei sinnlose Bruchstücke, und jede Regel liefe ins Leere.
  assert.deepEqual(leseRechte('Hausplaner,read Hausplaner,update'), ['Hausplaner,read', 'Hausplaner,update']);
  assert.equal(leseRechte(RECHT_BEARBEITEN)[0].includes(','), true);
});

test('mehrfacher Leerraum und Zeilenumbrüche erzeugen keine Leer-Einträge', () => {
  assert.deepEqual(leseRechte('  Hausplaner,read \n  Hausplaner,add  '), ['Hausplaner,read', 'Hausplaner,add']);
});

test('die Regel kennt keine einzige Rechte-Marke namentlich', () => {
  const regel = lies('app/state/rechte.ts');
  assert.doesNotMatch(regel, /Hausplaner/, 'sonst wäre sie eine Rechte-Quelle statt eines Lesers');
  assert.doesNotMatch(regel, /is_read|is_add|is_update|is_delete/);
});

// --- K4: keine Rechteprüfung, keine zweite Wahrheit ---------------------------------------------
test('K4: nirgends in der Insel wird ein Recht selbst ermittelt', () => {
  const treffer: string[] = [];
  const sammle = (pfad: string): void => {
    for (const e of readdirSync(pfad)) {
      // `__tests__` ist ausgenommen: Tests dürfen über `hasPermission` **reden** (dieser hier tut
      // es), sie laufen aber nicht in der Insel. Geprüft wird der Laufzeit-Code.
      if (['node_modules', 'dist', '__tests__'].includes(e)) continue;
      const p = join(pfad, e);
      if (statSync(p).isDirectory()) { sammle(p); continue; }
      if (!/\.(ts|tsx)$/.test(e)) continue;
      const q = ohneKommentare(readFileSync(p, 'utf8'));
      if (/hasPermission|isSuperAdmin|is_admin|user_rolls/.test(q)) treffer.push(p.slice(wurzel.length + 1));
    }
  };
  sammle(join(wurzel, 'resources/planner'));
  assert.deepEqual(treffer, [], 'eine eigene Rechte-Ableitung wäre eine zweite Wahrheit über Rechte');
});

test('K3: keine neue Aktion — `import` ist weiterhin keine Berechtigungsaktion', () => {
  const gesucht = ['permission:Hausplaner', ',import'].join('');
  assert.ok(!blade.includes(gesucht));
  assert.ok(!app.includes(gesucht));
  assert.equal(RECHT_IMPORTIEREN, 'Hausplaner,add', 'AUF-53 unverändert: eigenes Recht, keine neue Aktion');
});

// --- Die Verdrahtung: gelesen statt gesetzt -----------------------------------------------------
test('die App SETZT kein Recht mehr — sie liest es aus dem UI-State', () => {
  assert.doesNotMatch(app, /permissions: \[RECHT_BEARBEITEN\]/, 'der gesetzte Wert ist zurück');
  assert.doesNotMatch(app, /permissions: \['Hausplaner,[a-z]+'\]/, 'auch die zweite Stelle war hart gesetzt');
  assert.match(app, /permissions: rechte,/);
  assert.match(app, /permissions: usePlannerUiStore\.getState\(\)\.rechte,/, 'auch der Tastenkürzel-Pfad');
  assert.match(app, /const rechte = usePlannerUiStore\(\(s\) => s\.rechte\);/);
  // Ändern sich die Rechte, muss der Kontext neu gebaut werden — sonst bleibt die alte Sperre stehen.
  assert.match(app, /\[activeWorkspace, modus, selectedNodeIds, nodes, scene, level, waende\.length, rechte\]/);
});

test('main.tsx liest die Rechte über dieselbe Naht wie die Speichern-URL', () => {
  assert.match(einstieg, /mount\.dataset\.speichernUrl/, 'die bekannte Naht');
  assert.match(einstieg, /setRechte\(leseRechte\(mount\.dataset\[RECHTE_ATTRIBUT\]\)\)/);
  assert.equal(RECHTE_ATTRIBUT, 'rechte', 'data-rechte');
});

test('das Blade liefert die vier bekannten Rechte — aus hasPermission, ohne Ableitung', () => {
  assert.match(blade, /data-rechte="\{\{ \$hpRechte \}\}"/);
  assert.match(blade, /\$hpNutzer->hasPermission\('Hausplaner', \$aktion\)/);
  assert.match(blade, /\['read', 'add', 'update', 'delete'\]/, 'die vier, die das System kennt');
  // Ohne angemeldeten Nutzer bleibt die Liste leer — dieselbe Richtung wie K5.
  assert.match(blade, /\$hpNutzer && \$hpNutzer->hasPermission/);
});

test('K2/K3: der Weg berührt weder Modell-Store noch Routen noch das Rechtemodell', () => {
  for (const datei of ['store/hausplanerStore.ts', 'domain/scene.types.ts', 'domain/validation.ts']) {
    assert.doesNotMatch(lies(datei), /rechte|permission/i, `${datei}: Rechte gehören nicht ins Dokumentmodell`);
  }
  const nutzer = readFileSync(join(wurzel, 'app/Models/User.php'), 'utf8');
  assert.match(nutzer, /'read', 'view', 'show', 'index' => 'is_read'/, 'hasPermission unverändert');
});

// --- K7: die Wirkung, gemessen ------------------------------------------------------------------
const ALLE = [...TOOL_DEFINITIONS, ...TOOL_KATALOG];
const ALLES_DA = ['project.open', 'viewport.ready', 'activeLevel.exists', 'level.hasWalls'];
const kontext = (rechte: string[], workspace: string, auswahl: string[] = []) => baueAktivierungsKontext({
  workspace: workspace as never, view: '2d', selectionTypes: auswahl as never,
  permissions: rechte, capabilities: ALLES_DA,
});
const gesperrt = (rechte: string[], workspace: string, auswahl: string[] = []): number =>
  ALLE.filter((t) => !resolveToolState(t, kontext(rechte, workspace, auswahl)).enabled).length;

test('K7: das Import-Recht öffnet acht Werkzeuge — jetzt aus dem Nutzer, nicht aus dem Quelltext', () => {
  const ohne = gesperrt([RECHT_BEARBEITEN], 'import');
  const mit = gesperrt([RECHT_BEARBEITEN, RECHT_IMPORTIEREN], 'import');
  // AUF-53 maß 79 → 71, als das Recht fest gesetzt war. Dieselbe Differenz — anderer Ursprung.
  assert.equal(ohne, 79);
  assert.equal(mit, 71);
});

test('K7: 45 Werkzeuge hängen am Bearbeiten-Recht — vorher konnte das niemand verlieren', () => {
  // Arbeitsbereich Architektur, Wand ausgewählt: die Lage, in der am meisten bedienbar ist.
  const ohne = gesperrt([], 'architektur', ['wall']);
  const mit = gesperrt([RECHT_BEARBEITEN], 'architektur', ['wall']);
  assert.equal(ohne, 73);
  assert.equal(mit, 28);
  assert.equal(ohne - mit, 45, 'das ist die Anzeige-Lüge, die dieser Posten beendet');
});

test('K7 gemessen, nicht behauptet: im Import-Bereich ändert das Bearbeiten-Recht NICHTS', () => {
  // 79 = 79. Nicht weil das Recht wirkungslos wäre, sondern weil dort **ohne Auswahl** dieselben
  // Werkzeuge schon an anderen Vorbedingungen hängen. Die Zahl steht hier, damit niemand aus dem
  // Bericht schließt, das Recht wirke überall gleich — es wirkt dort, wo sonst nichts mehr sperrt.
  assert.equal(gesperrt([], 'import'), 79);
  assert.equal(gesperrt([RECHT_BEARBEITEN], 'import'), 79);
});
