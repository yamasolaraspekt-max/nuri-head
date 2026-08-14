# W-10 · Decke und Boden — PRÜFUNG

## Zuerst die Wortfalle, weil sie mich fast einen Phantom-Befund gekostet hätte

**Die Oberfläche verspricht „Treppen werden **ausgespart**" — im Code kommt das Wort „Aussparung"
NULL Mal vor.** *Nach A-24 wäre das der Befund gewesen: eine Zusage, die der Code nicht hält.*

**Selbst gemessen, über den ganzen Inselbaum:**

```text
'ausspar'              0 Dateien     <- und hier wird es scharf
'Aussparung'           0 Dateien
'aussparung'           0 Dateien
'ausgespar'            5 Dateien     <- DARUNTER DIE ZUSAGE SELBST (toolRegistry.ts:141)
'treppenDurchbrueche'  3 Treffer     <- die Sache
'Durchbr'             18 Treffer
```

> ***Die Falle hat DREI Stufen, nicht zwei — und die dritte ist die gemeinste.***
>
> 1. *Die Sache heißt im Code* **`treppenDurchbrueche`** *und nicht „Aussparung".*
> 2. *Die Zusage schreibt* **„ausgespart"** *— eine andere Wortform.*
> 3. **`grep 'ausspar'` findet die Zusage NICHT EINMAL SELBST**, *weil in „aus-ge-spart" ein `ge`
>    dazwischensteht.* **Wer so sucht, misst null und hält es für Abwesenheit — dabei hat er nicht
>    einmal den Text gefunden, den er prüfen wollte.**
>
> ***Das ist H-9 in Reinform:*** *ein Muster, das eine Schreibweise voraussetzt, misst die
> Schreibweise und nicht die Sache.* **Vierte Wortfalle derselben Art** — *nach `modus` (W-12),
> `Aufbau` und `versetzen`/`Versatz` (A-29).*

## Und die Zusage HÄLT — die tragende Stelle geöffnet, nicht gegrept

```text
VERSPRECHEN   toolRegistry.ts:141   „(Treppen werden ausgespart)"
LEISTUNG      applyCommand.ts:119   treppenDurchbrueche — Rumpf :121-136, vollstaendig
AUFRUF        applyCommand.ts:298   auto = oeffnungen? oeffnungen : treppenDurchbrueche(...)
ZUSAGE        decke.test.ts:62      „Treppe im Level ⇒ automatische Öffnung in der Decke"  ✔
```

> **Erst der vierte Schritt entscheidet.** *Gebaut, aufgerufen UND durch einen Wächter gehalten —
> jede der drei Stufen allein hätte gereicht, um sich zu täuschen.*

## Der Wächter: `__tests__/decke.test.ts` — 242 Z., DREIZEHN Zusagen, alle grün

**Selbst gefahren am Bau-Stand, Rohausgabe:**

```text
✔ additiv: Dokument OHNE ceilings validiert (kein 422); MIT ceilings ebenfalls
✔ ADD_CEILING legt eine Decke an; zweite je Level wird abgelehnt (max. 1)
✔ mm-Invariante: nicht-ganzzahlige Deckendicke wird abgelehnt
✔ Treppendurchbruch (aus Grundriss): Treppe im Level ⇒ automatische Öffnung in der Decke
✔ deckenNettoFlaecheM2: Umriss minus Durchbrüche
✔ UPDATE/REMOVE_CEILING
✔ Etagen-Stapel: nächste Elevation = Elevation + Wandhöhe + Deckendicke (eine Ableitung)
✔ Z-06/K-02: die Decke einer L-Form hat 68 m² — NICHT die 80 des umschliessenden Rechtecks
✔ Z-06/K-02: der Umlaufsinn aendert die Flaeche nicht — sonst haengt sie an der Klickrichtung
✔ Z-06/K-01: die Insel nimmt die Kontur — und nur das Dach behaelt den Umriss bedingungslos
✔ Z-06/K-03: ohne Kontur meldet die Fussleiste eine Naeherung — mit Kontur schweigt sie
✔ Z-07/K-06: das DACH meldet seine Naeherung ebenso — der Melder haengt an der Entscheidung
✔ Z-07/K-04: die L-Form bekommt ein L-DACH — 68 m², nicht die 80 der Bounding-Box
ℹ tests 13 · pass 13 · fail 0
```

### Die drei, auf die es ankommt

> ***„die Decke einer L-Form hat 68 m² — NICHT die 80 des umschließenden Rechtecks"*** *(`:122`).*
> **Der Kommentar darüber (`:110-114`) sagt, warum die Zusage die FLÄCHE prüft und nicht die
> Punktliste:** *„die Klasse dieser Scheibe ist ‚falsch, aber sieht richtig aus'. In jedem der acht
> Fälle ERSCHEINT eine Decke — sie hat nur die falsche Fläche, und die sieht im Bild niemand."*
> **Eine Punktliste friert den gebauten Zustand ein; die Fläche prüft die Aussage.**

> ***„der Umlaufsinn ändert die Fläche nicht"*** *(`:133`) — eine **Eigenschaft**, kein Beispiel.*
> **Sie hängt an `Math.abs` in `polygonFlaeche.ts:46`**; *ohne den Betrag hinge die Deckenfläche an
> der Klickrichtung des Anwenders.*

> ***„zweite je Level wird abgelehnt (max. 1)"*** *(`:50`) — die einzige Zusage, die eine ABLEHNUNG
> festhält.* **Wer `pruefeDeckeProLevel` auf `:296` entfernt, wird hier rot.**

## Was der Wächter NICHT hält

| ungeprüft | Folge |
|---|---|
| **die Ganzzahligkeits-REIHENFOLGE** (`:300` nach `:298`) | wer die zwei Zeilen tauscht, prüft die Eingabe statt des Ergebnisses — `:57` bleibt grün, weil die Testdicke schon vor der Automatik krumm ist |
| **`pruefeDeckeProLevel` in `UPDATE_CEILING`** (`:315`) | `:50` prüft nur den ADD-Weg; eine zweite Decke per Level-Umhängung fängt niemand |
| **der Ersatzwert 1 bei Länge null** (`:127`) | eine Treppe mit `start === end` ist von keiner Zusage berührt |
| **`deckenOberkanteMm`** (`deckenMesh.ts:10`) | die einzige der drei Ausfuhren ohne eigene Zusage |

> ***Die erste Zeile ist die wichtigste.*** *Die Reihenfolge auf `:298`–`:300` ist eine echte
> Zusage an Aufrufer (siehe `2-FUNKTION`), und sie ist durch keinen Wächter gesichert.* **Ein Test
> mit ganzzahliger Eingabe und krummer Lauflinie würde sie halten** — *als Befund festgehalten, nicht
> gebaut: eine Ablesung baut keine Tests.*

## Wie diese Ablesung rot werden könnte

**Nicht durch fehlenden Code** — *der existiert.* **Sondern durch eine falsche Ablesung:** *eine
Zeilennummer, die nicht trifft; eine Zahl ohne Träger; eine F-Nummer, die ich übernommen statt
gemessen hätte.*

> **Alle Zahlen dieses Blattes sind am Bau-Stand erhoben und tragen ihren Bezug:** *ZWÖLF gilt für
> `app/tools/toolRegistry.ts`, **35** für `deckenMesh.ts`, **242** und **13** für
> `__tests__/decke.test.ts`, **DREI** für die Ausfuhren von `deckenMesh.ts`.*
