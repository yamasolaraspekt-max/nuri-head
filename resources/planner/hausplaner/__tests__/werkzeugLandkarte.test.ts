/**
 * AUF-50 Stufe 1 — die Landkarte gegen die Wirklichkeit.
 *
 * **Die tragende Zusage ist K-03:** jede `deckt`-Marke nennt einen Befehl, den es in
 * `applyCommand.ts` WIRKLICH gibt — gelesen aus der Datei, nicht aus einer abgeschriebenen Liste.
 * *Eine Landkarte, die auf Befehle zeigt, die es nicht gibt, ist schlimmer als keine.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { WERKZEUG_VERTRAEGE } from '../app/tools/werkzeugVertrag';
import {
  WERKZEUG_LANDKARTE, markenZaehlung, vertraegeOhneEintrag, eintraegeOhneVertrag,
} from '../app/tools/werkzeugLandkarte';

const hier = dirname(fileURLToPath(import.meta.url));

/** Die Befehlsnamen, wie sie WIRKLICH in `applyCommand.ts` stehen — aus den `case`-Zweigen gelesen. */
const VORHANDENE_BEFEHLE = new Set(
  [...readFileSync(join(hier, '../commands/applyCommand.ts'), 'utf8').matchAll(/case '([A-Z_]+)':/g)]
    .map((m) => m[1]),
);

// --- K-01: jeder Vertrag trägt genau eine Marke ---------------------------------------------------

test('K-01: jeder Vertrag hat einen Landkarteneintrag — und der Test nennt die fehlende id, nicht nur die Zahl', () => {
  const fehlend = vertraegeOhneEintrag();
  assert.deepEqual(fehlend, [], `Verträge ohne Landkarteneintrag: ${fehlend.join(', ')}`);
});

test('K-01 (Gegenrichtung): kein Landkarteneintrag ohne Vertrag', () => {
  // Ohne diese Prüfung bliebe die Zusage oben grün, wenn jemand einen erfundenen Eintrag ergänzt.
  const ueberzaehlig = eintraegeOhneVertrag();
  assert.deepEqual(ueberzaehlig, [], `Landkarteneinträge ohne Vertrag: ${ueberzaehlig.join(', ')}`);
});

test('K-01: jede id kommt genau EINMAL vor — keine doppelte Marke', () => {
  const gesehen = new Set<string>();
  const doppelt: string[] = [];
  for (const e of WERKZEUG_LANDKARTE) {
    if (gesehen.has(e.werkzeugId)) doppelt.push(e.werkzeugId);
    gesehen.add(e.werkzeugId);
  }
  assert.deepEqual(doppelt, [], `doppelte Einträge: ${doppelt.join(', ')}`);
});

test('K-01: die Zahlen decken sich — Landkarte und Verträge sind gleich lang', () => {
  // **Gegen die Vertragsliste geprüft, NICHT gegen eine abgeschriebene Zahl.** Der Auftrag nennt
  // 111; gemessen sind es 110 (`grep -c 'umkehrbar:'` zählt die Interface-Deklaration mit,
  // `werkzeugVertrag.ts:40`). Diese Zusage bleibt auch dann richtig, wenn ein Vertrag dazukommt.
  assert.equal(WERKZEUG_LANDKARTE.length, WERKZEUG_VERTRAEGE.length);
});

// --- K-03: jede `deckt`-Marke zeigt auf einen echten Befehl ---------------------------------------

test('K-03: jede `deckt`-Begründung nennt einen Befehl, den es in applyCommand WIRKLICH gibt', () => {
  const erfunden: string[] = [];
  for (const e of WERKZEUG_LANDKARTE) {
    if (e.marke !== 'deckt') continue;
    if (!VORHANDENE_BEFEHLE.has(e.begruendung)) erfunden.push(`${e.werkzeugId} → ${e.begruendung}`);
  }
  assert.deepEqual(erfunden, [], `deckt-Marken mit unbekanntem Befehl: ${erfunden.join(' · ')}`);
});

