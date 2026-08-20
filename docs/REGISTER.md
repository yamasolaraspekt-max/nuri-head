# REGISTER — die fünf Ablagen

> **Zweck:** Ein Agent soll nicht suchen müssen. Er soll wissen, *wohin* er greift.
> Diese Seite ist die Landkarte; die fünf Register darunter sind die Fächer.

**Angelegt:** 20.08.2026 · **Autorität über den Inhalt:** Yama
**Diese Seite ändert keine Regel.** Verbindlich bleibt allein
[`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) (Fassung 1.4.2).

---

## Die fünf Fächer

| # | Fach | Ort | Was hineingehört | Wer schreibt |
|---|---|---|---|---|
| 1 | **Agenten** | [`.claude/agents/`](../.claude/agents/) | je eine Agenten-Definition (`name.md` mit Frontmatter) | Yama / Planner |
| 2 | **Regelwerk** | [`docs/regelwerk/`](regelwerk/REGISTER.md) | alles, was *gilt* — Regeln, Bauordnung, Rollen, Entscheidungen mit Reichweite | Yama |
| 3 | **Backlog / Nachbesserung** | [`docs/backlog/`](backlog/REGISTER.md) | alles, was *offen* ist — Befunde ohne Erledigung, Nachbesserungen, Rückstände | Prüfer melden, Planner trägt ein |
| 4 | **Konzept** | [`docs/konzept/`](konzept/REGISTER.md) | alles, was *gedacht, aber nicht gebaut* ist — Entwürfe, Zielbilder, Fach-Spezifikationen | Planner |
| 5 | **Fortschritt** | [`docs/fortschritt/`](fortschritt/REGISTER.md) | alles, was *erreicht* ist — Wellenberichte, Messreihen, Chronik | Integrator / Evaluator |

---

## Quellen außerhalb des Repos

**Lesen ist auf dem ganzen Rechner frei, schreiben nur in der Heimat-App** (Mehr-App-Regel;
Yamas Anweisung vom 20.08.). Wo die Fundgruben liegen — Wissensregister, planner-handover,
Playground, Grafik-/Fachordner — und welche vier Grenzen dabei gelten (Zugangsdaten tabu,
Kundendaten bleiben wo sie sind), steht in [`docs/regelwerk/QUELLEN.md`](regelwerk/QUELLEN.md).

## Der eine Ort, der ausdrücklich NICHT hierher wandert

**`docs/STATUS.md` bleibt der Statusträger.** ARBEITSREGELN §16 benennt ihn namentlich, §  „Yamas
§1-Entscheidung zu `e521bd98`" stellt fest: **erzeugt, nicht von Hand bearbeitet**, alleiniger
Schreiber ist der Integrator.

Das Fach *Fortschritt* trägt deshalb **abgeleitete** Fortschrittsbelege — Berichte, Messreihen,
Chronik. Es trägt **keinen Zustand**. Wer wissen will, ob ein Auftrag `BEREIT` ist, liest
`docs/STATUS.md` und sonst nichts. Ein zweiter Zustandsort wäre genau die zweite Wahrheit, die §1
abgeschafft hat.

---

## Warum es diese Seite gibt (der gemessene Anlass)

Stand 20.08.2026, frisch gezählt:

```text
docs/ gesamt                       3593 Dateien
davon lose in docs/ (Wurzel)        331 .md-Dateien
Unterordner                          21
docs/konzept/                         1 Datei
Ordner für Regelwerk                  fehlt
Ordner für Backlog                    fehlt   (4 lose backlog-*.md in der Wurzel)
Ordner für Fortschritt                fehlt
```

331 lose Blätter in einem Ordner sind für einen Agenten nicht adressierbar. Er findet sie nur
über Volltextsuche — also zufällig. Die fünf Fächer machen den Zugriff **benannt** statt zufällig.

---

## Migration: was noch NICHT passiert ist

Die Register unten **verweisen** auf die vorhandenen Blätter, sie haben sie **nicht verschoben**.

*Grund:* Ein Umzug von 331 Dateien ist ein großer Umbau. Die stehende Rückfall-Regel verlangt
dafür Archiv **plus** Manifest **plus** Yamas Freigabe — und jeder Verweis aus 3593 Dateien, der
auf einen alten Pfad zeigt, bricht beim Verschieben. Der Umzug ist ein eigener Vorgang mit
eigenem Rückweg, kein Nebeneffekt dieser Seite.

**Bis dahin gilt:** Neues wird in den fünf Fächern angelegt. Altes bleibt liegen und ist über die
Register auffindbar.
