# AUDIT 01 — Fehler-Inventur (Phase 1A)

> **Status: IN ARBEIT.** Rein lesend. Je Fund: Fundort · Repro · Schwere (P0 Sicherheit/Datenverlust · P1 Funktionsbruch · P2 · P3) · Rolle. Belege wörtlich.
> Teil-Sektionen werden befüllt, sobald der jeweilige Lese-Sweep fertig ist: [x] Daten-Integrität · [ ] Routen · [ ] Sicherheit · [ ] 15-Aktionen-Trace · [ ] JS-Konsole (manuell).

---

## 1A-3 · Daten-Integrität (SQL, read-only) — FERTIG

Kern-Zeilenzahlen (Stand Audit): new_leads 52 · lead_alternative_adds 71 · lead_product_lists 52 · offers 29 · deals 14 · invoices 11 · deal_invoices 0 · planner_items 0.

| # | Befund | Schwere | Fundort/Repro |
|---|--------|---------|---------------|
| DI-1 | **19 von 71 Objekten** (`lead_alternative_adds`) verweisen auf `lead_id` 11–52, die in `new_leads` (IDs real 105–156) **nicht existieren** → Kunde von diesen Objekten aus **nicht erreichbar** | **P0** | `lead_alternative_adds LEFT JOIN new_leads` = 19 Waisen; distinct lead_id 11,18,26–31,33,34,36,40,43,44,46,49–52. FK-Constraint deklariert, aber mit `foreign_key_checks=0` unterlaufen (Seed/Import-Artefakt). |
| DI-2 | Test-Rechnung **`TST-OPEN-2337`** in führender `invoices`-Schiene: `deal_id=36` verwaist (deals 15–29) + einziges `type='Rechnung'` statt `'final'` → verfälscht Umsatz +1.000 €, bricht deal→invoice-Kette | **P1** | `invoices id=21`, total 1.000 €, status open, customer 106, object 104. Übrige 10 deal_id valide. |
| DI-3 | **`customers`-Domäne tot:** `customers`=0, referenziert von **19 leeren Kind-Tabellen** (customer_alternative_adds, customer_product_lists, …, purchase_requests) = vollständiges totes Parallel-Schema | **P1** | exakte COUNT(*)=0; abgelöst durch aktive Kette new_leads/lead_alternative_adds/lead_product_lists. Bestätigt Glossar-Befund „Customer-Model-Falle". |
| DI-4 | FK-Ziele `stages` (← `lead_product_lists.product_stage_id`) und `lead_stage_sub_stages` (← `…sub_stage_id`) **leer aber verdrahtet** (dormant-but-wired); alle FK-Werte NULL → latent, nicht akut | **P2** | COUNT(*)=0 beide; 0 Waisen (Werte NULL). |
| DI-5 | **Status-Zoo:** `lead_product_lists.status` und `.stage` **redundant identisch** (`status <=> stage`-Divergenz = 0); freie varchar, DE/EN gemischt; tote Spalten (`old_stage`, `measurement_status`, `offer_acceptance_status` durchgehend NULL) | **P2** | status/stage: lead23/offer10/deal8/follow_up5/accepted4/project2. `deals.project_status` freitext DE: Montage/offen/in Bearbeitung/abgeschlossen. Ausreißer deal id=29 (`status='deal'`, deal_status+project_status NULL). |
| — | **Rechnungs-Schienen-Doppelzählung: KEIN Befund** — `deal_invoices` leer, keine Cross-Schienen-Summe | OK | bereinigter Echt-Umsatz `invoices` type='final' = 204.194,48 €. |

**Rollen-Wirkung:** DI-1 trifft jeden, der vom Objekt zum Kunden navigiert (Vertrieb/Innendienst) — „Kunde nicht gefunden"-Kandidat. DI-2 trifft Buchhaltung/Controlling (Umsatz-Verfälschung). DI-5 trifft alle Auswertungen (wackelige Status-Grundlage — deckt sich mit Weiche 1).

**NICHT-VERIFIZIERT:** Ursache der lead_id-Waisen (Import-Reihenfolge vs. bewusstes Re-Mapping); große Leer-Tabellen-Liste aus InnoDB-Schätzwert (Fokus-Tabellen exakt gezählt).

---

## 1A-1 · Routen-/HTTP-Sweep — FERTIG

