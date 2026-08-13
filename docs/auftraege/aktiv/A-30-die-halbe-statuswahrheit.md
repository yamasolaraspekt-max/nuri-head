# A-30 — Eine Tafelzeile ohne Datensatz ist unsichtbar, und die Ball-Drift-Barriere schweigt genau dort

```yaml
auftrag: "A-30"
werkzeug: "—  (Tor-Barriere, sechste)"
art: "BAU — eine neue Barriere in scripts/commit-pruefen.sh und ein Sichtbarkeitsloch in
      scripts/a26-ball-drift.sh. Kein Produktivcode der App."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: d8fd395d
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-30 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-29 sind vergeben. Frei."
keine_dublette: "Gemessen, BEVOR geschnitten: das Tor fuehrt fuenf Barrieren, und A-26
                 (scripts/a26-ball-drift.sh, 5337 Byte) vergleicht Tafelzeile und Datensatz je
                 Auftrag. Sie deckt DIESEN Fall NICHT ab — der Grund steht in Abschnitt 2 und ist
                 eine einzelne Zeile Code. A-27 prueft bau_sha, F-14 die Schreibwirkung, B5 die
                 Belegzeile, B6 die Erhebung: keine beruehrt die EXISTENZ des Datensatzes."
anlass: "Der plan-pruefer hat es an MIR gemessen und gemeldet (d5296fe7): meine Commits 86f94d98
         (A-29) und ca99466b (W-16/1) haben je nur die Tafelzeile angelegt, keinen Datensatz-Block.
         Seine Ballortung liest grep auf 'ballbesitz: plan-pruefer' IN docs/STATUS.md — ohne Block
         kein Treffer. ZWEI AUFTRAEGE LAGEN GLEICHZEITIG UNSICHTBAR IN SEINER BAHN. Er hat den
         W-16/1-Block selbst angelegt, damit die Statuswahrheit einen Ort hat. Der Befund trifft zu."
grundlage: "scripts/a26-ball-drift.sh:55-56 · scripts/commit-pruefen.sh (728 Z.) ·
            docs/STATUS.md · A-20-2 (Blatt, Tafelzeile und Datensatz in EINEM Commit) ·
            §16 (eine Statuswahrheit) · echte Staende: 86f94d98 und ca99466b rot,
            875d1da5 und c5e52994 und c82c7f55 und b778152b gruen"
```

## 1 — Der tragende Punkt: A-20-2 hat keine Barriere, und der Ausfall ist belegt

```text
A-20-2 verlangt woertlich (1-AUFTRAG.md:437):
  "Blatt, Tafelzeile und Datensatz in EINEM Commit   -> kein Fenster"

AM COMMIT GEMESSEN, meine zwei letzten Schnitte:
  86f94d98  A-29 geschnitten    docs/STATUS.md  1 insertion
            neue Tafelzeilen 1  ·  neue auftrag-Bloecke 0
  ca99466b  W-16/1 geschnitten  docs/STATUS.md  1 insertion
            neue Tafelzeilen 1  ·  neue auftrag-Bloecke 0

UND ES GING GUT, SOLANGE ES GUT GING — dieselbe Messung an vier fruehreren:
  875d1da5  W-21/2   Tafelzeile 1 · Block 1
  c5e52994  A-27     Tafelzeile 1 · Block 1
  c82c7f55  A-28     Tafelzeile 1 · Block 1
  b778152b  W-18/1   Tafelzeile 1 · Block 1

-> Der Handgriff war richtig und ist bei den letzten zwei ABGEBROCHEN. Genau
   dafuer sind Barrieren da: eine Regel, die nur solange haelt, wie ich sie
   erinnere, ist keine Regel, sondern Glueck.
```

> **Die Folge war nicht theoretisch.** *Die Ballortung des Plan-Prüfers liest `ballbesitz` **im
> Datensatz**. Ohne Block gibt es keinen Treffer — **zwei Aufträge lagen gleichzeitig unsichtbar in
> seiner Bahn**, während Blatt und Tafelzeile ihn als Ballhalter nannten. Er hat es selbst gefunden und
> den fehlenden Block angelegt. **Ohne seine Gegenmessung hätten die zwei liegengeblieben, und niemand
> hätte gewusst, warum.***

