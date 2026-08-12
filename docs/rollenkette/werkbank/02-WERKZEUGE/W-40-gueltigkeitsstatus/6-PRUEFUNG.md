# W-40 · Gültigkeitsstatus — PRÜFUNG

> **Vorgabe, kein Bau.** *Die Kriterien unten prüfen das BLATT — die Kriterien für den späteren Bau
> stehen darunter als Vorgabe.*

## Abnahmekriterien dieses Blattes

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Die **zwei Achsen** stehen mit Zitat | die drei Stufen als Erweiterung von `SchrittStatus` beschreiben | Quelle `:130-132`, am Bau-Stand gelesen |
| K-2 | Je Stufe **nur**, was die Quelle sagt | `blocked` ausschmücken — die Quelle sagt vier Wörter | `:127-130`, Satz für Satz |
| K-3 | Die **Zahlenlücke** ist gestellt, nicht beantwortet | eine Erklärung für `review-required` erfinden | `:117` acht gegen vier, `:121` der Gedankenstrich |
| K-4 | `blocked` gegen `DECISION_BLOCKED` als offene Frage | eine Abgrenzung behaupten | `7-GRENZEN.md` |
| K-5 | Übergänge: **Vorgabe oder benanntes Fehlen** | eine Tabelle erfinden | die Quelle gibt keine her — ausdrücklich festgestellt |
| K-6 | Bezug zu W-38 **mit Fundstelle** | „neben SchrittStatus" ohne Zeile | `studioDaten.ts:163` und `:255`, am Bau-Stand |

## Was der spätere BAU erfüllen muss — Vorgabe

```text
B-1  Der Gueltigkeitsstatus steht in einem EIGENEN Feld neben dem Fortschritt.
     Rot-Probe: ein Wert, der beides mischt, macht den Fall „ok UND outdated"
     undarstellbar — und genau der ist der Grund fuer die zweite Achse.

B-2  confirmed wird NUR durch eine Nutzerhandlung gesetzt.
     Rot-Probe: kann das Programm es selbst setzen, ist es keine Bestaetigung.

B-3  Jede Stufe traegt ein deutsches Wort, erzwungen wie in W-38 durch
     Record<Gueltigkeitsstatus, string>.
     Rot-Probe: eine neue Stufe ohne Wort erscheint als undefined.

B-4  outdated verschwindet nicht still. Was ungueltig wurde, bleibt sichtbar.
     Herkunft: „Aenderungen propagieren, NIEMALS stille Loeschung."

B-5  Die Uebergaenge werden NICHT neu erfunden, solange der Praezedenzfall
     in geometry/configuratorPackage.ts nicht geprueft ist — siehe 7-GRENZEN.
```

> **B-5 ist die Bau-Auflage, die aus meinem eigenen Befund folgt** — *nicht aus der Quelle.* **Sie
> verbietet nichts, sie verlangt eine Prüfung: es gibt bereits eine Übergangstabelle mit einem
> ausdrücklichen Grundsatz, und zwei Tabellen für dieselbe Sache wären die zweite Wahrheit, die
> W-40 verhindern soll.**

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| die drei Stufen in `SchrittStatus` hineinschreiben | **kein Test** — *heute existiert die Achse nicht; nach dem Bau: B-1* |
| `confirmed` programmseitig setzen | *nach dem Bau: B-2* |
| eine Stufe ohne Beschriftung ergänzen | *nach dem Bau: B-3* — dieselbe Sicherung wie W-38s `Record` |

> **Alle drei sind VORGABEN, nicht gefahren** — *es gibt nichts zu mutieren.* **Wer den Unterschied
> nicht kennzeichnet, verkauft eine Absicht als Messung.**

## Automatische Tests

| Datei | Prüft |
|---|---|
| **keine** | *W-40 hat keinen Code und deshalb keine Tests* |

**Die Tests der bereits gebauten Gültigkeitsachse sind trotzdem relevant** — *als Vorbild und als
Warnung:*

```text
__tests__/configuratorPackage.test.ts
  :32-45   die Uebergaenge einzeln, in BEIDE Richtungen (erlaubt und verboten)
  :48-51   das Tor: nur approved darf uebernommen werden
  :54-61   die Invalidierung: ein freigegebenes Paket wird outdated,
           ein Entwurf bleibt unveraendert
```

> **Das ist die Form, die ein Bau von W-40 zu erreichen hat** — *Übergänge in beide Richtungen
> geprüft, nicht nur die erlaubten.*

## Sichtprüfung und Bestandsprobe

- [ ] **entfallen** — *eine Vorgabe zeigt nichts an und ändert kein Dokument.*
