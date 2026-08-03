# W-06-N1 — Fehlt die Diagnose, ist das ein FEHLSCHLAG und kein Freibrief

**Spur B** · **Heimat: ticket** · **Basis: W-06 gebaut** · *Geschnitten 03.08. auf den Befund des Generators gegen seinen eigenen Bau*

```yaml
auftrag:
  id: W-06-N1
  strang: werkzeuge
  status: bereit   # B8 ERFUELLT: gegengelesen vom Evaluator 03.08. (TRAEGT), fail-open selbst nachgeprobt. Kleine Auflage eingearbeitet 03.08. 01:0x: scope.dateien nannte "resources/../scripts/..." - ein Pfad mit "..", den die frisch gebaute W-07-Zielpruefung in jedem befehl abweisen wuerde. Normalisiert auf scripts/zeile-ersetzen.mjs. Eingetragen vom Planner.
  gegengelesen_von: evaluator   # Werkzeug-Blatt -> Evaluator (B8/d1cecdcf; Kopfkommentar sagt noch Pruefer)
  gegengelesen_am: 2026-08-03
  befund: >
    TRAEGT, mit einer kleinen Auflage und einem Eingestaendnis. Probe selbst gefahren
    (node, am deklarierten Boden "W-06 gebaut"): mit Diagnose false, nach delete
    parseDiagnostics mit ?? [] true - fail-open belegt, Zeile 109 exakt wie im Blatt,
    K-01-Ausgangswert 1 bestaetigt. Die Entscheidung (fehlend=FALSE und laut, leer=TRUE,
    plus Selbstprobe mit roter Gegenprobe) unterscheidet die beiden Richtungen sauber -
    K-02s zweite Zeile und K-05 verhindern, dass der Riegel zum Riegel gegen alles wird.
    EINGESTAENDNIS: mein W-06-Gegenlesungs-Befund nannte den Parser fail-safe ("wirft
    laut statt still gruen") - das galt dem Rezept ohne ?? []; der Bau hat die Haertung
    gedreht, und der Generator hat es gegen den eigenen Bau gefunden, bevor es zu mir in
    die Abnahme kam. KLEINE AUFLAGE: scope.dateien nennt "resources/../scripts/…" - ein
    Pfad mit "..", den die frisch gebaute W-07-Zielpruefung in jedem befehl abweisen
    wuerde; normalisieren auf scripts/zeile-ersetzen.mjs. Kopf "Basis: W-06 gebaut" ist
    die richtige Lehre aus den Basis-Luecken - ehrlich benannt statt HEAD behauptet.
    B8-Fragen: Befehle laufen, K-04 misst Wirkung mit roter Gegenprobe, keiner mutiert.
```

## Der Befund kommt vom Generator, gegen seinen EIGENEN Bau

**Er hat W-06 gebaut und danach gemeldet, dass sein Ersatz eine Lücke hat, die der alte Zustand
nicht hatte. Vom Planner nachgemessen (03.08.):**

```text
scripts/zeile-ersetzen.mjs:109
  return (quelle.parseDiagnostics ?? []).length === 0;

Probe mit ENTFERNTER Eigenschaft:
  ts.createSourceFile('x.ts', 'const a = { x: 1;', …)   -> Syntaxfehler
  delete sf.parseDiagnostics
  (sf.parseDiagnostics ?? []).length === 0              -> TRUE   <- ALLES traegt
```

**`parseDiagnostics` ist eine INTERNE Eigenschaft von TypeScript, nicht Teil der öffentlichen
Schnittstelle.** *Fällt sie bei einem Update weg, liefert `?? []` eine leere Liste — und die
Sperre sagt zu allem ja.*

## Warum das schlimmer ist als der Zustand davor

```text
Klammer-Bilanz (alt)   sperrte ZU VIEL      61 Dateien unschreibbar   faellt SOFORT auf
Parser (neu)           kann ZU WENIG sperren                          faellt GAR NICHT auf
```

**Eine Barriere, die zu streng ist, meldet sich. Eine, die still stirbt, sieht weiter grün aus.**
*Dieselbe Klasse wie das `awk system()`-Loch aus F-18 und wie der Näherungs-Hinweis, der das
Neuladen nicht überlebt: nicht das Fehlen ist das Problem, sondern dass das Fehlen wie Erfolg
aussieht.*

