# wberechnung → ticket — Übernahme-Inventur (Vergleichs-Landkarte)

**Stand:** 2026-07-11 · **read-only** · **kein Bau/Import/Kopie/Migration/Refactor/Automatisierung.**
**Quellen (firsthand):** ticket-Systeminventur (3 Runden), `docs/playground-uebernahme-inventur.md`,
`docs/cutover-wb-abschlussbilanz.md` (2026-07-05), `database/data/wberechnung_import.php`,
ticket-Ist `app/Services/{Heizlast,Energie,Klima}/*` + `routes/web.php` + `resources/views/admin/energie/*`
(live 2026-07-11), wberechnung-App `/Users/yamanuri/Herd/wberechnung` (read-only).
**Geltung:** `docs/rueckfall-archiv-regeln.md` ist verbindlich (kein Löschen/Überschreiben ohne Rückfallpfad).

> **Ein-Satz-Befund:** Der wberechnung-**Energie-Rechenkern ist seit dem 05.07. weitgehend nach ticket
> transplantiert UND verdrahtet** (eigener `/admin/energie/*`-Bereich mit Controllern, Views, PDF-Ausgaben) —
> die offene Frage hat sich von *„portieren"* zu *„die 3 isolierten Kron-Services (`BivalenzService`,
> `HeizlastService`, `PvProjektService`) einhängen und die Kette an die CRM-Anker koppeln"* verschoben.

---

## 1. Was aus bisherigen wberechnung-Übernahmen in ticket bereits weiter gilt

Aus `cutover-wb-abschlussbilanz.md`, `wberechnung-katalog-cutover-status` (Memory) und den Cut-over-Docs — **weiterhin gültig**:

1. **ticket ist führend, wberechnung ist Quelle/Referenz** — nie umgekehrt. Kollision → Adapter oder additive Spalte, nie destruktiv (DAUERDIREKTIVE).
2. **Heizkörper-Modul ist vollständig übernommen** (erster Cut-over, live-testgrün): `RadiatorPerformanceService` (EN-442 `qReal`), `HydraulicService`, `CompatibilityService` (Regeln 1–6), `RadiatorCatalogAdapter`, HK-Katalog (30 Kermi via `RadiatorSpecSeeder`/`product_radiator_specs`), HK-Schema (9 Migrationen), HK-UI (Konfigurator + Ampel + Stückliste). **Bleibt unangetastet** (jQuery, kein Alpine-Umbau) — CLAUDE.md-Regel.
3. **Geräte-Katalog wb ist eingefroren** (W-C2): WP (19, `en14511_nenn`, datenblatt-verifiziert Buderus/NIBE/Viessmann) + AIKO Neostar 2P (2) + LONGi Hi-MO 7/LR7 (3) sind via `WberechnungImportSeeder` in `products`/`product_heat_pump_specs`/`product_pv_module_specs`. Geräteänderungen laufen ab jetzt **in ticket**; wb-Gerätedaten = nur Herkunftsnachweis. `database/data/wberechnung_import.php` = eingefrorene Quelle, **nicht manuell editieren**.
4. **Referenz-Kataloge übernommen** (B2a-1): `materials` (23), `konstruktionen` (5), `baualtersklassen` (25), `klima_plz` (8168) — je Zeile `verifikations_status` (din_belegt/tabula_richtwert), material_id-Remap wb→ticket. Über `ReferenzKatalogSeeder` + `database/data/b2a_referenz.php` + `klima_plz.csv`.
5. **Heizlast-Kern byte-genau portiert** (B2a-3, Diff=0): `HeizlastRechner` + `RaumHuelleService` + `HeizlastNormwerte` (H_T=17,26 · Φ_HL 892/948 W · spez. 37,9 W/m²).
6. **Bewusst verzichtet** (gilt weiter): wb-Procurement (A4 → ticket-OMD-Stack), wb-User/Auth (ticket-Auth gilt), wb-Framework/Config, Fenster-Dummies, HK-Katalog-Duplikat.
7. **Datenschutz-Regel „eine Wahrheit je Sachverhalt":** kein Parallelbetrieb; wb wird erst abgeschaltet, wenn alle B-Slots portiert **und** M5 (HK produktiv) + B5 (Grundriss) erledigt sind.

