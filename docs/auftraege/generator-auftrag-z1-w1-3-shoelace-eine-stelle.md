# Z1-W1-3 · Eine Formel, eine Stelle: polygonM2-Kopie zusammenführen

```yaml
zustand: ENTWURF              # Fassung 2 — revidiert 21.08. nach DoR-Verweigerung §145 + Plan-Prüfer-Bericht
basis_sha: 11f7c4c3           # Zeiger am HEAD 87dbbe77 NEU gemessen
herkunft: Befund R-1 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.3
gebaut: d7651d9c (21.08. 13:40) — VOR BEREIT gebaut; regularisiert durch diese Revision + Neu-DoR
baut: generator (Agent frontend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md (Zeile 98)
kopplung: gleiche Datei wie Z1-W1-2 (60c04eef) — W1-2 lag vor, Hunks überlappen nicht (§144/Prüfbericht)
```

## Ziel
`geometry/dachGeometrie.ts` benutzt `polygonFlaecheM2` aus `polygonFlaeche.ts`; die private
Kopie `polygonM2` ist entfallen. Der Satz in `kontur.ts` wird **berichtigt** (nicht „wahr
gemacht" — er handelt von `signierteFlaeche`/`roomDetection.ts`, und davon gibt es drei Fassungen,
die dieser Auftrag ausdrücklich NICHT anfasst).

## Ist-Beleg (HEAD `87dbbe77`)
- Vorher (Basis): `dachGeometrie.ts:39-48` private `polygonM2` (mm-Eingang, `/1_000_000`, KEIN
  `Number.isFinite`-Schutz); `polygonFlaeche.ts:11-13` Meter-Vertrag, `:29` „Niemals NaN".
- Nachher (gebaut): `dachGeometrie.ts:18` `import { polygonFlaecheM2 }`; `:89` Aufruf mit
  **erkennbarer mm→m-Umrechnung am Aufrufort** (`poly.map(p => ({x: p.x/1000, y: p.y/1000}))`) —
  der Meter-Vertrag der aufgerufenen Funktion bleibt unangetastet; `:82` `bboxM2` in Metern daneben.
- `kontur.ts:22-23` berichtigt: „statt eine achte Fassung der Formel zu schreiben".
- **Schuhband-Fassungen in `geometry/`, gemessen 21.08. (§145 + Prüfbericht):** `polygonFlaeche.ts:44`
  (Zielmodul) · `roomDetection.ts:70` · `dachAusschnitt.ts:103` · `grundriss.ts:94` ·
  `dachTopologie.ts:109` — **alle außer dem Zielmodul sind Nicht-Ziel.** Die Zeichenfolge
  `a.x * b.y` trifft zusätzlich 3D-Kreuzprodukte (`aufbauOrientierung.ts:32`, `gaubeGeometrie.ts:59`)
  — deshalb misst Kriterium A den **Bezeichner**, nicht das Formelmuster.

## Scope · Dateien
`geometry/dachGeometrie.ts` · `geometry/kontur.ts` (Satz berichtigen) · `__tests__/dachGeometrie.test.ts`.
**Nicht-Ziele:** keine Änderung an `polygonFlaeche.ts` (Vertrag bleibt Meter); keine der vier
anderen Fassungen anfassen; kein Verhaltensumbau von `pruefeRechteckigeKontur` über den
Formeltausch hinaus.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: `grep -c "polygonM2" geometry/dachGeometrie.ts` → 0 UND `grep -c "from './polygonFlaeche'" geometry/dachGeometrie.ts` → 1 | Zusammenführung | `d7651d9c` | grep-Rohausgaben (Evaluator) |
| B: NaN-Verhalten von `pruefeRechteckigeKontur` VORHER (Basis) und NACHHER gemessen und gegenübergestellt — Fallrichtung wird nicht angenommen | Messpflicht | `d7651d9c` | Rohausgaben im Baubericht/Abnahme |
| C: Umrechnung mm→m erkennbar am Aufrufort (`:89`), `polygonFlaeche.ts` unverändert (`git diff --numstat` = 0) | Vertrag | `d7651d9c` | diff-Rohausgabe |
| D: `kontur.ts:22-23` nennt die tatsächliche Lage (mehrere Fassungen), behauptet keine „genau eine Stelle" mehr | Doku | `d7651d9c` | Zitat |
| E: Suite grün, `tsc:hausplaner` exit 0 | Schutz | `d7651d9c` | Zähler |

**P1-Kriterium A war vor dem Bau wirksam rot** (`polygonM2` in `:39-48` vorhanden).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema, keine Daten.

## Abweichung, offen benannt
Bau vor BEREIT (DoR §145 NICHT ERTEILT wegen des unerfüllbaren Kriteriums A und der
D-Lesart). Beide Punkte sind in dieser Fassung behoben; der Bau selbst hat sie faktisch schon so
gelöst (Bezeichner weg, Satz berichtigt, Umrechnung am Aufrufort). Regularisierung: Neu-DoR →
`BEREIT` → `CODE_FERTIG` → Abnahme (inkl. Kriterium B, das der Evaluator selbst misst).
