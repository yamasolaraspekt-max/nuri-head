# KONZEPT — Hausplaner Golden Path: geführter Bauwerksprozess, Bodenplatte ≠ Zwischendecke

```yaml
zustand: "KONZEPT (gedacht, nicht gebaut) — Yama 21.08.2026 spaet, Wortlaut uebernommen"
spur: "Hausplaner-PRODUKT — Bau erst nach TESTBEREIT (Abschlussmodus Phase 10); Planner-Arbeit ohne Code ist jetzt erlaubt und laeuft: Modell-/Command-Plan Bodenplatte + Abhaengigkeitsmatrix (planner-architect, read-only)"
kernerkenntnis: "Bodenplatte und Zwischendecke duerfen nicht laenger als dasselbe Bauteil behandelt werden — der Bestand hat nur EINE CeilingNode je Level auf der Wandoberkante; der Tooltip sagt 'Decke / Bodenplatte', technisch gibt es keinen Bodenplattenweg"
bestand_zeiger: "domain/scene.types.ts:48 (CeilingNode) · commands/applyCommand.ts:111 · app/tools/toolRegistry.ts:132 · docs/rollenkette/werkbank/02-WERKZEUGE/W-10-decke-und-boden/4-BEDIENUNG.md:57"
verwandt: "docs/konzept/dachschichten-modell-zielkonzept.md + dachschichten-reuse-matrix.md (Phasen 11-13 des Golden Path)"
```

## Verbesserter Planer- und Architektenablauf (Yama)

Kein starrer Einweg-Assistent, sondern ein geführter Bauwerksprozess:

| Phase | Planerhandlung | Zentrale Entscheidung | Rückwärtswirkung |
|---|---|---|---|
| 1 | Projektgrundlagen festlegen | Maßeinheit, Nordrichtung, Geschosse, Standardhöhen | Änderungen zeigen betroffene Bauteile |
| 2 | Erdgeschoss-Bezugsebene anlegen | Höhe ±0,00 und Bezugspunkt | Keine Geometrie wird gelöscht |
| 3 | Gebäudegrundfläche definieren | aus Kontur, Räumen oder manuell | Abhängige Bauteile werden zur Prüfung markiert |
| 4 | Bodenplatte erzeugen | Dicke, Material, Höhenlage, Erdberührung | Wände und Höhen erhalten Änderungsvorschau |
| 5 | Außen- und Innenwände EG erstellen | Wandaufbau und Bezugslinie | Räume, Öffnungen und Decken werden neu geprüft |
| 6 | Fenster und Türen einsetzen | Maße, Brüstung, Anschlag | Öffnungen bleiben an ihrer Wirtswand gebunden |
| 7 | Räume und Treppe planen | Treppenlage und Treppenauge | Zwischendecke wird automatisch als betroffen markiert |
| 8 | Zwischendecke erzeugen | Kontur, Dicke, Aufbau, Öffnungen | OG-Höhe erhält eine Änderungsvorschau |
| 9 | Obergeschoss erzeugen | leer oder aus EG ableiten | Ableitung bleibt nachvollziehbar und lösbar |
| 10 | OG bearbeiten | übernommene oder eigene Geometrie | Dachkontur wird zur Prüfung markiert |
| 11 | Dachgrundform erzeugen | Form, Neigung, Traufe, First | Dachaufbauten und Schichten werden geprüft |
| 12 | Dachaufbauten einsetzen | Gaube, Dachfenster, Schornstein | Ausschnitte werden kontrolliert aktualisiert |
| 13 | Dachschichten aufbauen | Reihenfolge, Dicke, Material | Visualisierung folgt den echten Projektdaten |
| 14 | Prüfen und präsentieren | CAD, Konstruktion, Präsentation | Keine Modelldaten durch Ansichtswechsel verändern |
| 15 | Speichern und neu laden | verbindlicher Prüfstand | Identität des Projekts bestätigen |

## Notwendige Modellentscheidung (Yama — Empfehlung für den ersten vollständigen Ablauf)
- `CeilingNode` bleibt die **Zwischendecke auf der Wandoberkante**.
- Eine eigene additive `FoundationSlabNode` beziehungsweise `BodenplatteNode` wird ergänzt.
- Bodenplatte und Zwischendecke verwenden dieselben gemeinsamen Geometrie-, Schicht- und
  Materialfunktionen — bleiben aber fachlich getrennte Bauteile.
- Die Bodenplatte erhält eine explizite Höhenlage und die Randbedingung `erdberuehrt`.
- Fußbodenaufbau, tragende Platte und Bodenplatte dürfen nicht stillschweigend vermischt werden.
- Bestandsprojekte ohne Bodenplatte müssen weiterhin unverändert laden.
So wird kein zweites getrenntes System gebaut, aber die fachlich falsche Gleichsetzung beseitigt.

