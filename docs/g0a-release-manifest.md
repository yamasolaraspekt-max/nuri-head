# RELEASE-MANIFEST — Welle G0a (= AP-4a): Geometrie-Referenztests

**Stand:** 2026-07-14 · **test/spec-only** · kein Produktivcode geändert, keine Migration, kein Commit, kein Push.
**Abschlussformel:** *Umgesetzt, bereit zur unabhängigen Prüfung* (A6/H4). Keine Selbst-Abnahme.

---

## 1. Umgesetzter Scope
Referenztests der vorhandenen ticket-Geometrie nach der **P1–P8-Matrix** des freigegebenen Startblocks — P1–P5 als ausführbare Belastbarkeitsnachweise, P6–P8 (+ Öffnung>Wand) als dokumentierte Lücken (`markTestIncomplete`, Verweis G0b/AP-4b). Realer Diff=0-Abgleich mit wberechnung (I2.7) und Mutations-Gegenbeweis (I2.8).

**Verwendeter Startblock:** `docs/ap4a-geometrie-referenztests-startblock.md`
**Git-Stand des Startblocks:** **untracked / nicht committet** (Arbeitsbaum-Stand 2026-07-14, in dieser Session reviewt und von Yama als G0a beauftragt via Masterprompt V3.3 Teil I). *Hinweis an Evaluator:* Der Startblock ist noch nicht in git — es existiert kein Commit-Hash; die Dokumentversion ist der aktuelle Arbeitsbaum-Stand.

## 2. Bewusst nicht umgesetzt ([KANN]/n.z.)
- Keine [KANN]-Verschiebungen (G0a hat nur [MUSS]).
- **E0-Gate-Punkte „n.z." (test/spec-only, begründet):** Migrations-Rollback (keine Migration), Fehler-Injektion E0.1 (ab G2), Performance-Messprotokoll F2 (keine Laufzeitfunktion), Autorisierungsangriff (kein Endpunkt), Light/Dark/Leer/Fehler/Konflikt (keine UI).

## 3. Geänderte Dateien
**Keine.** (Produktivcode, Routen, Migrationen, Views, Alpine: unberührt — per `git status` belegt.)

## 4. Neue Dateien (nur Tests + dieses Manifest)
- `tests/Unit/Heizlast/GeometrieAbleitungTest.php` (P1, P2, P4, I2.8)
- `tests/Unit/Heizlast/RaumHuelleServiceTest.php` (P3 + Öffnung>Wand-Lücke)
- `tests/Unit/Heizlast/HeizlastRechnerReferenzTest.php` (P5)
- `tests/Unit/Heizlast/GeometrieBelastbarkeitLueckenTest.php` (P6, P7, P8)
- `docs/g0a-release-manifest.md` (dieses Dokument)

## 5. Datenbankänderungen
**Keine.** Keine Migration erstellt oder ausgeführt.

## 6. Routen + Berechtigungen
**Keine.** Kein Endpunkt, kein Feature-Flag, keine Policy berührt.

## 7. Führende Datenquellen vor/nach der Welle
Unverändert: Geometrie-Mathematik = `App\Services\Heizlast\GeometrieAbleitungService` + `RaumHuelleService`; Volumen/H_V = `HeizlastRechner`. G0a **belegt** sie nur, ändert sie nicht.

## 8. P1–P8 — Status je Fall (Soll/Ist)
| Fall | Beschreibung | Ziel-Funktion | Soll | Ist | Status |
|---|---|---|---|---|---|
| **P1** | Rechteck 5×5 | `polygonFlaecheM2` | 25,00 m² | 25,00 | 🟢 grün |
| **P2** | Wandfläche 5×3 (Ableitung) | `ausGeometrie` | 15,00 m² | 15,00 | 🟢 grün |
| **P3** | Öffnungsabzug 30−6 | `effektiveBauteile` | 24,00 m² | 24,00 | 🟢 grün |
| P3+ | zwei Fenster additiv 30−(6+4) | `effektiveBauteile` | 20,00 m² | 20,00 | 🟢 grün |
| **P4** | L-Form 8×6−3×2 | `polygonFlaecheM2` | 42,00 m² | 42,00 | 🟢 grün |
| **P5** | Volumen 25×2,5 → H_V | `HeizlastRechner::berechne` | H_V=10,625 W/K | ≈10,63 (Δ<0,02); linear ×2 | 🟢 grün |
| **P6** | Selbstschnitt (Schmetterling) | `polygonFlaecheM2` | Ablehnung (Ziel G0b) | rechnet still | 🟡 incomplete |
| **P7** | Entartung (kollinear/Nullfläche) | `polygonFlaecheM2` | Markierung (Ziel G0b) | still 0 | 🟡 incomplete |
| **P8** | Einheit m statt mm | `polygonFlaecheM2` | Guard (Ziel G0b) | still 0,000025 m² | 🟡 incomplete |
| Zusatz | Öffnung > Wand | `effektiveBauteile` | Blocker-Markierung (Ziel G0b) | still 0 | 🟡 incomplete |

