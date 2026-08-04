/**
 * W-09 — **Das Commit-Tor, erstmals ausgeuebt statt nur erwaehnt.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 8 von 8 blind, und zwar per Konstruktion:**
 *
 * ```text
 * grep -n 'commit-pruefen' scripts/__tests__/*.mjs
 *   auftragPruefen.test.mjs:781  'bash scripts/commit-pruefen.sh Botschaft pfad'
 *   auftragPruefen.test.mjs:791  skriptZielErlaubt(['bash','scripts/commit-pruefen.sh'])
 * ```
 *
 * **Beide Treffer sind Zeichenketten in der Erlaubnislisten-Zusage von W-07 — keiner fuehrt das
 * Tor aus.** 91 gruene Zusagen, und nicht eine davon uebt das Werkzeug aus, durch das JEDER
 * Commit dieses Projekts laeuft. *Eine Probe gegen null Abdeckung misst nichts; deshalb steht
 * hier die Abdeckungs-Messung statt einer Scheinzahl.*
 *
 * **Gefahren wird in einem WEGWERF-REPO unter `$TMPDIR`, nie im echten Baum.** Ein Test, der das
 * Commit-Tor im Arbeitsbaum ausuebt, verbucht echte Arbeit — genau der Unfall, gegen den das Tor
 * gebaut wurde. *Dieselbe Lehre wie am 01.08. um 22:11, als eine Wirkungsprobe wirklich gepusht
 * hat: die Probe muss dort laufen, wo ihre Wirkung folgenlos ist.*
 *
 * ---
 *
 * **W-04 UNABHAENGIG WIEDERGEFUNDEN — deshalb aendern die Proben eine VERFOLGTE Datei.**
 *
 * ```text
 * echo b > neu.txt  &&  bash scripts/commit-pruefen.sh "Probe" neu.txt
 *   -> error: pathspec 'neu.txt' did not match any file(s) known to git
 * ```
 *
 * `git commit -- <pfad>` greift bei einer UNGETRACKTEN Datei nicht — das Tor kann keine neue
 * Datei verbuchen. *Gemeldet am 01.08., als W-04 geschnitten, seither im Entwurf.* Meine erste
 * Fassung dieser Proben legte eine neue Datei an und lief genau dort hinein; zwei Zusagen waren
 * rot, **und beide zeigten auf W-04 statt auf W-09**. Sie aendern jetzt `anfang.txt`, damit sie
 * messen, was sie messen sollen. *Ein Messgeraet, das den falschen Fehler anzeigt, ist schlimmer
 * als eines, das schweigt.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { mkdtempSync, writeFileSync, readFileSync, existsSync, readdirSync, utimesSync } from 'node:fs';
import { execFileSync, spawn } from 'node:child_process';
import { join } from 'node:path';
import { tmpdir } from 'node:os';

const TOR = new URL('../commit-pruefen.sh', import.meta.url).pathname;

/** Ein frisches Repo im Systemtemp — mit dem Tor an derselben relativen Stelle wie im echten. */
function wegwerfRepo() {
  const verz = mkdtempSync(join(tmpdir(), 'w09-tor-'));
  execFileSync('git', ['init', '-q'], { cwd: verz });
  execFileSync('git', ['config', 'user.email', 'probe@example.invalid'], { cwd: verz });
  execFileSync('git', ['config', 'user.name', 'Probe'], { cwd: verz });
  execFileSync('mkdir', ['-p', join(verz, 'scripts')]);
  writeFileSync(join(verz, 'scripts', 'commit-pruefen.sh'), readFileSync(TOR, 'utf8'));
  writeFileSync(join(verz, 'anfang.txt'), 'erster Stand\n');
  execFileSync('git', ['add', 'anfang.txt'], { cwd: verz });
  execFileSync('git', ['commit', '-q', '-m', 'anfang'], { cwd: verz });
  return verz;
}

/** Faehrt das Tor und liefert Exitcode samt Ausgabe — ohne zu werfen. */
function tor(verz, ...args) {
  try {
    const aus = execFileSync('bash', ['scripts/commit-pruefen.sh', ...args],
      { cwd: verz, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] });
    return { code: 0, text: aus };
  } catch (e) {
    return { code: e.status ?? -1, text: `${e.stdout ?? ''}${e.stderr ?? ''}` };
  }
}

const lockSetzen = (verz, inhalt, alterSekunden) => {
  const p = join(verz, '.git', 'index.lock');
  writeFileSync(p, inhalt);
  if (alterSekunden > 0) {
    const t = new Date(Date.now() - alterSekunden * 1000);
    utimesSync(p, t, t);
  }
  return p;
};

const beiseiteGelegt = (verz) => {
  const wurzel = join(verz, '.git', '_locks_beiseite');
  if (!existsSync(wurzel)) return 0;
  return readdirSync(wurzel).reduce((n, tag) => n + readdirSync(join(wurzel, tag)).length, 0);
};

