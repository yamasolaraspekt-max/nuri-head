# Baubericht W-23 — Deckung und Material. Sieben Blätter aus der Quelle, eine Abweichung, und eine zurückgezogene

> **Nachbesserung 12.08. (§12.5).** *Die erste Fassung dieses Berichts meldete **zwei** Abweichungen
> zum Auftragsblatt. **Die erste war selbst der Fehler** und ist unten zurückgezogen; dazu kommt das
> neue Kriterium `W-23-8`.*

```yaml
auftrag: "W-23"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-23-deckung-und-material.md
basis_sha: 39270fab
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

**Der Auftrag, der W-21L zuschneidbar macht.** *Sieben Blätter, eine Registerzeile — und jede Zahl
darauf ist in der Braas-Datenbank nachlesbar.*

## W-23-1 · Aus der Quelle abgelesen, nicht entworfen

**Die Quelle habe ich VOR dem Ziehen geprüft**, damit kein Auftrag mit unlesbarem Operanden läuft:

```text
Datei    braas_dachziegel_datenbank_v14.xlsx      718.574 Byte   ← Größe aus dem Blatt, auf das Byte
Blatt    DB_Produkte  →  xl/worksheets/sheet11.xml
Umfang   128 Zeilen = 1 Kopf + 127 Daten  ·  48 Spalten
```

*`openpyxl` fehlt in dieser Umgebung; eine `.xlsx` ist ein ZIP mit XML, gelesen wurde mit
`zipfile` + `xml.etree`.* **Eine Eigenheit der Mappe, gemessen statt vorausgesetzt: sie hat KEINE
`sharedStrings.xml`** — die Texte stehen inline. *Mein zweites Leseskript setzte die Datei voraus
und brach ab; erst das Nachsehen half.*

**Alle dreizehn Spaltennamen des Blattes stimmen wörtlich** (26/27 Lattmaß, 32 Verschiebespiel,
33/34 Neigung, 22–25 Deckmaße, 37–39 Herkunft, 47 Eindeckmaß-Text).

**Und die Zahlen des Blattes, einzeln nachgemessen:**

| Angabe im Blatt | gemessen |
|---|---|
| 127 Datenzeilen · 48 Spalten | **127 · 48** ✓ |
| Datenstatus 78 / 26 / 17 / 6 | **78 / 26 / 17 / 6** ✓ |
| Füllquote 9 / 13 / 17 | **9 / 13 / 17** ✓ |
| 9 Zeilen = 7 Modelle, zwei doppelt | **9 Zeilen, 7 Modelle** — `Rubin 13V` (HA/OG) und `Topas 13V` (HA/OG) ✓ |

## Eine Abweichung zum Auftragsblatt — und eine, die ich ZURÜCKZIEHE

> ## ⚠ (1) ZURÜCKGEZOGEN 12.08. — meine „Abweichung" war selbst der Fehler
>
> **Hier stand:** *„Das Blatt schreibt `Harzer Pfanne 7`. In der Quelle heißt es `Modell_Typ =
> "Harzer Pfanne"` — eine `7` steht dort nicht."* **Das war falsch.**
>
> *Nachgemessen, ungekürzt:* **Zeile 10 trägt `Modell_Typ = "Harzer Pfanne 7"`, Variante `"Big"`,
> Lattmaß 372–405.** *Die `7` steht dort; das Auftragsblatt hatte recht.*
>
> **Die Ursache war mein eigenes Ausleseskript: es kürzte die Spalte auf 14 Zeichen — und
> `"Harzer Pfanne 7"` hat 15.** *Die Anzeige hat die Ziffer abgeschnitten, und ich habe die Kürzung
> als Befund gegen das Blatt gemeldet.* **Das ist H-9 in Reinform, an dem Tag, an dem ich die Regel
> selbst formuliert habe — nicht das Muster war falsch, sondern das, woran es ansetzte.**
>
> *Und es ist die gefährliche Richtung:* **wer mit `Harzer Pfanne` in die Quelle geht, landet auf
> Zeile 9 — dort stehen KEINE Lattmaße.** *Meine Korrektur hätte das Werkzeug-Blatt unbrauchbar
> gemacht.* Gefunden vom **Evaluator** (`30cc04c5`), unabhängig nachgemessen und bestätigt vom
> **Planner** (`3a94fe59`); ich habe es meinerseits an der Quelle nachgeprüft, bevor ich es
> angenommen habe. **Der Absatz bleibt stehen statt gelöscht zu werden — der Fehler gehört zur
> Kette, nicht in den Papierkorb.**

> **(2) `Rubin 13V` hat KEINE Regeldachneigung — in beiden Zeilen.** *Das Blatt führt in seiner
> Tabelle für alle sieben Modelle eine Regeldachneigung; gemessen fehlt sie bei einem.*
> **Und das ist nicht kosmetisch: die Schranke des Werkzeugs prüft genau gegen diesen Wert.**
> *Ausgerechnet das Modell mit der doppelten Datenlage kann sie nicht passieren — deshalb gibt es
> jetzt eine eigene **Absage 2** („Schranke nicht prüfbar") statt eines stillen Durchrutschens.*

## W-23-2 · Die entschiedene Fassung, und die verworfene daneben

`3-FORMELN.md` trägt die Fassung aus dem Vertretungsentscheid: **Neigungsschranke**,
`n_min`/`n_max`-Existenzprüfung, und **„nicht gleichmäßig teilbar" als echte Ausgabe statt als Zahl**
— dazu Yamas Fachaussage wörtlich und die Ampel 🟡 mit Geltungsbereich (Regelfläche; Traufreihe,
First, Ortgang, Restausgleich **nicht** erfasst).

**Der geforderte Fall mit `n_min > n_max`, ausgeschrieben:**

```text
Harzer Pfanne 7 (Variante Big), L = 1000 mm, Bereich 372-405
  n_min = aufrunden(1000/405) = 3      n_max = abrunden(1000/372) = 2
  n_min (3) > n_max (2)   ->  KEINE gleichmaessige Teilung, KEIN Wert
  zum Vergleich: n=2 gaebe 500 mm (zu gross), n=3 gaebe 333 mm (zu klein)
