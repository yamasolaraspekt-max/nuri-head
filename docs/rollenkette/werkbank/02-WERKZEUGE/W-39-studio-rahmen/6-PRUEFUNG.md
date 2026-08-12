# W-39 · Studio-Rahmen — PRÜFUNG

> **Bei einer Ablesung wäre „rot" nicht der fehlende Code, sondern die falsche Ablesung.**

## Abnahmekriterien

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Die **additive** Bauart ist benannt | W-39 als „neue Oberfläche" beschreiben | Zitat `:2-5`, Flag-Fundstelle `:140` |
| K-2 | Die **drei** Modi mit ihren Zeilen | ein Suchmuster auf `'expert'` als Nachweis | `:131` `:132` `:133`, und `imExperte` in `:85` gezeigt |
| K-3 | Zustände und Stores **am Code gezählt** | die Zahl aus dem Auftragsblatt übernehmen | `React.useState` 5 · `useRef` 1 · Store-Felder 4 + 1 |
| K-4 | `modeBtn` samt Entwurfsentscheidung | die Fabrik nennen, ohne zu sagen, warum der Erklärtext wanderte | Zitat `:135-139`, Rückweg in `:109-113` nachgemessen |
| K-5 | Die **acht** Wächter je mit Zusage | „acht Tests" allein | unten, jede Datei geöffnet |
| K-6 | Die dreizehn Module mit Werkzeug-Zuordnung | „benutzt viele Module" ohne Liste | `5-CODE/LIESMICH.md`, Reifegrad aus dem REGISTER |

## Die acht Wächter — was bewacht welcher

**Alle acht nennen `HausplanerStudio` namentlich** — *gemessen mit
`grep -l 'HausplanerStudio' __tests__/*.test.ts*`, acht Treffer, genau die acht des Blattes.*

**`breiten.test.ts` · 5 Tests — die tote Fläche**

```text
die gefuehrte Planung hat KEINE feste zweite Spalte mehr — das war die tote Flaeche
der Konfigurator ebenso — dieselbe Ursache, dieselbe Behebung
die Startseite legt so viele Spalten an, wie passen — nicht drei um jeden Preis
DIE KOPFZEILE BRICHT UM, STATT ZU SCHIEBEN     <- der Fall aus :102-105
```

**`speicherAnzeige.test.ts` · 10 Tests — die Plakette darf nicht lügen**

```text
ohne Speicherziel sagt die Plakette die Wahrheit — in JEDEM Zustand
„Gespeichert" steht NIE auf einer Flaeche, die nicht speichern kann
gesperrt ist der Knopf nur dort, wo Druecken schaden oder taeuschen wuerde
```

> **Das ist der Wächter zum Dateikommentar `:49-54`** — *dem Fall, den Yama in der Sichtprobe
> gesehen hat: „Gespeichert · Rev. 1" direkt neben „Testfläche — wird NICHT gespeichert".*

**`fussleistenEhrlich.test.ts` · 7 Tests — Yamas Maßstab**

```text
keine der beiden Fussleisten verspricht noch etwas
und auch sonst steht in keiner der beiden Dateien eine Vertroestung als Anzeige
die Schiene zeigt den Satz ihres eigenen Reiters — wiederverwendet, nicht erfunden
```

**Der Maßstab wörtlich, aus dem Dateikopf `:9` und `:14-15`:**

> *„**Der Maßstab ist derselbe: sagen, was da ist, statt zu versprechen, was kommt.**"*
>
> *„Die Studio-Navigation **zählt** aus `PROJ` und `FACH`. **Eine gezählte Zahl kann nicht
> veralten; eine abgetippte schon.**"*

> **Der zweite Satz ist die Umkehrung dessen, was heute dreimal zugeschlagen hat:** *A-21-3s feste
> Zahl wuchs von 13 auf 15, A-22-1s fiel von 17 auf 14, A-22-2b brauchte drei Fassungen.* **Der Code
> macht es hier richtig vor — er zählt, statt zu behaupten.** *Deshalb steht der Satz im Blatt und
> nicht nur im Test.*
>
> **Eine Einordnung, die dazugehört:** *der zitierte Satz beschreibt die Studio-Navigation, und die
> ist inzwischen ausgebaut (`:122-128`, AUF-83-T2).* **`PROJ` und `FACH` stehen unverändert in
> `studioDaten.ts`; gefallen ist die Darstellung als Baum, nicht der Inhalt.** *Der Maßstab gilt
> weiter, sein damaliger Anwendungsfall nicht mehr.*

**`dialogFokus.test.ts` · 11 Tests — Tastatur und Fokus**

```text
die Falle schlaegt an beiden Raendern um — sonst fuehrt Tab aus dem Dialog heraus
ein Dialog ohne fokussierbaren Inhalt laesst die Rechnung nicht abstuerzen
```

**`projektKlick.test.ts` · 15 Tests — ein Klick, nicht zwei**

```text
K2: jeder Eintrag traegt die Adresse seines eigenen Objekts
K2: ein Klick, nicht zwei — der Eintrag selbst ist der Verweis
K3: der dominante Eintrag ist ERSTER in der Tastfolge
```

**`arbeitszeileSuche.test.ts` · 7 Tests — genau ein Ort öffnet die Palette**

