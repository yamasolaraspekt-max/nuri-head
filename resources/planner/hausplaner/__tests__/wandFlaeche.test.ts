/**
 * AUF-77 — **Wandfläche brutto und netto** (M1).
 *
 * Die Rechnung, auf der Putz, Dämmung, Anstrich, Fassade und Heizlast alle aufsetzen. Sie fehlte
 * bisher vollständig: die Öffnungen lagen im Modell, aber niemand zog sie ab, weil es keine
 * Wandfläche gab.
 *
 * **Die zwei Eigenschaften, die dieser Test schützt:**
 *
 * 1. **Kein Ergebnis ohne Bezugsmaß** — und zwar als *Typfehler*, nicht als Sorgfalt. Dafür läuft
 *    hier ein echter Compiler.
 * 2. **Ein Zweifelsfall liefert keine Zahl.** Eine Wand mit überlappenden Öffnungen darf kein
 *    Ergebnis haben — *plausibel falsch ist schlimmer als offensichtlich fehlend.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import { readFileSync, writeFileSync, rmSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { wandMengen, type Bezugsmass, type WandFlaecheErgebnis } from '../geometry/wandFlaeche';
import type { WallNode, OpeningNode } from '../domain/scene.types';

const hier = dirname(fileURLToPath(import.meta.url));

const basis = {
  levelId: 'l1', visible: true, locked: false, tags: [] as string[],
  createdAt: '2026-07-26T10:00:00Z', updatedAt: '2026-07-26T10:00:00Z',
};

/** Die Wand der Handrechnung: 5 000 × 2 500 mm, 300 mm dick. */
const wand = (zusatz: Partial<WallNode> = {}): WallNode => ({
  ...basis, id: 'w1', type: 'wall',
  start: { x: 0, y: 0 }, end: { x: 5000, y: 0 },
  thickness: 300, height: 2500, ...zusatz,
} as WallNode);

/** Das Fenster der Handrechnung: 1 200 × 1 400 mm. */
const fenster = (zusatz: Partial<OpeningNode> = {}): OpeningNode => ({
  ...basis, id: 'o1', type: 'window', hostWallId: 'w1',
  offsetFromWallStart: 1000, width: 1200, height: 1400, sillHeight: 900, ...zusatz,
} as OpeningNode);

const mengenVon = (e: WandFlaecheErgebnis) => {
  assert.equal(e.art, 'mengen', e.art === 'meldung' ? e.meldungen.map((m) => m.text).join(' · ') : '');
  return (e as { art: 'mengen'; mengen: ReturnType<typeof Object> } & { mengen: any }).mengen;
};
const meldungenVon = (e: WandFlaecheErgebnis) => {
  assert.equal(e.art, 'meldung', 'hier darf es KEINE Zahl geben');
  return (e as { art: 'meldung'; meldungen: readonly { art: string; text: string }[] }).meldungen;
};

// --- K5: die Handrechnung -------------------------------------------------------------------------
test('K5: 5000 × 2500 mit Fenster 1200 × 1400 ⇒ brutto 12,5 · Öffnung 1,68 · netto 10,82 m²', () => {
  // Als ZAHL geprüft, nicht als Formel: eine Formel, die sich selbst nachrechnet, prüft nichts.
  const m = mengenVon(wandMengen(wand(), [fenster()], 'roh'));
  assert.equal(m.bruttoM2, 12.5);
  assert.equal(m.oeffnungenM2, 1.68);
  assert.equal(m.nettoM2, 10.82);
});

test('K5: dieselbe Wand als Volumen — 300 mm Dicke', () => {
  const m = mengenVon(wandMengen(wand(), [fenster()], 'roh'));
  assert.equal(m.bruttoM3, 3.75);
  assert.equal(m.oeffnungenM3, 0.504);
  assert.equal(m.nettoM3, 3.246);
});

// --- Kante 1: keine Öffnung -----------------------------------------------------------------------
test('Kante 1: Wand ohne Öffnungen ⇒ netto = brutto', () => {
  const m = mengenVon(wandMengen(wand(), [], 'roh'));
  assert.equal(m.nettoM2, m.bruttoM2);
  assert.equal(m.oeffnungenM2, 0);
});

// --- K4: kein Ergebnis ohne Bezugsmaß -------------------------------------------------------------
test('K4: jedes Feldbündel trägt die Angabe — zur Laufzeit', () => {
  for (const bezug of ['roh', 'fertig'] as Bezugsmass[]) {
    const m = mengenVon(wandMengen(wand(), [fenster()], bezug));
    assert.equal(m.bezug, bezug, 'die Angabe steht im Bündel und stimmt');
  }
});

