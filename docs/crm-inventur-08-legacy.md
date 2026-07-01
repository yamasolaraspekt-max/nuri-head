# CRM-Inventur 08 — Zone: Old Code / Legacy

- **Zone:** Old Code / Legacy (`app/Http/Controllers/Old/` + alle „Old"/„Legacy"/„OLD CODE"/„copy"/„test"/„v1"-Pfade in Controllern, Views, Models)
- **Stand:** 2026-07-01
- **Art:** REINE ANALYSE — nur Lesen. Kein Git, kein Build, kein `composer dump-autoload`. Keine Änderungen vorgenommen.

---

## 0. Kernbefund (kurz vorweg)

Der HINWEIS aus der Vor-Analyse ist **bestätigt und verschärft**:

1. `app/Http/Controllers/Old/` enthält **40 Dateien / 14.805 Zeilen**.
2. **37 von 40** tragen den falschen Namespace `App\Http\Controllers` (statt `…\Old`). PSR-4 mappt `App\` → `app/`, d.h. der FQCN `App\Http\Controllers\XController` wird unter `app/Http/Controllers/X.php` gesucht — diese Pfade **existieren nicht** (die Dateien liegen in `…/Old/`). Zusätzlich sind **KEINE** dieser Klassen im `vendor/composer/autoload_classmap.php` (334 Controller-Einträge geprüft, 0 Treffer für die Old-Klassen). ⇒ **Nicht autoloadbar. Fatal bei Aufruf.**
3. **ABER:** Es zeigt **keine einzige LIVE-Route** auf diese Klassen. Alle `Route::…([XController::class,…])`-Zeilen in `routes/web.php` sind **auskommentiert** (`//`). Aktiv sind nur 10 verwaiste `use`-Imports (Zeilen 148–387 in `web.php`) — die lösen kein Autoload aus und sind harmlos, aber toter Ballast.
4. `routes/api.php`: **0 Treffer** für alle Old-Klassen.

**Fazit Old/-Controller:** kein aktiver Schaden (keine Live-Route), aber 100 % toter Code. Löschbar inkl. der 10 `use`-Zeilen.

