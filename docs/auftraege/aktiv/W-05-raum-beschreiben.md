# W-05 Stufe 1 — Raum erkennen BESCHREIBEN: 190 Zeilen, und ein Fund für die Dachkonstruktion

```yaml
auftrag: "W-05/1"
werkzeug: "W-05 Raum erkennen"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-05 aus roomDetection.ts ableiten"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 3358d1cc
prioritaet: P1
anlass: "Runde 2 der Klasse A, vom Release-Pruefer freigegeben (b9dc3c35)"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 11.08. — Claim VOR dem Schnitt. Kein W-05-Blatt lag als Auftrag vor."
muster: "W-01/1, W-02/1, W-04/1, W-08/1, W-11/1"
```

## Ist-Zustand — und meine Grobzahl war zum dritten Mal zu hoch

**Anbindungsmessung je Modul an Exporten, Dateikopf und Nutzung gefahren:**

```text
GEHOERT ZU W-05
geometry/roomDetection.ts               190 Zeilen, 4 Exporte
  RaumKante · ErkannterRaum · signierteFlaeche() · erkenneRaeume()
  Dateikopf: "automatische Raumerkennung (P0). Verfahren klassisch, planar:
              Wandachsen -> Kanten-Graph, Waende an T-Punkten GETEILT (mm-Integer-Welt,
              keine Toleranz-Magie); je Knoten Halbkanten nach Winkel sortiert,
              jede Halbkante gehoert zu GENAU EINER Flaeche => keine Endlosschleife"
Registry-Werkzeug                       KEINES (0 Treffer auf raum/room)
Zusagen                                 2 dediziert, 4 erwaehnend

GEHOERT NICHT ZU W-05 — zwei Ausschluesse
geometry/grundriss.ts                   133 Zeilen
  GrundrissForm · grundrissPolygon() · EckenAnalyse · eckenAnalyse()
  anzahlInnenwinkel() · grundrissFlaecheM2() · istZusammengesetzt()
  erwarteteInnenwinkel() · formAusShape()
  -> BAUKOERPER-Geometrie (L-/T-/U-Form des Gebaeudes), NICHT Raumerkennung.
     Gemessen: Treffer auf "Raum" in grundriss.ts = 0, in roomDetection.ts = 7.
     Gehoert ins W-07-Umfeld (Dach aus Kontur) — siehe Fund unten.
geometry/polygonFlaeche.ts               48 Zeilen
  -> gehoert zu W-08 (dort im Scope). roomDetection hat seine eigene
     signierteFlaeche() und braucht es nicht.
```

> **Meine Fahrplan-Zahl war `371 Z / 16 Exporte`. Richtig sind `190 Z / 4 Exporte`.** *Dritter Fall
> derselben Art in dieser Klasse — nach W-08 (286 statt 48) und W-04 (277 statt 124). **Die
> Grobrunde hat konsequent zu hoch gemessen, weil sie Module nach Namensnähe zusammensummiert hat.**
> Die Einzelmessung korrigiert jedes Mal nach unten, nie nach oben; das ist ein Muster und kein
> Zufall.*

## Kein Registry-Werkzeug — dritter Fall der Schicht-Frage

```text
Registry            0 Treffer auf raum/room
Dateikopf           "AUTOMATISCHE Raumerkennung"
```

**Damit ist die offene Frage aus der Werkzeug-oder-Schicht-Vorlage für W-05 beantwortet, und zwar
vom Code selbst: `erkenneRaeume()` läuft automatisch aus den Wandachsen.** *Niemand klickt „Raum
erkennen" — Räume entstehen, wenn Wände einen Umlauf schließen. **W-05 ist eine Schicht wie W-01,
kein Werkzeug wie W-02.*** Die Vorlage hatte W-05 als vierten Kandidaten „offen, nicht entschieden"
geführt; hier ist die Messung, die sie schließt.

## Der Fund — und er gehört der laufenden Dachkonstruktion, nicht diesem Blatt

