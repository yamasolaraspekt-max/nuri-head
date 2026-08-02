/**
 * AUF-87 — **der Validator: ein Blatt, dessen Prüfbefehle ins Leere greifen, ist nicht abgebbar.**
 *
 * Er liest den YAML-Kopf eines Auftragsblatts, fährt jeden darin genannten Prüfbefehl und meldet,
 * welcher fehlschlägt oder nichts findet. **Er prüft AUSFÜHRBARKEIT, nicht Richtigkeit** — ob ein
 * Kriterium fachlich stimmt, entscheidet weiterhin ein Mensch.
 *
 * ## Warum es drei Stufen sind und nicht zwei
 *
 * `grep -c` liefert bei null Treffern `exit 1` — das fällt auf. `sed -n '9999,9999p'` liefert bei
 * einer zu kurzen Datei **nichts bei `exit 0`** — und das sieht aus wie Erfolg. **Der zweite Fall
 * ist der gefährlichere**, und genau so ist dem Planner die Grundgesamtheit von T3 durchgerutscht:
 * der Befehl lief, er beschrieb nur einen Stand von vor vier Tagen.
 *
 * ## Warum eine Denylist
 *
 * Ein Auftragsblatt ist eine Datei, die jede Rolle schreiben darf. Ein Werkzeug, das ihre Inhalte
 * ungefiltert an die Shell gibt, ist ein Hebel, den niemand beabsichtigt hat. *Die Blätter sind
 * heute vertrauenswürdig; das Werkzeug soll es auch dann noch sein, wenn eines es nicht ist.*
 * **Übersprungene Befehle werden gemeldet, nicht verschwiegen.**
 *
 * ## Was er NICHT tut
 *
 * Er repariert nichts. Findet er in einem alten Blatt einen toten Befehl, sagt er es — **sonst
 * wäre der erste Lauf ein Massenumbau von 80 Dateien.**
 */
import { readFileSync } from 'node:fs';
import { execSync } from 'node:child_process';
import yaml from 'js-yaml';

/** Die Meldungsstufen. Reihenfolge = Dringlichkeit, sie bestimmt auch die Zusammenfassung. */
export const STUFEN = /** @type {const} */ ({
  FEHLSCHLAG: 'FEHLSCHLAG',
  NULLTREFFER: 'NULLTREFFER',
  VERDAECHTIG: 'VERDAECHTIG',
  UEBERSPRUNGEN: 'UEBERSPRUNGEN',
  NICHT_MASCHINELL: 'NICHT MASCHINELL',
  OK: 'OK',
});

/**
 * Muster, die einen Befehl vom Ausführen ausschließen.
 *
 * **Sie stehen hier und nicht im Blatt**: eine Liste, die der Prüfling selbst mitbringt, ist keine
 * Grenze. Erweitert wird sie bewusst, nicht nebenbei.
 */
export const DENYLIST = [
  'git commit', 'git push', 'git add', 'git reset', 'git checkout', 'git switch',
  'rm', 'mv', 'chmod', 'truncate', 'dd', 'tee',
  'umleitung', 'npm run build', 'npx vite build', 'curl', 'wget',
];

/**
 * **Muster, die ein GATE bezeichnen — kein Sicherheitsproblem, sondern eine Zustaendigkeitsfrage.**
 *
 * Getrennt von der Denylist, weil der Grund ein anderer ist. Die Denylist haelt Befehle zurueck,
 * die etwas *veraendern*. Diese Liste haelt Befehle zurueck, die *jemand anders faehrt*: die
 * Testsuiten und Buildketten gehoeren zum Generator und zum Evaluator, nicht in einen
 * Struktur-Validator, den der Planner startet.
 *
 * **Der Anlass, 01.08.2026, gemessen:** ueber `docs/auftraege/` standen **46** `pruefung.befehl`
 * mit `npm run` — der Verzeichnislauf haette sie alle ausgefuehrt und kam auf der Geraete-VM in
 * 45 Sekunden nicht durch. Die Blaetter deswegen einzeln umzubauen waeren 46 Vorsaetze gewesen;
 * **R9 verlangt an dieser Stelle eine Barriere.** Ein Wort statt 46 Dateien.
 *
 * *Ein Gate wird gemeldet, nicht verschwiegen — es zaehlt als NICHT MASCHINELL, damit niemand
 * es fuer geprueft haelt.*
 */
export const GATE_MUSTER = ['npm run', 'npx', 'yarn', 'pnpm', 'php artisan', 'composer'];

/**
 * W-01 — **die ERLAUBNISLISTE. Der Validator fuehrt nur noch aus, was hier steht.**
 *
 * ---
 *
 * **Der Anlass ist kein Gedankenspiel.** Am 01.08. um 20:01 hat ein Verzeichnislauf wirklich
 * veroeffentlicht — nicht versehentlich getippt, sondern **ausgefuehrt, weil es als
 * Abnahmekriterium in einem Blatt stand** (`b01/K-05`, ein Wrapper-Skript).
 *
 * **`DENYLIST` konnte das nicht fangen, und keine Erweiterung haette es gekonnt.** Sie prueft
 * Befehls-TEXT; ein Wrapper-Skript enthaelt keinen. Der Dateiname traegt kein einziges Muster —
 * *der Text sagt nichts darueber, was die Datei tut.* Eine Liste des Verbotenen kann nur fangen,
 * was jemand vorher gedacht hat.
 *
 * **Deshalb dreht W-01 die Richtung um.** Ein Blatt ist eine Datei, die jede Rolle schreiben darf,
 * und was darin steht, **passiert**. Was der Validator ausfuehrt, gehoert damit nicht in die Hand
 * dessen, der das Blatt schreibt.
 *
 * ---
 *
 * **Die Liste ist aus dem Bestand gemessen, nicht erdacht** (erste Woerter aller
 * `pruefung.befehl` in `docs/auftraege/`, 01.08.):
 *
 * ```text
 * 52 grep · 38 npm · 37 node · 28 git · 4 bash · 3 php · 1 ls · 1 head · 1 for · 1 find · 1 cd
 * ```
 *
 * **`npm`, `php`, `bash` und Wrapper stehen bewusst NICHT hier.** `npm`/`php` fangen die
 * `GATE_MUSTER` schon ab (Zustaendigkeit, nicht Sicherheit); `bash` und jedes `./skript` sind
 * genau der Fall vom 01.08.: **ihr Text sagt nichts ueber ihre Wirkung.**
 *
 * **`git` steht nur mit LESENDEN Unterbefehlen drin.** Ein blankes `git` waere die Luecke, durch
 * die die schreibenden Unterbefehle zurueckkaemen — dann haetten wir die Denylist nur an eine
 * andere Stelle verschoben.
 */
