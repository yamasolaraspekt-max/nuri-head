# B5 — ein Zählergebnis, das einen Befund trägt, wird nie ohne seine Trefferzeilen gemeldet

```yaml
auftrag: "B5"
art: "sechste Barriere, nach dem Muster von B3 und B4 — Regel im BEFEHL, nicht im Kopf"
titel: "Wer mit -c etwas behauptet, faehrt denselben Lauf ohne -c und liest, was er gezaehlt hat"
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: 1734aa3b
prioritaet: P1
anlass: "Yamas Auflage 0b vom 11.08. 22:12. Fuenf Faelle derselben Klasse an EINEM Tag;
         §13 loest die Ursachenpruefung bei der ZWEITEN Wiederholung aus."
verursacher: "planner (alle fuenf Faelle sind meine)"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## Die Regel, wörtlich wie Yama sie gesetzt hat

> **B5 · Ein Zählergebnis, das einen Befund trägt, wird nie ohne seine Trefferzeilen gemeldet.**
> *Wer `-c` benutzt, um etwas zu behaupten, führt denselben Lauf ohne `-c` und liest, was er gezählt
> hat. Gilt für alle fünf Rollen, gilt auch für Messungen über die eigene Werkbank.*

## Die fünf Fälle — alle meine, alle an einem Tag, jeder mit seiner Zahl

```text
1  F-031 CSG als "gebaut" gezaehlt          grep auf 'csg' -> 1 Treffer.
   (603eddc2, vor dem Schreiben gefunden)   Beim ANSEHEN: dachAusschnitt.ts:10
                                            " * - Stufe C (NICHT hier): … CSG"
                                            Ein Treffer im DATEIKOPF ist kein Code.
2  Baurichtlinie: zwei greps ins Leere      Zitate gehen ueber ZWEI Zeilen, mein
   (dcf0071c)                               einzeiliger grep fand 0 und ich hielt es
                                            fast fuer einen Befund.
3  "die Werkbank kennt sie nicht"           1 Treffer in SCHICHTEN.md, den ich als
   (dcf0071c)                               Fehlmessung abtat — er war echt und hat
                                            meine These verbessert.
4  W-07 Platzhalter: 7, einer falsch        grep -rnoE '<[^>]{3,40}>' -> 7.
   (1734aa3b)                               Der siebte war "< 1 mm²", ein
                                            VERGLEICHSOPERATOR in einer Tabelle.
5  W-07 Platzhalter: 6, DREI fehlten        Ich setzte {2,40} ins Muster, um Fall 4
   (Yama fand 9)                            loszuwerden — und verlor :17 (86 Zeichen),
                                            :29 und :30. Der Filter gegen einen
                                            Fehlertyp erzeugte einen anderen.
   Zusatz: awk-Scope-Filter                 fing auch "NICHT im Scope"-Zeilen mit und
                                            meldete vier Blaetter statt keines.
```

> **Fall 4 und 5 sind die Lehre in Reinform: ich habe den ersten Messfehler bemerkt, ihn mit einer
> engeren Grenze „behoben" — und dabei einen größeren erzeugt, ohne es zu sehen.** *Beide Male hätte
> ein Blick auf die Trefferzeilen gereicht. **Beim ersten Mal hätte `-n` den Vergleichsoperator
> gezeigt; beim zweiten Mal hätte `-n` gezeigt, dass drei Zeilen fehlen.*** Yama hat es mit einem
> `grep -n` ohne Längengrenze in einem Lauf gefunden.

## DECISION

```text
WO       scripts/commit-pruefen.sh — dasselbe Tor, das B4 (TICKET_ROLLE) traegt.
WAS     Das Tor kann nicht pruefen, WIE gemessen wurde. Es kann aber pruefen, ob eine
        Botschaft eine ZAHLENBEHAUPTUNG ohne Belegzeilen traegt.
FORM    Warnung, nicht Abbruch — Stufe 1 der Barrierenleiter.
        Grund: eine harte Sperre auf Zahlen in Commit-Botschaften wuerde jeden
        legitimen Bericht blockieren (Suite 1692/1692, 0 Platzhalter, 5 von 10).
        Ein Abbruch, der bei jedem zweiten Commit falsch anschlaegt, wird umgangen —
        das ist an A-03 belegt (Riegel um artisan serve, benutzt wurde php -S).
DAZU    Eine Zeile im PRUEFWEG jeder Rolle: "Zaehlergebnis nie ohne Trefferzeilen."
        Die Regel wirkt beim SCHREIBEN, das Tor erinnert beim COMMITTEN.
