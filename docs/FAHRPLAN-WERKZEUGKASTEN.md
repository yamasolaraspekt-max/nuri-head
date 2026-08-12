# Fahrplan Werkzeugkasten — alle 42 Zeilen, eine Reihenfolge, messbare Abschlüsse

```yaml
art: "Fahrplan des Planners ueber die GANZE Tafel. Ersetzt FAHRPLAN-KLASSE-A.md."
erstellt: "12.08."
basis_sha: 21972085
anlass: "Yamas Frage: 'warum erstellst du dir dafuer nicht den Fahrplan'. Berechtigt."
verhaeltnis_zu_yamas_liste: "Yamas Reihenfolge (A-13, SELECTs, W-07N, Extraktoren, M-02,
   Bruecke, Klasse A, Barrieren) ist die AUFTRAGSLISTE und hat Vorrang. Dieser Fahrplan
   ist die WERKZEUG-Landkarte: welches Werkzeug wann, und was es entsperrt.
   ZWEI Ebenen, keine zwei Wahrheiten — dieser Plan ordnet keinen Auftrag um."
```

## Warum der alte Fahrplan versagt hat — und es ist ein Bauartfehler, kein Versehen

**`FAHRPLAN-KLASSE-A.md` war ein Runden-Plan für zehn Werkzeuge. `W-09 Treppe` passte in keine der
drei Runden. Ich habe daraufhin eine Zeile geschrieben:**

```text
FAHRPLAN-KLASSE-A.md:148   "NICHT IN A   W-09 (Treppe, 698 Z) — war nie in den drei Runden"
```

> **Ich habe die Lücke notiert und den Plan nicht geändert.** *Und weil sie notiert war, sah sie
> erledigt aus — sie stand ja da.* **Eine Notiz über eine Lücke ist kein Plan für die Lücke.** *Das
> ist derselbe Mechanismus wie bei `konterlattungMm` und bei `auswechslung.ts`: benannt, in zwei
> Blättern erwähnt, in keinem zuhause.*
>
> **Die Bauartfolge: ein Plan, der eine feste Zahl von Runden hat, wirft alles aus, was nicht in die
> Runden passt.** *Dieser Fahrplan hat deshalb keine Runden, sondern **Stufen mit Eintrittsbedingung**
> — und eine Zeile, die in keine Stufe passt, ist ein Befund gegen den Plan, nicht gegen das
> Werkzeug.*

## Vollerhebung — Menge definiert, Summe daneben (B6)

```text
MENGE     alle Zeilen '^| W-[0-9]+ |' in docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md
SUMME     42 Zeilen
  BESCHRIEBEN      7   W-01 W-02 W-04 W-05 W-11 W-21 W-22
  6/7 BLAETTER     1   W-07
  LEER            34
```

## Klassifikation — NEU, mit drei Korrekturen an der Fassung vom 10.08.

```text
KORREKTUR 1   W-17 Export und Speichern: von C nach A.
              VIER dedizierte Module gemessen — paketSpeichern · arbeitsbereichSpeicher ·
              schienenSpeicher · speicherAnzeige. Das ist eintragen, nicht bauen.
KORREKTUR 2   W-09 Treppe gehoert zu A und war nie eingeplant. Sieben Module, 698 Zeilen —
              nach W-07 das zweitgroesste Werkzeug der Tafel.
KORREKTUR 3   die 19 neuen Zeilen (W-24…W-42) sind noch nicht klassifiziert. Sechs von
              zehn Zielbild-"Luecken" und sieben der zehn Fuehrungszeilen sind GEBAUTER
              Code ohne Blatt — also A, nicht C.
WARNUNG       ein WORTGLEICHHEITSFALL, der die Klassifikation fast verdorben haette:
              bewerteDeckung() existiert — in heizkoerperLeistung.ts:53, als
              LEISTUNGSdeckung eines Heizkoerpers, NICHT als Dachdeckung.
              Haette ich den Treffer fuer W-23 gezaehlt, waere C um eins zu klein gewesen.
```

**Die drei Klassen, neu belegt:**

