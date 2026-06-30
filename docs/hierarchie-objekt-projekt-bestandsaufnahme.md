# Hierarchie Kunde → Objekt → Projekt → Vorgänge — Bestandsaufnahme

**Reine Lese-Analyse, nichts geändert.** Stand: 2026-06-30 · Branch `private/app-code-backup`.
Zweck: klären, wie die von Yama gelebte Struktur (Kunde → Objekt → Projekt → Angebot/Auftrag/Rechnung) im Datenmodell **wirklich** verkettet ist — und den scheinbaren Widerspruch zu `docs/workflow-analyse.md` (Schwäche 7: „projects hängt nicht an deals, lose Tripel-Verbindung") auflösen.

> **Auflösung in einem Satz:** Die Hierarchie **Kunde → Objekt → Vorgänge** existiert als **echte, DB-erzwungene FK-Kette** und ist **voll befüllt** — Schwäche 7 war an dieser Stelle zu pessimistisch. Was **nicht** existiert, ist „**Projekt**" als der Eltern-Container, der Angebot/Auftrag/Rechnung trägt: die `projects`-Tabelle ist ein **paralleler Geschwister-Datensatz** auf demselben Tripel, kein Vorgang verweist auf sie. Und die von Yama beschriebene **Vielfachheit** (Kunde mehrere Objekte, Objekt mehrere Gewerke) ist im Schema möglich, wird aber in den Daten **nicht gelebt** (1:1:1).

---

## 1. Existiert die Hierarchie im Schema — und wie ist sie verkettet?

### Die Stationen und ihre echten Tabellen
| Ebene | Tabelle | Verkettung |
|---|---|---|
| **Kunde** | `new_leads` | das „Profil" |
| **Objekt** | **`lead_alternative_adds`** | `lead_id` → `new_leads` (Adresse + Objekt-Technik: `object_name`, `object_type`, `full_address`, `building_width`, `heat_pump_subsidy_percent`, `total_electricity_consumption` …) |
| Objekt-Details | `lead_object_rooms`, `lead_alternative_pv_wp_details` | Räume / PV-WP-Daten je Objekt |
| **Gewerk-Vorgang** | `lead_product_lists`, `offers`, `deals`, `invoices`, `projects` | je `(customer_id, product_id, alternative_id)` |

**„Objekt" = `lead_alternative_adds`.** Es trägt `lead_id` (FK zum Kunden) und die komplette Objekt-/Gebäudebeschreibung. Das `alternative_id` im Tripel ist genau dieses Objekt.

### Die TATSÄCHLICHEN Fremdschlüssel (belegt aus den Migrationen)
- **Vorgang → Kunde:** echter FK. `deals`/`offers`/`projects` haben `foreign('customer_id')->references('new_leads')` (`create_deals_table:42`, `create_offers_table:28`, `create_projects_table:38`).
- **Vorgang → Objekt:** **echter FK.** `deals`/`offers`/`projects` haben `foreign('alternative_id')->references` auf `lead_alternative_adds` (`create_deals_table:45`, `create_offers_table:29`, `create_projects_table:42`). → **Das Objekt ist DB-erzwungen, nicht lose.**
- **Auftrag → Angebot:** echter FK seit P1-16 (`add_offer_relations_to_deals_table:13` `constrained()` → `deals.offer_id`).
- **Vorgang → PROJEKT:** **kein FK, gar keine Spalte.** Weder `offers` noch `deals` noch `invoices` tragen ein `project_id`:
  - `offers` → nur `alternative_id` (Objekt)
  - `deals` → `alternative_id` (Objekt) + `project_status` (nur ein Status-**Text**, kein FK)
  - `invoices` → `object_id` (das Objekt) — **kein** `deal_id`-Pflicht-Projektbezug
  - `deal_invoices` → nur `deal_id`
- **`projects` → Auftrag:** **kein `deal_id`.** Das Projekt verweist auf Kunde + Objekt + Produkt (Tripel), aber **nicht** auf den Auftrag.

### Bild der realen Verkettung
```
new_leads (Kunde)
   │  FK lead_id
   ▼
lead_alternative_adds (Objekt)  ◄─── FK alternative_id ───┬── offers
   ▲ FK alternative_id                                    ├── deals  ──FK offer_id──► offers
   │                                                       ├── projects   (KEIN Bezug zu deals)
   └───────────────────────────────────────────────────── └── invoices (object_id)
```
→ Kunde→Objekt→Vorgänge ist eine **echte FK-Kette**. Aber die Vorgänge hängen **alle direkt am Objekt** (parallel), **nicht** an einem Projekt-Container. `projects` ist einer dieser parallelen Vorgänge — kein Dach über den anderen.

---

## 2. Wird die Struktur im Alltag befüllt?

### Befüllung (geseedete + reale Demo-Daten)
| Ebene | Anzahl | Objekt-Verknüpfung gesetzt |
|---|--:|---|
| Kunden (`new_leads`) | 52 | — |
| **Objekte (`lead_alternative_adds`)** | **71** | **71/71 mit `lead_id`** ✓ |
| Projekte (`projects`) | 31 | 31/31 mit `alternative_id` ✓ |
| `lead_product_lists` | 52 | 52/52 ✓ |
| Angebote (`offers`) | 29 | 29/29 ✓ |
| Aufträge (`deals`) | 14 | 14/14 ✓ |
| Rechnungen (`invoices`) | 11 | 11/11 mit `object_id` ✓ |

→ **Jeder Vorgang trägt die Objekt-ID.** Die Objekt-Ebene ist nicht tot, sondern durchgängig gefüllt. Beim echten Anlegen wird das Objekt mitgeführt: `dealStore` übernimmt `alternative_id` aus dem `lead_product_list` (`DealController` — Objekt wird propagiert).

### ABER: die Vielfachheit wird nicht gelebt
- **Kunden mit > 1 Objekt: 0.** Kein Kunde hat in den Daten mehrere Objekte.
- **Objekte mit > 1 Gewerk (`lead_product_lists`): 0.** Kein Objekt trägt mehrere Gewerke (PV + Fenster + WP am selben Objekt).

→ Das Schema **erlaubt** „Kunde → mehrere Objekte → mehrere Gewerke je Objekt", aber die aktuellen (geseedeten) Daten sind durchweg **1 Kunde : 1 Objekt : 1 Gewerk**. Yamas Mehrfach-Struktur ist also **angelegt, aber nicht ausgefüllt** (zumindest in den Demo-Daten; ob das reale Anlegen Mehrfachheit erzeugt, hängt am Erfassungs-Workflow).

### „Projekt" wird unabhängig vom Auftrag erzeugt
- `projects` entsteht über den **Planer** (`AddEmployeeToProjectController:53/96` `Project::create`) bzw. den Seeder (`DemoOperationalDataSeeder:159`) — **nicht** beim Anlegen eines Auftrags.
- **Nur 8 von 31 Projekten** teilen das Tripel eines Auftrags. → Projekte und Aufträge laufen **getrennt** und **nicht 1:1**; ein Projekt entsteht nicht aus einem Deal und verweist nicht auf ihn.

---

## 3. Gibt es zwei konkurrierende Strukturen?

**Ja, in zweifacher Hinsicht:**

1. **Zwei Begriffe von „Projekt":**
   - **(i) Das konzeptionelle Gewerk** — die Kombination *Objekt × Produkt*, materialisiert über das Tripel in `lead_product_lists` + `offers` + `deals` + `invoices`. **Das** trägt tatsächlich Angebot/Auftrag/Rechnung (über das Objekt + Produkt). Wird genutzt.
   - **(ii) Die `projects`-Tabelle** — ein eigener Ausführungs-/Bauphasen-Datensatz (Projektleiter, Montagestart, Fortschritt), separat im Planer geführt, lose über das Tripel, **ohne** Bezug zu Angebot/Auftrag/Rechnung. Teilbefüllt (31), nur 8 mit Auftrags-Tripel.
   → Yamas „Projekt trägt die Daten" meint (i); die Tabelle `projects` ist (ii). Beide existieren nebeneinander und sind **nicht** verbunden.

2. **Zwei Objekt-Tabellen:**
   - **`lead_alternative_adds`** — die genutzte (71 Zeilen, voll verkettet).
   - **`customer_alternative_adds`** — **0 Zeilen**, Schlüssel `customer_id`/`address_no`. Eine parallele/ältere Objekt-Struktur, **tot**.

---

## 4. Fazit — welche Lage trifft zu?

**Eine Mischung, präzise:**

- **Für „Kunde → Objekt → Vorgänge" gilt (a):** Die Hierarchie existiert **sauber als echte FK-Kette** (`new_leads ← lead_id ← lead_alternative_adds ← alternative_id ← offers/deals/projects/invoices`) **und wird genutzt** (71/71 Objekte verkettet, alle Vorgänge tragen die Objekt-ID). → **Schwäche 7 ist an diesem Punkt zu relativieren:** der Kunde- und Objekt-Bezug ist **nicht** lose, sondern DB-erzwungen.

- **Für „Projekt als Container" gilt (b)+(c):** Der Eltern-Container „Projekt", der Angebot/Auftrag/Rechnung **trägt**, existiert **nicht** als Datensatz — kein Vorgang verweist auf `projects.id`. Was die Vorgänge eines Gewerks zusammenhält, ist das **Objekt × Produkt** (Tripel), nicht eine Projekt-Zeile. Die `projects`-Tabelle ist ein **paralleler, lose gekoppelter** Ausführungs-Datensatz (nur 8/31 mit Auftragsbezug) → **zwei konkurrierende „Projekt"-Begriffe (c)** plus eine **tote zweite Objekt-Tabelle**.

- **Vielfachheit:** Schema-seitig möglich, in den Daten **nicht gelebt** (1:1:1).

### Kurz für die Entscheidung (Yama + Planer)
- Yamas Bild **Kunde → Objekt** ist **korrekt und im System real** — gut.
- Yamas Bild **Objekt → Projekt → (trägt Angebot/Auftrag/Rechnung)** stimmt **inhaltlich** (die Vorgänge hängen am Objekt+Gewerk), aber es gibt **keine „Projekt"-Klammer** als Datensatz; die `projects`-Tabelle ist **nicht** diese Klammer.
- **Schwäche 7 bleibt teils gültig:** nicht für den Kunde/Objekt-Bezug (der ist FK-fest), sondern speziell für **Projekt↔Auftrag** (kein FK, nur 8/31 Überlappung) und das **Fehlen einer Projekt-Klammer**.
- Offene Grundsatzfrage (deckt sich mit `docs/architektur-entscheidungen.md` Frage 5): Soll „Projekt" zur echten Klammer werden (1 Objekt-Projekt bündelt mehrere Gewerke-Aufträge — passt zum objekt-zentrierten Zielbild), und soll die tote `customer_alternative_adds` entfallen?

---

*Ende der read-only Bestandsaufnahme. Keine Code-, Schema- oder Datenänderung. Belege: Migrationen `create_deals/offers/projects_table`, `add_offer_relations_to_deals_table`; Tabellen `lead_alternative_adds`, `projects`, `customer_alternative_adds`; Zähl-Abfragen auf den Demo-Daten.*
