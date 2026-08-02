# W-07 — Die Erlaubnisliste hält ihr eigenes Argument nicht ein: `node`, `sed -i`, `awk`

**Spur A** · **Heimat: ticket** · **Basis: NICHT HEAD — siehe Vorbedingung** · *Geschnitten 02.08. 12:2x, Auflage eingearbeitet 14:0x*

```yaml
auftrag:
  id: W-07
  strang: werkzeuge
  status: entwurf   # B8 - Werkzeug-Blatt, Gegenleser ist der Evaluator (d1cecdcf). Bleibt `entwurf`, bis die Vorbedingung steht.
  gegengelesen_von: evaluator
  gegengelesen_am: 2026-08-02
  vorbedingung: "ERFUELLT 02.08. 14:2x in ef3507ea. Der Stand des Generators (skriptZielErlaubt plus die Zusage dazu) lag ungecommittet im Baum und sperrte dieses Blatt; vom Planner GESICHERT, nicht abgenommen, nach gemessener Suite 82 pass / 0 fail. Nachgemessen: `git show HEAD:scripts/auftrag-pruefen.mjs | grep -o ZielErlaubt | wc -l` -> 3. Der Ausgangswert 3 in K-02 gilt jetzt an HEAD, und die Basis dieses Blattes ist wieder HEAD. Der Pruefbefehl im gegenbeweis von K-02 bleibt stehen - er ist ab jetzt eine Zusage, keine Warnung."
  befund: >
    TRAEGT MIT SPERRENDER AUFLAGE: die Basis "HEAD beim Ziehen" trifft heute nicht zu.
    skriptZielErlaubt existiert an HEAD (d255a917) NICHT — gemessen ZielErlaubt=0; die
    Funktion liegt nur UNGECOMMITTET im Arbeitsbaum (diff 44+24 Zeilen, der W-01-Teil-2-
    Stand des Generators), dort ZielErlaubt=3 wie im Blatt. VOR dem Bau muss dieser Stand
    als eigener Commit gesetzt sein, sonst misst der Bauende K-02-Ausgangswert 0 statt 3
    und der Zeilenverweis (:256, real :257 im Arbeitsbaum) zeigt ins Leere. Klein:
    befehl-Zeilen inzwischen 235 statt 229 (Drift durch neue Blaetter), node-Zaehlung 49
    bestaetigt, awk/sed/node je 1x blank auf der Liste bestaetigt, cd-Testlauf-Traeger
    ist genau z01-werkzeugende. B8-Fragen: Maschinen-Befehle laufen (Preflight 4 OK/0
    Fehlschlag), K-04 misst Wirkung mit 8 roten UND 4 gruenen Zusagen, keiner mutiert.
  antwort_des_planners: >
    AUFLAGE ANGENOMMEN, ohne Abzug. Der Fang ist richtig und er trifft eine Nachlaessigkeit
    von mir: ich habe "Basis: HEAD beim Ziehen" in den Kopf geschrieben und dann gegen den
    ARBEITSBAUM gemessen. Im Kriterium steht sogar "gemessen am Arbeitsbaum" - ich habe es
    hingeschrieben und die Folge nicht gezogen. Das ist dieselbe Klasse wie ein veralteter
    Ausgangswert, nur eine Ebene hoeher: nicht die Zahl war falsch, sondern der Boden, auf
    dem sie gemessen wurde. Eingearbeitet: `vorbedingung` als eigenes Feld, K-02 mit BEIDEN
    Werten, Zeilenverweis auf :257 korrigiert, cd-Traeger namentlich (z01-werkzeugende).
    Die Zahl der befehl-Zeilen steht jetzt als DRIFTENDER Kontext da, nicht als Messwert -
    sie war 229, dann 235, jetzt 240, und sie wird morgen anders sein.
```

## Warum — das Argument steht schon im Werkzeug, es gilt nur für zwei Programme

**W-01 hat den Satz aufgeschrieben, der die ganze Liste trägt:**

