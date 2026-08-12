# A-27 — E1 verlangt die Messung am Commit. Vierzig Datensätze sagen nicht, an welchem

```yaml
auftrag: "A-27"
titel: "Wer CODE_FERTIG schreibt, nennt den Bau-Commit in einem FELD — nicht nur in der Botschaft"
art: "BAU — fünfte Barriere im Tor, Bauform wie F-14, B5, B6 und A-26."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: 875d1da5
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-27 ist frei — 0 Treffer in docs/STATUS.md und im REGISTER, 0 Blätter."
anlass: "Der Plan-Prüfer hat es an den drei wartenden Aufträgen gemessen und ausdrücklich NICHT selbst
         nachgetragen (24a122e9): 'Ein Bau-SHA, den der Pruefer eintraegt, ist…' — die Meldung gehoert dem
         Bauenden. Ich habe die REICHWEITE gemessen, und sie ist größer als drei."
grundlage: "docs/STATUS.md, alle Datensätze selbst ausgezählt · ARBEITSREGELN.md:509 als E1-Verankerung
            · scripts/a26-ball-drift.sh als Bauform-Vorbild · 24a122e9 als Herkunft"
```

## 1 — Der tragende Punkt: eine Regel, die die Handlung verlangt und nicht ihre Auffindbarkeit

```text
ARBEITSREGELN.md:509  „Vor jeder CODE_FERTIG-Meldung wird JEDE beruehrte Datei
                       gegen den Commit geprueft" — das ist E1.
Was NICHT dasteht:     dass dieser Commit im DATENSATZ auffindbar sein muss.
```

> **Die Botschaften nennen ihn, die Statuswahrheit nicht** — *und nach §16 liest der Nächste den Block.
> **E1 bindet Aussagen über den Bau an den Commit und ist wertlos, wenn der Commit nicht auffindbar
> ist.***

**Der konkrete Schaden, vom Plan-Prüfer an A-23 gemessen:** *§12.4 verlangt, dass die Wieder-Abnahme
**alle** Kriterien fährt, E1 verlangt die Messung am Commit. `bau_sha` stand auf `3ad920b1` — dem Stand
**vor** der Nachbesserung; die Nachbesserung `9d800094` kommt im ganzen Block **null Mal** vor. **Wer
beide Regeln befolgt und den Commit aus dem Datensatz nimmt, misst am falschen Stand und meldet zu Recht
rot.***

## 2 — Die Reichweite, selbst gemessen — und sie ist größer als drei

```text
Datensaetze mit BAU-Zustand (CODE_FERTIG · ABGENOMMEN · BETRIEBSBESTAETIGT ·
                             RELEASE_FREI · NACHBESSERN):        57
  nennen einen Commit in einem FELD:                             17
  nennen KEINEN:                                                 40

Und die FORM ist uneinheitlich — 19 verschiedene Feldnamen fuer Commits:
  basis_sha 62x · release_sha 9x · pruef_sha 7x · bau_sha 6x
  dazu 15 Einzelfaelle: zum_bau_commit · bau_commit · commit · mess_sha ·
  inhalt_sha · abnahme_sha · abnahme_commit · buendel_sha · werkzeug_und_bericht_sha …
```

> **`basis_sha` ist etabliert und 62× belegt — die BASIS, also der Stand VOR dem Bau.** *Für den Stand
> **nach** dem Bau gibt es keinen verbindlichen Namen: `bau_sha` steht 6×, `bau_commit` einmal, und in
> vierzig Fällen steht nichts.*

**Und die drei Formen des Plan-Prüfer-Befunds sind drei verschiedene Fehler, keine Wiederholung:**

```text
A-23  das Feld zeigt auf den UEBERHOLTEN Stand   (bau_sha = alter Bau)
A-25  das Feld nennt ein ANDERES Artefakt        (sha-Feld = Bericht, nicht Bau)
A-26  das Feld FEHLT                             (Commit nur im Prueftext)
```

## 2a — DEFINITION, nachgetragen nach `e037ff7b`: was ein Bau-Commit-Feld IST

**Der Plan-Prüfer hat den Punkt getroffen, und er trägt vollständig:** *das Blatt definierte nirgends, was
ein „Bau-Commit-Feld" ist — die Zeichenfolge kam **genau einmal** vor, in A-27-1 selbst. **Davon hängt
zweierlei ab: die Barriere UND die Zahl.***

**Er hat drei vertretbare Lesarten gemessen, ich habe sie nachgemessen:**

```text
                                                        er        ich
weit    Feldname endet auf _sha ODER enthaelt commit    20/37     56/1
eng     bau_sha · bau_commit · pruef_sha · release_sha  18/39     18/39  ✓ gleich
enger   NUR bau_sha und bau_commit                       7/50      7/50  ✓ gleich
```

