# bauplaner-3d — erweiterter Prozess (7 Stufen + 2 Freigabe-Tore)

Verbindlicher Ablauf für jeden sichtbaren Hausplaner-Slice. Stufen sequenziell; Rollen getrennt
(Planner ≠ Generator ≠ Evaluator, Governance-Zyklus).

## Die 7 Stufen
1. **Bestandsaufnahme (Ist, gemessen).** Ledger/Arbeitskompass/ADRs lesen; im Code messen, was schon
   existiert (`grep`, `git log`, vorhandene Tests/Fixtures). Was ist die führende Wahrheit? Welche Operanden fehlen?
2. **Konzept.** Ziel + die EINE Entscheidung ausformuliert; Nahtstellen (wo im Code, wo bewusst NICHT);
   Reuse-Matrix (R1 direkt / R2 additiv / R4 Adapter / Neu nur das nachweislich Fehlende).
3. **Fach-Freigabe — TOR 1 (Yama).** Nur bei fach-/rechts-entscheidenden Slices (Tragwerk, Auslegung,
   neue Bauteile, Persistenz). Operanden-Gate: kein erfundener Wert. Ohne Tor 1 kein Bau solcher Slices.
4. **Slice-Plan.** Kantenliste (wo es bricht), Abnahmekriterien als überprüfbare Aussagen, Test-Plan
   (verriegelnder Unit-Test bevorzugt vor Screenshot), betroffene Dateien, Nicht-Ziele.
5. **Bau (Generator).** Genau der Plan, additiv; Modell-/Zod-Änderung ⇒ `schema:hausplaner` regenerieren.
   Reine Geometrie in `geometry/` (testbar), dünner three-Aufsatz. Byte-treue Ports unberührt.
6. **Gate (Generator, selbst gemessen).** `tsc:hausplaner 0 · schema:hausplaner:check 0 · test:hausplaner
   grün (inkl. neuem Test) · build:hausplaner 0`. Kein Beifang (gezielt `git add`). Meldung „umgesetzt".
7. **Abnahme (Evaluator, unabhängig).** Gegen-Beweis je Kriterium; Tests selbst ausführen; verriegelte
   Tests grün im selben Lauf; Sicht deterministisch via `?fixture=…&capture=1` (Hard-Reload gegen Bundle-Cache).
   Urteil grün/rot mit Belegen. Rot → zurück an Generator.

## TOR 2 — Merge nach main / Deploy ins LIVE-CRM
Bleibt **immer** eine bewusste Yama-Entscheidung. Alles davor (Bau + Prüfung bis grün auf `auto/`) ist
autonom; der irreversible Produktiv-Schritt (main-Merge, Push, Deploy an die 3000 Kunden) nicht. Push macht
ausschließlich Yama.

## Stehende Leitplanken
- Commits nur auf Yamas Wort · nie pushen · Tests nur gegen `ticket_testing`.
- Ein Serve-Ziel je Sicht-Runde; Baum nicht mitten in einer Browser-Runde umstellen.
- Bundle nie mergen — immer `build:hausplaner`.
