# W-28 · Dachentwässerung — FUNKTION

## Was das Werkzeug tut

**Nichts.** *Es gibt keinen Aufruf, keinen Zustand, keine Ausgabe.* **Was es gibt, ist ein
Typ-Wert, den der Übersetzer kennt und den kein Code je herstellt.**

```text
LinienBauteilArt (linienBauteile.ts:20-22)
  'schneefang' | 'laufrost' | 'trittstufe' | 'wartungsgang'
  | 'dachrinne' | 'firstlinie' | 'modulsperrlinie'
     ^^^^^^^^^^
     erzeugt: nirgends
```

## Was das Modul um ihn herum tut — damit die Einordnung stimmt

`geometry/linienBauteile.ts`, **167 Zeilen, 9 Ausfuhren:**

| Ausfuhr | Zeile | tut |
|---|---|---|
| `LinienBauteilArt` | `:20` | die Aufzählung, in der `dachrinne` steht |
| `DachLinienBauteil` | `:25` | ein Linienbauteil: Art, Fläche, relative Lage `v` (0 = Traufe, 1 = First) |
| `SCHNEEFANG_HINWEIS` | `:64` | der Vorbehalt zur Schneefang-Platzierung |
| `platziereSchneefang` | `:83` | **die eigentliche Rechnung des Moduls** |
| `sperrzoneVRel` | `:127` | aus einem Linienbauteil wird eine Sperrzone in `v` |
| `istInSperrzone` | `:139` | liegt ein Modul in dieser Zone |
| `flaecheInfoAusPolygon` | `:151` | Hilfsmaß für die Fläche |

> ***Der Zweck des Moduls ist die PV-Belegung, nicht die Entwässerung.*** *Ein Linienbauteil
> interessiert hier, weil es eine **Sperrzone** erzeugt — eine Zone, in der kein Modul liegen
> darf.* **Eine Dachrinne wäre dafür ein legitimer Fall** (an der Traufe ist kein Platz für ein
> Modul) — *und genau als solcher ist der Name eingetragen worden.*

## Die Folgerung, die daraus NICHT gezogen werden darf

**Dass die Dachrinne „schon halb angeschlossen" sei.** *Sie ist als möglicher Sperrzonen-Träger
vorgesehen; ein Werkzeug zur Entwässerung ist etwas anderes.* **Die beiden Aufgaben teilen sich
nur den Ort:**

```text
Sperrzone      wo darf KEIN Modul liegen        -> Geometrie in v
Entwaesserung  wieviel Wasser, welcher Quer-
               schnitt, wieviele Fallrohre      -> Bemessung nach Regen-
                                                  spende und Dachflaeche
```

> **Das erste ist eine Lage, das zweite eine Menge.** *Wer den Namen als Anfang der Entwässerung
> liest, hält eine Zeile Typdeklaration für die halbe Strecke.*

## Der Zustand, den es nicht gibt

**Kein `roofDrainage`, kein `rinneById`, kein Feld an einer Dachfläche.** *Gemessen:*

```text
toolRegistry.ts    rinne 0 · entwaesser 0 · regen 0
werkzeugPaket.ts   rinne 0 · entwaesser 0 · regen 0
```

> ***Damit gibt es auch nichts zu bedienen*** *(`4-BEDIENUNG`)* ***und nichts zu prüfen***
> *(`6-PRUEFUNG`).* **Das ist keine Lücke im Sinne von „unfertig", sondern die ehrliche Auskunft,
> dass dieses Werkzeug noch nicht begonnen wurde.**
