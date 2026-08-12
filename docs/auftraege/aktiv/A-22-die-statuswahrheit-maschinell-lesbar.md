# A-22 — Die Statuswahrheit ist maschinell nicht verlässlich lesbar

```yaml
auftrag: "A-22"
titel: "Doppelte yaml-Schlüssel und uneinheitliche Feldform in docs/STATUS.md"
art: "DATENFORM. Fasst ausschließlich docs/STATUS.md an — keine Regelwerksänderung.
      Drei Befunde eines Tages an derselben Datei, zwei davon hier behebbar."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: e1a478fb
prioritaet: P1
anlass: "Der Generator hat in e1a478fb 17 doppelte yaml-Schlüssel gemeldet, aus einem Nebenbefund
         des Evaluators in e5716bc0. Selbst nachgemessen und bestätigt. Es ist der DRITTE
         Strukturmangel dieser Datei an einem Tag — die anderen zwei stehen in Abschnitt 4."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## 1 — Die Messung (selbst gefahren über alle yaml-Blöcke)

```text
STAND e1a478fb, beim Schnitt         STAND 6855e9c7, nach zwei fremden Selbstkorrekturen
  145 yaml-Bloecke                     147 yaml-Bloecke
  DOPPELTE SCHLUESSEL     17            DOPPELTE SCHLUESSEL     14
    davon gleicher Wert    0              davon gleicher Wert    0
    ballbesitz             4              ballbesitz             0   <- BEHOBEN
    release_vermerk       10              release_vermerk       10
    letztes_votum          1              letztes_votum          1
    claim_abnahme          1              claim_abnahme          1
    eigener_messfehler     1              eigener_messfehler     1
                                          ballbesitz_bau         1   <- NEU dazu
  FELDFORM auftrag:  33 / 19 ohne       FELDFORM auftrag:  34 / 19 ohne
```

> **Die Ist-Messung eines Auftrags ist ein Zeitpunkt-Beleg und veraltet, wenn fünf Rollen parallel
> arbeiten.** *Zwischen Schnitt (`c1ad3e02`) und dieser Berichtigung haben **zwei fremde Rollen den
> gefährlichsten Teil selbst behoben** — der Release-Prüfer in `09c666d7`, der Generator in
> `6855e9c7`, beide als Selbstkorrektur an eigenen Einträgen. **Die DoR war zu diesem Zeitpunkt
> schon bestanden** (`be098f08`, bei noch vier Dubletten). Deshalb stehen hier ab jetzt **beide
> Stände mit ihrem SHA** und nicht eine Zahl.*

> **Dass KEINE der 17 denselben Wert trägt, ist die eigentliche Aussage.** *Der Generator hat es
> richtig eingeordnet: **niemand schreibt denselben Wert zweimal.** Jemand schreibt einen **neuen
> neben den alten**, statt den alten zu ändern. Jede Dublette ist also ein überholter Wert, der
> stehen geblieben ist — und keine Redundanz.*

## 2 — Die vier gefährlichen: `ballbesitz` auf abgeschlossenen Aufträgen

```text
W-01N · W-15/1 · B7 · A-21
  erste Zeile   ballbesitz: —  # Kette vollstaendig
  letzte Zeile  ballbesitz: generator      <- Rest aus der Zeit, als der Auftrag dort lag
```

**Die Asymmetrie, die der Evaluator benannt hat, macht daraus einen Fehlbefund:**

```text
Takt-Parser  nimmt das ERSTE Vorkommen   ->  sieht „Kette vollstaendig"   richtig
YAML         nimmt das LETZTE Vorkommen  ->  sieht „generator"            falsch
```

> **Ein YAML-Leser sieht damit VIER abgeschlossene Aufträge beim Generator liegen** — *und das ist
> „belegt melden, wo frei ist". **Geöffnet statt gezählt** (Pflichtprüfung 7): A-21 trägt
> `zustand: BETRIEBSBESTAETIGT`, seine Tafelzeile trägt als Ballbesitz einen Gedankenstrich, die
> erste Blockzeile `Kette vollstaendig` — und allein die letzte weicht ab. **Ausgerechnet die liest
> YAML.***

## 3 — Die dreizehn übrigen sind Belege und werden NICHT gelöscht

`release_vermerk` (10×), `letztes_votum`, `claim_abnahme`, `eigener_messfehler`: je ein Vermerk der
Stamm-Instanz neben einem einer frischen Instanz. **Beide sind echte Aufzeichnungen** — der zweite
widerlegt den ersten nicht, er kommt von einem anderen Lauf.

> **Hier gilt A-20-4 wörtlich:** *wer löscht, ohne zu sagen, was gegolten hat, vernichtet einen
> Befund. **Die Abhilfe ist Eindeutigkeit, nicht Entfernung** — beide Vermerke bleiben, aber unter
> unterscheidbaren Schlüsseln, damit ein YAML-Leser beide sieht statt nur den letzten.*

## 4 — Der dritte Befund gehört NICHT in diesen Auftrag, aber auf den Tisch

```text
NEBENLAEUFIGKEIT   Heute VIER Beifang-Vorgaenge an docs/STATUS.md:
                   release-pruefer nahm meine Tafelzeile mit
                   plan-pruefer    nahm mein Datensatzfeld mit
                   evaluator       nahm mein berichtigtes Feld mit
                   generator       committete OHNE die Datei — der einzige, der es vermied
                   und dazu die Regelkollision, die der Generator gemessen hat:
                   „zweiter Commit unmittelbar" gegen „nie fremde unverfolgte Arbeit
                   einsammeln" — bei belegter Datei ist nur EINE von beiden erfuellbar.
