# Master-Prompt Usability — Einordnung in unsere Ordnung

**26.07.2026, Planner.** Yama hat einen Master-Prompt mit 40 Abschnitten uebergeben:
Usability Engineering, intelligente Bedienung, Funktionsintelligenz, automatisiertes Testregelwerk.
Auftrag an mich: *"ergaenze dein Konzept mit diesen Informationen und ordne sie richtig ein."*

**Einordnen heisst nicht abnicken.** Ein Zielbild, das man ungeprueft in Auftraege uebersetzt, wird
zu 40 gleichzeitig offenen Baustellen — und wir haben heute gemessen, was passiert, wenn Gebautes
nicht angeschlossen wird. Ich habe deshalb jeden Abschnitt gegen den **gemessenen** Stand gehalten.

---

## Teil 1 — Was das Papier ist

**Es ist das Zielbild, und es ist ein gutes.** Es beschreibt denselben Zustand, den ich heute aus
der Gegenrichtung gemessen habe: eine Bedienung, die versteht, was der Nutzer vorhat. An drei
Stellen fordert es woertlich das, was meine Messungen als kaputt ausgewiesen haben — **ohne dass
der Verfasser die Messungen kannte**:

| Master-Prompt sagt | meine Messung von heute |
|---|---|
| §13: *"angrenzende Waende bleiben verbunden"* | `HausplanerApp.tsx:612` kennt keine Nachbarschaft; Fall B1: ein Raum verschwindet, 20,00 m² bleiben plausibel stehen |
| §12: *"Direktmasse muessen anklickbar sein"* | **0 Doppelklick-Griffe** in der gesamten Insel |
| §6: *"Griffe muessen ausreichend gross sein"* | es gibt keine Griffe; `auswahlDarstellung` entscheidet ueber sie und wird nur vom eigenen Test gerufen |

**Zwei unabhaengige Wege zum selben Befund sind ein starkes Zeichen.** Das Papier ist damit nicht
Wunschzettel, sondern Bestaetigung.

**Was es nicht ist: ein Auftrag.** 40 Abschnitte, 10 Umsetzungsphasen, 10 zu erstellende Dokumente,
ein neues Datenmodell und eine Telemetrie. Als Posten waere das ein Mehrfaches unseres gesamten
offenen Vorrats.

---

## Teil 2 — Die Einordnung in vier Toepfe

### Topf A — steht schon, muss nur angeschlossen werden

Der groesste Topf. **Das ist die gute Nachricht des ganzen Papiers.**

| Master | bei uns vorhanden | Zustand |
|---|---|---|
| §17 `ToolUsabilityDefinition` | `werkzeugVertrag.ts` — **110 Eintraege** mit Eingaben, Ergebnissen, Vorbedingungen, Seiteneffekten, Umkehrbarkeit | vorhanden |
| §9 kontextabhaengige Werkzeugleisten | `resolveToolState` + Auswahlkontext, 27 Werkzeuge an `selection.count` | **laeuft** |
| §29 intelligente Fehlervermeidung | 12 Vorbedingungen als Daten, jede mit deutschem Grund | vorhanden |
| §29 *"Loesung anbieten"* | `vorbedingungen.handlung` + `WegweiserOrt` (AUF-45/57) | vorhanden |
| §33 Smart Tool Chaining | `app/tools/naechsterSchritt.ts` | **angeschlossen** |
| §9 Vorschlaege | Zustand `empfohlen` in `werkzeugZustand.ts`, vom Wizard gespeist | vorhanden |
| §12 Direktmass-Anzeige | `masskette.ts`, `bemassung.ts` | **angeschlossen** |
| §6 Fangpunkte sichtbar | `fangKern.ts` — Endpunkt, Mittelpunkt, Raster, Ortho | **nur vom Test gerufen** |
| §6 Griffe | `auswahlDarstellung.griffe` | **nur vom Test gerufen** |
| §26 automatisierte UI-Tests | `test:hausplaner:dom` als Gate | laeuft |
| §35 Quality Gate | fuenf Gates + Evaluator mit Gegen-Beweis | laeuft |
| §18 Testregelwerk je Funktion | Abnahmekriterien je Auftrag | laeuft |

**Folgerung, und sie ist die wichtigste des ganzen Papiers:** Der Master-Prompt verlangt an vielen
Stellen ein **neues Register** (§17, §36). Wir haben es schon — es heisst `werkzeugVertrag.ts`.
**Ein zweites Register neben dem ersten waere die klassische zweite Wahrheit.** Was fehlt, sind
Felder *in* dem vorhandenen Register, nicht ein Register daneben. Diesen Satz halte ich fuer den
wichtigsten Schutz beim ganzen Vorhaben.

