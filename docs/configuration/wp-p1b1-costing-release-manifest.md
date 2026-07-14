# RELEASE-MANIFEST — WP Stufe 3b / P1b-1: WpCostingService (verhaltensgleiche Kostenextraktion)

**Stand:** 2026-07-14 · **lokal, kein Commit, kein Push** (Commit-Freigabe = Yama/Planner nach unabhängigem Evaluator).
**Ausgangs-HEAD:** `f76b7dd` (P1a committet; **lokal, noch nicht gepusht** — 1 Commit ahead of `backup-private`).
**Umgebung:** PHP 8.4.21 · Laravel 11.48.0 · Test-DB `ticket_testing` (RefreshDatabase/Unit).
**Aktive Anforderungs-IDs:** ARCH-07 (dünne Controller, Fachlogik in Services), A2 (Duplikat-Kette auflösen). **A1 bleibt unverändert.**
**Abschlussformel:** *Kostenservice verhaltensgleich extrahiert, gegen P1a-Golden-Master geprüft, keine Fachlogik verändert.*

---

## 1. Bestandsaufnahme (Ist vor der Änderung)
Die WP-Kostenlogik war in **beiden** Controllern identisch dupliziert (A2):
| Kostenwert | heutiger Codepfad (vor P1b-1) | Eingabe | Rundung | Ausgabe | P1a-Test |
|---|---|---|---|---|---|
| Investition netto | `wpErgebnis:412-413` / `baueWp:297-298`: `KostenService::berechne([],null,null,investition)['summe_netto']` | `investition` (Formularwert) | KostenService (2 NK) | `investition_netto` | `test_normalfall…`, `test_fehlender_preis…`, `test_foerderdeckel…` |
| Stromkosten/Jahr | `wpErgebnis:406` / `baueWp:294`: `stromKwh × strompreis` | `stromKwh` (JazService), `strompreis` | `round()` im Output | `stromkosten_jahr` | `test_normalfall…`, `test_warmwasser_ohne_wp…` |
`KostenService` wurde in beiden Controllern **ausschließlich** in diesem WP-Kostenblock genutzt (grep belegt).

## 2. Neuer Servicevertrag
- **`App\Services\Auslegung\WpCostingService::berechne(float $investition, float $stromKwh, float $strompreis): WpKostenErgebnis`**
  - kapselt: `investitionNetto = KostenService::berechne([],null,null,$investition)['summe_netto']` + `stromkostenJahr = $stromKwh × $strompreis` (roh).
  - Konstruktor: `KostenService` (Default-Instanz) — Reuse, unverändert.
- **`App\Services\Auslegung\WpKostenErgebnis`** (readonly DTO): `investitionNetto`, `stromkostenJahr` (roh; Ausgabe-Rundung bleibt bewusst im Controller-View-Model wie im Ist).
- **Eingabevertrag:** typisierte Skalare (kein Request/Controller/View/Session im Service). **Ausgabevertrag:** dieselben Feldwerte wie P1a; keine neue Kostenart, keine Umbenennung.
- **Bewusst NICHT im Service:** JAZ/Stromverbrauch (bleibt JazService beim Aufrufer), Warmwasser, **Förderung**, **Dokument**, Produktwahl, Ranking, Bivalenz. Der Service kennt **kein Gerät** → A1 (Geräte-Unabhängigkeit) strukturell erhalten.

## 3. Geänderte/neue Dateien (Pflicht-Dateiplan eingehalten)
- **Neu:** `app/Services/Auslegung/WpCostingService.php`, `app/Services/Auslegung/WpKostenErgebnis.php`, `tests/Unit/Auslegung/WpCostingServiceTest.php`, dieses Manifest.
- **Geändert (nur Kostenpfad):** `app/Http/Controllers/Energie/EnergieAuslegungController.php` (use + Konstruktor `KostenService`→`WpCostingService`; `wpErgebnis`-Kostenblock + Ausgabezeile), `app/Http/Controllers/Energie/EnergiekonzeptController.php` (analog in `baueWp`).
- **KEINE** Migration, Route, View, Config-Datei. Kein Aufräumen/Umbenennen außerhalb des Kostenpfads. Ein rein beschreibender Abschnitts-Kommentar (`EnergieAuslegungController:181`) nennt „KostenService" weiterhin korrekt (KostenService bleibt der gekapselte Kern).

