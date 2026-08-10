# A-04 — Aufrufformen aufzählen hat zweimal versagt. Also den Zustand messen.

```yaml
auftrag: A-04
titel: "Buehnen-Waechter: erkennt eine laufende Buehne auf einer Nicht-Testdatenbank, egal wie sie gestartet wurde"
basis_sha: 89f373d9
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```

> **📢 Fassung 1.1 der ARBEITSREGELN gilt seit 05.08.** Mitteilung und Kenntnisnahme in
> [`STATUS.md`](../../STATUS.md); offen ist `DECISION_BLOCKED` zu zwei Regelwerken.

## Anlass — mein eigener Spezifikationsfehler, an der laufenden Bühne gefunden

**A-03 riegelt `artisan serve`. Benutzt wird `php -S`.** Gemessen an `89f373d9`:

```text
php -S in scripts/browser-buehne.sh (A-03-Bau 26e378a5)   0 Treffer
php -S in docs/auftraege/ANKER-BROWSER.md                 0 Treffer
Skript, das php -S startet                                KEINES
tatsaechlich benutzt                                      Generator 00:08 · Evaluator 01:54

die beiden Nachbarformen:
  DB_DATABASE=ticket_testing php -S …   sicher     -> ticket_testing
  php -S …                              UNSICHER   -> faellt auf .env -> ticket
                                        Unterschied: EIN Praefix.
```

