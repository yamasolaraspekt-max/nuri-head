# B5 — ein Zählergebnis, das einen Befund trägt, wird nie ohne seine Trefferzeilen gemeldet

```yaml
auftrag: "B5"
art: "sechste Barriere, nach dem Muster von B3 und B4 — Regel im BEFEHL, nicht im Kopf"
titel: "Wer mit -c etwas behauptet, faehrt denselben Lauf ohne -c und liest, was er gezaehlt hat"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 1734aa3b
prioritaet: P1
anlass: "Yamas Auflage 0b vom 11.08. 22:12. Fuenf Faelle derselben Klasse an EINEM Tag;
         §13 loest die Ursachenpruefung bei der ZWEITEN Wiederholung aus."
verursacher: "planner (alle fuenf Faelle sind meine)"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## Die Regel, wörtlich wie Yama sie gesetzt hat

> **B5 · Ein Zählergebnis, das einen Befund trägt, wird nie ohne seine Trefferzeilen gemeldet.**
> *Wer `-c` benutzt, um etwas zu behaupten, führt denselben Lauf ohne `-c` und liest, was er gezählt
> hat. Gilt für alle fünf Rollen, gilt auch für Messungen über die eigene Werkbank.*

## Die fünf Fälle — alle meine, alle an einem Tag, jeder mit seiner Zahl

```text
1  F-031 CSG als "gebaut" gezaehlt          grep auf 'csg' -> 1 Treffer.
   (603eddc2, vor dem Schreiben gefunden)   Beim ANSEHEN: dachAusschnitt.ts:10
                                            " * - Stufe C (NICHT hier): … CSG"
                                            Ein Treffer im DATEIKOPF ist kein Code.
2  Baurichtlinie: zwei greps ins Leere      Zitate gehen ueber ZWEI Zeilen, mein
   (dcf0071c)                               einzeiliger grep fand 0 und ich hielt es
                                            fast fuer einen Befund.
3  "die Werkbank kennt sie nicht"           1 Treffer in SCHICHTEN.md, den ich als
   (dcf0071c)                               Fehlmessung abtat — er war echt und hat
                                            meine These verbessert.
4  W-07 Platzhalter: 7, einer falsch        grep -rnoE '<[^>]{3,40}>' -> 7.
   (1734aa3b)                               Der siebte war "< 1 mm²", ein
                                            VERGLEICHSOPERATOR in einer Tabelle.
5  W-07 Platzhalter: 6, DREI fehlten        Ich setzte {2,40} ins Muster, um Fall 4
   (Yama fand 9)                            loszuwerden — und verlor :17 (86 Zeichen),
                                            :29 und :30. Der Filter gegen einen
                                            Fehlertyp erzeugte einen anderen.
   Zusatz: awk-Scope-Filter                 fing auch "NICHT im Scope"-Zeilen mit und
                                            meldete vier Blaetter statt keines.
```

> **Fall 4 und 5 sind die Lehre in Reinform: ich habe den ersten Messfehler bemerkt, ihn mit einer
> engeren Grenze „behoben" — und dabei einen größeren erzeugt, ohne es zu sehen.** *Beide Male hätte
> ein Blick auf die Trefferzeilen gereicht. **Beim ersten Mal hätte `-n` den Vergleichsoperator
> gezeigt; beim zweiten Mal hätte `-n` gezeigt, dass drei Zeilen fehlen.*** Yama hat es mit einem
> `grep -n` ohne Längengrenze in einem Lauf gefunden.

## DECISION

```text
WO       scripts/commit-pruefen.sh — dasselbe Tor, das B4 (TICKET_ROLLE) traegt.
WAS     Das Tor kann nicht pruefen, WIE gemessen wurde. Es kann aber pruefen, ob eine
        Botschaft eine ZAHLENBEHAUPTUNG ohne Belegzeilen traegt.
FORM    Warnung, nicht Abbruch — Stufe 1 der Barrierenleiter.
        Grund: eine harte Sperre auf Zahlen in Commit-Botschaften wuerde jeden
        legitimen Bericht blockieren (Suite 1692/1692, 0 Platzhalter, 5 von 10).
        Ein Abbruch, der bei jedem zweiten Commit falsch anschlaegt, wird umgangen —
        das ist an A-03 belegt (Riegel um artisan serve, benutzt wurde php -S).