```text
KLASSE A  EINTRAGEN — Code existiert, Blatt fehlt
  fertig      W-01 W-02 W-04 W-05 W-11 W-21 W-22                        (7)
  im Bau      W-07 (6/7, W-07N geschnitten) · W-08 · W-13               (3, Blaetter da)
  NICHT       W-09 Treppe                                               (1, KEIN Blatt)
  GESCHNITTEN
  neu dazu    W-17 · W-25 W-29 W-30 W-31 · W-33…W-39                    (12, keine Blaetter)
KLASSE B  ANSCHLIESSEN — teils gebaut, Anschluss unklar
  W-03 W-06 W-10 W-12 W-14 W-16 W-18 · W-24 W-26 W-28                   (10)
KLASSE C  BAUEN — kein Modul, gemessen
  W-15 Material/Farbe · W-19 Sonne/Verschattung · W-20 Mengen (nur Holz
  vorhanden) · W-23 Deckung/Material · W-27 Kantentypen · W-32
  Giebelbindung · W-40 W-41 W-42 (Fuehrung)                             (9)
```

## Die Reihenfolge — nach ENTSPERRUNG, nicht nach Aufwand

```text
STUFE 1 · KLASSE A ZU ENDE                     Eintrittsbedingung: keine
  1.1  W-07N bauen         wartet auf DoR. Schliesst W-07 auf BESCHRIEBEN.
  1.2  W-08/1 bauen        wartet auf DoR. Blatt seit 10.08. geschnitten.
  1.3  W-13/1 bauen        wartet auf DoR. Blatt seit 10.08. geschnitten.
  1.4  W-09 SCHNEIDEN      << DAS LOCH. Sieben Module, 698 Z. Noch kein Blatt.
  1.5  W-09 bauen
  ABSCHLUSS MESSBAR:  grep -cE '^\| W-(0[1-9]|1[0-3]|21|22) .*BESCHRIEBEN' REGISTER.md
                      -> Ziel 11 (die zehn A-Werkzeuge + W-01)
                      heute 7. Die Zahl steigt nur durch Bauten, nicht durch Schnitte.

STUFE 2 · W-23 DECKUNG UND MATERIAL            Eintrittsbedingung: Fachdaten von Yama
  Warum VOR den anderen C-Werkzeugen: W-21L (Lattung) ist am OPERANDEN blockiert und
  wartet ausdruecklich auf W-23s Ziegeltabelle. W-23 loest einen blockierten Auftrag;
  W-15, W-19 und W-20 loesen nichts.
  BLOCKIERT DURCH: keine Deckungsart-/Lattweiten-Daten im Repo (0 Treffer gemessen).
                   M-04 traegt laut BESTAND-YAMA.md ein Dachziegel-Schema mit
                   verified/source_url -> das ist die Quelle.
  ABSCHLUSS: W-23 BESCHRIEBEN + W-21L nicht mehr blockiert.

STUFE 3 · DIE ZWOELF NEUEN A-ZEILEN EINTRAGEN  Eintrittsbedingung: Stufe 1 durch
  W-17 · W-25 W-29 W-30 W-31 · W-33…W-39
  Je Werkzeug: Anschluss MESSEN (welche Module, welche Formeln), dann Blatt schneiden.
  KEIN Sammelblatt. Ein Auftrag je Werkzeug, zwei Stufen, wie bisher.
  W-31 bleibt gesperrt bis F-028 🟢 (PV-Belegung).
  W-37 traegt A-14s Ausgabeauflage — es ist die Ausgabestelle von N-003.

STUFE 4 · KLASSE B ANSCHLIESSEN                Eintrittsbedingung: Stufe 3 durch
  W-03 W-06 W-10 W-12 W-14 W-16 W-18 · W-24 W-26 W-28
  Hier gilt zuerst die Messung: was ist gebaut, was fehlt. Erst danach A oder C.
  W-12 und W-18 stehen unter Yamas offener Streichfrage — nicht anfassen, bevor sie
  entschieden ist.

STUFE 5 · KLASSE C BAUEN                       Eintrittsbedingung: je Werkzeug eigen
  W-27 Kantentypen      -> braucht F-025/F-026, beide 🟢 nach A-12. FREI.
  W-32 Giebelbindung    -> braucht W-03 (Stufe 4). GESPERRT bis dahin.
  W-15 Material/Farbe   -> braucht W-13 (Stufe 1). Danach frei.
  W-19 Sonne            -> braucht W-07 + W-08 (Stufe 1) und beruehrt F-028. Vorsicht.
  W-20 Mengen           -> braucht W-05 + W-08. holzMengen ist der Anschluss.
  W-40 W-41 W-42        -> die Fuehrungsluecken. W-42 (Schreibpfad) ist die wichtigste:
                           solange der Wizard JSON herunterlaedt, arbeiten Wizard und
                           Expertenmodus nicht auf denselben Objekten (Yamas Teil 5).
```

