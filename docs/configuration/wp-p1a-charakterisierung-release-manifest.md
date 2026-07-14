# RELEASE-MANIFEST — WP Stufe 3b / P1a: Charakterisierung der WP-Auslegungspfade

**Stand:** 2026-07-14 · **read-only Charakterisierung** (kein Produktivcode, keine Migration, kein Commit, kein Push).
**HEAD:** `f0055d6` · **Branch:** `private/app-code-backup` · **Umgebung:** PHP 8.4.21 · Laravel 11.48.0 · Test-DB `ticket_testing` (RefreshDatabase, MySQL).
**Abschlussformel:** *Bestehendes Verhalten festgeschrieben, nicht verändert.*

---

## 1. Saubere committed Baseline (ohne lokale P1a-Dateien)
Ermittelt in einem **git-Worktree auf `f0055d6`** (eigenes reales vendor, kein Symlink) — dort existiert `tests/Feature/Energie/` nicht (committed Zustand ohne P1a).
- Erst-Lauf zeigte 12 Rotfälle — **rein umgebungsbedingt** (fehlendes Vite-`public/build/`-Manifest → `@vite`-Views werfen). Nach Kopieren der gebauten Assets in den Worktree:
- **`php artisan test` → 608 passed, 1 failed (2113 assertions), 32,35 s.** Einziger Rotfall: `Tests\Feature\Invoice\InvoiceDeletionGuardTest` (Reverb `cURL error 7 … localhost:6001`) = bekannter E4-Baseline.
- Die saubere Baseline wurde **nicht committet**; der Worktree ist Wegwerf-Infrastruktur.

## 2. Untersuchte Dateien (vollständig gelesen)
- `app/Http/Controllers/Energie/HeizlastController.php`
- `app/Http/Controllers/Energie/EnergieAuslegungController.php` (WP-Teil `wpIndex/wpBerechnen/wpDokument/wpErgebnis`)
- `app/Http/Controllers/Energie/EnergiekonzeptController.php` (`baueKonzept/baueWp`)
- Services: `JazService`, `WarmwasserService`, `VerbrauchsService`, `App\Services\Energie\KostenService`, `App\Services\Heizlast\FoerderungService`, `WaermepumpenMatchService`, `BivalenzService`, `WpAuslegungsketteService`, `CatalogDeviceRepository`
- Views/Dokument: `wp_auslegung.blade.php`, `wp_auslegung_dokument.blade.php`, `energiekonzept*.blade.php`, `heizlast.blade.php`
- Vorgängerbefund: `docs/wp-stufe3b-p1a-bestandsaufnahme.md` (untracked, 10-Punkte-Analyse)

## 3. Controller-Eingaben (Ist)
- **wpBerechnen/wpDokument (`:198-220`):** `wp_index`(req), `heizlast_kw`(req 0.1–100), `heizsystem`, `wp_typ`, `personen_im_haushalt`(req), `ww_mit_wp`, `badewanne_vorhanden`, `investition`(req ≥0), `heizungsart`, `heizung_alter`, `anzahl_we`, `selbst_bewohnte_we`, `effizienzbonus`, `einkommensbonus`, `strompreis`(Default 0.30), Verbrauchs-Plausi-Felder. **Kein Objektbezug** (produktzentriert, `wp_index` zuerst).
- **baueWp (`:256-311`):** dieselben Felder unter `wp.*`, mit `wpDefaults()`-Fallback je Feld; `required_if:wp_aktiv,1`.
- **HeizlastController (`:93-109`):** `projekt.*` (PLZ/Baujahr/Vorlauf/Spreizung), `raum.*` (Fläche/Höhe req), `bauteile[]` (typ/grenzflaeche/flaeche/u_wert req). Kein Objektbezug.

