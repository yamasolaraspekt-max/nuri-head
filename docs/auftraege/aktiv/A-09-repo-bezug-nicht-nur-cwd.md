# A-09 — „git-Prozess dieses Repos" darf nicht an der cwd allein haengen

```yaml
auftrag: A-09
titel: "Commit-Tor: Repo-Bezug eines git-Prozesses auch ueber --git-dir erkennen, nicht nur ueber die cwd"
spur: A                            # Werkzeug am Commit-Weg
heimat_app: ticket
status: ENTWURF                    # der Plan-Pruefer entscheidet ueber BEREIT
status_steht_in: docs/STATUS.md    # §16: EINE Statuswahrheit. Hier steht keine zweite.
prioritaet: P2
art: Folgeauftrag zu A-08 nach §12.5 — A-08 bleibt ABGENOMMEN, der Befund wirkt nicht rueckwirkend
anlass: 23b3a490 (A-08-Abnahme, Probe C des Evaluators)
verursacher: planner
claim: "planner 08.08. 14:1x — Claim VOR dem Schnitt gesetzt. Kein Claim lag auf dem Befund, kein
        Commit seit 23b3a490 (14:11). Die schadhafte Kantenzeile ist in MEINEM Nachtrag, deshalb
        ziehe ich den Ball statt ihn liegen zu lassen. Zweite Planner-Instanz arbeitet parallel an
        A-04/A-05 — dieses Blatt beruehrt beide nicht."
ballbesitz: plan-pruefer (DoR), danach generator
```

## Anlass — die Abnahme von A-08 war gruen, und der Befund gehoert mir

Der Evaluator hat A-08 an `85b03d23` **abgenommen** und dabei drei eigene Torlaeufe gefahren. Probe C:

```text
Ein git-Prozess DIESES Repos, gestartet mit --git-dir und FREMDER cwd,
wird von repo_git_laeuft() NICHT erkannt.
-> der Lock wurde beiseitegelegt, der Commit lief.
```

**Der Bau ist nicht schuld.** Er folgt der Kantenliste meines Nachtrags **woertlich**:

```text
git-Prozess in einem FREMDEN Verzeichnis   -> irrelevant   (Repo-Bezug, s. o.)
```

**Die Zeile verwechselt „fremde cwd" mit „fremdes Repo".** Das sind nicht dieselben Mengen, und
genau in der Differenz sitzt der Fall. Klasse `SPEC`, Verursacher Planner — der Evaluator hat das
ausdruecklich so eingeordnet und die Abnahme deshalb **nicht** blockiert.

## Ist-Zustand, am Bau gemessen

```text
commit-pruefen.sh:73-78   Kandidaten ueber `ps -axo pid=,comm=`, Basename `git` oder `git-*`
commit-pruefen.sh:80-85   je Kandidat `lsof -a -p <pid> -d cwd` -> Repo-Bezug NUR ueber die cwd
```

**Warum `git -C` heute erkannt wird und `--git-dir` nicht** — das ist der ganze Unterschied:

```text
git -C <pfad> …        wechselt das Arbeitsverzeichnis  -> cwd liegt im Repo  -> ERKANNT
git --git-dir=… …      wechselt es NICHT                -> cwd liegt fremd    -> UEBERSEHEN
```

Die Kandidatenermittlung nutzt `comm=` (nur den Kommandonamen). Die Aufrufform steht in `args=` und
wird heute gar nicht gelesen — deshalb kann `--git-dir` nicht auffallen.

## Wie gross ist der Schaden — ehrlich, nicht dramatisiert

**Klein, und der Evaluator hat den Grund mitgeliefert:** Haelt der uebersehene git-Prozess den Lock
tatsaechlich, meldet `lsof` ihn als Halter und **Bedingung 1 greift** (sein Probe B). Die Luecke
wirkt also nur, wenn ein git-Prozess auf diesem Repo arbeitet und den Lock **nicht** haelt — dann
ist das Beiseitelegen eines 0-Byte-Locks auch weniger folgenreich.

> **Warum es trotzdem ein Blatt bekommt und keine Fussnote:** Eine Kantenzeile, die eine falsche
> Menge beschreibt, wird beim naechsten Bau wieder woertlich befolgt. Genau das ist hier passiert —
> der Generator hat richtig gebaut und ist an meinem Satz gescheitert. Das ist derselbe Weg, auf dem
> A-02s „lsof trennt sie exakt" zum P0 wurde. **Ein falscher Satz in einem freigegebenen Blatt ist
> eine Zeitbombe mit langer Zuendschnur, unabhaengig von seiner Schwere.**

## DECISION — der Repo-Bezug wird an der Aufrufform mitgelesen

**Ein git-Prozess gilt als „auf diesem Repositorium arbeitend", wenn *eines* zutrifft:**

```text
1  seine cwd liegt im Arbeitsbaum                        (heute schon, bleibt)
2  seine Aufrufform nennt dieses Repo:  --git-dir=<…>  ODER  -C <…>  ODER
   --work-tree=<…>, jeweils auf diesen Arbeitsbaum bzw. dieses .git zeigend
3  der Bezug ist nicht feststellbar  ->  im Zweifel GEHALTEN
```