Zusätzlich **~194 Legacy-View-Dateien / ~43.700 Zeilen** (Kopien, `test`-, `v1`-, „Old Code"-Ordner) als reiner Ballast.

---

## 1. `app/Http/Controllers/Old/` — 40 Controller (14.805 Zeilen gesamt)

Routen-Status je Klasse. „TOT" = 0 Live-Route (nur auskommentiert und/oder nur `use`-Import). **Kein** Old-Controller hat eine aktive Route. Alle sind zusätzlich **nicht autoloadbar** (falscher Namespace + nicht im Classmap), außer wo unten anders vermerkt.

| Datei (in `Old/`) | Deklarierter FQCN | web.php | api.php | Routen-Status (Beleg) | Empfehlung |
|---|---|---|---|---|
| AddImageToSetController.php | `App\…\AddImageToSetController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| AddProductToSetController.php | `App\…\AddProductToSetController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| AppointmentCommentController.php | `App\…\AppointmentCommentController` | 3 | 0 | **TOT** — nur auskommentiert (web.php:3907–3909, alle `//`) | löschbar |
| AppointmentController.php | `App\…\AppointmentController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| AppointmentAttachmentController.php | `App\…\AppointmentAttachmentController` | 4 | 0 | **TOT** — nur auskommentiert (web.php:3914–3917, alle `//`) | löschbar |
| CustomerAlternativeAddController.php | `App\…\CustomerAlternativeAddController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| CustomerController.php | `App\…\CustomerController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| CustomerCartController.php | `App\…\CustomerCartController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| CustomerProductController.php | `App\…\CustomerProductController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| EmployeeSetController.php | `App\…\EmployeeSetController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| GroupSetController.php | `App\…\GroupSetController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| JobRepresentativeController.php | `App\…\JobRepresentativeController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| NewLeadResponsibilityController.php | `App\…\NewLeadResponsibilityController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| NewLeadsInvoiceController.php | `App\…\NewLeadsInvoiceController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:370), keine aktive Route | löschbar (+ use entfernen) |
| OfferCoverController.php | `App\…\OfferCoverController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| OfferGreetingController.php | `App\…\OfferGreetingController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:148) | löschbar (+ use entfernen) |
| OfferProductListController.php | `App\…\OfferProductListController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| OfferConfigController.php | `App\…\Customer\Offer\OfferConfigController` | 0 | 0 | **TOT** (0 Treffer; nicht im Classmap) | löschbar |
| PVChecklistController.php | `App\…\PVChecklistController` | 2 | 0 | **TOT** — nur auskommentiert (web.php:1509–1510, `//`) | löschbar |
| PVLongChecklistController.php | `App\…\PVLongChecklistController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| PVLongRoofController.php | `App\…\PVLongRoofController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProductMasterSetController.php | `App\…\ProductMasterSetController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProductSubSetController.php | `App\…\ProductSubSetController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProjectAwardController.php | `App\…\ProjectAwardController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProjectControlPersonController.php | `App\…\ProjectControlPersonController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:186) | löschbar (+ use entfernen) |
| ProjectController.php | `App\…\ProjectController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProjectFeedbackController.php | `App\…\ProjectFeedbackController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProjectMontageChecklistController.php | `App\…\ProjectMontageChecklistController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProjectMontagePhaseListController.php | `App\…\ProjectMontagePhaseListController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProjectTaskAttachmentController.php | `App\…\ProjectTaskAttachmentController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:188) | löschbar (+ use entfernen) |
| ProjectTaskCommentController.php | `App\…\ProjectTaskCommentController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:187) | löschbar (+ use entfernen) |
| ProjectTaskController.php | `App\…\ProjectTaskController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| ProjectTimeRequestController.php | `App\…\ProjectTimeRequestController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:364) | löschbar (+ use entfernen) |
| ProjectTimelineController.php | `App\…\ProjectTimelineController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:387) | löschbar (+ use entfernen) |
| RealtimeNotificationDebugController.php | `App\…\RealtimeNotificationDebugController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:363) | löschbar (+ use entfernen) |
| SetParagraphController.php | `App\…\SetParagraphController` | 0 | 0 | **TOT** (0 Treffer) | löschbar |
| TaskToDoController.php | `App\…\TaskToDoController` | 1 | 0 | **TOT** — nur `use`-Import (web.php:159) | löschbar (+ use entfernen) |
| WPChecklistController.php | `App\…\WPChecklistController` | 2 | 0 | **TOT** — nur `use`-Import (web.php:153) + auskommentiert | löschbar (+ use entfernen) |
| `OverdueCenterController copy.php` | `App\…\OverdueCenterController` | — | — | **TOT (Kopie)** — Klassen-/Dateiname-Duplikat; Live-Version lebt unter `app/Http/Controllers/Report/OverdueCenterController.php` (web.php: 20 Treffer). Diese *Kopie* nicht autoloadbar. | löschbar (Kopie) |
| `oldMainAppointment.php` | `App\…\MainAppointmentController` | (40) | 0 | **TOT** — Dateiname ≠ Klassenname ⇒ nicht ladbar. Die 40 web.php-Treffer für `MainAppointmentController` binden an die **Live-Klasse** `App\Http\Controllers\Appointment\MainAppointmentController` (im Classmap). Diese Old-Datei ist irrelevant. | löschbar |

**Beleg Autoload:** `vendor/composer/autoload_psr4.php` → `'App\\' => app/`. `vendor/composer/autoload_classmap.php` (1,1 MB, Stand 29.06.) enthält **0** Einträge für die 37 Old-Klassennamen. Erwartete PSR-4-Pfade (`app/Http/Controllers/<Name>.php`) existieren nicht — die Datei liegt nur in `Old/`. ⇒ Aufruf würde `Class not found` (fatal) werfen. Da aber keine Live-Route auf sie zeigt, tritt der Fehler aktuell nicht auf.

---

## 2. Legacy-Views (Kopien / test / v1 / „Old Code"-Ordner) — ca. 194 Dateien / ~43.700 Zeilen

Reiner Ballast (Blade-Kopien, Prototypen, alte Codestände). Nicht per Route gebunden (Views werden über `view('…')` referenziert; die Kopien tragen abweichende Namen und werden nicht aufgerufen). Gruppiert nach Verzeichnis, größte zuerst:

| Bereich / Verzeichnis | Zweck (erkennbar) | Dateien / Zeilen | Routen-Status | Empfehlung |
|---|---|---|---|---|
| `admin/checklist/profitablity_calculation/Old Code/` (+ `partials/`) | Alte Wirtschaftlichkeits-/Bonus-Berechnung (`profit.blade copy 2-5`, `bonus.blade copy`) | 26 / ~40.371 | TOT (Kopie-Namen) | löschbar |
| `admin/dashboard/old codes/` | Alte Dashboard-/Mobile-Stände (`dashboard.blade copy 2-5`, `copy new`, `mobile.blade copy`) | 10 / 35.673 | TOT | löschbar |
| `admin/offer/configuration/offer/Old/` | Alte Angebots-Konfiguration (`config.blade copy 1-4`) | 4 / 29.274 | TOT | löschbar |
| `admin/kanban/oldcode/` | Alte Kanban-/List-Ansicht | 6 / 29.521 | TOT | löschbar |
| `admin/layouts/OLD CODE/` | Alte App-Layouts (`app.blade copy mar 2025`, `test.blade copy 2-5`) | 10 / 28.573 | TOT | löschbar |
| `admin/offer/old/` | Alte Angebots-Index/Folder-Show | 3 / 28.449 | TOT | löschbar |
| `admin/planner/old/` (+ `partials/`, `components/`) | Alter Planer (Visual/Index-Kopien) | 14 / ~23.666 | TOT | löschbar |
| `admin/new_leads/old code/` (+ `edit copy/`) | Alte Lead-/Kundenprofile (`customer_profile.blade copy2/22`, `.bladessdf copy`) | 14 / ~15.782 | TOT | löschbar |
| `admin/daily_report/prototype/` | Prototyp Tagesbericht (`test1.blade`, `blackgrading copy`) | 5 / 10.396 | TOT (Prototyp) | löschbar |
| `admin/project/old code/` | Alte Projekt-Kundenansicht (`customer_view.blade copy/copy1`) | 4 / 9.052 | TOT | löschbar |
| `admin/todo/personal/Old Codes/` | Alte To-Do-/Task-Ansichten (`task_view.blade copy 2`, `copy2`) | 7 / 8.967 | TOT | löschbar |
| `admin/inquiry/oldCode/` | Alte Kontakt-/Anfrage-Views | 3 / 6.223 | TOT | löschbar |
| `admin/roof_config/` | Dach-Konfig-Kopien (`config.blade copy 2-7`, `config2.blade copy`) | 8 / 5.913 | TOT (Kopien) | löschbar |
| `admin/product/Old Code/` | Alte Produkt-Views | 3 / 4.036 | TOT | löschbar |
| `admin/product/inventory/purchase_request/oldCode/` | Alte Bestellanforderung | 3 / 2.456 | TOT | löschbar |
| `admin/todo/task_to_do/oldCode/` | Alte Task-Projekt-View | 1 / 2.124 | TOT | löschbar |
| `admin/employee/old Code/` + `department/old Code/` | Alte Mitarbeiter-/Org-Profile | 5 / 2.717 | TOT | löschbar |
| `admin/product/delivery/oldCode/` | Alte Lieferung-Views | 4 / 1.368 | TOT | löschbar |
| `admin/formula/` (`test.blade` + copy) | Formel-Testansichten | 2 / 1.034 | TOT (test) | löschbar |
| `admin/task/phase/OldCode/` | Alte Phasen-Details/-Management | 2 / 1.008 | TOT | löschbar |
| `admin/chats/Old Codes/` | Alter Chat (`chat.blade copy`, `chat v1.blade.php`) | 2 / 815 | TOT | löschbar |
| `admin/expense/Old/…` | Alte Ausgaben-Typ-Views | 9 / ~1.618 | TOT | löschbar |
| `admin/dashboard/employee/Old/partials/` | Alte Dashboard-Partials | 13 / 1.292 | TOT | löschbar |
| `ids/search_form.blade copy.php` | IDS-Suchformular-Kopie | 1 / 1.025 | TOT (Kopie) | löschbar |
| `admin/problem/…/old code/` | Alte Problem-Views | 2 / 1.277 | TOT | löschbar |
| `ai/chat_show.blade copy.php` | KI-Chat-Kopie | 1 / 348 | TOT (Kopie) | löschbar |
| `admin/breaking-news/calendar/appointment/test.blade(+copy)` | Termin-Testansicht | 2 / 555 | TOT (test) | löschbar |
| `admin/capacity/…copy` | Kapazität-Kopien (`index/terminal.blade copy`) | 2 / 499 | TOT (Kopien) | löschbar |
| `test/test.blade.php` | Leere Test-View | 1 / 0 | TOT | löschbar |
| (weitere Einzel-Kopien: `dashboard/test`, `layouts/test/test2`, `pvgis/… copy`, `master_sets/index.blade copy 1-3`, `todo/todo_checklist copy`, `daily_report/list copy`, `offer/set/old code`, `new_leads/layouts/*copy`, `task/phase/partials/*copy`, `employee/salary/*copy`, `pv.blade copy` …) | Diverse Alt-Stände/Kopien | Rest / Rest | TOT | löschbar |

