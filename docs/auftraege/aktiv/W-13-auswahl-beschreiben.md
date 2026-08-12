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

## Der zweite Befund — die Absicherung ist dünn, mit DEFINIERTER Messweise

> **⚠ KORRIGIERT nach dem DoR-Rest des Plan-Prüfers (`debf3fbe`).** Hier stand *„nur EINE
> Testdatei"* gegen *„SIEBEN bei W-02"* und *„DREI bei W-01"* — **drei Zahlen aus zwei verschiedenen
> Methoden.** Er hat es beanstandet: *„zählweise-abhängig, Zahl braucht definierte Messweise."*
> Er hat recht, und der Fehler war schlimmer als „unscharf":
>
> **Meine „eine Zusage" war `editierGeometrie.test.ts` — die Zusage des Moduls, das ich im selben
> Blatt als Nicht-Gegenstand ausgeschlossen habe.** Derselbe Namensfilter
> (`grep -icE 'auswahl|treffer|editier'`), derselbe Fehler wie in der Anschlussmatrix.

**Die Messweise, ab hier verbindlich für alle W-Blätter:**

```text
DEDIZIERT    Testdatei, deren Name mit einem Modulnamen des Werkzeugs beginnt
             ls __tests__/ | grep -icE '^(<modul1>|<modul2>)\.'
ERWAEHNEND   Testdatei, die ein Modul des Werkzeugs IMPORTIERT
             grep -rlE "from '.*(<modul1>|<modul2>)'" __tests__/ | wc -l
```

**Alle drei Werkzeuge mit dieser einen Messweise gemessen:**

```text
            dediziert   erwaehnend
W-01              1          3        fangKern.test.ts + 2 weitere
W-02              2          7        wallGeometry.test.ts, wandFlaeche.test.ts + 5
W-13              0          2        markieren.test.ts, teilKennung.test.ts
```

**Der Befund hält und ist jetzt belegt:** W-13 ist das einzige der drei **ohne eine einzige
dedizierte Zusage** — 321 Zeilen und 18 Exporte, für die keine Testdatei zuständig ist.

*Anmerkung zur Genauigkeit der 7 bei W-02: darunter ist `typprobe-wandFlaeche.tsprobe`, keine
Testdatei im engeren Sinn. Streng gezählt sind es 6. Ich nenne beide Zahlen, statt eine zu wählen —
für den Vergleich mit W-13 (0 dediziert) ist der Unterschied ohne Belang.*

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

**W-13/1-7 (P1, die dünne Absicherung ist benannt — mit Messweise):** `6-PRUEFUNG` sagt
ausdrücklich, dass W-13 **null dedizierte Zusagen** hat (W-01: 1, W-02: 2) und **zwei erwähnende**
(W-01: 3, W-02: 7) — **nach der im Blatt definierten Messweise**, die im Text mitsteht, damit die
Zahl nachrechenbar ist. *Eine Beschreibung, die eine dünne Grundlage verschweigt, lässt Stufe 2 in
eine Falle laufen — und eine Zahl ohne Messweise ist keine Zahl.*

**W-13/1-8 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite unverändert grün.

**W-13/1-9 (P1, Register mitgeführt):** Reifegrad **und** die vier Fundstellen im Abschnitt „Was
schon im Repo existiert".

**W-13/1-10 (P1, §3 wird BELEGT, nicht behauptet — NEU 10.08.):** Der `IN_ARBEIT`-Commit enthält den
**Befehl mit Ausgabe** für „kein anderer Auftrag steht auf `IN_ARBEIT`" — **mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer**, **an beiden Orten geprüft**
(Tafelzeile **und** `^zustand:`-Feld), im **selben** Commit, der `IN_ARBEIT` setzt.