## 4. Controller-Ausgaben (Ist)
- **wpErgebnis (`:428-460`):** `wp_label`, `wp{…Anzeigefelder…}`, `heizlast_kw`, `heizsystem_label`, `wp_typ_label`, `jaz`, `vorlauf_temp`, `b_vh`, `q_heiz_kwh`, `q_ww_kwh`, `strom_kwh`, `strompreis`, `stromkosten_jahr`, `ww`(WarmwasserService), `ww_mit_wp`, `verbrauch_plausi`(null ohne Verbrauch), `investition_netto`, `foerderung`(FoerderungService).
- **baueWp (`:313-325`, reduziert):** `hersteller`, `modell`, `jaz`, `strom_kwh`, `stromkosten_jahr`, `investition_netto`, `foerderung{zuschuss, effektiver_satz_pct, netto_investition}`.
- **HeizlastController:** `ergebnis.gebaeude{standardheizlast_kw, auslegungsheizlast_kw, grundflaeche_m2, spezifische_heizlast_w_m2, plausi}` + `wp{geraete, design_punkt, hinweis, verbindlich}` (hartkodiert luft_wasser/heizkoerper).

## 5. Datenquellen je fachlichem Wert
| Wert | führende Quelle | Methode/Datei | Fallback | Belastbarkeit |
|---|---|---|---|---|
| Heizlast (Rechner) | Formular-Bauteile → transientes Projekt | `HeizlastProjektService::fuerProjekt` | — | Rechner-Tool, transient |
| Heizenergiebedarf `q_heiz_kwh` | `heizlast_kw × B_VH_DEFAULT(2000)` | `wpErgebnis:404` | — | Konstante |
| Warmwasserbedarf `q_ww_kwh` | Personen × 700 kWh | `WarmwasserService::qWwKwh` | — | Kern |
| JAZ | Formular-Operanden (NICHT Gerät) | `JazService::jaz` | — | Kern |
| Stromverbrauch | `JazService::stromverbrauch(e, qHeiz, qWw)` | `:405` | — | Kern |
| Gerätepreis | **nicht verwendet** | — | — | — (Invest = Formularwert) |
| Anlagenkosten (Invest) | Formularfeld `investition` | `KostenService::berechne([],null,null,invest)` | 0 | Nutzereingabe |
| Förderung | `FoerderungService::berechne` (BEG) | `:416` | — | Kern (Regelwerk) |
| ausgewähltes Gerät | `repo->heatPumps()->get(wp_index)` | `CatalogDeviceRepository` | Null-Check → Fehler | Katalog |
| Dokumentdaten | dieselbe `wpErgebnis`-Wahrheit | `wpDokument:344` | — | keine Doppelrechnung |

## 6. Doppelte Logik (nicht bereinigt — nur festgeschrieben)
| Verhalten | Controller A (`wpErgebnis`) | Controller B (`baueWp`) | identisch | Abweichung |
|---|---|---|---|---|
| JAZ | `jaz->jaz(e)` | `jaz->jaz(e)` | ja | — |
| q_ww | `ww->qWwKwh(e)` | `ww->qWwKwh(e)` | ja | — |
| q_heiz | `hl × B_VH` | `hl × B_VH` | ja | — |
| Stromverbrauch | `jaz->stromverbrauch` | `jaz->stromverbrauch` | ja | — |
| Kosten | `KostenService::berechne` | `KostenService::berechne` | ja | — |
| Förderung | `FoerderungService::berechne` | `FoerderungService::berechne` | ja | — |
| vorlauf/ww-Ergebnis/verbrauch_plausi | vorhanden | **fehlt** in B | nein | B ist reduziert |
Test `EnergiekonzeptWpCharakterisierungTest::test_wp_teil_gleiche_kernwerte_wie_wp_auslegung` belegt die Parität der gemeinsamen Kernwerte → **P1b-Extraktionsgrundlage** (beide auf denselben WpCosting/WpFunding-Dienst umstellbar).