```

*Die verworfene erste Fassung (`n = aufrunden(L/max)`) hätte hier **333,3 mm** geliefert — leise, und
außerhalb dessen, was der Ziegel erlaubt.* **`6-PRUEFUNG.md` führt das als `K-6` gegen den Bau:
dieser Wert darf nirgends herauskommen.**

## W-23-3 · Die Füllquote, ungeschönt

**9 von 127 Zeilen tragen ein vollständiges Lattmaß — sieben Modelle, alle von einem Hersteller.**
*Steht so in `7-GRENZEN.md`, samt der Folge:* **für jedes andere Modell kann das Werkzeug NICHTS
sagen** — nicht „ungefähr", nicht „per Vorgabe". *Wer fehlende Werte ergänzt, erfindet sie.*

## W-23-4 · Die Eingangsprüfung, heute schon durchgerechnet

```text
Achat 12V     360-330=30 · Spiel 30 ✓      Rubin 9V      400-370=30 · Spiel 30 ✓
Granat 11V    380-338=42 · Spiel 42 ✓      Topas 11V     380-320=60 · Spiel 60 ✓
Rubin 13V HA  360-330=30 · Spiel 30 ✓      Topas 13V HA  360-320=40 · Spiel 40 ✓
Rubin 13V OG  360-330=30 · Spiel 30 ✓      Topas 13V OG  360-320=40 · Spiel 40 ✓
Harzer Pfanne 7  405-372=33 · Spiel  —  fehlt, ableitbar
```

**Acht von acht Zeilen mit beiden Werten stimmen, keine einzige Abweichung.** *Das Blatt nennt
„sechs von sechs" — es zählt **Modelle**, ich zähle **Zeilen**; die zwei Doppelvarianten erklären den
Unterschied vollständig.* **Beide Zahlen stimmen, sie zählen Verschiedenes.**

## W-23-8 · Der Modellname ist keine Adresse (neu, Nachbesserung)

*Das Kriterium ist aus meinem Fehler entstanden: der Planner hat den Evaluator-Befund nachgemessen
und daraus eine Zusage gemacht statt einer Bemerkung.* **Ich habe seine Zahlen an der Quelle
nachgeprüft, bevor ich sie übernommen habe — alle stimmen:**

```text
114 verschiedene Modellnamen bei 127 Zeilen   (Namen normalisiert: Braas-Praefix und
  8 Namen mit Dubletten                        Klammerzusaetze entfernt)
  5 davon OHNE jede gefuellte Zeile

  Opal Standard   5 Zeilen, 0 gefuellt    Frankfurter Pfanne  2 Zeilen, 0 gefuellt
  Rubin 11V       3 Zeilen, 0 gefuellt    Harzer Pfanne       2 Zeilen, 0 gefuellt
  Rubin 13V       3 Zeilen, 2 gefuellt    Harzer Pfanne 7     2 Zeilen, 1 gefuellt
  Taunus Pfanne   2 Zeilen, 0 gefuellt    Topas 13V           2 Zeilen, 2 gefuellt
