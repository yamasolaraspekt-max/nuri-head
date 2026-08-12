# W-38 · Schritt-Status und Prüfpunkte — FUNKTION

## Die vier Stufen — wörtlich, mit Fundstelle

```ts
resources/planner/hausplaner/app/studioDaten.ts:163
export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';
```

**Es sind genau vier, und das ist nicht behauptet, sondern an drei unabhängigen Stellen belegt:**

```text
1  die Typzeile oben        vier Alternativen in EINER Zeile, nachzaehlbar
2  studioDaten.ts:255-257   Record<SchrittStatus, string> — TypeScript erzwingt
                            Vollstaendigkeit; vier Schluessel stehen da, ein fuenfter
                            waere ein Uebersetzungsfehler
3  __tests__/gefuehrteEhrlich.test.ts:42
   assert.deepEqual(Object.keys(STATUS_LABEL).sort(), ['ok', 'open', 'prog', 'warn'])
                            ein Waechter, der eine fuenfte Stufe rot faerbt
```

> **Warum die Typzeile und nicht die Zahl:** *„vier" ist eine Behauptung, `'ok' | 'prog' | 'warn' |
> 'open'` ist der Bestand.* **Wer eine Stufe ergänzt, ändert Zeile 163 — und Zeile 42 des Tests
> schlägt fehl, bevor irgendetwas Falsches gerendert wird.**

## Eingabe

| Was | Typ | Einheit | Pflicht | Prüfung |
|---|---|---|---|---|
| — | — | — | — | **keine** |

**W-38 nimmt nichts entgegen.** *Die Datei ist rein deklarativ:*

```text
grep -c '^export function' resources/planner/hausplaner/app/studioDaten.ts   ->  0
```

## Verarbeitung — der Zustandsautomat

**Es gibt keinen.** *Und das ist eine Aussage, kein Auslassen:*

```text
Zustand A  ──Ereignis──►  Zustand B          gibt es hier NICHT
```

> **`SchrittStatus` beschreibt vier ZUSTÄNDE, aber kein Werkzeug führt von einem in den anderen.**
> *Den Wechsel bewirkt allein das Dokument: `app/dashboard/fahrschritte.ts:43` leitet ihn mit
> `statusAus(checks)` aus den Prüfpunkten ab.* **Wer hier einen Automaten sucht, sucht in W-34.**

## Ausgabe — die vier Datenformen

| Nr | Form | Fundstelle | Felder |
|---|---|---|---|
| 1 | `Pruefpunkt` | `studioDaten.ts:164` | `status: SchrittStatus` · `text: string` |
| 2 | `Aufgabe` | `studioDaten.ts:165` | **`warn?: boolean`** · `titel: string` · **`detail?: string`** |
| 3 | `Empfehlung` | `studioDaten.ts:166` | `titel: string` · `aktion: string` · **`cfg?: boolean`** |
| 4 | `Fahrschritt` | `studioDaten.ts:167-174` | `titel` · `status: SchrittStatus` · `hinweis` · `checks: Pruefpunkt[]` · `aufgaben: Aufgabe[]` · `empfehlung: Empfehlung \| null` |

**Die drei optionalen Felder sind eine Aussage, kein Schönheitsfehler:**

```text
warn?    Aufgabe:     eine Aufgabe ist NORMAL. Nur wenn warn gesetzt ist, ist sie
                      dringlich — die Dringlichkeit ist der Sonderfall, nicht der Regelfall.
detail?  Aufgabe:     ein Titel genuegt. Der Erklaersatz ist erlaubt, nicht gefordert.
cfg?     Empfehlung:  unterscheidet die Empfehlung, die einen KONFIGURATOR oeffnet, von der,
                      die nur weiterfuehrt. studioDaten.ts:206 ist die einzige mit cfg: true.
```

> **`empfehlung: Empfehlung | null` ist NICHT optional, sondern verpflichtend nullbar.** *Der
> Unterschied ist gewollt: ein `Fahrschritt` muss sich entscheiden, ob er eine Empfehlung hat —
> „vergessen" ist keine Möglichkeit.* **Gemessen: von den elf stillgelegten Schritten tragen sechs
> ausdrücklich `empfehlung: null`.**

## Kommando (für Rückgängig)

**Keines.** *W-38 ändert nichts am Datenmodell — es gibt nichts zurückzunehmen.* Rückgängig gehört
zu den Werkzeugen, die den `SceneDocument` schreiben.

## Schichtzuordnung

| Schicht | W-38 | Beleg |
|---|---|---|
| 1 Domäne | **nein** | die Datei importiert nichts aus `domain/` |
| 2 Geometrie | **nein** | keine F-Nummer, siehe `3-FORMELN.md` |
| 3 Anwendung | **ja** | `app/studioDaten.ts` — hier lebt es |
| 4/5 Darstellung | **nein, aber sichtbar** | die vier Wörter aus `STATUS_LABEL` liest der Anwender; gezeichnet werden sie von `app/GuidedView.tsx` (W-34) |

## Scope — was W-38 ist und was es nicht ist

```text
W-38 IST      das Statusmodell in studioDaten.ts: die vier Stufen, die vier Datenformen
              (Pruefpunkt, Aufgabe, Empfehlung, Fahrschritt), STATUS_LABEL, und die
              Kennzeichnung der stillgelegten Konstanten samt ihrer Waechter.

W-38 IST NICHT
              app/dashboard/fahrschritte.ts    -> gehoert W-34
              app/GuidedView.tsx               -> gehoert W-34
              app/dashboard/enginePanels.ts    -> gehoert W-37
              Sie BENUTZEN W-38s Typen. Benutzen ist nicht besitzen.
```

> **Hier liest es, wer weiterbaut.** *Keine Datei außerhalb `studioDaten.ts` wird für W-38
> angefasst. Wird beim Bauen klar, dass ohne Nachbardatei kein Blatt zu füllen ist, ist das eine
> **Meldung**, keine Scope-Erweiterung — §7.*
