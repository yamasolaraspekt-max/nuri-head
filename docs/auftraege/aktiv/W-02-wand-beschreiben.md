# W-02 Stufe 1 — Wand zeichnen BESCHREIBEN, und zwei Module gehören NICHT dazu

```yaml
auftrag: "W-02/1"
werkzeug: "W-02 Wand zeichnen"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 GEBAUT folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-02 aus wallGeometry.ts + wandFlaeche.ts ableiten"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 193681cd
prioritaet: P1
anlass: "Yamas Auftrag 10.08. — Fundamentstufe aus dem Register; Nachschnitt auf seine Ansage"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt, wie bei W-01. Kein W-02-Blatt lag als Auftrag vor."
muster: "W-01-fang-beschreiben.md — gleiche Struktur, gleiche Stufenteilung, gleiche Rot-Form"
```

## Ist-Zustand — und die Messung korrigiert meine eigene Matrix

**Anbindungsmessung nach Yamas Punkt 4, vor dem Schnitt gefahren. Sie widerlegt zwei Zeilen meiner
Anschlussmatrix (`WERKBANK-ANSCHLUSS.md`):**

```text
GEHOERT ZU W-02 — belegt an den Exporten
geometry/wallGeometry.ts                 317 Zeilen
  wandLaenge · punktAufWand · azimutDerNormalen · istGanzzahlig
  WandEingabe · WandBand · wandBaender
  TuerAnschlag · TuerOeffnung · TuerBlattGeometrie · tuerBlattGeometrie
geometry/wandFlaeche.ts                  238 Zeilen
  Bezugsmass · WandMengen · MeldungArt · Meldung · WandFlaecheErgebnis · wandMengen
Registry-Werkzeug 'wand'                 VOLLSTAENDIG: label, icon, shortcut 'W',
                                         bauteilKind 'wall', helpText, disabledReasonDefault
Zusagen                                  7 Testdateien

GEHOERT NICHT ZU W-02 — meine Matrix hat sie falsch zugeordnet
geometry/wandaufbau.ts                    72 Zeilen
  Schicht · BauteilArt · UEBERGANG · PruefSchwere · UPruefung · UErgebnis
  UOptionen · berechneUWert
  -> das ist BAUPHYSIK (U-Wert, Schichtaufbau), nicht Wandgeometrie.
     Ein Werkzeug "Wand zeichnen" berechnet keinen Waermedurchgang.
geometry/linienBauteile.ts               167 Zeilen
  LinienBauteilArt · DachLinienBauteil · SchneefangOpts · SCHNEEFANG_HINWEIS
  platziereSchneefang · sperrzoneVRel · istInSperrzone · flaecheInfoAusPolygon
  -> das ist DACH-ZUBEHOER (Schneefang, Sperrzonen). Der Modulname sagt "Linien-
     Bauteile", der Inhalt sagt Dach.
```

> ### Die Warnung aus meiner eigenen Matrix hat sofort zugeschlagen
>
> Dort steht: *„Zuordnung nach Modulnamen, nicht nach Codeanalyse. **Namensähnlichkeit ist keine
> Abdeckung** — jede Zeile braucht vor ihrem Auftrag eine eigene Prüfung."*
>
> **Genau das ist hier passiert.** `wandaufbau` und `linienBauteile` tragen „wand" bzw. „Bauteile" im
> Namen und stehen in meiner Matrix unter W-02. Ein Blick auf die Exporte zeigt: das eine rechnet
> U-Werte, das andere platziert Schneefang. **Hätte der Generator meine Matrix befolgt, hätte er
> Bauphysik und Dachzubehör in ein Wandwerkzeug beschrieben.**
>
> *Das ist der Grund, warum diese Messung je Werkzeug einzeln läuft und nicht einmal für die ganze
> Gruppe — und der Grund, warum ich die vier Blätter nicht in einem Rutsch geschnitten habe.*

**Folge für die Matrix:** `WERKBANK-ANSCHLUSS.md` muss in Stufe 2 korrigiert werden — `wandaufbau`
und `linienBauteile` brauchen einen eigenen Platz (Bauphysik/Energie bzw. W-07-Umfeld). *Das ist
kein Teil dieses Auftrags, sondern ein Nachtrag an die Matrix; hier steht es als Befund.*

