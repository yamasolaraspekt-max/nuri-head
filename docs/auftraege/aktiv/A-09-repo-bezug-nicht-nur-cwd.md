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

---

## §11-Bericht des Generators (10.08., CODE_FERTIG)

```yaml
auftrag: A-09
basis: d836fb91          # HEAD bei Uebernahme; basis_sha 5a54b004 ist Vorfahr, Scope-Drift 5a54b004..d836fb91 auf beide Skripte: nur die A-07/A-08-Bauten, exakt der erwartete Stand
commit: 12ca3798         # Bau (commit-pruefen.sh + commitPruefen.test.mjs); IN_ARBEIT-Tor: d6846f69
scope:
  - scripts/commit-pruefen.sh                    # NUR die repo_git_laeuft-Zone (Diff-Hunks @@68 und @@107 — Commit-Aufruf-Zone und Botschaft-Annahme unberuehrt)
  - scripts/__tests__/commitPruefen.test.mjs     # Import-Zeile (+relative) und der neue A-09-Block
  - docs/auftraege/aktiv/A-09-repo-bezug-nicht-nur-cwd.md   # dieser Bericht
  - docs/STATUS.md                               # IN_ARBEIT/CODE_FERTIG-Tore
tests:
  statisch: pass         # bash -n exit 0; node --check exit 0
  unit: "50/50"          # Basis d836fb91: 42/42; Bau: 50/50 (node --test, selbst gefahren, zweimal: nach dem Bau und nach den Mutationsproben)
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar
  browser: nicht_anwendbar
abweichungen:
  - "A-09-4 war an der Basis BEREITS ERFUELLT: der Planner hat die Kantenzeile schon beim Schnitt
     des Blattes richtiggestellt (48ca0099, 08.08. 14:16 'die schadhafte Kantenzeile richtiggestellt').
     Selbst verifiziert am A-08-NACHTRAG Z.266-270: 'git-Prozess auf einem FREMDEN REPOSITORIUM ->
     irrelevant' + Vermerk 'hier stand: in einem FREMDEN VERZEICHNIS … verwechselt fremde cwd mit
     fremdem Repo … 23b3a490, Probe C … laeuft als A-09'. Jede Anforderung des Kriteriums (Wortlaut
     UND Verweis auf den verursachten Befund) steht woertlich da — eine zweite Aenderung waere
     Doppelarbeit ohne Delta, deshalb fasst dieser Bau die Datei NICHT an. Der Evaluator moege das
     an der Fundstelle nachlesen statt einem Diff zu suchen, den es bewusst nicht gibt."
  - "Bauform ueber den Blatt-Wortlaut hinaus, gleiche Richtung: zusaetzlich zu --git-dir=<p> werden
     auch die ZWEITEILIGEN Formen --git-dir <p> und --work-tree <p> gelesen (git akzeptiert beide;
     nur die =-Form zu lesen waere dieselbe Luecken-Klasse in gruen). Erkennung wird dadurch nur
     BREITER, nie enger — die Richtung, die das Entdeckungssignal des Blattes erlaubt."
  - "Benannte Parser-Grenze (im Skriptkopf dokumentiert): die ps-Ausgabe traegt keine Anfuehrungs-
     zeichen — Pfade MIT LEERZEICHEN in Aufrufform/Umgebung sind nicht rueckgewinnbar und dort
     nicht erkennbar. Der cwd-Weg (lsof) bleibt davon unberuehrt. Das Blatt fordert keine
     Leerzeichen-Behandlung; verschwiegen wird die Grenze nicht."
  - "Fremde-Nutzer-Grenze wie im Blatt: KEIN Bau. Am Verhalten gemessen faengt sie heute der
     BESTEHENDE Zweifelspfad: fuer fremde Nutzer liefert schon lsof -d cwd keine Auskunft ->
     Kandidat existiert, cwd unbekannt -> im Zweifel gehalten. Die Weg-3-Luecke (ps -E liest fremde
     Umgebungen nicht) bleibt offen und ist im Skriptkommentar dokumentiert."
  - "HEAD wanderte waehrend des Baus (d836fb91 -> fec3a07a: A-12-Schnitt, W-01/1-IN_ARBEIT samt
     Ruecknahme, Evaluator-Notiz) — Scope-Drift auf scripts/ selbst gemessen: 0 Commits. Der
     parallele Release-Pruefer hielt zeitweise UNCOMMITTETE STATUS.md-Aenderungen im Arbeitsbaum;
     alle meine Tor-Commits stagen deshalb ausschliesslich eigene Pfade, der STATUS-Commit erst
     nach content-diff nur-eigener-Zeilen."
offene_akzeptanz: []
```

