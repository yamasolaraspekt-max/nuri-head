/**
 * AUF-44 — die „(geplant)"-Knöpfe der Icon-Zeile.
 *
 * **Der Befund:** Knöpfe, die nichts tun und es nur im `title` zugeben — „Auswahl um 90° drehen
 * **(geplant)**", „Als PDF-Planblatt exportieren **(geplant)**". Dieselbe Sorte falsches
 * Versprechen, die I2 aus dem Katalog entfernt hat, in der Icon-Zeile stehengeblieben.
 *
 * **Die Messung hat die Willensfrage entschieden:** Vier der fünf sind **Dubletten** — die
 * Werkzeuge existieren wirklich und stehen in ihrer Themen-Gruppe mit ehrlichem Zustand. Entfernt
 * wurde die tote Kopie, **nicht das Werkzeug**; damit bleibt auch die Forderung des
 * Nachbarpostens AUF-59 gewahrt („kein Werkzeug verschwindet"). Der fünfte, „Ansicht einpassen",
 * hat **kein** Gegenstück und bleibt deshalb stehen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { themaVonWerkzeug } from '../app/tools/werkzeugThemen';
import { vertrag } from '../app/tools/werkzeugVertrag';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const app = ohneKommentare((readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8')
  // AUF-48 Scheibe 4a: der Kopfrahmen (Werkzeugzeile, Arbeitsbereich-Waehler,
  // Bedien-Werkzeugleiste) ist nach `dashboard/Kopfrahmen.tsx` ausgezogen. **Beide Dateien
  // werden gelesen** — die geprueften Eigenschaften sind unveraendert, und eine Absenz-Zusage
  // darf nicht dadurch gruen werden, dass Inhalt eine Datei weiter gewandert ist.
  + readFileSync(join(hier, '../app/dashboard/Kopfrahmen.tsx'), 'utf8')));
const ALLE = [...TOOL_DEFINITIONS, ...TOOL_KATALOG];

/** Die vier entfernten Icon-Knöpfe und das Werkzeug, das sie doppelt hatten. */
const DUBLETTEN: Array<{ knopf: string; werkzeug: string }> = [
  { knopf: 'Auswahl um 90° drehen', werkzeug: 'drehen' },
  { knopf: 'Messwerkzeug — Abstand zwischen zwei Punkten messen', werkzeug: 'distanz-messen' },
  { knopf: 'Bemaßung — Maßkette am Grundriss anlegen', werkzeug: 'bemassen' },
  { knopf: 'Als PDF-Planblatt exportieren', werkzeug: 'pdf' },
];

test('die vier toten Versprechen sind aus der Icon-Zeile verschwunden', () => {
  for (const d of DUBLETTEN) {
    assert.ok(!app.includes(d.knopf), `„${d.knopf}" steht noch in der Icon-Zeile`);
  }
});

test('aber das WERKZEUG ist jeweils geblieben — mit Thema und Vertrag', () => {
  // Genau darauf beruht die Entscheidung: entfernt wurde eine Kopie, keine Funktion.
  for (const d of DUBLETTEN) {
    const t = ALLE.find((x) => x.id === d.werkzeug);
    assert.ok(t, `${d.werkzeug} fehlt im Katalog — dann wäre wirklich etwas verschwunden`);
    assert.ok(themaVonWerkzeug(d.werkzeug), `${d.werkzeug} steht in keiner Themen-Gruppe`);
    assert.ok(vertrag(d.werkzeug), `${d.werkzeug} hat keinen Funktionsvertrag`);
  }
});

test('„Ansicht einpassen" ist geblieben — und tut seit AUF-62 etwas', () => {
  // AUF-44 hat vier tote Knöpfe entfernt und diesen EINEN behalten, weil er als einziger keine
  // Dublette war: es gibt kein Werkzeug dieses Zwecks. Die Begründung gilt unverändert — nur ist
  // der Knopf seit AUF-62 kein Versprechen mehr, sondern eine Handlung.
  assert.match(app, /title="Ansicht einpassen — gesamten Grundriss ins Bild rücken" icon="einpassen" onClick=\{passeAnsichtEin\}/);
  for (const id of ['einpassen', 'ansicht-einpassen', 'zoom-einpassen']) {
    assert.equal(ALLE.find((t) => t.id === id), undefined, `${id} existiert doch — dann wäre auch dieser Knopf eine Dublette`);
  }
});

test('KEIN „geplant"-Knopf ist mehr übrig — der letzte hat mit AUF-62 seine Funktion bekommen', () => {
  // Vorher war es genau einer. Steigt die Zahl wieder, hat jemand ein neues Versprechen ohne
  // Deckung in die Zeile gestellt — genau das, was AUF-44 abgeräumt hat.
  assert.equal((app.match(/geplant \/>/g) ?? []).length, 0);
});

test('die `geplant`-Mechanik bleibt erhalten — für den nächsten Knopf, der sie braucht', () => {
  const opbtn = app.match(/const OpBtn = [\s\S]*?\n  \);/);
  assert.ok(opbtn, 'OpBtn nicht gefunden');
  assert.match(opbtn[0], /title=\{geplant \? `\$\{title\} \(geplant\)` : title\}/);
  assert.match(opbtn[0], /onClick=\{geplant \? undefined : onClick\}/);
  assert.match(opbtn[0], /disabled=\{disabled \|\| geplant\}/);
});

test('kein Werkzeug hat die Oberfläche verlassen — die Bilanz bleibt 110', () => {
  assert.equal(ALLE.length, 110);
});
