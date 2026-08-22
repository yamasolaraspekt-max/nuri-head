# KONZEPT — Etagenweiser Aufbau (Meilenstein „Etagen", Dirigent in Yamas Namen, 22.08.2026 19:1x)

```yaml
zustand: "KONZEPT mit Auftragszuschnitt — Scheiben E0..E6 sind Auftraege mit Ziel, Aufgaben und Kriterien (Planungsmodell V3); Planner schneidet die Blaetter, Plan-Pruefer erteilt DoR, Generator baut, Evaluator nimmt im Browser ab"
grundlage: "Yama 22.08. 18:4x: 'Objekt etagenweise aufbauen — Bodenplatte, Zwischendecke und Abschlussdecke sind fuer die Etage wichtig, alle anderen Werkzeuge passieren in den Etagen; sehr intelligent, smart, benutzerfreundlich, maximale Flexibilitaet' · 18:3x: 'Bodenplatte/Zwischendecke als Werkzeug fehlt vollstaendig — schnellstmoeglich, fundiert, jetzt auch bauen' · Yamas Golden Path 21.08. (docs/konzept/golden-path-bauwerksprozess.md §Modellentscheidung, §Vorwaerts/Rueckwaerts, §Effizienz) · GP-0 Modellplan (docs/konzept/golden-path-gp0-modellplan-bodenplatte.md) · Vorlage Lesesitzung 18:52 (Maurer/Statiker/Architekt) · Architektur-Linse + UX-Linse 19:0x (read-only am Stand 06956916)"
entscheidungen: "docs/auftraege/YAMA-ENTSCHEIDUNGEN-2026-08-22.md Posten 24 (Leistenreihenfolge) und 25 (sechs Operanden, GP-0-Fragen, E0 zuerst, W-24 offen)"
heimat_app: ticket
heimat_code: resources/planner/hausplaner (Insel) · PHP nur Validator/Request/Action
spur: "E0/E2/E3: Spur A (Commands/Geometrie, keine Modellaenderung) · E1/E6: Spur W (Bedienflaeche) · E4: Spur A mit Modellaenderung (additiv) + Fach-Linse Maurer/Statiker Pflicht · E5: Spur A"
nicht_ziele: "kein Vertikalschnitt-Renderer in dieser Welle · keine Normwerte im Werkzeug (Plattendicke/Bewehrung/Wasserbeanspruchung nie 'geprueft') · kein Phasenzwang (Fuehrung ja, Sperre nein) · keine zweite Geometrie-Wahrheit · kein eigener Pfad an applyCommand vorbei"
```

## Ziel (ein Satz) und Erreicht-Bedingung
**Ziel:** Ein Benutzer baut ein Gebäude Etage für Etage — sieht jederzeit, in welcher Etage er arbeitet, setzt Bodenplatte, Wände, Öffnungen, Treppe, Zwischendecke/Abschlussdecke und Dach in dieser Reihenfolge (Führung, kein Zwang), und jede Etagen-Operation (anlegen, einfügen, duplizieren, löschen, Höhe ändern) ist vollständig, sichtbar in ihren Folgen und als ein Schritt rückgängig.
**Erreicht**, wenn alle Scheiben E0–E6 `ABGENOMMEN (BROWSER)` sind und das Referenzhaus-Fixture (GP-0 §7) EG + OG + Dach mit Bodenplatte, Zwischendecke und Abschlussdecke/Dach in einer Sitzung bedienbar entsteht — ohne eine Zahl von Hand nachzurechnen.

