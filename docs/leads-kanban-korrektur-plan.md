# Leads-Kanban (/lead/kanban) — Ist-Kartierung + gestufter Korrektur-Plan (Stufe 1)

> **Reine Analyse + Plan, kein Bau, keine Migration, keine bestehende Datei geändert.** Einziges Schreibprodukt: dieses Doc. Baut auf `kanban-ebenen-phase-aufgabe-arbeitsschritt-bestandsaufnahme.md` (c48f8ba) auf — wiederholt den Ebenen-Befund nicht, verifiziert nur die **Leads-Kanban-spezifischen** Punkte hart nach. Zielbild = Weiche 1 + 6 (`architektur-entscheidungen.md`) — steht nicht zur Debatte, es wird dagegen kartiert. Stand 2026-07-02, Belege wörtlich (Datei:Zeile / DESCRIBE / Live-Count).

---
## TEIL 1 — IST-KARTIERUNG (Leads-Kanban-spezifisch)

### 1. Spalten-Quelle heute
Board: `LeadOverviewController::kanban` (`:219`) → View `admin.kanban.kanban` (5.071 Z.); Feed `kanbanFeed` (`:444`, `GET /lead/kanban/feed`). **Spalten kommen aus `lead_stages`** (nicht hartkodiert, nicht offer_kanban_stages):
```php
// leadStagesForUi() :2778
$this->ensureDefaultLeadStages();
return LeadStage::query()->with(['subStages' => fn($q)=>$q->where('is_active',1)->orderBy('sort_order')…])
    ->…->orderBy('sort_order')->orderBy('id')->get();
```
Das Board blendet `junk`+`ticket` per `reject()` aus (`:266-272`). → **Spalten = `lead_stages`, Unter-Spalten = eingebettete `lead_stage_sub_stages`. Live rendert das Board 9 Stufen minus junk = ~8 Spalten** (7 sichtbar ohne junk/ticket; s. §3).

### 2. Karten-Quelle heute
**Eine Karte = ein Gewerk = `lead_product_lists`** (nicht `new_leads`/Kunde). `kanbanFeed` mappt je Gewerk auf die Spalte über den **String-Status** (kein FK):
```php
// kanbanFeed :1561-1562
$stageKey = normalizeStage($lead->stage ?? $lead->status ?? 'lead');
$stageId  = $stageIdsByKey[$stageKey];
```
**Live: 52 `lead_product_lists` (whereNull deleted_at) = 52 Karten.** Felder auf der Karte: Kunde (join new_leads), Objekt (lead_alternative_adds), Produkt (article_groups), Teams (`teams`-JSON), „nächste Aufgabe" (kanban_lead_tasks ODER berechnete Template-Aufgabe je product_id — s. Ebenen-Befund §4).

### 3. Stage-Inhalt LIVE ↔ 6-Phasen-Soll (kritisch)
**`lead_stages` (9 Zeilen, wörtlich):**
```
10 lead       Lead        closed=0     40 accepted  Annehmen   closed=0     70 completed Abschluss closed=1
20 offer      Angebot     closed=0     50 deal      Auftrag    closed=0     80 archive   Archive   closed=1
30 follow_up  Nachfassen  closed=0     60 project   Montage    closed=0     90 junk      Junk      closed=1
```
**`lead_stage_sub_stages`: 0 Zeilen** (zweite Ebene strukturell da, **komplett unbespielt**).

