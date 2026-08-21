# Z1-W1-5 · insulationType: der tote Zweig sagt, dass er tot ist

```yaml
zustand: ENTWURF
basis_sha: 11f7c4c3
herkunft: Befund K-1 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.5
entscheidung_offen: Y-3 (Dämmungs-Regler + Fachbedeutung) — NICHT Teil dieses Auftrags
baut: generator (Agent frontend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
`projection/raumProjektion.ts:91` weist ehrlich aus, dass `insulationType` heute von nichts
gesetzt wird — nach dem Muster „ehrlich null" (`:95-96` ebd.). **Verhalten unverändert.**

## Ist-Beleg
`grep -rn insulationType` → genau 3 Treffer (Typ `scene.types.ts:109`, Zod `validation.ts:46`,
Lesestelle `raumProjektion.ts:91`), keine Schreibstelle im ganzen Baum; einziger
construction-Regler `EigenschaftenPanel.tsx:324` schreibt nur `materialId`. Folge: der Ternary
liefert für jede Wand konstant `'wand'`, der Zweig `aussenwand_gedaemmt` ist tot.

## Scope · Dateien
- `projection/raumProjektion.ts` (Kommentar/ehrlicher Ausweis an :91, Verweis auf Messung + Y-3)
- ggf. Charakterisierungstest (Projektion vorher = nachher).
**Nicht-Ziele:** KEIN neuer Regler, kein neues Feld (Y-3, fehlender Operand: was „gedämmt"
fachlich für den Bauteiltyp jenseits der PHP-Grenze bedeutet, entscheidet Yama); Typ und
Zod-Schema bleiben (das Feld selbst ist nicht falsch, nur unverdrahtet).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Die Stelle :91 benennt am Code, dass das Feld heute nirgends gesetzt wird (Messung + Y-3-Verweis) | Ausweis | *n.U.* | Zitat |
| B: Projektion liefert vorher und nachher identische Werte | Schutz | *n.U.* | Charakterisierungstest |
| C: Kein neues Feld, kein Regler im Diff | Grenze | *n.U.* | diff-Rohausgabe |

**P1-Kriterium A ist vor dem Bau wirksam rot** (heute kein Hinweis an der Stelle).

## Rückweg
Ein Commit, Kommentar + Test, zurückdrehbar.
