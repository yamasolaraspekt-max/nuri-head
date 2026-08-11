# W-22 · Gaube — CODE

**Angebunden aus `resources/planner/hausplaner/geometry/gaubeGeometrie.ts`** — 498 Zeilen, 26 Ausfuhren, selbst nachgezählt.

**Geometrie-Grundlage:** `Vec3` (34) · `LokalPunkt` (36) · `Dreieck` (37) · `Linie` (38) ·
`SurfaceFrame` (41) · `GaubeEingabe` (45) · `surfacePointRein()` (66) · `AufbauBasisWelt` (74) ·
`aufbauBasis()` (76) · `weltAusLokal()` (82)

**Hauptdach:** `Hauptdach` (87) · `hauptdachAusFrame()` (88) · `neigungAusFrame()` (92) ·
`signierterAbstand()` (97)

**Grenzwerte:** `MIN_PULT_GRAD` (102) · `MIN_FLACH_GRAD` (103)

**Gauben:** `PultGaube` (106) · `pultGaubeGeometrie()` (129) · `GiebelGaube` (203) ·
`giebelGaubeGeometrie()` (236) · `fussabdruckUV()` (367)

**Kamin:** `KaminGeometrie` (387) · `kaminGeometrie()` (389)

**Prüfung:** `Ampel` (398) · `PruefBefund` (399) · `pruefeAufbau()` (409)

**Abhängigkeit:** `aufbauOrientierung.ts` (Z.32) — die einzige.

## Das Thema ist größer als dieses Blatt: fünf Module, 975 Zeilen

| Modul | Z | was es ist |
|---|---|---|
| `resources/planner/hausplaner/geometry/gaubeGeometrie.ts` | **498** | dieses Blatt |
| `resources/planner/hausplaner/geometry/aufbauPlatzierung.ts` | 190 | flächenabhängige Platzierung |
| `resources/planner/hausplaner/geometry/auswechslung.ts` | **174** | Auswechslungen / Wechselhölzer |
| `resources/planner/hausplaner/geometry/aufbauOrientierung.ts` | 61 | Orientierung aufrecht |
| `resources/planner/hausplaner/geometry/aufbautenStatus.ts` | 52 | Statuslogik für Dach-Aufbauten |
| | **975** | **selbst nachgezählt, deckt sich mit dem Auftrag** |

**Die Werkbank führt davon nur „Gaube".**

### `auswechslung.ts` ist in keinem Blatt zuhause

Sie wird in **W-21 und W-22** als Nachbar genannt und ist **in beiden nur Nachbar**.
*Ohne diesen Satz verschwinden 174 Zeilen zwischen zwei Blättern.*

### Ein Modulname, der nicht dazugehört

`resources/planner/hausplaner/geometry/wandaufbau.ts` (72 Z) heißt „Aufbau", ist aber **Wandaufbau / U-Wert** — bei **W-02**
ausdrücklich Ausschluss. *Der Name trägt hier zum zweiten Mal in die Irre.*
