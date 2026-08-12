# W-38 · Schritt-Status und Prüfpunkte — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**W-38 rechnet nicht.** *Und das ist gemessen, nicht angenommen:*

```text
grep -c '^export function' resources/planner/hausplaner/app/studioDaten.ts     0
```

**Die Datei enthält Zahlen** — Farbwerte wie `rgba(28,40,48,.05)`, ein `goto: 1`. **Keine davon
steht in einer Rechnung.** *Das ist der Unterschied zwischen „enthält Zahlen" und „rechnet": eine
Zahl, die nur zugewiesen wird, ist ein Wert und keine Formel.*

> **Ich wollte das mit einem Rechenausdruck-Muster belegen und habe stattdessen H-9 vorgeführt.**
> *Gemessen mit `grep -cE '[0-9]+ *[*/+-] *[0-9]+'`:*

```text
6 Treffer — und KEINER ist eine Rechnung:
  Z.128  '<path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z"/>'   SVG-Pfad
  Z.138  '<path d="M3 21h18M6 21V11l6-4 6 4v10"/>'                 SVG-Pfad
  Z.139  '<path d="M3 21h18M5 21V8l7-4 7 4v13"/>'                  SVG-Pfad
  Z.140  '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/>'                   SVG-Pfad
  Z.158  meta: 'Rev. 42 · Schritt 2/11'                            Anzeigetext
  Z.160  meta: 'Rev. 12 · vor 3 Tagen'                             Anzeigetext
```

> **`6-4` in einem SVG-Pfad ist kein Minus, sondern zwei Koordinaten.** *Das Muster misst die
> Schreibweise und nicht die Sache — H-9, wörtlich.* **Der tragende Beleg ist deshalb die 0 bei
> `export function`, nicht diese 6.** *Ich lasse die Fehlmessung stehen, weil sie zeigt, wie leicht
> genau dieses Blatt eine Formel erfinden könnte, die es nicht gibt.*

## Reihenfolge der Anwendung

**Entfällt** — ohne Formel keine Reihenfolge.

## Fehlt eine Formel?

**Nein.** *Und diese Antwort ist wichtiger, als sie aussieht.*

> **Die Versuchung wäre, `statusAus()` hier als Formel einzutragen** — die Regel „ein Schritt ist
> `ok`, wenn alle Prüfpunkte `ok` sind" sieht wie eine aus. **Sie steht aber in
> `app/dashboard/fahrschritte.ts:43` und gehört damit zu W-34.** *Sie hier einzutragen hieße, eine
> Regel an zwei Orten zu führen — genau das, was das Vorwort dieses Blattes verbietet.*

**Was hier stünde, wenn W-38 sie besäße:** *eine F-Nummer und ein Satz, wofür.* **Was hier steht:
der Verweis, wo sie wirklich lebt.**

## Genauigkeit

**Gegenstandslos.** *Es gibt keine Eingangsgröße, keine Rundung und keine Toleranz ε — vier
Zeichenketten und drei Datenformen haben keine Genauigkeit.*

> **Eine bekannte Ungenauigkeit gibt es trotzdem, und sie ist keine Zahl:** *`STATUS_LABEL` bildet
> vier Stufen auf vier deutsche Wörter ab. Ändert jemand ein Wort, ändert sich, was der Anwender
> liest — ohne dass sich ein Wert ändert.* **Deshalb prüft `__tests__/gefuehrteEhrlich.test.ts:38`
> und `:43-45` alle vier Wörter zeichengenau.** *Siehe `6-PRUEFUNG.md`.*
