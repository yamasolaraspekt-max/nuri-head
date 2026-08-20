# S-1 — Anschlussmessung über die 37 `BESCHRIEBEN`-Zeilen

**Auftrag:** *„AN DEN PLANNER"*, 19.08., Schritt 1. **Kein Bau. Eine Liste.**
**Messstand:** `d0d62e49` · gemessen 19.08. abends · Rolle: Generator (Rollenwechsel auf Yamas
ausdrückliche Anweisung; die Abnahme liegt bei einer anderen Instanz).

---

## 0 · Was diese Messung beantworten kann — und was der Auftrag nicht wusste

Der Auftrag verlangt je Zeile drei Fragen (A angeschlossen · B soll es · C was fehlt). **Frage A
lässt sich nicht je Zeile beantworten, weil es keine Brücke von der Registerzeile zur
Registry-Kennung gibt.** Gemessen:

```
Registerzeilen, die eine Registry-Kennung nennen      2 von 37   (W-03 trimmen, W-11 auswahl)
Werkbank-Verzeichnisse, die 'toolRegistry' nennen    15 von 43
```

**Deshalb ist A hier über den Code beantwortet statt über die Zeile**, und zwar nach dem Maßstab,
den A-35 gesetzt hat: *hat das Modul, das die Blätter der Zeile führen, einen **Produktivaufrufer**
oder nur Tests?* `geradenGeometrie` hatte null, bis A-35 einen machte — das ist der gemessene
Unterschied zwischen „gebaut" und „angeschlossen".

**Zählbefehl statt Zahl:**

```bash
grep -rlE "from '[^']*<modul>'" --include='*.ts' --include='*.tsx' \
  resources/planner/hausplaner | grep -v __tests__ | grep -v "/<modul>\."
```

**Und eine Trennung, die der Auftrag noch nicht hatte:** *Zeile angeschlossen* und *Leitmodul
benutzt* sind zwei verschiedene Aussagen. W-10 „Decke und Boden" ist als Werkzeug erreichbar
(`decke` steht in der Registry) — **und das Modul, das seine Blätter führen, ist trotzdem tot.**
Die Tabelle unten trennt das.

---

## 1 · Frage B hat eine mechanische Antwort, und sie steht schon im Code

Der Auftrag fragt in §8, wozu der Werkzeugkasten da ist, und will es als Ordnungskriterium.
**`app/tools/faehigkeiten.ts` trägt es als getipptes Feld:**

| `FaehigkeitArt` | Bedeutung | Zahl |
|---|---|---|
| `'werkzeug'` / `'aktion'` | der Nutzer klickt es, es setzt `activeToolId` | **13**, alle erreichbar |
| `'engine'` | reine Eingang→Ergebnis-Rechnung, von einer Fläche aufgerufen | **13**, davon 8 mit Panel |

**26 Fähigkeiten, 21 `verfuegbar`, 5 `in_entwicklung`.** Die dritte Gruppe ist **absichtlich leer**
(`werkzeugKatalogFaehigkeiten = []`), mit der Begründung im Code: *„Bis I2 spiegelte die Navi eine
Teilmenge des Katalogs als `cad-*`. Das waren anklickbare Zeilen ohne Handler — falsche
Versprechen (AUF-28)."*

> **Berichtigung zur Vorlage:** sie nennt *„9 `verfuegbar` · 1 `schlaeft`"*. **`'schlaeft'` ist kein
> Wert** — `FaehigkeitZustand` kennt `verfuegbar · voraussetzung · nur_ergebnis · in_entwicklung`;
> das Wort steht nur in drei Kommentaren aus Batch 0. Und die 9 zählt Zeichenketten, nicht
> Fähigkeiten: die dreizehn Werkzeuge kommen über `TOOL_DEFINITIONS.map()` und stehen nirgends als
> Literal.

---

## 2 · Der Befund: sechs Module ohne jeden Produktivaufrufer

**Jedes einzeln gegengeprüft** — nicht nur der Import gezählt, sondern jede Nennung im Baum
angesehen:

| Zeile | Modul | Zeilen | Produktiv­aufrufer | Lage |
|---|---|---|---|---|
| **W-10** | `renderers/three-d/deckenMesh.ts` | — | **0** | **keine einzige Nennung im Baum.** `szene.ts:451-478` rendert die Decken inline über `bodenPunkteThree` aus `platzierung` |
| **W-13** | `geometry/trefferSuche.ts` | — | **0** | einzige Nennung: `werkzeugLandkarte.ts:248`, Vertrag `suche`, Marke `ohne-modell` |
| **W-20** | `geometry/holzMengen.ts` | — | **0** | als `engine-holzmengen` registriert, `zustand: 'in_entwicklung'` — sichtbar, nicht aufgerufen |
| **W-21** | `geometry/auswechslung.ts` | — | **0** | nur Kommentare in `sparrenTrennung.ts` und `dachOeffnung.ts` |
| **W-25 / W-43** | `geometry/holzBauteile.ts` | **82** | **0** | als `engine-holzbauteile` registriert, `in_entwicklung`; sonst nur Kommentare |
| **W-30** | `geometry/dachVorlage.ts` | **34** | **0** | **keine Nennung außerhalb der eigenen Datei und eines Tests** |

