# W-39 · Studio-Rahmen — BEDIENUNG

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | **keine** — W-39 **ist** die Umgebung, nicht ein Werkzeug darin |
| Modusschalter | drei Schaltflächen im Kopf, `:109-113` |
| Tastenkürzel | **keines für den Moduswechsel** |

## Der Kopf — was dauerhaft sichtbar ist

```text
:106-118   <header className="hp-studio-kopf">
  :107     Speicher-Plakette:  Punkt in der Farbe der Gewichtung + Text
                               + " · Rev. N"  NUR wenn eine Szene da ist UND
                                 gespeichert werden kann
  :108     Fueller
  :109-113 Modusschalter:      Uebersicht · Gefuehrte Planung · Expertenmodus
  :117     Namenskuerzel „YS"
```

**Die Plakette sagt die Wahrheit auch dort, wo nicht gespeichert werden kann** — *`scene &&
kannSpeichern` entscheidet, ob die Revision überhaupt erscheint (`:107`).* **Der Dateikommentar
`:49-54` nennt den Fall, der dazu geführt hat:** *eine zweite Statusanzeige sagte „Gespeichert ·
Rev. 1" direkt neben dem Hinweis „Testfläche — wird NICHT gespeichert".*

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | *öffnet den Hausplaner* | Modus `start` — die Startseite (W-33) |
| 2 | *klickt „Geführte Planung"* | den Stepper (W-34), Schritt und Modell werden mitgegeben |
| 3 | *klickt „Expertenmodus"* | die volle `HausplanerApp`, **eingebettet und unverändert** |
| 4 | *klickt „Geführte Planung" zurück* | denselben Stepper — **dasselbe Modell, dieselbe Revision** |

**Schritt 4 ist die eigentliche Leistung:** *der Wechsel ändert nur `modus` (`:23`). Szene,
Speicherstand und Revision liegen in den Stores und werden vom Wechsel nicht berührt.*

## Die Entwurfsentscheidung zum Erklärtext

**Vorher** *stand ein Erklärtext als dauerhafte Leiste über der Bühne.* **Jetzt** *trägt ihn der
Modusschalter als Titel* (`:112`):

> *„Experte — alle Werkzeuge, Projektbaum und Eigenschaften. Dasselbe Modell und dieselbe
> Revision."*

**Die Begründung steht im Code, `:135-139`:** *er „beantwortete eine Frage, die man **genau einmal**
hat" — und kostete dafür in jeder Sitzung eine Zeile Bühne.*

> **Und der Satz, der dabei am wichtigsten ist:** *„**Der Weg zurück in die geführte Planung ist
> nicht verschwunden** — er steht als eigener Schalter im Kopf, sichtbar in jedem Modus (K-05)."*
> **Nachgemessen: alle drei Schalter stehen in einem `<div className="hp-modusschalter">` (`:109`),
> und die Kopfzeile hängt nicht vom Modus ab.** *Es gibt keinen Modus, in dem der Rückweg fehlt.*

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| gespeichert | Plakette grün, Text aus `speicherAnzeige` | sachlich |
| kein Speicherziel | Plakette **ohne** Revision — *„Gespeichert" erscheint nicht* | erklärend |
| Konflikt | Farbe nach `AnzeigeArt` (`:58`: `ok`/`warnung`/`neutral`/`fehler`) | hinweisend |
| Konfigurator übernommen | **Toast**, 2600 ms (`:70`, `:146-148`) | bestätigend |
| Modul ohne Konfigurator | **kein Toast mehr** — stattdessen die Fachplaner-Fläche (`:80-82`) | erklärend |

**Die letzte Zeile ist eine abgeschaffte Vertröstung:** *`:80` sagt es wörtlich —* **„Kein Toast
mehr: das Modul bekommt seine Fläche mit der Feldstruktur des späteren Panels."**

## Abbruch

- **Konfigurator:** `onClose` setzt `konfig` auf `null` (`:150`) — *nichts wird übernommen.*
- **Fachplaner-Fläche:** `onZurueck` setzt `fachOffen` auf `null` (`:155`).
- **Der Zurück-Weg trägt seine Herkunft mit** (`:27-29`, `:74`): *`herkunft` bestimmt die
  Beschriftung, damit nie pauschal „zur Startseite" dasteht, wenn man aus der Navigation kam.*

## Tastenkürzel und Fokus

| Taste | Wirkung |
|---|---|
| **Tab** | wandert durch Kopf und Bühne; im Dialog hält die Fokusfalle (`istAusloeser`, `:8`) |
| Eingabe / Leertaste | löst selbstgebaute Schaltflächen aus — bewacht von `dialogFokus.test.ts` |

**Der Fokus ist über das ganze Studio sichtbar gemacht** (`:99-100`):

```ts
<style>{`.hp-studio :focus-visible{outline:2px solid ${T.accent};outline-offset:2px;…}`}</style>
```

## Sichtprüfung — eine Kante ist bereits gebrochen

```text
:102-105, woertlich aus dem Code:
  "AUF-46: Die Kopfzeile war auf 62 px Hoehe festgenagelt und durfte nicht umbrechen — bei
   390 px schob sie Titel, Status, Moduswechsel und Namenskuerzel ueber den rechten Rand und
   riss die ganze Seite in den waagerechten Ueberlauf (gemessen: scrollWidth 656 bei 390).
   Jetzt: umbrechen statt schieben, Mindesthoehe statt fester Hoehe."
```

- [ ] 1440 px · 1024 px · **375 px**
- [ ] Die Kopfzeile bricht um, statt zu schieben — *bewacht von `breiten.test.ts`*
- [ ] Der Modusschalter ist in **jedem** Modus sichtbar
