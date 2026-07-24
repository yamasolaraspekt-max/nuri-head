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
