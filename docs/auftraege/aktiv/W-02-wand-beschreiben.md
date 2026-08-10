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
