# A-19 — H-9: ein Muster misst, woran es ansetzt. Und die §3-Schranke ist der erste Anwendungsfall

```yaml
auftrag: "A-19"
titel: "Neun Belege an einem Tag, vier Rollen: ein Pruefmuster setzt am TEXT an statt am GEGENSTAND"
art: "REGEL + Musterberichtigung. EIN Blatt, weil beide Teile dieselbe Datei anfassen und
      dieselbe Ursache haben — zwei Blaetter wuerden sich gegenseitig sperren."
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: c89e9096
prioritaet: P1
anlass: "Yamas Freigabe 12.08. auf meine Vorlage, mit der Bedingung 'wenn du sicher bist dass das
         die beste Loesung ist und wir damit Qualitaet verbessern und der Workflow effizienter wird'."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "neun Meldungen von vier Rollen am 12.08. · H-6 (grenzt ab) · B5/B5N · §3-Verankerung"
```

## 1 · Der Befund — erhoben, nicht empfunden

**Über alle Commits des 12.08. gemessen: Meldungen der Form „Muster war zu eng/zu weit" oder
„schlägt bei RICHTIGER Arbeit an" — neun, von vier Rollen unabhängig.**

| | Fall | Das Muster misst | Es sollte messen | gemeldet von |
|---|---|---|---|---|
| 1 | `B5_BELEGZEILE` | eine **Schreibweise** (`datei:zeile`) | ob überhaupt ein Beleg da ist | Evaluator, Release-Prüfer, Plan-Prüfer |
| 2 | Geheimnisprüfung | Token-**Namen** im Text | echte Geheimnisse | Release-Prüfer (`27c88c20`) |
| 3 | Berechtigungsregel `deny` | den ganzen **Befehlstext** | den Befehl | Release-Prüfer (`c89e9096`) |
| 4 | **§3-Tafelzeilenmuster** | die ganze **Zeile** | die Zustandsspalte | **Planner, an sich selbst** |
| 5 | `--diff-filter=D` | gelöschte Pfade | alle veralteten Stände | Generator |
| 6 | Platzhalterzählung `<…>` | eine Klammerform | ob ein Blatt gefüllt ist | Planner/Generator (W-07) |
| 7 | `[AW]-[0-9]+` | zwei Präfixe | alle Auftragszeilen | Generator (`c528161c`) |
| 8 | erstes `zustand:` je Block | das erste Feld | alle Felder im Block | Release-Prüfer (`c67c6ab3`) |
| 9 | grüne Plakette zählen | eine von zwei Antworten | beide Antworten | Generator (A-17) |

> **Das ist keine Sammlung von Einzelfehlern.** *Neun Vorkommen, vier Rollen, ein Tag — und in jedem
> Fall war das Muster **syntaktisch korrekt** und traf **genau, was dort stand**. Falsch war, **woran
> es ansetzte**.*

## 2 · Warum H-6 den Fall NICHT abdeckt — die Abgrenzung ist der Kern

```text
H-6  "Ein Wort ist kein Beleg; erst die Stelle ist einer"
     Faelle: 'material' traf jedes THREE.Material · 'GEG' traf "gegeben" · '< 1 mm²'
     -> FEHLTREFFER. Das Muster trifft, was es nicht meint.

H-9  Das Muster trifft RICHTIG — und misst die falsche Sache.
     Fall 4: die B7-Zeile steht auf ABGENOMMEN und enthaelt im Fliesstext den Satz
             "P2: nie auf `IN_ARBEIT` gesetzt". Der Treffer ist korrekt, die
             Zaehlung ist falsch, weil sie einen Befund UEBER einen Zustand als
             diesen Zustand nimmt.
```

**Die zwei Richtungen brauchen zwei Regeln:** *H-6 fragt „triffst du, was du meinst?" — H-9 fragt
**„setzt du an, wo die Sache steht?"** Ein Muster kann H-6 bestehen und an H-9 scheitern; Fall 4 ist
genau das.*

## 3 · DECISION — die Regel, ihr Wortlaut und ihre Prüfform