## Was der Bestand hat (gemessen, Stand 06956916)
| Baustein | Beleg | Stand |
|---|---|---|
| Etagenmodell `levels: Level[]` (elevation, floorThickness, defaultWallHeight, sortOrder), Knoten mit `levelId` | `domain/scene.types.ts:37,62-68,85` | vorhanden |
| Ein Zustand `activeLevelId` + `setActiveLevel` (leert Auswahl) | `store/hausplanerStore.ts:29,116,131` | vorhanden, sauber |
| 2D/3D zeigen nur die aktive Etage | `app/ableitungen.ts:52`, `renderers/three-d/szene.ts:351,459,479,513` | vorhanden; kein Geist der Nachbaretage |
| Geschoss anlegen (nur oben drauf), duplizieren (ohne Decke), Etagen-Wähler im Kopfrahmen | `app/dashboard/Kopfrahmen.tsx:159-188`, `GeschossFlaeche.tsx:108-172`, `geometry/geschossVorlage.ts:43-78` | teilweise |
| Decke je Level (max. 1), Dach je Level (max. 1), Undo = ein Immer-Draft je Command-Bündel | `commands/applyCommand.ts:66-73,111-116`, `store/hausplanerStore.ts:147-178` | vorhanden |
| Höhenkette-Funktion B `naechsteEtageElevationMm` | `renderers/three-d/deckenMesh.ts:32-38` | **tot** (0 Produktivaufrufer); Kopfrahmen:172/geschossVorlage:54 rechnen mit floorThickness statt Deckendicke → **S2: 2700 statt 2740** |
| Sperrgrund „Kein aktives Geschoss" ehrlich benannt | `app/tools/vorbedingungen.ts:137` | vorhanden |
| Badge-Muster für Canvas-Chip, `nachbar()` für Etagenwechsel | `app/GuidedView.tsx:99`, `dashboard/geschossStapel.ts:100` | wiederverwendbar |

## Lücken (gemessen) → Scheibe
| # | Lücke | Beleg | Scheibe |
|---|---|---|---|
| L1 | Höhenkette = drei Rechnungen, eine tot, eine mit falscher Quelle | deckenMesh.ts:10-12/32-38, Kopfrahmen.tsx:172, HausplanerApp.tsx:1008 | **E0** |
| L2 | Etagen-Kontext fünfmal abgeschrieben + stiller Rückfall auf `levels[0]`; applyCommand prüft nur Existenz der levelId | HausplanerApp.tsx:368,937,980,1004,1065,1095; ConfigWizard.tsx:228,244; applyCommand.ts:145 | **E1** |
| L3 | Kein „Wo bin ich" im Canvas, kein Kürzel, kein Geist der Nachbaretage | Buehne.tsx, DreiDBereich.tsx, HausplanerApp.tsx:1233 | **E1**, Geist in **E5** |
| L4 | Etage löschen ignoriert Decke (verwaiste CeilingNode); Duplizieren vergisst Decke und erzeugt doppelte sortOrder/elevation | applyCommand.ts:400-414,370-377; sammelBefehle.ts:122-134; geschossVorlage.ts:57 | **E2** |
| L5 | Decke weder editierbar noch löschbar: UPDATE/REMOVE_CEILING ohne Aufrufer, kein Panel | commands.types.ts:30-31, applyCommand.ts:305-330, EigenschaftenPanel.tsx (0× ceiling) | **E3** |
| L6 | Bodenplatte ≠ Zwischendecke ≠ Abschlussdecke — ein Typ, keine Rolle; `boden` ohne Handler auf ADD_CEILING | scene.types.ts:348-358, werkzeugLandkarte.ts:177/180, toolRegistry.ts:147 | **E4** (Bodenplatte) + **E3** (Deckenrolle abgeleitet) |
| L7 | Etage einfügen unten/dazwischen fehlt; Höhe ändern zieht nichts nach (Folgeetagen, Traufhöhe, Wandhöhe) | Kopfrahmen.tsx:167-180, applyCommand.ts:380-394 | **E5** |
| L8 | Oberster Abschluss (Abschlussdecke ODER Dach) nicht modelliert; Dach auf jeder Etage möglich | scene.types.ts:280 (Kommentar), applyCommand.ts:66-73 | **E3/E6** |
| L9 | Leiste: Dach vor Decke/Treppe, keine Führung zur Baureihenfolge | toolPresentation.ts:72-81 | **Z1-W2-8** (läuft) + **E6** |
| L10 | Heizlast nutzt `Level.sortOrder` als Geschossnummer — Einfügen verschiebt Nummern | projection/raumProjektion.ts:27,34,63 | Kante in **E5** (Kriterium) |

