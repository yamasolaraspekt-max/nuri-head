# WP-Datenblatt-Verifikation — Katalog-Stufe (ii), Datenqualitäts-Gate

**Stand:** 2026-07-05 · **Scope:** die 19 Wärmepumpen aus `wberechnung.waermepumpen`, vor dem Import in
`product_heat_pump_specs`. **Read-only** gegen offizielle Herstellerdatenblätter. Ergebnis entscheidet über
Schritt 3 (Quell-Fix in wberechnung) und Schritt 4 (Import).

## 0. Betriebsart (code-belegt — die Referenz für „richtig")

`App\Services\Heizlast\WpKennlinieService` (wberechnung) interpretiert die Leistungskurve als
**maximale Heizleistung φ_max (Volllast)**:
- `phiMax()` = „Maximale Heizleistung (kW) bei Außentemperatur ϑ" (Z.27); `ausKurve()` → `@return [phi_max_kw, cop]` (Z.72).
- **Kurve ist primär**, die Prüfpunkt-Spalten nur **Fallback** (`stuetzpunkteLeistung`, Z.40).

→ **Kontrakt = Volllast/φ_max**, kW **und** COP eines Punkts aus **derselben** Betriebsart.

## 1. Quellen (offiziell, verifiziert)

| Hersteller | Dokument | Dok-Nr. | URL / PDF |
|---|---|---|---|
| Buderus | Planungsunterlage WLW176i/WLW186i AR | **6721874368** (2024/04) | buderus-de-de.boschtt-documents.com/download/file/file/6721874368.pdf · `scratchpad/buderus_wlw186i.pdf` |
| NIBE | Datenblatt S2125 (CIL) | **M12972 CIL DE** | assetstore.nibe.se/…/120943 · `scratchpad/nibe_s2125.pdf` |
| Viessmann | Datenblatt Vitocal 250-A (aktuell) + Planungsanleitung (Kennfelder) | **db-6246346** (9/2025) + **6175107** | viessmann.de/…/db-6246346-vitocal-250-a.pdf · `scratchpad/db-6246346-vitocal-250-a.pdf`, `pa-vitocal-250-a.pdf` |

## 2. Kernbefund

**Die Einzel-Spalten sind großteils korrekt — die Leistungskurven (φ_max, die Rechengrundlage) sind überall fehlerhaft/unverifiziert.**

| Serie | n | Spalten (Einzelfelder) | Leistungskurve (φ_max) | Verifikationsdatum |
|---|---|---|---|---|
| **Buderus** WLW MB AR | 5 | 🔴 kW=Volllast ✓, **COP=Nominal ✗** (A2/A7); **A-7-COP fehlt** | 🔴 kW=Volllast ✓, **COP daneben** (weder VL noch Nom) | 2026-07-05 |
| **NIBE** S2125 | 6 | 🟢 **exakt** = CIL (Nominal), inkl. korrekt übernommenem PDF-Tippfehler | 🟡 **nicht gegen CIL prüfbar** (CIL ohne Volllast-Zeile); **-14/-16 identische LK** (Duplikat-Verdacht) | 2026-07-05 |
| **Viessmann** Vitocal 251.Axx | 8 | 🟢 **exakt** = db-6246346 (EN-14511, aktueller Stand 9/2025) | 🟠 **kW zu hoch** (über Max, ~konstant Volllast-nah); COP korrekt | 2026-07-05 |

**Wichtige Einzelbefunde:**
- Buderus: „5,0 kW / COP 4,85" (WLW-4 A7) existiert physikalisch nicht — Volllast 4,99/**3,59** ODER Nominal 2,84/4,85. SCOP nur bei **WLW-5** falsch (wb 4,57 → **4,65**).
- NIBE: Spalten 6/6 exakt (S2125-14 A-7-COP „30,4"-Tippfehler im PDF → wb korrekt 3,04). LK-Volllast nicht im CIL — bräuchte die Montageanleitung (IHB) mit Kennfeld-Diagrammen.
- Viessmann: Spalten 8/8 exakt und **aktueller** als das im Auftrag genannte Alt-Datenblatt (die „9,7 vs 10,0"-Annahme war die Vorgängerversion; aktuell EN-14511 A-7 = 10,0). LK-kW liegen z. T. massiv über der Max-Wärmeleistung (A19: LK 19,2 vs. real A-7 12,30).

## 3. Sollwerte Buderus (Volllast, direkt übernehmbar aus 6721874368)

| Modell | A-7 kW/COP | A2 kW/COP | A7 kW/COP | SCOP W35 |
|---|---|---|---|---|
| WLW-4 | 3,92 / 2,89 | 4,31 / 3,21 | 4,99 / 3,59 | 4,58 |
| WLW-5 | 5,42 / 2,51 | 6,43 / 2,91 | 6,80 / 3,16 | **4,65** |
| WLW-7 | 6,71 / 2,36 | 7,09 / 2,83 | 7,97 / 3,07 | 4,58 |
| WLW-10 | 9,57 / 2,47 | 11,66 / 2,84 | 12,67 / 3,00 | 4,77 |
| WLW-12 | 11,56 / 2,43 | 12,61 / 2,64 | 12,90 / 2,71 | 4,66 |

## 4. Die zentrale Erkenntnis + offene Weichen

Der Rechenkern will **φ_max (Volllast)**. Aber: die **Standard-Datenblätter geben primär EN-14511-Nenn**;
die **Max-Wärmeleistung** liegt oft nur als Kennfeld/Diagramm vor (Viessmann PA), bei NIBE gar nicht im CIL
(nur IHB). Und die wb-LK ist überall daneben. Praktisch relevant: **bei A-7 (dem auslegungskritischen Punkt)
ist Max ≈ Nenn** — der Unterschied Max-vs-Nenn betrifft nur A2/A7 (unkritisch für die Auslegung), aber die
**überhöhten LK-kW bei A-7 verfälschen die Auslegung real** (WP erscheint zu stark).

**Weichen (Pflicht-Stopp):**
- **W-WP1 — Kurven-Semantik:** Soll die φ_max-Leistungskurve die **Max-Wärmeleistung** (code-konform, aber Beschaffung aufwändig, NIBE nur via IHB) oder die **EN-14511-Nennleistung** (belegt = die korrekten Spalten, aber unterschätzt bei A2/A7) tragen? *Empfehlung: EN-14511-Nenn als belastbare Basis + im Report vermerken; Max nur wo als Tabelle belegt (Buderus).*
- **W-WP2 — NIBE-LK:** gegen die **IHB** (Kennfelder) nachprüfen (aufwändig) — oder LK vorerst als „nicht verifiziert" markieren und den korrekten Nominal-Fallback nutzen? *Der `-14/-16`-Duplikat sollte in jedem Fall geprüft werden.*
- **W-WP3 — Fix-Umfang (Schritt 3):** Nur **Buderus** (klar fehlerhaft, Sollwerte da)? Oder alle drei (inkl. Viessmann-LK-kW + NIBE-LK)?
