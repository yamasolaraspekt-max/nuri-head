# Generator-Auftrag — 3D-Wandecken auf Gehrung (Ansatz A, Voll-Reuse)

**Fach-Freigabe Yama erteilt (Tor 1): Ansatz A.** Nur `auto/`-Branch, kein Push/Merge (Tor 2 = Yama).
Konzept: `docs/konzept/3d-wandecken-gehrung.md`. Reine Render-Geometrie — **kein Modell-/Schema-/Statik-Eingriff**.

## Ziel & Entscheidung
Der 3D-Wandkoerper bezieht seinen **Grundriss aus `wandBaender`** (gehrtes 4-Eck-Band je Wand, `geometry/
wallGeometry.ts`) statt aus Roh-Laenge x `thickness`. Damit sind 2D und 3D an den Ecken **exakt dieselbe
Geometrie** (eine Wahrheit) — Wand-Enden sitzen auf dem gemeinsamen Gehrungspunkt, keine Ueberlappung/kein Loch.

## Nahtstellen (wo genau)
- **Quelle Gehrung:** `wandBaender(waende)` — liefert je Wand `WandBand.ecken = [startLinks, endLinks,
  endRechts, startRechts]`. BYTE-REUSE, keine zweite Miter-Berechnung im 3D (kein `gehrungsEcken`-Nachbau).
- **WICHTIG (Nachbarschaft):** `wandBaender` berechnet Eckpunkte aus den **Nachbarwaenden** → dem 3D-Bau
  **ALLE Waende des Geschosses** uebergeben (Level-Menge), nicht Wand fuer Wand. Das ist die Korrektheits-Naht:
  die heutige `platziereWandQuader`-Kette arbeitet pro Wand und kennt die Nachbarn nicht.
- **Oeffnungen bleiben `segmentierung`:** `segmentiereWand` gibt weiter die Along-Achse-Segmente
  (voll/Bruestung/Sturz/Zwischen, `vonMm..bisMm`, `untenMm..obenMm`). Die Gehrung betrifft NUR die Wand-ENDEN;
  Segmentgrenzen an Oeffnungen bleiben rechtwinklig.
- **Meshing (Generator-Handwerk, three):** Wandkoerper = Extrusion des Band-Polygons auf Wandhoehe, Oeffnungen
  als vertikale Aussparung (`vonMm..bisMm` x `untenMm..obenMm`) subtrahiert. Aequivalent: die End-Segmente
  erhalten die abgeschraegte Endkappe aus den Band-Eckpunkten, Innensegmente bleiben rechtwinklig.
  Keine CSG-Bibliothek (Muster wie `segmentierung`: deterministische Polygone/Prismen). Betroffen:
  `renderers/three-d/segmentierung.ts`/`platzierung.ts`/`szene.ts` (Wand-Mesh-Bau) — NUR der Wandpfad.

## Kantenliste (wo es bricht)
- Wand ohne Nachbar an einem Ende (offenes Ende) → `gehrungsEcken` liefert dort keinen Miter → rechtwinklige
  Endkappe (Fallback), kein Absturz. (`wandBaender` gibt das schon so zurueck — pruefen, nicht neu erfinden.)
- ~kollineare/180°-Nachbarn → `gehrungsEcken` = null (entartet) → rechtwinklig, kein NaN.
- Oeffnung exakt am Wandende (Kante 1 aus `segmentierung`) → Zusammenspiel mit der gehrten Endkappe pruefen.
- T-Stoss/Kreuzung (3+ Waende an einem Punkt): `wandBaender` definiert das Verhalten → uebernehmen, nicht raten.

## Abnahmekriterien (Evaluator)
- Gates selbst: tsc 0 · schema 0 (Modell unberuehrt) · test gruen inkl. neuem Test.
- **Ecken-Dichtheits-Test** (die Kernzusicherung): fuer zwei im Winkel stossende Waende teilen die 3D-Wandkoerper
  an der Ecke den **gemeinsamen Gehrungspunkt** (Grundriss-Projektion deckt sich mit `wandBaender`), **keine
  Ueberlappung, kein Loch** — analog `wandBaender.test.ts`, auf die 3D-Grundrissprojektion. Reine Funktion, ohne Browser.
- Sicht (`?fixture=…&capture=1`, ggf. neue Ecken-Fixture): Ecken schliessen sauber wie im 2D-Grundriss.
- Regression: Oeffnungen (Tuer/Fenster) rendern weiter korrekt (Sturz/Bruestung); gerade Einzelwand unveraendert.

## Nicht im Scope
Statik/Tragend-Flag (Modell kennt es nicht), Auto-Giebel (eigener Slice), Deckenanschluss. Kein Beifang.
