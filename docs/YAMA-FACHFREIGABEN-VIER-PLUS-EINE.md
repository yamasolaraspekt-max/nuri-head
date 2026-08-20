# Vier Fachfreigaben bleiben bei Yama, die fünfte Frage entscheide ich — und einen Befund habe ich unterwegs gefunden

> **Release-Prüfer, 20.08. ~13:2x.** Auf `8384fcec`. Yama hat gebeten, diesen Posten zu übernehmen.
> **Ich übernehme ihn nicht vollständig, und das ist keine Weigerung, sondern seine eigene Regel:**
> *„NICHT vertreten bleiben: Fach-/Haftungsentscheidungen."* **Vier der fünf sind genau das.**
>
> **Was ich stattdessen tue:** jede der vier so weit messen, dass sie in einem Zug entscheidbar ist
> — Frage, fehlender Operand, Folge, Fundstelle der Norm. **Die fünfte ist Architektur und keine
> Fachfrage; die entscheide ich.**

---

## Warum diese vier nicht in meiner Vertretung liegen

Es sind nicht „schwierige" Fragen, sondern Fragen einer bestimmten Art. **Alle vier setzen eine
Zahl, die anschließend in eine Rechnung wandert, deren Ergebnis jemand verantwortet** — Material,
Geld oder Tragwerk. Bei dreien nennt der Bau selbst die Norm, die sie regelt.

```
B-01  Uebermessungsregel     VOB/C ATV DIN 18338     -> Materialmenge -> Geld
B-06  Sicherheitsrand        keine Fachgrundlage      -> Materialmenge -> Geld
      1a/2a-Zuschlag         DIN EN 1991-1-3/NA       -> Schneelast -> Querschnitt -> TRAGWERK
      Dachstuhl-Parameter    Eurocode 5 (kmod, NKL)   -> Bemessung -> TRAGWERK
```

**Und zwei davon haben die bauende Rolle selbst offengelassen** — mit derselben Begründung, aus der
ich sie nicht setze. Der Generator schreibt zum Schneelast-Fall wörtlich:

> *„1a und 2a AUS DEM AUSWAHLFELD GENOMMEN statt sie zu belegen — ihr Zuschlag nach DIN EN
> 1991-1-3/NA ist ein Normwert, den ich nicht setze (Operanden-Gate); eine Kachel, die ‚Zone 1a'
> anzeigt und Zone 3 rechnet, ist ein falsches Versprechen."*

**Wenn die bauende Rolle den Wert aus Prinzip nicht setzt, kann die transportierende ihn nicht
stellvertretend setzen.** Das wäre dieselbe Zahl mit weniger Fachgrundlage.

---

## Die vier, entscheidungsreif gemacht

### 1 · Übermessungsregel (B-01) — die einzige mit einem heute falschen Zahlenwert

```
dachOeffnung.ts:68     const halbU = breiteM / 2 + rand;        Rand IM Rechteck
dachAusschnitt.ts:312  oeffnungFlaecheM2 = (uMax-uMin)*(vMax-vMin);
dachAusschnitt.ts:386  nettoFlaecheM2 = bruttoFlaecheM2 - oeffnungFlaecheM2;
```

**Der 10-cm-Streifen um eine Dachöffnung ist eingedeckte Fläche** — dort liegen Anschlussziegel
bzw. Eindeckrahmen. Er wird heute **abgezogen**. Handwerklich läuft es umgekehrt: nach der
Übermessungsregel werden Öffnungen **bis zu einer Grenzgröße gar nicht abgezogen**.

```
gerechnet (Fachpruefung, von mir nachvollzogen):
  Dachfenster 1,14 x 1,40 m, Rand 0,10 m
  Prueffeld  1,34 x 1,60 = 2,144 m2      echtes Loch 1,596 m2
  Differenz  0,548 m2 je Fenster   ->  bei vier Fenstern ~3,7 % einer 60-m2-Seite
```

```
ZU ENTSCHEIDEN   ab welcher Oeffnungsgroesse wird abgezogen?
                 (VOB/C ATV DIN 18338 kennt eine Grenzgroesse; welche gilt vertraglich)
FOLGE HEUTE      nettoFlaecheM2 ist zu klein und kann in eine Kalkulation wandern
DRINGLICHKEIT    hoechste der vier — es ist der einzige Posten, der HEUTE eine falsche
                 Zahl liefert, nicht bloss eine fehlende
```

### 2 · Sicherheitsrand (B-06) — eine gesetzte Zahl ohne Herkunft

```
dachOeffnung.ts:60/63   0,10 m, isotrop in alle vier Richtungen
```

**Zwei Mängel, die getrennt zu entscheiden sind:** der Wert selbst hat keine Fachgrundlage, **und**
er ist in alle Richtungen gleich, obwohl ein Eindeckrahmen oben, unten und seitlich verschieden
viel Platz braucht.

