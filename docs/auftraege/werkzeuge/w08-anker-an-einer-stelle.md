# W-08 — Der Browser-Anker steht an EINER Stelle, und S-11 hält ihn dort

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 12:3x*

```yaml
auftrag:
  id: W-08
  strang: werkzeuge
  status: bereit   # B8 ERFUELLT: gegengelesen vom Evaluator 03.08. (TRAEGT MIT SPERRENDER AUFLAGE UND EINER KLEINEN), beide eingearbeitet 03.08. 01:0x. SPERREND war die Basis: scope-Datei scripts/auftrag-pruefen.mjs trug die unverbuchte W-07-Arbeit. ERLEDIGT, nicht nur benannt - der Generator hat W-07 um 00:44 verbucht; nachgemessen an HEAD: awk von der Liste -> 0, ZielErlaubt -> 4, Suite -> 91 pass. K-08-Ausgangswert von 82 auf 91 korrigiert. KLEIN: K-08b neu - die Inventur bekommt DREI stehende Zusagen, damit "meldet immer 0" nicht als Erfolg durchgeht. Eingetragen vom Planner.
  gegengelesen_von: evaluator   # DRITTES Gegenlesen 03.08. - neue Messmethode + neues Werkzeug im Umfang (die zwei frueheren Befunde stehen im Verlauf, 8b3868b1 und f4f0c89d-Delta)
  gegengelesen_am: 2026-08-03
  befund: >
    TRAEGT MIT SPERRENDER AUFLAGE UND EINER KLEINEN. Sperrend: das Blatt sagt "Basis: HEAD
    beim Ziehen", aber scope-Datei scripts/auftrag-pruefen.mjs traegt die UNVERBUCHTE
    W-07-Arbeit - exakt die Klasse, die W-07 selbst als Feld `vorbedingung` bekommen hat.
    Erst W-07 committen, dann W-08 bauen; auch K-08s Ausgangswert (82) gilt nur vor dem
    W-07-Commit (danach 86, vom Generator gemessen). Der Generator haelt die Reihenfolge
    bereits freiwillig ein - die Norm ist aber das Blatt, nicht sein Gedaechtnis.
    Klein: die Inventur braucht EINE stehende rote Zusage (Fixture mit ausgeschriebenem
    Anker im yaml-Block -> Inventur zaehlt 1), damit "Inventur meldet immer 0" nicht als
    Erfolg durchgeht - die K-09-Mutationsprobe ist einmalig, eine Zusage steht dauerhaft.
    Selbst gemessen (Worktree 28266ffb): 17 Blaetter mit typ browser = exakt die 17 der
    Planner-Vormessung (16 ausgeschrieben + 1 ohne = konsistent, Verweis-Soll 17 geht auf);
    K-01 Ausgangswert 0, K-02 0 mit Partner S-10=2. Die strukturelle Traeger/Zitat-Naht
    ist die richtige - mein eigener Namenslisten-Vorschlag ist zu Recht als Ausschluss
    verewigt. B8-Fragen: Maschinen-Befehle laufen, K-03/K-05 messen Wirkung mit roten UND
    gruenen Zusagen, kein maschineller Befehl mutiert.
```

## Warum — heute habe ich denselben Block ZWÖLFMAL bearbeitet

**Am 02.08. war der Anker zweimal falsch, und beide Male kostete die Korrektur sechs Blätter:**

```text
86f8e222   Anker v2 in 6 Blaetter   (Startzustand ist kein Fehlschlag)
5163cac2   Anker v3 in 6 Blaetter   (der Pruefer widerlegte Stufe 2 von v2)
```

**Zwölf Ersetzungen für zwei Erkenntnisse.** *Jede einzelne mit Zeilennummern, jede eine Gelegenheit
für genau den Splice-Fehler, gegen den W-02 gebaut wurde.* **Und zwölf weitere Blätter tragen den
ALTEN Anker weiter**, weil sie `ruht`, `abgenommen` oder ohne Kopf sind:

