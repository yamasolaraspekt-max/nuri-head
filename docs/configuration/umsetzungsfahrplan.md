# Umsetzungsfahrplan — bedarfsgeführte Konfiguration & Auslegung

**Stand:** 2026-07-14 · **read-only Plan** (kein Bau vor gesonderter Freigabe je Slice) · **Git:** `a00bb0a`.
**Prinzip:** kleine, einzeln prüfbare Slices; **keine Großwelle**, die mehrere unabhängige fachliche Änderungen unprüfbar vermischt. Jeder Slice = eigener Startblock (Anforderungs-IDs · Scope · Nicht-Scope · Datenwahrheit · Dateien · Migration · Rechte · Tests · Browserprüfung · Gegenbeweise · Release-Manifest · Pflicht-Stopp). Reihenfolge folgt der systemweiten Regel: **Konzept → Workflow → Verknüpfung → erst dann automatisieren**.

Jeder Slice endet mit: Tests → Release-Manifest → unabhängiger Evaluator → STOPP vor Commit → Matrix-Update.

---

## Phase 0 — Laufende/geparkte Arbeit zuerst abschließen

| Slice | IDs | Inhalt | Status |
|---|---|---|---|
| 0.1 | ARCH-07, TEST-01 | **WP Stufe 3b P1a** Charakterisierungstests (geparkt, uncommittet) | fortsetzen nach Freigabe |
| 0.2 | ARCH-07 | **WP Stufe 3b P1b** Dienst-Extraktion (WpCosting/WpFunding/WpDocument) | nach P1a |
| 0.3 | SEC-04, DATA-02 | **M-1** Unique-Index `(verankerbar,version)` + `anlegen`-Härtung (additive Migration, Pflicht-Stopp) | eigener Slice |
| 0.4 | OFFER-03 | **M-2** `StaleProfilVersionException` → HTTP 409 statt 422 | eigener Slice |

## Phase 1 — Datenwahrheit & Herkunft (Fundament)

| Slice | IDs | Inhalt | Datenwahrheit | Migration |
|---|---|---|---|---|
| 1.1 | DATA-09, DATA-06 | Herkunfts-/Vertrauensstufen-Enum systemweit erweitern (CAD/Dokument/LiDAR/…); Übernahme-Metadaten (Datum/Bearbeiter/Quelldatensatz) | `anforderungsprofil_werte` | additiv (Pflicht-Stopp) |
| 1.2 | DATA-03 | Additive JSON-Spalte `anforderungsprofile.auslegung_ergebnis` (Mindestvertrag: `status current/stale`, `input_hash`, `catalog_snapshot_at`, `weights`, `candidates`, …) | Profil | additiv (Pflicht-Stopp, echte Yama-Freigabe) — = WP Stufe 3b-1 |
| 1.3 | DATA-15, BLDG-04 | Ergebnis-Stale-Mechanismus: bei Eingabe-/Geometrieänderung abhängige `auslegung_ergebnis` auf `stale`; Eingaben vorwärts, Ergebnisse NICHT still fortführen | Profil | ggf. additiv |
| 1.4 | DATA-05, DATA-08 | Datenübernahme-Service + Datenprüf-Vorstufe (vorhanden/veraltet/widersprüchlich/fehlt/blockierend) vor jeder Berechnung | Quelltabellen | keine |

## Phase 2 — Gebäudemodell konsolidieren (eine Geometrie)

| Slice | IDs | Inhalt |
|---|---|---|
| 2.1 | DATA-11, BLDG-07 | `raum_geometrien`/`heizlast_*`-Legacy belegt stilllegen (Deploy-Tag-Backfill nach `g0c-migrationsplan.md`); Objekt-Zuordnungsstrategie (kein Raten) |
| 2.2 | BLDG-05, BLDG-01 | Mehrraum/Geschoss im Grundriss-Editor + `gebaeude_geometrie.raeume[]` |
| 2.3 | BLDG-08 | Import-/Datenherkunft an Geometrie mitführen (DXF/PDF/Plan → Quelle in `gebaeude_geometrie`) |
| 2.4 | BLDG-03 | 3D-Ableitung aus kanonischem 2D-Modell (Playground `pvtools`/`roof_config` entwerten oder andocken) |

## Phase 3 — 2D-Planer & Dach/PV-Geometrie vereinen

| Slice | IDs | Inhalt |
|---|---|---|
| 3.1 | ROOF-01, BLDG-09 | Kanonische Dachgeometrie (obere Gebäudekontur/Neigung/First/Traufe) am Modell |
| 3.2 | ROOF-02, PV-03 | `p_v_roof_plans` an kanonisches Dach andocken; `RoofAreaEstimator` entwerten/gaten (kein verbindliches Maß aus Schätzung) |

## Phase 4 — LiDAR (spätere Welle)

| Slice | IDs | Inhalt |
|---|---|---|
| 4.1 | LIDAR-01/02/03 | LiDAR-Aufnahme als Vorschlagsquelle + Bestätigungs-Workflow; nie automatische Modell-/Material-/Bestellmaß-Wahrheit |

## Phase 5 — Auslegungsmodule bedarfsgeführt anbinden

