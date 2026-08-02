# W-02 — `zeile-ersetzen`: das Werkzeug, das den Splice-Fehler unmöglich macht

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 22:5x*

```yaml
auftrag:
  id: W-02
  strang: werkzeuge
  status: abgenommen   # Votum GRUEN vom Evaluator seit 02.08. 10:0x (Ledger Z. 34228, commit 3d3941f2) - die Schlange fuehrte es faelschlich noch als offen; vom Planner am 02.08. 14:3x nachgetragen. Bestaetigt in 03ec8463 als Weg (a): die .ts-Luecke ist eine FEHLENDE Zusage, keine gebrochene, und der Fehlschlag ist fail-safe (byte-identische Verweigerung, keine Verstuemmelung). W-06 traegt die .ts-Bilanz nach. BIS W-06 GRUEN IST gilt fuer .ts-Dateien die B6-Uebergangsregel: ganze Datei schreiben oder anhaengen, kein Zeilen-Splice.
  gegengelesen_von: evaluator
  gegengelesen_am: "2026-08-01 23:0x"
  befund: >
    TRAEGT, mit zwei Auflagen. (1) Kante 5 (md5-Drift zwischen Lesen und
    Schreiben) ist benannt, aber KEIN Kriterium verriegelt sie - bitte als
    K-08 aufnehmen: md5 beim Lesen == md5 vor dem Schreiben, sonst Abbruch
    ohne Schreiben. Eine benannte Kante ohne Zusage ist Prosa (S4c-Klasse).
    (2) K-03-Grenzfall fehlt: bei von=1 gibt es keine Zeile 0, bei bis=EOF
    keine bis+1 - das Werkzeug muss die Raender ausdruecklich als
    'DATEIANFANG'/'DATEIENDE' zeigen statt zu schweigen; genau die
    off-by-one-Klasse, gegen die es gebaut wird. Klein, ohne Auflage:
    .tsx-Klammerbilanz bricht an Template-Literalen mit Backticks - als
    bekannte Grenze in den Werkzeugkopf. Bestand nachgemessen: 69 pass,
    14 Werkzeuge, 2 Zusagen-Dateien, K-01/K-05 laufen unter der Allowlist.
```

## Warum — vier Fehler derselben Klasse an einem Abend

**Am 01.08. zwischen 19:30 und 22:15 ist mir viermal derselbe Griff misslungen:** `head -N` +
Heredoc + `tail -n +M`, jedes Mal an der Grenzzeile.

```text
19:5x  Test-Datei: Import-Zeile doppelt         -> "Identifier 'bericht' has already been declared"
20:0x  auftrag-pruefen.mjs: Klammer verwaist    -> "SyntaxError: Unexpected token ']'"
22:0x  Z-03+Z-04: `id:` ueberschrieben          -> S-01 meldete 0 aktive Blaetter
22:1x  W-01: `schritte:` doppelt                -> yaml-Kopf unlesbar, PB-019 sperrte
```

**Vier Vorsätze haben nicht geholfen** — ich habe mir nach dem zweiten Mal in `docs/STAND.md`
selbst die Regel geschrieben *„vor jedem Splice die Grenzzeilen anzeigen"* und sie zwei Stunden
später wieder gebrochen. **R9 verlangt an dieser Stelle eine Barriere, und der Beschluss B6 hat
sie beschlossen.**

**Erschwerend:** `git checkout -- <datei>` repariert auf diesem Mount **nicht** — `unlink` ist
verboten. Der Rückweg ist `git show HEAD:<pfad> > /tmp/x && cat /tmp/x > <pfad>`, und den muss man
im Moment des Fehlers erst einmal wissen.

## Bestand — gemessen 01.08. 22:5x

```text
ls -1 scripts/zeile-ersetzen.mjs 2>/dev/null | wc -l   -> 0   (Partner: scripts/zaehle.mjs -> 1)
ls -1 scripts/*.mjs scripts/*.sh | wc -l               -> 14  Werkzeuge insgesamt
ls -1 scripts/__tests__/*.mjs | wc -l                  -> 2   Zusagen-Dateien
```

## Die Entscheidung

**Ein Werkzeug, das den Bereich ZEIGT, bevor es ihn ersetzt — und die Datei danach prüft.**

```text
node scripts/zeile-ersetzen.mjs <datei> <von> <bis> <neuer-inhalt-datei> [--zeigen]

--zeigen     druckt NUR die Zeilen von-1 … von und bis … bis+1 und aendert nichts.
             Das ist der Pflichtschritt, den ich viermal uebersprungen habe.
ohne         ersetzt die Zeilen von…bis, prueft die Datei DANACH, schreibt nur bei Erfolg.
```

**Die Prüfung danach richtet sich nach der Endung** — das ist der Kern, nicht der Ersatz selbst:

