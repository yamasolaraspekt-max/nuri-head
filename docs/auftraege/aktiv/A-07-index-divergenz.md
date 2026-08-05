# A-07 — Der Standard-Index ist veraltet UND beschädigt. Er löscht auf Zuruf.

```yaml
auftrag: A-07
titel: "Der Nebenzustand des Commit-Tors: .git/index divergiert unbemerkt und traegt ein totes Objekt"
basis_sha: 8967e2c4
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```

## Anlass — und ich habe ihn beim ersten Schnitt falsch zugeordnet

**Erster Schnitt (`4169cfec`):** Ich schrieb, der Standard-Index sei *„nicht nur veraltet, sondern
beschädigt"*, weil beim Committen `invalid object 100644 8fd24e1c… for '-f'` erschien.

> ### Das war falsch, und der Evaluator hat es widerlegt. Selbst nachgemessen:
>
> ```text
> 8fd24e1c im Standard-Index        0 Treffer
> Pfad "-f" im Standard-Index       0 Treffer
> git status meldet invalid object  0 mal
>
> das tote Objekt steht 116-fach in den TOR-Indizes unter $TMPDIR/ticket-index
> ```
>
> **Die Meldung kam aus dem Index des Tors, nicht aus `.git/index`.** *Ich hatte den Beleg — der
> Fehler erschien mitten in einem Tor-Aufruf — und habe trotzdem auf die falsche Datei geschlossen.
> Dieselbe Klasse wie beim Prüfbefehl in A-01: das Naheliegende gesehen, die Zuordnung angenommen.*

**A-07-4 war damit zweifach unbrauchbar:** an `.git/index` gemessen **ohne Zutun schon grün**, an
den Tor-Indizes gemessen ein **Angriff auf genau die Dateien, die A-07-3 als `must_preserve`
schützt.** Klasse `SPEC`, Ball war beim Planner — hier ist der Neuschnitt.

## Der wirkliche Befund — größer als der behauptete

**Selbst gemessen, `$TMPDIR/ticket-index`:**

```text
liegengebliebene Tor-Indizes   1736 Dateien, 8,5 MB
aeltester .. neuester          index.28196 .. index.65336
Standard-Index                 6994 Eintraege, 60 divergent, davon 17 Phantom-Loeschungen
                               (der 17. ist das A-07-Blatt selbst - der Anlass-Absatz oben
                                zaehlt den aelteren Evaluator-Stand von 16)
```

**Ursache laut Evaluator, an `commit-pruefen.sh:57-62`:** `GIT_INDEX_FILE=index.$$` wird **nie
initialisiert und nie geräumt**. Das Betriebssystem vergibt PIDs wieder — **ein Lauf erbt bei
wiederverwendeter PID den Index seines Vorgängers.** Daher 116 gleiche kaputte Einträge statt einem.

> **Drei Mängel, nicht einer:**
>
> ```text
> M1  1736 Indexdateien wachsen unbegrenzt und werden nie geraeumt        (8,5 MB)
> M2  PID-Wiederverwendung laesst einen Lauf einen FREMDEN Index erben,
>     ohne ihn zu initialisieren                                          (Korrektheit)
> M3  der Standard-Index divergiert: 17 Phantom-Loeschungen, die ein
>     Commit AM TOR VORBEI ausfuehren wuerde                              (unveraendert echt)
> ```

## Die Gefahr — präzise, nicht dramatisiert

```text
NICHT gefaehrdet   der Arbeitsbaum. Nichts ist verloren, 0 von 16 Dateien fehlen.
GEFAEHRDET         ein `git commit` AM TOR VORBEI. Er wuerde die 16 Loeschungen
                   ausfuehren - inklusive ARBEITSREGELN.md und Produktivcode.
BEREITS EINGETRETEN  git status und git diff HEAD lugen am 04.08. beide (belegt).
                     Seither ist `git show HEAD:<p> | diff - <p>` die einzige
                     verlaessliche Probe - eine Umgehung, keine Loesung.
```

*Es ist kein hypothetisches Risiko: am 04.08. hat ein Vorplanner genau in dieser Lage von Hand
geräumt, weil ihm der Arbeitsbaum unklar erschien.*

## Wiederverwendungsprüfung (§5, 1.2.2)

```text
scripts/commit-pruefen.sh          Stufe 5 setzt GIT_INDEX_FILE - der Ort, an dem es gehoert
scripts/__tests__/commitPruefen.test.mjs   30 vorhandene Zusagen, erweiterbar
git read-tree HEAD                 Bordmittel, schreibt den Index aus HEAD neu, ruehrt
                                   den Arbeitsbaum NICHT an
```

