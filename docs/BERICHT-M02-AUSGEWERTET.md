# Bericht — M-02 ausgewertet. Fünf Dateien, 13.852 Zeilen, und der Bestand ist überall weiter außer an einer Stelle

```yaml
art: "Messbericht des Planners. Yamas Prompt 2, Punkt 4: 'Wie M-01: Existenz, Umfang,
      Gleichheit, Fundstelle. Messen, nicht bewerten.'"
gemessen_am: "12.08."
basis_sha: 10544fbc
anlass: "M-02 stand seit dem ERSTEN Prompt als 'noch nicht ausgewertet'. W-21s Registerzeile
         fuehrt es ausdruecklich als ungelesen; der Generator hat es in 992d5d76 wiederholt."
```

## Zuerst eine Korrektur an mir: M-02 ist nicht eine Datei, sondern fünf

**Ich habe M-02 in mehreren Berichten als „2021 Zeilen" geführt** — das ist `profi_holzbau_solar_cad.tsx`,
**eine von fünf**. `BESTAND-YAMA.md:51-59` listet fünf Dateien.

```text
MENGE (B6)   die fuenf in BESTAND-YAMA.md:55-59 genannten Dateien
DATEI                             ZEILEN     BYTES   HASH (12)
dachdecker_pro.tsx                  2.993   169.236  8284914bbadb
profi_holzbau_solar_cad.tsx         2.021   123.466  924d6a3b66da
solarmaster_konstruktion.tsx        3.045   178.730  94eb1798e8fa
solarconstructapp.tsx               3.321   192.152  319105612ae3
solar_master_pro.tsx                2.472   154.092  393298739acd
                                   ------
SUMME                              13.852
```

> **Alle fünf existieren, und alle fünf Zeilenzahlen stimmen exakt mit `BESTAND-YAMA.md`.** *Das ist
> die erste Bestandsangabe in diesem Projekt, die bei der Nachmessung punktgenau hält — bei den
> Werkzeug-Grobzahlen fiel sie sechsmal von sechs.*
>
> **Meine „2021 Zeilen" waren B6 in Reinform:** *eine Zahl aus einer Registerzeile übernommen, die
> nur eine Datei nennt, und als Summe für M-02 ausgegeben. **Achter Fall der Reihe, und der erste,
> bei dem B6 die Klasse ist statt B5.***

## Gleichheit — die Kopien sind harmlos, und die Dublettenzahl ist überholt

```text
DATEI                        Vorkommen   einzigartig nach Inhalt
dachdecker_pro                    2            1
profi_holzbau_solar_cad           3            1
solarmaster_konstruktion          3            1
solarconstructapp                 3            1
solar_master_pro                  3            1
```

**Jede Datei liegt mehrfach, aber alle Kopien sind byte-identisch.** *Es gibt **keine divergenten
Fassungen** — man kann jede Kopie lesen, das Ergebnis ist dasselbe.*

**Und `VORGEHEN.md:43` ist überholt:** dort steht *„`profi_holzbau_solar_cad.tsx` liegt **fünfmal**"*.
**Gemessen sind es drei.** *Die Ursache kenne ich nicht — entweder wurde aufgeräumt, oder die alte
Zählung ging nach Dateinamen statt nach Inhalt. **Ich schreibe die Differenz hin und behaupte den
Grund nicht.***

## Fundstelle — der Vergleich mit dem Bestand, und er ist eindeutig

**Gemessen an `profi_holzbau_solar_cad.tsx` (die für W-21 relevante) gegen `geometry/`:**

```text
BEGRIFF          M-02   INSEL    Lage
sparren            22     268    beide — Insel 12x mehr
pfette             36      76    beide
gratsparren         7      34    beide
kehlsparren         4      30    beide
firstpfette         4      15    beide
fusspfette          2       7    beide
schifter            0      54    NUR die Insel
kehlbalken          0       7    NUR die Insel
zange               0       8    NUR die Insel
aufschiebling       0       9    NUR die Insel
```

