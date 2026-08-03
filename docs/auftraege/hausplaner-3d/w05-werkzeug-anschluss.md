# W-05 — Ein Werkzeug aus dem Katalog in die Leiste heben: EINE Zeile statt einer Scheibe

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 10:4x*

```yaml
auftrag:
  id: W-05
  strang: hausplaner-3d
  status: bereit   # Evaluator-Gegenlesung 03.08. (TRAEGT IM KERN, sperrende Auflage + zwei kleine): alle drei eingearbeitet 09:2x - K-03 scharfes Muster ", 101[),]" (6 echte statt 7 roher Treffer), Prosa auf SECHS+EINE korrigiert, K-08-Basis dynamisch statt 1641. K-10 vom Evaluator BESTAETIGT (Bilanz stimmt heute durch Zufall). Status vom Einarbeiter der sperrenden Auflage (B14-Randfall wie N1). Gegenlesen war Evaluator statt Pruefer - zu Ende gelesen nach angefangener Messung, dokumentiert.
  gegengelesen_von: evaluator   # umverteilt 03.08.; der Pruefer ist inzwischen wieder aktiv, ab dem naechsten Planner-Blatt gilt d1cecdcf
  gegengelesen_am: 2026-08-03
  befund: >
    TRAEGT IM KERN, MIT EINER SPERRENDEN UND ZWEI KLEINEN AUFLAGEN. Die Entscheidung -
    eine Hebeliste, Registry und Katalog leiten sich daraus ab, das Werkzeug WANDERT statt
    sich zu verdoppeln - ist richtig, und K-06 (das zweite Werkzeug darf nur Daten aendern)
    ist die scharfe Zusage, an der sich der Hebel messen laesst. Bestaetigt: Hebeliste 0
    mit Partner EIGENE_WERKZEUGE 20; bemassen in der Registry 0; die acht Paket-Werkzeuge
    stehen in Themen, Vertrag und Praesentation je 1, nur die Registry fehlt.
    K-10 ist berechtigt und kein F-07: WERKZEUGE_GESAMT existiert zwar schon
    (toolRegistry.ts:278), rechnet aber mit der GETIPPTEN 110 - die Bilanz stimmt heute
    tatsaechlich durch Zufall.
    SPERREND, K-03: das Kriterium ist im eigenen Scope nicht erfuellbar. Der Befehl
    grep -rn ', 101' liefert 7 Zeilen, aber eine davon ist bemassung.test.ts:32 - die
    Fenster-Masskette 240, 1880, 1010, 2870, 240; das Muster trifft die ZIFFERNFOLGE in
    1010, nicht die Katalogzahl. Echte Katalogzahlen sind SECHS. Auf die geforderte 0 kaeme
    der Bauende nur, indem er eine Masszusage aendert, die ausserhalb von scope.dateien
    liegt. Vor dem Bau: Muster praezisieren (zum Beispiel auf TOOL_KATALOG.length, 101 oder
    auf die zonen-Zusagen) und Ausgangswert auf 6 stellen.
    KLEIN 1: die Umfangs-Rechnung ist ueberzeichnet. grep -rn '9 + EIGENE_WERKZEUGE' liefert
    4 Zeilen, aber DREI davon sind 79 + EIGENE_WERKZEUGE.length in rechte.test.ts 201, 219,
    220 - die 9 ist dort die Endziffer von 79. Echt traegt nur toolPresentation.test.ts:33
    die feste 9. Statt der behaupteten ELF Stellen sind es SIEBEN. Das aendert den Bau nicht,
    aber der Satz begruendet den Umfang der Scheibe.
    KLEIN 2: K-08-Ausgangswert 1641 ist ueberholt - an sauberem HEAD selbst gefahren 1649
    pass, 0 fail. Nach unten falsch ist ungefaehrlich, aber wer bei 1645 landet, haelt einen
    Verlust fuer einen Gewinn.
    PROSA: der Satz TOOL_KATALOG sei "alle 110 Paket-Werkzeuge" stimmt nicht - die Liste hat
    101 Eintraege und TOOL_KATALOG damit ebenfalls 101; die 110 ist die getippte Zahl aus
    toolRegistry, die K-10 gerade aufraeumt.
    B8-Fragen: Befehle laufen, K-04 und K-06 messen die Wirkung mit roter Gegenprobe,
    kein maschineller Befehl mutiert.
```

## Warum — und eine Korrektur an meinem eigenen Bauplan von vor einer Stunde

