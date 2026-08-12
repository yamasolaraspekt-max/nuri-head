# W-18/1 — Topologie prüfen. Beide Formeln sind gebaut, und eine steckt in der Gehrung

```yaml
auftrag: "W-18/1"
werkzeug: "W-18 Topologie prüfen"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Die Einordnung ist GEMESSEN,
      nicht angenommen: beide genannten Formeln sind gebaut und angeschlossen."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 8c920624
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "Vergeben ist nur W-18 selbst; W-18/1 ist frei, 0 Blätter."
anlass: "Yamas Vorbehalt vom 13.08.: 'bei B gilt laut Fahrplan zuerst die Messung: was ist gebaut, was
         fehlt. Erst danach steht fest, ob eine B-Zeile eine Ablesung (schnell) oder ein Bau (langsam)
         wird'. Das ist die Messung, und sie ist zu Ende gefahren."
grundlage: "geometry/kontur.ts (175 Z., 8 Exporte) · geometry/wallGeometry.ts (317 Z., 12 Exporte) ·
            HausplanerApp.tsx:29-31 als Aufrufer · FORMELSAMMLUNG.md:75 F-004 und :120 F-013"
```

## 1 — Die Einordnung ist gemessen: ABLESUNG, nicht Bau

**Yamas Regel angewandt, Schritt für Schritt** — *die Registerzeile nennt F-004 und F-013; beide sind
gebaut:*

```text
F-013 Selbstschnitt-Pruefung  (FORMELSAMMLUNG:120, „Vorpruefung vor jeder
                               Flaechen- oder Dachberechnung")
  geometry/kontur.ts, 175 Z., ACHT Exporte:
    :41  KonturPunkt        :47  KonturGrund ('zu-wenig-punkte'|'selbstschnitt'|
                                              'keine-flaeche')
    :49  KonturUrteil       :55  KONTUR_MIN_PUNKTE = 3
    :61  KONTUR_MELDUNG     :109 schneidetSichSelbst()   <- F-013 selbst
    :135 pruefeKontur()     :156 konturStatusText()
  UND SIE HAT EINE LESBARE NUTZERMELDUNG, :63 woertlich:
    „Die Kontur ueberschneidet sich selbst — zieh den letzten Punkt so, dass sich
     keine zwei Kanten kreuzen."
  ANGESCHLOSSEN: HausplanerApp.tsx:31 fuehrt VIER Symbole ein — pruefeKontur,
  konturStatusText, KONTUR_MIN_PUNKTE und KonturGrund. Und :30 sagt den Grund:
  „Z-05: die Konturpruefung ist reine Geometrie und wohnt dort, nicht hier."

F-004 Schnittpunkt zweier Geraden  (FORMELSAMMLUNG:75, „Wandachsen verschneiden,
                                    Ecke bilden")
  GEBAUT, aber NICHT als eigenes Modul — er steckt in der GEHRUNG:
    wallGeometry.ts:62   „Gehrung (mitered): die beiden Bandkanten werden bis zum
                          Schnittpunkt verlaengert"
    wallGeometry.ts:106  „Liefert die beiden Schnittpunkte der Bandkanten
                          (Halbdicke h) oder null"
  Gegenprobe: kein achsenSchnitt, kein geradenSchnitt, kein
  schnittZweierGeraden im ganzen Repo — 0 Treffer.
```

> **Damit ist W-18 eine ABLESUNG** — *beide Formeln sind gebaut, eine sogar mit Nutzermeldung, und die
> Konturprüfung ist angeschlossen. **Es fehlen die Blätter, nicht der Code.** Für Yamas Frage heißt das:
> diese B-Zeile fällt auf die schnelle Seite, und der W-27-Maßstab greift hier nicht.*

**Und was das Blatt sagen muss, weil es sonst falsch gelesen wird:** *F-004 ist **nicht** als
Topologie-Formel gebaut, sondern als **Gehrungsdetail**. Wer im Werkzeug „Topologie prüfen" eine Funktion
`geradenSchnitt` sucht, findet keine — sie heißt anders und tut mehr.*

