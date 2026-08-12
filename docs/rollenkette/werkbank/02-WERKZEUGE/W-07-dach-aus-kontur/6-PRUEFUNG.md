# W-07 · Dach aus Kontur — PRÜFUNG

> **Dieses Blatt war bis 12.08. die unveränderte Vorlage** — nicht wegen fehlender Prüfung, sondern
> weil niemand die vorhandene aufgeschrieben hatte. *Der Bestand ist groß: **277 Testzusagen in 15
> Dateien**, jede Zahl unten einzeln gezählt und nicht geschätzt.*

## Was hier zu prüfen ist — die Reihenfolge, nicht die Rechnung

**Das Werkzeug hat eine Schranke, und der Fehler lag nie in der Schranke, sondern darin, dass sie
nicht gehört wurde.** *Bis A-01 wurde das Dach angelegt und erst beim Zeichnen verworfen; beide
Fänger in `szene.ts` schluckten den Wurf, und es blieb ein Knoten mit dem Status `bestaetigt`
zurück, den keine Ansicht kannte.* **Deshalb ist der Prüfgegenstand: fragt die Anwendung die
Schranke, bevor sie das Kommando absetzt?**

| zu belegen | wie |
|---|---|
| **Absage vor dem Kommando** | L-Kontur zeichnen → **kein** `roof`-Knoten, **kein** Status, `ADD_ROOF` nicht abgesetzt |
| **Absage ist lesbar** | der Grund steht in der **Fußleiste**, nicht nur in der Konsole |
| **die richtige Schranke wird gefragt** | die Entscheidung kommt aus `geometry/`, **nicht** aus der gleichnamigen Funktion in `app/tools` |
| **Rechteck geht durch** (`must_preserve`) | Rechteck-Kontur → Dach **mit dieser** Kontur |
| **Fläche und Mesh laufen nicht auseinander** | dieselbe `pruefeRechteckigeKontur` liefert Maße für beide |

## Abnahmekriterien

| Nr | Kriterium | Rot-Beleg vor dem Bau | Wie gemessen |
|---|---|---|---|
| **K-1** | L-Kontur erzeugt **kein** Dach-Objekt und keinen Status | war rot: das Dach wurde angelegt und erst im Renderer verworfen | `dachAusKontur.test.ts:87` |
| **K-2** | Rechteck-Kontur erzeugt ein Dach **mit dieser** Kontur | Gegenprobe zu K-1 — ohne sie wäre „alles ablehnen" grün | `:93` (`must_preserve`) |
| **K-3** | Der Absagegrund ist **lesbar**, in der Fußleiste | war rot: Grund nur in der Konsole | `:132` |
| **K-4** | Nach der Absage wird **kein** Kommando abgesetzt | war rot: `ADD_ROOF` lief trotzdem | `:143` — prüft die **Reihenfolge** von `setDachAbsage` und `return` im Quelltext |
| **K-5** | Die App ruft die Entscheidung aus `geometry/`, nicht die gleichnamige aus `app/tools` | war rot: zwei Rechteckbegriffe, der falsche Zeuge | `:118` (K-05) |
| **K-6** | Rechteck **mit kollinearem Zwischenpunkt** wird **nicht** abgelehnt | war rot: „die FALSCHE Absage" | `:104` (A-01-6) |
| **K-7** | Azimut-Kontrakt ▲D4: Satteldach-Flächen = First ± 90° | Drehprobe §4.2 | `dachGeometrie.test.ts:40` und `:52` |
| **K-8** | Kante 1: L-förmige Traufkontur → `DachGeometrieUngueltig` | — | `dachGeometrie.test.ts:91` |
| **K-9** | Kante 2: 89° bleibt endlich und positiv (`sichererCos`), 0° via Flach stabil | Divisionsschutz ▲D2 | `:104` |
| **K-10** | Kante 3: 500 mm Überstand vergrößert Fläche **und** First-Länge | Traufe **und** Giebel | `:115` |

