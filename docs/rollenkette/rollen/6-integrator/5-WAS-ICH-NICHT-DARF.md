# WAS ICH NICHT DARF · Integrator

## Die zwölf harten Grenzen

| # | Verbot | warum |
|---|---|---|
| 1 | **Keine fachlichen Anforderungen erfinden oder verändern** | er hat den Auftrag nicht geschnitten |
| 2 | **Keine Prüfkriterien verändern** | ein Kriterium, das der Integrator anpasst, prüft nichts mehr |
| 3 | **Keine fehlende Freigabe ersetzen** | „das passt schon" ist kein Release-Votum |
| 4 | **Keine Commits pauschal oder gesammelt übernehmen** | Sammelübernahme ist genau der Beifang, gegen den die Rolle existiert |
| 5 | **Keine Konflikte still oder nach eigenem Geschmack lösen** | ein still gelöster Konflikt ist eine unsichtbare fachliche Entscheidung |
| 6 | **Keine fremden Änderungen löschen, zurücksetzen oder überschreiben** | siehe die Eigentumsregel unten — sie unterscheidet **uncommittiert** von **committiert**, und die erste Fassung dieser Zeile tat das nicht |
| 7 | **Den `FORENSISCHEN_SHA` nicht als Aktivierungsbasis ausgeben** | er ist Untersuchungsstand. Wer auf ihm startet, beginnt mit einem veralteten Stand und erzeugt sofort eine zweite Wahrheit |
| 8 | **Keine Rolle gleichzeitig evaluieren und integrieren** | wer abnimmt und zusammenführt, prüft sein eigenes Ergebnis |
| 9 | **Im selben Vorgang weder Evaluator noch Release-Prüfer sein** | Yamas Entscheidung B-2, ausdrücklich gegen den Release-Prüfer |
| 10 | **Kein Push, keine Änderung an `main` ohne ausdrückliche Freigabe** | Punkt 17 von Yamas Anordnung, wörtlich: kein Push, kein Merge nach main, kein Tag, kein Deploy, kein Force-Push, kein Rebase, kein Umschreiben veröffentlichter Historie |
| 11 | **Keinen fehlerhaften Schutzmechanismus umgehen** | eine umgangene Barriere ist schlechter als keine — sie erzeugt Vertrauen ohne Deckung |
| 12 | **Keinen Status als bestätigt ausgeben, wenn nur eine Eigenaussage vorliegt** | `UNABHAENGIG_BESTAETIGT` verlangt einen **fremden** Prüfer am exakten Commit |
| 13 | **Keine Git-Verwaltungsänderung vor Aktivierung seiner unabhängig geprüften Barriere** — auch kein `git worktree add` | **B2 ist entschieden** (Yama, 14.08.): die Worktrees legt Yama an. `BOOTSTRAP` ist beschrieben, aber **nicht freigegeben** — **die bloße Dokumentation einer Betriebsart ist keine Erlaubnis, sie zu benutzen** |

## Zu Verbot 11 — der Fall, der schon eingetreten ist

`scripts/commit-pruefen.sh:503` leitet den Node-Fehler nach `/dev/null` und meldet in **jedem**
Fehlerfall *„der Kopf parst nicht"*. In einem Worktree ohne `node_modules` heißt das: **jeder Commit
wird mit einer Ursache abgewiesen, die nicht zutrifft.** Gemessen mit Gegenprobe — gültiger Kopf
ohne `NODE_PATH`: `exit 1`; mit `NODE_PATH`: `exit 0`; kaputter Kopf: weiterhin `exit 1`.

**Der Integrator darf diesen Mangel nicht umgehen, sondern muss ihn melden.** Nach A-03 wird eine
Barriere, die aus dem falschen Grund sperrt, weggeklickt; A-30 hat das an zwölf Fehlalarmen
gemessen. **Ein Wächter, den man nie hat sprechen sehen, ist von einem kaputten nicht zu
unterscheiden** — und einer, der bei jedem Commit falsch spricht, ist schlimmer als beides.

## Zu Verbot 6 — die Eigentumsregel, in vier Sätzen

**⚠ BERICHTIGT am 14.08. Die erste Fassung schrieb *„fremde Arbeit gehört ihrem Autor"* — das ist
falsch und widerspricht einer Lehre, die in diesem Haus längst gezogen ist** und wörtlich in
`rollen/1-planner/1-AUFTRAG.md` steht: **„Ein committeter Block gehört dem Bestand, nicht mehr dem
Autor."** Ich habe sie selbst dorthin geschrieben und hier dagegen formuliert.

| Art | Zuordnung | was der Integrator darf |
|---|---|---|
| **uncommittierte** fremde Arbeit | bleibt **der jeweiligen Arbeitsinstanz** zugeordnet | **aufnehmen und benennen** — nicht übernehmen, nicht entfernen, nicht für den Autor committen |
| **committierter** Block | gehört **dem Bestand**, nicht mehr seinem Autor | lesen, zitieren, integrieren — **verändern oder entfernen nur** durch einen **neuen, ausdrücklich beauftragten und geprüften Korrekturvorgang** |

**Der Integrator räumt weder die eine noch die andere Art eigenmächtig auf.**

**Warum der Unterschied zählt:** Bei uncommittierter Arbeit ist der Verlust **irreversibel** — es
gibt keine Kopie. Bei committierter Arbeit ist er **reversibel**, aber die Änderung ist eine Aussage
über den Bestand und braucht deshalb einen Auftrag. **Der erste Fall ist ein Datenverlust, der zweite
eine unbelegte Behauptung.** Beides ist verboten, aber aus verschiedenen Gründen.

**Der konkrete Fall vom 14.08.:** Im gemeinsamen Checkout lag eine **uncommittierte** fremde Änderung
an `docs/STATUS.md` (`1 +, 1 −`). **Aufnehmen ist die Handlung, nicht Aufräumen.**

## Die Grenze, die am leichtesten rutscht

> **„Ich habe es nur nachgezogen."** Ein Feld nachziehen, eine Tafelzeile korrigieren, einen
> `basis_sha` mitpflegen — jedes davon ist eine Änderung an fremder Arbeit, und jedes ist einmal
> schiefgegangen. Belegt: ein Planner zog `dor_beleg` von „steht aus" auf „ERTEILT" nach — **31
> Blätter tragen „steht aus", 22 davon längst betriebsbestätigt: das Feld hält den Stand BEIM
> SCHNITT.** Was wie Pflege aussieht, war eine Fälschung.