**Der Generator hat es mir um 00:08 wörtlich geschrieben** (*„Tragfähig ist `php -S`, gestartet aus
`public/` heraus"*). **Ich habe den Bericht gelesen, daraus zitiert — und `artisan serve`
vorgeschrieben.** Der Fehler gehört dem Planner, nicht dem Bauenden.

## DECISION — kein dritter Startweg, sondern ein Zustandsdetektor

**Zweimal hat das Aufzählen von Aufrufformen versagt:** A-03 schrieb `artisan serve` vor (niemand
nutzt es), `php -S` blieb ungenannt (alle nutzen es). **Eine dritte Form aufzuzählen würde beim
vierten Weg wieder versagen.**

> **Also wird nicht gefragt „wie wurde gestartet", sondern „was läuft gerade".**
> Ein Detektor, der laufende Serverprozesse findet und ihre aufgelöste Datenbank nennt, ist
> **unabhängig davon, wie sie entstanden sind** — auch von Wegen, die es noch nicht gibt.

**Er ersetzt A-03 nicht, er ergänzt es:** `browser-buehne.sh` verhindert den falschen Start,
A-04 findet die falsche Bühne — auch die, die an beidem vorbei gestartet wurde.

## Nicht-Ziele

- **Kein zweiter Startweg.** A-04 startet nichts. *Sonst hätte ich drei Wege statt zwei.*
- **Kein Eingriff in `browser-buehne.sh`.** A-03 ist gebaut und in Abnahme.
- **Kein Beenden fremder Prozesse.** Der Detektor **meldet**; wer beendet, entscheidet ein Mensch.
  *Ein Werkzeug, das fremde Prozesse killt, ist die nächste 888-kB-Geschichte.*

## Scope

```text
scripts/buehnen-waechter.sh                der Detektor
scripts/__tests__/buehnenWaechter.test.mjs die Zusagen
```

## Wiederverwendungsprüfung (§5, Fassung 1.2.2) — nachgetragen 05.08.

**Dieses Blatt hat den Mangel bewiesen, der zu Auflage A2 führte, und trug selbst keine Prüfung.**
*Nachgeholt, bevor der Plan-Prüfer es beanstanden muss.*

```text
scripts/browser-buehne.sh (A-03-Bau 26e378a5) - liegt seit dem Merge 27a61da9 AUF DEM ZWEIG
  :31  ERWARTETE_DB=ticket_testing              <- der erlaubte Name, als Konstante
  :60  GEFUNDENE_DB=$(APP_ENV=testing php artisan tinker --execute='echo config(...)')
                                                <- die Aufloesungs-Logik, fertig
scripts/commit-pruefen.sh                       Vorbild fuer Prozess-Auskunft (lsof)
Skripte mit `ps -eo`                            KEINES - das bringt A-04 neu
docs/_playground-archiv/                        nichts Vergleichbares (0 Treffer)
```

> ### Der kritische Fund: `ERWARTETE_DB` darf es nur EINMAL geben
>
> **Definiert A-04 seinen eigenen erlaubten Datenbanknamen, gibt es zwei Wahrheiten darüber,
> welche Datenbank zulässig ist — und sie werden auseinanderlaufen.** *Genau die zweite Wahrheit,
> gegen die A-01 geschnitten wurde.*
>
> **ZWEIGKORREKTUR 08.08.: die Abhängigkeit ist AUFGELÖST.** `browser-buehne.sh` liegt seit dem
> Merge `27a61da9` auf dem Arbeitszweig — selbst nachgemessen. *Der frühere Satz „A-04 kann erst
> gebaut werden, wenn A-03 hier liegt" ist damit überholt und stand hier eine Runde zu lange:
> die Auftragstafel war nachgezogen, das Blatt nicht.*

## Benannter Erstnutzer (§5, Fassung 1.2.2) — der Punkt, den dieses Blatt erzwungen hat

**`buehnen-waechter.sh` ist neu und kann nicht „in Gebrauch" sein.** Nach der neuen Fassung tritt
der Erstnutzer an diese Stelle:

```text
WER      der Evaluator
WANN     vor JEDER Browserabnahme nach §9, beginnend mit der ersten nach dem A-04-Bau
WOZU     er stellt fest, gegen welche Datenbank die Buehne laeuft, BEVOR er misst
BELEG    der Waechter-Aufruf samt Ausgabe gehoert in seinen Abnahmebericht
```

*Der Evaluator und nicht der Generator: **er** hat am 05.08. um 01:54 eine Bühne gefahren, und
**seine** Messung wäre wertlos gewesen, wenn sie auf der falschen Datenbank gelaufen wäre. Der
Erstnutzer ist die Rolle mit dem größten eigenen Interesse.*

## Restpunkte des Plan-Prüfers, erledigt 08.08.

### Rest 1 — der erlaubte Name: BEWUSSTE Duplikation mit Drift-Zusage

**Gemessen, bevor ich entscheide:** `ticket_testing` steht heute in **17 Dateien** —
`browser-buehne.sh`, zwei MySQL-Prüfskripte, `tests/TestDatenbank.php` und zwölf Feature-Tests.

> **Damit trägt die Vorgabe „gemeinsame Quelle" nicht.** *Eine Namensdatei nur für A-04 wäre der
> **achtzehnte** Ort, nicht der erste. Und `tests/TestDatenbank.php` — die PHP-seitige Wahrheit —
> kann keine Shell-Datei sourcen.*

```text
ENTSCHIEDEN   bewusste Duplikation zwischen browser-buehne.sh und buehnen-waechter.sh
              PLUS eine Zusage, die Drift zwischen beiden faengt:
              beide Skripte muessen denselben Namen nennen, sonst faellt sie.
NICHT         eine gemeinsame Namensdatei einfuehren - das waere ein eigener Auftrag
              ueber alle 17 Fundstellen und nicht Gegenstand von A-04.
```

*Die 17 Fundstellen sind ein eigener Befund. Ich schneide ihn hier **nicht** mit — Scope-Erweiterung
im laufenden Blatt ist genau das, was §7 verbietet.*

### Rest 2 — der Fixture-Weg für A-04-2 (§15-Kante)

**Der „unsichere" Testfall darf keine real an `ticket` gebundene Bühne erzeugen.**

```text
Wegwerf-Verzeichnis mit EIGENER .env, Datenbankname frei erfunden (kein echter Name)
Der Detektor liest PROZESS und ENV - niemals die echte Arbeits-DB-Bindung
Kein Zugriff auf ticket, kein Zugriff auf ticket_testing
```

*So bleibt der Test aussagekräftig, ohne dass eine Probe je an einer echten Datenbank hängt —
die Kante, an der A-03 geschnitten wurde.*

## Akzeptanzkriterien

**Die Dateien existieren an der Basis nicht** — alle P1 sind trivial rot. *Die Kraft steckt in
A-04-2 und A-04-3.*

**A-04-1 (P1):** Läuft ein Serverprozess gegen eine Datenbank ungleich `ticket_testing`, meldet der
Wächter ihn mit **PID, Startbefehl und gefundenem Datenbanknamen** und endet mit Exitcode 3
(`ENV_BLOCKED`, wie das Commit-Tor).

**A-04-2 (P1, der Kern — unabhängig von der Startform):** Der Nachweis gelingt für **beide**
Startformen. Im Test **je ein Fall**:

```text
php -S                ohne DB_DATABASE   -> erkannt als unsicher
php artisan serve     ohne APP_ENV       -> erkannt als unsicher
```

> *Ohne beide Fälle wäre ein Detektor grün, der nur die Form kennt, die ich gerade im Kopf habe —
> **genau der Fehler, gegen den A-04 gebaut wird.***

**A-04-3 (P1, die Gegenprobe):** Dieselben zwei Formen **korrekt** gestartet -> **kein Befund**,
Exitcode 0. *Ohne sie wäre „meldet immer alles" grün.*

**A-04-4 (`must_preserve`-KONTROLLE, von der Rot-Pflicht ausgenommen):** Der Wächter **beendet
nichts** und **ändert nichts**. Zusage: nach dem Lauf ist die Prozessliste unverändert.

**A-04-5 (P1, Mutationsprobe):** Mindestens fünf Mutationen fallen: Erkennung auf eine Startform
verengt · Vergleich `=== ticket_testing` auf ein Suffix aufgeweicht · Exitcode 3 auf 0 · Meldung
ohne Datenbanknamen · Prozesssuche auf den eigenen Baum verengt (fremde Worktrees übersehen).

**A-04-6 (P1):** `ANKER-BROWSER.md` nennt den Wächter als Pflichtschritt **vor** einer
Browserabnahme. *Prüfbefehl: `grep -c 'buehnen-waechter' docs/auftraege/ANKER-BROWSER.md` — Basis 0,
danach ≥ 1.*

## Prüfbefehle

```text
A-04-1..4   node --test scripts/__tests__/buehnenWaechter.test.mjs   (reines .mjs)
A-04-5      je Mutation die Suite fahren, Datei md5-identisch wiederherstellen
A-04-6      grep -c 'buehnen-waechter' docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste

```text
1  mehrere Buehnen gleichzeitig       -> ALLE melden, nicht die erste
2  Prozess gehoert einem anderen      -> melden, NICHT anfassen (A-04-4)
   Worktree oder Nutzer
3  Startbefehl verraet die DB nicht   -> dann ist sie unbekannt = UNSICHER melden.
   (weder Praefix noch APP_ENV)          Im Zweifel laut, nie still - Richtung wie A-02-3.
4  ps ist eingeschraenkt              -> Abbruch mit ENV_BLOCKED statt falscher Entwarnung
5  kein Serverprozess laeuft          -> Exitcode 0, eine Zeile "keine Buehne"
6  Wachtlauf waehrend eines Starts    -> Wiederholung ist erlaubt; der Waechter ist
                                          zustandslos und darf jederzeit laufen
```

## Auswirkungen (§5, Fassung 1.1)

```text
API · Schema · Migration · Bestandsdaten · Bundle    KEINE
Testdaten-Ziel                                       KEINES - der Waechter schreibt nicht
Prozessbindung                                       ENTFAELLT - er startet keinen Prozess,
                                                     er liest die vorhandenen
Werkzeuge auf der Zielmaschine                       ps: vorhanden und in Gebrauch (selbst
                                                     benutzt, 05.08. 01:5x, hat den Befund
                                                     ueberhaupt erst geliefert)
Browserabnahme                                       NICHT ANWENDBAR
```

## Rückweg

Zwei neue Dateien plus eine Zeile in ANKER-BROWSER. Löschen genügt.

## Übernommene Auflagen aus der A-03-Abnahme

Der Evaluator hat A-03 **abgenommen** (26e378a5) und drei Befunde verbucht. **B1/SPEC/P1 ist
dieser Auftrag.** Die zwei kleinen fahren hier mit, weil sie dieselbe Fläche berühren:

**B2 (CODE/P2) — der Papierregel-Satz steht neben dem neuen Absatz im Anker.**
`ANKER-BROWSER.md:82` sagt noch *„bis er steht, ist diese Regel die einzige Sicherung"*, obwohl das
Skript gebaut ist.

> **GESCHLOSSEN 08.08.** Die Bedingung lautete *„B2 wird mit dem A-03-Merge geschlossen, nicht
> davor"* — **der Merge `27a61da9` ist da.** `scripts/browser-buehne.sh` liegt auf dem Arbeitszweig
> (`grep -c 'browser-buehne' ANKER-BROWSER.md` = **2**, selbst gemessen), und der veraltete Satz im
> Anker (`Z.92`) ist mit diesem Commit nachgezogen.
>
> *Mein erster Nachzug war **halb**: ich habe die Zahl ersetzt und das Satzende stehen lassen —
> „liegt seit dem Merge auf dem Arbeitszweig UND ist hier nicht gemergt". Ein Widerspruch in einer
> Zeile, in genau der Korrektur, die einen Widerspruch beheben sollte.*

**B3 (CODE/P2) — Testlücke in A-03s Suite:** die `exec`-Zeile ohne `APP_ENV` überlebt die
Mutationsprobe, ein `assert` fehlt. **Auflage:** wird im Zuge von A-04 mitgeschlossen; der Bauende
ergänzt die fehlende Zusage in `browserBuehne.test.mjs`.

*B4/B5 (P3, Kommentargenauigkeit und Kanten-Meldetext) bleiben registriert und unbearbeitet — sie
blockieren nichts.*

## §13 — zweite Ausprägung derselben Klasse

Der Evaluator vermerkt: **B1 ist die zweite Ausprägung von „Regel läuft neben der Praxis her"** und
löst damit den Sofort-Trigger aus. **Er ist bereits ausgelöst und beantwortet:**
[`PROZESSPRUEFUNG-01.md`](../../PROZESSPRUEFUNG-01.md) und die daraus entstandene **Fassung 1.1**,
deren §5-Punkt *„vorgeschriebene Formen müssen vorhanden UND in Gebrauch sein"* genau diese Klasse
adressiert. **A-04 ist die bauliche Antwort, 1.1 die prozessuale.**

## §11-Bericht des Generators (10.08.)

```yaml
auftrag: A-04
basis: 89f373d9 (Blatt) · Bau-Ausgang HEAD 26a2c99a · IN_ARBEIT-Commit 17984c82
commit: c3d52f09
scope:
  - scripts/buehnen-waechter.sh            # NEU, der Detektor
  - scripts/__tests__/buehnenWaechter.test.mjs  # NEU, die Zusagen (7)
  - scripts/__tests__/browserBuehne.test.mjs    # B3-Auflage: exec-Zeile-Zusage ergaenzt (6 -> 7)
  - docs/auftraege/ANKER-BROWSER.md        # A-04-6: Waechter als Pflichtschritt (grep 0 -> 1)
tests:
  statisch: pass          # bash -n beide Skripte, node --check beide Suiten
  unit: "14/14"           # buehnenWaechter 7/7 + browserBuehne 7/7, je selbst gefahren
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar
  browser: nicht_anwendbar  # laut Blatt (§5-Block)
abweichungen:
  - "ANKER-BROWSER.md steht nicht im Scope-Block des Blatts (der nennt nur die zwei Skripte),
    wird aber von A-04-6 (P1) und dem Rueckweg-Absatz ('plus eine Zeile in ANKER-BROWSER')
    ausdruecklich verlangt — genau ein Absatz eingefuegt, sonst nichts."
  - "Rest-2-Auslegung im Positivfall A-04-3: 'frei erfundener Name' gilt fuer die unsicheren
    Faelle; Exit 0 ist nur zusagbar, wenn die Aufloesung exakt ticket_testing ergibt. Der Name
    kommt deshalb als blosse ZEICHENKETTE in Prozessumgebung bzw. Wegwerf-.env.testing vor —
    verbunden wird nichts: der artisan-Stub schlaeft nur, die php -S-Proben dienen ein LEERES
    Wegwerf-Verzeichnis aus. KEINE echte Buehne gestartet, kein Zugriff auf ticket/ticket_testing."
  - "Test-Naht BUEHNEN_WAECHTER_NUR_PIDS (PID-Filter, nur fuer die Suite dokumentiert):
    Positivfaelle laufen mit Naht (gegen zufaellig mitlaufende fremde Buehnen), Negativfaelle
    OHNE Naht — eine Mutation 'Naht immer aktiv' oder 'nur eigener Baum' faellt dort."
  - "Formen-Muster bewusst laut: es matcht php-Binaries mit Version (Herd: php84) und Pfaden
    mit Leerzeichen; eine Befehlszeile, die 'php -S'/'artisan serve' nur als TEXT traegt
    (z. B. ein grep), wuerde als UNSICHER gemeldet — im Zweifel laut, nie still (Kante 3)."
kriterien:
  A-04-1: "erfuellt — FALSCH-Meldung traegt PID, Startbefehl, gefundenen Namen; exit 3"
  A-04-2: "erfuellt — php -S ohne DB_DATABASE UND artisan serve ohne APP_ENV je UNSICHER;
           dazu die Rot-Zusage: DB_DATABASE bei artisan serve zaehlt NICHT als Sicherheit
           (WIRKUNGSLOS benannt — der Irrtum des 05.08.)"
  A-04-3: "erfuellt — beide Formen korrekt gestartet -> BUEHNE OK + exit 0"
  A-04-4: "erfuellt — verhaltensseitig (alle Proben leben nach dem Lauf) + Quell-Zusage
           (kein kill/pkill/rm/mv im Code)"
  A-04-5: "erfuellt — SECHS Mutationen einzeln gefahren, jede faellt, Datei danach
           md5-identisch (9916d803… Waechter, 23ee4473… Buehne):
           M1 Serve-Muster totgelegt -> 3 rot · M2 Gleichheit auf Suffix -> 1 rot
           (zz_ticket_testing faellt; ticket_testing_kopie deckt die Praefix-Richtung) ·
           M3 Befund-exit 3->0 -> 2 rot · M4 Meldung ohne DB-Name -> 1 rot ·
           M5 Suche auf eigenen Baum verengt -> 3 rot · M6 exec-Zeile ohne APP_ENV -> 1 rot (B3)"
  A-04-6: "erfuellt — grep -c buehnen-waechter ANKER-BROWSER.md: Basis 0, jetzt 1"
  B3: "geschlossen — Zusage in browserBuehne.test.mjs, Mutation M6 faellt"
drift_zusage: "beide Skripte muessen denselben ERWARTETE_DB-Namen tragen, sonst faellt die
  Zusage; zusaetzlich ist der Waechter-Name auf woertlich ticket_testing verankert"
erste_echte_messung: "der Waechter fand beim ersten Lauf die VERWAISTE Buehne PID 48098
  (05.08. 00:58, ppid 1, php84 -S :65535, Herd-Pfad MIT Leerzeichen) und loeste sie ueber
  APP_ENV=testing -> ticket-a01/.env.testing als ticket_testing/OK auf — exakt die
  Prozessklasse, fuer die er gebaut wurde. NICHT beendet (Nicht-Ziel 3), nur gemeldet."
offene_akzeptanz: []
```

## Offene Punkte für den Plan-Prüfer

1. **Ist der Detektor wirklich besser als eine dritte Aufrufform — oder baue ich eine zweite
   Wahrheit neben A-03?** *Meine Position: er beantwortet eine andere Frage („was läuft" statt „wie
   starte ich") und wird deshalb nicht mit A-03 auseinanderlaufen können. Aber die Frage gehört
   gestellt, und `NICHT NOTWENDIG` ist ein zulässiges Votum.*
2. **Soll der Wächter im Commit-Tor mitlaufen?** *Ich schlage NEIN vor: das Tor ist der einzige
   Commit-Weg aller Rollen, und A-02 hat gerade gezeigt, was eine zusätzliche externe Abhängigkeit
   dort anrichtet. Ein Aufruf vor der Browserabnahme genügt.*

## Evaluator-Votum A-04 (10.08.2026)

```yaml
auftrag: A-04
commit: c3d52f09
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "eigene Wegwerf-Proben nach dem Fixture-Weg (KEINE echte Buehne, kein ticket/
  ticket_testing) + zwei eigene Mutationen (Drift-Name, Befund-Exit) + Basis-Richtung an 89f373d9"
