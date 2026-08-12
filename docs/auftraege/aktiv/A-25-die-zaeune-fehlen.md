# A-25 — Sieben Datensätze in zwei Blöcken. Eine Prüfrolle misst seit Runden mit Glück

```yaml
auftrag: "A-25"
titel: "Zwei verschmolzene yaml-Bereiche in docs/STATUS.md — jeder Datensatz bekommt seinen eigenen Zaun"
art: "BAU — Zäune setzen. KEIN Text wird geändert, KEIN Wert, KEIN Datensatz entfernt."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: fc0abdd5
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
anlass: "Der Evaluator hat es beim A-24-Claim gemessen und ausdrücklich dem Planner übergeben
         (f017b6f9): 'Der Befund selbst gehoert nicht mir sondern dem Planner, und er ist hier belegt
         statt vermutet.' Vier der fünf Datensätze im größeren Bereich sind MEINE Einfügungen von heute."
grundlage: "docs/STATUS.md, zwei Bereiche selbst gemessen · A-22 als betriebsbestätigtes Vorbild für
            Skript-mit-Gegenprobe · f017b6f9 als Herkunft"
```

## 1 — Der tragende Punkt: eine Prüfrolle misst seit Runden mit Glück

**Wörtlich aus `f017b6f9`, und es ist eine Aussage über eine Prüfmethode:**

```text
„mein Takt-Scan liest seit Runden das LETZTE zustand-Feld eines Bereichs statt das
 des gesuchten Auftrags, und dass es bisher stimmte war GLUECK IN DER REIHENFOLGE."
```

> **Das ist der Grund für P1.** *Ein Takt-Scan, der den Zustand des falschen Auftrags liest, meldet
> Widersprüche, die es nicht gibt — und, gefährlicher, **er verschweigt Widersprüche, die es gibt.**
> Konkret meldete er für A-24 „Tafelzeile `CODE_FERTIG`, Datensatz `BETRIEBSBESTAETIGT`", und **beide
> sagen `CODE_FERTIG`**. Beim nächsten Mal kann es umgekehrt ausfallen: **zwei Orte widersprechen sich,
> und der Scan sagt nichts.** Das ist die A-20-Klasse, aber eine Ebene tiefer — nicht der Zustand ist
> falsch, sondern der Weg, ihn zu lesen.*

## 2 — Der Befund, selbst gemessen und CommonMark-korrekt

```text
ZWEI Bereiche tragen mehr als einen Datensatz:

  Zeile 1243-1315   72 Zeilen · 2 Datensaetze:  A-08 · A-09
  Zeile 7525-8084  559 Zeilen · 5 Datensaetze:  W-06 · W-31 · A-24 · A-23 · A-22

Datensaetze ausserhalb jedes Blocks: 0
```

**ZWEITE FORM DERSELBEN KLASSE, nachgetragen 13.08.** *— sie hat mich einen ganzen Tag gekostet:*

```text
Zeile 5431  ein ```yaml-Block, der mit `ballbesitz: planner` BEGINNT
            und KEIN auftrag:-Feld traegt.
            Inhalt: der Fuss der 'VORLAGE AN DEN PLANNER' des Release-Pruefers
            vom 12.08. (Ueberschrift Zeile 5355).
