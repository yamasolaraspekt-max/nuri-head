# W-07 · Dach aus Kontur — GRENZEN

> **Musterblatt.** Dieses Werkzeug ist genau an einer fehlenden Grenze gescheitert.
> Deshalb steht hier mehr als sonst — es ist der Lehrfall.

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender sieht |
|---|---|---|
| **Einspringende Ecken** (L, U, T) | Spalt-Ereignisse im Skeleton sind nicht umgesetzt | „Für diesen Grundriss kann noch kein Dach berechnet werden: er hat einspringende Ecken. Möglich sind zurzeit nur konvexe Umrisse. Wege: Grundriss in zwei rechteckige Bereiche teilen und je ein Dach setzen — oder Flachdach wählen." |
| **Selbstschneidende Kontur** | Fläche und Skeleton wären undefiniert | „Der Umriss überschneidet sich bei Punkt (x, y). Bitte die Wände dort begradigen." |
| **Neigung ≥ 85°** | `tan(α)` läuft gegen unendlich (F-021) | „Eine Dachneigung von 85° oder mehr ist nicht darstellbar." |
| **Kontur mit weniger als 3 Punkten** | Kein Polygon | „Für ein Dach braucht es mindestens drei Wände, die einen geschlossenen Umriss bilden." |
| **Entartete Kontur** (alle Punkte auf einer Linie) | 2A ≈ 0 in F-010 | „Der Umriss schließt keine Fläche ein." |
| **Löcher im Grundriss** (Innenhof) | Skeleton mit Löchern nicht umgesetzt | „Grundrisse mit Innenhof werden noch nicht unterstützt." |
| Gauben, Dachfenster, Zwerchgiebel | Nicht Zweck (siehe 1-ZWECK.md) | erscheint gar nicht als Angebot |

## Die Absagekette — hier lag der Fehler

**So war es (falsch):**

```
geometry/dachGeometrie.ts:87
        wirft DachGeometrieUngueltig            ✓ korrekt
                    ↓
renderers/three-d/szene.ts:499   catch { continue; }    ✗ verschluckt
renderers/three-d/szene.ts:545   catch { return; }      ✗ verschluckt
                    ↓
Oberfläche                        zeigt NICHTS           ✗
                    ↓
Anwender sieht ein Haus ohne Dach, ohne jede Erklärung.
```

Der Anwender kann daraus nicht schließen, was los ist. Er sieht ein fehlendes
Bauteil und vermutet einen Absturz — dabei hat die Software vollkommen richtig
erkannt, dass sie es nicht kann, und es nur nicht gesagt.

**So muss es sein:**

| Fall | Fehlername | Wer fängt | Anwendertext steht in |
|---|---|---|---|
| Einspringende Ecke | `DachGeometrieUngueltig` (Grund: `EINSPRINGENDE_ECKE`) | Schicht 3, meldet an 5 | `4-BEDIENUNG.md` |
| Selbstschnitt | `KonturSelbstschnitt` (mit Fundort x,y) | Schicht 3 | `4-BEDIENUNG.md` |
| Neigung zu steil | `NeigungAusserhalbBereich` | Schicht 3 | `4-BEDIENUNG.md` |
| Zu wenig Punkte | `KonturUnvollstaendig` | Schicht 3 | `4-BEDIENUNG.md` |

**Zwei Dinge sind zu bauen, nicht eines:**
1. das Werkzeug selbst
2. **der Fänger, der die Absage sichtbar macht**

Auftrag A-01 wurde deshalb in zwei Kriterien geteilt: *lesbare Absage, kein Objekt,
kein Status* (K-04) und *der Fänger muss melden* (K-04b). Wer nur das erste baut,
hat den Fehler nicht behoben — nur verschoben.

## Fänger-Prüfung

- [ ] Jeder Fehlerpfad ist durch einen Test belegt, der prüft:
      **die Meldung erreicht die Oberfläche** — nicht nur, dass geworfen wurde
- [ ] Kein `catch { }` ohne Weiterreichen in `szene.ts` im Dachpfad
- [ ] Kein stilles `return` bei ungültiger Kontur
- [ ] Wenn kein Dach entsteht: es entsteht auch **kein leeres Dach-Objekt**
      und **kein Status „Dach vorhanden"**

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| Knotenkoordinaten | ± 0,5 mm (Rundung auf mm) | nie in der Praxis |
| Mikro-Dreiecke bei fast parallelen Kanten | einzelne Dreiecke < 1 mm² | erst bei Gebäuden > 100 m Kantenlänge |
| Dachfläche gegenüber exakter Rechnung | < 0,01 % | nie in der Praxis |

## Was später kommen könnte

- Spalt-Ereignisse → damit L-, U-, T-Grundrisse (**der größte offene Wunsch**)
- Löcher / Innenhöfe
- Gewichtete Kanten → verschiedene Neigungen je Dachseite (Krüppelwalm)
- Gauben als eigenes Werkzeug
