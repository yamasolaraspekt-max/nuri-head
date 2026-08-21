# Z1-W1-2 · Walmdach: ungültige Kontur wird abgelehnt statt still falsch gerechnet

```yaml
zustand: ENTWURF
basis_sha: 11f7c4c3
herkunft: Befund P-1 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.2
entscheidung: Y-1 ENTSCHIEDEN 21.08. — ABLEHNEN mit Meldung (keine stille Dreh-Korrektur)
baut: generator (Agent frontend-entwickler; reine TS-Insel)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: dachdeckermeister (Meldung, keine Abnahme)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
kopplung: gleiche Datei wie Z1-W1-3 — Reihenfolge W1-2 VOR W1-3, getrennte Commits
```

## Ziel
`dachFlaechen()` liefert für `roofType==='walm'` mit `spannM > laengeM` **keine** stumme
Falschfläche mehr, sondern wirft `DachGeometrieUngueltig` mit erkennbarem Grund.

## Ist-Beleg
`geometry/dachGeometrie.ts:134-146`: `firstLenM = Math.max(0, laengeM - spannM)` klemmt still
auf 0; Gegenrechnung 6×8 m/30° → 64,66 statt 55,43 m² (+16,6 %), 4×10 m → 80,83 statt 46,19 m²
(+75 %). Live: `HausplanerApp.tsx:1024`, `dachProjektion.ts:29`; UI-erreichbar
(`EigenschaftenPanel.tsx:249` `<option value="walm">`). Vorhandene, getestete Prüfung:
`dachformVorlagen.ts:414-416` `walmIstKonsistent`.

## Scope · Dateien
- `geometry/dachGeometrie.ts` (Prüfung einziehen, Muster `:88-93`)
- `__tests__/dachGeometrie.test.ts` (beide gerechneten Fälle)
**Auflage (aus der Synthese):** `walmIstKonsistent` **benutzen** (Import), keine dritte Fassung
derselben Regel bauen.
**Nicht-Ziele:** keine Auto-Rotation der Firstachse (Y-1 hat sie abgelehnt); `polygonM2` bleibt
in diesem Auftrag unangetastet (W1-3); kein UI-Umbau — die Meldung reist über den vorhandenen
Fehlerweg der Ausnahme.

## Kanten
Sattel/Pult/Flach dürfen sich nicht ändern; der gutmütige Walm-Fall (L > B) bleibt exakt gleich;
Teilwalm/Kantentypen (STATUS-Treffer `prevIsTraufe`) sind NICHT Gegenstand.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Fall 6×8 m/30° wirft `DachGeometrieUngueltig` | Sperre | *n.U.* | Testname |
| B: Fall 4×10 m/30° wirft `DachGeometrieUngueltig` | Sperre | *n.U.* | Testname |
| C: Bestandstest `:61-68` (8×12 m) bleibt grün und **unverändert** | Schutz | *n.U.* | diff + Suite |
| D: `walmIstKonsistent` ist die einzige Fassung der Regel (grep-Beleg: kein neuer Prüfcode gleichen Inhalts) | Reuse | *n.U.* | grep-Rohausgabe |
| E: Browserabnahme — Walmdach mit B > L erzeugt sichtbare Absage, kein stilles Ergebnis | Abnahme | — | Screenshot |

**P1-Kriterien A/B sind vor dem Bau wirksam rot** (heutige Rückgabe: 64,66 bzw. 80,83 m², kein Wurf).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema, keine Daten. Entdeckung einer Fehlsperre: legitime
Walmdächer (L > B) würden abgewiesen — Kriterium C fängt das im Test.