export const ALLOWLIST = [
  // Lesen und Zaehlen
  //
  // **W-07: `node` und `awk` sind hier RAUS.** Sie standen als blanke Namen auf der Liste und
  // machten damit genau das, was die Liste verhindern soll:
  //   `node /tmp/fremd.mjs`      beliebiges JS von jedem Pfad
  //   `node -e "…"`              das Programm steht IM Befehl, nicht einmal auf der Platte
  //   `awk 'BEGIN{system("x")}'` fuehrt jeden Befehl aus — die ganze Liste umgangen
  // *`bash` fiel durch und `node -e` nicht — dabei sagt `node -e` noch weniger ueber seine
  // Wirkung.* `node` kommt ueber `skriptZielErlaubt` zurueck, `awk` gar nicht: es hat keine
  // harmlose Form, `system()` gehoert zur Sprache. **0 Verwendungen in Blaettern, 1 offene Tuer.**
  'grep', 'ls', 'head', 'tail', 'find', 'wc', 'cat', 'sed',
  'sort', 'uniq', 'tr', 'cut', 'printf', 'echo', 'basename', 'dirname', 'stat', 'md5',
  // Git — ausdruecklich NUR lesend
  'git diff', 'git log', 'git grep', 'git show', 'git status', 'git rev-list',
  'git rev-parse', 'git ls-files', 'git branch', 'git cat-file', 'git blame',
  // **Nachbefund des Planners (01.08. 23:0x) — angenommen, aber ENGER als vorgeschlagen.**
  //
  // Der Befund stimmt: ein rein lesender Befehl, der UEBERSPRUNGEN wird, misst nichts und sieht
  // im Bericht trotzdem nicht rot aus. Vorgeschlagen war `git remote`.
  //
  // **Beim Eintragen gemessen, dass der Vorschlag zu weit greift:** die Zwei-Wort-Granularitaet
  // unterscheidet nicht zwischen `git remote -v` und `git remote add`. Dasselbe bei `git reflog`
  // (`expire`, `delete`) und `git config` (jedes `git config x y` SCHREIBT). Drei Tueren, die
  // niemand gemeint hat.
  //
  // Aufgenommen wird deshalb nur, was gar keine schreibende Form hat: **`git ls-remote`** — und
  // das ist ausgerechnet der Befehl, der am 01.08. drei falsche Push-Zuordnungen verhindert
  // haette (B5). *`git remote` bleibt draussen; wer es braucht, braucht ein Gate — so hat der
  // Planner PW-01 selbst gebaut.*
  'git ls-remote',
];

/**
 * **Die Glieder eines Befehls** — eine Kette ist so sicher wie ihr schwaechstes Glied.
 *
 * `grep x docs | sed 's/a/b/' | sort` sind DREI Befehle; wer nur den ersten prueft, laesst jede
 * Kette durch, die harmlos anfaengt.
 *
 * **Zeichenketten werden vorher geschuetzt.** Ein Aufruf wie
 * `node scripts/zaehle.mjs datei "'a'|'b'"` traegt ein `|` INNERHALB von Anfuehrungszeichen; naiv
 * getrennt zerfiele er in Unsinn, und das Bruchstueck saehe aus wie ein unbekannter Befehl.
 * *Dieselbe Falle wie in `zaehle.mjs`, dieselbe Loesung.*
 */
export function befehlsGlieder(befehl) {
  // **Getrennt wird in EINEM Durchgang, ohne Platzhalter.** Mein erster Entwurf legte die
  // Zeichenketten in einen Tresor und setzte Zahlen als Marken ein — und `head -n 3 datei` traegt
  // eine `3`. Der Ruecktausch machte daraus `head -n undefined datei`. *Die Zusage darunter hat
  // genau das gefangen; ich hatte den Fehler vorher benannt und trotzdem stehen lassen.*
  //
  // Ein Platzhalter muss etwas sein, das im Bestand nicht vorkommen kann — es ist einfacher, gar
  // keinen zu brauchen: der Scanner merkt sich, ob er gerade INNERHALB von Anfuehrungszeichen
  // steht, und trennt nur ausserhalb.
  const text = String(befehl);
  const glieder = [];
  let aktuell = '';
  let anfuehrung = null;

  for (let i = 0; i < text.length; i += 1) {
    const z = text[i];
    if (anfuehrung) {
      aktuell += z;
      if (z === anfuehrung) {
        anfuehrung = null;
      }
      continue;
    }
    if (z === "'" || z === '"') {
      anfuehrung = z;
      aktuell += z;
      continue;
    }
    if (z === '|' || z === ';') {
      glieder.push(aktuell);
      aktuell = '';
      if (z === '|' && text[i + 1] === '|') {
        i += 1;
      }
      continue;
    }
    if (z === '&' && text[i + 1] === '&') {
      glieder.push(aktuell);
      aktuell = '';
      i += 1;
      continue;
    }
    aktuell += z;
  }
  glieder.push(aktuell);

  return glieder.map((g) => g.trim()).filter(Boolean);
}

/**
 * **Der Unterbefehl von `git`, an den Optionen VORBEI.**
 *
 * **Befund des Planners gegen meine erste Fassung, und er trifft:** sie las die ersten zwei
 * Woerter. `git --no-optional-locks diff …` ergibt damit das Paar `git --no-optional-locks` —
 * nicht auf der Liste, also uebersprungen. *Das ist ausgerechnet die Form, die die Bauordnung
 * vorschreibt;* vier Blaetter benutzen sie, und alle vier waeren stumm gescheitert.
 *
 * **Die Auflösung darf keine Luecke oeffnen.** Nach dieser Funktion ist `git --no-optional-locks
 * push` das Paar `git push` — und faellt durch, weil `git push` nicht auf der Erlaubnisliste
 * steht. *Die Denylist allein haette es NICHT gefangen: ihr Muster `git push` sucht die zwei
 * Woerter nebeneinander, und dazwischen stand eine Option.*
 */
function gitUnterbefehl(woerter) {
  let i = 1;
  while (i < woerter.length && woerter[i].startsWith('-')) {
    // `-C <pfad>` und `-c <name=wert>` nehmen ein Argument mit.
    if (woerter[i] === '-C' || woerter[i] === '-c') {
      i += 1;
    }
    i += 1;
  }

  return woerter[i] ?? '';
}

