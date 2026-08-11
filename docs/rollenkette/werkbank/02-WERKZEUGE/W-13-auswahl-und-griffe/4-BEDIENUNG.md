# W-13 · Auswahl und Griffe — BEDIENUNG

## Das Werkzeug gibt es wirklich

```text
resources/planner/hausplaner/app/tools/toolRegistry.ts:39   id: 'auswahl'
```

**Als einziges Klasse-A-Werkzeug.** *Bei W-01, W-05, W-08, W-21 und W-22 steht die Rechenschicht
allein; hier kann der Anwender das Werkzeug wählen.*

## Was er drückt und was passiert

| Eingabe | Modus | Wirkung |
|---|---|---|
| Klick | `replace` | nur dieses Objekt |
| **Shift** + Klick | `add` | dazunehmen |
| **Ctrl** / **Cmd** + Klick | `toggle` | drin → raus, draußen → rein |
| **Alt** + Klick | `remove` | herausnehmen |
| Klick ins Leere | — | **Auswahl weg** |
| Klick ins Leere **mit** Modifikator | — | **Auswahl bleibt** |

**Die letzte Zeile ist die, die man vermisst, wenn sie fehlt:** wer bei gedrückter Umschalttaste
danebentrifft, verliert nicht alles (`auswahlModus.ts:93-95`).

## Was er bei mehreren sieht

Eine **Mehrfach-Ansicht mit Anzahl je Typ** — keine Einzelfelder. Die Kante steht wörtlich im Code:
*„Mehrfachauswahl gemischter Typen (Wand + Fenster + Dach): das Panel darf **nicht raten**."*
(`resources/planner/hausplaner/app/tools/auswahlUebersicht.ts:3-5`)

`benenne()` (Z.73) liefert Ein- und Mehrzahl — und **bei unbekanntem Typ den Typnamen selbst**,
statt eine Bezeichnung zu erfinden.
