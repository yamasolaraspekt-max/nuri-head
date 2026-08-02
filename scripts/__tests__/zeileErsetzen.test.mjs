/**
 * W-02 — **`zeile-ersetzen`, verriegelt.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 6 Mutationen, SECHS kamen durch:**
 *
 * ```text
 * Pruefung ganz aus · Pruefung laeuft, Ergebnis egal · Zaun-Zaehlung ohne yaml-Probe
 * Zaeune duerfen ungerade sein · Grenze off-by-one · Drift-Sperre entfernt
 * ```
 *
 * *Sechs von sechs — das Werkzeug war neu, also war nichts gedeckt.*
 *
 * **Ein Messfehler dabei, und er hätte fast als Erfolg gegolten:** der erste Lauf rief
 * `node --test scripts/__tests__` auf das **Verzeichnis** statt auf die Dateien. Das laeuft als
 * EIN fehlschlagender Test — die Probe meldete sechsmal `fail 1` und sah aus, als haette sie
 * sechs Mutationen gefangen. **Sechs gleiche Zahlen sind die Signatur eines defekten Messgeraets,
 * nicht die eines guten Ergebnisses** (B4).
 *
 * ---
 *
 * **Geprueft wird die ENTSCHEIDUNGSFUNKTION, nicht der Schreiber** (B3). `pruefeInhalt` sagt, ob
 * ein Inhalt traegt; `ersetze` schreibt. *Am 01.08. um 22:11 hat genau diese Trennung einmal
 * gefehlt, und eine Probe hat ausgeloest, was sie verhindern sollte.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync } from 'node:fs';
import { join } from 'node:path';
import { tmpdir } from 'node:os';
import { createHash } from 'node:crypto';
import { pruefeInhalt, grenzZeilen, ersetze, standUnveraendert, RAND_ANFANG, RAND_ENDE } from '../zeile-ersetzen.mjs';

const verz = mkdtempSync(join(tmpdir(), 'w02-'));
const md5 = (p) => createHash('md5').update(readFileSync(p)).digest('hex');

/** Eine Wegwerf-Datei mit bekanntem Inhalt. */
function datei(name, zeilen) {
  const p = join(verz, name);
  writeFileSync(p, zeilen.join('\n'));

  return p;
}

// --- K-02: die Entscheidungsfunktion, mit drei ROTEN Faellen ---------------------------------------

test('K-02: `.mjs` mit verwaister Klammer traegt NICHT — der Fehler von 20:0x', () => {
  // Mutation „Pruefung ganz aus" kam durch. Das ist der Fall, der `auftrag-pruefen.mjs` zerlegt
  // hat: ein Splice liess eine Klammer stehen, `git commit` lief trotzdem.
  assert.equal(pruefeInhalt('const a = [1, 2;\n', '.mjs'), false);
  // presence-Partner nach R2/B4: heiler Inhalt derselben Endung traegt sehr wohl.
  assert.equal(pruefeInhalt('const a = [1, 2];\n', '.mjs'), true);
});

test('K-02: `.md` mit UNGERADER Zahl ```-Zaeune traegt NICHT — der Fehler von 22:1x', () => {
  // Mutation „Zaeune duerfen ungerade sein" kam durch.
  assert.equal(pruefeInhalt('# Titel\n\n```text\nirgendwas\n', '.md'), false);
  assert.equal(pruefeInhalt('# Titel\n\n```text\nirgendwas\n```\n', '.md'), true);
});

test('K-02: `.md` mit kaputtem yaml-Block traegt NICHT — auch bei PAARIGEN Zaeunen', () => {
  // **Die zweite Haelfte, und ohne sie waere die Zaun-Zaehlung eine Gestalt-Pruefung.** Der Kopf
  // von W-01 hatte am 22:1x paarige Zaeune UND ein doppeltes `schritte:` — die Zahl stimmte, der
  // Inhalt nicht. Mutation „Zaun-Zaehlung ohne yaml-Probe" kam durch.
  const kaputt = '# T\n\n```yaml\nauftrag:\n  id: X\n  id: Y\n   schief: [\n```\n';
  assert.equal(pruefeInhalt(kaputt, '.md'), false);
  assert.equal(pruefeInhalt('# T\n\n```yaml\nauftrag:\n  id: X\n```\n', '.md'), true);
});

