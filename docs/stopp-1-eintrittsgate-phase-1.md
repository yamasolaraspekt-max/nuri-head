# Stopp-1 — Eintrittsgate zu Phase 1 (EIN Prüfgegenstand)

> **Rolle:** Planer/Überwacher (diese Instanz plant & prüft, die Executor-Instanz führt Code aus).
> **Zweck:** Alle Phase-0-Ergebnisse in **einem** Dokument bündeln, damit Stopp 1 als **ein**
> Prüfgegenstand freigegeben werden kann. Phase 1 startet **nicht**, bevor dieses Gate grün ist.
> **Stand:** 2026-07-04 · Branch `private/app-code-backup` · alle Ist-Aussagen **belegt aus dem Code**,
> nicht vermutet. Offene Stellen sind als 🔴/🟡 markiert.

**Gate-Status gesamt: 🟢 GESCHLOSSEN** (2026-07-04) — MySQL-Grün-Nachweis erbracht
(**271/271 grün gegen MySQL in wberechnung**, Teil B.6) + ticket-Test-Isolation in `phpunit.xml`
verdrahtet (Teil A/③). Phase-1-Freigabe liegt bei Yama (Teil H).

> **Gate-Definition (Yama-Entscheid, 2026-07-04):** Stopp-1-Eintritt = **271/271 grün gegen MySQL
> IN WBERECHNUNG** — beweist, dass der Heizlast-Code MySQL-tauglich ist und die SQLite-Risiken für
> den späteren Umzug nach ticket ausgeräumt sind. Die 271 Tests **in ticket** sind **nicht**
> Gate-Bedingung, sondern das **Abnahme-Kriterium von Phase 1.4** (nach der echten Transplantation
> von Code+Tests). Hintergrund: wberechnung ist SQLite-nativ; der MySQL-Lauf ist der Umzugs-Tauglichkeits-Beweis.

---

## Teil A — Die drei Verifikationen (belegt)

### ① Skeleton-Delta — 🟢 umgewidmet (kein Gate-Blocker mehr)

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

**Umwidmung (Yama-Entscheid 2026-07-04):** Die Suite gehört zur eigenständigen App **wberechnung**
(SQLite-nativ; belegt: 57 Routen, 15 Controller, 48 Services, 43 Migrationen — die Tests exerzieren
`route('heizlast.*')`, `App\Services\…`, konkrete Rechenergebnisse). Ein reiner Test-Copy nach ticket
erzeugt 271× Rot (fehlende Routen/Services/Models) — **keine** Namespace-Anpassung, sondern fehlende
Applikation. Deshalb ist ① **kein Gate-Blocker**: Das Skeleton-Delta schließt sich durch die echte
**Phase-1.4-Transplantation** (Code+Tests), nicht durch Kopie. Der Gate-relevante Beweis ist der
**MySQL-Lauf in wberechnung** (Teil B.6) — inzwischen **erbracht**.

> Aktueller Suite-Umfang (belegt am Lauf): **271 Tests** (Verlauf 262 → 266 → 271; die Quelle wird
> aktiv weiterentwickelt). „262" war ein Zwischenstand aus der Planungssitzung.

### ② Command-Attribut-Syntax — ✅ grün

Alle **10** Commands in `app/Console/Commands/` deklarieren via `protected $signature`;
**0** nutzen das PHP-8-Attribut `#[AsCommand]`. **Keine Mischsyntax**, kein Attribut-/Property-Konflikt,
der einen Scheduler-/Kernel-Boot in Tests brechen könnte.

Betroffene Commands (alle konsistent):
`BackfillLeadStageId`, `BackfillPhaseSections`, `DeactivateExpiredBreakingNews`,
`DispatchMainAppointmentReminders`, `ProcessPersonalTaskScheduler`, `PurgeSoftDeletedGarbage`,
`SyncLeadEmails`, `SyncSolarNewsToChat`, `UpdateJobRepresentativeStatus`, `UpdateLeaveStatus`.

**Verifikation bestanden.**

### ③ Test-Isolation — 🟢 verdrahtet (2026-07-04)

> **Erledigt:** Der Fix aus B.1 ist in `ticket/phpunit.xml` committed — `DB_CONNECTION=mysql` +
> `DB_DATABASE=ticket_testing`, beide `force="true"`. Jeder ticket-Testlauf trifft ab jetzt
> strukturell `ticket_testing`, nie die reale `ticket`-Dev-DB. Der ursprüngliche Ist-Befund (unten)
> bleibt als Beleg der Ausgangslage stehen.

Belegter Ist-Stand (vor dem Fix):

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

### B.3 — Akzeptanzkriterien (hart) — ✅ alle erfüllt (siehe B.6)

