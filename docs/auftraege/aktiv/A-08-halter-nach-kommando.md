# A-08 — Die Halter-Frage muss nach dem KOMMANDO fragen, nicht nach der Offenheit

```yaml
auftrag: A-08
titel: "Commit-Tor: unterscheiden, ob ein GIT-Prozess einen Lock haelt - statt ob irgendwer die Datei offen hat"
basis_sha: d377683a
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```

## Anlass — das Tor hat den Planner ausgesperrt

**Kein gesuchter Befund.** Ein Commit lief auf `exit 3`:

```text
GEHALTENER LOCK  .git/index.lock — 0 Byte, 239s alt, Halter: 59792
ENV_BLOCKED: lock wird gehalten
```

**Das Werkzeug hat genau getan, was A-02 verlangt.** *Der Mangel liegt in der Frage, die ich als
Kriterium geschrieben habe — Klasse `SPEC`, Verursacher Planner.*
Befund: [`BEFUND-A02-LSOF-AUF-VIRTUALISIERTEM-MOUNT.md`](../../BEFUND-A02-LSOF-AUF-VIRTUALISIERTEM-MOUNT.md)

## Rot-Beleg, heute wirksam — an `d377683a` gemessen

```text
lsof -t .git/HEAD     -> 59792        lsof -t .git/config  -> 59792
lsof -t README.md     -> 59792        laufende git-Prozesse: 0
ps -p 59792 -o comm=  -> /System/Library/Frameworks/Virtualization.framework/…/XPCService
```

> **Die Virtualisierungsschicht hält den gesamten Mount offen.** *Damit meldet `lsof` für jede
> Datei einen Halter, und A-02s Zweig „kein Halter" ist auf dieser Maschine **unerreichbar**.*
>
> **Wirkung:** jeder verwaiste Lock erzeugt `ENV_BLOCKED` statt Beiseitelegen — **jeder Commit
> jeder Rolle blockiert**, bis jemand von Hand eingreift. *Genau das Handräumen, gegen das die
> Regeln geschrieben sind.*

## Die Ironie, die im Blatt stehen soll

**A-02 wurde geschnitten, um das Raten zu beenden:** statt „es ist wohl Ruhe" sollte das Tor **den
Halter fragen**. *Auf einem virtualisierten Mount lautet die Antwort immer „ja, gehalten" — eine
Frage, die nie „nein" sagen kann, ist keine Prüfung, sondern eine Blockade.*

**`lsof` beantwortet „hat jemand die Datei offen", nicht „arbeitet gerade git daran".**

## Wiederverwendungsprüfung (§5, Fassung 1.2.2)

```text
scripts/commit-pruefen.sh:57-62 + Lock-Block   der Ort - A-02s Bau, wird geschaerft
  darin bereits: perl-alarm-Zeitgrenze (2 s), Auskunft ueber Datei statt Pipe,
                 ENV_BLOCKED exit 3 mit Halter-Angabe   -> alles BLEIBT
scripts/__tests__/commitPruefen.test.mjs        30 vorhandene Zusagen, erweiterbar
ps -p <pid> -o comm=                            Bordmittel, in der Messung oben bereits benutzt
docs/_playground-archiv/                        nichts Vergleichbares
```

## Die Richtung — **dem Plan-Prüfer vorgelegt, nicht vorentschieden**

```text
WEG A   Kommando des Halters pruefen: nur ein Prozess, dessen Kommando `git` ist,
        gilt als Halter.       Genau · aber eine weitere Prozessabfrage je Lock
WEG B   Laeuft ueberhaupt ein git-Prozess? Wenn nein, ist JEDER Lock verwaist.
        Billig und hier eindeutig (0) · aber grob bei parallelen Repos auf derselben Maschine
```

*Beide hätten heute korrekt „verwaist" gesagt. **Meine Neigung ist A**, weil B bei mehreren
Repositories auf derselben Maschine einen fremden `git`-Lauf als Halter dieses Locks zählt — aber
das ist eine Vermutung über die Arbeitsweise, und die gehört gemessen, nicht geglaubt.*

## Akzeptanzkriterien

**A-08-1 (P1):** Hält **kein Prozess mit `git`-Kommando** den Lock, wird er als **verwaist**
behandelt — beiseitegelegt nach der bestehenden Regel, **nie gelöscht**.
*Rot heute: derselbe Fall endet in `ENV_BLOCKED`.*

**A-08-2 (P1, Gegenprobe):** Hält ein **echter `git`-Prozess** den Lock, bleibt es bei
`ENV_BLOCKED` und `exit 3`. *Ohne dieses Kriterium wäre „alles ist verwaist" grün — und das wäre
schlimmer als die Blockade.*

**A-08-3 (`must_preserve`):** **Alle A-02-Zusagen bleiben grün**, insbesondere die Zeitgrenze
(hängendes `lsof` → Abbruch statt Warten) und die `ENV_BLOCKED`-Meldeform mit Exitcode 3 und
Halter-Angabe. *§7: keine Abschwächung bestehender Tests.*

**A-08-4 (P2):** Die Meldung nennt bei `ENV_BLOCKED` **das Kommando** des Halters, nicht nur die
PID. *`Halter: 59792` sagt niemandem etwas; `Halter: 59792 (XPCService)` beendet die Suche sofort.*

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle        KEINE
Produktivcode      scripts/commit-pruefen.sh + scripts/__tests__/commitPruefen.test.mjs
Testdaten-Ziel     KEINES
Prozessbindung     ENTFAELLT - kein Serverstart, keine Datenbank; Proben im Wegwerf-Repo
Werkzeuge          node-Testsuite (30 Zusagen) - vorhanden UND in Gebrauch;
                   `ps` und `lsof` sind Bordmittel, beide oben bereits benutzt
```

**Erstnutzer** (§5 1.2.2 — das Tor ist vorhanden, die geänderte Frage ist neu): **jede Rolle beim
nächsten Commit**, ohne eigenen Aufruf. *Ein zusätzlicher Handgriff wäre die Umgehung, die A-02
verhindern sollte.*

## Rückweg

Eine Änderung an einem Skript, `git revert` genügt. **Kein Zustand außerhalb des Repos betroffen.**
