# CRM-Inventur 05 — Organisation & Personal (HR)

**Zone:** Organisation & Personal eines Laravel-11 Solar/PV-CRM
**Methode:** Breite vor Tiefe. Nur Lesen/Analyse. Stand: 2026-07-01.
**Umfang:** Abteilungen/Filialen/Teams · Mitarbeiter/HR · Urlaub/Anwesenheit/Arbeitszeit · Rollen/Rechte.
**NICHT in dieser Zone:** Projektmanagement/Planer/Aufgaben/Assets → Zone 06.

**Glossar:** Mitarbeiter = `employees` · Kunde = `new_leads` · Filiale = `branches`.

**Zentrale Strukturbefunde (verifiziert):**
- Die echte HR-Logik liegt in Unterordnern von `app/Http/Controllers/Employee/` (Department/, Position/, Profile/, TimeManagement/, Calendar/, Note/) sowie in `Branch/` und den Top-Level `Branch*`-Controllern. Der Monolith `Employee/EmployeeController.php` hat **3.523 Zeilen** und **52 Routen**.
- **Tote/geplante Artisan-Scaffolds** (nur `//`-Rümpfe, KEINE Routen): `EmployeeDepartmentController`, `AttendanceController`, `EmployeeProjectCoinController`, `EmployeeMonthlyTimeBudgetController` (je 65 Zeilen, verifiziert). Kein Routen-Verweis in `routes/`.
- `employee_monthly_time_budgets`: Model `EmployeeMonthlyTimeBudget` existiert, aber **keine Migration** → tot/geplant.
- **Rollen/Rechte-Muster:** per-User-Berechtigung über `user_rolls` (user_id + item_id + is_read/is_add/is_update/is_delete), `User::hasPermission($item,$action)` mit `is_admin`-Bypass. **Kein** benanntes Rollensystem, **kein** Rollenname über `users.name`. Merke: `users.name` speichert stattdessen die `employees.id` (siehe `User::employeeId()`).
- **Datenlage (row counts, verifiziert):** employees 51, users 51, branches **1**, departments 16, positions 24, department_positions 50, employee_departments 50, teams 16 (aber team_members **0**), user_rolls 83, user_roll_items 14. Leave/Sick/Attendance/Recurring/Schedule-Tabellen **alle 0** → Features gebaut, aber (noch) kaum/nicht in Nutzung.

---

## 1. Mitarbeiter / Personal (Stammdaten)

**(a) Zweck:** Zentrale Personalakte — Anlage, Profil, Stammdaten der 51 Mitarbeiter. Kern der gesamten Zone.

**(b) Controller/Routen:**
- `Employee/EmployeeController.php` (3.523 Z., 52 Routen) — Hauptcontroller Anlage/Liste/Profil.
- `Employee/EmployeeCapacityStateController.php` (~29 KB) + Top-Level `EmployeeCapacityStateController.php`.
- Profil-Unterordner `Employee/Profile/` (26 Controller): `EmployeeAddressController`, `EmployeeDocumentController`, `EmployeeLicenseController`, `EmployeeClothController`, `LanguagesController`, `SkillController`/`OtherSkillController`, `FurtherEducationController`, `EmergencyContactController`, `ContractTypeController`, `CountryController`, `TaxController`, `HolidayController`/`PublicHolidayController`, `EmployeePostcodeListController` u. a.
- Sidebar: „Mitarbeiter“-Bereich (sidebar.blade.php).

**(c) Kern-Tabellen:** `employees` (sehr breit: title/name/lastname/branch/dob/salary_per_hour/supervisor/working_hour/qualification_id/contract_type_id …), `employee_addresses`, `employee_languages` (+ `languages`), `employee_licenses` (+ `license_types`, `employee_license_types`), `employee_cloths`, `employee_documents`, `employee_postcode_lists`, `contract_types`. Verknüpft mit `users` (1:1 über users.name = employees.id).

**(d) Größe:** SEHR GROSS — Hauptcontroller 3.5k Z., 26 Profil-Subcontroller, ~10+ Stammdaten-Tabellen. 51 Datensätze aktiv.

