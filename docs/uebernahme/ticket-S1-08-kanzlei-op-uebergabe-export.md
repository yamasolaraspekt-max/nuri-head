# S1-08 — OP-/Kanzlei-Übergabeliste + Export

**Stand:** 2026-07-02 · **Detail-Ticket — KEIN Code, KEINE Migration geschrieben, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI später nur im ticket-Design. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P2 · **Sprint:** 1 · **Grundlage:** S1-01…S1-07. **A1 = Option 1** (Kanzlei führt FiBu; ticket liefert Belege + OP). **Kein** DATEV/Journal/Kontierung.

---

## 0. Einordnung in die S1-Reihe

Die Vorarbeiten haben die Datengrundlage geschaffen — S1-08 führt sie **erstmals** in einer nutzbaren Kanzlei-/Buchhaltungsansicht zusammen:
- **S1-01:** lückenlose Rechnungsnummer beim finalen Versandstatus.
- **S1-02:** Löschsperre für rechnungsrelevante Daten/Belege.
- **S1-03:** Editiersperre ausgestellter Rechnungen (OP-/Beleg-Stabilität).
- **S1-04:** Storno-/Korrekturfluss mit `original_invoice_id`/`reversal_invoice_id`.
- **S1-05/06:** `invoice_payments`, `paid_amount`, `open_amount`, `payment_status`, `is_overdue`.
- **S1-07:** unveränderliches finales Rechnungs-/Storno-PDF mit `sha256`, `final_pdf_file_id`, `pdf_failed_at`.

**S1-08 liest diese Daten nur** und bereitet sie für Kontrolle & Übergabe auf.

## 1. Ziel & fachliche Kernentscheidung

**Ziel:** eine zentrale OP-/Kanzlei-Übergabeliste, mit der Büro, Geschäftsführung und Steuerberater schnell erkennen: welche Rechnungen final gestellt/bezahlt/teilbezahlt/offen/überfällig sind, welche finalen PDFs vorliegen, wo Belege fehlen/fehlgeschlagen sind, und welche Rechnungen an die Kanzlei übergeben werden können.

**Kernentscheidung (unverrückbar):** Der finale Rechnungsbeleg aus **S1-07 bleibt unveränderlich**. Der Zahlungs-/OP-Stand ist **dynamisch** und darf **nicht** ins finale PDF eingefroren werden. Deshalb: **S1-08 rendert keine PDFs, finalisiert keine Rechnungen, vergibt keine Nummern und ändert keine Belege.** S1-08 ist eine **read-only Auswertungs-/Übergabeschicht** über bestehenden Daten; der einzige schreibende Vorgang ist das **Protokollieren von Prüf-/Übergabezuständen** (siehe §7/§9).

## 2. Scope

**Enthalten:** (1) OP-/Kanzlei-Übergabeliste · (2) Filter & Statuslogik · (3) Exportkonzept · (4) PDF-Verknüpfung · (5) Prüf-/Warnlogik · (6) Kanzlei-Übergabezustand · (7) Testfälle · (8) DoD · (9) Risiken/Guards.

**Nicht enthalten:** keine Steuer-/DATEV-Buchungslogik; keine Änderung/Neu-Rendern finaler PDFs; keine automatische Zahlungserfassung; keine Mahnlogik; keine DATEV-EXTF-Detailimplementierung (liegt ggf. in separaten Finanzphasen — hier nur Anschlussfähigkeit dokumentiert); keine UI-Komplettsanierung (nur fachliche Definition der Liste + Aktionen).

## 3. Datenbasis (nur lesend)

