# EXPERTEN-INVENTUR 10 — QUERSCHNITT (Daten · Prozess · Sicherheit)

> **Rolle:** Querschnitts-Experte. Bereichsübergreifend, nicht domänentief. **Rein lesend.**
> **Stand:** 2026-07-10. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Baut auf (gelesen, firsthand):** `docs/audit/{code-audit, intelligenz-audit, automatisierungs-hebel, 01-fehler-inventur, 02-architektur, stopp-1, p1-idor-inventur, p0-6-daten-analyse, 00-index}.md`, `docs/architektur-entscheidungen.md`. Glossar via Querverweis der Vorgänger.
> **Frisch nachgemessen (2026-07-10, read-only SQL + grep):** Rechte-Gate-Stand, Multiplikator-Aufrufer, Doppel-Wahrheiten-DB-Zustand, P0-6-Nachlauf. Wo ein Vorgänger-Befund sich seit seinem Stand geändert hat, ist das mit **[Δ seit Audit]** markiert.
> **TABU (nur an Nähten):** Nuriva-APIs, Video/Jitsi, Invoice-/Accounting-Zone (interne Qualität), Legacy Bitrix/NIBE/IMAP.

**Auftrag dieses Dokuments:** die vier Achsen zusammenführen, die *quer* durch alle Domänen laufen und deshalb in keinem Einzelbereich-Audit vollständig sichtbar werden: **(1) Datenintegrität / Doppel-Wahrheiten**, **(2) Prozess-Durchgängigkeit / Medienbrüche**, **(3) Berechtigung / DSGVO**, **(4) die vorhandenen Multiplikatoren**. Kein neuer Domänen-Befund — die Synthese der Querbrüche.

---

# TEIL A — IST-BEFUND (belegt)

## A1 · DOPPEL-WAHRHEITEN-REGISTER (das Querschnitts-Kernproblem Daten)

Prinzip aus `CLAUDE.md` / `docs/accounting/umsatzdefinition.md`: **eine Wahrheit je Sachverhalt.** Der Ist-Zustand verletzt das an mehreren Stellen gleichzeitig. Register mit *frisch gemessenem* Live-Zustand (2026-07-10):

