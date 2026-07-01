# CRM-Gesamtfunktions-Landkarte — Index (8-Zonen-Inventur)

> **Reine Analyse (nur Lesen), kein Code geändert.** Diese Datei ist die **navigierbare Landkarte** über das gesamte CRM. Sie fasst die 8 parallel erstellten Zonen-Inventuren zusammen, verweist auf die bereits früher kartierten Bereiche und listet am Ende die **echten Lücken im Fahrplan** (Bereiche ohne eigene Detail-Inventur). Stand: 8 Zonen-Agenten parallel, je eigenes Dokument.

## Die 8 Inventur-Zonen (aktive Funktionen)

| Zone | Dokument | Umfang | Status / größter Brocken |
|---|---|---|---|
| **01** Artikel / Produktkatalog | [crm-inventur-01-artikel.md](crm-inventur-01-artikel.md) | 14 Unterbereiche | aktiv · `ProductController` ~1.964 Z. |
| **02** Lager / Bestand / Beschaffung | [crm-inventur-02-lager-beschaffung.md](crm-inventur-02-lager-beschaffung.md) | 8 Unterbereiche | aktiv · Großhandels-Schnittstellen generisch (`SupplierConnectionController` ~1.106 Z.), DATANORM nur Prototyp |
| **03** Angebot / Set-Konfiguration | [crm-inventur-03-angebot-konfiguration.md](crm-inventur-03-angebot-konfiguration.md) | 8 Unterbereiche | aktiv · Master-Set-Subsystem ~6.700 Z. + `config.blade.php` ~25.000 Z. |
| **04** Auftrags-Dokumente | [crm-inventur-04-auftrag-dokumente.md](crm-inventur-04-auftrag-dokumente.md) | 5 Unterbereiche | aktiv, aber wenig eigenständig · Doku = Datei-Ablage über `images` |
| **05** Organisation / HR | [crm-inventur-05-organisation-hr.md](crm-inventur-05-organisation-hr.md) | 9 Unterbereiche | aktiv · `EmployeeController` ~3.523 Z.; viele HR-Tabellen 0 Zeilen |
| **06** Projekt / Planer / Aufgaben / Assets | [crm-inventur-06-projekt-aufgaben-assets.md](crm-inventur-06-projekt-aufgaben-assets.md) | 10 Unterbereiche | aktiv · `PlannerPlanController` **~11.080 Z.**; 3 parallele Phasen-Systeme |
| **07** Medien / Kommunikation / Rest (Auffang) | [crm-inventur-07-medien-kommunikation-rest.md](crm-inventur-07-medien-kommunikation-rest.md) | 9 Bereiche + 12 „Sonstige" | aktiv gemischt · Chat, KI-Assistent (Ollama), E-Mail (heikel), Tools |
| **08** Legacy / Old | [crm-inventur-08-legacy.md](crm-inventur-08-legacy.md) | toter Ballast | **~234 Dateien / ~58.500 Z. tot**, 0 Live-Routen |

## Bereits früher kartiert (nur Verweis — nicht Teil dieser 8 Zonen)

- **Kernprozess** Lead→Angebot→Auftrag→Rechnung (Status-Fluss, Storno): [workflow-analyse.md](workflow-analyse.md), [workflow-sollkonzept.md](workflow-sollkonzept.md)
- **Kundenprofil** (Mega-Blade + Zerlegung): [kundenprofil-architektur-bestandsaufnahme.md](kundenprofil-architektur-bestandsaufnahme.md), [kundenprofil-zerlegung-schnittplan.md](kundenprofil-zerlegung-schnittplan.md)
- **Begriffe/Datenmodell:** [glossar.md](glossar.md) (Kunde=`new_leads` · Objekt=`lead_alternative_adds` · Gewerk=`lead_product_lists` · Angebot=`offers` · Auftrag=`deals`), [customer-model-falle-befund.md](customer-model-falle-befund.md)
- **Kalender / Termine:** [kalender-termine-bestandsaufnahme.md](kalender-termine-bestandsaufnahme.md)
- **Mobile / Nuriva (API-Anbindung):** [nuriva-sync-anbindung-befund.md](nuriva-sync-anbindung-befund.md)
- **Hierarchie Objekt/Projekt, Erfassungs-Duplikat:** [hierarchie-objekt-projekt-bestandsaufnahme.md](hierarchie-objekt-projekt-bestandsaufnahme.md), [erfassung-duplikat-befund.md](erfassung-duplikat-befund.md)
- **Architektur-Weichen (Gate):** [architektur-entscheidungen.md](architektur-entscheidungen.md)

## Zonen-übergreifende Kernbefunde (aus allen 8 Zonen)