DAZU    Eine Zeile im PRUEFWEG jeder Rolle: "Zaehlergebnis nie ohne Trefferzeilen."
        Die Regel wirkt beim SCHREIBEN, das Tor erinnert beim COMMITTEN.
NICHT   Kein Verbot von grep -c. Es ist richtig, wenn die Zahl SELBST der Gegenstand
        ist (Suite 1692/1692). Verboten ist, aus einer Zahl einen BEFUND zu machen,
        ohne die Zeilen gelesen zu haben.
```

> **Die Unterscheidung ist der Kern, und sie muss ins Blatt:** *„Die Suite zählt 1692" ist eine Zahl
> als Gegenstand — die Trefferzeilen wären sinnlos.* **„CSG kommt einmal vor, also ist es gebaut" ist
> ein Befund aus einer Zahl — dort ist die Zeile alles.** *Wer B5 als „nie `-c` benutzen" liest, hat
> sie falsch verstanden und macht jede Suite-Meldung unlesbar.*

## Nicht-Ziele

- **Kein Abbruch am Tor.** Warnung. *Begründung oben, mit Beleg an A-03.*
- **Keine Prüfung, ob die Messung inhaltlich richtig ist.** Das kann kein Tor.
- **Kein Anfassen von B1–B4.** Sie bleiben unverändert; B5 tritt daneben.
- **Keine Änderung an `resources/**`.** Das Tor ist ein Shell-Skript in `scripts/`.

## Scope

```text
scripts/commit-pruefen.sh                    Warnzeile ergaenzen
docs/ARBEITSREGELN.md                        B5 in die Barrierenliste
docs/rollenkette/**/PRUEFWEG*.md ODER die Rollenblaetter   eine Zeile je Rolle
   -> der GENERATOR entscheidet nach Messung, wo der Pruefweg tatsaechlich steht.
      Ich habe es NICHT gemessen und behaupte es deshalb nicht (das waere Fall 6).
```

## Wiederverwendungsprüfung (§5)

```text
B4 / TICKET_ROLLE     VORHANDEN im Tor — Muster fuer eine Marken-/Warnzeile
B1 (&& zwischen
   Skript und Tor)    VORHANDEN — Muster fuer eine harte Sperre (hier NICHT gewaehlt)
B2 (ganze Datei nach
   der Korrektur)     VORHANDEN — der naechste Verwandte: B2 prueft NACH einer
                      Korrektur, B5 prueft VOR einer Behauptung
A-10 (Melder am
   leeren Ergebnis)   VORHANDEN — dieselbe Familie: sag es, wenn du nichts hast.
                      B5 ist ihr Zwilling: sag WAS du gezaehlt hast.
scripts/commit-pruefen.sh   VORHANDEN, traegt bereits Rollenmarke, Pfadpruefung,
                      Index-Angleichung -> anschliessen, nicht neu bauen
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER (scripts/, kein app/)
Testdaten-Ziel                                               KEINES
Datenbank                                                    NICHT BERUEHRT
Prozessbindung                                               JA — B5 wird Regel fuer alle
                                                             fuenf Rollen
Werkzeuge                                                    bash; die Tor-Zusagen
                                                             (scripts-Suite) muessen gruen
                                                             bleiben