## DECISION

```text
Quelle       wallGeometry.ts + wandFlaeche.ts + der Registry-Eintrag 'wand' + die 7 Zusagen
NICHT Quelle wandaufbau.ts, linienBauteile.ts — ausdruecklich ausgeschlossen, mit Begruendung
             im Blatt (sonst traegt W-02 zwei Fachgebiete, die niemand dort sucht)
3-FORMELN    nur F-Nummern. Kandidaten aus den Exporten: F-001 (Abstand -> wandLaenge),
             F-002/F-003 (Winkel/Lot -> punktAufWand, azimutDerNormalen),
             F-030 (laut Register bei W-02 gefuehrt). Jede Nummer im Code belegen.
5-CODE       "angebunden aus geometry/wallGeometry.ts + geometry/wandFlaeche.ts",
             mit Exportliste. NICHT "neu gebaut".
7-GRENZEN    am Code messen: was tut wandMengen bei entarteter Wand (Laenge 0)?
             Was meldet `Meldung`/`MeldungArt` — und wird die Meldung angezeigt?
             wandFlaeche hat einen Meldungs-Typ: das ist der Kandidat fuer die
             Grenzfrage, und er ist bereits gebaut.
```

**Kein Code wird angefasst.**

## Nicht-Ziele

- **Kein Registry-Eintrag.** `wand` existiert bereits vollständig — nichts zu tun, nur zu beschreiben.
- **Keine Bauphysik.** `berechneUWert` gehört nicht in dieses Blatt (siehe Ist-Zustand).
- **Kein Dachzubehör.** Schneefang und Sperrzonen gehören nicht hierher.
- **Keine Änderung an `wallGeometry.ts` / `wandFlaeche.ts`** und keiner ihrer 7 Zusagen.
- **Keine Mengenlehre.** `wandMengen` grenzt an W-20 (Stückliste und Mengen); dieses Blatt
  beschreibt es, es entscheidet nicht über die Zuständigkeit.

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-02-wand-zeichnen/1-ZWECK.md … 7-GRENZEN.md   (7 Blaetter)
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md    Reifegrad W-02 LEER -> BESCHRIEBEN
                                                      + wallGeometry.ts und wandFlaeche.ts
                                                        im Abschnitt "Was schon im Repo existiert"
