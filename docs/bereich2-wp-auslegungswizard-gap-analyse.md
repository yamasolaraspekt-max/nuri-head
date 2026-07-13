# WP-Auslegungswizard — Gap-Analyse gegen den fachlichen Prompt (READ-ONLY)

> **Status:** read-only Abgleich + Gap-Analyse. **Kein Bau, kein Commit, kein Push, keine Migration, keine Datenänderung.** Ehrlich, nicht rechtfertigend.
> **Frage:** Berücksichtigt die bisherige Planung (P3 / Paket 4) den geführten WP-Auslegungswizard aus dem Prompt — oder muss der Zielworkflow erweitert werden?
> **Datum:** 2026-07-13.

---

## 0. Ehrliche Kurzantwort (Entscheidung, vgl. §8)
**B — Teilweise, mit klarer Schlagseite:**
- **Rechenkern:** **weitgehend vorhanden und fachlich stark** (Heizlast DIN EN 12831-1, Bivalenz VDI 4645, WP-Match, JAZ, Klima-PLZ/NAT, EN-442-Heizkörper, Warmwasser, PV-Inverter, Datenlage-Marker, Operanden-Gate).
- **Geführter Wizard + Variantenvergleich (monovalent/monoenergetisch/bivalent) + strukturierter Ergebnisbericht + harte „belastbare Heizlast"-Regel:** **NICHT als zusammenhängendes Feature gebaut und in meiner Paket-4-Planung nicht ausreichend enthalten.** Mein Paket-4-Plan war auf ein **read-only Cockpit + Vorschau-Panels** zugeschnitten (Bündelung vorhandener Bausteine), **nicht** auf den geführten Auslegungswizard, den der Prompt beschreibt. **Für diesen Teil war die Planung zu klein** → eigener Baustein nötig (Vorschlag: **Paket 5 — WP-Auslegungswizard**).

---

## 1. War ein geführter Wizard bereits gedacht?
**Ehrlich: nur in Bruchstücken, nicht als Ganzes.**
- Vorhanden als **Mechanik-Bausteine**: `VisibleIfService` (nur relevante Fragen, `visible_if`, Sichtbarkeitskaskade), `FormulaEvaluationService` (Operanden-Gate: unvollständige Daten → **keine Zahl**, `fehlliste`), Datenlage je Wert (`gemessen/berechnet/geschaetzt`).
- **Nicht** vorhanden als **geführter Ablauf**: Fragen nacheinander mit **Standardwerten**, **Annahmen-Kennzeichnung**, **Erklärung fehlender Angaben**, „**keine exakte Empfehlung bei schwacher Datenlage**" als durchgesetzte UI-Regel, und die **strukturierte Abschluss-Zusammenfassung** (Ergebnisbericht) sind so **nicht gebaut**. Das Standalone-`EnergieAuslegungController` ist ein **Direkteingabe-Formular** (`methode='direkt'`), kein objekt-verankerter geführter Wizard. `AuslegungVorschlagService` (P3-c) ist eine **read-only Vorschau**, kein Eingabe-Wizard und kein Vollbericht.

---

## 2. Prompt-Abschnitt → vorhandener Code → Status