---

## 2. Was durch diese neue Untersuchung ergänzt oder korrigiert wurde

> Die Abschluss-Bilanz war ein **Zeitstand 2026-07-05**. Zwischen dem 05.07. und heute (11.07.) ist der Cut-over **massiv weitergelaufen**. Das ist die zentrale Korrektur.

**K-1 (KORREKTUR, groß).** Abschlussbilanz Teil B sagt: *„kein dieser Rechenkerne ist in ticket — FEHLT für Heizlast, Waermepumpe, WpKennlinie, Bivalenz, Pvgis, Sanierungs, Fussboden, Energiekonzept, PvProjekt, Klima."* **Das stimmt heute nicht mehr.** Live gezählt in ticket (2026-07-11):
- `app/Services/Heizlast/` (20 Dateien): `HeizlastRechner`, `HeizlastService`, `HeizlastProjektService`, `RaumHuelleService`, `HeizlastNormwerte`, `HeizlastEingabe`, `HeizlastKonstanten`, `GeometrieAbleitungService`, `UWertService`, `WarmwasserService`, `VerbrauchsService`, `KlimaBinService`, `HoehenkorrekturService`, `WpKennlinieService`, `BivalenzService`, `JazService`, `WaermepumpenMatchService`, `SanierungsWirtschaftlichkeitService`, `FoerderungService`, `FussbodenheizungService`.
- `app/Services/Energie/`: `InverterSizingService`, `PvProjektService`, `PvgisErtragService`, `KostenService`, `Contracts/*` (SizingInverter/Battery/Module), `Dto/*`.
- `app/Services/Klima/`: `KlimaPlzService`.
- **8 Controller** unter `Http/Controllers/Energie/` + **15 Views** unter `resources/views/admin/energie/` + **eigener Routenblock** `routes/web.php` ab ~Z. 5464 (`/admin/energie/{wr-auslegung,wp-auslegung,sanierung,energiekonzept,grundriss,materialliste,fussboden-check,heizlast,plan-upload}`).

**K-2 (ERGÄNZUNG, entscheidend für den Workflow).** Die offene Arbeit ist nicht mehr „portieren", sondern **„verdrahten"**. Live-Aufrufer-Zählung der Kron-Services (Suche in `app/` + `routes/`, ohne die Service-Datei selbst, ohne Tests):

| Service | Aufrufer | Status |
|---|---:|---|
| **`BivalenzService`** | **0** | **ISOLIERT** — Krone der WP-Auslegung (Bivalenzpunkt, JAZ, **E-Stab-Anteil**, **Laufstunden**, **Stromverbrauch**, Warnungen) nicht an eine Route gehängt |
| **`HeizlastService`** | **0** | **ISOLIERT** — vermutlich wb-Alt-Wrapper, in ticket von `HeizlastProjektService` (4 Aufrufer) überholt → Rückfall-/Klärungs-Kandidat |
| **`PvProjektService`** | **0** | **ISOLIERT** — PV-Projekt-Orchestrator; `InverterSizingService` wird direkt genutzt, der Bündler nicht |
| `WpKennlinieService` | 2 | verdrahtet (via `BivalenzService` + `Contracts/WpKennlinie`) — aber sein einziger Fach-Konsument (`BivalenzService`) ist selbst isoliert → **Kette endet blind** |
| `JazService` | 2 | verdrahtet (EnergiekonzeptController, EnergieAuslegungController) |
| `WaermepumpenMatchService` | 1 | verdrahtet (HeizlastController) |
| `HeizlastProjektService` | 4 | verdrahtet (GrundrissController, HeizlastController, +2 Services) |
| `KlimaPlzService` | 6 | stark verdrahtet (4 Controller + NormTempService + AnforderungsprofilService) |
| `KlimaBinService` | 2 | verdrahtet |
| `SanierungsWirtschaftlichkeitService` | 2 | verdrahtet |
| `FoerderungService` | 3 | verdrahtet |
| `FussbodenheizungService` | 2 | verdrahtet |
| `InverterSizingService` | 2 | verdrahtet |
| `PvgisErtragService` | 1 | verdrahtet (EnergiekonzeptController) |
| `UWertService` / `WarmwasserService` / `VerbrauchsService` | 3/3/1 | verdrahtet |

