# Vollständigkeitsbericht — Read-only-Gesamtanalyse

**Stand:** 2026-07-14 · **read-only** (keine Codeänderung, keine Migration, kein Commit, kein Push) · **Git:** `a00bb0a`.
**Grundlage:** Masterentscheidung §1–§20, fünf codebelegte Bestandsaufnahmen, Abgleich gegen aktiven Code-Stand.

---

## 1. Zählung der Anforderungen (aus `anforderungs-und-lueckenmatrix.md`)

**Erfasst gesamt: 80 Muss-Anforderungen** in **18 Tabellenabschnitten / 19 ID-Präfixen** (WIN + DOOR getrennt): ARCH 10, DATA 15, BLDG 9, LIDAR 3, HL 3, HYD 2, WP 4, PV 3, BAT 2, WB 2, GRID 2, ROOF 2, WIN 2 + DOOR 1, FAC 2, UX 6, SEC 4, TEST 4, OFFER 4.

**Zählung nach Evaluator-Korrektur B1** (SEC-03 von `widersprüchlich` → `besteht_teilweise`, da auth-gated). Alle 8 Statuskategorien mit Anzahl (auch = 0):

| Status | Anzahl (von 80) | Anteil |
|---|---|---|
| `besteht_belastbar` | 13 | 16 % |
| `besteht_teilweise` | 32 | 40 % |
| `fehlt` | 31 | 39 % |
| `widersprüchlich` | 3 | 4 % |
| `umgesetzt_ungeprüft` | 1 | 1 % |
| `blockiert` | 0 | 0 % |
| `umgesetzt_geprüft` | 0 | 0 % |
| `nicht_anwendbar` | 0 | 0 % |
| **Summe** | **80** | 100 % |

*(Kontrolle: 13+32+31+3+1+0+0+0 = 80. Anteile gerundet: 16+40+39+4+1 = 100 %.)*

## 2. Belastbar bestehend (13)

DATA-01/02/04/07/12/14 (Kern-Datenwahrheiten, Anforderungsprofil, Preis/Verfügbarkeit getrennt), BLDG-01/06 (führende versionierte Geometrie + Topologie-Gate), HL-01, HYD-01/02 (Heizlast-/Heizflächenkern), WP-02 (Match/Bivalenz/Ranking), TEST-04 (Gegenbeweis-Praxis). Das ist das gebaute Fundament aus den vorausgegangenen Wellen (Anforderungsprofil-Versionierung, Geometrie-Topologie-Gate, WP-Auslegungskette, portierter Heizlast-Kern).

## 3. Teilweise bestehend (32)

Schwerpunkt: die bedarfsgeführte Anbindung fehlt noch (ARCH-01/04/05/07/08), Herkunft/Belastbarkeit nur rudimentär (DATA-06/09/10/13), Geometrie-Konsolidierung offen (BLDG-02/03/05/07/08), WP-Bedarf unvollständig (WP-01/03), Fassade/Sanierung transient (FAC), Rechte lückenhaft (SEC-01/02/03/04), Tests/Angebotsübergabe teilweise (TEST-01/02, OFFER-01/04). **SEC-03** ist nach Korrektur hier (auth-gated, aber Callback-Auth-Widerspruch offen).

## 4. Fehlend (31)

Ganze Module: **Wallbox (WB)**, **Netz/Messkonzept (GRID)**, **Speicher-Sizing (BAT)**, **Fenster/Türen-Konfigurator (WIN/DOOR)**, **echte Dachgeometrie (ROOF-01)**, **LiDAR (LIDAR)**. Ferner: Ergebnis-Stale-Trigger (DATA-15), strukturierte Ergebnis-Persistenz (DATA-03), Datenübernahme/-prüfung (DATA-05/08), Auswahl/Freigabe-Zustände (ARCH-09/10, WP-04), Filter-Schicht (ARCH-02, ARCH-06, UX-06), UI-Rahmen (UX-01/02/03), Auslegung→Angebot-Write (OFFER-02/03), E2E/Browser (TEST-03), PV-Varianten (PV-02).

## 5. Widersprüchlich / Parallelwahrheiten (3)

- **DATA-11 / BLDG-09 / ROOF-02** — mehrere Geometrie-Wahrheiten: **3 schreibbare Stores** (`anforderungsprofile.gebaeude_geometrie` führend, `raum_geometrien` legacy, `p_v_roof_plans.roof_structures` PV-Insel) + **2 abgeleitete/Schätz-Quellen** (`sanierungs_varianten.massnahmen` = Maßnahmen-JSON, `RoofAreaEstimator` = nicht-persistenter Schätzer). Plus Positionsstruktur `offer_product_lists` vs. `offer_details.sections`.
- **SEC-03 KORRIGIERT (kein Sicherheitsleck):** Der frühere Befund „`/ids/callback` öffentlich schreibend ohne Auth" ist **widerlegt** — die Route läuft hinter `Authenticate` (Konstruktor `IdsController.php:24` `$this->middleware('auth')`; `route:list -v` belegt `Authenticate`). SEC-03 ist damit **kein** Widerspruch mehr, sondern `besteht_teilweise` (auth vorhanden). **Verbleibender Nebenbefund (Funktion/Design, kein P0):** die Route soll laut Kommentar „PUBLIC" sein, ist aber durch das Konstruktor-`auth` gated → externer GC-Online-Callback vermutlich funktional gebrochen; falls bewusst zu öffnen, fehlt HMAC/Secret/IP-Schutz.

## 6. Ungeprüft (1)

UX-05 (Light/Dark) — technisch durch Vuexy gegeben, aber ohne Browser-Nachweis.

## 7. Nicht zugeordnete Codepfade (Hinweise)

