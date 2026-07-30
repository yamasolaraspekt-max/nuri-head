/**
 * AUF-48 Scheibe 3 / K-02 — **keine Tastenbelegung hat sich geändert.**
 *
 * Die Zuordnung war bisher eine if/else-Kette *innerhalb* eines `useEffect` — nicht aufrufbar und
 * damit nie geprüft. Hier steht sie Taste für Taste, **einschließlich der beiden Reihenfolgen, die
 * bewusst so sind und die man aus der Kette nicht ablesen konnte.**
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { tastenAbsicht, type TastenEreignis } from '../app/tastenAbsicht';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';

/** Ein Tastendruck ohne Besonderheiten — die Felder, die die Zuordnung liest. */
const t = (ueber: Partial<TastenEreignis>): TastenEreignis => ({
  key: 'x', ctrlKey: false, metaKey: false, zielIstEingabe: false, paletteOffen: false, ...ueber,
});

// --- Die zwei Fälle, in denen gar nichts passiert -------------------------------------------------

test('K-02: im Eingabefeld tut keine Taste etwas — man tippt', () => {
  for (const key of ['Delete', 'Backspace', 'z', 'k', 'w', 'v']) {
    assert.equal(tastenAbsicht(t({ key, zielIstEingabe: true, ctrlKey: true })).art, 'ignorieren', `${key} griff im Eingabefeld`);
  }
});

test('K-02 (Kante 8): bei offener Palette schlägt KEINE Taste durch', () => {
  // Sonst wechselte ein Tastendruck im Filterfeld das Werkzeug.
  for (const key of ['w', 'v', 'Delete', 'z']) {
    assert.equal(tastenAbsicht(t({ key, paletteOffen: true, ctrlKey: key === 'z' })).art, 'ignorieren', `${key} schlug durch`);
  }
});

// --- Löschen ---------------------------------------------------------------------------------------

test('K-02: Delete UND Backspace löschen — beide, nicht nur eine', () => {
  assert.equal(tastenAbsicht(t({ key: 'Delete' })).art, 'loeschen');
  assert.equal(tastenAbsicht(t({ key: 'Backspace' })).art, 'loeschen');
});

test('K-02: Löschen unterdrückt das Ereignis NICHT — das war schon so', () => {
  assert.equal(tastenAbsicht(t({ key: 'Delete' })).preventDefault, false);
});

// --- Die vier Betriebssystem-Kürzel ----------------------------------------------------------------

test('K-02: ⌘Z · ⌘Y · ⌘S · ⌘K — mit Strg UND mit Meta, gross wie klein', () => {
  const paare: Array<[string, string]> = [['z', 'rueckgaengig'], ['y', 'wiederholen'], ['s', 'speichern'], ['k', 'palette-oeffnen']];
  for (const [key, art] of paare) {
    for (const mod of [{ ctrlKey: true }, { metaKey: true }]) {
      assert.equal(tastenAbsicht(t({ key, ...mod })).art, art, `${key} mit ${JSON.stringify(mod)}`);
      // Grossbuchstabe (Umschalt gedrückt) muss dasselbe bedeuten.
      assert.equal(tastenAbsicht(t({ key: key.toUpperCase(), ...mod })).art, art, `${key.toUpperCase()} mit ${JSON.stringify(mod)}`);
    }
  }
});

test('K-02: genau diese vier unterdrücken das Ereignis — und nur sie', () => {
  // Ohne `preventDefault` führte der Browser seine eigene Bedeutung aus (Seite speichern, Suche).
  for (const key of ['z', 'y', 's', 'k']) {
    assert.equal(tastenAbsicht(t({ key, ctrlKey: true })).preventDefault, true, `${key} unterdrückt nicht`);
  }
  for (const key of ['Delete', 'Backspace', 'w', 'v']) {
    assert.equal(tastenAbsicht(t({ key })).preventDefault, false, `${key} unterdrückt fälschlich`);
  }
});

test('K-02: OHNE Modifikator sind z/y/s/k KEINE Befehle', () => {
  // `k` ist das Registry-Kürzel von „Decke" — ohne Modifikator muss es das bleiben.
  assert.notEqual(tastenAbsicht(t({ key: 'z' })).art, 'rueckgaengig');
  assert.notEqual(tastenAbsicht(t({ key: 's' })).art, 'speichern');
  assert.notEqual(tastenAbsicht(t({ key: 'k' })).art, 'palette-oeffnen');
});

// --- Die Reihenfolge, die man aus der alten Kette nicht ablesen konnte -----------------------------

test('K-02 (Vorrang): ⌘K öffnet die Palette und wählt NICHT „Decke"', () => {
  // **Der Grund, warum der ⌘K-Zweig vor dem Kürzel-Zweig steht.** Der Kürzel-Zweig prüft die
  // Modifikatoren nicht — stünde er zuerst, griffe „K" auch mit Strg/⌘.
  const mit = tastenAbsicht(t({ key: 'k', metaKey: true }));
  assert.equal(mit.art, 'palette-oeffnen');
  const ohne = tastenAbsicht(t({ key: 'k' }));
  assert.equal(ohne.art, 'werkzeug');
  assert.equal(ohne.werkzeugId, 'decke', 'ohne Modifikator muss K weiterhin die Decke wählen');
});

// --- Werkzeug-Kürzel aus der Registry ---------------------------------------------------------------

test('K-02: JEDES Registry-Kürzel eines Werkzeugs ergibt genau sein Werkzeug', () => {
  // Aus der Registry gelesen, nicht getippt — kommt ein Kürzel hinzu, wächst die Zusage mit.
  const mitKuerzel = TOOL_DEFINITIONS.filter((w) => w.shortcut && w.art === 'werkzeug');
  assert.ok(mitKuerzel.length >= 5, `nur ${mitKuerzel.length} Werkzeuge mit Kürzel — die Zusage misst Leere`);
  for (const w of mitKuerzel) {
    const a = tastenAbsicht(t({ key: w.shortcut! }));
    assert.equal(a.art, 'werkzeug', `${w.shortcut} (${w.id}) ergibt keine Werkzeug-Absicht`);
    assert.equal(a.werkzeugId, w.id, `${w.shortcut} zeigt auf das falsche Werkzeug`);
  }
});

test('K-02: eine unbelegte Taste bedeutet nichts — und wirft nicht', () => {
  for (const key of ['ä', 'F5', '§', 'ArrowLeft']) {
    assert.equal(tastenAbsicht(t({ key })).art, 'nichts', `${key} ist unerwartet belegt`);
  }
});

// --- Die Abbildung kennt die Komponente nicht -------------------------------------------------------

test('K-01: dieselbe Eingabe ergibt immer dieselbe Absicht — kein verborgener Zustand', () => {
  const e = t({ key: 'w' });
  const a = tastenAbsicht(e);
  for (let i = 0; i < 5; i += 1) {
    assert.deepEqual(tastenAbsicht(e), a, 'die Zuordnung hängt von etwas ab, das nicht im Ereignis steht');
  }
});