| Quelle | Verwendete Felder |
|---|---|
| `invoices` | `id`, `invoice_no`, `type`, `status`, `issue_date`, `due_date`, `service_from/to`, `total_amount` (Brutto), `paid_amount`, `open_amount` (Attribut), `payment_status`, `is_overdue`, `is_reversal`, `is_reversed`, `original_invoice_id`, `reversal_invoice_id`, `final_pdf_file_id`, `pdf_failed_at`, `customer_id`, `deal_id`/`project_id`/`department_id` (falls vorhanden) |
| `invoice_files` | `id`, `file_role='final_pdf'`, `sha256`, `generated_at`, gespeicherter Pfad/Dateiname |
| `invoice_payments` (S1-05) | Summen/Historie (für OP-Detail & Plausibilität) |
| Kunde | `new_leads` (Name, ggf. Kundennummer) |
| Projekt/Auftrag | `deals`/`projects` (falls verknüpft) |

Alle Beträge/Status stammen aus den servicegepflegten Feldern (S1-05/06) — S1-08 rechnet **nicht** neu, sondern **zeigt** und **prüft** (Plausibilität, §8).

## 4. Liste / Spalten

Tabellenansicht (ticket-Design, bestehende Tabellen-/Badge-Komponenten):

| Spalte | Quelle/Ableitung |
|---|---|
| Rechnungsnummer | `invoice_no` (Drafts ohne Nummer erscheinen **nicht** in der Übergabeliste) |
| Kunde | `new_leads` |
| Projekt/Auftrag | `deal`/`project` (falls vorhanden, sonst „—") |
| Rechnungsdatum | `issue_date` |
| Fällig am | `due_date` |
| Brutto | `total_amount` |
| Bezahlt | `paid_amount` |
| Offen | `open_amount` |
| Zahlungsstatus | `payment_status` (offen/teilbezahlt/bezahlt) |
| Überfällig ja/nein | `is_overdue` |
| Tage überfällig | `heute − due_date` (nur wenn `is_overdue`) |
| Beleg-PDF vorhanden | `final_pdf_file_id != null` |
| PDF-Fehler | `pdf_failed_at != null && final_pdf_file_id == null` |
| Storno/Korrekturhinweis | aus `is_reversal`/`is_reversed` + `original_invoice_id`/`reversal_invoice_id` |
| Übergabestatus Kanzlei | abgeleitet + protokolliert (§6) |
| Aktionen | siehe §5 |

## 5. Aktionen

- **Finales PDF herunterladen** — Stream aus `final_pdf_file_id` (S1-07), **kein** Re-Render.
- **Rechnung öffnen** — Detailansicht.
- **Zahlungshistorie öffnen** — `invoice_payments` (S1-05).
- **OP-Detail öffnen** — offene Posten inkl. Storno-Netting (S1-06).
- **Als geprüft markieren** — setzt Prüf-Protokoll (`office_reviewed_at/by`, §7).
- **Für Kanzlei-Export vormerken** — markiert Rechnung/Selektion für den nächsten Export.
- **Export erzeugen** — CSV/Excel bzw. ZIP mit Belegen (§8).
- **PDF-Retry auslösen** — **nur** wenn `pdf_failed_at` gesetzt **und** `final_pdf_file_id == null`; ruft den S1-07-Retry (Job) auf. *(Einzige Aktion, die außerhalb S1-08 wirkt — delegiert an S1-07, kein eigenes Rendern.)*

## 6. Statuslogik & Kanzlei-Übergabezustand

**Anzeige-Zustände je Zeile:**
- **bezahlt** — `payment_status = paid`.
- **offen** — `payment_status = open`, nicht überfällig.
- **teilbezahlt** — `payment_status = partially_paid`, nicht überfällig.
- **überfällig** — `is_overdue = true`.
- **storniert** — `is_reversed = true` (Ursprungsrechnung) bzw. Beleg ist Storno (`is_reversal`).
- **korrigiert** — Ursprung mit vorhandenem `reversal_invoice_id` (Korrekturbezug vorhanden).
- **PDF fehlt** — ausgestellt, aber `final_pdf_file_id == null` **und** `pdf_failed_at == null`.
- **PDF-Fehler** — `pdf_failed_at != null && final_pdf_file_id == null`.

**Übergabezustand Kanzlei (abgeleitet):**
- **bereit für Übergabe**, wenn **ALLE** gelten:
  1. `status` final/`sent` (ausgestellt),
  2. `invoice_no` vorhanden,
  3. `final_pdf_file_id` vorhanden (finales PDF existiert),
  4. **kein** offener PDF-Fehler (`pdf_failed_at` gelöst),
  5. OP-Daten plausibel (keine Plausibilitätsfehler aus §8),
  6. **kein** fachlich blockierender Zwischenzustand (z. B. Storno-Draft ohne Nummer).
- **nicht bereit** — sobald eine Bedingung verletzt ist; die Zeile nennt den konkreten Grund.

**Protokollierte Übergabezustände** (persistiert, §7): `geprüft` → `vorgemerkt` → `exportiert/übergeben`. „bereit" ist rein abgeleitet, „exportiert" ist protokolliert.

## 7. Datenmodell-Konzept (additiv/nullable — nur Prüf-/Übergabeprotokoll)

S1-08 ist read-only auf Rechnungsinhalte; es persistiert **ausschließlich** Prüf-/Übergabestatus. Konzept (Umsetzung später, keine Migration in diesem Ticket):

**`invoices` additiv (Prüfstatus):** `office_reviewed_at` (timestamp, nullable), `office_reviewed_by` (FK employees, nullable). *(Diese Felder in die S1-03-Allow-Liste aufnehmen — sind kein Belegkontent.)*

**`kanzlei_export_batches` (neu, Übergabeprotokoll, append-only):** `id`, `period_from`, `period_to`, `format` (`csv`|`zip`|…), `created_by`, `created_at`, `invoice_count`, `pdf_count`, `has_warnings` (bool), `status` (`complete`|`with_warnings`). 

**`kanzlei_export_batch_items` (neu, Snapshot je exportierter Rechnung):** `batch_id`, `invoice_id`, plus **Snapshot** `invoice_no`, `total_amount`, `paid_amount`, `open_amount`, `payment_status`, `sha256`. Der Snapshot dokumentiert den **Stand zum Exportzeitpunkt** (der spätere dynamische OP-Stand darf den Protokolleintrag nicht rückwirkend verändern).

Das Protokoll ist **append-only**: ein erneuter Export erzeugt einen **neuen** Batch, überschreibt nie einen alten.

## 8. Export

**Varianten:**
1. **CSV/Excel** — für Büro-/Kanzlei-Kontrolle (tabellarisch, alle §Exportfelder).
2. **PDF-/ZIP-Export** — enthält **nur vorhandene finale PDFs** (`final_pdf_file_id`), gebündelt; fehlende Belege werden **nicht** erzeugt, sondern als Warnung im Protokoll gelistet.
3. **Optional spätere DATEV-nahe Übergabe** — hier **nur als Anschlussfähigkeit** dokumentiert (Feldstruktur so wählen, dass sie später mappbar ist); **keine EXTF-Implementierung** in S1-08.

**Exportfelder:** Rechnungsnummer · Rechnungsdatum · Kunde · Kundennummer (falls vorhanden) · Bruttobetrag · bezahlt · offen · Zahlungsstatus · Fälligkeitsdatum · Leistungs-/Projektbezug · Belegdateiname · `sha256` (aus `invoice_files`) · Storno-/Korrekturbezug (`original_invoice_id`/`reversal_invoice_id`) · Übergabezeitpunkt · Übergabebenutzer.

**Kernentscheidung Export:** Der Export **erzeugt keine Rechnungsnummern, finalisiert keine Rechnungen und rendert keine PDFs neu.** Er exportiert **nur bereits finalisierte und geprüfte** Daten (gespeicherte PDFs + servicegepflegte OP-Werte).

## 9. Übergabeprotokoll

Je Export wird dokumentiert (über `kanzlei_export_batches`, §7):
- **wann** exportiert wurde (`created_at`), **wer** (`created_by`),
- **welcher Zeitraum** (`period_from`–`period_to`),
- **wie viele Rechnungen** enthalten waren (`invoice_count`),
- **wie viele PDFs** enthalten waren (`pdf_count`),
- ob **Fehler/fehlende Belege** vorhanden waren (`has_warnings` + Warnliste),
- ob der Export **vollständig** oder **mit Warnungen** erzeugt wurde (`status`).

## 10. Idempotenz

- Mehrfacher Export desselben Zeitraums **verändert keine Rechnung** und rendert kein PDF.
- Ein erneuter Export **gibt denselben Datenstand erneut aus** (neuer, eigener Batch-Eintrag) und **überschreibt keine fachlichen Zustände** unbeabsichtigt (append-only Protokoll; `office_reviewed_*` bleibt bestehen, wird nicht durch Export verändert).

## 11. Prüf- & Warnlogik

Warnfälle (Zeile sichtbar markieren, „nicht bereit" bzw. Plausibilitätsfehler):
- finale Rechnung **ohne** PDF (`final_pdf_file_id == null`),
- Rechnung mit `pdf_failed_at` (offener PDF-Fehler),
- **offener Betrag negativ** (außer bei Storno-Belegen, wo negativ korrekt ist — dort separat behandeln),
- **bezahlt > Brutto** (Plausibilitätsfehler; sollte durch S1-05-Overpayment-Guard nie entstehen → Alarm),
- **fehlendes Fälligkeitsdatum** (`due_date` null),
- **fehlende Rechnungsnummer** bei ausgestellter Rechnung,
- Rechnung **nicht sent/final**, aber im Exportfilter → ausschließen + Warnung,
- **Storno ohne Bezug** (`is_reversal` ohne `original_invoice_id`),
- **Korrektur ohne Bezug** (Ursprung als `is_reversed`, aber `reversal_invoice_id` null),
- `final_pdf_file_id` zeigt auf **nicht vorhandene Datei** (Storage-Miss),
- **Hash fehlt** bei `file_role='final_pdf'` (`sha256` null).

Warnungen **blockieren die Übergabe-Bereitschaft** (nicht bereit) bzw. werden im Export-Protokoll als `has_warnings` geführt.

## 12. Berechtigungen

- **Buchhaltung/Finanzen:** Liste sehen **und** exportieren.
- **Geschäftsführung:** alles sehen (inkl. Export).
- **Vertrieb/Montage:** **keine** Kanzlei-Exporte auslösen (kein Export-/Vormerk-/Retry-Button).
- **Download finaler PDFs** nur für berechtigte Rollen.
- **Export-/Prüf-/Vormerk-Aktionen auditierbar** (wer/wann — via Protokoll + ggf. `invoice_histories`).

*(Umsetzung über den bestehenden ticket-Berechtigungsmechanismus — Rollen/`user_rolls`/Abteilung; der `is_admin`-Bypass bleibt für Admins wirksam. Rollen-Feinschliff kein S1-08-Blocker, aber Export/Download müssen gated sein.)*

## 13. Tests (≥ 10)

1. **bezahlte Rechnung** erscheint korrekt (Status „bezahlt", offen = 0).
2. **offene Rechnung** erscheint korrekt (offen = Brutto).
3. **teilbezahlte Rechnung** berechnet offenen Betrag korrekt (offen = Brutto − bezahlt).
4. **überfällige Rechnung** wird korrekt markiert (is_overdue, Tage überfällig).
5. **finale Rechnung ohne PDF** → „nicht bereit" + Warnung „PDF fehlt".
6. **Rechnung mit `pdf_failed_at`** → „PDF-Fehler"; Retry-Aktion verfügbar.
7. **Rechnung mit `final_pdf_file_id`** → Download liefert **gespeicherte** Datei (kein Re-Render).
8. **Export** enthält **nur** finalisierte Rechnungen (Drafts/nicht-sent ausgeschlossen).
9. **Export** verändert keine Rechnung und rendert kein PDF neu (Row-Hash/`updated_at` unverändert; keine neue `final_pdf`-Datei).
10. **Storno-/Korrekturrechnungen** werden nachvollziehbar dargestellt (beidseitiger Bezug sichtbar).
11. **bezahlt > Brutto** → Plausibilitätsfehler markiert, „nicht bereit".
12. **ZIP-/Belegexport** enthält **nur vorhandene** finale PDFs; fehlende als Warnung protokolliert.
13. **Idempotenz:** zweiter Export desselben Zeitraums ändert keine Rechnung; neuer Batch-Eintrag entsteht.
14. **Berechtigung:** Vertrieb/Montage sehen keinen Export-Button/keinen PDF-Download.

## 14. Risiken & Guards

| Risiko | Guard |
|---|---|
| Kanzlei-Export mit DATEV-Export verwechselt | S1-08 explizit als **OP-/Kontroll-Übergabe ohne Kontierung** deklariert; DATEV/EXTF ausdrücklich out of scope |
| Dynamischer OP-Stand landet im finalen Beleg | S1-08 rendert **nie** PDF; OP-Stand nur in Liste/Export/Snapshot, nie im S1-07-Beleg |
| PDF wird beim Export neu gerendert | Export/Download liest **ausschließlich** `final_pdf_file_id`; kein Renderpfad in S1-08 |
| Nicht-finale Rechnungen exportiert | Exportfilter erzwingt `sent` + `invoice_no` + `final_pdf_file_id` |
| Fehlende Belege übersehen | Prüf-/Warnlogik (§11) markiert + protokolliert; „nicht bereit" |
| Zahlungen/Status widersprechen sich | Plausibilitätsprüfung (bezahlt ≤ Brutto, offen = Brutto − bezahlt); Alarm bei Bruch |
| Export überschreibt fachliche Zustände | append-only Protokoll; Export ändert keine `invoices`-Inhalte, nur Protokoll |
| Unberechtigter Export/Download | Rollen-Gating (§12), auditierbar |

**Guards (verbindlich):** keine Änderung finaler Rechnungsbelege · kein Re-Render bei Export · Export nur aus gespeicherten Daten · `final_pdf_file_id` ist **einzige** Belegdownload-Quelle · Rechnungen ohne finale Nummer sind nicht exportfähig · fehlerhafte Daten sichtbar blockieren/kennzeichnen · Übergabe nachvollziehbar/auditierbar.

## 15. Definition of Done

1. Ticket erklärt Zweck, Datenbasis, Statuslogik, Listenansicht, Exportvarianten und Prüfregeln vollständig.
2. Saubere Abgrenzung von DATEV-Detailbuchung, Mahnwesen und UI-Cleanup.
3. Nutzt S1-07-PDFs **nur lesend**; kein Re-Render.
4. Macht klar, dass **OP-Daten dynamisch** bleiben und nicht in den finalen Beleg gelangen.
5. Definiert **Übergabestatus** (geprüft/vorgemerkt/exportiert) und **Exportfähigkeit** („bereit für Übergabe").
6. Enthält ausreichende **Tests, Risiken und Guards**.
7. Ist als eigenständiges Umsetzungsticket verständlich und schließt an S1-01…S1-07 an.

## 16. Nicht im Scope

DATEV-/Steuer-Buchungslogik · Änderung/Neu-Rendern finaler PDFs · automatische Zahlungserfassung · Mahnwesen · DATEV-EXTF-Detailimplementierung · UI-Komplettsanierung (nur Definition der Liste/Aktionen) · Kontierung/mapping_key · playground-Optik.

---
**Ein-Satz-Fazit:** S1-08 führt die in S1-01…S1-07 erzeugten Rechnungs-, Zahlungs-, OP- und PDF-Daten in einer read-only Kanzlei-Übergabeliste mit klarer „bereit für Übergabe"-Logik, Prüf-/Warnregeln und CSV-/ZIP-Export samt append-only Übergabeprotokoll zusammen — ohne PDFs neu zu rendern, ohne Rechnungen zu verändern und ohne jede DATEV-/Kontierungslogik.
