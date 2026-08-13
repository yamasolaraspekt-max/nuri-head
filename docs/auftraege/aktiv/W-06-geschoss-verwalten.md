# W-06 — Geschoss verwalten. Drei Schichten, ein ID-Remap, und ein Wert der im Modell lebte und nie erschien

```yaml
auftrag: "W-06"
werkzeug: "W-06 Geschoss verwalten"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT in DREI
      Modulen: geometry/geschossVorlage.ts 78 Z. · app/dashboard/geschossStapel.ts 104 Z. ·
      app/dashboard/GeschossFlaeche.tsx 173 Z. — zusammen 355 Zeilen, zehn Exporte."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: acb3d494
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
anlass: "Zweite Ablesung aus dem Vorrat, den ich beim Prüfen der 19 LEER-Werkzeuge gemessen habe.
         W-31 war die erste. Die Registerzeile von W-06 nennt keinen Code — sie behauptet nichts
         Falsches, sie verschweigt nur, dass drei Module existieren und alle angeschlossen sind."
grundlage: "die drei Module · HausplanerApp.tsx, Kopfrahmen.tsx, palette.ts als Aufrufer ·
            drei Wächtertests · AUF-43 als dokumentierter Anlass im Dateikopf"
```

## 1 — Der tragende Punkt: der ID-Remap, und was passiert, wenn ihn jemand bricht

**Wörtlich aus `geschossVorlage.ts:1-8`:**

```text
„Reine Funktion: aus einem Quell-Geschoss + seinen Nodes (+ optional Dach) entsteht
 ein neues Geschoss darueber, mit KOPIERTER Geometrie. Alle Nodes bekommen neue IDs;
 Oeffnungen werden auf die NEUEN Wand-IDs umgehaengt (id-Remap), damit Tueren/Fenster
 an ihren kopierten Waenden haengen — nicht an den alten."
```

> **Das ist eine fachliche Zusage mit Schadenspotenzial, und sie steht in genau einem Satz.** *Wer den
> Remap bricht, erzeugt ein duplziertes Geschoss, dessen Türen und Fenster an den **Wänden des
> Ursprungsgeschosses** hängen. **Das fällt beim Duplizieren nicht auf** — es fällt auf, wenn jemand
> unten eine Wand ändert und oben wandert das Fenster mit. Ein Blatt, das den Remap nicht nennt, lässt
> die nächste Rolle glauben, „Geometrie kopieren" sei die ganze Aufgabe.*

**Und die Grenze steht im selben Kopf:** *„Kein Schreibpfad, keine Szene-Mutation; das Ergebnis füttert
die Commands (`ADD_LEVEL` + `ADD_NODE` + `ADD_ROOF`)." **Die Funktion baut ein Ergebnis, sie schreibt
es nicht** — dieselbe Trennung wie bei W-37s Adaptern, die wandeln und nicht rechnen.*

## 2 — Drei Module, drei Schichten, und sie sind sauber getrennt

```text
geometry/geschossVorlage.ts        78 Z.   REINE GEOMETRIE
  :11  LevelVorlage
  :32  GeschossDuplikat<N extends NodeBasis, R extends RoofBasis>
  :43  dupliziereGeschoss<N, R>(…)
       -> typparametrisiert: die Geometrieschicht kennt das Dokument NICHT

app/dashboard/geschossStapel.ts   104 Z.   DATEN
  :22  StapelEintrag · :34 Stapel
  :51  hoehenLabel(elevation)  -> string
  :66  stapel(levels, aktivId) -> Stapel
  :94  kurzfassung(s)          -> string
  :100 nachbar(s, richtung)    -> StapelEintrag | undefined

app/dashboard/GeschossFlaeche.tsx 173 Z.   OBERFLAECHE
  :56  GeschossFlaeche({…})    EIN Export
```

> **Die Generics in `:32` und `:43` sind die Aussage, nicht Zierde:** *`<N extends NodeBasis, R extends
> RoofBasis>` heißt, die Geometrieschicht arbeitet gegen **Mindestanforderungen** statt gegen die
> Dokumenttypen. **Sie kann deshalb nicht versehentlich ins Szenendokument greifen** — die Trennung ist
> im Typsystem verankert und nicht in einer Absprache.*

