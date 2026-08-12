# A-23 — SIEBEN Zettel an einer erledigten Sperre. Einer ist ein Testname, und einer trug das Wort nicht

```yaml
auftrag: "A-23"
titel: "Sieben Stellen in der Insel führen AUF-40 Teil B als offen — beide Hälften sind gebaut"
art: "BAU — überholte Begleittexte an einem erledigten Posten berichtigen. KEINE Zusage ändert sich."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: 59c66eb2
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
anlass: "Der Generator hat den Befund ohne Auftrag zu Ende gemessen (c767426d) und ausdrücklich NICHT
         geschnitten: 'Kein Blatt geschnitten, das ist Planner-Arbeit'. Ich habe die zuerst gefundenen sechs Stellen
         selbst nachgemessen, bevor ich sie in ein Kriterium schreibe."
grundlage: "resources/planner/hausplaner/ — SIEBEN Stellen: sechs selbst gemessen, die siebte
            vom plan-pruefer gefunden (2772c198) ·
            AUF-78 und AUF-81 als Bauherkunft · A-20 als Klassenvorbild"
```

## 1 — Der Befund: sieben Zettel, und sie meinen nicht dasselbe

**Selbst gemessen** (`grep -rn 'Teil B\|TEIL B' resources/planner/hausplaner/`):

```text
StartView.tsx:18            „Gefuellt wird sie in Teil B (Route + Controller, bei Yama)."
StartView.tsx:205           „Die echte Liste braucht eine Route und ist Teil B (bei Yama)."
studioDaten.ts:155          „Route — das ist Teil B und liegt bei Yama."
startEhrlich.test.ts:16     „ist Teil B — der liegt bei Yama."
startEhrlich.test.ts:118    TESTNAME: „Teil A hat weder Route noch Controller
                            beruehrt — das ist Teil B"
konfiguratorEhrlich.test.ts:11
                            „als AUF-40 Teil B stehen — nicht gestrichen, nur nicht dran."

NACHGETRAGEN nach 2772c198 — SIE TRAEGT DIE ZEICHENFOLGE NICHT:
startEhrlich.test.ts:120    „Die Zulieferung der Liste bleibt deshalb offen."
                            (im Rumpf des geschuetzten Tests, ohne die Worte Teil B)
```

> **Die siebte Stelle ist der Beleg gegen mein eigenes Suchmuster.** *Ich habe `Teil B` gesucht — also
> die **Schreibweise** — und `startEhrlich:120` behauptet dasselbe mit anderen Worten. **H-9 an meinem
> eigenen Befehl, in demselben Blatt, das H-9 zweimal am Posten diagnostiziert.** Gefunden hat sie der
> Plan-Prüfer, weil er nach der SACHE gesucht hat und nicht nach dem Wort.*

> **Fünf meinen die PROJEKTLISTE, eine meint die PAKETSPEICHERUNG** — *`konfiguratorEhrlich:11` ist die
> einzige, die von der zweiten Hälfte spricht. **Wer „Teil B" liest, weiß nicht, welche Hälfte gemeint
> ist**, und genau daran ist der Posten zweimal falsch gemessen worden. Der Release-Prüfer hat in
> `5e9c8b08` belegt, dass AUF-40 zwei Gegenstände in einem Posten führt; **dieser Auftrag zeigt, dass
> die Vermischung bis in die Kommentare reicht.***

**Beide Hälften sind gebaut** (Fundstellen aus `054eaa0b`, je selbst geöffnet):

```text
HAELFTE 1 Projektliste (AUF-78):
  HausplanerController.php:42 PROJEKTLISTE_MAX · :101 hausplanerProjekte · :55 hpProjekte
  objekt.blade.php:141 data-projekte · main.tsx:18 + :82 leseProjekte
  KEINE Route — Mount-Attribut, Controller :57: „kein Lade-Fetch aus der Insel"
HAELFTE 2 Paketspeicherung (AUF-81, Yamas Freigabe im Auftragskopf zitiert):
  web.php:5016 POST · :5018 GET Liste · :5020 GET einzeln, je permission:Hausplaner
  objekt.blade.php:144 data-pakete-url · main.tsx:89 setzePaketZiel
  paketSpeichern.ts:45 fetch · ConfigWizard.tsx:255 benutzt es
  MIT Route — aber ueber ein Attribut gereicht statt in der Insel gebaut
```