## 2 — Warum die vorhandene Barriere hier nicht greift: eine Zeile, ein `continue`

```text
scripts/a26-ball-drift.sh sammelt IDs aus BEIDEN Orten (:32) und liest dann:

  :55  START="$(grep -n -m1 -E "^auftrag: \"?${ID}\"?[[:space:]]*$" "$DATEI" | ...)"
  :56  [ -z "$START" ] && continue

  -> FEHLT der Datensatz, wird die ID STILL uebersprungen.

Das ist nicht schlecht gebaut — es ist die richtige Vorsicht am falschen Ort.
Der Kopf der Datei (:4-8) nennt A-20-2 sogar ausdruecklich als Anlass und sagt,
'am Diff fehlte die Tafelzeile'. Die Barriere kennt die Regel und prueft ihre
EINE Haelfte; die andere faellt durch continue.
```

**Und das `continue` kostet mehr als meinen Fall — es kostet Deckung, die niemand vermisst:**

```text
HEUTE GEMESSEN in docs/STATUS.md, beide Mengen ausgezaehlt:
  Tafelzeilen mit ID       62
  Datensaetze mit auftrag  61
  TAFEL OHNE DATENSATZ     12   A-06 W-01 W-02 W-04 W-05 W-08 W-09
                                W-11 W-13 W-15 W-21 W-22
  DATENSATZ OHNE TAFEL     11   W-01/1 W-02/1 W-04/1 W-05/1 W-08/1
                                W-09/1 W-11/1 W-13/1 W-15/1 W-21/1 W-22/1

ELF DAVON SIND DASSELBE PAAR MIT ZWEI SCHREIBWEISEN. Geoeffnet und geprueft:
  TAFEL     :19  | **W-01** Raster und Fang | BETRIEBSBESTAETIGT | – | ...
  DATENSATZ      auftrag: "W-01/1"
                 titel: "Die sieben Blaetter von W-01 ... ableiten"
                 datei: docs/auftraege/aktiv/W-01-fang-beschreiben.md
                 zustand: BETRIEBSBESTAETIGT
  -> EIN Vorgang, zwei Kennungen: die Tafel ohne Suffix, der Datensatz mit /1.

DIE FOLGE: fuer diese ELF vergleicht A-26 NICHTS. Die Tafel-ID W-01 findet
keinen Block, die Datensatz-ID W-01/1 findet keine Tafelzeile — zweimal
continue. Bei 11 von 62 Tafelzeilen laeuft die Ball-Drift-Pruefung leer, und
weil continue still ist, sieht das niemand.
```

> ***Das ist der eigentliche Fund, und er ist größer als mein Fehler.*** *Eine Barriere, die schweigt,
> wo sie nicht prüfen kann, meldet dasselbe wie eine, die prüft und nichts findet: **nichts.** Die
> beiden sind von außen nicht unterscheidbar — genau die Klasse, gegen die B5 („Zählwort braucht
> Belegzeile") und E1 („am COMMIT messen") gebaut wurden.*

## 3 — Warum die naive Barriere FALSCH wäre, und was stattdessen gilt

```text
DIE NAIVE FASSUNG: "jede Tafelzeile braucht einen Datensatz" -> ZWOELF
Fehlalarme, sofort und dauerhaft, auf lauter legitimen Zeilen. Elf sind
abgeschlossene Vorgaenge mit Schreibweisen-Divergenz, einer (A-06) ist eine
alte ERLEDIGT-Zeile ohne Ball.

Und A-26-3 sagt, was dann passiert — der Satz steht in a26-ball-drift.sh selbst
(:73-76): "Ein Fehlalarm auf einer legitimen Zeile ist genau der Weg, auf dem
eine Barriere weggeklickt wird."

WAS STATTDESSEN GILT: die Barriere prueft nur, was IM COMMIT NEU DAZUKOMMT.
Dasselbe Prinzip wie F-14 (Schreibwirkung im Commit) und E1 (am COMMIT messen).
  -> Altbestand kommt nie vor: kein Fehlalarm auf den 12.
  -> Mein Fall wird gefangen: 86f94d98 und ca99466b legen je eine NEUE
     Tafelzeile an und keinen Block.
```

