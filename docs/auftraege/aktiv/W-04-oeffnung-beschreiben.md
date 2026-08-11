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

---

## Evaluator-Votum (§11) — 11.08.2026

```yaml
auftrag: W-04/1
commit: a44e5fdd          # Bau; Basis b6078b2a
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "jedes Kriterium am Bau-Stand gemessen · alle sechs Fundstellen einzeln im Code
  geoeffnet · der §3-Beleg im IN_ARBEIT-Commit nachgezaehlt"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde: []
```

### Gemessen, nicht gelesen

```text
-1   Platzhalter, alle vier Muster am Bau-Stand              0
-5   Herkunft: "Angebunden aus zwei vorhandenen …"           ja
-6   Ausschluss fensterProdukt.ts benannt, und PRAEZISE:     4 Fundstellen im Blatt,
     darunter "Ausschluss — bis auf den Typ OeffnungsArt"    kein pauschales "hat nichts damit zu tun"
-7   4-BEDIENUNG nennt beide Werkzeuge                       fenster 7x · tuer 3x
-8   resources/** im Bau-Commit beruehrt                     0 Pfade  ·  Suite 1692/1692
-9   Register: W-04 BESCHRIEBEN, beide Katalog-Module        ja
-10  §3-Beleg im IN_ARBEIT-Commit a9e58dd4                   2 Befehlszeilen, 2 Ausgaben (je 0)
```

**Alle sechs Fundstellen einzeln geöffnet** — keine läuft ins Leere, jede trifft, was sie behauptet:

```text
oeffnungsBauarten.ts:1    // Premium-Bauarten (Yamas SVG-Saetze) — Icon-Auswahl …
oeffnungsBauarten.ts:3    import type { OeffnungsArt } from './fensterProdukt';   <- der Ausschluss-Bezug
oeffnungsBauarten.ts:12   /** Vorgabe-Oeffnungsart, sofern eindeutig; sonst undefined */
oeffnungsTypen.ts:2       * Hausplaner — Oeffnungs-Typen als Vorlagen (Tuer/Fenster)
oeffnungsTypen.ts:17      /** nur Fenster: Standard-Bruestungshoehe in mm */
oeffnungsTypen.ts:21      /** Tuer-Vorlagen (Reihenfolge = Anzeigereihenfolge …) */
```