NICHT   Kein Verbot von grep -c. Es ist richtig, wenn die Zahl SELBST der Gegenstand
        ist (Suite 1692/1692). Verboten ist, aus einer Zahl einen BEFUND zu machen,
        ohne die Zeilen gelesen zu haben.
```

> **Die Unterscheidung ist der Kern, und sie muss ins Blatt:** *„Die Suite zählt 1692" ist eine Zahl
> als Gegenstand — die Trefferzeilen wären sinnlos.* **„CSG kommt einmal vor, also ist es gebaut" ist
> ein Befund aus einer Zahl — dort ist die Zeile alles.** *Wer B5 als „nie `-c` benutzen" liest, hat
> sie falsch verstanden und macht jede Suite-Meldung unlesbar.*

## Nicht-Ziele

- **Kein Abbruch am Tor.** Warnung. *Begründung oben, mit Beleg an A-03.*
- **Keine Prüfung, ob die Messung inhaltlich richtig ist.** Das kann kein Tor.
- **Kein Anfassen von B1–B4.** Sie bleiben unverändert; B5 tritt daneben.
- **Keine Änderung an `resources/**`.** Das Tor ist ein Shell-Skript in `scripts/`.

## Scope

```text
scripts/commit-pruefen.sh                    Warnzeile ergaenzen
docs/ARBEITSREGELN.md                        B5 in die Barrierenliste
docs/rollenkette/**/PRUEFWEG*.md ODER die Rollenblaetter   eine Zeile je Rolle
   -> der GENERATOR entscheidet nach Messung, wo der Pruefweg tatsaechlich steht.
      Ich habe es NICHT gemessen und behaupte es deshalb nicht (das waere Fall 6).
```

## Wiederverwendungsprüfung (§5)

```text
B4 / TICKET_ROLLE     VORHANDEN im Tor — Muster fuer eine Marken-/Warnzeile
B1 (&& zwischen
   Skript und Tor)    VORHANDEN — Muster fuer eine harte Sperre (hier NICHT gewaehlt)
B2 (ganze Datei nach
   der Korrektur)     VORHANDEN — der naechste Verwandte: B2 prueft NACH einer
                      Korrektur, B5 prueft VOR einer Behauptung
A-10 (Melder am
   leeren Ergebnis)   VORHANDEN — dieselbe Familie: sag es, wenn du nichts hast.
                      B5 ist ihr Zwilling: sag WAS du gezaehlt hast.
scripts/commit-pruefen.sh   VORHANDEN, traegt bereits Rollenmarke, Pfadpruefung,
                      Index-Angleichung -> anschliessen, nicht neu bauen
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER (scripts/, kein app/)
Testdaten-Ziel                                               KEINES
Datenbank                                                    NICHT BERUEHRT
Prozessbindung                                               JA — B5 wird Regel fuer alle
                                                             fuenf Rollen
Werkzeuge                                                    bash; die Tor-Zusagen
                                                             (scripts-Suite) muessen gruen
                                                             bleiben
