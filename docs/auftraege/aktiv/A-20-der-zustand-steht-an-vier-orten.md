# A-20 — Der Zustand steht an vier Orten, und 17 von 24 Blättern widersprechen sich selbst

```yaml
auftrag: "A-20"
titel: "Vier Zustandsorte auf zwei reduzieren — und die Frage beantworten, wer Tafelzeile und
        Datensatz anlegt"
art: "REGELWERK. Fasst docs/ARBEITSREGELN.md an (§16, §5) und danach 32 Auftragsblätter.
      Wie A-19: zwei Punkte in EINEM Blatt, weil beide dieselbe Datei anfassen."
spur: A
heimat_app: ticket
dor_beleg: "7d2e4f31 — plan-pruefer 12.08., DoR BESTANDEN MIT OFFENGELEGTER BEFANGENHEIT: der
         Auftrag regelt seine eigene Rolle und entscheidet seine eigene Frage GEGEN seinen
         Vorschlag. Er hat das offengelegt und gegen die Kriterien des Blattes geprueft statt
         gegen seinen Vorschlag, und die Entscheidung sachlich bestaetigt — der Block entsteht
         mit dem Auftrag statt Stunden spaeter, und die Leerstelle, die er heute ZWEIMAL selbst
         schliessen musste, kann dann nicht mehr auftreten. Seine Rot-Lage: 31 von 32, nachdem
         er sein eigenes Verfahren korrigiert hatte (erster Lauf 39 von 40, als unplausibel
         erkannt und zum Pruefanlass gemacht). Seine Bitte um die Grundmengen ist in 1a
         beantwortet: zwei Vergleiche, 17 von 24 und 21 von 22, beide gelten."
status_steht_in: docs/STATUS.md
basis_sha: f1296de8
prioritaet: P1
anlass: "Der plan-pruefer hat in 4ea7398d ausdrücklich an den Planner gefragt, wer den
         Datensatz-Block anlegt, und einen Vorschlag gemacht. Beim Nachmessen der Antwort ist ein
         größerer Befund aufgetaucht: die Widersprüche sind nicht Einzelfälle, sondern die Regel."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## 1 — Die Messung, die den Auftrag auslöst

```text
Befehl: je Datei in docs/auftraege/aktiv/ das erste  ^status: <WORT>  (Kopf)
                                        und das erste ^zustand: <WORT> (Fuss) vergleichen

  Auftragsblätter                       42
  davon mit  status:  im Kopf           32
  davon mit  zustand: im Fuss           24
  davon Kopf UNGLEICH Fuss              17     <- 17 von 24, nicht 3 von 24
```

**Die 17 im Einzelnen:**

```text
Kopf=ENTWURF  Fuss=CODE_FERTIG   W-01 W-02 W-04 W-05 W-08 W-09 W-11 W-13 W-21 W-22
                                 A-13 A-14 A-15
Kopf=BEREIT   Fuss=ENTWURF       A-17 B7 W-27
Kopf=CODE_FERTIG Fuss=ENTWURF    W-20
```

> **Die zehn W-Blätter der ersten Zeile sind die Klasse-A-Werkzeuge**, und die Statuswahrheit sagt
> für alle zehn `BETRIEBSBESTAETIGT`. *Kopf und Fuß liegen also nicht nur untereinander im Streit,
> sondern beide gegen die Wahrheit — und **es sind meine Blätter**, ich habe sie geschnitten.*

### 1a — Die Grundmengen, auf Bitte des plan-pruefers (7d2e4f31)

Er misst **31 von 32** und ich nenne **17 von 24**; er hat zu Recht verlangt, dass die Grundmenge
dabeisteht, „sonst prüft der Evaluator gegen eine Zahl ohne Grundmenge". **Es sind zwei
verschiedene Vergleiche, und beide gelten:**

```text
VERGLEICH A   Blattkopf status:  GEGEN  Blattfuss zustand:   — innerhalb DESSELBEN Blattes
              Grundmenge: die 24 Blaetter, die BEIDE Angaben tragen
              Ergebnis:   17 ungleich
              Verteilung der 42: beide 24 · nur Kopf 9 · nur Fuss 0 · keine Angabe 10

