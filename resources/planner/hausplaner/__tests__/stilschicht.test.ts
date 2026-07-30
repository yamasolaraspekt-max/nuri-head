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
import { messeDatei, stilBloecke, istStatisch, istAusnahme, rohfarben } from '../../../../scripts/statische-inline-stile.mjs';
// AUF-48: die Hauptansicht ist zerlegt — diese Zusage liest ALLE ihre Teile.
import { zerlegteApp } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const quelle = readFileSync(join(hier, '../hausplaner.css'), 'utf8');
const gebaut = join(hier, '../../../../public/hausplaner/hausplaner.css');

/**
 * **Traegt die Quelle diese Klasse WIRKLICH?**
 *
 * Ein einfaches `includes` reicht nicht: `hp-ef-wert` steckt auch in `hp-ef-wertzeile`. Eine
 * Zusage, die so prueft, bleibt gruen, obwohl die Klasse keinen Traeger hat.
 *
 * **Selbst gefunden, und zwar an der Gegenprobe:** beim Zurueckdrehen einer Stelle wurde nur
 * *ein* Test rot statt zweier. Ein fehlender roter Test ist ein Befund — die Gegenprobe prueft
 * nicht nur den Bau, sie prueft auch die Zusage.
 *
 * Geprueft wird deshalb auf Wortgrenze: die Klasse steht in Anfuehrungszeichen oder zwischen
 * Leerzeichen, wie in `className="a b"`.
 */
function traegt(quelltext: string, klasse: string): boolean {
  return new RegExp(`["' ]${klasse}["' ]`).test(quelltext);
}

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

const NACHZUG2: ReadonlyArray<[string, string[]]> = [
  ['.hp-start-nichtklick', ['cursor: default']],
  ['.hp-start-kopfzeile', ['display: flex', 'align-items: center', 'gap: 8px', 'flex-wrap: wrap']],
  ['.hp-start-name', ['font-size: 15.5px', 'font-weight: 700']],
  ['.hp-start-zeile', ['font-size: 13px', 'color: var(--hp-muted)', 'margin-top: 4px', 'line-height: 1.45']],
  ['.hp-start-fussnote', ['font-size: 12px', 'color: var(--hp-faint)', 'margin-top: 6px', 'line-height: 1.4']],
  ['.hp-start-eng', ['min-width: 0']],
  ['.hp-start-rubrik', ['font-size: 11.5px', 'font-weight: 700', 'letter-spacing: .1em', 'text-transform: uppercase', 'color: var(--hp-accent)']],
  ['.hp-start-dehnt', ['flex: 1 1 auto']],
  ['.hp-start-marke', ['display: inline-block', 'font-size: 11px', 'font-weight: 700', 'letter-spacing: .05em', 'text-transform: uppercase', 'color: var(--hp-accent)', 'margin-bottom: 2px']],
  ['.hp-start-bild', ['width: 40px', 'height: 40px', 'border-radius: 11px', 'margin-bottom: 12px']],
  ['.hp-start-kartentitel', ['font-size: 14px', 'font-weight: 700']],
  ['.hp-start-chips', ['display: flex', 'flex-wrap: wrap', 'gap: 8px', 'margin-top: 16px']],
  ['.hp-start-chip', ['font-size: 12.5px', 'font-weight: 600', 'color: var(--hp-accent-ink)', 'background: var(--hp-accent-soft)', 'border-radius: 999px', 'padding: 6px 13px', 'cursor: pointer']],
  ['.hp-start-hinweiskasten', ['margin-top: 24px', 'background: var(--hp-surface)', 'border-radius: 14px', 'padding: 14px 16px', 'box-shadow: var(--hp-schatten-flach)', 'max-width: 520px']],
  ['.hp-start-hinweistitel', ['font-size: 13.5px', 'font-weight: 700']],
  ['.hp-start-hinweistext', ['font-size: 12.5px', 'color: var(--hp-muted)', 'margin-top: 4px']],
  ['.hp-start-abstand', ['margin-top: 24px']],
  ['.hp-start-reihe', ['display: flex', 'gap: 12px', 'margin-top: 12px', 'flex-wrap: wrap']],
  ['.hp-start-abschnitt', ['margin-top: 40px']],
  ['.hp-start-abschnitttitel', ['font-size: 16px', 'font-weight: 700']],
  ['.hp-start-abschnittzusatz', ['font-size: 13px', 'color: var(--hp-faint)']],
  ['.hp-start-schild', ['margin-top: 34px', 'display: inline-flex', 'align-items: center', 'gap: 8px', 'background: var(--hp-accent-soft)', 'color: var(--hp-accent-ink)', 'border-radius: 999px', 'padding: 6px 14px', 'font-size: 12.5px', 'font-weight: 600']],
];

test('K3 Nachzug Scheibe 2: jede Regel traegt genau die Werte, die vorher inline standen', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  for (const [klasse, deklarationen] of NACHZUG2) {
    const block = ohneKommentare.match(new RegExp(`\\${klasse} \\{([^}]*)\\}`));
    assert.ok(block, `${klasse} fehlt in der CSS`);
    for (const d of deklarationen) {
      assert.ok(block[1]!.includes(d), `${klasse}: „${d}" fehlt`);
    }
  }
});

test('Nachzug Scheibe 2: jede Klasse wird auch benutzt', () => {
  const sv = readFileSync(join(hier, '../app/StartView.tsx'), 'utf8');
  for (const [klasse] of NACHZUG2) {
    assert.ok(sv.includes(klasse.slice(1)), `${klasse} steht in der CSS, aber niemand benutzt sie`);
  }
});

