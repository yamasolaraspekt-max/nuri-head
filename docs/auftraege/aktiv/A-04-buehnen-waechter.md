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

> **Ich habe ihn NICHT sofort behoben, und das ist Absicht.** Gemessen auf diesem Zweig:
> `grep -c 'browser-buehne' ANKER-BROWSER.md` = **2** *(selbst gemessen 08.08.; im Blatt stand **0**, das war der Stand vor dem Merge
> `27a61da9`)* — A-03s Bau liegt seit dem Merge auf dem Arbeitszweig und ist hier
> nicht gemergt. **Der Satz ist auf DIESEM Zweig noch wahr.** Ihn jetzt zu streichen hieße, auf ein
> Skript zu verweisen, das von hier aus nicht existiert.
>
> **Auflage:** B2 wird mit dem A-03-Merge geschlossen, nicht davor. *Wer eine Aussage korrigiert,
> bevor der Zustand eintritt, den sie beschreibt, baut den nächsten Widerspruch.*

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

## Offene Punkte für den Plan-Prüfer

1. **Ist der Detektor wirklich besser als eine dritte Aufrufform — oder baue ich eine zweite
   Wahrheit neben A-03?** *Meine Position: er beantwortet eine andere Frage („was läuft" statt „wie
   starte ich") und wird deshalb nicht mit A-03 auseinanderlaufen können. Aber die Frage gehört
   gestellt, und `NICHT NOTWENDIG` ist ein zulässiges Votum.*
2. **Soll der Wächter im Commit-Tor mitlaufen?** *Ich schlage NEIN vor: das Tor ist der einzige
   Commit-Weg aller Rollen, und A-02 hat gerade gezeigt, was eine zusätzliche externe Abhängigkeit
   dort anrichtet. Ein Aufruf vor der Browserabnahme genügt.*