> **Zwei von drei Lesarten sind deckungsgleich, die dritte nicht — und die Abweichung ist der Beleg:**
> *meine „weite" Zählung nahm `basis_sha` mit, seine nicht. **`basis_sha` steht 62× und meint den Stand
> VOR dem Bau** — wer es mitzählt, macht fast jeden Datensatz grün. **Selbst innerhalb einer Lesart gibt
> es also zwei Auslegungen, und sie unterscheiden sich um 36 Fälle.** Ohne Prädikat ist keine Zahl
> reproduzierbar; meine ursprünglichen 17/40 stammen aus einer vierten, undokumentierten Variante.*

**DIE DEFINITION, und sie folgt dem ZWECK und nicht der Bequemlichkeit:**

```text
Ein Bau-Commit-Feld ist GENAU EINES VON ZWEIEN:  bau_sha  ·  bau_commit

Und warum kein weiteres — je mit dem Grund, nicht mit einer Aufzaehlung:
  basis_sha    der Stand VOR dem Bau. E1 misst den Bau, nicht seinen Elter.
               62x vorhanden; mitzuzaehlen hiesse, die Luecke wegzudefinieren.
  pruef_sha    der Stand, an dem GEPRUEFT wurde — ein anderer Vorgang.
  release_sha  der veroeffentlichte Stand, nach der Abnahme.
  mess_sha ·   einzelne Messungen und Abnahmen. Sie belegen etwas anderes.
  abnahme_sha
```

> **Damit ist der Befund GRÖSSER als ich geschrieben habe: 50 Altfälle, nicht 40.** *Das gehört
> hierher, weil ich die kleinere Zahl gemeldet habe — sie stammt aus einem Muster, das `pruef_sha`,
> `release_sha`, `mess_sha` und `abnahme_sha` mitzählte, also aus einer Lesart, die ich nirgends
> aufgeschrieben habe.*

**Und keine dieser Zahlen steht ab jetzt in einem Kriterium** — *das ist die Lehre aus W-36-5 und
W-37-5, hier zum dritten Mal: **die Klasse ins Kriterium, die Zahl in den Bericht.** Ein Kriterium mit
fester Zahl zwingt den ehrlich Messenden, sie zu verletzen oder zu fälschen.*

## 3 — Scope

```text
A-27 IST   eine BARRIERE im Tor, Bauform wie A-26: fuehrt ein Commit
           `zustand: CODE_FERTIG` in einen Datensatz ein, muss derselbe Block ein
           Feld mit dem Bau-Commit nennen — und der genannte Commit muss
           EXISTIEREN (git cat-file -e).
           Dazu die FESTLEGUNG des Feldnamens: `bau_sha` (6x belegt, damit die
           haeufigste vorhandene Form) — kein neuer Name.

A-27 IST NICHT
           die Archaeologie der vierzig Altfaelle. Ihr Bau-Commit steht in der
           Botschaft; ihn nachtraeglich zu recherchieren erzeugt mehr Fehler als es
           behebt, und ein falsch nachgetragener SHA ist schlimmer als ein
           fehlender. Sie werden GEZAEHLT und benannt, nicht gefuellt.
           die Umbenennung der 15 Einzelfall-Feldnamen. Das war A-22s Gegenstand,
           und die Vermerke sind Belege — A-20-4 verlangt umbenennen statt
           loeschen, nicht Vereinheitlichung um ihrer selbst willen.
           ein ABBRUCH. Sie WARNT, wie B5 und A-26 (A-26-5s Begruendung gilt
           genauso: ein Zustandswechsel kann bewusst zwischen zwei Commits liegen).
           das Nachtragen durch den PRUEFER. Der plan-pruefer hat es ausdruecklich
           nicht getan, und das war richtig: ein Bau-SHA, den der Pruefer eintraegt,
           ist keine Meldung des Bauenden mehr.
```

## 4 — Abnahmekriterien

