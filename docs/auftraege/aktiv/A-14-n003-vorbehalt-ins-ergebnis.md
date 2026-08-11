# A-14 — der N-003-Vorbehalt gehört ins Ergebnis. Und die Plakette sagt heute „Alle Prüfungen bestanden"

```yaml
auftrag: "A-14"
titel: "Vorbehalt als Pflichtfeld · grundlage traegt die Grenze · die Plakette hoert auf, einen Nachweis zu behaupten"
art: "BAU, Spur A — Fachrecht/Haftung beruehrt, aber KEINE Fachentscheidung: der Vorbehalt ist von Yama festgelegt"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 1e09280d
prioritaet: P1
anlass: "Yamas Auflage 12.08. Abschnitt 1 — mein Vorschlag ('ins Ergebnis, nicht in die Anzeige')
         ersetzt seine Ortsaufzaehlung. Dazu seine zwei Verschaerfungen 1.1a und 1.1b."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "N-003 (FORMELSAMMLUNG, Dauergelb, Geltungsbereich von Yama festgelegt)"
```

## Der Fund, der diesen Auftrag schärfer macht als die Auflage

**Beim Messen der Ausgabestelle ist etwas aufgetaucht, das weder in Yamas Auflage noch in meinem
Vorschlag stand:**

```text
sparrenBerechnung.ts:80    bestanden: boolean;   // beide Nachweise <= 1,0
                                                    ^^^^^^^^^^ das Wort steht im Code
EngineFlaeche.tsx:136-144  die Plakette:
   bestanden === true   ->  "✓ Alle Pruefungen bestanden"    (gruener Hintergrund)
   bestanden === false  ->  "✕ Eine Pruefung ist nicht bestanden"
```

> **Heute zeigt die Sparren-Vorbemessung „✓ Alle Prüfungen bestanden" — grün hinterlegt, fett, als
> Gesamturteil über der Zahlenliste.** *Für eine Rechnung, die Wind, Mehrfeld, Knicken,
> Auflagerpressung und Lastkombinationen **nicht kennt**.* **Das ist die Nachweissprache, die Yamas
> Auflage verbietet, nur an einer Stelle, die keiner von uns beiden genannt hatte — und sie ist
> lauter als jede Ausnutzungszahl.**

**Und der Code hat den Präzedenzfall bereits, richtig gedacht (`EngineFlaeche.tsx:131-135`):**

> *„AUF-52 Scheibe C: **Die Plakette nur, wenn die Engine ein Bestehens-Merkmal liefert.**
> `berechneUw` und `pvSchnellBelegung` rechnen Werte aus — **sie bestehen nichts**. Eine Plakette
> ‚nicht bestanden' wäre dort eine **erfundene Bewertung**; die Hülle zeigt, was da ist, und wo nichts
> ist, steht nichts."*

**Jemand hat diese Frage schon entschieden — und die Antwort trifft N-003 genauso.** *Eine
Vorbemessung **besteht nichts**. Sie sagt, ob eine Ausnutzung unter 1,0 liegt; das ist eine Zahl, kein
Urteil über Standsicherheit.*

## DECISION — die Form, in vier Teilen. Drei nutzen vorhandene Mechanismen

**Yamas Bedingung bei 1.1b:** *„eine Ausnutzungszahl aus N-003 darf nirgends erscheinen, ohne dass im
selben Blick steht, dass sie kein Nachweis ist … die Form entscheidest du."*