/** Die einzige Heimat, aus der ein Skript aufgerufen werden darf. */
const SKRIPT_HEIMAT = 'scripts/';

/** Programme, die nicht selbst wirken, sondern ausfuehren, WAS man ihnen gibt. */
const SKRIPT_PROGRAMME = ['bash', 'sh', 'node'];

/**
 * **Das erste Wort, das kein Flag ist** — das Ziel eines Interpreter-Aufrufs.
 *
 * *Starr `woerter[1]` zu nehmen war die erste Fassung; sie sperrte `bash -x scripts/ok.sh`, weil
 * sie `-x` fuer den Pfad hielt.* Leere Zeichenkette, wenn es kein solches Wort gibt — `node`
 * allein hat kein Ziel und bekommt keins geschenkt.
 */
function ersteZielWort(woerter) {
  for (let i = 1; i < woerter.length; i += 1) {
    if (!woerter[i].startsWith('-')) {
      return woerter[i];
    }
  }

  return '';
}

/**
 * **Schreibt dieser `sed`-Aufruf die Datei um?** `sed` bleibt auf der Liste — als Filter ist es
 * genau das Werkzeug, das ein Blatt braucht. **Mit `-i` ist es kein Filter mehr, sondern eine
 * Zeichenketten-Chirurgie an einer Quelldatei — das, was B6 verbietet.**
 *
 * *Drei Schreibweisen, alle gemessen: `-i` · `--in-place` · `-ni` (zusammengezogen). Meine erste
 * Meldung nannte nur die erste — der Planner fand die anderen beiden.*
 */
function sedSchreibt(woerter) {
  return woerter.slice(1).some((w) => w === '--in-place' || w.startsWith('--in-place=')
    || (/^-[a-zA-Z]*i/.test(w)));
}

/**
 * **Darf dieser `bash`-Aufruf laufen?** Entschieden wird am ZIELPFAD, nicht am Programmnamen.
 *
 * **Warum es diese dritte Regelart gibt.** Die Liste kannte bisher Ein-Wort-Eintraege (`grep`)
 * und Wortpaare (`git log`). `bash` passt in keine von beiden: als Wort waere es eine Tuer zu
 * allem, als Paar muesste jedes Skript einzeln eingetragen werden. **Gemessen, was die Sperre
 * ohne diese Regel kostet:** vier Zusagen in drei Blaettern (`m2-bestand-skript`,
 * `auf87-n2-struktur` zweimal, `auf87-auftrag-pruefen`) rufen `bash scripts/…` — alle vier
 * wurden UEBERSPRUNGEN und sahen im Bericht trotzdem nicht rot aus. *Genau der Fall, den der
 * Planner bei `git ls-remote` beschrieben hat: eine Messung, die nichts misst und gruen wirkt.*
 *
 * **Was weiterhin durchfaellt:** jeder Pfad ausserhalb von `scripts/` (`bash /tmp/fremd.sh`),
 * und jeder Aufstieg daraus (`scripts/../…`). *Ohne die zweite Haelfte waere die erste eine
 * Einladung — `scripts/../` erfuellt den Praefix und zeigt trotzdem irgendwohin.*
 *
 * ---
 *
 * **W-07: derselbe Pruefer bedient jetzt auch `node`. EINER, nicht zwei.** *Wer dafuer eine
 * zweite Funktion anlegt, hat zwei Antworten auf dieselbe Frage — und die zweite driftet.*
 *
 * **Und er liest das erste NICHT-Flag-Wort statt starr `woerter[1]`.** Die alte Fassung sperrte
 * `bash -x scripts/ok.sh`, weil sie `-x` fuer das Ziel hielt — die richtige Richtung des Irrtums,
 * aber falsch. Mit dem ersten Nicht-Flag-Wort traegt sie auch `node --test scripts/…`.
 *
 * **`node -e "…"` faellt dabei von selbst durch, und das ist kein Zufall:** das erste Nicht-Flag-
 * Wort ist dann der PROGRAMMTEXT, und der faengt nicht mit `scripts/` an. *Ein Programm, das im
 * Befehl steht statt auf der Platte, hat kein Ziel — also auch kein erlaubtes.* Ebenso `node`
 * ohne Argument: kein Ziel, kein Durchlass.
 */
export function skriptZielErlaubt(woerter) {
  const ziel = ersteZielWort(woerter);

  return ziel.startsWith(SKRIPT_HEIMAT) && !ziel.includes('..');
}

/**
 * **Das erste Glied, das NICHT auf der Erlaubnisliste steht** — oder `null`, wenn alle erlaubt sind.
 *
 * Geprueft wird das fuehrende Wort, bei `git` das Wortpaar aus `git` und seinem Unterbefehl,
 * bei `bash`/`sh` der ZIELPFAD (siehe `skriptZielErlaubt`).
 * *Ein Glied, das mit `./` beginnt, faellt durch, ohne dass jemand seinen Inhalt kennen
 * muesste — und genau das ist der Punkt.*
 */
