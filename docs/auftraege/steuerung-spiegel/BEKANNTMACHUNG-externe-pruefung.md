# BEKANNTMACHUNG — eine externe Prüfung ist eingesetzt

*An: Dirigent · Planner · Plan-Prüfer · Generator · Evaluator · Integrator · Release-Prüfer*
*Von: externe Prüfung · eingesetzt von Yama am 22.08.2026 · zur Verteilung durch den Dirigenten*

## Wer

Eine Instanz **außerhalb** eurer Rollenkette. Sie ist nicht die siebte Rolle im Pull-System,
hält keine `rollen/*.yaml`, kein ACK, keine Lease und keine Generation. Der Dirigent kann ihr
keinen Auftrag erteilen; ihr einziger Vorgesetzter ist Yama.

## Was sie tut

Alle 20 Minuten misst sie den Stand des ganzen Hauses, beurteilt die Veränderung, erfasst den
Fortschritt, benennt offene Aufgaben und was als Nächstes kommt, und legt dem Dirigenten
Vorschläge vor. Sie prüft **in letzter Instanz** — also nach Evaluator und Plan-Prüfer, nicht an
ihrer Stelle. **Die Prüfenden sind ausdrücklich mitgeprüft.**

## Was sie ausdrücklich NICHT tut

- **Sie baut nichts** und behebt nichts. Kein Produktcode, kein Skript, kein Testfix.
- **Sie entscheidet nichts.** Kein Votum, keine Freigabe, kein Zustand. Ihre Ausgabe ist
  Befund + Vorschlag; die Entscheidung bleibt beim Dirigenten und bei Yama.
- **Sie blockiert nichts.** Ein erteiltes Votum bleibt gültig, auch wenn sie daran etwas findet.
- **Sie schreibt nichts bei euch.** Nicht in `.ticket-steuerung/`, nicht in einen Rollen-Worktree,
  nicht in den Integrations-Checkout. Kein Commit, kein Push, kein Merge. Sie liest nur.

## Was das für euch heißt

**Nichts an eurer Arbeitsweise ändert sich.** Ihr müsst ihr nichts melden, nichts quittieren,
nichts beantworten. Sie holt sich alles lesend selbst. Sie erzeugt keine Wartezeit und keinen
zusätzlichen Takt in eurer Kette.

Wenn sie etwas findet, geht es als **Vorschlag** an den Dirigenten — nicht als Weisung an euch.
Ihr erfahrt davon über ihn, wie bisher.

## Wie sie misst, damit ihr sie prüfen könnt

Ihre Kriterien liegen offen: `PRUEFKRITERIEN.md` (P1 Belegtreue · P2 Wirkung statt Ort ·
P3 Rollentrennung · P4 Zustandswahrheit · P5 Vollständigkeit der Lieferung · P6 Regelwirksamkeit ·
P7 Rückweg · P8 Fortschritt statt Bewegung), dazu die sechs Linsen des `qualitaetsraster` für
Produktbefunde. Schweregrade und Befundschema sind eure — sie baut keinen zweiten Maßstab.

Jeder Befund trägt Mess-SHA, Befehl und Reihenfolge-Prüfung. **Eine Regel gilt nicht rückwirkend:**
wurde eine Lieferung vor der Regel geschrieben, ist sie kein Verstoß, und das steht am Befund.

Ihre eigenen Fehler stehen in jedem Bericht in einem eigenen Abschnitt. Widerspruch gegen einen
Befund ist erwünscht und wird geprüft, nicht abgewehrt — er darf gewinnen.

## Wo alles liegt

    /Users/yamanuri/.ticket-externe-pruefung/
      REGELWERK.md          was die Rolle ist, Unabhängigkeit, der 20-Minuten-Takt
      PRUEFKRITERIEN.md     P1–P8, Befundschema, Schweregrade
      AGENTEN-UND-SKILLS.md welche Werkzeuge sie einsetzt — ausschließlich lesende
      ZUSTAND.md            Außenansicht mit Mess-SHA (KEIN zweiter Statusträger)
      berichte/             ein Bericht je Takt
      befunde/              ein Fund je Datei
      vorschlaege/          Vorlagen an den Dirigenten
      messungen/            Rohmessungen als Beleg

`docs/STATUS.md` bleibt der einzige Statusträger des Hauses. Wo `ZUSTAND.md` und `STATUS.md`
auseinandergehen, ist genau das der Befund — nicht eine zweite Wahrheit.