```text
grep -rl "L-01-anker" docs/auftraege/ | wc -l           ->  20      19 Traeger + DIESES Blatt
grep -rl "2  MONTIEREN" docs/auftraege/ | wc -l         ->   7       6 Traeger + DIESES Blatt
grep -rl "typ: browser" docs/auftraege/ | wc -l         ->  14      13 Traeger + DIESES Blatt
                                                    (gemessen 02.08. 14:0x)
```

**Die drei Zahlen tragen jede ein `+1`, und das ist keine Ungenauigkeit, sondern der Befund
selbst:** *dieses Blatt zitiert alle drei Suchmuster wörtlich, und für `grep` sieht ein Zitat
aus wie ein Anker.* **Der Evaluator hat das beim Gegenlesen gefunden** — mein K-05 hatte ein
Soll gefordert, das per rohem `grep` unerreichbar war. **Korrigiert in K-05, mit benannter
Ausnahme statt stiller Filterung.**

*Ich hatte diesen Selbsttreffer bei K-01 und K-02 bewusst vermieden — dort messe ich über das
Verzeichnis und über den Validator, gerade damit das Blatt sich nicht selbst trifft. Bei K-05
habe ich es übersehen. Dieselbe Falle, im selben Blatt, zweimal anders behandelt.*

**Und eine Lücke, die vorher niemand gesehen hat** — ein Blatt fährt Browser-Zahlen ganz ohne
Anker:

```text
comm -23 <(grep -rl "typ: browser" …) <(grep -rl "L-01-anker" …)
  ->  docs/auftraege/generator-auftrag-pb023-pb024-styleguide-und-tokens.md
```

## Die Entscheidung — derselbe Gedanke wie in W-05: WANDERN statt verdoppeln

```text
docs/auftraege/ANKER-BROWSER.md      DIE Fassung. Eine Datei, eine Wahrheit.

Im Blatt steht nur noch:
  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

**Eine Korrektur ist danach EINE Datei statt achtzehn.** *Genau die Rechnung, die W-05 für die
Werkzeugliste macht — hier für die Blätter.*

## Und die Mechanik dazu: S-11

**Ohne Sperre ist das ein Vorsatz, und Vorsätze haben heute zweimal nicht getragen (R9).**

```text
S-11   Ein Blatt mit `typ: browser` MUSS genau einen `L-01-anker` tragen.
       Ist er `typ: verweis`, muss die Quelle existieren.
       Ist er AUSGESCHRIEBEN, ist das der Fehler - die Kopie ist es, die driftet.
```

**S-11 greift nur bei `status` aus `aktiv · bereit · gebaut · entwurf · gesperrt`** — genau der
Menge, deren Anker gefahren wird. **Das ist keine Nachsicht gegenüber dem Archiv, sondern der
Riegel für den einzigen Weg, auf dem ein alter Anker zurückkommt:** wer ein `ruht`-Blatt auf
`bereit` setzt, fällt in derselben Sekunde in S-11. *Ein Archivblatt kann nicht schaden, solange
es Archiv bleibt; die Sperre sitzt am Übergang, nicht am Ruhezustand.*

## Nahtstellen

```text
Hier wird geschrieben:
  docs/auftraege/ANKER-BROWSER.md              NEU - der Anker, einmal
  scripts/auftrag-pruefen.mjs                  S-11 + `typ: verweis` im Bericht
  scripts/__tests__/auftragPruefen.test.mjs    die Zusagen dazu
  die 6 gefahrenen Blaetter                    Block raus, Verweis rein
  pb023-pb024                                  bekommt seinen fehlenden Anker