## 4 — Scope

```text
A-30 IST  (1) eine SECHSTE Tor-Barriere: wer in docs/STATUS.md eine NEUE
              Tafelzeile mit Auftragskennung anlegt, legt im SELBEN Commit den
              Datensatz-Block mit derselben Kennung an — und umgekehrt.
              Nur NEUE Kennungen im Commit, nicht der Bestand.
              AUFTRAGSKENNUNG IST FESTGELEGT (nachgetragen 13.08. nach 9e9e736e):
              die Praefixe A- und W-, je gefolgt von Ziffern und optional einem
              /Suffix. Das Muster steht in A-30-8, weil eine Zahl ohne Muster
              nachweislich zwei Zahlen ist.
          (2) das Sichtbarkeitsloch in scripts/a26-ball-drift.sh:56 schliessen:
              eine ID ohne Gegenstueck wird GEMELDET, und zwar als eigene
              Klasse ('nicht geprueft'), NICHT als Drift-Befund. Der Lauf darf
              daran nicht scheitern — sonst ist es die naive Fassung durch die
              Hintertuer.

A-30 IST NICHT
          eine Pruefung der Praefixe P- und M-. AUSDRUECKLICHES NICHT-ZIEL, und
          zwar aus zwei verschiedenen Gruenden — gemessen 13.08.:
            P-02 (docs/STATUS.md:31) 'parallele Instanzen' traegt den Zustand
                 VORLAGE. Eine VORLAGE ist kein Bauauftrag; §3 definiert das
                 Wort seit A-21. Sie hat legitim keinen Datensatz-Block, und
                 eine Barriere, die sie meldet, ist genau der Fehlalarm auf
                 einer legitimen Zeile, vor dem a26-ball-drift.sh:73 warnt.
            M-02 (docs/STATUS.md:5302) ist UEBERHAUPT KEINE Auftragszeile,
                 sondern eine Zeile in einer BEFUNDTABELLE: '| **M-02-Kopienzahl**
                 | drei Kopien gemessen … | VORGEHEN.md:43 sagt fuenfmal | …'.
                 Ein Muster, das M- einschliesst, liest Befundtabellen als
                 Auftraege. DAS ist der staerkere Grund gegen ein breites
                 Praefix-Muster: es greift nicht nur zu weit, es greift in eine
                 andere Art Tabelle.
          Die Zahl der Praefixe ist gemessen: W- 36x, A- 30x, P- 1x, M- 1x.
          Kaeme spaeter ein P- oder M-Auftrag MIT Bau-Zustand in die Kette, ist
          das eine Erweiterung dieser Barriere und kein stiller Einschluss.
          die BERICHTIGUNG der elf Schreibweisen-Divergenzen. Das sind elf
          Zeilen im Altbestand, alle BETRIEBSBESTAETIGT; das ist ein eigener
          Vorgang mit eigener Groesse und faellt unter die Rueckfall-Regeln
          (Original erhalten, kein Loeschen ohne Freigabe). HIER wird die Zahl
          nur SICHTBAR gemacht — behoben wird sie getrennt.
          eine Aenderung an A-20 oder §16. Die Regeln stehen; ihnen fehlt die
          Barriere. docs/ARBEITSREGELN.md aendert nach §1 nur Yama.
          eine Aenderung an den fuenf vorhandenen Barrieren. Keine.
          Produktivcode der App. resources/** und app/** bleiben unberuehrt.
```

## 5 — Abnahmekriterien

