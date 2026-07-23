# Generator-Auftrag — Energie-Services verdrahten (Umsetzung, keine Analyse)

**Rolle:** Generator (Claude Code in VS Code, nativ). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Ziel:** Die 4 „gebaut aber blinden" Energie-Services **erreichbar/testbar** machen. Rechnen ist da — nur an
keine Route gehängt. Grundlage: `docs/wberechnung-uebernahme-inventur.md` (K-2/K-3), Statusseite (geliefert).

## AP1 — BivalenzService in die WP-Auslegung (der Kern-Bruch K-3), zuerst
**Ist:** `app/Http/Controllers/Energie/EnergieAuslegungController.php::wpBerechnen` (Z.196) nutzt `JazService`,
**nicht** `BivalenzService` → Bivalenzpunkt · E-Stab-Anteil · Laufstunden · Stromverbrauch (VDI 4645/4650)
werden nicht gerechnet.
**Umsetzen:**
1. `BivalenzService` injizieren (Konstruktor, neben `JazService`).
2. In der Rechen-Methode (~Z.358–427, wo das `ergebnis`-Array gebaut wird) `BivalenzService::berechne(...)`
   aufrufen (Eingaben aus demselben `$e`/`$data` + gewählter/vorgeschlagener WP wie JAZ) und die Rückgabe in
   das `ergebnis`-Array mergen: `bivalenzpunkt_c`, `bivalenz_status`, `bivalenz_hinweis`, `deckung_ne_pct`,
   `laufstunden_h`, `jaz`, E-Stab-Anteil (`space_estab`/`ww_estab`), Stromverbrauch (`waerme`/Strom-Feld).
3. `resources/views/admin/energie/wp_auslegung.blade.php`: einen Ergebnis-Block „Bivalenz & Betrieb" ergänzen,
   der diese Felder anzeigt (Bivalenzpunkt, E-Stab-Anteil %, Laufstunden, Stromverbrauch, JAZ).
**Testbar danach:** `/admin/energie/wp-auslegung` zeigt Bivalenz-Ergebnisse; `BivalenzService`-Aufrufer ≥ 1.

## AP2 — PvProjektService an die PV-Route
**Ist:** `app/Services/Energie/PvProjektService::auswerten(array $daecher, SizingInverter $wr, ?SizingBattery,
array $opt)` — 0 Aufrufer (`InverterSizingService` wird direkt genutzt, der Orchestrator nicht).
**Umsetzen:** im PV-/WR-Controller (`EnergieAuslegungController` PV-Zweig bzw. der PV-Endpoint) `PvProjektService::
auswerten(...)` als Bündelung aufrufen und sein Ergebnis in die PV-View geben (statt nur `InverterSizingService`).
**Testbar danach:** PV-Route liefert das gebündelte PV-Projekt-Ergebnis; `PvProjektService`-Aufrufer ≥ 1.

## Gate (Generator selbst)
`php artisan test tests/Feature/Energie tests/Unit/Heizlast` grün (+ ggf. neuer Feature-Test: „wp-auslegung
enthält Bivalenz-Felder"). Keine Regression der bestehenden Energie-Tests. `tsc/schema/hausplaner`-Gates sind
hier N.A. (reines PHP/Blade).

## Abnahme (Evaluator)
1. **BivalenzService-Aufrufer ≥ 1** (grep `BivalenzService` in `app/`+`routes/` ohne die Service-Datei), und
   `/admin/energie/wp-auslegung` zeigt Bivalenzpunkt/E-Stab/Laufstunden/Strom (Browser-Beleg).
2. **PvProjektService-Aufrufer ≥ 1**; PV-Route nutzt den Orchestrator.
3. Bestehende Energie-Tests bleiben grün; additiv, keine destruktive Änderung (Adapter/additiv, DAUERDIREKTIVE).
4. Nur der Energie-Strang berührt; kein Beifang; nur `auto/`-Branch, kein Push ohne Yamas Wort.

## Guardrails
Additiv (Services unverändert, nur aufgerufen — byte-treu); ticket führend; eine Wahrheit; Meldung
„umgesetzt" (Test-Exit + Browser-Beleg) → Evaluator. **Batterie-Kompat (W-C4) + Rest-Referenz-Services
(OpenMeteo/StringBuilder…) sind NICHT dieser Slice.**
