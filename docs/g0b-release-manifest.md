# RELEASE-MANIFEST — Welle G0b (= AP-4b): Topologie-Gate & Toleranzvertrag

**Stand:** 2026-07-14 · **lokal, kein Push, keine Migration, kein Mirror-Eingriff, kein Commit** (Commit-Freigabe G0b = Yama nach unabhängiger Prüfung).
**Verwendeter Startblock:** `docs/g0b-topologie-gate-startblock.md` (v1.1). **Git-Ausgangsstand:** G0a-Commit `865e230` · 2026-07-14.
**Abschlussformel:** *Umgesetzt, bereit zur unabhängigen Prüfung.* Keine Selbst-Abnahme, keine Auto-Fortsetzung mit G0c.

---

## 1. Umgesetzter Scope
Vorgelagertes Topologie-Gate (Regeln 1–7) + zentraler Toleranzvertrag; Verankerung am Ingestion-Punkt (`GrundrissController::vorschau/speichern`). Ungültige Geometrie wird abgelehnt statt still gerechnet. Der gespiegelte Kern bleibt byte-unverändert. Die vier G0a-Lücken (P6/P7/P8 + Öffnung>Wand) sind über das Gate geschlossen.

## 2. Bewusst nicht umgesetzt / n.z. (E0-Verhältnismäßigkeit)
- Keine [KANN]-Verschiebung. Command-ID/Events/Calculation-States: **n.z.** (B11-Staffelung ab G2a/G3, nicht G0b). Fehler-Injektion E0.1: **n.z.** (ab G2). Migration-Rollback: **n.z.** (keine Migration).

## 3. Geänderte Dateien
- `app/Http/Controllers/Energie/GrundrissController.php` (Gate-Verankerung; archiviert).
- `tests/Unit/Heizlast/GeometrieBelastbarkeitLueckenTest.php`, `tests/Unit/Heizlast/RaumHuelleServiceTest.php` (Ex-Lücken → grün über Gate; archiviert).

## 4. Neue Dateien
`config/geometrie.php` · `app/Support/GeometrieToleranz.php` · `app/Services/Geometrie/{TopologieGate,TopologieErgebnis,GeometrieUngueltigException}.php` · `tests/Unit/Geometrie/TopologieGateTest.php` · `tests/Feature/Geometrie/{GrundrissGateHttpTest,GeometrieSchreibpfadWaechterTest,BestandScanTest}.php` · `tests/Fixtures/geometrie-bestand-2026-07-14.json` · `docs/{g0b-topologie-gate-startblock,g0b-release-manifest,deploy-tag-checkliste-g0b-geometrie-gate}.md` · `_archiv/2026-07-14/g0b-topologie-gate/*`.

## 5. Datenbankänderungen
**Keine.** Keine Migration, keine `building_*`-Tabelle.

## 6. Routen + Berechtigungen
**Keine neue Route.** Gate wired in bestehende `energie.grundriss.vorschau/speichern` (bereits `['auth']`). Autorisierungs-Gegenprobe: `test_unauthentifiziert_wird_abgelehnt` (anonymer POST → 302/401, keine Persistenz).

## 7. Toleranztabelle (final) + Begründung
| Schlüssel | Wert | Begründung |
|---|---|---|
| `punktgleichheit_mm` | 1 | 1 mm = Zeichen-/Rundungsrauschen, unter Bau-Relevanz |
| `min_segment_mm` | 10 | < 1 cm Kante = Degenerat-Verdacht im Baumaßstab |
| `min_polygon_flaeche_mm2` | 10000 (0,01 m²) | Räume ≥ 0,01 m²; fängt Einheiten-/Nullflächenfehler |
| `max_wandanschluss_spalt_mm` | 5 | nur Konstante (kein Anschluss-Code in G0b) |
| `winkel_toleranz_grad` | 0,5 | nur Konstante (keine Nutzung in G0b) |
| `anzeige_rundung_m2` | 2 | wie [IST] der Engine; Rundung nur an Ausgabegrenze |
| `recalc_abweichung_rel` | 1e-9 | numerische Vergleichs-Toleranz (Regel 7 Gleichheit) |

Startwerte als Planner-Vorschlag; Änderung = Config, kein Code. Grenzwerttests (9,9/10,0/10,1 mm; 99/100/101 mm) belegt.

## 8. Aufrufer-Inventar (vorher/nachher)
- **Schreibpfad-Beweis:** einziger Geometrie-Persistierer = `GrundrissController` (`RaumGeometrie::updateOrCreate`) — Architektur-Wächtertest verankert das. Kein weiterer Schreibpfad (Controllers/Commands/Jobs/Seeder gescannt).
- **`polygonFlaecheM2`-Aufrufer:** vorher `GeometrieAbleitungService`(intern), `GrundrissController`; nachher zusätzlich `TopologieGate` (Fassade). Allowlist per Wächtertest.
- **`effektiveBauteile`-Aufrufer:** unverändert `HeizlastRechner`, `RaumHuelleService`(intern) — alle Mirror-intern, erhalten nur gegatete Geometrie.

## 9. Golden-Master (P1–P5 unverändert)
Mirror byte-unverändert (`git status app/Services/Heizlast/*` leer). G0a-Referenztests weiter grün (25/15/24/42 m² · H_V 10,625). Fassaden-Gleichheit belegt: `TopologieGate::flaecheOderException(quadrat) === polygonFlaecheM2(quadrat)`.