## 2 — Der tragende Punkt: `startEhrlich:118` ist ein richtiger Test mit falschem Namen

**Das ist die Stelle, an der dieser Auftrag Schaden anrichten kann, wenn er falsch verstanden wird.**

```text
Der Test heisst   „Teil A hat weder Route noch Controller beruehrt — das ist Teil B"
Er prueft         dass StartView.tsx weder fetch noch dataset benutzt
Gemessen          0 Treffer — die Zusage HAELT
```

> **Die Zusage ist richtig und SOLL halten** — *der Generator hat das vor dem Bau gemessen und im
> Bericht festgehalten: **die Naht läuft über `main.tsx`, nicht über `StartView`.** Dass StartView
> selbst kein `fetch` und kein `dataset` anfasst, ist die Architekturgrenze der Insel und kein
> Übergangszustand. **Überholt ist nur der Begleitsatz, nicht der Test.***

> **„Ein richtiger Test mit altem Kommentar ist etwas anderes als ein falscher Test"** *— der Satz ist
> vom Generator und er ist der Kern dieses Auftrags. **Wer beim Aufräumen den Test entfernt, weil sein
> Name überholt klingt, reißt einen Wächter ab, der eine gültige Grenze hält.***

## 3 — Scope

```text
A-23 IST   die SIEBEN Begleittexte: fuenf Kommentare, EIN Testname und EIN
           Kommentar im Rumpf eines Tests. Sie sagen
           heute, ein gebauter Posten sei offen und liege bei Yama.
           Und je Stelle die Angabe, WELCHE Haelfte gemeint ist.

A-23 IST NICHT
           irgendeine ZUSAGE. Kein Test wird geloescht, kein assert geaendert,
           keine Testzahl darf sinken.
           W-33s Blatt -> liegt beim Evaluator (Claim 63c474ff), wird NICHT
           angefasst; seine 7-GRENZEN traegt das Zitat samt Kennzeichnung schon.
           die Tafelzeile AUF-40 -> in 054eaa0b berichtigt.
           die Frage, ob AUF-40 damit GESCHLOSSEN ist -> das sagt Yama, nicht
           dieser Auftrag. Hier werden nur ueberholte Texte berichtigt.
           der Browser-Nachweis, ob die Liste ankommt -> ausdruecklich NICHT im
           Scope; alle Naehte sind GELESEN und keine ausgefuehrt.
```

## 4 — Abnahmekriterien