```text
A-30-1 (P1, TRAGEND) Die Barriere ist AN DEN ECHTEN STAENDEN geprobt, in BEIDEN
       Richtungen, und die Rohausgabe steht im Bericht:
         ROT werden muss   86f94d98  (A-29:    1 neue Tafelzeile, 0 Bloecke)
                           ca99466b  (W-16/1:  1 neue Tafelzeile, 0 Bloecke)
         GRUEN bleiben muss 875d1da5 (W-21/2:  1 Tafelzeile, 1 Block)
                           c5e52994 (A-27)  ·  c82c7f55 (A-28)
                           b778152b (W-18/1)
       KEINE erfundenen Beispiele. A-26-1 verlangt echte Staende, und der Grund
       steht in a26-ball-drift.sh selbst: der Umlaut-Fehlalarm ist an einem
       echten Stand aufgefallen und waere an einem erfundenen nie sichtbar
       geworden.
A-30-2 (P1) KEIN FEHLALARM AUF DEM BESTAND. Gegenprobe, die rot werden kann: ein
       Lauf gegen den aktuellen HEAD meldet KEINEN Befund der Klasse (1) — weil
       keine der Bestandszeilen im Commit NEU ist.
       DIE ZAHL WIRD MIT IHREM MUSTER GENANNT, sonst sind es zwei Zahlen:
         unter dem Muster A-/W-      12 Tafelzeilen ohne Datensatz
         unter allen Grossbuchstaben 13 — die dreizehnte ist P-02, VORLAGE,
                                     und nach dem Nicht-Ziel oben legitim
       Die Liste ist im Bericht namentlich zu nennen, damit belegt ist, dass sie
       geprueft und bewusst nicht gemeldet wurde. AM BAU-STAND erheben (E1):
       meine Zahlen sind vom 13.08. am Stand d8fd395d.
       WARUM DIESE KLARSTELLUNG NACHGETRAGEN IST: der plan-pruefer hat in
       9e9e736e gemessen, dass meine erste Fassung 'die zwoelf' als P1-Gegenprobe
       nennt, ohne das Muster festzulegen — mit der naheliegenden breiten Fassung
       zaehlt der Bauende 13 und das Kriterium ist verfehlt. Nach §5 ist jede
       Anforderung Kriterium ODER ausdrueckliches Nicht-Ziel; das Muster war
       beides nicht. Der Befund trifft, und er ist meine eigene Lehre gegen mich:
       Pruefung 7 verlangt jede Zahl MIT TRAEGER, und ich habe die Datei genannt
       und das Muster weggelassen.
A-30-3 (P1) Die Meldung aus (2) unterscheidet ZWEI KLASSEN und wirft sie nicht
       zusammen: 'Drift' (beide Orte da, Werte verschieden) und 'nicht geprueft'
       (ein Ort fehlt). Wer beides gleich meldet, macht aus einer Deckungsluecke
       einen Befund und aus einem Befund Rauschen.
A-30-4 Die Zahl der ungeprueften Paare steht im Lauf-Ergebnis, AM BAU-STAND
       erhoben und nicht aus diesem Blatt uebernommen (E1). Meine Messung vom
       13.08. sagt 12 und 11 — wenn der Bau etwas anderes zaehlt, ist DAS der
       Befund, und die Zahl gehoert mit ihrem Muster in den Bericht (Pruefung 7:
       jede Zahl mit Traeger).
A-30-5 Die Barriere haelt sich an die Tor-Ordnung: sie prueft LESEND, raeumt
       nichts, und sie laeuft nicht in den Abbruchpfaden vor der eigentlichen
       Pruefung. Die A-07-Lehre gilt: ein Lauf, der bei FEHLER oder ENV_BLOCKED
       aussteigt, erreicht ein 'am Ende' nie.
A-30-6 Die elf Schreibweisen-Divergenzen sind NICHT beruehrt. Gegenprobe: der
       Bau-Commit aendert in docs/STATUS.md NICHTS — er fasst nur
       scripts/** an. Wenn doch, ist das Beifang.
A-30-7 Kein Produktivcode. Gegenprobe: resources/ und app/ kommen im Bau-Commit
       null Mal vor.
A-30-8 (P1) DAS KENNUNGSMUSTER STEHT IM CODE, nicht nur im Blatt: die Barriere
       liest Tafelzeilen und Datensaetze mit den Praefixen A- und W-, je Ziffern
       und optional ein /Suffix. Ein Kommentar an der Stelle nennt den Grund fuer
       den Ausschluss von P- und M- mit Fundstelle (docs/STATUS.md:31 VORLAGE,
       :5302 Befundtabelle) — sonst erweitert die naechste Rolle das Muster in
       gutem Glauben und baut den Fehlalarm ein, den A-30-2 ausschliesst.
       GEGENPROBE, die rot werden kann: ein Lauf mit kuenstlich breitem Muster
       ueber den Bestand meldet P-02, der Lauf mit dem festgelegten Muster nicht.
       Beide Rohausgaben in den Bericht — der Unterschied IST der Nachweis.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7),
**Probe an echten Ständen** (A-26-1).

```yaml
warum_P1: "Der Ausfall ist nicht hypothetisch — er hat heute zwei Auftraege unsichtbar gemacht, und
        gefunden hat es nicht das Tor, sondern eine fremde Rolle beim Nachmessen. Das ist genau die
        Lage, die §16 (eine Statuswahrheit) verhindern soll: die Tafel sagt, der Ball liege beim
        plan-pruefer, und sein Suchbefehl findet nichts."
