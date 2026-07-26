/**
 * Batch 0 — Fähigkeiten-Registry: eine Wahrheit, jede Landkarte-Fähigkeit sichtbar mit Zustand,
 * CAD-Teilmenge remappt / DTP raus. (Die Navi-Optik prüft der Evaluator im Browser.)
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { FAEHIGKEITEN, FAEHIGKEIT_GRUPPEN, faehigkeitenNach, doppelteIds } from '../app/tools/faehigkeiten';

test('eine Wahrheit: keine doppelten Fähigkeit-ids', () => {
  assert.deepEqual(doppelteIds(), []);
});

test('alle 13 Rechen-Engines sind registriert (echtes Modul + Ein-/Ausgang) — verfügbar ist nur das Angeschlossene', () => {
  // AUF-33/L2 schloss `engine-treppe` an; **AUF-52 Scheibe A** haengt `engine-sparren` an.
  // Alle uebrigen bleiben `in_entwicklung`, bis eine Scheibe sie anschliesst — Kacheln, die
  // „verfügbar" behaupten und es nicht sind, waeren die falschen Versprechen aus AUF-28.
  //
  // **Nachgezogen in AUF-52:** die Liste stand fest auf `['engine-treppe']`. Die Absicht war nie
  // die Zahl, sondern *nur das Angeschlossene ist verfuegbar* — und die haelt weiter.
  const engines = FAEHIGKEITEN.filter((f) => f.art === 'engine');
  assert.equal(engines.length, 13);
  const verfuegbar = engines.filter((e) => e.zustand === 'verfuegbar');
  // Nach der Reihenfolge des Registers, nicht nach der des Anschlusses — sortiert verglichen,
  // damit die Zusage nicht an der Zeilennummer im Register haengt.
  assert.deepEqual([...verfuegbar.map((e) => e.id)].sort(), ['engine-sparren', 'engine-treppe'],
    'genau die angeschlossenen Engines');
  const angeschlossen = new Set(['engine-treppe', 'engine-sparren']);
  for (const e of engines) {
    if (!angeschlossen.has(e.id)) {
      assert.equal(e.zustand, 'in_entwicklung', `${e.id} sollte schlafen (Fläche folgt in einer spaeteren Scheibe)`);
    }
    assert.ok(e.engineModul?.startsWith('geometry/'), `${e.id} referenziert ein echtes geometry-Modul`);
    assert.ok(e.eingang && e.ausgang, `${e.id} trägt Ein-/Ausgang fürs spätere Panel`);
  }
});

test('Guard (AP-E): jede Engine-Fähigkeit importiert REAL + der deklarierte Export existiert (Export ≠ Modulname)', async () => {
  // Verriegelt die „echte Engines"-Zusage per Beweis: dynamischer Import + Prüfung des deklarierten
  // Export-Namens. Rot, sobald Modul ODER Export fehlt/verfälscht ist (Gegenbeweis).
  const engines = FAEHIGKEITEN.filter((f) => f.art === 'engine');
  for (const e of engines) {
    assert.ok(e.engineModul && e.engineExport, `${e.id}: Modul UND Export müssen deklariert sein`);
    const modul = (await import('../' + e.engineModul)) as Record<string, unknown>;
    assert.equal(
      typeof modul[e.engineExport as string], 'function',
      `${e.id}: Export „${e.engineExport}" fehlt in ${e.engineModul} (Byte-Treue-Verriegelung)`,
    );
  }
});

test('die echten Werkzeuge sind verfuegbar und modus-schaltbar', () => {
  for (const id of ['auswahl', 'wand', 'fenster', 'tuer', 'dach', 'treppe']) {
    const f = FAEHIGKEITEN.find((x) => x.id === id);
    assert.ok(f, `Werkzeug ${id} in der Registry`);
    assert.equal(f?.art, 'werkzeug');
    assert.equal(f?.zustand, 'verfuegbar');
    assert.equal(f?.toolId, id);
  }
});

test('jede Fähigkeit hat eine gültige Gruppe', () => {
  const gruppen = new Set(FAEHIGKEIT_GRUPPEN.map((g) => g.id));
  for (const f of FAEHIGKEITEN) assert.ok(gruppen.has(f.gruppe), `${f.id}: gültige Gruppe (${f.gruppe})`);
});

test('AUF-28: die falschen Versprechen sind aus der Navi verschwunden', () => {
  // Vorher zeigte die Navi 15 `cad-*`-Einträge (Links ausrichten, Hand, Zoom, Freie Transformation …)
  // mit Zustand „in Entwicklung" — anklickbar, ohne dass ein Klick etwas tat. Sie stammten aus der
  // Zone `weitere`, und die ist seit dem Katalog-Tausch (I2) leer.
  const ids = new Set(FAEHIGKEITEN.map((f) => f.id));
  for (const id of ['cad-rotate', 'cad-scale', 'cad-align-left', 'cad-measure', 'cad-zoom', 'cad-layers-panel']) {
    assert.ok(!ids.has(id), `${id} war ein falsches Versprechen und darf nicht mehr in der Navi stehen`);
  }
  assert.equal([...ids].filter((id) => id.startsWith('cad-')).length, 0);
  for (const id of ['type', 'pen', 'preflight', 'swatches-panel', 'pages-panel']) {
    assert.ok(!ids.has(id), `DTP-Tool ${id} darf NICHT in der Registry sein`);
  }
});

test('TGA/Heizung listet die drei Heizungs-Engines', () => {
  const ids = faehigkeitenNach('tga-heizung').map((f) => f.id);
  for (const id of ['engine-fbh', 'engine-heizkoerper', 'engine-heizkreis']) assert.ok(ids.includes(id), `${id} in TGA/Heizung`);
});

test('Decke ist fachlich der Gruppe Bau zugeordnet, nicht den allgemeinen Werkzeugen', () => {
  assert.ok(faehigkeitenNach('bau').some((f) => f.id === 'decke'));
  assert.ok(!faehigkeitenNach('werkzeuge').some((f) => f.id === 'decke'));
});

test('mehrere Gruppen sind nicht leer (Navi zeigt sie)', () => {
  const nichtLeer = FAEHIGKEIT_GRUPPEN.filter((g) => faehigkeitenNach(g.id).length > 0);
  assert.ok(nichtLeer.length >= 6, `mindestens 6 Gruppen mit Fähigkeiten (ist: ${nichtLeer.length})`);
});
