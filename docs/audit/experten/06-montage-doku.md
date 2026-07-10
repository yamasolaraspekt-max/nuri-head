# 06 — MONTAGE / DOKUMENTATION — Experten-Inventur (Ausführung & Nachweis)

> **Rolle:** MONTAGE-/DOKU-EXPERTE (Feld-Ausführung, Techniker-App/Nuriva, Checklisten, Bericht, Fotos/Messwerte, Nachweise, Monteur→Büro-Rückfluss).
> **Status:** READ-ONLY-Audit, Stand 2026-07-10. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Zieldatei:** Dies ist die **einzige** von diesem Auftrag geschriebene Datei. Parallele Audits (`code-audit.md`, `intelligenz-audit.md`, `automatisierungs-hebel.md`, `experten/*`) wurden **nicht** angefasst.
> **Baut auf:** `docs/audit/{code-audit,intelligenz-audit,automatisierungs-hebel}.md`, `docs/glossar.md`, `docs/architektur-entscheidungen.md` (Weiche 6 Rückfluss + Weiche 1 Phasen-Wahrheit).

## TABU (gewahrt)
Nuriva-APIs nur **gelesen/erwähnt**, **nicht** zum-Ändern bewertet (die App ist extern, Backend-API hier). Video/Jitsi, Invoice-Zone, Legacy (Bitrix/NIBE/IMAP) **nicht** inhaltlich bewertet — nur an ihren Nähten benannt.

## Beleg-Disziplin & Datenbasis
Jeder Fund trägt Datei:Zeile (firsthand-verifiziert per Sub-Agent) oder eine SQL-Messung. Die Feld-Ausführungs-Schicht ist in der lokalen Dev-Restore **praktisch leer** (firsthand-Zählung, `ticket`-DB):

| Tabelle | Zeilen | Bedeutung |
|---|--:|---|
| `planner_items` | **0** | Feld-Ausführungs-Wahrheit (Weiche 6) — **keine Live-Daten** |
| `planner_plans` | **0** | Montage-Pläne — leer |
| `phase_activities` | 49 | Template-Aktivitäten (Arbeitsschritte) — vorhanden |
| `kanban_lead_tasks` | **0** | Büro-Aufgaben-Karten — leer |
| `deal_measurements` | **0** | Aufmaß — leer |
| `radiator_installations` | **0** | Heizkörper-Aufnahme — leer |
| `product_radiator_specs` | **30** | EN-442-Katalog — **inzwischen geseedet** (war 0 laut `heizkoerper-aufnahme-formular-reuse.md`, 2026-07-07) |
| `product_formulas` | **0** | Checklisten-Formulare — leer |
| `product_formula_routing_rules` | **0** | Smartrouting-Regeln — leer |
| `planner_item_status_histories` | 5 | Status-Audit — Spuren vorhanden |
| `customer_reports` | 1 | Fertig-/Monteur-Berichte — 1 Zeile |
| `customer_histories` | 95 | Historie — lebendig |
| `images` | 0 | Feld-Fotos — leer |

**Folge (bindend für alle Reife-Urteile unten):** Alle Aussagen zum Feld-Ausführungs-Verhalten sind **strukturell (Code-Pfad)**, nicht volumengemessen. Der Montage-/Nachweis-Kern ist im Dev-Stand **pre-production** — er ist gebaut/verdrahtet, aber nicht bespielt.

---

# RASTER je Funktionsblock

Reife-Skala: **0** nicht gebaut · **1** rudimentär · **2** teilweise · **3** solide gebaut · **4** rund/durchgängig · **5** exzellent.
Automatisierungs-Reife (quer): **1** stumme Datenbank … **5** mitdenkendes Assistenzsystem (Skala aus `intelligenz-audit.md`).

---

## B1 — Techniker-App-Anbindung (Nuriva) — Feld-Ausführung laden & abschließen

