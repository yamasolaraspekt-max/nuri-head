import test from 'node:test';
import assert from 'node:assert/strict';
import { enginePanel, ENGINE_PANELS } from '../app/dashboard/enginePanels.ts';

/**
 * A-17 — `abwassergefaelle` und `fbhAuslegung` verlieren das Gesamturteil.
 *
 * Nach dem Muster von `sparrenVorbehalt.test.ts` (A-14): aus den geforderten Gegenproben wird ein
 * bleibender Test. Die schärfere Probe ist die zweite Zusage unten — **das Flag darf EINE Engine
 * stumm schalten, nicht negative Urteile allgemein.** So hat der Release-Prüfer A-14 geprüft.
 */
const werteAusVorgaben = (panel: NonNullable<ReturnType<typeof enginePanel>>): Record<string, string> => {
  const werte: Record<string, string> = {};
  for (const f of panel.felder) {
    const a = (f as { auswahl?: readonly { wert: string }[] }).auswahl;
    werte[f.schluessel] = a && a.length > 0 ? a[0].wert : String(f.vorgabe ?? 1);
  }
  return werte;
};

for (const [id, wortlaut] of [
  ['engine-abwasser', 'DIN 1986-100 vereinfacht — Mindestgefaelle und Fallstrang-Distanz. '
    + 'Kein Entwaesserungsnachweis, keine Genehmigungsunterlage.'],
  ['engine-fbh', 'Rohrlaengen, Kreise und Plausibilitaet. Hydraulischer Abgleich und '
    + 'normative Auslegung sind NICHT erfasst.'],
] as const) {
  test(`A-17: ${id} schweigt — Flag gesetzt, Vorbehalt im Ergebnis, zeichengenau`, () => {
    const panel = enginePanel(id);
    assert.ok(panel, `Panel ${id} gefunden`);
    assert.equal(panel.keinGesamturteil, true, 'keinGesamturteil ist gesetzt');

    const ergebnis = panel.berechne(werteAusVorgaben(panel)) as Record<string, unknown>;
    console.log(`    ${id}  vorbehalt =`, JSON.stringify(ergebnis.vorbehalt));
    console.log(`    ${id}  bestanden =`, JSON.stringify(ergebnis.bestanden));

    // Zeichengenau gegen den ausgeschriebenen Wortlaut — ein Vergleich gegen die Konstante allein
    // haelte zwei Verweise auf dieselbe Zeichenkette nebeneinander und bliebe bei jeder stillen
    // Umformulierung gruen.
    assert.equal(ergebnis.vorbehalt, wortlaut);
    assert.equal(typeof ergebnis.bestanden, 'boolean',
      'bestanden bleibt im Datensatz — es wird NICHT entfernt (A-17-5)');
    assert.ok(panel.ergebnisFelder.some((f) => f.schluessel === 'vorbehalt'),
      'der Vorbehalt steht in DERSELBEN Werteliste wie die Zahlen, nicht in einer Fussnote');
    assert.ok(panel.grundlage.includes('NICHT erfasst') || panel.grundlage.includes('Kein Entwaesserungsnachweis'),
      'die grundlage-Zeile nennt die Reichweitengrenze');
  });
}

test('A-17-1/2 Gegenprobe: das Flag schaltet ZWEI Engines stumm, nicht das Urteil allgemein', () => {
  const stumm = ENGINE_PANELS.filter((p) => p.keinGesamturteil === true).map((p) => p.engineId);
  const laut = ENGINE_PANELS.filter((p) => p.keinGesamturteil !== true).map((p) => p.engineId);
  console.log('    stumm:', stumm.join(', '));
  console.log('    mit Gesamturteil:', laut.join(', '));

  for (const id of ['engine-abwasser', 'engine-fbh', 'engine-sparren']) {
    assert.ok(stumm.includes(id), `${id} ist stumm`);
  }
  // Die eigentliche Gegenprobe: mindestens drei andere Panels behalten das Gesamturteil.
  assert.ok(laut.length >= 3, `mindestens drei Panels behalten die Plakette — gemessen ${laut.length}`);
});

test('A-17-1/2 schaerfere Gegenprobe: ein NEGATIVES Urteil bleibt sichtbar', () => {
  // Die Frage, an der A-14 geprueft wurde: unterdrueckt das Flag nur die eine Engine, oder faellt
  // damit auch die rote Plakette anderswo? Gesucht wird ein Panel, das bestanden=false liefert.
  const werte = (p: NonNullable<ReturnType<typeof enginePanel>>) => werteAusVorgaben(p);
  const treffer = ENGINE_PANELS
    .filter((p) => p.keinGesamturteil !== true)
    .map((p) => ({ id: p.engineId, e: p.berechne(werte(p)) as Record<string, unknown> }))
    .filter((x) => x.e.bestanden === false);

  console.log('    Panels mit bestanden=false und ERHALTENER Plakette:',
    treffer.map((t) => t.id).join(', ') || '(keines)');
  assert.ok(treffer.length >= 1,
    'mindestens ein Panel liefert bestanden=false UND behaelt sein Gesamturteil — '
    + 'das Flag unterdrueckt EINE Engine, nicht negative Urteile allgemein');
});
