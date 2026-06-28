# Nav-Audit → Tier C (echte Bugs: Crash / Fatal / falsches Speichern)

**Stand:** 2026-06-28 · Aus dem Navigations-Konzept-Audit herausgelöste **echte Bugs**. Ergänzt die Tier-C-Crash-Liste (`arbeitsliste-nach-ausnutzbarkeit.md` / `befunde-bestaetigt.csv`). **Reine Doku — kein Code geändert.**

## Neu (im Software-Audit noch nicht erfasst)
| # | Bug | Wirkung | Beleg |
|---|---|---|---|
| N-C1 | **Urlaubsanspruch**: View nutzt `action()` mit falschem Namespace `App\Http\Controllers\LeaveDayController` (richtig: `Employee\Profile\LeaveDayController`) | **Alle Formulare (Anlegen/Bearbeiten) werfen 404/Exception** | `resources/views/admin/employee/holiday/leave_day.blade.php` |
| N-C2 | **Dashboard**: `loadTabContent` ruft `$this->getProjects()` und `$this->getOffers()` — Methoden existieren nicht | **PHP-Fatal beim Öffnen der Tabs „Projekte"/„Angebote"** | `EmployeeDashboardController:421/423` |
| N-C3 | **Rabattgruppen**: `store()`/`update()` redirecten auf Route `discount.group.info`, die nicht existiert (richtig: `discount_group.info`) | **RouteNotFoundException nach Anlegen/Bearbeiten** | Discount-Group-Controller |
| N-C4 | **Checklisten-Formulare**: `getProduct()` referenziert undefinierte Variable `$formula` | **Fatal Error** | ProductFormula-Controller |
| N-C5 | **Übergaben**: `elseif($request->request_type="add")` — Zuweisung statt Vergleich (`=` statt `==`) | Falsche Logik / falsches Speichern | `HandoverController@old_update` |
| N-C6 | **Wissensdatenbank**: Route-URL-Tippfehler `knowlege` statt `knowledge`; `edit()` gibt leeres `//` zurück | Falscher/verwirrender Pfad; kein echtes Edit | `routes/web.php:4535`, Knowledge-Controller |
| N-C7 | **Persönliche Notizen**: `initNoteSelect2()` außerhalb von `$(document).ready()` referenziert → **ReferenceError**; `add_category`-Button ohne Handler (tot) | JS-Fehler beim ersten Öffnen; toter Button | `resources/views/admin/notes/...` (Notiz-Modal) |

## Bereits im Software-Audit getrackt (Dubletten — nicht doppelt bearbeiten)
| Bug | Verweis |
|---|---|
| **BEG-Förderungen**: `BegFunding::create()` (Klasse heißt `BegFundings`) → Fatal beim Speichern. **Zusatz aus Nav-Audit:** `create()`/`edit()`-Views sind **leere 0-Zeilen-Dateien**, `show()` fehlt im Controller. | = Software-Audit-Crash (`BegFundingsController:49`) |
| **Externe Firmen**: „Neu"-Formular postet an `external.update` statt `external.store` → Anlegen schlägt fehl/überschreibt. | = Software-Audit (`external.blade.php`) |
| **Dashboard**: `getTabCounts()` nutzt undefinierte Klassen `Task`/`Appointment`. | = Software-Audit (`EmployeeDashboardController:683-685`) |
| **Ratenzahlungen**: Erfolgsmeldung auch im Fehler-Zweig; fehlerhafter OR-Query (Cross-Type-Treffer). | = Software-Audit (`AssetInstallmentController:94/109`) |

**Summe Tier-C-Bugs aus Nav-Audit: 11** (7 neu + 4 Dubletten).
