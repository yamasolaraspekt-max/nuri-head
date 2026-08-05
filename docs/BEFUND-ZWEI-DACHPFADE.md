# BEFUND — die Insel kann L-Dächer schon. Wir bauen eine Absage dafür.

```yaml
befund: SPEC / P1
betrifft: A-01
gefunden: 05.08.2026, auf Yamas Frage "warum greift ihr auf playground und PV-Dachplaner nicht zurück"
verursacher: planner
status_steht_in: docs/STATUS.md
```

## Yamas Frage war der Befund

> *„ihr habt doch genug Info über playground und PV-Dachplaner, warum greift ihr auf die Daten
> nicht zurück und schaut wie es aufgebaut ist"*

**Gemessen, statt geantwortet:**

```text
A-Blaetter, die playground oder Dachplaner pruefen      0
Dach-/3D-Dateien im playground-Archiv                  65
vorbereiteter Referenzordner                            docs/planner/pv-belegung-referenz/
  darin: Vorgabe 3D-Dachplanung (219 Z., Fachbewertung Zimmermann/Dachdecker/PV)
         Umsetzungsplan (175 Z., mit Yamas Pflichtanforderungen vom 12.06.)
```

**§2 und `CLAUDE.md` verlangen die Wiederverwendungsprüfung vor jeder Neuentwicklung.
Ich habe sie nicht gemacht.**

## Was die Prüfung ergibt — und es ist schlimmer als „nicht nachgesehen"

**Die Vorgabe selbst ist nur teilweise übertragbar** — ihre Kerndateien gibt es hier nicht:

```text
roofModel.ts · RoofScene3D.tsx · PvPlanungPage.tsx      0 Treffer, auch nicht im Archiv
deriveRoofGeometry (Schwaeche 6 der Vorgabe)            0 Treffer
dachGeometrie.test.ts (die fehlenden Geometrietests)    EXISTIERT hier bereits
```

*Die Vorgabe zielt auf die playground-App, nicht auf unsere Insel. Sie ist Fachwissen, keine
Bauanleitung für uns.* **Aber der Blick dorthin hat etwas anderes freigelegt:**

### Die Insel hat ZWEI Dachpfade, und A-01 benutzt den schwächeren

```text
PFAD 1  geometry/dachGeometrie.ts:87
        wirft bei jeder Kontur != Bounding-Box
        Kommentar im Code: "V1 unterstuetzt nur rechteckige Grundrisse"
        -> A-01 fragt genau diesen und baut die Absage darum

PFAD 2  domain/roofShape.ts          RoofShape · RECHTECK · VERSCHNEIDUNGS · istVerschneidungsForm
        geometry/dachVerschneidung.ts  10 Exporte, darunter lTBauGueltig,
                                       verschneidungslinien, verschneidungsFlaechen
        geometry/dachUForm.ts          10 Exporte, darunter uBauGueltig, uFormFlaechen
        angebunden an                  renderers/three-d/dachMesh.ts (Renderer)
                                       app/rahmen/EigenschaftenPanel.tsx (Oberflaeche)
                                       domain/validation.ts · domain/scene.types.ts
        Zusagen vorhanden              dachVerschneidung.test.ts · dachUForm.test.ts
                                       roofShape.test.ts
```

**`lTBauGueltig` heißt wörtlich: L-/T-Bau gültig.** Es gibt eine Formunterscheidung, eine
Validierung, ein Eigenschaftenpanel, einen Renderer und Zusagen — **für genau die Dächer, die A-01
als Nicht-Ziel führt.**

### Und der Anlege-Pfad ruft KEINEN von beiden

```text
grep 'roofShape|dachFlaechen|dachGeometrie' app/HausplanerApp.tsx      0 Treffer
```

*Er setzt ein Dach-Objekt mit Polygon zusammen (`:961`/`:965`) und überlässt alles Weitere dem
Renderer. Die Formfrage wird beim Anlegen nie gestellt.*

## Was daraus folgt

**FACT.** Zwei Pfade, der zweite mit Tests, UI und Renderer für L/T/U.
**FACT.** A-01 wählt Pfad 1 und baut eine Absage.
**FACT.** Meine A-01-DECISION (*„die Absage fragt `dachFlaechen()` selbst"*) entstand, ohne dass ich
Pfad 2 je gemessen hatte.

**HYPOTHESE — ausdrücklich ungemessen:** Ein L-Dach aus einer Kontur ist möglicherweise erreichbar,
indem beim Anlegen die **Form** gesetzt wird (`VERSCHNEIDUNGS` statt `RECHTECK`), statt eine Absage
zu erzeugen. **Ich habe das NICHT gemessen** und schreibe es deshalb nicht als Tatsache.

> **Wenn die Hypothese trägt, baut A-01 eine Fehlermeldung für etwas, das die Insel kann.**
> Dann ist der Auftrag nicht falsch, aber er löst das kleinere Problem — und das ist die Antwort
> auf Yamas Frage, warum es bei 3D und Dach nicht vorangeht.

## Mein Vorschlag

1. **A-01 NICHT stoppen.** Der A-01-4-Mangel (3D meldet nicht) ist unabhängig echt: auch ein
   künftig unterstütztes Dach braucht eine Meldung, wenn etwas nicht darstellbar ist. *Und ein
   laufender Bau wird nicht auf eine Hypothese hin angehalten.*
2. **Sofort messen, ob die Hypothese trägt** — ein Auftrag rein zur Messung, ohne Bau:
   trägt Pfad 2 eine L-Kontur aus dem Anlege-Pfad, oder fehlt mehr als die Formzuweisung?
3. **Erst mit dem Messergebnis** entscheiden, ob A-01s Nicht-Ziel („keine L-, T-, U-Dächer") bleibt.

**Nicht vorschlagen werde ich, die Vorgabe 1:1 umzusetzen.** Sie beschreibt eine andere Codebasis.
*Ihr Fachteil — Zimmermann, Dachdecker, PV-Installateur, und die Reihenfolge „Geometrie zuerst
härten" — ist wertvoll und gehört gelesen, bevor der nächste Dach-Auftrag geschnitten wird.*

## Was ich daraus über meine eigene Arbeit lerne

**Ich habe in A-01 einen zweiten Rechtecks-Begriff vermieden und dabei einen zweiten DACH-BEGRIFF
übersehen.** Die Sorgfalt galt der Stelle, auf die ich schaute. *Die Wiederverwendungsprüfung ist
kein Nebenpunkt von §5 — sie ist der Punkt, der bestimmt, ob der Auftrag überhaupt der richtige ist.*
