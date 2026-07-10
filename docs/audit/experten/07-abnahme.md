# 07 — Experten-Inventur: ABNAHME (Qualität / Übergabe)

> Rolle: ABNAHME-EXPERTE · Stufe 1 CRM-AUTOMATISIERUNG-MASTER · **REIN LESEND**
> Bereich: Abnahmeprotokoll · Vollständigkeitsprüfung · Kunden-Übergabe · Mängel/Nachbesserung · Gewährleistungsbeginn · Übergang Abnahme→Rechnung · Wartungsstart
> Baut auf: `docs/audit/{code-audit,intelligenz-audit,automatisierungs-hebel}.md`, `docs/architektur-entscheidungen.md` (Weiche 1: Abnahme = **eigene** Phase), `docs/glossar.md`
> TABU respektiert: Nuriva · Video/Jitsi · Invoice-Zone (nur an der Naht Abnahme→Rechnung) · Legacy (Bitrix/NIBE/IMAP)
> Datenstand: read-only SQL gegen `ticket` (CNF), 2026-07-10

---

## 0. Kernbefund in einem Satz

**Die Abnahme existiert im ticket-CRM als leere Kanban-Spalte und als kosmetischer Fortschritts-Substatus — es gibt KEIN strukturiertes Abnahmeprotokoll, KEINE Vollständigkeitsprüfung, KEINE Mängelliste, KEINEN aus der Abnahme abgeleiteten Gewährleistungsbeginn und KEINE Brücke zur Rechnung.** Die Abnahme ist die **reifste Lücke** und zugleich der **dünnste** Prozessschritt der gesamten Wertschöpfungskette.

---

## 1. IST-FUNKTIONEN (mit Beleg)