**Das sind die Kandidaten für den Anschlussvorrat aus Schritt 3 — gemessen, nicht geschätzt.**
Drei davon (`holzMengen`, `holzBauteile`, `dachVorlage`) gehören zum Holzbau/Dach und hängen
zusammen; `deckenMesh` ist ein Sonderfall, weil dort nicht der Anschluss fehlt, sondern **ein
zweiter Weg gebaut wurde** und der erste liegen blieb.

---

## 3 · Die übrigen 31 Zeilen: Leitmodul hat Produktivaufrufer

| Zeile | Leitmodul | Aufrufer | erste Fundstelle |
|---|---|---|---|
| W-01 | `fangKern` | 1 | `app/HausplanerApp.tsx` |
| W-02 | `wallGeometry` | **12** | `renderers/three-d/segmentierung.ts` |
| W-03 | `EigenschaftenPanel` | 1 | `app/HausplanerApp.tsx` |
| W-04 | `oeffnungsTypen` | 2 | `app/HausplanerApp.tsx` |
| W-05 | `roomDetection` | 5 | `renderers/three-d/szene.ts` |
| W-06 | `geschossStapel` | 3 | `app/HausplanerApp.tsx` |
| W-07 | `dachGeometrie` | 5 | `renderers/three-d/szene.ts` |
| W-08 | `polygonFlaeche` | 4 | `renderers/three-d/deckenMesh.ts` ⚠ *(der einzige Aufrufer ist selbst tot — siehe W-10)* |
| W-09 | `treppenBerechnung` | 5 | `app/dashboard/enginePanels.ts` |
| W-11 | `masseingabe` | 1 | `app/HausplanerApp.tsx` |
| W-12 | `szene` | 1 | `app/DreiDBereich.tsx` |
| W-14 | `editierGeometrie` | 5 | `app/sammelBefehle.ts` |
| W-16 | `kalibrierung` | 2 | `app/unterlage/UnterlagenWerkzeuge.tsx` |
| W-17 | `speicherAnzeige` | 3 | `app/HausplanerApp.tsx` |
| W-18 | `kontur` | 1 | `app/HausplanerApp.tsx` |
| W-22 | `gaubeGeometrie` | 2 | `renderers/three-d/dachAufbautenMesh.ts` |
| W-23 | `dachformVorlagen` | 1 | `renderers/three-d/dachMesh.ts` |
| W-26 | `aufbauOrientierung` | 1 | `geometry/gaubeGeometrie.ts` |
| W-28 | `linienBauteile` | 2 | `geometry/dachAusschnitt.ts` |
| W-29 | `dachOeffnung` | 1 | `geometry/dachAusschnitt.ts` |
| W-31 | `pvBelegung` | 1 | `app/dashboard/enginePanels.ts` |
| W-33 | `StartView` | 1 | `app/HausplanerStudio.tsx` |
| W-34 | `GuidedView` | 1 | `app/HausplanerStudio.tsx` |
| W-35 | `ConfigWizard` | 3 | `app/HausplanerStudio.tsx` |
| W-36 | `FaehigkeitenNavi` | 1 | `app/rahmen/GruppenzeileUndSchiene.tsx` |
| W-37 | `EngineFlaeche` | 2 | `app/HausplanerApp.tsx` |
| W-38 | `studioDaten` | **22** | `app/StartView.tsx` |
| W-39 | `HausplanerStudio` | 1 | `main.tsx` |
| W-40 | `configuratorPackage` | 2 | `app/ConfigWizard.tsx` |
| W-42 | `paketSpeichern` | 2 | `main.tsx` |

**W-08 ist der Nachsatz, der die Zahl relativiert:** `polygonFlaeche` hat vier Aufrufer, und der
erste ist `deckenMesh` — ein Modul, das selbst niemand aufruft. **Eine Aufruferzahl ist kein Beleg
für Erreichbarkeit, solange die Aufrufer nicht selbst erreichbar sind.** Das ist die Grenze dieser
Messung und sie wird hier genannt, nicht verschwiegen.

---

## 4 · Was diese Liste NICHT ist

- **Keine Lückenzahl.** Es steht nirgends „24 fehlen". Es steht: sechs Module haben null
  Produktivaufrufer, einzeln belegt.
- **Kein Bauauftrag.** Ob ein totes Modul angeschlossen oder gestrichen gehört, ist der nächste
  Schritt, nicht dieser. `dachVorlage.ts` mit 34 Zeilen ist ein anderer Fall als `holzBauteile.ts`
  mit 82 und einer Registrierung als Engine.
