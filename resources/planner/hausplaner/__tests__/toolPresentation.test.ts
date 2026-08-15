/**
 * Wizard-Welle A1 — Werkzeug-Präsentationsschicht.
 *
 * Prüft, dass die Kuratierung als DATEN vollständig, verwaisungsfrei und verhaltensneutral ist:
 * jedes Werkzeug aus Registry ODER Katalog hat genau eine Zone; die Fix-Zone ist die Registry;
 * die Navi liefert nach der Umstellung exakt dieselben ids in derselben Reihenfolge wie vorher.
 * Jede Invariante hat eine Rot-Gegenprobe (lokale Regel-Kopie, echte Daten bleiben unberührt).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  TOOL_PRESENTATION_RULES,
  praesentation,
  zoneTools,
  zoneToolsIn,
  verwaisteRegeln,
  verwaisteRegelnIn,
  regelloseWerkzeuge,
  type ToolPresentationRule,
} from '../app/tools/toolPresentation';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { AUS_PAKET_GEHOBEN } from '../app/tools/toolRegistry';
import { PAKET_ALS_TOOLS } from '../app/tools/paketAdapter';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { faehigkeitenNach, doppelteIds } from '../app/tools/faehigkeiten';
import { WERKZEUGE_GESAMT } from '../app/tools/toolRegistry';
import { EIGENE_WERKZEUGE } from '../app/tools/toolRegistry';

/** Z-05-N1: die Fix-Zone waechst mit jedem eigenen Werkzeug — 7 aus dem Paket plus unsere. */
const FIX_ZONE = 7 + EIGENE_WERKZEUGE.length;

// --- 1) Vollständigkeit ---------------------------------------------------------------------
test('jede Registry- und Katalog-id hat genau eine Regel — gerechnet, nicht getippt', () => {
  // I2: der Katalog ist seit dem Tausch das 110er-Fachpaket (vorher 54 InDesign-Einträge).
  // **W-05/K-03: beide festen Zahlen sind weg.** Die 9 war der Grundbestand, die 101 der
  // Katalog — beide wanderten bei JEDEM gehobenen Werkzeug. *Was hier zaehlt, ist die Bilanz:
  // was aus dem Katalog faellt, steht in der Registry, und die Summe bleibt.*
  assert.equal(TOOL_DEFINITIONS.length, 9 + EIGENE_WERKZEUGE.length + AUS_PAKET_GEHOBEN.length);
  assert.equal(TOOL_KATALOG.length, PAKET_ALS_TOOLS.length - AUS_PAKET_GEHOBEN.length);
  assert.equal(TOOL_DEFINITIONS.length + TOOL_KATALOG.length, WERKZEUGE_GESAMT,
    'die Bilanz ist gekippt: ein Werkzeug hat sich verdoppelt oder ist verschwunden');
  assert.equal(TOOL_PRESENTATION_RULES.length, WERKZEUGE_GESAMT);

  const ids = TOOL_PRESENTATION_RULES.map((r) => r.toolId);
  assert.equal(new Set(ids).size, ids.length, 'keine doppelte toolId');

  for (const t of [...TOOL_DEFINITIONS, ...TOOL_KATALOG]) {
    assert.ok(praesentation(t.id), `${t.id} braucht genau eine Präsentationsregel`);
  }
  assert.deepEqual(regelloseWerkzeuge(), [], 'kein Werkzeug ohne Zone');
});

test('Zonen nach I4: fix waechst mit den eigenen Werkzeugen · 2 kontext · 101 weitere · 0 versteckt', () => {
  assert.equal(zoneTools('fix').length, FIX_ZONE);
  assert.equal(zoneTools('kontext').length, 2);
  // I4: alle Fach-Werkzeuge sind über ihre Kategorie-Gruppe erreichbar, also nicht mehr
  // `versteckt`. Die Zone sagt „über den Überlauf erreichbar" — sie flutet die linke Leiste NICHT;
  // dort stehen weiter nur `fix` und persönlich Angeheftetes.
  // W-05/K-03: gegen das Paket gerechnet — die Zone zeigt alle Paket-Werkzeuge.
  assert.equal(zoneTools('weitere').length, PAKET_ALS_TOOLS.length);
  assert.equal(zoneTools('versteckt').length, 0, 'kein Werkzeug bleibt unerreichbar');
});