> **M-02 hat KEINEN Holzbau-Begriff, den die Insel nicht hat.** *Und vier hat nur die Insel.*
>
> **Vorbehalt, ausdrücklich (H-6): das ist eine Wortzählung, kein Inhaltsvergleich.** *Ein Wort ist
> kein Beleg — die Zahlen zeigen eine **Richtung**, nicht ein Ergebnis. Deshalb habe ich die drei
> auffälligen Namen einzeln aufgemacht.*

## Die drei Einzelfunde — einer ist echt, einer ist eine Bestätigung, einer eine Falle

### 1 · ABBUND — der einzige echte Fund, und er ist kleiner als er aussah

```text
M-02      abbundCanvasRef (:1285) · abbundData (:1294) · Zeichenlauf (:1397-1420)
          -> eine ZEICHNUNG auf einem Canvas
INSEL     dachformVorlagen.ts: 'abbundhinweis' als FELD (:107) mit DREIZEHN
          ausgefuellten Fachhinweisen, u.a.:
            :1716 "Sparren auf First-/Fusspfette geklinkt (Kerve), Aufschiebling
                   fuer Traufueberstand"
            :1914 "Gratsparren als 3D-Laenge Wurzel(dx²+dy²+dz²); Schifter an Grat
                   anschneiden"
            :1787 "Trapezblech-Auflager: Pfettenabstand auf Profil-Tragweite abstimmen"
            :2051 "Geplant — Abbund erst nach sauberer Geometrie-/Tragwerksumsetzung"
```

> **Zwei verschiedene Dinge, und beide fehlen jeweils dem anderen:** *M-02 hat eine **Abbund-Zeichnung**
> und keine Fachhinweise. Die Insel hat **dreizehn Zimmerer-Fachhinweise** je Dachformvorlage und keine
> Zeichnung.*
>
> **Der Gewinn ist damit die DARSTELLUNG, nicht das Wissen.** *Und `dachformVorlagen:2051` zeigt, dass
> die Insel die Lücke selbst kennt und benennt: „Geplant — Abbund erst nach sauberer Geometrie-/
> Tragwerksumsetzung." **Das ist H-1 richtig gemacht: „nicht hier" mit „sondern dort".***

### 2 · `TIME_VARS` — das ist F-051, wörtlich, und M-02 ist die zweite Quelle

```text
profi_holzbau_solar_cad.tsx:75-79
  SCAFFOLD_M2: 8, RAFTER_M: 10, MEMBRANE_M2: 3, BATTEN_M: 2,
  TILE_M2: 15, INSULATION_M2: 12, CLEANUP: 90, HOOK_STD: 6,
  HOOK_GRIND: 5, RAIL_M: 4, MOD_MOUNT: 12

FORMELSAMMLUNG, F-051 🔴:
  "Die elf Zeitwerte (Geruest 8 min/m², Sparren 10 min/lfm, Deckung 15 min/m² …)
   haben KEINE Herkunft."
-> Geruest 8 ✓ · Sparren 10 ✓ · Deckung/TILE 15 ✓ · ELF Werte ✓ — deckungsgleich.
```

**Und M-02 benutzt sie in einer Angebotsposition** (`:1505-1506`): `tBase: TIME_VARS.RAFTER_M`,
`tTotal: totalRafterLength * TIME_VARS.RAFTER_M`.

> **Damit ist F-051s rote Ampel doppelt begründet, nicht widerlegt:** *dieselben unbelegten Zahlen
> stehen in zwei Prototypen und werden in beiden zu **Arbeitszeiten für Angebote** verrechnet.*
> **Kein Gewinn — eine Bestätigung der Sperre.**

### 3 · `battenDist: 34` — die Zahl, die W-21L blockiert. Und sie hilft nicht

