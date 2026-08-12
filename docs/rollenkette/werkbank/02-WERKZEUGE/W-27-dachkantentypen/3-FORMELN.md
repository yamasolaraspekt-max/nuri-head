# W-27 · Dachkantentypen — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.** Eine Formel, die an zwei Orten steht, wird an
> einem Ort korrigiert und am anderen vergessen.

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **F-025** 🟢 | *„Was entsteht an der Ecke — Grat, Kehle oder Ortgang?"* | **ja, sie IST das Werkzeug** |
| **F-026** | Registerzeile führt sie für W-27 | **beim Bau zu prüfen** |

**F-025 steht in der Sammlung als 🟢 mit dem Vermerk „Mathematik nachvollzogen".** *Was hier
hinzukommt, ist nicht die Mathematik, sondern **ihre Anwendung**: `analyzeTopology` ist F-025 in
ausführbarer Form.* **Der Prototyp ist damit nicht nur Beleg, sondern Vorlage.**

## Reihenfolge der Anwendung

```text
1  Umlaufsinn aus der vorzeichenbehafteten Flaeche          ->  isCCW
2  Eckwinkel aus zwei Kantenrichtungen (acos des Skalarprodukts)  ->  baseAngle
3  Einspringend? aus dem Kreuzprodukt, GEGEN den Umlaufsinn  ->  isInnerReflex
4  Vollwinkel  angleDeg = isInnerReflex ? 360 - baseAngle : baseAngle
5  Eckenart    angleDeg > 180 ? 'innen' : 'aussen'
6  Verbindungsart aus Eckenart UND den zwei angrenzenden Kantentypen
```

> **Schritt 3 ist der, an dem eine eigene Implementierung scheitern würde.** *Der Winkel allein sagt
> nicht, ob eine Ecke einspringt — **das entscheidet erst das Vorzeichen des Kreuzprodukts im
> Verhältnis zum Umlaufsinn** (`isCCW ? cross > 0 : cross < 0`, `:206`).* **Wer den Umlaufsinn nicht
> mitführt, klassifiziert bei umgekehrt gezeichnetem Polygon alle Ecken falsch herum.**

## Zwei Größen, die NICHT über eine Formel kommen

| Größe | Kommt aus | Warum keine Formel |
|---|---|---|
| `EdgeTopologyType` je Kante | `getDefaultEdgeTopologyConfigs` (`:182`) bzw. der Anwender | eine **Festlegung**, keine Rechnung |
| `joinType` | der Tabelle in `2-FUNKTION.md` | eine **Fallunterscheidung**, keine Rechnung |

*Das ist kein Mangel: **die Fachaussage „an einer einspringenden Traufecke entsteht eine Kehle" ist
eine Regel, keine Gleichung.*** **Sie gehört als Tabelle ins Blatt, nicht als F-Nummer in die
Sammlung.**

## Fehlt eine Formel?

**Nein — und ausdrücklich auch kein Straight Skeleton.**

```text
F-020 / F-021 (Skelettweg)   nach der Messung 12.08. NICHT ZUTREFFEND:
                             'skelett' -> 0 Treffer in allen acht Dachmodulen
```

*W-27 arbeitet auf dem **Kanten-/Eckenweg**, nicht auf dem Skelettweg.* **Wer hier ein Straight
Skeleton einführt, wechselt das Verfahren — das wäre ein eigener Auftrag mit eigener Begründung.**

## Genauigkeit

- **Eingang** in Modellkoordinaten, **Winkel** in Grad, **Rückgabe** dimensionslose Typen.
- **Die Toleranz steckt in der Länge, nicht im Winkel:** `Math.hypot(...) || 1` (`:201`) fängt die
  Länge **null** ab — *zwei identische Punkte hintereinander ergeben keinen Absturz, sondern einen
  Ersatzvektor.* **Der Winkel an einer solchen Stelle ist bedeutungslos; das Blatt sagt es, statt es
  zu verschweigen.**
- `dot` wird auf `[-1, 1]` geklemmt (`:203`), damit `acos` nicht `NaN` liefert — *Rundungsfehler bei
  fast gestreckten Ecken.*
- **Keine Toleranz an der 180°-Schwelle** (`:208`). *Eine exakt gestreckte Ecke (180°) gilt als
  `aussen`. **Das ist eine Entscheidung, keine Selbstverständlichkeit** — sie gehört beim Bau
  geprüft, siehe `6-PRUEFUNG.md`.*
