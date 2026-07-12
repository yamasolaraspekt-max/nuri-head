# System-Inventur — Nachsuche 3 (versteckte Zusammenhänge & vergessene Funktionen)

**Stand:** 2026-07-11 · **Rolle:** Koordinator + 3 read-only Explore-Experten (Synonyme/Methoden · Prozessketten-Brüche · Tests/Docs) · **Modus:** nur finden/zuordnen/Lücken markieren — **keine Bewertung, keine Priorisierung, kein Bau.**
**Baseline:** gegengecheckt gegen `docs/system-inventur.md` + `docs/system-inventur-nachsuche.md`. „NEU" = in keinem der beiden erwähnt. Kennzeichnung **[sicher]** / **[Stichprobe]** / **[zu-verifizieren]**.

---

## 1. Neue Funde gegenüber Inventur 1 + Nachsuche 2

1. **Angebots-Wizard rechnet nicht + Dual-Datenmodell** [sicher]: `OfferWizardController` importiert **keinen** Service, `createOffer()` schreibt leere Hülle (`total_net=0`, `sections=[]`). Zwei getrennte Positions-Wahrheiten desselben Angebots: `offer_details.sections` (JSON, `OfferFolderController`) vs. Tabelle `offer_product_lists` (`OfferDetailsController::update`, berührt `offer_details` nicht).
2. **WP-Auslegung intern doppelt/entkoppelt** [sicher]: `EnergieAuslegungController::wpBerechnen` nutzt Jaz/WW/Kosten/Förderung/Verbrauch — **aber nicht** `WaermepumpenMatchService`/`BivalenzService`. Der Heizlast-Match (`HeizlastController`) und die WP-Wirtschaftlichkeit teilen **keinen** Datenfluss.
3. **Formular-Engine ist teil-LIVE, nicht dormant** [sicher]: `ProductFormulaController` voll geroutet (`web.php:2885–2898`, inkl. FS-07-Eval-Endpoint), nutzt `FormSchemaValidator`/`FormulaEvaluationService`/`VisibleIfService`. Korrigiert „Formular-Engine dormant".
4. **U-Wert-Referenzschicht fehlt komplett in der Karte** [sicher]: Tabellen `konstruktionen`, `baualtersklassen`, `heizlast_bauteile` + Models `Konstruktion`/`Baualtersklasse`/`HeizlastBauteil` + `ReferenzKatalogSeeder` (DIN 4108-4/ISO 6946/IWU-TABULA).
5. **Fusion/Website-Lead-Intake ist aktiver Kanal, nicht bloß Legacy** [sicher]: 5 Models (`WpFusionForm*`), `FusionWebhookController`, Middleware `VerifyFusionToken`, Live-Zähler `SidebarCountController:36 'website_leads'`.
6. **Montage-Fortschritt-Progressbar GEBAUT+verifiziert** [sicher]: `KanbanLeadTaskController::montageFieldProgress()` (`:119`), Weiche-6-Weg-A (Commit `f52ab10`), Balken in `customer_profile.blade.php`.
7. **Synonym-Kollisionen** [sicher]: `leads`=E-Mail-Modul ≠ `new_leads`=Kunde; „Ticket"=`Problem` (kein `Ticket`-Model); Auftrag=`Deal` (kein `Order`); `Appointment`(11×) vs `MainAppointment`(122×).
8. **Prozessketten enden isoliert** (FiBu, Bivalenz, Abnahme, Beschaffung — s. §8).
9. **Geroutete Routen ohne View** [sicher]: `ticket_tasks.*`, `admin.costing_sets.index`, `datanorm.parse`.
10. **Tote Public-Methoden in lebenden Klassen** [sicher]: `CustomerContextBuilder::addRecentWorkflow`, gesamte Wrapper-Schicht `DashboardLiveActivityService`.

---

## 2. Isolierte wertvolle Services (verfeinert: drei Klassen)

**(a) Vollständig unreferenziert — auch OHNE Test** [sicher]: `GoogleGeocoder`, `Heizlast/HeizlastService`, `Energie/PvProjektService`, `Suppliers/SupplierProductImportService`, `DashboardLiveActivityService`.
**(b) NUR in Tests, keine Produktions-Verdrahtung** [sicher]: `Maintenance/SeedOrphanCleanupService`, `Suppliers/Omd/OmdClient`, `Anforderungsprofil/AnforderungsprofilHeizlastAdapter`.
**(c) Namentlich bekannte Isolierte (bestätigt)**: `BivalenzService`, `SmartroutingService`, `PlausibilityService`; FiBu-Kette `AuswertungsService`/`DatevExtfExportService`/`EingangsBelegflussService` (0 Refs) + `BelegflussService`/`BuchungsEngine` (nur gegenseitig) — Isolation auf **Ketten-Ebene**.
**Methoden-Ebene** [sicher]: `CustomerContextBuilder::addRecentWorkflow(:205)` tot in lebender Klasse; `DashboardLiveActivityService` 7 Wrapper (`notifyTask/Appointment/Lead/Inquiry/Offer/Deal/Ticket`) tot, `notifyEmployees` in `PlannerPlanController:42` **reimplementiert** (Service umgangen).

