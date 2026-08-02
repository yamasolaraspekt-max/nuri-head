# W-06 — `zeile-ersetzen` FRAGT den TypeScript-Parser, statt Zeichen zu zählen

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 11:5x · NEU GESCHNITTEN 16:0x nach dem Widerspruch des Generators*

```yaml
auftrag:
  id: W-06
  strang: werkzeuge
  status: entwurf   # ZURUECKGESETZT von `bereit` auf `entwurf` am 02.08. 16:0x. Die ENTSCHEIDUNG hat sich geaendert, nicht nur eine Auflage - das erste Gegenlesen des Evaluators (8b3868b1) galt einem anderen Blatt. Es braucht ein neues.
  gegengelesen_von: evaluator   # zweites Gegenlesen - neue Entscheidung, neues Blatt (Kopf sagt es selbst)
  gegengelesen_am: 2026-08-03
  befund: >
    TRAEGT. Kernmechanik selbst geprobt (Worktree a7be48b0): ts.createSourceFile nimmt
    fangKern.ts UND breiten.test.ts an (beide heute unschreibbar) und weist
    'const a = { x: 1;' mit benannter Diagnose ab. Abhaengigkeit belegt: typescript 5.9.3
    in node_modules UND package.json, hook-abhaengigkeiten.mjs nutzt createSourceFile
    bereits. Ausgangswerte exakt: typescript 0/Partner js-yaml 1, klammerBilanz 3,
    pruef-tmp 2, 319 Dateien. Die 61 Durchfaller habe ich NICHT selbst gezaehlt - zwei
    getrennte Messungen (Planner+Generator) liegen vor, K-03 verbietet zu Recht den
    nachgebauten Zaehler. Zwei kleine Punkte, keine Auflagen: der Beleg breiten.test.ts:51
    liegt real bei :63; und hook-abhaengigkeiten uebergibt ScriptKind.TSX explizit,
    waehrend das Blatt-Rezept auf die Endungs-Ableitung baut - sie traegt (geprobt), der
    Generator moege die K-05-.tsx-Faelle mit echtem JSX-Inhalt fahren, dann ist genau das
    belegt. B8-Fragen: Befehle laufen, K-03s rote Gegenproben messen die Wirkung, kein
    maschineller Befehl mutiert. Fail-safe-Gedanke gecheckt: faellt parseDiagnostics je
    weg (internes API), wirft das Werkzeug laut statt still gruen.
  ruecknahme: "Der Planner nimmt die Entscheidung der ersten Fassung ZURUECK. Sie lautete: die Bilanz ehrlich machen, aber nicht zur Syntaxpruefung. Der Generator hat VOR dem Bau gemessen, dass sie damit 2 von 61 Faellen loest - und der Ausschlussgrund, ein Parser zoege eine Abhaengigkeit in ein abhaengigkeitsfreies Werkzeug, war schlicht falsch."
```

## Der Widerspruch des Generators — und er trifft

**Er hat W-06 nicht gebaut, sondern vorher gemessen. Mit genau der Funktion, die mein Blatt
vorschrieb:**

```text
319 .ts/.tsx-Dateien im Hausplaner
heute (roher Text)                    61 fallen durch
MIT `ohneKommentare` — mein Fix       59 fallen durch
zusaetzlich Texte + Regex maskiert    57 fallen durch
```

**Vom Planner unabhängig nachgemessen (02.08. 16:0x, eigenes Programm):**

```text
Dateien gesamt       319
heute FALLEN DURCH    61
mit dem PARSER         0
```

**Mein Fix hätte ZWEI Dateien befreit und 59 unschreibbar gelassen — während das Blatt sagt, das
Werkzeug sei repariert.** *Grün gegen K-01, K-02 und K-03, und die Wirkung wäre ausgeblieben.
Genau die Gestalt, vor der F-06 warnt: die Zusage misst die Stelle, nicht die Wirkung.*

## Die Ursache war nicht der Kommentar

