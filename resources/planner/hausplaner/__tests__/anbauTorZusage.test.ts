/**
 * A-24 — **die Zusage des Panels und die Bedingung des Tors sind DIESELBE.**
 *
 * **Der Befund:** Das Panel sagte für L/T *„braucht Außenmaß Länge und Breite > 0"* und berechnete
 * seine Warnung genauso — die zwei Anbaumaße prüfte es **nur bei U**. Das Tor
 * `anbauZuEingabe` (`renderers/three-d/dachMesh.ts`) verlangt aber für **jede** Verschneidungsform
 * alle vier. **Der Nutzer erfüllte, was dastand, die Warnung verschwand, und die Fläche blieb
 * leer.** Das ist keine fehlende Funktion, sondern eine Falschauskunft.
 *
 * **Warum dieser Test die KOPPLUNG prüft und nicht den Wortlaut.** Ein Test auf den Satz wäre beim
 * nächsten Umbau des Tors **nicht rot geworden** — er hätte weiter einen Text bewacht, während die
 * Bedingung dahinter wegwandert. Geprüft wird deshalb: *dieselben vier Felder, an beiden Orten,
 * ohne Formabhängigkeit.*
 *
 * **Was hier NICHT geprüft wird:** ob das Dach am Ende erscheint. Das ist eine Sichtprobe und
 * gehört in die Browserabnahme — dieser Test misst Quelltext, kein Bild.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');

const panel = ohneKommentare(readFileSync(join(hier, '../app/rahmen/EigenschaftenPanel.tsx'), 'utf8'));
const tor = ohneKommentare(readFileSync(join(hier, '../renderers/three-d/dachMesh.ts'), 'utf8'));

/** Die eine Zeile, die im Panel die Sichtbarkeit der Warnung entscheidet. */
function fehltZeile(): string {
  const m = panel.match(/const fehlt = [^;]+;/);
  assert.ok(m, 'die `fehlt`-Zeile wurde nicht gefunden — der Test prüft ins Leere');
  return m[0];
}

/**
 * Die eine Bedingung, die im Tor über `null` entscheidet.
 *
 * **Bis zum ersten `return null;` gelesen, nicht bis zur ersten schließenden Klammer.** Mein erster
 * Versuch nahm `if \(([^)]*)\)` — und `[^)]*` bricht an `(a.lengthB && a.lengthB > 0)` ab, also
 * mitten in der Bedingung. Der Test wurde rot und zeigte auf den Bau, während der Fehler im
 * Messmuster stand.
 */
function torBedingung(): string {
  const m = tor.match(/function anbauZuEingabe[\s\S]*?if \(([\s\S]*?)\)\s*\{\s*return null;/);
  assert.ok(m, 'die Torbedingung wurde nicht gefunden — der Test prüft ins Leere');
  return m[1];
}

// --- A-24-1(b): die Bedingung, nicht der Text -----------------------------------------------------
test('A-24: die `fehlt`-Bedingung des Panels prüft ALLE VIER Maße', () => {
  const z = fehltZeile();
  for (const feld of ['length', 'width', 'lengthB', 'widthB']) {
    assert.match(z, new RegExp(`a\\.${feld}\\b`), `\`${feld}\` fehlt in der Warn-Bedingung`);
  }
});

test('A-24: die Prüfung der zwei Anbaumaße hängt NICHT mehr an der Form', () => {
  // Der Kern des Befundes: `istU &&` vor den lengthB/widthB-Prüfungen machte die Warnung für L/T
  // unsichtbar, sobald zwei Maße gefüllt waren. Genau diese Kopplung darf nicht zurückkommen.
  assert.doesNotMatch(fehltZeile(), /istU/,
    'die Warn-Bedingung darf nicht von der Dachform abhängen — das Tor tut es auch nicht');
});

// --- Die KOPPLUNG: beide Orte nennen dieselben vier Felder ----------------------------------------
test('A-24: Panel-Bedingung und Tor-Bedingung nennen DIESELBEN vier Felder', () => {
  const felder = (s: string): string[] =>
    ['length', 'width', 'lengthB', 'widthB'].filter((f) => new RegExp(`\\.${f}\\b`).test(s));
  // Sortiert verglichen, damit die Zusage nicht an der Reihenfolge im Quelltext hängt.
  assert.deepEqual([...felder(fehltZeile())].sort(), [...felder(torBedingung())].sort(),
    'Panel und Tor müssen dieselben Maße verlangen — sonst lügt die Anzeige wieder');
});

test('A-24: das Tor selbst ist unberührt — es verlangt weiterhin alle vier', () => {
  // A-24-4: die Sackgasse wird im PANEL behoben, nicht durch Lockerung der Engine.
  const b = torBedingung();
  for (const feld of ['length', 'width', 'lengthB', 'widthB']) {
    assert.match(b, new RegExp(`a\\.${feld}\\b`), `das Tor prüft \`${feld}\` nicht mehr`);
  }
  assert.match(tor, /return null;/, 'das Tor gibt bei fehlenden Maßen weiterhin `null` zurück');
});

// --- A-24-1(a): der Text sagt dasselbe wie die Bedingung ------------------------------------------
test('A-24: der L/T-Hinweis nennt alle vier Maße und verspricht nicht zwei', () => {
  const m = panel.match(/'L\/T-Dach braucht ([^']*)'/);
  assert.ok(m, 'der L/T-Hinweis wurde nicht gefunden');
  const satz = m[1];
  assert.match(satz, /alle vier Maße/, 'der Text muss die tatsächliche Torbedingung nennen');
  assert.match(satz, /Anbau/, 'und die zwei zusätzlichen Maße benennen');
  assert.doesNotMatch(satz, /^Außenmaß Länge und Breite > 0 —/,
    'die alte Zusage über zwei Maße darf nicht zurückkommen');
});

