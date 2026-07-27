/**
 * AUF-38 Scheibe 1 — **das Grundgeruest der Stilschicht, ohne eine einzige Umstellung.**
 *
 * Die Scheibe beweist die Mechanik, **bevor** irgendetwas umgebaut wird: CSS entsteht, das Blade
 * zieht sie, die Variablen kommen an. Geht dabei etwas schief, ist nichts umgestellt.
 *
 * **Die Eigenschaft, die dieser Test schuetzt:** `T` bleibt die **einzige** Farbwahrheit. Ein
 * Farbwert in der CSS stuende neben `T` und altert dort still — genau die zweite Wahrheit, die T1
 * beseitigt hat.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { T } from '../app/studioDaten';
import { tokenVariablen, variablenName, setzeTokenVariablen, HP_PRAEFIX } from '../app/stil/tokenVariablen';

const hier = dirname(fileURLToPath(import.meta.url));
const quelle = readFileSync(join(hier, '../hausplaner.css'), 'utf8');
const gebaut = join(hier, '../../../../public/hausplaner/hausplaner.css');

// --- K5: die Variablen stammen aus T ---------------------------------------------------------------
test('K5: jede Variable traegt einen Wert aus `studioDaten.ts` — keine Konstante daneben', () => {
  const paare = tokenVariablen();
  assert.equal(paare.length, Object.keys(T).length, 'genau die Tokens aus T, keiner mehr');
  const werte = new Set<string>(Object.values(T).map(String));
  for (const [name, wert] of paare) {
    assert.ok(name.startsWith(HP_PRAEFIX), `${name} traegt nicht das Praefix`);
    assert.ok(werte.has(wert), `${name}: „${wert}" steht nicht in T`);
  }
});

test('K5: aendert sich T, aendert sich die Variable — sie ist abgeleitet, nicht abgeschrieben', () => {
  const paar = tokenVariablen().find(([n]) => n === '--hp-accent')!;
  assert.equal(paar[1], T.accent, 'derselbe Wert, aus derselben Quelle');
});

test('camelCase wird kebab-case — eine Schreibweise, nicht zwei', () => {
  assert.equal(variablenName('accent'), '--hp-accent');
  assert.equal(variablenName('accentSoft'), '--hp-accent-soft');
  assert.equal(variablenName('canvasGridStrong'), '--hp-canvas-grid-strong');
});

test('setzen ohne DOM tut nichts, statt zu werfen', () => {
  // Der Testlauf hat kein Fenster; ein Wurf waere ein Fehler ueber eine Lage, die keiner ist.
  assert.doesNotThrow(() => { setzeTokenVariablen(null); });
});

test('setzen schreibt genau die Paare — an ein Ziel, das mitschreibt', () => {
  const geschrieben: Array<[string, string]> = [];
  setzeTokenVariablen({ style: { setProperty: (n: string, w: string) => { geschrieben.push([n, w]); } } });
  assert.deepEqual(geschrieben, tokenVariablen());
});

// --- K4: kein roher Farbwert in der CSS -------------------------------------------------------------
test('K4: die CSS-Quelle enthaelt in KEINER Regel einen Farbwert', () => {
  // Kommentare erklaeren, warum keiner drinsteht — sie sind kein Code.
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const treffer = ohneKommentare.match(/#[0-9a-fA-F]{3,8}\b|rgba?\(/g) ?? [];
  assert.deepEqual(treffer, [], `Farbwerte in der CSS: ${treffer.join(', ')}`);
});

test('K4: und auch die GEBAUTE Datei traegt keinen', () => {
  assert.ok(existsSync(gebaut), 'die gebaute CSS fehlt — dann greift das Blade nicht');
  const treffer = readFileSync(gebaut, 'utf8').match(/#[0-9a-fA-F]{3,8}\b|rgba?\(/g) ?? [];
  assert.deepEqual(treffer, [], `Farbwerte in der gebauten CSS: ${treffer.join(', ')}`);
});

// --- Scheibe 1 stellt NICHTS um ----------------------------------------------------------------------
test('die wirkungslose Grundregel aus Scheibe 1 steht unveraendert', () => {
  // **Nachgezogen in Scheibe 2:** die Zusage pruefte, dass die CSS AUSSER dieser Regel nichts
  // enthaelt — das galt fuer Scheibe 1, die nichts umstellte. Scheibe 2 stellt um, also traegt die
  // Datei jetzt Klassen. **Die Absicht bleibt:** die Grundregel ist da und weiterhin wirkungslos.
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  assert.match(ohneKommentare, /:root \{\s*--hp-stilschicht: 1;\s*\}/);
});

// --- AUF-38 Scheibe 2 --------------------------------------------------------------------------------
test('Scheibe 2: jede Farbe in der CSS ist eine Variable, kein Wert', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const farben = ohneKommentare.match(/#[0-9a-fA-F]{3,8}\b|rgba?\(/g) ?? [];
  assert.deepEqual(farben, [], `Farbwerte statt Variablen: ${farben.join(', ')}`);
  // Und die benutzten Variablen gibt es wirklich in `T`.
  const benutzt = [...ohneKommentare.matchAll(/var\((--hp-[a-z0-9-]+)\)/g)].map((m) => m[1]!);
  const bekannt = new Set(tokenVariablen().map(([n]) => n));
  for (const v of new Set(benutzt)) {
    assert.ok(bekannt.has(v), `${v} kommt in T nicht vor`);
  }
  assert.ok(benutzt.length > 0, 'Scheibe 2 benutzt ueberhaupt Variablen?');
});

test('Scheibe 2: kein `!important` und keine Medienabfrage', () => {
  // Braucht es ein `!important`, stimmt die Reihenfolge nicht — dann melden. Responsive ist L7.
  assert.doesNotMatch(quelle, /!important/);
  assert.doesNotMatch(quelle, /@media/);
});

test('Scheibe 2: `StartView` traegt keine statischen Stil-Objekte mehr', () => {
  const start = readFileSync(join(hier, '../app/StartView.tsx'), 'utf8');
  for (const name of ['const wrap:', 'const kicker:', 'const h1:', 'const lead:',
    'const themeHead:', 'const grid3:', 'const cardBase:', 'const icoBox:']) {
    assert.ok(!start.includes(name), `${name} steht noch als Inline-Objekt da`);
  }
  for (const klasse of ['hp-start-wrap', 'hp-start-kicker', 'hp-start-titel', 'hp-start-lead',
    'hp-start-themenkopf', 'hp-start-raster3', 'hp-karte', 'hp-karte-icon']) {
    assert.ok(start.includes(klasse), `${klasse} wird nicht benutzt`);
    assert.ok(quelle.includes(`.${klasse}`), `${klasse} fehlt in der CSS`);
  }
});

// --- AUF-38 Scheibe 3 --------------------------------------------------------------------------------
/**
 * **Der Wortlaut, der vorher inline stand — Eigenschaft fuer Eigenschaft.**
 *
 * Das ist Kriterium 3 in ausfuehrbarer Form: *fuer jede umgestellte Stelle das Paar vorher-Wert →
 * CSS-Regel, keine Stelle ohne Zuordnung.* Steht hier eine Zahl anders als vorher, faellt der Test —
 * den Bildvergleich faehrt der Evaluator headful (Blocker `3cc9a018` aufgeloest).
 */
