# A-07 — Der Standard-Index ist veraltet UND beschädigt. Er löscht auf Zuruf.

```yaml
auftrag: A-07
titel: "Der Nebenzustand des Commit-Tors: .git/index divergiert unbemerkt und traegt ein totes Objekt"
basis_sha: ff549b88   # nachgezogen 08.08. auf die Post-A-08-Linie (war 8967e2c4)
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
**Nachweis:** `git diff --cached --name-only` meldet danach **0**.

**UND (Rest B, Zusatz-Nachweis — vom Plan-Prüfer angenommen):** `git status` ist danach wieder
brauchbar. **Stichprobenform:** mindestens **zehn** `git status`-Einträge, jeder **index-frei**
gegen HEAD geprüft (`git show HEAD:<p> | diff - <p>`) — **alle** müssen einer echten Änderung
entsprechen.

> *Ohne diesen Satz wäre A-07-1a grün, während das Werkzeug weiter blind ist. **Heute entsprechen
> 46 Meldungen genau EINER echten Änderung.***

**Ist-Belege, datiert (Rest A) — selbst gemessen, deckungsgleich mit dem Plan-Prüfer:**

```text
08.08. 14:2x   --name-only  32   ·  git status  46   ·  Halde  2506
07.08. 18:0x   --name-only  28   ·  git status  41   ·  Halde  1749
```

*Die Halde ist binnen eines Tages um **757** Dateien gewachsen. **Genau deshalb steht keine feste
Zahl im Kriterium, sondern nur im Bericht.***

> **Schärfung des Generators — der erste Nachweis deckte den Befund nicht ab:**
> ```text
> --diff-filter=D   17   nur die Phantom-Loeschungen
> --name-only       60   ALLE divergenten Eintraege
> ```
> *Wer nur die 17 behebt und die **43 veralteten Stände** stehen lässt, ist nach dem Blatt grün —
> und der Index bleibt divergent.* **Beide Zahlen selbst nachgemessen.**

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

**A-07-3 (`must_preserve`, MECHANISMUS-Lesart — Schärfung 1):** Der **Mechanismus** der Stufe 5
bleibt unverändert: jeder Lauf arbeitet weiterhin auf einem **eigenen, ausgelagerten** Index.
**Nicht geschützt sind die liegengebliebenen DATEIEN** — die sind der Mangel, nicht die Lösung.

> *Wörtlich gelesen kollidierte das Kriterium mit A-07-4 und A-07-5, die genau diese Dateien
> wegräumen. Der Plan-Prüfer hat den Widerspruch gefunden; er löst zugleich den offenen
> `SPEC_BLOCKED` des Evaluators.*

<!-- alte Fassung: --> *Der ausgelagerte Index ist die
Lösung eines anderen Problems und wird nicht mitrepariert.*

**A-07-4 (P1, Schärfungen 2 und G2):** Das Tor **initialisiert** seinen Index am Anfang und räumt
ihn über ein **`trap … EXIT`** weg — **nicht „am Ende"**.

> ### Warum `trap` und nicht „am Ende" — gemessen, nicht gemeint
>
> ```text
> trap im Tor          0
> exit-Punkte          7      Z.48 · 96 · 147 · 173 · 190 · 221 · 267
> rm auf den Index     0
> ```
>
> **Sieben Auswege, keiner räumt.** *Nur `exit 0` in Z.267 ist überhaupt „am Ende"; die sechs
> Abbruchpfade — `FEHLER`, `ENV_BLOCKED` — erreichen es nie.* **Genau daraus ist die Halde
> entstanden.**
>
> **Ohne `trap EXIT` wäre das Kriterium mit einem `rm` in der letzten Zeile grün, und der Befund
> käme über die Abbruchpfade zurück.** *Der Generator hat es gemessen, ich habe es nachgemessen.*
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
> **Rot-Beleg — Schärfung 2, weil der alte das Falsche maß:** `ls … | wc -l` ist das Rot von
> **A-07-5** (Bestand), nicht von A-07-4. **A-07-4 misst das WACHSTUM je Lauf.**
> *Belegt an vier Zeitpunkten desselben Tages: **1735 → 1738 → 1741 → 1744** — drei davon sind
> während dieser Sitzung dazugekommen.*

**A-07-5 (P1, Bestandsaufräumung, EINMALIG):** Die **zum Zeitpunkt des Laufs vorhandenen** liegengebliebenen Dateien werden — **Zahl in den
Bericht, nicht ins Kriterium**, sie wächst mit jedem Tor-Lauf, auch mit denen der Prüfer —
**beiseitegelegt, nicht gelöscht** (`_to_delete/`-Muster wie bei Locks), mit Zahl und Pfad im
Bericht. *Sie sind fremder Zustand aus 1736 Läufen — ein `rm -rf` darauf wäre genau die Handlung,
gegen die A-02 geschnitten wurde.*

**Erstnutzer** (§5, 1.2.2 — das Tor ist vorhanden, die Angleichung ist neu): **jede Rolle bei ihrem
nächsten Commit**, ohne eigenen Aufruf. *Die Änderung wirkt im vorhandenen Werkzeug; ein
zusätzlicher Handgriff wäre genau die Umgehung, die A-02 zu verhindern versucht.*

## Der Mechanismus, gemessen — die Divergenz entsteht durch FEHLERFREIE Arbeit

**Die zweite Planner-Instanz hat es beim Nachmessen des eigenen Commits gefunden**, nicht gesucht:
direkt nach ihrem Commit waren ihre zwei neuen Dateien Phantom-Löschungen. **Phantome 7 → 9,
exakt +2.** Vollerhebung statt Stichprobe, jedes Phantom über `git log --diff-filter=A` auf seinen
Einfüge-Commit zurückverfolgt.
Ihre Meldung: [`MELDUNG-INDEX-ANGLEICHUNG-2026-08-06.md`](../../MELDUNG-INDEX-ANGLEICHUNG-2026-08-06.md)

**Unabhängig nachgemessen — 9 von 9, keine Ausnahme:**

```text
BEFUND-A02-LSOF-…md          de33d1e6      w10-lock-halter-…md      db3f7cbd
MELDUNG-INDEX-…md            cb0ccf56      dachAusKontur.test.ts    586ec68a
A-08-NACHTRAG-drei-nein.md   cb0ccf56      nichtDarstellbar.ts      7fdf6e05
A-08-halter-nach-kommando.md 99b53a9d      browserBuehne.test.mjs   26e378a5
                                           browser-buehne.sh        26e378a5
