# Konzept — 3D-Wandecken auf Gehrung (Fach-Freigabe Yama, Regel 4)

**Status: KONZEPT zur Fach-Freigabe. Noch KEIN Generator-Auftrag.**

## Befund (gemessen, Regel 1)
- 2D-Wandecken sind sauber gehrt. Quelle: `geometry/wallGeometry.ts` -> `wandBaender(waende)` liefert je Wand
  ein gehrungssauberes 4-Eck-Polygon; "beide Waende berechnen an einer Ecke DENSELBEN Gehrungspunkt"
  (`gehrungsEcken`). Das ist die EINE Wahrheit fuer Wand-Ecken. Getestet: `wandBaender.test.ts`.
- 3D ignoriert sie. `renderers/three-d/segmentierung.ts` importiert aus wallGeometry NUR `wandLaenge`;
  `platziereWandQuader` (platzierung.ts) baut jede Wand als achsparallele Roh-Box (Laenge = Rohwandlaenge,
  Dicke = `thickness`, Drehung um die Mittelachse). NIEMAND in `renderers/three-d/` nutzt `wandBaender`.
  -> an der Ecke laufen zwei Boxen bis zum gemeinsamen Punkt und ueberlappen/klaffen -- nie gehrt.

## Linsen-Urteil (Fachpruefer-Panel)
- software-architekt: Es gibt bereits EINE Ecken-Wahrheit (`wandBaender`). 3D soll sie wiederverwenden,
  nicht eine zweite Miter-Berechnung erfinden (Reuse vor Neu, keine zweite Wahrheit). Kein Beifang: nur der
  Wand-3D-Bau aendert sich, `segmentierung`/Oeffnungslogik bleibt.
- maurer: Ecke = Verband-Stoss, legitim darzustellen. Modell hat KEIN Tragend-Flag -> reine Geometrie,
  KEINE Statik-Aussage, keine Modell-Aenderung noetig.
- technischer-zeichner: Sauber schliessende Ecke = normgerecht (DIN 1356-1); 3D muss zum Grundriss konsistent
  sein. Ueberlappung ist ein Darstellungsfehler.
- bauplaner-3d: additiv, reine Render-Geometrie -> KEIN Schema-Regen (Zod/Modell unberuehrt).

## Entscheidung (die Yama freigeben soll)
3D-Waende beziehen ihren Grundriss aus `wandBaender` (gehrtes Band) statt aus Roh-Laenge x Dicke. Oeffnungen
laufen unveraendert ueber `segmentierung` (sie schneiden nur die vertikalen Aussparungen im Wandkoerper; die
Gehrung betrifft nur die Wand-ENDEN an den Ecken). Zwei Umsetzungstiefen zur Wahl:

- A -- Voll-Reuse (sauber, mehr Arbeit): den Wandkoerper als Extrusion des gehrten Band-Polygons auf
  Wandhoehe bauen, Oeffnungen als Aussparung. 2D und 3D teilen EXAKT dieselbe Ecken-Geometrie. Aendert das
  Wand-Meshing (Box -> Extrusion-mit-Aussparung).
- B -- End-Gehrung (minimal, schneller): Boxen bleiben, aber die End-Segmente an den Ecken bekommen ihre
  Endkappe auf den Gehrungswinkel abgeschraegt (aus den `wandBaender`-Eckpunkten). Weniger invasiv, aber
  zwei Geometrie-Arten (Box + abgeschraegtes Ende).

Empfehlung: A -- es ist die echte "eine Wahrheit" (Grundriss und 3D nie wieder divergent), und die
Ueberlappung verschwindet strukturell statt kaschiert. B ist der schnellere Zwischenschritt, falls Tempo zaehlt.

## Umfang & Risiko (ehrlich)
Das ist ein Wand-Meshing-Eingriff -- spuerbar groesser als der U-Anker-Fix. Eigener Slice, eigener Test:
Ecken-Dichtheit im 3D (die zwei Waende teilen den Gehrungspunkt, keine Ueberlappung/kein Loch -- analog dem
2D-`wandBaender.test.ts`, projiziert auf den 3D-Grundriss). Kein Modell-/Schema-/Statik-Eingriff.

## Offen fuer Yama (Fach-Freigabe)
1. Ansatz "3D reused `wandBaender`" -- ja?
2. Tiefe A (voll) oder B (End-Gehrung zuerst)?
