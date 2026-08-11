# W-11 Stufe 1 — Maß und Bemaßung BESCHREIBEN: drei Module, zwei Schichten

```yaml
auftrag: "W-11/1"
werkzeug: "W-11 Mass und Bemassung"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-11 aus bemassung.ts + masskette.ts + masseingabe.ts ableiten"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 7a415aff
prioritaet: P1
anlass: "Yamas Ansage 10.08. — Klasse A vollstaendig, sorgfaeltig, behutsam; Runde 1, drittes Blatt"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt. Kein W-11-Blatt lag als Auftrag vor."
muster: "W-01/1, W-02/1, W-08/1, W-04/1"
```

## Ist-Zustand — drei Module, und eines davon ist eine andere Schicht

**Anbindungsmessung je Modul an den Exporten und Importen gefahren:**

```text
RECHENSCHICHT — Geometrie
geometry/masskette.ts                    118 Zeilen, 7 Exporte
  MassPunkt · MassSegment · masskette() · Bbox
  GrundrissMassketten · grundrissMassketten() · punkteMassketten()
geometry/bemassung.ts                    108 Zeilen, 6 Exporte
  BemPunkt · BemWand · BemOeffnung · AchsKetten · Bemassung · bemassung()
  IMPORTIERT aus masskette:  masskette, MassSegment, Bbox   (Z.18)

EINGABESCHICHT — Interaktion, nicht Geometrie
geometry/masseingabe.ts                  169 Zeilen, 9 Exporte
  MassPunkt · istBrauchbareLaenge() · richtungAus() · punktAusLaenge()
  MassEingabe · oeffneMit() · wechsleFeld() · tippe() · massEingabeText()
  -> oeffneMit / wechsleFeld / tippe sind TASTATUR- und FELDVERHALTEN.
     Kein Import, keine Abhaengigkeit zu den beiden anderen.

Registry-Werkzeug 'bemassen'             vorhanden, label "Bemassen"
Zusagen                                  4 dediziert (bemassung, masskette,
                                         masseingabe, punkteMassketten)
Register-Abhaengigkeit                   W-11 braucht W-13 (Auswahl)
Register-Formeln                          F-001, F-002, F-003
                                         ------------------------------
                                         395 Zeilen, 22 Exporte
```

> ### Kein Ausschluss — aber zwei Schichten in einem Werkzeug
>
> **Anders als bei W-02, W-04, W-08 und W-13 gehören hier alle drei Module dazu.** Das ist das erste
> Blatt der Runde ohne Falschzuordnung. *Aber `masseingabe.ts` ist keine Geometrie: `oeffneMit`,
> `wechsleFeld` und `tippe` beschreiben, was passiert, wenn der Nutzer eine Zahl eintippt und `Tab`
> drückt. Das ist Interaktionsverhalten in einem `geometry/`-Ordner.*
>
> **Ich schließe es nicht aus** — die Maßeingabe gehört zum Bemaßungswerkzeug, und sie liegt richtig
> bei W-11. **Aber das Blatt muss die zwei Schichten trennen**, sonst liest jemand `tippe()` als
> Rechenschritt.

## Ein Fund, der geprüft und für harmlos befunden wurde

```text
MassPunkt ist ZWEIMAL definiert, unabhaengig und ohne Import:
  masskette.ts:9     export interface MassPunkt { x: number; y: number; }
  masseingabe.ts:25  export interface MassPunkt { x: number; y: number; }
Zeichenweise verglichen -> IDENTISCH.
```

> **Das ist eine Doppelung, aber keine zweite Wahrheit** — beide Fassungen sind gleich, und
> TypeScript prüft strukturell, nicht nach Namen: die Typen sind austauschbar. **Es ist deshalb kein
> Befund, sondern eine Beobachtung mit einer Bedingung:** *ändert jemand eine der beiden — etwa um
> ein `z` zu ergänzen — divergieren sie **stumm**, weil kein Import sie verbindet. Der Compiler
> schweigt, solange die Verwendung passt.*
>
> **Das gehört in `7-GRENZEN`, nicht in einen Auftrag.** Ich verlange keinen Umbau: eine gemeinsame
> Definition wäre sauberer, aber `masseingabe` ist bewusst importfrei, und das hat Wert. **Ich melde
> die Bedingung, unter der es gefährlich wird.**

## DECISION