```text
A-23-1  (P1, TRAGEND) startEhrlich.test.ts:118 BEHAELT SEINE ZUSAGE. Der Test darf
        umbenannt werden, sein Rumpf NICHT geaendert und er darf NICHT entfernt
        werden. Nachweis: die Zusage vorher und nachher zeichengenau gleich, und
        die Testzahl der Datei vorher = nachher.
        BERICHTIGT nach 2772c198, weil meine erste Fassung mit A-23-2 KOLLIDIERTE:
        der md5 laeuft ueber den Rumpf OHNE KOMMENTARE, nicht ueber den Rohtext.
        Grund: startEhrlich.test.ts:120 traegt IM RUMPF des geschuetzten Tests den
        Satz 'Die Zulieferung der Liste bleibt deshalb offen' — heute falsch, und
        ohne die Zeichenfolge 'Teil B', deshalb stand er nicht in meinen ersten sechs.
        Wer ihn nach A-23-2 berichtigt, aendert den Rohtext-md5 und faellt an A-23-1;
        wer A-23-1 buchstaeblich nimmt, muss einen falschen Satz stehen lassen. Beides
        ist falsch, und die Kollision ist meine.
        DAS WERKZEUG DAFUER EXISTIERT IN DER INSEL: ohneKommentare steht in
        startEhrlich.test.ts:27 und konfiguratorEhrlich.test.ts:24. Der Nachweis
        benutzt dieselbe Funktion, die die Tests selbst verwenden — kein neues
        Verfahren, und deshalb auch keine neue Fehlerquelle.
        WAS DAMIT GESCHUETZT IST: die zwei assert.doesNotMatch-Zeilen (:121 und :122)
        auf fetch/axios/admin-Pfad und auf dataset. Sie sind die Zusage. Der
        Kommentar darueber ist es nicht.
        BEGRUENDUNG, damit niemand das Kriterium fuer Formalismus haelt: die Zusage
        ist RICHTIG und soll halten — StartView beruehrt weder fetch noch dataset,
        gemessen 0 Treffer, und das ist die Architekturgrenze der Insel, nicht ein
        Uebergangszustand. Die Naht laeuft ueber main.tsx. Wer den Test wegen seines
        Namens entfernt, reisst einen gueltigen Waechter ab.
A-23-2  (P1) Alle SIEBEN Stellen sind berichtigt, und JEDE nennt, WELCHE Haelfte sie
        meint — Projektliste oder Paketspeicherung. Fuenf meinen die erste,
        konfiguratorEhrlich.test.ts:11 die zweite.
        SIEBTE STELLE, nachgetragen nach 2772c198: startEhrlich.test.ts:120 'Die
        Zulieferung der Liste bleibt deshalb offen'. Sie traegt die ueberholte
        Aussage OHNE die Zeichenfolge 'Teil B' — mein Suchmuster hat sie deshalb
        nicht gefunden, obwohl sie dasselbe behauptet. H-9 an meinem eigenen
        Suchmuster: ich habe die SCHREIBWEISE gesucht und nicht die Sache. Sie wird
        mitberichtigt, und A-23-1 erlaubt das jetzt ausdruecklich.
        Der alte Wortlaut wird NICHT geloescht, sondern als ueberholt gekennzeichnet
        AN DERSELBEN STELLE. Das ist die Bedingung, die der plan-pruefer an W-33-5
        gesetzt hat (baa785a2), und sie gilt hier genauso: ein Satz, dessen
        Kennzeichnung einen Absatz weiter steht, wird spaeter als Beleg gelesen.
A-23-3  ACHTUNG, DIESES KRITERIUM HAT EINE FALLE, UND SIE IST MEINE: es verlangt die
        HERKUNFT, und die Herkunft von Haelfte 2 ist Yamas Tor 1 vom 26.07. Wer das
        in studioDaten.ts:155 mit dem naheliegenden Wort aufschreibt, laesst
        gefuehrteEhrlich fallen (siehe A-23-5). DIE HERKUNFT WIRD OHNE DIESES WORT
        GENANNT: Auftragsnummer und Datum genuegen — AUF-81, Tor 1, 26.07.
        (P1) Je Stelle steht die FUNDSTELLE des gebauten Wegs, nicht nur das Wort
        gebaut. Fuer die Projektliste: HausplanerController.php:101 -> :55 ->
        objekt.blade.php:141 -> main.tsx:82. Fuer die Paketspeicherung:
        web.php:5016/5018/5020 -> objekt.blade.php:144 -> main.tsx:89 ->
        paketSpeichern.ts:45 -> ConfigWizard.tsx:255.
        Ohne Fundstelle ist die Berichtigung nur ein anderes Wort und beim naechsten
        Zweifel wieder ungeprueft.
A-23-4  Der GRUND steht mindestens an einer der sieben Stellen: die Projektliste
        kommt ueber ein Mount-Attribut OHNE Lade-Fetch (Controller :57 sagt es
        woertlich), die Paketspeicherung ueber eine Route, die als Attribut gereicht
        wird. Wer 'Route' misst, findet im ersten Fall 0 und schliesst falsch; wer
        'fetch in der Insel' misst, findet im zweiten genau einen und haelt ihn fuer
        die Ausnahme. H-9 zweimal am selben Posten.
A-23-5  BERICHTIGT nach 2772c198, und der Befund ist schwerer als eine Zahl. Meine
        erste Fassung nannte DREI Ehrlichkeitswaechter, listete ZWEI und die zwei
        Genannten KOENNEN EINEN FEHLGRIFF NICHT FANGEN: startEhrlich liest ueber
        lies() mit ohneKommentare (:27-29), konfiguratorEhrlich ebenso (:24, :26,
        :117). A-23 aendert KOMMENTARE — beide sind dafuer blind. Eine Gegenprobe
        aus blinden Waechtern ist keine Gegenprobe.
        DER EINZIGE, DER GREIFEN KANN, STAND NICHT IM KRITERIUM:
        gefuehrteEhrlich.test.ts:30 liest studioDaten.ts ROH — die ohneKommentare-
        Funktion steht in derselben Datei (:28) und wird an dieser Stelle NICHT
        benutzt. Und :33-36 prueft, dass das Wort 'Frei'+'gegeben' (zusammengesetzt,
        damit ein grep es dort nicht findet) NULL Mal in der GANZEN Datei vorkommt,
        Kommentare eingeschlossen.
        DIE FALLE IST NICHT HYPOTHETISCH, UND SIE IST MEINE: A-23-3 verlangt die
        HERKUNFT des gebauten Wegs — und die Herkunft von Haelfte 2 ist Yamas Tor 1
        vom 26.07. Wer das in studioDaten.ts:155 mit dem naheliegenden Wort
        aufschreibt, laesst gefuehrteEhrlich fallen. Mein eigenes Kriterium treibt
        den Bauenden in einen roten Test.
        WAS ZU TUN IST: gefuehrteEhrlich MUSS GRUEN BLEIBEN, nachgewiesen durch LAUF
        und nicht durch Wortzaehlung. Die Zusage dahinter ist keine Formalie: der
        Test haelt fest, dass in dieser Datei KEIN Statuswort eine Freigabe
        behauptet. Wer die Herkunft nennen will, nennt sie ohne dieses Wort —
        Auftragsnummer und Datum genuegen (AUF-81, Tor 1, 26.07.).
        VOLLSTAENDIG, statt einer Zahl die Klassen: von den fuenf
        Ehrlichkeitswaechtern der Insel sind startEhrlich und konfiguratorEhrlich
        KOMMENTARBLIND, gefuehrteEhrlich ist KOMMENTAR-EMPFINDLICH auf studioDaten.ts,
        und fussleistenEhrlich sowie snapshotFlaecheEhrlich fassen keine der sieben
        Stellen an. Am Bau-Stand nachzaehlen — die Zuordnung, nicht die Zahl, ist
        das Pruefbare. Das ist die Lehre aus W-36-5.
A-23-5b Testzahl der Insel vorher = nachher, KEIN Test entfernt, keine Zusage
        geschwaecht. Vollstaendige Suite, Zaehler vorher und nachher im Bericht.
A-23-6  Die Fangprobe wird GEFAHREN und im Bericht belegt: eine der sieben
        Berichtigungen zuruecknehmen und zeigen, dass A-23-2 dann rot ist. Wird
        keine Probe gefahren, steht 'nicht gefahren' im Bericht — nicht Schweigen.
```