| # | Sachverhalt | Konkurrierende Quellen | DB-Zustand heute | Live doppelt-produktiv? | führende Wahrheit (Weiche) | Beleg |
|---|---|---|---|---|---|---|
| DW-1 | **Phase je Gewerk** | 12 Spalten in **einer** `lead_product_lists`-Zeile (`status, stage, old_stage, stage_mode, product_stage_id, product_task_phase_id, work_status, stage_history, product_stage_history, lead_stage_id, lead_stage_sub_stage_id, offer_acceptance_status`) | `status`≡`stage` (Divergenz 0), `lead_stage_id` durch Hook konsistent abgeleitet | **teilweise** — 1 Zeile trägt ≥6 Phasen-Repräsentationen, kein Constraint synchronisiert | `lead_stages`/`_sub_stage_id` (Weiche 1) | `02-architektur.md §1a`, `01-fehler DI-5` |
| DW-2 | **Stage-Katalog** | `lead_stages` (10) ↔ **`offer_kanban_stages` (30)** | **beide befüllt, 2026-07-10 gemessen: 30 vs 10** | **JA — die einzige echt live-parallele Stage-Quelle** | `lead_stages` | `02-architektur.md §1b` + frisch nachgemessen |
| DW-3 | **Stage-Tabellen-Wildwuchs** | `stages`(0, aber 225 Code-Refs), `phase_stages`, `customer_stages`, `customer_phase_stages`, `custom_process_stages`, `lead_stage_sub_stages` | leer, aber verdrahtet (`StageController::create` schreibt aktiv auf leere `stages`) | nein (dormant-but-wired) | `lead_stages` | `02-architektur.md §1b`, `01-fehler DI-4` |
| DW-4 | **Kunde** | `new_leads` (52) ↔ **`customers` Zombie** | **`customers` = 0 Zeilen, 2026-07-10 bestätigt**; 74 Spalten; 19 FK-Ziele zeigen darauf | nein (0 Schreiber, 2 Nicht-Old-Leser: `CustomerHeatingCircuitController:87`, `ChecklistRoomController:138`) | `new_leads` | `02-architektur.md §1d`, `code-audit 1D-B`, `01-fehler DI-3` |
| DW-5 | **`customer_id`-Doppeldeutigkeit** | dieselbe Spalte referenziert **ZWEI** Tabellen: `new_leads` (47 FK) + `customers` (19 FK); Aliase `lead_id` (10), `new_lead_id` (1) | strukturell | latent (Fehl-Join-Verdacht bei jedem generischen `customer_id`-Zugriff) | `new_leads` als einzige Kunden-Identität | `code-audit 1D-B`, `intelligenz C`-nah |
| DW-6 | **Auftrags-Fortschritt** | `deals`: 5 Felder (`status, deal_status, measurement_status, project_status, status_msg`) + Rückgriff auf `lead_product_lists.stage` | `project_status` = DE-Freitext (Montage/offen/in Bearbeitung/abgeschlossen); 13× `active`, 1× `deal` | **JA** — keine führende Auftrags-Phase | eine führende Phase (Weiche 1, noch nicht gebaut) | `intelligenz C1`, `01-fehler DI-5` |
| DW-7 | **Umsatz/Rechnung** | `invoices` (führend) ↔ `deal_invoices` (0) | `deal_invoices` = 0 Zeilen, nur 4 **Kommentar**-Refs | **nein — Weiche eingehalten** ✅ | `invoices` | `02-architektur.md §1c`, `code-audit 1D` |
| DW-8 | **Status-Werte-Kontrakt** | systemweit **139 varchar-`status`-Spalten** vs. 11 enum; 202 hartkodierte Status-Literale in Controllern | Freitext, DE/EN gemischt (`new_leads.status`: Lead/Active/QUALIFIZIERT/„Von Junk wiederhergestellt") | quer über alle Domänen | Enums/Referenztabellen je Kern | `code-audit 1D-B`, `02-architektur §4`, `intelligenz C4` |
| DW-9 | **`lead_stage_id`-Ableitung** | Kanon `LeadProductList::deriveLeadStageId()` (mit Fold) ↔ `NewLeadsController::normalizeCompanyStage()` (**ohne** Fold) ↔ dritte `match()`-Variante (Tippfehler-Key `archiv`) | **[Δ] `normalizeCompanyStage` weiter vorhanden — 3 Treffer, 2026-07-10; I-15 NICHT umgesetzt** | latent still divergierbar (heute in Daten konsistent, weil über Hook geschrieben) | `deriveLeadStageId` (Kanon) | `code-audit 1.4`, `intelligenz C3` |
| DW-10 | **Rechnungs-Schienen (Alt-2)** | `radiators` = Wechselrichter-Altlast (fehlbenannt) | strukturell, per `CLAUDE.md` „DO NOT DOCK" | nein — dokumentiert | `RadiatorSpec`/`RadiatorInstallation` | `02-architektur §4`, `CLAUDE.md` |

**Querschnitts-Muster (die Wurzel):** Immer dasselbe — **„Danebenbau statt Ablösen"**. Eine neue Quelle wurde gebaut, die alte nie belegt stillgelegt. Von 10 Registern sind **2 echt live-doppelt-produktiv** (DW-2 `offer_kanban_stages`, DW-6 `deals`-Statusfelder), **1 latent gefährlich** (DW-9 Fold-Divergenz), der Rest dormant/dokumentiert oder bereits Weiche-konform (DW-7). **FK-Waisen (DI-1): [Δ] behoben** — `p0-6-daten-analyse.md` ausgeführt (lokal), 2026-07-10 nachgemessen: **0 aktive Waisen** (19→0 soft-deleted), Test-Rechnung `TST-OPEN-2337` entfernt (invoices aktiv 11→10, Umsatz −1.000 €). **Nur lokale Dev-DB — Hetzner-Live unberührt (NICHT-VERIFIZIERT dort).**

## A2 · PROZESS-DURCHGÄNGIGKEIT — die Medienbrüche der Kern-Ketten

Die festgeschriebene Kette (`CLAUDE.md`, `00-index`): **Kunde → Objekt → Gewerk → Angebot → Auftrag → Rechnung**, 6 Phasen (Lead·Angebot·Auftrag·Montage·Abnahme·Abschluss). Strukturelle Ursache aller Brüche: **kein fachlicher Model-Observer im gesamten `app/`** — jede Folge muss explizit im Controller stehen (`intelligenz-audit ACHSE 1`). Der Stage-Move ist der saubere, aber ungenutzte Ereignis-Punkt.

| Ketten-Übergang | Auto-Folge? | Bruch | Automatisierungsgrad (1–5) | Beleg |
|---|---|---|---|---|
| Anfrage→Lead | Erfassung + Duplikat-Check | Lead löst keine Folge aus; Status-Zoo | 2 | `intelligenz K1`, Grad-Tabelle |
| Lead→Angebot | **NEIN** | kein Task/Reminder/Draft beim Übergang | 1–2 | `LeadOverviewController:5140-5142` |
| Angebot→Auftrag | **HALB** | `deals`-Zeile + Angebots-Gate (umgehbar) + Konkurrenz-Storno; aber **kein Kickoff** (Materialliste/Kalkulation/Aufgaben) | 2–3 | `intelligenz K2`, `DealController:3702-3709` |
| Auftrag→Montage | **NEIN (bewusst manuell)** | Kuratier-Prinzip, Yama-Entscheid Weiche 6 — korrekt | 2 (by design) | `PlannerPlanController:7158`, `architektur-entscheidungen` Weiche 6 |
| Montage→Abnahme | HALB | Feld-Rückfluss (Progressbar Weg A **[Δ] gebaut**, Commit f52ab10); Melde→Prüf-Kette Büro-Karte noch offen (Link fehlt) | 2 | `stopp-1`, Weiche 6 Nachträge |
| Abnahme→Rechnung | **NEIN** | reiner Medienbruch, kein Draft-Vorschlag | 1 | `intelligenz K4`, `InvoiceController:218` |
| Rechnung→Zahlung→Abschluss | HALB | Zahlungsstatus in `invoices` sauber, aber **kein Rücklauf** auf Auftragsstatus (4 Deals `active` trotz voll bezahlt); Storno-`destroy` stark | 2 | `intelligenz C2/K5` |
| Wartung/Follow-up | **JA (Vorbild, mit Lücken)** | `FollowUpCreator` = 1 Erzeugungsstelle; **dashboard-only** + 2 tote Herkunfts-Slots | 3 | `intelligenz K6` |
| **Rechen-Assistenz (Heizlast/Form/Anforderungsprofil)** | **JA** | Operanden-Gate, Plausi-Bänder, Registry-Ablehnung | **4** | `intelligenz P7`, Grad-Tabelle |

**Zusätzliche Querschnitts-Redundanzen (Erfassungs-Doppelarbeit):**
- **Heizlast tippt das Aufmaß neu** (`Energie/HeizlastController` transient, kein `DealMeasurement`-Bezug) — der **teuerste Einzel-Medienbruch** (~10–20 min/Vorgang). `automatisierungs-hebel H-D2 / I-7`.
- **Adresse Kunde↔Objekt** doppelt erfasst (`new_leads` + `lead_alternative_adds` je street/plz/city). `H-D1 / I-17`. *Positiv:* die Geld-Kette (offers/deals/invoices) ist FK-sauber durchgereicht, **keine** denormalisierten Namen — diese Achse ist im Beleg-Fluss sauber.
- **3–4 parallele Angebots-Wizards** (`config.blade.php` 1,16 MB + copy/old-Varianten). `Rd4 / code-audit 1.5`.

**Bruch-Fazit:** Der umsatztragende Alt-Kern ist **eine überwiegend stumme Datenbank (Grad ~2)** — speichert sauber, stößt Folgen nicht an. Die junge Rechen-Zone ist ein **echtes Assistenzsystem (Grad ~4)**. Der billigste Sprung liegt nicht im Neubau, sondern im Verdrahten der vorhandenen Auslöse-/Ableitungs-Bausteine an den Stage-Move.

## A3 · BERECHTIGUNG / DSGVO — Rechte-Stand (frisch gemessen 2026-07-10)

**Ausgangslage (Audits):** systemischer Autorisierungs-Mangel — `p1-idor-inventur` zählt **232 verifizierte IDOR-Kandidaten**; `code-audit 2.2a`: nur 5/1211 Schreibrouten gegatet, Rechte-Fundament (`hasPermission`, Action→CRUD-`match`) **gut gebaut, aber dormant**.

**[Δ seit Audit] — die P0-Runde + P1-IDOR-Bündel sind gelaufen** (git log, 2026-07-10):

| Schritt | Commit | Wirkung | Beleg |
|---|---|---|---|
| P0-1 anonyme Schreibrouten | `a6063cf` | `auth` auf 40 Route-Gruppen | `stopp-1` |
| P0-2 Account-Takeover | `e554817` | `updatePassword` gegatet | `stopp-1` |
| P0-3 HR/Lohn/Medizin-IDOR | `f5803ed` | `is_admin`-Gate, 13 Methoden | `stopp-1` |
| P0-4 Belegkette-Löschung | `13ef2bc` | `Customer,delete`, 4 Methoden | `stopp-1` |
| P0-5 Massenlöschungen | `fcf0d3b` | `is_admin`, 3 bulkDelete | `stopp-1` |
| P1-IDOR-1a Owner-Gate | `f939130` | PersonalNote (12 Methoden) | git |
| P1-IDOR HR | `9f66a87` | `permission:Employee`, 14 Profil-Controller | git |
| P1-IDOR Personal | `35aee30` | Department/Position | git |
| P1-IDOR Customer | `cf416ea` | `permission:Customer`, 17 Offer-/Lead-Controller | git |
| P1-IDOR Product | `76d41dc` | `permission:Product`, 35 Katalog-/Lager-Controller | git |
| P1-IDOR Ticket/Inquiry/Finance | `5ef5240` | 18 Controller | git |

**Gemessener Gate-Stand heute (2026-07-10):**
- **98 Controller** tragen `permission:`-Middleware im Konstruktor (`$this->middleware('permission:X,action')->only([...])`) — nicht als Route-Middleware (deshalb zeigt `web.php` weiter nur 5). 97 Controller nutzen `CheckUserPermission`/`hasPermission`.
- Verteilung der Keys: **Product 106 · Customer 67 · Employee 66 · Problem 31 · Finance 12 · Inquiry 9 · Users 4**. Deckt die vom Auftrag genannten Bündel **HR/Customer/Product/Problem/Finance**.

**⚠ Die entscheidende Querschnitts-Restgrenze (RBAC-01, aus `p1-idor-inventur §Architektur-Weiche`):** Das Rechte-**System** ist weiterhin **dormant** — außer dem `is_admin`-Bypass hat **niemand Keys** (`Leads/HR/Lager/Stammdaten/Finance…` sind **nicht geseedet**). Konsequenz: Die frisch gesetzten `permission:`-Gates lassen bei leerer Rollen-Zuweisung **nur Admins durch** — Nicht-Admin-Mitarbeiter wären ausgesperrt. Die Absicherung ist code-seitig da; **die Rollen-/Key-Aktivierung ist ein eigener, noch offener Yama-Posten**. Bis dahin läuft das System de facto als „Admin sieht alles, Nicht-Admin gesperrt" statt fein-granular.

**Zeilen-/Mandanten-Trennung (DSGVO-relevant, offen):** Es gibt Branch/Filial-Struktur, aber **keine erzwungene Zeilen-Trennung** — kein `branch_id`-Scope auf den Kern-Tabellen; die 2. Filiale bräuchte additive Spalten + Scopes (`code-audit 1D-D`). **Kaskadierender DSGVO-Löschpfad** über die ~80 `customer_id`-Tabellen: **NICHT-VERIFIZIERT** vorhanden — SoftDelete auf `new_leads` lässt Kinder als Waisen (FK-Constraints greifen nicht auf `deleted_at`). Sensible Daten (Lohn `salaries`, Krankheit `employee_sicks`) sind lokalisiert; **keine Klartext-Secrets** gefunden (`code-audit 1D-A`, positiv).

## A4 · DIE VORHANDENEN MULTIPLIKATOREN (Querschnitts-Fundamente, frisch nachgezählt)

Der billigste Automatisierungsgewinn liegt im **Verdrahten des bereits Gebauten**. Aufrufer-Zählung 2026-07-10 (grep über `app/`, `routes/`, `resources/`):

| Multiplikator | Zustand heute | Was er quer ermöglicht | Speist |
|---|---|---|---|
| **FollowUpCreator** (`Services/FollowUp/`) | **verdrahtet**, 8 Controller-Refs; 1 Erzeugungsstelle, Upsert `(type,source_type,source_id)`; **2 tote Slots** (`lead_product_list`, `appointment_report`) | eine neue Auslöse-Quelle = ein `sync()`-Call, dedup-sicher | H-A1, H-A2, H-V1 |
| **Stage-Move-Punkt** (`LeadOverviewController:5140`) | sauberer Ereignis-Punkt, **0 fachliche Folge** | der ideale, ungenutzte Auslöse-Hook für **jede** Phasen-Kausalität | H-A1, H-A4, H-A5 |
| **SmartroutingService** (`Services/Form/`) | **[bestätigt] 0 Aufrufer** (2026-07-10); Regel-Tabelle `product_formula_routing_rules` = 0 Zeilen | kontextgenaues Formular-Routing (Gewerk/Objekt/Phase) statt naivem Produktfilter | H-V2 / I-1 |
| **PlausibilityService** (`Services/Form/`) | **[bestätigt] 0 echte Aufrufer** (2026-07-10; der eine Treffer ist ein Docblock-Kommentar) | Sanity-Warnungen (negative Fläche/Menge, Einheiten-Mix) auf jeder Checklisten-/Aufmaß-Rechnung, ein Call, kein Schema-Change | I-2 |
| **Anforderungsprofil + `SchluesselRegistry`** | Registry-Guard aktiv (`saving`-Hook wirft); Wiring als Auslegungs-Brücke **0** | audit-sicheres „geschätzt vs. gemessen"-Register als Brücke Auslegung→Angebot | H-B5, H-B4 |
| **Auslegungs-Kerne Heizkörper** (`Services/Heizkoerper/`) | **BEREITS verdrahtet** — schreibt `DealMeasurementItem` aus `CompatibilityService`+`RadiatorPerformanceService` | **Referenz-Muster** „Rechen-Ergebnis → Angebots-Position" (beweist, dass das Muster im Repo trägt) | Muster für H-B5 |
| **FK-Kanban-Hook** (`LeadProductList::booted`+`deriveLeadStageId`) | aktiv, deckt alle Eloquent-Schreiber; Fold + Fallback + Stale-Guard | hält `lead_stage_id` heute konsistent (DW-1/DW-9 latent, nicht akut) | Phasen-Wahrheit |
| **Accounting/BuchungsEngine** | aktiv, dockt an `invoices`; Transaktion + `lockForUpdate` + Maker-Checker + Balance-Gate; GoBD-getestet | die einzige Zone mit erzwungenen Invarianten — Vorbild für Transaktions-Disziplin | (TABU-Naht) |
| **Vorlagen-System Angebot** (`OfferTemplatePickerController`) | gebaut (`match_score`, `department_id`-skaliert) | Auto-Vorschlag der Gewerk-Vorlage | H-V3 |
| **`department_id` auf Gewerk** | **52/52 gefüllt** | Zuständigkeits-Ableitung „Gewerk→Abteilung→Owner" sicher fahrbar | H-V1 |

**Kernbotschaft:** **Fünf** fertige Intelligenz-/Absicherungs-Schichten liegen ganz oder teilweise brach (SmartroutingService, PlausibilityService, Anforderungsprofil-Brücke, 2 FollowUpCreator-Slots, Vorlagen-Auto-Vorschlag) — und der Heizkörper-Pfad beweist das Andock-Muster. Das ist der günstigste Querschnitts-Hebel des ganzen Audits.

---

# TEIL B — STÄRKEN (was quer schützt/trägt)

1. **Der FK-Kanban-Hook ist der beste Querschnitts-Schutz** — eine abgeleitete Phasen-Wahrheit in EINEM Model-Hook (Fold + Fallback + Stale-Guard) hält trotz 12 konkurrierender Statusspalten die `lead_stage_id` heute konsistent. Er neutralisiert DW-1/DW-9 im laufenden Betrieb.
2. **Die Geld-Kette ist FK-sauber durchgereicht** — Kunde/Objekt/Positionen werden nie abgetippt, sondern per FK referenziert; `invoices` ist als einzige Umsatz-Wahrheit etabliert (DW-7 Weiche-konform, `deal_invoices` sauber stillgelegt). Die kritischste Datenintegrität (Geld) ist die stabilste Achse.
3. **Das Operanden-Gate als hausweiter Maßstab** — `FormulaEvaluationService` / `AnforderungsprofilWert::saving` rechnen nie mit erfundenen Werten weiter, markieren „geschätzt". Ein echter, dokumentierter Qualitätsanker, an dem die schwachen Stellen (PLZ-Default P5, Alt-Kern-Plausi) gemessen werden.
4. **Absicherung ist code-seitig massiv vorangekommen** — P0-Runde komplett + 5 P1-IDOR-Bündel; 98 Controller gegatet. Die anonymen Schreibrouten und der Account-Takeover sind geschlossen.
5. **Erzeugungs-Disziplin als Prinzip** — FollowUpCreator (1 Stelle, Upsert-Key), Seeder-Marker/Teardown, Registry-als-Vertrag, Migration-`down()`: die junge Zone zeigt reproduzierbar, wie „eine Wahrheit / reversibel / selbst-markierend" aussieht.
6. **Datenintegrität akut entschärft** — FK-Waisen (P0-6) lokal bereinigt, Test-Rechnung raus; Umsatz-Skew (+1.000 €) beseitigt.

---

# TEIL C — SCHWÄCHEN (was quer bricht)

1. **[P1] Rechte-System dormant trotz gesetzter Gates** — die 98 `permission:`-Gates greifen ohne geseedete Rollen als reines „Admin-oder-gesperrt". Die feingranulare Autorisierung existiert als Code, nicht als Betrieb. **Größte offene Querschnitts-Restgrenze** (eigener Yama-Aktivierungs-Posten: Keys + Rollen seeden).
2. **[P1] 2 echt live-parallele Doppel-Wahrheiten** — `offer_kanban_stages` (30) ↔ `lead_stages` (10) und die 5 `deals`-Statusfelder. Jede Auswertung/Cockpit baut auf wackeligem Grund, bis Weiche 1 gebaut ist.
3. **[P1] Prozess-Vorwärts-Kausalität fehlt strukturell** — kein Model-Observer; Lead→Angebot, Angebot→Auftrag-Kickoff, Abnahme→Rechnung sind Medienbrüche. Der Nutzer trägt die Ketten-Verantwortung im Kopf.
4. **[P1] Erfassungs-Redundanz Heizlast↔Aufmaß** — dieselben Maße zweimal getippt, transient nicht zurückgespeichert; teuerster Einzel-Vorgang.
5. **[P1] Werte-Kontrakt fehlt fast überall** — 139 varchar-`status`-Spalten, 202 Literale, DE/EN-Mix. DW-8 macht jede Gruppierung fragil und ist die Quelle stiller Divergenz.
6. **[P1] `customer_id`-Doppeldeutigkeit + Zombie `customers`** — dieselbe Spalte auf zwei Tabellen (47 vs. 19 FK); die 0-Zeilen-Tabelle steht als FK-Ziel im Weg. Semantik-Falle bei jedem generischen Kunden-Zugriff; Stilllegung ist eigener beauftragter Posten.
7. **[P1] Fehlende Transaktions-Klammer** — nur 96/387 Controller nutzen `DB::transaction`; Mehr-Tabellen-Schreiber (die 3 `lead_product_lists`-Muster) können teil-schreiben.
8. **[P2] DW-9 Fold-Divergenz noch offen** — `normalizeCompanyStage` weiter im Code (3 Treffer); still divergierbare Zweit-Wahrheit, I-15 nicht umgesetzt.
9. **[P2] DSGVO-Kaskaden-Löschung + Zeilen-Trennung nicht verankert** — kein erzwungener Kunden-Löschpfad, kein `branch_id`-Scope; SoftDelete-FK-Falle lässt Waisen.
10. **[P2] Multiplikatoren brach** — SmartroutingService/PlausibilityService 0 Aufrufer, Anforderungsprofil-Brücke ungewirt, 2 FollowUpCreator-Slots tot. Intelligenz liegt ungenutzt.

---

# TEIL D — REIFE (Querschnitts-Urteil je Achse)

| Querschnitts-Achse | Reife | Begründung |
|---|---|---|
| **Datenintegrität (Geld-Kette)** | 🟢 hoch | FK-sauber, invoices führend, Waisen bereinigt |
| **Datenintegrität (Status/Phase)** | 🟡 mittel | Hook schützt akut, aber 12+5 Statusspalten + 2 live-parallele Quellen bis Weiche 1 |
| **Prozess-Durchgängigkeit (Alt-Kern)** | 🔴 niedrig | Grad ~2, strukturell kein Auto-Trigger, teure Medienbrüche |
| **Prozess-Durchgängigkeit (Rechen-Zone)** | 🟢 hoch | Grad ~4, Operanden-Gate, Andock-Muster bewiesen |
| **Berechtigung (Code)** | 🟡 mittel-hoch | P0 komplett, 98 Controller gegatet |
| **Berechtigung (Betrieb/DSGVO)** | 🔴 niedrig | Rechte-System dormant (Keys nicht geseedet), keine Zeilen-Trennung, Löschpfad NICHT-VERIFIZIERT |
| **Multiplikatoren (Bau-Qualität)** | 🟢 hoch | sauber gebaut, DI, getestet, reversibel |
| **Multiplikatoren (Nutzung)** | 🔴 niedrig | fünf Schichten brach |

**Gesamtbild:** Zwei Welten quer durch jede Achse — die **junge Zone** (Rechen-Kerne, Accounting, Form-Engine, Absicherungs-Fundamente) ist reif und trägt; der **umsatztragende Alt-Kern** (Kunde/Kanban/Angebot/Auftrag/Task) ist prozess-stumm, status-mehrdeutig und betrieblich noch nicht fein-autorisiert. Der Weg ist durchgängig **Strangler + Verdrahten**, nicht Rewrite.

---

# TEIL E — AUTOMATISIERUNGS-REIFE der Querschnitts-Fundamente

Kann das System *quer* mitdenken (Kausalität, Plausibilität, Konsistenz, Routing) — und stehen die Fundamente, um es billig zu heben?

- **Fundament vorhanden, Nutzung fehlt (der Regelfall):** Erzeugungsstelle (FollowUpCreator), Auslöse-Punkt (Stage-Move), Kontext-Router (Smartrouting), Sanity-Prüfer (Plausibility), Ableitungs-Anker (`department_id` 52/52, `payment_terms`) — **alle gebaut, mehrheitlich unverdrahtet.** → Der Automatisierungsgrad des Alt-Kerns lässt sich **von ~2 auf ~3–4 heben, überwiegend durch Verdrahten**, nicht Neubau.
- **Konsistenz-Wächter fehlen als Kategorie:** Es gibt **keine** Cross-Table-Widerspruchs-Checks (z.B. „Auftrag active trotz bezahlt", „junk-Storno ohne Rechnungs-Storno") als laufende Wächter — sie müssten erst als Multiplikator gebaut werden. Der Hook-Ansatz (eine Ableitung zentral) ist das Muster dafür.
- **Grenze bewusst gezogen (Operanden-Gate):** Fach-/Rechtsentscheidungen (Rechnungs-Festschreibung, Materialmenge, Heizlast→Angebot, finale Zuweisung, Montageplan-Erzeugung) bleiben **Vorschlag + Bestätigung** — das ist Governance, kein Reifedefizit.
- **Reifegrad-Fazit:** Die Automatisierungs-**Fähigkeit** ist im Repo bereits vorhanden (Grad-4-Bausteine existieren); die Automatisierungs-**Durchdringung** des Kerns ist niedrig, weil Verdrahtung und Rechte-Aktivierung fehlen. Beide sind Aufwand S–M, nicht L.

---

# GELESEN / NICHT-GELESEN

**Gelesen (firsthand, vollständig):** `docs/audit/{code-audit, intelligenz-audit, automatisierungs-hebel, 01-fehler-inventur, 02-architektur, stopp-1, p1-idor-inventur, p0-6-daten-analyse, 00-index}.md`; `docs/architektur-entscheidungen.md` (Weichen 1–6 + alle Nachträge).
**Frisch nachgemessen (2026-07-10, read-only):** Rechte-Gate-Stand (`grep` `permission:`-Middleware: 98 Controller, Key-Verteilung; `git log` der P0/P1-Commits); Multiplikator-Aufrufer (SmartroutingService=0, PlausibilityService=0 echt, FollowUpCreator=8, `normalizeCompanyStage`=3, `product_formula_routing_rules`); Doppel-Wahrheiten-DB-Zustand via CNF (`offer_kanban_stages`=30, `lead_stages`=10, `customers`=0, `lead_alternative_adds`=71, aktive Waisen=0, `invoices` aktiv=10).
**Nicht gelesen / nur via Vorgänger-Beleg übernommen:** die Methodenkörper der 387 Controller (nur Groß-Controller in den Vorgänger-Audits); `docs/glossar.md` im Volltext (nur über Querverweise der Vorgänger); die 232-Zeilen-IDOR-Volltabelle (nur Bündel-Summen); Frontend-JS/Blade zur clientseitigen Verkettung; TABU-Zonen inhaltlich.

# NICHT-VERIFIZIERT

- **P0-6 nur lokal** — Waisen-Bereinigung + Test-Rechnung auf lokaler Dev-Restore; **Hetzner-Live-Zustand NICHT-VERIFIZIERT** (dort eigener Re-Check + Backup nötig, `p0-6-daten-analyse`).
- **Datenbasis dünn** — Dev-Restore zu ~82 % leer (363/442 Tabellen 0 Zeilen); alle Konsistenz-/Doppel-Wahrheiten-Aussagen sind strukturell + Stichprobe (11–71 lebendige Zeilen), nicht volumengemessen. An Prod (~3000 Kunden) können Divergenzen zahlreicher sein.
- **„0 Aufrufer" = statischer grep** (SmartroutingService, PlausibilityService, Anforderungsprofil-Brücke) — stark, aber kein Beweis gegen Reflection/String-Dispatch.
- **Gate-Wirksamkeit im Betrieb** — die 98 gesetzten `permission:`-Gates sind code-belegt, aber ihre **reale Zugriffs-Wirkung ist NICHT-VERIFIZIERT** (kein Feature-Test dieser Runde gelesen; Rollen-Seed fehlt → nur Admin-Pfad de facto testbar).
- **DSGVO-Kaskaden-Löschpfad** über ~80 `customer_id`-Tabellen — Existenz NICHT-VERIFIZIERT.
- **Clientseitige Verkettung** (Heizlast-Prefill, Angebots-Anstoß, Zuständigen-Vorschlag) — serverseitig widerlegt, clientseitig NICHT-VERIFIZIERT.

# SELBSTKRITIK

- **Synthese, nicht Erst-Erhebung:** Dieses Dokument aggregiert vier Vorgänger-Audits + einen frischen Mess-Sweep. Wo Vorgänger sich irren (z.B. dünne Datenbasis), erbt der Querschnitt den Irrtum — ich habe nur die *quer* relevanten Zahlen nachgemessen, nicht die Domänen-Belege der Vorgänger nachgeprüft.
- **Rechte-Stand ist der volatilste Befund:** Die Gate-Zählung (98) ist ein Momentbild vom 2026-07-10; die entscheidende Frage „greifen die Gates *für Nicht-Admins*" hängt am ungeprüften Rollen-Seed — hier ist meine Aussage bewusst konservativ („dormant, nur Admin-Pfad").
- **Frequenz fehlt:** Ohne Prod-Raten ist die Priorisierung der Medienbrüche/Multiplikatoren relativ (Seed-Häufigkeit), nicht absolut — deckungsgleich mit der Selbstkritik von `automatisierungs-hebel.md`.
- **TABU respektiert:** Invoice-/Accounting-Zone, Nuriva, Video, Legacy nur an den Nähten (fehlende Brücke Abnahme→Rechnung; BuchungsEngine als Transaktions-Vorbild genannt, nicht inhaltlich bewertet).