```text
Quelle       alle drei Module + Registry 'bemassen' + die vier Zusagen
2-FUNKTION   ZWEI Abschnitte: Rechenschicht (masskette, bemassung) und
             Eingabeschicht (masseingabe). Die Trennung ist der Kern des Blattes.
             Dazu die EINE Abhaengigkeit: bemassung -> masskette (Z.18), sonst keine.
3-FORMELN    nur F-Nummern. Register nennt F-001 (Abstand), F-002 (Richtungswinkel),
             F-003 (Lotfusspunkt) — jede im Code belegen. richtungAus() und
             punktAusLaenge() in masseingabe sind die Kandidaten fuer F-002.
5-CODE       "angebunden aus" mit allen drei Modulen, Exportliste, und der
             Schichtzuordnung je Modul
7-GRENZEN    zwei Fragen, beide am Code zu messen:
               (a) was liefert masskette() bei entarteter Eingabe (0 Punkte, 1 Punkt,
                   deckungsgleiche Punkte)?
               (b) was tut istBrauchbareLaenge() — die Funktion IST der Grenzfall
                   der Eingabeschicht, sie prueft schon; das Blatt liest sie aus
                   statt sie zu erfinden
             DAZU die MassPunkt-Doppelung als benannte Bedingung.
```

## Nicht-Ziele

- **Kein Ausschluss.** Alle drei Module gehören zu W-11 — das erste Blatt der Runde ohne
  Falschzuordnung.
- **Kein Umbau der `MassPunkt`-Doppelung.** Sie wird **benannt**, nicht behoben. *Eine gemeinsame
  Definition wäre sauberer, aber `masseingabe.ts` ist bewusst importfrei.*
- **Keine Aussage über die Darstellung.** Wie eine Maßkette **gezeichnet** wird, ist Renderer-Sache.
- **Keine Änderung an den drei Modulen** und keiner ihrer vier Zusagen.
- **Keine Registry-Einträge.** `bemassen` existiert.

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-11-mass-und-bemassung/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-11 LEER -> BESCHRIEBEN
                                                     + alle drei Module als Fundstelle
