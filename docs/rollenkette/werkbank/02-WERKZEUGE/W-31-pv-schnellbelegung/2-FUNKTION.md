# W-31 · PV-Schnellbelegung — FUNKTION

## Eingabe — `PvEingabe` (`geometry/pvBelegung.ts:10`), SIEBEN Felder

| Was | Feld | Einheit | Pflicht | Vorgabe |
|---|---|---|---|---|
| Dachbreite horizontal | `dachLaenge` | mm | ja | — |
| Dachlänge in Falllinie (entlang der Neigung) | `dachBreite` | mm | ja | — |
| Modulbreite (Hochformat) | `modulBreite` | mm | ja | — |
| Modulhöhe (Hochformat) | `modulHoehe` | mm | ja | — |
| Modul-Nennleistung | `modulLeistung` | Wp | ja | — |
| umlaufender Randabstand (First/Traufe/Ortgang) | `randabstand?` | mm | nein | **300** |
| Spalt zwischen Modulen | `modulabstand?` | mm | nein | **20** |

> ***Sieben Felder, und keines davon ist eine Richtung.*** *Kein Azimut, kein Neigungswinkel, keine
> Himmelsrichtung — das ist der gemessene Beleg für den tragenden Satz aus `1-ZWECK`.* **Die
> Dachneigung steckt implizit in `dachBreite`: das Maß läuft entlang der Falllinie, also auf der
> geneigten Fläche und nicht in der Grundrissprojektion** (`:13`).

## Verarbeitung — kein Zustandsautomat, eine reine Rechnung

```text
1. Nutzflaeche       nutzL = max(0, dachLaenge - 2·rand)          (:49)
                     nutzB = max(0, dachBreite - 2·rand)          (:50)
                     -> der Rand geht ZWEIMAL ab, umlaufend

2. beide Lagen       hoch = belegung(nutzL, nutzB, mBreite, mHoehe, gap)   (:52)
                     quer = belegung(nutzL, nutzB, mHoehe, mBreite, gap)   (:53)
                     -> DIESELBE Funktion, Modulmasse VERTAUSCHT

3. Belegung je Lage  spalten = max(0, floor((nutzL + gap) / (mW + gap)))   (:38)
                     reihen  = max(0, floor((nutzB + gap) / (mH + gap)))   (:39)
                     -> '+ gap' im Zaehler: der letzte Spalt entfaellt am Rand.
                        Ohne ihn faellt bei genau passender Flaeche ein Modul weg.

4. Auswahl           nimmHoch = hochN >= querN                    (:57)
                     -> bei GLEICHSTAND gewinnt hochkant. Determinismus vor Zufall.

5. Kennzahlen        kWp        = runde2(n · modulLeistung / 1000)         (:70)
                     dachFlaeche= (dachLaenge/1000) · (dachBreite/1000)    (:62)
                     belegt     = n · Modulflaeche                          (:63)
                     nutzung    = runde2(belegt / dachFlaeche · 100)        (:73)
                     -> Division nur, wenn dachFlaeche > 0; sonst 0, kein NaN
```

> **Schritt 3 ist die Stelle, an der eine Handrechnung schiefgeht.** *`+ gap` im Zähler bildet ab,
> dass **zwischen** n Modulen nur n−1 Spalte liegen.* **Schritt 4 ist die Stelle, die sie ganz
> überspringt:** *quer kann mehr Module ergeben als hochkant, und die Funktion probiert beide.*

**Die Flächennutzung wird gegen die BRUTTO-Dachfläche gerechnet** (`:62`, ohne Randabzug) — *also
gegen das, was der Anwender als Dach ausmisst, nicht gegen die Nutzfläche.* **Ein Wert von 100 %
ist damit unerreichbar, und das ist richtig so:** der Randabstand ist echte, nicht belegbare Fläche.

## Ausgabe — `PvBelegung` (`:26`)

| Was | Feld | Einheit |
|---|---|---|
| gewählte Lage | `orientierung` | `'hochkant' \| 'quer'` |
| Raster | `spalten` · `reihen` | Stück |
| Module gesamt | `moduleGesamt` | Stück |
| Anlagenleistung | `kWp` | kWp, auf 2 Stellen |
| Dachfläche brutto | `dachFlaecheM2` | m², auf 2 Stellen |
| belegte Fläche | `belegteFlaecheM2` | m², auf 2 Stellen |
| Flächennutzung | `flaechennutzung` | %, auf 2 Stellen |

**Gerundet wird an EINER Stelle** — `r2()` (`:43`), auf zwei Nachkommastellen. *`moduleGesamt`,
`spalten` und `reihen` sind ganzzahlig durch `Math.floor`, nicht durch Rundung.*

## Kommando (für Rückgängig)

**Keines.** *W-31 ist eine reine Rechnung ohne Modellzustand — es gibt nichts zurückzunehmen.* Die
Eingaben kommen aus dem Engine-Panel, das Ergebnis wird angezeigt.

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** *nein.*
- **Rechnet in Schicht 2 (Geometrie):** **keine F-Nummer** — siehe `3-FORMELN`.
- **Lebt in Schicht 3 (Anwendung):** `app/dashboard/enginePanels.ts` (`:403`).
- **Zeigt sich in Schicht 4/5:** Engine-Panel „PV-Schnellbelegung" und die Fachfläche
  `fach-pv-module`.

## Was NICHT hierher gehört — die Nachbarn

| Nachbar | Grenze |
|---|---|
| **W-36** Fähigkeiten-Navigation | führt den Registry-Eintrag (`faehigkeiten.ts:80`), rechnet aber nichts. *W-31 ist der Rechner, W-36 der Weg dorthin.* |
| **W-37** Rechenpanels | die **Adapter** zwischen Bedienung und Engine — `alsPvEingabe()` (`enginePanels.ts:509`) wandelt Texte in `PvEingabe`. *W-37 wandelt, W-31 rechnet.* |
| **W-19** Sonne und Verschattung | Ertrag und Verschattung. **Ausdrücklich außerhalb**, siehe `7-GRENZEN`. |
| **W-07 / W-08** Dach | liefern die Dachfläche, auf der belegt wird. W-31 nimmt sie als zwei Zahlen entgegen und kennt keine Dachgeometrie. |