## 3 — AUF-43s Befund steht im Dateikopf, und einer davon ist Ehrlichkeits-Klasse

**Wörtlich aus `geschossStapel.ts:1-8`:**

```text
„Der gemessene Befund: dreizehn Bedienelemente in einer Zeile, vier voneinander
 unabhaengige Aufgaben, und der Geschossname stand ZWEIMAL nebeneinander — einmal als
 111-px-Select, einmal als Textfeld mit demselben Wert. Die Hoehenlage (elevation) wird
 im Modell gefuehrt, aber nirgends gezeigt. Und es gab KEIN BILD vom Stapel: der Nutzer
 sah nie, wie viele Geschosse uebereinanderliegen und wo er gerade ist."
```

> **„Die Höhenlage wird im Modell geführt, aber nirgends gezeigt"** — *das ist dieselbe Klasse wie die
> Falschauskünfte, gegen die diese Insel fünf Ehrlichkeitswächter hält, nur die stille Variante: **kein
> falscher Wert, sondern ein vorhandener, der nie erscheint.** Der Nutzer kann nicht wissen, dass das
> Modell mehr weiß als die Oberfläche zeigt. **`hoehenLabel` (`:51`) ist die Antwort darauf**, und das
> Blatt muss sagen, welches Format es liefert.*

*Der doppelte Geschossname ist der zweite Befund und heute behoben — `geschossFlaeche.test.ts` verriegelt
es: **„der Name kommt genau einmal vor (K3)".***

## 4 — Die Wächter: drei, mit unterschiedlicher Zugriffsart

**Gemessen an den Importzeilen, nicht an Wortvorkommen** (die Lehre aus W-36 und W-37):

```text
IMPORT
  geschossVorlage.test.ts     -> geometry/geschossVorlage
  geschossFlaeche.test.ts     -> app/dashboard/geschossStapel
  paletteNavigation.test.ts   -> app/dashboard/geschossStapel

NUR QUELLE — und es ist der interessante Fall:
  geschossFlaeche.test.ts:27  liest GeschossFlaeche.tsx als TEXT
                              (ohneKommentare + readFileSync), importiert es NICHT
                        :124  verlangt ^export function GeschossFlaeche\\(
                        :125  verlangt, dass es KEINE zweite Definition im App-Rumpf gibt
                        :111  verlangt GENAU EIN <GeschossFlaeche im App
```

> **Der Test heißt nach der Komponente und importiert das Datenmodul** — *wer nach „wer testet
> `GeschossFlaeche`" mit einem Import-Muster sucht, findet **null** und schließt auf „ungetestet".
> **Gemessen ist die Komponente über ihre Quelle verriegelt, und zwar strenger als ein Import es
> könnte:** `:125` schließt eine zweite Definition aus, `:111` erzwingt genau eine Verwendung. **Das ist
> H-9 in der Wächterfrage**, und es ist der dritte Fall dieser Klasse nach W-36 und W-37.*

## 5 — Der Bedienweg, jede Stelle geöffnet

```text
geschossVorlage  <- app/HausplanerApp.tsx
geschossStapel   <- app/HausplanerApp.tsx · app/dashboard/GeschossFlaeche.tsx
                    app/dashboard/palette.ts
GeschossFlaeche  <- app/dashboard/Kopfrahmen.tsx
```

*`palette.ts` ist der Befehlspaletten-Zugang — **die Geschossnavigation ist also auch über die Palette
erreichbar**, nicht nur über die Fläche. Das gehört ins Blatt, weil es ein zweiter Bedienweg ist.*

## 6 — Scope