browser: nicht_anwendbar
befunde:
  - "P3/UMGEBUNG (kein Abnahme-Blocker): git-Index fuehrt die drei neuen Scope-Dateien als 'D'
    UND '??' zugleich, ANKER-BROWSER.md als 'MM' — Inhalt aller vier Dateien ist content-identisch
    zu c3d52f09 (je git show | diff -q = 0, selbst gemessen). A-07-Phaenomen-Klasse: gemeldet,
    nicht behoben."
```

**Geprüft an:** Arbeitsbaum-HEAD `e3d7b2c8`, alle vier Scope-Dateien content-identisch zu
Prüf-SHA `c3d52f09` (je `git show c3d52f09:<pfad> | diff -q - <pfad>` = 0 — die Prüfung lief
damit exakt auf dem gemeldeten Stand). Scope selbst gemessen: `git show c3d52f09 --stat` =
**exakt 4 Dateien, 364 Einfügungen, 0 Löschungen** (buehnen-waechter.sh NEU 149 Z.,
buehnenWaechter.test.mjs NEU 197 Z., browserBuehne.test.mjs +11 Z., ANKER-BROWSER.md +7 Z.).

### Beide Suiten selbst gefahren (Rohausgabe)

```text
node --test scripts/__tests__/buehnenWaechter.test.mjs
  ℹ tests 7 · ℹ pass 7 · ℹ fail 0            (alle 7 Zusagen namentlich ✔ gesehen)