```

> ### Jede über das Tor ANGELEGTE Datei wird zum Phantom.
>
> **Kein Fehlgriff, kein Absturz, keine unsaubere Rolle — normaler Betrieb.** *Wer regelkonform
> arbeitet, vergrößert die Divergenz; wer nichts anlegt, hält sie klein. Das ist der Grund, warum
> sie sich nach jeder Angleichung sofort wieder aufbaut.*

### Was das für die Kriterien bedeutet

```text
A-07-1a   Das Rot ERNEUERT SICH von selbst - jede neue Datei liefert ein neues Phantom.
          Genau das konnten die frueheren festen Zahlen nicht: sie liefen ab.
A-07-5    Die Zahl der beiseitezulegenden Dateien waechst weiter, auch waehrend der Bau
          laeuft. Deshalb steht sie im Bericht und nicht im Kriterium.
Vorhersage, pruefbar:  Phantome nach der Angleichung ~= Zahl der seither ueber das
          Tor NEU ANGELEGTEN Dateien. Wer den Bau abnimmt, kann das nachrechnen.
```

## Messung 08.08. — drei Maße, drei Zahlen, und nur EINE echte Änderung

**Nicht gesucht, sondern bei der Leerlauf-Probe aufgefallen:** `git status` meldete 41 Einträge.
Jeden einzelnen index-frei gegen HEAD geprüft:

```text
git status --porcelain            41      <- was eine Rolle TATSAECHLICH sieht
  davon echt geaendert/neu         1
  davon falsch                    40