### Kriterienstand, je mit Beleg

```text
A-09-1  ERFUELLT   Rot an der Basis SELBST 2x gemessen (Wegwerf-Repo, Skript von HEAD d836fb91):
                   ( sleep 30 | git --git-dir=<repo>/.git cat-file --batch ) & bei fremder cwd,
                   Prozess nachweislich lebend (ps args= zeigte die volle Aufrufform), 0-Byte-Lock
                   302 s -> BEISEITE, Commit lief, exit 0. Am Bau: exit 3, Lock liegt, ENV_BLOCKED
                   (Zusage 'A-09-1' + Gegenprobe im selben Lauf: nach Prozessende exit 0).
A-09-2  ERHALTEN   must_preserve: 'A-09-2 KONTROLLE' (git -C, an Basis UND Bau gruen — Weg 1 traegt).
A-09-3  ERHALTEN   must_preserve: 'A-09-3 KONTROLLE' — echtes zweites Wegwerf-Repo, git-Prozess
                   lebt, --git-dir zeigt DORTHIN -> zaehlt nicht, Commit laeuft (Basis UND Bau gruen).
A-09-4  ERFUELLT   an der Basis durch 48ca0099 (s. Abweichung 1) — verifiziert, kein neuer Diff.
A-09-5  ERFUELLT   SECHS Mutationen einzeln eingespielt, ALLE gefangen (Grün 50/50 vorher, je Probe
                   Rot, md5 fd351a78 vor und nach JEDER Probe byte-identisch, Grün 50/50 danach):
                     M1 Aufrufform-Schleife leer            -> 3 Zusagen rot
                     M2 Treffer-return neutralisiert (3 St.) -> 3 Zusagen rot
                     M3 Aufloesung durch Rohvergleich ersetzt-> 5 Zusagen rot
                     M4 cwd-Zweifel als kein-Repo-git       -> 1 Zusage rot ('A-09 Zweifel')
                     M5 Umgebungs-Schleife leer             -> 2 Zusagen rot
                     M6 ps ohne -E                          -> 2 Zusagen rot
A-09-6  ERFUELLT   Rot an der Basis SELBST 2x gemessen: GIT_DIR=<repo>/.git in der UMGEBUNG,
                   fremde cwd, Variable am lebenden Prozess per ps -E -p <pid> -o command=
                   nachgewiesen -> BEISEITE, Commit lief, exit 0. Am Bau: exit 3, Lock liegt
                   ('A-09-6' + Gegenprobe; 'A-09-6 GIT_WORK_TREE' deckt die zweite Kantenzeile).
```

### Rohausgaben (Kurzform; Befehle reproduzierbar)

```text
Suite Basis   node --test scripts/__tests__/commitPruefen.test.mjs @ d836fb91   tests 42 pass 42 fail 0
Suite Bau     dito @ 12ca3798 (zweimal gefahren)                                tests 50 pass 50 fail 0
Neue Zusagen  gegen BASIS-Skript (HEAD:scripts/commit-pruefen.sh, Struktur nachgestellt):
              8 A-09-Zusagen -> pass 3 / fail 5 — rot genau die fuenf Neu-Verhalten
              (A-09-1, Aufloesung, --work-tree, A-09-6, GIT_WORK_TREE); gruen genau die drei
              deklarierten Kontrollen (A-09-2, A-09-3, Zweifel).
Mutationsprobe md5 original fd351a78f23b9c52b433f313e8ccbaee; je Mutation: Fundstellen-Zaehlung,
              node --test --test-name-pattern A-09, Wiederherstellung md5-identisch; M1 fail 3 ·
              M2 fail 3 · M3 fail 5 · M4 fail 1 · M5 fail 2 · M6 fail 2.
Erstnutzer    dieser Bericht und beide Tor-Commits liefen bereits DURCH das geaenderte Tor
              (d6846f69 mit altem, 12ca3798 mit neuem repo_git_laeuft) — keine Aussperrung.
```

