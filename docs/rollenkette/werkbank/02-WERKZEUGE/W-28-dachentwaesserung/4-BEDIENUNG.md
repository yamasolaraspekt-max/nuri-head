# W-28 · Dachentwässerung — BEDIENUNG

## Es gibt nichts zu bedienen

```text
toolRegistry.ts    rinne 0 · entwaesser 0 · Entwaesser 0 · regen 0 · Regen 0
werkzeugPaket.ts   dieselben fuenf Schreibweisen:  0
```

**Kein Eintrag, kein Knopf, kein Schritt.** *Fünf Schreibweisen geprüft, damit die Null nicht die
Schreibweise misst* (H-9).

## Was der Anwender heute statt dessen sieht

**Nichts, was auf Entwässerung hindeutet.** *Die Dachrinne existiert nur im Typ; die Oberfläche
zeigt weder ein Linienbauteil-Werkzeug noch eine Liste, in der sie vorkommen könnte.*

> ***Das ist die ehrlichste Lage, die ein leeres Werkzeug haben kann:*** *nichts verspricht etwas.*
> **Ein halb sichtbarer Eintrag wäre schlechter als gar keiner** — *er lädt zum Klicken ein und
> führt in eine Rechnung, die es nicht gibt* (A-03: eine Oberfläche, die Erwartungen weckt, die der
> Kern nicht hält).

## Der Weg, den eine spätere Bedienung nehmen müsste

**Damit niemand ihn neu suchen muss, hier die gemessene Kette bis zum ersten fehlenden Glied:**

```text
W-07  Dach aus Kontur      liefert die Dachflaeche          GEBAUT
      polygonFlaeche       liefert m²                        GEBAUT
      Traufenlage          v = 0 in DachLinienBauteil        GEBAUT (Typ)
   -> Einzugsflaeche je Traufe                               FEHLT
   -> Regenspende am Ort                                     FEHLT
   -> Querschnitt / Anzahl Fallrohre                          FEHLT
   -> Darstellung an der Traufe                               FEHLT
```

> **Die ersten drei Glieder stehen.** *Das vierte ist der Punkt, an dem ein Bau anfinge* — **und es
> ist Geometrie, nicht Hydraulik:** *welche Traufenabschnitte entwässern in welche Rinne.*

## Bedienungs-Vorbild im Haus, falls gebaut wird

**`app/dashboard/enginePanels.ts:30` zeigt, wie eine Rechnung ohne eigenes Werkzeug bedienbar
wird:** *sie erscheint als Rechenpanel — Eingabefelder, Knopf, Ergebnisblock, Prüfliste*
(`app/EngineFlaeche.tsx`, dort im Kopf beschrieben als *„bewusst schlicht"*).

> ***Für eine Bemessung ist das der richtige Ort und nicht die Zeichenfläche:*** *der Anwender
> zeichnet keine Dachrinne, er lässt sie bemessen.* **W-28 wäre damit ein Panel-Werkzeug wie das
> Abwasser-Gefälle und kein Zeichenwerkzeug** — *eine Einordnung, die vor dem ersten Kriterium
> getroffen werden sollte, weil sie den ganzen Zuschnitt bestimmt.*
