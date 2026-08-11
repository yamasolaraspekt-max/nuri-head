# W-08 Stufe 1 — Dachfläche messen BESCHREIBEN: 48 Zeilen, und ein gefährlicher Grenzfall

```yaml
auftrag: "W-08/1"
werkzeug: "W-08 Dachflaeche messen"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-08 aus polygonFlaeche.ts ableiten"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: b202ad7c
prioritaet: P1
anlass: "Yamas Ansage 10.08. — Klasse A vollstaendig, sorgfaeltig, behutsam; Runde 1"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt. Kein W-08-Blatt lag als Auftrag vor."
muster: "W-01-fang-beschreiben.md und W-02-wand-beschreiben.md — Struktur, Kriterienform, Rot-Form"
```

## Ist-Zustand — und meine Grobmessung war um 238 Zeilen zu hoch

**Anbindungsmessung, je Modul an den Exporten gefahren:**

```text
GEHOERT ZU W-08
geometry/polygonFlaeche.ts                48 Zeilen, ZWEI Exporte
  Punkt2D            {x, y} — nimmt bewusst auch THREE.Vector2 an
  polygonFlaecheM2   Gausssche Flaechenformel (Shoelace)
Registry-Werkzeug 'flaeche-messen'        vorhanden, label "Flaeche messen"
Zusagen                                   1 dediziert (polygonFlaeche.test.ts)
                                          4 erwaehnend

GEHOERT NICHT ZU W-08 — meine eigene Grobmessung hatte es mitgezaehlt
geometry/wandFlaeche.ts                  238 Zeilen
  Bezugsmass · WandMengen · MeldungArt · Meldung · WandFlaecheErgebnis · wandMengen
  -> WAND-Mengenrechnung. Sie steht in W-02s Scope (11 Nennungen dort) und
     BENUTZT polygonFlaecheM2, gehoert aber nicht hierher.
```

> **Meine Fahrplan-Zahl für W-08 war `286 Z / 8 Exporte`. Richtig sind `48 Z / 2 Exporte`** — die
> Differenz war `wandFlaeche`, das ich in der Grobrunde mitsummiert habe. *Fünfter Fall heute, in dem
> eine Summenmessung eine Einzelmessung ersetzt hat und dabei falsch wurde. **Genau deshalb hat Yama
> „sorgfältig" gesagt** — mit der Grobzahl hätte dieses Blatt 238 Zeilen fremden Code beschrieben.*

## Das Werkzeug ist eine Schicht MIT Werkzeug darüber

```text
polygonFlaecheM2 wird aufgerufen von FUENF Stellen:
  renderers/three-d/deckenMesh.ts     (W-10 Decke)
  geometry/dachAusschnitt.ts          (W-07 Dach)
  geometry/dachformVorlagen.ts        (W-07 Dach)
  geometry/wandFlaeche.ts             (W-02 Wand)
  + Registry-Werkzeug 'flaeche-messen'
```

**Anders als bei W-01 gibt es hier BEIDES:** eine Rechenschicht, die vier andere Werkzeuge benutzen,
**und** ein bedienbares Werkzeug darüber. *Das ist kein Widerspruch zur
[Werkzeug-oder-Schicht-Frage](../../VORLAGE-WERKZEUG-ODER-SCHICHT.md) — es ist ihr dritter Fall:
Schicht **und** Werkzeug, im Gegensatz zu W-01 (nur Schicht) und W-02 (nur Werkzeug). Die Vorlage
sollte das als Möglichkeit kennen.*

## Der Grenzfall — und er ist der gefährlichste, den ich bisher gemessen habe

**Der Dateikopf sagt es selbst** (`polygonFlaeche.ts:1-18`):

```text
"Eingabe sind die 2D-Punkte der Dachflaeche IN DER (geneigten) Flaechenebene, in
 Metern (so liegt surf.polygon vor: lokale u/v-Koordinaten der Dachflaeche).
 Damit ist das Ergebnis die echte geneigte Dachflaeche in m²."
```