**(e) Status:** AKTIV/produktiv (Kernstammdaten). Profil-Detailtabellen (Adressen/Sprachen/Lizenzen/Kleidung/Dokumente) aktuell 0 Zeilen → Unterfeatures kaum befüllt.

---

## 2. Abteilungen & Positionen

**(a) Zweck:** Organisationsstruktur — Abteilungen, Positionen, Zuordnung Mitarbeiter↔Abteilung/Position, Org-Chart, Qualifikationen/Hierarchien.

**(b) Controller/Routen:**
- `Employee/Department/DepartmentController.php` (1.590 Z.), `DepartmentPositionController`, `DepartmentChartController` (Org-Chart).
- `Employee/Position/`: `PositionController` (Positionen, Qualifikations-Board, Hierarchie-Board/auto-generate/check — Routen 2254–2283), `PositionDescriptionController`, `QualificationController` (131 Z., emp_qualification), `ProductPositionController` (Produkt↔Position), `EmployeeOrganizationController` (`/employee-organization` Assign/Bulk-Assign, Routen 1497–1503).
- Sidebar: `department.info`, `position.index`, `department.organize`, `employee.organization.index`.
- **TOT:** Top-Level `EmployeeDepartmentController` (Scaffold, keine Routen) — echte Zuordnung läuft über `EmployeeOrganizationController` + `employee_departments`.

**(c) Kern-Tabellen:** `departments` (16), `positions` (24), `department_positions` (50), `department_positions_junctions`, `employee_departments` (50), `position_qualifications`, `position_qualification_hierarchies`, `product_positions`, `activity_departments`/`activity_positions`, `brand_departments`, `external_departments`, `distributor_departments`.

**(d) Größe:** GROSS — DepartmentController 1.6k Z., Position-Unterordner (5 Controller) mit reichem Qualifikations-/Hierarchie-Feature-Set.

**(e) Status:** AKTIV (16 Abt., 24 Pos., 50 Zuordnungen befüllt). Qualifikations-Hierarchie ist junges, ausgebautes Feature (Migrationen 2026-02/04).

---

## 3. Filialen (Branches) & Filialkosten

**(a) Zweck:** Filialverwaltung + Filialkosten-Controlling (Miete, Versicherungen, sonstige Kosten, Verträge). Randläufig Controlling-nah, gehört org-seitig hierher.

**(b) Controller/Routen:**
- `Branch/BranchController.php` (~12 KB), `Branch/BranchAddressController.php` — Anlage/Profil/Adressen (Routen 687–708).
- Filialkosten (Top-Level): `BranchExpenseController.php` (~21 KB, Analytics/Profile, Routen 727–748), `BranchExpenseRentController`, `BranchExpenseInsuranceController`, `BranchExpenseOtherCostController` (Routen 752–770), sowie ältere `BranchContractDetailsController`, `BranchRentInfoController`.
- Sidebar: „Filialen“ (branch.info), „branch.expense“.

**(c) Kern-Tabellen:** `branches` (+ Company-Profile-Felder, Migration 2026-04), `branch_addresses`, `branch_expenses`, `branch_rents`, `branch_insurances`, `branch_rent_infos`, `branch_contract_details`, `branch_expense_other_costs`, `rent_properties`, `rent_extra_costs`. Mehrere „upgrade_*“-Migrationen 2026-06.

**(d) Größe:** MITTEL–GROSS (Controller-seitig ~8 Controller). Datenseitig klein: **nur 1 Filiale**, Kostentabellen 0 Zeilen.

**(e) Status:** Filialstamm AKTIV (minimal, 1 Datensatz). Filialkosten-Modul kürzlich stark ausgebaut (2026-06 Upgrades), aber **noch ungenutzt** (0 Zeilen) → Feature-fertig, Daten-leer.

---

## 4. Teams

**(a) Zweck:** Team-Bildung — Mitarbeiter zu Teams gruppieren, Mitglieder syncen, Reserve-Mitglieder befördern.

