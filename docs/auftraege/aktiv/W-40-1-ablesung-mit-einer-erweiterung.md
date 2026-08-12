# W-40/1 — Nachbesserung: W-40 ist eine Ablesung mit EINER Erweiterung, und der Träger ist das Paket

```yaml
auftrag: "W-40/1"
werkzeug: "W-40 Gültigkeitsstatus"
art: "NACHBESSERUNG nach §12. W-40 ist BETRIEBSBESTAETIGT und inhaltlich überholt: Yamas
      Entscheidung vom 12.08. ordnet drei der vier Stufen dem vorhandenen Code zu und benennt
      NUR blocked als Erweiterung. Das Blatt gibt vor, was zu drei Vierteln existiert."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 2e7504ec
prioritaet: P1
anlass: "Yamas Antwort auf beide Fachfragen, eingetragen in 2e7504ec. Der Release-Prüfer hat den
         Ball ausdrücklich an mich gegeben: 'der Reifegrad ENTWORFEN trägt nach Yamas Entscheidung
         nicht mehr … ich ändere die Registerzeile NICHT selbst: das Register ist seine Arbeit.'"
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "Yamas Entscheidung 12.08. (STATUS.md, W-40-Datensatz) · geometry/configuratorPackage.ts
            · W-40s sieben Blätter als nachzubessernder Gegenstand"
```

## 1 — Was Yama entschieden hat, und meine Belege dazu selbst nachgemessen

```text
ENTSCHEIDUNG 1   review-required ist KEINE Zahlenluecke: die vier und die drei liegen
                 nicht auf derselben Achse, 4+3 muss nicht 8 ergeben.
                 review-required  = checked    (existiert, samt Uebergaengen)
                 confirmed        = approved   (existiert, kannIntegrieren prueft darauf)
                 outdated         = outdated   (existiert, markiereVeraltet setzt es)
                 blocked          = DIE EINZIGE Erweiterung, 0 Treffer
  -> W-40 ist eine ABLESUNG MIT EINER ERWEITERUNG und keine Vorgabe.

SELBST NACHGEMESSEN, jede Stelle geoeffnet:
  configuratorPackage.ts:107   checked: ['draft', 'approved', 'generated']
                               -> der einzige Weg nach approved fuehrt ueber checked
  :120-122                     kannIntegrieren(paket) => paket.status === 'approved'
  'blocked' im ganzen Hausplaner   0 Treffer
```

## 2 — Der Träger ist das PAKET, und W-40s Blatt sagt SCHRITT

**Die überholte Stelle, wörtlich aus `2-FUNKTION.md`:**

```text
RICHTIG  zwei Felder nebeneinander:
           fortschritt: SchrittStatus        (W-38, gebaut)
           gueltigkeit: Gueltigkeitsstatus   (W-40, Vorgabe)
         -> ein Schritt kann ok UND confirmed sein, oder ok UND outdated.
```

> **Nach Yamas Zuordnung hängt die Gültigkeitsachse am PAKET, nicht am Schritt.** *Der
> Release-Prüfer hat es benannt und dabei seine eigene frühere Ablesung zurückgezogen: **die
> Messung steht** — die beiden Träger sind im Code getrennt, Import-Zähler 0 in beide Richtungen —
> **aber der Schluss trägt nicht mehr**, weil Yama `confirmed` dem `approved` zuordnet und damit der
> Achse am Paket.*

**Und daraus folgt das Baurisiko, wörtlich von ihm:** *„Wer das Blatt liest und baut, ohne diese
Zeile zu kennen, baut die Achse ein **zweites Mal am falschen Träger** — und das wäre die zweite
Wahrheit, die es heute noch nicht gibt. Der Satz gehört ins Blatt, **bevor** gebaut wird."*

## 3 — Yamas zwei Auflagen für den Bau, und seine Namenswarnung

```text
AUFLAGE 1   blocked traegt seinen GRUND mit.
            Ein blocked ohne blockiert_durch ist eine Absage ohne Erklaerung.

AUFLAGE 2   blocked wird NIE von Hand gesetzt oder geloest.
            Wer das will, meint DECISION_BLOCKED und gehoert in die Rollenkette
            statt ins Modell.

DIE UNTERSCHEIDUNG, die beides traegt — WORAUF gewartet wird:
  DECISION_BLOCKED   wartet auf einen MENSCHEN · Ebene Prozess · Ort STATUS.md
                     Aufhebung NUR durch Yamas Entscheidung, nie maschinell
  blocked            wartet auf eine BEDINGUNG · Ebene Produkt · Ort Gebaeudemodell
                     Adressat das naechste Werkzeug
                     Aufhebung AUTOMATISCH, sobald die Vorbedingung messbar erfuellt ist

Yamas Beispiel: PV-Belegung auf einer Dachflaeche ohne bestaetigte Geometrie —
niemand entscheidet etwas, die Sperre faellt von selbst, wenn die Geometrie approved ist.
```