```text
.mjs .js .ts .tsx   ->  node --check  (bzw. Klammer-/Anfuehrungsbilanz bei ts/tsx)
.md                 ->  Zahl der ```-Zaeune ist GERADE, und jeder ```yaml-Block laesst sich
                        mit js-yaml laden  <- genau die zwei Fehler von 22:0x und 22:1x
.sh                 ->  bash -n
sonst               ->  Datei ist nicht leer und hat sich wirklich geaendert
```

**Schlägt die Prüfung fehl, bleibt die Datei unverändert** — nicht „geschrieben und gemeldet",
sondern **gar nicht geschrieben**. Das ist der Unterschied zwischen Stufe 3 und Stufe 4.

**Kein `unlink`, kein `mv` über den Mount:** die Datei wird per Truncate-und-Schreiben ersetzt
(`cat neu > ziel`-Semantik), weil `mv` auf diesem Mount an F-10 scheitert.

**Bekannte Grenze, vom Evaluator benannt (23:0x) und in den Werkzeugkopf gehörend:** die
Klammerbilanz für `.tsx` bricht an **Template-Literalen mit Backticks** — ein `` ` `` im String
verschiebt die Zählung. *Deshalb ist die `.tsx`-Prüfung ausdrücklich eine Bilanz und keine
Syntaxprüfung; sie fängt den groben Fall und nicht jeden. Wer sie für vollständig hält, verlässt
sich auf etwas, das sie nicht leistet.*

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/zeile-ersetzen.mjs                 NEU
  scripts/__tests__/zeileErsetzen.test.mjs   NEU

Hier bewusst NICHT:
  scripts/commit-pruefen.sh    prueft VOR dem Commit. W-02 prueft VOR dem Schreiben.
                               Zwei Tore, zwei Zeitpunkte - keine zweite Wahrheit.
  scripts/auftrag-pruefen.mjs  unberuehrt. W-01 ist dort in Arbeit.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/zeile-ersetzen.mjs
    - scripts/__tests__/zeileErsetzen.test.mjs
  population_command: "ls scripts/ | grep '^zeile-ersetzen.mjs$' | wc -l"
  ausschluesse:
    - stelle: "commit-pruefen.sh und auftrag-pruefen.mjs"
      grund: "Anderer Zeitpunkt, anderes Tor. W-01 ist gerade in Arbeit."
      entschieden_von: planner
    - stelle: "Ein allgemeines Such-und-Ersetze"
      grund: "Genau das ist die Klasse, die abgestellt wird. Zeilennummern, kein Muster."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Werkzeug existiert."
    pruefung:
      befehl: "ls scripts/ | grep '^zeile-ersetzen.mjs$' | wc -l"
      erwartet: "1"
    ausgangswert: "0 (gemessen 01.08. 22:5x; Partner `ls -1 scripts/zaehle.mjs | wc -l` -> 1)"

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "Eine kaputte Ersetzung wird NICHT geschrieben - das ist der ganze Zweck."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Gegen die ENTSCHEIDUNGSFUNKTION, nicht gegen den Schreiber (B3): `pruefeInhalt(text, endung)`
          .mjs mit verwaister Klammer          -> false
          .mjs syntaktisch heil                -> true
          .md mit UNGERADER Zahl ```-Zaeune    -> false      <- Fehler 22:1x
          .md mit kaputtem yaml-Block          -> false      <- Fehler 22:1x
          .md heil                             -> true
        Und EINE Zusage ueber den Schreiber, die belegt, dass bei false die Datei
        BYTE-IDENTISCH bleibt (md5 vorher == md5 nachher).
      erwartet: "sechs Zusagen, davon drei ROTE"

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "--zeigen aendert NICHTS."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        md5 vor und nach `--zeigen` identisch, und die Ausgabe enthaelt die Zeilen
        von-1, von, bis und bis+1 - also die Grenzen, an denen ich viermal daneben lag.
        AUFLAGE des Evaluators (23:0x): die RAENDER ausdruecklich zeigen, nicht schweigen.
          von = 1        -> es gibt keine Zeile 0    -> Ausgabe sagt "DATEIANFANG"
          bis = letzte   -> es gibt keine bis+1      -> Ausgabe sagt "DATEIENDE"
        Schweigt das Werkzeug an dieser Stelle, sieht der Rand aus wie eine leere Zeile -
        und genau daraus entsteht die off-by-one-Klasse, gegen die es gebaut wird.
      erwartet: "md5 gleich, vier Grenzzeilen; an den Raendern DATEIANFANG/DATEIENDE statt Leere"

  - id: K-04
    typ: behavioural
    aussage: "Die vier echten Fehler vom 01.08. sind als Zusagen festgenagelt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Je eine Zusage, die den Original-Fehlgriff nachstellt:
          doppelte Import-Zeile · verwaiste Klammer · ueberschriebene `id:`-Zeile ·
          doppeltes `schritte:` im yaml-Kopf.
        Jede muss OHNE das Werkzeug durchgehen und MIT ihm abgelehnt werden.
      erwartet: "vier Zusagen, alle rot ohne Werkzeug, alle gruen mit"

  - id: K-05
    typ: absence
    aussage: "Kein unlink, kein mv - F-10 wird nicht neu erzeugt."
    pruefung:
      befehl: "grep -oE 'unlinkSync|renameSync' -r scripts/ | grep zeile-ersetzen | wc -l"
      erwartet: "0"
    ausgangswert: "0 - die Datei existiert noch nicht; der Befehl laeuft trotzdem (wc -l liefert 0)"
    gegenbeweis: |
      Bewusst ueber das VERZEICHNIS und nicht ueber die Datei: `zaehle.mjs <datei>` wirft ENOENT
      fuer eine Datei, die es noch nicht gibt - ein Kriterium, das VOR dem Bau nicht laufen kann,
      verbietet Regel A. Und bewusst OHNE `2>/dev/null`: die Denylist des Validators wertet jede
      Umleitung als Sperrgrund, auch die nach /dev/null (beim ersten Schnitt dieses Blattes
      dreimal zugeschnappt). Partner: dasselbe Muster ueber scripts/ liefert 2 Treffer, misst also.

  - id: K-06
    typ: behavioural
    aussage: "Die bestehenden Zusagen bleiben gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.test.mjs"
      erwartet: "0 fail. Ausgangswert 69 pass (01.08. 22:4x). Danach mehr, nie weniger."

  - id: K-07
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: Pruefung ganz aus · Pruefung laeuft, Ergebnis ignoriert ·
        bei false trotzdem schreiben · --zeigen schreibt doch · Zaun-Zaehlung ohne yaml-Ladeprobe ·
        Grenzen off-by-one (von statt von-1).
        Wie viele kommen durch?

  - id: K-08
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Baum darf sich zwischen Lesen und Schreiben nicht bewegt haben."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        AUFLAGE des Evaluators (23:0x) - Kante 5 war benannt und unverriegelt.
        *Eine benannte Kante ohne Zusage ist Prosa.*
        Das Werkzeug merkt sich die md5 der Datei BEIM LESEN und vergleicht sie
        unmittelbar VOR dem Schreiben:
          md5(lesen) == md5(vor schreiben)   -> schreiben
          md5 abweichend                     -> KEIN Schreiben, Meldung mit beiden Summen
        Zusagen: gleiche Summe -> geschrieben · Datei zwischendurch veraendert -> NICHT
        geschrieben UND byte-identisch zur Fremdaenderung (die fremde Arbeit ueberlebt).
        Das ist derselbe Gedanke wie S-10 im Validator, eine Ebene tiefer: wer misst,
        waehrend sich der Baum bewegt, misst nichts - wer SCHREIBT, waehrend er sich
        bewegt, zerstoert.
      erwartet: "zwei Zusagen, davon eine ROTE"
