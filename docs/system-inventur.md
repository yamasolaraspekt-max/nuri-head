# System-Inventur — ticket (Kapitel A: Systemlandkarte / Querschnitt)

**Stand:** 2026-07-11 · **Rolle:** Koordinator (read-only) · **Kapitel:** A nach `docs/system-kapitelplan.md`
**Zweck:** Belastbare Orientierungs-Landkarte des vorhandenen Systems — **keine** Optimierung, keine Umsetzung, kein Tiefeneinstieg. Grundlage für Yamas Entscheidung, welches Fachkapitel zuerst tief bearbeitet wird.

**Belegtiefe (Evaluator-Ehrlichkeit):** Diese Karte ist **Karten-Ebene**. Zahlen stammen aus `docs/audit/code-audit.md` (firsthand gemessen, Stand 2026-07-08) und aus eigenen `find`/`grep`/`ls`-Läufen (2026-07-11). Die Zuordnung Controller→Kapitel ist **ordner-/navigations-basiert + gegen das Audit gekreuzt**, kein vollständiger Routen-für-Routen-Trace. Wo etwas nur vermutet ist, steht **(Stichprobe)** oder **(zu verifizieren)**.

**Größenordnung (Audit 1.1):** 387 Controller / **201.821 LOC** · 410 Models / 21.983 · 83 Services / 13.946 · 851 Views / **706.670** · 606 Migrationen · 71 Testdateien. Kern-Verhältnis Controller:Service ≈ **14:1** → Fachlogik lebt überwiegend in Fett-Controllern, die Service-Schicht ist jung und transplantat-gebunden.

---

## 1. Hauptkapitel des Systems (A–N)

Die Kapitel aus `docs/system-kapitelplan.md` sind am Code **plausibel bestätigt**. Ergänzung: mehrere **Quer-/Legacy-Bereiche** (Chat, AI, E-Mail, Bitrix, Nuriva-API, Video/Jitsi, BreakingNews, Wordpress) liegen außerhalb der Prozesskette und werden als **Rand/Legacy** geführt.

| Kap | Domäne | Reife (Audit-Experten) |
|---|---|---|
| A | Systemlandkarte / Querschnitt | — (dieses Dokument) |
| B | Eingang / Lead / Kunde / Objekt | ~3 |
| C | Vertrieb / CRM-Prozess | ~3 |
| D | Angebot / Auslegung / Konfiguration (WP = Unterkapitel) | Rechen ~4 / Verdrahtung ~1 |
| E | Auftrag / Deal / Vertragsübergang | ~3 |
| F | Produkte / Katalog / Sets / Preise | ~2–3 |
| G | Beschaffung / Lager / Material | ~2 |
| H | Planung / Disposition / Ressourcen | ~2 |
| I | Montage / Ausführung / Tagesberichte | ~3 |
| J | Dokumentation / Abnahme / Nachweise | **0–1 (niedrigste)** |
| K | Rechnung / Zahlung / FiBu | Beleg reif / Geld dünn |
| L | Controlling / Nachkalkulation / Auswertung | ~1,5–2 |
| M | Service / Betrieb / Reklamation / Wiederkehr | ~1–2 |
| N | Querschnitt Architektur/Sicherheit/Performance/UX | Prüfbrille |

---

## 2.–6. Module · Controller/Routen · Models/Tabellen · Services · Views je Kapitel

### Kapitel B — Eingang / Lead / Kunde / Objekt
- **Module:** Anfrage-Intake, Lead-Anlage, Kundenakte, Objekt-/Adressdaten, Kontakte, Zuständigkeits-Router.
- **Controller (wichtigste):** `Customer/NewLeadsController` (**14.054 LOC**, 121 public, 267 `DB::table` — Gott-Klasse), `Inquiry/InquiryController` (2.952), `Contacts/*`, `CustomerResponsibleController`, `CustomerStageController`.
- **Models/Tabellen (führend):** `new_leads` (**Kunde**), `lead_alternative_adds` (**Objekt**, God-Table: 193 Sp./1 FK), `lead_product_lists` (**Gewerk**), `inquiries`, `contacts`.
- **Services:** kaum (Alt-Kern ohne Service-Schicht).
- **Views/Einstiege:** „Neue Anfrage / Meine Anfragen / Kundenanfragen / Website-Leads", „Neuer Lead / Leadliste / Kundenakte", „Alle Kontakte".