node --test scripts/__tests__/browserBuehne.test.mjs
  ℹ tests 7 · ℹ pass 7 · ℹ fail 0            (inkl. 'B3 … exec-Zeile traegt APP_ENV=testing' ✔)

Baseline-Gegenprobe: browserBuehne.test.mjs aus c3d52f09^ (331cd125) temporaer eingespielt,
IM REPO gefahren: ℹ tests 6 · ℹ pass 6 · ℹ fail 0 — Basis war wirklich 6/6, B3 ist die
siebte, NEUE Zusage. Datei danach md5-identisch zurueck (9804c591… vorher = nachher).
(Ein erster Baseline-Lauf in einer Scratchpad-Kopie zeigte 5/6 — Extraktions-Artefakt:
A-03-4 prueft vendor/ServeCommand.php, das in der Kopie fehlt. Kein Befund.)
```

### A-04-1 — FALSCH-Meldung mit PID + Befehl + DB-Name, exit 3: **ERFÜLLT**

Eigene Wegwerf-Probe (Fixture-Weg: Wegwerf-Verzeichnis in der Scratchpad, eigene `.env` mit
`zz_eval_nur_fantasie`, `php -S` dient ein LEERES Verzeichnis aus, artisan-Stub schläft nur —
keine Datenbank, kein ticket, kein ticket_testing):

```text
BUEHNE FALSCH     PID 68211 (php -S)
  Befehl:    php -S 127.0.0.1:49733 -t …/eval-wegwerf/leer
  Datenbank: 'zz_eval_erfunden' (DB_DATABASE aus der Prozessumgebung) — erwartet ist exakt 'ticket_testing'.
