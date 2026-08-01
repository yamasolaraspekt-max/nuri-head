/**
 * AUF-38-P2 / K-03 — **die Stilschicht von Gruppenzeile und Schiene, verriegelt.**
 *
 * ---
 *
 * **Die Auflage des Evaluators aus der P1-Abnahme, und sie hat sich sofort bestätigt.** Die
 * Mutationsprobe **vor** dieser Datei, acht Mutationen an genau dem, was der Umbau anfasst:
 *
 * ```text
 * blind (7)  Klasse am falschen Element · zwei Klassen vertauscht · Regel ohne Wirkung
 *            Klassenname mit Tippfehler · Regel umbenannt · Abstand still veraendert
 *            A11y-Pfeilklasse entfernt
 * gefangen   nur "Rohfarbe statt Token"
 * ```
 *
 * ***Sieben von acht — dieselbe Zahl wie in P1.*** Nicht ähnlich, dieselbe. Das ist kein Zufall
 * dieser Datei, sondern die Eigenschaft der Umstellung selbst: **ein Inline-Stil wird von den
 * Bauteil-Zusagen mitgelesen; eine Klasse verlagert die sichtbare Wahrheit in eine zweite Datei,
 * und zwischen beiden liegt nichts, wenn niemand die Brücke prüft.**
 *
 * *Deshalb steht der Brücken-Test in P2 als Pflichtteil im Blatt und nicht mehr als Einfall des
 * Bauenden. Für P3 gilt dasselbe, bevor jemand fragt.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { teil } from './_zerlegteApp';

const quelle = teil('app/rahmen/GruppenzeileUndSchiene.tsx');
const css = teil('hausplaner.css');

/** Die `hp-gz-`-Klassen, die die Datei wirklich benutzt. */
function benutzteKlassen(): string[] {
  const raus = new Set<string>();
  for (const m of quelle.matchAll(/className="([^"]+)"/g)) {
    for (const k of m[1].split(/\s+/)) if (k.startsWith('hp-gz-')) raus.add(k);
  }
  return [...raus].sort();
}

/** Der Regelrumpf einer Klasse, oder `null`. */
function regel(klasse: string): string | null {
  const m = css.match(new RegExp(`\\.${klasse.replace(/-/g, '\\-')}\\s*\\{([^}]*)\\}`));
  return m ? m[1].trim() : null;
}

// --- Die Brücke: benutzt ⇔ definiert ---------------------------------------------------------------

test('K-03: jede benutzte `hp-gz-`-Klasse ist in der Stilschicht definiert', () => {
  // Die Mutationen „Klassenname mit Tippfehler" und „Regel umbenannt" kamen beide durch.
  // Ein ungestyltes Element sieht im Testlauf aus wie ein gestyltes — nur im Browser nicht.
  const benutzt = benutzteKlassen();
  assert.ok(benutzt.length >= 15, `nur ${benutzt.length} Klassen benutzt — die Zusage misst Leere`);
  for (const k of benutzt) {
    assert.ok(regel(k) !== null, `\`.${k}\` wird benutzt, steht aber in keiner Regel — das Element bleibt ungestylt`);
  }
});

