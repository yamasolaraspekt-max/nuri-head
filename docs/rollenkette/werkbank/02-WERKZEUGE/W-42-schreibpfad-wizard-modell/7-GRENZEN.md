# W-42 · Schreibpfad Wizard → Gebäudemodell — GRENZEN

> **Dieses Blatt ist Pflicht — und bei W-42 trägt es den Befund, wegen dessen der Auftrag anders
> geschnitten wurde, als Yama ihn freigegeben hat.**

## ZWEI überholte Quellen, wörtlich — und eine gemeinsame Ursache

**Erste Quelle: der eigene Dateikopf**, `app/ConfigWizard.tsx:6`:

> *„… ins Gebäudemodell (Command) **bleibt die nächste Scheibe**."*

**Zweite Quelle: der Bericht**, `docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md:184-185`:

> *„`ConfigWizard` 271 Z, Fenster/Tür/Treppe, 5 Schritte — **schreibt NICHTS ins BuildingDocument**,
> lädt JSON herunter. Schreibpfad ist als ‚nächste Scheibe' benannt."*

**Beide sind überholt.** *Gemessen am Bau-Stand: drei `executeCommand({ type: 'ADD_NODE' })` in
`:184`, `:205`, `:226`.*

### Die gemeinsame Ursache — und sie ist messbar

```text
BuildingDocument  in ConfigWizard.tsx   0 Treffer
SceneDocument     in ConfigWizard.tsx   0 Treffer
```

> **Der Schreibpfad nennt den Dokumenttyp GAR NICHT.** *Er nennt den **Store** und das
> **Kommando**.* **Wer nach dem Dokument sucht — unter welchem der beiden Namen auch immer —,
> findet nichts und schließt daraus, es werde nicht geschrieben.**
>
> **Das ist H-9 in seiner teuersten Form: ein Muster, das eine Schreibweise voraussetzt, misst die
> Schreibweise und nicht die Sache.** *Hier hat es **zwei** Quellen in die Irre geführt — und ohne
> diesen Abschnitt hätte die nächste Rolle einen **zweiten** Schreibpfad gebaut.*

**Deshalb ist dieses Blatt eine ABLESUNG und keine Vorgabe** — *abweichend von Yamas Freigabe, vom
Planner benannt und begründet, von mir in `40a9a74a` nachgemessen und bestätigt.* **Ob die Abweichung
gedeckt ist, entscheidet Yama; der Release-Prüfer hat die Frage als Punkt 10 vorgelegt.**

## Drei ungemessene Punkte — als FRAGE, nicht beantwortet

```text
1  Was geschieht, wenn executeCommand FEHLSCHLAEGT (ok === false)?
   Die Meldung unterscheidet zwei Faelle — ob etwas zurueckgerollt wird, ist
   UNGEPRUEFT. Ein halb gebauter Knoten waere die stille Loeschung von W-41,
   nur in der anderen Richtung.

2  Ergeben die ZWEI WEGE dasselbe Bauteil?
   Der Paket-Weg (ConfiguratorPackage) und der ADD_NODE-Weg laufen getrennt.
   Ob dasselbe herauskommt, ist nicht gemessen — DAS IST DIE FRAGE NACH EINER
   ZWEITEN WAHRHEIT, und sie wird hier GESTELLT und nicht beantwortet.

3  Greift Rueckgaengig wirklich?
   executeCommand verspricht es der Bauart nach. Gemessen ist es nicht.
```

> **Punkt 2 ist der schwerste.** *Zwei Wege, die dasselbe Bauteil erzeugen sollen, sind genau die
> Lage, aus der zwei Wahrheiten entstehen — dieselbe Klasse wie A-20s vier Zustandsorte und wie die
> Gültigkeitsachse, die W-40 doppelt vorgegeben hätte.* **Hier ist sie benannt, bevor sie
> auffällt.**

## Was der Schreibpfad NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| ein Bauteil **sinnvoll platzieren** | feste Startposition `x=2000, y=500` (`:181`) | das Bauteil an fester Stelle — *und die Meldung sagt „im Plan verschiebbar"* |
| ohne geladenes Gebäude einbauen | die Bedingung `&& scene` (`:174`) | den **JSON-Download** als Rückfall |
| aus dem Gebäude zurück in eine Konfiguration | es gibt keinen Rückweg | — |
| ein Bauteil an eine **gewählte** Wand setzen | *nur der Fenster/Tür-Zweig kennt eine Wand* | bei Heizkörper und Treppe: die feste Position |

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| Startposition | fest, unabhängig vom Grundriss | *sobald jemand erwartet, dass sie zur Geometrie passt* |
| Geschosswahl | `activeLevelId ?? levels[0] ?? null` (`:175`) | *wenn kein Geschoss aktiv ist und mehrere existieren* |

## Was dieses Blatt ausdrücklich NICHT entscheidet

| Frage | Gehört |
|---|---|
| Ob die Abweichung von Yamas Freigabe gedeckt ist | **Yama** — Punkt 10 des Release-Prüfers |
| Ob beide Wege dasselbe Bauteil ergeben | **eine Messung**, danach der Planner |
| Ob der überholte Satz in der Quelle berichtigt wird | **der Planner** — es ist seine Vorlage |
| Ob der Dateikopf `:6` berichtigt wird | **ein eigener Vorgang** — eine Ablesung ändert ihre Quelle nicht |

## Was später kommen könnte

```text
- die Messung, ob beide Wege dasselbe ergeben   -> die Frage aus Punkt 2
- eine Platzierungsregel statt fester Startwerte
- der Rueckweg Gebaeude -> Konfiguration
```