| Slice | IDs | Inhalt |
|---|---|---|
| 5.1 | HL-03, ARCH-08 | Heizlast-Adapter `berechneUndSchreibe` an Controller verdrahten; WP-Auslegung zieht Heizlast aus Profil statt neuer Eingabe |
| 5.2 | WP-01, WP-04, ARCH-09 | WP-Bedarf vervollständigen (Aufstellung/Schall/Elektro/Netz); Auswahl/Freigabe-Zustände modellieren (WP Stufe 3c/P1c) |
| 5.3 | PV-01/02 | PV-Service reparieren, an validierte Dachgeometrie binden, Ausgabevarianten |
| 5.4 | BAT-01/02 | Speicher-Sizing-Service (kWh) neu |
| 5.5 | WB-01/02 | Wallbox-Modul neu |
| 5.6 | GRID-01/02 | Netzanschluss/Messkonzept-Modul neu |
| 5.7 | WIN-01/02, DOOR-01 | Fenster-/Tür-Öffnungs-Domäne (Aufmaß-Gate) |
| 5.8 | FAC-01/02 | Fassaden-/Dämmungs-Flächen aus Geometrie + Mengen/Wärmebrücken |

## Phase 6 — Produktfilter, Preis, Verfügbarkeit

| Slice | IDs | Inhalt |
|---|---|---|
| 6.1 | DATA-13, WP-02 | Preis-Klassen-DTO (Gerätepreis ≠ Gesamtinvestition); `products.retail_price` als „Geräte-Listenpreis" kennzeichnen |
| 6.2 | — (Preisanker) | Spec↔Preis-Set über gemeinsame `product_id` konsolidieren (Auto-Anker ermöglichen); `product_heat_pump_specs.product_id` echten `->foreign()`-Constraint geben (heute nullable ohne FK-Constraint trotz „FK"-Kommentar) |
| 6.3 | ARCH-02, ARCH-10, UX-06 | Filter-/Constraint-Schicht (Query-State) + „bessere Lösung ausgeblendet"-Hinweis + Abweichungs-Begründung |
| 6.4 | DATA-12/14 | Verfügbarkeit sauber getrennt (kein `inventories.quantity`-Missbrauch) |

## Phase 7 — Angebotsübergabe

| Slice | IDs | Inhalt |
|---|---|---|
| 7.1 | OFFER-02 | Auslegung → `offer_details.sections`-Write-Adapter (mit Ergebnis-/Produkt-/Preisversion) |
| 7.2 | OFFER-01/03, ARCH-09 | Auswahl-/Freigabestatus persistieren; Snapshot bei Freigabe |
| 7.3 | DATA-11 | Positionsstruktur-Doppelwahrheit auflösen (`offer_product_lists` vs `offer_details.sections`) |
| 7.4 | OFFER-04 | Konfigurator → Montage/Checklisten koppeln |

## Phase 8 — UI/UX-Rahmen & dynamische Navigation

| Slice | IDs | Inhalt |
|---|---|---|
| 8.1 | UX-01/02 | Gemeinsamer Kopf + dynamische Prozessnavigation (nur relevante Module je Gewerk) |
| 8.2 | UX-03/04 | Einheitlicher Arbeits-/Ergebnisbereich (Bedarf/Daten/Varianten/Belastbarkeit/Status) |
| 8.3 | ARCH-05/06 | Modul-Aktivierung + Abhängigkeits-Engine sichtbar in der Navigation |

## Phase 9 — Sicherheit & Rechte

| Slice | IDs | Inhalt | Priorität |
|---|---|---|---|
| 9.1 | SEC-03 | **Callback-Auth-Widerspruch klären** (KEIN P0-Leck — auth-gated belegt): Route soll „PUBLIC" sein, ist aber per Konstruktor-`auth` gated → externer GC-Online-Callback vermutlich funktional gebrochen. Entscheidung: auth-pflichtig belassen **oder** `->except(['callback'])` **plus** HMAC/Secret/IP-Allowlist. Kein Preis-/Produktlogik-Umbau im selben Slice | niedrig-mittel (Funktion/Design) |
| 9.2 | SEC-01/02 | Ownership-Policies + RBAC-Gates für Konfigurator/Angebot | mittel |

## Phase 10 — Tests & Browser/E2E (querschnittlich je Slice)

| Slice | IDs | Inhalt |
|---|---|---|
| 10.x | TEST-01/02/03/04 | Je Slice: positive/negative/Grenztests, Modulübergabe-Tests, Browser/Light+Dark/Tastatur, fachliche Gegenbeweise |

---

## Reihenfolge-Empfehlung (Abhängigkeit)

0.1–0.4 (laufend/geparkt abschließen) → 1.x (Datenwahrheit) → 2.x/3.x (Geometrie konsolidieren) → 5.1 (Heizlast→WP-Naht) → 6.x (Preis/Filter) → 7.x (Angebotsübergabe) → 5.3–5.8 (neue Module) → 8.x (UI) → 9.x (Rechte/Callback-Klärung, kein P0) → 4.x (LiDAR). Jede Zeile ist ein eigener Startblock; keine Verschiebung ohne ID+Grund+Risiko+Ziel-Slice+Planner-Freigabe.
