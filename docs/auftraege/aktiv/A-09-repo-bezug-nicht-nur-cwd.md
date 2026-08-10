# A-09 — „git-Prozess dieses Repos" darf nicht an der cwd allein haengen

```yaml
auftrag: A-09
titel: "Commit-Tor: Repo-Bezug eines git-Prozesses auch ueber --git-dir erkennen, nicht nur ueber die cwd"
spur: A                            # Werkzeug am Commit-Weg
heimat_app: ticket
status: ENTWURF                    # der Plan-Pruefer entscheidet ueber BEREIT
status_steht_in: docs/STATUS.md    # §16: EINE Statuswahrheit. Hier steht keine zweite.
prioritaet: P2
basis_sha: 5a54b004   # (a) nachgetragen 10.08. — der A-08-Bau. Erst ab hier EXISTIERT
                      # repo_git_laeuft(); vorher ist die Rot-Lage A-09-1 nicht messbar,
                      # weil die Funktion fehlt. Fundstelle der dynamischen Probe C: 23b3a490.
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
3  seine UMGEBUNG nennt dieses Repo:    GIT_DIR=<…>  ODER  GIT_WORK_TREE=<…>
   gelesen mit `ps -E -p <pid> -o command=`               (NEU, 10.08.)
4  der Bezug ist nicht feststellbar  ->  im Zweifel GEHALTEN
```

Pfadvergleich **nach Auflösung** (`--git-dir=.git` aus dem Repo heraus meint dasselbe wie der
absolute Pfad), damit die Prüfung nicht an der Schreibweise scheitert. **Für Bedingung 3 gilt
derselbe Vergleich** — Probe D hat den Pfad aus `GIT_DIR` absolut aufgelöst und identisch zum
Repo-`.git` gemessen.

> ### ⚠ KORRIGIERT 10.08. — mein `GIT_DIR`-Nicht-Ziel ruhte auf einer widerlegten Begründung
>
> **Hier stand:** *„Nicht-Ziel: die Umgebungsvariable `GIT_DIR`. Sie kann denselben Effekt haben,
> ist aber in der Umgebung eines fremden Prozesses auf macOS nicht verlässlich lesbar. Ein
> Kriterium, das man nicht messen kann, gehört nicht ins Blatt."*
>
> **Probe D des Evaluators (`fc64f05e`) hat beide Halbsätze geprüft. Der erste hält, der zweite
> nicht:**
>
> ```text
> Effekt      ( sleep 40 | GIT_DIR=<repo>/.git git hash-object --stdin ) &   cwd fremd
>             Lock 0 Byte, 242 s  ->  BEISEITE, Commit lief
>             = derselbe Effekt wie Probe C. Der erste Halbsatz ist bestaetigt.
>
> Lesbarkeit  ps -p <pid> -o command=      -> kein GIT_DIR  (steht in der Umgebung)
>             ps -E -p <pid> -o command=   -> GIT_DIR=/…/pr9/.git
>                                             GIT_WORK_TREE=/…/pr9
>             Pfad aufgeloest              -> identisch mit dem Repo-.git
>             = LESBAR. Der zweite Halbsatz traegt nicht.
>
> echte Grenze  ps -E auf root-Prozess (PID 1)  -> 0 Treffer (fremder NUTZER)
>               ps -E auf eigenen Prozess       -> lesbar
>               alle Rollen dieses Repos laufen als yamanuri (gemessen)
> ```
>
> **„Nicht verlässlich lesbar" stimmt nutzerübergreifend und stimmt nicht für den Fall, um den es
> hier geht:** gleicher Nutzer, gleiche Maschine.
>
> **Meine Wahl von zwei zulässigen: AUFNEHMEN, nicht ehrlicher begründen.** Der Plan-Prüfer hat
> beide Wege freigegeben; dies ist die Begründung für diesen:
>
> ```text
> derselbe Effekt      -> es ist DIESELBE Luecke, die A-09 schliessen soll, nur ein anderer Weg hinein
> dasselbe Werkzeug    -> `ps`, das das Blatt ohnehin benutzt; nur `-E` kommt hinzu
> derselbe Vergleich   -> Pfad absolut aufloesbar, die DECISION-Regel gilt unveraendert
> realer Fall gedeckt  -> alle Rollen laufen als derselbe Nutzer
> ```
>
> *Ein Nicht-Ziel wäre formal zulässig gewesen. Aber „messbar, Werkzeug vorhanden, Zuwachs klein,
> gleiche Lücke — und wir lassen sie offen" ist genau die Bauweise, aus der A-02s `P0` entstand: eine
> Lücke, die man kennt und stehen lässt, kommt als Befund zurück. **In einem Blatt, das die Klasse
> „Zuordnung annehmen statt messen" behandelt, wäre das die Wiederholung des Fehlers im eigenen
> Nicht-Ziel-Block.***

