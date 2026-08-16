# W-29 · Dachdurchdringungen — FUNKTION

> **Ablesung des vorhandenen Codes, nicht Vorgabe.** *Jede Zeilenangabe ist einzeln geöffnet worden.*

## Drei Module, drei Aufgaben — und sie hängen in EINER Richtung zusammen

```text
dachOeffnung.ts      Wo liegt das Loch auf der Flaeche?     96 Z.,  4 Exporte
      |
      | oeffnungRechteck()   dachAusschnitt.ts:27 importiert, :303 ruft auf
      v
dachAusschnitt.ts    Darf man es ueberhaupt schneiden?     510 Z., 20 Exporte
                     und wieviel Flaeche bleibt uebrig?

auswechslung.ts      Was traegt danach?                    174 Z.,  5 Exporte
                     -> von KEINEM Produktivpfad gerufen
```

> ***Der Pfeil geht nur in eine Richtung, und das dritte Modul hängt an keinem.***

## `dachOeffnung` — das Loch als Rechteck in Flächenkoordinaten

```text
:52  oeffnungVTiefeM(o)   art 'window' -> hoeheM, sonst tiefeM
:60  oeffnungRechteck(o, f, sicherheitsrandM = 0.1)
     :61  W = max(0.01, breiteTraufeM)      Flaechenbreite, nie null
     :62  H = max(0.01, hoeheM)
     :66  uM = clamp(xRel, 0, 1) * W        relative Lage -> Meter
     :67  vM = clamp(yRel, 0, 1) * H
     :68  halbU = breiteM / 2 + rand        das Loch PLUS Sicherheitsrand
     :69  halbV = tiefeM  / 2 + rand
     :71  uMinRel = clamp((uM - halbU) / W, 0, 1)   und zurueck ins Relative
```

> ***Der Sicherheitsrand wird ZUM Loch gerechnet, nicht davon abgezogen*** (`:68-69`) — *das
> geschnittene Feld ist also größer als das Bauteil.* **Bei einem Dachfenster ist genau das
> richtig:** *der Ausschnitt in der Dachhaut muss über den Rahmen hinausgehen.*
>
> **Und `clamp` steht zweimal** (`:66-67` *für die Lage,* `:71-74` *für die Ränder*): *das Ergebnis
> bleibt im Band `[0,1]`, auch wenn die Öffnung über die Fläche hinausragt.* `innerhalb` *hält
> daneben fest, ob sie es tat.*

## `auswechslung` — die Rechnung, die niemand ruft

```text
:69  sparrenPositionenU(breiteM, rafterDistM, rafterWidthM = 0.08)
     :71  dist = max(0.05, rafterDistM, Vorgabe 0.7)
     :74  numRafters = min(2000, max(1, floor(breite / dist)))
:87  analysiereAuswechslung(flaeche, oeffnung, opts)
     :95  rand           = max(0, sicherheitsrandM, Vorgabe 0.05)
     :96  randThreshold  = max(0, randThresholdM,  Vorgabe 0.30)
     :104 keine gueltige Flaeche -> leeres Ergebnis, nichts ableiten
```

**Was sie liefert** (`:42-58`, die Feldkommentare wörtlich):

| Feld | Bedeutung |
|---|---|
| `betroffeneSparren` | Anzahl der von der Öffnung **inkl. Sicherheitsrand** geschnittenen Sparren |
| `spanntMehrereFelder` | mehr als **ein** Sparrenfeld betroffen |
| `naheRandzone` · `randzonen` | nahe First / Traufe / Ortgang |
| `wechselErforderlich` | überhaupt ein Sparren geschnitten |
| `pruefpflichtig` | **nicht sicher geometrisch ableitbar** |
| `wechselAnzahl` | 0 oder **2** — oben und unten |
| `wechselLaengeM` | Spannweite zwischen den **tragenden** Sparren |

> ***`pruefpflichtig` ist das ehrlichste Feld des ganzen Werkzeugs.*** *Es sagt nicht „geht nicht",
> sondern „hier reicht Geometrie nicht" — und genau diese Unterscheidung fehlt dem gebauten Weg,
> der stattdessen pauschal `true` meldet.*

## `dachAusschnitt` — darf man schneiden, und was bleibt übrig

```text
:32   AusschnittStatus = 'sicher' | 'teilweise' | 'pruefpflichtig'
:80   KANTEN_RAND_M = 0.2
:72   istAchsenRechteck        Toleranz 1e-4
:96   istKonvexesViereck       Toleranz 1e-6
:122  istSichereTrapezflaeche
:139  istSichereKonvexeFlaeche  minEcken 4
:177  konvexeTeilflaechenSicher
:228  rechteckKantenAbstandOk
:269  istEinfacheDurchdringung(art)
:279  istGaubeDurchdringung(art)
:289  berechneAusschnitt(...)
:458  sichereLoecher(...)
:500  flaechenBilanz(bruttoM2, befunde)
```

> ***Der dreiwertige Status ist der Kern dieses Moduls.*** *`'sicher'` und `'pruefpflichtig'` sind
> nicht dasselbe wie „ja" und „nein" — die dritte Antwort ist „das kann die Geometrie hier nicht
> entscheiden".* **Sieben der zwanzig Ausfuhren sind Prüfungen der Flächenform** (`istAchsenRechteck`
> *bis* `konvexeTeilflaechenSicher`), *weil ein Loch in einer Trapez- oder Walmfläche etwas anderes
> ist als eines in einem Rechteck.*

**Gauben werden getrennt behandelt** (`:279 istGaubeDurchdringung`) — *sie sind keine Öffnung in der
Fläche, sondern ein Aufbau darauf.*
