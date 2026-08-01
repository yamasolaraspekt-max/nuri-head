/**
 * AUF-38-P3 / K-03 — **die Stilschicht von Statusleiste, Befehlspalette und Engine-Fläche.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR dieser Datei — acht Mutationen an genau dem, was P3 anfasst:**
 *
 * ```text
 * blind (7)  Klassenname mit Tippfehler · Klasse am falschen Element · Regel ohne Wirkung
 *            Regel umbenannt · Abstand still veraendert · flex-Verhalten gedreht
 *            Kappung entfernt
 * gefangen   nur "Rohfarbe statt Token" (4 rot)
 * ```
 *
 * ***Sieben von acht — dieselbe Zahl wie in P1 und in P2.*** **Zum dritten Mal identisch.** Das
 * ist kein Zufall dreier Dateien, sondern die Eigenschaft der Umstellung selbst, und sie steht
 * inzwischen als **F-15** im Register: *ein Inline-Stil wird von den Bauteil-Zusagen mitgelesen;
 * eine Klasse verlagert die sichtbare Wahrheit in eine zweite Datei, und zwischen beiden liegt
 * nichts, wenn niemand die Brücke prüft.*
 *
 * **Die einzige gefangene Mutation ist die, die schon eine Zusage hatte** (`stilschicht.test.ts`
 * verbietet Rohfarben). Alles, was die Umstellung NEU einführt — Klassennamen, Zuordnung, Inhalt
 * der Regeln — war ungedeckt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { teil } from './_zerlegteApp';

const quelle = teil('app/rahmen/FussUndUeberlagerungen.tsx');
const css = teil('hausplaner.css');

/** Die `hp-fu-`-Klassen, die die Datei wirklich an ein Element schreibt. */
function benutzteKlassen(): string[] {
  const raus = new Set<string>();
  for (const m of quelle.matchAll(/className="([^"]+)"/g)) {
    for (const k of m[1]!.split(/\s+/)) if (k.startsWith('hp-fu-')) raus.add(k);
  }

  return [...raus].sort();
}

/** Der Regelrumpf einer Klasse, oder `null`. */
function regel(klasse: string): string | null {
  const m = css.match(new RegExp(`\\.${klasse.replace(/-/g, '\\-')}\\s*\\{([^}]*)\\}`));

  return m ? m[1]!.trim() : null;
}

// --- Die Brücke: benutzt ⇔ definiert ---------------------------------------------------------------

test('K-03: jede benutzte `hp-fu-`-Klasse ist in der Stilschicht definiert', () => {
  // Die Mutationen „Klassenname mit Tippfehler" und „Regel umbenannt" kamen beide durch.
  // Ein ungestyltes Element sieht im Testlauf aus wie ein gestyltes — nur im Browser nicht.
  const benutzt = benutzteKlassen();
  // **13, nicht mehr 12:** Z-03 hat `hp-fu-fang` ergaenzt — die Fangart in der Fussflaeche.
  // *Die Zahl waechst hier BEWUSST mit; sie haelt fest, dass keine Klasse still verschwindet,
  // nicht dass nie eine dazukommt.* Wer eine ergaenzt, zieht sie mit und sagt es im Commit.
  assert.equal(benutzt.length, 13, `${benutzt.length} Klassen benutzt statt 13 — die Umstellung ist unvollstaendig oder ausgeufert`);
  for (const k of benutzt) {
    assert.ok(regel(k) !== null, `\`.${k}\` wird benutzt, steht aber in keiner Regel — das Element bleibt ungestylt`);
  }
});

