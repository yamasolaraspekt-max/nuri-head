# Nachbesserungs-Auftrag — W-3a-fix (Evaluator-Votum: NACHBESSERN)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Branch:** weiter auf
`auto/hausplaner-w3a` (Fix-Commit oben drauf, kein neuer Wellen-Branch). **Ausgestellt von:** Planner, 2026-07-23.
**Kontext:** Evaluator hat W-3a (`b7f83f0`) geprüft — additiv/Schema/Command/Tests **korrekt**, aber Votum
**NACHBESSERN** wegen **M1 (blockierend)** + **M2**. Ball beim Evaluator; erst nach diesem Fix + Re-Abnahme.

## M1 — BLOCKIEREND: Dachbasis-Mapping doppelt berechnet (`renderers/three-d/dachMesh.ts`)
**Problem:** `dachflaechen()` (neu) dupliziert **Zeile für Zeile** die (u,v)→Welt-Basis- und Eckwert-
Herleitung (`rad/ux/uy/vx/vy/a/b/zt/tan/w()` + Eckwerte je `roofType`) aus `dachMeshWelt` (gleiche Datei).
Aktuell identisch, aber **Kopie statt geteilter Ort** ⇒ latente zweite Wahrheit: ändert jemand später
`dachMeshWelt` (Überstand, Azimut-Konvention …) und vergisst `dachflaechen`, driften die Aufbau-Trägerflächen
**still** von der gerenderten Dachhaut — kein Test schlägt an. Verletzt „abgeleiteter Wert an genau EINER Stelle".

**Soll:**
1. Die Basis- + Eckwert-Herleitung in **EINE geteilte Funktion** ziehen (z. B.
   `dachBasisUndFlaechen(roof) → { basis, flaechenEckenJeRoofType }`), die **beide** nutzen.
   `dachMeshWelt` **trianguliert die Flächen aus `dachflaechen()`** (bzw. aus der geteilten Quelle) —
   kein zweiter Rechenweg. `walm` behält seinen Sonderpfad (`[]`/Marker).
2. **Verriegelungs-Test:** belegt, dass die Ecken aus `dachflaechen()` **exakt auf der `dachMeshWelt`-Fläche
   liegen** (für flach/pult/sattel; walm = Marker). So schlägt der Test an, falls die zwei je wieder divergieren.

## M2 — nicht-blockierend (mitnehmen): Header-Kommentar überzogen (`dachAufbautenMesh.ts`)
„KEINE eigene Gauben-/Loch-Mathe — SSOT bleibt geometry/*" stimmt für **Gauben/Kamin** (die kommen aus
`geometry/gaubeGeometrie`), aber **Dachfenster/Lüfter/Sat/Lichtkuppel** nutzen lokale `boxTris`/`rechteckLoch`.
**Entweder** den Kommentar präzisieren („einfache Aufbauten = flacher lokaler Aufsatz; Gauben/Kamin über
`geometry/gaubeGeometrie`") **oder** die einfachen Aufbauten über `aufbauPlatzierung` führen.
**Empfehlung:** Kommentar präzisieren (kleiner Eingriff), außer die Engine-Route ist trivial.

## Gate (Generator selbst, vor „umgesetzt")
`npm run tsc:hausplaner` (0) · `npm run schema:hausplaner:check` (0) · `npm run test:hausplaner`
(≥ 623, **plus** der neue Verriegelungs-Test) · `npm run build:hausplaner`.
**Wichtig:** die Testzahl **selbst am w3a-Tip** belegen (der Evaluator will die Zahl nachmessen, nicht glauben).

## Guardrails
Additiv; `geometry/gaubeGeometrie` & die übrigen portierten `geometry/*`-Module **NICHT ändern** (nur die
Konsolidierung in `renderers/three-d/dachMesh.ts`); `roofType`-Enum + `pruefeRechteckigeKontur` **unverändert**
(bleibt W-3b); kein Beifang (`git reset -q HEAD -- .`, dann gezielt adden). Nur `auto/`-Branch,
**KEIN main-Merge/Push/Deploy** ohne Yamas Wort. Fix-Commit-Message:
`W-3a-fix: geteilte Dachbasis (SSOT dachflaechen↔dachMeshWelt) + Verriegelungs-Test; Aufbau-Header praezisiert`.
Meldung „umgesetzt" mit den vier Exit-Codes → **zurück an den Evaluator** (Pflicht-Stopp, kein neuer Slice).

## Abnahmekriterien (Evaluator in VS Code)
1. **Geteilte Quelle statt Kopie:** keine zweite `rad/ux/uy/vx/vy`-Herleitung mehr — `dachflaechen` und
   `dachMeshWelt` speisen sich aus **einer** Funktion (per grep/Lesart belegbar).
2. **Verriegelungs-Test** vorhanden + grün: `dachflaechen()`-Ecken liegen auf der `dachMeshWelt`-Fläche.
3. Volle Suite **selbst auf w3a** gemessen, grün (≥ 623 + 1).
4. M2: Header und tatsächliche Mathe konsistent.
5. Weiterhin additiv, Schema/Enum unverändert, kein Beifang.