> **Die Neigung steckt in der EINGABE, nicht in einer Korrektur.** Wer flache Koordinaten übergibt,
> bekommt die **Grundfläche** statt der Dachfläche — **und das Modul kann das nicht erkennen.** Zwei
> Punktlisten sehen identisch aus; nur die Bedeutung unterscheidet sie.
>
> **Und genau dieser Fehler ist der Anlass des Moduls.** Derselbe Dateikopf:
> *„Problem vorher: Die Material-/Holzliste berechnete die Dachfläche teils als Rechteck-Rahmen
> (width × height) … für Walm-, L-, T- und sonstige polygonale Flächen deutlich zu hoch → überhöhte
> Flächen-/Materialmengen."*
>
> **Ein Modul, das einen Flächenfehler behoben hat, kann denselben Fehler über die Eingabe
> zurückbekommen — und es merkt es nicht.** Das gehört in `7-GRENZEN`, nicht als Fußnote, sondern
> als der Kernsatz des Blattes.

## DECISION

```text
Quelle       polygonFlaeche.ts (48 Z) + Registry 'flaeche-messen' + polygonFlaeche.test.ts
NICHT Quelle wandFlaeche.ts — gehoert zu W-02, mit Begruendung im Blatt
3-FORMELN    F-011 belegt (Shoelace, Z.20 ff.). F-023 und F-024 siehe unten —
             sie stehen im Register, aber NICHT im Code dieses Werkzeugs.
5-CODE       "angebunden aus geometry/polygonFlaeche.ts", mit den fuenf Aufrufern
             als Nachweis, dass es eine Schicht ist
7-GRENZEN    DER KERN: die Eingabe-Ebene entscheidet ueber die Bedeutung des
             Ergebnisses, und das Modul kann sie nicht pruefen. Dazu die Faelle
             aus der Formel: weniger als 3 Punkte, selbstschneidend, offen.
```

## Die F-Liste des Registers stimmt nur zu einem Drittel

**Register: `F-011, F-023, F-024`. Selbst geprüft:**

```text
F-011  Flaeche eines Polygons (Shoelace)     IMPLEMENTIERT in polygonFlaecheM2   ✓
F-023  A_Dach = A_Grundriss / cos(alpha)     NICHT implementiert — und nicht noetig:
                                             F-011 auf GENEIGTE Koordinaten liefert
                                             die wahre Flaeche direkt. F-011 und F-023
                                             sind ZWEI WEGE zum selben Ziel, keine
                                             Ergaenzung. Wer den einen geht, braucht
                                             den anderen nicht.
F-024  Azimut aus Normalenvektor             liegt in wallGeometry.ts als
                                             azimutDerNormalen — also in W-02s Modulen,
                                             nicht in W-08s.
```

> **Das ist der vierte Fall der N1-Frage:** *ist die F-Liste im Register ein **Ist** oder ein
> **Soll**?* Bei W-01 war es F-004, hier sind es F-023 und F-024. **Das Blatt entscheidet die Frage
> nicht** — es benennt sie und trägt nur F-011 als belegt ein. *Die Entscheidung betrifft alle 23
> Zeilen und liegt beim Plan-Prüfer (Nachtrag N1).*
>
> *Wenn F-023 später gebaut wird, gehört sein Grenzfall mit: **„α → 90° → cos → 0 → Fläche →
> unendlich. Über 85° absagen"** — er steht in der Sammlung und ist im Code nicht vorhanden, weil
> der Code cos gar nicht benutzt.*

## Nicht-Ziele

- **Kein Registry-Eintrag.** `flaeche-messen` existiert.
- **Keine Wandfläche.** `wandFlaeche.ts` gehört zu W-02.
- **Kein F-023-Bau.** Der zweite Weg wird **benannt**, nicht gebaut — und nicht entschieden.
- **Keine Änderung an `polygonFlaeche.ts`** oder seiner Zusage.
- **Keine Aussage über PV-Belegung.** `pvBelegung.ts` benutzt Flächen, hat aber kein W-Blatt
  (siehe Anschlussmatrix: elf Module ohne Werkbank-Platz).

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-08-dachflaeche-messen/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-08 LEER -> BESCHRIEBEN
                                                     + polygonFlaeche.ts als Fundstelle