## 7. Testmatrix — Soll-/Ist-Werte (Golden gegen `f0055d6` erfasst)
| Matrix | Test | gepinnte Ist-Werte |
|---|---|---|
| 5.1 Standard | `test_normalfall_pinnt_rechenkette_und_rundung` | jaz 2.9, vorlauf 55.0, q_heiz 16000, q_ww 2800, strom 6483, stromkosten 1945, invest_netto 25000, zuschuss 12500, eff 50 % |
| 5.2 WW ohne WP | `test_warmwasser_ohne_wp_senkt_stromverbrauch` | phi_ww 0.0, strom 5517, stromkosten 1655, q_ww 2800 |
| 5.2 WW/Personen | `test_warmwasserbedarf_skaliert_mit_personen` | P1: q_ww 700, strom 5759 · P6: q_ww 4200, strom 6966 |
| 5.3 niedriger Bedarf | `test_niedriger_heizbedarf…` | q_heiz 8000, strom 3724, stromkosten 1117 |
| 5.3 hoher Bedarf | `test_hoher_heizbedarf…` | q_heiz 30000, strom 11310, stromkosten 3393 |
| 5.3 Verbrauchsmethode | `test_verbrauchsmethode_erzeugt_plausi_block` | ohne → verbrauch_plausi null; mit → nicht null, strom unverändert 6483 |
| 5.4 fehlender Preis | `test_fehlender_preis_investition_null` | invest_netto 0, zuschuss 0, eff 0 |
| 5.5 Förderung ohne Klimabonus | `test_foerderung_ohne_klimabonus…` | klima_berechtigt false, zuschuss 7500, eff 30 %, netto 17500 |
| 5.5 Förderung Boni | `test_foerderung_mit_effizienz_und_einkommensbonus` | effizienz 5, einkommen 30, zuschuss 17500, eff 70 %, netto 7500 |
| 5.5 Förder-Deckel | `test_foerderdeckel_begrenzt…` | invest 50000, deckel 30000, foerderfaehig 30000, zuschuss 15000 |
| 5.6 Produktwahl ungültig | `test_wp_index_ohne_geraet_liefert_fehler_ohne_ergebnis` | ergebnis null, Fehlermeldung „nicht gefunden" |
| 5.6 Geräte-Unabhängigkeit | `test_geraetewahl_aendert_nur_anzeige_nicht_die_zahlen` | idx0/idx1: Zahlen identisch, nur `wp.modell` TM-8/TM-12 |
| 5.7 Dokument = Rechenwahrheit | `test_dokument_liefert_dieselbe_rechenwahrheit…` | wpDokument == wpBerechnen (Kernwerte) |
| 5.7 Dokument fehlender Preis | `test_dokument_bei_investition_null` | invest_netto 0, zuschuss 0 |
| 5.8 Energiekonzept-Parität | `test_wp_teil_gleiche_kernwerte_wie_wp_auslegung` | jaz 2.9, strom 6483, kosten 1945, zuschuss 12500 |
| 5.8 nur WP aktiv | `test_nur_wp_aktiv_sanierung_und_pv_null_gesamt_spiegelt_wp` | sanierung/pv null; gesamt invest 25000, förderung 12500, eigenanteil 12500 |
| 5.8 WP ohne Gerät | `test_wp_index_ohne_geraet_liefert_null_wp_teil` | konzept.wp null |
| 5.9 Validierungsfehler (WP) | `test_unvollstaendige_eingabe_wirft_validierungsfehler` | `ValidationException` |
| HL Heizlast+Match | `test_heizlast_ergebnis_und_wp_matchblock` | auslegungsheizlast 4.35 kW, spez. 43.5 W/m², WP-Block {geraete,design_punkt,hinweis,verbindlich} |
| HL transient | `test_transient_kein_projekt_persistiert` | heizlast_projekte-Count unverändert |
| HL Validierung | `test_fehlende_pflichtfelder_werfen_validierungsfehler` | `ValidationException` |

