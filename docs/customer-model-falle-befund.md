# Customer-Model-Falle — Untersuchungs-Befund

**Reine Lese-Analyse, nichts geändert/repariert/umgebogen.** Stand: 2026-06-30 · Branch `private/app-code-backup`.
Grundlage: `docs/glossar.md` Abschnitt 5. Ziel: belegen, **welche** Funktionen durch die Falle still ins Leere laufen — als Entscheidungsgrundlage, **nicht** als Reparatur.

> **Kern-Erkenntnis (wichtig & neu):** Die Falle ist **kein simpler Tabellen-Verwechsler.** `customers` und `new_leads` haben **unterschiedliche Spalten**: `customers` nutzt `lat`/`lon` + hat `inquiry_screenshot`; `new_leads` nutzt `latitude`/`longitude` + hat **kein** `inquiry_screenshot`. Das `Customer`-Model + die `customers`-Tabelle sind also ein **halbfertig migriertes älteres Design**. Ein „Fix" ist daher pro Stelle ein **Feld-Mapping**, kein bloßes `Customer::` → `NewLeads::`.

---

## Schema-Divergenz (Grundlage der Bewertung)

| Feld | `new_leads` (echter Kunde) | `customers` (tot, 0 Z.) |
|---|---|---|
| `inquiry_screenshot` | **fehlt** | vorhanden |
| `lat` / `lon` | **fehlt** (heißt `latitude`/`longitude`) | vorhanden |
| `latitude` / `longitude` | vorhanden | fehlt |
| `postcode`, `name`, `lastname` | vorhanden | vorhanden |

→ Code, der `$customer->lat`/`->lon`/`->inquiry_screenshot` erwartet, ist auf das **`customers`-Schema** geschrieben — ein Umbiegen auf `new_leads` erfordert Feld-Anpassung (`lat`→`latitude`, `lon`→`longitude`) bzw. eine fehlende Spalte (`inquiry_screenshot`).

---

## 1. KRITISCH — bricht eine echte, erreichbare Funktion

### 1.1 `ToolsController@weather` :78 + `@weatherman` :150 — **Wetter/PVGIS-Tool crasht (404)**
```php
$customer = Customer::findOrFail($id);          // customers leer -> ModelNotFoundException -> 404
$temperature = Temperature::where('postcode', $customer->postcode)->first();
$lat = $customer->lat;  $lon = $customer->lon;  // 'lat'/'lon' existieren auf new_leads gar nicht
```
- **Zugriff:** Lesen, `findOrFail` → auf leerer Tabelle **Exception/404**.
- **Folge:** Das **Wetter-/PVGIS-Tool für einen Kunden** läuft für jeden echten Kunden (ID liegt in `new_leads`) auf **404**. Genau das Tool, das in der Navi unter „Tools → PV-Planer" verlinkt ist.
- **Fachliche Auswirkung:** Funktion tut nicht „nichts", sondern **bricht sichtbar** — Wetter/PVGIS pro Kunde nicht aufrufbar. **Im Betrieb zu prüfen**, ob das Tool über diese Route erreichbar ist.
- **Auf new_leads umzubiegen?** Ja, aber **nicht** als reiner Swap: `NewLeads::findOrFail` **plus** `lat`→`latitude`, `lon`→`longitude`. *(Vorschlag, nicht ausgeführt; Risiko: Feld-Mapping muss vollständig sein.)*

---

## 2. MITTEL — leeres Ergebnis verfälscht/entwertet eine Funktion (kein Crash)

| # | Stelle | Zugriff | Folge (leeres Ergebnis) | Auf new_leads? |
|---|---|---|---|---|
| 2.1 | `CustomerHeatingCircuitController:87` `Customer::find` | Lesen → `null` | Heizkreis-Tool bekommt keinen Kunden → Folge-Code läuft leer/null | ja (+ Feld-Check) |
| 2.2 | `ChecklistRoomController:138` `Customer::find` | Lesen → `null` | Checklisten-Raum ohne Kundendaten | ja |
| 2.3 | `Product/PurchaseRequestController:40` `Customer::select('id','name','lastname')` | Lesen → leere Liste | **Kunden-Dropdown im Bestellantrag immer leer** | ja (Felder vorhanden) |
| 2.4 | `AdminController:76` `DB::table('customers')…` | Lesen → leer | Admin-Element/Widget zeigt nichts (im Betrieb zu prüfen, welches) | ja |
| 2.5 | `Email/LeadsController:137` `DB::table('customers')…` | Lesen → leer | E-Mail-↔-Kunden-Abgleich findet **nie** einen Treffer | ja |
| 2.6 | `Kanban/LeadOverviewController:2409` `DB::table('customers')->where('id',…)` | Lesen → `null` | Kunden-Nachschlag im Lead-Kanban leer (Anzeige/Fallback prüfen) | ja |

→ Alle **mittel**: kein Crash, aber die jeweilige Funktion arbeitet auf 0 Datensätzen. Exakte fachliche Sichtbarkeit je Stelle **im Betrieb zu prüfen** (ob ein Fallback/anderer Pfad existiert).

---

## 3. HARMLOS — folgenlos (toter Code / ungenutzt / Legacy)

