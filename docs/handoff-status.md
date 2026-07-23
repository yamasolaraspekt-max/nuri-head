# Handoff-Status (Ledger) — Hausplaner 3D / UI

> **Zweck:** Eine Wahrheit über **wer am Ball ist**, **wie gearbeitet wird** und **was chronologisch geschah**.
> Jede Rolle liest beim Start ZUERST dieses Ledger (Arbeitsprinzipien + Kette), macht genau ihren Schritt,
> hinterlässt einen Bericht, hängt eine Journal-Zeile an, weckt die nächste Rolle, stoppt.
> **Rollen (getrennte Instanzen):** Planner · Generator · Evaluator · **Stand:** 2026-07-23

---

## 0. Verbindliche Arbeitsprinzipien (vor JEDEM Schritt beachten)

1. **Schritt für Schritt — kein Springen.** Bearbeite ausschließlich die EINE Welle, die laut „Aktuelle Kette" am Ball ist. Keine parallelen Baustellen, kein Vorgreifen auf spätere Wellen, kein Themenwechsel mitten in einer Aufgabe. Erst vollständig fertigstellen **und** Bericht hinterlegen, dann übergeben.
2. **Gründlich, gewissenhaft, mit Perfektion.** Jede Aufgabe vollständig gemäß Auftrag und Abnahmekriterien — keine Teillösung als „fertig" melden. Im Zweifel prüfen statt annehmen.
3. **Bericht-Pflicht — kein Schritt ohne Spur.** Planner → Auftragsdatei + Log; Generator → „umgesetzt" mit Belegen (Branch, Exit-Codes); Evaluator → `docs/abnahme-<welle>.md`. Zusätzlich immer eine Journal-Zeile (§Journal).
4. **Chronologie.** Ins „Journal" unten wird streng chronologisch angehängt (append-only, nie Vergangenes umschreiben): Zeit (per Gerät `date`), Rolle, Welle, Aktion, Ergebnis, nächster Ball.
5. **Effizienz · Plausibilität · Kausalität · Konsistenz.** Jede Aktion folgt kausal aus dem vorigen Schritt (kein Handeln ohne Auslöser im Ledger). **Redundanz vermeiden:** nichts neu bauen oder prüfen, was schon existiert/belegt ist — erst Bestand lesen (`docs/3d/*`, vorhandene Tests/Fixtures). **Konsistenz = eine Wahrheit:** Ledger = Ballbesitz · `hausplanerStore` = Modell · Zod→`scene-document-v2.schema.json` = Typwahrheit.
6. **Sauberes Routing, keine Kollision.** Es handelt IMMER nur die Rolle am Ball. Ball nicht bei dir → nichts tun, **nichts feuern**, stoppen. Genau **ein** Weckruf pro abgeschlossenem Schritt an genau die nächste Rolle — nie zwei Rollen gleichzeitig, nie ein Weckruf „auf Verdacht".
7. **Bei Blocker sauber anhalten statt raten.** Blocker ins Journal + Ledger, Ball bleibt liegen, Yama informieren. Wirklich irreversible Entscheidungen legt der Planner Yama vor.

---

## 1. Aktuelle Kette

| # | Welle | Zustand | Ball bei | Nächster konkreter Schritt |
|--:|---|---|---|---|
| 1 | **UI-2** Tool-Registry / Activation-Engine / UI-State | ✅ **COMMITTET** auf `auto/hausplaner-ui-2` (438eafc Code-Slice, 6bd753f Bundle) · Gate grün: tsc 0 · schema 0 · test 307/307 | erledigt | Kein main-Merge/Push/Deploy (auf Yamas Wort). `szene.ts` bleibt außerhalb UI-2 offen. |
| 2 | **W-1** Dach-Fundament (Werte + Verschneidung) | ✅ **COMMITTET + Evaluator-GRÜN** — `auto/hausplaner-w1` (588283d), Gate tsc 0 · test 338/338, `docs/abnahme-w1.md` | erledigt | Scope planner-geschärft: nur dachWerte/dachVerschneidung/dachUForm (importfrei, schema-neutral). `dachformVorlagen` → spätere Welle (Deps). |
| 3 | W-2 reine Dach-Engine (15 Module: polygonFlaeche/Aufbau/Holz/Öffnung/Gaube/dachformVorlagen/grundriss) | ✅ **COMMITTET + Evaluator-GRÜN** — `auto/hausplaner-w2` (2d7d8b3), byte-identisch, tsc 0 · test 607/607, `docs/abnahme-w2.md` | erledigt | Gesamte reine Engine jetzt im ticket (W-1+W-2). |
| 4 | **W-3 3D-Anschluss + Schema-Konsolidierung** (RoofShape L/T/U persistiert, pruefeRechteckigeKontur öffnen, roofType-Enum) | ⏸ **wartet auf Yama-Review** | Yama/Planner | Berührt Live-Daten/persistierte Szene → bewusst NICHT autonom. Autonomer Loop hier gestoppt. |
| 5 | UI-Strang: Werkzeug-Dashboard (65-Tools-Paket, Auge/Schloss/Fläche, InDesign-Katalog) | 📋 parallel | Planner | `docs/planner/*` (Benchmark, Vollkatalog, Lückenspec) + Yamas 65-Tools-Paket. |