---

## 3. Versteckte oder direkteinstiegsfähige Controller/Views

**Views ohne Route (echt, NICHT Old/Copy)** [sicher]: Alt-Angebots-UI `admin/offer/offer/{offer,offer_folder,print,select_product,customer_view}.blade.php`, `admin/offer/configuration/offer/index.blade.php`, `admin/offer/pdf_export.blade.php`; Alt-Rechnungs-UI `admin/invoice/{invoice_create,invoice_edit,invoice_approved}.blade.php`; weitere [Stichprobe]: `admin/master_sets/editor.blade.php`, `admin/deal_measurements/show.blade.php`, `admin/product/inventory/inventory_search.blade.php`.
**Route ohne View** [sicher]: `datanorm.parse`→`view('datanorm.upload')` fehlt (nur `admin/datanorm/upload`); `admin.costing_sets.index`→kein costing_sets-Blade; `ticket_tasks.*` (index/show/create/edit)→**kein** `admin/ticket_tasks/*`-Blade. Weitere [Stichprobe]: `beg_fundings.create/edit`, `admin.teams.create`, `admin.new_leads.layouts.nextStep`, `employee_sick.index` u. a.
**Direkteinstiegsfähige Tool-/Import-/Wizard-Controller** (Route aktiv, nicht in Sidebar) [zu-verifizieren]: `ToolsController`, `PVToolsController`, `LeadImportController`, `ProductImportController`, `ClimateImportController`, `CustomerHistoryImportController`, `MoserWpImportController`, `TaskWizardController`, `EmailConfigurationController`, `OfferWizardController` (durch `wizard-smart` ersetzt).

---

## 4. Tests, die auf vergessene Funktionen hinweisen

- **U-Wert-Referenzschicht** [sicher]: `ReferenzKatalogSeederTest`, `InverterSizingServiceTest` nutzen `DB::table('baualtersklassen'|'konstruktionen')` → Tabellen/Models/Seeder in Karte nicht geführt (§1.4).
- **Fusion/Website-Intake** [sicher]: `FusionWebhookTest` nutzt `wp_fusion_forms|_form_fields|_form_submissions|_form_entries` → Webhook-Datenschicht in Karte nicht geführt.
- **FiBu-Kette test-only** [sicher, bekannt vertieft]: `EingangsBelegflussTest`, `DatevExtfExportTest`, `AuswertungenTest`, `PositionsSplitTest` — die einzige Erreichbarkeit der Accounting-Services.
- **Formular-Engine** [sicher]: `tests/Unit/Form/*` belegen `product_formulas` + Eval-Kette als real gebaut.
- **Keine weitere test-only-Service-Insel** über die bekannten hinaus gefunden [sicher]; Factories (`Anforderungsprofil*`, `User`) enthalten keine versteckte Tabellen-Wahrheit.

---

## 5. Docs, die auf vergessene Funktionen hinweisen

- **FiBu-Backlog** (`docs/backlog-accounting.md`): (i)–(vi) gebaut + **(vii) Positions-Erlöskonten-Split GEBAUT**, **(viii) Kreditoren-/Wareneingangs-Seite „KERN GEBAUT" (2026-07-08)** — in Karte nicht einzeln benannt.
- **Formular-Backlog** (`docs/backlog-formulare.md`): FS-01/02/04/05/07 „GEBAUT"; offen FS-06/08/09/10 → „dormant" nur für Vorlagen-Befüllung + Antwortspeicherung. + `docs/backlog-rbac.md` RBAC-01 Builder-Gate.
- **Montage-Progressbar** (`docs/architektur-entscheidungen.md:303–326`): 🟢 FERTIG (Commit `f52ab10`).
- **Datenwahrheit-Divergenzen** [sicher, Doku-Beleg]:
  - `deal_invoices`: Glossar `:80` **„BEHALTEN — schlafendes beabsichtigtes Feature"** ↔ Karte „Drop ausstehend". Divergenz.
  - `projects` (Objekt-/Bauphasen-Klammer): `architektur-entscheidungen.md:126–152` WEICHE 1/5 **ENTSCHIEDEN Variante A**; 31 Zeilen, ohne `deal_id` — Karte nennt nur Risiko-Halbsatz.
  - Weichen 1 (entschieden), 2/4 (offen), 6-Restfrage „Montageplan NICHT automatisch, bewusst geplant" — erklärt Bruch „Auftrag→Montage" als Feature.
  - `customer_alternative_adds` (Model `CustomerAlternativeAdd`) = weiterer Zombie neben `lead_alternative_adds`.
