# Bereich 2 — WP/PV-Formularübernahme aus playground in die ticket-Engine — Konzept

**Stand:** 2026-07-12 · **read-only · nur Analyse/Konzept** · kein Bau/Import/Migration/Refactor/Automatisierung/Commit.
**Grundlage (firsthand, `datei:zeile`):** ticket-Formular-Engine (FormSchemaValidator/FormulaEvaluationService/VisibleIfService, `product_formulas`, `lead_product_checklist_values`), ticket-WP/PV-Services (HeizlastEingabe/HeizlastProjektService/WaermepumpenMatchService/BivalenzService/JazService/InverterSizingService/PvgisErtragService/KlimaPlzService), Anforderungsprofil-Registry, `offer_details.sections` + P1-a; playground WP/PV-Formulare + Konfigurator→BOM→Angebot.
**Rahmen:** ticket-Engine bleibt **führend**; playground liefert nur **Feldinhalte + Mapping-Ideen**; „eine Wahrheit"; Anker=Objekt/Gewerk via Anforderungsprofil; Positionen=`sections`; Preis=P1-a; `rueckfall-archiv-regeln.md`.

> **Zentrale fachliche Regel dieses Konzepts:** **playground liefert die FELDER (Erhebung), NICHT die FORMELN.** Die playground-WP-/Heizlast-Rechnung ist ausdrücklich **nicht normgerecht** (flächenbezogener Richtwert `Fläche×W/m²`, Faustwerte) — ticket hat mit `HeizlastProjektService` (DIN EN 12831, bauteilbezogen) und `BivalenzService` (VDI 4645/4650) die **überlegene** Rechen-Wahrheit. Übernommen werden die **Erhebungs-/Bedarfsfelder**; gerechnet wird mit den **ticket-Services**.

> **Pilot-Empfehlung (belegt): `produkt_waermepumpe` zuerst.** Begründung unten §10.

---

## 1. Ziel-Engine (Mapping-Zielspezifikation, kompakt)
- **`product_formulas.fields[]`** (v2-Feldobjekt): `key`(slug, eindeutig) · `label` · `type` · `[options{value,label}]` · `[min,max,decimals]` · `[unit]` · `[required]` · `[visible_if{field,op,value}]` · `[calculation{formel,rundung}]` · `[source]` · `[risk_level]`. Container `product_id`→`article_groups`(Gewerk), `section_name`, `status`, **`schema_version=2`**, **`imported_from`** (Import-/Rückbau-Marker).
- **21 Feldtypen:** text, textarea, number, integer, decimal, length, area, volume, power, select, multiselect, checkbox, checkbox_group, boolean, consent, date, email, **plz**, image, file, calculation.
- **Formel-Engine (eval-frei):** nur `+ - * /` + **`SUM/MENGE/FLAECHE/VOLUMEN`**; Rundung `kaufmaennisch/auf/ab`+`decimals`; Operanden-Gate (`vollstaendig`/`unvollstaendig`/`enthaelt-ungepruefte-werte`). **Nicht ausdrückbar:** IF/Bedingung, MIN/MAX/ROUND/ABS, Potenz/Wurzel/Modulo, Vergleiche, Cross-Formular.
- **`visible_if`:** eine `{field,op,value}`-Regel je Feld, Operatoren `= != > < >= <= in not_in`, Kaskade (kein AND/OR-Verbund).
- **Antworten:** `lead_product_checklist_values` — Anker `lead_product_list_id`(Gewerk)/`product_formula_id`/`customer_id`/`alternative_id`/`product_id`/`section_name`; `filled_values` + `formula_snapshot` + `formula_version`.
- **Mapping-Falle:** v1-Pfad liest `field['name']`, v2 nutzt `field['key']`. **Import zwingend als `schema_version=2` + `imported_from` + Validator-geprüft** (Ziel-Slug = `key`).

---

## 2. WP-Formulare — Feldinventar + Übernahmeliste

