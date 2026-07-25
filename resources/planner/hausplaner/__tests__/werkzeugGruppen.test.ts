/**
 * I4 (AUF-21) — die 110 Werkzeuge sind über 22 Kategorie-Gruppen erreichbar.
 *
 * Das Abnahmekriterium ist eine Bilanz: **22 Gruppen, Summe 110, jedes Werkzeug in genau einer
 * Gruppe, keines unerreichbar.** Genau das prüft diese Datei — und zwar so, dass ein verlorenes
 * Werkzeug auffällt, nicht erst dem Nutzer im Menü.
 *
 * Dazu die Anheftung (★): sie ist eine **Vorliebe des Bedieners**, keine Eigenschaft des Gebäudes.
 * Der Test hält fest, dass sie das Szenendokument nicht berührt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { WERKZEUG_GRUPPEN, gruppeNach, gruppeVonWerkzeug, mehrfachGefuehrt, iconPfad } from '../app/dashboard/werkzeugGruppen';
import { umschalten, ladeAngeheftet, speichereAngeheftet, ANGEHEFTET_SCHLUESSEL } from '../app/state/angeheftet';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { zoneTools } from '../app/tools/toolPresentation';

const hier = dirname(fileURLToPath(import.meta.url));
const angeheftetQuelle = readFileSync(join(hier, '../app/state/angeheftet.ts'), 'utf8');

// --- Bilanz: nichts verloren, nichts doppelt ---------------------------------------------------
test('22 Gruppen, Summe 110 — jedes Werkzeug genau einmal', () => {
  assert.equal(WERKZEUG_GRUPPEN.length, 22);
  const summe = WERKZEUG_GRUPPEN.reduce((s, g) => s + g.werkzeuge.length, 0);
  assert.equal(summe, 110, '101 Katalog + 9 Registry');
  assert.deepEqual(mehrfachGefuehrt(), [], 'kein Werkzeug in zwei Gruppen');
});

test('jedes Werkzeug aus Katalog UND Registry findet seine Gruppe', () => {
  for (const t of [...TOOL_KATALOG, ...TOOL_DEFINITIONS]) {
    assert.ok(gruppeVonWerkzeug(t.id), `${t.id} steht in keiner Gruppe — im Menü unerreichbar`);
  }
});

test('kein Werkzeug bleibt versteckt — Kriterium 4', () => {
  assert.equal(zoneTools('versteckt').length, 0);
});

test('jede Gruppe hat Schlüssel, Label und mindestens einen Eintrag', () => {
  for (const g of WERKZEUG_GRUPPEN) {
    assert.match(g.id, /^[a-z0-9-]+$/, `${g.id}: Schlüssel muss ASCII/klein sein`);
    assert.ok(g.label.length > 1, `${g.id}: Label fehlt`);
    assert.ok(g.werkzeuge.length >= 1, `${g.label}: leere Gruppe wäre ein leeres Menü`);
  }
  assert.equal(new Set(WERKZEUG_GRUPPEN.map((g) => g.id)).size, 22, 'doppelter Gruppen-Schlüssel');
});

test('Kante 2 — Ein-Eintrag-Gruppen sind zulässig und bewusst: TGA und Sanitär', () => {
  // Entscheidung im Bericht benannt: eigene Gruppe statt Sammelkorb. Eine Kategorie mit einem
  // Werkzeug wächst; ein „Sonstiges" wüchse nie wieder auseinander.
  for (const id of ['tga', 'sanitaer']) {
    assert.equal(gruppeNach(id)?.werkzeuge.length, 1, `${id}: erwartet genau ein Werkzeug`);
  }
});

test('jeder Eintrag hat Label und Icon-Pfad — Kriterium 5', () => {
  for (const g of WERKZEUG_GRUPPEN) {
    for (const t of g.werkzeuge) {
      assert.ok(t.label.length > 0, `${t.id}: Label fehlt`);
      assert.match(iconPfad(t), /^\/hausplaner\/icons\/tools\/[a-z0-9-]+\.svg$/, `${t.id}: Icon-Pfad unbrauchbar`);
    }
  }
});

test('die Reihenfolge folgt dem Arbeitsablauf, nicht der Größe', () => {
  const ids = WERKZEUG_GRUPPEN.map((g) => g.id);
  assert.equal(ids[0], 'auswahl', 'zuerst auswählen');
  assert.ok(ids.indexOf('architektur') < ids.indexOf('system'), 'Gebäude vor System');
  assert.equal(ids[ids.length - 1], 'system');
});

// --- Anheften: persönlich, und niemals in der Szene --------------------------------------------
test('Anheften und Lösen ändern den persönlichen Zustand — rein, ohne Nebenwirkung', () => {
  const leer = new Set<string>();
  const eins = umschalten(leer, 'raum');
  assert.deepEqual([...eins], ['raum']);
  assert.equal(leer.size, 0, 'die Eingabemenge bleibt unberührt');
  assert.deepEqual([...umschalten(eins, 'raum')], [], 'nochmal drücken löst wieder');
});

test('Kriterium 6: die Anheftung berührt das Szenendokument nicht', () => {
  assert.doesNotMatch(angeheftetQuelle, /executeCommand|SceneDocument|scene\./, 'kein Zugriff auf die Szene');
  assert.match(angeheftetQuelle, /localStorage/, 'gewählter Ort ist der lokale Speicher');
  assert.equal(ANGEHEFTET_SCHLUESSEL, 'hausplaner.angeheftet.v1');
});

test('ohne Browser: leere Menge statt Absturz (Testlauf, Server-Rendering)', () => {
  assert.deepEqual([...ladeAngeheftet()], []);
  assert.doesNotThrow(() => speichereAngeheftet(new Set(['raum'])));
});