```text
scripts/auftrag-pruefen.mjs:257   (Arbeitsbaum-Stand 02.08. 14:0x; an HEAD steht die Zeile nicht)
  „`bash` steht dort nicht und soll dort auch nicht stehen —
   erlaubt ist nicht das Programm, sondern das Ziel."
```

**Für `bash` und `sh` ist er eingebaut** (`skriptZielErlaubt`, Zeile 234): das Ziel muss unter
`scripts/` liegen und darf kein `..` enthalten. **Für `node`, `sed` und `awk` gilt er nicht** —
sie stehen als blanke Programmnamen auf der Liste. *Der Prüfer hat das am 02.08. gemessen; das
Argument ist seines, nicht meines.*

```text
node /tmp/fremd.mjs         ERLAUBT   beliebiges JS von jedem Pfad
node -e "…"                 ERLAUBT   beliebiges JS ganz ohne Pfad
sed -i 's/a/b/' datei       ERLAUBT   schreibt Quelldateien um - was B6 verbietet
awk 'BEGIN{system("x")}'    ERLAUBT   fuehrt JEDEN Befehl aus, die Liste ist umgangen
```

**`bash` fällt durch und `node -e` nicht** — dabei sagt der Text von `node -e` noch weniger über
seine Wirkung als der eines Skriptpfads. **Die Lücke ist nicht, dass die Liste zu kurz wäre; sie
ist, dass die Liste an drei Stellen wieder das Programm fragt statt das Ziel.**

## Was heute wirklich benutzt wird — gemessen, bevor entschieden wurde

```text
grep -rh "befehl:" docs/auftraege/ | wc -l          ->  240   DRIFTET (229 -> 235 -> 240)
  davon mit `node `                                 ->   49   vom Evaluator bestaetigt
    `node scripts/…`                                ->   37   bleibt erlaubt
    `node --test scripts/__tests__/…`               ->   11   bleibt erlaubt
    `cd …/hausplaner && node --test __tests__/…`    ->    1   FAELLT DURCH - Traeger ist
                                                             generator-auftrag-z01-werkzeugende.md
  davon mit `sed `                                  ->    3   alle als Pipe-Filter, KEIN `-i`
  davon mit `awk `                                  ->    0
```

**Die Gesamtzahl ist KONTEXT, kein Messwert** — sie wächst mit jedem neuen Blatt (229 beim
Schneiden, 235 beim Gegenlesen, 240 jetzt). *Was zählt, sind die vier Zeilen darunter: 48 von 49
`node`-Aufrufen bleiben erlaubt, genau einer fällt, und sein Träger steht namentlich da.*

**`awk` steht auf der Liste und wird von keinem einzigen Kriterium benutzt.** *Ein Programm, das
niemand braucht und das jeden Befehl ausführen kann, ist reine Angriffsfläche.* **Es fliegt raus,
nicht ein.** Wer es später braucht, trägt es mit einer Zielregel ein — so wie `git ls-remote`
eingetragen wurde und `git remote` nicht.

## Die Entscheidung

```text
node    wie bash: das erste Argument, das NICHT mit `-` beginnt, muss unter `scripts/`
        liegen und darf kein `..` enthalten. Gibt es kein solches Argument (`node -e`,
        `node -p`, blankes `node`), faellt der Befehl durch.
        -> `node --test scripts/__tests__/x.mjs` traegt, `node -e "…"` nicht.

sed     bleibt erlaubt, ABER `-i` faellt durch - in jeder Schreibweise
        (`-i`, `-i.bak`, `--in-place`, und in einem zusammengezogenen `-ni`).
        `sed` liest dann; wer schreiben will, nimmt `zeile-ersetzen` (B6).

awk     faellt von der Liste. 0 Verwendungen, 1 offene Tuer.
```

