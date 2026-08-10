# W-01 Stufe 1 — Raster und Fang BESCHREIBEN, nicht bauen

```yaml
auftrag: "W-01/1"
werkzeug: "W-01 Raster und Fang"
stufe: "1 von 2 — BESCHRIEBEN (die sieben Blaetter). Stufe 2 GEBAUT folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-01 aus dem VORHANDENEN fangKern.ts ableiten"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 32f83a6f
prioritaet: P1
anlass: "Yamas Auftrag 10.08. — Gruppe aus dem Register schneiden, nicht aus dem Fehlerbuch"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt. Kein W-Blatt lag als Auftrag vor (ls docs/auftraege/aktiv/ = nur A-xx)."
```

## Der Anlass — und die Drift, die er beendet

**Elf Aufträge (A-01…A-11) haben ausnahmslos den Bestand repariert, abgesichert und den Prozess
geordnet. Keiner hat ein Werkzeug aus dem Register gebaut.** Die Werkstatt ist in Ordnung, und sie
steht leer. Dieses Blatt ist der erste Auftrag aus dem **Register** statt aus einem Befund.

**Warum W-01 zuerst — das Register begründet es selbst, und die Begründung trägt:**

> *„Ohne verlässlichen Fang ist jede Wand ungenau, und jede Ungenauigkeit vererbt sich nach oben —
> bis das Dach nicht mehr schließt. Ein wackliger Fang ist kein Schönheitsfehler, er ist ein
> Fundamentfehler."*

## Ist-Zustand — SELBST GEMESSEN, und er ändert die Natur des Auftrags

