# QUELLEN

> Belegte Fundstellen. Was hier nicht steht, ist nicht belegt.
> Stand der Recherche: 07.08.2026

---

## Dachkonstruktion — Straight Skeleton

| Quelle | Was daraus stammt |
|---|---|
| [CGAL 6.2 — 2D Straight Skeleton and Polygon Offsetting](https://doc.cgal.org/latest/Straight_skeleton_2/index.html) | **Hauptquelle für F-020/F-021.** Definition, Winkelhalbierende, Kanten- und Spalt-Ereignis, Offset-Gleichung `w(ax+by+c)−t=0`, Eingabebedingung „gegen den Uhrzeigersinn, Löcher im Uhrzeigersinn", Dachextrusion und die Wasserablauf-Probe |
| [Straight skeletons with additive and multiplicative weights (ScienceDirect)](https://www.sciencedirect.com/science/article/pii/S0010448517301240) | Gewichtete Skelette → verschiedene Neigungen je Dachseite (Krüppelwalm) |
| [Straight Skeleton Computation Optimized for Roof Model Generation](https://www.researchgate.net/publication/336308549_Straight_Skeleton_Computation_Optimized_for_Roof_Model_Generation) | Optimierungen speziell für Dachmodelle |
| [Straight Skeleton — Seminarfolien MPI Informatik](https://resources.mpi-inf.mpg.de/departments/d1/teaching/ss10/Seminar_CGGC/Slides/09_Dinu_SSke.pdf) | Anschauliche Herleitung der Ereignistypen |
| [vterrain.org — Roofs](http://vterrain.org/Culture/BldCity/Roof/) | Übersicht der Dachformen und ihrer geometrischen Merkmale |

## Öffnungen und Körperverschneidung

| Quelle | Was daraus stammt |
|---|---|
| [three-bvh-csg (three.js Forum)](https://discourse.threejs.org/t/three-bvh-csg-a-library-for-performing-fast-csg-operations/42713) | Schnelle CSG-Umsetzung für three.js — Kandidat für F-031 |
| [three-csg-ts (npm)](https://www.npmjs.com/package/three-csg-ts) | TypeScript-CSG, einfacher, langsamer |
| [CSG.js](https://evanw.github.io/csg.js/) | Der Ursprungsalgorithmus (BSP-Bäume) |
| [Wandöffnungen in three.js — Praxisbericht](https://wawasensei.dev/tuto/threejs-wall-csg) | Konkrete Fallstricke bei Fenster-/Türöffnungen |
| [three.js Forum — window and door openings](https://discourse.threejs.org/t/how-to-create-window-and-door-openings-in-the-wall/20473) | Alternative ohne CSG: Wand als Polygon mit Loch |

## Gebäudedatenmodell

| Quelle | Was daraus stammt |
|---|---|
| [IFC-Schema: das IfcRelationship-Konzept (BibLus)](https://biblus.accasoftware.com/en/ifc-schema-the-ifcrelationship-concept/) | Die fünf Beziehungstypen; `IfcRelAggregates` (Gebäude→Geschoss), `IfcRelContainedInSpatialStructure` (Geschoss→Bauteil), `IfcRelConnectsPathElements` (Wand→Wand) |
| [Das IFC-Schema — Eigenschaften und Beziehungen (Domosoft)](https://www.domosoft.ch/blog/the-ifc-schema-properties-relationships-part-3/) | Aufbau einer IFC-Datei |
| [IFC-Format und Open BIM (BibLus)](https://biblus.accasoftware.com/en/ifc-format-and-open-bim-all-you-need-to-know/) | Überblick, Austauschformat |
| [Understanding IFC Files (ArchitectsWhoCode)](https://architectswhocode.com/what-an-ifc-file-actually-contains-in-simple-terms/) | Was eine IFC-Datei tatsächlich enthält |
| [BIM Holzbau — IFC-Datenschema (cadwork)](https://kb.cadwork.ch/en/3d-bim/manual/1016/structure-ifc-data-schema) | Struktur aus Sicht des Holzbaus |

> **Offene Lücke:** `IfcRelVoidsElement` (Wand hat Aussparung) und `IfcRelFillsElement`
> (Fenster füllt Aussparung) sind in den gelesenen Quellen nicht ausreichend
> beschrieben. **Muss nachrecherchiert werden, bevor W-04 geschnitten wird** —
> es ist genau die Modellierung, die W-04 braucht.

## Dachneigung — Rechenregeln

| Quelle | Was daraus stammt |
|---|---|
| [Dachneigung berechnen — Grad, Prozent, Sparrenlänge](https://rechner-portal.de/bau-handwerk/dach-dachkonstruktion/dachneigung-rechner) | F-022: Umrechnung, Sparrenlänge |
| [Dach-Rechner — Neigung, Fläche, Firsthöhe](https://www.konverta.info/tools/haushalt/dach-rechner) | F-022, F-023: Firsthöhe, wahre Dachfläche |
| [Dachneigung berechnen (Stegplattenversand)](https://stegplattenversand.de/ratgeber/dachneigung-berechnen/) | Gegenprobe der Umrechnungsformeln |

## Fang und Genauigkeit

| Quelle | Was daraus stammt |
|---|---|
| [nanoCAD — Objektfang-Modi](https://nanocad.com/learning/online-help/nanocad-platform/object-snap-mode/) | Welche Fangarten ein CAD anbietet → F-041 Rangfolge |
| [nanoCAD — Fang und Raster](https://nanocad.com/learning/online-help/nanocad-platform/snap-and-grid-mode/) | Rasterfang, Verhältnis Raster/Fang |
| [Depence — CAD-Editor Snapping](https://help.depence.com/depence-construction/depence-editor-tools/cad-editor-snapping) | Fangverhalten in einem 3D-Editor |
| [Undo (Wikipedia)](https://en.wikipedia.org/wiki/Undo) | Kommando-Muster vs. Zustandskopie → A3 |

## Marktüberblick — was vergleichbare Planer können

| Quelle | Was daraus stammt |
|---|---|
| [Plan7Architect — 3D-CAD-Hausplaner](https://plan7architect.com/) | Funktionsumfang eines fertigen Hausplaners → Anforderungsliste F-01…F-20 |
| [cadvilla — Hausplan-Software](https://www.cadvilla.com/en/house_plan_software/) | Gegenprobe des Funktionsumfangs |
| [diehausplaner.com — 3D House Planner Master](https://www.diehausplaner.com/en/product/3d-house-planner-master-2/) | Werkzeugzuschnitt im deutschsprachigen Markt |
| [Capterra — Architectural CAD Software 2026](https://www.capterra.com/architectural-cad-software/) | Marktübersicht |

---

## Was noch fehlt

| Thema | Warum es gebraucht wird | Für Werkzeug |
|---|---|---|
| `IfcRelVoidsElement` / `IfcRelFillsElement` im Detail | Öffnungsmodellierung | W-04 |
| Raumerkennung aus Wandgraph (Zyklensuche in planaren Graphen) | Räume automatisch finden | W-05 |
| Treppenformeln (Schrittmaßregel `2s + a = 63 cm`, Steigungsverhältnis) | Treppenbau | W-09 |
| Sonnenstandsberechnung (Deklination, Stundenwinkel, Höhe/Azimut) | Verschattung, PV | W-19 |
| Bauordnungsrechtliche Mindestmaße (Raumhöhe, Fensterfläche) | Prüfwerkzeug | W-18 |