### Topf B — gemessen nicht vorhanden, Neubau noetig

| Master | gemessener Stand |
|---|---|
| §7 Touch- und Fingerbedienung, komplett | **0 Treffer** fuer `onTouch`/`onPointer`/`touchstart` in `app/` |
| §32 Kontextmenue (Rechtsklick / Long-Press) | **existiert nicht** — der einzige Fund steht in `toolCatalogStillgelegt.ts`, also im stillgelegten Katalog |
| §12 Direktmass **bearbeitbar** | Anzeige ja, Eingabe nein |
| §6 Doppelklick | 0 |
| §8 Tastaturbedienung vollstaendig | **16 von 101** Werkzeugen haben ueberhaupt einen Shortcut |
| §22 Bedienungsanalytik | nichts, und das ist kein Versaeumnis (siehe Topf D) |
| §31 Lern-/Expertenmodus | nichts |

**Touch ist die groesste einzelne Luecke.** Nicht "schwach ausgepraegt" — **null**. Der
Master-Prompt widmet ihm einen ganzen Abschnitt mit Hitboxen, Drag-Schwellen, Long-Press-Dauer und
Gestenprioritaet. Auf einer Baustelle mit Tablet ist das der Unterschied zwischen benutzbar und
nicht benutzbar. **Das ist eine eigene Groessenordnung und gehoert nicht in AUF-50 hineingemischt.**

### Topf C — echte Widersprueche, die vor der Arbeit geklaert gehoeren

**C1 — Shortcut-Kollisionen, und zwar schon in unseren eigenen Daten.**

```
G  ->  verschieben  UND  raster
S  ->  skalieren    UND  fang
```

Zwei Doppelbelegungen, bevor irgendein Master-Prompt dazukommt. §8 verlangt *"Konflikte erkennen"* —
diese Pruefung haette **heute schon zwei Treffer**. Der Master will zusaetzlich `S = Fang`, was die
Kollision verschaerft; er sagt selbst *"abhaengig vom bestehenden Shortcut-System"*, also ist das
kein Fehler des Papiers, sondern eine offene Zuweisung. `W`, `D`, `F`, `V` sind frei — die
Master-Belegung passt, `A` (Direktauswahl) und `V` (Auswahl) muessten nebeneinander bestehen.

**C2 — Doppelklick: das Papier warnt, Yama fordert.**
§6 sagt *"Doppelklick nur verwenden, wenn ein klarer Mehrwert besteht"*. Yama hat den Doppelklick
auf Tuer und Fenster ausdruecklich verlangt. **Beides ist vereinbar, aber nur mit einer Regel:**
der Doppelklick ist **nie der einzige Weg** — er ist die Abkuerzung zu dem, was auch im Panel geht.
Dann ist der Mehrwert klar und die Warnung erfuellt. *Eine Funktion, die es nur per Doppelklick
gibt, ist auf Touch unerreichbar* — genau davor warnt §6.

**C3 — §35 Quality Gate: ab wann?**
*"Keine Funktion darf als fertig gelten, bevor …"* — dreizehn Posten sind heute abgenommen. Gilt
das rueckwirkend, sind sie alle wieder offen, und die Tafel verliert ihre Bedeutung. **Mein
Vorschlag: das Gate gilt fuer alles, was ab dem Beschluss beginnt.** Fuer Bestehendes wird es zur
Befundliste, nicht zur Ruecknahme. *Eine Regel, die die Vergangenheit umschreibt, wird beim ersten
Anwenden umgangen.*

**C4 — §19 Bewertung 0 bis 5: die eine Stelle, an der ich widerspreche.**
Eine Skala von 0 bis 5 sieht aus wie eine Messung und ist ein Urteil. Genau diese Verwechslung hat
uns heute schon zweimal getroffen — erfundene Uhrzeiten in einem Eingestaendnis, und eine Zaehlung
von 49 gegen tatsaechlich 65. **Eine Zahl, die niemand nachrechnen kann, macht einen Bericht nicht
genauer, sondern nur ueberzeugender.**

*Ich verwerfe die Skala nicht — ich haenge eine Bedingung daran:* **jede Ziffer traegt ihren Beleg
daneben.** "Praezision: 4" ist wertlos; *"Praezision: 4 — Soll 4.250, gemessen 4.250, aber erst
nach 6 Handgriffen statt 3"* ist brauchbar. Ohne Beleg wird die Ziffer nicht geschrieben.

