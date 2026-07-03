# S1-10 — Cleanup / Legacy Rechnungssystem

**Stand:** 2026-07-03 · **Detail-Ticket — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P3 · **Sprint:** 1 (Abschluss) · **Grundlage:** S1-01…S1-08.

---

## 0. Ziel & Leitentscheidung
Alte, doppelte oder gefährliche Rechnungswege identifizieren und **kontrolliert** stilllegen/härten, damit die neue S1-Logik nicht durch Legacy-Pfade umgangen wird.
**Leitentscheidung: Die S1-Logik ist führend. Legacy-Pfade dürfen S1-01…S1-08 nicht umgehen.**

**Kernregel:** S1-10 ist ein **Cleanup-/Härtungsticket** — **nicht blind löschen**. Vor jeder Stilllegung prüfen: Wird der Pfad noch genutzt? Welche Routen/Controller/Views/Tests/Jobs hängen davon ab? Gibt es Altdaten, die lesbar bleiben müssen? Gibt es Redirects/Kompatibilitätsschichten?

## 1. Scope
**Enthalten:** Analyse der Legacy-Pfade · Entscheidung je Pfad (behalten/umleiten/sperren/entfernen) · Sicherheitsregeln gegen Umgehung · Rückwärtskompatibilität für Altdaten · absichernde Tests · Dokumentation der entfernten/gesperrten Pfade.
**Nicht enthalten:** keine neue Rechnungs-/Nummern-/PDF-/Zahlungslogik · keine DATEV-Erweiterung · keine große UI-Neuentwicklung.

## 2. Vorgehensweise (verbindlich)
1. **Inventarisieren** (alle Kandidatenpfade auflisten). 2. **Risiko bewerten**. 3. **Entscheidung je Pfad dokumentieren**. 4. **Erst Tests schreiben/definieren**. 5. **Dann sperren/umleiten/entfernen**. 6. **Regression gegen S1-01…S1-08**.

## 3. Prüfbereiche & Entscheidungen

### 3.1 Doppelte `makeInvoiceNo`-Logik
- **Ist:** `Invoice::makeInvoiceNo()` (Model) + zweite Implementierung in `InvoiceCanvasController::makeInvoiceNo()`.
- **Führend nach S1-01:** ausschließlich `InvoiceNumberService` (Vergabe bei `draft → sent`).
- **Gefährlich:** jede zweite Stelle, die separat Nummern erzeugen kann.
- **Entscheidung:** zweite Logik **entfernen** bzw. auf den Service **delegieren**; Model-Methode deprecaten/umleiten.
- **Guard:** **Keine Rechnungsnummer darf außerhalb des S1-01-finalen Statuswechsels entstehen.**
- **Beweis-Tests:** nur ein autorisierter Nummernweg; alter Pfad erzeugt keine Nummer mehr.

### 3.2 `deal_invoices`-Direktroute
- **Ist:** Direktroute (`/deal/invoices`, ≈`routes/web.php` Z. 4332) auf die tote Alt-Schiene `deal_invoices` (Stub-Controller).
- **Prüfen:** umgeht sie Status-/Nummern-/Lösch-/PDF-Regeln? (ja, sie kennt die S1-Guards nicht).
- **Empfehlung:** **direkte Erstellung/Finalisierung über Legacy-Route sperren**. Falls nötig: Redirect auf den neuen Rechnungsarbeitsraum; Legacy-Anzeige **nur lesend**; klare Fehlermeldung bei alter Schreibaktion. **Altdaten** in `deal_invoices` bleiben lesbar, werden **nicht** gelöscht.

### 3.3 `invoice_histories` härten
- **Zu historisierende Ereignisse (mind.):** Statuswechsel · Nummernvergabe · Finalisierung · PDF-Erzeugung erfolgreich · PDF-Fehler · PDF-Retry · Zahlungserfassung · Storno · Korrektur · Kanzlei-Prüfung · Kanzlei-Export.
- **Prüfen:** welche Ereignisse fehlen heute; welche Felder dürfen nachträglich **nicht** manipulierbar sein.
- **Regel:** Historie **dokumentiert Vorgänge** und darf **keine Beleginhalte nachträglich ändern**. Nachvollziehbarkeit sicherstellen (append-orientiert; ggf. FK auf `invoices`).

