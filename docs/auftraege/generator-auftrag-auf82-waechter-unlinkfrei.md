# ⇒ GENERATOR-AUFTRAG AUF-82 — Der Wächter heilt sich ohne `rm`, und sein Scheitern wird sichtbar

**Vom:** Planner · **26.07.2026, 15:05** · **Spur A** (er fasst das Prüfwerkzeug selbst an).
**Anlass:** Die Messung des Evaluators vom 26.07., 13:53 (`1a8f43e`) — **nicht meine Vermutung.**

**Vorher gelesen:** der Evaluator-Bericht im Ledger · `scripts/waechter.sh` §§ `erobern()`, Trap ·
`scripts/hooks/post-commit` Z.22–23 · `docs/befunde/waechter.log` · §8, §9, §10, §13.

---

## 1. Der Befund, in Zahlen des Evaluators (nachmessen, nicht glauben)

65 Aufrufe im Log: **45 echte Prüfläufe** (39 grün · 3 rot · 3 unvollständig) · **10 gesunde Skips**
· **7 gelungene Selbstheilungen** · **3 stumme** (`OHNE lebenden Halter`, exit 2).

**Zwei Sätze aus der Messung tragen diesen Posten:**

1. **Der Schaden ist heute null:** alle drei stummen Läufe tragen **keinen Commit** (`- -`). Es ist
   **kein** Code-Commit ohne Gate-Deckung geblieben. **Das ist der Grund, warum dieser Posten
   klein ist und nicht dringend.**
2. **Der Hook schluckt den Fehler vollständig:** `nohup … >/dev/null 2>&1 &` gefolgt von `exit 0`.
   Der eigens gebaute **exit 2 erreicht den einzigen realen Auslöse-Pfad nie**. Am Commit ist
   nichts zu sehen. **Das ist der Grund, warum er trotzdem gebaut wird:** ein Wächter, der
   scheitert und dabei gesund aussieht, ist genau der Zustand, gegen den AUF-75 angetreten ist.

**Die Ursache ist eine Umgebung, kein Denkfehler.** `erobern()` und der EXIT-Trap benutzen
`rm -rf`. Über die Cowork-Brücke ist `unlink` auf dem Mount verboten; beide `rm` scheitern still.
In einer nativen Shell funktionieren sie — **7× im Log belegt**. Die Sperre bleibt also nur über
**aufeinanderfolgende Brücken-Commits** stehen und wird vom nächsten nativen Commit geräumt.

**Ausdrücklich:** Das ist **kein** Fehler der AUF-80-Abnahme. Die Drei-Fälle-Logik stimmt; geprüft
wurde sie im `/tmp`-Mini-Repo, wo `rm` erlaubt ist. Der Evaluator hat das selbst benannt, bevor ich
es fragen musste.

## 2. Was gebaut wird — zwei unabhängige Stücke

**(a) `erobern()` und der EXIT-Trap kommen ohne `unlink` aus.**
Der Weg ist gemessen und liegt bereit: **`mv` ist auf dem Mount erlaubt** — der Evaluator hat es
direkt belegt. Die verwaiste Sperre wird also **beiseitegeschoben statt gelöscht**, nach demselben
Muster, das für `.git/*.lock` schon gilt: ein datierter Ablageort, kein `rm`.
**Der Ablageort wird beim Start einmal aufgeräumt oder wächst begrenzt** — eine Ablage, die
unbegrenzt wächst, ist ein zweites Problem, kein Fix.
**Die Drei-Fälle-Logik bleibt Zeichen für Zeichen:** lebender Halter → überspringen, exit 0 ·
toter Halter → zurückerobern **mit Warnzeile** · weder Kennung noch Zeitpunkt → zurückerobern,
laut. **Es ändert sich das Werkzeug, nicht das Verhalten.**

**(b) Der Hook macht ein Scheitern sichtbar.**
Ein Wächter-Ende ≠ 0 darf nicht mehr spurlos nach `/dev/null` gehen. **Die Bedingung, unter der
das gebaut werden darf:** der Hook **blockiert weiterhin nicht** und gibt weiterhin **0** zurück —
das war die gemessene Begründung aus AUF-75, und sie gilt unverändert (*ein umgangener Wächter ist
schlechter als keiner*). Sichtbar heißt also: eine Spur, die ein Mensch findet, **nicht** ein
Abbruch des Commits. Welche Form — Meldedatei, Fehlerstrom, eine Zeile im Log mit eigener
Kennzeichnung — **entscheidest du und begründest es in einem Satz.**

## 3. Was **nicht** gebaut wird

- **Keine Änderung an der Drei-Fälle-Logik**, an `HOECHSTDAUER` oder an dem, was der Wächter prüft.
- **Kein `rm` an anderer Stelle „bei der Gelegenheit"** — der Posten ist die Umstellung eines
  Werkzeugs, keine Aufräumaktion.
- **Die stehende Beleg-Sperre (`pid 76`) wird nicht von Hand geräumt.** Sie ist der Prüfstein:
  dein Bau muss sie beim ersten Lauf selbst wegräumen. **Räumst du sie vorher weg, hast du dir
  deinen eigenen Beweis genommen.**

## 4. Abnahmekriterien

1. `grep -n "rm -rf" scripts/waechter.sh` = **0 Treffer**. Zahl nennen.
2. **Der Gegen-Beweis am lebenden Objekt:** die vorhandene verwaiste Sperre (pid 76) wird beim
   ersten Lauf **zurückerobert**, und im Log steht die **WARNUNG**-Zeile. Rohausgabe beilegen.
3. **Der gesunde Parallelfall bleibt unangetastet:** lebender Halter → `uebersprungen (Lauf aktiv,
   pid N)` und **exit 0**. Mit einem echten laufenden Prozess belegen, nicht behaupten.
4. **Mutationsprobe:** wird der `mv`-Aufruf entfernt, muss ein Lauf wieder stumm enden — der Test
   dazu wird **rot**. Zahl nennen.
5. **Der Hook gibt weiterhin 0 zurück und blockiert nicht** — mit einer Zeitmessung am Commit
   belegen, nicht mit einer Zusicherung.
6. **Ein Wächter-Ende ≠ 0 hinterlässt eine auffindbare Spur.** Herbeiführen, zeigen, benennen wo.
7. `resources/`, `app/`, `routes/`, `public/` — **null Zeilen.** Dieser Posten fasst nur
   `scripts/` an.
8. **Klassifikation: `unsichtbar`.** Keine Sichtprobe; der Beleg ist das Log.

## 5. Reihenfolge — und warum sie hier zählt

**AUF-82 läuft unmittelbar vor AUF-79.** Beide fassen `scripts/waechter.sh` an. Liegt etwas
dazwischen, baut AUF-79 auf einer Fassung, die es beim Committen nicht mehr gibt. **Zwei Posten in
derselben Datei gehören nebeneinander oder in einen — nicht auseinandergerissen.**
