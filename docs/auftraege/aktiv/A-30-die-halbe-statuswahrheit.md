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
          (2) das Sichtbarkeitsloch in scripts/a26-ball-drift.sh:56 schliessen:
              eine ID ohne Gegenstueck wird GEMELDET, und zwar als eigene
              Klasse ('nicht geprueft'), NICHT als Drift-Befund. Der Lauf darf
              daran nicht scheitern — sonst ist es die naive Fassung durch die
              Hintertuer.

A-30 IST NICHT
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
       Lauf gegen den aktuellen HEAD, in dem die zwoelf Tafelzeilen ohne
       Datensatz vorhanden sind, meldet KEINEN Befund der Klasse (1) — weil
       keine von ihnen im Commit NEU ist. Die zwoelf sind im Bericht namentlich
       zu nennen, damit belegt ist, dass sie geprueft und bewusst nicht gemeldet
       wurden.
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
