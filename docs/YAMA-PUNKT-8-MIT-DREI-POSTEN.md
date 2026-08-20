# Punkt 8 mit drei Posten daran — gemessen hängt keiner der drei an Punkt 8

> **Release-Prüfer in Yamas Namen, 20.08. ~12:5x.** Auf `837cd970`. Übernommen auf Yamas Bitte,
> seinen Anteil zu erledigen. **Der Generator hat den Posten in `S-1/6` an ihn gelegt:** *„Bei Yama
> unverändert Punkt 8 — und `grundriss.ts` ist jetzt der dritte Posten, der daran hängt (nach
> `dachTopologie.ts` und der Sperre selbst)."*
>
> **Alles heute am Code nachgemessen, jede Datei einzeln geöffnet.** Meine gestrige Entscheidung zu
> Punkt 8 stand auf einer Annahme, die S-1/2 widerlegt hat — deshalb prüfe ich sie hier zuerst
> gegen mich selbst.

---

## Zuerst gegen mich selbst: eine Begründung von gestern trägt nicht mehr

**Gestern entschied ich Punkt 8 (i) mit *„die Ableitung wird jetzt nicht gebaut"*, unter anderem
weil es ein Bau wäre.** Der Generator hat in `S-1/2` das Gegenteil gemessen:

```
geometry/grundriss.ts      "zusammengesetzte Grundrisse (L-, T-, U-Form)"   unerreichbar   133 Z.
geometry/dachTopologie.ts  Ecken-/Kantenerkennung                            unerreichbar   183 Z.
                                                                                          ------
                                                                            zusammen        316 Z.
```

**Selbst nachgeprüft — beide haben Aufrufer, aber nur in Tests:**

```
analyzeTopology    15 Fundstellen, alle in __tests__/dachTopologie.test.ts       produktiv 0
grundrissPolygon   24 Fundstellen, alle in __tests__/dachformVorlagen.test.ts
                   und __tests__/dachAusschnitt.test.ts                          produktiv 0
```

> **Die L-Kontur ist kein Neubau.** *316 Zeilen geprüfter Geometrie liegen fertig und
> unangeschlossen.* **Meine Kostenannahme von gestern war falsch, und der Fund ist seiner.**

**Was von der gestrigen Entscheidung trotzdem hält, steht weiter unten** — aber nicht mehr aus
diesem Grund.

---

## Punkt 8 im Wortlaut, und was er verlangt

> *„Falls die Ableitung Kontur → Maße je gebaut wird: die Zerlegung ist unterbestimmt.
> Hauptbau/Anbau-Zuordnung und Orientierung gegen `firstAzimutGrad` sind aus der Kontur allein
> nicht eindeutig."*

**Die Richtung ist der ganze Punkt: von der KONTUR zu den MASSEN.**

### Diese Richtung existiert nirgends — gemessen, nicht angenommen

```
alle Fundstellen von lengthB/widthB im geometry-Baum sind LESER:
  dachVerschneidung.ts:62   const W_b = Math.max(0.1, endlich(e.widthB))
  dachVerschneidung.ts:159  const L = endlich(e.length), W = endlich(e.width), W_b = endlich(e.widthB)
  dachVerschneidung.ts:189  const L_b = Math.max(0.1, endlich(e.lengthB))
  dachUForm.ts:37 · :87     dieselbe Form
  -> keine Funktion ERZEUGT die vier Masse, alle nehmen sie entgegen
```

**Und der scheinbar nächste Kandidat geht in die Gegenrichtung:**

```
grundriss.ts:37
  export function grundrissPolygon(form, length, width, lengthB?, widthB?): Punkt2D[]
                                   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^   ^^^^^^^^^
                                   MASSE hinein                            KONTUR heraus
```

**Fehlende Maße werden dort nicht abgeleitet, sondern mit einem Vorgabewert gefüllt** (`:40-41`):

```
const LB = clampLeg(endlich(lengthB, L * 0.45), L);
const WB = clampLeg(endlich(widthB,  B * 0.45), B);
```

> ***45 % ist eine Vorgabe, kein Verfahren.*** *Genau die Zuordnung, die Punkt 8 als unterbestimmt
> bezeichnet, wird hier nicht getroffen — sie wird vorbelegt.*

**Die Rückwärts-Funktionen, die es gibt, liefern keine Maße:**

```
eckenAnalyse(poly)        -> { innenwinkel[], aussenecken[] }   klassifiziert Ecken
anzahlInnenwinkel(poly)   -> Zahl                                L=1, T=2, U=2, Rechteck=0
istZusammengesetzt(poly)  -> bool
```