```

> **Ein Ball, den kein Zähler findet.** *Die Vorlage lag seit dem 12.08. mit `ballbesitz: planner` in der
> Statuswahrheit, und ich habe sie nicht gefunden — **jeder Zähler, der Bälle über Auftragskennungen
> sucht, sieht sie nicht.** Sie tauchte nur als nackte Zahl in der Wache auf („Bälle beim Planner: 1"),
> und ich habe die Zahl zweimal den historischen Prüfaufzeichnungen zugeschrieben, bis ich den Block
> geöffnet habe.*

**Für A-25 folgt daraus ein zweites Kriterium** — *nicht nur „ein Datensatz je Block", sondern auch
**„jeder Block, der Zustandsfelder trägt, nennt seinen Auftrag".***

> **NACHTRAG nach dem Bau (`83ad35e1`): mein Kriterium nannte EINEN Fall, gemessen sind es FÜNF.** *Der
> Generator hat vier weitere Blöcke mit `ballbesitz`-Feld ohne Kennung gefunden — Befundblöcke des
> Evaluators bei `3418`, `3596`, `4529` und `4673` — **und alle fünf behandelt, statt sich auf meinen
> einen Beleg zu beschränken.** Er hat den Kriterienwortlaut richtig gelesen („jeder Block, der
> Zustandsfelder trägt") und die Differenz **gemeldet statt stillschweigend geschlossen.***

> **Selbst nachgemessen am veröffentlichten Stand: 0 Blöcke mit Zustandsfeld ohne Kennung.** *Sein Weg
> trägt, und die Form ist richtig gewählt: **ein `vorgang:`-Feld mit der Überschrift des Abschnitts** —
> keine erfundene Auftragsnummer, denn „eine Vorlage ist kein Auftrag und ein Befund auch nicht".*

> **Und es ist derselbe Fehler an mir, den ich heute mehrfach an Blättern hatte:** *ein Kriterium nennt
> **einen** Beleg, und der Bauende findet fünf Fälle. **Diesmal hat es nichts gekostet**, weil das
> Kriterium den Fall allgemein formulierte und nur der Beleg einzeln war — genau der Unterschied, den
> W-36-5 mich gelehrt hat: **die Klasse ins Kriterium, die Zahl in den Bericht.***

**Und mein erster Zähler hätte den Befund halbiert** — *das gehört hierher, weil es die Fangprobe
bestimmt:*

```text
Mein erstes Muster zaehlte JEDEN ```-Zaun als Toggle.
  -> es meldete EINEN Bereich statt zwei und behauptete 35 Datensaetze „ausserhalb".
Nach CommonMark schliesst nur ein Zaun OHNE Info-String.
  -> ```yaml bei Zeile 1284 ist INHALT, kein Schliesser.
Korrekt gezaehlt: zwei Bereiche, 0 ausserhalb — deckungsgleich mit f017b6f9.
```

> **Ich habe die falsche Zahl nicht gemeldet, sondern das Muster gewechselt.** *Sie steht hier, weil ein
> Prüfmuster, das `​```yaml` als Schließer zählt, **„null Bereiche" melden und grün sein kann** —
> derselbe Mechanismus, der heute mehrfach zugeschlagen hat. **Das Kriterium muss diese Falle
> ausschließen, nicht nur das Ergebnis verlangen.***

## 3 — Wie es entstanden ist, und der größere Teil ist meiner

```text
Der 559-Zeilen-Bereich trug am Morgen EINEN Datensatz (A-22).
Ich habe heute VIER dazugehaengt — A-23, A-24, W-31, W-06 — und zwar mit
demselben Muster: den neuen Block VOR die Zeile `auftrag: "<vorheriger>"`
einfuegen. Das setzt ihn INNERHALB des bestehenden Zauns.
```

> **Vier Mal derselbe Griff, und kein Mal habe ich den Zaun mitgedacht.** *A-20-2 verlangt Tafelzeile
> UND Datensatz im selben Commit — **das habe ich erfüllt.** Was A-20-2 nicht sagt und was ich nicht
> geprüft habe: **dass der Datensatz auch ein eigener Block sein muss, um maschinell lesbar zu
> bleiben.** Genau das ist der Gegenstand von A-22, betriebsbestätigt heute — **ich habe seinen Zweck
> unterlaufen, während er in Kraft war.***

*Der Evaluator hat es bei der A-22-Abnahme schon gesehen und als Befund ohne Rot gemeldet, weil A-22-1
zustandsortgebunden misst. **Damals waren es zwei Datensätze, jetzt fünf** — der Bereich ist durch meine
Einfügungen gewachsen.*

## 4 — Scope

```text
A-25 IST   die ZAEUNE. Jeder Datensatz in docs/STATUS.md steht nach dem Bau in
           seinem EIGENEN ```yaml-Block. Betroffen sind die zwei gemessenen
           Bereiche mit zusammen SIEBEN Datensaetzen.

A-25 IST NICHT
           irgendein INHALT. Kein Wert, kein Feldname, kein Vermerktext wird
           geaendert; kein Datensatz entfernt, keiner zusammengefasst.
           die Reihenfolge der Datensaetze -> bleibt wie sie ist.
           der Takt-Scan des Evaluators -> seine Methode gehoert ihm; er hat sie
           in f017b6f9 selbst umgestellt (Messung ueber die auftrag-Zeile und die
           drei Zeilen darunter). Dieser Auftrag behebt die URSACHE, nicht sein
           Werkzeug.
           die Nebenlaeufigkeit an docs/STATUS.md -> Abschnitt 6 der Vorlage,
           Prozessentscheidung bei Yama. A-25 macht die Datei nicht kleiner.
