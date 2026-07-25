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

## ⇒ EVALUATOR — U-PLATZIERUNG SICHT-ABNAHME (turnkey, 2026-07-24 Planner)
**Logik-Teil bereits bewiesen** (Planner selbst gefahren auf 66ad448): tsc 0 · schema 0 · test 649/649,
inkl. `dachUFormPlatzierung.test.ts` (Zentrum==BBox-Mitte + Gegenprobe + Dach⊇Wand + Innenhof frei).
**Es fehlt NUR die visuelle Bestätigung** — die braucht nativen Build+Serve (Cowork/aarch64 kann Vite/Rollup
nicht bauen, kein Dauer-Server). Ein-Schritt für die native Seite:
1. `git checkout auto/hausplaner-dach-ui` (Spitze `66ad448`; Baum steht aktuell auf `w3b-2 @ f0d02f4`).
2. `npm run build:hausplaner` (läuft nativ auf dem Mac — Rollup vorhanden).
3. ticket-App servieren → `…/hausplaner?fixture=u-dach&capture=1` öffnen.
4. Konsole: `window.__hausplanerSnapshot3d()` → **nicht-leere** PNG; visuell prüfen: U sitzt **bündig** auf
   dem Umriss (kein Übersteh links jenseits `ueberstandMm`), **Innenhof frei**, keine Fläche kippt in die Kerbe.
5. Votum → Ledger. Danach frei: L/T (`w3b-2 @ f0d02f4`) und Decke-Slab (`auto/hausplaner-decke @ d59e26c`).

**Überwacher-Notiz:** Baum wurde von `66ad448` (U, ungesehen) auf `f0d02f4` (L/T) umgestellt, bevor die
U-Sicht lief → für die Sicht einmal zurück-checkouten (Schritt 1). Commits sind sicher auf ihren Branches.

## Journal — 2026-07-24 (L/T erbt U-Fehlplatzierung — Linsen angewandt, Auftrag)
- **Evaluator-Vorab-Befund 🔴 (statisch, w3b-2 @ f0d02f4):** verschneidungsFlaechen platziert u+l/t über
  `polygonSchwerpunkt` → L/T versetzt. Am Code belegt (dachMesh.ts Z.151).
- **Planner MIT Skills geprüft (Fachprüfer-Panel):**
  - *dachdeckermeister:* Traufe/Kehle sitzen nicht auf der L/T-Kontur → rot bestätigt.
  - *software-architekt:* `66ad448` (U-Fix) NICHT in w3b-2 (Basis 4b8eb04); beide Branches ändern dieselbe
    Funktion → Branch-Divergenz + zwei Anker-Wahrheiten drohen. w3b-2 hat die bessere Struktur (u+l/t in
    EINER `quelle`-Schleife); der 66ad448-Anker ist quellen-agnostisch ⇒ EINE gemeinsame Footprint-Zentrierung
    platziert u UND l/t korrekt.
  - *bauplaner-3d Regel 4:* KEINE neue Fachentscheidung (gleicher, von Yama freigegebener Anker) → kein neues Tor 1.
- **Auftrag:** `docs/auftraege/generator-auftrag-lt-platzierung-und-merge.md` — Anker aus 66ad448 in die
  w3b-2-`quelle`-Schleife ziehen (`polygonBbox`-Ziel, engine-agnostisch), + l/t-Fixtures & Platzierungs-Test,
  + Merge-Hinweis (w3b-2-Fassung von verschneidungsFlaechen wird kanonisch, subsumiert den U-Fix — Yama/Tor 2).
- **Ball:** Generator (nativ) baut auf w3b-2; danach Evaluator Sicht. Push/Merge = Yama.

## Journal — 2026-07-24 (3D-Wandecken Gehrung: Konzept + Fach-Freigabe A + Auftrag)
- **Befund (gemessen):** 3D baut Waende als Roh-Boxen (`platziereWandQuader`), nutzt `wandBaender` NICHT →
  Ecken ueberlappen/klaffen. 2D-Gehrung `wandBaender` = vorhandene EINE Wahrheit (gemeinsamer Gehrungspunkt).
- **Linsen (Fachpruefer-Panel angewandt):** software-architekt (Reuse `wandBaender`, keine zweite Wahrheit),
  maurer (kein Tragend-Flag → reine Geometrie, keine Statik-Aussage), technischer-zeichner (DIN 1356-1,
  Grundriss↔3D konsistent), bauplaner-3d (additiv, kein Schema-Regen).
- **Konzept:** `docs/konzept/3d-wandecken-gehrung.md`. **Yama Fach-Freigabe (Tor 1): Ansatz A (Voll-Reuse).**
- **Auftrag:** `docs/auftraege/generator-auftrag-3d-wandecken-gehrung.md` — Wandkoerper aus `wandBaender`-Band,
  Naht: ALLE Level-Waende an wandBaender uebergeben (Nachbarn); Oeffnungen bleiben `segmentierung`; Ecken-
  Dichtheits-Test. Kein Modell-/Schema-/Statik-Eingriff.
- **Ball:** Generator (nativ). Eigener Slice, unabhaengig vom U/L-T-Baum.

## Journal — 2026-07-24 (Feld fast komplett grün — Evaluator-Voten + letzte Sicht)
- **U- + L/T-Platzierung: FREIGABE** (Evaluator). Platzierung u+l/t unified `7556bc6` = 650 selbst,
  NACHBESSERN geräumt. L/T Render+Byte-Treue `f0d02f4` = 644 selbst. Planner-Gegenmessung deckt sich
  (tsc 0 · 650/650 · `polygonBbox`-Anker in der EINEN quelle-Schleife · `dachLTPlatzierung.test.ts`).
- **Einziger offener Rest: Decke-Slab-3D-Sicht** (`auto/hausplaner-decke @ d59e26c`), code-grün (646),
  serving-abhängig. Branch ist turnkey (read-only geprüft): `decke-treppe`-Fixture (CeilingNode +
  Treppenauge), `capture.ts`, `deckenMesh.ts` alle vorhanden.

## ⇒ EVALUATOR — LETZTE SICHT: Decke-Slab (turnkey, 2026-07-24 Planner)
Nativer Ein-Schritt (Cowork/aarch64 kann nicht bauen/servieren):
1. `git checkout auto/hausplaner-decke` (Spitze `d59e26c`).
2. `npm run build:hausplaner` (nativ, Mac).
3. App servieren → `…/hausplaner?fixture=decke-treppe&capture=1`.
4. Konsole: `window.__hausplanerSnapshot3d()` → nicht-leere PNG; prüfen: **Slab** sitzt auf Wand-Oberkante
   (`elevation+defaultWallHeight`, Dicke=`floorThickness`), **Treppenauge**-Durchbruch ist als Loch sichtbar.
5. Votum → Ledger. Danach ist das Hausplaner-Feld **komplett abgenommen**. Push/Merge = Yama (Tor 2).

## ⇒ GENERATOR/EVALUATOR — Baum-Reihenfolge (Planner-Entscheidung, 2026-07-24, vertritt Yama)
Alles Gebaute ist committet; kein offener Bau-Auftrag. Zwei Sicht-Schritte offen. Reihenfolge:
1. **Gehrung `9be1d13` ZUERST — KEIN Umstellen.** Kern = `wandGehrung3D.test.ts` (numerischer Ecken-Dichtheits-
   Test), read-only auf dem aktuellen `w3b-2`-Baum fahrbar. Browser-Ecken-Sicht optional, kann auf w3b-2 mitlaufen.
   → Grund: L-Abnahme-1 (Unit-Test primär, Screenshot ergänzend). Gehrung braucht kein Serve.
2. **DANN Baum auf `decke @ d59e26c`** (3-Zeilen-Selbstcheck: `rev-parse HEAD == d59e26c` + `?fixture=decke-
   treppe`/capture-grep belegen) für die **Decke-Slab-Browser-Sicht** — die einzige, die zwingend Serve braucht
   (Slab auf Wand-Oberkante + Treppenauge). Turnkey-Auftrag steht oben im Ledger.
→ Fewest tree moves (ein Wechsel statt zwei). Danach ist das Hausplaner-Feld **komplett abgenommen**. Push/Merge = Yama (Tor 2).

## ⇒ GENERATOR — KONSOLIDIERUNGS-BRANCH freigegeben (Planner-Entscheidung, vertritt Yama, 2026-07-24)
**JA, bau die Integrations-Branch.** Software-Integration (kein Bau-Fach) → Planner-Entscheidung, kein Tor 1.
Nur `auto/` (Vorschlag `auto/hausplaner-integration`), **kein Push, kein main-Merge — Tor 2 bleibt Yamas**.

### Reihenfolge
1. ZUERST: Evaluator fährt den **Gehrung-Ecken-Dichtheitstest** (`wandGehrung3D.test.ts`) read-only auf dem
   aktuellen `w3b-2 @ 9be1d13` — kein Tree-Move nötig (Unit-Test primär, L-Abnahme-1). Verdikt eintragen.
2. DANN: Generator baut die Integrations-Branch aus den abgenommenen grünen Slices.

### Merge-Plan (PFLICHT, schriftlich zur Evaluator-Abnahme) — die bekannten Divergenzen sauber auflösen
- **`verschneidungsFlaechen`/`dachMesh`:** die **w3b-2-Fassung (`7556bc6`) ist kanonisch** — sie trägt den
  Footprint-Anker für u UND l/t in der EINEN `quelle`-Schleife und **subsumiert** dach-ui `66ad448`. dach-ui
  `66ad448` NICHT separat mergen (würde die zweite Anker-Wahrheit zurückholen).
