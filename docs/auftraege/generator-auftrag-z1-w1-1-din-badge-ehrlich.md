# Z1-W1-1 · Das DIN-18065-Badge sagt, was es nicht geprüft hat

```yaml
zustand: ENTWURF              # Fassung 2 — revidiert 21.08. nach DoR-Verweigerung §143 + Plan-Prüfer-Bericht
basis_sha: 11f7c4c3           # Zeilenzeiger unten am HEAD 87dbbe77 NEU gemessen (tote Zeiger, §174)
herkunft: Befund K-4 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.1
vorbestand: Yamas eigener Verdacht, A-15-fachaussage-oder-hinweis.md:315-324 (PERSONENSCHADEN :298)
gebaut: 2bc0d2f2 (21.08. 13:56) — VOR BEREIT gebaut; Abweichung, regularisiert durch diese Revision + Neu-DoR
baut: generator (Agent frontend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: statiker (Meldung, keine Abnahme)
status_steht_in: docs/STATUS.md (Zeile 96, Datensatz vom Integrator)
```

## Ziel
Das Badge im Eigenschaften-Panel behauptet keine Vollprüfung mehr: solange `durchgangshoehe`
nicht übergeben wird, benennt ein Hinweis die Lücke — und verschwindet von selbst, sobald die
Prüfung im Ergebnis auftaucht.

## Ist-Beleg (am HEAD `87dbbe77`, 21.08.)
- Engine: `geometry/treppenBerechnung.ts:58` `DURCHGANG_MIN = 2000`; `:97` `if (e.durchgangshoehe !== undefined)`.
- Badge: `app/rahmen/EigenschaftenPanel.tsx:499` (`'DIN 18065 erfüllt' : 'DIN 18065 verletzt'`).
- **Aufrufer von `berechneTreppe` ohne das Feld — über den Funktionsnamen gemessen (§143), fünf,
  nicht zwei:** `EigenschaftenPanel.tsx:494` · `geometry/treppe2D.ts:54` · `geometry/treppe3D.ts:44`
  (→ `renderers/three-d/szene.ts:406-412` ruft `treppe3DKoerper`) · `geometry/treppenTypen.ts:48`
  (kein Ladeweg, 33er-Liste). **Gegenstand dieses Auftrags ist allein das Panel-Badge.**

## Scope · Dateien
- `app/rahmen/EigenschaftenPanel.tsx`: bedingter Hinweis unter dem Badge (Klasse `hp-ep-lesehinweis`),
  Bedingung `!erg.pruefungen.some(pr => pr.id === 'durchgangshoehe')`.
- **Test-Naht (Restpunkt 1 aus §143, ausdrücklich erlaubt):** zweischichtig — der TEXT als
  Quelltext-Zusage in `__tests__/eigenschaftenPanel.test.ts`, die BEDINGUNG an der echten Rechnung
  in `__tests__/treppenBerechnung.test.ts` (Engine **unverändert**; Testen ist keine Änderung).
**Nicht-Ziele:** `geometry/treppenBerechnung.ts` bleibt unverändert (0 geänderte Zeilen);
`renderers/three-d/szene.ts`, `treppe2D.ts`, `treppe3D.ts`, `treppenTypen.ts` werden NICHT
angefasst; keine Herleitung der Kopfhöhe (Posten 2.1, Gate Y-4); kein neues Eingabefeld;
keine neue CSS-Klasse/Regel (Inline-Zeilen-Budget AUF-38-P1).

## Kanten
Der Hinweis benennt GENAU das eine offene Kriterium und stellt die übrigen nicht in Frage
(Muster A-14/A-18). Er hängt an der fehlenden Prüfung im Ergebnis, nicht an einer festen Anzeige.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Aufruf OHNE `durchgangshoehe` → Hinweis vorhanden, Text als Quelltext-Zusage geprüft | Hinweis | `2bc0d2f2` | `__tests__/eigenschaftenPanel.test.ts` |
| B (**Schutz**, gegen die Engine direkt gemessen): ohne Übergabe keine `durchgangshoehe`-Prüfung im Ergebnis; mit 1900 mm ist sie drin, verletzt, kippt `bestanden` | Schutz | `2bc0d2f2` | `__tests__/treppenBerechnung.test.ts` |
| C: Browserabnahme — Screenshot einer platzierten Treppe mit Hinweis | Abnahme | — | **offen, Evaluator** |
| D: `git diff 2bc0d2f2^ 2bc0d2f2 --numstat -- geometry/treppenBerechnung.ts` → 0 geänderte Zeilen | Schutz | `2bc0d2f2` | Rohausgabe im Baubericht (Kriterium D belegt) |

**P1-Kriterium A war vor dem Bau wirksam rot** (Badge-Zeile trug keinen Hinweis — §143 bestätigt).
Rot-Probe laut Baubericht gefahren (Hinweis ausgehängt → Zusage fällt; zurück → 1773 grün).

## Rückweg
Ein Commit, Text + Tests, zurückdrehbar; kein Schema, keine Daten.

## Abweichung, offen benannt
Der Bau `2bc0d2f2` entstand in derselben Minute, in der die Statuswahrheit „NICHT ERTEILT" trug
(§173). §173 stellt fest, dass der Bau zwei der drei Restpunkte **widerlegt** (Test-Naht
gefunden; Engine getestet ohne Änderung); Restpunkt 3 (fünf Aufrufer statt zwei) ist in dieser
Fassung eingearbeitet. **Regularisierung:** Neu-DoR des Plan-Prüfers gegen diese Fassung →
`BEREIT` → Generator meldet `CODE_FERTIG` (Bau steht) → Evaluator Kriterium C. Der Prozessbruch
„Bau vor BEREIT" wird als Rote Karte des Generators geführt, nicht als Grund, gute Arbeit zu
verwerfen.
