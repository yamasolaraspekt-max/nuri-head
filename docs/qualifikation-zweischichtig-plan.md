# Qualifikation zweischichtig (universal vs. gewerkespezifisch) — Bestand + Design (Stufe 1)

> **Reine Analyse + Plan, kein Bau.** Einziges Schreibprodukt: dieses Doc. Additiv auf B1 (`4e873c0`) + B3 (`ec35ad7`). **KEIN neues Vokabular-System** — die eine Rang-Leiter `position_qualifications` bleibt das Vokabular, sie wird nur **zweifach verwendet** (universal + je Gewerk). Stand 2026-07-02, Belege wörtlich.

**Geschäftsregel (Yama, gewerkeübergreifender Vollsanierer):** UNIVERSAL = gewerkeunabhängiger Rang (Führung/Kommando). GEWERKESPEZIFISCH = fachliche Stufe JE GEWERK (dieselbe Person: Elektro=Obermonteur, SHK=Helfer). B3-Prüfentscheid muss die Stufe der Person **im Gewerk der Tätigkeit** verwenden. Eine Person = 1 universale Stufe + beliebig viele Gewerk-Stufen.

---
## TEIL 1 — BESTAND

### 1. Die Gewerk-Achse: `article_groups` (Produkte) vs. `departments` (Gewerke) — KEIN Link
**`article_groups` (13, live) = PRODUKTE, nicht Trades:**
```
2 Wärmepumpe · 4 Wallbox · 6 Photovoltaik · 7 Batteriespeicher · 8 Fenster · 9 Türen
10 Badsanierung · 11 Küche · 12 Fliesen · 13 Dach · 14 Insektenschutz · 15 Fliegengitter · 16 Tapete
```
**`departments` (16, live) = GEWERKE/Trades + Org:**
```
39 Heizung · 40 Elektro · 41 SHK · 42 Bauelemente · 43 Schreiner · 44 Dachdecker · 45 Maler
46 Fliesenleger · 47 Baudekoration | 48 Controlling · 49 Marketing · 50 Finanzen · 51 Buchhaltung
· 52 Verwaltung · 53 Management · 54 Geschäftsführung
```
**KERNBEFUND:** `article_groups` sind **Produkte/Verkaufsgruppen** (Wärmepumpe, PV, Dach…), `departments` sind die **Trades** (Elektro, SHK, Dachdecker…) + Org-Einheiten. **Es gibt KEINEN Link:** `article_groups`-Spalten = `id, article_group, initial, min_value, max_value, image` — **kein `department_id`**. Ein Produkt (Wärmepumpe) wird von einem Trade (Heizung/SHK) ausgeführt, aber die Zuordnung ist **nirgends hinterlegt** (NICHT VERIFIZIERT: keine Mapping-Tabelle gefunden; grep `article_groups`↔`department` = 0).

**Was B1/B3 heute ankern:** `phase_activities.product_id → article_groups` (B1: die Tätigkeit kennt ihr **Produkt**). → Die Anforderung ist heute implizit **produkt**-spezifisch (article_group), NICHT trade-spezifisch (department).

**Empfehlung (Yama entscheidet die Achse):**
- **Achse A — `article_groups` (empfohlen für den direkten Bau):** Die Tätigkeit trägt `product_id` direkt → R1-Auflösung ohne Mapping-Schicht (activity.product_id → Gewerk-Stufe der Person für **dieses article_group**). n:m = `employee × article_group × qualification`. „Person X: Wärmepumpe=Obermonteur, PV=Helfer." **Vorteil:** kein product→trade-Mapping nötig, deckt sich mit dem B1-Anker. **Nachteil:** produkt-granular (13), nicht trade-granular — eine Elektro-Fachkraft müsste PV+Wallbox+Batteriespeicher einzeln pflegen.
- **Achse B — `departments` (Yamas Sprache Elektro/SHK):** trade-granular (9 Trades), eine Elektro-Stufe deckt alle Elektro-Produkte. **ABER:** braucht zuerst ein **product→department-Mapping** (welches article_group gehört zu welchem Trade — existiert nicht, müsste gebaut werden), und B3 müsste activity.product_id → department auflösen. Größerer Vorbau.
→ **Meine Empfehlung: Achse A (`article_groups`)** — direkt, additiv, kein Mapping-Vorbau; wenn Yama Trade-Granularität will, ist das product→department-Mapping die Vorbedingung (eigener Schritt).

