# Befund — die Formelsammlung beschreibt dreimal einen Weg, den die Insel bewusst nicht geht

```yaml
art: "Befund des Planners, ausgeloest durch die Generator-Rueckgabe aus W-04"
gemessen_am: "11.08."
basis_sha: db9724be
anlass: "Der Generator meldet: W-04s Register-Formeln F-003 und F-031 stimmen nicht mit dem
         Code ueberein. Er aendert sie nicht — sie gehoeren dem Planner. Richtig so."
reichweite: "nicht W-04 allein. Drittes Vorkommen desselben Musters."
schreibsperre: "REGISTER.md ist NICHT angefasst — W-11 ist IN_ARBEIT und hat es im Scope (§3)."
```

## Die Rückgabe war richtig, und sie deckt zwei verschiedene Fehler auf

**Der Generator hat gemessen und gemeldet, statt zu korrigieren** — und das Register trägt seinen
Vermerk mit `⚠`. *Was er gefunden hat, sind aber **zwei verschiedene Dinge**, und sie brauchen
verschiedene Antworten.*

```text
W-04s Module gemessen (der Befund des Generators, von mir nachgemessen):
  oeffnungsBauarten.ts   Math. 0x     -> Katalog, keine Rechnung
  oeffnungsTypen.ts      Math. 0x     -> Katalog, keine Rechnung
Gegenprobe an allen anderen geschnittenen Werkzeugen:
  fangKern 9x · wallGeometry 15x · roomDetection 5x · masskette 9x ·
  sparrenBerechnung 6x · gaubeGeometrie 35x                -> alle rechnen
  => W-04 ist der EINZIGE Katalog-Fall. Der Befund ist spezifisch, nicht systematisch.
```

## Fehler 1 — F-003 steht am falschen ORT (leicht zu heilen)

```text
F-003  "Lotfusspunkt auf eine Strecke — Fang 'Lot', Oeffnung auf Wandachse einrasten"
GEBAUT ist sie: fangKern.ts, Export lotAufGerade()  -> gehoert zu W-01
IM REGISTER steht sie bei: W-01 ✓ · W-03 · W-04 ✗ · W-11 · W-13
```

