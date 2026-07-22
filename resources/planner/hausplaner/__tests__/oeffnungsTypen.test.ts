/**
 * Öffnungs-Typen als Vorlagen (Tür/Fenster). Reine Kataloge.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  TUER_TYPEN,
  FENSTER_TYPEN,
  tuerTyp,
  fensterTyp,
  type TuerTyp,
  type FensterTyp,
} from '../geometry/oeffnungsTypen';

test('Tür-Katalog enthält die gängigen Typen, Drehtür 1-flg. zuerst', () => {
  assert.equal(TUER_TYPEN[0].typ, 'dreh1');
  const typen = TUER_TYPEN.map((v) => v.typ);
  for (const t of ['dreh1', 'dreh2', 'schiebe', 'hebeschiebe', 'falt'] as TuerTyp[]) {
    assert.ok(typen.includes(t), `Tür-Typ ${t} fehlt`);
  }
});

test('Fenster-Katalog enthält Dreh-Kipp, fest, bodentief …', () => {
  const typen = FENSTER_TYPEN.map((v) => v.typ);
  for (const t of ['drehkipp', 'dreh', 'kipp', 'fest', 'zweiflg', 'schiebe', 'boden'] as FensterTyp[]) {
    assert.ok(typen.includes(t), `Fenster-Typ ${t} fehlt`);
  }
});

test('alle Vorlagen: Label + positive, ganzzahlige Standardmaße', () => {
  for (const v of [...TUER_TYPEN, ...FENSTER_TYPEN]) {
    assert.ok(v.label.length > 0);
    assert.ok(Number.isInteger(v.breite) && v.breite > 0, `${v.typ}: Breite`);
    assert.ok(Number.isInteger(v.hoehe) && v.hoehe > 0, `${v.typ}: Höhe`);
  }
});

test('Fenster tragen eine Brüstungshöhe (bodentief = 0)', () => {
  for (const v of FENSTER_TYPEN) {
    assert.ok(v.bruestung !== undefined && Number.isInteger(v.bruestung) && v.bruestung >= 0, `${v.typ}: Brüstung`);
  }
  assert.equal(fensterTyp('boden').bruestung, 0);
});

test('Lookup liefert die passende Vorlage', () => {
  assert.equal(tuerTyp('schiebe').label, 'Schiebetür');
  assert.equal(tuerTyp('dreh1').breite, 875);
  assert.equal(fensterTyp('drehkipp').hoehe, 1360);
});

test('unbekannter Typ fällt sicher zurück (nie undefined)', () => {
  // @ts-expect-error absichtlich ungültig
  assert.equal(tuerTyp('gibtsnicht').typ, 'dreh1');
  // @ts-expect-error absichtlich ungültig
  assert.equal(fensterTyp('gibtsnicht').typ, 'drehkipp');
});
