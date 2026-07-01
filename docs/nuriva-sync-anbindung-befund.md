# Nuriva / Mobile-Sync-Anbindung — Befund

> **Reine Analyse (nur Lesen/Suche), kein Code geändert.** Ziel: klären, ob und wo dieses CRM eine mobile App (nuriva.de) / Sync bedient, damit die laufende **Kundenprofil-Zerlegung** darauf Rücksicht nehmen kann.
>
> **Kurzfassung vorab:** Die Nuriva-App selbst liegt **NICHT** in diesem Repo — sie ist eine **separate mobile App**. Was hier liegt, ist die **Server-/Backend-Seite (die API), die Nuriva konsumiert**: eigene `Api/Mobile*`- und `Planner*Api*`-Controller + `routes/api.php` mit **Sanctum-Token-Auth**. **Die Kundenprofil-Zerlegung (CSS/HTML-Auslagerung) berührt diese Mobile-Seite NICHT** — geteilt wird nur die **Datenschicht** (dieselben Tabellen), und die fassen wir ohnehin nicht an.

---

## 1. Wortspuren — wo taucht „Nuriva" / Mobile auf?

**„Nuriva" ist real und aktiv im Code** — als Bezeichnung der mobilen Gegenstelle, nicht als hier liegender App-Code. Belege (Auswahl):

| Datei:Zeile | Fund |
|---|---|
| `database/migrations/2026_06_26_011304_make_images_alternative_nullable_for_mobile_photos.php:19` | Kommentar „**Nuriva may send photos** for a customer task where no lead alternative is selected yet" |
| `app/Http/Controllers/Api/MobileAttendanceController.php:24` | „Main endpoint used by **Nuriva mobile**" |
| `app/Http/Controllers/Api/MobileCalendarController.php:828` | „**Nuriva local** can pass employee_id, but token-auth user should win" |
| `app/Http/Controllers/Planner/PlannerMobileCustomerImageController.php:23` | „**Called by Nuriva.** Saves all task photos into the normal nuri-head…" |
| `app/Http/Controllers/Planner/PlannerEmployeeApiController.php:1779` | „This is what **Nuriva can read** immediately when it reloads planner-work" |
| `PlannerPlanController.php`, `PlannerItemMaterialController.php`, `PlannerEmployeeApiController.php` (mehrfach) | Quell-Kennzeichnung `'source' => 'nuriva_mobile'` bei Material-/Bericht-Datensätzen |
| `app/Http/Controllers/Ticket/ProblemController.php:173,406,2157` | Feld `nuriva` (boolean) am Ticket/Problem — „Nuriva-Montage" |
| `resources/views/admin/problem/problem.blade.php` (mehrere) | Web-UI „Nuriva Montage" / Checkbox `name="nuriva"` — Admin markiert, dass ein Ticket eine Nuriva-(Montage-)Aufgabe ist |

**Deutung:** „Nuriva" bezeichnet durchgängig die **externe mobile App der Monteure/Mitarbeiter**. Der hiesige Code **empfängt** von ihr (Fotos, Material-Anfragen, Anwesenheit, Berichte) und **liefert** ihr Daten (Aufgaben, Kalender, Kunden). „nuri-head" = dieses Backend-CRM.

---

## 2. API / Schnittstellen — `routes/api.php` (Präfix `/api`)

Es gibt eine vollwertige `routes/api.php` (365 Z.). Die Mobile-/Planner-Endpunkte:

### `/api/mobile/*` — Haupt-App (Sanctum + `throttle`)
| Methode | Pfad | Controller |
|---|---|---|
| POST | `/api/mobile/login` | `MobileAuthController::login` |
| GET | `/api/mobile/me` · POST `/logout` | `MobileAuthController` |
| GET | `/api/mobile/profile` | `MobileProfileController::show` |
| GET | `/api/mobile/tasks` · POST **`/tasks/sync`** | `MobilePlannerApiController` |
| POST | `/api/mobile/attendance/action` · `/location` · **`/sync`** · GET `/history` · POST `/log` | `MobileAttendanceController` |
| GET/POST | `/api/mobile/calendar` · GET `/calendar/{id}` | `MobileCalendarController` |
| GET | `/api/mobile/employees` | `MobileEmployeesController` |
| GET | **`/api/mobile/customers`** | `MobileCustomersController` |

### `/api/planner/*` — Planner-/Monteur-Seite (Token via `/auth/token`, dann Sanctum)
| Methode | Pfad | Controller |
|---|---|---|
| POST | `/api/planner/auth/token` · `/auth/me` · `/auth/logout(-all)` | `PlannerApiAuthController` |
| GET | `/api/planner/my-work` · `/my-day-report` · `/employees/{e}/work` · `/day-report` | `PlannerEmployeeApiController` |
| PATCH | `/api/planner/items/{item}/complete-report` | `PlannerEmployeeApiController` |
| POST/GET | **`/api/planner/customer-images/upload`** · `/customer-images` | `PlannerMobileCustomerImageController` |
| GET/POST/DELETE | `/api/planner/master-sets/*` · `/items/{item}/master-sets/*` · `/plans/{plan}/…` | `PlannerMasterSetController` |
| GET/POST | `/api/planner/items/{item}/materials` | `PlannerItemMaterialController` |

