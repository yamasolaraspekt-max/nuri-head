# S1-11 — Regressionssuite Rechnungsprozess

**Stand:** 2026-07-03 · **Detail-Ticket — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P3 · **Sprint:** 1 (Abschluss) · **Grundlage:** S1-01…S1-10.

---

## 0. Ziel & Kernregel
Eine zusammenhängende **Regressionssuite**, die den gesamten neuen Rechnungsprozess **S1-01…S1-08** absichert und zusätzlich die Cleanup-Risiken aus **S1-10** kontrolliert.
**S1-11 ist kein Feature-Ticket**, sondern die Absicherung der Sprint-1-Rechnungskette. Die Tests müssen beweisen: Nummern bleiben lückenlos · finale Belege unveränderlich · Löschsperren greifen · Zahlungen verändern keine Belege · Storno/Korrektur korrekt · OP-/Kanzlei-Übergabe nutzt gespeicherte Daten · Legacy-Pfade umgehen keine Regeln.

## 1. Scope
**Enthalten:** Testmatrix S1-01…S1-08 · Regression gegen Legacy-Pfade (S1-10) · kritische E2E-Szenarien · negative Tests/Missbrauchspfade · Berechtigungstests · Idempotenztests · Hash-/PDF-Stabilitätstests · Exporttests.
**Nicht enthalten:** keine neue Fachlogik · keine neuen UI-Features · keine DATEV-Detailbuchung · kein Mahnwesen · keine produktive Datenmigration.

## 2. Teststruktur nach Bereichen

### A. Nummernvergabe (S1-01)
1. Draft hat keine finale Rechnungsnummer. 2. Finalisierung erzeugt genau eine Nummer. 3. wiederholte Finalisierung erzeugt keine zweite Nummer. 4. Fehler nach Nummernvergabe → kein Nummernrollback. 5. parallele Finalisierung → keine doppelte Nummer. 6. Nummern entstehen nicht über Legacy-Pfade.

### B. Löschsperre (S1-02)
1. finale Rechnung nicht löschbar. 2. finale Position nicht veränderbar. 3. finaler Beleg nicht löschbar. 4. `immutable` `invoice_file` geschützt. 5. Kunde/Projekt mit finaler Rechnung geschützt bzw. nur regelkonform archivierbar. 6. Legacy-Löschpfad blockiert.

### C. Zahlungsstatus / OP (S1-03/05/06)
1. unbezahlte Rechnung = offen. 2. Teilzahlung = teilbezahlt. 3. Vollzahlung = bezahlt. 4. Überzahlung → Plausibilitätswarnung/Block. 5. negativer offener Betrag nicht stillschweigend akzeptiert. 6. Zahlungsstand verändert finalen Beleg nicht. 7. OP-Liste berechnet offenen Betrag korrekt. 8. überfällige Rechnung korrekt erkannt.

### D. Storno / Korrektur (S1-04)
1. finale Rechnung nicht direkt änderbar. 2. Storno erzeugt nachvollziehbaren Gegenbeleg. 3. Korrektur erzeugt neue Rechnung mit eigenem Bezug. 4. Storno/Korrektur verändert Original nicht. 5. Historie zeigt Storno-/Korrekturbezug. 6. OP-Status berücksichtigt Storno/Korrektur fachlich korrekt.

### E. Finales PDF (S1-07)
1. finales PDF nach Finalisierung erzeugt. 2. PDF enthält finale Nummer. 3. `final_pdf_file_id` gesetzt. 4. Download nutzt gespeicherte Datei. 5. wiederholter Download rendert nicht neu. 6. wiederholter Generate-Aufruf erzeugt kein zweites PDF. 7. `sha256` stabil. 8. `pdf_failed_at` bei Fehler gesetzt. 9. Status bleibt `sent` trotz PDF-Fehler. 10. Retry erzeugt bei fehlendem PDF genau einen Beleg.

### F. Kanzlei-/OP-Übergabe (S1-08)
1. bereite Rechnung ist exportfähig. 2. finale Rechnung ohne PDF nicht exportfähig. 3. Rechnung mit `pdf_failed_at` nicht exportfähig. 4. nicht-finale Rechnung nicht exportiert. 5. Export erzeugt Snapshot. 6. erneuter Export → neuer Batch, überschreibt keinen alten. 7. Export verändert keine Rechnung. 8. Export rendert kein PDF neu. 9. ZIP enthält nur vorhandene finale PDFs. 10. Hash aus `invoice_files` gelesen. 11. OP-Daten im Export = Snapshot-Zeitpunkt. 12. späterer Zahlungsstand verändert alten Batch nicht.

### G. UI / Berechtigung (S1-09)
1. Buchhaltung sieht Kanzlei-Export. 2. Vertrieb nicht. 3. Montage keine unzulässigen Finanzaktionen. 4. Geschäftsführung sieht alles Relevante. 5. finale Rechnung nur lesend. 6. gesperrte Aktionen zeigen Begründung. 7. PDF-Fehler sichtbar. 8. Storno-/Korrekturbezug sichtbar.

