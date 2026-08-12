# W-23 · CODE

## NOCH NICHT GEBAUT — aber die Quelle ist gelesen, nicht zitiert

**Es gibt keinen Code für W-23. Es gibt eine QUELLE, und sie ist geöffnet worden.** *Jede Zahl auf
den sieben Blättern steht in dieser Datei und ist mit den Angaben unten nachlesbar.*

```text
Datei     ~/Desktop/Downloads_Aufgeraeumt_2026-05-22/01_Energie_PV_Waermepumpe/Tabellen/
          braas_dachziegel_datenbank_v14.xlsx
Groesse   718.574 Byte
Blatt     DB_Produkte      im Archiv: xl/worksheets/sheet11.xml
Umfang    128 Zeilen  =  1 Kopfzeile + 127 Datenzeilen  ·  48 Spalten
```

**Gelesen wurde mit Bordmitteln** (`zipfile` + `xml.etree`), *weil `openpyxl` in dieser Umgebung
fehlt.* **Eine Besonderheit dieser Mappe, gemessen statt vorausgesetzt: sie hat KEINE
`sharedStrings.xml`** — die Texte stehen inline (`<is><t>`). *Mein erster Leseversuch setzte die
Datei voraus und brach ab; der zweite sieht nach.*

## Die Spalten, auf die es ankommt — Nummer und Name aus der Kopfzeile

| Nr | Feld | Rolle |
|---|---|---|
| 2 · 6 · 7 · 8 | `Hersteller` · `Produktfamilie` · `Modell_Typ` · `Variante_Ausfuehrung` | **die Identität einer Zeile** |
| 22–25 | `Decklaenge_min/max_mm` · `Deckbreite_min/max_mm` | Deckmaße für die Flächenrechnung |
| **26 · 27** | **`Lattmass_min_mm` · `Lattmass_max_mm`** | **der erlaubte Bereich** |
| **32** | **`Verschiebespiel_mm`** | die Doppelangabe → Eingangsprüfung |
| **33 · 34** | **`Regeldachneigung_grad`** · `Dachneigung_min_system_grad` | die Neigungsschranke |
| 37 · 38 · 39 | `Datenstatus` · `Quelle_1_URL` · `Quelle_2_URL` | **Herkunft je Datensatz** |
| 47 | `Eindeckmass_Text` | Klartextregel, wo vorhanden |

## Die neun Zeilen mit vollständigem Lattmaß — vollständig, mit Zeilennummer

*Das ist der gesamte Bestand, aus dem dieses Werkzeug heute rechnen kann.*

| Zeile | Hersteller | Modell | Variante | Lattmaß | Spiel | Probe | Regelneigung | Datenstatus | Quelle |
|---|---|---|---|---|---|---|---|---|---|
| 2 | Braas | Achat 12V | — | 330–360 | 30 | **OK** | 16° | verifiziert | ja |
| 6 | Braas | Granat 11V | — | 338–380 | 42 | **OK** | 25° | verifiziert | ja |
| 10 | Braas | **Harzer Pfanne 7** | **Big** | 372–405 | **—** | **fehlt** | 22° | verifiziert | ja |
| 23 | Braas | Rubin 13V | **HA** | 330–360 | 30 | **OK** | **—** | teilweise verifiziert | ja |
| 24 | Braas | Rubin 13V | **OG** | 330–360 | 30 | **OK** | **—** | teilweise verifiziert | ja |
| 25 | Braas | Rubin 9V | — | 370–400 | 30 | **OK** | 16° | verifiziert | ja |
| 30 | Braas | Topas 11V | — | 320–380 | 60 | **OK** | 25° | verifiziert | ja |
| 31 | Braas | Topas 13V | **HA** | 320–360 | 40 | **OK** | 25° | verifiziert | ja |
| 32 | Braas | Topas 13V | **OG** | 320–360 | 40 | **OK** | 25° | verifiziert | ja |

**Neun Zeilen, sieben Modelle** — *`Rubin 13V` und `Topas 13V` stehen je zweimal (Varianten `HA` und
`OG`), mit identischen Maßen.* **Alle neun tragen `Quelle_1_URL`.**

### W-23-8 · Der Modellname ist keine Adresse

**Adressiert wird über `Modell_Typ` PLUS `Variante_Ausfuehrung`** — *und wo auch das nicht eindeutig
ist, entscheidet **die Zeile mit gefüllten Lattmaßen**.*

```text
gemessen ueber alle 127 Zeilen, Namen normalisiert (Braas-Praefix und Klammerzusaetze entfernt):
  114 verschiedene Modellnamen bei 127 Zeilen
    8 Namen mit Dubletten — und bei FUENF davon traegt KEINE Zeile Lattmasse

  Opal Standard      5 Zeilen, 0 gefuellt      Frankfurter Pfanne  2 Zeilen, 0 gefuellt
  Rubin 11V          3 Zeilen, 0 gefuellt      Harzer Pfanne       2 Zeilen, 0 gefuellt
  Rubin 13V          3 Zeilen, 2 gefuellt      Harzer Pfanne 7     2 Zeilen, 1 gefuellt
  Taunus Pfanne      2 Zeilen, 0 gefuellt      Topas 13V           2 Zeilen, 2 gefuellt
```

