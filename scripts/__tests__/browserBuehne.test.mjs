/**
 * A-03 — **Die Buehne startet nur auf einer benannten Testdatenbank.**
 *
 * ---
 *
 * **Der Vorfall (05.08., 00:08):** eine Buehne lief gegen die ARBEITS-Datenbank. Der Aufruf hiess
 * `DB_DATABASE=ticket_testing php artisan serve` und sah richtig aus. Dass nichts passierte, lag
 * an einem fehlenden Testbenutzer — **Glueck, nicht Vorsicht.**
 *
 * **Die Ursache, am Framework gemessen:** Laravel reicht Umgebungsvariablen nicht durch, es setzt
 * sie aktiv auf `false` (`ServeCommand.php:179`). Die Durchreich-Liste hat 13 Eintraege,
 * **null davon `DB_*`**.
 *
 * ```text
 * DB_DATABASE=ticket_testing php artisan serve   Kind sieht: ticket             FALSCH, und leise
 * APP_ENV=testing php artisan serve              Kind sieht: ticket_testing     RICHTIG
 * beide Formen OHNE `serve`                      Elternprozess: ticket_testing  TAEUSCHT
 * ```
 *
 * **Die dritte Zeile ist der Grund fuer A-03-2.** Wer die Aufrufform prueft, indem er sie ohne
 * `serve` laufen laesst, bekommt die richtige Antwort — und startet danach eine Buehne auf der
 * falschen Datenbank. *Probe und Ernstfall beantworten dieselbe Frage verschieden.*
 *
 * ---
 *
 * **Was diese Zusagen NICHT tun: eine Buehne starten.** Ein Test, der einen Server oeffnet, laesst
 * ihn im Zweifel offen und belegt den Port fuer alle anderen. Geprueft wird der Riegel DAVOR —
 * die Entscheidung, ob gestartet wird, nicht das Starten selbst (dieselbe Trennung wie bei
 * `commit-pruefen.sh`: die Entscheidungsfunktion, nicht der Ausfuehrer).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, existsSync } from 'node:fs';
import { execFileSync } from 'node:child_process';

const BUEHNE = new URL('../browser-buehne.sh', import.meta.url).pathname;
const WURZEL = new URL('../..', import.meta.url).pathname;

/** Faehrt das Skript und liefert Exitcode samt Ausgabe — ohne zu werfen. */
function buehne(env = {}) {
  try {
    const aus = execFileSync('bash', [BUEHNE, '--port', '65535'], {
      cwd: WURZEL, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'], timeout: 120_000,
      env: { ...process.env, ...env },
    });

    return { code: 0, text: aus };
  } catch (e) {
    return { code: e.status ?? -1, text: `${e.stdout ?? ''}${e.stderr ?? ''}` };
  }
}

test('A-03-3: die wirkungslose Form wird BENANNT — kein stiller Start', () => {
  // *Wer `DB_DATABASE=` davorsetzt, glaubt, er habe die Datenbank gewaehlt. Er hat es nicht.*
  const r = buehne({ DB_DATABASE: 'ticket_testing' });

  assert.equal(r.code, 3, `erwartet Exitcode 3, kam ${r.code}`);
  assert.match(r.text, /WIRKUNGSLOS/, 'die Form wird nicht als wirkungslos benannt');
  assert.match(r.text, /APP_ENV=testing/, 'die RICHTIGE Form wird nicht genannt — eine Absage ohne Ausweg');
  assert.match(r.text, /ServeCommand\.php:179/, 'der Beleg fehlt: wo Laravel die Variable verwirft');
});

test('A-03-3 ROT: auch die scheinbar harmlose Form wird abgewiesen', () => {
  // Sonst waere die Zusage mit einem Sonderfall gruen zu bekommen: „nur wenn eine FALSCHE
  // Datenbank drinsteht". *Die Form ist wirkungslos, unabhaengig von ihrem Wert.*
  const r = buehne({ DB_DATABASE: 'ticket' });
  assert.equal(r.code, 3, 'DB_DATABASE=ticket kommt durch — die Form wird nur beim richtigen Wert abgewiesen');
});

