# Generator-Auftrag — W-3b Stufe 2a Teil 3: L/T-Verschneidungsflächen (Port-Abschluss)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`.
**Ausgestellt von:** Planner, 2026-07-23. **Basis:** `4b8eb04` (L/T/U-Slice Teil 2).
**GEPARKT — harte Vorbedingung:** erst starten, **nachdem der Evaluator `4b8eb04` GRÜN gibt** (inkl.
U-Sicht im Browser). Kein Aufbau auf ungeprüftem Tip (Lehre aus der Stufe-1-auf-ungefixt-Base-Abweichung).

## Warum (der jetzt exakt benannte Grund)
U rendert real, weil `dachUForm.ts` die **Flächen** aus `buildCompoundPitchedFaces` byte-treu gespiegelt
hat (`uFormFlaechen(e): UFlaeche[]`). L/T sind bewusst leer, weil der ticket-Port von
`geometry/dachVerschneidung.ts` bisher **nur die Kehl-/Gratlinien** enthält — die **Flächen-Ableitung**
aus `buildCompoundPitchedFaces` fehlt noch. Dieser Slice holt genau die nach.

## Quelle (byte-treuer Port)
- **Engine-Original:** `../Playground/src/pages/energie/DachplanerProPage.tsx` → Methode
  `buildCompoundPitchedFaces` (~Z.1155 ff.).
- **Reine Spiegelung (bereits im Playground extrahiert):** `../Playground/src/utils/dachVerschneidung.ts` —
  **dieselbe** Quelldatei, aus der die ticket-Linien schon stammen. Der Flächen-Teil dieser Datei wird nun
  **byte-treu nachgezogen** (nicht neu erfunden, nicht umformuliert).
- **Vorbild in-repo:** `geometry/dachUForm.ts` (`uFormKonstanten`/`mainSDoppelNotchPoly`/`uBauGueltig`/
  `uFormFlaechen`/`uFormKehlsparren`) zeigt exakt die Ziel-Form für L/T.

## Ziel & Entscheidung
1. **Port vervollständigen:** die L/T-**Flächen**-Exporte aus Playgrounds `dachVerschneidung.ts` byte-treu in
   den ticket-Port übernehmen — analog zu `UFlaeche[]` ein `VerschneidungFlaeche[]` liefern
   (`verschneidungsFlaechen(e: VerschneidungEingabe): VerschneidungFlaeche[]`). Die **schon portierten
   Linien-Funktionen bleiben byte-identisch** (unverändert); es wird nur der fehlende Flächen-Teil ergänzt.
2. **In `dachRoh` verdrahten:** im l-shape/t-shape-Zweig das dokumentierte `[]` durch die echten
   `verschneidungsFlaechen(anbauZuEingabe(node))` ersetzen — genau wie U es schon tut. SSOT bleibt `dachRoh`;
   `anbauZuEingabe` bleibt die eine Mapping-Stelle; `const rad`/`azRad` bleibt 1×.

## Nahtstellen (WO)
- `geometry/dachVerschneidung.ts`: **additive** Ergänzung der Flächen-Exporte, byte-treu zur Playground-Quelle
  (Linien-Teil unangetastet). **Dieser Slice DARF diese Datei anfassen** — sein Zweck IST der Port-Abschluss
  (anders als Teil 2, wo Ports nur aufgerufen wurden).
- `renderers/three-d/dachMesh.ts` (`dachRoh`): l/t-Zweig auf echte Flächen; Kommentar „L/T geparkt" entfernen.
- **Nicht anfassen:** `dachUForm.ts`, `gaubeGeometrie.ts`, `dachformVorlagen.ts`, `roofShape.ts`,
  `validation.ts`/Schema (kein neuer Modell-/Validierungs-Eingriff — `anbau` steht seit Teil 2).

## Kantenliste
- L/T ohne `lengthB/widthB` (nur U-Maße gesetzt) → leer + Marker, kein Wurf (Test aus Teil 2 hält).
- Degeneriert (Anbau ≥ Hauptbau / 0 / negativ) → sauber leer, kein NaN in die Triangulierung.
- T- vs. L-Form: beide Zweige abgedeckt (buildCompoundPitchedFaces unterscheidet sie über die Eingabe).

## Gate (Generator selbst)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 (kein Schema-Eingriff → bleibt grün) ·
`test:hausplaner` (≥ 638 + neue verhaltensprüfende L/T-Flächen-Tests) · `build:hausplaner` (nativ/x64).

## Abnahmekriterien (Evaluator misst selbst)
1. **L/T rendern real:** `dachMeshWelt('l-shape'/'t-shape' + anbau)` → nicht-leere Flächen (Dreiecke belegt);
   das dokumentierte `[]` ist ersetzt, kein stiller Wegfall mehr.
2. **Byte-Treue des Ports:** die neuen Flächen-Funktionen sind **zeichengleich** zur Playground-Quelle
   (`../Playground/src/utils/dachVerschneidung.ts`) — Gegenbeweis per Diff der übernommenen Blöcke; die
   bereits portierten Linien-Funktionen **unverändert**.
3. **SSOT intakt:** Flächen in `dachRoh`; `anbauZuEingabe` 1×; `const rad`/`azRad` 1×; `dachMeshWelt`/
   `dachflaechen` lesen nur `dachRoh`.
4. **Additiv/kein 422:** kein Schema-/Modell-Eingriff; Bestandsdaten unberührt; `schema:check` grün.
5. **Kein Crash an Kanten** (fehlende/degenerierte L/T-Maße → leer, Test belegt).
6. **U unberührt** (uFormFlaechen-Pfad unverändert; U-Tests bleiben grün).
7. **Gate:** tsc 0 · schema 0 · test ≥ 638 (+ neue) · build x64-grün.
8. **U-Optik-Watch (aus Teil-2-Hinweis):** falls der Evaluator die U-Platzierung (Schwerpunkt-Näherung) im
   Browser als versetzt sieht → **separater** kleiner Folge-Fix, NICHT dieser Slice.

## Guardrails
Byte-treuer Port (kein Umschreiben der Engine-Ableitung); additiv; eine Wahrheit (`dachRoh` SSOT);
kein Beifang (`git reset -q HEAD -- .`, gezielt adden); nur `auto/`-Branch, **KEIN main-Merge/Push/Deploy**
ohne Yamas Wort. Meldung „umgesetzt" (4 Exit-Codes) → zurück an den Evaluator, Pflicht-Stopp.