// --- A-24-2: die zwei Felder sind für L/T erreichbar ----------------------------------------------
test('A-24: die Anbau-Felder stehen NICHT mehr hinter `istU`', () => {
  // Vorher: `{istU && (<>…lengthB…widthB…</>)}`. Die Felder existierten, waren aber für L/T
  // unerreichbar — der Nutzer konnte die Bedingung gar nicht erfüllen.
  const eingaben = panel.match(/setzeAnbau\('(length|width|lengthB|widthB)'/g) ?? [];
  assert.equal(eingaben.length, 4, 'es müssen vier Eingabefelder schreiben, eines je Maß');
  assert.doesNotMatch(panel, /\{istU && \(/,
    'kein Feldblock darf mehr hinter einer Formbedingung liegen');
});

test('A-24-2: die Bezeichnung „Anbau" gilt für L/T, „Innenhof/Kerbe" bleibt bei U', () => {
  assert.match(panel, /istU \? 'Innenhof\/Kerbe Länge \(mm\)' : 'Anbau Länge \(mm\)'/);
  assert.match(panel, /istU \? 'Innenhof\/Kerbe Breite \(mm\)' : 'Anbau Breite \(mm\)'/);
});

// --- A-24-3: die Schutzgrenze, als EIGENSCHAFT statt als Zustandsvergleich ------------------------
test('A-24-3: `setzeAnbau` wird ausschließlich aus `onChange` gerufen', () => {
  // Ein Aufruf aus einem Rumpf oder Effekt würde beim bloßen ÖFFNEN des Panels schreiben und damit
  // Bestandsdaten verändern. Gemessen wird die Eigenschaft, nicht ein gespeicherter Zustand:
  // ein md5-Vergleich an einem Dokument wäre hier kein Beleg, weil die Testdatenbank zurückgesetzt
  // wird (70 von 137 Testdateien nutzen RefreshDatabase).
  // Die Definition heißt `const setzeAnbau = (…` und wird von `setzeAnbau\(` NICHT erfasst —
  // dazwischen steht ein `= `. Mein erster Versuch zog trotzdem eine 1 ab und verglich 3 mit 4.
  // Der Test war rot, der Bau richtig: eine Zählung, die etwas abzieht, das sie nie gezählt hat.
  const alle = panel.match(/setzeAnbau\(/g) ?? [];
  const ausOnChange = panel.match(/onChange=\{\(e\) => setzeAnbau\(/g) ?? [];
  assert.equal(alle.length, 4, 'vier Aufrufe erwartet — einer je Maß');
  assert.equal(alle.length, ausOnChange.length,
    'JEDER Aufruf von `setzeAnbau` muss aus einem `onChange` kommen — keiner aus einem Rumpf oder Effekt');
});

test('A-24-3: das Panel schreibt aus KEINEM Effekt ins Gebäudemodell', () => {
  assert.doesNotMatch(panel, /useEffect/, 'ein Effekt könnte beim Öffnen Bestandsdaten verändern');
});