> *Wortgleich zu `W-01/1-8` und `W-02/1-9`. Rot-Lage: `7dcbeba9` behauptete es ohne Beleg und lief
> als zweites `IN_ARBEIT` durch. Grenze ehrlich: der Nachweis verkleinert das Fenster auf die Dauer
> eines Commits, er schließt es nicht — dafür braucht es **einen** Ort für den Zustand.*

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
   disjunkt, dieselbe Datei. REIHENFOLGE W-01/1 -> W-02/1 -> W-13/1.
   §3 (ein IN_ARBEIT) loest die Beruehrung NUR, wenn er auch eingehalten wird — und das
   ist am 10.08. um 20:25 nachweislich misslungen. Deshalb steht der Nachweis als
   Kriterium W-13/1-10 im Blatt, nicht als Zusicherung in dieser Zeile.
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


## §11 — Bericht W-13/1 (Generator, 12.08.2026)

```yaml
auftrag: "W-13/1"
zustand: CODE_FERTIG
bau_commit: "a62ae7c6"
in_arbeit_commit: "3e7fa5b7"
basis: "193681cd"

kriterien:
  W-13/1-1:  GRUEN   # 28 -> 0 nach BEIDEN Zaehlweisen
  W-13/1-2:  GRUEN   # nur Nummern
  W-13/1-3:  GRUEN   # keine F-Nummer trifft zu; die eine Rechnung ohne Nummer GEMELDET
  W-13/1-4:  GRUEN   # kein Treffer UND mehrere gleich nahe, beide am Code gelesen
  W-13/1-5:  GRUEN   # vier Module mit Exportlisten und Zeilen
  W-13/1-6:  GRUEN   # editierGeometrie.ts namentlich, mit Verweis auf W-14 — nachgemessen
  W-13/1-7:  GRUEN   # 0 dediziert / 2 erwaehnend, MIT Messweise im Blatt
  W-13/1-8:  GRUEN   # 0/0/0, Suite unveraendert gruen
  W-13/1-9:  GRUEN   # Register: BESCHRIEBEN + alle vier Fundstellen
  W-13/1-10: GRUEN   # 3e7fa5b7: 2 Befehlszeilen, 2 Ausgabewerte

der_kernvertrag:
  oben_schlaegt_nah: "trefferInReihenfolge (trefferSuche.ts:56-65) sortiert ZUERST nach
                      Zeichenreihenfolge absteigend, ERST DANN nach Distanz aufsteigend.
                      Bei gleich nahen Treffern gewinnt das OBERE. Wer die Sortierung
                      'naechstes Objekt gewinnt' nennt, beschreibt den Ausnahmefall."
  waehlbar_filter: "trefferSuche.ts:58 prueft `waehlbar !== false`, NICHT `=== true`.
                    Ein Kandidat ohne gesetztes Feld ist waehlbar. Wer die Pruefung umstellt,
                    macht stillschweigend alles unwaehlbar, was es nicht ausdruecklich setzt."
  modifikator_vorrang: "alt -> remove, ctrl/meta -> toggle, shift -> add, sonst replace.
                        Eine Kette, kein Schalterfeld: Alt+Shift ergibt remove."
  primaerstand: "wird das PRIMAERE Objekt entfernt, rueckt das ZULETZT verbliebene nach;
                 sonst bleibt der Primaerstand unveraendert (auswahlModus.ts:84-85)."
  klick_ins_leere: "ohne Modifikator hebt er die Auswahl auf, MIT Modifikator nicht.
                    Wer bei gedrueckter Umschalttaste danebentrifft, verliert nicht alles."

zu_den_formeln:
  ich_korrigiere_nichts: "das Register fuehrt W-13 bereits als 'keine' und hat F-012 und F-003
                          gestrichen. Meine Messung BESTAETIGT das."
  was_stattdessen: "genau eine Rechnung in allen vier Modulen — toleranzInWelt(pixel, zoom)
                    (trefferSuche.ts:72-74), eine Einheitenumrechnung ohne Nummer in der Sammlung.
                    Nach W-13/1-3 als Befund GEMELDET und NICHT eingetragen."
  keine_abstaende: "TrefferKandidat bringt seine distanz MIT — hier wird nicht gemessen, sondern
                    sortiert. Wer eine Abstandsformel sucht, findet sie bei W-01."

ausschluss_nachgemessen:
  behauptung_des_auftrags: "editierGeometrie.ts gehoert zu W-14, nicht zu W-13"
  meine_messung: "STIMMT — kein einziger der acht Exporte betrifft Auswahl oder Griffe;
                  versetzePunkt, versetzteWand, spiegelePunkt, spiegelteWand, Bbox, bbox,
                  achsenMitte, Achse betreffen alle die Lageaenderung."

die_duenne_stelle:
  messweise: "dediziert = Testdatei, deren DATEINAME mit dem Modulnamen beginnt;
              erwaehnend = Testdatei, die das Modul IMPORTIERT"
  ergebnis: "0 dedizierte fuer alle vier Module. Zwei erwaehnende Dateien (markieren.test.ts,
             teilKennung.test.ts) mit zusammen 36 Zusagen."
  vergleich: "W-01 fangKern 2 dedizierte, W-02 wallGeometry 1 dedizierte"
  einordnung: "keine Grenze des Werkzeugs, sondern eine Grenze dessen, was man ueber es weiss"

besonderheit: "W-13 ist das EINZIGE Klasse-A-Werkzeug MIT Registry-Eintrag
               (app/tools/toolRegistry.ts:39, id 'auswahl'). Stufe 2 hat hier eine andere
               Ausgangslage als bei W-01, W-05, W-08, W-21 und W-22."

eigener_fehler_vor_dem_melden:
  was: "DREI Zeilenangaben falsch — trefferSuche 59 statt 58, 69-72 statt 50-53, 3-5 statt 4"
  danach: "22 Stellen inhaltlich geprueft, 0 falsch"
  bemerkung: "die zwei letzten waren zunaechst FALSCH-Meldungen meiner eigenen Pruefung
              (Umlaute im Suchbegriff). Erst die exakte Messung hat gezeigt, dass EINE davon
              echt war. Eine Pruefung, die falsch Alarm schlaegt, ist besser als eine, die
              schweigt — aber ihre Treffer sind selbst zu pruefen."

nicht_gemessen:
  - "was die 36 Zusagen der zwei erwaehnenden Dateien tatsaechlich abdecken — gezaehlt wurde,
     WELCHE Dateien importieren, nicht WAS darin steht. Als Frage in 6-PRUEFUNG notiert."

browserabnahme: "entfaellt — reine Dokumentblaetter"
ballbesitz: evaluator
```

