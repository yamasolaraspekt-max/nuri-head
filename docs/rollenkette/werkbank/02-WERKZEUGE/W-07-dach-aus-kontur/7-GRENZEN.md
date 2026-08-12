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

## Die Azimutgrenze — F-028 🔴, und sie ist keine Ungenauigkeit, sondern eine Doppeldeutigkeit

*Ergänzt 12.08. (W-07N, Yamas Auflage 1). **Der bestehende Inhalt dieses Blattes ist unverändert** —
diese Grenze kommt hinzu, weil sie fehlte, nicht weil etwas falsch war.*

**Zwei Konventionen sind im Haus, und im Bereich 0…180 ist ein Wert in beiden gültig:**

```text
Kompass  0 = Nord, 90 = Ost, 180 = Sued, 270 = West      0 … 360
          -> belegt in database/migrations/…create_p_v_roofs_table.php:67
             $table->float('roof_azimuth')->nullable(); // 0=N, 90=E, 180=S, 270=W
PVGIS    0 = Sued, negativ = Ost, positiv = West         -180 … +180
          -> belegt in app/Services/Energie/PvgisErtragService.php:41
             @param float $aspect  Azimut nach PVGIS-Konvention: 0 = Süd, -90 = Ost, 90 = West

-> "90" heisst im einen OST, im anderen WEST-SUEDWEST. "180" heisst SUED oder NORD.
   Ein Wert zwischen 0 und 180 ist in BEIDEN Konventionen gueltig und bedeutet
   Entgegengesetztes. Es gibt keinen Wert, an dem der Fehler auffaellt.
```

### Die Ableitung ist da — zweisprachig und konsistent. Sie wird GENANNT, nicht gebaut

**Wer aus einer Fläche einen Azimut braucht, findet ihn zweimal im Haus — und beide rechnen dasselbe:**

| Seite | Funktion | Fundstelle |
|---|---|---|
| TypeScript | `azimutDerNormalen(start, end, seite)` | [`wallGeometry.ts:37`](../../../../../resources/planner/hausplaner/geometry/wallGeometry.ts#L37) |
| PHP | `azimutRechteNormale(von, bis)` | [`SzeneProjektionService.php:258`](../../../../../app/Services/Geometrie/SzeneProjektionService.php#L258) |

**Beide tragen die Kompass-Konvention `Nord = +y`** und liefern ganzzahlig `0…359` bzw. `0…360`.
*Sie sind die einzige gepflegte Ableitung im Haus, und sie widersprechen einander nicht.*

> **Deshalb baut dieses Blatt keinen dritten Rechenweg.** *Eine neue Ableitung wäre die dritte
> Wahrheit — und der Bereich `0…180` zeigt gerade, was zwei schon anrichten.*

**Was W-07 selbst benutzt** — [`dachGeometrie.ts:4`](../../../../../resources/planner/hausplaner/geometry/dachGeometrie.ts#L4):
`Nord = +y`, und der Flächenazimut wird **nie gepflegt**, sondern aus `roof.firstAzimutGrad`
abgeleitet (Satteldach: First ± 90°).

| Grenze | Was gilt |
|---|---|
| **Kein Azimut ohne Konvention** | ein Zahlenwert allein darf dieses Werkzeug nicht verlassen und nicht erreichen; die Konvention gehört mitgeliefert |
| **Keine Umrechnung in W-07** | die Umrechnung Kompass ↔ PVGIS ist **nicht** Teil dieses Werkzeugs (Yamas Schritt 7/8, eigener Auftrag) |
| **`firstAzimutGrad` ≠ Flächenazimut** | das Feld ist die **First**-Richtung; wer es als Flächenazimut liest, liegt beim Satteldach um **90°** falsch |
| **`azimut_grad = null`** | heißt **horizontal** (Flachdach), nicht „unbekannt" — ein `null` darf nicht als 0 = Nord gelesen werden |

> **Warum das hier steht und nicht bei den Ungenauigkeiten:** *eine Ungenauigkeit ist ein Fehler mit
> bekannter Größe. Dies ist ein Fehler mit bekannter **Richtung** — 90° oder 180°, je nach
> Verwechslung — und ohne jedes Signal am Wert selbst. **PV-Ertrag und Heizlast hängen daran**; die
> Formelsammlung führt es deshalb als **F-028 🔴 GESPERRT für das Durchreichen**.*

**Der Bestand ist gemessen, nicht vermutet:** *Yamas drei SELECTs auf `p_v_roofs` ergaben **0/0/0**
(belegt in `docs/auftraege/aktiv/A-13-roof-azimuth-absichern.md:613`). **Es gibt keine Altdaten mit
Azimut** — das nimmt der Grenze die Rückwirkung, nicht die Gültigkeit: sie betrifft künftige
Eingaben. Ein leerer Bestand ist keine Freigabe.*

## Was an W-07 NICHT erledigt ist — und trotzdem `BESCHRIEBEN` danebensteht

**Die Registerzeile trägt `BESCHRIEBEN`. Das heißt „Blätter gefüllt", nicht „Sache geklärt."**
*Diese drei Posten sind offen, und sie stehen hier, damit der nächste Leser sie nicht für erledigt
hält, weil das Wort danebensteht.*

**1 · Die drei Werkbank-Nachträge N1, N2 und N3.** Sie betreffen die Formeln dieses Werkzeugs und
sind nicht eingearbeitet.

**2 · Der Widerspruch F-020-Weg gegen `roof.anbau`-Weg** (`db1dc3b6`). Dieses Blatt beschreibt den
**F-020-Weg**, während die Insel `roof.anbau` baut. **Zwei Wege, ein Werkzeug — und keiner ist als
der gültige benannt.**

**3 · Die acht F-Nummern der Registerzeile sind UNGEPRÜFT:**

```text
F-010 · F-013 · F-014 · F-025 · F-026 · F-020 · F-021 · F-022
```

**Keine einzige davon ist gegen den Code gemessen worden.** *Nach `603eddc2` ist bewiesen, dass
Registerformeln falsch zugeordnet sein können — dort fielen sieben von zehn. Bei W-07 sind es acht,
und die Trefferquote von damals ist kein Grund zur Beruhigung.*

> **Warum das hier steht und nicht nur im Auftragsblatt:** *ein Auftragsblatt liest, wer den Auftrag
> sucht. Ein Werkzeug-Blatt liest, wer das Werkzeug benutzt.* **Die zweite Gruppe ist die, die von
> einem ungeprüften F-Verweis überrascht wird.**