```

*NICHT im Scope: `resources/**`, die F-Liste des Registers (N1-Frage), `FORMELSAMMLUNG.md`.*

## Wiederverwendungsprüfung (§5)

```text
polygonFlaeche.ts        VORHANDEN, 48 Z — Quelle, unangetastet
Registry 'flaeche-messen' VORHANDEN — Quelle fuer 4-BEDIENUNG
polygonFlaeche.test.ts   VORHANDEN — Quelle fuer 6-PRUEFUNG
der Dateikopf selbst     18 Zeilen Begruendung mit dem Vorfall, der zum Modul fuehrte —
                         die beste Quelle fuer 1-ZWECK, die es in diesem Repo gibt
W-01/1, W-02/1           Muster fuer Struktur und Stufenteilung
```

**Nichts wird neu erfunden.** *`1-ZWECK` kann aus dem Dateikopf entstehen — er nennt Problem,
Lösung und Grund.*

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite unberuehrt
```

**Erstnutzer:** *der Generator von W-08 Stufe 2 — und jede Rolle, die eine Fläche misst: der
Grenzfall der Eingabe-Ebene betrifft W-02, W-07 und W-10 gleichermaßen, weil alle drei
`polygonFlaecheM2` aufrufen.*

## Akzeptanzkriterien

**W-08/1-1 (P1, kein Platzhalter):** keiner mehr in den sieben Blättern. *Rot-Lage vom Generator zu
zählen — Muster: alle `<…>`-Klammern, nicht nur feste Marker. **Meine erste Zählweise war zu eng
(8 statt 26 bei W-01); die korrigierte Form steht in `04f78b73`.***

**W-08/1-2 (P1):** `3-FORMELN` nennt nur F-Nummern, keine ausgeschriebene Formel.

**W-08/1-3 (P1, F-Nummern belegt):** `F-011` mit Zeilennummer in `polygonFlaeche.ts`. **`F-023` und
`F-024` werden als *im Register geführt, im Code nicht vorhanden* benannt** — mit dem Hinweis, dass
F-023 ein alternativer Weg und F-024 in `wallGeometry.ts` liegt.

**W-08/1-4 (P1, `7-GRENZEN` — das Kernkriterium dieses Blattes):** Das Blatt sagt, dass **die
Eingabe-Ebene über die Bedeutung des Ergebnisses entscheidet** und dass das Modul es **nicht prüfen
kann**. Dazu die Formelfälle: weniger als drei Punkte, selbstschneidend, nicht geschlossen — je am
Code gemessen. *Ohne diesen Satz beschreibt das Blatt eine Flächenformel und verschweigt die einzige
Weise, wie sie falsch benutzt werden kann.*

**W-08/1-5 (P1, Herkunft):** `5-CODE` sagt „angebunden aus `geometry/polygonFlaeche.ts`" **und nennt
die fünf Aufrufer** — der Nachweis, dass es eine Schicht ist.

**W-08/1-6 (P1, der Ausschluss):** `wandFlaeche.ts` ist namentlich als Nicht-Gegenstand benannt, mit
Verweis auf W-02. *Es benutzt `polygonFlaecheM2` — genau deshalb wird es sonst wieder zugeordnet.*

**W-08/1-7 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite unverändert grün.

**W-08/1-8 (P1, Register mitgeführt):** Reifegrad **und** `polygonFlaeche.ts` als Fundstelle.

**W-08/1-9 (P1, §3 wird BELEGT):** Der `IN_ARBEIT`-Commit enthält den Befehl mit Ausgabe für „kein
anderer Auftrag steht auf `IN_ARBEIT`", **an beiden Orten** (Tafelzeile und `^zustand:`-Feld), im
selben Commit. **Zählbare Form (E2 aus Prüfung 03): mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer.** *Wortgleich zu `W-01/1-8`; Rot-Lage und Grenze dort belegt.*

---

### NACHGETRAGEN 12.08. — zwei Auflagen aus Yamas Azimut-Antwort

*Grundlage: `docs/BEFUND-AZIMUT-KONVENTION.md` (`3d368625`) und Yamas Freigabe von Schritt 2 mit
zwei Auflagen. **Beide Auflagen stammen aus Funden dieses Berichts, nicht aus einer Vermutung.***

**W-08/1-10 (P1, der doppelsinnige Azimut-Bereich steht in `7-GRENZEN`):** Das Blatt nennt
**wörtlich**, dass ein Azimutwert im Bereich **0…180 in zwei Konventionen gültig ist und
Entgegengesetztes bedeutet** — Kompass (0=Nord, Hausstandard, im Schema dokumentiert:
`2024_06_04_103808_create_p_v_roofs_table.php:67`) gegen PVGIS (0=Süd,
`PvgisErtragService.php:41`). Dazu **die Antwort auf „was tut es, wenn es nicht kann?":** was das
Werkzeug tut, wenn es einen Azimutwert **ohne mitgelieferte Konvention** bekommt.

```text
ROT-LAGE, heute messbar:
  grep -ciE 'azimut' <W-08-Blattordner>/7-GRENZEN.md      -> heute 0
  grep -rnE '\+ *180|- *180' app/Services/Energie/*.php   -> 0 (keine Umrechnung im Haus)
ZUGELASSENE ANTWORTEN (eine davon, ausdruecklich):
  (a) das Werkzeug NIMMT nur Werte mit benannter Konvention und sagt es ab, wenn keine dabei ist
  (b) es nimmt Kompass als Vorgabe UND schreibt die Annahme in sein Ergebnis
  VERBOTEN: den Wert stillschweigend durchrechnen. Ein Sueddach traegt im Kompass 180;
  unveraendert an PVGIS gegeben rechnet PVGIS ein NORDdach — groesstmoeglicher Fehler,
  und nichts schlaegt an, weil 180 in beiden Systemen eine gueltige Zahl ist.
```

**W-08/1-11 (P1, die First/Fläche-Ableitung ist EINGANGSBEDINGUNG in `2-FUNKTION`, kein Nebensatz):**
Das Blatt sagt in `2-FUNKTION`, dass der Azimut einer Dachfläche **abgeleitet und nie gepflegt** ist:

```text
scene.types.ts:325  firstAzimutGrad: number;   // First-RICHTUNG (Grad)
scene.types.ts:280  "Die Flaechen-Azimute werden NIE gepflegt, sondern [abgeleitet]"
-> gespeichert ist die FIRSTrichtung. Der First laeuft ENTLANG des Dachs, die Flaeche
   schaut SENKRECHT dazu. Wer die Firstrichtung als Flaechenazimut nimmt, hat 90 Grad
   Fehler — zusaetzlich zu den 180 aus W-08/1-10.
DER ABLEITUNGSMECHANISMUS EXISTIERT und ist zweisprachig konsistent — im Blatt zu nennen,
nicht neu zu bauen:
  wallGeometry.ts:37   azimutDerNormalen(start, end, seite)  -> 0..359, Nord=0, Uhrzeigersinn
  SzeneProjektionService.php:258  azimutRechteNormale($von, $bis)
  BEIDE rechnen atan2(dy, -dx) fuer die rechte Normale — selbst nachgemessen, identisch.
  Zugesagt in SzeneProjektionServiceTest:80  assertSame([0, 90, 180, 270], $azimute)
```

> **Warum das Eingangsbedingung ist und nicht Hintergrund:** *W-08 **misst Dachflächen**. Wenn der
> Azimut einer Fläche nicht gespeichert, sondern gerechnet wird, dann ist „welcher Azimut gilt für
> diese Fläche" keine Randfrage, sondern die Frage, ob die Messung überhaupt einer benennbaren
> Fläche gilt.* **Ein Flächenmaß ohne belegte Ausrichtung ist für die Ertragsrechnung wertlos — und
> genau dorthin führt Schritt 7.**

**W-08/1-12 (P1, F-024 bleibt beim gemessenen Befund):** Der bereits im Blatt stehende Befund —
*F-024 „Azimut aus Normalenvektor" liegt in `wallGeometry.ts` als `azimutDerNormalen`, also in W-02s
Modulen, nicht in W-08s* — **bleibt wörtlich stehen und wird nicht durch die zwei neuen Kriterien
ersetzt.** *Er ist der Beleg dafür, dass W-08 die Ableitung **benutzt** und nicht **besitzt**; ohne
ihn liest W-08/1-11 wie eine Bauvorgabe.*

## Kantenliste

```text
flache Koordinaten uebergeben        -> Grundflaeche statt Dachflaeche, STILL falsch
                                        (W-08/1-4, der Kernfall)
< 3 Punkte                           -> MESSEN was polygonFlaecheM2 liefert
selbstschneidendes Polygon           -> Shoelace liefert eine Zahl, aber keine sinnvolle
nicht geschlossene Punktfolge        -> die Formel schliesst implizit; benennen
THREE.Vector2 als Eingabe            -> ausdruecklich erlaubt (Dateikopf), keine Kopplung
Einheit                              -> Eingabe in METERN, Ausgabe m². F-011 in der
                                        Sammlung rechnet in mm² -> EINHEITENBRUCH benennen,
                                        nicht angleichen
```

> *Die letzte Zeile ist ein echter Fund: **die Sammlung sagt „Fläche in mm² (→ /1 000 000 für m²)",
> der Code nimmt Meter und liefert m².** Beides ist richtig für sich, aber wer F-011 aus der Sammlung
> in ein anderes Modul überträgt und Millimeter einsetzt, bekommt eine Fläche um den Faktor 10⁶
> daneben. **Das gehört benannt, nicht stillschweigend vereinheitlicht** — die Einheitenwahl ist eine
> Fachentscheidung.*

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** wie bei W-01/W-02 — muss der Generator von Stufe 2 im Code nachsehen, was in
`2-FUNKTION` oder `7-GRENZEN` hätte stehen müssen, war die Beschreibung unzureichend.

## Konfliktprüfung (§5)

```text
W-02/1   BESCHRIEBEN (fertig)   werkbank/W-02/** + REGISTER.md    kein IN_ARBEIT mehr
W-13/1   ENTWURF                werkbank/W-13/** + REGISTER.md
W-08/1   DIESES                 werkbank/W-08/** + REGISTER.md
A-12     ENTWURF                FORMELSAMMLUNG + VORGEHEN         KEINE Beruehrung
A-11     gebaut                 scripts/**                        KEINE Beruehrung
-> REGISTER.md wird von W-08/1 und W-13/1 geaendert, je eine Zeile plus Fundstelle.
   Auf Zeilenebene disjunkt. §3 loest es, solange nur einer IN_ARBEIT ist —
   und das wird nach W-08/1-9 BELEGT, nicht zugesichert.
FACHLICHE Beruehrung: wandFlaeche (W-02) benutzt polygonFlaecheM2. Kein Dateikonflikt,
   aber der Grund fuer den Ausschluss in W-08/1-6.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "NICHT Runde 1 — korrigiert 10.08.: W-08 baut HINTER A-12, zusammen mit W-07.
                Grund: das Register fuehrt W-08 als 'braucht W-07', und W-07 haengt an der
                Dachweg-Frage. Dazu sind zwei der drei Register-Formeln nicht belegbar
                (F-023 nicht implementiert, F-024 nur als WAND-Variante). Das Blatt bleibt
                gueltig, nur die Einreihung aendert sich."
korrektur_am_fahrplan: "W-08 ist 48 Z / 2 Exporte, nicht 286 / 8 — die Grobzahl enthielt
                        wandFlaeche.ts, das zu W-02 gehoert"
```


## §11 — Bericht W-08/1 (Generator, 12.08.2026)

```yaml
auftrag: "W-08/1"
zustand: CODE_FERTIG
bau_commit: "7aa49e33"
in_arbeit_commit: "b972a8af"
basis: "b202ad7c"

kriterien:
  W-08/1-1:  GRUEN   # 28 -> 0 nach BEIDEN Zaehlweisen
  W-08/1-2:  GRUEN   # nur Nummern
  W-08/1-3:  GRUEN   # F-011 mit Zeile 44 und 46; F-023/F-024 als nicht vorhanden benannt
  W-08/1-4:  GRUEN   # Kernkriterium: die Eingabe-EBENE entscheidet, Modul kann sie nicht pruefen
  W-08/1-5:  GRUEN   # Herkunft + Aufrufer — mit gemessener Korrektur, s. u.
  W-08/1-6:  GRUEN   # Ausschluss benannt — Begruendung des Auftrags widerlegt, s. u.
  W-08/1-7:  GRUEN   # 0/0/0, Suite unveraendert gruen
  W-08/1-8:  GRUEN   # Register: BESCHRIEBEN + polygonFlaeche.ts
  W-08/1-9:  GRUEN   # b972a8af: 2 Befehlszeilen, 2 Ausgabewerte
  W-08/1-10: GRUEN   # Antwort (a), gemessen begruendet, s. u.
  W-08/1-11: GRUEN   # Eingangsbedingung steht in 2-FUNKTION vor allem anderen
  W-08/1-12: GRUEN   # F-024 bleibt beim gemessenen Befund: liegt in wallGeometry.ts

zwei_auftragsangaben_widerlegt:
  aufrufer:
    auftrag: "fuenf Aufrufer"
    gemessen: "VIER Importe im Produktivcode — deckenMesh.ts:7, dachAusschnitt.ts:26,
               dachformVorlagen.ts:33, grundriss.ts:19"
  ausschluss_begruendung:
    auftrag: "wandFlaeche.ts BENUTZT polygonFlaecheM2 — genau deshalb wird es sonst wieder zugeordnet"
    gemessen: "es importiert sie NICHT. Einzige Fundstelle: ein Kommentar in Zeile 27, und der
               sagt das Gegenteil — 'Keine zweite Flaechenengine. polygonFlaecheM2 und die
               Raumerkennung bleiben, was sie sind.'"
    folge: "Der Ausschluss bleibt richtig, seine Begruendung nicht. wandFlaeche.ts ist ein
            Nachbar, der Abstand haelt — kein Benutzer."

der_gefaehrliche_fall:
  was: "0 bedeutet DREIERLEI — zu wenig Punkte (Z.32), ungueltige Zahl (Z.42), nicht endliches
        Ergebnis (Z.47) — und eine entartete Flaeche ist ebenfalls 0."
  warum_bewusst: "der Kopf sagt 'Niemals NaN oder Infinity' zu (Z.29). Wer das zusagt, MUSS etwas
                  zurueckgeben; er hat 0 gewaehlt. Der Preis steht jetzt im Blatt."
  klasse: "A-10 — nicht das leere Ergebnis ist das Problem, sondern das gefuellte, das seine
           Herkunft verschweigt."

befund_ueber_W08_hinaus:
  drei_umsetzungen: "die Schuhbandformel ist DREIMAL im Haus, ZWEI Fassungen heissen GLEICH"
  ts: "polygonFlaeche.ts:31 — Eingabe METER, Ergebnis m², Betrag, Number.isFinite je Punkt"
  php: "app/Services/Heizlast/GeometrieAbleitungService.php:118 — Eingabe MILLIMETER,
        geteilt durch 1.000.000, KEINE Endlichkeitspruefung"
  dritte: "roomDetection.ts:70 signierteFlaeche — mm², MIT Vorzeichen (W-05)"
  gefahr: "wer die zwei gleichnamigen verwechselt, irrt um den Faktor eine Million — und beide
           liefern eine Zahl, die aussieht wie eine Flaeche"

W-08/1-10_antwort:
  gewaehlt: "(a) — in ihrer schaerfsten Form"
  begruendung: "polygonFlaecheM2() hat KEINEN Parameter fuer eine Ausrichtung. Das Werkzeug nimmt
                ueberhaupt keinen Azimutwert und kann ihn deshalb nicht stillschweigend
                durchrechnen. Die Gefahr sitzt eine Ebene weiter — und steht trotzdem im Blatt."
  selbst_nachgemessen: "0 Treffer auf '+180'/'-180' in app/Services/Energie — es gibt im Haus
                        keine Umrechnung zwischen den beiden Konventionen"

nicht_gemessen:
  - "ob die vier Aufrufer wirklich METER uebergeben — Zusage des Dateikopfs gelesen, Importe
     gezaehlt, aber die Einheit an den vier Aufrufstellen NICHT nachgerechnet.
     Steht als Frage in 6-PRUEFUNG."
  - "ob der PHP- und der TS-Weg bei derselben Geometrie dieselbe Zahl liefern — als Pruefpunkt
     benannt, nicht ausgefuehrt."

zeilenpruefung: "14 Stellen inhaltlich geprueft, 0 falsch — erstes Blatt ohne Korrektur"
browserabnahme: "entfaellt — reine Dokumentblaetter"
ballbesitz: evaluator
```
