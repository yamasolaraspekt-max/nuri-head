# W-22 · Gaube — FUNKTION

## Drei Gegenstände in einem Modul — getrennt gelesen

### 1 · GAUBE

| Ausfuhr | Zeile |
|---|---|
| `PultGaube` / `pultGaubeGeometrie()` | 106 / 129 |
| `GiebelGaube` / `giebelGaubeGeometrie()` | 203 / 236 |
| `fussabdruckUV()` | 367 |
| `MIN_PULT_GRAD` = 5 · `MIN_FLACH_GRAD` = 2 | 102 / 103 |

Fünf Bauarten stehen in `GaubeEingabe.type` (Z.46): `schleppgaube`, `flachgaube`, `trapezgaube`,
`giebelgaube`, `spitzgaube` — **und `chimney`.**

### 2 · KAMIN

`KaminGeometrie` (387) · `kaminGeometrie()` (389). **Ein Kamin ist hier kein Sonderfall der Gaube,
sondern ein eigener Aufbau mit eigenem Prüfkriterium** (AK5, siehe `7-GRENZEN`).

### 3 · PRÜFUNG

`Ampel` (398) · `PruefBefund` (399) · `pruefeAufbau()` (409). *Sie ist kein Nebenprodukt — sie ist
ein Drittel des Moduls.*

## Das lokale System — ohne das nichts zu lesen ist

```text
lx = Breite        (parallel zur Traufe)
ly = Welt-Hoch     (lotrecht, NICHT flaechennormal)
lz = Falllinie     (den Hang hinauf/hinunter)
```

*Wörtlich aus `resources/planner/hausplaner/geometry/gaubeGeometrie.ts:35`.* **`ly` ist Welt-Hoch und nicht die
Flächennormale** — genau daran hängt, dass ein Aufbau lotrecht steht, während die Fläche kippt.

Die Umrechnung: `weltAusLokal()` (82) über `aufbauBasis()` (76) aus dem `SurfaceFrame` (41).

## Die Kopplung, die das Pultdach bestimmt

```text
tan(b) = tan(a) - h/d          Neigung des Pultdachs aus Hauptdachneigung, Hoehe, Tiefe
d*tan(a) > h                   Machbarkeit — sonst ist der Anschluss unmoeglich
h <= d*(tan a - tan(minNeigung))   Entwaesserung — sonst wird h GEKLEMMT
```

*Aus `resources/planner/hausplaner/geometry/gaubeGeometrie.ts:24-26.* **Das Werkzeug lehnt nicht ab, es klemmt** — siehe `7-GRENZEN`.

## Eine echte Abhängigkeit

```text
gaubeGeometrie.ts:32   import { stehendeAufbauBasis } from './aufbauOrientierung';
```

**Die einzige.** *Die Orientierung „aufrecht" kommt aus einem Nachbarmodul, nicht von hier.*
