# A-03 — Die Bühne stand auf der falschen Datenbank, und niemand hätte es gemerkt

```yaml
auftrag: A-03
titel: "Browser-Buehne: der sichere Aufruf wird erzwungen, der lautlose wird laut"
zustand: ENTWURF
ballbesitz: plan-pruefer
basis_sha: 89d69c13
pruef_sha: ""
release_sha: ""
letztes_votum: ""
naechster_schritt: "Plan-Pruefer: Definition of Ready nach §5. Besondere Bitte: P2 (muss das ueberhaupt gebaut werden?) scharf pruefen — ein tragfaehiger Aufruf EXISTIERT bereits, der Auftrag baut nur den Riegel darum."
```

## Anlass — der Generator meldet ihn gegen sich selbst

**05.08., 00:08.** Der Generator bereitete das A-01-Fixture vor und schrieb im Bericht:

> *„`php artisan serve` reicht `DB_DATABASE` NICHT an den Serverprozess durch. Die Oberflaeche lief
> gegen die ARBEITS-Datenbank. Dass mein Login scheiterte — der Testbenutzer existiert nur in
> `ticket_testing` — hat den Schreibzugriff verhindert. **Das war Glueck, nicht Vorsicht.**"*

**Diese letzte Zeile ist der Auftrag.** Ein Schutz, der aus einem fehlenden Testbenutzer besteht,
hält genau so lange, bis jemand einen anlegt.

> **Getroffen ist eine Schutzgrenze aus `CLAUDE.md`, nicht eine Bequemlichkeit:**
> *„Tests und Test-Seeds laufen nur gegen eindeutig benannte Testdatenbanken, niemals gegen
> Produktivdaten."* Und §15 sagt dasselbe.

## Ist-Zustand — gemessen, und schaerfer als die Meldung

**Es ist nicht „reicht nicht durch". Laravel setzt die Variable aktiv auf `false`:**

```text
ServeCommand.php:179   in_array($key, static::$passthroughVariables) ? [$key=>$value] : [$key=>false]

$passthroughVariables (13 Eintraege, HERD_PHP_81..84 hier zu einem zusammengezogen):
  APP_ENV · HERD_PHP_81_INI_SCAN_DIR .. HERD_PHP_84_INI_SCAN_DIR (4) ·
  IGNITION_LOCAL_SITES_PATH · LARAVEL_SAIL · PATH · PHP_IDE_CONFIG · SYSTEMROOT ·
  XDEBUG_CONFIG · XDEBUG_MODE · XDEBUG_SESSION

DB_-Eintraege darin:   0
Zaehlprobe:            1 + 4 + 8 = 13
```

**Kind-Umgebung nachgebildet (`env -i`, nur Durchreich-Variablen ueberleben) — beide Formen selbst
gefahren, nur `config()` gelesen, kein Schreibzugriff:**

```text
Kind bei "DB_DATABASE=ticket_testing php artisan serve"   ->  ticket           FALSCH
Kind bei "APP_ENV=testing php artisan serve"              ->  ticket_testing   RICHTIG
Elternprozess, beide Formen                               ->  ticket_testing   TAEUSCHT

.env           DB_DATABASE=ticket
.env.testing   DB_DATABASE=ticket_testing
```

> **Die dritte Zeile ist die gefaehrliche.** Wer die Aufrufform prueft, indem er sie ohne `serve`
> laufen laesst, bekommt die richtige Antwort — und startet danach eine Buehne auf der falschen
> Datenbank. **Die Probe und der Ernstfall beantworten dieselbe Frage verschieden.**

## DECISION — es wird kein Durchreichen gebaut

**Ein tragfaehiger Aufruf existiert bereits:** `APP_ENV=testing php artisan serve`. `APP_ENV` steht
in der Durchreich-Liste, ueberlebt die Filterung, und Laravel laedt daraufhin `.env.testing`.
**Beides gemessen, nicht gefolgert.**

```text
VORGESCHRIEBEN   APP_ENV=testing php artisan serve --port=<port>
VERBOTEN         DB_DATABASE=... php artisan serve      (wirkungslos, und zwar lautlos)
```

**Also wird nicht das Werkzeug repariert, sondern der Riegel darum gebaut.** *Laravels Filterung
ist kein Fehler — sie verhindert, dass ein neuladender Server veraltete Umgebung mitschleppt. Wer
sie umbaut, repariert ein Verhalten, das seinen Grund hat.*

**Was fehlt, ist nicht Code, sondern Lautstaerke:** die falsche Form scheitert heute ohne ein Wort.

## Ziel

Eine Browser-Buehne laesst sich nur so starten, dass **der Kindprozess nachweislich auf einer
benannten Testdatenbank steht** — und jeder Versuch der wirkungslosen Form wird benannt, statt
stillschweigend zu tun, als haette er gewirkt.

## Nicht-Ziele