- **Kostenstellen-Modell Phase 2** (`docs/uebernahme/konzept-phase-2-kostenstellen-*`): ENTSCHIEDEN-konzeptionell, **kein Code** (`cost_center`/`kostenstelle` 0 Treffer) — anstehende Datenwahrheit Kapitel L.
- **STRAENGE.md**: parallele getrennte Instanzen/Stränge (Heizkörper, Katalog-Cut-over, wberechnung, CRM-Konversion, NAV, OMD, S1-Rechnung, SEC-DM) + Tabu-Zonen — Parallelbetrieb-Kontext.

---

## 6. Tabellen/Models mit unklarer Nutzung (ergänzend zu Nachsuche 2)

- **NEU modellos/karten-blind**: `konstruktionen`? (Model existiert), `product_formulas`, `product_formula_routing_rules`, `wp_fusion_form*`, `fusion_form_submissions`, `planner_item_material_requests` [zu-verifizieren modellos].
- **Zombie/Doppel ergänzt**: `customer_alternative_adds` (neben `lead_alternative_adds`), `leads` (E-Mail, neben `new_leads`), `product_w_p_s` + `heatpumps` + `product_heat_pump_specs` (3× WP), `heatpump_checklists` + `w_p_checklists` (2× WP-Checkliste).
- **Naming-Mismatch Model↔Tabelle** (mögl. tote Relation) [zu-verifizieren]: `Heatpump→heat_pumps`, `CustomerProduct→customer_product`, `BrandMaintenanceChecklist`/`DistributorMaintenanceChecklist` (Singular), `MasterSetGroupSet`.
- **Proliferation ohne Basis** [Stichprobe]: 16 Report-Models, 20+ Checklist-Models, mehrere Material-Request-Strukturen (`PurchaseRequest`/`InventoryRequestOut`/`GoodsReceipt`/`DeliveryNote`/`planner_item_material_requests`).

---

## 7. Automatisierungs-Spuren (ergänzend)

- **13 Model-`booted()`-Hooks** (aus Nachsuche 2, bestätigt): Nummernkreise Offer/Deal/Measurement, Invoice due_date+Löschschutz, LeadProductList Status→FK-Fold, ArticleGroup 8 Default-Phasen, Employee Default-Wochenplan, Anforderungsprofil-Registry.
- **Keine Observer**; verwaiste Jobs (`EmbedMessage`, `UpdateMemory`, `FusionFormEntryJob`); verwaiste Events (`DashboardEmployeeStatusUpdated`, `LeadSidebarCountsUpdated`, `SolarNewsPushed`); **tote Listener-Kette** `LeadRecordChanged`→`StoreLeadActivity` (nie gefeuert); **Scheduler-Dublette** `ProcessPersonalTaskScheduler`.
- **NEU**: `DashboardLiveActivityService` dispatcht toten Event `DashboardLiveActivityCreated`; Service-Layer wird umgangen (`PlannerPlanController` reimplementiert `notifyEmployees`, `DashboardLiveInboxController` nutzt Model/Event direkt).
- **Rechnungs-Automatik verdrahtet** (Model-Hook): `InvoiceNumberService` (Nummernkreis), `due_date` A3, Löschschutz. **Kein** Automatik-Übergang Rechnung→Buchung (FiBu isoliert).

---

## 8. Abgebrochene Prozessketten (Route→Controller→Service→Model→View)

