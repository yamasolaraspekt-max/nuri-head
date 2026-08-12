# A-14 — der N-003-Vorbehalt gehört ins Ergebnis. Und die Plakette sagt heute „Alle Prüfungen bestanden"

```yaml
auftrag: "A-14"
titel: "Vorbehalt als Pflichtfeld · grundlage traegt die Grenze · die Plakette hoert auf, einen Nachweis zu behaupten"
art: "BAU, Spur A — Fachrecht/Haftung beruehrt, aber KEINE Fachentscheidung: der Vorbehalt ist von Yama festgelegt"
spur: A
heimat_app: ticket
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


## §11 — Bericht A-14 (Generator, 12.08.2026)

```yaml
auftrag: "A-14"
zustand: CODE_FERTIG
bau_commit: "e0722979"
buendel_commit: "21940d33"
in_arbeit_commit: "f4441c36"
basis: "1e09280d"

kriterien:
  A-14-1: GRUEN   # SparrenErgebnis.vorbehalt ist Pflichtfeld, gespeist aus N003_VORBEHALT
  A-14-2: GRUEN   # Probelauf zeigt das Feld NACH dem Cast — und ist zum Dauertest geworden
  A-14-3: GRUEN   # Plakette beim Sparren NICHT IM DOM; Treppe und Heizkoerper zeigen ihre
  A-14-4: GRUEN   # Pixellage gemessen, s. u.
  A-14-5: GRUEN   # sparrenBerechnung.test.ts 0 geaenderte Zeilen
  A-14-6: GRUEN   # grundlage nennt Norm UND Reichweite, im Browser sichtbar
  A-14-7: GRUEN   # geometry/ genau EINE Datei, PHP-app/ 0, resources/ 0/0/0, Suite gruen
  A-14-8: GRUEN   # Browserabnahme, Waechter VOR der Messung mit exit 0
  A-14-9: GRUEN   # f4441c36: beide Orte, Befehl mit Ausgabe, vor der ersten Aenderung

browserabnahme:
  waechter_vorher: "bash scripts/buehnen-waechter.sh -> exit 0
                    'BUEHNE OK  PID 68498 (php -S) — Datenbank ticket_testing'"
  weg: "/login -> /admin/hausplaner/studio -> Experte -> Fachplaner -> Sparren-Vorbemessung
        -> Auslegung berechnen"
  viewport: "1700 x 1200"
  A-14-4_pixellage:
    ausnutzung_biegung:      "x=861  y=456  h=19"
    ausnutzung_durchbiegung: "x=861  y=533  h=19"
    vorbehalt:               "x=861  y=586  h=19  'Vorbemessung, ersetzt keine prüffähige Statik'"
    befund: "alle drei in DERSELBEN Spalte, der Vorbehalt 53 px unter der zweiten Ausnutzung,
             alle innerhalb der 1200 px Bildhoehe — EIN Blick, kein Tooltip, keine eigene Zeile darueber."
  A-14-3_plakette:
    sparren: "NICHT IM DOM — kein Element mit 'Alle Prüfungen bestanden'. Ersatzlos, nicht versteckt."
    treppe: "'Alle Prüfungen bestanden' bei y=413 — unveraendert"
    heizkoerper: "'Eine Prüfung ist nicht bestanden' bei y=380 — die ANDERE Fassung, also ist
                  nicht nur der Gruen-, sondern auch der Rot-Fall anderswo unberuehrt"
  bilder: "a14-sparren-ergebnis.png · a14-treppe2.png · a14-zweite.png (Kratzverzeichnis)"

was_ich_dem_auftrag_widerspreche:
  zahl_der_engines:
    auftrag: "A-14-3 sagt 'die zwoelf anderen Engines', A-14-7 sagt 'die zehn anderen'"
    gemessen: "ACHT Engines insgesamt (engine-treppe, -sparren, -fbh, -heizkoerper,
               -fensterprodukt, -abwasser, -kueche, -pv) — also SIEBEN andere."
    bemerkung: "die beiden Zahlen widersprechen sich zudem untereinander. Die Sichtprobe habe
                ich an den REAL vorhandenen gefahren, nicht an der Zahl."

eingriff_in_die_testdatenbank:
  was: "Probenutzer angelegt — id 268, a14-probe@example.test, is_admin=1"
  wo: "ticket_testing, vorher 0 Nutzer, nachher 1"
  warum: "die Route liegt hinter auth; ticket_testing hatte KEINEN Nutzer"
  kanal: "ueber PHPUnit, das laut phpunit.xml DB_DATABASE=ticket_testing erzwingt; der Test hat
          die Datenbank vor dem Schreiben selbst zugesichert"
  bemerkung: "scripts/browser-buehne.sh haelt fest, dass der fehlende Testbenutzer damals
              'Glueck war, nicht Vorsicht' — der Schutz ist die DB-Pruefung, nicht die leere Tabelle."

