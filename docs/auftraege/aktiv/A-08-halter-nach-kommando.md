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

---

## Evaluator-Votum (§11) — 08.08.2026

```yaml
auftrag: A-08
commit: 85b03d23          # Pruef-SHA; Bau 5a54b004, A-08-7-Doku 6a264834, Basis c2de1eec
votum: ABGENOMMEN
fehlerklasse: SPEC        # nur der eine P2-Befund unten; kein CODE-Befund
gegenprobe: sieben eigene Mutationen · Rot-Lauf gegen das Basis-Tor · drei eigene Torlaeufe
browser: nicht_anwendbar  # Shell-Werkzeug, keine sichtbare Wirkung
datenbank: nicht_anwendbar # §15: der Auftrag beruehrt keine Datenbank, kein Seed, kein Server
befunde:
  - "P2 SPEC: ein git-Prozess DIESES Repos mit --git-dir und fremder cwd wird nicht erkannt"
```

### Prüfstand

Frischer Worktree auf `85b03d23`, `node_modules` per `cp -al`; zweiter Worktree auf der Basis
`c2de1eec` als Kontrolle. **Scope-Diff des Bau-Commits** (nicht `Basis..HEAD`):

```text
5a54b004   scripts/commit-pruefen.sh  +144/-8 · scripts/__tests__/commitPruefen.test.mjs  +236
6a264834   docs/auftraege/aktiv/A-02-lock-halter-statt-ruhe.md  +20
85b03d23   docs/auftraege/aktiv/A-08-halter-nach-kommando.md   +139
```

*Kein fremder Pfad, kein Bundle, keine Migration.* **§14 eingehalten.**

### Selbst gefahren, nicht übernommen

```text
Suite Pruefstand   tests 38  pass 38  fail 0
Suite Basis        tests 30  pass 30  fail 0        <- alle 30 Bestandszusagen halten (A-08-9)
NEUE Zusagen GEGEN das Basis-Tor:  tests 38  pass 33  fail 5
  gefallen: A-08-1 · A-08-4 · A-08-5 · A-08 Form B · A-08-10
```

**Sieben Mutationen, je mit Anker (Treffer genau 1×), volle Suite, danach `md5` zurückgesetzt:**

```text
M1 && -> ||                     fail 3   GEFANGEN
M2 Kommando-Pruefung entfernt   fail 4   GEFANGEN
M3 Ergebnis ignoriert           fail 2   GEFANGEN
M4 Basename -> Pfad-Gleichheit  fail 3   GEFANGEN
M5 git-* nicht erkannt          fail 1   GEFANGEN
M6 unbekannt = nicht gehalten   fail 1   GEFANGEN
M7 0-Byte-Schranke entfernt     fail 3   GEFANGEN — faellt durch A-02-2 und A-02-4
md5 vor und nach jeder Probe identisch.
```

**Drei eigene Torläufe im Wegwerf-Repo** (Tor hineinkopiert, echter Lock, 0 Byte, 240 s):

```text
A  kein git-Prozess                 -> BEISEITE + Commit          (die Wirkung)
B  git -C <repo>, cwd fremd gestartet -> ENV_BLOCKED, Lock liegt  (git chdirt selbst)
C  git --git-dir=<repo>/.git, cwd fremd -> BEISEITE + Commit      <- der Befund
```

### Der eine Befund — P2, `SPEC`, Ball beim Planner

**Probe C ist reproduzierbar:** ein `git`-Prozess, der **an diesem Repository arbeitet**, aber
über `--git-dir` statt `-C` gestartet wurde, behält seine fremde `cwd` — und
`repo_git_laeuft()` erkennt ihn nicht. Der Lock wurde beiseitegelegt und der Commit lief.

**Warum es dennoch keine Abnahme blockiert:**

- **A-08-1 verlangt „kein git-Prozess dieses Repositoriums"** — die **Kantenliste** des Blattes
  legt dafür `cwd` gegen `REPO_WURZEL` fest und schreibt: *„git-Prozess in FREMDEM Verzeichnis
  zählt nicht."* **Der Bau folgt dem Blatt genau.** *Die Zeile verwechselt „fremdes Verzeichnis"
  mit „fremdes Repository" — das ist eine Lücke im Schnitt, nicht im Bau (§12.1 → Planner).*
- **Die gefährliche Lage deckt Bedingung 1 ab:** ein arbeitendes `git` **hält** seinen
  `index.lock` offen, und dann greift der git-Halter-Zweig — in Probe B genau so gemessen.
  Für einen Fehlgriff müssten zusammentreffen: `--git-dir` **und** fremde `cwd` **und** ein
  0-Byte-Lock **älter als 60 s**, den dieses `git` **nicht offen hält**.
