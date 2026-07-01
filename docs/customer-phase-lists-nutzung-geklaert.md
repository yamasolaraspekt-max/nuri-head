# customer_phase_lists — Nutzung geklärt: FAKTISCH TOT (live bewiesen)

> **Reine Analyse (nur Lesen), keine Änderung.** Klärt die zwei offenen Fragen aus `customer-phase-lists-abloese-befund.md` (Spalten-Mismatch + Menü-Verlinkung), um zu entscheiden, ob das Feature faktisch tot ist.
>
> **⚠️ ERGEBNIS — FAKTISCH TOT, live bewiesen.** Der gesamte Code liest UND schreibt gegen **nicht-existente Spalten** (`customer`/`product`/`alternative`) — das echte Schema hat `customer_id`/`product_id`/`alternative_id`. **Jeder Read UND jeder Write wirft `SQLSTATE[42S22] Unknown column`** (live gegen die echte DB reproduziert). Das Feature kann **nichts speichern und nichts anzeigen** — deshalb ist die Tabelle leer (0 Zeilen sind die *logische Folge*, nicht Zufall). **Das korrigiert auch meinen eigenen Vorbefund:** „code-lebendig, voll funktionsfähiges Feature" war zu stark — der Code ist zwar **verdrahtet** (Routen/Views), aber **funktional kaputt**. Ablösung ist damit **risikoarm** (man kann nichts brechen, was bei jedem Aufruf crasht und 0 Daten hat).

---

## 1. Spalten-Mismatch — AUFGELÖST (mit Live-Crash-Beweis)

**Nur EINE Migration, keine ALTER:** `grep customer_phase_lists database/migrations/` → **nur** `2025_04_01_091560_create_customer_phase_lists_table.php`. **Keine Umbenennungs-Migration.**

**Echtes DB-Schema (`DESCRIBE customer_phase_lists`, wörtlich):**
```
id, project_id, customer_id, checklist_id, phase_id, activities_id, product_id,
service_id, department_id, service, alternative_id, parent_id, contact_person,
responsible_person, outside_service, outside_company, color, active_by,
jump_steps, jump_steps_by, done, type, main_id, outside_type, done_date,
reason, done_status, status, work_progress, more_time, total_time, created_at, updated_at
```
→ **`customer` = NEIN · `customer_id` = JA** | **`product` = NEIN · `product_id` = JA** | **`alternative` = NEIN · `alternative_id` = JA**.

**Der Code nutzt durchgängig die NICHT-existenten Kurz-Spalten:**
- Reads: **14 Vorkommen** von `customer_phase_lists.(customer|product|alternative)` (8× `CustomerPhaseListController`, 6× `NewLeadsController`), **0 Vorkommen** von `.customer_id`/`.product_id`/`.alternative_id`.
- Write: `CustomerPhaseListController` `create()`-Payload (`:111`) schreibt `'customer' => … 'product' => … 'alternative' => …` (wörtlich aus dem Payload).

**LIVE-BEWEIS gegen die echte DB (beide Pfade crashen):**
```
READ  (get_phase-Query):  SQLSTATE[42S22]: Column not found: 1054
                          Unknown column 'customer_phase_lists.customer' in 'where clause'
WRITE (create-Insert):    SQLSTATE[42S22]: Column not found: 1054
                          Unknown column 'customer' in 'field list'
                          → Zeilen customer_phase_lists nach Test: 0 (nichts geschrieben)
```

**URTEIL Frage 1: Die Reads UND Writes laufen gegen NICHT-existente Spalten → sie CRASHEN (SQLSTATE 42S22), nicht „leer".** Das Feature ist **faktisch kaputt**. *(Zusatzbefund: `create()` setzt zudem die NOT-NULL-Spalten `activities_id`/`done`/`type` nicht — selbst bei passenden Spaltennamen würde der Insert scheitern. Doppelt kaputt.)*

## 2. Menü-/Einstiegs-Verlinkung — geklärt

