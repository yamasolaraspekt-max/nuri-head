# CRM-Inventur — Zone 07: Medien / Kommunikation / Rest (Auffang-Zone)

**Stand:** 2026-07-01
**Agent:** Inventur-Agent Zone 07 (Auffang-Agent) — NUR Lesen/Analyse
**Auftrag:** Alles, was NICHT in Zone 1–6 (Kernprozess Lead→Angebot→Auftrag→Rechnung, Kundenprofil, Cockpit/Controlling, Mobile/Nuriva, Kalender) und NICHT in Legacy (Zone 08) gehört.
**Methode:** Breite vor Tiefe. Quellen: `routes/web.php` (5434 Z.), `routes/api.php` (365 Z.), `routes/channels.php`, `app/Http/Controllers/*`, Sidebar `resources/views/admin/layouts/sidebar.blade.php`, Migrationen `database/migrations/*`.
**Glossar:** Kunde = `new_leads`. In diesem Projekt: `users.name` speichert die `employees.id` (kein echter Name).

**Datenlage-Hinweis:** Lokale Dev-DB ist nahezu leer (siehe Memory „users table was empty"). Fast alle Zone-Tabellen haben 0 Datensätze; Ausnahmen: `chats`=10, `chat_groups`=1, `user_dashboard_settings`=2. Feld „(d) Größe" bewertet daher **Code-Umfang / Reifegrad**, nicht Datenmenge.

**Referenzen (bereits kartiert, hier NUR verwiesen, nicht wiederholt):** `docs/workflow-analyse.md` (Kernprozess), `docs/kundenprofil-*` (Kundenprofil), `docs/cockpit-inventur.md` + `docs/controlling-bestandsaufnahme.md` (Auswertungen), `docs/nuriva-sync-anbindung-befund.md` (Mobile — nutzt u.a. `images`-Tabelle), `docs/kalender-termine-bestandsaufnahme.md` (Kalender).

---

## A. Dokumente / Medien / Bilder / Galerien

### A1. Zentrale Bild-/Datei-Ablage (`images`)
- **(a) Zweck:** Zentrale Upload-Ablage für Kundenbilder/Dokumente je Kunde+Alternative+Stage; auch von Mobile/Nuriva befüllt (Foto-Upload). Verwaltet Screenshots, Roof-Layout-Bilder, PDF/DOC-Anhänge.
- **(b) Controller/Routen:** `Customer/ImageController` (`uploads`, `store`, `uploadScreenshot`, `saveScreenshot`, `loadScreenshot`, `deleteScreenshot`) — Routen ~`web.php:1415-1426`; `POST /images`, `/upload-screenshot`, `/save-screenshot`, `/load-images/{alternativeId}`, `/delete-screenshot`.
- **(c) Kern-Tabellen:** `images` (24-Spalten-Migration, `alternative_id` seit 2026-06-26 nullable für Mobile-Fotos), Verknüpfung zu `new_leads`, `lead_alternative_adds`, `offer_folder_attachments`.
- **(d) Größe:** Groß / zentral, breit referenziert. 0 Datensätze lokal.
- **(e) Status:** **aktiv** (geteilt mit Mobile/Nuriva — dort tiefer kartiert).

### A2. Bild-Kategorien (`image_categories`)
- **(a) Zweck:** Kategorisierung von Bildern.
- **(b) Controller/Routen:** `ImageCategoryController` — Methoden nur Scaffold (`index`/`store`/… leer/`//`).
- **(c) Kern-Tabellen:** `image_categories`.
- **(d) Größe:** winzig.
- **(e) Status:** **tot / Scaffold** (leere Methodenrümpfe).

### A3. Produkt-Dokumente & Produkt-Bilder (Medien-Seite)
- **(a) Zweck:** Datenblätter/Dokumente und Bild-Galerie je Produkt (Produktkatalog-Medien).
- **(b) Controller/Routen:** `Product/ProductDocumentsController` (`create`/`store`/`update`/`destroy`/`upload`/`list`/`delete`/`updateName`) `web.php:2348-2428`; `Product/ProductImageController` + `Product/ProductImageCsvImportController`; Galerie `GET /products/gallery` → `ProductController@getImage` (`web.php:3550`).
- **(c) Kern-Tabellen:** `product_documents`, `product_images`.
- **(d) Größe:** mittel (auth-geschützt, funktional).
- **(e) Status:** **aktiv** (Produkt-Randbereich; Kern-Produktkatalog ist eigene Zone).

### A4. Weitere bild-/anhang-bezogene Modelle (nur benennen)
- `new_lead_images`, `problem_images`, `delivery_note_images`, `ticket_images`, `feedback_images`, `add_image_to_sets` — je eigene Migration; gehören funktional zu Lead/Ticket/Lieferschein/Feedback/Sets (teils andere Zonen). Hier als **Medien-Landkarte** gelistet.
- Angebots-/Aufgaben-Anhänge: `OfferFolderController` Attachments (`web.php:3475-3483`), `PersonalTaskAttachmentController` (`web.php:3656-3659`), `DailyReportAttachmentController` (`web.php:1853-1855`), `Offer roof-layout uploadImage` (`web.php:3306`). → gehören zu Angebot/Task/Report (andere Zonen), Medien-Aspekt hier vermerkt.
- **Status:** überwiegend **aktiv**, funktional an ihre Fachbereiche gebunden.

---

## B. E-Mail / Kommunikation (⚠ NUR KARTIERT — vom Nutzer als heikel markiert, NICHT vertieft)

### B1. Lead-E-Mail-Postfächer / Inbox (IMAP-Reader)
- **(a) Zweck:** IMAP-Postfächer je Lead-E-Mail-Konto abrufen, Inbox anzeigen, Mails als gelesen markieren, Domain-Filter.
- **(b) Controller/Routen:** `Email/LeadEmailAccountsController` (resource + realtime + toggle/test), `Email/LeadEmailReaderController` (`fetchAndStore`, `inbox`, `show`, `markAsRead`), `Email/LeadEmailDomainFilterController` — `web.php:1389-1407`, `1396-1398`. CSV/PDF-Export nur Stub (`fn() => 'TODO'`).
- **(c) Kern-Tabellen:** `lead_email_accounts`, `lead_emails`, `lead_email_domain_filters`, `email_open_events`.
- **(d) Größe:** mittel–groß (IMAP via `Webklex/PHPIMAP`).
- **(e) Status:** **aktiv/teilweise** — Export TODO. **NICHT weiter analysiert (heikel).**

### B2. Alt-E-Mail-Konfiguration & Versand
- **(a) Zweck:** SMTP/IMAP-Konfiguration, E-Mail an Lead senden.
- **(b) Controller/Routen:** `Email/EmailConfigurationController` (`web.php:3251-3259`, u.a. `lead_send_email_view`), `Email/LeadsController` (IMAP-basiert). Nutzt `App\Mail\LeadEmail`.
- **(c) Kern-Tabellen:** `email_configurations`, `send_emails`.
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv/teilweise (unklar, ggf. parallel zu B1).** **NICHT vertieft (heikel).**
- **Hinweis:** IMAP-Anbindung generell — laut Projekt-Memory sind IMAP-Alt-Integrationen als **Legacy** zu behandeln → siehe auch Rubrik „Sonstige" + Zone 08. E-Mail braucht eigene Detail-Inventur (s.u.), hier bewusst nur benannt.

---

## C. Chat / interne Kommunikation

### C1. Mitarbeiter-Chat (Echtzeit) — aktiver Neubau
- **(a) Zweck:** Interner 1:1- und Gruppen-Chat zwischen Mitarbeitern, mit Anhängen, Mentions, Lese-Status, Pinnen, Kunden-/Kontext-Verknüpfung.
- **(b) Controller/Routen:** `Chat/ChatController` (send/fetch/messages/unreadCounts/markRead/mentions/readers/searchCustomers/searchCustomerContexts…), `Chat/ChatGroupController` (Gruppen, Invites, Mitglieder), `Chat/ChatMentionController`, `Chat/ChatAttachmentController`, `Chat/PinnedPrivateChatController` — `web.php:4680-4740`. Broadcast via `routes/channels.php` + Laravel Echo. JS: `resources/js/chat*.js`, `chat-mensions.js`, `mini_chat.js`.
- **(c) Kern-Tabellen:** `chats`, `chat_groups`, `chat_group_user` (+ invites/rights), `chat_reads`, `chat_attachments`, `chat_mentions`, `pinned_private_chats`, `pinned_group_chats`.
- **(d) Größe:** groß (viele Controller, Events, JS). Lokal: `chats`=10, `chat_groups`=1.
- **(e) Status:** **aktiv** (jüngste Migrationen 2025-07 bis 2026-06; in aktiver Entwicklung — mehrere `chat copy*.js`/`.blade copy.php`-Altstände deuten Iteration an).

### C2. Lern-/Tutorial-Bereich (im Chat-Modul, „KI-Wissen")
- **(a) Zweck:** Lern-Themen mit Medien, Zuweisung an Abteilungen/Positionen/Mitarbeiter; im Sidebar als „KI-Wissen" (`admin.chat.learnings.index`).
- **(b) Controller/Routen:** `Chat/Learning/LearningTopicController` (index/list/show/store/destroy/uploadMedia/deleteMedia) `web.php:4753-4770`; `ChatController@learning`/`learningShow`.
- **(c) Kern-Tabellen:** `learning_topics`, `learning_topic_media`, `learning_topic_assignments`.
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv** (neu, Nov 2025).

### C3. News-Feed im Chat (Solar-News)
- **(a) Zweck:** Zieht Solar-News von NewsAPI und postet sie als Systemnachrichten in eine „Solar News"-Chatgruppe.
- **(b) Controller/Routen:** `Chat/Feed/NewsFeedController` (`syncSolarNews`, `index`) `web.php:4764-4765`. Config: `services.newsapi.key/slug`.
- **(c) Kern-Tabellen:** nutzt `chat_groups`/`chats`.
- **(d) Größe:** klein.
- **(e) Status:** **teilweise/aktiv** (abhängig von `NEWSAPI_KEY`; ohne Key inaktiv).

---

## D. KI / AI-Assistent (Kunden-Chat mit LLM)

### D1. AI-Chat je Kunde (Ollama-basiert)
- **(a) Zweck:** LLM-Chat mit Kundenkontext (CustomerContextBuilder, Wetter, Norm-Temp, Dachfläche, Embeddings/Memory). Streaming-Antworten (SSE). Teilen per Share-Token.
- **(b) Controller/Routen:** `Ai/ChatPageController` (index/show/search/byCustomerIds), `Ai/AiMessageController` (`createChat`, `ask` — SSE-Stream), `Ai/ShareController` (`toggleShare`, öffentliche Share-Seite) `web.php:4774-4777`. Services: `OllamaClient`, `PromptFactory`, `CustomerContextBuilder`, `EmbeddingClient`, `ConversationMemory`, `WeatherClient`, `NormTempService`, `RoofAreaEstimator`. Zugang zusätzlich über `ChatController@indexApi` (liest `ai_chats`).
- **(c) Kern-Tabellen:** `ai_chats`, `ai_messages`, `ai_chat_participants` (Verknüpft an `new_leads` via `customer_id`).
- **(d) Größe:** groß (Service-Landschaft, Streaming, Auth/Gate).
- **(e) Status:** **aktiv/experimentell** (0 Datensätze lokal; hängt an lokalem Ollama). ⚠ LLM-Bereich — bei Vertiefung eigene Inventur nötig. **Hinweis:** eigenes lokales Ollama, nicht Anthropic-/Claude-API.
- **Alt-Referenz:** `resources/views/admin/AiChat.php` wurde gelöscht (git status: `D`); aktive Views liegen unter `resources/views/ai/`.

---

## E. Benachrichtigungen / Notifications

### E1. System-Benachrichtigungen (Laravel Notifications + Echtzeit)
- **(a) Zweck:** In-App-Benachrichtigungen (Termin/Task/Kunde/Lead/Projekt/Ticket-Scopes), Echtzeit via Laravel Echo, lesen/als-gelesen-markieren.
- **(b) Controller/Routen:** `Notification/NotificationController` (`index`, `markAsRead`) `web.php:1640-1643`; `Notification/NotificationListController` (Mitarbeiter-Benachrichtigungen, Urlaubs-/Leave-Flows) `web.php:1648-1676`; diverse `getTaskNotifications`/`markAsRead` in `NewLeadsController`, `InquiryController`, `PersonalTaskController`, `MainAppointmentController`, `OverdueCenterController` (verstreut). JS: `resources/js/notification.js` (Echo), `user-notifications.js`. View `resources/views/admin/layouts/notification.blade.php`.
- **(c) Kern-Tabellen:** `notifications` (Standard-Laravel `notifications`-Tabelle, morph zu `notifiable`).
- **(d) Größe:** groß, aber **stark verteilt** (kein einheitlicher Ort — jeder Fachcontroller hat eigene Notification-Endpunkte).
- **(e) Status:** **aktiv** (0 lokal). Fragmentiert → Kandidat für eigene Detail-Inventur.

### E2. Termin-/Kanban-Reminder & Overdue-Center (Grenzfall zu Kalender/Cockpit)
- **(a) Zweck:** Fällige Erinnerungen für Termine, Kanban-Karten, Reports.
- **(b) Controller/Routen:** `MainAppointmentReminderController` (`web.php:1241-1243`), `LeadReminderController` (Kanban-Reminder, `web.php:1115-1120`), `OverdueCenterController` (Report-Notifications, `web.php:646-651`).
- **(c) Kern-Tabellen:** diverse Reminder-Tabellen (nicht vertieft).
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv** — **Grenzfall**: überlappt mit Kalender (`docs/kalender-termine-bestandsaufnahme.md`) und Cockpit/Controlling. Hier nur verwiesen.

---

## F. Einstellungen / System-Konfiguration / Tools (Sidebar-Sektionen „Konfiguration/Tools/System/Einstellungen")

### F1. Systemwarnung (globales Wartungs-/Status-Banner)
- **(a) Zweck:** Globales System-Banner (Entwicklung/Upload/Fix/Wartung) mit Theme/Text; Historie; Echtzeit-Push.
- **(b) Controller/Routen:** `Admin/SystemWarningController` (`index`/`current`/`update`/`toggle`) `web.php:439,484-486`. Event `SystemWarningUpdated`.
- **(c) Kern-Tabellen:** `system_warnings`, `system_warning_histories`.
- **(d) Größe:** klein–mittel.
- **(e) Status:** **aktiv** (Sidebar „System").

### F2. Datenbankbereinigung (Garbage Collector)
- **(a) Zweck:** Soft-deleted Datensätze vorschau/löschen (Tabelle/bulk/all), Permission-gated (Administrator + `is_delete`).
- **(b) Controller/Routen:** `Admin/GarbageController` (`index`/`deleteTable`/`bulkDelete`/`deleteAll`) `web.php:489-492`. Service `SoftDeletedGarbageCollector`.
- **(c) Kern-Tabellen:** operiert über alle Soft-Delete-Tabellen.
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv** (Sidebar „System").

### F3. Feedback (interne Rückmeldungen mit Upload)
- **(a) Zweck:** Internes Feedback-/Bug-Meldesystem mit Bild-Upload, Status, Antworten.
- **(b) Controller/Routen:** `System/FeedbackController` (`index`/`list`/`store`/`update`/`changeStatus`/`upload`/`destroy`) `web.php:3998-4005`.
- **(c) Kern-Tabellen:** `feedback`, `feedback_images`, `project_feedback`.
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv** (Sidebar „System"). Hinweis: separater leerer `FeedbackImageController` (Scaffold, tot) existiert daneben.

### F4. Wissensdatenbank (Knowledge Base)
- **(a) Zweck:** Kategorien + Fragen/Antworten als interne Wissensbasis (mit Foto-Upload nach `images/knowledge/`).
- **(b) Controller/Routen:** `KnowledgeCategoryController` (resource + `question`/`search`), `KnowledgeQuestionController` (create/store/update/get/edit/destroy) `web.php:4557-4566`. Sidebar „Wissensdatenbank" (`knowledge.base`).
- **(c) Kern-Tabellen:** `knowledge_categories`, `knowledge_questions`.
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv** (eigene Sidebar-Sektion „Wissen").

### F5. Werkzeuge / PV-Tools / PVGIS / Wetter (Sidebar „Tools")
- **(a) Zweck:** Externe Datentools — PVGIS-Ertragsdaten, Wetter/Temperatur, PV-Planer per PLZ (Google Geocoding → PVGIS).
- **(b) Controller/Routen:** `ToolsController` (`index`=`tools.view`, `fetchPvgis`, `fetchWeatherData`, `weatherman`) `web.php:3623-3629`; `PVToolsController` (`admin.pvgis.index`, `fetchByPostcode`, `getPVData`) `web.php:3627,3630-3631`; Klimadaten-Import/Wetterstation `Customer/Climate/WeatherStationController` (`weather_station`, `web.php:4550-4551`). Nutzt `GOOGLE_MAPS_KEY`, PVGIS-API (`re.jrc.ec.europa.eu`), Job `ProcessWeatherData`.
- **(c) Kern-Tabellen:** `temperatures`, `weather_stations`, `climate_locations`, `climate_stations`, `climate_monthly_data`, `climate_solar_monthly_data`, `climate_evaluation_rows`.
- **(d) Größe:** mittel–groß (viel Klima-/Wetter-Infrastruktur).
- **(e) Status:** **aktiv/teilweise** (Klima-Import neu Feb 2026; PVGIS aktiv). Grenzfall zu Angebots-Wirtschaftlichkeit — dort teils referenziert.

### F6. Konfiguration / Kalkulationssätze / Filialen / Struktur (Sidebar „Konfiguration"/„Einstellungen")
- **(a) Zweck:** Stammdaten-Konfiguration: Arbeitsschritte/Phasen (`task_phase.index`), Projekt-Struktur/Stages (`stages.index`), Filialen (`branch.info`), Kalkulationssätze (`admin.costing_sets.index` unter `admin/settings`).
- **(b) Controller/Routen:** `CostingSetController` + `CostingSetRoleController` (`web.php:1482-1493` `admin/settings`-Gruppe); Filialen `Branch/*`; Phasen/Stages `Phase/*`.
- **(c) Kern-Tabellen:** `costing_sets` (+ roles), `stages`, `task_phases`, `branches`.
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv** — Grenzfall: Costing/Stages/Branch berühren Kernprozess/Auftrag (siehe `docs/workflow-analyse.md`). Hier nur als Konfig-Menü verortet.

### F7. Persönliche Einstellungen / Dashboard-Layout / Notizen
- **(a) Zweck:** Persönliche Kalender-/User-Einstellungen, Reihenfolge der Dashboard-Icons, persönliche Notizen + Reminder.
- **(b) Controller/Routen:** `PersonalSettingsController` (`calendar-settings` get/save, `web.php:855-856`); `DashboardIconController` (`saveOrder`, `web.php:683`); `AdminPersonalNoteController`/`PersonalNoteController` (`personal-notes/*`, `web.php:4611-4616`); `Employee/Note/PersonalNoteReminderController` (`getDueReminders`).
- **(c) Kern-Tabellen:** `personal_settings`, `user_dashboard_settings` (=2 lokal), `dashboard_icons`, `personal_notes` (+ indexes), `note_categories`.
- **(d) Größe:** mittel.
- **(e) Status:** **aktiv**. Grenzfall zu Dashboard/Cockpit (`docs/dashboard-konzept.md`, `docs/cockpit-inventur.md`).

---

## G. Breaking News (Ticker/Ansage mit Audio)

- **(a) Zweck:** Admin-Ansagen/Breaking-News-Banner mit Typ (info/warning/danger/success), Icon, Zeitfenster, optional **Audio-Aufnahme** (webm/mp3/…).
- **(b) Controller/Routen:** `BreakingNews/BreakingNewsController` (index/store/update/destroy, Audio-Handling) — Routen um `web.php` (`breaking-news`), View `resources/views/admin/breaking-news/index.blade.php`.
- **(c) Kern-Tabellen:** `breaking_news` (+ `add_audio_to_breaking_news`).
- **(d) Größe:** klein–mittel.
- **(e) Status:** **aktiv** (Dez 2025, Audio-Feature).

---

## H. Kontakte / Adressbuch (übergreifend)

- **(a) Zweck:** Globales Adressbuch über alle Entitäten (Kunden `new_leads`, Marken `brands`, Distributoren, Mitarbeiter, Ansprechpartner); globale Suche + CSV-Export + globale Restore-Aktionen.
- **(b) Controller/Routen:** `Contacts/AllContactController` (`index`=`all.contacts`, `export`, `globalSearch`, `customer`/`brand`/`distributor` restore) `web.php:4631-4637`; Ansprechpartner `CustomerContactPersonController` (`contact-people/*`, `web.php:1373-1386`). Sidebar `all.contacts` (Z.627).
- **(c) Kern-Tabellen:** `new_leads`, `brands`, `distributors`, `employees`, `customer_contact_people`.
- **(d) Größe:** mittel–groß (aggregiert viele Quellen).
- **(e) Status:** **aktiv**. Grenzfall: baut auf Kunden-/Marken-Stammdaten (Zone 1/Produkt) auf; hier als eigenständige „Adressbuch"-Seite verortet.

---

## I. Kleinere aktive Bereiche (Rest, korrekt zuordenbar)

- **QR-Codes:** `QrCodeController` (`qr.code`/`qr.details`/`qr.print`/`qr.destroy`, `web.php:2673-2676`) — Generierung/Druck. **aktiv**, klein. Tabelle `qr_codes`.
- **Bitrix-Kontakt-Sync (Adressbuch-Altbestand):** `BitrixController@contact_list` liefert hartkodiertes JSON. → **Legacy** (s. Rubrik Sonstige).

---

## Sonstige / unklare Bereiche (Auffangliste — damit nichts verloren geht)

Alles Folgende habe ich in Routen/Menüs gesehen und konnte es nicht zweifelsfrei einer Zone zuordnen (oder es ist Legacy):

1. **Fusion-Formulare / Webhook (Goneo)** — `FusionFormSubmissionController` + `FusionWebhookController` (`web.php:600,660-675`; `api.php:82-86`). Import externer Web-Formular-Leads in Inquiries. **Aktiver Lead-Zulauf** → gehört vermutlich zu **Zone 1 (Lead-Erfassung)**, nicht meine Zone. Hinweis: doppelte Route `/fusion/webhook/ajax` (zwei Controller, `web.php:671`+`672`) — Route-Kollision/Bug.
2. **`MessageController` + `bitrix_chats`/`messages`-Tabellen** (`web.php:4534-4537`, `chats/{user}`, `dispatch-chat-jobs`, `chat-jobs`) — zieht Chats aus **Bitrix24-REST** (`solaraspekt.bitrix24.de`) per Jobs. **→ LEGACY-Integration (Bitrix), NICHT fixen, Verweis Zone 08.** Achtung: Sidebar `chats.view` (Z.498) zeigt auf diesen alten `MessageController@index` — evtl. veralteter Menülink neben neuem Chat (C1).
3. **`BitrixController` / `BitrixChatController`** (`web.php` Bitrix-Kontaktliste, `BitrixChat`-Model) — **→ LEGACY (Bitrix), Zone 08.** BitrixChatController ist leeres Scaffold.
4. **E-Mail-IMAP-Anbindung generell (B1/B2)** — laut Memory sind IMAP-Alt-Integrationen Legacy-nah; zusätzlich vom Nutzer als heikel markiert. Status offen: teils aktiver Neubau (`lead_emails`), teils Alt (`email_configurations`/`send_emails`). **Braucht eigene, vorsichtige Detail-Inventur.**
5. **`FeedbackImageController`** (Root-Namespace) — komplett leeres Scaffold (`//`-Rümpfe). **tot.** (Nicht verwechseln mit aktivem `System/FeedbackController` + `feedback_images`.)
6. **`ImageCategoryController`** — leeres Scaffold. **tot.**
7. **`emergency_contacts`-Tabelle** (Migration 2023) — kein sichtbarer aktiver Controller gefunden. Zuordnung unklar (evtl. Mitarbeiter-Notfallkontakt). **unklar.**
8. **`ChecklistApartment/Assemble/EndTask/Room`-, `BuildingType`-, `HeatingType`-Controller** — Stammdaten/Checklisten. Gehören zu Objekt-/Checklisten-Zonen (nicht meine). Hier nur vermerkt.
9. **`WebsiteController@getEmailDetails`** (`web.php:601`, `lead/email/api/{id}`) — WordPress/Website-nahe E-Mail-Detail. Grenzfall E-Mail/Website. **unklar.**
10. **`KnowledgeQuestionController` Foto-Delete via `unlink('images/knowledge/…')`** — direkte Dateisystem-Löschung ohne Storage-Abstraktion. Hinweis für spätere Detail-Inventur (Robustheit).
11. **Menü-Dubletten Chat:** Sidebar-Link `chats.view`→alter `MessageController` (Bitrix) vs. neuer `Chat/ChatController` (`admin/chat`). Welcher ist der „echte" Menüpunkt? **Klärungsbedarf.**
12. **Viele `*.blade copy.php` / `chat copy*.js` / gelöschte Alt-Views** (git status zeigt zahlreiche `D`) — Iterations-Altstände im Chat/Kalender/AiChat-Bereich. Nicht funktional, aber Aufräum-Kandidaten.

---

## Braucht eigene Detail-Inventur

Reihenfolge nach Nutzen/Risiko:

1. **Benachrichtigungen (E1)** — stark fragmentiert über viele Fachcontroller, kein einheitliches Modell. Hoher Konsolidierungs-Nutzen.
2. **E-Mail-Gesamtbild (B1+B2)** — heikel + Legacy-nah; sauber trennen: aktiver Lead-Inbox-Neubau vs. Alt-SMTP/IMAP. **Vorsichtig, mit Yama abstimmen.**
3. **AI-Assistent (D1)** — Service-Landschaft (Ollama/Embeddings/Memory), experimentell; klären ob produktiv geplant.
4. **Chat-Modul (C1–C3)** — groß, aktiv, aber mit Alt-Menülink (Bitrix) + JS-Dubletten; Menü/Route entwirren.
5. **Tools/Klima/Wetter (F5)** — viele Klima-Tabellen (Feb 2026); Grenze zu Angebots-Wirtschaftlichkeit prüfen.

---

## Belege (geprüfte Quellen)

- **Routen:** `routes/web.php` (Z.439, 484-492, 600-675, 683, 855-856, 1115-1120, 1241-1243, 1373-1386, 1389-1407, 1415-1426, 1482-1493, 1640-1676, 2348-2428, 2673-2676, 3251-3259, 3550, 3623-3631, 3998-4005, 4534-4537, 4557-4566, 4611-4616, 4631-4637, 4680-4777); `routes/api.php` (Z.70-86); `routes/channels.php` (Broadcast/Echo, `users.name`=employee.id).
- **Controller:** `Chat/{ChatController,ChatGroupController,ChatMentionController,ChatAttachmentController,PinnedPrivateChatController,Learning/LearningTopicController,Feed/NewsFeedController}`, `Notification/{NotificationController,NotificationListController}`, `Ai/{ChatPageController,AiMessageController,ShareController}`, `BreakingNews/BreakingNewsController`, `Contacts/AllContactController`, `System/FeedbackController`, `Admin/{SystemWarningController,GarbageController}`, `ToolsController`, `PVToolsController`, `Customer/{ImageController,Climate/WeatherStationController}`, `Product/{ProductDocumentsController,ProductImageController}`, `ImageCategoryController`(tot), `FeedbackImageController`(tot), `DashboardIconController`, `KnowledgeCategoryController`, `KnowledgeQuestionController`, `AdminPersonalNoteController`, `Employee/Note/PersonalNoteReminderController`, `QrCodeController`, `MessageController`(Bitrix/Legacy), `BitrixController`/`BitrixChatController`(Legacy), `Email/*`(heikel, nur benannt).
- **Sidebar:** `resources/views/admin/layouts/sidebar.blade.php` (Z.498, 627, 1319-1352 — Sektionen Konfiguration/Tools/System/Einstellungen/Wissen).
- **JS:** `resources/js/{notification.js,chat.js,chat-mensions.js,mini_chat.js,user-notifications.js,ids-listener.js}`.
- **Migrationen (Tabellen bestätigt):** `images`, `image_categories`, `notifications`, `chats`, `chat_groups`, `chat_group_user`, `chat_reads`, `chat_attachments`, `chat_mentions`, `pinned_private_chats`, `pinned_group_chats`, `ai_chats`, `ai_messages`, `ai_chat_participants`, `breaking_news`(+audio), `feedback`, `feedback_images`, `project_feedback`, `knowledge_categories`, `knowledge_questions`, `learning_topics`(+media,+assignments), `messages`, `bitrix_chats`, `personal_notes`, `personal_settings`, `dashboard_icons`, `user_dashboard_settings`, `lead_email_accounts`, `lead_emails`, `lead_email_domain_filters`, `email_configurations`, `send_emails`, `email_open_events`, `product_images`, `product_documents`, `new_lead_images`, `temperatures`, `weather_stations`, `climate_*`, `customer_contact_people`, `emergency_contacts`.
- **DB-Zählung (tinker, lokal):** fast alle 0; `chats`=10, `chat_groups`=1, `user_dashboard_settings`=2.
