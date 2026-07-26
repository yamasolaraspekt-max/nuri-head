/**
 * AUF-68 — die drei Gruppenwörter aus der Bedienleiste.
 *
 * **Yamas Wunsch, wörtlich:** *„kannst du die Wörter Ansicht, Messen & Export sowie Bearbeiten weg
 * machen"* — dazu die frühere Ansage aus derselben Zeile: *„die können auch als Icon dienen, nicht
 * mit Worten ausgeschrieben — dafür haben wir Tooltip."*
 *
 * **Warum das gefahrlos geht:** Die Trennstriche (`opSep`) liegen bereits zwischen den Gruppen —
 * für das Auge trägt also die Trennung, was vorher die Schrift trug.
 *
 * **Die Bedingung, unter der das Wort gehen darf:** Wer die Zeile mit einem Vorleseprogramm
 * bedient, hätte die Gruppen sonst ersatzlos verloren — Trennstriche sind für ihn nicht da.
 * Deshalb bleibt der Name als `aria-label` an einer `role="group"`-Umhüllung. Das ist keine Zutat,
 * das ist der Preis.
 *
 * **Das eigentliche Risiko dieses Postens** ist nicht das Entfernen, sondern der Beifang: ein Knopf
 * weniger, eine Sperre verschoben, eine Reihenfolge vertauscht. Deshalb prüft dieser Test zuerst,
 * dass sich an der Zeile **außer den Wörtern nichts** geändert hat.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const quelle = readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8');
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');

/** Nur die Bedien-Werkzeugleiste — nicht die Themenzeile darunter, die andere Wörter trägt. */
function leiste(): string {
  const q = ohneKommentare(quelle);
  const start = q.indexOf('<OpGruppe name="Ansicht">');
  const ende = q.indexOf('Zoom {(zoom * 100)', start);
  assert.ok(start > 0 && ende > start, 'die Bedienleiste wurde nicht gefunden');
  return q.slice(start, ende);
}

// --- Die Wörter sind weg ------------------------------------------------------------------------
test('K3: `opLbl` ist restlos verschwunden — Aufrufe UND Helfer', () => {
  assert.equal((quelle.match(/opLbl/g) ?? []).length, 0);
  // Nicht auskommentiert und nicht auf '' gesetzt — ein toter Helfer ist genau die Sorte Rest,
  // die später jemand für Absicht hält.
  assert.doesNotMatch(quelle, /textTransform: 'uppercase', color: T\.muted, marginRight: 2/);
});

