# W-37 · Rechenpanels — CODE

**ZWEI Module, 739 Zeilen.** *Am Bau-Stand gezählt.*

| Modul | Z | Rolle |
|---|---|---|
| `resources/planner/hausplaner/app/dashboard/enginePanels.ts` | **540** | Daten und Adapter |
| `resources/planner/hausplaner/app/EngineFlaeche.tsx` | **199** | Anzeige |

## Was `enginePanels.ts` ausführt

```text
TYPEN
  :35   EngineFeld            :51   EngineErgebnisFeld
  :57   EnginePanel           :89   EngineErgebnis

DATEN
  :119  ENGINE_PANELS         readonly EnginePanel[] — ACHT Eintraege

ADAPTER — die Klasse ist die SIGNATUR (werte: Record<string,string>) -> Eingabetyp
  :100  alsTreppenEingabe     :414  alsSparrenEingabe     :439  alsFbhEingabe
  :457  alsBetriebsBedingung  :482  alsUwEingabe          :494  alsAbwasserEingabe
  :503  alsArbeitsdreieck     :509  alsPvEingabe

BEDIENFLAECHE
  :522  enginePanel(engineId)                :527  startwerte(panel)
  :538  fehlendePflichtfelder(panel, werte)
```

> **`alsBetriebsBedingung` (:457) und `alsArbeitsdreieck` (:503) enden nicht auf „Eingabe".** *Ein
> Namensmuster findet sechs von acht. Gemessen hat es dabei **keinen** Über-Treffer — es lässt nur
> weg.* **Die Klasse ist die Signatur.**

## `EngineFlaeche.tsx` — ein Export

**`SCHWERE_ANZEIGE` (`:31`) ist modulintern und wird nicht ausgeführt** — *drei Grade mit Zeichen,
Wort und Farbmarke; siehe `4-BEDIENUNG`.*

## Die Anbindung an die Engines

**Acht `berechne`-Zeilen, sieben verschiedene Engines** (`:164 :227 :266 :301 :329 :356 :377 :403`).
*Sieben davon sind einzeilig und haben dieselbe Gestalt. Die achte (`:301`, `engine-heizkoerper`)
reicht zwei Zahlen neben der `BetriebsBedingung` durch und benennt `ausreichend` in `bestanden` um —
mit einem Kommentar, der die Grenze selbst zieht: „Nichts wird gerechnet, nichts entschieden."*

## Der Punkt, an dem eine Auflage im Code sitzt

```text
geometry/sparrenBerechnung.ts:100   N003_VORBEHALT = 'Vorbemessung, ersetzt keine
                                    prüffähige Statik'
enginePanels.ts:225                 { schluessel: 'vorbehalt', label: 'Vorbehalt' }
                                    -> Ergebnisfeld von engine-sparren
```

**Dasselbe Feld führen `engine-fbh` (`:264`) und `engine-abwasser` (`:354`)** — *dort aus A-17.*

## Kein Rechenaufruf

**`Math.*` kommt in beiden Dateien NULL Mal vor** — *739 Zeilen ohne eine einzige Rechenoperation.*
Siehe `3-FORMELN`.