**2363 Routen** (1145 GET · 1218 schreibend), 297 Controller. Prüftiefe: **alle 2270 App-`Controller@method`-Paare** statisch auf Methoden-Existenz geprüft (nicht Stichprobe).

**RT-1 (P1): 57 tote Action-Endpunkte** — Route registriert, aber Controller-Methode existiert nicht → bei Aufruf `BadMethodCallException` → **HTTP 500**. Laufzeit NICHT-VERIFIZIERT (kein HTTP-Aufruf, READ-ONLY), Befund statisch belastbar.

Harte P1 (kein erkennbarer Ersatz, bei Verlinkung echter Bruch):
| Route | URI | fehlende Methode |
|---|---|---|
| lead.overview | GET /lead/overview | `LeadOverviewController@index` |
| leads.archive / leads.junk | GET /lead/archive · /lead/junk | `@archiveLeads` · `@junkLeads` |
| offers.generate-pdf | POST /offers/generate-pdf | `OfferController@generatePdf` |
| customer.notes.update / destroy | PUT/DELETE /customer-notes/{note} | `@noteUpdate` · `@noteDestroy` |
| employee.info | GET /employee_details | `EmployeeController@view` |
| ticketTasks.load / timeline | GET /ticket-tasks/… | `TicketTaskController@loadTasks` · `@timeline` |
| save.customer.history | POST /customer-history/save | `CustomerHistoryController@save` |
| admin.master_sets.editor | GET /admin/master-sets/{id}/editor | `MasterSetController@editor` |
| profitability report/data (4 Routen) | GET /customer_profit/… | `ProfitabilityCalculationController@index/showReport/getCalculationReport/getProfitabilityData` |

Verwaiste Alt-Zwillinge (Ersatz existiert → real geringeres Risiko): `deal.save`→`store` (Ersatz `deal.store`→`dealStore`), `product.formula.updates`→`update` (Ersatz `product.formula.update`→`updateFormula`), `images.store`→`store` (Ersatz `upload`/`uploads`). *(Deckt sich mit FS-07-Befund: Route `product.formula.updates` ist der tote Zwilling.)*

**RT-2 (P2): Namens-Duplikat (Bug)** — zwei Routen tragen identisch den Namen `admin.master_sets.` (Präfix ohne Suffix): `duplicate` + `duplicate-options` → `route('admin.master_sets.')` mehrdeutig (letzte gewinnt). Einziger echter Name-Duplikat.

**RT-3 (P3): Verwechslungs-Namenspaare** — `product.formula.update`/`.updates`, `deal.save`/`.store`, `get.position`/`.positions`, `lead.junk`/`.junks`, `lead.reference`/`.references` → `route()`-Verwechslungsrisiko.

**243 unbenannte Routen** (Häufung Kunde/Lead 92, System-Closures 64).

**Offen (NICHT-VERIFIZIERT):** welche der 57 toten Routen tatsächlich aus Blades/Nav verlinkt sind (Live-Impact-Ranking) — braucht Frontend-Verlinkungs-Check. TABU nicht gewertet: Nuriva-API, Video/Jitsi, `lead-email-accounts.show` (IMAP-Legacy-nah → P2/ignorierbar).
## 1A-2 · Sicherheit — FERTIG (schwerste Fundklasse)

Basis: 2363 Routen, **1218 schreibend; 1117 tragen nur `web`+`Authenticate`** (kein Rollen-/Ownership-Gate). Autorisierung (`is_Admin`, `CheckUserPermission`, `InvoiceMiddleware`) nur auf ~150 Routen. Jede „unauthenticated"-Aussage zusätzlich am Controller-`__construct` gegengeprüft.