**K-3 (ERGÄNZUNG, Workflow-Bruch).** Der WP-Auslegungs-Endpoint `EnergieAuslegungController::wpBerechnen` nutzt `JazService`, **aber nicht `BivalenzService`** → die WP-Route berechnet JAZ, überspringt aber das **Bivalenz-Ranking** (Bivalenzpunkt + E-Stab-Anteil + Laufstunden + Strom). Das ist genau die Fach-Substanz (VDI 4645/4650), die schon gebaut, aber nicht erreichbar ist.

**K-4 (ERGÄNZUNG).** Auch **Grundriss/Plan-Import** (in der Abschlussbilanz noch als B5-offen geführt) ist **teilweise transplantiert**: `GrundrissController` (index/editor/vorschau/speichern) + `PlanUploadController` + Views `grundriss.blade.php`/`grundriss_editor.blade.php`/`plan_upload.blade.php` + Routen existieren. Offen bleibt vermutlich `MassstabVorschlagService` (wb hat `Import/MassstabVorschlagService`, in ticket nicht gefunden).

**K-5 (ERGÄNZUNG).** **PDF-/Dokument-Ausgaben sind mit-transplantiert:** `*_dokument.blade.php` (wp/wr/sanierung/energiekonzept) + `*Dokument`-Controller-Methoden + Routen (`.../dokument`). Das deckt die wb-Report-Views (`energiekonzepte/angebot`, `wr-auslegung/protokoll`, `heizlast-projekt/nachweis`) fachlich ab.

**K-6 (ERGÄNZUNG, Rest-Referenz).** wberechnung-Services, die in ticket (noch) **nicht** existieren → wb bleibt dafür Referenz: `OpenMeteoKlimaService` (Live-Klima-Abgleich), `AuslegungService`, `WpHandoffService`/`AuslegungHandoffService` (Übergabe-Bündler), `GeocodingService`, `StringBuilderService` (PV-Strang), `InverterSuggestionService`, `PerformanceService`, `Import/MassstabVorschlagService`. Datei `wp_material_sets.json` (WP-Materialsets/Hydraulik-Paket je WP) in wb vorhanden, in ticket-`database/data/` **nicht** gefunden → Rest-Referenz-Kandidat.

**K-7 (KORREKTUR der Ampel).** Abschluss-Ampel „🟡 14/215 übernommen, 201 offen" ist **überholt nach oben**: der gesamte Energie-Rechenkern (Heizlast/WP/PV/Klima/Wirtschaftlichkeit/Förderung/Fußboden/Energiekonzept/Grundriss) ist inzwischen transplantiert + größtenteils verdrahtet. Realistischer Reststand: **~3 isolierte Services + ~8 Rest-Referenz-Services + M5 (Prod-Deploy)** — nicht mehr „201 offen". *(Exakte Neuzählung nicht Teil dieser read-only-Inventur; Zahl bewusst als Näherung markiert.)*

---

## 3. Fach-Feld-Matrix (die von Yama benannten Prüf-Felder)

Legende Verdrahtung: 🟢 verdrahtet (Route→Controller→Service→View) · 🟡 gebaut, aber isoliert/blind · ⚪ nur in wb (Rest-Referenz).