```text
TEIL 1  PFLICHTFELD IM ERGEBNIS                                        (Yamas Abschnitt 1)
        SparrenErgebnis bekommt ein Feld mit dem Vorbehaltssatz.
        WARUM ES DEN CAST UEBERLEBT, gemessen: EngineErgebnis (enginePanels.ts:78)
        traegt "[feld: string]: unknown" — ein zusaetzliches Feld geht durch
        "as unknown as EngineErgebnis" NICHT verloren.

TEIL 2  DIE PLAKETTE FAELLT — ERSATZLOS                    (Yamas Entscheidung 12.08.)
        MEINE erste Fassung wollte den Plakettentext ERSETZEN ("Vorbemessung im
        Rahmen"). Yama hat das aufgehoben, woertlich:
          "Bei N-003 faellt die gruene Plakette. Ersatzlos. Nicht umformulieren,
           nicht abschwaechen, nicht in Gelb. WEG.
           Nach eurer eigenen Regel: wo nichts besteht, steht nichts."
        Und seine Diagnose ist praeziser als meine:
          bestanden: true IST WAHR — der Kommentar sagt ehrlich "beide Nachweise
            <= 1,0". Das Feld luegt nicht.
          DIE PLAKETTE luegt: sie macht aus "beide gerechneten" ein "ALLE".
            Das Wort ALLE ist der Fehler, nicht der Boolean.
        N-003 rechnet ZWEI Nachweise von sechs moeglichen (Wind, Mehrfeld, Knicken,
        Auflagerpressung fehlen) und schreibt darueber "Alle Pruefungen bestanden".

        DIE FORM, die mir gehoert — und sie loest das Problem fuer ALLE Engines:
        Der PLAKETTENTEXT gehoert der ENGINE, nicht der Huelle.
        Yamas Begruendung: "Eine Huelle, die fuer dreizehn Rechnungen denselben Satz
        erfindet, wird irgendwann fuer eine davon falsch — heute ist es die Statik."
        Gewaehlt: EngineErgebnis bekommt ein OPTIONALES Urteilstext-Feld.
          Feld vorhanden  -> Plakette mit DIESEM Text
          Feld fehlt      -> KEINE Plakette
          N-003 liefert es NICHT  -> Plakette weg, ersatzlos
        Das ist genau die Mechanik von AUF-52 Scheibe C, eine Ebene feiner:
          dort  kein bestanden      -> keine Plakette
          hier  kein Urteilstext    -> keine Plakette
        Die zwoelf anderen Engines behalten ihren heutigen Wortlaut, weil die Huelle
        einen Vorgabetext behaelt, solange keine Engine widerspricht — bis die
        Klassifizierung (A-15) sagt, welche davon ebenfalls schweigen muessen.
        NICHT gewaehlt: bestanden weglassen. Es traegt echte Information und die
        Tests haengen daran (Yama ausdruecklich: "das Feld darf bleiben").

TEIL 3  EIN PRUEFLISTEN-EINTRAG, ueber das VORHANDENE Muster            (Yamas 1.1b)
        EngineErgebnis kennt schon "pruefungen?" mit { id, schwere, meldung,
        bestanden } — EngineFlaeche.tsx:165-185 rendert sie samt Schweregrad.
        berechneSparren liefert einen Eintrag mit dem Vorbehalt.
        Damit steht der Satz IN DERSELBEN LISTE wie die Ausnutzungen, nicht
        eine Zeile weiter oben und nicht in einem Tooltip. Yamas Bedingung
        woertlich erfuellt: im selben Blick.

TEIL 4  panel.grundlage TRAEGT DIE GRENZE                              (Yamas 1.1a)
        heute: 'Eurocode 5 (Biegung, Durchbiegung L/300) mit Schneelast nach
                DIN EN 1991-1-3'
        -> die Norm ohne Reichweite liest sich als Nachweis. Die Grenze kommt dazu.
        EIN Ort (enginePanels.ts), zentral gerendert (EngineFlaeche.tsx:56-58).
```

> **Drei von vier Teilen bauen nichts Neues:** *das Prüflisten-Muster, das `grundlage`-Feld und die
> Plakettenlogik existieren.* **Neu ist ein Feld und ein Text.** *Das ist „anbinden vor bauen" in
> Reinform — und es ist der Grund, warum dieser Auftrag klein ist, obwohl er vier Stellen berührt.*

## Der hausweite Befund — er gehört Yama, nicht diesem Auftrag

