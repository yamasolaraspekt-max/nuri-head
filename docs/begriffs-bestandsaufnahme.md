# Begriffs-Bestandsaufnahme — CRM-Kernprozess

**Reine Lese-Analyse, nichts geändert. KEINE Empfehlungen — nur der ehrliche Ist-Zustand der Begriffe.**
Stand: 2026-06-30 · Branch `private/app-code-backup`. Zeilen-Zahlen aus den aktuellen Demo-Daten (belegen „lebendig" vs. „tot").
Begriff = welche **Tabelle(n)/Feld(er)** ihn repräsentieren; ergänzt um **Doppel/Tote/Widersprüche**.

> **Leitbefund:** Mehrere Kernbegriffe sind **mehrfach** im Schema (lebendige + tote Varianten) und teils **uneinheitlich benannt** (dasselbe Wort für Verschiedenes, oder ein Ding unter irreführendem Namen). Tote Tabellen sind mit **0 Zeilen** belegt.

---

## 1. KUNDE

| | Tabelle | Zeilen | Status | Bemerkung |
|---|---|--:|---|---|
| **Echter Kunde** | **`new_leads`** | **52** | **lebendig** | DAS Kunden-„Profil" (Anzeige: `customer_profile.blade`). Felder: `customer_type`, `customer_no`, `title`, `firma`, `name`, `lastname`, `full_address`, `street`, `postcode`, `city`, … |
| Doppel (tot) | `customers` | 0 | **tot, aber referenziert** | **Gleiche** Spaltenstruktur wie `new_leads` (customer_type/customer_no/title/firma/name/…). 0 Zeilen, aber **4 aktive Controller** greifen noch via `DB::table('customers')` darauf zu → Geister-Tabelle. |
| Namenskollision | `leads` | 0 | **tot, anderer Zweck** | Trotz Name **kein** Kunde, sondern **E-Mail-/Nachrichten**-Tabelle (`subject`, `body`, `sender_email`, `recipient_name`). 0 Zeilen, 0 aktive Nutzung. |