```text
H-9 KOMMT als neunte Hausregel in §18a, ANGEHAENGT hinter H-8 (ARBEITSREGELN.md:812).
    Wortlaut vom Generator, der ihn am 12.08. zweimal selbst formuliert hat:

      "Ein Muster, das eine Schreibweise voraussetzt, misst die Schreibweise
       und nicht die Sache."

    Und die PRUEFFORM, dreifach erprobt statt erfunden — die Kontrollprobe:

      "Findet der Befehl die Zeile, die ich mit eigenen Augen gelesen habe?
       Erst danach zaehlen."

KEINE ACHTE BARRIERE. Ausdruecklich, und mit Begruendung:
    B5, B6 und B7 stehen in DERSELBEN Datei, und am 12.08. wurde DREIMAL gemeldet,
    dass eine Warnung bei richtiger Arbeit weggeklickt wird. Eine Barriere gegen
    falsche Muster waere selbst ein Muster — und koennte denselben Fehler machen.
    Die Regel wirkt ueber die DoR, nicht ueber das Tor.

KEINE AENDERUNG an §§ 1-19 im Uebrigen. Die neun Faelle sind WERKZEUG-Fehler, keine
    Prozess-Fehler. Der Prozess hat Fall 4 in derselben Minute gefangen, weil §3 ZWEI
    Orte messen laesst und ihre Abweichung als Befund definiert — ohne den zweiten Ort
    haette der Planner "§3 belegt" gemeldet und einen abgenommenen Auftrag blockiert.
```

## 4 · Der zweite Teil — die §3-Schranke, und der Fehler ist meiner

```text
ARBEITSREGELN.md:103   grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT' docs/STATUS.md

Gemessen am 12.08., beide Fassungen gegen dieselbe Datei:
  mit  .*IN_ARBEIT   (heutige Fassung)   ->  1     FEHLALARM
  auf Spalte 2 begrenzt                  ->  0     richtig
  Zustandsfeld                           ->  0     richtig
```

**Der Fehltreffer ist die B7-Zeile** — sie steht auf `ABGENOMMEN` und enthält in der letzten Spalte
`P2: nie auf 'IN_ARBEIT' gesetzt`. **`.*IN_ARBEIT` reicht bis zum Zeilenende, über alle Spalten
hinweg.**

> **Und die Ironie gehört ins Blatt, weil sie die Lehre trägt:** *ich habe dieses Muster am 12.08.
> selbst verankert und dabei von `[AW]` auf `[A-Z]+` erweitert, **weil es zu eng war** — und dabei
> nicht bemerkt, dass sein rechtes Ende **zu weit** ist. Ich habe eine Seite geprüft und die andere
> nicht angesehen. **Das ist H-9 an der Regel, die H-9 verankern soll.***

## 5 · Abnahmekriterien

```text
A-19-1  (P1) H-9 steht in §18a, ANGEHAENGT hinter H-8, im Wortlaut aus Abschnitt 3 —
        Regel UND Pruefform. Rot vorher: grep auf 'misst die Schreibweise' in
        ARBEITSREGELN.md ergibt heute 0.

A-19-2  (P1) Die Abgrenzung zu H-6 steht IM Regeltext, nicht nur in diesem Blatt: zwei
        Richtungen, zwei Regeln, mit je einem Beispiel. Ohne sie wird H-9 als Dublette
        von H-6 gelesen und weggewunken — genau das Schicksal, das eine neunte Regel in
        einer Sammlung von acht droht.

A-19-3  (P1) Das §3-Muster in ARBEITSREGELN.md:103 wird auf die ZUSTANDSSPALTE begrenzt.
        Gegenprobe DREIFACH, alle drei Zahlen im Bericht:
          (a) die B7-Zeile (ABGENOMMEN, Fliesstext nennt IN_ARBEIT)  -> NICHT gezaehlt
          (b) eine echte IN_ARBEIT-Tafelzeile                        -> gezaehlt
          (c) das Zustandsfeld-Muster                                -> gleiche Zahl wie (b)
        Ohne (b) waere die Berichtigung eine Abschaltung: ein Muster das NIE zaehlt
        meldet immer frei, und das ist die gefaehrliche Richtung.

A-19-4  (P1) DER DOPPELORT BLEIBT. Beide Orte werden weiter gemessen und beide Zahlen
        genannt. Wer bei dieser Gelegenheit auf EINEN Ort verkuerzt, nimmt die Kontrolle
        heraus, die Fall 4 ueberhaupt sichtbar gemacht hat.

A-19-5  (must_preserve) H-1 bis H-8 zeichengleich. Die §3-Regel selbst ('hoechstens einen
        Auftrag im Zustand IN_ARBEIT') unveraendert — berichtigt wird die PRUEFMETHODE,
        nicht die Regel. Nachweis: git diff zeigt 0 geloeschte Zeilen im §3-Absatz und
        im §18a-Block ausser der EINEN Musterzeile 103.
        scripts/** unveraendert — dieser Auftrag faesst das Tor NICHT an.
        resources/** und app/** byte-identisch.

A-19-6  (P2) Die vier Auftragsblaetter, die das alte §3-Muster als Kriteriumstext tragen,
        werden GEMESSEN und im Bericht genannt — nicht geaendert. Wer sie anfasst,
        beruehrt fremde und teils abgeschlossene Blaetter; wer sie ignoriert, laesst das
        falsche Muster in Umlauf. Die Entscheidung darueber gehoert dem Planner und
        folgt NACH diesem Auftrag.

A-19-7  (P1, §3 wird BELEGT) Beide Orte nach der Methode aus §3, unmittelbar vor der
        ersten Aenderung, beide Zahlen genannt. Und zwar mit der BERICHTIGTEN Fassung,
        sobald sie steht — dieser Auftrag ist sein eigener erster Anwendungsfall.
```