- **Keine Erreichbarkeitsprüfung bis zur Oberfläche.** Gemessen ist die Importkette, nicht der
  Klick. Der W-08-Nachsatz zeigt, warum das ein Unterschied ist. **Für die sechs Kandidaten ist es
  egal — null Aufrufer heißt null Kette.** Für die einunddreißig ist es eine offene Verschärfung.

---

# S-1/2 — die Verschärfung: erreichbar statt „hat Aufrufer"

*Nachgereicht 19.08. gegen `4699f0e6`. Der Nachsatz zu W-08 oben verlangte sie, hier ist sie.*

## Das Verfahren, und warum es dem Zählen überlegen ist

Nicht Aufrufer gezählt, sondern **die Importkette vom Bündel-Einstieg aus gezogen** — transitiv,
mit Auflösung endungsloser Pfade (`.ts`/`.tsx`/`index.*`). Der Einstieg ist belegt, nicht vermutet:

```
vite.hausplaner.config.ts:21   input: resolve(__dirname, "resources/planner/hausplaner/main.tsx")
```

**Methodenprobe vorweg**, damit die Kette nicht Leeres prüft: `wallGeometry`, `szene`,
`faehigkeiten`, `toolRegistry`, `HausplanerApp` müssen erreichbar sein — **fünf von fünf sind es.**
Handprobe an einem, der es nicht sein darf: `dachTopologie` hat null Nennungen außerhalb der Tests,
unabhängig von der Kette und deckungsgleich mit ihr **und** mit dem, was die Registerzeile W-27
selbst sagt.

```
Module ohne Tests            165
davon von main.tsx erreichbar 137
NICHT erreichbar               25   (plus 5 DOM-Testdateien, hier nicht gezählt)

Zeilen unerreichbar         3.105   von 29.361  =  10,6 %
```

## Der Befund: die Importzählung hat die Lage beschönigt

Sechs Module fand die erste Runde. **Es sind fünfundzwanzig.** Der Grund ist genau der W-08-Fall,
jetzt für alle aufgelöst: `dachOeffnung` hat einen Aufrufer und `dachAusschnitt` zwei — **und beide
Aufrufer sind selbst unerreichbar.** Eine Aufruferzahl misst Nachbarschaft, keine Erreichbarkeit.

## Und zuerst das, was HÄLT — dreizehn von dreizehn

**Jede Engine, die `verfuegbar` heißt, ist erreichbar. Jede, die `in_entwicklung` heißt, ist es
nicht. Ausnahmslos.**

| `zustand` | Engines | Modul erreichbar |
|---|---|---|
| `verfuegbar` | fbh · heizkoerper · abwasser · kueche · pv · fensterprodukt · sparren · treppe | **8 von 8 ja** |
| `in_entwicklung` | heizkreis · uwert · holzmengen · holzbauteile · schifter | **5 von 5 nein** |

