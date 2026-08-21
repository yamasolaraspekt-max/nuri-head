# Z1-W1-4 · dachWerte: eine Quelle, Stilllegung statt Löschung

```yaml
zustand: ENTWURF
basis_sha: 11f7c4c3
herkunft: Befund R-2 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.4
entscheidung: Y-2 ENTSCHIEDEN 21.08. — NICHT löschen; Stilllegungsvermerk (Rückfall-Regel)
baut: generator (Agent frontend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
Nur noch `geometry/dachWerte.ts` ist in Benutzung; die byte-identische Kopie
`resources/planner/utils/dachWerte.ts` bleibt liegen, trägt aber einen Stilllegungsvermerk.

## Ist-Beleg
`diff` beider Dateien leer (104 Z., 4188 B); Verbraucher: `dachGeometrie.ts:13` →
`../../utils/dachWerte`, `dachformVorlagen.ts:34` → `./dachWerte`. Der einzige Test
(`__tests__/dachWerte.test.ts:12`) deckt nur die geometry-Kopie — eine Änderung an der
utils-Kopie fiele heute nirgends auf. Entstehung `00bfed2b` (18.07.) vs. `588283df` (23.07.,
W-1 „reine Reuse", Zielort ausdrücklich nur `geometry/`).

## Scope · Dateien
- `geometry/dachGeometrie.ts:13` (Import auf `./dachWerte`)
- `resources/planner/utils/dachWerte.ts` (NUR Kopfvermerk: stillgelegt, Datum, Grund, Verweis)
**Nicht-Ziele:** keine Löschung (Y-2); keine inhaltliche Änderung an Werten oder Funktionen;
`resources/planner/utils/` wird nicht umgebaut.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: `grep -rn "utils/dachWerte" resources/` → 0 produktive Treffer | Import | *n.U.* | grep-Rohausgabe |
| B: utils-Kopie existiert weiter, Kopf trägt Stilllegungsvermerk | Vermerk | *n.U.* | Zitat |
| C: md5 der Datei VOR der Kopfergänzung im Bericht (Beleg für spätere Y-Löschentscheidung) | Beleg | *n.U.* | md5-Rohausgabe |
| D: Suite grün, `tsc:hausplaner` exit 0 | Schutz | *n.U.* | Zähler |

**P1-Kriterium A ist vor dem Bau wirksam rot** (heute 1 produktiver Treffer: `dachGeometrie.ts:13`).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema, keine Daten, keine Löschung.
