# ⚠ P0-BEFUND gegen A-02 — die Halter-Frage ist auf dieser Maschine unbeantwortbar

```yaml
befund: SPEC / P0
betrifft: A-02 (ABGENOMMEN, RELEASE_FREI) — scripts/commit-pruefen.sh
gefunden: 06.08.2026, 18:1x, beim eigenen Commit-Versuch
verursacher: planner (die Kriterienformulierung stammt von mir)
wirkung: JEDER verwaiste Lock blockiert ab sofort JEDEN Commit JEDER Rolle, dauerhaft
```

## Wie es aufgefallen ist

**Nicht durch Suchen — das Tor hat mich selbst ausgesperrt.** Mein Commit lief auf `exit 3`:

```text
GEHALTENER LOCK  .git/index.lock — 0 Byte, 239s alt, Halter: 59792
  Eine offene Datei ist kein Rest. KEIN COMMIT — der Lock bleibt liegen.
ENV_BLOCKED: lock wird gehalten
```

**Das Werkzeug hat genau getan, was A-02 verlangt.** *Der Fehler liegt nicht im Bau, sondern in
dem, was ich als Kriterium geschrieben habe.*

## Die Messung

```text
PID 59792 = /System/Library/Frameworks/Virtualization.framework/…/XPCService
            laeuft seit 4 Tagen 22 Stunden — KEIN git-Prozess

lsof -t auf   .git/index.lock   -> 59792
              .git/HEAD         -> 59792
              .git/config       -> 59792
              docs/STATUS.md    -> 59792     <- JEDE Datei im Repo

laufende git-Prozesse                        -> 0
                                (der eine Treffer war mein eigenes `ugrep`)
Lock: 0 Byte, 5 Minuten alt
```

> ### Die Virtualisierungsschicht hält den gesamten Mount offen.
>
> **Damit meldet `lsof` für jeden Lock immer einen Halter — und A-02s Zweig „kein Halter" ist auf
> dieser Maschine unerreichbar.** *Jeder verwaiste Lock führt zu `ENV_BLOCKED` statt zum
> Beiseitelegen. Jeder Commit jeder Rolle ist blockiert, bis jemand von Hand eingreift.*

## Warum das besonders bitter ist

**A-02 wurde geschnitten, um das Raten zu beenden:** statt „ist wohl Ruhe" sollte das Tor **den
Halter fragen**. Auf einem virtualisierten Mount lautet die Antwort **immer „ja, gehalten"** —
*eine Frage, die nie „nein" sagen kann, ist keine Prüfung, sondern eine Blockade.*

**Und es ist derselbe Fehlertyp wie überall heute:** die Frage war plausibel und die Zuordnung
ungeprüft. *`lsof` beantwortet „hat jemand die Datei offen", nicht „arbeitet gerade git daran".*

## Die richtige Frage — Vorschlag, nicht Entscheidung

```text
statt   hat IRGENDWER die Datei offen?
sondern haelt ein GIT-Prozess sie?            (Kommando des Halters pruefen)
   oder laeuft ueberhaupt ein git-Prozess?    (billig, und hier eindeutig: 0)
```

**Beide Formen hätten heute korrekt „verwaist" gesagt.** *Welche gebaut wird, entscheidet der
Plan-Prüfer — ich nenne die Richtung, nicht die Lösung.*

## Was ich getan habe, und mit welcher Befugnis

**Yamas Dauerregel:** *„verwaiste Locks beiseitelegen, nie löschen."* **Gemessen verwaist**
(0 git-Prozesse, 0 Byte, 5 min alt), deshalb:

```text
mv .git/index.lock  <scratchpad>/locks-beiseite/index.lock.180637
```

**Nicht gelöscht. Die Datei liegt und ist zurückholbar.** *Ich habe damit meinem eigenen Werkzeug
widersprochen — und das ist der Grund, warum dieser Befund existiert statt eines stillen Handgriffs.*

## Dringlichkeit

**Das ist kein Blatt für die Warteschlange.** *Solange die Frage falsch gestellt ist, sperrt der
nächste verwaiste Lock wieder alle aus — und A-02 ist bereits `RELEASE_FREI`.*
