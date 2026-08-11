# W-04 Stufe 1 — Öffnung BESCHREIBEN: zwei Kataloge, und die Geometrie gehört W-02

```yaml
auftrag: "W-04/1"
werkzeug: "W-04 Oeffnung Tuer/Fenster"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-04 aus oeffnungsBauarten.ts + oeffnungsTypen.ts ableiten"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: b6078b2a
prioritaet: P1
anlass: "Yamas Ansage 10.08. — Klasse A vollstaendig, sorgfaeltig, behutsam; Runde 1, zweites Blatt"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt. Kein W-04-Blatt lag als Auftrag vor."
muster: "W-01/1, W-02/1, W-08/1"
```

## Ist-Zustand — W-04s Code liegt in DREI Modulen, und keines davon ist „sein" Modul

**Anbindungsmessung, je Modul an den Exporten und am Dateikopf gefahren:**

```text
GEHOERT ZU W-04 — zwei KATALOGE, keine Geometrie
geometry/oeffnungsBauarten.ts             75 Zeilen, 5 Exporte
  OeffnungsBauart · FENSTER_BAUARTEN · TUER_BAUARTEN
  fensterBauartNach() · tuerBauartNach()
  Dateikopf: "Premium-Bauarten (Yamas SVG-Saetze) — Icon-Auswahl im Panel.
              Reine Katalog-Daten; SVGs unter public/hausplaner/icons/"
geometry/oeffnungsTypen.ts                49 Zeilen, 7 Exporte
  TuerTyp · FensterTyp · TypVorlage · TUER_TYPEN · FENSTER_TYPEN
  tuerTyp() · fensterTyp()
  Dateikopf: "Reine Kataloge mit Standardmassen (DIN-nah) fuer die Auswahl BEIM
              Anlegen ... Keine Szene-Mutation, kein Rendern — reine Tabelle + Lookup"
                                          --------------------------------
                                          124 Zeilen, 12 Exporte
Registry-Werkzeuge                        ZWEI: 'fenster' und 'tuer'
Zusagen                                   3 dediziert, 5 erwaehnend

DIE GEOMETRIE LIEGT IN W-02s MODUL
geometry/wallGeometry.ts:267-294
  TuerAnschlag ('links'|'rechts') · TuerOeffnung ('innen'|'aussen')
  TuerBlattGeometrie · tuerBlattGeometrie()

NICHT ZU W-04 — mit einer Ausnahme, die praezise benannt werden muss
geometry/fensterProdukt.ts               153 Zeilen
  AUSGESCHLOSSEN: berechneUw() · rcMachbar() · preisFenster() · PROFIL_KATALOG ·
                  VERGLASUNG_KATALOG · ProfilSystem · Verglasung · UwEingabe ·
                  UwErgebnis · PreisEingabe · PreisErgebnis · RahmenMaterial · RcKlasse
                  -> Bauphysik (U_w), Sicherheit (RC-Klassen) und PREIS.
                     Vierter Fall wie wandaufbau bei W-02.
  ABER: der Typ `OeffnungsArt` liegt dort und wird von oeffnungsBauarten
        IMPORTIERT (Z.3: import type { OeffnungsArt } from './fensterProdukt').
        Er gehoert fachlich zur Oeffnung. -> NICHT pauschal ausschliessen.
```

> ### Der Befund: W-04 hat kein eigenes Modul
>
> ```text
> Kataloge      in zwei eigenen Modulen        124 Z
> Geometrie     in wallGeometry.ts (W-02)       28 Z
> ein Typ       in fensterProdukt.ts (Produkt)   1 Z
> Bauphysik     in fensterProdukt.ts           ausgeschlossen
> ```
>
> **Die Öffnung ist über drei Dateien verteilt, und die Geometrie liegt in der Wand.** *Das ist kein
> Mangel des Codes — eine Türöffnung IST ein Loch in einer Wand, und `tuerBlattGeometrie` braucht die
> Wandstärke. Aber es macht die Zuordnung mehrdeutig, und mehrdeutige Zuordnung erzeugt
> Doppelbeschreibungen.*

## DECISION — ein Code-Ort, ein Blatt: die Türgeometrie bleibt bei W-02

**W-02 hat sie BEREITS beschrieben, selbst nachgemessen:**

```text
W-02/2-FUNKTION.md  Z.28  | 267/268 | TuerAnschlag, TuerOeffnung | links/rechts, innen/aussen |
                    Z.29  | 270     | TuerBlattGeometrie         | Tuerblatt                  |
                    Z.30  | 291     | tuerBlattGeometrie()       | Tuerblatt aus Oeffnung     |
W-02/5-CODE         nennt alle vier in der Exportliste von wallGeometry.ts
```

**Entschieden: W-04 beschreibt sie NICHT, sondern verweist.**