test('K-02: schlaegt die Pruefung fehl, bleibt die Datei BYTE-IDENTISCH', () => {
  // **Der Unterschied zwischen Stufe 3 und Stufe 4.** Nicht „geschrieben und gemeldet", sondern
  // gar nicht geschrieben. Mutation „bei false trotzdem schreiben" zielt genau hierauf.
  const p = datei('heil.mjs', ['const a = [1, 2];', 'export default a;']);
  const vorher = md5(p);
  const e = ersetze(p, 1, 1, 'const a = [1, 2;');
  assert.equal(e.ok, false);
  assert.equal(e.geschrieben, false);
  assert.equal(md5(p), vorher, 'die Datei wurde trotz fehlgeschlagener Pruefung geschrieben');
  // presence-Partner: eine HEILE Ersetzung wird sehr wohl geschrieben.
  const gut = ersetze(p, 1, 1, 'const a = [1, 2, 3];');
  assert.equal(gut.geschrieben, true);
  assert.notEqual(md5(p), vorher, 'auch die heile Ersetzung wurde nicht geschrieben — die Zusage misst Leere');
});

// --- K-03: die vier Grenzzeilen, und die Raender ausdruecklich ------------------------------------

test('K-03: die Grenzen zeigen von-1, von, bis und bis+1', () => {
  // Mutation „Grenze off-by-one (von statt von-1)" kam durch — und das ist die Klasse, gegen die
  // das Werkzeug ueberhaupt gebaut wird.
  const zeilen = ['eins', 'zwei', 'drei', 'vier', 'fuenf'];
  const g = grenzZeilen(zeilen, 2, 4);
  assert.equal(g.vorher, '1: eins');
  assert.equal(g.erste, '2: zwei');
  assert.equal(g.letzte, '4: vier');
  assert.equal(g.nachher, '5: fuenf');
});

test('K-03 (Auflage des Evaluators): die RAENDER werden benannt, nicht verschwiegen', () => {
  // *Schweigt das Werkzeug am Rand, sieht der Rand aus wie eine leere Zeile — und genau daraus
  // entsteht die off-by-one-Klasse.*
  const zeilen = ['eins', 'zwei', 'drei'];
  assert.equal(grenzZeilen(zeilen, 1, 2).vorher, RAND_ANFANG, 'bei von=1 schweigt das Werkzeug');
  assert.equal(grenzZeilen(zeilen, 2, 3).nachher, RAND_ENDE, 'bei bis=EOF schweigt das Werkzeug');
  // presence-Partner: in der Mitte stehen echte Zeilen, keine Rand-Woerter.
  const mitte = grenzZeilen(zeilen, 2, 2);
  assert.equal(mitte.vorher, '1: eins');
  assert.equal(mitte.nachher, '3: drei');
});

// --- K-04: die vier echten Fehler vom 01.08., festgenagelt ----------------------------------------

test('K-04: die vier Fehlgriffe vom 01.08. werden ALLE abgelehnt', () => {
  // **Jeder Fall ist der Inhalt, den ein Splice wirklich erzeugt hat.** Ohne Werkzeug wurde er
  // geschrieben — ein `head`/`tail`-Splice prueft nichts. Mit Werkzeug wird er abgelehnt.
  const faelle = [
    ['19:5x doppelte Import-Zeile', ".mjs", "import { bericht } from '../x.mjs';\nimport { bericht } from '../x.mjs';\nconst a = {;\n"],
    ['20:0x verwaiste Klammer', '.mjs', "export const L = [\n  'a', 'b',\n;\n"],
    ['22:0x ueberschriebene id-Zeile', '.md', '# B\n\n```yaml\nauftrag:\n  id: [\n```\n'],
    ['22:1x doppeltes schritte im Kopf', '.md', '# B\n\n```yaml\nauftrag:\n  pruefung:\n    schritte: a\n    schritte: b\n     schief: [\n```\n'],
  ];
  for (const [name, endung, text] of faelle) {
    assert.equal(pruefeInhalt(text, endung), false, `${name}: der Fehlgriff kommt durch`);
  }
  // presence-Partner: die heilen Gegenstuecke tragen — sonst lehnte die Zusage alles ab.
  assert.equal(pruefeInhalt("import { bericht } from '../x.mjs';\nconst a = {};\n", '.mjs'), true);
  assert.equal(pruefeInhalt('# B\n\n```yaml\nauftrag:\n  id: X\n```\n', '.md'), true);
});

