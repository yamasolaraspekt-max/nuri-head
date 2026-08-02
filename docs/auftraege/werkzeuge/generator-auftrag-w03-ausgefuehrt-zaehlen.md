# W-03 — Der Validator sagt, wie viele Befehle er AUSGEFÜHRT hat

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 23:0x*

```yaml
auftrag:
  id: W-03
  strang: werkzeuge
  status: gesperrt
  sperrgrund: "W-01 arbeitet gerade an scripts/auftrag-pruefen.mjs. Zwei Blaetter an derselben Datei sind Beifang mit Ansage (F-05). Wird `entwurf`, sobald W-01 abgenommen ist - dann Gegenlesen durch den Evaluator (B8)."
  gegengelesen_von:
  gegengelesen_am:
  befund:
```

## Warum — und was daran NICHT stimmt, was der Beschluss behauptet

**B2 im Beschluss sagt:** *„Der Validator meldet am Ende, was er ausgeführt hat — nicht nur, was
fehlschlug."* **Beim Messen stellt sich heraus: er meldet es bereits.**

```text
node scripts/auftrag-pruefen.mjs docs/auftraege/hausplaner-3d/generator-auftrag-z10-masseingabe.md | tail -1
   ── 12 Eintrag/Eintraege: 5 OK · 0 verdaechtig · 0 Fehlschlag · 0 nulltreffer
                            · 0 uebersprungen · 7 nicht maschinell
```

**`OK + verdaechtig + fehlschlag + nulltreffer` sind die ausgeführten Befehle.** Die Zahl steht da —
**verteilt auf vier Summanden, die man selbst addieren muss.**

**Und genau daran bin ich am 01.08. um 20:00 vorbeigelaufen.** Meine eigene Ausgabe trug
`b01 … 4 OK`, ich habe nach `FEHLSCHLAG` gefiltert und geschrieben *„bewusst liegen gelassen"*.
**Ein `OK` heißt: der Befehl lief.** Um 20:01 stand ein Push-Wrapper im Log.

> **Eine Meldung, die man erst zusammenrechnen muss, wird nicht gelesen.**

*Das ist die ganze Begründung dieses Blattes — und sie ist schwächer, als B2 im Beschluss klingt.
Sie gehört trotzdem gebaut: der Unterschied zwischen „steht verteilt da" und „springt einen an" ist
genau der Unterschied zwischen dem, was ich hätte sehen können, und dem, was ich gesehen habe.*

## Bestand — gemessen 01.08. 23:0x

```text
node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'ausgefuehrt'    -> 7  (alles Prosa/Hinweise)
node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'uebersprungen'  -> 1  (Partner: misst)
node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'STUFEN'         -> 18
```

## Die Entscheidung

**Eine eigene Zeile, VOR der Aufschlüsselung, mit der Zahl der wirklich gelaufenen Befehle.**

```text
   ══ 5 Befehle AUSGEFUEHRT · 7 nicht ausgefuehrt (uebersprungen oder nicht maschinell)
   ── 12 Eintrag/Eintraege: 5 OK · 0 verdaechtig · 0 Fehlschlag · 0 nulltreffer · …
```

**Warum davor und nicht danach:** wer eine Ausgabe überfliegt, liest die erste Zeile eines Blocks
und die letzte. Die Aufschlüsselung ist die letzte. **Die Zahl, die zählt, gehört an die erste.**

**Warum ein anderes Zeichen (`══`):** damit ein `grep '──'`, wie ich ihn den ganzen Abend gefahren
habe, die Zeile **nicht** wegfiltert. *Die Meldung muss die Filter überleben, die man um sie
herumbaut — sonst ist sie eine Meldung an niemanden.*

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/auftrag-pruefen.mjs                 die Funktion `bericht()`, EINE zusaetzliche Zeile
  scripts/__tests__/auftragPruefen.test.mjs   die Zusagen