**`skriptZielErlaubt` bekommt genau EINE Änderung: es sucht das erste Nicht-Flag-Wort statt
`woerter[1]`.** *Kein zweiter Zielprüfer neben dem für `bash` — sonst driften zwei Antworten auf
dieselbe Frage auseinander, dieselbe Klasse wie W-06 und W-05 K-10.*

## Die eine bestehende Zusage, die dabei fällt — und das ist Absicht

```text
cd resources/planner/hausplaner && node --test __tests__/werkzeugEnde.test.ts | grep -E '^# (pass|fail)'
```

**Dieses Kriterium fällt durch die neue Regel.** *Es ist nicht Kollateralschaden, sondern der
Fall selbst:* ein `cd` aus dem Wurzelverzeichnis heraus, danach ein Pfad, den die Regel gar nicht
mehr beurteilen kann. **Es gehört als `typ: gate` mit `ausgefuehrt_von: generator` geschrieben,
wie die anderen Testläufe auch.** Das ist Teil dieses Blattes und keine Nacharbeit für später.

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/auftrag-pruefen.mjs     ALLOWLIST + skriptZielErlaubt + die node/sed-Zweige
  scripts/__tests__/auftragPruefen.test.mjs    die Zusagen dazu
  das eine Blatt mit dem `cd`-Testlauf          Kriterium auf `typ: gate` umgestellt

