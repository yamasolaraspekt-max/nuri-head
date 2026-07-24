# Generator-Auftrag — U-Render-Platzierung: Anker auf Footprint-Zentrum (L/T/U Teil 2)

**Status: Fach-Freigabe (Tor 1) durch Yama AUSSTEHEND.** Erst nach „ja" scharf. Nur `auto/`-Branch, kein Push.
**Auslöser:** Evaluator-Votum 🔴 NACHBESSERN (deterministische U-Sicht, `fixture=u-dach` @ eef179f): U-Dach
sitzt versetzt auf dem U-Grundriss (ragt links über die Wand, eine Fläche kippt in den Hof). Geometrie war
test-grün, die **Ausrichtung** nicht.

## Ursache — belegt am Code (Regel 1, gemessen)
- `renderers/three-d/dachMesh.ts` Z.141: U-Pfad ankert an `polygonSchwerpunkt(roof.polygon)` = **Mittel der
  Eckpunkte**. Für ein U ≠ Footprint-Zentrum (die Ecken clustern → Anker wandert zur Masse).
- Rechteck-Pfad (Z.184/198) ankert an `cx,cy` aus `pruefeRechteckigeKontur` = **Rechteck-Zentrum** → deckt
  sich mit den Wänden. Wände (`platzierung.ts`) sitzen an absoluten Footprint-mm.
- U-Engine `geometry/dachUForm.ts` `uFormFlaechen` (Z.105/106): Flächen-Origins **symmetrisch um das
  Außenrechteck-Zentrum** (`x: ±L/2±ohG`, `z: ∓W/2∓oh`). Der Engine-Ursprung (0,0) gehört also ans
  **Außenrechteck-Zentrum** — und das ist für ein U-Polygon exakt seine **Bounding-Box-Mitte**.

## Auflage (surgical, eine Funktion)
1. In `verschneidungsFlaechen` (dachMesh.ts) den Anker `c` von `polygonSchwerpunkt(roof.polygon)` auf die
   **Bounding-Box-Mitte** von `roof.polygon` umstellen:
   `c = { x:(minX+maxX)/2, y:(minY+maxY)/2 }` über alle Polygon-Ecken. (Rotation um `c` bleibt wie gehabt —
   gleicher Pivot wie der Rechteck-Pfad.)
   - `polygonSchwerpunkt` bleibt bestehen, falls anderswo (Walm-Prüf-Marker szene.ts Z.568) genutzt —
     NICHT global umdefinieren; nur den U-Anker wechseln. (Prüfen: wird `polygonSchwerpunkt` sonst noch
     gebraucht? Wenn nein → durch `footprintZentrum` ersetzen; wenn ja → beide nebeneinander, klar benannt.)
   - Nichts an `uFormFlaechen`/`dachUForm`/`anbauZuEingabe`/der 4-Maß-Kette ändern (die bleiben grün).
2. **Deckungs-Test** (die Lücke, die die Sicht aufdeckte — nicht nur „Dreiecke > 0"): eine Zusicherung, dass
   die **projizierte Dach-Flächen-Bounding-Box mit dem Wand-U-Außenrechteck deckt** (min/max in x,y within
   Toleranz, z. B. ≤ Überstand), und dass der **Innenhof/Kerbe frei** bleibt (kein Dach-Dreieck-Zentroid im
   Kerbe-Rechteck). Reine Funktion auf den erzeugten Flächen — testbar ohne Browser.

## Abnahmekriterien (für den Evaluator, nach Bau)
- Gates selbst: tsc 0 · schema 0 · test (neuer Deckungs-Test grün, Bestand unverändert).
- Deterministische Sicht `fixture=u-dach&capture=1`: Traufe/Kehle sitzen auf der U-Kontur, Hof frei, kein
  Überstand über die Wand jenseits `ueberstandMm`.
- Additiv: rect/sattel/walm/pult/flach unverändert (Rechteck-Pfad nicht berührt); l/t bleibt bewusst leer.

## Nicht im Scope
L/T-Flächen (Teil 3, `buildCompoundPitchedFaces`) — separat. Keine Modell-/Schema-Änderung (reine Render-Geometrie).