---

## 2. Betriebsmodus — Live über die App (Autopiloten AUS)

**Entscheidung 2026-07-23:** Yama fährt alles über die Claude-Code-App; die Rollen laufen **live in interaktiven Sessions**, nicht als selbstfeuernde Autopiloten.

**Warum Autopiloten aus:** Mehrere gleichzeitig gefeuerte Cloud-Sessions liefen ins **Rennen um `.git/*.lock`** (Redundanz/Kollision). Verschärfend: der gemountete Ordner **verbietet `unlink`**, deshalb kann git seine Lock-Dateien nach jeder Operation nicht selbst aufräumen → jede git-Schreiboperation hinterlässt eine `HEAD.lock`/`index.lock`, die die nächste blockiert. Headless gefeuerte Sessions haben zudem u. U. **keine Geräte-Brücke** (die hängt an Yamas aktiver Desktop-App). Fazit: unbeaufsichtigte Selbstauslösung ist hier unzuverlässig.

| Rolle | Trigger-ID | Status |
|---|---|---|
| Planner | `trig_0179RLPqfLMDtXcQnoWPuqSv` | aktiv (Heartbeat/Auffang) |
| Generator | `trig_01BY6JAqqKXDVskxDwczDnYi` | **DEAKTIVIERT** |
| Evaluator | `trig_01Gb5BHynnNGHHZdq9t1n61x` | **DEAKTIVIERT** |

**Arbeitsweise live:** Toolchain ist bestätigt (node v22/npm/git + `node_modules`; tsc/schema/test grün ohne Netz). git-Schreiben funktioniert, erfordert aber vor jeder Operation ein **Wegräumen stehengebliebener Locks** (`mv .git/*.lock` beiseite; löschen ist gesperrt). Der wirklich saubere git-Weg wäre „Cowork auf dem Computer" (nativer Dateizugriff, `unlink` erlaubt) — nicht VS Code, sondern der On-Computer-Modus der App.

**Guardrails (unverändert):** nur Arbeits-Branch `auto/hausplaner-<welle>`, lokale Commits, **kein main-Merge / kein Push / kein Deploy**. Rollentrennung bleibt: wer generiert, nimmt nicht selbst ab.

---

## 3. Journal (chronologisch, append-only)

