# AGENTEN UND SKILLS — wer wofür, und wie man sie gezielt einsetzt

> Teil des [Regelwerks](REGISTER.md) · Landkarte: [`docs/REGISTER.md`](../REGISTER.md)
> Inventur-Läufe und Modell-Stufen: [`INVENTUR-VERFAHREN.md`](INVENTUR-VERFAHREN.md)
> **Stand:** 20.08.2026, frisch gemessen (zweite Ausbaustufe: + 6 Finder + Synthese).

---

## Der Unterschied, der ständig verwechselt wird

| | **Skill** | **Agent** |
|---|---|---|
| Was es ist | Anweisungstext | eigener Unterprozess |
| Kontext | wird in den **laufenden** Kontext geladen | hat einen **eigenen** |
| Parallel? | nein, seriell | ja, mehrere gleichzeitig |
| Liegt in | `.claude/skills/<name>/SKILL.md` | `.claude/agents/<name>.md` |
| Rückgabe | wirkt sofort im Gespräch | meldet ein Ergebnis zurück |

**Der Ort ist technisch zwingend.** Ein „Agenten-Ordner" an einer anderen Stelle wird nicht gelesen
— er wäre eine Sammlung wirkungsloser Textdateien. Deshalb ist `.claude/agents/` das Fach 1, nicht
etwas unter `docs/`.

---

## Die 22 Agenten

Jeder Agent trägt seine Modell-Stufe als `model:` im Frontmatter — Zuordnungslogik und Regeln in
[`INVENTUR-VERFAHREN.md`](INVENTUR-VERFAHREN.md), Abschnitt Modell-Zuordnung.

### Inventur-Finder — read-only, je genau eine Linse, laufen parallel und gegenseitig blind

| Agent | Linse | sucht ausschließlich | Modell |
|---|---|---|---|
| `fehler-finder` | Inhalt | falsche Werte, defekte Logik, tote Versprechen, lügende Köpfe | sonnet |
| `redundanz-finder` | Effizienz | zweite Wahrheiten, Zweitumsetzungen ohne Gegenprobe, tote Module | sonnet |
| `konsistenz-finder` | Konsistenz | Einheiten-Drift (mm!), Namens-/Statuswort-Streuung, Doku↔Code | sonnet |
| `kausalitaets-finder` | Kausalität | unterbrochene Wirkketten: gesetzt-aber-nie-gelesen, Anzeige≠Wirkung | sonnet |
| `plausibilitaets-finder` | Plausibilität | Werte gegen die Realität, mit eigener Gegenrechnung | sonnet |
| `routing-finder` | Workflow | Routen ohne Rechte, tote/doppelte Routen, abreißende Bedienketten | sonnet |
| `inventur-schreiber` | Synthese | Dedupe, Backlog-Abgleich, Gewichtung, Fahrplan-ENTWURF | **opus** |

### Prüfen — read-only, unabhängig, nehmen nichts ab

| Agent | Wofür einsetzen | Werkzeuge | Modell |
|---|---|---|---|
| `dachdeckermeister` | Dach-Geometrie: Neigung, Traufe/First/Ortgang, Kehle/Grat, Überstand, Eindeckung | Glob, Grep, Read | sonnet |
| `zimmermannmeister` | Dachstuhl/Holzbau: Sparren, Pfetten, Kehl-/Gratsparren, Firstlinie, Anschlüsse | Glob, Grep, Read | sonnet |
| `maurer` | Wände: Dicke, Ecken/Gehrung, Öffnungen, Anschluss an Decke/Dach | Glob, Grep, Read | sonnet |
| `statiker` | Tragwerk-Geometrie und Lastweg — **bemisst nicht**, das ist Fach-Freigabe | Glob, Grep, Read | sonnet |
| `ux-designer` | Layout, Dichte, Token- und Komponenten-Disziplin, Statusfarben, A11y | Glob, Grep, Read | sonnet |
| `software-architekt` | Schichten, eine Wahrheit, additive Erweiterung, Insel-Grenze, Reuse-vor-Neu | + Bash (lesend) | **opus** |
| `qualitaets-pruefer` | Bestandsaufnahme/Audit nach den sechs Linsen | + Bash (lesend) | sonnet |
| `repo-inventur` | Inventar als Grundlage der Reuse-Matrix (Pfad · Zweck · Verbraucher · Tests) | + Bash (lesend) | sonnet |
| `planner-architect` | Einordnung eines Planner-Slices in die Zielarchitektur | Glob, Grep, Read | sonnet |
| `ticket-reuse-reviewer` | Wurde vorhandener Ticket-Code gesucht und wiederverwendet? | Glob, Grep, Read | sonnet |
| `security-reviewer` | Rechte/Org/Projektbindung, Fremd-Org, Uploads, additive DB | + Bash (lesend) | **opus** |
| `test-reviewer` | Testabdeckung, Regressionsschutz, Charakterisierung vor Extraktion | + Bash (lesend) | sonnet |

