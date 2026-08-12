import test from 'node:test';
import assert from 'node:assert/strict';
import { enginePanel } from '../app/dashboard/enginePanels.ts';

/**
 * A-14 — der N-003-Vorbehalt als Zusage, nicht als Probelauf.
 *
 * Gefordert war ein Probelauf (A-14-2). Daraus ist ein bleibender Test geworden: die Frage
 * "überlebt das Pflichtfeld den Cast?" muss auch in einem Jahr noch beantwortet sein, und ein
 * Probelauf beantwortet sie einmal. `sparrenBerechnung.test.ts` bleibt dabei unberührt (A-14-5).
 */
test('A-14-2: vorbehalt ueberlebt berechneSparren(...) as unknown as EngineErgebnis', () => {
  const panel = enginePanel('engine-sparren');
  assert.ok(panel, 'Panel engine-sparren gefunden');
  const werte: Record<string, string> = {};
  // Vorgaben nehmen; wo eine Auswahl steht, ihren ERSTEN Wert — nicht raten.
  for (const f of panel.felder) {
    const a = (f as { auswahl?: readonly { wert: string }[] }).auswahl;
    werte[f.schluessel] = a && a.length > 0 ? a[0].wert : String(f.vorgabe ?? 1);
  }
  const ergebnis = panel.berechne(werte);
  const schluessel = Object.keys(ergebnis);
  console.log('    Schluessel nach dem Cast:', schluessel.join(', '));
  console.log('    vorbehalt =', JSON.stringify((ergebnis as Record<string, unknown>).vorbehalt));
  console.log('    bestanden =', JSON.stringify((ergebnis as Record<string, unknown>).bestanden));
  console.log('    keinGesamturteil am Panel =', JSON.stringify(panel.keinGesamturteil));
  assert.equal((ergebnis as Record<string, unknown>).vorbehalt,
    'Vorbemessung, ersetzt keine prüffähige Statik');
  assert.equal(typeof (ergebnis as Record<string, unknown>).bestanden, 'boolean',
    'bestanden bleibt erhalten — es wird NICHT entfernt');
  assert.equal(panel.keinGesamturteil, true);
  assert.ok(panel.ergebnisFelder.some((f) => f.schluessel === 'vorbehalt'),
    'vorbehalt steht in derselben Ergebnisliste wie die Ausnutzungen');
});