**Der einzige LEBENDE Einstiegspunkt:** `resources/views/admin/new_leads/customer_object_profile.blade.php:3079` (Objekt-Profil, live geroutet via `web.php:812` `new.lead.profile.object`):
```js
$(document).on('click', '.files', function () {
    ...
    const url = `/customer/phase/get/${customerId}/${alternativeId}/${productId}`;   // :3079
    // Update hidden input fields with the fetched data
});
```
→ Ein Klick auf `.files` im Objekt-Profil ruft `get_phase` (`lead.phase.get.status`) auf — **und dieser Endpoint crasht** (§1, Read-Crash). Die „hidden input fields" werden also **nie** mit Phasen-Daten gefüllt. *(NICHT VERIFIZIERT: ob das JS den 500-Fehler abfängt oder sichtbar bricht — funktional lädt jedenfalls keine Phase.)*

**Die Phase-Management-UI (`phase_management/manage` + `customer_phase`) hat KEINEN lebenden Menü-Link:** `grep customer_phase_manage|customer.phase.managment.show` in allen lebenden Views → **kein Menü-/Sidebar-Eintrag**; der einzige Treffer (`manage.blade.php:537`) ist ein **interner** Delete-Aufruf INNERHALB der Manage-UI selbst, kein Einstieg. Die zwei UI-Seiten werden nur gerendert, wenn man die Route direkt trifft — es führt **kein lebendes Menü** dorthin *(NICHT VERIFIZIERT für ALLE Menü-Orte — grep über layouts/ + new_leads/ + resources/views/ ohne old/copy).*

**URTEIL Frage 2:** Ein Nutzer erreicht das Feature praktisch nur über den `.files`-Klick im Objekt-Profil → der aber auf einen crashenden Endpoint zeigt. Zur Phase-Management-UI führt kein lebendes Menü. **Faktisch nicht nutzbar.**

## 3. Gesamturteil

