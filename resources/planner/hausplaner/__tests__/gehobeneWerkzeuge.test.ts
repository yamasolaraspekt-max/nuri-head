/**
 * W-05 — **die gehobenen Werkzeuge, und der Preis ihrer Doppelung.**
 *
 * `toolRegistry` importiert `paketAdapter` NICHT. Diese Richtung ist die ganze Lösung des
 * Import-Kreises: `paketAdapter` liest `TOOL_DEFINITIONS` und wertet `PAKET_ALS_TOOLS` auf
 * Modulebene aus — jede Kante zurück schliesst den Kreis (gefahren, zweimal: `ReferenceError`
 * bei `arbeitsbereiche.ts:69`, dann bei `paketAdapter.ts:53`).
 *
 * **Der Preis: `label`, `icon` und `variante` stehen zweimal.** Diese Datei ist die Gegenleistung.
 * *Ein Test darf beide Seiten importieren — er ist ein Blatt, kein Modul im Kreis.* Damit kostet
 * die Doppelung Konsistenz nichts: laufen die Seiten auseinander, wird hier rot, nicht beim Kunden.
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 4 von 4 blind:**
 *
 * ```text
 * label im Registry-Eintrag verstellt      -> 1667 pass, kommt durch
 * icon  im Registry-Eintrag verstellt      -> 1667 pass, kommt durch
 * eine id aus AUS_PAKET_GEHOBEN entfernt   -> 1667 pass, kommt durch
 * ein Eintrag aus der Registry entfernt    -> 1667 pass, kommt durch
 * ```
 *
 * *Die 1667 bestehenden Zusagen prüfen Zahlen und Reihenfolgen — keine prüft, ob der Handeintrag
 * noch dem Paket entspricht. Genau die Lücke, die die Doppelung aufreisst.*
 *
 * **Gegenprobe danach: drei gefangen, EINE noch blind** — und die vierte war die lehrreiche.
 * „Eine id aus `AUS_PAKET_GEHOBEN` entfernen" liess alle fünf Zusagen grün, weil sie ihre
 * Grundgesamtheit AUS DER LISTE ziehen: wird die Liste kürzer, prüfen sie weniger. *Der Schaden
 * bleibt trotzdem: der Handeintrag steht weiter da, das Werkzeug fällt in den Katalog zurück —
 * und steht dann zweimal.* Die letzte Zusage unten zählt deshalb gegen BEIDE Sammlungen und kann
 * nicht mit dem Prüfling schrumpfen. Danach: 4 von 4 gefangen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { AUS_PAKET_GEHOBEN, TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { PAKET_ALS_TOOLS } from '../app/tools/paketAdapter';
import { PAKET_WERKZEUGE } from '../app/tools/werkzeugPaket';

test('W-05: jede gehobene id steht wirklich im Paket — sonst hebt die Liste ins Leere', () => {
  for (const id of AUS_PAKET_GEHOBEN) {
    const imPaket = PAKET_WERKZEUGE.find((w) => w.id === id);
    assert.ok(imPaket, `AUS_PAKET_GEHOBEN nennt '${id}', das Paket kennt die id nicht`);
  }
  assert.ok(AUS_PAKET_GEHOBEN.length > 0, 'die Hebeliste ist leer — die Zusagen messen Leere');
});

test('W-05: jede gehobene id hat einen Registry-Eintrag — die Liste allein hebt nichts', () => {
  for (const id of AUS_PAKET_GEHOBEN) {
    const eintrag = TOOL_DEFINITIONS.find((t) => t.id === id);
    assert.ok(eintrag, `'${id}' steht in der Hebeliste, aber nicht in TOOL_DEFINITIONS`);
    assert.equal(eintrag.art, 'werkzeug', `'${id}' ist kein Werkzeug und erscheint nicht in der Leiste`);
  }
});

test('W-05 DIE KERNZUSAGE: Handeintrag und Paket stimmen FELD FUER FELD ueberein', () => {
  // **Das ist die Zusage, die die Doppelung bezahlt.** Sie faellt, sobald jemand im Paket ein
  // Label aendert und den Handeintrag vergisst — oder umgekehrt.
  for (const id of AUS_PAKET_GEHOBEN) {
    const paket = PAKET_WERKZEUGE.find((w) => w.id === id)!;
    const registry = TOOL_DEFINITIONS.find((t) => t.id === id)!;

    assert.equal(registry.label, paket.label, `'${id}': label laeuft auseinander`);
    assert.equal(registry.meaning, paket.funktion, `'${id}': Bedeutung laeuft auseinander`);
    assert.equal(registry.usageArea, paket.einsatz, `'${id}': Einsatzbereich laeuft auseinander`);
    assert.equal(registry.group, paket.kategorie, `'${id}': Kategorie laeuft auseinander`);

    // Das Icon steht im Paket als Pfad, in der Registry als Name — verglichen wird der Name.
    const ausPaket = paket.icon.replace(/^.*\//, '').replace(/\.svg$/, '');
    assert.equal(registry.icon, ausPaket, `'${id}': Icon laeuft auseinander`);
  }
});

test('W-05: ein gehobenes Werkzeug ist NICHT mehr im Katalog — es wandert, es verdoppelt sich nicht', () => {
  for (const id of AUS_PAKET_GEHOBEN) {
    assert.ok(!TOOL_KATALOG.some((t) => t.id === id), `'${id}' steht in Registry UND Katalog`);
    // ... aber im PAKET bleibt es: gefiltert wird beim Katalog, nicht an der Quelle.
    assert.ok(PAKET_ALS_TOOLS.some((t) => t.id === id),
      `'${id}' fehlt im Paket — dann verliert verworfeneKuerzel() seine Grundgesamtheit`);
  }
});

test('W-05: kein gehobenes Werkzeug bringt eine Kuerzel-Kollision mit', () => {
  // Die zwei heutigen tragen kein Kuerzel. Die Zusage steht trotzdem: das naechste koennte eins
  // haben, und dann waere eine stille Doppelbelegung der teuerste Fall — zwei Werkzeuge auf
  // derselben Taste, und keines davon ist mehr vorhersagbar erreichbar.
  const kuerzel = TOOL_DEFINITIONS
    .map((t) => t.shortcut?.toLowerCase())
    .filter((s): s is string => Boolean(s));

  assert.equal(new Set(kuerzel).size, kuerzel.length,
    `doppelt belegte Kuerzel in der Registry: ${kuerzel.filter((k, i) => kuerzel.indexOf(k) !== i).join(', ')}`);
});

test('W-05/K-05: KEIN Werkzeug steht in Registry und Katalog zugleich — ueber ALLE ids', () => {
  // **Nachtrag aus der Mutationsprobe.** Die Zusagen oben laufen ueber `AUS_PAKET_GEHOBEN`;
  // nimmt jemand eine id aus der Liste, laufen sie ueber weniger Elemente und bleiben gruen —
  // waehrend der Handeintrag stehen bleibt und das Werkzeug in den Katalog zurueckfaellt.
  // **Dann steht es zweimal.** *Eine Zusage, die ihre Grundgesamtheit aus dem Prueflings-Wert
  // zieht, schrumpft mit ihm.* Diese hier zaehlt gegen BEIDE Sammlungen und kann nicht schrumpfen.
  const doppelt = TOOL_DEFINITIONS
    .map((t) => t.id)
    .filter((id) => TOOL_KATALOG.some((k) => k.id === id));

  assert.deepEqual(doppelt, [],
    `diese Werkzeuge stehen in Registry UND Katalog: ${doppelt.join(', ')}`);
});