```text
'bestanden: boolean' ist ein MUSTER ueber ELF Engines, gemessen:
  sparrenBerechnung  <- N-003, STATIK, Haftungsbezug
  treppenBerechnung · treppe2D · treppe3D · treppenTypen
  abwassergefaelle · kuecheArbeitsdreieck · fbhAuslegung
  heizkreisVerteiler · wandaufbau (UPruefung) · werkzeugRegistry · configuratorPackage
-> ALLE haengen an DERSELBEN Plakette "Alle Pruefungen bestanden".
-> MEIN SATZ "N-003 ist die EINZIGE, deren Ergebnis in einem Bauwerk landen kann"
   IST FALSCH. Yama hat die Menge erhoben und ihn widerlegt:
     treppenBerechnung  DIN 18065        -> Sturzrisiko
     wandaufbau         U-Wert/Feuchte   -> Schimmel, Energie
     abwassergefaelle   DIN 1986         -> Rueckstau
     fbhAuslegung · heizkreisVerteiler   -> Anlagenauslegung
   Nur kuecheArbeitsdreieck ist wirklich ein HINWEIS ohne Fachrecht.
   Ich hatte die Liste gemessen und die REICHWEITE geschaetzt — genau die Klasse
   "richtige Einzelmessung, zu weite Aussage", die der Plan-Pruefer an sich selbst
   gefunden hat (a1d29aed). Diesmal in die andere Richtung: zu ENG statt zu weit.
   Yamas Hausregel, die daraus wird:
     "Wo eine Rechnung eine NORM nennt, darf die Software nicht 'bestanden' sagen."
   -> die Klassifizierung der dreizehn Dateien ist ein EIGENER Auftrag (A-15),
      und sie wird GEMESSEN (Normnennung ja/nein), nicht eingeschaetzt.
```

