# AUF-38 Scheibe 3 — Auftragsblatt (Planner, 27.07.)

**Gegenstand:** `resources/planner/hausplaner/app/FachFlaeche.tsx`.
**Spur A.** **Heimat-App: ticket.** **Ein Bauender, ein Posten** (§13).

Dieses Blatt ergaenzt `generator-auftrag-auf38-inline-styles.md` — es ersetzt es nicht. Es steht
hier, weil Scheibe 3 am 26.07. **ohne Codeaenderung zurueckgegeben** wurde und der Grund im Auftrag
gefehlt hat.

---

## 1 · Probe des Erprobers

> *Er oeffnet die Fachplaner-Flaeche und sieht dieselbe Ansicht wie vorher — kein Pixel verschoben,
> und der Schwebezustand reagiert weiter.*

## 2 · Warum diese Scheibe zurueckkam, und was sich geaendert hat

`FachFlaeche` ist ein **Dialog**. Der K9-Beleg des Postens (*„sieht die Seite exakt aus wie
vorher?"*) verlangt Bildschirmfotos **mit geoeffnetem Dialog** — headless rendert die Insel nicht.
Der Generator hat die Scheibe deshalb zurueckgelegt, statt die Wertgleichheit zu behaupten.
**Das war richtig.**

**Der Weg ist inzwischen gemessen** — vom Evaluator, ohne zu bauen (`3cc9a018`, 26.07., 23:44):
headful rendert die Insel (Canvas + 3 Reiter), `FachFlaeche` geht auf.

> **Der headful-Lauf gehoert damit in diesen Auftrag und nicht in die Ueberraschung.**
> Der Generator baut; der Evaluator faehrt die headful-Sichtprobe wie bei 55.1/56.1.

## 3 · Die Grenze (wie in Scheibe 2, unveraendert)

**Ziel ist null *statische* Inline-Stile, nicht null Inline-Stile.**

| wandert in die Stilschicht | bleibt inline |
|---|---|
| konstante `React.CSSProperties`-Objekte | alles aus dem **Zeiger** (`hover`) |
| Farben als `--hp-*` aus `T` | alles aus einem **Zustand** (aktiv, dominant, gesperrt) |
| | alles aus einer **Messung** (berechnete Groessen, Positionen) |

**Ein Zustand, der in die CSS wandert, ist eingefroren — und das faellt in keinem Gate auf**,
solange niemand den Zustand umschaltet. Deshalb: **testverriegelt in beide Richtungen**, wie in
Scheibe 2 (der Schwebezustand steht weiter inline; weder `hover` noch der aktive Zustand sind in
die CSS gewandert).

## 4 · Keine Zahl als Bedingung

Der Auftrag nennt **bewusst keine Anzahl von Stellen.**

*Begruendung, und sie ist ein eingestandener Fehler:* fuer Scheibe 2 stand „20 Stellen" im Auftrag,
gemessen waren es 34 — AUF-56 und AUF-66 hatten dazugelegt. **Eine Zahl im Auftrag ist eine Messung
zum Zeitpunkt des Schreibens, keine Abnahmebedingung.** Im selben Bericht mussten zwei geerbte
Zusagen nachgezogen werden, die eine *Anzahl* festnagelten statt einer Eigenschaft — derselbe
Bautyp, zum dritten und vierten Mal.

**Also:** wie viele es sind, wird **gemessen und berichtet**. Zur Orientierung, nicht als Vorgabe:
`grep -c "style={{"` liefert heute **27**, `React.CSSProperties` **2**. Weicht die Messung ab, ist
das ein Befund und kein Fehler.

## 5 · Abnahmekriterien

Die des Hauptauftrags (§5, je Scheibe) gelten unveraendert. Drei stehen hier noch einmal, weil sie
diese Scheibe tragen:

1. **K4 — keine rohen Farbwerte** in der CSS-Quelle **und** in der gebauten Datei; jede benutzte
   `--hp-*`-Variable existiert wirklich in `T`.
2. **K9 — headful, mit geoeffnetem Dialog**, drei Viewports, Bildschirmfotos sha256-verglichen.
   *Ein sichtbarer Unterschied waere hier ein Fehler, kein Fortschritt.*
3. **Grenze belegt:** ein Test haelt fest, dass die zustandstragenden Stile **inline geblieben**
   sind — nicht nur, dass die statischen gewandert sind.

**Gegen-Beweis (K7/K8):** Variablenwert verfaelschen ⇒ rot. Und: eine rohe Farbe in die CSS setzen
⇒ „jede Farbe eine Variable" rot.

## 6 · Grenzen des Postens

`store/` · `domain/` · `renderers/` · `geometry/` — **null Zeilen**.
Kein `!important`, keine Medienabfrage (responsive ist L7), **keine Strukturaenderung**: kein `div`
kommt dazu, keins faellt weg.
**Bundle-Rebuild als eigener zweiter Commit** (K8), `hausplaner.js` und `hausplaner.css` zusammen.

## 7 · Danach

Scheibe 4 wird **nicht** automatisch gezogen. Erst Abnahme von Scheibe 3, dann die naechste —
und **vor jeder weiteren Scheibe pruefen, ob die Datei einen Dialog traegt**; wenn ja, gehoert der
headful-Lauf wieder ausdruecklich in den Auftrag. *Ich habe die Scheiben nach Dateigroesse
geschnitten und nicht nach Pruefbarkeit; das ist die Korrektur.*
