# W-01N — Nachbesserung: die feste Suite-Zahl aus W-01/1-6 entfernen

```yaml
auftrag: "W-01N"
art: "Nachbesserung nach §12.5 — W-01/1 bleibt ABGENOMMEN, der Befund wirkt nicht rueckwirkend"
titel: "W-01/1-6 traegt die Zahl 1689/1689, gemessen sind 1692 — zahlfreie Form wie in W-02"
spur: A
heimat_app: ticket
status: BEREIT
status_steht_in: docs/STATUS.md
dor_beleg: "a5aab234 — plan-pruefer: 'W-01N und W-07N BEREIT beim ersten Review, beide Rot-Lagen
         selbst gemessen'. Angeglichen 12.08. vom Planner: der Blattkopf hing auf ENTWURF, waehrend
         Tafelzeile und DoR BEREIT sagten — der Pruefer hatte die Datei nicht angefasst."
basis_sha: ed7ccb70
basis_aktualisiert: "12.08. — vorher 548bef5c. Zwischen Schnitt und Bau liegen zwei WURZELFIXE und
         die gesamte Klasse-A-Schlussrunde; eine Basis von gestern haette den Bau auf einen Stand
         gestellt, den es nicht mehr gibt." 
prioritaet: P2
anlass: "SPEC-Rest aus der W-01/1-Abnahme (320a95c8), vom Evaluator verbucht"
verursacher: planner
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt. Der Befund ist meiner, deshalb ziehe ich ihn."
umfang: "zwei Zeilen in einem Blatt plus eine Regelzeile im Fahrplan — kleinster Auftrag der Gruppe"
```

## Der Befund — und er ist genau der Fehler, den ich selbst benannt habe

**Aus der Abnahme von W-01/1 (`320a95c8`), wörtlich:**

> *„Nicht erledigt bleibt `W-01/1-6`, das wörtlich **1689/1689** verlangt, während **1692** gemessen
> sind — das ist SPEC … **der Bauende kann eine Zahl nicht erfüllen, die schon bei der
> Blatt-Erstellung überholt war**, `dbb7ff66` ist Vorfahr der Basis. Der Befund bleibt verbucht und
> erzeugt einen Folgeauftrag."*

```text
W-01/1-6, Z.135 und Z.177    "die Insel-Suite (1689 Zusagen) MUSS unveraendert gruen bleiben"
                             "bleibt bei 1689/1689"
gemessen bei der Abnahme     1692/1692
Ursache                      A-10 hat drei Zusagen hinzugefuegt (dbb7ff66) — VOR meinem
                             Blatt-Schnitt. Ich habe eine Zahl aus dem Gedaechtnis
                             geschrieben statt sie zu messen.
```

> **Und das ist derselbe Fehler, den ich in A-07 selbst als Grundsatz formuliert habe:**
> *„Ein Kriterium mit fester Zahl wäre hier falsch; A-07-5 verlangt deshalb richtig ‚alle zum
> Zeitpunkt des Laufs vorhandenen, Zahl im Bericht'."*
>
> **Ich habe den Grundsatz aufgeschrieben und im nächsten Blatt gebrochen.** *Zehnter Fall meiner
> Klasse — und diesmal nicht „Stellvertreter statt Quelle", sondern schlichter: eine Zahl behauptet,
> die zwei Commits vorher schon anders war.*

## NACHTRAG 12.08. — der Befund ist stärker geworden, ohne dass jemand etwas getan hat

**Beim Schnitt lautete er „1689 im Blatt gegen 1692 gemessen". Heute sind es vier Zahlen für
dieselbe Sache:**

```text
1689/1689   steht im Blatt W-01-fang-beschreiben.md, Z.135 und Z.177   (Kriterium W-01/1-6)
1692/1692   Messung des Evaluators bei der W-01/1-Abnahme (320a95c8)   ZWEIMAL in Commits genannt
1693/1693   der heutige Stand — FUENFMAL unabhaengig genannt:
              Generator beim A-18-Bau · RELEASE+BETRIEBSPRUEFUNG A-15 · dieselbe fuer W-07N ·
              plan-pruefer bei W-07N Runde 2
1668        meine eigene Zaehlung per grep -cE '^\s*(test|it)\(' ueber 166 Testdateien
```

> **Der letzte Wert ist der interessanteste, und er gehört ins Blatt:** *ich habe **1668** gemessen,
> während die Suite **1693** meldet. Beide Zahlen sind richtig — sie messen Verschiedenes: `grep`
> zählt geschriebene `test(`-Aufrufe, der Lauf zählt **ausgeführte** Zusagen, und parametrisierte
> Tests erzeugen mehr Läufe als Zeilen. **Eine feste Zahl in einem Kriterium ist damit nicht nur
> veraltet, sobald jemand einen Test schreibt — sie ist schon in dem Moment unbestimmt, in dem zwei
> Rollen mit verschiedenen Werkzeugen messen.***