test('K-03 (Grundlage): der Leser findet die Befehle überhaupt — sonst prüft die Zusage oben Leere', () => {
  // presence-Partner nach R2: schlüge das `matchAll` fehl, wäre `VORHANDENE_BEFEHLE` leer und die
  // Zusage oben nur dann grün, wenn es GAR KEINE `deckt`-Marke gäbe.
  assert.equal(VORHANDENE_BEFEHLE.size, 19, 'die Zahl der Modellbefehle hat sich geändert');
  for (const bekannt of ['ADD_NODE', 'REMOVE_NODE', 'MOVE_NODE', 'UPDATE_NODE', 'UPDATE_SETTINGS']) {
    assert.ok(VORHANDENE_BEFEHLE.has(bekannt), `${bekannt} nicht gefunden — der Leser greift daneben`);
  }
});

test('K-03: es gibt überhaupt `deckt`-Marken — die Zusage misst nicht Leere', () => {
  assert.ok(markenZaehlung().deckt > 0);
});

// --- Inhaltliche Mindestqualität ------------------------------------------------------------------

test('jede Begründung ist ausgefüllt — keine leere Marke', () => {
  const leer = WERKZEUG_LANDKARTE.filter((e) => e.begruendung.trim().length === 0).map((e) => e.werkzeugId);
  assert.deepEqual(leer, [], `Einträge ohne Begründung: ${leer.join(', ')}`);
});

test('jede `fehlt`-Begründung sagt, was der Befehl leisten müsste — nicht nur „fehlt"', () => {
  // Der Auftrag verlangt das ausdrücklich: bei `fehlt` steht „der Satz, was der Befehl leisten
  // müsste". Ein Ein-Wort-Grund wäre kein Bauvorrat, sondern eine Wiederholung der Marke.
  const zuKurz = WERKZEUG_LANDKARTE
    .filter((e) => e.marke === 'fehlt' && e.begruendung.length < 40)
    .map((e) => e.werkzeugId);
  assert.deepEqual(zuKurz, [], `fehlt-Marken mit zu knapper Begründung: ${zuKurz.join(', ')}`);
});

test('jede `stillgelegt`-Marke ist begründet — sie wird gemeldet, nicht entfernt', () => {
  const still = WERKZEUG_LANDKARTE.filter((e) => e.marke === 'stillgelegt');
  assert.ok(still.length > 0, 'keine stillgelegt-Marke — dann prüft diese Zusage nichts');
  for (const e of still) {
    assert.ok(e.begruendung.length >= 40, `${e.werkzeugId}: zu knapp begründet`);
    // Der Vertrag bleibt bestehen — genau das ist die Auflage.
    assert.ok(WERKZEUG_VERTRAEGE.some((v) => v.werkzeugId === e.werkzeugId),
      `${e.werkzeugId} ist aus den Verträgen verschwunden — stillgelegt heißt MELDEN, nicht entfernen`);
  }
});

// --- Das Produkt dieser Stufe: die Zahlen ----------------------------------------------------------

test('die Markenzählung geht auf — Summe gleich Zahl der Verträge', () => {
  const z = markenZaehlung();
  const summe = z.deckt + z.fehlt + z['ohne-modell'] + z.stillgelegt;
  assert.equal(summe, WERKZEUG_VERTRAEGE.length);
});

test('ERGEBNIS der Stufe: die Zahlen stehen fest und sind hier festgehalten', () => {
  // **Diese Zusage ist absichtlich hart.** Ändert jemand eine Marke, wird sie rot — und genau das
  // ist gewollt: die Zahl ist das Produkt der Stufe, sie darf sich nicht unbemerkt verschieben.
  // Wer eine Marke bewusst ändert, zieht diese Zahl mit und sagt es im Commit.
  assert.deepEqual(markenZaehlung(), {
    deckt: 41,
    fehlt: 21,
    'ohne-modell': 42,
    stillgelegt: 6,
  });
});
