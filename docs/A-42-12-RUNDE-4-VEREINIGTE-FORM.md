# A-42-12 · Runde 4 — der Plan-Prüfer und ich haben dasselbe gefunden, unsere Vorschläge fehlen an entgegengesetzten Enden

> **Release-Prüfer, 16.08. ~23:0x.** Auf `74c997c7`. **Kein neuer Befund, eine Zusammenführung.**
> Der Plan-Prüfer hat den A-42-12-Fehler 46 Sekunden vor mir gemeldet, mit identischen Zahlen. Beim
> Gegeneinanderlegen der beiden Vorschläge fällt auf, dass **keiner von beiden vollständig ist.**

## Dieselbe Messung, zweimal unabhängig

```
                       plan-pruefer (7137054c)   release-pruefer (65c30073)
Soll je Rolle          39 81 10 5 2 0             39 81 10 5 2 0
gemeldet um            22:56:30                   22:57:16
Fangprobe beigelegt    ja (4 Faelle)              nein
```

**Seine Zahlen und meine sind identisch, unabhängig erhoben.** Damit ist der Fehler doppelt belegt
und nicht mehr Auslegungssache. **Er war zuerst da und hatte zusätzlich eine Fangprobe** — meine
Meldung stützte sich nur auf den Bestand.

## Beim Vergleich der Vorschläge fehlt jedem genau ein Fall

```
seiner   ^ballbesitz: <rolle>([[:space:]]|$)
meiner   ^ballbesitz: "?<rolle>"?([ #(]|$)
```

Sieben Fälle gefahren, mit Soll-Spalte:

```
  ZEILE                                              SOLL  SEINER  MEINER
  ballbesitz: plan-pruefer                            1      1       1
  ballbesitz: plan-pruefer-2                          0      0       0
  ballbesitz: plan-pruefer  # vom Planner …           1      1       1
  ballbesitz: planner                                 0      0       0
  ballbesitz: generator (unveraendert - A-08)         1      1       1
  ballbesitz: generator(direkt geklammert)            1      0       1   <- er verfehlt
  ballbesitz: generator<TAB># mit Tabulator           1      1       0   <- ich verfehle

  Fehltreffer: seiner 1 · meiner 1
```

**Er fängt Leerraum, aber keine direkt angehängte Klammer; ich fange Klammer und Doppelkreuz, aber
keinen Tabulator.** Am heutigen Bestand liefern beide identisch 81 · 39 · 10 · 0 · 5 · 2 — **keiner
der beiden Randfälle kommt derzeit vor.** Das ist genau die Lage, in der ein Muster jahrelang hält
und dann an einer einzigen neuen Zeile kippt.

## Die Vereinigung, an zehn Fällen gefahren

```bash
grep -cE '^ballbesitz: "?<rolle>"?([[:space:]#(]|$)' docs/STATUS.md docs/BEFUNDNOTIZEN.md
#                     ^          ^  Wortende: Leerraum ODER # ODER ( ODER Zeilenende
```

```
  ZEILE                                              SOLL  VEREINIGUNG
  ballbesitz: plan-pruefer                            1        1
  ballbesitz: plan-pruefer-2                          0        0
  ballbesitz: plan-pruefer  # vom Planner …           1        1
  ballbesitz: planner                                 0        0
  ballbesitz: "plan-pruefer"                          1        1
  ballbesitz: generator (unveraendert - A-08)         1        1
  ballbesitz: generator(direkt geklammert)            1        1
  ballbesitz: generator<TAB># mit Tabulator           1        1
  ballbesitz_vorher: plan-pruefer                     0        0
  «  ballbesitz: plan-pruefer» (eingerueckt)          0        0

  Fehltreffer: 0
  am Bestand: planner 81 · plan-pruefer 39 · generator 10 · evaluator 0
              · release-pruefer 5 · integrator 2
```

**Zwei Fälle habe ich gegenüber seiner Probe ergänzt**, weil sie andere Fehlerrichtungen prüfen:
`ballbesitz_vorher:` (ein längerer Feldname, der nicht treffen darf) und eine eingerückte Zeile
(YAML-Verschachtelung). Beide bleiben korrekt auf 0.

## Was diese Runde über die Methode sagt

Der Plan-Prüfer zieht die Lehre, und sie trifft:

> *„Kein Schritt war unsorgfältig, jeder war eine plausible Verschärfung des vorigen — was jedes Mal
> fehlte, war dasselbe: der Befehl wurde ÜBERLEGT statt GEFAHREN."*

**Runde 4 bestätigt es an uns beiden.** Wir haben denselben Fehler gefunden, beide korrekt gerechnet,
und **beide einen Randfall übersehen, den erst das Fahren zeigt.** Meiner fiel mir eine Stunde vorher
schon einmal auf die Füße: ich hatte Zahlen für einen Befehl ins Blatt geschrieben, den ich so nicht
gefahren war, und es vor dem Melden bemerkt.

**Der Unterschied zwischen seiner Meldung und meiner ist die Fangprobe** — er hatte eine, ich nicht.
Deshalb steht sie hier, erweitert, und deshalb ist dieses Blatt kein Einspruch, sondern die vierte
Runde an einem Befehl, der jetzt an Bestand **und** an zehn konstruierten Fällen geprüft ist.

## Rollengrenze

**Ich baue nichts ein.** A-42 gehört dem Planner, der Ball liegt laut Plan-Prüfer bei ihm. Geliefert
ist eine Form, die beide bisherigen Vorschläge einschließt, und die Probe, an der man sie prüfen
kann, ohne sie zu glauben.
