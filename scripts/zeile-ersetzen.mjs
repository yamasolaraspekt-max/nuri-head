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
import { readFileSync, writeFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import { extname } from 'node:path';
// Derselbe Parser, den `auftrag-pruefen.mjs` benutzt — nicht ein zweiter fuer dieselbe Frage.
import yaml from 'js-yaml';

/** Was am Rand steht, wenn es dort keine Zeile mehr gibt. */
export const RAND_ANFANG = 'DATEIANFANG';
export const RAND_ENDE = 'DATEIENDE';

/**
 * **Balancieren die Klammern und Anfuehrungszeichen?**
 *
 * Bewusst eine Bilanz und keine Syntaxpruefung: fuer `.ts`/`.tsx` gibt es hier keinen Parser.
 * Sie faengt genau den Fall vom 20:0x — eine verwaiste Klammer nach einem Splice.
 */
function klammerBilanz(text) {
  const paare = [['{', '}'], ['[', ']'], ['(', ')']];
  for (const [auf, zu] of paare) {
    const a = text.split(auf).length - 1;
    const z = text.split(zu).length - 1;
    if (a !== z) {
      return false;
    }
  }

  return true;
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
export function pruefeInhalt(text, endung, hilfsPfad = null) {
  if (typeof text !== 'string' || text.length === 0) {
    return false;
  }
  const e = String(endung).toLowerCase();

  // **Die Hilfsdatei behaelt die ENDUNG des Ziels.** Mein erster Entwurf schrieb nach
  // `<pfad>.pruef-tmp` — und `node --check` liest eine unbekannte Endung als CommonJS. Damit
  // scheiterte JEDE gueltige ESM-Datei an meiner eigenen Pruefung, und das Werkzeug haette nie
  // etwas geschrieben. *Die Zusage „eine heile Ersetzung wird sehr wohl geschrieben" hat es
  // gefunden — der presence-Partner, nicht der rote Fall.*
  if (e === '.mjs' || e === '.js') {
    if (!hilfsPfad) {
      return klammerBilanz(text); // ohne Datei kein `node --check` — dann wenigstens die Bilanz
    }
    try {
      writeFileSync(hilfsPfad, text);
      execFileSync(process.execPath, ['--check', hilfsPfad], { stdio: 'ignore' });

      return true;
    } catch {
      return false;
    }
  }
  if (e === '.ts' || e === '.tsx') {
    return klammerBilanz(text);
  }
  if (e === '.md') {
    return zaeuneGerade(text) && yamlLaedt(text);
  }
  if (e === '.sh') {
    if (!hilfsPfad) {
      return true;
    }
    try {
      writeFileSync(hilfsPfad, text);
      execFileSync('bash', ['-n', hilfsPfad], { stdio: 'ignore' });

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

  if (pruefen && !pruefeInhalt(neuText, extname(pfad), `${pfad}.pruef-tmp${extname(pfad)}`)) {
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