ENV_BLOCKED   2 von 3 Buehnen FALSCH oder UNSICHER — erst klaeren, dann messen.
EXIT=3
```

**Zwei-Richtungs-Probe:** an der Basis `89f373d9` existiert das Verhalten nicht —
`git ls-tree 89f373d9 -- scripts/buehnen-waechter.sh` = **0 Treffer** (Datei existiert nicht,
trivial rot); an `c3d52f09` grün wie oben.

### A-04-2 — der Kern, beide Startformen + nackte-`.env`-Verbot: **ERFÜLLT**

Eigene Proben, alle exit 3:

```text
php -S OHNE DB_DATABASE            -> BUEHNE UNSICHER PID 68637 … Datenbank: UNBEKANNT
artisan-Stub OHNE APP_ENV          -> BUEHNE UNSICHER PID 68212 … Datenbank: UNBEKANNT
DB_DATABASE=ticket_testing php artisan serve (die A-01-Vorfallsklasse, sieht richtig aus):
  -> BUEHNE UNSICHER PID 68638 … DB_DATABASE='ticket_testing' ist bei 'artisan serve'
     WIRKUNGSLOS (ServeCommand-Filter) und APP_ENV fehlt. Im Zweifel laut, nie still.
```

Code-Kern gegengelesen: `grep -n '\.env' scripts/buehnen-waechter.sh` → im **Code** (Kommentare
raus) nur Z.103 `"$basis/.env.$app_env"` und Z.113 (Meldetext) — **die nackte `.env` wird an
keiner Codestelle gelesen**; der WIRKUNGSLOS-Zweig steht in Z.92.

### A-04-3 — Gegenprobe, beide Formen korrekt → exit 0: **ERFÜLLT**

```text
DB_DATABASE=ticket_testing php -S …        -> BUEHNE OK PID 68275
APP_ENV=testing php artisan (Stub, loest Wegwerf-.env.testing auf) -> BUEHNE OK PID 68276
ALLE BUEHNEN OK   2 geprueft, Datenbank jeweils 'ticket_testing'.   EXIT=0
```

`ticket_testing` kam dabei nur als Zeichenkette in Prozessumgebung/Wegwerf-`.env.testing` vor —
verbunden wurde nichts (Rest-2-Auslegung nachvollzogen und für tragfähig befunden).

### A-04-4 — beendet nichts, ändert nichts: **ERFÜLLT**

```text
grep kill/pkill/killall/rm/mv im Code von buehnen-waechter.sh (Kommentare raus): 0 Treffer
Verhaltensprobe: nach dem Waechterlauf mit Befund leben beide Proben —
  ps -p 68211 -> LEBT · ps -p 68212 -> LEBT (danach von MIR beendet, nicht vom Waechter)