```

> **Das ist kein Formfehler, sondern die Bauart: fünf Rollen schreiben in eine Datei, und keine
> kann sie halten.** *Jede Abhilfe — Datensätze in eigene Dateien je Auftrag, eine Schreibsperre,
> eine andere Zerlegung — **ändert, wie alle fünf Rollen arbeiten.** Das ist **Yamas
> Entscheidung**, nicht meine, und deshalb steht sie hier als benannter offener Punkt und nicht als
> Kriterium.*

## 5 — Abnahmekriterien

```text
A-22-1  0 doppelte yaml-Schluessel MIT ABWEICHENDEM Wert in docs/STATUS.md.
        Nachweis: das Raster aus Abschnitt 1 ueber alle yaml-Bloecke, vorher 17.
        Gemessen wird die STRUKTUR (Schluessel je Block), nicht ein Wort im Volltext —
        deshalb waechst dieser Messgegenstand nicht durch das Dokumentieren des
        Befunds (Pflichtpruefung 8; A-21-3 ist daran gescheitert).
A-22-2  ERLEDIGT VOR DEM BAU, und NICHT durch diesen Auftrag — Kriterium gestrichen.
        Die vier ballbesitz-Dubletten (W-01N, W-15/1, B7, A-21) sind aufgeloest: der
        Release-Pruefer in 09c666d7, der Generator in 6855e9c7, beide als
        Selbstkorrektur an eigenen Eintraegen. Nachgemessen: ballbesitz-Dubletten am
        Stand be098f08 noch 4, am Stand 6855e9c7 genau 0.
        NACH PFLICHTPRUEFUNG 4 IST EIN GRUENES KRITERIUM KEINS — es beschreibt den
        Bestand statt ihn zu pruefen. Wer es stehen liesse, kaeme mit einem Haken
        zurueck, den er nicht verdient hat.
        WAS AN DER STELLE ZU TUN BLEIBT: nichts. Die URSACHE bleibt und steht in
        A-22-2b, denn der Generator hat sie selbst gemessen und sie ist kein
        Einzelfall.
A-22-2b Die URSACHE der ballbesitz-Dubletten ist im Bericht benannt: 65 Commits des
        Generators auf docs/STATUS.md fuegen eine ballbesitz-Zeile HINZU, statt eine
        vorhandene zu aendern — seine eigene Messung, und zweimal an einem Tag
        eingetreten (A-21 in 869c560d, W-34 im selben Handgriff). Ein neues
        ballbesitz_bau ist bereits nachgewachsen. Das Kriterium verlangt KEINE
        Verhaltensaenderung fremder Rollen, sondern dass die Bereinigung den
        Nachwuchs MITZAEHLT: die Zahl am Bau-Stand, nicht die aus diesem Blatt.
A-22-3  Die Aufzeichnungs-Dubletten sind INHALTLICH vollstaendig erhalten und unter
        unterscheidbaren Schluesseln eindeutig. Nachweis: die Zahl der Vermerktexte
        vorher und nachher ist gleich — kein Text ist verschwunden. Wer hier loescht,
        verletzt A-20-4.
        KEINE FESTE ZAHL: das Blatt nannte 13, gemessen sind es jetzt 14 (ein
        ballbesitz_bau ist nachgewachsen). Gezaehlt wird am BAU-STAND, und die Zahl
        gehoert in den Bericht statt in dieses Kriterium — dieselbe Lehre wie bei den
        Halden-Zahlen: eine feste Zahl in einem Kriterium driftet.
A-22-4  Alle auftrag:-Felder tragen dieselbe Form. Nachweis: die Zaehlung aus
        Abschnitt 1 liefert 0 in der abweichenden Form, vorher 19.
A-22-5  KEINE FREMDE Zustandsaenderung. Nachweis AM COMMIT: git show <bau-sha> --
        docs/STATUS.md zeigt geaenderte zustand:-Zeilen und Tafelzeilen nur dort, wo
        dieser Auftrag sie aendert — die EIGENE Fertigmeldung ist ausdruecklich
        erlaubt und braucht keinen zweiten Commit.
        (Pflichtpruefung 9: A-21-6 verbot dem Bauenden seine eigene Meldung und
        erzwang damit ein Zeitfenster, das sich prompt gefuellt hat.)
A-22-6  Der Nebenlaeufigkeits-Befund aus Abschnitt 4 ist im Bericht als YAMA-ENTSCHEIDUNG
        benannt und NICHT mitentschieden. Wer ihn nebenbei loest, hat die Arbeitsweise
        von fuenf Rollen geaendert, ohne dass Yama gefragt wurde.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **mindestens eine Stelle je Zählung geöffnet**
(Pflichtprüfung 7), **Messung am Commit** (E1).

```yaml
warum_P1: "Vier abgeschlossene Auftraege sehen fuer jeden YAML-Leser aus, als lagen sie beim
        Generator. Das ist die Richtung, die BELEGT meldet wo FREI ist — heute harmlos, weil alle
        vier erkennbar BETRIEBSBESTAETIGT sind, aber die Datei ist die einzige Statuswahrheit und
        wird maschinell gelesen."
warum_KEINE_regelwerksaenderung: "A-20 und A-21 haben heute beide ARBEITSREGELN.md angefasst und
        beide sind betriebsbestaetigt. Hier ist nichts zu regeln, sondern eine Datenform zu
        bereinigen — die Regel dagegen steht schon: eine Statuswahrheit, zwei Zustandsorte."
was_dieser_auftrag_NICHT_ist: "Keine Zerlegung von docs/STATUS.md, keine neue Schreibordnung, kein
        Eingriff in die Arbeitsweise der Rollen. Genau das ist Abschnitt 4 und gehoert Yama."
A_22_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