A-23-7  ENTSCHEIDUNG DES PLANNERS ZUR ACHTEN STELLE, 13.08. — und sie wirkt
        NICHT rueckwirkend. Der Generator hat sie gemessen und ausdruecklich NICHT
        berichtigt (STATUS.md, Block A-23): startEhrlich.test.ts:140 traegt in der
        assert-MELDUNG den Satz 'auch nicht ueber eine Naht, die es noch nicht
        gibt'. Die Naht GIBT es — main.tsx:18 fuehrt leseProjekte ein und
        main.tsx:82 liest mount.dataset. Der Satz ist ueberholt.
        SEINE KLEMME WAR ECHT und er hat richtig gehandelt: eine assert-Meldung
        steht im RUMPF, den A-23-1 per md5 schuetzt. Wer sie berichtigt, bricht den
        md5; wer A-23-1 einhaelt, laesst einen ueberholten Satz stehen.
        MEINE ENTSCHEIDUNG, dreifach begruendet und selbst gemessen:
        (1) A-23-1 schuetzt die ZUSAGE, nicht ihren Erklaertext. Die Zusage ist das
            Muster doesNotMatch(start, /dataset\./) — und sie ist RICHTIG: StartView
            benutzt kein dataset, die Naht laeuft ueber main.tsx.
        (2) Eine assert-Meldung ist SICHTBARER als ein Kommentar, nicht harmloser:
            sie erscheint im Testprotokoll, sobald der Test faellt. Ein ueberholter
            Satz dort ist gefaehrlicher als einer im Kommentar.
        (3) Die Aenderung ist risikofrei: KEIN Test prueft Meldungstexte — selbst
            gemessen, 0 Treffer ueber alle Testdateien.
        WAS DARAUS FOLGT: kuenftige Blaetter dieser Art messen den md5 ueber den
        Rumpf OHNE Kommentare UND OHNE Meldungstexte. Fuer A-23 gilt es als
        NACHTRAG nach der Abnahme und NICHT als Kriterium — der Bau ist an A-23-1
        in der geltenden Fassung gemessen, und wer das Kriterium mitten in der
        Abnahme erweitert, macht einen korrekten Bau nachtraeglich unvollstaendig.
        Der Generator hat die Stelle gemeldet statt sie stillschweigend zu
        beruehren; genau deshalb ist sie jetzt entschieden und nicht verwaist.

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT statt am Zustand** (Yamas E1),
**Fundstellen am Bau-Stand** (Pflichtprüfung 8).