```

## Kantenliste

```text
1  Die Pruefung laeuft, ihr Ergebnis wird aber nicht ausgewertet. Sieht aus wie Stufe 4,
   ist Stufe 0. -> K-02 prueft die Entscheidungsfunktion getrennt vom Schreiber.
2  Off-by-one an der Grenze: `von` ist 1-basiert und INKLUSIV, `bis` ebenso. Wer eine Zeile
   zu frueh ansetzt, ueberschreibt genau die `id:`-Zeile - Fehler 22:0x.
3  .tsx laesst sich nicht mit `node --check` pruefen. Dann Klammer- und Anfuehrungsbilanz,
   und das muss im Blatt stehen, nicht im Kopf des Bauenden.
4  Ein Heredoc im neuen Inhalt, der selbst ``` enthaelt, verschiebt die Zaun-Zaehlung.
5  Die Datei bewegt sich zwischen Lesen und Schreiben (andere Instanz). md5 vor dem Schreiben
   gegen md5 beim Lesen - sonst ueberschreibt das Werkzeug fremde Arbeit.
```

## Rückweg und Entdeckung

**Rückweg:** ein neues Werkzeug, das niemand benutzen muss — der Commit lässt sich zurückdrehen,
und bis dahin arbeitet jeder weiter wie bisher.

**Entdeckung:** die Zahl der Splice-Unfälle. Sie stand am 01.08. bei **vier an einem Abend**.
Steht sie danach bei null, wirkt das Werkzeug; steht sie bei null, **weil niemand es benutzt**,
wirkt es nicht — deshalb gehört in den nächsten STAND eine Zeile, wer es benutzt hat.

## Danach

**B2** — der Validator meldet am Ende, was er ausgeführt hat. Eine Zeile, eigenes Blatt.