```
ZU ENTSCHEIDEN   (a) welcher Rand gilt   (b) bleibt er isotrop oder wird er gerichtet
FOLGE HEUTE      wirkt in dieselbe Flaechenbilanz wie B-01 — die beiden gehoeren zusammen
                 entschieden, sonst korrigiert man dieselbe Zahl zweimal in andere Richtungen
```

### 3 · 1a/2a-Zuschlag — sauber gesperrt, wartet nur auf den Normwert

**Hier ist nichts kaputt.** Der Generator hat den Defekt behoben (die Zone kam nie in der Formel an,
`1|2|3` gegen Zeichenketten, Doppel-Cast hatte die Prüfung abgeschaltet), Wächter gesetzt,
Rot-Probe gefahren, 1765 Tests grün. **Die Zonen 1a und 2a sind bewusst aus dem Auswahlfeld
genommen, statt sie mit einem geratenen Faktor zu belegen.**

```
ZU ENTSCHEIDEN   der Zuschlagsfaktor fuer Zone 1a und 2a nach DIN EN 1991-1-3/NA
FOLGE HEUTE      keine falsche Zahl — die Zonen sind schlicht nicht waehlbar
                 (das ist die richtige Zwischenlage: fehlt lieber als falsch)
NEBENBEI OFFEN   kmod 0.9 gegen den Kommentar "mittelfristig" und die Durchbiegungsgrenze
                 gegen w_inst — vom Generator ausdruecklich als Statiker-Fragen benannt
```

### 4 · Dachstuhl-Parameter am RoofNode — zwei Ebenen, nur eine ist Fachfrage

**Gemessen, was heute ist:**

```
RoofNode traegt   type · polygon · roofType · neigungGrad · firstAzimutGrad
                  ueberstandMm · traufhoeheMm · aufbauten? · anbau?
                  -> KEIN Dachstuhl-Parameter

SparrenEingabe verlangt   gebaeudebreiteM · neigungGrad · sparrenabstandM · breiteMm
                          hoeheMm · schneezone · gelaendehoeheM · holzklasse?

liest das Panel den RoofNode?   NEIN (enginePanels.ts: keine RoofNode-Fundstelle)
```

> **Zwei Operanden stehen bereits im Modell und werden trotzdem getippt:** `neigungGrad` steht am
> `RoofNode`, die Gebäudebreite steckt im `polygon`. **Das ist die Doppelerfassung, die den Posten
> auslöst.**

```
EBENE A (Modell)   sollen sparrenabstandM, breiteMm, hoeheMm, holzklasse ans RoofNode,
                   und sollen neigungGrad/Gebaeudebreite von dort GELESEN statt getippt werden?
                   -> Architektur- und Modellfrage. Die koennte ich entscheiden.
EBENE B (Fach)     welche Werte zulaessig und welche Vorgabe sinnvoll ist (Sparrenabstand,
                   Holzklasse, kmod, Nutzungsklasse) -> Eurocode 5, Fachfrage.
```

**Ich entscheide auch Ebene A hier nicht**, und der Grund ist nicht Vorsicht, sondern Reihenfolge:
**welche Felder ans Modell gehören, folgt daraus, welche Werte fachlich gebraucht werden.** Ebene A
vor Ebene B zu entscheiden hieße, das Datenmodell gegen eine noch offene Fachfrage zu schneiden.

**Und eine Quellenlage, die ich offenlege:** zu diesem vierten Posten habe ich **keine committete
Prüfung gefunden**, die ihn stellt — anders als bei B-01, B-06 und dem Schneelast-Fall. Was oben
steht, ist meine eigene Messung am Code. **Falls die Frage aus einer Prüfung stammt, die noch nicht
transportiert ist, kann ihr Zuschnitt von meinem abweichen.**

---

## Die fünfte Frage entscheide ich: Träger der Raumerkennung

**Sie ist keine Fachfrage.** Sie berührt weder Norm noch Geld noch Tragwerk — sie fragt, wo eine
Rechenvorschrift wohnt. **Und sie ist vollständig messbar.**

### Gemessen: sie läuft tatsächlich zweimal, und beide Male produktiv

```
TYPESCRIPT  geometry/roomDetection.ts (190 Z.)  ->  erkenneRaeume(waende, hoeheMm)
  Verbraucher:  renderers/three-d/szene.ts:357     (die 3D-Darstellung)
                app/ableitungen.ts:62              (raeumeAus)
                app/HausplanerApp.tsx:21           (Import)

PHP  Services/Geometrie/SzeneProjektionService.php (329 Z.)  ->  projiziere(scene)
  Verbraucher:  Domain/Hausplaner/Actions/UebernehmeSzeneInAuslegung.php:43 (injiziert)
                Http/Controllers/Hausplaner/HausplanerController.php:228
                routes/web.php:4999   POST /objekt/{objekt}/uebernehmen
  und die Kette SCHREIBT:  UebernehmeSzeneInAuslegung.php:113  $neu->save()

GEGENPROBE zwischen beiden:   KEINE   (kein Test haelt die zwei Ergebnisse gegeneinander)
```

