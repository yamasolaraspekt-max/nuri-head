# INVENTUR Z2 — Folgeläufe Z2b (`/admin/energie/*`) und Z2c (`api/planner/*`, Nuriva-Mobile-API), 21.08.2026

> Fach: [Backlog](REGISTER.md) · Vorgänger: [inventur-2026-08-21-z2.md](inventur-2026-08-21-z2.md)
> **Z2c-Vorbestand:** keiner — `docs/audit/p1-idor-inventur.md` schließt Nuriva **ausdrücklich als TABU**
> aus der 232er-Zählung aus; alle fünf Z2c-Befunde sind Erstfunde. Gruppenrahmen gemessen:
> `routes/api.php:231-253` — öffentlich nur `POST /auth/token` (`throttle:10,1`, korrekt), alles
> Übrige `auth:sanctum` **ohne** Ability-/Permission-Gate.

## Z2c · `api/planner/*` — fünf Erstfunde (routing-finder, Controller-Rümpfe gelesen)

### A-1 · IDOR lesen · jeder Planner-Token liest Arbeit + GPS jedes Mitarbeiters
**beleg:** `routes/api.php:281-287` `GET /employees/{employee}/work|day-report` →
`PlannerEmployeeApiController@employeeWork` (`:37-40`) / `@employeeDayReport` (`:62-65`) reichen
`{employee}` (reine `whereNumber`-ID) ungeprüft durch; Antwort enthält `latest_location` (lat/lng,
`:836-858`, `:905-931`). Docblock `:33-36` sagt „Manager/admin endpoint" — **die Prüfung dazu fehlt**;
2375 Zeilen, 0 Treffer für is_admin/hasPermission/Gate/authorize/role. **erklaerung:**
Bewegungsprofile von Kollegen per durchzählbarer ID, ohne Zuständigkeit; Missbrauch unbemerkt.
**erledigt_wenn:** `{employee} == authEmployeeId()` ODER Aufrufer in der Vorgesetztenkette
(Baustein `resolveReviewer` im selben Controller vorhanden) ODER Admin; Test „fremder → 403".
**aufwand:** M · **wirkung:** HOCH (PII/Standort, DSGVO/Betriebsrat)

### A-2 · IDOR lesen+schreiben · Kundenfotos jeder `customer_id`
**beleg:** `routes/api.php:318-322` → `PlannerMobileCustomerImageController@upload` (`:26-161`) /
`@index` (`:166-224`); `customer_id` aus Request, nur `exists:new_leads,id` (`:36`); `index` filtert
nur nach Client-`customer_id` (`:178`), `upload` hängt an jede `customer_id` (`:102`).
**erklaerung:** Fotos jedes Kunden lesbar, Bilder in fremde Kundenakten unterschiebbar.
**erledigt_wenn:** `customer_id` gehört zu einem Planner-Item des `authEmployeeId()` — Muster
`whereExists(planner_item_employees…)` aus `completeItemWithReport()` (`PlannerEmployeeApiController.php:1726-1731`).
**aufwand:** M · **wirkung:** HOCH

### A-3 · IDOR schreiben · Master-Sets an fremde Pläne/Items
**beleg:** `routes/api.php:340-353` → `PlannerMasterSetController@link/unlink/addToPlan`
(`:175-228`), reines Route-Model-Binding ohne Scope; `addToPlan` legt neues Item in fremdem Plan an
(`:215-225`). **erklaerung:** fremde Baustellen-Pläne verfälschbar (Material taucht in fremden
Projekten auf). **erledigt_wenn:** Zuständigkeitsprüfung wie A-2; Test „fremder Plan → 403". **aufwand:** M