// --- 2) Keine verwaisten Regeln (+ Rot-Gegenprobe) -------------------------------------------
test('verwaisteRegeln() ist leer', () => {
  assert.deepEqual(verwaisteRegeln(), []);
});

test('GEGENPROBE: eine erfundene id in einer lokalen Regel-Kopie wird als verwaist gemeldet', () => {
  const kopie: ToolPresentationRule[] = [
    ...TOOL_PRESENTATION_RULES,
    { toolId: 'gibt-es-nicht', zone: 'weitere', ordnung: 99, herkunft: 'katalog', begruendung: 'Testfall' },
  ];
  assert.deepEqual(verwaisteRegelnIn(kopie), ['gibt-es-nicht']);
  // und sie taucht NICHT in der Zone auf (auslassen statt werfen)
  assert.equal(zoneToolsIn(kopie, 'weitere').length, PAKET_ALS_TOOLS.length);
});

// --- 3) Invariante Fix-Zone (+ Rot-Gegenprobe) -----------------------------------------------
test('Fix-Zone = genau die art:werkzeug-Registry-ids in Registry-Reihenfolge', () => {
  const erwartet = TOOL_DEFINITIONS.filter((t) => t.art === 'werkzeug').map((t) => t.id);
  assert.deepEqual(erwartet, [
    'auswahl', 'wand', 'fenster', 'tuer', 'dach', 'decke', 'treppe',
    'bemassen', 'flaeche-messen',   // <- W-05, gehoben
    'kontur',
  ]);

  // **W-05: „in der Registry" heisst nicht mehr automatisch „in der Fix-Zone".**
  // Die zwei gehobenen Werkzeuge behalten ihre Praesentationsregel (`zone: 'weitere'`,
  // `herkunft: 'katalog'`) — gemessen, nicht angenommen. Das Blatt schliesst `toolPresentation`
  // ausdruecklich aus („traegt die acht bereits"), also ist das der gewollte Zustand: sie sind
  // ERREICHBAR, nicht fest angeheftet. *Die alte Gleichsetzung Registry == Fix-Zone galt nur,
  // solange die Registry ausschliesslich Fix-Werkzeuge kannte.*
  const inFix = new Set(zoneTools('fix').map((t) => t.id));
  assert.deepEqual([...inFix], erwartet.filter((id) => !(AUS_PAKET_GEHOBEN as readonly string[]).includes(id)));
  for (const id of AUS_PAKET_GEHOBEN) {
    assert.equal(inFix.has(id), false, `${id} sitzt in der Fix-Zone, obwohl seine Regel 'weitere' sagt`);
  }
});

test('keine Registry-id liegt in der versteckten Zone', () => {
  const versteckt = new Set(zoneTools('versteckt').map((t) => t.id));
  for (const t of TOOL_DEFINITIONS) {
    assert.ok(!versteckt.has(t.id), `${t.id} ist ein echtes Werkzeug und darf nicht versteckt sein`);
  }
});

test('GEGENPROBE: wand auf versteckt gesetzt ⇒ Fix-Invariante bricht', () => {
  const kopie: ToolPresentationRule[] = TOOL_PRESENTATION_RULES.map((r) =>
    r.toolId === 'wand' ? { ...r, zone: 'versteckt' as const } : r,
  );
  const fix = zoneToolsIn(kopie, 'fix').map((t) => t.id);
  assert.equal(fix.length, 6 + EIGENE_WERKZEUGE.length, 'die Fix-Zone hätte ein Werkzeug verloren');
  assert.ok(!fix.includes('wand'));
  assert.ok(zoneToolsIn(kopie, 'versteckt').some((t) => t.id === 'wand'));
  // echte Daten unberührt
  assert.deepEqual(zoneTools('fix').map((t) => t.id).includes('wand'), true);
});

