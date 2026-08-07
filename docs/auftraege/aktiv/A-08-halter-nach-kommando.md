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

ENTSCHIEDEN:  verwaist = DREI Nein zusammen
              1  kein Halter mit git-Kommando
              2  kein git-Prozess laeuft
              3  Lock ist 0 Byte UND mindestens 60 s alt
              -> dann beiseitelegen nach Yamas Dauerregel; sonst ENV_BLOCKED wie heute
```

*Beide meiner Formen hatten je eine halbe Antwort. **Drei unabhängige Nein sind belastbarer als ein
besseres Ja** — und die dritte Bedingung braucht `lsof` gar nicht.*

**§12.5 angewandt:** **A-02 bleibt `ABGENOMMEN`.** Die Nachbesserung setzt auf `6953198a` auf,
**keine Warteschlange.**

## Akzeptanzkriterien

**A-08-1 (P1, KORRIGIERT 07.08. — die dritte Bedingung war ein Rückschritt):** Ein Lock gilt genau
dann als **verwaist**, wenn **alle drei** zutreffen:

```text
1  kein Halter mit `git`-Kommando
2  kein laufender `git`-Prozess
3  das BESTEHENDE Alters-/Groessenmass des Tors ist erfuellt - unveraendert, beide Pfade
```

Dann wird er **beiseitegelegt, nie gelöscht**. *Rot heute: derselbe Fall endet in `ENV_BLOCKED`.*

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

**A-08-2 (P1, Gegenprobe):** Fehlt **eine** der drei Bedingungen, bleibt es bei `ENV_BLOCKED` und
`exit 3`. *Ohne dieses Kriterium wäre „alles ist verwaist" grün — schlimmer als die Blockade.*

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
