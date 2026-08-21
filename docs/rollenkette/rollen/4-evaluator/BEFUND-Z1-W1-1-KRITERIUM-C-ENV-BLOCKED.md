# Z1-W1-1 Kriterium C — `ENV_BLOCKED`. Die Wiederholung ist eingetreten.

**evaluator · 21.08. 21:03 · Prüfstand `52c861a3` · Bühne 8199**

Kriterium C verlangt eine **Browserabnahme** („Screenshot einer platzierten Treppe mit Hinweis",
Blatt `:48`, dort ausdrücklich *„offen — Abnahme (Evaluator)"*). Sie ist **nicht durchführbar**,
solange alle Rollen dieselbe Testablage benutzen.

## Zwei Vorfälle in 18 Minuten, je gemessen

| Zeit | Ereignis |
|---|---|
| ~20:2x | Prüfkonto angelegt: `id=70`, Konten 1 |
| 20:44 | Konten **0** — Konto weg; Anmeldung scheitert, Insel lädt nicht (`hp 0`, `canvas 0`) |
| 20:45 | neu angelegt → **`id=1`** statt 71 (Zähler zurückgesetzt = Tabelle neu aufgesetzt) |
| 20:45 | Insel lädt wieder: `hp 80 → 96`, `canvas 2` — Messung möglich |
| 21:03 | Konten **0** — Konto **erneut** weg; `canvas` wieder leer. **Bühne läuft weiter.** |

Der zweite Vorfall ist der entscheidende: Zwischen 20:45 und 21:03 habe ich die Bühne nicht
angefasst und nichts an der Ablage geändert. Sie wurde **während meiner laufenden Messung** von
außen neu aufgesetzt.

## Warum das `ENV_BLOCKED` ist und kein Baumangel

`ARBEITSREGELN.md:304`, wörtlich: *„Ein durch Locks, **konkurrierende Datenbankläufe**, fehlende
Browsersteuerung oder defekte Testinfrastruktur verursachter Lauf ist `ENV_BLOCKED` und kein
Produktfehler."*

`:302` verbietet die Ursache: *„Insbesondere dürfen Generator und Evaluator nicht gleichzeitig
dieselbe `ticket_testing`-Datenbank verwenden."* Alle sechs Bäume schreiben sie über `force="true"`
vor — je aus ihrer eigenen `phpunit.xml` gelesen.

**Ich hatte diese Grenze selbst gesetzt**, bevor sie eintrat (`521327a8`): *„Erst wenn es sich
während der Messung wiederholt, ist das Ergebnis nicht mehr belastbar und der Lauf wird
`ENV_BLOCKED` — mit genau diesem Beleg."* Sie ist eingetreten, und ich halte mich daran.

## Was das für Z1-W1-1 heißt

| | Ergebnis | Beleg |
|---|---|---|
| A | **erfüllt** | Bau fügt Bedingung `!erg.pruefungen.some(pr => pr.id === 'durchgangshoehe')` und den Text ein |
| B | **erfüllt** | an der echten Engine gerechnet: ohne Übergabe 6 Prüfungen / `bestanden true`; mit 1900 mm 7 Prüfungen / `bestanden false` |
| C | **`ENV_BLOCKED`** | Browserabnahme, zweimal von außen unterbrochen |
| D | **erfüllt** | `git diff 2bc0d2f2^ 2bc0d2f2 --numstat -- geometry/treppenBerechnung.ts` → **0 Zeilen** |

**Kein Votum für Z1-W1-1**, weder `ABGENOMMEN` noch `NACHBESSERN`: Drei Kriterien tragen, eines ist
nicht messbar. Der Gesamtauftrag bindet mich hier ausdrücklich (`:82`): *„offene Browserprüfung
niemals als bestanden melden."*

## Was zur Behebung nötig ist — und wem es gehört

Eine **eigene Testablage je gleichzeitig arbeitender Rolle** (oder eine technische Serialisierung
aller ablageverändernden Läufe). Eine Absprache genügt nicht: Die kollidierende Rolle konnte nicht
anders, ihre eigene `phpunit.xml` schreibt ihr dieselbe Ablage vor.

**Ball beim Planner** — `phpunit.xml` ist gemeinsamer versionierter Code, dieselbe Einordnung wie
`js-yaml` bei A-37-21. Bis dahin bleibt Kriterium C offen, und mit ihm die Abnahme von Z1-W1-1.

## Was ich in der Zwischenzeit tue

Die Kriterien der übrigen vier Aufträge, soweit sie ohne Bühne messbar sind — Diff, Rechnung,
Suite, `tsc`, Scope. Was einen Browser braucht, bleibt offen und wird als offen gemeldet.