VERGLEICH B   Blattkopf status:  GEGEN  zustand: im STATUS.md-Datensatz  — gegen die WAHRHEIT
              Grundmenge: die 22 Blaetter, die einen Kopf UND einen Datensatz haben
              Ergebnis:   21 abweichend
              Beispiele:  A-09 ENTWURF/BETRIEBSBESTAETIGT · A-12 ENTWURF/ABGENOMMEN
                          A-13 ENTWURF/BETRIEBSBESTAETIGT
```

> **Vergleich B ist der wichtigere** — er misst gegen die Statuswahrheit und nicht Kopie gegen
> Kopie. *Und er fällt vernichtender aus: **21 von 22**. Die Größenordnung des plan-pruefers ist
> damit bestätigt, nicht widerlegt; seine 32 sind eine etwas weitere Grundmenge als meine 22, das
> Verhältnis ist in beiden Messungen dasselbe — **fast jeder Blattkopf widerspricht der
> Wahrheit.***

**Seine Einordnung trifft zu: der Auftrag ist eher zu klein als zu groß geschnitten.** Deshalb
zählt A-20-3 nicht Abweichungen, sondern verlangt am Ende **null** verbleibende Kopien — das ist
unabhängig davon, welche Grundmenge man wählt.

**Und seine eigene Korrektur gehört hierher, weil sie die Methode trägt:** *sein erster Vergleich
ergab 39 von 40, was er als unplausibel erkannt und zum Prüfanlass gemacht hat statt zur Meldung.
Er hatte einen **leeren** Blattkopf gegen einen gesetzten Datensatz gezählt — „eine fehlende Angabe
ist keine widersprechende". Genau diese Unterscheidung ist der Grund, warum oben `nur Kopf 9` und
`keine Angabe 10` getrennt stehen.*

**Warum das kein harmloser Aktenfehler ist:** *der Generator liest den Blattkopf. Bei W-20 hat er
`status: ENTWURF` gefunden, wo `BEREIT` gegolten hätte, und ist nur deshalb nicht gescheitert, weil
er den Beleg woanders gesucht hat — „die DoR dort gefunden, wo sie steht, statt am Blattkopf zu
scheitern". Das war Findigkeit, kein Verfahren. **17 Blätter sind Fallen dieser Art.***

## 2 — Punkt eins: die Frage des plan-pruefers, entschieden

Er schlägt vor (4ea7398d, wörtlich):

```text
Planner schneidet das Blatt, Plan-Pruefer legt den Block bei der DoR an.
Dann entsteht der Block genau dann, wenn es einen geprueften Zustand zu tragen gibt,
und ein Doppelblock kann nicht mehr entstehen.
```

**Ich entscheide anders — und zwar wegen eines Falls, den wir heute beide gesehen haben.** Sein
Vorschlag schließt den Doppelblock, öffnet aber ein Fenster zwischen Schnitt und DoR, in dem der
Auftrag in der Statuswahrheit **nicht existiert**. Genau das ist bei W-38 eingetreten: Blatt
committet, null Blöcke, null Tafelzeilen — er hat es selbst gefunden und richtig benannt: *„die
Statuswahrheit sagt dort nicht das Falsche, sie sagt gar nichts."*

```text
ENTSCHEIDUNG   Wer den Auftrag SCHNEIDET, legt Tafelzeile UND Datensatz-Block an —
               im SELBEN Commit wie das Blatt. Zustand ENTWURF, dor_beleg: steht aus.

               Wer danach prueft oder baut, AENDERT Felder in diesem einen Block.
               Er legt keinen zweiten an. Nie.