## Regeln für Vorwärts- und Rückwärtshandlungen (Yama)
**Vorwärts:** „Weiter" schließt eine Phase nur ab, wenn deren Mindestdaten gültig sind; genau eine
hervorgehobene Hauptaktion („Weiter: Bodenplatte prüfen", „Weiter: Erdgeschosswände", „Weiter:
Zwischendecke erzeugen", „Weiter: Obergeschoss ableiten"); optionale Details einklappbar.
**Zurück:** navigiert nur zur vorherigen Phase, löscht nichts, ist nicht Undo. Drei getrennte
Funktionen: **Zurück** (vorherige Phase) · **Undo/Redo** (letzte Modelländerung) · **Phase
zurücksetzen** (bewusst alle Änderungen einer Phase entfernen — erst nach Auswirkungsanzeige und
Bestätigung).
**Änderungen an früheren Phasen:** abhängige Bauteile werden nie unbemerkt überschrieben.
Beispiel: Bodenplattendicke 200→240 mm → EG-Bezugshöhe prüfen → Zwischendecke/OG-Lage betroffen →
Treppe/Dachhöhe möglicherweise betroffen → Vorschau → Anwender bestätigt. Entscheidungen:
abhängige Elemente aktualisieren · bisherige Position beibehalten · Bauteil von der Ableitung lösen ·
abbrechen. Jede abgeleitete Geometrie trägt sichtbare Herkunft („aus Gebäudegrundfläche abgeleitet",
„aus EG übernommen", „manuell gezeichnet", „nur Bounding-Box-Näherung", „nachträglich von Vorlage gelöst").

## Effizienz aus Sicht eines Architekten (Yama)
Drei stabile Bereiche: links Bauwerksstruktur + Prozessfortschritt · Mitte 2D/3D/Split · rechts
Eigenschaften. Phasenstatus: nicht begonnen · Entwurf · gültig · Prüfung erforderlich · abgeschlossen.
Besonders sinnvoll: rechteckige Bodenplatte mit einem Klick aus der Grundfläche · freie Kontur nur bei
Bedarf · EG als Vorlage für OG duplizieren (Auswahl: Wände, Öffnungen, Räume, Installationen) · Treppe
vor der Zwischendecke planen (Treppenauge entsteht automatisch) · Höhen ausschließlich aus einer
zentralen Berechnung · Geistervorschau vor Änderungen · zusammengehörige Aktionen als eine Undo-Einheit ·
benannter Prüfpunkt nach jeder Phase · Fehler am Bauteil anzeigen, aus der Fehlerliste hinspringen ·
Varianten als Projektvarianten, nicht per Undo/Redo-Chaos.