### H. Cleanup / Legacy (S1-10)
1. alter Nummernpfad deaktiviert/delegiert korrekt. 2. `deal_invoices`-Direktroute erzeugt keine finale Rechnung. 3. alte Statusroute setzt `sent` nicht direkt. 4. alter PDF-Pfad rendert keinen finalen Beleg neu. 5. alte Löschroute kann finale Rechnung nicht löschen. 6. Altdaten bleiben lesbar. 7. Redirects funktionieren. 8. Historie protokolliert relevante Vorgänge.

## 3. End-to-End-Szenarien
1. **Standardrechnung:** Draft → Finalisierung → Nummer → PDF → OP offen → Zahlung → bezahlt → Kanzlei-Export.
2. **PDF-Fehler:** Draft → Finalisierung → Nummer → PDF-Fehler → `sent` bleibt → Retry → PDF vorhanden → exportfähig.
3. **Teilzahlung:** finale Rechnung → Teilzahlung → OP teilbezahlt → Export mit offenem Betrag → spätere Zahlung verändert alten Export nicht.
4. **Storno:** finale Rechnung → Storno → Original unverändert → Storno-Beleg vorhanden → OP/Kanzlei zeigt Bezug.
5. **Korrektur:** finale Rechnung → Korrekturfluss → neue Rechnung mit eigener Nummer/PDF → Bezug sichtbar.
6. **Legacy-Angriff:** Versuch über alte Route Nummer/Status/PDF/Löschen zu umgehen → blockiert oder umgeleitet.

## 4. Idempotenztests
Finalisierung mehrfach · PDF-Erzeugung mehrfach · Download mehrfach · Retry mehrfach · Export mehrfach · Kanzlei-Prüfung mehrfach · Zahlungserfassung mit gleicher Referenz (falls vorhanden). Jeweils: kein doppelter Effekt, keine Mutation, kein Zweitbeleg/keine Zweitnummer.

## 5. Negative Tests / Missbrauchspfade
Rechnung ohne Nummer exportieren · Rechnung ohne PDF exportieren · Rechnung mit `pdf_failed_at` exportieren · Zahlung > Brutto · `final_pdf_file_id` zeigt ins Leere · `sha256` fehlt · `immutable=false` bei `final_pdf` · User ohne Recht löst Export aus · Legacy-Route setzt `sent` direkt. Alle → definierter Fehler/Blockade/Warnung, keine stille Mutation.

## 6. Testdaten (Fixtures/Factories)
Draft-Rechnung · finale offene Rechnung · finale bezahlte Rechnung · finale teilbezahlte Rechnung · überfällige Rechnung · Rechnung mit PDF · Rechnung mit PDF-Fehler · stornierte Rechnung · Korrekturrechnung · Rechnung mit fehlendem Hash · Legacy-Altdatensatz · Nutzerrollen: Geschäftsführung, Buchhaltung, Vertrieb, Montage, Admin.
*(Factories müssen die produktiven Guards **respektieren**, nicht künstlich umgehen — sonst falsche Sicherheit.)*

## 7. Risiken & Guards
| Risiko | Guard |
|---|---|
| Tests prüfen nur Happy Path | Negativ-/Missbrauchspfade (§5) verpflichtend |
| Legacy-Pfade ungetestet | Bereich H + E2E-6 explizit negativ testen |
| PDF-Stabilität nicht byte-genau | Download gegen gespeicherte Datei + `sha256`-Vergleich |
| dynamischer OP-Stand mit statischem Export verwechselt | Snapshot-Test (F-11/F-12) |
| parallele Finalisierung ungetestet | Parallelitätstest A-5 |
| Berechtigungen vergessen | Bereich G verpflichtend |
| Testdaten umgehen Guards künstlich | Factories respektieren Guards; Reconcile-Checks |

**Guards (verbindlich):** kritische Tests zuerst · Nummernlogik mit Parallelitätsfall · PDF-Download gegen gespeicherte Datei · Export ohne Mutation · Legacy-Pfade explizit negativ · Rollenrechte testen · alte + neue Daten gemeinsam · **Regression nach S1-10 erneut vollständig laufen lassen**.

## 8. Definition of Done
- Vollständige Testmatrix S1-01…S1-08 + Zusatztests S1-09/S1-10.
- End-to-End-Szenarien definiert.
- Idempotenz-, Negativ-, Berechtigungs- und Legacy-Tests enthalten.
- Schützt den Rechnungsprozess gegen Rückfälle.
- Als eigenständiges Umsetzungsticket verständlich.

## 9. Nicht im Scope
Neue Fachlogik/UI-Features · DATEV-Detailbuchung · Mahnwesen · produktive Datenmigration · Performance-/Lasttests (separat) · playground-Optik.

---
**Ein-Satz-Fazit:** S1-11 definiert die durchgängige Regressionssuite über S1-01…S1-10 — Nummern-, Löschsperr-, OP-, Storno-, PDF-, Export-, UI-/Rechte- und Legacy-Tests plus E2E-, Idempotenz- und Negativpfade —, damit die gesamte Sprint-1-Rechnungskette gegen Rückfälle und Umgehung dauerhaft abgesichert ist.
