# Stopp-1 — Eintrittsgate zu Phase 1 (EIN Prüfgegenstand)

> **Rolle:** Planer/Überwacher (diese Instanz plant & prüft, die Executor-Instanz führt Code aus).
> **Zweck:** Alle Phase-0-Ergebnisse in **einem** Dokument bündeln, damit Stopp 1 als **ein**
> Prüfgegenstand freigegeben werden kann. Phase 1 startet **nicht**, bevor dieses Gate grün ist.
> **Stand:** 2026-07-04 · Branch `private/app-code-backup` · alle Ist-Aussagen **belegt aus dem Code**,
> nicht vermutet. Offene Stellen sind als 🔴/🟡 markiert.

**Gate-Status gesamt: 🔴 OFFEN** — Grund: MySQL-Grün-Nachweis ausstehend (Suite noch nicht im Baum) +
Test-Isolation in `phpunit.xml` noch nicht verdrahtet. Siehe Teil B/C.

---

## Teil A — Die drei Verifikationen (belegt)

### ① Skeleton-Delta — 🔴 Diskrepanz (kritisch)

Belegter Ist-Stand der Test-Suite (aus dem Baum, nicht geschätzt):

| Ort | `*Test.php`-Dateien | Testmethoden |
|---|---|---|
| `private/app-code-backup` (dieser Branch) | 7 | ~40 (38 echt + 2 Laravel-Stubs) |
| `main` | 4 | — |
| `checkpoint/wip-2026-06-26` | 4 | — |
| **Stash** | leer | — |
| **Worktrees** | nur dieser | — |

Die echten 40 Methoden verteilen sich auf:
`FusionWebhookTest` 2 · `PlannerMobileApiTest` 4 · `VideoCall/VideoCallTest` 11 ·
`Invoice/InvoiceNumberServiceTest` 12 · `Invoice/InvoiceDeletionGuardTest` 9 · 2× `ExampleTest` (Stub).

**Befund:** Der Stopp-1-Sollwert **262 Tests** existiert **an keiner git-erreichbaren Stelle**
(kein Branch, kein Stash, kein Worktree). Der Delta ist damit **nicht** „im Baum noch zu schreiben",
sondern: **die Suite ist hier nicht vorhanden.** Sie liegt vermutlich im **uncommitteten Arbeitsbaum
der Executor-Instanz** und wurde nie auf den Branch gepusht.

**Konsequenz (entschieden):** Executor pusht die 262er-Suite zuerst → dann Grün-Verifikation (Teil B/C).

### ② Command-Attribut-Syntax — ✅ grün

Alle **10** Commands in `app/Console/Commands/` deklarieren via `protected $signature`;
**0** nutzen das PHP-8-Attribut `#[AsCommand]`. **Keine Mischsyntax**, kein Attribut-/Property-Konflikt,
der einen Scheduler-/Kernel-Boot in Tests brechen könnte.

Betroffene Commands (alle konsistent):
`BackfillLeadStageId`, `BackfillPhaseSections`, `DeactivateExpiredBreakingNews`,
`DispatchMainAppointmentReminders`, `ProcessPersonalTaskScheduler`, `PurgeSoftDeletedGarbage`,
`SyncLeadEmails`, `SyncSolarNewsToChat`, `UpdateJobRepresentativeStatus`, `UpdateLeaveStatus`.

**Verifikation bestanden.**

### ③ Test-Isolation — 🔴 nicht verdrahtet (Datenverlust-Gefahr) + Fix vorgeschrieben

Belegter Ist-Stand:

- `phpunit.xml` pinnt **keine** Test-DB — die `sqlite/:memory:`-Zeilen sind **auskommentiert**
  (Zeilen 24–25). Es existiert **keine `.env.testing`**.
- `.env` → `DB_CONNECTION=mysql`, `DB_DATABASE=ticket` (**die reale Dev-DB**).
- Isolations-Traits gemischt: `RefreshDatabase` in 4 Klassen (VideoCall, FusionWebhook,
  InvoiceNumberService, InvoiceDeletionGuard), `DatabaseTransactions` in 1 (PlannerMobileApi).
- Die Invoice-Tests warnen sogar wörtlich im Kommentar: *„RefreshDatabase nur gegen isolierte
  Test-DB ausführen"* — aber **die Konfiguration erzwingt das nicht.**

**Befund:** Ein blindes `php artisan test` würde jetzt `RefreshDatabase` = `migrate:fresh`
**gegen die reale `ticket`-DB** fahren und sie leeren. **Isolation ist nicht bestanden.**

#### Isolations-Vorbeweis (belegt, nicht-zerstörend)

Die Isolation ist **technisch sicher machbar** — sie ist nur in `phpunit.xml` noch nicht verdrahtet:

1. `ticket` und `ticket_testing` sind **getrennte Schemata**: je **410 Tabellen**
   (`information_schema.tables`, gruppiert). Kein geteiltes Schema, kein Überlauf.
2. Laravel-Config ist **nicht gecacht** (`bootstrap/cache/config.php` fehlt) → env-Overrides greifen.
3. Der Override trifft nachweislich die Wegwerf-DB:
   `DB_DATABASE=ticket_testing php artisan tinker --execute="echo DB::connection()->getDatabaseName()"`
   → **`CONNECTED_DB=ticket_testing`**.

**Fix = Teil B (phpunit.xml-Block wörtlich).** Nach dem Fix ist ③ grün.

---

## Teil B — MySQL-Grün-Eintrittsbedingung

**Pflicht-Bestandteil. Ohne diesen Beleg startet Phase 1 nicht.**

### B.1 — Exakte Isolations-Konfig (`phpunit.xml`, wörtlich)

Der `<php>`-Block wird **vollständig** durch diesen ersetzt. Die zwei entscheidenden Zeilen
(`DB_CONNECTION` + `DB_DATABASE` mit `force="true"`) erzwingen die Isolation für **jeden** Lauf,
unabhängig von `.env` — dadurch ist ③ strukturell gelöst (nicht nur lokal):

```xml
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="mysql" force="true"/>
        <env name="DB_DATABASE" value="ticket_testing" force="true"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
```

> `force="true"` garantiert, dass der phpunit-Wert Vorrang vor `.env` (`DB_DATABASE=ticket`) hat —
> selbst falls die Ladereihenfolge sich ändert. **Belt-and-suspenders gegen genau die Datenverlust-Gefahr aus ③.**

### B.2 — Run-Kommando

```bash
php artisan test
```

`RefreshDatabase` fährt automatisch `migrate:fresh` gegen `ticket_testing` (566 Migrationen).
Kein manueller DB-Eingriff nötig. Optionaler Vorlauf-Check zur Sicherheit:

```bash
php artisan test 2>&1 | head -3   # Header muss Connection=mysql / DB=ticket_testing zeigen
```

### B.3 — Akzeptanzkriterien (hart)

Das Gate ist **nur** grün, wenn **alle** erfüllt sind:

1. **262 / 262 Tests grün** (`OK` / „passed"), **0 failed, 0 errored, 0 risky-ohne-Grund**.
2. Lauf **nachweislich gegen `mysql` / `ticket_testing`** (Header + optional ein Bootstrap-Assert
   `DB::connection()->getDatabaseName() === 'ticket_testing'`).
3. **Die reale `ticket`-DB bleibt unangetastet** (Tabellenzahl vorher = nachher: 410).
4. **Jeder Rot-Fall** ist in der SQLite→MySQL-Fixliste (B.4) belegt und behoben — kein
   „übersprungen/skipped", um grün zu erzwingen.

### B.4 — SQLite→MySQL-Fixliste (füllt sich beim Lauf)

Wird beim ersten MySQL-Lauf gefüllt. Pro Rot-Fall eine Zeile:

| # | Test (Datei::Methode) | Fehlermeldung (Kurz) | Ursache-Klasse | Fix |
|---|---|---|---|---|
| — | *(pending: Executor-Push abwarten)* | — | — | — |

**Ursache-Klassen (erwartbare SQLite→MySQL-Fallen):** strict-mode/`ONLY_FULL_GROUP_BY`,
`AUTO_INCREMENT` vs. rowid, JSON-Spalten-Handling, Fremdschlüssel-Reihenfolge bei `migrate:fresh`,
Groß-/Kleinschreibung von Tabellennamen, `TEXT`-Default-Werte, Datums-/Zeitzonen-Vergleiche,
Transaktions-Verhalten von `DatabaseTransactions` unter MySQL.

### B.5 — Schema-Vorbeweis: Status

🟡 **Vorbereitet, aber bewusst NICHT von der Planer-Instanz ausgeführt.** Der `migrate:fresh`-Lauf
gegen `ticket_testing` (566 Migrationen) gehört **in denselben Zug** wie der Suite-Lauf beim Executor —
nicht in einen isolierten Vorab-Lauf der Planer-Instanz. Sicherheit + Machbarkeit sind über den
Vorbeweis (③) bereits belegt.

---

## Teil C — EXECUTOR-HANDOFF (wörtlich)

> **DIE EINE UNVERHANDELBARE REGEL:**
> **Test-Suite UND Isolations-Konfig (`phpunit.xml`-Block aus B.1) gehören in DENSELBEN
> Push / Commit — NIE getrennt.**
>
> Begründung (belegt): Ohne den Isolations-Block zeigt `.env` auf `DB_DATABASE=ticket` (reale
> Dev-DB). `RefreshDatabase` = `migrate:fresh` **löscht dann beim allerersten Testlauf die
> `ticket`-Dev-DB.** Eine Suite ohne verdrahtete Isolation ist ein **Datenverlust-Werkzeug**.
> `phpunit.xml` ist zudem die in `gesamtstand.md` markierte Mehr-Spuren-Kreuzung — **vor dem Edit
> `git pull`, methodengenau/blockgenau arbeiten.**

**Executor-Schritte, in Reihenfolge:**

1. `git pull` (Branch `private/app-code-backup` aktuell ziehen).
2. `phpunit.xml` → `<php>`-Block **1:1** durch B.1 ersetzen.
3. Die 262er-Test-Suite hinzufügen (`tests/Unit`, `tests/Feature`).
4. **Ein** Commit mit **beidem** (Suite + Isolations-Block). Push.
5. Signal an Planer: „Push da."

**Danach (Planer-Instanz, automatisch = Option B):**

6. `php artisan test` gegen `ticket_testing`.
7. Rot-Fälle → Fixliste B.4 füllen + beheben (SQLite→MySQL).
8. `262/262` grün + `ticket`-DB unangetastet (410 Tabellen) → **Grün-Nachweis hier nachtragen.**
9. Stopp-1-Paket schließen → **Phase-1-Freigabe (liegt bei Yama).**

---

## Teil D — Kollisions-Karte (Phase-0-Ergebnis, aus `gesamtstand.md`)

Belegt: 6 Code-Dateien wurden von **mehr als einer Spur** angefasst — **konfliktfrei**
(sequenziell, additiv, 0 Merge-Commits, alle Pushes Fast-Forward → gestapelt, nicht überschrieben).

| Datei | Spuren | Bewertung |
|---|---|---|
| **routes/web.php** | A · C · D | Zentrales Nadelöhr — alle hängen Routen an. Kein Konflikt. |
| **NewLeadsController.php** | A · D | Verschiedene Methoden. |
| **InquiryController.php** | A · D (+P2) | Verschiedene Methoden. |
| **UserController.php** | A · B | ctor + store koexistieren. |
| **GeneralTaskController.php** | A · B | Policy + employeeId() koexistieren. |
| **BrandDepartmentController.php** | A · D | products-Query + search-Fix koexistieren. |

**Überwachungspunkt für künftige Parallel-Runden:** `routes/web.php` + geteilte Controller
(User/GeneralTask/Inquiry/NewLeads) — **vor jedem Edit pullen, methodengenau bleiben.**
**Ergänzung Stopp 1:** `phpunit.xml` ist ab jetzt ebenfalls eine geteilte Kreuzung (Teil C).

---

## Teil E — Framework-Delta (belegt)

| Achse | Wert |
|---|---|
| Laravel | `laravel/framework: ^11.44.7` |
| PHP-Floor (composer) | `^8.2` |
| PHP-Laufzeit (CLI) | **8.4.21** |
| Config-Cache | nicht gecacht |

**Testrelevanter Delta:** Die Suite läuft auf **PHP 8.4**, während der Composer-Floor **8.2** ist.
→ Möglicher Lärm durch 8.4-**Deprecations** (z. B. implizit-nullable Parameter), der Tests
`risky`/`warning` macht, ohne echter Fehler zu sein. Beim MySQL-Lauf ist zwischen
*echtem Rot* (SQLite→MySQL) und *Deprecation-Lärm* (PHP-8.4) sauber zu trennen — nur echtes Rot
gehört in die Fixliste B.4.

> Hinweis: Die genaue prior-session-Definition von „Framework-Delta" ist nicht im Repo dokumentiert;
> die Achsen oben sind aus `composer.json` + `php -v` **belegt**.

---

## Teil F — Funktions-Landkarte (Verweis + Kern)

Vollständige 8-Zonen-Inventur: **`docs/crm-inventur-00-index.md`** (navigierbar).

| Zone | Größter Brocken |
|---|---|
| 01 Artikel/Katalog | `ProductController` ~1.964 Z. |
| 02 Lager/Beschaffung | `SupplierConnectionController` ~1.106 Z.; DATANORM nur Prototyp |
| 03 Angebot/Set-Konfiguration | Master-Set ~6.700 Z. + `config.blade.php` ~25.000 Z. |
| 04 Auftrags-Dokumente | wenig eigenständig; Doku = Datei-Ablage über `images` |
| 05 Organisation/HR | `EmployeeController` ~3.523 Z.; viele HR-Tabellen 0 Zeilen |
| 06 Projekt/Planer/Aufgaben/Assets | `PlannerPlanController` **~11.080 Z.**; 3 parallele Phasen-Systeme |
| 07 Medien/Kommunikation/Rest | Chat, KI (Ollama), E-Mail (heikel), Tools |
| 08 Legacy/Old | **~234 Dateien / ~58.500 Z. tot**, 0 Live-Routen |

**Datenmodell-Anker (Glossar):** Kunde=`new_leads` · Objekt=`lead_alternative_adds` ·
Gewerk=`lead_product_lists` · Angebot=`offers` · Auftrag=`deals`.

**Kernbefund für Stopp 1:** Zone 08 (`app/Http/Controllers/Old/`, ~58.500 Z.) ist toter Ballast
mit 0 Live-Routen — **darf die Test-Suite und den MySQL-Lauf nicht belasten** (eigener Aufräum-Strang, später).

---

## Teil G — Architektur-Vorschlag (Phase-0-Ergebnis)

> Belegte Ist-Situation je Punkt + vorgeschlagene Richtung. **Endentscheidung liegt bei Yama**
> (konsistent mit dem Architektur-Entscheidungen-Gate). Kein Umbau in Phase 1 ohne Freigabe.

### G.1 — `wb_`-Präfix (Legacy-Schema-Erbe)

**Ist (belegt):** `wb_` erscheint **nur** als toter String in `app/Http/Controllers/Old/CustomerController.php`.
In der DB `ticket` gibt es **0** `wb_`-Tabellen (`information_schema`, `table_name LIKE 'wb\_%'` = 0).
→ Der `wb_`-Präfix ist ein **Relikt des Vorgänger-Systems** ohne aktive Entsprechung.

**Vorschlag:** `wb_`-Referenzen mit dem Old/-Aufräum-Strang (Zone 08) entsorgen — **kein** neues
`wb_`-Namensschema einführen; neue Tabellen folgen dem bestehenden Laravel-Konvention (unpräfixiert).

### G.2 — `heizlast.`-Routen (Modul-Konsolidierung)

**Ist (belegt):** Heizlast-Logik ist **verstreut** — `AiMessageController` (Intent-Erkennung,
Prompt-Regeln), `PersonalTaskController:6235` (`notice_room_heatingload_windows_doors`),
`Services/PromptFactory:184`. **Kein** eigener Route-/Modul-Namespace.

**Vorschlag:** Heizlast als **eigenes Modul** mit `heizlast.`-Route-Namespace bündeln
(Eingabe → Berechnung → Angebots-Anbindung), damit die verstreuten Fragmente **einen** Ort +
**eigene Tests** bekommen. Kandidat für frühe Phase-1-Test-Abdeckung.

### G.3 — Auth-Ablösung (Gate-Wildwuchs)

**Ist (belegt):** Zwei **fast namensgleiche** Admin-Gate-Middlewares —
`app/Http/Middleware/isAdmin.php` **und** `app/Http/Middleware/is_Admin.php` — plus
`InvoiceMiddleware`, alle keyen auf `is_admin` + `user_rolls`. Guards: `web`=session/users,
`api`=sanctum/users. **Risiko:** `isAdmin` vs. `is_Admin` unterscheiden sich nur in der
Groß-/Kleinschreibung → auf case-insensitivem Dateisystem (macOS-Dev) **latenter Klassen-/Autoload-Konflikt.**

**Vorschlag:** Die Admin-Gates auf **ein** kanonisches Middleware + **Policies/Gates** konsolidieren,
den case-only-Doppelgänger auflösen, Sanctum als API-Fundament (für Nuriva) beibehalten.
**Reihenfolge beachten:** Auth-Umbau **nach** dem stabilen MySQL-Grün-Gate — nicht davor.

> Hinweis: Die detaillierte Begründung dieser drei Vorschläge stammt aus der Planungssitzung und ist
> hier **auf die belegten Code-Fakten verankert**. Feinentwurf je Punkt = eigenes Ticket in Phase 1+.

---

## Teil H — Freigabe-Gate zu Phase 1

Phase 1 startet **erst**, wenn **alle** Punkte grün sind:

- [x] ② Command-Attribut-Syntax grün (10× `$signature`, 0× `#[AsCommand]`).
- [x] ③ Isolations-Machbarkeit belegt (getrennte Schemata, Config ungecacht, Override trifft `ticket_testing`).
- [ ] ③ Isolations-Konfig **verdrahtet** (`phpunit.xml`-Block B.1 committed).
- [ ] ① Skeleton-Delta geschlossen (262er-Suite im Baum, Executor-Push).
- [ ] **B.3 MySQL-Grün: 262/262 gegen `ticket_testing`**, `ticket`-DB unangetastet.
- [ ] B.4 SQLite→MySQL-Fixliste vollständig (jeder Rot-Fall belegt+behoben, 0 erzwungene skips).

**Freigabe-Entscheid: Yama.** Erst danach beginnt Phase 1.