**Und der Satz des Generators, der es auf den Punkt bringt:** *„Ich baue das nicht eigenmächtig
nach — W-06 steht auf `gebaut` und liegt in der Abnahme. Ein Nachschieben wäre Weiterbauen an
einem Blatt, das gerade geprüft wird."* **Richtig. Deshalb ein eigenes Blatt.**

## Die Entscheidung

```text
Fehlt `parseDiagnostics`, ist das ein FEHLSCHLAG - die Datei traegt NICHT.
Nicht `?? []`, sondern: keine Liste -> false, und zwar LAUT.
```

**Und weil eine still gestorbene Barriere ihren Tod nicht meldet, kommt eine zweite Sicherung
dazu:** *das Werkzeug prüft beim Start EINMAL an einem eingebauten Beispiel, dass der Parser
überhaupt noch Fehler findet.*

```text
SELBSTPROBE beim Start:
  ein Schnipsel mit bekanntem Syntaxfehler wird geparst
    findet der Parser ihn NICHT  ->  das Werkzeug bricht ab und sagt, warum
```

*Das ist derselbe Gedanke wie die Kontrolle A/A' im Z-06-Browsertest des Generators:* **erst weil
der bekannte Fehlerfall rot wird, bedeutet ein grünes Urteil etwas.**

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/zeile-ersetzen.mjs                    Zeile 109 + die Selbstprobe
  scripts/__tests__/zeileErsetzen.test.mjs      die Zusagen dazu

Hier bewusst NICHT:
  Zurueck zur Klammer-Bilanz    Sie loeste 2 von 61 Faellen. Der Parser ist die richtige
                                Naht - er braucht nur einen Riegel gegen das stille Sterben.
  Eine eigene TS-Version pinnen  Waere die groessere Antwort auf "interne API" und ist eine
                                eigene Entscheidung: sie bindet das ganze Repo an eine
                                Version, nicht nur dieses Werkzeug.
  Die oeffentliche TS-Schnittstelle statt parseDiagnostics
                                `ts.createProgram` liefert oeffentliche Diagnostik, laedt
                                aber das ganze Projekt samt tsconfig - fuer EINE Datei je
                                Aufruf ist das die falsche Groesse. Eigene Entscheidung,
                                falls die Selbstprobe je anschlaegt.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/zeile-ersetzen.mjs
  population_command: "grep -o 'parseDiagnostics' scripts/zeile-ersetzen.mjs | wc -l"
  ausschluesse:
    - stelle: "Zurueck zur Klammer-Bilanz"
      grund: "Sie loeste 2 von 61 Faellen - gemessen vom Generator und vom Planner getrennt."
      entschieden_von: planner
    - stelle: "Eine eigene TS-Version pinnen"
      grund: "Bindet das ganze Repo an eine Version, nicht nur dieses Werkzeug. Eigene Entscheidung."
      entschieden_von: planner
    - stelle: "ts.createProgram statt parseDiagnostics"
      grund: "Laedt das ganze Projekt samt tsconfig - fuer EINE Datei je Aufruf die falsche Groesse. Eigene Entscheidung, falls die Selbstprobe je anschlaegt."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "Kein `?? []` mehr hinter der Diagnose."
    pruefung:
      befehl: "grep -o 'parseDiagnostics ?? ' scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "0"
    ausgangswert: "1 (gemessen 03.08.; Partner 'parseDiagnostics' gesamt -> mehrfach, die Messung ist nicht leer)"
    gegenbeweis: |
      `?? []` macht aus einer fehlenden Diagnose eine leere Diagnose - und aus "ich weiss es
      nicht" ein "alles in Ordnung". Das ist die gefaehrlichste Form des Schweigens: sie
      sieht aus wie ein Ergebnis.

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG: fehlt die Diagnose, faellt die Datei durch."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion:
          ein Quelltext-Objekt OHNE `parseDiagnostics`  -> pruefeInhalt liefert FALSE
          dasselbe mit leerer Diagnose-Liste            -> liefert TRUE
        Die zweite Zeile ist die Gegenprobe: eine leere Liste ist ein ECHTES Ergebnis
        ("keine Fehler gefunden") und muss weiterhin durchgehen. Wer beide Faelle
        gleich behandelt, hat das Problem nur umgedreht.
      erwartet: "zwei Zusagen, und sie muessen sich UNTERSCHEIDEN"

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Die Selbstprobe existiert."
    pruefung:
      befehl: "grep -oi 'selbstprobe' scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Selbstprobe schlaegt an, wenn der Parser blind wird."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Ein eingebauter Schnipsel mit bekanntem Syntaxfehler wird beim Start geparst.
          Parser findet ihn      -> das Werkzeug arbeitet normal weiter
          Parser findet ihn NICHT -> ABBRUCH mit Grund im Klartext
        Und die rote Gegenprobe, ohne die das nur eine Zeile Code ist:
          die Diagnose kuenstlich abschalten -> das Werkzeug bricht ab, statt zu schreiben
        Das ist derselbe Gedanke wie die Kontrolle A/A' im Z-06-Browsertest: erst weil der
        bekannte Fehlerfall rot wird, bedeutet ein gruenes Urteil etwas.
      erwartet: "drei Zusagen, davon eine ROTE"

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die 61-auf-0-Wirkung von W-06 bleibt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Ueber ALLE .ts/.tsx im Hausplaner, mit `pruefeInhalt` selbst:
          319 Dateien, 0 fallen durch   (Stand nach W-06)
        Ein Riegel gegen fail-open darf nicht zum Riegel gegen alles werden.
        Faellt hier wieder etwas durch, ist die Selbstprobe zu scharf.
      erwartet: "0 Durchfaller, unveraendert"

  - id: K-06
    typ: behavioural
    aussage: "Die Werkzeug-Suite bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.mjs"
      erwartet: "0 fail. Ausgangswert 91 pass / 0 fail (Generator, 03.08. nach W-06)."

  - id: K-07
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: `?? []` wieder eingebaut · fehlende Diagnose als leere
        behandelt · Selbstprobe entfernt · Selbstprobe laeuft, aber ihr Ergebnis wird
        ignoriert · Selbstprobe so scharf, dass gueltige Dateien fallen · Abbruch ohne Grund.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - das Werkzeug hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen. Der Beleg ist K-04 mit seiner roten
        Gegenprobe und K-05 ueber 319 Dateien.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  `?? []` bleibt und macht aus "weiss nicht" ein "in Ordnung".        -> K-01, K-02
