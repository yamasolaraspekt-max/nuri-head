# FORTSCHRITT — was erreicht ist

> **Fach 5 von 5.** Landkarte: [`docs/REGISTER.md`](../REGISTER.md)

---

## ⚠ Die wichtigste Zeile dieser Seite

**Dieses Fach trägt KEINEN Zustand.**

Der Statusträger ist **`docs/STATUS.md`**, namentlich benannt in ARBEITSREGELN §16. Er wird
**erzeugt, nicht von Hand bearbeitet**; alleiniger Schreiber ist der Integrator.

Wer wissen will, *ob* ein Auftrag `BEREIT`, `IN_ARBEIT` oder abgenommen ist, liest
[`docs/STATUS.md`](../STATUS.md) — und sonst nichts. Ein zweiter Zustandsort wäre genau die
zweite Wahrheit, die ARBEITSREGELN §1 abgeschafft hat.

Hier liegt, **was daraus geworden ist**: Berichte, Messreihen, Chronik. Abgeleitet, nicht führend.

---

## Was hineingehört

- **Wellen-/Rundenberichte** — was eine Messwelle ergeben hat
- **Messreihen mit Rohausgabe** — Zähler vorher/nachher, Suchbefehl samt Trefferliste
- **Abnahmebelege** — Urteil des Evaluators mit Artefakt
- **Chronik** — was wann warum entschieden wurde, wenn es den Fortschritt erklärt

## Was NICHT hineingehört

- Zustand eines Auftrags (→ `docs/STATUS.md`)
- offene Punkte (→ [`docs/backlog/`](../backlog/REGISTER.md))
- geltende Regeln (→ [`docs/regelwerk/`](../regelwerk/REGISTER.md))

---

## Belegpflicht

Ein Fortschrittsblatt ohne **Rohausgabe** ist Prosa. Zusammenfassende Sätze dürfen danebenstehen,
**nie stattdessen** — der Grund steht im Governance-Zyklus: „Testsuite selbst ausgeführt, grün" ist
von „Testsuite behauptet grün" nicht unterscheidbar, wenn nur der Satz ankommt.

Mindestens: **Befehl**, **Rohausgabe**, **Commit-SHA**, **Datum**.

---

## Lageberichte auf genau einem Mess-SHA (Berichtsregeln 22.08.)

| Blatt | Mess-SHA · Datenzeitpunkt | Ansicht |
|---|---|---|
| [`inventur-bilanz-2026-08-22.md`](inventur-bilanz-2026-08-22.md) | `eb304cf5` · 22.08.2026 08:40 | Bilanz der Inventur: 27 Produktbefunde (2 belegt behoben, 12 gebaut ohne Votum, 13 offen) + 16 Steuerungsbefunde (2 behoben, 2 Regel, 12 technisch offen), je Befund Auftrag/Korrigierender/Stand/Wirkung |
| [`lagebericht-2026-08-22-3b2e5334.md`](lagebericht-2026-08-22-3b2e5334.md) | `3b2e5334` · 22.08.2026 00:20:51 | Plattform/Rechte/Steuerung: A-42 ABGENOMMEN (11/11), A-37 begonnen (Planner-Lease), Rollen-Tabelle je Generation, Pull-Betrieb SOFT-AKTIV, eine nächste Handlung |

## Vorhandene Fortschrittsquellen (noch nicht migriert)

| Quelle | Rolle |
|---|---|
| [`docs/STATUS.md`](../STATUS.md) | **Statusträger** — bleibt, wo er ist, wird erzeugt |
| [`docs/auftraege/AUFTRAGSTAFEL.md`](../auftraege/AUFTRAGSTAFEL.md) | Auftragstafel (+ Archiv, + Historie 07/2026) |
| [`docs/AUFTRAGSZAEHLER.md`](../AUFTRAGSZAEHLER.md) | Zähler |
| [`docs/fortschritt.html`](../fortschritt.html) | erzeugte Fortschrittsansicht |
| [`docs/planner/fortschritt-2026-07-29.html`](../planner/fortschritt-2026-07-29.html) | Planner-Fortschritt, Stichtag |
| [`docs/rollenkette/uebergaben/`](../rollenkette/uebergaben/) | Übergaben zwischen den Rollen |
| [`docs/befunde/`](../befunde/) | 23 Roh-Testausgaben (`*-phpsuite.txt`, `waechter.log`) |
| [`docs/handoff-status.md`](../handoff-status.md) | **Archiv seit 31.07.2026** — 1,7 MB append-only, keine Statusquelle |
| [`docs/STAND.md`](../STAND.md) | Einseiter, wird überschrieben |