nicht_gemessen:
  - "die uebrigen fuenf Engines — A-14-3 verlangt mindestens zwei, ich habe zwei gefahren"
  - "ob der Vorbehalt in Export/Stueckliste/PDF ankommt — es gibt heute keinen solchen Weg
     (0 Treffer ausserhalb enginePanels/EngineFlaeche). Genau dafuer steht er im ERGEBNIS."

nebenbefund_nicht_meiner:
  - "die Buehne wirft bei jedem Aufruf ein Notice aus Laravels server.php:21 —
     'file_put_contents(): Write of N bytes failed with errno=32 Broken pipe'.
     Nicht meine Aenderung, nicht behoben, hier gemeldet."

ballbesitz: evaluator
```

## §11 — Votum A-14 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-14"
votum: ABGENOMMEN
abnahme_commit: "21940d33"
bau_commit: "e0722979"
elter: "efca1899"
basis: "1e09280d"
pruefstand: "worktree --detach auf 21940d33 (Bau) und efca1899 (Elter), je node_modules UND vendor
             per cp -al; Buendel im Arbeitsbaum md5-gleich mit 21940d33, deshalb Browsermessung dort"

messtisch:
  A-14-1: GRUEN
    beleg: "sparrenBerechnung.ts:89  'vorbehalt: string;'  — PFLICHTFELD, kein '?';
            :149  'vorbehalt: N003_VORBEHALT,';  :100 der Satz selbst.
            Rot am Elter: die Zusage faellt dort (1 fail)."
  A-14-2: GRUEN
    beleg: "sparrenVorbehalt.test.ts gegen das ELTER-Modul: 1 fail.
            Im Pruefstand: pass 1 / fail 0, Rohausgabe zeigt
            'vorbehalt = \"Vorbemessung, ersetzt keine pruefaehige Statik\"' NACH dem Cast."
    zwei_richtungen: "Zwei Mutationen, jeder Anker vorher als EINDEUTIG gezaehlt (Treffer 1):
                      (a) 'vorbehalt: N003_VORBEHALT,' entfernt -> fail 1.
                      (b) 'keinGesamturteil: true,' entfernt   -> fail 1.
                      Beide zurueckgestellt, md5 vorher/nachher identisch. Die Zusage haelt
                      BEIDE Haelften des Auftrags fest, nicht nur das Feld."
  A-14-3: GRUEN
    browser: "Sparren-Flaeche nach 'Berechnen': KEIN Element mit 'Alle Pruefungen bestanden'
              und keines mit 'Eine Pruefung ist ...' — bei 1440, 1024 und 375 px je [] (leer)."
    gegenprobe: "Treppen-Auslegung 'Alle Pruefungen bestanden' x=763 y=263 sichtbar (1440),
                 y=240 (1024), y=1045 (375, unter der Falz wie die ganze Liste).
                 Fussbodenheizung-Auslegung: x=763 y=284 sichtbar.
                 Abwasser-Gefaelle: x=763 y=406 sichtbar."
    code: "'keinGesamturteil' ist GENAU EINMAL gesetzt (enginePanels.ts:176, engine-sparren);
           die Bedingung lautet '!panel.keinGesamturteil && typeof ergebnis.bestanden === boolean'.
           Fuer jedes andere Panel ist sie wortgleich mit dem Elter."
    meine_zaehlfalle_offengelegt: "grep -c 'Plakette' in EngineFlaeche.tsx: Pruefstand 4, Elter 2.
           Der Zuwachs sieht nach 'mehr Plakette' aus und ist der ERKLAERENDE KOMMENTAR des Bauens.
           Ich habe den Diff gelesen statt die Zahl zu melden — Punkt 4 des Takts."
    warum_nicht_PV: "A-14-3 verlangt die Sichtprobe an 'Treppe und einer Anlagen-Engine'.
           Die einzige Anlagen-Engine ist PV, und PV KANN keine Plakette haben: pvBelegung.ts
           enthaelt 'bestanden' 0-mal, am Elter WIE am Bau, und die Datei wird vom Bau nicht
           angefasst. Eine leere Plakette dort belegt nichts. Ich habe deshalb zwei Engines mit
           echter Plakette genommen (Heizung/TGA und Sanitaer) statt eine Null zu melden."
  A-14-4: GRUEN
    beleg_1440: "Ausnutzung Biegung y=429 · Ausnutzung Durchbiegung y=506 · Vorbehalt y=532 ·
                 Wert y=559 — alle x=728, dieselbe Spalte, dieselbe Liste, UNTER den beiden
                 Ausnutzungen. Kein Tooltip, keine Zeile darueber."
    beleg_1024: "y=426 / 503 / 529 / 556, alle x=520, alle sichtbar."
    beleg_375:  "Nach Bildlauf zur Liste: y=567 / 644 / 670 / 697, alle x=40, alle sichtbar.
                 OHNE Bildlauf liegt bei 375 px die GANZE Ergebnisliste unter der Falz — auch
                 beide Ausnutzungen und die Plakette der Treppe. Das ist die Seitenlaenge,
                 kein Befund gegen den Vorbehalt."
    code: "enginePanels.ts:225 '{ schluessel: vorbehalt, label: Vorbehalt }' als LETZTER Eintrag
           desselben felder-Arrays, in dem :219 ausnutzungBiegung und :222 ausnutzungDurchbiegung
           stehen."
  A-14-5: GRUEN
    beleg: "git diff efca1899 21940d33 -- sparrenBerechnung.test.ts: leer. 0 geaenderte Zeilen."
  A-14-6: GRUEN
    beleg: "Sichtbare Zeile, gemessen bei 1440/1024/375 je sichtbar=true:
            'Grundlage: Eurocode 5 (Biegung, Durchbiegung L/300) mit Schneelast nach DIN EN
             1991-1-3 — VORBEMESSUNG im Entwurf: kein Ausfuehrungsnachweis, keine
             Genehmigungsunterlage, keine Freigabe zur Ausfuehrung. Wind, Mehrfeld, Knicken
             und Auflagerpressung sind NICHT erfasst.'
            NORM genannt (Eurocode 5, DIN EN 1991-1-3) UND GRENZE genannt."
    meine_zweite_falle: "Mein erster Lauf schnitt den Text bei 70 Zeichen ab und meldete
            nenntGrenze=false. Die Grenze stand hinter dem Schnitt. Korrigiert und neu gemessen."
  A-14-7: GRUEN
    beleg: "Insel-Suite im Pruefstand: tests 1693 / pass 1693 / fail 0 / skipped 0.
            Am Elter: 1692/1692 — Differenz +1 ist genau die neue Zusage.
            'app/'-Treffer im Bau: 0 (keine PHP-Seite beruehrt).
            tsc -p tsconfig.hausplaner.json --noEmit: grün (in build:hausplaner vorgeschaltet)."
    buendel: "npm run build:hausplaner im Pruefstand erzeugt md5 a5ea00566991cf15a5ce2a83c15e08f1
              — BYTEGLEICH mit dem committeten Buendel aus 21940d33. Das Buendel stammt also
              aus genau diesen Quellen, nicht aus einem aelteren Lauf."
  A-14-8: GRUEN
    waechter_vorher: "bash scripts/buehnen-waechter.sh ->
                      'BUEHNE OK  PID 68498 (php -S) — Datenbank ticket_testing'
                      'ALLE BUEHNEN OK   1 geprueft, Datenbank jeweils ticket_testing.'
                      Gefahren VOR der ersten Messung (A-04-Erstnutzerregel)."
    was_die_buehne_ausliefert: "curl auf /hausplaner/hausplaner.js -> HTTP 200, 1448736 Bytes,
                      md5 a5ea0056... — identisch mit 21940d33, verschieden vom Elter
                      (57314651...). Ich habe also den BAU gemessen, nicht irgendeinen Stand."
    weg: "/login (a14-probe@example.test) -> /admin/hausplaner/studio -> Expertenmodus ->
          Fachplaner -> <Engine> -> Berechnen"
    breiten: "1440x900, 1024x800, 375x812; Sichtbarkeit im Viewport gemessen (Rechteck,
              visibility, display, opacity), nicht blosse Existenz im DOM."
  A-14-9: GRUEN
    beleg: "f4441c36 (12.08. 02:33:02) fasst NUR docs/STATUS.md an und setzt A-14 an BEIDEN
            Orten auf IN_ARBEIT (Tafelzeile + 'zustand: IN_ARBEIT' im Datensatz).
            Erste Aenderung am Produktivcode: e0722979 (02:46:33) — 13 min 31 s spaeter.
            SELBST nachgemessen am Stand f4441c36: genau EINE Tafelzeile IN_ARBEIT und genau
            EIN 'zustand: IN_ARBEIT', beide A-14. §3 gehalten."

spec_abweichung_nicht_blockierend:
  klasse: SPEC
  ball: planner
  was: "Der Auftrag nennt zwei verschiedene und beide falsche Zahlen: A-14-3 'die zwoelf anderen
        Engines', A-14-7 'die zehn anderen'. Gemessen: enginePanels.ts fuehrt ACHT Panels
        (treppe, sparren, fbh, heizkoerper, fensterprodukt, abwasser, kueche, pv) — also SIEBEN
        andere. Der Generator hat es selbst gemeldet; ich habe es unabhaengig nachgezaehlt und
        bestaetige die Acht."
  warum_ohne_blockade: "§12.5 — die Sache stimmt, die Zahl im Blatt nicht. Die Forderung hinter
        der Zahl ('nur diese eine Engine verliert die Plakette') ist erfuellt und doppelt belegt,
        im Bild und im Code."
  zusatz: "A-14-3 verlangt ausserdem eine Sichtprobe an 'einer Anlagen-Engine'. Diese Probe ist
        am heutigen Stand NICHT FUEHRBAR (PV hat konstruktiv keine Plakette). Der Planner moege
        das Kriterium beim naechsten Blatt an die Wirklichkeit binden."

eingriff_von_mir_offengelegt:
  was: "Ich habe das PASSWORT des Probenutzers 268 (a14-probe@example.test, vom GENERATOR
        angelegt) in ticket_testing neu gesetzt — ich kannte seines nicht, und die Route liegt
        hinter auth."
  umfang: "ein Datensatz, eine Spalte, in ticket_testing. Nutzerzahl vorher 1, nachher 1.
           Kein Produktivsystem, keine zweite Aenderung."
  warum_hier: "Eine Browserabnahme ohne Anmeldung gibt es nicht. Ich melde es, weil ein
               Eingriff des Pruefers in die Buehne den Messwert beeinflussen KANN — hier nicht,
               denn gemessen wurde die gerenderte Flaeche, nicht der Nutzer."

nebenbefund_bestaetigt:
  was: "Die Bühne wirft bei jedem Aufruf 'file_put_contents(): ... errno=32 Broken pipe' aus
        Laravels server.php. Vom Generator gemeldet, von mir in der Browserkonsole
        wiedergesehen. Nicht A-14, nicht behoben, bleibt offen."

nicht_gemessen:
  - "die uebrigen vier Engines mit Plakette (heizkoerper, fensterprodukt, kueche, wand) —
     A-14-3 verlangt mindestens zwei, ich habe DREI gefahren (treppe, fbh, abwasser)."
  - "ob der Vorbehalt in Export/PDF/Stueckliste ankommt — es gibt heute keinen solchen Weg."

ballbesitz: release-pruefer
```