export function nichtErlaubtesGlied(befehl) {
  for (const glied of befehlsGlieder(befehl)) {
    const rein = glied.replace(/^[({\s]+/, '');
    const woerter = rein.split(/\s+/);
    const eins = woerter[0] ?? '';
    const istGit = eins === 'git';
    const zwei = istGit ? `git ${gitUnterbefehl(woerter)}` : woerter.slice(0, 2).join(' ');

    // **Vor der Liste, weil die Liste diesen Fall nicht ausdruecken kann.** `bash`, `sh` und
    // `node` stehen dort nicht und sollen dort auch nicht stehen — erlaubt ist nicht das
    // Programm, sondern das Ziel.
    if (SKRIPT_PROGRAMME.includes(eins)) {
      if (skriptZielErlaubt(woerter)) {
        continue;
      }

      // Die Meldung nennt das ZIEL, nicht nur das Programm. *Ein „nicht erlaubt: node" schickt
      // den naechsten Leser suchen, obwohl der Grund im Argument steht.*
      return `${eins} ${ersteZielWort(woerter) || '(ohne Ziel)'}`.trim();
    }

    // **`sed` bleibt erlaubt — aber nur als Filter.** Mit `-i` schreibt es die Datei um; das ist
    // keine Messung mehr, sondern ein Eingriff, und der gehoert nicht in ein Abnahmekriterium.
    if (eins === 'sed' && sedSchreibt(woerter)) {
      return 'sed -i';
    }

    if (ALLOWLIST.includes(zwei) || ALLOWLIST.includes(eins)) {
      continue;
    }
    // Bei `git` nennt die Meldung das PAAR, nicht nur das Wort `git`. *Ein „nicht erlaubt: git"
    // laesst offen, welcher Unterbefehl gemeint war — und schickt den naechsten Leser suchen.*
    if (istGit) {
      return zwei.trim();
    }

    return eins || glied;
  }

  return null;
}

/**
 * Wie ein Muster erkannt wird — **als Wortform, nicht als Zeichenkette.**
 *
 * **Befund `AUF-87-B2` (P2), und er war berechtigt:** die erste Fassung suchte mit
 * `befehl.includes('rm ')`. Neun realistische Schreibweisen schlüpften durch — `echo x >datei`
 * (Umleitung ohne Leerzeichen), `rm<TAB>-rf`, `git  commit` mit zwei Leerzeichen, und
 * **`git switch`**, der moderne Ersatz für das gelistete `git checkout`.
 *
 * Deshalb steht hier je Muster ein Ausdruck statt einer Zeichenkette:
 * - Wortmuster (`rm`, `mv`, `dd`) greifen nur als **eigenes Wort** — `rm` fängt `rm -rf`,
 *   aber nicht `npm` oder `format`.
 * - Zwei-Wort-Muster erlauben **beliebigen Zwischenraum** — `git  commit` ist `git commit`.
 * - `umleitung` ist kein Wort, sondern der Fall `>` / `>>` in jeder Schreibweise.
 */
function musterAusdruck(muster) {
  if (muster === 'umleitung') return />>?/;
  const teile = muster.split(' ');
  if (teile.length > 1) return new RegExp(`\\b${teile.join('\\s+')}\\b`);
  return new RegExp(`(^|[\\s;&|(])${muster}([\\s;&|)]|$)`);
}

/** Der erste `​```yaml`-Block einer Datei — der Kopf. */
export function lieseKopfRoh(text) {
  return lieseAlleBloecke(text)[0] ?? null;
}

/**
 * **ALLE** `​```yaml`-Blöcke einer Datei, in Reihenfolge.
 *
 * **Befund `AUF-87-N2 / K-06`, und er kam aus meinem eigenen ersten Bestandslauf:** die erste
 * Fassung las nur den Kopf und meldete *„2 yaml-Blöcke, geprüft wurde der ERSTE"*. Ehrlich, aber
 * folgenlos — **die ganze R19-Umstellung des Planners steht in zweiten Blöcken** (`measurements:`),
 * und für den Validator war sie unsichtbar. *Ein Werkzeug, das die Hälfte nicht sieht, meldet
 * Vollständigkeit, die es nicht hat.*
 */
export function lieseAlleBloecke(text) {
  const zeilen = text.split('\n');
  const bloecke = [];
  let start = -1;
  for (let i = 0; i < zeilen.length; i += 1) {
    const z = zeilen[i].trim();
    if (start < 0 && z === '```yaml') { start = i; continue; }
    if (start >= 0 && z === '```') { bloecke.push(zeilen.slice(start + 1, i).join('\n')); start = -1; }
  }
  return bloecke;
}

/** Wie viele YAML-Blöcke die Datei überhaupt hat — für die Meldung, dass es mehr als einen gibt. */
export function zaehleBloecke(text) {
  return text.split('\n').filter((z) => z.trim() === '```yaml').length;
}

/**
 * Alle Prüfbefehle eines Kopfes — **als MENGE, nicht nach Muster.**
 *
 * Gesammelt werden `scope.population_command` **und** jedes `kriterien[].pruefung.befehl`. Wer nur
 * nach einem Muster sucht, findet den nächsten neuen Ort nicht — das ist F-01, die Klasse mit vier
 * Ausprägungen.
 */
export function sammleBefehle(kopf) {
  const gefunden = [];
  const auftrag = kopf?.auftrag ?? kopf ?? {};
  const scope = kopf?.scope ?? auftrag?.scope;
  if (typeof scope?.population_command === 'string' && scope.population_command.trim()) {
    gefunden.push({ id: 'scope.population_command', befehl: scope.population_command.trim(), typ: null });
  }
  const kriterien = kopf?.kriterien ?? auftrag?.kriterien ?? [];
  for (const k of Array.isArray(kriterien) ? kriterien : []) {
    const id = k?.id ?? '(ohne id)';
    const p = k?.pruefung ?? {};
    if (typeof p.befehl === 'string' && p.befehl.trim()) {
      // `erwartet` und `ausgangswert` reisen mit - S-07/S-08 vergleichen die Ausgabe damit.
      gefunden.push({
        id, befehl: p.befehl.trim(), typ: p.typ ?? null, ausgefuehrt_von: k?.ausgefuehrt_von ?? null,
        erwartet: p.erwartet ?? null, ausgangswert: k?.ausgangswert ?? null,
      });
    } else if (p.typ && p.typ !== 'befehl') {
      // Kein Befehl, aber eine Prüfung: der Validator darf NICHT so tun, als hätte er das geprüft.
      gefunden.push({ id, befehl: null, typ: p.typ, ausgefuehrt_von: k?.ausgefuehrt_von ?? null });
    }
  }
  return gefunden;
}

/** Steht der Befehl auf der Denylist? Liefert das Muster, damit die Meldung den Grund nennt. */
export function verbotenesMuster(befehl) {
  return DENYLIST.find((m) => musterAusdruck(m).test(befehl)) ?? null;
}

/** Bezeichnet der Befehl ein Gate (Testsuite, Build, Artisan)? Liefert das Muster. */
export function gateMuster(befehl) {
  return GATE_MUSTER.find((m) => musterAusdruck(m).test(befehl)) ?? null;
}

/** Einen einzelnen Eintrag bewerten — ohne zu werfen. */
export function pruefeEintrag(eintrag, arbeitsverzeichnis) {
  if (!eintrag.befehl) {
    return {
      ...eintrag, stufe: STUFEN.NICHT_MASCHINELL,
      hinweis: `typ: ${eintrag.typ}${eintrag.ausgefuehrt_von ? ` · ausgefuehrt_von: ${eintrag.ausgefuehrt_von}` : ''}`,
    };
  }
  const muster = verbotenesMuster(eintrag.befehl);
  if (muster) {
    return { ...eintrag, stufe: STUFEN.UEBERSPRUNGEN, hinweis: `enthaelt "${muster}"` };
  }
  const gate = gateMuster(eintrag.befehl);
  if (gate) {
    return {
      ...eintrag, stufe: STUFEN.NICHT_MASCHINELL,
      hinweis: `GATE: faehrt "${gate}" — gehoert zum Generator, nicht in den Validator-Lauf`,
    };
  }
  // W-01: **die Erlaubnisliste, zuletzt.** Denylist und Gate behalten Vorrang, damit die Meldung
  // den genauen Grund nennt. Ein Wrapper faellt hier durch, ohne dass jemand seinen Inhalt kennen
  // muss — und genau das ist der Punkt.
  //
  // **Diese vier Zeilen haben am 01.08. gefehlt, und das Fehlen hat ein zweites Mal
  // veroeffentlicht.** Die Liste und ihre Prueffunktion standen schon in der Datei; nur der
  // AUFRUF fehlte. *Ein Mechanismus, den niemand aufruft, ist keine Barriere, sondern ein
  // Kommentar mit Klammern.*
  const fremd = nichtErlaubtesGlied(eintrag.befehl);
  if (fremd) {
    return {
      ...eintrag, stufe: STUFEN.UEBERSPRUNGEN,
      hinweis: `"${fremd}" steht nicht auf der Erlaubnisliste — der Validator fuehrt nur Lesendes aus`,
    };
  }
  try {
    const ausgabe = execSync(eintrag.befehl, {
      cwd: arbeitsverzeichnis, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'], timeout: 120_000,
    });
    if (ausgabe.trim() === '') {
      return { ...eintrag, stufe: STUFEN.VERDAECHTIG, hinweis: 'exit 0, aber KEINE Ausgabe' };
    }
    return {
      ...eintrag, stufe: STUFEN.OK, ausgabe: ausgabe.trim(),
      hinweis: `${ausgabe.trim().split('\n').length} Zeile(n)`,
    };
  } catch (fehler) {
    const code = fehler?.status ?? '?';
    const kette = brechendesGlied(eintrag.befehl, arbeitsverzeichnis);
    if (code === 1 && kette) {
      return {
        ...eintrag, stufe: STUFEN.NULLTREFFER,
        hinweis: `\`${kette.glied}\` findet nichts (exit 1) — ${kette.rest} weitere(s) Glied(er) NICHT gelaufen`,
      };
    }
    return { ...eintrag, stufe: STUFEN.FEHLSCHLAG, hinweis: `exit ${code}` };
  }
}

/**
 * **Die Barriere fuer F-07 und die zweite Haelfte von F-04.**
 *
 * **F-07 — „Bestand nicht gemessen, sondern nachgebaut"** (Zaehler 5): ein Auftrag beschreibt als
 * *zu bauen*, was schon steht. *Am 01.08. lag `geometry/fangKern.ts` seit Tagen fertig da und wurde
 * von nichts benutzt — beinahe haette ich ihn ein zweites Mal bauen lassen.*
 *
 * **F-04 — „Zahl behauptet statt gemessen"** (Zaehler 5): der `ausgangswert` im Blatt ist eine
 * Messung zum Zeitpunkt des Schreibens. **Er veraltet, und niemand merkt es** — bis die Abnahme
 * gegen eine Zahl prueft, die nie gestimmt hat.
 *
 * **Was hier passiert:** die Kriterien laufen ohnehin. Ihre Ausgabe wird jetzt gegen `erwartet`
 * und `ausgangswert` gehalten:
 *
 * - Ausgabe erfuellt `erwartet` **schon vor dem Bau** ⇒ `STEHT SCHON` (F-07). *Nichts zu tun,
 *   oder das Kriterium misst das Falsche.*
 * - Ausgabe passt nicht zum `ausgangswert` ⇒ `AUSGANGSWERT VERALTET` (F-04/F-03).
 *
 * **Bewusst konservativ:** verglichen wird nur, was eindeutig ist — eine nackte Zahl oder
 * `mindestens N`. Alles andere wird stillschweigend uebergangen. *Ein Werkzeug, das bei jedem
 * zweiten Blatt falschen Alarm gibt, wird abgeschaltet, und dann faengt es auch die echten
 * Faelle nicht mehr.*
 */
export function vergleicheErwartung(eintrag) {
  const zahl = (s) => {
    const m = String(s ?? '').trim().match(/^-?\d+/);
    return m ? Number(m[0]) : null;
  };
  const ist = zahl(eintrag.ausgabe);
  if (ist === null || eintrag.stufe !== STUFEN.OK) return null;

  const erwartetRoh = String(eintrag.erwartet ?? '').trim();
  const mindestens = erwartetRoh.match(/^mindestens\s+(\d+)/i);
  const nackt = /^-?\d+$/.test(erwartetRoh) ? Number(erwartetRoh) : null;

  const ausgang = zahl(eintrag.ausgangswert);
  // **Eine WACHE ist kein Bauziel.** Traegt das Blatt denselben Wert als `ausgangswert` wie als
  // `erwartet`, sagt es ausdruecklich: "das ist heute schon so und soll so bleiben" - etwa
  // "keine CSS-Variable in der Zeichenflaechen-Palette". S-07 darauf loszulassen hiesse, jede
  // Wache als Fehler zu melden. Gemessen am 01.08.: drei von acht Meldungen waren genau das.
  const istWache = ausgang !== null && (
    (nackt !== null && ausgang === nackt) || (mindestens && ausgang >= Number(mindestens[1]))
  );
  if (istWache) {
    // Eine Wache meldet kein "steht schon" - aber wenn die Messung vom eigenen Ausgangswert
    // abweicht, ist die Wache **heute schon gebrochen** oder der Ausgangswert war nie richtig.
    // Genau so ist am 01.08. eine falsche Grundzahl in PB-023/K-03 aufgefallen (Blatt 0, echt 1).
    return ist === ausgang ? null
      : { regel: 'S-08', id: eintrag.id, text: `WACHE STIMMT NICHT — Blatt sagt ${ausgang}, gemessen ${ist} (F-04)` };
  }

  if (mindestens && ist >= Number(mindestens[1])) {
    return { regel: 'S-07', id: eintrag.id, text: `STEHT SCHON — Messung ${ist} erfuellt "${erwartetRoh}" bereits vor dem Bau (F-07)` };
  }
  if (nackt !== null && ist === nackt) {
    return { regel: 'S-07', id: eintrag.id, text: `STEHT SCHON — Messung ${ist} ist bereits der Zielwert (F-07)` };
  }
  if (ausgang !== null && ausgang !== ist) {
    return { regel: 'S-08', id: eintrag.id, text: `AUSGANGSWERT VERALTET — Blatt sagt ${ausgang}, gemessen ${ist} (F-04/F-03)` };
  }
  return null;
}

/**
 * Welches Glied einer `&&`-Kette bricht, und wie viele danach nicht mehr laufen?
 *
 * **`AUF-87-N2 / K-07` — meine Wahl, im Voraus gemeldet.** Der T5-Befehl verkettet drei `grep`;
 * der letzte sucht `collapsed|klappZu|schieneZu` und findet **0** — genau das ist das gewünschte
 * Ergebnis, es beweist die Lücke. **`grep` liefert dafür exit 1 und reißt die Kette.**
 *
 * *Der Befehl ist richtig, sein Exitcode ist es nicht.* Die Alternativen hätten die Arbeit auf den
 * Menschen verschoben — `|| true` in 89 Blättern, oder ein Kopffeld, das vorher weiß, welches
 * Glied null liefert. **Das Werkzeug kann es selbst sehen.**
 *
 * **Die Unterscheidung bleibt sichtbar:** nur ein *suchender* Befehl mit exit 1 gilt als
 * Nulltreffer. `exit 127` (Befehl existiert nicht) und jeder andere Code bleiben `FEHLSCHLAG`.
 */
const SUCHENDE = /^\s*(grep|rg|ag|find)\b/;

export function brechendesGlied(befehl, arbeitsverzeichnis) {
  const glieder = befehl.split(/&&/).map((g) => g.trim()).filter(Boolean);
  if (glieder.length < 2) {
    // Kein Kettenfall: ein einzelner suchender Befehl mit exit 1 ist ebenfalls ein Nulltreffer.
    return SUCHENDE.test(befehl) ? { glied: befehl.slice(0, 60), rest: 0 } : null;
  }
  for (let i = 0; i < glieder.length; i += 1) {
    const glied = glieder[i];
    if (!SUCHENDE.test(glied)) continue;
    try {
      execSync(glied, { cwd: arbeitsverzeichnis, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'], timeout: 60_000 });
    } catch (f) {
      if (f?.status === 1) return { glied: glied.slice(0, 60), rest: glieder.length - i - 1 };
      return null;
    }
  }
  return null;
}

/**
 * **Die fünf Strukturprüfungen aus `AUFTRAGSSCHEMA §7` (AUF-87-N2).**
 *
 * `AUF-87` prüft AUSFÜHRBARKEIT — läuft der Befehl. Diese hier prüfen **STRUKTUR** — ob das Blatt
 * überhaupt so gebaut ist, dass eine Abnahme möglich wäre. *„Beides zusammen ergibt erst das Gate."*
 *
 * **Genau diese fünf, keine sechste.** Ein Meta-System, das jede Konvention erzwingt, wird
 * abgeschaltet; diese fünf sind an echten Rückweisungen belegt.
 */
/**
 * **Die Barriere fuer F-03 und F-12 — „Messung aelter als der Baum".**
 *
 * Der Pruefende misst einen Stand, den der Bauende schon verlassen hat. *Am 30.07. hat das viermal
 * Zeit gekostet; beim dritten Mal wurde es teuer.* **R14 verlangte bisher, dass jemand daran denkt,
 * `git log -1` vorher und nachher zu vergleichen.** Hier tut es das Werkzeug: die Kriterien laufen
 * ueber Sekunden bis Minuten, und wenn eine andere Rolle in dieser Zeit committet, sind alle
 * gemessenen Zahlen von einem Baum, den es nicht mehr gibt.
 */
export function baumStand(arbeitsverzeichnis = process.cwd()) {
  try {
    return execSync('git --no-optional-locks rev-parse HEAD', {
      cwd: arbeitsverzeichnis, encoding: 'utf8', stdio: ['ignore', 'pipe', 'ignore'],
    }).trim();
  } catch {
    return null;
  }
}

export function strukturBefunde(kopf) {
  const befunde = [];
  // **S-09 / F-08b** — „Eine Entscheidung aendert den Auftrag und steht nur in Tafel und Ledger,
  // nicht im Blatt." Ein Kopf OHNE `status` kann diese Entscheidung gar nicht tragen: er ist von
  // aussen nicht unterscheidbar in aktiv, gesperrt oder laengst erledigt. Am 01.08. trugen 17
  // Blaetter `aktiv`, obwohl die meisten gebaut waren - genau dieser Fall, nur andersherum.
  {
    const a = kopf?.auftrag ?? kopf ?? {};
    if (kopf && !a?.status) {
      befunde.push({ regel: 'S-09', id: a?.id ?? '(ohne id)', text: 'Kopf ohne `status` — die Lage des Blattes steht dann nur in Tafel und Ledger (F-08b)' });
    }
  }
  const auftrag = kopf?.auftrag ?? kopf ?? {};
  const kriterien = kopf?.kriterien ?? auftrag?.kriterien ?? [];
  const liste = Array.isArray(kriterien) ? kriterien : [];

  // S-02: jedes Kriterium hat typ UND einen Befehl — oder ist manuell MIT Begründung.
  for (const k of liste) {
    const id = k?.id ?? '(ohne id)';
    const p = k?.pruefung ?? {};
    if (!k?.typ) befunde.push({ regel: 'S-02', id, text: 'kein `typ` am Kriterium' });
    const hatBefehl = typeof p.befehl === 'string' && p.befehl.trim();
    const istManuell = p.typ && p.typ !== 'befehl';
    const hatBegruendung = Boolean(k?.begruendung || k?.grenze || p?.schritte || p?.erwartet);
    if (!hatBefehl && !istManuell) {
      befunde.push({ regel: 'S-02', id, text: 'weder `pruefung.befehl` noch `pruefung.typ`' });
    } else if (!hatBefehl && istManuell && !hatBegruendung) {
      befunde.push({ regel: 'S-02', id, text: `manuell (\`${p.typ}\`) ohne Begruendung/Schritte` });
    }
  }

  // S-03: jedes P0/P1-`absence`-Kriterium braucht einen presence/behavioural-Partner.
  //
  // **Die Prüfung mit dem höchsten Wert.** Bei AUF-83-T2 waren K-01 bis K-04 `absence` mit einem
  // `grep`; der Evaluator hat die entfernte Navigation zurückgeholt — kein Test wurde rot.
  // *Ohne Partner hat man nicht aufgeräumt, sondern entfernt.*
  const hatPartner = liste.some((k) => k?.typ === 'presence' || k?.typ === 'behavioural');
  for (const k of liste) {
    const krit = String(k?.kritikalitaet ?? '');
    if (k?.typ === 'absence' && (krit === 'P0' || krit === 'P1') && !hatPartner) {
      befunde.push({ regel: 'S-03', id: k?.id ?? '(ohne id)', text: `absence ${krit} ohne presence/behavioural-Partner im Blatt` });
    }
  }

  // S-04: jedes `coverage`-Kriterium braucht eine Grundgesamtheit.
  const scope = kopf?.scope ?? auftrag?.scope;
  const hatPopulation = typeof scope?.population_command === 'string' && scope.population_command.trim();
  for (const k of liste) {
    if (k?.typ === 'coverage' && !hatPopulation && !k?.pruefung?.befehl) {
      befunde.push({ regel: 'S-04', id: k?.id ?? '(ohne id)', text: 'coverage ohne `population_command` und ohne eigenen Befehl' });
    }
  }

  // S-05: jeder Ausschluss nennt Grund UND Entscheider.
  const aus = scope?.ausschluesse ?? auftrag?.ausschluesse ?? [];
  for (const [i, a] of (Array.isArray(aus) ? aus : []).entries()) {
    const wo = a?.stelle ? `"${String(a.stelle).slice(0, 40)}"` : `#${i + 1}`;
    if (!a?.grund) befunde.push({ regel: 'S-05', id: wo, text: 'Ausschluss ohne `grund`' });
    if (!a?.entschieden_von) befunde.push({ regel: 'S-05', id: wo, text: 'Ausschluss ohne `entschieden_von`' });
  }
  return befunde;
}

/**
 * S-01 — **genau EIN Blatt trägt `status: aktiv`.**
 *
 * Diese Prüfung ist die einzige, die **über** ein Blatt hinausreicht; sie läuft deshalb über die
 * Menge der geprüften Dateien. **Der Beleg ist frisch:** am 30.07. trug die Auftragstafel sieben
 * Steuerungsmarken statt einer, sechs davon auf abgenommene Posten. *Wer sie nach §1 abholt, zieht
 * einen erledigten Auftrag.*
 */
export function aktiveBlaetter(ergebnisse) {
  return ergebnisse.filter((e) => e.status === 'aktiv').map((e) => e.pfad);
}

/**
 * **Die Barriere fuer F-08 — „Leerlauf eines Bauenden".**
 *
 * R16 verlangt: *mindestens zwei baubare Auftraege liegen jederzeit vor der Rolle.* Das war bis
 * heute ein Vorsatz — und am 01.08. um 11:20 ist er gebrochen: der Generator hatte genau EIN Blatt,
 * es kam mit einem Befund zurueck, und er stand. **Der Planner hat es selbst ausgeloest.**
 *
 * **Baubar heisst `aktiv` oder `bereit`.** Nicht baubar: `gesperrt` (Vorbedingung offen), `ruht`
 * (Zustand nicht nachgemessen), `erledigt`, `zurueckgestellt`.
 */
export function baubareBlaetter(ergebnisse) {
  return ergebnisse.filter((e) => e.status === 'aktiv' || e.status === 'bereit').map((e) => e.pfad);
}

/**
 * Ein ganzes Blatt prüfen.
 *
 * **Ein Blatt ohne Kopf ist kein Fehlschlag.** 67 der 80 Blätter im Bestand haben keinen — ein
 * Werkzeug, das bei ihnen rot wird, wird abgeschaltet, und dann fängt es auch die neuen nicht mehr.
 */
export function pruefeBlatt(pfad, arbeitsverzeichnis = process.cwd()) {
  const text = readFileSync(pfad, 'utf8');
  const roh = lieseKopfRoh(text);
  if (roh === null) {
    // PB-019: ein Blatt OHNE Kopf ist weiterhin kein Fehlschlag - 67 von 80 haben keinen.
    // ABER: nennt es sich selbst `status: aktiv`, wird nach ihm GEBAUT. Dann ist der fehlende
    // Kopf keine Altlast, sondern eine Luecke im laufenden Betrieb - und die sperrt.
    return { pfad, kopf: false, bloecke: 0, eintraege: [], aktivOhneKopf: /status:\s*aktiv/.test(text) };
  }
  let kopf;
  try {
    kopf = yaml.load(roh);
  } catch (fehler) {
    // Ein UNLESBARER Kopf ist immer ein Fehlschlag: jemand hat einen geschrieben, und er traegt nicht.
    return { pfad, kopf: false, unlesbar: String(fehler?.message ?? fehler), bloecke: zaehleBloecke(text), eintraege: [], aktivOhneKopf: true };
  }
  // K-06: ALLE Blöcke lesen. Der erste ist der Kopf; die weiteren tragen seit R19 die Messungen.
  const weitere = lieseAlleBloecke(text).slice(1).map((roh) => { try { return yaml.load(roh); } catch { return null; } });
  const eintraege = sammleBefehle(kopf).map((e) => pruefeEintrag(e, arbeitsverzeichnis));
  for (const [i, block] of weitere.entries()) {
    if (!block) continue;
    for (const e of sammleBefehle(block)) {
      eintraege.push(pruefeEintrag({ ...e, id: `block${i + 2}.${e.id}` }, arbeitsverzeichnis));
    }
  }
  const messungen = weitere.filter(Boolean).filter((b) => b.measurements || b.messungen).length;
  const auftrag = kopf?.auftrag ?? kopf ?? {};
  const status = auftrag?.status ?? null;
  // S-07/S-08 gelten nur, solange das Blatt noch gebaut werden SOLL. Nach dem Bau ist
  // "erfuellt" der Normalfall - dort waere die Meldung Laerm und wuerde das Werkzeug entwerten.
  const erwartungen = (status === 'bereit' || status === 'aktiv')
    ? eintraege.map(vergleicheErwartung).filter(Boolean)
    : [];
  return {
    pfad, kopf: true, bloecke: zaehleBloecke(text), eintraege,
    status, struktur: strukturBefunde(kopf), messbloecke: messungen, erwartungen,
  };
}

/** Ein Bericht als Text. **Nichts wird gekappt** — eine Liste, die bei zehn abschneidet, liest sich
 *  wie „zehn Probleme" und ist „mindestens zehn". */
export function bericht(ergebnis) {
  const zeilen = [`── ${ergebnis.pfad}`];
  if (!ergebnis.kopf) {
    if (ergebnis.unlesbar) zeilen.push(`   SPERRE  KOPF UNLESBAR: ${ergebnis.unlesbar}`);
    else if (ergebnis.aktivOhneKopf) zeilen.push('   SPERRE  KEIN KOPF, aber `status: aktiv` — nach diesem Blatt wird gebaut (PB-019)');
    else zeilen.push('   KEIN KOPF (kein Fehler — aeltere Blaetter haben keinen)');
    return zeilen.join('\n');
  }
  if (ergebnis.bloecke > 1) {
    zeilen.push(`   ${ergebnis.bloecke} yaml-Bloecke gelesen`
      + (ergebnis.messbloecke ? ` (davon ${ergebnis.messbloecke} mit Messungen)` : ''));
  }
  for (const b of ergebnis.erwartungen ?? []) {
    zeilen.push(`   ${('SPERRE ' + b.regel).padEnd(16)} ${String(b.id).padEnd(24)} ${b.text}`);
  }
  for (const b of ergebnis.struktur ?? []) {
    zeilen.push(`   ${('STRUKTUR ' + b.regel).padEnd(16)} ${String(b.id).padEnd(24)} ${b.text}`);
  }
  if (ergebnis.eintraege.length === 0) {
    zeilen.push('   SPERRE  KEIN PRUEFBEFEHL im Kopf gefunden — ein Kopf ohne Befehl misst nichts (PB-019)');
  }
  for (const e of ergebnis.eintraege) {
    zeilen.push(`   ${e.stufe.padEnd(16)} ${String(e.id).padEnd(24)} ${e.hinweis}`);
    if (e.befehl && e.stufe !== STUFEN.OK) {
      zeilen.push(`   ${''.padEnd(16)} ${''.padEnd(24)} $ ${e.befehl.replace(/\s+/g, ' ').slice(0, 150)}`);
    }
  }
  const zahl = (s) => ergebnis.eintraege.filter((e) => e.stufe === s).length;
  zeilen.push(`   ── ${ergebnis.eintraege.length} Eintrag/Eintraege: `
    + `${zahl(STUFEN.OK)} OK · ${zahl(STUFEN.VERDAECHTIG)} verdaechtig · ${zahl(STUFEN.FEHLSCHLAG)} Fehlschlag · `
    + `${zahl(STUFEN.NULLTREFFER)} nulltreffer · `
    + `${zahl(STUFEN.UEBERSPRUNGEN)} uebersprungen · ${zahl(STUFEN.NICHT_MASCHINELL)} nicht maschinell`
    + `${(ergebnis.struktur?.length ?? 0) > 0 ? ` · ${ergebnis.struktur.length} STRUKTUR-Befund(e)` : ''}`);
  return zeilen.join('\n');
}

// --- CLI -----------------------------------------------------------------------------------------

const istDirekterAufruf = process.argv[1] && import.meta.url.endsWith(process.argv[1].split('/').pop());
if (istDirekterAufruf) {
  const dateien = process.argv.slice(2);
  if (dateien.length === 0) {
    console.error('Aufruf: auftrag-pruefen.sh <blatt.md> [weitere.md ...]');
    process.exit(2);
  }
  let fehlschlaege = 0;
  const alle = [];
  const baumVorher = baumStand();
  for (const pfad of dateien) {
    const e = pruefeBlatt(pfad);
    alle.push(e);
    console.log(bericht(e));
    fehlschlaege += e.eintraege.filter((x) => x.stufe === STUFEN.FEHLSCHLAG).length;
    fehlschlaege += (e.struktur ?? []).length;
    // S-07 "steht schon" sperrt: ein Blatt, das Vorhandenes bauen laesst, kostet einen ganzen
    // Durchlauf. S-08 "Ausgangswert veraltet" meldet nur - die Zahl kann aus gutem Grund wandern.
    fehlschlaege += (e.erwartungen ?? []).filter((x) => x.regel === 'S-07').length;
    // PB-019 - der Befund lautete: "der Validator BENENNT `KEIN KOPF`, gibt aber exit 0; sechs
    // aktive Blaetter kaemen so durch". Ein Gate, das nur redet, ist keine Barriere (R9).
    if (!e.kopf && e.aktivOhneKopf) fehlschlaege += 1;
    if (e.kopf && e.eintraege.length === 0) fehlschlaege += 1;
  }
  // S-01 gilt über die Menge, nicht je Blatt — sie kann erst am Ende beantwortet werden.
  const aktive = aktiveBlaetter(alle);
  if (dateien.length > 1) {
    if (aktive.length === 1) {
      console.log(`\n── STRUKTUR S-01  genau ein aktiver Auftrag: ${aktive[0]}`);
    } else {
      console.log(`\n── STRUKTUR S-01  ${aktive.length} Blaetter mit \`status: aktiv\` — erwartet genau EINES`);
      for (const p of aktive) console.log(`                  ${p}`);
      if (aktive.length > 1) fehlschlaege += 1;
    }
    // S-06 / F-08: die Schlange darf nicht leerlaufen. Siehe `baubareBlaetter`.
    // (S-02 bis S-05 sind vergeben; S-03 prueft absence-Kriterien ohne Partner.)
    const baubar = baubareBlaetter(alle);
    if (baubar.length >= 2) {
      console.log(`── STRUKTUR S-06  ${baubar.length} baubare Auftraege in der Schlange (R16 verlangt mindestens 2)`);
    } else {
      console.log(`\n── STRUKTUR S-06  NUR ${baubar.length} baubare(r) Auftrag/Auftraege — R16 verlangt mindestens 2.`);
      console.log('                  F-08 "Leerlauf eines Bauenden", eingetreten am 01.08. um 11:20.');
      console.log('                  Der Bauende steht, sobald dieses eine Blatt zurueckkommt.');
      for (const p of baubar) console.log(`                  ${p}`);
      fehlschlaege += 1;
    }
  }
  // S-10 / F-03 + F-12: hat sich der Baum waehrend der Messung bewegt?
  const baumNachher = baumStand();
  if (baumVorher && baumNachher && baumVorher !== baumNachher) {
    console.log(`\n── STRUKTUR S-10  DER BAUM HAT SICH WAEHREND DER MESSUNG BEWEGT (F-03/F-12)`);
    console.log(`                  vorher  ${baumVorher.slice(0, 8)}`);
    console.log(`                  nachher ${baumNachher.slice(0, 8)}`);
    console.log('                  Jede Zahl oben stammt aus einem Baum, den es nicht mehr gibt. Noch einmal fahren.');
    fehlschlaege += 1;
  }
  // **Der Exitcode meldet nur FEHLSCHLAG.** „Verdaechtig" und „nicht maschinell" sind Hinweise an
  // einen Menschen, keine Gruende, eine Kette abzubrechen — sonst wird das Werkzeug abgeschaltet.
  process.exit(fehlschlaege > 0 ? 1 : 0);
}