**Gegenüberstellung Ist (9) ↔ Soll (6 Weiche-1):**
| lead_stages-Ist | ist es eine Phase? | 6-Phasen-Soll | Vorschlag (Yama entscheidet) |
|---|---|---|---|
| `lead` Lead | ✅ Phase | **Lead** | behalten |
| `offer` Angebot | ✅ Phase | **Angebot** | behalten |
| `follow_up` Nachfassen | ❌ **Zustand** (Wiedervorlage) | — | **entfernen als Spalte** → Zustand „Wiedervorlage" (§6) |
| `accepted` Annehmen | ❌ **Übergang** (Zusage) | — | **entfernen als Spalte** → Übergang in Auftrag (§6) |
| `deal` Auftrag | ✅ Phase | **Auftrag** | behalten (Key `deal`→`auftrag`? Yama) |
| `project` Montage | ✅ Phase | **Montage** | behalten (Key `project`→`montage`? Yama) |
| — | fehlt | **Abnahme** | **NEU ergänzen** (zwischen Montage & Abschluss) |
| `completed` Abschluss | ✅ Phase | **Abschluss** | behalten |
| `archive` Archive | ❌ **Zustand** (archiviert) | — | entfernen als Spalte → Zustand |
| `junk` Junk | ❌ **Zustand** (verloren) | — | entfernen als Spalte → Zustand (heute schon per reject ausgeblendet) |
→ **`lead_stages` bildet die 6 Phasen NICHT ab:** 9 statt 6, **„Abnahme" fehlt ganz**, `Montage` heißt Key `project`, und **4 der 9 (follow_up/accepted/archive/junk) sind Zustände/Übergänge, keine Phasen.**

### 4. Schreibpfad `changeStage` (`:1959`, die 8-Parameter-POST) — wörtlich
Route: `POST /lead/kanban/{customer}/{alternative}/{product}/{employee?}/{service}/{stage}/{service_id}/{department_id}` (`web.php:889`). Schreibt in **EINER Transaktion ausschließlich `lead_product_lists`** (`:2100-2113`):
```php
$payload = [
    'status'        => $newStage,                       // normalizeStage(incoming) -> lead_stages.key als STRING
    'stage_history' => json_encode($history, …),        // Append {from,to,team_ids,changed_by,changed_at,description}
    'teams'         => json_encode($allTeamAssignments,…),
    'updated_at'    => now(),
];
if (Schema::hasColumn('lead_product_lists','lead_product_status')) $payload['lead_product_status'] = $newStage;  // Spalte existiert NICHT -> feuert nie
DB::table('lead_product_lists')->where('id',$locked->id)->update($payload);
$this->logActivity('updated','App\Models\LeadProductList', …);  // Activity-Log
```
**Einordnung:** Das ist **DER Kanban-Schreibpfad** im ~12-Schreibpfade-Zoo (Weiche 1). Er schreibt **nur** `lead_product_lists.status` (String) + `stage_history` + `teams`. **Er schreibt NICHTS in `new_leads`, NICHTS in `kanban_lead_tasks`, NICHTS in `lead_stage_sub_stage_id`, und NICHTS in die `lead_stages`-Wahrheit als FK** — der String `status` *ist* zwar ein `lead_stages.key`, aber es gibt keine FK-Verankerung. → **Die Phase wird heute als freier Key-String in `lead_product_lists.status` geführt, nicht in der Stage-Wahrheit.**

### 5. Stage-Bindung der Karte
- **`lead_product_lists.lead_stage_id`: existiert NICHT** (`Schema::hasColumn` = false). **`lead_stage_sub_stage_id`: existiert**, aber **0 von 52** Gewerken belegt.
- **Kein einziger Schreiber** von `lead_product_lists.lead_stage_sub_stage_id` gefunden (grep über `app/` nach `update(['lead_stage_sub_stage_id'…])` auf lead_product_lists = **0 Treffer**; nur Casts + Leser in Appointment/Ticket-Controllern). → **Spalte auf `lead_product_lists` faktisch tot.**
- **Ableitbarkeit Sub→Haupt vorhanden, aber unbespielt:** `LeadStageSubStage::leadStage()` = `belongsTo(LeadStage,'lead_stage_id')` (`LeadStageSubStage.php:84`) — d. h. *wäre* eine Sub-Stage gesetzt, ließe sich die Hauptphase ableiten; heute nicht nutzbar (0 Sub-Stages).
- **Die einzige lebende Phasen-Relation der Karte:** `LeadProductList::leadStage()` = **`belongsTo(LeadStage, 'status', 'key')`** (`LeadProductList.php:162`) — ein **String-Key-Join** `lead_product_lists.status = lead_stages.key`, kein FK-id. → Zielbild „Karte braucht nur die EINE Sub-Stage-Bindung" ist **erst nach Backfill** möglich (heute 0 gebunden).

