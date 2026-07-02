# Mitarbeiter-Hierarchie & Berechtigungen — Bestandsaufnahme (reine Analyse)

> **Nur Lesen/Analyse, kein Bau, kein Umsetzungs-Vorschlag.** Klärt: Existiert schon ein Fundament für Yamas geplante 9-Stufen-Hierarchie (Abteilungsleiter > Projektleiter > Bauleiter > Obermonteur > Monteur > Helfer > Azubi 3/2/1), aus der später „braucht eine Aufgabe PL-Prüfung?" folgt? Stand 2026-07-02, Belege wörtlich.

> **⚠️ ERGEBNIS: Wie bei Follow-up — mehrere überlappende Systeme (7 Stück), KEIN einheitliches „Rollen-/Rang-Fundament".** Ein **Rang-Mechanismus existiert und ist mit Personen verbunden** (`position_qualifications.sort_order` + „can-perform-below"-Logik, 50 Mitarbeiter verknüpft) — aber **costing-/gewerkorientiert**, nicht Yamas Kommando-Hierarchie, und die **Autoritäts-Entscheidungslogik ist dormant** (nicht verdrahtet). Yamas 9 Stufen sind über 3–4 Systeme **verstreut**. Fundament zum **Erweitern** vorhanden, aber kein fertiges. Details + Urteil unten. **Kein Paket** (kein spatie/laravel-permission).

---

## Die überlappenden Systeme (7, live-geprüft)

