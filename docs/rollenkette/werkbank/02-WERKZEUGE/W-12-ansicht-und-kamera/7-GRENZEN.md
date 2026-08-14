# W-12 · Ansicht und Kamera — GRENZEN

## Das Wichtigste zuerst: es gibt KEIN Werkzeug, und es soll keines geben

**Die Registerzeile führt W-12 als `LEER`. Wer das als „nichts gebaut" liest, liest es falsch** —
alle vier Gegenstände sind gebaut (siehe `2-FUNKTION`). **Was fehlt, ist ein Registry-Eintrag, und
er fehlt zu Recht.**

**Belegt über die ZUGRIFFSART, nicht über eine Zahl:**

```text
Registry-Eintraege mit der ID 'ansicht' / '2d' / '3d' / 'split'
  in app/tools/toolRegistry.ts                                    KEINER

dieselben Woerter als WERTE von supportedViews
  in app/tools/toolRegistry.ts                                    NEUN Eintraege
```

> ***Dieselben Wörter, zwei völlig verschiedene Rollen.*** *Als **ID** wäre die Ansicht ein Werkzeug,
> das man anklickt. Als **Wert von `supportedViews`** ist sie eine **Eigenschaft**, an der sich
> Werkzeuge ausrichten — „dieses Werkzeug gibt es in 2D".*

**Die Ansicht ist die Bedingung, unter der Werkzeuge erscheinen — sie ist keines von ihnen.**
*Dieselbe Lage wie W-01s Fang, dessen Blatt sagt, er liege **unter** anderen Werkzeugen und sei
keines.*

> **Ohne diesen Satz liest die nächste Rolle `LEER` als Auftrag** und baut einen Registry-Eintrag,
> der nichts kann außer den Zustand zu setzen, den drei Knöpfe schon setzen.

### Die Zahlen dazu, je mit ihrem Träger

*Eine nackte Zahl trägt hier nichts — dieselbe Zeichenkette bedeutet je nach Datei etwas anderes:*

| Zahl | Träger |
|---|---|
| **12** | `supportedViews` in `app/tools/toolRegistry.ts` |
| **75** | `supportedViews` im **ganzen** Hausplaner (der Rest liegt überwiegend im stillgelegten Katalog) |
| **9** | `supportedViews`-Einträge in `toolRegistry.ts`, die `'2d'` oder `'split'` als **Wert** führen |
| **0** | Registry-Einträge mit `'ansicht'`/`'2d'`/`'3d'`/`'split'` als **ID** |

## Die Frage aus W-01, hier beantwortet

**`W-01-fang-beschreiben.md:94` verweist die Frage „wird das sichtbare Raster gezeichnet?"
ausdrücklich hierher.** *Antwort: **ja, in beiden Ansichtsschichten** — mit der ganzen Kette:*

```text
3D   szene.ts:212-215                 GridHelper(80, 80, …), an die Szene gehaengt
2D   HausplanerApp.tsx:349            rasterAn = useState(true)
                     :1261-1269       rasterLinien werden erzeugt
                     :1337 :1409      durchgereicht
     Kopfrahmen.tsx:304               der Knopf
     Buehne.tsx:146                   {rasterAn && rasterLinien}   <- hier wird gezeichnet
```

> ***`Buehne.tsx:62` beantwortet die Frage NICHT*** — *dort steht `rasterAn: boolean;`, eine
> Props-Typzeile.* **H-8: der Ort ist nicht die Wirkung.** *Ein Typeintrag sagt, dass etwas
> übergeben werden **kann**, nicht dass etwas gezeichnet **wird**.*

**Und „beide Renderer" wäre falsch:** *`renderers/` enthält nur `three-d/`.* **Die 2D-Seite ist die
Konva-Bühne in `app/rahmen/` — es ist eine Unterscheidung von SCHICHTEN, nicht von Renderern.**

## Was das Werkzeug nicht kann

| Grenze | Beleg |
|---|---|
| **Die Kameralage überlebt keinen Neuaufbau** — sie liegt in `szene.ts` und in keinem Dokument | `szene.ts:100`/`:178` |
| **Kein Undo für Ansicht, Raster oder Kamera** — kein Dokumentzustand | `hausplanerStore.ts:126`, `set({ modus })` ohne Patch |
| **Keine Kamera in 2D** | dort Zoom und Verschub der Bühne, keine Kamera |
| **Keine eigene Kamerarechnung** | `OrbitControls` aus `three` (`szene.ts:23`) |
| **Rasterweite nicht einstellbar** | folgt dem Zoom (`HausplanerApp.tsx:1262-1263`) |
| **Keine Beleuchtung, keine Verschattung** | W-19 |

## Der Hygiene-Posten, benannt und nicht angefasst

**`app/state/uiState.ts:11`:** *„(Rename `modus→viewMode` ist ein eigener Hygiene-Slice.)"*

> **Dieses Blatt nennt ihn und ändert nichts.** *Die Umbenennung berührte den Modell-Store, die
> Activation-Engine und jeden Leser — eigener Vorgang, eigene Reichweite.*

## Ungeprüft — mit Absicht hier und nicht in `6-PRUEFUNG`

**Für die Kamera, den `GridHelper` und die 2D-Zeichenstelle gibt es keinen Wächter.** *Ein Bruch
fiele erst im Bild auf.* **Und die Verwechslung der zwei `setModus` ist durch nichts ausgeschlossen
außer durch die Typen** — *`setModus('guided')` auf dem Modell-Store bricht `tsc`, weil `'guided'`
kein `HausplanerModus` ist.* **Das trägt nur, solange die zwei Wertemengen sich nicht überschneiden.**