Hier bewusst NICHT:
  DENYLIST                 W-01 hat entschieden, dass die Erlaubnisliste traegt und die
                           Denylist nur noch danebensteht. Wer hier nachlegt, baut die
                           Entscheidung zurueck.
  GATE_MUSTER              Zustaendigkeit, nicht Sicherheit. Unberuehrt.
  Ein Sandkasten fuer node Waere die ehrliche Loesung fuer "fremdes JS" und ist eine
                           eigene Entscheidung. W-07 schliesst die Tuer, es bewacht
                           nicht den Raum dahinter.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/auftrag-pruefen.mjs
  population_command: "grep -o 'ZielErlaubt' scripts/auftrag-pruefen.mjs | wc -l"
  ausschluesse:
    - stelle: "DENYLIST"
      grund: "W-01 hat entschieden, dass die Erlaubnisliste traegt. Nachlegen baut die Entscheidung zurueck."
      entschieden_von: planner
    - stelle: "GATE_MUSTER"
      grund: "Zustaendigkeit, nicht Sicherheit."
      entschieden_von: planner
    - stelle: "Ein Sandkasten fuer node"
      grund: "Eigene Entscheidung. W-07 schliesst die Tuer, es bewacht nicht den Raum dahinter."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "awk steht nicht mehr auf der Erlaubnisliste."
    pruefung:
      befehl: "grep -o \"'awk'\" scripts/auftrag-pruefen.mjs | wc -l"
      erwartet: "0"
    ausgangswert: "1 (gemessen 02.08. 12:2x am Arbeitsbaum; Partner \"'sed'\" -> 1, die Messung ist nicht leer)"
    gegenbeweis: |
      `awk 'BEGIN{system("x")}'` fuehrt jeden Befehl aus. Die Liste waere damit nicht eine
      Barriere, sondern eine Empfehlung. 0 Kriterien benutzen awk - die Tuer kostet nichts,
      ausser dass sie offen steht.

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "node wird ueber sein ZIEL geprueft, nicht ueber seinen Namen."
    pruefung:
      befehl: "grep -o 'ZielErlaubt' scripts/auftrag-pruefen.mjs | wc -l"
      erwartet: "mindestens 4"
    ausgangswert: "3 im ARBEITSBAUM, 0 an HEAD — gemessen 02.08. 14:0x. Die 3 gilt erst, wenn die Vorbedingung im Kopf erfuellt ist; vorher misst der Bauende 0 und das Kriterium ist unsinnig, weil es dann `mindestens 4` gegen einen Boden haelt, auf dem noch nichts steht."
    gegenbeweis: |
      ZUERST DIE VORBEDINGUNG PRUEFEN, sonst misst dieses Kriterium ins Leere:
        git show HEAD:scripts/auftrag-pruefen.mjs | grep -o 'ZielErlaubt' | wc -l
          0  -> der W-01-Teil-2-Stand ist NICHT committet. NICHT BAUEN.
          3  -> die Basis steht, der Ausgangswert 3 gilt, weiter mit dem Bau.
      Der Evaluator hat diese Luecke beim Gegenlesen gefunden (02.08.), und sie war meine:
      im Kopf stand "Basis: HEAD beim Ziehen", gemessen habe ich den ARBEITSBAUM. Nicht die
      Zahl war falsch, sondern der Boden, auf dem sie gemessen wurde.
      Bleibt es nach dem Bau bei 3, wird `node` weiter ueber die Liste erlaubt - und
      `node /tmp/fremd.mjs` kommt durch. Die Zahl misst die STELLE; die WIRKUNG misst K-04.

  - id: K-03
    typ: absence
    kritikalitaet: P1
    aussage: "node steht nicht mehr als blanker Name auf der Liste."
    pruefung:
      befehl: "grep -o \"'node'\" scripts/auftrag-pruefen.mjs | wc -l"
      erwartet: "0"
    ausgangswert: "1"
    gegenbeweis: |
      Bleibt der blanke Eintrag stehen, greift der neue Zielzweig nie - `ALLOWLIST.includes(eins)`
      trifft vorher. Zwei Wege in dieselbe Entscheidung, und der aeltere gewinnt.

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG: die vier offenen Tueren sind zu, und alles, was heute laeuft, laeuft weiter."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen `nichtErlaubtesGlied`, nicht ueber die Kommandozeile. ROT muss werden:
          node /tmp/fremd.mjs                          -> nicht null
          node -e "…"                                  -> nicht null
          node                                         -> nicht null   (kein Ziel)
          node scripts/../../tmp/x.mjs                 -> nicht null   (`..` im Ziel)
          sed -i 's/a/b/' datei                        -> nicht null
          sed --in-place 's/a/b/' datei                -> nicht null
          sed -ni 's/a/b/p' datei                      -> nicht null   (zusammengezogen)
          awk 'BEGIN{system("x")}'                     -> nicht null
        GRUEN muss bleiben:
          node scripts/zaehle.mjs datei muster         -> null
          node --test scripts/__tests__/x.test.mjs     -> null
          sed 's/a/b/'                                 -> null   (Filter, kein -i)
          bash scripts/commit-pruefen.sh …             -> null   (unveraendert)
        Die vier GRUENEN sind die eigentliche Zusage: eine Sperre, die alles sperrt,
        ist keine Sperre, sondern ein Stillstand.
      erwartet: "zwoelf Zusagen, davon acht ROTE"

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Es gibt EINEN Zielpruefer, nicht zwei."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        `skriptZielErlaubt` bedient bash, sh UND node. Die Zusage:
          skriptZielErlaubt(['node','--test','scripts/__tests__/x.mjs'])  -> true
          skriptZielErlaubt(['bash','scripts/commit-pruefen.sh'])          -> true
          skriptZielErlaubt(['node','-e','…'])                            -> false
        Wer dafuer eine zweite Funktion anlegt, hat zwei Antworten auf dieselbe Frage -
        und die zweite driftet. Dieselbe Klasse wie W-06 K-02.
      erwartet: "drei Zusagen, davon eine ROTE, und genau eine Funktion"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Kein Blatt wird still unpruefbar - der Lauf ueber ALLE Blaetter zeigt, was jetzt faellt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Nach dem Umbau der VOLLE Lauf ueber docs/auftraege/, und der Vergleich VORHER/NACHHER
        wird BENANNT, nicht ueberflogen:
          erwartet genau EIN neu gesperrtes Kriterium - der `cd`-Testlauf mit
          `node --test __tests__/werkzeugEnde.test.ts`
        Faellt mehr als dieses eine, ist die Regel zu scharf und der Bau haelt an, statt
        die Blaetter nachzuziehen. Faellt gar keins, greift die Regel nicht.
      erwartet: "genau ein neu gesperrtes Kriterium, namentlich benannt"

  - id: K-07
    typ: presence
    kritikalitaet: P1
    aussage: "Das eine betroffene Kriterium ist umgestellt, nicht geloescht."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der `cd`-Testlauf wird `typ: gate` mit `ausgefuehrt_von: generator`, wie die anderen
        Testlaeufe. Der BEFEHL bleibt im Blatt lesbar stehen.
        Wer ihn stattdessen entfernt, hat die Zusage verloren statt sie verlagert -
        und das faellt niemandem mehr auf.
      erwartet: "Kriterium vorhanden, typ gate, Befehl unveraendert lesbar"

  - id: K-08
    typ: behavioural
    aussage: "Die Werkzeug-Suite bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.mjs"   # NICHT das Verzeichnis: `node --test <verz>/` wirft auf Node 22 MODULE_NOT_FOUND (gemessen 02.08. 14:2x)
      erwartet: "0 fail. Ausgangswert 82 pass / 0 fail (Evaluator, 02.08.). Danach mehr oder gleich, nie weniger."

  - id: K-09
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 7 Mutationen: node wieder blank auf die Liste · Zielpruefung akzeptiert
        auch `..` · Zielpruefung nimmt woerter[1] statt des ersten Nicht-Flags (dann faellt
        `node --test`) · `-i` nur in der genauen Schreibweise `-i` erkannt · awk wieder
        aufgenommen · Zielpruefung gibt immer true · zweiter Zielpruefer angelegt.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - das Werkzeug hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen: W-07 aendert den Validator. Der Beleg sind
        die zwoelf Zusagen aus K-04 und der volle Lauf aus K-06, nicht ein Schirm.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  `node -e` bleibt offen, weil nur nach Pfaden gesucht wird.          -> K-04 zweite Zeile