```yaml
warum_spur_A_und_nicht_B: "Es sind Kommentare und ein Testname, also Text — nach dem Buchstaben Spur B.
        Ich stufe hoch, aus zwei Gruenden. ERSTENS beruehrt der Auftrag einen WAECHTER: startEhrlich:118
        ist ein Test, und wer dort den Namen aendert, ist eine Zeile von der Zusage entfernt. ZWEITENS
        gilt Zweifel-heisst-A, und der Zweifel ist hier benannt und nicht abstrakt — die
        naheliegendste Fehlhandlung dieses Auftrags ist, den Test als ueberholt zu LOESCHEN. Eine
        Kurzspur ohne Evaluator wuerde genau das nicht fangen."
was_dieser_auftrag_NICHT_entscheidet: "Ob AUF-40 Teil B geschlossen ist. Das ist Yamas Posten, und der
        release-pruefer hat ihn in dd0fee90 als gegenstandslos gemeldet. Dieser Auftrag raeumt nur die
        Zettel weg, die den falschen Stand behaupten — er schliesst keinen Auftrag."
was_ich_selbst_gemessen_habe_und_was_nicht: "Sechs der sieben Stellen: selbst gemessen, jede Zeile gelesen. Die
        Naht beider Haelften: die Projektliste habe ich in 054eaa0b Stelle fuer Stelle geoeffnet, die
        Paketspeicherung habe ich fuer die drei Routen und die Migration geoeffnet, aber
        paketSpeichern.ts:45 und ConfigWizard.tsx:255 habe ich vom Generator uebernommen und nur
        gegengelesen. Das steht hier, damit niemand meine Uebernahme fuer eine Messung haelt."
mein_beitrag_zum_befund_und_meine_grenze: "Der Befund ist der des Generators, und er hat ihn zweimal
        erweitert: bei W-33 nannte er drei Stellen, dann sechs, weil sein Suchbereich zuerst die eine
        Datei war, die er las. Meine Aufgabe war nicht, ihn zu wiederholen, sondern das zu tun, was er
        ausdruecklich nicht getan hat: schneiden. Und die eine Sache, die ich dabei ergaenzt habe, ist
        A-23-1 — dass die naheliegendste Fehlhandlung das LOESCHEN eines richtigen Tests ist. Der
        Generator hat den Fall benannt, aber er stand in keinem Kriterium."
zwei_fehler_in_meinen_eigenen_kriterien_2772c198: "ERSTENS eine KOLLISION: A-23-1 schuetzte den
        Rumpf per md5 ueber den Rohtext, A-23-2 verlangt die Berichtigung ueberholter Saetze — und in
        genau diesem Rumpf steht einer (startEhrlich:120). Wer das eine erfuellt, faellt am anderen.
        Behoben, indem der md5 ueber den Rumpf OHNE KOMMENTARE laeuft, mit dem Werkzeug der Insel selbst.
        ZWEITENS eine GEGENPROBE AUS BLINDEN WAECHTERN: ich nannte startEhrlich und konfiguratorEhrlich
        als Absicherung, und beide lesen mit ohneKommentare. Sie koennen einen Fehlgriff an Kommentaren
        nicht fangen. Der einzige, der es kann, stand nicht drin — und mein A-23-3 treibt den Bauenden
        sogar genau in dessen Wortsperre hinein. Der plan-pruefer hat beides vor dem Bau gefunden; ohne
        ihn haette der Bauende einen roten Test bekommen und nicht gewusst, dass mein Auftrag ihn dorthin
        geschickt hat."
was_ich_daraus_fuer_kuenftige_kriterien_mitnehme: "Ein Kriterium, das einen WAECHTER als Gegenprobe
        nennt, muss sagen WORAUF der Waechter empfindlich ist — nicht nur dass er existiert. Bei
        Textaenderungen ist die Frage kommentarblind oder nicht, und sie ist an einer Zeile ablesbar:
        liest der Test roh oder ueber ohneKommentare. Das ist dieselbe Klasse wie H-8, Ort ist nicht
        Wirkung: ein Waechter, der die Datei anfasst, bewacht nicht automatisch die Aenderung."
A_23_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

---

## 5 — Votum des Evaluators (§11)

**NACHBESSERN**, Fehlerklasse **`CODE`** (§12.1), Ball beim **Generator**. Bau `3ad920b1`, Elter
`4e590f74` (als git-Elter nachgemessen), zwei Prüfstände mit `node_modules` und `vendor`.

**Der Befund ist klein und er ist trotzdem einer: ein Zitat, das Wörtlichkeit behauptet und keine
liefert.** `startEhrlich.test.ts:129` schreibt *„hier stand: … liegt hinter Yamas **Entscheidung**"*
— im Elter stand *„liegt hinter Yamas **Freigabe**"*. Dreimal gemessen, zuletzt am Commit-Diff
selbst (E1):

```text
git show 3ad920b1 -- .../startEhrlich.test.ts
  -  // hinter Yamas Freigabe. Die Zulieferung der Liste bleibt deshalb offen.
  +  // alles, was `routes/` … liegt hinter Yamas Entscheidung. Die Zulieferung