Das Gate ist **nur** grün, wenn **alle** erfüllt sind — Nachweis je Kriterium in B.6:

1. ✅ **271/271 Tests grün** (`"result":"passed"`), **0 failed, 0 errored**.
2. ✅ Lauf **nachweislich gegen MySQL** (isolierte DB `wberechnung_mysql_test`, 40 Tabellen migriert —
   SQLite hätte 0 hinterlassen).
3. ✅ **Die realen ticket-DBs bleiben unangetastet** (`ticket` + `ticket_testing` je 410, vorher = nachher).
4. ✅ **Kein erzwungenes skip** — die SQLite→MySQL-Fixliste (B.4) ist leer, weil 0 Rotfälle auftraten
   (nicht, weil etwas übersprungen wurde).

### B.4 — SQLite→MySQL-Fixliste

**Ergebnis des MySQL-Laufs (B.6): LEER — 0 Rotfälle.**

| # | Test (Datei::Methode) | Fehlermeldung | Ursache-Klasse | Fix |
|---|---|---|---|---|
| — | *(keine — alle 271 Tests grün gegen MySQL auf Anhieb)* | — | — | — |

Der Heizlast-Code ist **out-of-the-box MySQL-tauglich**: keine der erwartbaren SQLite→MySQL-Fallen
(strict-mode/`ONLY_FULL_GROUP_BY`, `AUTO_INCREMENT` vs. rowid, JSON-Handling, FK-Reihenfolge bei
`migrate:fresh`, Tabellennamen-Case, `TEXT`-Defaults, Datums-/Zeitzonen-Vergleiche, `DatabaseTransactions`
unter MySQL) ist aufgetreten. Das spricht für sauberen, Eloquent-/Query-Builder-basierten Datenzugriff
ohne DB-spezifisches Roh-SQL — der beste denkbare Ausgangspunkt für die Phase-1.4-Transplantation.

### B.5 — Schema-Vorbeweis (ticket-Seite): Status

🟡 Der `migrate:fresh`-Lauf gegen `ticket_testing` (566 ticket-Migrationen) gehört zur
**Phase-1.4-Abnahme** (wenn der Heizlast-Code tatsächlich in ticket liegt), nicht zum Stopp-1-Gate.
Die ticket-Isolation ist verdrahtet (③), die Machbarkeit belegt — der volle ticket-Testlauf ist damit
jederzeit sicher startbar.

### B.6 — MySQL-Grün-Nachweis (erbracht 2026-07-04) ✅

**Kommando** (env-Override; **null** wberechnung-Dateiänderung; wberechnungs eigenes PHPUnit-Binary +
Config → kein Versions-Mix mit ticket):

```bash
DB_CONNECTION=mysql DB_HOST=localhost DB_DATABASE=wberechnung_mysql_test \
DB_USERNAME=ticket_user DB_PASSWORD=*** \
php /Users/yamanuri/Herd/wberechnung/vendor/bin/phpunit \
    --configuration /Users/yamanuri/Herd/wberechnung/phpunit.xml
```

**Ergebnis (wörtlich):**

```json
{"tool":"phpunit","result":"passed","tests":271,"passed":271,"assertions":1045,"duration_ms":4336}
```

→ **271/271 grün gegen MySQL** (Suite auf 271 gewachsen; Verlauf 262 → 266 → 271).

**Beweis, dass es MySQL war (nicht SQLite):** nach dem Lauf trägt `wberechnung_mysql_test`
**40 migrierte Tabellen** (`RefreshDatabase` = `migrate:fresh` gegen MySQL). SQLite `:memory:` hätte
die DB **leer** gelassen.

**Beweis Unversehrtheit der ticket-DBs (Baseline vorher = nachher):**

| DB | Tabellen vorher | Tabellen nachher |
|---|---|---|
| `ticket` (reale Dev-DB) | 410 | 410 |
| `ticket_testing` | 410 | 410 |
| `wberechnung_mysql_test` | 0 | 40 (migriert) |

**wberechnung-Setup-Änderung:** **KEINE Dateiänderung** (reiner env-Override, per Definition
uncommitted). Einziges Artefakt: die MySQL-DB `wberechnung_mysql_test` (von mir via root angelegt,
`ticket_user` mit `GRANT ALL`). **Empfehlung:** DB als Wegwerf-Test-DB belassen (der Re-Check B.7
nutzt sie wieder); kein Commit im wberechnung-Repo nötig — und es erfolgte keiner.

> ⚠️ **Schnappschuss-Charakter:** wberechnung ist **aktiv in Entwicklung** (Suite/Daten wachsen weiter,
> 262 → 266 → 271). Diese `271/271` sind der Stand **2026-07-04**. Der Nachweis belegt MySQL-Tauglichkeit
> **zu diesem Zeitpunkt**. Bei jeder wberechnung-Änderung neu ziehen → **Teil I** (Ein-Befehl-Re-Check).
> Der endgültige Abnahme-Lauf ist der Phase-1.4-Transplantlauf gegen den dann finalen Stand.

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

