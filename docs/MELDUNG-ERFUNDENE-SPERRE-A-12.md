# Meldung — vier meiner Blätter behaupten eine §3-Sperre, die es nicht gibt

```yaml
art: "Selbstmeldung des Planners, ausgeloest durch die eigene §3-Belegmessung"
gemessen_am: "11.08."
basis_sha: 17460692
verursacher: planner
fehlerklasse: "Zuordnung annehmen statt messen — Unterform FALSCHER ZUSTAND (3 von 4)"
betroffen: "W-01N, W-05/1, W-21/1, W-22/1 — alle ENTWURF, keines abgenommen"
nicht_betroffen: "W-04/1, W-08/1, W-11/1 (schreiben korrekt ENTWURF), W-13/1 (nennt A-12 nicht)"
```

## Wie es aufgefallen ist — beim Ausführen des eigenen Kriteriums

Ich habe für den Fahrplan-Commit die zwei §3-Belegbefehle gefahren, die ich selbst in sieben Blätter
geschrieben habe. Sie lieferten **62** und **0**. *Beide Zahlen belegen nichts* — und beim Nachmessen,
**warum**, fiel etwas Größeres auf als der schlechte Befehl.

## Die Messung

```text
BEFEHL                                                              AUSGABE
grep -nE '^status:' docs/auftraege/aktiv/A-12-f026-ausfuehren.md    status: ENTWURF
grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md              0
Zustaende in der Statuswahrheit, gezaehlt:
  7 VEROEFFENTLICHT · 2 RELEASE_FREI · 2 CODE_FERTIG · 1 ERLEDIGT · 1 ABGENOMMEN · 1 VORLAGE
  -> KEIN EINZIGER Auftrag steht heute auf IN_ARBEIT
grep -nE '^§3: A-12 ist IN_ARBEIT' docs/auftraege/aktiv/*.md       VIER Treffer:
  W-01N:181   W-05/1:241   W-21/1:265   W-22/1:254
A-12 sagt selbst (Z.223): "A-12 geht ERST IN_ARBEIT, wenn kein anderer Auftrag IN_ARBEIT ist"
```

## Der Befund, in zwei Stufen

**Stufe 1 — die falsche Tatsache.** Vier Blätter schreiben `A-12 IN_ARBEIT`. A-12 steht auf
`ENTWURF`, und der Auftrag sagt es in seinem eigenen Schlussblock. *Das allein wäre eine falsche
Zeile in einer Konfliktprüfung.*

**Stufe 2 — die erfundene Sperre, und die ist schwerer.** Aus der falschen Tatsache folgt in allen
vier Blättern derselbe Satz:

> *„§3: A-12 ist `IN_ARBEIT`. W-xx/1 geht **NICHT** vor dessen Abschluss in `IN_ARBEIT`."*

**Diese Sperre existiert nicht.** §3 (ARBEITSREGELN Z.85) lautet: *„Es darf gleichzeitig höchstens
einen Auftrag im Zustand `IN_ARBEIT` geben."* Heute sind es **null**. Also steht §3 **keinem** der
sieben Blätter im Weg — jedes darf sofort in `IN_ARBEIT`, sobald sein DoR durch ist.

> **Ich habe eine planerische Empfehlung als Regel getarnt.** *Es gibt einen guten Grund, A-12
> vorzuziehen: F-026 ist 🟡 und damit die Sperre vor der Dachkonstruktion, und W-07/W-08 hängen
> fachlich daran. **Das ist Vorrang, nicht Verbot.*** Der Unterschied ist praktisch: eine §3-Sperre
> **darf** niemand übergehen; einen planerischen Vorrang **kann** der Plan-Prüfer oder Yama anders
> entscheiden. **Indem ich das eine als das andere geschrieben habe, habe ich mir eine Entscheidung
> genommen, die nicht meine ist — und Klasse A ohne Regelgrund blockiert.**

## Die Bauart des Fehlers — sie ist mechanisch, und das ist die Nachricht

```text
W-04/1   10.08.   "A-12  ENTWURF"    RICHTIG
W-08/1   10.08.   "A-12  ENTWURF"    RICHTIG
W-11/1   10.08.   "A-12  ENTWURF"    RICHTIG
W-13/1   10.08.   A-12 nicht genannt  —
W-01N    10.08.   "A-12  IN_ARBEIT"  FALSCH   <- ab hier kopiert
W-05/1   11.08.   "A-12  IN_ARBEIT"  FALSCH
W-21/1   11.08.   "A-12  IN_ARBEIT"  FALSCH
W-22/1   11.08.   "A-12  IN_ARBEIT"  FALSCH
```

**Die Serie war zuerst richtig und wurde mit jeder Kopie falscher.** *Der Mechanismus: ich habe den
Konfliktprüfungs-Block eines Vorgängerblattes übernommen — sinnvoll, weil Struktur und Pfade gleich
bleiben — **und dabei den Zustand mitkopiert. Der Zustand ist aber das Einzige in einer
Konfliktprüfung, das sich zwischen zwei Blättern ändert.** Pfade driften nicht, Zustände immer.*