```text
__tests__/breiten.test.ts:51
  const kopf = css.match(/\.hp-studio-kopf \{([^}]*)\}/);
                                          \{   [^}]  \}     1 auf · 2 zu
```

**Kein Kommentar, keine Zeichenkette — Code.** *Und `ohneKommentare` setzt Zeichenketten am Ende
ausdrücklich zurück (`zaehle.mjs:56`, „ihr Inhalt ist Code, kein Kommentar") — richtig fürs Zählen
von Vorkommen, falsch für eine Klammerbilanz.*

**Der Satz des Generators, der die ganze erste Fassung erledigt:** *eine zeichenzählende Bilanz
ist auf echtem TypeScript grundsätzlich nicht zuverlässig — **sie misst Häufigkeit und nennt es
Syntax**.*

## Mein Ausschlussgrund war falsch, und zwar doppelt

**Die erste Fassung schloss einen Parser aus mit der Begründung, er *„zöge eine Abhängigkeit in
ein bisher abhängigkeitsfreies Werkzeug"*. Gemessen:**

```text
node_modules/typescript          vorhanden, Version 5.9.3
package.json                     typescript ist GELISTET
scripts/hook-abhaengigkeiten.mjs:70   benutzt ts.createSourceFile BEREITS
```

**Die Abhängigkeit ist da, sie ist erklärt, und ein Werkzeug im selben Ordner benutzt genau
diesen Aufruf schon.** *Ich habe einen Preis behauptet, ohne ihn zu messen — dieselbe Klasse wie
die „vier Stellen" im Werkzeug-Bauplan und die „60 ungepushten Commits". Der dritte Fall
derselben Art in zwei Tagen.*

## Die neue Entscheidung

```text
pruefeInhalt fragt fuer .ts .tsx .mjs .js DEN PARSER:
  ts.createSourceFile(pfad, text, ts.ScriptTarget.Latest, true).parseDiagnostics.length === 0

EINE Quelle statt vier Naeherungen. Keine Klammerbilanz, kein `ohneKommentare`,
kein Hilfsdatei-Umweg fuer `node --check`.
```

**Selbst gemessen, bevor das hier stand:**

```text
fangKern.ts        (faellt heute durch)   ->  TRAEGT
breiten.test.ts    (das Regex-Beispiel)   ->  TRAEGT
zeile-ersetzen.mjs                        ->  TRAEGT
`const a = { x: 1;`                       ->  ERKANNT
```

**`node --check` für `.mjs`/`.js` fällt damit weg.** *Das ist eine Entscheidung dieses Blattes und
keine Nebenwirkung:* der Parser beantwortet dieselbe Frage ohne Hilfsdatei — **und damit
verschwindet zugleich die zweite Kante der ersten Fassung**, die Prüfdatei neben der Quelle, die
auf diesem Mount nicht löschbar war (F-10). *Zwei Probleme, eine Entfernung.*

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/zeile-ersetzen.mjs      pruefeInhalt + der Wegfall von klammerBilanz und Hilfsdatei
  scripts/__tests__/zeileErsetzen.test.mjs    die Zusagen dazu

Hier bewusst NICHT:
  scripts/zaehle.mjs              `ohneKommentare` bleibt, wie es ist - es ZAEHLT VORKOMMEN,
                                  und dafuer ist es richtig. Es war nur die falsche Antwort
                                  auf eine Syntaxfrage.
  scripts/hook-abhaengigkeiten.mjs   benutzt den Parser bereits. Nicht anfassen - es ist der
                                  BELEG, dass das Muster im Haus ist, nicht sein Gegenstand.
  Typpruefung (tsc)               `parseDiagnostics` ist SYNTAX. Ob die Typen stimmen, ist
                                  eine andere Frage und gehoert ins Gate des Generators.
                                  Wer das hier hineinzieht, macht aus einem Schreib-Riegel
                                  einen Compiler.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/zeile-ersetzen.mjs
  population_command: "grep -o 'pruefeInhalt' scripts/zeile-ersetzen.mjs | wc -l"
  ausschluesse:
    - stelle: "scripts/zaehle.mjs"
      grund: "`ohneKommentare` ZAEHLT VORKOMMEN und ist dafuer richtig. Es war die falsche Antwort auf eine Syntaxfrage, kein falsches Werkzeug."
      entschieden_von: planner
    - stelle: "scripts/hook-abhaengigkeiten.mjs"
      grund: "Benutzt den Parser bereits - es ist der Beleg, dass das Muster im Haus ist, nicht der Gegenstand dieser Scheibe."
      entschieden_von: planner
    - stelle: "Typpruefung (tsc)"
      grund: "parseDiagnostics ist SYNTAX. Typen gehoeren ins Gate des Generators. Sonst wird aus einem Schreib-Riegel ein Compiler."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Werkzeug fragt den Parser."
    pruefung:
      befehl: "grep -o 'typescript' scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 02.08. 16:0x; Partner 'js-yaml' -> 1, die Messung ist nicht leer)"

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Die Klammerbilanz ist WEG, nicht nur umgangen."
    pruefung:
      befehl: "grep -o 'klammerBilanz' scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "0"
    ausgangswert: "3"
    gegenbeweis: |
      Bleibt sie als toter Zweig stehen, findet sie der naechste Leser und haelt sie fuer
      eine gueltige Pruefung - sie SIEHT ja aus wie eine. Eine Naeherung, die niemand mehr
      ruft, ist keine Sicherheit, sondern eine Falle mit Halbwertszeit.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG, und sie ist die ganze Scheibe: 61 unschreibbare Dateien werden 0."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Ueber ALLE .ts/.tsx im Hausplaner, mit `pruefeInhalt` selbst - nicht mit einem
        nachgebauten Zaehler:
          319 Dateien geprueft
          VORHER   61 fallen durch   (gemessen 02.08. 16:0x, Planner und Generator getrennt)
          NACHHER   0 fallen durch
        UND die ROTE Gegenprobe, sonst misst die Zusage nur, dass etwas immer true sagt:
          `const a = { x: 1;`                       -> faellt durch
          eine echte Datei mit entfernter `}`       -> faellt durch
          dieselbe Datei unveraendert               -> traegt
        Die drei Gegenproben sind die eigentliche Zusage. Ein Pruefer, der alles durchlaesst,
        macht die erste Zahl ebenfalls zu 0.
      erwartet: "vier Zusagen, davon drei Gegenproben, eine davon ROT"

  - id: K-04
    typ: absence
    kritikalitaet: P1
    aussage: "Keine Hilfsdatei mehr - das Problem verschwindet mit dem Umweg, den es brauchte."
    pruefung:
      befehl: "grep -o 'pruef-tmp' scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "0"
    ausgangswert: "2 (davon 1 im Kopfkommentar, 1 als Pfad - beide fallen weg)"
    gegenbeweis: |
      Auf diesem Mount ist `unlink` verboten (F-10). Die Hilfsdatei entstand NEBEN der Quelle
      und blieb liegen - auch dann, wenn der Ersatz anschliessend abgelehnt wurde. Der Parser
      braucht keine Datei; damit ist die Kante nicht gemildert, sondern fort.

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "EINE Quelle fuer alle vier Endungen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        `.ts` `.tsx` `.mjs` `.js` laufen durch DENSELBEN Zweig:
          eine heile Datei jeder Endung   -> traegt
          eine kaputte Datei jeder Endung -> faellt
          und: `.mjs` braucht KEINE Hilfsdatei mehr (K-04 misst die Stelle, hier die Wirkung)
        Der `node --check`-Zweig faellt weg. Bleibt er als zweiter Weg stehen, gibt es
        wieder zwei Antworten auf dieselbe Frage - dieselbe Klasse wie `PAKET_WERKZEUGE`
        in W-05 K-10 und wie der doppelte Kommentar-Abzug in der ERSTEN Fassung dieses Blattes.
      erwartet: "acht Zusagen plus die Wegfall-Zusage"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Fehler vom 01.08. 20:0x bleibt gefangen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der Anlass des Werkzeugs war eine verwaiste Klammer NACH einem Splice in
        auftrag-pruefen.mjs. Diese Zusage bleibt und muss GRUEN bleiben, ohne angepasst
        zu werden. Wird sie angefasst, um gruen zu werden, ist der neue Pruefer zu lasch.
      erwartet: "gruen, Zusage unveraendert"

  - id: K-07
    typ: behavioural
    aussage: "Die Werkzeug-Suite bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.mjs"   # NICHT das Verzeichnis: `node --test <verz>/` wirft auf Node 22 MODULE_NOT_FOUND
      erwartet: "0 fail. Ausgangswert 82 pass / 0 fail (Evaluator, 02.08.). Danach mehr oder gleich, nie weniger."

  - id: K-08
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 7 Mutationen: parseDiagnostics ignoriert (immer true) · nur `.ts` geprueft,
        `.tsx` faellt zurueck auf die Bilanz · Klammerbilanz als Rueckfall stehen gelassen ·
        Diagnostik-Laenge mit `>= 0` statt `=== 0` verglichen · `setParentNodes` false und
        daraus falsche Schluesse · Hilfsdatei aus Gewohnheit wieder angelegt · Typfehler
        als Syntaxfehler gewertet (dann faellt gueltiger Code durch).
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - das Werkzeug hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen. Der Beleg ist K-03 ueber 319 Dateien
        plus die Suite aus K-07, nicht ein Schirm.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Der Pruefer laesst alles durch, und die 61 werden nur deshalb 0.     -> K-03 Gegenproben
2  Die Klammerbilanz bleibt als toter Zweig liegen.                     -> K-02
3  Zwei Wege fuer dieselbe Frage (`node --check` bleibt daneben).       -> K-05
4  Die Hilfsdatei wird aus Gewohnheit wieder angelegt.                  -> K-04, K-08
5  Typfehler werden als Syntaxfehler gewertet - dann faellt gueltiger
   Code durch und das Werkzeug schreibt gar nichts mehr.                -> K-08, Ausschluss
6  Der Parser ist langsamer als eine Zeichenzaehlung.
   OHNE ZUSAGE, mit Grund: es geht um EINE Datei je Aufruf, nicht um 319 im Lauf.
   Die 319 laufen nur EINMAL, in K-03. Eine Laufzeitzusage waere hier eine Zahl ohne
   Schmerz - und die haben wir genug.
7  `typescript` verschwindet eines Tages aus den Abhaengigkeiten.
   OHNE ZUSAGE, mit Grund: es steht in package.json und wird von `hook-abhaengigkeiten.mjs`
   und vom Gate `tsc:hausplaner` gebraucht. Faellt es, faellt vorher anderes lauter.
```

## Rückweg und Entdeckung

**Rückweg:** eine Datei, ein Zweig weniger, ein Import mehr. **Der Zustand davor ist der heutige** —
61 unschreibbare Dateien und eine Regel, die auf ein Werkzeug zeigt, das ihr nicht folgen kann.

**Entdeckung:** K-03 erste Zahl **ohne** die Gegenproben. *Wenn jemand den Prüfer lasch macht,
werden die 61 ebenfalls zu 0 — und das sieht in jedem Bericht wie ein Erfolg aus.* **Deshalb ist
die rote Gegenprobe hier nicht Beiwerk, sondern die eigentliche Zusage.**

## Was daraus für den Zyklus folgt

**Der Generator hat VOR dem Bau widersprochen, gemessen, und gewartet statt gebaut.** *Das ist der
zweite Fall an einem Tag — bei Z-05 hat derselbe Weg funktioniert.* **Beide Male hat der
Widerspruch ein Blatt des Planners korrigiert, und beide Male war der Fehler dieselbe Klasse: ein
Preis oder eine Wirkung BEHAUPTET statt gemessen.**
