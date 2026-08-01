<!-- pfade-pruefen: historisch -->
<!-- Bestandsaufnahme vor dem Port; die genannten Pfade beschreiben einen vergangenen Zustand (PB-031, 01.08.2026) -->

# 3D-Planer — Soll-Architektur gegen Bestand geprüft (Gap-Analyse)

**Datum:** 2026-07-16 · **Rolle:** Planner (Prüfung, kein Bau) · Quellen: ticket-Code, playground-Code, deine Soll-Architektur (17 Kapitel).
**Prüfmethode:** jedes Soll-Kapitel gegen belegte Funde gemessen (R1/R5: zwei Repos + Doku, jede Aussage mit Fundort).

---

## A · Die wichtigste Erkenntnis zuerst

**Phase 5 der Soll-Architektur (Dach & PV) ist in playground praktisch fertig gebaut — als einbettbare Insel.** `src/planer/main.tsx` ist ein Standalone-Vite-Entry, der ausdrücklich dafür geschrieben wurde, in eine **Blade-Seite** gemountet zu werden (`#roof-planner`, „ohne AppShell, ohne React-Router"). Dahinter: `DachplanerProPage` (3.786 Zeilen, three.js 0.184), ein Store **exakt nach deinem Grundsatz** („Auswahlfelder UND 3D-Szene lesen/schreiben DENSELBEN Zustand", „einzige Quelle", immutabel + referenz-erhaltend), Geometrie-Utils **mit Tests** (dachVerschneidung, dachAusschnitt, dachUForm, dachOeffnung, dachformVorlagen, dachWerte) und ein **GLB-Katalog** (Dachsteine/Ziegel unter dist/models/tiles). Der erste Schritt ist also kein Neubau, sondern **das Landen dieser Insel in ticket** — genau dein Auftrag „von playground alles rüberholen".

## B · Bestands-Landkarte (belegt)

| Fund | Ort | Bedeutung für die Soll-Architektur |
|---|---|---|
| **3D-Dachplaner-Insel** | playground `src/planer/main.tsx` + `pages/energie/DachplanerProPage.tsx` | Phase 5 fertig; Einbettungs-Muster (Kap. 3) bewiesen |
| **Store nach Soll-Muster** | playground `stores/roofConfigStore.ts` (+ roofTypes, roofVocab) | Kap. 8 („zentraler State, ein Modell") im Dach-Scope erfüllt |
| **Geometrie-Engine + Tests** | playground `utils/dach*.ts` + `services/__tests__/` | Kap. 7 Geometrie-Ordner existiert, getestet |
| **GLB-Assets + Produkt-Service** | playground `dist/models/tiles/*.glb`, `dachproduktService` | Kap. 11 Katalog-Keim |
| **Grundriss-Editor 2D** | ticket `admin/energie/grundriss_editor.blade.php` (1.092 Z., SVG + jQuery) | Phase 2 zur Hälfte — aber als Blade-Monolith (Anti-Pattern Kap. 17) |
| **Raum-Projektionen** | ticket `anforderungsprofile.gebaeude_geometrie` (raeume[]+bauteile[]) + `raum_geometrien` | Kap. 14 Projektions-Idee EXISTIERT — Heizlast liest heute schon strukturierte Räume |
| **Dach/PV in ticket (alt)** | `roof_config/roofs.blade.php`, `solar/configuration/configure.blade.php` (three direkt in Blade), Modelle PVRoof/PVRoofPlan/OfferRoofLayoutConfiguration | dritte 3D-Wahrheit — muss in der Ziel-Architektur AUFGEHEN, nicht parallel weiterleben |
| **Führungs-Ordnung Auslegung** | ticket: Heizlast-Services · `WpAuslegungsketteService` (seit heute verdrahtet) · Anforderungsprofil kanonisch am Objekt | Kap. 14 Rollenteilung ist schon Realität |
| **Technik-Basis** | ticket: Vite ✓ · three 0.163 in package.json ✓ · Sanctum ✓ · **MySQL** · kein Tenant-System · Vue-Plugin (kein React) | Kap. 2 teils da, drei Abweichungen (s. E) |

## C · Kapitel-für-Kapitel: erfüllt / teilweise / fehlt

| Kap. | Soll | Status | Beleg/Lücke |
|---|---|---|---|
| 1 | Eigenständiges Planner-Modul, 2D+3D = EIN Modell | **teilweise** | Insel-Prinzip bewiesen (playground); in ticket heute DREI getrennte Wahrheiten: Grundriss-SVG, roof_config, raum_geometrien |
| 2 | Stack (React/TS/Vite/Zustand/Immer/Konva/three/Zod) | **teilweise** | Vite+three+Sanctum ✓ · React/TS nur playground · Zustand-Äquivalent selbst gebaut (roofConfigStore) · Immer/Konva/Zod fehlen |
| 3 | Blade-Mount `#planner-root` + Vite-Bundle | **erfüllt (Muster)** | playground main.tsx ist exakt dieses Muster, wartet auf die Blade-Seite in ticket |
| 4 | Laravel-Domain `app/Domain/Planner` (Actions/Data/Events/…) | **fehlt** | ticket hat kein Domain-Modul; Namenskonflikt beachten: „Planner" ist in ticket die Plantafel (Einsatzplanung)! → Modul-Name **Hausplaner/ScenePlanner** wählen |
| 5 | DB: documents/snapshots/assets/catalog/locks/projections | **fehlt** | nichts davon existiert; ABER: Anker-Frage anders lösen (→ E: Objekt statt „project", kein tenant_id) |
| 6 | SceneDocument (mm, Wall/Opening/Object/Zone/Route) | **fehlt** | kein gemeinsames Schema; roofTypes = Keim für Dach-Teil; Grundriss-SVG hat eigenes Ad-hoc-Format |
| 7 | Frontend-Struktur (domain/store/commands/geometry/renderers) | **teilweise** | playground hat stores/services/utils/tests im richtigen Geist; commands/ und 2D-3D-Renderer-Split fehlen |
| 8 | Zentraler State, Renderer lesen nur | **erfüllt im Dach-Scope** | roofConfigStore-Kommentar = wörtlich dein Grundsatz; für Haus-Planer zu verallgemeinern |
| 9 | Command-System + Undo/Redo (Immer-Patches) | **fehlt** | nirgends vorhanden |
| 10 | Renderer ohne Geschäftslogik | **teilweise** | RoofEngine liest Store (gut); ticket-Altbestand rechnet in Blade-Skripten |
| 11 | Objektkatalog (Maße, 2D-Symbol, GLB, Wartungsabstände) | **teilweise** | GLB-Dacheindeckungen + dachproduktService ✓; CatalogItem-Schema, Wartungs-/Schallzonen fehlen; **Weiche beachten: ticket-Artikel-DB = EIN Katalog** → planner_catalog referenziert Artikel, dupliziert nie |
| 12 | API v1 + Revisionsprüfung (409) | **fehlt** | Sanctum liegt bereit, Routen/Controller fehlen |
| 13 | Autosave + Konfliktfenster | **fehlt** | — |
| 14 | Führungs-Ordnung: Planer=Geometrie · Heizlast=Rechnung · Orchestrator=Ranking · Profil=Ergebnis | **teilweise — Fundament fertig** | genau diese Ordnung existiert seit heute end-to-end (Kette verdrahtet); fehlt: `PlannerProjectionService` → schreibt in `gebaeude_geometrie`/raum-Projektionen; Hinweis: `anforderungsprofile.auslegung_ergebnis` existiert noch nicht (das ist Stufe 3b der Kette — konsistent) |
| 15 | Rechte: tenant_id + Policies | **abweichend** | ticket ist Single-Tenant (kein tenants-Modell) → tenant_id ENTFÄLLT; statt Policies existiert user_rolls + S-1a-Ownership-Muster (`authorizeMeasurementWrite`) → dieses Muster nutzen |
| 16 | Phasen 1→6 linear | **Reihenfolge drehen** | Phase 5 (Dach+PV) ist fertig gebaut → ZUERST landen; dann Foundation (Phase 1), dann 2D/3D-Haus |
| 17 | Anti-Pattern | **3 aktive Verstöße im Bestand** | Grundriss als 1.092-Zeilen-Blade-Monolith · three direkt in Blade (roof_config, solar) · 2D/3D/Dach getrennt gespeichert (raum_geometrien ∥ PVRoof ∥ roofTypes) |

## D · Die Rüberhol-Liste (playground → ticket, konkret)

1. `src/planer/main.tsx` (Einbettungs-Muster) → ticket `resources/js/hausplaner/`
2. `src/pages/energie/DachplanerProPage.tsx` (3.786 Z. — unverändert lassen, @ts-nocheck bleibt)
3. `src/stores/roofConfigStore.ts` + `roofTypes.ts` + `roofVocab.ts`
4. `src/utils/dach*.ts` **inkl. `services/__tests__/`** (die Tests sind der Schatz — sie sichern den Port)
5. `src/services/dachproduktService.ts` + `roofConfiguratorService.ts` (API-Aufrufe → auf ticket-Routen adaptieren)
6. `dist/models/tiles/*.glb + *.png` → ticket Storage `planner-assets/`
7. Referenz: `backend-laravel/...add_dach_json_to_anlagen_konfigurationen.php` (wie playground Dach-JSON persistiert — in ticket stattdessen am Objekt verankern)

## E · Abweichungs-Entscheide (Soll-Text an Yamas Realität angepasst)

1. **MySQL statt PostgreSQL** — ticket ist MySQL (live, 3000 Kunden). `jsonb` → `json`-Spalten; kein DB-Wechsel für den Planer.
2. **Kein `tenant_id`** — Single-Tenant; Scoping über Objekt/Kunde + Rollen (S-1a-Muster).
3. **Anker = OBJEKT, nicht „Projekt"** — `planner_documents.alternative_id` (LeadAlternativeAdd), exakt wie Anforderungsprofil („Geometrie gehört ans Objekt") und die heutige Gebäudeakte. Damit lesen Heizlast/WP/PV automatisch dieselbe Verankerung.
4. **Modul-Name nicht „Planner"** — der Begriff ist in ticket durch die Plantafel (Einsatzplanung) belegt (Begriffs-Regel!). Vorschlag: **`Hausplaner`** (Domain `App\Domain\Hausplaner`, Tabellen `hausplaner_*`).
5. **React nur als Insel** — Blade bleibt CRM-Oberfläche; das React-Bundle lebt gekapselt wie die Alpine-Zwei-Scope-Regel. **Braucht deine ausdrückliche Scope-Freigabe** (analog Alpine-Entscheid 2026-07-06): „React erlaubt AUSSCHLIESSLICH im Hausplaner-Bundle."
6. **Katalog referenziert die Artikel-DB** — planner_catalog_items verweisen auf ticket-Artikel (EIN Katalog), tragen nur Planer-Zusätze (GLB, 2D-Symbol, Abstände).

## F · Empfohlene Reihenfolge (gedreht, jede Welle einzeln abnehmbar)

- **W1 · Dach-Insel landen** (Rüberholen nach D): Blade-Seite + Vite-Bundle + Route + Navi „Dachplaner" live. Nutzen sofort sichtbar, Null Risiko für Bestand.
- **W2 · Hausplaner-Foundation P0**: Migrationen (documents/snapshots am Objekt, json, Revision), API v1 mit 409-Prüfung, SceneDocument-Schema v1 (mm), Domain-Gerüst, Policies/Ownership. *(= das „P0-Ticket" aus dem Soll-Text, angepasst nach E)*
- **W3 · Dach-Insel andockt**: Dach-Store speichert ins SceneDocument des Objekts (statt lokal), Snapshot/Undo aus der Foundation.
- **W4 · 2D-Grundriss neu** auf SceneDocument (Konva) — der SVG-Editor bleibt parallel, bis die Projektion `gebaeude_geometrie` gleichwertig speist (Heizlast darf nie brechen), dann Ablösung als eigener Posten.
- **W5 · 3D-Haus** (Wände extrudieren, Öffnungen) · **W6 · technische Objekte** (Heizkörper, WP mit Wartungs-/Schallzonen aus Katalog) · **W7 · Ausbau** (Import, Mehrbenutzer).

## F2 · Nachtrag (Yama-Hinweis): Vorarbeiten in ticket — eingearbeitet

Die Prüfung hatte drei ticket-Vorarbeiten zu schwach gewichtet (R7-Korrektur):

1. **`docs/ap4-geometrie-3d-gebaeudemodell-validierung.md` (14.07., read-only):** Die Dach-**Utils** sind belastbare Mathematik (Shoelace, Trigonometrie, Öffnungsabzug — 320 Tests in pg), die DachplanerPro-**UI/Engine ist ein @ts-nocheck-Prototyp**; server-seitig persistiert pg nur eine Bounding-Box-Näherung („kein Aufmaß"). **Vier Pflicht-Gates vor echten Ergebnissen:** (a) Polygon-Topologie-Validierung, (b) Referenztests nach ticket transplantieren, (c) versionierte Geometrie-Persistenz am Objekt, (d) belastbare Dachflächen-/Azimut-Quelle (`RoofAreaEstimator` hat einen Web-Mercator-Flächenfehler). → **Diese vier Gates sind ab jetzt Pflicht-Inhalt der Foundation (W2)**; W1 landet die Insel nur als gekennzeichnetes Prototyp-Werkzeug OHNE Persistenz-Anspruch.
2. **Geometrie-Kern liegt schon in ticket** (`GeometrieAbleitungService`, `RaumHuelleService`, `RoofAreaEstimator`) — aus wberechnung transplantiert, aber **ohne die Referenztests** (Gate b).
3. **`docs/uebernahme/uebernahme-empfehlung.md`** schloss den React-Dachplaner seinerzeit aus („kein Laravel/Blade") — das Standalone-Bundle (17.06., `vite.planer.config.ts` → feste Dateien `planer.js/css`) löst genau diesen Einwand; der Ausschluss ist damit überholt, nicht widerlegt. Zusätzlich: `public/models` in ticket trägt bereits 127 MB GLB (roof_config/solar) — die Insel-Assets bleiben getrennt unter `public/planer/`.

## G · Offene Entscheide an Yama

**(a)** React-Scope-Freigabe fürs Hausplaner-Bundle (E5) — ja/nein?
**(b)** Anker Objekt/Gebäudeakte (E3) — bestätigen?
**(c)** Modul-Name „Hausplaner" (E4) — oder dein Begriff?
**(d)** Startpunkt W1 (Dach-Insel landen) — Freigabe?
