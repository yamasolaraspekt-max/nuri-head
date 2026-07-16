# AP-4 — Geometrie- / 3D-Gebäudemodell — Belastbarkeits-Validierung (read-only)

**Stand:** 2026-07-14 · **read-only** · **kein Bau, kein Commit, kein Push, keine Migration, keine neue Tabelle, kein 3D, kein Three.js-Prototyp, kein playground-Code kopiert, keine eigene Geometrieformel erfunden.**
**Zweck:** Vor jeder 3D-/Geometrie-Baustelle klären, was **mathematisch belastbar** vorhanden ist und was fehlt — als Prüfung des Masterprompts „3D-Gebäudeplaner / digitales Gebäudemodell". **Kein Bauauftrag.**
**Quellen (firsthand, read-only, 2026-07-14):** ticket-Code (`app/Services/Heizlast/GeometrieAbleitungService.php`, `RaumHuelleService.php`, `app/Services/RoofAreaEstimator.php`, `GrundrissController`, `raum_geometrien`/`gebaeude_geometrie`-Migrationen, `tests/`); playground `src/utils/*` + `DachplanerProPage.tsx` + `PvBelegungExtractor`/`RoofTemplateFeatureExtractor`; wberechnung `GeometrieAbleitungService`/`RaumHuelleService`/`HeizlastRechner` + Geometrie-Tests.

---

## 1. Kurzfazit