**Die Gegenprobe, die das Kriterium verlangt — an der Quelle gefahren:**

```text
Zugriff "Harzer Pfanne"                    ->  Zeile 9,  Lattmass LEER   ->  KEIN Bereich
Zugriff "Harzer Pfanne 7" + Variante "Big" ->  Zeile 10, 372-405          ->  Bereich
```

> **Warum das gefährlicher ist als eine fehlende Zahl:** *eine leere Zelle **sieht man**. Ein Treffer
> auf der falschen Zeile sieht aus wie ein Ergebnis.* **Wer über den Namen allein adressiert, bekommt
> für fünf der acht Dubletten stillschweigend eine Zeile ohne Werte — und für `Harzer Pfanne` genau
> die, an der dieses Blatt gescheitert ist.**

> ### Eine Abweichung zum Auftragsblatt — und eine ZURÜCKGEZOGENE
>
> **(1) ZURÜCKGEZOGEN, 12.08. — die „Korrektur" war selbst der Fehler.** *Hier stand: „das Blatt
> schreibt `Harzer Pfanne 7`, in der Quelle heißt das Modell `Harzer Pfanne`, eine `7` steht dort
> nicht."* **Das war falsch.** *Zeile 10 trägt `Modell_Typ = "Harzer Pfanne 7"`, Variante `"Big"` —
> die `7` steht dort, und das Auftragsblatt hatte recht.*
>
> **Die Ursache war mein Ausleseskript:** *es kürzte die Spalte auf 14 Zeichen, und
> `"Harzer Pfanne 7"` hat 15.* **Die Anzeige hat die Ziffer abgeschnitten, und ich habe die Kürzung
> als Befund gemeldet — H-9 an dem Tag, an dem ich die Regel formuliert habe.**
>
> *Der Vermerk bleibt stehen statt gelöscht zu werden, weil die Richtung zählt:* **wer mit
> `Harzer Pfanne` in die Quelle geht, landet auf Zeile 9 — dort stehen KEINE Lattmaße.** *Die
> falsche Korrektur hätte dieses Blatt unbrauchbar gemacht.* Gefunden vom Evaluator (`30cc04c5`),
> nachgemessen und bestätigt vom Planner (`3a94fe59`).
>
> **(2) Die Regeldachneigung von `Rubin 13V` ist LEER — in beiden Zeilen.** *Das Blatt führt in
> seiner Tabelle für alle sieben eine Regeldachneigung; gemessen fehlt sie bei einem.* **Und das ist
> nicht kosmetisch: die Schranke des Werkzeugs prüft genau gegen diesen Wert.** *Für `Rubin 13V`
> kann heute nicht entschieden werden, ob es auf ein gegebenes Dach darf — siehe `7-GRENZEN.md`,
> Absage 2.*

## Schnittstelle — Vorgabe für den Bau, aus der Quelle abgeleitet

```ts
// VORGABE. Die Feldnamen folgen den Spalten 26/27/32/33; die TypeScript-Form ist Entwurf.
interface ZiegelBereich {
  lattmassMinMm: number;        // Spalte 26
  lattmassMaxMm: number;        // Spalte 27
  verschiebespielMm?: number;   // Spalte 32 — ableitbar als max - min
  regeldachneigungGrad?: number;// Spalte 33 — FEHLT bei Rubin 13V
}
type LattungErgebnis =
  | { art: 'reihen'; reihen: Array<{ n: number; lattmassMm: number }> }
  | { art: 'absage'; fall: 'neigung' | 'schranke-unbekannt' | 'nicht-teilbar' };
```

*Die Rückgabe ist bewusst eine **Union** und keine Zahl mit Sonderwert: **eine Absage darf nicht als
`0`, `null` oder `NaN` durch die Schichten wandern** — genau so verschwindet sie unterwegs.*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-07 Dach aus Kontur** · **W-08 Dachflächen messen** | Sparrenlänge und Neigung kommen von dort | **ja** — beide `BESCHRIEBEN`, `REGISTER.md:60` nennt sie als Voraussetzung |
| die Ziegelquelle | Bereich und Schranke | **ja, gelesen** — 127 Zeilen, davon 9 nutzbar |
| **W-21L** | ist der **Verbraucher**, nicht die Voraussetzung | ja — einseitig, kein Kreis |

**Kein Import, kein Schema, keine Migration, kein Seeder.** *Die 127 Zeilen bleiben, wo sie sind;
W-23 **beschreibt** das Werkzeug.*