**IST-FUNKTIONEN (Beleg):**
- Nuriva = die `/planner/*`-API-Fläche (Route-Header „Planner / Nuriva Integration API", `routes/api.php:224`; `'source'=>'nuriva_mobile'` in `PlannerEmployeeApiController`). Auth via `POST /planner/auth/token` (Sanctum).
- **Laden:** `GET /planner/my-work` → `PlannerEmployeeApiController::myWork` liest **ausschließlich `planner_items`** des Mitarbeiters (kein Union mit `kanban_lead_tasks`). Die einzigen von Sync erzeugten Items sind `source_type='phase_activity'` (+ task_phase/appointment/ticket/personal_task/master_set) — **nie `kanban_task`**.
- **Abschließen:** `PATCH /planner/items/{item}/complete-report` → `completeItemWithReport` (`PlannerEmployeeApiController.php:1682-1867`, alles in **einer** `DB::transaction` `:1752`). Validiert `next_step` + `due_date` (`:1696-1697`, nullable) — die Follow-up-Felder sind bereits im Abschluss angelegt.
- **Zeit/Status:** `PlannerItemStateController` play/pause/stop je (plan, item) — `started_at`/`paused_at`/`stopped_at`.
- Material/Fotos je planner_item-ID: `/planner/items/{item}/materials`, `/planner/customer-images/upload`.
- **Runtime-belegt** (`nuriva-montage-flow-durchgespielt.md`): Weg 2 (phase_activity) ist der **einzige aktionierbare** Montage-Weg. Weg 1 (kanban_task) ist doppelt tot: kein Aktions-Endpunkt (Karten-ID → HTTP 404) **und** die Bundle-Projektion `kanbanTasksPayload` **crasht** (`Unknown column 'laa.name'`, HTTP 500).

**STÄRKEN:** Ein sauberer, planner_item-ID-zentrierter Flow (laden→timen→abschließen), alles in einer Transaktion; Sanctum-Auth; Material/Foto/Kommentar je Aufgabe; `next_step`/`due_date` schon im Abschluss-Payload.

**SCHWÄCHEN:** Zwei Repräsentationen nebeneinander (phase_activity aktionierbar, kanban_task nur Anzeige-Beigabe mit **kaputtem** SQL) — ein „mehrere Wahrheiten"-Muster auch hier. Die kanban_task-Projektion ist toter/halbfertiger Ballast (eigener Fix-Kandidat, außerhalb dieses Bereichs). Kein Server-Beleg, welchen Weg die reale App aufruft (0 Daten) — Struktur zeigt zwingend Weg 2.

**REIFE: 3** (solide gebaut, aber ein toter Zweig + 0 Live-Daten). **Automatisierungs-Reife: 3** (aggregiert Quellen, schreibt zurück — s. B2).

---

## B2 — Monteur→Büro-Rückfluss (Weiche 6: PL-Prüfschritt GEBAUT — Korrektur ggü. Doku)

> **WICHTIGE KORREKTUR (firsthand, diese Session):** Die gelesenen Rückfluss-Befunde (`monteur-rueckfluss-*`, `rueckfluss-stufe1-bauplan.md`, Stand 2026-07-02: „Rück-Richtung fehlt / NOCH NICHT gebaut") sind **überholt.** Firsthand-Prüfung (DB-Schema + Code) zeigt: die Weiche-6-Kette (Feld→Büro-Karte **mit Projektleiter-Prüfschritt**) ist **gebaut** — und über den Plan hinaus um ein **Qualifikations-Gate** erweitert. Beleg: Migrationen `2026_07_02_120000_add_kanban_lead_task_id_to_planner_items`, `..._130000_add_required_qualification_id_to_phase_activities`, `..._140000_add_reviewer_employee_id_to_kanban_lead_tasks` (alle vorhanden, Spalten in `ticket`-DB verifiziert).

**IST-FUNKTIONEN (Beleg):** Ein Abschluss (`completeItemWithReport`) schreibt in **6 Stellen** in einer Transaktion:
1. `planner_items` → `status='done'`, `done_at`, `done_by_employee_id` (`:1758-1773`).
2. `planner_item_comments` → Bericht-Mirror („was Nuriva beim Reload liest", Helper `:2020`, insert `:2076`).
3. `customer_reports` → „Fertig-Bericht" (Helper `:2079`, insert `:2165-2185`) — **nur** bei `source_type ∈ {kanban_task, phase_activity, task_phase, customer_phase, planner}`.
4. `customer_histories` → bei `phase_activity`: `updateOrInsert` per `product_id`/`alternative_id` (update `:2328` / insert `:2345`).
5. `planner_item_status_histories` → Übergang alt→neu (Helper `:2350`, insert `:2362`).
6. `images` → Feld-Fotos (`/planner/customer-images/upload`).

**PLUS (neu, Stufe 1c) — Rückfluss auf die Büro-Karte mit Qualifikations-Gate:** `applyMontageQualificationRueckfluss` (`PlannerEmployeeApiController.php:1875-1938`), gerufen aus der Abschluss-Transaktion (`:1842`). Greift **nur** für `phase_activity`-Items mit gesetztem `kanban_lead_task_id` (Link) und lebender, nicht-abgeschlossener Karte:
- Liest `phase_activities.required_qualification_id` (B1-Anforderung der Tätigkeit, `:1902`).
- **Keine Anforderung** ODER **Monteur qualifiziert** (`position_qualifications.sort_order` performer ≤ required, `:1916`) → Karte **`status='done'`** + `done_at` (`markKanbanCardDone`).
- **Nicht qualifiziert** → Karte **`status='reported'`** (gemeldet) + `reviewer_employee_id` (ermittelter Prüfer, `:1922-1937`); `done_at` bleibt **null** → PL bestätigt später.
- Der Link wird im Sync gesetzt (`PlannerPlanController.php:565` cardMap, persist `:825-826`).
- **Idempotenz (Stufe 1a)** ohne DB-Unique: `storeFromTemplate` (`KanbanLeadTaskController.php:505-520`) reused/restauriert (SoftDelete-bewusst) je (Gewerk, Aktivität) genau **eine** Karte; manuelle Karten (`phase_activity_id=null`) bleiben mehrfach möglich.
- **PL-Bestätigung:** `updateStatus` (`:593`) erlaubt `reported` als Status; `reported→done` = Bestätigung. **Sichtbarkeit:** „reported"-Zähler + „Zu prüfen bei X" im Drawer (`:305-308`, `:807`).

**Die vier Rückfluss-Ziele (Yamas Praxis) — aktualisiert:**
| Ziel | Zustand | Über welche Tabelle |
|---|---|---|
| Kundenprofil-Historie + Fotos | ✅ **verdrahtet** | `customer_reports` (view-belegt `context-feed/customer-reports.blade.php:64-68`) + `customer_histories` + `images` |
| Aufgabe erledigt → Büro-Karte | ✅ **verdrahtet (neu)** — mit Melde→Prüf→Bestätigt + Qualifikations-Gate | `planner_items` **+ `kanban_lead_tasks`** (done bzw. reported+reviewer) — **sofern ein Link (`kanban_lead_task_id`) existiert** |
| Tagesbericht | ⚠️ **halb** | Item-Ebene persistiert (`done_by`/`done_at` + status_histories); `myDayReport` = **Live-Aggregation**; **0 Writes in `daily_reports`** aus Planner-Controllern (firsthand) |
| Progressbar | ✅ **gebaut (Weg A)** | s. B3 (rechnet unabhängig aus `planner_items`) |

**STÄRKEN:** Sechs Rückfluss-Pfade + Karten-Rückfluss in **einer Transaktion**; Kundenprofil echt verdrahtet; **die Weiche-6-Kernentscheidung (Melde→Prüf→Bestätigt mit PL) ist umgesetzt** — sogar intelligenter als geplant (Qualifikations-Gate: qualifizierter Monteur → auto-done, sonst → PL-Prüfung mit ermitteltem Prüfer); saubere Guards (SoftDelete, Zustands-Guard „Büro schon done → kippt nichts").

**SCHWÄCHEN / Rest-Lücken:** (1) Der Karten-Rückfluss greift **nur, wenn ein Link existiert** — d.h. wenn das Büro für die Aktivität eine `kanban_lead_tasks`-Karte angelegt hat und der Sync sie verlinkt hat; für rein feld-getriebene Items ohne Büro-Karte bleibt es beim Historie-Audit. (2) **Kein Follow-up am Feld-Abschluss:** `completeItemWithReport` ruft `FollowUpCreator` **NICHT** (firsthand: 0 Treffer); Nachfass entsteht nur vom Büro-Pfad (`KanbanLeadTaskController:656-668`, F4) — und dort **bewusst nicht** bei der PL-Bestätigung `reported→done` (Doppel-Andock vermieden, `:657`). Ein Monteur-Abschluss löst also **nie** automatisch einen Nachfass aus (= `intelligenz-audit` I-9 / `automatisierungs-hebel` H-A2, gilt weiter). (3) Kein gebuchter Tagesbericht (`daily_reports` vom Sync ungenährt). (4) `phase_activities`-Template + `customer_phase_lists` bleiben unberührt.

**REIFE: 4** (die zentrale Rück-Richtung inkl. PL-Prüfschritt + Qualifikations-Gate ist gebaut und guard-hart; Rest-Lücken sind Nachfass + Tagesbericht + Link-Abdeckung). **Automatisierungs-Reife: 3-4** (echte Melde→Prüf-Kausalität mit fachlicher Verzweigung; es fehlt nur die Nachfass-Auslösung am Feld).

---

## B3 — Progressbar / Baufortschritt (Weg A gebaut)

**IST-FUNKTIONEN (Beleg):** `KanbanLeadTaskController::context()` liefert zusätzlich `field_progress` aus `montageFieldProgress()` (`:119-165`): zählt `planner_items` mit `source_type='phase_activity'` des **Montage-Plans** (stage='montage'), **nach Anzahl** (nicht kanban_lead_tasks, nicht zeitgewichtet). `normalizePlannerItemStatus()` (`:171-183`) spiegelt done-/cancel-Aliasse. `cancelled` fällt aus dem Gesamt (Seed-verifiziert: 2 done/3 offen/1 storniert → 40 %). Anzeige `customer_profile.blade.php`: Balken/Prozent/„X von Y"/„Z offen", Titel „Montage-Fortschritt". Commit `f52ab10`, additiv, verifiziert.

**STÄRKEN:** Ehrlicher **Anzahl**-Balken (keine Scheingenauigkeit); bewegt sich beim Monteur-Abschluss (Kernziel erfüllt, Weiche-6-Design-Entscheidung „Feld-Ausführung zeigen" umgesetzt); storniert korrekt ausgeschlossen; sauberes Fundament für spätere Zeitgewichtung (nur Rechnung tauschen).

**SCHWÄCHEN (bewusst aufgeschoben, `progressbar-zeitgewichtet…md`):** **Zeitgewichtung heute unbaubar** — `phase_activities.duration` ist ein `time`-Feld (`00:00:0X`), `(int)`-Cast im Sync → 0 → Default 60 → alle Schritte wögen gleich (verkleidete Anzahl-Zählung). **Soll-Ist/Verzug unbaubar** — `planned_end_at` wird beim Sync auf NULL gesetzt (nur manuelles Planen füllt es) → kein „Soll". Beide brauchen zuerst Daten-/Planungs-Disziplin. Balken zeigt Feld-Fortschritt, die Drawer-Aufgabenliste bleibt Büro (bewusster, akzeptierter Mismatch).

**REIFE: 3** (der ehrliche Teil ist fertig & verifiziert; Zeit/Soll-Ist bewusst offen). **Automatisierungs-Reife: 3**.

---

## B4 — Nachweis-/Bericht-Persistenz (customer_reports, Historie, Fotos)

**IST-FUNKTIONEN (Beleg):**
- Manueller Bericht (Büro): `CustomerReportController::store` (`:31-82`) → `customer_reports` create (`:50-57`); ruft `FollowUpCreator::sync` (`:61-77`) **nur** bei `follow_up_outcome ∈ {nachfass, weitere_aufgaben}` (nicht bei „keine"). `report_details` (freies JSON) wird nur im `kanbanStore`-Pfad geschrieben (`:177/:189`).
- Feld-Bericht (Monteur): via `completeItemWithReport` → `customer_reports` „Fertig-Bericht" (s. B2).
- Historie: `customer_histories` (95 Zeilen, im Profil breit genutzt).
- Fotos: `images` (kunden-/objektbezogen), via `PlannerMobileCustomerImageController::upload`.

**STÄRKEN:** Zwei Bericht-Quellen (Büro + Feld) landen im **selben** Profil-Feed; `FollowUpCreator` ist die **eine** dedup-sichere Nachfass-Erzeugungsstelle (Upsert per `(source_type, source_id)`) und **greift** vom Büro-Bericht.

**SCHWÄCHEN:** Nachfass entsteht **nur vom Büro-Bericht**, **nicht vom Feld-Abschluss** (B2). `report_details` (strukturierte Mess-/Nachweis-Details) nur im kanbanStore-Pfad — der Monteur-Fertig-Bericht nutzt einen anderen Aufbau; keine einheitliche Nachweis-Struktur (Messwerte/Prüfprotokolle). Kein dediziertes Mess-/Prüfprotokoll-Modell (Inbetriebnahme-Werte etc.) außerhalb der freien JSON-Felder.

**REIFE: 3** (Bericht + Historie + Foto laufen; strukturierte Messwert-Nachweise fehlen). **Automatisierungs-Reife: 3** (Büro-Bericht → Nachfass ist echte Kausalität).

---

## B5 — Checklisten je Gewerk (product_formulas / Formular-Strang FS)

**IST-FUNKTIONEN (Beleg):** `LeadProductChecklistValueController` wählt die Checkliste **naiv** per `ProductFormula::where('product_id', …)` (`:41/:116`) — **kein** Stage-/Kontext-Routing. `SmartroutingService` (Kontext-Match Gewerk/Objekt/Phase, gebaut FS-05) wird **NICHT** aufgerufen (firsthand: 0 Treffer); die Regel-Tabelle `product_formula_routing_rules` = **0 Zeilen**. Antwortspeicherung: `filled_values` (JSON) via `updateOrCreate` (`:129-138`). FS-Strang-Stand (`backlog-formulare.md`): FS-02/04/05/07 **gebaut**, `new Function` vollständig entfernt (FS-07, sicherer Evaluator `form-safe-eval.js`), **FS-08 (Antwortspeicherung produktiv verdrahten) offen und sicherheits-gated**; die Fill-Seite ist **nicht** ins UI verdrahtet (`formular-sicherheitsbefund.md`: dormant, 0 Zeilen). Weiche 6 (Nachtrag): Kopplung `product_formulas` ↔ `phase_activities` (Arbeitsschritte) = **später, niedrige Priorität**.

**STÄRKEN:** Sichere, eval-freie Auswertungs-Engine ist portiert/gebaut (FS-03/07); `visible_if` (Alpine, CLAUDE.md-Scope-2-konform) + `VisibleIfService` server-autoritativ; `SmartroutingService` fertig gebaut — nur nicht verdrahtet.

**SCHWÄCHEN:** Live-Checklistenwahl ist **naiv** (nur product_id → „jeder sieht alles je Produkt", kein Gewerk/Objekt/Phase-Kontext); der intelligente Router liegt **tot** (0 Aufrufer + 0 Regeln) = `intelligenz-audit` I-1 / H-V2. Ausfüllung produktiv **nicht verdrahtet** (FS-08 offen, gated). Checklisten nicht an Arbeitsschritte gekoppelt → im Feld-Ablauf noch nicht eingebunden.

**REIFE: 2** (Bausteine gebaut, Fill-Seite nicht live, Routing tot). **Automatisierungs-Reife: 2** (Intelligenz existiert, brach liegend).

---

## B6 — Aufmaß (deal_measurements) + Durchreichung in Heizlast/Materialbedarf

**IST-FUNKTIONEN (Beleg):** `DealMeasurementController` persistiert `sections_snapshot` / `material_summary` / `materials_snapshot` (JSON); `saveDetail` schreibt `form_data`/`roof_data`/`pv_data`/`wp_data`/`raw_snapshot` (`:2036-2062`). Material-Delta/Vergleich rechnet `DealMeasurementMaterialController` (Snapshot vs. Bedarf).

**Durchreichungs-Befund (Kern-Frage des Auftrags):**
- **Aufmaß → Heizlast: NEIN.** `DealMeasurement*`-Controller haben **0 Treffer** auf `Heizlast`/`HeizlastRaum` (firsthand). `Energie/HeizlastController` baut ein **transientes** Ein-Zonen-Projekt, tippt Raum-/Flächenmaße **frisch** und referenziert **kein** `DealMeasurement` (= `intelligenz-audit` H-I7 / `automatisierungs-hebel` H-D2, hier bestätigt). → Dieselben Gebäudemaße werden **zweimal** erfasst; weil Heizlast transient ist, wird das Getippte nicht mal zurückgespeichert. Größter Einzel-Zeitfresser der Erfassung (10-20 min/Vorgang).
- **Aufmaß → Materialbedarf: teilweise.** Materialmengen werden **von Hand** in `materials_snapshot` gepflegt; die Maschine rechnet **nur** Delta/Vergleich, **nicht** die Menge aus der Geometrie (= H-B3, fachlich → Vorschlag, kein Automat).

**STÄRKEN:** Strukturiertes, gewerkspezifisches Aufmaß mit Snapshot-Feldern (roof/pv/wp); Material-Delta-Rechnung vorhanden.

**SCHWÄCHEN:** Kein Prefill Heizlast←Aufmaß (Doppelerfassung, sicher automatisierbar); keine Mengen-Ableitung aus Geometrie; Snapshot-Invalidierung nicht dokumentiert (Divergenz-Risiko Snapshot↔Quelle, NICHT-VERIFIZIERT).

**REIFE: 2** (Erfassung solide, aber isoliert — nicht in Auslegung/Materialbedarf durchgereicht; 0 Live-Daten). **Automatisierungs-Reife: 1-2** (stumme Ablage, keine Weiterverwendung).

---

## B7 — Heizkörper-Aufnahme (RadiatorInstallationController, Aufnahme-CRUD)

**IST-FUNKTIONEN (Beleg):** `Product/PV/RadiatorInstallationController` = Aufnahme-CRUD auf `radiator_installations` (Kunde/Objekt, Bautyp 10/11/21/22/33, Glieder, Raum/Fläche, Maße B/H/T, Nische, Ventil-Positionen Vor-/Rücklauf, Thermostatkopf, Foto). **jQuery/AJAX** (`ajaxCustomers`/`ajaxObjects`/`ajaxList`), Blade `radiator_create`/`radiator_view` (Select2, **kein Alpine** — CLAUDE.md-konform). EN-442-Felder additiv freigeschaltet: `radiator_spec_id` (exists:product_radiator_specs), `heating_circuit_id`, `q_norm_*`, `exponent_n`, `anzahl`, `anschluss_*` — `store()/update()` reichen sie via `fill()` durch (`heizkoerper-aufnahme-formular-reuse.md`).

**STÄRKEN:** Bestehendes, regelkonformes (jQuery) Aufnahmeformular per **Reuse** für die EN-442-Auslegung nutzbar (M4-a); Katalog-Link (`product_radiator_specs`) jetzt **geseedet (30 Zeilen)** → Katalog-Select nicht mehr leer. CLAUDE.md-Schutz gewahrt: `radiator.config.*` **unangetastet**; Domäne läuft über `RadiatorSpec`/`RadiatorInstallation`/`Services/Heizkoerper/*` (NICHT die Wechselrichter-Altlast `radiators`).

**SCHWÄCHEN:** Blade-Abschnitt „Auslegung/EN-442" im Modal + Edit-Populate-JS noch **offen** (Heizkörper-Strang) → die neu freigeschalteten Felder haben noch keine UI. 0 Live-Aufnahmen (`radiator_installations`=0).

**REIFE: 3** (CRUD solide + Backend EN-442-fähig; UI-Anschluss offen). **Automatisierungs-Reife: 3-4** (Heizkörper-Kern ist bereits ans Angebot verdrahtet — Referenz-Muster „Rechen-Ergebnis→Position", `automatisierungs-hebel` TEIL 2; das ist die reifste Naht in diesem Bereich).

---

# SYNTHESE — Automatisierungs-Reife gesamt

| Block | Reife | Autom.-Reife | Kernbefund |
|---|:--:|:--:|---|
| B1 Nuriva-Anbindung | 3 | 3 | Ein aktionierbarer Weg (phase_activity); kanban_task-Zweig tot/SQL-kaputt |
| B2 Rückfluss→Büro | **4** | 3-4 | Profil ✅; **Melde→Prüf→Bestätigt + Qualifikations-Gate GEBAUT** (Korrektur ggü. Doku); Rest: kein Feld-Nachfass, kein Tagesbericht |
| B3 Progressbar | 3 | 3 | Weg A (Anzahl) gebaut/verifiziert; Zeit/Soll-Ist bewusst offen |
| B4 Nachweis/Bericht | 3 | 3 | Bericht+Historie+Foto laufen; Nachfass nur vom Büro; keine Messwert-Struktur |
| B5 Checklisten | 2 | 2 | Naive Wahl; **Smartrouting tot** (0 Aufrufer/0 Regeln); Fill-Seite FS-08 offen/gated |
| B6 Aufmaß→Heizlast/Material | 2 | 1-2 | **Aufmaß NICHT in Heizlast durchgereicht** (Doppelerfassung); Menge manuell |
| B7 Heizkörper-Aufnahme | 3 | 3-4 | CRUD + EN-442-Backend da; UI offen; Kern ans Angebot verdrahtet (Referenz) |

**Gesamturteil (Automatisierungs-Reife ~3, „solide erfasst, Rückfluss gebaut, Rand-Verkettung schwach"):**
Der Montage-/Nachweis-Bereich **erfasst und protokolliert sauber** (6-Pfad-Abschluss in einer Transaktion, Profil-Rückfluss verdrahtet, ehrlicher Progressbar gebaut). Die geschäftlich zentrale **Rück-Richtung** (Feld → Büro-Karte **mit Projektleiter-Prüfschritt**, Weiche 6) ist — anders als die (auf 2026-07-02 datierte) Doku behauptet — **inzwischen gebaut** (Link-Spalte + Qualifikations-Gate + reported/Prüfer-Kette, guard-hart). Damit ist die schärfste zuvor dokumentierte Lücke **geschlossen**. Es bleiben **Rand-Verkettungen**: (a) **kein Feld-Nachfass** — der Monteur-Abschluss zündet den vorhandenen `FollowUpCreator` nicht (I-9/H-A2); (b) **Aufmaß→Heizlast nicht durchgereicht** (Doppelerfassung, H-I7); (c) **Smartrouting der Checklisten tot** (0 Aufrufer + 0 Regeln, I-1/H-V2) + Fill-Seite FS-08 gated; (d) kein gebuchter Tagesbericht (`daily_reports`). Der **billigste Reifegewinn bleibt Verdrahten, nicht Neubau** (deckungsgleich mit den Nachbar-Audits). Der reifste Punkt ist jetzt der **Rückfluss** (B2) + die Heizkörper-Naht (Kern ans Angebot verdrahtet); der leerste ist die Checklisten-Ausführung.

**Die drei größten Rest-Lücken (nach Alltags-ROI):**
1. **Feld-Abschluss → Follow-up** (B2/B4) — ein `FollowUpCreator::sync`-Call, dedup-sicher, `next_step`/`due_date` schon im Abschluss-Payload; heute 0 Treffer.
2. **Aufmaß → Heizlast-Prefill** (B6) — größter Einzel-Zeitfresser (10-20 min/Vorgang), sichere Durchreichung (gleiche physische Größe).
3. **Smartrouting-Checklisten verdrahten** (B5) — Router gebaut (FS-05), 0 Aufrufer + 0 Regeln; naive product_id-Wahl live. (Sekundär: gebuchter Tagesbericht + Link-Abdeckung feld-getriebener Items ohne Büro-Karte in B2.)

---

# Gelesen / Nicht-gelesen

**Gelesen (firsthand, Doku):** `docs/audit/{intelligenz-audit,automatisierungs-hebel}.md` (vollständig), `docs/glossar.md`, `docs/architektur-entscheidungen.md` (Weiche 1/5/6 inkl. aller Nachträge), `docs/monteur-rueckfluss-{vier-ziele,verknuepfungen}-befund.md`, `docs/nuriva-montage-{flow-durchgespielt,endpunkt-geklaert}.md`, `docs/kanban-ebenen-montage-planner-nuriva-befund.md`, `docs/progressbar-zeitgewichtung-geprueft.md`, `docs/backlog-formulare.md`, `docs/formular-sicherheitsbefund.md`, `docs/befund-b2a-heizlast.md`, `docs/heizkoerper-aufnahme-formular-reuse.md`, `docs/erfassung-duplikat-befund.md`, `docs/rueckfluss-stufe1-bauplan.md`.
**Gemessen (firsthand, SQL):** Row-Counts der 14 Kern-Tabellen (`ticket`-DB, via CNF) — s. Datenbasis-Tabelle oben.
**Verifiziert (firsthand, Code, via Sub-Agent, file:line):** `completeItemWithReport` (1682-1867, 6 Schreibstellen, `next_step`/`due_date` 1696-1697, **FollowUpCreator 0 Treffer**); `CustomerReportController::store` (31-82, sync nur nachfass/weitere_aufgaben); `RadiatorInstallationController` (jQuery-CRUD, EN-442-Felder); `DealMeasurementController` (Snapshot-Felder 2036-2062, **Heizlast 0 Treffer**); `LeadProductChecklistValueController` (ProductFormula::where 41/116, **SmartroutingService 0 Treffer**, filled_values 129-138); `montageFieldProgress` (119-165) + `normalizePlannerItemStatus` (171-183); `daily_reports` 0 Writes aus Planner-Controllern.
**Verifiziert (firsthand, diese Session, Code+Schema) — Weiche-6-Rückfluss GEBAUT:** Migrationen `add_kanban_lead_task_id_to_planner_items` / `add_required_qualification_id_to_phase_activities` / `add_reviewer_employee_id_to_kanban_lead_tasks` (alle 2026-07-02, Spalten in `ticket`-DB bestätigt); `applyMontageQualificationRueckfluss` (`PlannerEmployeeApiController.php:1875-1938`, Aufruf `:1842`); Link-Setzung im Sync (`PlannerPlanController.php:565/825`); SoftDelete-Idempotenz `storeFromTemplate` (`KanbanLeadTaskController.php:505-520`); `reported`-Status in `updateStatus` (`:593`) + Follow-up-Dedup (`:656-668`); kein DB-Unique (nur PRIMARY) — App-Idempotenz gewählt.

**Nicht gelesen / nur oberflächlich:** die vollständigen Methodenkörper von `PlannerPlanController` (~11k Zeilen — nur zitierte Sync-/Upsert-Stellen); der reale Nuriva-App-Client (extern, TABU); die Blade/JS-Laufzeit (ob clientseitig etwas verkettet, was serverseitig fehlt — z.B. Heizlast-Prefill); die Invoice-/Video-/Legacy-Zonen inhaltlich (TABU); jede Snapshot-Invalidierung; der `type-switcher` im Profil-Feed (customer_histories vs customer_reports Darstellung).

# NICHT-VERIFIZIERT

- **Alle Verhaltensaussagen sind strukturell**, nicht volumengemessen — die Feld-Ausführungs-Tabellen sind im Dev **leer** (planner_items/plans/deal_measurements/radiator_installations/images = 0). Auf einer produktiv bespielten Nuriva-Instanz kann das Bild abweichen.
- **Welchen Endpunkt die reale App aufruft** (my-work/phase_activity vs. kanban_tasks-Bundle) — Struktur zwingt Weg 2, aber kein Request-Log/App-Trace (TABU: Nuriva nicht zum-Ändern bewertet).
- **„0 Aufrufer/0 Treffer"** (SmartroutingService, FollowUpCreator im Abschluss, Heizlast in DealMeasurement) sind statische Greps — stark, aber kein Beweis gegen Reflection/String-Dispatch.
- **`(int) time`→0-Progressbar-Befund** an 5 echten `phase_activities`-Werten belegt, nicht erschöpfend über alle Formate.
- **Ob Nuriva den Ist-Timer** (`started_at`/`stopped_at`) real nutzt — Route existiert, Nutzung unbekannt (blockiert Aufwand-Soll-Ist).

# Selbstkritik

- Der Bereich ist **pre-production** in der Dev-DB → meine schärfsten Befunde (Rückfluss-Lücke, Doppelerfassung, tote Router) sind **code-strukturell** robust, aber die *Häufigkeit/Schmerz*-Gewichtung stützt sich auf Schätzungen der Nachbar-Audits, nicht auf gemessene Raten.
- Ich habe die **Nuriva-App-Seite bewusst nicht bewertet** (TABU) — damit bleibt die Endgültig-Bestätigung „Weg 2" ein Backend-Schluss, kein App-Beweis.
- Der FS-Strang-Stand ändert sich schnell (FS-07 seit 2026-07-08 gebaut, `new Function` raus) — mein „FS-08 offen/gated" ist zum Stand 2026-07-10 korrekt, aber der Strang ist in Bewegung.
- `product_radiator_specs` = 30 widerspricht dem „0"-Stand der Reuse-Doku (2026-07-07) → der Katalog wurde zwischenzeitlich geseedet; ich habe den **aktuellen** Messwert genommen, aber nicht geprüft, welcher Seeder-Lauf das war.
- **Doku-Aktualitäts-Falle (wichtigste Lehre dieses Audits):** Ich hatte B2 zunächst auf Basis der gelesenen Rückfluss-Docs (Stand 2026-07-02, „NOCH NICHT gebaut") als „Lücke" bewertet. Ein firsthand-Gegencheck (Migrationen + DB-Schema + Code `applyMontageQualificationRueckfluss`) zeigte, dass die Kette **inzwischen gebaut** ist. **Lehre:** die `docs/`-Befunde sind teils schneller veraltet als das Repo — jeder „fehlt/nicht gebaut"-Fund gehört firsthand am Code/Schema gegengeprüft, nicht aus der Doku übernommen. Die übrigen „nicht gebaut/tot"-Funde (Smartrouting 0 Aufrufer, Feld-Nachfass 0 Treffer, Aufmaß→Heizlast 0 Treffer, `daily_reports` 0) sind **firsthand in dieser Session** verifiziert, nicht aus Doku übernommen.
- Der Rückfluss-Karten-Update greift **nur bei existierendem Link** — ob in der Praxis für jede feld-getriebene Montage-Aufgabe eine verlinkte Büro-Karte existiert, ist bei 0 Live-Zeilen **NICHT VERIFIZIERT** (Abdeckungsfrage).
