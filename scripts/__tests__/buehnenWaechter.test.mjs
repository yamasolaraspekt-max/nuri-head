/**
 * A-04 — **Der Waechter findet eine laufende Buehne auf einer Nicht-Testdatenbank,
 * egal wie sie gestartet wurde.**
 *
 * ---
 *
 * **Der Anlass:** zweimal hat das Aufzaehlen von Aufrufformen versagt — A-03 riegelt
 * `artisan serve`, benutzt wurde `php -S`. Deshalb misst A-04 den ZUSTAND (welche
 * Serverprozesse laufen, auf welcher aufgeloesten Datenbank), nicht die Aufrufform.
 *
 * **Der Fixture-Weg (Blatt, Rest 2):** alle Probeprozesse leben in einem WEGWERF-Verzeichnis
 * mit EIGENER `.env` und erfundenen Datenbanknamen. Es wird KEINE echte Buehne gestartet:
 * der `artisan`-Stub schlaeft nur, und die `php -S`-Proben dienen ein LEERES Verzeichnis aus —
 * kein Laravel, keine Datenbankverbindung, kein Zugriff auf `ticket` oder `ticket_testing`.
 * Der erwartete Name `ticket_testing` kommt im POSITIVFALL als blosse Zeichenkette in
 * Prozessumgebung bzw. Wegwerf-`.env.testing` vor (anders ist Exit 0 nicht zusagbar),
 * verbindet aber nichts: keiner dieser Prozesse oeffnet je eine Datenbank.
 *
 * **Die Test-Naht `BUEHNEN_WAECHTER_NUR_PIDS`** beschraenkt die Betrachtung auf die eigenen
 * Probeprozesse — NUR in den Positivfaellen, damit eine zufaellig mitlaufende fremde Buehne
 * sie nicht faelscht. Die Negativfaelle laufen OHNE Naht: eine Mutation, die die Suche
 * verengt (Naht immer aktiv, oder nur der eigene Baum — die Proben leben ausserhalb des
 * Repos), faellt genau dort.
 */
import { test, before, after } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdirSync, mkdtempSync, rmSync, chmodSync } from 'node:fs';
import { execFileSync, spawn } from 'node:child_process';
import { setTimeout as schlafe } from 'node:timers/promises';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const WAECHTER = new URL('../buehnen-waechter.sh', import.meta.url).pathname;
const BUEHNE = new URL('../browser-buehne.sh', import.meta.url).pathname;
const WURZEL = new URL('../..', import.meta.url).pathname;

/** Faehrt den Waechter und liefert Exitcode samt Ausgabe — ohne zu werfen. */
function waechter(env = {}) {
  try {
    const aus = execFileSync('bash', [WAECHTER], {
      cwd: WURZEL, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'], timeout: 120_000,
      env: { ...process.env, ...env },
    });
    return { code: 0, text: aus };
  } catch (e) {
    return { code: e.status ?? -1, text: `${e.stdout ?? ''}${e.stderr ?? ''}` };
  }
}

// ── Wegwerf-Verzeichnis (Rest 2): eigene .env, erfundene Namen, ausserhalb des Repos ─────────
let basis; // das Wegwerf-Verzeichnis
let leer; // leeres Docroot fuer die php -S-Proben
const kinder = [];

before(() => {
  basis = mkdtempSync(join(tmpdir(), 'zz-a04-wegwerf-'));
  leer = join(basis, 'leer');
  mkdirSync(leer);
  // Die EIGENE .env des Wegwerf-Verzeichnisses (erfundener Name). Der Waechter liest sie NIE —
  // sie liegt hier, damit keine Probe je auf eine echte Env-Datei zurueckfallen koennte.
  writeFileSync(join(basis, '.env'), 'DB_DATABASE=zz_wegwerf_fantasie\n');
  // Fuer den Positivfall A-04-3 (artisan serve, korrekt mit APP_ENV=testing gestartet):
  // die Wegwerf-.env.testing traegt den erwarteten Namen als Zeichenkette — verbunden wird nichts.
  writeFileSync(join(basis, '.env.testing'), 'DB_DATABASE=ticket_testing\n');
  // Der artisan-STUB: schlaeft nur. `php artisan serve` startet damit KEINEN Server.
  writeFileSync(join(basis, 'artisan'), '<?php sleep(120);\n');
});

after(() => {
  for (const kind of kinder) {
    try { kind.kill('SIGKILL'); } catch { /* schon beendet */ }
  }
  rmSync(basis, { recursive: true, force: true });
});

/** Startet eine Probe im Wegwerf-Verzeichnis und wartet, bis sie steht. */
async function probe(args, extraEnv = {}) {
  const kind = spawn('php', args, {
    cwd: basis, stdio: 'ignore',
    env: { PATH: process.env.PATH, HOME: process.env.HOME, ...extraEnv },
  });
  kinder.push(kind);
  await schlafe(250);
  assert.equal(kind.exitCode, null, `Probeprozess 'php ${args.join(' ')}' ist sofort gestorben`);
  return kind;
}

