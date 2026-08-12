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