| Fach-Feld | wb-Quelle | ticket-Ort | Verdrahtung | Rest-Referenz in wb? |
|---|---|---|---|---|
| **Heizlast** DIN EN 12831 | `HeizlastRechner`, `RaumHuelleService`, `HeizlastNormwerte` | gleiche Namen in `Services/Heizlast/*` (byte-genau, Diff=0) | 🟢 via `HeizlastController` + `HeizlastProjektService` | nein (`HeizlastService`-Wrapper 🟡 isoliert) |
| **Verbrauchsmethode** | `VerbrauchsService` | `Services/Heizlast/VerbrauchsService` | 🟢 (EnergieAuslegungController) | nein |
| **PLZ / Klima / NAT / Heizgradtage** | `KlimaPlzService`, `KlimaBinService`, `HoehenkorrekturService`, `OpenMeteoKlimaService` | `Klima/KlimaPlzService` (6 Aufrufer), `Heizlast/KlimaBinService`, `HoehenkorrekturService` + `klima_plz` (8168) | 🟢 stark | ⚪ `OpenMeteoKlimaService` (Live-Klima) fehlt in ticket |
| **WP-Kennlinien** | `WpKennlinieService` + `waermepumpen_kurven.json` | `Services/Heizlast/WpKennlinieService` + `wberechnung_wp_kurven.json` (`en14511_nenn`) | 🟡 nur von isoliertem `BivalenzService` genutzt | nein |
| **Bivalenz** (VDI 4645/4650) | `BivalenzService` | `Services/Heizlast/BivalenzService` | **🟡 0 Aufrufer — Krone nicht erreichbar** | nein |
| **JAZ / COP / SCOP** | `JazService` | `Services/Heizlast/JazService` | 🟢 (2 Controller) | nein |
| **Heizstab-Anteil** | Ergebnis aus `BivalenzService::berechne()` | in `BivalenzService` (E-Stab) | **🟡 blind** (Service isoliert) | nein |
| **Betriebsstunden** | `BivalenzService` (Laufstunden) | in `BivalenzService` | **🟡 blind** | nein |
| **Stromverbrauch** | `BivalenzService` (Strom) | in `BivalenzService` | **🟡 blind** | nein |
| **Wirtschaftlichkeit** | `SanierungsWirtschaftlichkeitService` | gleicher Name | 🟢 via `SanierungController` (`/admin/energie/sanierung`) | ⚪ `AuslegungService`/`Handoff` teils fehlend |
| **Förderung** BAFA/KfW | `FoerderungService` | gleicher Name | 🟢 (3 Aufrufer) | nein |
| **Heizkörper / Vorlauf / Hydraulik** | `HeizkoerperService`, `HydraulikService` | `Heizkoerper/RadiatorPerformanceService` + `HydraulicService` + `CompatibilityService` | 🟢 eigener HK-Strang (live-testgrün, Konfigurator-UI) | nein (voll übernommen) |
| **PV** | `PvProjektService`, `StringBuilderService` | `Energie/PvProjektService` | **🟡 `PvProjektService` 0 Aufrufer** | ⚪ `StringBuilderService` fehlt |
| **Wechselrichter** | `InverterSizingService`, `InverterSuggestionService`, `WrAuslegungController` | `Energie/InverterSizingService` | 🟢 via `EnergieAuslegungController` (`/admin/energie/wr-auslegung`) | ⚪ `InverterSuggestionService` fehlt |
| **Batterie** | `Contracts/SizingBattery`, `batterie_wr_kompatibilitaet` (Tab.) | `Energie/Contracts/SizingBattery` | 🟡 Contract da, `batterie_wr_kompatibilitaet` = W-C4-Rest (offen) | ⚪ n:m-Kompat-Tabelle |
| **Produkt-/Katalogdaten** | `waermepumpen.csv`, `artikel` | `wberechnung_import.php` → `products`/`product_heat_pump_specs`/`product_pv_module_specs` | 🟢 (Katalog eingefroren W-C2) | nein |
| **Angebots-/PDF-/Report-Ausgaben** | `energiekonzepte/angebot`, `wr-auslegung/protokoll`, `heizlast-projekt/nachweis` | `*_dokument.blade.php` + `*Dokument`-Methoden + Routen | 🟢 | teilweise ⚪ (Layout-Details) |
| **Importdaten / Seeder / JSON / CSV / PHP** | `klima_plz.csv`, `waermepumpen.csv`, `waermepumpen_kurven.json`, `wp_material_sets.json` | `klima_plz.csv`, `wberechnung_import.php`, `wberechnung_wp_kurven.json`, `b2a_referenz.php` + Seeder (`Wberechnung`/`ReferenzKatalog`/`RadiatorSpec`) | 🟢 | ⚪ `wp_material_sets.json` (WP-Materialsets) nicht in ticket gefunden |

