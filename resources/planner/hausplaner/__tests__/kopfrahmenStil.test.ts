/**
 * AUF-38-P4+P5 — **die Stilschicht des Kopfrahmens, verriegelt. Und damit ist das Programm zu Ende.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR dieser Datei — 8 Mutationen, FÜNF kamen durch:**
 *
 * ```text
 * blind (5)  Klassenname mit Tippfehler · Werkzeugzeile und Bedienzeile getauscht
 *            Regel ohne Wirkung · Regel umbenannt · Fueller fuellt nicht mehr
 * gefangen   Gruppenabstand 6 -> 2 (1 rot) · Rohfarbe statt Token (4 rot)
 *            Massband verliert `overflow: hidden` (2 rot)
 * ```
 *
 * ***Fünf statt sieben — zum ersten Mal in diesem Programm ist die Zahl gefallen.*** Der Grund ist
 * messbar und kein Zufall: **drei geerbte Zusagen wurden mit über die Brücke gezogen**, statt sie
 * auf den Inline-Stil zeigen zu lassen — `buehnenHoehe` (zweimal) und `eineWerkzeugzeile` (K13).
 * Sie prüfen jetzt Klasse UND Regel und fangen genau die drei Mutationen, die sonst blind wären.
 *
 * *Das ist die Lehre aus F-15, einmal angewandt statt nur beschrieben: **wer eine Wahrheit
 * verschiebt, zieht die Zusage mit — dann braucht der Brücken-Test nur noch den Rest.***
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { teil } from './_zerlegteApp';
import { messeDatei } from '../../../../scripts/statische-inline-stile.mjs';

const hier = dirname(fileURLToPath(import.meta.url));

const quelle = teil('app/dashboard/Kopfrahmen.tsx');
const app = teil('app/HausplanerApp.tsx');
const css = teil('hausplaner.css');

/** Die `hp-kr-`-Klassen, die der Kopfrahmen wirklich an ein Element schreibt. */
function benutzteKlassen(): string[] {
  const raus = new Set<string>();
  for (const m of quelle.matchAll(/className="([^"]+)"/g)) {
    for (const k of m[1]!.split(/\s+/)) if (k.startsWith('hp-kr-')) raus.add(k);
  }

  return [...raus].sort();
}

function regel(klasse: string): string | null {
  const m = css.match(new RegExp(`\\.${klasse.replace(/-/g, '\\-')}\\s*\\{([^}]*)\\}`));

  return m ? m[1]!.trim() : null;
}

// --- Die Brücke: benutzt ⇔ definiert ---------------------------------------------------------------

test('K-03: jede benutzte `hp-kr-`-Klasse ist in der Stilschicht definiert', () => {
  const benutzt = benutzteKlassen();
  assert.equal(benutzt.length, 9, `${benutzt.length} Klassen benutzt statt 9`);
  for (const k of benutzt) {
    assert.ok(regel(k) !== null, `\`.${k}\` wird benutzt, steht aber in keiner Regel — das Element bleibt ungestylt`);
  }
});