```

**Erstnutzer:** *ich selbst, beim nächsten Zählbefund. **Und der Test der Barriere ist, ob sie MICH
fängt** — fünf von fünf Fällen sind meine.*

## Akzeptanzkriterien

**B5-1 (P1, die Warnung existiert und feuert):** Das Tor gibt eine Warnung aus, wenn die
Commit-Botschaft eine Zahlenbehauptung ohne Belegzeile trägt. **Rot-Lage heute messbar:**

```text
grep -cE 'Trefferzeile|B5' scripts/commit-pruefen.sh   -> heute 0
```

**B5-2 (P1, sie feuert NICHT bei legitimen Zahlen):** Eine Botschaft mit `Suite 1692/1692` oder
`0 Platzhalter` löst **keine** Warnung aus. *Nachweis: zwei Probeläufe im Wegwerf-Repo, einer der
feuert, einer der schweigt — beide Ausgaben im Bericht.* **Ohne diesen Gegenbeleg ist die Barriere
eine Belästigung und wird umgangen.**

**B5-3 (P1, kein Abbruch):** Der Rückgabewert des Tors bleibt bei einer B5-Warnung **unverändert
gegenüber heute**. *Nachweis: derselbe Commit einmal mit und einmal ohne die Warnung, `echo $?`
beide Male gleich.*

**B5-4 (P1, die Regel steht in ARBEITSREGELN):** B5 steht in der Barrierenliste, mit dem
**Unterschied zwischen „Zahl als Gegenstand" und „Befund aus einer Zahl"** — sonst wird sie als
„nie `-c`" gelesen.

**B5-5 (P1, der Prüfweg wird GEMESSEN, nicht angenommen):** Der Generator **misst zuerst**, wo der
Prüfweg je Rolle tatsächlich steht, und trägt die Zeile dort ein. *Falls es keinen gemeinsamen
Prüfweg gibt, sagt er das — statt eine Datei zu erfinden.* **Ich habe es nicht gemessen und
behaupte es deshalb nicht.**

**B5-6 (`must_preserve`):** `resources/**` und `app/**` byte-identisch. Die `scripts/`-Suite bleibt
grün (ohne Zahl). Die bestehenden Torfunktionen — Rollenmarke, Pfadprüfung, Index-Angleichung —
unverändert; **Nachweis: `git diff` zeigt nur Einfügungen im Tor, 0 gelöschte Zeilen.**

**B5-7 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, mindestens zwei Befehlszeilen
und zwei Ausgabewerte. **Und die Messung fällt UNMITTELBAR vor der ersten Änderung** *(Lehre aus
`ce30174f`)*.

## Kantenliste

```text
Warnung feuert bei jeder Zahl        -> B5-2 faengt es. Eine Barriere, die immer
                                        anschlaegt, wird abgeschaltet (A-03-Beleg).
Warnung wird zum Abbruch             -> VERBOTEN, B5-3 misst den Rueckgabewert.
"nie grep -c benutzen"               -> FALSCHLESUNG. B5-4 verlangt die Unterscheidung
                                        im Regeltext.
Das Tor prueft die Messung inhaltlich -> kann es nicht, ist Nicht-Ziel.
Pruefweg-Datei existiert nicht        -> B5-5: dann sagt der Generator das.
```

## Rückweg und Entdeckung

**Rückweg:** eine Warnzeile im Tor, eine Regelzeile, eine Prüfwegzeile. `git revert` genügt; das Tor
bleibt in jeder Zwischenstufe funktionsfähig, weil die Warnung den Rückgabewert nicht ändert.

**Entdeckung:** Tritt ein sechster Fall derselben Klasse auf — *eine Zahl trägt einen Befund, die
Zeilen sind nicht gelesen* —, hat die Warnung nicht gewirkt. *Dann ist die nächste Stufe eine harte
Sperre, und der Preis dafür ist an fünf Fällen belegt.*

## Konfliktprüfung (§5)

```text
§3 gemessen 12.08.   1 IN_ARBEIT -> W-21/1 (9bd728fe), Scope werkbank/W-21/** + REGISTER.md
B5 (dieses)          scripts/commit-pruefen.sh + ARBEITSREGELN.md + Pruefweg
                     -> KEINE Beruehrung mit W-21. Disjunkt.
W-07N                werkbank/W-07/** + REGISTER.md -> beruehrt W-21s Scope, wartet auf §3
A-09 / A-11          haben scripts/commit-pruefen.sh beruehrt, beide VEROEFFENTLICHT
                     -> kein offener Auftrag auf dem Tor. Basis frei.
```

```yaml
fehlerklasse: "Messweg — fuenf Faelle, alle planner, alle 11./12.08."
prioritaet: P1
warteschlange: "kann sofort in DoR — beruehrt REGISTER.md NICHT und ist damit von W-21
                unabhaengig. Reihenfolge: vor W-07N, weil B5 dessen Messungen absichert."
kern: "eine Zahl beweist nichts, solange niemand gelesen hat, WAS sie gezaehlt hat.
       Und ein Filter, der einen Fehlertyp ausschliesst, erzeugt leicht einen anderen."
```

## §11 — Votum B5 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "B5"
votum: ABGENOMMEN
fehlerklasse: KEINE   # ein P2, blockiert nicht
abnahme_commit: "157576c2"
elter: "2a95ab40"
in_arbeit_commit: "c528161c"
pruefstand: "worktree --detach auf 157576c2 und 2a95ab40, node_modules UND vendor per cp -al"
pruefform: "B5 baut eine BARRIERE. Ich habe sie deshalb AUSGELOEST statt ihren Code zu lesen:
     Wegwerf-Repo, das Tor hineinkopiert, drei Probelaeufe und eine Mutation."

messtisch_alle_sieben:
  B5-1: GRUEN
    rot_lage_selbst_gemessen: "grep -cE 'Trefferzeile|B5' scripts/commit-pruefen.sh —
            am ELTER 0 (wie das Blatt sagt), im Bau 8."
    ausgeloest: |
      $ TICKET_ROLLE=evaluator bash scripts/commit-pruefen.sh \
          "der Begriff kommt einmal vor, also ist er gebaut" docs/a.md
      B5-WARNUNG  Zaehlwort in der Botschaft, aber keine Belegzeile (datei.ext:zeile).
                  Zahl als Gegenstand ist in Ordnung. Traegt die Zahl einen BEFUND,
                  fahre denselben Lauf ohne -c und nimm die Zeilen mit, die du gezaehlt hast.
                  Warnung, kein Abbruch — der Commit laeuft weiter.
      exit=0, Commit 7b3df02 ist entstanden.
    mutation: "B5_ZAEHLWORT-Block entfernt (Anker genau 1x), derselbe Lauf: 0 Warnungen.
            Zurueckgestellt, md5 identisch. Die Warnung kommt nachweislich aus diesem Code."
  B5-2: GRUEN
    gegenprobe_1: "'Suite 1692/1692 gruen, 0 Platzhalter' -> 0 Warnungen, exit=0."
    gegenprobe_2: "'drei Treffer gemessen, Trefferzeilen: geometry/treppe.ts:42, :57, :91'
            -> 0 Warnungen. Ein Zaehlwort MIT Belegzeile schweigt, wie es soll."
  B5-3: GRUEN
    beleg: "exit=0 bei feuernder UND bei schweigender Botschaft. Der Commit der feuernden
            Probe ist entstanden — die Warnung sperrt nicht, sie erinnert."
    im_code_abgesichert: "Die Stelle liegt NACH dem Fehler-Riegel und setzt weder FEHLER=1
            noch exit. Das habe ich nach dem Auslesen zusaetzlich gelesen, nicht stattdessen."
  B5-4: GRUEN
    beleg: "ARBEITSREGELN §18b, neu. Die Unterscheidung steht als Tabelle:
            'Zahl als Gegenstand' 2 Treffer, 'Befund aus einer Zahl' 2 Treffer. Elter: 1
            B5-Erwaehnung (die H-6-Querverweiszeile), Bau: der ganze Abschnitt."
    und_das_leere_ergebnis_gemeldet: "Der Auftrag sagt 'B5 steht in der Barrierenliste'.
            Eine solche Liste GIBT ES NICHT — am Elter 0 Treffer, von mir nachgemessen.
            Der Bauende hat das gemeldet statt eine Liste zu erfinden, mit Verweis auf A-10.
            Genau die Antwort, die ich sehen will."
  B5-5: GRUEN
    beleg: "Er hat zuerst gemessen: find docs -iname '*pruefweg*' -o -iname '*rollenblatt*'
            -> 0 Dateien. SELBST nachgefahren: ebenfalls 0. Die Alternative
            4-WAS-ICH-ABLIEFERE.md existiert fuenfmal (nachgezaehlt: 5), und die neue
            Trefferzeilen-Zeile steht in allen fuenf."
    warum_das_gut_ist: "Das Blatt sagte woertlich 'Ich habe es nicht gemessen und behaupte es
            deshalb nicht'. Der Bauende hat die Luecke gemessen statt sie zu fuellen."
  B5-6: GRUEN
    beleg: "resources/ und app/ 0 Dateien · Tor +30/-0 (nur Einfuegungen, wie verlangt) ·
            scripts-Suite: Bau 107/107/0, Elter 107/107/0."
  B5-7: GRUEN
    beleg: "c528161c (09:02:14) setzt BEIDE Orte und traegt die Scope-Messung: 43
            Auftragsbloecke, davon IN_ARBEIT 0, 'gehaltene Dateien: KEINE', dazu der eigene
            Scope namentlich. Erste Aenderung 09:23:08 — 21 Minuten spaeter, Reihenfolge stimmt."

p2_die_musterluecke:
  klasse: BEWEIS
  schwere: P2
  was: "Das Belegmuster verlangt eine Dateiendung: [A-Za-z0-9_./-]+\\.[A-Za-z]{1,5}:[0-9]+.
        Fundstellen in den Schreibweisen 'Z.157', 'treppenTypen:4', 'Zeile 39' erkennt es nicht —
        die Warnung feuert dann, obwohl die Zeilen dastehen."
  gemessen: "Ueber die letzten 30 Commit-Botschaften: 8 ohne Zaehlwort (nie betroffen),
        5 mit Zaehlwort UND erkannter Belegzeile (schweigen), 17 warnend. Von diesen 17 tragen
        VIER eine Fundstelle in nicht erkannter Form — 95b4de4f (Z.73, Z.1672, Z.2255),
        50505407 (Z.157, Z.1), a704fb1d (:38, :19, treppenTypen:4), 379c134e (Zeile 39).
        Die uebrigen dreizehn warnen zu Recht."
  bewertung: "Kein verletztes Kriterium — B5-2 nennt zwei Beispiele, und beide schweigen.
        Aber der Auftrag begruendet -2 damit, dass eine Barriere, die zu oft falsch anschlaegt,
        umgangen wird. Vier vermeidbare Warnungen in dreissig Commits sind wenig; die Erweiterung
        des Belegmusters um ':[0-9]+' nach einem Wort und um 'Z.' waere zwei Zeichen teuer."
  ball: "generator oder planner — keine Blockade, ein Vorschlag."

zwei_eigene_fallen_offengelegt:
  vendor_vergessen: "Mein erster Suite-Lauf meldete 106/107 mit EINEM Fehlschlag. Beinahe ein
        Regressionsbefund. Gelesen statt gezaehlt: es fiel 'A-03-4 KONTROLLE: php artisan serve
        bleibt unangetastet (must_preserve)' mit der Meldung 'ServeCommand fehlt — dann misst die
        Zusage Leere'. Ursache: ich hatte node_modules verlinkt, aber VENDOR VERGESSEN — exakt der
        Fehler, den mein eigener Takt namentlich auffuehrt und der mir bei A-04 schon einmal
        passiert ist. Mit vendor: Bau 107/107, Elter 107/107. Die Zusage hat mich gewarnt, indem
        sie sagte, dass sie Leere misst — genau dafuer ist sie gebaut."
  ich_haette_gezaehlt_statt_gelesen: "Meine erste Falschpositiv-Messung lautete '17 von 30
        Botschaften warnen'. Als Rate gemeldet waere das ein schwerer Vorwurf gewesen. Erst das
        LESEN der 17 zeigte: dreizehn warnen zu Recht, vier wegen der Musterluecke. Das ist B5,
        angewandt auf mich, waehrend ich B5 pruefe — und der Grund, warum ich den Befund als
        schmalen P2 melde statt als Quote."

sein_nebenfund_trifft_MEIN_werkzeug:
  was_er_meldet: "Der kanonische §3-Tafelbefehl trifft '^| ** gefolgt von [AW]-[0-9]+' und
        uebersieht damit B-, M- und P-Auftraege. Er hat den Befehl NICHT einseitig geaendert,
        weil er als Kriteriumstext in vielen Blaettern steht, sondern gemeldet."
  selbst_nachgemessen: "In docs/STATUS.md findet mein eigenes Takt-Muster [AW]- 30 Tafelzeilen,
        ein breites Muster 34. Unsichtbar sind: P-02, B5, B6, B7."
  was_das_fuer_mich_heisst: "Ich habe B5 in der TAFEL nie gesehen — ich habe diesen Auftrag ueber
        den Datensatz-Parser gefunden. Meine §3-Zaehlung 'Tafel IN_ARBEIT: 0' war in mehreren
        Takten falsch, waehrend B5 sichtbar auf IN_ARBEIT stand. Der Befund gehoert dem Planner,
        aber die Lehre gehoert mir: ein Raster, das ich seit Tagen benutze, hat vier Zeilen nie
        gezeigt — dieselbe Klasse wie meine W-Auftrags-Luecke von vorgestern, nur eine Ebene
        weiter. Ich fuehre den Takt ab sofort mit dem breiten Muster."

zusammenfassung: "Sieben von sieben. Die Barriere feuert, wo sie soll, schweigt, wo sie soll,
     und sperrt nicht — alles drei von mir ausgeloest, nicht gelesen, und die Mutation zeigt,
     dass die Warnung aus genau diesem Code kommt. Zwei Stellen heben den Bau ueber den
     Durchschnitt: die 'Barrierenliste' des Auftrags gibt es nicht, und er hat das gemeldet
     statt sie zu erfinden; und den §3-Befehlsfehler, den er beim Belegen selbst ausgeloest hat,
     hat er nicht stillschweigend repariert, obwohl er es haette tun koennen. Ein P2 zur
     Musterluecke, der nicht blockiert."

ballbesitz: release-pruefer
```
