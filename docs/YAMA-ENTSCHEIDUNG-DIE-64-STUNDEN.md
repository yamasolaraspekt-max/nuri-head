# Yamas Posten aus §112 entschieden: die Statuswahrheit wird nachgeführt, nicht abgeschnitten

> **Release-Prüfer in Yamas Namen, 19.08. ~15:5x.** Auf `8ad16cfa`. Grundlage seiner stehenden
> Anweisungen: *„du sollst die Aufgaben, welche an mich gerichtet sind, erledigen"* (12.08.) ·
> *„du übernimmt alle fragen und aufgaben in namen von Yama"* (13.08.).

## Der Posten im Wortlaut

Der Plan-Prüfer legt ihn in `8ad16cfa` (§112) ausdrücklich bei Yama ab:

> *„**Bei Yama** — die 64 Stunden. Ob die Statuswahrheit nachgeführt wird oder ob der Stand vom
> 16.08. 20:39 als gültiger Schnitt erklärt wird, ist keine Messfrage."*

**Zwei Wege, einer zu wählen.** Er hat recht, dass es keine Messfrage ist — aber der Umfang ist
eine, und ohne ihn ist keine der beiden Antworten arbeitsfähig. Deshalb erst die Messung.

## Frisch gemessen, nicht aus Notizen

```
git log -1 --format='%h %ci' -- docs/STATUS.md   -> 0f969d5e  16.08. 20:39:34
Systemuhr bei der Messung                        -> 19.08. 15:44:38
Abstand                                          -> 4025 min  = 67 h 05

git rev-list --count 0f969d5e..HEAD              -> 314
  davon Betreff '^integrator:'                   ->  90
  davon mit docs/STATUS.md                       ->   0
git log --all -1 -- docs/STATUS.md               -> 0f969d5e   (keine juengere Schreibung, auch nicht in rolle/*)

git rev-list --count --since='2026-08-17 03:00'  ->   1        (der Commit, der diesen Posten stellt)
BEREIT 0 · IN_ARBEIT 0
```

**Die 64 Stunden des Plan-Prüfers halten** (er maß 3874 min um 13:1x, ich 4025 um 15:44 — dieselbe
Uhr, zwei Ablesezeitpunkte). **Und seine Zusatzaussage hält auch:** von den 67 Stunden trugen die
letzten 58 keinen einzigen Commit. Es sind nicht 67 Stunden Arbeit ohne Statusführung, sondern
6 Stunden dichteste Arbeit ohne Statusführung und danach 58 Stunden Stillstand.

## Wen die Lücke betrifft — der Umfang, der in der Frage fehlte

Ein Verfahren, benannt: gezählt werden **yaml-Blöcke mit einem `zustand`-Feld**.

```
Datensaetze (yaml-Bloecke mit zustand)   89
davon ruhend                             78   BETRIEBSBESTAETIGT 75 · ERLEDIGT 1 · ZURUECKGEZOGEN 1 · VORLAGE 1
davon lebend                             11
```

**Ein eigener Griff, vor der Abgabe gefangen:** mein erster Entwurf schrieb *„87 Datensätze, davon
78 ruhend, 11 lebend"* — **drei Zahlen aus zwei Verfahren in einem Satz**, dieselbe Klasse wie das
„4 von 74" aus der Zaunmessung. Drei Verfahren geben drei Grundmengen, und alle drei sind für sich
richtig:

```
V1  ^zustand: im Volltext                       90   zaehlt 1 Zeile ausserhalb jedes geschlossenen Blocks mit
V2  yaml-Bloecke mit zustand-Feld               89   die Datensaetze  <- hier verwendet
V3  drift.py "Datensaetze mit zustand"          87   nur die, die einer Tafelzeile zugeordnet sind
```

Die Zeile, die V1 mehr sieht als V2, ist einer der beiden Öffner-ohne-Schließer (Grundlinie D = 2);
die zwei, die V3 weniger sieht, tragen keine Tafelzeile. **Die Auswahl ist V2, weil die Frage nach
Datensätzen gestellt ist und nicht nach Tafelzeilen.**

| Zustand | Anzahl | Kennungen |
|---|---|---|
| `ENTWURF` | 4 | A-38, A-39, A-40, A-42 |
| `CODE_FERTIG` | 1 | A-37 |
| `DECISION_BLOCKED` | 1 | W-21L |
| `BEFUND` | 3 | P-03, P-04, P-04 |
| `ABGENOMMEN` | 2 | A-05, A-12 |

**Die zwei `ABGENOMMEN` sind kein Release-Rückstand** — beide sind Messaufträge und tragen ihren
Grund im eigenen Datensatz: A-05 ist im Titel *„MESSAUFTRAG (kein Produktivbau)"*, A-12 trägt die
ausdrückliche Korrektur *„ein Messauftrag hat keinen Release-Kandidaten"*, beide sind am 12.08. vom
Planner geschlossen und ihr Ergebnis verwertet. **Ich habe das geöffnet, bevor ich es einordne** —
sonst stünde hier ein §10, der keiner ist.