test('die drei ausgeschriebenen Wörter stehen nicht mehr als sichtbarer Text in der Leiste', () => {
  const l = leiste();
  for (const wort of ['Ansicht', 'Bearbeiten', 'Messen & Export']) {
    // Sie dürfen NUR noch als aria-label vorkommen, nicht als freier Text.
    const alsLabel = new RegExp(`<OpGruppe name="${wort.replace('&', '&')}">`);
    assert.match(l, alsLabel, `${wort}: die Gruppe fehlt`);
  }
  assert.doesNotMatch(l, /\{opLbl\(/);
});

// --- K6: der Name überlebt unsichtbar -----------------------------------------------------------
test('K6: alle drei Gruppen tragen `role="group"` UND ein nichtleeres `aria-label`', () => {
  const q = ohneKommentare(quelle);
  const huelle = q.match(/const OpGruppe = [\s\S]*?\);/);
  assert.ok(huelle, 'die Umhüllung fehlt');
  assert.match(huelle[0], /role="group"/, 'ohne die Rolle ist es keine Gruppe');
  assert.match(huelle[0], /aria-label=\{name\}/, 'ohne den Namen ist die Gruppe stumm');

  const namen = [...leiste().matchAll(/<OpGruppe name="([^"]*)">/g)].map((m) => m[1]);
  assert.deepEqual(namen, ['Ansicht', 'Bearbeiten', 'Messen & Export'], 'Namen oder Reihenfolge geändert');
  for (const n of namen) {
    assert.ok(n.trim().length > 0, 'ein leeres aria-label ist so gut wie keines');
  }
});

// --- K4/K5: kein Beifang ------------------------------------------------------------------------
test('K4: die Knopfzahl der Leiste ist unverändert — elf', () => {
  // Gemessen 25.07. im Browser: 11 Knöpfe. Ändert sich diese Zahl, ist ein Knopf verschwunden
  // oder dazugekommen — beides war nicht beauftragt.
  assert.equal((leiste().match(/<OpBtn /g) ?? []).length, 11);
});

test('K4: sechs · vier · einer — die Aufteilung auf die drei Gruppen', () => {
  const l = leiste();
  const teile = l.split(/<OpGruppe name="[^"]*">/).slice(1);
  const zahlen = teile.map((t) => (t.split('</OpGruppe>')[0].match(/<OpBtn /g) ?? []).length);
  assert.deepEqual(zahlen, [6, 4, 1], 'ein Knopf ist in eine andere Gruppe gerutscht');
});

test('K5: keine Sperre geändert — dieselben Bedingungen an denselben Knöpfen', () => {
  const l = leiste();
  const gesperrt = [...l.matchAll(/icon="([^"]+)"[^/]*?(disabled=\{([^}]*)\}|geplant)/g)]
    .map((m) => `${m[1]}:${m[3] ?? 'geplant'}`);
  assert.deepEqual(gesperrt, [
    // AUF-62: `einpassen` ist aus dieser Liste verschwunden, weil der Knopf seine Funktion bekommen
    // hat — nicht, weil eine Sperre gelöst wurde. Die vier übrigen sind unverändert.
    'dup:selectedNodeIds.length === 0',
    'del:selectedNodeIds.length === 0',
    'mirror-h:waende.length === 0',
    'mirror-v:waende.length === 0',
  ], 'eine Sperre ist verschoben — das war der Fehler, den dieser Test verhindern soll');
});

test('die Reihenfolge der Knöpfe ist Zeichen für Zeichen dieselbe', () => {
  const icons = [...leiste().matchAll(/icon="([^"]+)"/g)].map((m) => m[1]);
  assert.deepEqual(icons, [
    'zoom-in', 'zoom-out', 'zoom-reset', 'einpassen', 'grid', 'fang',
    'dup', 'del', 'mirror-h', 'mirror-v',
    'export',
  ]);
});

test('die Trennstriche stehen weiterhin zwischen den Gruppen — sie tragen jetzt die Gliederung', () => {
  const l = leiste();
  assert.equal((l.match(/\{opSep\(\)\}/g) ?? []).length, 2, 'zwei Trenner für drei Gruppen');
  // Und zwar AUSSERHALB der Gruppen: ein Trenner innerhalb wäre eine Gliederung in der Gliederung.
  for (const teil of l.split(/<OpGruppe name="[^"]*">/).slice(1)) {
    assert.doesNotMatch(teil.split('</OpGruppe>')[0], /\{opSep\(\)\}/);
  }
});

test('die Zoom-Anzeige bleibt rechts und außerhalb der Gruppen', () => {
  // Sie ist keine Bedienung, sondern eine Anzeige — in einer Bedien-Gruppe wäre sie falsch
  // angekündigt. `marginLeft: 'auto'` hält sie am rechten Rand.
  const q = ohneKommentare(quelle);
  const nachLetzterGruppe = q.slice(q.lastIndexOf('</OpGruppe>'));
  assert.match(nachLetzterGruppe, /marginLeft: 'auto'[\s\S]*?Zoom \{\(zoom \* 100\)/);
});

// --- Was NICHT angefasst wurde ------------------------------------------------------------------
test('die Themenzeile darunter ist unberührt — dort standen andere Wörter', () => {
  // Der Auftrag nennt sie ausdrücklich als Nicht-Ziel: `WerkzeugGruppenMenue` trägt eigene
  // Beschriftungen, die niemand weghaben wollte.
  const q = ohneKommentare(quelle);
  assert.match(q, /<WerkzeugGruppenMenue/, 'die Themenzeile ist verschwunden');
});
