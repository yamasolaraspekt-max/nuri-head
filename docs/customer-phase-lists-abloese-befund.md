# customer_phase_lists ablösen — Kartierung (harte Verifikation der Dormanz-Annahme)

> **Reine Analyse (nur Lesen), keine Änderung. Pflicht-Stopp.** Erste Stufe des Ablöse-Vorhabens: vollständig kartieren, was `customer_phase_lists` heute wirklich berührt, BEVOR etwas geändert wird. „Ablösen" = aufhören zu füttern (Schreibpfade stilllegen), NICHT Tabelle droppen.
>
> **⚠️ KERNBEFUND — die „fast dormant"-Annahme ist FALSCH.** Der frühere Befund (`architektur-bewertung-zweitmeinung.md`: „1 Schreibstelle, 0 Leser, fast dormant, billig ablösbar") stimmt **nicht**. `customer_phase_lists` ist ein **code-LEBENDIGES, voll verdrahtetes Phasen-Management-Feature**: **9 aktive Routen**, **2 gerenderte UI-Seiten**, **mehrere Leser UND Schreiber**, **3 Views rufen die Routen aktiv auf**. Es ist **nur daten-leer** (DB 0 Zeilen), NICHT code-tot. **Ablösen ist deutlich mehr als „die eine Schreibstelle stilllegen".** Nuriva/API sind nicht betroffen (bestätigt).

---

## 1. Schreibstellen (alle, wörtlich)

Alle Schreiber liegen in **`CustomerPhaseListController`** und werden **manuell über die UI** ausgelöst — **kein automatischer Feeder** (kein Job/Command/Seeder/Observer; grep über `app/` + `database/` bestätigt):

| Aktion | Code | Route |
|---|---|---|
| **Anlegen** | `:111` `CustomerPhaseList::create([...])` (in `store()` `:92`) | POST `/customer_phase_management_store` (`web.php:1463`) |
| **Löschen** | `:190` `CustomerPhaseList::findOrFail($id)->delete()` (in `deletePhase()` `:188`) | DELETE `/customer_phase_management_delete/{id}` (`:1465`) |
| **Ändern (Farbe/Status)** | `:201` `$entry = CustomerPhaseList::findOrFail($request->id);` (in `color()` `:194`) | POST `/customer_phase_management/color` (`:1464`) |

→ **Nicht „1 Schreibstelle", sondern 3 Schreib-Aktionen** (create/delete/update) — alle manuell, keine automatische Befüllung. *(Das erklärt die leere DB: das Feature füllt sich nur, wenn jemand die UI bedient.)*

## 2. Lesestellen (alle, wörtlich) — NICHT 0

**In `CustomerPhaseListController`:**
- `:37` `DB::table('customer_phase_lists')->…` (Existenz-Check in `create()`).
- `:72` `->leftJoin('customer_phase_lists as list', …)` + `:78` `->whereNull('list.phase_id')` (Vorlagen-Auswahl).
- `getPhase()` `:134` → `:139` `DB::table('customer_phase_lists')->join(new_leads/article_groups/task_phases)->where(customer/product/service/alternative)` (JSON-Leser).
- `getPhaseNew()` `:165` → `:168` `DB::table('customer_phase_lists')->…` (JSON-Leser).
- `show()` `:209` → `:214` `DB::table('customer_phase_lists')->join(...)->select(...GROUP_CONCAT(phase_id))` → rendert View.

**In `NewLeadsController`:** `get_phase()` `:5750` → `:5753` `DB::table('customer_phase_lists')->join('task_phases',…)->where('customer'…)->…->get()` → `return response()->json($data, 200)` (`:5755`-Bereich). **Route:** `GET /customer/phase/get/{customer}/{alternative}/{product}` → `lead.phase.get.status` (`web.php:853`). → **Ein eigenständiger, live gerouteter JSON-Leser außerhalb des Phase-Controllers.**

→ **„0 Leser" ist falsch:** mindestens **5 Lesestellen** im Phase-Controller + **1 in NewLeadsController** (get_phase).

## 3. Modell / Migrationen / Beziehungen