```

**Warum das nicht Formalismus ist.** A-23-2 verlangt wörtlich, *der alte Wortlaut wird NICHT
gelöscht, sondern als überholt gekennzeichnet* — und begründet es mit der W-33-5-Bedingung: ein
Satz wird später **als Beleg gelesen**. Ein Zitat in der Form *„Hier stand: …"* ist genau dieser
Beleg. Wer künftig prüft, was hier gegolten hat, bekommt ein Wort, das nie dastand. Das ist die
wiederkehrende Fehlerklasse in ihrer leisesten Form: **die Kennzeichnung trägt die Form eines
wörtlichen Zitats und ist keins** — in einem Auftrag, der von Belegtreue handelt.

**Kein Wächter erzwingt die Änderung.** `gefuehrteEhrlich:33-36` verbietet `'Frei'+'gegeben'` in
`studioDaten.ts` und `GuidedView.tsx` — nicht in dieser Datei und nicht das Wort „Freigabe", das in
der Insel **58×** vorkommt. Der Bericht sagt bei `:53` ausdrücklich *„Der alte Wortlaut steht
**überall** noch da, als Zitat"*; gemessen sind **5 von 6** Zitaten wörtlich.

**Die Behebung ist ein Wort und bricht nichts** — selbst gefahren, mit Anker und md5-Rücksetzung:

```text
'Entscheidung' -> 'Freigabe':  md5(Rumpf ohne Kommentare) c8e514e0d2ae399ffa8b4fa1baae0114
                               = der A-23-1-Anker, UNVERAENDERT (der Satz steht in einem //-Kommentar)
                               a23-sieben-stellen.mjs: 7 von 7 belegt
                               startEhrlich.test.ts:    9 tests, 9 pass, 0 fail
```

**Alles Übrige trägt.** Der Messtisch vollständig:

| Kriterium | Befund | Wie ich es selbst gemessen habe |
|---|---|---|
| **A-23-1** (TRAGEND) | **grün** | md5 des Rumpfs **ohne Kommentare** an Bau und Elter identisch (`c8e514e0…`, mit derselben `ohneKommentare`-Funktion wie die Insel). Testzahl der Datei **9 = 9**. Der Name ist geändert — das erlaubt das Kriterium; die zwei `assert.doesNotMatch`-Zeilen sind zeichengleich |
| **A-23-2** | **ROT im Belegteil, grün im Rest** | Sieben Stellen, jede berichtigt, jede nennt ihre Hälfte (sechs Projektliste, `konfiguratorEhrlich:12` als einzige Paketspeicherung). „An derselben Stelle" selbst nachgemessen: Abstand Marke → Zitat **0–3 Zeilen** (max 306 Zeichen). **Aber 1 von 6 Zitaten nicht wörtlich** — siehe oben |
| **A-23-3** | **grün** | Jede Fundstelle **geöffnet**, nicht abgeschrieben: `HausplanerController.php:101/:55/:57` · `objekt.blade.php:141` (`data-projekte`) `:144` (`data-pakete-url`) · `main.tsx:82/:89` · `web.php:5016/5018/5020` · `paketSpeichern.ts:45` (`await fetch`) · `ConfigWizard.tsx:255`. **Alle stimmen.** Die Herkunft steht ohne das Wort, das `gefuehrteEhrlich` verbietet: „AUF-81, Tor 1, 26.07." |
| **A-23-4** | **grün** | Der Grund steht an drei Stellen, und `HausplanerController.php:57` sagt ihn wörtlich: *„dieselbe Naht wie `hpProjekte`, kein Lade-Fetch aus der Insel"* |
| **A-23-5** | **grün** | `gefuehrteEhrlich` **gefahren**, nicht wortgezählt: 8 tests, 8 pass, 0 fail an Bau **und** Elter. Das verbotene Wort selbst gezählt: **0** in `studioDaten.ts`, **0** in `GuidedView.tsx` |
| **A-23-5b** | **grün** | Insel-Suite **1718/1718 an beiden Ständen** — kein Test entfernt, keiner dazu |
| **A-23-6** | **grün** | Fangprobe selbst gefahren: ÜBERHOLT-Marke in `studioDaten.ts` entfernt (Anker genau 1×) → **6 von 7**, `[überholt NEIN]`. md5 danach zurückgesetzt, identisch |

**Ein Nebenbefund am Werkzeug, nicht am Bau.** `scripts/a23-sieben-stellen.mjs:32` sucht die Marke
im Umkreis von **±1200 Zeichen** — etwa zwölf Zeilen. Damit würde es eine Kennzeichnung, die „einen
Absatz weiter" steht, für gültig halten; genau den Fall, den A-23-2 ausschließt. **Der Bau braucht
diese Nachsicht nicht** (gemessen 0–3 Zeilen), das Werkzeug wäre nur beim nächsten Mal zu milde.

**Meine eigenen Messfehler in dieser Runde**, alle vor dem Urteil gefunden:

1. `grep --include=*.ts` **ohne Anführungszeichen** — zsh hat das Glob geschluckt und **0 Treffer**
   gemeldet. Hätte ich die Null genommen, stünde hier „keine der sieben Stellen existiert".
2. Ein 8-Zeilen-Fenster **ab** der Fundstelle sollte messen, welche Hälfte jede Stelle nennt — die
   ÜBERHOLT-Marke steht aber **davor**. Es meldete `konfiguratorEhrlich` als „Projektliste", obwohl
   sie die Paketspeicherung meint. Berichtigt, indem ich die Stellen **gelesen** habe (B6).
3. Mein Zitat-Regex suchte das schließende `“` (U+201C); dort steht ein ASCII-`"` (U+0022). Er fand
   nichts — leer statt falsch, aber wieder ein zu enges Muster.
4. **Zwei Pfade geraten statt gesucht:** `views/hausplaner/objekt.blade.php` (liegt unter
   `views/admin/hausplaner/`) und `app/paketSpeichern.ts` (liegt unter `app/state/`). Beide Male
   meldete `sed` „No such file" — beinahe ein Fehlbefund „die Fundstelle existiert nicht" gegen eine
   Kette, die vollständig stimmt. **Derselbe Griff wie bei A-24; er ist mir jetzt zweimal passiert.**
5. `set -- $p` in einer `for`-Schleife greift unter zsh nicht wie erwartet — beide Prüfstände
   entstanden zunächst gar nicht.

**§15 belegt:** keine Datenbankschreibung in dieser Abnahme; die Suite läuft gegen die Insel, nicht
gegen eine Datenbank.

**Weiter an den Generator**, Umfang **genau dieser eine Befund** (§12.2: keine Nebenreparaturen).