## 4. Bewusst unveränderte Altprobleme
- **A1 — Geräte-Unabhängigkeit:** Kosten hängen weiterhin NICHT vom Gerät ab (Service ohne Geräte-Parameter). Test `test_geraetewahl_aendert_nur_anzeige_nicht_die_zahlen` + `WpCostingServiceTest::test_geraete_unabhaengig…` belegen das erhaltene Ist. **Korrektur ist P1c, nicht P1b-1.**
- **A2 — Duplikat:** durch die Extraktion aufgelöst (beide Controller rufen denselben Service); Fachwerte unverändert.

## 5. Paritätsmatrix (P1a-Golden-Master = Sollstand)
`php artisan test tests/Feature/Energie/` → **21 passed, unverändert** (kein einziger Golden-Wert verschoben). Abgedeckt: Standard · niedriger/hoher Bedarf · WW mit/ohne WP · Personen · Verbrauchsmethode · fehlender Preis/Preis 0 · Förderung an/aus/Boni/**Deckel** · Rundungsgrenzen · **verschiedene `wp_index`** · **Energiekonzept ↔ Einzel-WP-Parität** · Dokument = Rechenwahrheit · Fehlerfälle. Zusätzlich 6 fokussierte `WpCostingServiceTest`.

## 6. Testbefehle & Rohresultate
```
php artisan test tests/Unit/Auslegung/WpCostingServiceTest.php        → 6 passed (14 assertions)
php artisan test tests/Feature/Energie/                                → 21 passed (126)  [P1a-Golden unverändert]
php artisan test  (volle Suite)                                        → 635 passed, 1 failed (2253)
```
Baseline `f76b7dd` = 629 passed/1 failed → **Delta +6** (genau die neuen Service-Tests). Einziger Rotfall unverändert **Reverb-E4** (`InvoiceDeletionGuardTest`, `localhost:6001`) — nicht P1b-1-verursacht.

## 7. Gegenbeweise (temporäre Mutationen, per md5 sauber restauriert)
| # | Mutation | Erwartung | Ergebnis |
|---|---|---|---|
| G1 | Stromkostenformel `× 1.1` | ≥1 Golden + Service rot | **8 rot** (`normalfall`, `standardausgabe`, …) |
| G2 | `summe_netto + 1` (Investitionsformel) | Kosten-/Förder-Test rot | **6 rot** (`normalfall` + Förder-Kaskade) |
| G3 | Ausgabe `round` → `floor` | Rundungs-Test rot | **1 rot** (`normalfall` stromkosten) |
| G4 | verschiedene `wp_index` | weiterhin P1a-Ist (Geräte-unabhängig) | grün (`test_geraetewahl…`) |
| G5 | Energiekonzept vs Einzel-WP | gleiche WP-Kosten | grün (`test_wp_teil_gleiche_kernwerte…`) |
Restore-Verifikation: `md5` von Service **und** Controller nach den Mutationen **identisch** zum Vor-Stand → keine Mutation im finalen Diff.

## 8. Bestätigungen
- ✅ **A1 unverändert** (Kosten geräteunabhängig wie im Ist).
- ✅ **Förderung NICHT extrahiert** (`FoerderungService::berechne` bleibt im Controller, unverändert), **Dokument NICHT extrahiert**.
- ✅ Kein Ranking, keine Orchestrator-Verdrahtung, keine Bivalenz/JAZ-Neuberechnung im Service.
- ✅ **Keine Migration, keine View, keine Route, keine Config**, keine Preisquellen-/`retail_price`-/OMD/IDS-Änderung, keine neuen Request-Felder, kein Angebots-Write, kein M-1/M-2.
- ✅ Controller **fachlich dünner** (keine duplizierte WP-Kostenformel mehr); bestehende Kostenfelder/Fallbacks/Dokumentwerte/Förderlogik erhalten.
- ✅ `CLAUDE.md` unberührt. Kein `git add -A`, kein Commit, kein Push.

## 9. Offene Punkte
- **P1b-2** Förderungs-Extraktion (`WpFundingAssessmentService`) · **P1b-3** Dokument (`WpDocumentService`) · **P1b-4** Gesamtbereinigung · **P1c** A1-Korrektur (geräteabhängige Kosten/JAZ mit Kennlinien). Alle **gesperrt** bis eigener Startblock.

## 10. Ballbesitz
Generator hat umgesetzt → **unabhängiger read-only Evaluator** → danach **Yama** (separate Commit-Freigabe). **Kein Commit, kein Push, keine Fortsetzung mit P1b-2.**
