# CRM-Inventur 04 — Auftrag: Dokumente & Bestätigung

> Zone 04 von einer arbeitsteiligen Inventur. **Nur Lesen/Analyse**, keine Bewertung des Codes.
> Glossar: **Auftrag = `deals`**, **Angebot = `offers`**, **Kunde = `new_leads`**.

## Abgrenzung & Einordnung

Diese Zone beschreibt die **dokumentbezogenen** Funktionen rund um den Auftrag (`deals`):
Datei-Upload/-Download am Auftrag, Auftrags-Notizen, sowie die Frage nach einer
Auftragsbestätigungs-PDF.

**Grenzen:**
- **NICHT hier — Zone 03 (Angebot/Set-Konfiguration):** Angebots-PDF-Export
  (`offer/pdf_export.blade.php`), Set-/Angebotskonfiguration, Angebotsordner-Erzeugung.
  Der Auftrag *referenziert* zwar Angebot/Ordner (`deals.offer_id`, `deals.offer_folder_id`,
  Migration `2026_04_02_073002`), die Erzeugung dieser Angebotsartefakte liegt aber in 03.
- **NICHT hier — Status-/Storno-/Rechnungs-STATUS-Fluss:** liegt in `docs/workflow-analyse.md`.
  Zone 04 fasst nur die **Dokument-/Bestätigungs-Funktionen** an (die Datei-Kategorie
  „Auftragsbestätigung", das Dokument-Panel, Notizen).
- **Randbereich Rechnung:** `deal_invoices` als Dokument (PDF-Rechnung) — hier nur kurz
  eingeordnet; der Rechnungs-*Status* gehört zum Workflow (Zone Status).

### Kernbefund vorab (verifiziert)

1. **Es gibt KEINE dedizierte Auftragsbestätigungs-PDF-Generierung.** „Auftragsbestätigung"
   ist ausschließlich ein Kategorie-/Filterwert `confirmed_order` in einem Datei-Dropdown
   (`deal/partials/gallary.blade.php:6`, `deal/customer_view.blade.php:2761`). Es wird kein
   eigenes Dokument gerendert — es ist eine **Ablage-Kategorie** für hochgeladene Dateien.
2. **`dompdf` wird für den Auftrag NICHT genutzt.** PDF-Erzeugung existiert nur in Zone 03
   (`offer/pdf_export.blade.php`), beim Lieferschein (`product/delivery/pdf.blade.php`) und
   im Tagesbericht (`DailyReportController`). Der Deal-Bereich hat nur `Storage::download()`.
3. **`DealNoteController` ist ein leeres Resource-Gerüst** (alle Methoden `//`,
   `2026_04_02`). Die echte Notiz-Logik läuft über `DealController::profileStoreNote` und
   `CustomerContextFeedController::storeDealNote`.

Fazit für die ganze Zone: **wenig eigenständige Dokument-*Erzeugung*, überwiegend
Datei-*Ablage* (Upload/Download/Rename/Delete) + Auftrags-Notizen.**

---

## Unterbereich 1 — Auftrags-Dateien (Upload / Download / Verwaltung)

- **(a) Zweck:** Beliebige Dateien (jpg/png/pdf/doc/xls, max 20 MB) am Auftrag ablegen,
  umbenennen, herunterladen, löschen. Kategorisierung über Datei-Status/Stufe
  (`order` = Kundenauftrag, `confirmed_order` = Auftragsbestätigung, `offer`, `offer_folder`).
  Dateien werden **nicht** in einer eigenen Deal-Dokumenttabelle geführt, sondern in der
  generischen `images`-Tabelle (Status `order`/`deal`/`deal_document`) bzw. als
  Angebotsordner-Anhang (`offer_folder_attachments`, Quelle `attachment`).
- **(b) Controller/Routen:** `DealController` (`app/Http/Controllers/Customer/Deal/`).
  - `POST /customer_upload` → `uploadCustomerFile` (web.php:4304), speichert nach
    `uploads/customers`, legt `Image`-Zeile mit `status='order'` an.
  - `GET /deal/load-customer-files` → `loadCustomerFiles` (:4307)
  - `POST /deal/rename-file` → `renameCustomerFile` (:4310)
  - `POST /deal/delete-file` → `deleteCustomerFile` (:4313)
  - `GET /deal/file/{source}/preview/{id}` → `previewCustomerFile` (:4316)
  - `GET /deal/file/{source}/download/{id}` → `downloadCustomerFile` (:4319, `Storage::download`)
  - Profil-Variante: `POST /deal/{deal}/profile/documents/upload` → `profileUploadDocument`
    (:4216, Status `deal`/`deal_document`, Stage „Auftrag Profil");
    `DELETE .../documents/{source}/{id}` → `profileDeleteDocument` (:4219).
  - View: `deal/partials/gallary.blade.php` (Galerie + Stufen-Filter), Profil-Tab
    „Dokumente" in `deal/profile.blade.php:1850` (`dpDocumentUploadForm`).
- **(c) Kern-Tabellen:** `images` (generisch, Filter über `customer_id`/`alternative_id`/
  `article_group`/`status`), `offer_folder_attachments` (Quelle `attachment`, teils externe
  `file_url`). **Keine** eigene `deal_documents`-Tabelle.
- **(d) Größe:** groß (Upload/Download/Preview/Rename/Delete + Profil-Doppel-Weg über
  `DealController.php`, ~3974 Zeilen gesamt; die Datei-Methoden ~Z. 1630–2013 + 2932–3060).
- **(e) Status:** aktiv, breit ausgebaut, funktional. Auffällig: keine dedizierte
  Deal-Dokumenttabelle — Dokumente hängen indirekt über Kunde/Angebotsordner am Auftrag,
  nicht per FK am `deal`. **Zu verifizieren in Detail-Inventur.**

---

## Unterbereich 2 — „Auftragsbestätigung" (`confirmed_order`)

- **(a) Zweck:** Eine **Datei-Kategorie**, kein generiertes Dokument. Nutzer laden eine
  Auftragsbestätigung (z. B. unterschriebenes PDF) hoch und filtern sie in der Galerie
  über den Wert `confirmed_order`.
- **(b) Controller/Routen:** keine eigenen. Reine Frontend-Kategorie im Datei-Dropdown:
  `deal/partials/gallary.blade.php:6`, `deal/customer_view.blade.php:2761`. Upload/Download
  laufen über Unterbereich 1. Datenfeld: `deals.confirmed_at` (Migration deals, Z. 30,
  „Bestätigt am") — reines Datumsfeld, keine Dokument-Erzeugung daran gekoppelt.
- **(c) Kern-Tabellen:** `deals` (`confirmed_at`, `order_number`, `offer_number`),
  `images` (Kategorie/Status). Keine eigene Tabelle.
- **(d) Größe:** sehr klein (2 Dropdown-Zeilen + 1 Datumsfeld).
- **(e) Status:** **dünn.** Es existiert **keine** eigenständige Auftragsbestätigungs-PDF.
  Die HINWEIS-Vermutung aus dem Auftrag ist bestätigt: „Auftragsbestätigung" = Ablage-Label,
  kein gedrucktes eigenes Dokument und kein gedruckte Deal-Blade.

---

## Unterbereich 3 — Auftrags-Notizen (`deal_notes`)

- **(a) Zweck:** Freitext-Notizen am Auftrag (mit Threading via `parent_id`, Soft-Deletes).
  Neu eingeführt mit Migration `2026_04_02_055610`.
- **(b) Controller/Routen:**
  - **`DealNoteController` ist leer** (`app/Http/Controllers/DealNoteController.php`, alle
    7 Resource-Methoden nur `//`). Nicht in `routes/web.php` an Routen gebunden.
  - Echte Erzeugung:
    `POST /deal/{deal}/profile/notes` → `DealController::profileStoreNote` (web.php:4213,
    schreibt `DealNote`) und `POST /deal/{deal}/note` →
    `CustomerContextFeedController::storeDealNote` (web.php:1204, mit `parent_id`/Threading).
  - Anzeige: Notizen-Tab in `deal/profile.blade.php:1867` (`dpNoteForm`, `note-item.blade.php`,
    `notes-list.blade.php`).
  - Daneben existiert ein **separater**, generischer `CustomerNoteController` (customer-notes,
    web.php:1327 ff.) — Kunden-Notizen, **nicht** `deal_notes`; nicht Teil dieser Zone.
- **(c) Kern-Tabellen:** `deal_notes` (`deal_id` FK→deals cascade, `parent_id` FK→deal_notes,
  `customer_id`/`alternative_id`/`product_id`, `description` longText, `created_by`/`updated_by`,
  softDeletes). Model `app/Models/DealNote.php` (46 Z.).
- **(d) Größe:** klein–mittel (Model + Migration + 2 Store-Methoden + Profil-View-Tab).
- **(e) Status:** aktiv genutzt, aber **inkonsistent verkabelt** — dedizierter
  `DealNoteController` existiert als leeres Gerüst, während die Logik in zwei anderen
  Controllern liegt. **Zu verifizieren / aufräumen-Kandidat.**

---

## Unterbereich 4 — Auftrags-Rechnung als Dokument (`deal_invoices`) — Randbereich

- **(a) Zweck:** Rechnungen am Auftrag (Dokumentseite). Hier nur zur Abgrenzung — der
  Rechnungs-*Status-Fluss* gehört in den Workflow, nicht in Zone 04.
- **(b) Controller/Routen:** `DealInvoiceController` (214 Z.):
  `GET /deal/invoices` (web.php:4345), `POST /deal/invoices/store` (:4348),
  Resource-Methoden `create/store/show/edit/update/destroy`.
  Views: `deal/invoice/invoice.blade.php` (+ partials kanban/list).
- **(c) Kern-Tabellen:** `deal_invoices` (Migration `2025_06_23_053704`), Verknüpfung an Deal
  über `2026_06_09_123236_add_deal_link_and_history_to_invoices`.
- **(d) Größe:** mittel (Controller 214 Z. + Views).
- **(e) Status:** aktiv. **Grenzfall** — hier nur als Dokument erwähnt; Details/Status
  bewusst nicht vertieft (gehört in Status/Workflow-Zone). **Eigene Detail-Inventur nötig.**

---

## Unterbereich 5 — Deal-Feinaufmaß-Dokumente (`deal_measurements`, Bilder) — Randbereich

- **(a) Zweck:** Feinaufmaß am Auftrag inkl. Foto-Upload. Nur der **dokumentbezogene** Teil
  (Bild-Upload/-Anzeige) ist für Zone 04 relevant; die eigentliche Aufmaß-/Material-Logik
  ist ein eigener großer Komplex (nicht dokumentbezogen → eigene Zone).
- **(b) Controller/Routen:** `DealMeasurementImageController`
  (`index`/`upload`/`destroy`): `GET/POST /deal-measurements/{measurement}/images...`
  (web.php:4467–4473, 4525–4527). Umfeld: `DealMeasurementController` (2413 Z.),
  `DealMeasurementMaterialController`.
- **(c) Kern-Tabellen:** `deal_measurements` (+ `_items`, `_details`, `_histories`;
  Migrationen `2026_04_29`/`2026_04_30`), Bilder in generischer `images`-Tabelle.
- **(d) Größe:** der Aufmaß-Komplex ist groß; der **dokument-**relevante Bild-Teil ist klein.
- **(e) Status:** aktiv. Für Zone 04 nur Bild-Ablage relevant; der Rest ist **außerhalb**
  dieser Zone und braucht eine **eigene Detail-Inventur** (Feinaufmaß).

---

## Braucht eigene Detail-Inventur

- **Datei-Ablage-Architektur (hohe Priorität):** Warum keine `deal_documents`-Tabelle?
  Dokumente hängen über `images.customer_id/status` + `offer_folder_attachments` am Auftrag,
  nicht per FK am `deal`. Zuordnung/Isolation je Auftrag genauer prüfen
  (`getDealFileCount`, `profileImagesQuery`/`profileAttachmentsQuery`).
- **`DealNoteController` (Aufräum-Kandidat):** leeres Gerüst vs. Logik in `DealController` +
  `CustomerContextFeedController`; Threading nur über einen der beiden Pfade.
- **`deal_invoices` (Rechnungs-Dokument):** eigene Inventur, in Abstimmung mit der
  Status-/Workflow-Zone (Grenze Dokument ↔ Status).
- **`deal_measurements` (Feinaufmaß):** eigener großer Komplex; hier nur Bild-Ablage berührt.
- **Bestätigen: keine Auftragsbestätigungs-PDF** — falls fachlich gewünscht, ist das eine
  **Lücke/To-do**, kein bestehendes Feature.

## Belege

- Routen: `routes/web.php:1204` (deal.note), `:4213` (profile/notes),
  `:4216/4219` (profile/documents), `:4304–4325` (customer_upload / file preview/download),
  `:4345–4348` (deal/invoices), `:4467–4473` (measurement images).
- `app/Http/Controllers/Customer/Deal/DealController.php:1630` (`uploadCustomerFile`),
  `:1972` (`downloadCustomerFile`, `Storage::download`), `:2894` (`profileStoreNote`,
  schreibt `DealNote`), `:2932` (`profileUploadDocument`, images-Status `deal`/`deal_document`).
- `app/Http/Controllers/DealNoteController.php:13–64` (leeres Resource-Gerüst).
- `app/Http/Controllers/Customer/CustomerContextFeedController.php:420` (`storeDealNote`).
- `app/Http/Controllers/Customer/Deal/DealInvoiceController.php:120` (`store`).
- Views: `resources/views/admin/deal/partials/gallary.blade.php:6` (`confirmed_order` =
  Auftragsbestätigung), `resources/views/admin/deal/customer_view.blade.php:2761`,
  `resources/views/admin/deal/profile.blade.php:1850` (Dokument-Upload-Form),
  `:1867` (Notizen-Tab).
- Migrationen: `2025_02_05_125814_create_deals_table.php` (Z. 28–31: `order_number`,
  `offer_number`, `confirmed_at`), `2026_04_02_055610_create_deal_notes_table.php`,
  `2026_04_02_073002_add_offer_relations_to_deals_table.php` (`offer_id`, `offer_folder_id`),
  `2025_06_23_053704_create_deal_invoices_table.php`,
  `2026_06_09_123236_add_deal_link_and_history_to_invoices.php`.
- Negativbeleg PDF: `grep dompdf/Pdf::load` trifft nur `offer/pdf_export.blade.php` (Zone 03),
  `product/delivery/pdf.blade.php`, `DailyReportController.php:3756` — **nichts** für Deal/Auftrag.
