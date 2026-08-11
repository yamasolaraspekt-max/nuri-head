# W-13 · Auswahl und Griffe — CODE

**Angebunden aus vier vorhandenen Modulen** — 321 Zeilen, 18 Ausfuhren, jede Zahl einzeln nachgezählt.

**`resources/planner/hausplaner/app/tools/auswahlModus.ts` — 98 Z, 7 Ausfuhren**
`Auswahlmodus` (23) · `Modifikatoren` (26) · `aufloeseAuswahlmodus()` (42) · `Auswahlstand` (50) ·
`wendeAuswahlAn()` (64) · `klickInsLeere()` (92) · `LEERE_AUSWAHL` (98)

**`resources/planner/hausplaner/app/tools/auswahlDarstellung.ts` — 71 Z, 3 Ausfuhren**
`DarstellungEingabe` (18) · `Darstellung` (28) · `aufloeseDarstellung()` (51)

**`resources/planner/hausplaner/app/tools/auswahlUebersicht.ts` — 77 Z, 4 Ausfuhren**
`TypZaehlung` (30) · `MehrfachUebersicht` (38) · `mehrfachUebersicht()` (50) · `benenne()` (73)

**`resources/planner/hausplaner/app/tools/trefferSuche.ts` — 75 Z, 4 Ausfuhren**
`TrefferKandidat` (21) · `besterTreffer()` (44) · `trefferInReihenfolge()` (55) · `toleranzInWelt()` (73)

**Werkzeugschicht:** `toolRegistry.ts:39` — `id: 'auswahl'`.

## AUSSCHLUSS — `editierGeometrie.ts` gehört zu W-14

```text
resources/planner/hausplaner/geometry/editierGeometrie.ts   75 Zeilen — NICHT Gegenstand
  versetzePunkt · versetzteWand · spiegelePunkt · spiegelteWand · Bbox · bbox · achsenMitte · Achse
```

**Das ist Versetzen und Spiegeln — also W-14 (Kopieren · Spiegeln · Drehen).** *W-13 ist Auswahl und
**Griffe**: das Auswählen und Anfassen, nicht das Verschieben.*

> **Der Auftrag hat diese Zuordnung selbst berichtigt** — die Matrix des Planners hatte das Modul
> W-13 zugeschlagen. **Ich habe es am Code nachgemessen und stimme zu:** kein einziger der acht
> Exporte betrifft Auswahl oder Griffe; alle acht betreffen Lageänderung.

## Was gebaut ist und was nicht

**Gebaut:** vier reine Module **und** ein Registry-Werkzeug. *Stufe 2 (`GEBAUT`) hat hier eine
andere Ausgangslage als bei den übrigen Klasse-A-Werkzeugen.*
