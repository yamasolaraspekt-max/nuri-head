# W-38 · Schritt-Status und Prüfpunkte — GRENZEN

> **Dieses Blatt ist Pflicht.**

## Die härteste Grenze zuerst: dieses Werkzeug tut nichts

```text
grep -c '^export function' resources/planner/hausplaner/app/studioDaten.ts    ->  0
grep -c '^import'          resources/planner/hausplaner/app/studioDaten.ts    ->  0
```

**Null Funktionen. Null Importe.** *W-38 ist ein Vokabular: vier Wörter, vier Datenformen, eine
Übersetzungstabelle.* **Jede Aussage der Form „W-38 prüft…", „W-38 berechnet…", „W-38 entscheidet…"
ist falsch, egal wie plausibel sie klingt.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| einen Status **ermitteln** | keine Funktion in der Datei | den von `fahrschritte.ts:43` abgeleiteten Status (W-34) |
| einen Status **anzeigen** | zeichnet nichts | die Plakette aus `GuidedView.tsx:71` (W-34) |
| einen Status **speichern** | schreibt nicht ins Dokument | nichts — der Status wird bei jedem Laden neu abgeleitet |
| Gültigkeit von **Ergebnissen** ausdrücken | anderer Wortschatz, siehe unten | — noch nichts, W-40 hat keinen Code |

## Die Absagekette

**Es gibt keine, und das ist begründet und nicht vergessen.**

```text
Schicht 1/2 wirft benannten Fehler        -> W-38 hat keine Schicht 1/2
Schicht 3 faengt und uebersetzt           -> es gibt nichts zu fangen
Schicht 4 reicht DURCH                    -> W-38 erreicht Schicht 4 nie
Schicht 5 zeigt einen verstaendlichen Satz-> die vier Woerter SIND dieser Satz
```

> **Eine Konstantentabelle kann nicht scheitern.** *Der teuerste Fehler des Projekts — eine
> geschluckte Absage — braucht einen Fehlerpfad, und den hat W-38 nicht.* **Die Gefahr liegt hier
> nicht im Verschlucken, sondern im FALSCHEN WORT.**

## Die eine Falschauskunft, die hier möglich war — behoben und bewacht

**`ok` trug einmal ein Wort aus der Freigabe-Sprache und behauptete damit einen Vorgang, den es
nicht gegeben hatte** — `studioDaten.ts:245-254`:

> *„Es behauptet einen Vorgang, den es nicht gegeben hat — **niemand hat etwas geprüft und
> bestätigt**."*

**Bewacht durch `__tests__/gefuehrteEhrlich.test.ts:38, :43, :44, :45`** — *jedes der vier Wörter
zeichengenau.* **An die Stelle einer Absagekette tritt hier eine Wortprüfung.**

## Die zwei Attrappen — stillgelegt, nicht gelöscht

| Konstante | Fundstelle | Was sie war | Wächter |
|---|---|---|---|
| `ZULETZT_STILLGELEGT` | `:157` | drei erfundene Projekte, die **jedem** Nutzer beim ersten Start erschienen | `startEhrlich.test.ts:24, :37, :43, :44` |
| `STEPS_STILLGELEGT` | `:186` | elf Schritte, die Tatsachen über ein leeres Dokument behaupteten | `gefuehrteEhrlich.test.ts:100` · `fahrschritte.test.ts:71, :174` |

> **Wer sie als Fähigkeit beschreibt, schreibt ein Blatt, das dem Code widerspricht.** *Sie sind
> **Attrappendaten** — der Dateikopf bei `:144-156` und `:176-185` sagt beides ausdrücklich:
> „Nichts rendert sie mehr."*
>
> **Und wer sie „aufräumt", zerstört einen Beleg:** *sie stehen als Nachweis dessen, was vorher
> behauptet wurde, und als Vergleichsgrundlage für den Test.* **Deshalb die Wächter.**

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| **keine** | — | *vier Zeichenketten haben keine Genauigkeit* |

## Offener Anschluss: W-40 führt einen ZWEITEN Wortschatz

```text
W-38  ok · prog · warn · open                       studioDaten.ts:163, im Code
W-40  confirmed · outdated · blocked                REGISTER.md Stufe 6, KEIN Code
```

**Gemessen: es sind zwei verschiedene Wortschätze, kein gemeinsamer.** *Kein Wort kommt in beiden
vor.*

> **Damit steht die Frage im Raum, ob W-40 ein zweites Statussystem neben W-38 einführt.** *Der
> Wächter „keine verwaisten zweiten Wahrheiten" spricht dagegen.* **Die Frage gehört zu W-40 und
> wird hier NICHT entschieden — sie steht hier, damit sie nicht verlorengeht.**

**Was für zwei getrennte Systeme spricht:** *sie beschreiben Verschiedenes.* `SchrittStatus` sagt
*„wie weit ist dieser Planungsschritt"*, `confirmed/outdated/blocked` sagt *„ist dieses Ergebnis
noch gültig"*. **Ein Schritt kann `ok` sein und sein Ergebnis trotzdem `outdated`, wenn sich die
Geometrie darunter geändert hat.**

**Was für eine Zusammenlegung spricht:** *das Register nennt `W-40 braucht W-38`* — **wer aufeinander
aufbaut, sollte nicht zwei Vokabulare führen.**

> **Die Entscheidung liegt beim Planner, wenn W-40 geschnitten wird.** *W-38 hat sie nicht zu
> treffen und trifft sie nicht.*

## Was später kommen könnte

*Absichtlich weggelassen, damit es nicht als Fehler gemeldet wird:*

```text
- eine fuenfte Stufe (z.B. 'blocked')   -> waere die W-40-Frage, nicht eine W-38-Erweiterung
- ein Kurzwort je Stufe fuer enge Spalten
- Farbrollen je Stufe in T              -> heute stehen sie in GuidedView.tsx:18/:22 (W-34)
```
