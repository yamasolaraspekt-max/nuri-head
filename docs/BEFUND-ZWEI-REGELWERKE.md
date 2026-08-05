# BEFUND — es gibt ZWEI verbindliche Arbeitsregeln, und wir folgen der älteren

```yaml
befund: DECISION_BLOCKED
gefunden: 05.08.2026, 08:5x, vom Planner
anlass: "Beim Commit von Fassung 1.1 meldete das Tor die Datei als NEU — das verbindliche
         Regelwerk war auf diesem Zweig nie in Git. Die Nachfrage foerderte mehr zutage."
entscheidung_gehoert: Yama
gebaut_wird_nichts: "Ich merge nicht, pushe nicht, ueberschreibe nichts."
```

## Der Befund

```text
                        unser Zweig                     governance/arbeitsregeln-v1.1-20260804
Version                 1.0  (durch mich 1.1)           1.3
Zeilen                  440 -> 495                      592
in Git auf dem Zweig    erst seit 05.08. 08:44          seit 04.08. 09:09, DREI Commits
§5-Punkte               18 (15 + meine 3)               15
Statustraeger           "ein kompakter Auftragsdaten-   docs/AKTUELLER_AUFTRAG.yaml,
                        satz" - KEIN Dateiname          namentlich und verbindlich
Statuscommits           keine Regel                     genau EIN Zustandswechsel je Commit,
                                                        jede weitere Datei darin verboten
Blockzustaende          Liste                           + fortsetzung_zustand, Rueckkehrregeln
Push                    keine Regel                     Push = Transport, NICHT VEROEFFENTLICHT;
                                                        zwei getrennte Freigaben
Unterschied gesamt      229 abweichende Zeilen
```

**Der Zweigstand dazu:**

```text
fork/auto/hausplaner-integration   ENTHAELT den governance-Merge   (uns 10 Commits voraus)
auto/hausplaner-integration (wir)  enthaelt ihn NICHT              (dem fork 42 voraus)
main                               enthaelt ihn NICHT
```

**Beide Seiten sind gelaufen.** Das ist keine vergessene Aktualisierung, sondern eine Gabelung.

## Was daraus folgt — zwei Punkte, die sofort wirken

**1. `docs/STATUS.md` steht in keinem der beiden Regelwerke.**
Unser §16 nennt **keinen Dateinamen**, nur „einen kompakten Auftragsdatensatz". `STATUS.md` hat
ein Planner erfunden, um die Lücke zu füllen — das war unter 1.0 vertretbar. **1.3 nennt
stattdessen `docs/AKTUELLER_AUFTRAG.yaml`.** *Unsere gesamte Statusführung hängt an einer Datei,
die die neuere Fassung nicht kennt.*

**2. Meine Fassung 1.1 sitzt auf dem älteren Ast.**
Geprüft: **keine** meiner vier Regeln kommt in 1.3 vor (je 0 Treffer). *Sie sind additiv — bei
jeder Entscheidung geht nichts verloren, sie müssen nur ggf. umgesetzt werden.*

## Was ich NICHT getan habe

- **kein Merge, kein Push, kein Überschreiben.** §14 und die Rangfolge lassen das nicht zu, und
  hier ist es auch sachlich falsch: 42 Commits gegen 10, beide mit echter Arbeit.
- **keine Wahl zwischen den Fassungen.** Welche gilt, ist eine ausdrücklich Yama vorbehaltene
  Entscheidung — genau die Definition von `DECISION_BLOCKED`.

## Meine Empfehlung, falls sie hilft

**1.3 als Grundlage, meine vier Regeln darauf neu aufgesetzt (dann 1.4).**

*Begründung:* 1.3 ist neuer, liegt hinter einem PR-Merge und steht bereits auf dem Fern-Zweig.
Einer älteren Fassung zu folgen, während eine neuere gemergt ist, ist genau die zweite Wahrheit,
die §1 abgeschafft hat.

> **Der Preis ist real und gehört genannt:** 1.3 verlangt `AKTUELLER_AUFTRAG.yaml` und
> Statuscommits mit genau einem Zustandswechsel. **Das ist ein Umbau mitten in drei laufenden
> Aufträgen** (A-01/A-03 beim Evaluator, A-02 in Nachbesserung). *Er sollte nicht zwischen zwei
> Voten passieren.*

**Gegenposition, ehrlich dazugestellt:** Unsere 42 Commits sind unter 1.0 entstanden und in sich
stimmig. Wer 1.3 übernimmt, erklärt eine Nacht Arbeit nicht für falsch — aber er ändert die
Buchführung darüber rückwirkend.

## Offene Frage an Yama

**Welche Fassung gilt ab jetzt — und ab welchem Zeitpunkt?** Solange das offen ist, arbeite ich
weiter nach der Fassung im Baum (1.1) und kennzeichne jede Statusaussage als unter 1.1 entstanden.
