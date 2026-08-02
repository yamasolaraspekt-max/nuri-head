# W-08 — Der Browser-Anker steht an EINER Stelle, und S-11 hält ihn dort

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 12:3x*

```yaml
auftrag:
  id: W-08
  strang: werkzeuge
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Pruefer
  gegengelesen_von: evaluator   # nach d1cecdcf: Werkzeug-Blatt -> Evaluator (Kopfkommentar oben sagt noch Pruefer)
  gegengelesen_am: 2026-08-02
  befund: >
    TRAEGT MIT AUFLAGE AN DER MESSMETHODE: die rohen grep-Zaehlungen zaehlen ZITATE mit.
    W-08 selbst und w05-werkzeug-anschluss enthalten 'L-01-anker', '2  MONTIEREN' und
    'typ: browser' als woertlich zitierte Suchmuster — gemessen heute 20/7/14 statt
    19/6/13 (der +1 ist dieses Blatt). Damit ist das K-05-Soll "0 Blaetter mit MONTIEREN"
    per rohem grep UNERREICHBAR; der Generator braucht eine benannte Ausnahme
    (Zitat-Blaetter ausschliessen oder Muster auf den Stufe-3-Kontext praezisieren),
    sonst wird das Soll passend gemacht statt gemessen. Bestaetigt: K-01 Ausgangswert 0
    (keine ANKER-Datei), K-02 0 mit Partner S-10=2, pb023-pb024 ist exakt das eine
    Browser-Blatt ohne Anker (comm-Probe). Preflight 3 OK/0 Fehlschlag. B8-Fragen:
    Maschinen-Befehle laufen, K-03 misst Wirkung mit 3 roten UND 3 gruenen Zusagen,
    keiner mutiert.
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
grep -rl "L-01-anker" docs/auftraege/ | wc -l           ->  19
grep -rl "2  MONTIEREN" docs/auftraege/ | wc -l         ->   6   auf dem Stand von heute
grep -rl "typ: browser" docs/auftraege/ | wc -l         ->  13
```

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
    - docs/auftraege/ANKER-BROWSER.md
  population_command: "ls docs/auftraege | grep ANKER | wc -l"
  ausschluesse:
    - stelle: "Die 12 Archiv-Blaetter (ruht, abgenommen, ohne Kopf)"
      grund: "Sie werden beim Wiederbeleben von S-11 gefangen. Die Sperre sitzt am Uebergang, nicht am Ruhezustand."
      entschieden_von: planner
    - stelle: "Der Inhalt des Ankers"
      grund: "v3 ist vom Pruefer gegengelesen. W-08 verschiebt ihn, es aendert ihn nicht."
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
    aussage: "Die sechs gefahrenen Blaetter tragen den Verweis, und der Anker steht danach nur noch einmal ausgeschrieben."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Nach dem Umzug, ueber den ganzen Ordner gemessen:
          Blaetter mit ausgeschriebenem Anker in Stufe 3 ("BUEHNE")  ->  12 (nur Archiv,
            und alle mit dem ALTEN Wortlaut - kein einziges mit "MONTIEREN")
          docs/auftraege/ANKER-BROWSER.md enthaelt "MONTIEREN"       ->  1
        Heute: 6 Blaetter mit "MONTIEREN". Danach: 0 Blaetter, 1 Ankerdatei.
        Steht die Zahl danach bei 6, ist der Verweis eingebaut und der Block liegen
        geblieben - dann gibt es zwei Wahrheiten statt einer.
      erwartet: "0 Blaetter mit MONTIEREN, 1 Ankerdatei"

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
      schritte: "node --test scripts/__tests__/"
      erwartet: "0 fail. Ausgangswert 82 pass / 0 fail (Evaluator, 02.08.)."

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
