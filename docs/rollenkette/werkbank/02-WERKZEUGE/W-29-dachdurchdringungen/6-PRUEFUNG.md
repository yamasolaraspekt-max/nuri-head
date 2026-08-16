# W-29 · Dachdurchdringungen — PRÜFUNG

## Zwei Wächter, 82 Zusagen, alle grün — selbst gefahren

```text
auswechslung.test.ts     90 Z., 11 Zusagen
dachAusschnitt.test.ts  594 Z., 71 Zusagen
ℹ tests 82 · pass 82 · fail 0
```

> ***Das Verhältnis ist bemerkenswert und sagt etwas über den Gegenstand:*** `dachAusschnitt` *hat
> 510 Zeilen Code und 594 Zeilen Zusagen* — **mehr Prüfung als Sache.** *Bei einem Modul, das über
> „darf man hier schneiden" entscheidet, ist das die richtige Richtung.*

## Die vier Zusagen, auf die es ankommt

> ***„Kamin liegt zwischen zwei Sparren → kein Schnitt, keine Auswechslung"*** (`auswechslung:33`).
> **Das ist die Zusage, die der gebaute Weg VERLETZT** — *nicht der Test, sondern
> `dachOeffnung.ts:91`, das in genau diesem Fall `auswechslungErforderlich: true` meldet.* **Die
> Rechnung ist geprüft und die Behauptung ist ungeprüft**, *weil niemand sie mit der Rechnung
> vergleicht.*

> ***„Öffnung nahe First, die einen Sparren schneidet → prüfpflichtig, KEINE erfundenen
> Wechselhölzer"*** (`:41`). *Die zweite Hälfte des Satzes ist die wertvolle:* **das Modul erfindet
> nichts, wo es nichts weiß.** *Eine Bemessung nahe der Randzone wäre geometrisch scheinbar
> ableitbar und fachlich falsch.*

> ***„keine Doppelzählung: Funktion erzeugt nur Wechsel, KEINE Sparren-Teilstücke"*** (`:85`) und
> ***„Restflächen-Summe + Öffnung = Bruttofläche"*** (`dachAusschnitt:85`). **Zwei Bilanzzusagen,
> und beide prüfen eine EIGENSCHAFT statt eines Beispiels** — *sie fangen jede Umstellung, die
> irgendwo doppelt zählt.*

> ***„Öffnung maßhaltig: Breite=u (0.78), Höhe=v (Dachfenster=Länge 1.18); Tiefe NICHT
> verwechselt"*** (`dachAusschnitt:51`) *und* ***„Kamin: v-Ausdehnung = tiefeM (Footprint), nicht
> die Körperhöhe"*** (`:57`). **Zwei Zusagen gegen dieselbe Verwechslung** — *bei einem Dachfenster
> ist die zweite Ausdehnung seine **Länge**, bei einem Kamin sein **Grundriss**, nicht seine Höhe.*
> *`oeffnungVTiefeM` entscheidet das an einer Stelle* (`dachOeffnung.ts:52`), *und zwei Zusagen
> halten sie fest.*

## Was KEIN Wächter hält

| ungeprüft | Folge |
|---|---|
| **`auswechslungErforderlich: true`** (`dachOeffnung.ts:91`) | keine Zusage vergleicht die **Behauptung** mit der **Rechnung** — der Widerspruch ist unsichtbar |
| **`dachOeffnung.ts` als Ganzes** | **kein eigener Wächter**; es wird nur mittelbar über `dachAusschnitt` mitgeprüft |
| **der Anschluss** | dass `ADD_ROOF_AUFBAU` niemand ruft, hält keine Zusage fest |
| **`kamin`, `luefter`, `lichtkuppel`** in der Oberfläche | die Rechnung kennt sie, der Katalog nicht — ungeprüft, weil es nichts zu prüfen gibt |

> ***Die erste Zeile ist der Befund dieses Blattes in Prüfform.*** *Elf Zusagen sichern
> `analysiereAuswechslung`, und keine einzige sichert, dass ihr Ergebnis auch verwendet wird.*
> **Ein Wächter, der beides vergleicht, wäre heute rot** — *und genau deshalb gibt es ihn nicht:
> er würde einen Zustand festhalten, den bisher niemand entschieden hat.*

## Wie diese Ablesung rot werden könnte

**Nicht durch fehlenden Code.** *Sondern durch eine falsche Ablesung — und ich bin in diesem Blatt
zweimal knapp daran vorbei:*

1. *Meine erste Messung suchte nur in `toolRegistry.ts` und hätte „nichts da" ergeben.* **Die
   zweite fand fünf Dateien in `app/`.**
2. *Die zweite hätte „also doch gebaut" ergeben.* **Die dritte fand, dass `ADD_ROOF_AUFBAU` keinen
   Aufrufer hat.** *Erst die dritte Messung trägt.*

> ***Zweimal hätte ein Zwischenstand ein plausibles Blatt ergeben*** — *einmal zu pessimistisch,
> einmal zu optimistisch.* **Ort ist nicht Wirkung, und das gilt in beide Richtungen.**

**Alle Zahlen tragen ihren Bezug:** *96 / 174 / 510 Zeilen für die drei Geometriemodule, 4 / 5 / 20
Ausfuhren, 90 und 594 Zeilen für die zwei Wächter, 11 und 71 Zusagen, 82 gefahren.*