## Die offene Frage ist BEANTWORTET — vom Generator, gegen meine Neigung

**Ich hatte vorgeschlagen:** angleichen nur, wenn im Standard-Index nichts gestaget ist.
**Er hat alle 60 Einträge vermessen:**

```text
43  tragen einen alten Stand   (Blob liegt in der Historie)
17  sind Phantom-Loeschungen
 0  tragen Arbeit, die nirgends gesichert ist
```

> **Damit greift meine Bedingung NIE.** Es sehen permanent 60 Dateien gestaget aus —
> **Weg A wäre in der Praxis Weg B.** *Meine Bedingung war nicht vorsichtig, sie war wirkungslos;
> sie hätte wie ein Schutz ausgesehen und nichts geschützt.*

**Seine messbare Fassung wird übernommen:**

```text
angleichen, solange kein Index-Blob existiert, der in KEINEM Commit vorkommt
```

*Er hat dabei eine eigene Fehlmessung offengelegt: sein erster Durchgang meldete
`handoff-status.md` als nicht-in-der-Historie, weil er auf 40 Commits begrenzt hatte — die Datei
hat 567 Commits, der Blob liegt in `15f51340`. **Ohne den zweiten Durchgang hätte er einen
Phantom-Fund gemeldet, und ich hätte danach geschnitten.***

## Akzeptanzkriterien (Entwurf, abhängig von der Wegentscheidung)

**A-07-1a (P1, der Regelfall):** Existiert **kein** Index-Blob, der in keinem Commit vorkommt,
gleicht das Tor nach erfolgreichem Commit den Standard-Index an HEAD an.
**Nachweis:** `git diff --cached --diff-filter=D` meldet danach **0**.
*Rot an der Basis: heute **17** Phantom-Löschungen (selbst gemessen, 05.08.).*

**A-07-1b (P1, der Kippfall):** Existiert **ein solcher Blob**, lässt das Tor den Index
**unangetastet** und meldet **Zahl und Pfade**.
*Das ODER aus dem ersten Schnitt ist damit aufgelöst — es hätte sich mit jedem der beiden Wege
grün rechnen lassen.*

**A-07-2 (P1, Gegenprobe zu A-07-1b):** Echte gestagete Arbeit wird **nicht** verworfen.

**Herstellung, damit die Probe den echten Index nicht gefährdet** (Form des Plan-Prüfers,
übernommen):

```text
im Wegwerf-Repo, das commitPruefen.test.mjs ohnehin baut:
  1  Datei anlegen, `git add` -> der Blob liegt in der Objektdatenbank,
     aber in KEINEM Commit  = genau die Lage aus A-07-1b
  2  Tor laufen lassen
  3  Zusage: der Index ist UNVERAENDERT, und die Meldung nennt Zahl und Pfad
```

*Ohne dieses Kriterium wäre „Index immer plattmachen" grün — und das wäre schlimmer als der Fehler.*

**A-07-3 (`must_preserve`):** Der ausgelagerte Index (Stufe 5) bleibt unverändert. *Er ist die
Lösung eines anderen Problems und wird nicht mitrepariert.*

**A-07-4 (P1, NEU geschnitten — der alte war unbrauchbar):** Nach einem Tor-Lauf bleibt **kein
verwaister Index** zurück: das Tor räumt seinen eigenen `index.$$` am Ende weg **und**
initialisiert ihn am Anfang, statt einen vorgefundenen zu erben.
**Gegenprobe im selben Test:** ein absichtlich vorgelegter Fremd-Index unter demselben Pfad darf
den Lauf **nicht** beeinflussen.

> ### Rest 1 des Plan-Prüfers ist beantwortet — der Fundort ist belegt
>
> Seine Fundort-Probe auf der Halde **wurde abgebrochen und ist nie gelaufen**. Ich habe sie
> gefahren, alle 1738 Indizes einzeln:
>
> ```text
> for f in $TMPDIR/ticket-index/index.*; do
>   GIT_INDEX_FILE=$f git ls-files --stage | grep -q 8fd24e1c && treffer++
> done
>   durchsucht 1738 · TREFFER 116
>   Eintrag:  100644 8fd24e1c54250c64f06e78ab815c85364af6e3e6 0    -f
> ```
>
> **Seine Zahl 116 ist exakt bestätigt.** Das tote Objekt liegt auf der Stufe-5-Halde, nicht in
> `.git/index` — *und dass 116 Indizes denselben kaputten Eintrag tragen, IST der Beweis für die
> PID-Erbschaft: ein einzelner Fehlgriff hat sich über 116 Läufe fortgepflanzt.*
>
> **Rot-Beleg für A-07-4, heute wirksam:** `ls $TMPDIR/ticket-index | wc -l` → **1738 > 0.**

