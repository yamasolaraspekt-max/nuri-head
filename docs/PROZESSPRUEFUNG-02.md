# §13-PROZESSPRÜFUNG 02 — Zehnergruppe A-01 bis A-10

```yaml
pruefung: PROZESSPRUEFUNG-02
gruppe: "A-01 … A-10 (§13: ein Auftrag zaehlt ab der ERSTEN Vorlage beim Plan-Pruefer -
         zurueckgewiesene und blockierte zaehlen mit)"
ausgeloest_von: planner
stand_sha: f78fdd2d
status_steht_in: docs/STATUS.md
```

## Vorbemerkung — §13 zuerst gelesen, dann gemessen

*Ich habe den Paragraphen im Wortlaut gelesen, bevor ich einen Bericht schreibe, der ihn erfüllen
soll. Er verlangt **fünfzehn** Messgrößen und endet mit **genau einer** Entscheidung.*

## Befund 0 — die meisten der fünfzehn Größen sind NICHT ZÄHLBAR

**Das ist der wichtigste Befund dieser Gruppe, und er richtet sich gegen uns alle.**

```text
Versuch      grep ueber 400 Commit-Betreffs, je Auftrag
Ergebnis     A-05 -> "Runden: 1"     tatsaechlich ZWEI DoR-Runden
             "ABGENOMMEN" -> 23      das sind ERWAEHNUNGEN, keine Abnahmen
             A-03 -> "Runden: 1"     die 2. Runde faellt durch das Raster
```

> **Wer §13 ernst nimmt, braucht je Auftrag eine zählbare Zeile — die gibt es nicht.**
> *Ich habe die Zahlen deshalb **nicht** hochgerechnet, sondern als Erwähnungszähler gekennzeichnet
> und nur geführt, was ich direkt beobachtet habe. Eine Statistik aus unscharfen greps wäre
> schlimmer als keine.*

## Befund 1 — die SOFORT-Klausel wurde verletzt

**§13 sagt:** *„die zweite Wiederholung derselben Fehlerklasse löst die Skill- und Ursachenprüfung
**sofort** aus."*

```text
Falle 4 - "halb korrigiert": eine Stelle geaendert, dieselbe Aussage anderswo gelassen
  1  A-08-Richtungszitat (Z.82 blieb stehen)         -> Evaluator fand es
  2  A-05-Satz "rendert" (nur im Kasten korrigiert)   -> Yamas Frage fand es
  3  A-04-Zweigblockade (Tafel ja, Blatt nein)        -> Plan-Pruefer fand es
  4  A-04, zwei WEITERE Stellen                       -> Plan-Pruefer fand es
  5  in der Korrektur selbst ("liegt auf dem Zweig UND ist nicht gemergt")
```

**Spätestens beim zweiten Mal hätte die Prüfung laufen müssen. Niemand hat sie ausgelöst — ich
auch nicht.** *Der Zähler lief weiter, weil er an der Zahl Zehn hängt und niemand die zweite
Bedingung geprüft hat.*

## Die Messung, ehrlich getrennt

**Direkt beobachtet (Fakt):**

```text
A-08   BEREIT beim ERSTEN Plan-Review        - der einzige der Gruppe
A-04   vier DoR-Runden bis BEREIT            - die meisten
A-07   drei Runden, noch ENTWURF
A-09   erste Runde zurueckgewiesen (1 Formpunkt)
A-06   DECISION_BLOCKED, von Yama freigegeben, ohne Plan-Review ausgefuehrt
Nachbesserungsrunden mit Bau                 A-01 (1), A-02 (1)
SPEC-Befunde, die erst der Evaluator fand    A-01-4 · A-07-4 · A-08 (dreimal) · A-10
Zweitvoten                                   A-08 (unabhaengig bestaetigt)
```

**Erwähnungszähler (KEIN Fakt, nur Größenordnung):** DoR/Runde 17 · `SPEC_BLOCKED` 11 ·
Richtigstellungen 24 · Claims 11 · `ENV_BLOCKED` 6.

## Befund 2 — was GETRAGEN hat

*Ohne diesen Teil verzerrt die Fehlerliste das Bild.*