**Das Feld ist keine Dekoration — es sagt die Erreichbarkeit exakt voraus.** Die Lehre aus AUF-28
(*„anklickbare Zeilen ohne Handler sind falsche Versprechen"*) ist eingehalten: **die Navi
verspricht nichts, was sie nicht hat.** Das gehört genannt, bevor die 25 gezählt werden.

## Die 25, klassifiziert statt zusammengezählt

**Eine Summe ohne Klassen wäre wieder eine Zahl ohne Erhebung.** Jede Datei einzeln geöffnet:

| Klasse | Zahl | Module |
|---|---|---|
| **Ehrlich erklärt** — als `in_entwicklung` markiert | **5** | `heizkreisVerteiler` · `wandaufbau` · `holzMengen` · `holzBauteile` · `schifterListe` |
| **Ausdrücklich stillgelegt**, nicht gelöscht | **1** | `toolCatalogStillgelegt.ts` — *„nicht gelöscht, sondern stillgelegt"* (I2/AUF-21) |
| **Reine Daten ohne Verhalten**, so gebaut | **1** | `werkzeugLandkarte.ts` — *„kein Verhalten, kein Dispatcher"*; Verbraucher sind die Tests |
| **Doppelter Weg gebaut, erster liegen geblieben** | **1** | `deckenMesh.ts` — `szene.ts:451-478` rendert inline über `bodenPunkteThree` |
| **Vorbau** — reine, getestete Geometrie ohne Anschluss | **17** | s. u. |

**Sieben der 25 sind damit kein Befund**, sondern gewollt und im Code begründet. **Der eigentliche
Rest ist siebzehn**, und er trägt ein einziges Muster: *reine, testbare Logik, gebaut und nicht
angeschlossen* — genau das, was die Registerzeile W-27 an `dachTopologie` selbst benennt: **„kein
Fehlbau, sondern ein Vorbau."**

Die größten davon: `dachAusschnitt` (510 Z.) · `wandFlaeche` (238) · `dachTopologie` (183) ·
`auswechslung` (174) · `treppenTypen` (153) · `treppeSvg` (142) · `integrationAbgleich` (135) ·
`grundriss` (133).

## Der Fund, der die Baureihenfolge betrifft

**Der L-Kontur-Verbund liegt zur Hälfte fertig da:**

| Modul | Lage | Zeilen |
|---|---|---|
| `geometry/grundriss.ts` — *„zusammengesetzte Grundrisse (L-, T-, U-Form)"* | **unerreichbar** | 133 |
| `geometry/dachTopologie.ts` — Ecken-/Kantenerkennung | **unerreichbar** | 183 |
| `geometry/dachVerschneidung.ts` | erreichbar | 205 |
| `geometry/dachUForm.ts` | erreichbar | 126 |
| `geometry/dachGeometrie.ts` — trägt die Rechteck-Sperre `:88-92` | erreichbar | 153 |

**Die L-Kontur ist kein Neubau.** 316 Zeilen geprüfter Geometrie für zusammengesetzte Grundrisse
und Kantenerkennung liegen gebaut und unangeschlossen; die drei erreichbaren Nachbarn laufen. Was
fehlt, ist der Anschluss — und die Rechteck-Sperre in `dachGeometrie.ts:88-92`, die jede
nicht-rechteckige Kontur abweist.

**Das ändert die Einschätzung aus meiner Dauer-Rechnung:** ich hatte die L-Kontur als schwersten
Einstieg genannt. Gemessen ist sie ein **Anschluss** nach A-35-Muster, kein Bau — mit dem
Unterschied, dass sie zusätzlich eine Sperre öffnen muss, und **das** ist der Punkt, an dem Yamas
Punkt 8 hängt.

## Was auch diese Runde NICHT beantwortet

Erreichbar heißt: **im Bündel**. Es heißt nicht, dass ein Nutzer hinkommt — ein Modul kann
importiert und trotzdem hinter einem nie gesetzten Zustand liegen. Diese Stufe misst die
Importkette; die Bedienkette ist die nächste Verschärfung und wird hier **nicht** behauptet.

---

# S-1/3 — die Bedienkette: kein Fehlversprechen gefunden, und das ist das Ergebnis

*Nachgereicht 20.08. gegen `dd0a870b`. Die Stufe davor endete mit „erreichbar heißt im Bündel,
nicht dass ein Nutzer hinkommt". Hier ist die Antwort — und sie fällt anders aus als erwartet.*

## Die Kette, Glied für Glied gemessen

| Glied | Messung | |
|---|---|---|
| Was liegt in der Leiste? | `toolPresentation.ts`: **111 Einträge** — 8 `fix` + 2 `kontext` + 3 `weitere` aus der **Registry**, 98 `weitere` aus dem **Katalog** | |
| Welche Art hat ein Registry-Werkzeug? | **10 `werkzeug`** (setzen `activeToolId`) + **3 `aktion`** (Sofortbefehl) | |
| Werden die Sofortbefehle bedient? | `HausplanerApp.tsx:704-706` — `loeschen`, `duplizieren`, `trimmen`. **Drei Arten, drei Verzweigungen** | **vollständig** |
| Was wird aus den 98 Katalog-Verträgen? | `paketAdapter.ts:124` → `activationRules: regelnFuer(vertrag(w.id)?.vorbedingungen ?? [])` | |
| Tragen die Verträge Vorbedingungen? | **110 von 110. Null ohne.** | **keine Lücke** |
| Was geschieht mit unerfüllbaren? | `vorbedingungen.ts` (AUF-36): *„Fünf sind es nicht — und die werden nicht ausgelassen und schon gar nicht auf ‚erfüllt' verdrahtet, damit eine Kachel klickbar aussieht. Sie sind unerfüllt mit ehrlichem Grund."* | **gesperrt statt Attrappe** |

## Der Verdacht, den ich hatte, und warum er fällt

Nach dem Zonen-Abgleich sah es nach dem AUF-28-Fall durch die Hintertür aus: **97 Kennungen in der
Oberfläche ohne Registry-Eintrag.** Vier Messungen später ist er erledigt, und zwar an vier
verschiedenen Stellen:

1. `toolPresentation` führt `herkunft: 'registry' | 'katalog'` — sie **weiß**, was was ist.
2. Die Zone heißt `'weitere'` und ist im Kopf definiert als *„kuratiert verfügbar, **Handler folgt**"*.
3. `paketAdapter` erfindet keine Regeln, sondern zieht sie aus dem Funktionsvertrag; *„nicht
   messbare Vorbedingungen erzeugen **keine** Regel"* — Operanden-Gate im Code.
4. Und die Verträge sind lückenlos: **110 von 110 mit Vorbedingung.**

**Es gibt kein Glied, an dem eine Kachel klickbar wird, ohne dass etwas passiert.**

## Und damit ist die Ausgangsfrage des Auftrags beantwortet

> *„Wenn der Code da ist: warum fühlt sich der Werkzeugkasten dann nicht wie einer an?"*

**Nicht weil etwas kaputt ist, sondern weil 98 von 111 Kacheln ehrlich als Noch-Nicht ausgewiesen
sind.** Der Werkzeugkasten zeigt den vollen Umfang des Vorhabens und gibt dreizehn davon frei. Das
ist eine Produktentscheidung, keine Baulücke — und sie ist an vier Stellen im Code begründet
(AUF-21, AUF-28, AUF-36, I4).

**Die Lücke, die der Auftrag sucht, liegt nicht zwischen Registerzeile und Registry.** Sie liegt
zwischen **Vertrag und Handler**: 110 Verträge beschreiben, was die Werkzeuge tun sollen; 13 tun es.

## Was das für S-3 heißt

Der Anschlussvorrat aus S-1/2 (**17 Vorbau-Module**) und diese 98 Kacheln sind **dieselbe Lücke von
zwei Seiten**: die Module sind die Rechnung ohne Bedienung, die Kacheln die Bedienung ohne Rechnung.
**Ein Anschluss nach A-35-Muster schließt beide auf einmal** — `trimmen` war genau das: Vertrag war
da, Kachel war da, Geometrie war da (`geradenSchnitt` seit A-32), es fehlte das Stück dazwischen.

**Damit ist die Reihenfolge für S-3 messbar statt Geschmack:** zuerst die Kacheln, deren Rechnung
schon als Vorbau-Modul daliegt. Das sind die kürzesten Anschlüsse, und sie sind namentlich bekannt.

## Ein negatives Ergebnis ist ein Ergebnis

Diese Stufe hat **keinen** Befund gefunden. Sie wird trotzdem geliefert: die Frage *„liegt zwischen
Bündel und Klick ein Fehlversprechen?"* ist damit beantwortet und muss nicht noch einmal gestellt
werden. **Nicht gefunden ist etwas anderes als nicht gesucht** — der Suchweg steht oben, Glied für
Glied, und ist nachfahrbar.

---

# S-1/4 — die Schnittmenge: die Bauliste, nach Anschlusslänge geordnet

*Nachgereicht 20.08. gegen `70f46b31`. S-1/2 fand 17 Vorbau-Module, S-1/3 fand 98 wartende Kacheln.
**Hier werden sie gepaart.***

## Das Ordnungskriterium, aus den drei Stufen abgeleitet

Ein Anschluss ist **kurz**, wenn drei Stücke schon liegen und nur das Bindeglied fehlt — genau das
Muster von A-35 (`trimmen`: Vertrag da, Kachel da, `geradenSchnitt` seit A-32 da).

```
1  RECHNUNG   ein Vorbau-Modul liegt, geprueft und unerreichbar        (S-1/2)
2  KACHEL     eine Kachel liegt in der Leiste, Zone 'weitere'          (S-1/3)
3  BEFEHL     Marke 'deckt' (Befehl existiert) ODER 'ohne-modell'      (Landkarte)
              -> in beiden Faellen ist KEIN neuer Modellbefehl noetig
```

**Marke `fehlt` schliesst einen kurzen Anschluss aus** — dort ist zusaetzlich ein Modellbefehl zu
bauen. Von den 20 `fehlt`-Marken taucht in dieser Liste deshalb keine auf.

## Und eine Unterscheidung, ohne die die Liste falsch wäre

**Nicht jedes unerreichbare Modul bedeutet eine fehlende Fähigkeit.** Geprüft je Kandidat: *gibt es
das Können heute irgendwo, auch ohne die Kachel?*

| Kandidat | Fähigkeit heute erreichbar? | |
|---|---|---|
| `ausblenden` · `einblenden` · `sperren` · `entsperren` | **ja** — `EigenschaftenPanel.tsx:234/238` setzt `SET_NODES_SICHTBAR` / `SET_NODES_GESPERRT` ab | **zweite Oberfläche**, keine neue Fähigkeit |
| alle übrigen unten | **nein** — kein Verbraucher außerhalb des Moduls | echter Anschluss |

`auswahlDarstellung.ts` (71 Z.) ist damit der **zweite `deckenMesh`-Fall**: reines Modul gebaut, die
Sache danach inline in der Komponente gelöst. Zwei Fälle sind ein Muster und gehören dem Planner
gemeldet — **nicht** als Bauauftrag, sondern als Frage, welcher der beiden Wege bleiben soll.

## Die Bauliste

> **BERICHTIGT 20.08. durch S-1/5 — vier der fünf Paarungen halten nicht.** Die Tabelle bleibt
> stehen, weil sie zitiert ist; **gültig ist S-1/5 am Dateiende.** Es bleibt **eine** Paarung:
> `dachfenster`.

**Sortiert nach Anschlusslänge: was am wenigsten braucht, steht oben.**

| # | Kachel | Marke | Rechnung liegt in | Z. | fehlt |
|---|---|---|---|---|---|
| 1 | `suche` | `ohne-modell` | `trefferSuche.ts` — `besterTreffer`, `toleranzInWelt` | 75 | nur der Bedienweg; **kein Verbraucher außerhalb der Datei** nachgemessen |
| 2 | `volumen-messen` | `ohne-modell` | `wandFlaeche.ts` — `wandMengen` | 238 | nur der Bedienweg |
| 3 | `grundriss-erkennen` | `ohne-modell` | `grundriss.ts` — `grundrissPolygon`, `eckenAnalyse` | 133 | nur der Bedienweg |
| 4 | `schnitt` · `aufriss` | `ohne-modell` | `raumProjektion.ts` (98) · `dachProjektion.ts` (43) · `treppeSvg.ts` (142) | 283 | Bedienweg + Entscheidung, welche Projektion welche Kachel bedient |
| 5 | `dachfenster` | `deckt` | `dachOeffnung` (96) · `dachAusschnitt` (510) · `auswechslung` (174) · `sparrenTrennung` (67) · `aufbautenStatus` (52) | **899** | Bedienweg; Befehl existiert |
| — | `ausblenden` · `einblenden` · `sperren` · `entsperren` | `deckt` | `auswahlDarstellung.ts` | 71 | **kein Anschluss** — zweite Oberfläche, siehe oben |

**Fünf Anschlüsse, 1.628 Zeilen geprüfte Rechnung, die heute niemand erreicht.**

## Drei Module bleiben ohne Paarung — und das ist eine Aussage

| Modul | Z. | warum keine Kachel |
|---|---|---|
| `dachTopologie.ts` | 183 | gehört zur L-Kontur, nicht zu einer Kachel — hängt an der Sperre `dachGeometrie.ts:88-92` und damit an Yamas Punkt 8 |
| `integrationAbgleich.ts` | 135 | setzt *„Yamas Abschnitt 14 (Import- und Abgleichslogik)"* um; welche Kachel das tragen soll, ist **nicht** gemessen und wird nicht geraten |
| `werkzeugRegistry.ts` | 68 | Plattform-Kern, kein Werkzeug — hat richtigerweise keine Kachel |
| `dachVorlage.ts` · `treppenTypen.ts` | 34 · 153 | Datenvorlagen hinter `dach` bzw. `treppe`; beide Werkzeuge sind erreichbar, die Vorlagen werden nur nicht benutzt |

## Was diese Stufe NICHT tut

- **Sie schneidet keinen Auftrag.** Reihenfolge und Zuschnitt sind Planner-Sache; hier steht das
  Material, nach einem benannten Kriterium sortiert.
- **Sie behauptet keine Aufwände.** Der einzige gemessene Maßstab ist A-35: 12 Dateien, 894 Zeilen,
  291 davon Test. Ob Nummer 1 kleiner ist als A-35, ist nicht gemessen — nur, dass ihre Rechnung
  bereits liegt.
- **Sie prüft die Paarungen nicht fachlich.** Dass `wandFlaeche.wandMengen` das ist, was
  `volumen-messen` meint, ist aus Namen und Vertragsfamilie (`measurement`) geschlossen, **nicht am
  Vertragstext belegt**. Vor dem Zuschnitt gehört jede Paarung einmal gegen ihren Vertrag gehalten.

---

# S-1/5 — die Paarungen gegen ihre Verträge gehalten: vier von fünf fallen

*20.08. gegen `ee319d54`. S-1/4 endete mit dem Satz, die Paarungen seien „aus Namen und
Vertragsfamilie geschlossen, **nicht am Vertragstext belegt**". Das war eine Lücke in meiner eigenen
Lieferung. Hier ist sie geschlossen — und sie kippt vier Fünftel der Liste.*

## Verfahren

Je Kandidat **den Vertrag** (`werkzeugVertrag.ts`: `commandId`, `familie`, `eingaben`, `ergebnisse`)
gegen **die Signatur des Moduls** gehalten, dazu die Begründung der Landkarte im vollen Wortlaut.
Nicht Namen verglichen — Ein- und Ausgänge.

## Ergebnis

| # | Paarung | Vertrag verlangt | Modul liefert | |
|---|---|---|---|---|
| 1 | `suche` ↔ `trefferSuche` | `SearchCommand` · workflow · `subjectIds, workflowParameters` → `workflowResult` | `besterTreffer(kandidaten, toleranz)` — **Hit-Test**: *„Welches Objekt hat der Nutzer getroffen, wenn mehrere übereinanderliegen?"* | **fällt** |
| 2 | `volumen-messen` ↔ `wandFlaeche` | `MeasureVolumeCommand` · measurement · `points, measurementOptions` → `measurementResult` | `wandMengen(wand: WallNode, oeffnungen, bezug)` — Mengen **einer Wand** | **fällt** |
| 3 | `grundriss-erkennen` ↔ `grundriss` | `RecognizeCommand` · import · `referenceId, region, categories, recognitionProfile` → `detectionBatchId` | `grundrissPolygon(form, length, width, …)` — **konstruiert** ein L/T/U-Polygon aus Maßen | **fällt** |
| 4 | `schnitt` · `aufriss` ↔ Projektionen | `SectionCommand`/`ElevationCommand` · **create** · → `createdObjectIds` | `projiziereDach(scene) → DachFlaecheProjektion[]` — erzeugt **nichts** | **fällt** |
| 5 | `dachfenster` ↔ `dachOeffnung` | `RoofWindowCommand` · create · `roofFaceId, positionUV, width, height, productId` → `roofWindowId, roofOpeningId` | `oeffnungRechteck(o: OeffnungEingabe, f: DachflaecheInfo, sicherheitsrand)` → Rechteck auf der Dachfläche; Befehl `ADD_ROOF_AUFBAU` existiert | **hält** |

**Eine von fünf.**

## Warum ich danebenlag — vier verschiedene Gründe, nicht einer

1. **Eine Begründung als Umsetzungsbeleg gelesen.** Die Landkarte schreibt bei `suche`: *„Findet
   Knoten, ändert keine (`trefferSuche.ts`)."* Das begründet die Marke `ohne-modell` — es behauptet
   **nicht**, dass `trefferSuche` das Werkzeug `suche` implementiert. Der Modulkopf sagt selbst, was
   es ist: **der Hit-Test** für überlappende Objekte. Ich habe eine Klammer als Zuordnung gelesen.
2. **Namensähnlichkeit für Fachgleichheit genommen.** `wandFlaeche` ↔ `volumen-messen`: Fläche ist
   nicht Volumen, eine Wand ist keine Punktfolge. Die Landkarte nennt dort **gar kein Modul**
   (*„Temporäre Messung."*) — ich habe eins dazuerfunden.
3. **Konstruktion mit Erkennung verwechselt.** `grundriss.ts` **baut** ein Polygon aus Form und
   Maßen; `grundriss-erkennen` **liest** eines aus einer Unterlage. Die Landkarte sagt es sogar
   ausdrücklich: *„Die Erkennung selbst liefert Kandidaten (`kandidat_geometrie` am Upload)."* Die
   Auflösung stand im Text, den ich zitiert hatte.
4. **Die Familie nicht gegen die Marke gehalten.** Bei `schnitt`/`aufriss` verlangt der Vertrag
   `familie: 'create'` mit `createdObjectIds`, die Landkarte sagt `ohne-modell` — *„eine Darstellung,
   kein Knoten"*. **Das ist ein Widerspruch zwischen Vertrag und Landkarte, kein Anschlussfall**, und
   er gehört als eigener Befund gemeldet (unten).

**Dreimal von vier stand die Auflösung in einem Text, den ich schon zitiert hatte.** Das ist
dieselbe Klasse wie §106/§111 und wie Fehler 30 — und der Grund, warum diese Prüfung nicht optional
war.

## Nebenbefund: Vertrag und Landkarte widersprechen sich bei `schnitt` und `aufriss`

| Quelle | Aussage |
|---|---|
| `werkzeugVertrag.ts` | `familie: 'create'`, `ergebnisse: ['createdObjectIds']` — **es entstehen Objekte** |
| `werkzeugLandkarte.ts` | `marke: 'ohne-modell'` — *„Eine Schnittansicht ist eine Darstellung, kein Knoten."* |

**Beides kann nicht stimmen.** Entweder erzeugt ein Schnitt einen Knoten (dann ist die Marke falsch
und es fehlt ein Modellbefehl), oder er tut es nicht (dann sind `familie` und `ergebnisse` des
Vertrags falsch). Das betrifft **zwei** Verträge und ist keine Messfrage, sondern eine
Fachentscheidung. **Ball beim Planner.**

## Die berichtigte Bauliste

| # | Kachel | Marke | Rechnung liegt in | Z. | Beleg |
|---|---|---|---|---|---|
| 1 | `dachfenster` | `deckt` (`ADD_ROOF_AUFBAU`) | `dachOeffnung` (96) · `dachAusschnitt` (510) · `auswechslung` (174) · `sparrenTrennung` (67) · `aufbautenStatus` (52) | **899** | Ein- und Ausgänge gegen den Vertrag gehalten |

**Ein Anschluss statt fünf. 899 Zeilen statt 1.628.**

Die vier gefallenen Module sind damit **nicht** wertlos — sie sind nur nicht das, wofür ich sie
gehalten habe. Welche Kachel sie tragen (falls eine), ist **nicht gemessen** und wird hier **nicht
geraten**. Das ist der Unterschied zu S-1/4.

---

# S-1/6 — die vier gefallenen Module: wem gehören sie wirklich?

*20.08. gegen `8bd5ff48`. S-1/5 ließ ausdrücklich offen, welche Kachel die vier tragen. Offen
gelassene Fragen der eigenen Lieferung sind Arbeit, keine Vorlage — hier ist die Antwort.*

| Modul | gehört zu | Lage heute |
|---|---|---|
| `trefferSuche.ts` (75 Z.) | **`auswahl`** — Vertrag `pointerPosition, selectionMode → selectionIds`, und das ist genau der Hit-Test | **umgangen.** `auswahl` ist erreichbar und funktioniert; die 3D-Auflösung geschieht mit einem eigenen `THREE.Raycaster` in `szene.ts:690` |
| `wandFlaeche.ts` (238 Z.) | **keiner** | **Waise.** Alle fünf `measurement`-Verträge haben identische Ein-/Ausgänge (`points, measurementOptions → measurementResult`); `wandMengen(wand: WallNode, oeffnungen, bezug)` passt zu keinem. Auch keine Engine-Registrierung |
| `grundriss.ts` (133 Z.) | **`dach`** — dessen Vertrag nimmt `footprint`, und `grundrissPolygon` erzeugt genau einen (L/T/U) | **gesperrt.** `dach` ist erreichbar, akzeptiert aber nur Rechtecke (`dachGeometrie.ts:88-92`). Derselbe Riegel wie bei `dachTopologie` — **Punkt 8** |
| `raumProjektion.ts` (98) · `dachProjektion.ts` (43) | **keiner Kachel** — sie sind der Vertrag zur Heizlast (`raum_geometrien`) | **überholt.** PHP hat eine **eigene** Umsetzung, und die ist verdrahtet |

## Der Fund: die Projektion existiert zweimal, und die andere läuft

```
TypeScript   projection/raumProjektion.ts        unerreichbar, kein Verbraucher
PHP          Services/Geometrie/SzeneProjektionService.php
             -> Actions/UebernehmeSzeneInAuslegung.php
             -> Http/Controllers/Hausplaner/HausplanerController.php:228   ← verdrahtet
             + tests/Feature/Hausplaner/UebernehmeSzeneInAuslegungTest.php
```

Der PHP-Kopf sagt die Herkunft selbst: *„eingefrorener Vertrag (nur als Referenz gelesen, kein Code
kopiert)"*. **Das ist kein Versehen — es ist eine bewusste Zweitumsetzung, weil die Heizlast
serverseitig rechnet.** Was fehlt, ist der Schlussstrich: die TS-Seite steht weiter da, als wäre sie
der Weg.

## Und damit ist das Muster vollständig — es sind vier Fälle, nicht einer

**Ein reines, getestetes Modul wird als „die eine Regel" gebaut. Die Sache wird danach woanders
gelöst. Das Modul bleibt stehen.**

| Fall | Modul | wo die Sache stattdessen gelöst wurde |
|---|---|---|
| 1 | `deckenMesh.ts` | inline in `szene.ts:451-478` über `bodenPunkteThree` |
| 2 | `auswahlDarstellung.ts` | inline in `EigenschaftenPanel.tsx:234/238` |
| 3 | `trefferSuche.ts` | eigener `Raycaster` in `szene.ts:690` |
| 4 | `raumProjektion` · `dachProjektion` | eigenständig in PHP, verdrahtet bis zum Controller |

**Viermal dieselbe Bewegung, an vier verschiedenen Stellen, von vier verschiedenen Bauten.** Das ist
kein Liegenlassen aus Nachlässigkeit, sondern ein Umgehen der Modulschicht — und es steht gegen die
Grundregel 3 des Hausplaner-Skills: *„EINE Wahrheit je Sachverhalt, kein zweiter SSOT-Anker."*

**Wirkung, gemessen:** 544 Zeilen geprüfter Code (75 + 71 + 98 + 43 + 257 für `deckenMesh`-Umfeld
nicht mitgerechnet), die aussehen wie der Weg und keiner sind. Wer sie liest, um zu verstehen, wie
die Auswahl trifft oder wie eine Decke entsteht, liest die falsche Datei — **und der Test daneben
ist grün.**

## Ball

**Beim Planner, als eine Entscheidung für alle vier:** bleibt die Modulschicht der Weg (dann werden
die vier angeschlossen und die Inline-Lösungen weichen), oder ist sie aufgegeben (dann werden die
Module stillgelegt wie `toolCatalogStillgelegt.ts` — **nicht gelöscht**, mit Grund im Kopf).
**Einzeln zu entscheiden wäre der Fehler**, weil es viermal dieselbe Frage ist.

**Bei Yama** unverändert Punkt 8 — und `grundriss.ts` ist jetzt der dritte Posten, der daran hängt
(nach `dachTopologie.ts` und der Sperre selbst).

**Nicht geraten:** `wandFlaeche.ts` (238 Z.) gehört zu keiner Kachel und zu keiner Engine. Wofür es
gebaut wurde, steht in keiner Quelle, die ich gemessen habe. Das bleibt offen und wird **nicht**
mit einer plausiblen Zuordnung gefüllt.
