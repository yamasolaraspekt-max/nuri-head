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
brauchbar. **Alle** gemeldeten Einträge müssen einer echten Änderung entsprechen.

**Stichprobenform — KORRIGIERT 10.08. 19:0x, zwei Schritte und der erste ist Pflicht:**

```text
SCHRITT 1  ist der Pfad ueberhaupt getrackt?     git ls-files --error-unmatch <p>
             NICHT getrackt (?? in git status)  ->  zaehlt als ECHT. Fertig, kein Schritt 2.
             getrackt                           ->  Schritt 2
SCHRITT 2  index-freier Inhaltsvergleich         git show "HEAD:<p>" | diff - <p>
             Unterschied  -> echte Aenderung
             identisch    -> Phantom (Befund)
```

> **⚠ Schritt 1 fehlte in meiner ersten Fassung, und das Kriterium war dadurch defekt.** Beim ersten
> Lauf nach dem Bau hat es **einen Fehlalarm gegen einen fehlerfreien Bau** erzeugt — gemeldet in
> `55317e1e`, nachgemessen: `zz-unlink-probe` liegt weder im Index (`ls-files` 0) noch in HEAD
> (`ls-tree` 0), ist also **untracked** und leer.
>
> **Der Konstruktionsfehler:** `git show "HEAD:<p>"` liefert für einen Pfad, der **nicht in HEAD**
> ist, keine Ausgabe. `diff -` gegen eine leere Datei meldet dann *identisch* — und identisch war in
> meiner Fassung die Definition von „Phantom". **Eine untracked Datei ist aber das genaue Gegenteil
> eines Phantoms:** das Phantom ist „in HEAD, im Index als gelöscht"; untracked ist „nirgends
> geführt, auf der Platte vorhanden". Mein Vergleich konnte die beiden nicht unterscheiden, weil er
> nur eine Frage stellte.
>
> **Ergebnis mit der korrigierten Form:** beide verbleibenden `git status`-Einträge sind **echt**
> (zwei untracked Dateien). A-07s Zusatz-Nachweis **besteht**.
>
> *Das ist kein Bau-Mangel, sondern ein Mangel meines Kriteriums — und er hätte einen fehlerfreien
> Bau rot gefärbt. Gefunden wurde er nur, weil vor der Meldung gemessen wurde statt behauptet.*

> *Ohne diesen Zusatz-Nachweis wäre A-07-1a grün, während das Werkzeug weiter blind ist. **Vor dem
> Bau entsprachen 46 Meldungen genau EINER echten Änderung.***

**Ist-Belege, datiert (Rest A) — selbst gemessen, deckungsgleich mit dem Plan-Prüfer:**

```text
10.08. 18:3x   --name-only  35   ·  git status  52   ·  Halde  2546
08.08. 14:2x   --name-only  32   ·  git status  46   ·  Halde  2506
07.08. 18:0x   --name-only  28   ·  git status  41   ·  Halde  1749
```

**MESSORT DER HALDE — bitte genau diesen nehmen** (`commit-pruefen.sh:59-61`):

```text
$TMPDIR/ticket-index/index.<PID>          <- die Halde
ls "${TMPDIR:-/tmp}/ticket-index" | grep -c '^index\.'
```

> *Ich habe beim ersten Griff `ls "$TMPDIR" | grep -c 'index\|tor-'` gemessen und **3496**
> bekommen — das zaehlt Eintraege in `TMPDIR` selbst, nicht die Halde. Die richtige Zahl ist
> **2546**. Offengelegt, weil eine um 950 zu hohe Haldenzahl im Blatt genau die Sorte Beleg waere,
> die bei der Abnahme auffliegt und dann das ganze Blatt in Zweifel zieht — wie die 7011 des
> Generators.*

**PRAEZISIERUNG des Wachstums — die Halde waechst pro TOR-LAUF, nicht pro Tag:**

```text
07.08. -> 08.08.   1749 -> 2506   +757    viele Tor-Laeufe (A-08-Bau, drei DoR-Runden, Abnahme)
08.08. -> 10.08.   2506 -> 2546    +40    rund 45 Stunden STILLSTAND, kaum Laeufe
```

