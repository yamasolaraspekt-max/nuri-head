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

// --- Zwei Löcher in der Barriere selbst, gefunden 01.08. 13:0x -------------------------------------

/**
 * **Zwei Stunden nachdem ich `zaehle.mjs` als Barriere gemeldet hatte, hatte sie zwei Löcher.**
 * *Gefunden nicht durch Nachdenken, sondern durch drei Randfälle, die ich gegen die eigene Arbeit
 * gefahren habe — auf Yamas Frage „bist du sicher, dass nichts offen ist".*
 *
 * **Das zweite Loch war das gefährliche:** ein `//` in einer Zeichenkette machte echten Code
 * unsichtbar. **Ein Zähler, der zu WENIG meldet, lässt ein Kriterium „erwartet 0" grün werden,
 * obwohl die Stelle noch dasteht** — genau der Fehler, den die Barriere verhindern sollte.
 */
test('Loch 1: ein unbeendeter Blockkommentar verschluckte nicht mehr, sondern zu wenig', () => {
  const css = '.a { color: #111111; } /* unbeendet\n.b { color: #222222; }\n';
  assert.equal(zaehle(css, '#[0-9a-f]{6}'), 1, 'nur die Farbe VOR dem offenen Kommentar zählt');
});

test('Loch 2: `//` in einer Zeichenkette ist Code, kein Kommentar', () => {
  const ts = 'const s = "// kein Kommentar";\nconst t = 1;';
  assert.equal(zaehle(ts, 'kein Kommentar'), 1, 'der Inhalt einer Zeichenkette bleibt stehen');
});

test('Loch 2, die andere Richtung: was WIRKLICH hinter // steht, verschwindet weiter', () => {
  const ts = 'const s = "a"; // hier steht wegdamit\n';
  assert.equal(zaehle(ts, 'wegdamit'), 0);
});

test('eine URL in CSS ist kein Kommentar', () => {
  const css = 'a { background: url(http://x.test/i.png); }\n.b { color: #abcdef; }';
  assert.equal(zaehle(css, '#[0-9a-f]{6}'), 1);
  assert.equal(zaehle(css, 'x\\.test'), 1);
});

test('Gegenprobe an einer echten Datei: der Zähler misst nach dem Umbau dasselbe wie vorher', () => {
  // HausplanerApp.tsx im Stand von 7e2bf407 trug GENAU EINE eigene Fang-Schleife. Die alte
  // Fassung dieser Funktion zählte dort 1, die neue muss dasselbe zählen — sonst hätte die
  // Reparatur der zwei Löcher ein drittes aufgerissen.
  const quelle = 'if (Math.hypot(p.x - x, p.y - y) <= 150) { return { x: p.x, y: p.y }; }\n'
    + '// 1) Endpunkt-Snap (150 mm Radius) hat Vorrang.';
  assert.equal(zaehle(quelle, 'hypot\\(p\\.x - x, p\\.y - y\\)'), 1);
  assert.equal(zaehle(quelle, '150', { wort: true }), 1, 'die 150 im Kommentar zählt nicht mit');
});