**(b) Controller/Routen:** `Employee/Profile/TeamController.php` (195 Z.). `Route::resource('teams', …)` + `teams/{team}/members/sync` + `teams/{team}/promote` (Routen 1584–1586). Sidebar: `teams.index` (Zeile 949).

**(c) Kern-Tabellen:** `teams` (16), `team_members` (0), `employee_sets` (verwandt).

**(d) Größe:** KLEIN (1 Controller, ~3 Routen).

**(e) Status:** TEILAKTIV — 16 Teams angelegt, aber **0 team_members** → Teams existieren als Hüllen, Zuordnung nicht befüllt.

---

## 5. Urlaub & Abwesenheit (Leave / Sick / Recurring)

**(a) Zweck:** Urlaubsanträge/-genehmigung, Vertretung, Krankmeldungen, Kurzabwesenheiten, wiederkehrende Abwesenheiten (mit Overrides/Exdates — RRULE-artig).

**(b) Controller/Routen:**
- `Employee/Profile/LeaveController.php` (1.252 Z.) — store/update/approve/representer/accept/change, Notes, Department-Leader-Lookup (Routen 1799–1827).
- `Employee/Profile/LeaveDayController.php` — Feiertage/Urlaubstage-Stamm (Routen 1710–1715).
- `Employee/Profile/EmployeeSickController.php` — Krankmeldungen inkl. Dokumente (Routen 1834–1841).
- `Employee/Profile/EmployeeRecurringLeaveController.php` — wiederkehrende Abwesenheiten + occurrences/exdate/override (Routen 1264–1288).
- Sidebar: `leave.day.info`, `employee.sickness-holiday-analyser`.

**(c) Kern-Tabellen:** `leaves` (0), `leave_days` (1), `employee_sicks` (0), `employee_short_leaves` (0), `employee_recurring_leaves` (0) + `_overrides` + `_exdates`.

**(d) Größe:** GROSS (LeaveController 1.25k Z., 4 Controller, RRULE-Mechanik). Datenseitig **fast leer**.

**(e) Status:** Feature ausgebaut (Recurring-Mechanik ist junges Feature 2025-08…2026-06), aber **produktiv ungenutzt** (alle Tabellen 0–1 Zeilen).

---

## 6. Anwesenheit / Zeiterfassung (Attendance)

**(a) Zweck:** An-/Abwesenheitserfassung, Check-in/out, Reise-/Arbeits-/Pausenzeiten, GPS-Tracking, Mobile Zeiterfassung + Analytics.

**(b) Controller/Routen:**
- **Web/Planer:** `Planner/PlannerAttendanceController.php` (652 Z.) — check-in/out, travel-start, location, arrived, work-start/-end, pause-start/-end (Routen 5390–5401, unter `plans/{plan}/attendance/*`).
- **Mobile/API:** `Api/MobileAttendanceController.php` (772 Z.) — action/location/sync/history/log (`routes/api.php` 153–166).
- **Analytics:** `Admin/AttendanceAnalyticsController.php` (197 Z., Routen 1911–1914).
- **TOT:** Top-Level `AttendanceController` (Scaffold, 65 Z., keine Routen) → die echte Attendance-Logik liegt in Planner- + Api-Controllern.

**(c) Kern-Tabellen:** `attendances` (0; + Status/Geo-Spalten, Passcode auf employees, Realtime-Prep 2026-06). (`attendance_events`/`attendance_locations` als eigenständige Migrationen nicht gefunden — Ereignisse/Standorte vermutlich in attendances-Spalten oder Planner-Zone; **verifizieren in Detail-Inventur**.)

**(d) Größe:** GROSS (Planner 652 + Mobile 772 + Analytics 197 Z.).

**(e) Status:** Feature aktiv gebaut & jung (Migrationen 2026-01…2026-06, Realtime-Prep), aber **0 Datensätze** → produktiv (noch) ungenutzt. Kopplung an Planner (Zone 06) beachten — Attendance ist plan-zentriert (`plans/{plan}/attendance`).

---

## 7. Arbeitszeit-Planung / Zeitkonten (TimeManagement)

