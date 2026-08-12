# W-21 Stufe 1 — Sparren und Lattung BESCHREIBEN: fünf Module, und Bemessung nach Eurocode

```yaml
auftrag: "W-21/1"
werkzeug: "W-21 Sparren und Lattung"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-21 aus fuenf vorhandenen Holzbau-Modulen ableiten"
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: c9325929
prioritaet: P1
anlass: "Runde 2 der Klasse A, vom Release-Pruefer freigegeben (b9dc3c35)"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 11.08. — Claim VOR dem Schnitt. Kein W-21-Blatt lag als Auftrag vor."
muster: "W-01/1, W-02/1, W-04/1, W-05/1, W-08/1, W-11/1"
```

## Ist-Zustand — die erste Grobzahl der Klasse, die stimmt

**Anbindungsmessung je Modul an Exporten und Dateikopf gefahren:**

```text
GEHOERT ZU W-21 — alle fuenf, und sie sind kohaerent
geometry/sparrenBerechnung.ts           131 Z, 7 Exporte
  Schneezone · Holzklasse · bodenschneelast() · formbeiwertSchnee()
  SparrenEingabe · SparrenErgebnis · berechneSparren()
  Kopf: "Dachstuhl/Sparren-Vorbemessung (Einfeld-Sparren) nach EUROCODE"
geometry/sparrenTrennung.ts              67 Z, 3 Exporte
  SparrenTeilstueck · sparrenTeilstuecke() · istSicherTrennbar()
  Kopf: "Trennung eines Sparrens an einer Oeffnung (Dachfenster/Kamin);
         ergaenzt auswechslung.ts (Sicher-Entscheidung)"
geometry/schifterListe.ts               152 Z, 8 Exporte
  SchifterArt · SchifterSparren · SchifterMengen · klassifiziereSchifter()
  schifterAusFlaeche() · schifterMengen() · HolzStueckRef · schifterMengenAusListe()
  Kopf: "Klassifikation + Stueckliste der SCHIFTSPARREN (jack rafters)"
geometry/holzBauteile.ts                 82 Z, 4 Exporte
  HolzStueckRef · HolzBauteilMengen · OFFENE_HOLZBAUTEILE · holzBauteileAusListe()
  Kopf: "Aggregation der WEITEREN Holzbauteile (Pfetten, Grat-/Kehlsparren) aus der
         ECHTEN, bereits in der 3D-Geometrie erzeugten [Liste]"
geometry/holzMengen.ts                   64 Z, 3 Exporte
  HolzStueck · HolzMengen · holzMengenAusListe()
                                        --------------------------------
                                        496 Zeilen, 25 Exporte
Registry-Werkzeug                       KEINES (0 Treffer auf sparren/holz/lattung/dachstuhl)
Zusagen                                 SECHS dediziert — beste Absicherung aller Werkzeuge
                                        (sparrenBerechnung, sparrenTrennung, schifterListe,
                                         holzBauteile, holzMengen, enginePanelSparren)
Register                                LEER · braucht W-07 · F-001, F-030 · Quelle M-01/M-02
```

> **Kein Ausschluss, und die Grobzahl stimmt auf die Zeile (496).** *Das ist der erste Fall in dieser
> Klasse — bei W-08, W-04 und W-05 war sie zu hoch. **Der Grund ist lesbar:** die fünf Module bilden
> eine Kette (Berechnung → Trennung → Schifter → weitere Bauteile → Mengen), und ihre Namen tragen
> dieselbe Sache. Die Namensheuristik funktioniert genau dann, wenn die Namen nicht lügen.*

## Vierter Fall der Schicht-Frage — wieder ohne Werkzeug

```text
Registry     0 Treffer
Zusage       enginePanelSparren.test.ts deutet auf ein PANEL, nicht auf ein Werkzeug
```