### Kapitel C — Vertrieb / CRM-Prozess
- **Module:** Lead-Kanban/Stages, Aufgaben/Follow-ups, Termine, Tagesberichte, Arbeitsliste (Inbox).
- **Controller:** `Customer/Kanban/LeadOverviewController` (7.075, typisiert/neu-Stil), `Customer/Kanban/KanbanLeadTaskController`, `Customer/Kanban/LeadStageController`, `Task/PersonalTaskController` (6.570), `Task/PersonalTaskBoardController` (3.152), `Appointment/MainAppointmentController` (3.428), `ArbeitslisteController` (neu), `CustomerReportController`, `Report/DailyReportController`.
- **Models/Tabellen (führend):** `lead_stages` (**Phase**), `lead_stage_sub_stages`, `personal_tasks`, `employees_personal_tasks`, `kanban_lead_tasks`, `main_appointments`.
- **Services:** `FollowUp/FollowUpCreator` (die eine Follow-up-Erzeugung).
- **Views/Einstiege:** „Lead-Kanban", „Meine Aufgaben", „Was jetzt?" (Arbeitsliste), „Mein Kalender / Terminübersicht", „Tagesberichte".

### Kapitel D — Angebot / Auslegung / Konfiguration  *(größte, service-reichste Domäne; WP-Unterkapitel)*
- **Module:** Angebotsordner/OfferDetails/Vorlagen, Produkt-/Set-Auswahl, Kalkulation, **Auslegungen** (WP, PV, Heizlast, Heizkörper, Energie/Grundriss), Angebotsübernahme.
- **Controller:** `Customer/Offer/OfferController` (2.951), `Customer/Offer/OfferFolderController` (3.810), `Customer/Offer/DealMaterialListController` (2.758), `Energie/HeizlastController`, `Energie/EnergieAuslegungController`, `Energie/EnergiekonzeptController`, `Energie/GrundrissController`, `Heizkoerper/*`, `CustomerOfferWPController`, `CostingSetController`, `EconomicAssumptionController`, `Customer/ProfitCalculator/*`.
- **Models/Tabellen (führend):** `offers` (**Angebot**), `offer_details`, `offer_folders`, `master_sets`, `klima_plz`, `heizlast_projekte`, `radiator_specs`/`radiator_installations`, `anforderungsprofil*`.
- **Services (dicht, normbasiert):** `Heizlast/*` (Klima­Bin, Verbrauchs, Heizlast, HeizlastRechner, HeizlastProjekt, **WaermepumpenMatch**, **WpKennlinie**, **Bivalenz**, Jaz, Hoehenkorrektur, Warmwasser, UWert, RaumHuelle, SanierungsWirtschaftlichkeit, Foerderung, Fussbodenheizung, GeometrieAbleitung, Konstanten/Normwerte), `Klima/KlimaPlzService`, `Energie/*` (Pvgis, InverterSizing, PvProjekt, Kosten), `Heizkoerper/*`, `Anforderungsprofil/*`, `Form/*`, `Spec/*`, `Repositories/CatalogDeviceRepository` (**EINE Geräte-Wahrheit**).
- **Views/Einstiege:** „Angebots-Assistent / Übersicht / Vorlagen", „Checklisten-Formulare"; sehr große Konfig-Views (`offer/configuration/offer/config.blade.php` 25.064 Z., stark Inline-JS).

### Kapitel E — Auftrag / Deal / Vertragsübergang
- **Controller:** `Customer/Deal/DealController` (3.952), `Customer/Deal/DealMeasurementController` (2.429), `DealNoteController`.
- **Models/Tabellen (führend):** `deals` (**Auftrag**), `deal_measurements`(+details/items/histories), `deal_notes`. **`deal_invoices` = stillgelegt/dormant** (Doppel-Schiene, s. §11).
- **Views/Einstiege:** „Feinaufmaß-Kanban".

### Kapitel F — Produkte / Katalog / Sets / Preise
- **Controller:** `Product/MasterSet/MasterSetController` (2.464), `ArticleGroup/*`, `Product/Distributor/DistributorController`, `Inventory/*`, `DatanormController`.
- **Models/Tabellen (führend):** `products`/`article_groups` (**EIN Katalog**), `master_sets`(+components/cart), `distributors`/`distributor_prices`/`distributor_product`, `customer_product*`.
- **Services:** `Suppliers/*`, `Import/*`, `Spec/*` (DATANORM/GC-Import).
- **Views/Einstiege:** „Katalog / Favoriten / Stamm-Listen / Preisvergleich", „Master-Sets", „Lieferanten-Schnittstellen / GC Online IDS", „Einheiten / Rabattgruppen / Artikel-Gruppen".