---

## Release-Prüfung A-14 (§10) — 12.08.2026, Release-Prüfer

**Urteil: `RELEASE_FREI`.** Produktivcode mit Sichtwirkung, §10 voll gefahren — nicht die
Doku-Sammelform. Alle Zahlen unten sind an HEAD `a2385d35` selbst gemessen, jede mit Befehl.

```yaml
auftrag: "A-14"
inhalt_sha: "21940d33"          # das Buendel; enthaelt den Quellbau e0722979 als Vorfahr
status_commit: "a2385d35"       # Zweigspitze zum Pruefzeitpunkt
merge_ziel: "auto/hausplaner-integration (Arbeitszweig, KEIN main-Merge)"
merge_basis_sha: "efca1899"     # Elter des Quellbaus
merge_verfahren: nicht_anwendbar  # kein PR, kein main-Merge; Sicherungs-Push auf fork
merge_sha: null
votum: RELEASE_FREI
ci: pass
artefakte_reproduzierbar: true
migration: nicht_anwendbar
rueckweg: pass
smoke_test_plan: "Buehne starten (bash scripts/buehnen-waechter.sh), Sparren-Vorbemessung oeffnen,
                  berechnen: KEINE gruene Plakette, dafuer die Zeile 'Vorbehalt — Vorbemessung,
                  ersetzt keine prueffaehige Statik' in DERSELBEN Werteliste unter den beiden
                  Ausnutzungen; Treppen-Auslegung zur Gegenprobe: Plakette da."
befunde:
  - "P2/SPEC (nicht blockierend): die sichtbare grundlage-Zeile nennt VIER der sechs Sonderlasten
     aus Yamas NICHT-ERLAUBT-Liste. Schnee-Verwehung und Lastkombinationen fehlen. Details unten."
regel_version: "1.4.2, sha256 285c9830…, 884 Zeilen, gelesen"
```

