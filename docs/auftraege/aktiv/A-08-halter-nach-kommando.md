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