**Ich nehme NICHT ab.** Ball beim Evaluator.

---

## Evaluator-Votum A-09 (10.08.2026)

```yaml
auftrag: A-09
commit: af8f2054          # Pruef-SHA; Bau 12ca3798 — scripts/ zwischen 12ca3798, af8f2054 und
                          # HEAD ded32c75 content-identisch (git diff --stat je 0 Zeilen)
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "A-09-3-Gegenrichtung selbst gefahren: echtes zweites Wegwerf-Repo pr-fremd, lebender
             git-Prozess mit --git-dir DORTHIN bei fremder cwd -> das NEUE Tor legte den alten
             0-Byte-Lock beiseite und der Commit lief (exit 0) — 'jeder git zaehlt' ist damit
             widerlegt. Dazu Probe C und Probe D je in BEIDEN Richtungen: neues Tor exit 3 /
             Basis-Tor beiseite+Commit, am selben lebenden Prozess."
browser: nicht_anwendbar
befunde: []
```

**Messumgebung:** Worktrees (`git worktree add -q`) an `af8f2054` (Prüf-SHA) und `12ca3798^`
(= `fec3a07a`); Scope-Drift `d836fb91..fec3a07a` auf `scripts/` selbst gemessen: **0 Commits,
0 Zeilen** — die Generator-Basis d836fb91 und der Bau-Elter sind für den Scope derselbe Stand.
Torläufe ausschließlich in Wegwerf-Repos unter dem Scratchpad, nie am echten Baum.

### Je Kriterium, mit Rohausgabe

**Suite (Wächter-Baseline, selbst gefahren):** Basis `12ca3798^`: `tests 42 pass 42 fail 0` ·
Prüf-SHA `af8f2054`: `tests 50 pass 50 fail 0`. Namensabgleich (tap, `comm`): **exakt 8 neue
A-09-Zusagen, 0 Bestandszusagen weggefallen** — alle 42 Bestandszusagen (W-09/W-04/Tor/A-02/A-08/
A-07) laufen namensgleich im 50er-Lauf. Tests gewachsen, nie geschrumpft.

**A-09-1 ERFÜLLT (Zwei-Richtungs-Probe C):** lebender Prozess
`git --git-dir=<pr-c>/.git cat-file --batch` (PID 15626, per `ps -o args=` belegt), cwd fremd,
0-Byte-Lock 300 s:

```text
NEUES Tor   LOCK BEI LAUFENDEM GIT .git/index.lock — 0 Byte, 300s alt, kein Halter
            ENV_BLOCKED: git-prozess dieses repos laeuft — exit=3, LOCK LIEGT NOCH
BASIS-Tor   (derselbe Prozess lebt, PID 15626)
            BEISEITE .git/index.lock (0 Byte, 300s alt, kein Halter) — Commit 01dd4f5, exit=0
```

**A-09-2 ERHALTEN:** lebender `git -C <pr-c> cat-file --batch` (PID 15890) → neues Tor `exit=3`,
Lock liegt. In der Suite trägt `A-09-2 KONTROLLE` denselben Fall; an der Basis grün über Weg 1
(cwd) — der neue Weg tritt daneben, nicht an die Stelle.

**A-09-3 ERHALTEN (die Gegenrichtung, eigener Gegen-Beweis):** lebender
`git --git-dir=<pr-fremd>/.git cat-file --batch` (PID 15965, zweites echtes Wegwerf-Repo) →
neues Tor: `BEISEITE … Commit 4565feb`, `exit=0`, der fremde Prozess lebte danach nachweislich
noch. Ohne dieses Kriterium wäre die in `d4308d35` verworfene Form B unbemerkt zurückgekommen.