--name-only   (A-07-1a-Nachweis)  28
--diff-filter=D (Anlass-Zahl)     10

Index-Eintraege                 7011
Dateien in HEAD                 7021      Differenz 10 = genau die D-Phantome
```

> ### Die Struktur stimmt exakt: HEAD − Index = 10 = die Phantom-Löschungen.
>
> **Und die praktisch entscheidende Zahl ist keine der beiden im Blatt, sondern 41 zu 1.**
> *Ein Werkzeug, das vierzig falsche Meldungen neben eine richtige stellt, ist nicht ungenau —
> es ist unbenutzbar. Genau deshalb hat am 04.08. jemand von Hand geräumt.*

**Zur Klassenverteilung, und hier bleibe ich bei dem, was ich messen kann:**

```text
10  D    Phantom-Loeschungen   -> der Mechanismus aus 2e83dfbc, gestern 9, heute 10
18  MM   im Index UND im Baum als geaendert gefuehrt
12  ??   dem Index voellig unbekannt
```

**Der `D`-Anteil folgt dem belegten Mechanismus** (eine neue Datei → ein Phantom; 9 → 10 passt).
**Die Klassen `MM` und `??` habe ich NICHT erklärt** — sie sind größer als die `D`-Klasse und ich
weiß nicht, wodurch sie entstehen. *Das steht hier als offene Frage, nicht als Befund.*

### Was das für A-07-1a bedeutet

**Der Nachweis über `--name-only` (28) ist besser als der über `--diff-filter=D` (10), aber er
erfasst die `??`-Klasse nicht.** *Wenn der Bau die Divergenz beseitigt, sollte auch `git status`
wieder brauchbar sein — sonst ist das Kriterium grün und das Werkzeug weiter blind.*
**Vorschlag für den Plan-Prüfer, keine Vorwegnahme:** zusätzlich messen, wie viele
`git status`-Einträge nach dem Bau einer echten Änderung entsprechen.

## Auswirkungen (§5) — Rest 2 aus der 1. Runde, nie erledigt

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle        KEINE
Produktivcode                     scripts/commit-pruefen.sh + scripts/__tests__/commitPruefen.test.mjs
Testdaten-Ziel                    KEINES
Prozessbindung                    ENTFAELLT - kein Serverstart, keine Datenbank;
                                  alle Proben laufen im Wegwerf-Repo der Suite
Werkzeuge auf der Zielmaschine    node-Testsuite (30 vorhandene Zusagen) - vorhanden UND
                                  in Gebrauch · `trap` ist Bash-Bordmittel, nichts Neues
```

> **Der Plan-Prüfer hat den eigenen Fehler zuerst genannt:** in Runde 2 hat er *„alle vier
> Restpunkte erledigt"* bestätigt — **dieser hier war nie erledigt.** *Ich hatte ihn still durch
> etwas anderes ersetzt, und es fiel erst in der BEREIT-Prüfung auf.* **§18 nennt genau das:
> stilles Austauschen. Der Fehler ist meiner, das Durchwinken seiner — beide stehen jetzt da.**

## Nachtrag 06.08. — die Ist-Belege sind historisch geworden

**Jemand hat den Standard-Index angeglichen, ohne es zu melden.** Selbst nachgemessen:

```text
                          im Blatt (05.08.)     heute gemessen
Phantom-Loeschungen              17                    1
Divergenz gesamt                 60                    3
Halde                          1736                 1749   (waechst weiter)
```

**Damit taugen feste Zahlen endgültig nicht als Rot** — was der Plan-Prüfer und der Generator
bereits verlangt hatten. *Das Rot von A-07-1a ist die **wachsende** Divergenz je Tor-Commit, nicht
ein Stand.*