- **`git -C`, die verbreitete Form, wird erkannt** (Probe B) — auch die, die ich selbst benutze.

### Offengelegt

- **§4-Reihenfolge:** die Ausgabe von `git worktree add` hat mir die **Betreffzeile** von
  `85b03d23` gezeigt (`Suite 30/30 -> 38/38, Rot-Lage 5/8, sieben Mutationen`), **bevor** ich
  gemessen hatte. *Alle Zahlen oben sind trotzdem eigene Läufe; die Übereinstimmung ist das
  Ergebnis, nicht die Quelle.* Beim nächsten Mal `worktree add` mit unterdrückter Ausgabe.
- **A-08-2, A-08-4 `git-*` und A-08-8 sind an der Basis grün** — kein wirksames Rot. *Das ist
  richtig so und im Blatt als Erhaltungsrichtung deklariert; ich nenne es, weil „5 von 8 rot"
  sonst wie ein Mangel aussieht.*

---

## Evaluator-ZWEITVOTUM — 08.08.2026, ABGENOMMEN (unabhängige Bestätigung nach Instanzen-Kollision)

> **Offenlegung der Kollision.** Die erste Evaluator-Instanz galt nach zwei Abbrüchen als
> abgestorben (Claim-Nachtrag `966dea39`); ich wurde als zweite frische Instanz mit derselben
> Abnahme gestartet. Während meiner Messung hat die erste Instanz ihr Votum committet
> (`23b3a490`, oben). **Alle meine Messungen liefen davor an einem stillstehenden Stand** —
> beide Scope-Dateien vor und nach jeder Probe byte-identisch mit `85b03d23` (`git diff` je
> 0 Zeilen, md5 `7c71f5ba2daac3601ae9b1ee6fa4a912`); der fremde Commit berührte nur
> STATUS/Trägerblatt, nicht den Scope. Die Voten sind unabhängig entstanden und
> deckungsgleich — dieses hier ist die Zweitbestätigung, das Erstvotum trägt.
>
> **Zweite Offenlegung:** Meine ERSTE Votum-Fassung (geschrieben vor Kenntnis des Erstvotums,
> mit dem inzwischen falschen Satz „keine neuen SPEC-Befunde" und ohne Kollisionskennzeichnung)
> stand kurz auf der Platte und wurde von `4307987b` als Beifang mitcommittet — `7c2958fd`
> stellt das in der Botschaft richtig. **Dieser Commit ersetzt sie durch die vorliegende
> gekennzeichnete Zweitfassung** (daher die Entfernung der 118 Zeilen vor dem Erstvotum);
> inhaltlich sind beide Fassungen bis auf Kennzeichnung und P2-Einarbeitung identisch.

```yaml
auftrag: A-08
commit: 85b03d23        # Pruef-SHA; gemessen am Arbeitsbaum (Scope-Dateien byte-identisch, s. o.)
votum: ABGENOMMEN
fehlerklasse: SPEC      # ausschliesslich der P2-Befund des Erstvotums — von mir REPRODUZIERT, kein neuer
gegenprobe: "eigene Wegwerf-Proben je Kriterium + Zwei-Richtungs-Probe gegen das Basis-Tor c2de1eec + alle sieben Mutationen eigenhaendig gesetzt + P2-Befund selbst reproduziert"
browser: nicht_anwendbar
befunde:
  - "P2 SPEC (Erstvotum, von mir bestaetigt): git-Prozess DIESES Repos mit --git-dir und fremder cwd wird von repo_git_laeuft() nicht erkannt"
```

**Unabhängigkeit:** zuerst Auftrag, Diff (`git diff c2de1eec 85b03d23` — 427 Zeilen) und Code
gelesen, alles selbst gemessen, den Generatorbericht erst danach abgeglichen, das Erstvotum erst
nach Abschluss meiner Messungen gesehen. Scope-Diff `git diff --name-only c2de1eec 85b03d23`
selbst gemessen: exakt die fünf Blatt-Dateien. Statisch: `bash -n` SYNTAX-OK, `node --check`
CHECK-OK.

### Suite, selbst gefahren (Baseline-Paar)

```text
Basis c2de1eec (Basis-Tor eingesetzt, nur A-08-Zusagen):   ℹ tests 8   ℹ pass 3   ℹ fail 5
  rot an der Basis: A-08-1 · A-08-4 · A-08-5 · A-08 Form B · A-08-10
  gewollt gruen (Gegenhalter/Erhaltung): A-08-2 · A-08-4 git-* · A-08-8
Pruef-SHA 85b03d23 (voller Lauf):                           ℹ tests 38  ℹ pass 38  ℹ fail 0
```

### Je Kriterium (verbindliche Lesart: Nachtrag 1–8 + Trägerblatt als 9/10)

**A-08-1 — GRÜN.** Eigene Wegwerf-Probe (0-Byte-Lock, 239 s, lebender node-Halter, kein
Repo-git) am aktuellen Tor:

```text
exit=0
BEISEITE   .git/index.lock  (0 Byte, 240s alt, Halter ohne git-Kommando: 15771 (node),
           kein git-Prozess dieses Repos) -> .git/_locks_beiseite/2026-08-08/
lock-liegt-noch=nein · Datei liegt in _locks_beiseite/2026-08-08/index.lock (NIE geloescht)
```

**Zwei-Richtungs-Probe:** dieselbe Lage gegen das Basis-Tor (`git show c2de1eec:` in
Wegwerf-Kopie): `exit=3`, `GEHALTENER LOCK … Halter: 15841` — rot an der Basis, grün am Bau.
**Realfall-Beleg (zitiert, ersetzt keine Messung):** `.git/_locks_beiseite/2026-08-08/index.lock`
(0 Byte, mtime 07.08. 16:58) — das umgebaute Tor hat am 08.08. im Wildbetrieb einen echten
0-Byte-Lock beiseitegelegt, Original erhalten.

**A-08-2 — GRÜN.** Eigene Probe: 0 Byte, 30 s, lebender Halter → `exit=3`, Lock liegt,
`Der Lock ist juenger als das Altersmass des Tors.` + `ENV_BLOCKED: … (Halter: 15889 (node))`.
Gegenrichtung: Mutation M1 lässt genau diese Zusage fallen.

**A-08-3 / A-08-9 (must_preserve) — GRÜN.** Alle 30 Bestandszusagen im eigenen 38/38-Lauf grün,
namentlich `Tor Teil 2` (Z.115) · `A-02-2` (Z.512) · `A-02-2 GEGENPROBE` · `A-02-1 KONTROLLE`
(Z.547) · `A-02-3` · `A-02-4` (Z.579) · `A-02-4 ROT` · `A-02 Kante 2` (hängendes lsof: 5081 ms
statt Hängen). Eigene Probe der A-02-2-Klasse: 900 B, 400 s, node-Halter → `exit=3`, Lock liegt.
Doppelpfad Z.163 laut Diff unangetastet (Bedingung 3 zitiert ihn, Original im HALTER=0-Zweig).

**A-08-4 — GRÜN.** Code: `HBASE=${HKOMMANDO##*/}` + `case git|git-*`. Eigene Proben: Halter
`git` als Symlink (ps liefert VOLLEN Pfad) → `exit=3`, `Ein Halter traegt ein git-Kommando`;
`git-remote-https` (Suite) grün. **Gegenfall selbst gemessen:** Halter `gitarre` (0 Byte, 300 s)
zählt NICHT als git → beiseite, `exit=0`, `Halter ohne git-Kommando: 16001 (gitarre)` — die
Muster sind exakt, kein Präfix-Befund. Kante `mehrere Halter, EINER git` (node+git): `exit=3`.

**A-08-5 — GRÜN.** Eigene Probe: lebender Halter + stummes fake-`ps` im PATH → `exit=3`, Lock
liegt, `ENV_BLOCKED: halter-kommando nicht ermittelbar — .git/index.lock (Halter: unbekannt)`.

**A-08-6 — GRÜN, alle sieben Mutationen EIGENHÄNDIG gesetzt** (md5 vorher = nachher =
`7c71f5ba2daac3601ae9b1ee6fa4a912`, Endzustand `git diff 85b03d23` 0 Zeilen, finaler Lauf 38/38):

```text
M1 && -> ||                        tests 38  pass 35  fail 3   A-08-2 · A-08-4 · A-08-4 git-*
M2 case erkennt nie git            tests 38  pass 36  fail 2   A-08-4 · A-08-4 git-*
M3 Ergebnis ignoriert              tests 38  pass 36  fail 2   A-08-4 · A-08-4 git-*
M4 Basename -> Pfad-Gleichheit     tests 38  pass 35  fail 3   A-08-4 · git-* · A-08-10
M5 git-* nicht erkannt             tests 38  pass 37  fail 1   A-08-4 git-*
M6 unbekannt = nicht gehalten      tests 38  pass 37  fail 1   A-08-5
M7 0-Byte-Schranke entfernt        tests 38  pass 35  fail 3   A-02-2 · A-02-4 · A-08-10
```

M7 fällt exakt durch die bestehenden Zusagen `A-02-2`/`A-02-4` — der `f5098c40`-Fall ist nicht
stumm grün. *Meine M2/M3-Bauart weicht von der der Erstinstanz ab (deren fail-Zähler 4/2, meine
2/2) — die Mutationsklasse fällt in beiden Fassungen; kein Befund.*

**A-08-7 — GRÜN.** Richtigstellung steht im A-02-Blatt direkt unter der widerlegten Messung,
Original erhalten, Fehlertyp und Verweise (`de33d1e6`, `d377683a`, A-08) benannt.

**A-08-8 — GRÜN.** Am Testcode geprüft: Lock stammt aus `git update-index --index-info` (git
schreibt `index.lock` selbst), `statSync(p).size === 0` asserted, Blockade am LEBENDEN echten
git-Halter vor dem SIGKILL asserted, Name/Kommentar benennen die Herkunft. Kein `writeFileSync`
mit Etikett.

**A-08-10 (P2) — GRÜN.** Eigene Probe am unveränderten Schutzpfad (900 B + Halter):
`ENV_BLOCKED: lock wird gehalten — .git/index.lock (Halter: 15947 (node))`. An der Basis rot
(selbst gemessen: `Halter: 15841` ohne Kommando; `✖ A-08-10` im Basis-Lauf).

**Kanten, stichprobenartig:** mehrere Halter/einer git → liegt · `lsof` fehlt (900 B/400 s,
PATH ohne lsof) → `exit=3`, `Ohne lsof gilt nur: 0 Byte und >=60s alt` — A-02-3-Rückfall
unverändert · hängendes lsof → Zeitgrenze wirkt (5,1 s, Suite).

### Der P2-Befund des Erstvotums — von mir unabhängig REPRODUZIERT

```text
Probe --git-dir (0-Byte-Lock 300 s, kein Halter, git --git-dir=<repo>/.git cat-file --batch, cwd fremd):
  exit=0 · BEISEITE .git/index.lock (0 Byte, 300s alt, kein Halter)   <- die Luecke
Gegenrichtung (git -C <repo>, cwd fremd gestartet):
  exit=3 · LOCK BEI LAUFENDEM GIT … ENV_BLOCKED: git-prozess dieses repos laeuft
```

Einordnung wie im Erstvotum: Klasse `SPEC`, P2, Ball beim Planner als Folgeauftrag — der Bau
folgt der Kantenliste wörtlich („git-Prozess in FREMDEM Verzeichnis zählt nicht", cwd-Kriterium);
blockiert die Abnahme nach §12.5 nicht.

**Gesamturteil: ABGENOMMEN — als unabhängige Zweitbestätigung des Erstvotums.** Alle zehn
Kriterien grün, keine offene P0/P1-Abweichung, keine Regression (30/30-Bestand vollständig im
38/38-Lauf), Mutationsprobe vollständig erneut gefahren (§12.4), Browserabnahme nicht anwendbar.
Einziger Befund ist der bestätigte P2-`SPEC` oben (Folgeauftrag beim Planner). Ball beim
Release-Prüfer.

---

## Bekannte Grenze nach der Abnahme (P2, `SPEC`, Ball beim Planner)

**Der Evaluator hat sie in Probe C gefunden — bei `ABGENOMMEN`, nicht danach:**

```text
git --git-dir=<dieses Repo>/.git … aus FREMDER cwd
  -> repo_git_laeuft() erkennt den Prozess NICHT
  -> der Lock wurde beiseitegelegt und der Commit lief
```

> **Die Lücke steckt im Schnitt, nicht im Bau.** *Meine Kantenliste sagt wörtlich „ein git-Prozess
> in FREMDEM Verzeichnis zählt nicht". **`--git-dir` habe ich nicht bedacht**, und der Bauende hat
> getan, was dastand.*

**Warum die Abnahme zu Recht nicht blockiert ist** — Bewertung des Evaluators, von mir übernommen
und **ausdrücklich nicht nachgemessen**; sein Beleg liegt im Votum `23b3a490`:

```text
die gefaehrliche Lage   deckt Bedingung 1 ab (Halter mit git-Kommando)
die verbreitete Form    `git -C` WIRD erkannt
die Luecke              `--git-dir` aus fremder cwd - selten, aber echt
```

**Kein Kriterium wird nachträglich geändert** — A-08 ist `ABGENOMMEN`, inzwischen mit unabhängigem
**Zweitvotum** (`f430242d`). *Die Grenze steht hier, damit sie niemand neu sucht; ein eigener
Auftrag folgt, wenn wieder Platz ist.*

**Wie er sie gefunden hat, gehört dazu:** drei eigene Torläufe im Wegwerf-Repo mit einem **echten**
0-Byte-Lock — statt den Bericht zu lesen.
