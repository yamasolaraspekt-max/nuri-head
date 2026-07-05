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
**Urteil:** **C / EINGEFROREN** — Weiche-3-Verstoß + parallele Accounting-Instanz besitzt das Thema. **Ich bewerte es nicht weiter; Verweis an die Accounting-Instanz.** (Größter Einzelblock von playground fällt damit raus.)

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
**ticket hat das?** **Nein** — ticket hat keine generische Formular-Engine (NICHT-VERIFIZIERT, aber kein bekanntes Äquivalent).
**Urteil:** **A (transplantieren)** — echte Lücke, substanzieller Realbestand (358 Felder = geronnene Fachlogik), koppelt an Angebotsampel/Pflichtdaten-Gate (B aus §2). **Stärkster Transplantations-Kandidat.**

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

**→ STOPP.** Yama entscheidet die A-Liste (Empfehlung: **Formular-Engine als Prio-1-A**; Lastmanagement als Energie-Verzahnungs-Weiche; Kundendienst/Betriebsmittel erst nach Reife-Befund). Gebaut wird erst nach Abschluss des wberechnung-Cut-overs.