> **Das schaerft die Rot-Lage, statt sie zu entkraeften.** Der Satz „binnen eines Tages um 757
> gewachsen" legt eine Zeitrate nahe; tatsaechlich haengt das Wachstum an der **Aktivitaet**. In
> 45 Stunden ohne Arbeit kamen nur 40 dazu. Die Folge ist unangenehmer als eine Zeitrate: **je
> produktiver das Team, desto schneller waechst die Halde** — und sie schrumpft nie, weil das Tor
> keinen `trap` hat (heute erneut gemessen: **0**). Ein Kriterium mit fester Zahl waere hier falsch;
> A-07-5 verlangt deshalb richtig „alle zum Zeitpunkt des Laufs vorhandenen, Zahl im Bericht".

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

---

## §11-Bericht des Generators (10.08., CODE_FERTIG)

```yaml
auftrag: A-07
basis: ff549b88
commit: c512f931
scope: [scripts/commit-pruefen.sh, scripts/__tests__/commitPruefen.test.mjs]
tests:
  statisch: pass          # bash -n + node --check, beide sauber
  unit: "42/42"           # 38 Bestand + 4 neue A-07-Zusagen, selbst gefahren, vorher 38/38
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar
  browser: nicht_anwendbar
abweichungen:
  - "Unaufgeloeste Merge-Eintraege im Standard-Index gelten als Kippfall (nicht angleichen, melden) — konservative Ergaenzung ueber den Blatt-Wortlaut hinaus, Richtung 'weniger anfassen'."
  - "A-07-5 mit 60s-Altersgrenze beim Beiseitelegen (Schutz eines moeglichen lebenden Parallel-Laufs, A-02-Klasse); zum Raeumzeitpunkt fielen ALLE 2589 darunter, 0 verblieben."
  - "Die 1b-Meldung steht auf stdout, nicht stderr — der Lauf ist erfolgreich (exit 0), sie gehoert in den Erfolgs-Ausgabekanal."
  - "HEAD wanderte waehrend des Baus (8adffd3d -> 60ebed62): die parallele A-04-Release-Kette committete 18:54-18:58 durchs GETEILTE Arbeitsverzeichnis — und damit durch das bereits editierte Tor. Die erste echte Angleichung fiel dadurch VOR meinem Bau-Commit, durch einen fremden Erstnutzer (genau die Erstnutzer-Form des Blatts). Alle Nachweise danach neu gemessen."
  - "Risiko offen benannt: waehrend der vier Mutationsfenster (je Sekunden) koennten Parallel-Commits durch ein mutiertes Tor gelaufen sein. Verwaiste Blobs waren zu JEDEM Messzeitpunkt 0 (auch M2 'unbedingt angleichen' haette nichts verworfen); der Halden-Zuwachs 2554->2589 stammt aus Suite-Laeufen mit dem alten Tor und liegt in der A-07-5-Raeumung."
offene_akzeptanz: []
```

### Kriterienstand, je mit Beleg

```text
A-07-1a  ERFUELLT   Tor gleicht nach erfolgreichem Commit an HEAD an (env -u GIT_INDEX_FILE
                    git read-tree HEAD auf .git/index; Arbeitsbaum nie beruehrt). Real belegt
                    an c512f931: 'INDEX ANGEGLICHEN' live, danach diff --cached --name-only = 0.
         ZUSATZ     git status real: 58 Eintraege vorher -> 4 nachher, ALLE 4 echt (2 M mit
                    Content-Abweichung = mein Scope, 2 ?? wirklich untracked); VIERZEHN
                    verschwundene Eintraege einzeln index-frei belegt (git show HEAD:<p> |
                    diff - <p> leer, darunter die D-Phantome — Dateien unversehrt auf der
                    Platte). Verlangt waren zehn. 4-zu-0 statt 41-zu-1.
A-07-1b  ERFUELLT   Kippfall gemessen VOR der ersten Angleichung: 20 Kandidaten-Blobs, 0 in
                    keinem Commit, 0 unmerged -> Regelfall lag vor. Kippfall-Verhalten per
                    Zusage: Index unangetastet (ls-files --stage identisch), Meldung
                    'INDEX NICHT ANGEGLICHEN 1 … ungesichert.txt' mit Zahl UND Pfad.
A-07-2   ERFUELLT   Zusage im Wegwerf-Repo: Datei angelegt, git add (Blob in ODB, in KEINEM
                    Commit), Tor laeuft -> Index unveraendert + Meldung. Mutation 'unbedingt
                    angleichen' faellt genau an dieser Zusage.
A-07-3   ERHALTEN   Mechanismus unveraendert: eigener ausgelagerter Index je Lauf (index.$$,
                    beide Bestands-Zusagen dazu weiter gruen); dazugekommen sind nur
                    Initialisierung und Raeumung — wie das Blatt es erlaubt.
A-07-4   ERFUELLT   Initialisierung: liegt unter index.$$ ein Erbe, wird er BEISEITEGELEGT
                    (_to_delete/, nie geloescht), dann read-tree HEAD (ohne HEAD: --empty);
                    Raeumung per trap 'rm -f …' EXIT — deckt alle sieben Auswege, Zusage
                    prueft Erfolgs- UND Abbruchpfad plus das trap-Konstrukt im Quelltext.
                    Gegenprobe als Zusage: vorgelegter Fremd-Index unter demselben Pfad
                    (exec haelt die PID) beeinflusst den Lauf nicht, kein 'invalid object',
                    Erbe liegt beiseite. Realwirkung: voller Suite-Lauf (rund 40 Tor-
                    Aufrufe) hinterlaesst 0 Halden-Dateien — vorher je Lauf ~35.
A-07-5   ERLEDIGT   EINMALIG, von Hand, dokumentiert: 2589 Halden-Dateien (gewachsen von
                    2551 im Blatt) nach $TMPDIR/ticket-index/_to_delete/2026-08-10-A-07-5/
                    beiseitegelegt — nur mv, 0 geloescht, 0 verblieben, 0 laufende Tore
                    zum Zeitpunkt der Raeumung.
```

