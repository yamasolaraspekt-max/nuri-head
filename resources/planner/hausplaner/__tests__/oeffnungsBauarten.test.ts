import { test } from 'node:test';
import assert from 'node:assert/strict';
import { FENSTER_BAUARTEN, TUER_BAUARTEN, fensterBauartNach, tuerBauartNach } from '../geometry/oeffnungsBauarten';

const GUELTIGE_OA = new Set(['fest', 'dreh', 'kipp', 'dreh-kipp']);

test('Fenster-/Tür-Bauarten: je 24, IDs eindeutig, Datei = id.svg', () => {
  assert.equal(FENSTER_BAUARTEN.length, 24);
  assert.equal(TUER_BAUARTEN.length, 24);
  for (const kat of [FENSTER_BAUARTEN, TUER_BAUARTEN]) {
    const ids = new Set<string>();
    for (const b of kat) {
      assert.ok(b.id.length > 0, 'id nicht leer');
      assert.ok(b.label.length > 0, 'label nicht leer');
      assert.equal(b.datei, `${b.id}.svg`, `Datei passt zur id: ${b.id}`);
      assert.ok(!ids.has(b.id), `id eindeutig: ${b.id}`);
      ids.add(b.id);
      if (b.oeffnungsArt !== undefined) {
        assert.ok(GUELTIGE_OA.has(b.oeffnungsArt), `gültige Öffnungsart: ${b.oeffnungsArt}`);
      }
    }
  }
});

test('Lookup findet per ID und liefert undefined bei Unbekannt', () => {
  assert.equal(fensterBauartNach('05_dreh_kipp_links')?.oeffnungsArt, 'dreh-kipp');
  assert.equal(fensterBauartNach('01_festverglasung')?.oeffnungsArt, 'fest');
  assert.equal(tuerBauartNach('03_haustuer')?.label, 'Haustür');
  assert.equal(fensterBauartNach('gibtsnicht'), undefined);
  assert.equal(fensterBauartNach(undefined), undefined);
});