## Die Scheiben — je ein Auftrag mit Ziel, Aufgaben, Kriterien
**E0 · Höhenkette = eine Wahrheit** (Spur A, kein Modell) — *Ziel:* genau eine Funktion rechnet Etagen-Elevation/Deckenoberkante/Traufhöhe; Kopfrahmen „Geschoss anlegen" und Dach-Traufhöhe lesen daraus.
Aufgaben: `geometry/hoehenkette.ts` nach GP-0 §3 (Signatur :118-127), ruft deckenOberkanteMm/naechsteEtageElevationMm; Kopfrahmen.tsx:172 + HausplanerApp.tsx:1008 + geschossVorlage.ts:54 umhängen. Kriterien: (1) `grep` findet genau eine Erzeugerfunktion der nächsten Elevation und drei Aufrufer; (2) Rot-Probe EG floorThickness 200, Decke 240, „Geschoss anlegen" → 2740 (vorher 2700) — Browser + Bildbeleg; (3) Referenzhaus-Fixture: alle drei alten Werte bitgleich, wo keine Decke modelliert ist; (4) tsc 0, Suite grün, Bündel in der Lieferung; (5) kein Modell-/Schema-Diff.
**E1 · Wo bin ich — Etagen-Kontext als Vertrag** (Spur W) — *Ziel:* der Benutzer sieht jederzeit die aktive Etage und kann sie per Kürzel wechseln; jedes Werkzeug bezieht die Etage aus genau einer Funktion, ohne stillen Rückfall.
Aufgaben: `aktiveEtage(scene, activeLevelId)` in ableitungen.ts; Rückfall `levels[0]` entfernen (Ablehnung mit Grund „Kein aktives Geschoss", vorbedingungen.ts:137); fünf Klickstellen + ConfigWizard umhängen; Canvas-Chip „Erdgeschoss · ±0 mm" (Muster GuidedView.tsx:99) in 2D und 3D; Bild↑/Bild↓ über `nachbar()`. Kriterien: (1) Chip sichtbar bei jedem Zoom, in 2D und 3D, Bildbeleg; (2) Bild↑/↓ wechselt, Fokus bleibt, Chip aktualisiert; (3) ohne aktive Etage legt kein Werkzeug etwas an, Grund sichtbar (Rot-Probe); (4) `grep "levelId: level.id"` → 0 Handschreibstellen, eine Funktion; (5) tsc 0, Suite, Bündel; (6) kein Modell-Diff.
**E2 · Etagen-Integrität** (Spur A, kein Modell; **schnellster Nutzen**) — *Ziel:* keine Etagen-Operation verliert oder verdoppelt Bauteile.
Aufgaben: REMOVE_LEVEL prüft `ceilings` (Muster hatDach :406); Duplizieren nimmt die Decke mit (sammelBefehle.ts:122-134, geschossVorlage.ts:75); ADD/UPDATE_LEVEL lehnen doppelte sortOrder ab; Validation prüft Fremdschlüssel auch für ceilings/roofs (validation.ts:368-373). Kriterien: (1) Etage mit Decke löschen → Ablehnung `level_nicht_leer`, sichtbar; (2) EG duplizieren → OG hat die Decke, eigene sortOrder/elevation (Browser, Bildbeleg); (3) Dokument mit verwaister Decke wird beim Laden benannt, nicht still akzeptiert; (4) Bestandsdokumente laden unverändert; (5) tsc/Suite/Bündel; (6) kein Modell-Diff.
**E3 · Decke bedienbar + Rolle abgeleitet** (Spur W/A) — *Ziel:* eine Decke ist auswählbar, in Dicke/Schichten änderbar, löschbar, und zeigt ihre Rolle (Zwischendecke / Abschlussdecke) und Heizlast-Grenzfläche — abgeleitet aus der Lage, übersteuerbar, nie als starres Feld gespeichert.
Aufgaben: `deckenLage(level, levels)` neben deckenOberkanteMm; UPDATE/REMOVE_CEILING verdrahten; Decke im EigenschaftenPanel (hp-ep-Klassen, AUF-38-P1); Grenzfläche beheizt/unbeheizt/außen nach Posten 25 (2) mit `MitHerkunft`; Hinweis „Dach/Abschlussdecke nur auf oberster Etage sinnvoll" (Warnung, kein Zwang). Kriterien: (1) Decke anklicken → Panel, Dicke ändern → 3D folgt; (2) löschen → weg, Undo → zurück; (3) Geschoss oben aufsetzen → bisher oberste Decke wird „Zwischendecke/beheizt" ohne Benutzeraktion (Rot-Probe: vorher „Abschlussdecke"); (4) manuelle Übersteuerung bleibt beim Nachberechnen stehen und trägt Herkunft; (5) tsc/Suite/Bündel; (6) Schema nur, wenn ein optionales Übersteuerungsfeld nötig ist (dann additiv, Schema + Zod + PHP nachgezogen).
**E4 · Bodenplatte als eigenes Bauteil** (Spur A mit additivem Modell; Fach-Linse Maurer + Statiker PFLICHT; Operanden Posten 25) — *Ziel:* Werkzeug „Bodenplatte" an erster Stelle der Leiste erzeugt einen `slab` auf der untersten Etage mit Dicke, Höhenlage (OK = ±0,00 − Fußbodenaufbau), Schichten (Index 0 = Oberseite), `erdberuehrt`; sichtbar in 3D und minimal in 2D; Heizlast-Grenzfläche erdreich.
Aufgaben: `slabs?: SlabNode[]` additiv (Muster ceilings scene.types.ts:44-54), Zod (validation.ts) + `scene-document-v2.schema.json` (additionalProperties:false!) + PHP-Validator (SceneDocumentValidator.php:12, SpeichereHausplanerDokumentRequest.php:66) + `migriereSzene` eine Zeile, SCHEMA_VERSION bleibt; Commands ADD/UPDATE/REMOVE_SLAB; max. 1 je Gebäude (unterstes Level); Werkzeug `bodenplatte` (Registry, Handler, Leiste Platz 1, `boden`-Doppelabbildung auflösen); 3D-Mesh (gemeinsame Geometrie-/Schichtfunktionen mit Decke, fachlich getrennt); 2D Umriss + Schraffur in der untersten Etage; Panel (Dicke, Höhenlage, erdberührt, Schichten) mit Herkunft; `hoehenkette.ts` (E0) kennt die Platte als unteres Ende; Durchbrüche nur ausdrücklich; W-24 im Register OFFEN → Blatt. Kriterien: (1) Leiste beginnt mit „Bodenplatte"; ein Klick aus der Grundfläche erzeugt die Platte (Browser, Bildbeleg 2D+3D); (2) zweite Platte im Gebäude → Ablehnung mit Grund; (3) Platte auf einer nicht-untersten Etage → Ablehnung/Hinweis; (4) Speichern/Neu laden: Dokument mit Platte gültig (PHP 200, nicht 422), Bestandsdokument ohne Platte unverändert; (5) Höhenkette: OK Platte = ±0,00 − Aufbau, Vermerk „Aufbau nicht erfasst" sichtbar; (6) Heizlast-Projektion liefert Grenzfläche erdreich (Test); (7) Panel zeigt nie „geprüft" für Dicke/Bewehrung (Wortprobe); (8) Fach-Linsen-Votum Maurer/Statiker vor DoR; (9) tsc/Suite/PHP-Tests/Bündel; Revert = ein Commit + Schema-Rückbau.
**E5 · Flexibilität** (Spur A) — *Ziel:* Etage einfügen (darunter/dazwischen), duplizieren mit Auswahl (Wände/Öffnungen/Räume/Decke), Höhe ändern mit Folgen-Vorschau und Bestätigung — alles als ein Undo-Schritt; Geist der Nachbaretage in 2D/3D.
Kriterien: (1) „+ darunter/dazwischen" erzeugt korrekte sortOrder/elevation, Heizlast-Geschossnummern bleiben konsistent (raumProjektion.ts — Test); (2) Höhe ändern zeigt Vorschauliste (betroffene Etagen, Traufhöhe, Wandhöhen) → Bestätigen → ein Undo; (3) Geist erkennbar, nicht anklickbar, eigener Token (nicht GESPERRT_DECKKRAFT); (4) Escape-Stapel gemeinsam (GeschossFlaeche Muster); (5) tsc/Suite/Bündel; (6) kein Modell-Diff.
**E6 · Führung** (Spur W) — *Ziel:* Baureihenfolge (Posten 24) als „nächster sinnvoller Schritt" (wegweiserSatz) je Etage; Phasenstatus je Etage (nicht begonnen · Entwurf · gültig · Prüfung erforderlich) nach Golden Path §Effizienz; keine Sperre.
Kriterien: (1) Hinweis wechselt mit dem Bauzustand (leer → „Wände", Wände → „Öffnungen/Treppe", Treppe → „Zwischendecke", oberste Etage → „Abschlussdecke oder Dach"); (2) jedes Werkzeug bleibt jederzeit klickbar; (3) Bildbeleg; (4) tsc/Suite/Bündel.

## Reihenfolge und Termine (Ziele mit Messbedingung, nicht Versprechen)
| Wann | Was | Erreicht-Bedingung |
|---|---|---|
| **heute 22.08. abends** | Z1-W2-8 Leiste (ohne Bodenplatte) gebaut; Planner schneidet E0 + E2 | DoR erteilt, Generator gestartet |
| **23.08.** | E0, E2 ABGENOMMEN (BROWSER); E1 geschnitten/gebaut; E4-Blatt mit Fach-Linsen DoR-reif | Votum-SHAs, Bildbelege |
| **24.08. (M1)** | E1 + E3 ABGENOMMEN; E4 im Bau | — |
| **26.08.** | **E4 Bodenplatte ABGENOMMEN (BROWSER)** — Leiste beginnt mit Bodenplatte | Kriterien 1–9 |
| **29.08. (M2)** | E5 ABGENOMMEN | — |
| **05.09. (M3)** | E6 ABGENOMMEN; Referenzhaus in einer Sitzung | Erreicht-Bedingung oben |

## Leitplanken (Architektur-Linse) und Fallen (UX-Linse)
Eine Wahrheit SceneDocument in ganzen mm (pruefeGanzzahlig) · additive Sammlungen neben nodes/roofs/ceilings, Konsumenten mit `?? []` · Änderungen nur über applyCommand (Undo, Ablehnung) · **Zod UND JSON-Schema UND PHP nachziehen** (additionalProperties:false → sonst 422 beim Speichern; kein Generierungsscript gefunden) · Insel-Grenze · Belegkette unberührt. Fallen: Dichte im GeschossFlaeche-Menü (K-08), Mindestbreite 1024 px, ein Escape-Stapel, keine UI-Behauptung „drei getrennte Bauteile", solange das Modell sie nicht trägt (erst nach E3/E4), Heizlast-sortOrder.

## Rückweg je Scheibe
E0/E1/E2/E3/E5/E6: Revert eines Commits, Bestandsdokumente unberührt. E4: Revert + Schema-Rückbau; Dokumente ohne `slabs` bleiben in jedem Fall gültig (optional). Entdeckung: Kriterien-Tests + Referenzhaus-Fixture + Browserabnahme.