test('K-03: keine `hp-gz-`-Regel ohne Nutzer', () => {
  const definiert = [...css.matchAll(/^\.(hp-gz-[a-z0-9-]+)\s*\{/gm)].map((m) => m[1]);
  assert.ok(definiert.length >= 15, `nur ${definiert.length} Regeln gefunden — die Zusage misst Leere`);
  const benutzt = new Set(benutzteKlassen());
  for (const k of definiert) {
    assert.ok(benutzt.has(k), `\`.${k}\` ist definiert, wird aber nirgends benutzt`);
  }
});

// --- Jede Klasse trägt, was sie ersetzt hat ---------------------------------------------------------

/**
 * **Die Eigenschaften aus dem Stand VOR dem Umbau**, Zeile für Zeile abgelesen.
 * *Ohne diese Tabelle wäre „die Klasse existiert" die ganze Aussage — und eine leere Regel
 * erfüllt sie. Genau diese Mutation kam durch.*
 */
const ERSETZT: ReadonlyArray<readonly [string, readonly string[]]> = [
  ['hp-gz-optionsfeld', ['font-size: 12.5px', 'padding: 4px 8px', 'var(--hp-control-border)']],
  ['hp-gz-optionszeile', ['flex: 0 0 auto', 'display: flex', 'gap: 10px', 'var(--hp-surface2)', 'var(--hp-hair)']],
  ['hp-gz-trenner', ['width: 1px', 'height: 16px', 'var(--hp-hair)']],
  ['hp-gz-gruppenzeile', ['display: flex', 'gap: 2px', 'padding: 3px 14px 6px', 'var(--hp-bg)', 'flex: 0 0 auto']],
  ['hp-gz-inhalt', ['flex: 1', 'min-height: 0', 'overflow-y: auto', 'flex-direction: column']],
  ['hp-gz-wegweiser', ['margin: 8px 10px 4px', 'border-radius: 9px', 'var(--hp-brand-wash)', 'var(--hp-brand-ink)', 'overflow-wrap: anywhere']],
  ['hp-gz-wegweiser-pfeil', ['flex: 0 0 auto']],
  ['hp-gz-wegweiser-satz', ['flex: 1 1 120px', 'min-width: 0']],
  ['hp-gz-werkzeugicon', ['width: 18px', 'height: 18px', 'place-items: center', 'flex: 0 0 auto']],
  ['hp-gz-werkzeuglabel', ['flex: 1']],
  ['hp-gz-kuerzel', ['font-size: 10.5px', 'var(--hp-muted)', 'var(--hp-control-border)', 'padding: 1px 5px']],
  ['hp-gz-leerzustand', ['flex-direction: column', 'align-items: flex-start', 'padding: 2px 12px 10px', 'var(--hp-muted)']],
  ['hp-gz-gruppe', ['margin-bottom: 2px']],
  ['hp-gz-gruppenkopf', ['padding: 6px 12px 2px', 'font-weight: 700', 'letter-spacing: .04em', 'var(--hp-muted)']],
  ['hp-gz-gruppenname', ['flex: 1']],
  ['hp-gz-gruppenzahl', ['font-variant-numeric: tabular-nums']],
  ['hp-gz-eingeklappt', ['padding: 0 12px 6px 20px', 'font-size: 11px', 'var(--hp-muted)']],
  ['hp-gz-fuss', ['padding: 10px 12px', 'var(--hp-muted)', 'var(--hp-canvas-grid)', 'flex: 0 0 auto']],
];

test('K-03: jede Klasse trägt die Eigenschaften, die ihr Inline-Stil trug', () => {
  for (const [klasse, muss] of ERSETZT) {
    const r = regel(klasse);
    assert.ok(r, `\`.${klasse}\` fehlt in der Stilschicht`);
    for (const eigenschaft of muss) {
      assert.ok(r.includes(eigenschaft),
        `\`.${klasse}\` hat \`${eigenschaft}\` verloren — der Inline-Stil trug es:\n    ${r}`);
    }
  }
});

// --- Jede Klasse an ihrem Element -------------------------------------------------------------------

test('K-03: die Optionszeile und die Gruppenzeile sind NICHT vertauscht', () => {
  // Mutation „Klasse am falschen Element" kam durch: die beiden unterscheiden sich in Hintergrund,
  // Innenmass und Rahmen — sichtbar, aber von nichts gehalten.
  const opt = quelle.indexOf('className="hp-gz-optionszeile"');
  const grp = quelle.indexOf('className="hp-gz-gruppenzeile"');
  assert.ok(opt > 0, 'die Optionszeile trägt ihre Klasse nicht');
  assert.ok(grp > 0, 'die Gruppenzeile trägt ihre Klasse nicht');
  assert.ok(opt < grp, 'die Optionszeile steht nicht mehr vor der Gruppenzeile');
  // Und jede genau einmal — eine zweite Verwendung wäre eine stille Dublette.
  assert.equal([...quelle.matchAll(/className="hp-gz-optionszeile"/g)].length, 1);
  assert.equal([...quelle.matchAll(/className="hp-gz-gruppenzeile"/g)].length, 1);
});

test('K-03: Gruppenname und Gruppenzahl sind NICHT vertauscht', () => {
  // Mutation „zwei Klassen vertauscht" kam durch. Der Name füllt die Zeile (`flex: 1`), die Zahl
  // steht rechts mit gleichbreiten Ziffern — vertauscht springt die Zahl in der Liste.
  const kopf = quelle.slice(quelle.indexOf('className="hp-gz-gruppenkopf"'));
  const name = kopf.indexOf('className="hp-gz-gruppenname"');
  const zahl = kopf.indexOf('className="hp-gz-gruppenzahl"');
  assert.ok(name > 0 && zahl > 0, 'eine der beiden Klassen fehlt im Gruppenkopf');
  assert.ok(name < zahl, 'Gruppenname und Gruppenzahl sind vertauscht');
});

test('K-03: der Wegweiser behält Pfeil UND Satz — beide mit ihrer Klasse', () => {
  // Mutation „A11y-Pfeilklasse entfernt" kam durch. Der Pfeil ist `aria-hidden` und trägt die
  // Bedeutung nicht; ohne `flex: 0 0 auto` schrumpft er und der Satz rutscht.
  const w = quelle.slice(quelle.indexOf('className="hp-gz-wegweiser"'));
  assert.match(w.slice(0, 400), /aria-hidden className="hp-gz-wegweiser-pfeil"/, 'der Pfeil hat seine Klasse verloren');
  assert.match(w.slice(0, 400), /className="hp-gz-wegweiser-satz"/, 'der Satz hat seine Klasse verloren');
});

test('K-03: die zwei vorhandenen `hp-schiene-`-Klassen sind unberührt', () => {
  // Ausschluss des Blattes: sie liegen bereits in der Stilschicht. Wer sie umbenennt, bewegt
  // Stellen, die dieses Blatt nicht misst.
  for (const k of ['hp-schiene-kopf', 'hp-schiene-kopf-reiter']) {
    assert.ok(quelle.includes(k), `\`${k}\` ist aus der Datei verschwunden`);
    assert.ok(regel(k) !== null, `\`.${k}\` ist aus der Stilschicht verschwunden`);
  }
});

// --- Die Grenze des Auftrags ------------------------------------------------------------------------

test('K-01: in der Datei bleibt keine offene statische Inline-Stelle', () => {
  const zeilen = quelle.split('\n').filter((z) => z.includes('style={{'));
  assert.equal(zeilen.length, 9, `${zeilen.length} Inline-Zeilen statt 9`);
  // presence-Partner nach R2: die verbliebenen sind DYNAMISCH und damit ausserhalb des Auftrags.
  const alle = quelle.split('\n');
  for (const z of zeilen) {
    const i = alle.indexOf(z);
    const block = alle.slice(i, i + 7).join(' ');
    assert.ok(/\$\{|\?|FARBEN\.|T\.|schienen\.|istSchmal|zustand\./.test(block),
      `eine verbliebene Inline-Stelle sieht statisch aus und gehörte umgestellt:\n    ${block.trim().slice(0, 130)}`);
  }
});
