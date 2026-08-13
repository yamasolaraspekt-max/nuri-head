# W-31 · PV-Schnellbelegung — PRÜFUNG

## Zwei Wächter — und eine dritte Stelle, die KEINE ist

**Über Importzeilen gemessen, nicht über Wortvorkommen.**

| Stelle | Zugriff | was sie leistet |
|---|---|---|
| `__tests__/pvBelegung.test.ts` (48 Z., 5 Zusagen) | **IMPORT** (`:6`) | die Rechnung selbst |
| `__tests__/enginePanelRest.test.ts` (10 Zusagen) | **IMPORT** (`:18`) | Panel und Rechnung zusammen (`:24` holt `enginePanel('engine-pv')`, `:53` rechnet gegen) |
| `app/tools/faehigkeiten.ts:80` | **nur STRING** | **keine Verriegelung** — Registry-Verdrahtung |

> **Die dritte Zeile gehört in diese Tabelle, obwohl sie kein Wächter ist.** *Sie nennt
> `'geometry/pvBelegung'` als Zeichenkette. Wer „wer prüft W-31" über Textsuche beantwortet, zählt
> sie mit und kommt auf drei.* **Gemessen sind es zwei, und der Unterschied ist die Zugriffsart.**

## Was `pvBelegung.test.ts` festhält — fünf Zusagen im Wortlaut

```text
'Rechteckdach 10×8 m, Standardmodul: plausible Modulzahl + kWp'
'waehlt die Orientierung mit mehr Modulen'
'zu kleines Dach -> 0 Module, kein Absturz'
'mehr Flaeche -> nie weniger Module (Monotonie)'
'Determinismus'
```

> **Drei davon sind EIGENSCHAFTEN und keine Beispiele, und das ist die Stärke dieses Wächters.**
>
> - **Monotonie** — *„mehr Fläche ⇒ nie weniger Module" ist eine Aussage über **alle** Eingaben.
>   Eine Umstellung, die bei einem Beispiel richtig bleibt und die Spaltkorrektur verdreht, fällt
>   hier auf.*
> - **Determinismus** — *fängt genau die Stelle, an der `nimmHoch = hochN >= querN` steht: bei
>   Gleichstand muss immer dieselbe Lage gewinnen.*
> - **„kein Absturz"** — *belegt die `Math.max(0, …)`-Klemmen (`:38`, `:39`, `:49`, `:50`): ein zu
>   kleines Dach ergibt 0 Module und nicht eine negative Spaltenzahl.*

**Die zwei übrigen sind Beispiele mit echten Maßen** — *10 × 8 m mit einem Standardmodul; die
Zahlen stehen im Test und nicht in diesem Blatt, damit sie an einer Stelle gepflegt werden.*

## Was `enginePanelRest.test.ts` zusätzlich hält

**Es prüft das PANEL gegen die RECHNUNG** (`:24`, `:53`): *dass es den Eintrag `engine-pv`
überhaupt gibt, und dass sein `berechne` dasselbe liefert wie ein direkter Aufruf von
`pvSchnellBelegung`.* **Damit ist der Adapter `alsPvEingabe()` mitverriegelt** — *eine Umbenennung
eines Feldschlüssels würde die zwei auseinanderlaufen lassen.*

## Was NICHT geprüft wird

- **Die Fachfläche `fach-pv-module` hat keinen eigenen Wächter für W-31.** *Sie ist Anzeige; dass
  sie gerendert wird, belegen die Wächter von `FachFlaeche`/`HausplanerStudio` — nicht diese.*
- **Kein Browser-Nachweis.** *Beide Wächter messen Rechnung und Datenstruktur.*
- **Die Vorgabewerte 300 mm und 20 mm** sind in den Zusagen nicht als Zahl festgeschrieben. *Wer sie
  ändert, wird von keinem Wächter aufgehalten — sie sind Voreinstellung, nicht Zusage
  (siehe `3-FORMELN`).*