**A-07-5 (P1, Bestandsaufräumung, EINMALIG):** Die 1736 liegengebliebenen Dateien werden
**beiseitegelegt, nicht gelöscht** (`_to_delete/`-Muster wie bei Locks), mit Zahl und Pfad im
Bericht. *Sie sind fremder Zustand aus 1736 Läufen — ein `rm -rf` darauf wäre genau die Handlung,
gegen die A-02 geschnitten wurde.*

**Erstnutzer** (§5, 1.2.2 — das Tor ist vorhanden, die Angleichung ist neu): **jede Rolle bei ihrem
nächsten Commit**, ohne eigenen Aufruf. *Die Änderung wirkt im vorhandenen Werkzeug; ein
zusätzlicher Handgriff wäre genau die Umgehung, die A-02 zu verhindern versucht.*

## Rückweg

Eine Änderung an einem Skript, `git revert` genügt. **Der Arbeitsbaum wird nicht angefasst** —
`read-tree` schreibt nur den Index.

---

## Zulieferung des Generators und meine Gegenprobe (05.08., 15:48 / 19:5x)

**Sein Fund steht und ist alarmierend.** Stichprobe über **25 von 1739** liegengebliebenen
Tor-Indizes: 24 identisch mit HEAD — *die Erbschaft fällt nicht auf* —, **einer weicht ab und trägt
7011 Einträge, einen kompletten Fremdbaum samt des längst entfernten `.ai-workflow`.**
*Er hat ausdrücklich nicht hochgerechnet: 25 von 1739 sind eine Stichprobe, keine Quote.*

**Seine Folgerung habe ich geprüft statt übernommen:** *„Wer diese PID zieht, committet 7011
Dateien mit."*

### Gemessen im Wegwerf-Repo — beide Richtungen

```text
FALL 1  Index traegt eine FREMDE gestagete Datei, Tor-Form `git commit -- a.txt`
        -> Commit enthaelt NUR a.txt · fremd.txt im Commit-Baum: 0 Treffer

FALL 2  Index traegt eine geerbte LOESCHUNG von b.txt (Datei liegt auf der Platte),
        Tor-Form `git add -- a.txt ; git commit -- a.txt`
        -> b.txt bleibt im Commit-Baum (1 Treffer) UND auf der Platte
```

> ### Die Folgerung trägt nicht — und der Grund verdient einen Namen
>
> **`git commit -- <pfade>` baut den Commit aus HEAD plus den genannten Pfaden. Der Index wird für
> den Inhalt nicht herangezogen.** Deshalb schlägt ein vergifteter Index **in keiner der beiden
> Richtungen** durch.
>
> **Das `-- "$@"` in `commit-pruefen.sh:188` ist der Grund, warum aus 1739 verwaisten Indizes noch
> kein Schaden entstanden ist.** *Es stand nie als Schutzmaßnahme im Blatt; es wirkt seit Monaten,
> und niemand hat es dafür in Anspruch genommen.*

### Was trotzdem echt bleibt — die Schwere sinkt, sie verschwindet nicht

```text
1  ein Commit AM TOR VORBEI zieht den Index heran -> dort ist die Katastrophe moeglich
2  git status und git diff HEAD luegen -> am 04.08. hat genau das zum Handraeumen gefuehrt
3  1739 Dateien, 8,5 MB, unbegrenzt wachsend
4  116 Indizes tragen ein totes Objekt -> `invalid object`-Rauschen bei jedem Treffer
```

**A-07 bleibt in vollem Umfang.** *Geändert hat sich nur, wovor es schützt: nicht vor einem
verseuchten Tor-Commit — den verhindert die Commit-Form —, sondern vor jedem Weg daran vorbei und
vor Diagnosewerkzeugen, die lügen.*

**Und die Zuordnung stimmt diesmal, weil ich sie gemessen habe.** *Beim ersten Schnitt hatte ich
sie geraten und lag falsch. Dieselbe Prüfung auf seine Aussage anzuwenden war das Mindeste.*