```

### A-04-5 — Mutationsprobe, zwei eigene Mutationen: **ERFÜLLT**

```text
M-E1 (Namens-/Drift-Pruefung): browser-buehne.sh:31 ERWARTETE_DB=ticket_testing_drift
  -> Suite: tests 7 · pass 6 · fail 1 — GENAU 'Drift-Zusage (Rest 1)' faellt
  -> Rueckstellung git show c3d52f09^: md5 23ee4473… (identisch zum Bericht des Generators)
M-E2 (Befund-Exit): buehnen-waechter.sh Z.145 exit 3 -> exit 0
  -> Suite: tests 7 · pass 5 · fail 2 — 'A-04-1/2' und 'A-04-2 ROT' fallen
  -> Rueckstellung git show c3d52f09: md5 9916d803… (identisch zum Bericht des Generators)
Kontrolllauf nach beiden Rueckstellungen: tests 7 · pass 7 · fail 0
```

Damit ist die Drift-Zusage in **beide** Richtungen belegt: beide Skripte nennen heute denselben
Namen (`buehnen-waechter.sh:29` und `browser-buehne.sh:31`, je `ERWARTETE_DB=ticket_testing`,
selbst gegriffen), und ein verstellter Name lässt die Zusage fallen (M-E1).

### A-04-6 — Anker nennt den Wächter als Pflichtschritt: **ERFÜLLT**

```text
grep -c 'buehnen-waechter' docs/auftraege/ANKER-BROWSER.md = 1   (Basis 89f373d9: 0)
```

Absatz gelesen: „**Pflichtschritt VOR jeder Browserabnahme (A-04)**: `bash
scripts/buehnen-waechter.sh` … erst klären, dann messen … Der Aufruf samt Ausgabe gehört in den
Abnahmebericht (Erstnutzer: der Evaluator)." — inhaltlich korrekt, VOR der Abnahme verortet.

**Scope-Würdigung:** `ANKER-BROWSER.md` steht nicht im Scope-Block des Blatts, wird aber von
A-04-6 (P1) und dem Rückweg-Absatz („plus eine Zeile in ANKER-BROWSER") ausdrücklich verlangt.
Die Abweichung war vom Generator offen deklariert und ist **kriteriengedeckt** — ich schließe
mich dem Plan-Prüfer an: kein Befund. Ein Blatt, dessen P1 eine Datei verlangt, hat sie im
Scope, auch wenn der Scope-Block sie nicht aufzählt.

### B3 (Auflage aus A-03) — **GESCHLOSSEN**

Die neue Zusage steht in `browserBuehne.test.mjs` (selbst gelesen), Suite 6→7, Baseline-Lauf
6/6 selbst geführt; die Mutationsklasse (exec-Zeile ohne `APP_ENV`) ist damit gedeckt.

### Realfund PID 48098 — Zustand vermerkt, nicht angefasst

```text
ps -p 48098: LÄUFT WEITER — ppid 1, gestartet Mi 05.08. 00:58:16,
  …/Herd/bin/php84 -S 127.0.0.1:65535 …ticket/vendor/…/server.php
Mein Waechterlauf loest sie auf: BUEHNE OK — 'ticket_testing'
  (DB_DATABASE aus /Users/yamanuri/Documents/ticket-a01/.env.testing via APP_ENV=testing)