```text
A-27-1  (P1, TRAGEND) Die Barriere meldet, wenn ein Commit `zustand: CODE_FERTIG`
        in einen Datensatz einfuehrt und derselbe Block WEDER bau_sha NOCH
        bau_commit nennt — genau diese zwei, nach der Definition in Abschnitt 2a.
        KEIN ANDERES FELD ZAEHLT, und der Grund steht dort je Feld: basis_sha ist
        der Stand VOR dem Bau, pruef_sha der Pruefstand, release_sha der
        veroeffentlichte Stand. Wer eines davon zulaesst, definiert die Luecke weg. Nachweis an den DREI echten Faellen aus 24a122e9: A-23, A-25 und
        A-26 — die Staende stehen in der Historie, also ist der Nachweis fahrbar.
        Am Bau-Stand die SHAs pruefen, nicht aus diesem Blatt uebernehmen.
A-27-2  (P1) Der genannte Commit muss EXISTIEREN: `git cat-file -e <sha>^{commit}`.
        Ein Feld mit einem Tippfehler ist schlimmer als ein fehlendes, weil es
        Auffindbarkeit BEHAUPTET.
A-27-3  (P1) WAS DIE BARRIERE NICHT FAENGT, steht im Bericht — und es ist der Fall,
        der den Anlass gab: A-23s Feld nannte einen EXISTIERENDEN, aber
        UEBERHOLTEN Commit. Beide Pruefungen aus A-27-1 und A-27-2 waeren gruen
        gewesen.
        KEINE Loesung dafuer verlangt: sie hiesse, den genannten Commit gegen den
        letzten zu vergleichen, der die Blaetter des Auftrags beruehrt hat — das ist
        eine Heuristik, und eine Barriere, die auf einer Heuristik rot meldet, wird
        nach A-03 abgeschaltet. Die Luecke wird BENANNT, nicht geschlossen.
A-27-4  (P1, WIRKSAMKEIT) Die Barriere ist an einem sauberen Stand STILL. Nachweis:
        ein Commit, der einen Datensatz MIT bau_sha auf CODE_FERTIG setzt, erzeugt
        keine Meldung. Eine Barriere, die immer warnt, ist nach A-03 in drei Tagen
        abgeschaltet — und dann ist der Zustand schlechter als vorher.
A-27-5  Nur die im Diff BERUEHRTEN Datensaetze werden geprueft, nicht alle 76. Das
        Tor laeuft bei jedem Commit (A-26-4, dieselbe Begruendung).
A-27-6  BERICHTIGT nach e037ff7b, und meine Zahl war zu klein. Die Altfaelle sind
        AM BAU-STAND zu zaehlen — nach dem Praedikat aus Abschnitt 2a — und im
        Bericht zu benennen, mit der ausdruecklichen Feststellung, dass sie NICHT
        gefuellt werden. Eine stille Auslassung waere ein Freibrief; eine gezaehlte
        ist eine Grenze.
        KEINE FESTE ZAHL IM KRITERIUM: mein erster Wortlaut sagte 'die VIERZIG
        Altfaelle'. Wer ehrlich zaehlt, bekommt je nach Lesart 37, 39 oder 50 und
        muesste das Kriterium verletzen oder eine falsche Zahl schreiben — Klasse
        W-36-5 und W-37-5, zum dritten Mal. Zur Einordnung, NICHT als Sollwert:
        nach der jetzt geltenden Definition sind es 50 von 57, gemessen am
        13.08. und mit dem plan-pruefer deckungsgleich.
A-27-7  Die Fangprobe wird GEFAHREN und belegt: das Feld aus einem Datensatz
        entfernen und zeigen, dass A-27-1 rot wird. Nicht gefahren heisst
        'nicht gefahren' im Bericht.
A-27-8  Die vier vorhandenen Barrieren bleiben unberuehrt — F-14, B5, B6 und die
        A-26-Ball-Drift. Gegenprobe per Diff.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl an zwei Mustern** (Prüfung 7).

```yaml
warum_P1: "Weil zwei Regeln zusammen ins Rote laufen, die einzeln richtig sind: §12.4 verlangt bei der
        Wieder-Abnahme ALLE Kriterien, E1 verlangt die Messung am Commit. Wer beide befolgt und den
        Commit aus dem Datensatz nimmt, misst am falschen Stand — und meldet zu Recht rot, obwohl der
        Bau stimmt. Das ist kein Formfehler, das ist ein falsches Urteil aus richtigen Regeln."
was_ich_gemessen_habe_und_wo_ich_zuerst_zu_grob_war: "Mein erster Zaehler fragte nur, wie viele
        Datensaetze ein Bau-Feld nennen: 7 von 76. Diese Zahl ist WERTLOS, weil bei einem ENTWURF das
        Fehlen richtig ist — ein Auftrag ohne Bau hat keinen Bau-Commit. Erst die Einschraenkung auf
        BAU-Zustaende gibt die Sache: 57 Datensaetze, 17 mit Feld, 40 ohne. Ich habe die grobe Zahl
        nicht gemeldet, sondern das Muster verschaerft — dieselbe Bewegung, die heute mehrfach der
        Unterschied zwischen Meldung und Befund war."
warum_bau_sha_und_kein_neuer_name: "bau_sha steht 6x und ist damit die haeufigste vorhandene Form fuer
        den Stand NACH dem Bau; basis_sha steht 62x und meint den Stand DAVOR. Einen neuen Namen zu
        erfinden hiesse, eine zwanzigste Feldform anzulegen — und die Uneinheitlichkeit ist Teil des
        Befunds, nicht seine Loesung."
die_luecke_die_ich_offen_lasse_und_warum: "A-23s Fall — ein EXISTIERENDER, aber UEBERHOLTER Commit im
        Feld — faengt diese Barriere NICHT. Ihn zu fangen hiesse, den genannten Commit gegen den letzten
        zu halten, der die Blaetter beruehrt hat; das ist eine Heuristik, und eine Barriere, die auf
        einer Heuristik rot meldet, ist nach A-03 in drei Tagen abgeschaltet. Ich benenne die Luecke in
        A-27-3, statt eine schwache Pruefung zu verlangen — dieselbe Entscheidung wie bei A-26s zweiter
        Klasse, wo ich ebenfalls keine tragfaehige Barriere hatte und es gesagt habe."
A_27_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
