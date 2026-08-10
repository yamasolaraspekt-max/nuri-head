# W-02 · Wand zeichnen — FUNKTION

## Was tut das Werkzeug, Schritt für Schritt?

1. Aus **Achse, Stärke und Höhe** entsteht ein **Band** — die Wand mit zwei Seiten.
2. An **Endpunkten mehrerer Wände** werden die Bänder aneinander angeschlossen.
3. Aus Band und Öffnungen entstehen **Mengen** — brutto und netto.

## Zwei Schichten, zwei Dateien

| Schicht | Datei | Ausfuhren |
|---|---|---|
| **Geometrie** | `resources/planner/hausplaner/geometry/wallGeometry.ts` | 12, 317 Zeilen |
| **Mengen** | `resources/planner/hausplaner/geometry/wandFlaeche.ts` | 6, 238 Zeilen |

### Geometrie — die Ausfuhren mit Fundstelle

| Zeile | Ausfuhr | Rolle |
|---|---|---|
| 7 | `Punkt` | x/y in mm |
| 13 | `wandLaenge()` | Länge in mm; **Gleitkomma für Vergleiche, Persistenz ganzzahlig** |
| 18 | `punktAufWand()` | Punkt im Abstand *offset* auf der Achse |
| 37 | `azimutDerNormalen()` | Richtung der **Normalen**, nicht der Wand |
| 53 | `istGanzzahlig()` | die mm-Invariante |
| 70 | `WandEingabe` | Eingabeform |
| 78 | `WandBand` | die Wand als Band |
| 153 | `wandBaender()` | **Kern** — Bänder samt Endpunkt-Anschluss |
| 267/268 | `TuerAnschlag`, `TuerOeffnung` | links/rechts, innen/außen |
| 270 | `TuerBlattGeometrie` | Türblatt |
| 291 | `tuerBlattGeometrie()` | Türblatt aus Öffnung |

### Mengen — die Ausfuhren mit Fundstelle

| Zeile | Ausfuhr | Rolle |
|---|---|---|
| 38 | `Bezugsmass` | `roh` oder `fertig` |
| 46 | `WandMengen` | brutto, netto, Abzüge |
| 77 | `MeldungArt` | **fünf** benannte Fehlerlagen |
| 84 | `Meldung` | Art **und** Klartext mit Kennungen |
| 96 | `WandFlaecheErgebnis` | **entweder** Mengen **oder** Meldungen |
| 135 | `wandMengen()` | die Rechnung |

## Die Azimut-Konvention ist Teil des Vertrags

```text
Nord = +y · Azimut = Richtung der Wand-NORMALEN · im Uhrzeigersinn von Nord · ganzzahlig 0-359
```

*Dokumentiert als Spec ▲K2 im Kopf von `wallGeometry.ts`.* **Nicht die Laufrichtung der Wand — die
Normale.** Wer das verwechselt, bekommt 90° Abweichung ohne Fehlermeldung.