> **Der Geometrie-Rechenkern existiert und ist in Teilen echte, getestete Mathematik — aber als *führende Wahrheit* für Flächen/3D ist heute NICHTS belastbar.**
> - **ticket:** korrekte Shoelace-Fläche + DIN-EN-12831-Öffnungsabzug + Volumen — aber **die Referenztests wurden NICHT nach ticket mit-transplantiert** (Geometrie in ticket lokal *unbewiesen*, belastbar nur „per Identität" zur getesteten wberechnung-Quelle). Geometrie hängt am **HeizlastProjekt-Stand-alone-Tool, nicht versioniert, nicht am Objekt/Anforderungsprofil**. Dach: `RoofAreaEstimator` hat einen **Web-Mercator-Flächenfehler** und ist nur an den KI-Chat verdrahtet. three.js-Editoren = **Browser-Prototyp ohne Persistenz-Naht**.
> - **playground:** reicher, handgeschriebener Util-Kern (320 Tests) für Dach-Trigonometrie/Fläche/Öffnung/Modulplatzierung; **kWp wird server-seitig belastbar aus der Modulzahl abgeleitet**. **ABER:** tragende UI/Engine = `@ts-nocheck`-Prototyp; **kein Volumen, kein Azimut, keine Geo-Libs**; die **genaue Fläche entsteht nur im Browser** — server-seitig wird nur eine bewusst als „kein Aufmaß" deklarierte **Bounding-Box-Näherung** (für L/T/U/Walm systematisch zu hoch) persistiert; **keine Versionierung**.
> - **wberechnung:** dieselbe Heizlast-Raumgeometrie **mit** Referenztests (byte-genau) + DIN-Beleg — Dach/PV-Geometrie **fehlt komplett** (`azimut_grad` wird nur mitgeführt, nicht gerechnet).
>
> **Konsequenz:** Weder Modulanzahl aus Dachfläche noch Heizlast aus Raumgeometrie darf *heute* echte Ergebnisse liefern, bevor (a) eine **Polygon-Topologie-Validierung** als Pflicht-Gate, (b) **Referenztests in ticket**, (c) **versionierte Geometrie-Persistenz am Objekt/Kern** und (d) eine **belastbare Dachflächen-/Azimut-Quelle** ergänzt sind.

---

## 2. Was ist belastbar vorhanden?

| Baustein | Ort | Beleg |
|---|---|---|
| **Shoelace-Flächenformel** (als reine Funktion) | ticket `GeometrieAbleitungService::polygonFlaecheM2` / wb identisch / pg `polygonFlaecheM2` | Gauß-Trapezformel, mm→m² (ticket/wb) bzw. m (pg), `abs`, `n<3`→0. Mathematisch korrekt für **einfache** Polygone. |
| **Öffnungsabzug Heizlast** (DIN EN 12831-1 §6) | ticket/wb `RaumHuelleService::effektiveBauteile` | Netto-Wandfläche, `max(0,…)`-Clamp. wb-Referenztest: Wand 30−6=24 m². |
| **Raumvolumen** (Lüftungsheizlast) | wb/ticket `HeizlastRechner` | `V = grundflaeche·hoehe`, `Φ_V=0,34·n·V·Δθ`, getestet (H_V=10,63 W/K). |
| **Dach-Trigonometrie** (Neigung/Fläche/Kehle) | pg `dachformVorlagen.ts`, `dachVerschneidung.ts`, `dachUForm.ts` | echte Trig (`/cos`, `·tan`, `atan(tan a/√2)`), 105+11+10 Tests, „geplant"-Status statt Fake-Geometrie. |
| **Dach-Öffnungsabzug** (Gauben/Ausschnitte) | pg `dachAusschnitt.ts::berechneAusschnitt` | `netto=max(0,brutto−oeffnung)`, ≥90 %-Guard, „nur wenn sicher", 71 Tests. |
| **Konvex-/Selbstschnitt-Validierer** (vorhanden, aber nicht erzwungen) | pg `dachAusschnitt.ts::istSichereKonvexeFlaeche/konvexeTeilflaechenSicher` | Umlaufzahl=±2π, Flächen-Verifikation der Slab-Zerlegung. Rigoros. |
| **kWp aus Modulbelegung** (server-seitig) | pg `PvBelegungExtractor::extract` | kWp = platzierte Module × Watt/1000, `null` bei unbekanntem Watt, 4 Tests. **Einziger server-belastbarer Geometrie-Folgewert.** |
| **Heizlast-Engine auf gegebenen Flächen** | ticket/wb `HeizlastRechner` | byte-genau, DIN EN 12831-1, „Wohnzimmer"-Referenztest (nur in wb). |

---

## 3. Was ist teilweise vorhanden?

- **Polygon-Validierung:** rigorose Werkzeuge existieren (pg), aber **nicht als Pflicht-Gate vor jeder Flächenberechnung**; ticket/wb prüfen nur `count(polygon)≥3` + `hoehe between 1000..6000`. Kollineare/doppelte Punkte, Selbstschnitt, Geschlossenheit werden in `polygonFlaecheM2` **nicht** abgefangen.
- **Öffnungsabzug (Heizlast):** korrekt für Transmissions-Summe, aber **aggregierter Flächenfaktor über alle Außenwände**, nicht wandsegment-genau; Zuordnung „Fenster↔Wand" geht verloren; Öffnung>Wand wird **still** auf 0 geklemmt (keine Warnmarkierung).
- **Geometrie-Persistenz:** ticket persistiert `raum_geometrien` + abgeleitete `heizlast_bauteile` — aber am **HeizlastProjekt** (Stand-alone-Tool), **nicht** am Objekt/`Anforderungsprofil`, **destruktiv** (`bauteile()->delete()` bei Neuberechnung), **ohne Versionierung/Historie**.
- **Dach-Trig (pg):** Neigung/Fläche/Kehle belastbar, aber **nur für Formen, die die Engine wirklich baut**; Rest ist ehrlich „geplant".

---

## 4. Was ist nur Prototyp?

- **playground `DachplanerProPage.tsx`** (3786 Z., `@ts-nocheck`, `eslint-disable`) + `src/planer/main.tsx` + `roofTypes.ts` — die tragende UI/`RoofEngine` (three.js) ist **typ-ungeprüfter Gemini-Prototyp** (eigener Header: „Härtung/Backend-Anbindung als Folgeschritt"). Die *Utils dahinter* sind kein Prototyp; die *UI/Engine* schon.
- **ticket three.js-Editoren** `roof_config/roof.blade.php`, `solar/configuration/configure.blade.php` — **keine Persistenz-Naht im View gefunden** → als Browser-Prototyp einzustufen (bis Gegenbeweis). Zusätzlich Cruft-Kopien (`config.blade copy 2/3.php`, `pvgis_details.blade copy.php`).
- **playground Server-Dachfläche** `RoofTemplateFeatureExtractor::roofArea` — bewusst „grobe Dachfläche, kein Aufmaß" (Bounding-Box/cos), **ungetestet**, für L/T/U/Walm zu hoch.

---

## 5. Was fehlt komplett?

- **Polygon-Topologie-Validierung als durchgängiges Pflicht-Gate** (Selbstschnitt/Geschlossenheit/Entartung) — nirgends erzwungen vor Flächen.
- **Referenztests in ticket** für `polygonFlaecheM2`/`RaumHuelleService`/`GeometrieAbleitungService` — in wberechnung vorhanden, **in ticket NICHT** (`tests/Unit/GeometrieAbleitungTest`/`GrundrissEditorTest` fehlen in ticket).
- **Azimut-Berechnung** (Himmelsrichtung einer Fläche) — überall nur **manuelle Eingabe** bzw. aus PVGIS-API; nirgends aus Geometrie abgeleitet (`azimut_grad` wird nur gespeichert/durchgereicht).
- **Belastbare Dachflächen-Quelle** — ticket `RoofAreaEstimator` (Web-Mercator-Flächenfehler + Grundriss statt Dach), pg (nur Browser + BBox-Server-Näherung), wb (fehlt). **Keine** valide geneigte Dach-Ist-Fläche.
- **Volumen im 3D-Planer** (pg hat keins; Heizlast-Volumen existiert getrennt).
- **Versionierte Geometrie am Objekt/Kern** (Historie/Snapshot des Aufmaßes je Kunde/Objekt).
- **Robuste Geometrie-Libraries** (turf/clipper/martinez/earcut) — überall **handgeschrieben**.
- **Geschoss-/Mehrraum-Gebäudemodell** über einen Raum hinaus — ticket `GrundrissController` persistiert **genau einen Raum** je Projekt; `raum_geometrien.geschoss` existiert als Feld, aber kein Mehrgeschoss-/Mehrraum-Modellierungsfluss.
- **IFC/DWG-Import**, echtes Bauteil-Schichten-3D, Fassaden-/Fenster-Aufmaß-Domäne.

---

## 6. Mathematik-/Geometrie-Risiken (kritisch)

1. **Ungegatete Fläche (GIGO):** `polygonFlaecheM2` liefert für ein **selbstschneidendes** Polygon eine mathematisch definierte, physikalisch **falsche** Fläche — ohne Warnung. Kein Pflicht-Validierungs-Gate davor.
2. **Web-Mercator ist nicht flächentreu:** `RoofAreaEstimator::polygonAreaMeters` rechnet Shoelace auf Mercator-Metern → Flächen bei ~50°N um **~Faktor 2 überschätzt** (keine cos²(lat)-Korrektur). **Nicht belastbar** für absolute Fläche/Modulzahl.
3. **Grundriss ≠ Dachfläche:** OSM-Pfad liefert Gebäude-**Footprint**, keine geneigte Dachfläche; Decke/Boden = Grundfläche (Flachprojektion, kein Neigungsfaktor).
4. **Bounding-Box-Näherung (pg-Server):** persistierte `roof_area_m2` = `length×width/cos` → für L/T/U/Walm **systematisch zu hoch**; die genaue Polygonfläche bleibt im Browser.
5. **Stiller Clamp:** Öffnung>Wand → Netto=0 ohne Fehler/Markierung (Heizlast).
6. **Einheiten-Mismatch bei Zusammenführung:** ticket/wb = **mm**; playground = **m/cm/Grad gemischt**. Ein Zusammenführen ohne einheitliches mm-/Rundungs-Konzept erzeugt Rundungs-/Skalierungsfehler.
7. **Azimut fehlt für PV-Ertrag:** ohne belastbaren Flächen-Azimut ist jeder PV-Ertrag aus Geometrie unbelegt.
8. **Keine Geo-Lib:** alle Formeln handgeschrieben → jede Kante ist Eigenverantwortung; ohne Referenztests kein Beweis.

---

## 7. Datenmodell-Lücken

- **Geometrie nicht am Objekt/Kern:** `raum_geometrien` hängt an `heizlast_raeume`/`HeizlastProjekt`, **nicht** an `lead_alternative_adds`/`Anforderungsprofil`. Der `anforderungsprofile.gebaeude_geometrie`-JSON-Hook **existiert** (versioniert!), wird aber vom Grundriss-Tool **nicht** befüllt.
- **Ein-Raum-Beschränkung:** GrundrissController = 1 Projekt : 1 Raum. Kein Geschoss/Mehrraum-Aggregat.
- **Kein Dach-/Fassaden-/Öffnungs-Datenmodell** am Objekt (nur Heizlast-Bauteile + pg-`roof_templates`-Bibliothek, nicht objektgebunden/versioniert).
- **Keine Geometrie-Versionierung/Historie** (destruktives Neu-Erzeugen der Bauteile).

---

## 8. UI/UX-Lücken

- **Zeichenoberfläche:** vorhanden — ticket SVG-Grundriss (`grundriss_editor`), ticket three.js (`roof_config`, `solar/configuration`), pg 3D-Planer (React). Aber **uneinheitlich** (SVG vs. 2× three.js vs. React) und teils Prototyp/Browser-only.
- **2D/3D-Umschaltung, Bauteilpalette, Eigenschaftsleiste, Validierungs-/Status-Anzeige:** im pg-Planer ansatzweise vorhanden (Prototyp), in ticket **nicht** als konsistente, Ticket-CI-konforme Oberfläche.
- **Ticket-CI:** pg-Design ist **keine** Vorlage; Alpine nur in 2 erlaubten Scopes; React ist stack-fremd. Aus playground darf **nur die Idee/das Util-Verhalten** übernommen werden, nicht Code/Design.

---

## 9. Import-Lücken

| Format | Ticket | playground | wberechnung |
|---|---|---|---|
| **PDF** | Plan-Upload (`plan_uploads`, `PlanUploadController`) vorhanden | — | Underlay/Kalibrierung |
| **Bild** | Plan-Upload/Underlay | — | Underlay |
| **DXF/DWG** | teils (Grundriss-Underlay) | — | `MassstabVorschlagService` (DXF-Maßstab, nur wb) |
| **IFC** | **fehlt** | fehlt | fehlt |
| **SVG** | SVG-Grundriss-Editor (nativ) | — | — |

→ **Kein produktiver, validierter CAD-Import** (IFC gar nicht). Import ist Underlay/Maßstab, **keine** geometrie-erzeugende, validierte Pipeline.

---

## 10. Abhängigkeit zu AP-3a / Objekt-Konfigurationssicht

- AP-3a (Objekt-Sicht) aggregiert read-only je Objekt — ein späteres Geometriemodell würde dort als **Datenquelle je Gewerk** einspeisen (Heizlast-Hülle, PV-Dach). Heute liefert AP-3a Reife/Belastbarkeit, **keine** Geometrie.
- Das **richtige Andockmuster** ist der bereits versionierte `anforderungsprofile.gebaeude_geometrie`-Hook am Objekt (Option E, AP-3): Geometrie gehört ans Objekt/Profil, nicht ans HeizlastProjekt-Tool. AP-4 bestätigt: die Klammer (AP-3) ist die richtige Heimat, die Geometrie muss noch dorthin **umziehen** (eigener späterer Slice, ggf. Migration → STOPP).
- **Kopplung:** AP-4 hängt an AP-3 (Klammer), **nicht** umgekehrt. Kein Geometrie-Bau vor validierter Basis.

---

## 11. Was darf als erster kleiner Slice gebaut werden?

**AP-4a — Belastbarkeits-Fundament der VORHANDENEN Geometrie (test-/spec-only, keine Produktionslogik-Änderung, keine neue Formel, kein 3D, keine Migration):**
- **Referenztests in ticket** für die bereits vorhandene Geometrie-Mathematik: `polygonFlaecheM2` (5×5=25 m², L-Form=Summe Rechtecke, Trapez), `RaumHuelleService` (Wand 30 − Fenster 6 = 24 m² netto; Öffnung>Wand → 0 + Markierungsbedarf), `GeometrieAbleitungService::ausGeometrie` (Äquivalenz Geometrie-Raum ↔ Maskenraum über die Engine) — **transplantiert/adaptiert aus den wberechnung-Tests**, damit ticket lokal beweist, was heute nur „per Identität" belastbar ist.
- **Read-only Validierungs-Befund** (Spec, kein Bau): welche Polygon-Topologie-Prüfungen fehlen und wo sie als Pflicht-Gate hingehören.
- **Scope-Grenzen:** nur Testdateien + Doku; **keine** Änderung an `GeometrieAbleitungService`/`RaumHuelleService`/`RoofAreaEstimator`, **keine** neue Geometrieformel, **kein** Validator-Einbau (nur Befund), **kein** 3D, **keine** Migration.
- Rückfall: reine Testdateien → löschen.

*(Optionaler Folge-Slice AP-4a-2, separat: ein reiner, ungenutzter Polygon-Validator als pure Funktion + Tests — noch nicht verdrahtet. Nur nach eigener Freigabe.)*

---

## 12. Was darf ausdrücklich noch nicht gebaut werden?

- **Kein 3D-Bau / kein Three.js-Prototyp / kein playground-Code kopieren.**
- **Keine Modulanzahl/kWp aus unvalidierter Dachfläche** (Mercator/BBox verboten als Wahrheit).
- **Keine Heizlast aus unvalidierter Raumgeometrie** (ohne Topologie-Gate + ticket-Referenztests).
- **Keine eigene neue Geometrieformel** (die vorhandene Shoelace/DIN-Logik wird nur belegt/getestet, nicht ersetzt).
- **Keine Migration / keine neue Geometrie-Tabelle** (Umzug ans `gebaeude_geometrie`/Objekt ist ein späterer, zu begründender Posten → STOPP).
- **Kein Azimut „erfinden"**, kein Angebot/Montageplan aus 3D, kein IFC/DWG-Bau.
- **Keine Zusammenführung ticket(mm) ↔ playground(m/cm)** ohne vorher fixiertes Einheiten-/Rundungskonzept.

---

## 13. Referenz-Testfälle, die wir brauchen

1. **Rechteck 5×5 → 25,00 m²** (Shoelace-Grundbeweis).
2. **L-/T-/U-Form → Summe der Teilrechtecke** (beweist: BBox ist falsch, Polygon ist richtig).
3. **Öffnungsabzug:** Wand 30 m², Fenster 6 m² → Netto 24 m²; zweites Fenster additiv.
4. **Öffnung > Wand** → Netto 0 **und** Pflicht-Markierung (kein stiller Clamp).
5. **Selbstschneidendes Polygon** → **abgelehnt** (nicht still eine Fläche liefern).
6. **Entartung** (kollineare/doppelte Punkte, Nullfläche) → abgelehnt/0 mit Markierung.
7. **Geneigte Dachfläche:** Grundriss A, Neigung 30° → Ist-Fläche = A/cos(30°) (bekannter Sollwert); **nicht** BBox, **nicht** Mercator.
8. **Azimut aus zwei Punkten** (sobald gebaut) → bekannter Kompasswinkel.
9. **Modulbelegung** auf definiertem Rechteck-Dach → erwartete Modulzahl + kWp (server-abgeleitet).
10. **Volumen** Raum 25 m² × 2,5 m = 62,5 m³ → H_V-Sollwert (DIN).
11. **Einheiten:** mm-Polygon vs. m-Polygon → identische m²-Fläche (Umrechnungs-Beweis).

---

## 14. Empfehlung für AP-4a / AP-4b / AP-4c

| Phase | Inhalt | Voraussetzung | Migration? |
|---|---|---|---|
| **AP-4a** (erster Slice, s. §11) | Referenztests der vorhandenen ticket-Geometrie + Validierungs-Befund (test/spec-only) | AP-3a (da) | **nein** |
| **AP-4b** (nach 4a, eigener Startblock) | Polygon-Topologie-**Validierungs-Gate** als pure Funktion + Verdrahtung vor Flächenberechnung; Öffnung>Wand-Markierung; **Einheiten-/Rundungskonzept** fixieren | AP-4a grün | nein (reiner Code + Tests) |
| **AP-4c** (nach 4b, eigener Startblock) | Geometrie ans **Objekt/`anforderungsprofile.gebaeude_geometrie`** umziehen (versioniert) + Mehrraum/Geschoss-Konzept; **erst danach** Dach-/PV-Geometrie (Azimut, geneigte Ist-Fläche) bewerten — playground-Utils nur als **Referenz** | AP-4b grün + AP-3-Kern | **wahrscheinlich ja → STOPP + eigener begründeter Posten** |

**Dach-/3D-PV-Geometrie und Modulbelegung liegen bewusst NACH AP-4c** — kein 3D, keine Modulzahl aus Fläche, bevor Validierung + versionierte, objektgebundene, belastbare Flächen stehen.

---

## 15. STOPP

Read-only Validierung abgeschlossen. Nur dieses Dokument erstellt; keine Code-/Schema-/Datenänderung, kein Bau, kein Commit, kein Push, keine Geometrieformel erfunden, `.env`/`CLAUDE.md` unangetastet. **Klare Linie:** Der Rechenkern ist real, aber die Geometrie ist **erst nach Validierung (Topologie-Gate + ticket-Referenztests + versionierte objektgebundene Persistenz + belastbare Dach-/Azimut-Quelle) nutzbar**. Nächster Schritt laut Auftrag: **STOPP** — Yama entscheidet AP-4a bzw. die Reihenfolge.

*Ende AP-4.*