### Topf D — Entscheidungen, die Yama gehoeren

1. **§22 Bedienungsanalytik.** Telemetrie in einem System mit ~3000 echten Kunden ist eine
   Datenschutzfrage, keine Bauaufgabe — Einwilligung, Zweckbindung, Aufbewahrung. Das Papier sagt
   selbst "optional und datenschutzkonform". **Ich schreibe dazu keinen Auftrag.**
2. **§36 Datenmodell** (`UsabilityTestCase`, `UsabilityFinding`, `InteractionMetric`). Neue
   persistierte Tabellen ⇒ Schema ⇒ Tor 2 ⇒ Yama. **Zwischenweg, den ich empfehle:** die ersten
   Berichte als Dateien unter `docs/`. Ein Datenmodell fuer Befunde, die es noch nicht gibt, ist
   Vorratsbau — genau das Muster, das uns die 28 unangeschlossenen Module eingebracht hat.
3. **§38 Quality-Agent-Integration** — setzt einen laufenden Quality Agent voraus. Ob es ihn gibt
   und wo er laeuft, weiss ich nicht; **ich behaupte es nicht.**
4. **Touch (§7) als eigenes Vorhaben** neben AUF-50 oder danach?

---

## Teil 3 — Wo das Papier in unsere Ordnung faellt

**Der entscheidende Schnitt verlaeuft zwischen §40 und den USABILITY-Phasen.**

### §40 ist Planner-Arbeit und kann sofort laufen

Der Abschnitt sagt ausdruecklich: *"Arbeite zunaechst read-only. Keine Implementierung. Keine
Aenderungen an bestehenden Werkzeugen. Nach Fertigstellung stoppen."*

**Das verletzt weder §13 noch §14.** §13 (Einspurbetrieb) regelt den **Bauenden** — eine Analyse
baut nicht. §14 verbietet neue **Posten** — eine Planner-Untersuchung ist kein Posten auf der
Tafel. Sie schreibt nur nach `docs/`, was ohnehin meine einzige Schreibheimat ist, und sie nimmt
dem Generator nichts weg.

**Ich fange damit in den Luecken der Wache an**, so wie heute mit der Raumerkennung. Zusammenlegen
statt vervielfachen: von den zehn geforderten Dokumenten sind vier heute **im Kern schon
geschrieben**:

| §40 fordert | liegt bereits |
|---|---|
| `usability-current-state.md` | `bedienmodell-110-werkzeuge-2026-07-26.md` (+ SWOT) |
| `tool-usability-matrix.md` | `bestandsaufnahme-auf50-werkzeuge-2026-07-26.md` (101/110/7/19) |
| `usability-test-plan.md` | `vierte-rolle-erprober-benchmark-2026-07-26.md` (Pruefstrecke) |
| `optimization-backlog.md` | Befundliste + `bedienprobe-befunde-2026-07-26.md` |

**Sechs fehlen wirklich:** `interaction-pattern-inventory` · `object-editing-patterns` ·
`mouse-touch-keyboard-rules` · `smart-interaction-rules` · `accessibility-gaps` ·
`automated-test-matrix`. Die schreibe ich — unter unseren Dateinamen, nicht unter zehn neuen, sonst
haben wir zwei Dokumentenwelten.

### USABILITY-01 bis -10 sind Posten und fallen unter §14

Sie kommen auf die Befundliste und warten auf AUF-38, 52, 48, 50. **Und sie sind nicht neu:**
gegen meinen Zuschnitt gehalten decken sie sich weitgehend.

| Master-Phase | bei uns |
|---|---|
| USABILITY-01 Registry | **AUF-50.1** (Empfaenger) — Felder *in* `werkzeugVertrag`, kein zweites Register |
| USABILITY-02 Maus/Touch/Tastatur | Maus+Tastatur ≈ 50.2 · **Touch ist neu und eigenstaendig** |
| USABILITY-03 Direktmasse und Griffe | **AUF-50.2 + 50.3** |
| USABILITY-04 Vorschlaege, Kontextwerkzeuge | **AUF-50.6**, Bausteine liegen |
| USABILITY-05 Interaktionstests | Erprober + Pruefstrecke |
| USABILITY-06 Bewertung und Reports | Benchmark, **mit der Belegpflicht aus C4** |
| USABILITY-07 Metriken | Topf D — Yamas Entscheidung |
| USABILITY-08 Accessibility und mobil | teils vorhanden (15 `aria-label`, 4 `aria-disabled`), Touch = null |
| USABILITY-09 Quality Agent | Topf D |
| USABILITY-10 Pilotwerkzeuge | **deckungsgleich mit unseren 7 angeschlossenen** — Auswahl, Wand, Fenster, Tuer, Boden, Decke |