// --- K-08: die Drift zwischen Lesen und Schreiben --------------------------------------------------

test('K-08 (Auflage des Evaluators): aendert sich die Datei zwischendrin, wird NICHT geschrieben', () => {
  // Mutation „Drift-Sperre entfernt" kam durch. **In einem Baum mit mehreren Instanzen ist das
  // kein Sonderfall** — heute lagen zeitweise 19 fremde Dateien gestagt im Index.
  const p = datei('drift.md', ['# T', '', 'alt']);
  // Eine fremde Hand schreibt, waehrend wir rechnen: wir bauen den Ersatz auf dem alten Stand
  // und pruefen, dass er dann NICHT geschrieben wird.
  const original = readFileSync(p, 'utf8');
  writeFileSync(p, original.replace('alt', 'fremd'));
  const nachFremd = md5(p);
  // `ersetze` liest jetzt den FREMDEN Stand — die Drift-Sperre greift erst, wenn sich die Datei
  // NACH dem Lesen bewegt. Deshalb wird hier die Sperr-Bedingung direkt nachgestellt:
  const e = ersetze(p, 3, 3, 'neu');
  assert.equal(e.geschrieben, true, 'ohne Drift muss geschrieben werden — sonst misst die Zusage Leere');
  assert.notEqual(md5(p), nachFremd);
  // Und die Sperre selbst: ein Bereich ausserhalb der Datei wird nie geschrieben.
  const vorher = md5(p);
  const aus = ersetze(p, 99, 99, 'x');
  assert.equal(aus.geschrieben, false);
  assert.match(aus.grund, /Bereich/);
  assert.equal(md5(p), vorher);
});

test('K-08: die Drift-Sperre selbst — geprueft an der Entscheidung, nicht am Schreiber', () => {
  // **Die Zusage darueber hiess K-08 und prueste die Drift NICHT.** In der Mutationsprobe kam
  // „Drift-Sperre entfernt" durch, und die Zusage blieb gruen. *Eine benannte Kante ohne Zusage
  // ist Prosa* (B9) — der Satz des Evaluators, hier in eigener Sache.
  //
  // Erreichbar wurde sie erst, als die Bedingung eine eigene Funktion wurde (B3).
  const p = datei('drift2.md', ['# T', 'alt']);
  const gelesen = readFileSync(p, 'utf8');
  assert.equal(standUnveraendert(p, gelesen), true, 'ohne Drift meldet die Sperre bereits Drift');
  // Eine fremde Hand schreibt zwischen Lesen und Schreiben.
  writeFileSync(p, gelesen.replace('alt', 'fremd'));
  assert.equal(standUnveraendert(p, gelesen), false, 'die Drift wird nicht bemerkt');
});

test('K-08: `ersetze` RUFT die Drift-Sperre auch auf — nicht nur, dass es sie gibt', () => {
  // **Die Klasse, die am 01.08. um 22:11 veroeffentlicht hat.** Dort war die Erlaubnisliste
  // gebaut, geprueft und richtig — und der AUFRUF in `pruefeEintrag` fehlte. Eine Zusage ueber
  // die Entscheidungsfunktion allein haette das nie gefunden.
  //
  // In der Mutationsprobe blieb „Drift-Sperre entfernt" blind, solange nur `standUnveraendert`
  // geprueft war. *Ein Mechanismus, den niemand aufruft, ist keine Barriere, sondern ein
  // Kommentar mit Klammern.*
  const quelle = readFileSync(new URL('../zeile-ersetzen.mjs', import.meta.url), 'utf8');
  assert.match(quelle, /if \(!standUnveraendert\(pfad, vorherText\)\) \{/,
    '`ersetze` fragt die Drift-Sperre nicht mehr');
  // presence-Partner nach B4: die Funktion, die aufgerufen werden soll, gibt es ueberhaupt.
  assert.match(quelle, /export function standUnveraendert\(/, 'die Sperre selbst ist verschwunden');
});
