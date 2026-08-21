# Z1-W1-1 · Das DIN-18065-Badge sagt, was es nicht geprüft hat

```yaml
zustand: ENTWURF
basis_sha: 11f7c4c3
herkunft: Befund K-4 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.1
vorbestand: Yamas eigener Verdacht, A-15-fachaussage-oder-hinweis.md:315-324 (PERSONENSCHADEN-Einstufung :298)
baut: generator (Agent frontend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: statiker (Meldung, keine Abnahme)
status_steht_in: docs/STATUS.md — Tafelzeile+Datensatz braucht den Integrator-Lauf (alleiniger Schreiber)
```

## Ziel
Das Badge im Eigenschaften-Panel behauptet keine Vollprüfung mehr: solange `durchgangshoehe`
nicht übergeben wird, benennt die Anzeige die Lücke.

## Ist-Beleg
`geometry/treppenBerechnung.ts:97-99` prüft Kopfhöhe nur `if (e.durchgangshoehe !== undefined)`
(`DURCHGANG_MIN=2000` :58); reale Aufrufe ohne das Feld: `app/rahmen/EigenschaftenPanel.tsx:494`,
`renderers/three-d/szene.ts:406-412`; Badge-Text „DIN 18065 erfüllt" `EigenschaftenPanel.tsx:499`.

## Scope · Dateien
- `app/rahmen/EigenschaftenPanel.tsx` (Badge-Text) + zugehöriger Test.
**Nicht-Ziele:** `treppenBerechnung.ts` bleibt unverändert (Rechnung wird NICHT angefasst);
keine Herleitung der Kopfhöhe (das ist Posten 2.1, Gate Y-4); kein neues Eingabefeld.

## Kanten
Text darf nicht suggerieren, die übrigen Kriterien seien auch unvollständig; Muster A-14/A-18
(Vorbehalt reist mit dem Ergebnis).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Aufruf OHNE `durchgangshoehe` → Anzeige benennt die fehlende Prüfung, im Test am **Text** festgehalten | Badge-Text | *nach Umsetzung* | *nach Umsetzung* |
| B: Aufruf MIT `durchgangshoehe` < 2000 → weiterhin „verletzt" | Badge-Text | *nach Umsetzung* | *nach Umsetzung* |
| C: Browserabnahme — Screenshot platzierte Treppe mit neuem Text | Abnahme | — | Screenshot-Pfad |
| D: `git diff` zeigt 0 geänderte Zeilen in `treppenBerechnung.ts` | Schutz | *nach Umsetzung* | diff-Rohausgabe |

**P1-Kriterium A ist vor dem Bau wirksam rot:** heutiger Badge-Text enthält keinen Lückenhinweis
(`EigenschaftenPanel.tsx:499`).

## Rückweg
Ein Commit, reine Text-/Teständerung, zurückdrehbar; kein Schema, keine Daten.