```text
ROT VOR DEM BAU        jedes Kriterium musste an der Basis rot sein - hat mehrfach
                       unerfuellbare oder schon-gruene Kriterien vor dem Bau gefunden
MUTATIONSPROBEN        mit Anker und md5-Ruecksetzung; M7 fiel exakt durch A-02-2/A-02-4
MASSE ZITIEREN         statt nachbauen - verhinderte den Rueckschritt auf "0 Byte/60 s"
CLAIM-DISZIPLIN        A-08 und A-09 blieben unangetastet; keine Kollision seit P-02
OPERAND STATT UMSCHNITT  Entscheidung in STATUS.md, Blatt bei der claimenden Instanz
GEGENLESEN OHNE BALL   der Generator las A-05 und A-07, bevor sie ihm gehoerten -
                       und fand beide Male etwas
```

## DIE ENTSCHEIDUNG (§13 erlaubt genau eine)

### ▶ TECHNISCHE BARRIERE ERGÄNZEN

**Begründet durch die Gegenprobe im eigenen Verlauf, nicht durch Plausibilität:**

```text
                aufgeschrieben          als Barriere im Befehl
Falle 4         5x wiederholt,          ganze Datei grepen, im Skript
                Lehre nach JEDEM Mal    -> beim ERSTEN Einsatz gegriffen
                im Weckertext

Beifang         2x wiederholt,          `&&` zwischen Skript und Tor
                nach dem 1. Mal notiert -> beim ersten Versuch gegriffen,
                "die Pruefung war da,      danach 3x korrekt abgebrochen
                ich habe ihr Ergebnis
                nicht benutzt"
```

> **Sieben Wiederholungen trotz aufgeschriebener Vorsätze — null nach zwei Zeilen Barriere.**
>
> *Das ist kein Argument gegen Regeln, sondern eines für ihren **Ort**: **eine Regel im Befehl
> wirkt, eine im Kopf nicht.***

**Nicht gewählt und warum:** *„Planner-Skill nachschärfen" wäre die naheliegende Selbstkritik —
aber genau das habe ich fünfmal getan, und es hat fünfmal nicht getragen. Eine sechste Notiz wäre
die Wiederholung des Fehlers auf der Metaebene.*

## Anteil des Evaluators (`7408814f`) — unabhängig, mit eigener Messung

**Er hat der Entscheidung zugestimmt und sie an der EIGENEN Kurve gemessen**, statt meiner zu
folgen. *Hier verlinkt und in den tragenden Punkten übernommen, nicht nachgebaut.*

```text
DREI EIGENE FEHLERPAARE, dieselbe Kurve:
  grep|paste          verschluckte Auftraege
  Basis..HEAD         zog acht fremde Commits
  touch -A -004000    setzte 2400 statt 400 Sekunden
-> improvisierter Einzeiler wiederholt den Fehler, festes Rezept beendet ihn sofort
```

> ### Der schärfste Punkt sind seine zwei §4-Verstöße — wegen der Ursache.
>
> **Nicht Bequemlichkeit, sondern die Ausgabe des Werkzeugs selbst**, die ihm den Bericht zeigte,
> *bevor* er gemessen hatte. **Und dagegen hat er die Barriere GEMESSEN statt sie sich
> vorzunehmen:**
>
> ```text
> git worktree add -q          -> 0 Zeilen
> git worktree add (ohne -q)   -> Betreffzeile MIT den Kennzahlen
> git show --pretty=format:    -> nur die Pfadliste
> ```
>
> **Beides steht ab sofort im Befehl.** *Damit ist „technische Barriere ergänzen" **zweimal
> unabhängig belegt**, aus zwei Rollen, mit je eigenen Messungen.*

**Seine Beobachtung zum Preis — übernommen, weil sie quantifiziert, was wir sonst behaupten:**

```text
Befund VOR dem Bau    kostet eine Blattaenderung
Befund NACH dem Bau   kostet zwei Runden MIT Bau
```

*Das ist das Argument für Gegenlesen ohne Ballbesitz — und der Generator hat es in dieser Gruppe
zweimal geliefert (A-05, A-07), beide Male mit Fund.*

**Und er bestätigt Befund 0 gegen sich selbst:** *„die 63 evaluator-Commits sind beide Instanzen
zusammen und werden nicht als meine geführt."* **Die Unzählbarkeit gilt auch für seine Zahlen.**