## 2 — Die H-9-Falle in diesem Werkzeug, und sie ist scharf

```text
'kontur'          toolRegistry.ts:230 — ein WERKZEUG. Label „Kontur", icon 'raum',
                  art 'werkzeug', groupId 'gebaeude', Ansichten 2d und split.
                  Das ist das ZEICHNEN-Werkzeug.
geometry/kontur.ts  die PRUEFUNG. Anderer Gegenstand, gleicher Name.
```

**Gemessen, was das für die Wächterzählung bedeutet:**

```text
IMPORT von geometry/kontur:   kontur.test.ts                      (EINER)
Wort 'kontur' in __tests__:   ZWOELF Dateien — toolRegistry,
                              werkzeugVertrag, toolPresentation,
                              werkzeugLandkarte, buehne, markieren,
                              dachProjektion, dachGeometrie, masseingabe,
                              leisteAusZonen, fussUndUeberlagerungenStil,
                              kontur.test
```

> **Elf der zwölf treffen die WERKZEUG-ID, nicht das Prüfmodul.** *Wer „zwölf Wächter" schreibt, hat
> eine Zahl und keinen Befund — **das ist wörtlich der Fehler, der W-36-5 und W-37-5 je eine DoR-Runde
> gekostet hat.** Das Blatt nennt deshalb die Zugriffsart je Test und keine nackte Zahl.*

## 3 — Ein offener Posten, der W-18 gehört und aus einem anderen Blatt kommt

```text
W-09-treppe-beschreiben.md:207-208, woertlich:
  „Treppe ohne Zielgeschoss  -> gehoert zu W-18 (Topologie), nicht hierher.
                                Yama hat W-18 ausdruecklich behalten."
```

> **Das ist ein DRITTER Topologie-Fall** — *neben Selbstschnitt (F-013) und Achsenschnitt (F-004).
> **Und er ist heute nicht gebaut:** die Konturprüfung sieht eine Punktfolge, nicht ein Geschoss. Das
> Blatt trägt ihn als Grenze, damit er nicht in W-09s Blatt verwaist — und die Zeile belegt zugleich,
> dass W-18 auf Yamas ausdrückliche Entscheidung bleibt.*

## 4 — Scope

```text
W-18/1 IST  die Ablesung des Gebauten: kontur.ts mit acht Exporten und der
            Nutzermeldung, F-004 in der Gehrung von wallGeometry.ts, der Anschluss
            ueber HausplanerApp.tsx:31, und die Waechter je mit Zugriffsart.
            Dazu die H-9-Grenze zwischen dem Werkzeug 'kontur' und dem Modul
            geometry/kontur.ts.

W-18/1 IST NICHT
            ein WERKZEUG 'Topologie pruefen'. Es gibt keines, und ob es eines
            braucht, ist eine eigene Frage — die Vorpruefung wirkt heute beim
            Zeichnen. Als GRENZE benennen, nicht bauen.
            wallGeometry.ts als Ganzes -> es gehoert zu W-02 (Wand); hier wird nur
            die F-004-Stelle mit Fundstelle genannt.
            das Zeichnen-Werkzeug 'kontur' -> eigener Gegenstand, nur zur
            Abgrenzung genannt.
            Treppe ohne Zielgeschoss -> als offener Posten benannt, nicht gebaut.
```

## 5 — Abnahmekriterien

