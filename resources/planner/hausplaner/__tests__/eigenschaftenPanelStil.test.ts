/**
 * AUF-38-P1 / K-05 — **die Stilschicht des Eigenschaften-Panels, verriegelt.**
 *
 * ---
 *
 * **Der Befund, der diese Datei nötig gemacht hat.** Die Mutationsprobe **vor** dem Schreiben,
 * acht Mutationen an genau dem, was der Umbau anfasst — **sieben kamen durch:**
 *
 * ```text
 * Klasse am falschen Element · zwei Klassen vertauscht · Regel ohne Wirkung
 * Regel umbenannt, Markup zeigt ins Leere · A11y-Symbolklasse entfernt
 * Treppen-Raster verliert seine Hoehe · Abstand still veraendert
 * gefangen: nur "Rohfarbe statt Token" (von der Zusage aus Scheibe 1)
 * ```
 *
 * **Das ist die Kehrseite von AUF-38, und sie gehört benannt:** ein Inline-Stil steht im Bauteil
 * und wird von den Bauteil-Zusagen mitgelesen. Eine Klasse verlagert die sichtbare Wahrheit in
 * eine zweite Datei — und zwischen beiden liegt **nichts**, wenn niemand die Brücke prüft.
 * *Ein Tippfehler im Klassennamen macht ein Element ungestylt, und kein Testlauf merkt es.*
 *
 * **Diese Datei ist die Brücke.** Sie prüft drei Dinge, die vorher niemand geprüft hat:
 *
 * 1. **Jede benutzte Klasse existiert** in der Stilschicht — und umgekehrt: keine Regel ohne Nutzer.
 * 2. **Jede Klasse trägt die Eigenschaften**, die sie ersetzt hat — Zeile für Zeile aus dem
 *    Stand vor dem Umbau.
 * 3. **Jede Klasse sitzt an ihrem Element** — die fünf Abschnittstitel, die vier Knopfreihen,
 *    das Treppen-Raster mit seiner eigenen Höhe.
 *
 * **Warum eine NEUE Datei:** das Blatt schliesst `eigenschaftenPanel.test.ts` ausdrücklich aus
 * (*„S4d hat dort zwei ungeschützte A11y-Entscheidungen geschlossen"*). Der Ausschluss wird
 * respektiert; was hier dazukommt, steht daneben statt darin.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const panel = readFileSync(join(hier, '../app/rahmen/EigenschaftenPanel.tsx'), 'utf8');
const css = readFileSync(join(hier, '../hausplaner.css'), 'utf8');

/** Die Klassen, die im Panel wirklich benutzt werden. */
function benutzteKlassen(): string[] {
  const raus = new Set<string>();
  for (const m of panel.matchAll(/className="([^"]+)"/g)) {
    for (const k of m[1].split(/\s+/)) if (k.startsWith('hp-ep-')) raus.add(k);
  }
  return [...raus].sort();
}

/** Der Regelrumpf einer Klasse aus der Stilschicht, oder `null`. */
function regel(klasse: string): string | null {
  const m = css.match(new RegExp(`\\.${klasse.replace(/-/g, '\\-')}\\s*\\{([^}]*)\\}`));
  return m ? m[1].trim() : null;
}

// --- Die Brücke: benutzt ⇔ definiert ---------------------------------------------------------------

test('K-05: jede benutzte `hp-ep-`-Klasse ist in der Stilschicht auch definiert', () => {
  // Mutation „Regel umbenannt, Markup zeigt ins Leere" kam durch. Ein ungestyltes Element sieht
  // im Testlauf aus wie ein gestyltes — nur im Browser nicht.
  const benutzt = benutzteKlassen();
  assert.ok(benutzt.length >= 20, `nur ${benutzt.length} Klassen benutzt — die Zusage misst Leere`);
  for (const k of benutzt) {
    assert.ok(regel(k) !== null, `\`.${k}\` wird benutzt, steht aber in keiner Regel — das Element bleibt ungestylt`);
  }
});

