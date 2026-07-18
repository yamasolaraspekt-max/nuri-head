/**
 * P1a — Adapter-Tests (Abnahmekriterien 2 + 3 der P1-Spec):
 * Round-Trip mm → m → mm byte-gleich (inkl. Extremwerte) und der Achsen-Beweis
 * (Südfenster einer West→Ost-Wand zeigt in three nach +z) — als Unit-Test, nicht als Augenmaß.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { weltZuThree, threeZuWelt, MM_PRO_METER } from '../renderers/three-d/adapter';
import { azimutDerNormalen } from '../geometry/wallGeometry';

test('weltZuThree: definierte Abbildung (x→x/1000, z→y/1000, y→−z/1000)', () => {
  assert.deepEqual(weltZuThree({ x: 4000, y: 6000, z: 2500 }), { x: 4, y: 2.5, z: -6 });
});

test('Round-Trip threeZuWelt(weltZuThree(p)) ist byte-gleich — inkl. Extremwerte (Kriterium 3)', () => {
  const referenzPunkte = [
    { x: 0, y: 0, z: 0 },
    { x: 1, y: -1, z: 1 },                                    // kleinste mm-Schritte
    { x: 999, y: -999, z: 333 },                              // /1000 nicht float-exakt
    { x: 123_456_789, y: -123_456_789, z: 20_000 },           // große Szene
    { x: 2_147_483_647, y: -2_147_483_647, z: 2_147_483_647 } // int32-Extremwert
  ];
  for (const p of referenzPunkte) {
    assert.deepEqual(threeZuWelt(weltZuThree(p)), p, `Round-Trip verliert mm bei ${JSON.stringify(p)}`);
  }
});

test('Round-Trip flächig: alle mm in [−2000, 2000] auf allen Achsen byte-gleich', () => {
  for (let mm = -2000; mm <= 2000; mm += 7) {
    const p = { x: mm, y: -mm, z: mm };
    assert.deepEqual(threeZuWelt(weltZuThree(p)), p);
  }
});

test('Achsen-Beweis (Kriterium 2): Südfenster einer West→Ost-Wand zeigt in three nach +z', () => {
  // Referenzwand West→Ost: Laufrichtung +x; die rechte Normale zeigt nach Süden (Azimut 180).
  const start = { x: 0, y: 0 };
  const end = { x: 5000, y: 0 };
  assert.equal(azimutDerNormalen(start, end, 'rechts'), 180, 'Vorbedingung: rechte Normale = Süd');

  // Süd-Normale als Richtungsvektor in Welt-mm: (0, −1, 0). Die Abbildung ist linear
  // (keine Translation), gilt also unverändert für Richtungen.
  const suedInThree = weltZuThree({ x: 0, y: -MM_PRO_METER, z: 0 });
  assert.ok(suedInThree.z > 0, 'Süden muss in three nach +z zeigen (Nord = −z)');
  assert.equal(suedInThree.x, 0);
  assert.equal(suedInThree.y, 0);

  // Gegenprobe Nord: Welt (0, +1, 0) ⇒ three −z.
  const nordInThree = weltZuThree({ x: 0, y: MM_PRO_METER, z: 0 });
  assert.ok(nordInThree.z < 0, 'Norden muss in three nach −z zeigen');
});

test('Höhe bleibt Höhe: Welt +z (oben) ⇒ three +y (y-up)', () => {
  const obenInThree = weltZuThree({ x: 0, y: 0, z: MM_PRO_METER });
  assert.deepEqual(obenInThree, { x: 0, y: 1, z: -0 });
  assert.ok(obenInThree.y > 0);
});