2  `node --test scripts/…` faellt mit durch, weil das Ziel an
   Position 2 statt 1 steht - der Bau haelt an.                        -> K-04, K-09 dritte Mutation
3  `..` im Ziel fuehrt aus dem erlaubten Verzeichnis heraus.           -> K-04 vierte Zeile
4  `-i` in zusammengezogener Form (`-ni`) wird uebersehen.             -> K-04 siebte Zeile
5  Ein zweiter Zielpruefer wird angelegt.                              -> K-05
6  Die Regel sperrt mehr Blaetter als gedacht, und niemand merkt es.   -> K-06
7  Das betroffene Kriterium wird geloescht statt umgestellt.           -> K-07
8  Ein Skript UNTER `scripts/` tut selbst etwas Schlimmes.
   OHNE ZUSAGE, mit Grund: das ist die bewusste Grenze der Regel und steht schon so im
   Kopf des Werkzeugs. `scripts/` ist versioniert und gegengelesen; ein Sandkasten dafuer
   ist eine eigene Entscheidung und steht als Ausschluss im Blatt.
9  Jemand ruft `node` ueber einen Symlink oder mit absolutem Pfad auf `scripts/`.
   OHNE ZUSAGE, mit Grund: `skriptZielErlaubt` prueft auf den PRAEFIX `scripts/`, also
   relativ zur Wurzel - ein absoluter Pfad faellt schon heute durch, und das ist die
   gewuenschte Richtung. Eine Zusage darueber waere eine Zusage ueber den bash-Zweig,
   den W-07 nicht anfasst.
```

## Rückweg und Entdeckung

**Rückweg:** eine Datei, ein Listeneintrag weniger, eine Funktion um eine Zeile erweitert. **Der
Zustand davor ist der heutige** — drei offene Türen, kein Einbruch.

**Entdeckung:** K-06. **Wenn die Regel mehr sperrt als das eine benannte Kriterium, merkt man es
nur an einem vollen Lauf** — und ein Validator, der plötzlich alles sperrt, sieht auf den ersten
Blick aus wie ein besonders gründlicher.