## 6 · Warum das den Ablauf schneller macht und nicht langsamer

*Yamas Bedingung war ausdrücklich Qualität **und** Effizienz. Beides ist messbar:*

```text
KOSTEN heute   Fall 1 wurde DREIMAL gemeldet, bevor er ein Auftrag wurde — drei Rollen,
               drei Takte, dieselbe Arbeit. Fall 4 kostet bei JEDEM Rundgang jeder Rolle
               eine Nachpruefung, weil die Zahlen auseinandergehen. Fall 3 hat SIEBEN
               Veroeffentlichungsversuche ueber SECHS Takte gekostet, bevor die Ursache
               gefunden war.
NUTZEN         eine Frage in der DoR ("woran setzt dein Muster an") faengt den Fall VOR
               dem Bau. Das ist dieselbe Stelle, an der heute schon die Rot-Lage geprueft
               wird — kein neuer Schritt, eine Zeile mehr in einer vorhandenen Liste.
KEINE NEUE     kein Tor-Lauf, keine Warnung, kein zusaetzlicher Commit-Zwang.
BREMSE
```

> **Der Ehrlichkeit halber, was diese Regel NICHT kann:** *sie verhindert keinen falschen Ausdruck.
> Sie macht die Frage danach zur Pflicht. **Neun Fälle an einem Tag wurden alle erst nach dem
> Schaden gefunden — die Regel verschiebt den Fund nach vorn, sie ersetzt ihn nicht.***

## 7 · Rückweg & Entdeckung

```text
RUECKWEG      reiner Revert. Textzeilen in einer Regeldatei; kein Code, kein Datenpfad,
              keine Migration. git apply --check -R Exit 0.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier NICHT behauptet — heute
              stauen sich fuenf Releases an einer Berechtigungssperre (c89e9096), der
              Transport ist also NICHT selbstverstaendlich.
ENTDECKUNG    zwei Signale:
              (1) die zwei §3-Zahlen gehen auseinander, obwohl kein Auftrag laeuft
                  -> A-19-3 gebrochen, Richtung Fehlalarm
              (2) ein laufender Auftrag wird von der Tafelzeile NICHT gezaehlt
                  -> A-19-3(b) gebrochen, und das ist der gefaehrlichere Fall:
                     die Schranke meldet frei, waehrend gebaut wird.
```

## 8 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Tafelzeile, heutige Fassung   ->  1   (FEHLALARM: B7-Zeile, steht auf ABGENOMMEN)
Tafelzeile, auf Spalte 2      ->  0
Zustandsfeld                  ->  0
             -> kein Auftrag laeuft. Der Widerspruch IST der Gegenstand dieses Blattes.
Index leer · ARBEITSREGELN.md im Arbeitsbaum unveraendert
SPERRE, die ohne Zutun gilt: B5N und B7 haben ARBEITSREGELN.md ebenfalls im Scope. Beide
             sind gebaut (B5N CODE_FERTIG, B7 ABGENOMMEN) — laeuft eine Nachbesserung an
             einem von beiden, wartet A-19.
A-19 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
warum_EIN_blatt: "H-9 und die Musterberichtigung fassen BEIDE docs/ARBEITSREGELN.md an. Zwei
       Blaetter wuerden sich nach H-4 gegenseitig sperren und zwei DoR-Runden kosten — bei
       derselben Ursache und demselben Scope. Ein Blatt, zwei Teile, ein Scope."
warum_keine_barriere: "B5, B6, B7 in derselben Datei; dreimal gemeldet, dass eine falsch
       anschlagende Warnung weggeklickt wird. Eine Barriere gegen falsche Muster waere selbst
       ein Muster."
der_fehler_ist_meiner: "ich habe das §3-Muster am 12.08. verankert, die linke Seite erweitert
       und die rechte nicht angesehen. H-9 an der Regel, die H-9 verankern soll."
```
