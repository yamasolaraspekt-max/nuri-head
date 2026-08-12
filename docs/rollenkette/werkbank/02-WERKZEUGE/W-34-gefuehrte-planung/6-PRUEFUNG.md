# W-34 · Geführte Planung — PRÜFUNG

> **Bei einer Ablesung wäre „rot" nicht der fehlende Code, sondern die falsche Ablesung.** *Die
> Spalte nennt deshalb, welche Behauptung das Blatt widerlegen würde.*

## Abnahmekriterien

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | `statusAus` mit **allen fünf** Zweigen **und der Reihenfolge** | die Zweige aufzählen, ohne zu sagen, dass `warn` vor den `every`-Prüfungen steht | `fahrschritte.ts:43-49` gezeigt, nicht beschrieben |
| K-2 | **elf** Schritte, am Code gezählt | die Zahl aus `fahrschritte.test.ts` übernehmen | Einträge des `return`-Arrays `:124-196`, Kommentarzeilen ausgenommen |
| K-3 | **sechs** Lücken vollständig, je mit „was fehlt" | „sechs Schritte ohne Grundlage" ohne die sechs Sätze | `Object.keys(SCHRITTE_OHNE_GRUNDLAGE)`, alle sechs in `7-GRENZEN.md` |
| K-4 | `bebauteGeschosse` mit **Begründung aus dem Code** | die Regel nennen, ohne zu sagen, warum sie nötig ist | `:77-88`, wörtlich zitiert |
| K-5 | Der Anschluss an W-38 **mit Fundstelle** | „benutzt W-38s Typen" ohne Zeile | `GuidedView.tsx:4`, `:15`, `:18`, `:22`, `:71` · `fahrschritte.ts:27` |
| K-6 | Die fünf Wächter **je mit ihrer Zusage** | „fünf Tests" allein | unten, jeder Test geöffnet |

## Die fünf Wächter — was bewacht welcher

**`fahrschritte.test.ts` · 12 Tests — die Ableitung selbst**

```text
K5  ein LEERES Dokument liefert keinen gruenen Schritt und keinen gruenen Pruefpunkt
K5  Gegenprobe an den stillgelegten Demo-Daten: DIE behaupteten gruen
K3  zweimal mit demselben Dokument ⇒ tief gleiches Ergebnis (keine Zeit, kein Zufall)
K2  die Ableitung LIEST das Dokument — sie aendert es nicht
K4  elf Schritte, Titel und Reihenfolge unveraendert gegenueber den stillgelegten Daten
K6  ein Schritt ohne Modellgrundlage sagt WAS fehlt und traegt keine erfundenen Pruefpunkte
K6  kein Hinweis ist leer, keiner vertroestet auf „folgt" oder „in Kuerze"
```

> **Der zweite K5-Test ist der schärfste des ganzen Werkzeugs:** *er prüft nicht den neuen Code,
> sondern hält die **alte Lüge** als Vergleichsgrundlage fest.* **Die stillgelegten Demo-Daten
> behaupteten grün — und der Test beweist, dass die Ableitung es nicht mehr tut.** *Ein Test, der
> ohne die stillgelegte Konstante (W-38) nicht möglich wäre.*

**`gefuehrteEhrlich.test.ts` · 8 Tests — die Wörter und die leere Karte**

```text
K3  kein Statuswort behauptet mehr eine Freigabe
K4  die SCHLUESSEL sind unveraendert — geaendert wurde das Wort, nicht der Wert
K4  kein Schritt hat seinen Status gewechselt
K6  eine leere Aufgabenliste hinterlaesst KEINE leere Ueberschrift
K7  eine nicht-leere Liste rendert unveraendert — Zeichen fuer Zeichen
    gemessen: KEIN Schritt traegt heute Aufgaben — die leere Karte ist der Regelfall
```

**`breiten.test.ts` · 5 Tests — die tote Fläche**

```text
die gefuehrte Planung hat KEINE feste zweite Spalte mehr — das war die tote Flaeche
keine der vier Flaechen traegt noch eine feste Spaltenbreite in der Grid-Vorlage
```

**`dialogFokus.test.ts` · 11 Tests — Tastatur und Fokus**

```text
die Falle schlaegt an beiden Raendern um — sonst fuehrt Tab aus dem Dialog heraus
eine selbstgebaute Schaltflaeche loest auf Enter UND Leertaste aus (WCAG 2.1.1)
tabindex="-1" gehoert NICHT in die Falle
```

**`stilschicht.test.ts` · 58 Tests — Farben nur aus Tokens**

```text
K5  jede Variable traegt einen Wert aus studioDaten.ts — keine Konstante daneben
K4  die CSS-Quelle enthaelt in KEINER Regel einen Farbwert
```

> **`stilschicht` und `dialogFokus` bewachen nicht W-34 allein**, *sie decken die ganze Insel ab —
> aber sie halten Zusagen, ohne die `GuidedView` seine Farben und seine Tastaturbedienung
> verlöre.* **Genannt, weil W-34-6 fragt „was bewacht welcher", und die ehrliche Antwort
> unterscheidet zwischen eigenen und geteilten Wächtern.**

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| in `statusAus` Zweig 2 (`warn`) **hinter** die `every`-Prüfungen schieben | *ein Schritt mit einem `warn` und sonst `ok` meldete `ok`* — `fahrschritte.test.ts` K5 |
| einen Eintrag aus `SCHRITTE_OHNE_GRUNDLAGE` entfernen | `fahrschritte.test.ts:98` (`… .length === 6`) |
| einen zwölften Schritt ergänzen | `fahrschritte.test.ts:37` und `:71` (je `11`) |
| `bebauteGeschosse` durch `geschosse` ersetzen | *ein leeres Projekt meldete „1 Geschoss bebaut"* — K5 |
| `checks: []` durch einen erfundenen `ok`-Punkt ersetzen | K6 (`checks` muss leer sein) |

> **Diese fünf sind ABGELESEN, nicht gefahren.** *Ich habe die Wächterzeilen gelesen und benannt,
> welche Mutation sie fangen **würden** — eine Mutationsprobe im Code habe ich für W-34 **nicht**
> ausgeführt.* **Wer den Unterschied nicht kennzeichnet, verkauft eine Herleitung als Messung.**

## Automatische Tests

| Datei | Tests | Prüft |
|---|---|---|
| `__tests__/fahrschritte.test.ts` | 12 | die Ableitung: Leerdokument, Reinheit, elf Schritte, die sechs Lücken |
| `__tests__/gefuehrteEhrlich.test.ts` | 8 | Statuswörter, leere Aufgabenkarte |
| `__tests__/breiten.test.ts` | 5 | keine feste zweite Spalte |
| `__tests__/dialogFokus.test.ts` | 11 | Fokusfalle, Tastaturauslösung |
| `__tests__/stilschicht.test.ts` | 58 | Farben nur aus Tokens |

## Sichtprüfung

- [ ] 1440 px · 1024 px · **375 px**
- [ ] Der Hinweis eines Schrittes ohne Grundlage ist vollständig lesbar

*Bei 375 px lag die tote Fläche (`GuidedView.tsx:36-39`) — das ist die Breite, die zählt.*

## Bestandsprobe

- [ ] **entfällt** — *W-34 schreibt nicht ins Dokument; `fahrschritte.test.ts` K2 prüft genau das
      („die Ableitung LIEST das Dokument — sie ändert es nicht").*
