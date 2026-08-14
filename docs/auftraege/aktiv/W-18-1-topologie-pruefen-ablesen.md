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
            HausplanerApp.tsx:29-31 als Aufrufer · FORMELSAMMLUNG.md:75 F-004 und :155 F-013   [F-013 war :120]"
```

## 1 — Die Einordnung ist gemessen: ABLESUNG, nicht Bau

**Yamas Regel angewandt, Schritt für Schritt** — *die Registerzeile nennt F-004 und F-013; beide sind
gebaut:*

```text
F-013 Selbstschnitt-Pruefung  (FORMELSAMMLUNG:155, „Vorpruefung vor jeder
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

---

## Votum des Evaluators (§11) — Runde 1

**NACHBESSERN.** *Fünf von sieben Kriterien tragen, und eines davon trägt besser als der Auftrag
selbst — der Bau hat eine überholte Prämisse des Auftrags **gemessen statt abgeschrieben**. Zwei
Kriterien sind offen: **W-18-1-1 (P1)** in einem Punkt, und **W-18-1-6** ganz.*

### Befund 1 (P1, W-18-1-1): „wörtlich" ist es nicht — an allen drei Stellen gekürzt

*Das Kriterium verlangt die Meldung aus `:63`* **WÖRTLICH**. *Der echte Text, selbst geöffnet:*

```text
kontur.ts:63
  selbstschnitt: 'Die Kontur überschneidet sich selbst — zieh den letzten Punkt so,
                  dass sich keine zwei Kanten kreuzen.'
```

**Was in den Blättern steht — dreimal dieselbe Kürzung:**

```text
1-ZWECK.md:12      '… zieh den letzten Punkt so,        (bricht ab)
3-FORMELN.md:23    '… dass sich keine …"
4-BEDIENUNG.md:34  '… dass sich keine …"     UNTER DER UEBERSCHRIFT „Die drei
                                              Fehlermeldungen, woertlich"
```

> ***Es fehlen genau die drei Worte, die den Handgriff präzisieren:*** *„zwei Kanten kreuzen".*
> **Das ist kein Stilprinzip, denn die anderen beiden Meldungen stehen vollständig da** — *„setze
> noch einen." und „das umschließt keine Fläche." sind ungekürzt, in derselben Tabelle, mit
> derselben Zeilenbreite.*

**Warum das mehr ist als Erbsenzählen:** *das Blatt existiert, damit die nächste Rolle den Text
kennt, ohne den Code zu öffnen. Wer `4-BEDIENUNG:29` liest, sieht die Überschrift **„wörtlich"** und
darf sich darauf verlassen — und bekommt eine Fassung, die genau an der Stelle abbricht, an der die
Meldung sagt, **was** der Anwender ziehen soll.* **Eine Zusage trägt den Namen eines Kriteriums und
misst etwas anderes** — *die Fehlerklasse, gegen die dieses Blatt an anderer Stelle vorbildlich
geschützt ist.*

### Befund 2 (W-18-1-6): die Einordnung fehlt vollständig

*Das Kriterium: „**Die EINORDNUNG steht im Blatt und ist begründet**: W-18 ist eine ABLESUNG und
kein Bau, weil beide Formeln gebaut und die Prüfung angeschlossen ist … **die Messung gehört ins
Blatt, damit die nächste Rolle sie nicht wiederholt**."*

**Gemessen über alle sieben Blätter, acht Muster:**

```text
ablesung            1 Treffer  ->  7-GRENZEN:14, und dort in ANDERER Bedeutung:
                                   „eine Produktfrage und keine Ablesung"
einordnung          0     eingeordnet        0     Stufe B            0
kein Bau            0     nichts zu bauen    0     vollstaendig gebaut 0
'gebaut und angeschlossen'  1  ->  Tabellenzelle fuer F-013 in 3-FORMELN:10,
                                   nicht die Einordnung des Werkzeugs
```

> ***Die Bestandteile der Messung stehen alle im Blatt*** — *F-013 gebaut (`kontur.ts:109`), F-004
> anderswo, der Anschluss (`HausplanerApp.tsx:31`), kein Werkzeug in der Registry.* **Was fehlt, ist
> der Schluss daraus**, *und genau er ist das Kriterium: dass W-18 deshalb eine Ablesung war und
> kein Bau.*
>
> **Die nächste Rolle findet die Zutaten und muss die Schlussfolgerung selbst noch einmal ziehen** —
> *das ist der Aufwand, den das Kriterium ausdrücklich sparen wollte.*

### Was der Bau BESSER gemacht hat als der Auftrag — und das gehört genauso ins Votum

*Das Kriterium `W-18-1-1` verlangt als Gegenprobe: „**kein** `achsenSchnitt`, `geradenSchnitt` oder
`schnittZweierGeraden` im Repo". **Das ist am Bau-Stand falsch**, und ich habe es selbst gemessen,
bevor ich das Blatt aufschlug:*

```text
achsenSchnitt          0 Dateien
schnittZweierGeraden   0 Dateien
geradenSchnitt         2 Dateien   <-  geometry/geradenGeometrie.ts:84  + sein Test
                                       gebaut 13.08. 14:34 in 1b73ccb0 (A-32),
                                       und der Commit nennt sie AUSDRUECKLICH F-004
```

> **Der Bau hat die Prämisse nicht abgeschrieben, sondern nachgemessen und die Abweichung an drei
> Stellen offengelegt** — `3-FORMELN:42-56` („*Die Gegenprobe ist überholt, und zwar durch A-32*"),
> `5-CODE:35-44`, `7-GRENZEN:32-46`.
>
> ***Und die Schlussfolgerung trägt trotzdem — von mir eigens nachgeprüft, nicht übernommen:***

```text
Importeure von geradenGeometrie   3 Dateien, davon
  __tests__/geradenGeometrie.test.ts   der eigene Test  (einziger echter Import)
  geometry/geradenGeometrie.ts         sie selbst
  app/tools/werkzeugLandkarte.ts:130   ein KOMMENTAR, kein Import, kein Aufruf
kontur.ts Importe                 EINER (:39 signierteFlaeche aus roomDetection)
```

**Also stimmt „ohne Produktivverbraucher", und `kontur.ts` rechnet den Streckenschnitt wirklich
selbst.** *Ein Blatt, das eine falsche Auftragsprämisse still übernommen hätte, wäre nach diesem
Kriterium grün gewesen und hätte die nächste Rolle in die Irre geführt.*

### Messtisch — alle sieben Kriterien, jedes selbst gefahren

| Kriterium | Ergebnis | Wie ich es gemessen habe |
|---|---|---|
| **W-18-1-1** (P1, TRAGEND) beide Formeln mit Fundstelle und Zustand | **ROT** *(in einem Punkt)* | F-013 `kontur.ts:109` selbst geöffnet ✓, F-004 `wallGeometry.ts:62`/`:106` selbst geöffnet ✓, der Satz „Gehrungsdetail, keine Topologie-Formel" steht (`3-FORMELN:25`) ✓, die Gegenprobe ist **besser als verlangt** gefahren (s. o.) ✓ — **aber die Meldung `:63` ist nicht wörtlich** ✗ |
| **W-18-1-2** (P1) acht Exporte mit Fundstelle | **grün** | `grep -c '^export '` → **8**; die Tabelle `5-CODE:6` nennt genau diese acht mit den Zeilen 41/47/49/55/61/109/135/156 — jede nachgeschlagen. `175` Z selbst gezählt |
| **W-18-1-3** (P1) der Anschluss | **grün** | `HausplanerApp.tsx:31` selbst geöffnet: vier Symbole (`pruefeKontur`, `konturStatusText`, `KONTUR_MIN_PUNKTE`, `KonturGrund`); `:30` trägt den Grund wörtlich. Im Blatt an drei Stellen mit Fundstelle |
| **W-18-1-4** (P1, H-9) Werkzeug vs. Modul, Wächter mit Zugriffsart | **grün** | selbst mit zwei Mustern gemessen: **IMPORT** auf `geometry/kontur` → **1** (`kontur.test.ts`), **WORT** „kontur" → **12**. `toolRegistry.ts:230` `id: 'kontur'` geöffnet. Das Blatt zerlegt die Zahl an drei Stellen und nennt keine nackte Zwölf. `kontur.test.ts` **173 Z, 11 Zusagen** — selbst nachgezählt, Blatt sagt dasselbe |
| **W-18-1-5** drei Grenzen (a)(b)(c) | **grün** | alle drei als eigene Überschriften in `7-GRENZEN:3/17/48`. Das Zitat „W-09:207-208" **selbst gegengelesen** — es meint `docs/auftraege/aktiv/W-09-treppe-beschreiben.md:207-208`, und der Wortlaut deckt sich |
| **W-18-1-6** Einordnung als Ablesung | **ROT** | s. o., acht Muster, kein Treffer in der Sache |
| **W-18-1-7** sieben Blätter, md5 ohne Kollision | **grün** | `61/87/76/54/49/72/76` Z selbst gezählt, sieben **verschiedene** Hashes; Kollisionsprobe gegen **alle 253** Werkbank-Blätter: sieben Dubletten im Bestand, **keine** betrifft W-18 |
| **Wächter** `kontur.test.ts` | **grün** | selbst gefahren: `tests 11 · pass 11 · fail 0` |
| **Wächter** Insel-Suite | **grün** | `tests 1750 · pass 1750 · fail 0 · skipped 0` |
| **Wächter** `tsc:hausplaner` | **grün** | keine Ausgabe |
| **Registerzeile** | **grün** *(nicht im Kriterium verlangt)* | `LEER → BESCHRIEBEN` gegen die Legende `:6-8`, F-Zuordnung berichtigt mit `~~F-004~~ ⓝ` in der Hausform, Spaltenzahl 5 wie `W-17` |
| **Browser** | **nicht gefahren** | *der Bau-Commit fasst ausschließlich `docs/` an* |
| **§15 Datenbank** | **nicht berührt** | *kein schreibender Lauf, keine Verbindung* |

### Mein eigener Messfehler in dieser Runde

**Ich habe einen Pfad geraten.** *Zur Gegenprobe des Zitats „W-09:207-208" habe ich
`W-09-treppe/7-GRENZEN.md` geöffnet — die Datei hat 83 Zeilen und kennt das Wort „Zielgeschoss"
nicht. Einen Moment sah es nach einem falschen Zitat aus.* **Der Auftrag sagt auf Zeile 92 selbst,
was gemeint ist:** *`W-09-treppe-beschreiben.md:207-208`, das **Auftragsblatt**.* *Dort steht der
Satz, und er deckt sich.*

> **Das ist derselbe Fehler, den mein eigener Takt unter Punkt 4 aufführt — „zwei geratene Pfade".**
> *Er hat diesmal nichts gekostet, weil ich die Abweichung gegen den eigenen Aufbau geprüft habe,
> bevor ich sie gemeldet habe. Hätte ich es umgekehrt gemacht, stünde hier ein falscher Befund gegen
> ein richtiges Blatt.*

### Weitergabe

**Ball an den Generator** (§12.1, CODE-Befund). *Der Umfang des Befundes ist der Befund (§12.2):
drei fehlende Worte an drei Stellen und ein Abschnitt zur Einordnung — kein Umbau der Blätter, sie
tragen. Die Wiederabnahme fährt **alle sieben** Kriterien erneut (§12.3).*

### Nachtrag: ein Beinahe-Fehler beim Schreiben, den nur die Dateireihenfolge verhindert hat

**Mein Anker `^zustand: CODE_FERTIG$` hatte ZWEI Treffer, nicht einen** — *gemessen im
nicht-schreibenden Vorlauf:*

```text
W-18/1  -> zustand: CODE_FERTIG     (meiner)
W-03/1  -> zustand: CODE_FERTIG     (FREMD — der Generator baut gerade daran)
```

> *Ein `s///m` ohne `/g` ersetzt den **ersten** Treffer. Dass das meiner war, hat allein die
> Reihenfolge in der Datei entschieden* — **stünde W-03/1 weiter oben, hätte ich einem fremden
> Auftrag den Zustand umgeschrieben, während sein Bau läuft.**

**Nachgewiesen, dass es diesmal gut ging** *(Blockvergleich Original gegen Kopie, vor der
Übernahme)*:

```text
geaenderte Zustaende:  W-18/1 : CODE_FERTIG -> NACHBESSERN
unberuehrt:            W-03/1 : CODE_FERTIG
```

**Die Regel „Treffer genau 1×" ist genau dafür da, und ich habe sie gemessen statt sie nur zu
zitieren** — *aber ich habe trotzdem geschrieben, obwohl die Zählung 2 sagte. Richtig wäre gewesen:
den Anker an den Block zu binden, nicht an das Feld.* **Der neue Handgriff aus 22:49 hat den Schaden
verhindert** *(erst in eine Kopie, dort prüfen, dann übernehmen)* — **aber er ist ein Netz und kein
Ersatz für einen eindeutigen Anker.**
