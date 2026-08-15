# W-14 · Kopieren, Spiegeln, Drehen — GRENZEN

## Drehen fehlt — und der Grund ist das SCHEMA, nicht die Rechnung

**Am Code erhoben:**

```text
toolRegistry.ts                 'drehen'  ->  0 Treffer
editierGeometrie.ts             keine Trigonometrie, keine Matrix
domain/scene.types.ts:193-196   transform: { position, rotation, scale }
                                -> am ObjectNode. Rotation in GRAD.
Waende                          tragen start/end. KEINE Rotation.
commands/applyCommand.ts:203    MOVE_NODE fuer undefinierte Typen
                                -> CommandAbgelehnt('node_unbekannt')
```

> ***Ein Objekt kann sich drehen, eine Wand nicht.*** *Ein `ObjectNode` hat ein `rotation`-Feld —
> drehen heißt dort: eine Zahl ändern. **Eine Wand ist durch zwei Punkte beschrieben**; drehen hieße
> dort: beide neu rechnen.*
>
> **Das ist keine fehlende Formel, sondern eine andere Art von Operation** — und deshalb ist
> „drehen" für Objekte etwas anderes als für Wände, obwohl es dasselbe Wort ist.

### Die zwei Fragen, die ein Bau beantworten müsste — als Fragen, nicht als Vorschlag

**1. Was ist der Bezugspunkt?** *Der Schwerpunkt der Auswahl? Die Bbox-Mitte? Ein vom Anwender
gesetzter Punkt? Der erste Klick?* — **Jede Antwort ergibt ein anderes Ergebnis, und keine ist
offensichtlich falsch.**

**2. Was passiert mit angedockten Öffnungen?** *Eine Öffnung hängt über `hostWallId` und
`offsetFromWallStart` an ihrer Wand. **Bei der Spiegelung stimmt das von selbst** (nachgerechnet,
siehe `2-FUNKTION`) — **bei einer Drehung um einen freien Winkel ist die Wand danach nicht mehr
achsparallel**, und was das für Anschlüsse, Gehrung und Bemaßung heißt, ist ungeklärt.*

> ***Diese zwei Fragen gehören VOR einen Bau und nicht hinein.*** *Wer sie beim Bauen nebenbei
> entscheidet, trifft eine Produktentscheidung an einer Stelle, an der niemand sie sucht.*

## Der Bezugsrahmen ist nicht überall derselbe — und das ist die zweite Grenze

```text
duplizieren · loeschen   wirken auf die AUSWAHL
spiegeln                 wirkt auf den GANZEN GRUNDRISS
verschieben              auf die gezogene Wand bzw. die Auswahl
```

> **Wer aus zwei Auswahl-Werkzeugen kommt und den Spiegel-Knopf drückt, spiegelt alles.** *Die Titel
> sagen es („Grundriss links/rechts spiegeln"), die Sperre sagt es (`waende.length === 0` statt
> Auswahl) — **aber keine Zusage hält es fest** (siehe `6-PRUEFUNG`).*

**Ob Spiegeln auf die Auswahl wirken SOLL, ist eine Produktfrage** — *hier festgehalten, nicht
entschieden.*

## Was `versetzteWand` NICHT ist

**Kein Parallelversatz.** *Sie verschiebt beide Endpunkte um denselben Vektor — die Wand wandert,
Richtung und Länge bleiben.* **Ein Parallelversatz legt eine neue Achse im senkrechten Abstand
daneben und braucht die Normale.**

> *Diese Verwechslung hat am 13.08. eine falsche Fahrplan-Einordnung und eine Zusage an Yama
> erzeugt, beide zurückgezogen (A-29).* **Das Wort trägt sie: „versetzen" und „Versatz" sind
> dasselbe Wort und zwei Vorgänge.**

*Der echte Parallelversatz liegt seit A-32 in `geometry/geradenGeometrie.parallelVersatz`.*

## Was die Spiegelung nebenbei tut

**Die Laufrichtung jeder Wand kehrt sich um** (`editierGeometrie.ts:41-43`, im Doc-Kommentar
benannt).

> ***Das ist harmlos — und mehr noch: es ist der Grund, warum Öffnungen richtig mitwandern.***
> *Nachgerechnet in `2-FUNKTION`: `start` und `end` werden an ihrem Platz gespiegelt statt
> vertauscht, deshalb misst `offsetFromWallStart` weiter vom richtigen Ende.*

## F-032 stand in der Registerzeile und trägt nicht

**Berichtigt mit W-14/1** — *keine Matrix, keine Trigonometrie; sechs `Math.round`, zwei `Math.max`,
zwei `Math.min`.* Siehe `3-FORMELN`.

> *F-032 hat den **Gegenstand** getroffen und die **Bauform** verfehlt.* **Eine Drehung wäre die
> erste Operation dieses Werkzeugs, für die sie in Frage käme** — und die gibt es nicht.

## Nachbarschaft, richtiggestellt

**`bbox` und `achsenMitte` liegen in derselben Datei, dienen aber auch W-12** (`einpassen.ts:21`).
*Der alte Nachtrag „brauchen BEIDE (W-13 und W-14)" ist berichtigt:* **W-13s Auswahl-Module
importieren `editierGeometrie` NULL Mal** — selbst gemessen über `auswahlModus`,
`auswahlDarstellung`, `auswahlUebersicht` und `trefferSuche`.