```

Die verwaiste Bühne ist harmlos (ticket_testing), bleibt aber ein Prozess mit ppid 1 seit fünf
Tagen — **Entscheidung über Beenden gehört einem Menschen** (Nicht-Ziel 3); an Yama gemeldet.

### Gesamturteil

**ABGENOMMEN** an `c3d52f09`, Fehlerklasse KEINE. Alle sechs Kriterien plus B3 mit eigenen
Messungen und Gegen-Beweisen grün; §15-Grenze gehalten (keine Probe berührte je eine Datenbank);
Nicht-Ziele gehalten (startet nichts, beendet nichts, `browser-buehne.sh` content-identisch zur
Basis — selbst gediffed). Ball: **Release-Prüfer**. Randnotiz an den Planner (nicht blockierend):
der Index-Zustand des Arbeitsbaums (neue Dateien als „D"+„??") ist die A-07-Phänomen-Klasse und
gehört in dessen Befundsammlung.

## Release-Prüfung A-04 (§10) — 10.08.2026

```yaml
auftrag: A-04
abnahme_commit: c3d52f09
release_commit: c3d52f09
votum: RELEASE_FREI
ci: pass                      # beide Suiten am HEAD 6ebf236d selbst gefahren
artefakte_reproduzierbar: true
migration: nicht_anwendbar    # kein Datenpfad, kein Schema
rueckweg: pass                # rein additiv, Rückdiff sauber angewendet (--check)
smoke_test_plan: "Erstnutzung nach Anker: bash scripts/buehnen-waechter.sh VOR der nächsten
  Browserabnahme, Aufruf samt Ausgabe in den Abnahmebericht"
befunde:
  - "OFFEN AN YAMA (kein Blocker): Realfund PID 48098 läuft weiter — verwaiste Bühne seit
    05.08. 00:58, ppid 1, php84 -S 127.0.0.1:65535 …server.php; löst als ticket_testing/OK
    auf. Beenden ja/nein entscheidet Yama (Nicht-Ziel 3), heute 10.08. erneut per ps belegt."
  - "ENV-NOTIZ P3: die A-07-Index-Phantome (neue Scope-Dateien als D+?? zugleich) bestehen
    fort — content-diff aller vier Scope-Dateien gegen c3d52f09 je 0, deshalb kein Blocker."
```

**Je Prüfpunkt der Beleg (alles selbst gemessen, HEAD `6ebf236d`):**

1. **Kette** — jeder Übergang per `git merge-base --is-ancestor`, Exit je 0:
   `d58b220e` (BEREIT) → `17984c82` (IN_ARBEIT) → `c3d52f09` (Bau) → `8fb99a30`
   (CODE_FERTIG) → `b6a63e3e` (ABGENOMMEN) → HEAD. IN_ARBEIT stand **vor** der ersten
   Scope-Änderung: `git show 17984c82 --stat` = nur `docs/STATUS.md`; zwischen `17984c82`
   und `c3d52f09` liegen vier fremde Doku-Commits, keiner berührt den Scope.
2. **Votum = Kandidat** — Evaluator-Votum im Blatt und `letztes_votum` in STATUS zeigen beide
   auf `c3d52f09`; das ist der Release-Kandidat, `b6a63e3e` ist Vorfahr von HEAD.
3. **Tore am Kandidaten grün** — beide Suiten am HEAD selbst gefahren:
   `node --test scripts/__tests__/buehnenWaechter.test.mjs` → `tests 7 · pass 7 · fail 0`;
   `node --test scripts/__tests__/browserBuehne.test.mjs` → `tests 7 · pass 7 · fail 0`.
   Alle vier Scope-Dateien am HEAD content-identisch zu `c3d52f09`
   (je `git show c3d52f09:<pfad> | diff -q - <pfad>` = 0); `git log c3d52f09..HEAD --
   <4 Scope-Pfade>` = leer — der A-07-Parallel-Bau (Tor-Strang) hat den Scope nicht berührt.
4. **Scope-Reinheit** — `git show c3d52f09 --stat` = **exakt 4 Dateien, 364(+)/0(−)**.
   Anker-Abweichung selbst gewürdigt: der Scope-Block (Z.56 f.) nennt nur die zwei Skripte,
   aber A-04-6 (P1, Blatt Z.167) **verlangt** `ANKER-BROWSER.md` und die B3-Auflage (Blatt
   Z.228/252) **verlangt** `browserBuehne.test.mjs` — alle vier Dateien kriteriengedeckt,
   offen deklariert (§11-Abweichungsblock), kein Befund.
5. **Rückweg** — Diff ist rein additiv (numstat 7/11/197/149 Einfügungen, 0 Löschungen),
   nur Skript + Tests + ein Doku-Absatz, kein Datenpfad, keine Migration. Zerstörungsfrei
   geprüft: `git diff c3d52f09^ c3d52f09 | git apply --reverse --check` = sauber —
   `git revert c3d52f09` genügt als Rückweg.
6. **§15** — Testcode selbst gegriffen: `grep -E 'mysql|PDO|DB_HOST|createConnection|3306|3307'`
   über beide Suiten = **0 Treffer**; `ticket_testing` kommt nur als Zeichenkette in
   Prozess-Env/Wegwerf-`.env.testing` vor (Fixture-Weg Rest 2). Wächter-Code ohne
   `kill/rm/mv/mysql` (Kommentare raus, 0 Treffer) — beendet nichts, verbindet nichts.
7. **Offene P0/P1** — keine: Fehlerklasse KEINE, einziger Evaluator-Restpunkt ist die
   P3/UMGEBUNG-Randnotiz (A-07-Klasse); der Realfund ist eine Betriebsentscheidung, kein
   Befund am Auftrag.

**Urteil: `RELEASE_FREI`.** Veröffentlichung genehmigt Yama (§10); nach v1.2-Vertretung folgt
ein Sicherungs-Push (`git push fork auto/hausplaner-integration`) — Ergebnis im STATUS-Vermerk.

---

## ZWEITVOTUM des Evaluators (10.08.2026) — unabhängig gefahren, bevor das Erstvotum vorlag

```yaml
auftrag: A-04
commit: c3d52f09
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "frischer Worktree auf dem Pruef-SHA + Elter-Worktree als Kontrolle · alle FUENF
  Mutationen aus A-04-5 · die B3-Mutation aus meinem eigenen A-03-Befund · zwei echte php -S-
  Buehnen ausserhalb der Suite"
