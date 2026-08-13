# W-21 · Sparren und Lattung — GRENZEN

## Das Wichtigste zuerst: es ist eine VORBEMESSUNG

> *„**WICHTIG: VORBEMESSUNG** (Einfeldträger, gleichmäßige Last, senkrechte Lastkomponente).
> **Ersetzt KEINE prüffähige Statik** — dient der schnellen Querschnittsabschätzung im
> Verkauf/Planung. **Wind, Mehrfeld, Knicken, Auflagerpressung, Lastkombinationen bleiben dem
> Tragwerksplaner.**"* (`resources/planner/hausplaner/geometry/sparrenBerechnung.ts:10-12`)

**Wer eine Vorbemessung für eine Bemessung nimmt, baut ein Dach danach.** *Die Annahmen sind nicht
versteckt — sie stehen im Kopf der Datei, und sie stehen hier, weil ein Dateikopf nicht mitgeliefert
wird, wenn jemand nur die Zahl übernimmt.*

Die Grenzen im Einzelnen: **Einfeld-Sparren**, gleichmäßige Last, **nur die senkrechte
Lastkomponente** (`Math.cos`, Z.90), Schneezone als **Eingabe** statt aus dem Ort abgeleitet.

## Was das Werkzeug von sich selbst sagt: `OFFENE_HOLZBAUTEILE`

Eine **gebaute Selbstauskunft** (`resources/planner/hausplaner/geometry/holzBauteile.ts:45`) — vier Einträge, wörtlich:

```text
Mittelpfette      benoetigt Auflagerpunkte/Stuhl — in der Geometrie nicht modelliert
Schwelle          Auflagerschwelle — nicht modelliert
Wechselholz /     an Kamin/Gaube/Dachfenster: Oeffnungsraender + betroffene Sparren
Auswechslung      nicht eindeutig bestimmt
Schiftersparren   verkuerzte Sparren liegen geclippt vor, sind aber nicht eindeutig
als eigene Klasse als Schifter bestimmbar
```

Der Grund steht darüber: *„geometrisch (noch) NICHT zuverlässig vorliegend und deshalb bewusst NICHT
als echte Mengen ausgegeben — **keine erfundenen Werte**. Für die ehrliche Dokumentation in
UI/Bericht."*

**Das ist die A-10-Klasse, richtig gelöst:** *nicht das leere Ergebnis ist das Problem, sondern das
gefüllte, das seine Herkunft verschweigt.*

## `auswechslung.ts` (W-21/2, 13.08.) — was es NICHT kann

**Der Kopf sagt es in einem Satz:** *„**Keine statische Bemessung.**"* (`:19`) — *dieselbe Grenze wie
`berechneSparren()` oben, nur an einer anderen Stelle des Dachs.*

Die Grenzen im Einzelnen, alle am Code erhoben:

| Fall | was das Modul tut |
|---|---|
| Öffnung nahe **First · Traufe · Ortgang** (Vorgabe 0,3 m) | `pruefpflichtig = true`, **wechselAnzahl 0** — kein Wechselholz, obwohl eines nötig ist |
| **flankierender Sparren** links oder rechts nicht eindeutig | ebenso `pruefpflichtig` — *„Wechsel geometrisch noch nicht vollständig ableitbar"* |
| Öffnung ragt **über die Fläche hinaus** | ebenso `pruefpflichtig` |
| Fläche oder Öffnung mit Maß ≤ 0 | leeres Ergebnis, **kein Hinweis** — s.u. |
| **trapezförmige Fläche** (Walm, Schifter) | **nicht vorgesehen.** Das Raster wird aus **einer** Breite `breiteM` erzeugt; eine oben schmalere Fläche kennt das Modul nicht |
| **Querschnitt des Wechselholzes** | nie. Es liefert **Länge und Anzahl**, keine Bemessung |

> **`pruefpflichtig = true` heißt NICHT „Fehler", sondern „hier entscheidet ein Mensch".** *Es ist
> dieselbe Haltung wie `OFFENE_HOLZBAUTEILE` — lieber eine gemeldete Lücke als eine erfundene Menge.*

**Eine Grenze, die man beim Lesen übersieht — gemessen, nicht vermutet:** *bei ungültiger Fläche oder
Öffnung (`:103`, `:111`) kehrt das Modul mit dem **leeren Ergebnis** zurück, und `hinweise` bleibt
**leer**.* **Ein Aufrufer, der nur die Hinweise anzeigt, zeigt in diesem Fall nichts an** — dasselbe
Ergebnis wie „alles in Ordnung, keine Auswechslung nötig". *Der Unterschied steht nirgends im
Rückgabewert.*