### Die Entscheidung: beide bleiben — was fehlt, ist nicht Vereinheitlichung, sondern die Gegenprobe

**Der Grund für zwei Träger ist technisch zwingend und steht nicht zur Wahl.** Die Heizlast rechnet
serverseitig; ein serverseitiges Ergebnis darf nicht auf einer Zahl aufbauen, die im Browser
entstanden ist. **Das ist keine Architekturvorliebe, sondern die Grenze zwischen vertrauenswürdiger
und nicht vertrauenswürdiger Herkunft.** Wer die TS-Seite zum alleinigen Träger macht, verlagert
eine Rechnung, die eine Auslegung trägt, auf den Client. Wer die PHP-Seite zum alleinigen Träger
macht, nimmt dem Planer die unmittelbare Darstellung.

**Der PHP-Kopf (`:8`) hat das von Anfang an richtig gefasst:** *„eingefrorener Vertrag (nur als Referenz
gelesen, kein Code kopiert)"* — **eine bewusste Zweitumsetzung nach festgeschriebenem Vertrag ist
kein SSOT-Bruch, solange der Vertrag geprüft eingehalten wird.**

> ***Genau diese Prüfung fehlt.*** **Zwei Umsetzungen derselben planaren Raumerkennung, 190 gegen
> 329 Zeilen, beide produktiv, keine einzige Probe, die sie gegeneinander hält. Laufen sie
> auseinander, merkt es niemand — und die Folge landet in einer Auslegung.**

```
ENTSCHIEDEN   beide Traeger bleiben. Keiner wird abgeschafft, keiner stillgelegt.
GEFORDERT     eine Gegenprobe: dieselbe Szene durch beide Seiten, Raumzahl und
              Polygonflaechen verglichen, als Waechter im Tor.
NICHT ENTSCHIEDEN  die langfristige Zielarchitektur — ob eine Seite spaeter weicht,
              ist eine Frage fuer die Zielarchitektur und nicht fuer heute.
```

**Warum das die tragfähige Antwort auf „TypeScript oder PHP" ist:** die Frage unterstellt, dass eine
von beiden falsch am Platz ist. **Gemessen ist keine falsch am Platz — falsch ist, dass niemand
prüft, ob sie dasselbe sagen.**

---

## Der Befund, der unterwegs angefallen ist

**Der Dateikopf von `SzeneProjektionService.php` behauptet das Gegenteil des heutigen Zustands:**

```
Kopf (:20-21)   "Verdrahtung/Schreiben nach gebaeude_geometrie = P2-2 (Yama-Go).
                 Diese Klasse schreibt NICHTS und wird von KEINEM Produktivpfad aufgerufen."

gemessen        Action injiziert ihn (:43) · Controller ruft die Action (:228)
                · Route web.php:4999 · die Kette schreibt (:113 $neu->save())
```

**Für die Klasse selbst stimmt der Satz** — sie schreibt nichts. **Für ihre Lage stimmt er nicht
mehr:** sie wird aufgerufen, und der Aufrufer schreibt. Der Kopf war zum Zeitpunkt seines Schreibens
richtig (P2-1b) und ist durch die spätere Verdrahtung (P2-2) überholt worden.

> **Dieselbe Klasse wie §109–§111 und §114 — eine Aussage, die bei der Abgabe stimmte und danach
> ungültig wurde.** *Hier wiegt sie schwerer als sonst: Ein Kopf, der „von KEINEM Produktivpfad
> aufgerufen" sagt, lädt dazu ein, die Datei für folgenlos zu halten. Sie ist es nicht.*

**Ball für diese Berichtigung: beim Generator** — es ist eine Kopfzeile an seinem Bau, keine
Entscheidung.

---

## Ball

**Bei Yama, unverändert und ungekürzt:**

```
1  Uebermessungsregel   B-01, VOB/C ATV DIN 18338 — welche Grenzgroesse gilt
2  Sicherheitsrand      B-06, (a) welcher Wert  (b) isotrop oder gerichtet
                        -> 1 und 2 zusammen entscheiden, sie wirken in dieselbe Bilanz
3  1a/2a-Zuschlag       DIN EN 1991-1-3/NA — Faktor fuer Zone 1a und 2a
                        (dazu kmod 0.9 und die Durchbiegungsgrenze, beides Statiker)
4  Dachstuhl-Parameter  zuerst Ebene B (welche Werte fachlich), dann Ebene A (welche
                        Felder ans RoofNode) — nicht umgekehrt
```

**Bei niemandem mehr:** die Trägerfrage der Raumerkennung. Beide bleiben; die Gegenprobe ist
gefordert und ist ein Bau, keine Entscheidung.

**Beim Generator:** die überholte Kopfzeile in `SzeneProjektionService.php`.