test('W-09/K-02: ein liegengebliebener 0-Byte-Lock verhindert den Commit NICHT mehr', () => {
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, '', 300);                       // 0 Byte, 5 Minuten alt = ein Rest

  const r = tor(verz, 'Probe: Rest-Lock', 'anfang.txt');
  assert.equal(r.code, 0, `das Tor bricht trotz Rest-Lock ab:\n${r.text}`);
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), false, 'der Rest liegt noch da');
  assert.equal(beiseiteGelegt(verz), 1, 'der Rest wurde nicht beiseitegelegt');
});

test('W-09/K-02 ROT: ein FRISCHER Lock mit INHALT bricht ab — und bleibt liegen', () => {
  // **Ein Tor, das jeden Lock wegzieht, ist gefaehrlicher als eines, das gar nicht aufraeumt:**
  // es zerstoert den laufenden Vorgang eines anderen.
  // *Seit Tor Teil 2 (03.08.) entscheidet nicht mehr die GROESSE allein, sondern die RUHE:
  // ein Lock unter 120s gilt als lebend, ganz gleich was drinsteht.*
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, 'ein laufender Vorgang\n', 5);

  const r = tor(verz, 'Probe: lebender Lock', 'anfang.txt');
  assert.notEqual(r.code, 0, 'das Tor committet ueber einen fremden Vorgang hinweg');
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), true, 'der fremde Lock wurde weggezogen');
});

test('Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest', () => {
  // *Der Evaluator wurde am 03.08. ZWEIMAL von einem 317s alten 885-kB-Lock blockiert und hat
  // dreifach belegt, dass nichts mehr lief (Groesse und mtime ueber 40s unveraendert, kein
  // git-Prozess, `lsof` nur lesend). Die alte Regel "0 Byte UND >=60s" konnte ihn nicht
  // erkennen — sie trennte die Faelle nur zur Haelfte.*
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, 'Rest eines abgestuerzten Laufs\n', 300);

  const r = tor(verz, 'Probe: stillstehender Rest', 'anfang.txt');
  assert.equal(r.code, 0, 'ein nachweislich toter Lock sperrt das Tor weiterhin');
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), false, 'der Rest liegt noch am alten Ort');
  assert.ok(beiseiteGelegt(verz) >= 1, 'der Rest wurde geloescht statt beiseitegelegt');
});

test('W-09/K-02 ROT: ein FRISCHER Lock bricht ab — Alter allein entscheidet nicht', () => {
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, '', 0);                         // 0 Byte, aber gerade erst entstanden

  const r = tor(verz, 'Probe: frischer Lock', 'anfang.txt');
  assert.notEqual(r.code, 0, 'ein Lock von gerade eben wird fuer einen Rest gehalten');
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), true, 'der frische Lock wurde weggezogen');
});

test('W-09/K-02: ein Lock IN DER TIEFE wird auch gefunden — `*.lock` ist ein Muster ohne Tiefe', () => {
  // **NACHTRAG 03.08., am eigenen Leib gemessen:** `.git/refs/heads/<zweig>.lock` hat den
  // Blaetter-Umzug DREIMAL blockiert. Er entsteht beim `commit` selbst, also am spaetesten
  // moeglichen Punkt — *wer ihn nicht wegraeumt, hat den Commit gebaut und verliert ihn im
  // letzten Schritt.*
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  const zweig = execFileSync('git', ['branch', '--show-current'], { cwd: verz, encoding: 'utf8' }).trim();
  const tief = join(verz, '.git', 'refs', 'heads', `${zweig}.lock`);
  writeFileSync(tief, '');
  const alt = new Date(Date.now() - 300 * 1000);
  utimesSync(tief, alt, alt);

  const r = tor(verz, 'Probe: Lock in der Tiefe', 'anfang.txt');
  assert.equal(r.code, 0, `der Ref-Lock blockiert den Commit weiterhin:\n${r.text}`);
  assert.equal(existsSync(tief), false, 'der Ref-Lock liegt noch da — die Suche hat keine Tiefe');
});

test('W-09/K-03: der Abbruch NENNT Dateiname, Groesse und Alter', () => {
  // *Ein „Abbruch" ohne Grund schickt den naechsten Leser suchen — dieselbe Auflage wie bei der
  // Erlaubnisliste in W-01.*
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, 'zwoelf Zeichen', 300);

  const r = tor(verz, 'Probe: Meldung', 'anfang.txt');
  assert.match(r.text, /index\.lock/, 'der Dateiname fehlt in der Meldung');
  assert.match(r.text, /\b14 Byte\b/, 'die Groesse fehlt in der Meldung');
  assert.match(r.text, /\b\d+s alt\b/, 'das Alter fehlt in der Meldung');
});

test('W-09/K-07: STUFE 5 — nach einem vollen Lauf liegt KEIN index.lock im Mount', () => {
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');

  const r = tor(verz, 'Probe: Stufe 5', 'anfang.txt');
  assert.equal(r.code, 0, `der Lauf ist gescheitert:\n${r.text}`);
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), false, 'ein index.lock ist entstanden');
});