was_die_pflichtpruefungen_hier_verhindert_haben: "ZWEI Dinge. ERSTENS Pruefung 1: ich haette die
        sechste Barriere schneiden koennen, ohne zu pruefen, was A-26 schon leistet — dann waere eine
        Dublette entstanden, und der eigentliche Fund (das stille continue) waere unentdeckt
        geblieben. ZWEITENS Pruefung 6, die Reichweite: mein erster Entwurf lautete 'jede Tafelzeile
        braucht einen Datensatz'. Gemessen haette das ZWOELF Fehlalarme auf legitimen Zeilen erzeugt
        — und a26-ball-drift.sh sagt im eigenen Kommentar, dass genau so eine Barriere weggeklickt
        wird. Die Messung hat die Barriere von 'jede Zeile' auf 'jede NEUE Zeile im Commit' verengt,
        und damit ueberhaupt erst brauchbar gemacht."
was_ich_gemessen_habe_und_was_nicht: "SELBST GEMESSEN: die zwei roten und vier gruenen Staende am
        Diff, die 62/61/12/11 in docs/STATUS.md an zwei Mengen, das Paar W-01 gegen W-01/1 durch
        Oeffnen beider Stellen, das continue in a26-ball-drift.sh:56 im Rumpf, und dass die anderen
        vier Barrieren die Existenz nicht pruefen. NICHT GEMESSEN: ob die elf Divergenzen einen
        gemeinsamen Ursprung haben (alle tragen /1 und alle sind BETRIEBSBESTAETIGT — das SIEHT nach
        einer Runde aus, aber ich habe die Historie nicht aufgerollt). Das gehoert zu dem getrennten
        Vorgang, nicht hierher."
A_30_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

## Votum des Evaluators (§11) — ABGENOMMEN

```yaml
votum: ABGENOMMEN
geprueft_am: "13.08.2026, evaluator"
bau_commit: "0aceee01 (21:55) — GESUCHT, nicht aus dem Feld genommen. Es ist der einzige: fce4afff
  (21:57) traegt ausschliesslich docs/STATUS.md, also die Zustandsmeldung, keinen Code."
elter: "c6a5a707"
pruefstand: "worktree --detach auf 0aceee01, node_modules UND vendor per cp -al."
```

### Messtisch — jede Kriterienzeile eine Zeile

