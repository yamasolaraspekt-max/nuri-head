# W-02 · Wand zeichnen — BEDIENUNG

## Was der Anwender tut

Zwei Punkte setzen. **Der Fang (W-01) liegt darunter** und bestimmt, wo die Punkte landen.

## Was er einstellt

| Größe | Wirkung |
|---|---|
| **Stärke** | Breite des Bandes, symmetrisch zur Achse |
| **Höhe** | Extrusion nach oben |
| **Seite** | `links` oder `rechts` — bestimmt den Azimut der Normalen |

> **BERICHTIGT 21.08. — „Bezugsmaß" stand hier als etwas, das der Anwender einstellt. Er kann es
> nicht.** Der Typ `Bezugsmass` (`wandFlaeche.ts:38`) existiert, aber **`wandFlaeche.ts` hat keinen
> einzigen Produktivverbraucher** — gemessen über `resources/planner/hausplaner` ohne Tests: null
> Treffer auf `wandMengen`/`wandFlaeche` außerhalb der Datei selbst. Es gibt kein Bedienelement,
> keinen Aufruf und kein Panel. Ein Blatt, das einen Typ als Bedienung ausgibt, lässt den nächsten
> Leser glauben, die Kette sei geschlossen — dieselbe Klasse wie P7 („Ort ≠ Wirkung").
>
> Die Größe bleibt unten unter **„Was das Modul kann, sobald es angeschlossen ist"** stehen, weil
> sie fachlich richtig ist; sie steht nur nicht unter „Was er einstellt". *(Gefunden von der
> Fach-Linse Maurerhandwerk, Befund B-8; die Null-Messung von mir gegengeprüft.)*


## Was er zurückbekommt — und was nicht

**Entweder Mengen oder Meldungen.** Nie beides.

| Meldungsart | Wann |
|---|---|
| `oeffnung-ragt-hinaus` | Öffnung liegt teilweise ausserhalb der Wand |
| `oeffnungen-ueberlappen` | zwei Öffnungen überschneiden sich |
| `oeffnung-hoeher-als-wand` | Öffnung höher als die Wand |
| `schichten-dicker-als-wand` | Aufbau dicker als die Wand selbst |
| `fremde-oeffnung` | Öffnung gehört nicht zu dieser Wand |

*Belegt als `MeldungArt` in `resources/planner/hausplaner/geometry/wandFlaeche.ts:77`.*

**Jede Meldung trägt Klartext mit den beteiligten Kennungen** — *„eine Meldung ohne Bezug ist keine
Meldung"* (`wandFlaeche.ts:84`).

## Die mm-Regel

Punkte sind **ganzzahlige Millimeter**. `istGanzzahlig()` (`wallGeometry.ts:53`) prüft die
Invariante. **Wandlängen dürfen für Vergleiche Gleitkomma sein — was gespeichert wird, ist ganzzahlig.**

## Was das Modul kann, sobald es angeschlossen ist

**Heute nichts davon** — `geometry/wandFlaeche.ts` (238 Z.) wird von keiner Produktivdatei gezogen.
Die Meldungstabelle oben beschreibt richtig, was die Funktion **liefern würde**; sie beschreibt
nicht, was ein Anwender heute sieht.

| Größe | Was sie leistet | Lage |
|---|---|---|
| **Bezugsmaß** `roh` / `fertig` | wählt, ob Mengen am Rohbau- oder Fertigmaß gerechnet werden | Typ vorhanden, **keine Bedienung** |
| Mengen je Wand | Fläche und Volumen mit Öffnungsabzug | rechenbar, **kein Aufrufer** |

**Warum das hier steht und nicht als Mangel:** die Fach-Linse Maurerhandwerk hat die Rechnung als
**Massenermittlung** eingeordnet (nicht als Aufmaß) und ihr keinen Platz im Messwerkzeug-Vertrag
geben können — die fünf `measurement`-Verträge nehmen `points`, diese Funktion nimmt ein **Bauteil**.
Wohin sie gehört, ist damit eine offene Frage an den Planner, kein Bauauftrag.