## Nicht-Ziele

- **Keine Aenderung an den Bedingungen 1 und 3** und keine an A-08s 0-Byte-Schranke.
- **Kein Rueckwirken auf A-08.** Es bleibt `ABGENOMMEN` (§12.5).
- **Kein maschinenweites „irgendwo laeuft git"** — das war Form B allein und ist in `d4308d35`
  verworfen.
- **Git-Prozesse eines FREMDEN Nutzers.** `ps -E` liest deren Umgebung nicht (an `PID 1` gemessen:
  0 Treffer). *Das ist die Grenze, die es wirklich gibt — gemessen, nicht vermutet. Sie ist
  hinnehmbar, weil alle Rollen dieses Repos als derselbe Nutzer laufen; ein fremder git-Prozess auf
  diesem Arbeitsbaum wäre ein eigenes Problem. **Die Lücke bleibt offen und ist hier dokumentiert,
  statt hinter „nicht messbar" zu verschwinden.***

## Scope

```text
scripts/commit-pruefen.sh                  repo_git_laeuft(): Aufrufform mitlesen (args statt nur comm)
scripts/__tests__/commitPruefen.test.mjs   die Zusagen
docs/auftraege/aktiv/A-08-NACHTRAG-…md     die schadhafte Kantenzeile richtigstellen (A-09-4)
```

## Wiederverwendungsprüfung (§5) — (d) nachgetragen 10.08.

*Die Inhalte standen schon im Ist-Zustand; hier sind sie als eigener Block benannt, damit die
Prüfung nicht aus dem Fließtext herausgelesen werden muss.*

```text
repo_git_laeuft()          VORHANDEN (A-08-Bau, Z.73-106) — wird GESCHAERFT, nicht ersetzt.
                           Kandidatensuche und Schleife bleiben; nur die Bezugsfrage kommt hinzu.
lsof -a -p <pid> -d cwd    VORHANDEN (Z.81-85) — bleibt unveraendert der erste Weg (A-09-2).
                           Inklusive der perl-Zeitgrenze aus A-02-6, die weiter gilt.
ps -o args=                BORDMITTEL, im Tor bisher NICHT genutzt (es liest nur `comm=`).
                           Kein neues Werkzeug — dieselbe `ps`-Familie, anderes Feld.
ps -E -p <pid> -o command= DASSELBE Werkzeug mit einem Schalter mehr; liest die Umgebung
                           (GIT_DIR/GIT_WORK_TREE). Vom Evaluator in Probe D bereits
                           gefahren und belegt — kein unerprobter Bestandteil.
commitPruefen.test.mjs     VORHANDEN UND IN GEBRAUCH — 38 Zusagen nach A-08, wird erweitert.
Pfad-Aufloesung            KEIN vorhandener Baustein im Tor. Bordmittel-Weg (`cd … && pwd -P`
                           oder Vergleich gegen `git rev-parse --git-dir`) statt neuer Helfer.
docs/_playground-archiv/   nichts Vergleichbares.
```

**Kein neuer Baustein, keine neue Abhaengigkeit.** *A-09 ist eine Verfeinerung einer Funktion, die
A-08 gebaut hat — genau der Fall, fuer den §12.5 den Folgeauftrag vorsieht.*

## Auswirkungen (§5) — (b) nachgetragen 10.08.

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle     KEINE
Produktivcode    scripts/commit-pruefen.sh (Z.73-106) + scripts/__tests__/commitPruefen.test.mjs
Testdaten-Ziel   KEINES
Prozessbindung   ENTFAELLT - kein Serverstart, keine Datenbank; Proben im Wegwerf-Repo
Werkzeuge        node-Testsuite commitPruefen.test.mjs - vorhanden UND in Gebrauch (38 Zusagen)
                 `ps` und `lsof` sind Bordmittel, beide im Tor bereits im Einsatz