```text
W-06 IST   die drei Module: dupliziereGeschoss mit id-Remap und Generics, der
           Stapel als Daten samt hoehenLabel/kurzfassung/nachbar, und die
           Flaeche. Dazu die drei Aufrufer und der zweite Bedienweg ueber die
           Palette.

W-06 IST NICHT
           der SCHREIBPFAD. dupliziereGeschoss fuettert ADD_LEVEL, ADD_NODE und
           ADD_ROOF — die Commands gehoeren nicht hierher, nur der Verweis.
           Kopfrahmen.tsx und palette.ts -> eigene Gegenstaende, nur als
           Aufrufer genannt.
           W-02 als Vorgaenger im Register -> Verweis, keine Beschreibung.
           F-032 ist eine FORMEL (Transformation eines Punktes,
           FORMELSAMMLUNG.md:218) und keine Sperre — die Registerspalte ist die
           Formelreferenz. Das gehoert klargestellt, weil vier LEER-Werkzeuge
           dieselbe Referenz tragen und das wie ein gemeinsamer Blocker aussieht.
```

## 7 — Abnahmekriterien

```text
W-06-1  (P1, TRAGEND) Der ID-REMAP steht in 1-ZWECK, woertlich aus
        geschossVorlage.ts:5-7: Oeffnungen werden auf die NEUEN Wand-IDs umgehaengt,
        damit Tueren und Fenster an ihren kopierten Waenden haengen und nicht an den
        alten. UND DER SCHADENSFALL steht daneben: wer den Remap bricht, haengt die
        Oeffnungen des Duplikats an die Waende des Ursprungsgeschosses — das faellt
        beim Duplizieren NICHT auf, sondern erst wenn unten eine Wand geaendert wird.
        Ohne den Schadensfall liest die naechste Rolle eine Implementierungsnotiz.
W-06-2  (P1) Die GRENZE steht in 2-FUNKTION, woertlich: kein Schreibpfad, keine
        Szene-Mutation, das Ergebnis fuettert die Commands ADD_LEVEL, ADD_NODE und
        ADD_ROOF. Dieselbe Trennung wie bei W-37s Adaptern.
W-06-3  (P1) Die DREI SCHICHTEN mit ihren Exporten und Fundstellen: reine Geometrie,
        Daten, Oberflaeche. Und die GENERICS als Aussage: <N extends NodeBasis,
        R extends RoofBasis> heisst, die Geometrieschicht arbeitet gegen
        Mindestanforderungen und kann nicht versehentlich ins Szenendokument greifen —
        die Trennung ist im Typsystem verankert und nicht in einer Absprache.
        Alle Exporte am Bau-Stand zaehlen, keine Zahl aus diesem Blatt uebernehmen.
W-06-4  (P1) AUF-43s Befund steht in 1-ZWECK, und ausdruecklich der stille Teil: die
        Hoehenlage wird im Modell gefuehrt und nirgends gezeigt. Das ist die stille
        Variante der Falschauskunft — kein falscher Wert, sondern ein vorhandener der
        nie erscheint. hoehenLabel (geschossStapel.ts:51) ist die Antwort, und das
        Blatt sagt welches FORMAT es liefert, am Code gelesen.
W-06-5  Die Waechter je mit ZUGRIFFSART, getrennt nach IMPORT und NUR QUELLE.
        Besonders: geschossFlaeche.test.ts heisst nach der Komponente, IMPORTIERT das
        Datenmodul und prueft die Komponente ueber ihre QUELLE (:27 readFileSync).
        Wer mit einem Import-Muster sucht, findet fuer GeschossFlaeche NULL und
        schliesst auf ungetestet — gemessen ist sie STRENGER verriegelt als ein Import
        es koennte: :125 schliesst eine zweite Definition aus, :111 erzwingt genau
        eine Verwendung. Keine Zahl im Kriterium, am Bau-Stand erheben.
W-06-6  Der zweite Bedienweg steht im Blatt: palette.ts fuehrt geschossStapel ein,
        die Geschossnavigation ist also auch ueber die Befehlspalette erreichbar und
        nicht nur ueber die Flaeche.
W-06-7  7-GRENZEN stellt klar, dass F-032 eine FORMEL ist und keine Sperre
        (FORMELSAMMLUNG.md, Abschnitt "### F-032 · Transformation eines Punktes").
        ANKER STATT NUMMER, berichtigt durch A-34 am 13.08.: hier stand
        "FORMELSAMMLUNG.md:218" — die F-004-Berichtigung (136ebca1) hat rund 35
        Zeilen davor eingefuegt, seitdem traegt jene Zeile "Zweck: PV-Ertrag,
        Verschattung" und F-032 liegt auf 253. Ein Kriterium wird BEFOLGT, nicht
        nur gelesen; eine verschobene Zahl darin schickt den Bauenden zur
        falschen Formel. Vier LEER-Werkzeuge
        tragen dieselbe Referenz — das sieht wie ein gemeinsamer Blocker aus und ist
        keiner. Ein Satz, der spaeter viermal Messzeit spart.
W-06-8  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zahl an zwei Mustern** (Pflichtprüfung 7), **Nachweis muss rot werden können** (Pflichtprüfung 4).

```yaml
warum_W_06_nach_W_31: "W-31 war die erste Ablesung aus dem Vorrat und die kleinste (75 Zeilen, drei
        Exporte). W-06 ist die groesste des Vorrats mit 355 Zeilen ueber drei Schichten — und sie ist
        deshalb die naechste, weil sie den Beleg liefert, dass der Vorrat nicht aus Restposten besteht.
        Ein Werkzeug mit drei sauber getrennten Schichten, einem typverankerten Zugriffsschutz und einem
        id-Remap mit Schadenspotenzial ist kein Rest."
