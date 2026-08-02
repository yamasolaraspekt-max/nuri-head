# W-06 — `zeile-ersetzen` prüft TypeScript gegen den CODE, nicht gegen die Kommentare

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 12:0x*

```yaml
auftrag:
  id: W-06
  strang: werkzeuge
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Pruefer
  gegengelesen_von: evaluator   # nach d1cecdcf: Werkzeug-Blatt -> Evaluator (Kopfkommentar oben sagt noch Pruefer)
  gegengelesen_am: 2026-08-02
  befund: >
    TRAEGT. Alle Ausgangswerte unabhaengig nachgemessen und exakt (Worktree auf d255a917):
    fangKern 81 auf/87 zu, davon 6 in nummerierten Kommentaren; ohneKommentare zaehle.mjs:41
    mit raute=false; K-01 0/Partner 3; K-02 0/Partner 1; K-04 1/Partner 5, zweiter
    pruef-tmp-Treffer Z.128 ist Kommentar; Suite 82 pass/0 fail. Preflight 4 OK/0 Fehlschlag,
    beide yaml-Bloecke gelesen. EINE kleine Auflage: K-02 zaehlt auch eine blosse
    Kommentar-Erwaehnung von 'zaehle.mjs' als Import — der Generator soll die Importzeile
    (import ... from './zaehle.mjs') belegen; K-03 Zeile 3 bleibt der tragende Beweis.
    B8-Fragen: alle Maschinen-Befehle laufen, messen Wirkung (Partner + Ausgangswerte),
    keiner mutiert.
```

## Warum — eine Mechanik, die nicht trägt, ist schlimmer als keine

**B6 hat `perl -pi` verboten und `zeile-ersetzen` als Ersatz benannt.** Der Prüfer hat am 02.08.
gemessen, dass der Ersatz **gewöhnliche Quelldateien nicht schreiben kann**. Vom Planner
unabhängig nachgemessen:

```text
grep -o '(' resources/planner/hausplaner/geometry/fangKern.ts | wc -l   ->  81
grep -o ')' resources/planner/hausplaner/geometry/fangKern.ts | wc -l   ->  87
```

**Sechs überzählige `)` — alle aus nummerierten Kommentaren (`// 1)` … `// 6)`).** `klammerBilanz`
zählt über den **ganzen Text**, also auch über Kommentare und Zeichenketten. Ergebnis:
`pruefeInhalt(fangKern.ts UNVERÄNDERT, '.ts')` → `false`. **Das Werkzeug zeigt auf die Änderung
des Lesers statt auf sich selbst.**

**Die Tragweite ist keine Bequemlichkeit, sondern die Wirkhierarchie:** *Regel < Mechanik*. Eine
Mechanik, die am gewöhnlichen Fall scheitert, drängt den Benutzer zurück auf das, was die Regel
verboten hat — und dann trägt weder das eine noch das andere.

## Die Entscheidung — der Kommentar-Abzug existiert bereits, er wird nicht zweimal gebaut

```text
scripts/zaehle.mjs:41   export function ohneKommentare(text, { raute = false })
```

**`klammerBilanz` ruft ihn auf, statt einen zweiten zu bekommen.** *Zwei Antworten auf die Frage
„was ist hier Kommentar?" sind eine zweite Wahrheit — dieselbe Klasse wie der doppelte Name
`PAKET_WERKZEUGE` in W-05.*

**Nebengewinn, ausdrücklich nicht das Ziel:** `ohneKommentare` maskiert Zeichenketten, bevor es
Kommentare abzieht (sein eigener Fehler 1, dort behoben). **Damit fällt zugleich die bekannte
Backtick-Grenze weg**, die der Evaluator am 01.08. benannt hat — Template-Literale sind
Zeichenketten. *Das ist ein Nebengewinn und keine Zusage: wer ihn zur Zusage macht, verspricht
eine Syntaxprüfung, die es weiterhin nicht ist.*

## Die zweite Kante — die Hilfsdatei entsteht am falschen Ort

```text
scripts/zeile-ersetzen.mjs   `${pfad}.pruef-tmp${extname(pfad)}`     2 Treffer
```

**Die Prüfdatei entsteht NEBEN der Quelle, im Arbeitsbaum.** Auf diesem Mount ist `unlink`
verboten (F-10) — sie **bleibt liegen und lässt sich nicht entfernen**. Und sie entsteht, *bevor*
die Drift-Sperre greift: auch ein abgelehnter Ersatz hinterlässt sie. **Sie gehört unter
`os.tmpdir()`**, mit der Endung des Ziels (der Grund für die Endung steht schon im Kopf des
Werkzeugs und bleibt gültig).

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/zeile-ersetzen.mjs      klammerBilanz + der Pfad der Hilfsdatei
  scripts/__tests__/…             die Zusagen dazu

