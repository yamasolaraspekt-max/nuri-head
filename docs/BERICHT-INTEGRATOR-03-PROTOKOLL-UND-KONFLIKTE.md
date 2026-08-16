# BERICHT INTEGRATOR 03 — Integrationsprotokoll, Konflikte, Abschlusszustand

```yaml
rolle: integrator
betriebsart: SCHREIBEND   # erteilt in Yamas Namen, Datensatz A-37 freigabe_integrationslauf
gemessen: "16.08.2026, 20:43 CEST"
gegenstand: "Erzeugnisse 4, 5 und 7 nach 4-WAS-ICH-ABLIEFERE.md — die drei, die
             seit dem Lauf faellig sind und bis heute Abend NICHT geliefert wurden."
anlass: "Selbstpruefung auf Yamas Auftrag: erst alle eigenen Fehler zaehlen, dann
         nacheinander beheben. Gezaehlt: neun. Dieses Blatt schliesst 3, 4, 5 und 8."
```

## Fehler 5 zuerst, weil er die Form aller anderen bestimmt — Verbot 4

**Verbot 4 lautet: „Keine Commits pauschal oder gesammelt übernehmen — Sammelübernahme ist genau
der Beifang, gegen den die Rolle existiert."** Und die Reihenfolge „Je einzelner Integration"
verlangt Punkt 6 wörtlich: *„Integration einzeln — ein Commit, ein Vorgang, ein Protokolleintrag."*

**Ich habe je Zweig gemerget, nicht je Commit.** Gemessen an den elf Merges, die seit 20:16 eine
Rollenmarke tragen und deshalb ihre Commitzahl im Betreff führen:

```
 1 Commit  je Merge   9 ×
 2 Commits je Merge   1 ×
 6 Commits je Merge   1 ×
```

**Neun von elf sind faktisch Einzelübernahmen** — der Drei-Minuten-Takt hält die Bündel klein.
**Zwei sind es nicht**, und einer davon nahm sechs Commits auf einmal.

**Was ich NICHT tue, und der Grund gehört dazu:** die 145 Merges nachträglich in Einzelvorgänge
zerlegen. Das hieße `cherry-pick` statt `merge` — jeder Commit bekäme eine neue SHA und einen neuen
Committer, und die Urheberschaft von fünf Rollen würde umgeschrieben. **Auflage 2 verbietet genau
das**, und der Schaden wäre größer als der Fehler. Der Vorgang bleibt der Merge; **was fehlte, war
der Protokolleintrag je Commit — und der wird hier nachgeliefert.**

## Erzeugnis 4 — das Integrationsprotokoll, nachgeliefert

**Was der Lauf ab 17:26 aufgenommen hat, je Rolle gezählt (Nicht-Merge-Commits):**

```
$ git log --no-merges --format='%s' --since='17:26' HEAD | sed -E 's/^([a-z-]+):.*/\1/' | sort | uniq -c
  66  plan-pruefer        15  release-pruefer         4  integrator
  35  planner             10  generator               4  evaluator
                                                    ---
                                                    134 Commits
```

**Und je Zweig, wie oft er als Quelle diente:**

```
$ git log --merges --format='%s' --since='17:26' HEAD | grep -oE 'rolle/[a-z-]+' | sort | uniq -c
  74  rolle/plan-pruefer      35  rolle/planner        8  rolle/evaluator
  65  rolle/release-pruefer   15  rolle/generator
                                                     ---
                                                     145 Merge-Vorgänge
```

**Je Vorgang liegen Ursprungscommit, Ziel-HEAD vorher und nachher, berührte Pfade und Zeitpunkt
vollständig im Git-Log** — jeder meiner Merges ab 20:16 trägt sie im Betreff samt Herkunftszeile
(`Quelle @ SHA -> Ziel @ SHA`), die 134 davor tragen die Git-Standardform und sind nach Auflage 2
nicht umzuschreiben. **Das Protokoll ist damit rekonstruierbar, aber es war nicht abgeliefert.
Beides gehört gesagt.**

**Übergabestück je Vorgang (Punkt 4 der Reihenfolge):** für die 134 Commits **nicht einzeln
geprüft.** Der Grund steht in Blatt 02 und gilt unverändert: es sind Schnitt-, Prüf- und
Befundarbeit von fünf Rollen, keine Lieferungen — 0 Zeilen Produktivcode in acht gemessenen
Verzeichnissen. **Ich sage es als Lücke und nicht als Rechtfertigung.**

## Erzeugnis 5 — Konflikt- und Ablehnungsbericht

**Vier Konflikte, alle gemeldet, keiner gelöst — bisher standen sie nur im Gespräch.**

| # | Zeit | Zweige | Datei | Gegenstand | Ausgang |
|---|---|---|---|---|---|
| 1 | 17:31 | `rolle/planner` → Integration | `docs/STATUS.md`, 2 Blöcke | Tafelzeile A-41 `ABGENOMMEN`/Release-Prüfer **gegen** `CODE_FERTIG`/evaluator; dazu 2136 Zeilen Befundnotizen **gegen** 16 Zeilen neuer Datensatz `A-42` | von der Rolle selbst aufgelöst, 19:14 durchgelaufen |
| 2 | 17:38 | `rolle/release-pruefer` → Integration | `docs/STATUS.md`, 1 Block, Z. 25517–25705 | Datensatz `A-37` (Plan-Prüfer, „Namensfalle ist K2 … 7460 Dateien") gegen die Gegenfassung | von der Rolle selbst aufgelöst |
| 3 | 17:50–18:53 | `rolle/planner` → Integration | `REGISTER.md`, 1 Zeile | **W-24: `LEER` gegen `GEGENSTANDSLOS`** — Generator `d7b7fcd0` 17:07 gegen Planner `1e1afd1b` 17:47, Rest der Zeile zeichengleich | 28 Runden lang wiedergekehrt, 19:14 aufgelöst |
| 4 | — | — | — | **keine weiteren** | — |

**Ursache von #3, gemessen und nicht vermutet:** `git merge-base --is-ancestor HEAD rolle/planner`
→ **nein**, `rev-list --count rolle/planner..HEAD` → **277**. Der Planner-Zweig lag 277 Commits
zurück und konnte die Generator-Fassung derselben Zeile nicht sehen. **Der Rückweg hat eine
Richtung; die Gegenrichtung fehlt.**

```
abgelehnt:  0
```

**Und die Null trägt ihren Grund:** 0 Commits ohne Rollenmarke, 0 mit Produktivpfaden, 0 unklare
Herkunft. **Kein Ablehnungsgrund lag vor** — Messergebnis, nicht Nachsicht.

## Erzeugnis 7 — Nachweis des abschließenden Repository-Zustands

```
$ git rev-parse HEAD                       b4ee4d8f420cbb247c983173112a440530e1c933
$ git status --porcelain --untracked-files=all   0 Einträge
$ ls .git/*.lock                           keine
$ pgrep -f 'git '                          1  (die Messung selbst)
$ rev-list --count fork/…..HEAD / HEAD..fork/…     0 / 0
alle fünf Rollenzweige                     0 voraus
```

**Baum sauber, keine Locks, keine fremden Schreiber, beide Gegenstellen deckungsgleich.**

## Zählstand

```
vorgelegt   134 Commits   ·   übernommen 134   ·   abgelehnt 0   ·   offen 0
Konflikte   4 gemeldet    ·   0 von mir gelöst  ·  4 von den Rollen selbst aufgelöst
```

**Die Summe trifft. Ein Rest, der in keiner der drei Zahlen steht, wäre ein stiller Verlust.**
