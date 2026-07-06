# Formular-Synthese ticket ⊕ playground — Arbeitspaket (Befund, read-only, 2026-07-06)

> Strang `formulare` (Betriebsordnung). **Reiner Befund — kein Code, keine Migration, keine UI, keine Datenmigration.** Ziel: eine andere Instanz kann danach bauen (nach Yamas Entscheidungen in §13). Quellen: `/Users/yamanuri/Documents/ticket` (führend) + `/Users/yamanuri/Documents/Playground/backend-laravel` (nur gelesen, Bauteile-Lager). Belege aus zwei read-only-Lesern + DB-Abfragen.

---

## 1. Kurzurteil
- **Was ticket schon hat:** ein **generisches Vorlagen-System** `ProductFormula` (JSON-`fields` je Artikelgruppe) mit **Versionierung** (`version`), **Antwort-Snapshot** (`formula_snapshot`+`formula_version`), Feldtypen, Pflichtfeldern, Test/Preview, Formeln und bedingter Sichtbarkeit (`advancedCondition`). Der **Builder ist live**; die **Ausfüll-Seite ist gebaut, aber nicht ins Lead-/Objekt-UI verdrahtet**.
- **Was playground besser hat:** einen **eval-freien, view-agnostischen PHP-Engine-Kern** — `FormulaEvaluationService` (Shunting-Yard→RPN, kein `eval`, **Operanden-Gate** vollständig/unvollständig/ungeprüft, Wiederhol-Aggregation), `UnitConversionService` (11 Dimensionen), `PlausibilityService` (Warnungen), sauberes **Smartrouting** (`FormRoutingService`), **strukturierte Feldtypen** (`FormFieldType`, 40 Typen), Preis-Rechteschutz serverseitig, und **21 fachliche Formularvorlagen / 358 Felder** als Domänen-Konserve (Heizlast/Aufmaß/PV/WP).
- **Warum verschmelzen statt importieren:** ticket bleibt führend (eine Formularwelt, keine zweite). playgrounds React-freier PHP-Kern wird als **Fachlogik-Bauteil** in tickets Bestand gehoben; die **Alpine-Views + der Stored-Procedure-Seed werden NICHT übernommen**. Der stärkste Grund ist **Sicherheit**: tickets Formel-/Sichtbarkeitsauswertung läuft heute über **`new Function(gespeicherter String)`** (Client-Code-Ausführung, JS-Injection-Vektor) — playgrounds sichere Engine ersetzt das.
- **Führende Navi-Bezeichnung:** **„Checklisten-Formulare"** (Sidebar Z.514) bleibt. **Kein** neuer Bereich „Formulare".

---

## 2. Ist-Befund ticket

### `app/Models/ProductFormula.php` — Vorlage
- **Zweck:** Formular-/Checklisten-**Vorlage** je Artikelgruppe. **Datenmodell:** `product_id`(→`article_groups`), `section_name`, `fields`(JSON, cast array), `status`(default `Unpublished`), `version`(int, default 1), `created_by/edited_by/deleted_by`(→`employees`, **cascade**), SoftDeletes. `filledChecklists()`→`hasMany(LeadProductChecklistValue)`.
- **Stärken:** Audit, Versionierung, SoftDeletes, JSON-Flexibilität, W5-nah verankert (Artikelgruppe).
- **Schwächen:** `section_name` im Kopf, obwohl UI Multi-Section kann → **ein Row = eine Section** (Modellbruch). Trait `softDeletes` klein geschrieben (Stil).
- **Risiken / darf nicht kaputtgehen:** FK `product_id→article_groups`; die drei `employees`-FKs mit `cascade` (Employee-Löschung würde Vorlagen mitlöschen — Altlast, aber 0 Zeilen).

### `app/Models/LeadProductChecklistValue.php` — Antwort
- **Zweck:** ausgefüllte Checkliste je Lead+Produkt+Vorlage. **Datenmodell:** `lead_product_list_id`(→`lead_product_lists`), `product_formula_id`, `customer_id`(→`new_leads`), `alternative_id`(→`lead_alternative_adds`), `product_id`(→`article_groups`), `section_name`, `filled_values`(JSON-Map `{feld:wert}`), `formula_snapshot`(JSON), `formula_version`(int).
- **Stärken:** **Snapshot+Version = eingefrorene Antwort** (stabil trotz Vorlagenänderung) — guter Ansatz, den playground identisch fährt (`dynamic_form_version`).
- **Schwächen:** kein SoftDeletes/Audit; **keine Serverseitige Pflichtfeld-Validierung** beim Speichern.
- **Risiken:** 5 FKs cascade an die Lead-Welt.