Hier bewusst NICHT:
  scripts/zaehle.mjs              wird nur GERUFEN. Wer ihn hier anfasst, aendert
                                  gleichzeitig die Grundlage von AUF-38 und W-01.
  Ein echter TypeScript-Parser    Waere die ehrliche Loesung und ist eine eigene
                                  Entscheidung: sie zoege tsc oder ein Paket in ein
                                  Werkzeug, das heute ohne Abhaengigkeit laeuft.
                                  W-06 macht die BILANZ ehrlich, es macht sie nicht
                                  zur Syntaxpruefung.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/zeile-ersetzen.mjs
  population_command: "grep -o 'klammerBilanz' scripts/zeile-ersetzen.mjs | wc -l"
  ausschluesse:
    - stelle: "scripts/zaehle.mjs"
      grund: "Wird nur gerufen. Eine Aenderung dort trifft AUF-38 und W-01 mit."
      entschieden_von: planner
    - stelle: "Ein echter TypeScript-Parser"
      grund: "Zoege eine Abhaengigkeit in ein bisher abhaengigkeitsfreies Werkzeug. Eigene Entscheidung."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Bilanz benutzt den vorhandenen Kommentar-Abzug."
    pruefung:
      befehl: "grep -o 'ohneKommentare' scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 02.08. 12:0x; Partner 'klammerBilanz' -> 3, die Messung ist nicht leer)"

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Der Abzug wird aus zaehle.mjs IMPORTIERT und nicht nachgebaut - belegt an der IMPORTZEILE."
    pruefung:
      befehl: "grep -o \"from './zaehle.mjs'\" scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 02.08. 14:0x; Partner \"from 'js-yaml'\" -> 1, die Messung ist nicht leer)"
    gegenbeweis: |
      AUFLAGE des Evaluators (02.08., B8-Gegenlesung), uebernommen: das erste Muster war das
      blosse Wort `zaehle.mjs`. Das haette auch eine ERWAEHNUNG im Kommentar gezaehlt - ein
      Satz wie "wir koennten zaehle.mjs rufen" haette das Kriterium gruen gemacht, ohne dass
      ein einziger Aufruf existiert. Gemessen wird jetzt die IMPORTZEILE.
      Zweite Korrektur des Planners, noch vor dem Gegenlesen: der allererste Entwurf mass
      `replace(/` und behauptete Ausgangswert 0 - nachgemessen ist es 1 (`.replace(/\n$/, '')`
      am Ende des Werkzeugs). Das Kriterium waere von Anfang an rot und nie erfuellbar gewesen.
      Steht hier nach dem Bau 0, obwohl K-01 gruen ist, hat jemand `ohneKommentare` im
      Werkzeug NACHGEBAUT statt es zu rufen. Dann gibt es zwei Antworten auf die Frage
      "was ist Kommentar?", und die zweite driftet, sobald die erste dazulernt - und sie
      lernt dazu: `ohneKommentare` hat seinen Zeichenketten-Fehler bereits einmal korrigiert.
      DER TRAGENDE BEWEIS bleibt K-03 Zeile 3 (ueberzaehlige `)` NUR im Kommentar -> true) -
      diese Zusage faellt, wenn der Abzug ausgebaut wird, und sie laesst sich nicht
      gruen tippen.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Eine UNVERAENDERTE echte Quelldatei traegt - und eine echt kaputte nicht."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion `pruefeInhalt`, nicht ueber die Kommandozeile:
          pruefeInhalt(<Inhalt von geometry/fangKern.ts>, '.ts')            -> true
            (heute false; 81 auf zu 87 zu, sechs davon in `// 1)`-Kommentaren)
          pruefeInhalt(<derselbe Inhalt, eine `}` im CODE entfernt>, '.ts') -> false
          pruefeInhalt(<Inhalt mit einer ueberzaehligen `)` NUR im Kommentar>, '.ts') -> true
        Die dritte Zeile ist die eigentliche Zusage: sie faellt, wenn jemand den Abzug
        wieder ausbaut, und sie faellt NICHT, wenn er nur die Zahl anpasst.
      erwartet: "drei Zusagen, davon eine ROTE"

  - id: K-04
    typ: absence
    kritikalitaet: P1
    aussage: "Die Hilfsdatei entsteht nicht mehr im Arbeitsbaum."
    pruefung:
      befehl: "grep -o 'pfad}.pruef-tmp' scripts/zeile-ersetzen.mjs | wc -l"
      erwartet: "0"
    ausgangswert: "1 (gemessen 02.08. 12:1x mit genau diesem Befehl; der zweite Treffer von 'pruef-tmp' steht im Kopfkommentar und ist kein Pfad. Partner 'writeFileSync' -> 5, die Messung ist nicht leer)"
    gegenbeweis: |
      Auf diesem Mount ist `unlink` verboten (F-10). Eine Hilfsdatei neben der Quelle bleibt
      liegen, taucht in `git status` auf und laesst sich nicht entfernen - und sie entsteht
      HEUTE sogar dann, wenn der Ersatz anschliessend abgelehnt wird.

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Hilfsdatei landet unter dem Systemtemp und behaelt die Endung des Ziels."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der Grund fuer die ENDUNG steht im Kopf des Werkzeugs und bleibt gueltig: `node --check`
        liest eine unbekannte Endung als CommonJS, und dann scheitert jede gueltige ESM-Datei.
          Pfad der Hilfsdatei beginnt mit os.tmpdir()
          Pfad der Hilfsdatei endet auf die Endung des Ziels
          nach einem ABGELEHNTEN Ersatz liegt neben der Quelle KEINE neue Datei
        Die dritte Zeile ist die rote: sie ist heute verletzt.
      erwartet: "drei Zusagen, davon eine ROTE"

  - id: K-06
    typ: behavioural
    aussage: "Der Fehler vom 01.08. 20:0x bleibt gefangen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der Anlass des Werkzeugs war eine verwaiste Klammer NACH einem Splice in
        auftrag-pruefen.mjs. Diese Zusage bleibt und muss GRUEN bleiben, ohne angepasst
        zu werden. Wird sie angefasst, um gruen zu werden, ist der Abzug zu gierig.
      erwartet: "gruen, Zusage unveraendert"

  - id: K-07
    typ: behavioural
    aussage: "Die Werkzeug-Suite bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/"
      erwartet: "0 fail. Ausgangswert 82 pass / 0 fail (Evaluator, 02.08.). Danach mehr oder gleich, nie weniger."

  - id: K-08
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: Abzug ausgebaut (roher Text wie heute) · Abzug auch auf
        Zeichenketten angewandt (Code in Strings verschwindet) · nur `{}` geprueft, `()` nicht ·
        Bilanz gibt immer true zurueck · Hilfsdatei wieder neben die Quelle · Hilfsdatei ohne
        Endung. Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - das Werkzeug hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen: W-06 aendert ein Kommandozeilen-Werkzeug.
        Der Beleg ist die Suite aus K-07 plus die drei Zusagen aus K-03, nicht ein Schirm.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Der Abzug frisst Code, der wie ein Kommentar aussieht.              -> K-03 dritte Zeile, K-08
