# VOTUM Z1-W2-0 — Bedienbarkeits-Probe

**evaluator · 22.08.2026 · Auftrag gen 15, Teil B · Basis `78e5fb3e` · Bau `ee6ce517` · Endstand `0d97a57c` · Blatt `adf03d32`**

## Ergebnis: ABGENOMMEN — 8 von 8 Kriterien

Zielreifegrad entfällt hier ausdrücklich („es wird nicht bedient, es misst"). **Kein Browser
nötig** — das habe ich am Blatt nachgeprüft, statt es aus der Generatormeldung zu übernehmen.

| # | Beleg |
|---|---|
| **a** | `TOOL_DEFINITIONS` **19×** in der Datei, `import … from '../app/tools/toolRegistry'`; hartkodiertes ID-Array: **0** |
| **b** | Test „je Werkzeug — aktivieren, aktiv, Wirkung zugesagt, Escape stellt zurück" grün |
| **c** | Weg **aus dem Datenfeld**: `const weg = t.shortcut ? 'taste' : …`; Test für die kürzellosen Einträge grün |
| **d** | Test „ALLE Einträge sind erfasst — grün oder je begründet ausgenommen" grün |
| **e** | Test „ein **erfundener** Eintrag ohne Bedienweg macht die Probe rot — ausgelöst, nicht behauptet" grün |
| **f** | Test „ein gültiger neuer Eintrag erzeugt automatisch einen Fall": „13 → 14 Fälle, neuer Eintrag über Kürzel 'Q' aktivierbar" |
| **g** | Diff: **nur** das Blatt und die Testdatei; `app/ routes/ database/` leer; Registry **0** geänderte Zeilen |
| **h** | `npm run test:hausplaner:dom` → **36 tests, 36 pass, 0 fail**, Exit 0, Stand `0d97a57c` |

## Zu h — eine Abweichung, die der Bau selbst gemeldet hat

Das Blatt nennt als Ort **„Vitest im Repo-Wurzelverzeichnis"**. Gemessen:

```
vitest als Abhängigkeit  -> FEHLT
vitest.config*           -> keine Datei
Testdatei importiert     -> from 'node:test'
npx vitest run <datei>   -> "No test suite found"
npm run test:hausplaner:dom -> 36 tests, 36 pass, 0 fail
```

**Vitest gibt es in diesem Projekt nicht.** Die Datei folgt dem Hausbrauch (`node:test`), wie die
gesamte Insel-Suite. Der Generator hat das **offen gemeldet** — in der CODE_FERTIG-Meldung und in
der Matrix des Blatts („⚠ Blatt nennt Vitest …") — statt es stillschweigend anders zu machen.

Ich werte (h) als **erfüllt**: verlangt ist ein Befehl mit Ort, den jede Rolle unverändert fahren
kann. Das ist `npm run test:hausplaner:dom`. Die Nennung „Vitest" ist eine Ungenauigkeit **des
Blatts**, kein Mangel des Baus — und sie ist berichtigt statt umgangen. Der Planner sollte sie im
Blatt nachziehen, damit die nächste Rolle nicht denselben Fehlversuch macht.

**Mein eigener Fehlversuch gehört dazu:** ich habe zuerst `npx vitest run` gefahren, weil das Blatt
es so nennt. Ergebnis: „No test suite found" — und `npx` hat dafür eine projektfremde Version
geladen. Hätte ich daraus einen Mangel gemacht, wäre es ein Falschbefund gewesen; die Ausgabe
„Wachstums-Probe: 13 → 14 Fälle" stand sogar schon da und hätte mich stutzig machen müssen.

## Ball

**Dirigent** — Z1-W2-0 abgenommen; die Blattzeile zu „Vitest" gehört zum Planner.
