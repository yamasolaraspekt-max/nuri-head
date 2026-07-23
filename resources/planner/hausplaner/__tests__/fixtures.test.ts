/**
 * Studio-Fixtures — jede Fixture MUSS die volle Boot-Pipeline überstehen (migriereSzene → Schema →
 * Integrität), sonst lädt sie im Browser nicht. Plus: die u-dach-Fixture treibt die U-Form wirklich
 * (alle vier anbau-Maße > 0 → anbauZuEingabe liefert Eingabe statt null, kein erfundener Innenhof).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { STUDIO_FIXTURES, ladeFixture, fixtureNameAusSearch } from '../fixtures/studioFixtures';
import { sceneDocumentSchema, validateSceneIntegrity, migriereSzene } from '../domain/validation';

test('jede Fixture überlebt die volle Boot-Pipeline (Schema + Integrität)', () => {
  for (const [name, bauer] of Object.entries(STUDIO_FIXTURES)) {
    const geparst = sceneDocumentSchema.safeParse(migriereSzene(bauer()));
    assert.equal(geparst.success, true, `${name}: Schema ${geparst.success ? '' : JSON.stringify(geparst.error?.issues)}`);
    if (geparst.success) {
      assert.deepEqual(validateSceneIntegrity(geparst.data), [], `${name}: Integritätsfehler`);
    }
  }
});

test('decke-treppe: Decke mit Treppenauge + Treppe im Level (Slab-mit-Loch-Sicht)', () => {
  const szene = ladeFixture('decke-treppe');
  assert.ok(szene, 'decke-treppe existiert');
  const decke = szene!.ceilings?.[0];
  assert.ok(decke, 'eine Decke');
  assert.equal(decke!.polygon.length, 4, 'Rechteck-Slab');
  assert.ok((decke!.oeffnungen?.length ?? 0) >= 1, 'Treppenauge als Durchbruch gesetzt');
  assert.equal(szene!.nodes.filter((n) => n.type === 'object' && (n as { objectType?: string }).objectType === 'stair').length, 1, 'Treppe im Level');
});

test('fixtureNameAusSearch: liest ?fixture=, sonst null; unbekannt ⇒ ladeFixture null', () => {
  assert.equal(fixtureNameAusSearch('?fixture=decke-treppe'), 'decke-treppe');
  assert.equal(fixtureNameAusSearch('?capture=1'), null);
  assert.equal(fixtureNameAusSearch(''), null);
  assert.equal(ladeFixture('gibt-es-nicht'), null, 'unbekannt ⇒ Fallback auf eingebettete Szene');
  assert.equal(ladeFixture(null), null);
});