**In `docs/planner/programm-werkzeuge-bauplan-2026-08-02.md` steht, ein Werkzeug erreichbar zu
machen koste „vier Stellen". Das habe ich von Z-05-N1 übernommen, ohne es für die Paket-Werkzeuge
nachzumessen. Gemessen sind es DREI VON VIER SCHON DA:**

```text
Werkzeug            Registry  Themen  Vertrag  Praesentation
bemassen                   0       1        1              1
flaeche-messen             0       1        1              1
grundriss-erkennen         0       1        1              1
gaube                      0       1        1              1
oeffnung                   0       1        1              1
heizkoerper                0       1        1              1
verteiler                  0       1        1              1
kuechenplanung             0       1        1              1
```

**Bei allen acht fehlt ausschliesslich der Registry-Eintrag.** *Z-05-N1 war teuer, weil `kontur`
ein EIGENES Werkzeug war und in alle vier Stellen neu musste. Die acht sind Paket-Werkzeuge — sie
stehen längst überall, nur nicht in der Leiste.*

## Der eigentliche Widerstand — und er ist eine Zusage, kein Code

**Wer `bemassen` in die Registry schreibt, bricht sofort eine abgenommene Zusage.**
`__tests__/toolPresentation.test.ts` Zeile 31:

```text
assert.equal(TOOL_DEFINITIONS.length, 9 + EIGENE_WERKZEUGE.length);
assert.equal(TOOL_KATALOG.length, 101);
assert.equal(new Set(ids).size, ids.length, 'keine doppelte toolId');
```

`TOOL_KATALOG` ist `PAKET_ALS_TOOLS` — **alle 110 Paket-Werkzeuge, ungefiltert.** Ein Werkzeug in
der Registry wäre danach **zweimal** da: einmal als Registry-Eintrag, einmal im Katalog. **Die
Dubletten-Zusage geht rot, und zwar zu Recht.**

```text
grep -rnE ", 101[),]" resources/planner/hausplaner/__tests__/*.ts | wc -l               ->  6   (der 7. rohe Treffer ist die Ziffernfolge 1010 in einer Masskette)
grep -rn '9 + EIGENE_WERKZEUGE' resources/planner/hausplaner/__tests__/*.ts       -> 4
```

**SECHS Zusagen tragen die feste 101, EINE die reine 9** (die "vier" waren drei 79er-Endziffern — Evaluator-Befund, nachgemessen). **Sieben feste Stellen** zahlt jedes Werkzeug erneut — genau der Massenumbau, den W-05 verhindern soll.
Werkzeug diese elf Stellen erneut — genau der Massenumbau, den W-05 verhindern soll.

## Die Entscheidung

**Eine Liste sagt, welche Paket-Werkzeuge in die Leiste gehoben sind. Registry und Katalog leiten
sich daraus ab — beide, aus derselben Quelle.**

```text
AUS_PAKET_GEHOBEN = ['bemassen', …]        <- die EINE Zeile je Werkzeug

TOOL_DEFINITIONS = 9 Grundeintraege + EIGENE_WERKZEUGE + AUS_PAKET_GEHOBEN
TOOL_KATALOG     = PAKET_ALS_TOOLS OHNE die Ids aus AUS_PAKET_GEHOBEN

Bilanz, unveraendert gueltig:
   TOOL_DEFINITIONS.length + TOOL_KATALOG.length === WERKZEUGE_GESAMT
```

**Die Summe bleibt konstant, weil ein Werkzeug WANDERT statt sich zu verdoppeln.** *Das ist
derselbe Gedanke wie `PAKET + EIGENE.length` aus Z-05-N1: nicht die Zahl anfassen, sondern
sie berechnen.*

**Warum `AUS_PAKET_GEHOBEN` und nicht `GEHOBEN`:** `GEHOBEN` ist vergeben — `schattenGehoben` und
`ROH_GEHOBEN` in `__tests__/elevationTokens.test.ts` (5 Treffer). *Zwei Bedeutungen für einen
Namen sind eine zweite Wahrheit; gemessen, bevor der Name gewählt wurde.*