### Mutationsproben (Gruen/Rot/Gruen, md5)

```text
md5 vorher/nachher  59e23956c627085b0792def9486935dc  (identisch wiederhergestellt)
M1 Angleichung entfernt          -> A-07-1a faellt          1/1 rot
M2 Angleichung unbedingt         -> A-07-2 faellt           1/1 rot
M3 trap entfernt                 -> A-07-4 faellt           1/1 rot
M4 Initialisierung entfernt      -> A-07-4-Gegenprobe faellt 1/1 rot
danach voller Lauf 42/42 gruen
```

**Bauentscheidungen, die der Evaluator kennen sollte:** Die Verwaisten-Frage laeuft ueber EIN
`rev-list --objects --all` (22776 Objekte, 0,6 s gemessen) statt einer `log --find-object`-Suche
je Blob; Kandidaten sind nur die von HEAD abweichenden Eintraege ohne Loeschungen (die tragen
keinen Blob). Initialisierung und Raeumung stehen NACH der Stufe-4-Lock-Aufraeumung, damit
W-09/K-01 (erst Locks raeumen, dann der erste git-Aufruf) wortgleich haelt. Der `trap` wird erst
NACH dem Beiseitelegen des Erbes gesetzt, damit er ihn nie loeschen kann.

---

## Evaluator-Votum (§11) — 10.08.2026

```yaml
auftrag: A-07
commit: c512f931          # Pruef-SHA; Elter d6637a1c als Kontrolle
votum: ABGENOMMEN
fehlerklasse: BEWEIS      # nur der eine P2 unten
gegenprobe: "Rot-Lauf der vier neuen Zusagen gegen das Elter-Tor · vier eigene Mutationen ·
  Erbschafts-Szenario von Hand, mit und ohne Initialisierung · Halden-Zuwachs zwei Richtungen"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde:
  - "P2 BEWEIS: die INITIALISIERUNG aus A-07-4 ist von keiner Zusage gedeckt und in keinem
     konstruierbaren Szenario wirksam — ihr Wegfall aendert nichts ausser einer stderr-Zeile."
```

### Selbst gefahren

```text
Scope-Diff c512f931   commit-pruefen.sh +94 · commitPruefen.test.mjs +143   nichts sonst
Statik                bash -n SYNTAX-OK · node --check OK
Suite Pruefstand      42/42        Suite Elter d6637a1c   38/38
Rot am Elter          die VIER neuen Zusagen: 4 von 4 rot
                      A-07-1a · A-07-2 · A-07-4 · A-07-4 GEGENPROBE
```

**Die Wirkung in beide Richtungen, an derselben Suite gemessen** — das ist der Kern von A-07-4:

```text
Suite im PRUEFSTAND (mit trap)   Halde vorher 54 -> nachher 54   Zuwachs  0
dieselbe Suite am ELTER          Halde vorher 54 -> nachher 70   Zuwachs +16
```

**Rest B in der am 10.08. korrigierten Zweischrittform, an ALLEN Einträgen statt an zehn:**