test('W-09/K-07: der Ausweichpfad liegt AUSSERHALB des Mounts und ist je Prozess eigen', () => {
  // **Die Mutation „Index bleibt im Mount" kam blind durch** — meine erste Fassung pruefte nur,
  // dass `TMPDIR` im Quelltext VORKOMMT. *Das ist F-06: die Stelle statt der Wirkung.* Ein
  // `INDEX_HEIMAT=".git/ticket-index"` erfuellt sie ebenfalls und legt den Index genau dorthin
  // zurueck, wo er nicht hingehoert. **Gefragt wird jetzt der Pfad, den das Tor WIRKLICH setzt.**
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  const vorher = readdirSync(join(verz, '.git'));
  const r = tor(verz, 'Probe: Ausweichort', 'anfang.txt');
  assert.equal(r.code, 0, `der Lauf ist gescheitert:\n${r.text}`);

  // Nach dem Lauf darf im Mount KEIN neuer Index-Ort stehen. *Ein `INDEX_HEIMAT` unter `.git/`
  // erfuellt jede Quelltext-Zusage und legt den Index trotzdem genau dorthin zurueck, wo er
  // nicht hingehoert — sichtbar wird das nur an dem, was danach im Verzeichnis liegt.*
  const neu = readdirSync(join(verz, '.git')).filter((e) => !vorher.includes(e));
  assert.deepEqual(neu.filter((e) => /index/i.test(e)), [],
    `das Tor hat einen Index-Ort IM Mount angelegt: ${neu.join(', ')}`);

  const quelle = readFileSync(TOR, 'utf8');
  assert.match(quelle, /GIT_INDEX_FILE=/, 'der Index wird nicht verlegt');
  // **Auflage des Evaluators:** teilen sich zwei gleichzeitige Laeufe denselben externen Index,
  // waere die Kollision nur nach draussen gewandert statt zu verschwinden.
  assert.match(quelle, /index\.\$\$/, 'der Ausweichpfad ist nicht je Prozess eindeutig');
});

test('W-09/K-07: eine von aussen gesetzte GIT_INDEX_FILE wird NICHT ueberschrieben', () => {
  // *Die stille Falle: wer die Variable hart setzt, zieht einem Aufrufer den Index unter den
  // Fuessen weg, der bewusst einen eigenen benutzt.*
  const quelle = readFileSync(TOR, 'utf8');
  assert.match(quelle, /if \[ -z "\$\{GIT_INDEX_FILE:-\}" \]/,
    'die Variable wird bedingungslos gesetzt');
});

test('W-09/K-08: der PREIS von Stufe 5 steht im Kopf des Werkzeugs, in Klartext', () => {
  // *Ein Werkzeug, das eine Umgebung veraendert, ohne den Preis zu nennen, ist eine
  // Ueberraschung mit Halbwertszeit — dieselbe Klasse wie eine Naeherung ohne Vermerk (B10).*
  const kopf = readFileSync(TOR, 'utf8').split('\n').slice(0, 40).join('\n');
  assert.match(kopf, /STAGING/i, 'der Preis nennt den Staging-Zustand nicht');
  assert.match(kopf, /KEINE Arbeit geht verloren/i, 'der Kopf sagt nicht, was NICHT verloren geht');
});

