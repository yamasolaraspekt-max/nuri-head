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

/**
 * Blockkommentare, Zeilenkommentare, HTML- und Raute-Kommentare entfernen.
 *
 * **Zwei Fehler, die diese Fassung behebt — beide gefunden am 01.08. um 13:0x, zwei Stunden nachdem
 * ich diese Datei als „Barriere" gemeldet hatte.** *Sie stehen hier, weil eine Barriere mit einem
 * stillen Loch schlimmer ist als gar keine: sie erzeugt Vertrauen, das sie nicht trägt.*
 *
 * 1. **`//` in einer Zeichenkette wurde als Kommentar gelesen** — `const s = "// kein Kommentar";`
 *    verlor seinen Inhalt. **Das ist die gefährliche Richtung: der Zähler meldet weniger, als da
 *    ist.** Ein Kriterium „erwartet 0" wäre grün geworden, obwohl die Stelle noch dasteht.
 *    *Behoben, indem Zeichenketten VOR dem Kommentar-Abzug maskiert und danach zurückgesetzt werden.*
 * 2. **Ein unbeendeter `/*` blieb stehen** — dahinter wurde weitergezählt. *Behoben: ein `/*` ohne
 *    Abschluss gilt bis zum Dateiende, so wie jeder Parser es auch sieht.*
 */
export function ohneKommentare(text, { raute = false } = {}) {
  // 1) Zeichenketten wegsperren, damit ein `//` oder `/*` darin nichts anrichtet.
  const tresor = [];
  let t = text.replace(/(['"`])(?:\\.|(?!\1)[^\\\n])*\1/g, (treffer) => {
    tresor.push(treffer);
    return `\u0000${tresor.length - 1}\u0000`;
  });

  t = t.replace(/\/\*[\s\S]*?\*\//g, '');          // /* ... */   abgeschlossen
  t = t.replace(/\/\*[\s\S]*$/, '');                // /* ...      ohne Abschluss: bis Dateiende
  t = t.replace(/<!--[\s\S]*?-->/g, '');            // <!-- ... --> HTML, Markdown
  t = t.replace(/<!--[\s\S]*$/, '');                // <!-- ...    ohne Abschluss
  t = t.replace(/(^|[^:])\/\/[^\n]*/g, '$1');       // // ...      aber nicht in http://
  if (raute) t = t.replace(/(^|\s)#[^\n]*/gm, '$1'); // # ...      Shell, YAML

  // 2) Zeichenketten zuruecksetzen - ihr Inhalt ist Code, kein Kommentar.
  return t.replace(/\u0000(\d+)\u0000/g, (_, i) => tresor[Number(i)]);
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