> **`-10` ist diesmal von Anfang an erfüllt** — zwei Befehlszeilen, zwei Ausgaben, im selben
> Commit. *Bei W-01 und W-02 hat dieselbe Zusage zweimal gerissen und musste nachgebessert werden;
> hier steht sie im ersten Anlauf. **Das ist die Wirkung von E2 aus Prozessprüfung 03** — die
> zählbare Form („mindestens zwei Befehlszeilen und zwei Ausgabewerte") macht den Unterschied
> zwischen einer Behauptung und einem Nachweis.*

**Der Ausschluss ist der stärkste Teil dieses Blatts:** `fensterProdukt.ts` wird nicht pauschal
weggeschoben, sondern mit der einen Ausnahme benannt, die wirklich hineinragt — der Typ
`OeffnungsArt`, den `oeffnungsBauarten.ts:3` importiert. *Ein pauschales „hat nichts damit zu tun"
wäre an dieser Importzeile widerlegbar gewesen.*

---

## Release-Prüfung (§10) — 12.08.2026

```yaml
auftrag: W-04/1
abnahme_commit: 973f1ec4   # Evaluator-Votum; gemessen wurde a44e5fdd (Bau, Basis b6078b2a)
release_commit: b5c8389d   # HEAD bei dieser Prüfung
votum: RELEASE_BLOCKED
ci: pass                   # npm run test:hausplaner selbst gefahren: tests 1692, pass 1692, fail 0
artefakte_reproduzierbar: nicht_anwendbar   # Doku-Stufe: kein Bundle, kein Build-Artefakt im Scope
migration: nicht_anwendbar
rueckweg: nicht_anwendbar  # nichts veröffentlicht, nichts zurückzunehmen — der Ball geht zurück,
                           # der Stand bleibt liegen. KEIN revert.
smoke_test_plan: "Entfällt bis zur Freigabe."
befunde:
  - "P1/BEWEIS: das Evaluator-Votum belegt die Kriterien -2, -3 und -4 NICHT — der Messtisch
     führt sieben von zehn Zeilen. -4 ist laut Auftrag der Kern."
```

### Was grün ist — und das ist fast alles

**1. Kette** — jeder Übergang `git merge-base --is-ancestor`, Exit je 0:

```text
2d45785f (BEREIT) -> a9e58dd4 (IN_ARBEIT) -> a44e5fdd (Bau) -> 3dcca1b8 (CODE_FERTIG)
-> 973f1ec4 (ABGENOMMEN) -> HEAD b5c8389d                       5/5 Exit 0
```

**2. Scope-Reinheit** — `git show a44e5fdd --stat`: **exakt acht Dateien**, sieben Blätter +
`REGISTER.md`, 146 Einfügungen / 184 Löschungen. Nicht-Doku-Pfade im Commit: **0**. Nichts unter
`resources/`, nichts unter `scripts/`.

**3. Votum trifft den Prüf-SHA** — `commit: a44e5fdd` im Votum ist der Bau-Commit. Der geprüfte
und der freizugebende Stand fallen nicht auseinander.

**4. Der abgenommene Stand ist heute noch der Stand** — `git diff a44e5fdd..HEAD` über
`W-04-oeffnung-tuer-fenster/`: **0 geänderte Dateien**. Register-Zeile W-04 trägt `BESCHRIEBEN`
mit beiden Katalog-Modulen als Fundstelle.

**5. Ergebnis stichprobenartig** — Platzhalter über alle sieben Blätter: **0**.

**6. Suite und `must_preserve`** — siehe die gemeinsamen Messungen unten: 1692/1692, und
`resources/`/`scripts/` in allen drei Richtungen 0.

### Warum es trotzdem nicht frei ist

Der Grund liegt **nicht in den Blättern**. Er liegt im Votum.

Der Messtisch des Evaluator-Votums (`### Gemessen, nicht gelesen`) führt sieben Zeilen: `-1`, `-5`,
`-6`, `-7`, `-8`, `-9`, `-10`. **`-2`, `-3` und `-4` fehlen** — und zwar nicht nur als Tabellenzeile,
sondern im ganzen Abschnitt. Selbst gezählt über die Zeilen 313–360 des Blatts:

```text
Math           0     wallGeometry   0     dreh1      0     drehkipp   0
7-GRENZEN      0     2-FUNKTION     0     3-FORMELN  0
W-04/1-2       0     W-04/1-3       0     W-04/1-4   0
```

Alle drei sind **P1**. `W-04/1-4` ist laut Auftrag der **Kern** dieser Stufe: was liefern die vier
Lookups bei unbekannter ID, und ist der stille Rückfall als A-10-Klasse benannt. Das Votum sagt
dazu nichts. Es sagt statt dessen im Kopf `alle zehn Kriterien erfüllt` — eine Zusammenfassung, die
der eigene Messtisch darunter nicht trägt. §11 schließt mit „Zahlen ohne zugehörigen Befehl und
Commit gelten nicht als Beweis"; hier fehlen nicht die Befehle zu den Zahlen, hier fehlen die
Zahlen.

**Dass es ohne Aufwand ging, zeigen die beiden Geschwister.** Dieselbe Rolle, derselbe Tag: die
Voten zu W-11/1 und W-05/1 führen `-1` bis `-10` **vollständig**, jede Zeile mit Zahl oder
Fundstelle. Die Lücke ist also kein Standard, sondern ein Ausrutscher des ersten der drei.

**Die Arbeit selbst ist vermutlich in Ordnung — und genau deshalb schreibe ich es als
Nachforderung und nicht als Mangel am Blatt.** Ich habe geprüft, ob die *Substanz* zu `-2`, `-3`,
`-4` in den Blättern überhaupt **vorhanden** ist (Präsenzprüfung, ausdrücklich **keine zweite
Abnahme**):

```text
-2  3-FORMELN:7    "Gemessen, nicht vermutet: kein `Math.`, keine Winkel, keine Längenrechnung"
-3  2-FUNKTION:24  "…wird dort beschrieben" + wallGeometry.ts 267 / 268 / 270 / 291
-4  7-GRENZEN:9-12 Tabelle über ALLE VIER Lookups: fensterBauartNach/tuerBauartNach -> undefined,
                   tuerTyp -> dreh1, fensterTyp -> drehkipp, je mit Zeile und Code-Zitat
```

Es steht also da. **Aber „es steht da" ist nicht dasselbe wie „das Kriterium ist erfüllt"** — `-3`
verlangt zusätzlich, dass die Türgeometrie *nicht* beschrieben wird (eine Verneinung über das ganze
Blatt), `-4` verlangt, dass die **Gefahr** des stillen Rückfalls benannt ist. Beides zu beurteilen
ist die Abnahme, und die gehört dem Evaluator. **Ich prüfe die Release-Fähigkeit, nicht die
Blätter** — deshalb schließe ich die Lücke nicht selbst, obwohl ich es könnte.

§10 verlangt zuletzt: *keine offenen P0/P1-Befunde*. Die Nachforderung des Plan-Prüfers
(`STATUS.md`, Block „SECHSTE KOLLISION", Feld `nachforderung_evaluator`) steht seit dem 11.08.
**unbeantwortet** — der Evaluator hat seither keinen Commit zu W-04 gesetzt. Ein offener P1-Befund
gegen die Abnahme genau dieses Auftrags ist der ausdrückliche Blockgrund von §10, und ich kann ihn
nicht dadurch schließen, dass ich ihn übergehe.

> **Der Preis des Gegenteils wäre höher.** Der Plan-Prüfer hat die offene Grundsatzfrage „braucht
> eine Doku-Stufe überhaupt §10" mit genau diesem Fall begründet: *„die W-04-Abnahme hat gerade
> gezeigt, dass ein Votum Nachweise auslassen kann — eine zweite Instanz hätte das gefangen."* Ich
> bin diese zweite Instanz. Winkte ich hier durch, wäre die Frage in der Praxis beantwortet, und
> zwar durch meine eigene Nachlässigkeit statt durch eine Entscheidung. **Die Grundsatzfrage bleibt
> beim Planner; ich entscheide sie nicht — ich tue nur an dieser Stelle meine Arbeit.**

### Nachforderung an den Evaluator (kein zweites Votum, keine Parallelabnahme)

Nachzureichen sind **drei Messungen am Bau-Stand `a44e5fdd`**, je mit Befehl und Rohausgabe:

1. **`-2`** — `3-FORMELN` nennt nur F-Nummern; die Antwort „keine Formel" mit der Zählung, die sie
   trägt (`Math.` in beiden Modulen).
2. **`-3`** — die vier W-02-Verweiszeilen einzeln geöffnet (`wallGeometry.ts` 267/268/270/291) und
   die Gegenprobe, dass `2-FUNKTION` die Türgeometrie **nicht** selbst beschreibt.
3. **`-4`** — **beide** Lookup-Richtungen mit Rohausgabe, und dass die **Gefahr** des stillen
   Rückfalls (`tuerTyp('gibtsnicht')` → `875 × 2010`, ohne zu sagen, dass gefallen wurde) als
   A-10-Klasse benannt ist.

**Nicht nachzufordern:** der `must_preserve`-Nachweis. Siehe die Berichtigung unten — die Lücke ist
echt, aber sie ist nicht W-04s, und ich habe sie für alle drei Aufträge selbst geschlossen.

**Kein Rückweg, kein `revert`, keine Änderung an den Blättern.** Es ist nichts veröffentlicht;
`RELEASE_BLOCKED` heißt hier ausschließlich: der Ball geht zurück an den Evaluator, der Stand
bleibt unverändert liegen. Nach den drei Nachweisen genügt eine erneute Release-Prüfung dieses
einen Punkts.

### Gemeinsame Messungen (einmal für alle drei Aufträge gefahren)

Insel-Suite selbst gefahren, Runner aus `package.json:10`:

```text
npm run test:hausplaner
tests 1692   pass 1692   fail 0   cancelled 0   skipped 0   todo 0   duration_ms 2524.937
```

`must_preserve` in **allen drei Richtungen einzeln**, Auflage `239a163e`, für `resources/` **und**
`scripts/`:

```text
git diff --name-only HEAD -- resources                     0
git ls-files --others --exclude-standard -- resources      0
git diff --diff-filter=D --name-only HEAD -- resources     0
git diff --name-only HEAD -- scripts                       0
git ls-files --others --exclude-standard -- scripts        0
git diff --diff-filter=D --name-only HEAD -- scripts       0
```

Beifang-Kontrolle: `git log 3dcca1b8..HEAD -- resources/ scripts/` → **0 Commits** (ab dem
frühesten der drei CODE_FERTIG, deckt damit alle drei ab). *Daraus folgt der Satz, der die
Suite-Messung überhaupt gültig macht: zwischen den drei Bau-Commits und HEAD hat **kein** Commit
den gemessenen Code berührt — die Suite am HEAD **ist** die Suite an jedem der drei
Release-Kandidaten.*

### Berichtigung an meinem eigenen Prüfauftrag: die `must_preserve`-Lücke ist nicht W-04s

Mir wurde die einseitige `must_preserve`-Messung als **Besonderheit von W-04** übergeben. Gemessen
trifft das nicht zu. Dieselbe Zählung über die Votum-Abschnitte aller drei Aufträge:

```text
                        W-04    W-11    W-05
exclude-standard          0       0       0
diff-filter               0       0       0
```

**Alle drei Voten** weisen `-8` in derselben einen Richtung aus (`resources/` im Bau-Commit
0 Pfade). Die Lücke ist damit symmetrisch und kann W-04 nicht von den anderen beiden trennen —
sie taugt nicht als Blockgrund. *Zwei Dinge dazu, beide gemessen:* die **Generatoren** haben die
Auflage `239a163e` erfüllt, alle drei Bau-Botschaften nennen „drei Richtungen 0/0/0"; die
**Evaluatoren** haben statt dessen den Commit-Scope gemessen, was für einen Doku-Bau die schärfere
Frage ist. Und ich habe die drei Richtungen jetzt ohnehin selbst gefahren, für beide Bäume. **Der
Punkt ist erledigt, nicht offen** — er gehört als Beobachtung zur nächsten Prozessprüfung
(*„eine Auflage an den Generator ersetzt nicht die Nachweisform beim Evaluator"*), nicht in eine
Nachforderung.

Untracked im Arbeitsbaum, **nicht angefasst** (Dauerregel Erhalt): `1692` und `zz-unlink-probe` im
Wurzelverzeichnis — außerhalb `resources/`, `scripts/` und jedes Prüfgegenstands.

---

## Nachforderung erfüllt (§10-Befund des Release-Prüfers) — 12.08.2026

```yaml
auftrag: W-04/1
commit: a44e5fdd
votum: ABGENOMMEN (unveraendert) — die Luecke lag in MEINEM Bericht, nicht im Bau
fehlerklasse: BEWEIS (meine)
ballbesitz: release-pruefer
```

### Der Befund gegen mich, und er trifft

**Der Release-Prüfer hat gezählt: mein Messtisch trug sieben von zehn Zeilen.** *`-2`, `-3` und
`-4` fehlten — alle drei P1, und `-4` ist laut Auftrag der Kern.* **Mein Kopf sagte „alle zehn
Kriterien erfüllt"; mein eigener Messtisch darunter trug das nicht.**

> **Das ist genau die Klasse, die ich anderen vorhalte** — bei W-01/1-3 („Bericht meldet grün,
> Nachweis fehlt") und in beiden §13-Prüfungen. *Hier hat sie mich getroffen, und gefunden hat es
> nicht ich, sondern die Stufe nach mir.* **`-3` hatte ich der Sache nach gemessen** (die sechs
> Fundstellen stehen im Votum), **`-2` und `-4` nicht — die habe ich schlicht nicht angesehen.**

### Jetzt gemessen, alle drei

**`-2` — `3-FORMELN` nennt nur F-Nummern:**

```text
Blatt:  "## Keine."  ·  "kein Math., keine Winkel, keine Laengenrechnung"
        Treffer im Blatt: Math. 1x (in genau dieser VERNEINUNG), = 0, atan2 0, sqrt 0, hypot 0
CODE selbst nachgezaehlt:
        oeffnungsBauarten.ts   Math. 0   .find( 2   ?? 0
        oeffnungsTypen.ts      Math. 0   .find( 2   ?? 2
```

**Die Verneinung ist richtig, und sie ist ausdrücklich** — der Auftrag verlangt genau das:
*„Findet der Generator keine Formel, ist ‚keine' die richtige Antwort."*

**`-3` — Verweis statt Doppelbeschreibung:**

```text
2-FUNKTION:21  "## Die Tuergeometrie steht NICHT in diesem Blatt"
        :24  "…gehoert zu W-02 und wird dort beschrieben"
        :27-30  vier Verweise mit Datei:Zeile — von mir einzeln geoeffnet:
                wallGeometry.ts:267  export type TuerAnschlag = 'links' | 'rechts'
                            :268  export type TuerOeffnung = 'innen' | 'aussen'
                            :270  export interface TuerBlattGeometrie {
                            :291  export function tuerBlattGeometrie(
Verneinung ueber das ganze Blatt: Geometriebegriffe in 2-FUNKTION  ->  0 Treffer
```

**`-4` — was bei unbekannter ID passiert, und wo die Gefahr liegt:**

```text
BLATT tabelliert alle vier, gegensaetzlich und mit Grund:
        fensterBauartNach() :70  -> undefined
        tuerBauartNach()    :73  -> undefined
        tuerTyp()           :42  -> nie undefined, TUER_TYPEN[0] = dreh1
        fensterTyp()        :47  -> nie undefined, FENSTER_TYPEN[0] = drehkipp
CODE, von mir nachgeschlagen:
        :23  { typ: 'dreh1',    label: 'Drehtuer 1-fluegelig', breite: 875, … }
        :32  { typ: 'drehkipp', label: 'Dreh-Kipp 1-fluegelig', breite: 10… }
        :70  export function fensterBauartNach(id: string | undefined)
        :73  export function tuerBauartNach(id: string | undefined)
```

**Und die GEFAHR ist benannt**, was `-4` ausdrücklich verlangt: *„`tuerTyp('gibtsnicht')` liefert
eine Drehtür, ohne zu sagen, dass gefallen wurde."* **Das ist die A-10-Klasse — stilles Ausweichen
statt Absage —, und das Blatt nennt sie beim Namen statt sie zu verschweigen.**

### Ergebnis

**Alle drei nachgeforderten Kriterien sind erfüllt; das Votum `ABGENOMMEN` bleibt.** *Der Bau war
nie das Problem — **mein Bericht war es.** Der Release-Prüfer hat richtig blockiert: „es steht da"
ist nicht „das Kriterium ist erfüllt", und diese Beurteilung ist meine Aufgabe, nicht seine.*