```text
W-18-1-1 (P1, TRAGEND) Beide Formeln stehen mit Fundstelle und ZUSTAND: F-013 in
         geometry/kontur.ts:109 (schneidetSichSelbst), mit der Meldung aus :63
         WOERTLICH; F-004 in wallGeometry.ts:62 und :106 — und ausdruecklich der
         Satz, dass F-004 NICHT als Topologie-Formel gebaut ist, sondern als
         GEHRUNGSDETAIL. Gegenprobe im Bericht: kein achsenSchnitt,
         geradenSchnitt oder schnittZweierGeraden im Repo.
         Ohne diesen Satz sucht die naechste Rolle eine Funktion, die es nicht
         gibt, und schliesst auf eine Luecke.
W-18-1-2 (P1) Die ACHT Exporte von kontur.ts mit Fundstelle, am Bau-Stand
         gezaehlt. Keine Zahl aus diesem Blatt uebernehmen.
W-18-1-3 (P1) Der ANSCHLUSS steht: HausplanerApp.tsx:31 fuehrt vier Symbole ein,
         und :30 nennt den Grund („die Konturpruefung ist reine Geometrie und
         wohnt dort"). Eine Ablesung, die den Anschluss nicht nennt, laesst offen
         ob der Code wirkt.
W-18-1-4 (P1, H-9) Die Grenze zwischen dem WERKZEUG 'kontur' (toolRegistry.ts:230,
         Zeichnen) und dem MODUL geometry/kontur.ts (Pruefung) steht im Blatt.
         Und die Waechter stehen JE MIT ZUGRIFFSART: genau EINER importiert das
         Modul (kontur.test.ts), die uebrigen nennen die Werkzeug-ID.
         KEINE nackte Zahl im Kriterium — am Bau-Stand erheben. Wer „zwoelf
         Waechter" schreibt, hat eine Zahl und keinen Befund; das hat W-36-5 und
         W-37-5 je eine DoR-Runde gekostet.
W-18-1-5 7-GRENZEN traegt DREI Grenzen: (a) es gibt KEIN Werkzeug
         'Topologie pruefen', die Vorpruefung wirkt beim Zeichnen; (b) F-004 ist
         ein Gehrungsdetail und keine eigene Topologie-Funktion; (c) der offene
         Posten aus W-09:207-208 — Treppe ohne Zielgeschoss gehoert hierher und
         ist NICHT gebaut, weil die Konturpruefung eine Punktfolge sieht und kein
         Geschoss.
W-18-1-6 Die EINORDNUNG steht im Blatt und ist begruendet: W-18 ist eine ABLESUNG
         und kein Bau, weil beide Formeln gebaut und die Pruefung angeschlossen
         ist. Das ist Yamas Verfahren fuer Klasse B — erst messen, dann einordnen —
         und die Messung gehoert ins Blatt, damit die naechste Rolle sie nicht
         wiederholt.
W-18-1-7 Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je
         Blatt, keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zahl an zwei Mustern** (Prüfung 7), **Nachweis muss rot werden können** (Prüfung 4).

```yaml
warum_dieser_schnitt_yamas_regel_folgt: "Er hat am 13.08. gesagt, bei B gelte zuerst die Messung: was ist
        gebaut, was fehlt — erst danach stehe fest, ob eine B-Zeile eine Ablesung oder ein Bau wird, und
        wenn Bau, gelte der W-27-Maszstab von etwa zweieinhalb Stunden. Ich habe die Messung zu Ende
        gefahren, bevor ich geschnitten habe: Module und Exporte gezaehlt, die Aufrufer mit ZWEI Mustern
        gemessen (Import und String), und die entscheidenden Stellen GEOEFFNET statt gezaehlt. Ergebnis:
        Ablesung. Fuer W-12 steht die Vollmessung noch aus; dort liegt bisher nur eine Indikation."
was_die_zwei_muster_hier_gebracht_haben: "Der IMPORT auf geometry/kontur liefert EINE Testdatei, das WORT
        'kontur' liefert ZWOELF — weil elf davon die WERKZEUG-ID treffen und nicht das Pruefmodul. Haette
        ich nur das Wort gezaehlt, stuende 'zwoelf Waechter' im Kriterium, und eine ehrliche Messung
        haette es verletzt. Genau diese Klasse hat W-36-5 und W-37-5 je eine DoR-Runde gekostet."
was_ich_NICHT_gemessen_habe: "Ob ein Werkzeug 'Topologie pruefen' fachlich noetig ist. Die Vorpruefung
        wirkt heute beim Zeichnen und meldet lesbar; ob der Nutzer zusaetzlich einen Gesamtbefund ueber
        seinen Grundriss braucht, ist eine Produktfrage und keine Ablesung. Als Grenze benannt."
W_18_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
