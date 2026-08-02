# BAUPLAN — wie aus 112 beschriebenen Werkzeugen benutzbare werden

**Planner Hausplaner · Strang `hausplaner-3d` · 02.08.2026, 10:2x**
*Antwort auf Yamas Frage: „welche Tools werden als nächstes erstellt, wie erstellen wir sie alle."*

---

## 0 · Der eine Satz, der den ganzen Plan trägt

> **Das Haus ist voller fertiger Fachlogik, die niemand erreichen kann.**

```text
ls -1 resources/planner/hausplaner/geometry/*.ts | wc -l              -> 52 Dateien
grep -h '^export function' .../geometry/*.ts | wc -l                  -> 189 Funktionen
davon von der App ueberhaupt aufgerufen                               ->  49
```

**14 Geometrie-Dateien ruft die Anwendung an keiner Stelle auf** — gemessen, Datei für Datei:

```text
grundriss           grundrissPolygon · eckenAnalyse · grundrissFlaecheM2 · formAusShape
masskette           masskette · grundrissMassketten · punkteMassketten
wandFlaeche         wandMengen                      <- die Mengenermittlung
dachOeffnung        oeffnungVTiefeM · oeffnungRechteck
auswechslung        sparrenPositionenU · analysiereAuswechslung
sparrenTrennung     sparrenTeilstuecke · istSicherTrennbar
linienBauteile      platziereSchneefang · sperrzoneVRel · istInSperrzone
treppenTypen · dachWerte · dachVorlage · aufbauPlatzierung · aufbauOrientierung
aufbautenStatus · integrationAbgleich
```

**Das ist kein Rückstand, das ist ein Vorrat.** Diese Werkzeuge sind nicht zu 0 % gebaut, sondern
zu 80 % — es fehlt der Weg von der Rechnung an die Oberfläche.

**Und diesen Weg sind wir am 01.08. schon einmal gegangen: Z-05-N1.** Die Kontur-Geometrie war
gebaut und grün, das Werkzeug trotzdem unerreichbar. Vier Stellen fehlten: Registry-Eintrag,
Themen-Bindung, Vertrag, Präsentationsregel. **Das war eine ganze Scheibe für EIN Werkzeug.**

## 1 · Die drei Klassen — jedes der 112 gehört in genau eine

```text
node -e '...' ueber werkzeugVertrag.ts + toolRegistry.ts + geometry/
   13  in der Registry                        BEDIENBAR
    8  Fachlogik belegt, kein Weg             KLASSE A
    6  stillgelegte Zeichen-Primitive         KLASSE C - werden NIE gebaut
   85  weder Registry noch Fachlogik          KLASSE B
```

**KORREKTUR IN EIGENER SACHE:** mein erster Zähler meldete **16** für Klasse A. Er verglich
Namenspräfixe über sechs Zeichen und traf dabei `verschieben` → `dachVerschneidung.ts` und
`schnitt` → `dachAusschnitt.ts` — **zwei falsche Treffer, beide plausibel, beide falsch.**
Nachgeprüft Datei für Datei bleiben **8**. *B4: ein Messgerät ohne Partner. Die Zahl 16 ist
zurückgenommen.*

### Klasse A — die acht, bei denen nur der Weg fehlt

| Werkzeug | vorhandene Fachlogik |
|---|---|
| `bemassen` | `geometry/bemassung.ts` · `geometry/masskette.ts` |
| `flaeche-messen` | `geometry/polygonFlaeche.ts` |
| `grundriss-erkennen` | `geometry/grundriss.ts` · `geometry/roomDetection.ts` |
| `gaube` | `geometry/gaubeGeometrie.ts` |
| `oeffnung` | `geometry/oeffnungsTypen.ts` · `oeffnungsBauarten.ts` |
| `heizkoerper` | `geometry/heizkoerperLeistung.ts` · `heizkoerperTypen.ts` |
| `kuechenplanung` | `geometry/kuecheArbeitsdreieck.ts` |
| `verteiler` | `geometry/heizkreisVerteiler.ts` |

### Klasse C — die sechs, die nie gebaut werden

`linie` · `polylinie` · `rechteck` · `kreis` · `bogen` · `polygon`
**Yamas Entscheidung, 01.08.: sie bleiben stillgelegt.** Ein Bauplaner zeichnet Bauteile, keine
Primitiven. *Sie bleiben im Katalog stehen, damit die Bilanz nicht lügt.*

## 2 · Der Hebel — warum wir NICHT 104 Werkzeuge einzeln bauen

**Z-05-N1 hat vier Stellen angefasst, um EIN Werkzeug erreichbar zu machen.** Für 104 wären das
416 Einzeleingriffe — und jeder eine Gelegenheit, die Bilanz zu brechen.

> **Der nächste Bau ist kein Werkzeug. Es ist der WEG zum Werkzeug.**

**W-05 — „Werkzeug-Anschluss als Verfahren".** Eine Scheibe, die aus den vier Stellen **eine
Datenzeile** macht:

```text
HEUTE   Registry-Eintrag von Hand · Thema von Hand · Vertrag pruefen · Praesentationsregel
        · acht Bilanz-Zusagen nachziehen                        = eine Scheibe je Werkzeug

DANACH  ein Eintrag { id, thema, vertragId, zone, kuerzel, bauteilKind }
        Registry, Thema, Praesentation und Bilanz LEITEN SICH DARAUS AB
                                                                = eine Zeile je Werkzeug
```

**Die Bilanz ist der Prüfstein.** Z-05-N1 hat sie schon richtig gebaut: `PAKET 110 + EIGENE.length`
statt einer nackten Zahl. **W-05 muss dieselbe Trennung halten** — sonst wird aus jedem neuen
Werkzeug wieder eine Zusagen-Reparatur.

*Ob der Hebel trägt, entscheidet sich am zweiten Werkzeug, nicht am ersten.* Deshalb enthält W-05
die Zusage: **zwei Werkzeuge aus Klasse A anschließen, und die zweite Anbindung ändert nur Daten.**

## 3 · Die Reihenfolge — Wellen, nicht Liste

**Welle 0 — jetzt, ohne neues Papier.** Z-05 abnehmen → **Z-06 Decke aus Kontur.**
*Das ist Yamas stehendes Ziel und hängt an einem einzigen Votum.*

**Welle 1 — der Hebel.** W-05 Werkzeug-Anschluss. Beweis am zweiten Werkzeug.

**Welle 2 — die acht aus Klasse A, in dieser Reihenfolge.** Begründung je Zeile, nicht Geschmack:

```text
1  bemassen             Ein Plan ohne Masse ist kein Plan. Fachlogik komplett.
2  flaeche-messen       Flaeche ist die Grundlage jeder Menge und jedes Angebots.
3  grundriss-erkennen   Macht aus gezeichneten Waenden RAEUME - Vorbedingung fuer alles,
                        was raumbezogen rechnet (Heizlast, Belag, Sanitaer).
4  oeffnung             Fenster/Tuer gibt es; die allgemeine Oeffnung fehlt (Durchbruch, Nische).
5  gaube                Dachlogik ist gebaut, die Gaube ist der sichtbarste Teil davon.
6  heizkoerper          Yamas Fach. Leistung und Typen sind gerechnet, der Weg fehlt.
7  verteiler            Haengt fachlich an 6.
8  kuechenplanung       Arbeitsdreieck ist gebaut; der Nutzen steht und faellt mit 3.
```

**Welle 3 — Klasse B, 85 Werkzeuge.** *Hier wird nicht nach Werkzeug geplant, sondern nach
Familie.* Die Verträge geben die Sortierung schon vor:

```text
41  create               legen Bauteile an - dieselbe Naht wie Wand/Decke/Dach
20  modify               veraendern Vorhandenes - brauchen Auswahl + Undo, EINE gemeinsame Naht
15  workflow             mehrschrittig, teuerste Klasse, ZULETZT
 9  assign-or-calculate  rechnen (U-Wert, Heizlast) - brauchen Norm-Entscheidungen von Yama
 8  import               DATANORM, IDS, Open Masterdata -> gehoert Strang `produktdaten`
 7  view · 5 measurement · 4 selection · 2 domain
```

**Die acht `import`-Werkzeuge gehören nicht uns.** Sie liegen im Strang `produktdaten` — das ist
der erste belastbare Nutzen der Strangbindung des zweiten Planners.

## 4 · Was das kostet — ehrlich, mit dem Unsicherheitsband

```text
Welle 0   Z-06                       eine Scheibe        gemessen: Z-05 brauchte ~2 h
Welle 1   W-05 Hebel                 eine Scheibe        Schaetzung, KEINE Messung
Welle 2   8 Werkzeuge Klasse A       8 x eine Zeile      ERST NACH W-05 schaetzbar
Welle 3   85 Werkzeuge Klasse B      nach Familie        NICHT schaetzbar. Ehrlich: unbekannt.
```

**Ich nenne für Welle 3 bewusst keine Zahl.** Wer 85 Werkzeuge in Stunden umrechnet, ohne eines
davon gebaut zu haben, liefert eine Behauptung mit Nachkommastelle. **Nach Welle 2 haben wir acht
Messpunkte — dann wird geschätzt, vorher nicht.**

## 5 · Was ZUERST entschieden werden muss — sonst baut Welle 2 ins Leere

```text
TOR 1, Yama    Die 9 `assign-or-calculate` rechnen nach Normen (U-Wert, Heizlast, FBH).
               Welche Norm, welche Fassung - das ist Fachentscheidung, keine Bauentscheidung.
TOR 1, Yama    Z-09 T-Stoss: ACHSE oder FLANKE (docs/planner/entscheidung-z09-tstoss-...)
               Beruehrt jede Mengenermittlung, also Welle 2 Punkt 2.
PLANNER        W-05 schneiden - danach ist Welle 2 mechanisch.
```

---

**Zusammengefasst in drei Zeilen:**
**Acht Werkzeuge sind fast fertig und brauchen einen Weg. Der Weg wird einmal gebaut (W-05) und
gilt dann für alle. Die restlichen 85 werden nach Familie geplant, nicht nach Namen — und erst,
wenn wir acht echte Messpunkte haben.**
