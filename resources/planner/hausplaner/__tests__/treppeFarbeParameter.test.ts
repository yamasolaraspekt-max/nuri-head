/**
 * AUF-54 — **Farbe als Parameter statt in `geometry/`.**
 *
 * Yamas Entscheidung vom 25.07. Vorher führte `geometry/treppeSvg.ts` sechs rohe Farbwerte —
 * eine reine Geometrie-Datei, die über Aussehen entschied.
 *
 * **Dieser Posten verschiebt Herkunft, nicht Aussehen.** Genau das ist schwer zu beweisen und
 * leicht zu behaupten: eine Umstellung, die „ungefähr gleich" aussieht, hat den Auftrag verfehlt.
 * Deshalb steht hier ein **Byte-Vergleich** gegen den Stand **vor** dem Umbau: die Prüfsummen unten
 * stammen aus der Datei, wie sie in `d8038bf` lag — geholt mit `git show` und mit genau demselben
 * Aufruf gerendert.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { createHash } from 'node:crypto';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { treppenTyp, type TreppenTyp } from '../geometry/treppenTypen';
import { treppeAlsSvg } from '../geometry/treppeSvg';
import { T, TREPPE_FARBEN } from '../app/studioDaten';

const hier = dirname(fileURLToPath(import.meta.url));

/**
 * **Erzeugt vom Stand VOR dem Umbau** — die Datei aus `d8038bf` wurde per `git show` geholt und mit
 * genau dem Aufruf unten gerendert. Ändert sich hier ein Byte, hat sich das Bild geändert, und dann
 * ist es kein Herkunfts-Posten mehr.
 *
 * **Der erste Anlauf war wertlos, und das gehört hierher:** ich hatte die Eingabe
 * `geschosshoeheMm`/`laufbreiteMm` geschrieben — `TreppenTypEingabe` heißt `geschosshoehe` und
 * `laufbreite`. Die Felder liefen ins Leere, die Treppe war entartet, und die Prüfsummen verglichen
 * eine Zeichnung **ohne Trittstufen** mit sich selbst. *Ein Vergleich, der immer grün ist, prüft
 * nichts.* Aufgefallen ist es nur, weil eine zweite Zusage den Farbwert `stufe` im SVG suchte und
 * ihn nicht fand.
 */
const VORHER: Record<string, { laenge: number; sha256: string }> = {
  gerade: { laenge: 2961, sha256: '1cff8f06b4b3182bb52677a60a061f92882d9a078c8e6da87ef56a55d3c928de' },
  'l-podest': { laenge: 3028, sha256: '077db67750d06ac9cbb60111a70a5025c266d640648acd1d56181cb81bd7dc9c' },
  'u-podest': { laenge: 2920, sha256: 'b7af095e6c14aef3ba08bfaf993d3008c717e5ff437499310c947e666987583c' },
  spindel: { laenge: 3901, sha256: '95f8c0d867aadcb4be1f7fb51678996415362b7c737d9857767da9abb26554d3' },
};

const svgVon = (typ: TreppenTyp): string => {
  const r = treppenTyp({ typ, geschosshoehe: 2800, laufbreite: 1000, durchmesser: 1800 });
  return treppeAlsSvg(r.zeichnung, { farben: TREPPE_FARBEN, titel: `${typ} · Probe` });
};

// --- K2: wertgleich, Byte für Byte ---------------------------------------------------------------
for (const typ of ['gerade', 'l-podest', 'u-podest', 'spindel'] as const) {
  test(`K2: ${typ} rendert byte-genau wie vor dem Umbau`, () => {
    const svg = svgVon(typ);
    assert.equal(svg.length, VORHER[typ]!.laenge, 'die Länge allein wäre schon ein Unterschied');
    assert.equal(createHash('sha256').update(svg).digest('hex'), VORHER[typ]!.sha256,
      'ein einziges geändertes Zeichen fällt hier auf');
  });
}

test('K2: alle sechs Farbwerte stehen unverändert im erzeugten SVG', () => {
  // Der Prüfsummen-Vergleich oben würde auch dann grün bleiben, wenn zwei Farben getauscht wären
  // und sich die Summe zufällig träfe. Hier steht der Wert selbst.
  const svg = svgVon('gerade');
  for (const wert of Object.values(TREPPE_FARBEN)) {
    assert.ok(svg.includes(wert), `${wert} fehlt im SVG`);
  }
});