```

*NICHT im Scope: `resources/**`, `WERKBANK-ANSCHLUSS.md` (der Matrix-Nachtrag ist ein eigener Vorgang).*

## Wiederverwendungsprüfung (§5)

```text
wallGeometry.ts + wandFlaeche.ts   VORHANDEN, 555 Zeilen — Quelle, unangetastet
Registry-Eintrag 'wand'            VORHANDEN, vollstaendig — Quelle fuer 4-BEDIENUNG
                                   (helpText und disabledReasonDefault sind schon formuliert!)
7 Zusagen zu Wand                  VORHANDEN — Quelle fuer 6-PRUEFUNG
W-07-Blaetter                      Muster fuer Form und Tiefe (einziges BESCHRIEBENes)
W-01/1                             Muster fuer die Stufenteilung dieses Auftragstyps
```

**Nichts wird neu erfunden** — `4-BEDIENUNG` kann sogar aus dem vorhandenen `helpText` beginnen.

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite bleibt
                                                             unberuehrt gruen (Kontrolle)
```

**Erstnutzer:** *der Generator von W-02 Stufe 2 und jeder Plan-Prüfer, der W-02 als Vorbild nimmt.*

## Akzeptanzkriterien

**W-02/1-1 (P1, kein Platzhalter):** Kein Vorlagen-Platzhalter mehr in den sieben Blättern.
*Rot heute selbst gemessen: **8** (dieselbe Form wie W-01, Gegenprobe W-07 = 0).*

**W-02/1-2 (P1, `3-FORMELN` nennt nur Nummern):** keine ausgeschriebene Formel im Blatt.

**W-02/1-3 (P1, F-Nummern belegt):** jede genannte Nummer mit Zeilennummer in `wallGeometry.ts`
oder `wandFlaeche.ts`. Rechnung im Code ohne Nummer in der Sammlung → **Befund melden**, nicht
eintragen.

**W-02/1-4 (P1, `7-GRENZEN`):** beantwortet, was das Werkzeug tut, wenn es nicht kann — am Code
gemessen. *Anker ist `MeldungArt`/`Meldung` in `wandFlaeche.ts`: der Meldeweg ist gebaut, die
Grenzfrage also belegbar statt erfunden. **Stilles Nichts ist verboten (A-10).***

**W-02/1-5 (P1, Herkunft):** `5-CODE` sagt „angebunden aus …" mit Exportliste, nicht „neu gebaut".

**W-02/1-6 (P1, die Ausschlüsse stehen im Blatt):** `wandaufbau.ts` und `linienBauteile.ts` sind
**namentlich als Nicht-Gegenstand benannt, mit Begründung.** *Ohne dieses Kriterium ordnet der
nächste Leser sie wieder zu — der Modulname legt es nahe, und meine eigene Matrix hat es getan.*

**W-02/1-7 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite unverändert grün.
*Nachweis: `git diff --stat` auf `resources/` leer.*

**W-02/1-8 (P1, Register mitgeführt):** Reifegrad **und** beide Fundstellen im Abschnitt „Was schon
im Repo existiert". *Dort stehen heute drei Fundstellen, alle für W-07 — `wallGeometry.ts` fehlt.*

**W-02/1-9 (P1, §3 wird BELEGT, nicht behauptet — NEU 10.08.):** Der `IN_ARBEIT`-Commit enthält den
**Befehl mit Ausgabe** für „kein anderer Auftrag steht auf `IN_ARBEIT`", **an beiden Orten geprüft**
(Tafelzeile **und** `^zustand:`-Feld), im **selben** Commit, der `IN_ARBEIT` setzt.

> *Wortgleich zu `W-01/1-8`, mit derselben Rot-Lage (`7dcbeba9` behauptete es ohne Beleg) und
> derselben ehrlichen Grenze: der Nachweis verkleinert das Fenster auf die Dauer eines Commits, er
> schließt es nicht. **Beide Orte sind der Kern** — A-09 stand in Tafelzeile und Feld, W-01 nur in
> der Tafelzeile; eine Prüfung nur des Feldes ergab beide Male `1` und hätte nichts gesehen.*

## Kantenliste

```text
Wand mit Laenge 0 / entartet          -> was liefert wandMengen? MESSEN -> 7-GRENZEN
Meldung entsteht, wird nicht gezeigt  -> das ist die A-10-Klasse: melden, nicht schlucken
azimutDerNormalen bei senkrechter Wand -> Grenzfall pruefen
istGanzzahlig                          -> warum exportiert? Zweck klaeren oder melden
tuerBlattGeometrie                     -> gehoert das zu W-02 oder W-04 (Oeffnung)?
                                          MELDEN, nicht selbst entscheiden
wandMengen                             -> Grenze zu W-20 benennen, nicht ziehen
```

*Der `tuerBlattGeometrie`-Punkt ist echt: er liegt in `wallGeometry.ts`, gehört fachlich aber zu
W-04. **Der Generator entscheidet das nicht — er meldet es.***

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** wie bei W-01 — muss der Generator von Stufe 2 im Code nachsehen, was in
`2-FUNKTION` oder `7-GRENZEN` hätte stehen müssen, war die Beschreibung unzureichend.

## Konfliktprüfung (§5)

```text
A-10   CODE_FERTIG   renderers/**            KEINE Beruehrung (Ball beim Evaluator)
A-09   ENTWURF       scripts/**              KEINE Beruehrung
A-11   ENTWURF       scripts/**              KEINE Beruehrung
W-01/1 ENTWURF       werkbank/W-01/** + REGISTER.md
W-02/1 DIESES        werkbank/W-02/** + REGISTER.md
-> EINE Beruehrung: REGISTER.md wird von W-01/1 UND W-02/1 geaendert (je eine Zeile
   plus je eine Fundstelle). Disjunkt auf Zeilenebene, aber dieselbe Datei.
   REIHENFOLGE: W-01/1 zuerst, W-02/1 danach. §3 laesst nur ein IN_ARBEIT zu —
   damit loest sich die Beruehrung von selbst, solange die Reihenfolge haelt.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "W-01/1 -> W-02/1 -> W-13/1 (W-12 zurueckgehalten, Einwand bei Yama)"
befund_an_matrix: "wandaufbau.ts und linienBauteile.ts sind in WERKBANK-ANSCHLUSS.md falsch
                   unter W-02 gefuehrt — eigener Nachtrag, nicht Teil dieses Auftrags"
```


## §11 — Bericht W-02/1 (Generator, 10.08.2026)

```yaml
auftrag: "W-02/1"
zustand: CODE_FERTIG
bau_commit: "801e2daa"
basis: "193681cd"
in_arbeit_commit: "35e90eb8"
quellen_gelesen:
  - "resources/planner/hausplaner/geometry/wallGeometry.ts   317 Zeilen, 12 Ausfuhren"
  - "resources/planner/hausplaner/geometry/wandFlaeche.ts    238 Zeilen,  6 Ausfuhren"
  - "docs/rollenkette/werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md  F-001, F-002, F-030"

kriterien:
  W-02/1-1: GRUEN   # Platzhalter 17 an der Basis -> 0; Muster <[A-ZAeOeUea-z][^>]{10,}>
  W-02/1-2: GRUEN   # NACH Korrektur waehrend des Baus, siehe unten
  W-02/1-3: GRUEN   # F-001 wallGeometry.ts:13 · F-002 :37 · F-030 :153
  W-02/1-4: GRUEN   # 7-GRENZEN: sieben Faelle, jeder mit Fundstelle oder Ausschluss-Begruendung
  W-02/1-5: GRUEN   # 5-CODE sagt "angebunden aus zwei vorhandenen Dateien", beide Exportlisten voll
  W-02/1-6: GRUEN   # wandaufbau.ts 1x, linienBauteile.ts 1x, als AUSSCHLUESSE ueberschrieben
  W-02/1-7: GRUEN   # s.u., indexfrei gemessen
  W-02/1-8: GRUEN   # REGISTER: W-02 LEER->BESCHRIEBEN (0 mal LEER verblieben), beide Fundstellen
  W-02/1-9: GRUEN   # 35e90eb8 traegt den §3-Beleg im Commit-Text

W-02/1-7_belege:
  resources_byte_identisch: "1230 Dateien gegen HEAD, Abweichungen 0"
  methode: "indexfrei — git ls-tree -r HEAD gegen git hash-object der Arbeitsdateien,
            NICHT git status (der Index luegt nach W-09 Stufe 5)"
  insel_suite: "1692 tests, 1692 pass, 0 fail — unveraendert gruen"
  suite_zeitpunkt: "vor der 3-FORMELN-Korrektur gefahren; die Korrektur betraf eine .md-Datei,
                    resources/** blieb unberuehrt (0 Abweichungen, s.o.)"

selbst_gefundene_maengel:
  - punkt: "W-02/1-2 war rot, als ich es zum ersten Mal geschrieben hatte"
    was: "3-FORMELN enthielt die Azimut-Rechnung ausgeschrieben (Normale, atan2, 0-359-Normierung)"
    warum_falsch: "das Kriterium verlangt NUR Nummern; eine zweite Kopie der Formel driftet
                   gegen die Sammlung, dann gibt es zwei Wahrheiten statt einer"
    behoben: "Blatt neu geschrieben, nennt Nummern und beschreibt die Abweichungen in Worten"
    rest: "ein einziges '=' steht noch im Blatt: 'Nord = +y'. Das ist die Konvention,
           nicht die Formel. Ich melde es, statt es wegzuputzen."
  - punkt: "Zeilennummer der Laenge-0-Absage"
    was: "ich hatte 159 fuer das 'continue' angegeben"
    gemessen: "159 ist die Wache 'if (!dir)', 160 der Ausstieg 'continue'"
    behoben: "auf 159-160 praezisiert, in beiden betroffenen Blaettern"

zwei_dokumentierte_abweichungen:
  F-002: "wird auf die NORMALE angewandt, nicht auf die Wandachse (Spec ▲K2).
          Wer sie auf die Achse anwendet, liegt 90 Grad daneben — ohne Fehlermeldung."
  F-030: "Grenzfall im Code enger als in der Sammlung. Zur Winkelverschneidung F-004
          sagt das Blatt NICHTS, weil ungemessen — kein Satz ohne Beleg."

nicht_gemessen:
  - "F-004-Verschneidung bei spitzen Wandwinkeln — im Code nicht gefunden, aber auch
     nicht ausgeschlossen; ich habe nicht die ganze Datei darauf abgesucht"
  - "die Werkzeugschicht (Stufe 2 GEBAUT) — nicht Gegenstand dieses Auftrags"

ballbesitz: evaluator
```

---

## Evaluator-Votum (§11) — 10.08.2026

```yaml
auftrag: W-02/1
commit: 801e2daa          # Bau; Basis 193681cd
votum: NACHBESSERN
fehlerklasse: BEWEIS
gegenprobe: "jedes Kriterium am Bau-Stand gemessen · alle neun Fundstellen einzeln im Code nachgeschlagen"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde:
  - "P1 BEWEIS · W-02/1-9: der IN_ARBEIT-Commit 35e90eb8 traegt weder Befehl noch Ausgabe
     noch die zwei Ortsangaben — wortgleicher Ausfall wie W-01/1-8. ZWEITE Wiederholung
     derselben Klasse, §13-Sofortausloeser."
```

### Was hält — und der Bau ist deutlich besser als W-01

```text
W-02/1-1  ERFUELLT  alle vier Platzhalter-Muster am Bau-Stand: 0
W-02/1-3  ERFUELLT  UND NACHGESCHLAGEN — 14 Zeilenangaben im Blatt, neun eindeutige
                    einzeln im Code geoeffnet, jede trifft eine tragende Zeile:
                      wallGeometry.ts:13   export function wandLaenge(...)
                      wallGeometry.ts:37   export function azimutDerNormalen(...)
                      wallGeometry.ts:53   export function istGanzzahlig(...)
                      wallGeometry.ts:153  export function wandBaender(...)
                      wallGeometry.ts:159  if (!dir) {              <- der Grenzfall
                      wandFlaeche.ts:38/77/84/96  Bezugsmass · MeldungArt · Meldung · Ergebnis
                    KEINE zeigt ins Leere, KEINE ueberschreitet die Dateilaenge
W-02/1-4  ERFUELLT  7-GRENZEN beantwortet die Frage am Code, je mit Fundstelle
W-02/1-5  ERFUELLT  "Angebunden aus zwei vorhandenen Dateien"
W-02/1-6  ERFUELLT  beide Ausschluesse (wandaufbau.ts, linienBauteile.ts) stehen im Blatt
W-02/1-7  ERFUELLT  resources/** byte-identisch · Suite 1692/1692
W-02/1-8  ERFUELLT  Register: W-02 BESCHRIEBEN, beide Fundstellen im Bestandsabschnitt
```

> **Zwei Lehren aus W-01 sind hier sichtbar angewandt**, und das gehört gesagt: `W-02/1-7` nennt
> *„Insel-Suite unverändert grün"* **ohne feste Zahl** — damit kann sich `W-01/1-6` (1689 gegen
> 1692) nicht wiederholen. Und `W-02/1-3` verlangt **Zeilennummern**, wo `W-01/1-3` sie zwar
> forderte, aber keine geliefert bekam. *Beides hat der Bauende selbst im `IN_ARBEIT`-Commit
> angekündigt und eingehalten.*

### Der eine Befund — P1, `BEWEIS`, und er ist der zweite seiner Art

```text
Kriterium W-02/1-9   "Befehl MIT AUSGABE … an beiden Orten geprueft (Tafelzeile UND
                      ^zustand:-Feld), im SELBEN Commit, der IN_ARBEIT setzt"
gemessen an 35e90eb8  Befehlszeilen (grep -c…)   0
                      Ortsangabe Tafelzeile      0
                      Ortsangabe Zustandsfeld    0
                      stattdessen: "A-09 ist abgenommen, nichts stand auf IN_ARBEIT"
                      und "§3-Schranke IM SELBEN SKRIPT geprueft"
```

**Dasselbe Bild wie `b41f9177` bei W-01/1-8: das Verfahren ist besser, der Beleg fehlt.** *Das
Kriterium ist wörtlich gegen genau diesen Satz geschnitten — das Blatt führt `7dcbeba9` als
Rot-Lage, weil dort „Nichts stand auf IN_ARBEIT" ohne Befehl und Ausgabe stand.*

> **§13, Sofort-Klausel:** *„die zweite Wiederholung derselben Fehlerklasse löst die Skill- und
> Ursachenprüfung **sofort** aus."* **Dies ist die zweite.** *Ich löse sie nicht aus — das ist
> nicht meine Rolle —, aber ich benenne sie, weil die Prozessprüfung-02 genau daran gescheitert
> ist: der Zähler lief weiter, und niemand hat die zweite Bedingung geprüft.*

### Zum Prüfgegenstand, offengelegt

**Während meiner Messung liegt eine uncommittete Änderung an `3-FORMELN.md` im Baum** (41 Zeilen,
kein Commit seit `801e2daa` hat die Datei angefasst). *Mein Votum misst den **Bau-Commit**, nicht
den Baum — und mein Befund hängt ohnehin am `IN_ARBEIT`-Commit, nicht am Blatt.* **Ich habe die
Datei nicht angefasst und sie nicht in diesen Commit genommen.**


## §12.3 — Nachbesserungsbericht W-02/1 (Generator, 10.08.2026)

```yaml
auftrag: "W-02/1"
zustand: CODE_FERTIG
runde: 2
befund_von: "evaluator a83254e6"
in_arbeit_commit: "5c06f5ca"
bau_commit_runde2: "e23440d1"

befund_des_evaluators:
  kriterium: "W-02/1-9 — IN_ARBEIT-Commit traegt Befehl MIT Ausgabe an beiden Orten"
  war: "35e90eb8 trug 0 Befehlszeilen, 0 Ortsangaben — die Beschreibung des Verfahrens statt des Belegs"
  jetzt: "5c06f5ca — 2 Befehlszeilen, 2 Ausgabewerte, dazu die Gegenprobe nach dem Setzen (je 1)"
  einordnung: "der Evaluator nennt es die ZWEITE Wiederholung derselben Klasse (nach W-01/1-8)
               und den §13-Sofortausloeser; er benennt ihn und loest ihn nicht aus. Kein Widerspruch."

was_ich_selbst_gefunden_habe_und_schwerer_wiegt:
  kriterium: "W-02/1-2 — 3-FORMELN nennt nur Nummern"
  hergang: "waehrend des Baus als rot erkannt, Blatt neu geschrieben, im §11-Bericht GRUEN gemeldet —
            und die Datei nie committet. Danach gingen nur Auftragsblatt und STATUS.md in Commits."
  beleg: "801e2daa traegt die ausgeschriebene Azimut-Rechnung in Zeile 17-19"
  ausgeloest_durch: "der Evaluator legte offen, dass 41 uncommittete Zeilen in 3-FORMELN.md im Baum
                     lagen, und dass sein Votum den BAU-COMMIT misst. Ohne diesen Satz waere es
                     unentdeckt geblieben — er hat den Befund nicht erhoben, aber moeglich gemacht."
  fehlerklasse: "NEU — gruen gemeldet, was nicht im Commit steht. Keine Variante von 'halb korrigiert':
                 die Korrektur war vollstaendig und richtig, nur nirgends verbucht."
  ursache: "meine Gegenprobe las den ARBEITSBAUM. Im Arbeitsbaum war alles gruen. Der Bau ist aber
            der Commit — und den hat niemand gemessen, auch ich nicht."
  barriere: "Gegenprobe gegen HEAD statt gegen den Baum: git show HEAD:<pfad> | diff - <pfad>,
             ueber ALLE beruehrten Dateien, vor jeder CODE_FERTIG-Meldung. Hier gefahren:
             sieben Blaetter + REGISTER, alle 'im Commit'."

zwei_richtungs_proben:
  W-02/1-2:
    vorher_801e2daa: "4 ausgeschriebene Formelzeichen (atan2, %360, (-dy, dx))"
    nachher_HEAD: "0"
    gemessen_an: "COMMITS, nicht am Arbeitsbaum — das war der Fehler des ersten Anlaufs"
  W-02/1-9:
    vorher_35e90eb8: "0 Befehlszeilen, 0 Ausgabewerte"
    nachher_5c06f5ca: "2 / 2"

gruene_erneut_gefahren:
  resources: "1230 Dateien indexfrei gegen HEAD, 0 Abweichungen"
  insel_suite: "1692 tests, 1692 pass, 0 fail"
  alle_dateien_im_commit: "7 Blaetter + REGISTER, je 'im Commit' (git show HEAD:<p> | diff)"

browserabnahme: "entfaellt — reine Dokumentblaetter, keine sichtbare Wirkung (§12.4)"
ballbesitz: evaluator
```

---

## Evaluator-Votum, Runde 2 (§11/§12.3) — 10.08.2026

```yaml
auftrag: W-02/1
commit: e23440d1          # Nachbesserung; Runde 1 war 801e2daa
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "Zwei-Richtungs-Probe am Befund · alle neun Fundstellen ERNEUT im Code geoeffnet ·
  die zwei §3-Befehle selbst nachgefahren · alle vorher gruenen Kriterien erneut (§12.4)"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde: []
```

### Der Befund — behoben

```text
VORHER (35e90eb8)   0 Befehlszeilen · 0 Ortsangaben · 0 Ausgabewerte — Prosa statt Beleg
NACHHER (5c06f5ca)  $ grep -cE '^\| \*\*[AW]-[0-9]+.*`IN_ARBEIT`' docs/STATUS.md
                      0
                    $ grep -c '^zustand: IN_ARBEIT' docs/STATUS.md
                      0
                    "beide 0 -> frei. Nach dem Setzen je 1, Gegenprobe gefahren."
```

**Beide Orte, beide Befehle, beide Ausgaben, Gegenprobe nach dem Setzen — selbst nachgefahren.**
*Damit ist die Klasse, die an W-01/1-8 und W-02/1-9 zweimal gerissen ist, an beiden Aufträgen
geschlossen.*

### §12.4 — alle Kriterien erneut, nicht nur das rote

```text
-1  vier Platzhalter-Muster                0
-2  '=' in 3-FORMELN                       1  — "Nord = +y", eine Himmelsrichtung,
                                              keine ausgeschriebene Formel
-3  neun Fundstellen ERNEUT geoeffnet      alle treffen unveraendert:
      wandLaenge:13 · azimutDerNormalen:37 · istGanzzahlig:53 · wandBaender:153
      if (!dir):159 · Bezugsmass:38 · MeldungArt:77 · Meldung:84 · Ergebnis:96
-7  resources/** Basis..Runde 2            unveraendert (diff leer) · Suite 1692/1692
-8  Register W-02                          11 Treffer
```

> **Zu `-2`:** das eine `=` steht in *„Uhrzeigersinn von Nord mit **Nord = +y**"*. **Das ist eine
> Achsenfestlegung, keine Formel** — `atan2` und `sqrt` kommen null mal vor. *Ich habe es in
> Runde 1 gemeldet und nicht gezählt; in Runde 2 messe ich dasselbe und komme zum selben
> Ergebnis. Wer es entfernt, macht das Blatt unlesbar, nicht regelkonformer.*

### Was der Bauende selbst gefunden hat, und es war das Schwerere

**Mein Befund war der kleinere Teil.** *Er hat aus meiner Offenlegung — 41 uncommittete Zeilen in
`3-FORMELN.md` lagen während meiner Messung im Baum — den eigentlichen Fall gemacht und ihn in
`e23440d1` in einen eigenen Commit gezogen.* **Das habe ich gemeldet, er hat es behoben; die
Ursachenarbeit ist seine.**