**A-05 hat als Lücke 4 gemeldet:** *„Ein Form-Erkenner existiert nicht (A-05-2): `lTBauGueltig`/
`uBauGueltig` validieren Maße [erkennen aber keine Form]."*

**Das ist richtig — und unvollständig. `grundriss.ts` trägt die Bausteine dafür:**

```text
Dateikopf: "reine, testbare Geometrie fuer ZUSAMMENGESETZTE Gebaeudegrundrisse
            (L-, T-, U-Form) ... ERKENNT EINSPRINGENDE INNENWINKEL
            (Kehlen-Kandidaten bei geneigten Daechern) und vorspringende
            Aussenecken ... Die Engine (buildFlat) baut daraus EINE Dachflaeche
            'main' MIT GENAU DIESEM POLYGON"

vorhanden   eckenAnalyse() · anzahlInnenwinkel() · erwarteteInnenwinkel()
            istZusammengesetzt() · grundrissPolygon()
NICHT       ein Erkenner, der aus einer KONTUR die Form ableitet.
vorhanden   formAusShape() nimmt den bereits bekannten shape-STRING
            ('l-shape' -> 'l-form') — eine Uebersetzung, keine Erkennung.
```

> **Zwei Folgerungen, und ich ziehe nur die erste:**
>
> **1. A-05s Lücke 4 bleibt gültig** — es gibt keinen Kontur-Erkenner. *Aber der Weg dorthin ist
> kürzer, als das Blatt vermuten lässt:* `eckenAnalyse` liefert die Ecken, `anzahlInnenwinkel` die
> Kehlen-Kandidaten, `erwarteteInnenwinkel` den Soll-Wert je Form. **Es fehlt die Zuordnung, nicht
> die Analyse.**
>
> **2. NICHT von mir gezogen:** ob A-05s Lücke 5 (*„auch im Zielzustand formt die Kontur das Dach
> nicht"*) ebenfalls zu eng ist. Der Dateikopf sagt, `buildFlat` baue *„eine Dachfläche mit genau
> diesem Polygon"* — **für Flachdächer formt die Kontur das Dach also.** *Ob das für geneigte gilt,
> habe ich **nicht gemessen**, und es zu behaupten wäre genau die Unterform, die heute dreimal
> auffiel: eine richtige Messung, aus der eine zu weite Aussage folgt.*
>
> **Adressat:** A-12 läuft gerade als Messlauf (`4e935e84`, `IN_ARBEIT`). *Dieser Fund gehört seinem
> Bericht, nicht diesem Blatt — hier steht er, weil er beim Ausschluss von `grundriss.ts` anfiel und
> nirgends verloren gehen soll.*

## DECISION

```text
Quelle       roomDetection.ts (190 Z) + die zwei dedizierten Zusagen
NICHT Quelle grundriss.ts (Baukoerper, W-07-Umfeld), polygonFlaeche.ts (W-08)
4-BEDIENUNG  "Nichts Eigenes" — wie bei W-01. Raeume entstehen automatisch, wenn
             Waende einen Umlauf schliessen. Das Blatt sagt, WORAN der Anwender es
             merkt, nicht welche Taste er druckt.
3-FORMELN    nur F-Nummern. Kandidaten: die signierte Flaeche (Shoelace-Familie,
             F-011?) und der Winkel-Umlauf (F-002/F-012?). Jede im Code belegen —
             ACHTUNG: roomDetection hat seine EIGENE signierteFlaeche(), nicht die
             aus polygonFlaeche. Wenn dieselbe Formel zweimal im Code steht, ist das
             ein BEFUND und wird gemeldet, nicht angeglichen.
7-GRENZEN    der Dateikopf nennt die Grenzen schon halb: T-Punkt-Teilung ohne
             Toleranz (mm-Integer), "jede Halbkante gehoert zu genau einer Flaeche
             => keine Endlosschleife". Zu messen: offener Wandzug (kein Umlauf),
             sich kreuzende Waende, Wand mit Laenge 0.
```

## Nicht-Ziele

- **Keine Baukörper-Geometrie.** `grundriss.ts` gehört nicht hierher (siehe Ist-Zustand).
- **Keine Aussage über die Dachkonstruktion.** Der Fund oben ist eine Zulieferung, kein Gegenstand.
- **Kein Registry-Eintrag.** W-05 ist eine Schicht; ein Werkzeug wäre eine eigene Entscheidung.
- **Keine Angleichung der doppelten Flächenformel.** `signierteFlaeche` gegen `polygonFlaecheM2`
  wird **gemeldet**, nicht zusammengelegt. *Dieselbe Regel wie bei `MassPunkt` in W-11.*
- **Keine Änderung an `roomDetection.ts`** oder seinen Zusagen.

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-05-raum-erkennen/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-05 LEER -> BESCHRIEBEN
                                                     + roomDetection.ts als Fundstelle
```

*NICHT im Scope: `resources/**`, `grundriss.ts`, die F-Liste des Registers (N1-Frage).*

## Wiederverwendungsprüfung (§5)

```text
roomDetection.ts       VORHANDEN, 190 Z — Quelle, unangetastet
sein Dateikopf         nennt Verfahren UND Grenzen in zehn Zeilen — beste Quelle
                       fuer 1-ZWECK und 2-FUNKTION
2 dedizierte Zusagen   VORHANDEN — Quelle fuer 6-PRUEFUNG
W-01s Blaetter         Muster fuer ein SCHICHT-Blatt: 4-BEDIENUNG = "Nichts Eigenes",
                       aber die Sichtbarkeit benannt ("nie stumm")
W-01/1 … W-11/1        Muster fuer Struktur, Kriterienform, Rot-Form
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite bleibt
                                                             unveraendert gruen (ohne Zahl —
                                                             W-01N-Regel)
```

**Erstnutzer:** *der Generator von W-05 Stufe 2 — und W-10 (Decke und Boden), das laut Register auf
W-05 aufbaut: eine Decke braucht einen geschlossenen Raum.*

## Akzeptanzkriterien

**W-05/1-1 (P1, kein Platzhalter):** keiner mehr in den sieben Blättern. *Zählweise: alle
`<…>`-Klammern.*

**W-05/1-2 (P1):** `3-FORMELN` nennt nur F-Nummern, keine ausgeschriebene Formel.

**W-05/1-3 (P1, F-Nummern belegt):** jede genannte Nummer mit Zeilennummer in `roomDetection.ts` —
oder ausdrücklich als **nicht in der Sammlung vorhanden** gemeldet. *`signierteFlaeche` ist der
kritische Fall: steht dieselbe Formel als F-011 in der Sammlung, ist es ein Befund und keine
Fundstelle.*

**W-05/1-4 (P1, `7-GRENZEN`):** beantwortet am Code gemessen: **offener Wandzug** (kein Umlauf),
sich kreuzende Wände, Wand mit Länge 0. *Der Dateikopf nennt die Endlosschleifen-Freiheit als
Zusicherung — das Blatt prüft, was **stattdessen** passiert.*

**W-05/1-5 (P1, Herkunft):** `5-CODE` sagt „angebunden aus `geometry/roomDetection.ts`" mit
Exportliste.

**W-05/1-6 (P1, die zwei Ausschlüsse):** `grundriss.ts` und `polygonFlaeche.ts` sind namentlich als
Nicht-Gegenstand benannt, mit Begründung. *`grundriss.ts` heißt „Grundriss" und wird sonst wieder
zugeordnet — meine eigene Matrix hat es getan.*

**W-05/1-7 (P1, die Schicht-Lage ist benannt):** Das Blatt sagt, dass **kein Registry-Werkzeug
existiert** und `erkenneRaeume()` **automatisch** läuft — wie W-01. *`4-BEDIENUNG` sagt „Nichts
Eigenes" und beschreibt stattdessen, **woran der Anwender es merkt**.*

**W-05/1-8 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite **unverändert** grün.
*Ohne Zahl — W-01N-Regel.*

**W-05/1-9 (P1, Register mitgeführt):** Reifegrad **und** `roomDetection.ts` als Fundstelle.

**W-05/1-10 (P1, §3 wird BELEGT):** Befehl mit Ausgabe für „kein anderer Auftrag auf `IN_ARBEIT`",
an beiden Orten, **mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer**. *E2 aus
Prüfung 03.*

## Kantenliste

```text
offener Wandzug                        -> kein Umlauf, kein Raum. Was wird gemeldet?
zwei Waende kreuzen sich               -> T-Punkt-Teilung greift; MESSEN
Wand mit Laenge 0                      -> MESSEN
Raum im Raum (Innenhof)                -> was liefert erkenneRaeume? MESSEN, nicht raten
signierteFlaeche negativ               -> Umlaufrichtung; im Code ist sie das SIGNAL,
                                          nicht ein Fehler — benennen
mm-Integer ohne Toleranz               -> Dateikopf nennt es als ABSICHT
                                          ("keine Toleranz-Magie"). Das ist eine
                                          Entscheidung, kein Grenzfall — als solche schreiben
grundriss.ts als "Grundriss" gelesen   -> Ausschluss, W-05/1-6
```

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** Muss der Generator von Stufe 2 für einen Grenzfall in `grundriss.ts` nachsehen, war
der Ausschluss falsch — dann zurück an den Planner. *Das ist der Preis dieser Trennung, und sie ist
mit 0 gegen 7 Raum-Treffern gemessen, nicht geraten.*

## Konfliktprüfung (§5)

```text
A-12     ENTWURF     FORMELSAMMLUNG + VORGEHEN + BERICHT-A-12    KEINE Beruehrung
W-01N    ENTWURF     W-01-Blatt + FAHRPLAN                       KEINE Beruehrung
W-04/1   ENTWURF     werkbank/W-04/** + REGISTER.md
W-08/1   ENTWURF     werkbank/W-08/** + REGISTER.md
W-11/1   ENTWURF     werkbank/W-11/** + REGISTER.md
W-13/1   ENTWURF     werkbank/W-13/** + REGISTER.md
W-05/1   DIESES      werkbank/W-05/** + REGISTER.md
-> FUENF Blaetter teilen REGISTER.md, je eine Zeile plus Fundstellen, zeilenweise
   disjunkt. §3 loest es; belegt in W-05/1-10.
§3 GEMESSEN 11.08. (korrigiert, siehe docs/MELDUNG-ERFUNDENE-SPERRE-A-12.md):
   grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md   -> 0
   A-12 traegt status: ENTWURF, NICHT IN_ARBEIT.
   -> §3 sperrt W-05/1 NICHT. Es darf in IN_ARBEIT, sobald DoR durch ist.
   Der Vorrang von A-12 (F-026 ist gelb, W-07/W-08 haengen fachlich daran) ist eine
   planerische EMPFEHLUNG, kein Verbot. Die Reihenfolge entscheidet der Plan-Pruefer.
FACHLICHE Beruehrung: W-10 (Decke) baut laut Register auf W-05 auf. Kein Dateikonflikt.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "Runde 2: W-05 -> W-21 -> W-22"
korrektur_am_fahrplan: "W-05 ist 190 Z / 4 Exporte, nicht 371 / 16 — grundriss.ts und
                        polygonFlaeche.ts gehoeren nicht dazu"
schliesst_offene_frage: "Werkzeug-oder-Schicht-Vorlage, vierter Kandidat W-05: es ist
                         eine SCHICHT, am Code gemessen (0 Registry-Treffer, automatischer Lauf)"
zulieferung_an: "A-12 / Dachkonstruktion — grundriss.ts traegt die Bausteine der
                 Formerkennung (eckenAnalyse, anzahlInnenwinkel, erwarteteInnenwinkel).
                 A-05s Luecke 4 bleibt gueltig, aber der Weg ist kuerzer als sie vermuten laesst."
```