| Zeit | Rolle | Welle | Aktion | Ergebnis / nächster Ball |
|---|---|---|---|---|
| 2026-07-23 | Planner | 3D-Bestand | Fünf Bestandsdokumente `docs/3d/*` erstellt (read-only) | Reuse-Plan steht → Grundlage W-1..W-5 |
| 2026-07-23 | Evaluator | UI-2 | Abnahme: tsc 0 · schema 0 · test 306/306 · isoliert 19/19 · Eine-Wahrheit per grep | GRÜN, 3 nicht-blockierende Notizen → Ball Generator (Commit) |
| 2026-07-23 | Planner | UI-2 / W-1 | Commit-Auftrag UI-2 + W-1-Auftrag abgelegt; Autopiloten + Arbeitsprinzipien aufgesetzt | Ball → Generator (UI-2 committen, auf Yamas Wort) |
| 2026-07-23 10:40 | Planner | UI-2 | Evaluator-Bericht UI-2 bewertet: Scope/Eine-Wahrheit/additiv/Self-run-Belege geprüft → GRÜN bestätigt. Yama-GO für lokalen Branch-Commit vermerkt. W-1 bleibt gesperrt bis UI-2-Commit steht (Schritt-für-Schritt). | Ball → Generator (ausführen); Weckruf ausgelöst |
| 2026-07-23 11:50 | Planner/Ops | — | Autopilot-Kollision erkannt (mehrere gefeuerte Sessions, stale `.git/*.lock`, `unlink` gesperrt). Generator+Evaluator-Autopiloten DEAKTIVIERT. Betriebsmodus → live über die App. | Rennen gestoppt |
| 2026-07-23 11:52 | Generator (live) | UI-2 | Branch `auto/hausplaner-ui-2`: Code-Slice `438eafc` + Bundle `6bd753f` committet (Locks vor jeder Op weggeräumt). Gate selbst gemessen: tsc 0 · schema 0 · test 307/307. `szene.ts` bewusst ausgelassen (Scope). | UI-2 committet & grün → nächster: W-1 |
| 2026-07-23 12:00 | Planner | W-1 | Abhängigkeiten gelesen: dachWerte/dachVerschneidung/dachUForm importfrei → W-1; dachformVorlagen zieht spätere-Wellen-Deps → verschoben. Scope geschärft. | Generator-Schritt freigegeben |
| 2026-07-23 12:02 | Generator (live) | W-1 | 3 Module verbatim portiert + 3 Tests (Importpfade `../geometry/`). Branch `auto/hausplaner-w1`, Commit `588283d`. Gate: tsc 0 · test 338/338. | Ball → Evaluator |
| 2026-07-23 12:04 | Evaluator (live) | W-1 | Gegen-Beweis: Module byte-identisch (reine Reuse), importfrei, Roof-Schema unverändert, Scope = 6 Dateien ohne Beifang; unabhängig 338/338. `docs/abnahme-w1.md`. | **GRÜN** → nächster: W-2 |
| 2026-07-23 15:40 | Generator (Mitwecker) | W-2 | 15 Module topologisch portiert (verbatim) + 10 Tests. Zwischenfehler: Test-Helfer `grundriss` fehlte (Test-Import übersehen) → nachgeportet. Branch `auto/hausplaner-w2`, Commit `2d7d8b3`. Gate tsc 0 / test 607. | Ball → Evaluator |
| 2026-07-23 15:42 | Evaluator (Mitwecker) | W-2 | 15/15 byte-identisch, roofType-Enum unverändert, Scope 25 Dateien ohne Beifang, unabhängig 607/607. `docs/abnahme-w2.md`. | **GRÜN**. Reine Engine komplett → W-3 braucht Yama-Review → autonomer Loop gestoppt (kein Mitwecker). |

## Journal — 2026-07-23 (Planner, dach-ui gebaut + AP1 committet; Reihenfolge festgelegt)
- **Batch 0 `4198561`: FREIGABE vollständig** (Evaluator: 639 selbst, Optik+4 Fachagenten im Browser grün).
- **dach-ui `1d8c735` gebaut + serviert** (Generator, ticket.test 200) — U jetzt UI-erreichbar.
- **AP1 (BivalenzService→wp-auslegung) committet** auf `auto/energie-verdrahtung @ 7f844e8` (geht nicht verloren).
- **Reihenfolge (Planner, Kollisions-sicher am geteilten Baum):**
  1. JETZT: Evaluator fährt Bahn 3 auf `dach-ui @ 1d8c735` (U-Optik + Dach-UI-Votum). **Generator hält den
     Baum dort** — NICHT umstellen, sonst bricht die Browser-Runde.
  2. NACH dem Dach-UI-Votum: Baum auf `auto/energie-verdrahtung @ 7f844e8`, Generator baut/serviert →
     Evaluator nimmt AP1 ab (BivalenzService sichtbar unter `/admin/energie/wp-auslegung`).
  3. Dann nächste Code-Scheibe (Decke / L/T-Faces Teil 3 / AP2).
- **Push:** Yamas Hand (Ziel `cff1fe5`), parallel.
- **Planner parallel:** Decken-Slice in x64-Cloud (kollidiert nicht mit dem Geräte-Baum).