---

## Evaluator-Votum (§11) — 12.08.2026

```yaml
auftrag: "W-13/1"
commit: a62ae7c6          # Bau; Basis 193681cd
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "vierzehn Fundstellen einzeln geoeffnet · die Absicherungs-Zahlen des Blattes
  (321 Zeilen, 18 Ausfuhren, 0 dediziert, 36 Zusagen) Modul fuer Modul und Datei fuer Datei
  selbst nachgezaehlt"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde: []
```

### Messtisch — ALLE ZEHN Zeilen

```text
-1   Platzhalter, vier Muster                     0
-2   3-FORMELN: das eine '=' ist ein CODE-ZITAT — siehe unten
-3   vierzehn Fundstellen, ALLE einzeln geoeffnet, keine laeuft ins Leere
-4   7-GRENZEN beantwortet BEIDE Faelle: kein Treffer (drei Unterfaelle mit Fundstelle)
     und mehrere gleich nahe ("OBEN gewinnt, nicht NAH", trefferSuche.ts:60-65)
-5   Herkunft "angebunden aus …" mit allen vier Modulen
-6   Ausschluss editierGeometrie.ts namentlich benannt     3 Nennungen
-7   duenne Absicherung mit Messweise — Zahlen unten selbst nachgezaehlt
-8   resources/ im Bau-Commit 0 Pfade  ·  Suite 1692/1692
-9   Register: alle vier Fundstellen
-10  §3-Beleg in 3e7fa5b7                          2 Befehlszeilen, 2 Ausgaben
```

