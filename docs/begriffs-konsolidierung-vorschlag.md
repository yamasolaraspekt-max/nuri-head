# Begriffs-Konsolidierung — Vorschlag & Referenz-Karte

**Reine Analyse / Vorschlag. KEIN Rename, KEIN Drop, KEIN Schema-Eingriff, KEIN Refactor.**
Stand: 2026-06-30 · Branch `private/app-code-backup`. Baut auf `docs/begriffs-bestandsaufnahme.md` (Ist-Zustand) + `docs/workflow-sollkonzept.md`.
Gate-Schritt 1 (Begriffe konsolidieren) vor Struktur/Bau. **Dieses Dokument schlägt vor und kartiert — Yama + Planer ratifizieren.**

> **Jeder Glossar-Vorschlag ist mit ⚠️ „von Yama zu bestätigen" markiert. Wo eine Begriffswahl in Wahrheit eine Geschäftsregel/Architektur-Frage ist, ist sie als solche ausgewiesen — NICHT hier entschieden.**

---

## TEIL 1 — Glossar-Vorschlag (je Kernbegriff)

| Begriff | Vorgeschlagener verbindlicher Name | Single-Source (führende Tabelle/Feld) | Benennungs-Widerspruch heute | Status |
|---|---|---|---|---|
| **Kunde** | **Kunde** | **`new_leads`** (52 Z., lebendig) | Heißt `new_leads` (klingt nach „Interessent"); **`Customer`-Model zeigt per Default auf die TOTE `customers`** (0 Z.); `leads`-Tabelle = E-Mails | ⚠️ zu bestätigen |
| **Objekt** | **Objekt** (Immobilie/Adresse) | **`lead_alternative_adds`** (71 Z.) · Feld **`alternative_id`** | Das Wort **„alternative" bedeutet im Kernprozess „Objekt"** — nicht selbsterklärend; tote `customer_alternative_adds` (0 Z.) | ⚠️ zu bestätigen |
| **Gewerk / Vorgang** | **Gewerk** (am Objekt) | **`lead_product_lists`** (52 Z.) = Kunde × Produkt × Objekt | „PV Müller" ist eine `lead_product_lists`-Zeile, kein eigenes Objekt | ⚠️ zu bestätigen |
| **Projekt** | **(zwei Begriffe trennen!)** | (i) Gewerk = `lead_product_lists`; (ii) **Bauphase** = `projects` (31 Z.) | **Dasselbe Wort meint zweierlei** (Gewerk-am-Objekt vs. Bauphasen-Tabelle) | ⚠️ + **Geschäftsregel** (Architektur-Frage 5: ist „Projekt" eigener Vorgang oder Auftragsphase?) |
| **Angebot** | **Angebot** | **`offers`** (29 Z., + `offer_folders`/`offer_details`) | einheitlich | ⚠️ zu bestätigen |
| **Auftrag** | **Auftrag** | **`deals`** (14 Z.) | „Deal" (engl.) für Auftrag | ⚠️ zu bestätigen |
| **Rechnung** | **Rechnung** | **offen** — `invoices` (11 Z., gefüllt) **vs.** `deal_invoices` (0 Z.) | **Zwei Rechnungs-Tabellen** | ⚠️ + **GESCHÄFTSREGEL / Steuerberater** (Architektur-Frage 3: welches System gilt buchhalterisch?) — **hier NICHT entscheiden** |
| **Status / Phase** | **drei getrennte Begriffe** (laut Sollkonzept): **Phase** (wo), **Zustand** (wie), **Historie** (was) | je Dimension EINE Quelle (heute ~11 Felder vermischt in `lead_product_lists` + 5 in `deals`) | „Status"/„Stage"/„Phase" uneinheitlich; `project_status` an mehreren Entitäten mit anderer Bedeutung | ⚠️ + **Architektur-Frage 1** (verbindliche Statusquelle) |
| **Duplikat** | **Duplikat** (Kunden-/Objekt-Dublette) | Definition in `checkCustomer()` (Adresse `street`+`postcode` + Kontakt Tel/E-Mail), geprüft gegen **Kunden UND Objekte** | unterscheidet nicht „versehentlich doppelt" vs. „legitimes 2. Objekt" | ⚠️ + **Geschäftsregel** (s. `erfassung-duplikat-befund.md`) |

**Wichtigste Benennungs-Widersprüche (zur Klärung):**
1. **„Kunde" = `new_leads`**, nicht `customers`. Das **`Customer`-Model ist eine Falle**: es zeigt (Laravel-Default) auf die **leere** `customers`-Tabelle. Code, der `Customer::` nutzt, arbeitet auf 0 Datensätzen.
2. **„alternative" = Objekt.** `alternative_id` ist die Objekt-ID — das zentralste Feld des Kernprozesses trägt einen irreführenden Namen.
3. **„Projekt" doppeldeutig** — Gewerk vs. `projects`-Tabelle (Geschäftsregel, nicht nur Benennung).

---

## TEIL 2 — Referenz-Karte der TOTEN Tabellen (sicherheitskritisch)

> Modell→Tabelle-Zuordnung (alle Laravel-**Default**): `Customer`→`customers`, `Lead`→`leads`, `CustomerAlternativeAdd`→`customer_alternative_adds`, `NewLeads`→`new_leads` (lebendig), `LeadAlternativeAdd`→`lead_alternative_adds` (lebendig), `DealInvoice`→`deal_invoices` (explizit).

### 2.1 `customers` (0 Zeilen) — **NICHT sicher entfernbar**
**Aktive (Nicht-Old) Referenzen:**
- `App\Models\Customer` (→ `customers`) wird in **~10 aktiven Controllern** genutzt (CustomerHeatingCircuit, PVTools, Tools, ChecklistRoom, CustomerPhaseList, PurchaseRequest, Email/Leads …) — alle arbeiten dadurch faktisch auf der leeren Tabelle.
- `NewLeadsController:5520` — **Schreibzugriff**: `DB::table('customers')->where('id',…)->update(['inquiry_screenshot'=>…])` → läuft heute **ins Leere** (0 Zeilen), aber ist aktiver Code.
- `AdminController:76` (Lesen), `Email/LeadsController:137` (Lesen).
- Plus zahlreiche `Old/*`-Controller (AppointmentController, CustomerController, CustomerProductController).

**Was bräche bei Drop:** Das `Customer`-Model + alle `Customer::`-Nutzungen würden bei jeder Query einen SQL-Fehler werfen (Tabelle fehlt); die o. g. `DB::table('customers')`-Stellen ebenso.
→ **Klassifikation: (b) erst Referenzen bereinigen.** (Funktional ist die Tabelle tot, aber technisch hängt aktiver Code dran.)

### 2.2 `leads` (0 Zeilen, = E-Mail-Nachrichten) — **fast tot**
**Referenzen:** `App\Models\Lead` (→ `leads`); aktive DB-Nutzung nur **`Old/CustomerController:423`** (`$data['emails']=DB::table('leads')`). Keine aktive `Lead::`-Nutzung in Email/Wordpress gefunden.
→ **Klassifikation: (a)/(b) — voraussichtlich entfernbar**, nachdem bestätigt ist, dass `Lead`-Model + der eine Old-Zugriff wirklich ungenutzt sind. Namenskollision mit dem Begriff „Lead/Kunde" auflösen.

### 2.3 `customer_alternative_adds` (0 Zeilen) — **Referenzen bereinigen**
**Referenzen:** `App\Models\CustomerAlternativeAdd`; genannt in `NewLeadsController`, `Kanban/LeadOverviewController`, `ArticleGroupController` + mehrere `Old/*` (eigener `Old/CustomerAlternativeAddController`).
→ **Klassifikation: (b) erst Referenzen bereinigen.** Tote Daten, aber aktiver Code referenziert sie.

### 2.4 `deal_invoices` (0 Zeilen) — **BEHALTEN**
**Referenzen:** `App\Models\DealInvoice` (explizit `deal_invoices`), `DealInvoiceController` (vollständiger CRUD), `DealController@cancelDealInvoices` (Storno-Fix), `LeadOverviewController`.
→ **Klassifikation: (c) behalten.** Das ist **kein Müll, sondern ein schlafendes, beabsichtigtes Feature** (auftragsgebundene Abschlag/Schluss-Rechnung). Ob es das führende Rechnungssystem wird, ist **Architektur-Frage 3** (Steuerberater) — bis dahin nicht anfassen.

### Übersicht
| Tote Tabelle | Zeilen | Aktive Referenzen | Klassifikation |
|---|--:|---|---|
| `customers` | 0 | Customer-Model (~10 Ctrl) + 3 DB::table (1 Schreib) + Old | **(b) Referenzen bereinigen** |
| `leads` | 0 | Lead-Model + 1 Old-Zugriff | (a)/(b) voraussichtlich entfernbar |
| `customer_alternative_adds` | 0 | Model + 3 aktive Ctrl + Old | **(b) Referenzen bereinigen** |
| `deal_invoices` | 0 | Model + DealInvoiceController + Storno | **(c) behalten (schlafendes Feature)** |

---

## TEIL 3 — Sprengradius möglicher Umbenennungen (nur Sizing, KEIN Rename)

Gemessen an Vorkommen je Bereich (Schema-FKs zählen besonders, da sie DB-Migrationen erfordern).

| Rename-Kandidat | Migrationen (Schema/FK) | Controller | Models | Views (Blade) | Routen | Größenklasse |
|---|---|---|---|---|---|---|
| **`new_leads` → „Kunde"** (`customers`?) | **62 Dateien / 78 Zeilen** (mit FKs) | **70 Dateien / 1074 Zeilen** | 3 | 51 Dateien / 162 Zeilen | 7 | **SEHR VIELE** |
| **`lead_alternative_adds` → „Objekt"** | 53 Dateien / 127 Zeilen (FKs) | 50 Dateien / 283 Zeilen | 0 | 7 Dateien / 9 Zeilen | 1 | **VIELE** |
| **`alternative_id` → „object_id"** | 62 Dateien / 143 Zeilen (FKs) | **83 Dateien / 1054 Zeilen** | **53 Models** | **119 Dateien / 849 Zeilen** | 13 | **EXTREM (höchstes Risiko)** |

**Einordnung (nur zur Kostentransparenz, keine Empfehlung):**
- Ein **physischer** Rename ist ein **sehr großer, schema-tiefer Umbau**: 60+ Migrationen mit **Fremdschlüsseln** müssten geändert (oder per Migration umgesetzt) werden, dazu ~1.000+ Code-Zeilen je Begriff und hunderte Blade-Stellen.
- `alternative_id` ist der **teuerste** Kandidat (Tripel-Feld → in 83 Controllern, 119 Views, 53 Models, 62 Migrationen). Ein direkter Spalten-Rename ohne Übergangsschicht wäre hochriskant.
- **Alternative zum physischen Rename** (nur als Denkanstoß, nicht entschieden): die Begriffs-Klarheit auf **Anzeige-/Code-Ebene** schaffen (Glossar + sprechende Model-Aliasse/Accessoren), ohne die physischen Spalten/Tabellen anzufassen — das vermeidet den Schema-Sprengradius. Ob physischer Rename oder Alias-Schicht: **Yamas/Planer-Entscheidung.**

---

## Zusammenfassung für die Ratifizierung

1. **Glossar (Teil 1):** 9 Begriffe mit Vorschlag + Single-Source, jeder **⚠️ zu bestätigen**; **Rechnung** + **Projekt** + **Status** + **Duplikat** sind teils **Geschäftsregel/Architektur-Fragen** (3/5/1) — dort entscheidet Yama (Rechnung: + Steuerberater).
2. **Tote Tabellen (Teil 2):** `deal_invoices` **behalten** (schlafendes Feature); `customers` + `customer_alternative_adds` **erst Referenzen bereinigen** (aktiver Code hängt dran, u. a. das `Customer`-Model = Falle); `leads` voraussichtlich entfernbar nach Bestätigung.
3. **Rename (Teil 3):** physischer Rename ist **sehr groß bis extrem** (Schema-FKs + ~1.000+ Stellen je Begriff); Alias-/Glossar-Schicht als kostengünstigere Alternative vermerkt — Entscheidung offen.

---

*Reine Analyse — nichts umbenannt, nichts entfernt, kein Schema-Eingriff. Belege: Model-Default-Tabellen (`app/Models/*`), `DB::table('customers'/'leads'/…)`-Fundstellen, Vorkommens-Zählungen über `database/migrations`, `app/Http/Controllers`, `app/Models`, `resources/views`, `routes`. Querverweise: `begriffs-bestandsaufnahme.md`, `architektur-entscheidungen.md`, `erfassung-duplikat-befund.md`, `workflow-sollkonzept.md`.*
