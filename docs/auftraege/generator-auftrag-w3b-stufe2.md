# Generator-Auftrag — W-3b Stufe 2: L/T/U-Rendering + Vorlagen-Picker (die 187) + rect-Fix

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Basis:** `auto/hausplaner-w3b` @ `e9334bb` (W-3b Stufe 1, Evaluator-FREIGABE, Gates tsc/schema/test 631 grün).
Neuer Slice-Branch `auto/hausplaner-w3b-2` aus `e9334bb`. **Erst starten nach dem Konsolidierungs-Push
und auf Yamas „go".**

## Ziel
Die portierte Dach-Engine endlich **sichtbar** machen: L/T/U-Dächer rendern und die 187 `dachformVorlagen`
im Werkzeug anbieten. Stufe 1 hat das Fundament (RoofShape eine Wahrheit, additive Enum, Guard in `dachRoh`);
Stufe 2 füllt den bislang render-neutralen Guard mit echter Geometrie und macht die Vorlagen wählbar.

## Umsetzung
1. **L/T/U-Rendering.** In `renderers/three-d/dachMesh.ts`/`szene.ts` für `VERSCHNEIDUNGS_FORMEN`
   (l-shape/t-shape/u-shape) statt `flaechen: []` die echten Flächen + Kehl-/Gratlinien aus den **reinen
   Modulen** `dachVerschneidung` / `dachUForm` bauen (Module NUR aufrufen, nicht ändern — Byte-Treue).
   Ungültige/nicht-auflösbare Kontur ⇒ überspringen + sichtbar markieren (bestehendes Kante-Muster), kein Crash.
   Die SSOT-Quelle `dachRoh` bleibt der eine Ort; der Verriegelungs-Test bleibt grün.
2. **Vorlagen-Picker (die 187).** Das Dach-Werkzeug/den Wizard so erweitern, dass er `dachformVorlagen`
   anbietet — **gruppiert/filterbar** (nicht 187 flach), mit Label/Vorschau. Auswahl schreibt Form + Parameter
   ins Modell (`ADD_ROOF`/`UPDATE_ROOF`). **Eine Wahrheit:** `dachVorlage.ts` (die 4 Grundformen) wird
   entweder abgelöst oder als Teilmenge von `dachformVorlagen` geführt — kein zweiter Vorlagen-Satz.
3. **`rect`-Auflage (Evaluator, aus Stufe 1).** `'rect'` darf nicht mehr **still** durch `dachRoh` fallen
   (aktuell kein `case` → leeres Mesh ohne Marker). Entweder `'rect'` einen expliziten `case` geben
   (vermutlich = `flach`-Verhalten) **oder** explizit durch Guard/Marker führen — „kein stiller Wegfall".

## Gate (Generator selbst, am Tip)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 · `test:hausplaner` (≥ 631 + neue Tests: L/T-Fläche liefert
Kehl-/Gratlinien; Picker-Auswahl schreibt Modell; `rect` nicht mehr stiller Wegfall) · `build:hausplaner`
(nativ/x64 — auf ARM-Geräte-VM bekannt nicht fahrbar).

## Guardrails
Additiv; portierte `geometry/*`-Module unverändert; SSOT `dachRoh` + Verriegelungs-Test + B1-Compile-Beweis
**nicht** aufweichen; eine Wahrheit (ein Vorlagen-Satz); kein Beifang (`git reset -q HEAD -- .`, gezielt adden);
nur `auto/`-Branch, **KEIN main-Merge/Push/Deploy** ohne Yamas Wort. Meldung „umgesetzt" (4 Exit-Codes) →
zurück an den Evaluator, Pflicht-Stopp.

## Abnahmekriterien (Evaluator in VS Code — Logik + teils Sichtprüfung im Browser)
1. Ein L-/T-/U-Dach rendert im 3D die korrekte Verschneidung (Kehle/Grat) aus der Engine — nicht mehr leer.
2. Der Vorlagen-Picker zeigt die `dachformVorlagen` gruppiert; Auswahl landet als Form+Parameter im Modell.
3. `'rect'` wird explizit behandelt (kein stiller Wegfall).
4. `geometry/*`-Ports unberührt; SSOT/Verriegelungs-Test/B1-Beweis intakt; Enum weiter additiv (kein 422).
5. `tsc`/`schema`/`test` grün (selbst am Tip belegt); `build` nativ grün.
**Hinweis:** das Aussehen (Picker-UI, L/T/U-Optik) prüft der Evaluator/Yama im Browser; der Generator belegt
die Logik per Tests — „fertig" erst nach der Sichtprüfung.