### Kapitel G — Beschaffung / Lager / Material
- **Controller:** `Inventory/*`, `Customer/Offer/DealMaterialListController` (Bestelllisten/Materialbedarf).
- **Models/Tabellen:** Bestelllisten, `distributor_product`, Inventar/Lager. *(Audit: Prozess-Schicht weitgehend leer; Wareneingang bucht keinen Bestand — zu verifizieren im Tiefenkapitel.)*
- **Services:** `Suppliers/*`.

### Kapitel H — Planung / Disposition / Ressourcen
- **Controller:** `Planner/PlannerPlanController` (**11.097 LOC**), `Planner/PlannerEmployeeApiController` (2.375), `Appointment/*`, `Employee/*` (Teams/Qualifikationen).
- **Models/Tabellen:** `planner_*`, `employees` (**mega-gekoppelt: 90 Controller**), `employee_*` (Qualifikationen, Zeiten, Leaves), `appointment_employees`.
- **Views/Einstiege:** „Einsatzplan", „Allgemeine Aufgaben".

### Kapitel I — Montage / Ausführung / Tagesberichte
- **Controller:** `Report/DailyReportController` (4.001), `Report/OverdueCenterController` (4.618), `Checklist*Controller` (Assemble/Room/Apartment/EndTask), `Project/*`.
- **Models/Tabellen:** Reports/Tagesberichte, Checklisten, `project*`.
- **Views/Einstiege:** „Tagesberichte", „Berichts-Übersicht", „Überfällige Berichte".

### Kapitel J — Dokumentation / Abnahme / Nachweise
- **Controller:** `Customer/Maintenance/*`, `BrandMaintenanceChecklistController`, Heizkörper-/Protokoll-Flächen.
- **Models/Tabellen:** `maintenance_protocols` (Muster vorhanden), **`handovers` = Lager-Asset-Transfer (Namensfalle, NICHT Kunden-Abnahme)**.
- *(Audit: **niedrigste Reife** — Abnahmeprotokoll/Mängelliste/Unterschrift fehlen; Abnahme = leere Kanban-Spalte.)*

### Kapitel K — Rechnung / Zahlung / FiBu
- **Controller:** `Invoice/InvoiceController`, `Invoice/InvoiceCanvasController`, `Old/NewLeadsInvoiceController` (Alt).
- **Models/Tabellen (führend):** `invoices` (**Umsatz-Wahrheit**), `invoice_items`, `invoice_files`, `accounting_documents`/`accounting_journal`/`accounting_foundation`. `deal_invoices` dormant.
- **Services:** `Accounting/*` (BuchungsEngine, Auswertungs, DatevExtfExport, EingangsBelegfluss, Belegfluss), `Invoice/*` (InvoiceNumberService race-safe, InvoiceDeletionGuard).
- **Views/Einstiege:** „Rechnungen".

### Kapitel L — Controlling / Nachkalkulation / Auswertung
- **Controller:** `Dashboard/DashboardCompanyController`, `Dashboard/DashboardDepartmentController`, `EmployeeDashboardController` (2.420), `Report/OverdueCenterController`.
- **Models/Tabellen:** Umsatz-/Aktivitäts-Aggregate über `invoices`/Aktivitäten. **Keine persistierte Nachkalkulation** (nur Vorkalkulation).
- **Services:** `Energie/KostenService`.

### Kapitel M — Service / Betrieb / Reklamation / Wiederkehr
- **Controller:** `Ticket/ProblemController` (2.864), `Customer/Maintenance/*`, `BrandMaintenanceChecklistController`.
- **Models/Tabellen:** `problems`, `error_problem`, `customer_maintenance_contracts`, `next_service_date`.
- *(Audit: `next_service_date` vorhanden, aber keine automatische Wiedervorlage.)*

