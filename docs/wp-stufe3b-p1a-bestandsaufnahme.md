# P1a — Read-only Bestandsaufnahme vor dem Charakterisierungs-Testbau (WP-Ergebnis/Kosten/Förderung/Dokument)

**Stand:** 2026-07-14 · **read-only** (keine Produktivänderung). **Git-Ausgangsstand:** `a00bb0a` (P0 committet).
**Zweck:** Das heutige fachliche Ist-Verhalten der WP-Pfade **belegt festhalten**, bevor P1b es in Dienste extrahiert.
Charakterisierungstests schreiben genau dieses Ist fest (fragwürdige Altwerte werden dokumentiert, in P1a **nicht** verbessert).

---

## 1. Konkrete Methoden + Dateipfade
- **A) `app/Http/Controllers/Energie/EnergieAuslegungController.php`**
  - `wpIndex()` `:185` (GET Formular), `wpBerechnen()` `:196` (POST Rechenseite), `wpDokument()` `:285` (POST Dokument).
  - **Kern `wpErgebnis(array $data, array $eingabe): array` `:360-461`** — EINE Wahrheit für Rechenseite + Dokument.
  - Helfer: `wpOptions()` `:469`, `wpDefaults()` `:483`, `heizsystemLabels()` `:510`, `wpTypLabels()` `:520`.
- **B) `app/Http/Controllers/Energie/EnergiekonzeptController.php`**
  - `baueKonzept()` `:202`, **`baueWp(array $in): ?array` `:256-326`** (WP-Teil, reduzierte Struktur), `baueSanierung()` `:337`, `bauePv()`.
  - Routen: `energie.energiekonzept[.berechnen/.dokument]`.
- **Routen A:** `energie.wp-auslegung` (GET), `energie.wp-auslegung.berechnen` (POST), `energie.wp-auslegung.dokument` (POST) — `routes/web.php:5502-5511`.

## 2. Eingaben und Ausgaben
- **Eingaben (A, validiert `wpBerechnen`/`wpDokument`):** `wp_index`(req int), `heizlast_kw`(req 0.1–100), `heizsystem`(fussbodenheizung|heizkoerper|beides), `wp_typ`(luft_wasser|sole_sonde|sole_kollektor|wasser_wasser), `personen_im_haushalt`(1–20), `ww_mit_wp`(bool), `badewanne_vorhanden`(bool), `investition`(req ≥0), `heizungsart`(oel|gas|…|keine), `heizung_alter`(0–120), `anzahl_we`(≥1), `selbst_bewohnte_we`(≥0), `effizienzbonus`/`einkommensbonus`(bool), `strompreis`(≥0, Default 0.30), Verbrauchs-Plausi (`verbrauch_menge/einheit/aktuelles_heizmedium/verbrauch_zeitraum_jahre/enthaelt_warmwasser`).
- **Ausgabe A (`wpErgebnis`-Array `:428-460`):** `wp_label`, `wp{hersteller,modell,geraetetyp,serie,kaeltemittel,scop_35,scop_55,heizleistung_a7_w35_kw,heizleistung_am7_w35_kw,max_vorlauf_c,modulation_min_kw,modulation_max_kw}`, `heizlast_kw`, `heizsystem_label`, `wp_typ_label`, `jaz`, `vorlauf_temp`, `b_vh`, `q_heiz_kwh`, `q_ww_kwh`, `strom_kwh`, `strompreis`, `stromkosten_jahr`, `ww`(WarmwasserService::ergebnis), `ww_mit_wp`, `verbrauch_plausi`(VerbrauchsService::berechne, null wenn kein Verbrauch), `investition_netto`, `foerderung`(FoerderungService::berechne).
- **Ausgabe B (`baueWp`-Array `:313-325`, REDUZIERT):** `hersteller`, `modell`, `jaz`, `strom_kwh`, `stromkosten_jahr`, `investition_netto`, `foerderung{zuschuss, effektiver_satz_pct, netto_investition}`. **Kein** vorlauf/ww/verbrauch/q_heiz/q_ww.
- **Wichtiger Ist-Befund:** JAZ/Verbrauch/Kosten/Förderung hängen NUR an den Formular-Operanden + Konstanten. Das gewählte Gerät `$hp` liefert **nur Anzeigefelder** (`wp{}`) + den `wp_index`-Null-Check — die **Zahlen sind geräteunabhängig**.

## 3. Doppelte Logik zwischen den Controllern
`EnergiekonzeptController::baueWp` `:277-311` ist ein **Teil-Duplikat** von `EnergieAuslegungController::wpErgebnis` `:376-426`:
- identisch: `HeizlastEingabe::fromArray(methode=direkt,…)`, `jaz->jaz`, `ww->qWwKwh`, `qHeizKwh = heizlast_kw * B_VH_DEFAULT`, `jaz->stromverbrauch`, `kosten->berechne([],null,null,investition)`, `foerderung->berechne([…])`.
- Unterschied: `baueWp` rechnet **kein** `vorlaufTemp`/`ww->ergebnis`/`VerbrauchsService` und gibt weniger Felder zurück. Nutzt `wpDefaults()` als Fallback je Feld (robuster gegen fehlende Keys als A).
→ **Genau diese gemeinsame Kette ist das P1b-Extraktionsziel** (WpCosting/WpFunding, + WpDocument für den Dokumentpfad).