test('A-03-2: der Nachweis wird am KINDPROZESS gefuehrt, nicht am Elternprozess', () => {
  // **Das eigentliche Kriterium.** Eine Pruefung am Elternprozess waere gruen gewesen — und der
  // Vorfall waere trotzdem passiert, weil der Kindprozess eine andere Umgebung sieht.
  const quelle = readFileSync(BUEHNE, 'utf8');
  const code = quelle.split('\n').filter((z) => !/^\s*#/.test(z)).join('\n');

  assert.match(code, /artisan tinker --execute/,
    'der Riegel fragt keinen Kindprozess — dann misst er die Umgebung, in der er selbst laeuft');
  assert.doesNotMatch(code, /GEFUNDENE_DB=\$\{?DB_DATABASE/,
    'der Riegel liest die eigene Umgebungsvariable statt den Kindprozess zu fragen');

  // Und die Gegenrichtung: die Auskunft muss AUCH ausgewertet werden.
  assert.match(code, /"\$GEFUNDENE_DB" != "\$ERWARTETE_DB"/,
    'die Kindprozess-Auskunft wird geholt, aber nicht verglichen');
});

test('A-03-1: EIN Name, kein Muster — `ticket_test_kopie` darf nicht durchkommen', () => {
  // *Ein Muster wie `*_test*` liesse eine KOPIE der Arbeitsdaten durch, und die traegt dieselben
  // Kundendaten wie das Original.* Deshalb Gleichheit, nicht Aehnlichkeit.
  const quelle = readFileSync(BUEHNE, 'utf8');
  const code = quelle.split('\n').filter((z) => !/^\s*#/.test(z)).join('\n');

  assert.match(code, /ERWARTETE_DB=ticket_testing\b/, 'der erwartete Name steht nicht mehr im Riegel');
  assert.doesNotMatch(code, /ERWARTETE_DB=.*\*/, 'der erwartete Name ist ein MUSTER geworden');
  assert.doesNotMatch(code, /GEFUNDENE_DB"?\s*==\s*.*test\*/, 'verglichen wird mit einem Suffix-Muster');
  // Der Vergleich ist Ungleichheit-auf-Abbruch, nicht Aehnlichkeit:
  assert.doesNotMatch(code, /case "\$GEFUNDENE_DB" in/, 'der Vergleich ist ein Musterabgleich geworden');
});

test('A-03-4 KONTROLLE: `php artisan serve` bleibt unangetastet (must_preserve)', () => {
  // **Ohne dieses Kriterium waere „ich sperre artisan serve komplett" eine gruene Loesung.**
  // A-03 baut einen zusaetzlichen Weg, keinen Zwang auf allen Wegen.
  assert.equal(existsSync(`${WURZEL}/vendor/laravel/framework/src/Illuminate/Foundation/Console/ServeCommand.php`), true,
    'ServeCommand fehlt — dann misst die Zusage Leere');

  const serve = readFileSync(`${WURZEL}/vendor/laravel/framework/src/Illuminate/Foundation/Console/ServeCommand.php`, 'utf8');
  assert.match(serve, /passthroughVariables/, 'ServeCommand ist veraendert — A-03 fasst vendor/ nicht an');

  // Und der Riegel selbst darf `serve` nicht ersetzen, sondern muss es AUFRUFEN.
  const quelle = readFileSync(BUEHNE, 'utf8');
  assert.match(quelle, /artisan serve --port=/, 'der Riegel startet die Buehne nicht mehr ueber artisan serve');
});

test('A-03-6: ANKER-BROWSER nennt das Skript als den Weg auf die Buehne', () => {
  // **Gegen die Papier-Falle eine Ebene hoeher.** Ein Skript, das niemand kennt, ersetzt keine
  // Regel — wer vor einer Browserrunde den Anker liest und dort nichts findet, improvisiert weiter.
  const anker = readFileSync(`${WURZEL}/docs/auftraege/ANKER-BROWSER.md`, 'utf8');

  assert.match(anker, /browser-buehne\.sh/, 'der Anker nennt das Skript nicht');
  assert.match(anker, /APP_ENV=testing/, 'der Anker nennt die tragfaehige Aufrufform nicht');
});