test('K4: und ein Bündel OHNE die Angabe ist ein Typfehler — mit echtem Compiler belegt', () => {
  // **Der Kern des Postens.** Zur Laufzeit lässt sich Unmöglichkeit nicht prüfen; `@ts-expect-error`
  // dreht die Aussage um: der Lauf ist grün, WEIL der Fehler eintritt. Macht jemand `bezug`
  // optional, verschwindet der Fehler — und dieser Test wird rot.
  const probe = join(hier, 'typprobe-wandFlaeche.generiert.ts');
  writeFileSync(probe, readFileSync(join(hier, 'typprobe-wandFlaeche.tsprobe'), 'utf8'));
  try {
    execFileSync('./node_modules/typescript/bin/tsc', [
      '--noEmit', '--strict', '--target', 'es2022', '--module', 'esnext',
      '--moduleResolution', 'bundler', '--skipLibCheck', probe,
    ], { cwd: join(hier, '../../../..'), stdio: 'pipe' });
  } catch (fehler) {
    const ausgabe = String((fehler as { stdout?: Buffer }).stdout ?? fehler);
    assert.fail(`der erwartete Typfehler blieb aus (oder ein anderer trat auf):\n${ausgabe}`);
  } finally {
    rmSync(probe, { force: true });
  }
});

// --- K6: fehlende Schichten ------------------------------------------------------------------------
test('K6: ohne Schichten gilt fertig = roh — und das Ergebnis sagt es ausdrücklich', () => {
  const roh = mengenVon(wandMengen(wand(), [], 'roh'));
  const fertig = mengenVon(wandMengen(wand(), [], 'fertig'));
  assert.equal(fertig.dickeMm, roh.dickeMm, 'ohne Schichten bleibt die Dicke das Rohmaß');
  assert.ok(fertig.rohmassRest.some((r: string) => r.startsWith('dicke:')),
    'und es steht im Ergebnis, statt still als „fertig" durchzugehen');
  assert.deepEqual(roh.rohmassRest, [], 'beim Rohmaß gibt es nichts zu vermerken');
});

test('mit Schichten wird die Dicke wirklich kleiner', () => {
  const w = wand({ schichten: [{ materialId: 'putz', dickeMm: 15 }, { dickeMm: 25 }] });
  const m = mengenVon(wandMengen(w, [], 'fertig'));
  assert.equal(m.dickeMm, 260, '300 − 40');
  assert.ok(!m.rohmassRest.some((r: string) => r.startsWith('dicke:')), 'die Dicke ist jetzt gerechnet');
});

test('die Grenzen des Fertigmaßes werden BENANNT, nicht verschwiegen', () => {
  // Länge: hängt vom Wandverbund ab (Auftrag §3). Höhe: hängt am Fussboden-/Deckenaufbau, der
  // nicht im Eingang liegt — ein fehlender Operand wird gemeldet, nicht geschätzt.
  const m = mengenVon(wandMengen(wand({ schichten: [{ dickeMm: 40 }] }), [], 'fertig'));
  assert.ok(m.rohmassRest.some((r: string) => r.startsWith('laenge:')));
  assert.ok(m.rohmassRest.some((r: string) => r.startsWith('hoehe:')));
  assert.equal(m.laengeMm, 5000);
  assert.equal(m.hoeheMm, 2500);
});

// --- K7: die Meldefälle liefern KEINE Zahl --------------------------------------------------------
test('Meldefall 2: Öffnung ragt über die Wand hinaus ⇒ Meldung, kein Ergebnis', () => {
  const e = wandMengen(wand(), [fenster({ offsetFromWallStart: 4500, width: 1200 })], 'roh');
  const m = meldungenVon(e);
  assert.equal(m.length, 1);
  assert.equal(m[0]!.art, 'oeffnung-ragt-hinaus');
  assert.match(m[0]!.text, /5700 mm/, 'die Meldung nennt die Zahl, nicht nur den Fall');
});

test('Meldefall 3: zwei Öffnungen überlappen ⇒ Meldung — der häufigste Fehler solcher Systeme', () => {
  const e = wandMengen(wand(), [
    fenster({ id: 'o1', offsetFromWallStart: 1000, width: 1200 }),
    fenster({ id: 'o2', offsetFromWallStart: 1800, width: 1200 }),
  ], 'roh');
  const m = meldungenVon(e);
  assert.equal(m[0]!.art, 'oeffnungen-ueberlappen');
  assert.match(m[0]!.text, /o1 und o2/);
});

