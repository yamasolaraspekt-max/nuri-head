/**
 * Die Zusagen zur Barriere von **F-09** und **F-11**.
 *
 * **Jede Zusage hier hält einen Fall fest, der wirklich eingetreten ist** — keine erfundenen
 * Beispiele. Das ist der Unterschied zwischen einer Zusage, die den gebauten Zustand einfriert,
 * und einer, die einen Fehler festnagelt (F-06).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { ohneKommentare, zaehle } from '../zaehle.mjs';

// --- F-09: der Kommentar, der erklaert, warum etwas verboten ist -----------------------------------

test('F-09: ein Farbwert im Kommentar wird nicht mitgezählt', () => {
  const css = `/* das Hinweisband (#eef3f2) wurde bewusst NICHT in die CSS geholt */\n.a { color: var(--x); }`;
  assert.equal(zaehle(css, '#[0-9a-fA-F]{3,6}', { mitKommentaren: true }), 1, 'roh gezählt sind es eins');
  assert.equal(zaehle(css, '#[0-9a-fA-F]{3,6}'), 0, 'ohne Kommentar ist es keins — das ist die Absicht');
});

test('F-09: der echte Fall vom 01.08. — acht Rohwerte, alle in Kommentaren', () => {
  const css = [
    '/*',
    ' * das Massstab-Schild (rgba(255,255,255,.7)) · das Hinweisband (#eef3f2)',
    ' * sein Symbol (#7fd8d3) · der Empfehlungstitel (#0a4f4d)',
    ' */',
    '.hp-a { color: var(--hp-fg); }',
  ].join('\n');
  assert.equal(zaehle(css, '#[0-9a-fA-F]{3,6}', { mitKommentaren: true }), 3);
  assert.equal(zaehle(css, '#[0-9a-fA-F]{3,6}'), 0);
});

test('F-09: ein HTML-Kommentar zählt genauso wenig', () => {
  const md = '<!-- `app/gibtsnicht.tsx` ist die Rotprobe -->\nEcht: `app/echt.tsx`';
  assert.equal(zaehle(md, 'app/[a-z.]+', { mitKommentaren: true }), 2);
  assert.equal(zaehle(md, 'app/[a-z.]+'), 1);
});

test('F-09: `//` in einer URL ist kein Kommentar', () => {
  const js = 'const u = "http://ticket.test/a"; // echter Kommentar mit http://weg.test/b';
  assert.equal(zaehle(js, 'ticket\\.test'), 1);
  assert.equal(zaehle(js, 'weg\\.test'), 0, 'was hinter // steht, ist Kommentar');
});

test('F-09: Rauten-Kommentare nur auf Verlangen — in CSS ist `#` eine Farbe', () => {
  const sh = 'echo eins # das hier ist ein Kommentar\n';
  assert.equal(zaehle(sh, 'Kommentar', { raute: true }), 0);
  assert.equal(zaehle(sh, 'Kommentar'), 1, 'ohne --raute bleibt die Zeile stehen');
  assert.equal(zaehle('.a { color: #abcdef; }', '#[0-9a-f]{6}'), 1, 'eine CSS-Farbe ueberlebt');
});

// --- F-11: die Mutation x => xy, die gruen blieb ---------------------------------------------------

test('F-11: ohne Wortgrenze zählt `hp-ok` auch in `hp-ok-menue` mit', () => {
  const q = 'class="hp-ok" class="hp-ok-menue"';
  assert.equal(zaehle(q, 'hp-ok'), 2);
  assert.equal(zaehle(q, 'hp-ok', { wort: true }), 1, 'die Mutation x => xy muss auffallen');
});

test('F-11: die Wortgrenze achtet auf Bindestrich und Unterstrich, nicht nur auf Leerzeichen', () => {
  assert.equal(zaehle('T.bg T.bg2 T_bg', 'T\\.bg', { wort: true }), 1);
});

// --- Der Rueckgabewert, an dem sich `grep -c` verschluckt -------------------------------------------

test('null Treffer sind eine Null, kein Fehlschlag', () => {
  assert.equal(zaehle('nichts hier', 'gibtsnicht'), 0);
});

test('ohneKommentare laesst den Rest unangetastet', () => {
  const t = 'a\n/* weg */\nb';
  assert.match(ohneKommentare(t), /a/);
  assert.match(ohneKommentare(t), /b/);
  assert.doesNotMatch(ohneKommentare(t), /weg/);
});
