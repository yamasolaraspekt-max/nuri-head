# Auftragszähler — die Zehnergruppe nach ARBEITSREGELN §13

**Autorität:** [`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) §13. Diese Seite wird **fortgeschrieben**,
eine Zeile je Auftrag. Sie ist keine Statuswahrheit — der Stand steht in [`STATUS.md`](STATUS.md).

**Angelegt 04.08.2026 durch den Planner, weil §13 einen Zähler verlangt und keiner existierte.**
*Gemessen: `grep -rl 'Zehnergruppe'` findet ausschließlich die Regel selbst. Eine Pflichtprüfung,
die niemand auslösen kann, findet nicht statt — sie ist dann ein Vorsatz, keine Barriere.*

---

## Zählregel, wörtlich aus §13

> *„Ein Auftrag zählt, sobald der Planner ihn dem Plan-Prüfer **erstmals** vorlegt. Zur
> Zehnergruppe gehören damit auch zurückgewiesene, blockierte oder später abgebrochene Aufträge;
> schlechte Pläne dürfen nicht aus der Statistik verschwinden."*

**Der Zähler wird nie zurückgesetzt** — nicht wegen Sitzung, Monatswechsel, Branchwechsel oder
Rollenwechsel. **Vor Aufgabe elf steht die Prozess- und Skill-Prüfung.**

**Und sie wartet nicht auf die Zehn:** ein P1-Spezifikationsfehler, ein unerfüllbarer Auftrag, eine
übersehene Daten-/Sicherheitskante oder die zweite Wiederholung derselben Fehlerklasse löst sie
sofort aus. Der Vorfall bleibt trotzdem Teil seiner Gruppe.

---

## Gruppe 1 — laufend

| # | Auftrag | erstmals vorgelegt | 1. Plan-Prüfung | Runden bis BEREIT | Vermerk |
|---|---|---|---|---|---|
| **1** | **A-01** Dach aus Kontur | 04.08. 23:0x | **zurückgewiesen** (PLANUNGSBLOCKIERT) | **3** | Doppelführung mit Z-07 · SPEC: zwei Rechtecks-Begriffe · drei Prüfbefehle auf falschem Runner |

**Stand: 1 von 10.**

### Was in Gruppe 1 schon zu messen ist (§13-Kennzahlen)

```text
beim ersten Plan-Review BEREIT              0 von 1
zurueckgewiesen / blockiert                 1 von 1
Spezifikationsfehler, vom Plan-Pruefer      3  (Doppelfuehrung · Rechtecks-Begriff · Runner)
Spezifikationsfehler, vom Evaluator         - (noch kein Bau)
Kriterien bereits gruen vor dem Bau         1  (A-01-2, jetzt als Kontrolle gekennzeichnet)
unerfuellbare Kriterien                     1  (alter Z-07-P1: L-Dach, von der Domaene verweigert)
Nachbesserungsrunden im Plan                3
```

**Drei von drei Spezifikationsfehlern hat der Plan-Prüfer gefunden, keiner der Generator.**
*Das ist genau der Zweck der Rolle — und zugleich die ehrlichste Zahl über die Planungsqualität
dieser Gruppe. Sie gehört hierhin, nicht in eine Erzählung.*

---

## Was NICHT gezählt wird

- **Blätter aus dem aufgehobenen Regime** (Z-07, W-10, die S-/W-Serie). Sie sind nach §17 Historie
  und wurden dem Plan-Prüfer nie unter diesen Regeln vorgelegt. *Sie nachträglich einzurechnen
  würde die Gruppe mit Vorgängen füllen, die nach anderen Maßstäben entstanden sind.*
- **Nachträge und Korrekturen an einem laufenden Auftrag.** Ein Auftrag zählt **einmal**, beim
  ersten Vorlegen — sonst belohnt der Zähler das Aufteilen und bestraft das Zusammenfassen.