**Warum das besser ist als die 1689-Fehlzahl:** Dort war es Gedächtnis, hier ist es **Kopie** — und
eine Kopie ist mechanisch erkennbar. *Ein Blatt, das einen Fremdzustand **behauptet**, statt ihn mit
dem Befehl zu **belegen**, der ihn liest, ist maschinell auffindbar.* **Das ist ein
Barrieren-Kandidat und kein Appell.**

## Auch gefunden: die zwei §3-Belegbefehle in meinen Blättern sind untauglich

```text
grep -cE 'IN_ARBEIT' docs/STATUS.md                        -> 62, davon 0 in Zustandsform
                                                              (alle 62 sind Prosa: Regelzitate,
                                                              Rueckblicke, Erklaerungen)
grep -cE '^\|.*IN_ARBEIT' docs/auftraege/AUFTRAGSTAFEL.md  -> 0, und AUFTRAGSTAFEL.md traegt
                                                              ueberhaupt 0 Auftragszeilen
                                                              (grep -cE '^\| (A|W)-[0-9]' = 0)
```

**Der zweite Ort ist leer.** *Die Auftragstafel führt keine Auftragszeilen mehr — sie ist auf
Regelwerk-Ebenen-Tabellen umgebaut, die aktiven Zeilen liegen in `AUFTRAGSTAFEL-ARCHIV.md` und
`AUFTRAGSTAFEL-HISTORIE-2026-07.md`.* **Damit ist die „je Ort einer"-Belegpflicht aus E2 an einem
Ort nicht erfüllbar** — nicht weil der Bauende nachlässig wäre, sondern weil der Ort den Zustand
nicht mehr trägt.

**Der taugliche Befehl, selbst gemessen:**

```text
grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md    -> 0   (Spalte 2, Zustandsform)
                                                                 bindet an die ZEILENFORM, nicht
                                                                 an das Wort — P02-Lehre
```

> *Das ist wörtlich der Fehler, den ich im Fahrplan selbst notiert habe: **„Ein Zählbefehl über eine
> Tabelle muss an die Zeilenform gebunden sein, nicht an das Wort."** Ich habe die Lehre
> aufgeschrieben und im Belegbefehl nicht angewendet.*

## Was ich geändert habe, und was ausdrücklich nicht

```text
GEAENDERT   in W-01N, W-05/1, W-21/1, W-22/1 (alle ENTWURF, keines abgenommen):
              die Zustandszeile         "A-12  IN_ARBEIT"  ->  "A-12  ENTWURF (gemessen)"
              die Schluss-Sperrzeile    Sperre  ->  gemessene Lage + Vorrang als EMPFEHLUNG
            Korrektur VOR dem DoR, mit dieser Meldung daneben — nicht still.
NICHT       W-04/1, W-08/1, W-11/1: sie sind richtig. Nicht angefasst.
GEAENDERT   Die §3-Belegkriterien (W-xx/1-11) bleiben WORTGLEICH stehen. Sie kommen aus
            E2 und sind vom Release-Pruefer in Yamas Namen angenommen (b9dc3c35) —
            ein Planner aendert kein angenommenes Prozesskriterium, um sich einen
            untauglichen Befehl zu ersparen. Der Befund gegen den ZWEITEN ORT gehoert
            als eigener Punkt an den Plan-Pruefer, nicht in meine Feder.
NICHT       Kein Statusfeld. STATUS.md unberuehrt.
```

## Was daraus für die Reihenfolge folgt — und wer sie entscheidet

```text
GEMESSENE LAGE   0 Auftraege auf IN_ARBEIT. §3 sperrt NICHTS.
                 Sieben Blaetter sind geschnitten und warten auf DoR.
MEINE EMPFEHLUNG A-12 zuerst, weil F-026 gelb ist und W-07/W-08 fachlich daran haengen.
                 Danach Runde 1 (W-04, W-11, W-13), dann Runde 2 (W-05, W-21, W-22).
NICHT MEINE      Ob so gebaut wird. Der Plan-Pruefer fuehrt die DoR, und §3 laesst
ENTSCHEIDUNG     jeden dieser Auftraege einzeln zu — es ist kein Verbot, es ist eine
                 Empfehlung, und ich sage es diesmal als solche.
```

```yaml
offen_an_plan_pruefer: "der zweite §3-Belegort traegt den Zustand nicht mehr —
                        AUFTRAGSTAFEL.md hat 0 Auftragszeilen. E2s 'je Ort einer' ist
                        dort nicht erfuellbar. Braucht eine Entscheidung, kein Planner-Patch."
barrieren_kandidat: "ein Blatt darf einen FREMDZUSTAND nicht behaupten, sondern muss ihn
                     mit dem lesenden Befehl belegen — Konfliktpruefungs-Kopien sind
                     maschinell erkennbar"
kern: "Pfade driften nicht, Zustaende immer. Wer eine Konfliktpruefung kopiert, kopiert
       genau das Feld mit, das er neu messen muesste."
```