## ⇒ GENERATOR — OFFENE AUFTRÄGE (2026-07-23, Planner)
Zwei kleine Frontend-Fixes, zusammen machbar (beide `HausplanerApp`/`szene.ts`), dann zurück an den Evaluator:
1. **`docs/auftraege/generator-auftrag-fix-ushape-anbau.md`** — u-shape: ALLE vier Anbau-Felder einblenden
   (length/width = Außenmaß, lengthB/widthB = Innenhof/Kerbe), an `anbau` verdrahten wie l/t. Rein UI;
   `anbauZuEingabe`/`dachMesh`/`dachUForm` UNVERÄNDERT. → macht das U-Dach erreichbar.
2. **`docs/auftraege/generator-auftrag-3d-snapshot-capture.md`** — 3D-Snapshot hinter Capture-Flag
   (`szene.ts`-Renderer ohne `preserveDrawingBuffer` → WebGL nicht capturebar). → macht die 3D-Optik prüfbar.
Danach Meldung „umgesetzt" (Gates) → Evaluator fährt die U-Optik-Abnahme (statisch + Browser, 3 Viewports).
Basis: Dach-UI-Tip. Nur `auto/`-Branch, kein Push.

## ⇒ EVALUATOR — DECKE zur Abnahme freigegeben (Fach-Freigabe Yama „ja passt", 2026-07-23)
**Stand:** `auto/hausplaner-decke` @ `9cbc202` — CeilingNode additiv (`ceilings?`, Muster roofs), Zod+Schema
regeneriert, Commands, `deckenMesh.ts` (3D-Slab), Werkzeug, `decke.test.ts`. Tor 1 (Fach) erteilt.
**Deine Abnahme (Qualitätsprüfer):** Gates read-only (tsc/schema/test); Additiv/kein 422 (Bestand ohne
`ceilings` lädt); Slab sitzt auf Wand-Oberkante (`level.elevation+defaultWallHeight`), Dicke=`floorThickness`;
Etagen-Stapel (nächste Etage = elevation+Wandhöhe+Deckendicke, eine Ableitung); Treppendurchbruch nutzt das
`dachAusschnitt`-Loch-Muster; Node-Union unberührt. 3D-Optik (Slab sichtbar, Loch) braucht den 3D-Snapshot
(#61). Statik-Hinweis: Decke ist GEOMETRISCH, nicht statisch dimensioniert (bewusst später) — kein Verstoß.
Rot → Generator; grün → Planner.


## ⇒ EVALUATOR — WebGL-Sicht ist bereits gelöst (Capture-Fix gebaut, 2026-07-23 Planner)
Deine zwei Vorschläge (Generator-Screenshot / Split-View) sind **nicht nötig** — und ein vom Generator
beigelegter Screenshot wäre „Bericht statt Beweis". Der 3D-Snapshot-Enabler ist **gebaut**: `renderers/three-d/
capture.ts` + `szene.ts` (auf dem U-Fix-Stand `8dfa02b`). So capturest du deinen **eigenen** Frame:
1. Studio mit **`?capture=1`** öffnen (Flag). Dann ist `preserveDrawingBuffer` aktiv (nur dann — keine
   Perf-Regression) und `window[SNAPSHOT_GLOBAL]()` freigeschaltet.
2. `window[SNAPSHOT_GLOBAL]()` (bzw. die `snapshot()`-Methode der Szene) gibt den WebGL-Frame als
   PNG-DataURL zurück — nicht-leer, weil der Puffer erhalten bleibt. Das ist dein Sicht-Beleg.
- **U-Optik:** auf `8dfa02b` (trägt U-Fix **und** Capture) direkt prüfbar — u-shape + 4 Maße → `?capture=1`
  → Snapshot → Lage/Orientierung.
- **Decke-3D-Sicht:** braucht die Capture auf dem Decke-Tree; bis Decke+Capture auf einem Stand liegen, ist
  die Slab-Geometrie test-belegt (Fläche>0, Loch verkleinert Fläche), die reine Sicht folgt als Nachtrag.

## Journal — 2026-07-23 (Planner, Evaluator-Bericht gelesen + Linsen angewandt)
- **Decke `9cbc202`: FREIGABE (Code/Geo, 646 selbst).** „Eine Wahrheit" bestätigt (dekorative Raum-Decke
  ersetzt via `hatModellDecke ? [] : raeume`, nicht dupliziert). Offen nur: 3D-Sicht (jetzt über Capture lösbar).
- **U-Fix `8dfa02b`:** Planner-Lens-Read (frontend+dachdecker) = code-seitig korrekt (u-shape 4 Felder,
  Innenhof/Kerbe-Labels, an anbau verdrahtet, Render unberührt; Capture hinter Flag). Re-Abnahme durch
  Evaluator ist der nächste Schritt (Statik + `?capture=1`-Sicht).
- **Nächste Bau-Entscheidung (Planner, Regel 4 → Yama-Fach-Freigabe):** nach Decke folgen laut Etagenbau-Kette
  Fußbodenaufbau + Auto-Giebel; die neuen Tragteile (Stütze/Unterzug/Träger/Fundament) + Geschoss-Bedienung
  brauchen je ein Konzept zur Fach-Freigabe, bevor Auftrag.

## ⇒ EVALUATOR — KORREKTUR Capture-Stand (Planner, gemessen)
**Richtigstellung:** Die Capture (`capture.ts`) liegt auf **`40a9ede`** (HEAD von `auto/hausplaner-dach-ui`),
NICHT auf `8dfa02b`. Historie: `1d8c735` (8 Formen+Felder) → `6b87c42` (falscher Zwischen-Fix, überholt) →
`8dfa02b` (u-Fix: 4 UI-Felder, Render unangetastet) → **`40a9ede`** (3D-Snapshot-Capture).
**Also: U-Re-Abnahme auf `40a9ede`** — dort ist u-Fix UND Capture; `?capture=1` → `window[SNAPSHOT_GLOBAL]()`.
**Kette geprüft (Planner, Regel 1):** `anbauZuEingabe` (dachMesh.ts Z.72) verlangt ALLE VIER Maße
(length/width/lengthB/widthB > 0 → sonst null), `UFormEingabe`/`uFormFlaechen` nutzen alle vier
(Innenhof/Kerbe aus lengthB/widthB). UI zeigt vier Felder. **Chain durchgängig korrekt — der falsche
6b87c42-Zwischenstand ist überholt.** Also code-seitig grün; deine Live-Sicht (`?capture=1`) schließt es.

## ⇒ EVALUATOR — OFFENE AUFGABEN (priorisiert, 2026-07-23 Planner)
**1. JETZT — U-Optik + u-Fix Re-Abnahme auf `40a9ede`** (Branch `auto/hausplaner-dach-ui`, Baum steht dort):
   Dieser Tip trägt U-Render + u-Fix (`8dfa02b`) + 3D-Snapshot (`40a9ede`) zusammen — **nicht `8dfa02b`**.
   - **Statik (read-only):** u-shape zeigt 4 Felder (Außenmaß + Innenhof/Kerbe); `anbauZuEingabe` verlangt alle
     vier Maße (dachMesh.ts Z.72); Render/`dachUForm` unberührt; Gates selbst (erwartet tsc 0 / schema 0 / 641).
   - **Sicht (Browser):** Studio mit **`?capture=1`** → `window.__hausplanerSnapshot3d()` → **nicht-leere**
     PNG-DataURL des 3D-Frames; u-shape + 4 Maße setzen → U-Dach-Lage/Orientierung prüfen. Ohne Flag:
     Normalbetrieb, `preserveDrawingBuffer` genau 1× flag-gated (keine Perf-Regression). → Votum U-Fix.
**2. Navi/Topbar-Optik `9d79109`** — Votum (Werkzeuge als Icons in Topbar, Token-Disziplin, 3 Viewports).
**3. Decke `9cbc202` — 3D-Sicht-Nachtrag:** Code/Geo bereits FREIGABE; die reine Slab+Treppenauge-Sicht
   erst prüfbar, wenn die Capture auf dem Decke-Stand liegt (Planner richtet das ein).
**4. Energie AP1 `7f844e8`** (BivalenzService in wp-auslegung) — Baum dorthin, bauen/servieren, `/admin/energie/
   wp-auslegung` zeigt Bivalenzpunkt/E-Stab/Laufstunden/Strom.
**Reihenfolge-Regel:** Baum bleibt auf `40a9ede`, bis #1 durch ist (sonst bricht die Browser-Runde). Danach
umstellen für #2/#3/#4. Rot → Generator; grün → Planner.
