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

> ### KORRIGIERT (07.08.) — meine Verallgemeinerung trug nicht
>
> **Ich schrieb: „auf dieser Maschine unerreichbar."** Der Evaluator hat den Zweig `HALTER=0`
> **erreicht**. Selbst nachgemessen:
>
> ```text
> frisch angelegte Datei im Repo      KEIN Halter   (auch nach cat, auch nach 700 s)
> README.md                           59792
> zz-unlink-probe (vom 03.08.)        59792
> .git/index.lock (gestern 18:06)     59792
> ```
>
> **Es ist eine Eigenschaft der DATEI, nicht der Maschine.** *Was die beiden Gruppen trennt, hat
> er **nicht** ermittelt, und ich auch nicht — Alter allein erklärt es nicht (der Lock war fünf
> Minuten alt und meldete einen Halter).* **Die Ursache bleibt ausdrücklich offen.**
>
> **Für den Fix ändert das nichts, für die Formulierung viel:**
> *„die Maschine kann nicht antworten" ist nicht prüfbar — **„`lsof` antwortet auf eine andere
> Frage als die gestellte" schon.*** Das Kriterium darf nicht von einer unerklärten Erscheinung
> abhängen.

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

## Die Richtung ist ENTSCHIEDEN (Plan-Prüfer, 07.08.) — und keine meiner beiden war es

**Ich hatte A oder B vorgelegt. Er nimmt keine von beiden allein:**

```text
A allein   Kommando des Halters pruefen   -> SPIEGELT den Fehler
B allein   laeuft ueberhaupt ein git-Prozess?  -> ungemessene ZUORDNUNG

ENTSCHIEDEN:  verwaist = DREI Nein zusammen — und NUR bei 0-BYTE-Locks (Umschnitt 07.08.)
              1  kein Halter mit git-Kommando
              2  kein git-Prozess DIESES Repositoriums laeuft
              3  das Altersmass des Tors ist erfuellt  (NICHT neu formuliert -
                 commit-pruefen.sh:163 fuehrt einen DOPPELPFAD, siehe A-08-1)
              -> dann beiseitelegen nach Yamas Dauerregel; sonst ENV_BLOCKED wie heute

UMSCHNITT (07.08., f5098c40 -> 0a4efd84): die Kommando-Frage ersetzt die Halter-Blockade
              NUR bei 0-Byte-Locks. Ein Lock MIT Inhalt (> 0 Byte) und Halter bleibt liegen
              wie heute, egal welches Kommando der Halter traegt. Sonst faerbte die Tabelle
              die Zusagen A-02-2 (commitPruefen.test.mjs:512) und A-02-4 (Z.579) rot:
              A-02 schuetzt dort die EXISTENZ eines lebenden Halters, nicht sein Kommando —
              der Generator hat das VOR dem Bau gemessen, der Plan-Pruefer bestaetigt.
```

*Beide meiner Formen hatten je eine halbe Antwort. **Drei unabhängige Nein sind belastbarer als ein
besseres Ja** — und die dritte Bedingung braucht `lsof` gar nicht.*

**§12.5 angewandt:** **A-02 bleibt `ABGENOMMEN`.** Die Nachbesserung setzt auf `6953198a` auf,
**keine Warteschlange.**

## Akzeptanzkriterien

**A-08-1 (P1, ZWEIMAL KORRIGIERT 07.08. — zuletzt Umschnitt auf die 0-Byte-Fassung):** Ein
**0-Byte-Lock** gilt genau dann als **verwaist**, wenn **VORAB und alle drei** zutreffen:

```text
0  VORAB: der Lock ist 0 Byte gross — nur dann stellt sich die Kommando-Frage ueberhaupt
1  kein Halter mit `git`-Kommando
2  kein laufender `git`-Prozess DIESES Repositoriums
3  das BESTEHENDE Altersmass des Tors ist erfuellt (fuer 0 Byte: >= 60 s, commit-pruefen.sh:163)
```

Dann wird er **beiseitegelegt, nie gelöscht** — die Meldung nennt Zielpfad, Größe und Alter; der
Commit läuft weiter. Ein Lock **mit Inhalt (> 0 Byte) und Halter bleibt liegen wie heute**,
unabhängig vom Kommando des Halters. *Rot heute: der Vorfall vom 06.08. (0 Byte, 239 s,
VM-Halter) endet in `ENV_BLOCKED`, zweimal gemessen.*

