# S1-09 — UI-Konsolidierung Rechnungen / OP / Kanzlei

**Stand:** 2026-07-03 · **Detail-Ticket — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, **keine Design-Vorlage** — die konsolidierte UI wird strikt im bestehenden ticket-Design gebaut (Vuexy/Bootstrap-Blade, ticket-Sidebar, vorhandene Cards/Tabellen/Buttons/Modals/Badges, Select2/Toastr). Planner-/Kanban-Änderungen unberührt.
**Priorität:** P2 · **Sprint:** 1 (Abschluss) · **Grundlage:** S1-01…S1-08.

---

## 0. Einordnung & Kernregel
Die S1-Reihe hat den Rechnungsprozess fachlich geschlossen (Nummer, Löschsperre, OP/Zahlung, Storno, finales PDF, Kanzlei-Export). **S1-09 führt diese Funktionen in einer konsistenten Oberfläche zusammen — ohne neue Fachlogik.**

**Kernregel:** S1-09 ist ein **UI-/UX-Konsolidierungsticket**. Es **vergibt keine Rechnungsnummern, rendert keine PDFs, berechnet keine Zahlungen und definiert keine Exportlogik neu.** Jede Aktion delegiert an die bestehenden Services aus S1-01…S1-08.

## 1. Scope
**Enthalten:** einheitliche Rechnungsübersicht · Detailansicht · OP-Ansicht · Zahlungsstatus-Anzeige · PDF-Status-Anzeige · Kanzlei-Übergabestatus · Storno-/Korrekturhinweise · statusabhängige Aktionen · klare Warnhinweise bei blockierenden Zuständen · Rollen-/Rechte-Sichtbarkeit.
**Nicht enthalten:** keine Änderung der Rechnungslogik · kein neues Datenmodell · keine neue Exportlogik · keine DATEV-Detailintegration · kein Mahnwesen · kein Re-Render finaler PDFs.

## 2. UI-Bereiche

### 2.1 Rechnungsübersicht (Liste)
Spalten: Rechnungsnummer · Kunde · Projekt/Auftrag · Status · Rechnungsdatum · Fälligkeitsdatum · Brutto · Bezahlt · Offen · PDF-Status · Kanzlei-Status · Aktionen.
- Status-/PDF-/Kanzlei-Spalten als **Badges** (ticket-Stil); Filter nach Status/Zahlungsstatus/Überfälligkeit/PDF-/Kanzlei-Status.
- Drafts erkennbar (ohne Nummer); finale Rechnungen visuell abgesetzt.

### 2.2 Rechnungsdetail
Stammdaten · Beträge · **Positionen bei finalisierten Rechnungen nur lesend** · finaler Beleg (PDF) · Zahlungsverlauf (S1-05) · OP-Status (S1-06) · Historie (`invoice_histories`) · Storno-/Korrekturbezug (`original_invoice_id`/`reversal_invoice_id`).

### 2.3 OP-/Zahlungsbereich
bezahlt · teilbezahlt · offen · überfällig · Tage überfällig · Plausibilitätswarnungen (z. B. bezahlt > Brutto, offen negativ außer bei Storno). Werte **nur angezeigt**, nicht neu berechnet.

### 2.4 PDF-Bereich
finales PDF vorhanden (`final_pdf_file_id != null`) · `sha256` vorhanden · `pdf_failed_at` gesetzt · **Download der gespeicherten Datei** (kein Re-Render) · **Retry-Button nur** bei `pdf_failed_at != null && final_pdf_file_id == null`, ausschließlich über die S1-07-Logik.

### 2.5 Kanzlei-Bereich
geprüft · bereit für Übergabe · nicht bereit (mit Grund) · exportiert · Export-Batch-Hinweis · Warnungen bei fehlenden Voraussetzungen (Ableitung aus S1-08, nur angezeigt).

## 3. Aktionen (statusabhängig)
Rechnung öffnen · finales PDF herunterladen · Zahlungshistorie öffnen · OP-Detail öffnen · als geprüft markieren · für Kanzlei-Export vormerken · Export-Batch öffnen · Storno/Korrektur anzeigen · PDF-Retry auslösen (nur wenn erlaubt). Alle Aktionen rufen bestehende S1-Services/-Ansichten.

