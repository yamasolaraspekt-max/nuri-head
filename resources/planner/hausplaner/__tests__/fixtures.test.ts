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

test('u-dach: u-shape-Dach mit ALLEN vier Maßen (treibt die U-Verschneidung)', () => {
  const szene = ladeFixture('u-dach');
  assert.ok(szene, 'u-dach existiert');
  const dach = szene!.roofs[0];
  assert.equal(dach.roofType, 'u-shape');
  const a = dach.anbau;
  assert.ok(a && a.length > 0 && a.width > 0 && (a.lengthB ?? 0) > 0 && (a.widthB ?? 0) > 0,
    'alle vier Maße gesetzt — sonst wäre die Kerbe erfunden (Operanden-Gate)');
  // U-Grundriss trägt die Kerbe (8 Punkte) und ein geschlossener Wandring liegt darunter.
  assert.equal(dach.polygon.length, 8);
  assert.equal(szene!.nodes.filter((n) => n.type === 'wall').length, 8);
});

test('fixtureNameAusSearch: liest ?fixture=, sonst null; unbekannt ⇒ ladeFixture null', () => {
  assert.equal(fixtureNameAusSearch('?fixture=u-dach'), 'u-dach');
  assert.equal(fixtureNameAusSearch('?capture=1'), null);
  assert.equal(fixtureNameAusSearch(''), null);
  assert.equal(ladeFixture('gibt-es-nicht'), null, 'unbekannt ⇒ Fallback auf eingebettete Szene');
  assert.equal(ladeFixture(null), null);
});