1. **Großer toter Ballast (Zone 08):** `app/Http/Controllers/Old/` = 40 Dateien / 14.805 Z., **0 Live-Routen**, 37/40 mit falschem Namespace → nicht autoloadbar. Plus ~194 Legacy-Views (~43.700 Z.). Gesamt **~58.500 Z. löschbar** (später, eigener Aufräum-Strang).
2. **Angebots-PDF ist eine tote Route (Zone 03/04):** `offers.generate-pdf` → `OfferController@generatePdf` **existiert nicht**. Angebote werden **client-seitig im Browser** gerendert; Backend speichert nur JSON in `offer_details`. Es gibt **keine** serverseitige Angebots- oder Auftragsbestätigungs-PDF. → echte **Lücke**, kein Feature.
3. **Drei parallele Phasen-/Aufgaben-Systeme (Zone 06):** klassisch (`phase_sections→task_phases→phase_activities→task_sub_tasks`, letzteres deprecated) · Kanban (`kanban_lead_tasks`) · Planner (`planner_plans→planner_items`). Dazu drei Aufgaben-Welten (personal/general/kanban) + `projects`-Universum (14 Tabellen) **neben** `planner_plans` — führende Tabelle offen.
4. **Set-/Kalkulations-Welt konsolidiert (Zone 03):** aktiv = `master_sets` + `costing_sets` (+Groups/Cart); alt = `product_master_sets`/`group_sets`/`employee_sets`/`product_sub_sets` liegen tot in `Old/`.
5. **Rechte-System (Zone 05):** per-User `user_rolls` (item_id + is_read/add/update/delete) via `User::hasPermission()` + `is_admin`-Bypass; **kein** benanntes Rollensystem, Enforcement nur stellenweise (~20 Aufrufe).
6. **„Viel gebaut, wenig genutzt" (Zone 05/07):** lokale Dev-DB weitgehend leer (0 team_members, 0 Leave/Sick/Attendance, 1 Filiale, chats=10) → Größenbewertung nach **Code-Umfang/Reife**, nicht Datenmenge.
7. **Tote Scaffolds quer durch (Zonen 04/05/07):** `DealNoteController`, `EmployeeDepartmentController`, `AttendanceController`, `EmployeeProjectCoinController`, `EmployeeMonthlyTimeBudgetController`, `FeedbackImageController`, `ImageCategoryController` — leere `//`-Rümpfe; echte Logik liegt woanders (Subordner/Planner/Mobile).
8. **Sicherheits-/Konsistenz-Notizen (Zonen 02/07):** öffentliche IDS-Callback-Routen ohne Auth; doppelte Route `/fusion/webhook/ajax` (Controller-Kollision); Sidebar-Link `chats.view` zeigt auf den **alten** Bitrix-`MessageController` statt auf den neuen Chat.

## Fahrplan-Lücken: Bereiche OHNE eigene Detail-Inventur (Kandidaten für nächste Runde)

**Groß / vorrangig** (jeweils in der Zonen-Datei als „braucht eigene Detail-Inventur" markiert):
- **Planner** (`PlannerPlanController` ~11.080 Z. + 3 Phasen-Systeme + `projects↔planner_plans`-Weiche) — Zone 06
- **Master-Set-/Kalkulations-Subsystem** (~6.700 Z. Controller + ~15k-Z.-Blade) — Zone 03
- **Produktkatalog** (`ProductController` ~1.964 Z., ~40 Routen, Preislogik) + **ArticleGroup-Hierarchie** — Zone 01
- **Großhandels-Schnittstellen** (welche Händler real über Presets angebunden; DATANORM ausbauen/entfernen) + **Prozesskette Kaufanfrage→Bestellung→Wareneingang** (fehlende „Bestellung"-Entität) — Zone 02
- **HR-Monolith** `EmployeeController` (~3.523 Z. / 52 Routen) + Attendance-Verteilung (Top-Level tot, real in Planner/Mobile) — Zone 05

**Mittel / punktuell:**
- **Auftrags-Dokumente:** Doku hängt nur indirekt (`customer_id`/`status`) am Auftrag, keine `deal_documents`-FK; `deal_notes` inkonsistent verkabelt — Zone 04
- **Notifications** (fragmentiert über viele Fachcontroller), **E-Mail** (heikel/Legacy-nah), **KI-Assistent** (Ollama), **Chat-Modul** (Alt-Menülink entwirren), **Tools/Klima/PVGIS** — Zone 07
- **Distributor-/Preis-Familie** gemeinsam mit Zone 01 abgrenzen (Katalog vs. Einkauf) — Zone 02
- **Verbindliche 01↔03-Zuordnung** für `ProductFormula`/`Temperature`/`PV`/`WP` — Zonen 01/03

**Aufräum-Strang (später, nicht jetzt):** `Old/`-Controller + Legacy-Views löschen; verwaiste `use`-Importe in `web.php`; Route-Dublette Fusion-Webhook; Menü-Dublette Chat.

---

*Reine Analyse — nichts am Code geändert. Grundlage: 8 parallele Zonen-Inventuren (Belege je in der Zonen-Datei: Controller-Zeilen, Routen, Migrationen, Sidebar, Row-Counts). Querverweise siehe „Bereits früher kartiert".*