### 6. Zustands-Vermischung (Phase ≠ Zustand)
Im heutigen Board stecken **Zustände/Übergänge als Pseudo-Phasen-Spalten**:
| Vermischung | heute | Zielbild-Zuordnung | Beleg |
|---|---|---|---|
| **`follow_up` (Nachfassen)** = Wiedervorlage | Spalte (5 Gewerke) | **Zustand „Wiedervorlage"**, Phase bleibt (s. Teil 2) | live: alle 5 follow_up-Gewerke haben ein `offer`, kein `deal` → Phase = **Angebot** + Zustand Wiedervorlage |
| **`accepted` (Annehmen)** = Zusage | Spalte (4) | **Übergang** in Auftrag (Weiche 1: Zusage = Übergang) | lead_stages :40 |
| **`archive`** = archiviert | Spalte | **Zustand „archiviert"** | is_closed=1 |
| **`junk`** = verloren | Spalte (heute per reject ausgeblendet) | **Zustand „verloren"** | is_closed=1, reject :266-272 |
→ 4 der 9 Spalten sind im Zielbild **Karten-Zustände**, keine Spalten.

### 7. Weitere Leser der heutigen Spalten-/Karten-Quellen (wer bricht bei Umstellung)
`lead_product_lists.status` / die Stage-Keys werden außerhalb des Boards gelesen:
| Datei:Zeile | Nutzung | Bricht bei Rendering-Umstellung? |
|---|---|---|
| `EmployeeDashboardController:1124,2089` | `whereIn('lpl.status',['lead','new'])` (Dashboard-„meine Leads") | Nein, solange `status` als Brücke weitergeschrieben wird |
| `EmployeeDashboardController:1889,1932` | select `lead_product_lists.status` | dito |
| `MainAppointmentController:1852,1913` | `leftJoin('lead_stages','lead_stages.key','=','lead_product_lists.status')` + `status as lead_stage_key` | dito (nutzt schon den Key-Join → **profitiert** von einer FK-Bindung, bricht nicht) |
| `CustomerMainAppointmentController:1050-1106` | dito Key-Join + liest `lead_stage_sub_stage_id` | dito |
| `PlannerEmployeeApiController:297` | `lpl.status as project_status` | dito |
| `CustomerNoteController`, `NewLeadsController` | rufen `normalizeStage()` | dito |
→ **Kein Leser bricht, solange `lead_product_lists.status` als Legacy-Brücke synchron mitgeschrieben bleibt** (genau das Weiche-1-Prinzip). Der Bruch-Risiko-Punkt ist NICHT das Weiterlesen von `status`, sondern das **Umstellen der Board-Zuordnung** von String auf FK (Stufe B) — dort muss die Sub-Stage-Bindung vollständig backgefüllt sein, sonst landen Karten in „keiner Spalte". *(NICHT VERIFIZIERT: ob eine der Ansichten die 9 Keys hart erwartet und bei Vokabular-Änderung [z. B. `project`→`montage`] bricht — das ist die eigentliche Gefahr, s. Stufe A/B-Risiko.)*

### 8. Aufgaben/Arbeitsschritt-Anbindung an die Board-Karte
- **`context(Request, LeadProductList $leadProduct)`** (`:29`) ist **per-Gewerk** (ein leadProduct) → liefert `tasks` + `field_progress` + `employees`. **Für 52 Board-Karten je ein context-Call = 52 Calls = N+1** → **nicht** für das Board-Rendering geeignet.
- **`summaries(Request)`** (`:190`, `POST /admin/kanban/tasks/summaries`) ist ein **Batch-Endpunkt**: `validate(['lead_product_list_ids'=>['required','array']])`, `whereIn('id',$ids)` + `whereIn('lead_product_list_id',$ids)` → Zähler je Gewerk in **einem** Query. → **Das ist das richtige Werkzeug für Board-Badges** (open/reported/done-Zähler je Karte, ein Call für alle 52). *(NICHT VERIFIZIERT: ob `summaries` bereits einen `reported`-Zähler getrennt ausweist — beim Bau prüfen/ergänzen; B3-Status `reported` ist additiv.)*
- **Montage-Fortschritt:** `montageFieldProgress` (`:116`) rechnet aus `planner_items` (Weg A) — heute per-Gewerk im Drawer; fürs Board entweder in `summaries` integrieren oder separater Batch.

---
## TEIL 2 — BESTANDSDATEN-MAPPING (Vorschlag — YAMA ENTSCHEIDET)
`lead_product_lists.status` live (52 Gewerke, whereNull deleted_at):
| Status-Wert | Anzahl live | Vorschlag PHASE | Vorschlag ZUSTAND | Begründung (Beleg) | Yama-Frage |
|---|---:|---|---|---|---|
| `lead` | 23 | **Lead** | aktiv | direkte Entsprechung | — |
| `offer` | 10 | **Angebot** | aktiv | direkte Entsprechung | — |
| `follow_up` | 5 | **Angebot** | **Wiedervorlage** | live-plausibilisiert: alle 5 haben `offers`-Eintrag, keinen `deals` → in Angebot-Phase, nur nachzufassen | „follow_up = Angebot + Wiedervorlage" bestätigen? Oder eigene Datenlage je Fall? |
| `accepted` | 4 | **Auftrag** | aktiv (gewonnen) | Zusage = Übergang in Auftrag (Weiche 1) | Sind die 4 schon Auftrag, oder noch Angebot-mit-Zusage? (kein `deals`-Check gemacht) |
| `deal` | 8 | **Auftrag** | aktiv | direkte Entsprechung | Sind einzelne davon schon in Montage? (planner_items live 0 → datenseitig keiner „in Montage") |
| `project` | 2 | **Montage** | aktiv | Key `project` = Montage (lead_stages :60) | — |
*(Keine Live-Zeilen mit `completed`/`archive`/`junk` → im Mapping nicht belegt; bei Auftreten → Abschluss / Zustand archiviert / Zustand verloren.)*
→ **Backfill-Regel (umkehrbar):** je Gewerk `ziel_stage = lead_stages.where(key = normalizeStage(status)).id` (bzw. nach Yamas Vokabular), Zustand aus der Tabelle oben. Da `status`-Werte **bereits** lead_stages-Keys sind, ist das ein **1:1-Backfill** (kein unscharfes Raten) — die einzige Unschärfe sind die Vermischungs-Zeilen (follow_up/accepted).

---
## TEIL 3 — GESTUFTER UMBAU-PLAN (vorlegen, NICHT bauen)

### STUFE A — additiv, kein Verhaltenswechsel
**Inhalt:** (1) Stage-Vokabular an Weiche 1 angleichen (nach Yamas Teil-1-§3-Entscheidung: 6 Phasen, „Abnahme" ergänzen, follow_up/accepted/archive/junk als Nicht-Phasen markieren). (2) `lead_product_lists` eine **Sub-Stage-Bindung befüllen** (Backfill aus Teil 2) — **entweder** die tote `lead_stage_sub_stage_id` nutzen **oder** (sauberer) eine neue `lead_stage_id`-Spalte einführen (Yama-Entscheidung, s. Ebenen-Befund §6.2 Kandidat 2). (3) `changeStage` schreibt **zusätzlich** die Stage-Bindung (FK), **Legacy `status` exakt wie heute weiter** = Brücke.
**Umfang:** 1 Migration (Spalte/Backfill) + `changeStage` additiv erweitern + ein Backfill-Kommando (umkehrbar). **Risiko: niedrig** (additiv; Board rendert weiter aus `status`). **Verifikation:** nach A sind beide Wahrheiten synchron; Board unverändert (HTTP 200, gleiche Spalten/Karten); Backfill idempotent + rückrollbar. **Yama entscheidet:** Vokabular (Keys/Namen, Abnahme), UND `lead_stage_sub_stage_id` wiederbeleben vs. neue `lead_stage_id`-Spalte.

### STUFE B — Rendering-Umstellung
**Inhalt:** Board-Spalten aus `lead_stages` (schon so) **+ Karten-Zuordnung von String auf die FK-Stage-Bindung** umstellen (`kanbanFeed` liest die Bindung statt `normalizeStage(status)`); Legacy-`status` wird **nur noch mitgeschrieben, nicht mehr gelesen** (im Board). **Parallel schaltbar:** die alte String-Zuordnung als Fallback behalten, bis abgenommen (Feature-Flag / die alte Route bleibt).
**Umfang:** `kanbanFeed`-Mapping + View-Anpassung (Sub-Stages als Swimlanes/Feinspalten — Darstellungs-Entscheidung). **Risiko: mittel** — **Bruchgefahr:** (a) Karten ohne Stage-Bindung landen in keiner Spalte (→ A muss 100% backfillen); (b) Ansichten aus §7, die die **9 alten Keys** hart erwarten, brechen bei Vokabular-Änderung (`project`→`montage`, follow_up entfällt). **Verifikation:** alle 52 Karten erscheinen in genau einer Spalte; Verschieben schreibt FK **und** Legacy synchron; die §7-Leser (Dashboard/Appointments) liefern unverändert. **Yama entscheidet:** Sub-Stage-Darstellung (Swimlane vs. Feinspalte), Fallback-Dauer.

### STUFE C — Karten-Anreicherung
**Inhalt:** je Karte Aufgaben-Badges (open/**reported**/done-Zähler via **`summaries`**, Batch — kein N+1) + Montage-Fortschritt (`montageFieldProgress`-Muster, batch) + Prüfer-Hinweis („Zu prüfen: N", aus B3/fa41c61). **Umfang:** `summaries` um `reported`-Zähler ergänzen (falls fehlt) + Board-Badges. **Risiko: niedrig** (additive Anzeige, bestehende Endpunkte). **Verifikation:** Board zeigt korrekte Zähler; ein Batch-Call statt 52; reported-Karten sichtbar markiert. **Yama entscheidet:** welche Badges (nur Zähler vs. Mini-Liste).

### STUFE D — Zustands-Dimension
**Inhalt:** Zustand (Wiedervorlage-mit-Datum / verloren / pausiert / archiviert) als **Karten-Zustand** setzen/anzeigen (NICHT als Spalte). Anbindung an die follow_up-Bereinigung (Teil 2) + **Verbindung zum Follow-up-Konzept** (`follow-up-bestandsaufnahme.md` — `lead_reminders` als Wiedervorlage-Träger). **Umfang:** ein Zustands-Feld/-Quelle je Gewerk + Karten-Badge + Filter. **Risiko: mittel** (neue Dimension; hängt an der Follow-up-Design-Entscheidung — nicht hier bauen). **Verifikation:** follow_up-Gewerke erscheinen in Phase Angebot **mit** Zustand Wiedervorlage; verloren/archiviert filterbar. **Yama entscheidet:** Zustands-Quelle (neue Spalte vs. `lead_reminders`-Kopplung) — **eigener Pflicht-Stopp, verzahnt mit Follow-up**.

### STUFE E — Seeder + Test (Yamas Testplan)
**Inhalt:** das Test-Harness (`database/seeders/Testing/`, `[TEST-HARNESS]`-Marker, local-Sperre — entsteht in Commit 3 des Bug/Harness-Strangs) **erweitern** um Kanban-Szenarien: mehrere Projekte/Gewerke über **alle 6 Phasen** verteilt, Aufgaben in open/reported/done, Montage-Fortschritt in Stufen, je ein Wiedervorlage-/verloren-Zustand. **Umfang:** ein `LeadsKanbanTestSeeder` (am selben Marker, gleiche local-Sperre + Teardown). **Risiko: niedrig** (nur Testdaten, nie automatisch). **Verifikation je Board-Stufe:**
- **A:** Seeder legt Gewerke mit Legacy-`status` + FK-Bindung an → Backfill-Idempotenz beweisbar; beide Wahrheiten synchron.
- **B:** alle 6 Phasen haben Karten → jede Spalte gefüllt, Umstellung sichtbar korrekt.
- **C:** Gewerke mit open/reported/done-Aufgaben + Fortschritt → Badges/Zähler stimmen; reported-Karte trägt Prüfer.
- **D:** ein Wiedervorlage- + ein verloren-Gewerk → Zustand sichtbar, nicht als Spalte.
**Yama:** Umfang der Szenarien (wie viele Gewerke je Phase).

### Sequenz-Empfehlung + was schiefgehen kann (ehrlich)
**A → E(A-Fälle) → B → E(B-Fälle) → C → D.** Kern-Risiken:
- **Vokabular-Änderung bricht §7-Leser:** wenn `project`→`montage` oder follow_up entfällt, brechen Stellen, die die **alten Keys hart** erwarten (Dashboard `whereIn('lpl.status',['lead','new'])`, Appointment-Key-Joins). **Gegenmittel:** in A **nur ergänzen** (Abnahme + FK-Bindung), Keys **vorerst NICHT umbenennen** (Namen ändern reicht fürs UI); Key-Renames erst in einem eigenen Schritt mit Migration aller Leser.
- **Backfill unvollständig → Karten verschwinden** in B: A muss 52/52 binden, mit Prüf-Query.
- **follow_up/accepted-Fehlmapping:** die Vermischungs-Zeilen brauchen Yamas Urteil (Teil 2), sonst landet ein Angebot-Wiedervorlage-Fall fälschlich in „Auftrag".
- **Doppel-Wahrheit-Drift** (Weiche-1-Kern): solange `status` + FK-Bindung beide geschrieben werden, müssen sie **über die eine `changeStage`-Stelle** synchron bleiben — kein zweiter Schreibpfad daneben.

---
## ALLE YAMA-ENTSCHEIDUNGEN (gesammelte Fragenliste)
1. **Stage-Vokabular (Teil 1 §3):** die 9 lead_stages auf 6 Phasen bringen — **„Abnahme" als neue Stufe zwischen Montage & Abschluss ergänzen**; `follow_up`/`accepted`/`archive`/`junk` als Nicht-Phasen (Zustände) herausnehmen. Keys umbenennen (`deal`→`auftrag`, `project`→`montage`) **jetzt** oder später (Risiko §7)?
2. **Karten-Phasenträger (Stufe A):** die tote `lead_product_lists.lead_stage_sub_stage_id` wiederbeleben — **oder** eine saubere neue `lead_product_lists.lead_stage_id`-Spalte einführen (empfohlen, aber deine Wahl)?
3. **Bestandsdaten-Mapping (Teil 2):** `follow_up` (5) = Angebot+Wiedervorlage bestätigen? `accepted` (4) = Auftrag oder Angebot-mit-Zusage? `deal`/`accepted` schon teilweise Montage (datenseitig: nein)?
4. **Sub-Stage-Darstellung (Stufe B):** Feinstufen als Swimlanes, Feinspalten, oder Drill-down?
5. **Zustands-Quelle (Stufe D):** neue Zustands-Spalte am Gewerk vs. Kopplung an `lead_reminders` (Follow-up-Konzept) — eigener Pflicht-Stopp.
6. **Board-Badges (Stufe C):** nur Zähler (open/reported/done) oder Mini-Aufgabenliste je Karte?

**Nächster Schritt nach deiner Entscheidung zu (1)+(2)+(3): Stufe A als eigener Pflicht-Stopp bauen.**

---
## Gelesen / NICHT gelesen (ehrlich)
**Live geprüft:** `lead_stages` (alle 9 wörtlich), `lead_stage_sub_stages` (0), `lead_product_lists` Spalten (`lead_stage_id`=NEIN, `lead_stage_sub_stage_id`=JA, `status`=JA, `lead_product_status`=NEIN) + Counts (52; Sub-Stage 0/52; status-Verteilung 23/10/8/5/4/2); follow_up-Gewerke gegen `offers`/`deals` plausibilisiert (5×offer, 0×deal). **Code:** `LeadOverviewController` `changeStage` (:1959-2160 wörtlich — nur lead_product_lists geschrieben), `leadStagesForUi` (:2778), `stageMap`/`normalizeStage` (:2690/:2757), `kanbanFeed`-Mapping (:1561-1562 via Ebenen-Befund); `KanbanLeadTaskController` `context` (:29), `summaries` (:190, Batch); Models `LeadProductList::leadStage()` (:162, String-Join) / `leadStageSubStage()` (:180), `LeadStageSubStage::leadStage()` (:84); §7-Leser (EmployeeDashboard/MainAppointment/CustomerMainAppointment/PlannerEmployeeApi); grep „kein Schreiber von lead_stage_sub_stage_id auf lead_product_lists". **Aufgebaut auf** `kanban-ebenen-…-bestandsaufnahme.md` (c48f8ba) für die Ebenen-/Bruch-Befunde (nicht wiederholt).
**NICHT (vollständig):** `admin/kanban/kanban.blade.php` (5.071 Z.) nur strukturell — die **exakte Frontend-Darstellung** der Sub-Stages (Swimlane vs. Spalte) = **NICHT VERIFIZIERT**; `LeadOverviewController` (286 KB) nur gezielt — ein abweichender Zuordnungspfad nicht 100 % ausgeschlossen; `summaries`-Output-Format nicht Feld-für-Feld (ob `reported` bereits getrennt) = **NICHT VERIFIZIERT**; `accepted`-Gewerke NICHT gegen `deals` plausibilisiert (nur follow_up); ob eine §7-Ansicht die 9 Keys **hart** erwartet = **NICHT VERIFIZIERT** (Grep, nicht Laufzeit).

## Selbstkritik
- **Der Plan ist groß** — bewusst gestuft, damit A/B/C/D/E einzeln abnehmbar sind; die eigentliche Arbeit (und das Risiko) steckt in **B** (String→FK) und im **Vokabular-Rename**, das §7-Leser brechen kann. Ich empfehle, Keys **vorerst nicht** umzubenennen (nur Namen + Abnahme ergänzen) — das ist eine Bewertung, keine Vorgabe.
- **Mapping-Unschärfe:** ich habe `follow_up` live plausibilisiert (5×offer/0×deal), `accepted` **nicht** — dort steht meine Phase-Zuordnung (Auftrag) auf Weiche-1-Logik, nicht auf Daten. Als Yama-Frage markiert.
- **„Karte braucht nur EINE Sub-Stage-Bindung"** setzt voraus, dass Sub-Stages je Phase überhaupt gepflegt werden — heute 0. Solange keine Sub-Stages existieren, wäre eine **Haupt-Stage-Bindung** (`lead_stage_id`) das Ehrlichere; die Sub-Ebene ist Zukunft. Deshalb Frage (2).
- **Kollisionsfrei zum parallelen Strang:** ich habe **keine** der Bug/Harness-Dateien angefasst (nur gelesen: LeadOverview/KanbanLeadTask), nur dieses Doc geschrieben.

---
*Reine Analyse — nichts am Code/Schema geändert. Querverweise: `kanban-ebenen-phase-aufgabe-arbeitsschritt-bestandsaufnahme.md` (c48f8ba), `architektur-entscheidungen.md` (Weiche 1/6), `follow-up-bestandsaufnahme.md`, `glossar.md`. Belege: siehe Inline-Datei:Zeile + DB-Live 2026-07-02.*
