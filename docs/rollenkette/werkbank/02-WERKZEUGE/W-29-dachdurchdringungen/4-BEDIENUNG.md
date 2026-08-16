# W-29 · Dachdurchdringungen — BEDIENUNG

## Der Anwender kann nichts davon bedienen — und die Messung dazu musste ich zweimal machen

**Erste Messung, zu eng:**

```text
toolRegistry.ts   'dachfenster' 0 · 'kamin' 0 · 'luefter' 0 · 'lichtkuppel' 0 · 'gaube' 0
```

**Zweite Messung, weiter gefasst — und sie findet etwas:**

```text
app/tools/  'dachfenster' und 'gaube' in FUENF Dateien:
   werkzeugPaket.ts:147/:151   Katalogeintraege mit Label und Icon
   werkzeugVertrag.ts          Vertragszeilen
   werkzeugThemen.ts           Themen-Bindung
   toolPresentation.ts         Zonen-Regel
   werkzeugLandkarte.ts:172    { werkzeugId: 'dachfenster', marke: 'deckt',
                                 begruendung: 'ADD_ROOF_AUFBAU' }
                       :176    dasselbe fuer 'gaube'
```

> ***Hätte ich nach der ersten Messung „kein Werkzeug, nichts da" geschrieben, wäre das Blatt
> falsch gewesen*** — *und zwar auf die bequeme Art: die enge Suche bestätigt die einfache These.*
> **Die Landkarte sagt sogar `marke: 'deckt'`, also „vom Modell abgedeckt".**

## Aber die dritte Messung entscheidet, und sie dreht das Ergebnis wieder um

```text
ADD_ROOF_AUFBAU  existiert im Schema      commands.types.ts:24
                 existiert im Reducer     applyCommand.ts:332-341
                 PRODUKTIVE AUFRUFER      NULL
```

> ***Der Befehl ist gebaut und wird von niemandem ausgelöst.*** *Die einzigen zwei Treffer außerhalb
> von Schema und Reducer sind die beiden **Begründungstexte** in der Landkarte — also die Stelle,
> die von sich behauptet, das Werkzeug sei gedeckt.*
>
> **Damit ist `marke: 'deckt'` wahr und irreführend zugleich:** *das MODELL deckt es — es gibt einen
> Befehl, der einen Dachaufbau anlegt.* **Die BEDIENUNG deckt es nicht** — *niemand ruft ihn.*
> *Dieselbe Klasse wie mein eigener Befund vom 15.08. bei `deckenMesh`:* **Ort ist nicht Wirkung.**

## Was der Anwender heute tun kann

| | |
|---|---|
| **ein Dachfenster setzen** | nein — kein Registry-Eintrag, kein Aufrufer |
| **eine Gaube setzen** | nein — dasselbe |
| **einen Kamin, Lüfter, eine Lichtkuppel** | nein — nicht einmal im Katalog |
| **die Auswechslung sehen** | nein — `analysiereAuswechslung` hat keinen Produktivaufrufer |

> **Drei der fünf Durchdringungsarten, die im Titel des Werkzeugs stehen, kommen im ganzen
> `app/`-Verzeichnis nicht vor** — *`kamin`, `luefter` und `lichtkuppel` je 0 Dateien.* **Die
> Rechnung kennt sie** (`istEinfacheDurchdringung(art)`, `dachAusschnitt.ts:269`), *der Katalog
> nicht.*

## Was ein Bau zuerst beantworten müsste — als Fragen, nicht als Vorschlag

**1. Wo wird die Durchdringung gesetzt — auf der Fläche oder im Grundriss?** *Die Rechnung arbeitet
in Flächenkoordinaten `u`/`v` mit `xRel`/`yRel` im Band `[0,1]`* (`dachOeffnung.ts:66-67`). **Ein
Klick im 2D-Grundriss müsste erst auf eine Dachfläche projiziert werden** — *das ist eine eigene
Rechnung, und sie existiert nicht.*

**2. Was sieht der Anwender vom dreiwertigen Status?** `'sicher' | 'teilweise' | 'pruefpflichtig'`
*(`dachAusschnitt.ts:32`).* **Die dritte Antwort ist die wichtige und die schwerste zu zeigen** —
*sie heißt „das kann die Geometrie hier nicht entscheiden" und darf nicht wie ein Fehler aussehen.*

**3. Und die Frage, die aus dem Befund folgt:** *soll `auswechslungErforderlich` weiterhin `true`
sein, oder soll `analysiereAuswechslung` angeschlossen werden?* **Das ändert eine Auskunft über
Statik** — *siehe `7-GRENZEN`; es ist keine Ablesefrage.*