> **Ich behandle in diesem Auftrag NUR N-003.** *Ob die anderen zehn Engines eine Unterscheidung
> zwischen **Hinweis** und **Fachurteil** brauchen, ist eine Frage über das ganze Haus — und nach
> Yamas eigener Regel („gelb setzen, Reichweite hinschreiben, mir vorlegen") gehört sie ihm.*
> **Hier benannt, damit sie nicht zwischen den Aufträgen verschwindet.**

## Nicht-Ziele

- **Keine Änderung an der Rechnung.** *`berechneSparren` rechnet weiter genau wie heute; die
  Zahlen bleiben identisch.* **Nachweis: `sparrenBerechnung.test.ts` bleibt unverändert grün.**
- **Kein Entfernen von `bestanden`.** Es trägt echte Information.
- **Keine Änderung an den anderen zehn Engines.** Der hausweite Befund ist gemeldet, nicht behoben.
- **Keine Umformulierung der N-003-Ampel.** Sie ist Dauergelb, von Yama festgelegt.
- **Kein neuer Renderer, kein neues Panel.** Alle vier Teile hängen an vorhandenen Stellen.

## Scope

```text
geometry/sparrenBerechnung.ts               Pflichtfeld + Prueflisten-Eintrag
app/dashboard/enginePanels.ts               Plakettentext + grundlage mit Grenze
app/EngineFlaeche.tsx                       Plakettentext auslesen (falls noetig)
__tests__/sparrenBerechnung.test.ts         die Zusagen (siehe A-14-5)
```

## Wiederverwendungsprüfung (§5)

```text
pruefungen + schwere        VORHANDEN — EngineErgebnis:80, gerendert EngineFlaeche:165-185,
                           Muster in treppenBerechnung.ts:81 (p.push({id, schwere, meldung}))
                           -> ANSCHLIESSEN, nicht neu bauen
panel.grundlage            VORHANDEN — enginePanels.ts, gerendert EngineFlaeche:56-58
[feld: string]: unknown    VORHANDEN — EngineErgebnis:81, traegt Zusatzfelder durch den Cast
Plakettenlogik             VORHANDEN — EngineFlaeche:136, mit dem Praezedenzfall
                           "keine Plakette, wo nichts besteht" (AUF-52 Scheibe C)
SCHWERE_ANZEIGE            VORHANDEN — die Zeichen/Wort-Zuordnung je Schweregrad
N-003 Geltungsbereich      VORHANDEN — FORMELSAMMLUNG, Wortlaut von Yama
                           -> ZITIEREN, nicht neu formulieren
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten   KEINE
Bundle                     JA — resources/planner wird geaendert, Bundle wird neu gebaut
Rechenergebnisse           UNVERAENDERT. Nur ein Feld kommt hinzu.
Sichtbares Verhalten       JA, und das ist der Zweck: die gruene Plakette "Alle Pruefungen
                           bestanden" verschwindet bei der Sparren-Vorbemessung.
                           DAS GEHOERT IN DEN BERICHT — es sieht wie eine Verschlechterung
                           aus und ist eine Berichtigung.
Browserabnahme             JA, §9 — es ist eine Sichtaenderung. Vor der Abnahme
                           bash scripts/buehnen-waechter.sh (Erstnutzer-Regel A-04).
Testdaten-Ziel             KEINES
```

**Erstnutzer:** *jeder, der die Sparren-Vorbemessung benutzt — und, ehrlich gesagt, jeder Prüfer,
der den Bildschirm dieses Panels je als Nachweis in einer Unterlage gesehen hätte.*

## Akzeptanzkriterien

**A-14-1 (P1, Pflichtfeld im Ergebnis):** `SparrenErgebnis` trägt den Vorbehaltssatz. **Rot heute:**
`grep -cE 'vorbehalt|Vorbemessung' geometry/sparrenBerechnung.ts` → **im Interface 0** (der Satz
steht nur im Dateikopf, und *„ein Dateikopf wird nicht mitgeliefert, wenn jemand nur die Zahl
übernimmt"*).

**A-14-2 (P1, es überlebt den Cast — belegt, nicht vermutet):** Ein Probelauf zeigt das Feld
**nach** `berechneSparren(...) as unknown as EngineErgebnis` (`enginePanels.ts:210`). *Beleg für die
Machbarkeit: `EngineErgebnis:81` trägt `[feld: string]: unknown`.* **Ohne diesen Nachweis ist Teil 1
eine Hoffnung.**

**A-14-3 (P1, die Plakette ist WEG — ersatzlos, nicht umformuliert):** Bei der Sparren-Vorbemessung
erscheint **keine** Plakette. **Nicht** „Alle Prüfungen bestanden", **auch nicht** ein milderer Satz.
*Nachweis: Browserabnahme mit Bildlage, plus die Rohausgabe des heutigen Zustands zum Vergleich.*
**Und die zwölf anderen Engines behalten ihre Plakette** — Nachweis: Sichtprobe an mindestens zwei
davon (Treppe und eine Anlagen-Engine). *Eine Änderung, die alle dreizehn trifft, wäre ein anderer
Auftrag und würde zwölf Engines ohne Messung verändern.*

**A-14-4 (P1, der Vorbehalt steht im selben Blick):** Der Prüflisten-Eintrag erscheint **in derselben
Liste** wie `ausnutzungBiegung`/`ausnutzungDurchbiegung`, nicht darüber und nicht in einem Tooltip.
*Nachweis: Browserabnahme mit Bildlage — beide Ausnutzungswerte und der Vorbehaltssatz **im selben
Bildschirmausschnitt**, mit Pixelangabe wie bei A-01-4.*

**A-14-5 (P1, die Zahlen bleiben identisch):** `sparrenBerechnung.test.ts` bleibt **unverändert**
grün — **die Datei wird nicht angefasst**, nur ergänzt. *Nachweis: `git diff` zeigt für die
bestehenden Testfälle 0 geänderte Zeilen.* **Wer eine Rechnung ändert, während er ein Feld hinzufügt,
hat zwei Dinge getan.**

**A-14-6 (P1, `grundlage` trägt die Grenze):** Die sichtbare Zeile nennt **beides** — Norm **und**
Reichweite. *Nachweis: die gerenderte Zeichenkette im Bericht.*

**A-14-7 (`must_preserve`):** Insel-Suite unverändert grün (ohne Zahl). `app/`-Verzeichnis
(PHP-Seite) unberührt. **Die zehn anderen Engines byte-identisch** — Nachweis: `git diff --stat` über
`geometry/` zeigt genau **eine** geänderte Datei.

**A-14-8 (P1, Browserabnahme nach §9):** Sichtänderung, also Abnahme am Bildschirm, mit
`scripts/buehnen-waechter.sh` **vor** der Messung (A-04-Erstnutzerregel) und Aufruf samt Ausgabe im
Bericht.

**A-14-9 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **Messung unmittelbar vor der
ersten Änderung**.

## Kantenliste

```text
bestanden wird entfernt              -> VERBOTEN, Nicht-Ziel. Es traegt Information.
Plakettentext wird global geaendert  -> VERBOTEN. Nur N-003. A-14-3 misst die Gegenprobe.
Vorbehalt nur im Dateikopf           -> genau der Fehler, den A-14-1 behebt.
Vorbehalt als Tooltip                -> VERBOTEN. Yamas Bedingung: im selben Blick.
Rechnung wird "nebenbei" korrigiert  -> A-14-5. Zwei Dinge in einem Commit sind zwei Befunde.
Feld geht im Cast verloren           -> A-14-2 misst es. Nicht annehmen.
```

## Rückweg und Entdeckung

**Rückweg:** vier Textstellen und ein Feld, kein Schema, keine Daten. `git revert` genügt; das Bundle
wird neu gebaut. **Und wenn der Vorbehalt eines Tages falsch formuliert ist, ist er an EINER Stelle
falsch** — das war der Zweck.

**Entdeckung:** Erscheint irgendwo eine Ausnutzungszahl aus N-003 ohne den Vorbehalt, hat das
Pflichtfeld nicht gewirkt. *Prüfbar, indem man eine neue Ausgabestelle baut und schaut, ob sie den
Satz mitbringt — was sie muss, weil er im Ergebnis liegt.*

## Konfliktprüfung (§5)

```text
§3 UNMITTELBAR gemessen   1 IN_ARBEIT -> W-22/1, Scope werkbank/W-22/** + REGISTER.md
A-14 (dieses)             geometry/sparrenBerechnung.ts · app/dashboard/enginePanels.ts ·
                          app/EngineFlaeche.tsx · __tests__/sparrenBerechnung.test.ts
                          -> KEINE Beruehrung mit W-22. Disjunkt.
A-13                      app/Models · app/Http · tests/ (PHP)  -> disjunkt zu A-14 (TS)
W-21/1                    ABGENOMMEN, beruehrt sparrenBerechnung NICHT (nur Doku)
W-21L                     BLOCKIERT (Operanden), beruehrt eigenlastKnM2 ausdruecklich NICHT
```

```yaml
fehlerklasse: keine
prioritaet: P1
neuer_befund: "die Plakette zeigt heute 'Alle Pruefungen bestanden' fuer eine Vorbemessung,
               die Wind/Mehrfeld/Knicken nicht kennt. Weder in Yamas Auflage noch in meinem
               Vorschlag genannt — beim Messen der Ausgabestelle gefunden."
praezedenzfall: "EngineFlaeche:131-135 (AUF-52 Scheibe C) hat die Frage schon richtig
                 entschieden: keine Plakette, wo nichts besteht. N-003 besteht nichts."
hausweiter_befund_an_yama: "'bestanden' ist ein Muster ueber ELF Engines, alle an derselben
                            Plakette. N-003 ist die einzige mit Haftungsbezug. Ob die anderen
                            zehn eine Trennung Hinweis/Fachurteil brauchen, gehoert Yama."
anbinden_statt_bauen: "drei von vier Teilen nutzen vorhandene Mechanismen (pruefungen+schwere,
                       panel.grundlage, Plakettenlogik). Neu ist ein Feld und ein Text."
```