**Das ist der eigentliche Grund für die zahlfreie Form, und er ist stärker als der ursprüngliche:**
*nicht „die Zahl driftet" (das tut sie, dreimal in zwei Tagen), sondern **„die Zahl hat gar keinen
eindeutigen Wert"**, solange die Messmethode nicht mitgenannt wird. Genau das ist B5 und B6 in einem
Fall — und beide Barrieren sind inzwischen gebaut beziehungsweise in Arbeit.*

## Die Lehre ist schon gezogen — nur nicht in W-01

**Selbst gemessen über alle sechs Blätter:**

```text
W-01   Z.135, Z.177   "1689/1689"                        FESTE ZAHL  <- dieser Auftrag
W-02   Z.311          "Insel-Suite unveraendert gruen —
                       OHNE feste Zahl, damit kann sich
                       der Fall (1689 -> 1692) nicht
                       wiederholen"                       ZAHLFREI    ✓
W-04                  keine Suite-Zahl im Kriterium       ZAHLFREI    ✓
W-08                  dito                                ZAHLFREI    ✓
W-11                  dito                                ZAHLFREI    ✓
W-13                  dito                                ZAHLFREI    ✓
```

**Fünf von sechs Blättern sind schon richtig.** *W-02 hat die zahlfreie Form ausdrücklich mit
Begründung eingeführt — die Lehre wurde also während der Runde gezogen, nur das erste Blatt trägt
sie nicht. **Dieser Auftrag holt W-01 nach, er führt nichts Neues ein.***

## DECISION

```text
AENDERN     W-01/1-6 und die Auswirkungen-Zeile Z.135:
            "Insel-Suite bleibt unveraendert gruen" — OHNE Zahl.
            Nachweis bleibt: `git diff --stat` auf resources/ ist leer, und die Suite
            laeuft mit derselben Zahl wie VOR dem Bau (die im Bericht steht, nicht im Blatt).
NICHT       Die Abnahme wird NICHT berührt. W-01/1 bleibt ABGENOMMEN (§12.5).
AENDERN     Kein anderes Kriterium, keine andere Zahl, kein Blatt ausser W-01.
DAZU        Eine Regelzeile in FAHRPLAN-KLASSE-A.md: keine festen Suite-Zahlen in
            Kriterien, nur "unveraendert" plus die gemessene Zahl im BERICHT.
            Damit gilt es auch fuer Runde 2 und 3.
```

> **Warum die Abnahme nicht berührt wird, und warum ich das nicht selbst entschieden habe:** Die
> Änderung macht ein **unerfüllbares** Kriterium **erfüllbar**. Hätte ich sie eigenmächtig
> vorgenommen, wäre der abgenommene Bau nachträglich legitimiert worden — *das ist Grün-machen durch
> Spezifikationsänderung und wäre schwerer als der Fehler selbst.* **Der Evaluator hat es korrekt
> nach §12.5 behandelt: blockiert nicht, bleibt verbucht, Folgeauftrag.** Dieser hier.

## Nicht-Ziele

- **Keine Änderung an `resources/**`.** Kein Code, keine Zusage.
- **Keine Änderung der Abnahme von W-01/1.** Sie bleibt gültig.
- **Kein Anfassen der fünf anderen Blätter** — sie sind schon zahlfrei.
- **Keine neue Suite-Zahl ins Blatt.** *Die Ersetzung „1689" → „1692" wäre derselbe Fehler mit einer
  neueren Zahl. Der Punkt ist nicht die falsche Zahl, sondern **dass eine Zahl dort steht**.*

## Scope

```text
docs/auftraege/aktiv/W-01-fang-beschreiben.md   Z.135 und Z.177 — Zahl entfernen
docs/FAHRPLAN-KLASSE-A.md                       eine Regelzeile fuer Runde 2 und 3
```

*NICHT im Scope: `resources/**`, die Werkbank-Blätter, jedes andere W-Blatt.*

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    Editor. Die Suite wird NICHT
                                                             gefahren — dieser Auftrag aendert
                                                             kein Verhalten, nur eine Zahl im Text