## 4. Status-/Button-Regeln (Sichtbar/Gesperrt)
| Zustand | sichtbare/gesperrte Aktionen |
|---|---|
| **draft** | bearbeiten/löschen erlaubt; kein PDF/Kanzlei/Export |
| **sent/final** | read-only Inhalt; PDF-Download; Storno; Zahlung erfassen; Kanzlei-Aktionen je Bereitschaft |
| **bezahlt** | PDF-Download; keine weitere Zahlung nötig; Kanzlei-Export möglich |
| **teilbezahlt** | Zahlung erfassen; OP sichtbar |
| **offen** | Zahlung erfassen |
| **überfällig** | wie offen/teilbezahlt + Überfällig-Badge |
| **storniert** (`is_reversed`) | read-only; Verweis auf Storno-Beleg; keine Zahlung |
| **korrigiert** | Verweis auf Korrekturbeleg |
| **PDF fehlt** | „nicht bereit"; Hinweis; kein Export |
| **PDF-Fehler** | Retry-Button; „nicht bereit" |
| **bereit für Kanzlei** | „als geprüft"/„vormerken"/Export möglich |
| **bereits exportiert** | Export-Batch-Hinweis; erneuter Export möglich (neuer Batch) |

## 5. Wichtige UI-Regel (finale Rechnungen)
Finalisierte Rechnungen dürfen **nicht** wie Entwürfe bearbeitbar wirken. Klar sichtbar machen:
- Beleg ist **unveränderlich**,
- Änderungen **nur über Storno/Korrektur**,
- Zahlungsstand ist **dynamisch** (nicht Beleginhalt),
- PDF ist **gespeicherter Originalbeleg** (kein Live-Render).

## 6. Berechtigungen (rollenabhängige Sicht)
- **Geschäftsführung:** alles.
- **Buchhaltung:** Rechnungen, OP, PDF, Kanzlei, Export.
- **Vertrieb:** nur relevante Rechnungseinsicht, **kein** Kanzlei-Export.
- **Montage:** kein Kanzlei-Export, keine Finanzdetails (außer fachlich erlaubt).
- **Admin:** technische Diagnose.
*(Umsetzung über bestehenden ticket-Berechtigungsmechanismus; `is_admin`-Bypass bleibt. Export-/Download-/Retry-Buttons müssen gated sein.)*

## 7. Tests / Akzeptanzfälle
1. finale Rechnung ist nicht mehr wie ein Entwurf bearbeitbar.
2. PDF-Download nutzt gespeicherte Datei.
3. PDF-Fehler wird sichtbar angezeigt.
4. OP-Status korrekt dargestellt.
5. überfällige Rechnung klar markiert.
6. stornierte Rechnung zeigt Bezug.
7. Korrekturrechnung zeigt Bezug.
8. nicht bereite Rechnung blockiert Kanzlei-Aktion.
9. bereite Rechnung zeigt Kanzlei-Aktion.
10. Nutzer ohne Recht sieht keinen Exportbutton.
11. Zahlungsstand verändert nicht das finale PDF.
12. UI zeigt Warnung bei fehlendem `final_pdf_file_id`.

## 8. Risiken & Guards
| Risiko | Guard |
|---|---|
| UI suggeriert Änderbarkeit finaler Belege | finale Rechnung nur lesend darstellen; deutliche „unveränderlich"-Kennzeichnung |
| Kanzlei-Export für falsche Rollen sichtbar | Rollen-Gating der Kanzlei-Aktionen |
| PDF-Retry mit Re-Render verwechselt | Retry ruft nur S1-07-Logik; Download immer gespeicherte Datei |
| OP-Stand als Beleginhalt missverstanden | klare Trennung „Beleg (fix)" vs. „OP (dynamisch)" in der UI |
| Storno/Korrektur nicht nachvollziehbar | beidseitige Verlinkung + Badge |
| UI-Aktion umgeht Fachlogik | jede Aktion delegiert an bestehende S1-Services; keine Direktmutation |

## 9. Definition of Done
- Konsistente UI für Rechnungen, OP, PDF und Kanzlei beschrieben.
- Entwurf / finaler Beleg / dynamischer OP-Stand klar getrennt.
- Statusanzeigen, Aktionen, Sperren und Berechtigungen definiert.
- **Keine** Fachlogik geändert; kein Re-Render; kein neues Datenmodell.
- Als eigenständiges Umsetzungsticket verständlich, schließt an S1-01…S1-08 an.

## 10. Nicht im Scope
Neue Rechnungs-/Nummern-/PDF-/Zahlungs-/Exportlogik · DATEV-Detailintegration · Mahnwesen · Re-Render finaler PDFs · UI-Komplettsanierung über Rechnungen/OP/Kanzlei hinaus · playground-Optik.

---
**Ein-Satz-Fazit:** S1-09 bündelt Rechnungs-, OP-, PDF- und Kanzlei-Funktionen in einer konsistenten, rollenbewussten ticket-UI, die Entwurf, unveränderlichen Beleg und dynamischen OP-Stand klar trennt — ohne eine einzige Fachlogik zu verändern.