**Widerspruch:** Der Kunde heißt im Code **`new_leads`** (klingt nach „neuer Interessent", ist aber der vollwertige Kunde). Daneben eine **tote, gleich strukturierte `customers`** und eine **fehlbenannte `leads`** (= E-Mails). Drei Tabellen im Dunstkreis „Kunde/Lead", nur eine echt.

---

## 2. OBJEKT (Immobilie / Adresse)

| | Tabelle | Zeilen | Status | Felder (Objekt-Beschreibung) |
|---|---|--:|---|---|
| **Echtes Objekt** | **`lead_alternative_adds`** | **71** | **lebendig** | `lead_id` (→ Kunde), `full_address`, `street`, `postcode`, `city`, `object_name`, `object_type`, `objective`, `building_width`, `tile_name`, `heat_pump_subsidy_percent`, `total_electricity_consumption`, … |
| Objekt-Details | `lead_object_rooms`, `lead_alternative_pv_wp_details` | — | lebendig | Räume / PV-WP-Technik je Objekt |
| Doppel (tot) | `customer_alternative_adds` | 0 | **tot** | Parallele Objekt-Tabelle (`customer_id`, `address_no`). 0 Zeilen. Eigener `Old/CustomerAlternativeAddController`. |

**Widerspruch:** Das Objekt heißt **`lead_alternative_adds`** und steckt im Tripel als **`alternative_id`** — d. h. das Wort **„alternative"** bedeutet im ganzen Kernprozess **„Objekt"**. Das ist nicht selbsterklärend (klingt nach „alternative Adresse", meint aber die zentrale Immobilie/das Bauobjekt). Dazu eine **tote zweite** Objekt-Tabelle.

---

## 3. GEWERK / PRODUKT-VORGANG

| Begriff | Repräsentation | Beleg |
|---|---|---|
| **Gewerk** („PV Müller") | **`lead_product_lists`** — eine Zeile = **Kunde × Produkt × Objekt** (`customer_id`, `product_id`, `alternative_id`) | 52 Zeilen; alle mit `alternative_id` (Objekt) |
| Produkt-Katalog | `products` / `article_groups` | `product_id` zeigt darauf |
| Bindung ans Objekt | über **`alternative_id`** (echter FK auf `lead_alternative_adds`) | s. `hierarchie-objekt-projekt-bestandsaufnahme.md` |

**„PV Müller" ist also kein eigenes Objekt, sondern eine `lead_product_lists`-Zeile** (Produkt PV am Objekt des Kunden Müller). Das Gewerk hängt **am Objekt** (`alternative_id`), nicht an einem Projekt-Datensatz.

---

## 4. PROJEKT — zwei Bedeutungen

| | „Projekt"-Bedeutung | Repräsentation | Zweck / Felder |
|---|---|---|---|
| **(i)** | **Gewerk-am-Objekt** (Yamas „Projekt trägt Angebot/Auftrag/Rechnung") | **kein eigener Datensatz** — die Kombination **Objekt × Produkt**, materialisiert über das Tripel in `lead_product_lists` + `offers` + `deals` + `invoices` | das, was die Vorgänge fachlich zusammenhält |
| **(ii)** | **Bauphasen-Projekt** | **`projects`-Tabelle** (31 Zeilen) | Ausführung: `project_leader`, `project_start`, `montage_start`, `progress`, `project_status`. **Kein `deal_id`** — nur 8/31 teilen ein Auftrags-Tripel. Separat im Planer geführt. |

**Widerspruch:** Dasselbe Wort **„Projekt"** meint (i) das **Gewerk** (das die Geschäftsdaten trägt) und (ii) die **`projects`-Tabelle** (Bau-/Montagephase). Beide existieren **nebeneinander und sind nicht verbunden**. Zusätzlich tragen **mehrere** Tabellen ein `project_status`-Feld (`deals`, `projects`, `lead_product_lists`-Umfeld), das nicht dasselbe meint.

---

## 5. ANGEBOT / AUFTRAG / RECHNUNG

| Begriff | Tabelle(n) | Zeilen | Doppel/Widerspruch |
|---|---|--:|---|
| **Angebot** | `offers` (+ `offer_folders`, `offer_details`) | 29 | Ausarbeitung in „Foldern"; Begriff einheitlich |
| **Auftrag** | `deals` | 14 | „Deal" = Auftrag (englisch/deutsch gemischt) |
| **Rechnung (generisch)** | **`invoices`** | **11** | **lebendig** — hier liegen die echten Umsätze (204 k €); hat `deal_id` + `object_id` + `paid_amount`/`status` (open/paid/storniert) |
| **Rechnung (auftragsbezogen)** | **`deal_invoices`** | **0** | **tot** — Abschlag/Schluss-Logik (`invoice_type`, `paid_amount`, SoftDeletes), aber **0 Zeilen** |

**Widerspruch:** **Zwei Rechnungs-Begriffe** nebeneinander (`invoices` vs. `deal_invoices`) — beide auftragsfähig (`deal_id`), aber nur `invoices` befüllt. (Auflösung ist Frage 3 in `architektur-entscheidungen.md`.)

---

## 6. STATUS / PHASE — je Entität

| Entität | Status-/Stufen-Felder | Erkennbare Bedeutung |
|---|---|---|
| **Anfrage** (`inquiries`) | `status` | Published / Unpublished / Junk / Draft / progress / verified |
| **Lead/Gewerk** (`lead_product_lists`) | **~11 Felder:** `status`, `stage`, `stage_mode`, `product_stage_id`, `product_task_phase_id`, `work_status`, `lead_stage_sub_stage_id`, `offer_acceptance_status`, `old_stage`, `stage_history` (json), `product_stage_history` (json) | Pipeline-Stufe: lead/offer/follow_up/accepted/deal/project — **verteilt über viele Felder**, keine eine Wahrheit |
| **Angebot** (`offers`) | `status`, `status_msg` | Angebots-Status |
| **Auftrag** (`deals`) | **5 Felder:** `status`, `deal_status`, `measurement_status`, `project_status`, `status_msg` | `status` = Liste/Kanban (order/deal/Junk); `deal_status` = Workflow (confirm/inconfirm); `measurement_status` = Aufmaß; `project_status` = Bau |
| **Rechnung** (`invoices`) | `status` | open / paid / **storniert** / storniert_bezahlt_pruefen (letztere aus dem Storno-Fix) |
| **Projekt** (`projects`) | `status`, `project_status`, `status_msg` | Bau-/Montagestatus |

**Widerspruch:** „Status"/„Stage"/„Phase" sind **nicht einheitlich** — ein Lead trägt ~11 Stufenfelder, ein Auftrag 5; `project_status` existiert an **mehreren** Entitäten mit je anderer Bedeutung. (Konsolidierung = Frage 1 in `architektur-entscheidungen.md`.)

---

## 7. DUPLIKAT

| | Definition im Code | Beleg |
|---|---|---|
| **Prüf-Funktion** | `NewLeadsController@checkCustomer()` → GET `/check-new-leads` (JSON-Lookup) | :5122 |
| **Worauf** | **Adresse** (`street` + `postcode`) **UND** **Kontakt** (`telephone` ODER `phone` ODER `email`, ziffern-/trim-normalisiert) | :5145-5240 |
| **Wogegen** | **`new_leads`** (Kunde) **UND** **`lead_alternative_adds`** (bestehende Objekte) | zwei Abfragen |
| **Bezug des Begriffs** | „Duplikat" = **gleiche Adresse + gleicher Kontakt** — auf **Kunden-** *und* **Objekt-Ebene** | — |

**Widerspruch:** „Duplikat" ist **adress-/kontaktbasiert** und unterscheidet **nicht** zwischen „versehentlich doppelter Kunde" und „legitimes zweites Objekt/Gewerk an bekannter Adresse" — beides erzeugt einen Treffer. (Details + Geschäftsregeln in `erfassung-duplikat-befund.md`.)

---

## Schnellübersicht — lebendig vs. tot je Begriff

| Begriff | Lebendig (Zeilen) | Tot/Doppelt (0 Zeilen oder fehlbenannt) |
|---|---|---|
| Kunde | `new_leads` (52) | `customers` (0, noch referenziert), `leads` (0, = E-Mails) |
| Objekt | `lead_alternative_adds` (71) | `customer_alternative_adds` (0) |
| Gewerk | `lead_product_lists` (52) | — |
| Projekt | *(i)* Gewerk (konzeptionell) | *(ii)* `projects` (31, lose; gleicher Name, anderer Sinn) |
| Rechnung | `invoices` (11) | `deal_invoices` (0) |

---

*Ende der reinen Begriffs-Bestandsaufnahme. Keine Code-/Schema-/Datenänderung, keine Empfehlung. Belege: Tabellen-Zeilenzählungen + `SHOW COLUMNS`; `NewLeadsController@checkCustomer`; Querverweise auf `hierarchie-objekt-projekt-bestandsaufnahme.md`, `workflow-analyse.md`, `architektur-entscheidungen.md`.*
