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

---

## 6. NACHTRAG 26.07., 14:15 — ein dritter Fall, und er ist der gefährlichste

**Gemessen, nicht vermutet.** Im Log steht um 14:08:

```
2026-07-26T14:08:11 - - uebersprungen (Lauf aktiv, pid 79)
```

Das ist die Zeile für den **gesunden** Fall: ein anderer Lauf arbeitet, also überspringen, exit 0.
**Sie ist falsch.** Die Sperre wurde um **13:54** von pid 79 angelegt — meinem eigenen Lauf über
die Cowork-Brücke, der längst beendet war. Zum Zeitpunkt der Prüfung um 14:08 hat `kill -0 79`
trotzdem **wahr** geliefert, weil in diesem Kontext inzwischen **ein anderer Prozess dieselbe
Nummer trug**. Gegenprobe eben ausgeführt: `kill -0 79` liefert jetzt **falsch**.

**Warum das schlimmer ist als der stumme Fall aus §1:** Der stumme Fall schreibt wenigstens
`OHNE lebenden Halter` ins Log — forensisch auffindbar. **Dieser hier schreibt die Zeile des
Gesundzustands** und endet mit **exit 0**. Ein Wächter, der übersprungen hat, weil er eine fremde
Prozessnummer für sich selbst hielt, **sieht in jeder Auswertung aus wie ein Wächter, bei dem alles
in Ordnung war.** Das ist genau die Richtung, nach der bei der Zustands-Inventur schon einmal
niemand gesucht hatte: etwas, das gesund aussieht und es nicht ist.

**Die Zeitgrenze rettet hier nicht.** `HOECHSTDAUER=1800`; die Sperre ist gemessen **1036 s** alt.
Das Alter greift also erst nach einer halben Stunde — und danach landet es wieder im `rm`-Problem
aus §2(a).

### (c) Was zusätzlich gebaut wird: eine Prozessnummer ist keine Identität

Die Sperre muss ihren Halter **eindeutig** ausweisen, nicht nur nummerisch. Der Weg ist deine
Entscheidung; zwei liegen nahe, und beide sind mit Bordmitteln zu haben:

- **Nummer plus Startzeitpunkt des Prozesses** — stimmt die Nummer, aber nicht der Startzeitpunkt,
  ist es ein anderer Prozess. `ps -o lstart= -p <pid>` liefert ihn.
- **Ein Lebenszeichen**, das der laufende Wächter regelmäßig erneuert. Ist es älter als ein
  benanntes Maß, lebt niemand mehr, ganz gleich, was die Nummer sagt.

**Kriterium, zusätzlich zu §4:**
9. **Der Fall ist nachgestellt und wird erkannt:** eine Sperre mit einer Nummer, die es gibt, aber
   von einem anderen Prozess — der Wächter darf sie **nicht** als lebenden Halter behandeln. Der
   Test dazu wird rot, wenn die Prüfung wieder auf die Nummer allein zurückfällt. Zahl nennen.
10. **Die Zeile für den gesunden Fall bleibt dem gesunden Fall vorbehalten.** Wer aus einem
    anderen Grund überspringt, schreibt eine andere Zeile. **Es darf keine zwei Zustände geben,
    die dieselbe Zeile schreiben** — das war der ganze Fehler.

**Die jetzt stehende Sperre (`pid 79`, geboren 13:54) ist der neue Prüfstein.** Nicht von Hand
räumen.
