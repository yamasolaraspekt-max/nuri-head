# Bereich 2 — Gewerke / Dienstleistungen / Formulare: ticket ↔ playground Abgleich

**Stand:** 2026-07-12 · **read-only** · **kein Import/Kopieren/Löschen/Refactor/Migration/Automatisierung/parallele Formularwelt.**
**Zweck:** Alle für Angebotserstellung relevanten Gewerke/Dienstleistungen/Formulare — ticket zuerst, dann playground als „Bauteile-Lager", dann Vergleich + Vorschlag.
**Grundlage:** firsthand (2026-07-12): ticket DB `ticket` (Live-Counts) + Code; playground `/Users/yamanuri/Documents/Playground/backend-laravel` (Code/Seeds).
**Rahmen:** Ziel-Wahrheit Angebotspositionen = `offer_details.sections`; „eine Wahrheit je Sachverhalt"; `rueckfall-archiv-regeln.md`.

> **Kern-Erkenntnis:** ticket hat die **Formular-Engine bereits** (aus playground portiert, eval-frei) — aber **0 Formulare** in der DB. playground hat die **~21 Formulare / ~364 Felder** (den Inhalt) **und** eine funktionierende **Konfigurator→BOM→Angebot-Brücke**, die ticket **fehlt**. Der Übernahme-Wert liegt daher im **Formular-Inhalt** + im **Brücken-Konzept** — **nicht** in einem zweiten Formular-System.

---

## 1. Ticket-Bestand (firsthand, Live)

### 1.1 Gewerke — `article_groups` (15 Zeilen, live)
WP(2) · Wallbox(4) · Photovoltaik(6) · Batteriespeicher(7) · Fenster(8) · Türen(9) · Badsanierung(10) · Küche(11) · Fliesen(12) · Dach(13) · Insektenschutz(14) · Fliegengitter(15) · Tapete(16) · Wechselrichter(38) · Zubehör(39).
`sub_article_groups` = **0 Zeilen** (Struktur da, leer). Gewerk-Achse = `article_groups`; alle Formulare/Phasen zeigen via `product_id` hierauf.

### 1.2 Dienstleistungen/Leistungsarten
**Keine `services`-Tabelle** (`service_id` in `lead_product_lists` = toter FK). „Leistung" = **Ablauf-Modell je Gewerk**: `phase_sections`(13) → `task_phases`(13) → `phase_activities`(49, mit `required_qualification_id`/`duration`) + Pipeline `lead_stages`(10). `product_types`(0) = Artikel/SKU-Ebene, keine Leistungs-Taxonomie. `machine_services`(0) = Aftersales, nicht Angebot.

### 1.3 Formulare + Engine
- `product_formulas` (Model `ProductFormula`): `product_id`→`article_groups` (Gewerk), `section_name`, `fields`(JSON), `schema_version`(1/2), `status`. **Live: 0 Formulare.**
- `product_formula_routing_rules` (FS-05, Anker `article_group_id`/`lead_product_list_id`/`object_type`): **0 Zeilen.**
- `LeadProductChecklistValue.filled_values`: **0.**
- **Engine `app/Services/Form/*`** (gebaut): `FormSchemaValidator` (v2, 22 Feldtypen inkl. length/area/volume/power/calculation), `VisibleIfService` (bedingte Sichtbarkeit), **`FormulaEvaluationService` (eval-frei, aus playground portiert — SUM/MENGE/FLAECHE/VOLUMEN, Operanden-Gate 3 Status)**, `PlausibilityService` (nur Warnungen), `UnitConversionService`.
- **Verdrahtung:** nur `ProductFormulaController` (`/product-formula/*`, `evaluate`) + `LeadProductChecklistValueController`. **`SmartroutingService` + `PlausibilityService` = 0 Aufrufer (dormant).**

### 1.4 Auslegungs-Tools je Gewerk (ticket)
- **WP/Heizlast:** `app/Services/Heizlast/*` (21 Services: HeizlastRechner, BivalenzService, JazService, WaermepumpenMatchService, …).
- **Heizkörper:** `app/Services/Heizkoerper/*` (RadiatorPerformance, Hydraulic, Compatibility).
- **PV/WR/Batterie:** `app/Services/Energie/*` (PvProjekt, PvgisErtrag, InverterSizing).
- **Klima:** `KlimaPlzService`. **Bedarf:** `Anforderungsprofil*` (verankert Objekt/Gewerk).
- **Kein Tool** für Fenster/Türen/Bad/Küche/Fliesen/Dach/Insektenschutz/Fliegengitter/Tapete — nur Katalog + (geplant) Formular.