browser: nicht_anwendbar
befunde:
  - "P3/BEWEIS: der im Blatt gefuehrte basis_sha 89f373d9 taugt fuer EINE der vier Scope-Dateien
    nicht als Kontrolle — scripts/__tests__/browserBuehne.test.mjs existiert dort nicht.
    Tragfaehig ist der Elter des Baus, 331cd125."
```

**Beide Voten sind unabhängig entstanden** — ich hatte den Prüfstand aufgebaut und gemessen,
bevor das Erstvotum committet war; die Übereinstimmung ist deshalb ein Ergebnis, keine Übernahme.
*Der Claim-Mechanismus hat hier nicht gegriffen: A-04 fiel auf `ballbesitz: evaluator`, und zwei
Instanzen haben ihn genommen. Das ist der Vorfall, nicht das Votum.*

### Was ich zusätzlich gemessen habe

```text
A-04-5 verlangt MINDESTENS FUENF Mutationen — alle fuenf selbst gefahren,
Anker je genau 1x, md5 vor und nach identisch:

  M1 Erkennung auf EINE Startform verengt        fail 3   GEFANGEN
  M2 Vergleich auf Suffix aufgeweicht            fail 1   GEFANGEN
  M3 Exitcode 3 auf 0 (Befund-Ausgang)           fail 2   GEFANGEN
  M4 Meldung ohne Datenbanknamen                 fail 1   GEFANGEN
  M5 Prozesssuche auf den eigenen Baum verengt   fail 3   GEFANGEN

Zusaetzlich die Mutation aus MEINEM A-03-Befund, deren Ueberleben B3 ueberhaupt ausgeloest hat:
  "APP_ENV weg von der exec-Zeile"               fail 1   GEFANGEN — und zwar genau durch B3
```

**Damit ist die Lücke aus der A-03-Abnahme nachweislich zu**, nicht nur behauptet.

```text
Suiten selbst gefahren   Waechter 7/7 · browserBuehne 7/7 · Elter 331cd125: 6/6
Statik                   bash -n SYNTAX-OK · node --check OK
A-04-6                   grep 'buehnen-waechter' ANKER-BROWSER: Basis 0 -> Pruefstand 1
```

**Eigene Gegenprobe außerhalb der Suite** — zwei echte `php -S`-Bühnen mit Fantasienamen,
kein Laravel, keine Datenbankverbindung (§15):

```text
DB_DATABASE=zz_evaluator_fantasie php -S …   -> BUEHNE FALSCH,   Name in der Meldung
php -S … (ohne Angabe)                       -> BUEHNE UNSICHER, "UNBEKANNT"
beide Proben leben nach dem Lauf             -> A-04-4 haelt
Kontrolle, Waechter allein aufgerufen        -> 1 reale fremde Buehne, ticket_testing, exit 0
```

*Der Wächter hat dabei eine **verwaiste Bühne aus einem fremden Worktree** gefunden
(`ticket-a01`, Port 65535) und korrekt als `OK` eingestuft — er wirkt nicht nur in der Suite.*

### Mein eigener Messfehler, offengelegt

**Ich hatte `vendor/` im Prüfstand vergessen.** Dadurch fiel `A-03-4 KONTROLLE (must_preserve)`
an der ersten Messung — *und ich war einen Schritt davon entfernt, meinen eigenen Aufbau als
Regression zu melden.* Mit `vendor` verlinkt: 7/7, am Elter ebenso 6/6.

> **Dieselbe Klasse wie mein `node_modules`-Fehler vom 03.08.** Das Prüfstand-Rezept nennt beide
> Schritte; ich habe einen ausgelassen und es erst durch die Kontrolle am Elter bemerkt.
> *Genau deshalb gehört zu jedem Rot die Kontrolle — sonst hätte ich hier eine Regression
> gemeldet, die es nie gab.*

### Zum P3 des Erstvotums

**Bestätigt und derselben Klasse zugeordnet:** die `D`/`??`/`MM`-Einträge der vier Scope-Dateien
sind die bekannten Stale-Index-Phantome (A-07). *Der Inhalt ist identisch, gemessen über
`git show <sha>:<pfad> | diff -` — die verlässliche Probe, nicht `git status`.*