was_ich_selbst_gemessen_habe: "Alle drei Dateien geoeffnet, Zeilen und Exporte gezaehlt VOR dem Scope
        (Pflichtpruefung 7, dritter Schritt), die Aufrufer je Modul ueber Importzeilen erhoben, die drei
        Waechter geoeffnet und ihre Zugriffsart bestimmt, F-032 in der FORMELSAMMLUNG nachgesehen statt
        sie fuer eine Sperre zu halten, und geprueft dass kein aktiver Auftrag W-06 abdeckt."
die_falle_die_ich_vermieden_habe: "F-032 steht bei W-06, W-12, W-14 und W-16 in der Register-Spalte. Ich
        haette daraus eine gemeinsame Sperre lesen koennen — vier Werkzeuge, eine Referenz, alle LEER.
        Nachgesehen ist es eine FORMEL, Transformation eines Punktes. Haette ich es fuer eine Sperre
        gehalten, waeren vier Werkzeuge weiter unbeschrieben geblieben, und zwar aus demselben Grund wie
        W-31: ein Wort gelesen statt die Sache gemessen."
W_06_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

## Votum des Evaluators (§11) — ABGENOMMEN

```yaml
votum: ABGENOMMEN
geprueft_am: "13.08.2026, evaluator"
bau_commit: "a05d6d12 (22:42) — GESUCHT, der einzige. Sieben Dateien, alle unter
  02-WERKZEUGE/W-06-geschoss-verwalten/."
elter: "543a6b2e"
```

### Messtisch — jede Kriterienzeile eine Zeile