### 1.5 Klassifikations-/Matching-Felder (ticket)
Vorhanden: Gewerk-Anker `product_id`→`article_groups`; Routing-Anker (leer). **Fehlt:** Objektart-Taxonomie (`object_type` nur freier String), Region/PLZ am Angebot, Keywords/Tags, Dienstleistungs-Taxonomie.

---

## 2. Playground-Bestand (firsthand — „Bauteile-Lager")

### 2.1 Zwei getrennte Welten (NICHT verdrahtet)
1. **Dynamisches Formular-System:** `dynamic_forms → form_sections → form_fields → form_field_options` + `form_routing_rules` + `form_answers`. Services `FormRoutingService`, `FormulaEvaluationService` (eval-frei, **Ursprung** des ticket-Ports), `PlausibilityService`, `UnitConversionService`. Controller API-first (React-SPA) + Blade-Bridges. **Umfang: ~21 Formulare / ~364 Felder** (SQL-Seed 17/185 + 4 PHP-Seeder). Feldtypen text/number/select/multiselect/email/textarea/calculation; echte deutsche Fachfelder (kein Faker).
2. **Anlagen-Konfigurator:** `app/Services/Konfigurator/Ausleger/` (`GewerkAusleger`-Contract: pv/wr/batterie/eps/wp/wallbox/zubehoer) → **BOM** (`AnlagenKonfiguration`/`AnlagenKonfigPosition`) → **`KonfiguratorAngebotService`** mappt BOM 1:1 in Angebots-Entwurf (`Angebot\Offer`/`OfferChapter`/`OfferItem`, draft) → `OfferCalculationService`. **Das ist die Tool→Angebot-Brücke.**

