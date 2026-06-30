# Kundenprofil — Architektur-Bestandsaufnahme

**Reine Lese-Analyse, nichts geändert.** Stand: 2026-07-01 · Branch `private/app-code-backup`.
Verbindliche Begriffe: `docs/glossar.md` (Kunde=`new_leads`, Objekt=`lead_alternative_adds`/`alternative_id`, Gewerk=`lead_product_lists`).
Hinweis: Die Haupt-Blade hat **23.145 Zeilen** — eine zeilengenaue Vollkartierung ist nicht sinnvoll; dieses Dokument erfasst **Gerüst, Sektionen, Datenflüsse und Auffälligkeiten** belegt, nicht jede einzelne Zeile.

> **Kurzfazit:** Das Kundenprofil ist eine **einzelne Mega-Blade (23.145 Z.)**, gespeist von einer **~630-Zeilen-Controller-Methode** mit **~31 Datenblöcken** und **~105 AJAX-Calls**. Es hängt **sauber an `new_leads`** (NICHT an der Customer-Model-Falle). Die **Mehrfachheit** (Kunde→Objekte→Gewerke) ist über ein **Objekt-Produkt-Baum-Panel** technisch abgebildet (auch wenn die Daten heute 1:1:1 sind). Hauptproblem ist die **Wartbarkeit** (Dateigröße + Inline-Alles + Modal-Dubletten).

---

## 1. Einstieg & Gerüst

- **Route:** `GET /new_lead_profile/{id}` → `NewLeadsController@view` (`name: new.lead.profile`, routes/web.php:795).
- **Controller-Methode:** `view($id)` — **Z.1907–2537 (~630 Zeilen)**; baut ein Daten-Array `$item` und gibt zurück: `return view('admin.new_leads.customer_profile', $item)` (:2537).
- **Haupt-Blade:** `resources/views/admin/new_leads/customer_profile.blade.php` — **23.145 Zeilen**.
- **Teil-Views (nur 3 `@include`):** `layouts.profile` (:3623), `layouts.new_product_form` (:3646), `partials.customerEditDrawer` (:3936). → **~99 % der Blade ist inline** (HTML + CSS + JS direkt eingebettet).
- **Verwandte Route:** `object_profile($id,$alternative)` (`/new_lead_profile_object/...`, :812) — Objekt-spezifische Profil-Sicht; `saveObjectData` (:806).

**Layout-Gerüst (wie im Code):** Kein klassisches 3-Spalten-Layout (links-Sidebar / Main / rechts-Sidebar), sondern:
- **Karten-Grid** als Hauptbereich (`col-md-6` 39×, `col-md-4` 11×, `col-md-3` 6×, `col-md-12` 4×).
- **Einschiebbare Drawer** (slide-in, `display:none` bis ausgelöst) — s. Abschnitt 3.
- **`<aside>`-Panels** (:4665, :4772) — u. a. der Objekt-Produkt-Baum.
- Die globale App-Sidebar (linkes Hauptmenü) ist Layout, nicht profil-eigen.

---

## 2. Interne Navigation (Tabs/Reiter)

- **Zählung:** 10 `tab-pane`, 6 `data-toggle="tab"`-Trigger.
- **Aber: die Tabs sind überwiegend MODAL-intern**, nicht eine Profil-Hauptnavigation. Die `href="#…"`-Ziele sind: `addTabGallery`/`addTabDetails` (Tabs **innerhalb** des „Produkt hinzufügen"-Modals) und `editTabGallery`/`editTabDetails` (Tabs im „Produkt bearbeiten"-Modal). → Galerie/Details-Reiter je Gewerk-Modal.
- Eine weitere Tab-Gruppe (`kb-enterprise-tab-pane`, ~Z.4853/4934) gehört zum **Aufgaben-Block** („Aufgabenliste" / „Neue manuelle Aufgabe").
- **Das Profil selbst ist kein Reiter-System, sondern ein langer Scroll aus Karten + AJAX-Panels.** Die „Tabs" strukturieren die Modals, nicht die Seite.

→ **Tote/leere/doppelte Tabs:** keine klassischen Profil-Tabs, daher keine toten Profil-Reiter; die Modal-Tabs (Gallery/Details) sind aktiv genutzt.

---

## 3. Sidebars / Drawer

Statt fester Seitenleisten nutzt das Profil **einschiebbare Drawer** und **Aside-Panels**:

| Element | Beleg (Z.) | Inhalt / Daten | Status |
|---|---|---|---|
| **reportSidebar** (Drawer) | 3660 (`display:none`) | „Kundenprozessbericht" — Berichte; Speichern via `ajax.save.customer.history` | aktiv (on-demand) |
| **commentSidebar** (Drawer) | 3725 (`display:none`) | „Kommentare" — Notizen via `customer_card_notes.store` | aktiv (on-demand) |
| **customerEditDrawer** (Partial) | `@include` :3936 | Kunden-Stammdaten bearbeiten | aktiv |
| **customerSidebar** (JS) | 5035 | Kunden-Detail-Slide | aktiv (JS-gesteuert) |
| **`<aside>` Objekt-Produkt-Baum** | 4665–4668 | **„Produkte zu Objekten zuordnen"** — zeigt Objekte + ihre Gewerke; Daten via `ajax.customer.objectProductTree` (CustomerObjectProductModalController) | aktiv — **die Mehrfachheits-Ansicht** |

→ **Aktiv vs. tot:** alle o. g. sind aktiv (per Trigger/JS). Insgesamt **46× `display:none`** in der Blade — viele davon Modals/Drawer (gewollt versteckt), einige potenziell tote Blöcke (s. Abschnitt 6).

---

## 4. Hauptbereich (main content)

Karten-/Sektions-Blöcke (aus den Überschriften belegt) und ihr Bezug:

| Block | Daten | Bezug |
|---|---|---|
| **Übersicht** | Kunde (Stammdaten) | `new_leads.id` (`$item['customer']`) |
| **Produkte / Gewerke** | `$item['products']`, `productcount` | `lead_product_lists` je `(customer, product, alternative)` — mit Modals: „Produkt hinzufügen/ansehen/bearbeiten", „Seriennummern verwalten", „Umsatz bearbeiten" |
| **„Produkte zu Objekten zuordnen"** | Objekte + Gewerke als Baum | `lead_alternative_adds` (Objekte) × `lead_product_lists` (Gewerke) — **hier wird die Mehrfachheit dargestellt** |
| **Kontaktperson hinzufügen** | Kontakte | kundenbezogen |
| **Kundenprozessbericht / Verlaufsdetails** | Berichte/Historie | `ajax.save.customer.history` |
| **Kommentare** | Notizen | `customer_card_notes.store` |
| **Aufgaben** | `$item['to_does']`, `tickets` | `task_to_dos`, `problems` (Tickets) |
| **Bilder/Galerien** | `$item['images']`, `screenshots`, `image_*_sort` | `images` (sortiert je Kategorie) |
| **KPI / Analytics** | `$item['kpi_analytics']` | Auswertung |
| **PV/WP-Technikdaten** | `roofs`, `heating`, `heating_types`, `cold_water`, `warm_water`, `circulation`, `electro`, `meter_cabinet`, `wp`, `rediators`[sic], `tiles` | Objekt-Fachdaten (PV/Wärmepumpe) |

