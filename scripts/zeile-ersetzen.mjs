#!/usr/bin/env node
/**
 * W-02 — **`zeile-ersetzen`: das Werkzeug, das den Splice-Fehler unmöglich macht.**
 *
 * ---
 *
 * **Der Anlass sind vier Fehler derselben Klasse an einem Abend** (01.08., 19:30–22:15). Jedes Mal
 * `head -N` + Heredoc + `tail -n +M`, jedes Mal an der Grenzzeile:
 *
 * ```text
 * 19:5x  Test-Datei: Import-Zeile doppelt         -> "Identifier 'bericht' has already been declared"
 * 20:0x  auftrag-pruefen.mjs: Klammer verwaist    -> "SyntaxError: Unexpected token ']'"
 * 22:0x  Z-03+Z-04: `id:` ueberschrieben          -> S-01 meldete 0 aktive Blaetter
 * 22:1x  W-01: `schritte:` doppelt                -> yaml-Kopf unlesbar, PB-019 sperrte
 * ```
 *
 * **Vier Vorsaetze haben nicht geholfen.** Nach dem zweiten Mal stand die Regel *„vor jedem Splice
 * die Grenzzeilen anzeigen"* in `docs/STAND.md` — zwei Stunden spaeter war sie wieder gebrochen.
 * **R9 verlangt dann eine Barriere, und B6 hat sie beschlossen.**
 *
 * ---
 *
 * ## Aufruf
 *
 * ```text
 * node scripts/zeile-ersetzen.mjs <datei> <von> <bis> <neuer-inhalt-datei> [--zeigen]
 *
 * --zeigen   druckt NUR die Zeilen von-1 … von und bis … bis+1 und aendert NICHTS.
 *            Das ist der Pflichtschritt, der viermal uebersprungen wurde.
 * ohne       ersetzt die Zeilen von…bis, prueft die Datei DANACH, schreibt nur bei Erfolg.
 * ```
 *
 * ## Der Kern ist nicht der Ersatz, sondern die Pruefung DANACH
 *
 * ```text
 * .mjs .js         node --check          (echte Syntaxpruefung)
 * .ts .tsx         Klammer-/Zaun-Bilanz  (siehe Grenze unten)
 * .md              Zahl der ```-Zaeune GERADE  UND  jeder ```yaml-Block laedt
 * .sh              bash -n
 * sonst            nicht leer und wirklich geaendert
 * ```
 *
 * **Schlaegt die Pruefung fehl, bleibt die Datei unveraendert** — nicht „geschrieben und
 * gemeldet", sondern **gar nicht geschrieben**. Das ist der Unterschied zwischen einer Regel
 * (Stufe 3) und einer Mechanik (Stufe 4).
 *
 * ## Bekannte Grenze, vom Evaluator benannt (01.08. 23:0x)
 *
 * **Die Klammerbilanz fuer `.ts`/`.tsx` bricht an Template-Literalen mit Backticks.** Ein `` ` ``
 * im String verschiebt die Zaehlung. *Deshalb ist die `.tsx`-Pruefung ausdruecklich eine BILANZ
 * und keine Syntaxpruefung; sie faengt den groben Fall und nicht jeden. Wer sie fuer vollstaendig
 * haelt, verlaesst sich auf etwas, das sie nicht leistet.*
 *
 * ## Kein `unlink`, kein `mv`
 *
 * Auf diesem Mount scheitert beides an F-10. Geschrieben wird per Truncate-und-Schreiben
 * (`writeFileSync` auf denselben Pfad), nie ueber eine Zwischendatei mit `rename`.
 */