Pfadvergleich **nach Auflösung** (`--git-dir=.git` aus dem Repo heraus meint dasselbe wie der
absolute Pfad), damit die Prüfung nicht an der Schreibweise scheitert.

**Nicht-Ziel: die Umgebungsvariable `GIT_DIR`.** Sie kann denselben Effekt haben, ist aber in der
Umgebung eines **fremden** Prozesses auf macOS nicht verlaesslich lesbar. *Ein Kriterium, das man
nicht messen kann, gehoert nicht ins Blatt — das war der Fehler von A-02. Die Grenze wird benannt,
nicht verschwiegen.*

## Nicht-Ziele

- **Keine Aenderung an den Bedingungen 1 und 3** und keine an A-08s 0-Byte-Schranke.
- **Kein Rueckwirken auf A-08.** Es bleibt `ABGENOMMEN` (§12.5).
- **Kein maschinenweites „irgendwo laeuft git"** — das war Form B allein und ist in `d4308d35`
  verworfen.
- **Kein `GIT_DIR`-Kriterium**, siehe oben.

## Scope

```text
scripts/commit-pruefen.sh                  repo_git_laeuft(): Aufrufform mitlesen (args statt nur comm)
scripts/__tests__/commitPruefen.test.mjs   die Zusagen
docs/auftraege/aktiv/A-08-NACHTRAG-…md     die schadhafte Kantenzeile richtigstellen (A-09-4)
```

## Akzeptanzkriterien

**Jedes P1 ist an der Basis wirksam rot** — der Plan-Pruefer bestaetigt das vor dem Bau. *Die
Rot-Lage zu A-09-1 hat der Evaluator bereits gefahren (Probe C); die uebrigen nicht ich.*

**A-09-1 (P1, der Befund):** Ein git-Prozess mit `--git-dir` auf dieses Repo und fremder cwd wird
als Repo-git **erkannt** -> Lock bleibt liegen, `ENV_BLOCKED`. *Rot an der Basis: Probe C des
Evaluators, Lock wurde beiseitegelegt und der Commit lief.*

**A-09-2 (`must_preserve`):** Ein git-Prozess mit `-C` auf dieses Repo bleibt erkannt (heute gruen
ueber die cwd) — die neue Prüfung tritt **daneben**, nicht an ihre Stelle.

**A-09-3 (`must_preserve`, die Gegenrichtung):** Ein git-Prozess, der auf ein **anderes**
Repositorium arbeitet — mit fremder cwd *und* fremdem `--git-dir` — zaehlt weiterhin **nicht**.
*Ohne dieses Kriterium waere „jeder git-Prozess zaehlt" gruen, und das ist die in `d4308d35`
verworfene Form B.*

**A-09-4 (P2, Doku):** Die Kantenzeile im A-08-Nachtrag wird richtiggestellt: nicht „fremdes
Verzeichnis", sondern **„fremdes Repositorium"** — mit dem Vermerk, dass die alte Fassung diesen
Befund verursacht hat.

**A-09-5 (P1, Mutationsprobe):** Mindestens **vier** Mutationen fallen — Aufrufform-Prüfung entfernt ·
ihr Ergebnis ignoriert · Pfadvergleich ohne Auflösung (relativ vs. absolut) · „nicht feststellbar"
als „kein Repo-git" gewertet.

## Kantenliste

```text
--git-dir zeigt auf dieses .git, cwd fremd     -> Repo-git   (der Befund)
-C zeigt auf diesen Baum                       -> Repo-git   (heute schon)
--git-dir relativ (.git) aus dem Repo heraus   -> Repo-git   (Auflösung nötig)
--git-dir zeigt auf ein FREMDES Repo           -> kein Repo-git
weder cwd noch Aufrufform feststellbar         -> im Zweifel GEHALTEN
Halter ist selbst ein git-Prozess              -> Bedingung 1 greift schon vorher
GIT_DIR in der Umgebung gesetzt                -> AUSDRUECKLICH NICHT ERFASST (Nicht-Ziel)
```

## Rueckweg und Entdeckung

**Rueckweg:** Skriptaenderung ohne Datenmigration, der Commit ist zurueckdrehbar.

**Entdeckung:** Unveraendert das Signal aus A-08 — die Meldung nennt beim Beiseitelegen Groesse,
Alter und Zielpfad. Ein beiseitegelegter Lock **mit Inhalt** heisst: einem laufenden git wurde der
Index weggezogen. Zusaetzlich hier: wird nach dieser Aenderung **haeufiger** `ENV_BLOCKED` gemeldet
als vorher, ist die Aufrufform-Prüfung zu weit geraten (etwa Pfadvergleich per Teilzeichenkette) —
dann zurueck an den Planner.

```yaml
fehlerklasse: SPEC
verursacher: planner
prioritaet: P2
warteschlange: ja — nach A-07 (P0-Rest) und ohne Eile; A-08 ist abgenommen und nicht betroffen
```