| Abschn. | vorhandener Code / Service / Model / DB | Status |
|---|---|---|
| **A Gebäudegrunddaten** | `ProductFormula` WP (`wp_gebaeudeart`, `wp_wohnflaeche`), `HeizlastProjekt`/`HeizlastRaum`/`HeizlastBauteil`, `Anforderungsprofil.gebaeude_geometrie` | **teilweise** (Formularfelder + Rechen-Input da; nicht als Wizard-Schritt) |
| **B Baujahr / energet. Zustand** | `wp_baujahr`, `wp_daemmzustand`, `sanierungsstufe`, `UWertService`, `HeizlastNormwerte` | **teilweise** |
| **C best. Heizung / Verbrauch** | `wp_energietraeger`, `wp_heizung_art`, `wp_gasverbrauch`/`wp_oelverbrauch`, **`VerbrauchsService` (Methode V)** | **vorhanden (Rechnung)**, aber Verbrauchs-Heizlast nicht klar als „vorläufig" geführt (§5) |
| **D Heizlast** | **`HeizlastRechner` (DIN EN 12831-1)**, `HeizlastProjektService` (`standardheizlast_kw`/`auslegungsheizlast_kw`, `datenqualitaet_konfidenz`) | **stark vorhanden** (Rechnung), nicht im Wizard verdrahtet |
| **E Verteilung / Heizkörper / Vorlauf** | **`RadiatorPerformanceService` (EN 442, `minVorlauf`)**, `HydraulicService`, `RadiatorInstallation`/`RadiatorSpec`, `wp_heizflaechen`, `wp_vorlauftemperatur` | **stark vorhanden**, HK-Modul **Feature-Flag OFF** (M5); nicht im Wizard |
| **F Warmwasser** | **`WarmwasserService`** | **vorhanden**, WW-**Speicher-Dimensionierung fehlt** |
| **G gewünschtes WP-System** | `wp_ziel`, `WaermepumpenMatchService` (wpTyp/heizsystem) | **teilweise** |
| **H Aufstellung / Schall / innen** | `wp_aufstellung`, `RadiatorSituation` (Aufstellbezug HK) | **Feld da, Schall/Abstände fehlen** |
| **I Hydraulik / Puffer / Wasserqualität** | `HydraulicService`, `CompatibilityService` | **teilweise; Puffer-Dimensionierung fehlt, Wasserqualität fehlt** |
| **J Elektrik / Netzanschluss** | `phase_count` (products), keine Netzanschluss-Prüfung | **fehlt weitgehend** |
| **K PV / Energiemanagement** | **`PvgisErtragService`, `InverterSizingService`**, `ProductPV` | **vorhanden (PV-Domäne), nicht mit WP-Wizard verkoppelt** |
| **L Kühlung** | — | **fehlt** |
| **M Prioritäten / Budget / Zeitplan** | `lead_product_lists.interest`/`objective` | **fehlt als strukturierte Felder** |
| **N technische Auslegung** | **`HeizlastProjektService` + `BivalenzService` + `WaermepumpenMatchService` + `JazService` + `KlimaPlzService`/`KlimaBinService`** | **stark vorhanden**, aber **nicht** zu einem geführten Auslegungsergebnis zusammengeführt |
| **O Ergebnis / Ampel / offene Punkte** | `AuslegungVorschlagService` (Ampel/Datenlage/Aufgaben, **preislos**), `EnergieAuslegungController::wpErgebnis` (Standalone-Dokument) | **teilweise**; **vollständiger Ergebnisbericht fehlt** |

---

## 3. Fachlich/rechnerisch stark vorhanden (belegt)
- **`HeizlastProjektService` / `HeizlastRechner`** — DIN EN 12831-1, bauteilbezogen, `standardheizlast_kw` + `auslegungsheizlast_kw`, `datenqualitaet_konfidenz`. ✅
- **`BivalenzService`** — `bivalenzpunkt_c`, `bivalenz_status`, `deckung_ne_pct` (Nachheiz-/Heizstab-Deckung), getrennte **E-Stab-Wärme/Strom**, **`jaz`** + `jaz_nur_wp`, Bin-Simulation. ✅
- **`WaermepumpenMatchService`** — `kandidaten(kW,…)` mit `deckung_pct`, **Status** (`passt`/`monoenergetisch`/`zu_gross`/`zu_klein`), **Ranking** (`sortBy` nach Status+Deckung), `hinweis`, max-Vorlauf-Guard. ✅
- **`WpKennlinieService`** (φ_max/COP), **`JazService`** (Richtwert-JAZ, `to_verify`). ✅
- **Klima:** `KlimaPlzService` (NAT per PLZ), `KlimaBinService` (Temperatur-Häufigkeit). ✅
- **Heizkörper:** `RadiatorPerformanceService` (EN 442, Mindest-Vorlauf), `HydraulicService`. ✅ (Modul-Flag OFF)
- **Warmwasser:** `WarmwasserService`. ✅ (ohne Speicher-Sizing)
- **PV:** `InverterSizingService`, `PvgisErtragService`. ✅ (eigene Domäne)
- **`AuslegungVorschlagService`** (P3-c): read-only Vorschau mit Datenlage/Ampel/Aufgaben. ✅ (preislos)
- **`OfferReadinessService`**: Reife inkl. Kriterium „technische_auslegung". ✅
- **WP-Formular** `product_formulas` (18 Felder, v2), `FormulaEvaluationService` (Operanden-Gate), `VisibleIfService`. ✅