> **Seine Namenswarnung ist der Kern der zweiten Auflage:** *zwei Zustände, die beide „blockiert"
> heißen und **gegensätzlich** aufgelöst werden, werden verwechselt. **Beim Bau trägt `blocked` im
> Blatt und im Code den Satz mit: „nicht `DECISION_BLOCKED`, dieser hier löst sich ohne mich."***

## 4 — Der Reifegrad folgt dem Blatt, nicht umgekehrt

```text
HEUTE    Registerzeile 127 traegt ENTWORFEN.
         Yamas Entscheidung sagt: Ablesung mit einer Erweiterung.
         BESCHRIEBEN waere aber HEUTE falsch, denn das Blatt liest nichts ab —
         es gibt vor.

DESHALB  die Registerzeile bleibt ENTWORFEN, BIS das Blatt tatsaechlich abliest.
         Sie wird als LETZTER Schritt dieses Auftrags nachgezogen, nicht als erster.
         Wer sie vorher aendert, behauptet eine Ablesung, die es nicht gibt.
```

*Ich habe den Reifegrad in `5028375e` ausdrücklich nicht geändert, bis Yamas Antwort da ist. **Sie
ist da — und sie sagt, dass zuerst das Blatt zu ändern ist.** Der Reifegrad ist die Folge, nicht die
Ursache.*

## 5 — Abnahmekriterien

```text
W-40/1-1  (P1, TRAGEND) BERICHTIGT nach dem Befund des Generators (ea418041): hier
          stand 'die ueberholte Stelle in 2-FUNKTION' — SINGULAR. Es sind DREIZEHN
          Stellen in VIER Blaettern, und wer eine berichtigt, haelt zwoelf
          Widersprueche fest, die alle belegt aussehen. Das ist H-8.
          SEINE LISTE, von ihm je mit Zeile gemessen:
            Entscheidung 1 (review-required) — NEUN Stellen:
              3-FORMELN:33 · 6-PRUEFUNG:12 · 7-GRENZEN:48, :54, :56, :61, :65-66, :106
            Entscheidung 2 (blocked gegen DECISION_BLOCKED) — FUENF Stellen:
              2-FUNKTION:18 · 6-PRUEFUNG:13 · 7-GRENZEN:73-74, :107
          ZWEI ZAEHLWEISEN, und die inhaltliche gilt: mein Wortmuster auf
          review-required oder DECISION_BLOCKED findet 9 Treffer (2-FUNKTION 1,
          6-PRUEFUNG 2, 7-GRENZEN 6). Seine 13 zaehlt auch Stellen, die die Sache
          ohne diese Woerter tragen — etwa 3-FORMELN:33 mit der Rechnung 4+3. Wer nach
          dem Wort sucht, findet die Rechnung nicht. Gezaehlt wird am BAU-STAND und
          nach INHALT, nicht nach Muster.
          Der TRAEGER ist dabei zu berichtigen: die Gueltigkeitsachse haengt am PAKET
          (ConfiguratorPackage), nicht am Schritt.
          KEINE Stelle wird geloescht, jede wird als ueberholt gekennzeichnet, mit
          Yamas Zuordnung und dem Datum — ein nachtraeglich umgeschriebenes Blatt ist
          kein Beleg mehr (A-20-4).
W-40/1-1b (P1, DER GEFAEHRLICHSTE TEIL) Die KRITERIEN K-3 und K-4 in 6-PRUEFUNG sind
          mitzuberichtigen. Sie lauten heute woertlich: K-3 'Die Zahlenluecke ist
          gestellt, nicht beantwortet' mit dem Fehlerfall 'eine Erklaerung fuer
          review-required erfinden', und K-4 'blocked gegen DECISION_BLOCKED als
          OFFENE FRAGE' mit dem Fehlerfall 'eine Abgrenzung behaupten'.
          YAMAS ANTWORT IST DIE ERKLAERUNG UND IST DIE ABGRENZUNG. Wer K-3 und K-4
          stehen laesst, verlangt vom naechsten Bauenden, Yamas Entscheidung zu
          IGNORIEREN — und ein Blatt, dessen Kriterien der eigenen Vorgabe
          widersprechen, ist an dieser Stelle unbaubar. Beide Kriterien werden
          umgestellt: von 'Frage stellen' auf 'Yamas Antwort tragen, mit Fundstelle'.
W-40/1-2  (P1) Die DREI vorhandenen Stufen sind als ABLESUNG beschrieben, je mit
          Fundstelle am Bau-Stand: review-required als checked, confirmed als approved
          mit kannIntegrieren, outdated als outdated mit markiereVeraltet.
W-40/1-3  (P1) blocked ist als EINZIGE Erweiterung gekennzeichnet, mit der Messung
          0 Treffer. Alles andere ist Ablesung.
W-40/1-4  (P1) Yamas ZWEI AUFLAGEN stehen im Blatt: blocked traegt blockiert_durch;
          blocked wird nie von Hand gesetzt oder geloest. Beide woertlich.
W-40/1-5  (P1) Die Unterscheidung blocked gegen DECISION_BLOCKED steht in 7-GRENZEN
          mit allen vier Merkmalen: worauf gewartet wird, Ebene, Ort, Aufhebung. Dazu
          Yamas Namenswarnung und der mitzufuehrende Satz.
W-40/1-6  Zwei Pfadangaben aus Yamas Belegen sind im Blatt korrekt: statusAus liegt in
          app/dashboard/fahrschritte.ts (der dashboard-Teil fehlte), und der
          Uebergangsblock beginnt bei :103 und nicht bei :101. Der INHALT beider
          Angaben stimmt — nur die Fundstelle wird geradegezogen.
W-40/1-7  Die REGISTERZEILE wird als LETZTER Schritt nachgezogen, wenn das Blatt
          abliest: von ENTWORFEN auf BESCHRIEBEN. Nachweis: die Zeile vorher und
          nachher, und der Satz, dass die Erweiterung blocked darin genannt ist.
W-40/1-8  Kein Produktivcode. Diese Nachbesserung aendert Blaetter und die
          Registerzeile — nicht configuratorPackage.ts und nicht studioDaten.ts.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit geöffneter Stelle** (Pflichtprüfung 7).

```yaml
warum_P1: "Das Blatt gibt heute vor, was zu drei Vierteln existiert, und es haengt die Achse an den
        falschen Traeger. Wer danach baut, erzeugt die zweite Wahrheit, die es heute noch nicht gibt —
        der Release-Pruefer hat das gemessen und ausdruecklich gesagt, der Satz gehoere ins Blatt
        BEVOR gebaut wird. Solange W-40 so steht, ist jeder Bau darauf riskant."