**A-09-4 ERFÜLLT AN DER BASIS (Abweichung des Generators nachgeprüft, zutreffend):**
`git show 48ca0099` (planner, 08.08. 14:16) trägt exakt den Tausch
`-git-Prozess in einem FREMDEN Verzeichnis` → `+git-Prozess auf einem FREMDEN REPOSITORIUM`
samt Vermerk `⚠ RICHTIGGESTELLT 08.08. … 23b3a490, Probe C … Klasse SPEC, Verursacher Planner,
laeuft als A-09`. An der heutigen Fundstelle (A-08-NACHTRAG, Kantenblock) selbst gelesen: Wortlaut
UND Befund-Verweis stehen da. **Kein Doppel-Diff war die richtige Entscheidung** — ein zweiter
Diff hätte kein Delta getragen.

**A-09-5 ERFÜLLT (zwei der sechs Mutationen selbst gesetzt, §12.4):** Original-md5 selbst
gemessen: `fd351a78f23b9c52b433f313e8ccbaee` (deckt die Generator-Angabe).

```text
M3 Pfadvergleich ohne Aufloesung (Rohvergleich statt pwd -P)   -> tests 50 pass 45 fail 5
   rot: A-09-1 · Aufloesung · --work-tree · A-09-6 · GIT_WORK_TREE   (Generator: fail 5 ✓)
M6 ps ohne -E                                                  -> tests 50 pass 48 fail 2
   rot: A-09-6 · A-09-6 GIT_WORK_TREE                                (Generator: fail 2 ✓)
Wiederherstellung nach JEDER Probe: md5 fd351a78… byte-identisch, Suite danach 50/50.
```

**A-09-6 ERFÜLLT (Zwei-Richtungs-Probe D):** lebender Prozess `git hash-object --stdin`
(PID 15755), `GIT_DIR=<pr-c>/.git` **nur** in der Umgebung — `ps -o args=` zeigt kein GIT_DIR,
`ps -E -p 15755 -o command=` zeigt `GIT_DIR=/…/pr-c/.git` (selbst gemessen):

```text
NEUES Tor   ENV_BLOCKED: git-prozess dieses repos laeuft — exit=3, LOCK LIEGT NOCH
BASIS-Tor   BEISEITE … Commit 5027789, exit=0   (derselbe lebende Prozess)
```

**Pfadauflösung (Mutations-Gegenstück am lebenden Tor):** `git --git-dir=pr-c/.git` **relativ**
aus fremder cwd (PID 16197) → neues Tor `exit=3`, Lock liegt. Zusammen mit M3 ist die Auflösung
in beide Richtungen belegt: vorhanden = gefangen, entfernt = Suite rot.

### Grenzen gegengelesen

- **Fremde Nutzer:** ehrlich benannt und **konservativ** — `lsof -d cwd` liefert für fremde
  Prozesse nichts, der Zweifelspfad (`GCWD` leer + Prozess existiert → gehalten, Z.147–151)
  fängt sie Richtung Blockade, nie Richtung Beiseitelegen. Deckt sich mit der Nicht-Ziel-Messung
  des Blattes (root-Probe 0 Treffer).
- **Leerzeichen-Pfade in ps:** ehrlich benannt (Skriptkopf + §11-Abweichung), aber diese Grenze
  wirkt in Richtung **Übersehen** (Weg 2/3 können einen Repo-Pfad mit Leerzeichen nicht
  rückgewinnen; Weg 1/cwd unberührt). Das Blatt fordert keine Behandlung — **Randnotiz, kein
  Befund**: dieses Repo liegt auf leerzeichenfreiem Pfad; sollte je ein Arbeitsbaum mit
  Leerzeichen entstehen, gehört die Grenze neu bewertet (Planner-Wissen, kein Auftrag).

### Wächter / must_preserve

A-08-Kette (0-Byte-Schranke) und A-07-Angleichung unberührt: die Bestandszusagen A-02-*/A-08-*/
A-07-* laufen alle 42 namensgleich grün; die Torläufe oben zeigen `INDEX ANGEGLICHEN` nach jedem
erfolgreichen Commit. **Realtest Erstnutzer:** dieses Votum und der STATUS-Commit gehen selbst
durch das geänderte Tor. Rückweg unverändert: Skriptänderung ohne Datenmigration, `git revert
12ca3798` genügt.

**Gesamturteil: ABGENOMMEN an `af8f2054`.** Ball beim Release-Prüfer.