| Prozess | Kette bricht wo | Beleg |
|---|---|---|
| **Angebot** | Wizard ohne Service (leere Hülle); 2 Positions-Wahrheiten `offer_details.sections` ↔ `offer_product_lists` | `OfferWizardController::createOffer`; `OfferDetailsController::update`; `OfferFolderController::calculateDetailTotals:2279` |
| **WP-Auslegung** | endet bei `wpMatch->kandidaten()`; kein Bivalenz/Ranking je Kandidat; `BivalenzService`/`WpKennlinieService`/`KlimaBinService` test-only Insel; wpBerechnen entkoppelt | `HeizlastController:180`; `EnergieAuslegungController::wpBerechnen:196` |
| **Rechnung→Buchung** | „paid" setzt nur `paid_amount/paid_at`; Accounting-Schicht ohne HTTP-Eingang | `InvoiceController::applyStatusAccounting:1208`; `Services/Accounting/*` nur in Tests |
| **Montage→Büro** | nur `appointment.is_report=1` / `planner_items.status` / Notification; kein Lifecycle-Schluss zu Deal/Phase | `AppointmentReportController:128`; `PlannerItemStateController`; `PlannerItemMaterialController` |
| **Beschaffung→Lager** | `GoodsReceipt::issue/store` bucht keinen Bestand; entkoppelt von `inventory` (kein `deal_id`/`offer_id`) | `GoodsReceiptController:345`; `GoodsReceipt` fillable |
| **Abnahme** | nur leere `lead_stages` `key='abnahme'`; kein Controller/Model/Route/View | Migrationen `…add_abnahme_lead_stage`/`…activate_abnahme_lead_stage:15` |

---

## 9. Namens-/Synonym-Funde

| Konzept | Varianten (Doppelstrukturen) |
|---|---|
| **Kunde/Lead** | `new_leads`(Kunde) vs `leads`(E-Mail-Modul) vs `customers`(`Customer`, Zombie); `CustomerAlternativeAdd`↔`LeadAlternativeAdd`, `CustomerProductList`↔`LeadProductList`↔`InquiryProductList`, `CustomerResponsible`↔`NewLeadResponsibility`, `CustomerActivity`↔`LeadActivityLogs`, `CustomerReport`↔`InquiryReport` |
| **Auftrag** | `Deal`/`deals` (kein `Order`/`Bestellung`-Model); Alias „Auftrag" durchgängig; `sync-auftrag`→`InvoiceCanvasController::syncFromAuftrag` |
| **Rechnung/FiBu** | Invoice-Welt (`Invoice*`, aktiv) vs Accounting-Welt (`Services/Accounting/*` + `accounting_*`-Tabellen, isoliert) |
| **Wärmepumpe** | `Heatpump`/`heatpumps` · `ProductWP`/`product_w_p_s` · Spec `product_heat_pump_specs`; Checklisten `HeatpumpChecklist` vs `WPChecklist`; + `CustomerOfferWP`, `CustomerWPCable`, `LeadAlternativePvWpDetail` |
| **Termin** | `Appointment`(schwach) vs `MainAppointment`(stark) |
| **Ticket/Service** | nur `Problem` (`ticket_no`, `TicketTask.ticket_id→Problem`); kein `Ticket`/`Reklamation`/`Complaint`/`Service`-Model |
| **Report/Checklist** | 16 Report-Models · 20+ Checklist-Models — je ohne gemeinsame Basis |

---

## 10. Was weiterhin nicht sicher ausgeschlossen werden kann

1. **Live-Row-Counts** (dormant vs. produktiv) — durchgehend aus Doku, **nicht neu an der DB gemessen**.
2. **Dynamische Registrierung/Reflection** (`Route::resource` via String, `::class`, `@include($var)`) — könnte „verwaiste" Controller/Views/Methoden doch aktivieren.
3. **Deckungsgleichheit** der drei „isolierte-Service"-Zählungen aus 3 Runden (Mengendeckung wahrscheinlich, nicht 1:1 abgeglichen).
4. **`planner_item_material_requests` modellos?** + `customer_id→new_leads`-Alias-Durchgängigkeit + Naming-Mismatch-Tabellen — je Einzelprüfung offen.
5. **Route→fehlende-View-Restliste** (~37 Kandidaten): pro Fall Routing nicht einzeln nachverfolgt.
6. **API-Schicht** (`routes/api.php`, Mobile/Nuriva) + Rand/Legacy (Chat/AI/Bitrix/Video, docker/S3/SES/Redis) nur oberflächlich — TABU.
7. **Semantische Gleichheit** der Synonym-Paare (Existenz belegt; ob wirklich „dasselbe Konzept" je Paar fachlich zu bestätigen).

---

## Evaluator-Notiz
- **Belegt (firsthand Code+Doku, 3 Experten):** alle §-Kernbefunde mit Datei:Zeile.
- **Stichprobe/zu-verifizieren:** Live-Row-Counts, Proliferations-Vollzähligkeit, Naming-Mismatch, Route→View-Restliste, semantische Synonym-Gleichheit.
- **Nicht gemacht (korrekt):** keine Bewertung/Priorisierung/Lösung, keine Änderung, kein Commit.
- **Nächster Schritt (auf Yamas Auftrag):** Zusammenführung Inventur 1 + Nachsuche 2 + Nachsuche 3 (Dubletten raus, Unsicherheiten markiert) → dann Bewertungs-Startblock.
