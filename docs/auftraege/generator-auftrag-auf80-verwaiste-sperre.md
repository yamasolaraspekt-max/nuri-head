# ⇒ GENERATOR-AUFTRAG AUF-80 — Die verwaiste Sperre macht den Wächter stumm

**Vom:** Planner · **26.07.2026, 12:50** · **Spur A** — es geht um die Frage, ob eine
Sicherheitsmeldung überhaupt noch kommt. **Heimat-App:** `ticket`.
**Grundlage:** **Auflage AUF-75.1** aus dem Votum des Evaluators (`892337e`), live beobachtet und
reproduziert.

**Vorher gelesen:** HEAD `892337e` · Votum `### AUF-75` in
`docs/abnahme-evaluator-haertung-2026-07-25.md` · `scripts/waechter.sh` · `scripts/hooks/post-commit`
· `docs/befunde/waechter.log`.

---

## 1. Der Befund — und er ist die Falle des Postens durch eine andere Tür

**Live beobachtet, nicht hergeleitet:** `docs/befunde/.waechter-laeuft` lag von 12:35 bis 12:38+
**ohne haltenden Prozess** (`ps`: kein Wächter). Jeder Folgelauf meldete
`uebersprungen (Lauf aktiv)` **mit exit 0**.

**Der Wächter war stumm geschaltet — und sah dabei gesund aus.**

**Ursache, sauber benannt:** `mkdir`-Sperre plus `trap 'rmdir …' EXIT` fängt das normale Ende und
die meisten Signale — **aber nicht SIGKILL.** Der per Hook mit `nohup` gestartete Hintergrundlauf
wird beim Sitzungs- oder Terminalende **hart** beendet, bevor der `trap` läuft. **Es gibt keine
Erkennung einer verwaisten Sperre** — keine Prozesskennung, kein Alter. **Sie heilt nie.**

**Das ist wörtlich die Gefahr, die AUF-75 §2c selbst benannt hat:** *„Ein umgangener Wächter ist
schlechter als keiner, weil er Sicherheit vortäuscht."* **Nur kommt sie nicht durch das Umgehen,
sondern durch die Sperre.**

## 2. Was gebaut wird — die Richtung stammt vom Evaluator, nicht von mir

1. **Die Sperre trägt die Prozesskennung** und den Zeitpunkt ihrer Entstehung.
2. **Schlägt `mkdir` fehl, wird geprüft, ob der Halter lebt** — und ob die Sperre älter ist als die
   längste plausible Laufdauer.
3. **Verwaiste Sperre ⇒ zurückerobern, mit sichtbarer Warnung im Log** — nicht stillschweigend.
   *Ein Wächter, der sich selbst repariert und nichts sagt, verbirgt, dass etwas nicht stimmte.*
4. **„Übersprungen" ohne lebenden Halter ist kein `exit 0`.** Das ist der Kern: **die Zeile darf
   nicht aussehen wie ein normaler, erfolgreicher Lauf.**

## 3. Was **nicht** gebaut wird

- **Keine zweite Sperrmechanik daneben.** Die vorhandene wird um Kennung und Altersprüfung ergänzt.
- **Kein Aufräumdienst, kein Zeitplan, kein Hintergrunddienst.** Geprüft wird beim nächsten Lauf.
- **Keine Änderung an der Betroffenheits-Logik, den Gates oder dem Log-Format** außer der neuen
  Zeile für die Zurückeroberung.
- **Kein Anfassen von `app/`, `resources/`, `routes/`, `database/`, `tests/`.** Dieser Posten bleibt
  in `scripts/` — **auch damit er den gemessenen Merge-Stand nicht bewegt.**

## 4. Abnahmekriterien

1. **Der reproduzierte Fall heilt:** Wächter starten, `kill -9`, danach ein Commit ⇒ der nächste
   Lauf **läuft** (kein Dauer-Überspringen). Vorführen mit Log-Auszug.
2. **Die Zurückeroberung ist sichtbar:** das Log trägt eine **Warnzeile**, nicht nur den normalen
   Lauf. Wortlaut nennen.
3. **Überspringen ohne lebenden Halter ist nicht `exit 0`.** Test mit erzwungener verwaister Sperre.
4. **Echtes Parallel-Überspringen bleibt erhalten:** zwei Läufe gleichzeitig, der zweite überspringt
   **mit** lebendem Halter — und **das** ist weiterhin `exit 0`. *Der Unterschied zwischen den beiden
   Fällen ist der ganze Posten.*
5. **Die AUF-75-Zusagen bleiben grün**, namentlich: **rot gegen `e0d1144`** und
   **„nicht gelaufen" ist nie grün**. Beide erneut vorführen, nicht nur behaupten.
6. **Der Wächter schmutzt den Baum nicht:** `git status` nach einem Lauf unverändert;
   `git check-ignore` bestätigt Log und Sperre.
7. **`--no-optional-locks` weiterhin an jedem `git`-Aufruf** — `grep` mit Zahl.
8. **Nichts außerhalb von `scripts/` und `docs/befunde/`** — `git diff --numstat`, Zahl nennen.
9. **Mutations-Gegenbeweis:** die Altersprüfung entfernen ⇒ Kriterium 1 wird rot.
10. **Klassifikation: `Vorarbeit`.**

## 5. Zur Einordnung — damit der Posten nicht überschätzt wird

**AUF-75 ist abgenommen und bleibt es.** Der Wächter erfüllt jedes gestellte Kriterium; der
Evaluator hat beides selbst nachgewiesen — den roten Pfad und „nicht gelaufen ≠ grün".

**Dieser Posten schließt eine Lücke im Werkzeug, nicht im Erzeugnis.** Er ändert **null Zeilen** an
allem, was zum Kunden geht. *Er steht trotzdem vorn, weil ein Wächter, dem man nicht ansieht, dass
er schweigt, schlimmer ist als keiner — und weil genau darauf ab jetzt Vertrauen gesetzt werden soll.*

## 6. Bis dahin gilt

**Ein grüner Wächter-Lauf ist bis zur Abnahme dieses Postens kein Beleg.** Wer sich auf ihn beruft,
prüft vorher, ob überhaupt gelaufen wurde. **Das steht hier, damit es nicht mündlich weitergegeben
werden muss.**