```

**Erstnutzer:** *ich selbst, beim nächsten Zählbefund. **Und der Test der Barriere ist, ob sie MICH
fängt** — fünf von fünf Fällen sind meine.*

## Akzeptanzkriterien

**B5-1 (P1, die Warnung existiert und feuert):** Das Tor gibt eine Warnung aus, wenn die
Commit-Botschaft eine Zahlenbehauptung ohne Belegzeile trägt. **Rot-Lage heute messbar:**

```text
grep -cE 'Trefferzeile|B5' scripts/commit-pruefen.sh   -> heute 0
```

**B5-2 (P1, sie feuert NICHT bei legitimen Zahlen):** Eine Botschaft mit `Suite 1692/1692` oder
`0 Platzhalter` löst **keine** Warnung aus. *Nachweis: zwei Probeläufe im Wegwerf-Repo, einer der
feuert, einer der schweigt — beide Ausgaben im Bericht.* **Ohne diesen Gegenbeleg ist die Barriere
eine Belästigung und wird umgangen.**

**B5-3 (P1, kein Abbruch):** Der Rückgabewert des Tors bleibt bei einer B5-Warnung **unverändert
gegenüber heute**. *Nachweis: derselbe Commit einmal mit und einmal ohne die Warnung, `echo $?`
beide Male gleich.*

**B5-4 (P1, die Regel steht in ARBEITSREGELN):** B5 steht in der Barrierenliste, mit dem
**Unterschied zwischen „Zahl als Gegenstand" und „Befund aus einer Zahl"** — sonst wird sie als
„nie `-c`" gelesen.

**B5-5 (P1, der Prüfweg wird GEMESSEN, nicht angenommen):** Der Generator **misst zuerst**, wo der
Prüfweg je Rolle tatsächlich steht, und trägt die Zeile dort ein. *Falls es keinen gemeinsamen
Prüfweg gibt, sagt er das — statt eine Datei zu erfinden.* **Ich habe es nicht gemessen und
behaupte es deshalb nicht.**

**B5-6 (`must_preserve`):** `resources/**` und `app/**` byte-identisch. Die `scripts/`-Suite bleibt
grün (ohne Zahl). Die bestehenden Torfunktionen — Rollenmarke, Pfadprüfung, Index-Angleichung —
unverändert; **Nachweis: `git diff` zeigt nur Einfügungen im Tor, 0 gelöschte Zeilen.**

**B5-7 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, mindestens zwei Befehlszeilen
und zwei Ausgabewerte. **Und die Messung fällt UNMITTELBAR vor der ersten Änderung** *(Lehre aus
`ce30174f`)*.

## Kantenliste

```text
Warnung feuert bei jeder Zahl        -> B5-2 faengt es. Eine Barriere, die immer
                                        anschlaegt, wird abgeschaltet (A-03-Beleg).
Warnung wird zum Abbruch             -> VERBOTEN, B5-3 misst den Rueckgabewert.
"nie grep -c benutzen"               -> FALSCHLESUNG. B5-4 verlangt die Unterscheidung
                                        im Regeltext.
Das Tor prueft die Messung inhaltlich -> kann es nicht, ist Nicht-Ziel.
Pruefweg-Datei existiert nicht        -> B5-5: dann sagt der Generator das.
```

## Rückweg und Entdeckung

**Rückweg:** eine Warnzeile im Tor, eine Regelzeile, eine Prüfwegzeile. `git revert` genügt; das Tor
bleibt in jeder Zwischenstufe funktionsfähig, weil die Warnung den Rückgabewert nicht ändert.

**Entdeckung:** Tritt ein sechster Fall derselben Klasse auf — *eine Zahl trägt einen Befund, die
Zeilen sind nicht gelesen* —, hat die Warnung nicht gewirkt. *Dann ist die nächste Stufe eine harte
Sperre, und der Preis dafür ist an fünf Fällen belegt.*

## Konfliktprüfung (§5)

```text
§3 gemessen 12.08.   1 IN_ARBEIT -> W-21/1 (9bd728fe), Scope werkbank/W-21/** + REGISTER.md
B5 (dieses)          scripts/commit-pruefen.sh + ARBEITSREGELN.md + Pruefweg
                     -> KEINE Beruehrung mit W-21. Disjunkt.
W-07N                werkbank/W-07/** + REGISTER.md -> beruehrt W-21s Scope, wartet auf §3
A-09 / A-11          haben scripts/commit-pruefen.sh beruehrt, beide VEROEFFENTLICHT
                     -> kein offener Auftrag auf dem Tor. Basis frei.
```

```yaml
fehlerklasse: "Messweg — fuenf Faelle, alle planner, alle 11./12.08."
prioritaet: P1
warteschlange: "kann sofort in DoR — beruehrt REGISTER.md NICHT und ist damit von W-21
                unabhaengig. Reihenfolge: vor W-07N, weil B5 dessen Messungen absichert."
kern: "eine Zahl beweist nichts, solange niemand gelesen hat, WAS sie gezaehlt hat.
       Und ein Filter, der einen Fehlertyp ausschliesst, erzeugt leicht einen anderen."
```
