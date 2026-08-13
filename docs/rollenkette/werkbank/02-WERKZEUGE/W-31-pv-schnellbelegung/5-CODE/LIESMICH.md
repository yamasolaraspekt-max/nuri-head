# W-31 · PV-Schnellbelegung — CODE

**EIN Modul, 75 Zeilen, DREI Ausfuhren.** *Am Bau-Stand gezählt.*

| Modul | Z | Ausfuhren |
|---|---|---|
| `resources/planner/hausplaner/geometry/pvBelegung.ts` | 75 | `PvEingabe` (10) · `PvBelegung` (26) · `pvSchnellBelegung()` (46) |

**Zwei Helfer bleiben absichtlich innen:** `belegung()` (`:37`) und `r2()` (`:43`). *`belegung()`
ist der Kern — sie wird **zweimal** aufgerufen, einmal je Modul-Lage, mit vertauschten Maßen. Ihre
Nicht-Ausfuhr hält fest, dass die Lagen-Wahl zur Funktion gehört und nicht zum Aufrufer.*

## Die fünf Anschlussstellen

```text
app/dashboard/enginePanels.ts:32     import { pvSchnellBelegung, type PvEingabe }
                             :380    engineId 'engine-pv'
                             :403    berechne: pvSchnellBelegung(alsPvEingabe(werte))
                             :509    alsPvEingabe(werte) -> PvEingabe   (Adapter, W-37)
app/tools/faehigkeiten.ts:80         Registry, zustand 'verfuegbar'
app/dashboard/fachFlaechen.ts:240-258  Fachflaeche 'fach-pv-module', engine 'engine-pv'
                                       :248 typ PvEingabe · :255 typ PvBelegung
```

> **`faehigkeiten.ts` nennt das Modul als ZEICHENKETTE (`'geometry/pvBelegung'`) und importiert es
> nicht.** *Das ist Registry-Verdrahtung und keine Verriegelung — eine Umbenennung der Datei bräche
> hier nichts, was `tsc` bemerkt.* **Wer die Anbindung über Importe misst, findet diese Stelle
> nicht.**

## Das Modul ist gebaut, angeschlossen und im Programm erreichbar

*Der Registry-Eintrag trägt `zustand: 'verfuegbar'`, die Fachfläche wird gerendert
(`app/FachFlaeche.tsx`, `HausplanerStudio.tsx:18`).* **Die Registerzeile sagt `LEER` — das bezieht
sich auf die VOLLSTÄNDIGE Belegung, nicht auf diesen Teil.** Siehe `1-ZWECK`.

## Was gebaut ist und was nicht

**Gebaut:** die Schnellstufe vollständig — Belegung beider Lagen, Auswahl, Kennzahlen, mit zwei
Wächtern (siehe `6-PRUEFUNG`).
**Nicht vorhanden:** Ertrag, Verschattung, Strings, Ausrichtung. *Siehe `7-GRENZEN` — das ist keine
Lücke, sondern eine gezogene Grenze zwischen zwei Apps.*