test('K-03: keine `hp-fu-`-Regel ohne Nutzer', () => {
  const definiert = [...css.matchAll(/^\.(hp-fu-[a-z0-9-]+)\s*\{/gm)].map((m) => m[1]!);
  assert.equal(definiert.length, 13, `${definiert.length} Regeln gefunden statt 13`);
  const benutzt = new Set(benutzteKlassen());
  for (const k of definiert) {
    assert.ok(benutzt.has(k), `\`.${k}\` ist definiert, wird aber nirgends benutzt`);
  }
});

// --- Jede Klasse trägt, was sie ersetzt hat ---------------------------------------------------------

/**
 * **Die Eigenschaften aus dem Stand VOR dem Umbau**, Zeile für Zeile abgelesen.
 *
 * *Ohne diese Tabelle wäre „die Klasse existiert" die ganze Aussage — und eine leere Regel erfüllt
 * sie. Genau diese Mutation kam durch.*
 *
 * **Einheiten mitgeprüft:** React schrieb `padding: 6` und meinte 6px. Eine einheitenlose Zahl in
 * CSS ist keine Länge und wird ignoriert — der Abstand wäre still verschwunden.
 */
const ERSETZT: ReadonlyArray<readonly [string, readonly string[]]> = [
  ['hp-fu-fueller', ['flex: 1']],
  ['hp-fu-befehlshinweis', ['var(--hp-muted)']],
  ['hp-fu-palette-flaeche', ['position: fixed', 'inset: 0', 'z-index: 60', 'var(--hp-canvas-wall-ghost)', 'align-items: flex-start', 'padding-top: 12vh']],
  ['hp-fu-palette-kasten', ['width: 460px', 'max-width: 92vw', 'var(--hp-surface)', 'var(--hp-control-border)', 'border-radius: 12px', 'overflow: hidden']],
  ['hp-fu-palette-liste', ['max-height: 46vh', 'overflow-y: auto', 'padding: 6px']],
  ['hp-fu-palette-leer', ['padding: 10px']],
  ['hp-fu-gruppe-leer', ['font-size: 12.5px', 'var(--hp-muted)', 'padding: 3px 0']],
  ['hp-fu-gruppenkopf', ['font-size: 10.5px', 'font-weight: 700', 'letter-spacing: .07em', 'text-transform: uppercase', 'var(--hp-faint)', 'padding: 10px 10px 4px']],
  ['hp-fu-eintrag-label', ['flex: 0 0 auto', 'min-width: 74px']],
  ['hp-fu-eintrag-grund', ['flex: 1', 'font-size: 11px', 'var(--hp-warn-ink)']],
  ['hp-fu-eintrag-zusatz', ['flex: 1', 'font-size: 11px', 'var(--hp-faint)', 'overflow: hidden', 'text-overflow: ellipsis', 'white-space: nowrap']],
  ['hp-fu-eintrag-kuerzel', ['flex: 0 0 auto', 'font-size: 10.5px', 'var(--hp-muted)', 'var(--hp-control-border)', 'border-radius: 4px', 'padding: 1px 5px']],
];

test('K-03: jede Klasse trägt die Eigenschaften, die ihr Inline-Stil trug', () => {
  assert.equal(ERSETZT.length, 12, 'die Tabelle deckt nicht mehr alle zwölf umgestellten Stellen ab');
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

test('K-03: die drei Teile eines Paletten-Eintrags sind NICHT vertauscht', () => {
  // Mutation „Klasse am falschen Element" kam durch. Label und Kuerzel sind beide `flex: 0 0 auto`
  // — vertauscht sieht die Zeile im Testlauf gleich aus und im Browser falsch: das Kuerzel stuende
  // links mit 74 px Mindestbreite, die Beschriftung rechts in einem Kasten.
  const label = quelle.indexOf('className="hp-fu-eintrag-label">{eintrag.label}');
  const grund = quelle.indexOf('className="hp-fu-eintrag-grund">{eintrag.grund}');
  const zusatz = quelle.indexOf('className="hp-fu-eintrag-zusatz">{eintrag.zusatz');
  const kuerzel = quelle.indexOf('className="hp-fu-eintrag-kuerzel">{eintrag.shortcut}');
  for (const [name, i] of [['label', label], ['grund', grund], ['zusatz', zusatz], ['kuerzel', kuerzel]] as const) {
    assert.ok(i > 0, `\`hp-fu-eintrag-${name}\` sitzt nicht mehr an seinem eigenen Inhalt`);
  }
  assert.ok(label < grund && grund < zusatz && zusatz < kuerzel,
    'die vier Teile des Eintrags stehen nicht mehr in ihrer Reihenfolge');
});

test('K-03: die Palettenfläche und ihr Kasten sind NICHT vertauscht', () => {
  // Die Flaeche ist das Overlay ueber dem ganzen Bildschirm, der Kasten der Dialog darin.
  // Vertauscht laege ein 460 px breiter Kasten fest ueber der Seite und der Dialog fuellte alles.
  const flaeche = quelle.indexOf('className="hp-fu-palette-flaeche"');
  const kasten = quelle.indexOf('className="hp-fu-palette-kasten"');
  assert.ok(flaeche > 0 && kasten > 0, 'eine der beiden Klassen fehlt');
  assert.ok(flaeche < kasten, 'der Kasten steht vor der Fläche — die beiden sind vertauscht');
  // Und der Dialog ist der Kasten, nicht die Fläche: die Rolle sitzt am inneren Element.
  const nahKasten = quelle.slice(Math.max(0, kasten - 260), kasten);
  assert.match(nahKasten, /role="dialog"/, 'die Dialog-Rolle sitzt nicht mehr am Kasten');
});

test('K-03: der Füller trennt Statusleiste und Ablehnung weiterhin', () => {
  // `flex: 1` auf einem leeren Element ist der Abstandhalter, der die Ablehnung nach rechts
  // schiebt. Faellt er weg, ruecken alle Hinweise zusammen und die Warnung geht in der Reihe unter.
  assert.match(quelle, /<span className="hp-fu-fueller" \/>/, 'der Füller der Statusleiste ist fort');
  assert.equal([...quelle.matchAll(/hp-fu-fueller/g)].length, 1, 'der Füller steht mehr als einmal');
});

// --- Die Grenze des Auftrags ------------------------------------------------------------------------

test('K-01: in der Datei bleibt keine offene statische Inline-Stelle', () => {
  const vorkommen = [...quelle.matchAll(/style=\{\{/g)].length;
  assert.equal(vorkommen, 8, `${vorkommen} Inline-Vorkommen statt 8`);
  // presence-Partner nach R2: die verbliebenen acht sind DYNAMISCH und damit ausserhalb des
  // Auftrags. Ohne diese Haelfte waere die Null auch erreichbar, indem man Stellen dynamisch
  // macht statt sie umzustellen — genau der Gegen-Beweis, den das Blatt verlangt.
  const zeilen = quelle.split('\n');
  for (let i = 0; i < zeilen.length; i++) {
    if (!zeilen[i]!.includes('style={{')) continue;
    const block = zeilen.slice(i, i + 6).join(' ');
    assert.ok(/\$\{|\?|FARBEN\.|T\.|istSchmal|zustand\./.test(block),
      `eine verbliebene Inline-Stelle sieht statisch aus und gehörte umgestellt:\n    ${block.trim().slice(0, 130)}`);
  }
});

test('K-03 (Grenze): `hp-kontur-` und `hp-pause-` sind unberührt', () => {
  // Ausschluss des Blattes: sie liegen bereits in der Stilschicht. Wer sie umbenennt, bewegt
  // Stellen, die dieses Blatt nicht misst.
  for (const k of ['hp-kontur-hinweis', 'hp-pause-hinweis']) {
    assert.ok(quelle.includes(k), `\`${k}\` ist aus der Datei verschwunden`);
    assert.ok(regel(k) !== null, `\`.${k}\` ist aus der Stilschicht verschwunden`);
  }
});