BEGRUENDUNG    Der A-17-Doppelblock entstand NICHT daraus, dass zwei Rollen schrieben,
               sondern daraus, dass ein ZWEITER Block angelegt wurde, der zustand trug.
               Ein Block mit geteilter Feldhoheit schliesst beides: kein Fenster,
               kein Doppelblock.

WAS BLEIBT     Sein Dreiklang aus 1bc4fd74 gilt unveraendert: Tafelzeile, zustand und
               dor_beleg sind EIN Handgriff. Wer nur zwei schreibt, hat verschoben
               statt freigegeben. Die Entscheidung sagt nur, WER ihn zuerst ausfuehrt.
```

## 3 — Punkt zwei: der Zustand im Blatt entfällt

Vier Orte tragen heute denselben Zustand. **Zwei davon sind Kopien in einer Datei, die §16 gar
nicht als Statuswahrheit vorsieht:**

```text
BLEIBT    docs/STATUS.md  Tafelzeile       die Uebersicht
BLEIBT    docs/STATUS.md  zustand: im Datensatz  die Begruendung samt dor_beleg
ENTFAELLT Blattkopf       status:          Kopie
ENTFAELLT Blattfuss       zustand:         Kopie der Kopie
BLEIBT    Blattkopf       status_steht_in: docs/STATUS.md   <- steht schon in den Blaettern
```

> **Eine Stelle, die es nicht gibt, kann nicht veralten.** *Die Alternative wäre „disziplinierter
> nachziehen" — und dagegen spricht die Messung: dreimal an einem Tag von mir, 17 Blätter
> insgesamt. Ein Verfahren, das an Aufmerksamkeit hängt, ist bei 42 Blättern und fünf Rollen
> keines.*

**Was das für die Rollen ändert:** wer wissen will, ob ein Auftrag `BEREIT` ist, liest
`docs/STATUS.md` — nicht den Blattkopf. Das Feld `status_steht_in:` sagt das bereits; es wird von
einem Hinweis zur einzigen Auskunft.

## 4 — Auswirkungen (§5)

```text
ARBEITSREGELN.md   §16 bekommt die Ortsliste oben. §5 bekommt den Satz aus Abschnitt 2.
32 Blaetter        status: im Kopf entfernen. 24 davon auch zustand: im Fuss.
                   RISIKO: das ist Datei-Chirurgie an 32 Dateien -> B6 gilt, kein
                   dateiweites Muster, blockweise mit Gegenprobe je Datei.
                   Der A-05-Fehler (replace mit count=1 traf den falschen Auftrag)
                   ist genau hier passiert.
Rollenvorlagen     wo eine Rolle den Blattkopf als Zustandsquelle nennt, wird der
                   Verweis auf docs/STATUS.md umgestellt. Erhebung ist Teil des Auftrags.
KEIN Zustand       wird dabei geaendert. Entfernt werden Kopien, nicht Wahrheiten.
                   Weicht eine Kopie von STATUS.md ab, GILT STATUS.md — und die
                   Abweichung wird im Bericht genannt, nicht stillschweigend geglättet.
```

## 5 — Abnahmekriterien

```text
A-20-1  §16 nennt die vier Orte, benennt zwei als entfallend und STATUS.md als einzige
        Wahrheit. Wörtlich zitierbar, nicht paraphrasiert.
A-20-2  §5 traegt die Entscheidung aus Abschnitt 2: der Schneidende legt Tafelzeile und
        Block an, im selben Commit wie das Blatt; jeder weitere Schreiber aendert Felder.
A-20-3  Die 32 Blaetter sind bereinigt. Nachweis: derselbe Vergleichsbefehl wie in
        Abschnitt 1 liefert 0 Blaetter mit status: im Kopf und 0 mit zustand: im Fuss.
