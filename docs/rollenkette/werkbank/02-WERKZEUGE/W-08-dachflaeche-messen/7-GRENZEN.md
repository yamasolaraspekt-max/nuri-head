# W-08 · Dachfläche messen — GRENZEN

## Der gefährliche Fall: `0` bedeutet dreierlei

```text
polygonFlaeche.ts:32   weniger als 3 Punkte      -> 0
polygonFlaeche.ts:42   ungueltige Zahl im Polygon -> 0
polygonFlaeche.ts:47   Ergebnis nicht endlich     -> 0
                       und: eine ENTARTETE Flaeche (alle Punkte auf einer Geraden) -> 0
```

**`0` ist zugleich ein gültiges Ergebnis und das Fehlersignal.** Der Aufrufer bekommt eine Zahl und
kann nicht unterscheiden, ob die Fläche wirklich null ist oder ob seine Eingabe kaputt war.

> **Das ist die A-10-Klasse:** *nicht das leere Ergebnis ist das Problem, sondern das gefüllte, das
> seine Herkunft verschweigt.* **Und es ist eine bewusste Entscheidung** — der Kopf sagt zu:
> *„Niemals `NaN` oder `Infinity`"* (Z.29). Wer das zusagt, muss etwas zurückgeben; er hat `0`
> gewählt. *Der Preis steht hier, damit ihn jemand kennt.*

## Die Eingabe-Ebene entscheidet — und das Modul kann sie nicht prüfen

| Punkte liegen in… | Ergebnis ist… |
|---|---|
| der **geneigten** Flächenebene (`surf.polygon`, u/v) | die **echte Dachfläche** — der Zweck des Moduls |
| der **Grundriss**-Ebene | die **Grundfläche** — kleiner, und niemand merkt es |

**Beide Male kommt eine plausible Zahl in m² heraus.** *Es gibt keinen Rückgabewert, keinen Typ und
keine Meldung, die die beiden auseinanderhält.* **Das ist die einzige Weise, wie dieses Modul falsch
benutzt werden kann — und sie ist von innen nicht abzuwehren.**

## Selbstschneidendes Polygon — keine Prüfung, keine Meldung

Die Sammlung sagt zu F-011 ausdrücklich: *„Selbstschneidendes Polygon liefert eine **falsche, aber
plausible** Zahl — keine Fehlermeldung. **Deshalb vorher F-013 laufen lassen.**"*
**Hier läuft keine F-013-Prüfung** — dieselbe Lage wie bei W-05. *Gemeldet, nicht bewertet.*

## Nicht geschlossene Punktfolge — kein Fall

Der Code schließt selbst: `punkte[(i + 1) % punkte.length]` (Z.36). **Eine wiederholte
Schlusskoordinate schadet nicht** — sie erzeugt ein Segment der Länge 0, das nichts beiträgt.

## DREI Umsetzungen derselben Formel im Haus — und zwei heißen gleich

| Fassung | Fundstelle | Eingabe | Ergebnis | Vorzeichen |
|---|---|---|---|---|
| `polygonFlaecheM2()` | `resources/planner/hausplaner/geometry/polygonFlaeche.ts:31` | **Meter** | m² | Betrag |
| `polygonFlaecheM2()` | `app/Services/Heizlast/GeometrieAbleitungService.php:118` | **MILLIMETER** (÷ 1.000.000) | m² | Betrag |
| `signierteFlaeche()` | `resources/planner/hausplaner/geometry/roomDetection.ts:70` | mm | mm² | **mit Vorzeichen** (W-05) |

> **Zwei Funktionen mit demselben Namen, derselben Formel und verschiedener Eingabe-Einheit.**
> *Wer sie verwechselt, irrt um den Faktor eine Million — und beide liefern eine Zahl, die aussieht
> wie eine Fläche.*

**Dazu ein Unterschied im Verhalten:** die TS-Fassung prüft jeden Punkt mit `Number.isFinite` und gibt
`0` zurück; die PHP-Fassung castet nach `float` und hat **keine solche Prüfung**.

## Der Azimut: 0…180 ist in zwei Konventionen gültig und bedeutet Entgegengesetztes

**Dieses Modul nimmt keinen Azimut** — es hat keinen Parameter dafür (`2-FUNKTION`). Es kann ihn also
nicht stillschweigend durchrechnen. **Die Gefahr sitzt dort, wo die Fläche mit einer Ausrichtung
zusammengeführt wird**, und sie gehört hierher, weil eine Dachfläche ohne Ausrichtung selten bleibt:

| Konvention | 0 bedeutet | Bereich | Fundstelle |
|---|---|---|---|
| **Kompass** (Hausstandard) | **Nord** | 0…360 | `database/migrations/2024_06_04_103808_create_p_v_roofs_table.php:67` |
| **PVGIS** (Fremd-API) | **Süd** | −180…180 | `app/Services/Energie/PvgisErtragService.php:41` |

**Der Bereich 0…180 ist in beiden gültig und bedeutet das Gegenteil.** Ein Süddach trägt im Kompass
**180**; unverändert an PVGIS gegeben, rechnet PVGIS ein **Norddach**. *Größtmöglicher Fehler — und
nichts schlägt an, weil 180 in beiden Systemen eine gültige Zahl ist.*

**Eine Umrechnung gibt es im Haus nicht** (gemessen: keine `+180`/`−180` in `app/Services/Energie`).
**Verboten wäre, den Wert stillschweigend durchzurechnen** — dieses Modul tut es nicht, weil es ihn
gar nicht erst annimmt.
