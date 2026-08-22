# FOLGEAUFTRAG — Hausplaner Golden Path, vier Abnahmescheiben GP-0 … GP-3

```yaml
zustand: "ENTWURF — NICHT VOR DEM ABSCHLUSSURTEIL (TESTBEREIT mit Test-SHA) aktivieren; kein Generator beginnt damit"
erteilt_von: "Yama, 21.08.2026 spaet — 'Bitte nur einplanen und noch nicht bauen'; fachliche Richtung 'Bodenplatte vor Zwischendecke' ist erteilt"
spur: "Hausplaner-PRODUKT (getrennt von Sicherheit und Governance)"
konzept: "docs/konzept/golden-path-bauwerksprozess.md (Wortlaut) · docs/konzept/dachschichten-modell-zielkonzept.md + dachschichten-reuse-matrix.md (GP-2)"
planner_vorarbeit_erlaubt: "ja, ohne Code — Modell-/Command-Plan Bodenplatte + Abhaengigkeitsmatrix + Phasendefinition (planner-architect, read-only, laeuft); Freigabe durch Plan-Pruefer; dann ruht alles bis TESTBEREIT"
kette_nach_aktivierung: "Planner -> Plan-Pruefer -> Generator -> unabhaengiger Evaluator -> Browserabnahme -> Integrator"
```

## Fachliche Festlegung (Yama)
1. Zuerst wird die Gebäudegrundfläche bestimmt. 2. Danach wird eine echte Bodenplatte erstellt.
3. Darauf folgen Erdgeschosswände, Öffnungen, Räume und Treppe. 4. Danach wird eine eigenständige
Zwischendecke mit Treppenöffnung erstellt. 5. Erst anschließend wird das Obergeschoss erzeugt oder
aus dem Erdgeschoss abgeleitet. 6. Danach folgen Dachgrundform, Dachaufbauten, Dachschichten und
die drei Ansichtsmodi CAD, Konstruktion und Präsentation.
**Bodenplatte und Zwischendecke dürfen nicht als dasselbe Bauteil behandelt werden.** Der Bestand
unterstützt nur eine Geschossdecke pro Level (`CeilingNode`, `scene.types.ts:48`); deshalb legt der
Planner **vor jeder Umsetzung** eine additive, bestandskompatible Modellentscheidung für eine
eigenständige Bodenplatte vor. Änderungen an früheren Planungsschritten dürfen spätere Bauteile
niemals still überschreiben: Auswirkungsvorschau, bestätigte Aktualisierung, getrennte Funktionen
**Zurück / Undo-Redo / Phase zurücksetzen**.

## Die vier Abnahmescheiben
| Scheibe | Inhalt | Abnahme |
|---|---|---|
| **GP-0 Modellgrundlage** | Bodenplatte (additiv: `FoundationSlabNode`/`BodenplatteNode`, Höhenlage, `erdberuehrt`, Dicke, Material, optionale Schichten, Herkunft, nur ausdrückliche Durchbrüche), Zwischendecke (bleibt `CeilingNode` auf Wandoberkante), **Höhenbezüge als EINE Quelle** (Bezugshöhe → Bodenplatten-OK → Wandhöhe → Zwischendecken-OK → nächstes Geschoss → Dachauflager), Migration (Bestandsprojekte ohne neue Felder laden unverändert), Command-Verträge (granular, Undo je Schritt, Vorschau-vor-Commit für abhängige Änderungen) | Zod/JSON-Schema-Tests, Migrations-Test, Command-Tests, **kein Renderer-Umbau** |
| **GP-1 Planerablauf** | Grundfläche → Bodenplatte → EG → Treppe → Zwischendecke (Treppenauge automatisch) → OG (leer/abgeleitet, Herkunft sichtbar) → Dach; Phasenstatus (nicht begonnen/Entwurf/gültig/**PRÜFUNG ERFORDERLICH**/abgeschlossen); eine Hauptaktion je Phase; Abhängigkeitsmarkierung | Tests je Phase, 2D/3D-Konsistenz, Abhängigkeits-Vorschau |
| **GP-2 Dachkonstruktion** | physische Dachschichten mit Material, Dicke, Reihenfolge, Speicherung (Reuse-Matrix: `schichten`-Muster R1, Commands nach `aufbauten`-Muster R2, Playground nur Ideen R3, Ebenenpanel/Renderer R5; Nicht-Ziele: `dachformVorlagen.ts` deckungsneutral, `holzBauteile`/`holzMengen`/`sparrenBerechnung` nicht verdrahten) | Ansichtsprofil getrennt von Konstruktion; Explosion `0` = Normalgeometrie |
| **GP-3 Golden-Path-Abnahme** | Undo/Redo phasenübergreifend, Speichern/Laden verlustfrei, 2D/3D gleiche Konstruktion, Browser mit festem zweigeschossigem Referenzhaus (Testdaten, keine bauliche Empfehlung), feste Referenzbilder für CAD/Konstruktion/Präsentation, unabhängiger Evaluator wiederholt den Ablauf | die Fertig-Kriterien aus dem Konzept, einzeln belegt |

## GP-0 Planner-Output liegt vor (21.08.)
[`docs/konzept/golden-path-gp0-modellplan-bodenplatte.md`](../konzept/golden-path-gp0-modellplan-bodenplatte.md)
— Ist-Höhenkette gemessen (drei Rechnungen, `naechsteEtageElevationMm` tot, `Kopfrahmen.tsx:172`
liest Level-Vorgabe statt echter Decke), additiver Modell-/Command-Plan, `berechneHoehenkette` als
eine Quelle, Abhängigkeitsmatrix (on-demand berechnet, kein gespeicherter Status — Lehre aus K-2),
15 Phasen, Referenzhaus-Fixture, 7 messbare Abnahmekriterien, **9 Fachfragen für Yama** (Bezugshöhe,
Erdberührung, Feldname `herkunft`→`polygonQuelle`, eine Platte je Level oder Gebäude, …) — zu
klären **vor dem GP-0-Bau**, keine Eile. Nächster Schritt: Plan-Prüfer-Freigabe des Plans, dann Ruhe
bis `TESTBEREIT`.

## Was JETZT passiert (und nur das)
Planner-Vorarbeit ohne Code: Modell-/Command-Plan Bodenplatte, Höhenkette-Ist-Messung,
Abhängigkeitsmatrix, Phasendefinition 1–15, Referenzhaus-Fixture-Vorschlag, Abnahmekriterien GP-0 —
als Konzept abgelegt, vom Plan-Prüfer unabhängig freigegeben. **Danach ruht der Folgeauftrag auf
`ENTWURF`, bis das Abschlussurteil mit Test-SHA vorliegt.** Kein Produktcode wird bis dahin verändert.
