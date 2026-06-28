# Gleis 1 — die 6 anonym ausnutzbaren Löcher geschlossen (mit Nachweis)

**Stand:** 2026-06-28 · **Modus:** live an der laufenden App (App-Code ist per `.gitignore` nicht versioniert — siehe Hinweis unten). Jeder Fix wurde per anonymem `curl` (ohne Session-Cookie) vorher/nachher geprüft.

> ⚠️ **Versionierungs-Hinweis:** `app/` (886 Dateien) und `routes/` sind komplett gitignored — die Fixes sind **live wirksam**, aber **nicht in Git committet** (so von dir entschieden). Dieser Nachweis (in `docs/`, getrackt) ist die Dokumentation der Änderungen.

## Nachweis-Tabelle (anonym, ohne Login)

| Fund | Datei / Stelle | Fix | Vorher | Nachher |
|---|---|---|---|---|
| **#20** | `routes/web.php:599` | `…getEmailDetails])->middleware('auth')` (route-level, da WebsiteController gemischt öffentlich/privat) | 404 (offen) | **302 → /login** ✅ |
| **#23** | `routes/web.php` all-contacts-Gruppe | `Route::middleware(['web','auth'])->group(…)` | `/export` = **200 + CSV** | **302 → /login** ✅ |
| **#26** | `routes/web.php` `leaves`-Prefix | `Route::prefix('leaves')->middleware(['web','auth'])->group(…)` | 404 (offen) | **302 → /login** ✅ |
| **#29** | `routes/web.php` 2 `web`-Gruppen (Handover/RequestOut + Purchase) | `['middleware' => ['web','auth']]` | 500 (offen) | **302 → /login** ✅ |
| **#30** | `routes/web.php` goods-receipts-Gruppe | `Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(…)` | 500 (offen) | **302 → /login** ✅ |
| **#31** | `app/Http/Controllers/Customer/BegFundingsController.php` | `__construct(){ $this->middleware('auth'); }` (Konstruktor-Guard, wie Product-/NewLeadsController) | 500 (offen) | **302 → /login** ✅ |

## Gegencheck #23 — legitime Nutzung bleibt erhalten
- **Eingeloggt** (`yama.solaraspekt@gmail.com`): `GET /all-contacts/export` → **HTTP 200**, `Content-Type: text/csv`, Header `Name,Nachname,Typ,Telefon,Email,Adresse`. ✅ Der Export funktioniert für authentifizierte Nutzer unverändert; nur der anonyme Zugriff ist gesperrt.

## Syntax
`php -l routes/web.php` und `php -l …/BegFundingsController.php` → **No syntax errors**.

## Ergebnis
Alle 6 dynamisch bestätigten anonymen Löcher antworten jetzt nachweislich **gesperrt (302 → /login)**, die legitime eingeloggte Nutzung (#23-Export) bleibt funktionsfähig.
