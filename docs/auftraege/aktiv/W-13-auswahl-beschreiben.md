# W-13 Stufe 1 — Auswahl und Griffe BESCHREIBEN, und ein Modul gehört zu W-14

```yaml
auftrag: "W-13/1"
werkzeug: "W-13 Auswahl und Griffe"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 GEBAUT folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-13 aus vier vorhandenen Auswahl-Modulen ableiten"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 193681cd
prioritaet: P1
anlass: "Yamas Auftrag 10.08. — Fundamentstufe aus dem Register; Nachschnitt auf seine Ansage"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt, wie bei W-01 und W-02."
muster: "W-01-fang-beschreiben.md"
```

## Ist-Zustand — vier Module gehören dazu, eines nicht

**Anbindungsmessung nach Yamas Punkt 4, vor dem Schnitt gefahren:**

```text
GEHOERT ZU W-13
app/tools/auswahlModus.ts              98 Zeilen
  Auswahlmodus · Modifikatoren · aufloeseAuswahlmodus · Auswahlstand
  wendeAuswahlAn · klickInsLeere · LEERE_AUSWAHL
app/tools/auswahlDarstellung.ts        71 Zeilen
  DarstellungEingabe · Darstellung · aufloeseDarstellung
app/tools/auswahlUebersicht.ts         77 Zeilen
  TypZaehlung · MehrfachUebersicht · mehrfachUebersicht · benenne
app/tools/trefferSuche.ts              75 Zeilen
  TrefferKandidat · besterTreffer · trefferInReihenfolge · toleranzInWelt
Registry-Werkzeug 'auswahl'            vorhanden
                                       ---------------------------------
                                       321 Zeilen, 18 Exporte

GEHOERT NICHT ZU W-13 — meine Matrix hat es falsch zugeordnet
geometry/editierGeometrie.ts           75 Zeilen
  versetzePunkt · versetzteWand · spiegelePunkt · spiegelteWand
  Bbox · bbox · achsenMitte · Achse
  -> das ist VERSETZEN und SPIEGELN, also W-14 (Kopieren·Spiegeln·Drehen).
     W-13 ist Auswahl und GRIFFE — das Auswaehlen und Anfassen, nicht das Verschieben.
     `bbox`/`achsenMitte` sind Hilfsmittel, die BEIDE brauchen; sie liegen richtig
     in geometry/, gehoeren aber im Register zu W-14.
```

> **Zweiter Fall derselben Klasse in einem Auftrag.** Bei W-02 waren es `wandaufbau` und
> `linienBauteile`, hier ist es `editierGeometrie`. **Alle drei stehen in meiner Anschlussmatrix
> unter dem falschen Werkzeug**, weil ich nach Modulnamen zugeordnet habe. *Die Warnung stand im
> selben Dokument — sie zu schreiben hat nicht gereicht, erst die Messung je Werkzeug hat es
> gefunden.*

## Der zweite Befund — die Absicherung ist dünn

```text
321 Zeilen Auswahl-Logik  ·  18 Exporte  ·  aber nur EINE Testdatei
Vergleich: W-02 hat 555 Zeilen und SIEBEN Zusagen
           W-01 hat 276 Zeilen und DREI Zusagen
```

**Das ist kein Mangel dieses Auftrags, aber es gehört ins Blatt** — `6-PRUEFUNG` kann sich hier
nicht auf vorhandene Zusagen stützen wie bei W-01 und W-02. *Wer W-13 Stufe 2 baut, baut auf einer
dünner abgesicherten Grundlage; das muss er wissen, bevor er anfängt, nicht danach.*

## DECISION

```text
Quelle       die vier Auswahl-Module + der Registry-Eintrag 'auswahl' + die eine Zusage
NICHT Quelle editierGeometrie.ts — gehoert zu W-14, mit Begruendung im Blatt
3-FORMELN    nur F-Nummern. Kandidaten: F-003 (Lot/Naehe -> besterTreffer),
             F-012 (laut Register bei W-13 gefuehrt), F-040/F-041 (Toleranz ->
             toleranzInWelt). Jede Nummer im Code belegen.
5-CODE       "angebunden aus app/tools/auswahlModus.ts + auswahlDarstellung.ts +
             auswahlUebersicht.ts + trefferSuche.ts", mit Exportliste.
7-GRENZEN    am Code messen — der Anker ist bereits gebaut:
             `klickInsLeere` und `LEERE_AUSWAHL` sagen, was bei keinem Treffer
             passiert. Was tut `besterTreffer` bei mehreren gleich nahen Kandidaten?
             `trefferInReihenfolge` deutet auf eine Entscheidung hin — MESSEN.
6-PRUEFUNG   die duenne Zusagenlage ausdruecklich benennen (siehe zweiter Befund)
```

**Kein Code wird angefasst.**

## Nicht-Ziele

- **Kein Versetzen, Spiegeln, Drehen.** Das ist W-14 (siehe Ist-Zustand).
- **Keine neuen Zusagen.** Die dünne Absicherung wird **benannt**, nicht behoben — das wäre
  Stufe 2 oder ein eigener Auftrag. *Ein Doku-Auftrag, der Tests schreibt, ist kein Doku-Auftrag.*
- **Keine Änderung an den vier Modulen** und an der Registry.
- **Keine Aussage über Griff-Darstellung.** Wie Griffe gezeichnet werden, ist Renderer-Sache.

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-13-auswahl-und-griffe/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-13 LEER -> BESCHRIEBEN
                                                     + die vier Fundstellen nachtragen
