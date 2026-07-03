# Sprint 1 — Rechnungsprozess / GoBD-Grundlage / Kanzlei-Übergabe

**Stand:** 2026-07-03 · **Übersichts-/Index-Datei — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — spätere UI strikt im ticket-Design. Planner-/Kanban-Änderungen unberührt.
**Zweck:** zentraler, verbindlicher Einstiegspunkt für die ausführende Claude-Code-Instanz zur Umsetzung der S1-Rechnungskette.

---

## 2. Kurzbeschreibung

Sprint 1 schließt die fachliche Rechnungskette Ende-zu-Ende: von der **sicheren Rechnungsnummer** über **Lösch-/Editiersperren**, **Storno/Korrektur**, **Zahlungs-/OP-Stand** und **unveränderliches Beleg-PDF** bis zur **Kanzlei-/OP-Übergabe** und der abschließenden **UI-/Cleanup-/Regressionsabsicherung**.

Der Sprint macht den Rechnungsprozess **prüfbar, nachvollziehbar und revisionstauglich** und bereitet saubere Belege + offene Posten für die **Kanzlei** auf (A1 = Option 1: die Kanzlei führt die Buchhaltung, ticket liefert Belege/OP/Buchungsvorschläge).

**Wichtig:** Sprint 1 ist **kein** DATEV-Vollprojekt, **kein** Mahnwesen und **keine** komplette Buchhaltung. Er schafft die belastbare **Grundlage** für einen prüfbaren Rechnungsprozess — nicht mehr, aber auch nicht weniger.

---

## 3. Ticket-Index

> Ticket-Titel wörtlich aus den jeweiligen Ticket-Dateien übernommen. Bei Abweichung gilt der Titel im Ticket.