```text
W-06-1 (P1, TRAGEND)  ERFUELLT — die QUELLE zuerst geoeffnet, dann das Blatt
  geschossVorlage.ts:5-7 sagt woertlich "Oeffnungen werden auf die NEUEN Wand-IDs umgehaengt
  (id-Remap), damit Tueren/Fenster an ihren kopierten Waenden haengen — nicht an den alten."
  1-ZWECK:15 traegt es als eigene Ueberschrift, :21 im Zitat, :25 den SCHADENSFALL getrennt
  ("wer den Remap bricht ..."). Beides da, nicht nur die Implementierungsnotiz.

W-06-2 (P1)           ERFUELLT
  Die Grenze steht woertlich in 2-FUNKTION:15 — "Kein Schreibpfad, keine Szene-Mutation; das
  Ergebnis fuettert die Commands (ADD_LEVEL + ADD_NODE + ADD_ROOF)". Am Quelltext gegengelesen:
  geschossVorlage.ts:7-8 sagt denselben Satz. :76 haelt zusaetzlich fest, dass W-06 keinen
  eigenen Befehl mitbringt.

W-06-3 (P1)           ERFUELLT — die Zahlen SELBST gezaehlt, nicht aus dem Blatt genommen
  geometry/geschossVorlage.ts        78 Zeilen · 3 Exporte
  app/dashboard/geschossStapel.ts   104 Zeilen · 6 Exporte
  app/dashboard/GeschossFlaeche.tsx 173 Zeilen · 1 Export
  SUMME                             355 Zeilen · 10 Exporte
  2-FUNKTION:5 sagt "355 Zeilen, ZEHN Ausfuhren" — beide Zahlen treffen exakt.
  Die GENERICS als Aussage: 2-FUNKTION:27 ("Die Generics sind eine AUSSAGE, keine Verzierung"),
  :36 die Begruendung ueber Mindestanforderungen, :39 "im Typsystem verankert und nicht in einer
  Absprache". Am Code belegt: geschossVorlage.ts:32 und :43 tragen
  <N extends NodeBasis, R extends RoofBasis>.

W-06-4 (P1)           ERFUELLT
  AUF-43s Befund steht in 1-ZWECK:39 mit eigener Ueberschrift ("ein Wert, der im Modell lebte und
  nie erschien") — die STILLE Falschauskunft ist als solche benannt.
  Das FORMAT ist am Code gelesen und nicht geraten. Ich habe geschossStapel.ts:46-58 selbst
  geoeffnet: '±0 mm' bei 0, Vorzeichen explizit, U+202F als Tausendertrennung, U+2212 als Minus.
  1-ZWECK:59-62 fuehrt genau diese vier Faelle als Tabelle, 7-GRENZEN:60/63 nennt U+2212 und das
  schmale geschuetzte Leerzeichen mit dem Hinweis, was ein Parser damit zu tun hat.

W-06-5                ERFUELLT — und das Kriterium beschreibt eine Falle, die ich nachgestellt habe
  Die drei Waechter mit ihrer Zugriffsart, von mir einzeln gemessen:
    geschossVorlage.test.ts    import=1  readFileSync=0   -> reiner IMPORT
    geschossFlaeche.test.ts    import=1  readFileSync=2   -> GEMISCHT
    paletteNavigation.test.ts  import=1  readFileSync=2   -> GEMISCHT
  Der gemischte Fall im Einzelnen nachgeprueft: geschossFlaeche.test.ts:16 importiert das
  DATENMODUL (stapel, kurzfassung, hoehenLabel, nachbar aus geschossStapel), :27 liest die
  KOMPONENTE ueber readFileSync aus GeschossFlaeche.tsx.
  DIE FALLE IST ECHT: `grep -rE "^import .*GeschossFlaeche" __tests__/` liefert 0 Treffer. Wer so
  sucht, haelt die Komponente fuer ungetestet. Gemessen ist sie STRENGER verriegelt:
    :111  assert.equal((app.match(/<GeschossFlaeche/g) ?? []).length, 1  -> genau eine Verwendung
    :125  assert.doesNotMatch(app, /function GeschossFlaeche/)           -> keine zweite Definition
  Beide Zeilen selbst geoeffnet, beide sagen wortwoertlich, was das Kriterium behauptet.

W-06-6                ERFUELLT
  app/dashboard/palette.ts:38 importiert `Stapel` aus './geschossStapel', :25 nennt die Geschosse
  im Kopfkommentar. Der zweite Bedienweg ist damit belegt und nicht behauptet.

W-06-7                ERFUELLT — die Zahl "vier" selbst nachgezaehlt
  7-GRENZEN:3 stellt es als ERSTES klar ("F-032 ist eine FORMEL und keine Sperre"), :15-16 mit dem
  ANKER statt einer Zeilennummer (die A-34-Lehre ist hier schon angewandt).
  Im REGISTER selbst gezaehlt — vier Werkzeuge mit F-032, alle auf LEER:
    :38 W-12 Ansicht und Kamera · :48 W-16 Grundriss unterlegen
    :54 W-06 Geschoss verwalten · :67 W-14 Kopieren/Spiegeln/Drehen
  Genau vier, wie das Kriterium sagt.

W-06-8                ERFUELLT — und ich habe die Gegenprobe WEITER gefasst als das Kriterium
  Alle sieben Blaetter gefuellt: 1-ZWECK 101 · 2-FUNKTION 89 · 3-FORMELN 59 · 4-BEDIENUNG 77 ·
  5-CODE/LIESMICH 51 · 6-PRUEFUNG 70 · 7-GRENZEN 72 Zeilen, sieben verschiedene md5.
  Das Kriterium verlangt "keine zwei Werkzeuge mit gleichem Hash". Ich habe deshalb nicht nur
  W-06 gegen sich selbst geprueft, sondern ALLE 239 Blattdateien unter 02-WERKZEUGE nach
  gleichnamigen Blaettern mit identischem Rumpf durchsucht:
    Blattnamen mit identischem Rumpf in mehreren Werkzeugen: 7   (Altbestand)
    davon betreffen W-06:                                    0
  DASS DIE PROBE SIEBEN ECHTE DOPPEL IM ALTBESTAND FINDET, IST IHR WIRKSAMKEITSNACHWEIS: sie ist
  nicht blind, sie findet Kopiervorlagen — und bei W-06 findet sie keine.

Suite / tsc           1750 / 1750 / fail 0, tsc exit=0 — und der Bau fasst 0 Dateien unter
                      resources/ oder app/ an. Kein Produktivcode, wie der Scope sagt.
Browser               NICHT GEFAHREN, mit Grund: reiner Dokumentationsbau, kein Verhalten.
§15                   Kein Schreibvorgang gegen eine Datenbank im Pruefumfang.
```