## Fangprobe (Mutationsprobe)

**Eine Prüfung, die eine absichtlich eingebaute Fehlerstelle nicht findet, prüft nichts.**

| Mutation | Muss erkannt werden von |
|---|---|
| Toleranz `0.01` → `0.5` (fast alles gilt als Rechteck) | **K-1** und **K-8** — die L-Kontur käme durch |
| `throw` in `pruefeRechteckigeKontur` entfernen | **K-1**, **K-4**, **K-8** |
| `setDachAbsage(...)` vor `return` entfernen | **K-3** — der Grund verschwindet, das Dach fehlt trotzdem |
| `return` nach der Absage entfernen | **K-4** — die Reihenfolgeprüfung im Quelltext fällt |
| Flächenazimut = `firstAzimutGrad` (statt ± 90°) | **K-7** — genau der 90°-Fehler, der PV und Heizlast trifft |
| `sichererCos` → `Math.cos` | **K-9** — 89° liefert dann einen Ausreißer statt eines endlichen Werts |

## Automatische Tests

**15 Dateien, 277 Zusagen — je Datei gezählt (`grep -cE '^\s*(test|it)\('`):**

| Datei | Zusagen | Prüft |
|---|---|---|
| `dachformVorlagen.test.ts` | **105** | Dachform-Katalog (1.410 Z.) |
| `dachAusschnitt.test.ts` | **71** | Ausschnitte, `istAchsenRechteck` |
| `dachAusKontur.test.ts` | 15 | **die Absagekette — K-1 bis K-6** |
| `dachVerschneidung.test.ts` | 11 | Verschneidung |
| `dachModell.test.ts` · `dachUForm.test.ts` · `dachWerte.test.ts` | 10 · 10 · 10 | Modell, U-Form, Kennwerte |
| `dachGeometrie.test.ts` | 8 | **Flächen und Azimute — K-7 bis K-10** |
| `dachAufbauten.test.ts` | 8 | Aufbauten |
| `dachMesh.test.ts` · `dachVerschneidungFlaechen.test.ts` | 6 · 6 | Mesh, Verschneidungsflächen |
| `dachProjektion.test.ts` · `dachVorlage.test.ts` | 5 · 5 | Projektion, Vorlage |
| `dachUFormPlatzierung.test.ts` · `dachLTPlatzierung.test.ts` | 4 · 3 | Platzierung U-, L-/T-Form |

## Sichtprüfung (die Oberfläche ist betroffen)

- [ ] 1440 px
- [ ] 1024 px
- [ ] 375 px
- [ ] **Meldung bei Absage lesbar und vollständig sichtbar** — K-3 prüft nur, *dass* sie in der Fußleiste steht, nicht *ob sie hineinpasst*
- [ ] **Näherungshinweis** sichtbar, wenn ohne Kontur gebaut wurde (`setDachNaeherung(true)`)

## Bestandsprobe

- [ ] Ein vor der Änderung gespeichertes Dokument lädt danach unverändert

## Was ich NICHT geprüft habe — als Frage notiert, nicht als Zusage

**Ob die 277 Zusagen tatsächlich grün laufen.** *Ich habe sie **gezählt und ihre Gegenstände
gelesen**, die Suite in diesem Arbeitsgang aber nicht ausgeführt — das ist die Aufgabe des
Evaluators, und ich baue hier in fremder Rolle.* **Zweitens:** ob die Absagemeldung bei 375 px
vollständig sichtbar ist; PB-046 hat für den Objekt-Planer bei 375 px acht Bedienelemente außerhalb
gefunden, und das Muster ist hier ungeprüft. **Drittens:** ob `dachformVorlagen`s 105 Zusagen
überhaupt W-07 betreffen oder überwiegend den Katalog — die Zahl ist gezählt, ihre Zuordnung zu
diesem Werkzeug ist **nicht** geprüft.