**Wichtig:** Formulare erzeugen **keine** Angebotspositionen (nur `form_answers` mit Mengen/Flächen als „Vorwert"); der Konfigurator liest **keine** Formularantworten. Brücke Formular→Auslegung bewusst nicht gebaut.

### 2.2 Gewerk-/Produkt-Modellierung
`Trade.php` (Gewerk-Stammdaten Elektro/SHK/PV, `code`/`name`) · `ProductServiceGroup`→`Product`/`Service` (Produkt vs. Dienstleistung getrennt) · `product_form_templates` + `form_routing_rules` (14 Produkt-Form-Links + 14 Routing-Regeln, Produkt-Slug→Formular).

---

## 3. Vergleichstabelle (Gewerk / Dienstleistung / Formular)

Legende: T=ticket, PG=playground. „Reife" = grob.

| Gewerk / Leistung | ticket-Status | playground-Status | Relevanz Angebot | mögliche Angebotspositionen | ableitbare Mengen/Lohn/Technik | fehlt in ticket | direkt übernehmbar? | Adapter? | Risiko | Empfehlung |
|---|---|---|---|---|---|---|---|---|---|---|
| **Wärmepumpe** | Gewerk✓, **Tools✓** (Heizlast/Bivalenz/Match), Formular **0** | Formulare✓ (`produkt_waermepumpe`+`aufmass`+`heizlast`+`konfigurator`), Konfig✓ | sehr hoch | WP-Gerät, Speicher, Hydraulik, Heizkörper, Montage | Heizlast→WP-Größe, JAZ, Mengen | **Formular-Inhalt** (Felder) | Inhalt ja (als Seed), System nein | ja (Feld→product_formulas) | zweite Formularwelt | **übernehmen (Inhalt), zuerst** |
| **PV** | Gewerk✓, Tools✓ (InverterSizing/PVGIS), Formular **0** | Formular✓ (Dach/Verschattung sehr detailliert)+`aufmass`, Konfig✓ | sehr hoch | Module, WR, Speicher, Montage, DC/AC | kWp, Fläche, Strings | **Formular-Inhalt** (v.a. Dach) | Inhalt ja | ja | dito | **übernehmen (Inhalt), zuerst** |
| **Speicher/Batterie** | Gewerk✓, Contract da | in PV-Formular + `BatterieAusleger` | hoch | Speicher, BMS | kWh-Bedarf | eigenes Formular-Feldset | teilweise | ja | — | später prüfen |
| **Wallbox** | Gewerk✓, kein Tool | `produkt_wallbox`+`WallboxAusleger`+Lastmanagement | hoch | Wallbox, Leitung, Lastmgmt | Leistung, Kabellänge | Formular + Lastmgmt-Konzept | Inhalt ja | ja | — | übernehmen (Inhalt) |
| **Heizkörper** | Gewerk (WP)✓, **Tool✓** (Konfigurator, dockt an `deal_measurement_items`) | in WP-Formular `wp_heizflaechen` | hoch | HK, Ventile, Hydraulik | q_norm, Vorlauf | — (ticket reifer) | — | — | — | **ticket-Bestand reicht** |
| **Heizlast** | **Tool✓ norm-nah** (DIN EN 12831) | `fachwerkzeug_heizlast` „**KEINE** Norm-Heizlast, nur Vorwert" | hoch | (Grundlage, keine Position) | Heizlast W | — | — | — | schlechter als ticket | **ticket-Bestand reicht** |
| **Dach/Objekt/Gebäude** | via `lead_alternative_adds`+`Anforderungsprofil` | `grundformular`+PV-Dach-Sektion | hoch | (Objektdaten) | Fläche, Ausrichtung | Formular-Inhalt Objektdaten | Inhalt ja | ja | — | übernehmen (Inhalt) |
| **Elektroarbeiten** | Gewerk **fehlt** in `article_groups`, kein Tool | `produkt_elektroinstallation`✓ | mittel-hoch | E-Positionen | — | **Gewerk + Formular** | Inhalt ja | ja | — | später prüfen (Gewerk anlegen = Yama) |
| **Sanitär/Heizung** | Gewerk **fehlt** (nur Badsanierung) | `produkt_sanitaerinstallation`/`produkt_badsanierung`✓ | mittel | SHK-Positionen | — | Gewerk + Formular | Inhalt ja | ja | — | später prüfen |
| **Fenster** | Gewerk✓, kein Tool/Formular | `produkt_*` (Seed) | mittel | Fenster, Montage | Maße, Stück | Formular-Inhalt | Inhalt ja | ja | — | übernehmen (Inhalt) später |
| **Türen/Küche/Fliesen/Insektenschutz/Sonnenschutz/Parkett/Tapete** | Gewerke teils✓, kein Tool/Formular | Seed-Formulare (rein erhebend) | mittel-niedrig | Handwerk-Positionen | Maße/Mengen | Formular-Inhalt | Inhalt ja | ja | — | später prüfen |
| **Wartung/Service** | `machine_services`(0), Aftersales | `Service`/`MaintenancePlan`/`Serviceauftrag` Models, **kein Formular** | Bereich 6 | Wartungspositionen | Intervall | — | — | — | **→ Bereich 6, nicht hier** |
| **Reparatur/Reklamation** | `problems`/`error_problem` (Bereich M) | `Reklamation`/`Complaint`/`Ticket`, kein Formular | Bereich 6 | — | — | — | — | — | **→ Bereich 6/8, nicht hier** |

---

## 4. Fehlende Gewerke / Dienstleistungen / Formulare im ticket

- **Formulare komplett fehlend (0 in DB)** — obwohl die Engine steht: für **jedes** der 15 Gewerke. Das ist die größte Lücke; playground liefert den Inhalt.
- **Gewerke, die playground hat und ticket nicht als `article_group`:** **Elektroinstallation**, **Sanitärinstallation** (ticket hat nur „Badsanierung"). Anlegen = Yama-Entscheidung.
- **Dienstleistungs-Taxonomie:** ticket hat keine `services`-Wahrheit; playground trennt `Product`/`Service`/`Trade` sauberer — als **Konzept** interessant.
- **Tool→Angebot-Brücke (Weg 3):** fehlt in ticket ganz (nur Heizkörper→Measurement); playground `KonfiguratorAngebotService` ist das Vorbild-Konzept.
- **Objektart-/Keyword-/Region-Klassifikation:** fehlt beidseitig strukturiert (playground `object_type`/`Trade` nur ansatzweise).

---

## 5. Übernahme-Kandidaten aus playground

| Kandidat | Was | Übernahmeart | Priorität |
|---|---|---|---|
| **K1 — Formular-INHALTE** | ~21 Formulare/~364 Felder (Feld-Definitionen je Gewerk, v.a. WP/PV/Dach) | **Inhalt** in ticket-`product_formulas` (schema v2) als **Marker-Seeder** (`imported_from='playground'`), gewerk-weise — **NICHT** das dynamic_forms-System | **hoch, zuerst** |
| **K2 — Tool→Angebot-Brücke (Konzept)** | `KonfiguratorAngebotService`: BOM→Angebots-Kapitel/-Positionen | **Konzept/Adapter** in ticket-Struktur (BOM→`offer_details.sections`), kein Code-Kopie (playground-Offer≠ticket-sections) | hoch (= Weg 3) |
| **K3 — Feldtyp-/Operanden-Gate-Muster** | bereits portiert (`FormulaEvaluationService`) — Rest-Felder/Funktionen abgleichen | Abgleich, kein Neuimport | mittel |
| **K4 — Product/Service/Trade-Trennung** | saubere Dienstleistungs-/Gewerk-Modellierung | **Idee** (Kategorie C), nicht Code | niedrig-mittel |
| **K5 — Handwerk-Formulare** (Fenster/Bad/Fliesen/Elektro/Sanitär) | reine Erhebungs-Formulare | Inhalt später (nach WP/PV) | niedrig |

---

## 6. Nicht nutzbare / gefährliche playground-Teile

- **dynamic_forms als zweites Formular-SYSTEM** — ticket hat `ProductFormula` + Engine bereits. Übernahme des Systems ⇒ **zweite Formular-Wahrheit** (verletzt „eine Wahrheit"). **Nur Inhalt übernehmen, nicht das System.**
- **React-SPA-Views** (`app.jsx`, `src/`) — nicht ins ticket (jQuery/Blade/Vuexy); Views = Neubau (Memory: playground keine Design-Vorlage).
- **playground-`form_answers`-Daten** — bei DB-Import Kundendaten-Risiko; nur **Struktur/Seed** (unbedenklich), keine Antwortdaten.
- **playground-Heizlast/WP-Fachwerkzeug** als Rechen-Wahrheit — explizit „**nicht** normgerecht, nur Vorwert". ticket-Heizlast (DIN EN 12831) ist überlegen → **nicht** übernehmen, ticket führt.
- **Rollen-/Permission-Kopplung** der playground-Formular-Controller — zieht fremde Auth-Schicht mit; nicht übernehmen.
- **playground-Offer/OfferChapter/OfferItem** — eigene Angebotsstruktur; **nicht** übernehmen (ticket = `offer_details.sections`).

---

## 7. Zielstruktur: Formular → Gewerk → Dienstleistung → Angebotsposition

```
Gewerk (article_groups, ticket führend)
   │  (product_id-Anker)
Formular (product_formulas, ticket-Engine; Inhalt aus playground-Kandidat K1)
   │  Nutzer füllt → FormulaEvaluationService → Mengen/Flächen/technische Werte (Operanden-Gate)
   │  + Auslegungs-Tool (WP/PV/Heizkörper) liefert Produktart/Mengen/Lohn/Technik
Dienstleistung/Leistung (phase_sections/task_phases je Gewerk — der Ablauf)
   │  (Adapter K2: Ergebnis → Sets/Artikel aus master_sets/products, gefiltert per article_group)
Angebotsposition  ►  offer_details.sections  (EINE führende Wahrheit)
   │
   └─ Server-Pricing (P1-a CatalogPriceGuard) · optional als Vorlage speichern (offer_templates + Metadaten)
```

**Regel:** Das Formular ist **Erhebung + Mengenrechnung**, es erzeugt **keine** eigene Angebotswelt. Der **Adapter** (K2) übersetzt Formular-/Tool-Ergebnis in `sections`-Knoten (mit `herkunft='tool'`/`preis_quelle` aus P1-a). Kein Formular schreibt direkt Preise.

---

## 8. Empfehlung — Reihenfolge der späteren Übernahme (kein Bau jetzt)

1. **ticket-Formular-Engine als führend bestätigen** — **kein** playground-System importieren. *(Backend: nichts · Frontend: nichts · Test: Review.)*
2. **K1 WP + PV Formular-Inhalte** als Marker-Seeder in `product_formulas` (schema v2), gewerk-weise, `imported_from='playground'`. *(B: Seeder + Feld-Mapping-Adapter · F: Formular-Rendering nutzt vorhandene Engine · Test: Formular lädt/validiert, Felder rechnen.)*
3. **`SmartroutingService` + Routing-Regeln füttern** (Gewerk→Formular), damit das richtige Formular je Gewerk erscheint. *(B: Regeln-Seed · F: Formularauswahl · Test: Anker→Formular korrekt.)*
4. **K2 Tool→`sections`-Adapter (ein Gewerk zuerst, WP)** — Formular/Tool-Ergebnis → Sets/Artikel → `sections`-Entwurf (Weg 3). *(B: Adapter · F: „als Entwurf übernehmen" · Test: prüfbarer Entwurf mit Katalog-Preisen.)*
5. **Handwerk-Formulare** (Elektro/Sanitär/Fenster/Bad/Fliesen) — Inhalt übernehmen; ggf. neue Gewerke `article_groups` (Yama). *(reine Erhebung → manuelle Set-Auswahl.)*
6. **Matching später** (Anforderungsprofil + Formularantworten → Vorlagen-/Tool-Vorschlag).

*(Jeder Schritt: Backend + Frontend + Testpfad; Reihenfolge folgt „①Konzept→②Workflow→③Verknüpfung→④Automatisierung".)*

---

## 9. Offene Entscheidungen an Yama

1. **Formular-System:** bestätigst du, dass **ticket-`product_formulas`+Engine führt** und playground nur **Inhalt** liefert (kein zweites System)?
2. **Neue Gewerke:** sollen **Elektroinstallation** und **Sanitärinstallation** als eigene `article_groups` angelegt werden (playground hat Formulare dafür)?
3. **Formular-Inhalt-Umfang:** alle ~21 playground-Formulare übernehmen oder nur WP/PV zuerst?
4. **Tool→Angebot-Brücke:** K2 als eigener Bereich-2-Umsetzungsschritt (nach dem Vorlagen-Schritt §14 des Angebots-Konzepts) einplanen?
5. **Dienstleistungs-Taxonomie:** brauchen wir eine echte `services`-Wahrheit (playground `Product/Service/Trade`) oder reicht `article_group + phase_sections`?
6. **Objektart/Region/Keywords:** als strukturierte Felder jetzt mitdenken (Matching-Grundlage) oder später?
7. **Wartung/Reklamation:** bestätigt, dass diese zu **Bereich 6 (Service)** gehören, nicht in die Angebots-Formular-Runde?

---

## Evaluator-Notiz
- **Belegt (firsthand):** ticket 15 Gewerke (live), 0 product_formulas/0 routing-rules (live), Engine gebaut + teils dormant, Tools nur WP/PV/Heizkörper; playground ~21 Formulare/~364 Felder + Konfigurator→Angebot-Brücke, Formular↔Konfigurator getrennt, eval-frei, React-SPA.
- **Ehrlich offen:** exakte Feldzahl der 3 playground-PHP-Seeder (~58/59/62, label-geschätzt, nicht DB-verifiziert); ob Handwerk-Produktformulare Rechenfelder enthalten (nur WP/PV/Heizlast/Aufmaß geprüft); genaues Feld-Mapping playground→ticket-schema-v2 (nicht Feld-für-Feld erstellt).
- **Keine Aktion:** kein Import/Kopieren/Löschen/Refactor/Migration/Automatisierung; keine zweite Formularwelt gebaut. Kandidaten sind **Vorschläge**.

---

*Nächster Schritt laut Auftrag: **STOPP.** Yama prüft Abgleich + offene Entscheidungen (§9). Erst danach — auf Freigabe — Detail-Konzept/Umsetzung eines einzelnen Übernahme-Schritts.*