test('Nachzug Scheibe 2 (Wirkung): jeder verbliebene Inline-Stil in StartView hat einen Grund', () => {
  const sv = readFileSync(join(hier, '../app/StartView.tsx'), 'utf8');
  const bloecke = [...sv.matchAll(/style=\{\{([\s\S]*?)\}\}/g)].map((m) => m[1]!);
  assert.ok(bloecke.length > 0, 'gar kein Inline-Stil mehr? Dann ist die Zusage stumpf geworden');
  const dynamisch = /\?|hover|dominant|grund|\bp\.|\bk\.|\bt\./;
  const rohwert = /#[0-9a-fA-F]{3,8}\b|rgba?\(/;
  const ohneGrund = bloecke.filter((b) => !dynamisch.test(b) && !rohwert.test(b));
  assert.deepEqual(ohneGrund, [],
    `Inline-Stile ohne Grund (nur Token und Literale — gehoeren in die Stilschicht):\n${ohneGrund.join('\n---\n')}`);
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
  ['.hp-fueller', ['flex: 1']],
  ['.hp-studio-reihe', ['flex: 1', 'min-height: 0', 'display: flex']],
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

const SCHEIBE4B: ReadonlyArray<[string, string[]]> = [
  ['.hp-studio', ['font-family: Inter, system-ui, sans-serif', 'color: var(--hp-ink)', 'min-height: 100vh',
    'display: flex', 'flex-direction: column', 'background: var(--hp-bg)']],
  ['.hp-status', ['display: flex', 'align-items: center', 'gap: 7px', 'color: var(--hp-muted)', 'font-size: 13px']],
  ['.hp-modusschalter', ['display: flex', 'background: var(--hp-surface)', 'border-radius: 12px', 'padding: 4px',
    'box-shadow: var(--hp-schatten-flach)']],
];

test('K3 Scheibe 4 (Rest): jede Regel traegt genau die Werte, die vorher inline standen', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  for (const [klasse, deklarationen] of SCHEIBE4B) {
    const block = ohneKommentare.match(new RegExp(`\\${klasse} \\{([^}]*)\\}`));
    assert.ok(block, `${klasse} fehlt in der CSS`);
    for (const d of deklarationen) {
      assert.ok(block[1]!.includes(d), `${klasse}: „${d}" fehlt`);
    }
  }
});

/**
 * **Die Wirkung, wie bei Scheibe 3 — und diesmal von vornherein.**
 *
 * Der erste Anlauf an dieser Datei stellte acht Stellen um und liess **siebzehn** stehen; die
 * Zusagen daneben waren gruen, weil sie die acht pruefen. *Dieselbe Luecke, die das Votum zu
 * Scheibe 3 aufgedeckt hat.* Diese Zusage prueft, was uebrig ist: **jeder verbliebene Inline-Stil
 * muss dynamisch sein oder einen Rohwert ohne Token tragen.** Alles andere gehoert in die Schicht.
 */
test('Scheibe 4 (Wirkung): jeder verbliebene Inline-Stil in HausplanerStudio hat einen Grund', () => {
  const studio = readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8');
  const bloecke = [...studio.matchAll(/style=\{\{([\s\S]*?)\}\}/g)].map((m) => m[1]!);
  assert.ok(bloecke.length > 0, 'gar kein Inline-Stil mehr? Dann ist die Zusage stumpf geworden');
  const dynamisch = /\?|navZu|offeneHubs|imExperte|navBreit|\bst\.|\bp\.|\bf\./;
  const rohwert = /#[0-9a-fA-F]{3,8}\b|rgba?\(/;
  const ohneGrund = bloecke.filter((b) => !dynamisch.test(b) && !rohwert.test(b));
  assert.deepEqual(ohneGrund, [],
    `Inline-Stile ohne Grund (nur Token und Literale — gehoeren in die Stilschicht):\n${ohneGrund.join('\n---\n')}`);
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

/**
 * **Befund AUF38-S4-1 des Evaluators, behoben.**
 *
 * Von den zwei Rohwert-Ausnahmen in `HausplanerStudio` war nur das Namenskuerzel beidseitig
 * verriegelt. Der **Toast** traegt `#1a262a` — gleichartige Ausnahme, **ohne jede Verriegelung**.
 * Der Wirkungs-Test fing den Fall *„`1a262a` bekommt einen Token und bleibt trotzdem inline"* nicht:
 * er verlangt nur *irgendeinen* Grund, und ein Rohwert bleibt ein Rohwert, auch wenn er inzwischen
 * einen Namen hat.
 *
 * **Beide Richtungen, wie beim Namenskuerzel:** die Stelle ist noch inline **und** die Farbe steht
 * weiterhin nicht in `T`. Bekommt sie einen Token, faellt der Test — und die Stelle gehoert in die
 * Schicht.
 */
test('Scheibe 4: der Toast bleibt inline — seine Farbe hat keinen Token', () => {
  const studio = readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8');
  assert.match(studio, /background: '#1a262a'/, 'die Stelle ist noch inline');
  const werte = new Set<string>(Object.values(T).map(String));
  assert.ok(!werte.has('#1a262a'),
    '#1a262a hat inzwischen einen Token — dann gehoert der Toast in die Stilschicht');
});

/**
 * **Und die Regel dahinter, damit die naechste Ausnahme nicht wieder einzeln vergessen wird.**
 *
 * Bisher stand je Ausnahme eine eigene Zusage — wer eine neue anlegt, muss daran denken, auch die
 * Zusage zu schreiben. Genau das ist beim Toast unterblieben. Diese Zusage prueft die **Menge**:
 * jede Rohfarbe, die in `HausplanerStudio` inline steht, muss in dieser Liste stehen, und keine
 * davon darf einen Token haben.
 */
/**
 * **Befund beim Anlegen dieser Zusage:** es waren nicht drei Rohfarben, sondern **vier**. `#3f464e`
 * stand in den zwei Navi-Eintraegen und war ebenfalls unverriegelt — dieselbe Klasse wie der Toast,
 * nur hatte sie niemand gezaehlt.
 *
 * **Nachgezogen in AUF-83-T2, und die Zusage hat sich dabei selbst bewaehrt:** mit der zweiten
 * Navigation sind ihre beiden Eintraege gefallen und `#3f464e` mit ihnen. **Die Zusage ist rot
 * geworden und hat es gemeldet** — genau dafuer gibt es die Mengen-Form. Waere hier nur eine
 * Einzelzusage je Farbe gestanden, waere die vierte lautlos verschwunden.
 */
const STUDIO_ROHFARBEN = ['#dfe4ea', '#5b636d', '#1a262a'] as const;

test('Scheibe 4: JEDE inline gebliebene Rohfarbe ist benannt und keine hat einen Token', () => {
  const studio = readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8');
  const gefunden = [...new Set([...studio.matchAll(/#[0-9a-fA-F]{6}\b/g)].map((m) => m[0]!.toLowerCase()))];
  assert.deepEqual(gefunden.sort(), [...STUDIO_ROHFARBEN].sort(),
    'eine Rohfarbe ist dazugekommen oder verschwunden — sie braucht ihre Verriegelung');
  const werte = new Set<string>(Object.values(T).map(String));
  for (const farbe of gefunden) {
    assert.ok(!werte.has(farbe), `${farbe} hat inzwischen einen Token — dann gehoert die Stelle in die CSS`);
  }
});

test('Scheibe 3: die zwei konstanten Stil-Objekte sind fort', () => {
  const fach = readFileSync(join(hier, '../app/FachFlaeche.tsx'), 'utf8');
  assert.ok(!fach.includes('const raster:'), '`raster` steht noch als Inline-Objekt da');
  assert.ok(!fach.includes('const spaltenTitel:'), '`spaltenTitel` steht noch als Inline-Objekt da');
});

/**
 * **Die WIRKUNG, nicht die Gestalt — das Votum zu Scheibe 3 hatte recht.**
 *
 * Mein erster Test pruefte, dass zwei benannte Objekte fort sind. Er war gruen, waehrend **sechs
 * statische Inline-Stile stehen blieben** — die erklaerte Leistung („null statische Inline-Stile")
 * trat nicht ein, und kein Gate merkte es. *Die Gestalt geprueft, nicht die Wirkung* — genau das
 * Muster, das ich in diesem Zyklus fuenfmal an fremden Zusagen bemaengelt habe.
 *
 * Diese Zusage prueft die Wirkung: **jeder verbliebene Inline-Stil muss einen Grund haben**, und der
 * Grund muss einer von zweien sein — Sperr-Werte aus `gesperrtStil.ts` (eine Wahrheit, AUF-71) oder
 * ein Rohwert ohne Token (Kriterium 4 verbietet ihn in der CSS). Alles andere gehoert umgestellt.
 */
test('Scheibe 3 (Wirkung): jeder verbliebene Inline-Stil in FachFlaeche hat einen benannten Grund', () => {
  const fach = readFileSync(join(hier, '../app/FachFlaeche.tsx'), 'utf8');
  const bloecke = [...fach.matchAll(/style=\{\{([\s\S]*?)\}\}/g)].map((m) => m[1]!);
  assert.ok(bloecke.length > 0, 'gar kein Inline-Stil mehr? Dann ist die Zusage stumpf geworden');
  const ohneGrund = bloecke.filter((b) =>
    !b.includes('GESPERRT_') && !/rgba?\(|#[0-9a-fA-F]{3,8}\b/.test(b));
  assert.deepEqual(ohneGrund, [],
    `Inline-Stile ohne Grund (nur Token und Literale — gehoeren in die Stilschicht):\n${ohneGrund.join('\n---\n')}`);
});

test('Scheibe 3 (Wirkung): und es sind genau die drei benannten Ausnahmen', () => {
  const fach = readFileSync(join(hier, '../app/FachFlaeche.tsx'), 'utf8');
  const bloecke = [...fach.matchAll(/style=\{\{([\s\S]*?)\}\}/g)];
  assert.equal(bloecke.length, 3, `${bloecke.length} Inline-Stile — erwartet drei mit Grund`);
  const gruende = bloecke.map(([, b]) => (b!.includes('GESPERRT_') ? 'sperrstil' : 'rohwert'));
  assert.deepEqual(gruende.sort(), ['rohwert', 'rohwert', 'sperrstil']);
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

/* ─────────────────────────────────────────────────────────────────────────────
 * AUF-38 Scheibe 5 — der Konfigurator.
 *
 * **Der Unterschied zu den Scheiben davor: hier misst die Zusage mit DEMSELBEN Werkzeug, das die
 * Grundgesamtheit zaehlt.** Scheibe 4 trug dafuer eine handgeschriebene Regex-Liste (`navZu`,
 * `offeneHubs`, …) — die musste jeder neue Bezeichner nachziehen, und sie war ein zweiter Massstab
 * neben dem Skript. Genau die Sorte Doppelung, gegen die `scripts/statische-inline-stile.mjs`
 * gebaut wurde. **Zwei Massstaebe fuer dieselbe Sache sind der Fehler, nicht die Loesung.**
 * ───────────────────────────────────────────────────────────────────────────── */

const KONFIG = join(hier, '../app/ConfigWizard.tsx');

test('Scheibe 5 (Wirkung): in ConfigWizard bleibt KEINE offene statische Stelle', () => {
  // Die Wirkungs-Aussage nach R2: nicht „diese 33 Klassen existieren", sondern „es ist nichts mehr
  // offen". Eine Gestalt-Zusage geht nie rot, wenn etwas FEHLT.
  const m = messeDatei(KONFIG);
  assert.deepEqual(m.offen, [],
    `offene statische Stellen (nur Literale und Token — gehoeren in die Stilschicht): Z${m.offen.join(', Z')}`);
});

test('Scheibe 5: die Zusage misst ueberhaupt etwas — es gibt noch Inline-Stellen', () => {
  // Der presence-Partner nach R2. Ohne ihn waere die Zusage oben auch dann gruen, wenn die Datei
  // geloescht wird oder das Werkzeug nichts mehr findet.
  const m = messeDatei(KONFIG);
  assert.ok(m.gesamt >= 5, `nur ${m.gesamt} Stellen gefunden — das Werkzeug greift nicht`);
});

test('Scheibe 5: genau ZWEI Ausnahmen, beide benannt, beide ohne Token', () => {
  // **Die Mengenzusage.** Sie ist die Lehre aus AUF38-S4-1 und AUF38-NZ2-1: eine Einzelzusage je
  // Ausnahme findet nur, was jemand vorher gezaehlt hat. Diese hier faellt auch, wenn eine DRITTE
  // Ausnahme dazukommt, die niemand benannt hat.
  const quelltext = readFileSync(KONFIG, 'utf8');
  const ausnahmen = stilBloecke(quelltext).filter((b) => istStatisch(b.text) && istAusnahme(b.text));
  assert.equal(ausnahmen.length, 2, 'die Zahl der zugelassenen Ausnahmen hat sich geaendert');

  // Die Overlay-Flaeche und der Dialog-Schatten. Beide tragen Rohwerte OHNE Token in `T`; in die
  // CSS geholt waeren es rohe Farbwerte und verletzten Kriterium 4, und einen Token dafuer zu
  // erfinden waere ein Palette-Entscheid, der dem Bauenden nicht zusteht.
  assert.match(quelltext, /background: 'rgba\(24,34,38,\.30\)'/, 'die Overlay-Flaeche ist nicht mehr inline');
  assert.match(quelltext, /boxShadow: '0 10px 34px rgba\(28,50,55,\.18\)'/, 'der Dialog-Schatten ist nicht mehr inline');
  const werte = new Set<string>(Object.values(T).map((w) => String(w).toLowerCase()));
  for (const roh of ['rgba(24,34,38,.30)', 'rgba(28,50,55,.18)']) {
    assert.ok(!werte.has(roh), `${roh} hat inzwischen einen Token — dann gehoert die Stelle in die Schicht`);
  }
});

test('Scheibe 5: jede angelegte Klasse wird auch benutzt — keine Regel ins Leere', () => {
  // Die andere Richtung. Eine Klasse in der CSS, die niemand traegt, ist tote Regel; sie faellt in
  // keinem Gate auf und wird beim naechsten Aufraeumen mitgeschleppt.
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const klassen = [...ohneKommentare.matchAll(/\.(hp-kw-[a-z0-9-]+)\s*\{/g)].map((m) => m[1]!);
  assert.ok(klassen.length >= 30, `nur ${klassen.length} Scheibe-5-Klassen in der CSS`);
  const quelltext = readFileSync(KONFIG, 'utf8');
  const unbenutzt = [...new Set(klassen)].filter((k) => !traegt(quelltext, k));
  assert.deepEqual(unbenutzt, [], `Klassen ohne Traeger:\n${unbenutzt.join('\n')}`);
});

/* ─────────────────────────────────────────────────────────────────────────────
 * AUF-38 Scheibe 6 — die gefuehrte Planung.
 *
 * **Die hoechste Ausnahmezahl aller Dateien: fuenf.** Der Auftrag verlangt sie **einzeln benannt
 * und beidseitig verriegelt** — die generische Rohwert-Zusage allein reicht hier nicht, weil sie
 * nur die eine Richtung faengt (*„Farbe bekommt einen Token"*). Die andere Richtung, *„die Stelle
 * ist noch inline"*, steht hier.
 * ───────────────────────────────────────────────────────────────────────────── */

const GEFUEHRT = join(hier, '../app/GuidedView.tsx');

test('Scheibe 6 (Wirkung): in GuidedView bleibt KEINE offene statische Stelle', () => {
  const m = messeDatei(GEFUEHRT);
  assert.deepEqual(m.offen, [],
    `offene statische Stellen — gehoeren in die Stilschicht: Z${m.offen.join(', Z')}`);
});

test('Scheibe 6: die Zusage misst ueberhaupt etwas', () => {
  // presence-Partner nach R2 — sonst waere die Zusage oben auch bei geloeschter Datei gruen.
  const m = messeDatei(GEFUEHRT);
  assert.ok(m.gesamt >= 12, `nur ${m.gesamt} Stellen gefunden — das Werkzeug greift nicht`);
});

/**
 * **Die fuenf Ausnahmen, einzeln.** Jede Zeile nennt ihren Rohwert und ihren Grund. Faellt der
 * Grund weg — bekommt die Farbe einen Token —, geht die Zusage rot und die Stelle gehoert in die
 * Schicht. Das ist die Form, die AUF38-S4-1 und AUF38-NZ2-1 gefehlt hat.
 */
const GEFUEHRT_AUSNAHMEN: ReadonlyArray<[string, string]> = [
  ['rgba(255,255,255,.7)', 'Massstab-Schild ueber der Buehne'],
  ['rgba(20,30,34,.92)', 'Hinweisband am Fuss der Buehne'],
  ['#eef3f2', 'Schrift des Hinweisbands'],
  ['#7fd8d3', 'Symbol im Hinweisband'],
  ['#0a4f4d', 'Titel der empfohlenen Aktion'],
  ['#d3dbdb', 'gestrichelter Rahmen der erweiterten Bearbeitung'],
];

test('Scheibe 6: jede Ausnahme ist noch inline UND hat keinen Token', () => {
  const guided = readFileSync(GEFUEHRT, 'utf8');
  const werte = new Set<string>(Object.values(T).map((w) => String(w).toLowerCase()));
  for (const [roh, grund] of GEFUEHRT_AUSNAHMEN) {
    assert.ok(guided.includes(roh), `${roh} (${grund}) steht nicht mehr inline — Zusage nachfuehren`);
    assert.ok(!werte.has(roh.toLowerCase()),
      `${roh} (${grund}) hat inzwischen einen Token — dann gehoert die Stelle in die Stilschicht`);
  }
});

test('Scheibe 6: es sind GENAU fuenf Ausnahme-Bloecke — keine sechste unbenannte', () => {
  // **Die Mengenzusage.** Eine Liste findet nur, was jemand hineingeschrieben hat; diese Zusage
  // faellt auch bei einer Ausnahme, die niemand gezaehlt hat.
  const guided = readFileSync(GEFUEHRT, 'utf8');
  const ausnahmen = stilBloecke(guided).filter((b) => istStatisch(b.text) && istAusnahme(b.text));
  assert.equal(ausnahmen.length, 5, 'die Zahl der zugelassenen Ausnahme-Bloecke hat sich geaendert');
  // Und jeder dieser Bloecke traegt mindestens einen der benannten Rohwerte — sonst ist eine
  // Ausnahme durch eine andere ersetzt worden, und die Zahl allein haette es verdeckt.
  for (const b of ausnahmen) {
    const benannt = GEFUEHRT_AUSNAHMEN.some(([roh]) => b.text.includes(roh));
    assert.ok(benannt, `Ausnahme in Z${b.zeile} traegt keinen der benannten Rohwerte`);
  }
});

test('Scheibe 6: jede angelegte Klasse wird auch benutzt — keine Regel ins Leere', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const klassen = [...ohneKommentare.matchAll(/\.(hp-gf-[a-z0-9-]+)\s*\{/g)].map((m) => m[1]!);
  assert.ok(klassen.length >= 25, `nur ${klassen.length} Scheibe-6-Klassen in der CSS`);
  const guided = readFileSync(GEFUEHRT, 'utf8');
  const unbenutzt = [...new Set(klassen)].filter((k) => !traegt(guided, k));
  assert.deepEqual(unbenutzt, [], `Klassen ohne Traeger:\n${unbenutzt.join('\n')}`);
});

/* ─────────────────────────────────────────────────────────────────────────────
 * AUF-38 Scheibe 8a — die Engine-Flaeche.
 *
 * **Die erste Scheibe ohne eine einzige Ausnahme.** Diese Datei traegt keinen Rohwert; alle Farben
 * kommen aus `T`. Deshalb steht hier keine Ausnahme-Verriegelung — es gibt nichts zu verriegeln,
 * und eine leere Liste als Zusage waere eine Zusage ueber nichts. **Stattdessen die schaerfere
 * Aussage: es DARF keine Ausnahme geben**, und das faellt, sobald jemand einen Rohwert einfuehrt.
 * ───────────────────────────────────────────────────────────────────────────── */

const ENGINE = join(hier, '../app/EngineFlaeche.tsx');

test('Scheibe 8a (Wirkung): in EngineFlaeche bleibt KEINE offene statische Stelle', () => {
  const m = messeDatei(ENGINE);
  assert.deepEqual(m.offen, [],
    `offene statische Stellen — gehoeren in die Stilschicht: Z${m.offen.join(', Z')}`);
});

test('Scheibe 8a: die Zusage misst ueberhaupt etwas', () => {
  const m = messeDatei(ENGINE);
  assert.ok(m.gesamt >= 4, `nur ${m.gesamt} Stellen gefunden — das Werkzeug greift nicht`);
});

test('Scheibe 8a: die Datei traegt KEINEN Rohwert — und das bleibt so', () => {
  // Die Umkehrung der Ausnahme-Zusage. Kommt ein Rohwert dazu, ist er entweder eine neue Ausnahme
  // (dann gehoert sie benannt und verriegelt) oder ein Versehen — beides soll auffallen.
  const m = messeDatei(ENGINE);
  assert.equal(m.ausnahmen, 0, 'eine Ausnahme ist dazugekommen — sie braucht Namen und Verriegelung');
  const farben = stilBloecke(readFileSync(ENGINE, 'utf8')).flatMap((b) => rohfarben(b.text));
  assert.deepEqual(farben, [], `Rohfarben in EngineFlaeche: ${farben.join(', ')}`);
});

test('Scheibe 8a: jede angelegte Klasse wird auch benutzt — keine Regel ins Leere', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const klassen = [...ohneKommentare.matchAll(/\.(hp-ef-[a-z0-9-]+)\s*\{/g)].map((m) => m[1]!);
  assert.ok(klassen.length >= 20, `nur ${klassen.length} Scheibe-8a-Klassen in der CSS`);
  const engine = readFileSync(ENGINE, 'utf8');
  const unbenutzt = [...new Set(klassen)].filter((k) => !traegt(engine, k));
  assert.deepEqual(unbenutzt, [], `Klassen ohne Traeger:\n${unbenutzt.join('\n')}`);
});

/* ─────────────────────────────────────────────────────────────────────────────
 * AUF-38 Scheibe 8b — die Geschoss-Flaeche.
 *
 * **Die erste Scheibe, die BEIDE Schreibweisen abraeumt** — Auflage des Planners nach Befund
 * `AUF38-MW-7`: `style={{…}}` **und** `style={bezeichner}`. Das Zaehlwerkzeug sieht nur die erste;
 * eine Null aus seinem Lauf allein wuerde hier zwei Stellen verschweigen.
 * ───────────────────────────────────────────────────────────────────────────── */

const GESCHOSS = join(hier, '../app/dashboard/GeschossFlaeche.tsx');

test('Scheibe 8b (Wirkung): in GeschossFlaeche bleibt KEINE offene statische Stelle', () => {
  const m = messeDatei(GESCHOSS);
  assert.deepEqual(m.offen, [],
    `offene statische Stellen — gehoeren in die Stilschicht: Z${m.offen.join(', Z')}`);
});

test('Scheibe 8b: die Zusage misst ueberhaupt etwas', () => {
  const m = messeDatei(GESCHOSS);
  assert.ok(m.gesamt >= 5, `nur ${m.gesamt} Stellen gefunden — das Werkzeug greift nicht`);
});

test('Scheibe 8b (AUF38-MW-7): die ZWEITE Schreibweise ist ebenfalls abgeraeumt', () => {
  // `style={knopfStil}` war ein konstantes Objekt an zwei Knoepfen — derselbe statische Inline-Stil,
  // nur in der Schreibweise, die `messeDatei` nicht zaehlt. **Ohne diese Zusage bliebe die Null
  // oben stehen und meinte zwei Stellen nicht.**
  const geschoss = readFileSync(GESCHOSS, 'utf8');
  assert.doesNotMatch(geschoss, /style=\{knopfStil\}/,
    'die direkte Verwendung des konstanten Objekts ist zurueck — sie gehoert in die Klasse');
  assert.ok(traegt(geschoss, 'hp-gs-knopf'), 'die Klasse dafuer wird nicht benutzt');
  // **Was bleibt, bleibt mit Grund:** `knopfStil` wird an drei weiteren Stellen per Spread
  // gemischt (`{ ...knopfStil, … }`). Diese Bloecke sind nach der Definition **dynamisch** und
  // gehoeren nicht zu dieser Scheibe. Faellt die letzte Spread-Verwendung weg, gehoert die
  // Konstante selbst fort — und dann faellt diese Zusage.
  const spreads = (geschoss.match(/\.\.\.knopfStil/g) ?? []).length;
  assert.ok(spreads > 0,
    'keine Spread-Verwendung mehr — dann ist `knopfStil` tot und gehoert geloescht');
});

test('Scheibe 8b: die Datei traegt KEINEN Rohwert — und das bleibt so', () => {
  const m = messeDatei(GESCHOSS);
  assert.equal(m.ausnahmen, 0, 'eine Ausnahme ist dazugekommen — sie braucht Namen und Verriegelung');
  const farben = stilBloecke(readFileSync(GESCHOSS, 'utf8')).flatMap((b) => rohfarben(b.text));
  assert.deepEqual(farben, [], `Rohfarben in GeschossFlaeche: ${farben.join(', ')}`);
});

test('Scheibe 8b: jede angelegte Klasse wird auch benutzt — keine Regel ins Leere', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const klassen = [...ohneKommentare.matchAll(/\.(hp-gs-[a-z0-9-]+)\s*\{/g)].map((m) => m[1]!);
  assert.ok(klassen.length >= 14, `nur ${klassen.length} Scheibe-8b-Klassen in der CSS`);
  const geschoss = readFileSync(GESCHOSS, 'utf8');
  const unbenutzt = [...new Set(klassen)].filter((k) => !traegt(geschoss, k));
  assert.deepEqual(unbenutzt, [], `Klassen ohne Traeger:\n${unbenutzt.join('\n')}`);
});

/* ─────────────────────────────────────────────────────────────────────────────
 * AUF-38 Scheibe 8c — die vier kleinen Flaechen, eine Einheit.
 *
 * Vier Dateien, ein Posten — so geschnitten, weil der `population_command` sie in einem Lauf misst.
 * Die Zusage prueft sie deshalb auch in einem Lauf: **keine der vier traegt noch eine offene
 * statische Stelle.**
 * ───────────────────────────────────────────────────────────────────────────── */

const KLEINE = [
  '../app/dashboard/WerkzeugGruppenMenue.tsx',
  '../app/FaehigkeitenNavi.tsx',
  '../app/DreiDBereich.tsx',
  '../app/dashboard/ReiterLeiste.tsx',
].map((p) => join(hier, p));

test('Scheibe 8c (Wirkung): keine der vier kleinen Flaechen hat noch eine offene Stelle', () => {
  const offen = KLEINE.flatMap((p) => messeDatei(p).offen.map((z) => `${p.split('/').pop()}:Z${z}`));
  assert.deepEqual(offen, [], `offene statische Stellen:\n${offen.join('\n')}`);
});

test('Scheibe 8c: die Zusage misst ueberhaupt etwas — alle vier Dateien werden gefunden', () => {
  // presence-Partner nach R2. Ein Tippfehler im Pfad wuerde die Zusage oben still gruen lassen.
  for (const p of KLEINE) {
    assert.ok(messeDatei(p).gesamt >= 1, `${p.split('/').pop()}: keine einzige Stelle gefunden`);
  }
});

test('Scheibe 8c: genau ZWEI Ausnahmen, beide in DreiDBereich, beide ohne Token', () => {
  const werte = new Set<string>(Object.values(T).map((w) => String(w).toLowerCase()));
  const ausnahmen = KLEINE.flatMap((p) => stilBloecke(readFileSync(p, 'utf8'))
    .filter((b) => istStatisch(b.text) && istAusnahme(b.text))
    .map((b) => ({ datei: p.split('/').pop()!, block: b })));
  assert.equal(ausnahmen.length, 2, 'die Zahl der zugelassenen Ausnahmen hat sich geaendert');
  for (const a of ausnahmen) {
    assert.equal(a.datei, 'DreiDBereich.tsx', `unerwartete Ausnahme in ${a.datei}`);
  }
  // Die Werkzeugleiste ueber dem 3D-Bild und der Ladehinweis. Beide Rohwert ohne Token in `T`.
  for (const roh of ['#ffffffcc', '#e5e7eb', '#6b7280', '#d1d5db']) {
    assert.ok(!werte.has(roh), `${roh} hat inzwischen einen Token — dann gehoert die Stelle in die Schicht`);
  }
});

test('Scheibe 8c: jede angelegte Klasse wird auch benutzt — keine Regel ins Leere', () => {
  const ohneKommentare = quelle.replace(/\/\*[\s\S]*?\*\//g, '');
  const klassen = [...ohneKommentare.matchAll(/\.(hp-(?:wg|fn|3d|rl)-[a-z0-9-]+)\s*\{/g)].map((m) => m[1]!);
  assert.ok(klassen.length >= 13, `nur ${klassen.length} Scheibe-8c-Klassen in der CSS`);
  const zusammen = KLEINE.map((p) => readFileSync(p, 'utf8')).join('\n');
  const unbenutzt = [...new Set(klassen)].filter((k) => !traegt(zusammen, k));
  assert.deepEqual(unbenutzt, [], `Klassen ohne Traeger:\n${unbenutzt.join('\n')}`);
});

/* ─────────────────────────────────────────────────────────────────────────────
 * AUF-83-T2 — die zweite und die dritte Navigation fallen.
 *
 * **Warum diese Zusagen hier stehen, obwohl der Auftrag sie nicht verlangt:** K-01 bis K-04 sind
 * `absence`-Kriterien, und ihr Pruefverfahren ist ein `grep`. **Ein grep zur Abnahme verhindert
 * keinen Rueckfall** — er misst einmal und schweigt danach. Die Gegenprobe hat es gezeigt: die
 * zweite Navigation liess sich zurueckholen, **ohne dass ein einziger Test rot wurde.**
 * *Eine Abwesenheit, die niemand bewacht, ist eine Absicht und kein Zustand.*
 * ───────────────────────────────────────────────────────────────────────────── */

const STUDIO_TSX = join(hier, '../app/HausplanerStudio.tsx');
const STUDIO_BLADE = readFileSync(join(hier, '../../../views/admin/hausplaner/studio.blade.php'), 'utf8');
const OBJEKT_BLADE = readFileSync(join(hier, '../../../views/admin/hausplaner/objekt.blade.php'), 'utf8');

test('T2/K-01: der Studio-Bildschirm traegt keine zweite Navigation mehr', () => {
  const studio = readFileSync(STUDIO_TSX, 'utf8');
  assert.doesNotMatch(studio, /hp-navi/, 'die zweite Navigation ist zurueck');
  assert.doesNotMatch(quelle, /\.hp-navi/, 'ihre Regeln stehen wieder in der Stilschicht');
  // presence-Partner nach R2: die Datei existiert und traegt ueberhaupt noch Markup.
  assert.match(studio, /className="hp-studio-kopf"/, 'die Kopfzeile ist fort — dann prueft das hier nichts');
});

test('T2/K-01: die DATEN der Navigation sind unberuehrt', () => {
  // Der ausdrueckliche Ausschluss des Auftrags. Faellt `FACH` mit, hat T2 stillschweigend T3
  // vorweggenommen — und die Fachplaner waeren aus dem Studio verschwunden statt umgezogen.
  const daten = readFileSync(join(hier, '../app/studioDaten.ts'), 'utf8');
  assert.match(daten, /export const FACH/, 'FACH ist geloescht — das war nicht beauftragt');
  assert.match(daten, /export const PROJ/, 'PROJ ist geloescht — das war nicht beauftragt');
});

test('T2/K-03: die Hausplaner-Marke ist fort — in der Insel UND in BEIDEN Blades', () => {
  // Yama: „ich brauche kein logo fuer hausplaner oben." Der Grund ist nicht Geschmack: die
  // Ticket-Sidebar markiert den Bereich seit T1b selbst. Eine zweite Marke ist Wiederholung.
  //
  // **Erweitert nach Planner-Entscheid 21:10.** Mein erster Schnitt nahm nur die Studio-Blade —
  // meine eigene Messung hatte gezeigt, dass `objekt.blade` sehr wohl eine Leiste traegt. **Nur
  // im Studio zu raeumen hiesse, zwei Hausplaner-Flaechen mit verschiedenem Kopf zu hinterlassen.**
  assert.doesNotMatch(readFileSync(STUDIO_TSX, 'utf8'), /hp-marke|hp-title/);
  assert.doesNotMatch(STUDIO_BLADE, /hp-marke|hp-title/);
  assert.doesNotMatch(OBJEKT_BLADE, /hp-marke|hp-title/);
  assert.doesNotMatch(OBJEKT_BLADE, /‹ Zurück/, 'der doppelte Zurueck-Weg ist zurueck');
});

test('T2/K-03: der EINZIGARTIGE Inhalt der Objektleiste ist NICHT verloren — er steht jetzt in der Kopfleiste', () => {
  // **Diese Zusage hat ihren eigenen Nachfolger benannt.** Ihr Wortlaut lautete: *„Objektname mit
  // Adresse und der Uebernehmen-Knopf sind kein Doppel der Shell — **sie wandern mit T3 in die
  // Kopfleiste**. Faellt hier etwas davon, hat T2 still T3 vorweggenommen und dabei Funktion
  // entfernt statt Wiederholung."*
  //
  // **T3 hat den angekuendigten Umzug ausgefuehrt.** Die Zusage wird deshalb nicht geloescht,
  // sondern **umgehaengt**: sie prueft weiterhin genau dieselbe Sache — dass keiner der drei
  // Inhalte auf dem Weg verloren ging —, nur am neuen Ort. *Eine Zusage zu streichen, weil der
  // Bau sie eingeholt hat, ist genau der Moment, in dem eine Funktion still verschwindet.*
  //
  // **Ein zweites Mal umgehaengt durch AUF-83-T3-N1:** K-08 war rot (Zeile 1 kostete Hoehe statt
  // sie zu gewinnen), der Uebernehmen-Knopf und die Staleness-Pille sind seither hinter einem
  // Ueberlauf-Knopf in einer eigenen Datei (`ObjektkopfUeberlauf.tsx`) — nicht geloescht, nur ein
  // zweites Mal umgezogen. Der Objektname bleibt sichtbar in `HausplanerApp.tsx`.
  const app = zerlegteApp();
  const ueberlauf = readFileSync(join(hier, '../app/dashboard/ObjektkopfUeberlauf.tsx'), 'utf8');
  assert.match(app, /className="hp-ok-name"/, 'die Objektidentitaet ist auf dem Weg verloren gegangen');
  assert.match(ueberlauf, /In Auslegung übernehmen/, 'der Uebernehmen-Knopf ist auf dem Weg verloren gegangen');
  assert.match(ueberlauf, /hp-ok-pille--\$\{objektkopf\.status\}/, 'die Staleness-Pille ist auf dem Weg verloren gegangen');
  // Und im Blade steht keiner von ihnen mehr — sonst stuenden sie zweimal.
  assert.doesNotMatch(OBJEKT_BLADE, /class="hp-obj"/, 'die Objektidentitaet steht doppelt');
  assert.doesNotMatch(OBJEKT_BLADE, /class="hp-uebernehmen"/, 'der Uebernehmen-Knopf steht doppelt');
  // Und in HausplanerApp.tsx selbst steht der Knopf nicht mehr doppelt neben dem Ueberlauf.
  assert.doesNotMatch(app, /In Auslegung übernehmen/, 'der Uebernehmen-Knopf steht jetzt doppelt (App UND Ueberlauf)');
});

test('T2/K-02: der Testflaechen-Hinweis steht nicht mehr in der Blade-Leiste', () => {
  // **Der gekoppelte bleibt, der feste faellt.** Der Hinweis der Insel haengt am fehlenden
  // `data-speichern-url` und ist testverriegelt (`speicherAnzeige.test.ts`); der in der Blade war
  // eine feste Zeichenkette und stand immer da — auch dann, wenn gespeichert wuerde.
  const blade = STUDIO_BLADE;
  assert.doesNotMatch(blade, /class="hp-scratch"/, 'die feste Zeichenkette ist zurueck');
  assert.doesNotMatch(blade, /class="hp-bar"/, 'die eigene Leiste ist zurueck');
});

test('T2/K-04: der Erklaertext kostet keine Zeile mehr — ist aber ERHALTEN', () => {
  const studio = readFileSync(STUDIO_TSX, 'utf8');
  assert.doesNotMatch(studio, /hp-experte-leiste|hp-experte-hinweis|hp-experte-zurueck/,
    'die dauerhafte Zeile ist zurueck');
  // **Die Grenze des Auftrags, woertlich: „Der Text wird NICHT geloescht."**
  assert.match(studio, /Experte — alle Werkzeuge, Projektbaum und Eigenschaften/,
    'der Erklaertext ist geloescht statt umgezogen');
  assert.match(studio, /title=\{titel\}/, 'er haengt an keinem Titel-Attribut mehr');
});

test('T2/K-05: der Weg in die gefuehrte Planung ist direkt erreichbar', () => {
  // Er lag im Zurueck-Knopf der Experten-Leiste, die mit K-04 faellt. **Ohne Ersatz waere er zwei
  // Klicks entfernt gewesen** — deshalb steht er jetzt als eigener Schalter im Modusschalter,
  // sichtbar in jedem Modus.
  const studio = readFileSync(STUDIO_TSX, 'utf8');
  assert.match(studio, /modeBtn\('guided', 'Geführte Planung'/, 'der direkte Weg fehlt');
});