```text
BEGRUENDUNG   Die Blaetter werden AUS DEM CODE abgeleitet, also folgt das Blatt der
              DATEI und nicht dem Fachgebiet. wallGeometry.ts gehoert zu W-02,
              vollstaendig — inklusive der Tuergeometrie darin.
FOLGE         W-04/2-FUNKTION verweist auf W-02 mit Datei:Zeile. Es beschreibt NICHT,
              was tuerBlattGeometrie tut — das steht dort.
WARUM NICHT   Zwei Blaetter, die denselben Code beschreiben, sind zwei Wahrheiten.
UMGEKEHRT     Die Beschreibung nach W-04 zu verschieben hiesse, W-02s fertiges und
              abgenommenes Blatt zu oeffnen — fuer einen Gewinn, der nur in der
              Fachlogik liegt, nicht in der Auffindbarkeit.
```

> *Ich halte das für die richtige, aber nicht die schöne Lösung. **Die schöne wäre, dass die
> Türgeometrie in einem eigenen Modul liegt** — dann fiele die Frage weg. Das ist eine
> Code-Entscheidung und gehört nicht in ein Doku-Blatt; ich melde sie als Beobachtung, ohne einen
> Umbau zu verlangen.*

## Der zweite Befund: ZWEI Registry-Werkzeuge für EIN Blatt

```text
Registry 'fenster'   label "Fenster"
Registry 'tuer'      label "Tuer"
Werkbank W-04        "Oeffnung (Tuer/Fenster)" — EIN Eintrag
```

**Das Blatt muss beide bedienen.** `4-BEDIENUNG` beschreibt zwei Werkzeuge, die dieselben Kataloge
und dieselbe Geometrie benutzen und sich in den Vorlagen unterscheiden (`TUER_TYPEN` gegen
`FENSTER_TYPEN`, `TUER_BAUARTEN` gegen `FENSTER_BAUARTEN`). *Das ist sauber gebaut — die Trennung
liegt in den Daten, nicht im Code.*

## Nicht-Ziele

- **Keine Türgeometrie beschreiben.** Sie steht in W-02 (siehe DECISION).
- **Keine Bauphysik, keine Preise.** `berechneUw`, `rcMachbar`, `preisFenster` gehören nicht hierher.
- **Kein pauschaler Ausschluss von `fensterProdukt.ts`** — der Typ `OeffnungsArt` wird gebraucht und
  ist zu nennen. *Ein Ausschluss, der eine benutzte Abhängigkeit mitverbietet, macht das Blatt falsch.*
- **Keine Registry-Einträge.** `fenster` und `tuer` existieren.
- **Keine Aussage über Dachöffnungen.** `dachOeffnung.ts` gehört zu W-07.
- **Keine Änderung an den drei Modulen** und keiner ihrer Zusagen.

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-04-oeffnung-tuer-fenster/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-04 LEER -> BESCHRIEBEN
                                                     + beide Katalog-Module als Fundstelle
