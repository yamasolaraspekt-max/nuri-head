/**
 * AUF-38 — **die generische Rohwert-Zusage. Eine für alle Scheiben.**
 *
 * **Warum sie generisch ist.** Bis hierher trug jede Ausnahme ihre eigene Zusage. Wer eine neue
 * anlegt, muss daran denken, auch die Zusage zu schreiben — und genau das ist zweimal unterblieben
 * (Toast `#1a262a`, Gradient `#e9f4f2/#eef3e6`). Der Evaluator hat es benannt: *„Per-Scheibe-Locks
 * skalieren nicht."* **Einzelzusagen finden nur, was jemand vorher gezählt hat.**
 *
 * **Was sie zusagt:** eine Rohfarbe darf inline bleiben — aber **nur solange sie keinen Token hat**.
 * Bekommt sie einen, ist sie keine Ausnahme mehr, sondern ein Stil, der in die Schicht gehört, und
 * der Test geht rot. Das gilt für **alle** Dateien der Insel, auch für die, die noch keine Scheibe
 * gesehen haben.
 *
 * Gemessen wird mit `scripts/statische-inline-stile.mjs` — **demselben Werkzeug**, das die
 * Grundgesamtheit einer Scheibe zählt. Zwei Maßstäbe für dieselbe Sache wären der Fehler, gegen den
 * das Skript gebaut wurde.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { T } from '../app/studioDaten';
import {
  stilBloecke, istStatisch, istAusnahme, rohfarben, tsxDateien, messeAlle,
} from '../../../../scripts/statische-inline-stile.mjs';

const TOKENWERTE = new Set<string>(Object.values(T).map((w) => String(w).toLowerCase()));

// --- Die Zusage ----------------------------------------------------------------------------------

test('KEINE inline gebliebene Rohfarbe hat einen Token — über alle Dateien der Insel', () => {
  const treffer: string[] = [];
  for (const pfad of tsxDateien()) {
    const quelle = readFileSync(pfad, 'utf8');
    for (const block of stilBloecke(quelle)) {
      for (const farbe of rohfarben(block.text)) {
        if (TOKENWERTE.has(farbe)) treffer.push(`${pfad}:${block.zeile} — ${farbe}`);
      }
    }
  }
  assert.deepEqual(treffer, [],
    'Diese Farben stehen inline UND haben einen Token. Damit sind sie keine Ausnahme mehr — '
    + `sie gehören in die Stilschicht:\n${treffer.join('\n')}`);
});

test('die Zusage misst überhaupt etwas — es gibt Rohfarben, die sie prüfen kann', () => {
  // Ohne diesen Wächter wäre die Zusage oben still grün, sobald das Werkzeug nichts mehr findet
  // (Tippfehler im Pfad, geänderte Dateiendung). **Eine Zusage, die nichts sieht, sagt nichts zu.**
  const alle = tsxDateien().flatMap((p) =>
    stilBloecke(readFileSync(p, 'utf8')).flatMap((b) => rohfarben(b.text)));
  assert.ok(alle.length >= 4, `nur ${alle.length} Rohfarben gefunden — das Werkzeug greift nicht`);
});

// --- Das Werkzeug selbst -------------------------------------------------------------------------

test('die Klammerzählung schneidet verschachtelte Objekte nicht mitten durch', () => {
  // Ein naives „bis zum ersten `}}`" hätte hier nach `{ a: 1 }` abgebrochen und den Rest verloren —
  // und damit einen dynamischen Block für statisch gehalten.
  const quelle = "<div style={{ a: { b: 1 }, c: hover ? 1 : 2 }} />";
  const [block] = stilBloecke(quelle);
  assert.ok(block!.text.includes('hover'), 'der Block wurde zu früh abgeschnitten');
  assert.equal(istStatisch(block!.text), false);
});

test('eine Vorlagen-Zeichenkette mit `}` beendet den Block nicht', () => {
  const quelle = '<div style={{ border: `1px solid ${T.hair}`, gap: 4 }} />';
  const [block] = stilBloecke(quelle);
  assert.ok(block!.text.endsWith('}}'), 'der Block endet nicht an der richtigen Stelle');
  assert.equal(istStatisch(block!.text), true, 'nur Literale und ein Token — das ist statisch');
});

test('die Definition: Bedingung, Spread, Aufruf und fremder Bezeichner sind NICHT statisch', () => {
  const nein = [
    "style={{ color: an ? T.ink : T.muted }}",
    "style={{ ...grund, cursor: 'default' }}",
    "style={{ width: breite(3) }}",
    "style={{ width: navBreit }}",
  ];
  for (const t of nein) assert.equal(istStatisch(t), false, `sollte dynamisch sein: ${t}`);
});

test('die Definition: Literale und `T.*` sind statisch — Eigenschaftsnamen zählen nicht', () => {
  const ja = [
    "style={{ display: 'flex', gap: 8 }}",
    "style={{ color: T.muted, fontSize: 13.5 }}",
    "style={{ padding: '9px 12px', boxShadow: T.schattenFlach }}",
  ];
  for (const t of ja) assert.equal(istStatisch(t), true, `sollte statisch sein: ${t}`);
});

test('Ausnahme erkennt beides: Rohwert und Sperrstil-Modul', () => {
  assert.equal(istAusnahme("style={{ background: '#1a262a' }}"), true, 'Rohfarbe');
  assert.equal(istAusnahme("style={{ background: 'rgba(24,34,38,.30)' }}"), true, 'rgba');
  assert.equal(istAusnahme('style={{ cursor: GESPERRT_ZEIGER }}'), true, 'Sperrstil-Modul');
  assert.equal(istAusnahme("style={{ color: T.ink }}"), false, 'reiner Token ist keine Ausnahme');
});

test('das Werkzeug liefert für jede Datei eine vollständige Rechnung', () => {
  // gesamt = statisch + dynamisch, und statisch = Ausnahmen + offen. Geht das nicht auf, zählt es
  // etwas doppelt oder gar nicht — dann ist jede Scheibenzahl daraus falsch.
  for (const m of messeAlle()) {
    assert.ok(m.statisch <= m.gesamt, `${m.pfad}: mehr statisch als gesamt`);
    assert.equal(m.ausnahmen + m.offen.length, m.statisch,
      `${m.pfad}: Ausnahmen + offen ergibt nicht statisch`);
  }
});