| Formular | Sekt. | Felder | calc | Charakter | Übernahme-Empfehlung |
|---|---|---|---|---|---|
| `produkt_waermepumpe` | 8 | **18** | 0 | schlanke **Bedarfsabfrage** (select/number) | **übernehmen — Pilot** (Felder 1:1, kein Formel-Risiko) |
| `aufmass_waermepumpe` | 10 | 28 | 0 | Vor-Ort-**Aufmaß** (Fotos/`recognition`) | später übernehmen (Aufmaß-Strang, nach Pilot) |
| `fachwerkzeug_heizlast` | 14 | 58 | **7** | Heizlast-**Datengrundlage**, „KEINE Norm-Heizlast" | **Felder ja, Formeln NEIN** (ticket-Norm-Service führt) |
| `fachwerkzeug_waermepumpe_konfigurator` | 17 | 58 | **5** | Konfig-Vorschlag, „KEINE normgerechte WP-Auslegung" | **Felder selektiv, Formeln NEIN** (ticket-Match/Bivalenz führt) |

**Übernahme-Liste WP (Felder, gewerk-normalisiert `wp_*`):** Bedarf/Gebäude `wp_gebaeudeart, wp_baujahr, wp_wohnflaeche, wp_daemmzustand, wp_vorhaben, wp_ziel` · Bestand/Verbrauch `wp_energietraeger, wp_gasverbrauch, wp_oelverbrauch, wp_heizung_art, wp_heizung_baujahr, wp_warmwasser_enthalten, wp_warmwasserspeicher` · Wärmeverteilung `wp_heizflaechen, wp_vorlauftemperatur` · Warmwasser/Nutzer `wpk_personen_anzahl, wpk_ww_komfort` · Klima/Standort (aus `fachwerkzeug_heizlast`) `standort_plz, norm_aussentemperatur, standort_hoehenlage, raumtemperatur_auslegung` · Geometrie/Bauteile (Heizlast) `raum_laenge/breite/hoehe, raum_solltemperatur, bauteil_typ/flaeche/u_wert/u_wert_quelle, oeffnung_*` · Heizflächen `hk_bauart/breite/hoehe/anzahl, fbh_*` · WP-Konfig selektiv `wpk_wp_bauart, wpk_vorlauftemp_c, wpk_heizstab_kw, wpk_komponenten(checkbox_group)`.

**Wichtig:** Slug-Wiederverwendung (`wp_*`) ist der natürliche Mapping-Anker; **Einheiten vor Import normalisieren** (Falle: `m2`↔`m²`, `Liter`↔`l`, `wp_vorlauftemperatur` mal `text` mal `number`+°C).

---

## 3. PV-Formulare — Feldinventar + Übernahmeliste

| Formular | Sekt. | Felder | calc | Charakter | Übernahme-Empfehlung |
|---|---|---|---|---|---|
| `grundformular` (Objekt-Basisblock) | 3 | 30 | 0 | Objekt/Kunde/Projekt | **Objekt-Sektion** als Basis (→ Objekt-Anker), nicht Kundendaten doppeln |
| `produkt_photovoltaikanlage` | 10 | **45** | 0 | PV-**Bedarfs-/Dach-Erhebung** | **übernehmen** (nach WP-Pilot) — reiches Dach-Fachwissen |
| `aufmass_photovoltaik` | 9 | 29 | 0 | PV-**Aufmaß** (Dachflächen repeatable) | später (Aufmaß-Strang) |
| `energieverbrauchsdaten` | — | 13 | 0 | Verbrauch (kWh/Jahr, Strompreis) | Verbrauchs-Sektion (Basisblock) |

**Übernahme-Liste PV (Felder):** Verbrauch `pv_stromverbrauch(kWh/Jahr), pv_lastprofil, pv_haushaltsgroesse, pv_besondere_verbraucher, pv_speicher_gewuenscht, pv_wallbox_gewuenscht, pv_notstrom_gewuenscht` · Objekt/Dach `pv_dachart, pv_dachform, pv_dachneigung(°), pv_dachausrichtung, pv_dachmasse, pv_traufhoehe/firsthoehe, pv_verschattung, pv_dacheindeckung, pv_dachzustand, pv_dachstatik, pv_asbestverdacht, pv_sparren*` · Technik/Montage `pv_modulart, pv_unterkonstruktion, pv_wechselrichter_ort, pv_speicher_ort, pv_geruest` · Netz/Zähler `pv_kabelfuehrung_dach, pv_kabellaenge_dach_wr(m), pv_zaehlerschrank_vorhanden/zustand, pv_freier_zaehlerplatz, pv_erdung` · Aufmaß-Vorwerte `pv_kwp_vorwert(power), pv_modulanzahl_vorwert, dachflaechen[]`(repeatable).

---

## 4. Nicht-übernehmen-Liste

