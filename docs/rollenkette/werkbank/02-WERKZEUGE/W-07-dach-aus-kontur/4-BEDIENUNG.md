# W-07 · Dach aus Kontur — BEDIENUNG

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | Gruppe „Aufbau", Symbol Dachschräge, Beschriftung „Dach" |
| Tastenkürzel | `D` |
| Kontextmenü | Rechtsklick auf ein Geschoss → „Dach aufsetzen" |

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | Werkzeug wählen | Oberstes Geschoss wird hervorgehoben, seine Außenkontur blinkt |
| 2 | Dachform wählen | Vier Kacheln: Satteldach · Walmdach · Pultdach · Flachdach |
| 3 | Neigung eingeben | Feld mit Umschalter Grad/Prozent, Vorgabe 38°. Live-Vorschau am Modell |
| 4 | Bestätigen | Dach entsteht, Kamera schwenkt so, dass es ganz sichtbar ist |

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| Alles gut | „Dach erstellt · 4 Flächen · 112,4 m²" | sachlich |
| Neigung noch leer | „Bitte eine Dachneigung angeben" | hinweisend |
| **Einspringende Ecke** | siehe unten | **erklärend** |
| **Selbstschnitt** | siehe unten | **erklärend** |
| **Neigung zu steil** | siehe unten | **erklärend** |

## Die Absagetexte im Wortlaut

Für jeden Fall aus `7-GRENZEN.md` steht hier der Satz, den der Anwender liest.
**Diese Texte sind Teil des Auftrags, nicht Beiwerk.**

> **Einspringende Ecken**
> „Für diesen Grundriss kann noch kein Dach berechnet werden: er hat einspringende
> Ecken (bei Punkt 4 und 7). Möglich sind zurzeit nur nach außen gewölbte Umrisse.
> **Wege:** den Grundriss in zwei rechteckige Bereiche teilen und je ein Dach
> setzen — oder Flachdach wählen, das geht bei jeder Form."

> **Selbstschnitt**
> „Der Umriss überschneidet sich zwischen Wand 3 und Wand 8. Ein Dach lässt sich
> nur auf einen geschlossenen, überschneidungsfreien Umriss setzen.
> **Weg:** die beiden Wände dort begradigen — die Stelle ist rot markiert."

> **Neigung zu steil**
> „Eine Dachneigung von 85° oder mehr lässt sich nicht darstellen — das Dach wäre
> senkrecht. Übliche Neigungen liegen zwischen 15° und 50°."

> **Zu wenige Wände**
> „Für ein Dach braucht es mindestens drei Wände, die einen geschlossenen Umriss
> bilden. Zurzeit sind es 2, und der Umriss ist offen."

> **Innenhof**
> „Grundrisse mit Innenhof werden noch nicht unterstützt. Das Dach würde die
> Öffnung überdecken."

**Was diese Texte gemeinsam haben:** Sie nennen (1) was nicht geht, (2) warum,
(3) wo genau, (4) mindestens einen Weg weiter. Ein Text ohne Weg weiter ist eine
Sackgasse und keine Meldung.

## Abbruch

- **Esc** bricht ab. Kein Dach, kein halbes Dach, **kein leeres Dach-Objekt**.
- Nach Abbruch ist das Modell exakt wie vorher — auch der Auswahlzustand.

## Tastenkürzel während des Werkzeugs

| Taste | Wirkung |
|---|---|
| Esc | abbrechen |
| Eingabe | bestätigen |
| Pfeil ↑/↓ | Neigung um 1° ändern |
| Umschalt + Pfeil | Neigung um 5° ändern |
| Tab | zwischen den Dachformen wechseln |
