# Produkt-Einstellungen / je-Produkt-Qualifizierung — Befund

> **Reine Analyse (nur Lesen), kein Code geändert.** Frage: „Wir können für alle Produkte in der Einstellung etwas hinterlegen." — Wo ist dieser Bereich und was wird je Produkt/Gewerk hinterlegt? Besonders: existiert schon das Konzept **je Produkt/Gewerk eigene Qualifizierungs-Felder**?
>
> **Kurzantwort (Punkt 5): JA — es existiert, und zwar als echtes, gebautes System**, nicht bloß Stammdaten+Zuordnungen. Es gibt einen **Formular-Builder je Produkt** (`product_formulas`, Menü „Checklisten-Formulare"), dessen Ausgabe **je Gewerk ausgefüllt und versioniert** gespeichert wird (`lead_product_checklist_values`). Dazu eine per-Produkt-**Prozessstruktur** (Phase→Aufgabe/Arbeitsschritt: `task_phases`/`phase_activities`) und eine per-Produkt-**Default-Zuständigkeit** (`product_positions`). Verbindliche Begriffe: Gewerk=`lead_product_lists`, Produkt-Bezug über `product_id → article_groups` (siehe `glossar.md`).

---

## 1. WO ist der Einstellungs-/Konfigurationsbereich? (Wege für den Nutzer)

Verteilt über **zwei Sidebar-Blöcke** (`resources/views/admin/layouts/sidebar.blade.php`):

| Menüpunkt | Route | Sidebar-Block | Controller |
|---|---|---|---|
| **„Checklisten-Formulare"** | `product.formula.index` (`web.php:2875`) | Artikel (`sidebar:1166`) | `Product/ProductFormulaController` |
| **„Anfragevorschläge"** | `product.position.view` | Artikel (`sidebar:1172`) | `ProductPositionController` (Tab. `product_positions`) |
| **„Arbeitsschritte"** | `task_phase.index` | **Konfiguration** (`sidebar:1310`) | Phasen/Aktivitäten (`task_phases`/`phase_activities`) |
| **„Projekt-Struktur"** | `stages.index` | **Konfiguration** (`sidebar:1311`) | Stages |

**Formular-Builder-Routen** (`routes/web.php:2875–2887`): `index` · `create/{id}` · `store` · `edit` · `show/{product_id}` · `update` · `editFormula` · `updateFormula` · `save` · `test/{id}` + `test-submit` · **`checklist/{product_id}`** (`loadChecklist`). Views: `resources/views/admin/formula/{create,edit,list,test}.blade.php`. → Der Nutzer wählt ein Produkt (Artikel-Gruppe) und baut/bearbeitet dessen Formular; „test" zeigt die Vorschau.

---

## 2. WAS wird je Produkt hinterlegt? (a / b / c getrennt)

### (a) STAMMDATEN — der reine Katalog *(Zone 01)*
`article_groups` (`2023_06_22_085600`): nur `article_group` (Name), `initial`, `min_value`, `max_value`, `image`. **Keine** Zuständigkeits-/Feld-Definition hier. Detail-Preise/Lieferanten im Produktkatalog (`products`) — siehe `crm-inventur-01-artikel.md`.

### (b) ZUORDNUNGEN — die „automatische Ableitung" *(existiert)*
**Default je Produkt: `product_positions`** (`2024_07_14_012639`, Menü „Anfragevorschläge"):
`stage` · `article_group_id`→`article_groups` · `service_id`→`phase_sections` · `department_id`→`departments` · **`position_ids` (JSON)**.
→ Regel „**Produkt (+ Service + Stage) ⇒ zuständige Abteilung + Positionen/Rollen**". Das ist die von Yama beschriebene Ableitung — als Vorschlag/Default.

**Konkrete Instanz je Gewerk: `lead_product_lists`** (`2024_07_19_144003`):
`product_id`→`article_groups` · `service_id`→`phase_sections` · `department_id` · `employee_id` *(Innendienst)* · `field_employee` *(Außendienst)* · `teams` (JSON) · `service`(default `complete`) · `interest` · `realization_time` · `stage`/`stage_history`.
→ Beim Anlegen eines Gewerks werden Abteilung/Innen-/Außendienst/Team gesetzt (Maske im Kundenprofil); der Produkt-Default aus `product_positions` liefert die Vorschlagswerte.

### (c) FELD-/FORMULAR-DEFINITION je Produkt — **der interessante Fall: JA, vorhanden**
**Definition: `product_formulas`** (`2025_06_02_083017`) — der **Formular-Builder je Produkt**:
`product_id`→`article_groups` · `section_name` · **`fields` (JSON) — „Full JSON from builder"** · `status`(Un/Published) · **`version`** · `created_by`/`edited_by`/`deleted_by` · SoftDeletes.
→ Pro Produkt beliebig viele benannte **Sektionen** mit einer frei gebauten **Feldliste**. Builder-Feldtypen (aus `admin/formula/create.blade.php`): **text, textarea, select (mit Optionen), checkbox, file** + **`required`**-Flag (17×). Mehrere Sektionen/Formulare je Produkt möglich, versioniert.

**Ausgefüllte Instanz je Gewerk: `lead_product_checklist_values`** (`2025_06_03_205826`):
`lead_product_list_id`→`lead_product_lists` · `product_formula_id` · `customer_id`→`new_leads` · `alternative_id`→`lead_alternative_adds` · `product_id`→`article_groups` · `section_name` · **`filled_values` (JSON)** · **`formula_snapshot` (JSON — Feldstruktur wie beim Ausfüllen)** · **`formula_version`**.
→ Wird ein Gewerk bearbeitet, lädt `loadChecklist($product_id)` genau die Formulare dieses Produkts; die Eingaben landen als `filled_values` **plus eingefrorener Snapshot** der Feldstruktur (`LeadProductChecklistValueController` `firstOrCreate`:46 / `updateOrCreate`:129). Damit bleiben alte Ausfüllungen stabil, auch wenn das Formular später geändert wird.

**Zusätzlich Prozessstruktur je Produkt (Phase→Aufgabe→Arbeitsschritt):**
- `task_phases` (`2023_08_31_060956`): `product_id`→`article_groups`, `section_id`→`phase_sections`, `phase_name`, `stage`, `order`. *(Menü „Arbeitsschritte")*
- `phase_activities` (`2023_08_31_060957`): `phase_id`→`task_phases`, `product_id`→`article_groups`, `title`, `duration`, `description`, `photo`, `priority`, `percent`, `sort_order`, `parent_id` (Sub-Schritte).
- `phase_sections` = die Prozess-Varianten (z. B. `complete`, Wartung), referenziert als `service_id`.
→ Pro Produkt ist also auch der **Ablauf** (welche Phasen, welche Arbeitsschritte, Dauer, Foto-Pflicht via Aktivität) hinterlegbar.

---

## 3. Tabellen, die die Einstellungen speichern (Übersicht)

| Ebene | Tabelle | Rolle |
|---|---|---|
| **Feld-/Formular-DEFINITION je Produkt** | `product_formulas` | Builder-Ausgabe (`fields` JSON), versioniert |
| ausgefüllt je Gewerk | `lead_product_checklist_values` | `filled_values` + `formula_snapshot` + `formula_version` |
| **Prozess je Produkt** | `task_phases` → `phase_activities` (+ `phase_sections`) | Phasen/Aufgaben/Arbeitsschritte |
| **Default-Zuständigkeit je Produkt** | `product_positions` | Produkt+Service+Stage ⇒ Abteilung + Positionen |
| Zuordnung je Gewerk (Instanz) | `lead_product_lists` | department/employee/field_employee/teams/service |
| Stammdaten | `article_groups` / `products` | Katalog (keine Zuständigkeit/Felder) |

---

## 4. Unterschiedliche Formulare je Produkt? (Smart Routing / bedingte Felder)

- **Je Produkt UNTERSCHIEDLICHE Formulare: JA.** `product_formulas` ist per `product_id` getrennt; `loadChecklist($product_id)` (`ProductFormulaController:245`) liefert **nur** die Formulare des jeweiligen Produkts. Zwei verschiedene Gewerke zeigen also verschiedene Felder. Das ist „Smart Routing auf Produkt-Ebene".
- **Bedingte Felder INNERHALB eines Formulars** (show-if/depends/„Feld X nur wenn Y"): **kein Beleg gefunden.** Der Builder kennt Feldtyp + `required` + Optionen, aber keine sichtbare Bedingungs-/Abhängigkeitslogik. → Formulare unterscheiden sich **zwischen** Produkten, nicht dynamisch **innerhalb** eines Formulars.

---

## 5. FAZIT — existiert „je Produkt/Gewerk eigene Qualifizierungs-Felder"?

**JA — das Konzept ist vorhanden, gebaut und im Kern nutzbar**, nicht nur Stammdaten+Zuordnungen:

1. **Definition:** Formular-Builder je Produkt → `product_formulas` (`fields` JSON, versioniert), Menü **„Checklisten-Formulare"**. Mehrere Sektionen/Felder je Produkt, Feldtypen + Pflichtfelder.
2. **Instanz:** je Gewerk ausgefüllt → `lead_product_checklist_values` mit **Snapshot + Version** (robust gegen spätere Formularänderungen).
3. **Prozess:** je Produkt Phasen/Arbeitsschritte → `task_phases`/`phase_activities`, Menü **„Arbeitsschritte"**.
4. **Zuständigkeit:** je Produkt Default-Ableitung → `product_positions` (**„Anfragevorschläge"**), konkret je Gewerk in `lead_product_lists`.

**Grenzen / offene Punkte:**
- **Keine bedingten Felder innerhalb eines Formulars** (nur unterschiedliche Formulare *zwischen* Produkten).
- **Nutzungsgrad** (wie viele Produkte real Formulare/Phasen hinterlegt haben) ist hier **nicht** geprüft — lokale Dev-DB oft dünn; das gehört in eine Detail-Inventur.
- Drei nebeneinander liegende „Struktur"-Systeme (`product_formulas` ↔ `task_phases/phase_activities` ↔ Kanban/Planner) — die Abgrenzung „Formular-Qualifizierung vs. Prozess-Arbeitsschritte vs. Aufgaben" ist im Detail zu klären (verweist auf die Planner-/Phasen-Detail-Inventur, `crm-inventur-06`).

**Antwort auf Yamas Aussage:** Ja — „für alle Produkte in der Einstellung etwas hinterlegen" ist real: unter **Artikel → „Checklisten-Formulare"** definiert man je Produkt die zu erfassenden Felder (Qualifizierung), unter **Konfiguration → „Arbeitsschritte"** den Ablauf, und über **„Anfragevorschläge"** die zuständige Abteilung/Position. Der Baustein für die gewerkespezifische Qualifizierung ist damit **vorhanden** — auszubauen wäre v. a. bedingte Feldlogik und die verbindliche Nutzung.

---

*Reine Analyse — nichts geändert. Belege: `routes/web.php:2875–2887`; `sidebar.blade.php:1166/1172/1310/1311`; `ProductFormulaController` (store:55, save:127, loadChecklist:245); `LeadProductChecklistValueController` (firstOrCreate:46, updateOrCreate:129); Migrationen `product_formulas`(2025_06_02), `lead_product_checklist_values`(2025_06_03), `product_positions`(2024_07_14), `task_phases`/`phase_activities`(2023_08_31), `lead_product_lists`(2024_07_19), `article_groups`(2023_06_22); Builder-View `admin/formula/create.blade.php`. Querverweis: `glossar.md`, `crm-inventur-01-artikel.md`, `crm-inventur-06-projekt-aufgaben-assets.md`.*
