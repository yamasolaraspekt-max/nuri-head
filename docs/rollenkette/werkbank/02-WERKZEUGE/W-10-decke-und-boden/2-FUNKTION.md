# W-10 · Decke und Boden — FUNKTION

> **Ablesung des vorhandenen Codes, nicht Vorgabe.** *Jede Zeilenangabe ist einzeln geöffnet worden;
> keine Zahl ist aus dem Auftragsblatt übernommen.*

## Der Weg vom Knopf bis zur gespeicherten Decke

```text
1  KNOPF        toolRegistry.ts:132  id 'decke', shortcut 'K' (:139),
                bauteilKind 'ceiling' (:140), Ansichten ['2d','split'] (:138)

2  AUFRUF       HausplanerApp.tsx:1026  store.executeCommand({ type:'ADD_CEILING', … })
                :1025  ausKontur = letzteKontur !== null && laenge >= KONTUR_MIN_PUNKTE
                :1031  polygon: ausKontur ? letzteKontur : gebaeudeUmriss()
                :1031  dickeMm: level.floorThickness
                :1035  herkunftFuerNeueDecke(ausKontur)

3  REDUCER      applyCommand.ts:288  case 'ADD_CEILING'
                :290  Level unbekannt      -> CommandAbgelehnt 'level_unbekannt'
                :293  draft.ceilings fehlt -> leeres Feld anlegen (Migration ist Lade-seitig)
                :296  pruefeDeckeProLevel
                :298  Oeffnungen mitgegeben ? diese : treppenDurchbrueche(...)
                :299  gespeichert = { ...ceiling, oeffnungen, createdAt, updatedAt }
                :300  pruefeDeckeGanzzahlig(GESPEICHERT)
                :301  push

4  DARSTELLUNG  deckenMesh.ts  reine Kennwerte, kein three/WebGL (Dateikopf :1-5)
```

> ***Die Reihenfolge auf `:298` bis `:300` ist der tragende Punkt dieses Werkzeugs.*** *Erst werden
> die Durchbrüche eingesetzt,* **dann** *wird auf Ganzzahligkeit geprüft — also auf dem Ergebnis der
> Automatik und nicht auf der Eingabe.* **Wer die zwei Zeilen tauscht, prüft etwas, das noch nicht
> existiert.**

## Die Aussparung selbst: `treppenDurchbrueche`, Rumpf geöffnet

```text
applyCommand.ts:119  function treppenDurchbrueche(draft, levelId): CeilingOeffnung[]
              :121   for (const n of draft.nodes)
              :122   if (n.type !== 'object') continue
              :123   if (n.objectType !== 'stair' || n.levelId !== levelId) continue
              :124   const tp = parametereZuTreppe(n.parameters)
              :126   dx = tp.endX - tp.startX ,  dy = tp.endY - tp.startY
              :127   len = Math.hypot(dx, dy) || 1
              :128   nx = -dy / len ,  ny = dx / len          // Normale zur Lauflinie
              :129   h  = tp.laufbreite / 2
              :130   p(x,y) = { x: Math.round(x), y: Math.round(y) }
              :131-136  VIER Punkte:  start ± n·h  und  end ± n·h
```

> **`|| 1` auf `:127` ist der Grenzfall im Rumpf**: *eine Treppe mit `start === end` hätte `len = 0`
> und würde durch null teilen.* **Der Ersatzwert 1 liefert dann eine entartete, aber endliche
> Normale** — *keine Absage, kein `NaN`.*

## Die zwei Regeln, die Aufrufer treffen

```text
(a) EINE DECKE PRO LEVEL      applyCommand.ts:296  pruefeDeckeProLevel
                              UND :315 erneut in UPDATE_CEILING —
                              „falls levelId geaendert wurde" (Kommentar dort)
(b) GANZZAHLIGKEIT (mm)       applyCommand.ts:300  auf dem GESPEICHERTEN Knoten
                              :316 in UPDATE ebenso
```

> ***Beide Prüfungen stehen in ADD und in UPDATE*** — *das ist kein Doppel, sondern die Antwort auf
> denselben Angriffsweg von zwei Seiten:* **eine zweite Decke kann auch dadurch entstehen, dass man
> eine vorhandene auf ein fremdes Level umhängt.**

## `UPDATE` und `REMOVE` — kurz, weil sie kurz sind

```text
:305 UPDATE_CEILING   Knoten unbekannt -> CommandAbgelehnt 'decke_unbekannt' (:311)
                      :313 Object.assign(ceiling, command.changes)
                      :314 updatedAt
                      :315/:316 beide Pruefungen erneut
:320 REMOVE_CEILING   :324 vorher = laenge  ->  :325 filter auf id
```

## Etagen-Stapel: eine Ableitung, kein zweiter Rechenweg

```text
deckenMesh.ts:32  naechsteEtageElevationMm(level, decke)
             :33  deckeDickeMm = decke ? decke.dickeMm : level.floorThickness
             :34  Math.round(elevation + defaultWallHeight + deckeDickeMm)
```

> **Der Dateikopf sagt den Grund** (`:4`): *„die EINE Etagen-Stapel-Ableitung (eine Wahrheit, kein
> zweiter Rechenweg)".* **Und der Rückfall ist benannt statt geraten** (`:29-30`): *fehlt die Decke,
> gilt `level.floorThickness` — „dokumentierter Rückfall, kein Rateswert der Höhe selbst".*