### Bauen — Generator-Rolle, mit Schreibrechten

| Agent | Wofür einsetzen | Werkzeuge | Modell |
|---|---|---|---|
| `backend-entwickler` | Laravel/MySQL, PHP-Validierung der Szene | + Edit, Write, Bash | erbt Sitzungsmodell |
| `frontend-entwickler` | Blade/jQuery/Vuexy im CRM, React/three.js/Konva in der Insel | + Edit, Write, Bash | erbt Sitzungsmodell |
| `fullstack-entwickler` | nur bei einer **durchgehenden** Kante von DB bis Bedienelement | + Edit, Write, Bash | erbt Sitzungsmodell |

> **Kein bauender Agent nimmt seine eigene Arbeit ab.** Er meldet „umgesetzt" — nie „grün",
> „fertig" oder „abgenommen". Die Abnahme macht ein Prüf-Agent oder der Evaluator.

---

## Die 19 Skills

**Im Repo** (`.claude/skills/`, 16): `bauplaner-3d` (Leit-Skill Hausplaner) · `backend-entwickler` ·
`frontend-entwickler` · `software-architekt` · `building-document` · `laravel-planner-integration` ·
`planner-architecture` · `planner-repository-audit` · `planner-security-review` ·
`planner-slice-orchestrator` · `planner-verification` · `ticket-code-reuse` · `dachdeckermeister` ·
`zimmermannmeister` · `maurer` · `statiker`

**Nutzerweit** (`~/.claude/skills/`, 3): `governance-zyklus` · `qualitaetsraster` · `ux-design`

Nicht jeder Skill braucht einen Agenten. `bauplaner-3d`, `building-document`,
`laravel-planner-integration`, `planner-slice-orchestrator`, `planner-verification` und
`governance-zyklus` sind **Rahmen**, die ein Agent *lädt* — sie sind keine eigene Rolle.

---

## Einsatz nach Aufgabe

| Aufgabe | Agenten, parallel |
|---|---|
| **Fehler-/Schwächen-Inventur je Zone** | bis zu 6 Finder (fähigkeitsbasiert besetzt) → `inventur-schreiber` — Ablauf in [`INVENTUR-VERFAHREN.md`](INVENTUR-VERFAHREN.md) |
| **Fortschritts-Inventur** | `repo-inventur` + `qualitaets-pruefer` (Behauptung↔Bestand) |
| **Dach-Slice** | `dachdeckermeister` + `zimmermannmeister` + `statiker` |
| **Wand-/Geschoss-Slice** | `maurer` + `statiker` + `software-architekt` |
| **Oberfläche** | `ux-designer` + `frontend-entwickler` (prüfend gelesen) |
| **Vor jedem Bau** | `ticket-reuse-reviewer` + `repo-inventur` — **erst suchen, dann bauen** |
| **Vor jeder Abnahme** | `security-reviewer` + `test-reviewer` + der Fach-Prüfer des Slices |
| **Umsetzung** | genau **einer** der drei bauenden — nie zwei am selben Pfad |

**Die eine Regel über allem:** wer gebaut hat, prüft nicht. Setze für Bau und Abnahme
**verschiedene** Agenten ein, sonst ist die Prüfung wertlos — sie misst gegen genau die Erwartung,
die der Bauende eingebaut hat.

---

## Wo dieselben Dateien noch liegen

Jeder Worktree trägt eine **eigene Kopie** von `.claude/skills/` und `.claude/agents/`:
`ticket`, `ticket-main`, `ticket-a01`, `ticket-release-pruefung`, `ticket-rolle-generator`,
`ticket-rolle-evaluator`, `ticket-rolle-planner`, `ticket-rolle-plan-pruefer`, `ticket-rolle-release`.

**Folge:** Wer in einem anderen Worktree arbeitet, sieht die neuen Agenten **nicht**, bis der Stand
dort ankommt. Das ist der wahrscheinlichste Grund für den Eindruck, Agenten seien „weg" — sie sind
im Nachbarbaum, nicht gelöscht. Die Git-Historie zeigt **null** gelöschte Skill- oder
Agenten-Dateien.