### 3.4 Alte Statuspfade
- **Prüfen:** Controller-Methoden, die direkt `status = sent` setzen · Bulk-Aktionen · Admin-Hilfsrouten · Jobs/Commands · Tests/Factories/Seeder, die neue Guards umgehen.
- **Guard:** **Finalisierung nur über autorisierten S1-Fluss** (Nummer + PDF + Historie).

### 3.5 Alte PDF-/Downloadpfade
- **Prüfen:** dynamische PDF-Downloads, die bei jedem Aufruf **neu rendern** · Downloads ohne `final_pdf_file_id` · alte Templates, die finale Belege simulieren · ungeschützte Storage-Links.
- **Guard:** finaler Rechnungsdownload **immer** gespeicherte Datei aus S1-07; **kein Re-Render** beim Download.

### 3.6 Alte Lösch-/Editierpfade
- **Prüfen:** kann eine finale Rechnung noch gelöscht werden? Positionen finaler Rechnungen geändert? `invoice_files` mit `immutable=true` gelöscht? Kunden/Projekte mit rechnungsrelevanten Bezügen gelöscht?
- **Guard:** **S1-02-Löschsperre + S1-03-Editiersperre dürfen nicht umgangen werden.**

## 4. Tests / Akzeptanzfälle
1. Rechnungsnummer entsteht nur über führenden S1-01-Pfad.
2. alter `makeInvoiceNo`-Pfad kann keine Nummer mehr separat erzeugen.
3. Legacy-Direktroute kann keine finale Rechnung erzeugen.
4. alte Links werden sauber umgeleitet oder lesend angezeigt.
5. finale Rechnung kann über alten Pfad nicht bearbeitet werden.
6. finale Rechnung kann über alten Pfad nicht gelöscht werden.
7. finales PDF wird nicht dynamisch neu gerendert.
8. `immutable` `invoice_file` kann nicht gelöscht werden.
9. Status `sent` kann nicht direkt ohne S1-Flow gesetzt werden.
10. Historie protokolliert Finalisierung.
11. Historie protokolliert PDF-Fehler.
12. Historie protokolliert Kanzlei-Export.
13. Admin-/Bulkpfade umgehen keine Guards.
14. Altdaten bleiben lesbar.

## 5. Risiken & Guards
| Risiko | Guard |
|---|---|
| unbemerkter Legacy-Pfad erzeugt Nummernlücken | Inventur; nur ein autorisierter Nummernweg; Test 1/2 |
| alter PDF-Download rendert abweichende Belege | Download nur über `final_pdf_file_id`; kein Re-Render |
| direkte Route umgeht Löschsperre | alte Schreibpfade sperren; S1-02/03 bleiben wirksam |
| Altdaten durch Cleanup unzugänglich | keine blinde Löschung; Lesepfade erhalten; Redirect statt Bruch |
| Tests mit alten Factories → falsche Sicherheit | Factories/Seeder gegen S1-Guards prüfen |
| Historie mit Beleginhalt verwechselt | Historie dokumentiert Vorgänge, ändert keine Belege |

**Guards (verbindlich):** keine blinde Löschung · erst Inventur, dann Entscheidung · alte Schreibpfade sperren · alte Lesepfade bei Bedarf erhalten · Redirect statt harter Bruch bei bestehenden Nutzerlinks · jede Stilllegung mit Test absichern · S1-01…S1-08 bleiben führend.

## 6. Definition of Done
- Relevante Legacy-Risiken gelistet; klare Entscheidung je Cleanup-Bereich.
- Sichere Stilllegung/Umleitung/Härtung beschrieben; Altdaten bleiben lesbar.
- S1-01…S1-08 nachweislich vor Umgehung geschützt.
- Ausreichende Tests, Risiken und Guards.
- Als eigenständiges Umsetzungsticket verständlich.

## 7. Nicht im Scope
Neue Rechnungs-/Nummern-/PDF-/Zahlungslogik · DATEV-Erweiterung · Mahnwesen · große UI-Neuentwicklung · endgültiges Löschen von `deal_invoices` (spätere Migrationsentscheidung) · playground-Optik.

---
**Ein-Satz-Fazit:** S1-10 inventarisiert die Legacy-Rechnungswege (doppelte Nummernlogik, `deal_invoices`-Direktroute, ungeschützte Status-/PDF-/Lösch-/Editierpfade), entscheidet je Pfad kontrolliert (sperren/umleiten/härten) und sichert per Test ab, dass die S1-Kette nicht mehr umgangen werden kann — ohne Altdaten unzugänglich zu machen.