## Und die Grenze, die nicht im Modul steht: es ist nicht angeschlossen

**`OFFENE_HOLZBAUTEILE` oben meldet weiterhin** *„Wechselholz / Auswechslung … nicht eindeutig
bestimmt"* — **und das bleibt richtig, obwohl es das Modul jetzt gibt.** *Gemessen:*

```text
auswechslung.ts        importiert NICHTS und wird von KEINEM Produktivcode gelesen.
                       Einziger Importeur: __tests__/auswechslung.test.ts
holzBauteile.ts        kennt auswechslung.ts nicht -> die Selbstauskunft stimmt
```

> *Das ist kein Widerspruch zwischen den zwei Blättern, sondern die genaue Lage: **die Rechnung ist
> da, der Weg zur Menge nicht.*** **Wer `OFFENE_HOLZBAUTEILE` eines Tages um diesen Eintrag kürzt,
> muss vorher den Anschluss bauen — nicht umgekehrt.**

**Und eine dritte Stelle sagt heute etwas, das das Modul widerlegen könnte, es aber nie gefragt
wird:** `dachOeffnung.ts:91` gibt **`auswechslungErforderlich: true` als festen Wert** zurück —
*einziger Rückgabepfad der Datei (`oeffnungRechteck()`, `:86`), das Feld ist als `boolean` deklariert
(`:40`), und **`false` kann dort nie herauskommen**.* Kein Import verbindet die zwei Dateien.

> **Das ist die Zwei-Wahrheiten-Klasse:** *ein Modul entscheidet sorgfältig, ob eine Auswechslung
> nötig ist — und ein anderes sagt der Oberfläche „immer ja", ohne es je zu fragen.* **Hier nur
> gemeldet: `dachOeffnung.ts` ist Produktivcode und gehört nicht zu W-21** (W-21/2 ändert keinen
> Produktivcode). *Der Befund gehört zu W-29 „Dachdurchdringungen", das heute leer ist.*

## Die Lattung — der Werkzeugname verspricht mehr, als ein Modul hält

**Es gibt kein Lattungs-Modul.** Kein Dateiname in `geometry/` enthält `latt`. Das Wort kommt an
diesen Stellen vor, und sie bedeuten **Verschiedenes**:

| Fundstelle | Bedeutung | gebaut? |
|---|---|---|
| `resources/planner/hausplaner/geometry/sparrenBerechnung.ts:63` | Lattung als **Lastanteil** (Dachdeckung + Lattung + Sparren-Eigengewicht) | **ja** |
| `resources/planner/hausplaner/geometry/holzMengen.ts:34` | *„Summe der echten **Traglatten**längen (lfm)"* — als **Menge** | **ja** |
| `resources/planner/hausplaner/geometry/holzMengen.ts:32` | *„Summe der echten **Konterlatten**längen (lfm)"* | **ja** |
| `resources/planner/hausplaner/geometry/dachformVorlagen.ts:118, 122` | `lattmassAbhaengigVonProdukt`, `konterlattungMm` als **Produktmaß** | **Daten da, außerhalb der Vorlagendatei nur von einem Test gelesen** |
| `resources/planner/hausplaner/geometry/dachWerte.ts:20` | `battenDist: 0.05` — **Mindest-Lattenabstand** als Prüfgrenze | **ja, als Schranke** |

**Was daraus folgt — und es ist genauer als „gebaut oder nicht":** Lattung als **Last** und Lattung
als **Menge** sind beide da. Was **fehlt**, ist der Schritt dazwischen: **niemand leitet den
Lattenabstand aus der Deckungsart ab**, und `konterlattungMm` wird außerhalb seiner eigenen Datei von
**keinem** Produktivcode gelesen — nur von einem Test.

*Die Mengen sind echt, weil sie aus der gezeichneten Geometrie kommen. Aber was gezeichnet wird,
entscheidet nicht dieses Werkzeug.*

## M-02 ist nicht ausgewertet

Das Register führt **M-02 (2.021 Zeilen)** als Quelle dieses Werkzeugs. **Sie ist nicht gelesen
worden** — weder für dieses Blatt noch, soweit erkennbar, für den Code.

> *Ein Blatt, das eine ungelesene Quelle als Grundlage führt, behauptet Vollständigkeit, die es nicht
> hat.* **Deshalb steht sie hier als offen und nicht im Literaturverzeichnis.**