- [x] ② Command-Attribut-Syntax grün (10× `$signature`, 0× `#[AsCommand]`).
- [x] ③ Isolations-Machbarkeit belegt (getrennte Schemata, Config ungecacht, Override trifft `ticket_testing`).
- [x] ③ Isolations-Konfig **verdrahtet** (`phpunit.xml`-Block B.1 committed).
- [x] ① Skeleton-Delta umgewidmet (Suite gehört zu wberechnung; schließt sich via Phase 1.4, **kein** Gate-Blocker).
- [x] **MySQL-Grün erbracht: 271/271 gegen MySQL in wberechnung** (B.6), ticket-DBs unangetastet (410/410).
- [x] B.4 SQLite→MySQL-Fixliste: **leer, 0 Rotfälle** (kein erzwungenes skip).

**→ Stopp-1-Gate 🟢 GESCHLOSSEN (2026-07-04).** **Freigabe-Entscheid für Phase 1: Yama.**

> Rest-Vorbehalt (kein Blocker): wberechnung wächst weiter → der Nachweis ist ein Schnappschuss.
> Vor dem eigentlichen Phase-1.4-Transplant den Re-Check (Teil I) gegen den dann aktuellen Stand fahren.

---

## Teil I — Re-Verifikation & Cut-over (wberechnung → ticket)

### I.1 — Ein-Befehl-Re-Check (bei jeder wberechnung-Änderung)

wberechnung ist SQLite-nativ und wächst weiter. Um die MySQL-Tauglichkeit **jederzeit neu** zu belegen,
ohne etwas an wberechnung zu ändern:

```bash
bash scripts/wberechnung-mysql-check.sh
```

Das Script (im ticket-Repo) fährt die **komplette aktuelle** wberechnung-Suite gegen die isolierte
`wberechnung_mysql_test` und druckt danach den Tabellen-Beweis (MySQL genutzt + ticket-DBs unberührt).
Zugangsdaten liegen lokal, **nicht versioniert** in `scripts/wberechnung-mysql-test.env` (gitignored).
Voraussetzung (einmalig, schon erfüllt): DB `wberechnung_mysql_test` + `GRANT` für `ticket_user`.

**Re-Check-Historie:**

| Datum | Tests | Ergebnis | Rotfälle (SQLite→MySQL) |
|---|---|---|---|
| 2026-07-04 | 271 | ✅ 271/271 | 0 |

### I.2 — Cut-over-Kriterium: ab wann in ticket weiterbauen, wberechnung stoppen?

**Technisch bist du nicht blockiert:** der MySQL-Beweis (271/271) heißt, der Umzug ist **jederzeit**
möglich. Es geht nur um den **günstigsten Moment** — den Punkt, ab dem Weiterbauen in wberechnung mehr
kostet (größerer Transplant, Divergenz), als es an Velocity bringt.

**Wechsle nach ticket (und friere wberechnung ein), sobald EINES zutrifft:**

1. **Schema-Stillstand:** Seit ~1–2 Wochen **keine neue Migration** mehr in wberechnung (nur noch
   Service-/UI-Feinschliff). Die 43 Migrationen sind der **teuerste** Transplant-Faktor (Kollision mit
   tickets 566). Stabiles Schema = günstigstes Umzugsfenster.
2. **ticket-Daten-Bedarf:** Das nächste sinnvolle Feature braucht echte ticket-Entitäten
   (Kunde=`new_leads`, Objekt=`lead_alternative_adds`, Angebot=`offers`). Dann ist Weiterbau in
   wberechnung Arbeit an einer **Attrappe**, die beim Umzug nochmal angefasst werden muss.
3. **Transplant wächst schneller als Fortschritt:** Wenn pro Woche mehr Code/Migrationen/Routen
   **dazukommen**, als fachlich Neues entsteht → du portierst künftig mehr, als du gewinnst.
4. **Roter Re-Check:** Falls `scripts/wberechnung-mysql-check.sh` je **rot** wird (neue SQLite-Eigenheit),
   ist das ein Frühwarnsignal, dass die Divergenz teuer wird — dann zeitnah umziehen statt weiter divergieren.

**Solange KEINES zutrifft:** in wberechnung weiterbauen ist **richtig** — SQLite-Velocity, isoliert,
schnell. Heute (2026-07-04) sind das **48 Services / 15 Controller / 43 Migrationen / 57 Routen** — noch
gut transplantierbar. Miss den Cut-over an der **Migrations-Kurve**: flacht sie ab → Umzugsfenster.
