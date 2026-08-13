# W-37 · Rechenpanels — FUNKTION

## Die ACHT Adapter — über die SIGNATUR erhoben, mit Fundstelle

**Alle acht haben dieselbe Bauform: `(werte: Record<string, string>) => <Eingabetyp>`.**

| # | Adapter | Fundstelle | liefert |
|---|---|---|---|
| 1 | `alsTreppenEingabe` | `enginePanels.ts:100` | `TreppenEingabe` |
| 2 | `alsSparrenEingabe` | `:414` | `SparrenEingabe` |
| 3 | `alsFbhEingabe` | `:439` | `FbhEingabe` |
| 4 | **`alsBetriebsBedingung`** | `:457` | `BetriebsBedingung` |
| 5 | `alsUwEingabe` | `:482` | `UwEingabe` |
| 6 | `alsAbwasserEingabe` | `:494` | `AbwasserEingabe` |
| 7 | **`alsArbeitsdreieck`** | `:503` | `Arbeitsdreieck` |
| 8 | `alsPvEingabe` | `:509` | `PvEingabe` |

**Die zwei fett gesetzten fallen durch jedes Namensmuster** — *sie heißen nach ihrer Sache, nicht
nach ihrer Rolle.* Siehe `1-ZWECK`.

> ***Und sie rechnen nicht.*** *Jeder von ihnen liest Felder aus einer Textkarte und baut daraus
> einen typisierten Datensatz. Was danach passiert, steht in `geometry/` — siehe `3-FORMELN`.*

## Die Regel, die alle acht teilen: LEER ist nicht NULL

**Wörtlich aus dem Kommentar über `alsSparrenEingabe`:**

> *„Dieselbe Regel wie bei der Treppe: **leere Felder werden weggelassen, nicht auf 0 gesetzt. Eine
> 0 wäre eine erfundene Angabe.**"*

```text
Pflichtfeld    feldZahl(werte, 'x') ?? 0
               -> leer wird 0 und laeuft in die Rechnung
Optionsfeld    ...(feldZahl(...) !== undefined ? { x: ... } : {})
               -> leer wird WEGGELASSEN, die Engine setzt ihre Vorgabe
```

> **Die Auslassung ist die einzige Schreibweise, die „nicht angegeben" von „ausdrücklich null"
> unterscheidet.** *Wer sie durch eine 0 ersetzt, macht aus einer fehlenden Angabe eine gemessene —
> und die Engine kann den Unterschied nicht mehr sehen.*

## Die Panelliste und die vier Typen

```text
enginePanels.ts:35   EngineFeld           ein Eingabefeld (Schluessel, Label, Einheit, Pflicht, Vorgabe)
              :51    EngineErgebnisFeld   ein Ergebnisfeld (Schluessel, Label, Einheit)
              :57    EnginePanel          engineId · titel · zweck · grundlage · felder ·
                                          ergebnisFelder · berechne
              :89    EngineErgebnis       was eine Engine zurueckgibt
              :119   ENGINE_PANELS        readonly EnginePanel[] — ACHT Eintraege, am Bau-Stand gezaehlt
```

**Acht Panels, acht Adapter — und das ist kein Zufall, sondern die Bauform:** *jedes Panel nennt in
`berechne` genau einen Adapter und genau eine Engine.*

## Die Bedienfläche des Moduls — drei Ausfuhren, die keine Adapter sind

```text
:522  enginePanel(engineId)               -> EnginePanel | undefined
:527  startwerte(panel)                   -> Record<string, string>   (aus den Vorgaben)
:538  fehlendePflichtfelder(panel, werte) -> EngineFeld[]
```

> ***`enginePanel` ist die breitest benutzte Ausfuhr des Moduls*** — *gemessen: **alle SECHS**
> importierenden Wächter benutzen sie.* **`startwerte` und `fehlendePflichtfelder` stehen in
> vieren, und zwar immer zusammen** — *sie gehören zum selben Vorgang: Formular vorbelegen, dann
> prüfen, was fehlt.*

*Diese drei standen in keinem früheren Scope-Block dieses Auftrags — sie sind der dritte
Vollständigkeitsfund an seinen Blättern und deshalb hier ausdrücklich aufgeführt.*

## Die Anzeige — `app/EngineFlaeche.tsx` (199 Z.)

**Ein Export**, die Fläche. *Sie nimmt ein `EnginePanel`, zeigt die Felder, ruft `berechne` und
stellt das Ergebnis dar — samt Prüfhinweisen mit ihrer Schwere (siehe `4-BEDIENUNG`).*

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** *nein.* Kein Befehl, kein Schema, keine Szene-Mutation.
- **Rechnet in Schicht 2 (Geometrie):** **nichts selbst.** *Es ruft auf, was dort liegt.*
- **Lebt in Schicht 3 (Anwendung):** `app/dashboard/enginePanels.ts`.
- **Zeigt sich in Schicht 4/5:** `app/EngineFlaeche.tsx`.

## Die Scope-Grenze zu `geometry/`

> **Die Rechnungen werden AUFGERUFEN, nicht beschrieben.** *Was `berechneSparren`, `pruefeAbwasser`
> oder `pvSchnellBelegung` tun, steht in den Blättern ihrer eigenen Werkzeuge — W-21, W-31 und die
> übrigen.* **Dieses Blatt beschreibt den Weg dorthin und die Zusagen, die auf dem Weg gelten** —
> allen voran die A-14-Ausgabeauflage.