- **Kein Eingriff in `vendor/`.** Laravels Filterung bleibt, wie sie ist.
- **Kein Ersatz fuer `php artisan serve` im Alltag.** Wer gegen `ticket` arbeiten will, soll das
  weiter koennen — der Auftrag baut einen zusaetzlichen Weg, keinen Zwang auf allen Wegen.
- **Keine Aenderung an `.env` oder `.env.testing`.** Beide sind korrekt; das Problem lag dazwischen.
- **Kein Loeschen und kein Anlegen von Datenbanken.**

## Scope

```text
scripts/browser-buehne.sh              der einzige vorgeschriebene Weg auf die Buehne
scripts/__tests__/browserBuehne.test.mjs   die Zusagen
```

## Akzeptanzkriterien

**Die Datei existiert an der Basis nicht** — alle P1 sind damit trivial rot. *Das ist ehrlich, aber
schwach; die Kraft steckt in A-03-2 und A-03-3, die auch bei vorhandener Datei rot waeren.*

**A-03-1 (P1):** Die Buehne startet nur, wenn die aufgeloeste Datenbank ein benannter Testname ist.
Andernfalls **startet sie nicht** und nennt den gefundenen Namen.

**A-03-2 (P1, der Kern):** Der Nachweis wird **am Kindprozess** gefuehrt, nicht am Elternprozess.
Gegenprobe im selben Test: eine Fassung, die den Elternprozess fragt, muss **durchfallen**, obwohl
sie „richtig" antwortet.

> *Ohne diese Gegenprobe waere genau die Pruefung gruen, die den Vorfall nicht verhindert haette —
> siehe die dritte Messzeile oben. **Das ist das eigentliche Kriterium dieses Auftrags.***

**A-03-3 (P1, die Lautstaerke):** Wird `DB_DATABASE` beim Aufruf mitgegeben, sagt die Buehne, dass
diese Form **wirkungslos** ist, und nennt die richtige. Kein stiller Start.

**A-03-4 (`must_preserve`-KONTROLLE, von der Rot-Pflicht ausgenommen):** `php artisan serve` ohne
das Skript bleibt unveraendert benutzbar. *Ohne dieses Kriterium waere „ich sperre artisan serve
komplett" eine gruene Loesung.*

**A-03-5 (P1, Mutationsprobe):** Mindestens fuenf Mutationen fallen: Testnamen-Pruefung entfernt ·
Pruefung auf den Elternprozess umgestellt · Warnung bei `DB_DATABASE` entfernt · Abbruch in eine
blosse Warnung verwandelt · Testnamen-Muster so geweitet, dass `ticket` durchkommt.

## Pruefbefehle

```text
A-03-1/-2/-3/-4   node --test scripts/__tests__/browserBuehne.test.mjs
                  (reines .mjs wie commitPruefen.test.mjs, KEIN TypeScript-Loader)
A-03-5            je Mutation die Suite fahren, Datei md5-identisch wiederherstellen,
                  Ergebnis als Tabelle im Bericht
```

## Kantenliste

```text
1  Port belegt                          -> Buehne meldet es; kein stiller Fehlstart
2  .env.testing fehlt                   -> Abbruch mit Namen der fehlenden Datei
3  APP_ENV bereits von aussen gesetzt   -> das Skript setzt es ausdruecklich, gewinnt
4  Testname heisst anders als erwartet  -> Muster ist eine benannte Liste im Skript,
                                            keine Rateheuristik. Aenderung = Codeaenderung.
5  MySQL laeuft nicht                   -> Abbruch mit der Klartextmeldung des Treibers
6  jemand ruft das Skript aus public/   -> cd auf die Wurzel wie im Commit-Tor
```

## Auswirkungen (§5)

```text
API · Schema · Migration · Bestandsdaten · Bundle   KEINE
Server                                              nur ein zusaetzlicher Startweg
vendor/                                             UNBERUEHRT
Browserabnahme                                      NICHT ANWENDBAR - keine Produktoberflaeche
```

## Rueckweg

Zwei neue Dateien, kein Eingriff in Bestehendes. Loeschen genuegt; `git revert` ebenso.
**Kein Datenweg wird veraendert** — der Auftrag verhindert Schreibzugriffe, er erzeugt keine.

## Offene Punkte fuer den Plan-Pruefer

1. **P2 scharf pruefen.** Ein tragfaehiger Aufruf existiert bereits (gemessen). Ist der Riegel den
   Bau wert, oder genuegt die Festlegung im Text? *Meine Position: eine Regel, die nur auf Papier
   steht, hat den Vorfall gerade nicht verhindert — der Vorplanner hatte dieselbe Regel gelesen.
   Aber die Frage gehoert gestellt, und `NICHT NOTWENDIG` ist ein zulaessiges Votum.*
2. **Die Liste der erlaubten Testnamen** gehoert festgelegt, bevor gebaut wird. Vorschlag:
   ausschliesslich `ticket_testing` und `wberechnung_mysql_test`. Weitere nur mit Eintrag.
