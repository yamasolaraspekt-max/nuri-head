# W-06 · Geschoss verwalten — CODE

**Angebunden aus DREI vorhandenen Modulen** — Stand 13.08.: **355 Zeilen, 10 Ausfuhren**. *Jede
Zeilenzahl und jede Ausfuhr einzeln am Bau-Stand gezählt, nicht aus dem Auftrag übernommen und nicht
aus der Summe abgeleitet.*

| Schicht | Modul | Z | Ausfuhren |
|---|---|---|---|
| reine Geometrie | `resources/planner/hausplaner/geometry/geschossVorlage.ts` | 78 | `LevelVorlage` (11) · `GeschossDuplikat` (32) · `dupliziereGeschoss()` (43) |
| Daten | `resources/planner/hausplaner/app/dashboard/geschossStapel.ts` | 104 | `StapelEintrag` (22) · `Stapel` (34) · `hoehenLabel()` (51) · `stapel()` (66) · `kurzfassung()` (94) · `nachbar()` (100) |
| Oberfläche | `resources/planner/hausplaner/app/dashboard/GeschossFlaeche.tsx` | 173 | `GeschossFlaeche()` (56) |

## Zwei Typen, die absichtlich NICHT ausgeführt werden

`NodeBasis` (`geschossVorlage.ts:20`) und `RoofBasis` (`:27`) sind **nicht exportiert**. *Sie sind die
Innenseite des Vertrags: die Geometrieschicht sagt damit, welche vier bzw. zwei Felder sie braucht —
und mehr sieht sie nicht.* **Der Aufrufer nennt seinen eigenen Typ und bekommt ihn zurück**
(`N[]`, `R | null`), er muss nichts umdeuten. *Siehe `2-FUNKTION`, „Die Generics sind eine Aussage".*

## Die Aufrufer, je Modul über die Importzeile erhoben

```text
geschossVorlage.ts    app/HausplanerApp.tsx:113          dupliziereGeschoss
geschossStapel.ts     app/HausplanerApp.tsx:93           stapel
                      app/dashboard/GeschossFlaeche.tsx:29  stapel · kurzfassung · StapelEintrag
                      app/dashboard/palette.ts:38        type Stapel
GeschossFlaeche.tsx   app/dashboard/Kopfrahmen.tsx:38    GeschossFlaeche
```

> **Alle drei Module sind angeschlossen** — *das ist der Punkt, den die Registerzeile verschweigt.
> Sie nennt keinen Code und behauptet damit nichts Falsches; sie lässt nur offen, dass W-06 gebaut
> und verdrahtet ist.*

## Der Weg vom Duplikat zum Befehl — seit A-31 EIN Undo-Schritt

```text
GeschossFlaeche  -> onDuplizieren
HausplanerApp    -> dupliziereGeschoss(...)                geometry, rein
                 -> befehleGeschossDuplizieren(dup)        app/sammelBefehle.ts
                 -> executeCommands([ADD_LEVEL, ...ADD_NODE, ADD_ROOF?])
                    EIN produceWithPatches, EIN historie.push
```

*Vorher waren es `N+2` Undo-Schritte für **ein** Geschoss.* **Die Klammer sitzt im Store, nicht in
diesem Werkzeug** — W-06 liefert die Daten, A-31 hat die Ausführung gebündelt.

## Was gebaut ist und was nicht

**Gebaut:** alle drei Schichten, rein getrennt, mit Wächtern (siehe `6-PRUEFUNG`).
**Nicht vorhanden:** ein eigener Modellbefehl. *W-06 kommt mit `ADD_LEVEL`, `ADD_NODE`, `ADD_ROOF`
und `UPDATE_LEVEL` aus — vier Befehle, die es schon gab.*
