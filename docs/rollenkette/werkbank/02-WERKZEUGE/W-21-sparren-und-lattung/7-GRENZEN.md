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