**Fazit §3:** Der **Rechen-/Fach-Kern** deckt N (technische Auslegung), D (Heizlast), Bivalenz/JAZ/Heizstab, E (HK/Vorlauf) und Klima **belastbar** ab. Das ist die stärkste Substanz.

---

## 4. Was fehlt als Workflow/Wizard (kategorisiert)
| Kategorie | Konkret |
|---|---|
| **Datenfeld fehlt** | Schall/Aufstellabstände (H), Wasserqualität (I), Netzanschluss-Detail (J), Kühlung (L), Prioritäten/Budget/Zeitplan strukturiert (M) |
| **Feld vorhanden, nicht verdrahtet** | `wp_vorlauftemperatur`/`wp_heizflaechen`/`wp_gasverbrauch` → fließen nicht automatisch in Heizlast/Match |
| **Service vorhanden, nicht im UI** | `BivalenzService`, `WaermepumpenMatchService`, `HeizlastProjektService`, `WarmwasserService` sind **nicht** in einem objekt-verankerten Wizard verdrahtet (nur Standalone-`EnergieAuslegungController` / read-only Vorschau) |
| **UI vorhanden, rechnet nicht** | `AuslegungVorschlagService`-Panel zeigt Positionen, **rechnet aber keine Auslegung** (liest nur vorhandene Profilwerte) |
| **Rechnung ohne Angebotsübernahme** | Auslegungsergebnis → **kein** Schreibpfad nach `offer_details.sections` (P3-e offen, preisanker-blockiert) |
| **Datenqualität/Ampel fehlt (aggregiert)** | Datenlage **je Wert** existiert; eine **aggregierte Datengrundlagen-Bewertung** + harte Belastbarkeits-Regel im Reife-/Auslegungsfluss **fehlt** (§5) |
| **Ergebnisbericht fehlt** | Vollständiger strukturierter WP-Auslegungsbericht (§7) ist **nicht** gebaut |
| **Variantenvergleich fehlt** | monovalent/monoenergetisch/bivalent **nebeneinander mit Ranking + Begründung** fehlt (§6) |

---

## 5. „Keine verbindliche WP-Größe ohne belastbare Heizlast" — der schärfste Punkt
- **Wird zu sicher dimensioniert?** — **Risiko vorhanden, teilweise abgesichert:**
  - `WaermepumpenMatchService` dimensioniert aus **`benoetigtKw` (Eingabe)** — es prüft **nicht**, ob dieses kW aus einer **belastbaren** Heizlast (bauteilbezogen) oder aus einer **Schätzung** (Fläche×W/m² / Verbrauchsmethode) stammt. → Bei schwacher Datenlage würde es **zu selbstsicher** Geräte vorschlagen. **Der Belastbarkeits-Guard liegt NICHT im Match-Service.**
  - **Abgesichert** ist die read-only Seite: `AuslegungVorschlagService` **wählt kein Gerät** (WP-Gerät = `zu_bestaetigen`, kein Auto-Anker) und markiert fehlende Operanden als **Aufgabe** statt zu rechnen. `OfferReadinessGate` sperrt bei offenen Blockern.