// --- K1: `geometry/` kennt keinen Farbwert mehr ---------------------------------------------------
test('K1: `treppeSvg.ts` enthält keinen einzigen rohen Farbwert', () => {
  const roh = readFileSync(join(hier, '../geometry/treppeSvg.ts'), 'utf8');
  const treffer = roh.match(/#[0-9a-fA-F]{3,8}\b/g) ?? [];
  assert.deepEqual(treffer, [], `noch vorhanden: ${treffer.join(', ')}`);
});

test('K1: es gibt auch keinen Standardwert, hinter dem einer überleben könnte', () => {
  // Der Auftrag erlaubte einen Standardwert für „neun Aufrufstellen". **Es sind zwei** — der Grund
  // besteht nicht, also gibt es ihn nicht. `farben` ist Pflicht; ohne sie kompiliert kein Aufruf.
  const roh = readFileSync(join(hier, '../geometry/treppeSvg.ts'), 'utf8');
  assert.match(roh, /^\s{2}farben: TreppenFarben;$/m, 'Pflichtfeld, kein `?`');
  assert.doesNotMatch(roh, /opt: SvgOptionen = \{\}/, 'kein leerer Standard mehr');
  assert.match(roh, /const F = opt\.farben;/, 'die Farben kommen aus dem Aufruf');
});

test('K1: `geometry/` bezieht nichts aus der Anzeigeschicht — die Richtung bleibt gewahrt', () => {
  // Der bequeme Fehler wäre, `TREPPE_FARBEN` in `geometry/` zu importieren. Dann läge der Wert
  // zwar woanders, aber die Geometrie hinge weiter am Aussehen — nur unsichtbarer.
  const roh = readFileSync(join(hier, '../geometry/treppeSvg.ts'), 'utf8');
  assert.doesNotMatch(roh, /from '\.\.\/app/, 'kein Griff nach oben');
  assert.doesNotMatch(roh, /TREPPE_FARBEN/);
});

// --- Die Palette selbst ---------------------------------------------------------------------------
test('die Palette deckt genau die sechs Rollen ab — nicht mehr, nicht weniger', () => {
  assert.deepEqual(Object.keys(TREPPE_FARBEN).sort(),
    ['bg', 'lauflinie', 'rahmen', 'stufe', 'text', 'umriss']);
});

test('gemessen und festgehalten: zwei der sechs sind schon vorhandene Rollen, vier nicht', () => {
  // **Nicht angeglichen, nur gemessen.** Angleichung wäre eine sichtbare Farbänderung und ist
  // Yamas Entscheidung. Die Zahl steht hier, damit sie später auf Zahlen trifft, nicht auf
  // Erinnerung.
  assert.equal(TREPPE_FARBEN.umriss, T.canvasWall, 'der Umriss ist die Wandfarbe');
  assert.equal(TREPPE_FARBEN.bg, T.surface, 'der Hintergrund ist die Flächenfarbe');
  const vorhanden = new Set<string>(Object.values(T));
  const ohneRolle = Object.entries(TREPPE_FARBEN).filter(([, w]) => !vorhanden.has(w)).map(([k]) => k);
  assert.deepEqual(ohneRolle.sort(), ['lauflinie', 'rahmen', 'stufe', 'text'],
    'vier Werte haben heute keine Rolle — für sie eine zu erfinden wäre eine eigene Entscheidung');
});

test('die Lauflinie behält ihr eigenes Grün — der Tausch ist NICHT entschieden', () => {
  // `#93c21c` hier, `#7fae1c` als Marke, `0xa3e635` in `szene.ts`: drei Grüns für eine Rolle.
  // Ob die Lauflinie Markenfarbe tragen soll, ist offen — ein stiller Tausch wäre genau die
  // sichtbare Änderung, die dieser Posten nicht machen darf.
  assert.notEqual(TREPPE_FARBEN.lauflinie, T.brand);
  assert.equal(TREPPE_FARBEN.lauflinie, '#93c21c');
});