```text
A-30-1 (P1, TRAGEND)  ERFUELLT — SELBST GEFAHREN, alle sechs echten Staende, Rueckgabewerte einzeln
  86f94d98  exit=1  "A-29: neue TAFELZEILE ohne Datensatz-Block"     <- ROT, richtige Kennung
  ca99466b  exit=1  "W-16/1: neue TAFELZEILE ohne Datensatz-Block"   <- ROT, richtige Kennung
  875d1da5  exit=0 · c5e52994  exit=0 · c82c7f55  exit=0 · b778152b  exit=0   <- alle vier gruen
  Die roten melden nicht irgendetwas, sondern GENAU die Kennung, die im Stand fehlt.

A-30-2 (P1)           ERFUELLT
  Lauf gegen den Bau-Stand: exit=0, Ausgabe LEER — kein Fehlalarm auf dem Bestand.
  Die Zwoelf mit ihrem Muster, von mir am Bau-Stand erhoben (nicht aus dem Blatt):
    Muster [AW]-[0-9]+[A-Z]?(/[0-9A-Za-z]+)?
    TAFEL OHNE DATENSATZ (12): A-06 W-01 W-02 W-04 W-05 W-08 W-09 W-11 W-13 W-15 W-21 W-22

A-30-3 (P1)           ERFUELLT — und ich habe es nicht am Text geprueft, sondern gefahren
  Die Trennung steht im Code (a26-ball-drift.sh, "UNGEPRUEFT" getrennt vom Drift-Befund).
  EIGENE PROBE mit Anker (Treffer genau 1x, md5 vorher/nachher kontrolliert): eine Tafelzeile
  W-01 im Arbeitsbaum angefasst — daraufhin meldet A-26
    "A-26-HINWEIS  NICHT GEPRUEFT ... W-01: kein Datensatz-Block"   exit=0
  Also: gemeldet UND der Rueckgabewert NICHT gehoben. Genau das verlangt das Kriterium.

A-30-4                ERFUELLT
  Am BAU-STAND selbst erhoben, mit Muster (E1, Pruefung 7):
    Tafelzeilen 73 · Datensaetze 72 · TAFEL OHNE DATENSATZ 12 · DATENSATZ OHNE TAFEL 11
  Die 12 und die 11 stimmen mit dem Blatt zeichengleich. Die Gesamtzahlen 73/72 weichen von den
  62/61 des Blattes ab — das Blatt mass am Stand d8fd395d, der Vorrat ist seither gewachsen.
  Das ist KEIN Befund: A-30-4 verlangt die Erhebung am Bau-Stand, und genau die liegt vor.

A-30-5                ERFUELLT
  LESEND: a30-datensatz-paar.sh enthaelt kein mktemp, kein rm, keine Ausgabeumlenkung in eine
  Datei — nur git show / cat / grep / comm. Selbst gegrept, nicht geglaubt.
  TOR-ORDNUNG: der Hook steht in commit-pruefen.sh:642, NACH allen 12 exit-Punkten davor, also im
  Hauptpfad und nicht in einem Abbruchpfad; er laeuft mit `|| true`, und nach ihm folgen noch 2
  git-commit-Stellen — der Commit laeuft also weiter. Warnung, kein Abbruch.

A-30-6                ERFUELLT — docs/STATUS.md kommt im Bau-Commit 0 Mal vor.
A-30-7                ERFUELLT — resources/ und app/ kommen im Bau-Commit 0 Mal vor.
                      Der Commit fasst ausschliesslich drei Dateien unter scripts/ an.

A-30-8 (P1)           ERFUELLT
  Das Muster steht IM CODE (a30-datensatz-paar.sh:61), nicht nur im Blatt.
  Der Kommentar nennt beide Fundstellen — BEIDE selbst geoeffnet:
    docs/STATUS.md:31    "| **P-02** parallele Instanzen | `VORLAGE` | ..."     stimmt
    docs/STATUS.md:5586  "| **M-02-Kopienzahl** | **drei** Kopien gemessen ..."  stimmt
  GEGENPROBE ALS UNTERSCHIED ZWEIER LAEUFE, beide von mir gefahren:
    [AW]-[0-9]+[A-Z]?(/…)?     -> 12, P-02 NICHT dabei
    [A-Z]+-[0-9]+[A-Z]?(/…)?   -> 13, P-02 ist die dreizehnte
  Der Unterschied ist der Nachweis, und er faellt genau auf die eine Kennung.
```

### Was ich zusaetzlich gefahren habe, weil der Auftrag es im Scope verlangt und kein Kriterium es misst

```text
SCOPE (1) sagt "... und umgekehrt" — ein NEUER DATENSATZ ohne Tafelzeile. Kein Kriterium nennt
dafuer einen echten Stand. Eigene Probe, md5 von docs/STATUS.md vorher/nachher kontrolliert:
  einen Block  auftrag: "A-99"  angehaengt   ->  exit=1
  "A-99: neuer DATENSATZ ohne Tafelzeile — der Ueberblick zeigt ihn nicht"
Die zweite Richtung traegt also wirklich und steht nicht nur im Kommentar.
```

### Fangprobe — drei Mutationen, Anker je 1x, md5 jedes Mal zurueck auf 1f05e12f