### Kapitel N — Querschnitt Architektur / Sicherheit / Performance / UX
- **Sicherheit:** Middleware-Alias `permission` → `CheckUserPermission`, Tabelle `user_rolls`, `User::hasPermission()` (P0/P1-IDOR über 98 Controller gegatet, `tests/Feature/Security`).
- **Architektur:** zwei Welten (Alt-Fett-Controller vs. junge Services), Strangler-Weg. `docs/architektur/bauordnung.md`.
- **Performance/UX:** 706k View-LOC, hoher Inline-JS-Anteil (`kanban.js` 17.204 Z., `new_leads/customer_profile` 91,5 % JS), Design-System uneinheitlich.

### Rand / Legacy (außerhalb der Prozesskette)
Chat/`Ai`/`Email`/`BreakingNews`/`Wordpress`/`Bitrix*`/`Api` (Nuriva)/Video-Jitsi. **Legacy Bitrix/NIBE/IMAP** laut Projekt-Gedächtnis bewusst ignorieren; Nuriva-API + Video = TABU-Zone (nur lesen).

---

## 7. Vorhandene Automatisierungen (Commands · Jobs · Hooks · Follow-ups)

- **Console-Commands (14):** `BackfillLeadStageId`, `BackfillPhaseSections`, `BackfillDealMeasurementOwner`, `DispatchMainAppointmentReminders`, `ProcessPersonalTaskScheduler`, `PurgeSoftDeletedGarbage`, `FollowUpDedupeTasks` (A1b), `SyncLeadEmails`, `SyncSolarNewsToChat`, `UpdateLeaveStatus`, `UpdateJobRepresentativeStatus`, `DeactivateExpiredBreakingNews`, `SpecImportCommand`, `SpecRollbackCommand`.
- **Jobs (10, async):** `ImportClimateWorkbookJob`, `ProcessWeatherData`, `ScheduleTaskReminder`, `EmbedMessage`, `UpdateMemory`, `PlanKlassifizieren`, `ProcessChatChunk`/`ProcessChatData`, `ProcessFusionEntry`/`FusionFormEntryJob`.
- **Model-`booted()`-Hooks (13 = abgeleitete Wahrheiten/Invarianten):** `LeadProductList` (Stage-FK-Ableitung, Kanon), `Invoice` (Nummernkreis + due_date-Ableitung A3 + Storno-Wächter), `Deal`, `NewLeads`, `LeadStage`/`LeadStageSubStage`, `Offer`, `DealMeasurement`, `Anforderungsprofil(+Wert)`, `ArticleGroup`, `InvoiceFile`, `Employee`.
- **Follow-up-Automatik:** `FollowUpCreator` (UPSERT je Quelle) + A1-Verdrahtung (Lead→Angebot-Task am Stage-Move); Race per Unique-Index abgesichert (A1b).
- **Listeners (3):** `LogUserLogin`/`LogUserLogout`, `StoreLeadActivity`.

---

## 8. Tests je Kapitel (71 Dateien)

`tests/Feature/`: **Accounting** (K), **Invoice** (K), **Arbeitsliste** (C), **FollowUp** (C), **Kanban** (C), **Heizkoerper/Heizlast/Anforderungsprofil** (D), **Catalog/Spec/Suppliers** (F), **DealMeasurement** (E), **Planner** (H), **Maintenance** (M/J), **Security** (N), **Form** (D), **VideoCall** (Rand). `tests/Unit/`: **Energie/Heizlast/Heizkoerper/Form**. — **Schwerpunkt Tests = die jungen Zonen (D-Auslegung, K-FiBu, C-CRM, N-Security).** Der Alt-Kern (B, H, I) ist test-arm **(Stichprobe/zu verifizieren)**.

---

## 9. Vorhandene Dokumentation je Kapitel

Umfangreicher Bestand — **reuse, nicht duplizieren**:
- **Querschnitt/A:** `audit/code-audit.md`, `architektur/bauordnung.md`, `architektur-entscheidungen.md`, `architektur-bewertung-zweitmeinung.md`, `crm-inventur-00…08`, `cockpit-inventur.md`, `bestandsaufnahme-24h.md`.
- **B/C:** `crm-daten-inventur.md`, `customer-model-falle-befund.md`, `customer-phase-lists-*`, `begriffs-*`.
- **D:** `crm-inventur-03-angebot-konfiguration.md`, `befund-b2a-heizlast.md`, `cutover-wb-*`, `audit/experten/02-*`.
- **F/G:** `crm-inventur-01-artikel.md`, `crm-inventur-02-lager-beschaffung.md`, `spec-import/`.
- **K:** `accounting/`, `backlog-accounting.md`.
- **L:** `controlling-bestandsaufnahme.md`.
- **Governance:** `BETRIEBSORDNUNG.md`, `STRAENGE.md`, `agents/00–05`, `system-kapitelplan.md`, `systemoptimierung-fahrplan.md`, `arbeitskompass-ticket.md`.