test('K-03: keine `hp-kr-`-Regel ohne Nutzer', () => {
  const definiert = [...css.matchAll(/^\.(hp-kr-[a-z0-9-]+)\s*\{/gm)].map((m) => m[1]!);
  assert.equal(definiert.length, 9, `${definiert.length} Regeln statt 9`);
  const benutzt = new Set(benutzteKlassen());
  for (const k of definiert) {
    assert.ok(benutzt.has(k), `\`.${k}\` ist definiert, wird aber nirgends benutzt`);
  }
});

// --- Jede Klasse trägt, was sie ersetzt hat ---------------------------------------------------------

const ERSETZT: ReadonlyArray<readonly [string, readonly string[]]> = [
  ['hp-kr-trenner', ['width: 1px', 'height: 20px', 'var(--hp-hair)', 'margin: 0 4px']],
  ['hp-kr-gruppe', ['display: flex', 'align-items: center', 'gap: 6px']],
  ['hp-kr-werkzeugzeile', ['display: flex', 'gap: 8px', 'padding: 8px 14px', 'var(--hp-surface)', 'var(--hp-hair)']],
  ['hp-kr-marke', ['display: flex', 'gap: 9px', 'margin-right: 8px']],
  ['hp-kr-markenname', ['font-size: 14px']],
  ['hp-kr-fueller', ['flex: 1']],
  ['hp-kr-objektzeile', ['flex: 0 0 auto', 'align-items: baseline', 'gap: 10px', 'padding: 5px 14px 0', 'var(--hp-bg)']],
  ['hp-kr-objektzeile-inhalt', ['flex: 1', 'min-width: 0']],
  ['hp-kr-bedienzeile', ['display: flex', 'gap: 6px', 'padding: 6px 14px', 'var(--hp-bg)', 'var(--hp-hair)', 'flex: 0 0 auto']],
];

test('K-03: jede Klasse trägt die Eigenschaften, die ihr Inline-Stil trug', () => {
  assert.equal(ERSETZT.length, 9, 'die Tabelle deckt nicht mehr alle neun umgestellten Stellen ab');
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

test('K-03: die drei Zeilen des Kopfrahmens sind NICHT vertauscht', () => {
  // Mutation „Werkzeugzeile und Bedienzeile getauscht" kam durch. Beide sind flex-Zeilen mit
  // Abstand und Unterkante — vertauscht sieht der Quelltext vollstaendig aus, und im Browser
  // bekaeme die obere Zeile den Hintergrund der unteren und ein anderes Innenmass.
  const werkzeug = quelle.indexOf('className="hp-kr-werkzeugzeile"');
  const objekt = quelle.indexOf('className="hp-kr-objektzeile"');
  const bedien = quelle.indexOf('className="hp-kr-bedienzeile"');
  assert.ok(werkzeug > 0 && objekt > 0 && bedien > 0, 'eine der drei Zeilen traegt ihre Klasse nicht');
  assert.ok(werkzeug < objekt && objekt < bedien,
    'die drei Zeilen des Kopfrahmens stehen nicht mehr in ihrer Reihenfolge');
  for (const k of ['hp-kr-werkzeugzeile', 'hp-kr-objektzeile', 'hp-kr-bedienzeile']) {
    assert.equal([...quelle.matchAll(new RegExp(`className="${k}"`, 'g'))].length, 1,
      `\`${k}\` steht mehr als einmal — eine stille Dublette`);
  }
});

test('K-03: der Füller schiebt die rechte Seite wirklich nach rechts', () => {
  // Mutation „Fueller fuellt nicht mehr" kam durch: `flex: 0` laesst die ganze rechte Haelfte der
  // Werkzeugzeile nach links rutschen. Im Quelltext sieht das nach nichts aus.
  assert.match(quelle, /<span className="hp-kr-fueller" \/>/, 'der Füller ist fort');
  assert.match(regel('hp-kr-fueller') ?? '', /flex: 1\b/, 'der Füller füllt nicht mehr');
});

test('K-03: der Markenname sitzt IN der Marke, nicht daneben', () => {
  const marke = quelle.indexOf('className="hp-kr-marke"');
  const name = quelle.indexOf('className="hp-kr-markenname"');
  assert.ok(marke > 0 && name > marke, 'der Markenname steht nicht mehr innerhalb der Marke');
  // Der gedaempfte Zusatz daneben bleibt inline — er traegt `FARBEN.gedaempft` und ist damit
  // ausserhalb dieses Blattes.
  assert.match(quelle, /· Solar Aspekt/, 'der Zusatz der Marke ist verschwunden');
});

// --- P5: die eine Stelle der Hauptansicht -----------------------------------------------------------

test('K-03 (P5): das Massband trägt seine Klasse und die Regel trägt seine Werte', () => {
  // **Kein achtes Praefix:** die Stelle sitzt auf der Studio-Schale, deren Familie `hp-studio-`
  // es schon gibt. Der Name ist der, den der Kommentar an dieser Stelle selbst benutzt (AUF-72).
  assert.match(app, /<div ref=\{inhaltRef\} className="hp-studio-massband">/,
    'die gemessene Reihe traegt ihre Klasse nicht mehr');
  const r = regel('hp-studio-massband');
  assert.ok(r, 'die Regel des Massbands fehlt');
  for (const wert of ['flex: 1', 'position: relative', 'overflow: hidden', 'display: flex']) {
    assert.ok(r.includes(wert), `dem Massband fehlt \`${wert}\``);
  }
});

// --- Die Grenze: das Programm ist zu Ende -----------------------------------------------------------

test('K-01: in beiden Dateien bleibt keine offene statische Inline-Stelle', () => {
  // **Gemessen mit DEMSELBEN Werkzeug, das die Grundgesamtheit zaehlt.** Mein erster Entwurf trug
  // hier eine eigene Muster-Liste (`FARBEN.|T.|istSchmal|…`) — und sie fiel sofort ueber
  // `statusPill.farbe`, einen dynamischen Wert, den sie nicht kannte. *Das war ein zweiter
  // Massstab neben dem Skript, also genau der Fehler, den PB-010 in `stilschicht.test.ts`
  // gerade beseitigt hat.* Zwei Massstaebe fuer dieselbe Sache sind der Fehler, nicht die Loesung.
  const kr = messeDatei(join(hier, '../app/dashboard/Kopfrahmen.tsx'));
  const ha = messeDatei(join(hier, '../app/HausplanerApp.tsx'));
  assert.deepEqual(kr.offen, [], `offene statische Stellen im Kopfrahmen: Z${kr.offen.join(', Z')}`);
  assert.deepEqual(ha.offen, [], `offene statische Stellen in der Hauptansicht: Z${ha.offen.join(', Z')}`);
  // presence-Partner nach R2: es gibt ueberhaupt noch Inline-Stellen — die Zusage misst nicht Leere.
  assert.equal(kr.gesamt, 7, `${kr.gesamt} Inline-Stellen im Kopfrahmen statt 7`);
  assert.equal(ha.gesamt, 3, `${ha.gesamt} Inline-Stellen in der Hauptansicht statt 3`);
});

test('K-03 (Grenze): `hp-az-` und `hp-ok-` sind unberührt', () => {
  for (const k of ['hp-az-suchen', 'hp-ok-name']) {
    assert.ok(quelle.includes(k), `\`${k}\` ist aus dem Kopfrahmen verschwunden`);
    assert.ok(regel(k) !== null, `\`.${k}\` ist aus der Stilschicht verschwunden`);
  }
});