- **Model:** `app/Models/CustomerPhaseList.php` (`:8`).
- **Relation:** `app/Models/TaskPhase.php:51–53` `public function customerPhaseList() { return $this->hasOne(CustomerPhaseList::class, 'phase_id'); }`. → **Definiert**, aber **kein Aufrufer** von `->customerPhaseList()` gefunden (grep: nur die Definition; die `$phaseList`-Treffer in MasterSet/Views sind **andere** Variablen). *NICHT VERIFIZIERT, dass die Relation nirgends dynamisch genutzt wird.*
- **Migration:** `2025_04_01_091560_create_customer_phase_lists_table.php`. Spalten (Auszug): `project_id`, `customer_id`, `checklist_id`, `phase_id`, `activities_id`, `product_id`, `service_id`, `department_id`, `service`, `alternative_id`, `parent_id`, `contact_person`, `responsible_person`, `outside_service/company`, `color`, `active_by`, `jump_steps(_by)`, `done`(enum true/false), `type`(enum main/sub), `main_id`, `outside_type`, `done_date`. → **reiche Phasen-Instanz-Tabelle** je Kunde/Objekt/Produkt. Keine späteren ALTER-Migrationen im ersten Grep gefunden (nur die eine create-Migration).
- **⚠️ Spalten-Mismatch (NICHT VERIFIZIERT):** Migration definiert `customer_id`/`product_id`/`alternative_id`, aber die Controller-Queries lesen `customer`/`product`/`alternative` (z. B. `CustomerPhaseListController:140/152`, `NewLeadsController:5755`). Entweder gibt es eine **ALTER-Migration** (nicht geprüft) oder die Reads laufen ins Leere. Falls letzteres → weiteres Indiz für „gebaut, aber ungenutzt". **Zu klären.**

## 4. Views / Frontend — NICHT 0

**Gerenderte UI (durch den Controller):**
- `create()` `:84` → `return view('admin.customer.phase_management.manage', $item)`.
- `show()` `:284` → `return view('admin.customer.phase_management.customer_phase', $data)`.

**Views, die die Routen AKTIV aufrufen (nicht bloß erwähnen), Live (keine `old code`/`copy`):**
- `admin/customer/phase_management/manage.blade.php` — `:415` `customer_phase_get_new`, `:484` `customer_phase_get` (AJAX auf getPhaseNew/getPhase).
- `admin/customer/phase_management/customer_phase.blade.php` — `:59` `customer.phase.managment.create`.
- `admin/customer/customer_product_create.blade.php` — `:2070` `customer.phase.managment`.

→ **„0 Views" ist falsch.** *(Der frühere Befund grepte auf `customer_phase_lists`/`CustomerPhaseList` in Views = 0 — korrekt für den **Tabellennamen**, aber die Views referenzieren das Feature über **Route-Namen**, nicht den Tabellennamen. Deshalb der Fehlschluss.)*

## 5. Nuriva / API

`grep customer_phase_lists|CustomerPhaseList` in **`routes/api.php` = 0**. → **Nuriva/Mobile-API berührt `customer_phase_lists` NICHT.** Bestätigt (wie erwartet).

## 6. Dormanz-Urteil — KORRIGIERT

| Behauptung (früher) | Realität (belegt) |
|---|---|
| „1 Schreibstelle" | **3 Schreib-Aktionen** (create:111 / delete:190 / update:201), alle manuell |
| „0 Leser" | **≥6 Lesestellen** (Controller :37/72/139/168/214 + NewLeadsController::get_phase:5753) |
| „0 Views" | **2 gerenderte UI-Seiten** + **3 Views** rufen die Routen aktiv auf |
| „fast dormant, billig ablösbar" | **code-LEBENDIG** (9 Routen, volles CRUD-Feature „Phasen-Management"), nur **daten-leer** (0 Zeilen) |
| Nuriva berührt es | **korrekt: nein** |

**Fazit:** `customer_phase_lists` ist **data-dormant, aber NICHT code-dormant.** Es ist ein **vollständiges, verdrahtetes Phasen-Management-Feature** (`customer_phase_manage` / `phase_management/*`) — vermutlich der **Vorgänger** des `lead_stages`/Kanban-Systems (Weiche 1), aber im Code noch komplett live. Die DB ist leer (0 Zeilen), was **stark** für „ungenutzt" spricht — aber **kein Beweis**, dass niemand die UI je öffnet (**NICHT VERIFIZIERT**: ob ein lebendes Menü/Profil auf `customer_phase_manage`/`get_phase` verlinkt/zugreift).

---

## Vorschlag (NICHT umgesetzt — Yama entscheidet)

**Ist „Ablösen = nur die eine Schreibstelle stilllegen" sicher? → NEIN.** Es gibt **kein** einzelnes Write; es gibt ein **ganzes Feature** (2 UIs + create/delete/update + getPhase/getPhaseNew/get_phase/show). Einfach „create abschalten" ließe die **Leser + Views + den JSON-Endpoint** live (sie liefern dann leer — was bei leerer DB ohnehin schon der Fall ist).