## Was BLOCKIERT ist — und durch wen

```text
W-21L Lattung        OPERANDEN-GATE: keine Deckungsart-Daten. -> Yama oder W-23.
W-31 PV-Belegung     F-028 🔴. -> Umrechnung an der Systemgrenze (Yamas Schritt 9).
W-12 · W-18          Yamas offene Streichfrage.
N-003-Ampel          DAUERGELB, entschieden. Nicht blockiert, dauerhaft begrenzt.
W-07s acht Formeln   ALLE ungeprueft. Nach 603eddc2 (sieben von zehn fielen) ist das
                     ein eigener Auftrag, und er gehoert VOR W-07s Abschluss.
```

## Was dieser Fahrplan NICHT tut

```text
- Er ordnet KEINEN Auftrag um. Yamas Liste hat Vorrang; dieser Plan sagt, welches
  WERKZEUG wann kommt, nicht welcher Auftrag heute laeuft.
- Er nennt KEINE Zeitraeume. Jede Stufe hat eine Eintrittsbedingung, keine Frist —
  eine Frist waere eine Zahl ohne Messung.
- Er entscheidet NICHT ueber W-12/W-18 (Yamas Streichfrage) und nicht ueber die
  Prozessebene-Inhalte (Yamas Zielbild).
- Er klassifiziert die 19 neuen Zeilen VORLAEUFIG. Jede braucht ihre eigene
  Anschlussmessung, bevor ein Blatt entsteht — das ist Stufe 3 und Stufe 4.
```

## Der alte Fahrplan

```text
docs/FAHRPLAN-KLASSE-A.md   bleibt als BELEG stehen (drei Runden, sechs Grobzahl-
   Korrekturen, die Klasse-A-Messungen). Er ist NICHT MEHR der Plan.
   -> in seinem Kopf wird ein Verweis hierher eingetragen, damit niemand zwei
      Fahrplaene liest. Das ist Teil dieses Auftrags, nicht spaeter.
```

## REIHENFOLGE-ENTSCHEIDUNG 12.08. — W-09/1 läuft vor A-15

**Anlass: der Generator hat eine Sperre gemeldet statt sie zu umgehen** (`bd011a06`), und die
Entscheidung ausdrücklich mir überlassen: *„die Reihenfolge zweier Aufträge gehört dem Planner."*

```text
DIE SPERRE, seine Messung:
  A-15-13 verlangt einen Vorschlag fuer JEDE der elf Engines.
  A-15-11 verlangt, dass die VIER Treppen-Dateien NICHT dort gemessen, sondern aus
          W-09/1-5 uebernommen werden.
  W-09/1 steht auf BEREIT und ist NICHT gebaut -> die Zulieferung existiert nicht.
  -> beide Kriterien sind heute nicht gleichzeitig erfuellbar.
```

**ENTSCHEIDUNG: W-09/1 läuft zuerst.** *Vier Gründe, und der billigere ist nicht der erste:*

```text
1  A-15-11 ist INHALTLICH richtig. Die Auflage umzukehren hiesse, den Fehler zuzulassen,
   den sie verhindert: zwei Auftraege, die dieselbe Datei messen, erzeugen zwei Zahlen.
2  W-09/1 schliesst KLASSE A ab. Mit W-07N steigt der Zaehler von 9 auf 11 — und er
   steigt nur durch Bauten (H-3).
3  A-15 verliert NICHTS. Die sieben messbaren Zeilen stehen vollstaendig; die vier
   Treppen-Zeilen kommen als ZULIEFERUNG dazu, nicht als Nacharbeit.
4  Es kostet keine zusaetzliche Runde: W-09/1 muss ohnehin gebaut werden.
```