**Darstellung der Mehrfachheit:** **Ja, technisch vorhanden** — der Objekt-Produkt-Baum („Produkte zu Objekten zuordnen") bildet mehrere Objekte je Kunde und mehrere Gewerke je Objekt ab und erlaubt Zuordnen/Verschieben (`moveProduct`). Da die Daten heute **1:1:1** sind (s. `hierarchie-objekt-projekt-bestandsaufnahme.md`), sieht man die Mehrfachheit aktuell selten gefüllt — die **UI kann es**, die **Daten zeigen es kaum**.

**Auslösbare Aktionen:** Gewerk hinzufügen (Produkt-Modal, mit `customer_id`+`alternative_id` → Fall B), Objekt zuordnen/verschieben (Baum), Stammdaten bearbeiten (Drawer), Notiz/Kommentar, Aufgabe anlegen, Bericht schreiben, Umsatz bearbeiten, Seriennummern verwalten, Bilder verwalten — alles **AJAX** (105 Calls).

---

## 5. Datenfluss & Abhängigkeiten

- **Controller `view()`:** ~630 Zeilen, baut **~31 `$item`-Blöcke**. Direkte Quellen u. a.: `lead_product_lists` (mehrfach: Total, Liste, je Stage), `lead_alternative_adds` (Objekte), `employees` (mehrfach), `problems`/`error_problem` (Tickets), `images` (mehrfach), `task_to_dos`.
- **Profil hängt SAUBER an `new_leads`** — in `view()` **kein** `DB::table('customers')` und **kein** `Customer::`. → **NICHT** von der Customer-Model-Falle betroffen (`docs/customer-model-falle-befund.md`). Der Kunde ist `$item['customer']` aus `new_leads`.
- **Bedienende Endpoints (Auszug, ~105 AJAX-Calls):** `customer.profile.workflow.stages`, `suggest.employees.store/update`, `personal.task.customer.store`, `lead_product_lists.bulk.store`, `lead.products.update/save`, `customer_card_notes.store`, `ajax.save.customer.history`, `ajax.customer.objectProductTree` (+ `moveProduct`/`createObject`/`deleteProduct`).
- **Performance-Auffälligkeiten (nur benannt, nicht gemessen):** Die mehrfachen `lead_product_lists`-/`employees`-Queries in `view()` plus ~105 client-seitige AJAX-Calls erzeugen viele Round-Trips; ein N+1-Risiko besteht überall, wo je Gewerk/Objekt/Karte einzeln nachgeladen wird. **Im Betrieb zu messen.**

---

## 6. Auffälligkeiten (benannt, nicht gelöst)

| # | Auffälligkeit | Beleg | Bewertung |
|---|---|---|---|
| 1 | **Mega-Blade (23.145 Z.) + 630-Z.-Controller-Methode**, ~99 % inline (nur 3 Includes) | `customer_profile.blade.php`; `view()` 1907–2537 | **kritisch (Wartbarkeit)** — kaum testbar/änderbar ohne Risiko |
| 2 | **Mehrere Produkt-(Gewerk-)Modal-Varianten** nebeneinander: „Neues Produkt hinzufügen", „Produkt hinzufügen", „Produkt ansehen / bearbeiten", „Produkt bearbeiten" | Karten-Überschriften | **mittel** — mögliche Dubletten/Redundanz, zu konsolidieren |
| 3 | **46× `display:none` + 21 Blade-Kommentare + 2 TODO** | grep | **mittel** — versteckte/teils tote Blöcke; schwer zu unterscheiden, was live ist |
| 4 | **Begriffs-Inkonsistenz im UI/Code:** „products" = Gewerke, „alternative" = Objekt, `data-object-product` = product_id/article_groups.id | :22406 u. a. | **kosmetisch–mittel** (Glossar-konform umzubenennen wäre Folge der Begriffs-Arbeit) |
| 5 | **Zweite Profil-Blade `customer_view.blade.php` (9.860 Z.)** existiert parallel | wc -l | **mittel** — zwei große Kunden-Views nebeneinander; Abgrenzung/Dublette zu klären |
| 6 | **Mehrfachheit gebaut, aber kaum gelebt** — Objekt-Produkt-Baum kann mehrere Objekte/Gewerke, Daten sind 1:1:1 | Abschnitt 4 | **Designfrage (GR)** — ob/wie der Erfassungs-Workflow die Mehrfachheit füllt (s. `erfassung-duplikat-befund.md`) — **kein Bug** |
| 7 | **Positiv-Befund:** Profil sauber an `new_leads`, NICHT an der Customer-Falle | Abschnitt 5 | — (wichtige Entlastung) |

---

## Aufbau-Skizze (vereinfacht)

```
GET /new_lead_profile/{id}  →  NewLeadsController@view (~630 Z.)  →  customer_profile.blade (23.145 Z.)

┌───────────────────────────────────────────────────────────────┐
│  [globale App-Sidebar]   |   KUNDENPROFIL (Karten-Grid)        │
│                          |                                      │
│                          |  • Übersicht (Kunde / new_leads)     │
│                          |  • Produkte/Gewerke (lead_product_   │
│                          |    lists)  + Modals (Add/Edit/SN/    │
│                          |    Umsatz, Gallery/Details-Tabs)     │
│                          |  • „Produkte zu Objekten zuordnen"   │
│                          |    = Objekt-Produkt-BAUM (Mehrfach-  │
│                          |    heit: Objekte × Gewerke)          │
│                          |  • Aufgaben / Tickets / Bilder /     │
│                          |    KPI / PV-WP-Technikdaten          │
│                          |                                      │
│  Drawer (einschiebbar):  reportSidebar · commentSidebar ·       │
│                          customerEditDrawer · customerSidebar   │
└───────────────────────────────────────────────────────────────┘
        ~105 AJAX-Endpoints (workflow stages, products, notes,
        history, object-product-tree, tasks, employees …)
```

---

*Reine Analyse — nichts geändert. Belege: routes/web.php:795/806/812; `NewLeadsController@view` 1907–2537; `customer_profile.blade.php` (Includes, Tabs, Karten-Überschriften, `<aside>`, Drawer, `display:none`-Zählung, AJAX-Routen). Querverweise: `glossar.md`, `customer-model-falle-befund.md`, `hierarchie-objekt-projekt-bestandsaufnahme.md`, `erfassung-duplikat-befund.md`.*