2  Fehlende und leere Diagnose werden gleich behandelt - das Problem
   ist dann nur umgedreht, und gueltige Dateien fallen durch.          -> K-02 zweite Zeile
3  Die Selbstprobe laeuft, aber ihr Ergebnis wird nicht ausgewertet.   -> K-04, K-07
4  Die Selbstprobe ist zu scharf und sperrt gueltige Dateien.          -> K-05
5  Der Abbruch schweigt.                                               -> K-04
6  Ein TS-Update aendert `parseDiagnostics`, ohne sie zu entfernen -
   etwa ihr Format.
   OHNE ZUSAGE, mit Grund: die Selbstprobe faengt genau diesen Fall MIT, weil sie das
   ERGEBNIS prueft ("wird der bekannte Fehler gefunden?") und nicht die Form der Liste.
   Eine eigene Zusage ueber das Format waere eine Zusage ueber die Gestalt (F-06).
7  Das Werkzeug laedt den ganzen TypeScript-Compiler fuer eine Datei.
   OHNE ZUSAGE, mit Grund: benannt vom Generator als Kosten des Weges, und es ist eine
   Kosten- und keine Korrektheitsfrage. Eine Laufzeitzusage waere hier eine Zahl ohne
   Schmerz - es geht um EINE Datei je Aufruf.
```

## Rückweg und Entdeckung

**Rückweg:** eine Zeile und ein Startblock. **Der Zustand davor ist der heutige** — der Parser
trägt, und wenn er eines Tages blind wird, merkt es niemand.

**Entdeckung:** K-02 zweite Zeile. **Wer fehlende und leere Diagnose gleich behandelt, hat das
Loch nur umgedreht** — dann fällt jede gültige Datei durch, das Werkzeug schreibt gar nichts mehr,
und der Schaden ist sofort da statt später. *Beide Richtungen sind falsch; die Zusage muss sie
unterscheiden.*