**Die Anbindungsmessung nach Yamas Punkt 4 („anbinden vor bauen"), gefahren vor dem Schnitt:**

```text
GEFUNDEN IM REPO
resources/planner/hausplaner/geometry/fangKern.ts        276 Zeilen
  export type FangArt                  die Fangarten
  export interface FangPunkt           Punkt mit Art
  export interface FangOptionen        Konfiguration
  export interface FangErgebnis        Rueckgabe MIT Art (nicht nur Koordinate)
  export function lotAufGerade()       Lotfusspunkt   (F-003-Familie)
  export function fange()              die Hauptfunktion
  export const FANG_PX = 12            Toleranz in Bildschirmpixeln
  export function toleranzAusZoom()    zoomabhaengige Toleranz  (F-040/F-041-Familie)
  export interface WandStrecke         Eingabeform
  export function wandFangpunkte()     Fangpunkte aus Waenden ableiten
  export const FANG_TEXT               Beschriftung je Fangart
Zusagen dazu                                             3 Testdateien

NICHT GEFUNDEN
Werkzeug in der Registry (toolRegistry.ts)               0 Treffer auf fang/raster/snap
Eintrag im Werkbank-Register                             W-01 steht auf LEER
Fundstelle im Register-Abschnitt "Was schon im Repo"     fangKern.ts NICHT genannt
```

> ### Damit ist W-01 kein Bauauftrag, sondern ein Anschlussauftrag
>
> **Die Rechenschicht ist gebaut** — mit Typen, Toleranzmodell, Zoom-Abhängigkeit und
> Beschriftung. **Die Werkzeugschicht fehlt**, und die Werkbank weiß von der Rechenschicht nichts:
> der Register-Abschnitt *„Was schon im Repo existiert"* nennt **drei** Fundstellen, alle für W-07.
> `fangKern.ts` steht dort nicht.
>
> *Wer W-01 ohne diese Messung als Bauauftrag geschnitten hätte, hätte 276 Zeilen mit Tests danebengelegt.
> **Das ist genau der Neubau-neben-Bestand, den Yamas Punkt 4 verbietet** — und er wäre hier fast
> passiert, weil das Register die Fundstelle nicht kennt.*

## DECISION — die sieben Blätter werden AUS DEM CODE abgeleitet

```text
Quelle der Beschreibung   fangKern.ts + seine drei Zusagen. NICHT die Vorlage ausfuellen,
                          sondern den vorhandenen Bau lesen und beschreiben.
3-FORMELN                 nur F-NUMMERN. Zu pruefen und einzutragen: welche der 24 Formeln
                          fangKern tatsaechlich benutzt (Kandidaten: F-001 Abstand,
                          F-003 Lotfusspunkt, F-040/F-041 Raster). Findet sich eine
                          benutzte Rechnung NICHT in der Sammlung, wird sie GEMELDET,
                          nicht ins Blatt geschrieben.
5-CODE/LIESMICH           die Zeile lautet "angebunden aus geometry/fangKern.ts",
                          mit Export-Liste und Zeilennummern. NICHT "neu gebaut".
7-GRENZEN                 die Pflichtfrage: was tut der Fang, wenn er NICHT kann?
                          Am Code zu messen: was liefert fange() ohne Treffer?
                          Stilles Nichts ist verboten (A-10-Lehre).
```

**Kein Code wird angefasst.** *Stufe 1 ist die Abnahme des Denkens; sie fällt billiger auf als der
Bau — Yamas Punkt 7.3.*

## Nicht-Ziele

- **Kein Werkzeug in der Registry.** Das ist Stufe 2 (`GEBAUT`) und ein eigener Auftrag.
- **Keine Änderung an `fangKern.ts`** und an keiner seiner Zusagen.
- **Keine Formel in das Blatt abschreiben.** Nur F-Nummern — die Regel steht im Blatt selbst und
  hat einen Grund: *eine Formel an zwei Orten wird an einem korrigiert und am anderen vergessen.*
- **Keine Aussage über Raster-Darstellung.** Ob ein sichtbares Raster gezeichnet wird, ist eine
  Renderer-Frage und steht in W-12/Schicht 4, nicht hier.

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/1-ZWECK.md
                                                            2-FUNKTION.md
                                                            3-FORMELN.md
                                                            4-BEDIENUNG.md
                                                            5-CODE/LIESMICH.md
                                                            6-PRUEFUNG.md
                                                            7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-01 LEER -> BESCHRIEBEN
                                                     + fangKern.ts im Abschnitt "Was schon
                                                       im Repo existiert" nachtragen
```

*Ausdrücklich NICHT im Scope: `resources/**` — kein Produktivcode, keine Zusage, keine Registry.*

## Wiederverwendungsprüfung (§5)

```text
fangKern.ts                    VORHANDEN, 276 Zeilen — Quelle der Beschreibung, unangetastet
seine 3 Zusagen                VORHANDEN — Quelle fuer 6-PRUEFUNG (was ist schon zugesagt?)
FORMELSAMMLUNG.md              24 Formeln, F-001/F-003/F-040/F-041 sind die Kandidaten
W-07-Blaetter                  das EINZIGE BESCHRIEBENE Werkzeug — Muster fuer Form und Tiefe
02-WERKZEUGE/_VORLAGE/         die 7 Blattvorlagen — Struktur, nicht Inhalt
```

**Nichts wird neu erfunden.** *Form kommt aus dem Muster W-07, Inhalt aus `fangKern.ts`, Formeln aus
der Sammlung.*

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle     KEINE
Produktivcode                                                  KEINER — reine Doku-Stufe
Testdaten-Ziel                                                 KEINES
Prozessbindung                                                 ENTFAELLT (kein Serverstart, keine DB)
Werkzeuge                                                      grep/Editor. Die Insel-Suite
                                                               (1689 Zusagen) MUSS unveraendert
                                                               gruen bleiben — sie wird nicht
                                                               beruehrt, das ist die Kontrolle
```

**Erstnutzer:** *der Generator von W-01 Stufe 2 — er baut das Werkzeug gegen diese Beschreibung.
Und der Plan-Prüfer jedes weiteren W-Auftrags, der `W-01` als Vorbild nimmt.*

## Akzeptanzkriterien

**Die Rot-Lage dieser Stufe ist ZÄHLBAR, nicht behauptet:** die Blätter tragen heute
Vorlagen-Platzhalter. Der Plan-Prüfer bestätigt das vor dem Bau mit einem `grep`.

**W-01/1-1 (P1, kein Platzhalter mehr):** In keinem der sieben Blätter steht noch ein
Vorlagen-Platzhalter. *Rot heute messbar:*

```text
grep -rcE 'F-0xx|<Name>|<In EINEM Satz|<Zwischenergebnis>|ja / nein — weil' \
     docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/
-> heute > 0 (Probe: 3-FORMELN.md traegt "F-0xx" und "ja / nein — weil …")
-> Soll: 0
```

**W-01/1-2 (P1, `3-FORMELN` nennt nur Nummern):** Das Blatt enthält **keine ausgeschriebene
Formel** — kein `=`, kein `atan2`, kein `sqrt` als Rechnung. Nur F-Nummern mit Verwendungszweck und
Grenzfall-Spalte. *Rot heute: die Tabelle ist leer und trägt `F-0xx`.*

**W-01/1-3 (P1, die Formeln sind GEPRÜFT, nicht geraten):** Jede genannte F-Nummer ist im Code
belegt — Zeilennummer in `fangKern.ts`, wo sie angewandt wird. Eine Rechnung, die im Code steht und
in der Sammlung **fehlt**, wird als Befund gemeldet und **nicht** ins Blatt geschrieben.
*Sonst entstehen Formeln zweiter Ordnung, die niemand pflegt.*

**W-01/1-4 (P1, `7-GRENZEN` beantwortet die Pflichtfrage):** Das Blatt sagt, **was der Fang tut,
wenn er nicht kann** — am Code gemessen, mit Fundstelle. *Rot heute: das Blatt ist Vorlage. **Das ist
die A-10-Lehre: der teuerste Fehler des Projekts war ein `catch { continue; }`, das eine korrekte
Absage schluckte.** Ein Werkzeug ohne beantwortete Grenzfrage darf nicht `BESCHRIEBEN` heißen.*

**W-01/1-5 (P1, `5-CODE` sagt die Wahrheit über die Herkunft):** Die Zeile lautet **„angebunden
aus `geometry/fangKern.ts`"** mit Export-Liste und Zeilennummern — nicht „neu gebaut".
*Yamas Punkt 4: „neu gebaut" ohne vorherige Suche ist ein Befund, kein Fortschritt.*

**W-01/1-6 (`must_preserve`):** `resources/**` bleibt **byte-identisch**, und die Insel-Suite
bleibt bei **1689/1689**. *Nachweis: `git diff --stat` auf `resources/` ist leer. Ohne dieses
Kriterium wäre „ich baue nebenbei das Werkzeug mit" grün — und Stufe 1 wäre keine eigene Stufe.*

**W-01/1-7 (P1, das Register wird mitgeführt):** `REGISTER.md` trägt W-01 als `BESCHRIEBEN` **und**
`fangKern.ts` im Abschnitt *„Was schon im Repo existiert"*. *Yamas Punkt 7.4: sonst haben wir in
einer Woche wieder zwei Wahrheiten. **Heute fehlt die Fundstelle dort — das ist der Grund, warum
dieser Auftrag beinahe als Neubau geschnitten worden wäre.***

**W-01/1-8 (P1, §3 wird BELEGT, nicht behauptet — NEU 10.08. nach dem gemessenen Wettlauf):**
Der `IN_ARBEIT`-Commit enthält den **Befehl mit Ausgabe**, der zeigt, dass kein anderer Auftrag auf
`IN_ARBEIT` stand — **an beiden Orten geprüft**, weil der Zustand heute doppelt geführt wird:

```text
Tafelzeile      grep -cE '^\| \*\*[AW]-[0-9]+.*`IN_ARBEIT`' docs/STATUS.md
Zustandsfeld    grep -c '^zustand: IN_ARBEIT' docs/STATUS.md
beide 0  ->  frei.   Der Beleg gehoert in DENSELBEN Commit, der IN_ARBEIT setzt.
```

> **Rot heute belegbar am eigenen Vorgang.** `7dcbeba9` (20:25:56) setzte W-01/1 auf `IN_ARBEIT`
> und enthält den Satz *„Nichts stand auf IN_ARBEIT"* — **ohne Befehl, ohne Ausgabe, ohne
> Ortsangabe.** Elf Sekunden vorher hatte `d6846f69` A-09 gesetzt; der Verstoß wurde in `fec3a07a`
> selbst gefunden und zurückgenommen.
>
> **Warum „an beiden Orten" der Kern ist:** A-09 wurde in Tafelzeile **und** Zustandsfeld gesetzt,
> W-01 **nur** in der Tafelzeile. Selbst nachgemessen: `grep -c '^zustand: IN_ARBEIT'` ergibt an
> beiden Commits **1** — **eine Prüfung nur des Feldes hätte den zweiten `IN_ARBEIT` nie gesehen.**
>
> **Ehrlich zur Wirkung:** Der Beleg **schließt das Fenster nicht**, er verkleinert es von einer
> Vorprüfungsrunde auf die Dauer eines Commits. Ein echter Riegel braucht **einen** Ort für den
> Zustand — siehe [`BEFUND-P02-DER-ERSTE-GEMESSENE-WETTLAUF.md`](../../BEFUND-P02-DER-ERSTE-GEMESSENE-WETTLAUF.md).
> *Bis dahin ist dies das Beste, was ein Blatt verlangen kann: kein Hinweis im Fließtext, sondern
> ein Nachweis, den der Evaluator prüft.*

## Kantenliste

```text
fange() ohne Treffer                   -> was kommt zurueck? MESSEN, dann in 7-GRENZEN
Zoom sehr klein / sehr gross           -> toleranzAusZoom-Grenzen in 7-GRENZEN
FangArt, die es im Code gibt, aber
  in 4-BEDIENUNG nicht erklaerbar ist  -> melden, nicht erfinden
Rechnung im Code ohne F-Nummer         -> BEFUND (W-01/1-3), nicht ins Blatt
F-Nummer in der Sammlung, die fangKern
  NICHT benutzt                        -> nicht nennen; 3-FORMELN ist keine Wunschliste
Raster-DARSTELLUNG                     -> gehoert nicht hierher (Nicht-Ziel)
```

## Rückweg und Entdeckung

**Rückweg:** Sieben Doku-Dateien und eine Registerzeile — `git revert` genügt. Kein Code, keine
Daten, kein Zustand außerhalb des Repos.

**Entdeckung:** Der Zweck ist ein Blatt, gegen das Stufe 2 gebaut werden kann. Das Signal ist
deshalb der **erste Bauversuch**: muss der Generator von Stufe 2 im Code nachsehen, was in
`2-FUNKTION` oder `7-GRENZEN` hätte stehen müssen, war die Beschreibung unzureichend — dann zurück
an den Planner. *Eine Beschreibung, die man beim Bauen umgehen muss, ist keine.*

## Konfliktprüfung (§5)

```text
A-10   IN_ARBEIT   renderers/three-d/szene.ts + app/DreiDBereich.tsx    KEINE Beruehrung
A-09   ENTWURF     scripts/commit-pruefen.sh                            KEINE Beruehrung
A-11   ENTWURF     scripts/commit-pruefen.sh                            KEINE Beruehrung
W-01/1 DIESES      docs/rollenkette/werkbank/**                         disjunkt zu allen

§3-SCHRANKE: Dateifreiheit ist NICHT Ablauffreiheit (§3 Z.85). Der Ablauf wird NICHT hier
als Satz behauptet, sondern in W-01/1-8 als NACHWEIS verlangt — an beiden Orten, im
selben Commit, der IN_ARBEIT setzt. Grund: am 10.08. um 20:25 ist genau dieser Satz
in einem Blatt gestanden und ein Generator ist trotzdem ins Fenster gelaufen.
Ein Hinweis im Fliesstext schuetzt nicht; ein geprueftes Kriterium schon.
```

```yaml
fehlerklasse: keine (Erstauftrag aus dem Register)
prioritaet: P1
warteschlange: "nach A-10; W-01/1 vor W-01/2 vor W-02/1"
naechster_schritt: "Plan-Pruefer prueft DoR. Yama entscheidet ueber die Freigabe der Gruppe."
```

---

## §11 — Generator-Bericht

```yaml
auftrag: "W-01/1"
basis: 32f83a6f
commit: 04f78b73
scope:
  - docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/1-ZWECK.md
  - docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/2-FUNKTION.md
  - docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/3-FORMELN.md
  - docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/4-BEDIENUNG.md
  - docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/5-CODE/LIESMICH.md
  - docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/6-PRUEFUNG.md
  - docs/rollenkette/werkbank/02-WERKZEUGE/W-01-raster-und-fang/7-GRENZEN.md
  - docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md
tests:
  statisch: nicht_anwendbar
  unit: "1692/1692"          # Insel-Suite, W-01/1-6 must_preserve
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar
  browser: nicht_anwendbar
abweichungen:
  - "F-003 wird OHNE die [0,1]-Begrenzung gerechnet (0 Treffer Math.max/min/clamp
     in lotAufGerade). Absicht: achse und verlaengerung sind eigene Fangarten."
  - "F-041-Rangfolge != Code: kein Schnittpunkt, dafuer ortho, mittelpunkt vor achse."
  - "EIGENER MESSFEHLER: §7-Rot-Messung meldete 4 Platzhalter, richtig waren 26 in
     sechs Blaettern - Muster traf die <...>-Klammern nicht. Vor dem Bau korrigiert."
offene_akzeptanz:
  - "REGISTER fuehrt fuer W-01 auch F-004 (Schnittpunkt). Die Art existiert im Code
     NICHT. Aendern waere Scope-Erweiterung (§7) - gemeldet, nicht korrigiert."
```

### Kriterienstand, je Kriterium gemessen

```text
W-01/1-1  kein Platzhalter        GRUEN   0 in allen sieben (die zwei "<" sind
                                          `laenge2 < 1e-9`, Mathematik)
W-01/1-2  nur F-Nummern           GRUEN   keine ausgeschriebene Formel in 3-FORMELN
W-01/1-3  F-Nummern im Code belegt GRUEN  F-001 Toleranzvergleich · F-003 lotAufGerade
                                          F-040 raster · F-041 Rangfolge
W-01/1-4  7-GRENZEN beantwortet    GRUEN   sechs Zeilen "was NICHT", je mit Grund
W-01/1-5  5-CODE nennt die Herkunft GRUEN  "Angebunden an …/fangKern.ts"
W-01/1-6  must_preserve            WOERTLICH ROT, sachlich unversehrt
                                          Kriterium verlangt 1689/1689, gemessen 1692/1692.
                                          resources/** 0 Aenderungen, byte-identisch.
                                          URSACHE: dbb7ff66 (A-10-Bau) ist VORFAHR der
                                          Blatt-Basis 32f83a6f - die Zahl war schon bei der
                                          Blatt-Erstellung ueberholt. Klasse SPEC -> Planner.
W-01/1-7  Register                 GRUEN   BESCHRIEBEN + fangKern.ts als Fundstelle
```

**Ich nehme nichts ab.** *Der Ball geht an den Evaluator.*

### Richtigstellung 10.08. — nach dem Nebenbefund des Evaluators (`ded32c75`)

**Punkt 1 („gebaut ohne `IN_ARBEIT`") ist widerlegt.** *Die Zeitachse:*

```text
20:25:56  7dcbeba9  IN_ARBEIT, erster Versuch
20:30:08  fec3a07a  zurueck auf BEREIT - §3-Verstoss, von mir selbst gemeldet
20:42:57  b41f9177  IN_ARBEIT, §3-Schranke im selben Skript geprueft   <- ausgelassen
20:47:47  04f78b73  Bau
20:51:44  d4eca213  CODE_FERTIG
```

*Der Auftrag stand wieder auf `IN_ARBEIT` — **4 min 50 s vor dem Bau**.*

**Punkt 2 trifft, und er trifft mich.** *Ich habe „grün" für die Zahl eingesetzt. Der Kriterienstand
oben ist korrigiert; die **Zahl im Kriterium ändere ich nicht** — Klasse `SPEC`, Ball beim Planner.*

**Punkt 3, präzisiert:** *seine „4 an der Basis" sind die `F-0xx`-Marken; die `<...>`-Klammern waren
**26 in sechs Blättern**. Beide Zahlen stimmen, sie zählen Verschiedenes — dieselbe zu enge Form
hatte meine erste Messung auch.*