```

*NICHT im Scope: `resources/**`, W-02s Blätter, die F-Liste des Registers (N1-Frage).*

## Wiederverwendungsprüfung (§5)

```text
oeffnungsBauarten.ts + oeffnungsTypen.ts   VORHANDEN, 124 Z — Quelle, unangetastet
beide Dateikoepfe                          nennen ihren Zweck selbst ("reine Kataloge",
                                           "Icon-Auswahl im Panel") — Quelle fuer 1-ZWECK
Registry 'fenster' + 'tuer'                VORHANDEN — Quelle fuer 4-BEDIENUNG
3 dedizierte Zusagen                       VORHANDEN — Quelle fuer 6-PRUEFUNG
W-02s Blaetter                             Quelle fuer den VERWEIS auf die Tuergeometrie
public/hausplaner/icons/                   die SVG-Saetze, auf die der Katalog zeigt
W-01/1, W-02/1, W-08/1                     Muster
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite unberuehrt
```

**Erstnutzer:** *der Generator von W-04 Stufe 2 — und jede Rolle, die eine Öffnung anlegt: sie muss
wissen, dass die Maße aus einem Katalog kommen und **nach dem Setzen frei überschreibbar** sind
(Dateikopf `oeffnungsTypen.ts`).*

## Akzeptanzkriterien

**W-04/1-1 (P1, kein Platzhalter):** keiner mehr in den sieben Blättern. *Zählweise: alle
`<…>`-Klammern.*

**W-04/1-2 (P1):** `3-FORMELN` nennt nur F-Nummern. *Erwartung: **wenig oder keine** — beide Module
sind Kataloge und Lookups ohne Rechnung. **Findet der Generator keine Formel, ist „keine" die
richtige Antwort** und keine Lücke; das Blatt sagt es dann ausdrücklich.*

**W-04/1-3 (P1, der Verweis statt der Doppelbeschreibung):** `2-FUNKTION` beschreibt die
Türgeometrie **nicht**, sondern verweist auf W-02 mit `Datei:Zeile`. *Rot heute: W-04s Blätter sind
Vorlage; die Doppelbeschreibung ist der Fehler, der ohne dieses Kriterium entsteht.*

**W-04/1-4 (P1, `7-GRENZEN`):** beantwortet, was passiert, wenn ein Typ oder eine Bauart **nicht im
Katalog** steht — am Code gemessen (`tuerTyp()`, `fensterTyp()`, `fensterBauartNach()`,
`tuerBauartNach()`: was liefern sie bei unbekannter ID?). *Stilles `undefined` ist die A-10-Klasse.*

**W-04/1-5 (P1, Herkunft):** `5-CODE` sagt „angebunden aus …" mit beiden Modulen und Exportliste.

**W-04/1-6 (P1, der Ausschluss ist PRÄZISE):** `fensterProdukt.ts` ist als Nicht-Gegenstand benannt
— **mit der Ausnahme `OeffnungsArt`**, die von `oeffnungsBauarten` importiert wird. *Ein pauschaler
Ausschluss wäre falsch und würde beim Bau auffallen.*

**W-04/1-7 (P1, beide Werkzeuge):** `4-BEDIENUNG` beschreibt **`fenster` und `tuer`** und benennt,
dass die Trennung in den Daten liegt (`TUER_TYPEN` gegen `FENSTER_TYPEN`), nicht im Code.

**W-04/1-8 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite unverändert grün.

**W-04/1-9 (P1, Register mitgeführt):** Reifegrad **und** beide Katalog-Module als Fundstelle.

**W-04/1-10 (P1, §3 wird BELEGT):** Befehl mit Ausgabe für „kein anderer Auftrag auf `IN_ARBEIT`",
an beiden Orten, im selben Commit. **Zählbare Form (E2 aus Prüfung 03): mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer.** *Wortgleich zu `W-01/1-8`.*

## Kantenliste

```text
unbekannte Typ-/Bauart-ID              -> was liefern die Lookups? MESSEN (W-04/1-4)
Bauart ohne `oeffnungsArt`             -> Dateikopf: "sonst undefined (Nutzer waehlt)" —
                                          das ist ABSICHT, nicht Luecke; benennen
Masse nach dem Setzen geaendert        -> Katalog ist nur die VORLAGE (Dateikopf).
                                          Das Blatt darf nicht behaupten, sie seien fest.
SVG fehlt zur Bauart-ID                -> id = Dateiname ohne Endung; fehlende Datei
                                          faellt erst im Panel auf. GRENZE benennen.
Tuer im Fenster-Katalog gesucht        -> getrennte Tabellen, kein Fallback
Dachfenster                            -> dachOeffnung.ts, gehoert zu W-07
```

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** Muss der Generator von Stufe 2 für die Türgeometrie in W-02 nachsehen und findet
dort **nicht**, was er braucht, war der Verweis zu dünn — dann zurück an den Planner. *Das ist der
Preis der Verweis-Lösung, und er ist bewusst gewählt.*

## Konfliktprüfung (§5)

```text
W-08/1   ENTWURF    werkbank/W-08/** + REGISTER.md
W-13/1   ENTWURF    werkbank/W-13/** + REGISTER.md
W-04/1   DIESES     werkbank/W-04/** + REGISTER.md
W-02/1   BESCHRIEBEN (fertig)   — wird nur GELESEN, nicht geaendert
A-12     ENTWURF    FORMELSAMMLUNG + VORGEHEN     KEINE Beruehrung
-> DREI Blaetter teilen REGISTER.md, je eine Zeile plus Fundstellen, zeilenweise disjunkt.
   §3 loest es; belegt wird das in W-04/1-10.
FACHLICHE Beruehrung mit W-02: dessen wallGeometry.ts traegt die Tuergeometrie.
   Kein Dateikonflikt — W-04/1 aendert W-02s Blaetter NICHT, es verweist darauf.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "Runde 1: W-08 -> W-04 -> W-11"
befund_an_matrix: "W-04 hat kein eigenes Geometrie-Modul — Kataloge eigen, Geometrie in
                   wallGeometry (W-02), ein Typ in fensterProdukt. Gehoert in den
                   naechsten Matrix-Nachtrag."
beobachtung_ohne_forderung: "eine eigene Datei fuer die Tuergeometrie wuerde die
                             Zuordnungsfrage aufloesen — Code-Entscheidung, nicht meine"
```


## §11 — Bericht W-04/1 (Generator, 11.08.2026)

```yaml
auftrag: "W-04/1"
zustand: CODE_FERTIG
bau_commit: "a44e5fdd"
in_arbeit_commit: "a9e58dd4"
basis: "b6078b2a"

kriterien:
  W-04/1-1:  GRUEN   # 22 -> 0, Zaehlweise des Auftrags: ALLE <…>-Klammern
  W-04/1-2:  GRUEN   # "keine" ist die Antwort — Math. null mal in BEIDEN Modulen, gemessen
  W-04/1-3:  GRUEN   # 2-FUNKTION verweist auf W-02, vier Datei:Zeile-Angaben, beschreibt nicht
  W-04/1-4:  GRUEN   # alle vier Lookups am Code gemessen, Rueckfallwerte selbst nachgezaehlt
  W-04/1-5:  GRUEN   # "Angebunden aus zwei vorhandenen Modulen", beide Exportlisten voll
  W-04/1-6:  GRUEN   # Ausschluss MIT der Ausnahme OeffnungsArt, als import type belegt
  W-04/1-7:  GRUEN   # beide Werkzeuge, Trennung liegt in den Daten (Registry-Zeilen genannt)
  W-04/1-8:  GRUEN   # drei Richtungen, s. u.
  W-04/1-9:  GRUEN   # Register: BESCHRIEBEN + beide Katalog-Module
  W-04/1-10: GRUEN   # a9e58dd4: 2 Befehlszeilen, 2 Ausgabewerte, je Ort einer (E2)

W-04/1-8_drei_richtungen:   # Auflage 239a163e — einzeln ausgewiesen
  geaendert:    "0 von 1230 verfolgten Dateien (ls-tree HEAD gegen hash-object)"
  hinzugefuegt: "0 (git ls-files --others --exclude-standard -- resources/)"
  entfernt:     "0 (in HEAD, nicht mehr im Baum)"
  insel_suite:  "1692 tests, 1692 pass, 0 fail — unveraendert gruen"
  hinweis: "die Einweg-Messung haette gestern 0 gesagt und gelogen; heute ist auch die
            fremde A-12-Wegwerf-Probe weg, gemessen statt angenommen"

E1_commit_messung:
  gefahren: JA
  ergebnis: "7 Blaetter + REGISTER, je 'im Commit' (git show HEAD:<p> | diff - <p>)"

der_fachliche_kern:
  was: "die vier Lookups antworten GEGENSAETZLICH auf eine unbekannte ID"
  undefined: "fensterBauartNach (Z.70), tuerBauartNach (Z.73)"
  nie_undefined: "tuerTyp (Z.42) -> TUER_TYPEN[0] = dreh1 · fensterTyp (Z.47) -> FENSTER_TYPEN[0] = drehkipp"
  selbst_nachgezaehlt: "Z.23 dreh1, Z.32 drehkipp — der Kommentar behauptet es, und diesmal stimmt er"
  warum_beides_richtig: "Bauart ist Aussehen (fehlt sie, gibt es nichts zu zeichnen);
                         Typ traegt die Masse (eine Oeffnung ohne Mass ist keine)"
  gefahr_benannt: "tuerTyp('gibtsnicht') liefert 875x2010 OHNE zu sagen dass gefallen wurde —
                   A-10-Klasse. Der TS-Typ verhindert es im Uebersetzer, nicht bei Daten aus
                   Datei, Netz oder Altstand."

befund_nicht_still_behoben:
  - was: "das REGISTER schreibt W-04 die Formeln F-003 und F-031 zu"
    gemessen: "beide Module enthalten KEINE Rechnung — Math. null mal, nur find() und ??"
    getan: "Reifegrad und Fundstellen nachgetragen, F-Spalte UNVERAENDERT, Fussnote im Register"
    warum: "die Zuordnung gehoert dem Planner; er hat selbst gemeldet, dass 3 von 4 geprueften
            Matrix-Zeilen falsch waren"

eigener_messfehler_waehrend_des_baus:
  was: "erste Zaehlung ergab TUER_TYPEN = 12 Eintraege"
  erkannt_an: "der Typ TuerTyp erlaubt nur FUENF Werte — 12 kann nicht stimmen"
  ursache: "meine sed-Bereichsgrenze /^\];/ griff nicht, der Bereich lief in FENSTER_TYPEN hinein
            und zaehlte dessen 7 mit (5+7=12)"
  behoben: "an festen Zeilenbereichen nachgezaehlt: TUER 5, FENSTER 7 — deckungsgleich mit den
            Typunionen. Vor dem Schreiben gefunden, nicht danach."

nicht_gemessen:
  - "ob die 48 SVG-Dateien unter public/hausplaner/icons/ wirklich existieren — als Pruefpunkt
     in 6-PRUEFUNG benannt, nicht als Zusage im Blatt behauptet"
  - "die Werkzeugschicht (Stufe 2 GEBAUT) — nicht Gegenstand dieses Auftrags"

browserabnahme: "entfaellt — reine Dokumentblaetter, keine sichtbare Wirkung"
ballbesitz: evaluator
```