### Frage 1 — Liegt das gebaute Bündel im Kandidaten, und passt es zum Quellstand?

**Ja, byte-gleich, und der Neubau bestätigt es.**

```text
git log -1 --oneline -- public/hausplaner/hausplaner.js   -> 21940d33 (der Bau selbst)
md5 Arbeitsbaum      a5ea00566991cf15a5ce2a83c15e08f1
md5 git show HEAD:…  a5ea00566991cf15a5ce2a83c15e08f1   identisch
npm run build:hausplaner   (schema:check + tsc --noEmit + vite build, exit 0, 356 Module)
md5 NACH dem Neubau  a5ea00566991cf15a5ce2a83c15e08f1   UNVERAENDERT
git status --short public/                                leer
```

Der Arbeitsbaum ist damit nach dem Neubau ohne Zutun wiederhergestellt — der Neubau erzeugte
denselben Stand. **Das Bündel trägt die Änderung auch tatsächlich:**

```text
Kandidat: "Vorbemessung, ersetzt keine prüffähige Statik"  1×
          "keinGesamturteil"                               2×
          "keine Genehmigungsunterlage"                     1×
Elter efca1899 (md5 57314651…): alle drei 0×
```

Zusatzschluss, der die Kette schließt: `a5ea0056` ist genau die md5, die der Evaluator im
Browserlauf ausgeliefert bekam (curl+md5, Votum `2d8592ab`). **Das Artefakt, das am Bildschirm
gemessen wurde, ist Byte für Byte das Artefakt im Release-Kandidaten.**

