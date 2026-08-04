# A-03 — Die Bühne stand auf der falschen Datenbank, und niemand hätte es gemerkt

```yaml
auftrag: A-03
titel: "Browser-Buehne: der sichere Aufruf wird erzwungen, der lautlose wird laut"
zustand: ENTWURF
ballbesitz: plan-pruefer
basis_sha: 89d69c13
pruef_sha: ""
release_sha: ""
letztes_votum: "plan-pruefer 05.08. 00:19 (1. Runde): P2 SCHARF GEPRUEFT -> BAUEN gerechtfertigt. Kein bestehender Wrapper; Vendor-Behauptung woertlich bestaetigt (13 Eintraege / 0 x DB_ / :179 false). Zwei Restpunkte: Verankerung in ANKER-BROWSER + Testnamen-Liste NUR ticket_testing."
naechster_schritt: "Plan-Pruefer: BEREIT-Votum. Beide Restpunkte erledigt — ANKER-BROWSER-Text steht bereits (Planner, ausserhalb des Baus, weil er heute schon wahr ist und die naechste Browserrunde schuetzt), der Bau schuldet nur noch den Zeiger als A-03-6; Namensliste auf ticket_testing allein zusammengestrichen, mein Zweitvorschlag wberechnung_mysql_test gemessen verworfen (fremde Anwendung, eigener Variablenname WB_DB)."
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

**A-03-1 (P1):** Die Buehne startet nur, wenn die aufgeloeste Datenbank **genau `ticket_testing`**
ist. Andernfalls **startet sie nicht** und nennt den gefundenen Namen.
*Ein Name, kein Muster — die Begruendung steht unter „Offene Punkte".*

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
blosse Warnung verwandelt · Vergleich von `=== ticket_testing` auf ein Suffix-Muster
(`*_test*`) aufgeweicht, sodass `ticket_test_kopie` durchkommt.

**A-03-6 (P1, gegen die Papier-Falle eine Ebene hoeher):** `docs/auftraege/ANKER-BROWSER.md`
**nennt das Skript als den Weg auf die Buehne.** Ein Skript, das niemand kennt, ersetzt keine Regel.

> **Der Plan-Pruefer hat den Kern getroffen:** A-03 verschiebt die Regel vom Papier ins Werkzeug —
> aber wer vor einer Browserrunde ANKER-BROWSER liest und dort nichts findet, improvisiert weiter.
> **Dann waere die Papierregel nur eine Ebene hoeher gerutscht, statt zu verschwinden.**

**Der TEXT-Teil ist bereits erledigt und NICHT Teil des Baus:** Der Planner hat die Regel am
05.08. in ANKER-BROWSER verankert (Abschnitt *„Vor dem Anker: auf WELCHER Datenbank die Buehne
steht"*), samt Messung, Herkunft und dem ausdruecklichen Vermerk, dass sie **bis A-03 eine
Papierregel** ist. *Sie ist heute wahr und schuetzt die naechste Browserrunde, die vor dem Bau
stattfinden kann — deshalb wartet sie nicht auf ihn.*

**A-03-6 verlangt nur noch den Zeiger:** Sobald das Skript steht, ersetzt der Bauende den Satz
*„bis er steht, ist diese Regel die einzige Sicherung"* durch den Aufruf des Skripts.
**Prueftext-Zusage:** `ANKER-BROWSER.md` enthaelt danach `scripts/browser-buehne.sh`.

## Pruefbefehle

```text
A-03-1/-2/-3/-4   node --test scripts/__tests__/browserBuehne.test.mjs
                  (reines .mjs wie commitPruefen.test.mjs, KEIN TypeScript-Loader)
A-03-5            je Mutation die Suite fahren, Datei md5-identisch wiederherstellen,
                  Ergebnis als Tabelle im Bericht
A-03-6            grep -c 'scripts/browser-buehne.sh' docs/auftraege/ANKER-BROWSER.md
                  erwartet >= 1 NACH dem Bau · an der Basis 0 (selbst gemessen, deshalb
                  wirksam rot) · Gegenbeweis: Zeiger entfernen -> Zusage faellt
```

## Kantenliste

```text
1  Port belegt                          -> Buehne meldet es; kein stiller Fehlstart
2  .env.testing fehlt                   -> Abbruch mit Namen der fehlenden Datei
3  APP_ENV bereits von aussen gesetzt   -> das Skript setzt es ausdruecklich, gewinnt
4  Testname heisst anders als erwartet  -> ERLAUBT IST GENAU EIN NAME: ticket_testing.
                                            Keine Liste, kein Muster, keine Heuristik.
                                            Aenderung = Codeaenderung + Auftrag.
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

## Offene Punkte — alle drei geschlossen, 05.08. 00:3x

**1. Runde des Plan-Prüfers:** P2 scharf geprüft, **BAUEN gerechtfertigt** — kein bestehender
Wrapper, und die Vendor-Behauptung hat er wörtlich nachgemessen (13 Einträge / 0 × `DB_` /
`:179 false`). Zwei Restpunkte kamen zurück, beide sind erledigt:

| # | Punkt | Erledigung |
|---|---|---|
| **P2** | Muss überhaupt gebaut werden? | **JA**, vom Prüfer geprüft, nicht von mir vorentschieden |
| **1** | Verankerung in ANKER-BROWSER | **Text steht bereits** (Planner, 05.08.) · als **A-03-6** wird nur noch der Zeiger aufs Skript zugesagt |
| **2** | Liste erlaubter Testnamen | **auf `ticket_testing` allein zusammengestrichen** |

### Warum ich meinen eigenen Vorschlag zur Namensliste fallen lasse

Ich hatte `ticket_testing` **und** `wberechnung_mysql_test` vorgeschlagen. **Gemessen:**

```text
grep -rn 'wberechnung_mysql_test' --include='*.php' --include='*.env*' --include='*.xml'
  scripts/wberechnung-mysql-test.env.example:9:  WB_DB=wberechnung_mysql_test
  -> ein EIGENER Variablenname (WB_DB), nicht Laravels DB_DATABASE, nicht diese App
```

**Der zweite Name gehört einer anderen Anwendung.** Ihn zuzulassen hätte das Tor geöffnet, ohne
dass je ein Fall ihn gebraucht hätte. *Eine Erlaubnis ohne Anwendungsfall ist kein Komfort,
sondern eine Lücke mit Vorlauf.*

**Ein einziger erlaubter Name heißt auch: kein Muster, keine Heuristik, kein `*_test`-Suffix.**
Ein Suffix-Muster wäre bequem und würde beim ersten Namen wie `ticket_test_kopie` genau das
durchlassen, wogegen das Kriterium gebaut wird.

**Damit sind alle §5-Punkte adressiert.** Ball zurück an den Plan-Prüfer für das `BEREIT`-Votum.