```

**Erstnutzer:** *der Generator von Runde 2 — er darf keine Suite-Zahl mehr in ein Kriterium schreiben,
und die Regelzeile im Fahrplan sagt es ihm, bevor er es tut.*

## Akzeptanzkriterien

**W-01N-1 (P1, die Zahl ist weg):** In `W-01-fang-beschreiben.md` steht **keine** Suite-Zahl mehr in
`W-01/1-6` und in der Auswirkungen-Zeile. *Rot heute messbar:*

```text
grep -c '1689' docs/auftraege/aktiv/W-01-fang-beschreiben.md
-> heute > 0 (Z.135, Z.177, plus die Befund-Zitate)
-> Soll: nur noch in den ZITATEN des Befundes, nicht im Kriterium
```

**W-01N-2 (P1, das Kriterium bleibt prüfbar):** Die neue Fassung nennt einen **überprüfbaren**
Nachweis — `git diff --stat` auf `resources/` leer, Suite unverändert gegenüber dem Stand **vor** dem
Bau, Zahl im Bericht. *Ein Kriterium ohne Nachweis wäre schlechter als eines mit falscher Zahl.*

**W-01N-3 (`must_preserve`):** Die Abnahme von W-01/1 bleibt unberührt — **kein anderes Kriterium
wird geändert**, und die Änderung wird im Blatt als §12.5-Nachbesserung gekennzeichnet. *Nachweis:
`git diff` zeigt genau die zwei Stellen plus die Kennzeichnung, nichts sonst.*

**W-01N-4 (P1, die Regel gilt weiter):** `FAHRPLAN-KLASSE-A.md` trägt die Regel für Runde 2 und 3.
*Ohne sie wiederholt sich der Fall im nächsten Blatt, das eine Zahl braucht.*

**W-01N-5 (P1, §3 wird BELEGT):** Befehl mit Ausgabe für „kein anderer Auftrag auf `IN_ARBEIT`", an
beiden Orten, **mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer**. *E2 aus
Prüfung 03, angenommen in `b9dc3c35`.*

> **Aktualisiert 12.08.: die Prüfmethode ist inzwischen VERANKERT** — `docs/ARBEITSREGELN.md`, §3,
> Abschnitt „Die Prüfmethode". *Es gilt der dort stehende Ausdruck `^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT`
> und **nicht** das alte `[AW]`-Muster: es war blind für `B-`, `M-` und `P-`-Aufträge, und genau so
> ist `B5` durch die Schranke gefallen. **Wer hier noch das alte Muster benutzt, belegt §3 nicht,
> sondern täuscht es.** Und weil `B6` gerade läuft — ein `B-`Auftrag — ist dieser Auftrag der erste,
> bei dem der Unterschied unmittelbar wirkt.*

## Kantenliste

```text
"1689" in den Befund-Zitaten          -> MUSS stehen bleiben. Das Zitat des Fehlers ist
                                         die Begruendung; nur das KRITERIUM wird zahlfrei
                                         (B2-Gegenfall: der Treffer ist berechtigt)
"1692" einsetzen statt loeschen        -> VERBOTEN, derselbe Fehler mit neuerer Zahl
Suite aendert sich waehrend des Baus   -> genau der Fall, den die zahlfreie Form abdeckt
andere feste Zahlen im Blatt
  (276 Zeilen, 11 Exporte, 3 Zusagen)  -> BLEIBEN. Sie beschreiben den Ist-Zustand einer
                                         Datei zum Messzeitpunkt, nicht eine Bedingung,
                                         die spaeter erfuellt werden muss. Der Unterschied
                                         ist: Ist-Beleg gegen Soll-Kriterium.
```

> *Die letzte Zeile ist der eigentliche Kern und gilt für alle künftigen Blätter: **eine Zahl in
> einem Ist-Beleg ist richtig und datiert; eine Zahl in einem Soll-Kriterium ist eine Zeitbombe.***

## Rückweg und Entdeckung

**Rückweg:** zwei Textstellen und eine Regelzeile, `git revert` genügt.

**Entdeckung:** Taucht in einem späteren Blatt wieder eine Suite-Zahl in einem Kriterium auf, hat die
Regelzeile im Fahrplan nicht gewirkt — dann braucht es eine Barriere statt einer Regel. *Prüfbar mit
einem `grep` über `docs/auftraege/aktiv/W-*.md`.*

## Konfliktprüfung (§5)

```text
A-12     ENTWURF     Messlauf, FORMELSAMMLUNG + VORGEHEN + BERICHT   KEINE Beruehrung
W-04/1   ENTWURF     werkbank/W-04/** + REGISTER.md                  KEINE Beruehrung
W-08/1   ENTWURF     werkbank/W-08/** + REGISTER.md                  KEINE Beruehrung
W-11/1   ENTWURF     werkbank/W-11/** + REGISTER.md                  KEINE Beruehrung
W-13/1   ENTWURF     werkbank/W-13/** + REGISTER.md                  KEINE Beruehrung
W-01N    DIESES      W-01-Blatt (docs/auftraege/) + FAHRPLAN         disjunkt zu allen
-> Dieser Auftrag beruehrt KEINE Werkbank-Datei und KEIN REGISTER. Er ist der einzige
   der Gruppe, der nur in docs/auftraege/ und docs/ arbeitet.
§3 GEMESSEN 11.08. (korrigiert, siehe docs/MELDUNG-ERFUNDENE-SPERRE-A-12.md):
   grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md   -> 0
   A-12 traegt status: ENTWURF, NICHT IN_ARBEIT.
   -> §3 sperrt W-01N NICHT. Es darf in IN_ARBEIT, sobald DoR durch ist.
   Der Vorrang von A-12 (F-026 ist gelb, W-07/W-08 haengen fachlich daran) ist eine
   planerische EMPFEHLUNG, kein Verbot. Die Reihenfolge entscheidet der Plan-Pruefer.
```

```yaml
fehlerklasse: SPEC
verursacher: planner
prioritaet: P2
warteschlange: "nach A-12; vor oder parallel zu Runde 2 — er beruehrt keine gemeinsame Datei"
kern: "eine Zahl in einem Ist-Beleg ist richtig und datiert; eine Zahl in einem
       Soll-Kriterium ist eine Zeitbombe"
```