/** Ein zufaelliger hoher Port je Probe — kollidiert er doch, stirbt die Probe hoerbar (assert oben). */
let portZaehler = 49500 + (process.pid % 5000);
const naechsterPort = () => (portZaehler += 7);

test('A-04-1/2: beide Startformen, falsche UND unbekannte Datenbank — ALLE gemeldet, exit 3, nichts beendet', async () => {
  // VIER Proben in EINEM Lauf (Kante 1: alle melden, nicht die erste):
  const pFalsch = await probe(['-S', `127.0.0.1:${naechsterPort()}`, '-t', leer], { DB_DATABASE: 'zz_ticket_testing' });
  const pKopie = await probe(['-S', `127.0.0.1:${naechsterPort()}`, '-t', leer], { DB_DATABASE: 'ticket_testing_kopie' });
  const pOhne = await probe(['-S', `127.0.0.1:${naechsterPort()}`, '-t', leer]);
  const pServeOhne = await probe(['artisan', 'serve']);

  const r = waechter(); // OHNE Naht: der volle Prozessscan muss die Proben ausserhalb des Repos finden

  assert.equal(r.code, 3, `erwartet Exitcode 3, kam ${r.code}\n${r.text}`);

  // A-04-1: die Meldung traegt PID, Startbefehl und den GEFUNDENEN Datenbanknamen.
  assert.match(r.text, new RegExp(`BUEHNE FALSCH\\s+PID ${pFalsch.pid}\\b`), 'die falsche php -S-Buehne fehlt in der Meldung');
  assert.match(r.text, /zz_ticket_testing/, 'der gefundene Datenbankname fehlt in der Meldung');
  assert.match(r.text, /-S 127\.0\.0\.1:/, 'der Startbefehl fehlt in der Meldung');

  // Gleichheit, kein Muster: eine Aufweichung auf Praefix/Suffix/Teilstring liesse eine der
  // beiden Varianten durch — `zz_ticket_testing` (Suffix) oder `ticket_testing_kopie` (Praefix).
  assert.match(r.text, new RegExp(`BUEHNE FALSCH\\s+PID ${pKopie.pid}\\b`), 'ticket_testing_kopie kommt durch — der Vergleich ist kein exaktes Gleich mehr');

  // A-04-2, der Kern: BEIDE Startformen ohne Datenbank-Auskunft werden als UNSICHER erkannt.
  assert.match(r.text, new RegExp(`BUEHNE UNSICHER\\s+PID ${pOhne.pid}\\b`), 'php -S ohne DB_DATABASE gilt nicht als unsicher');
  assert.match(r.text, new RegExp(`BUEHNE UNSICHER\\s+PID ${pServeOhne.pid}\\b`), 'artisan serve ohne APP_ENV gilt nicht als unsicher');
  assert.match(r.text, /UNBEKANNT/, 'die unbekannte Datenbank wird nicht als UNBEKANNT benannt');

  // A-04-4 (must_preserve, verhaltensseitig): nach dem Lauf leben alle Proben noch.
  for (const kind of [pFalsch, pKopie, pOhne, pServeOhne]) {
    assert.equal(kind.exitCode, null, `der Waechter hat PID ${kind.pid} beendet — er darf nur melden`);
    assert.doesNotThrow(() => process.kill(kind.pid, 0), `PID ${kind.pid} ist nach dem Waechterlauf verschwunden`);
  }
});

test('A-04-2 ROT: die wirkungslose Form zaehlt NICHT als Sicherheit — DB_DATABASE bei artisan serve', async () => {
  // *Genau der Irrtum des 05.08.: `DB_DATABASE=ticket_testing php artisan serve` SIEHT richtig
  // aus, Laravel reicht die Variable aber nicht durch (ServeCommand.php:179).* Ein Waechter,
  // der sie als Sicherheit wertet, gaebe Entwarnung fuer die Buehne auf der Arbeitsdatenbank.
  const p = await probe(['artisan', 'serve'], { DB_DATABASE: 'ticket_testing' });

  const r = waechter({ BUEHNEN_WAECHTER_NUR_PIDS: `${p.pid}` });

  assert.equal(r.code, 3, `erwartet Exitcode 3, kam ${r.code}\n${r.text}`);
  assert.match(r.text, new RegExp(`BUEHNE UNSICHER\\s+PID ${p.pid}\\b`), 'die wirkungslose Form gilt als sicher');
  assert.match(r.text, /WIRKUNGSLOS/, 'die Meldung benennt nicht, DASS die Form wirkungslos ist — eine Absage ohne Erklaerung');
});

