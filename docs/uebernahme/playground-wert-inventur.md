# Playground → ticket — Wert-Inventur (Transplantations-Vorbereitung)

**Stand:** 2026-07-05 · **read-only** in beiden Codebases · **Schreibprodukt = nur dieses Doc.**
**Rolle:** Playground-Inventur-Instanz. Ich baue nichts; Gebaut wird erst **nach Abschluss des wberechnung-Cut-overs** (harte Regel, andere Instanz). Yama entscheidet die A-Liste.

## Quellen · Gelesen / Nicht-gelesen
**Gelesen (belegt):**
- `docs/uebernahme/inventar-playground.md` (88 Z., Vorgänger-Inventur, Modul-Skelett).
- `docs/architektur-entscheidungen.md` (Weichen — `:33` 6 Phasen, `:140`/`:327-328` Kunde→Objekt→Gewerk/keine Projekt-Ebene W5).
- Playground-Struktur live: `backend-laravel/` = Laravel 11/12, **164 Migrationen · 274 Models · 269 Controller** (eigene Zählung 2026-07-05; Vorgänger-Doc nannte 173/297/279 — **Delta = Code hat sich geändert, NICHT-VERIFIZIERT welche**).
- **Live-Zeilenzahlen** aus `crm_erp` via playgrounds eigener `artisan tinker` (57 Schlüssel-Tabellen, exakte COUNTs, s. TEIL 1/4).
- Root-Layout playground (`app.jsx` 199 KB React, `backend-laravel/`, `docs/` 36 Einträge; Konzept-Docs `BEFUND.md`, `crm_erp_fach_technikkonzept.md` 147 KB — **nur Existenz/Größe gesehen, Inhalt NICHT gelesen**).

**NICHT gelesen (ehrliche Grenze):**
- Der **React/TS-SPA-Teil** (`app.jsx`, `src/**`, 3D-Dachplaner Three.js, TS-Connectoren) — bewusst außerhalb (stack-fremd, s. u.).
- Die 274 Model-/269 Controller-Dateien **einzeln** — Modul-Aussagen stützen sich auf das Vorgänger-Inventar + Live-Counts + Tabellennamen, **nicht** auf Zeile-für-Zeile-Code-Review. **Reifegrad je Modul ist daher teils übernommen/NICHT-VERIFIZIERT.**
- Playgrounds `Gesamtinventur-2026-06-11.md`, `BEFUND.md`, `crm_erp_fach_technikkonzept.md` (Inhalt) — Zeitbudget; als Nachlese markiert.
- Die „122-Punkte-Nav" als geschlossene Liste habe ich **nicht** als ein Artefakt gefunden (existiert verteilt über `navigation-vergleich-*`/NAV-01, andere Instanz). Ich nutze das ~84-Modul-Skelett als Checkliste und markiere das als **Abweichung von der Aufgabenformulierung**.

## Framework-Delta (belegt)
| | playground | ticket |
|---|---|---|
| Backend | Laravel 11/12, MySQL **`crm_erp`** | Laravel 11, MySQL `ticket` |
| Frontend | **React-SPA** (`app.jsx`/`src`, Vite) **+ Blade-Migration im Gange** (Vorgänger-Doc: ~180 Blade-Dateien, `views/modules/`-Baum) | **Blade + jQuery/Vuexy**, datengetriebene Sidebar, Alpine nur HK |
| Auth | **RBAC** (`roles`+`permissions`+Pivot, `role:*`/`permission:*`-Middleware), Session **+** API-Token | `is_admin`-Bypass + `user_rolls`; `users.name = employees.id` |
| Realtime | `laravel/reverb` (WebSockets) | kein Reverb |
**Kern-Delta:** playgrounds transplantierbarer Teil ist **ausschließlich die Laravel/Blade-Seite** (Models + Geschäftslogik + Blade-Bausteine). React-SPA, 3D-Dachplaner (Three.js), TS-Connector-Framework sind **stack-fremd → keine Übernahme, höchstens Konzept/SQL-Schema**.

---

# TEIL 1 — Modul-Inventur (Domänen-gruppiert, Urteil A/B/C)

> **A** = transplantieren · **B** = Konzept übernehmen, ticket-Bestand erweitern · **C** = verwerfen (Doppelung/Weiche-Verstoß/Fragment).
> Zeilenzahlen = live `crm_erp` (2026-07-05). „Ø" = Tabelle nicht gemessen (NICHT-VERIFIZIERT).

## 1. CRM / Kontakte / Kommunikation
**Was:** Kunden, Kundenakte, Kontakte/-arten/-vorlagen, Anfragen, Kommunikation (Chat/E-Mail via Reverb), Benachrichtigungen. Kontakt-„Intelligenz" + Resource-Layer.
**Daten:** `customers=14` · `objects=13` · `business_contacts=30` · `customer_contacts=0` · `inquiries=0` (leere Hülle). → **Sample-Daten.**
**ticket hat das?** **Ja, produktiv & reicher:** `new_leads` (~3000), `inquiries`/`inquiry.*`-Sichten, Kontakte, `lead_product_lists`, Chat + **Reverb-Video** (`video_calls`). ticket ist hier führend.
**Urteil:** **C** (Doppelung; ticket-CRM ist Live-Wahrheit). *Ausnahme:* Objekt-Ebene (`objects=13`) ist konzeptuell die W5-Klammer — aber die existiert in ticket schon als FK-Kette (`architektur-entscheidungen:140`) → **kein Import**.

## 2. Vertrieb: Angebote / Aufträge / Rechnungen / Verträge / Förderung
**Was:** Angebote (+Sets/Vorlagen/Versionen), Aufträge (+Bestätigungen, Labor-/Material-Zeilen), Rechnungen, Leistungen, Verträge, Förderungen. **Herzstück „Angebotsampel"** (Grün/Gelb/Rot blockiert Phasenwechsel bei fehlenden Pflichtdaten).
**Daten:** `angebot_offers=4` · `angebot_offer_items=1` · `orders=1` · `angebot_invoices=1` · `vertraege=0` · `foerderungen=0`. → **dünner Prototyp.**
**ticket hat das?** Angebote/Aufträge: **ja** (`Offer*`, `OfferDetail`, `deals`, `master_sets`, `deal_measurement_items`). Rechnungen: **ja, aber EINGEFROREN (Weiche 3, S1-Strang, `/invoices`)**. Förderungen: ticket hat `foerderungen` (Sidebar-Eintrag).
**Urteil:** Angebote/Aufträge/Rechnungen = **C** (Doppelung + `angebot_invoices` = **Weiche-3-Verstoß, Invoice-Zone tabu**). **Angebotsampel = B** (starkes Konzept, das ticket fehlt — Pflichtdaten-Gate vor Phasenwechsel passt zu W1/6; als ticket-Erweiterung, nicht als Code-Port).