| Nicht übernehmen | Warum |
|---|---|
| **playground `dynamic_forms`-System** (Tabellen/Models/Builder) | ticket hat `product_formulas`+Engine — zweite Formularwelt verboten |
| **React-SPA + Blade-Bridges** | ticket = jQuery/Blade/Vuexy (Alpine nur „formulare"-Scope) |
| **Stored-Procedure-Seeding** (`crm_add_form/field`) | ticket-Import = Marker-Seeder in `product_formulas` (schema v2) |
| **playground-Offer-Struktur** (`Angebot\Offer/OfferChapter/OfferItem`, String-IDs) | ticket-Belegkette + `offer_details.sections` führt — zweite Angebotswahrheit verboten |
| **playground-Heizlast-Formeln** (`calc_*`, flächenbezogen `Fläche×W/m²`) | **nicht normgerecht**; ticket `HeizlastProjektService` (DIN EN 12831, bauteilbezogen) ist überlegen → Doppel-/Falschwahrheit |
| **playground-WP-Konfig-Formeln** (`wpk_puffer_liter=kW×20`, `ww=Pers×50`, `montagezeit=×0.5+8`) | Faustwerte, „keine Auslegung"; ticket-Services/Katalog übernehmen die Fachwerte |
| **`PvAusleger`-Heuristik** (kWp=Verbrauch/1000, Dach/Verschattung ungenutzt) | ticket `InverterSizingService` (Norm-Strangauslegung) + `PvgisErtragService` führen |
| **Doppelte Kundendaten** aus `grundformular` | Kunde=`new_leads` führt; Formular verankert an Objekt/Gewerk, nicht Kundenduplikat |

---

## 5. Mapping playground-Feld → ticket-Ziel (Anker)

| Feld-Gruppe (playground) | ticket-Ziel | Anker/Tabelle |
|---|---|---|
| Kunde (`grundformular` Kundendaten) | **Kunde** | `new_leads` (nicht im Formular duplizieren) |
| Objekt/Dach/Gebäude (`objektart`, `pv_dach*`, `wp_gebaeudeart/baujahr/wohnflaeche`) | **Objekt** | `lead_alternative_adds` (+ `Anforderungsprofil.gebaeude_geometrie`) |
| Gewerk-Zuordnung (WP/PV) | **Gewerk** | `lead_product_lists`→`article_groups` (WP=2, PV=6) |
| Bedarf/Operanden (`standort_plz, norm_aussentemp, phi_hl_kw, vorlauf_c, personen, verbrauch`) | **Anforderungsprofil** | `anforderungsprofile`/`_werte` (SchluesselRegistry) |
| Rechenergebnis (Heizlast/WP-Kandidat/kWp) | **Anforderungsprofil** (Ergebnis) → später **Angebot** | Registry-Ergebnis-Keys → `sections` |
| Positionen (Geräte/Sets/Material/Lohn) | **Angebot** | `offer_details.sections` (+ P1-a Preis) |
| Fotos/Nachweise/Annahmen | **Dokumentation** | `filled_values`/Anhang (keine Rechen-/Preiswahrheit) |

**Anker-Lücke (belegt):** `SchluesselRegistry` deckt heute nur den **Heizlast-Kern** (phi_hl_kw, standort_plz, norm_aussentemp_c, vorlauf_c, …). Für WP-Match/Bivalenz/PV fehlen Schlüssel (`wp_typ, heizsystem, jaz, bivalenzpunkt_c, kwp, modul_id, wr_id`) → **additive, bewusste Registry-Erweiterung** nötig (eigener späterer Schritt, kein Pilot-Thema).

---

## 6. Mapping Formularwert → Berechnung (Werte, die in Services gehen)

**Brücke = `HeizlastEingabe` (DTO, `fromArray` mappt Formular-Keys 1:1).** Welche Formularwerte welchen Service speisen:

| Formularwert (playground) | ticket-Eingabe | Service | Ausgabe |
|---|---|---|---|
| `standort_plz` | → `KlimaPlzService.findByPlz` | Klima | `nat_c`(NAT), `hoehe_m`, `lat/lon` |
| `norm_aussentemperatur` (oder aus PLZ) | `norm_aussentemp_c` | HeizlastRechner | Auslegungsheizlast |
| `raum_laenge/breite/hoehe`, `raum_solltemperatur`, `bauteil_typ/flaeche/u_wert`, `oeffnung_*` | `raeume[]`+`bauteile[]` | **HeizlastProjektService** (DIN EN 12831, live) | `auslegungsheizlast_kw`, `benoetigte_max_vorlauftemp_c`, Konfidenz |
| `wp_gasverbrauch/oelverbrauch`, `wp_energietraeger`, `wp_warmwasser_enthalten` | `verbrauch_menge/einheit`, `aktuelles_heizmedium` | VerbrauchsService (live) | `q_heiz_kwh`, `phi_hl_kw` (Alternativpfad) |
| `wpk_personen_anzahl`, `wpk_ww_komfort`, `ww_mit_wp` | `personen_im_haushalt`, `ww_komfort` | WarmwasserService (live) | `qWwKwh`, Speicherliter |
| `wp_heizflaechen`, `wpk_vorlauftemp_c`, `wp_typ` | `heizsystem`, `vorlauf_c`, `wpTyp` | **WaermepumpenMatchService** (live) | Geräte-Kandidaten + `deckung_pct` |
| Heizlast + Vorlauf + PLZ + qHeiz/qWw + Gerät | phiHlKw, vorlaufC, **plz**, qHeiz, qWw, WpKennlinie | **BivalenzService** (⚠️ isoliert) | Bivalenzpunkt, JAZ, E-Stab, Strom, Saison |
| `pv_stromverbrauch`, `pv_dachausrichtung/neigung`, `pv_dachmasse`, `pv_verschattung` | Module/WR/Strings + kwp/angle/aspect | **InverterSizingService** (live) + **PvgisErtragService** (live) | WR-Auslegung + Jahresertrag |

**Merker:** BivalenzService braucht **PLZ** — die liefert nur `fachwerkzeug_heizlast` (`standort_plz`); `produkt_waermepumpe` hat keine PLZ (die kommt aus `grundformular`/Objekt). KlimaBinService leitet die Zone grob aus der **PLZ-Anfangsziffer** ab (nicht aus `klima_plz`); `hgt15_kd` liegt in `klima_plz`, wird aber noch nicht genutzt.

---

## 7. Mapping Berechnung → Angebotsposition (`sections`)

**Vorbild = `HeizkoerperController::uebernehmen`** (das einzige „Tool→Positionen" im ticket): **server-re-derived** (kein Client-Vertrauen), **Replace-per-Sektion in 1 Transaction**, **Preise ehrlich NULL** bis Katalog-SKU gebunden, **`raw_snapshot`** mit Herkunft/Datenqualität. *(Schreibt heute in `deal_measurement_items`, nicht `sections` — das Muster ist übertragbar auf `sections`.)*

**Architektur-Vorbild = playground `GewerkAusleger`-Vertrag** (nur Vertrag, nicht Code): `auslege(bedarf) → {ergebnis, positionen, ampel, warnungen, to_verify}` + **BOM-Zwischenschicht** (Position: `article_id`, `menge`, `quelle`, `quelle_spec_id`, `manuell_ergaenzen`, `quantity_confident`, Herkunfts-Snapshot) → Mapper in `sections`-Knoten. Übertragbar: die **dreistufige Trennung Auslegung→BOM→Angebot** + **Herkunft/Confidence-Marker** (= Operanden-Gate) + **ehrliche Preislogik** (EK→VK, keine erfundene Marge). **P1-a** setzt beim Speichern den Katalog-Preis für `component_id`-Positionen.

**Ziel-`sections`-Knoten:** `{kind, name, qty/menge, unit_price/ek (NULL bis Katalog), subItems[], herkunft:'tool', raw_snapshot:{phi_hl_kw, geraet, deckung_pct, normbezug, datenqualitaet}}`. **Kein Formular schreibt direkt Preise.**

---

## 8. Kritische Ketten-Prüfung (Yamas Prüfpunkte)

- **WP-Kette** Objekt → Verbrauch/Bestand → Technik → **Heizlast (ticket-Norm)** → **WP-Kandidaten (Match, live)** → **Bivalenz (isoliert!)** → Ranking → Angebot: fachlich vollständig **auf der ticket-Seite** vorhanden; die playground-Formulare liefern die **Eingabe-Erhebung**. Bruch: `BivalenzService` unverdrahtet + braucht PLZ + Anforderungsprofil-Erweiterung.
- **PV-Kette** Objekt/Dach → Verbrauch → **PV-Auslegung (InverterSizing, live + PVGIS-Ertrag, live)** → Speicher/Wallbox → Material/Set → Angebot: ticket-Seite live; `PvProjektService` (Mehrdach) isoliert. playground liefert reiche Dach-/Verschattungs-/Netzfelder.
- **PLZ/Ort/Klima/NAT/HGT (WP):** `KlimaPlzService`→`nat_c/hoehe_m/lat/lon` (DB `klima_plz`); Zone via PLZ-Ziffer in `KlimaBinService` (nur für Bivalenz); `hgt15_kd` ungenutzt. **`standort_plz` ist Pflicht-Eingabe für den Bivalenz-Zweig.**
- **BivalenzService-Anbindung:** braucht `WpKennlinie` (Gerät) + phiHl + qHeiz + qWw + vorlauf + **PLZ**; heute 0 Aufrufer → separater Verdrahtungs-Posten (nicht Pilot).
- **InverterSizing/PV-Anbindung:** live über `EnergieAuslegungController`; PV-Formular-Dachfelder → Strang/Ausrichtung/Neigung; Ertrag über PVGIS (lat/lon aus `klima_plz`).
- **Übergang Formular → Service → sections:** existiert **nicht** durchgängig (nur Heizkörper→Measurement) → eigener Adapter-Posten nach dem Formular-Pilot.
- **Wo playground fachlich schwächer:** Heizlast (flächenbezogen statt bauteilbezogen; U-Werte erfasst, aber **nicht** in den Formeln genutzt) und WP-Konfig-Faustwerte → **ticket-Services müssen rechnen**, playground nur erheben.
- **Wo ticket bessere Services hat:** `HeizlastProjektService` (Norm), `WaermepumpenMatchService` (Katalog-Match), `BivalenzService` (Bin-Simulation), `InverterSizingService` (Strang-Norm), `PvgisErtragService` (Ertrag), `CatalogDeviceRepository` (Geräte-Wahrheit).

---

## 9. Empfohlene Reihenfolge
1. **Pilot: `produkt_waermepumpe`** als **eine** `product_formula` in die ticket-Engine (Rendern + Antworten erfassen, kein calc, keine sections). *(§10)*
2. **WP-Bedarf → Anforderungsprofil-Verankerung** (Formularantworten → Registry-Werte am Objekt/Gewerk; Registry additiv um WP-Schlüssel erweitern).
3. **`fachwerkzeug_heizlast`-Felder** übernehmen — aber an **ticket-`HeizlastProjektService`** rechnen (playground-Formeln verwerfen).
4. **WP-Auslegung→`sections`-Adapter** (Match/Bivalenz-Ergebnis → Positionen, P1-a-Preise) + **BivalenzService verdrahten** (PLZ).
5. **PV analog:** `produkt_photovoltaikanlage` → Engine → InverterSizing/PVGIS → `sections`.
6. WP und PV **nicht gleichzeitig** bauen.

## 10. Erstes kleines Baupaket (max. 1 Formular) — Vorschlag + Beleg

**Pilot: `produkt_waermepumpe` (18 Felder, 0 calc) als eine `product_formula` (Gewerk WP, `article_group_id=2`).**

**Warum WP-Bedarfsformular zuerst (am Code belegt):**
1. **Niedrigstes technisches Risiko:** reine select/number/multiselect-Felder → mappen 1:1 auf die 21 ticket-Typen; **0 calculation-Felder** → kein Konflikt mit der eingeschränkten Formel-Engine (nur `+-*/`+SUM/MENGE/FLAECHE/VOLUMEN). Der Pilot beweist die **Import→Render→Erfassungs-Pipeline** ohne Formel-Komplikationen.
2. **Höchster Anschlusswert:** die Felder (`wp_gebaeudeart/baujahr/wohnflaeche/daemmzustand/energietraeger/verbrauch/heizflaechen/vorlauftemperatur`) mappen direkt auf `HeizlastEingabe`/`SchluesselRegistry` → sauberer Vorwärtspfad zur **schon live vorhandenen** Norm-WP-Kette (`HeizlastProjektService`+`WaermepumpenMatchService`).
3. **Yamas Priorität** (WP-Auslegung) + die ticket-WP-Services sind die vollständigsten.
4. **Bewusst NICHT `fachwerkzeug_heizlast` zuerst:** dessen 7 flächenbezogene `calc`-Felder sind nicht normgerecht und kollidieren mit `HeizlastProjektService` → Doppelwahrheit-Risiko. Zuerst das formelfreie Bedarfsformular.

**Paket-Umriss (erst nach separater Bau-Freigabe):** Marker-Seeder `product_formulas` (WP, `schema_version=2`, `imported_from='playground:produkt_waermepumpe'`, Einheiten normalisiert), Rendern über bestehenden `ProductFormula`-Pfad, Antworten in `lead_product_checklist_values`. **Testpfad:** Formular lädt/validiert (FormSchemaValidator), Antworten persistiert am Gewerk-Anker; Browser: WP-Gewerk am Lead → Checkliste ausfüllen → gespeichert. **Rückfall:** Seeder-Rückbau über `imported_from`-Marker (kein Bestandseingriff); Variante B falls Engine-/Renderer-Änderung nötig. **Nicht im Pilot:** calc, sections-Bridge, Bivalenz-Verdrahtung, Registry-Erweiterung, PV.

---

## 11. Offene Entscheidungen an Yama
1. **Pilot bestätigen:** `produkt_waermepumpe` als erstes Formular (statt PV)? 
2. **Import-Weg:** playground-Felder als **Marker-Seeder** in `product_formulas` (schema v2) — bestätigt (kein `dynamic_forms`)?
3. **Anforderungsprofil-Erweiterung:** darf die `SchluesselRegistry` additiv um WP-/PV-Schlüssel (wp_typ, jaz, kwp, …) erweitert werden (eigener späterer Schritt)?
4. **Einheiten-Normalisierung:** verbindliche Einheiten je Slug festlegen (m², kWh, l, °C) beim Import?
5. **Aufmaß vs. Bedarf:** sollen Bedarfs- und Aufmaß-Formulare getrennte `product_formulas` sein oder ein Formular mit Sektionen?
6. **PLZ-Quelle:** PLZ für WP aus Objekt (`lead_alternative_adds`) ziehen statt separatem Formularfeld?

## 12. Risiken + Rückfallpfad
| Risiko | Mitigation |
|---|---|
| **zweite Formularwelt** (dynamic_forms) | nur Feld-Definitionen ins ticket-Schema; kein System-Import |
| **zweite Rechenwahrheit** (playground-calc vs. ticket-Norm-Services) | playground-Formeln verwerfen; ticket-Services rechnen |
| **zweite Angebotswahrheit** (playground-Offer) | nur Mapping-Muster; Ziel = `offer_details.sections` |
| **Anker-Losigkeit** (Formular ohne Objekt/Gewerk) | Verankerung an Anforderungsprofil/`lead_product_list` |
| **Einheiten-/Typ-Inkonsistenz** (m2↔m², text↔number) | Normalisierung beim Import, Validator erzwingt Typ |
| **Formel-Inkompatibilität** (IF/MIN fehlen) | keine playground-Formeln übernehmen; Logik über `visible_if`/Service |
| **DSGVO** | nur Struktur/Felder, keine playground-Antwortdaten |
| **Rückfall** | Seeder-Import über `imported_from`-Marker rückbaubar (kein Bestandseingriff); Engine/Renderer-Änderung = Variante B (Archiv+MANIFEST); path-scoped Commit; alles hinter Prüf-/Freigabe-Schritt |

---

## Evaluator-Notiz
- **Belegt (firsthand, `datei:zeile`):** ticket-Engine-Schema/Typen/Formel-Grenzen; WP/PV-Service-Eingaben (HeizlastEingabe-DTO, Match, Bivalenz-PLZ, InverterSizing, PVGIS); Klima-Kette; Anforderungsprofil-Registry + Lücke; sections + Heizkörper-Übernahme-Muster; playground WP-Formulare (18/28/58/58 Felder, calc-Formeln wörtlich, „nicht normgerecht") + PV-Formulare (30/45/29/13) + Konfigurator→BOM→Angebot-Muster.
- **Ehrlich offen:** genaues Feld-für-Feld-Zielmapping (Slug→key) nicht fertig erstellt (Pilot-Umfang); exakte Anzahl der zu übernehmenden Felder je Formular (Auswahl noch Yama-abhängig); Registry-Erweiterungs-Vertrag nicht ausdesignt.
- **Nicht gemacht (korrekt):** kein Bau/Import/Migration/Refactor/Automatisierung/Commit; keine zweite Formular-/Angebotswelt; keine React/dynamic_forms-Übernahme.

---

*Nächster Schritt laut Auftrag: **STOPP.** Yama prüft Konzept + Pilot-Empfehlung (§10) + offene Fragen (§11). Erst danach — auf separate Freigabe — das erste kleine Baupaket (1 WP-Formular).*