### Was mir positiv aufgefallen ist, weil es genau die Lehre der letzten Abnahme aufnimmt

```text
Die Blaetter fuehren eine ZITIERTE Zahl mit ihrer Herkunft statt sie als heutige auszugeben.
1-ZWECK:80 zitiert "34 der 110 Werkzeuge" aus GeschossFlaeche.tsx:5-7 und setzt :83-85 daneben:
  "Der Nenner 110 ist der Stand jenes Textes; heute sind es 111 — der Kontur-Vertrag kam mit
   1fba9a1d hinzu (A-29). Zitat unveraendert gelassen, weil es einen Stand belegt; der Zaehler 34
   ist hier nicht nachgemessen und wird deshalb nicht als heutige Zahl gefuehrt."
7-GRENZEN:69-72 sagt dasselbe unter der Ueberschrift "Nicht ausgewertet".
Das ist genau die Trennung, deren Fehlen ich heute bei A-29 beanstandet habe — hier steht sie von
selbst da, samt Verweis auf den Auftrag, der die Zahl berichtigt hat.
```

### Eine Beobachtung ohne Befund — sie gehoert dem Release-Prüfer, nicht dem Bau

```text
Das REGISTER (02-WERKZEUGE/REGISTER.md:54) fuehrt W-06 weiterhin als LEER, obwohl die sieben
Blaetter jetzt gefuellt sind. KEIN Kriterium und KEIN Scope-Punkt nennt das Register — der Scope
zaehlt die drei Module, die Aufrufer und den Bedienweg auf, sonst nichts. Der Bau hat es also zu
Recht nicht angefasst; ich melde es, damit die Divergenz nicht unbemerkt bleibt.
Zweitens, aus demselben Grund ohne Befund: Abschnitt 6 dieses Blattes (Scope, Z.142) nennt
weiterhin "FORMELSAMMLUNG.md:218", waehrend das KRITERIUM W-06-7 durch A-34 auf den Anker
umgestellt wurde. A-34s eigener Scope sagt ausdruecklich "Nur Kriterien — nicht Befund- und
Belegtexte", und ein Scope-Abschnitt ist keines von beiden. Auch das gehoert gemeldet und nicht
still entschieden.
```

### Eigene Messfehler in diesem Durchgang

```text
KEINE, die ein Ergebnis verfaelscht haetten. Anzumerken ist ein Aufbau-Detail: mein erster Lauf
zum Zaehlen der Exporte lieferte fuer eine gesuchte Datei nichts, weil ich mit einem
find-Muster ohne Endungs-Alternative suchte (GeschossFlaeche ist .tsx, nicht .ts). Gemerkt am
leeren Ergebnis, nicht an einer Zahl — und vor jeder Wertung behoben.
```

**Weitergabe:** ABGENOMMEN → **Release-Prüfer**. Die zwei Beobachtungen (Register-Zustand,
Scope-Zeile 142) → ohne Auflage, zur Kenntnis.
