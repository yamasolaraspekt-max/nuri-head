# VOTUM Z1-W1-3 — „Eine Formel, eine Stelle: polygonM2-Kopie zusammenführen"

**evaluator · 21.08. · Bau `d7651d9c` · Basis `11f7c4c3` · Prüfstand `52c861a3`**

## Ergebnis: ABGENOMMEN — fünf von fünf Kriterien, je selbst gemessen

## Messtisch — jede Zeile, nichts zitiert

| # | Ergebnis | Beleg (selbst gefahren) |
|---|---|---|
| A | **erfüllt** | beide Befehle wörtlich: `grep -c "polygonM2" dachGeometrie.ts` → **0** · `grep -c "from './polygonFlaeche'"` → **1** |
| B | **erfüllt** | NaN-Verhalten vorher/nachher gegenübergestellt, drei Fälle, **identisch** (s. u.) |
| C | **erfüllt** | Umrechnung sichtbar am Aufrufort: `polygonFlaecheM2(poly.map(p => ({x: p.x/1000, y: p.y/1000})))`; `git diff --numstat -- polygonFlaeche.ts` → **0 Zeilen** |
| D | **erfüllt** | `kontur.ts` nennt die tatsächliche Lage und **widerruft die alte Behauptung wörtlich**: *„hier stand ‚die Flächenformel steht damit weiterhin an genau einer Stelle'. Das trifft nicht zu und traf nie zu."* |
| E | **erfüllt** | `tsc:hausplaner` exit **0**, `error TS` **0** · Suite **1777 / 1777**, fail **0** |

**Kriterium B, beide Stände selbst gefahren** — Basis in eigenem Worktree (`d7651d9c^` = `7e28d051`):

| Fall | VORHER | NACHHER |
|---|---|---|
| NaN in x | DURCH | DURCH |
| NaN in y | DURCH | DURCH |
| sauberes Rechteck | DURCH | DURCH |

Das Kriterium verlangt ausdrücklich nur Messung und Gegenüberstellung — *„Fallrichtung wird nicht
angenommen"*. Ergebnis: **der Bau ändert das NaN-Verhalten nicht.** Keine Regression, keine stille
Verschärfung.

**Scope:** `d7651d9c` berührt **3 Dateien** — `dachGeometrie.ts`, `kontur.ts`, `dachGeometrie.test.ts`.
Verbotene Pfade: `app/` **0**, `docs/STATUS.md` **0**, `polygonFlaeche.ts` **0** (Nicht-Ziel des Blattes).

## Eine Anmerkung, die den Bau entlastet statt belastet

Das Blatt trägt bei Kriterium A selbst **„NICHT erfüllt — 6 Fassungen bleiben, 3 erreichbar"** ein.
**Nach dem Wortlaut des Kriteriums ist A erfüllt**: Es nennt genau zwei Befehle, beide auf
`dachGeometrie.ts`, und beide liefern den geforderten Wert. Die „6 Fassungen" sind eine
Repo-weite Aussage, die das Kriterium nicht verlangt.

Meine Gegenprobe bestätigt die Zahl der Sache nach — sechs Dateien tragen eine eigene
Flächen-Schleife (`dachAusschnitt` · `dachTopologie` · `grundriss` · `polygonFlaeche` ·
`roomDetection` · `kontur`). **Der Generator hat sich also strenger bewertet, als das Kriterium
verlangt, und die Abweichung offen benannt statt sie zu verschweigen.** Das ist kein Mangel;
es ist der Grund, warum ich A ohne Vorbehalt grün werte.

**Für den Planner, nicht für diesen Bau:** Wenn „eine Formel, eine Stelle" repo-weit gelten soll,
braucht es ein Kriterium, das das misst — das jetzige misst eine Datei. Das ist eine
Spezifikationsfrage, kein Nachbesserungspunkt.

## Weitergabe

**ABGENOMMEN** → Release-Prüfer. Den Zustand setze ich nicht: `docs/STATUS.md` ist mir nach A-37-6
gesperrt, der Nachtrag gehört dem **Integrator**.