### Controller
- **`ProductFormulaController`** (CRUD+Test): **Wildwuchs** — zwei Editoren (`edit()`→`create.blade`, `editFormula()`→`edit.blade`), zwei Save-Pfade (`save()` ohne / `updateFormula()` mit Versions-Increment), **Multi-Section-Persistenz-Bug** (`break` nach 1. Section), `getProduct()` referenziert undefiniertes `$formula`, **kaputte Route `product.formula.updates`→`@update` (Methode fehlt)**, viel `\Log::info`-Rauschen, `testSubmit` = No-Op.
- **`LeadProductChecklistValueController`**: `initChecklistRender` (firstOrCreate), `save`/`saveChecklist` (zwei Pfade: Snapshot vs. live `fields`). index/create/… = leere Stubs.

### Views `resources/views/admin/formula/`
- `list.blade.php` (Übersicht je Artikelgruppe; jQuery+Select2+SweetAlert2+Quill), `create.blade.php` (**Builder**, Vanilla-JS + **SortableJS-CDN**, Live-Preview, Formel via `new Function`), `edit.blade.php` (**Copy-Paste-Zwilling** des Builders), `test.blade.php` (Preview + Client-Validierung), **`index1.blade.php` = toter Prototyp** (Bootstrap-5-CDN, eigene section-wrapped JSON-Shape, von keiner Route referenziert), **`test.blade copy.php` = Leiche**.
- **Design:** Custom-CSS (`:root`, Primary `#93c21c`) + jQuery/Select2/SweetAlert/SortableJS — **nicht** durchgängig Vuexy.