```

**Die verlangte Gegenprobe, an der Quelle gefahren:**

```text
Zugriff "Harzer Pfanne"                     ->  Zeile 9,  Lattmass LEER  ->  KEIN Bereich
Zugriff "Harzer Pfanne 7" + Variante "Big"  ->  Zeile 10, 372-405        ->  Bereich
```

> **Adressiert wird über `Modell_Typ` PLUS `Variante_Ausfuehrung`** — *und wo auch das nicht
> eindeutig ist, entscheidet die Zeile mit gefüllten Lattmaßen.* **Warum das gefährlicher ist als
> eine fehlende Zahl: eine leere Zelle sieht man. Ein Treffer auf der falschen Zeile sieht aus wie
> ein Ergebnis.**

*Auch `Rubin 13V` hat drei Zeilen, nicht zwei — die dritte (Zeile 75) ist leer. **Die Angabe „neun
Zeilen, sieben Modelle" bleibt richtig**, sie zählt nur die gefüllten; die Adressierung muss die
leeren trotzdem kennen.*

## W-23-5 · Kein Wert ohne Herkunft

**Alle neun Zeilen tragen `Datenstatus` UND `Quelle_1_URL`.** *Übernommene Werte: 9 · mitgeführte
Statusangaben: 9.* **Kein Wert ohne Herkunft — F-051s Lehre angewandt, bevor der Fehler entsteht.**

## W-23-6 · `must_preserve`

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien** — geändert, hinzugefügt, entfernt je 0 |
| **die Quelldatei** | **unverändert, 718.574 Byte** — nur gelesen |
| Register | **genau eine Werkzeugzeile** (`LEER` → `BESCHRIEBEN`) |
| Abschlusszähler `BESCHRIEBEN` | **11 → 12** — *hier soll er steigen: W-23 ist Ziel `BESCHRIEBEN`, anders als W-15/1 (`ENTWORFEN`)* |
| Import · Schema · Migration · Seeder | **keiner** |

## Platzhalter — drei Treffer, keiner ist einer

```text
grep -nE '<[^>]+>' über die sieben Blätter  ->  3
  3-FORMELN.md:25        n_min <= n_max   ->      ein VERGLEICHSOPERATOR
  5-CODE:18              <is><t>                  eine XML-Tag-Erwähnung
  5-CODE:75              Array<{ n: number … }>   ein TypeScript-Generic
```

> **Alle drei gelesen, keiner ist ein unausgefüllter Platzhalter.** *Rot vorher: 27.* **Das ist B5s
> Fall 4 in neuer Gestalt — `< 1 mm²` wurde damals als Platzhalter gezählt.** *Deshalb steht hier die
> Zahl **mit** ihren Zeilen und nicht allein.*

## Was das für W-21L bedeutet

**Zuschneidbar, nicht entsperrt.** *Ein Auftrag über **sieben belegte Modelle** ist möglich; ein
Auftrag über „die Lattung" ist es weiterhin nicht.* **Und für `Rubin 13V` fehlt die Schranke — wer
es aufnimmt, muss diesen Fall benennen.** *Der Zuschnitt gehört dem Planner.*

## Rückweg

`git apply --check -R` → **Exit 0**, Arbeitsbaum unangetastet. *Sieben Doku-Blätter und zwei
Registerzeilen; kein Code, kein Datenpfad, keine Migration.*

## Berührte Dateien

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-23-deckung-und-material/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/W-23-deckung-und-material/5-CODE/LIESMICH.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md      Werkzeugzeile + Fundstelle
docs/BERICHT-W-23-deckung-und-material.md               dieser Bericht
docs/STATUS.md                                          Zustand an beiden Orten
```