### Frage 2 — Ist der Rückweg bei einer SICHTänderung wirklich ein reiner `git revert`?

**Ja — zerstörungsfrei belegt, ohne den Arbeitsbaum anzufassen.**

```text
git diff efca1899 21940d33 -- resources/planner/hausplaner public/…/hausplaner.js  (168 Zeilen)
  | git apply --check -R          -> exit 0   "laesst sich sauber zuruecknehmen"
git log 21940d33..HEAD -- <alle 5 A-14-Pfade>   -> 0 Commits (nichts kreuzt den Rueckweg)
git diff --name-only efca1899 21940d33 -- database/         -> 0
git diff --name-only … -- app/ routes/ config/              -> 0
git diff … -- resources/ | grep -iE "migration|schema|localStorage|fetch|DB::|persist|revision" -> leer
git diff … -- resources/ | grep -iE "password|secret|token|api_key|\.env|DB_"                    -> leer
git diff … -- resources/ | grep -iE "permission|policy|auth|organisation|tenant|csrf"            -> leer
```

Kein Schema, keine Migration, kein Datenpfad, keine Rechte-/Mandantengrenze berührt. Das Bündel ist
**mitcommittet**, der Revert stellt also Quelle *und* Auslieferung in einem Zug her; ein Neubau ist
Kür, nicht Pflicht. `migration: nicht_anwendbar`, `rueckweg: pass`.

**Und weiß ein Nutzer, der die Plakette vermisst, warum sie weg ist?** Ja — sie verschwindet nicht
stumm, sie wird ersetzt. An ihre Stelle treten zwei sichtbare Texte im selben Panel: die
`grundlage`-Zeile mit der Reichweitengrenze und der Ergebniseintrag „Vorbehalt" **in derselben
Werteliste** wie die beiden Ausnutzungen (`enginePanels.ts:223`, Pixellage vom Evaluator gemessen:
y=532 unter y=429/506, gleiche Spalte x=728).

**DoR-Hinweis des Plan-Prüfers, hiermit ausdrücklich genannt statt geschluckt:** *Der §5-Block des
Blattes führt den Rückweg NICHT als eigene Zeile.* Er steht nur im Abschnitt „Rückweg und
Entdeckung". Bei einem Auftrag, der sichtbares Verhalten ändert, ist das nicht rein formal — wer die
Plakette vermisst, muss den Weg zurück in der Auswirkungstabelle finden, nicht im Fließtext. Der
Plan-Prüfer hat es dreimal in Folge (A-14, A-15, W-09) gemeldet; es ist ein **Muster der
Blattvorlage**, kein Versehen dieses Schnitts. Sachlich blockiert es nicht: ich habe den Rückweg
oben gemessen, er ist ein reiner Revert.

### Frage 3 — Die richtige Lesart von A-14-3, selbst nachgeprüft

Ich habe die Angabe des Plan-Prüfers **nicht übernommen**, sondern die *Renderbedingung selbst
ausgeführt*. `EngineFlaeche.tsx:138` lautet `{!panel.keinGesamturteil && typeof ergebnis.bestanden
=== 'boolean' && (` — also habe ich für **jedes** Panel die Engine mit ihren Startwerten gerechnet
und genau diesen Ausdruck ausgewertet (Runner wie `test:hausplaner`, Skript im Scratchpad):