const SCHEIBE3: ReadonlyArray<[string, string[]]> = [
  ['.hp-fach-raster', ['display: grid', 'grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))', 'gap: 12px']],
  ['.hp-fach-spaltentitel', ['font-size: 11.5px', 'font-weight: 700', 'letter-spacing: .07em',
    'text-transform: uppercase', 'color: var(--hp-faint)', 'margin: 0 0 10px']],
  ['.hp-fach-feld', ['display: block', 'min-width: 0']],
  ['.hp-fach-kopf', ['display: flex', 'align-items: flex-start', 'gap: 12px', 'flex-wrap: wrap', 'padding: 20px 24px 12px']],
  ['.hp-fach-kopf-text', ['flex: 1 1 240px', 'min-width: 0']],
  ['.hp-fach-titelzeile', ['display: flex', 'align-items: center', 'gap: 10px', 'flex-wrap: wrap', 'margin-top: 4px']],
  ['.hp-fach-titel', ['font-size: 21px', 'font-weight: 800', 'letter-spacing: -.01em', 'margin: 0', 'overflow-wrap: anywhere']],
  ['.hp-fach-hinweis', ['flex: 1 1 220px', 'min-width: 0']],
  ['.hp-fach-rumpf', ['padding: 18px 24px 24px', 'display: grid', 'grid-template-columns: repeat(auto-fit, minmax(280px, 1fr))', 'gap: 22px']],
  ['.hp-fach-spalte', ['min-width: 0']],
  ['.hp-fach-liste', ['display: flex', 'flex-direction: column', 'gap: 8px']],
];

test('K3 Scheibe 3: jede Regel traegt genau die Werte, die vorher inline standen', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  for (const [klasse, deklarationen] of SCHEIBE3) {
    const block = ohneKommentare.match(new RegExp(`\\${klasse}\\s*\\{([^}]*)\\}`));
    assert.ok(block, `${klasse} fehlt in der CSS`);
    for (const d of deklarationen) {
      assert.ok(block[1]!.includes(d), `${klasse}: „${d}" fehlt`);
    }
  }
});

test('K3 Scheibe 3: jede Klasse wird auch benutzt — keine Regel ins Leere', () => {
  const fach = readFileSync(join(hier, '../app/FachFlaeche.tsx'), 'utf8');
  for (const [klasse] of SCHEIBE3) {
    assert.ok(fach.includes(klasse.slice(1)), `${klasse} steht in der CSS, aber niemand benutzt sie`);
  }
});