2  Ein zweiter Kommentar-Abzug wird nachgebaut.                        -> K-02
3  Die Hilfsdatei bleibt im Arbeitsbaum liegen und ist nicht loeschbar.-> K-04, K-05
4  Die Hilfsdatei verliert ihre Endung -> `node --check` liest CommonJS
   und JEDE gueltige ESM-Datei faellt durch.                           -> K-05 zweite Zeile
5  Die Bilanz wird fuer eine Syntaxpruefung gehalten.
   OHNE ZUSAGE, mit Grund: sie ist und bleibt eine Bilanz. Der Kopf des Werkzeugs sagt das
   bereits; W-06 macht sie ehrlich, nicht vollstaendig. Ein echter Parser ist eine eigene
   Entscheidung und steht als Ausschluss im Blatt.
6  `ohneKommentare` behandelt `#` nur mit `--raute`. Fuer `.ts` ist das richtig -
   dort ist `#` ein privates Feld, kein Kommentar.
   OHNE ZUSAGE, mit Grund: der Vorgabewert ist bereits `raute = false`, und genau der
   wird gebraucht. Eine Zusage darueber waere eine Zusage ueber zaehle.mjs, das hier
   ausgeschlossen ist.
```

## Rückweg und Entdeckung

**Rückweg:** eine Datei, ein Aufruf, ein Pfad. Kein Datenpfad, kein Schema. **Der Commit lässt
sich zurückdrehen** — und der Zustand davor ist der heutige, in dem das Werkzeug `.ts` ablehnt.

**Entdeckung:** K-03 dritte Zeile. **Wenn jemand den Abzug zu gierig macht, verschwindet Code aus
der Bilanz und eine wirklich kaputte Datei geht durch** — das sieht wie ein Erfolg aus und ist der
gefährlichere Fall von beiden.
