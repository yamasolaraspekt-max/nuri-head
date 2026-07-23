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