```text
M-02:1305   battenDist: 34        (in cm, also 34 cm Lattweite)
            rafterSpacing: 70 · rafterWidth: 8 · rafterHeight: 18
QUELLE      grep auf quelle|hersteller|norm|DIN|ZVDH im Umfeld -> 0 Treffer
INSEL       dachWerte.ts:20  battenDist: 0.05  "Lattenabstand min 5 cm"
                             (Schutz gegen Division durch ~0, KEIN Fachwert)
```

> **34 cm ist ein plausibler Wert — ich hatte in `W-21L` „32 bis 38 cm" geschrieben, und er liegt
> mitten drin.** *Aber er trägt **keine Quelle**, keinen Ziegeltyp und keine Neigung.*
>
> **Nach H-7 und `W-21L-1` ist er damit kein Sollwert, sondern ein Default aus einem Prototyp** —
> genau die Gestalt, in der F-051 entstanden ist. **Er löst das Operanden-Gate von W-21L NICHT.**
> *Er taugt als **Plausibilitätsgrenze**: eine Tabelle, die 12 cm oder 60 cm liefert, ist falsch.*

## Die vier anderen Dateien — grob gemessen, nicht ausgewertet

```text
dachdecker_pro.tsx           2.993 Z   laut BESTAND "Vorgaenger von M-01"
solarmaster_konstruktion.tsx 3.045 Z   PV-Belegung, Reihen/Spalten
solarconstructapp.tsx        3.321 Z   "Konstruktion + Statik?"
solar_master_pro.tsx         2.472 Z   Angebots-/Ertragsseite
-> 11.831 Zeilen NICHT inhaltlich ausgewertet.
   Grund: W-21 brauchte den Holzbau, und der ist gemessen. Die drei PV-/Angebots-
   Dateien gehoeren zu W-31 (PV, gesperrt bis F-028 🟢) und W-23 — beide sind
   heute blockiert. Eine Auswertung waere ein Fund ohne Abnehmer.
   DAS IST EIN AUSSCHLUSS MIT ZIELADRESSE (H-1): sie gehoeren zu W-31 und W-23,
   und sie werden dort gemessen, nicht hier.
```

> **`solarconstructapp.tsx` trug laut Bestandsliste ein Fragezeichen bei „Statik?" — das war nach
> N-003 der interessanteste offene Posten der vier.**

### NACHTRAG 12.08. — der Prüfkandidat ist gemessen: KEINE Statik

**Die Frage war: liegt dort eine zweite Statik-Rechnung, also eine zweite Wahrheit zu
`sparrenBerechnung`? Gemessen: nein, und zwar eindeutig.**

```text
GESUCHT (B5, Zeilen gelesen):
  eurocode · DIN EN 199x · biegung · biegespannung · ausnutzung · durchbiegung ·
  kmod · gamma · schneelast · tragfaehigkeit · nachweis
  -> NULL Treffer, alle elf Begriffe.
Die N-003-Sicherheitsbeiwerte einzeln:
  GAMMA_G 0 · GAMMA_Q 0 · GAMMA_M 0 · KMOD 0 · 1.35 0
  (1.5 kommt 66x vor, 1.3 zweimal, 0.9 fuenfmal — Zahlen ohne Statik-Kontext,
   kein einziger Beiwertname daneben)
```

> **Es gibt keine zweite Statik-Wahrheit im Archiv. Der Posten ist geschlossen, und N-003 bleibt die
> einzige Statik-Rechnung im Haus.** *Das war die Frage, die ich vor einer N-003-Erweiterung geklärt
> haben wollte — sie ist geklärt, bevor jemand sie erweitert hat.*

**Und die Bestandszeile „Konstruktion + Statik?" ist damit falsch. Was es wirklich ist:**