```text
K-05b: die Arbeitszeile traegt einen sichtbaren Einstieg in die Palette
K-05b (Grenze): es gibt weiterhin GENAU EINEN Ort, der die Palette oeffnet
```

**`fachFlaechen.test.ts` · 9 Tests — gemessen, nicht abgeschrieben**

```text
19 Flaechen — gemessen, nicht abgeschrieben (der Fahrplan sagte 20)
Deckung in BEIDE Richtungen: kein Modul ohne Flaeche, keine Flaeche ohne Modul
jede id ist eindeutig und schluesseltauglich (Kante 1: ss, Umlaut, Leerzeichen)
```

> **Der erste Test ist derselbe Gedanke wie Yamas Maßstab, eine Ebene tiefer:** *der Fahrplan sagte
> 20, gemessen sind es 19 — und der Test hält die gemessene Zahl fest, nicht die behauptete.*

**`stilschicht.test.ts` · 58 Tests — Farben nur aus Tokens**

```text
K5: jede Variable traegt einen Wert aus studioDaten.ts — keine Konstante daneben
K4: die CSS-Quelle enthaelt in KEINER Regel einen Farbwert
```

> **`stilschicht`, `dialogFokus` und `breiten` sind GETEILTE Wächter** — *sie decken die ganze Insel
> ab.* **`speicherAnzeige`, `fussleistenEhrlich`, `projektKlick`, `arbeitszeileSuche` und
> `fachFlaechen` treffen den Rahmen unmittelbar.** *Der Unterschied gehört genannt, weil „acht
> Tests" sonst mehr verspricht, als es hält.*

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| die Markenzeile im Studio einblenden | **`kopfrahmen.test.ts:138-147`** — *und dieser Test kennt die Mutation namentlich* |
| `height: imStudio ? …` verändern | **`buehnenBreite.test.ts:76-86`** |
| die Kopfzeile wieder auf feste Höhe setzen | `breiten.test.ts` („die Kopfzeile bricht um") |
| `kannSpeichern` aus `:107` streichen | `speicherAnzeige.test.ts` („Gespeichert steht NIE …") |
| den `guided`-Schalter aus `:111` entfernen | **kein Test** — K-05 ist nur im Kommentar belegt |
| einen vierten Modus ergänzen ohne Schalter | **kein Test** — `StudioModus` erzwingt nur den Typ |

> **Hier stand zuerst, das Flag `imStudio` sei durch keinen Test gesichert. Das war falsch, und ich
> habe es gemessen statt es stehen zu lassen:** *`grep -rl imStudio __tests__/` findet **zwei**
> Dateien.* **Sie nennen `HausplanerStudio` nicht und gehörten deshalb nicht zu den acht — sie
> sitzen am EMPFANGENDEN Ende, in `Kopfrahmen.tsx`.**

```text
kopfrahmen.test.ts:138   test('K-03 (Bindung): die Marke steht NUR ausserhalb des Studios')
  woertlich im Test:  "Die Mutation {!imStudio && ( -> {imStudio && ( kam DURCH. Im Studio
                       gibt es kein Objekt; eine Marke „Hausplaner · Solar Aspekt" ueber einer
                       Testflaeche waere genau die Anzeige, die AUF-40 entfernt hat —
                       nur andersherum."
  :147  assert.doesNotMatch(kopf, /\{imStudio && \(/)
```

> **Ein Wächter, der aus einer durchgekommenen Mutation entstanden ist** — *er prüft nicht nur, dass
> die Bedingung da ist, sondern auch, dass sie **nicht umgedreht** wurde.* **Die additive Bauart von
> W-39 ist damit belegt gesichert, nur eben von der anderen Seite der Grenze.**

**Alle sechs Zeilen sind ABGELESEN, nicht gefahren.** *Zwei Lücken bleiben und stehen in
`7-GRENZEN.md`: der Rückweg-Schalter (K-05) und ein vierter Modus sind durch keinen Test gesichert.*

## Automatische Tests

| Datei | Tests | trifft W-39 |
|---|---|---|
| `stilschicht.test.ts` | 58 | geteilt |
| `projektKlick.test.ts` | 15 | unmittelbar |
| `dialogFokus.test.ts` | 11 | geteilt |
| `speicherAnzeige.test.ts` | 10 | unmittelbar |
| `fachFlaechen.test.ts` | 9 | unmittelbar |
| `arbeitszeileSuche.test.ts` | 7 | unmittelbar |
| `fussleistenEhrlich.test.ts` | 7 | unmittelbar |
| `breiten.test.ts` | 5 | geteilt |

## Sichtprüfung

- [ ] 1440 px · 1024 px · **375 px** — *bei 390 px riss die Kopfzeile einmal die ganze Seite in den
      waagerechten Überlauf (`:102-105`, gemessen `scrollWidth 656`)*
- [ ] Der Modusschalter ist in **jedem** Modus sichtbar
- [ ] Im Expertenmodus erscheint die Markenzeile der `HausplanerApp` **nicht** — *bewacht von
      `kopfrahmen.test.ts:138`, die Sichtprüfung bestätigt nur*

## Bestandsprobe

- [ ] **entfällt** — *W-39 schreibt nicht ins Dokument. Der Moduswechsel ändert `modus` und sonst
      nichts; Szene und Revision liegen im Store.*
