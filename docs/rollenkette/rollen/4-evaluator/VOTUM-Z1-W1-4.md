# VOTUM Z1-W1-4 — „dachWerte: eine Quelle, Stilllegung statt Löschung"

**evaluator · 21.08. · Bau `b2371d7e` · Basis `11f7c4c3` · Prüfstand `52c861a3`**

## Ergebnis: ABGENOMMEN — vier von vier Kriterien, je selbst gemessen

| # | Ergebnis | Beleg (selbst gefahren) |
|---|---|---|
| A | **erfüllt** | `grep -rn "utils/dachWerte" resources/` → 2 Treffer, **0 echte Imports**. Beide **einzeln geöffnet**: `dachGeometrie.ts:13` ist ein Kommentar, der zweite ist der Stilllegungsvermerk in der Datei selbst |
| B | **erfüllt** | Kopie existiert (6176 B / 131 Z.), Kopf trägt wörtlich *„STILLGELEGT am 21.08.2026 — Z1-W1-4. Nicht gelöscht, nicht ändern."* |
| C | **erfüllt, zeichengenau** | md5 am Stand `b2371d7e^` **selbst nachgerechnet**: `b5738234bebca5a3599f65c3f797c06f` — identisch mit der Zusage; auch **103 Z. / 4188 B** exakt |
| D | **erfüllt** | `tsc:hausplaner` exit **0**, `error TS` **0** · Suite **1777 / 1777**, fail **0** |

## Nicht-Ziele — alle eingehalten, einzeln gemessen

| Nicht-Ziel | Messung |
|---|---|
| keine Löschung (Y-2) | gelöschte Dateien: **0** |
| keine inhaltliche Änderung an Werten oder Funktionen | `dachWerte.ts`: **0** Nicht-Kommentarzeilen hinzugefügt, **0** entfernt |
| kein Fremdpfad | `app/` und `docs/STATUS.md`: **0** |

Der gesamte inhaltliche Eingriff ist **eine Zeile**: der Import in `dachGeometrie.ts` wandert von
`'../../utils/dachWerte'` auf `'./dachWerte'`. Der Bau begründet das im Code, und die Begründung
trägt: **nur die geometry-Fassung ist getestet** (`__tests__/dachWerte.test.ts:12`) — eine
Abweichung in der utils-Kopie wäre nirgends aufgefallen. Aus zwei byte-identischen Fassungen mit je
einem Verbraucher wird eine getestete Quelle, ohne dass etwas verschwindet.

## Warum Kriterium C das beste der vier ist

Es verlangt einen **md5 vor der eigenen Änderung** — also einen Beleg, den der Bau nachträglich
nicht mehr herstellen könnte, und der eine spätere Löschentscheidung (Y-2) tragfähig macht.
Ich habe ihn nicht aus dem Blatt übernommen, sondern am Elternstand neu gerechnet: **identisch.**
*Ein Kriterium, das seinen eigenen Beleg unfälschbar macht, ist mehr wert als drei, die man
nachträglich hinschreiben kann.*

## Weitergabe

**ABGENOMMEN** → Release-Prüfer. Zustand setze ich nicht — `docs/STATUS.md` ist mir nach A-37-6
gesperrt; der Nachtrag gehört dem **Integrator**.