```

*NICHT im Scope: `resources/**`, und der Matrix-Nachtrag zu `editierGeometrie` (eigener Vorgang).*

## Wiederverwendungsprüfung (§5)

```text
die vier Auswahl-Module   VORHANDEN, 321 Zeilen — Quelle, unangetastet
Registry 'auswahl'        VORHANDEN — Quelle fuer 4-BEDIENUNG
eine Zusage               VORHANDEN, aber duenn — Quelle fuer 6-PRUEFUNG, mit Vermerk
klickInsLeere/LEERE_AUSWAHL  VORHANDEN — der Grenzfall ist gebaut, also belegbar
W-07-Blaetter             Muster fuer Form und Tiefe
W-01/1, W-02/1            Muster fuer die Stufenteilung
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite unberuehrt
```

**Erstnutzer:** *der Generator von W-13 Stufe 2 — und er ist der erste, der auf eine dünne
Zusagenlage trifft; das Blatt muss ihn warnen.*

## Akzeptanzkriterien

**W-13/1-1 (P1, kein Platzhalter):** keiner mehr in den sieben Blättern. *Rot heute selbst
gemessen: **8**. Gegenprobe W-07 = 0.*

**W-13/1-2 (P1):** `3-FORMELN` nennt nur F-Nummern, keine ausgeschriebene Formel.

**W-13/1-3 (P1):** jede F-Nummer mit Zeilennummer in einem der vier Module belegt. Rechnung ohne
Nummer in der Sammlung → **Befund melden**, nicht eintragen.

**W-13/1-4 (P1, `7-GRENZEN`):** beantwortet, was bei **keinem** und was bei **mehreren gleich nahen**
Treffern passiert — am Code gemessen. *Anker: `klickInsLeere`, `LEERE_AUSWAHL`,
`trefferInReihenfolge`. **Der Grenzfall ist gebaut; er muss nur gelesen werden.***

**W-13/1-5 (P1, Herkunft):** `5-CODE` sagt „angebunden aus …" mit allen **vier** Modulen und
Exportliste.

**W-13/1-6 (P1, der Ausschluss steht im Blatt):** `editierGeometrie.ts` ist **namentlich als
Nicht-Gegenstand benannt, mit Verweis auf W-14.** *Ohne dieses Kriterium ordnet der nächste Leser es
wieder zu — meine Matrix hat es getan.*

**W-13/1-7 (P1, die dünne Absicherung ist benannt):** `6-PRUEFUNG` sagt ausdrücklich, dass 321
Zeilen von **einer** Zusage abgedeckt sind, im Vergleich zu 7 bei W-02 und 3 bei W-01. *Eine
Beschreibung, die eine dünne Grundlage verschweigt, lässt Stufe 2 in eine Falle laufen.*

**W-13/1-8 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite unverändert grün.

**W-13/1-9 (P1, Register mitgeführt):** Reifegrad **und** die vier Fundstellen im Abschnitt „Was
schon im Repo existiert".

## Kantenliste

```text
kein Treffer                       -> klickInsLeere / LEERE_AUSWAHL: gebaut, lesen
mehrere gleich nahe Kandidaten     -> trefferInReihenfolge: MESSEN, was entscheidet
Mehrfachauswahl gemischter Typen   -> mehrfachUebersicht/TypZaehlung: beschreiben
`benenne`                          -> Zweck klaeren (Beschriftung? Umbenennen?) oder MELDEN
toleranzInWelt vs. toleranzAusZoom -> ZWEI Toleranzwege (W-13 und W-01). Beruehrung
   (W-01 fangKern)                    benennen, NICHT zusammenlegen — das waere ein
                                      Bauentscheid und gehoert nicht in ein Doku-Blatt
```

> *Der letzte Punkt ist der interessanteste Fund: **W-01 und W-13 haben je einen eigenen
> Toleranzbegriff** (`toleranzAusZoom` in `fangKern`, `toleranzInWelt` in `trefferSuche`). Ob das
> eine zweite Wahrheit ist oder zwei berechtigte Begriffe, entscheidet dieses Blatt nicht — es
> **benennt** die Berührung, damit W-01/1 und W-13/1 sie beide nennen und niemand sie später
> „aufräumt", ohne den Grund zu kennen.*

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** wie bei W-01/W-02. Zusätzlich hier: findet Stufe 2, dass der Toleranzbegriff doch
zusammengehört, ist das ein Befund an den Planner — **nicht** eine stille Zusammenlegung.

## Konfliktprüfung (§5)

```text
A-10   CODE_FERTIG   renderers/**              KEINE Beruehrung
A-09/A-11 ENTWURF    scripts/**                KEINE Beruehrung
W-01/1 ENTWURF       werkbank/W-01/** + REGISTER.md
W-02/1 ENTWURF       werkbank/W-02/** + REGISTER.md
W-13/1 DIESES        werkbank/W-13/** + REGISTER.md
-> DREI Blaetter aendern REGISTER.md, je eine Zeile plus Fundstellen. Auf Zeilenebene
   disjunkt, dieselbe Datei. REIHENFOLGE W-01/1 -> W-02/1 -> W-13/1; §3 (ein IN_ARBEIT)
   loest die Beruehrung, solange die Reihenfolge haelt.
FACHLICHE Beruehrung mit W-01: der Toleranzbegriff (Kantenliste). Kein Dateikonflikt.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "W-01/1 -> W-02/1 -> W-13/1 (W-12 zurueckgehalten, Einwand bei Yama)"
befund_an_matrix: "editierGeometrie.ts ist in WERKBANK-ANSCHLUSS.md falsch unter W-13
                   gefuehrt — gehoert zu W-14. Eigener Nachtrag."
befund_bestand: "321 Zeilen Auswahl-Logik, nur EINE Zusage. Kein Auftragsgegenstand,
                 aber im Blatt zu benennen."
```