**Wie W-01 und W-05: Rechenschicht gebaut, keine Werkzeugschicht.** *Der Dachstuhl entsteht aus dem
Dach, er wird nicht angeklickt. `4-BEDIENUNG` sagt „Nichts Eigenes" und beschreibt, wo die Ergebnisse
erscheinen — der Test-Dateiname nennt ein Panel, das ist die Spur.*

## BEFUND 1 — hier steckt BEMESSUNG, nicht nur Geometrie

```text
sparrenBerechnung.ts, Kopf:  "Sparren-Vorbemessung nach EUROCODE"
Exporte:  Schneezone · bodenschneelast() · formbeiwertSchnee() · Holzklasse
          berechneSparren()
```

> **Das ist Tragwerksplanung, nicht Zeichengeometrie.** Schneelastzonen, Formbeiwerte und
> Holzfestigkeitsklassen sind normative Größen; `berechneSparren()` liefert eine **Vorbemessung**.
>
> **Und dafür gilt eine Regel, die älter ist als dieses Blatt:** die Statik-Fachlinse trennt
> ausdrücklich *„Geometrie (jetzt) von Bemessung (Fach-Freigabe/später)"*. **Der Code enthält beides.**
>
> **Was dieses Blatt tut:** es beschreibt, **dass** die Bemessung da ist, **was** sie annimmt
> (Einfeld-Sparren, Eurocode, Schneezone als Eingabe) und **dass sie eine Vorbemessung ist** — mit
> der Grenze in `7-GRENZEN`. **Was es nicht tut:** die Bemessung fachlich bewerten oder freigeben.
>
> *Ein Doku-Blatt, das eine Eurocode-Vorbemessung beschreibt, ohne ihren Vorbemessungs-Charakter zu
> benennen, erzeugt genau das Missverständnis, gegen das die Fachlinie gebaut ist. **Das ist keine
> Formalie: wer eine Vorbemessung für eine Bemessung nimmt, baut ein Dach danach.***

## BEFUND 2 — W-21s Materialquelle ist nicht ausgewertet

```text
Register W-21:   "Quelle M-01/M-02"
BESTAND-YAMA:    profi_holzbau_solar_cad.tsx   2.021 Zeilen, 46 Trigonometrie-Stellen
                 "Holzbau/Sparrenkonstruktion — fuer W-21"
VORGEHEN.md:     Schritt 1 von 6: "profi_holzbau_solar_cad.tsx auswerten
                 -> Sparrenkonstruktion -> W-21"   NOCH NICHT GEFAHREN
```

> **Das Register nennt eine Quelle, die niemand gelesen hat.** *Kein Blocker: die fünf Module
> existieren unabhängig davon und sind sechsfach abgesichert. **Aber das Blatt darf nicht behaupten,
> W-21 sei vollständig beschrieben, solange 2.021 Zeilen Fremdcode als Quelle geführt und nicht
> ausgewertet sind.*** Das ist ein eigener Messauftrag — dieselbe Bauart wie A-12 für F-026 — und
> gehört **nicht** in dieses Blatt.

## DECISION

```text
Quelle       alle fuenf Module + die sechs Zusagen
NICHT Quelle M-02 (nicht ausgewertet) — als offener Punkt benannt, nicht verwendet
2-FUNKTION   die KETTE zeigen: Berechnung -> Trennung -> Schifter -> weitere Bauteile
             -> Mengen. Drei der fuenf Koepfe sagen "aus der ECHTEN, bereits in der
             3D-Geometrie erzeugten Liste" — die Module rechnen NICHT selbst das Dach,
             sie aggregieren, was die Engine erzeugt hat. Das ist der Kern.
3-FORMELN    nur F-Nummern. Register nennt F-001, F-030. ABER: bodenschneelast und
             formbeiwertSchnee sind NORMATIVE Groessen, keine Geometrieformeln —
             wenn die Sammlung sie nicht kennt, wird das GEMELDET.
5-CODE       "angebunden aus" mit allen fuenf Modulen und der Schichtzuordnung
7-GRENZEN    DREI Fragen, alle am Code:
               (a) der Vorbemessungs-Charakter (Befund 1) — die wichtigste Grenze
               (b) istSicherTrennbar() IST ein Grenzfall-Entscheider, gebaut: auslesen
               (c) OFFENE_HOLZBAUTEILE — der Name sagt, dass etwas offen ist. MESSEN,
                   was darin steht; es ist wahrscheinlich die Liste der noch nicht
                   erfassten Bauteile und damit eine gebaute Selbstauskunft ueber Grenzen
```