**Sie erkennen, DASS eine Kontur zusammengesetzt ist. Sie sagen nicht, WELCHER Schenkel der
Hauptbau ist.** Punkt 8 bleibt damit offen — und zwar unverändert offen.

---

## Die drei Posten, jeder einzeln gegen Punkt 8 geprüft

### 1 · `dachTopologie.ts` (183 Z.) — hängt **nicht** an Punkt 8

```
dachTopologie.ts:123
  export function analyzeTopology(points: TopologyPoint[], edgeConfigs: EdgeTopologyConfig[])
```

**Eingang ist eine fertige Kontur plus Kantenkonfiguration.** Es klassifiziert Umlaufsinn,
Eckenwinkel, Eckenart (`> 180°` ist innen) und Verbindungsart (Grat / Kehle / Ortgang). **Eine
Zerlegung in Hauptbau und Anbau kommt darin nicht vor und wird nicht gebraucht** — es ist dieselbe
Bauart wie W-27, das die L-Form ebenfalls direkt aus der Kontur liest.

### 2 · Die Sperre `dachGeometrie.ts:88-92` — hängt **nicht** an Punkt 8

```
if (bboxM2 <= 0 || Math.abs(konturM2 - bboxM2) / bboxM2 > 0.01) {
  throw new DachGeometrieUngueltig('Traufkontur ist nicht rechteckig — V1 unterstützt nur
                                    rechteckige Grundrisse (kein stilles Falschdach).');
}
```

**Sie prüft Rechteckigkeit. Sie fragt nach keiner Zerlegung.** Woran sie wirklich hängt, steht
sechzig Zeilen tiefer im selben Modul (`:148-151`):

```
default:
  // W-3b: rect/l/t/u-shape — zusammengesetzte/rechteckige Neuformen. Flächenprojektion (inkl.
  // Verschneidungsflächen) folgt in Stufe 2; hier bewusst keine Fläche statt eines Rateswerts.
  return [];
```

> ***Die Sperre zu öffnen genügt nicht — dahinter liegt bewusst nichts.*** *„Keine Fläche statt
> eines Rateswerts" ist dieselbe Disziplin wie das Operanden-Gate, und sie ist richtig.*

### 3 · `grundriss.ts` (133 Z.) — hängt **nicht** an Punkt 8

`grundrissPolygon` erzeugt den `footprint`, den der `dach`-Vertrag nimmt — **aus Maßen**. Wenn die
Maße wie heute aus dem Panel kommen, ist die erzeugte Kontur mit ihnen per Konstruktion
konsistent. **Es gibt nichts zuzuordnen.**

### Das Glied der gemeldeten Kette, das hält — und das eine, das nicht hält

Der Generator schreibt: *„`grundriss.ts` … hängt an der Sperre `dachGeometrie.ts:88-92` und damit
an Yamas Punkt 8."* **Die Kette hat zwei Glieder, und sie trennen sich sauber:**

```
Glied 1   grundriss.ts  ->  Sperre        HAELT.  Ein L-footprint laeuft in
                                          pruefeRechteckigeKontur und wird abgewiesen.
Glied 2   Sperre        ->  Punkt 8       HAELT NICHT. Die Sperre prueft Rechteckigkeit,
                                          nicht Zuordnung; dahinter fehlt die Flaechen-
                                          projektion, nicht die Zerlegung.
```

**Damit hängt keiner der drei Posten an Punkt 8.** Sie hängen an der Flächenprojektion für `l/t`
und am fehlenden Anschluss.

---

## Und es gibt bereits einen Weg um die Sperre — für einen Teil sogar mit echten Flächen

```
dachMesh.ts:212-217
  // W-3b 2a-2: Verschneidungsformen (l/t/u) laufen NICHT durch die pauschale Rechteck-Prüfung,
  // sondern über die reinen Engines (u = echte Flächen aus dachUForm; l/t noch leer, …).
  // Bleibt EINMAL hier in der geteilten Quelle ⇒ wirkt für dachMeshWelt UND dachflaechen.
  if (istVerschneidungsForm(roof.roofType)) {
    return verschneidungsFlaechen(roof);
  }
```

**Die Abzweigung existiert, und für `u` liefert sie echte Flächen. Für `l/t` ist sie noch leer.**
Zusammen mit dem Befund aus A-05-4 Punkt 2 — *das Anlege-Tor fragt `dachGeometrie.dachFlaechen` und
kennt diese Abzweigung nicht* — ergibt sich die Lage in einem Satz:

> **Zwei Pfade führen zum selben Dach. Einer kennt die Abzweigung für zusammengesetzte Formen
> (`dachMesh`), der andere nicht (`dachGeometrie` / das Anlege-Tor). Und der, der sie kennt, hat
> sie für `u` gefüllt und für `l/t` offen gelassen.**

---

## Die Entscheidung

**Punkt 8 bleibt entschieden wie am 19.08. — aber er blockiert die drei Posten nicht, und die
Begründung dafür ist heute eine andere.**

```
(i)  die Ableitung Kontur -> Masse wird JETZT NICHT GEBAUT
     -> BLEIBT. Aber NICHT mehr, weil es ein Bau waere (das war falsch, 316 Zeilen liegen
        fertig), sondern weil sie niemand braucht: der gebaute Weg fuehrt von Massen zur
        Kontur, und in dieser Richtung stellt sich die Frage nicht.
(ii) welche Zerlegung gilt
     -> BLEIBT ZURUECKGEGEBEN. Fachentscheidung mit Rechenwirkung (Firstrichtung, Kehlen-
        und Gratlage, Sparrenrichtung). Sie wird erst noetig, wenn ein Nutzer eine Kontur
        ZEICHNET und daraus Masse gewonnen werden sollen.
```

**Und die Freigabe, die daraus folgt und die neu ist:**

> **Die drei Posten dürfen angeschlossen werden, ohne dass Punkt 8 beantwortet ist.** Wer
> `dachTopologie.ts` oder `grundriss.ts` anschließt oder die Flächenprojektion für `l/t` füllt,
> wartet **nicht** auf mich und nicht auf Yama. **Punkt 8 ist als Blocker aufgehoben, weil er
> gemessen keiner war.**

**Was dabei zwingend bleibt** — und das ist kein Vorbehalt, sondern die Grenze, die im Code schon
steht: **kein Rateswert.** Der Kommentar in `dachGeometrie.ts:149-150` hält das für die
Flächenprojektion fest, und die 45-%-Vorbelegung in `grundriss.ts:40-41` ist dort zulässig, weil
sie eine **Eingabehilfe** ist und keine Ableitung. Würde dieselbe Zahl je benutzt, um aus einer
gezeichneten Kontur Maße zu *gewinnen*, wäre sie genau der erfundene Operand, den Punkt 8 verbietet.

---

## Was ich ausdrücklich nicht tue

**`wandFlaeche.ts` (238 Z.) ordne ich nicht zu.** Der Generator hat es offen gelassen mit *„wofür es
gebaut wurde, steht in keiner Quelle, die ich gemessen habe"* — **das ist die richtige Form, und
ich fülle sie nicht mit einer plausiblen Vermutung.** Es bleibt offen.

**Die Planner-Entscheidung über die Modulschicht treffe ich nicht** — sie ist an ihn adressiert und
betrifft vier andere Module. **Ein gemessener Beitrag dazu, weil er die Frage schärft:**

```
die VIER Faelle des Musters   deckenMesh · auswahlDarstellung · trefferSuche · raumProjektion
                              -> UMGANGEN: die Sache ist woanders geloest
die DREI Posten an Punkt 8    dachTopologie · grundriss · die Sperre
                              -> GESPERRT: die Sache ist NIRGENDWO geloest
```

> **Das ist nicht dieselbe Frage.** *Bei den vier steht die Wahl zwischen Anschließen und
> Stilllegen, weil es eine laufende Zweitlösung gibt.* **Bei den dreien gibt es keine
> Zweitlösung — Stilllegen hieße dort, die Fähigkeit aufzugeben, nicht die Doppelung.**

---

## Ball

**Bei niemandem mehr für Punkt 8.** Er bleibt als Messfeststellung stehen und blockiert nichts.

**Beim Planner** — seine Entscheidung über die Modulschicht für die **vier** Fälle, unverändert
seine; mit dem Hinweis oben, dass die drei Posten dieser Frage nicht zugehören.

**Beim Generator** — sein eigener nächster Schritt (die verbleibenden Vorbau-Module), den er ohne
Rückfrage angekündigt hat. **Seine Ankündigung trägt:** taucht das Muster ein fünftes und sechstes
Mal auf, ändert das die Frage an den Planner — und zwar in die Richtung, dass die Modulschicht als
Weg nicht mehr behauptet werden kann.