**USABILITY-10 ist die beste Nachricht darin.** Der Master-Prompt schlaegt als Piloten genau die
Werkzeuge vor, die als einzige einen Empfaenger haben. **Wir muessen fuer den Piloten nichts
freischalten** — er laeuft auf dem, was steht.

### Die Bedienlogik aus Yamas Nachtrag ist die Fassung, die ich uebernehme

> Objekt anklicken → hervorheben → Direktmasse → Griffe → Panel → Kontextleiste → Ziehen oder Mass
> aendern → Live-Vorschau → fachliche Pruefung → Bestaetigen → Undo/Redo

**Diese Kette ist besser als meine "drei Tiefen"**, weil sie die fachliche Pruefung *vor* dem
Bestaetigen ausdruecklich benennt — und genau dort sitzt der Wandlaengen-Fehler. Sie ersetzt Satz 2
meines Bedienmodells. Gemessener Stand der Kette heute: **Schritt 1 und 2 gehen, Schritt 3 bis 7
fehlen, Schritt 8 fehlt, Schritt 9 fehlt teilweise, Schritt 10/11 gehen.**

### Testaufgaben zusammenlegen, nicht verdoppeln

Der Master nennt 20 Aufgaben (§24), ich habe 6 (P1–P6). **Sie konkurrieren nicht:**
meine sechs sind die **feste Pruefstrecke** mit nachrechenbaren Sollwerten — sie ergeben die Kurve.
Seine zwanzig sind der **Aufgabenvorrat des Erprobers** — daraus zieht er je Durchgang, und dort
darf die Willkuer hinein. *Eine Strecke, die sich aendert, misst keinen Fortschritt; ein Vorrat,
der sich nie aendert, findet nichts Neues.* Beides zusammen ist richtig.

Vier seiner zwanzig gehoeren allerdings in die Pruefstrecke, weil sie exakte Sollwerte tragen:
Nr. 1 (Wand auf exakt 4,25 m) · Nr. 3 (Fensterbreite 1,20 → 1,50) · Nr. 11 (mehrere Fenster
gleichmaessig verteilen) · Nr. 12 (Wand per Tastatur). **Nr. 1 ist heute unser P6 in anderer
Kleidung** — dieselbe Stelle, an der es weh tut.

---

## Teil 4 — Der Qualitaetsgrundsatz

> *"Eine Funktion ist nicht fertig, wenn sie nur technisch funktioniert."*

**Den uebernehme ich ohne Vorbehalt.** Er sagt in einem Satz, was ich heute in vier Papieren
umkreist habe: dreizehn Posten korrekt abgenommen, jedes Votum richtig — und trotzdem 83 Werkzeuge,
die aufgehen und nichts tun. Technisch war alles fertig.

**Er bekommt eine Ergaenzung aus der Messung von heute**, und zwar eine, die im Master-Prompt fehlt:

> **Und sie ist nicht fertig, wenn sie richtig rechnet und niemand sie ruft.**

28 von 128 Modulen der Insel haben als einzigen Aufrufer ihre eigene Testdatei. Kein einziger
Abschnitt des Papiers faengt diesen Fall — er prueft Funktionen, die es in der Oberflaeche gibt.
**Ein Modul ohne Aufrufer wird von keiner Usability-Regel je erwischt**, weil es keine Bedienung
hat, die man pruefen koennte. Diese Zeile gehoert deshalb dazu.

---

## Was ich als Naechstes tue

1. **In den Luecken der Wache**: die sechs fehlenden §40-Dokumente, read-only, nur `docs/`.
   Beginnend mit `mouse-touch-keyboard-rules` — dort liegen die zwei Shortcut-Kollisionen und die
   Touch-Null, also der groesste Erkenntnisgewinn je Aufwand.
2. **Kein Posten, kein Rahmen-Paragraph**, bevor Yama die vier Punkte aus Topf D und C3 entschieden
   hat.
3. **AUF-50 bleibt zugeschnitten wie am 22:35** — der Master-Prompt bestaetigt den Zuschnitt, er
   aendert ihn nicht. Ergaenzt wird nur die fachliche Pruefung vor dem Bestaetigen.
