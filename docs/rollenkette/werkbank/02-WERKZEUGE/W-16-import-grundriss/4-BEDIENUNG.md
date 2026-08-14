# W-16 · Grundriss unterlegen — BEDIENUNG

## Die Rubrik in der Werkzeugleiste

**Überschrift „Referenzunterlage"** (`UnterlagenWerkzeuge.tsx:173`). *Der Name sagt, was das Bild
ist: eine **Referenz**, keine Zeichnung.*

## Schritt 1 — Bild hochladen

**Titel des Knopfs** (`:181`): *„PDF oder Bild als Referenz unter die Zeichnung legen"*

> **Der Titel nennt beide Dateiarten und den Ort.** *„Unter die Zeichnung" ist die wichtigste
> Angabe: die Unterlage kommt nicht davor und verdeckt nichts.*

**Danach steht die Herkunft da** (`:191`) — *der ursprüngliche Dateiname als Titel, damit man
mehrere Uploads auseinanderhält.*

## Schritt 2 — Maßstab setzen

**Der aktuelle Maßstab ist immer sichtbar** (`:198`): *„Maßstab: `x.xxx`"* — auf **drei**
Nachkommastellen.

**Der Kalibrier-Knopf, Titel** (`:206`): *„Zwei Punkte auf der Unterlage anklicken, deren echte
Länge bekannt ist"*

> ***Der Titel erklärt das Verfahren, nicht den Knopf.*** *„Kalibrieren" allein sagt einem
> Bauzeichner nichts über die zwei Klicks; dieser Satz schon.*

**Der Ablauf:**

```text
1  Knopf druecken
2  ZWEI Punkte auf der Unterlage anklicken   — eine Strecke, deren Mass man kennt
3  die echte Laenge eingeben                 — in mm
4  berechneMassstab(...)  ->  PUT …/massstab  ->  gespeichert
```

**Warum genau zwei Punkte und nicht ein Rahmen:** *eine Strecke reicht — der Maßstab ist ein
Verhältnis, keine Verzerrung. Ein Rahmen würde vier Freiheitsgrade anbieten, von denen drei nicht
gebraucht werden.*

## Was der Anwender NICHT tun kann — und es ist gesichert

| nicht möglich | Beleg |
|---|---|
| die Unterlage **anklicken** oder auswählen | `listening={false}` am Bild, verriegelt in `unterlage.test.ts` (K-03) |
| die Unterlage **verschieben** | kein Klick-Handler, ebenfalls verriegelt |
| aus ihr **Wände ableiten** | keine Erkennung — sie ist eine Vorlage zum Nachzeichnen |

> ***Die Unterlage ist bewusst „tot".*** *Sie liegt unter allem, reagiert auf nichts und rührt weder
> Befehle noch Auswahl noch das Modell an — vier eigene Zusagen halten das fest (siehe
> `6-PRUEFUNG`).*

## Was passiert, wenn etwas schiefgeht

**Der Verarbeitungsstand kommt vom Server** (`…/status`) und wird angezeigt statt verschluckt:

```text
importDienstNoetig   -> ein ERKLAERENDER SATZ, keine leere Flaeche   (K-06)
fehler               -> wird GENANNT, nicht verschluckt              (K-06)
verarbeitet          -> kein Hinweis noetig
```

> **Ein PDF muss erst klassifiziert und gerastert werden.** *Solange das nicht passiert ist, steht
> da, warum noch kein Bild erscheint — nicht eine leere Fläche, die wie ein Fehler aussieht.*

**Und ein halber Datensatz wird verworfen, nicht halb angezeigt** (`leseUnterlage`) — *fehlt ein
Pflichtfeld, gilt die Unterlage als nicht vorhanden.* **Lieber nichts als etwas Halbes.**

## Abbruch

**Die Kalibrierung lässt sich abbrechen, ohne etwas zu ändern** — *erst der `PUT` auf `…/massstab`
schreibt.* **Und `berechneMassstab` liefert `null` statt eines unsinnigen Werts**, wenn die Eingabe
nicht auswertbar ist (siehe `7-GRENZEN`).