```text
git status meldet 2 Eintraege   (Blatt-Ist am 10.08. 18:3x: 52)
  ?? docs/rollenkette/     untracked -> ECHT (Schritt 1)
  ?? zz-unlink-probe       untracked -> ECHT (Schritt 1)
ECHT 2 · PHANTOM 0
```

*Die Zehnerprobe ist nicht mehr fahrbar, weil der Bau erfolgreich war — es bleiben keine zehn
Einträge übrig. Die Substanz ist erfüllt, die Form hat sich selbst überholt.*

**Vier eigene Mutationen, Anker je genau 1×, `md5` vor und nach identisch:**

```text
M1 unbedingt angleichen (Kippfall ignoriert)   fail 1   GEFANGEN durch A-07-2
M2 Angleichung ganz entfernt                   fail 1   GEFANGEN durch A-07-1a
M3 trap EXIT entfernt                          fail 2   GEFANGEN durch A-07-4 + Gegenprobe
M4 Initialisierung entfernt                    fail 0   BLIND
```

### Der eine Befund — P2, `BEWEIS`, Ball beim Generator

**A-07-4 nennt zwei Mechanismen. Der eine ist bewiesen, der andere nicht.**

```text
trap EXIT          M3 faellt, zwei Zusagen greifen, Halden-Zuwachs 0 gegen +16   BEWIESEN
Initialisierung    M4 faellt NICHT — keine einzige Zusage bemerkt ihren Wegfall
```

**Ich habe zwei Szenarien von Hand gebaut, in denen sie zählen müsste, und in beiden zählt sie
nicht:**

```text
1  geerbter Muell-Index, Beiseitelegen gelingt
     mit Initialisierung   -> GEERBTER INDEX beiseite, Commit, INDEX ANGEGLICHEN
     ohne Initialisierung  -> GEERBTER INDEX beiseite, Commit, INDEX ANGEGLICHEN   IDENTISCH
2  geerbter Muell-Index, Beiseitelegen SCHEITERT (Verzeichnis nur lesbar)
     mit Initialisierung   -> "INDEX-INITIALISIERUNG GESCHEITERT" + fatal: index file smaller
     ohne Initialisierung  -> fatal: index file smaller                            GLEICHER AUSGANG
```

**Die Erbschaft wird vom BEISEITELEGEN beendet, nicht vom `read-tree`.** *Der beobachtbare
Beitrag der Initialisierung ist eine Zeile auf stderr.*

> **Warum das die Abnahme nicht blockiert:** der **Zweck** von A-07-4 — die Erbschaft endet, der
> Wegwerf-Index bleibt nicht liegen — ist an beiden Enden belegt. *Geschuldet ist kein Code,
> sondern ein Nachweis:* entweder ein Szenario, in dem die Initialisierung den Lauf rettet, oder
> der ehrliche Satz im Blatt, dass sie eine **Diagnose** ist und die Erbschaft vom Beiseitelegen
> beendet wird. **§12: `BEWEIS` lässt den Code unverändert und schuldet die Zusage.**

### Zwei Dinge in eigener Sache

**A-07-5 ist erfüllt und ich habe die Zahl verfälscht.** Der Bau hat **2590** Dateien nach
`_to_delete/2026-08-10-A-07-5/` beiseitegelegt — *beiseite, nicht gelöscht, Dauerregel gewahrt.*
**Danach standen wieder 92 im Verzeichnis — alle nach 19:00 entstanden, alle aus MEINEN
Elter-Kontrollläufen** (das alte Tor ohne `trap`). *Wer die Halde nach mir misst, hätte 92 als
Rückfall gelesen.* Ich habe sie nach derselben Konvention beiseitegelegt
(`_to_delete/2026-08-10-evaluator-kontrolllaeufe/`, 92 Dateien, nichts gelöscht); Stand danach: **0**.

**Zum Prüfstand:** `node_modules` **und** `vendor` verlinkt — beide Rezeptschritte, nachdem ich
bei A-04 heute `vendor` vergessen und dadurch beinahe meinen eigenen Aufbau als Regression
gemeldet hatte.

---

## Evaluator-Votum A-07 — Zweitinstanz, 10.08. (Claim-Kollision: Bestätigung, KEIN zweites Urteil)