```text
M1  die Verengung auf NEUES entfernt (NEUE_TAFEL = NEU_TAFEL)
      -> exit=1, GENAU ZWOELF Meldungen: A-06 W-01 W-02 W-04 W-05 W-08 W-09 W-11 W-13 W-15
         W-21 W-22
      Das ist der beste Beleg des ganzen Auftrags: die naive Fassung erzeugt nachweislich die
      zwoelf Fehlalarme, die das Blatt vorhergesagt hat — und die Verengung verhindert sie.
M2  die zweite Richtung ausgebaut (NEUE_SAETZE="")
      -> die A-99-Probe rutscht durch (0 Meldungen, exit=0). Die Zeile traegt also.
M3  das Muster auf [A]- verengt
      -> der Stand ca99466b (W-16/1) wird GRUEN statt rot. Das Muster traegt also auch.
```

### Regressionsprobe an A-26 — weil der Bau ihren Rumpf anfasst

```text
Der Bau aendert a26-ball-drift.sh. §12.1 stuft REGRESSION immer P0, deshalb selbst gefahren:
  --stand 55cd13d8^   exit=1   "W-33  BALL: Tafel 'Planner' <-> Datensatz 'generator'"
  --stand 38bc5e12    exit=1   Drift weiterhin gemeldet
  alte Fahrweise "a26-ball-drift.sh docs/STATUS.md"  -> exit=0, Ausgabe leer
  und der Tor-Hook ruft genau so (commit-pruefen.sh:619) — unveraendert.
KEINE Regression: A-26 findet ihren eigenen Zweck weiter, und der Tor-Aufruf bricht nicht.
```

### Eine Scope-Frage, die der Generator SELBST gemeldet hat — sie gehoert dem Planner

```text
Scope (2) sagt: "das Sichtbarkeitsloch in scripts/a26-ball-drift.sh:56 schliessen". Der Bau hat
zusaetzlich eine `--stand <sha>`-Fahrweise in A-26 eingebaut und schreibt dazu ausdruecklich:
"DAS GEHT UEBER DEN WORTLAUT VON SCOPE (2) HINAUS — gemeldet, nicht entschieden."

MEINE WERTUNG, und ich benenne sie als Wertung: das traegt kein NACHBESSERN.
  - Der Zusatz ist additiv; die Vorgabe-Fahrweise ist unveraendert (selbst gemessen, oben).
  - Er ist fuer den Nachweis noetig, den A-30-1 im Wortlaut verlangt: "an den ECHTEN STAENDEN
    geprobt". Ohne --stand laeuft A-26 gegen den Diff des Arbeitsbaums, und der ist an einem
    historischen Stand leer — der Nachweis waere nicht fahrbar gewesen.
  - Er ist gemeldet statt verschwiegen, und zwar bevor jemand fragte.
  - Keine Regression (Probe oben).
Die Entscheidung ueber Scope trifft aber nicht der Evaluator. ALS FRAGE AN DEN PLANNER: soll
Scope (2) um die Fahrbarkeit an historischen Staenden erweitert werden, oder ist der Zusatz ein
eigener kleiner Vorgang? Ich habe ihn weder beanstandet noch gutgeheissen — ich habe ihn gemessen.
```

### Meine eigenen Messfehler in diesem Durchgang — zwei

```text
1  ICH HABE "0 DECKUNGSLUECKEN" GEMESSEN UND WAERE BEINAHE MIT EINEM BEFUND HERAUSGEKOMMEN.
   Mein Aufruf war `a26-ball-drift.sh docs/STATUS.md` im sauberen Pruefstand — ohne --stand liest
   die Barriere `git diff HEAD`, und der ist dort LEER. Kein Diff, keine IDs, keine Meldung. Die
   Barriere war richtig, mein Aufbau falsch. Erst die Anker-Probe an W-01 (oben) hat sauber
   gemessen. Haette ich die Null als Befund genommen, stuende hier "die Deckungsluecken-Meldung
   greift nicht" gegen einen Bau, der sie sehr wohl bringt.
2  Mein erster Kriterien-Block endete mit exit 1 und sah nach Fehlschlag aus — das kam aus meiner
   eigenen `[ ... ] && echo`-Kette als letztem Befehl, nicht aus dem Bau. Vor dem Melden geprueft.
```

**Weitergabe:** ABGENOMMEN → **Release-Prüfer**. Die Scope-Frage zur `--stand`-Fahrweise in
A-26 → **Planner** (keine Entscheidung durch mich).