test('K-05: keine `hp-ep-`-Regel ohne Nutzer — eine Klasse, die niemand trägt, ist tot', () => {
  const definiert = [...css.matchAll(/^\.(hp-ep-[a-z0-9-]+)\s*\{/gm)].map((m) => m[1]);
  assert.ok(definiert.length >= 20, `nur ${definiert.length} Regeln gefunden — die Zusage misst Leere`);
  const benutzt = new Set(benutzteKlassen());
  for (const k of definiert) {
    assert.ok(benutzt.has(k), `\`.${k}\` ist definiert, wird aber nirgends benutzt`);
  }
});

// --- Jede Klasse trägt, was sie ersetzt hat ---------------------------------------------------------

/**
 * **Die Eigenschaften aus dem Stand VOR dem Umbau**, Zeile für Zeile aus `15de0857` abgelesen.
 * *Ohne diese Tabelle wäre „die Klasse existiert" die ganze Aussage — und eine leere Regel
 * erfüllt sie.*
 */
const ERSETZT: ReadonlyArray<readonly [string, readonly string[]]> = [
  ['hp-ep-titel', ['font-weight: 700', 'margin-bottom: 10px']],
  ['hp-ep-untertitel', ['font-weight: 700', 'margin: 12px 0 6px']],
  ['hp-ep-knopfreihe', ['display: flex', 'gap: 6px', 'margin-top: 8px']],
  ['hp-ep-knopfreihe-unten', ['display: flex', 'gap: 6px', 'margin-bottom: 12px']],
  ['hp-ep-feldgruppe', ['margin-bottom: 12px']],
  ['hp-ep-rubrik', ['font-size: 11px', 'font-weight: 700', 'text-transform: uppercase', 'var(--hp-muted)']],
  ['hp-ep-bauartraster', ['display: grid', 'repeat(4, 1fr)', 'max-height: 220px', 'overflow-y: auto']],
  ['hp-ep-bauartraster--treppe', ['max-height: 200px']],
  ['hp-ep-bauartbild', ['width: 100%', 'height: auto', 'display: block']],
  ['hp-ep-befund', ['display: flex', 'var(--hp-err-soft)', 'var(--hp-err-border)', 'var(--hp-err-ink)']],
  ['hp-ep-befundliste', ['list-style: none', 'padding: 0']],
  ['hp-ep-schwere-symbol', ['font-weight: 700']],
  ['hp-ep-schwere-text', ['font-weight: 700']],
  ['hp-ep-typ', ['border-radius: 999px', 'var(--hp-brand-wash)', 'var(--hp-brand-ink)']],
  ['hp-ep-mehrfach', ['var(--hp-surface2)', 'var(--hp-hair)']],
  ['hp-ep-abschnitt', ['margin-top: 12px', 'padding-top: 10px', 'var(--hp-hair)']],
  ['hp-ep-fusskasten', ['margin-top: 12px', 'padding: 10px', 'var(--hp-bg)', 'border-radius: 8px']],
  ['hp-ep-umfang', ['font-size: 11px', 'var(--hp-muted)', 'padding-top: 8px']],
  ['hp-ep-kennzahl', ['font-size: 12px']],
  ['hp-ep-lesehinweis', ['font-size: 11.5px', 'margin-bottom: 10px']],
];

test('K-05: jede Klasse trägt die Eigenschaften, die ihr Inline-Stil trug', () => {
  // Die Mutationen „Regel ohne Wirkung" und „Abstand still verändert" kamen beide durch. Eine
  // leere Regel erfüllt jede Existenz-Zusage und ändert doch das Bild.
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

test('K-05: die fünf Abschnittstitel tragen `hp-ep-titel` — nicht eine fremde Klasse', () => {
  // Mutation „Klasse am falschen Element" kam durch: `hp-ep-titel` gegen `hp-ep-rubrik` getauscht
  // ändert Schriftgrösse, Laufweite und Farbe — und keine Zusage sagte etwas.
  const titel = [...panel.matchAll(/className="hp-ep-titel"/g)];
  assert.equal(titel.length, 5, `${titel.length} Abschnittstitel statt fünf (Dach · Wand · Öffnung · Treppe · Objekt)`);
  for (const wort of ['Dach', 'Wand', 'Treppe']) {
    assert.match(panel, new RegExp(`className="hp-ep-titel">${wort}<`), `der Titel „${wort}" trägt die Titel-Klasse nicht`);
  }
});

test('K-05: die vier Knopfreihen stehen UNTER ihrem Abschnitt — nicht darüber', () => {
  // Mutation „zwei Klassen vertauscht" kam durch. Der Unterschied ist `margin-top: 8px` gegen
  // `margin-bottom: 12px` — sichtbar, aber von nichts gehalten.
  assert.equal([...panel.matchAll(/className="hp-ep-knopfreihe"/g)].length, 4,
    'die Zahl der Knopfreihen mit Abstand NACH oben hat sich geändert');
  assert.equal([...panel.matchAll(/className="hp-ep-knopfreihe-unten"/g)].length, 1,
    'die eine Reihe mit Abstand nach unten (Mehrfachauswahl) ist fort oder verdoppelt');
});

test('K-05: das Treppen-Raster trägt BEIDE Klassen — Grundform und eigene Höhe', () => {
  // Mutation „Treppen-Raster verliert seine Höhe" kam durch: es bekäme 220 statt 200 px.
  assert.match(panel, /className="hp-ep-bauartraster hp-ep-bauartraster--treppe"/,
    'das Treppen-Raster hat seine eigene Höhe verloren');
  assert.equal([...panel.matchAll(/className="hp-ep-bauartraster/g)].length, 2,
    'es gibt nicht mehr genau zwei Bauart-Raster (Öffnungen und Treppen)');
});

test('K-05: die A11y-Klassen der Schwere sitzen an ihren Elementen', () => {
  // Mutation „A11y-Symbolklasse entfernt" kam durch. S4d hat gemessen, dass die Regel
  // „Symbol UND Text" durch nichts geschützt war; sie darf es beim Umbau nicht wieder werden.
  const i = panel.indexOf('befunde.map(');
  assert.ok(i > 0, 'die Befundliste wurde nicht gefunden — die Zusage misst Leere');
  const eintrag = panel.slice(i, panel.indexOf('</ul>', i));
  assert.match(eintrag, /aria-hidden className="hp-ep-schwere-symbol"/, 'das Schwere-Symbol hat seine Klasse verloren');
  assert.match(eintrag, /<strong className="hp-ep-schwere-text">Abgelehnt<\/strong>/, 'der Schwere-Text hat seine Klasse verloren');
});

// --- Die Grenze des Auftrags ------------------------------------------------------------------------

test('K-01: im Panel steht keine offene statische Inline-Stelle mehr', () => {
  // Die führende Zahl des Blattes, hier als Zusage statt nur als Abnahmebefehl.
  // **Zeilen, nicht Vorkommen** — das Messwerkzeug zählt 34 Stellen, weil zwei Zeilen zwei tragen.
  const alle = panel.split('\n');
  const treffer = alle.map((z, i) => [z, i] as const).filter(([z]) => z.includes('style={{'));
  assert.equal(treffer.length, 32, `${treffer.length} Inline-Zeilen statt 32`);
  // presence-Partner nach R2: die verbliebenen sind DYNAMISCH und damit ausserhalb des Auftrags.
  //
  // **Meine erste Fassung prüfte nur die Fundzeile** — bei einem mehrzeiligen Stil-Objekt steht
  // dort aber nur `style={{`, und die Zusage meldete es fälschlich als statisch. *Ein Fenster
  // statt einer Zeile: der Stil endet spätestens sechs Zeilen weiter.*
  for (const [z, i] of treffer) {
    const block = alle.slice(i, i + 7).join(' ');
    assert.ok(/\$\{|\?|FARBEN\.|T\.|selected|a\?\.|GESPERRT_/.test(block),
      `eine verbliebene Inline-Stelle sieht statisch aus und gehörte umgestellt:\n    ${block.trim().slice(0, 140)}`);
  }
});