---

## 4. Vergleich entlang der ticket-Kapitel A–N

wberechnung ist eine **Ein-Domänen-App (Energie/Auslegung)** — inhaltlich fast ausschließlich **Kapitel D** (Angebot/Auslegung/Konfiguration), mit Ausläufern nach F (Katalog) und K (Angebots-/PDF-Ausgabe). Für B/C/E/G/H/I/L/M liefert wb **nichts** (das leisten ticket-Kern + playground).

| Kap | Thema | wb-Beitrag | ticket-Status | Einordnung |
|---|---|---|---|---|
| **A** | Systemlandkarte | — | ticket führend | — |
| **B** | Eingang/Lead/Kunde/Objekt | — | ticket (`new_leads`/`lead_alternative_adds`) | wb liefert nichts |
| **C** | Vertrieb/CRM-Prozess | — | ticket + playground | wb liefert nichts |
| **D** | **Angebot/Auslegung/Konfiguration** | **gesamter Energie-Rechenkern** | **transplantiert + verdrahtet, 3 isolierte Services** | **Kern der wb-Übernahme** |
| **E** | Auftrag/Deal | — | ticket (`deals`) | wb liefert nichts |
| **F** | Produkte/Katalog/Sets/Preise | Geräte-Katalog (WP/PV) eingefroren | in `products`/Specs | übernommen (W-C2) |
| **G** | Beschaffung/Lager | wb-Procurement | **A4-Verzicht** (ticket-OMD) | nicht übernehmen |
| **H** | Planung/Disposition | — | ticket-Planner | wb liefert nichts |
| **I** | Montage/Ausführung | — | ticket | wb liefert nichts |
| **J** | Doku/Abnahme/Nachweise | Nachweis-/Protokoll-Views | `*_dokument`-Views übernommen | übernommen |
| **K** | Rechnung/Zahlung/FiBu | — | ticket-FiBu (isoliert) + playground | wb liefert nichts |
| **L** | Controlling/Nachkalkulation | Wirtschaftlichkeit (Sanierung) | `SanierungsWirtschaftlichkeitService` 🟢 | übernommen |
| **M** | Service/Betrieb | — | — | wb liefert nichts |
| **N** | Querschnitt Arch/Sec/Perf/UX | Alpine-Scope (heizkoerper/formulare) geregelt | CLAUDE.md-Grenze | Grenze gilt |

---

## 5. Übernahmekategorien A–F