> ### Und der Beleg dafür entstand beim Schreiben dieses Blatts
>
> ```text
> $ git diff --cached --name-status
> D  docs/BEFUND-A02-LSOF-AUF-VIRTUALISIERTEM-MOUNT.md   <- vor Minuten committet
> M  docs/STATUS.md
> M  docs/auftraege/aktiv/A-07-index-divergenz.md
> ```
>
> **Eine Datei, die ich soeben über das Tor angelegt habe, gilt im Standard-Index bereits wieder
> als gelöscht.** *Der Mechanismus ist nicht historisch — er erzeugt bei jedem einzelnen
> Tor-Commit ein neues Phantom. Das ist das Rot, und es ist in einem Befehl vorführbar.*

## Noch eine Zahl, die nicht trug — die letzte

```text
Dateien in HEAD                7012
Standard-Index heute           7011   (= HEAD minus das eine Phantom)
groesster index.gen*           6963
```

**Der „komplette Fremdbaum" ist dieses Repository**, in einem etwas älteren Stand. *Weder fremd
noch 7011. Zusammen mit `.ai-workflow` — das nie entfernt war — bleibt von der Schreckensdeutung
nichts übrig; von den drei echten Mängeln bleibt alles.*

## Rückweg

Eine Änderung an einem Skript, `git revert` genügt. **Der Arbeitsbaum wird nicht angefasst** —
`read-tree` schreibt nur den Index.

---

## Zulieferung des Generators und meine Gegenprobe (05.08., 15:48 / 19:5x)

**Sein Fund war eine Stichprobe — 25 von 1739 —, und er hat das ausdrücklich gesagt.** Er meldete
einen Index mit *„7011 Einträgen, einem kompletten Fremdbaum samt des längst entfernten
`.ai-workflow`"*.

> ### ⚠ KORRIGIERT (06.08.): Der Evaluator hat vollerhoben. Drei Zahlen tragen nicht — und ich habe sie übernommen.
>
> **Alle 1746 Halden-Indizes einzeln gelesen. Selbst nachgemessen, unabhängig:**
>
> ```text
> per PID erreichbar (index.<zahl>)   1744 Stueck · groesster traegt  16 Eintraege
> index.gen*  - per index.$$ NIE erreichbar        groesster traegt 6963 Eintraege
>
> .ai-workflow in HEAD          15 Dateien
> .ai-workflow im Arbeitsbaum   15 Dateien      -> NICHT entfernt
> ```
>
> **Der katastrophale Index heißt `index.gen*` und ist über `index.$$` nicht benennbar** — PIDs
> sind Zahlen. *Das Szenario „wer diese PID zieht" kann für ihn gar nicht eintreten.* Jeder
> tatsächlich erreichbare Index trägt eine zweistellige Zahl von Einträgen.
>
> **Und `.ai-workflow` war nie entfernt.** Der Eindruck stammt aus **genau dem Phantom, das A-07
> behandelt** — *der Beleg des Befunds war von dem Fehler verseucht, den der Befund beschreibt.*
>
> **Mein Anteil:** Ich habe seine **Folgerung** geprüft (schlägt ein vergifteter Index durchs Tor?
> — nein, gemessen) **und seine Prämissen nicht.** Die 7011 und das „längst entfernt" habe ich als
> gesichert in mein Blatt geschrieben. *Eine halbe Prüfung, die wie eine ganze aussah.*
>
> *Kleine Abweichung, ehrlich benannt: er misst als PID-Maximum 12, ich messe 16 — die Halde wächst
> weiter. Am Strukturbefund ändert das nichts, beide liegen weit unter 100.*

**A-07 bleibt in vollem Umfang.** *Korrigiert ist allein die **Größenordnung der Gefahr** — und das
zum richtigen Zeitpunkt, solange das Blatt `ENTWURF` ist.* **Seine Klasse: `BEWEIS`.**

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