**Was dem Generator freibleibt** — es ist Bauweise, nicht Reihenfolge: *A-15 mit den sieben Zeilen
abschließen und die vier als ausstehende Zulieferung führen, **oder** A-15 zurückstellen, bis W-09
gebaut ist.* **Beides ist mit dieser Entscheidung vereinbar.**

**Und der Mangel, der die Sperre erst möglich gemacht hat, ist meiner:** *ich habe `A-15-11`
geschnitten, ohne A-15 in der Konfliktprüfung **hinter** W-09 zu stellen.* **Ein Blatt, das eine
Zulieferung aus einem anderen Auftrag zitiert, braucht eine Reihenfolge-Zeile — nicht nur den
Verweis.** *Das ist derselbe Vorlagen-Mangel wie der fehlende Rückweg: **die Vorlage kennt das Feld
nicht.** Beide werden in den nächsten Schnitt eingebaut, nicht rückwirkend in laufende Blätter.*

```yaml
stufen: 5
zeilen_gesamt: 42
heute_beschrieben: 7
naechster_schnitt: "W-09 Treppe — das letzte ungeschnittene Klasse-A-Werkzeug"
kern: "ein Plan mit fester Rundenzahl wirft aus, was nicht in die Runden passt.
       Dieser hat Stufen mit Eintrittsbedingung — eine Zeile, die in keine Stufe passt,
       ist ein Befund gegen den Plan."
offen_an_yama: "Deckungsart-Tabelle (Stufe 2) · W-12/W-18-Streichfrage (Stufe 4) ·
                ob W-09 sofort geschnitten wird"
```


## REGEL — keine festen Suite-Zahlen in Kriterien (aus W-01N übernommen, 12.08.)

> **Der Generator hat diese Regel in `docs/FAHRPLAN-KLASSE-A.md` eingetragen, weil Kriterium und
> Scope von W-01N sie dort verlangten — und ausdrücklich gemeldet, dass diese Datei seit 12.08.
> aufgehoben ist.** *Er hat den Widerspruch im Regeltext sichtbar gemacht statt eine Datei außerhalb
> seines Scopes anzufassen, und den Ball an den Planner gegeben. **Der SPEC-Fehler ist meiner:** ich
> habe W-01N heute überarbeitet — Zustand, Basis, §3-Verweis, Drift-Beleg — und dabei nicht bemerkt,
> dass sein Ziel eine aufgehobene Datei ist. **Hier steht sie im gültigen Plan; damit wirkt sie.***

**Die Regel:** *Ein Kriterium sagt „die Insel-Suite bleibt **unverändert** grün" — **ohne Zahl**. Die
gemessene Zahl gehört in den **Bericht**, zusammen mit dem Befehl, der sie erzeugt hat.*

**Der Fall, aus dem sie kommt, und er ist stärker als „die Zahl veraltet":**

```text
1689   steht als Kriterium in W-01/1-6
1692   gemessen bei der Abnahme — schon beim Schnitt des Blattes ueberholt
1693   Stand am 12.08., FUENFMAL unabhaengig genannt
1694   Stand nach dem A-18-Bau
1668   grep -cE '^\s*(test|it)\(' ueber 166 Testdateien
```

> **Die letzten beiden sind BEIDE richtig und messen Verschiedenes:** *`grep` zählt geschriebene
> `test(`-Aufrufe, der Lauf zählt **ausgeführte** Zusagen — parametrisierte Tests erzeugen mehr Läufe
> als Zeilen. **Eine feste Zahl ist damit nicht erst veraltet, sobald jemand einen Test schreibt,
> sondern schon unbestimmt, sobald zwei Rollen mit verschiedenen Werkzeugen messen.*** *Deshalb
> „unverändert" statt einer Ziffer: das ist prüfbar, ohne die Messmethode mitzuliefern.*

**Gilt für alle Stufen dieses Plans**, nicht nur für Klasse A — und für jedes `must_preserve`, das
eine Suite nennt.
