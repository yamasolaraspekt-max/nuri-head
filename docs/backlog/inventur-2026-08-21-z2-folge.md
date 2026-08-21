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

### Gegenprobe Z2c (security-reviewer, opus, 21.08.) — A-1..A-4 BESTÄTIGT
`route:list --path=api/planner`: 20 Routen, nur `api` + `Authenticate:sanctum`; `permission:` in
routes/api.php 0×; keine Policy/Gate::before/globaler Scope/`resolveRouteBinding`; Contract-Test
berührt A-1..A-4 nicht. **Radius:** Token für **jeden** Account mit Passwort (kein Rollenfilter;
52 User, 49 Nicht-Admins, 1 Token seit 02.07.); `sanctum.stateful` enthält `ticket.test` → **jede
eingeloggte CRM-Browser-Session** erreicht die API per Cookie. A-2 Upload-Härtung (Typ, Größe,
Pfad, `storage/app`) **grün** — nur Ownership rot. A-4 verschärft: Client-Wert gewinnt (`:130-131`).
Baustein A-1: `directSupervisor`/`resolveReviewer` (`:1978-2018`) + `isSuperAdmin()`.

### A-6 · Bestand (aus Gegenprobe) · `secure.image` liefert jedes Bild nach Auth ohne Ownership
**beleg:** `routes/web.php:1447` Gruppe `['web','auth']`; `ImageController::secureImage` (`:770-785`)
`Image::findOrFail($id)` ohne Bindung. **wirkung:** mittel–hoch (Kundenfotos per ID). **aufwand:** S/M.
Gegenprobe ausstehend (außerhalb A-2 gemessen).

### A-7 · Auth · `users.is_active` wird nirgends geprüft; Token laufen nie ab
**beleg:** `grep -rn "is_active" app/` → nur Fillable/Cast (`User.php:18,30`); `config('sanctum.expiration')`
= NULL; `PlannerApiAuthController:50-80` prüft nur Passwort. **erklaerung:** ein deaktivierter
Ex-Mitarbeiter zieht sich weiter einen Dauer-Token — und ob der **Web-Login** `is_active` prüft,
ist **nicht gemessen** (Messauftrag, nicht annehmen). **Y-10:** Token-Ablaufzeit (Bedienfolge Nuriva).
Entwarnung: Selbstregistrierung ist zu (`Auth::routes(['register'=>false])`).