## Umsetzungs-Prompt (Yama, direkt weitergegeben an den Planner)
> Untersuche und verbessere den vollständigen Hausplaner-Golden-Path aus Sicht eines professionellen
> Planers und Architekten. **Implementiere nicht sofort**, sondern liefere zuerst Modellentscheidung,
> UX-Ablauf, Abhängigkeiten und prüfbare Abnahmekriterien. Der sichtbare Bauwerksaufbau erfolgt in der
> Reihenfolge: 1 Projektgrundlagen und EG-Bezugsebene · 2 Gebäudegrundfläche · 3 Bodenplatte ·
> 4 Erdgeschosswände · 5 Fenster und Türen · 6 Räume und Treppe · 7 Zwischendecke mit Treppenöffnung ·
> 8 Obergeschoss leer oder aus EG abgeleitet · 9 Obergeschosswände und Öffnungen · 10 Dachgrundform ·
> 11 Dachaufbauten und Dachausschnitte · 12 Dachschichten · 13 CAD-, Konstruktions- und
> Präsentationsansicht · 14 Speichern, schließen und neu laden · 15 Browserabnahme mit festen
> Referenzbildern.
> Bodenplatte und Zwischendecke sind fachlich verschiedene Bauteile; der Bestand unterstützt nur eine
> `CeilingNode`-Geschossdecke pro Level — eine Bodenplatte darf nicht als zweite Decke desselben Levels
> simuliert werden. Plane eine additive, migrationssichere Bodenplattenstruktur mit expliziter Höhenlage,
> Erdberührung, Dicke, Material und optionalen Schichten; teile Geometrie- und Renderingfunktionen mit
> der Geschossdecke, vermische ihre fachliche Bedeutung nicht.
> Definiere für jede Phase: Einstiegsvoraussetzung · Hauptaktion · notwendige Planerentscheidung ·
> automatisch ableitbare Daten · sichtbare Herkunft automatisch erzeugter Daten · Vorwärtsbedingung ·
> Zurück-Navigation · Undo-/Redo-Verhalten · Auswirkungen späterer Änderungen · Speichern-/Laden-Nachweis ·
> 2D-/3D-Abnahme. „Zurück", „Undo" und „Phase zurücksetzen" sind getrennte Funktionen; Zurück löscht
> nichts; frühere Änderungen verändern abhängige Bauteile nie still — erst Auswirkungsvorschau, dann
> Aktualisieren / Beibehalten / Ableitung lösen / Abbrechen. Abhängigkeitsstatus `PRÜFUNG ERFORDERLICH`:
> Änderung an Bodenplatte, Wandhöhe, Zwischendeckendicke, Treppe oder OG-Kontur markiert alle
> betroffenen Geschosse, Treppen, Decken, Dächer sichtbar.
> Höhenkette mit genau einer technischen Quelle: Bezugshöhe → Bodenplattenoberkante → Wandhöhe →
> Zwischendeckenoberkante → nächstes Geschoss → Dachauflager. Eine Zwischendeckenänderung aktualisiert
> die OG-Höhe nur über eine bestätigte Command-Änderung. Die Treppenöffnung entsteht automatisch, sofern
> nicht bewusst manuell definiert; die Bodenplatte erhält nur ausdrücklich geplante Durchbrüche.
> Dachschichten sind reale Projektdaten (Tragwerk, Schalung/Unterspannbahn, Dämmung, Konterlattung,
> Traglattung, Dacheindeckung) mit Reihenfolge, Dicke, Material, Sichtbarkeit, dauerhafter Speicherung;
> Sichtbarkeit, Solo, Transparenz, Explosion gehören ausschließlich zum Ansichtszustand. Drei
> Darstellungen: CAD · Konstruktion · Präsentation. Bedienung: Prozessnavigation, kontextbezogener
> Eigenschaftenbereich, eine Hauptaktion pro Phase, intelligente Standardwerte mit sichtbarer Herkunft,
> Vorschauen vor abhängigen Änderungen, Tastaturbedienung, phasenweise Undo-Transaktionen, anklickbare
> Fehlerhinweise. Abnahme mit festem zweigeschossigem Referenzhaus (Maße sind Testdaten) — Bodenplatte,
> EG, Treppe, Zwischendecke, OG, Dach, Dachaufbauten, Dachschichten.
> **Golden Path fertig erst, wenn:** Bodenplatte und Zwischendecke gleichzeitig korrekt existieren ·
> fachlich und technisch unterscheidbar gespeichert · Zwischendecke hat das Treppenauge · Geschosshöhen
> aus realen Bauteildicken · Änderungen zeigen ihre Abhängigkeiten · Undo/Redo phasenübergreifend ·
> Bestandsprojekte ohne neue Felder laden weiter · Speichern/Neuladen verliert nichts · 2D und 3D zeigen
> dieselbe Konstruktion · Dachschichten einzeln schaltbar und explodierbar · Explosionswert `0` stellt
> exakt die Normalgeometrie wieder her · ein unabhängiger Evaluator wiederholt den Ablauf im Browser ·
> CAD-/Konstruktions-/Präsentationsansicht per festen Screenshots verglichen.
> **Liefere zuerst einen migrationssicheren Modell- und Command-Plan für die Bodenplatte und erst nach
> dessen unabhängiger Freigabe die Umsetzung des vollständigen Golden Paths.**

## Registrierung (Yama, 21.08. spät): Folgeauftrag `ENTWURF`, vier Scheiben
**GP-0** Modellgrundlage (Bodenplatte, Zwischendecke, Höhenbezüge, Migration, Command-Verträge) ·
**GP-1** Planerablauf (Grundfläche → Bodenplatte → EG → Treppe → Zwischendecke → OG → Dach) ·
**GP-2** Dachkonstruktion (physische Dachschichten) · **GP-3** Golden-Path-Abnahme (Undo/Redo,
Speichern/Laden, 2D/3D, Browser, feste Referenzbilder). Blatt:
[`folgeauftrag-golden-path-GP-0-bis-3.md`](../auftraege/folgeauftrag-golden-path-GP-0-bis-3.md).
**Bis zum Abschlussurteil `ENTWURF`; kein Generator beginnt damit.** Der Abschlussmodus behält
uneingeschränkt Vorrang.

## Einordnung Dirigent
**Jetzt (erlaubt, ohne Code):** Modell-/Command-Plan Bodenplatte + Abhängigkeitsmatrix + Phasendefinition
durch `planner-architect` (read-only, am Bestand gemessen) → Ablage als Konzept → unabhängige Freigabe
(Plan-Prüfer). **Bau:** erst nach `TESTBEREIT` (Abschlussmodus Phase 10), als erster Produkt-Slice nach
Bodenplatten-Freigabe, vor/mit dem Dachschichten-Schnitt in der Reihenfolge des Golden Path.
