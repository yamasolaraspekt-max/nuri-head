# W-25 · Pfetten und Kehlbalken — BEDIENUNG

## Es gibt nichts zu bedienen — und das ist hier kein Mangel

```text
toolRegistry     'pfette' 0 · 'kehlbalken' 0
werkzeugPaket    'pfette' 0 · 'kehlbalken' 0
```

> ***W-25 ist kein Zeichenwerkzeug.*** *Niemand „setzt eine Pfette" — sie entsteht, wenn ein Dach
> entsteht.* **Was der Anwender hier bekommt, ist eine ZAHL im Bericht, kein Knopf in der Leiste.**

## Wo die Zahl ankommt

```text
app/tools/faehigkeiten.ts:86
  { id: 'engine-holzbauteile', label: 'Holz-Bauteile (BOM)',
    gruppe: 'dach-zimmerei', art: 'engine', zustand: 'in_entwicklung' }
```

> **Die Fähigkeit ist angemeldet und trägt den Zustand `in_entwicklung`** — *dieselbe Gruppe
> `dach-zimmerei` führt daneben `engine-holzmengen` (:85) und `engine-schifter` (:87), beide
> ebenfalls `in_entwicklung`.* **Drei Engines derselben Familie, alle als unfertig ausgewiesen.**

## Was der Anwender vom Ehrlichkeitsteil sieht

**`OFFENE_HOLZBAUTEILE` ist exportiert, damit die Oberfläche es anzeigen kann** — *der Kopf sagt
es wörtlich:* „Für die ehrliche Dokumentation in UI/Bericht."

> ***Ob es irgendwo angezeigt wird, habe ich NICHT gemessen*** — *das liegt außerhalb der Insel, im
> Bericht.* **Als offene Stelle benannt statt behauptet.**

## Und was er dabei Falsches liest

*Zwei der vier Einträge sind überholt* (`1-ZWECK`): **Wechselholz** *rechnet `auswechslung.ts`,*
**Schifter** *benennt `schifterListe.ts`.*

> **Der Anwender liest „nicht ermittelbar" über zwei Posten, die zwei geprüfte Module ermitteln.**
> *Eine Untertreibung ist billiger als eine erfundene Zahl — aber sie kostet trotzdem: sie führt zu
> einer Nachfrage, die niemand mehr beantworten muss.*