```

## 5 — Abnahmekriterien

```text
A-25-1  (P1, TRAGEND) Nach dem Bau traegt KEIN yaml-Block mehr als einen
        Datensatz. Nachweis mit einem Muster, das CommonMark folgt: ein Zaun
        schliesst nur OHNE Info-String, ```yaml ist ein OEFFNER und niemals ein
        Schliesser.
        DIE FALLE STEHT IM KRITERIUM, weil sie mich selbst erwischt hat: ein
        Muster, das jeden ```-Zaun als Toggle zaehlt, meldete bei mir EINEN
        Bereich statt zwei und 35 Datensaetze 'ausserhalb'. Ein solches Muster
        kann nach dem Bau NULL melden und gruen sein, ohne dass etwas behoben ist.
        Der Bericht nennt das verwendete Muster im Wortlaut.
A-25-2  (P1, SCHUTZGRENZE) KEIN Inhalt geaendert. Nachweis vor dem Schreiben und
        danach, wie A-22 es gefahren hat: die Liste aller auftrag-Werte und die
        Liste aller zustand-Werte sind vorher und nachher ZEICHENGLEICH, sortiert
        verglichen; die ANZAHL der Datensaetze ist gleich. Weicht eines ab, bricht
        der Lauf ab statt zu schreiben.
        BEGRUENDUNG: docs/STATUS.md ist die EINE Statuswahrheit. Ein Lauf, der
        dort einen Vermerk verliert, loescht einen Beleg — und A-20-4 verlangt
        ausdruecklich, dass nichts geloescht wird, nur umbenannt oder umgestellt.
        NACHTRAG 13.08. — DIESES KRITERIUM IST AM BAU-COMMIT NICHT MEHR FAHRBAR,
        UND DIE URSACHE IST MEIN EIGENER BEIFANG. Der plan-pruefer hat es
        gemessen (c07fb129): der Bau steckt in c8dd6d49, und dieser Commit traegt
        AUSSER dem Bau einen neuen Auftrag — meinen. Deshalb sind die Listen
        NICHT zeichengleich: auftrag 69 auf 70, zustand 60 auf 61.
        WER DAS KRITERIUM STUR FAEHRT, MELDET ROT, obwohl nichts verloren ging.
        WAS STATTDESSEN GILT, und es ist der bessere Nachweis: nicht die ANZAHL
        vergleichen, sondern die MENGEN mit comm — NULL Werte verschwunden, und
        jeder neue einzeln benannt. Gemessen: null verschwunden, genau einer neu
        (auftrag W-05/1, zustand ENTWURF), und das ist der Auftrag des Planners
        im selben Commit. Kein Verlust.
        DIE IRONIE IST DIE LEHRE, und sie ist seine: A-25-6 wurde geschrieben, um
        genau das zu verhindern — der Schaden kam nicht durch das Skript, sondern
        durch einen FREMDEN Commit auf dieselbe Datei waehrend des Baus. Mein
        Commit war der fremde. Ein Kriterium, das 'vorher gegen nachher' am
        Bau-Commit messen will, setzt voraus, dass der Bau-Commit NUR den Bau
        traegt — und das kann in diesem Baum niemand zusagen.
A-25-3  (P1) Die SIEBEN Datensaetze sind namentlich im Bericht: A-08 und A-09 aus
        dem Bereich 1243-1315, sowie W-06, W-31, A-24, A-23 und A-22 aus 7525-8084.
        Je mit der Zeilennummer VOR dem Bau. Am Bau-Stand erheben — die Nummern
        wandern, weil fuenf Rollen in diese Datei schreiben.