- **Verbrauchsschätzung als vorläufig?** — `VerbrauchsService` liefert `methode='verbrauch'`; **aber** die Datenlage wird beim Zurückschreiben ins Profil nicht zwingend als **`geschaetzt`** geführt und **nicht** hart von einer belastbaren Heizlast unterschieden.
- **Datengrundlagen-Bewertung?** — **je Wert ja** (`SchluesselRegistry.DATENLAGE`, `HeizlastProjektService.datenqualitaet_konfidenz`, viele `to_verify`/Richtwert-Marker), **aggregiert/als Nutzer-Ampel nein.**
- **Warnungen bei fehlender Heizlast/Vorlauf/HK/Aufstellort?** — **teilweise:** `AuslegungVorschlagService` markiert Hydraulik als „fehlt" ohne Vorlauf, WP-Gerät als „Aufgabe" ohne Heizlast; **aber** keine kohärente Warn-Checkliste (Vorlauf/HK-Daten/Aufstellort) als Auslegungs-Voraussetzung.
- **Kritische Lücke:** Die Reife-Regel `technische_auslegung` prüft nur, **dass ein Heizlast-Wert existiert** (`phi_hl_kw`/`standardheizlast_kw`/`auslegungsheizlast_kw`), **nicht dessen Belastbarkeit (Datenlage).** → **Eine geschätzte Heizlast passiert die Reife.** Die vom Prompt geforderte **harte Regel „keine verbindliche WP-Größe ohne belastbare Heizlast" ist NICHT durchgesetzt.** *(Sharpest gap.)*

---

## 6. Variantenbewertung (monovalent/monoenergetisch/bivalent)
- **Deckt `BivalenzService` das ab?** — Es **simuliert einen** Betriebspunkt und liefert `bivalenz_status` + `bivalenzpunkt_c` + `deckung_ne_pct` + E-Stab-Anteil + JAZ. **Bivalenzpunkt ✅, Heizstabanteil ✅ (`deckung_ne_pct`/E-Stab), JAZ ✅.**
- **Aber:** **kein** expliziter **Vergleich der drei Betriebsweisen** (monovalent vs monoenergetisch vs bivalent) **nebeneinander**. `WaermepumpenMatchService` rankt **Geräte** (Status/Deckung), **nicht** Betriebsweisen-Varianten.
- **Ranking mehrerer WP-Alternativen?** — **Geräte-Ranking ja** (`WaermepumpenMatchService`), **Betriebsweisen-/Konzept-Varianten-Ranking nein.**
- **Erklärung „warum besser/schlechter"?** — nur als **`hinweis`** je Gerät (z. B. „max. Vorlauf zu niedrig"), **nicht** als strukturierte Variantenbegründung. **→ Variantenvergleich mit Begründung fehlt.**

---

## 7. Ergebnisdarstellung — Zielbild-Abgleich
| Element | vorhanden? |
|---|---|
| Eingabe-Zusammenfassung | teilweise (Formular-Antworten, kein Report) |
| Heizlast / Heizlastbereich | Rechnung ✅, **Bereich/Unsicherheit** nicht dargestellt |
| empfohlener Leistungsbereich | Match liefert kW/Deckung, **kein Bereich** |
| Modulationsbereich | Gerätedaten `modulation_min/max_kw` da, **nicht dargestellt** |
| Anlagenkonzept | **fehlt** (kein Konzept-Zusammenzug) |
| Pufferspeicher | **fehlt** (kein Sizing) |
| Warmwasserspeicher | **fehlt** (WW-Bedarf ✅, Speicher-Sizing ✗) |
| Stromverbrauch / JAZ | `JazService`/`BivalenzService` ✅, **nicht im Report** |
| Risiken als Ampel | Bausteine (Ampel je Kennzahl) ✅, **kein Gesamt-Risikobild** |
| offene Punkte als To-do | `AuslegungVorschlagService`-Aufgaben ✅ (partiell) |
| Bewertung der Datengrundlage | **fehlt aggregiert** (§5) |
**→ Der strukturierte Ergebnisbericht (Zielbild O) ist NICHT gebaut.**

---

## 8. Entscheidung
**B — Teilweise.** Präzise:
- **Schon drin (belegt §3):** Rechenkern Heizlast/Bivalenz/JAZ/Heizstab/Bivalenzpunkt/Klima/HK/WW/PV, Datenlage-je-Wert, Operanden-Gate, `visible_if`, WP-Formular, read-only Vorschau/Cockpit (Paket 4a).
- **Fehlt (belegt §4–§7):** geführter Auslegungswizard (A→O mit Defaults/Annahmen/Erklärungen), **harte Belastbarkeits-Regel** (§5), **Varianten-Vergleich mit Ranking+Begründung** (§6), **Puffer-/WW-Speicher-Sizing**, **aggregierte Datengrundlagen-Ampel**, **strukturierter Ergebnisbericht** (§7).
- **Ehrliche Selbsteinordnung:** Für **genau diesen Wizard-Teil war die bisherige Planung zu klein** (Paket 4 = Cockpit/Vorschau, nicht der geführte Auslegungs-/Report-Wizard). → **Zielworkflow erweitern.**