> **Führender Wortlaut ist Nachtrag-A-08-1** (verbindliche Lesart des Plan-Prüfers: EIN Katalog).
> Dieser Absatz trägt dieselbe Fassung; bei Abweichung gilt der Nachtrag. *Die Doppelfassungs-Falle
> ist in diesem Auftrag zweimal passiert (`3392400f`, `1dcdc32e`) — deshalb steht die Rangfolge
> hier ausdrücklich.*

> ### Warum die dritte Bedingung nicht mehr ausgeschrieben wird
>
> **Ich hatte „0 Byte und ≥ 60 s alt" hineingeschrieben** — die Kurzform aus der
> Richtungsentscheidung, **ohne im Tor nachzusehen, was dort steht.** Der Evaluator hat den
> Widerspruch **vor dem Bau** gemeldet. Selbst nachgemessen:
>
> ```text
> commit-pruefen.sh:25    "nur ein 0-Byte-Lock, aelter als 60 s, wird beiseitegelegt"
> commit-pruefen.sh:101   "Bis A-02 galt: wer 120 s nicht schreibt, laeuft…"  <- ZWEITER Pfad
> Zusagen :122 / :163     Locks MIT INHALT, 300 s alt, Erwartung: beiseitelegen
>                         eine davon traegt `must_preserve` im Namen
> ```
>
> **Wer A-08-1 wörtlich baut, nimmt den 120-s-Pfad heraus und färbt beide Zusagen rot** — *nach
> A-08-3 gescheitert, nach A-08-1 richtig.* **Zwei meiner eigenen Kriterien hätten einander
> widersprochen.**
>
> **Und die Herkunft verschärft es:** der zweite Pfad stammt aus der Blockade des Evaluators vom
> 03.08. (317 s, 885 kB), und der Testkommentar sagt wörtlich, dass die alte Regel
> „0 Byte UND ≥ 60 s" **genau diesen Fall nicht erkennen konnte.** *Ich hatte die alte Regel
> wieder hingeschrieben.*
>
> *Dritter Fall derselben Klasse: eine Formulierung übernommen, den Gegenstand nicht gemessen.*

**A-08-6 (Bezug, ausdrücklich als NICHT-Befund vermerkt):** „Kein laufender `git`-Prozess" nennt
**keinen Bezug auf dieses Repository** — ein `git`-Lauf in einem fremden Repo derselben Maschine
zählt mit. *Der Evaluator hat es dreimal gemessen, je **0**; die Sorge ist **nicht** gestützt.*
**Deshalb steht sie im Blatt und nicht in der Umsetzung** — wer später einen Fehlalarm sieht, findet
hier die bekannte Grenze, statt sie neu zu suchen.

**A-08-2 (P1, Gegenprobe — KORRIGIERT 07.08. nach dem Umschnitt):**

```text
Lock ist 0 Byte   und eine der Bedingungen 1-3 fehlt   -> ENV_BLOCKED, exit 3
Lock ist > 0 Byte                                      -> das Tor entscheidet UNVERAENDERT
                                                          wie bisher (A-02-Logik). A-08 aendert
                                                          daran nichts.
```

*Ohne die erste Zeile wäre „alles ist verwaist" grün — schlimmer als die Blockade.*

> ### Warum die zweite Zeile dazugehört — ein Widerspruch in meinem eigenen Kriterienpaar
>
> **Die alte Fassung lautete unbeschränkt** *„fehlt eine der drei Bedingungen → `ENV_BLOCKED`"*.
> **Gegen den Zusagen-Bestand gehalten:**
>
> ```text
> Zusage :547 (must_preserve)   Lock MIT Inhalt (885 kB), alt (317 s), OHNE Halter
>                               -> beiseite. Heute gruen, an der Basis schon.
> ```
>
> **Für diesen Fall verlangte A-08-2 wörtlich `ENV_BLOCKED` — A-08-3 wäre gebrochen.** *Und liest
> man das `VORAB` nicht als eine der Bedingungen mit, fällt der Fall durch **beide** Kriterien und
> ist ungeregelt.* **Beide Lesarten waren defekt: die eine widersprüchlich, die andere lückenhaft.**
>
> *Dazu eine Unsauberkeit, die den Streit erst ermöglichte: A-08-1 sagte „alle **drei**" und listete
> **vier** Punkte. Ein Kriterium, dessen Zählung nicht stimmt, lässt sich in zwei Richtungen lesen —
> und der Bauende müsste wählen.*

**A-08-3 (`must_preserve`):** **Alle A-02-Zusagen bleiben grün**, insbesondere die Zeitgrenze
(hängendes `lsof` → Abbruch statt Warten) und die `ENV_BLOCKED`-Meldeform mit Exitcode 3 und
Halter-Angabe. *§7: keine Abschwächung bestehender Tests.*