### `-7` ist der wertvollste Teil — und jede Zahl darin habe ich nachgezählt

**Das Blatt behauptet: „Null dedizierte Zusagen bei 321 Zeilen und 18 Ausfuhren", dazu zwei
erwähnende Dateien mit 36 Zusagen zusammen.** *Ich habe es nicht geglaubt, sondern gezählt:*

```text
                        Zeilen  Ausfuhren     dedizierte Testdatei
auswahlModus.ts            98       7                 0
trefferSuche.ts            75       4                 0
auswahlUebersicht.ts       77       4                 0
auswahlDarstellung.ts      71       3                 0
SUMME                     321      18                 0        <- Blatt: 321 / 18 / 0

markieren.test.ts     21 Zusagen
teilKennung.test.ts   15 Zusagen
                      36 zusammen                              <- Blatt: 36
```

**Vier Zahlen, vier Treffer.** *Ein Blatt, das seine eigene dünne Grundlage beziffert statt sie zu
verschweigen, ist mehr wert als eines, das grün aussieht — und der Satz daneben sagt auch warum:
„Eine Beschreibung, die eine dünne Grundlage verschweigt, lässt Stufe 2 in eine Falle laufen."*

### Zu `-2`: das eine `=` ist kein Verstoß

```text
BLATT  trefferSuche.ts:72-74   toleranzInWelt(pixel, zoom) = zoom > 0 ? pixel / zoom : pixel
CODE   :73  export function toleranzInWelt(pixel: number, zoom: number): number
       :74  return zoom > 0 ? pixel / zoom : pixel;
```

**Es ist das wörtliche Zitat der einzigen Rechnung in allen vier Modulen** — und das Blatt zieht
daraus ausdrücklich **keinen** Sammlungseintrag: *„Eine Einheitenumrechnung, keine Geometrieformel.
Eine Division ist keine Formel, die man nachschlägt."* **Das ist die richtige Antwort auf `-3`,
nicht eine Umgehung von `-2`.**


## Release-Prüfung (§10, Sammel-Kontrolle 3) — 12.08.2026

```yaml
auftrag: "W-13/1"
abnahme_commit: a62ae7c6
release_commit: a62ae7c6
votum: RELEASE_FREI
ci: pass
artefakte_reproduzierbar: true
migration: nicht_anwendbar
rueckweg: nicht_anwendbar
smoke_test_plan: "Doku-Stufe ohne Laufzeitanteil — der betriebliche Nachweis ist der erste
  Stufe-2-Bauversuch gegen die sieben Blaetter. Regressionswache: Insel-Suite 1692/1692,
  von mir selbst gefahren."
befunde: []
```

### Die Kette, je Stufe mit `merge-base --is-ancestor` gegen die folgende

```text
BEREIT        6df53243   (2. Runde; die 1. endete mit einem Mini-Rest)
IN_ARBEIT     3e7fa5b7
Bau           a62ae7c6   8 Dateien = sieben Blaetter + REGISTER.md
CODE_FERTIG   fbc361a7
ABGENOMMEN    ce30ff98
letzte Stufe gegen HEAD geprueft — Kette lueckenlos, eine Runde, keine Nachbesserung.
Basis 193681cd ist Vorfahr des Bau-Commits (nachgemessen).
```

**Scope-Reinheit:** `a62ae7c6` trägt **0** Pfade unter `resources/` und **0** unter `scripts/`.
**Das Votum nennt den gemessenen Commit:** `commit: a62ae7c6` im Votum-YAML, `ABGENOMMEN an
a62ae7c6` in `STATUS.md`, Release-Kandidat `a62ae7c6`.

### Die Pflichtfrage — trägt der Messtisch JEDE Kriterienzeile? Gezählt.

```text
Kriterien im Blatt                          10   W-13/1-1 … -10
im Evaluator-Votum ausgewiesen              10   Messtisch "ALLE ZEHN Zeilen", -1 bis -10,
                                                 jede mit eigenem Messwert
FEHLEND                                      0
```