> **Die Formel ist richtig und gebaut — nur nicht dort, wo das Register sie behauptet.** *Das
> Register hat nach **Thema** zugeordnet („eine Öffnung rastet auf der Wandachse ein — also F-003"),
> die Blätter beschreiben aber **Module**. Beide Sichtweisen sind legitim, sie passen nur nicht
> zusammen.* **Heilbar durch eine Registerzeile plus eine klare Regel, welche der beiden Sichten das
> Register führt.**

## Fehler 2 — F-031 beschreibt einen Weg, den die Insel BEWUSST nicht geht (das ist der wichtige)

```text
F-031  "Oeffnung ausschneiden (CSG-Differenz)"
       Formel:  Ergebnis = Wand \ Oeffnung   (boolesche Differenz)
IM REGISTER bei: W-04 und W-22

GEMESSEN, nur auf 'csg' ohne Beifang:
  renderers/three-d/segmentierung.ts:2
    "Hausplaner P1 — CSG-FREIE Wand-Segmentierung"
  segmentierung.ts:7
    "Zwischen-Quader (seitlich). KEINE CSG-BIBLIOTHEK, mm-exakt,
     UNIT-TESTBAR OHNE BROWSER."
  geometry/dachAusschnitt.ts:10
    "Stufe C (NICHT HIER): echte Polygonloch-/CSG-Operationen fuer Walm/Trapez/L-T-U"
  -> F-031 ist in der Insel NICHT gebaut, und zwar ABSICHTLICH.
     Statt Boolean-Differenz: Wand-Segmentierung durch Quader (W-02-Seite) und
     Dachausschnitt in u/v-Koordinaten mit Stufenmodell A/B/C (Dach-Seite).
```

> **Der Grund steht im Code und ist besser als die Formel: „unit-testbar ohne Browser".** *Eine
> CSG-Differenz ist ohne Render kaum prüfbar; ein Quader-Schnitt in mm ist es. **Die Insel hat die
> Formel nicht übersehen, sie hat sie abgelehnt und den Grund aufgeschrieben.***

## Das ist das dritte Vorkommen desselben Musters

```text
1  F-020  Straight Skeleton fuer beliebige Polygone
   Insel:  0 Treffer im ganzen Repo. Gebaut ist Parametergeometrie ueber eine Formliste.
   Quelle dieser Zeile: W-07s Blatt (F-020 gegen roof.anbau) — NICHT heute von mir
   nachgemessen; ich habe nur die 0 Treffer selbst gemessen.
2  F-026  Kantentopologie in sechs Schritten
   Insel/Fremdcode: die Kette laeuft nicht (0 Aufrufe aus dem Aufbau), sie speist eine
   Anzeige. Gebaut ist Parametergeometrie. HEUTE selbst gemessen, siehe FORMELSAMMLUNG.
3  F-031  CSG-Differenz
   Insel: bewusst CSG-frei, zwei eigene Wege, Grund benannt. HEUTE selbst gemessen.
```

**Und jedes Mal ist der Weg der Insel der PRÜFBARERE, mit benanntem Grund:**

```text
F-026-Familie   "kein 3D-Render verfuegbar"       -> Geometrie numerisch einfrieren
                                                     (dachVerschneidung als Regressionsschloss)
F-031           "unit-testbar ohne Browser"       -> CSG-frei, Quader statt Boolean
Dachformen      "statt still falsche Geometrie"   -> status 'geplant' als Ampel
W-22            "kein Render verfuegbar"          -> pruefeAufbau() rechnet nach
A-10            (der Auftrag selbst)              -> Melder am leeren Ergebnis
```

> **Fünf Mal derselbe Gedanke, unabhängig entstanden, viermal davon VOR dem Auftrag, der ihn zur
> Regel gemacht hat.** *Ich habe das in der Dachweg-Vorlage einen „Hausstil" genannt. **Nach dieser
> Messung ist es mehr: die Insel hat eine Baurichtlinie, die die Werkbank nicht kennt** — „baue
> nichts, was du ohne Browser nicht prüfen kannst; und wenn du es nicht kannst, sag es."*

## Was daraus folgt

**Die Formelsammlung stammt aus Yamas Prototypen (M-01…M-04). Die Insel ist unabhängig davon
gewachsen und hat an drei Stellen den besseren Weg gefunden.** *Das ist genau die Umkehrung, die in
`WERKBANK-ANSCHLUSS.md` schon für die **Werkzeuge** steht — „der Anschluss ist Code → Werkbank
eintragen, nicht umgekehrt". **Neu ist, dass es auch für die FORMELN gilt**, und dort wiegt es
schwerer: eine Formel im Register begründet Aufträge.*

```text
NICHT die Folgerung   "die Formelsammlung ist falsch". Sie ist es nicht — F-003 ist richtig
                      und gebaut, F-031 ist mathematisch korrekt, F-020 ist ein echtes
                      Verfahren. Falsch ist die ZUORDNUNG und der stille Anspruch, dass
                      eine Formel im Register auch der gegangene Weg ist.
DIE FOLGERUNG         Jede F-Zeile im Register braucht eine dritte Angabe: nicht nur
                      "welche Formel", sondern "gebaut in <Modul>" oder "NICHT gebaut,
                      Insel geht <Weg>, Grund <Zitat>". Ohne die dritte Angabe ist eine
                      F-Nummer im Register eine Absichtserklaerung, die wie ein Beleg aussieht.
```

## Mein Vorschlag für den nächsten Schritt

```text
JETZT NICHT   REGISTER.md anfassen. W-11 ist IN_ARBEIT und hat es im Scope.
              Gemessen: grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md -> 1 (W-11).
              Das ist genau die Konfliktpruefung, die ich in jedes Blatt schreibe —
              diesmal trifft sie mich.
DANACH        EIN Auftrag, klein, Spur A, nur Doku: die F-Spalte des Registers um die
              dritte Angabe erweitern, fuer die geschnittenen Werkzeuge. Er heilt
              W-04s Befund, N1 (F-004 in W-01s Zeile) und F-031 bei W-22 in einem Zug,
              statt drei Einzelkorrekturen.
UNBERUEHRT    Die Formeln selbst. Ich streiche keine F-Nummer und aendere keine Formel —
              nur ihre Zuordnung und ihren Belegstatus.
OFFEN AN      ob die Baurichtlinie der Insel ("nichts bauen, was ohne Browser nicht
YAMA          pruefbar ist; und wenn du nicht kannst, sag es") als Regel in die Werkbank
              gehoert. Fuenf unabhaengige Vorkommen sind ein Beleg, kein Zufall — aber
              eine Regel zu setzen ist nicht meine Entscheidung.
```

```yaml
fehlerklasse_1: "falscher Ort — F-003 ist gebaut, aber in W-01s Modul, nicht in W-04s"
fehlerklasse_2: "falscher Weg — F-031 ist nirgends gebaut, die Insel geht bewusst anders"
gewuerdigt: "der Generator hat gemessen, gemeldet und NICHT korrigiert. Haette er die
             Registerzeile still geaendert, waere aus dem Befund eine Reparatur geworden
             und das Muster (drittes Vorkommen) haette niemand gesehen."
kern: "eine F-Nummer im Register ohne Modulangabe ist eine Absichtserklaerung, die wie
       ein Beleg aussieht"
```

---

## ⚑ NACHTRAG 13.08. — der vorgeschlagene Auftrag hat sich selbst erledigt. Und F-004 war falsch

**Der Vorschlag oben lautete:** *„EIN Auftrag, klein, Spur A, nur Doku: die F-Spalte des Registers um
die dritte Angabe erweitern"* — mit der Sperre „JETZT NICHT, W-11 ist IN_ARBEIT".

**Die Sperre ist gefallen** *(W-11 steht heute auf `BETRIEBSBESTAETIGT`, das einzige `IN_ARBEIT` ist
A-32)*. **Bevor ich den Auftrag geschnitten habe, habe ich gemessen, was er noch zu tun hätte:**

```text
Registerzeilen mit F-/N-Nummer                24
  davon MIT dritter Angabe (Modul + Stelle)   13
  davon OHNE                                  11

DIE ELF, gegen den Auftragsstand gehalten:
  W-12 W-10 W-16 W-06 W-14 W-18   -> BEREIT, Blatt liegt beim Generator
  W-13 W-23 W-20                  -> BETRIEBSBESTAETIGT
  W-03 W-19                       -> KEIN Auftrag
```

> **Die sechs BEREIT-Zeilen lösen sich von selbst** — *ich habe in jedes der Ablesungsblätter
> geschrieben, dass die Formeln **am Code zu erheben** sind und die Registerzeile **geprüft statt
> übernommen** wird (`W-10-1-4`, `W-14-1-7`, `W-16-1-2`). **Die dritte Angabe entsteht also beim
> Schneiden** — genau so, wie sie bei den dreizehn entstanden ist, die sie schon tragen.*

**Und drei der elf tragen sie doch — mein Muster war zu streng.** *Es verlangte einen `.ts`-Dateinamen.
`W-13` führt **„keine ⓝ"**, `W-20` „heute keine benutzt", `W-23` einen Bereichsvermerk. **Das sind
dritte Angaben in Worten**, nur ohne Fundstelle. Die Klasse ist dieselbe, die mich heute mehrfach
erwischt hat: das Muster misst die Form und nicht die Sache.*

```text
ECHTE LUECKE: ZWEI Zeilen — W-03 und W-19, beide ohne Auftrag.
  W-03  F-003, F-004, F-030
        Heute eingeordnet: BAU mit ZWEI verschiedenen Fundamenten. Und F-004 ist
        jetzt gebaut (A-32, geometry/geradenGeometrie.ts) — die dritte Angabe
        entsteht mit dem W-03-Blatt.
  W-19  F-024
        Heute eingeordnet: ZUSTAENDIGKEITSFRAGE, nicht Bau. pvBelegung.ts:6 sagt
        'GRENZE: Ertrag/Verschattung/Strings bleiben der Fach-Engine
        (wberechnung) vorbehalten'. Die dritte Angabe waere hier genau der
        Grund-Zitat-Fall, den die Folgerung oben vorsieht.
```

> ***Ergebnis: ich schneide den Auftrag NICHT.*** *Zwei Zeilen, deren Blätter ohnehin kommen müssen, sind
> kein eigener Auftrag — das wäre ein Vorgang, der zwei Zellen füllt und zwei weitere Blätter davon
> abhängig macht. **Der Vorschlag war richtig und ist durch die Blätter eingelöst worden**, nicht durch
> einen Doku-Auftrag. Er gilt damit als erledigt und liegt nicht mehr offen.*

### Der Nachtrag, der die Folgerung oben SCHÄRFER macht: F-004 war falsch

*Die Folgerung sagte, ohne dritte Angabe sei eine F-Nummer „eine Absichtserklärung, die wie ein Beleg
aussieht". **Am 13.08. hat sich gezeigt, dass es schlimmer ist:*

```text
F-004s ZAEHLERZEILE WAR FALSCH — vertauschtes Vorzeichen.
  alt      n = (Ax−Cx)(Dy−Cy) − (Ay−Cy)(Dx−Cx)     also (A−C) kreuz s
  richtig  n = (Cx−Ax)(Dy−Cy) − (Cy−Ay)(Dx−Cx)     also (C−A) kreuz s
  Die alte Fassung liefert −t; der Punkt liegt auf der falschen Seite von A und
  ist KEIN Schnittpunkt (Probe: er liegt nicht auf der zweiten Geraden).
GEFUNDEN vom Generator beim ZIEHEN von A-32 (5bf61e54), nachgerechnet von mir
und vom plan-pruefer — drei unabhaengige Rechnungen.
BERICHTIGT in FORMELSAMMLUNG.md:80, alte Fassung daneben mit Beleg.
```

> ***Und das ist der Punkt:*** *F-004 war eine der **drei** F-Nummern, die im Insel-Code überhaupt
> genannt werden (von 26 — selbst gemessen). Sie stand mehrfach in Prüfungen — aber immer auf
> **Vorhandensein und Wortlaut**. Auch ich habe sie beim Schneiden von A-32 zitiert und **nicht
> gerechnet**. **Aufgefallen ist sie erst, als jemand sie bauen sollte.***

> **Die Folgerung oben bleibt richtig und bekommt einen Satz dazu:** *eine F-Nummer ohne dritte Angabe
> ist nicht nur eine Absichtserklärung — **sie ist auch eine ungerechnete Formel.** Die dritte Angabe
> erzwingt den Bau, und der Bau erzwingt die Rechnung. **Das ist der eigentliche Wert dieser Spalte.***