mein_anteil: "Ich habe W-40 als Vorgabe geschnitten und dabei die Praemisse kein Code aus der Quelle
        UEBERNOMMEN statt sie zu messen — H-6, und heute der vierte Fall dieser Klasse. Der Traeger
        ist die zweite Haelfte desselben Fehlers: ich habe die Achse an den Schritt gehaengt, weil
        W-38s SchrittStatus dort haengt, ohne zu pruefen woran die VORHANDENE Achse haengt."
was_dieser_auftrag_NICHT_tut: "Er baut nichts und er loescht nichts. Die ueberholte Stelle bleibt als
        Beleg stehen und wird gekennzeichnet — dieselbe Form, die der Generator bei W-23 vorgemacht
        hat und die A-20-4 verlangt."
W_40_1_nimmt_den_paragraf3_platz: "Sobald gezogen: IN_ARBEIT. §3 steht bei 0."
```


## §11 — Votum W-40/1 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-40/1"
votum: ABGENOMMEN
geprueft_an: "53142fc2"
elter: "5e9c8b08"
scope_diff: "9 Dateien, +633/-133: alle sieben W-40-Blaetter, REGISTER.md, Bericht neu.
  0 Code-Dateien — W-40/1-8 erfuellt, configuratorPackage.ts und studioDaten.ts unberuehrt."
pruefstand: "git worktree add -q --detach auf 53142fc2. Reine Blattarbeit — Suite nicht
  einschlaegig, §15 gegenstandslos, Browserabnahme entfaellt."
mein_eigener_anteil_am_anlass: "Dieser Auftrag entsteht aus MEINEM Befund: ich habe W-40 mit
  sieben von sieben abgenommen und dabei gemeldet, dass die Praemisse 'kein Code' nicht traegt.
  Und ich habe damals ausdruecklich NICHT bestaetigt, dass approved die Rolle von confirmed
  spielt — das war eine Einordnung des Generators. Yama hat sie inzwischen entschieden."

messtisch:

  W_40_1_1_dreizehn_stellen_in_vier_blaettern:
    urteil: ERFUELLT
    die_stellen_selbst_geoeffnet: "Ich habe die zwoelf genannten Positionen am ELTER 5e9c8b08
      einzeln aufgeschlagen — 3-FORMELN:33, 6-PRUEFUNG:12 und :13, 7-GRENZEN:48, :54, :56, :61,
      :65, :73, :106, :107, 2-FUNKTION:18. Jede traegt tatsaechlich die ueberholte Aussage.
      Die Liste des Generators trifft."
    KEINE_STELLE_GELOESCHT: "Das ist der Kern des Kriteriums, und ich habe ihn haert geprueft:
      der Diff entfernt SIEBEN Zeilen mit 'review-required' oder 'DECISION_BLOCKED'. Eine
      Entfernung im Diff ist aber noch keine Loeschung — ich habe jede der sieben im Bau
      aufgesucht:
        K-3 und K-4       umgestellt, mit dem ALTEN Wortlaut daneben zitiert (6-PRUEFUNG:19-21)
        die zwei Yama-Fragen  stehen weiter in der Tabelle, um eine dritte Spalte
                          'Stand 12.08.' erweitert (7-GRENZEN:181-182): BEANTWORTET
        2-FUNKTION:18     der Satz steht jetzt bei :41 als Zitat, mit Yamas Antwort daneben
        7-GRENZEN:73-74   woertlich bei :134-136: 'Was hier zuvor stand — ueberholt, nicht
                          geloescht', mit dem alten Wortlaut in Anfuehrungszeichen
      Keine der sieben ist ersatzlos verschwunden. A-20-4 gewahrt."
    kennzeichnungen_gezaehlt: "Alle sieben Blaetter tragen Kennzeichnungen (3/9/1/5/6/9/9 Zeilen);
      allein in den vier betroffenen Blaettern 28."
    der_traeger_ist_berichtigt: "2-FUNKTION traegt jetzt die Achse am PAKET statt am Schritt —
      genau der zweite Teil des Fehlers, den das Blatt selbst als 'mein_anteil' benennt."

  W_40_1_1b_K3_und_K4_umgestellt:
    urteil: ERFUELLT
    warum_das_der_gefaehrlichste_teil_war: "Stehen gelassen haetten die beiden Kriterien vom
      naechsten Bauenden verlangt, Yamas Entscheidung zu IGNORIEREN — ein Blatt, dessen Kriterien
      der eigenen Vorgabe widersprechen, ist unbaubar."
    gemessen: "6-PRUEFUNG:14 traegt 'K-3 (umgestellt) — Das Blatt traegt Yamas Aufloesung:
      review-required IST checked', :15 'K-4 (umgestellt) — Das Blatt traegt Yamas Abgrenzung:
      DECISION_BLOCKED wartet auf einen MENSCHEN, blocked auf eine BEDINGUNG'. Und :19-21 nennt
      beide alten Fassungen woertlich: 'K-3 und K-4 sind UMGESTELLT, nicht gestrichen'."

  W_40_1_2_drei_stufen_als_ablesung:
    urteil: ERFUELLT
    mit_fundstelle_am_bau_stand: "2-FUNKTION:10-12 ordnet zu: review-required -> checked
      (configuratorPackage.ts:26, Uebergang :107), confirmed -> approved (:26) mit
      kannIntegrieren (:120), outdated -> outdated (:26) mit markiereVeraltet (:125-128).
      Damit ist die Einordnung, die ich in meinem W-40-Votum ausdruecklich NICHT bestaetigt
      habe, jetzt als Yamas Entscheidung getragen — nicht mehr als Vermutung des Bauenden."

  W_40_1_3_blocked_als_einzige_erweiterung:
    urteil: ERFUELLT
    selbst_gegengeprobt: "2-FUNKTION:6 'blocked ist die EINZIGE Erweiterung', :13 mit der Messung.
      Meine eigene Gegenprobe im Produktivcode: 'blocked' 0 Treffer. Die drei anderen Stufen sind
      als Ablesung belegt, blocked als einziges als Vorgabe."

  W_40_1_4_yamas_zwei_auflagen_woertlich:
    urteil: ERFUELLT
    beide_im_wortlaut: "7-GRENZEN:110ff: 'blocked traegt seinen Grund mit, denn ein blocked ohne
      blockiert_durch ist eine Absage ohne Erklaerung.' und 'blocked wird NIE von Hand gesetzt
      oder geloest, wer das will meint DECISION_BLOCKED.' Beide als Zitat gekennzeichnet.
      Und die Verbindung zu W-41 steht daneben: dort outdated mit Grund, hier blocked mit
      blockiert_durch — beide aus demselben Satz."

  W_40_1_5_vier_merkmale:
    urteil: ERFUELLT
    beleg: "7-GRENZEN:95-105 als Tabelle: worauf gewartet wird (Mensch gegen Bedingung), Ebene
      (Prozess gegen Produkt), Ort (docs/STATUS.md gegen Gebaeudemodell), Aufhebung (nur Yama
      gegen automatisch). Dazu ein fuenftes Merkmal (Adressat) und Yamas Beispiel."

  W_40_1_6_zwei_pfadangaben_berichtigt:
    urteil: ERFUELLT
    beide_gegengeprobt: "5-CODE/LIESMICH:98-104 fuehrt beide mit genannt/richtig.
      Ich habe sie an der Quelle nachgemessen: statusAus liegt tatsaechlich in
      app/dashboard/fahrschritte.ts, und dort bei :43 — der dashboard-Teil fehlte.
      STATUS_UEBERGAENGE beginnt bei :103 (nicht :101), der Block endet bei :111.
      Beide Angaben des Blattes treffen zeichengenau."
    der_satz_der_dazugehoert: "'Ein falscher Pfad kostet den Naechsten die Suche, und wer nicht
      findet was belegt ist, haelt es fuer unbelegt. Genau so ist die Praemisse kein Code
      entstanden.' Das ist die richtige Einordnung meines eigenen W-40-Befunds."

  W_40_1_7_registerzeile:
    urteil: ERFUELLT
    diff_geoeffnet: "ENTWORFEN -> BESCHRIEBEN, und der Titel ist mitberichtigt: aus
      'confirmed · outdated · blocked' wird 'checked · approved · outdated + blocked' — die
      GEBAUTEN Namen plus die eine Erweiterung, statt der Zielbild-Namen."

  W_40_1_8_kein_produktivcode:
    urteil: ERFUELLT
    am_commit_gemessen: "0 Code-Dateien im Bau-Diff; configuratorPackage.ts und studioDaten.ts
      mit 0 Treffern. Die Fertigmeldung liegt in einem EIGENEN Commit (aedc9d27) und aendert nur
      docs/STATUS.md — Bau und Meldung sind getrennt."

meine_eigenen_messfehler_in_dieser_runde:
  - "ICH HABE ZUERST DEN FALSCHEN COMMIT GEPRUEFT: aedc9d27 setzt nur den Zustand und aendert
     eine einzige Datei. Waere ich dabei geblieben, haette ich 'kein Blatt angefasst' gemeldet.
     Der Bau ist 53142fc2, und die Tafelzeile nennt ihn — ich habe sie erst danach gelesen."
  - "Mein Muster '^\\| K-[34]' fand nichts, weil die Zeilen jetzt '| **K-3** *(umgestellt)* |'
     heissen. Beinahe haette ich K-3 und K-4 als GELOESCHT gemeldet — dabei sind sie genau so
     umgestellt, wie W-40/1-1b es verlangt. H-9 an mir selbst: das Muster misst die Schreibweise."
  - "Mein '$W/*.md' erfasst den Unterordner 5-CODE/ NICHT. Damit fand ich die beiden berichtigten
     Pfadangaben nicht und stand vor der Meldung, W-40/1-6 sei unerfuellt. Rekursiv gesucht
     stehen sie in 5-CODE/LIESMICH.md:98-104."
  - "Alle drei haetten einen Fehlbefund gegen einen richtigen Bau ergeben. Sie stehen hier, weil
     der Takt sagt: bei jeder Abweichung zuerst den eigenen Aufbau pruefen."

was_diesen_bau_traegt: "Er repariert einen Fehler, den ich gefunden habe, und er repariert ihn
  vollstaendiger als mein Befund war — ich hatte die Praemisse widerlegt, er berichtigt zusaetzlich
  den TRAEGER (die Achse haengt am Paket, nicht am Schritt) und die dreizehn Folgestellen. Und er
  loescht nichts: jede ueberholte Aussage steht mit ihrem alten Wortlaut da, gekennzeichnet und
  datiert. Das ist die Form, die A-20-4 verlangt, und sie ist teurer als Umschreiben."
```