**(a) Zweck:** Monats-Arbeitszeitplanung (Plan laden/speichern/einreichen/Status), Zeit-Slots.

**(b) Controller/Routen:** `Employee/TimeManagement/TimeManagementController.php` (344 Z.) — index/loadMonth/save/submit/updateStatus/slotsIndex (Routen 1601–1614). Verwandt: `Employee/Calendar/PersonalSettingsController`.

**(c) Kern-Tabellen:** `employee_time_schedules` (0). **TOT:** `employee_monthly_time_budgets` (Model ohne Migration), `employee_project_coins` (Migration existiert, aber Controller `EmployeeProjectCoinController` ist leeres Scaffold ohne Routen).

**(d) Größe:** KLEIN–MITTEL (344 Z., 1 aktiver Controller).

**(e) Status:** TEILAKTIV/jung (Migration 2025-11), 0 Zeilen. „Coins“/„Monthly Time Budget“ → geplant/tot.

---

## 8. Gehalt / Lohn (Salary)

**(a) Zweck:** Gehaltsverwaltung, Lohnabrechnungs-Sheets, Steuer-Defaults.

**(b) Controller/Routen:** `Employee/Profile/SalaryController.php` + `SalarySheetController.php` — salary_management/refresh/upsert, salary_sheet, tax-defaults (Routen 1685–1692). Sidebar: `salary.index`.

**(c) Kern-Tabellen:** Gehaltsfelder auf `employees` (salary_per_hour, working_hour), Salary-Sheets (Upload-basiert), `TaxController`-Bezug.

**(d) Größe:** KLEIN–MITTEL.

**(e) Status:** Vorhanden; Nutzungstiefe in Detail-Inventur zu klären (Steuerberater-Gate laut Architektur-Memo).

---

## 9. Benutzer / Rollen / Rechte

**(a) Zweck:** Benutzerkonten-Verwaltung + per-Benutzer-Berechtigungen. Zugriffssteuerung der gesamten App.

**(b) Controller/Routen:**
- `User/UserController.php` — Anlage/Edit/Passwort, admin_user, make_admin/make_limit, active/deactive, logoff, adminUsers* (Routen 2194–2240).
- `User/UserRollController.php` (369 Z.) — index/ajax/store/update/destroy unter `user-rolls/*`, abgesichert per `middleware('permission:Users,<action>')` (Routen 2243–2248).
- `User/UserPreferenceController.php` — UI-Präferenzen (Offer-Designer-Spalten).
- Middleware: `CheckUserPermission` (`permission:<item>,<action>` → `abort(403)`), `isAdmin`/`is_Admin`.
- Sidebar: `user-rolls.index` (Zeile 1293).

**(c) Kern-Tabellen:**
- `user_rolls` (83) — item_id + is_read/is_add/is_update/is_delete (waren `string 'on'/'off'`, per Migration 2026-04 auf TINYINT(1) migriert).
- `user_roll_items` (14) — Katalog der schützbaren „Items“ (frei, kein Enum).
- `users` (51, is_admin/is_active; users.name = employees.id).

**(d) Größe:** MITTEL (UserController + UserRollController 369 Z. + Middleware).

**(e) Status:** AKTIV. Muster: `is_admin`-Bypass + item-basierte CRUD-Flags pro User. **Kein** benanntes Rollenkonzept, keine Rollen-Gruppen. Berechtigungs-Enforcement uneinheitlich: nur ~20 `hasPermission`-Aufrufe in `app/` + wenige `permission:`-Middleware-Routen → Rechteprüfung ist **stellenweise, nicht flächendeckend** (Detail-Audit nötig).

---

## Braucht eigene Detail-Inventur