---

## 10. Führende Datenwahrheiten (Soll, aus CLAUDE.md/Weichen)

| Sachverhalt | Führende Wahrheit |
|---|---|
| Umsatz | **`invoices`** (`deal_invoices` stillgelegt) |
| Phase/Stufe | **`lead_stages`** (6 Phasen) |
| Kunde | **`new_leads`** |
| Objekt | **`lead_alternative_adds`** (Objekt-ID = id, referenziert via `alternative_id`) |
| Gewerk | **`lead_product_lists`** (`product_id`→`article_groups`) |
| Angebot / Auftrag | **`offers`** / **`deals`** |
| Katalog | **EIN** ticket-Artikel-Katalog |
| WP-Geräte | **`CatalogDeviceRepository`** |
| Klimadaten | **`klima_plz`** / `KlimaPlzService` |
| Follow-up-Träger | **`personal_tasks`** (type=follow_up) |

---

## 11. Erkennbare Doppelwahrheiten (Audit-belegt)

1. **Stage-Ableitung ≥3 Varianten mit echtem Fold-Unterschied:** `LeadProductList::deriveLeadStageId()` (Kanon, mit Fold) vs. `NewLeadsController::normalizeCompanyStage()` (ohne Fold) vs. `match()`-Variante mit Tippfehler-Key → **`lead_stage_id`-Divergenzrisiko**.
2. **Drei Speichern-Muster für `lead_product_lists`** mit **drei Default-Status** (`Lead`/`open`/`archive`) — Status-Zoo an der Quelle.
3. **`customers`-Zombie-Tabelle** neben `new_leads` (Doppel-/Alt-Struktur).
4. **`deal_invoices`** dormant neben `invoices` (Umsatz-Doppelschiene; Drop ausstehend).
5. **„bezahlt" doppelt konzipiert:** amountbasiert (`Invoice::isFullyPaid`/`scopePaid`, A5) vs. `status='paid'`-Dashboardfilter — Model konsolidiert (isPaid delegiert), **Dashboards noch status-basiert** (Posten A5b offen).
6. **`offer_kanban_stages` ↔ `lead_stages`** (zwei Phasen-Quellen, Audit-Befund).

---

## 12. Unverdrahtete, wertvolle Bausteine (gebaut, 0/​wenige Aufrufer)