### Messung A-6/A-7 + Sanctum (security-reviewer, 21.08.) — Ergebnisse
**A-7 präzisiert — `is_active` ist KEIN Kontostatus, sondern ein Online-Flag:** `LogUserLogin.php:26`
setzt bei jedem Login `is_active=1`, `LogUserLogout.php:16` bei Logout `0` (EventServiceProvider:22-27).
Web-Login (`LoginController` nur Trait, kein `credentials()`), laufende Session (keine Middleware)
und `PlannerApiAuthController@token` prüfen es **nie** — einzig `MobileAuthController:68-73` prüft es
(und sperrt dadurch fälschlich Nutzer nach regulärem Web-Logout: „Benutzer ist nicht aktiv"). Die UI
behauptet Kontostatus (`user_view.blade.php:38` Active/Deactivated; Schreibpfade `UserController:518-530,
734-750`). **Reproduktion:** Admin deaktiviert X → X loggt sich ein → gelingt, Flag springt auf 1.
**`logOffUser` (`UserController:449-455`) ist vollständig wirkungslos:** liest `session('user_session_'.$userId)`
der eigenen Admin-Session (immer null für fremde ID) und `sessions`-Tabelle existiert nicht
(`session.driver=file`). **wirkung:** HOCH (Auth: Entlassene bleiben drin, unabhängig von der
Rechte-Politik). **→ W0-9.**
**A-6 bestätigt ROT:** `secure.image` (`routes/web.php:1463-1464`, `ImageController:770-785`
`findOrFail` ohne Bindung) + Geschwister `secureDownloadScreenshot` (`:753-767`, findOrFail VOR
auth-check) + `secureImageByFilename` (`:787-819`); `images.customer_id` → `new_leads` existiert, wird
nicht benutzt; 7 Produktiv-Erzeuger kennen den Kunden an der Aufrufstelle. Lokal 31 von 52 Nutzern
ohne Customer-Recht → unter Yamas Entscheidung (alle Rechte) heute faktisch 0, **strukturell weiter
offen → W0-8.** Pfad-Injektion grün (`basename`); Dateinamen nicht ratbar, IDs schon.
**Sanctum:** `expiration=null` (`config/sanctum.php:49`), **keine** Bereinigung (kein
`sanctum:prune-expired` im Scheduler), 1 Token seit 02.07. ungenutzt, Admin hat kein Widerrufsmittel
(`logout-all` ist Selbstbedienung) — **Y-10** Token-Laufzeit (Operand Stunden). `stateful` enthält
`ticket.test` (Default aus APP_URL), `EnsureFrontendRequestsAreStateful` erste Position der
api-Gruppe; CSRF greift (11 Ausnahmen in `VerifyCsrfToken:14-29`, u.a. `api/reminder/*/status`);
Cookie ohne Referer/Origin → 401 (grün). **0 von 59 API-Routen tragen `permission:`**; 35 per
Browser-Cookie erreichbar.
**A-8 NEU (Messauftrag):** `api/secure/master-sets`, `/{id}`, `/master-sets-debug` tragen **keine
Authentifizierung** (nur `throttle:60,1`), nur am Middleware-Stack gemessen — **Inhalt nicht geprüft**.
**A-9 NEU:** `ImageController.php:30` Upload-Regel `file.*` ohne `max:` (andere Pfade 25–50 MB) — GELB, S.

### Messung A-8 (security-reviewer, 21.08.) — KORREKTUR + zwei echte Punkte
**A-8 zurückgenommen als Auth-Loch:** `api/secure/master-sets*` sperrt in `MasterSetApiController::authApi()`
(`:26-79`) als erste Anweisung jeder Methode; live anonym → **401**; Vergleich `hash_equals` (`:64-65`),
kein fester String. Der alte Fund #116 (`software-audit/fix-ledger.md:122`) war ein falscher Alarm
(`reproduktion.md:56` führte ihn schon so). **Offen (GELB/ROT):** Secret wird auch aus dem
**Query-String** angenommen (`:28-34`, live bestätigt → Passwort im Access-Log) · `env()` statt
`config()` (`:36-37`; unter `config:cache` → 500, Schnittstelle in Produktion latent tot; kein
`config/`-Eintrag, Hausmuster `services.php:34-36`) · keine Protokollierung von Fehlversuchen bei
`throttle:60,1` · Payload = EK-Preise/Margen/Skonto/Lieferantenkonditionen + Stundensätze +
**Mitarbeiter-Klarnamen/Fotos** (`loadComponents:649ff`, `loadLabor:932-1041`) · **Debug-Endpunkt ROT:**
Schema/Spalten/10 Rohzeilen + ungegateter Exception-Text (`:388-393`, `index/show` gaten auf
`app.debug`) · `with_deleted=1` (`:262`) post-auth. **Kein Konsument im Repo** (Nuriva nutzt
`/api/mobile/*` + `/api/planner/*`) → **Y-11.** → **W0-10.**

### A-10 NEU · CSRF · `POST ids/callback` in den Ausnahmen — Fremdzuschreibung per Auto-Submit
**beleg:** `VerifyCsrfToken.php:14-29` nimmt `ids/callback` aus; Route hinter `Authenticate`;
`IdsController@callback:33-78` legt `ImportedIdsItem` an, `?auto=1` → `autoPromoteItem():89ff` →
`Distributor::firstOrCreate('GC Online')` + Produktanlage; `uid` frei aus Query (`:56`), keine
Signatur/kein Nonce. **erklaerung:** eingeloggtes Opfer + fremde Seite mit Auto-Submit-Form →
Import + Auto-Promotion unter fremder `uid`. **wirkung:** ROT (Integrität, Produktstamm) → **W0-11.**
Dazu: `POST ai/chats/{chat}/message` ohne Token (Kosten, niedriger); `supplier-connectors/.../return`
GELB by design ohne State-Parameter; **5 von 11 Ausnahmen sind tot** (Pfade `api/reminder/*/status`,
`api/due-personal-notes`, `ids/search/callback`, `/ids/receive`, `/ids/callback` treffen keine Route).

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