// --- AUF-38 Scheibe 4 --------------------------------------------------------------------------------
const SCHEIBE4: ReadonlyArray<[string, string[]]> = [
  ['.hp-studio-kopf', ['min-height: 62px', 'flex: 0 0 auto', 'display: flex', 'align-items: center',
    'flex-wrap: wrap', 'gap: 12px', 'padding: 8px 16px']],
  ['.hp-studio-marke', ['display: flex', 'align-items: center', 'gap: 11px', 'font-weight: 700', 'font-size: 16px', 'min-width: 0']],
  ['.hp-fueller', ['flex: 1']],
  ['.hp-studio-reihe', ['flex: 1', 'min-height: 0', 'display: flex']],
  ['.hp-navi-kopf', ['display: flex', 'align-items: center', 'gap: 10px', 'padding: 16px 16px 8px']],
  ['.hp-navi-liste', ['flex: 1', 'overflow: auto', 'padding: 4px 10px 12px']],
  ['.hp-experte', ['position: absolute', 'inset: 0', 'display: flex', 'flex-direction: column']],
  ['.hp-experte-buehne', ['flex: 1', 'min-height: 0']],
];

test('K3 Scheibe 4: jede Regel traegt genau die Werte, die vorher inline standen', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  for (const [klasse, deklarationen] of SCHEIBE4) {
    const block = ohneKommentare.match(new RegExp(`\\${klasse}\\s*\\{([^}]*)\\}`));
    assert.ok(block, `${klasse} fehlt in der CSS`);
    for (const d of deklarationen) {
      assert.ok(block[1]!.includes(d), `${klasse}: „${d}" fehlt`);
    }
  }
});

test('K3 Scheibe 4: jede Klasse wird auch benutzt', () => {
  const studio = readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8');
  for (const [klasse] of SCHEIBE4) {
    assert.ok(studio.includes(klasse.slice(1)), `${klasse} steht in der CSS, aber niemand benutzt sie`);
  }
});

test('Scheibe 4: das Namenskuerzel bleibt inline — seine Farben haben keinen Token', () => {
  // **Der ehrliche Rest.** Rohe Farbwerte in einer CSS-Regel verbietet Kriterium 4; einen Token zu
  // erfinden waere eine Palette-Entscheidung. Also bleibt die Stelle, wo sie ist — mit Begruendung.
  const studio = readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8');
  assert.match(studio, /background: '#dfe4ea'/, 'die Stelle ist noch inline');
  const werte = new Set<string>(Object.values(T).map(String));
  for (const farbe of ['#dfe4ea', '#5b636d']) {
    assert.ok(!werte.has(farbe), `${farbe} hat inzwischen einen Token — dann gehoert die Stelle in die CSS`);
  }
});

test('Scheibe 3: die zwei konstanten Stil-Objekte sind fort', () => {
  const fach = readFileSync(join(hier, '../app/FachFlaeche.tsx'), 'utf8');
  assert.ok(!fach.includes('const raster:'), '`raster` steht noch als Inline-Objekt da');
  assert.ok(!fach.includes('const spaltenTitel:'), '`spaltenTitel` steht noch als Inline-Objekt da');
});

test('Scheibe 2: was aus Zeiger oder Zustand kommt, blieb INLINE', () => {
  // Ziel ist null STATISCHE Inline-Stile, nicht null Inline-Stile. Eine gerechnete Breite in eine
  // Klasse zu pressen baut einen Fehler.
  const start = readFileSync(join(hier, '../app/StartView.tsx'), 'utf8');
  assert.match(start, /boxShadow: hover \? T\.schattenGehoben : T\.schattenFlach/, 'der Schwebezustand bleibt inline');
  assert.match(start, /width: dominant \? 46 : 38/, 'der Zustand der Kachel bleibt inline');
  // Und keine dieser Bedingungen ist in die CSS gewandert — **kommentarfrei gemessen**: der
  // erklaerende Kopf der CSS nennt `hover` und `dominant`, um zu sagen, dass sie dort NICHT stehen.
  // Ein Verbot, das seine eigene Begruendung trifft, prueft den Text und nicht den Code.
  assert.doesNotMatch(quelle.replace(/\/\*[\s\S]*?\*\//g, ''), /hover|dominant/);
});

test('die Stilschicht wird genau einmal importiert — in `main.tsx`', () => {
  const einstieg = readFileSync(join(hier, '../main.tsx'), 'utf8');
  assert.equal((einstieg.match(/import '\.\/hausplaner\.css';/g) ?? []).length, 1);
  assert.match(einstieg, /setzeTokenVariablen\(\);/, 'und die Variablen werden beim Start gesetzt');
});

test('das Blade bindet die CSS bewacht ein — es brauchte keine Aenderung', () => {
  const blade = readFileSync(join(hier, '../../../views/admin/hausplaner/objekt.blade.php'), 'utf8');
  assert.match(blade, /@if \(file_exists\(public_path\('hausplaner\/hausplaner\.css'\)\)\)/);
});