---

## 9. Integrationsvorschlag — Paket 5: WP-Auslegungswizard (eigener Schritt)
**Einordnung im Workflow (Paket-4-Cockpit-Prozessleiste):** neuer Schritt **zwischen „Bedarf" und „Auslegungs-Vorschau"** → *Bedarf → **Auslegung (Wizard)** → Vorschlag → Preisfähigkeit → Angebot.*

- **Felder aus Formular:** `ProductFormula` WP (A–G-Basis) + **zu ergänzende Felder** H (Schall/Abstand), I (Puffer/Wasserqualität), J (Netzanschluss), L (Kühlung), M (Prioritäten). Sichtbarkeit über `visible_if` (`VisibleIfService`), Defaults + **Annahmen-Marker** (Datenlage=`geschaetzt`).
- **Serverseitige Berechnungen (Reuse, on-the-fly):** `HeizlastProjektService`/`HeizlastRechner` (D), `RadiatorPerformanceService`/`HydraulicService` (E), `WarmwasserService` (F), `BivalenzService` + `WaermepumpenMatchService` + `JazService` + `WpKennlinieService` (N), `KlimaPlzService`/`KlimaBinService`. **Operanden-Gate durchsetzen.**
- **Ergebnisse → `Anforderungsprofil`-Werte** (führende Wahrheit): `standardheizlast_kw`/`auslegungsheizlast_kw` (+ **Datenlage**), `vorlauf_c`, `bivalenzpunkt_c` (neuer Registry-Key), `jaz`, WP-Kandidat. **`AuslegungVorschlagService` liest sie** (bereits so gebaut).
- **Später → `offer_details.sections`:** erst über den P3-e-Adapter **nach** belegtem Katalog-/Preisanker (OMD/IDS) — bis dahin Vorschlag **preislos/markiert**.
- **Nur Hinweis/Aufgabe:** fehlende Operanden, geschätzte Werte, Speicher-Sizing (bis Service existiert), Kühlung/Netzanschluss ohne Detail.
- **Blockiert Angebotserstellung (harte Regel, NEU):** **keine belastbare Heizlast** (Datenlage nicht `gemessen`/`berechnet`, sondern nur `geschaetzt`) ⇒ Auslegung als **vorläufig**, **keine verbindliche WP-Größe**, Angebotserstellung gesperrt oder nur „unverbindlich/Schätzung" markiert. (Reife-Kriterium `technische_auslegung` um **Datenlage-Prüfung** erweitern.)
- **Blockiert nur Preisfähigkeit:** OMD/IDS-Preisanbindung (bekannt, P3-d2b) — Technik kann grün sein, Preis rot.

**Empfohlene Paket-5-Slices (read-only-first):**
1. **5a** — Varianten-/Ergebnis-**Vorschau** (read-only): Betriebsweisen monovalent/monoenergetisch/bivalent **nebeneinander** aus `BivalenzService` (mehrere Läufe) + Geräte-Ranking, mit **Begründung** und **Datengrundlagen-Ampel**. Kein Write.
2. **5b** — **Belastbarkeits-Gate**: Reife-Kriterium `technische_auslegung` um Datenlage-Prüfung erweitern; „keine verbindliche Größe ohne belastbare Heizlast" durchsetzen (nur Vorschlag+Markierung). Kleiner, testbarer Slice.
3. **5c** — **geführter Eingabe-Wizard** (A→O, Defaults/Annahmen) → schreibt Anforderungsprofil-Werte (additiv, Operanden-Gate). Erst nach 5a/5b.

---

## 10. Nicht-Ziele (dieses Dokument)
Read-only · kein Bau/Commit/Push · kein Seeder/Migration/Datenänderung · keine Preis-/Katalogänderung. Nur dieses Dokument neu (nicht committet).
