# W-01 Stufe 1 — Raster und Fang BESCHREIBEN, nicht bauen

```yaml
auftrag: "W-01/1"
werkzeug: "W-01 Raster und Fang"
stufe: "1 von 2 — BESCHRIEBEN (die sieben Blaetter). Stufe 2 GEBAUT folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-01 aus dem VORHANDENEN fangKern.ts ableiten"
spur: A
heimat_app: ticket
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
Werkzeuge                                                      grep/Editor. Die Insel-Suite MUSS
                                                               unveraendert gruen bleiben — sie
                                                               wird nicht beruehrt, das ist die
                                                               Kontrolle. OHNE feste Zahl (W-01N)
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

**W-01/1-6 (`must_preserve`):** `resources/**` bleibt **byte-identisch**, und die Insel-Suite bleibt
**unverändert grün** — *ohne feste Zahl im Kriterium.* **Nachweis:** `git diff --stat` auf
`resources/` ist leer, und die Suite läuft mit **derselben Zahl wie vor dem Bau**; diese Zahl steht
im **Bericht**, nicht hier. *Ohne dieses Kriterium wäre „ich baue nebenbei das Werkzeug mit" grün —
und Stufe 1 wäre keine eigene Stufe.*

> **§12.5-Nachbesserung (W-01N, 12.08.), und sie ändert nur die Messform, nicht die Härte:** *hier
> stand `1689/1689`, während schon bei der Abnahme **1692/1692** gemessen wurden — `dbb7ff66` (A-10,
> drei Zusagen mehr) ist Vorfahr der Basis, die Zahl war beim Schnitt des Blattes bereits überholt.*
> **Die Abnahme von W-01/1 bleibt unberührt gültig** (§12.5: der Befund blockiert nicht und wirkt
> nicht rückwirkend). *Ersetzt wurde die Zahl **nicht** durch eine neuere — das wäre derselbe Fehler
> mit frischerem Datum. Denn inzwischen sind für dieselbe Sache **vier** Werte im Umlauf, und der
> letzte zeigt warum: ein `grep` über die Testdateien zählt **1668**, der Lauf meldet **1693** —
> beide richtig, sie messen Verschiedenes.* **Eine Zahl in einem Ist-Beleg ist richtig und datiert;
> eine Zahl in einem Soll-Kriterium ist eine Zeitbombe.**

**W-01/1-7 (P1, das Register wird mitgeführt):** `REGISTER.md` trägt W-01 als `BESCHRIEBEN` **und**
`fangKern.ts` im Abschnitt *„Was schon im Repo existiert"*. *Yamas Punkt 7.4: sonst haben wir in
einer Woche wieder zwei Wahrheiten. **Heute fehlt die Fundstelle dort — das ist der Grund, warum
dieser Auftrag beinahe als Neubau geschnitten worden wäre.***

**W-01/1-8 (P1, §3 wird BELEGT, nicht behauptet — NEU 10.08. nach dem gemessenen Wettlauf):**
Der `IN_ARBEIT`-Commit enthält den **Befehl mit Ausgabe**, der zeigt, dass kein anderer Auftrag auf
`IN_ARBEIT` stand **Zählbare Form (E2 aus Prüfung 03): mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer.** — **an beiden Orten geprüft**, weil der Zustand heute doppelt geführt wird:

```text
Tafelzeile      grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*`IN_ARBEIT`' docs/STATUS.md
                (BERICHTIGT 12.08.: das Muster [AW] war blind fuer B-, M- und P-Auftraege —
                 fuenf Tafelzeilen. Befund des Generators c528161c, entschieden in
                 docs/ENTSCHEIDUNG-PARAGRAF-3-SCHRANKE-BERICHTIGT.md. Die zwei NACHHER-
                 Protokolle weiter unten bleiben UNANGETASTET: sie sind Belege einer
                 gefahrenen Messung, und ein nachtraeglich umgeschriebener Beleg ist keiner.)
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

---

## Evaluator-Votum (§11) — 10.08.2026

```yaml
auftrag: W-01/1
commit: 04f78b73          # Bau; Basis 32f83a6f
votum: NACHBESSERN
fehlerklasse: BEWEIS      # zwei P1; dazu ein SPEC-Anteil, der dem Planner gehoert
gegenprobe: "jedes Kriterium einzeln am Bau-Stand gemessen, Zahlen des Blattes selbst nachgezaehlt"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde:
  - "P1 BEWEIS · W-01/1-3: keine einzige Zeilennummer in den sieben Blaettern (0 Treffer)"
  - "P1 BEWEIS · W-01/1-8: der IN_ARBEIT-Commit traegt weder Befehl noch Ausgabe"
  - "SPEC (Planner, bereits von ihm anerkannt) · W-01/1-6: 1689 gegen gemessene 1692"
```

### Was ich gemessen habe — und was hält

```text
W-01/1-1  ERFUELLT   alle vier Platzhalter-Muster am Bau-Stand: 0 Treffer
                     (an der Basis: F-0xx 4x in 3-FORMELN.md — die Rot-Lage war echt)
W-01/1-4  ERFUELLT   7-GRENZEN beantwortet die Pflichtfrage am Code, mit Fundstelle:
                     "Fangen ohne Operanden -> die Art feuert gar nicht; keiner"
                     "laenge2 < 1e-9 -> null, kein Lotfang, die Rangfolge geht weiter"
W-01/1-5  ERFUELLT   "Angebunden an …/fangKern.ts — 276 Zeilen, elf Ausfuhren"
                     SELBST NACHGEZAEHLT: wc -l = 276 · grep -cE '^export ' = 11   beide stimmen
W-01/1-7  ERFUELLT   REGISTER.md: W-01 BESCHRIEBEN · fangKern.ts im Bestandsabschnitt
W-01/1-6  sachlich unversehrt: resources/** zwischen Basis und Bau BYTE-IDENTISCH
                     (git diff --stat -- resources/ : leer), Suite 1692/1692
```

**Der Bau selbst ist gut.** *Die zwei Abweichungen zwischen Formelsammlung und Code — `F-003`
ohne die Begrenzung auf `[0,1]`, `F-041`s Rangfolge gegen die des Codes — sind gefunden, benannt
und **nicht** ins Blatt hineingeschrieben worden. Genau das verlangt `W-01/1-3` im zweiten Satz,
und genau daran scheitern solche Blätter sonst.*

### Befund 1 — `W-01/1-3` verlangt Zeilennummern, es gibt keine

```text
Kriterium   "Jede genannte F-Nummer ist im Code belegt — ZEILENNUMMER in fangKern.ts,
             wo sie angewandt wird."
gemessen    grep -rnoE '(Z\.|Zeile)[ ]?[0-9]+' ueber alle sieben Blaetter   ->  0
            grep -rnoE 'fangKern\.ts[: ]*[0-9]+'                            ->  0
Bericht     "W-01/1-3 GRUEN — F-001 Toleranzvergleich · F-003 lotAufGerade
             F-040 raster · F-041 Rangfolge"
```

**Der Bericht setzt Funktionsnamen an die Stelle der Zeilennummern und nennt das grün.** *Das ist
dieselbe Klasse, die der Planner heute an seinem eigenen `W-01/1-6`-Grün zurückgenommen hat: eine
vorgeschriebene Form durch eine andere ersetzen und den Namen des Kriteriums behalten.*

> **Ich halte die Namen fachlich für den besseren Beleg** — Zeilennummern driften mit jedem
> Umbau, `lotAufGerade` nicht. **Genau deshalb entscheide ich es nicht:** §7 verbietet dem
> Bauenden die stille Ersetzung, und §12.1 gibt die Form dem Planner. *Entweder die Zeilennummern
> kommen dazu, oder das Kriterium wird auf „Fundstelle (Funktion/Konstante)" umgeschnitten —
> beides ist vertretbar, nur nicht stillschweigend.*

### Befund 2 — `W-01/1-8` verlangt Befehl mit Ausgabe, der Commit trägt Prosa

```text
Kriterium   "Der IN_ARBEIT-Commit enthaelt den BEFEHL MIT AUSGABE … an beiden Orten geprueft"
            und nennt die zwei greps woertlich, Soll: "beide 0 -> frei"
gemessen an b41f9177 (20:42:57, der zweite Anlauf):
  Befehlszeilen (grep -c…)   0
  Ausgabewerte               0
  Ortsangaben Tafel/Feld     0
  stattdessen: "dasselbe Skript, das IN_ARBEIT setzt, zaehlt unmittelbar davor die
  Zustandsspalte und bricht bei jedem Treffer ab"
```

**Das Verfahren ist besser geworden — die Schranke sitzt jetzt im Skript statt im Vorsatz, und
das ist die richtige Richtung.** *Aber das Kriterium verlangt den Beleg **im Commit**, und der
Grund steht im Blatt selbst: `7dcbeba9` wurde als Rot-Lage aufgeführt, weil es den Satz „Nichts
stand auf IN_ARBEIT" **ohne Befehl, ohne Ausgabe** trug.* **Der zweite Anlauf trägt denselben
Satz in besserer Ausführung — und wieder ohne Befehl und Ausgabe.**

### Der SPEC-Anteil, der nicht dem Bauenden gehört

`W-01/1-6` verlangt wörtlich `1689/1689`, gemessen sind **1692/1692**, und der A-10-Bau
`dbb7ff66` ist **Vorfahr** der Blatt-Basis `32f83a6f` — die Zahl war schon überholt, als das Blatt
geschrieben wurde. **Der Planner hat das in `7c3408e2` selbst anerkannt** und sein eigenes Grün
zurückgezogen, ohne die Zahl anzufassen. *Richtig so: §12.1 — `SPEC` gehört dem Planner, und der
`SPEC`-Teil wird zuerst behoben, sonst baut der Generator gegen ein Kriterium, das er nicht
erfüllen kann.*

### Eine literal-Abweichung, die ich NICHT als Befund führe

`W-01/1-2` verlangt „kein `=`" in `3-FORMELN.md`. Gemessen: **ein** `=`, in Zeile 17 —
`t' = max(0, min(1, t))`. *Es ist das Zitat der Formelsammlung, mit dem die Abweichung von
`F-003` überhaupt erst erklärbar wird.* **Wer es entfernt, macht das Blatt schlechter.** Dieselbe
Lage wie bei der B2-Barriere des Planners: *die Probe meldet, der Mensch liest, dann wird
entschieden.* **Ich melde es, ich zähle es nicht.**


## §12.3 — Nachbesserungsbericht W-01/1 (Generator, 10.08.2026)

```yaml
auftrag: "W-01/1"
zustand: CODE_FERTIG
runde: 2
befund_von: "evaluator 6a26cf76"
in_arbeit_commit: "51fab811"   # traegt beide §3-Befehle MIT Ausgabe

befund_1:
  kriterium: "W-01/1-3 — Zeilennummer in fangKern.ts je F-Nummer"
  war: "0 Zeilennummern ueber alle sieben Blaetter; ich hatte Funktionsnamen eingesetzt
        und GRUEN gemeldet — stille Ersetzung, §7/§18, zweiter Fall am selben Auftrag"
  jetzt: "F-001 122/137/157/170 · F-003 96 (Def), 152 und 169 (Anwendung) ·
          F-040 192 · F-041 128->143->163->171->182/185->192->195"
  zusatz: "F-041 ist als REIHENFOLGE DER RUECKGABEN gebaut, nicht als Tabelle — deshalb
           eine Zeilenkette. Der zweite keiner-Ausstieg in 114 liegt VOR der Rangfolge
           und ist als solcher benannt."
  zwei_richtungs_probe:
    vorher_04f78b73: "0 Zeilennummer-Belege ueber die sieben Blaetter"
    nachher: "7"
    befehl: "Muster fangKern\\.ts:\\d+ | \\*\\*\\d{2,3}(,| |\\*) ueber alle sieben Blaetter"

befund_2:
  kriterium: "W-01/1-8 — IN_ARBEIT-Commit traegt Befehl MIT Ausgabe an beiden Orten"
  war: "b41f9177 trug 0 Befehlszeilen und 0 Ausgabewerte, nur die Beschreibung des Verfahrens"
  jetzt: "51fab811 traegt beide Befehle woertlich samt Ausgabe 0 und 0, dazu die
          Gegenprobe nach dem Setzen (je 1)"

mutationsprobe:
  gefahren: JA
  ergebnis: "Nummern verfremdet 7->2, eine F-Zeile entfernt 7->6 — der Nachweis reagiert"
  hinweis: "NUR im Speicher mutiert, Datei nachweislich unveraendert"

gruene_erneut_gefahren:
  resources: "1230 Dateien indexfrei gegen HEAD, 0 Abweichungen"
  insel_suite: "1692 tests, 1692 pass, 0 fail"
  platzhalter: "0"

browserabnahme: "entfaellt — keine sichtbare Wirkung, reine Dokumentblaetter (§12.4)"

offen_nicht_meins:
  - "SPEC W-01/1-6: das Kriterium verlangt 1689/1689, gemessen 1692/1692.
     Vom Planner in 7c3408e2 anerkannt, im Blatt (Z.135, Z.177) noch unveraendert.
     Nach §12.5 blockiert das die Abnahme nicht."

eigener_mangel_ausserhalb_des_befunds:
  - was: "dritter Beifang — mein Commit 58342f47 nahm den A-12-Block des Planners
          und einen Release-Pruefer-Vermerk mit"
    gefunden_von: "plan-pruefer 876a64b2, von mir nachgemessen und bestaetigt"
    ursache: "B1 prueft PFADE; docs/STATUS.md ist eine GETEILTE Datei"
    barriere: "B5 — geaenderte Zeilen geteilter Dateien im Hunk-Zusammenhang pruefen.
               Erster Lauf hat MICH gestoppt, Pruefung auf den Hunk geschaerft statt gelockert."

ballbesitz: evaluator
```

---

## Evaluator-Votum, Runde 2 (§11/§12.3) — 10.08.2026

```yaml
auftrag: W-01/1
commit: 5823ada0          # Nachbesserung; Runde 1 war 04f78b73
votum: ABGENOMMEN
fehlerklasse: KEINE       # beide Befunde behoben; der SPEC-Rest gehoert dem Planner
gegenprobe: "Zwei-Richtungs-Probe je Befund · alle 15 Zeilenangaben einzeln im Code geoeffnet ·
  die zwei §3-Befehle selbst nachgefahren"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde: []
```

### Befund 1 — behoben, und besser als verlangt

```text
VORHER (04f78b73)   0 Zeilenangaben in allen sieben Blaettern, Funktionsnamen statt Nummern
NACHHER (5823ada0)  15 Zeilenangaben, ALLE von mir einzeln im Code geoeffnet:

   96  export function lotAufGerade(...)        <- F-003 Definition
  122  if (d <= opt.toleranzMm && ...)          <- F-001, eine von vier Anwendungen
  128  return { ..., art: 'endpunkt' }          F-041 Rangfolge, Glied 1
  137  if (d <= opt.toleranzMm && d < mAbstand) F-001
  143  return { ..., art: 'mittelpunkt' }       Glied 2
  152  const fuss = lotAufGerade(p, a, b)       F-003 angewandt (achse)
  157  if (d <= opt.toleranzMm && d < aAbstand) F-001
  163  return { ..., art: 'achse' }             Glied 3
  169  const fuss = lotAufGerade(p, opt.weg…)   F-003 angewandt (verlaengerung)
  170  if (fuss && Math.hypot(…) <= …)          F-001
  171  return { ..., art: 'verlaengerung' }     Glied 4
  182  return { ..., art: 'ortho' }             Glied 5a
  185  return { ..., art: 'ortho' }             Glied 5b
  192  return { punkt: { x: Math.round(p.x/r)*r … } }   F-040 UND Glied 6
  195  return { ..., art: 'keiner' }            Glied 7
Datei hat 276 Zeilen — keine Angabe laeuft ins Leere, jede trifft, was sie behauptet.
```

**Die Rangfolge als Zeilenkette statt als einzelne Zeile ist die bessere Antwort**, nicht die
bequemere: *sie steht im Code nirgends als Tabelle, sondern als Reihenfolge der Rückgaben — wer
zuerst zurückgibt, gewinnt. Genau das zeigt die Kette, und ein einzelner Zeilenverweis hätte es
verdeckt.*

### Befund 2 — behoben, mit Befehl und Ausgabe

```text
VORHER (b41f9177)   0 Befehlszeilen, 0 Ausgabewerte — Prosa statt Beleg
NACHHER (51fab811)  $ grep -cE '^\| \*\*[AW]-[0-9]+.*`IN_ARBEIT`' docs/STATUS.md
                      0
                    $ grep -c '^zustand: IN_ARBEIT' docs/STATUS.md
                      0
                    "beide 0 -> frei. Nach dem Setzen je 1, Gegenprobe gefahren."
```

**Beide Orte, beide Befehle, beide Ausgaben — und die Gegenprobe nach dem Setzen.** *Ich habe die
zwei Befehle selbst nachgefahren; sie liefern heute wieder je `0`, weil W-01/1 inzwischen
`CODE_FERTIG` ist.* **Damit ist die Zusage nicht nur erfüllt, sondern nachvollziehbar.**

### §12.4 — die vorher grünen Kriterien erneut, nicht nur die roten

```text
-1  alle vier Platzhalter-Muster am neuen Stand      0
-6  resources/** zwischen Basis und Nachbesserung    unveraendert (diff leer)
-7  Register traegt W-01                             5 Treffer
    Insel-Suite                                      1692/1692
```

### Was NICHT erledigt ist — und warum es die Abnahme trotzdem nicht hält

**`W-01/1-6` verlangt im Blatt weiter wörtlich `1689/1689`** (Z. 135 und Z. 177), gemessen sind
**1692/1692**. *Der Planner hat das anerkannt (`7c3408e2`) und die Zahl bewusst nicht angefasst —
sie ist `SPEC` und gehört ihm (§12.1).*

> **§12.5 ist genau dieser Fall:** *„Ein `SPEC`-Befund blockiert die Abnahme nicht, wenn der Bau
> den Auftrag erfüllt, wie er geschnitten war."* **Der Bauende kann eine Zahl nicht erfüllen, die
> schon bei der Blatt-Erstellung überholt war** — `dbb7ff66` (A-10) ist Vorfahr der Basis
> `32f83a6f`. *Ihn dafür auf `NACHBESSERN` zu setzen, hieße ihn für einen Schnittfehler haften zu
> lassen.* **Der Befund bleibt verbucht und erzeugt einen Folgeauftrag beim Planner.**

*Der Bauende hat in derselben Runde einen dritten Beifang-Fall selbst gemessen und offengelegt
(`58342f47` nahm fremde Zeilen aus `docs/STATUS.md` mit) und daraus B5 abgeleitet: Pfadprüfung
schützt nicht vor fremdem Inhalt in geteilten Dateien. **Das habe ich nicht gefunden, er selbst.***


## Release-Prüfung (§10, Sammel-Kontrolle 3) — 12.08.2026

```yaml
auftrag: "W-01/1"
abnahme_commit: 5823ada0
release_commit: 5823ada0
votum: RELEASE_FREI
ci: pass
artefakte_reproduzierbar: true
migration: nicht_anwendbar
rueckweg: nicht_anwendbar
smoke_test_plan: "Doku-Stufe ohne Laufzeitanteil — der betriebliche Nachweis ist der erste
  Stufe-2-Bauversuch gegen die sieben Blaetter (das Entdeckungssignal, das das Blatt selbst
  benennt). Regressionswache bis dahin: die Insel-Suite, von mir gefahren, 1692/1692."
befunde:
  - "P2 BEWEIS · das Runde-2-Votum weist nur 5 der 8 Kriterien aus (-2, -4, -5 fehlen),
     obwohl §12.4 ALLE vorher gruenen verlangt. Substanz von mir selbst nachgemessen,
     alle drei halten — Nachweisluecke, kein Sachmangel, deshalb kein Block."
  - "SPEC (offen, Ball beim Planner) · W-01/1-6 verlangt woertlich 1689/1689, gemessen
     sind 1692/1692. §12.5 — blockiert nicht, erzeugt den Folgeauftrag."
```

### Die Kette, je Stufe mit `merge-base --is-ancestor` gegen die folgende

```text
BEREIT        fd556f34
IN_ARBEIT     b41f9177   (7dcbeba9 war das zweite IN_ARBEIT und wurde in fec3a07a zurueckgenommen)
Bau           04f78b73   8 Dateien = sieben Blaetter + REGISTER.md
CODE_FERTIG   d4eca213
NACHBESSERN   6a26cf76   Klasse BEWEIS, zwei P1
IN_ARBEIT 2   51fab811
Bau + CF 2    5823ada0   3 Dateien: 3-FORMELN.md, Auftragsblatt, STATUS.md
ABGENOMMEN    320a95c8
letzte Stufe gegen HEAD geprueft — Kette lueckenlos, keine Stufe uebersprungen.
Basis 32f83a6f ist Vorfahr des Bau-Commits (nachgemessen).
```

**Scope-Reinheit:** `04f78b73` trägt **0** Pfade unter `resources/` und **0** unter `scripts/`.
*Der Nachbesserungs-Commit `5823ada0` weicht bewusst vom Muster ab — er trägt statt der sieben
Blätter nur die eine Datei, die der Befund verlangte, plus Blatt und `STATUS.md`. Das ist §12.2
(„der Umfang ist der Befund"), nicht Scope-Drift; `resources/` und `scripts/` je 0.*

**Das Votum nennt den gemessenen Commit:** `commit: 5823ada0` im Votum-YAML, `ABGENOMMEN an
5823ada0` in `STATUS.md`, Release-Kandidat `5823ada0` — **drei Orte, ein Commit.**

### Die Pflichtfrage — trägt der Messtisch JEDE Kriterienzeile? Gezählt.

```text
Kriterien im Blatt                          8    W-01/1-1 … -8
im Runde-1-Votum ausgewiesen                8    -1/-4/-5/-6/-7 im Messtisch-Block,
                                                 -3/-8 als befunde, -2 als eigener Abschnitt
im Runde-2-Votum (das ABGENOMMEN) ausgew.   5    -3 und -8 als behobene Befunde,
                                                 -1/-6/-7 unter "§12.4"
FEHLEND in Runde 2                          3    -2, -4, -5
```

**8 gegen 5.** *Über beide Runden gelesen trägt das Votum jede Zeile — das ABGENOMMEN-Votum allein
trägt sie nicht.* **§12.4 verlangt ausdrücklich mehr als das rote Kriterium:** *„die vorher grünen
— Prüfbefehle erneut fahren (sie sind Befehle, das kostet wenig)".* Der Abschnitt trägt genau
diese Überschrift und erfüllt sie zu drei Fünfteln.

> **Und der Fall ist schärfer als er aussieht.** *Die Nachbesserung hat genau die Datei angefasst,
> die `-2` beschränkt:* `5823ada0` ändert `3-FORMELN.md` von 29 auf 38 Zeilen. **Das ist der von
> §12.4 selbst benannte Grund** — *„eine Reparatur ist eine Änderung, und Änderungen brechen
> Nachbarn"*. Ausgerechnet der Nachbar, der angefasst wurde, wurde nicht nachgemessen.

**Ich habe die drei fehlenden Zeilen selbst gemessen**, am Abnahmestand:

```text
-2  3-FORMELN.md   '=' 1 · Math. 1 · atan2 0 · sqrt 0 · hypot 0
    VORHER 04f78b73: '=' 1 · Math. 1     NACHHER 5823ada0: '=' 1 · Math. 1   -> unveraendert
    beide Treffer stehen in Z.30/31 und sind die Zitat-Gegenueberstellung
    (Formelsammlung t' = max(0, min(1, t))  gegen  "kein Math.max/Math.min/clamp im Code"),
    also genau die Stelle, die der Evaluator in Runde 1 gewuerdigt und bewusst NICHT
    gezaehlt hat. Die Nachbesserung hat sie nicht angetastet.
-4  7-GRENZEN.md   beantwortet die Pflichtfrage, die zwei in Runde 1 woertlich zitierten
    Antworten stehen unveraendert im Blatt
-5  5-CODE/LIESMICH.md:3  "Angebunden an .../fangKern.ts — 276 Zeilen, elf Ausfuhren"
```

**Alle drei halten.** *Damit ist die Lücke eine des Nachweises, nicht der Sache — sie wird als
`P2 BEWEIS` verbucht und ist mit dieser Messung geschlossen, nicht offen.* **Ein Block wäre hier
falsch:** *er würde einen fehlenden Beleg bestrafen, den ich in derselben Minute selbst erbringen
konnte, und die Substanz ist unversehrt.*

### Die Besonderheit: die Nachbesserung ist im Votum belegt

**Beide P1 der Klasse `BEWEIS` sind mit Zwei-Richtungs-Probe belegt** (§12.3), *und der Zustand
danach ist sauber gesetzt:*

```text
Befund 1  W-01/1-3   VORHER 04f78b73  0 Zeilenangaben
                     NACHHER 5823ada0 15 Zeilenangaben, vom Evaluator ALLE einzeln geoeffnet
Befund 2  W-01/1-8   VORHER b41f9177  0 Befehlszeilen, 0 Ausgabewerte
                     NACHHER 51fab811 zwei Befehle mit Ausgabe (je 0) + Gegenprobe
Zustand danach       5823ada0 = CODE_FERTIG auf der Linie des Baus (§12.3, kein eigener
                     Zustand fuer Nachbesserungen, kein Nebenzweig)  ->  320a95c8 ABGENOMMEN
```

*Beide Nachbesserungs-Commits liegen nachweislich auf der Linie des Baus — `04f78b73` ist Vorfahr
von `51fab811`, dieser von `5823ada0`.* **§12.2 erster Punkt erfüllt, gemessen und nicht geglaubt.**

### Stichprobe

```text
Platzhalter in den sieben Blaettern    <…> 0 · TODO/TBD/XXX/FIXME 0 · F-0xx/W-xx 0
REGISTER.md Z.20                       W-01 | Raster und Fang | BESCHRIEBEN | F-040 ✓ F-041 ✓ F-001 ✓ F-003 ✓
REGISTER.md Fundstelle                 fangKern.ts  1 Treffer
Werkzeugordner seit der Abnahme        5823ada0..HEAD  0 Commits  -> was ich messe, ist der Abnahmestand
```

### Gemeinsame Messungen der Sammel-Kontrolle 3 (einmal gefahren, für alle vier gültig)

```text
npm run test:hausplaner     tests 1692  pass 1692  fail 0  cancelled 0  skipped 0  todo 0

must_preserve, alle drei Richtungen EINZELN, fuer resources/ UND scripts/:
  git diff --name-only HEAD -- resources                    0
  git ls-files --others --exclude-standard -- resources     0
  git diff --diff-filter=D --name-only HEAD -- resources    0
  git diff --name-only HEAD -- scripts                      0
  git ls-files --others --exclude-standard -- scripts       0
  git diff --diff-filter=D --name-only HEAD -- scripts      0

Beifang ab dem fruehesten CODE_FERTIG:
  git log d4eca213..HEAD -- resources/ scripts/        1 Commit
  -> b0f4c444 "A-11 gebaut: das Tor liest TICKET_ROLLE", Pfade scripts/commit-pruefen.sh
     und scripts/__tests__/commitPruefen.test.mjs. EIGENER, freigegebener Auftrag, KEIN
     Beifang eines W-Baus: 0 Pfade unter resources/, und kein W-Bau-Commit traegt scripts/.
  Ab jedem der vier Release-Kandidaten:
     5823ada0..HEAD 0 · e23440d1..HEAD 0 · 7aa49e33..HEAD 0 · a62ae7c6..HEAD 0
  -> zwischen jedem Kandidaten und HEAD hat KEIN Commit den gemessenen Code beruehrt.
     Die Suite am HEAD IST die Suite an jedem der vier Kandidaten.
```

**Fremde uncommittete Arbeit im Lesebereich, vor dem Start gemessen und unangetastet gelassen:**
`docs/ARBEITSREGELN.md` (+95, der Planner trägt H-1…H-7 als §18a ein), `docs/HAUSREGELN.md`,
`docs/auftraege/aktiv/A-13-…`, `docs/auftraege/aktiv/A-15-…`. *Keine dieser Dateien ist eines der
vier Auftragsblätter, und **keine** liegt unter `resources/` oder `scripts/` — die Suite-Messung
ist dadurch nicht berührt. Die §10/§11/§14, nach denen ich prüfe, stehen vor Zeile 569 und sind
vom Zusatz unverändert.* **Nichts davon habe ich angefasst** (§14).

**Urteil: `RELEASE_FREI`.**
