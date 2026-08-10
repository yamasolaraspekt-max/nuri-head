# W-07 · Dach aus Kontur — FORMELN

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **F-010** Orientierung | Kontur auf „gegen den Uhrzeigersinn" bringen | **JA** — der Skeleton-Algorithmus verlangt es. Falsche Orientierung → nach innen gestülptes Dach |
| **F-013** Selbstschnitt | Vorprüfung der Kontur | **JA** — eine selbstschneidende Kontur liefert ein plausibles, aber falsches Dach ohne Fehlermeldung |
| **F-020** Straight Skeleton | **Kern.** Firste, Grate, Kehlen erzeugen | **JA** — Spalt-Ereignisse bei einspringenden Ecken. Siehe unten |
| **F-021** Skelett anheben | Aus dem flachen Skelett das räumliche Dach | **JA** — α = 90° verboten, α = 0° eigener Weg |
| **F-022** Neigung umrechnen | Anwender gibt Grad oder Prozent ein | **JA** — 45° = 100 %, nicht 50 % |

## Reihenfolge der Anwendung

```
1. F-013  Selbstschnitt-Prüfung
             └─ nicht bestanden → Absage „Kontur überschneidet sich"

2. F-010  Orientierung bestimmen
             └─ im Uhrzeigersinn → Punktfolge umdrehen
             └─ 2A ≈ 0 → Absage „Kontur ist entartet"

3. F-022  Neigung in Radiant umrechnen
             └─ α ≥ 85° → Absage „Neigung zu steil"
             └─ α = 0° → Sonderweg Flachdach, Schritt 4+5 entfallen

4. F-020  Straight Skeleton berechnen
             ├─ Kanten-Ereignisse   (jede Kontur)
             └─ Spalt-Ereignisse    (nur bei einspringenden Ecken)
             └─ Ereignis nicht behandelbar → Absage mit Ereignistyp und Ort

5. F-021  Knoten anheben: z = t · tan(α)
             └─ Ergebnis: Dreiecksnetz der Dachflächen

6. Probe: fließt Wasser von jedem Punkt zur Traufe?
             └─ nein → innerer Fehler, nicht ausliefern
```

## Der entscheidende Grenzfall — einspringende Ecken

Ein rechteckiger Grundriss hat **nur** Kanten-Ereignisse. Eine Umsetzung, die nur
diese behandelt, liefert für ein Rechteck ein einwandfreies Dach — und ist damit
scheinbar fertig.

**Bei einem L- oder U-förmigen Grundriss** entsteht ein **Spalt-Ereignis**: Eine
wandernde Ecke trifft auf eine gegenüberliegende Kante, und das Polygon zerfällt
in zwei Teile, die getrennt weiterwandern.

> Das ist die Stelle, an der Auftrag Z-07 rot wurde. Der Auftrag verlangte ein
> L-förmiges Dach mit 68 m². Die Domäne hatte das nie gekonnt und warf korrekt
> `DachGeometrieUngueltig` für **jede** nicht-rechteckige Kontur. Der Fehler lag
> nicht im Code, sondern in der **unbelegten Machbarkeitsannahme** im Auftrag.
>
> **Regel daraus: bevor ein Auftrag eine Kontur voraussetzt, wird gemessen,
> ob die Domäne sie kann. Nicht geschätzt.**

## Genauigkeit

- Eingang: Konturpunkte in ganzen Millimetern
- Rechnung: Fließkomma (`double`) — Winkelhalbierende und Ereigniszeiten brauchen es
- Rückgabe: Knotenkoordinaten auf ganze Millimeter gerundet
- Toleranz ε = 0,5 mm für Ereignis-Gleichzeitigkeit
- **Bekannte Ungenauigkeit:** Bei fast parallelen Kanten (Winkel < 1°) liegen die
  Ereigniszeiten so dicht beieinander, dass ihre Reihenfolge von der
  Rundung abhängt. Das erzeugt gelegentlich ein zusätzliches
  Mikro-Dreieck. Sichtbar wird es erst bei sehr großen Gebäuden.