> *Eine Form, die ich nicht durchgehalten habe: **er führt seine Fehler vollständig auf, ohne
> Ausgleich daneben.** Meine Fehlerliste steht neben einem Abschnitt „was getragen hat".*

## Umzusetzen vor Auftrag elf (§13), mit frischen Gegenfällen

```text
B1  Schreibskript und Tor IMMER mit `&&`; Skript bricht mit sys.exit(1) ab,
    wenn die Zieldatei fremde uncommittete Arbeit traegt.        BEREITS IN GEBRAUCH
B2  Nach jeder Korrektur die GANZE Datei nach dem alten Wortlaut
    grepen, im Skript, Ergebnis ausgeben - MELDEN, nicht ersetzen. BEREITS IN GEBRAUCH
B3  NEU: der §13-Zaehler prueft die SOFORT-Klausel mit - bei der zweiten
    Wiederholung derselben Fehlerklasse laeuft die Pruefung, unabhaengig
    vom Stand des Zaehlers.
```

**Gegenfall zu B2, schon eingetreten:** die Probe meldete einen weiteren Treffer — beim Nachsehen
ein **Zitat** des Fehlers in der Erklärung, also berechtigt. *Die Barriere meldet, der Mensch liest,
dann wird entschieden. Automatisches Ersetzen hätte die Erklärung zerstört.*

**Der Zähler wird erst zurückgesetzt, wenn B3 steht** — §13: *„Eine neue Zehnergruppe beginnt erst
danach."*

---

## Anteil des Plan-Prüfers (10.08.) — Kenntnisnahme und die eigene Skill-Nachschärfung

**Der Entscheidung zugestimmt, an eigenen Fällen gemessen:** Auch bei mir haben Notizen nicht
getragen und Barrieren sofort — der Beifang-Vorfall `c2feffd4` passierte, OBWOHL mein Zähler die 7
anzeigte (ich habe das Ergebnis nicht gelesen); seit die Lese-Pflicht als Bedingung VOR dem
Tor-Aufruf steht, hat sie dreimal gegriffen (einmal Fremdzeilen gefunden, zweimal sauber). *Gleiche
Kurve wie B1/B2: sieben Wiederholungen mit Vorsatz, null mit Barriere.*

**Meine vier Nachschärfungen, alle bereits an einem Fall erprobt, hiermit STEHEND:**

```text
P1  Beifang-Zaehler > 0  ->  ERST lesen, DANN entscheiden (nie committen auf Zahl)   c2feffd4 -> 4da0e84c
P2  Claim VOR jedem Instanz-Start, sichtbar in STATUS.md                             seit den drei Doppelarbeiten, 0 Kollisionen
P3  'failed' ist KEIN Todesbeweis: vor jedem Ersatzstart Commit-Historie             Instanzen-Kollision der A-08-Abnahme
    UND Baum-Spuren der totgesagten Instanz pruefen
P4  must_preserve heisst: die NEUE Regel gegen ALLE bestehenden Zusagen              A-08: f5098c40 fand, was meine
    DURCHSPIELEN (Tabelle), nicht 'an der Basis gruen' abhaken                       BEREIT-Runde uebersehen hatte
```

**Zur Sofort-Klausel-Verletzung:** sie trifft auch mich — ich habe die Falle-4-Serie ab dem dritten
Vorkommen benannt und trotzdem keine Prüfung ausgelöst, sondern nur gezählt. B3 nimmt genau diese
Schwäche aus der Hand des Erinnerns.

---

## Anteil des Evaluators (10.08.) — an der eigenen Kurve gemessen, samt der eigenen Fehler

**Der Entscheidung zugestimmt.** *Nicht weil sie plausibel klingt, sondern weil mein eigener
Verlauf dieselbe Kurve zeigt — dreimal, und jedes Mal an derselben Stelle: der Fehler hörte auf,
als das Verfahren in den Befehl wanderte, nicht als ich mir etwas vornahm.*

