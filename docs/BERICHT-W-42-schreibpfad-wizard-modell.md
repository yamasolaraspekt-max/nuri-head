# Baubericht W-42 — Schreibpfad Wizard → Gebäudemodell, abgelesen

```yaml
auftrag: "W-42"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-42-schreibpfad-wizard-modell.md
art: "STUFE 6 · ABLESUNG — abweichend von Yamas Freigabe (ENTWORFEN), weil der Code existiert"
befund_vor_dem_ziehen: "40a9a74a"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Ein Fehlgriff steckt in diesem Bau, und ich habe ihn vor der Fertigmeldung selbst gefunden** —
> *zum dritten Mal heute dieselbe Klasse: eine Testdatei nach ihrer Überschrift eingeordnet, statt
> zu messen, welche den Gegenstand berührt.* **Abschnitt „Der Fehlgriff" unten.**

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-42-schreibpfad-wizard-modell/
  1-ZWECK.md  2-FUNKTION.md  3-FORMELN.md  4-BEDIENUNG.md
  5-CODE/LIESMICH.md  6-PRUEFUNG.md  7-GRENZEN.md
REGISTER.md   Zeile 129:  LEER -> BESCHRIEBEN
```

**Kein Produktivcode.** *`ConfigWizard.tsx` ist gelesen, nicht geändert.*

## W-42-1 · Der Schreibpfad ist gebaut — drei Stellen, vier Bauteilarten

```ts
ConfigWizard.tsx   271 Zeilen
:184   store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode })   Heizkoerper
:205   store.executeCommand({ type: 'ADD_NODE', node: treppe   as SceneNode })   Treppe
:226   store.executeCommand({ type: 'ADD_NODE', node: knoten   as SceneNode })   Fenster UND Tuer
```

**Drei Aufrufe, vier Arten** — *`:226` trägt zwei.* **Wer die Aufrufe zählt und die Arten meint,
zählt eine zu wenig.**

## W-42-2 · Beide überholten Quellen, und die gemeinsame Ursache

```text
ConfigWizard.tsx:6            „… ins Gebaeudemodell (Command) bleibt die naechste Scheibe."
BERICHT-…-DREI-FRAGEN.md:184  „schreibt NICHTS ins BuildingDocument, laedt JSON herunter."
```

**Die Ursache ist messbar:**

```text
BuildingDocument  in ConfigWizard.tsx   0
SceneDocument     in ConfigWizard.tsx   0
```

> **Der Schreibpfad nennt den Dokumenttyp gar nicht** — *er nennt den **Store** und das
> **Kommando**.* **Wer nach dem Dokument sucht, unter welchem Namen auch immer, findet nichts.**
> *H-9 in seiner teuersten Form: zwei Quellen sind daran vorbeigelaufen, und ohne diesen Abschnitt
> hätte die nächste Rolle einen **zweiten** Schreibpfad gebaut.*

## W-42-3 · Die zwei Wege, je mit Bedingung

```text
Weg A  im Gebaeude    Bedingung  && scene   (:174, :172)   -> ADD_NODE
Weg B  standalone     kein Gebaeude          (:244-247)     -> Blob + a.download
```

**Der Download ist der RÜCKFALL, nicht der Regelfall.** *Genau diese Verwechslung steckt in beiden
überholten Quellen.*

## W-42-4 · Drei ungemessene Punkte, als Frage

```text
1  was bei ok === false geschieht — Rueckrollen ist UNGEPRUEFT
2  ob beide Wege DASSELBE Bauteil ergeben — die Frage nach der zweiten Wahrheit
3  ob Rueckgaengig wirklich greift
```

**Punkt 2 ist der schwerste** — *zwei Wege, die dasselbe erzeugen sollen, sind die Lage, aus der
zwei Wahrheiten entstehen.* **Gestellt, nicht beantwortet.**

## W-42-5 · Die Grenze zu W-35 verläuft INNERHALB einer Datei

**Beide Werkzeuge leben in `ConfigWizard.tsx`.** *W-35 ist alles bis zur Auswahl; W-42 ist, was
danach damit geschieht.* **Das ist ungewöhnlich und steht deshalb in `2-FUNKTION.md` — wer die Datei
öffnet, sieht beide nebeneinander.**

## W-42-6 · Sieben Blätter, Gegenprobe grün

```text
1-ZWECK 4e69a066 · 2-FUNKTION efdb020a · 3-FORMELN da85df8c · 4-BEDIENUNG 58ff3f48
5-CODE/LIESMICH f1f05fef · 6-PRUEFUNG f96a86e7 · 7-GRENZEN c1949535
keines gleicht der Vorlage oder einem der 29 fremden Werkzeugordner
```

## Der Fehlgriff — und er ist der dritte seiner Art an einem Tag

**In `6-PRUEFUNG.md` stand zuerst: „Kein Test in dieser Liste prüft den `ADD_NODE`-Weg."** *Ich
hatte drei Paket-Tests aufgezählt und daraus auf eine Lücke geschlossen.*

```text
grep -rl 'ADD_NODE' __tests__/
  levelCommands · dachModell · applyCommand · configWizardWrite · werkzeugLandkarte
```

**`configWizardWrite.test.ts` — 85 Zeilen, drei Tests, einer je Bauteilart:**

```text
· Fenster mit Bauart landet als OpeningNode auf der …
· Treppe landet als ObjectNode(stair) mit typ im Mo…
· Heizkoerper landet als ObjectNode(radiator) mit ob…
```

**Und `konfuguratorEhrlich.test.ts` trägt K6:** *„der Übernahme-Weg ins Modell ist unberührt — **er
war schon wahr**."* **Jemand hat die Ehrlichkeit dieses Pfades geprüft, bevor die Werkbank ihn
beschrieben hat.**

> **Dieselbe Klasse wie bei W-39 (`stilschicht` als „Farben" abgetan) und bei W-39s `imStudio`
> (Lücke behauptet, die es nicht gab).** *Der Unterschied: dort fand es einmal der Evaluator, hier
> und beim ersten Mal fand ich es selbst.* **Die Regel, die daraus folgt und die ich in
> `6-PRUEFUNG` auch angewandt habe: ein „kein Test" ohne Messung ist keine Aussage — die zwei
> Mutationen, die ich nicht gemessen habe, stehen jetzt ausdrücklich als „unklar".**

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien geändert** — nur gelesen |
| die Quelle `BERICHT-PROZESSEBENE-DREI-FRAGEN.md` | **0** — *sie ist die Vorlage des Planners an Yama* |
| `ConfigWizard.tsx:6` (der überholte Dateikopf) | **0** — *eine Ablesung ändert ihre Quelle nicht* |
| Rückweg | reine Neuanlage plus **eine** Registerzeile; `git revert` genügt |