### Besonders geklärt
- **`product_formulas.fields`-Aufbau (0 Zeilen live, aus Builder abgeleitet):** flaches JSON-**Array von Feld-Objekten**; Keys: `label, name, type, defaultValue, options, formula, subfields, min, max, pattern, advancedCondition, required`. Typen: `text, number, select, checkbox, formula, textarea, date, file, multi-group`. **Konsistenz:** Kern-Shape kohärent; **Wildwuchs liegt in den Schreibpfaden** (2 Save-Wege, tolerierte Doppel-Shapes in `edit.blade`, abweichende Shape im toten `index1`), nicht im Grund-JSON.
- **Antwortspeicherung:** `filled_values` JSON-Map + `formula_snapshot`/`formula_version` (s.o.).
- **Verankerung:** Vorlage an **Artikelgruppe**; Antwort an **Lead / Lead-Alternative / Lead-Produkt-Zeile / Artikelgruppe / Vorlage**. **Kein** direkter Gewerk- oder Kunden(firmen)-Bezug (nur über Lead).
- **Versionierung: JA** · **Pflichtfelder: JA** · **Test/Preview: JA** (Submit No-Op) · **Berechnungen: JA** (aber `new Function`) · **bedingte Sichtbarkeit: JA** (`advancedCondition`, aber `new Function`) · **Feldvalidierung: JA nur Client** (min/max/pattern/required; keine Serverprüfung).
- **⚠️ Sicherheits-Kernbefund:** Formeln UND `advancedCondition` werden client-seitig über **`new Function(ungefilterter gespeicherter String)`** ausgeführt → **Code-Ausführung/JS-Injection**, sobald Vorlagen-Autor ≠ Ausfüller. **Das ist der stärkste Grund für die playground-Engine.**
- **Mehrfachsysteme (E5-relevant):** parallele Checklisten-Systeme existieren — **Old** (`Old/PVChecklistController`, `WPChecklistController`, `PVLongChecklistController`; Routen **auskommentiert**), **Heatpump** (`heatpump_checklists`), **Maintenance-Familie** (`MaintenanceChecklistController`, aktiv, eigener Sidebar-Eintrag „Wartungs-Checklisten" Z.553), **Checklist-Set-Familie** (`checklists/checklist_sets/…`), **Master-Set-Kopplung** (`master_set_checklists`). ProductFormula ist das **jüngste/generischste**.

### E5 — Bestandsdaten der Alt-Checklisten (Zeilenzahlen, DB live)
| Tabelle | Zeilen | Jüngster | Lead/Deal dran? |
|---|---|---|---|
| `product_formulas` | **0** | – | – |
| `lead_product_checklist_values` | **0** | – | FK-Spalten da, leer |
| `heatpump_checklists` | **0** | – | `customer_id` da, leer |
| `p_v_checklists` | **0** | – | `customer_id`/`alternative_id` da, leer |
| `w_p_checklists` | **0** | – | leer |
| `p_v_long_checklists` | **0** | – | leer |
| `maintenance_checklists` | **0** | – | Template-Tabelle, leer |
**Konsequenz (bindend für §11/§12):** **alle sieben Tabellen 0 Zeilen** → jede Ablösung/Konsolidierung ist **reiner Code-Rückbau** (Muster `deal_invoices`), **kein** Daten-Migrations-/Tag-X-Thema. Es gibt keine unantastbaren Bestandsdaten. *(Bleibt es bis zum Bau bei 0 Zeilen, ist vor dem Rückbau erneut zu zählen — sonst kippt die Einordnung.)*

---

## 3. Ist-Befund playground

### Kern (view-agnostisches PHP — 1:1-Kandidaten)
- **Models (6):** `DynamicForm`(`dynamic_forms`: name/slug/version/form_type/status, `UNIQUE(slug,version)`) → `FormSection`(`is_required`, `is_repeatable`) → `FormField`(`field_type`, `options_json`, `validation_rules_json` + **9 Baukasten-Spalten**: `calculation_json, visible_if_json, depends_on_field_key, min_value, max_value, decimals, internal_only, external_visible, recognition_json`) → `FormFieldOption`(`anzeigename`/`technischer_wert`-Split, `folgeaktion_json`) · `FormAnswer`(**5 Wertspalten** value_text/number/date/boolean/json, `is_assumption/assumption_reason/risk_note`, `is_calculated/calculation_meta_json`, `dynamic_form_version` eingefroren, `UNIQUE(project_id, form_field_id, repeat_group_key)`) · `FormRoutingRule`(Anker-Spalten).
- **Services (4):** `FormRoutingService`(Smartrouting) · **`FormulaEvaluationService` (903 Z., Herzstück)** · `PlausibilityService`(Warnungen) · `UnitConversionService`(11 Dim./~40 Einheiten). **Stärken:** sicher, isoliert, framework-neutral. **Abhängigkeiten:** nur `UnitConversionService` + Models (`EntityHistoryService`/`Project` nur in Controllern). **Warum nicht 1:1:** Kopplung an `Project`/`Interest`/`Object` + RBAC → an ticket-Domäne anzupassen.
- **`Support/Form/FormFieldType`:** 40 gültige Typen, gruppiert nach Speicherspalte (TEXT 12 / NUMBER 12 / DATE 5 / BOOLEAN 4 / JSON 7). Validierung `Rule::in(FormFieldType::ALL)`.

### Controller / Views / Seeder
- **Controller:** `Api/DynamicFormController`(read-only, Filter/Sort-Whitelist/Pagination), `Api/FormAnswerController`(Schreibpfad: Version einfrieren, Serverseitige Validierung `rulesForField`, typabhängige Spalte, idempotentes `updateOrCreate`), `Api/FormCalculationController`(preview/recalculate/override; **Preis-Rechteschutz: Felder ohne `calc.*.view` werden WEGGELASSEN**), `Api/FormBuilderController`(816 Z., CRUD, **kein Hard-Delete**, tiefe Versions-Klonung bei Änderung freigegebener Vorlagen, `pruefeVorlage()`-Freigabe). **Abhängigkeit:** `EntityHistoryService` (ticket-spezifisch zu ersetzen).
- **Views** (`modules/formulare`, `modules/formularbaukasten`): echte Blade, aber **Alpine.js-CDN + Tailwind + `fetch`**, nur **Listen-/CRUD-Kopf** (kein Renderer/Feld-Editor). **Warum nicht 1:1:** Alpine nur in `heizkoerper.*` erlaubt (CLAUDE.md), Tailwind fremd → **im ticket-Design neu bauen**; nur AJAX-Vertragsform + Slugify übernehmbar.
- **Seeder:** **Basistabellen = Raw-DDL** (`crm_erp_mysql_schema.sql`), Zusatzspalten migrationsbasiert. **17 Kern-Formulare = Stored-Procedure-SQL** (`crm_erp_form_seed.sql`, MySQL-spezifisch → übersetzen). **Heizlast/Aufmaß/WP-Konfigurator + Permission-Seeder = echtes idempotentes Laravel** → direkt übersetzbar.

### Besonders geklärt
- **`FormRoutingService`:** je Projekt-Interest Query auf `form_routing_rules->active()`, **NULL=Wildcard** für `product/service/interest_type/group/object_type` (5 Anker gefiltert). `phase_id/progress_id/requires_installation` fließen NUR in den **Spezifitäts-Score** (= Anzahl nicht-NULL-Anker), `condition_json` wird **nie** ausgewertet. Sortierung `priority*100 + specificity`. **Kein Treffer:** immer 3 **hart verdrahtete Basisformulare** (`grundformular/energieverbrauchsdaten/zukunftswuensche`).
- **`FormulaEvaluationService`:** **kein eval** — Tokenizer(Whitelist)→Shunting-Yard→RPN→Stack. Operatoren `+-*/`; Funktionen **`SUM`(Aggregat über Wiederholzeilen)/`MENGE`/`FLAECHE(l,b)`/`VOLUMEN(l,b,h)`**. Formelquelle `calculation_json.{formel|formula|expression|ausdruck}`; Operanden = Feld-Slugs + Literale. **Operanden-Gate:** `vollstaendig`(verbindlich) / `enthaelt-ungepruefte-werte`(`is_assumption` ODER ungeprüftes `recognition_json`-Feld ODER geerbt aus Kette → berechnet, **nicht verbindlich**) / `unvollstaendig`(fehlender Pflicht-Operand → **keine Zahl**). Pro Operand **UnitConversion** in die Zieleinheit; **Rundung** (`decimals`, Modus); **min/max nur Warnung**. DoS-Schutz (Formel≤2000, Klammertiefe≤32). Persistenz nur im Controller (`is_calculated=1`).
- **Feldtypen:** 40 definiert, **20 real belegt**. Verteilung (358): `select 150, number 49, text 30, textarea 27, length 21, multiselect 19, calculation 14, boolean 10, image 9, integer 6, area 5, file 4, power 3, decimal 3, checkbox_group 2, consent 2, email/plz/date/checkbox je 1`.
- **`visible_if_json`: NUR gespeichert/validiert, NIRGENDS ausgewertet** — es gibt **keine** bedingte-Sichtbarkeits-Engine (weder Server noch View). Schema steht, **Logik fehlt (in BEIDEN Systemen neu zu bauen).** `calculation_json`: **wird ausgewertet**. `recognition_json`: deklarativ, aber **wirksames Gate-Signal** (Existenz ⇒ Operand „ungeprüft" bis Freigabe).
- **21 Formulare / 358 Felder als Fachvorlage: JA.** Slugs: `grundformular`(30), `energieverbrauchsdaten`(12), `zukunftswuensche`(13), 14×`produkt_*` (PV 45, WP 18, Wallbox 6, Fenster 8, …), `fachwerkzeug_heizlast`(58), `fachwerkzeug_waermepumpe_konfigurator`(58), `aufmass_waermepumpe`(28), `aufmass_photovoltaik`(29). **Fachgrenze:** Heizlast-Seeder deklariert seine Formeln als **vorläufig, KEINE Norm-Heizlast DIN EN 12831** (Pflicht-Consent).
- **API/React-lastig / nicht portierbar:** **nichts React** im Backend; nur die **2 Alpine-Views** + der **Stored-Procedure-Seed**. Der Engine-Kern ist PHP-portierbar. **Ungebaut (Schema da, Logik fehlt):** `visible_if`-Auswertung, sektions-Routing + Pflichtdokumente/-aufgaben (`form_routing_rule_sections/…`), `condition_json/phase_id/progress_id`-Filter, `recognition_json`-Inhaltsverarbeitung.

---

## 4. Entscheidung: Zielarchitektur in ticket
> Defaults sind **widerlegbar** (E3): ticket ist nicht automatisch richtig. Der Befund stützt die Defaults, benennt aber die Gegenoption.

- **`ProductFormula` bleibt führendes Modell — JA.** Begründung: W5-nah verankert (Artikelgruppe), Versionierung + Antwort-Snapshot vorhanden, 0 Fremddaten-Risiko. playground wird **eingepflanzt**, nicht danebengestellt.
- **`fields` bleibt JSON — JA für Phase 1 (Default), MIT Disziplinierung.** Der JSON-**Shape ist tragfähig**; die Mängel liegen in Schreibpfaden/Views. → **`schema_version` einführen, EIN Schreibpfad, Server-Validierung gegen das Schema.** **Gegenoption (E3, gleichwertig vorgelegt):** Normalisierung auf playground-artige Tabellen (`form_sections`/`form_fields`/`form_field_options`) — **Vorteil** abfragbar/pro-Feld/Options-Integrität; **Nachteil** hoher Migrations-Aufwand + großer Blast-Radius (Builder-UI + Antwortpfad umbauen). **Empfehlung:** JSON Phase 1, Normalisierung als **optionale Phase 3**, wenn Feld-Abfragen nötig werden. → **Yama-Frage §13-3/§13-7.**
- **playground-Felder ins JSON-Schema:** `key(=slug), type(FormFieldType), unit, min, max, decimals, options, required, help_text, visible_if, calculation, validation, default, source, imported_from, risk_level, assumption_allowed` (s. §5).
- **Neue Spalten an `product_formulas`:** **minimal** — `schema_version`(int, Default 1) + `imported_from`(nullable). Routing als **eigene kleine Tabelle** (s. §6/§8), nicht ins `fields`-JSON.
- **Neue Services (ticket):** `FormulaEvaluationService` (Port), `UnitConversionService` (Port), `PlausibilityService` (Port), `VisibleIfService` (NEU, in beiden Systemen fehlend), `FormRoutingService` (Port, re-anchored).
- **Smartrouting-Abbildung:** playground-Regelwerk auf ticket-Anker (s. §6).
- **Sichere Formel-Auswertung:** playground-Engine **ersetzt `new Function`** (Server als Wahrheit; Client nur Vorschau).
- **`visible_if`:** neue kleine Engine (Bedingungs-Syntax `Feld op Wert`), s. §5/§7.
- **Pflichtfelder/Gates:** Server-Validierung im Antwortpfad (heute Client-only).
- **Alte hardcodierte Checklisten:** da **0 Zeilen** → späterer **Code-Rückbau** (Muster `deal_invoices`), eigener Strang/Posten nach Yama (Frage §13-5). **Nicht Teil des ersten Baus.**

---

## 5. Ziel-JSON-Schema `product_formulas.fields` (v2, versioniert)
Kopf: `product_formulas.schema_version = 2`. `fields` = Array von Feld-Objekten:
```
{
  "key":            "raumhoehe",              // stabiler Slug (ersetzt name), eindeutig je Formular
  "label":          "Raumhöhe",
  "type":           "length",                 // aus FormFieldType-Whitelist (ticket-Portierung)
  "required":       true,
  "options":        [ {"value":"kfw","label":"KfW"}, … ],  // nur Auswahltypen
  "unit":           "m",
  "min":            1.5,
  "max":            4.0,
  "decimals":       2,
  "default":        null,
  "help_text":      "lichte Höhe",
  "visible_if":     {"field":"bauart","op":"=","value":"neubau"},   // NEU-Engine; null = immer sichtbar
  "calculation":    null,                      // nur type=calculation: {"formel":"FLAECHE(laenge,breite)","rundung":"kaufmaennisch"}
  "validation":     {"pattern":null},          // Serverregeln zusätzlich zu required/min/max
  "source":         "manuell",                 // manuell | recognition | import
  "imported_from":  "playground:fachwerkzeug_heizlast",  // Herkunft der Vorlage/Feldes; null = nativ
  "risk_level":     "normal",                  // normal | fachlich-vorlaeufig (z.B. Heizlast ≠ DIN EN 12831)
  "assumption_allowed": true                   // darf als is_assumption befüllt werden (Operanden-Gate)
}
```
**Beispiele:**
- **Normales Eingabefeld:** `{"key":"personen","label":"Personen im Haushalt","type":"integer","required":true,"min":1,"max":20,"unit":null,"visible_if":null,"calculation":null}`
- **Select:** `{"key":"heizmedium","label":"Heizmedium","type":"select","required":true,"options":[{"value":"gas","label":"Gas"},{"value":"oel","label":"Öl"},{"value":"pellets","label":"Pellets"}]}`
- **Berechnet:** `{"key":"flaeche","label":"Fläche","type":"calculation","unit":"m2","decimals":2,"calculation":{"formel":"FLAECHE(laenge,breite)","rundung":"kaufmaennisch"},"assumption_allowed":false}`
- **Bedingt sichtbar:** `{"key":"sanierung_art","label":"Sanierungsmaßnahme","type":"select","options":[…],"visible_if":{"field":"geplante_sanierung","op":"=","value":"ja"}}`

---

## 6. Smartrouting-Ziel
playgrounds Idee = deklarative Regeln `(Anker*) → Formular`, NULL=Wildcard, `priority` + Spezifität. Abbildung auf ticket-Anker:
| ticket-Anker | Eignung | Phase |
|---|---|---|
| **`article_groups`** (= playground `product`) | **stark** (Vorlage hängt schon dran) | **1** |
| `lead_product_lists` (Gewerk/Produkt-Zeile am Lead) | stark (Ausfüll-Kontext) | **1** |
| `lead_alternative_adds.object_type` (Objekt-Typ) | mittel (playground `object_type`) | **1** |
| `products` (Einzelprodukt) | mittel | 2 |
| `deals` (Auftrag) | mittel | 2 |
| `lead_stages`/Phasen (playground `phase_id`) | mittel (nur Score, nie gefiltert bei pg) | 2 |
| `departments`/`branches` | schwach | später |
| `customer`/Objekt | schwach (nur über Lead) | später |
- **Phase 1:** Anker **Artikelgruppe + Lead-Produkt-Zeile + Objekt-Typ**. Regeln in **eigener Tabelle `product_formula_routing_rules`** (nicht ins JSON): `product_formula_id, article_group_id?, object_type?, service?, priority, is_active` (NULL=Wildcard).
- **Mehrdeutigkeit:** höchste `priority`, bei Gleichstand höchste Spezifität (Anzahl gesetzter Anker) — playground-Regel übernehmen.
- **Kein Formular gefunden:** definierte **Fallback-Liste** (analog playgrounds 3 Basisformularen, aber **konfigurierbar** statt hart verdrahtet) ODER leere Liste mit klarer UI-Meldung „keine Vorlage hinterlegt". **Yama-Default:** leere Liste + Hinweis (kein stiller Fallback).

---

## 7. Formel-Engine-Ziel
- **Übernehmen (Port):** kein `eval`; Whitelist-Parser (Shunting-Yard→RPN); Operatoren `+-*/`; Funktionen `SUM/MENGE/FLAECHE/VOLUMEN`; Status `vollstaendig/unvollstaendig/enthaelt-ungepruefte-werte`; **Operanden-Gate**; `UnitConversion`; Rundung; **min/max-Warnung** (kein Abbruch); DoS-Grenzen (2000/32).
- **Neuer Service ticket:** `App\Services\Formular\FormulaEvaluationService` (+ `UnitConversionService`, `PlausibilityService`, `VisibleIfService`). **Kein Alpine/JS-`eval` mehr** — Server ist Wahrheit, Client nur unverbindliche Vorschau.
- **Eingabe/Ausgabe-Vertrag:** `evaluate(formula: string, operands: array<slug,{value,unit,is_assumption,status}>, field:{unit,decimals,rundung}) : { status, value|null, verbindlich:bool, protokoll:[{regel,schwere,text}] }`. **Reine Funktion, keine Persistenz** — Speichern entscheidet Controller/Service, nie die Engine (wie playground).
- **Tests:** s. §10.

---

## 8. Migration-/Daten-Konzept (nur Konzept, keine Migration)
- **Reicht JSON-Erweiterung ohne Migration?** Für die Feld-Keys **ja** (JSON ist schemalos). **Aber** `schema_version` + `imported_from` als **Spalten** an `product_formulas` sind sinnvoll (Query/Marker) → **2 additive nullable Spalten** (Migration, Tag-X). Routing braucht **eine neue Tabelle** (`product_formula_routing_rules`).
- **`product_formulas.schema_version`:** JA (steuert Renderer/Validierung, erlaubt sanfte v1→v2-Migration).
- **`imported_from`:** JA (Herkunft playground-Vorlage, Rückbau-Beweis, Betriebsordnung 1.1-4).
- **`routing_rules` eigene Tabelle vs. JSON:** **eigene Tabelle** (abfragbar, mehrere Regeln je Vorlage, NULL-Wildcard). JSON würde Routing intransparent machen.
- **playground-Vorlagen als Seeder:** die **Laravel-Seeder** (Heizlast/Aufmaß/WP-Konfigurator) → direkt in ticket-Eloquent-Seeder übersetzen; der **Stored-Procedure-SQL** (17 Kern-Formulare) → in Insert-Arrays/Eloquent übersetzen (Daten behalten, Form ändern). Alle mit `imported_from='playground:<slug>'`.
- **Marker/Teardown:** je geseedete Vorlage `imported_from`-Marker → marker-basierter Teardown-Seeder (RELEASE-MANIFEST-Konvention).
- **Was NICHT migriert wird:** **keine** playground-**Antwortdaten** (`form_answers`), **keine** `form_answers`-Struktur, **keine** playground-`projects`-Kopplung; **nur Vorlagen/Feld-Definitionen** als Fachkonserve. Keine Live-Datenmigration.

---

## 9. UI/Navi-Regeln
- **Führende Bezeichnung: „Checklisten-Formulare"** (Sidebar Z.514) — bleibt. **Kein** neuer Bereich „Formulare", **keine** doppelten Navi-Begriffe.
- **Keine playground-Optik / keine Alpine-Views / kein React.** UI später im **ticket-Design** (Vuexy/Blade/jQuery), vorhandene Cards/Tabs/Modals/Badges/toastr/Select2 nutzen.
- Neue Funktionen (Renderer, `visible_if`, sichere Berechnung) nur **in vorhandener Struktur** sichtbar machen.
- **Aufräumen (später, eigener Schnitt):** die Wildwuchs-Leichen (`index1.blade.php`, `test.blade copy.php`), Doppel-Editor/Doppel-Save, kaputte Route `product.formula.updates`.
- **`sidebar.blade.php` + `routes/web.php` sind TABU** für diesen Strang (nur gelesen; sidebar = NAV-Strang; Routen erst mit FS-Tickets nach Freigabe).

---

## 10. Tests / Akzeptanzkriterien (definiert, nicht geschrieben)
- **JSON-Schema-Validierung:** ungültiges Feld-Objekt (fehlender `key`/`type` nicht in Whitelist) wird abgelehnt.
- **Pflichtfeld-Validierung (Server):** Antwort ohne Pflichtwert → Fehler (nicht nur Client).
- **`visible_if`-Auswertung:** Feld erscheint/verschwindet je Bedingung; unsichtbares Pflichtfeld blockt nicht.
- **Formel ohne `eval`:** nicht-whitelistetes Zeichen → `unvollstaendig`, **nie Ausführung**; `new Function` nirgends im Antwortpfad.
- **`unvollstaendig`** bei fehlendem Pflicht-Operanden (keine Zahl).
- **`enthaelt-ungepruefte-werte`** bei `is_assumption`/`recognition`-Operand (berechnet, nicht verbindlich).
- **Smartrouting** findet die richtige Vorlage je Anker; Mehrdeutigkeit → priority/Spezifität; kein Treffer → definierter Fallback.
- **Keine zweite Formularwelt:** genau ein aktives System (`ProductFormula`); grep auf reaktivierte Alt-Controller = leer.
- **Bestehende `ProductFormula`-Views brechen nicht** (Builder/list/edit weiter funktionsfähig).
- **Bestehende hardcodierte Checklisten brechen nicht** (bis zum bewussten Rückbau unangetastet).

---

## 11. Risiken
- **JSON-Wildwuchs:** ohne `schema_version` + Server-Validierung wächst `fields` unkontrolliert → **Mitigation:** v2-Schema + EIN Schreibpfad + Validierung.
- **Alte Checklisten bleiben parallel:** solange nicht zurückgebaut, zwei „Wahrheiten" im Code → **Mitigation:** Rückbau als eigener Posten; **da 0 Zeilen = reiner Code-Rückbau** (kein Datenrisiko).
- **Doppelter Navi-Begriff:** „Checklisten-Formulare" + „Wartungs-Checklisten" nebeneinander → **Mitigation:** Terminologie-Cleanup (§12 FS-10), Yama-Entscheid.
- **Formel-Engine erzeugt falsche Werte:** **Mitigation:** Operanden-Gate + Referenz-Tests + Fachgrenze (Heizlast ≠ DIN EN 12831, Pflicht-Consent übernehmen).
- **358-Felder-Migration erzeugt falsche Fachwahrheit:** playground-Formeln sind teils vorläufig → **Mitigation:** `risk_level='fachlich-vorlaeufig'`, keine produktive Nutzung ohne Fachfreigabe.
- **Performance:** JSON-Rendering großer Formulare (PV 45, Heizlast 58 Felder) → **Mitigation:** serverseitiges Rendern, Lazy-Sections.
- **Rechte/Auth:** playgrounds Preis-Rechteschutz (`calc.*.view` → Feld weglassen) an ticket-RBAC (`is_admin`/`user_rolls`) anpassen (nicht 1:1).
- **Alte Daten/Antworten:** **keine** (0 Zeilen) → kein Migrationsrisiko; vor Rückbau erneut zählen.
- **Parallele Agenten:** Strang `formulare` exklusiv `docs/formular-*`; `sidebar`/`routes` tabu; Migrationen-Timestamps über STRAENGE koordinieren.

---

## 12. Umsetzungstickets (VORSCHLAG für `docs/backlog-formulare.md` — erst nach Yama §13 befüllen)
- **FS-01 Ist-Bestand & Schema-Doku** — Ziel: dieses Dokument + `fields`-v1-Bestand fixieren. Dateien: docs/formular-*. Abh.: —. Akzeptanz: Schema v1 dokumentiert. Nicht im Scope: Code.
- **FS-02 ProductFormula JSON-Schema v2** — Ziel: v2-Schema + `schema_version`/`imported_from` (Migration-Konzept). Dateien: `ProductFormula`, Migration (Tag-X), Validator. Abh.: FS-01. Akzeptanz: Schema-Validierung grün. Nicht im Scope: Renderer.
- **FS-03 FormulaEvaluationService in ticket** — Port (kein eval). Dateien: `app/Services/Formular/*`. Abh.: FS-02. Akzeptanz: Operanden-Gate-Tests grün. Nicht im Scope: UI.
- **FS-04 visible_if Engine** — NEU (`VisibleIfService`, Syntax `Feld op Wert`). Abh.: FS-02. Akzeptanz: Sichtbarkeits-Tests. Nicht im Scope: Alpine-Entscheid (§13-6).
- **FS-05 SmartroutingService** — Tabelle `product_formula_routing_rules` + Port re-anchored. Abh.: FS-02. Akzeptanz: Routing-Tests (Mehrdeutigkeit/kein Treffer). Nicht im Scope: Phase-2-Anker.
- **FS-06 playground-Vorlagen-Mapper/Seeder Dry-Run** — Stored-Procedure-SQL + Laravel-Seeder → ticket-Eloquent-Seeder, `imported_from`, Dry-Run (kein Insert). Abh.: FS-02. Akzeptanz: Dry-Run listet 21 Vorlagen/358 Felder korrekt. Nicht im Scope: produktiver Seed.
- **FS-07 Builder/UI minimal erweitern** — EIN Editor/EIN Save-Pfad, v2-Keys, ticket-Design; Leichen entfernen. Abh.: FS-02/03/04. Akzeptanz: Builder speichert v2, kein `new Function`. Nicht im Scope: sidebar.
- **FS-08 Antwortspeicherung & Auswertung** — Server-Validierung, `filled_values`+Snapshot, Berechnung persistiert (`is_calculated`), Lead-/Objekt-Verankerung. Abh.: FS-03/05. Akzeptanz: idempotentes Speichern, Pflichtfeld-Serverprüfung. Nicht im Scope: OCR/recognition-Inhalt.
- **FS-09 Regression/Tests** — Suite §10. Abh.: alle. Akzeptanz: 0 Fehler, Anzahl ≥ Vorgänger.
- **FS-10 Navi/Terminologie-Cleanup** — „Checklisten-Formulare" führend, Alt-Checklisten-Rückbau (Code, 0 Zeilen), Doppelbegriff auflösen. Abh.: Yama §13-1/5. Akzeptanz: eine Formularwelt, ein Begriff. Nicht im Scope: NAV-Strang-Dateien.

---

## 13. Entscheidungspunkte für Yama (Ja/Nein)
1. Bleibt **„Checklisten-Formulare"** der führende Navi-Begriff? *(Empfehlung: Ja)*
2. Bleibt **`ProductFormula`** das führende Modell? *(Empfehlung: Ja)*
3. Bleibt **JSON** das Speicherformat in Phase 1? *(Empfehlung: Ja, mit `schema_version`+Server-Validierung)*
4. Dürfen **playground-Formularvorlagen als Seeder-Vorlagen** übernommen werden (mit `imported_from`, `risk_level`)? *(Empfehlung: Ja, als Dry-Run zuerst)*
5. Sollen die **hardcodierten PV/WP/Heatpump-Checklisten später abgelöst** werden (Code-Rückbau, da 0 Zeilen)? *(Empfehlung: Ja, eigener Posten)*
6. Darf **Alpine** für dynamische Sichtbarkeit genutzt werden, oder nur **jQuery**? **⚠️ Direktiven-Frage (E4):** ein JA erweitert den CLAUDE.md-Alpine-Scope (bisher nur `heizkoerper.*`) → **eigener Yama-Doku-Commit**, kein Formular-Detail. Bis dahin plant das Arbeitspaket **beide** Varianten: *visible_if mit Alpine* (deklarativ, wenig Code, aber Scope-Erweiterung) vs. *mit vorhandenem jQuery* (kein neues Framework, mehr Handarbeit/Wartung). **Keine Festlegung** ohne Yama.
7. **(E3-Zusatz)** Falls JSON diszipliniert wird — bleibt es bei JSON, oder **Normalisierung** auf Tabellen als **Phase 3**? *(Empfehlung: JSON Phase 1; Normalisierung nur bei belegtem Bedarf, hoher Blast-Radius)*

---

## 14. Klare Empfehlung
- **Bauen — aber gestuft und erst nach §13.** Der Wert ist hoch (sichere Engine ersetzt `new Function`), das Datenrisiko null (0 Zeilen), der Blast-Radius klein (ticket bleibt führend, additiv).
- **Reihenfolge:** FS-01 → FS-02 (Schema v2) → **FS-03 (sichere Formel-Engine)** → FS-04 (visible_if) → FS-05 (Routing) → FS-06 (Vorlagen-Dry-Run) → FS-07 (Builder) → FS-08 (Antworten) → FS-09 (Tests) → FS-10 (Cleanup).
- **Kleinster sicherer erster Schritt:** **FS-03 — `FormulaEvaluationService` als reiner, getesteter ticket-Service portieren** (kein UI, keine Migration, kein Bestandseingriff). Er ersetzt später das unsichere `new Function`, ist framework-neutral isoliert, und beweist den Synthese-Wert an einem Baustein — bevor irgendetwas an `product_formulas` oder der UI angefasst wird.

**→ STOPP.** Befund vollständig; Bau erst nach Yamas §13-Antworten (Backlog `docs/backlog-formulare.md` wird dann befüllt).