**Zuerst die Rollenlage, ehrlich:** Ich wurde als Evaluator für die A-07-Abnahme angesetzt und habe
den `claim_abnahme` der Erstinstanz beim Prüfstart gesehen — und trotzdem gemessen, weil mein
Auftrag mich als „den" Evaluator adressierte. **Während meiner Messungen hat die Erstinstanz die
Abnahme vollzogen (`fc5a3daa`, ABGENOMMEN an `c512f931`).** Ihr Votum ist das gültige; dieses
Blatt bekommt von mir **kein zweites Urteil, sondern eine unabhängige Zweitmessung** — dieselbe
Klasse Kollision wie bei A-04, und diesmal steht sie als Befund da statt als zwei konkurrierende
Voten. *Alle folgenden Zahlen sind selbst gemessen, kein Wert vom Generator oder der Erstinstanz
übernommen; wo beide dasselbe messen, ist das jetzt eine DREIFACH unabhängige Bestätigung.*

**Messbasis:** Arbeitsbaum-Scope-Dateien je `content-diff` **IDENTISCH** zu `c512f931`
(vor und nach den Messungen geprüft; HEAD wanderte während der Prüfung 55317e1e → 5f98cc28 →
fc5a3daa → 199039fa, **kein Commit berührte den Scope**: `git log c512f931.. -- <beide>` leer).

### Suite und Basis — selbst gefahren, eigener TMPDIR je Lauf

```text
c512f931 (Arbeitsbaum identisch)   tests 42 · pass 42 · fail 0   Halden-Rueckstand des Laufs: 0
c512f931^ (worktree add -q)        tests 38 · pass 38 · fail 0   Halden-Rueckstand des Laufs: 16
Basis-Gegengriff: trap 0 · "INDEX ANGEGLICHEN|read-tree" 0 Treffer im Basis-Tor
statisch: bash -n pass · node --check pass · md5 Tor 59e23956c627085b0792def9486935dc (= §11-Bericht)
```

### Je Kriterium — eigene Wegwerf-Proben (Repo selbst gebaut, Tor aus `git show c512f931:`)

**A-07-1a BESTÄTIGT.** Eigener Tor-Zyklus: `INDEX ANGEGLICHEN … der Arbeitsbaum ist unberuehrt`,
danach `diff --cached --name-only` = **0**, Arbeitsbaum trägt den neuen Stand. **Zwei-Richtungs-
Probe:** identischer Zyklus mit dem Basis-Tor (`c512f931^`) → `diff --cached` = **1** (`anfang.txt`
bleibt divergent) und 1 Halden-Datei. **Real am echten Repo:** `git status --porcelain` = 1 Eintrag
(`?? zz-unlink-probe`), in der **korrigierten Zwei-Schritt-Form** (Schritt 1 `ls-files`) als
untracked = ECHT; `diff --cached` = 0; **1 zu 1 statt 41 zu 1.**

**A-07-1b/2 BESTÄTIGT.** Kippfall selbst hergestellt (`git add ungesichert.txt`, Blob in keinem
Commit): `INDEX NICHT ANGEGLICHEN  1 … ungesichert.txt` — **Zahl UND Pfad**, `ls-files --stage`
vorher/nachher **byte-identisch**, exit 0 (melden, nicht blockieren). Auflösung (Blob committet)
→ nächster Tor-Lauf wieder `INDEX ANGEGLICHEN`, `diff --cached` = 0. **Dazu der Feldbeleg, den
niemand bestellen konnte:** `7ab67893`/`eec79bc4` — der Kippfall trat LIVE mit 211 fremden
Index-Blobs ein, das Tor meldete und fasste nichts an.

