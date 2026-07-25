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
import { WERKZEUG_GRUPPEN, gruppeNach, gruppeVonWerkzeug, mehrfachGefuehrt, werkzeugeOhneGruppe, iconPfad } from '../app/dashboard/werkzeugGruppen';
import { umschalten, ladeAngeheftet, speichereAngeheftet, ANGEHEFTET_SCHLUESSEL } from '../app/state/angeheftet';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { zoneTools } from '../app/tools/toolPresentation';

const hier = dirname(fileURLToPath(import.meta.url));
const angeheftetQuelle = readFileSync(join(hier, '../app/state/angeheftet.ts'), 'utf8');

// --- Bilanz: nichts verloren, nichts doppelt ---------------------------------------------------
// AUF-34/Nachtrag 2: Die Bilanz gilt unverändert — nur die Zahl der Gruppen hat sich geändert
// (22 Kategorien ⇒ 15 Themen, Yamas Entscheidung „15"). Die Summe bleibt 110.
test('15 Gruppen, Summe 110 — jedes Werkzeug genau einmal', () => {
  assert.equal(WERKZEUG_GRUPPEN.length, 15);
  const summe = WERKZEUG_GRUPPEN.reduce((s, g) => s + g.werkzeuge.length, 0);
  assert.equal(summe, 110, '101 Katalog + 9 Registry');
  assert.deepEqual(mehrfachGefuehrt(), [], 'kein Werkzeug in zwei Gruppen');
  assert.deepEqual(werkzeugeOhneGruppe(), [], 'kein Werkzeug ohne Thema — sonst unerreichbar');
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
    assert.ok(g.kurz.length > 1, `${g.id}: Kurzform fehlt — die Leiste hätte einen leeren Knopf`);
    assert.ok(g.werkzeuge.length >= 1, `${g.label}: leere Gruppe wäre ein leeres Menü`);
  }
  assert.equal(new Set(WERKZEUG_GRUPPEN.map((g) => g.id)).size, 15, 'doppelter Gruppen-Schlüssel');
});

test('AUF-34: keine Ein-Eintrag-Gruppe mehr — genau das war der Mangel', () => {
  // Vorher (22 Kategorien): `TGA` und `Sanitär` trugen je EIN Werkzeug, zwei volle Menüs für je
  // einen Eintrag. Die 15 Themen fassen sie zusammen (`Heizung, Hydraulik & TGA` = 6,
  // `Sanitär, Bad & Küche` = 7). Die kleinste Gruppe hat jetzt vier Einträge.
  const kleinste = Math.min(...WERKZEUG_GRUPPEN.map((g) => g.werkzeuge.length));
  assert.ok(kleinste >= 4, `kleinste Gruppe hat ${kleinste} Einträge — erwartet mindestens 4`);
  assert.equal(gruppeNach('10-heizung-tga')?.werkzeuge.length, 6);
  assert.equal(gruppeNach('11-bad-kueche')?.werkzeuge.length, 7);
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
  assert.equal(ids[0], '01-grundbedienung', 'zuerst auswählen');
  assert.ok(ids.indexOf('07-architektur') < ids.indexOf('15-system-export'), 'Gebäude vor System');
  assert.equal(ids[ids.length - 1], '15-system-export');
  // Paket-Reihenfolge 01…15, fest verdrahtet: eine Leiste, die sich mit den Daten umsortiert,
  // zwingt den Nutzer jedes Mal zum Suchen.
  assert.deepEqual(ids, [...ids].sort(), 'die Themen stehen in ihrer numerischen Paket-Reihenfolge');
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