### SEC-0 (P0) — UNAUTHENTIFIZIERTE Schreibrouten (anonym, ohne Login)
Ganze Gruppen `Route::group(['middleware'=>'web'])` **ohne `auth`**, Controller ohne ctor-auth → **jeder anonyme Internet-Nutzer** kann auslösen. Verifiziert: route:list + Controller-Quelle + web.php GELESEN.
| Fundort | Effekt |
|---|---|
| `web.php:1738` EmployeeDocumentController@destroy/upload/update | HR-Dokumente anonym löschen/hochladen/umbenennen |
| `web.php:1772 ff.` Employee\Profile\* (emp_address/emergency/license/skill) | **HR-Stammdaten** (Adresse, Notfallkontakt, Führerschein) anonym schreibbar |
| ImageController (document/upload·delete·rename, images, screenshot) | **Kunden-Dokumente/Bilder** anonym hoch-/runterladen/löschen |
| `web.php:1200` CustomerStageController (initialize/update-customer-stage) | **Belegkette-Phasen** anonym manipulierbar |
| `web.php:3540` OfferDetailsController@update (details/assets/employees/products) | **Belegkette-ANGEBOT** anonym editierbar (außerhalb der auth-Gruppe die bei :3536 schließt) |
| CustomerCardNote, CustomProcess, TimeManagement, CustomerMeasure, BuildingTypeValue, branch/product/inquiry-Stammdaten | anonym schreibend (P1) |

Beleg: `web.php:1738 Route::group(['middleware'=>'web'], …)` (kein auth) → `EmployeeDocumentController::destroy($id){ EmployeeDocument::find($id)->delete(); }` (kein auth, keine Ownership).
**Mini-Fix-Auftrag:** `auth`-Middleware (+ passendes Rollen-Gate) auf diese Gruppen; Verifikation: anonymer POST → 302/401 statt 200. **Höchste Priorität — kein Login nötig, HR/Lohn/Kunde betroffen.**

### SEC-1 (P0) — Account-Takeover + HR/Lohn/Medizin (eingeloggt, ungegatet, IDOR)
| Fundort | Effekt | Schwere |
|---|---|---|
| `UserController.php:544` updatePassword(`users/{user}/password`) | setzt **Passwort BELIEBIGEN Users** (inkl. Admin) — kein is_admin/isSelf | **P0 Takeover** |
| `EmployeeController.php:687/790` profile_update | **Lohn/Bank** (`salary_per_hour, iban, tax_id, tax_class`) beliebig überschreibbar | **P0** |
| `EmployeeController.php:187` updatePasscode · `:678` destroy · `:640` update | Login-Passcode setzen / Mitarbeiter löschen/überschreiben | **P0** |
| `LeaveController.php:265` approve · destroy/update | fremden Urlaub genehmigen, **`remaining_day` fremd dekrementieren** | **P0** |
| `EmployeeSickController.php:208-391` store/update/destroy | **Krankmeldungen (medizinisch)** + Dokumente fremd ändern/löschen | **P0** |
| `NewLeadsController.php:13941` deleteObject · `:3291` destroyWithReason | **Kaskaden-Massenlöschung** Objekt+Gewerke+History / Kunde löschen | **P0** |
| `OfferController.php:1314` destroy | **Hard-delete** ganzer Angebots-Baum (7 Tabellen, `forceDelete`) — Belegkette | **P0** |
| `InquiryController.php:2231` bulkDelete | Massenlöschung freier Anfrage-IDs | **P0** |

Kontrast (positiv, GELESEN): `admin/users/{user}/password`→`adminUsersPassword` MIT `CheckUserPermission:Users,update` — die Legacy-Route `:544` ist der ungegatete Zwilling. `DealController::destroy` korrekt gegated (`authorizeDealDelete`), **aber Angebot NICHT**. `invoices` via `InvoiceMiddleware` gegated ✅.

### SEC-1b/c (P1) — verbreitetes IDOR-Muster (`findOrFail($id)->delete()` ohne Ownership)
~40 weitere Actions: PersonalTask/PersonalTaskBoard (destroy/updateLeadStage), MainAppointment (destroy/forceDelete), Offer folder/update, CustomerContactPerson, CustomerProductInfo (+ Disk-Delete), Distributor (+ Preise), Asset/Machine/Inventory/DeliveryNote, RadiatorInstallation, DailyReport-Zeiterfassung, MasterSetCart (`authorizeCart(){ if(Auth::check()) return; }` = defektes Gate). Jeder eingeloggte Nutzer trifft fremde Objekte. *(Zeilennummern Cluster 1b/1c via Explore-Subagent, stichprobenartig gegengeprüft — teils NICHT-VERIFIZIERT.)*

### SEC-2/3 — Entwarnung (belegt)
- **Mass-Assignment: kein P0** — 22× `create/update($request->all())`, aber alle Ziel-Models feld-begrenztes `$fillable` ohne `is_admin`/`role`; `$guarded=[]`-Models nicht mit `request->all()` befüllt.
- **SQL-Injection: keine** — einzige `whereRaw` mit Request nutzen parametrisierte Bindings (`LeadOverviewController.php:3908`).

