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
