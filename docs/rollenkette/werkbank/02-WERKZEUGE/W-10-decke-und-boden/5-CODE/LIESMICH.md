# W-10 · Decke und Boden — CODE

> ***Der Gegenstand ist SIEBEN Schichten groß, nicht ein Modul.*** *Der Fahrplan-Eintrag nannte
> `deckenMesh.ts` — **35 Zeilen**, während der Wächter **242** hat. Ein Verhältnis von 7:1 ist der
> Hinweis darauf, dass die Sache nicht im Modul steckt.*

## Alle Schichten mit Fundstelle — am Bau-Stand geöffnet

| Schicht | Datei | Stelle |
|---|---|---|
| **Oberfläche** | `app/tools/toolRegistry.ts` | `:132-148` — Eintrag `'decke'`, **einer von ZWÖLF** |
| **Schema** | `domain/scene.types.ts` | `:338` `CeilingOeffnung` · `:348` `CeilingNode` · `:54` `SceneDocument.ceilings?` |
| **Befehlstypen** | `domain/commands.types.ts` | `:29` `ADD_CEILING` |
| **Befehle** | `commands/applyCommand.ts` | `:288` ADD · `:305` UPDATE · `:320` REMOVE |
| **Rechnung** | `commands/applyCommand.ts` | `:119` `treppenDurchbrueche`, aufgerufen `:298` |
| **Darstellung** | `renderers/three-d/deckenMesh.ts` | 35 Z., **DREI** Ausfuhren |
| **Aufruf** | `app/HausplanerApp.tsx` | `:1026-1035` |
| **Probedaten** | `fixtures/studioFixtures.ts` | `:63` `deckeTreppe()` |
| **Wächter** | `__tests__/decke.test.ts` | 242 Z., **DREIZEHN** Zusagen |

## `deckenMesh.ts` — 35 Zeilen, DREI Ausfuhren, kein `three`

```text
:10  deckenOberkanteMm(level)          elevation + defaultWallHeight
:18  deckenNettoFlaecheM2(ceiling)     Umriss minus Loecher, /1e6, nie negativ
:32  naechsteEtageElevationMm(level, decke)   elevation + Wandhoehe + Dicke
```

> **Der Dateikopf grenzt sich selbst ab** (`:2-4`): *„REINE, testbare Geometrie-Helfer (kein
> three/WebGL). Der three-Aufsatz (`szene.ts`) baut aus dem Decken-Polygon minus `oeffnungen` eine
> Shape-mit-Löchern".* **Die Datei liegt unter `renderers/three-d/` und enthält kein `three`** —
> *dieselbe Trennung wie bei `kontur.ts` (W-18).*

## Das Schema

```text
scene.types.ts:338  CeilingOeffnung { polygon }                   Loch-Polygon in mm
              :348  CeilingNode extends BaseNode, MitHerkunft
                    :349 type 'ceiling'
                    :351 polygon      Umriss in mm
                    :353 dickeMm      Default level.floorThickness
                    :355 oeffnungen?  CeilingOeffnung[]
                    :357 schichten?   { materialId?, dickeMm }[]
              :54   SceneDocument.ceilings?: CeilingNode[]
```

> **Eigene Sammlung statt Node-Union** — *der Kommentar `:346` nennt den Grund: „Eigene Sammlung
> `SceneDocument.ceilings` (Muster `roofs`) ⇒ Node-Union unberührt".* **Deshalb kennt `applyCommand`
> für Decken eigene Fälle und nicht `ADD_NODE`.**

**`schichten` ist vorhanden und hier nicht Gegenstand** — *`:356` sagt „Fußbodenaufbau (Feature B) —
in Feature A leer/optional; feldgleich mit `wandaufbau.Schicht`".* **Die Mengenermittlung dahinter
ist AUF-76/M0**, *als vorhanden benannt, nicht beschrieben.*

## Der Aufrufer

```text
HausplanerApp.tsx:1025  ausKontur = letzteKontur !== null && laenge >= KONTUR_MIN_PUNKTE
                 :1026  store.getState().executeCommand({ ... })
                 :1027  type: 'ADD_CEILING'
                 :1031  polygon: ausKontur ? letzteKontur : gebaeudeUmriss()
                 :1031  dickeMm: level.floorThickness
                 :1035  ...herkunftFuerNeueDecke(ausKontur)
```

> **`:1023` grenzt ausdrücklich ab:** *„Das Dach (`ADD_ROOF`, gleiche Datei) bleibt ausdrücklich
> unangetastet — das ist Z-08."*

## Die Probedaten sagen selbst, was sie umgehen

```text
studioFixtures.ts:58-61  „Der Durchbruch ist als Loch-Polygon GESETZT (Fixtures umgehen den
                          ADD_CEILING-Reducer, der es sonst aus der Lauflinie ableitet) —
                          Rechteck um den Lauf (± halbe Laufbreite), deckungsgleich zur
                          Reducer-Regel."
                 :63     function deckeTreppe(): SceneDocument
```

> ***Das ist der beste Beleg für `:298`, den es im Repo gibt*** — *jemand hat die Regel beim Bauen
> der Fixtures bemerkt und die Umgehung dokumentiert, statt sie stillschweigend auszunutzen.*

## Nachbarn — nur abgegrenzt

```text
W-05   Raumerkennung liefert das Polygon      Registerzeile nennt sie als Nachbar
W-09   die TREPPE selbst                      eigenes Blatt; hier nur ihre WIRKUNG
W-18   F-013 pruefeKontur auf dem Kontur-Weg  HausplanerApp.tsx:831
W-24   'boden'                                s. 7-GRENZEN — Vertrag ohne Oberflaeche
```