**`customer_phase_lists` ist FAKTISCH TOT** — nicht nur daten-leer, sondern **funktional kaputt**:
1. **Jeder Code-Pfad crasht** (Read + Write, SQLSTATE 42S22 „Unknown column", live reproduziert) — der Code wurde gegen ein anderes Spalten-Schema (`customer`/`product`/`alternative`) geschrieben als die Migration anlegt (`*_id`); es gibt **keine** ALTER-Migration, die das heilt.
2. **DB 0 Zeilen** — die *logische Folge*, weil `create()` immer crasht (nie befüllbar).
3. **Der einzige lebende Einstieg** (Objekt-Profil `.files`) ruft einen crashenden Endpoint; **kein Menü** zur Management-UI.
4. **Nuriva/API: 0** (bestätigt, §Vorbefund).

→ **Ablösung ist risikoarm.** Man kann ein Feature, das bei jedem Read/Write crasht und keine Daten hat, nicht „kaputt machen". Es gibt sogar **nichts zu „stilllegen"** im Sinne von „aufhören zu füttern" — das Füttern (`create`) scheitert ohnehin bei jedem Versuch. Der einzige heute sichtbare Effekt ist eine **fehlschlagende AJAX-Anfrage** beim `.files`-Klick im Objekt-Profil (500-Fehler, keine Phasen-Anzeige).

**Konsequenz für den Ablöse-Plan (Vorschlag, NICHT umgesetzt):** Da nichts „gefüttert" wird, entfällt der reversible „Schreibstelle stilllegen"-Schritt faktisch. Der sinnvolle Schritt ist ein **Cleanup des toten Codes** (später, eigener Pflicht-Stopp): `CustomerPhaseListController` + 9 Routen + `NewLeadsController::get_phase` + der `.files`-Handler im Objekt-Profil + die 2 Views + `TaskPhase::customerPhaseList`-Relation + Model + Tabelle. **Reihenfolge-Empfehlung:** zuerst den lebenden **`.files`-Aufruf im Objekt-Profil entfernen** (beseitigt den einzigen sichtbaren 500-Fehler, verhaltensneutral, da er eh nur crasht), dann der Rest. Der Tabellen-Drop bleibt der letzte, unumkehrbare Schritt.

**Ehrliche Korrektur (zweifach):**
- Der *ursprüngliche* Befund („fast dormant, 1 Write/0 Leser, billig") war doppelt ungenau: es gibt viele R/W-Stellen — ABER sie funktionieren alle nicht.
- Mein *eigener* Vorbefund („code-lebendig, voll funktionsfähiges Feature") war zu stark: verdrahtet ≠ funktionsfähig. Das Feature ist **verdrahtet, aber kaputt**. Netto stimmt die Ablöse-Richtung des Ursprungsbefunds („risikoarm") — nur aus einem *anderen* Grund (kaputt statt dormant).

---

## Gelesen / NICHT gelesen (ehrlich)

**Vollständig geprüft/bewiesen:** `DESCRIBE customer_phase_lists` (echtes Schema, 34 Spalten); alle Migrationen mit `customer_phase_lists` (nur 1 create, keine ALTER); Read-Spalten-Zählung (14× kurz, 0× `_id`); `create()`-Payload (`:111`, schreibt `customer`/`product`/`alternative`); **Live-Ausführung** der get_phase-Query (Read-Crash) UND eines Inserts (Write-Crash), beide SQLSTATE 42S22, Tabelle danach 0; `customer_object_profile.blade.php:3079` (`.files`-Klick → get_phase) + dessen Live-Route (`web.php:812`); Menü-Grep (kein lebender Einstieg zu `customer_phase_manage`).

**Nur gegrept / NICHT VERIFIZIERT:**
- Ob das JS im Objekt-Profil den 500-Fehler **abfängt** (graceful) oder die `.files`-Interaktion sichtbar bricht — nicht im Browser getestet.
- Ob es **irgendwo** doch einen Menü-Link zu `customer_phase_manage` gibt, den mein Grep (layouts/ + new_leads/ + views ohne old/copy) verfehlt hat — unwahrscheinlich, aber nicht 100 %.
- `TaskPhase::customerPhaseList`-Relation: dynamische Nutzung weiterhin nicht 100 % ausgeschlossen (würde bei Nutzung ebenfalls gegen `phase_id` laufen — das existiert; aber die Relation selbst wird nirgends aufgerufen, s. Vorbefund).

## Schwächen dieser Analyse (Selbstkritik)
- Der Crash-Beweis ist **stark** (live gegen die echte DB reproduziert), aber gegen **meine lokale Schema-Version** — falls Produktion ein anderes (repariertes) Schema hätte, sähe es dort anders aus. **NICHT VERIFIZIERT** für Produktion (kein Zugriff); die eine Migration im Repo ist aber die einzige Quelle der Wahrheit im Code → Produktion sollte identisch sein.
- „Faktisch tot" ist ein starkes Urteil — es beruht darauf, dass *jeder* geprüfte Pfad crasht. Ich habe die **Haupt**-Pfade (get_phase, getPhase, create) geprüft; theoretisch könnte ein Pfad `*_id`-Spalten nutzen (Grep sagt: keiner, 0 Treffer) — daher sehr unwahrscheinlich, aber die Aussage steht auf „alle *geprüften* + 0 `_id`-Treffer".

---

*Reine Analyse — nichts geändert. Belege: `DESCRIBE customer_phase_lists` (customer_id/product_id/alternative_id vorhanden, customer/product/alternative NICHT); Migrationen-Grep (nur create 2025_04_01, keine ALTER); Read-Spalten 14× kurz / 0× _id (`CustomerPhaseListController` + `NewLeadsController`); `create()`-Payload:111; Live-Crash READ (SQLSTATE 42S22 „Unknown column 'customer_phase_lists.customer'") + WRITE (SQLSTATE 42S22 „Unknown column 'customer'", Tabelle danach 0 Zeilen); `customer_object_profile.blade.php:3079` (.files→get_phase), Route `web.php:812`; Menü-Grep (kein lebender Einstieg, nur interner Delete manage.blade:537). Querverweis: `customer-phase-lists-abloese-befund.md` (Vorbefund, hier korrigiert), `architektur-entscheidungen.md` (Weiche 6), `glossar.md`.*