| Stelle | Warum harmlos |
|---|---|
| `NewLeadsController:5520` (Schreib `inquiry_screenshot`) | Liegt in einer `foreach ($customers …)`-Schleife eines **internen Backfill-/Matching-Helfers**; `$customers` aus der **leeren** `customers`-Tabelle → **Schleife läuft nie**. Zudem hat `new_leads` **kein** `inquiry_screenshot`. → Inert; das Feature ist ein Sackgassen-Rest. *(Falls doch geroutet/aufgerufen: dann kritisch — im Betrieb zu prüfen.)* |
| `PVToolsController`, `CustomerPhaseListController` | Importieren `App\Models\Customer`, nutzen aber `CustomerPhaseList::` u. a. — **toter Import**, kein aktiver `Customer::`-Aufruf. |
| `Old/CustomerController`, `Old/CustomerProductController`, `Old/CustomerCartController`, `Old/TaskToDoController`, `Old/AppointmentController` | **Legacy** (`Old/`), 0 aktive Routen (vgl. Kalender-/Begriffs-Doku). |
| Model-Beziehungen `belongsTo(Customer::class)` in `Appointment`, `Product`, `CustomerChecklist`, `CustomerResponsible`, `TaskDocument`, `CustomerPhaseStage`, `PurchaseRequest`, `CustomerChecklistAlbum` | Lösen **gegen die leere `customers`** auf, **falls** traversiert (`$x->customer` → `null`). Meist auf Alt-/Tot-Pfaden; potenziell überall dort still leer, wo ein solches `->customer` gelesen wird. **Breitenrisiko — im Einzelfall zu prüfen.** |

---

## 4. Ist es ein simpler Modell-/Tabellen-Verwechsler — oder mehr?

**Mehr als ein Verwechsler.** Es ist ein **unvollständig abgeschlossener Umzug** von `customers` (älteres Schema: `lat`/`lon`, `inquiry_screenshot`, eigenes Beziehungsgeflecht) auf `new_leads` (aktuelles Schema: `latitude`/`longitude`, kein `inquiry_screenshot`). Folgen:
- Ein pauschales `Customer::` → `NewLeads::` würde an den **Feldnamen** scheitern (`lat`/`lon`/`inquiry_screenshot`).
- Das `Customer`-Model selbst (mit `customer_product_lists`-Beziehung etc.) gehört zum **alten** Datenmodell; ob seine Beziehungen überhaupt aufs neue Modell passen, ist offen.
- Sauber wäre: pro Stelle prüfen, ob sie auf `new_leads` gehört, und **mit Feld-Mapping** umstellen — **nicht** ein globaler Suchen-Ersetzen.

---

## Priorisierte To-do-Liste (Vorschläge — NICHT ausgeführt)

1. **(✅ ERLEDIGT — Commits `168b464` + `0aa2c3c`)** `ToolsController` Wetter/PVGIS → `NewLeads` + Feld-Mapping `lat→latitude`, `lon→longitude` (in **beiden** Methoden `@weather` + `@weatherman`). **Korrektur zum Befund:** der geroutete Pfad `@weatherman` warf **kein 404, sondern 500** (`BadMethodCallException` — die Methode war `private`, eine Route kann sie nicht aufrufen); die Customer-Falle war dahinter verdeckt. `@weatherman` separat auf `public` gesetzt; Verifikation: `GET /get_weather/105` → 200, `NewLeads::findOrFail(105)` liefert echte `latitude/longitude`. Siehe **Nachtrag** unten.
2. **(mittel)** `PurchaseRequestController:40` Kunden-Dropdown → `NewLeads` (leeres Dropdown ist offensichtlich).
3. **(mittel)** `Email/LeadsController:137`, `LeadOverviewController:2409`, `CustomerHeatingCircuit:87`, `ChecklistRoom:138`, `AdminController:76` → je Stelle prüfen + auf `new_leads` mit Feld-Mapping.
4. **(Breitenrisiko)** `belongsTo(Customer::class)`-Beziehungen durchgehen: welche `->customer`-Zugriffe sind live? (eigene Mini-Untersuchung wert).
5. **(harmlos/aufräumen)** `NewLeadsController:5520`-Backfill, tote Importe (PVTools/CustomerPhaseList), `Old/*` → im Zuge der `customers`-Bereinigung (Glossar Abschnitt 4) entfernen.

---

## Nachtrag: verwaiste Wetter-Route (Backlog — KEINE Aktion jetzt)

Beim Falle-Fix (`0aa2c3c`) wurde `ToolsController::weatherman` von `private` auf `public` gesetzt, damit die **registrierte** Route überhaupt aufrufbar ist (vorher 500 `BadMethodCallException`). Dabei festgestellt:
- **`GET /get_weather/{id}` (`name: weather.man`) hat keinen UI-Aufrufer** — in Views/JS nicht auffindbar. Die Route ist jetzt lauffähig, aber **verwaist** (nur per direkter URL erreichbar). Der frühere Hinweis auf `weather_station/station.blade.php:18` war falsch — das ist `weather_stations.upload` (anderes Feature).
- Ebenso **ohne erkennbaren View-Trigger:** `tools.weather` (→ `PVToolsController@getPVData`), `weather.data` (→ `fetchWeatherData`). Die ToolsController-Wetter-Familie wirkt insgesamt **registriert, aber nicht verdrahtet**.
- **`ToolsController@weather`** bleibt **unrouted/verwaist** (eigenständige öffentliche Methode ohne Route).

→ **Backlog-Entscheidung (keine Aktion):** das „Wetter-pro-Kunde"-Feature entweder **verdrahten** (Button/JS auf `get_weather/{id}`), falls gewollt — oder die verwaisten Wetter-Routen **entfernen**, falls nicht. Nur vermerkt.

---

*Reine Analyse. Keine Korrektur, kein Umbiegen, kein Drop, kein Schema-Eingriff. Belege: `Customer::`/`DB::table('customers')`-Fundstellen (app-weit), `SHOW COLUMNS` new_leads vs customers, `app/Models/Customer.php`. Querverweis: `glossar.md` (Abschnitt 4 Aufräum-Reihenfolge, Abschnitt 5 Falle).*
