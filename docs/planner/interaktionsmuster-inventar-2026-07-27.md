# Interaktionsmuster — was der Planer heute an Gesten kennt

*Planner, 27.07.2026. Zweites der fünf offenen Papiere aus §40/§41. Es beantwortet eine Frage,
die Yama am 26.07. wörtlich gestellt hat — „Elemente, Formen, Maße ändern, und zwar mit Doppelklick
zum Beispiel auf Tür oder Fenster“ — und die ich damals konzeptionell beantwortet habe, ohne
nachzusehen, ob es die Geste überhaupt gibt.*

## 1. Die Messung

Arbeitsbaum 27.07., Zweig `auto/hausplaner-integration`, Basis `e411201c`. Gezählt über alle
`.ts`/`.tsx` des Planers ohne Testordner.

| Geste | Stellen | Bemerkung |
|---|---|---|
| Klick (`onClick`) | in den 91 Maus-Ereignissen enthalten | trägt heute fast alles |
| Ziehen (`draggable`, `onDrag*`) | **16** | Konva, im Grundriss |
| Mausrad (`onWheel`) | 1 | Zoom |
| Schweben (`onMouseEnter/Leave/Over`) | **4** | |
| **Doppelklick** (`onDblClick`, `dblclick`, `dbltap`) | **0** | |
| **Kontextmenü** (`onContextMenu`) | **0** | |
| **Rechte Maustaste** (`evt.button`) | **0** | |
| Touch / Pointer in Bauteilen | **0** | (siehe Eingabe-Papier) |

**Der Planer kennt drei Gesten: Klicken, Ziehen, Rad.** Alles andere, was ein Zeichner aus jedem
CAD-Programm mitbringt, gibt es nicht — nicht schwach, nicht halb, sondern gar nicht.

## 2. Befund A — der Doppelklick fehlt vollständig

Yamas Frage vom 26.07. hat im Code heute **keine Entsprechung**. Null Stellen. Ich habe damals ein
Bedienmodell dazu geschrieben, ohne die Null zu kennen; das Modell war deshalb nicht falsch, aber
es stand auf einer Annahme statt auf einer Messung.

Wie ändert man heute die Breite einer Tür? Es gibt **genau einen** Weg
(`HausplanerApp.tsx:2025`):

```
<input type="number" min={100} value={selectedOpening.width}
       onChange={(e) => aktualisiereOeffnung({ width: ... })} />
```

Der Ablauf für den Architekten, Schritt für Schritt: Werkzeug auf Auswahl · die Tür im Grundriss
treffen · Blick nach rechts über die volle Fensterbreite · im Panel das richtige Feld unter
mehreren finden · Zahl markieren · neue Zahl tippen · Blick zurück zum Grundriss, um zu sehen, was
passiert ist. **Sieben Schritte und zwei Blickwechsel für eine Zahl.** Im Zeichenprogramm sind es
zwei: doppeltippen, Zahl tippen.

Das ist der Unterschied, den Yama meint, wenn er sagt, es müsse einfach sein. Es geht nicht um ein
fehlendes Bedienelement, sondern darum, **wo** die Eingabe steht: heute am Rand, gewünscht am
Objekt.

## 3. Befund B — kein Kontextmenü, keine rechte Maustaste

Null Stellen. Jede Aktion an einem Objekt muss heute über die obere Leiste oder das rechte Panel
gehen. Bei 110 Werkzeugen ist das die falsche Richtung: **je mehr Werkzeuge, desto wichtiger, dass
die wenigen zum Objekt passenden am Objekt liegen** und nicht in einer Leiste, die alles trägt.

Das Kontextmenü ist dabei nicht „ein Menü mehr“, sondern der einzige Ort, an dem der Vorrat
**gefiltert** erscheint: was hier und jetzt an diesem Objekt geht. Genau die Auswahl, die
`resolveToolState` bereits berechnet und heute nur zum Ausgrauen benutzt wird.

## 4. Befund C — die Griffe werden berechnet und von niemandem gezeichnet

`app/tools/auswahlDarstellung.ts:34/67`:

```
griffe: boolean;
griffe: Boolean(e.primaer) && !e.gesperrt,
```

Die Entscheidung *„dieses Objekt darf Anfasser zeigen“* wird sauber getroffen — pro Auswahl, mit
Sperr-Berücksichtigung. **Gezeichnet wird sie nirgends.** Der Wert wird berechnet und fällt auf den
Boden. (Stand schon als Befund in der Liste; hier ist er beziffert und eingeordnet: er ist die
**halbe Antwort auf Befund A**, denn Griffe sind das Ziehen dessen, was das Zahlenfeld tippt.)