### A-4 · IDOR + Spoofing · Materialanfragen auf fremde Items, Melder frei wählbar
**beleg:** `routes/api.php:356-362` → `PlannerItemMaterialController@index/store`; kein
Ownership; `requested_by_employee_id` `['nullable','integer']` (`:95`) vom Client — obwohl
`authEmployeeId()` im selben Controller existiert (`:574`). **erklaerung:** Beschaffungs-Workflow
(`:479-536`, Benachrichtigung `pm_employee_id`) auf fremde Items, Zurechenbarkeit gebrochen.
**erledigt_wenn:** `index` prüft Zuständigkeit; `store` setzt Melder serverseitig. **aufwand:** S

### A-5 · Kausalität · Token-Abilities vergeben, nirgends geprüft
**beleg:** `PlannerApiAuthController@token` (`routes/api.php:75-80`) vergibt `planner:read|write|
attendance|kanban`; `grep tokenCan|ability:|abilities` in routes/api.php + Planner-Controllern +
Middleware → **0 Treffer**; Gruppe prüft nur `auth:sanctum` (`:253`). **erklaerung:** Anschein
einer Rechteabstufung ohne Durchsetzung; ein eingeschränkter Token bliebe voll schreibfähig.
**erledigt_wenn:** Abilities per `ability:`-Middleware je Untergruppe durchgesetzt ODER aus
`createToken()` entfernt — Code und Zusage stimmen überein. **aufwand:** S

**Z2c nicht geprüft:** `PlannerEmployeeApiController` jenseits der genannten Methoden;
`PlannerItemMaterialController` :100-610 (Accept/Reject); `config/sanctum.php` Token-Ablauf;
`secure.image`-Auslieferung (eigener Anschlussfund möglich); `/api/mobile/*`, `/api/secure/master-sets/*`
(eigener `authApi()`-Mechanismus, außerhalb Zone).

## Z2b · `/admin/energie/*` — zwei Befunde (routing-finder, alle 8 Controller gelesen)

### E-1 · Rückfrage · 17 Rechner-Routen ohne `permission:`, aber OHNE Kundendaten-Zugriff
**beleg:** `routes/web.php:5621-5693` nur `auth`; sechs Controller (EnergieAuslegung, Sanierung,
Energiekonzept, Heizlast, FussbodenCheck, Materialliste) 0 Treffer für permission/authorize/Gate.
**ABER:** keine Route mit `{id}`-Parameter (`route:list` bestätigt); Sanierung/Energiekonzept/Heizlast
bauen `HeizlastProjekt` **transient** in einer Transaktion und löschen es (`SanierungController:161-214`,
`EnergiekonzeptController:354-409`, `HeizlastController:145-179`); Rest rechnet aus Request gegen
Katalog. **Keine Ownership-Lücke wie S-2.** Strukturell verletzt die Bauordnung „kein Endpunkt
ohne", Nachbargruppe Hausplaner hat das Gate. **⚠ Y-7:** bewusst offen (reine Rechner, keine PII)
oder vergessenes Gate? Falls Bindung: Gruppen-Middleware `permission:Energie,read` um den Block.
**aufwand:** S · **wirkung:** niedrig

### E-2 · Workflow · Fehlermeldung bei WR-/WP-Dokument verschwindet (toter Redirect)
**beleg:** `EnergieAuslegungController.php:136-138` und `:339-341` `redirect()->route(…)->with('error', …)`;
Zielviews `wr_auslegung.blade.php`/`wp_auslegung.blade.php` und `admin.layouts.app` lesen
`session('error')` **nie** (0 Treffer; 14 andere Views tun es); `berechnen()` (`:73-81`) nutzt
korrekt `'fehler' => …` inline. **erklaerung:** Klick → Redirect → leere Seite ohne Erklärung,
obwohl der Kommentar `:118` „mit Fehlermeldung" verspricht. **erledigt_wenn:** Zielview zeigt
`session('error')` ODER Inline-Return wie `berechnen()`. **aufwand:** S · **wirkung:** mittel (Bedienung)

**Z2b bestätigt, kein Neufund:** S-2 (GrundrissController) erneut gemessen; PlanUploadController
Positiv-Referenz; 27 Routen ohne Duplikate; Sidebar deckungsgleich.
