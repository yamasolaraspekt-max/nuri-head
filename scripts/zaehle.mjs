/**
 * **Die Barriere für F-09 und F-11.**
 *
 * **F-09 — „Text wird gemessen, nicht Absicht"** (Zähler 8, bis heute ohne Barriere): eine verbotene
 * Zeichenfolge steht im Kommentar, der erklärt, *warum* sie verboten ist — und der Zähler zählt sie
 * mit. Gemessen am 01.08.2026 zweimal an einem Vormittag: **751 tote Code-Pfade statt 75**, und
 * **8 rohe Farbwerte**, die alle in Kommentaren stehen und dort dokumentieren, was bewusst NICHT in
 * die CSS geholt wurde.
 *
 * **F-11 — „Zusage prüft eine Zeichenkette ohne Wortgrenze"** (Zähler 2, beide Male vom Prüfenden
 * gefunden): die Mutation `x` ⇒ `xy` bleibt grün, weil `xy` das `x` enthält.
 *
 * **Warum ein Werkzeug und keine Regel:** beide Klassen verlangen bisher, dass jemand im richtigen
 * Moment daran denkt. *Genau das hat achtmal nicht getragen.* Ein Befehl, der Kommentare gar nicht
 * erst sieht, braucht keine Aufmerksamkeit.
 *
 * Aufruf:
 *   node scripts/zaehle.mjs <datei> <muster> [--wort] [--mit-kommentaren]
 *
 * Ausgabe: eine Zahl. Exitcode immer 0 — **auch bei null Treffern.**
 * *`grep -c` liefert bei null Treffern `exit 1`; ein Kriterium, das schon am Ausgangswert abbricht,
 * misst nichts. Diese Falle steht in den Planner-Notizen und ist am 01.08. trotzdem in ein Blatt
 * geraten — der Validator hat sie gefangen.*
 */
import { readFileSync } from 'node:fs';

/** Blockkommentare, Zeilenkommentare, HTML- und Raute-Kommentare entfernen. */
export function ohneKommentare(text, { raute = false } = {}) {
  let t = text;
  t = t.replace(/\/\*[\s\S]*?\*\//g, '');          // /* ... */   CSS, JS, TS
  t = t.replace(/<!--[\s\S]*?-->/g, '');            // <!-- ... --> HTML, Markdown
  t = t.replace(/(^|[^:])\/\/[^\n]*/g, '$1');       // // ...      aber nicht in http://
  if (raute) t = t.replace(/(^|\s)#[^\n]*/gm, '$1'); // # ...      Shell, YAML
  return t;
}

/** Zählt, wie oft `muster` in `text` vorkommt. `wort` setzt Wortgrenzen. */
export function zaehle(text, muster, { wort = false, mitKommentaren = false, raute = false } = {}) {
  const quelle = mitKommentaren ? text : ohneKommentare(text, { raute });
  const roh = wort ? `(?<![A-Za-z0-9_-])(?:${muster})(?![A-Za-z0-9_-])` : muster;
  const treffer = quelle.match(new RegExp(roh, 'g'));
  return treffer ? treffer.length : 0;
}

const istDirekterAufruf = process.argv[1] && import.meta.url.endsWith(process.argv[1].split('/').pop());
if (istDirekterAufruf) {
  const [datei, muster, ...rest] = process.argv.slice(2);
  if (!datei || !muster) {
    console.error('Aufruf: node scripts/zaehle.mjs <datei> <muster> [--wort] [--mit-kommentaren] [--raute]');
    process.exit(2);
  }
  const text = readFileSync(datei, 'utf8');
  console.log(zaehle(text, muster, {
    wort: rest.includes('--wort'),
    mitKommentaren: rest.includes('--mit-kommentaren'),
    raute: rest.includes('--raute'),
  }));
}