test('W-09/K-01: die Aufraeumung steht VOR dem ersten echten git-Aufruf — relativ, nicht absolut', () => {
  // **BEFUND an den Planner:** K-01 nennt „hoechstens Zeile 35". Diese Zahl ist durch K-08
  // DESSELBEN Blattes unerfuellbar geworden — der Preis im Kopf laesst ihn um 25 Zeilen wachsen.
  // *Zwei Kriterien eines Blattes ziehen in verschiedene Richtungen; eines verlangt Text im
  // Kopf, das andere verbietet ihn implizit.* **Die AUSSAGE ist relativ pruefbar und bleibt es
  // fuer immer** — deshalb steht hier der Vergleich und nicht die Zahl.
  const zeilen = readFileSync(TOR, 'utf8').split('\n');
  const nr = (treffer) => zeilen.findIndex(treffer) + 1;

  const aufraeumung = nr((z) => z.includes('_locks_beiseite'));
  const ersterGit = nr((z) => /^\s*git\s/.test(z) || /^\s*execFileSync\('git'/.test(z));
  assert.ok(aufraeumung > 0, 'die Aufraeumung steht gar nicht mehr im Werkzeug');
  assert.ok(ersterGit > 0, 'kein git-Aufruf gefunden — die Zusage misst Leere');
  assert.ok(aufraeumung < ersterGit,
    `die Aufraeumung (Zeile ${aufraeumung}) steht hinter dem ersten git-Aufruf (Zeile ${ersterGit})`);
});

test('W-09/K-04: die Nachsorge am Ende bleibt — sie ersetzt die Vorsorge nicht', () => {
  // Die eine raeumt weg, was VORHER dalag; die andere, was DURCH diesen Lauf entstand.
  const quelle = readFileSync(TOR, 'utf8');
  const stellen = [...quelle.matchAll(/_locks_beiseite/g)].length;
  assert.ok(stellen >= 2, `nur ${stellen} Stelle(n) — Vorsorge ODER Nachsorge fehlt`);
});

/**
 * ── W-04 ──────────────────────────────────────────────────────────────────────────────────
 *
 * **Die Mutationsprobe VOR diesen Zusagen: 6 von 6 blind.**
 *
 * ```text
 * BLIND   M1 git add -A statt der Pfadliste
 * BLIND   M2 stagen VOR der Pruefung
 * BLIND   M3 auch getrackte Pfade nochmal stagen
 * BLIND   M4 die Meldung weglassen
 * BLIND   M5 bei Fehlschlag trotzdem stagen
 * BLIND   M6 `--` vor dem Pfad weglassen
 * ```
 *
 * *Alle sechs kommen an den elf W-09-Zusagen vorbei — die messen Locks und Index-Ort, nicht das
 * Stagen.* **Das ist kein Vorwurf an W-09: eine Zusage kann nur fangen, was es beim Schreiben
 * schon gab.** Es ist der Grund, warum die Probe VOR die Zusagen gehoert und nicht danach.
 *
 * **M1 und M5 sind die beiden, die wirklich weh taeten.** M1 sammelt die ungesicherte Arbeit
 * aller anderen Instanzen ein (R13, F-05) — am 01.08. lagen 19 fremde Dateien im Index. M5
 * hinterlaesst nach einem ABGELEHNTEN Aufruf einen halb gefuellten Index, den der naechste
 * Commit einer fremden Rolle stillschweigend mitnimmt. *Beide sind unsichtbar, bis sie fremde
 * Arbeit verbucht haben.*
 */

test('W-04/K-04: eine NEUE Datei wird verbucht — der Fall, an dem das Tor bisher scheiterte', () => {
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'neu.txt'), 'frisch\n');

  const r = tor(verz, 'W-04 Probe', 'neu.txt');
  assert.equal(r.code, 0, `Tor abgebrochen: ${r.text}`);

  const drin = execFileSync('git', ['show', '--name-only', '--format=', 'HEAD'],
    { cwd: verz, encoding: 'utf8' });
  assert.match(drin, /neu\.txt/, 'die neue Datei steht nicht im Commit');
  assert.match(r.text, /NEU\s+neu\.txt/, 'das Stagen geschah STILL — M4 (ein stiller Nebeneffekt am Tor)');
});

test('W-04/K-04 ROT: ein Pfad, der NICHT genannt wurde, bleibt draussen — das ist die eigentliche Zusage', () => {
  // Ohne diese Zusage belegt K-04 nur, dass ueberhaupt etwas passiert. **Sie faengt M1 und M3.**
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'genannt.txt'), 'a\n');
  writeFileSync(join(verz, 'FREMD.txt'), 'die Arbeit einer anderen Instanz\n');

  const r = tor(verz, 'nur die genannte', 'genannt.txt');
  assert.equal(r.code, 0, `Tor abgebrochen: ${r.text}`);

  const drin = execFileSync('git', ['show', '--name-only', '--format=', 'HEAD'],
    { cwd: verz, encoding: 'utf8' });
  assert.match(drin, /genannt\.txt/);
  assert.doesNotMatch(drin, /FREMD\.txt/, 'FREMD.txt wurde mitverbucht — M1/M3, Beifang am Tor');

  const lage = execFileSync('git', ['status', '--porcelain', '--', 'FREMD.txt'],
    { cwd: verz, encoding: 'utf8' });
  assert.match(lage, /^\?\?/, `FREMD.txt liegt im Index statt unberuehrt: ${lage.trim()}`);
});

test('W-04/K-05: eine abgelehnte Pruefung hinterlaesst KEINEN halb gefuellten Index', () => {
  // **Faengt M2 und M5.** Der naechste Commit einer fremden Rolle nimmt einen solchen Index mit,
  // ohne es zu merken — der Schaden faellt woanders an als der Fehler.
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'heil.txt'), 'in Ordnung\n');
  writeFileSync(join(verz, 'kaputt.md'), '```yaml\nnicht: [ zu\n```\n');

  const r = tor(verz, 'sollte abbrechen', 'heil.txt', 'kaputt.md');
  assert.notEqual(r.code, 0, 'das Tor hat trotz kaputtem YAML-Kopf committet');

  const gestagt = execFileSync('git', ['diff', '--cached', '--name-only'],
    { cwd: verz, encoding: 'utf8' }).trim();
  assert.equal(gestagt, '', `der abgelehnte Lauf hat gestagt: ${gestagt}`);
});