A-25-4  Der Bau nennt die HERKUNFT der zwei Bereiche und unterscheidet sie, weil
        die Fehlerart verschieden ist: bei 1243 steht in Zeile 1284 ein ```yaml
        MITTEN im Block, wo ein Schliesser stehen muesste; bei 7525 ist der Zaun
        korrekt, aber fuenf Datensaetze liegen darin. Wer nur eine Form behebt,
        laesst die andere stehen.
A-25-5  Die Fangprobe wird GEFAHREN und belegt: einen der neuen Zaeune wieder
        entfernen und zeigen, dass A-25-1 rot wird. Nicht gefahren heisst
        'nicht gefahren' im Bericht, nicht Schweigen.
A-25-1b (P1) NACHGETRAGEN 13.08.: jeder yaml-Block, der ZUSTANDSFELDER traegt
        (zustand: oder ballbesitz:), nennt auch seinen auftrag: — oder er nennt
        ausdruecklich, zu welchem Vorgang er gehoert.
        DER BELEGTE FALL: Zeile 5431 traegt einen Block, der mit ballbesitz:
        planner BEGINNT und keine Auftragskennung hat. Es ist der Fuss der
        'VORLAGE AN DEN PLANNER' des Release-Pruefers vom 12.08. Der Ball lag
        einen Tag beim Planner und WAR UNSICHTBAR: jeder Zaehler, der Baelle
        ueber Auftragskennungen sucht, findet ihn nicht.
        Das ist nicht dieselbe Fehlerform wie A-25-1 (mehrere Datensaetze in
        einem Block), aber dieselbe Klasse: ein Zustandsfeld, das maschinell
        nicht zuordenbar ist. Wer nur die Zaeune setzt, laesst diesen Fall
        stehen.
        NICHT VERLANGT: eine Auftragsnummer zu erfinden. Eine Vorlage ist kein
        Auftrag. Es genuegt ein Feld, das den Vorgang benennt — der Bauende
        waehlt die Form, das Kriterium verlangt die Zuordenbarkeit.
A-25-6  NEBENLAEUFIGKEIT: der Lauf misst den HEAD vor und nach dem Schreiben und
        bricht ab, wenn er sich bewegt hat. Fuenf Rollen schreiben in diese Datei;
        ein Skript, das 559 Zeilen umschreibt, waehrend eine andere Rolle darin
        schreibt, erzeugt genau den Schaden, den es verhindern soll.
        Und es wird NUR docs/STATUS.md gestagt — kein fremdes Artefakt, das
        gerade ungespeichert im Baum liegt.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl an zwei Mustern** (Prüfung 7).

```yaml
warum_P1_obwohl_kein_wert_falsch_ist: "Weil eine PRUEFROLLE betroffen ist. Der Evaluator sagt es selbst:
        sein Takt-Scan liest seit Runden das letzte zustand-Feld eines Bereichs statt das des gesuchten
        Auftrags, und dass es bisher stimmte war Glueck in der Reihenfolge. Ein Scan, der Widersprueche
        meldet die es nicht gibt, ist aergerlich; einer der Widersprueche verschweigt die es gibt, ist
        gefaehrlich. Beides folgt aus derselben Ursache, und die Ursache ist behebbar."
mein_eigener_anteil_und_er_ist_der_groessere: "Der 559-Zeilen-Bereich trug am Morgen EINEN Datensatz. Ich
        habe heute vier dazugehaengt — A-23, A-24, W-31, W-06 — mit demselben Muster: den neuen Block vor
        die auftrag-Zeile des vorherigen einfuegen, also INNERHALB des bestehenden Zauns. Vier Mal
        derselbe Griff, kein Mal den Zaun mitgedacht. A-20-2 verlangt Tafelzeile UND Datensatz im selben
        Commit, das habe ich erfuellt; dass der Datensatz auch ein eigener BLOCK sein muss, um maschinell
        lesbar zu bleiben, sagt A-20-2 nicht — und ich habe damit den Zweck von A-22 unterlaufen,
        waehrend A-22 in Kraft war."
was_ich_daraus_fuer_meine_eigene_arbeit_mitnehme: "Mein Einfuegemuster war t[:i] + block + t[i:] mit i =
        Position der vorherigen auftrag-Zeile. Es ist bequem und es ist falsch, weil es die Zaunlage
        nicht kennt. Kuenftig: der Block wird VOR dem oeffnenden Zaun eingefuegt und bringt seinen
        eigenen mit. Das gehoert nicht in eine Pflichtpruefung — es ist ein Handgriff und keine
        Pruefung — aber es gehoert in diesen Fuss, damit der naechste Leser die Ursache kennt und nicht
        den vierten Fall auch noch baut."
die_falle_im_pruefmuster_ist_der_eigentliche_lehrsatz: "Mein erster Zaehler zaehlte jeden ```-Zaun als
        Toggle und meldete EINEN Bereich statt zwei plus 35 Datensaetze ausserhalb. Nach CommonMark
        schliesst nur ein Zaun ohne Info-String. Haette ich diese Zahl gemeldet, waere der Befund
        halbiert und ein zweiter erfunden gewesen. Und schlimmer: nach dem Bau kann genau dieses Muster
        NULL melden und gruen sein. Deshalb steht die Falle in A-25-1 und nicht nur das Ergebnis."
A_25_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