**Und genau diese zweite Wahrheit steckt schon drin — gemessen am 02.08.:** `PAKET_WERKZEUGE`
heisst zweimal etwas anderes. In `werkzeugPaket.ts:34` ist es die **Liste** (101 Eintraege), in
`toolRegistry.ts:272` die **Zahl 110**. *Und die 110 ist nicht das Paket — sie ist 9 Grundeintraege
plus 101 Paket.* **Die Bilanzgleichung stimmt heute, aber sie stimmt durch Zufall.** Nimmt jemand
ein Werkzeug aus dem Paket, bleibt die getippte 110 stehen und die Gleichung kippt lautlos.
**Das raeumt K-10 auf — bevor die Hebeliste sich auf diese Gleichung stuetzt.**
**Die sieben festen `101` und vier festen `9` werden auf die abgeleiteten Ausdrücke umgestellt** —
`TOOL_KATALOG.length` und `TOOL_DEFINITIONS.length` selbst, nicht ihre heutigen Zahlen.

## Der Beweis, dass der Hebel trägt — und er entscheidet sich am ZWEITEN Werkzeug

**W-05 hebt ZWEI Werkzeuge: `bemassen` und `flaeche-messen`.**

```text
Das erste  darf die Mechanik veraendern - dafuer ist die Scheibe da.
Das zweite darf NUR Daten veraendern: eine Zeile in AUS_PAKET_GEHOBEN und einen
           Registry-Eintrag. Aendert es mehr, traegt der Hebel nicht, und das
           Blatt ist ROT - auch wenn beide Werkzeuge funktionieren.
```

*Ein Hebel, der beim zweiten Mal wieder Code braucht, ist kein Hebel.*

## Nahtstellen