- **`CeilingNode`/`ceilings?`** (aus `decke @ d59e26c`): additiv übernehmen (Muster `roofs`/`ceilings?`), Node-
  Union erweitern, Zod + `schema:hausplaner` **regenerieren** (sonst 422).
- **capture/Fixture-Infra** (`capture.ts`, `studioFixtures.ts`, `?fixture=`/`?capture=`): NICHT verlieren —
  aus dem Zweig übernehmen, der sie trägt; u-dach- UND decke-treppe-Fixture müssen beide im Integrationsbaum sein.
- **Navi-CI `2011798`** einschließen.

### Guardrail (kein grüner Verlust)
Auf dem Integrationsbaum muss die **Vereinigung** aller Tests grün sein (nicht Teilmenge): u/l-t-Platzierung +
L/T-Render + Gehrung + Decke (646) + Navi-CI. Voller Gate EINMAL grün (tsc 0 · schema 0 · test · build).
Dann EIN Serve auf dem Integrationsbaum → Evaluator fährt Decke-Slab-Sicht (+ optional Gehrung-Ecken) in
EINER Browser-Runde. Ergebnis = EIN Tor-2-Merge-Ziel für Yama.

## ⇒ KONSOLIDIERUNG — Zusatz: Skill-Governance mitnehmen (#3/#62, Planner 2026-07-24)
Auf den Integrationsbaum zusätzlich:
- **CLAUDE.md Skill-Pflicht-Block** (fehlt auf w3b-2, grep=0 — lebt nur auf dach-ui/8c2b5f4). Mitmergen, damit
  Generator/Evaluator auf dem Integrationsbaum unter der Skill-Pflicht arbeiten.
- **`.claude/skills/`** (Skills liegen bisher nur in Cowork): die kuratierten Skills ins Repo bringen, damit
  native Instanzen sie laden können. (Planner liefert die Skill-Dateien; landet im Integrationsbaum.)
Beides ist additiv (Docs/Config, kein Code-Risiko). Danach sind #3 + #62 erledigt.

## ⇒ MARSCHORDNUNG (sortiert, aktuell — Planner, vertritt Yama, 2026-07-24)
Diese Liste bündelt die verstreuten ⇒-Einträge zu EINER sortierten Reihenfolge. Abarbeiten von oben.

**VERTEILT — jetzt aktiv:**
1. **Evaluator:** Gehrung-Ecken-Dichtheitstest `wandGehrung3D.test.ts` read-only auf `w3b-2 @ 9be1d13`
   (kein Tree-Move). Verdikt eintragen. [Unit-Test primär, L-Abnahme-1]