### 2. Die 26 Qualifikationen — Klassifikation (Vorschlag, Yama entscheidet)
| sort | id·name | universal (Führung/Org) | gewerk-Stufe (fachlich) |
|---|---|---|---|
| 1 | 1 Geschäftsführung | ✅ | |
| 2 | 13 Management · **2 Elektromeister** | ✅ Management | ✅ Elektromeister (trade-gebacken) |
| 3 | **14 Meister** · **3 Elektrofachkraft** | | ✅ beide |
| 4 | **15 Geselle** · **4 Anlagenmechaniker SHK** | | ✅ beide |
| 5 | **16 Helfer** · **5 PV-Monteur** | | ✅ beide |
| 6 | **6 Dachmonteur** · 17 Techniker | (Techniker?) | ✅ Dachmonteur |
| 7 | 7 Disponent · 18 Planer | ✅ beide | |
| 8 | 8 Vertriebsberater · 19 Designer | ✅ beide | |
| 9 | 9 Projektplaner · 20 Controlling | ✅ beide | |
| 10 | 10 Lagerist · 21 Außendienst | ✅ beide | |
| 11 | 11 Buchhalter · 22 Innendienst | ✅ beide | |
| 12 | 12 Bürokraft · 23 Buchhaltung | ✅ beide | |
| 13–15 | 24 Marketing · 25 Verwaltung · **26 Ausbildung** | ✅ Marketing/Verwaltung | ✅ Ausbildung (Azubi) |
**Beobachtung:** Einige Quals sind **trade+Stufe kombiniert** (Elektromeister, PV-Monteur, Dachmonteur, Anlagenmechaniker SHK) — die wären in einer sauberen Zweischichtung eigentlich „Trade × Stufe". Die **generischen Stufen** (Meister/Geselle/Helfer/Ausbildung) sind trade-agnostisch = ideal für die Gewerk-Schicht. **Fehlend (Yamas Kommando-Achse, aus Vorbefund):** Abteilungsleiter, Projektleiter, Bauleiter, Obermonteur, generischer Monteur — **Ergänzungs-Vorschlag** (neue Zeilen in DERSELBEN `position_qualifications`, sort_order eingeordnet): z. B. Abteilungsleiter (sort ~1.5), Projektleiter/Bauleiter (~2–3), Obermonteur (~4, über Geselle), Monteur (~4–5). **Yama entscheidet die Liste + sort_order.**

### 3. Wo employees.qualification_id gepflegt wird — **NICHT eindeutig lokalisiert**
- `resources/views/admin/employee/position/position.blade.php` + `_table.blade.php` verwalten den **Quals-Katalog + Hierarchie-Matrix** (`PositionController`, Route `position.index` :2241) — das ist die `position_qualifications`-Pflege, **NICHT** die MA↔Qualifikation-Zuordnung.
- **Kein expliziter Schreiber** von `employees.qualification_id` in `EmployeeController` gefunden (grep = 0 explizite `'qualification_id' =>`-Zuweisung). `employees.qualification_id` ist live befüllt (50/51, Vorbefund) — **Herkunft NICHT VERIFIZIERT** (vermutlich Seeder `DemoCompanyMasterDataSeeder` und/oder ein Employee-Formularfeld per Mass-Assignment; der exakte Schreib-Controller ist beim Bau zu pinnen).
- **Konsequenz für Punkt 8:** die Gewerk-Stufen-Pflege gehört in die **Employee-Bearbeitung** — der genaue Ort (EmployeeController + Employee-Edit-Blade) ist beim Bau zu bestätigen. *(Ehrlich: hier habe ich keinen harten Beleg — das ist die einzige unklare Stelle in Teil 1.)*