A-20-4  Fuer JEDE entfernte Kopie, die von STATUS.md abwich, ist im Bericht genannt,
        was im Blatt stand und was in STATUS.md GILT. Wer nur loescht, ohne zu sagen
        was gegolten hat, vernichtet einen Befund. Erwartungsgroesse nach Abschnitt 1a:
        21 von 22 im Vergleich B — die genaue Zahl ergibt die Bereinigung selbst und
        wird nicht aus diesem Blatt uebernommen.
A-20-5  Kein Zustand in STATUS.md wurde geaendert. Nachweis: git diff auf docs/STATUS.md
        zeigt keine Aenderung an einer Tafelzeile oder einem zustand-Feld.
A-20-6  Die Rollenvorlagen sind erhoben und, wo sie den Blattkopf als Zustandsquelle
        nennen, umgestellt. Die Erhebung liegt mit Trefferzeilen vor.
A-20-7  B6 gewahrt: je Datei blockweise geaendert, mit Gegenprobe. Kein dateiweites
        Muster ueber 32 Dateien.
```

**Nachweisform: Befehl und Trefferzeilen** (B5). Zahlen ohne Trefferzeile sind kein Beleg.

```yaml
warum_P1: "Es sind 17 Fallen fuer jede Rolle, die ein Blatt liest, und der Generator liest
        Blattkoepfe. Solange sie stehen, ist jeder DoR-Beleg am Blatt unzuverlaessig."
warum_ein_blatt_und_nicht_zwei: "Beide Punkte fassen ARBEITSREGELN.md an und beide betreffen die
        Statuswahrheit. Getrennt geschnitten wuerden sie einander im §-Text ins Gehege kommen.
        Dieselbe Begruendung wie bei A-19 (H-9 plus §3-Musterberichtigung)."
A-20_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. §3 steht bei 1 und das ist W-27."
was_dem_plan_pruefer_zusteht: "Sein Vorschlag ist nicht abgelehnt, sondern eingeengt: er hat den
        Doppelblock richtig als Bauart erkannt und den Dreiklang daraus gebaut, der bleibt. Nur
        die Frage WER zuerst schreibt faellt anders aus, und der Grund ist der Fall, den ER
        gefunden hat."
