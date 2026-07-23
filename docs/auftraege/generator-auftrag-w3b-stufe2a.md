# Generator-Auftrag — W-3b Stufe 2a: rect-Fix + L/T/U-Rendering (logik, test-abnehmbar)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Basis-Branch:** `auto/hausplaner-w3b-2` aus `e9334bb` (W-3b Stufe 1, FREIGABE). **Geparkt:** erst starten
nach dem Konsolidierungs-Push **und** Yamas „go". Kein Browser, keine UI — das ist der rein logik-beweisbare
Teil (der Vorlagen-Picker ist **Stufe 2b**, eigener UI-Slice).

## Ziel & Entscheidung
Zwei Dinge, beide per Test abnehmbar, **eine Wahrheit** wahrend:
1. **`rect`-Fix** (Evaluator-Notiz aus Stufe 1): `'rect'` bekommt in `dachRoh` **definiertes** Verhalten —
   eigener `case` (Entscheidung: `'rect'` = **flache Fläche**, wie `flach`) — **kein stiller Wegfall** mehr.
2. **L/T/U-Rendering:** der bisherige `istVerschneidungsForm(...) → flaechen: []`-Guard in `dachRoh` wird
   durch **echte Verschneidungsflächen** ersetzt, gebaut aus den **reinen Modulen** `dachVerschneidung`
   (l/t) und `dachUForm` (u) — die Module werden **nur aufgerufen, nie geändert** (Byte-Treue W-1/W-2).

## Nahtstellen (WO)
`renderers/three-d/dachMesh.ts` (`dachRoh` = die eine Quelle; `dachMeshWelt` trianguliert, `dachflaechen`
filtert) und ggf. `szene.ts` (Konsum). **Nicht** anfassen: `geometry/dachVerschneidung.ts`,
`geometry/dachUForm.ts`, `geometry/gaubeGeometrie.ts`, `geometry/dachformVorlagen.ts` (nur importieren).
`roofShape.ts`/`validation.ts` unverändert (Enum steht seit Stufe 1).

## Arbeitspakete
1. `dachRoh('rect')`: expliziter `case` = flache Dachfläche (gleiche Ecken wie `flach`); Test.
2. `dachRoh` für l-shape/t-shape: Flächen aus `dachVerschneidung` (Kehl-/Gratlinien-Flächen) statt `[]`.
3. `dachRoh` für u-shape: Flächen aus `dachUForm` statt `[]`.
4. Degenerierte/ungültige L/T/U-Geometrie ⇒ Marker/leer (kein Wurf, kein Crash — Sinn der geöffneten
   `pruefeRechteckigeKontur`).
5. `dachMeshWelt`/`dachflaechen` konsumieren weiter **nur** `dachRoh` (kein zweiter Rechenweg; `const rad =`
   bleibt genau 1×). Verriegelungs-Test für rechteckige Formen bleibt grün; für L/T/U mitziehen **oder**
   im Test begründen, warum Verschneidungsflächen nicht dem Rechteck-Kontrakt folgen.
6. Neue verhaltensprüfende Tests (siehe Abnahme).

## Gate (Generator selbst, am Tip)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 · `test:hausplaner` (≥ 631 + neue) · `build:hausplaner`
(nativ/x64 grün + Bundle deckungsgleich; ARM-Geräte-VM bekannt nicht baubar).

## Abnahmekriterien (Evaluator-Messlatte — er misst genau das selbst nach)
1. **`rect` kein stiller Wegfall:** definiertes Verhalten in `dachRoh`, belegt per Test auf
   `dachMeshWelt('rect')`/`dachflaechen('rect')` — kein Durchfallen durchs `switch`.
2. **SSOT `dachRoh` intakt:** L/T/U-Flächen fließen in `dachRoh`; Guard `→[]` durch echte
   Verschneidungsflächen ersetzt; kein zweiter Rechenweg; `const rad =` bleibt 1×.
3. **Verriegelungs-Test hält/erweitert:** Ecken-auf-Fläche grün für rechteckige Formen; L/T/U mitgezogen
   oder explizit begründet.
4. **Ports nur aufgerufen:** `dachVerschneidung`/`dachUForm`/`gaubeGeometrie`/`dachformVorlagen` im git-Diff
   **nicht** enthalten (Byte-Treue).
5. **B1-Compile-Beweis intakt** (`EngineRoofShape extends RoofShape`).
6. **Kein Crash an Kanten:** degenerierte/ungültige L/T/U → Marker/leer, nie Wurf/Crash.
7. **Additiv:** Bestandsdaten unberührt; L/T/U validieren schon (Stufe 1) → keine neue 422-Fläche.
8. **Gate:** tsc 0 · schema:check 0 · test ≥ 631 (+ neue, verhaltensprüfend) · build x64-grün + Bundle deckungsgleich.
9. **Scope/kein Beifang:** nur die deklarierten Dateien; geprüfter Stand `auto/hausplaner-w3b-2` aus `e9334bb`.

## Guardrails
Additiv; eine Wahrheit (`dachRoh` SSOT, ein RoofShape); portierte `geometry/*` unverändert; kein Beifang
(`git reset -q HEAD -- .`, gezielt adden); nur `auto/`-Branch, **KEIN main-Merge/Push/Deploy** ohne Yamas Wort.
Meldung „umgesetzt" (4 Exit-Codes) → zurück an den Evaluator, Pflicht-Stopp. **Stufe 2b (Vorlagen-Picker)
ist ein eigener UI-Slice und startet separat.**
