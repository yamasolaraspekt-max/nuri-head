/**
 * AUF-83-T3 / K-05 — **das gesperrte Merkmal der geteilten Leiste, und was es NICHT anfasst.**
 *
 * `ReiterLeiste` hat seit AUF-27 **drei** Nutzer: der Panel-Reiter im Eigenschaften-Bereich, der
 * Schienen-Reiter links, und die Arbeitsbereiche oben. K-05 braucht nur den dritten — *„Import ist
 * ausgegraut und sagt das auch"*.
 *
 * **Die Auflage des Blattes ist der Grund für diese Datei:** *„Eine Zusage belegt, dass die beiden
 * anderen Nutzer unverändert sind — nicht die Behauptung, sie seien es."* Genau das steht hier.
 *
 * **Warum das Merkmal ein Satz ist und kein `true`:** ein Merker sagt *dass*, der Satz sagt
 * *warum*. Das halbe Kriterium lautet „sagt das auch" — ein blasser Reiter ohne Begründung ist
 * eine Sackgasse mit Beschriftung.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { GESPERRT_DECKKRAFT, GESPERRT_BESCHRIFTUNG } from '../app/dashboard/gesperrtStil';
import { ARBEITSBEREICHE } from '../app/dashboard/arbeitsbereiche';
// AUF-48: die Hauptansicht ist zerlegt — diese Zusage liest ALLE ihre Teile.
import { zerlegteApp } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const leiste = ohneKommentare(readFileSync(join(hier, '../app/dashboard/ReiterLeiste.tsx'), 'utf8'));
const app = ohneKommentare(zerlegteApp());

// --- Das Merkmal existiert und ist optional -------------------------------------------------------

test('K-05: die Leiste kennt ein Merkmal für „trägt noch nicht"', () => {
  assert.match(leiste, /nochNicht\?: string/, 'kein optionales Merkmal im Vertrag der Leiste');
});

test('K-05 (Auflage): das Merkmal ist OPTIONAL — wer es nicht setzt, bekommt das heutige Verhalten', () => {
  // **Die Wirkung, nicht die Gestalt.** Ein `nochNicht: string` (ohne `?`) würde jeden der drei
  // Nutzer zwingen, es zu setzen — der Vertrag der geteilten Komponente wäre gebrochen, und die
  // beiden anderen müssten angefasst werden. Das Fragezeichen IST die Auflage.
  assert.doesNotMatch(leiste, /nochNicht: string/, 'das Merkmal ist zur Pflicht geworden');
  // Und jede Wirkung hängt an seiner Anwesenheit, nicht an seinem Fehlen:
  for (const stelle of ['opacity: tab.nochNicht ?', 'tab.nochNicht &&', 'tab.nochNicht ?']) {
    assert.ok(leiste.includes(stelle), `die Wirkung ist nicht an \`nochNicht\` gebunden: ${stelle}`);
  }
});

test('K-05 (Auflage): die beiden anderen Nutzer setzen es NICHT — sie sind unberührt', () => {
  // **Der eigentliche Beleg der Auflage.** Nicht „ich habe sie nicht angefasst", sondern: die
  // DATENQUELLEN der beiden anderen tragen das Merkmal nicht. Dort entstünde es, nicht im JSX.
  //
  // **Zweite Fassung — die erste prüfte nichts.** Sie schnitt bei `indexOf('const panelReiter')`,
  // und diesen Bezeichner gibt es gar nicht: `indexOf` gab `-1`, der Ausschnitt war leer, die
  // Zusage grün. `SCHIENEN_REITER` traf die Import-Zeile statt der Aufrufstelle.
  // **Dritte Ausprägung derselben Klasse bei mir** (nach `hp-ef-wert` ⊂ `hp-ef-wertzeile` und
  // `data-schiene` ⊂ `data-schienex`): *ein Anker, den niemand gegengeprüft hat, misst Leere.*
  // Deshalb steht unten zuerst, dass es die drei Stellen überhaupt gibt.
  // **AUF-48 Scheibe 4a: sortiert statt in Reihenfolge.** Der Bereich-Waehler ist in den
  // Kopfrahmen gezogen; `app` ist seither die Verkettung zweier Dateien, und die Textreihenfolge
  // waere ein Artefakt dieser Verkettung, keine Aussage ueber den Code. **Die Zusage selbst
  // bleibt so scharf wie vorher:** es sind GENAU diese drei Nutzer — einer mehr oder weniger
  // faellt weiterhin auf.
  const stellen = [...app.matchAll(/reiter=\{(\w+)\}/g)].map((m) => m[1]).sort();
  assert.deepEqual(stellen, ['PANEL_TABS', 'SCHIENEN_REITER', 'bereichReiter'],
    'die drei Nutzer der geteilten Leiste sind nicht mehr die erwarteten');

  // Beide anderen führen ihre Reiter in einer eigenen Datei — dort entstünde das Merkmal.
  // **Der dritte tote Anker in Folge fiel hier auf, und zwar durch die Zeile, die ihn prüft:**
  // mein `const PANEL_TABS` gab es nicht, `PANEL_TABS` wird importiert. *Der Unterschied zu den
  // ersten beiden Malen ist nur, dass diesmal jemand danach gefragt hat.*
  for (const [name, pfad] of [
    ['Schienen-Reiter', '../app/dashboard/schienenReiter.ts'],
    ['Panel-Reiter', '../app/dashboard/panelTabs.ts'],
  ] as const) {
    const quelle = readFileSync(join(hier, pfad), 'utf8');
    assert.ok(quelle.length > 0, `${name}: Quelle leer — der Pfad stimmt nicht`);
    assert.ok(!quelle.includes('nochNicht'), `${name} setzt das Merkmal — er war nicht gemeint`);
  }
});

// --- Was das Merkmal bewirkt ----------------------------------------------------------------------

test('K-05: „ausgegraut" liest aus der EINEN Quelle, statt eine sechste Meinung zu erfinden', () => {
  // Genau dieser Wert stand vor AUF-71 an fünf Stellen verschieden: 0,6 · 0,4 · 0,45. Eine
  // abgetippte Zahl hier wäre die sechste.
  assert.match(leiste, /import \{[^}]*GESPERRT_DECKKRAFT[^}]*\} from '\.\/gesperrtStil'/,
    'die Deckkraft kommt nicht aus `gesperrtStil.ts`');
  assert.match(leiste, /opacity: tab\.nochNicht \? GESPERRT_DECKKRAFT/, 'eigene Deckkraft statt der Quelle');
  // presence-Partner nach R2: die Quelle liefert überhaupt einen Wert.
  assert.equal(typeof GESPERRT_DECKKRAFT, 'number');
  assert.ok(GESPERRT_DECKKRAFT > 0 && GESPERRT_DECKKRAFT < 1, 'eine Deckkraft, die nichts abschwächt, sagt nichts');
  assert.equal(typeof GESPERRT_BESCHRIFTUNG, 'string');
});

test('K-05: der Reiter SAGT es — Blässe allein ist keine Auskunft', () => {
  // WCAG 1.4.1: eine Aussage darf nicht nur an der Farbe hängen. Ohne diesen Zusatz wäre der
  // Reiter blass und sonst nichts — und ein blasser Reiter sieht aus wie ein Darstellungsfehler.
  assert.match(leiste, /noch nicht<\/span>/, 'kein sichtbarer Zusatz am ausgegrauten Reiter');
  assert.match(leiste, /title=\{tab\.nochNicht \?/, 'der GRUND steht nicht im Tooltip');
  assert.match(leiste, /\$\{tab\.label\} — \$\{tab\.nochNicht\}/, 'der Tooltip nennt den Grund nicht');
});

test('K-05: das Merkmal SPERRT nicht — die Bedienung bleibt vollständig', () => {
  // **Die Grenze, und sie ist nicht kosmetisch.** Ein `disabled` an diesem Knopf risse ein Loch in
  // die Pfeiltasten-Navigation: `onKeyDown` wandert über den Index, und ein übersprungener Reiter
  // wäre für die Tastatur unerreichbar. „Ausgegraut" ist eine Aussage über den INHALT.
  const knopf = leiste.slice(leiste.indexOf('<button'), leiste.indexOf('</button>'));
  assert.ok(!/disabled/.test(knopf), 'der Reiter ist gesperrt worden — beauftragt war ausgegraut');
  assert.match(knopf, /onClick=\{\(\) => setAktiv\(tab\.id\)\}/, 'der Reiter ist nicht mehr anwählbar');
  assert.match(knopf, /tabIndex=\{aktivT \? 0 : -1\}/, 'die Tastaturbedienung hat sich geändert');
});

// --- K-05, zweite Hälfte: GENAU Import sagt es --------------------------------------------------

test('K-05: Import trägt den Grund — und zwar in den DATEN', () => {
  // **Die Vorgänger-Fassung dieser Zeile hielt fest, dass NIEMAND das Merkmal setzt** — solange
  // `arbeitsbereiche.ts` nicht in `pfade` stand. Sie ist um 06:45 rot geworden, weil der Planner
  // die Datei freigegeben hat, und damit hat sie genau getan, wofür sie da war.
  assert.match(ARBEITSBEREICHE[0].id, /import/i, 'Import ist nicht mehr der erste Bereich');
  const grund = ARBEITSBEREICHE[0].nochNicht;
  assert.ok(grund && grund.length > 20, 'Import trägt keinen Grund — „ausgegraut" ohne Auskunft');
});

test('K-05: und GENAU Import — die anderen vier sind unberührt', () => {
  // **Die Wirkung, nicht die Gestalt.** Ohne diese Zusage wäre auch ein Blatt grün, auf dem alle
  // fünf ausgegraut sind: das Kriterium nennt Import, nicht „mindestens Import".
  const mitGrund = ARBEITSBEREICHE.filter((b) => b.nochNicht).map((b) => b.id);
  assert.equal(mitGrund.length, 1, `${mitGrund.length} Bereiche ausgegraut — genau einer war gemeint: ${mitGrund.join(', ')}`);
  assert.equal(ARBEITSBEREICHE.length, 5, 'es sind nicht mehr fünf Bereiche');
});

test('K-05: `HausplanerApp` REICHT den Grund durch, statt ihn zu setzen', () => {
  // **Die Grenze aus dem Entscheid vom 06:45:** ein Literal an der Bildschirm-Ebene wäre die
  // zweite Wahrheit darüber, was ein Bereich kann. Durchreichen ist keins.
  assert.match(app, /nochNicht: b\.nochNicht/, 'der Grund wird nicht aus den Daten durchgereicht');
  const literale = [...app.matchAll(/nochNicht: *['"`]/g)];
  assert.deepEqual(literale.map((m) => m[0]), [],
    'in HausplanerApp steht ein fester Grund — dort wäre er die zweite Wahrheit');
});
