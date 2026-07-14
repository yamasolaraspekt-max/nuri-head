# Startblock Welle G0b (= AP-4b) — Topologie-Gate und Toleranzvertrag — **v1.1**

**Git-Ausgangsstand (G0a-Commit):** `865e230` · 2026-07-14 (lokal, kein Push)

**Änderung v1.0 → v1.1:** Aufrufer-Verankerung auf Ingestion-Punkt-Architektur umgestellt (Entscheidung E2), Scan-Fixture definiert (E3). Fachlicher Kern unverändert.

**Heimat-App:** ticket · **Rolle des Empfängers:** Generator · **Status:** freigegeben durch Entscheidungsprotokoll E1–E4 + „Bau frei für G0b".
**Voraussetzung:** G0a unabhängig grün (erledigt) **und** G0a-Commit ausgeführt (Schritt 0).

## 1. Ziel und Entscheidung
Ungültige Polygon-Geometrie wird **abgelehnt statt still gerechnet**; alle Toleranzen/Rundungen zentral.
**Kern:** Topologie-Gate als **vorgelagerter, ticket-eigener Service** — `GeometrieAbleitungService`/`RaumHuelleService` byte-unverändert (Mirror). **Verankerung am Ingestion-Punkt** (`GrundrissController::vorschau/speichern`, einziger externer Polygon-Eingang); Mirror erhält nur gegatete Geometrie, interne Aufrufe unverändert. „Alle Aufrufer über das Gate" = **alle Schreib-/Ingestion-Pfade**, nicht Mirror-intern.
**Erweiterungspunkt:** Gate so schneiden, dass der spätere Gebäudeplaner (B2/B9) dieselbe Validierung nutzt.

## 2. Bausteine
### 2.1 Toleranzvertrag (zentrale Config)
`config/geometrie.php` + Zugriffsklasse. Startwerte: `punktgleichheit_mm=1`, `min_segment_mm=10`, `min_polygon_flaeche_mm2=10000`, `max_wandanschluss_spalt_mm=5` (nur Konstante), `winkel_toleranz_grad=0.5` (nur Konstante), `anzeige_rundung_m2=2`, `recalc_abweichung_rel=1e-9`. `[IST]`-Rundungsweg (mm-Integer → /2 → /1e6) dokumentieren; intern keine cm-Rundung.
### 2.2 Topologie-Gate-Service
`App\Services\Geometrie\TopologieGate`. Eingabe Punktliste mm. Ausgabe Ergebnisobjekt `valid` + Blocker-Liste (`rule_key`, Klartext, Indizes) + Berechnungsfassade, die bei ungültig eine fachliche Exception wirft. Keine stille Reparatur/Fallback.
Regeln: (1) ≥3 Punkte nach Dedup; (2) geschlossener Ring; (3) keine Segmente < `min_segment_mm`; (4) keine Selbstschnitte (Bow-Tie); (5) keine Selbstberührung; (6) keine Degeneriertheit (kollinear / Fläche < `min_polygon_flaeche_mm2`); (7) Öffnungsplausibilität Σ Öffnung ≤ Wandbrutto (Gleichheit ok, Überschreitung Blocker). Orientierung ist **kein** Blocker (abs()-neutral, Testfall).
### 2.3 Verankerung am Ingestion-Punkt
(1) **Schreibpfad-Inventar (Beweis):** alle Geometrie-Schreibpfade (Controller/Commands/Jobs/Seeder/Importe/API); Erwartung: nur `GrundrissController::vorschau/speichern`. Weiterer Pfad → Pflicht-Stopp. (2) Gate am Ingestion-Punkt validiert Topologie (1–6) + Öffnung (7) vor Berechnung/Persistenz. (3) **Architektur-Wächtertest** (Schreibpfad-Liste) + **HTTP-Regressionstest** (ungültiges Polygon → abgelehnt, keine Persistenz, kein Flächenwert). (4) **Bestandsdaten-Scan (E3):** primär Read-only-Export der Geometriespalten → `tests/Fixtures/geometrie-bestand-<datum>.json` (SHA256 im Manifest); Fallback synthetische Fixture (ehrlich deklariert + Deploy-Tag-Checklistenpunkt). Scan=0 → Hartschaltung; Scan>0 → **PFLICHT-STOPP**.
### 2.4 Tests
Vier G0a-incomplete-Fälle → grün über das Gate. Golden-Unverändert-Beweis P1–P5 (25/15/24/42 m², H_V 10,625). Grenzwerttests je Toleranz (unter/auf/über). Fuzz-Mindestsatz. Mutations-Gegenbeweis (Gate load-bearing).

## 3. Nicht-Scope (hart)
Kein Mirror-Eingriff, keine wberechnung-Änderung, keine Migration, keine `building_*`, keine Route/UI/Alpine, kein 2D/3D, kein Dach/PV/Azimut, keine Heizlast-Formeländerung, keine Auto-Heilung, kein G0c, keine Wandanschluss-Logik (nur Konstante).

## 4. Pflicht-Stopps (zusätzlich A7)
Scan>0; unerwarteter Schreibpfad; Gate nur mit Mirror-Eingriff verankerbar; Toleranzwert unentscheidbar; Golden-Beweis kippt.

## 5. Abnahme (Evaluator)
Mirror-Erhalt selbst gemessen; P1–P5 nachgerechnet unverändert; 4 Ex-incomplete grün aus dem Gate; Schreibpfad-Inventar gegengeprüft + Bypass-Versuch; Grenzwerttests + Config-Variation; Mutations-Gegenbeweis; Scan-Ergebnis + Fixture-Herkunft; Gate-Overhead p95; Wächter grün (Reverb-Vorbestand E4 anerkannt).

## 6. Manifest-Zusätze
Startblock+Git-Stand; Toleranztabelle final+Begründung; Aufrufer-Inventar vorher/nachher; Scan-Ergebnis+Hash; Bestätigungen (Mirror unverändert, keine Migration/Route/UI/Alpine, G0c nicht begonnen); Prüfbefehle; Ballbesitz.

## 7. Ablauf
(1) Toleranzvertrag → (2) Gate+Tests → (3) Golden-Beweis → (4) Inventar+Ingestion-Gate+Wächter → (5) Scan → (6) Hartschaltung nur bei 0 → (7) Manifest. Abschluss: Standardformel, kein Grün-Urteil, keine Auto-Fortsetzung G0c.
