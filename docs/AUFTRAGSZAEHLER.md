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

| **2** | **A-02** Lock: Halter statt Ruhe | 04.08. 23:5x | **zurückgewiesen** (2 Restpunkte) | **2** | A-02-1 an der Basis grün (gemessen 23/0, jetzt must_preserve) · ENV_BLOCKED-Form fehlte, Exitcode-Staffel vor der Wahl gegengemessen (3 frei) |

| **3** | **A-03** Browser-Bühne auf Testdatenbank | 05.08. 00:2x | **zurückgewiesen** (2 Restpunkte) | **2** | P2 scharf: BAUEN gerechtfertigt (Papier-Regel hatte den Vorfall nicht verhindert) · Verankerung in ANKER-BROWSER fehlte (Papier-Falle eine Ebene höher) · Namensliste auf exakt ticket_testing |

| **4** | **A-04** Bühnen-Wächter (Zustand statt Aufrufform) | 05.08. 08:58 | *läuft* | – | aus B1/SPEC/P1 der A-03-Abnahme · mein Spezifikationsfehler |
| **5** | **A-05** MESSAUFTRAG L-Kontur → L-Dach | 05.08. 09:3x | *läuft* | – | aus Yamas Frage nach playground/PV-Dachplaner · erstes Blatt MIT Wiederverwendungsprüfung · kein Produktivbau |

| **6** | **A-06** Probedaten in der Arbeits-DB | 05.08. 09:4x | *läuft* | – | Selbstmeldung des Evaluators · `DECISION_BLOCKED`, Freigabe Yama · meine Annahme „echte Kundendaten" von der Messung widerlegt |

| **7** | **A-07** Index-Divergenz des Tors | 05.08. 14:39 | **SPEC_BLOCKED** (A-07-4) | *läuft* | mein Kernbefund zeigte auf den falschen Index · vom Evaluator widerlegt, neu geschnitten · Weg-Bedingung vom Generator als wirkungslos gemessen |

| **8** | **A-08** Halter nach Kommando statt Offenheit | 07.08. 08:4x | **BEREIT** (a3d373b2) | **1** | P0 aus dem eigenen Tor-Ausschluss · Klasse SPEC, Verursacher Planner · **ERSTES BEREIT beim ersten Review der Gruppe** — alle Rot-Lagen selbst gemessen; Doppelführung (zwei A-08-Dateien) vom Planner selbst angezeigt, Lesart im Votum zusammengeführt; Konfliktprüfung gegen A-07 (gleiche Dateien) von mir ergänzt: A-08 baut zuerst |

**Stand: 8 von 10.**

> ### §13-Prozessprüfung ausgelöst bei 3, nicht bei 10
>
> **Sofort-Auslöser „übersehene Daten-/Sicherheitskante" ist eingetreten** — in A-01. Der Auftrag
> nannte `ticket_testing` dreimal und traf trotzdem daneben: er sagte, **wohin geseedet wird**,
> nicht, **wogegen der Server läuft**. Bericht: [PROZESSPRUEFUNG-01.md](PROZESSPRUEFUNG-01.md).
> **Der Vorgang bleibt Teil der Gruppe**, der Zähler wird nicht zurückgesetzt.

### Was in Gruppe 1 schon zu messen ist (§13-Kennzahlen) — nachgeführt 05.08. (Plan-Prüfer)

```text
beim ersten Plan-Review BEREIT              0 von 3
zurueckgewiesen / blockiert                 3 von 3   (A-01 PLANUNGSBLOCKIERT · A-02 2 Rest · A-03 2 Rest)
Runden bis BEREIT                           3 · 2 · 2
Spezifikationsfehler, vom Plan-Pruefer      6  (A-01: Doppelfuehrung · Rechtecks-Begriff · Runner ·
                                                A-02: gruenes P1 + fehlende ENV_BLOCKED-Form ·
                                                A-03: fehlende Verankerung)
Spezifikationsfehler, vom Evaluator         - (Abnahmen laufen)
Kriterien bereits gruen vor dem Bau         2  (A-01-2 · A-02-1, beide jetzt must_preserve-Kontrollen)
unerfuellbare Kriterien                     1  (alter Z-07-P1: L-Dach, von der Domaene verweigert)
Nachbesserungsrunden im Plan                A-01: 2 · A-02: 1 · A-03: 1
```

**Das Muster nach drei Aufträgen, offen benannt:** Kein Blatt war beim ersten Review BEREIT —
aber jede Rückweisung wurde in EINER Runde geschlossen, und zwei der sechs Planner-Fehler hat der
Planner inzwischen selbst gefunden, bevor ich sie fand. Die Kette wird schneller, nicht lascher.
*(Fortschreibung: Evaluator-Spalten nach den Voten zu 6bc38d7d und 586ec68a.)*

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