test('W-04/K-02: kein pauschales Stagen im Werkzeug — nicht -A, nicht Punkt, nicht Muster', () => {
  // **Faengt M1 am Text statt an der Wirkung.** Beides gehoert her: die Wirkungsprobe oben
  // braucht einen fremden Pfad im Baum, die Textprobe greift auch dann, wenn keiner daliegt.
  const quelle = readFileSync(TOR, 'utf8');
  const zeilen = quelle.split('\n').filter((z) => !/^\s*#/.test(z));
  const pauschal = zeilen.filter((z) => /git\s+add\s+(-A|--all|\.\s*$|\*)/.test(z));
  assert.deepEqual(pauschal, [], `pauschales Stagen im Tor: ${pauschal.join(' | ')}`);

  const benannt = zeilen.filter((z) => /git\s+add\s+--\s/.test(z));
  assert.ok(benannt.length >= 1, 'gar kein `git add --` im Tor — die Messung misst Leere');
});

test('W-04/Kante-4: der Pfad steht hinter `--`, damit eine Datei namens `-f` kein Schalter wird', () => {
  // **Faengt M6.** Der Fall ist selten und der Schaden gross: git liest die Datei als Option.
  const verz = wegwerfRepo();
  writeFileSync(join(verz, '-f'), 'heisst wie ein Schalter\n');

  const r = tor(verz, 'Datei heisst -f', '-f');
  assert.equal(r.code, 0, `Tor an der Schalter-Datei gescheitert: ${r.text}`);

  const drin = execFileSync('git', ['show', '--name-only', '--format=', 'HEAD'],
    { cwd: verz, encoding: 'utf8' });
  assert.match(drin, /-f/, 'die Datei `-f` steht nicht im Commit');
});

test('W-04/K-06: die vorhandenen Pruefungen, zum ersten Mal festgenagelt', () => {
  // Bestand, nicht Aenderung. Sechs Faelle, die das Tor seit dem 01.08. koennen soll und die
  // bis heute keine einzige Zusage hatte.
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'leer.txt'), '');
  writeFileSync(join(verz, 'kaputt.mjs'), 'const a = {;\n');
  writeFileSync(join(verz, 'kaputt2.md'), '```yaml\nnicht: [ zu\n```\n');
  writeFileSync(join(verz, 'heil2.txt'), 'geaendert\n');

  assert.notEqual(tor(verz, 'fehlt', 'gibt-es-nicht.txt').code, 0, 'fehlende Datei');
  assert.notEqual(tor(verz, 'leer', 'leer.txt').code, 0, 'leere Datei');
  assert.notEqual(tor(verz, 'unveraendert', 'anfang.txt').code, 0, 'unveraenderte Datei');
  assert.notEqual(tor(verz, 'syntax', 'kaputt.mjs').code, 0, '.mjs mit Syntaxfehler');
  assert.notEqual(tor(verz, 'yaml', 'kaputt2.md').code, 0, '.md mit kaputtem yaml-Kopf');

  const gut = tor(verz, 'alles heil', 'heil2.txt');
  assert.equal(gut.code, 0, `der heile Fall wurde abgelehnt: ${gut.text}`);
});

/**
 * ── NACHTRAG zur Gegenprobe: M3 und M5 ueberlebten die erste Fassung, und der Grund ist lehrreich
 *
 * **M5 (bei Fehlschlag trotzdem stagen) ist mit einer Index-Beobachtung NICHT zu fangen — weil
 * W-09 Stufe 5 den Schaden bereits unmoeglich macht.** Selbst nachgestellt:
 *
 * ```text
 * Wegwerf-Repo, Tor mit eingebauter M5-Mutation, abgelehnter Lauf:
 *   exit                       1
 *   git diff --cached          LEER          <- .git/index ist unberuehrt
 *   $TMPDIR/ticket-index/      index.<pid>   <- DORT liegt das Gestagte, und es stirbt mit dem Prozess
 * ```
 *
 * *Der halb gefuellte Index, vor dem K-05 warnt, kann eine fremde Rolle gar nicht mehr erreichen:
 * jeder Lauf hat seinen eigenen Index ausserhalb des Mounts.* **Zwei Scheiben greifen ineinander,
 * ohne dass es jemand geplant hat.**
 *
 * **Daraus folgt aber nicht, dass die Stelle egal ist — sondern dass sie STRUKTURELL geprueft
 * gehoert.** Eine Zusage, die eine Wirkung misst, die ein anderes Bauteil ohnehin garantiert,
 * ist gruen aus dem falschen Grund (F-06). *Deshalb steht unten die Reihenfolge selbst, gemessen
 * wie W-09/K-01: relativ, nicht als Zeilennummer.* **Faellt Stufe 5 je weg, haelt diese Zusage
 * weiter — die andere haette dann nie gewarnt.**
 *
 * **M3 (auch getrackte Pfade stagen) ist in der WIRKUNG folgenlos** — `git commit -- <pfad>`
 * verbucht ohnehin den Arbeitsbaum-Stand. *Was M3 kaputtmacht, ist die MELDUNG:* das Tor sagt
 * dann „NEU" zu einer Datei, die es seit Wochen gibt. **Eine Meldung, die luegt, ist schlimmer
 * als keine** — sie wird beim Lesen geglaubt.
 */