| # | System | Tabelle(n) | Zeilen (live) | Was es ist | Rang/Hierarchie? |
|---|---|---|---|---|---|
| 1 | **Positions-Katalog** | `positions` | **24** | flache Job-Titel-Liste (Meister/in, Geselle/in, SHK/PV/Dach-Monteur/in, Helfer/in, Azubi, Geschäftsführer/in …) | **nein** (nur `position`, `description`, `status`) |
| 2 | **Qualifikations-Rang** | `position_qualifications` | **26** | **rang-tragend** (`sort_order`) + `default_price` (Stundensatz) | **JA — `sort_order`** |
| 2b | Capability-Matrix | `position_qualification_hierarchies` | **0 (dormant)** | performer↔required + allowed/efficiency/cost (aus #2 ableitbar) | JA, aber leer |
| 3 | **Zuordnung Employee↔Position** | `department_positions` | **50** | employee_id + department_id + position_id + montage/office-Prozent + `main` | nein |
| 3b | Employee↔Department | `employee_departments` | **50** | reine Zuordnung | nein |
| 4 | **Rechte-System** | `user_rolls` | **83** | pro User/Item CRUD-Flags (`is_read/is_update/is_delete/is_add`) | nein (Feature-Zugriff) |
| 5 | **Reports-to-Baum** | `employees.supervisor` (Self-FK) | **49/51 belegt** | jeder MA hat einen Vorgesetzten (anderer MA) | **JA (Baum, personen-basiert)** |
| 6 | **Suggest-Rollen** | `customer_suggest_employees.role` | *(persistiert)* | enum `monteur/obermonteur/bauleiter/techniker/…` je Vorschlag | nein (flaches Tag) |
| 7 | **Ad-hoc-Rollen** | — (im Code) | — | Projektleiter (aus `project.employee_id`), Abteilungsleiter (Department-Head), String-Match auf Positions-Text | implizit |

**Kein Rollen-Paket:** `composer.json` enthält nur `spatie/browsershot` + `spatie/laravel-ignition` (unrelated). Keine `roles`/`role_user`/`permissions`-Tabellen (existieren nicht).

---

## Antworten auf die Kartierungs-Fragen

### 1. Rollen-/Positions-Tabellen (wörtlich)
- **`positions`** (`create_positions_table`): `position` (string), `description`, `status`. → 24 flache Titel, **kein Rang**.
- **`position_qualifications`** (`2026_02_12`): `name` (unique), `default_price` (decimal), **`sort_order`** (unsignedInteger), `status`, softDeletes. → **26 Zeilen, rang-tragend.**
- **`position_qualification_hierarchies`** (`2026_04_27`): `performer_qualification_id`, `required_qualification_id`, `allowed` (bool), `efficiency_factor`, `cost_factor`, `notes`, unique + FKs. → **0 Zeilen (dormant)**.
- **`department_positions`** (`2024_07_04`): `employee_id`, `department_id`, `position_id`, `percent`, `montage_percent`, `office_percent`, `working_hours`, `main`. → **50 Zeilen** (der echte Employee↔Position-Link).
- Peripher: `departments` (16), `employee_departments` (50), `activity_positions` (0), `activity_departments`, `qualifications` (**0, Legacy-leer**), `costing_set_roles`.
- **Kein Paket** (kein spatie/permission).

### 2. Was ist der Mitarbeiter?
**Zwei Entitäten, getrennt:** `users` (51 — Login: `id, name, email, is_admin, is_active, password`; **nur `is_admin` als Rollen-Flag, keine role-Spalte**) und `employees` (51 — die Person). **Der Task/Karten/planner_items-Zuordnung liegt die `employee`-id zugrunde** (aus dem Rückfluss: `performer_employee_id`, `planner_item_employees.employee_id`). User↔Employee-Brücke: **`users.name` = Employee-id** (numerisch; `authEmployeeId()` = `(int) auth()->user()->name`).
**Klassifikations-Felder auf `employees`:** `qualification_id` (→ #2), `supervisor` (→ #5), `trainee`/`trainee_start_date`/`trainee_end_date` (Azubi-Flag), `skill_id`, `branch`, `working_type`. **Kein `position_id`** direkt (Position via `department_positions`).

### 3. Bestehende Hierarchie-Signale
- **`position_qualifications.sort_order` = Rang** (1 = höchste). Live-Verteilung (26, Auszug): `1 Geschäftsführung`, `2 Management/Elektromeister`, `3 Meister/Elektrofachkraft`, `4 Anlagenmechaniker SHK/Geselle`, `5 PV-Monteur/Helfer`, `6 Dachmonteur/Techniker`, … `15 Ausbildung (14€)`. → **Rang existiert, ist aber costing-getönt (Stundensätze) und hat Gleichstände** (mehrere Quals je Stufe).
- **„can-perform-below"-Logik CODIERT:** `Employee/Position/PositionController` (Docblock: „1 Meister … 6 Azubi 1 · Meister can perform everyone below") baut die Matrix via **`$allowed = (int)$performer->sort_order <= (int)$required->sort_order`** und hat `hierarchyCheck(Request)` (validiert performer/required-qualification). → **Exakt Yamas „höhere Rolle darf mehr" — aber s. §4-Urteil: dormant.**
- **`employees.supervisor` (Self-FK → employees):** **49/51 belegt** — ein echter, gepflegter Reports-to-Baum (personen-basiert, nicht rollen-basiert).
- **`employees.trainee` + trainee_start/end:** Azubi-Kennzeichnung vorhanden (aber kein 1./2./3.-Jahr-Feld).
- **Deutsche Begriffe im Code (wörtlich):** `PositionQualification.php:31` „Meister can perform Geselle, Helfer, Azubi"; `CustomerSuggestEmployeeController:21` enum `team,leader,representative,monteur,obermonteur,helper,innendienst,aussendienst,bauleiter,buchhaltung,techniker,controller`; `PlannerPlanController:6719` `$roleMap[project->employee_id][] = 'Projektleiter'`; `OverdueCenterController:162/239` „Abteilungsleiter"; `EmployeeController:1886` `str_contains($position, 'abteilungsleiter')` (String-Match!).

### 4. Berechtigungs-Logik (wer darf was)
- **Dominant: `is_admin` (61 Nutzungen)** — Super-Admin-Bypass in Middleware (`isAdmin.php`, `is_Admin.php` — **zwei fast gleiche**, Wildwuchs; `InvoiceMiddleware`).
- **`user_rolls` (83 Zeilen)** = das eigentliche Rechte-System: `auth()->user()->is_admin || DB::table('user_rolls')…` (`isAdmin.php:23`). Pro User/Item CRUD-Flags. **`CheckUserPermission`-Middleware**: `handle(Request, Closure, string $item, string $action='read')` → sonst `abort(403)`. → **Feature-/Menü-Zugriff, NICHT Aufgaben-Autorität.**
- **Laravel Gates/`can()`: nur 2** Treffer. **Policies: 4** (AiChat, ChatGroup, GeneralTask, PersonalTask) — feature-spezifisch, keine Rang-Policy.
- **→ Berechtigung heute = `is_admin`-Flag + `user_rolls`-CRUD-Grid je Feature.** Es gibt **keinen** Mechanismus, der anhand eines **Rangs** entscheidet, ob eine Aufgabe eine PL-Prüfung braucht — genau das fehlt.

### 5. Konfigurations-Stellen
- **Keine** `config/`-Datei für Rollen/Rechte/Positionen (nur Standard `config/auth.php`).
- Konfiguriert wird über **Daten + Admin-UI**: `positions`, `position_qualifications` (Preise/sort_order), `department_positions` (Zuordnung + Prozente), `user_rolls` (Rechte-Grid). PositionController hat UI-Endpunkte (Positions-CRUD). → Yamas Vermutung „es gibt Konfigurationen" = **ja, aber verstreut über diese Tabellen/UIs**, nicht als ein Rang-/Rechte-Ort.

### 6. Überlappung / Wildwuchs (einzeln benannt)
Sieben nebeneinander (s. Tabelle): (1) `positions` flach · (2) `position_qualifications` rang+preis · (3) `department_positions` Zuordnung · (4) `user_rolls`+`is_admin` Rechte · (5) `supervisor` Reports-Baum · (6) `customer_suggest_employees.role` enum · (7) Ad-hoc-Strings (Projektleiter/Abteilungsleiter/String-Match). **Zusätzlich Doppel-Middleware `isAdmin.php`/`is_Admin.php` und Doppel-Junction `department_positions`/`department_positions_junctions`.** Die Rollen-Vokabulare **widersprechen sich teils** (Trade: Meister/Geselle/Helfer/Azubi vs. Org: Monteur/Obermonteur/Bauleiter/PL vs. Costing-Quals).

### 7. URTEIL
**Ein tragfähiges Teil-Fundament existiert — aber kein fertiges 9-Stufen-Kommando-System.**

**Nächster an Yamas Wunsch: `position_qualifications` (#2)** — weil es **(a)** einen **Rang** trägt (`sort_order`), **(b)** die **„höhere Stufe darf mehr"-Logik bereits codiert** hat (PositionController), **(c)** **mit realen Personen verbunden** ist: `employees.qualification_id` → `position_qualifications.id` **matcht 15/15, 50 Mitarbeiter** (de-facto Link, **ohne FK** — historisch zeigte das Feld auf das leere `qualifications`).

**Aber die Lücken zu Yamas 9 Stufen sind erheblich:**
- **Vokabular passt nur teils:** vorhanden Helfer, „Ausbildung"(=Azubi, ohne 1/2/3-Split), PV-/Dach-Monteur, Meister/Geselle. **Fehlen:** generischer Monteur, **Obermonteur, Bauleiter, Projektleiter, Abteilungsleiter** (die liegen ad-hoc in #7).
- **`sort_order` ist costing-getönt** (Stundensätze) mit **Gleichständen** (mehrere Quals je Stufe) → **keine strikte 1-pro-Stufe-Kommando-Ordnung**.
- **Die Autoritäts-Entscheidung ist DORMANT:** `position_qualification_hierarchies` = **0 Zeilen**, und `hierarchyCheck`/die Hierarchie-Methoden sind **nicht geroutet** (Grep in `routes/` findet nur den unverwandten `ProductPositionController`). → Die „can-do-below"-Logik **entscheidet heute nichts**.
- **`supervisor`-Baum** (49/51) ist eine **zweite, personen-basierte** Hierarchie — überlappt konzeptionell, ist aber nicht rollen-/rang-basiert.
- **`user_rolls`** regelt Feature-Zugriff, **nicht** Aufgaben-Autorität.

**Fazit:** **Erweitern, nicht neu bauen** — die Rang-Achse (`position_qualifications.sort_order` + PositionController-Logik + der live Employee-Link) ist die **einzige sinnvolle Basis**. Zu schaffen wäre später: (i) eine **strikte Rang-Dimension** für Yamas Kommando-Rollen (die fehlenden Obermonteur/Bauleiter/PL/Abteilungsleiter ergänzen bzw. eine **Kommando-Ordnung getrennt vom Costing-sort_order**), (ii) die **Autoritäts-Regel** „ab Rang X keine PL-Prüfung" (heute nirgends), (iii) eine **Entscheidung, wie `supervisor`-Baum + `positions` + `user_rolls`** dazu stehen (mit-konsolidieren oder getrennt). **Kein Vorgriff — das ist Yamas spätere Design-Entscheidung.**

---

## Gelesen / NICHT gelesen (ehrlich)
**Geprüft (wörtlich/live):** `composer.json` (kein Permission-Paket); Migrations-Strukturen `positions`, `position_qualifications`, `position_qualification_hierarchies`, `department_positions`, `employee_departments`, `user_rolls`; Live-Counts (positions 24, position_qualifications 26, hierarchies 0, qualifications 0, department_positions 50, employee_departments 50, user_rolls 83); vollständige `position_qualifications`-Rangliste (26, sort_order+Preis); `employees`/`users`-Spalten; **Daten-Match** `employees.qualification_id` ↔ `position_qualifications.id` (15/15, 50 MA) + `supervisor` (49/51); PositionController can-do-below-Logik (`sort_order <=`) + `hierarchyCheck`; Berechtigungs-Mechanik (`is_admin` 61×, `user_rolls`, `CheckUserPermission`, 4 Policies, 2 Gates, Doppel-Middleware); deutsche Rollen-Begriffe (5 Fundstellen wörtlich); `customer_suggest_employees.role` (persistiert).

**NUR gegrept / NICHT VERIFIZIERT:**
- Ob `employees.qualification_id` **semantisch** als „position_qualification" gemeint ist (Daten matchen 15/15, aber **kein FK**, historisch auf leeres `qualifications` zeigend) — de-facto ja, formal ungesichert.
- Ob `PositionController::hierarchyCheck`/die Matrix-Erzeugung **irgendwo** doch aufgerufen wird (kein Route-Treffer gefunden; `position_qualification_hierarchies`=0 stützt „dormant" — aber nicht 100 % ausgeschlossen, z. B. interner Aufruf).
- Genaue Nutzung von `department_positions.main`/Prozente (nur Struktur + Count gelesen).
- Ob `customer_suggest_employees.role` über Vorschläge hinaus etwas **entscheidet** (nur Persistenz belegt).
- `user_rolls.item_id`-Wertebereich (welche Features) — nicht ausgezählt.

## Selbstkritik / Risiken
- **„Nächster an Yamas Wunsch = position_qualifications" ist ein Urteil, kein Beweis** — es beruht auf „hat als Einziges Rang + Personen-Link + can-do-below-Logik". Man könnte ebenso argumentieren, Yamas **Kommando**-Hierarchie sei näher am **`supervisor`-Baum** (echte Über-/Unterstellung) — der ist aber personen-, nicht rollen-basiert. Beide sind Kandidaten; ich habe den rang-/rollen-basierten bevorzugt, weil Yamas Regel an **Rollen** hängt („höhere Rolle").
- **Die 7-System-Landschaft ist real, aber ich habe nicht jede UI durchgespielt** — welche davon der Alltag tatsächlich nutzt (außer den Live-Counts) ist teils Vermutung. `user_rolls` (83) + `department_positions` (50) + `position_qualifications` (26, frisch 2026-06-28/29 befüllt) + `supervisor` (49) sind aber alle **belegt befüllt**, also aktiv.
- **„Dormant" für die Autoritäts-Logik** stützt sich auf 0 Hierarchie-Zeilen + fehlenden Route-Treffer — stark, aber ein versteckter interner Aufruf ist nicht 100 % ausgeschlossen (als NICHT VERIFIZIERT markiert).
- Yamas 9 Stufen als **strikte lineare Kette** widersprechen der Realität mehrerer **paralleler** Achsen (Costing-Rang, Reports-Baum, Feature-Rechte). Die spätere Umsetzung muss klären, welche Achse „Autorität" trägt — das ist bewusst **nicht** Teil dieser Analyse.

---

*Reine Analyse — nichts geändert. Querverweise: `architektur-entscheidungen.md` (Weiche 6 Rückfluss/PL-Prüfung), `rueckfluss-stufe1-bauplan.md` (1c Projektleiter-Bestätigung), `follow-up-bestandsaufnahme.md` (gleiches „mehrere Wahrheiten"-Muster), `glossar.md`.*
