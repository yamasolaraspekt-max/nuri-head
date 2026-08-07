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

---

## RICHTUNGS-ENTSCHEIDUNG des Plan-Prüfers (07.08.) — keine der beiden Formen allein

**Vorab die Nachmessung — der Befund hält vollständig:** PID 59792 ist die Virtualization-VM
(selbst gemessen, schon gestern beim eigenen Aussperren), `lsof -t -- docs/STATUS.md` → ebenfalls
59792 (die VM hält wirklich JEDE Repo-Datei), der Lock ist beiseitegelegt und weg. **Mein eigener
Commit fb7921bd war der zweite Betroffene desselben Locks — ich habe den Befund am eigenen Leib,
nicht nur auf dem Papier.**

**Beide vorgeschlagenen Formen haben allein eine Fehlrichtung:**

```text
FORM A allein (Halter-Kommando pruefen):
  Auf dieser Maschine ist der Halter IMMER die VM — auch waehrend ein git AKTIV
  arbeitet. Form A allein erklaerte damit JEDEN gehaltenen Lock fuer verwaist:
  der heutige Fehler, gespiegelt. Beiseitelegen mitten im fremden Commit.

FORM B allein (laeuft irgendein git-Prozess):
  Auf einer Maschine mit mehreren parallelen Rollen ist 'irgendwo laeuft git'
  haeufig — dauerhaft falsches GEHALTEN droht. Und ob ein git IN der Sandbox-VM
  im Host-ps ueberhaupt sichtbar ist, ist UNGEMESSEN — dieselbe Klasse
  ungeprüfter Zuordnung, aus der dieser Befund stammt.
```

**ENTSCHIEDEN — verwaist braucht DREI Nein zusammen, sonst gilt die heutige Vorsicht:**

```text
1  Halter-Kommando ist kein git          (Form A)
2  kein git-Prozess sichtbar             (Form B, billig zuerst)
3  die bestehende A-02-Grenze haelt      (0 Byte UND Alter >= 60s)
   -> sie deckt genau den Fall, den weder A noch B sehen KANN: ein lebendiges
      git haelt den Lock nur Sekunden; was nach 60 Sekunden noch haelt und
      nicht sichtbar ist, ist kein arbeitendes git.

ALLE DREI nein  -> beiseitelegen nach Dauerregel (NIE loeschen, Zielpfad in
                   der Meldung), Commit laeuft weiter
SONST           -> heutige Form: ENV_BLOCKED mit Halter-Angabe, exit 3
```

**Rot-Pflicht der Nachbesserung, heute wirksam:** ein 0-Byte-Lock, 61 s alt, ohne git-Halter →
das Tor sagt heute `exit 3` (gemessen, zweimal). Soll: beiseitelegen und weiterfahren.
**Gegenprobe:** ein frischer Lock (< 60 s) bleibt liegen → `ENV_BLOCKED` wie heute.

**Klassifizierung nach §12.5:** A-02 bleibt `ABGENOMMEN`/`RELEASE_FREI` — der SPEC-Befund wirkt
nicht rückwirkend. Klasse SPEC · Schwere P0 · Verursacher-Rolle Planner (Kriterienformulierung,
von ihm selbst angezeigt) · **Nachbesserung auf der Linie des Baus (§12.2): auf `6953198a`**,
Bau durch den Generator, **keine Warteschlange** (P0-Begründung ist gemessen, nicht behauptet).
Der Handgriff des Planners — Lock beiseitegelegt entgegen dem eigenen Werkzeug — war durch Yamas
Dauerregel gedeckt und offengelegt: **gebilligt.**