```

**Erstnutzer** (§5 — das Tor ist vorhanden, die geänderte Bezugsfrage ist neu): **jede Rolle beim
nächsten Commit, ohne eigenen Aufruf** — wie bei A-08. *Die Wirkung ist nur in einer Richtung
spürbar: es wird eher `ENV_BLOCKED` gemeldet als vorher, nie seltener. Ein zusätzlicher Handgriff
entsteht nicht.*

> ### Dass dieser Block hier nachgetragen wird, ist selbst ein Befund
>
> Der Plan-Prüfer hat es beim Bündeln vermerkt: **dritter Auftrag in Folge, dem der §5-Block beim
> ersten Schnitt fehlt.** Das ist kein Zufall dreimal, sondern eine Lücke in meiner Schnittroutine —
> ich schreibe Anlass, Ist-Zustand, DECISION und Kriterien zuverlässig und den Formblock nicht.
>
> **Es ist derselbe Griff, der bei A-10 saß** (dort war der Block beim ersten Schnitt da, und das
> Blatt hieß deshalb „das sauberste Erstblatt der Gruppe") **und bei A-11 wieder** — die zwei
> Blätter, die ich *nach* dieser Rückmeldung geschnitten habe. Die Reihenfolge der Blätter zeigt den
> Lernvorgang, aber sie entschuldigt A-09 nicht. *Gehört als Muster in die nächste Prozessprüfung,
> nicht als Einzelfall in dieses Blatt.*

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

**A-09-5 (P1, Mutationsprobe):** Mindestens **sechs** Mutationen fallen — Aufrufform-Prüfung entfernt ·
ihr Ergebnis ignoriert · Pfadvergleich ohne Auflösung (relativ vs. absolut) · „nicht feststellbar"
als „kein Repo-git" gewertet · **Umgebungs-Prüfung entfernt** · **`-E` bei `ps` weggelassen** (die
Aufrufform wird dann noch gelesen, die Umgebung nicht — der Fall von Probe D kommt stumm zurück).

**A-09-6 (P1, der Umgebungsweg — NEU 10.08. nach Probe D):** Ein git-Prozess, dessen **Umgebung**
`GIT_DIR` oder `GIT_WORK_TREE` auf dieses Repo setzt, wird als Repo-git **erkannt**, auch bei fremder
`cwd` und ohne `--git-dir` in der Aufrufform -> Lock bleibt liegen, `ENV_BLOCKED`.

> *Rot an der Basis vom Evaluator gemessen (Probe D, `fc64f05e`):
> `( sleep 40 | GIT_DIR=<repo>/.git git hash-object --stdin ) &` mit fremder `cwd`, Lock 0 Byte und
> 242 s alt -> **beiseitegelegt, Commit lief**. Derselbe Effekt wie Probe C, über einen anderen Weg.*

## Kantenliste

```text
--git-dir zeigt auf dieses .git, cwd fremd     -> Repo-git   (der Befund)
-C zeigt auf diesen Baum                       -> Repo-git   (heute schon)
--git-dir relativ (.git) aus dem Repo heraus   -> Repo-git   (Auflösung nötig)
--git-dir zeigt auf ein FREMDES Repo           -> kein Repo-git
weder cwd noch Aufrufform feststellbar         -> im Zweifel GEHALTEN
Halter ist selbst ein git-Prozess              -> Bedingung 1 greift schon vorher
GIT_DIR zeigt auf dieses .git, cwd fremd       -> Repo-git   (Probe D, jetzt ERFASST)
GIT_WORK_TREE zeigt auf diesen Baum            -> Repo-git   (dieselbe Zeile in ps -E)
GIT_DIR zeigt auf ein FREMDES Repo             -> kein Repo-git
git-Prozess eines FREMDEN NUTZERS              -> ps -E liest nichts -> Bezug nicht
                                                  feststellbar -> im Zweifel GEHALTEN
                                                  (die verbleibende, dokumentierte Grenze)
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
