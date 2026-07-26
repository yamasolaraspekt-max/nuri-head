/**
 * AUF-67 — **die Befehlspalette wird globale Navigation.**
 *
 * Der Befund: sie konnte genau **eine** Art von Sache. Alles andere, wonach jemand sucht, existierte
 * bereits als Register — und die Palette fragte keines davon.
 *
 * **Die Eigenschaft, die dieser Test schuetzt, ist die eiserne Regel des Postens:**
 * *Die Palette weiss nichts selbst. Sie fragt die vorhandenen Register.* Deshalb steht hier zu jeder
 * Art eine **Mutation**: aendert sich das Register, aendert sich die Palette. Ein Test, der nur
 * prueft, dass Eintraege da sind, wuerde eine fest eingebaute Liste genauso gruen faerben.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { palettenGruppen, palettenFlach, PALETTE_ARTEN, PALETTE_LEER } from '../app/dashboard/palette';
import { stapel } from '../app/dashboard/geschossStapel';
import { ARBEITSBEREICHE } from '../app/dashboard/arbeitsbereiche';
import type { AktivierungsKontext } from '../app/tools/toolTypes';
import type { BaumGruppe } from '../app/dashboard/projektBaum';

const hier = dirname(fileURLToPath(import.meta.url));
const kontext: AktivierungsKontext = {
  workspace: 'architektur' as AktivierungsKontext['workspace'],
  view: '2d' as AktivierungsKontext['view'],
  selection: { count: 0, types: [], states: [] },
  permissions: ['Hausplaner,update'],
  capabilities: ['project.open', 'viewport.ready', 'activeLevel.exists'],
  projectState: 'editable',
};

const geschosse = [
  { id: 'l1', name: 'Erdgeschoss', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 },
  { id: 'l2', name: 'Dachgeschoss', elevation: 2700, defaultWallHeight: 2300, floorThickness: 200, sortOrder: 1 },
];
const baum: BaumGruppe[] = [
  { gruppe: 'Wände', anzahl: 2, eingeklappt: false,
    eintraege: [{ id: 'w1', label: 'Wand Nord', typ: 'wall' }, { id: 'w2', label: 'Wand Süd', typ: 'wall' }] },
  { gruppe: 'Dächer', anzahl: 1, eingeklappt: false,
    eintraege: [{ id: 'd1', label: 'Satteldach', typ: 'roof' }] },
];
const quellen = (zusatz = {}) => ({
  kontext,
  stapel: stapel(geschosse as never, 'l1'),
  baum,
  schritt: { satz: 'Zeichne eine Wand — das schaltet 12 Werkzeuge frei.', ort: 'schiene' },
  ...zusatz,
});
const artVon = (g: ReturnType<typeof palettenGruppen>, art: string) =>
  g.find((x) => x.art === art)!.eintraege;

// --- Die Arten -------------------------------------------------------------------------------------
test('die Palette findet fuenf Arten, in fester Reihenfolge', () => {
  const g = palettenGruppen(quellen(), '');
  assert.deepEqual(g.map((x) => x.art), ['werkzeug', 'geschoss', 'bauteil', 'bereich', 'schritt']);
  // Fest, weil eine Palette, deren Abschnitte springen, das Laufen mit Pfeiltasten unbrauchbar macht.
  assert.deepEqual(palettenGruppen(quellen(), 'wand').map((x) => x.art), g.map((x) => x.art));
});

test('jede Art traegt ihren Eintraegen die Art an — sonst weiss der Klick nicht, wohin', () => {
  for (const gruppe of palettenGruppen(quellen(), '')) {
    for (const e of gruppe.eintraege) assert.equal(e.art, gruppe.art);
  }
});

// --- K2: eine Quelle je Art, mit Mutation ----------------------------------------------------------
test('K2 Geschosse: aendert sich der Stapel, aendert sich die Palette', () => {
  const mit = artVon(palettenGruppen(quellen(), ''), 'geschoss');
  assert.deepEqual(mit.map((e) => e.label), ['Dachgeschoss', 'Erdgeschoss'], 'Reihenfolge des Stapels: von oben');
  // **Mutation:** ein Geschoss aus dem Register entfernt ⇒ es verschwindet aus der Palette.
  const ohne = artVon(palettenGruppen(
    quellen({ stapel: stapel([geschosse[0]!] as never, 'l1') }), ''), 'geschoss');
  assert.deepEqual(ohne.map((e) => e.label), ['Erdgeschoss']);
});

test('K2 Bauteile: aendert sich der Projektbaum, aendert sich die Palette', () => {
  const mit = artVon(palettenGruppen(quellen(), ''), 'bauteil');
  assert.deepEqual(mit.map((e) => e.id), ['w1', 'w2', 'd1'], 'Gruppenreihenfolge des Baums');
  const ohne = artVon(palettenGruppen(quellen({ baum: [baum[1]!] }), ''), 'bauteil');
  assert.deepEqual(ohne.map((e) => e.id), ['d1']);
});

test('K2 Bereiche: die Palette zeigt genau die Register-Bereiche', () => {
  const b = artVon(palettenGruppen(quellen(), ''), 'bereich');
  assert.deepEqual(b.map((e) => e.id), ARBEITSBEREICHE.map((x) => x.id));
});

test('K2 Schritt: ohne Wegweiser kein Eintrag — es wird keiner erfunden', () => {
  assert.equal(artVon(palettenGruppen(quellen({ schritt: null }), ''), 'schritt').length, 0);
  assert.equal(artVon(palettenGruppen(quellen(), ''), 'schritt').length, 1);
});

test('K2 Werkzeuge: die Registry bleibt die Quelle', () => {
  const w = artVon(palettenGruppen(quellen(), ''), 'werkzeug');
  assert.ok(w.length > 5, `nur ${w.length} Werkzeuge`);
});

// --- K3: keine zweite Aktivierungslogik ------------------------------------------------------------
test('K3: `enabled`/`grund` werden nirgends in dieser Datei entschieden', () => {
  const roh = readFileSync(join(hier, '../app/dashboard/palette.ts'), 'utf8')
    .replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
  // Genau EIN Aufruf der Engine, und sonst keine Stelle, die `enabled` berechnet.
  assert.equal((roh.match(/resolveToolState\(/g) ?? []).length, 1);
  // **Die Eigenschaft, nicht ein Zeichen.** Mein erster Anlauf verbot jedes `enabled:`, dem kein
  // `true`/`false` folgte — und schlug damit auf genau die Zeile an, die den Wert richtigerweise
  // AUS DER ENGINE liest. Ein Verbot, das den erlaubten Fall trifft, prueft nicht die Regel,
  // sondern die Schreibweise. Erlaubt ist: aus der Engine, oder `true` fuer Navigation.
  // Die TYP-Zeile `enabled: boolean;` ist eine Deklaration, keine Zuweisung — sie endet auf `;`.
  const werte = [...roh.matchAll(/enabled: ([^,;\n]+)[,;]/g)].map((m) => m[1]!.trim())
    .filter((w) => w !== 'boolean');
  assert.ok(werte.length > 0, 'die Datei setzt ueberhaupt kein enabled?');
  for (const w of werte) {
    assert.ok(['zustand.enabled', 'true'].includes(w), `enabled wird hier gerechnet: ${w}`);
  }
  const gruende = [...roh.matchAll(/grund: ([^,;\n]+)[,;]/g)].map((m) => m[1]!.trim())
    .filter((g) => g !== 'string | null');
  for (const g of gruende) {
    assert.ok(['zustand.reason', 'null'].includes(g), `Grund selbst getextet: ${g}`);
  }
});

test('K3: Navigations-Eintraege sind immer frei — sie fuehren hin, das ist nie gesperrt', () => {
  for (const art of ['geschoss', 'bauteil', 'bereich', 'schritt']) {
    for (const e of artVon(palettenGruppen(quellen(), ''), art)) {
      assert.equal(e.enabled, true);
      assert.equal(e.grund, null);
    }
  }
});

// --- K5: der Filter -------------------------------------------------------------------------------
test('K5: der Filter trifft label UND id, ueber alle Arten, ohne Gross-/Kleinschreibung', () => {
  const treffer = palettenFlach(palettenGruppen(quellen(), 'DACH'));
  const arten = new Set(treffer.map((e) => e.art));
  assert.ok(arten.has('geschoss'), 'Dachgeschoss fehlt');
  assert.ok(arten.has('bauteil'), 'Satteldach fehlt');
  assert.ok(arten.has('werkzeug'), 'das Dachwerkzeug fehlt');
  // Genau der Fall aus der Auftragszeile: „Dach" fuehrt zum Werkzeug UND zum Objekt.
  assert.ok(treffer.some((e) => e.id === 'd1'));
  assert.ok(treffer.some((e) => e.id === 'l2'));
});

test('K5: die id trifft auch dann, wenn das Label nicht passt', () => {
  const treffer = palettenFlach(palettenGruppen(quellen(), 'w2'));
  assert.deepEqual(treffer.filter((e) => e.art === 'bauteil').map((e) => e.id), ['w2']);
});

// --- K6: Reihenfolge stabil -------------------------------------------------------------------------
test('K6: die Reihenfolge haengt nicht an der Auswahl', () => {
  const ohneAuswahl = palettenFlach(palettenGruppen(quellen(), 'wand')).map((e) => `${e.art}:${e.id}`);
  const mitAuswahl = palettenFlach(palettenGruppen(
    quellen({ kontext: { ...kontext, selection: { count: 1, types: ['wall'], states: [] } } }), 'wand'))
    .map((e) => `${e.art}:${e.id}`);
  // Werkzeuge duerfen ihren Zustand aendern (das ist die Engine), aber die Navigations-Arten nicht.
  const nav = (l: string[]) => l.filter((x) => !x.startsWith('werkzeug:'));
  assert.deepEqual(nav(ohneAuswahl), nav(mitAuswahl));
});

test('K6: die flache Liste ist genau die sichtbare Reihenfolge', () => {
  const g = palettenGruppen(quellen(), '');
  assert.deepEqual(palettenFlach(g), g.flatMap((x) => x.eintraege));
});

// --- K7: Leerzustand je Art, woertlich ---------------------------------------------------------------
test('K7: jede Art hat einen woertlichen Leerzustand — kein leerer Kasten', () => {
  for (const a of PALETTE_ARTEN) {
    assert.ok(a.leer.length > 20, `${a.art}: Leerzustand zu duenn`);
    assert.ok(!/folgt|in Kürze|demnächst/.test(a.leer), `${a.art}: Vertroestung`);
  }
  assert.equal(PALETTE_ARTEN[0]!.leer, PALETTE_LEER, 'der Werkzeug-Satz bleibt woertlich derselbe');
});

test('K7: ohne Treffer bleibt jede Gruppe leer — und sagt es', () => {
  const g = palettenGruppen(quellen(), 'zzz-gibt-es-nicht');
  assert.equal(palettenFlach(g).length, 0);
  for (const gruppe of g) assert.ok(gruppe.leer.length > 0);
});