**A — bereits in ticket übernommen/gebaut:** Heizkörper-Modul (voll, live) · Referenz-Kataloge (`materials`/`konstruktionen`/`baualtersklassen`/`klima_plz`) · Geräte-Katalog (19 WP + AIKO/LONGi, eingefroren) · Heizlast-Kern (byte-genau) · **der gesamte Energie-Rechenkern + `/admin/energie/*`-Bereich** (Heizlast/WP/PV/Klima/Wirtschaftlichkeit/Förderung/Fußboden/Energiekonzept/Grundriss/PDF) — *größtenteils verdrahtet.*
**B — gebaut, aber isoliert → nur verdrahten (kein Neubau):** `BivalenzService` (0 Aufrufer, Krone) · `HeizlastService` (0, Alt-Wrapper klären) · `PvProjektService` (0) · WP-Route um Bivalenz-Ranking ergänzen (E-Stab/Laufstunden/Strom erreichbar machen).
**C — Idee/Norm übernehmen, wb-Code nicht neu:** VDI-4645/4650-Bivalenz-Logik (schon als Code da) · DIN-EN-12831-Normwerte (da) · Verzahnung mit CRM-Ankern (Objekt `lead_alternative_adds` / Gewerk `lead_product_list`) statt wb-`heizlast_projekte`.
**D — bereits in ticket vorhanden (wb = Doppelung/Herkunft):** WP/PV/WR-Auslegung · Klima-Daten · Heizlast · Geräte-Katalog · Referenz-Kataloge · PDF-Ausgaben.
**E — nicht übernehmen (Verzicht/Risiko):** wb-Procurement (A4) · wb-User/Auth · wb-Framework/Config · Fenster-Dummies · HK-Katalog-Duplikat.
**F — unklar, Tiefenprüfung nötig:** Rest-Referenz-Services (`OpenMeteoKlimaService`, `StringBuilderService`, `InverterSuggestionService`, `AuslegungService`, `*HandoffService`, `MassstabVorschlagService`) — real gebraucht oder verzichtbar? · `wp_material_sets.json` (Materialsets) übernehmen? · `batterie_wr_kompatibilitaet` (W-C4-Rest) · Grundriss-Rest (`MassstabVorschlagService`) · exakte Neuzählung des Reststands.

---

## 6. Zukunftsliste (Vorschlag, **nicht** Freigabe · Priorität · Baustein · Kapitel · Nutzen · Risiko · Übernahmeart · Voraussetzung)

| Prio | wb-/Energie-Baustein | ticket-Kap | Nutzen | Risiko | Übernahmeart | Voraussetzung |
|---|---|---|---|---|---|---|
| 1 | **`BivalenzService` verdrahten** (WP-Route → Bivalenz-Ranking, E-Stab/Laufstunden/Strom erreichbar) | D | **hoch** (Fach-Krone, VDI 4645/4650) | niedrig (Code + Tests da) | **verdrahten** (B) | Klärung Eingabe-Anker (Objekt/Gewerk) |
| 2 | `HeizlastService` (0 Aufrufer) klären: Alt-Wrapper archivieren **oder** einhängen | D | Aufräumung/Klarheit | niedrig | Befund + ggf. Archiv (Variante B) | Rückfall-/Archiv-Regel |
| 3 | `PvProjektService` verdrahten (PV-Bündler an WR-Route) | D | mittel-hoch | niedrig | verdrahten (B) | PV-Workflow-Weiche |
| 4 | Energie-Kette an **CRM-Anker** koppeln (Objekt `lead_alternative_adds` / Gewerk statt Stand-alone-Formular) | D/B | hoch (Integration statt Insel) | mittel | Adapter (C) | Zielbild objekt-zentriert |
| 5 | Rest-Referenz-Services (F) prüfen: `OpenMeteoKlima`/`StringBuilder`/`InverterSuggestion`/`Massstab` — brauchen wir sie? | D | mittel | niedrig | Tiefenprüfung (F) | — |
| 6 | `wp_material_sets.json` (WP-Materialsets/Hydraulik-Paket) übernehmen? | D/F | mittel | niedrig | Datenmodell (F) | Materiallisten-Workflow |
| 7 | `batterie_wr_kompatibilitaet` (W-C4-Rest) | D | mittel | mittel | n:m-Adapter (B) | PV/WR-Sizing-Slot |
| — | **M5 — HK + Katalog produktiv auf main** (Prod-Migration ~3000 Kunden) | D/F | Voraussetzung wb-Abschaltung | **hoch** | Deploy-Fenster (Yama) | Backup + Rollback-Plan |

---

## 7. Offene Fragen an Yama