test('A-04-3 Gegenprobe: beide Formen KORREKT gestartet -> kein Befund, exit 0', async () => {
  // *Ohne diese Zusage waere "meldet immer alles" gruen.* Mit Naht, damit eine zufaellig
  // mitlaufende fremde Buehne den Positivfall nicht faelscht.
  const pS = await probe(['-S', `127.0.0.1:${naechsterPort()}`, '-t', leer], { DB_DATABASE: 'ticket_testing' });
  const pServe = await probe(['artisan', 'serve'], { APP_ENV: 'testing' }); // loest Wegwerf-.env.testing auf

  const r = waechter({ BUEHNEN_WAECHTER_NUR_PIDS: `${pS.pid} ${pServe.pid}` });

  assert.equal(r.code, 0, `erwartet Exitcode 0, kam ${r.code}\n${r.text}`);
  assert.match(r.text, new RegExp(`BUEHNE OK\\s+PID ${pS.pid}\\b`), 'die korrekt gestartete php -S-Buehne wird nicht als OK gefuehrt');
  assert.match(r.text, new RegExp(`BUEHNE OK\\s+PID ${pServe.pid}\\b`), 'die korrekt gestartete artisan-serve-Buehne wird nicht als OK gefuehrt');
  assert.match(r.text, /ALLE BUEHNEN OK/, 'das Gesamturteil fehlt');
});

test('A-04-4 KONTROLLE: der Waechter beendet und aendert nichts (must_preserve)', () => {
  // Verhaltensseitig oben mitgeprueft (alle Proben leben nach dem Lauf). Hier die Quelle:
  // kein kill, kein pkill, kein rm/mv — ein Detektor, der fremde Prozesse anfasst, ist die
  // naechste 888-kB-Geschichte (Nicht-Ziel 3 des Blatts).
  const quelle = readFileSync(WAECHTER, 'utf8');
  const code = quelle.split('\n').filter((z) => !/^\s*#/.test(z)).join('\n');

  assert.doesNotMatch(code, /\b(kill|pkill|killall)\b/, 'der Waechter beendet Prozesse — er darf nur melden');
  assert.doesNotMatch(code, /\b(rm|mv) /, 'der Waechter veraendert Dateien — er darf nur lesen');
});

test('Drift-Zusage (Rest 1): beide Skripte nennen DENSELBEN erlaubten Namen', () => {
  // Der erlaubte Name ist BEWUSST dupliziert (eine Namensdatei waere der achtzehnte Ort,
  // nicht der erste). Der Preis der Duplikation ist diese Zusage: laufen die beiden
  // Skripte auseinander, faellt sie.
  const ausBuehne = /ERWARTETE_DB=([A-Za-z0-9_]+)/.exec(readFileSync(BUEHNE, 'utf8'));
  const ausWaechter = /ERWARTETE_DB=([A-Za-z0-9_]+)/.exec(readFileSync(WAECHTER, 'utf8'));

  assert.ok(ausBuehne, 'browser-buehne.sh traegt keinen ERWARTETE_DB-Namen mehr');
  assert.ok(ausWaechter, 'buehnen-waechter.sh traegt keinen ERWARTETE_DB-Namen mehr');
  assert.equal(ausWaechter[1], ausBuehne[1],
    `DRIFT: buehnen-waechter.sh erlaubt '${ausWaechter[1]}', browser-buehne.sh '${ausBuehne[1]}' — zwei Wahrheiten ueber die zulaessige Datenbank`);
  // Und der Anker der Duplikation bleibt der eine Name (deckungsgleich mit A-03-1):
  assert.equal(ausWaechter[1], 'ticket_testing', 'der erlaubte Name im Waechter ist nicht mehr ticket_testing');
});

test('Kante 5: kein Serverprozess -> exit 0 und eine Zeile "KEINE BUEHNE"', async () => {
  // Naht auf einen Prozess, der sicher KEINE Buehne ist: der eigene Testlaeufer.
  const r = waechter({ BUEHNEN_WAECHTER_NUR_PIDS: `${process.pid}` });

  assert.equal(r.code, 0, `erwartet Exitcode 0, kam ${r.code}\n${r.text}`);
  assert.match(r.text, /KEINE BUEHNE/, 'die Kein-Befund-Zeile fehlt');
});

test('Kante 4: ist `ps` eingeschraenkt, gibt es ENV_BLOCKED — keine falsche Entwarnung', () => {
  // Ein ps, das nichts liefert, darf NICHT wie "keine Buehne" aussehen.
  const stummesBin = join(basis, 'stummes-bin');
  mkdirSync(stummesBin, { recursive: true });
  writeFileSync(join(stummesBin, 'ps'), '#!/bin/sh\nexit 1\n');
  chmodSync(join(stummesBin, 'ps'), 0o755);

  const r = waechter({ PATH: `${stummesBin}:${process.env.PATH}` });

  assert.equal(r.code, 3, `erwartet Exitcode 3, kam ${r.code}\n${r.text}`);
  assert.match(r.text, /ENV_BLOCKED/, 'der Abbruch nennt seine Klasse nicht');
  assert.doesNotMatch(r.text, /KEINE BUEHNE/, 'ein gestoertes ps wird als "keine Buehne" ausgegeben — falsche Entwarnung');
});
