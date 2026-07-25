/**
 * L4 (AUF-25) — die Fachplaner-Flächen sind Daten, also prüfbar.
 *
 * Herkunft des Datenmoduls: entworfen vom Cowork-Strang, der wegen Doppelbelegung abgebrochen und
 * seine Arbeit als Materialspende beiseitegelegt hat (`docs/auftraege/l4-generator-beiseite-25-07/`).
 * Der zugehörige Test wurde dort nie geschrieben — er steht hier, vom nativen Strang, der die
 * Umsetzung übernommen hat.
 *
 * Was verriegelt wird: Anzahl, Eindeutigkeit, Zustand, Zwecktext, die Feldstruktur (tief statt flach),
 * die Trennung zu den vier echten Konfiguratoren — und, am wichtigsten, die **Deckung in beide
 * Richtungen**: kein anklickbares Modul ohne Fläche, keine Fläche ohne Modul.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  FACH_FLAECHEN, KONFIGURATOR_NAMEN, HERKUNFT_ZURUECK,
  fachFlaecheNach, fachFlaecheMitId, zurueckLabel, anklickbareModule,
  fehlendeFlaechen, verwaisteFlaechen,
} from '../app/dashboard/fachFlaechen';

const ZUSTAENDE = ['verfuegbar', 'voraussetzung', 'nur_ergebnis', 'in_entwicklung'];
const hier = dirname(fileURLToPath(import.meta.url));

/**
 * Kommentare entfernen, bevor über den Quelltext geprüft wird — sonst schlägt die Prüfung auf den
 * erklärenden Kommentar an („Fläche statt ‚Konfigurator folgt'-Toast") und misst Prosa statt Code.
 * Dieselbe Funktion steht in `leisteAusZonen.test.ts`; beim dritten Vorkommen gehört sie in einen
 * gemeinsamen Test-Helfer — als eigener Posten zurückgegeben, nicht hier nebenbei gebaut.
 */
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const studio = ohneKommentare(readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8'));

test('19 Flächen — gemessen, nicht abgeschrieben (der Fahrplan sagte 20)', () => {
  assert.equal(FACH_FLAECHEN.length, 19);
  // Gegenrechnung aus den Quelldaten: anklickbare Module minus die vier mit echtem Konfigurator.
  const mitKonfig = anklickbareModule().filter((n) => KONFIGURATOR_NAMEN[n]).length;
  assert.equal(anklickbareModule().length - mitKonfig, 19);
});

test('Deckung in BEIDE Richtungen: kein Modul ohne Fläche, keine Fläche ohne Modul', () => {
  assert.deepEqual(fehlendeFlaechen(), [], 'ein anklickbares Modul ohne Fläche fällt wieder in den Toast');
  assert.deepEqual(verwaisteFlaechen(), [], 'eine Fläche ohne Modul findet nie ein Klick');
});

test('jede id ist eindeutig und schlüsseltauglich (Kante 1: ß, Umlaut, Leerzeichen)', () => {
  const ids = FACH_FLAECHEN.map((f) => f.id);
  assert.equal(new Set(ids).size, ids.length);
  for (const id of ids) assert.match(id, /^[a-z0-9-]+$/, `${id}: id muss ohne Umlaut/Leerzeichen auskommen`);
  assert.equal(fachFlaecheMitId('fach-heizlast')?.label, 'Heizlastberechnung');
});

test('jede Fläche trägt einen gültigen StudioZustand', () => {
  for (const f of FACH_FLAECHEN) {
    assert.ok(ZUSTAENDE.includes(f.zustand), `${f.label}: „${f.zustand}" ist kein StudioZustand`);
  }
});

test('kein Blindtext: jeder Zweck ist konkret, keiner vertröstet', () => {
  for (const f of FACH_FLAECHEN) {
    assert.ok(f.zweck.length > 10, `${f.label}: Zweck fehlt oder ist zu dünn`);
    assert.doesNotMatch(f.zweck, /keine daten/i, `${f.label}: „keine Daten" ist als Leerzustand verboten`);
    assert.doesNotMatch(f.zweck, /folgt\.?$/i, `${f.label}: „folgt" ist genau die Vertröstung, die L4 ablöst`);
  }
});

test('die Fläche ist TIEF, nicht flach: jede hat Ein- und Ausgangsgrößen mit Beschriftung', () => {
  for (const f of FACH_FLAECHEN) {
    assert.ok(f.eingaenge.length >= 3, `${f.label}: mindestens drei Eingänge, sonst ist es eine flache Fläche`);
    assert.ok(f.ausgaenge.length >= 3, `${f.label}: mindestens drei Ausgänge`);
    for (const feld of [...f.eingaenge, ...f.ausgaenge]) {
      assert.ok(feld.label.trim().length > 2, `${f.label}: leere Feldbeschriftung`);
    }
  }
});

test('Kante 3: die vier Module MIT Konfigurator haben KEINE L4-Fläche', () => {
  for (const name of Object.keys(KONFIGURATOR_NAMEN)) {
    assert.equal(fachFlaecheNach(name), undefined, `${name} hat einen echten Konfigurator — keine zweite Wahrheit`);
  }
});

test('Kante 2: der Zurück-Weg richtet sich nach der Herkunft, nie pauschal', () => {
  assert.notEqual(zurueckLabel('start'), zurueckLabel('navi'));
  assert.equal(zurueckLabel('guided'), HERKUNFT_ZURUECK.guided);
  assert.equal(zurueckLabel(undefined), HERKUNFT_ZURUECK.navi, 'Rückfall bleibt vorhersagbar');
});

test('EINE Wahrheit: das Studio liest KONFIGURATOR_NAMEN, statt die vier Namen zu wiederholen', () => {
  assert.match(studio, /KONFIGURATOR_NAMEN\[name\]/);
  assert.doesNotMatch(studio, /name === 'Treppe'/, 'die Namen dürfen nicht ein zweites Mal im Studio stehen');
  assert.doesNotMatch(studio, /Konfigurator folgt/, 'der Vertröstungs-Toast ist Geschichte');
});