| Ticket | Datei | Titel (wörtlich) | Prio | Zweck | Abhängigkeiten | Status |
|---|---|---|---|---|---|---|
| S1-01 | `ticket-S1-01-nummernkreis.md` | S1-01 — Transaktionaler, lückenarmer Rechnungsnummernkreis (Nummer erst bei `draft → sent`, eine Vergabestelle) | **P0** | eine einzige, transaktionale Nummernvergabe erst bei Ausstellung; keine Nummer für Drafts | — | **umgesetzt / verhalten verifiziert / formaler phpunit-Lauf offen** (2026-07-03; Migration angewandt, Transaktions-Härtung + UI-Platzhalter erledigt, Verhalten transaktional getestet, Live-Daten unverändert; **phpunit organisatorisch blockiert**: weder MySQL-Admin-Rechte — `ticket_user` hat nur `USAGE`+`ALL ON ticket`, kein CREATE — noch Docker verfügbar; isolierte Test-DB `ticket_test` muss extern bereitgestellt werden) |
| S1-02 | `ticket-S1-02-loeschsperre.md` | S1-02 — Löschsperre für Rechnungen & Belegdateien; kein physisches Löschen nach Ausstellung; Vorbereitung Storno-statt-Löschen | **P0** | ausgestellte/bezahlte Rechnungen + Belege unlöschbar; nur Drafts löschbar | S1-01 | dokumentiert / noch nicht umgesetzt |
| S1-03 | `ticket-S1-03-editiersperre.md` | S1-03 — Editiersperre ab Ausstellung: gesendete/nummerierte Rechnungen sind inhaltlich unveränderlich | **P1** | Kopf/Positionen/Beträge ausgestellter Rechnungen einfrieren; nur Zahlung/Status änderbar | S1-01, S1-02 | dokumentiert / noch nicht umgesetzt |
| S1-04 | `ticket-S1-04-storno-gutschrift.md` | S1-04 — Storno/Gutschrift für ausgestellte Rechnungen: Korrekturweg statt Löschen/Editieren | **P1** | verknüpfter negativer Korrekturbeleg statt Bearbeiten/Löschen | S1-01, S1-02, S1-03 | dokumentiert / noch nicht umgesetzt |
| S1-05/06 | `ticket-S1-05-06-teilzahlung-op.md` | S1-05 / S1-06 — Teilzahlungen (`invoice_payments` + PaymentService) & `payment_status`/Offene Posten | **P1** | Teilzahlungen, `paid/open_amount`, `payment_status`, Überfälligkeit, OP-Grundlage | S1-01, S1-03, (S1-04 fürs OP-Netting) | dokumentiert / noch nicht umgesetzt |
| S1-07 | `ticket-S1-07-beleg-pdf.md` | S1-07 — Unveränderlicher Rechnungs-/Storno-PDF-Beleg mit SHA-256-Hash | **P1** | finaler, unveränderlicher Beleg mit Hash bei Versand; kein Re-Render | S1-01, S1-03, S1-04 | dokumentiert / noch nicht umgesetzt |
| S1-08 | `ticket-S1-08-kanzlei-op-uebergabe-export.md` | S1-08 — OP-/Kanzlei-Übergabeliste + Export | **P2** | OP-/Kanzlei-Übersicht + CSV/ZIP-Export mit Übergabeprotokoll (read-only) | S1-05/06, S1-07 | dokumentiert / noch nicht umgesetzt |
| S1-09 | `ticket-S1-09-ui-konsolidierung-rechnungen-op-kanzlei.md` | S1-09 — UI-Konsolidierung Rechnungen / OP / Kanzlei | **P2** | einheitliche UI ohne neue Fachlogik; klare Trennung Entwurf/Beleg/OP | S1-01…S1-08 | dokumentiert / noch nicht umgesetzt |
| S1-10 | `ticket-S1-10-cleanup-legacy-rechnungssystem.md` | S1-10 — Cleanup / Legacy Rechnungssystem | **P3** | Legacy-Pfade inventarisieren, kontrolliert sperren/umleiten/härten | S1-01…S1-08 (fachliche Zieldefinition) | dokumentiert / noch nicht umgesetzt |
| S1-11 | `ticket-S1-11-regressionssuite-rechnungsprozess.md` | S1-11 — Regressionssuite Rechnungsprozess | **P3** | durchgängige Regression über S1-01…S1-10 | S1-01…S1-10 | dokumentiert / noch nicht umgesetzt |

---

## 4. Umsetzungsreihenfolge (verbindlich)

1. **S1-01** → 2. **S1-02** → 3. **S1-03** → 4. **S1-04** → 5. **S1-05/06** → 6. **S1-07** → 7. **S1-08** → 8. **S1-09** → 9. **S1-10** → 10. **S1-11**

- **S1-01 + S1-02** sind der **P0-Grundschutz** (sichere Nummer + Unlöschbarkeit).
- **S1-03 bis S1-07** schließen den **eigentlichen Rechnungsprozess** (Editiersperre, Storno, OP/Zahlung, finales PDF).
- **S1-08** macht die Rechnungskette für **Büro/Kanzlei nutzbar**.
- **S1-09** **konsolidiert die Oberfläche**.
- **S1-10** **räumt Legacy-Pfade auf**.
- **S1-11** **sichert alles regressionsfest ab**.

---

## 5. Abhängigkeitslogik

- **S1-02 braucht S1-01** — gelöschte finale Rechnungen würden Nummern-/Beleglogik gefährden.
- **S1-04 braucht finale Rechnungen + Editiersperren (S1-01/02/03)** — Korrektur darf nicht über Bearbeitung laufen.
- **S1-05/06 braucht finale Rechnungen** — OP/Zahlung wirken auf ausgestellte Rechnungen.
- **S1-07 braucht finale Rechnungsnummern** — die Nummer muss im PDF stehen (Nummer zuerst, dann PDF).
- **S1-08 braucht OP-Daten (S1-05/06) und finale PDFs (S1-07)**.
- **S1-09 braucht S1-01…S1-08** als fachliche Grundlage (nur Konsolidierung).
- **S1-10 erst nach fachlicher Zieldefinition** — damit keine benötigten Altdaten/Pfade blind entfernt werden (erst Inventur, dann Entscheidung).
- **S1-11 läuft am Ende** über die gesamte Kette (inkl. Legacy-Regression nach S1-10).