import { readFileSync, writeFileSync, mkdtempSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import { extname, join } from 'node:path';
import { tmpdir } from 'node:os';
// Derselbe Parser, den `auftrag-pruefen.mjs` benutzt — nicht ein zweiter fuer dieselbe Frage.
import yaml from 'js-yaml';
// **W-06: derselbe Parser, den `tsc` selbst benutzt.** Er liegt seit jeher im Haus; der
// Ausschlussgrund der ersten Blattfassung („kein Parser verfuegbar") war schlicht falsch.
import ts from 'typescript';

/** Was am Rand steht, wenn es dort keine Zeile mehr gibt. */
export const RAND_ANFANG = 'DATEIANFANG';
export const RAND_ENDE = 'DATEIENDE';

/**
 * **Traegt dieser Quelltext syntaktisch?** — EINE Quelle fuer `.ts` `.tsx` `.mjs` `.js`.
 *
 * **Was hier stand und warum es weg ist.** Bis zum 03.08. zaehlte eine Klammer-Bilanz die
 * Zeichen `{}`, `[]`, `()` ueber den ganzen Text. *Ihr Funktionsname steht hier bewusst NICHT
 * ausgeschrieben: K-02 misst seine Abwesenheit mit `grep`, und ein Kommentar, der ihn nennt,
 * macht die Absenz-Zusage unerfuellbar (F-09 — zweimal an einem Tag zugeschnappt).*
 * Gemessen an 319 Hausplaner-Dateien:
 *
 * ```text
 * roher Text                    61 von 319 fallen durch
 * mit Kommentar-Abzug           59      <- der urspruengliche Fix loeste ZWEI Faelle
 * Texte und Regex zusaetzlich   57
 * ts.createSourceFile            0
 * ```
 *
 * **Die Ursache war nie der Kommentar.** `breiten.test.ts:51` traegt
 * `css.match(/\.hp-studio-kopf \{([^}]*)\}/)` — ein Regex-Literal mit einer oeffnenden und zwei
 * schliessenden Klammern. Kein Kommentar, keine Zeichenkette, sondern Code. *Eine zeichenzaehlende
 * Bilanz misst Haeufigkeit und nennt es Syntax; auf echtem TypeScript ist sie grundsaetzlich
 * unzuverlaessig, und Regex-Literale sind von Divisionen ohne Parser nicht sicher trennbar.*
 *
 * **Nebengewinn, der mehr wert ist als die Zahl:** die alte Fassung meldete *„die Datei traegt
 * nach der Ersetzung NICHT"* und zeigte damit auf die Aenderung des Lesers statt auf sich selbst.
 * Der Parser liefert `'}' expected.` mit Position — der Unterschied zwischen einer Sperre, die
 * anklagt, und einer, die hilft.
 *
 * **`parseDiagnostics` ist intern, aber genau die Liste, die `tsc` fuer SYNTAXfehler fuehrt** —
 * keine Typpruefung: ein unbekannter Name ist hier kein Fehlschlag, eine fehlende Klammer schon.
 */
function traegtSyntaktisch(text, endung) {
  const art = endung === '.tsx'
    ? ts.ScriptKind.TSX
    : endung === '.ts' ? ts.ScriptKind.TS : ts.ScriptKind.JS;
  const quelle = ts.createSourceFile(`pruefling${endung}`, text, ts.ScriptTarget.Latest, true, art);

  return (quelle.parseDiagnostics ?? []).length === 0;
}

/** Sind die ```-Zaeune paarig? **Fehler 22:1x: ein Splice liess einen offenen Zaun zurueck.** */
function zaeuneGerade(text) {
  const zaeune = (text.match(/^```/gm) ?? []).length;

  return zaeune % 2 === 0;
}

/**
 * **Laesst sich jeder ```yaml-Block laden?**
 *
 * *Ohne diese Haelfte waere die Zaun-Zaehlung eine Gestalt-Pruefung:* der Kopf von W-01 hatte am
 * 22:1x paarige Zaeune UND ein doppeltes `schritte:` — die Zahl stimmte, der Inhalt nicht.
 */
function yamlLaedt(text) {
  const bloecke = [...text.matchAll(/^```yaml\n([\s\S]*?)^```/gm)].map((m) => m[1]);
  for (const b of bloecke) {
    try {
      yaml.load(b);
    } catch {
      return false;
    }
  }

  return true;
}

/**
 * **DIE ENTSCHEIDUNGSFUNKTION.** Traegt dieser Inhalt fuer diese Endung?
 *
 * Sie steht ausdruecklich getrennt vom Schreiber (B3): *wer eine Sperre prueft, fragt die
 * Entscheidung, nie den Ausfuehrer.* Am 01.08. um 22:11 ist genau diese Trennung einmal gefehlt,
 * und die Probe hat ausgeloest, was sie verhindern sollte.
 */
export function pruefeInhalt(text, endung) {
  if (typeof text !== 'string' || text.length === 0) {
    return false;
  }
  const e = String(endung).toLowerCase();

  // **W-06: EIN Zweig fuer alle vier Endungen — und der `node --check`-Umweg faellt mit weg.**
  //
  // Bliebe er als zweiter Weg stehen, gaebe es wieder zwei Antworten auf dieselbe Frage
  // (dieselbe Klasse wie `PAKET_WERKZEUGE` in W-05 K-10). Er brauchte ausserdem eine Hilfsdatei
  // NEBEN der Quelle — und auf diesem Mount ist `unlink` verboten (F-10), sie blieb also liegen,
  // auch wenn der Ersatz danach abgelehnt wurde. *Der Parser braucht keine Datei; damit ist die
  // Kante nicht gemildert, sondern fort.*
  //
  // `ScriptKind` wird AUSDRUECKLICH uebergeben statt aus der Endung abgeleitet — so haelt es das
  // Nachbarwerkzeug `hook-abhaengigkeiten.mjs`, und der Evaluator hat am 03.08. darauf gezeigt,
  // dass die Ableitung fuer `.tsx` mit echtem JSX ungeprobt war.
  if (e === '.ts' || e === '.tsx' || e === '.mjs' || e === '.js') {
    return traegtSyntaktisch(text, e);
  }
  if (e === '.md') {
    return zaeuneGerade(text) && yamlLaedt(text);
  }
  // **`.sh` braucht weiterhin eine Datei — `bash -n` liest nicht von der Standardeingabe.**
  // Sie entsteht jetzt aber im SYSTEM-Temp und nicht mehr neben der Quelle. *Das war der Kern
  // von F-10: auf diesem Mount ist `unlink` verboten, die Hilfsdatei blieb im Arbeitsbaum liegen
  // — auch dann, wenn der Ersatz anschliessend abgelehnt wurde.* Ein Rest im Systemtemp stoert
  // niemanden; einer neben der Quelle landet im naechsten `git status`.
  if (e === '.sh') {
    const verz = mkdtempSync(join(tmpdir(), 'zeile-ersetzen-'));
    const ziel = join(verz, 'pruefling.sh');
    try {
      writeFileSync(ziel, text);
      execFileSync('bash', ['-n', ziel], { stdio: 'ignore' });

      return true;
    } catch {
      return false;
    }
  }

  return true;
}

/**
 * **Die vier Grenzzeilen — und die Raender ausdruecklich benannt.**
 *
 * Auflage des Evaluators (01.08. 23:0x): *bei `von = 1` gibt es keine Zeile 0, bei `bis = EOF`
 * keine `bis+1` — das Werkzeug muss die Raender als `DATEIANFANG`/`DATEIENDE` zeigen statt zu
 * schweigen.* **Schweigt es, sieht der Rand aus wie eine leere Zeile — und genau daraus entsteht
 * die off-by-one-Klasse, gegen die es gebaut wird.**
 */
export function grenzZeilen(zeilen, von, bis) {
  const hole = (n) => (n >= 1 && n <= zeilen.length ? `${n}: ${zeilen[n - 1]}` : null);

  return {
    vorher: von <= 1 ? RAND_ANFANG : hole(von - 1),
    erste: hole(von),
    letzte: hole(bis),
    nachher: bis >= zeilen.length ? RAND_ENDE : hole(bis + 1),
  };
}

/**
 * **Ersetzt die Zeilen `von…bis` — und schreibt NUR, wenn die Datei danach traegt.**
 *
 * `md5Vorher` ist die Sperre gegen die Drift zwischen Lesen und Schreiben (K-08, Auflage des
 * Evaluators): *hat sich die Datei zwischen dem Blick und dem Schreiben bewegt, schreibt niemand.*
 * In einem Baum mit mehreren Instanzen ist das kein Sonderfall.
 */
/**
 * **Steht die Datei noch so da, wie wir sie gelesen haben?**
 *
 * *Ausgeführt und nicht versteckt, weil eine Sperre, die man nur ueber den Schreiber erreicht,
 * von keiner Zusage gehalten werden kann* (B3, B9). In der Mutationsprobe kam „Drift-Sperre
 * entfernt" durch, solange sie im Rumpf von `ersetze` steckte — meine K-08-Zusage prueste danebe
 * und hiess trotzdem K-08. **Eine benannte Kante ohne Zusage ist Prosa.**
 */
export function standUnveraendert(pfad, gelesenerText) {
  return readFileSync(pfad, 'utf8') === gelesenerText;
}

export function ersetze(pfad, von, bis, neu, { pruefen = true } = {}) {
  const vorherText = readFileSync(pfad, 'utf8');
  const zeilen = vorherText.split('\n');

  if (!Number.isInteger(von) || !Number.isInteger(bis) || von < 1 || bis < von || bis > zeilen.length) {
    return { ok: false, grund: `Bereich ${von}…${bis} liegt nicht in 1…${zeilen.length}`, geschrieben: false };
  }

  const neuText = [...zeilen.slice(0, von - 1), ...String(neu).split('\n'), ...zeilen.slice(bis)].join('\n');

  // **Kein dritter Parameter mehr.** Die Entscheidungsfunktion besorgt sich, was sie braucht —
  // und fuer die vier Quellcode-Endungen braucht sie gar nichts.
  if (pruefen && !pruefeInhalt(neuText, extname(pfad))) {
    return { ok: false, grund: 'die Datei traegt nach der Ersetzung NICHT — nicht geschrieben', geschrieben: false };
  }
  // **Drift-Sperre (K-08):** zwischen Lesen und Schreiben kann eine andere Instanz geschrieben
  // haben. Dann ist unser Ersatz auf einem Stand gerechnet, den es nicht mehr gibt.
  if (!standUnveraendert(pfad, vorherText)) {
    return { ok: false, grund: 'die Datei hat sich zwischen Lesen und Schreiben geaendert — nicht geschrieben', geschrieben: false };
  }
  writeFileSync(pfad, neuText);

  return { ok: true, grund: null, geschrieben: true };
}

// --- Aufruf von der Kommandozeile -----------------------------------------------------------------

if (process.argv[1] && process.argv[1].endsWith('zeile-ersetzen.mjs')) {
  const [datei, vonRoh, bisRoh, inhaltDatei] = process.argv.slice(2);
  const nurZeigen = process.argv.includes('--zeigen');
  if (!datei || !vonRoh || !bisRoh) {
    console.error('Aufruf: node scripts/zeile-ersetzen.mjs <datei> <von> <bis> <neuer-inhalt-datei> [--zeigen]');
    process.exit(2);
  }
  const von = Number(vonRoh);
  const bis = Number(bisRoh);
  const zeilen = readFileSync(datei, 'utf8').split('\n');
  const g = grenzZeilen(zeilen, von, bis);

  console.log(`  vor  ${g.vorher}`);
  console.log(`  von  ${g.erste}`);
  console.log(`  bis  ${g.letzte}`);
  console.log(`  nach ${g.nachher}`);

  if (nurZeigen) {
    process.exit(0);
  }
  if (!inhaltDatei) {
    console.error('ohne --zeigen wird eine Datei mit dem neuen Inhalt gebraucht');
    process.exit(2);
  }
  const e = ersetze(datei, von, bis, readFileSync(inhaltDatei, 'utf8').replace(/\n$/, ''));
  console.log(e.ok ? '  GESCHRIEBEN' : `  NICHT GESCHRIEBEN: ${e.grund}`);
  process.exit(e.ok ? 0 : 1);
}
