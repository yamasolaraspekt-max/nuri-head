# W-39 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 1 Domäne | — | **nur gelesen**, über den Store |
| 2 Geometrie | — | **keine** |
| 3 Anwendung | `resources/planner/hausplaner/app/HausplanerStudio.tsx` **(159 Z.)** | **der ganze Bestand von W-39** |
| 4/5 Oberfläche | *dieselbe Datei* | Kopfzeile, Modusschalter, Bühne, Toast |

**Eine Datei, ein Export** (`:22`). *Der kleinste der fünf Stufe-6-Bausteine — 159 Zeilen gegen 739
bei W-37.*

## Schnittstelle

```ts
// HausplanerStudio.tsx:22
export function HausplanerStudio(): React.ReactElement;
//   keine Props. Alles kommt aus zwei Stores und fuenf eigenen Zustaenden.
```

## Kernstelle

```tsx
// :133-142 — die additive Einbettung
{imExperte && (
  <div className="hp-experte">
    <div className="hp-experte-buehne"><HausplanerApp imStudio /></div>
  </div>
)}
```

**Das ist die Kernstelle, weil `imStudio` der EINZIGE Eingriff in die bestehende App ist.** *Kein
Umbau, keine Kopie, kein Fork — ein optionales Flag, das die Markenzeile ausblendet, weil der Rahmen
schon eine Kopfzeile trägt.*

## Die vierzehn Importzeilen — und warum das Blatt dreizehn sagt

```text
:7  import React from 'react';                                        <- EXTERN
:8  { istAusloeser }        ./dashboard/dialogFokus
:9  { HausplanerApp }       ./HausplanerApp
:10 { StartView }           ./StartView
:11 { GuidedView }          ./GuidedView
:12 { T, FACH, PROJ, type StudioModus }   ./studioDaten
:13 { ConfigWizard, type KonfigArt }      ./ConfigWizard
:14 { FachFlaeche as FachFlaecheAnsicht } ./FachFlaeche
:15 { ableitenSchritte }    ./dashboard/fahrschritte
:16 { usePlannerUiStore }   ./state/uiState
:17 { speicherAnzeige, type AnzeigeArt }  ./dashboard/speicherAnzeige
:18 { fachFlaecheNach, KONFIGURATOR_NAMEN, … }  ./dashboard/fachFlaechen
:19 { Ikon }                ./studioUi
:20 { useHausplanerStore, type SpeicherStatus }  ../store/hausplanerStore
```

> **`grep -c '^import'` liefert 14, das Auftragsblatt nennt 13.** *Beide sind richtig und meinen
> verschiedene Mengen:* **Zeile 7 ist React und damit extern; die 13 des Blattes sind die
> Insel-Module.** *Aufgelöst durch Öffnen der Zeilen, nicht durch Rechnen.*

## Die dreizehn Module mit ihrer Werkzeugzuordnung

*Der Stand ist der **Reifegrad im REGISTER**, nicht der Auftragszustand in `docs/STATUS.md` — die
Aufträge W-34 und W-38 sind `BETRIEBSBESTAETIGT`, die Werkzeuge stehen auf `BESCHRIEBEN`. **Zwei
verschiedene Dinge, und ich hatte sie beim ersten Schreiben verwechselt.***

| Modul | Werkzeug | Reifegrad im REGISTER |
|---|---|---|
| `StartView` | **W-33** | LEER |
| `GuidedView` · `dashboard/fahrschritte` | **W-34** | **BESCHRIEBEN** |
| `ConfigWizard` | **W-35** | LEER |
| `studioDaten` | **W-38** | **BESCHRIEBEN** |
| `HausplanerApp` | *kein Werkzeug* | — |
| `FachFlaeche` · `dashboard/fachFlaechen` | *kein Werkzeug* | — |
| `studioUi` (`Ikon`) | *kein Werkzeug* | — |
| `state/uiState` (`usePlannerUiStore`) | *kein Werkzeug* | — |
| `store/hausplanerStore` | *kein Werkzeug* | — |
| `dashboard/speicherAnzeige` | *kein Werkzeug* | — |
| `dashboard/dialogFokus` | *kein Werkzeug* | — |

**Vier der dreizehn sind erfasst, zwei davon fertig beschrieben.** *Die übrigen stehen in
`7-GRENZEN.md` als Anschlussliste — sie ist gemessen und nicht vermutet.*

> **Benutzen ist nicht besitzen.** *W-39 importiert alle dreizehn und beschreibt keines. Wer eines
> von ihnen abliest, findet in diesem Blatt bereits, wo seine Zuständigkeit endet.*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-38** `studioDaten` | `StudioModus`, `T`, `FACH`, `PROJ` | **ja** — W-38 importiert nichts, kein Kreis |
| **W-34** `GuidedView`, `fahrschritte` | die geführte Ansicht und `ableitenSchritte` | **ja** — W-34 importiert W-38, nicht W-39 |
| **W-33** `StartView` | die Startansicht | **ja** |
| **W-35** `ConfigWizard` | Art und Dialog | **ja** |
| zwei Stores | Szene, Speicherstand, Projektliste | **ja** — Stores importieren keine Ansicht |

> **W-39 ist die Wurzel dieses Teilbaums — mit einer Einschränkung, die ich gemessen habe:** *von
> den dreizehn importiert es **keines** zurück, aber es ist nicht ganz oben.* **Genau ein Modul
> importiert `HausplanerStudio`, und das ist der Einstiegspunkt:**
>
> ```text
> resources/planner/hausplaner/main.tsx:14
> import { HausplanerStudio } from './app/HausplanerStudio';
> ```
>
> *`main.tsx` hängt das Studio in die Seite und befüllt den UI-Store aus dem Blade (`:37-38`).*
> **Deshalb war es die richtige Reihenfolge, den Rahmen zuerst abzulesen — die Grenzen der Nachbarn
> sind gemessen, bevor sie an die Reihe kommen.**