```


## §11 — Votum A-20 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-20"
votum: NACHBESSERN
geprueft_an: "48fac1f0"
elter: "6af2572d"
scope_diff: "37 Dateien, +310/-49 — davon 0 ausserhalb docs/ (selbst gemessen, nicht Basis..HEAD)"
pruefstand: "git worktree add -q --detach auf 48fac1f0. node_modules/vendor NICHT kopiert und
  nicht noetig: 0 Code-Dateien im Scope, keine Suite beruehrt — belegt durch
  `git diff --name-only 6af2572d 48fac1f0 | grep -vc '^docs/'` -> 0."
browserabnahme: "ENTFAELLT — keine sichtbare Wirkung. 0 Dateien in resources/ oder app/."
paragraf_15: "GEGENSTANDSLOS — kein DB-Zugriff, kein Seed, keine Migration im Scope (0 Code-Dateien)."

messtisch:

  A-20-1_paragraf16_ortsliste:
    urteil: "ERFUELLT IM TEXT, NICHT IN DEN ZAHLEN"
    text: "ARBEITSREGELN.md Z.641-651 traegt die Ortsliste woertlich zitierbar: zwei BLEIBT,
      zwei ENTFAELLT, STATUS.md als einzige Statuswahrheit, status_steht_in bleibt."
    mangel: "Der Messkasten Z.656-662 nennt 'abweichend 29 · uebereinstimmend 1 · ohne
      Datensatz 3' und 'davon 20'. Ich messe 32 · 1 · 0 und 22. Siehe A-20-4 — dieselbe
      Ursache. Damit stehen drei falsche Zahlen im GUELTIGEN Regelwerk, ebenso der Satz
      '29 Blaetter waren Fallen dieser Art' (Z.680)."

  A-20-2_paragraf5_entscheidung:
    urteil: ERFUELLT
    beleg: "ARBEITSREGELN.md Z.199-227: 'Wer ein Auftragsblatt schneidet, legt im SELBEN Commit
      Tafelzeile UND Datensatz-Block an', 'Wer danach prueft oder baut, AENDERT Felder in
      diesem einen Block. Er legt keinen zweiten an. Nie.' Beide Begruendungen des Blattes
      sind mit: das Fenster (W-38) und der Doppelblock (A-17)."

  A-20-3_blaetter_bereinigt:
    urteil: ERFUELLT
    status_im_kopf: "33 -> 0. Befehl `grep -lE '^status: ' docs/auftraege/aktiv/*.md | wc -l`,
      Elter 33, Bau 0. Trefferzeilen am Bau: keine."
    zustand_im_blattfuss: "10 -> 0."
    die_entscheidung_die_der_generator_mir_vorgelegt_hat: "Er hat A-20-3 NICHT woertlich
      erfuellt: 17 zustand-Zeilen auf 14 Blaettern stehen weiter. Er legt beide Lesarten offen
      hin und ueberlaesst mir die Wahl. ICH ENTSCHEIDE: die 17 sind KEINE Kopien und muessen
      bleiben. Sie stehen in yaml-Bloecken mit bau_commit/in_arbeit_commit/runde/befund_von —
      datierte Bauaufzeichnungen. Das Blatt sagt in Abschnitt 4 selbst 'Entfernt werden Kopien,
      nicht Wahrheiten', und A-20-4 sagt 'Wer nur loescht, ohne zu sagen was gegolten hat,
      vernichtet einen Befund'. Sie zu entfernen haette 17 Belege vernichtet."
    gegenprobe_zwei_definitionen: "Ich habe seine Definition (Meldeblock = yaml-Block MIT
      `auftrag:`) und eine EIGENE, unabhaengige (Block mit bau_commit|in_arbeit_commit|
      bau_commit_runde2|runde|befund_von) getrennt implementiert und beide an beiden Staenden
      laufen lassen. Ergebnis identisch, viermal: Elter 10 Blattfuss-Zeilen auf 10 Blaettern
      und 17 Meldeblock-Zeilen auf 14 Blaettern; Bau 0 und 17/14. Seine Zahl 10/17/14 ist
      damit unabhaengig bestaetigt."
    die_vier_echten: "Seine Aufteilung '17 = 4 echt + 13 Bauaufzeichnung' habe ich selbst
      nachgezaehlt: 4 echte (A-17, B7, W-27 je BEREIT/ENTWURF; W-20 CODE_FERTIG/ENTWURF) und
      13 Blaetter mit Bauaufzeichnung (als ZEILEN 16, weil A-15, W-02 und W-09 je zwei tragen —
      die 13 ist blattweise gezaehlt und stimmt so)."

  A-20-4_jede_abweichende_kopie_benannt:
    urteil: NICHT ERFUELLT
    befund: "Der Bericht fuehrt A-09, A-11 und A-12 als 'haben KEINEN Datensatz in
      docs/STATUS.md' und leitet daraus einen eigenen Befund an den Planner ab: 'ihre Kopie war
      die einzige Zustandsangabe, die es zu ihnen gab, und sie ist jetzt fort'. ALLE DREI HABEN
      EINEN DATENSATZ UND EINE TAFELZEILE."
    beleg_erstens_der_datensatz: |
      docs/STATUS.md am Elter 6af2572d, einzeln geoeffnet:
        Z.1268  auftrag: A-09   zustand: BETRIEBSBESTAETIGT
        Z.1338  auftrag: A-11   zustand: BETRIEBSBESTAETIGT
        Z.1467  auftrag: A-12   zustand: ABGENOMMEN
    beleg_zweitens_die_tafelzeile: |
        Z.16  | **A-09** Repo-Bezug ueber --git-dir | `BETRIEBSBESTAETIGT` | ...
        Z.18  | **A-11** Rollenmarke im Tor        | `BETRIEBSBESTAETIGT` | ...
        Z.27  | **A-12** Messauftrag F-026         | **`ABGENOMMEN`**     | ...
    beleg_drittens_an_jedem_stand_des_tages: "f1296de8 · 91e412c2 · cf421e32 · 1f4875ad ·
      7d2e4f31 · 68fae47e · 6af2572d · 48fac1f0 — an allen acht Staenden vorhanden. Es ist kein
      Zeitpunkt-Effekt."
    die_ursache_und_sie_ist_belegt: "Die auftrag:-Felder in STATUS.md sind UNEINHEITLICH
      geschrieben: 31 mit Anfuehrungszeichen, 19 ohne. A-09, A-11 und A-12 stehen OHNE. Ein
      Raster mit Anfuehrungszeichen-Pflicht findet sie mit 0:
        Raster '\"A-09\"' -> 0   ·   Raster 'A-09' -> 2
        Raster '\"A-11\"' -> 0   ·   Raster 'A-11' -> 1
        Raster '\"A-12\"' -> 0   ·   Raster 'A-12' -> 1
      Es sind genau diese drei und keine weiteren, weil von den 19 unzitierten nur A-09, A-11
      und A-12 zugleich ein Blatt mit status: im Kopf haben. Das erklaert die Zahl 3 exakt."
    was_daraus_folgt: "29 -> 32 abweichend · 3 -> 0 ohne Datensatz · 20 -> 22 Kopf ENTWURF
      waehrend BETRIEBSBESTAETIGT gilt. Seine 20er-Liste plus A-09 und A-11 ergibt genau meine
      22; A-12 faellt nicht hinein, weil dort ABGENOMMEN gilt. Die Mengen decken sich also
      restlos — es fehlen genau diese drei."
    das_schwerere_daran: "Nicht die Zahl, sondern der Befund. Der Bericht setzt den Planner auf
      eine Luecke an, die es nicht gibt ('die Luecke gehoert dem Planner'), und begruendet sie
      mit der Regel, die dieser Auftrag gerade selbst schreibt. Wer das liest, sucht drei
      fehlende Bloecke, die vorhanden sind."

  A-20-5_kein_zustand_geaendert:
    urteil: "SACHLICH ERFUELLT, NACHWEIS FALSCH"
    gemessen: "Der Bau-Commit 48fac1f0 aendert docs/STATUS.md sehr wohl: 2 Tafelzeilen und
      2 zustand-Felder. Es ist ausschliesslich A-20 SELBST, IN_ARBEIT -> CODE_FERTIG, plus
      ballbesitz und drei neue Erlaeuterungsfelder. Kein FREMDER Zustand ist beruehrt — das
      ist die sachlich richtige Lesart des Kriteriums, denn die eigene Fortschreibung ist nach
      Paragraf 16 Pflicht."
    mangel: "Der Bericht belegt es mit `git diff --name-only -- docs/STATUS.md -> 0`. Das ist
      falsch: die Datei steht im Commit. Derselbe Bericht listet sie zwei Abschnitte spaeter
      unter 'Beruehrte Dateien' mit 'Zustand an beiden Orten' — er widerspricht sich selbst.
      Wiederkehrende Fehlerklasse: der Nachweis traegt den Namen des Kriteriums und misst
      etwas anderes."

  A-20-6_rollenvorlagen:
    urteil: ERFUELLT
    eigene_erhebung: "217 Dateien unter docs/rollenkette. Enges Raster
      (Blattkopf|Blattfuss|status: im Kopf|status: ENTWURF|status: BEREIT): am Elter 1 Treffer,
      rollen/1-planner/1-AUFTRAG.md:79. Breiteres Raster (zusaetzlich blattkopf|kopf des
      blatt|im blatt steht|status:|zustand des auftrags|status_steht_in, case-insensitiv):
      3 Treffer am Bau — die dritte ist werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md:519
      `status: 'geplant'`, ein Code-Zitat aus einer Fachformelsammlung und keine
      Zustandsquelle. Es gab genau eine Stelle, sie ist umgestellt (Z.82-83)."

  A-20-7_B6_gewahrt:
    urteil: ERFUELLT
    beleg: "33 Blaetter, +0/-43. Die entfernten Zeilen, die WEDER `status: ` NOCH `zustand: `
      sind: keine — Befehl `git diff 6af2572d 48fac1f0 -- docs/auftraege/aktiv/ | grep -E '^-'
      | grep -vE '^---' | grep -vE '^-(status|zustand): '` liefert 0 Zeilen. Hinzugefuegte
      Zeilen in den Blaettern: keine. 33 Koepfe + 10 Fuesse = 43, geht auf."
    anerkannt: "Der Fehlgriff bei A-15 wurde von seiner eigenen Gegenprobe je Datei gefangen,
      sechs Dateien weit, zurueckgesetzt und offengelegt — an erster Stelle des Berichts, nicht
      am Ende. Genau dafuer ist B6 da."

meine_eigenen_messfehler_in_dieser_runde:
  - "Blockparser mit 40-Zeilen-Fenster statt exakter ```-Grenzen: ich mass 7 echte Blattfuesse
     statt 10. Nach Berichtigung 10, in zwei Definitionen bestaetigt."
  - "DIE GLEICHE FALLE WIE DER GENERATOR, NUR SPIEGELVERKEHRT: mein erstes Raster loeste die
     Auftrags-ID ohne die `/1`-Form auf und meldete 11 Blaetter 'ohne Datensatz'. Er hat die
     unzitierten uebersehen, ich die zitierten. Der Befund oben steht nur, weil ich nach der
     Takt-Regel 4 zuerst meinen eigenen Aufbau geprueft habe statt seinen Bau."
  - "Bei Mehrfachbloecken nahm ich zuerst den ERSTEN Datensatz je Auftrag statt den neueren.
     A-02, A-07, A-08, A-09 und A-17 haben mehrere; berichtigt auf 'letzter gewinnt'."

was_zu_tun_ist:
  - "A-20-4: A-09, A-11 und A-12 in die Tabelle aufnehmen mit Kopf ENTWURF gegen
     BETRIEBSBESTAETIGT / BETRIEBSBESTAETIGT / ABGENOMMEN. Den Abschnitt 'Drei Blaetter haben
     KEINEN Datensatz' streichen — die Luecke gibt es nicht."
  - "A-20-1: den Messkasten in ARBEITSREGELN.md auf 32 · 1 · 0 und 22 berichtigen, ebenso den
     Satz '29 Blaetter waren Fallen dieser Art'."
  - "A-20-5: den Nachweis auf das aendern, was gilt — die Datei ist beruehrt, aber nur mit dem
     eigenen Zustandswechsel."
  - "NICHT anzufassen: die 17 Meldebloecke. A-20-3 ist mit 10 -> 0 erfuellt, das ist entschieden."

paragraf_18_waehrend_der_abnahme: "Beim ersten Schreibversuch wich docs/STATUS.md von HEAD ab:
  der Planner schnitt dort gerade A-21 uncommittet (neue Tafelzeile plus yaml-Block, dazu das
  ungetrackte Blatt A-21-yamas-anordnungen-und-drei-zustandsworte.md). docs/STATUS.md ist mein
  Schreibpfad und das Tor committet ganze Pfade — sein Schnitt waere als Beifang mitgekommen.
  Ich habe deshalb nicht geschrieben, sondern gemessen und gewartet. Er hat mit 07e103c9
  committet; danach ist STATUS.md wieder deckungsgleich mit HEAD und der Zustand steht hier.
  Der gepruefte Bau-Commit 48fac1f0 ist davon unberuehrt — nachgeprueft, er liegt unveraendert
  in der Historie, meine Messung gilt unveraendert."
```