- `CustomerOfferWPController` / `HeatpumpController` — leere Resource-Stubs (veraltet oder geplant, unklar).
- `roof_config/config.blade copy 2/3.php`, `config2.blade copy.php`, `pvgis_details.blade copy.php` — Cruft-Kopien.
- three.js `public/js/pvtools`, `roof_config/*.blade` — Playground/Test-Routen (`/testnav`, `/roofs`) ohne DB-Naht.
- `docs/ap4-geometrie-3d-gebaeudemodell-validierung.md` — teilweise überholt (listet Topologie-Gate/Referenztests/versionierte Geometrie als „fehlt", die inzwischen gebaut sind).

## 8. Nicht getestete fachliche Bereiche

PV-Projektauslegung (`PvProjektService` „gebrochen"), Sanierungs-Wirtschaftlichkeit direkt, Speicher-Sizing/Wallbox/Netz (nicht existent), Auslegung→Angebot-Übergabe (kein Pfad), E2E/Browser durchgängig.

## 9. Bekannte Parallelwahrheiten (zur Konsolidierung)

1. Geometrie: **3 schreibbare Stores** (`gebaeude_geometrie` führend, `raum_geometrien` legacy, `p_v_roof_plans` PV-Insel) + 2 abgeleitete/Schätz-Quellen → eine Wahrheit am kanonischen Modell; abgeleitete andocken, nicht als Zweitwahrheit schreiben.
2. Angebotspositionen (`offer_product_lists` vs `offer_details.sections`) → Führung entscheiden.
3. Preisanker: Spec-`product_id` (nullable, Deklaration ohne `->foreign()`-Constraint trotz „FK"-Kommentar) ↔ `master_set_components` konsolidieren.

## 10. Pflicht-Stopps (für spätere Umsetzung)

- Jede additive Migration (DATA-03 `auslegung_ergebnis`; SEC-04 Unique-Index; DATA-09 Herkunft) = eigener Slice mit Pflicht-Stopp + echter Yama-Migrationsfreigabe.
- `/ids/callback`-Auth-Widerspruch (SEC-03) = **Funktions-/Design-Befund** (kein P0-Leck): entweder auth-pflichtig belassen oder `->except(['callback'])` + Signatur/Secret/IP.
- Geometrie-Konsolidierung / Legacy-Backfill = eigener Deploy-Tag-Slice (Objekt-Zuordnung ohne Raten).

## 11. Empfohlene Reihenfolge

0.1–0.4 (laufend/geparkt: WP 3b P1a/P1b, M-1, M-2) → 1.x (Datenwahrheit/Herkunft/Stale/Ergebnis-Persistenz) → 2.x/3.x (Geometrie konsolidieren) → 5.1 (Heizlast→WP-Naht) → 6.x (Preis/Filter) → 7.x (Angebotsübergabe) → 5.3–5.8 (neue Module) → 8.x (UI) → 4.x (LiDAR). Der Callback-Auth-Widerspruch (9.1) ist **kein P0** mehr und kann bei Gelegenheit als kleiner Funktions-/Design-Slice laufen. Details: `umsetzungsfahrplan.md`.

## 12. Ist die Analyse vollständig?

**Ja — die Analyse ist vollständig und lückenlos dokumentiert.** Jede Muss-Anforderung der Masterentscheidung (§2–§14) ist mit stabiler ID in der Nachweismatrix erfasst; kein Punkt ohne Zeile, Status und Beleg. Abgeglichen gegen: dieses Masterdokument, die fünf codebelegten Bestandsaufnahmen, die geänderten/vorhandenen Dateien, die vorhandenen Analyse-Docs (crm-inventur, ux-audit, bereich2-*, wp-stufe*, g0*, ap2/3/4, gesamtfahrplan, kundenprofil-*) und den aktuellen Code-Stand `a00bb0a`.

**Ausdrücklich NICHT behauptet:** dass die Plattform vollständig umgesetzt sei. Der Umsetzungsgrad ist 16 % belastbar / 40 % teilweise / 39 % fehlend / 4 % widersprüchlich / 1 % ungeprüft. Es wurde in diesem Slice **nur analysiert und dokumentiert** — kein Code, keine Migration, kein Commit, kein Push.

## 13. Warum nichts vergessen wurde (Beleg)

- **Vollständige Anforderungsmatrix:** 80 IDs in 18 Tabellenabschnitten / 19 ID-Präfixen, jede mit Status + Beleg + Restarbeit.
- **Abgleich gegen Masterdokument:** §2 (ARCH), §3 (ARCH-02/03), §4 (ARCH-04/05/06), §5 (DATA-01..04, BLDG), §6 (DATA-05..08), §7 (DATA-09/10), §8 (BLDG/LIDAR), §9 (HL/HYD/WP/PV/BAT/WB/GRID/ROOF/WIN/DOOR/FAC), §10 (ARCH-09/10), §11 (Filter/UX-06), §12 (DATA-12..14), §13 (UX), §14 (ARCH-07/08, DATA-15, SEC) — jeder Paragraf auf IDs abgebildet.
- **Abgleich gegen aktiven Stand:** Bestandsaufnahmen mit Datei:Zeile-Belegen; Doc-Drift (ap4) benannt.
- **Abgleich gegen geänderte Dateien/Tests/Manifeste:** die geparkte WP-3b-Arbeit (P1a-Tests, P0-Commit `a00bb0a`) ist in der Matrix (ARCH-07, TEST-01, SEC-04, OFFER-03) und im Fahrplan (Phase 0) verzeichnet.

---

*Ende der Read-only-Gesamtanalyse. Sechs Pflicht-Dokumente liegen vor (`ADR-0001`, Gesamtarchitektur, Anforderungs-/Lückenmatrix, Modul-/Abhängigkeitsmatrix, Umsetzungsfahrplan, dieser Bericht).*