Hier bewusst NICHT:
  Die vier Stufen                Sie bleiben, wie sie sind. Die neue Zeile RECHNET sie zusammen,
                                 sie ersetzt sie nicht - sonst gibt es zwei Wahrheiten ueber
                                 dieselbe Zahl.
  ALLOWLIST / GATE_MUSTER        gehoert W-01.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/auftrag-pruefen.mjs
    - scripts/__tests__/auftragPruefen.test.mjs
  population_command: "node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'AUSGEFUEHRT'"
  ausschluesse:
    - stelle: "Die vier Stufen OK/verdaechtig/fehlschlag/nulltreffer"
      grund: "Die neue Zeile rechnet sie zusammen, sie ersetzt sie nicht. Zwei Wahrheiten ueber dieselbe Zahl waeren schlimmer als keine."
      entschieden_von: planner
    - stelle: "ALLOWLIST und GATE_MUSTER"
      grund: "Gehoert W-01, das an derselben Datei arbeitet."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Wort AUSGEFUEHRT steht im Bericht."
    pruefung:
      befehl: "node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'AUSGEFUEHRT'"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 23:0x; Partner 'uebersprungen' -> 1, die Messung ist nicht leer)"

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Zahl ist die SUMME der vier ausgefuehrten Stufen - gegen die Funktion geprueft, nicht gegen die Ausgabe."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3: gegen die Entscheidung, nicht gegen den Ausfuehrer. Eine Funktion, die aus einer
        Ergebnisliste die Zahl liefert - `zahlAusgefuehrt(eintraege)`:
          [OK, OK, NICHT_MASCHINELL]                  -> 2
          [UEBERSPRUNGEN, NICHT_MASCHINELL]           -> 0
          [FEHLSCHLAG, NULLTREFFER, VERDAECHTIG]      -> 3
          []                                          -> 0
        KEIN Testfall ruft `pruefeEintrag` oder faehrt einen echten Befehl.
      erwartet: "vier Zusagen, eine davon der leere Fall"

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Zeile ueberlebt den Filter, der sie sonst verschluckt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der Bericht wird durch `grep '──'` geschickt - der Filter, den der Planner am 01.08.
        den ganzen Abend gefahren hat. Die AUSGEFUEHRT-Zeile muss danach NOCH DA sein.
        Ohne diese Zusage baut man eine Warnung, die genau der Gewohnheit zum Opfer faellt,
        gegen die sie gerichtet ist.
      erwartet: "Zeile ueberlebt `grep '──'`"

  - id: K-04
    typ: absence
    aussage: "Die vier Stufen sind unveraendert."
    pruefung:
      befehl: "node scripts/zaehle.mjs scripts/auftrag-pruefen.mjs 'uebersprungen'"
      erwartet: "1 - unveraendert"
    ausgangswert: "1"

  - id: K-05
    typ: behavioural
    aussage: "Die bestehenden Zusagen bleiben gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/auftragPruefen.test.mjs scripts/__tests__/zaehle.test.mjs"
      erwartet: "0 fail. Ausgangswert 69 pass (01.08. 22:4x) bzw. der Stand nach W-01. Danach mehr, nie weniger."

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 5 Mutationen: NICHT_MASCHINELL mitzaehlen · UEBERSPRUNGEN mitzaehlen ·
        VERDAECHTIG NICHT mitzaehlen · die Zeile ans ENDE statt an den Anfang ·
        dasselbe Zeichen `──` statt `══`.
        Wie viele kommen durch?
```

## Kantenliste

```text
1  UEBERSPRUNGEN wird mitgezaehlt. Dann meldet der Validator Ausfuehrungen, die nicht
   stattgefunden haben - schlimmer als keine Zahl. -> K-02
2  NICHT MASCHINELL wird mitgezaehlt. Ein Gate ist kein ausgefuehrter Befehl.
3  VERDAECHTIG wird NICHT mitgezaehlt, obwohl der Befehl lief (exit 0, keine Ausgabe).
   Er LIEF - er gehoert dazu. Das ist die Kante, an der die Zaehlung still falsch wird.
4  Die Zeile bekommt `──` und verschwindet im Filter. -> K-03
5  Zwei Wahrheiten: die neue Zahl und die Aufschlueselung driften auseinander, weil eine
   von beiden spaeter gepflegt wird. Deshalb wird sie BERECHNET, nie zweitgezaehlt.
```

## Rückweg und Entdeckung

**Rückweg:** eine Zeile im Bericht und eine Funktion — zurückdrehbar, kein Datenpfad, kein Schema.

**Entdeckung:** die Zahl selbst. Steht sie bei 0, während Kriterien mit `befehl:` im Blatt stehen,
prüft der Validator nichts mehr. Steht sie so hoch wie die Zahl der Einträge, führt er auch die
Gates aus. **Beide Enden sind rot und beide sehen ruhig aus.**