test('aber ein Oberlicht ÜBER einer Tür ist keine Überlappung — Fehlalarme wären genauso schlimm', () => {
  // Beide teilen sich denselben Abschnitt der Wandachse, liegen aber übereinander. Ein
  // Prüfschritt, der hier meldet, wird nach dem dritten Mal abgeschaltet.
  const e = wandMengen(wand(), [
    fenster({ id: 'tuer', offsetFromWallStart: 1000, width: 1000, height: 2000, sillHeight: 0 }),
    fenster({ id: 'oberlicht', offsetFromWallStart: 1000, width: 1000, height: 400, sillHeight: 2000 }),
  ], 'roh');
  const m = mengenVon(e);
  assert.equal(m.oeffnungenM2, 2.4, '2,0 + 0,4 m² — beide abgezogen, keiner doppelt');
});

test('Meldefall 4: Öffnung höher als die Wand ⇒ Meldung', () => {
  const e = wandMengen(wand(), [fenster({ sillHeight: 1500, height: 1400 })], 'roh');
  assert.equal(meldungenVon(e)[0]!.art, 'oeffnung-hoeher-als-wand');
});

test('Meldefall 6: Schichten dicker als die Wand ⇒ Meldung, nicht rechnen', () => {
  // Ein negatives Fertigmaß ist kein Ergebnis.
  const w = wand({ schichten: [{ dickeMm: 200 }, { dickeMm: 150 }] });
  const m = meldungenVon(wandMengen(w, [], 'fertig'));
  assert.equal(m[0]!.art, 'schichten-dicker-als-wand');
  assert.match(m[0]!.text, /350 mm > Dicke 300 mm/);
});

test('dieselbe Wand im ROHMASS rechnet weiter — die Schichten stören dort nicht', () => {
  const w = wand({ schichten: [{ dickeMm: 200 }, { dickeMm: 150 }] });
  assert.equal(mengenVon(wandMengen(w, [], 'roh')).dickeMm, 300);
});

test('eine fremde Öffnung wird gemeldet, nicht mitgerechnet', () => {
  // Sie stillschweigend abzuziehen wäre ein Fehler, den niemand mehr findet.
  const e = wandMengen(wand(), [fenster({ id: 'fremd', hostWallId: 'w2' })], 'roh');
  const m = meldungenVon(e);
  assert.equal(m[0]!.art, 'fremde-oeffnung');
  assert.match(m[0]!.text, /w2/);
});

// --- Kante 5: Null bleibt Null ---------------------------------------------------------------------
test('Kante 5: Höhe 0 ⇒ Ergebnis 0, kein NaN und kein Infinity', () => {
  const m = mengenVon(wandMengen(wand({ height: 0 }), [], 'roh'));
  for (const wert of [m.bruttoM2, m.nettoM2, m.bruttoM3, m.nettoM3]) {
    assert.equal(wert, 0);
    assert.ok(Number.isFinite(wert));
  }
});

test('Kante 5: Länge 0 ebenso', () => {
  const m = mengenVon(wandMengen(wand({ end: { x: 0, y: 0 } }), [], 'roh'));
  assert.equal(m.bruttoM2, 0);
  assert.ok(Number.isFinite(m.nettoM3));
});

// --- K3: rein und wiederholbar ---------------------------------------------------------------------
test('K3: zweimal dieselbe Eingabe ⇒ tiefengleiches Ergebnis', () => {
  const a = wandMengen(wand(), [fenster()], 'fertig');
  const b = wandMengen(wand(), [fenster()], 'fertig');
  assert.deepEqual(a, b);
});

test('K3: keine DOM-, Store- oder Befehls-Abhängigkeit im Quelltext', () => {
  const roh = readFileSync(join(hier, '../geometry/wandFlaeche.ts'), 'utf8');
  for (const wort of ['window', 'document', 'getState', 'executeCommand']) {
    assert.ok(!roh.includes(wort), `${wort} steht in einer reinen Rechen-Datei`);
  }
});

// --- K8: eine Rundungsstelle ------------------------------------------------------------------------
test('K8: genau EIN Ort, an dem gerundet wird', () => {
  // Zwei Rundungsorte ergeben zwei Summen, die sich um Cents unterscheiden — daran zerbricht
  // später ein Angebot.
  const roh = readFileSync(join(hier, '../geometry/wandFlaeche.ts'), 'utf8');
  const stellen = (roh.match(/Math\.round|toFixed|Math\.ceil|Math\.floor/g) ?? []);
  assert.equal(stellen.length, 1, `gerundet wird an ${stellen.length} Stellen: ${stellen.join(', ')}`);
});

test('die Rundung greift wirklich — krumme Maße ergeben zwei Nachkommastellen', () => {
  const m = mengenVon(wandMengen(wand({ end: { x: 3333, y: 0 }, height: 2777 }), [], 'roh'));
  assert.equal(m.bruttoM2, 9.26, '3333 × 2777 mm² = 9,255741 m² ⇒ 9,26');
  assert.equal(String(m.bruttoM2).split('.')[1]?.length ?? 0, 2);
});
