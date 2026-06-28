# Gleis 1b — weitere anonyme Löcher hinter Auth (Nachweis)

**Stand:** 2026-06-28 · Fixes am **versionierten** Code (Branch `private/app-code-backup`), live wirksam und committet. Nachweis per anonymem `curl` gegen die laufende App.

> Methode bei Routen mit Seiteneffekt (#33, #34, #123, #124): Die `auth`-Middleware blockiert **vor** dem Handler — der „Nachher"-curl löst also **nichts** aus (kein Artisan-Lauf, kein Job-Dispatch, kein DB-Write). Das „Vorher" stammt aus der verifizierten Offen-Lage (siehe `reproduktion.md`), wurde bewusst **nicht** destruktiv erneut ausgelöst.

| Fund | Stelle | Fix | Vorher (verifiziert offen) | Nachher (anonym) |
|---|---|---|---|---|
| **#104** | `LeadEmailAccountsController` (toggle-status/test, `routes/web.php`) | Konstruktor-Guard `$this->middleware('auth')` | außerhalb auth-Gruppe, Controller ohne auth | **302 → /login** (anonymer CSRF-POST, bogus id) ✅ |
| **#123** | `routes/web.php:4514-4515` (dispatch-chat-jobs, chat-jobs) | `->middleware('auth')` je Route | standalone, MessageController ohne auth | **302 → /login** (beide) ✅ |
| **#124** | `routes/web.php` `/run-backfill-phase-sections` | `})->middleware('auth')` | standalone Closure (Artisan) | **302 → /login** ✅ |
| **#33** | `routes/web.php` `/route-cache` | `})->middleware('auth')` | standalone Closure (Artisan) | **302 → /login** ✅ |
| **#34** | `routes/web.php` `/fix-notes` | `})->middleware('auth')` | standalone Closure (DB-Write) | **302 → /login** ✅ |

**Verifikation:** `php -l` (web.php + Controller) OK · `php artisan route:list` parst (2041 Routen) · `/login` = 200 (legitime Nutzung intakt).

## Offen: #115 (öffentliche Selbstregistrierung) — Produktentscheidung
Befund: kein Kundenportal, kein separater Guard; `RegisterController` legt einen normalen `User` an (wie Admin/Mitarbeiter). → Selbstregistrierung ist hier ein Loch, kein Feature. **Empfehlung: komplett deaktivieren** (`Auth::routes(['register' => false])`). Wird mit dem Nutzer abgestimmt, bevor geändert wird.
