# Startblock — Bereich 2: WP/PV-Formularübernahme aus playground in die ticket-Engine

**Stand:** 2026-07-12 · **read-only Planungs-Startblock** · **kein Bau/Import/Refactor/Migration/Kopieren.**
**Zweck:** Prüfen und planen, wie die vorhandenen **playground-Formularinhalte für WP und PV** in die **bestehende ticket-Formular-Engine / `product_formulas`** überführt werden — **ohne** das playground-`dynamic_forms`-System als zweite Wahrheit. **Nur Startblock; die Analyse/Konzept-Runde folgt erst nach Yama-Freigabe.**
**Geltung:** `docs/ARBEITSREGELN.md` sowie die fachlichen Ziel-Wahrheiten Bereich 2
(Anker=Anforderungsprofil, Positionen=`offer_details.sections`, Preis=P1-a).

---

## 1. Domäne / Kapitel
- **Bereich 2 — Angebot, Auslegung & Kalkulation.**
- **Teilpaket:** WP/PV-Formulare → ticket-Engine (`product_formulas`) → Bedarf/Auslegung → Angebot (`offer_details.sections`).
- **Rolle:** Planner (read-only). Generator/Evaluator erst nach separater Bau-Freigabe.

## 2. Startpunkt
- **ticket-Formular-Engine + `product_formulas`** (Engine gebaut, DB leer — 0 Formulare).
- **playground WP/PV-`dynamic_forms`-Inhalte** (Feld-Definitionen, als reines „Bauteile-Lager").
- **ticket WP/PV-Auslegungstools** (`Heizlast/*`, `Energie/*`).
- **`offer_details.sections`** als Zielstruktur (führende Positions-Wahrheit).
- **P1-a `CatalogPriceGuard`** als Preis-Wahrheit (Katalog-Pricing).

## 3. Ziel (+ Nicht-Ziele)
**Ziel:** WP/PV-Formularinhalte aus playground **bewerten**; entscheiden **welche Felder** übernommen werden; **Mapping auf die ticket-Engine** (schema v2) planen; die Kette **Formular → Bedarf → technische Werte → Angebotspositionen** planen.
**Nicht-Ziele (ausdrücklich):** kein Import, kein Bau, kein Feld-Seeder, keine Migration, keine zweite Formularwelt (`dynamic_forms`), keine React-SPA, keine zweite Angebotsstruktur, **nicht** alle 21 Formulare — **nur WP/PV**.

## 4. Was gelesen/geprüft werden muss (genaue Leseliste)

### ticket (firsthand)
- `app/Models/ProductFormula.php` + Migrationen `*product_formulas*` (+ `add_schema_version…`) — Zielschema (`fields`-JSON v2, `product_id`→`article_groups`, `section_name`, `status`).
- `app/Services/Form/FormSchemaValidator.php` (v2, 22 Feldtypen inkl. length/area/volume/power/calculation) — was die Engine akzeptiert.
- `app/Services/Form/FormulaEvaluationService.php` (eval-frei: SUM/MENGE/FLAECHE/VOLUMEN, Operanden-Gate) — welche Formeln direkt laufen.
- `app/Services/Form/VisibleIfService.php` (Operatoren `= != > < >= <= in not_in`) — welche `visible_if`-Regeln direkt laufen.
- `app/Http/Controllers/Product/ProductFormulaController.php` (+ `LeadProductChecklistValueController`) — Erfassung/Speicherung der Antworten.
- WP/PV-Auslegung: `app/Services/Heizlast/*` (HeizlastRechner, WaermepumpenMatchService, BivalenzService, JazService) + `app/Services/Energie/*` (InverterSizing, PvgisErtrag, PvProjekt) + `app/Services/Anforderungsprofil/*` (Bedarfs-Anker, `SchluesselRegistry`).
- Angebot/Ziel: `Offer`/`OfferFolder`/`OfferDetail` + `sections`-Knotenstruktur; `app/Services/Offer/CatalogPriceGuard.php` (P1-a).
- Vorhandene Views/Wizards: `resources/views/admin/energie/*` (Auslegungs-Tools) + `resources/views/admin/offer/configuration/offer/config.blade.php` (Angebots-Wizard).

### playground (firsthand, `/Users/yamanuri/Documents/Playground/backend-laravel`, nur lesen)
- **WP-Formulare:** `produkt_waermepumpe`, `aufmass_waermepumpe`, `fachwerkzeug_heizlast`, `fachwerkzeug_waermepumpe_konfigurator` (SQL-Seed `database/sql/crm_erp_form_seed.sql` + `AufmassFormSeeder`/`HeizlastFormSeeder`/`WaermepumpenKonfiguratorSeeder`).
- **PV-Formulare:** `produkt_photovoltaikanlage` (Dach/Verschattung/Eindeckung), `aufmass_photovoltaik`, `grundformular` (Objekt-Basisblock).
- Je Formular: **Felder** (slug/typ/einheit/optionen), **Regeln** (`visible_if`), **Formeln** (`calculation`) — Models `DynamicForm`/`FormSection`/`FormField`/`FormFieldOption`.
- **Mapping zu BOM/Angebot:** `app/Services/Konfigurator/*` (`GewerkAusleger`, `KonfiguratorAngebotService` BOM→Offer) — als **Konzept-Vorbild** für Formular/Tool→`sections`.
- Zugehörige Controller/Services/Views/Seeder/Tests (`tests/Feature/Formular/*`) — nur zur Reife-/Vollständigkeits-Bewertung.

## 5. Expertenrollen (agents/05 — Pflicht bei Auslegungs-Wizard)
- **Konzeption-Agent:** welche Felder fachlich Pflicht/Komfort/Doku; WP/PV-Bedarfslogik.
- **Workflow-Agent:** Kette Formular → Bedarf (Anforderungsprofil) → Auslegung → `sections` → P1-a; wo Mensch bestätigt (Operanden-Gate).
- **Architektur-Agent:** Mapping playground-Feld → ticket-schema-v2; „eine Wahrheit"; keine `dynamic_forms`-Zweitwelt; Anker Kunde/Objekt/Gewerk.
- **Frontend-Design-Agent:** Formular-Rendering in ticket (Alpine-Scope „formulare" erlaubt), Einbettung in Angebots-/Auslegungs-Fluss; Browser-Prüfbarkeit.
- **Energie-Fach (zusätzlich):** WP-Auslegung (Heizlast/JAZ/Bivalenz) + PV (kWp/Dach/WR) — welche Formularwerte in die Rechenkerne gehen.

*(Getrennte Instanzen, wenn Bau-Runde; im Planner-Startblock nur benannt.)*

## 6. Vorgehen Schritt für Schritt (der geplanten Analyse-Runde, nach Freigabe)
1. **ticket-Engine-Grenzen firsthand** feststellen (welche Feldtypen/Formeln/Regeln v2 akzeptiert).
2. **playground WP/PV-Formulare firsthand** inventarisieren (Felder/Regeln/Formeln je Formular, tabellarisch).
3. **Feld-Bewertung:** Pflicht / Komfort / nur Doku; Überschneidung mit ticket; Lücke in ticket.
4. **Formel-/Regel-Abgleich:** direkt lauffähig in ticket-Engine vs. anzupassen.
5. **Datenfluss-Zuordnung:** je Feld → Auslegung / Angebotsposition / nur Dokumentation; Anker (Kunde/Objekt/Gewerk/Angebot).
6. **Mapping-Plan** playground-Feld → ticket-`product_formulas`-`fields`-JSON (schema v2), gewerk-weise (WP zuerst, dann PV).
7. **Doppelwahrheit-Prüfung:** wo würde ein Formularwert eine zweite Wahrheit neben Anforderungsprofil/Auslegung/Katalog erzeugen.
8. **Ergebnis:** Übernahme-Konzept + kleinster erster Bau-Schnitt (1 Formular, z. B. WP) — **noch kein Bau**.

## 7. Kritische Fragen (in der Analyse zu beantworten)
- Welche WP/PV-Felder sind fachlich **Pflicht**, welche nur **Komfort**?
- Welche Felder **überschneiden** sich mit ticket, welche **fehlen** in ticket?
- Welche Formeln kann die ticket-Engine **direkt** ausführen, welche müssen **angepasst** werden?
- Welche Werte gehen in die **Auslegung**, welche in **Angebotspositionen**, welche dienen nur **Dokumentation**?
- An welchem **Anker** hängen die Daten (Kunde `new_leads` / Objekt `lead_alternative_adds` / Gewerk `lead_product_lists` / Dienstleistung / Angebot `offers`)?
- **Wo droht Doppelwahrheit** (Formularwert vs. Anforderungsprofil vs. Auslegungs-Rechenkern vs. Katalog/P1-a)?

## 8. Regeln (bindend)
ticket-Engine bleibt **führend** · playground liefert **nur** Inhalt/Felder/Mapping-Ideen · **kein**
`dynamic_forms`-System · **keine** React-SPA · **keine** zweite Angebotsstruktur · **kein** Import
aller 21 Formulare · **nur WP/PV zuerst** · kein Code/Import/Refactor/Migration · für jeden späteren
Bau gilt der Rückweg aus `docs/ARBEITSREGELN.md`.

## 9. Risiken (vorab benannt)
- **Zweite Formularwelt** (playground-System statt nur Inhalt) → Mitigation: nur Feld-Definitionen ins ticket-Schema mappen.
- **Zweite Rechenwahrheit** (Formularwert konkurriert mit Auslegungs-Rechenkern/Anforderungsprofil) → Mitigation: klare Zuordnung „Auslegung vs. Doku vs. Position".
- **Anker-Losigkeit** (Formular ohne Objekt/Gewerk-Bindung, wie heutige `/admin/energie/*`) → Mitigation: Verankerung an Anforderungsprofil planen.
- **Formel-Inkompatibilität** (playground-Funktionen ≠ ticket-Whitelist) → in Schritt 4 prüfen.
- **playground-Heizlast „nicht normgerecht"** → ticket-Heizlast (DIN EN 12831) führt; playground-Werte nur Erhebung/Vorwert.
- **DSGVO:** nur Formular-Struktur/Felder betrachten, keine playground-Antwortdaten.

## 10. Ergebnisdokument
`docs/bereich2-wp-pv-formular-uebernahme-konzept.md` (in der Analyse-Runde nach Freigabe) — mit: playground-WP/PV-Feldinventar, Feld-Bewertung (Pflicht/Komfort/Doku), Mapping-Plan auf `product_formulas` v2, Datenfluss (Auslegung/Position/Doku + Anker), Doppelwahrheit-Prüfung, kleinster erster Bau-Schnitt, offene Fragen.

## 11. Stop-Kriterium + Yama-Abnahmepunkt
- **Dieser Startblock** liegt vor → **STOPP.**
- **Yama-Abnahmepunkt:** Yama gibt (a) den Start der **Analyse-/Konzept-Runde** frei (read-only, Ergebnisdokument oben) und **danach separat** (b) einen etwaigen **ersten Bau-Schnitt** (1 Formular). **Kein Bau ohne neue Freigabe.**

---

*Nächster Schritt laut Auftrag: **STOPP.** Kein Bau. Ich warte auf dein Go für die WP/PV-Formular-Analyse-/Konzept-Runde.*
