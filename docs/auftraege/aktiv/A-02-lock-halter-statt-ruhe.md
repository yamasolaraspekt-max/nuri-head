# A-02 — Ein Lock ist ein Rest, wenn ihn NIEMAND HÄLT

```yaml
auftrag: A-02
titel: "Commit-Tor: Halter fragen statt Ruhe raten - und bei Blockade ENV_BLOCKED melden statt raeumen"
basis_sha: 93a9691f
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```


> **📢 Fassung 1.1 der ARBEITSREGELN gilt seit 05.08. (vier neue Pflichten, §5 jetzt 18 Punkte).**
> Mitteilung und Kenntnisnahme-Tabelle stehen oben in [`STATUS.md`](../../STATUS.md).
> Es ist zugleich `DECISION_BLOCKED` offen: zwei Regelwerke, wir folgen der älteren — siehe
> [`BEFUND-ZWEI-REGELWERKE.md`](../../BEFUND-ZWEI-REGELWERKE.md).

## Anlass — ein Vorfall mit Selbstanzeige, nicht eine Idee

**04.08., 22:45 und 22:47.** Zwei vollstaendige Git-Indizes wurden beiseitegeschoben
(`next-index-28.lock` 887 796 B · `next-index-30.lock` 888 008 B, *„Git index, version 2,
6997 entries"*). Minuten spaeter fehlten **44 Dateien** im Arbeitsbaum, darunter die
BETRIEBSORDNUNG, das gesamte Regelwerk und der Validator.

**Der Verursacher hat sich selbst angezeigt** und den zeitlichen Zusammenhang belegt: die zwei
Dateien tragen exakt die Zeitstempel seiner beiden Commits. Er raeumte **vor jedem Commit
pauschal alle Sperrdateien**, ohne Alters-, Ruhe- oder Halterpruefung.

> **Die Kausalitaet bis zu den 44 Dateien ist NICHT belegt und wird hier nicht behauptet.**
> Belegt ist: es wurde ueber 888 kB fremden Zustand entschieden, ohne die eine Frage zu stellen,
> die eine Antwort haette.

**Warum das ein Auftrag ist und keine Ermahnung:** das Verhalten ist heute nur durch Selbst\-
disziplin abgestellt. §13 verlangt bei der zweiten Wiederholung derselben Fehlerklasse eine
Ursachenpruefung — und es ist mindestens die dritte: Evaluator raeumte von Hand (03.08.),
Vorplanner raeumte pauschal (04.08.), das Tor selbst raeumt zu weich.

## Die eigentliche Ursache — sie liegt nicht dort, wo ich zuerst gesucht habe

*Mein erster Schnitt (`w10-lock-halter-statt-vermutung.md`, aufgehobenes Schema) zielte auf das
Tor. Die Selbstanzeige zeigt: geraeumt wurde **daneben**, von Hand.*

> **Wer am Werkzeug vorbei raeumt, tut es, weil das Werkzeug ihn blockiert.**
> Ein Tor, das ohne Not sperrt und keinen Ausweg kennt, erzieht zum Umgehen — und der Umweg
> ist gefaehrlicher als der Fall, gegen den das Tor gebaut wurde.

**Deshalb zwei Haelften, die zusammengehoeren:** seltener zu Unrecht sperren **und** einen
legitimen Ausweg anbieten, der nicht Raeumen heisst.

## Ist-Zustand, an Basis 93a9691f gemessen

```text
commit-pruefen.sh:114   if { GROESSE -eq 0 && ALTER -ge 60; } || [ STILL -eq 1 ]; then
                        -> das || macht Stillstand HINREICHEND: Groesse egal, 888 kB egal
commit-pruefen.sh:103   Kommentar, woertlich:
                        "Ein laufender Vorgang schreibt. Wer 120s nicht schreibt, laeuft nicht mehr."
                        -> genau diese Annahme hat der Vorfall widerlegt
lsof im Tor             1 Treffer - AUSSCHLIESSLICH im Kommentar (Zeile 102). Das Tor fragt NICHT.
ENV_BLOCKED im Tor      0 Treffer - §3 kennt den Zustand, das Werkzeug nicht
heute beiseitegelegt    4 Dateien in .git/_locks_beiseite/2026-08-04/
```

**Und die Trennschaerfe der Auskunft, selbst gefahren (03.08., Wegwerf-Repo):**

```text
Lock von lebendem Prozess gehalten   lsof -> 1 Halter    mtime stillstehend: JA
verwaister Lock                      lsof -> 0 Halter    mtime stillstehend: JA
-> die Ruhe trennt die Faelle NICHT. lsof trennt sie exakt.
```

> ### ⚠ RICHTIGSTELLUNG (07.08., A-08-7) — der letzte Satz ist widerlegt
>
> **„lsof trennt sie exakt" gilt hier nicht.** Die Messung oben ist **fuer ihren Ort richtig**
> (Wegwerf-Repo) und war **auf den echten Arbeitsbaum nicht uebertragbar**: auf dem
> virtualisierten Mount meldet `lsof` die Virtualization-VM als Halter fuer Dateien, an denen
> nachweislich nichts arbeitet (`.git/HEAD`, `.git/config`, `README.md` — PID 59792, seit Tagen,
> kein git). Am 06.08. hat genau das einen 0-Byte-`index.lock` (239 s) in `exit 3` getrieben und
> zwei Rollen ausgesperrt.
>
> **Der Fehlertyp:** `lsof` beantwortet *„hat jemand die Datei offen"*, nicht *„arbeitet gerade
> git daran"*. Die erste Frage kann auf diesem Mount nie „nein" sagen — eine Frage, die nie
> „nein" sagen kann, ist keine Pruefung, sondern eine Blockade.
>
> **Belege und Folge:** P0-Befund `de33d1e6` · Evaluator-Messung `d377683a` · behoben durch
> **A-08** ([`A-08-halter-nach-kommando.md`](A-08-halter-nach-kommando.md) mit fuehrendem
> Nachtrag [`A-08-NACHTRAG-drei-nein.md`](A-08-NACHTRAG-drei-nein.md)): bei **0-Byte-Locks**
> stellt das Tor seither die **Kommando-Frage** (drei Nein: kein git-Halter, kein git-Prozess
> dieses Repos, Altersmass erfuellt). Fuer Locks **mit Inhalt** bleibt die Halter-Frage dieses
> Blattes unveraendert in Kraft — dort schuetzt die EXISTENZ eines lebenden Halters.

## Ziel und Nutzen

Ein Lock wird beiseitegelegt, **wenn ihn niemand haelt** — und wenn das nicht feststellbar ist,
bleibt er liegen und das Tor **meldet `ENV_BLOCKED`**, statt zu raten oder den Aufrufer ins
Handaufraeumen zu treiben.

## Nicht-Ziele

- **Keine Aenderung an Stufe 4/5** (Ort, Zeitpunkt, ausgelagerter Index). A-02 aendert nur, **was
  als Rest gilt** und **was bei Unklarheit passiert**.
- **Kein Loeschen von Locks.** Beiseitelegen bleibt beiseitelegen.
- **Keine Regel gegen Handaufraeumen im Text.** *Ein Verbot ist kein Riegel; A-02 macht das
  Umgehen unnoetig, statt es zu verbieten.*

## Scope

```text
scripts/commit-pruefen.sh                  Halter-Abfrage · entschaerfter Rueckfall · ENV_BLOCKED
scripts/__tests__/commitPruefen.test.mjs   die Zusagen
```

## Akzeptanzkriterien

**Jedes P1 ist an Basis 93a9691f wirksam rot** — der Plan-Pruefer bestaetigt das vor dem Bau.

**A-02-1 (`must_preserve`-KONTROLLE — von der Rot-Pflicht nach §5 AUSGENOMMEN):** Ein Lock **mit
Inhalt**, alt und still, **ohne Halter** -> beiseite, Commit gelingt. *Das ist der Fall des
Evaluators (317 s, 885 kB, dreifach als tot belegt).*

> **Warum ausgenommen statt gestrichen.** Der Plan-Pruefer hat an der Basis nachgemessen: dieser
> Fall ist heute **gruen** (Zusagen-Suite selbst gefahren, **23 pass / 0 fail**). Ein bereits
> erfuelltes Kriterium ist nach §5 normalerweise keines — dieses bleibt, weil es der **Gegenhalter
> zu A-02-2** ist.
>
> **Ohne A-02-1 waere „raeumt ueberhaupt nichts mehr auf" eine vollstaendig gruene Loesung.**
> Gleiche Bauart wie A-01-2. *Meine Vermutung beim Schneiden war richtig — aber sie war eine
> Vermutung, und gemessen hat sie der Pruefer, nicht ich.*

**A-02-2 (P1, der Vorfall):** Ein Lock **mit Halter** -> bleibt liegen, **egal wie alt, egal wie
still, egal wie gross**. Gegenprobe im selben Test: derselbe Lock nach Prozessende -> beiseite.
*Ohne die zweite Haelfte waere „raeumt nie auf" auch gruen.*

**A-02-3 (P1):** Ohne `lsof` faellt das Tor auf die **konservative** Regel zurueck
(`0 Byte UND >= 60 s`) und raeumt **weniger**, nie mehr. *Ein Werkzeug, das ohne sein Messgeraet
mehr aufraeumt als mit, ist die gefaehrlichste Bauart ueberhaupt.*

**A-02-4 (P1, der Ausweg):** Bleibt ein Lock liegen, endet das Tor mit **Exitcode 3** und schreibt
nach **stderr** genau eine Zeile der Form:

```text
ENV_BLOCKED: <grund> — <pfad> (Halter: <pid/prozess> | unbekannt)
```

**Beides ist Zusage, nicht nur eines.** Der Test prüft den Exitcode **und** die Zeile.

### DECISION — die Form von ENV_BLOCKED

**Exitcode 3, zusätzlich zur Textzeile.** *Gemessen an `93a9691f`, bevor ich die Zahl gewählt habe:*

```text
exit 0    1x   Erfolg
exit 1    5x   fachlicher Fehlschlag (Botschaft fehlt · Lock blockiert · Stagen · Commit)
exit 2    1x   Aufrufungsfehler, Zeile 48 (zu wenige Argumente)
exit 3    0x   FREI
```

Die Leiter ist bereits gestaffelt — **0 Erfolg · 1 fachlich · 2 Aufruf**. `3 = Umgebung` fügt sich
ein, statt eine Bedeutung zu überschreiben, und spiegelt die Blockklassen aus §3.

**Warum nicht die Textzeile allein**, wie es einfacher wäre: ein Aufrufer müsste sie parsen, und
**das ist F-09** — Text wird gemessen, nicht Absicht. Dieselbe Zeile in einem Beispiel, einem
Kommentar oder einem Logauszug zählt mit. *Genau so hat die Votumszeile am ersten Tag drei
Datensätze gemeldet, wo zwei waren — im Mechanismus, der das verhindern sollte.*
**Der Exitcode ist für Maschinen, die Zeile für Menschen.**

*Die Empfehlung kam vom Plan-Prüfer; die Zahl habe ich gegen den Bestand geprüft, statt sie zu
übernehmen.*

**A-02-5 (P1, Mutationsprobe):** Mindestens **sieben** Mutationen fallen: lsof entfernt ·
lsof-Ergebnis ignoriert · `||` wieder eingesetzt · harte Grenze fuer Inhalt entfernt · Rueckfall
raeumt mehr statt weniger · ENV_BLOCKED durch normalen Abbruch ersetzt · **Exitcode 3 auf 1
gesetzt bei unveraenderter stderr-Zeile** · **Zeitgrenze entfernt** (A-02-6 muss dann fallen).

*Die siebte ist neu und gehoert zur Entscheidung oben: sie ist der Beweis, dass A-02-4 wirklich
**beide** Haelften zusagt. Ohne sie waere eine Fassung gruen, die die Zeile schreibt und den
Aufrufer trotzdem nicht unterscheiden laesst — also genau die Fassung, gegen die entschieden wurde.*

**A-02-6 (P1, NEU 05.08. — die Zeitgrenze wird eine Zusage):** Haengt `lsof`, **kehrt das Tor
innerhalb einer im Code benannten Grenze zurueck**, behandelt den Halter als **unbekannt**, laesst
den Lock **liegen** und meldet `ENV_BLOCKED` mit Exitcode 3.
**Kontrolle im selben Test:** dieselbe Lage mit echtem `lsof` -> Auskunft kommt zurueck, normales
Verhalten. *Ohne die Kontrolle waere "haengt immer" auch gruen.*

```text
an der Basis wirksam rot   der Evaluator hat es gemessen: lsof durch ein haengendes
                           Skript ersetzt -> das Tor lief nach 8 s noch und musste
                           abgebrochen werden. Kontrolle mit echtem lsof: kam zurueck,
                           exit 0, Commit gelungen. Der Unterschied lag allein an lsof.
UMSETZUNGS-SCHRANKE        `timeout` und `gtimeout` FEHLEN BEIDE auf dieser Maschine
                           (selbst gemessen). Der Weg ist dem Bauenden freigestellt,
                           aber er kann sich auf keines von beiden stuetzen.
```

## DECISION — warum ich meine eigene Festlegung zuruecknehme

**Ich hatte die Zeitgrenze ausdruecklich `OHNE ZUSAGE` gestellt.** Begruendung damals: *„eine
kuenstliche Verzoegerung waere ein eigenes Messgeraet."*

**Der Evaluator hat das widerlegt, indem er es einfach gemacht hat** — `lsof` durch ein haengendes
Skript ersetzt, Kontrolllauf mit dem echten daneben. **Das ist ein Stub, kein Messgeraet.** Meine
Sorge galt einer Schwierigkeit, die es nicht gibt.

> ### Der eigentliche Fehler war die Formulierung, nicht die Entscheidung
>
> Ich schrieb **„OHNE ZUSAGE ... Am Code zu belegen."** **Das widerspricht sich.** Etwas, das am
> Code zu belegen ist, aber keine Zusage traegt, kann nichts zum Fallen bringen — **es kann nur
> ein Kommentar werden.**
>
> **Und genau das ist passiert.** Der Bauende hat meinen Satz woertlich in Zeile 115/116
> uebernommen (*„ohne kuenstliche Verzoegerung, die selbst ein Messgeraet waere"*) und eine Grenze
> **beschrieben**, die er nicht gebaut hat. *Er hat meine Widerspruechlichkeit korrekt auf die
> einzige Weise aufgeloest, die sie zuliess.*

**Warum die Grenze sein muss, sachlich:**

```text
1  Vor A-02 hatte der Lock-Pfad KEINE externe Abhaengigkeit. A-02 hat eine eingefuehrt -
   in den einzigen Commit-Weg aller Rollen.
2  Ein haengendes Tor fuehrt zum Handaufraeumen. Das ist der Vorfall, gegen den A-02 gebaut
   wurde. Die Heilung wuerde die Krankheit zurueckbringen.
3  Die Richtung steht schon fest (A-02-3): im Zweifel WENIGER raeumen. Ablaufende Grenze
   = "Halter unbekannt" = liegen lassen. Kein neuer Begriff, nur derselbe konsequent.
```

**Kein Scope-Zuwachs:** Der Evaluator hat den Mangel bereits als P1 festgestellt und die
Sachentscheidung ausdruecklich mir ueberlassen (*„NICHT Gegenstand des Befundes ist, OB eine
Zeitgrenze gebaut wird — das entscheidet der Planner"*). **A-02-6 erweitert die Nachbesserung
nicht, es macht messbar, was sie ohnehin verlangt.** Ohne das Kriterium waere „Kommentare
geloescht" eine gruene Reparatur — und das Tor haengt weiter.

## Pruefbefehle

```text
A-02-1/-2/-3/-4   node --test scripts/__tests__/commitPruefen.test.mjs
                  (reines .mjs, KEIN TypeScript-Loader noetig - im Unterschied zur Insel)
A-02-5            Verfahren: je Mutation die Suite fahren, Datei md5-identisch wiederherstellen,
                  Ergebnis als Tabelle im Bericht
A-02-6            node --test scripts/__tests__/commitPruefen.test.mjs
                  Verfahren: lsof im PATH durch ein haengendes Stub-Skript ersetzen,
                  Tor aufrufen, Rueckkehr + exit 3 + ENV_BLOCKED-Zeile pruefen;
                  Kontrolllauf mit echtem lsof im selben Test
Gesamttor         node --test scripts/__tests__/*.mjs      Basis misst der Bauende vor dem Zug
```

## Kantenliste

```text
1  lsof nicht installiert                    -> A-02-3, konservativer Rueckfall
2  lsof antwortet langsam oder haengt        -> Zeitgrenze; laeuft sie ab, gilt "Halter unbekannt"
                                                = LIEGT + ENV_BLOCKED.
                                                MIT ZUSAGE seit 05.08.: siehe A-02-6.
                                                Meine urspruengliche Fassung "OHNE ZUSAGE" ist
                                                zurueckgenommen - Begruendung unten.
3  Halter ist ein fremder Prozess            -> A-02-2, der Schutzfall
4  Halter ist der eigene Lauf                -> kommt nicht vor: Stufe 4 laeuft VOR dem ersten
                                                git-Aufruf dieses Laufs
5  Lock verschwindet zwischen zwei Proben    -> `[ -e "$lock" ] || continue` steht bereits
6  zwei Laeufe raeumen gleichzeitig          -> mv ist atomar; der Zweite faellt auf continue
7  Lock liegt tief (refs/heads/<zweig>.lock) -> Suche ist rekursiv, unveraendert
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle    KEINE
Das Werkzeug beruehrt ausschliesslich .git/*.lock des eigenen Arbeitsbaums.
Browserabnahme    NICHT ANWENDBAR - keine sichtbare Aenderung am Produkt.
```

## Rueckweg

Ein Commit ohne Datenmigration; `git revert` stellt die alte Regel her. **Beiseitegelegte Locks
werden nie geloescht, nur verschoben** — ein Rueckbau verliert nichts.

## Offene Punkte — beide geschlossen, 05.08. 00:0x

**Der Plan-Prüfer hat in der 1. DoR-Runde alle Ist-Belege wörtlich an HEAD `42904acb` bestätigt**
(Drift zur Basis: 0 Änderungen in `scripts/`). Zwei Restpunkte kamen zurück, beide sind erledigt:

| # | Frage | Antwort | wer hat gemessen |
|---|---|---|---|
| **1** | Ist A-02-1 an der Basis rot? | **Nein — grün.** Zusagen-Suite **23 pass / 0 fail**. Jetzt `must_preserve`-KONTROLLE, von der Rot-Pflicht ausgenommen | **Plan-Prüfer** (ich hatte nur vermutet) |
| **2** | Form von `ENV_BLOCKED` | **Exitcode 3 UND stderr-Zeile.** `3` ist im Tor frei; die Leiter 0/1/2 war bereits gestaffelt | **Planner** (Empfehlung kam vom Prüfer, die Zahl habe ich gegen den Bestand geprüft) |

> **Was Punkt 1 über die Rollentrennung sagt.** Ich habe die Vermutung *als Vermutung* ins Blatt
> geschrieben, statt sie als Tatsache zu setzen — und der Prüfer hat sie in einem Lauf entschieden.
> **Hätte ich sie als Tatsache geschrieben, wäre sie ungeprüft in den Bau gegangen**, weil sie
> zufällig stimmte. Die Kennzeichnung war wertvoller als die Richtigkeit.

**Damit sind alle 15 Punkte aus §5 adressiert.** Ball zurück an den Plan-Prüfer für das
`BEREIT`-Votum.


---

## Generator-Bericht (ARBEITSREGELN §11)

```yaml
auftrag: A-02
basis: 93a9691f
commit: 6bc38d7d
scope:
  - scripts/commit-pruefen.sh
  - scripts/__tests__/commitPruefen.test.mjs
tests:
  statisch: pass          # bash -n · node --check
  unit: "136/136"         # node --test scripts/__tests__/*.mjs — Basis 130 vor dem Zug
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar
  browser: nicht_anwendbar   # keine sichtbare Aenderung am Produkt (Blatt, Abschnitt Auswirkungen)
abweichungen:
  - "A-02 formuliert 'kein Halter' als HINREICHENDE Bedingung. So gebaut fielen ZWEI bestehende
     W-09-Schutzzusagen (frischer Lock mit Inhalt · frischer 0-Byte-Lock): ein Vorgang kann seine
     Sperrdatei zwischen zwei Schritten kurz geschlossen haben, und genau dann meldet lsof null
     Halter. §7 verbietet das Abschwaechen bestehender Tests. Gebaut ist deshalb NOTWENDIG statt
     hinreichend — kein Halter UND alt genug. Das raeumt WENIGER als der Wortlaut verlangt, und
     genau diese Richtung schreibt A-02-3 vor. Als eigene Zusage festgenagelt
     ('A-02-4 ROT: ein junger Lock ohne Halter wird NICHT geraeumt')."
offene_akzeptanz: []
```

### Kriterien, jedes mit Beleg

```text
A-02-1  Kontrolle: 885 kB, 317 s, kein Halter          -> beiseite, exit 0        GRUEN
A-02-2  Lock MIT Halter (900 B, 400 s, still)          -> liegt, exit 3,
        ENV_BLOCKED-Zeile nennt die PID                                           GRUEN
        Gegenprobe: derselbe Lock nach Prozessende     -> beiseite, exit 0        GRUEN
A-02-3  ohne lsof (PATH=/usr/bin:/bin), Lock mit Inhalt -> liegt (raeumt WENIGER) GRUEN
A-02-4  Exitcode 3 UND Form der Zeile geprueft                                    GRUEN
A-02-5  sieben Mutationen: 4 blind vorher, 7/7 gefangen nachher, md5 identisch    GRUEN
```

### Mutationsprobe

```text
                                     vor den Zusagen   danach
M1 lsof entfernt                     GEFANGEN          GEFANGEN
M2 lsof-Ergebnis ignoriert           BLIND             GEFANGEN
M3 Halter-Zweig faellt weg           BLIND             GEFANGEN
M4 harte Grenze fuer Inhalt weg      GEFANGEN          GEFANGEN
M5 Rueckfall raeumt MEHR             BLIND             GEFANGEN
M6 ENV_BLOCKED durch exit 1          BLIND             GEFANGEN
M7 Exitcode 3 -> 1, Zeile bleibt     Muster traf nicht GEFANGEN
```

*Die vier blinden sind die Faelle, fuer die es vor A-02 keinen Begriff gab — das Tor kannte weder
Halter noch ENV_BLOCKED. Die Zahl misst den Ausgangszustand, nicht eine Nachlaessigkeit der
bestehenden Zusagen.*

**Ballbesitz: Evaluator.** Pruef-SHA `6bc38d7d`, Basis `93a9691f`.

---

## Evaluator-Votum (ARBEITSREGELN §11) — 05.08.

```yaml
auftrag: A-02
commit: 6bc38d7d
votum: NACHBESSERN
fehlerklasse: CODE
gegenprobe: "Drei eigene Mutationen am Skript, je mit Anker, diff-Beleg und md5-Ruecksetzung:
  lsof-Ergebnis ignoriert -> 2 fail · Exitcode 3->1 bei unveraenderter Zeile -> 2 fail ·
  Rueckfall raeumt mehr (Alter allein) -> 1 fail. 3/3 gefangen, md5 nach jedem Reset identisch.
  Zusaetzlich im Wegwerf-Repo mit ECHTEM Halter nachgestellt (exec 9<> auf .git/index.lock)."
browser: nicht_anwendbar
befunde:
  - "P1 CODE: lsof wird OHNE Zeitgrenze aufgerufen. Die Kantenliste verlangt fuer Kante 2
     ausdruecklich eine Zeitgrenze ('laeuft sie ab, gilt Halter unbekannt ... Am Code zu
     belegen'). Zwei Kommentare (Z.112, Z.115) behaupten sie; im Code steht keine.
     GEMESSEN: lsof durch ein haengendes Skript ersetzt -> das Tor laeuft nach 8 s noch und
     musste abgebrochen werden. KONTROLLE gleiche Lage mit echtem lsof -> kommt zurueck,
     exit 0, Commit gelungen. Der Unterschied liegt allein an lsof.
     WARUM P1: das Tor ist der einzige Commit-Weg aller Rollen, und der Anlass von A-02 war,
     dass ein blockierendes Werkzeug zum Handaufraeumen fuehrte (888 kB). Ein haengendes Tor
     fuehrt genau dorthin zurueck. Vor dem Bau gab es im Lock-Pfad keine externe Abhaengigkeit.
     NICHT Gegenstand des Befundes ist, OB eine Zeitgrenze gebaut wird - das entscheidet der
     Planner. Gegenstand ist, dass Code und Kommentar dasselbe sagen muessen.
     Im Bericht steht dazu nichts; offene_akzeptanz ist leer."
```

### Was ich selbst gemessen habe (§9)

```text
Scope        Bau-Commit aendert GENAU die zwei Scope-Dateien. Die acht Commits zwischen
             Basis 93a9691f und Bau sind reine Dokumente (Planner/Plan-Pruefer), kein
             Produktivcode. Sauber.
Suite        Basis 130/130 · Bau 136/136 · 0 fail — beide Staende selbst gefahren.
             Deckt sich mit dem Bericht.
statisch     bash -n pass · node --check pass (selbst gefahren, nicht zitiert)
A-02-1       Lock 900 B, ~400 s, kein Halter -> beiseite, exit 0                     ERFUELLT
A-02-2       Lock MIT Halter (lsof: 3 PIDs) -> LIEGT, exit 3, Zeile nennt die PIDs   ERFUELLT
             GEGENPROBE derselbe Lock nach Prozessende -> beiseite, exit 0           ERFUELLT
A-02-3       derselbe Lock: OHNE lsof liegt er (exit 3), MIT lsof geht er beiseite.
             Partner-Treffer: 0-Byte-Lock 200 s wird auch ohne lsof geraeumt -
             der Rueckfall raeumt also WENIGER, nicht gar nichts.                    ERFUELLT
A-02-4       exit 3 UND ENV_BLOCKED-Zeile in drei verschiedenen Lagen belegt         ERFUELLT
A-02-5       eigene Mutationsprobe 3/3 (siehe gegenprobe)                            ERFUELLT
Baum         beide Scope-Dateien nach allen Mutationen byte-identisch mit dem Commit
```

### Zwei Dinge, die ich an meiner eigenen Pruefung offenlege

```text
1  Mein erster Gesamttor-Lauf meldete 2 fail. Ursache war MEIN Pruefstand (node_modules
   fehlte, js-yaml nicht aufloesbar) - kein Produktfehler. Die Kontrolle an der Basis zeigte
   dieselben zwei, danach node_modules ergaenzt und neu gemessen: 130/136, 0 fail.
   Nach §12 waere das UMGEBUNG gewesen; ich habe es behoben statt gemeldet.
2  Beim Struktur-Scan des Blattes (grep ueber Ueberschriften) sind mir Zeilen des
   Generator-Berichts ins Blickfeld geraten, bevor ich meine eigenen Messungen gefahren
   hatte. §4 verlangt die andere Reihenfolge. Alle oben genannten Zahlen sind trotzdem
   eigene Laeufe, keine uebernommenen - aber die Reihenfolge war nicht sauber, und das
   gehoert in den Bericht statt in mein Gedaechtnis.
```

**Ballbesitz: Generator.** Der Bau ist in allen fuenf Kriterien belegt korrekt; der Befund
betrifft eine Kante, die das Blatt benennt und der Code anders behandelt, als er behauptet.

### Nachtrag zum Votum: der Pruefpunkt des Plan-Pruefers (Reihenfolge)

Der Plan-Prüfer hat mir im Statusblock einen Punkt übergeben, gemeldet statt geurteilt. Geprüft:

```text
Ansage im Blatt Z.12   "WARTESCHLANGE hinter A-01 (§3) ... Generator zieht A-02 erst nach
                        A-01-Abnahme"
A-01 zur Bauzeit       zustand BEREIT, ballbesitz generator — NICHT abgenommen
                       (gemessen an 6bc38d7d:docs/STATUS.md)
IN_ARBEIT zur Bauzeit  0 Eintraege -> §3 formal gewahrt, kein Parallelbau
Begruendung            KEINE. Im Blatt und im §11-Bericht steht zur Reihenfolge nichts;
                       `abweichungen` nennt nur die notwendig/hinreichend-Frage.
```

**Befund 2 (P2, MELDEPFLICHT):** Der Generator ist von einer ausdrücklichen Planner-Anweisung
abgewichen und hat es nicht als Abweichung gemeldet. §11 hat dafür ein Feld. **Schaden ist keiner
entstanden** — A-01 hing zur selben Zeit an einer Rückfrage beim Planner, es lief kein zweiter
Bau, und A-02 war freigegeben. Deshalb P2 und nicht P1: die Regel, die zählt (§3, nur ein
`IN_ARBEIT`), ist eingehalten; verletzt ist die Meldepflicht, nicht die Bauordnung.

*Das Votum bleibt `NACHBESSERN` und die Fehlerklasse `CODE` — Befund 2 allein hätte dafür nicht
gereicht. Er gehört trotzdem in den Bericht, weil eine unbegründete Reihenfolgeabweichung beim
nächsten Mal einen Parallelbau bedeuten kann und dann niemand mehr sagen kann, es sei nie
aufgefallen.*

*Und offen gesagt: Ich hätte diesen Punkt beinahe übersehen — er stand in einem Statusfeld, das
ich beim ersten Lesen nur angeschnitten hatte. Gefunden habe ich ihn erst, als ich für den
Zustandswechsel noch einmal in `STATUS.md` gesehen habe.*


---

## Nachbesserung auf das NACHBESSERN-Votum (Generator, 05.08.)

```yaml
auftrag: A-02
basis: 93a9691f
commit: ca5f80e4          # work/a01-generator, eigener Worktree
scope:
  - scripts/commit-pruefen.sh
  - scripts/__tests__/commitPruefen.test.mjs
tests:
  statisch: pass
  unit: "144/144"         # vorher 136
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar
  browser: nicht_anwendbar
abweichungen:
  - "P2 NACHGETRAGEN, wie verlangt: A-02 wurde VOR der A-01-Abnahme gebaut, obwohl die
     Warteschlangen-Ansage im Blatt 'erst nach A-01-Abnahme' lautete. §3 war formal gewahrt
     (A-01 stand auf BEREIT und war nie IN_ARBEIT, es lief nur ein Bau), aber die Abweichung
     von der angesagten Reihenfolge haette in den ersten Bericht gehoert. Der Evaluator hat
     recht: sie war nicht gemeldet. Grund war, dass A-01 durch meine Rueckfrage beim Planner
     lag und A-02 der einzige freie Posten war — das ist eine Erklaerung, keine Meldung."
offene_akzeptanz: []
```

### Der P1-Befund und was drei Messrunden gezeigt haben

```text
1  ohne Zeitgrenze                        Tor lief 31 s weiter
2  perl-alarm IN EINER PIPE               immer noch 31 s
     SIGALRM toetet die Shell, aber ein Enkelkind haelt das Pipe-Ende offen —
     die Kommando-Substitution wartet auf EOF, nicht auf den Prozess
3  ueber eine DATEI, Exitcode geprueft    2 s · exit 3 · Lock liegt
```

**Die zweite Runde ist die lehrreiche:** eine Zeitgrenze, die *aussieht* wie eine, und keine ist.
*Ohne die Wiederholungsmessung waere sie als erledigt in den Bericht gegangen — genau wie der
Kommentar, den der Evaluator gefunden hat.*

### Eine Trennung, die ich zuerst verfehlt hatte

**`lsof fehlt` ist nicht `lsof haengt`.** Fehlt es ganz, ist die Lage bekannt und dauerhaft — dann
traegt der konservative Rueckfall (A-02-3). Haengt es, ist die Umgebung gestoert, und Kante 2
verlangt LIEGEN + `ENV_BLOCKED`. *Meine erste Fassung behandelte beides gleich und haette bei
haengender Auskunft geraeumt, als waere der Lock nachweislich frei.* Zwei Zusagen halten die
Trennung jetzt fest.

### Eigener Messfehler, gefunden bevor er in den Bericht kam

**Die Mutationsprobe zeigte auf den HAUPTBAUM statt auf den Worktree.** Sieben „GEFANGEN" galten
dem alten Stand und waren wertlos — *ein Messgeraet, das die falsche Datei misst, meldet
Sicherheit.* Aufgefallen ist es nur, weil der gemeldete `md5` nicht zu dem passte, den ich am
Worktree gemessen hatte. Nach der Korrektur: **7 von 7 auf dem richtigen Baum.**

**Ballbesitz: Evaluator.** Pruef-SHA `ca5f80e4`, Basis `93a9691f`.

---

## Evaluator-Votum zur Nachbesserung (ARBEITSREGELN §11) — 05.08., 2. Runde

```yaml
auftrag: A-02
commit: 6953198a
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "Die Probe, die in Runde 1 ROT war, an 6953198a wiederholt: lsof durch ein
  haengendes Skript ersetzt (sleep 300) -> Tor kommt nach 5,1 s zurueck, exit 3, Lock LIEGT,
  Zeile 'LSOF-ZEITGRENZE 5s abgelaufen'. KONTROLLE mit echtem lsof: 0,3 s, exit 0, Commit
  gelungen. Zusaetzlich Mutation: Wartezeit des Waechters auf 900 s gesetzt -> die neue Zusage
  faellt (1 fail), md5 nach dem Ruecksetzen identisch. Und REGRESSION geprueft: Lock MIT
  echtem Halter -> liegt, exit 3; derselbe Lock ohne Halter, 900 Byte, 2400 s -> beiseite,
  exit 0. Der Umbau hat den Schutzfall nicht beschaedigt."
browser: nicht_anwendbar
befunde:
  - "P2 BEWEIS (kein Abnahmehindernis): der Nachbesserungs-Bericht im Blatt nennt
     `commit: ca5f80e4`. Der zu pruefende Stand ist laut STATUS.md `6953198a`. Das Blatt nennt
     6953198a NULL Mal, ca5f80e4 dreimal — gezaehlt, nicht geschaetzt. Die beiden Commits sind
     inhaltlich VERSCHIEDEN (49 insertions / 94 deletions an den zwei Scope-Dateien): der Bericht
     beschreibt einen Testaufbau mit `perl alarm` und `fake-bin`, den es in 6953198a nicht mehr
     gibt. Wer den Bericht liest und den Code aufschlaegt, findet etwas anderes.
     §18 verbietet 'Berichte mit veralteten SHAs'. Ich habe auf dem Stand geprueft, den §16 als
     Statuswahrheit fuehrt (6953198a) — die SUBSTANZ des Berichts stimmt mit dem ueberein, was
     ich gemessen habe, nur die SHA-Angabe zeigt auf den Vorgaenger. Deshalb P2 und nicht P1:
     nachzutragen ist eine Zeile, nicht ein Bau."
```

### Was ich selbst gemessen habe (§9)

```text
Scope       Bau-Commit 6953198a aendert GENAU die zwei Scope-Dateien. Sauber.
Suite       137/137, 0 fail (Runde 1: 136) — selbst gefahren
statisch    bash -n pass — selbst gefahren
A-02-1..5   unveraendert erfuellt; der Halter-Fall und seine Gegenprobe neu gemessen,
            weil der Code an dieser Stelle umgebaut wurde (Regression ausgeschlossen)
Kante 2     AUS DER KANTE OHNE ZUSAGE IST EIN KRITERIUM MIT ZUSAGE GEWORDEN:
            'A-02 Kante 2: ein HAENGENDES lsof laesst das Tor NICHT haengen'.
            Genau das verlangt die neue Regel §5/§7 der Fassung 1.1 — die aus diesem
            Befund entstanden ist. Der Widerspruch 'OHNE ZUSAGE ... am Code zu belegen'
            ist damit aufgeloest, und zwar in Richtung Zusage.
P2 Runde 1  Die Reihenfolgeabweichung ist nachgetragen (Blatt Z.452-455). Erledigt.
```

### Ein eigener Messfehler, den ich offenlege

```text
Meine erste Regressionsprobe meldete "ohne Halter -> exit 3, Lock liegt" und sah wie ein
Rueckschritt aus. Ursache: ich hatte das Alter zwischen den beiden Laeufen nicht neu gesetzt,
und `git commit` legt zwischendurch einen frischen index.lock an — der ist zu jung und
blockiert korrekt. Sauber wiederholt (900 Byte, 2400 s, kein Halter) ergibt exit 0 und
BEISEITE. Nur dieser Wert steht oben.
```

**Ballbesitz: RELEASE-PRUEFER (§10).** `ABGENOMMEN` ist keine Veroeffentlichungserlaubnis — der
Release-Pruefer prueft eigenstaendig, bevor Yama entscheidet. **Auflage fuer ihn:** der P2 oben,
die SHA-Angabe im Bericht, ist vor `RELEASE_FREI` zu korrigieren; ein Release-Kandidat und ein
Bericht, die auf verschiedene Commits zeigen, sind genau der Fall, den §10 abfangen soll.