**NICHT-VERIFIZIERT/Grenzfall:** `employee/qr/*` (Kiosk-Zeiterfassung, evtl. gewollt öffentlich — prüfen); `user_save`-Gate-Diskrepanz (route:list vs web.php:2195).
## 1A-4 · 15-Kern-Aktionen Code-Pfad-Trace — FERTIG (statisch)

Rein lesend: Zuordnung jeder Aktion zu Route/Controller + belegtes Bruch-/Risiko-Signal aus 1A-1/2/3. Live-Mutieren bewusst NICHT ausgeführt (READ-ONLY) → mutierende Pfade **analysiert**, nicht durchgespielt (NICHT-VERIFIZIERT zur Laufzeit).

| # | Aktion | Träger-Controller | Belegtes Risiko |
|---|---|---|---|
| 1 | Kunde anlegen | `NewLeadsController` (14.054 Z., Gott-Klasse) | DI-1 (P0): 19 Objekte mit unerreichbarem Kunden; destroy/deleteObject ungegatet (SEC-1) |
| 2 | Objekt anlegen | NewLeads/LeadAlternativeAdd | DI-1 (P0) FK-Waisen `lead_id` |
| 3 | Gewerk anlegen | LeadProductList-Pfad | 12 konkurrierende Status-Spalten (Arch 1a); `status`==`stage` redundant (DI-5) |
| 4 | Phasen ziehen (Kanban) | `lead_stages` + `CustomerStageController` | **SEC-0 (P0): Stage-Update anonym**; `offer_kanban_stages` live parallel (Arch 1b) |
| 5 | Angebot erstellen | `OfferController`/`OfferDetailsController` | **SEC-0 (P0): Angebot anonym editierbar** (web.php:3540); Config-Blade 25.064 Z. |
| 6 | Angebot→Auftrag | `DealController` (3.952 Z.) | deal.save-Route TOT (RT-1); Deal-destroy korrekt gegated ✅ |
| 7 | Materialliste/Bestellung | `DealMaterialListController` (2.758 Z.) | — (FS-viii-Kern deckt Kreditoren-Buchung) |
| 8 | Montageplan planen | `PlannerPlanController` (11.097 Z.) | planner_items 0 Zeilen (leer); Gott-Klasse |
| 9 | Monteur-Abschluss+Rückfluss | Planner→Büro | Weiche 6: Rückfluss-Link fehlt (bekannt, nicht gebaut) |
| 10 | Aufmaß/Auslegung | Energie-Tools | `radiators`=Wechselrichter-Altlast (Arch 4b) |
| 11 | Checkliste ausfüllen | `ProductFormulaController` | FS-07: `new Function` entfernt ✅; Route `product.formula.updates` TOT (RT-1) |
| 12 | Rechnung + festschreiben | `InvoiceController` + Accounting | `invoices` gegated ✅; DI-2 (P1) Test-Rechnung verfälscht Umsatz |
| 13 | Wiedervorlage/Task | `PersonalTaskController` (6.570 Z.) | **SEC-1b (P1): destroy/update ungegatet (IDOR)**; personal_tasks 0 Zeilen |
| 14 | Kundendienst-Ticket | `ProblemController`/`TicketTaskController` | `ticketTasks.load/timeline`-Routen TOT (RT-1) |
| 15 | Dokument/Foto hochladen | `ImageController` | **SEC-0 (P0): Dokument-Upload/Delete anonym** |

**Muster:** Die Belegkette (Aktionen 4/5/15) ist an mehreren Stellen **anonym schreib-/löschbar** — das ist der gefährlichste Alltags-Befund. Aktion 12 (Rechnung) ist die einzige sauber gegatete Kette.

## 1A-5 · JS-Konsole Top-10 — OFFEN (manueller Nachtrag)
Braucht laufenden Browser (headless nicht belastbar). **NICHT-VERIFIZIERT.** Nachzuholen an: /home, Board, Leadliste, Kundenakte, Angebots-Config, Planner, Checklisten-Ausfüllung. Kandidaten aus 1A-1: verlinkte tote Routen (57) werfen bei Klick 500 → Konsolen-Fehler.