### 4. Nuriva/Bestands-Check — kein API liest `employees.qualification_id`
Grep `qualification_id` in `Api/` + `Planner/`: die Treffer sind **`master_set_labor.qualification_id` / `task_labor.qualification_id`** (Costing-Labor-Zeilen, join auf `position_qualifications` — `MasterSetApiController:950/1129`), **nicht** `employees.qualification_id`. B3 liest `employees.qualification_id` nur intern (`PlannerEmployeeApiController:1967`, kein Payload). → **Kein API-Payload gibt `employees.qualification_id` aus**; eine neue `employee_gewerk_qualifications`-Tabelle würde **nirgends durchsickern** (die Costing-Quals sind eine andere Achse). ✅

---
## TEIL 2 — DESIGN (Vorschlag, nicht bauen)

### 5. Datenmodell (additiv) — neue n:m-Tabelle
Achse gemäß Yama-Entscheidung (Platzhalter `<gewerk>_id` = `article_group_id` [Empfehlung] oder `department_id`):
```php
// up()
Schema::create('employee_gewerk_qualifications', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('employee_id');
    $table->unsignedBigInteger('article_group_id');          // = Gewerk-Achse (Empfehlung A); ODER department_id
    $table->unsignedBigInteger('qualification_id');          // -> position_qualifications (dieselbe Leiter)
    $table->timestamps();
    $table->softDeletes();                                   // ja: Pflege/Historie, konsistent zu position_qualifications (SoftDeletes)
    $table->unique(['employee_id','article_group_id'], 'egq_emp_gewerk_unique');   // EINE Stufe je (Person, Gewerk)
    $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
    $table->foreign('article_group_id')->references('id')->on('article_groups')->cascadeOnDelete();
    $table->foreign('qualification_id')->references('id')->on('position_qualifications')->cascadeOnDelete();
});
// down(): Schema::dropIfExists('employee_gewerk_qualifications');
```
**`employees.qualification_id` BLEIBT unverändert = die UNIVERSALE Stufe.** SoftDeletes: **ja** (Pflege-UI kann Einträge „entfernen" ohne Historie zu verlieren; konsistent zu `position_qualifications`). FK-Stil: `->foreign()...` (stilkonform zu `phase_activities`/`kanban_lead_tasks`).

### 6. Auflösungs-Regel für B3 (Kern — Yama entscheidet)
Tätigkeit hat `product_id` (= article_group = Gewerk-Achse A). Effektive Stufe der Person für den Vergleich:
- **R1:** Gewerk-Stufe der Person für **dieses** article_group (aus `employee_gewerk_qualifications`), falls gepflegt.
- **R2 (Fallback), zwei Varianten:**
  - **WEICH (empfohlen zur Einführung):** ohne Gewerk-Eintrag → **universale Stufe** (`employees.qualification_id`) gilt = **heutiges Verhalten**. → Sanfte Einführung: solange keine Gewerk-Stammdaten gepflegt sind, kippt **nichts** auf `reported`.
  - **STRENG:** ohne Gewerk-Eintrag → **nicht qualifiziert im Gewerk** → Prüfung nötig. → Konservativ, aber **kippt SOFORT alle ungepflegten Fälle auf reported** (Stammdaten-Pflicht vorher).
- **Empfehlung: WEICH als Einführung** (feature-verträglich, kein Verhaltensbruch), **STRENG später umschaltbar** (ein Konfig-Flag), wenn die Gewerk-Stammdaten gepflegt sind. Begründung: B3 ist gerade erst live; ein sofortiges Massen-`reported` (STRENG bei 0 Gewerk-Einträgen) wäre ein Verhaltensbruch für ALLE Montage-Abschlüsse.
- **PRÜFER-ERMITTLUNG (konsistent):** `resolveReviewer` klettert heute die supervisor-Kette mit der **globalen** Stufe. **Vorschlag: ja, konsistent** — jedes Ketten-Mitglied per **derselben R1/R2** gegen die Anforderung prüfen (Gewerk-Stufe für das article_group der Tätigkeit, sonst Fallback). → `resolveReviewer($employeeId, $requiredSort, $productId)` bekommt `productId` und nutzt `resolveEffectiveQualSort($memberId, $productId)` statt `employeeQualSort($memberId)`. **Yama entscheidet:** Prüfer muss im Gewerk qualifiziert sein — ja/nein.

### 7. B3-Integration (additiv, feature-gesichert) — Diff-Umriss
Ein neuer Helper + Umleitung des Stufen-Lookups in `applyMontageQualificationRueckfluss` (`PlannerEmployeeApiController`):
```php
// NEU:
private function resolveEffectiveQualSort(int $employeeId, int $productId): ?int {
    if ($productId > 0 && Schema::hasTable('employee_gewerk_qualifications')) {
        $qid = DB::table('employee_gewerk_qualifications')
            ->where('employee_id',$employeeId)->where('article_group_id',$productId)
            ->when($this->safeColumn('employee_gewerk_qualifications','deleted_at'), fn($q)=>$q->whereNull('deleted_at'))
            ->value('qualification_id');
        if ($qid) return (int) DB::table('position_qualifications')->where('id',$qid)->value('sort_order');
        // R2 WEICH: kein Gewerk-Eintrag -> universale Stufe (Fallback)
    }
    return $this->employeeQualSort($employeeId);   // = heutiges Verhalten
}
// in applyMontageQualificationRueckfluss: productId = phase_activities.product_id der Tätigkeit (source_id);
//   $performerSort = $this->resolveEffectiveQualSort($employeeId, $productId);   // statt employeeQualSort
// resolveReviewer bekommt $productId + nutzt resolveEffectiveQualSort je Ketten-Mitglied.
```
**Greift NUR wenn die n:m-Tabelle existiert; ohne Gewerk-Einträge exakt wie heute** (WEICH). STRENG = eine Zeile (Fallback → null statt universale Stufe), per Flag.

### 8. Pflege-UI (Entwurf)
In der **Employee-Bearbeitung** (Punkt 3, Ort beim Bau pinnen) eine **Gewerk-Stufen-Liste**: mehrere Zeilen `[Gewerk-Dropdown (article_groups)] × [Stufen-Dropdown (position_qualifications, sort-sortiert)]` mit add/remove — Muster wiederverwendbar aus dem modal-activity-Dropdown (B1) bzw. dem `department_positions`-Mehrzeilen-Muster. **B1-Pflege bleibt unverändert** (Anforderung ist schon je Tätigkeit = je Produkt/Gewerk). `employees.qualification_id` (universal) bleibt ein einzelnes Select wie heute.

### 9. Test-Harness-Erweiterung (benennen)
`QualifikationTestSeeder` (Harness aus dem Bug/Harness-Strang) um Gewerk-Fälle ergänzen: (i) Person **hoch in Gewerk A + niedrig in Gewerk B** → dieselbe Anforderung ergibt **done in A, reported in B**; (ii) Person **ohne** Gewerk-Eintrag → **Fallback-Variante** greift (WEICH: universale Stufe → wie heute); (iii) **Prüfer-Kette mit Gewerk-Auflösung** (Chef qualifiziert im Gewerk vs. nur global). Am `[TEST-HARNESS]`-Marker, local-Sperre, Teardown.

---
## GESAMMELTE YAMA-ENTSCHEIDUNGEN
1. **Gewerk-Achse:** `article_groups` (Produkte — Empfehlung, direkt am B1-Anker) **oder** `departments` (Trades — braucht product→department-Mapping als Vorbau)?
2. **Vokabular:** Klassifikation der 26 (Teil-1-§2) bestätigen + **welche Kommando-Rollen ergänzen** (Abteilungsleiter/PL/Bauleiter/Obermonteur/Monteur) und mit welchem sort_order?
3. **Fallback R2:** **WEICH** (Empfehlung, keine Verhaltensänderung) oder STRENG (kippt ungepflegte sofort auf reported)?
4. **Prüfer im Gewerk qualifiziert:** ja (konsistent, Empfehlung) oder nein (Prüfer per globaler Stufe)?

**Nach diesen 4 Entscheidungen: gestufter Bau (Migration → Pflege-UI → B3-Auflösung → Harness-Fälle), jeder Schritt eigener Pflicht-Stopp.**

---
## Gelesen / NICHT gelesen
**Live geprüft:** `article_groups` (alle 13, = Produkte), `departments` (alle 16, = Trades+Org), `article_groups`-Spalten (kein department_id → kein Link), alle 26 `position_qualifications` (id/name/sort_order/preis), `employees.qualification_id`-Leser B3 (`PlannerEmployeeApiController:1967`), API-Grep (`MasterSetApiController` liest Labor-Quals, nicht employee-Quals). **Code:** B1 `LeadTaskPhaseManagementController` (required_qualification_id), B3 `applyMontageQualificationRueckfluss`/`employeeQualSort`/`resolveReviewer` (aus B3-Bau bekannt); `position.blade`/`PositionController` (Katalog-Pflege). Aufgebaut auf `mitarbeiter-hierarchie-bestandsaufnahme.md`, `qualifikation-fundament-modell-geprueft.md`.
**NICHT verifiziert:** **exakter Schreiber von `employees.qualification_id`** (nicht lokalisiert — vermutl. Seeder/Employee-Form; beim Bau pinnen); ob ein product→department-Mapping doch irgendwo (meta/Frontend) existiert (keins gefunden); ob `position.blade`-Modal (`#m-qid`) doch employee-Quals setzt (es managt den Katalog, nicht die Zuordnung — nicht 100 % durchtracet); Trade-Semantik der kombinierten Quals (Elektromeister etc.) — meine Klassifikation ist ein Vorschlag.

## Selbstkritik
- **Die Achsen-Frage ist die eigentliche Design-Entscheidung** und ich kann sie nicht für Yama treffen: `article_groups` (direkt, aber produkt-granular) vs. `departments` (Yamas Sprache, aber Mapping-Vorbau). Ich empfehle A, aber das ist eine Bewertung — bei „eine Elektro-Fachkraft für alle Elektro-Produkte" kippt es zu B.
- **Die kombinierten Quals** (Elektromeister/PV-Monteur/Dachmonteur) sind eine **halbe Zweischichtung schon im Vokabular** — sie mischen Trade + Stufe. Sauber wäre „Trade (Achse) × generische Stufe (Meister/Geselle/Helfer)". Das aufzulösen wäre eine Vokabular-Bereinigung, die ich bewusst NICHT vorschlage (kein neues System danebenbauen) — aber es bleibt eine Inkonsistenz, die die Gewerk-Schicht teilweise dupliziert.
- **Der unklare employees.qualification_id-Schreiber** (§3) ist die schwächste Stelle des Befunds — ehrlich als NICHT VERIFIZIERT markiert; die Pflege-UI-Verortung (§8) hängt daran und ist beim Bau zu pinnen.

---
*Reine Analyse — nichts geändert. Querverweise: `mitarbeiter-hierarchie-bestandsaufnahme.md`, `qualifikation-fundament-achsen-analyse.md`, `qualifikation-fundament-modell-geprueft.md`, B1 (4e873c0)/B3 (ec35ad7), `glossar.md`. Belege: DB-Live 2026-07-02 + Datei:Zeile inline.*