### Weitere
- `/api/secure/master-sets` (+ `/{id}`, `/master-sets-debug`) — `MasterSetApiController`
- `/api/user` (Sanctum) · `/api/lead-name-suggestions`, `/api/lead-lastname-suggestions` (`NewLeadsController`)
- `/api/fusion/webhook`, `/api/fusion-form/webhook/entries` — externe Formular-Webhooks (`FusionWebhookController`)

**Controller im `Api/`-Ordner:** `MobileAuthController`, `MobileProfileController`, `MobilePlannerApiController`, `MobileAttendanceController`, `MobileCalendarController`, `MobileEmployeesController`, `MobileCustomersController`, `MasterSetApiController`, `ApiLinkController`. **Planner-API:** `PlannerApiAuthController`, `PlannerEmployeeApiController`, `PlannerMobileCustomerImageController`, `PlannerMasterSetController`, `PlannerItemMaterialController`.

→ **Klar mobil/Sync:** JSON-Endpunkte, Token-Auth, dedizierte **`/sync`-Routen** (`tasks/sync`, `attendance/sync`), Foto-Upload aus dem Feld.

---

## 3. Auth für Mobil — **Laravel Sanctum (Token)**

- `composer.json:16` → `"laravel/sanctum": "^4.0"`; `config/sanctum.php` vorhanden.
- Migration `2019_12_14_000001_create_personal_access_tokens_table.php` vorhanden (Token-Speicher).
- `routes/api.php`: Mobile-/Planner-Gruppen unter `middleware('auth:sanctum')`; `PlannerApiAuthController::token` stellt Tokens aus (`/api/planner/auth/token`), `MobileAuthController::login` ebenso.

→ **Die mobile Seite meldet sich per Token an** — **getrennt** von der **Session-Auth** (`auth`), mit der der Admin das Web-Kundenprofil nutzt. Zwei getrennte Auth-Welten, zwei getrennte Route-Dateien (`api.php` vs `web.php`).

---

## 4. Sync-Datenmodell

- **Sync passiert endpunkt-basiert** (Push-Aktionen + Pull-Snapshots): `POST /api/mobile/tasks/sync`, `POST /api/mobile/attendance/sync`, Foto-`upload`, Material-`store`.
- **Kein klassisches Offline-Replikations-Gerüst** gefunden: keine prominenten Felder wie `device_id`, `->uuid`, `outbox`, `is_dirty`, `pending_sync`, `synced_at` in den Migrationen; **kein** Capacitor/Expo/React-Native/Flutter/`fcm_token`/`push_token` im Repo (erwartbar — die App liegt woanders).
- **`sqlite`** taucht nur als Standard-`config/database.php`-Treiberblock auf (Laravel-Default), **kein** mobiler Offline-Store hier.
- Quell-Markierung `'source' => 'nuriva_mobile'` unterscheidet mobil erzeugte Datensätze (Material, Berichte) von web-erzeugten — das ist die faktische „Herkunfts"-Spur statt eines Dirty-Flags.
- Eine Migration passt die **`images`-Tabelle** an Mobil an: `images.alternative_id` wird **nullable** gemacht, „**Nuriva may send photos** … where no lead alternative is selected yet".

→ **Fazit Datenmodell:** Request/Response-Sync über die API, keine Offline-First-Replikation im hiesigen Schema. Die mobile Seite schreibt/liest direkt in die **regulären** CRM-Tabellen.

---

## 5. Bezug zum Kundenprofil (kritisch) — **geteilt wird die DATENSCHICHT, nicht die View**

Die Mobile-/Planner-API greift auf **genau die Glossar-Kern­tabellen** zu, die auch das Kundenprofil nutzt:

| Tabelle (Glossar) | Mobile-/Planner-Fundstelle |
|---|---|
| **`new_leads`** (Kunde) | `MobileCustomersController:15` `DB::table('new_leads')`-Suche · `PlannerMobileCustomerImageController:36` `exists:new_leads,id` · `/api/lead-*-suggestions` |
| **`lead_alternative_adds`** (Objekt) | `MobileCalendarController:638-668` liest Objekte · `PlannerMobileCustomerImageController:37` `exists:lead_alternative_adds,id` · `PlannerAttendanceController` `alternative_id` |
| **`lead_product_lists`** (Gewerk) | `MobileCalendarController` `lead_product_list_id` · `PlannerAttendanceController:36` `DB::table('lead_product_lists')` · `PlannerMobileCustomerImageController:39` |
| **`images`** | Mobile-Foto-Upload (`images.alternative_id` nullable gemacht) |