## 3. Projekte / Montage / Aufmaß
**Was:** Projekte (+Akte/Profile/Phasen/Lohnkosten), Aufgaben (+Material/Nachweise), Bautagesberichte, **Feinaufmaß**, Montagevorbereitung, Innenaufträge, Entwicklungsberichte.
**Daten:** `projects=7` · `tasks=13` · `feinaufmasse=0` · `project_daily_reports=0`. → dünn.
**ticket hat das?** **Feinaufmaß: ja, produktiv** (`deal_measurements`/`deal_measurement_items`, DealMaterialListController — mein SEC-DM-Strang). Aufgaben: `personal_tasks`/`planner_items`. Montage: Planner/Kanban.
**Urteil:** **Projekt-EBENE = C (Weiche-5-Verstoß** — „Projekt" ist bewusst **keine** eigene Ebene, `architektur-entscheidungen:327-328`; ticket = Kunde→Objekt→Gewerk). **Feinaufmaß/Bautagesbericht/Aufgabennachweis-Konzepte = B** auf Gewerk-Ebene (ticket hat Feinaufmaß schon; Bautagesbericht/Nachweis-Fotoketten könnten ticket erweitern).

## 4. Disposition / Planung / Kalender
**Was:** Dispositionen, Termine, Planungs-Ansichten (gespeicherte Views), Kapazität/Produktivität, Personalressourcen/-zuordnungen.
**Daten:** `dispositionen=0` · `termine=0` · `personnel_assignments=0`. → **leere Hülle.**
**ticket hat das?** **Ja:** Planner (`/api/planner`, `planner_items`, `planner_plans`), Kanban, Termine/Appointments. ticket-Planner ist mobile-angebunden (Nuriva).
**Urteil:** **C** (Doppelung + leer). Kapazitäts-/Produktivitäts-Sicht evtl. **B** (Konzept), aber ohne Daten + Weiche-1-abhängig (Fortschritts-Wahrheit) → nachrangig.

## 5. Artikel / Lager / Einkauf
**Was:** Artikel (+Gruppen/Stückliste/technische-Daten/Import), Produktkatalog, Lieferanten, Bestellungen, Wareneingänge, Materialentnahmen, Lagerorte, Inventur. Excel-/DATANORM-Import.
**Daten:** `articles=398` (**größter Realbestand**) · `article_groups=18` · `lieferanten=6` · `bestellungen=0` · `wareneingaenge=0` · `bestaende=0`. → Katalog geseedet, Einkauf/Lager leer.
**ticket hat das?** **Ja, EIN Katalog ist Weiche** (ticket-`products`/`article_groups`/`master_sets`/`distributors`/`supplier_article_map` = **einzige Wahrheit**). Lager/Einkauf/Inventur: ticket teils (Bestellungen/Distributor-Preise).
**Urteil:** Artikel/Katalog = **C** (EIN-Katalog-Weiche; ticket gewinnt, playgrounds 398 Artikel = Sample). **Lager/Inventur/Wareneingang-Konzepte = B** falls ticket sie nicht hat (zu prüfen) — aber leere Hüllen, niedrige Prio. **Stückliste/technische-Daten** überlappt mit ticket-`master_sets` + wberechnung-`product_*_specs` → C/B.

## 6. HR / Personal / Lohnvorbereitung / Organisation
**Was:** Mitarbeiter, Arbeitsverträge, HR-Prozesse, Lohnarten, **Lohnvorbereitung mit Freigabe-Workflow**, Personalnachweise, Zeiterfassung/-auswertung, Überstunden, Abteilungen(+GuV), Teams, Niederlassungen, Gewerke.
**Daten:** `employee_profiles=14` · `departments=26` · `branches=5` · `trades=10` · `hr_time_entries=5` · `employment_contracts=1` · `hr_payroll_runs=0`. → Org geseedet, Lohn leer.
**ticket hat das?** Org: **ja** (`employees`, `departments`, `branches`, `position_qualifications`). Lohn-Vorbereitung: **nein**.
**Urteil:** Org/Abteilungen/Teams/Gewerke = **C** (ticket hat es). **Lohn-Vorbereitung = B/EINGEFROREN** — reiches Konzept, aber **Lohn/HR ist laut STRAENGE.md G-Strang-/Accounting-nah (tabu)** + payroll→DATEV berührt Weiche 3. **Nicht mein Scope zu bewerten → an Accounting/HR-Instanz.**

## 7. Buchhaltung (DATEV/GoBD) — größte Suite (~30 Submodule)
**Was:** Doppelte Buchführung, Journal (unveränderbar/Festschreibung), Konten/Kontenrahmen, Bank/Kasse, Belege, Rechnungsein-/ausgang, Offene Posten, Mahnwesen, Nummernkreise, Perioden/Monatsabschluss, Bilanz/BWA/SuSa/UStVA, Anlagen(AfA), Kostenstellen/Dimensionen, DATEV-EXTF-Export, GoBD-Protokoll, Prüfzentrum, Steuerberater-Portal.
**Daten:** `accounting_clients=1` · `accounts=17` · `accounting_journal_entries=6` · `accounting_outgoing_invoices=3` · `open_items=0` · `accounting_datev_exports=0`. → **Prototyp, DATEV-Testpaket lt. Vorgänger-Doc „nicht bestanden".**
**ticket hat das?** **Nein — und BEWUSST NICHT** (Weiche 3 + A1: **Kanzlei führt FiBu; keine Buchhaltung in ticket**).
**Urteil:** **⚠️ NEU 2026-07-05 — Weiche 3 von Yama AUFGEHOBEN** („Steuerberater ignorieren, Einfrieren weg, ausführen"). Damit nicht mehr C-eingefroren → **Wert-Kandidat**, ABER: großer Prototyp (45 Models, DATEV legally sensitiv, DATEV-Testpaket lt. playground **nicht bestanden**). **Bau = Accounting-Instanz, nach Cut-over** (nicht diese Inventur-Instanz). **Tiefer Reife-/Risiko-Befund s. Anhang E.**

## 8. Kundendienst
**Was:** Tickets (Nachrichten/Notizen), Serviceaufträge, Reklamationen.
**Daten:** `tickets=0` · `reklamationen=0`. → **leere Hülle.**
**ticket hat das?** Ticket-System i.e.S.: **nein** (ticket hat Deals/Video-Calls, kein Support-Ticket-Modul). Reklamation: unklar (NICHT-VERIFIZIERT).
**Urteil:** **B/A-Kandidat** (Konzept, das ticket fehlt) — aber **leere Hülle**, Reifegrad NICHT-VERIFIZIERT → niedrige Prio, erst Code-Reife prüfen.

## 9. Betriebsmittel / Fuhrpark
**Was:** Betriebsmittel/-arten mit Kosten, Nutzungen, Reservierungen, Wartungsereignissen, Prüfplänen (Fuhrpark/Maschinen).
**Daten:** `betriebsmittel=0`. → **leere Hülle.**
**ticket hat das?** **Nein** (NICHT-VERIFIZIERT, aber kein bekanntes ticket-Äquivalent).
**Urteil:** **A/B-Kandidat** (echte Lücke in ticket) — aber leer + Reife unbekannt → Prio mittel, Reife-Prüfung nötig.

## 10. Energie-Tools (nur Laravel/Daten-Seite)
**Was:** Wärmepumpe/WP-Auslegung, WR-Auslegung, **Lastmanagement/Lastprofil**, Konfigurator, Produktkatalog, Dachplaner (Blade-Hülle; 3D = React → raus).
**Daten:** `auslegungen=0` · `pv_modules=3` · `inverters=3` · `batteries=2` · `waermepumpe_specs=7` · `roof_tiles=94`. → Katalog-Seeds, Auslegungen leer.
**ticket hat das?** **Kommt gerade über den wberechnung-Cut-over** (`product_pv_module_specs`/`product_heat_pump_specs`/`inverters`/`batteries`, Marker-Interfaces, HK-Modul mit EN-442-Rechenkern). **Starke Überlappung.**
**Urteil:** **C (Doppelung mit wberechnung-Cut-over)** für WP/PV/WR-Auslegung + Produktkatalog. **⚠️ B-Kandidaten, SOLANGE die Energie-Architektur frisch ist:** **Lastmanagement/Lastprofil** (Wallbox-/Verbrauchs-Steuerung — kommt der wberechnung-Cut-over NICHT, Lücke!) und **Dachplaner-Datenbasis** (`roof_tiles=94`, `mounting_components`, `solar_mounts` — die 3D-UI ist React/raus, aber die **Belegungs-/Montage-Daten** könnten den ticket-Energie-Bereich ergänzen). **→ Verzahnungs-Weiche für Yama (s. TEIL 3).**

## 11. Formulare (dynamische Abfrage-Engine) — **A-Kandidat #1**
**Was:** Dynamische Formulare (Sektionen/Felder/Optionen), Formular-Antworten, Formular-Berechnung, **„Smartrouting" (FormRoutingService): Produktauswahl lädt automatisch Pflichtfelder/Dokumente/Aufgaben.**
**Daten:** `dynamic_forms=21` · `form_fields=358` (**echte Formular-Definitionen!**) · `form_answers=0`.
**ticket hat das? ⚠️ KORREKTUR 2026-07-05 (Yama-Hinweis, erst übersehen):** **JA** — ticket hat ein eigenes **„Checklisten-Formulare"-System**: `ProductFormula`/`product_formulas` (**generischer Builder je Produkt**: `product_id`→`article_groups`, `section_name`, **`fields` = JSON-Builder**, Publish-Status, **Version**, Antworten via `LeadProductChecklistValue` je Lead) + Builder-UI `admin/formula/{create,edit}` (Sidebar „Checklisten-Formulare", `product.formula.index`) + **hardcodierte Per-Produkt-Checklisten** (`heatpump_checklists`/`p_v_checklists`/`w_p_checklists` mit fixen Aufnahme-Feldern) + **Wartungs-Checklisten** + `knowledge/question`. **Schon W5-verankert** (article_group + Lead, kein „Project").
**Urteil:** **B — Synthese/erweitern, NICHT danebenstellen** (Yama-Direktive: „aus beiden das beste Formular"). ticket liefert die **W5-native Grundlage** (per-Produkt, Lead-Antworten, Builder-UI, Versionierung, Blade); playground liefert die **Engine-Stärken, die ticket fehlen** (eval-freie Calc-Engine + Operanden-Gate, `visible_if`, Feldtyp-Semantik, erweitertes Smartrouting nach Service/Phase/Objekt). → Verschmelzen, s. A.7. *(Selbstkritik: mein erstes „A/ticket lacks it" war ein Such-Fehler — nur `fusion.forms` geprüft, das ticket-Checklisten-System übersehen; vom Nutzer korrigiert.)*

## 12. Controlling / Strategie
**Was:** Controlling-KPI, Ziele (OKR), Abteilungs-GuV.
**Daten:** `controlling_kpis=0` · `okrs=0`. → leer.
**ticket hat das?** **Nein.**
**Urteil:** **B** (Konzept) — leere Hülle, niedrige Prio; Abteilungs-GuV berührt Weiche 3 (Buchhaltung) → EINGEFROREN-nah.

## 13. Plattform / Auth / System
**Was:** Auth, **RBAC (Rollen/Permissions, `permissions=85`)**, Systemmodule (`tenant_modules=0`), Einstellungen, Design-System, Uploads, Veranstaltungen, **Erkennung (Beleg-/Bild-OCR)**, `history_entries=14` (Append-only-Audit).
**ticket hat das?** Auth: ticket-`user_rolls`/`is_admin` (schwächer als playgrounds RBAC). OCR: ticket hat kein OCR (wberechnung-A-3d hat Grundriss-OCR, andere Instanz). Audit: ticket teils.
**Urteil:** **RBAC = B** (playgrounds Rollen/Permissions-Modell ist sauberer als ticket-`user_rolls`/`is_admin`-Bypass — Konzept für ticket-Härtung, **kein** Direkt-Port ins Live-Auth). **OCR/Erkennung = C** (gehört zum wberechnung-Grundriss-Strang). **history_entries (Append-only-Audit) = B** (Konzept). Design-System/Uploads/Veranstaltungen = C (ticket-eigenes Design ist verbindlich).

---

# TEIL 2 — Kollisions-Karte (nur A-/B-Kandidaten)

**A-Kandidaten:** #11 Formular-Engine (klar A) · #8 Kundendienst · #9 Betriebsmittel (A/B, reife-abhängig). **B-Konzepte:** Angebotsampel, Feinaufmaß-Nachweise, RBAC, Audit, Lastmanagement.

| Dimension | Kollision | Detail / Beleg |
|---|---|---|
| **Framework** | **HOCH** | playground = React-SPA + Laravel-**API**; ticket = Blade + jQuery. **Kein Blade-Port 1:1** — playgrounds Formular-UI ist teils React. **Nur Models + Services + Migrationsschema portierbar**, UI in ticket **neu in Blade/Vuexy** (wie beim wberechnung-Cut-over). |
| **Tabellen** | **MITTEL** | `dynamic_forms`/`form_sections`/`form_fields`/`form_field_options`/`form_answers` — Namen existieren in ticket vermutlich nicht (NICHT-VERIFIZIERT) → additiv, aber **Präfix/`imported_from`-Marker** nötig (Cut-over-Muster). `angebot_offer_item_calculations` koppelt an playgrounds Angebots-Tabellen → **beim Port entkoppeln** (ticket-Offer-Schema ≠ playground). |
| **Auth** | **HOCH** | playgrounds Services setzen **RBAC `role:*`/`permission:*`** voraus; ticket nutzt `is_admin`/`user_rolls`. Portierte Endpunkte brauchen **Gate-Übersetzung auf ticket-Auth** (nicht playground-RBAC mitschleppen — sonst zwei Auth-Systeme). |
| **Routen** | **MITTEL** | playground = `routes/modules/{modul}.php` (120 Dateien); ticket = ein `web.php` + `/api/planner`. Neue `formulare.*`-Routen additiv, **Namensraum prüfen** (kein `inquiry.*`/`deal.*`-Clash). |
| **Assets** | **MITTEL** | playground React/Vite-Bundle vs ticket Vuexy/jQuery. **Keine Asset-Übernahme**; Alpine nur falls Yama es (wie HK) freigibt. |
| **Weichen** | **KRITISCH** | Buchhaltung/Rechnung (§2/§7) = **W3 tabu**; Projekt-Ebene (§3) = **W5-Verstoß**; Katalog (§5) = **EIN-Katalog**; Energie (§10) = **Cut-over-Doppelung**. → diese fallen aus A raus, **bevor** Kollision überhaupt relevant wird. |

---

# TEIL 3 — Transplantations-Roadmap (A-Module priorisiert)

**Priorisierung = Nutzwert × (1/Aufwand) × Abhängigkeiten. Bau erst NACH wberechnung-Cut-over-Abschluss.**

### Prio 1 — Formular-Engine + Smartrouting (§11, A) + Angebotsampel-Gate (§2, B)
- **Nutzwert hoch** (echte Lücke, 358 Feld-Definitionen = Fachlogik), **Aufwand mittel** (Models+Service portierbar, UI neu in Blade).
- **Stufenplan (Cut-over-Muster):**
  1. **Schema additiv:** `dynamic_forms`/`form_sections`/`form_fields`/`form_field_options`/`form_answers` als ticket-Migrationen (nullable, FKs `nullOnDelete`, `imported_from='playground'`-Marker).
  2. **Daten mit Marker:** die 21 Formulare / 358 Felder als Seeder (Marker), **Rückbau-Statement** dokumentiert.
  3. **Adapter:** `FormRoutingService` nach ticket portieren, Auth auf ticket-Gates übersetzen, an ticket-Produktauswahl (`article_groups`/`master_sets`) koppeln statt an playground-Artikel.
  4. **Views in ticket-Design:** Formular-Renderer als Blade/Alpine (Yama-Freigabe für Alpine wie bei HK), **kein React-Port**.
  5. **Angebotsampel** als Pflichtdaten-Gate obendrauf (koppelt an W1-Phasen — **hängt an Weiche 1**, daher NACH W1).
- **Verzahnung:** unabhängig vom wberechnung-Cut-over; **hängt an Weiche 1** (Ampel-Gate) und an der ticket-Offer-Struktur.

### Prio 2 — Lastmanagement / Lastprofil (§10, B) — **nur solange Energie-Architektur frisch**
- **Wichtig JETZT:** der wberechnung-Cut-over liefert WP/PV/Auslegung, aber **nicht** Wallbox-/Lastmanagement (Lücke). Solange `product_*_specs`/Marker-Interfaces frisch sind, günstig andockbar.
- **⚑ Weiche für Yama:** Gehört Lastmanagement + Dachplaner-Datenbasis (`roof_tiles`/`mounting_components`/`solar_mounts`) **in den laufenden Energie-Cut-over** (dann andere Instanz) oder **separat später**? **Nicht meine Entscheidung** — ich markiere die Überlappung.
- Stufenplan analog (Schema→Daten-Marker→Adapter→Blade), **koordiniert mit der Energie-/Cut-over-Instanz** (sonst Doppel-Schema).

### Prio 3 — Kundendienst (§8) + Betriebsmittel/Fuhrpark (§9) — reife-abhängig
- Echte ticket-Lücken, aber **leere Hüllen** + **Reifegrad NICHT-VERIFIZIERT**. **Vor Roadmap-Aufnahme: Code-Reife-Befund** (fertig vs. Fragment). Erst dann A oder C.

### Prio 4 — B-Härtungs-Konzepte (kein Modul-Port)
- **RBAC** (§13) → ticket-Auth-Härtung (Konzept, koordiniert mit einer Security-Instanz — berührt meinen SEC-DM-Strang thematisch, aber **anderer Scope**).
- **Append-only-Audit** (`history_entries`) → Konzept für ticket.

**Explizit NICHT auf der Roadmap (raus):** Buchhaltung/Rechnung (W3), Projekt-Ebene (W5), Artikel-Katalog (EIN Katalog), CRM/Angebote/Aufträge/Disposition/Org (ticket-Doppelung), WP/PV-Auslegung (Cut-over), React-SPA/3D-Dachplaner/TS-Connectoren (stack-fremd), Lohn/HR (andere Instanz).

---

# TEIL 4 — Daten-Frage

**Hat playground LIVE-Daten, die ticket braucht? → NEIN.**
Belegt über 57 Live-COUNTs: playground trägt **dünne Sample-/Seed-Daten** — `customers=14`, `angebot_offers=4`, `orders=1`, `projects=7`, `tasks=13`, Buchhaltung/Lager/Disposition/Kundendienst/OKR = **0 oder Prototyp-Seeds**. **ticket** ist das Live-CRM mit **~3000 Kunden** — playgrounds Kundendaten sind Testdaten und wandern **nicht**.

**Substanzielle Bestände (aber KEIN Migrationsbedarf):**
| Bestand | Zeilen | Warum trotzdem nicht migrieren |
|---|---|---|
| `articles=398` | 398 | **EIN-Katalog-Weiche:** ticket-Artikel-DB = einzige Wahrheit → playground-Artikel = Sample, raus. |
| `form_fields=358` / `dynamic_forms=21` | 358/21 | **Das ist Konfiguration/Code, keine Geschäftsdaten** — wandert als **Seeder mit dem Formular-Engine-Port** (Prio 1), nicht als Datenmigration. |
| `roof_tiles=94` | 94 | Referenz-/Katalog-Daten der Dachbelegung — **nur falls** Lastmanagement/Dachbasis-Weiche (Prio 2) positiv; dann als markierter Seeder, nicht als Live-Migration. |

**Fazit TEIL 4:** Es wandern **NUR Code + Konzepte + Konfigurations-Seeds** (Formular-Definitionen), **keine** echten Kunden-/Angebots-/Buchhaltungs-Daten. Genau die Cut-over-Erwartung (Testdaten).

---

# Selbstkritik / Grenzen (ehrlich)
- **Reifegrad je Modul ist überwiegend NICHT-VERIFIZIERT** — ich habe 274 Models/269 Controller **nicht** einzeln gelesen, sondern aus Vorgänger-Inventar + Live-Counts + Tabellennamen abgeleitet. „fertig/Prototyp/Fragment" ist teils **übernommen**, nicht neu belegt. **Vor jedem A-Bau: Code-Reife-Befund je Modul.**
- **„122-Punkte-Nav" nicht als Artefakt gefunden** — ich nutze das ~84-Modul-Skelett; die exakte 122-Zählung (vermutlich Submodul-granular, v. a. Buchhaltungs-30) ist **nicht abgeglichen**. Abweichung von der Aufgabe offengelegt.
- **Zähl-Delta** playground (164/274/269 heute vs. 173/297/279 im Vorgänger-Doc) **nicht aufgelöst** — Code hat sich geändert; ich habe die Differenz nicht kartiert.
- **Konzept-Docs** (`crm_erp_fach_technikkonzept.md` 147 KB, `BEFUND.md`, `Gesamtinventur`) **inhaltlich nicht gelesen** — dort steckt vermutlich präziserer Reife-/Ketten-Status (z. B. „Angebot→Auftrag-Endpoint fehlt" laut Vorgänger). **Nachlese-Empfehlung** vor A-Entscheidung.
- **Lastmanagement/Dachbasis + Kundendienst/Betriebsmittel-Reife** sind die **wichtigsten offenen Prüfpunkte** (Verzahnung Energie-Cut-over bzw. leer+unklar).
- **Weiche-3-/W5-/Katalog-/Energie-Ausschlüsse** habe ich streng angewandt; falls eine Weiche später kippt, ändert sich das Urteil (v. a. Buchhaltung).

**→ STOPP (TEIL 1–4).** A-Liste unten in Anhang A vertieft.

---

# Anhang A — Formular-Engine: Code-Reife-Befund (2026-07-05, read-only)

> Auflage aus der Selbstkritik erfüllt: Engine **wirklich gelesen** (nicht aus Counts), Reife belegt, `fusion.forms`-Abgrenzung, Aufwand ehrlich. Quellen: Code `backend-laravel/app/{Models,Services,Http/Controllers}` + playgrounds **eigener Audit** `docs/projekt/formular-modul-bestandsaufnahme.md` (2026-06-02, am echten Code geprüft).

## A.1 Architektur (wörtlich gelesen)
- **Models (6):** `DynamicForm`(40 Z.) → `FormSection`(113 live) → `FormField`(62 Z.) → `FormFieldOption`(489 live) · `FormAnswer` · **`FormRoutingRule`**(45 Z.). `FormField`-Baukasten: `options_json`,`validation_rules_json`,`calculation_json`,`visible_if_json`,`recognition_json`,`min/max_value`,`decimals`,`internal_only`/`external_visible`,`depends_on_field_key`.
- **Services:** `FormRoutingService`(91 Z., Smartrouting) · `Services/Form/FormulaEvaluationService`(902 Z., Berechnung) · `ErkennungController`+`EntityHistoryService` (OCR-Kette + Audit).
- **Controller dual:** `Api/*` (FormBuilder/FormCalculation/FormAnswer/DynamicForm/ProjectForm — für React) **+** `Modules/*` (Blade-Admin-Listen). **API-first.**
- **Live-Bestand:** 21 Formulare · 113 Sektionen · **358 Felder** (select=150, number=49, text=30, textarea=27, **length=21/area=5/power=3/plz** = technische Aufnahme, calculation=14) · 489 Optionen · **14 Routing-Rules**.

## A.2 Smartrouting (belegt, `FormRoutingService:12-58`)
`resolveForProject(Project)` → je Projekt-**Interest** (`product_id`+`service_id`+`interest_type`+`group_id`) matchende `FormRoutingRule` (null=Wildcard) + `object_type`, sortiert nach `priority`×100 + Spezifitäts-Score (Dimensionen inkl. **`phase_id`/`progress_id`**). Basis-Formulare immer dazu. **Elegant, generisch, phasen-fähig.**

## A.3 Berechnungs-Engine (belegt, `FormulaEvaluationService:7-45`) — passt zu tickets „ehrlicher Datenlage"
Sicherer **Shunting-Yard→RPN, KEIN `eval()`**; Whitelist +,-,*,/, `SUM/MENGE/FLAECHE/VOLUMEN`, Feld-Slugs+Literale. **Operanden-Gate:** `is_assumption`/ungeprüfter Erkennungswert → `enthaelt-ungepruefte-werte` (berechnet, **nicht verbindlich**); fehlender Pflicht-Operand → `unvollstaendig`, **keine erfundene Zahl**. → **konzeptuell identisch mit meinem S-3-Prinzip (NULL statt 0).**

## A.4 Reife — ehrlich (playground-Audit `bestandsaufnahme.md` + Code)
| Ebene | Reife | Beleg |
|---|---|---|
| Read-API | ✅ | `DynamicFormController` index/show, Tests, RBAC `permission:formulare.view` |
| Schreibpfad (Antworten) | 🟡 | Option-Validierung select/multiselect **fehlt**; min/max/decimals fließen nicht ein; **Status ohne Zustandsmaschine** (DB `begonnen` ≠ Ctrl `eingereicht`) — `:52/62` |
| Berechnungs-Engine | 🟡 **6/10** | Design stark, Score-Lücken `:15` |
| Bedingte Sichtbarkeit `visible_if` | 🔴 **nicht implementiert** | „nur Spalte, KEINE Auswertungslogik" `:81` |
| Fundament-Migrationen | 🔴 | Basistabellen **nur Raw-SQL** `crm_erp_mysql_schema.sql:388-463`, **nicht migrationsbasiert** `:51/56` |
| **Frontend (Renderer+Builder)** | 🔴 **fehlt** | „nur Backend-API, React/TS-Oberfläche fehlt vollständig" `:95`; kein Blade-Renderer |
| Feldtyp-Katalog | 🟡 generisch | 40 generische statt 40 Fachtypen; ~19 fehlen `:96` |
| OCR-Erkennungskette | ✅ reif | `ErkennungController` Statuskette+Tests `:64` *(aber `UNSICHERE_STUFEN` rein deskriptiv `:66`)* |
| RBAC/Audit | ✅ | `permission:*`-Gating + `EntityHistoryService` `:68-69` |

**Gesamturteil:** **starkes Konzept + solide Backend-Logik, aber NICHT produktionsreif** (Raw-SQL-Fundament, Schreibpfad-Lücken, `visible_if` fehlt, **Frontend komplett fehlend**). Kein Fragment — „fortgeschrittener Prototyp mit belegten Lücken".

## A.5 `fusion.forms`-Abgrenzung (definitiv)
ticket-`fusion.forms` = `FusionFormSubmissionController` (Namespace **`Wordpress`**) — passiver **Import/Sync von Website-Einsendungen** (Goneo/WordPress → Leads), Tabellen `fusion_forms`/`fusion_fields`. **Kein Builder/Calc/Routing.** playground-Engine = **interne dynamische Aufnahme-Formulare**. → **Verschiedene Zwecke, keine funktionale Überlappung, keine Tabellen-Kollision** (`fusion_*` ≠ `dynamic_forms`/`form_*`). **Erweitern statt danebenstellen? NEIN** — additives neues Modul; **in der Nav klar trennen** (fusion=„Website-Eingang", dynamische Formulare=„Aufnahme/Konfigurator") + `imported_from='playground'`-Marker.

## A.6 Transplantations-Aufwand (ehrlich — Blade-Neubau-Anteil HOCH)
| Teil | Aufwand | Anmerkung |
|---|---|---|
| Models + Read-API + Schreibpfad + Calc/Routing/OCR | portierbar (mittel) | reine Laravel-Logik, **mit Gap-Fixes** (Option-Validierung, Status-Maschine, `visible_if`-Logik NEU) |
| Basistabellen-Migrationen | klein, aber **NEU schreiben** | Raw-SQL nicht portierbar → echte Migrationen (Cut-over-Muster, Marker) |
| **Re-Anchoring Weiche 5** | **mittel-hoch** | `FormRoutingService` hängt an `Project`+`interests` → auf ticket **Kunde→Objekt→Gewerk** umhängen (`lead_product_list`/`deal`+`object`; Routing-Dim = `article_group`/`phase_section`/`lead_stage`). Kern-Anpassung. |
| **Frontend (Renderer+Builder)** | **HOCH — ~100 % Blade-Neubau** | playground hat **kein** übernehmbares Frontend → Feld-Renderer (20 Typen), Baukasten-UI, Antwort-Formulare, `visible_if`-JS **komplett neu in ticket-Blade/Vuexy** (Alpine nur mit Yama-Freigabe). |

**Fazit:** Backend-**Wert hoch** (geronnene Fachlogik + prinzipientreue Calc-Engine), **Aufwand nicht klein** (UI-Neubau + Re-Anchoring). **Mehrstufig (Cut-over-Muster):** (i) Schema+Marker, (ii) Models+Read-API, (iii) Calc+Routing re-anchored, (iv) Schreibpfad+Gaps, (v) Blade-Renderer, (vi) Baukasten-UI, (vii) OCR optional.

---

# Anhang B — B-Konzepte als ticket-Verbesserungs-Merker (kein Code-Port)
- **Angebotsampel = Pflichtdaten-Gate vor Phasenwechsel** → FK-Kanban-**`changeStage`-Guard** (blockiert Phasenwechsel bei fehlenden Pflichtdaten). Kandidat für spätere **Kanban-Ausbaustufe** (koppelt an Weiche 1). Merker.
- **RBAC-Ablösung `is_admin`/`user_rolls`** → **eigener späterer Strang (groß!)**, mit Security-Instanz abstimmen.
- **Append-only-Audit** (`EntityHistoryService`/`history_entries`) + **Feinaufmaß-Nachweisketten** = Muster-Referenzen für ticket-Härtung.

# Anhang C — Energie-Verzahnungs-Weiche (an die Cut-over-Instanz)
Der Cut-over liefert WP/PV/Auslegung, **nicht** `lastprofile`/`lastmanagement` (Wallbox/Verbrauch) und **nicht** die Dachbelegungs-Datenbasis (`roof_tiles=94`/`mounting_components`/`solar_mounts`). **Frage (Yama entscheidet):** additive Schema-Erweiterung **JETZT** in die frische Energie-Architektur, damit später nichts umgebaut wird? **Nur Datenmodell + Laravel-Logik** — React/Three.js-3D-UI bleibt raus. Ich treffe keine Entscheidung; ich lege die Verzahnung offen.

---

## A.7 Bau-Scope — **SYNTHESE ticket ⊕ playground** (entschieden 2026-07-05), Bau NACH Cut-over
**Direktive Yama:** aus **beiden** Systemen das **beste** Formular bauen — ticket-Bestand (`product_formulas` + Per-Produkt-Checklisten) **als Grundlage erweitern**, playground-Engine-Stärken **einpflanzen**. Kein Parallel-Modul, keine Doppelung. Mittlere Tiefe: Renderer + Smartrouting + Calc; voller Builder minimal (ticket hat schon einen JSON-Builder).

**Grundlage (bleibt, ticket-nativ):** `product_formulas` (per `article_group`, `fields`-JSON, Version, Publish) + `LeadProductChecklistValue` (Lead-/Gewerk-Antworten) + Builder-UI `admin/formula/*` + Blade-Design. **Schon W5-verankert** — das erspart das große Re-Anchoring.

**Aus playground einpflanzen (die Lücken, die ticket fehlen):**
- **(a) Calc-Engine 1:1** (`FormulaEvaluationService`): Shunting-Yard, **kein `eval`**, **Operanden-Gate** (`ungeprüft`/`unvollständig` → keine erfundene Zahl) — **prinzipientreu übernehmen**; ticket-`fields`-Felder um `calculation`/Einheit erweitern.
- **(b) `visible_if`-Logik** (bedingte Sichtbarkeit `Feld=Wert`) — **Pflicht** (große Aufnahme-Formulare sonst unbenutzbar); ticket-`fields`-JSON um Bedingungen erweitern.
- **(c) Feldtyp-Semantik + Option-/Wert-Validierung** (length/area/power/plz, select gegen Optionen, min/max/decimals) — die technischen Aufnahme-Feldtypen aus playgrounds 358 Feldern in ticket-`fields` heben.
- **(d) Erweitertes Smartrouting** (playgrounds `FormRoutingRule`-**Logik**): heute lädt ticket per `product_id`; ergänzen um **Service/Gewerk + Objekt-Typ + Phase** (`lead_product_list.service` / `lead_alternative_adds.object_type` / `lead_stages`). **Nur die Routing-Logik, an ticket-Anker** — kein `Project`/`interests`.
- **(e) Fachlogik-Konserve:** playgrounds 21 Formulare/358 Felder/489 Optionen als **Vorlagen-Import** (Marker `imported_from='playground'`) in tickets `product_formulas`-Struktur überführen — die geronnene Fach-Aufnahme nicht verlieren.

**Weiche (Yama/Bau-Session):** JSON-`fields` (ticket) **beibehalten & erweitern** vs. auf normalisierte `form_fields`-Tabellen (playground) migrieren? **Empfehlung: JSON behalten** (ticket-nativ, weniger Umbau), Calc/visible_if/Feldtypen **im JSON-Schema** ergänzen — nur falls Abfragen über Felder nötig werden, normalisieren.
**NICHT im Scope:** voller Drag-Drop-Builder · React-Reste · OCR-Kette (später) · RBAC (eigener Strang) · playgrounds Raw-SQL-Tabellen 1:1 (ticket-Struktur gewinnt).

**Pflicht vor Bau:** eigener **ticket-Checklisten-System-Befund** (ProductFormulaController + `fields`-JSON-Schema + `LeadProductChecklistValue` + die hardcodierten `*_checklists` wörtlich lesen) — damit die Synthese am echten ticket-Stand ansetzt, nicht an Annahmen. *(Dieser Befund fehlt noch — ich habe playground gelesen, tickets Formular-System nur angerissen.)*

**→ STOPP (Synthese-Scope).** Eingefroren; Bau nach Cut-over. Vor Bau: ticket-Formular-System-Befund.

---

# Anhang D — Kundendienst + Betriebsmittel: Reife-Befund (2026-07-05, read-only, freigegeben parallel)

> Gleiche Tiefe wie Anhang A: Code **wirklich gelesen**, Reife ehrlich, ticket-Lücke (**Nav-Bereich 9**) gegen Aufwand. Daten beider Domänen = **0** (nur Code/Konzept wandert).

## D.1 Kundendienst (Tickets · Reklamationen · Serviceaufträge)
**Architektur (gelesen):** Models `Ticket`/`TicketNachricht`/`TicketNotiz`/`Reklamation`/`Serviceauftrag`/`Service`. **Echte Migrationen** (`create_tickets_schema` = 3 Tabellen tickets+nachrichten+notizen; `create_reklamationen_schema` = 1). Controller: **`Api/TicketController` (258 Z.)** — voll: `index/show/store/update/destroy` **+ `updateStatus` (Status-Workflow) + `addNachricht`/`addNotiz` (Threaded) + `EntityHistoryService`-Audit**; **`Api/ReklamationController` (217 Z.)** — Status + **Fristen/Überfälligkeit (SLA-artig: `frist < now` & nicht abgeschlossen → überfällig)** + Priorität/Kategorie + Status-Whitelist `Rule::in(Reklamation::STATUS)`. Blade = dünne Listen (`Modules/*Controller` 76/61 Z.).
**Reife:** **Api-Backend mittel-reif/funktional** (CRUD + Status + Threading + SLA + Audit — sauber, getestet-Muster). **Frontend fehlt** (Blade nur Listen; Bearbeitungs-UI war React). Daten leer.
**ticket hat das?** **Nein** — Nav-Bereich 9 (Tickets/Reklamationen/Wartung) ist **echte Lücke**.
**Urteil:** **A-Kandidat #2** — realer Nav-Lücken-Bezug + reifes Api-Backend. **Aufwand deutlich unter Formular-Engine** (Standard-CRUD, KEIN Smartrouting/Calc): Re-Anchoring an ticket-`new_leads`/`deals` + Standard-Blade-CRUD-UI. Gutes Nutzwert/Aufwand-Verhältnis.

## D.2 Betriebsmittel / Fuhrpark
**Architektur (gelesen):** Models `Betriebsmittel`/`BetriebsmittelArt`. **Echte Migration** `create_betriebsmittel_schema` = **2 Tabellen** (`betriebsmittel_arten` + `betriebsmittel`: Inventarnr/Status/Zustand/Hersteller/Standort/`verantwortlicher_user_id`/Anschaffungswert/Kostenstelle/`naechste_wartung_at`/`naechste_pruefung_at`/`meta_json`). Controller `Api/BetriebsmittelController` (281 Z., voll-CRUD + Audit).
**Reife:** **funktionales Asset-Register**, aber **flacher als das Vorgänger-Inventar behauptete** — die dort genannten Sub-Tabellen (Kosten/Nutzungen/Reservierungen/Wartungsereignisse/Prüfpläne) **existieren in dieser Migration NICHT** (nur `naechste_wartung/pruefung_at`-Felder). **NICHT-VERIFIZIERT**, ob Sub-Tabellen in anderer Migration; im gelesenen Schema = 2 Tabellen.
**ticket hat das?** **Nein.**
**Urteil:** **B / A-niedrig** — sauberes kleines Modul, echte Lücke, aber **geringere Tiefe + geringerer Betriebswert** als Kundendienst. Quick-Win-A möglich (kleine Blade-CRUD), sonst B (später). **Korrektur zum Vorgänger-Inventar: keine 6-Tabellen-Fuhrpark-Suite belegt.**

## D.3 Priorisierung (Nachtrag zu TEIL 3)
1. **Formular-Engine** (A, entschieden, s. A.7).
2. **Kundendienst** (A-Kandidat #2) — Nav-Bereich-9-Lücke, reifes Backend, moderater Aufwand. **Empfehlung: nächster A nach Formular.**
3. **Betriebsmittel** (A-niedrig/B) — Quick-Win oder Backlog, Yama entscheidet; Tiefe klein.

---

**→ STOPP.** Formular-Bau-Scope eingefroren (A.7); Kundendienst = klarer A-Kandidat #2; Betriebsmittel = klein. **Alle Bauten erst nach Cut-over.**

---

# Anhang E — Buchhaltung / DATEV: Reife-/Risiko-Befund (2026-07-05, read-only)
**Anlass:** Yama hebt **Weiche 3 auf** („Steuerberater ignorieren, Einfrieren weg, ausführen"). Notiert als Entscheidung. **Dieser Befund = read-only, mein Scope; der BAU gehört der Accounting-Instanz + nach Cut-over.**

## E.1 Umfang (Struktur-Scan)
**18 Migrationen · 45 Models · voller Service-Layer.** Domänen (belegt an Services/Tabellen): Mandant (`accounting_clients`) · **Kontenrahmen** (`accounts`/`account_mappings`) · **doppelte Buchführung** (`accounting_journal_entries`+`accounting_journal_lines`) · Belege (`accounting_documents`) · Ein-/Ausgangsrechnungen · **Bank + `BankMatchingService`** · Offene Posten · **Mahnwesen** (`dunning`) · Fristen · **GoBD Maker-Checker-Gates** (`AccountingGateDecisionService`/`GateReleaseService`) · **DATEV-EXTF-Export + `DatevExtfKonformitaetService`** · **Bilanz/BWA/SuSa/UStVA/AfA** (`BalanceSheet`/`Bwa`/`Afa`-Service) · Adjustment-Suggestions.

## E.2 Reife — ehrlich = **Prototyp**
- **Datenfüllung dünn:** `accounting_clients=1` · `accounts=17`/`mappings=17` · `journal_entries=6`/`lines=12` · `outgoing_invoices=3` — **aber `documents=0`, `incoming_invoices=0`, `open_items=0`, `datev_exports=0`, `dunning=0`.** → Kontenrahmen + Test-Buchungen, **kein echter Beleg-/Export-/OP-Fluss gelebt.**
- **DATEV-Konformität NICHT bewiesen:** `DatevExtfKonformitaetService` ist ein **Prüfer, der „Blockgründe" liefert** (Header/Encoding/CSV/Feldformate) — d. h. Konformität ist ein zu prüfender Zustand, nicht garantiert. Playgrounds eigenes Vorgänger-Inventar: **„DATEV-Testpaket nicht bestanden, offene GoBD-Punkte".**
- **NICHT-VERIFIZIERT:** die 45 Models einzeln, die tatsächliche EXTF-Export-Korrektheit, die GoBD-Festschreibungs-Vollständigkeit — dafür bräuchte es einen **dedizierten Accounting-Fach-Befund** (Accounting-Instanz), nicht meinen Struktur-Scan.

## E.3 ticket-Kontext + Weichen-Umkehr
ticket hat **keine** Buchhaltung — nur das **Invoice-Modul** (`/invoices`, S1-Strang: Nummernkreis/Storno/Teilzahlung). **A1 war entschieden: „Kanzlei führt FiBu, keine Buchhaltung in ticket".** Yama kehrt das jetzt um → **A1 + Weiche 3 im `architektur-entscheidungen.md` nachziehen** (nicht meine Datei — **an Yama/Accounting-Instanz**), sonst divergiert die dokumentierte Architektur.

## E.4 ⚠️ Risiko-Hinweis (Sorgfaltspflicht — trotz „ignorieren")
Ein **nicht-DATEV-zertifiziertes Accounting-Prototyp** in ein **Live-CRM mit ~3000 echten Kunden** zu heben und daraus **steuerrelevante DATEV-Exporte/UStVA/Bilanz** zu erzeugen, ohne Steuerberater-Validierung, ist ein **reales rechtliches/steuerliches Risiko** (GoBD-Unveränderbarkeit, Festschreibung, EXTF-Konformität sind prüfungsrelevant). Ich sage das als Befund — **entscheiden tust du**; ich baue nichts blind.

## E.5 Urteil (Inventur)
**Hoher konzeptioneller + Code-Wert** (vollständige FiBu-Architektur mit GoBD-Gates + DATEV-EXTF + Auswertungen — das gibt es in ticket nicht), **aber NICHT produktionsreif + legally sensitiv.** → **Kein Transplant, sondern dediziertes Accounting-Projekt** (eigene Instanz, nach Cut-over), mit **eigenem Fach-Reife-Befund + DATEV-Zertifizierungs-Pfad** als Pflicht-Vorstufe. **Wert = A (groß), Aufwand + Risiko = sehr hoch.**

---

**→ STOPP (Anhang E).** Buchhaltung/DATEV: Weiche 3 aufgehoben, Reife = Prototyp, Risiko-Hinweis, Bau = Accounting-Instanz nach Cut-over.

---

# Anhang F — PV-3D-Planer: Datenmodell + Rechenkern (2026-07-05, read-only) · **ZEITKRITISCH für Cut-over**
**Scope (Yama):** NUR portierbares **Laravel-Datenmodell + Rechenkern**. Die **3D-UI (Three.js/React) bleibt raus** = Stack-Neubau. Zeitkritisch, weil der Cut-over die Energie-Architektur **jetzt** formt — additive Schema-Entscheidungen sind jetzt billig.

## F.1 Montage-/Dach-Katalog — **ECHT gefüllt (geronnene Fachlogik), A-Wert**
| Tabelle | Zeilen | Inhalt |
|---|---|---|
| `roof_tiles` | **94** | Ziegel-/Eindeckungstypen |
| `roof_coverings` | 15 | Dacheindeckungen |
| `solar_mounts` | 7 | Montagesysteme |
| **`tile_solar_mount`** | **220** | **Ziegel↔Montage-Kompatibilität** |
| **`mounting_components`** | **66** | Montage-Komponenten |
| **`mounting_roof_compat`** | **111** | **Komponente↔Dach-Kompatibilität** |
`roof_templates=0`/`energie_roof_models=1`/`auslegungen=0` = leer (Testdaten). → **Die Kompatibilitäts-Matrizen (220+111) sind der Wert** — welche Halterung auf welchen Ziegel/welches Dach passt = schwer neu zu erzeugende Montage-Fachlogik. **Empfehlung: additive Schema-Übernahme JETZT durch die Cut-over-Instanz** (Marker `imported_from='playground'`), solange die Energie-Architektur frisch ist.

## F.2 PV-Rechenkern — reif, ~1210 Z., **an die wberechnung-Naht andockbar**
`InverterSizingService`(553) · `StringBuilderService`(161) · `KabelService`(153) · `PerformanceService`(140) · `SchutzkomponentenService`(108) · `PvBelegungExtractor`(95, extrahiert Belegung aus 3D-`dach_json`).
- **`InverterSizingService`** (gelesen): normgerechte Strangauslegung (Spannungsfenster n_min/n_max, Strang-Sicherung 1,25², DC/AC-Ratio) mit **Ampel grün/gelb/rot + Normbezug** und **Elektrofachkraft-Haftungshinweis** — Qualität wie mein HK-EN-442-Kern.
- **⭐ Kern-Verzahnung:** nutzt **`App\Services\Energie\Contracts\Sizing{Inverter,Module,Battery}`** — **dieselbe Marker-Interface-Naht, die der wberechnung-Cut-over als Adapter-Naht gebaut hat.** → playgrounds PV-Sizing **plugt konzeptuell in die vorhandene Cut-over-Naht**, statt neu erfunden zu werden.

## F.3 Verzahnung mit wberechnung-Cut-over (was „konzeptionell anpassen" heißt)
- Der Cut-over bringt **Specs** (`product_pv_module_specs`/`inverters`/`batteries`) + die **WP-Seite**. Playground bringt die **PV-Sizing-/Montage-Seite** (Strang/WR/Kabel/Schutz + Dach-/Montage-Katalog) — **komplementär, nicht doppelt.**
- **Empfehlung an die Cut-over-Instanz (Yama entscheidet):** (a) Montage-/Dach-Katalog jetzt additiv ins Schema; (b) playgrounds PV-Rechenkern über die **bestehende `Sizing*`-Naht** andocken (Adapter, nicht Neubau); (c) Doppelungen bei WR-Auslegung zwischen playground und wberechnung **konzeptuell zusammenführen** (eine Sizing-Wahrheit). `dach_json` = 3D-Layout-**Datenvertrag** (portabel, auch wenn die 3D-UI neu gebaut wird).

## F.4 Urteil (Inventur)
- **Montage-/Dach-Datenmodell = A, ZEITKRITISCH** (echte Kompatibilitäts-Fachlogik, jetzt billig additiv).
- **PV-Rechenkern = A** (reif, normgerecht, **an die Cut-over-Naht andockbar** — konzeptionell anpassen, nicht neu bauen).
- **3D-UI (Three.js) = C/Neubau** (stack-fremd, separater späterer UI-Strang; `dach_json`-Kontrakt bleibt).
- **Bau/Andock = Cut-over-/Energie-Instanz, nach Cut-over** — ich liefere nur diesen Befund an sie.

---

**→ STOPP (Anhang F).** PV-3D: Montage-Katalog echt → additiv jetzt; Rechenkern an `Sizing*`-Naht andockbar; 3D-UI = Neubau.

---

# Anhang G — Lohn / Gehalt: Reife-/Risiko-Befund (2026-07-05, read-only)
**Scope:** read-only, mein Job; Bau = HR-/Accounting-Instanz nach Cut-over.

## G.1 Umfang + Wesen
**Lohn-VORBEREITUNG, kein Netto-Lohnlauf.** Migrationen: `extend_payroll_for_preparation`, `lohn_ueberstunden_lohnarten`, **`lohn_korrektur_pruefung_freigabe`** (Korrektur→Prüfung→Freigabe-Workflow), **`lohn_uebergabe_dokumente`** (Übergabe ans Lohnbüro), `hr_shifts`, `hr_clock_events`. Services: `Hr/PayrollCheckService`, `Hr/PayrollStatusService`, `Hr/ProjektLohnkostenService` (Lohnkosten je Vorgang), `ArticleMountingTimeService` (Montagezeit→Lohn). → **Sammelt Zeiten/Lohnarten/Überstunden, führt Freigabe-Workflow, erzeugt Übergabe-Dokumente** fürs externe Lohnbüro/DATEV-Lohn. **Rechnet KEINE Löhne/SV/Lohnsteuer selbst.**

## G.2 Reife = **Framework, ungenutzt**
`employee_profiles=14` · `employment_contracts=1` · `hr_time_entries=5` — dünner Seed. **`hr_payroll_runs=0` · `entries=0` · `wage_lines=0` · `approvals=0` · `exports=0` · `wage_types=0`** → die **Payroll-Maschinerie wurde nie gelaufen**. Framework steht, Betrieb = 0.

## G.3 ticket-Kontext + Risiko-Klasse
ticket hat **HR-Org** (`employees`/`departments`/`branches`/`position_qualifications`), **keine Lohn-Vorbereitung**. → echte Lücke. **Aber gleiche Risiko-Klasse wie Buchhaltung** (Anhang E): Lohn → **Lohnsteuer/SV/Lohnbüro/DATEV-Lohn** = rechtlich/steuerlich sensibel; ein ungenutztes Vorbereitungs-Framework in ein Live-System zu heben, das steuer-/sv-relevante Übergaben erzeugt, verlangt **Fach-Reife-Befund + Lohnbüro-/Berater-Einbindung** als Pflicht-Vorstufe. *(Nebenbefund: „Projekt-Lohnkosten" berührt Weiche 5 → auf Gewerk/Objekt umankern.)*

## G.4 Urteil (Inventur) — bestätigt Yamas Erwartung
**Wert = A (Konzept: Lohn-Vorbereitung + Freigabe-Workflow + Übergabe), Reife = Prototyp (ungenutzt), Risiko + Aufwand hoch.** → **Dediziertes HR-/Payroll-Projekt** (eigene Instanz, nach Cut-over) mit **eigenem Fach-Reife-Befund + Berater-/Lohnbüro-Pfad**. **Gleiches Urteil wie Buchhaltung** (E.5), wie von dir vorhergesagt.

---

# Zusammenfassung der 4 „großen Brocken" (Yama-Priorität)
| Brocken | Wert | Reife | Zuständigkeit / Timing |
|---|---|---|---|
| **PV-3D-Datenmodell + Rechenkern** (F) | **A** | Katalog echt (220+111), Rechenkern reif, an `Sizing*`-Naht andockbar | **ZEITKRITISCH → Cut-over-Instanz JETZT** (additives Schema); 3D-UI = Neubau (raus) |
| **Buchhaltung/DATEV** (E) | A (groß) | **Prototyp, DATEV nicht bewiesen** | Accounting-Instanz nach Cut-over; **DATEV-Zert.+Steuerberater PFLICHT**, Risiko hoch |
| **Lohn/Gehalt** (G) | A | **Framework, ungenutzt** | HR-Instanz nach Cut-over; **Berater/Lohnbüro PFLICHT**, Risiko-Klasse wie Buchhaltung |
| **Formular-Engine/Smartrouting** (A/A.7) | B (Synthese) | Backend solide, Frontend fehlt | ticket ⊕ playground **verschmelzen**, nach Cut-over |

**→ STOPP (Inventur vollständig).** Alle vier von dir priorisierten Brocken befundet (E/F/G + A.7-Synthese) + TEIL 1–4 + Kundendienst/Betriebsmittel (D). **Kein Bau — reine Inventur.** Nächste Züge liegen bei dir: (1) den **PV-3D-Befund (F) an die Cut-over-Instanz** geben (zeitkritisch), (2) Accounting-/HR-Instanzen mit E/G beauftragen (nach Cut-over, mit Berater-Pfad), (3) `architektur-entscheidungen.md` A1/Weiche 3 nachziehen lassen. Ich bleibe für weitere Befunde/Vertiefungen bereit.