> Hinweis: Der Ordner `admin/calendar/appointment/` mit `calender.blade.php`, `test.blade copy.php`, `appointment_edit.blade.php` ist laut `git status` bereits **gelöscht** (D) — bereits im Abbau.

---

## 3. Alt-Integrationen Bitrix / NIBE / IMAP (nur Vermerk)

Laut Projekt-Memory **nicht fixen, nicht als Crash zählen**. Nur als Legacy vermerkt — liegen NICHT in `Old/`, sondern im aktiven Baum:

- **Bitrix:** `app/Http/Controllers/BitrixController.php`, `BitrixChatController.php`, `app/Models/BitrixChat.php`, `app/Jobs/ProcessChatChunk.php`, `MessageController.php`.
- **NIBE:** Referenzen in `BitrixController.php`, `app/Http/Controllers/Api/ApiLinkController.php`.
- **IMAP (Webklex):** `app/Http/Controllers/Email/LeadEmailReaderController.php`, `EmailConfigurationController.php`, `LeadEmailAccountsController.php`; Models `LeadEmailAccounts.php`, `EmailConfiguration.php`.

Empfehlung: **behalten / nicht anfassen** (Memory-Vorgabe). Keine Aufräum-Aktion im Rahmen dieser Zone.

---

## 4. Kurz-Fazit — toter Ballast vs. lebendig

- **`app/Http/Controllers/Old/`:** 40 Dateien / **14.805 Zeilen** — **100 % toter Ballast.** Keine einzige Live-Route. 37 Klassen zusätzlich nicht autoloadbar (falscher Namespace, nicht im Classmap). Die einzige „Lebens"-Illusion (`MainAppointmentController` mit 40 web-Treffern) löst sich auf: die Treffer binden an die **echte** Klasse in `…/Appointment/`; die Old-Datei ist Namensdreck.
- **„Lebt trotz Old-Namespace":** **0 Dateien.** Kein Old-Controller ist Route-gebunden UND ladbar.
- **Aktiver Rest-Ballast in `web.php`:** 10 verwaiste `use`-Importe (Z. 148, 153, 159, 186, 187, 188, 363, 364, 370, 387) auf tote Old-Klassen — harmlos (lösen kein Autoload aus), aber mitzuentfernen.
- **Legacy-Views:** ~**194 Dateien / ~43.700 Zeilen** Kopien/Prototypen/„Old Code"-Ordner — durchweg TOT (nicht per `view()`-Name gebunden).

**Gesamter toter Ballast dieser Zone: ~234 Dateien / ~58.500 Zeilen** (≈14,8k Controller + ≈43,7k Views), Aufräum-Empfehlung durchgehend **„löschbar"**. Empfohlene Reihenfolge beim späteren Aufräumen: (1) `Old/`-Ordner + 10 `use`-Zeilen entfernen, (2) `composer dump-autoload -o` neu erzeugen, (3) Legacy-View-Ordner in Blöcken löschen. Bitrix/NIBE/IMAP: **außen vor lassen** (Memory).