**10 gegen 10 — vollständig.** *Der Messtisch trägt zusätzlich die Zahlen, die `-7` behauptet, und
zwar nachgezählt statt geglaubt: 98/7, 75/4, 77/4, 71/3, Summe 321 Zeilen und 18 Ausfuhren, 0
dedizierte Testdateien, 36 Zusagen in den zwei erwähnenden Dateien.* **Vier Behauptungen, vier
eigene Zählungen — das ist §11 letzter Satz in Reinform.**

### Zwei Hinweise des Plan-Prüfers, in den Vermerk genommen — **keine Hindernisse**

**1 · Der Typ-Komplex, sechster und schärfster Fall.** `Auswahlstand` (`auswahlModus.ts:50-54`)
beschreibt denselben Zustand wie die losen Store-Felder `selectedNodeIds` und `primaerId`
(`hausplanerStore.ts:30` / `:36`). *Schärfer noch:* `LEERE_AUSWAHL` trägt den Kommentar **„eine
Stelle, damit ‚nichts ausgewählt' überall dasselbe heißt"** — und der Store schreibt das Literal
**dreimal selbst** (`:74-75`, `:89-90`, `:103`) und **importiert die Konstante nie**. *Eine
Konstante, deren erklärter Zweck „eine Stelle" ist und die an keiner der drei Stellen benutzt wird,
ist die teuerste Form dieses Befunds: sie sieht aus wie die Lösung des Problems, das sie hat.*

**2 · Die „Griffe"-Hälfte ist entschieden, aber nicht gezeichnet.** *Von 18 Ausfuhren sind nur vier
produktiv verdrahtet;* `auswahlDarstellung.ts` und `trefferSuche.ts` **haben außerhalb von
`markieren.test.ts` keinen Aufrufer.** *Das Blatt beziffert seine dünne Grundlage vorbildlich
(`-7`), aber die Frage „was davon läuft überhaupt" beantwortet es nicht.*

**Warum beides den Release nicht hält:** *keines der zehn Kriterien verlangt es.* **Der Auftrag
war, den vorhandenen Code zu BESCHREIBEN, nicht ihn zu bewerten** — und ein Release-Prüfer, der
gegen eine ungestellte Anforderung misst, verschiebt die Ziellinie nach der Abnahme. *Beide Punkte
sind an den Planner adressiert: der Typ-Komplex hat mit diesem Fall sechs Belege und ist damit ein
eigenes Blatt wert; die Verdrahtungslücke gehört in die Stufe, die W-13 tatsächlich baut.*

### Stichprobe

```text
Platzhalter in den sieben Blaettern    <…> 0 · TODO/TBD/XXX/FIXME 0 · F-0xx/W-xx 0
REGISTER.md Z.22                       W-13 | Auswahl und Griffe | BESCHRIEBEN | W-02 | keine ⓝ
REGISTER.md Fundstellen                auswahlModus.ts 1 · trefferSuche.ts 2 ·
                                       auswahlUebersicht.ts 1 · auswahlDarstellung.ts 1
                                       -> alle vier Module getragen
Werkzeugordner seit der Abnahme        a62ae7c6..HEAD  0 Commits
```

### Gemeinsame Messungen der Sammel-Kontrolle 3

*Belege vollständig im W-01-Blatt unter derselben Überschrift.* **Kurzfassung:**

```text
npm run test:hausplaner                                 1692/1692, fail 0
must_preserve drei Richtungen einzeln, resources/       0 · 0 · 0
must_preserve drei Richtungen einzeln, scripts/         0 · 0 · 0
Beifang d4eca213..HEAD -- resources/ scripts/           1 Commit (b0f4c444 = A-11-Bau,
                                                        eigener Auftrag, nur scripts/, 0 resources/)
a62ae7c6..HEAD -- resources/ scripts/                   0 Commits
```

**Urteil: `RELEASE_FREI`.**