Hier bewusst NICHT:
  Die 12 Archiv-Blaetter    `ruht`/`abgenommen`/ohne Kopf. Sie werden beim
                            Wiederbeleben von S-11 gefangen - das ist der Sinn.
  Der INHALT des Ankers     v3 steht und ist vom Pruefer gegengelesen. W-08
                            verschiebt ihn, es aendert ihn nicht.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/auftrag-pruefen.mjs
    - scripts/anker-inventur.mjs
    - docs/auftraege/ANKER-BROWSER.md
  population_command: "ls docs/auftraege | grep ANKER | wc -l"
  ausschluesse:
    - stelle: "Der Inhalt des Ankers"
      grund: "v3 ist vom Pruefer gegengelesen. W-08 verschiebt ihn, es aendert ihn nicht."
      entschieden_von: planner
    - stelle: "Ein eigener YAML-Parser fuer die Inventur"
      grund: "anker-inventur.mjs benutzt js-yaml, denselben Parser wie auftrag-pruefen.mjs und zeile-ersetzen.mjs. Zwei Antworten auf 'was steht in diesem Block' waeren dieselbe Klasse wie der doppelte Kommentar-Abzug in W-06 und der doppelte Name in W-05 K-10."
      entschieden_von: planner
    - stelle: "Eine Blattnamen-Ausnahme in K-05"
      grund: "ZWEIMAL versucht, zweimal binnen Stunden gebrochen (Zitat in W-08 selbst, dann Zitat in FEHLERKLASSEN.md). Die Naht ist strukturell, nicht textlich - wer wieder eine Namensliste braucht, hat die falsche Naht gewaehlt."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Der Anker hat eine Heimat."
    pruefung:
      befehl: "ls docs/auftraege | grep ANKER | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 02.08. 12:3x; bewusst ueber das VERZEICHNIS gemessen, weil die Datei vor dem Bau nicht existiert - `zaehle.mjs <datei>` wuerde ENOENT werfen)"

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "S-11 steht im Validator."
    pruefung:
      befehl: "grep -o 'S-11' scripts/auftrag-pruefen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 02.08. 12:3x; Partner 'S-10' -> 2, die Messung ist nicht leer)"

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG: S-11 faengt genau die drei Faelle und keinen vierten."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion, nicht ueber den vollen Lauf. ROT:
          Blatt `status: bereit`, `typ: browser`, KEIN L-01-anker      -> S-11 meldet
          Blatt `status: aktiv`,  L-01-anker `typ: verweis`, Quelle fehlt -> S-11 meldet
          Blatt `status: entwurf`, L-01-anker AUSGESCHRIEBEN            -> S-11 meldet
        GRUEN:
          Blatt `status: ruht`,   L-01-anker AUSGESCHRIEBEN            -> still
          Blatt `status: bereit`, Verweis auf eine vorhandene Quelle    -> still
          Blatt `status: bereit`, GAR KEIN `typ: browser`               -> still
        Die drei GRUENEN sind die eigentliche Zusage: eine Sperre, die auch das Archiv
        rot faerbt, zwingt zu einem Massenumbau, den W-08 gerade vermeiden soll.
      erwartet: "sechs Zusagen, davon drei ROTE"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Ein unbekannter typ verschwindet NICHT still - F-17 ist fuer `verweis` geschlossen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        F-17 steht offen: ein unbekannter `typ:` faellt lautlos aus dem Bericht.
        `typ: verweis` ist ein NEUER typ - wer ihn einfuehrt, ohne ihn zu melden,
        macht aus einem Anker eine Leerstelle, die wie Zustimmung aussieht.
          ein Eintrag `typ: verweis` erscheint im Bericht, mit seiner Quelle
          die Zeilensumme des Blattes steigt um 1, nicht um 0
        Die zweite Zeile ist die scharfe: sie faellt auch dann, wenn der Eintrag
        zwar gedruckt, aber nicht gezaehlt wird.
      erwartet: "zwei Zusagen, beide gruen erst NACH dem Bau"

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Jedes gefahrene Blatt traegt den VERWEIS, und der Anker steht danach nur noch einmal ausgeschrieben - STRUKTURELL gemessen, nicht ueber ein Textmuster."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        ZWEITE KORREKTUR, 03.08. — der Generator hat NICHT gebaut und hatte recht.
        Die Namensausnahme aus der ersten Korrektur hielt keine zwei Stunden:

            Ausgangswert im Blatt   6
            gemessen 03.08.         8
            neu dazu:  FEHLERKLASSEN.md   <- ZITAT in der Beschreibung von F-19
                       z06n1-…            <- echter Traeger

        **Die Fehlerklasse, die den Selbsttreffer beschreibt, trifft sich selbst.**
        Und die Auflage des Evaluators sagt ausdruecklich: die Ausnahme wird nicht
        erweitert, sondern das Muster praezisiert. Beides zusammen heisst: das TEXTMUSTER
        ist die falsche Naht - jede Liste waechst mit dem naechsten Zitat.

        DIE NEUE NAHT — nicht der TEXT, sondern die STRUKTUR:
        Ein Zitat steht in Fliesstext oder in einem ```text-Block. Ein Anker ist ein
        KRITERIUM in einem ```yaml-Block. Das ist maschinell trennbar und kann per
        Definition kein Zitat treffen.

            scripts/anker-inventur.mjs   NEU, Teil dieser Scheibe
              liest die ```yaml-Bloecke (derselbe Parser wie auftrag-pruefen.mjs)
              und zaehlt je Blatt: L-01-anker ausgeschrieben · als verweis · gar nicht

        Vom Planner mit genau dieser Logik vorgemessen (03.08.):

            Blaetter mit Anker oder Browser-Kriterium   17
            Anker AUSGESCHRIEBEN                        16      -> danach 0
            Anker als VERWEIS                            0      -> danach 17
            Browser OHNE Anker                           1      -> danach 0  (K-06)

        Die Zahlen sind hoeher als in der ersten Fassung (16 statt 6), weil sie zum
        ersten Mal ALLE Blaetter zaehlen statt nur die mit dem heutigen Wortlaut -
        Archiv eingeschlossen. Das ist kein Mehraufwand: ein Archivblatt bekommt
        denselben Dreizeiler.

        Und die Gegenrichtung, damit nicht einfach alles verschwindet:
            ls docs/auftraege | grep ANKER | wc -l                  ->  1
            die Ankerdatei enthaelt die drei Stufen                 ->  ja

        KEINE AUSNAHME MEHR, KEINE NAMENSLISTE. Wer eine braucht, hat die falsche
        Naht gewaehlt - das ist die Lehre aus zwei Korrekturen an derselben Zusage.
      erwartet: "ausgeschrieben 16 -> 0, verweis 0 -> 17, eine Ankerdatei; ohne jede Blattnamen-Ausnahme"

  - id: K-06
    typ: presence
    kritikalitaet: P1
    aussage: "Das Blatt ohne Anker bekommt einen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        `generator-auftrag-pb023-pb024-styleguide-und-tokens.md` faehrt Browser-Zahlen
        ohne jeden Anker - gefunden beim Schneiden dieses Blattes, nicht vorher.
        Es bekommt den Verweis wie die anderen. Danach:
          Blaetter mit `typ: browser` und OHNE L-01-anker  ->  0
      erwartet: "0"

  - id: K-07
    typ: behavioural
    aussage: "Der volle Lauf bleibt so gruen wie heute."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der VOLLE Lauf ueber docs/auftraege/ vorher und nachher, und die Differenz wird
        BENANNT. Erwartet: kein neu gesperrtes Kriterium ausser den von S-11 gemeldeten.
        Faellt mehr, hat der Verweis eine Pruefung mitgenommen, die vorher lief.
      erwartet: "keine neue Sperre ausser S-11"

  - id: K-08
    typ: behavioural
    aussage: "Die Werkzeug-Suite bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.mjs"   # NICHT das Verzeichnis: `node --test <verz>/` wirft auf Node 22 MODULE_NOT_FOUND (gemessen 02.08. 14:2x)
      erwartet: "0 fail. Ausgangswert 91 pass / 0 fail — vom Planner AN HEAD nachgemessen 03.08. 01:0x (`node --test scripts/__tests__/*.mjs`). Die 82 des Evaluators galten VOR dem W-07-Commit; der Generator hat W-07 um 00:44 verbucht, und damit ist die Zahl 91."

  - id: K-08b
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Inventur hat eine STEHENDE rote Zusage - 'meldet immer 0' geht nicht als Erfolg durch."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        AUFLAGE des Evaluators (03.08.), angenommen: die Mutationsprobe aus K-09 ist EINMALIG.
        Eine Zusage steht DAUERHAFT. Ohne sie faellt niemandem auf, wenn die Inventur
        eines Tages nur noch Nullen liefert - und Nullen sehen im Bericht wie Erfolg aus.

            Fixture: ein Blatt mit AUSGESCHRIEBENEM L-01-anker im ```yaml-Block
              -> anker-inventur zaehlt dort GENAU 1 ausgeschrieben
            Fixture: dasselbe Blatt mit `typ: verweis`
              -> zaehlt 0 ausgeschrieben, 1 verweis
            Fixture: ein Blatt, das den Anker nur ZITIERT (```text-Block)
              -> zaehlt 0 - das ist die Traeger/Zitat-Naht, dauerhaft verriegelt

        Die dritte Zeile ist die eigentliche Zusage dieses Blattes: sie faellt, wenn
        jemand die Inventur je wieder auf ein Textmuster umstellt.
        Die Fixtures liegen bei den Blaettern, NICHT unter tests/fixtures/auftraege/ -
        dieses Verzeichnis existiert nicht (S-10 zeigt seit dem 02.08. darauf ins Leere,
        gemessen im Schlusslauf 12:5x). Wer es hier neu erfindet, baut F-20 nach.
      erwartet: "drei Zusagen, alle drei dauerhaft"

  - id: K-09
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 7 Mutationen: S-11 greift auch bei `ruht` · S-11 greift nie ·
        fehlende Quelle wird nicht geprueft · ausgeschriebener Anker gilt als in Ordnung ·
        `typ: verweis` wird gedruckt, aber nicht gezaehlt · S-11 meldet, ohne den Exitcode
        zu setzen · Blatt ohne `typ: browser` wird trotzdem gemeldet.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - und das ist hier fast komisch."
    pruefung:
      typ: verfahren
      schritte: |
        W-08 handelt VOM Browser-Anker und braucht selbst keinen: es aendert den Validator
        und Textdateien. Ausdruecklich benannt statt weggelassen - der Beleg sind K-03 bis
        K-07, nicht ein Schirm.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  S-11 faerbt auch das Archiv rot und erzwingt den Massenumbau.       -> K-03 vierte Zeile
2  Der Verweis zeigt auf eine Datei, die es nicht gibt.                -> K-03 zweite Zeile
3  Der Block bleibt liegen UND der Verweis kommt dazu - zwei Wahrheiten.-> K-05
4  `typ: verweis` faellt lautlos aus dem Bericht (F-17).               -> K-04
5  Beim Umzug geht eine Pruefung verloren, die vorher lief.            -> K-07
6  Das Blatt ohne Anker bleibt ohne.                                   -> K-06
7  Der Anker-INHALT wird beim Umzug "nebenbei verbessert".
   OHNE ZUSAGE, mit Grund: v3 ist vom Pruefer gegengelesen und steht als Ausschluss
   im Blatt. Wer ihn hier aendert, umgeht das Gegenlesen - eine Zusage darueber waere
   eine Zusage ueber Absicht, und die gibt es nicht.
8  Ein Blatt braucht einen ANDEREN Anker als den einen (andere Seite, anderer Mount).
   OHNE ZUSAGE, mit Grund: heute tut es keines - alle 13 zeigen auf denselben Studio-
   Bildschirm. Kaeme so ein Fall, waere die Antwort eine zweite Ankerdatei und ein
   zweiter Verweis, nicht ein zurueckkopierter Block. Das steht hier, damit die naechste
   Sitzung nicht raet.
```

## Rückweg und Entdeckung

**Rückweg:** eine neue Datei, ein Validator-Abschnitt, sieben Blätter mit drei Zeilen statt
sechzehn. **Der Block liegt im Verlauf** — wer zurückdreht, hat ihn wieder.

**Entdeckung:** K-05. **Wenn Verweis UND Block nebeneinander stehen bleiben, sieht alles grün aus
und die nächste Korrektur trifft wieder nur die Hälfte.** *Das ist genau der Zustand, in dem der
Ordner heute ist — nur ohne Verweis.*
