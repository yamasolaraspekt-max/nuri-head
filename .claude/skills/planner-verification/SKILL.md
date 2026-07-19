---
name: planner-verification
description: Abschluss-Review eines Planner-Slices (Evaluator-Rolle). Prüft zusätzlich zur Fachabnahme, ob Ticket-Reuse eingehalten wurde: Reuse-Matrix vorhanden? Doppelimplementierung vermieden? Zweites Designsystem? Ticket-Tests grün? Ticket-Komponenten unzulässig aufgeweicht? Extraktion mit Feature vermischt? Bestehende Ticket-Funktionen unverändert nutzbar?
---

# planner-verification

## Ziel
Unabhängige Abnahme: fachliche Korrektheit UND Reuse-/Konsistenz-Disziplin.

## Rolle
Evaluator (getrennt vom Generator). Führt Tests selbst aus, macht Gegen-Beweis, urteilt grün/rot
je Kriterium mit Beleg.

## Reuse-Gate (zwingend)
- Liegt eine Reuse-Matrix für den Slice vor?
- Wurde passender Ticket-Code gesucht und nicht übersehen?
- Wurde unnötig neu entwickelt / ein zweites Designsystem gebaut?
- Wird vorhandene Workflow-/Aufgaben-/Kommentar-/Dokumentenlogik dupliziert?
- Ist eine vorgeschlagene Extraktion sinnvoll und nicht mit dem Feature vermischt?
- Wurden Ticket-Komponenten fachlich unzulässig aufgeweicht?
- Sind bestehende Ticket-Funktionen weiter unverändert nutzbar? Ticket-Regression grün?

## Fach-Gate
- tsc/Tests/Build grün; Persistenz/Revision/409; Rechte/Org; Szene-Round-Trip.
- Browser-Sichtprobe (echtes Rendering) wo relevant, mit Beleg (Screenshot/DB-Ausschnitt).

## Ausgabe
Urteil grün/rot je Kriterium + konkreter Reproduktionsfall bei Rot + betroffene Tests + Risiken.
Ohne erfüllte Reuse-Matrix + Fach-Gate kein Grün.

## Pflicht-Stopp
Nur Prüfung; verändert keinen Produktivcode. Kein Commit. Kein Push.
