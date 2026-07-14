# Startblock — AP-4a: Geometrie-Referenztests & Belastbarkeitsnachweis

**Stand:** 2026-07-14 · **read-only Vorbereitung — KEIN Bau, kein Commit, kein Push, keine Migration, keine UI, kein 3D, kein PV, keine Dachplanung, kein playground-Code kopiert, kein Schreibpfad, keine Produktivlogik-Änderung (Ausnahme: minimaler Guard NUR nach separater STOPP-Freigabe).**
**Kapitel:** 4 (Geometrie), Fundament. **Grundlage:** `docs/ap4-geometrie-3d-gebaeudemodell-validierung.md` §11/§13, Session-Governance (Startblock → Yama-Bau-Freigabe → Evaluator → STOPP).

## Ziel
Die **vorhandene** ticket-Geometrie-Mathematik lokal **beweisen** (Referenztests mit Sollwerten) **oder** die Nicht-Belastbarkeit **sauber dokumentieren** — ohne Produktivlogik zu ändern, ohne neue Geometrieformel. `test/spec-only`.

---

## 1. Welche ticket-Geometrie-Funktionen werden getestet?
Zielobjekte (alle **read-only** aufgerufen, Signaturen verifiziert):
- `App\Services\Heizlast\GeometrieAbleitungService::polygonFlaecheM2(array $polygon): float` — Shoelace, Polygon in **mm** `[{x,y}]` → m² (`/1_000_000`).
- `GeometrieAbleitungService::ausGeometrie(RaumGeometrie $geometrie): array` — Wandfläche = Segmentlänge(mm) × Höhe(mm) /1e6; Öffnung = breite·höhe/1e6 (brutto, Öffnung separat).
- `App\Services\Heizlast\RaumHuelleService::effektiveBauteile(array $bauteile): array` — Netto-Wandfläche (Öffnungsabzug, aggregierter Faktor, `max(0,…)`-Clamp).
- `RaumHuelleService::huellbilanz(array $raeume): array` — Σ A·U je Bauteilgruppe.
- **Volumen:** `App\Services\Heizlast\HeizlastRechner::berechne()` (intern `raum()` Z. 77 `V = grundflaeche·hoehe`; belegt über H_V = 0,34·n·V, Z. 98).

