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
import { execFileSync } from 'node:child_process';
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

test('W-09/K-02 ROT: ein Lock mit INHALT bricht ab — und bleibt liegen', () => {
  // **Ein Tor, das jeden Lock wegzieht, ist gefaehrlicher als eines, das gar nicht aufraeumt:**
  // es zerstoert den laufenden Vorgang eines anderen.
  const verz = wegwerfRepo();
  writeFileSync(join(verz, 'anfang.txt'), 'zweiter Stand\n');
  lockSetzen(verz, 'ein laufender Vorgang\n', 300);

  const r = tor(verz, 'Probe: lebender Lock', 'anfang.txt');
  assert.notEqual(r.code, 0, 'das Tor committet ueber einen fremden Vorgang hinweg');
  assert.equal(existsSync(join(verz, '.git', 'index.lock')), true, 'der fremde Lock wurde weggezogen');
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