**A-07-3 BESTÄTIGT.** Stufe-5-Block unverändert (nur der Marker `INDEX_VOM_TOR` kam hinzu);
eigener ausgelagerter Index je Lauf, PID im Pfad. Die zwei Bestandszusagen: Z.180 („Ausweichpfad
AUSSERHALB des Mounts, je Prozess eigen") und Z.205 („von aussen gesetzte GIT_INDEX_FILE wird
NICHT ueberschrieben") — beide in meinem 42/42-Lauf grün.

**A-07-4 BESTÄTIGT** (mit dem BEWEIS-Vorbehalt der Erstinstanz, s.u.). Code: `trap 'rm -f …' EXIT`
Z.355, gesetzt NACH dem Beiseitelegen des Erbes; ich zähle **12** exit-Punkte (Blatt 7, Plan-Prüfer
10 — nicht tragend, `trap EXIT` deckt alle). Verhaltensproben selbst: Abbruchlauf (`FEHLT`-Pfad,
exit 1) hinterlässt **0** `index.*`; exec-PID-Trick mit Müll-Index → `GEERBTER INDEX … beiseitegelegt,
nicht geloescht`, **kein** `invalid object`, Commit trägt exakt den genannten Pfad, Erbe liegt unter
`_to_delete/`, Rückstand 0. Wirkungsdifferenz ganzer Suite-Lauf: **0 (neu) gegen 16 (Basis)**.

**A-07-5 BESTÄTIGT.** Selbst gezählt: `_to_delete/2026-08-10-A-07-5/` = **2589** Dateien (deckt die
Generator-Zahl; die 2590 der Erstinstanz weicht um 1 ab — nicht tragend), beiseitegelegt statt
gelöscht; lebende Halde bei Abschlussmessung **0**, dazu `_to_delete/2026-08-10-evaluator-
kontrolllaeufe/` = 92 (die Alt-Tor-Kontrollläufe der Erstinstanz — ich hatte sie 19:05–19:06 als
52 wachsende Dateien live gesehen und als Rätsel notiert, ihr Blattnachtrag löst es auf).

### Mutationsproben — an einer KOPIE, kein Mutationsfenster im echten Tor

```text
Kopie-Kontrolllauf                42/42       md5 = 59e23956c627085b0792def9486935dc
M2 Angleichung unbedingt (-eq 0 -> -ge 0)    41/42 — GENAU A-07-2 faellt (die gefaehrliche Richtung)
M3 trap entfernt                             40/42 — A-07-4 UND Gegenprobe fallen
je md5-identisch zurueckgestellt, Abschlusslauf 42/42
```

*Der Weg über die Kopie beweist dasselbe wie die Generator-Mutationen — ohne die von ihm als
Abweichung deklarierten Sekunden-Fenster am geteilten Arbeitsbaum. Empfehlung: künftig so.*

### Die vier deklarierten Abweichungen, gewürdigt

```text
Erstnutzer ungeplant (A-04-Kette)   GEDECKT   exakt die Erstnutzer-Form des Blatts ("naechster
                                              Commit, ohne eigenen Aufruf"); kein Schaden belegt
Mutationsfenster                    GEDECKT   ehrlich deklariert; verwaiste Blobs zu jedem
                                              Messzeitpunkt 0; kuenftig Kopie statt Fenster
unmerged -> Kippfall                GEDECKT   konservativ, keine Zusage geschwaecht; RANDNOTIZ
                                              P3 an den Planner: im Blatt kodifizieren + Zusage
1b-Meldung auf stdout               GEDECKT   Blatt schreibt keinen Kanal vor; exit 0 = Erfolgskanal
60s-Schutz bei A-07-5               GEDECKT   A-02-Klasse (lebende Laeufe); Ergebnis = Blattforderung
```

### Befunde dieser Zweitmessung (keiner blockiert)

1. **KOLLISION (Prozess, P2):** Zweitbesetzung der Abnahme-Station trotz sichtbarem Claim — dieselbe
   Klasse wie bei A-04. Der Claim-Mechanismus braucht eine Regel, die auch die **Beauftragung**
   bindet, nicht nur die Instanz, die den Eintrag liest.
2. **P3/UMGEBUNG:** Eine PID-lose Altdatei `index` (03.08., 145 B) liegt weiter in der Halde — von
   `index.$$` nie erreichbar (Klasse `index.gen*`), von der A-07-5-Räumung nicht erfasst, vom
   Blatt-Messbefehl (`^index\.`) nicht gezählt. Beim nächsten Aufräumen mit beiseitelegen.
3. **Bestätigung des BEWEIS-Befunds der Erstinstanz (P2):** Meine M3-Probe zeigt, dass die
   A-07-4-Zusagen an `trap` und Beiseitelegen hängen; den Wegfall der `read-tree`-Initialisierung
   habe ich nicht separat mutiert — der Erstinstanz-Befund (M4 lässt die Suite grün) ist mit meiner
   Code-Lesung konsistent: das Beiseitelegen beendet die Erbschaft, `read-tree` ist Diagnose/Netz.

**Ergebnis der Zweitmessung: das Votum ABGENOMMEN an `c512f931` (Fehlerklasse KEINE, ein
P2-BEWEIS-Folgeauftrag beim Generator) wird in allen Kriterien unabhängig bestätigt.**