1. **Isolierte Kern-Services:** `BivalenzService`, `HeizlastService`, `PvProjektService` sind 0-Aufrufer. Sollen sie **verdrahtet** (Prio 1/3) oder — bei `HeizlastService` — als überholt **archiviert** werden (Variante B, kein Löschen)? *(Nur Klärung, kein Bau in dieser Runde.)*
2. **WP-Route ohne Bivalenz:** Der WP-Auslegungs-Endpoint rechnet JAZ, aber **nicht** das Bivalenz-Ranking. Ist das gewollt (JAZ reicht) oder eine zu schließende Lücke?
3. **Energie-Verankerung:** Soll die Energie-Kette an die **CRM-Anker** (Objekt `lead_alternative_adds` / Gewerk `lead_product_list`) gekoppelt werden statt als Stand-alone-`/admin/energie/*`-Tool zu laufen — jetzt oder später?
4. **Rest-Referenz-Services (F):** `OpenMeteoKlimaService`, `StringBuilderService`, `InverterSuggestionService`, `MassstabVorschlagService`, `AuslegungService`/`*HandoffService` — welche davon werden real gebraucht (übernehmen) und welche entfallen?
5. **`wp_material_sets.json`:** WP-Materialsets/Hydraulik-Pakete übernehmen (Materiallisten-Nutzen) oder Verzicht?
6. **wb-Abschaltung:** Ist wberechnung nach dem heutigen Stand (Rechenkern in ticket) **näher an abschaltbar** als die 05.07.-Bilanz sagt? Bestätigst du, dass die Restabhängigkeit ~F-Services + M5 ist — und soll ich (in einer **späteren, freigegebenen** Runde) eine exakte Neuzählung machen?
7. **`deal_invoices`** (aus playground-Inventur offen) — bleibt hier unberührt, nur Querverweis.

---

## 8. Evaluator-Notiz

- **Belegt (firsthand, 2026-07-11):** ticket-Ist der Energie-Services + Aufrufer-Zählung (grep in `app/`+`routes/`), Routenblock `/admin/energie/*`, Views `resources/views/admin/energie/*`, `database/data/*`; wberechnung-App-Struktur (49 Services/15 Controller/45 Migr/31 Views/56 Tests, read-only). Die 3 isolierten Services (`BivalenzService`/`HeizlastService`/`PvProjektService` = 0 Aufrufer) sind live verifiziert.
- **[Grundlage: `cutover-wb-abschlussbilanz.md` 2026-07-05, NICHT komplett neu verifiziert]:** Commit-Hashes der HK-/Katalog-/Referenz-Ports, Test-Zählungen (26 HK grün etc.), Zeilen-Soll der Kataloge — als Zeitstand 05.07. übernommen und wo überholt in §2 korrigiert.
- **Offene Suchlücken (bewusst nicht in read-only-Runde gelöst):** exakte Neuzählung des Reststands (A/B/offen) nach dem gewachsenen Cut-over; ob `HeizlastService` wirklich toter Wrapper ist (nicht Zeile-für-Zeile gegen `HeizlastProjektService` diffed); genaue Deckung der `*_dokument`-Views gegen die wb-Report-Layouts; ob `MassstabVorschlagService`/`wp_material_sets.json` in ticket unter anderem Namen doch existieren.
- **Keine vorschnelle Empfehlung:** Kategorien A–F und Zukunftsliste sind **fund-basiert**, nicht priorisierte Umsetzungsentscheidung. Verdrahten ≠ freigegeben.
- **Nicht gemacht (korrekt):** kein Bau, kein Import, kein Kopieren, keine Migration, kein Refactor, keine Automatisierung, **kein Gesamtkonzept**, keine Strategieentscheidung, kein Commit. `docs/rueckfall-archiv-regeln.md` beachtet (nichts gelöscht/überschrieben).

---

*Nächster Schritt laut Auftrag: **STOPP.** Das Gesamtkonzept (`docs/gesamt-konzept-ticket-playground-wberechnung.md`) wird jetzt **noch nicht** erstellt — Yama prüft zuerst diese Inventur.*
