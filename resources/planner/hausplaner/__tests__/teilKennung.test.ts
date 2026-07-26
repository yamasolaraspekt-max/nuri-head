/**
 * AUF-35b — **die Teil-Identität: eine Wandseite, eine Dachflaeche.**
 *
 * Der Befund: Auswahl ist knotenweise. Die zwei Seiten einer Wand sind **implizit** und existieren
 * nirgends als Daten; `surfaceId` lebt nur innerhalb von `geometry/dachAusschnitt.ts`.
 *
 * **Die Eigenschaft, die dieser Test schuetzt:** die Kennung ist **abgeleitet, nicht gespeichert**.
 * Kein Schema, kein Command, keine Persistenz — sie ueberlebt kein Neuladen, und das ist richtig so.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  baueTeilId, zerlegeTeilId, knotenVon, wandSeiten, seiteVonPunkt, dachFlaechen, teilKlartext,
} from '../app/tools/teilKennung';
import { mehrfachUebersicht } from '../app/tools/auswahlUebersicht';
import type { WallNode, SceneNode } from '../domain/scene.types';

const hier = dirname(fileURLToPath(import.meta.url));
const basis = {
  levelId: 'l1', visible: true, locked: false, tags: [] as string[],
  createdAt: '2026-07-26T10:00:00Z', updatedAt: '2026-07-26T10:00:00Z',
};
const wand = (id: string, end: { x: number; y: number }, start = { x: 0, y: 0 }): WallNode =>
  ({ ...basis, id, type: 'wall', start, end, thickness: 300, height: 2500 } as WallNode);

// --- Die Kennung ------------------------------------------------------------------------------------
test('bauen und zerlegen sind zueinander umkehrbar', () => {
  const id = baueTeilId('wall-7', 'seite', 'links');
  assert.equal(id, 'wall-7#seite:links');
  assert.deepEqual(zerlegeTeilId(id), { teilId: id, nodeId: 'wall-7', art: 'seite', wert: 'links' });
});

test('eine reine Knoten-id ist KEIN Fehler — sie ist der Normalfall', () => {
  assert.equal(zerlegeTeilId('wall-7'), null);
  assert.equal(knotenVon('wall-7'), 'wall-7');
  assert.equal(knotenVon('wall-7#seite:rechts'), 'wall-7');
});

test('Unfug wird abgewiesen, statt halb gelesen zu werden', () => {
  for (const kaputt of ['#seite:links', 'wall-7#', 'wall-7#seite', 'wall-7#seite:', 'wall-7#unbekannt:1']) {
    assert.equal(zerlegeTeilId(kaputt), null, `„${kaputt}" haette gelesen werden duerfen?`);
  }
});

// --- K5: zwei Seiten je Wand, deterministisch ---------------------------------------------------------
test('K5: jede Wand mit Laenge hat genau zwei Seiten', () => {
  for (const w of [wand('w1', { x: 5000, y: 0 }), wand('w2', { x: 0, y: 4000 }), wand('w3', { x: 3000, y: 3000 })]) {
    const seiten = wandSeiten(w);
    assert.deepEqual(seiten.map((s) => s.wert), ['links', 'rechts'], `${w.id}`);
    assert.deepEqual(seiten.map((s) => s.nodeId), [w.id, w.id]);
  }
});

test('K5: eine Wand OHNE Laenge hat keine Seiten — „links" waere eine Behauptung ohne Richtung', () => {
  assert.deepEqual(wandSeiten(wand('w0', { x: 0, y: 0 })), []);
});

test('K5: die Zuordnung ist geometrisch bestimmt — drei Richtungen, davon eine senkrechte', () => {
  // waagerecht nach Osten: Norden (+y) liegt links
  assert.equal(seiteVonPunkt(wand('w1', { x: 5000, y: 0 }), { x: 2500, y: 100 }), 'links');
  assert.equal(seiteVonPunkt(wand('w1', { x: 5000, y: 0 }), { x: 2500, y: -100 }), 'rechts');
  // SENKRECHT nach Norden: Westen (−x) liegt links
  assert.equal(seiteVonPunkt(wand('w2', { x: 0, y: 4000 }), { x: -100, y: 2000 }), 'links');
  assert.equal(seiteVonPunkt(wand('w2', { x: 0, y: 4000 }), { x: 100, y: 2000 }), 'rechts');
  // schraeg
  assert.equal(seiteVonPunkt(wand('w3', { x: 3000, y: 3000 }), { x: 1000, y: 2000 }), 'links');
  assert.equal(seiteVonPunkt(wand('w3', { x: 3000, y: 3000 }), { x: 2000, y: 1000 }), 'rechts');
});

test('K5: dieselbe Wand liefert nach „Neuladen" dieselbe Zuordnung', () => {
  // Neuladen heisst hier: dieselben Daten, neu aufgebaut. Nichts haengt an Reihenfolge oder Zeit.
  const a = wand('w1', { x: 5000, y: 0 });
  const b = JSON.parse(JSON.stringify(a)) as WallNode;
  assert.deepEqual(wandSeiten(a), wandSeiten(b));
  assert.equal(seiteVonPunkt(a, { x: 1, y: 1 }), seiteVonPunkt(b, { x: 1, y: 1 }));
});

test('genau auf der Achse gibt es keine Seite — null statt einer geratenen', () => {
  assert.equal(seiteVonPunkt(wand('w1', { x: 5000, y: 0 }), { x: 2500, y: 0 }), null);
});

// --- K4: rein, zweimal dasselbe ------------------------------------------------------------------------
test('K4: zweimal dieselbe Eingabe ⇒ tiefengleiches Ergebnis', () => {
  const w = wand('w1', { x: 5000, y: 0 });
  assert.deepEqual(wandSeiten(w), wandSeiten(w));
});

// --- Dachflaechen: gelesen, nicht erfunden --------------------------------------------------------------
test('ein Dach ohne Flaechenangabe hat hier keine Teile — es wird keine erfunden', () => {
  assert.deepEqual(dachFlaechen({ id: 'r1' } as never), []);
});

test('mit Flaechenangabe entsteht je Flaeche ein Teil, in Reihenfolge', () => {
  const teile = dachFlaechen({ id: 'r1', flaechen: [{}, {}, {}] } as never);
  assert.deepEqual(teile.map((t) => t.teilId), ['r1#flaeche:0', 'r1#flaeche:1', 'r1#flaeche:2']);
});

// --- K7: Klartext, kein Schluessel -----------------------------------------------------------------------
test('K7: die Uebersicht zeigt Klartext, nie die rohe Kennung', () => {
  const t = zerlegeTeilId('wall-7#seite:links')!;
  const text = teilKlartext(t, 'Wand 3');
  assert.equal(text, 'Wand 3 · Seite links');
  assert.ok(!text.includes('#'), 'ein Schluessel in der Oberflaeche macht den Nutzer zum Datenbankleser');
  assert.equal(teilKlartext(zerlegeTeilId('r1#flaeche:0')!, 'Dach 1'), 'Dach 1 · Fläche 1');
});

// --- K6: der Knoten bleibt waehlbar -----------------------------------------------------------------------
test('K6: die Uebersicht zaehlt einen Teil als seinen Knoten — nicht als Nichts', () => {
  const nodes = [wand('w1', { x: 5000, y: 0 })] as unknown as SceneNode[];
  const ohneTeil = mehrfachUebersicht(['w1'], nodes);
  const mitTeil = mehrfachUebersicht(['w1#seite:links'], nodes);
  assert.equal(ohneTeil.gesamt, 1);
  assert.equal(mitTeil.gesamt, 1, 'sonst saehe das Panel leer aus, obwohl etwas gewaehlt ist');
  assert.deepEqual(mitTeil.typen, ohneTeil.typen);
});

// --- K3: kein Schema, kein Command, keine Persistenz -------------------------------------------------------
test('K3: die Teil-Kennung kommt in Schema, Commands und Store NICHT vor', () => {
  const wurzel = join(hier, '..');
  const treffer: string[] = [];
  const gehe = (verzeichnis: string): void => {
    for (const name of readdirSync(verzeichnis)) {
      if (name === 'node_modules' || name === '__tests__' || name === '__domtests__') continue;
      const pfad = join(verzeichnis, name);
      if (statSync(pfad).isDirectory()) { gehe(pfad); continue; }
      if (!/\.tsx?$/.test(pfad)) continue;
      if (!/\/(domain|store|geometry|renderers)\//.test(pfad)) continue;
      const inhalt = readFileSync(pfad, 'utf8');
      if (inhalt.includes('teilKennung') || inhalt.includes('#seite:') || inhalt.includes('#flaeche:')) {
        treffer.push(pfad.slice(wurzel.length + 1));
      }
    }
  };
  gehe(wurzel);
  assert.deepEqual(treffer, [], `Teil-Kennung ausserhalb der App-Schicht: ${treffer.join(', ')}`);
});

test('K3: und sie lebt in der App-Schicht — dort, wo Anzeige-Zustand hingehoert', () => {
  const datei = readFileSync(join(hier, '../app/tools/teilKennung.ts'), 'utf8');
  // **Ohne Kommentare gemessen.** Beim ersten Lauf schlug diese Zusage an — auf meinen eigenen
  // Erklaersatz „eine neue persistierte Struktur", der das verbotene Wort enthaelt. *Ein Verbot,
  // das die Begruendung fuer das Verbot trifft, prueft den Text und nicht den Code.*
  const roh = datei.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
  assert.doesNotMatch(roh, /executeCommand|getState|persist|localStorage/,
    'kein Command, kein Store, keine Persistenz');
  assert.match(datei, /Anzeige-Zustand, kein Modell/, 'und der Grund steht im Kopf');
});