## 2. Welche wberechnung-Referenztests fehlen in ticket?
In **wberechnung vorhanden, in ticket NICHT** (per `find`/`grep` bestätigt):
- `tests/Unit/GeometrieAbleitungTest.php` (Shoelace Rechteck 30 m² / Trapez 15 m² / Öffnung 1,68 m² / Äquivalenz Geometrie↔Maske über Engine).
- `tests/Feature/GrundrissEditorTest.php` (Vorschau/Öffnungsabzug/Rotationsinvarianz End-to-End).
- `tests/Unit/Heizlast/HeizlastRechnerTest.php` („Wohnzimmer" byte-genau: H_T=17,26 · H_V=10,63 · Netto 30−6=24 · Φ≈892/948 W).
→ **ticket hat 0 direkte Geometrie-/Rechnerkern-Tests** (nur `AnforderungsprofilHeizlastAdapterTest`, der **vorgegebene** Flächen 30/6/25 in den Rechner füttert — beweist NICHT die Geometrie→Fläche-Ableitung).

## 3. Welche Tests können 1:1 übernommen / nachgebaut werden?
- **Nachbaubar (Code ist Diff=0-Mirror):** `polygonFlaecheM2`, `effektiveBauteile`, `ausGeometrie` — die wberechnung-Sollwerte gelten identisch. **Adaptieren**, nicht blind kopieren (ticket-Namespaces `App\Services\Heizlast\*`, ticket-`RaumGeometrie`-Model, kein `fenster_artikel_id`-Zweig).
- **Anpassen:** `HeizlastRechnerTest` (Wohnzimmer) — Konstanten identisch, ticket-Namespace.
- **NICHT kopieren:** playground-Utils (anderer Stack/Einheiten m/cm) — nur als Referenz, kein Code.

## 4. Pflicht-Testfälle
| # | Fall | Ziel-Funktion | Erwartung |
|---|---|---|---|
| P1 | **Rechteck 5×5 = 25 m²** | `polygonFlaecheM2([{0,0},{5000,0},{5000,5000},{0,5000}])` | 25.00 (grün) |
| P2 | **Wandfläche 5×3 = 15 m²** | `ausGeometrie` Segment 5000mm × Höhe 3000mm | 15.00 (grün) |
| P3 | **Wandöffnung 30 − 6 = 24 m²** | `effektiveBauteile` (Wand 30, Fenster 6, beide `aussen`) | Netto-Wand 24.00 (grün) |
| P4 | **L-Form bekannte Fläche** | `polygonFlaecheM2` L-Polygon (z. B. 8×6 − 3×2 = 42 m²) | 42.00 (grün) |
| P5 | **Raumvolumen = Fläche × Höhe** | `HeizlastRechner::berechne` → H_V (25 m² × 2,5 m = 62,5 m³) | H_V = 0,34·n·62,5 (grün) |
| P6 | **Selbstschneidendes Polygon** (Schmetterling) darf NICHT still akzeptiert werden | `polygonFlaecheM2` | **heute: liefert still eine (falsche) Fläche → ROT/Lücke** |
| P7 | **Entartetes Polygon** (kollinear / doppelte Punkte / Nullfläche) | `polygonFlaecheM2` | **heute: 0 oder still falsch → ROT/Lücke** |
| P8 | **Falsche Einheit m statt mm** (Koords in Metern) | `polygonFlaecheM2([{0,0},{5,0},{5,5},{0,5}])` | liefert 0,000025 m² (=25 mm²) statt 25 m² → **kein Einheiten-Guard → ROT/Lücke oder klar dokumentiert** |

## 5. Was ist heute erwartbar grün?
**P1–P5** — die Kern-Mathematik ist korrekt (Shoelace, Segment×Höhe, Öffnungsabzug, V=Fläche×Höhe). Diese Tests **beweisen die Belastbarkeit der vorhandenen Formeln lokal** in ticket (schließen die „nur per Identität"-Lücke aus AP-4).

## 6. Was wird voraussichtlich rot (weil kein Topologie-/Einheiten-Gate existiert)?
**P6, P7, P8** — es gibt heute **kein** Polygon-Topologie-Gate (Selbstschnitt/Entartung) und **keinen** Einheiten-Guard. `polygonFlaecheM2` rechnet die abs-Shoelace-Summe für **jede** Punktliste ≥3 ohne Prüfung. Diese drei Fälle belegen die in AP-4 benannten Risiken (GIGO, Einheiten-Mismatch).

## 7. Wie wird ein roter Test dokumentiert, ohne Produktivlogik zu ändern?
**Kein rot-brechender CI-Test, kein „falsches Verhalten als richtig" asserten.** Stattdessen: die Lücken-Fälle (P6–P8) als **`markTestIncomplete('Topologie-/Einheiten-Gate fehlt — AP-4b')`** anlegen — sie erscheinen als *incomplete* (nicht *failed*), nennen die fehlende Absicherung, brechen die Suite nicht und ändern keine Produktivlogik. Zusätzlich in einem `@group belastbarkeit-luecke` gebündelt. Alternativ (falls gewünscht) ein separater „Belastbarkeits-Lücken"-Testfall, der das **heutige** Verhalten mit explizitem `// LÜCKE, AP-4b`-Kommentar festhält — aber `markTestIncomplete` ist ehrlicher (dokumentiert Soll ≠ Ist ohne das Ist zu legitimieren). **Empfehlung: `markTestIncomplete`.**

## 8. Wann muss AP-4a stoppen und AP-4b (Guard-Slice) verlangen?
**STOPP + AP-4b-Anforderung**, sobald ein Pflicht-Belastbarkeits-Fall nur **grün** werden kann, indem Produktivlogik (Topologie-/Einheiten-Guard) eingebaut wird — konkret P6/P7/P8. In AP-4a werden diese **nur dokumentiert** (`markTestIncomplete`), **nicht** durch Produktivänderung grün gemacht. Der Startblock-Scope erlaubt einen minimalen Guard **nur nach separater STOPP-Freigabe** → das ist genau AP-4b. AP-4a endet mit dem Belastbarkeits-Nachweis (P1–P5 grün) + dokumentierten Lücken (P6–P8 incomplete) + der AP-4b-Empfehlung.

## 9. Welche Dateien wären betroffen?
- **Nur NEUE Testdateien** (`tests/Unit/Heizlast/*`). **Keine** Produktivdatei. Die Zielservices (`GeometrieAbleitungService`, `RaumHuelleService`, `HeizlastRechner`) werden **nur read-only aufgerufen**, nicht geändert.
- Falls im Bau wider Erwarten eine Bestandsdatei berührt werden müsste → **STOPP** (das wäre AP-4b).

## 10. Welche Tests werden neu angelegt?
- `tests/Unit/Heizlast/GeometrieAbleitungTest.php` — P1 (Rechteck), P2 (Wandfläche), P4 (L-Form), Öffnung-Brutto/-Trennung; ggf. Äquivalenz Geometrie↔Maske.
- `tests/Unit/Heizlast/RaumHuelleServiceTest.php` — P3 (30−6=24), zwei Fenster additiv, **Öffnung>Wand → 0 (heute still) → `markTestIncomplete`** (Markierungsbedarf).
- `tests/Unit/Heizlast/HeizlastRechnerReferenzTest.php` — P5 (Volumen/H_V), optional Wohnzimmer-Sollwerte (byte-genau).
- `tests/Unit/Heizlast/GeometrieBelastbarkeitLueckenTest.php` — P6/P7/P8 als `markTestIncomplete` (`@group belastbarkeit-luecke`).

## 11. Rückfallpfad (falls Bestandsdateien doch berührt werden)
AP-4a berührt **keine** Bestandsdatei → Rückfall = neue Testdateien löschen. **Sollte** doch eine Bestandsdatei nötig werden: **STOPP vor der Änderung**, Archiv + MANIFEST nach `docs/rueckfall-archiv-regeln.md`, und Umwidmung als AP-4b (Guard-Slice mit eigener Freigabe).

## 12. Evaluator
Strikt **read-only**, keine git-Schreibbefehle, keine Dateiänderung. Prüft: P1–P5 beweisen echte Sollwerte (nicht gegen sich selbst); P6–P8 dokumentieren die Lücke ohne Produktivänderung; keine Bestandsdatei verändert; keine playground-Kopie; kein Schreibpfad.

---

## Stop-Kriterium / Yama-Abnahme
- Startblock endet hier. **Bau erst nach Yama-Freigabe.**
- Beim Bau **STOPP + melden**, falls (a) ein Pflicht-Belastbarkeits-Fall nur via Produktiv-Guard grün wird (→ AP-4b), (b) eine Bestandsdatei berührt werden müsste, (c) der Scope über test/spec hinausginge.
- Yama-Abnahme vor Bau-Freigabe und vor jedem Commit/Push.

*Ende Startblock AP-4a. Read-only, kein Bau, kein Commit, kein Push.*