## Nicht-Ziele

- **Keine fachliche Bewertung der Bemessung.** Das Blatt beschreibt, es prüft nicht. *Eine
  Fach-Freigabe ist eine eigene Sache und nicht Planner-Zuständigkeit.*
- **Keine Auswertung von M-02.** Eigener Messauftrag (Befund 2).
- **Kein Registry-Eintrag.** W-21 ist eine Schicht.
- **Keine Änderung an den fünf Modulen** und keiner ihrer sechs Zusagen.
- **Keine Aussage über Lattung.** *Der Werkzeugname sagt „Sparren und **Lattung**" — in den fünf
  Modulen habe ich kein Lattungs-Modul gefunden. Das Blatt hat es zu benennen, nicht zu erfinden.*

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-21-sparren-und-lattung/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-21 LEER -> BESCHRIEBEN
                                                     + alle fuenf Module als Fundstelle
```

*NICHT im Scope: `resources/**`, M-02, die F-Liste des Registers (N1-Frage).*

## Wiederverwendungsprüfung (§5)

```text
die fuenf Module        VORHANDEN, 496 Z — Quelle, unangetastet
ihre Dateikoepfe        nennen Verfahren, Norm und Herkunft — drei sagen ausdruecklich
                        "aus der ECHTEN, bereits erzeugten Liste"
6 dedizierte Zusagen    VORHANDEN, beste Absicherung der Klasse — Quelle fuer 6-PRUEFUNG
istSicherTrennbar()     VORHANDEN — Grenzfall-Entscheider, gebaut
OFFENE_HOLZBAUTEILE     VORHANDEN — gebaute Selbstauskunft ueber die eigenen Grenzen
auswechslung.ts         VERWANDT (Sicher-Entscheidung, laut sparrenTrennung-Kopf) —
                        NICHT im Scope, aber im Blatt zu verlinken
W-01/1, W-05/1          Muster fuer SCHICHT-Blaetter
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite bleibt
                                                             unveraendert gruen (ohne Zahl)
```

**Erstnutzer:** *der Generator von W-21 Stufe 2 — und jede Rolle, die eine Holzliste liest: sie muss
wissen, dass die Module **aggregieren**, was die Engine erzeugt hat, und nicht selbst konstruieren.*

## Akzeptanzkriterien

**W-21/1-1 (P1, kein Platzhalter):** keiner mehr in den sieben Blättern. *Zählweise: alle
`<…>`-Klammern.*

**W-21/1-2 (P1):** `3-FORMELN` nennt nur F-Nummern, keine ausgeschriebene Formel.

**W-21/1-3 (P1, F-Nummern belegt — und die Lücke gemeldet):** `F-001`, `F-030` je mit Zeilennummer.
**Für `bodenschneelast()` und `formbeiwertSchnee()` gilt: findet sich keine F-Nummer, wird das
gemeldet** — es sind normative Größen, keine Geometrieformeln, und die Sammlung kennt sie
möglicherweise zu Recht nicht. *Eine erfundene F-Nummer wäre schlimmer als eine gemeldete Lücke.*

**W-21/1-4 (P1, `7-GRENZEN` — der Vorbemessungs-Charakter ist die erste Zeile):** Das Blatt sagt
ausdrücklich, dass `berechneSparren()` eine **Vorbemessung** nach Eurocode ist, mit ihren Annahmen
(Einfeld-Sparren, Schneezone als Eingabe) — **und dass sie keine Bemessung ersetzt.** *Wer eine
Vorbemessung für eine Bemessung nimmt, baut ein Dach danach. **Das ist die wichtigste Zeile dieses
Blattes.***

**W-21/1-5 (P1, `OFFENE_HOLZBAUTEILE` ist ausgelesen):** Das Blatt nennt, **was** darin steht — es
ist eine gebaute Selbstauskunft über die Grenzen des Werkzeugs. *Am Code gemessen, nicht vom Namen
abgeleitet.*

**W-21/1-6 (P1, die Aggregations-Natur):** `2-FUNKTION` sagt, dass drei der fünf Module **aus der
bereits erzeugten 3D-Liste aggregieren** und nicht selbst konstruieren. *Ohne diesen Satz liest
jemand `holzMengenAusListe()` als Konstruktion.*

**W-21/1-7 (P1, Herkunft):** `5-CODE` sagt „angebunden aus …" mit allen fünf Modulen.

**W-21/1-8 (P1, die Lattung wird benannt — mit den Fundstellen):** Das Blatt sagt, dass **kein
Lattungs-Modul existiert**, obwohl der Werkzeugname sie nennt, **und wo das Wort trotzdem vorkommt**:

```text
KEIN Modul in geometry/ (0 Treffer auf latt/konterlatt/traglatt im Dateinamen)
Wort "Lattung" kommt vor in:
  geometry/sparrenBerechnung.ts     vermutlich als LASTANNAHME (Eigengewicht der
                                    Dachhaut) — zu MESSEN, nicht anzunehmen
  geometry/dachformVorlagen.ts      zu MESSEN
  app/dashboard/enginePanels.ts     Anzeige-Ebene
```

> **Der Unterschied ist wesentlich und muss im Blatt stehen:** *Lattung als **Lastanteil** in einer
> Sparrenbemessung ist etwas anderes als Lattung als **Gegenstand** (Lattenabstand aus Deckungsart,
> Konterlattung, Traglattung). **Das erste ist offenbar gebaut, das zweite nicht.*** Der Generator
> misst, welches von beiden zutrifft — *nicht erfinden, nicht verschweigen, und nicht das eine für
> das andere nehmen.*

**W-21/1-9 (P1, M-02 ist als offen benannt):** Das Blatt nennt, dass die Register-Quelle **M-02
(2.021 Zeilen) nicht ausgewertet** ist. *Ein Blatt, das eine ungelesene Quelle als Grundlage führt,
behauptet Vollständigkeit, die es nicht hat.*

**W-21/1-10 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite **unverändert** grün
(ohne Zahl — W-01N-Regel).

**W-21/1-11 (P1, Register mitgeführt):** Reifegrad **und** alle fünf Module als Fundstelle.

**W-21/1-12 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **mindestens zwei
Befehlszeilen und zwei Ausgabewerte, je Ort einer**. *E2 aus Prüfung 03.*

## Kantenliste

```text
Schneezone nicht gesetzt / unbekannt   -> MESSEN, was bodenschneelast liefert
Sparren nicht sicher trennbar          -> istSicherTrennbar() ist der gebaute Entscheider
Oeffnung groesser als Sparrenabstand   -> MESSEN (sparrenTeilstuecke)
Schifter an einer Kehle vs. am Grat    -> klassifiziereSchifter unterscheidet; benennen
leere Holzliste                        -> was liefern die drei Aggregatoren? MESSEN
Holzklasse ausserhalb der Norm         -> Grenze der Vorbemessung
Lattung                                -> KEIN Modul gefunden (W-21/1-8)
M-02 bringt spaeter neue Formeln        -> dann ist das Blatt nachzufuehren, kein Fehler
```

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** Wird `berechneSparren()` je in einem Angebot, einer Statik oder einer Freigabe
zitiert, **ohne dass der Vorbemessungs-Charakter mitzitiert wird**, hat `W-21/1-4` nicht gewirkt.
*Das ist das einzige Entdeckungssignal dieser Klasse, das nach außen zeigt — alle anderen bleiben im
Repo.*

## Konfliktprüfung (§5)

```text
A-12     ENTWURF     FORMELSAMMLUNG + VORGEHEN + BERICHT-A-12    KEINE Beruehrung
W-01N    ENTWURF     W-01-Blatt + FAHRPLAN                       KEINE Beruehrung
W-04/1 · W-05/1 · W-08/1 · W-11/1 · W-13/1   ENTWURF   werkbank/W-xx/** + REGISTER.md
W-21/1   DIESES      werkbank/W-21/** + REGISTER.md
-> SECHS Blaetter teilen REGISTER.md, je eine Zeile plus Fundstellen, zeilenweise disjunkt.
   §3 loest es; belegt in W-21/1-12.
§3 GEMESSEN 11.08. (korrigiert, siehe docs/MELDUNG-ERFUNDENE-SPERRE-A-12.md):
   grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md   -> 0
   A-12 traegt status: ENTWURF, NICHT IN_ARBEIT.
   -> §3 sperrt W-21/1 NICHT. Es darf in IN_ARBEIT, sobald DoR durch ist.
   Der Vorrang von A-12 (F-026 ist gelb, W-07/W-08 haengen fachlich daran) ist eine
   planerische EMPFEHLUNG, kein Verbot. Die Reihenfolge entscheidet der Plan-Pruefer.
FACHLICHE Beruehrung: W-21 braucht laut Register W-07 (Dach) — der Dachstuhl entsteht aus
   dem Dach. Kein Dateikonflikt, aber es ist der Grund, warum W-21 nach W-07 GEBAUT werden
   sollte, auch wenn es JETZT beschrieben werden kann.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "Runde 2: W-05 -> W-21 -> W-22"
grobzahl: "stimmt — 496 Zeilen, erste zutreffende Grobzahl der Klasse"
befund_1: "sparrenBerechnung ist Eurocode-VORBEMESSUNG, nicht Geometrie — Fach-Freigabe
           ist eine eigene Sache, das Blatt beschreibt und bewertet nicht"
befund_2: "Register-Quelle M-02 (profi_holzbau_solar_cad.tsx, 2.021 Z) ist nicht
           ausgewertet — eigener Messauftrag, Bauart wie A-12 fuer F-026"
befund_3: "kein Lattungs-Modul gefunden, obwohl der Werkzeugname sie nennt"
```


## §11 — Bericht W-21/1 (Generator, 12.08.2026)

```yaml
auftrag: "W-21/1"
zustand: CODE_FERTIG
bau_commit: "992d5d76"
in_arbeit_commit: "9bd728fe"
basis: "c9325929"

kriterien:
  W-21/1-1:  GRUEN   # 34 -> 0, nach BEIDEN Zaehlweisen (woertlich und Platzhaltermuster)
  W-21/1-2:  GRUEN   # nur Nummern
  W-21/1-3:  GRUEN   # beide als NICHT vorhanden gemeldet + die umgekehrte Luecke benannt
  W-21/1-4:  GRUEN   # Vorbemessung ist die erste Ueberschrift in 7-GRENZEN
  W-21/1-5:  GRUEN   # OFFENE_HOLZBAUTEILE woertlich ausgelesen, vier Eintraege
  W-21/1-6:  GRUEN   # drei der fuenf aggregieren; schifterListe als GEMISCHT benannt
  W-21/1-7:  GRUEN   # alle fuenf Module mit Zeilen und Exportlisten
  W-21/1-8:  GRUEN   # Lattung mit fuenf Fundstellen — und die Auftragsvermutung widerlegt
  W-21/1-9:  GRUEN   # M-02 als ungelesen im Blatt UND im Register
  W-21/1-10: GRUEN   # drei Richtungen 0/0/0, Suite unveraendert gruen (ohne Zahl, W-01N)
  W-21/1-11: GRUEN   # Register: BESCHRIEBEN + alle fuenf Module
  W-21/1-12: GRUEN   # 9bd728fe: 2 Befehlszeilen, 2 Ausgabewerte

wo_ich_dem_auftrag_widerspreche:
  punkt: "W-21/1-8 — der Auftrag vermutet: Lattung als Lastanteil sei gebaut, als Gegenstand nicht"
  gemessen: "beides ist gebaut. Last: sparrenBerechnung.ts:63. Menge: holzMengen.ts:32
             (Konterlatten, lfm) und :34 (Traglatten, lfm), aus der ECHTEN 3D-Liste."
  was_wirklich_fehlt: "der Schritt DAZWISCHEN — niemand leitet den Lattenabstand aus der
                       Deckungsart ab; konterlattungMm wird ausserhalb der eigenen Datei von
                       KEINEM Produktivcode gelesen, nur von einem Test (dachformVorlagen.test.ts:533)."
  bemerkung: "Der Auftrag verlangt ausdruecklich zu messen statt anzunehmen — genau daran
              faellt die Vermutung. Nicht erfunden, nicht verschwiegen."

f_nummern:
  F-001: "NICHT im Code — kein Math.hypot/Math.sqrt in KEINEM der fuenf Module"
  F-030: "NICHT im Code — nichts wird extrudiert; die Staebe kommen fertig aus der 3D-Engine"
  stimmigkeit: "drei der fuenf aggregieren aus einer bereits erzeugten Liste — sie brauchen
                keine Geometrieformel, weil sie keine Geometrie erzeugen"
  umgekehrte_luecke: "bodenschneelast() und formbeiwertSchnee() RECHNEN, haben aber keine
                      F-Nummer. Normative Groessen (DIN EN 1991-1-3 / EN 1995-1-1) gehoeren
                      nicht in ein Geometrie-Verzeichnis. Eine erfundene Nummer waere schlimmer."

benannt_aber_NICHT_gemessen:
  - "HolzStueckRef ist doppelt definiert (schifterListe.ts:134 und holzBauteile.ts:22), kein
     Import verbindet sie — dieselbe Lage wie MassPunkt bei W-11. OB die Felder heute
     deckungsgleich sind, habe ich nicht geprueft. Im Blatt als ungemessen gekennzeichnet."
  - "M-02 (2.021 Zeilen) — nicht gelesen, weder fuer dieses Blatt noch erkennbar fuer den Code"

eigener_fehler_vor_dem_melden:
  was: "eine Zeilenangabe falsch — holzMengen.ts:31 statt :32 fuer die Konterlattenlaengen"
  gefunden: "durch die inhaltliche Zeilenpruefung, die seit W-11 vor jedem Melden laeuft"
  danach: "18 Zeilenangaben geprueft, 0 ueber dem Dateiende, 17 Schluesselstellen inhaltlich"
  bilanz: "dritter Auftrag in Folge mit dieser Klasse — aber alle drei Male VOR der Meldung
           gefunden. Die Pruefung faengt sie zuverlaessig; das Abzaehlen bleibt unzuverlaessig."

browserabnahme: "entfaellt — reine Dokumentblaetter"
ballbesitz: evaluator
```

---

## Evaluator-Votum (§11) — 12.08.2026

```yaml
auftrag: W-21/1
commit: 992d5d76          # Bau; Basis c9325929
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "achtzehn Fundstellen einzeln geoeffnet · fuenf Modul-Zeilenzahlen selbst nachgezaehlt ·
  die Formel-Verneinung gegen alle fuenf Module gegengeprueft"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde: []
```

### Messtisch — ALLE ZWÖLF Zeilen, auch die mit der Antwort „keine"

*Nach dem §10-Befund gegen mein W-04-Votum (sieben von zehn Zeilen) führe ich hier jede
Kriterienzeile einzeln, ohne Ausnahme.*

```text
-1   Platzhalter, vier Muster                          0
-2   3-FORMELN: kein '=', kein atan2. 'Math.' 2x und 'sqrt/hypot' je 1x —
     beide in der VERNEINUNG bzw. als Code-Beleg, nicht als Rechnung
-3   achtzehn Fundstellen, ALLE einzeln geoeffnet      keine laeuft ins Leere
     Luecke gemeldet: F-001 und F-030 stehen NICHT im Code — von mir
     gegengeprueft, alle fuenf Module tragen hypot 0 und Math.sqrt 0
-4   Vorbemessungs-Charakter ist die ERSTE Ueberschrift von 7-GRENZEN
     ("Das Wichtigste zuerst: es ist eine VORBEMESSUNG … ersetzt KEINE pruefbare Statik")
-5   OFFENE_HOLZBAUTEILE ausgelesen: alle vier Eintraege im Blatt genannt
     (Mittelpfette · Schwelle · Wechselholz · Schifter), Code holzBauteile.ts:45
-6   2-FUNKTION nennt die Aggregations-Natur                    ja
-7   5-CODE listet alle FUENF Module mit Zeilenzahl und Ausfuhren
-8   Lattung: 15 Nennungen, und die zwei Fundstellen ausserhalb der fuenf Module
     sind belegt — dachWerte.ts:20 "battenDist: 0.05 // Lattenabstand"
     und dachformVorlagen.ts:118 "lattmassAbhaengigVonProdukt"
-9   M-02 als offene Quelle benannt                              2 Nennungen
-10  resources/ im Bau-Commit 0 Pfade  ·  Suite 1692/1692
-11  Register: alle fuenf Module als Fundstelle
-12  §3-Beleg in 9bd728fe: 2 Befehlszeilen, 2 Ausgaben
```

### Zwei Stellen, an denen ich das Blatt gegen den Code gehalten habe

**Die fünf Zeilenzahlen — selbst nachgezählt, nicht abgelesen:**

```text
sparrenBerechnung.ts  Blatt 131  gemessen 131      schifterListe.ts  Blatt 152  gemessen 152
sparrenTrennung.ts    Blatt  67  gemessen  67      holzBauteile.ts   Blatt  82  gemessen  82
holzMengen.ts         Blatt  64  gemessen  64
```

**Die Formel-Verneinung — gegen alle fünf Module geprüft, nicht gegen eines:**

```text
Blatt: "F-001 … NEIN — kein Math.hypot"      alle fuenf Module: hypot 0, Math.sqrt 0
Blatt: "sparrenBerechnung.ts:90 benutzt Math.cos fuer die senkrechte Lastkomponente"
CODE   :90   const cosA = Math.cos(a);
```

> **Das Blatt meldet seine eigene Lücke**, statt sie zu glätten: die zwei im Register geführten
> F-Nummern stehen **nicht** im Code, und das steht mit ⚠ im Register. *Genau das verlangt `-3` im
> zweiten Halbsatz — eine Formel, die in der Sammlung steht und im Code fehlt, wird gemeldet und
> nicht hineingeschrieben.*

**`-12` zum vierten Mal in Folge im ersten Anlauf** (W-04, W-11, W-05, W-21). *E2 aus
Prozessprüfung 03 hält jetzt über vier Blätter.*

---

## Release-Prüfung (§10, Sammel-Kontrolle 2) — 12.08.2026

```yaml
auftrag: W-21/1
abnahme_commit: e5b4c219   # Evaluator-Votum; gemessen wurde 992d5d76 (Bau, Basis c9325929)
release_commit: 50e968e9   # HEAD bei dieser Prüfung
votum: RELEASE_FREI
ci: pass                   # npm run test:hausplaner selbst gefahren: tests 1692, pass 1692, fail 0
artefakte_reproduzierbar: nicht_anwendbar   # Doku-Stufe: kein Bundle, kein Build-Artefakt im Scope
migration: nicht_anwendbar
rueckweg: nicht_anwendbar   # nichts veröffentlicht; Rückweg wäre `git revert 992d5d76`, acht Doku-Dateien
smoke_test_plan: "Entfällt — reine Dokumentblätter, keine sichtbare oder betriebliche Wirkung."
befunde: []
```

### Die Pflichtfrage der Sammel-Kontrolle — gezählt

**Trägt der Messtisch JEDE Kriterienzeile?**

```text
Kriterien im Blatt (Abschnitt Akzeptanzkriterien)   12   (W-21/1-1 … -12)
Zeilen im Votum-Messtisch                           12   (-1 -2 -3 -4 -5 -6 -7 -8 -9 -10 -11 -12)
                                                    ->  12 von 12, lückenlos
```

**Der Evaluator führt die Zeilen ausdrücklich als Konsequenz aus dem W-04-Befund** — *„Nach dem
§10-Befund gegen mein W-04-Votum (sieben von zehn Zeilen) führe ich hier jede Kriterienzeile
einzeln, ohne Ausnahme."* **Und er führt auch die Zeilen mit der Antwort „keine"**, die genau die
sind, die beim Überfliegen wegfallen: `-3` weist aus, dass `F-001` und `F-030` **nicht** im Code
stehen, gegengeprüft über **alle fünf** Module (`hypot 0`, `Math.sqrt 0`) statt über eines.

> *Das ist der Unterschied, den Kontrolle 1 sichtbar gemacht hat: ein Kriterium, dessen richtige
> Antwort „keine" lautet, ist keine Zeile, die man weglassen darf — es ist die Zeile, an der man
> sieht, ob gemessen oder vermutet wurde.*

### Kette, Scope, Stichprobe

```text
Kette      dcf0071c (BEREIT) -> 9bd728fe (IN_ARBEIT) -> 992d5d76 (Bau)
           -> 37cd8890 (CODE_FERTIG) -> e5b4c219 (ABGENOMMEN) -> HEAD
           je git merge-base --is-ancestor, Exit 0                      5/5
Basis      c9325929 -> 9bd728fe  Exit 0
Scope      git show 992d5d76 --name-only: 8 Dateien = 7 Blätter + REGISTER.md
           Pfade unter resources/ oder scripts/:                        0
Votum-SHA  Votum nennt 992d5d76 = Bau-Commit                            deckungsgleich
Blattstand git diff 992d5d76..HEAD -- W-21-sparren-und-lattung/         0 Dateien
Ergebnis   Platzhalter über alle sieben Blätter                         0
Register   Z.43 W-21 BESCHRIEBEN · Z.161-165 alle FÜNF Module mit Zeilen und Ausfuhren,
           M-02 ausdrücklich als ungelesen geführt
```

**Zur Herkunft des `BEREIT`-Commits:** `dcf0071c` trägt eine Planner-Botschaft, weil die beiden
`BEREIT`-Blöcke dort als unbenannter Beifang mitgesichert wurden. *Die Richtigstellung steht im
Block selbst (`herkunft_w21_w22`) und bei `66fb2476`.* **Für die Kette ändert das nichts** — der
Zustand ist an einem Commit festgeschrieben und der Commit ist Vorfahr des Baus; für die
Statuswahrheit ist es der bekannte Bauart-Befund, der beim Planner liegt.

**Urteil: `RELEASE_FREI`.** *Ohne Befund. Der Messtisch trägt seine zwölf Zeilen, der Bau-Commit
trägt acht Doku-Dateien und keinen Produktivpfad, und das Blatt meldet seine eigene Lücke, statt
sie zu glätten.*