test('W-04/K-05 STRUKTUR: das Stagen steht HINTER dem Fehler-Riegel — relativ gemessen', () => {
  const zeilen = readFileSync(TOR, 'utf8').split('\n');
  const nr = (treffer) => zeilen.findIndex(treffer) + 1;

  const riegel = nr((z) => /^if \[ "\$FEHLER" -ne 0 \]/.test(z));
  assert.ok(riegel > 0, 'der Fehler-Riegel steht nicht mehr im Werkzeug');

  // **Nicht „das ERSTE Stagen kommt danach", sondern „VORHER kommt gar keins".** Die erste
  // Fassung fragte `riegel < erstesStagen` — und blieb blind, als die Mutation ein ZWEITES
  // `git add` in den Fehlerzweig setzte: das erste stand ja weiterhin hinten. *Eine Zusage, die
  // den ersten Treffer prueft, sagt nichts ueber den zweiten.*
  const stagenZeilen = zeilen
    .map((z, i) => [i + 1, z])
    .filter(([, z]) => !/^\s*#/.test(z) && /git\s+add\s/.test(z));
  assert.ok(stagenZeilen.length >= 1, 'kein `git add` gefunden — die Zusage misst Leere');

  const davor = stagenZeilen.filter(([n]) => n < riegel);
  assert.deepEqual(davor, [],
    `vor dem Fehler-Riegel (Zeile ${riegel}) wird gestagt: ${davor.map(([n, z]) => `${n}: ${z.trim()}`).join(' | ')}`);

  // **Und der Zweig SELBST ist der gefaehrliche Ort, nicht nur alles davor.** Zweiter Anlauf:
  // die vorige Fassung mass „vor Zeile des Riegels" — die Mutation setzte das Stagen INS `then`,
  // also dahinter, und kam durch. *Der Abbruchzweig ist genau der Pfad, auf dem nichts mehr
  // passieren darf: der Aufruf ist bereits abgelehnt.*
  const zweigEnde = zeilen.findIndex((z, i) => i >= riegel && /^fi$/.test(z)) + 1;
  assert.ok(zweigEnde > riegel, 'das Ende des Fehlerzweigs ist nicht auffindbar — die Zusage misst Leere');
  const imZweig = stagenZeilen.filter(([n]) => n > riegel && n <= zweigEnde);
  assert.deepEqual(imZweig, [],
    `im Abbruchzweig (Zeile ${riegel}–${zweigEnde}) wird gestagt: ${imZweig.map(([n, z]) => `${n}: ${z.trim()}`).join(' | ')}`);
});

test('W-04/K-04 ROT: eine GETRACKTE Datei bekommt keine NEU-Meldung — die Meldung darf nicht luegen', () => {
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');

  const r = tor(verz, 'nur geaendert, nicht neu', 'anfang.txt');
  assert.equal(r.code, 0, `Tor abgebrochen: ${r.text}`);
  assert.doesNotMatch(r.text, /NEU\s+anfang\.txt/,
    'das Tor nennt eine seit Langem verfolgte Datei „NEU" — M3');
});

/**
 * ── TOR-PAKET (Planner-Vergabe 9cbcf932) ──────────────────────────────────────────────────
 *
 * **Mutationsprobe VOR diesen Zusagen: die zwei Muster trafen NICHT** — die Konstrukte gab es
 * noch nicht. *Die Probe meldet „MUSTER TRIFFT NICHT" statt „BLIND"; das ist der Unterschied
 * zwischen einer Lücke und einem ungebauten Teil.* Die dritte (Altersschwelle auf 0) war
 * GEFANGEN, die vorhandenen Zusagen decken sie.
 *
 * **Gebaut wurde EINE der zwei bestellten Stellen. Die zweite habe ich gemessen und NICHT
 * gebaut** — Begründung unten bei der `lsof`-Zusage.
 */

test('Tor/GNU: GNU zuerst, BSD als Rueckfall — und die mtime muss aus ZIFFERN bestehen', () => {
  // *Die erste Fassung (Tor Teil 1) verlangte beide Dialekte in EINER Zeile:
  // `stat -f %m … || stat -c %Y …`. Am 03.08. gefahren und widerlegt: GNU-`stat -f` ist die
  // DATEISYSTEM-Auskunft, sie ignoriert `%m`, schreibt einen mehrzeiligen Block auf STDOUT
  // ("File: … Blocks: …") — der `||`-Zweig wird nie erreicht, MZEIT traegt Text, und die
  // Arithmetik stirbt mit "File: unbound variable". Genau der Abbruch, den Teil 1 beheben
  // sollte, blieb also bestehen. Die Reihenfolge ist deshalb Teil der Zusage, nicht Geschmack.*
  const quelle = readFileSync(TOR, 'utf8');
  const zeilen = quelle.split('\n').filter((z) => !/^\s*#/.test(z));

  const gnu = zeilen.findIndex((z) => /MZEIT=\$\(stat -c %Y/.test(z));
  const bsd = zeilen.findIndex((z) => /MZEIT=\$\(stat -f %m/.test(z));
  assert.ok(gnu >= 0, 'der GNU-Dialekt fehlt — auf Linux sperrt dann jeder liegende Lock das Tor');
  assert.ok(bsd >= 0, 'der BSD-Rueckfall fehlt — auf macOS bliebe die mtime ungemessen');
  assert.ok(gnu < bsd, 'BSD steht vor GNU: auf Linux gewinnt dann die Dateisystem-Auskunft');
  assert.ok(zeilen.some((z) => /\*\[!0-9\]\*/.test(z)),
    'die Ziffernpruefung fehlt — Text aus dem falschen Dialekt liefe ungeprueft in die Arithmetik');
});

test('Tor/GNU ROT: eine nicht messbare mtime bricht ab, statt sie zu raten', () => {
  // Ohne diesen Zweig waere `ALTER` leer, die Arithmetik ergaebe den heutigen Zeitstempel,
  // und ein frischer Lock sähe uralt aus — das Tor raeumte fremde, LEBENDE Locks weg.
  const quelle = readFileSync(TOR, 'utf8');
  assert.match(quelle, /STAT VERSAGT/, 'der Abbruch bei nicht messbarer mtime fehlt');
  const nachSTAT = quelle.slice(quelle.indexOf('STAT VERSAGT'));
  assert.match(nachSTAT.slice(0, 400), /exit 1/, 'STAT VERSAGT meldet, sperrt aber nicht');
});

test('Tor: der Alters-Zweig entscheidet weiterhin AUS ZWEI Merkmalen — Groesse UND Alter', () => {
  const quelle = readFileSync(TOR, 'utf8');
  const zeilen = quelle.split('\n').filter((z) => !/^\s*#/.test(z));
  const zweig = zeilen.find((z) => /GROESSE.*-eq 0/.test(z));
  assert.ok(zweig, 'der Entscheidungszweig ist nicht mehr auffindbar');
  assert.match(zweig, /ALTER/, 'das Alter ist aus der Entscheidung verschwunden');
});

/**
 * ── A-02 — EIN LOCK IST EIN REST, WENN IHN NIEMAND HAELT ───────────────────────────────────
 *
 * **Mutationsprobe VOR diesen Zusagen — vier von sieben blind:**
 *
 * ```text
 * M1 lsof entfernt                    GEFANGEN   (die Rueckfall-Zusage aus W-09 greift)
 * M2 lsof-Ergebnis ignoriert          BLIND
 * M3 Halter-Zweig faellt weg          BLIND
 * M4 harte Grenze fuer Inhalt weg     GEFANGEN
 * M5 Rueckfall raeumt MEHR            BLIND
 * M6 ENV_BLOCKED durch exit 1         BLIND
 * M7 Exitcode 3 -> 1, Zeile bleibt    Muster traf nicht - unten mit eigenem Anker nachgeholt
 * ```
 *
 * *Die vier blinden sind genau die Faelle, fuer die es vor A-02 keinen Begriff gab: das Tor
 * kannte weder Halter noch ENV_BLOCKED.* **Die Zahl misst den Ausgangszustand, nicht eine
 * Nachlaessigkeit der 23 bestehenden Zusagen.**
 */

/** Haelt eine Datei offen, bis `stop()` gerufen wird — ein echter fremder Halter. */
function halterFuer(pfad) {
  const kind = spawn(process.execPath, ['-e', `
    const fs = require('node:fs');
    const fd = fs.openSync(${JSON.stringify(pfad)}, 'a');
    process.stdout.write('offen\\n');
    setTimeout(() => { fs.closeSync(fd); }, 30000);
  `], { stdio: ['ignore', 'pipe', 'ignore'] });
  return new Promise((fertig) => {
    kind.stdout.once('data', () => fertig({ pid: kind.pid, stop: () => { try { kind.kill('SIGKILL'); } catch { /* schon fort */ } } }));
  });
}

test('A-02-2: ein Lock MIT HALTER bleibt liegen — egal wie alt, still und gross', async () => {
  // **Der Vorfall vom 04.08.:** zwei vollstaendige Indizes (je ~888 kB) wurden beiseitegeschoben,
  // weil sie ruhig aussahen. *Ruhe und Tod sind nicht dasselbe — `lsof` kennt den Unterschied.*
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  const p = lockSetzen(verz, 'x'.repeat(900), 400);
  const halter = await halterFuer(p);

  try {
    const r = tor(verz, 'Probe: gehaltener Lock', 'anfang.txt');
    assert.equal(existsSync(p), true, 'ein GEHALTENER Lock wurde weggezogen — der Vorfall wiederholt sich');
    assert.equal(r.code, 3, `erwartet Exitcode 3 (ENV_BLOCKED), kam ${r.code}`);
    assert.match(r.text, /ENV_BLOCKED:/, 'die ENV_BLOCKED-Zeile fehlt');
    assert.match(r.text, new RegExp(`Halter: .*${halter.pid}`), 'die Meldung nennt den Halter nicht');
  } finally {
    halter.stop();
  }
});

test('A-02-2 GEGENPROBE: derselbe Lock nach Prozessende — beiseite, Commit gelingt', async () => {
  // Ohne diese Haelfte waere „raeumt nie auf" ebenfalls gruen. *Erst der Unterschied zwischen
  // beiden Laeufen sagt, dass die Halterfrage wirkt und nicht bloss immer sperrt.*
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  const p = lockSetzen(verz, 'x'.repeat(900), 400);
  const halter = await halterFuer(p);
  halter.stop();
  await new Promise((r) => setTimeout(r, 400));

  const r = tor(verz, 'Probe: Halter ist fort', 'anfang.txt');
  assert.equal(existsSync(p), false, 'der verwaiste Lock liegt noch — das Tor raeumt gar nicht mehr');
  assert.equal(r.code, 0, `Commit gescheitert: ${r.text}`);
  assert.equal(beiseiteGelegt(verz), 1, 'der Rest wurde nicht beiseitegelegt');
});

test('A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (must_preserve)', () => {
  // Der Fall des Evaluators: 885 kB, 317 s, dreifach als tot belegt. Er war an der Basis bereits
  // gruen und bleibt es — **ohne ihn waere „raeumt ueberhaupt nichts mehr auf" eine gruene Loesung.**
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, 'x'.repeat(885_000), 317);

  const r = tor(verz, 'Probe: toter Riese', 'anfang.txt');
  assert.equal(r.code, 0, `das Tor sperrt einen nachweislich freien Lock: ${r.text.slice(0, 160)}`);
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), false, 'der freie Lock liegt noch');
});

test('A-02-3: OHNE lsof raeumt das Tor WENIGER, nie mehr', () => {
  // *Ein Werkzeug, das ohne sein Messgeraet mehr aufraeumt als mit, ist die gefaehrlichste
  // Bauart ueberhaupt.* Geprueft wird mit leerem PATH-Fund: `command -v lsof` schlaegt fehl.
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, 'x'.repeat(900), 400);          // Inhalt + alt: MIT lsof waere das beiseite

  let aus;
  try {
    aus = execFileSync('bash', ['scripts/commit-pruefen.sh', 'Probe: ohne lsof', 'anfang.txt'],
      { cwd: verz, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'], env: { ...process.env, PATH: '/usr/bin:/bin' } });
    aus = { code: 0, text: aus };
  } catch (e) {
    aus = { code: e.status ?? -1, text: `${e.stdout ?? ''}${e.stderr ?? ''}` };
  }
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), true,
    'ohne Halterauskunft wurde ein Lock MIT INHALT weggezogen — der Rueckfall raeumt mehr statt weniger');
  assert.notEqual(aus.code, 0, 'das Tor committet ohne jede Halterauskunft ueber einen Lock hinweg');
});

test('A-02-4: die Blockade nennt BEIDES — Exitcode 3 UND eine lesbare Zeile', async () => {
  // **Der Exitcode ist fuer Maschinen, die Zeile fuer Menschen.** Nur die Zeile zu pruefen waere
  // F-09: derselbe Text in einem Kommentar oder Logauszug zaehlte mit. Nur den Code zu pruefen
  // liesse einen Aufrufer ohne Grund stehen.
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  const p = lockSetzen(verz, 'x'.repeat(50), 400);
  const halter = await halterFuer(p);

  try {
    const r = tor(verz, 'Probe: Form der Blockade', 'anfang.txt');
    assert.equal(r.code, 3, `Exitcode ${r.code} statt 3 — ein Aufrufer kann Umgebung nicht von Fachfehler trennen`);
    const zeile = r.text.split('\n').find((z) => z.startsWith('ENV_BLOCKED:'));
    assert.ok(zeile, 'keine Zeile beginnt mit ENV_BLOCKED:');
    assert.match(zeile, /ENV_BLOCKED: .+ — .+ \(Halter: .+\)/, `Form verletzt: ${zeile}`);
  } finally {
    halter.stop();
  }
});

test('A-02-4 ROT: ein junger Lock ohne Halter wird NICHT geraeumt — er meldet ENV_BLOCKED', () => {
  // **Die Abweichung vom Wortlaut des Blattes, festgenagelt.** „Kein Halter" allein macht keinen
  // Rest: ein Vorgang kann seine Sperrdatei zwischen zwei Schritten kurz geschlossen haben.
  // *Ohne diese Zusage waere die hinreichende Lesart gruen — und zwei W-09-Schutzzusagen fielen.*
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, '', 0);

  const r = tor(verz, 'Probe: jung und frei', 'anfang.txt');
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), true, 'ein FRISCHER Lock wurde weggezogen');
  assert.equal(r.code, 3, `erwartet 3 (Umgebung), kam ${r.code}`);
  assert.match(r.text, /ENV_BLOCKED: lock zu jung/, 'der Grund fehlt oder ist ein anderer');
});