```text
Hier wird geschrieben:
  app/tools/toolRegistry.ts        AUS_PAKET_GEHOBEN + die zwei Eintraege
  app/tools/toolCatalog.ts         der Filter gegen AUS_PAKET_GEHOBEN
  __tests__/…                      die 11 festen Zahlen auf abgeleitete umstellen

Hier bewusst NICHT:
  app/tools/paketAdapter.ts        das Paket bleibt vollstaendig. Gefiltert wird beim
                                   KATALOG, nicht an der Quelle - sonst verliert
                                   `verworfeneKuerzel()` seine Grundgesamtheit.
  werkzeugThemen · werkzeugVertrag · toolPresentation
                                   alle drei tragen die acht bereits. Nichts zu tun.
  Die Fachlogik in geometry/       bemassung.ts und polygonFlaeche.ts sind gebaut.
                                   W-05 macht sie erreichbar, es rechnet nicht neu.
```

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/tools/toolRegistry.ts
    - resources/planner/hausplaner/app/tools/toolCatalog.ts
  population_command: "grep -ro 'AUS_PAKET_GEHOBEN' resources/planner/hausplaner/ | wc -l"
  ausschluesse:
    - stelle: "paketAdapter.ts"
      grund: "Das Paket bleibt vollstaendig; gefiltert wird beim Katalog. Sonst verliert verworfeneKuerzel() seine Grundgesamtheit."
      entschieden_von: planner
    - stelle: "werkzeugThemen, werkzeugVertrag, toolPresentation"
      grund: "Tragen alle acht bereits - gemessen. Nichts zu tun."
      entschieden_von: planner
    - stelle: "Die Geometrie in bemassung.ts und polygonFlaeche.ts"
      grund: "Gebaut. W-05 macht sie erreichbar, es rechnet nicht neu."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Hebeliste existiert."
    pruefung:
      befehl: "grep -ro 'AUS_PAKET_GEHOBEN' resources/planner/hausplaner/ | wc -l"
      erwartet: "mindestens 2"
    ausgangswert: "0 (gemessen 02.08. 10:4x; Partner 'EIGENE_WERKZEUGE' -> 20, die Messung ist nicht leer)"

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Beide Werkzeuge stehen in der Registry."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolRegistry.ts \"'bemassen'\""
      erwartet: "mindestens 1"
    ausgangswert: "0 (Partner: dieselbe Datei, 'wand' -> mehrfach)"

  - id: K-03
    typ: absence
    kritikalitaet: P1
    aussage: "Keine feste 101 mehr in den Zusagen - die Katalogzahl wird abgeleitet."
    pruefung:
      befehl: "grep -rnE ', 101[),]' resources/planner/hausplaner/__tests__/ | wc -l"
      erwartet: "0"
    ausgangswert: "6 (SPERRENDE AUFLAGE des Evaluators eingearbeitet 03.08.: das rohe Muster ', 101' traf 7 - der siebte war die ZIFFERNFOLGE 1010 in der Masskette bemassung.test.ts:32, ausserhalb von scope.dateien und nie erreichbar. Das scharfe Muster trifft genau die 6 echten Katalogzahlen; nachgemessen)"
    gegenbeweis: |
      Bleibt eine feste 101 stehen, geht sie beim naechsten gehobenen Werkzeug rot - und der
      Hebel kostet dann wieder eine Zusagen-Reparatur je Werkzeug. Genau das soll W-05 beenden.

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Bilanz ist WANDERUNG, nicht Zuwachs - gegen die Rechnung geprueft, nicht gegen die Zahl."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidung, nicht gegen die Oberflaeche:
          TOOL_DEFINITIONS.length + TOOL_KATALOG.length === WERKZEUGE_GESAMT
        Diese eine Gleichung, gefahren mit LEERER Hebeliste, mit EINEM und mit ZWEI Eintraegen.
        Dreimal dieselbe Summe. Und die rote Gegenprobe:
          ein Eintrag in AUS_PAKET_GEHOBEN, der im Paket NICHT existiert
            -> muss auffallen, nicht stillschweigend die Summe senken
      erwartet: "vier Zusagen, davon eine ROTE"

  - id: K-05
    typ: absence
    kritikalitaet: P1
    aussage: "Kein Werkzeug ist doppelt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Die vorhandene Zusage 'keine doppelte toolId' bleibt und muss GRUEN bleiben, ohne
        angepasst zu werden. Wird sie angefasst, um gruen zu werden, ist der Filter falsch.
      erwartet: "gruen, Zusage unveraendert"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "DER HEBEL TRAEGT - das zweite Werkzeug aendert NUR Daten."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der Bau laeuft in ZWEI Commits, und die Reihenfolge ist der Beweis:
          Commit 1: Mechanik + `bemassen`
          Commit 2: NUR `flaeche-messen`
        Fuer Commit 2 gilt:
          git show --stat <commit2>  ->  nur toolRegistry.ts, und der Diff enthaelt
                                        KEINE neue Funktion, KEINE Zusagen-Aenderung
        Braucht das zweite Werkzeug Code, ist W-05 ROT - auch wenn beide Werkzeuge laufen.
      erwartet: "Commit 2 beruehrt eine Datei und aendert nur Daten"

  - id: K-07
    typ: behavioural
    aussage: "Kuerzel-Kollision wird gemeldet, nicht verschluckt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        `verworfeneKuerzel()` existiert, WEIL das Paket Kollisionen hat. Ein gehobenes
        Werkzeug bringt sein Paket-Kuerzel mit.
          shortcutKollisionen() nach dem Heben  ->  LEER
        Ist es nicht leer, bekommt das gehobene Werkzeug ein neues Kuerzel - und das steht
        im Beleg, nicht im Kopf des Bauenden.
      erwartet: "shortcutKollisionen() leer, jede Abweichung benannt"

  - id: K-08
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Basis misst der Bauende VOR dem Zug an sauberem HEAD und benennt sie (Evaluator 03.08.: 1649/0 - die 1641 des ersten Schnitts war ueberholt, F-20-Lehre). Danach mehr oder gleich, nie weniger."

  - id: K-10
    typ: presence
    kritikalitaet: P1
    aussage: "Der Name PAKET_WERKZEUGE hat nur noch EINE Bedeutung, und die Gesamtzahl wird gerechnet statt behauptet."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        BEFUND des Planners, 02.08. - gemessen, nicht erinnert:
          werkzeugPaket.ts:34  export const PAKET_WERKZEUGE: readonly PaketWerkzeug[]  -> 101 Eintraege
          toolRegistry.ts:272  export const PAKET_WERKZEUGE = 110                      -> eine Zahl
        EIN Name, ZWEI Bedeutungen, in EINEM Ordner. Und die 110 meint nicht das Paket (das
        sind 101), sondern 9 Grundeintraege + 101 Paket. Die Bilanz stimmt heute, aber sie
        stimmt durch Zufall und nicht durch Rechnung - nachlesen kann sie niemand, weil der
        Name etwas anderes sagt als der Wert. Genau dieselbe Klasse wie AUS_PAKET_GEHOBEN
        gegen GEHOBEN, nur schon eingebaut.

        Zu tun - die ZAHL in toolRegistry.ts wird umbenannt und abgeleitet:
          GRUNDEINTRAEGE   = 9    benannt, mit einem Satz, warum genau 9
          WERKZEUGE_GESAMT = GRUNDEINTRAEGE + PAKET_WERKZEUGE.length + EIGENE_WERKZEUGE.length
                                              ^ die LISTE aus werkzeugPaket.ts
        Danach gibt es den Namen PAKET_WERKZEUGE nur noch einmal, und zwar als Liste.

        Zusagen:
          WERKZEUGE_GESAMT ist heute 111 - aber gerechnet, nicht getippt. Der Beweis:
            eine Zeile aus der Paket-Liste nehmen -> WERKZEUGE_GESAMT sinkt um 1.
            Heute bliebe die feste 110 stehen und die Bilanz kippte still. DAS ist der Fehler.
          Zaehlung ueber app/tools/: genau EINE Deklaration von PAKET_WERKZEUGE.
      erwartet: "eine Deklaration, WERKZEUGE_GESAMT gerechnet, K-04 weiterhin gruen"
  - id: K-09
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 7 Mutationen: Filter im Katalog aus (Werkzeug doppelt) · Hebeliste ignoriert ·
        Bilanz als feste Zahl statt Summe · unbekannte Id in der Hebeliste wird verschluckt ·
        Kuerzel-Kollision ungeprueft · Registry-Eintrag ohne `art: 'werkzeug'` ·
        Filter an der Quelle statt am Katalog.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - beide Werkzeuge sind in der Leiste und tun etwas."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, angemeldet, Expertenmodus.
        (a) die Leiste nennt "Bemaßen" und "Fläche messen" - beide waehlbar
        (b) Bemassen: zwei Punkte -> eine Massangabe erscheint mit einer ZAHL, nicht nur einer Linie
        (c) Flaeche messen: einen geschlossenen Umriss -> eine Flaeche in m2
        (d) NACHMESSEN: die Zahl aus (b) gegen das Messwerkzeug halten, nicht schaetzen
        PARTNERMESSUNG: "Wand" ist weiterhin waehlbar. Ist sie es nicht, ist die Leiste
        kaputt und nicht das neue Werkzeug.
        Drei Pflicht-Viewports: 1440, 1024, 375.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Das Werkzeug ist doppelt (Registry UND Katalog).                    -> K-05
