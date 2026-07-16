# ADR-0001 (building-planner) — Kanonischer CAD-Modellvertrag: Wandreferenz, Wandanschlüsse, Flächenregelwerk

**Status:** ratifiziert durch Yama-Startblock „G1a / Alias 3D-1A" (2026-07-15).
**Heimat-App:** `ticket` · **Welle:** G1a (kanonischer Schema- und Modellvertrag) · **Bindet:** alle Folgewellen des 3D-Gebäudeplaners (G1b–G8+).
**Grundlage:** Masterprompt V3.3 (B9/B10/B12), CAD-/Architektur-Fachprüfung „freigabefähig_mit_auflagen" (`docs/cad-fachpruefung-modellvertrag.md`), Startblock G1a §5.1–§5.3.
**Verhältnis zu ADR-0001 (configuration):** eigener, bereichs-lokaler ADR-Zähler unter `docs/building-planner/adr/`; er steht unter der Quellenhierarchie A5 des Masterprompts und ändert `docs/configuration/adr/ADR-0001` nicht.

> Ein kombinierter ADR nach zulässiger Repo-Konvention (Startblock §14): drei klar gegliederte Entscheidungen CAD-E1, CAD-E2, CAD-E3.

---

## Kontext

Der kanonische Gebäudemodell-Vertrag v1 (`resources/schemas/building-model/v1.schema.json` + `App\Services\BuildingModel\CanonicalBuildingModelValidator`) ist die nachgelagerte, fachlich eindeutige Beschreibung eines Gebäudes (3D-0B Variante C: fachliche Schreibwahrheit bleibt `gebaeude_geometrie`). Die CAD-Fachprüfung hat drei Punkte als Planner-Entscheidung ausgewiesen. Diese ADR ratifiziert sie.

---

## CAD-E1 — Wandreferenz

**Entscheidung.** Jede Wand trägt eine **gerichtete Referenzlinie** (`reference_path.node_ids`, ≥ 2 Knoten, mm-Integer) und einen **Pflicht-`reference_line_type` ∈ {`axis`, `inner`, `outer`}**.

- Neue, manuell erzeugte Wände verwenden standardmäßig `axis` (dickenunabhängig, verlustfrei nach innen/außen ableitbar, IFC-Achsen-tauglich).
- Importierte/migrierte Wände dürfen `inner` oder `outer` führen.
- Die gespeicherte Bezugslinie ist die **einzige** geometrische Wandwahrheit; Innen- und Außenkante werden deterministisch abgeleitet, **nicht** als zweite Wahrheit persistiert (keine zusätzlichen Kantenpolygone).
- Die **Richtung** der Bezugslinie ist fachlich relevant (Öffnungsstation, linke/rechte Wandseite, Raumzuordnung, Außennormale) und darf nicht still umgekehrt werden.
- Dickenänderung erfolgt über eine **Fixkante** `thickness_change_anchor ∈ {reference_line, inner_face, outer_face}`, Standard `reference_line`. Die gewählte Fixkante bleibt geometrisch stehen; die andere Kante bzw. die Bezugslinie wird deterministisch neu berechnet — keine stille Verschiebung, keine automatische Änderung anderer Wände.
- Eine spätere Umstellung `inner`↔`axis`↔`outer` ist eine ausdrückliche Modelltransformation und erzeugt (ab G1b) eine neue Modellrevision.

**Vertragsdurchsetzung (G1a).** Schema: `reference_line_type`, `thickness_change_anchor`, `reference_path.minItems=2`, `exterior_side` (Innen/Außen-Seite, siehe CAD-A4) sind kodiert. Validator: `wall.missing_reference_line_type`, `wall.invalid_thickness_anchor`, `wall.missing_node`, `wall.degenerate`, `wall.segment_too_short`, `wall.exterior_side_conflict`.

## CAD-E2 — Wandanschlüsse

**Entscheidung.** Für Version 1 werden **Standardanschlüsse deterministisch abgeleitet** aus gerichteten Bezugslinien, Wanddicken, Wandtypen und dem zentralen Toleranzvertrag — L-, T-, X-, Stumpf-Anschluss und durchlaufende Wand.

- **Nicht** persistiert werden in v1: eigene Junction-Schnittpunktkoordinaten, separate Anschlussgeometrie, automatisch erzeugte Anschlussflächen als zweite Wahrheit.
- Eine Tabelle/Entität `building_wall_junctions` wird in G1a/G1b **nicht** als kanonische Geometrie vorgesehen.
- Spätere Sonderfälle sind nur als **`junction_override`** (Regel-Metadaten) zulässig: stabile Override-ID, beteiligte Wand-IDs, Anschlussart, Auflösungsregel, Herkunft, Begründung, Revision — **ohne** eigene, konkurrierende Wandkoordinaten (kein Dual-Write).

**Wirkung.** `building_wall_junctions` bleibt als Zielarchitektur-Kandidat (C2) leer/ungenutzt bis zu einem etwaigen späteren ADR. **G2c (Topologie/Räume) darf ohne diese ratifizierte Entscheidung nicht beginnen** — sie ist hiermit getroffen.

## CAD-E3 — Flächenregelwerk

**Entscheidung.** Das kanonische Modell und G1a führen **ausschließlich neutrale geometrische Größen** (geometrische Grundfläche, Nettofläche, Raumvolumen, Wandfläche, Öffnungsfläche).

- **Nicht** zulässig: ein Wert wird ohne Regelwerk als „Wohnfläche" bezeichnet; DIN-277- und WoFlV-Werte werden vermischt; WoFlV-Anrechnungsfaktoren werden still angenommen; Dachschrägen werden automatisch rechtlich bewertet.
- **DIN 277** (geometrische NGF/BGF) und **WoFlV** (rechtliche Wohnfläche) werden später als **getrennte, versionierte Berechnungsregelwerke** implementiert. Das Gebäudemodell liefert nur die geometrischen Operanden.
- Etwaige Felder wie `living_area`/`wohnflaeche` aus Vorentwürfen werden nicht als kanonische Geometriewahrheit übernommen (im Vertrag v1 nicht vorhanden).

---

## Konsequenzen

- **Positiv:** eindeutige Wandreferenz und -richtung (keine ½-Wanddicke-Drift), deterministische Anschlüsse ohne Zustands-Ballast, keine juristische Flächen-Fehlaussage, offener IFC-Pfad (Achse + spätere SpaceBoundary).
- **Kosten/Folgen:** Umstellung der Wandreferenz bzw. Junction-Overrides erzeugen ab G1b Modellrevisionen; die Innen/Außen-Ableitung (CAD-A4, Umlaufsinn) ist verbindlich mitzuführen.
- **Abgesichert durch:** Contract-/Validator-/Paritätstests unter `tests/Unit/BuildingModel/` (40 Tests) und 7 Gegenbeweise (Startblock §15.3).

## Nicht Teil dieser Entscheidung

Persistenz/Migration (G1b), Editor/Frontend (FG-1, G2), 3D-Viewer (G6), Dach/PV (G7/G8), LiDAR/Scan (G4L). Keine Änderung an `gebaeude_geometrie`.