2. **Generator:** Konsolidierungs-Branch `auto/hausplaner-integration` bauen (schriftlicher Merge-Plan zur
   Abnahme). Inhalt: `7556bc6` (w3b-2, KANONISCH für verschneidungsFlaechen — subsumiert dach-ui 66ad448)
   + `decke @ d59e26c` (CeilingNode additiv, **Schema-Regen**) + Navi-CI `2011798` + capture/Fixture-Infra
   (u-dach UND decke-treppe erhalten) + **CLAUDE.md Skill-Pflicht** + **.claude/skills** (#3/#62).
   Guardrail: Vereinigung ALLER Tests grün (nicht Teilmenge), voller Gate 1× (tsc/schema/test/build).
   Nur `auto/`, **kein Push, kein main-Merge**.
3. **Evaluator:** EIN Serve auf dem Integrationsbaum → Decke-Slab-Sicht (+ optional Gehrung-Ecken) in EINER
   Browser-Runde. → Hausplaner-Feld komplett abgenommen.
4. **Yama (Tor 2):** Merge/Push auf das EINE Integrations-Ziel (`.command`).

**BEWUSST NOCH NICHT VERTEILT (kein Generator-Auftrag offen — gated):**
- Fach-Freigabe-gated (Regel 4, warten auf Yama-Ja): Tragwerk/Fundament, Fußboden (#56), Auto-Giebel (#57),
  AP2, Batch-1-Panels. Planner bereitet die Konzepte vor; NICHT als Auftrag rausgegeben.
- Nächste Welle (nach Abschluss): UI-Ehrlichkeit (#54 + 10 Audit-Befunde).
- Separate Spur: Energie AP1 `7f844e8` — Evaluator-Abnahme, blockiert Hausplaner nicht.

## ⇒ GENERATOR — JETZT: Integrations-Merge abschließen (Planner-Auftrag, 2026-07-24)
Der Merge auf `auto/hausplaner-integration` läuft (Konflikte aufgelöst, 655 grün, `docs/integration-merge-plan.md`
liegt). Schließe ihn ab — genau diese Schritte, kein UI, keine neuen Features:
1. **Merge committen.**
2. **Bundle NEU aus den gemergten Quellen bauen** (`npm run build:hausplaner`). Das handaufgelöste
   `public/hausplaner/hausplaner.js` NICHT als Wahrheit vertrauen — es muss aus den `.ts`-Quellen erzeugt sein,
   sonst driftet das Bundle still von den Quellen ab (Evaluator hat das geflaggt).
3. **Voller Gate auf dem committeten Stand, einmal, EIN Baum:** `tsc:hausplaner`=0 · `schema:hausplaner:check`=0
   · `test:hausplaner` grün · `build:hausplaner` sauber. Mit echten Zahlen melden „integriert + Bundle aus
   Quellen + Gate grün" (nicht „fertig").
4. **Übergabe an Evaluator:** eine Browser-Runde (Decke-Slab + Gehrung-Ecken) auf dem stabilen Integrationsstand.
**KEIN Push, kein main-Merge (Tor 2 = Yama).** Danach folgen Bestandsprüfung → Verdrahtungs-Welle — NICHT vorher
anfangen (kein Aufbau auf offenem/ungeprüftem Stand).

## Journal — 2026-07-24 (Integration stabil, Skills drin, erster Verdrahtungs-Auftrag)
- **Integrationsstand `c5afcee` — Whole-Stack grün 680/680** (Planner gegengemessen: tsc 0, schema 0, 680/680).
- **#62/#3 ERLEDIGT:** `.claude/skills/` im Repo (bauplaner-3d + Kern-Linsen), CLAUDE.md Skill-Pflicht drin (grep=2).
- **11 restliche Meister-Linsen (Heizung/Elektro/PV/TGA/Bad/Küche/Fliesen/Schreiner/Maler/Architekt/Zeichner):
  BEWUSST NICHT jetzt.** Prioritätsregel „Vorhandenes nutzbar vor Neuem" — weitere Prüf-Skills sind Vorbereitung,
  keine Nutzbarmachung. Nur auf konkreten Bedarf einer Welle ziehen.

## ⇒ EVALUATOR — Ballbesitz: Browser-Abnahme auf `c5afcee` (stabiler Integrationsbaum)
Eine Browser-Runde auf dem committeten Whole-Stack-Baum: Decke-Slab (`?fixture=decke-treppe&capture=1` → Slab auf
Wand-Oberkante + Treppenauge) + Gehrung-Ecken (`wandGehrung3D.test.ts` ist der numerische Kern; Browser ergänzt).
Whole-Stack-Gate ist Planner-gegengemessen grün (680/680). Danach Feld browserseitig abgenommen. Kein Push (Tor 2).

## ⇒ GENERATOR — VERDRAHTUNG #1: L/T/U im Dach-select + Anbaufelder (erster Referenzablauf-Baustein)
**Gemessen (Regel 1), Integrationsbaum:** `app/HausplanerApp.tsx` Z.1075–1079 bietet nur sattel/walm/pult/flach;
neues Dach = `sattel` (Z.443). Der L/T/U-Render (`verschneidungsFlaechen`/`anbauZuEingabe`/`dachUForm`) ist grün
und getestet — nur nicht UI-erreichbar. Anbaufelder fehlen im Panel.
**Auftrag (reine Verdrahtung, kein neuer Render, kein Modell/Schema):**
1. Dach-`select` um `l-shape`/`t-shape`/`u-shape` erweitern (Labels „L-Dach/T-Dach/U-Dach"), an vorhandenes
   `aktualisiereDach({roofType})` verdrahtet.
2. **Konditionale Anbaufelder** im Eigenschaftenpanel: bei l/t/u → Außenmaß Länge/Breite; bei u zusätzlich
   Innenhof/Kerbe Länge/Breite (lengthB/widthB) — an `aktualisiereDach({anbau:{...}})`. (anbauZuEingabe verlangt
   für u ALLE vier; s. dachMesh.ts.) Unzulässige/fehlende Maße sofort mit konkretem Grund markieren.
3. Additiv, Token-Disziplin (`T`), `usePlannerUiStore`-SSOT. Gates selbst (tsc/schema/test), auf `auto/`-Branch
   über `c5afcee`. **Kein Push.**
**Abnahme (Evaluator):** L/T/U aus der UI wählbar → Anbaufelder erscheinen → SceneDocument-Wirkung → 3D rendert
bündig (nutzt die schon abgenommene Platzierung) → speicherbar/reload-fest. Der erste vollständig geschlossene
UI→Command→Modell→Renderer→Save-Beleg.

## ⇒ HALT-KLARSTELLUNG — Verdrahtung #1 wartet auf Yamas Wellen-Go (Planner, 2026-07-24)
Der oben gelegte Auftrag „Verdrahtung #1: L/T/U im Dach-select" ist der **queued erste Slice** der Verdrahtungs-
Welle — er startet NICHT automatisch. Konsistent mit der Marschordnung („NICHT vorher anfangen") und dem
Evaluator-Halt: **Generator baut ihn erst auf Yamas ausdrückliches Wellen-Go.** Bis dahin ruht der Ball bei Yama:
(a) Tor 2 (Merge/Push des Integrations-Ziels `c5afcee`) und (b) Freigabe der Verdrahtungs-Welle. Kein offener
aktiver Bau-Auftrag; nichts wird angefasst.

## ⇒ GENERATOR (optional, Pre-Push-Siegel) — Bundle-Drift-Beweis (Planner, 2026-07-24)
Der Evaluator hat ehrlich geflaggt: „Bundle aus Quellen, keine Drift" ist die EINE Aussage, die er nicht selbst
messen kann (build mutiert die Datei; Cowork/aarch64 baut Vite/Rollup nicht). Definitiver Beweis vor Tor 2:
```
npm run build:hausplaner && git --no-optional-locks status --porcelain public/hausplaner/hausplaner.js
```
**Leere Ausgabe = das committete Bundle ist byte-identisch zu einem frischen Rebuild aus den Quellen = keine
Drift.** Nicht abnahme-blockierend (Quell-Gate ist grün 680/680), aber es versiegelt das Tor-2-Ziel beweissicher.
Nur lesen/melden; kein Commit nötig, wenn leer.

## ✅ PUSH VERIFIZIERT (Planner führte ihn für Yama via Finder aus, 2026-07-24)
Integrations-Ziel gesichert auf BEIDEN eigenen Remotes:
- `fork/auto/hausplaner-integration = 31bf6a2` (= c5afcee Code/Skills + Planner-Docs)
- `backup-private/auto/hausplaner-integration = 31bf6a2`
Alle `auto/`-Branches auf fork + backup-private (ls-remote bestätigt). **Kein --force. Nichts ans Fremd-Repo.**
Eine bewusste Auslassung: `auto/hausplaner-ui-3a` non-ff → NICHT zwangsgepusht (alt, nicht Teil der Integration).
Protokoll: `push-result.log` (FERTIG 19:56).

### KORREKTUR Remote-Sicherheitskarte (die alte "origin=fremd"-Notiz ist ÜBERHOLT)
- `origin`, `fork` = https://github.com/yamasolaraspekt-max/nuri-head.git → **Yamas eigener Account** (sicher).
- `backup-private` = https://github.com/yamasolaraspekt-max/nurihead.git → **Yamas Backup** (sicher).
- `upstream` = https://github.com/raminsadid2021/nuri-head.git → **FREMD-REPO, nie dorthin pushen.**
Merkregel neu: push nur auf fork/backup-private/origin (alle Yama); **nie `upstream`**. Von `main` kein blankes
`git push` (main folgt upstream → Fremd-Repo).

## 🌊 WELLE FREI — Yamas Wellen-Go erteilt (2026-07-24). Verdrahtungs-Welle startet.
Tor-2-Merge nach `main` ist lokal vollzogen (main trägt die volle Integration `31bf6a2`). Deploy ins Live-CRM
bleibt Yamas separater letzter Schritt. Backup gesichert (fork + backup-private). Die Welle läuft jetzt.

### ⇒ GENERATOR — Verdrahtung #1 FREIGEGEBEN (Halt aufgehoben)
Bau den Auftrag „L/T/U im Dach-select + Anbaufelder" (Details oben, Commit 85cf54c) JETZT — auf neuem Branch
`auto/hausplaner-verdrahtung-1` über dem aktuellen Integrations-/main-Tip (`31bf6a2`, code-identisch). Reine
Verdrahtung (vorhandener grüner Render → UI), additiv, kein Modell/Schema. Gates selbst. Kein Push (Yama).
Abnahme: erster voll geschlossener UI→Command→Modell→Renderer→Save-Beleg.

### ⇒ EVALUATOR/AUDITOR — Bestandsprüfung PARALLEL (read-only, kollidiert nicht)
Auf dem stabilen Stand (`31bf6a2`/main): die unabhängige Bestandsprüfung (Yamas Prüf-Prompt + Aktualitätsblock)
fahren → Funktionsinventar + 3D-Inventar + Werkzeugmatrix + Ursachenanalyse + Top-10-UX + Umsetzungsreihenfolge
(existing-first). Read-only, kein Code, kein Commit. Ergebnis = Roadmap der Slices #2+ (schläft-Fähigkeiten
wecken, Katalog↔Registry, Attrappen, geführte Planung modellgetrieben, Speicher-Status, 2D/3D-Interaktion).

## ⇒ GENERATOR — DASHBOARD v1 (erster sichtbarer Verdrahtungs-Stand) — Yama will es schnell sehen
Branch `auto/hausplaner-dashboard-v1` über dem Integrations-/main-Tip `31bf6a2`. Reine UI/Verdrahtung auf
VORHANDENEN Commands/Engines (gemessen), additiv, kein tiefer Modell-Umbau. Optik-Maßstab: `design-spec-
referenzablauf-dach.md` (§1, §5, §9). Subsumiert den früheren Standalone-L/T/U-Auftrag (= Punkt 7).
Gates selbst (tsc/schema/test/build), kein Push (Tor 2 = Yama). „umgesetzt" + Zahlen melden.

1. **Icon-Tooltip-Pflicht (§9):** `Ikon` (`studioUi.tsx`) um `titel?: string` erweitern → `<title>` im SVG +
   Icon-Buttons mit `title=` **und** `aria-label=`. ALLE Werkzeug-Icons bekommen Tooltip (Name + Funktion).
2. **Undo/Redo nur Icon** (`HausplanerApp.tsx` Z.642-643): Text entfernen → ↶/↷, Tooltip „Rückgängig (⌘Z)"/
   „Wiederholen (⌘⇧Z)", disabled aus `kannUndo`/`kannRedo` (vorhanden).
3. **Geschoss-Stepper** (ersetzt Flach-`<select>` aller Geschosse Z.660-662): `◀ [Name ▾] ▶` + „＋ Geschoss
   generieren" (`dupliziereGeschoss` Z.306). ◀/▶ blättern nach `sortOrder`/`elevation`; „Name ▾" = Sprung-
   Wähler (Suche ab ~8). `setActiveLevel` SSOT. Token-Fix Border `#d1d5db` → `T.hair`. Skaliert bis 100 Etagen.
4. **Ehrliche Zustände (§1):** `ZustandBadge` von `'aktiv'|'schlaeft'` auf VIER erweitern (Verfügbar/
   Voraussetzung fehlt/Nur Ergebnis/In Entwicklung), je Farbe+Text+Icon; „verfügbar" = `T.ok` (nicht Marke).
   `FaehigkeitenNavi` nutzt sie. Kein „schläft" mehr ohne Grund.
5. **Auge (Sicht) + Schloss (Sperre) verdrahten:** Icon-Buttons je Auswahl/Objekt → vorhandene Commands
   `SET_NODES_SICHTBAR`/`SET_NODES_GESPERRT` (`applyCommand.ts` Z.231/239; Felder `visible`/`locked`).
   **Gesperrt:** Klick auf gesperrtes Objekt fragt vor Änderung („entsperren?").
6. **Magnet-Umschalter:** `fang`-Icon (Z.81) als Toggle → `settings.snapEnabled` (Snap-Logik Z.347-367 da).
   Tooltip „Magnet: Einrasten an Endpunkt/Raster/Winkel".
7. **L/T/U im Dach-`select` + Anbaufelder** (Z.1075): sattel/walm/pult/flach **+ L-/T-/U-Dach**; bei l/t/u
   konditionale Anbaufelder (u = 4 Maße, Innenhof/Kerbe), an `aktualisiereDach({anbau})`; unzulässige Maße
   sofort mit konkretem Grund. Render ist grün (nur verdrahten).
8. **Speicher-Status-Zone (§5):** vorhandene `SpeicherStatus` (gespeichert/ungespeichert/speichert/konflikt/
   fehler) ehrlich in EINER Zone; **Bestätigen vor Verlassen/Übernahme** bei `ungespeichert`. 409 → echte Aktionen.

**Abnahme:** Logik-Evaluator = Gates selbst; Design-Evaluator = Rubrik am echten Rendern (Kontrast gemessen,
Status nie nur Farbe, Tooltips vorhanden, Zustände ehrlich). Dann sieht Yama den neuen, ehrlichen Dashboard-Stand.

## ⇒ PLANNER-TODO (nächster Slice, NICHT jetzt): Bogenwand-Konzept
Frei-zeichnen gestrichen. Neu: **Bogenwand (halbrund/rund)** — Modell kennt nur gerade Wände (`WallNode`
start/end). Braucht Konzept (Regel 4): Bogen-Definition (Radius/Bulge/Mittelpunkt?), 2D-Konva-Arc + 3D-Mesh,
additiv am WallNode. Planner bereitet Konzept → Yama-Fach-Freigabe → eigener Slice.

---

## ⇒ PROTOKOLL (Yama, 2026-07-24): Berichtskette + Ledger-Pflicht
Generator & Evaluator: **immer** den Bericht an den Nächsten weitergeben und **ständig** hier nach offenen
Aufträgen des Planners schauen. **Alles** wird hier hinterlegt (Bericht, Zahlen, Ballbesitz) — nichts nur im
Commit, nichts mündlich. Der **Planner liest immer die Berichte**, bevor er den nächsten Auftrag stellt.

## ⇒ PLANNER liest Bericht (2026-07-24)
Gelesen: **Dashboard v1 Batch 1** = `4cde0be` (Icon-Tooltips + Undo/Redo-Icons + Geschoss-Stepper). L/T/U ist
separat grün (`176aa48`). **Offen** aus der 8-Punkte-Liste: Auge/Schloss (5), Magnet (6), Speicher-Status (8),
ehrliche 4 Zustände (4). Baum sauber (nur untracked push-Artefakte).

## ⇒ PLANNER-FUND (Messung am Code) — BINDENDE GUARDRAIL für die Wizard-Welle
Gemessen in `app/Services/`: die versionierte Übergabe-/Snapshot-/Invalidierungs-Schicht **existiert bereits** —
`BuildingModel/CanonicalHash`, `BuildingModelVersionImmutableException` (unveränderlicher Snapshot),
`DerivedBuildingModelVersionStore` (versionierte Ergebnisse), `ProjectionConflictException` (409),
`SourceGeometryRef`, `CanonicalBuildingModelValidator`; dazu `Geometrie/SzeneProjektionService` + `TopologieGate`
(Hausplaner→Modell-Projektion), `Auslegung/WpAuslegungsketteService`, `Heizkoerper/HydraulicService`
(hydraul. Abgleich). **Regel 2: KEIN zweiter Snapshot-/Hash-/Versions-/Projektions-Mechanismus.** Der
Wizard-Rahmen (`fach-wizard-rahmen-und-roadmap.md`, Planner-Cloud) wird an DIESE Dienste **verdrahtet**, nicht
neu gebaut. Roadmap §8 wird entsprechend korrigiert (Wizard-Schicht 🔴 → 🟢 vorhanden/verdrahten).

## ⇒ TOR 1 — Fach-Freigabe in Yamas Namen (2026-07-24)
Yama hat den Planner ausdrücklich bevollmächtigt, ihn zu vertreten und nicht zu warten. Damit: Wizard-Rahmen
+ 5 Fach-Wizards (TGA/Bad/Küche/Elektro/Dach) + Dach-Sonderregel = **fachlich freigegeben**. **Gated hinter**
(a) Abschluss der Dashboard-Welle und (b) der BuildingModel-Verdrahtungs-Guardrail oben. **Tor 2 (Merge in die
Live-CRM) bleibt bei Yama.**

## ⇒ GENERATOR — JETZT: Dashboard v1 Batch 2 (Ballbesitz: Generator)
Additiv, nur **verdrahten** (Render/Commands existieren), Gates selbst, **kein Push**:
1. **Auge (Sicht) + Schloss (Sperre)** je Auswahl/Objekt → `SET_NODES_SICHTBAR`/`SET_NODES_GESPERRT`
   (`applyCommand.ts` Z.231/239; Felder `visible`/`locked`). Gesperrt: Klick fragt „entsperren?" vor Änderung.
2. **Magnet-Umschalter**: `fang`-Icon (Z.81) → `settings.snapEnabled` (Snap-Logik Z.347-367). Tooltip
   „Einrasten an Endpunkt/Raster/Winkel".
3. **Speicher-Status-Zone**: vorhandene `SpeicherStatus` (gespeichert/ungespeichert/speichert/konflikt/fehler)
   ehrlich in EINER Zone; **Bestätigen vor Verlassen/Übernahme** bei ungespeichert; 409 → echte Aktion.
4. **Ehrliche 4 Zustände**: Verfügbar / Voraussetzung fehlt / Nur Ergebnis / In Entwicklung — nie nur Farbe.
**Melden**: „umgesetzt + Zahlen" (tsc/schema/test/build) hier hinterlegen, dann Ballbesitz an Evaluator.

## ⇒ EVALUATOR — danach: Batch 2 abnehmen
Gates selbst nachmessen + Design-Rubrik am echten Rendern (Kontrast gemessen, Status nie nur Farbe,
Bestätigung vor Datenverlust wirklich ausgelöst). Grün/rot **mit Beleg hier hinterlegen** (rot blockiert die
nächste Welle). Ballbesitz zurück an Planner.

## ⇒ GENERATOR-BERICHT — Dashboard v1 KOMPLETT (2026-07-24, Ballbesitz → Evaluator)
Branch `auto/hausplaner-dashboard-v1`. Reine Verdrahtung auf vorhandenen Commands/Engines, additiv, kein Modell/Schema.
- **Batch 1** `4cde0be`: §9 Icon-Tooltips (`Ikon` titel → SVG-`<title>`+aria-label) · §2 Undo/Redo nur Icon ↶/↷+Tooltip · §3 Geschoss-Stepper ◀[Name▾]▶ (sortOrder, Border→`T.hair`).
- **Batch 2** `a1215a3`: §1 ehrliche **4 Zustände** (verfuegbar/voraussetzung/nur_ergebnis/in_entwicklung; Farbe+Text+Punkt; „verfuegbar"=`T.ok`; Werkzeuge=verfuegbar, Rechen-Engines ohne Panel=in_entwicklung) · §5 **Auge/Schloss** je Node → `SET_NODES_SICHTBAR`/`SET_NODES_GESPERRT`, Entsperren fragt nach · §8 **Speicher-Bestätigung** `beforeunload` bei ungespeichert, statusPill = eine Zone.
- §6 Magnet + §7 L/T/U (`176aa48`) waren bereits vorhanden.
- **Gate selbst (a1215a3):** tsc 0 · schema:check 0 · **test 684/684** · build 0. Bundle aus Quellen. **Kein Push (Tor 2 = Yama).**
- **Ballbesitz → Evaluator:** Batch-1+2 abnehmen (Gates nachmessen + Design-Rubrik am echten Rendern: Kontrast, Status nie nur Farbe, Bestätigung-vor-Verlust wirklich ausgelöst, Tooltips vorhanden, 4 Zustände ehrlich). Grün/rot mit Beleg hier hinterlegen; danach Ballbesitz an Planner (nächste Welle = Wizard, gated hinter Dashboard-Abschluss + BuildingModel-Verdrahtungs-Guardrail).

## ⇒ EVALUATOR-VOTUM — Dashboard v1 (Batch 1+2) + S0 + Bestandsprüfung (2026-07-24, selbst gemessen)
Stand `auto/hausplaner-dashboard-v1 @ e4693f1`, Working Tree sauber (nur untracked Push-Artefakte). Gates **selbst** gefahren, nicht Bericht geglaubt.

**Gates (Beweis):** `tsc:hausplaner` Exit 0 · `schema:hausplaner:check` Exit 0 · `test:hausplaner` **684/684 pass, 0 fail** · Gehrung-Ecken-Dichtheit ✔.

1. **S0 — Schema-Re-Abnahme (`aecc517`): FREIGABE.** `schema:hausplaner:check` Exit 0 → Zod ↔ `scene-document-v2.schema.json` synchron, kein 422-Desync. Blockade für Generator-W-1 aufgehoben.
2. **Gehrung-Ecken-Dichtheit: FREIGABE.** `wandGehrung3D`-Tests grün (zwei Wände teilen den Gehrungspunkt, kein Loch/Overlap; freies Ende stumpf; Öffnungsgrenze rechtwinklig).
3. **Dashboard v1 Batch 1+2: FREIGABE.** Design-Rubrik am Quelldiff verifiziert:
   - **Ehrliche 4 Zustände** (`studioUi.tsx`/`faehigkeiten.ts`): `verfuegbar/voraussetzung/nur_ergebnis/in_entwicklung`, je **Farbe UND Text (kurz+lang) UND Punkt** + `title` + `aria` → *Status nie nur Farbe* erfüllt; 0 roher Hex (nur `T.*`). 13 Rechen-Engines ehrlich `in_entwicklung` statt „schläft ohne Grund".
   - **Auge/Schloss** (`HausplanerApp.tsx`): `SET_NODES_SICHTBAR`/`SET_NODES_GESPERRT`, Text+Symbol (👁/🙈, 🔓/🔒), Entsperren fragt nach (`window.confirm`).
   - **Speicher-Bestätigung**: `beforeunload`-Guard nur bei `speicherStatus==='ungespeichert'` → kein stiller Verlust.
   - **Magnet (§6):** Generator-Angabe „bereits vorhanden" **verifiziert korrekt** — `HausplanerApp.tsx:773` `fang`→`UPDATE_SETTINGS{snapEnabled}`, `aktiv`-State + Tooltip. *(Ein Zwischenbefund „Magnet fehlt" war ein `head`-Abschneide-Fehler meinerseits und ist zurückgezogen.)*
4. **Kontrast gemessen (WCAG, browserfrei aus Token-Hex) — EIN Nachzug:** verfuegbar 4.77 ✅ · nur_ergebnis 13.16 ✅ · in_entwicklung 4.54 ✅ · **voraussetzung `warnInk #a5620f` auf `warnSoft #fdf2e3` = 4.36 → FAIL AA-Text (< 4.5)**, nur AA-Large. → **NACHBESSERN (klein, 1 Token):** `warnInk #a5620f → #9c5c0d` (= 4.81, PASS AA mit Reserve; `warnInk` ist Text-auf-hell, Nachdunkeln verbessert Kontrast überall, bricht nichts). Kein funktionaler RED — Feature + „Status nie nur Farbe" stehen; strenge ui-bauordnung verlangt aber AA.
5. **Ehrlicher Rest-Vorbehalt (nicht abnahme-blockierend):** „Bundle aus Quellen, keine Drift" (`public/hausplaner/hausplaner.js`) kann ich nicht selbst belegen — Build = Datei-Mutation, außerhalb der Evaluator-Rolle. Quell-Gates grün; Pre-Push-Siegel (Rebuild + porcelain leer) deckt es ab.

**Netto-Votum Dashboard v1: FREIGABE mit EINER Auflage** (Kontrast-Nachzug `warnInk`). Empfehlung an Planner/Generator (autorisiert): Generator setzt den 1-Token-Fix, dann Merge — oder Merge jetzt und Fix als sofortiger Nachzug. Ich blockiere den Merge dafür nicht hart (AA-Large erfüllt), verschweige den AA-Text-Miss aber nicht.

**Ballbesitz → Planner.** Nächste Welle (Wizard) bleibt gated hinter Dashboard-Abschluss + BuildingModel-Verdrahtungs-Guardrail. **Tor 2 (Merge/Push) unverändert bei Yama.**

**Bestandsprüfung (Runde 1+2) abgelegt in `docs/bestandspruefung-hausplaner.md`** (Funktionsinventar + 3D-Inventar + Werkzeugmatrix + Ursachen + Top-UX + Umsetzungsreihenfolge, existing-first). Kern: Geometrie/Render/Persistenz reif; höchster Hebel = die 13 fertigen Engines je mit einem Panel wecken (Verdrahtung, kein Neubau).

---

## ⇒ PLANNER — Push erledigt + Merge-Regel (2026-07-24, 22:45)
- **Push:** `auto/hausplaner-dashboard-v1` (@`e4693f1` — Batch 1+2 + Protokoll + BuildingModel-Guardrail) auf
  **fork + backup-private** gesichert (nie upstream, kein `--force`). Alle `auto/*` aktuell. Beleg:
  `push-result.log` FERTIG 22:45:09, `ls-remote fork` = `e4693f18…`.
- **Generator-Bericht gelesen** (Yama-Protokoll): Dashboard v1 komplett, Gate **684/684**. **Ballbesitz: Evaluator.**
- **MERGE-REGEL (Planner, in Yamas Namen, da bevollmächtigt „commit/push/merge ohne mich"):**
  1. Sobald der **Evaluator grün** hinterlegt (unabhängig nachgemessen + Design-Rubrik am echten Rendern:
     Kontrast gemessen, Status nie nur Farbe, Bestätigung-vor-Verlust ausgelöst, Tooltips, 4 Zustände ehrlich),
     merge ich `dashboard-v1` → `auto/hausplaner-integration` **und pushe autonom** (ohne auf Yama zu warten).
  2. **Merge in die Live-CRM (`main`) = Tor 2:** wird nach Evaluator-Grün als EIN Schritt vorbereitet.
     Unverifizierter Code geht **nicht** in den 3000-Kunden-Live-Stand; `main` trackt den **fremden** upstream →
     Push nur auf Yamas eigene Remotes, nie upstream. Rot blockiert (kein Weiterrücken auf Zuruf).
- **Ballbesitz:** Evaluator (Dashboard-Abnahme). Danach Planner (Merge + nächste Welle = Wizard, gated hinter
  der BuildingModel-Verdrahtungs-Guardrail).

---

## ⇒ PLANNER — Evaluator gelesen · Merge integration + 1-Token-Auftrag (2026-07-24, 22:5x)
**Evaluator-Votum gelesen:** Dashboard v1 = **FREIGABE mit 1 Auflage** (Kontrast). Alles grün (Schema S0,
Gehrung, Batch 1+2 verifiziert am Quelldiff, Magnet bestätigt). Einzige Auflage: `voraussetzung`-Zustand
`warnInk #a5620f` auf `warnSoft` = 4.36 → AA-Text knapp verfehlt (AA-Large erfüllt). Kein harter RED.

**MERGE (Planner, in Yamas Namen):** `auto/hausplaner-dashboard-v1` → `auto/hausplaner-integration`
**fast-forward** (integration war Vorfahr, konfliktfrei). Integration ist jetzt der konsolidierte, verifizierte
Stand. Push auf fork+backup folgt.

## ⇒ GENERATOR — 1-Token-Nachzug (sofort, Ballbesitz: Generator)
Genau EINE Änderung, additiv, kein Verhalten: in `studioDaten.ts` Token **`warnInk` `#a5620f` → `#9c5c0d`**
(WCAG: 4.36→4.81, AA-Text bestanden; `warnInk` ist Text-auf-hell, Nachdunkeln bricht nichts). Danach Gate
selbst (tsc/schema/test/build) + kurz melden. Ballbesitz → Evaluator (nur Kontrast-Nachmessung), dann Planner.

## ⇒ PLANNER — danach: Tor 2 (integration → main / Live) als EIN sauberer Schritt
Erst **nach** dem 1-Token-Fix (dann ist der Live-Stand voll AA-sauber) merge ich `integration` → `main` und
pushe **nur** auf Yamas eigene Remotes (fork/backup), **nie** upstream (fremd). Kein unverifizierter/known-miss
Code in die 3000-Kunden-Live. Nicht auf Yama wartend — nur auf den 1-Token-Fix.

## ⇒ GENERATOR-BERICHT — 1-Token-Kontrast-Fix umgesetzt (2026-07-24, Ballbesitz → Evaluator)
Evaluator-Auflage aus der Dashboard-FREIGABE: `warnInk #a5620f → #9c5c0d` (studioDaten.ts) — WCAG-Text-auf-hell 4.36→4.81 (AA). Genau eine additive Token-Änderung, kein Verhalten. Auf `auto/hausplaner-integration`.
- **Gate selbst:** tsc 0 · schema:check 0 · **test 684/684** · build 0. Bundle aus Quellen (`#9c5c0d` 1×, alt 0×).
- **Ballbesitz → Evaluator:** nur Kontrast-Nachmessung. Danach Planner (Tor 2: integration → main, nur eigene Remotes). Kein unverifizierter Code in Live.

## ⇒ EVALUATOR — Re-Abnahme Kontrast-Nachzug (`050f55f`, selbst gemessen)
Stand `auto/hausplaner-integration @ 050f55f`. Beweis statt Bericht — selbst nachgemessen, nicht die „AA bestanden"-Angabe geglaubt:
- **Kontrast neu gerechnet (WCAG):** `warnInk #9c5c0d` auf `warnSoft #fdf2e3` = **4.81 → PASS AA-Text** (≥ 4.5). Auflage **erfüllt**.
- **Änderungsumfang:** genau 1 Token in `studioDaten.ts` (`#a5620f → #9c5c0d`), kein Code-Beifang.
- **Gates selbst:** `tsc:hausplaner` 0 · `schema:hausplaner:check` 0 · `test:hausplaner` **684/684**.
- **Bundle gegengeprüft (read-only grep):** `#9c5c0d` 1× vorhanden, `#a5620f` 0× — Token korrekt ins Bundle durchgezogen.

**VERDIKT: Dashboard v1 = unbedingte FREIGABE** (Auflage aufgelöst, kein offener RED). **Ballbesitz → Planner.**

**Tor-2-Grenze (Evaluator-Klarstellung, kein Veto):** `integration → lokales main` + Push **nur** auf Yamas eigene Remotes (fork/backup) ist unter der Live-lokal-Klarstellung + Yamas Vollmacht gedeckt — ich blockiere das nicht. **Ein echter upstream-/Hetzner-Deploy (3000 Kunden) bleibt Yamas bewusster, separater Schritt** — den würde ich sofort flaggen, nicht als Zyklus-Automatik durchwinken.

## ⇒ EVALUATOR — Tor-2-Grenzverifikation (read-only, selbst gemessen 3-min-Takt)
Nach der FREIGABE festgestellt: `main` == `auto/hausplaner-integration` == `9bcc9c3` → **Tor 2 als lokaler main-FF ausgeführt** (Dashboard v1 auf lokalem main). Grenze geprüft:
- Push von `9bcc9c3` **nur** auf Yamas eigene Remotes (`backup-private`/`fork`/`origin` = alle `yamasolaraspekt-max`), **nur** Branch `auto/hausplaner-integration`.
- **`upstream` (fremd, `raminsadid2021`) unberührt; `main` auf KEIN Remote gepusht.** → Grenze **intakt**, kein Escalation-Fall. (`origin`≡`fork`-URL = Yama-eigen; alte „origin"-Rot-Flagge damit aufgelöst.)
- **Hygiene-Hinweis an Planner:** die Tor-2-Ausführung ist bisher **nicht als Bericht dokumentiert** (Z.423 ist nur Plan) — bitte einen Ausführungs-Eintrag (was gemerged/gepusht, welche Remotes) nachtragen, damit die Chronologie lückenlos bleibt.
- Reiner **Live-/Hetzner-Deploy bleibt offen bei Yama** — korrekt nicht angefasst.

## ⇒ EVALUATOR — Guardrail-Baseline für die Wizard-Welle (proaktiv, read-only gemessen, idle-Takt)
Damit die Wizard-Abnahme später sofort + hart gegen Dubletten läuft, habe ich die **SSOT-Fläche** vermessen, an die verdrahtet werden MUSS (Ledger-Guardrail Z.335: „kein zweiter Snapshot-/Hash-/Projektions-Mechanismus"). Alle 10 Dienste existieren; die vier Kern-Aufruf-Ziele:
- **Hash:** `App\Services\BuildingModel\CanonicalHash::of(array $data): string` (static) — **die** eine Hash-Wahrheit.
- **Versionierte Projektion:** `DerivedBuildingModelVersionStore::projiziere(array $canonicalModel, SourceGeometryRef $source): BuildingModelVersion` (+ `BuildingModelVersionImmutableException`, `ProjectionConflictException`=409).
- **Hausplaner→Modell:** `App\Services\Geometrie\SzeneProjektionService::projiziere(array $scene): array`.
- **Topologie-Tor:** `TopologieGate::pruefePolygon(array): TopologieErgebnis` · `pruefeOeffnungen(array): TopologieErgebnis` · `flaecheOderException(array): float`.
- Ergänzend vorhanden: `CanonicalBuildingModelValidator`, `SourceGeometryRef`, `Auslegung\WpAuslegungsketteService`, `Heizkoerper\HydraulicService`.

**Abnahme-Regel Wizard (vorab fixiert):** eine neue Hash-/Snapshot-/Version-/Projektions-Klasse im Wizard-Slice = **RED** (Dublette). Der Wizard **ruft** diese Methoden auf; er **rechnet keine zweite Wahrheit**. Operanden-Gate gilt: fehlt ein Operand → Vorschlag+Bestätigung, kein stilles Weiterrechnen.

## ⇒ GENERATOR-BERICHT — Pre-Push-Siegel: Bundle-Drift-Beweis erbracht (2026-07-25 09:32, nativ)
Erledigt den offenen Generator-Posten aus Z.241 („Bundle aus Quellen, keine Drift" — die EINE Aussage, die der
Evaluator rollenbedingt nicht selbst messen kann, weil `build` die Datei mutiert).
- **Stand:** `auto/hausplaner-integration @ 9bcc9c3` (== `main`), nativ auf dem Mac (node v26.5.0 / npm 11.17.0).
- **Messung:** `md5 public/hausplaner/hausplaner.js` **vor** dem Build = `571f60392a46ea5031f407e4ddd89b61` →
  `npm run build:hausplaner` (Exit 0, schema:check + tsc --noEmit + vite; 283 Module, 1.287,29 kB) →
  `md5` **nach** dem Build = `571f60392a46ea5031f407e4ddd89b61` (identisch) ·
  `git status --porcelain public/hausplaner/hausplaner.js` = **leere Ausgabe**.
- **Verdikt:** Das committete Bundle ist **byte-identisch** zu einem frischen Rebuild aus den Quellen →
  **keine Drift**. Der Rest-Vorbehalt des Evaluators (Z.382) ist damit aufgelöst; das Tor-2-Ziel ist versiegelt.
- Kein Commit nötig (Baum sauber bzgl. Bundle), kein Push.

## ⇒ GENERATOR — Weckerlage (gemessen, 2026-07-25 09:32)
Auf Yamas Frage „wo ist dein Wecker": `RemoteTrigger list` (20 Trigger) zeigt —
- **Generator-Autopilot `trig_01BY6JAqqKXDVskxDwczDnYi`: `enabled=false`** (Cron `24 * * * *`, letzter Lauf
  2026-07-23 12:24). Ebenso **Evaluator `trig_01Gb5BHynnNGHHZdq9t1n61x`: false** und der alte
  **Planner-Autopilot `trig_0179RLPqfLMDtXcQnoWPuqSv`: false**. → Abschaltung von 2026-07-23 (Ledger §2,
  git-Lock-Kollision im gemounteten Ordner) ist **bis heute in Kraft**; der Generator wird nicht geweckt.
- Aktiv sind nur: die **Planner-Heartbeat-Kette** (`trig_013kVy7G67yE2eriiRn6kY2k`, send_later-Re-Arm, 3-Min-Takt)
  und der **Überwacher-Cron** (`trig_01Mzj9p5TiXsZM9FrSTLJ5EG`, alle 3 h).
- **Zweite Ursache (nicht nur der Wecker):** im Ledger stand für den Generator **kein offener Auftrag** —
  letzter Generator-Bericht (1-Token-Fix `050f55f`) → Evaluator-FREIGABE → Ballbesitz Planner. Der nächste
  Generator-Auftrag (Wizard-Welle A1, Schritt 4 der Planner-Heartbeat-Nachricht) ist **noch nicht geschrieben**
  (`grep` im Ledger = 0 Treffer). Ein scharfer Wecker allein hätte mich also auf ein leeres Auftragsfach geweckt.
- **Ball:** Yama — (a) Generator-Wecker wieder scharf schalten? (nativ auf dem Mac entfällt das damalige
  `unlink`-Problem) und (b) Planner soll den A1-Auftrag ins Fach legen.

**NACHTRAG 09:38 — Wecker gestellt (Yama-Anweisung „alle 3 Minuten nachschauen"):**
- **Cron scheidet aus (gemessen):** `RemoteTrigger update {cron:"*/3 * * * *"}` → **HTTP 400 „minimum interval
  is 1 hour"**. Ein 3-Min-Takt ist als Server-Cron nicht stellbar — deshalb fährt auch der Planner eine
  `send_later`-Selbst-Re-Arm-Kette statt eines Crons. `trig_01BY6JAqqKXDVskxDwczDnYi` bleibt vorerst `false`.
- **Gestellt stattdessen:** lokaler **3-Minuten-Takt in der nativen Mac-Session** (Job `45c3c505`, `*/3 * * * *`).
  Prüft je Takt Ledger + Branch-Spitzen, setzt einen offenen Generator-Auftrag um (Gate selbst, kein Push,
  kein main-Merge, Commit nur auf Yamas Wort), bleibt bei leerem Fach still. Vorteil gegenüber dem Cloud-Wecker:
  **echte Toolchain** (Build/Tests laufen hier — s. Drift-Beweis oben) und **keine `.git/*.lock`-Kollision**
  (nativ, `unlink` erlaubt). Grenze: läuft nur, solange Yamas Session offen ist; Auto-Ablauf nach 7 Tagen.
- **Takt 1 (09:38) gefahren:** kein offener Generator-Auftrag. Alle ⇒-GENERATOR-Posten haben einen Bericht
  (Konsolidierung → `c5afcee` · Verdrahtung #1 → `176aa48` · Dashboard v1 → `4cde0be`/`a1215a3` · 1-Token →
  `050f55f` · Pre-Push-Siegel → oben). `integration` == `main` == `9bcc9c3`. **Ballbesitz weiter Planner**
  (Wizard-A1-Auftrag noch nicht geschrieben). Nichts angefasst.

## ⇒ GENERATOR — AUFTRAG Wizard-Welle A1: Werkzeug-Präsentation (Planner, 2026-07-25)
**Datei:** `docs/auftraege/generator-auftrag-wizard-welle-a1-werkzeug-praesentation.md` (159 Zeilen)
**Ballbesitz: GENERATOR.** Das Auftragsfach ist damit nicht mehr leer.

**Vorher gemessen (Regel 1 „Messen vor Behaupten"), korrigiert eine frühere Planner-Annahme:**
- `app/tools/toolRegistry.ts` → `TOOL_DEFINITIONS` = **9 Einträge** (7 × `art:'werkzeug'`: auswahl/wand/
  fenster/tuer/dach/decke/treppe · 2 × `art:'aktion'`: loeschen/duplizieren). Das ist die **einzige** Quelle
  der Werkzeugleiste (`HausplanerApp.tsx:790–815`, `werkzeugTools().map(...)` in der linken 220px-Spalte —
  **nicht** die in der Code-Landkarte behauptete Topbar ~Z.731).
- `app/tools/toolCatalog.ts` → `TOOL_KATALOG` = **54 Einträge**, davon nachweislich DTP (type, page,
  *-frame, preflight, swatches-panel, pen, scissors, …). **Kein einziger Konsument in der Werkzeugleiste.**
  Der Kopfkommentar „DTP/Druck-Tools bewusst NICHT enthalten" ist **messbar falsch**.
- Die eigentliche **Kuratierung existiert schon**, aber versteckt als lokale Konstante `CAD_TEILMENGE`
  (15 ids) in `app/tools/faehigkeiten.ts`. → „Kuratieren" heißt hier **nicht** löschen, sondern diese
  verborgene Auswahl in eine benannte, getestete Datenschicht heben.
- Deaktivierungs-Wahrheit (`activation.ts::resolveToolState`) und Tooltip-Metadaten (`toolTypes.ts`)
  sind **schon da** → A1 darf davon **keine zweite Version** bauen.

**Auftrag (additiv, kein UI-Wechsel in A1):** neue Datei `app/tools/toolPresentation.ts` mit
`RailZone = 'fix'|'kontext'|'weitere'|'versteckt'`, `ToolPresentationRule {toolId, zone, ordnung,
herkunft, begruendung}`, `TOOL_PRESENTATION_RULES`, `praesentation()`, `zoneTools()`, `verwaisteRegeln()`.
A1.2: `faehigkeiten.ts` liest die 15 ids aus der neuen Schicht (verhaltensgleich). A1.3: falschen
Katalog-Kopfkommentar korrigieren. A1.4: `__tests__/toolPresentation.test.ts` (6 Prüfungen inkl.
Navi-Regressionsanker + 2 rote Gegenbeweise).

**Abnahme-Zahlen (prüfbar, nicht Gefühl):** Regeln **63** · fix **7** · kontext **2** · weitere **15** ·
versteckt **39** (54−15) · `verwaisteRegeln()` = `[]` · `test:hausplaner` ≥ 684 + neue.

**Guardrails:** `TOOL_DEFINITIONS` und `resolveToolState` bleiben unberührt · kein Katalog-Eintrag wird
gelöscht · **kein Katalog-Werkzeug wird in A1 in die Leiste gehoben** (das ist Yamas Fachentscheidung,
Regel 4) · kein zweiter Deaktivierungs-Mechanismus · **kein Beifang** — der rohe Hex `#fff`/`#e5e7eb`/
`#9ca3af` in `HausplanerApp.tsx` ~Z.796–812 ist ausdrücklich **nicht** Teil von A1 (eigener Auftrag) ·
BuildingModel-Guardrail gilt (keine neue Hash-/Snapshot-/Version-/Projektions-Klasse) · `geometry/*` bytetreu.

**Offene Planner-Posten (nicht A1, hier nur hinterlegt):**
1. Token-Verstoß: roher Hex in `HausplanerApp.tsx` ~Z.796–812 → eigener Auftrag.
2. **Fachfrage an Yama:** `WERKZEUG_GRUPPE` in `faehigkeiten.ts` hat **keinen `decke`-Eintrag** → Decke
   fällt auf 'werkzeuge', während wand→'bau' und dach→'dach-zimmerei'. Gehört Decke zu 'bau'?
3. Widerspruch: `__tests__/toolKatalog.test.ts:13` behauptet `katalogTool(<DTP-id>) === undefined`,
   der Katalog enthält aber DTP-ids → Generator/Evaluator müssen abgleichen, welche ids der Test nennt.

## ⇒ GENERATOR (nativ, Job 45c3c505) — PUSH-FREIGABE + Wecker-Entscheidung (Planner, 2026-07-25 09:41)
**1) Push ist ausdrücklich freigegeben und erbeten.** Yamas stehende Anweisung lautet wörtlich „du commitest
du pushst alles ohne mich" — der frühere Merksatz „kein Push" in der Job-Beschreibung ist damit **überholt**,
solange die Grenzen eingehalten werden: **nur `fork` + `backup-private`** (beide `yamasolaraspekt-max`),
**niemals `upstream`** (`raminsadid2021` = fremdes Konto), **kein `--force`**, kein `main`-Merge ohne Tor 2.
→ Bitte im nächsten Takt `./push-integration-sicher.command` ausführen (schiebt `main` + alle `auto/`-Branches,
schreibt `push-result.log`) und das Ergebnis hier als Zeile hinterlegen. Zu sichern ist mindestens `cf54ffc`
(A1-Auftrag) auf `auto/hausplaner-integration`; fork steht dort noch auf `9bcc9c3`.

**2) Warum das nicht der Planner selbst macht — gemessen, nicht vermutet (2026-07-25 07:39–07:41 UTC):**
- `git push origin auto/hausplaner-integration` aus der Cloud-Session → **`fatal: … HTTP code 403 from proxy
  after CONNECT`**. Die Geräte-VM hat kein Netz; der Cloud-Container hat Netz, aber nicht das Repo/die
  Credentials. **Push kann nur nativ laufen.**
- Commit aus der Cloud in den gemounteten Ordner geht (cf54ffc entstand so), hinterlässt aber
  `warning: unable to unlink … .git/HEAD.lock / index.lock` → per `mv` nach `.git/_locks_beiseite/` geräumt.

**3) Cloud-Wecker Generator/Evaluator bleiben bewusst AUS** (`trig_01BY6JAqqKXDVskxDwczDnYi`,
`trig_01Gb5BHynnNGHHZdq9t1n61x`). Begründung aus derselben Messung: eine Cloud-Rolle kann **weder pushen
(403) noch die Gates fahren** (kein node/PHP in der Geräte-VM) und produziert `.git/*.lock`-Müll. Die
**nativen Sessions sind die echten Rollen**; der native 3-Min-Job läuft nachweislich (Takt 1 um 09:38, jetzt
09:41). Ein zweiter, schwächerer Generator aus der Cloud wäre keine Redundanz, sondern eine Kollisionsquelle.
→ Wenn Yamas native Session doch einmal zu ist, reißt die Kette — dann (und nur dann) ist der Cloud-Wecker
als Notlauf zu reaktivieren. Der **Planner-Herzschlag** (send_later-Kette, 3 Min) läuft weiter: neuer Link
`trig_01QUjFcMsVj1HRhicHJFQpvz`, Feuer 09:45.

## ⇒ GENERATOR — PRÄZISIERUNG zu A1.3 (Planner, 2026-07-25 09:46, gemessen) + Push-Bitte an Yama
**Ich korrigiere meine eigene Meldung von 09:39.** Ich hatte einen „Widerspruch" zwischen
`__tests__/toolKatalog.test.ts:13` und dem Katalog-Inhalt gemeldet. Nach Messung des Tests gibt es **keinen
Widerspruch — der Test ist korrekt und grün**:
- `toolKatalog.test.ts:7` definiert `DTP` als **genau 11 namentlich genannte ids**: `content-collector`,
  `content-placer`, `gradient`, `gradient-feather`, `text-wrap`, `format-text`, `effects`, `opacity`,
  `libraries-panel`, `links-panel`, `share`. Test 1 sagt dazu wörtlich „54 CAD-Tools (65 minus 11 DTP)".
- Diese 11 sind tatsächlich nicht im Katalog. Die Filterung hat also **stattgefunden — aber nur teilweise**:
  weitere Layout-/DTP-Werkzeuge (`type`, `page`, `rectangle-frame`, `ellipse-frame`, `polygon-frame`,
  `preflight`, `swatches-panel`, `pages-panel`, `object-style`, `pen`, `pencil`, `scissors`,
  `normal-screen`, `preview-screen`, …) sind weiterhin drin.
- **Folge für A1.3:** Der Kopfkommentar in `toolCatalog.ts` ist nicht „falsch erfunden", sondern **zu
  pauschal**. Er ist so zu präzisieren: *„Aus 65 Quell-Werkzeugen wurden 11 namentlich genannte DTP-Tools
  entfernt (siehe `__tests__/toolKatalog.test.ts`). Weitere Layout-Werkzeuge sind bewusst noch enthalten;
  ihre Sichtbarkeit wird nicht durch Löschen, sondern über `toolPresentation.ts` (Zone `versteckt`)
  geregelt."* — **`__tests__/toolKatalog.test.ts` bleibt unangetastet** (kein Beifang, der Test ist die
  Belegstelle für die 65→54-Historie).
- Die Abnahme-Zahlen von A1 ändern sich dadurch **nicht**: 63 Regeln / 7 fix / 2 kontext / 15 weitere /
  39 versteckt (54−15), `verwaisteRegeln()` = [].

## ⇒ YAMA — eine Bitte (blockiert nichts)
`push-result.log` steht noch auf **Fr. 24.07. 23:21**, fork/backup liegen damit auf `9bcc9c3`. Lokal sind
`cf54ffc` (A1-Auftrag) und `2b9d468` (Push-Freigabe) noch **ungesichert**. Aus der Cloud geht kein Push
(403 vom Proxy). → Bitte einmal **Doppelklick auf `push-integration-sicher.command`** (bzw. der native
Generator führt es aus). Nur `fork` + `backup-private`, nie `upstream`, kein `--force`.

## ⇒ GENERATOR (nativ) — COMMIT-FREIGABE (Planner, 2026-07-25 09:49)
Deine A1-Arbeit liegt seit 09:41–09:43 **unversioniert** im Baum (`toolPresentation.ts` 12,1 kB,
`__tests__/toolPresentation.test.ts` 5,9 kB, `faehigkeiten.ts` + `toolCatalog.ts` geändert) und hat sich
seit 6 Minuten nicht bewegt. Vermutete Ursache: die alte Job-Regel „**Commit nur auf Yamas Wort**".
**Diese Regel ist hiermit aufgehoben** — Yamas stehende Anweisung lautet „du commitest du pushst alles ohne
mich". Also: **auf `auto/hausplaner-integration` committen** (eigener Commit, Bericht in diesen Ledger als
`⇒ PLANNER`-Block mit den gemessenen Zahlen), Gates vorher selbst fahren (`tsc:hausplaner` ·
`schema:hausplaner:check` · `test:hausplaner` · `build:hausplaner`), danach `push-integration-sicher.command`.
Grenzen unverändert: kein `main`-Merge (Tor 2), nur `fork`/`backup-private`, nie `upstream`, kein `--force`.
Der Planner fasst deine Dateien **nicht** an (Rollentrennung) — sie warten unangetastet auf dich.

## ⇒ YAMA — Fach-/Design-Freigabe nötig: T1 Token-Konsolidierung (gemessen, blockiert A1 nicht)
Neues Konzept: `docs/auftraege/planner-konzept-token-konsolidierung-hausplanerapp.md` (50 Zeilen).
Messung an `HausplanerApp.tsx`: die Datei **importiert `T` schon** (Z. 16), hält aber eine **zweite
Farbwahrheit** `FARBEN` (Z. 42–47) plus **31 verschiedene Hex-Werte in 79 Vorkommen auf 50 Zeilen** plus
4× `var(--sa-…)`-Fallback. Der rote Kern: **zwei Grüns** — `FARBEN.auswahl #93c21c` ≠ `T.brand #7fae1c`;
zusätzlich wird Grün hier als **Akzent** benutzt, während `T.accent` **Teal #12807d** ist.
**Deine Entscheidung (nur die eine Frage):** Auswahl-/Aktiv-Grün auf **(a) `T.brand #7fae1c`**
(Planner-Empfehlung, kleinste optische Verschiebung), **(b) `T.accent` Teal #12807d** (konsistent mit dem
v9-Studio-Kopf, sichtbar andere Optik) oder **(c) `#93c21c` als neues Token in `T`** (dann drei Grüns)?
Umsetzung erst **nach** A1-Abnahme (sonst Konflikt in derselben Dateiregion). Abnahmezahl danach:
`grep -c "#[0-9a-fA-F]" HausplanerApp.tsx` = **0**.

## ⇒ GENERATOR-BERICHT (nativ) — Wizard-Welle A1 UMGESETZT (2026-07-25 09:5x, Ballbesitz → Evaluator)
Auftrag `docs/auftraege/generator-auftrag-wizard-welle-a1-werkzeug-praesentation.md` vollständig umgesetzt auf
`auto/hausplaner-integration` (Basis `3229866`). Additiv, kein UI-Umbau, kein Modell-/Schema-Eingriff.

**Umgesetzt (4 Punkte, exakt der Auftrag):**
- **A1.1** neu: `app/tools/toolPresentation.ts` — `RailZone`/`ToolHerkunft`/`ToolPresentationRule`,
  `TOOL_PRESENTATION_RULES` (63 Regeln, jede id EINZELN aufgeführt), `praesentation()`, `zoneTools()`,
  `verwaisteRegeln()`; zusätzlich `zoneToolsIn()`/`verwaisteRegelnIn()` (parametrisierte Kerne, damit
  Gegenproben ohne Mutation der echten Daten laufen) und `regelloseWerkzeuge()` (Gegenrichtung der
  Vollständigkeit). Auflösung `toolNach() ?? katalogTool()` = **Registry hat Vorrang**; Sortierung
  `ordnung`, bei Gleichstand Regel-Index (nicht nach id); unbekannte id wird ausgelassen, nie geworfen.
- **A1.2** `faehigkeiten.ts`: lokale `CAD_TEILMENGE` entfällt, die 15 ids kommen aus `zoneTools('weitere')`.
  Import `katalogTool` → `zoneTools`. **Eine** Kuratierungs-Wahrheit statt zweier Stellen.
- **A1.3** Kopfkommentar `toolCatalog.ts` in der **Planner-Präzisierung von 09:46** (nicht in meiner ersten,
  pauschaleren Fassung): 65 → 54 durch 11 namentlich entfernte DTP-Tools (Belegstelle `toolKatalog.test.ts`),
  weitere Layout-Werkzeuge bewusst enthalten, Sichtbarkeit über `toolPresentation.ts` Zone `versteckt`.
  Nur Kommentar, keine Datenänderung. `__tests__/toolKatalog.test.ts` unangetastet.
- **A1.4** neu: `__tests__/toolPresentation.test.ts` — 11 Fälle (alle 6 geforderten Punkte + Registry-Vorrang
  `auswahl` ≠ `selection` + „kein Datenverlust: jede versteckte id bleibt Katalog-Eintrag").

**Zahlen-Beweis (§5, selbst gemessen, Exit-Codes):**
`tsc:hausplaner` **0** · `schema:hausplaner:check` **0** · `test:hausplaner` **0 → 695/695 pass, 0 fail**
(vorher 684, also **+11**) · `build:hausplaner` **0**.
`TOOL_PRESENTATION_RULES.length` = **63** · `zoneTools('fix')` = **7** · `('kontext')` = **2** ·
`('weitere')` = **15** · `('versteckt')` = **39** · `verwaisteRegeln()` = **[]** · `regelloseWerkzeuge()` = **[]**.

**Gegen-Beweise — beide TATSÄCHLICH ROT gesehen** (echte Regeldatei temporär verfälscht, danach per Kopie
zurückgesetzt, `diff` = identisch):
- (a) `wand` → `zone:'versteckt'`: **5 von 11 Tests rot** (Zonen-Aufteilung · Fix-Zone-Reihenfolge ·
  „keine Registry-id versteckt").
- (b) erfundene id `erfunden-xyz` in den Regeln: **3 von 11 Tests rot** (Vollständigkeit ·
  `verwaisteRegeln()` leer · Gegenprobe-Test selbst).

**Regressionsanker gemessen statt behauptet:** die im Test hart hinterlegte Vorher-Liste von
`faehigkeitenNach('werkzeuge')` (19 ids) habe ich nicht abgeleitet, sondern aus
`git show HEAD:…/faehigkeiten.ts` in einer temporären Kopie **ausgeführt** und verglichen — identisch.
Temporärdatei entfernt (`git status` sauber).

**Bundle aus Quellen:** `build` zweimal hintereinander → md5 beide Male `be0f864c0b722573acdda978c1e6cd70`
(deterministisch, keine Drift). Bundle-Größe 1.287,29 kB → **1.292,69 kB** (neues Modul enthalten).

**Offener Punkt, bewusst NICHT eigenmächtig behoben (Auftrag §4.6) — Frage an Yama:** in `faehigkeiten.ts`
fehlt `decke` in `WERKZEUG_GRUPPE` und fällt auf `'werkzeuge'`, während `wand → 'bau'` und
`dach → 'dach-zimmerei'` gehen. Vermutlich gehört `decke → 'bau'`. Das ist eine **Fachzuordnung**, kein
Bau-Posten → unverändert gelassen. (Wirkt sich auf den Regressionsanker aus: `decke` steht heute in der
Gruppe „Werkzeuge" — eine spätere Korrektur ändert diese Liste bewusst.)

**Guardrails eingehalten:** kein Katalog-Eintrag gelöscht · kein Katalog-Werkzeug in die Leiste gehoben
(Fachentscheidung Yama) · `HausplanerApp.tsx` nicht angefasst · `activation.ts`/`toolTypes.ts`/
`toolRegistry.ts`/`toolContext.ts`/`domain`/`geometry`/`renderers`/PHP unberührt · `toolFuerShortcut` liest
weiterhin nur die Registry (keine Katalog-Shortcuts) · kein neuer Deaktivierungs-Mechanismus ·
kein Hash-/Snapshot-/Versions-/Projektions-Neubau · die rohen Hex-Werte in `HausplanerApp.tsx` **nicht**
mitrepariert (eigener Posten T1).

**Ballbesitz → EVALUATOR.** Bitte selbst nachmessen (Gates + die 6 Zahlen + beide Gegen-Beweise
nachstellen) und Votum hier hinterlegen. **Kein `main`-Merge (Tor 2 = Yama).**

---

## ⇒ EVALUATOR — AUFTRAG: Abnahme Wizard-Welle A1 (Planner, 25.07. vormittags)

**Ballbesitz: EVALUATOR.** Der Auftrag steht vollständig in
`docs/auftraege/evaluator-auftrag-wizard-welle-a1-werkzeug-praesentation.md`. Prüfgegenstand ist Commit
`c0ffe31` gegen `docs/auftraege/generator-auftrag-wizard-welle-a1-werkzeug-praesentation.md`.

**Kurzfassung der Pflicht:** Der Generator-Bericht darüber ist sauber und ausführlich — das ist **kein
Grund, ihm zu glauben**. Alle Zahlen selbst erzeugen: vier Gates mit Exit-Codes (behauptet 695/695, vorher
684 — auch die 684 selbst nachfahren oder ausdrücklich als „nicht verifiziert" kennzeichnen), die sieben
Kennzahlen (63/7/2/15/39, `verwaisteRegeln()`=[], `regelloseWerkzeuge()`=[]) direkt am Modul, dazu die vom
Planner ergänzte Querprobe `new Set([...registryIds, ...katalogIds]).size` — geht die 63 wirklich auf oder
rettet eine id-Überschneidung die Summe zufällig?

**Drei Gegen-Beweise**, nicht zwei: (a) `wand`→`versteckt` und (b) erfundene id nachstellen (behauptet 5/11
und 3/11 rot), **(c) neu vom Planner:** eine id aus den Regeln entfernen — wird kein Test rot, deckt die
Suite den Vollständigkeits-Anspruch nicht ab.

**Der eigentliche Risikopunkt** ist A1.2: `faehigkeiten.ts` liest die 15 ids jetzt aus `zoneTools('weitere')`.
Reihenfolge-Vergleich gegen `git show 3229866:…` selbst ausführen — und zwar für **alle** Gruppen, nicht nur
`'werkzeuge'`, über die der Bericht allein spricht.

**Neu aufgenommen, weil es sonst niemand liest:** der Commit enthält `public/hausplaner/hausplaner.js`
(408 Zeilen). Das ist hier etablierte Praxis (`050f55f`, `a1215a3`, `4cde0be`, `176aa48` ebenso), also kein
Regelbruch — aber ungeprüft. Der Evaluator baut frisch und vergleicht gegen den committeten Bundle, prüft
die md5-Nicht-Drift `be0f864c0b722573acdda978c1e6cd70` und sieht nach, ob der Bundle-Delta **mehr** enthält,
als der Quell-Delta erklärt.

**Grenzen:** kein `main`-Merge, kein Deploy (Tor 2 = Yama) · kein Push zu `upstream` (fremdes Konto), Push
nur `fork`/`backup-private` via `push-integration-sicher.command`, nie `--force` · **keine Code-Korrektur
durch den Evaluator** (Befund → Planner) · `decke` in `WERKZEUG_GRUPPE` bleibt Yamas Fachfrage, nur
bestätigen, dass der Generator sie korrekt offengelassen hat · die 31 rohen Hex-Werte in `HausplanerApp.tsx`
sind bewusst ausgeklammert (Posten T1) und **kein** A1-Befund.

**Votum** als Block `## ⇒ EVALUATOR-VOTUM — Wizard-Welle A1` hier hinterlegen, Grün/Rot in der ersten Zeile
ohne Weichmacher, plus die 9 Fix-/Kontext-ids im Klartext, damit Yama sie fachlich sehen kann.
**Rot blockiert A2.**

---

## ⇒ YAMA — zwei offene Entscheidungen + eine Bitte (Stand 25.07., Planner)

1. **T1 Grün-Entscheidung** (Konzept: `docs/auftraege/planner-konzept-token-konsolidierung-hausplanerapp.md`):
   `HausplanerApp.tsx` führt eine zweite Farbwahrheit (`FARBEN`) plus 31 verschiedene Hex-Werte in 79
   Vorkommen. Das Akzent-Grün dort ist `#93c21c`, das Marken-Token ist `T.brand #7fae1c`. Deine Wahl:
   (a) auf `T.brand #7fae1c` ziehen [meine Empfehlung] · (b) auf `T.accent` Teal `#12807d` ·
   (c) neues Token für `#93c21c`. Erst danach wird T1 ein Generator-Auftrag.
2. **`decke` in `WERKZEUG_GRUPPE`** (`faehigkeiten.ts`): fehlt und fällt auf `'werkzeuge'`, während
   `wand → 'bau'` und `dach → 'dach-zimmerei'`. Gehört `decke` fachlich zu `'bau'`?
3. **Bitte:** `push-integration-sicher.command` doppelklicken. `push-result.log` steht noch auf
   Fr. 24.07. 23:21, der fork auf `9bcc9c3`; lokal ungesichert sind `cf54ffc`, `2b9d468`, `9e11457`,
   `3229866`, `c0ffe31`. Aus der Cloud geht Push nicht (403 am Proxy).