**A-08-4 (P2):** Die Meldung nennt bei `ENV_BLOCKED` **das Kommando** des Halters, nicht nur die
PID. *`Halter: 59792` sagt niemandem etwas; `Halter: 59792 (XPCService)` beendet die Suche sofort.*

**A-08-5 (P1, Form der Probe — aus dem Selbstbefund des Evaluators):** Die Lock-Probe in der
Testsuite **entsteht aus einem echten `git`-Lauf**, nicht aus `touch` oder `printf`.

> **Seine Gegenprobe vom 03.08. lief an einer selbst angelegten Datei** — *genau der Sorte, die den
> Phantom-Halter nie bekommt.* **Der Beweis war echt und trotzdem blind für diesen Fall.**
> *Eine Probe, die den Gegenstand selbst herstellt, prüft ihre eigene Herstellung mit.*

## Ehrliche Grenze — was dieser Auftrag AUSDRÜCKLICH nicht löst

**Die 03.08.-Klasse bleibt Handräumung:** ein Lock **mit Inhalt**, tatsächlich verwaist, aber vom
Phantom-Halter des virtualisierten Mounts als „offen" gemeldet (die Sorte `zz-unlink-probe`: seit
dem 03.08. hält PID 59792 sie, obwohl nichts daran arbeitet), endet weiterhin in `ENV_BLOCKED`
mit `exit 3` und wird **von Hand nach Yamas Dauerregel geräumt** — nie gelöscht, Original
erhalten. *Das ist konservatives Scheitern ohne Datenverlust, kein Mangel des Baus.*