---

## 6. Durchgehende Guards (gelten über ALLE Tickets)

- Keine finale Rechnung ohne **kontrollierte Nummernvergabe**.
- **Keine Rechnungsnummer außerhalb** des autorisierten Finalisierungsflusses (S1-01).
- **Keine Löschung** finaler oder rechnungsrelevanter Daten (S1-02).
- **Keine Bearbeitung** finaler Rechnungsinhalte (S1-03).
- Änderung finaler Belege **nur über Storno/Korrektur** (S1-04).
- Finales PDF ist **unveränderlicher gespeicherter Originalbeleg** (S1-07).
- **Download finaler PDFs rendert nie neu** — immer gespeicherte Datei.
- **Zahlungs-/OP-Stand bleibt dynamisch** und verändert den finalen Beleg **nicht**.
- **Kanzlei-Export liest nur** vorhandene, geprüfte Daten (keine Mutation, kein Re-Render).
- **Legacy-Pfade dürfen die neue S1-Logik nicht umgehen.**
- **Tests dürfen produktive Guards nicht künstlich umgehen.**

---

## 7. Stop-Regeln für Claude Code

Claude Code darf **nicht einfach weitermachen**, sondern **muss stoppen und einen Befund mit Optionen formulieren**, wenn:

- eine Rechnungsnummer an **mehr als einer Stelle** erzeugt wird,
- ein **alter Pfad** weiterhin finalisieren kann,
- ein finales PDF beim **Download neu gerendert** wird,
- eine finale Rechnung **bearbeitet oder gelöscht** werden kann,
- eine **Migration bestehende Daten gefährdet**,
- **unklar** ist, ob ein Legacy-Pfad noch **produktiv genutzt** wird,
- Tests **nur durch Umgehung produktiver Guards** grün werden,
- ein Ticket eine **fachliche Entscheidung** verlangt, die **nicht dokumentiert** ist.

In diesen Fällen: **stoppen**, Befund + Optionen vorlegen, auf Freigabe warten.

---

## 8. Definition of Done für Sprint 1

Sprint 1 gilt erst als abgeschlossen, wenn:

- **S1-01 bis S1-08 fachlich umgesetzt** sind,
- **S1-09 die UI konsolidiert** hat,
- **S1-10 Legacy-Pfade** gesperrt/umgeleitet/gehärtet hat,
- **S1-11 die vollständige Regression** abdeckt,
- **alle kritischen Tests grün** sind,
- **keine finale Rechnung über Altpfade** verändert werden kann,
- **PDF-Download byte-identisch** aus gespeicherter Datei erfolgt,
- **OP-/Kanzlei-Export keine Belege verändert**,
- **Storno/Korrektur nachvollziehbar** ist,
- **diese Sprint-1-Indexdatei aktuell** bleibt (Status-Spalte gepflegt).

---

## 9. Scope-Grenze

**Nicht Teil von Sprint 1:**
- DATEV-EXTF-Detailbuchung
- vollständige Finanzbuchhaltung
- Mahnwesen
- automatische Bankanbindung
- Steuer-/SV-Berechnung
- komplette UI-Neuentwicklung
- neue Angebotslogik
- neue Projektlogik

**Sprint 1 ist die Rechnungs-/Beleg-/OP-/Kanzlei-Grundlage** — nicht die Buchhaltung selbst.

---

## 10. Abschlussnotiz

Diese Index-Datei ist der **verbindliche Einstiegspunkt** für die Umsetzung der S1-Rechnungskette. Die **einzelnen Tickets bleiben führend** für Detailentscheidungen; diese Übersicht regelt **Reihenfolge, Abhängigkeiten und Guards**.