1. **Mitarbeiter/EmployeeController (3.523 Z., 52 Routen)** — Monolith; Aufschlüsselung aller Aktionen, Anlage-Flow, Kopplung users↔employees (users.name = employees.id), Kapazitäts-State.
2. **Rollen/Rechte-Enforcement** — Coverage-Audit: welche der ~37 Controller sind durch `permission:`/`hasPermission` geschützt vs. offen? `user_roll_items`-Katalog (14 Items) vs. tatsächliche Menüpunkte. `is_admin`-Bypass-Risiko.
3. **Urlaub/Recurring (LeaveController 1.252 Z. + Recurring/Override/Exdate)** — RRULE-artige Mechanik, Genehmigungs-/Vertretungslogik, Verhältnis zu Kalender/Planer (Zone 06).
4. **Attendance (Planner 652 + Mobile 772 + Analytics 197 Z.)** — Datenmodell klären: `attendances`-Spalten vs. angenommene `attendance_events`/`attendance_locations` (nicht als Migration gefunden). Realtime-Prep, GPS. Zone-Grenze zu Planner.
5. **Filialkosten** — kürzlich stark umgebaut (upgrade_* 2026-06), aber 0 Daten; Verhältnis zu Controlling-Zone klären (Überschneidung mit Controlling-Inventur).
6. **Positions-Qualifikation & Hierarchie** — junges, komplexes Feature (Board/auto-generate/check); Datenmodell `position_qualification_hierarchies`.
7. **Salary/Tax** — Nutzungstiefe + Steuerberater-Gate (siehe Architektur-Memo).

---

## Belege

**Tote/leere Scaffolds (verifiziert):**
- `app/Http/Controllers/EmployeeDepartmentController.php` — 65 Z., nur `//`-Rümpfe, kein Routen-Verweis.
- `app/Http/Controllers/AttendanceController.php` — 65 Z., nur `//`-Rümpfe, kein Routen-Verweis (echte Logik: `Planner/PlannerAttendanceController`, `Api/MobileAttendanceController`).
- `app/Http/Controllers/EmployeeProjectCoinController.php` / `EmployeeMonthlyTimeBudgetController.php` — je 65 Z., `//`-Rümpfe, keine Routen.
- `employee_monthly_time_budgets`: Model `app/Models/EmployeeMonthlyTimeBudget.php` vorhanden, **keine** Migration in `database/migrations/` → tot.

**Rollen/Rechte-Muster (verifiziert):**
- `app/Models/User.php` Z.56–74: `hasPermission($item,$action)` mit `is_admin`-Bypass (Z.58) über `user_rolls` (item_id + is_read/is_add/is_update/is_delete). Z.81–84: `users.name` = `employees.id`.
- `app/Http/Middleware/CheckUserPermission.php`: `permission:<item>,<action>` → `abort(403)`.
- Migration `2026_04_25_161053_fix_user_rolls_columns.php`: 'on'/'off' → TINYINT(1).
- `grep hasPermission app/` = 20 Treffer (Enforcement stellenweise).

**Row counts (php artisan tinker, verifiziert 2026-07-01):** employees 51, users 51, branches 1, departments 16, positions 24, department_positions 50, employee_departments 50, teams 16, team_members 0, leaves 0, leave_days 1, employee_recurring_leaves 0, employee_sicks 0, employee_short_leaves 0, attendances 0, employee_time_schedules 0, user_rolls 83, user_roll_items 14; alle employee_*-Profil-Tabellen (addresses/languages/licenses/cloths/documents/postcode_lists) und branch_*-Kosten (expenses/rents/insurances) = 0.

**Routen (routes/web.php / routes/api.php):** Employee 52 (web), Branch 687–770 + 2768–2770, Department/Position 1468–1503 + 2254–2283, Teams 1584–1586, Leave 1710–1827, Recurring 1264–1288, Sick 1834–1841, TimeManagement 1601–1614, Salary 1685–1692, User 2194–2240, user-rolls 2243–2248, Attendance web 5390–5401 / api 153–166.

**Sidebar (resources/views/admin/layouts/sidebar.blade.php):** teams.index (949), employee.sickness-holiday-analyser (969), salary.index (978), leave.day.info (1022), department.info (1044), position.index (1050), department.organize (1056), employee.organization.index (1062), branch.expense (1253), user-rolls.index (1293), „Filialen“/branch.info (1312).

**Excludes:** vendor, node_modules, storage, .git nicht analysiert.
