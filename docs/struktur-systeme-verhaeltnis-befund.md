# Die drei „Struktur"-Systeme und ihr Verhältnis — Detail-Befund

> **Reine Analyse (nur Lesen), kein Code geändert.** Ziel: klären, wie die drei Struktur-Systeme (A Phasen/Aktivitäten · B Formular-Felder · C Kanban/Planner) zusammenhängen und **wo Yamas Prinzip „Phase → Aufgabe → Arbeitsschritt" heute lebt**, damit die Prozess-Struktur auf dem Vorhandenen aufbaut statt daneben. Begriffe: `glossar.md` (Gewerk=`lead_product_lists`, Objekt=`lead_alternative_adds`, Kunde=`new_leads`, Produkt-Bezug=`product_id → article_groups`). Verbindliche Phasen (Weiche 1): Lead→Angebot→Auftrag→Montage→Abnahme→Abschluss.
>
> **Kurzfazit:** **A ist die natürliche Heimat** von Phase→Aufgabe→Arbeitsschritt (Template je Produkt) und ist **bereits an die Lead-Phasen angebunden** (`task_phases.lead_stage_id`, seit 2026-06). **B (Felder) liegt daneben — ohne jede Verbindung zu A.** **C ist die Ausführung — aber gleich DREIFACH** (customer_phase_lists · kanban_lead_tasks · planner_items instanziieren dasselbe A parallel). Das ist dasselbe „mehrere Wahrheiten"-Muster wie beim Status. **Was Yama entscheiden muss:** welches Ausführungssystem die *eine* Wahrheit ist, und ob Felder (B) an Arbeitsschritte (A) gekoppelt werden.

---

## System A — `task_phases` / `phase_activities` / `phase_sections` (das TEMPLATE je Produkt)

**Zweck:** die *definitorische* Prozess-Vorlage — pro Produkt (Gewerk-Typ) wird festgelegt, aus welchen Phasen, Aufgaben und Schritten der Ablauf besteht. Menü **Konfiguration → „Arbeitsschritte"** (`task_phase.index`), Controller `Phase/TaskPhaseController` (index:28), `Phase/PhaseActivitiesController`, `Phase/LeadTaskPhaseManagementController`.

**Tabellen & Hierarchie (alle je `product_id → article_groups`):**
| Ebene | Tabelle | Kern-Spalten | = Yamas Ebene |
|---|---|---|---|
| Prozess-Variante | `phase_sections` (`2023_08_31_060955`) | `product_id`, `phase_section`, `sort_order` | *„Service"/Variante (über Phase)* |
| **Phase** | `task_phases` (`2023_08_31_060956`) | `product_id`, `section_id`, `phase_name`, `stage`, `stage_id→stages`, **`lead_stage_id`/`lead_sub_stage_id`** (2026-06), `order` | **Phase** |
| **Aufgabe** | `phase_activities` (`2023_08_31_060957`) | `phase_id→task_phases`, `product_id`, `section_id`, `title`, `duration`, `description`, `photo`, `priority`, `percent`, `sort_order`, **`parent_id→self`** | **Aufgabe** |
| **Arbeitsschritt** | `phase_activities.parent_id` (Sub-Aktivität) **oder** `task_sub_tasks` (`…060958`, halb-deprecated, noch 13× referenziert) | `phase_id`, `task_id→phase_activities`, `description`, `duration` | **Arbeitsschritt** |

**Befund:** A bildet Yamas Dreiteilung ab — **`task_phases`=Phase, `phase_activities`=Aufgabe, deren `parent_id`-Kinder (bzw. `task_sub_tasks`)=Arbeitsschritt**. Definiert **je Produkt**, nicht global. **Wichtig:** `task_phases` hat seit **2026-06-05** `lead_stage_id`/`lead_sub_stage_id` → die Phasen sind **bereits an das Lead-Phasen-System (die 6 Phasen) anschließbar**. Zwei „Arbeitsschritt"-Repräsentationen nebeneinander (`parent_id` vs. `task_sub_tasks`) = kleine interne Doppelung.

---

## System B — `product_formulas` / `lead_product_checklist_values` (die FELDER je Produkt)