## 8. Rundungsregeln (Ist)
`jaz => round(,2)`; `q_heiz_kwh/q_ww_kwh/strom_kwh/stromkosten_jahr => round()` (ganzzahlig); `investition_netto`/`foerderung.*` = KostenService/FoerderungService-eigene Rundung (`foerderfaehig`/`netto` auf 2 NK, `zuschuss`/`effektiver_satz_pct` gerundet). Konzept-Gesamt `round()` auf alle vier Summen. Konstante `B_VH_DEFAULT = 2000`.

## 9. Dokumentierte Altprobleme (nur festgehalten, NICHT verbessert)
- **A1 — Geräte-Unabhängigkeit:** JAZ/Strom/Kosten/Förderung hängen ausschließlich an Formular-Operanden; das gewählte Gerät ändert **nur Anzeigefelder** (belegt: idx0/idx1 identische Zahlen). Fachlich fragwürdig (SCOP/Leistung des Geräts fließen nicht in JAZ/Strom ein). **Folgepunkt** für P1c/Orchestrator-Verdrahtung, nicht P1a/P1b.
- **A2 — Duplikat-Kette** `baueWp` ↔ `wpErgebnis` (§6) — Ziel der P1b-Extraktion.
- **A3 — HeizlastController-WP-Match hartkodiert** `luft_wasser`/`heizkoerper` (kein Bedarf/Objektbezug). HeizlastController bleibt laut Entscheidung schlank.
- **A4 — Gerätepreis nicht genutzt:** Invest = Formularwert; Auto-Preisanker konstruktiv unmöglich (Spec-`product_id` nullable ohne FK) — siehe Anforderungsmatrix DATA-13/Preisanker.

## 10. Geänderte/neue Dateien
- **Neu (untracked, Tests):** `tests/Feature/Energie/WpAuslegungCharakterisierungTest.php` (15), `tests/Feature/Energie/EnergiekonzeptWpCharakterisierungTest.php` (3), `tests/Feature/Energie/HeizlastCharakterisierungTest.php` (3).
- **Neu (Doku):** dieses Manifest.
- **Produktivcode:** **KEINE Änderung** (`git status app/ database/ routes/ config/ resources/` → leer). Keine Testzugänglichkeits-Änderung am Produktivcode nötig (Charakterisierung über öffentliche Controller-Methoden + View-Model, Blade wird nicht gerendert).
- **CLAUDE.md:** unberührt (lokale Vorbestand-Drift, nicht Teil von P1a).

## 11. Testbefehle & Rohresultate
```
# saubere committed Baseline (Worktree, ohne P1a):        608 passed, 1 failed (2113)  → nur Reverb E4
php artisan test tests/Feature/Energie/                    → 21 passed (126 assertions)
php artisan test tests/Feature/Geometrie tests/Feature/Anforderungsprofil tests/Unit/Auslegung → 33 passed
php artisan test  (volle Suite MIT P1a)                    → 629 passed, 1 failed (2239)
```
**Delta Baseline→Gesamt = +21 (genau die P1a-Tests), einziger Rotfall unverändert Reverb E4** (`InvoiceDeletionGuardTest`, `localhost:6001`) — **nicht** durch P1a verursacht (Signatur identisch, kein Produktivcode-Diff).

## 12. Offene Punkte für P1b
- Extraktion `WpCostingService` (Kosten/Betriebskosten/Vergleich) + `WpFundingAssessmentService` (Förderung) + `WpDocumentService` aus `wpErgebnis`/`baueWp` — **byte-/wertgleich** gegen diese Golden-Werte.
- `baueWp`/`wpErgebnis` danach auf die Dienste umstellen (Parität via `test_wp_teil_gleiche_kernwerte…`).
- Altprobleme A1/A3/A4 bleiben für P1c/Folgeslices (nicht in P1b).

## 13. Ballbesitz
Generator hat umgesetzt → **unabhängiger read-only Evaluator** (eigenes Nachrechnen ausgewählter Sollwerte, Prüfung Ist-Messung, kein Produktivcode-Diff, saubere Baseline getrennt) → danach **Yama** (separate Commit-Freigabe). **Kein Commit, kein Push, keine Fortsetzung mit P1b.**
