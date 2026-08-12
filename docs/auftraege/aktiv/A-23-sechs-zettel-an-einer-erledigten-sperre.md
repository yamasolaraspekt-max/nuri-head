# A-23 — Sechs Zettel an einer erledigten Sperre. Und einer davon ist ein Testname

```yaml
auftrag: "A-23"
titel: "Sechs Stellen in der Insel führen AUF-40 Teil B als offen — beide Hälften sind gebaut"
art: "BAU — überholte Begleittexte an einem erledigten Posten berichtigen. KEINE Zusage ändert sich."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: 59c66eb2
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
anlass: "Der Generator hat den Befund ohne Auftrag zu Ende gemessen (c767426d) und ausdrücklich NICHT
         geschnitten: 'Kein Blatt geschnitten, das ist Planner-Arbeit'. Ich habe die sechs Stellen
         selbst nachgemessen, bevor ich sie in ein Kriterium schreibe."
grundlage: "resources/planner/hausplaner/ — sechs Stellen, selbst gemessen ·
            AUF-78 und AUF-81 als Bauherkunft · A-20 als Klassenvorbild"
```

## 1 — Der Befund: sechs Zettel, und sie meinen nicht dasselbe

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
```

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
A-23 IST   die SECHS Begleittexte: fuenf Kommentare und EIN Testname. Sie sagen
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
        werden. Nachweis: die Zusage vorher und nachher zeichengenau gleich
        (Rumpf per md5 vor und nach der Aenderung, beide Werte im Bericht), und
        die Testzahl der Datei vorher = nachher.
        BEGRUENDUNG, damit niemand das Kriterium fuer Formalismus haelt: die Zusage
        ist RICHTIG und soll halten — StartView beruehrt weder fetch noch dataset,
        gemessen 0 Treffer, und das ist die Architekturgrenze der Insel, nicht ein
        Uebergangszustand. Die Naht laeuft ueber main.tsx. Wer den Test wegen seines
        Namens entfernt, reisst einen gueltigen Waechter ab.
A-23-2  (P1) Alle SECHS Stellen sind berichtigt, und JEDE nennt, WELCHE Haelfte sie
        meint — Projektliste oder Paketspeicherung. Fuenf meinen die erste,
        konfiguratorEhrlich.test.ts:11 die zweite.
        Der alte Wortlaut wird NICHT geloescht, sondern als ueberholt gekennzeichnet
        AN DERSELBEN STELLE. Das ist die Bedingung, die der plan-pruefer an W-33-5
        gesetzt hat (baa785a2), und sie gilt hier genauso: ein Satz, dessen
        Kennzeichnung einen Absatz weiter steht, wird spaeter als Beleg gelesen.
A-23-3  (P1) Je Stelle steht die FUNDSTELLE des gebauten Wegs, nicht nur das Wort
        gebaut. Fuer die Projektliste: HausplanerController.php:101 -> :55 ->
        objekt.blade.php:141 -> main.tsx:82. Fuer die Paketspeicherung:
        web.php:5016/5018/5020 -> objekt.blade.php:144 -> main.tsx:89 ->
        paketSpeichern.ts:45 -> ConfigWizard.tsx:255.
        Ohne Fundstelle ist die Berichtigung nur ein anderes Wort und beim naechsten
        Zweifel wieder ungeprueft.
A-23-4  Der GRUND steht mindestens an einer der sechs Stellen: die Projektliste
        kommt ueber ein Mount-Attribut OHNE Lade-Fetch (Controller :57 sagt es
        woertlich), die Paketspeicherung ueber eine Route, die als Attribut gereicht
        wird. Wer 'Route' misst, findet im ersten Fall 0 und schliesst falsch; wer
        'fetch in der Insel' misst, findet im zweiten genau einen und haelt ihn fuer
        die Ausnahme. H-9 zweimal am selben Posten.
A-23-5  Testzahl der Insel vorher = nachher, KEIN Test entfernt, keine Zusage
        geschwaecht. Gegenprobe: die drei Ehrlichkeitswaechter, die betroffene
        Dateien anfassen (startEhrlich, konfiguratorEhrlich), laufen gruen.
A-23-6  Die Fangprobe wird GEFAHREN und im Bericht belegt: eine der sechs
        Berichtigungen zuruecknehmen und zeigen, dass A-23-2 dann rot ist. Wird
        keine Probe gefahren, steht 'nicht gefahren' im Bericht — nicht Schweigen.
```

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
was_ich_selbst_gemessen_habe_und_was_nicht: "Die sechs Stellen: selbst gemessen, jede Zeile gelesen. Die
        Naht beider Haelften: die Projektliste habe ich in 054eaa0b Stelle fuer Stelle geoeffnet, die
        Paketspeicherung habe ich fuer die drei Routen und die Migration geoeffnet, aber
        paketSpeichern.ts:45 und ConfigWizard.tsx:255 habe ich vom Generator uebernommen und nur
        gegengelesen. Das steht hier, damit niemand meine Uebernahme fuer eine Messung haelt."
mein_beitrag_zum_befund_und_meine_grenze: "Der Befund ist der des Generators, und er hat ihn zweimal
        erweitert: bei W-33 nannte er drei Stellen, jetzt sechs, weil sein Suchbereich zuerst die eine
        Datei war, die er las. Meine Aufgabe war nicht, ihn zu wiederholen, sondern das zu tun, was er
        ausdruecklich nicht getan hat: schneiden. Und die eine Sache, die ich dabei ergaenzt habe, ist
        A-23-1 — dass die naheliegendste Fehlhandlung das LOESCHEN eines richtigen Tests ist. Der
        Generator hat den Fall benannt, aber er stand in keinem Kriterium."
A_23_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