### 1.1 Abnahme als Phase (lead_stages)
- **Existiert:** `lead_stages` id=11, `key='abnahme'`, name=„Abnahme", `sort_order=70` (zwischen `project`/Montage=60 und `completed`/Abschluss=80). *(DB verifiziert)*
- Angelegt **2026-07-03** (jüngste Stage), `is_default=0`, `is_protected=0`, `is_closed=0`, **ohne color, ohne icon**. Bestätigt die Weiche-1-Entscheidung („Abnahme = eigene Phase"), aber nur als nackter Slot.
- **Verdrahtet in den Kanban:** `LeadOverviewController.php:271` — `$kanbanPhaseKeys = ['lead','offer','deal','project','abnahme','completed']`. Die Spalte ist also sichtbar.
- **Keine Unterphasen:** `lead_stage_sub_stages` enthält keinen Abnahme-Eintrag. Die anderen Stages haben Sub-Stages, die Abnahme nicht.
- **Nutzung = NULL:** `SELECT lead_stage_id, COUNT(*) FROM lead_product_lists GROUP BY lead_stage_id` → nur Stages 1,2,5,6 belegt (23/15/12/2). **Kein einziger Vorgang** steht in der Abnahme-Phase. Die Spalte ist real leer.

### 1.2 „Abnahme / Qualitätskontrolle" als Fortschritts-Substatus
- `abnahme_qualitaetskontrolle` ist **ein Label-Token** in der hartcodierten Deal-Dokument-Workflow-Substatus-Liste: `LeadOverviewController.php:1961` (`'abnahme_qualitaetskontrolle' => 'Abnahme / Qualitätskontrolle'`), gespiegelt in `resources/views/admin/offer/folder-show.blade.php:6954/7002/7581`.
- Es ist **ein einzelner Status-Marker** in einer Sequenz von ~14 Deal-Substati (…`montage_abgeschlossen` → **`abnahme_qualitaetskontrolle`** → `rechnung_erstellt`…). Reiner Anzeige-/„viewed"-Zustand — **kein Protokoll, keine Checkliste, keine Datenerfassung dahinter.**

### 1.3 „Abnahme" als Checklisten-/Set-Typ (Vorlagen-Ebene, ungenutzt)
- Maintenance-Checkliste kennt einen freien Typ-Text: `maintenance_checklists/index.blade.php:2179` → Placeholder „z.B. Wartung, **Abnahme**". Nur Beispieltext, kein Datentyp.
- `master_sets` Editor bietet Set-Zweck `{ value: 'acceptance', label: 'Abnahme' }` (`master_sets/editor.blade.php:1352`, `index.blade.php:4202`). Eine **Vorlagen-Kategorie**, die eine Abnahme-Checkliste tragen KÖNNTE — real ungenutzt (keine belegten Sets/Protokolle).

### 1.4 Historischer Rest: „Abnahme" = Fotogruppe
- In Kundenprofil-Views ist Abnahme die **letzte Foto-Phase** („end"): `customer_object_profile.blade.php:1516` (`<option value="end">Abnahme</option>`), plus Alt-Code „BILDER VOM ABNAHME" / „SORTIEREN NACH ABNAHME". Abnahme war historisch **nur eine Bilder-Ablagegruppe** am Ende der Montagefotos — kein Protokoll.

### 1.5 Wartung / Wartungsstart (Downstream der Abnahme) — gebaut, fast leer
- **Reiche Infrastruktur vorhanden:** `MaintenanceProtocol` (Model, SoftDeletes; Felder `protocol_no, scheduled_at, started_at, finished_at, status, checklist_snapshot, answers, result_summary`, Techniker-Pivot, `documents()` morphMany), `MaintenanceChecklist`, `MaintenanceChecklistItem`, `CustomerMaintenanceContract`, `MaintenanceAsset`.
- `CustomerMaintenanceContractController.php` = **1.694 Zeilen** (Kalender-Feed, Kanban-Feed, Checklisten-AJAX, Bulk-Status) — substanziell.
- **Daten:** `customer_maintenance_contracts` = **10 Zeilen** (Felder u.a. `start_date, end_date, status, status_overall, recommended_interval_months`). Aber `maintenance_protocols = 0`, `maintenance_checklists = 0`. **Der Protokoll-Motor ist gebaut, aber unbenutzt** — nur die Verträge sind gepflegt.
- **Kein Protokoll-Controller:** `MaintenanceProtocol` wird nur von Models + `CustomerMaintenanceContractController` referenziert; es gibt keinen eigenständigen Protokoll-CRUD. Das ist die **Wartungs**-Schiene, nicht die Erst-Abnahme — aber es ist das **einzige belastbare „Protokoll"-Muster im System** und die naheliegende Vorlage für ein echtes Abnahmeprotokoll (Checklist-Snapshot + Answers + Techniker + Dokumente).

### 1.6 Gewährleistung / Warranty — verstreut, nicht abnahme-abgeleitet
- Warranty-Felder existieren, aber **an keinem Abnahme-Ereignis aufgehängt:**
  - `CustomerProductInfo`: `warranty_until`, `guarantee_until` (`app/Models/CustomerProductInfo.php:24-25`).
  - `MaintenanceAsset`: `warranty_until` (date-cast).
  - `Problem` (Ticket/Reklamation): `warranty_type`, `warranty_duration`, `warranty_remaining` (`app/Models/Problem.php:45-47`, gesetzt in `ProblemController.php:292-294`).
- `NewLeadsController.php:9868` zeigt `warranty_until` nur an (aus `CustomerProductInfo`), setzt nichts aus einer Abnahme. **Kein Feld „Gewährleistungsbeginn = Abnahmedatum", keine Ableitung, keine einzige Wahrheit.**

### 1.7 Mängel / Nachbesserung — kein Abnahme-Mechanismus
- **Kein Abnahme-Mängellisten-Modell.** Mangel/Nachbesserung fällt in das **generische Problem/Ticket-Modul** (`ProblemController`, `problem_board.blade.php`) — das ist **reaktive Reklamation**, nicht die Mängelerfassung **im Abnahmetermin**.
- Der Deal-Substatus endet mit `problem_reklamation` (`LeadOverviewController:1965`) — Reklamation ist als Nachlauf gedacht, nicht als Abnahme-Zwischenzustand („fertig, aber Mängel offen").

### 1.8 NAMENSFALLE: `Handover`/`handovers` ≠ Kunden-Übergabe
- `HandoverController` / `HandoverToController` / Models `Handover`, `HandoverTo` bedienen **internen Lager-/Asset-Transfer zwischen Mitarbeitern** — Route-Prefix `lager/vermoegensbestand` (`routes/web.php:2605-2612`), Joins über `assets` + `employees` (`handover_from`/`handover_to` = Employee-IDs), Mengen-Verbuchung gegen `assets.quantity`.
- **Daten:** `handovers = 0`, `handover_tos = 0` Zeilen.
- **Das ist NICHT die Kunden-Übergabe/Abnahme.** Wer nach „handover" sucht, landet in der Werkzeug-/Geräteausgabe. Diese Falle ist im Audit explizit zu markieren, damit kein Bauplan die Kunden-Abnahme fälschlich an `handovers` andockt.

### 1.9 Abnahme→Rechnung (Naht, TABU-Grenze)
- Bestätigt die Vorbefunde: **reiner Medienbruch.** Kein Pfad aus Abnahme/Abschluss zu einem Rechnungs-Entwurf; Rechnung nur manuell (`InvoiceController.php:218`, `InvoiceCanvasController.php:56`). Belege: intelligenz-audit **K4 / I-10**, automatisierungs-hebel **H-A6**.
- Verschärfend hier: Da die Abnahme-Phase **kein Ereignis erzeugt** (0 Nutzung, kein Protokoll, kein „Abnahme erfolgt am"-Datum), fehlt sogar der **Trigger**, an den ein Rechnungs-Entwurf-Vorschlag angehängt werden könnte. Die Brücke ist nicht nur ungebaut — es fehlt das Widerlager auf der Abnahme-Seite.
- **Reihenfolge nicht erzwungen:** `moveStageWorkflow` lässt jede aktive Stage als Ziel zu (`stageExists` `:2818`; Validierung `:4904-4917`); „Abnahme ohne Montage" bzw. Übersprung ist nicht verhindert (intelligenz-audit I-… / `:5330` Gate nur beim Deal-Eintritt). Für die Abnahme heißt das: sie kann übersprungen werden, ohne dass etwas fehlt-markiert wird.

---

## 2. STÄRKEN

1. **Die Phase ist konzeptionell verankert und frisch angelegt.** `lead_stages` id=11 „Abnahme" (sort 70) setzt Weiche-1 („Abnahme = eigene Phase") bereits datenseitig um und ist im Kanban sichtbar — das Fundament (der Slot zwischen Montage und Abschluss) steht.
2. **Ein belastbares Protokoll-Muster existiert bereits im Haus** (Wartung): `MaintenanceProtocol` mit `checklist_snapshot` (eingefrorene Vorlage), `answers`, `protocol_no`, Techniker-Zuordnung, morph-Dokumenten — genau die Bausteine, die ein Abnahmeprotokoll braucht. Ein Abnahme-Protokoll müsste **nicht neu erfunden**, sondern nach diesem Muster gebaut/wiederverwendet werden.
3. **Vorlagen-Haken vorhanden:** Set-Zweck `acceptance` und der freie Checklisten-Typ „Abnahme" sind schon als Kategorien angelegt — die Vorlagen-Ebene ist vorbereitet, nur nicht befüllt.
4. **Warranty-Felder existieren** (mehrere) — ein Gewährleistungsbeginn müsste nur **abgeleitet/verdrahtet**, nicht als Feld erst geschaffen werden.

## 3. SCHWÄCHEN

1. **Kein strukturiertes Abnahmeprotokoll.** Kein Model/Controller/Tabelle/Route für eine Abnahme. `abnahme_qualitaetskontrolle` ist ein bloßes Anzeige-Label. → Der rechtlich/finanziell schärfste Prozessschritt (Gewährleistungsbeginn, Fälligkeit Schlussrechnung) ist datenseitig **ein Loch**.
2. **Keine Vollständigkeitsprüfung.** Nichts prüft vor/bei Abnahme, ob alle Montage-Schritte, Fotos, Dokumente, Materialien abgeschlossen sind. Die Progressbar (aus `planner_items`) ist Anzeige, kein Gate.
3. **Keine Mängel-/Nachbesserungserfassung im Abnahmekontext.** Der Zustand „fertig, aber noch nicht abgenommen" bzw. „abgenommen unter Vorbehalt / mit Restmängeln" — von der Weiche-1-Entscheidung ausdrücklich gefordert („muss sichtbar bleiben") — ist **nicht abbildbar**. Mängel verschwinden ins generische Ticket-Modul.
4. **Keine Kunden-Übergabe / kein Signatur-Flow für die Abnahme.** Signatur-Infrastruktur existiert (Video/Mobile/Maintenance), ist aber nicht auf eine Kunden-Abnahme gerichtet. Kein Abnahmedatum, keine Kundenunterschrift, kein PDF-Abnahmeprotokoll.
5. **Kein Gewährleistungsbeginn aus der Abnahme.** Warranty-Felder sind verstreut (CustomerProductInfo / MaintenanceAsset / Problem) und werden **nicht** durch ein Abnahme-Ereignis gesetzt. Keine „eine Wahrheit" für Gewährleistungsstart — Konflikt mit der Governance-Regel „eine Wahrheit je Sachverhalt".
6. **Namensfalle `handovers`** (Lager-Asset-Transfer) verleitet zur falschen Andockstelle für die Kunden-Übergabe.
7. **Abnahme→Rechnung = Medienbruch ohne Widerlager** (K4/H-A6): keine Auslösung, und mangels Abnahme-Ereignis nicht einmal ein Anknüpfpunkt.
8. **Phase überspringbar:** keine Reihenfolge-Erzwingung/Markierung; Abnahme kann still übergangen werden.
9. **Wartungs-Protokollmotor liegt brach** (`maintenance_protocols`/`maintenance_checklists` = 0): der natürliche Downstream der Abnahme (Wartungsstart nach Gewährleistung) ist gebaut, aber nicht in Betrieb — der Übergang Abnahme→Wartung ist noch nicht real gelebt.

---

## 4. REIFE (je Teilfunktion)

| Teilfunktion | Reife (0=fehlt, 5=produktiv/rund) | Beleg |
|---|---|---|
| Abnahme-Phase (Kanban-Slot) | **1** — existiert, sichtbar, aber leer/ohne Sub-Stages/ohne Style | lead_stages id=11; Kanban :271; 0 Nutzung |
| Abnahmeprotokoll (strukturiert) | **0** — nicht vorhanden | kein Model/Route/Tabelle |
| Vollständigkeitsprüfung | **0** | — |
| Mängel/Nachbesserung (im Abnahmekontext) | **0** — nur generisches Ticket downstream | ProblemController; `problem_reklamation` |
| Kunden-Übergabe / Signatur / Abnahmedatum | **0** | keine Abnahme-Signatur/Datum |
| Gewährleistungsbeginn (abgeleitet) | **1** — Felder da, aber nicht abnahme-verdrahtet | CustomerProductInfo/MaintenanceAsset/Problem |
| Abnahme→Rechnung-Brücke | **0** (Medienbruch) | K4/I-10/H-A6 |
| Wartungsstart (Downstream) | **2** — Infra reich, Daten fast leer | Contracts 10; Protokolle 0 |
| Vorlagen-Haken (acceptance-Set/Checklist-Typ) | **1** — Kategorie da, unbefüllt | master_sets `acceptance` |

## 5. AUTOMATISIERUNGS-REIFE (gesamt für den Bereich)

**Grad 0–1 von 5 (niedrigster im gesamten CRM).**

Die Intelligenz-Skala des `intelligenz-audit` bewertet den Alt-Kern als „stumme Datenbank (Grad ~2)". Die **Abnahme unterschreitet das**: Grad 2 setzt voraus, dass Zustände wenigstens sauber gespeichert werden — für die Abnahme existiert aber **kein Datenmodell**, das etwas speichern könnte. Es gibt nichts zu automatisieren, weil es nichts zu erfassen gibt. Die einzige „Automatik" wäre der Kanban-Zug in eine leere Spalte.

- **Kein Vorwärts-Automatismus** (Abnahme→Rechnung, Abnahme→Gewährleistungsstart, Abnahme→Wartungsplanung) — alles Medienbruch.
- **Kein Gate/Plausibilisierung** (Vollständigkeit, Reihenfolge).
- **Kein Assistenzbaustein** (anders als die junge Rechen-Zone Grad ~4).

Bewertung deckt sich mit `intelligenz-audit` (Kette-Tabelle: „Abnahme→Rechnung: 1 — kein Draft, kein Anstoß") und `automatisierungs-hebel` H-A6 (b). **Fazit: höchster Nachholbedarf, aber auch günstiger Hebel**, weil das Protokoll-Muster (MaintenanceProtocol) und die Vorlagen-Kategorien (`acceptance`) bereits im Haus sind — Bau = Wiederverwendung + Verdrahtung, nicht Neuerfindung.

---

## 6. Empfohlene Hebel-Reihenfolge (nur Landkarte, NICHT jetzt bauen)

1. **Abnahme-Datenmodell** nach MaintenanceProtocol-Muster (checklist_snapshot + answers + Mängelpositionen + Abnahmedatum + Unterschrift/PDF), an `deal`/`lead_product_list` gehängt — additiv (Daten-/Ketten-Schutz-Direktive).
2. **Zustand „abgenommen mit Vorbehalt / Mängel offen"** abbildbar machen (Weiche-1-Forderung).
3. **Gewährleistungsbeginn = Abnahmedatum** als *eine* abgeleitete Wahrheit setzen (statt drei verstreuter Warranty-Felder).
4. **Abnahme→Rechnungs-Entwurf** als Vorschlag (H-A6, **(b)** Operanden-Gate; TABU-Naht Invoice-Zone respektieren).
5. **Abnahme→Wartungsstart** (den brachliegenden Protokollmotor in Betrieb nehmen).
6. Vollständigkeits-/Reihenfolge-Gate beim Eintritt in Abnahme (Markierung, nicht harter Block — Weiche 2).

---

## 7. Gelesen / Nicht gelesen · NICHT-VERIFIZIERT · Selbstkritik

### Gelesen (verifiziert)
- Code: `app/Models/Handover.php`, `HandoverTo.php`, `MaintenanceProtocol.php`; `HandoverController.php`, `HandoverToController.php`; `LeadOverviewController.php` (Ausschnitte :271, :1935-1976); Ausschnitt `folder-show.blade.php` (:6945-6960); `routes/web.php` (Abnahme/handover/maintenance-Bereiche).
- DB (read-only): `lead_stages` (voll), `lead_stage_sub_stages`, Spalten `lead_product_lists`, Verteilung `lead_product_lists.lead_stage_id`/`.stage`, Zeilenzahlen `handovers/handover_tos/maintenance_protocols/maintenance_contracts/customer_maintenance_contracts/maintenance_checklists`, Spalten `customer_maintenance_contracts`.
- Grep-Belege: warranty-Felder (CustomerProductInfo/MaintenanceAsset/Problem/NewLeadsController), Mängel/Signatur/Abnahme-Streuung, Route-Namen.
- Basis-Docs: `intelligenz-audit.md` (K4/I-10, Kette-Tabelle), `automatisierungs-hebel.md` (H-A6), `architektur-entscheidungen.md` (Weiche 1), `glossar.md` (kein Abnahme-Treffer → als Lücke notiert).

### Nicht gelesen (bewusst / Zeit)
- `CustomerMaintenanceContractController.php` (1.694 Z.) nur nach Umfang/Referenzen geprüft, nicht Zeile-für-Zeile.
- Vollständiger `folder-show.blade.php` (>7.000 Z.) — nur Abnahme-relevante Stellen.
- `code-audit.md` nur mittelbar (über Querbelege), nicht ganz gelesen.
- Alt-Code-/copy-Views (bewusst ignoriert außer als historischer Beleg für „Bilder vom Abnahme").

### NICHT VERIFIZIERT
- **PV-Montage-Prozess-Flowchart (Wissens-Register IMG-020, „Abnahme erfolgreich?"-Verzweigung):** Bild wurde **nicht** geöffnet/gelesen. Die Soll-Ablauf-Verzweigung ist damit als Anspruch bekannt, aber nicht gegen das Bild abgeglichen. Aussage „IST bildet die Verzweigung nicht ab" stützt sich auf den Code-Befund (keine Ja/Nein-Abnahme-Logik), nicht auf das Bild.
- Ob `customer_maintenance_contracts.start_date` in der Praxis an ein Abnahme-/Inbetriebnahme-Datum gekoppelt wird (10 Zeilen nicht inhaltlich inspiziert) — vermutet nein, nicht belegt.
- Ob `master_sets` mit Zweck `acceptance` tatsächlich 0 Zeilen hat — Spalte `purpose` existiert unter diesem Namen nicht (Query-Fehler); die acceptance-Kategorie ist nur view-seitig belegt, DB-seitig nicht gegengeprüft.
- Signatur-Fund in `LeadAlternativeAdd` nicht inhaltlich geöffnet (vermutlich nicht Abnahme).

### Selbstkritik
- Die Grenze zwischen **Wartung** (MaintenanceProtocol, mein „stärkstes Protokoll-Muster") und der **Erst-Abnahme** ist fachlich real; ich bewerte die Wartungs-Infra als *Vorlage*, nicht als vorhandene Abnahme — das könnte optimistisch wirken, ist aber als „gebaut, aber nicht Abnahme" klar markiert.
- „0 Nutzung" der Abnahme-Phase ist ein Momentaufnahme-Argument (Stage erst 2026-07-03 angelegt); der Slot könnte demnächst befüllt werden. Reife-Urteil stützt sich primär auf das **fehlende Datenmodell**, nicht nur auf die leere Spalte — das hält auch, wenn morgen Vorgänge einziehen.
- Invoice-Zone strikt nur an der Naht bewertet (TABU) — die interne Rechnungserzeugungs-Qualität ist bewusst nicht Teil dieses Urteils.

---

## 8. 5-Zeilen-Zusammenfassung

1. **Abnahme = leere Kanban-Spalte** (`lead_stages` id=11, seit 2026-07-03, 0 Vorgänge) + **ein kosmetisches Substatus-Label** (`abnahme_qualitaetskontrolle`) — **kein strukturiertes Abnahmeprotokoll, keine Vollständigkeitsprüfung, keine Mängelliste, keine Kundenunterschrift.**
2. **`handovers`/`HandoverController` sind eine Namensfalle:** interner Lager-Asset-Transfer zwischen Mitarbeitern (0 Zeilen), **nicht** die Kunden-Übergabe — nicht als Abnahme-Andockstelle verwenden.
3. **Gewährleistungsbeginn** wird nirgends aus einer Abnahme abgeleitet; Warranty-Felder liegen verstreut (CustomerProductInfo/MaintenanceAsset/Problem) — keine eine Wahrheit.
4. **Abnahme→Rechnung** ist Medienbruch (K4/H-A6) — und mangels Abnahme-Ereignis fehlt sogar der Trigger; die Wartungs-Protokoll-Infra ist reich gebaut, aber leer (Protokolle=0, Verträge=10).
5. **Automatisierungs-Reife des Bereichs: Grad 0–1** (niedrigster im CRM) — aber günstiger Hebel, weil Protokoll-Muster (MaintenanceProtocol) und Vorlagen-Kategorie (`acceptance`) bereits im Haus sind: Bau = Wiederverwendung + Verdrahtung, additiv.
