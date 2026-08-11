# BEFUND — meine `must_preserve`-Messung sieht Hinzugefügtes nicht

**Gemessen:** 11.08.2026 · **Rolle:** Generator, gegen die eigene Methode · **Ball:** Evaluator
(er prüft das Kriterium) und Planner (es steht in jedem W-Blatt).

## Der Beweis

```text
MEINE Methode  : 1230 verfolgte Dateien, Abweichungen 0   -> meldet "byte-identisch"
WIRKLICHKEIT   : 1236 Dateien im Baum, davon 6 NICHT in HEAD
```

**Die Methode vergleicht `git ls-tree -r HEAD` gegen `git hash-object` — also nur, was HEAD schon
kennt.** Eine **hinzugefügte** Datei kommt in dieser Liste nicht vor und kann darum keine Abweichung
erzeugen. *Ich habe geprüft, ob sich etwas geändert hat, und daraus geschlossen, dass sich nichts
geändert hat.*

## Wie schwer es wiegt — nachgemessen statt geschätzt

Von den sechs sind **fünf `git`-ignoriert** (`resources/js/.env`, vier `.DS_Store`) — Rauschen.
**Eine nicht:**

```text
resources/planner/hausplaner/__tests__/zzA12wegwerf.test.ts   14.612 B   10.08. 21:58:59
```

Eine Wegwerf-Probe aus dem laufenden A-12 einer anderen Generator-Instanz — **unverfolgt, nicht
ignoriert, und sie liegt im `__tests__`-Ordner der Insel.** Ein Testlauf nimmt sie mit.

## Was das für meine bisherigen Meldungen heißt

**Meine drei Meldungen „`resources/**` byte-identisch, 0 Abweichungen" (W-01, W-02 zweimal) waren
zum Zeitpunkt der Messung sachlich richtig** — die Datei entstand um 21:58:59, meine letzte Messung
lief um 21:49. *Der Satz stimmte; die Methode, mit der ich ihn belegt habe, trägt ihn nicht.*
**Das ist der Unterschied zwischen recht haben und es gemessen haben.**

## Die scharfe Form

```bash
# 1) Geändertes: verfolgte Dateien gegen HEAD (bisher)
git ls-tree -r HEAD -- resources/  gegen  git hash-object
# 2) Hinzugefügtes: unverfolgt UND nicht ignoriert  (fehlte)
git ls-files --others --exclude-standard -- resources/
# 3) Entferntes: in HEAD, nicht mehr im Baum        (fehlte ebenfalls)
```

**Alle drei Richtungen, sonst heißt „unverändert" nur „nichts von dem, wonach ich gesucht habe".**

## Was ich NICHT tue

**Ich fasse `zzA12wegwerf.test.ts` nicht an.** Sie gehört zum laufenden A-12 einer anderen Instanz,
ist dort als Wegwerf-Probe angekündigt, und Aufräumen an fremder Arbeit ist Beifang. **Gemeldet, nicht
gelöscht** — dasselbe gilt für die Streudatei `1692` im Wurzelverzeichnis (35 B, Inhalt
`(eval):19: command not found: 1689`, offenbar eine verunglückte Umleitung).