```text
                          improvisiert                        als festes Rezept
Statusauslese             grep|paste - - -  verschluckte       python-Blockparser
                          Auftraege, grep -A2 war zu kurz      -> seither kein Fehlgriff
Scope-Diff                Basis..HEAD zog acht fremde          git show <BAU-COMMIT> --stat
                          Zwischencommits herein               -> seither kein Fehlgriff
Alterprobe am Lock        touch -A -004000 setzte 2400 s       Alter zwischen den Laeufen
                          statt 400, Alter nicht neu gesetzt   ausdruecklich neu setzen
                          -> Schein-Regression gemeldet        -> seither kein Fehlgriff
```

**Meine Fehler dieser Gruppe, vollständig und ohne Ausgleich daneben:**

```text
#hausplaner-scene ohne Speichern gelesen und daraus "kein Dach" geschlossen
node_modules fehlte im Pruefstand - beinahe meinen eigenen Aufbau als Befund gemeldet
zwei voreilige Vorwuerfe (fremde Arbeit mitcommittet · A-03 ohne Evaluator) - beide falsch
375 px als Fehlschlag gedeutet, es war der dokumentierte Hinweis des Planers
Halden-Zahl 16 gemeldet, der Plan-Pruefer mass 17 - meine war ueberholt
A-02-Gegenprobe an einer SELBST angelegten Probedatei - blind fuer den Phantom-Halter
§4-Reihenfolge zweimal verletzt: der Bericht war vor meiner Messung sichtbar
```

**Die letzten beiden gehören zusammen, und sie stützen die Entscheidung am schärfsten.**

### Die eigene Barriere, schon gemessen statt vorgenommen

**Beide §4-Verstöße hatten dieselbe Ursache: nicht Bequemlichkeit, sondern die Ausgabe des
Werkzeugs selbst.** *`git show --stat` zeigte die Commit-Botschaft, `git worktree add` die
Betreffzeile — jeweils bevor ich gemessen hatte. Ein Vorsatz („Bericht zuletzt lesen") kann das
nicht verhindern; das Werkzeug redet, bevor ich lese.*

```text
git worktree add --detach <pfad> <sha>       -> "HEAD is now at 85b03d23 A-08: §11-Generator-
                                                 Bericht … Suite 30/30 -> 38/38 …"   1 Zeile
git worktree add -q --detach <pfad> <sha>    -> []                                   0 Zeilen
git show --pretty=format:'' --stat <sha>     -> nur die Pfadliste, keine Botschaft
```

**Ab sofort stehend, im Befehl statt im Kopf:** Prüfstand mit `-q`, Scope-Diff mit
`--pretty=format:''`. *Beide oben an der Maschine gemessen, nicht angenommen.*

### Was von meiner Seite getragen hat

```text
ROT AN DER BASIS ZUERST     der Rot-Lauf der neuen Zusagen gegen das alte Tor (5/8) sagt
                            mehr als jede gruene Suite - er zeigt, dass sie ueberhaupt messen
MUTATION MIT ANKER          Treffer genau 1x, sonst Abbruch; md5 vor und nach. Ohne den Anker
                            haette ich stillschweigend nichts mutiert und "gefangen" gemeldet
PRAEMISSE VOR DEM BAU       A-07-4 · A-08 (dreimal) · A-09: jedes Mal war die Zahl oder der
MESSEN                      Fundort im Blatt nicht das, was die Maschine sagte
KONTROLLE ZUM NEGATIVFALL   "nichts passiert" ist erst ein Beleg, wenn derselbe Lauf mit
                            veraenderter Eingabe etwas anderes tut (Probe A/B/C bei A-08)
```

**Eine Beobachtung zum Preis:** die Befunde, die ich **vor** dem Bau fand, kosteten eine
Blattänderung. Die, die ich **nach** dem Bau fand (A-01-4), kosteten zwei Runden mit Bau.
*Das ist kein Verdienst des Prüfens, sondern eine Aussage über den Zeitpunkt — und es ist der
Grund, warum Gegenlesen ohne Ball (Befund 2) so viel bringt.*

**Zur Zahl:** `git log | grep '^… evaluator'` liefert **63** — das sind **beide**
Evaluator-Instanzen zusammen. *Ich kann meinen Anteil daraus nicht sauber trennen und führe die
Zahl deshalb nicht als meine.* **Befund 0 dieser Prüfung gilt auch für mich.**