## 4. Aufgerufene Services (Reuse-Kerne, in P1b nur zu verdrahten)
- `App\Services\Heizlast\JazService::{jaz,vorlaufTemp,stromverbrauch}` (nur `HeizlastEingabe` bzw. + qHeiz/qWw).
- `App\Services\Heizlast\WarmwasserService::{qWwKwh,ergebnis}`.
- `App\Services\Heizlast\VerbrauchsService::berechne` (Plausi, `?array`, null ohne Verbrauch).
- `App\Services\Energie\KostenService::berechne(array $modulPosten, ?float $wrPreis, ?float $batteriePreis, float $sonstige=0.0): array` → hier `berechne([], null, null, investition)`; genutzt: `['summe_netto']`.
- `App\Services\Heizlast\FoerderungService::berechne(array $in): array` → genutzt: `zuschuss`, `effektiver_satz_pct`, `netto_investition`.
- Gerätequelle: `App\Repositories\CatalogDeviceRepository::heatPumps()` (Spec-Katalog `product_heat_pump_specs`), Auswahl per `->values()->get($wp_index)`.

## 5. Bestehende Rundungsstellen (Ist — festzuschreiben, nicht zu ändern)
- A `wpErgebnis`: `jaz => round($jaz, 2)` `:447`; `q_heiz_kwh => round($qHeizKwh)` `:450`; `q_ww_kwh => round($qWwKwh)` `:451`; `strom_kwh => round($stromKwh)` `:452`; `stromkosten_jahr => round($stromkostenJahr)` `:454`. `investition_netto` = `KostenService`-Wert (dessen eigene Rundung), `foerderung` = Service-Werte.
- B `baueWp`: `jaz => round(,2)` `:316`; `strom_kwh => round()` `:317`; `stromkosten_jahr => round()` `:318`.
- Konzept-Gesamt `baueKonzept`: `round()` auf `investition_eur/foerderung_eur/eigenanteil_eur/einsparung_eur_a` `:241-244`.
- Konstante: `qHeizKwh = heizlast_kw * HeizlastKonstanten::B_VH_DEFAULT`.

## 6. Datenquellen für Kosten und Förderung (Ist)
- **Kosten:** ausschließlich der Formularwert `investition` → `KostenService::berechne([],null,null,investition)`; **kein** `products`/`retail_price` (bestätigt: die spätere PS-3-Preisanbindung ist NICHT Teil des heutigen Pfades). „Fehlender Preis" heute = `investition=0` (validiert `min:0`).
- **Förderung:** `FoerderungService::berechne` mit `foerderfaehige_kosten=investition_netto`, `anzahl_we`, `selbst_bewohnte_we`, `we_unter_40k`(einkommensbonus? min(selbstWe,1):0), `heizungsart`(keine→null), `heizung_alter`, `effizienzbonus`, `zusatz=0`, `rabatt=0`.

## 7. Dokument-/PDF-Pfad
- A: `wpDokument()` `:285` validiert wie `wpBerechnen`, Null-Check `$hp`, ruft **dieselbe** `wpErgebnis()`-Wahrheit, rendert `resources/views/admin/energie/wp_auslegung_dokument.blade.php` (keine Doppelrechnung). Fehlerfall: redirect zurück mit `error`.
- B: `energiekonzept.dokument` rendert `energiekonzept_dokument.blade.php` aus `baueKonzept`.
→ P1b `WpDocumentService` kapselt die Dokumentdaten-Aufbereitung; Layout/Blade bleibt unverändert.

## 8. Vorhandene Tests
- **Keine** Feature-/Controller-Tests für `wp-auslegung`/`energiekonzept` (grep leer). Vorhanden nur angrenzend: `tests/Unit/Energie/HeatPumpKennlinieMappingTest`, `tests/Unit/Auslegung/WpAuslegungsketteServiceTest` (Orchestrator, gestubt), `tests/Feature/Offer/WpKatalogMatchingPreviewTest` (anderer Strang). → P1a schafft die Charakterisierung **neu**.

## 9. Noch nicht abgedeckte Fälle (P1a schließt sie)
Normalfall vollständig · WW mit WP · WW ohne WP · fehlender Preis (`investition=0`) · unvollständige/fehlende Eingabe (Validierungsfehler 422) · Rundungsgrenzen · Förderung anwendbar · Förderung nicht anwendbar (`heizungsart=keine`) · Dokumentdaten (gleiche Wahrheit wie Rechenseite) · Fehler-/Hinweisfall (`wp_index` ohne Gerät → Fehlermeldung/redirect) · Duplikat-Parität A↔B (gemeinsame Felder gleich bei gleichen Eingaben).

## 10. Minimaler Testdateiplan (Charakterisierung, keine Produktivänderung)
- **`tests/Feature/Energie/WpAuslegungCharakterisierungTest.php`** — HTTP gegen `energie.wp-auslegung.berechnen`/`.dokument`; seedet 1 minimales `product_heat_pump_specs` (+products/brands) für `wp_index=0`; pinnt das `ergebnis`-View-Model (Struktur + Golden-Werte für jaz/q_heiz/q_ww/strom/stromkosten/investition_netto/foerderung) über die Testmatrix; Fehler-/Validierungsfälle.
- **`tests/Feature/Energie/EnergiekonzeptWpCharakterisierungTest.php`** — HTTP gegen `energie.energiekonzept.berechnen`; pinnt den `wp`-Teil + Parität zu A bei gleichen Eingaben.
- Golden-Werte werden per Erstlauf erfasst und exakt gepinnt (Golden-Master). Kein Verändern der Ist-Werte.

**Regeln P1a:** keine Extraktion, keine Produktivänderung (außer minimale Testzugänglichkeit nur nach eigenem Pflicht-Stopp — hier **nicht nötig**, da HTTP-Ebene charakterisiert), kein Ranking, keine Orchestrator-Verdrahtung, keine View/Route/Feld-Änderung, kein Commit ohne Evaluator, kein Push.