## 5. Der Musterkatalog — welche Geste welche Bedeutung bekommt

Damit nicht jeder Posten eine eigene Geste erfindet, wird die Zuordnung **einmal** festgelegt.
Das ist die eigentliche Lieferung dieses Papiers.

| Geste | Bedeutung — immer dieselbe | Gegenbeispiel (verboten) |
|---|---|---|
| **Einfachklick** | auswählen; bei aktivem Zeichenwerkzeug: Punkt setzen | nie: bearbeiten öffnen |
| **Doppelklick auf Objekt** | **in das Objekt hineingehen** — Maßeingabe direkt am Objekt | nie: löschen, nie: Werkzeug wechseln |
| **Doppelklick auf leere Fläche** | Auswahl aufheben | nie: ein Objekt anlegen |
| **Ziehen am Objekt** | verschieben | |
| **Ziehen am Griff** | Maß ändern — **dieselbe Wirkung wie die Zahl im Feld** | nie: eine zweite Rechenquelle |
| **Rechtsklick** | Kontextmenü: die für dieses Objekt **zulässigen** Werkzeuge | nie: eine Aktion sofort ausführen |
| **Mausrad** | Zoom zum Zeiger | |
| **Esc** | eine Ebene zurück (Eingabe ab, dann Auswahl ab, dann Werkzeug ab) | |

Zwei Regeln dazu, die den Katalog erst tragfähig machen:

**M1 — Eine Geste, eine Bedeutung, im ganzen Planer.** Kein Bereich darf den Doppelklick anders
belegen, „weil es hier besser passt“. Der Zeichner lernt die Geste einmal.

**M2 — Jede Geste hat einen zweiten Weg.** Was per Doppelklick geht, geht auch über das Panel; was
per Griff geht, geht auch als Zahl. **Der Griff ist die Abkürzung, nicht der einzige Weg** — sonst
wird die Fläche unbedienbar, sobald jemand ohne Maus arbeitet, und das rechte Panel wird zur
Attrappe.

## 6. Was hier **nicht** entschieden wird

- **Wie die Maßeingabe am Objekt aussieht** (Feld im Grundriss, schwebende Leiste, Bemaßung mit
  editierbarer Zahl) — das ist Gestaltung und gehört an die Token-Quelle (Weg B, AUF-38).
- **Welche Werkzeuge ins Kontextmenü kommen** — folgt aus `resolveToolState`, nicht aus einer
  neuen Liste. Eine zweite Liste wäre eine zweite Wahrheit.
- **Touch** — der Doppelklick hat auf Touch ein Gegenstück (`dbltap`), das Schweben nicht. Bleibt
  beim offenen Entscheid im Eingabe-Papier.

## 7. Probe des Erprobers

| Er tut | Heute | Soll |
|---|---|---|
| doppelklickt auf eine Tür | nichts | die Breite steht am Objekt und ist tippbar |
| doppelklickt ins Leere | nichts | die Auswahl ist weg |
| klickt eine Wand mit rechts an | Browser-Menü des Systems | die Werkzeuge, die an einer Wand gehen |
| wählt ein Fenster aus | Rahmen, keine Anfasser | Anfasser an den Kanten |
| zieht einen Anfasser auf 1010 mm | — | **dieselbe Zahl** steht im rechten Feld |

Die letzte Zeile ist die wichtigste: sie ist der Gegen-Beweis gegen die zweite Rechenquelle. Wenn
Griff und Feld je eine eigene Zahl führen, driften sie — und zwar still.

## 8. Reihenfolge

Drei Posten, in dieser Folge, **keiner davon jetzt** (§13/§14 — AUF-38 läuft, und alles hier fasst
`HausplanerApp.tsx` an):

1. **Griffe zeichnen** — der Wert wird schon berechnet, es fehlt nur der Zeichner. Kleinster
   Posten, sofort sichtbarer Gewinn, und er beantwortet Befund C und die halbe Frage von Yama.
2. **Doppelklick → Maß am Objekt** — braucht die Gestaltungsentscheidung aus Weg B, deshalb nach
   AUF-38.
3. **Kontextmenü** — braucht nichts Neues außer `resolveToolState`, ist aber der größte Schnitt
   in die Leiste; zuletzt.

Alle drei gehören nach AUF-48 (Zerlegung), nicht davor: sie würden sonst in eine Datei geschrieben,
die kurz darauf zerschnitten wird.