**Nutzt das Kundenprofil selbst `/api/`?** Praktisch **nein**. Die Profil-Blade-AJAX-Aufrufe laufen über ein **eigenes `CP.api.*`-Objekt** auf **Web-Routen** (`web.php`, session-auth) — z. B. `CP.api.mediaIndex/mediaUpload/leadBlade/loadData/store/show/update` (`customer_profile.blade.php:3134-3668`). Die einzigen literalen „/api/"-Treffer in der Blade sind die **Google-Maps-URL** (`maps/api/js`). → **Profil-AJAX ≠ Mobile-API.** Andere Route-Datei, andere Auth, andere Controller.

---

## 6. Fazit & Kernfrage

### Liegt Nuriva in diesem Repo?
**Nein — die App nicht.** Hier liegt nur die **Backend-Schnittstelle**, die Nuriva bedient:
- **Routen:** `routes/api.php` (`/api/mobile/*`, `/api/planner/*`, `/api/secure/master-sets`).
- **Controller:** `app/Http/Controllers/Api/Mobile*` + `app/Http/Controllers/Planner/Planner*Api*` / `PlannerMobileCustomerImageController`.
- **Auth:** Sanctum-Token (`personal_access_tokens`, `config/sanctum.php`).
- **Sync:** endpunkt-basiert (`/tasks/sync`, `/attendance/sync`, Foto-Upload), keine Offline-Replikations-Felder.

### Muss die Kundenprofil-Zerlegung auf die Mobile-/Sync-Seite Rücksicht nehmen?

**Für die aktuell laufende Arbeit: NEIN.** Scheibe 1 (Inline-CSS → Datei) und die geplante Scheibe 2 (Modal-HTML → Blade-Partials, **wortgleich, IDs unverändert**) berühren **ausschließlich das serverseitige Rendering der Admin-Web-View** `customer_profile.blade.php`. Die Nuriva-App **rendert oder liest diese Blade nie** — sie spricht **JSON-API-Endpunkte** per **Token** an. Verschieben von CSS/HTML ändert **null** an: API-Routen, JSON-Formaten, Controllern, DB-Schema. → **Mobile ist unberührt.**

**Worauf trotzdem zu achten ist (Grenze, die aber ohnehin unsere Standing-Entscheidung ist):**

1. **Datenschicht ist heilig — sie ist die einzige geteilte Fläche.** Die Tabellen/Spalten der Glossar-Triple **`new_leads` · `lead_alternative_adds` · `lead_product_lists`** (+ `images.alternative_id`) werden **von beiden Seiten** genutzt. Ein physischer **Rename/Drop** von Tabellen/Spalten würde die **Mobile-API mitreißen**. → Bestätigt die bereits getroffene **„KEIN physischer Rename"**-Entscheidung (siehe `glossar.md`). Die reine View-Zerlegung fasst diese Schicht **nicht** an.
2. **JSON-Antwortformen der `/api/mobile`- und `/api/planner`-Endpunkte** dürfen sich nicht unbemerkt ändern — betrifft aber nur Arbeit an **jenen** Controllern, **nicht** die Profil-View-Zerlegung.
3. **Element-IDs & die `CP.api.*`-Web-Routen des Profils sind web-only** (Session-Auth, Admin). Sie zu ändern würde **die Mobile-Seite nicht** treffen (die nutzt sie nicht) — wir halten die IDs nur wegen des **profil-eigenen** JS stabil, nicht wegen Nuriva.

**Merksatz für die weitere Zerlegung:** *Solange wir nur Views/CSS/HTML/Profil-JS umräumen und die Tabellen `new_leads`/`lead_alternative_adds`/`lead_product_lists`/`images` sowie die `/api/*`-Verträge nicht anfassen, ist die Nuriva-Mobile-Seite nicht betroffen.* Erst wenn eine spätere Scheibe an **gemeinsam genutzte Controller-Methoden** oder an das **DB-Schema** ginge, wäre eine gezielte Mobile-Prüfung nötig — dann separat.

---

*Reine Analyse — nichts geändert. Belege: projektweiter Grep (`nuriva`, `sync`, `mobile`, `sanctum`, `capacitor/sqlite/expo/…`), `routes/api.php` (Route-Landkarte), `app/Http/Controllers/Api/` + `Planner/`-Listing, Migration `…make_images_alternative_nullable_for_mobile_photos`, `composer.json` (`laravel/sanctum`), `config/sanctum.php`, `customer_profile.blade.php` (`CP.api.*`-Aufrufe). Querverweis: `glossar.md`, `kundenprofil-zerlegung-schnittplan.md`, `legacy-integrationen-ignorieren` (Memory — Bitrix/NIBE/IMAP sind separate Alt-Integrationen, NICHT Nuriva).*