2  Die Bilanz wird auf 11+100 festgeschrieben statt berechnet.         -> K-03, K-04
3  Eine unbekannte Id in der Hebeliste senkt still die Katalogzahl.    -> K-04 Gegenprobe
4  Kuerzel-Kollision mit einem bestehenden Werkzeug.                   -> K-07
5  Gefiltert wird an der Quelle (paketAdapter) statt am Katalog -
   dann verliert `verworfeneKuerzel()` seine Grundgesamtheit.          -> Ausschluss + K-09
6  Das zweite Werkzeug braucht doch Code.                              -> K-06, und DAS ist rot
7  Das gehobene Werkzeug erscheint in der Leiste, tut aber nichts -
   die Geometrie ist da, die Verdrahtung fehlt.
   OHNE ZUSAGE im Code, mit Grund: das faengt L-01 (b)+(c)+(d) im Browser. Eine Zusage
   ueber "tut etwas" waere eine Zusage ueber die Gestalt, nicht ueber die Wirkung (F-06).
8  `bemassen` schreibt in den Speicher (Massketten sind Knoten?).
   OHNE ZUSAGE, mit Grund: `geometry/masskette.ts` gibt reine Werte zurueck und ruft nichts.
   Ob eine Masskette PERSISTIERT werden soll, ist eine eigene Entscheidung und eine eigene
   Scheibe - hier wird nur angezeigt.
9  `PAKET_WERKZEUGE` ist zweimal deklariert - einmal Liste, einmal Zahl. Die Bilanz
   stuetzt sich auf die getippte Zahl und kippt lautlos, sobald das Paket waechst
   oder schrumpft.                                                     -> K-10
```

## Rückweg und Entdeckung

**Rückweg:** eine Liste, ein Filter, zwei Registry-Einträge. Kein Datenpfad, kein Schema, keine
Migration. **Leere Hebeliste = heutiger Zustand** — der Rückweg ist im Bau selbst enthalten.

**Entdeckung:** die Bilanzgleichung aus K-04. Sie ist die einzige Zahl, die man beobachten muss.
**Stimmt sie nicht mehr, ist ein Werkzeug verschwunden oder doppelt** — und beides sieht in der
Oberfläche gleich harmlos aus.

## Danach

**Welle 2 aus dem Bauplan:** `grundriss-erkennen` · `oeffnung` · `gaube` · `heizkoerper` ·
`verteiler` · `kuechenplanung`. **Wenn W-05 trägt, ist jedes davon eine Zeile plus ein Eintrag —
und der Browsertest, der immer bleibt.**
