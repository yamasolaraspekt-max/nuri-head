# W-40 · Gültigkeitsstatus — PRÜFUNG

> **Ablesung mit einer Erweiterung, kein Bau** *(berichtigt W-40/1 — hier stand „Vorgabe, kein
> Bau").* **Die Kriterien unten prüfen das BLATT; die Kriterien für den späteren Bau stehen darunter
> als Vorgabe** — *und die betreffen nach Yamas Einordnung nur noch `blocked`, denn alles andere ist
> gebaut und geprüft.*

## Abnahmekriterien dieses Blattes

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Die **zwei Achsen** stehen mit Zitat | die drei Stufen als Erweiterung von `SchrittStatus` beschreiben | Quelle `:130-132`, am Bau-Stand gelesen |
| K-2 | Je Stufe **nur**, was die Quelle sagt | `blocked` ausschmücken — die Quelle sagt vier Wörter | `:127-130`, Satz für Satz |
| **K-3** *(umgestellt)* | **Das Blatt trägt Yamas Auflösung:** `review-required` **ist** `checked`, und die vier und die drei liegen **nicht auf derselben Achse** | *die Rechnung `4 + 3 = 7` als offene Frage stehen lassen* | **`configuratorPackage.ts:26`** (`checked` in der Union) und **`:107`** (`checked: ['draft','approved','generated']` — der einzige Weg nach `approved`); Blatt: `7-GRENZEN.md`, `3-FORMELN.md` |
| **K-4** *(umgestellt)* | **Das Blatt trägt Yamas Abgrenzung:** `DECISION_BLOCKED` wartet auf einen **Menschen**, `blocked` auf eine **Bedingung** — alle vier Merkmale und die zwei Auflagen | *„die Abgrenzung ist nicht belegt" stehen lassen; oder eine eigene Abgrenzung erfinden statt Yamas zu zitieren* | **`7-GRENZEN.md`**, Abschnitt „`blocked` gegen `DECISION_BLOCKED`"; Herkunft `docs/STATUS.md`, W-40/1-Block |
| K-5 | Übergänge: **abgelesen, nicht erfunden** | eine zweite Tabelle neben der gebauten | **BERICHTIGT (W-40/1):** *hier stand „Vorgabe oder benanntes Fehlen … die Quelle gibt keine her".* **Der Bestand gibt eine her: `configuratorPackage.ts:103-111`** |
| K-6 | Bezug zu W-38 **mit Fundstelle** | „neben SchrittStatus" ohne Zeile | `studioDaten.ts:163` und `:255`, am Bau-Stand |

> **K-3 und K-4 sind UMGESTELLT, nicht gestrichen** *(W-40/1-1b — der gefährlichste Teil).* **Sie
> lauteten:** *K-3 „Die Zahlenlücke ist gestellt, nicht beantwortet", Fehlerfall „eine Erklärung für
> `review-required` erfinden"; K-4 „`blocked` gegen `DECISION_BLOCKED` als OFFENE FRAGE",
> Fehlerfall „eine Abgrenzung behaupten".*
>
> **In dieser Fassung waren sie zum Zeitpunkt der Abnahme grün und sind heute gefährlich:** *Yamas
> Antwort **ist** die Erklärung und **ist** die Abgrenzung.* **Ein Kriterium, das verlangt, sie nicht
> zu haben, verlangt vom nächsten Bauenden, Yamas Entscheidung zu ignorieren — und macht das Blatt an
> dieser Stelle unbaubar.** *Deshalb steht jetzt in beiden dieselbe Sache umgedreht: nicht „die Frage
> stellen", sondern „die Antwort tragen, mit Fundstelle".*

## Was der spätere BAU erfüllen muss — Vorgabe

```text
B-1  Der Gueltigkeitsstatus steht in einem EIGENEN Feld an SEINEM EIGENEN TRAEGER.
     BERICHTIGT (W-40/1, 12.08.) — hier stand „in einem EIGENEN Feld neben dem
     Fortschritt", also beide am selben Gegenstand. Yamas Zuordnung:
       Fortschritt  am SCHRITT   fortschritt: SchrittStatus
       Gueltigkeit  am PAKET     status: ConfiguratorStatus
     Rot-Probe: ein Wert, der beides mischt, macht den Fall „ok UND outdated"
     undarstellbar — und genau der ist der Grund fuer die zweite Achse.
     Zweite Rot-Probe: zwei Felder an EINEM Traeger erfuellen B-1 NICHT.

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
| die drei Stufen in `SchrittStatus` hineinschreiben | **ungemessen** — *B-1 nach dem Bau; die Achse existiert am PAKET, nicht am Schritt* |
| `confirmed` programmseitig setzen | *nach dem Bau: B-2* |
| eine Stufe ohne Beschriftung ergänzen | *nach dem Bau: B-3* — dieselbe Sicherung wie W-38s `Record` |
| einen verbotenen Übergang erlauben | **`configuratorPackage.test.ts:31` und `:41`** — *gebaut und geprüft, nicht Vorgabe* |
| `kannIntegrieren` auch für `checked` öffnen | **`:48`** |

> **Die ersten drei sind VORGABEN und nicht gefahren.** *Wer den Unterschied nicht kennzeichnet,
> verkauft eine Absicht als Messung.* **Die letzten zwei sind der Unterschied, den Yamas Entscheidung
> macht: für den gebauten Teil der Achse gibt es Fänger, für `blocked` keinen.**
>
> **BERICHTIGT (W-40/1, 12.08.):** *in Zeile 1 stand „**kein Test** — heute existiert die Achse
> nicht".* **Sie existiert, nur an einem anderen Träger.** *Ob ein Test diese eine Mutation fängt,
> habe ich nicht gemessen — deshalb „ungemessen" und nicht „kein Test".*

## Automatische Tests

**BERICHTIGT (W-40/1, 12.08.).** *Hier stand:* **„keine — W-40 hat keinen Code und deshalb keine
Tests."** *Das war die „kein Code"-Prämisse, und sie trägt nicht:* **drei der vier Stufen sind gebaut
UND geprüft.** *Am Bau-Stand gemessen, Testnamen statt Zeilenbereichen, weil Bereiche verrotten:*

| Datei · Test | Prüft |
|---|---|
| `configuratorPackage.test.ts:31` *„Statusübergänge: erlaubte Wege gelten, verbotene nicht"* | die Übergänge in **beide** Richtungen |
| `:41` *„Freigabe-Schutz: aus approved/integrated geht es nur über outdated zurück"* | den Grundsatz aus dem Dateikopf |
| `:48` *„kannIntegrieren nur bei approved"* | **das Tor** — der Beleg für `confirmed` |
| `:54` *„markiereVeraltet: freigegebenes Paket wird outdated, Entwurf bleibt unberührt"* | **die Invalidierung** |

*Die Datei trägt sieben Tests; diese vier berühren die Gültigkeitsachse.*

> **Das ist die Form, die ein Bau von `blocked` zu erreichen hat** — *Übergänge in beide Richtungen
> geprüft, nicht nur die erlaubten.* **Ungeprüft ist nur die eine Erweiterung: `blocked` hat 0
> Treffer auf der Insel und deshalb auch 0 Tests.**

## Sichtprüfung und Bestandsprobe

- [ ] **entfallen** — *dieses Blatt ändert kein Dokument, und die abgelesene Achse hat heute keine
      Oberfläche (`2-FUNKTION`, Schicht 4/5: „noch nicht").* **Es gibt nichts zu sehen, weil nichts
      angezeigt wird — nicht, weil nichts gebaut wäre.** *Der Unterschied ist der ganze Inhalt von
      W-40/1.*