## 10. Bestandsdaten-Scan (E3)
- **Weiche:** Dev-DB `ticket` = **0 `raum_geometrien`-Zeilen** (Read-only-Scan) → **Fallback** synthetische Fixture.
- **Fixture:** `tests/Fixtures/geometrie-bestand-2026-07-14.json` (Rechteck/L/T/U/Walm), **Herkunft: synthetisch** (ehrlich deklariert, Aussagekraft eingeschränkt). SHA256 `b829cf08d3264907a725acd3310e9ac5e8f7eb197335e6172207bae401b251d6`.
- **Scan-Ergebnis:** 0 Treffer (5/5 Formen gültig; `BestandScanTest`). → **Hartschaltung** (Gate am Ingestion hart; kein Altdaten-Rollback nötig, da 0 Bestandszeilen).
- **Deploy-Tag-Checklistenpunkt** angelegt: `docs/deploy-tag-checkliste-g0b-geometrie-gate.md` (echter Hetzner-Scan vor Produktiv-Hartschaltung).

## 11. Performance-Messprotokoll (F2)
- Umgebung: lokal (Herd), PHP 8.4, `php artisan tinker`; Kaltlauf; 12 Wiederholungen.
- Gate `pruefePolygon`: **n=20 Punkte (typisches Raum-Polygon): Median 0,088 ms · p95 0,157 ms** (≪ 1-ms-Richtwert). **n=300 (pathologischer Einzelring): Median 21,2 ms · p95 21,5 ms** (O(n²)-Selbstschnitt; weit unter B7-Recalc-Budget 500 ms; reale Räume sind klein).
- Kein spürbarer Regress für realistische Nutzung.

## 12. Tests — Befehle & Rohresultate
```
php artisan test tests/Unit/Geometrie tests/Feature/Geometrie tests/Unit/Heizlast
→ Gate 14, HTTP 4, Wächter 2, Scan 1, Heizlast/Geometrie-Referenz … alle grün, 0 incomplete
php artisan test            → 592 passed, 1 failed, 0 incomplete
```
(592 nach Ergänzung des Autorisierungs-Angriff-Tests `test_unauthentifiziert_wird_abgelehnt`; vom Evaluator bestätigt.)
Der eine Fehler: `Tests\Feature\Invoice\InvoiceDeletionGuardTest` (BroadcastException, Reverb `localhost:6001`) — **per E4 anerkannter, G0b-fremder Vorbestand**, einziger Rotfall.

## 13. Mutations-Gegenbeweis
`TopologieGateTest::test_mutation_gate_ist_load_bearing`: am Gate vorbei liefert der Mirror für ein selbstschneidendes Polygon still einen Wert; durch das Gate wird derselbe Fall abgelehnt → das Gate ist load-bearing.

## 14. Rollback/Restore
Rein additiv + 3 archivierte Bestandsdateien (`_archiv/2026-07-14/g0b-topologie-gate/` + MANIFEST). Rückfall = Originale zurück + neue Dateien löschen; kein Schema/Daten berührt.

## 15. Bestätigungen
- ✅ Mirror **unverändert** (`GeometrieAbleitungService`/`RaumHuelleService`/`HeizlastRechner` byte-gleich; git leer).
- ✅ **Keine Migration**, keine `building_*`-Tabelle.
- ✅ **Keine neue Route, keine UI, keine Alpine-Datei**.
- ✅ **G0c nicht begonnen** (Geometrie-Umzug ans Objekt bleibt eigener Auftrag).
- ✅ Kein `git add -A`, kein Push, HEAD unverändert (`865e230`).

## 16. Prüfbefehle für den Evaluator
1. `git status --short` → nur der dokumentierte Dateiplan; `git status app/Services/Heizlast` leer (Mirror).
2. `php artisan test tests/Unit/Geometrie tests/Feature/Geometrie tests/Unit/Heizlast` → alle grün, 0 incomplete.
3. Diff-Gegenprobe Mirror ↔ wberechnung (RaumHuelle Diff=0; GeometrieAbleitung nur fenster_artikel_id).
4. Bypass-Versuch: neuer `RaumGeometrie::updateOrCreate`-Aufruf ohne Gate → `GeometrieSchreibpfadWaechterTest` muss rot werden.
5. Config-Variation: `min_segment_mm` in `config/geometrie.php` ändern → `test_regel3_grenzwerte_min_segment` kippt nachvollziehbar.
6. Golden: P1–P5 selbst nachrechnen (unverändert).

## 17. Offene Punkte / Risiken / nächster Schritt / Ballbesitz
- **Offen:** synthetische Bestand-Fixture (echter Scan = Deploy-Tag-Checkliste); O(n²)-Selbstschnitt (bekannt, für reale Raumgrößen unkritisch — spätere Bounding-Box-Vorfilterung möglich).
- **Risiko:** niedrig (test/config/1 Controller, reversibel).
- **Nächster Schritt:** unabhängige Prüfung gegen Startblock §5 → bei grün **Yama: Commit-Freigabe G0b**. Danach **G0c** (Geometrie-Umzug ans versionierte Objekt, Migration → eigener Pflicht-Stopp).
- **Ballbesitz:** Evaluator (Prüfung) → danach **Yama** (Commit-Freigabe G0b).

---

*Umsetzung der beauftragten Welle G0b abgeschlossen. RELEASE-MANIFEST und Prüfbefehle liegen vor. Umgesetzt, bereit zur unabhängigen Prüfung.*
