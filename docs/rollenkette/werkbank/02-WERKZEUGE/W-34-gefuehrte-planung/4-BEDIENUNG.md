# W-34 · Geführte Planung — BEDIENUNG

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | **die geführte Planung ist ein Studio-MODUS**, `StudioModus = 'start' \| 'guided' \| 'expert'` (`studioDaten.ts:97`) |
| Tastenkürzel | **keines** |
| Kontextmenü | **nein** |

**Der Anwender ruft nicht W-34 auf, er ist darin.** *Die geführte Planung ist einer von drei Modi;
solange er in ihr steht, ist der Stepper seine Oberfläche.*

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | *öffnet die geführte Planung* | den Stepper mit **elf** Schritten, jeder mit einer Plakette |
| 2 | *wählt einen Schritt* | die Fokus-Schrittkarte: Hinweis, Prüfpunkte, Aufgaben, Empfehlung |
| 3 | *liest einen Prüfpunkt* | Symbol und Farbe nach der Stufe — `✓` bei `ok`, `!` bei `warn`, nichts bei `open`/`prog` |
| 4 | *arbeitet am Gebäude* | beim nächsten Ableiten ändern sich die Zahlen — **niemand pflegt sie von Hand** |

## Die vier Stufen am Bildschirm

**Die Wörter kommen aus W-38 (`STATUS_LABEL`), die Farben aus W-34:**

```ts
GuidedView.tsx:18-21    badgeFarbe: Record<SchrittStatus, { bg; fg }>
  ok   T.okSoft   / T.okInk        prog  T.infoSoft / T.infoInk
  warn T.warnSoft / T.warnInk      open  T.hair2    / T.muted

GuidedView.tsx:22-25    checkFarbe: Record<SchrittStatus, { bg; fg; sym }>
  ok   sym '✓'    warn sym '!'     open sym ''    prog sym ''
```

**`Record<SchrittStatus, …>` an beiden Stellen** — *eine fünfte Stufe in W-38 zwänge hier sofort
eine Farbe und ein Symbol nach; der Übersetzungsfehler wäre ein Typfehler und keine leere Fläche.*

> **Zwei der vier Prüfpunkt-Symbole sind leer**, *und das ist eine Aussage: `open` und `prog` tragen
> **kein** Zeichen.* **Ein Häkchen bei „in Bearbeitung" wäre genau die Behauptung, die dieses
> Werkzeug abschafft.**

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| Alles erfüllt | Plakette **„Vollständig"**, alle Prüfpunkte `✓` | sachlich |
| Teilweise | **„In Bearbeitung"** *(kein eigener Test — der Rest, siehe `2-FUNKTION`)* | hinweisend |
| Ein Zwang verletzt | **„Prüfung erforderlich"**, der betroffene Punkt trägt `!` | hinweisend |
| Nicht begonnen | **„Offen"** | sachlich |
| **Keine Modellgrundlage** | **„Offen"**, **keine** Prüfpunkte, und der Hinweis sagt WAS FEHLT | **erklärend** |

**Die letzte Zeile ist die Absage dieses Werkzeugs**, *und sie ist die einzige.* **Ein Beispiel im
Wortlaut aus dem Code (`:57-58`):**

> *„Bauherr, Adresse und Grundstück stehen im CRM, nicht im Gebäudemodell. Solange der Planer sie
> nicht liest, kann dieser Schritt nichts bestätigen."*

> **Das ist die Form, die `7-GRENZEN.md` für jede der sechs Lücken verlangt** — *nicht
> „DatenGrundlageFehlt", sondern ein Satz, den ein Handwerker versteht, und die Angabe, **welche**
> Angabe fehlt.*

**Bewacht:** *`fahrschritte.test.ts` K6 prüft für jeden der sechs, dass der Status `open` ist, die
Prüfpunktliste **leer**, und der Hinweis **länger als 40 Zeichen*** — *„der Grund ist nicht
erklärt", sagt die Fehlermeldung.* **Und ein zweiter Test verlangt, dass kein Hinweis auf „folgt"
oder „in Kürze" vertröstet.**

## Abbruch

**Gegenstandslos** — *es gibt keinen Vorgang. Der Stepper zeigt an, er führt nichts aus.*

## Tastenkürzel während des Werkzeugs

| Taste | Wirkung |
|---|---|
| **Tab** | wandert durch die Bedienelemente; **im Dialog hält die Fokusfalle** (`dashboard/dialogFokus`, importiert `GuidedView.tsx:3`) |
| Eingabe / Leertaste | löst eine selbstgebaute Schaltfläche aus — **WCAG 2.1.1**, bewacht von `dialogFokus.test.ts` |

## Sichtprüfung — eine Kante ist bereits einmal gebrochen

```text
GuidedView.tsx:36-39, woertlich aus dem Code:
  "AUF-46: Die zweite Spalte war mit `1fr 320px` fest. Bei 390 px passte `1fr + 320 + Luecke`
   nicht mehr; das Aufgaben-aside legte sich ueber den Inhalt und fing die Zeigerereignisse ab —
   eine sichtbare, aber tote Schaltflaeche."
```

**Behoben mit `auto-fit`, und seither bewacht von `breiten.test.ts`:** *„die geführte Planung hat
KEINE feste zweite Spalte mehr — das war die tote Fläche".*

- [ ] 1440 px
- [ ] 1024 px
- [ ] **375 px** — *der Fall, der eingetreten ist; hier lag die tote Fläche*
- [ ] Der Hinweis eines Schrittes ohne Grundlage ist vollständig lesbar
