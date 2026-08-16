# Ballbesitz Generator — die zehn, klassifiziert

> **Anlass:** Yama am 16.08. abends — *„beim Generator zehn, vier davon zu A-41"*.
> **Gemessen** in `docs/STATUS.md` des Integrationszweiges, nicht aus dem Gedächtnis.

```text
Bloecke mit  ballbesitz: generator     10
```

## Die Einteilung

| Anzahl | Kennung | Lage heute | Folge |
|---|---|---|---|
| **4** | `A-41` | die vier von Yama genannten Befunde | **behoben** am 16.08. — zwei schon vorher (`2e9cf127`, `253a51d7`), zwei an diesem Abend (`a26b85b4`) |
| **1** | `a37_exit_codes…` | der K4-Befund des Plan-Prüfers (`e000f087`) | **behoben** (`a26b85b4`): K4 gibt 0 und meldet, statt 2 zu belegen |
| **3** | `A-04` | der Auftrag steht auf `BETRIEBSBESTAETIGT` | **Ball ersatzlos weg** |
| **2** | `A-07`, `A-08` | Befundblöcke vom 03./04.08.; beide sagen im eigenen Text, der Punkt sei aufgelöst | **kein Halter nötig** |

## Warum fünf davon nie Arbeit waren

**`ballbesitz` wurde als „ich habe das gemessen" geschrieben — das Feld bedeutet aber „hier liegt
Arbeit".** *H-9, ein Wort mit zwei Bedeutungen.*

> ***Der Plan-Prüfer hat dasselbe an sich selbst gefunden*** (`651e61e4`): *39 Bälle, keiner
> wartend, dieselbe Ursache.* **Bei ihm 39-mal, bei mir fünfmal — das Muster ist nicht persönlich,
> es steckt im Feld.**

**Geöffnet statt gezählt:** `A-07` trägt eine Gegenprobe auf einer Kopie mit unberührtem Original,
`A-08` schließt mit *„damit tragen die zwei Vorfälle keine gegensätzliche Lehre mehr"*. **Beide
sind Belege, keine Aufträge.**

## Was daraus folgt — und für wen

- **Für den Integrator:** die fünf Bälle unter `A-04`, `A-07`, `A-08` können entfallen. *Ich kann
  es nicht selbst tun:* `docs/STATUS.md` hat einen Schreiber, und **meine eigene Sperre hält mich
  davon ab** — gemessen, nicht vermutet (`TOR_STATUS_PFAD=1` → `exit 1`).
- **Für den Planner:** ob `ballbesitz` künftig zwei Felder braucht — *wer gemessen hat* und *wo
  Arbeit liegt* — ist eine Formfrage und gehört ihm. **Ich melde nur, dass ein Feld heute beides
  trägt.**

## Stand des Generators am 16.08. abends

```text
eigene Fehlerinventur      9 gefunden, 9 behoben, in drei Klassen
Baelle                    10 geprueft, 0 offen
offen und nicht bei mir    5-CODE.md als Datei in W-26/W-28/W-43 gegen 38x Verzeichnis
                           — der Weg fuehrt ueber eine Umbenennung, und das Tor weist
                             geloeschte Pfade mit FEHLT ab. Wartet auf Yamas Entscheidung.
```