**Minimaler, sicherer, REVERSIBLER Ablöse-Schritt (Vorschlag):**
1. **ZUERST bestätigen (Voraussetzung):** dass das Feature wirklich ungenutzt ist — den Spalten-Mismatch (§3) klären UND prüfen, ob ein lebendes Menü/Profil auf `customer_phase_manage` (`web.php:1458`) oder `get_phase` (`:853`) verlinkt. *(Solange das nicht bestätigt ist, ist keine Abschaltung „sicher".)*
2. **Dann reversibel:** die **Schreib-Routen auskommentieren** (`store` :1463, `color` :1464, `deletePhase` :1465) + die **UI-Einstiegspunkte** (Menü-/Button-Links zu `manage`/`customer_phase`) deaktivieren → das Feature kann nicht mehr gefüttert werden. **Reversibel** (auskommentieren rückgängig). Die Leser bleiben, liefern leer (Status quo).

**Späterer, UNumkehrbarer Cleanup (separater Schritt, NICHT jetzt):** Tabelle droppen + Model + `CustomerPhaseListController` + `NewLeadsController::get_phase` + die 2 Views + `TaskPhase::customerPhaseList`-Relation + alle 9 Routen entfernen. Das ist ein echter Rückbau, kein reversibles „aufhören zu schreiben".

**Ehrliche Konsequenz:** Die Ablösung ist **nicht billig/trivial**, wie der Vorbefund suggerierte. Sie berührt ein live gerendertes Feature. Der sicherste nächste Schritt ist **nicht Code-Änderung, sondern Nutzungs-Klärung** (öffnet jemand `customer_phase_manage`? läuft `get_phase` in einem aktiven Profil?), bevor irgendetwas stillgelegt wird.

---

## Gelesen / NICHT gelesen (ehrlich)

**Vollständig gelesen/gegrept:** app-weiter Grep `customer_phase_lists|CustomerPhaseList` (app/ + resources/views/ + routes/ + database/migrations/); `CustomerPhaseListController` (Methoden-Signaturen + view-Returns + create/delete/update-Zeilen + Read-Queries :37/72/139/168/214); `NewLeadsController::get_phase` (:5750–5769, `->get()`→json); `TaskPhase.php:51–53` (Relation); Migration `2025_04_01_091560` (Spalten); Routen `web.php:853,1458–1465`; die 3 Live-Views (Route-Aufrufe :415/484/59/2070); `api.php`-Gegenprobe (0); Auto-Feeder-Grep (nur `:111`).

**Nur gegrept / NICHT VERIFIZIERT:**
- **Runtime-Nutzung:** ob ein lebendes **Menü/Profil** die `customer_phase_manage`/`get_phase`-Routen tatsächlich aufruft (öffnet ein Nutzer das Feature?). DB-leer ist starkes, aber kein absolutes Indiz.
- **Spalten-Mismatch** `customer` vs `customer_id` — ob eine **ALTER-Migration** existiert (nicht gezielt gesucht) oder die Reads teils broken sind.
- **`TaskPhase::customerPhaseList`-Relation** — dynamische Nutzung nicht 100 % ausgeschlossen (grep fand nur Definition).
- `CustomerPhaseListController` **nicht** Zeile-für-Zeile ganz gelesen (nur die relevanten Blöcke) — eine weitere R/W-Stelle könnte übersehen sein (unwahrscheinlich, da app-weiter Grep vollständig).

## Schwächen dieser Analyse (Selbstkritik)
- Die **Live/Tot-Frage ist code-basiert, nicht nutzungsbasiert.** Ich habe belegt, dass das Feature **verdrahtet** ist — nicht, ob es **benutzt** wird. Ein Zugriffs-Log/Browser-Test würde „ungenutzt" erhärten.
- Der **Spalten-Mismatch** ist ein echter offener Punkt: falls die Reads broken sind, ist das Feature faktisch tot (unterstützt Ablösung); falls eine ALTER existiert, ist es voll funktionsfähig. **Ich habe das nicht aufgelöst** — das ändert die Ablöse-Risikobewertung und muss vor jeder Abschaltung geklärt werden.
- Meine „reversibel vs. unumkehrbar"-Einteilung ist eine **Einordnung**, keine reine Code-Aussage.

---

*Reine Analyse — nichts geändert. Belege: `CustomerPhaseListController` (store/create:92/111, deletePhase/delete:188/190, color/update:194/201, getPhase:134, getPhaseNew:165, show:209, view-Returns :84/284, Reads :37/72/139/168/214); `NewLeadsController::get_phase:5750–5769`; `TaskPhase.php:51–53`; Migration `2025_04_01_091560_create_customer_phase_lists_table`; Routen `web.php:853,1458–1465`; Views `phase_management/manage.blade`(415/484), `phase_management/customer_phase.blade`(59), `customer_product_create.blade`(2070); `api.php`=0; Auto-Feeder-Grep app/+database/ = nur create:111. Querverweis: `architektur-bewertung-zweitmeinung.md` (korrigiert die dortige Dormanz-Annahme), `architektur-entscheidungen.md` (Weiche 6: customer_phase_lists ablösen), `struktur-systeme-verhaeltnis-befund.md`, `glossar.md`.*