Wer diese Klasse automatisch geräumt haben will, braucht eine **Phantom-Erkennung** (z. B. die
Kontrollprobe aus der Triage `0a4efd84`: hält dieselbe PID auch eine unbeteiligte Referenzdatei
wie `.git/config`, ist sie Mount-Rauschen). Die hängt an der **unerklärten** Trennung der zwei
Dateigruppen (Nachtrag, „NICHT ERMITTELT") und gehört in ein **eigenes Blatt mit eigener
Messung** — nicht als Beifang in A-08.

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

---

## Generator-Bericht (§11) — CODE_FERTIG, 07.08.2026

```yaml
auftrag: A-08
basis: c2de1eec          # HEAD bei Uebernahme; Rot-Messungen der Blaetter an d377683a, Linie 6953198a ist Vorfahr
commit: 5a54b004         # der Bau (Tor + Zusagen); A-08-7-Doku: 6a264834; IN_ARBEIT: 1f17f93a
scope:
  - scripts/commit-pruefen.sh
  - scripts/__tests__/commitPruefen.test.mjs
  - docs/auftraege/aktiv/A-02-lock-halter-statt-ruhe.md   # NUR A-08-7-Richtigstellung
  - docs/auftraege/aktiv/A-08-halter-nach-kommando.md     # dieser Bericht
  - docs/STATUS.md                                        # Statuswechsel
tests:
  statisch: pass          # bash -n commit-pruefen.sh: SYNTAX-OK · node --check commitPruefen.test.mjs: CHECK-OK
  unit: "38/38"           # vorher 30/30 an c2de1eec, nachher 38/38 an 5a54b004 — beide selbst gefahren
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar  # kein Bundle beruehrt
  browser: nicht_anwendbar
abweichungen: []
offene_akzeptanz: []
```

**Nummerierung nach der verbindlichen Lesart in `docs/STATUS.md`:** Nachtrag-Katalog A-08-1..8,
dazu Traegerblatt-„A-08-3" als **A-08-9** und Traegerblatt-„A-08-4" als **A-08-10**.

### Suite-Zaehler, Rohausgabe (`node --test scripts/__tests__/commitPruefen.test.mjs`)

```text
VORHER  (Basis c2de1eec, Tor unveraendert):   ℹ tests 30   ℹ pass 30   ℹ fail 0
NACHHER (Bau 5a54b004):                        ℹ tests 38   ℹ pass 38   ℹ fail 0
ROT-LAGE der 8 neuen Zusagen GEGEN DAS BASIS-TOR (git show HEAD:scripts/commit-pruefen.sh,
node --test --test-name-pattern 'A-08'):       ℹ tests 8    ℹ pass 3    ℹ fail 5
  gefallen an der Basis: A-08-1 · A-08-4 · A-08-5 · A-08 Form B · A-08-10
  an der Basis gruen (gewollt, Gegenhalter-/Erhaltungsrichtung): A-08-2 · A-08-4 git-* · A-08-8
```

### Je Kriterium

**A-08-1 (P1, der Vorfall — erfuellt).** Zusage *„A-08-1: der Vorfall — 0-Byte-Lock, alt,
NICHT-git-Halter, kein Repo-git -> beiseite, Commit laeuft"*: 0 Byte, 239 s, lebender
node-Halter (kein git-Kommando) → `exit 0`, Lock in `_locks_beiseite/` (nie geloescht), die
BEISEITE-Zeile nennt Zielpfad, `0 Byte` und `\d+s alt` — alles einzeln asserted.
**Zwei-Richtungs-Probe:** dieselbe Zusage gegen das Basis-Tor **rot** (`✖ A-08-1`, Rohausgabe
oben), am Bau **gruen**. Umsetzung: Drei-Nein-Block in `commit-pruefen.sh` (Halter-Zweig), die
Bedingungen als EIN `&&`-Ausdruck (`KEIN_GIT_HALTER && KEIN_REPO_GIT && MASS_ERFUELLT`).

**A-08-2 (must_preserve Zeit — erfuellt).** Zusage *„A-08-2: … FRISCH (< 60 s) -> liegt,
ENV_BLOCKED, exit 3"*: 0 Byte, 30 s, lebender Halter → Lock liegt, `exit 3`. An der Basis gruen
(korrekt deklariert), und sie MISST: Mutation M1 (`&&`→`||`) laesst genau sie fallen.

**A-08-3 (must_preserve Bestand — erfuellt).** Alle 30 Bestandszusagen gruen (Rohausgabe oben),
ausdruecklich einschliesslich der beiden am Stillstandspfad (*„Tor Teil 2"* Z.115 und *„A-02-1
KONTROLLE"* Z.547). Der Doppelpfad ist **nicht angetastet** — Bedingung 3 ZITIERT ihn woertlich
(`{ GROESSE -eq 0 && ALTER -ge 60 } || ALTER -ge 120`), im HALTER=0-Zweig steht er unveraendert.
Beleg: `grep -c 'ALTER" -ge 120' scripts/commit-pruefen.sh` → 2 (Bedingung-3-Zitat + Original).

**A-08-4 (P1, Basename + git-* — erfuellt).** Zwei Zusagen: Halter mit Kommando
`/…/git` (voller Pfad, per Symlink auf node — `ps -o comm=` zeigt den Symlink-Pfad, selbst
gemessen) → blockt; Halter `git-remote-https` → blockt. Verglichen wird der **Basename**
(`HBASE=${HKOMMANDO##*/}`), `git-*` im `case`-Muster. Rot-Beleg an der Basis: `✖ A-08-4`
(Meldung nannte kein Kommando); Mutationen M4 (Pfad-Gleichheit) und M5 (`git-*` entfernt) fallen.

**A-08-5 (P1, Unklarheit konservativ — erfuellt).** Zusage *„A-08-5"*: lebender Halter, aber ein
`ps` ohne Antwort (fake-`ps` im PATH, dieselbe Technik wie die bestehende fake-`lsof`-Zusage) →
Lock liegt, `exit 3`, Zeile `ENV_BLOCKED: halter-kommando nicht ermittelbar — … (Halter:
unbekannt)`. Rot an der Basis (`✖ A-08-5`), Mutation M6 faellt.

**A-08-6 (P1, Mutationsprobe — erfuellt, Gruen/Rot/Gruen).** Gruen vorher: 38/38. Sieben
Mutationen einzeln eingespielt (exakte Ersetzung, je Lauf die volle Suite), danach byte-identisch
wiederhergestellt (Checksumme `1713f09a…` vor und nach jeder Probe identisch), Gruen nachher:
38/38. Rohausgabe der Proben:

```text
M1 && -> ||                       tests 38  pass 35  fail 3   A-08-2 · A-08-4 · A-08-4 git-*
M2 Kommando-Pruefung entfernt     tests 38  pass 34  fail 4   A-08-4 · A-08-4 git-* · A-08-5 · A-08-10
M3 Ergebnis ignoriert             tests 38  pass 36  fail 2   A-08-4 · A-08-4 git-*
M4 Basename -> Pfad-Gleichheit    tests 38  pass 35  fail 3   A-08-4 · A-08-4 git-* · A-08-10
M5 git-* nicht erkannt            tests 38  pass 37  fail 1   A-08-4 git-*
M6 unbekannt = nicht gehalten     tests 38  pass 37  fail 1   A-08-5
M7 0-Byte-Schranke entfernt       tests 38  pass 35  fail 3   A-02-2 · A-02-4 · A-08-10
```

M7 faellt **durch die bestehenden Zusagen A-02-2 (Z.512) und A-02-4 (Z.579)** — exakt der Fall
aus `f5098c40`, nie wieder stumm gruen.

**A-08-7 (P0, Doku — erfuellt).** Commit `6a264834`: Richtigstellung direkt unter der Messung in
`A-02-lock-halter-statt-ruhe.md` (Z.61–67) — *„lsof trennt sie exakt" gilt hier nicht*, mit
Fehlertyp (`lsof` beantwortet „offen", nicht „git arbeitet"), Verweisen auf `de33d1e6`,
`d377683a` und beide A-08-Blaetter. Das Original bleibt stehen, richtiggestellt statt geloescht.

**A-08-8 (P1, Probenherkunft — erfuellt).** Zusage *„A-08-8 PROBENHERKUNFT: der Lock stammt aus
einem ECHTEN git-Lauf — unterbrochenes git, kein touch"*: `git update-index --index-info` nimmt
den `index.lock` beim Start und haelt ihn, solange stdin offen ist (vor dem Bau im Scratchpad
gemessen: 0 Byte, `lsof`-Halter = git-PID, `ps comm=` `/Library/Developer/CommandLineTools/usr/
bin/git`). Der Test misst die Groesse des ECHTEN git-Locks (`statSync(p).size === 0`), prueft
zuerst die Blockade am lebenden git (Bedingung 1 an einem echten git-Halter), toetet den Lauf
mit SIGKILL mitten im Lock — die Entstehung eines Verwaisten — und faehrt dann die Drei-Nein-
Raeumung. Der Testname und -kommentar benennen die Herkunft ausdruecklich.

**A-08-9 (must_preserve, Traegerblatt-„A-08-3" — erfuellt).** Alle A-02-Zusagen gruen (Suite-
Rohausgabe oben), einschliesslich Zeitgrenze (*„A-02 Kante 2: ein HAENGENDES lsof …"* gruen,
5,1 s statt Haengen) und ENV_BLOCKED-Meldeform mit Exitcode 3 und Halter-Angabe (*„A-02-4"*
gruen). Keine bestehende Zusage abgeschwaecht; die Suite ist von 30 auf 38 GEWACHSEN.

**A-08-10 (P2, Kommando in der Meldung — erfuellt).** Zusage *„A-08-10"*: Blockade-Meldung am
unveraenderten Schutzpfad nennt `Halter: <pid> (node)` — asserted per Regex mit der echten PID.
Im Vorfalls-Fall hiesse das kuenftig `Halter: 59792 (XPCService)`. Rot an der Basis
(`✖ A-08-10`: die alte Meldung trug nur die PID).

### Kanten, ausdruecklich nach Kantenliste gebaut

```text
mehrere Halter, EINER git            case-Schleife ueber ALLE PIDs: ein git-Treffer -> GIT_HALTER=ja -> liegt
PID zwischen lsof und ps wiederverwendet/verschwunden   Kommando leer -> unbekannt -> liegt (im Zweifel gehalten)
git-Prozess in FREMDEM Verzeichnis   cwd-Auskunft (lsof -a -p -d cwd -Fn) gegen REPO_WURZEL (pwd -P) — zaehlt nicht
Form B nicht ermittelbar             (lsof haengt/cwd unlesbar, Prozess existiert) -> zaehlt als JA, liegt
lsof haengt                          A-02-6-Zeitgrenze unveraendert, auch fuer die cwd-Frage uebernommen
lsof fehlt                           A-02-3-Pfad WOERTLICH unveraendert (Kantenliste: konservativer Rueckfall)
Form B auch am HALTER=0-Pfad         0-Byte-Lock ohne Halter bei laufendem Repo-git -> liegt (die zweite benannte
                                     Verhaltensaenderung; Zusage „A-08 Form B" prueft BEIDE Richtungen)
```

### Erstnutzer, bereits eingetreten

Der Commit `6a264834` (A-08-7) lief als erster **durch das umgebaute Tor** — jede Rolle ist ab
dem naechsten Commit Nutzer, ohne eigenen Handgriff.

### Ehrliche Grenzen (aus dem Blatt, unveraendert)

Die 03.08.-Klasse (Content-Lock, verwaist, phantom-gehalten) bleibt `ENV_BLOCKED` mit
Handraeumung nach Dauerregel. Die Nach-BEREIT-Korrektur `4c85e9b9` (Traegerblatt-A-08-2) wartet
auf die Bestaetigung des Plan-Pruefers; der Bau ist mit alter UND korrigierter Fassung
deckungsgleich, ein Konflikt entsteht nicht.

**Meldung: CODE_FERTIG.** Ich nehme nicht ab — Ball beim Evaluator.