**Es bleiben sechs Aufträge, an denen die Lücke tatsächlich hängt** — dieselben sechs, die der
Plan-Prüfer in seiner Alterungstabelle führt.

## Was nachzuführen wäre — die Liste, nicht das Gefühl

**Vier DoR-Ergebnisse sind geliefert und committet; die Felder kennen sie nicht.** Gezählt über
Blöcke mit `zustand`-Feld, nicht über `auftrag:`-Zeilen (das nackte Muster trifft 0, weil die Datei
`auftrag: "A-38"` schreibt — die Form gelesen, nicht die 0 geglaubt):

```
A-38  ENTWURF  plan-pruefer  dor_beleg "BEREIT — 2. Runde 15.08., …"    geliefert 01:26  ERTEILT
A-39  ENTWURF  plan-pruefer  dor_beleg "steht aus"                      geliefert 01:30  ERTEILT
A-40  ENTWURF  plan-pruefer  dor_beleg "steht aus"                      geliefert        NICHT ERTEILT, 2 Restpunkte
A-42  ENTWURF  plan-pruefer  dor_beleg "steht aus"                      geliefert        NICHT ERTEILT, 1 Restpunkt
```

**Bei A-37 steht derselbe Sachverhalt an drei Orten mit drei Werten** — selbst nachgemessen, jede
Stelle einzeln geöffnet:

```
Blatt      A-37-…md:10   dor_beleg: "NICHT ERTEILT — 3. Runde, siehe docs/STATUS.md."
Datensatz  STATUS.md     dor_beleg: "BEREIT — 2. Runde 15.08., siehe dor_votum_runde_2"
Tafelzeile STATUS.md:88  "DoR steht aus."
```

**Das Blatt verweist für seinen Wert auf `docs/STATUS.md` — und dort stehen zwei andere.** Der
Verweis zeigt auf seinen eigenen Widerspruch.

Dieselbe Tafelzeile, zwei weitere Aussagen, beide selbst nachgezählt:

```
"Elf Kriterien"                     Blatt: 21   (zwei Verfahren, beide 21)
"A-37-2 und A-37-7 sind die         Blatt:  3   (A-37-2 Z.284 · Z.323 · A-37-14 Z.337)
 Positivfaelle"
Ball-Drift                          Tafel "Plan-Pruefer"  <->  Datensatz "integrator"
```

## Die Entscheidung

**In Yamas Namen: die Statuswahrheit wird nachgeführt. Der Stand vom 16.08. 20:39 wird NICHT als
gültiger Schnitt erklärt.**

Drei Gründe, alle an der Messung oben:

**(1) Ein erklärter Schnitt vernichtet gelieferte Arbeit.** Vier DoR-Ergebnisse liegen vor. Sie zu
Nicht-Ergebnissen zu erklären, weil das Feld sie nicht trägt, kehrt die Beweislast um: die
Statuswahrheit soll den Stand abbilden, nicht ihn bestimmen.

**(2) Ein erklärter Schnitt macht einen Widerspruch dauerhaft gültig.** Bei A-37 stehen drei Werte
an drei Orten. Ein Schnitt friert nicht einen Stand ein, sondern drei einander widersprechende —
und einer davon verweist ausdrücklich auf einen der anderen.

**(3) Nachführen verwirft nichts und ist rückholbar.** Ein Schnitt ist es nicht: was einmal als
„gültiger Stand" erklärt ist, muss später gegen die Erklärung zurückgenommen werden.

**Das ist keine Fachentscheidung, keine Geld-, Rechts- oder Datenfrage und berührt weder Produktion
noch Produktivdaten.** Sie liegt innerhalb dessen, was ich in Yamas Namen entscheide. Was ich in
seinem Namen **nicht** entscheide und ausdrücklich offen lasse: **ob die vier DoR-Ergebnisse
inhaltlich richtig sind** — das ist die Sache des Plan-Prüfers und war nie Gegenstand dieses
Postens.

## Was ich nicht tue

**Ich trage nichts ein.** `docs/STATUS.md` gehört dem Integrator; `rollen-tor.sh` bindet exakt auf
diesen Pfad, und ein Zustandswechsel ist kein Transport. **Die Entscheidung ist eine Antwort auf
eine Frage, keine Erlaubnis für mich selbst.**

## Ball

**Beim Integrator** — vier `dor_beleg`-Felder, die A-37-Tafelzeile (drei Aussagen), die A-37-Ball-Drift.
Danach steht dem `ENTWURF → BEREIT` der zwei erteilten nichts mehr im Weg.

**Beim Plan-Prüfer** — der Rest von A-40 (2 Restpunkte) und A-42 (1 Restpunkt), unverändert seine.

**Bei Yama bleibt** — nur, was er ausdrücklich selbst entscheiden wollte: W-15, A-16/A-18, die zwei
Fachfragen aus W-21L und die Push-Regel. **Die 64 Stunden nicht mehr.**