```text
engine-treppe          keinGesamturteil=false  bestanden=true       => PLAKETTE JA
engine-sparren         keinGesamturteil=true   bestanden=false      => PLAKETTE NEIN
engine-fbh             keinGesamturteil=false  bestanden=true       => PLAKETTE JA
engine-heizkoerper     keinGesamturteil=false  bestanden=false      => PLAKETTE JA
engine-fensterprodukt  keinGesamturteil=false  bestanden=undefined  => PLAKETTE NEIN
engine-abwasser        keinGesamturteil=false  bestanden=true       => PLAKETTE JA
engine-kueche          keinGesamturteil=false  bestanden=true       => PLAKETTE JA
engine-pv              keinGesamturteil=false  bestanden=undefined  => PLAKETTE NEIN
```

**Die Lesart hält, und die Gegenprobe ist stärker als verlangt.** `keinGesamturteil: true` steht
genau einmal im Repo (`enginePanels.ts:176`), im Block `engine-sparren` (Zeile 169, Titel
„Sparren-Vorbemessung"; der nächste Panelblock beginnt bei 229). **Fünf** Engines mit echtem
Bestehens-Merkmal behalten ihre Plakette — darunter `engine-heizkoerper`, die mit den Startwerten
`bestanden=false` liefert und die **rote** Plakette zeigt. Das ist die schärfere Gegenprobe: das
Flag unterdrückt *diese eine Engine*, nicht etwa „negative Urteile" allgemein. Die zwei ohne
Plakette (`fensterprodukt`, `pv`) haben **gar kein** `bestanden` — das ist der AUF-52-Präzedenzfall
und von A-14 unberührt. `bestanden` bleibt bei Sparren erhalten (Nicht-Ziel eingehalten), nur die
Plakette darüber fällt.

### Standard-§10

**Kette** (jeweils `git merge-base --is-ancestor`, Exit 0):

```text
1e09280d (Basis) -> f4441c36 (IN_ARBEIT, §3-Beleg) -> efca1899 (Elter) -> e0722979 (Bau Quelle)
 -> 21940d33 (Bau Buendel) -> 1643409d (CODE_FERTIG) -> 8a1603e9 (Claim) -> 2d8592ab (ABGENOMMEN)
 -> 5238cc5d (Release-Claim) -> a2385d35 (HEAD)
```

Lückenlos, linear, keine Rückwärtssprünge. *Anmerkung ohne Befund:* zwischen `e0722979` und
`21940d33` liegen zwei fremde Plan-Prüfer-Commits (`73888d10`, `a5aab234`, beide nur `docs/`) — der
Quellbau ist also nicht der unmittelbare Elter des Bündels. Das kreuzt den A-14-Scope nicht
(gemessen: 0 Berührungen), und §3 war eingehalten, weil A-14 das einzige `IN_ARBEIT` war.

**Scope-Reinheit** — beide Bau-Commits sauber:

```text
git show e0722979 --stat -> 4 Dateien, alle im Blatt-Scope bzw. dessen Zusage:
    __tests__/sparrenVorbehalt.test.ts (+34)  app/EngineFlaeche.tsx (+5/-1)
    app/dashboard/enginePanels.ts (+19/-1)    geometry/sparrenBerechnung.ts (+20)
git show 21940d33 --stat -> 1 Datei: public/hausplaner/hausplaner.js
git diff --stat efca1899 21940d33 -- resources/…/geometry/  -> GENAU EINE Datei (A-14-7)
git diff --name-only efca1899 21940d33 -- app/              -> 0 (PHP-Seite unberuehrt)
```

`sparrenBerechnung.test.ts` ist in keinem der beiden Commits — A-14-5 hält am Release-Stand.

**Insel-Suite selbst gefahren** (`npm run test:hausplaner`, Runner aus `package.json:10`, kein bares
`node --test` auf `.ts`): **1693 pass / 0 fail / 1693 tests.** Deckt sich mit dem Bau- und dem
Abnahmelauf.

**`must_preserve` in allen drei Richtungen, einzeln je Verzeichnis** (A=hinzugefügt, M=geändert,
D=gelöscht, `git diff --diff-filter=…`):

```text
resources/   21940d33..HEAD  A=0 M=0 D=0     scripts/   21940d33..HEAD  A=0 M=0 D=0
             1643409d..HEAD  A=0 M=0 D=0                1643409d..HEAD  A=0 M=0 D=0
             2d8592ab..HEAD  A=0 M=0 D=0                2d8592ab..HEAD  A=0 M=0 D=0
             Arbeitsbaum     A=0 M=0 D=0                Arbeitsbaum     A=0 M=0 D=0
```

**Beifang-Kontrolle ab CODE_FERTIG:** `git diff --stat 21940d33 HEAD` zeigt **ausschließlich
`docs/`** (STATUS.md, das A-14-Blatt, zwei A-15-Berichte). Kein `resources/`, kein `public/`, kein
`app/`, kein `scripts/`. Der geprüfte Produktivstand ist seit dem Bau unberührt.

### Der fachliche Blick — verändert die Umsetzung die Reichweitengrenze?

Das war meine Abbruchbedingung. **Nein — sie trägt sie, sie verschiebt sie nicht.** Der Vorbehalt
im Pflichtfeld ist Yamas Wortlaut **zeichengenau**:

```text
Code    sparrenBerechnung.ts:  N003_VORBEHALT = 'Vorbemessung, ersetzt keine prüffähige Statik'
Quelle  FORMELSAMMLUNG.md:729  „Wer die Zahl sieht, sieht den Satz ‚Vorbemessung, ersetzt keine
                                prüffähige Statik'. Nicht als Fußnote …, sondern am Wert."
```

Die drei **NICHT-ERLAUBT-Verwendungen** (FORMELSAMMLUNG 707–710) stehen vollständig in der
sichtbaren `grundlage`-Zeile: kein Ausführungsnachweis, keine Genehmigungsunterlage, keine Freigabe
zur Ausführung. Nichts wird erlaubt, was Yama verboten hat.

**P2/SPEC-Befund, nicht blockierend — die Sonderlastenliste ist verkürzt:**

```text
Yama (FORMELSAMMLUNG 711-712):  Wind · Schnee-Verwehung · Mehrfeld · Knicken ·
                                Auflagerpressung · Lastkombinationen        (sechs)
Dateikopf sparrenBerechnung.ts: Wind · Mehrfeld · Knicken · Auflagerpressung ·
                                Lastkombinationen                           (fuenf, unveraendert)
grundlage-Zeile (A-14, neu):    Wind · Mehrfeld · Knicken · Auflagerpressung (vier)
```

Es fehlen **Schnee-Verwehung** und **Lastkombinationen**. Warum das **kein** Blocker ist: die
`grundlage`-Zeile trug vor A-14 **null** Reichweitenangabe (nur „Eurocode 5 … DIN EN 1991-1-3") —
die Änderung ist auf dieser Achse rein **additiv**, es wurde nichts weggenommen. Die verbindliche
Aussage ist der Totalausschluss „ersetzt keine prüffähige Statik", und der steht wortgleich als
Pflichtfeld am Wert. Niemand gewinnt eine Erlaubnis. **Aber:** da die Plakette nun fällt, ist diese
Zeile der sichtbare Träger der Grenze, und zwei Fassungen derselben Warnung sind genau die zweite
Wahrheit, vor der der Code-Kommentar in `sparrenBerechnung.ts` selbst warnt. **Erledigt-Kriterium:**
die `grundlage`-Zeile nennt alle sechs Posten aus FORMELSAMMLUNG 711–712, oder das Blatt begründet
die Auswahl. Ball beim Planner, eigener Schnitt.

### Übrige §10-Punkte

```text
Qualitaetstore am Kandidaten neu gruen  schema:hausplaner:check + tsc --noEmit + vite build (exit 0)
                                        npm run test:hausplaner 1693/1693
Artefakte frisch/reproduzierbar         ja, md5 nach Neubau unveraendert
Konfiguration/Umgebung/Abhaengigkeiten  unveraendert (0 Dateien in config/ routes/ app/ database/)
Sicherheit/Rechte/Mandant/Datenschutz   unberuehrt (Scans leer)
§15 Testdaten                           keine DB im Diff; die neue Zusage ist reines TS
offene P0/P1                            keine
```

**Nicht von mir gemessen, bewusst:** die Browserabnahme selbst (§9, gehört dem Evaluator; gefahren
mit Wächter-Vorlauf an genau diesem Bündel-md5, deshalb übernehme ich die Pixellagen als belegt).

**Fremde Arbeit im Baum, nicht angefasst** (§14): eine uncommittete Löschung von
`docs/BERICHT-A-15-klassifikation.md` sowie die Streudateien `1692` und `zz-unlink-probe`. Die
Löschung gehört zu A-15: `git mv` wurde in `82d7c31e` nur zur Hälfte committet — der neue Pfad ist
drin, der alte ist **weiterhin getrackt**. Berührt A-14 nicht, gemeldet an den Generator.

**Nächster Schritt: Yama.** Nach §10 darf nur er die Veröffentlichung genehmigen. Ein main-Merge
steht hier nicht an; der Sicherungs-Push auf `fork/auto/hausplaner-integration` ist unten verbucht.

### Nachtrag — Sicherungs-Push, ein eigener Fehler und ein Fund am Fernziel

**Push-Ergebnis: ABGELEHNT (non-fast-forward), zweimal versucht, NICHT forciert.**

```text
git push fork auto/hausplaner-integration
  ! [rejected]  auto/hausplaner-integration -> … (non-fast-forward)
git rev-list --left-right --count fork/…...HEAD   -> 12  2
```

`fork` trägt zwölf Commits, die lokal fehlen — darunter Merge-Commits einer parallelen Linie. Ein
Fast-Forward ist unmöglich. **Kein `--force`** (§14, ohne Yamas Freigabe verboten) und **kein
einseitiger Merge der zwölf Fremd-Commits** in den Zweig, den ich soeben zertifiziert habe: das wäre
eine Integrationsentscheidung, nicht der beauftragte Sicherungs-Push.

**Gute Nachricht zuerst:** der Inhalts-Commit `21940d33` **liegt bereits auf `fork`**, ebenso mein
Release-Commit `93b591e1` (dort per `b455b93b` gemerged). Der geprüfte Produktivstand ist am
Fernziel gesichert; nur zwei lokale Commits fehlen dort (`f8b0ee26` fremd, `5d88f198` meine
Reparatur).

**MEIN FEHLER, offengelegt — `93b591e1` hat drei fremde Dateien mitgerissen.** Ich hatte genau zwei
Pfade gestaged und mit `git diff --cached --name-only` geprüft; die Anzeige zeigte genau diese zwei.
Dann habe ich **ohne Pfadangabe** committet — und der Commit nahm den **gesamten geteilten Index**,
in dem veraltete Einträge dreier anderer Rollen lagen:

```text
docs/BEFUND-GETEILTER-INDEX-STEHT-VOLL.md   GELOESCHT       (62 Zeilen)
docs/FAHRPLAN-WERKZEUGKASTEN.md             202 -> 166      (fbce86eb, Planner)
docs/BERICHT-A-15-fachaussage-oder-hinweis.md  258 -> 233   (bd011a06, Generator)
```

*Die Datei, die ich gelöscht habe, ist der Befund des Generators über genau diese Falle.* **Lokal
behoben in `5d88f198`, zerstörungsfrei** — kein `reset`, kein `amend`, keine Historienänderung; der
Arbeitsbaum trug noch den richtigen Stand (sha256 aller drei identisch mit `ad8f7314`), der
Reparatur-Commit stellt ihn exakt wieder her. **Die Lehre:** `git diff --cached --name-only` zeigt
den Index *zum Zeitpunkt des Aufrufs*; bei einem Index, in den drei Rollen nebenher schreiben, ist
das kein Zustand, auf den man sich einen Schritt später berufen kann. Belastbar ist nur: `add`,
Index-Inhalt **gegen die erwartete Liste vergleichen und bei Abweichung abbrechen**, alles in einem
Arbeitsgang.

> **⚠ OFFEN UND DRINGEND — der Schaden ist auf `fork` gelandet, die Reparatur nicht.** `b455b93b`
> hat `93b591e1` gemerged; auf `fork` steht heute `BEFUND-GETEILTER-INDEX…` mit **0** Zeilen,
> `FAHRPLAN-WERKZEUGKASTEN` mit **166** statt 202, `BERICHT-A-15-…` mit **233** statt 258.
> `5d88f198` kann das ohne Integration der zwölf Fremd-Commits nicht erreichen. **Das braucht eine
> Entscheidung: Integration des Fernstands in den Arbeitszweig (dann Push) — oder ein eigener
> Transport-Auftrag.** Nicht meine Entscheidung, deshalb hier und nicht still.

**Zweiter Fund am Fernziel, ohne Wertung:** die A-14-Tafelzeile auf `fork` steht bereits auf
`BETRIEBSBESTAETIGT` (Ball `–`), lokal auf `RELEASE_FREI` (Ball Yama). Eine parallele Instanz hat
den Übergang dort schon gesetzt. **Ich ziehe meinen lokalen Zustand deswegen nicht nach** — ich habe
die Betriebsprüfung nicht gesehen und übernehme keinen Zustand, den ich nicht gemessen habe. Der
Widerspruch gehört gemeldet, nicht angeglichen.

**Die Release-Prüfung selbst ist von alledem unberührt:** der Fehler lag im *Transport*, nicht in
der *Messung*. Gegenprobe am finalen HEAD nach allen Parallel-Commits: `git diff --stat 21940d33
HEAD -- resources/ public/ app/ scripts/ database/ config/ routes/` **leer**, Bündel-md5 weiterhin
`a5ea0056`, Insel-Suite erneut **1693/1693**. Urteil bleibt **`RELEASE_FREI`**.
