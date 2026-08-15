# W-18 · Topologie prüfen — GRENZEN

## Grenze (a) — es gibt KEIN Werkzeug „Topologie prüfen", und es soll keines geben

**Die Prüfung wirkt beim Zeichnen, nicht auf Knopfdruck.** *`HausplanerApp.tsx:31` bindet vier
Symbole ein; `:30` nennt den Grund: „die Konturprüfung ist reine Geometrie und wohnt dort, nicht
hier."*

> **Ein nachträglicher Gesamtbefund wäre eine zweite Wahrheit neben einer Prüfung, die schon
> verhindert, dass der Fehler entsteht.** *Wer eine sich selbst schneidende Kontur gar nicht
> schließen kann, braucht keinen Bericht darüber, dass er es getan hat.*

**Was das NICHT beantwortet:** *ob der Anwender eine Übersicht über seinen ganzen Grundriss braucht
— Wände ohne Anschluss, Räume ohne Abschluss.* **Das ist eine Produktfrage und keine Ablesung; hier
ausdrücklich offen gelassen.**

## Grenze (b) — F-004 ist ein Gehrungsdetail

```text
wallGeometry.ts:62    „Gehrung (mitered): die beiden Bandkanten werden bis zum
                       Schnittpunkt verlaengert"
              :106    „Liefert die beiden Schnittpunkte der Bandkanten (Halbdicke h)"
```

> ***Der Schnittpunkt entsteht dort zwischen den RÄNDERN zweier Wandbänder*** — damit die Ecke auf
> Gehrung steht. **Mit „schneidet sich diese Kontur selbst" hat er nichts zu tun.**

**Wer eine Topologie-Funktion namens `achsenSchnitt` oder `schnittZweierGeraden` sucht, findet
nichts** — *beide Muster: null Treffer.* **Und `kontur.ts` importiert aus `wallGeometry` gar
nichts** (`:39`, einziger Import: `signierteFlaeche`).

### Seit A-32 gibt es F-004 doch in reiner Form — und es ändert hier nichts

```text
geometry/geradenGeometrie.ts:84   geradenSchnitt(...)   seit A-32, 13.08.
Produktivverbraucher                                     KEINER
kontur.ts benutzt es                                     NEIN
```

> **Die Konturprüfung rechnet ihren Streckenschnitt weiterhin selbst.** *Damit bleibt der Satz
> richtig: F-004 ist für W-18 kein Bestandteil — weder als Gehrungsdetail noch als reine Formel.*
>
> *Ob `schneidetSichSelbst` eines Tages auf `geradenSchnitt` umgestellt werden sollte, ist eine
> eigene Frage. **Sie wäre nicht trivial:** ein Streckenschnitt ist mehr als ein Geradenschnitt — er
> braucht zusätzlich die Prüfung, ob der Schnittpunkt **innerhalb beider Strecken** liegt, und den
> kollinearen Überlappungsfall, den `kontur.test.ts` ausdrücklich verriegelt.*

## Grenze (c) — der offene Posten: Treppe ohne Zielgeschoss

**Wörtlich aus `W-09:207-208`:**

> *„Treppe ohne Zielgeschoss → gehört zu **W-18** (Topologie), nicht hierher. Yama hat W-18
> ausdrücklich behalten."*

> ***Er ist NICHT gebaut, und der Grund ist strukturell:*** *die Konturprüfung sieht eine
> **Punktfolge** und kein **Geschoss**. Sie kennt `{x, y}` und sonst nichts — `KonturPunkt` hat zwei
> Felder.*
>
> **Eine Treppe ohne Ziel ist keine Frage an eine Punktfolge, sondern an den Geschoss-Stapel.**
> *Wer sie hier bauen wollte, müsste dem Prüfmodul das Dokument geben — und damit genau die
> Schichtung aufheben, die `HausplanerApp.tsx:30` begründet.*

**Hier festgehalten, damit der Posten nicht zwischen W-09 und W-18 verschwindet.**

## Was das Modul sonst nicht tut

| Grenze | Beleg |
|---|---|
| **keine Reparatur** — es sagt, was nicht geht, und rückt keinen Punkt | `pruefeKontur` gibt ein Urteil, keine Punkte |
| **keine Toleranz** für benachbarte Kanten — sie werden **ausgenommen**, nicht geduldet | `:118` |
| **kein Zustand, kein Undo** | der Prüfung ist kein Befehl nachgelagert |
| **keine Wände, keine Räume** | einziger Import: `signierteFlaeche` |

> **Und ein Dreieck kann sich nicht selbst schneiden** (`:111`, `n < 4 → false`). *Das ist keine
> Abkürzung, sondern eine Aussage: unterhalb von vier Punkten ist die Frage sinnlos, und die
> Reihenfolge in `pruefeKontur` fängt den Fall vorher mit einem brauchbareren Satz ab.*