```text
modul   146    string  57    montage 21    schiene 15    klemme 10    dachhaken 5
wechselrichter 0 · mppt 0 · verschattung 0 · ertrag 0
sparren 8 · pfette 5   -> nur als BEFESTIGUNGSGRUND (Dachhaken auf Sparren)
Tabellen: MODULE_DIMS · MODULE_TYPES · FASTENER_MAPPING
-> es ist PV-MODULBELEGUNG UND MONTAGESYSTEM. Keine Statik, keine Elektrotechnik,
   keine Ertragsrechnung.
-> Zieladresse: W-31 (PV-Belegung, gesperrt bis F-028 🟢), NICHT N-003.
```

**Und `TIME_VARS` steht auch hier — zum DRITTEN Mal.**

```text
F-051s Zeitwerte finden sich in:  dachdecker_pro_3d (M-01) · profi_holzbau_solar_cad
                                  · solarconstructapp
-> DREI Prototypen, dieselben elf unbelegten Zahlen. Die rote Ampel ist damit
   dreifach begruendet. Und es zeigt, dass die Prototypen VONEINANDER kopiert sind —
   was zugleich erklaert, warum keiner eine Quelle nennt: es gab nie eine.
```

## Ergebnis

```text
1  M-02 ist AUSGEWERTET, soweit W-21 es braucht: der Holzbau-Teil ist gemessen.
2  Der Bestand ist bei JEDEM gemessenen Holzbau-Begriff weiter. Vier Begriffe
   (Schifter, Kehlbalken, Zange, Aufschiebling) hat NUR die Insel.
   -> Yamas Satz "der Bestand ist an neun von zehn Stellen weiter als das Archiv"
      ist fuer den Holzbau bestaetigt.
3  EIN echter Fund: die Abbund-ZEICHNUNG. Das Fachwissen dazu hat die Insel schon
   (13 abbundhinweis-Texte), die Darstellung fehlt ihr.
4  KEIN Gewinn fuer W-21L: battenDist 34 ist quellenlos, das Gate bleibt zu.
5  F-051s Sperre ist BESTAETIGT, nicht aufgehoben — zweite Quelle derselben
   unbelegten Zeitwerte.
6  11.831 Zeilen bleiben ungelesen, mit Zieladresse: W-31 (PV) und W-23 (Deckung),
   beide blockiert. Plus ein Prueffall: solarconstructapp und die Frage "Statik?".
```

```yaml
existenz: "alle fuenf vorhanden, Pfad Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d"
umfang: "13.852 Zeilen, Einzelzahlen exakt wie BESTAND-YAMA.md"
gleichheit: "alle Kopien byte-identisch, keine divergenten Fassungen.
             VORGEHEN.md:43 nennt 'fuenfmal', gemessen sind drei — Grund unbekannt."
fundstelle: "Holzbau gemessen. Insel ueberall weiter, vier Begriffe nur bei ihr."
echter_fund: "Abbund-Zeichnung (Canvas). Das Wissen hat die Insel, die Darstellung nicht."
kein_gewinn: "TIME_VARS = F-051 (bestaetigt die Sperre) · battenDist 34 ohne Quelle"
offen: "11.831 Zeilen in vier Dateien, Zieladresse W-31 und W-23 (beide blockiert)"
pruefkandidat_geschlossen: "solarconstructapp.tsx traegt KEINE Statik — null Treffer auf
   elf Statik-Begriffe und keinen der N-003-Beiwerte. Es ist PV-Modulbelegung und
   Montagesystem (modul 146, schiene 15, klemme 10, dachhaken 5). Zieladresse W-31,
   nicht N-003. Die Bestandszeile 'Konstruktion + Statik?' war falsch."
f051_dritte_quelle: "TIME_VARS steht in DREI Prototypen — die elf unbelegten Zeitwerte
   sind voneinander kopiert. Das erklaert, warum keiner eine Quelle nennt: es gab nie eine."
eigener_fehler: "ich fuehrte M-02 als '2021 Zeilen' — das ist EINE der fuenf.
                 B6-Klasse, achter Fall der Reihe."
```