**Zweck (aus `produkt-einstellungen-befund.md`):** Formular-Builder je Produkt (`product_formulas.fields` JSON, Menü „Checklisten-Formulare"), je Gewerk ausgefüllt in `lead_product_checklist_values` (mit Snapshot+Version).

**NEUER Befund (der Kern dieser Aufgabe): B ist von A vollständig ENTKOPPELT.**
- `product_formulas` und `lead_product_checklist_values` haben **keine** Spalte `phase_id`/`activity_id`/`task_phase_id` (geprüft: 0 Treffer).
- `task_phases`/`phase_activities` haben **keinen** `product_formula_id`/`checklist`-Bezug (0 Treffer).
- Beide hängen nur gemeinsam am **Produkt** (`article_groups`) bzw. am **Gewerk** (`lead_product_lists`) — aber **nicht aneinander**.

→ **Felder gehören NICHT zu einem Arbeitsschritt.** „Welche Felder erfasse ich?" (B) und „Welche Schritte hat der Ablauf?" (A) sind heute **zwei unverbundene Welten**, die sich nur über Produkt+Gewerk „treffen". Das ist eine **Lücke**, keine Überlappung.

---

## System C — Kanban / Planner (die AUSFÜHRUNG — dreifach)

**Zweck:** aus dem Template (A) werden *konkrete, verfolgbare* Aufgaben je Gewerk/Vorgang — Terminierung, Status, Zuständige. Problem: das passiert in **drei parallelen Systemen**, die alle A konsumieren:

| Instanz-System | Tabelle(n) | Bezug zu A | Scope | Alter |
|---|---|---|---|---|
| **Kunden-Phasenliste** | `customer_phase_lists` (`2025_04_01`) | `activities_id→phase_activities`, `phase_id` | je Kunde/Objekt/Gewerk (`customer_id`,`alternative_id`,`product_id`); `work_progress`,`done`,`responsible_person`,`jump_steps` | älter |
| **Kanban-Lead-Aufgaben** | `kanban_lead_tasks` (`2026_06_05`) | **`task_phase_id` + `phase_activity_id`** | je Gewerk (`lead_product_list_id`,`customer_id`); `status`,`estimated_minutes`,`planned_start/end_at` | neu |
| **Planner** | `planner_plans` → `planner_items` (`2026_01`) | `planner_items.source_type='phase_activity'` (+ `appointment`/`personal_task`/`ticket_task`/`manual`), `source_id` | je Plan (`planner_plans.stage` = **montage\|inbetriebnahme**), Plan hängt an `customer_id`+`project_id→lead_product_lists` | neu |

**Befunde:**
- **Alle drei instanziieren dieselben `phase_activities` (A)** — customer_phase_lists per `activities_id`, kanban per `phase_activity_id`, planner per `source_type='phase_activity'`. → **dreifache „Wahrheit"** über den Zustand einer Aufgabe je Gewerk.
- **Planner ist am breitesten:** `planner_items` bündelt nicht nur Phasen-Aktivitäten, sondern auch Termine, persönliche Aufgaben und Tickets in EINE Ausführungsliste (`source_type`). Planner hat eine **eigene Stage-Achse** (`montage|inbetriebnahme`) — das ist **NICHT** die 6-Phasen-Achse, sondern eine Ausführungs-/Einsatz-Phase.
- **Nuriva (Mobile):** greift über `/api/planner/*` (`PlannerEmployeeApiController::myWork`) auf **Planner-Items** zu — die App liest also **C/Planner**, nicht Kanban und nicht customer_phase_lists (siehe `nuriva-sync-anbindung-befund.md`). *(nur benannt)*

---

## Das Verhältnis A ↔ B ↔ C (der Kern)

```
                 B  product_formulas / lead_product_checklist_values
                    (FELDER je Produkt)  ──✗── keine Verbindung ──✗──┐
                                                                     │  (Lücke: Felder
                                                                     │   hängen nicht an Schritten)
   A  TEMPLATE je Produkt (article_groups)                          │
   phase_sections ─▶ task_phases(Phase, lead_stage_id) ─▶ phase_activities(Aufgabe ─▶ parent_id=Arbeitsschritt)
        │                    ▲                                   ▲   ▲   ▲
        │  (Vorlage)         │ (an 6 Lead-Phasen angebunden)     │   │   │   konsumieren dieselbe Vorlage
        ▼                    │                                   │   │   │
   C  AUSFÜHRUNG (je Gewerk/Vorgang) — DREI parallele Instanzen: │   │   │
      ├─ customer_phase_lists   (activities_id) ─────────────────┘   │   │
      ├─ kanban_lead_tasks      (phase_activity_id + task_phase_id) ──┘   │
      └─ planner_plans→planner_items (source_type='phase_activity') ──────┘
             └ eigene Stage-Achse: montage | inbetriebnahme     └▶ Nuriva liest hier
```

**Getrennt / überlappend / hierarchisch — konkret:**
- **A → C ist hierarchisch** (Vorlage → Instanz), aber die Instanz-Ebene ist **verdreifacht** → Überlappung/Doppelung.
- **B steht getrennt daneben** (kein Bezug zu A oder C) — semantisch *sollte* es zu A/C gehören („Felder eines Schritts"), tut es aber nicht.

**DOPPELUNGEN („mehrere Wahrheiten"):**
1. **Aufgaben-Instanz je Gewerk: 3×** (customer_phase_lists · kanban_lead_tasks · planner_items) — dieselbe `phase_activity` wird in drei Tabellen zu „Status/Fortschritt" gemacht. Keine Single Source of Truth.
2. **Zwei Stage-Achsen:** `task_phases.lead_stage_id` (die 6 Lead-Phasen) vs. `planner_plans.stage` (montage/inbetriebnahme, Ausführung) — unterschiedliche Bedeutung, verwechselbar.
3. **Zwei Arbeitsschritt-Repräsentationen:** `phase_activities.parent_id` vs. `task_sub_tasks` (halb-deprecated).

**SAUBERE Grenzen (jeweils eigener Zweck):**
- **A = Vorlage** („welcher Ablauf gilt für dieses Produkt") — klarer, eigener Zweck.
- **B = Datenfelder** („welche Infos erfasse ich für dieses Produkt") — klarer, eigener Zweck, aber **ohne Anschluss** an A.
- **C/Planner = Einsatz-/Montageplanung** (Termine, Zuständige, Ausführungs-Status) — hätte einen klaren eigenen Zweck, WENN es nicht mit customer_phase_lists + kanban um dieselbe Aufgaben-Instanz konkurrierte.

---

## Fazit für Yamas Prinzip „Phase → Aufgabe → Arbeitsschritt"

**Wo lebt es heute am ehesten? → In System A.**
- `task_phases` = **Phase**, `phase_activities` = **Aufgabe**, `phase_activities.parent_id`/`task_sub_tasks` = **Arbeitsschritt**. Je Produkt definiert.
- A ist **bereits an die 6 verbindlichen Phasen anschlussfähig** (`lead_stage_id`/`lead_sub_stage_id`) — d. h. das Fundament, um „Phase" mit Weiche 1 zu verbinden, **existiert schon**.

**Ist die naheliegende Soll-Architektur „A=Struktur, B=Felder je Schritt, C=Ausführung" heute erfüllt?** — **Teilweise:**
- ✅ A trägt die Struktur und ist stage-fähig.
- ❌ B liefert die Felder, ist aber **nicht an A gekoppelt** (Felder hängen am Produkt, nicht am Schritt).
- ⚠️ C ist die Ausführung, aber **dreifach** — es gibt keine eine Ausführungs-Wahrheit.

**Empfohlene Zielrichtung (zur Entscheidung, NICHT umgesetzt):** A als *einzige* Struktur-Vorlage festschreiben (mit `lead_stage_id` an die 6 Phasen gebunden); B optional per neuer Verknüpfung an `phase_activities` hängen (Felder je Schritt); **eines** der drei C-Systeme zur Ausführungs-Wahrheit erklären, die anderen ablösen/migrieren.

---

## Zu entscheiden (Geschäftsregel-/Design-Fragen für Yama — NICHT geraten)

1. **Ausführungs-Wahrheit:** Welches System ist künftig *die* Instanz der Aufgaben je Gewerk — **`planner_items`** (breit, Mobile-angebunden), **`kanban_lead_tasks`** (Board, neu) oder **`customer_phase_lists`** (älter)? Die anderen zwei werden dann abgelöst/gespiegelt. *(Kern-Weiche — dasselbe Muster wie Status-Weiche 1.)*
2. **Felder ↔ Schritte:** Sollen Qualifizierungs-Felder (B) an einen konkreten **Arbeitsschritt/eine Aufgabe** (A) gekoppelt werden (dann neue Verknüpfung `product_formulas → phase_activities`), oder bleiben sie **produkt-global**?
3. **Zwei Stage-Achsen:** Wie verhalten sich die **6 Lead-Phasen** (`task_phases.lead_stage_id`) und die **Planner-Stages** (`montage|inbetriebnahme`) zueinander? Ist „Montage" eine der 6 Phasen (dann eine Achse) oder eine Ausführungs-Unterteilung *innerhalb* der Auftrags-/Montage-Phase (dann zwei Ebenen)?
4. **Arbeitsschritt-Ebene:** `phase_activities.parent_id` **oder** `task_sub_tasks` als kanonische Arbeitsschritt-Ebene? (Eine wählen, die andere aufräumen.)
5. **Prozess-Variante:** Ist `phase_sections`/`service` (z. B. `complete` vs. Wartung) eine Ebene **über** der Phase (Ablauf-Variante je Produkt) — soll das im Modell so bleiben?

---

*Reine Analyse — nichts geändert. Belege: Migrationen `phase_sections`/`task_phases`/`phase_activities`/`task_sub_tasks` (2023_08_31_060955–060958), `add_lead_stage_fields_to_task_phases` (2026_06_05), `product_formulas` (2025_06_02), `lead_product_checklist_values` (2025_06_03), `customer_phase_lists` (2025_04_01), `kanban_lead_tasks` (2026_06_05), `planner_plans`/`planner_items` (2026_01_21), `lead_product_lists` (2024_07_19); Controller `Phase/TaskPhaseController` (index:28), `Phase/PhaseActivitiesController`, `Phase/LeadTaskPhaseManagementController`, `Planner/PlannerEmployeeApiController` (myWork); `source_type`-Nutzung in `Planner/*`. Entkopplung B↔A per Negativ-Grep (0 Treffer) belegt. Querverweis: `produkt-einstellungen-befund.md`, `glossar.md`, `crm-inventur-06-projekt-aufgaben-assets.md`, `nuriva-sync-anbindung-befund.md`, `architektur-entscheidungen.md` (Weiche 1).*