// --- 4) Kuratierungs-Beweis -------------------------------------------------------------------
test('die DTP-Reste sind aus den Regeln verschwunden — nicht nur versteckt', () => {
  const alle = TOOL_PRESENTATION_RULES.map((r) => r.toolId);
  for (const id of [
    'type', 'page', 'preflight', 'swatches-panel', 'pages-panel',
    'rectangle-frame', 'pen', 'note', 'object-style', 'eyedropper',
  ]) {
    assert.ok(!alle.includes(id), `${id} ist ein DTP-Rest und darf in keiner Zone mehr auftauchen`);
  }
  const erreichbar = zoneTools('weitere').map((t) => t.id);
  // **W-05/K-03: die feste 101 ist auch hier weg.** Die Zone 'weitere' zeigt weiterhin ALLE
  // Paket-Werkzeuge — auch die gehobenen, die ihre Regel behalten. *Gezaehlt wird deshalb gegen
  // das Paket, nicht gegen eine Zahl, die beim naechsten Heben falsch wird.*
  assert.equal(erreichbar.length, PAKET_ALS_TOOLS.length);
  // kein Datenverlust: jedes erreichbare Werkzeug steht auch im Katalog ODER ist gehoben
  for (const id of erreichbar) {
    const imKatalog = TOOL_KATALOG.some((t) => t.id === id);
    const gehoben = (AUS_PAKET_GEHOBEN as readonly string[]).includes(id);
    assert.ok(imKatalog || gehoben,
      `${id} ist weder im Katalog noch gehoben — hier geht wirklich etwas verloren`);
  }
});

test('AUF-31: gleichbedeutende Werkzeuge sind zusammengeführt, nicht doppelt geführt', () => {
  // Vorher: `auswahl` (Registry) und `select` (Paket) waren zwei Einträge für dasselbe Werkzeug.
  // Nach Weg 1 gibt es je EINE Regel — in der Registry, mit den Metadaten des Pakets.
  assert.equal(praesentation('auswahl')?.herkunft, 'registry');
  assert.equal(praesentation('select'), undefined, 'die englische Dublette ist verschwunden');
  assert.equal(praesentation('auswahl')?.zone, 'fix');
  // ein echtes Paket-Werkzeug bleibt Katalog-Herkunft
  assert.equal(praesentation('raum')?.herkunft, 'katalog');
});

// --- 5) Regressionsanker: die Navi verhält sich unverändert ------------------------------------
test('Regressionsanker: faehigkeitenNach(werkzeuge) bleibt nach der Fachzuordnung in derselben Reihenfolge', () => {
  // `decke` wurde nach Yamas Fachentscheidung bewusst nach `bau` verschoben; der übrige Stand bleibt
  // hart hinterlegt aus Registry-Reihenfolge + bisheriger CAD_TEILMENGE.
  // I2/AUF-28: die 15 `cad-*`-Einträge sind WEG — sie kamen aus der Zone `weitere`, und die ist
  // seit dem Katalog-Tausch leer. Übrig bleiben die echten Registry-Werkzeuge der Gruppe.
  // Z-05-N1: `kontur` reiht sich nach `auswahl` ein — die Reihenfolge folgt der Registry, und
  // dort steht sie hinter `treppe` und vor den beiden Aktionen. **Der Anker bleibt ein Anker:**
  // er haelt die REIHENFOLGE fest, nicht die Laenge, und genau die hat sich nicht verschoben.
  // **W-05: die zwei gehobenen Werkzeuge reihen sich nach `auswahl` ein** — Registry-Reihenfolge,
  // gemessen und nicht geraten. *Der Anker bleibt ein Anker:* er haelt fest, dass sich die
  // BESTEHENDEN vier nicht gegeneinander verschieben; dass zwei dazukommen, ist der Zweck der
  // Scheibe und kein Bruch.
  // A-35: `trimmen` reiht sich als drittes gehobenes ein — Registry-Reihenfolge, ans Ende gestellt.
  const vorher = ['auswahl', 'bemassen', 'flaeche-messen', 'kontur', 'loeschen', 'duplizieren', 'trimmen'];
  const ist = faehigkeitenNach('werkzeuge').map((f) => f.id);
  assert.deepEqual(ist, vorher);

  // Die eigentliche Ankerzusage, unabhaengig von der Laenge: die vier Alten stehen weiterhin in
  // ihrer alten Ordnung zueinander.
  assert.deepEqual(
    ist.filter((id) => !(AUS_PAKET_GEHOBEN as readonly string[]).includes(id)),
    ['auswahl', 'kontur', 'loeschen', 'duplizieren'],
  );
});

// --- 6) Konsolidierungs-Schutz ----------------------------------------------------------------
test('doppelteIds() der Fähigkeiten bleibt leer', () => {
  assert.deepEqual(doppelteIds(), []);
});