## 9. `markTestIncomplete`-Fälle mit Begründung
1. `test_p6_selbstschneidendes_polygon_wird_nicht_abgelehnt` — kein Topologie-Gate (G0b/AP-4b).
2. `test_p7_entartetes_polygon_wird_nicht_markiert` — kein Entartungs-Gate (G0b/AP-4b).
3. `test_p8_falsche_einheit_meter_faellt_nicht_auf` — kein Einheiten-Guard (G0b/AP-4b).
4. `test_oeffnung_groesser_wand_wird_still_geklemmt_luecke` — stiller 0-Clamp ohne Blocker-Markierung (G0b/AP-4b).

## 10. I2.7 — Realer Diff=0-Abgleich mit wberechnung (nicht „per Identität")
- `app/Services/Heizlast/RaumHuelleService.php` ↔ wberechnung: **DIFF=0** (kommentarbereinigt identisch).
- `app/Services/Heizlast/GeometrieAbleitungService.php` ↔ wberechnung: **eine** Abweichung — der `fenster_artikel_id → Artikel::fensterSpec->u_w`-Zweig ist in ticket entfernt (dokumentierte Adapter-Naht, ticket hat keine Fenster-Artikel-Domäne). Die **Flächen-/Geometrie-Mathematik** (`polygonFlaecheM2`, Wand-/Öffnungsfläche) ist **identisch**; die Abweichung betrifft nur die Fenster-U-Wert-Quelle, nicht die Fläche.
- Belegbefehl: `diff <(grep -vE '^\s*//|^\s*\*|^\s*/\*' <ticket>) <(… <wberechnung>)`.

## 11. I2.8 — Mutations-Gegenbeweis (ohne Produktivänderung)
`test_i2_8_referenz_ist_diskriminierend`: rohe Shoelace-Summe ohne `/2` (=50) und ohne `/1e6` (=50e6) ≠ Sollwert 25 → die Referenz P1 würde bei fehlendem `/2`- oder `/1e6`-Term rot; die korrekte Formel ist nachweislich load-bearing.

## 12. Tests — exakte Befehle & Rohresultate
```
php artisan test tests/Unit/Heizlast/GeometrieAbleitungTest.php \
  tests/Unit/Heizlast/RaumHuelleServiceTest.php \
  tests/Unit/Heizlast/HeizlastRechnerReferenzTest.php \
  tests/Unit/Heizlast/GeometrieBelastbarkeitLueckenTest.php
→ Tests: 4 incomplete, 8 passed (15 assertions)
```
Wächter (volle Suite): `php artisan test` → **567 passed, 4 incomplete, 1 failed**.
Der eine Fehler: `Tests\Feature\Invoice\InvoiceDeletionGuardTest` — `BroadcastException` (Pusher/Reverb `localhost:6001` nicht erreichbar, `app/Traits/AuditableLead.php:150`). **Umgebungsbedingter Vorbestand**, referenziert keine G0a-Symbole, unabhängig von dieser Welle.

## 13. Bestätigungen (I5)
- ✅ **Kein Topologie-Gate** implementiert (P6–P8 bleiben incomplete).
- ✅ **Kein Produktivverhalten** verändert (0 Produktivdateien im Diff).
- ✅ **Keine Migration** erstellt/ausgeführt.
- ✅ **Keine Route, UI oder Alpine-Datei** verändert.
- ✅ **G0b nicht begonnen.**
- ✅ Keine `building_*`-Tabelle, kein Feature-Flag, keine playground-Kopie.

## 14. Prüfbefehle für den unabhängigen Evaluator
1. `git status --short` → nur die 4 neuen Testdateien + dieses Manifest (untracked), keine Produktivdatei.
2. `php artisan test tests/Unit/Heizlast/…` (s. §12) → 8 passed, 4 incomplete selbst nachrechnen (25 · 15 · 24 · 42 · H_V).
3. Diff-Gegenprobe I2.7 selbst ausführen (RaumHuelle Diff=0; GeometrieAbleitung nur fenster_artikel_id).
4. Mutations-Gegenbeweis: eine Kopie von `polygonFlaecheM2` ohne `/2` gegen P1 laufen lassen → muss rot werden.
5. Bestätigen: P6–P8 sind incomplete, nicht grün.

## 15. Offene Punkte / Risiken / nächster Schritt
- **Offen:** Startblock ist noch untracked (kein Commit-Hash) — vor/mit Abnahme committen empfohlen.
- **Risiko:** keines für G0a (test-only, reversibel: 4 Dateien löschen).
- **Nächster Schritt:** unabhängige Prüfung → bei grüner Abnahme **G0b/AP-4b** (Topologie-Gate + Einheiten-/Rundungskonzept) als eigener Auftrag mit eigenem Startblock.
- **Ballbesitz:** **Evaluator / Yama** (unabhängige Prüfung + Commit-Freigabe). Der Generator startet G0b **nicht** automatisch.

---

*Umsetzung der beauftragten Welle G0a abgeschlossen. RELEASE-MANIFEST und Prüfbefehle liegen vor. Umgesetzt, bereit zur unabhängigen Prüfung.*