```

*NICHT im Scope: `resources/**`, die F-Liste des Registers (N1-Frage), `FORMELSAMMLUNG.md`.*

## Wiederverwendungsprüfung (§5)

```text
die drei Module           VORHANDEN, 395 Z — Quelle, unangetastet
Registry 'bemassen'       VORHANDEN — Quelle fuer 4-BEDIENUNG
4 dedizierte Zusagen      VORHANDEN, bestes Verhaeltnis der Runde — Quelle fuer 6-PRUEFUNG
istBrauchbareLaenge()     VORHANDEN — der Grenzfall der Eingabeschicht ist GEBAUT,
                          das Blatt liest ihn aus statt ihn zu erfinden
W-01/1 … W-04/1           Muster fuer Struktur, Kriterienform, Rot-Form
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite unberuehrt
```

**Erstnutzer:** *der Generator von W-11 Stufe 2 — und die Rolle, die W-13 (Auswahl) beschreibt: das
Register führt W-11 als **abhängig von W-13**, und diese Abhängigkeit ist im Blatt zu prüfen, nicht
zu übernehmen.*

## Akzeptanzkriterien

**W-11/1-1 (P1, kein Platzhalter):** keiner mehr in den sieben Blättern. *Zählweise: alle
`<…>`-Klammern.*

**W-11/1-2 (P1):** `3-FORMELN` nennt nur F-Nummern, keine ausgeschriebene Formel.

**W-11/1-3 (P1, F-Nummern belegt):** `F-001`, `F-002`, `F-003` je mit Zeilennummer — oder
ausdrücklich als **nicht gefunden** benannt. *Das Register nennt sie; ob sie im Code stehen, ist zu
messen. **Eine Registerangabe ist keine Fundstelle** — vierter Fall der N1-Frage.*

**W-11/1-4 (P1, die zwei Schichten sind getrennt):** `2-FUNKTION` trennt Rechenschicht
(`masskette`, `bemassung`) von Eingabeschicht (`masseingabe`) und nennt die **einzige** Abhängigkeit
(`bemassung → masskette`, Z.18). *Ohne diese Trennung liest jemand `tippe()` als Rechenschritt.*

**W-11/1-5 (P1, `7-GRENZEN`):** beantwortet **beide** Fragen am Code gemessen — entartete Eingabe
bei `masskette()`, und was `istBrauchbareLaenge()` prüft. *Die Funktion **ist** der Grenzfall; das
Blatt liest sie aus statt einen zu erfinden.*

**W-11/1-6 (P1, die `MassPunkt`-Doppelung ist benannt):** `7-GRENZEN` nennt, dass `MassPunkt` in
`masskette.ts:9` **und** `masseingabe.ts:25` unabhängig definiert ist, dass beide **heute identisch**
sind, und **unter welcher Bedingung es gefährlich wird**: ändert eine Seite, divergieren sie stumm,
weil kein Import sie verbindet. *Ohne diesen Satz wird die Doppelung entweder übersehen oder
voreilig „aufgeräumt".*

**W-11/1-7 (P1, die Abhängigkeit zu W-13 wird GEPRÜFT):** Das Blatt sagt, **ob** und **wo** die
Bemaßung die Auswahl braucht — am Code gemessen, nicht aus dem Register übernommen. *Das Register
behauptet „W-11 braucht W-13"; drei von vier Registerangaben waren in dieser Runde ungenau.*

**W-11/1-8 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite unverändert grün.

**W-11/1-9 (P1, Register mitgeführt):** Reifegrad **und** alle drei Module als Fundstelle.

**W-11/1-10 (P1, §3 wird BELEGT):** Befehl mit Ausgabe für „kein anderer Auftrag auf `IN_ARBEIT`",
an beiden Orten, im selben Commit. **Zählbare Form (E2 aus Prüfung 03): mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer.** *Wortgleich zu `W-01/1-8`.*

## Kantenliste

```text
masskette() mit 0 oder 1 Punkt         -> MESSEN, was zurueckkommt
zwei deckungsgleiche Punkte            -> Laenge 0, was meldet es? A-10-Klasse
istBrauchbareLaenge falsch             -> die Eingabeschicht hat den Grenzfall GEBAUT,
                                          das Blatt liest ihn aus
MassPunkt geaendert auf einer Seite    -> stumme Divergenz (W-11/1-6)
tippe() mit nicht-numerischer Eingabe  -> Eingabeschicht, MESSEN
Bemassung ohne Auswahl                 -> haengt W-11 wirklich an W-13? (W-11/1-7)
grundrissMassketten vs punkteMassketten -> zwei Einstiege, Unterschied benennen
Darstellung der Masskette              -> Renderer, NICHT hier (Nicht-Ziel)
```

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** wie bei den drei Vorgängern. *Zusätzlich hier: findet Stufe 2, dass die
`MassPunkt`-Doppelung doch zusammengelegt werden muss, ist das ein Befund an den Planner — **keine
stille Zusammenlegung**, weil `masseingabe.ts` bewusst importfrei ist.*

## Konfliktprüfung (§5)

```text
W-08/1   ENTWURF    werkbank/W-08/** + REGISTER.md
W-04/1   ENTWURF    werkbank/W-04/** + REGISTER.md
W-13/1   ENTWURF    werkbank/W-13/** + REGISTER.md
W-11/1   DIESES     werkbank/W-11/** + REGISTER.md
A-12     ENTWURF    FORMELSAMMLUNG + VORGEHEN     KEINE Beruehrung
-> VIER Blaetter teilen REGISTER.md, je eine Zeile plus Fundstellen, zeilenweise disjunkt.
   §3 loest es (nur einer IN_ARBEIT); belegt wird das in W-11/1-10.
FACHLICHE Beruehrung mit W-13: das Register fuehrt W-11 als abhaengig davon —
   zu PRUEFEN, nicht zu uebernehmen (W-11/1-7). Kein Dateikonflikt.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "Runde 1 damit vollstaendig geschnitten: W-08, W-04, W-11"
besonderheit: "erstes Blatt der Runde OHNE Falschzuordnung — alle drei Module gehoeren dazu"
beobachtung_ohne_forderung: "MassPunkt doppelt definiert, heute identisch, ohne Import verbunden"
```


## §11 — Bericht W-11/1 (Generator, 11.08.2026)

```yaml
auftrag: "W-11/1"
zustand: CODE_FERTIG
bau_commit: "0299e5ca"
in_arbeit_commit: "a436d8a3"
basis: "7a415aff"

kriterien:
  W-11/1-1:  GRUEN   # 28 -> 0, Zaehlweise: alle <…>
  W-11/1-2:  GRUEN   # nur Nummern, keine ausgeschriebene Formel
  W-11/1-3:  GRUEN   # F-001 belegt; F-002 und F-003 ausdruecklich als NICHT GEFUNDEN benannt
  W-11/1-4:  GRUEN   # Rechen- gegen Eingabeschicht, einzige Abhaengigkeit bemassung.ts:18
  W-11/1-5:  GRUEN   # masskette() entartet + istBrauchbareLaenge() vollstaendig ausgelesen
  W-11/1-6:  GRUEN   # MassPunkt doppelt (9 / 25), heute identisch, stumme Divergenz benannt
  W-11/1-7:  GRUEN   # W-13-Abhaengigkeit GEMESSEN und verneint
  W-11/1-8:  GRUEN   # drei Richtungen 0/0/0, Suite 1692/1692
  W-11/1-9:  GRUEN   # Register: BESCHRIEBEN + alle DREI Module
  W-11/1-10: GRUEN   # a436d8a3: 2 Befehlszeilen, 2 Ausgabewerte, je Ort einer

zwei_registerangaben_die_nicht_tragen:   # gemeldet, NICHT still korrigiert
  formeln:
    F-001: "belegt — bemassung.ts:77 und masseingabe.ts:58 (Math.hypot)"
    F-002: "NICHT im Code — kein atan2 in keinem der drei Module.
            Stattdessen der UMGEKEHRTE Weg: Math.cos/Math.sin in masseingabe.ts:99-100 machen
            aus einem Winkel eine Richtung. Der Anwender tippt den Winkel; das Werkzeug misst ihn nicht."
    F-003: "NICHT im Code — lotAufGerade kommt null mal vor"
  abhaengigkeit_W13:
    behauptung: "Register: W-11 braucht W-13"
    gemessen:
      - "auswahl/select/markiert in bemassung.ts und masskette.ts: 0 Treffer"
      - "Signatur bemassung(waende, oeffnungen, toleranz) — kein Auswahl-Parameter"
      - "einzige Aufrufstelle HausplanerApp.tsx:1268 uebergibt ALLE Waende und ALLE Oeffnungen"
    schluss: "traegt nicht. Die Bemassung rechnet ueber den ganzen Grundriss."
  getan: "Reifegrad und drei Fundstellen nachgetragen, beide Spalten mit ⚠ markiert und in einer
          Fussnote begruendet. Zuordnung UNVERAENDERT — sie gehoert dem Planner."

eigener_fehler_vor_dem_melden_gefunden:
  was: "VIER Zitat-Zeilenangaben waren falsch"
  wie_entstanden: "aus dem Blickfeld eines sed-Ausschnitts abgezaehlt statt gemessen"
  einzeln: "masskette 1-6 -> 5-6 · masseingabe 20-23 -> 19-21 · 38-39 -> 37-38 · 44-51 -> 48-53"
  danach: "ALLE 15 eindeutigen Zeilenangaben der sieben Blaetter gegen die Dateien geprueft,
           0 ueber dem Dateiende"
  einordnung: "keine neue Klasse — es ist die Zeilennummer-Praezision aus W-02 (159 gegen 159-160),
               diesmal vierfach. Der Unterschied: gefunden, bevor gemeldet wurde."

fachlicher_kern:
  - "MassPunkt ist ZWEIMAL definiert (masskette.ts:9, masseingabe.ts:25), heute Feld fuer Feld
     identisch, absichtlich lokal: 'Bewusst lokal: dieses Modul kennt keine Szene' (Z.24).
     Gefaehrlich wird es, wenn eine Seite sich aendert — dann divergieren sie STUMM, weil kein
     Import sie verbindet. Kein Uebersetzerfehler, keine Warnung."
  - "Weder uebersehen noch voreilig aufraeumen: ein Import wuerde die Unabhaengigkeit der
     Eingabeschicht aufgeben, die ausdruecklich gewollt ist. Wer sie zusammenlegt, ENTSCHEIDET etwas."
  - "istBrauchbareLaenge() und richtungAus() sind eigene Ausfuhren, weil eine Mutationsprobe
     sonst BLIND bleibt — das steht als Lehre im Code selbst (masseingabe.ts:48-53)."

nicht_gemessen:
  - "ob die Bemassung an der Werkzeugschicht doch eine Auswahl braucht — gemessen sind die zwei
     Rechenmodule und die einzige Aufrufstelle, nicht die gesamte App"
  - "die Werkzeugschicht (Stufe 2 GEBAUT)"

browserabnahme: "entfaellt — reine Dokumentblaetter"
ballbesitz: evaluator
```