- **`BivalenzService`** (WP-Ranking: Bivalenzpunkt/JAZ/E-Stab/Laufstunden/Strom/Warnungen, VDI-4645/4650) — **0 Aufrufer**. Die komplette Auslegungs-Kette (KlimaBin→Verbrauch→Heizlast→Match→Kennlinie→Bivalenz) ist **nur bis „Kandidaten" verdrahtet** (`HeizlastController`), das Ranking fehlt.
- **`SmartroutingService`** (Formular-/Checklisten-Routing) — 0 Regeln, 0 Aufrufer.
- **`PlausibilityService` / `AnforderungsprofilHeizlastAdapter`** — Intelligenz gebaut, kaum verdrahtet.
- **`MaintenanceProtocol`-Muster** — vorhanden, für Abnahme (J) ungenutzt.
- *(Leitmotiv des Audits: „gebaut-aber-nicht-verdrahtet".)*

---

## 13. Grobe Prozessbrüche (aus Prozess-Graph; markiert, nicht gelöst)

Lead→Angebot 🟡 (A1 verdrahtet den Task, Rest offen) · **Auslegung→Angebot 🔴** (Auslegungs-Welt isoliert) · Angebot→Auftrag 🔴 · Auftrag→Beschaffung 🟡 (GR bucht keinen Bestand) · Auftrag→Montage 🔴 (keine Verfügbarkeitsprüfung) · Feld-Abschluss→Nachfass 🔴 · **Montage→Abnahme ⬜** (fehlt) · **Abnahme→Rechnung 🔴** (kein Trigger) · **Rechnung→Buchung ⬜** (FiBu gebaut, 0 Buchungen) · Rechnung→Zahlung/Mahnung ⬜ (blind) · Abschluss→Nachkalkulation ⬜ · Wartung 🔴 (keine Auto-Wiedervorlage).

---

## 14. Risiken

- **Gott-Klassen/God-Table:** `NewLeadsController` (14k), `PlannerPlanController` (11k), `lead_alternative_adds` (193 Sp./1 FK, mega-gekoppelt) → jede Änderung breite Reichweite.
- **Hook-Umgehung:** `DB::table` in 160/387 Controllern umgeht Model-Hooks (abgeleitete Wahrheiten können divergieren).
- **Toter Ballast:** `app/Http/Controllers/Old/` (37 Dateien, 100 % tot), ~110 `copy/backup`-Dateien, ~28 „Old Code"-View-Ordner.
- **Fehlende Struktur:** 0 `FormRequest` (Validierung inline/lückig), Status-Zoo (140. `status`-Varianten), Inline-JS als größte Frontend-Last.
- **Weiße Flecken:** Abnahme (J), Nachkalkulation (L), Zahlungs-/Mahnwesen (K), Beschaffungs-Prozessschicht (G).
- **Daten:** Weichen (Objekt-Klammer `projects` ohne `deal_id`) teils dormant → Cross-Objekt-Sicht unvollständig.
- **Sicherheit:** P0/P1 gegatet + getestet (gut); Rest-Rechte-Grants dünn (Nicht-Admins ggf. gesperrt — Betriebsthema).

---

## 15. Vorschlag: erstes Tiefenkapitel (2–3 Optionen — Yama entscheidet)

**Option 1 — Kapitel D (Angebot/Auslegung, WP-Unterkapitel).** *Höchster Wert + größte liegende Substanz.* Die normbasierte Auslegungs-Kette ist fast fertig gebaut, aber die wertvollste Stufe (`BivalenzService`-Ranking) ist **0-Aufrufer**; die Kette ist über 3 Controller fragmentiert. Passt zu Yamas ausdrücklicher Sorge. **Risiko:** größte/komplexeste Domäne — braucht die volle 8-Kapitel-Methode (Inventur→…→Abnahme).

**Option 2 — Kapitel B (Eingang/Kunde/Objekt).** *Das Fundament.* God-Table `lead_alternative_adds`, `customers`-Zombie, Stage-Zoo an der Quelle, Gott-Klasse `NewLeadsController`. Wenn die **führende Wahrheit für Kunde/Objekt/Gewerk** hier sauber ist, entlastet das **alle** nachgelagerten Kapitel (D/E/K). **Risiko:** berührt den umsatztragenden Alt-Kern breit.

**Option 3 — Kapitel K (Rechnung/FiBu).** *Geld-kritisch, aber eng abgegrenzt.* `invoices` ist bereits die saubere führende Wahrheit; die FiBu-Services sind gebaut, aber **0 Buchungen** (Festschreib→Buchung-Trigger fehlt), Zahlung/Mahnung blind. Kleinster, klar umrissener Schnitt mit direktem betriebswirtschaftlichem Nutzen. **Risiko:** GoBD/rechtlich sensibel — hohe Prüftiefe nötig.

**Koordinator-Empfehlung (nicht Entscheidung):** Wenn das Ziel *„Substanz nutzbar machen"* ist → **D**. Wenn *„Fundament zuerst sauber"* → **B**. Wenn *„schneller, abgegrenzter Geld-Nutzen"* → **K**.

---

## Evaluator-Notiz (Selbstprüfung dieser Runde)

- **Belegt:** Größen/Top-Controller/Duplikate/toter Code (Audit firsthand); Ordner-/Service-/Command-/Test-/Doc-Struktur (eigene `ls`/`find`/`grep`, 2026-07-11); `BivalenzService` 0 Aufrufer, Kette bis „Kandidaten" verdrahtet (firsthand gelesen).
- **Nur Stichprobe / zu verifizieren:** genaue Controller→Kapitel-Zuordnung je Route (ordner-/navi-basiert, kein Voll-Trace); Reife-Zahlen (aus Experten-Audit übernommen); G-Beschaffung „bucht keinen Bestand" und I/H-Testarmut nicht neu vermessen.
- **Nicht gemacht (korrekt):** keine Bewertung-in-Tiefe, kein Soll-Konzept, keine Verknüpfung/Automatisierung, keine Codeänderung, kein Commit.
- **Nächster logischer Schritt:** Yama wählt Tiefenkapitel → dann `docs/<domäne>-inventur.md` (Kapitel 1 der 8-Kapitel-Methode).
