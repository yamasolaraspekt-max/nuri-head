# W-34 · Geführte Planung — FUNKTION

## Eingabe

| Was | Typ | Einheit | Pflicht | Prüfung |
|---|---|---|---|---|
| das Dokument | `SceneDocument \| null` | — | **nein** | `null` ist ein gültiger Fall und ergibt elf offene Schritte |

**`null` ist kein Fehlerfall, sondern der Startzustand** — *`schrittTitel()` (`:200-202`) ruft
`ableitenSchritte(null)` ausdrücklich auf, um die Titel zu bekommen.*

## `statusAus` — die Regel, die W-38 nur als Typ kennt

```ts
fahrschritte.ts:43-49
export function statusAus(checks: readonly Pruefpunkt[]): SchrittStatus {
  if (checks.length === 0) return 'open';
  if (checks.some((c) => c.status === 'warn')) return 'warn';
  if (checks.every((c) => c.status === 'ok')) return 'ok';
  if (checks.every((c) => c.status === 'open')) return 'open';
  return 'prog';
}
```

**Fünf Zweige — und die REIHENFOLGE ist die Aussage, nicht die Aufzählung:**

| # | Bedingung | Ergebnis | Was das heißt |
|---|---|---|---|
| 1 | **keine** Prüfpunkte | `open` | *ein Schritt ohne prüfbare Aussage ist nicht fertig, er ist **unbekannt*** |
| 2 | **irgendein** `warn` | `warn` | **`warn` schlägt alles** — *ein einziger Warnpunkt macht den ganzen Schritt gelb, egal wie viele grün sind* |
| 3 | **alle** `ok` | `ok` | vollständig |
| 4 | **alle** `open` | `open` | nicht begonnen |
| 5 | sonst | `prog` | **kein eigener Test, sondern der REST** |

> **ZURÜCKGEZOGEN, und der Fehler ist meiner.** *Hier stand: „Zweig 2 steht VOR den
> `every`-Prüfungen — **deshalb** gewinnt `warn` gegen neunmal `ok`."* **Diese Ursache gibt es
> nicht.** *Ein `warn` bricht beide `every`-Bedingungen ohnehin; die Mengen sind disjunkt.* **Der
> Evaluator hat es gefunden (`e5716bc0`), und ich habe es selbst nachgemessen — beide Fassungen
> über alle 85 Kombinationen aus vier Statuswerten bei Länge 0 bis 3:**

```text
M1  der warn-Zweig HINTER die every-Pruefungen verschoben   0 Abweichungen von 85
```

**Die Reihenfolge trägt trotzdem — nur an einer anderen Stelle, und die ist gemessen:**

```text
M2  der LEER-Zweig HINTER die every-Pruefungen verschoben   1 Abweichung von 85
      []   original 'open'   ->   mutiert 'ok'
```

> **`checks.length === 0` MUSS vor den `every`-Prüfungen stehen, weil `[].every(...)` WAHR ist.**
> *Steht der Leer-Zweig dahinter, liefert eine leere Prüfpunktliste `ok`.* **Fachlich ist das genau
> die Lüge, die dieses Werkzeug abschafft: ein Schritt OHNE Prüfpunkt meldete „Vollständig"** — *und
> die sechs Schritte ohne Modellgrundlage haben alle `checks: []`.*

**Und Zweig 5 hat keine Bedingung** — *`prog` bedeutet „weder ganz grün noch ganz offen", es wird
nie geprüft, sondern übrig gelassen.*

> **Was ich daraus mitnehme:** *ich hatte eine Reihenfolge gesehen und ihr eine Wirkung
> **zugeschrieben**, statt sie zu messen.* **Die Aussage klang tragend und war es nicht — und die
> tragende Stelle stand direkt daneben.** *Der Dateikopf `:40-41` hatte sie sogar benannt: „**Leere
> Liste ⇒ `open`** — ein Schritt ohne prüfbare Aussage ist nicht fertig, er ist unbekannt."*
>
> *Der Dateikopf sagt dasselbe kürzer (`:23-24`): „`status` folgt aus den Prüfpunkten, nicht
> umgekehrt: alle erfüllt → `ok`, keiner → `open`, gemischt → `prog`, ein verletzter Zwang →
> `warn`."*

## Die elf Schritte — am Code gezählt, nicht aus den Tests übernommen

**Gemessen an den Einträgen des `return`-Arrays in `ableitenSchritte` (`:124-196`), Kommentarzeilen
ausgenommen:**

```text
 1  :125  Projektgrundlagen            ohne Grundlage
 2  :128  Import oder Grundriss        ohne Grundlage, mit EINEM messbaren Teil (Waende)
 3  :134  Geschosse und Gebäude
 4  :145  Fenster, Türen und Treppen
 5  :157  Dach und Fassade
 6  :162  Räume und Einrichtung        ohne Grundlage, mit EINEM messbaren Teil (Raeume)
 7  :168  Küche und Bad                ohne Grundlage, mit EINEM messbaren Teil (Sanitaer)
 8  :174  Elektro
 9  :184  TGA
10  :194  Prüfung und Koordination     ohne Grundlage
11  :195  Dokumentation und Rendering  ohne Grundlage

Eintraege im return-Array: 11
```

> **Warum am Code und nicht aus `fahrschritte.test.ts`:** *dort stehen zwei Zusagen auf 11 —*
> `:37` *(`assert.equal(schritte.length, 11)`) und* `:71` *(`assert.equal(STEPS_STILLGELEGT.length,
> 11)`).* **Ein Test ist ein Beleg für eine ERWARTUNG, nicht für den Bestand.** *Stimmten Code und
> Test nicht überein, wäre die Testzahl die falsche Quelle.*
>
> *Beim Nachmessen dieser beiden Fundstellen fiel ein dritter Treffer an — `:20` trägt die UUID
> `'11111111-1111-4111-8111-111111111111'`. **Ein Wort ist kein Beleg, erst die Stelle ist einer**
> (H-6); ich habe alle drei geöffnet.*
>
> **Und die Zählung hat mich zweimal fast betrogen:** *ein Muster auf `schritt(`/`ohneGrundlage(`
> zählte **16**, weil es die geschachtelten Zweige der Ternärausdrücke mitnahm; eines auf die
> Einrückung zählte **13**, weil zwei Kommentarzeilen auf derselben Ebene stehen (`:127`, `:161`).*
> **Erst „genau vier Leerzeichen UND keine Kommentarzeile" trifft die Array-Einträge.**

**Drei der elf sind Ternärausdrücke** (`:128`, `:162`, `:168`) — *sie liefern je nach Modellinhalt
einen Schritt **mit** einem Prüfpunkt oder einen ohne Grundlage. Ein Eintrag, zwei mögliche
Gestalten.*

## Die zweite Ehrlichkeitsregel: `bebauteGeschosse`

```ts
fahrschritte.ts:84-88
const bebauteGeschosse = (scene?.levels ?? []).filter((l) => (
  nodes.some((n) => n.levelId === l.id)
  || (scene?.roofs ?? []).some((r) => r.levelId === l.id)
  || (scene?.ceilings ?? []).some((c) => c.levelId === l.id)
)).length;
```

**Gezählt wird nicht, wie viele Geschosse existieren, sondern wie viele etwas TRAGEN** — *`nodes`,
`roofs` oder `ceilings` müssen darauf verweisen.* **Die Begründung steht im Code selbst
(`:77-83`), wörtlich:**

> *„Ein frisch angelegtes Projekt **hat** bereits ein Geschoss, weil die Anwendung es anlegt, nicht
> der Nutzer. „1 Geschoss angelegt ✓" wäre also genau die Sorte Behauptung, die dieser Posten
> beseitigt — grün, ohne dass jemand etwas getan hat. Gezählt wird deshalb, was das Geschoss
> trägt."*

**Beide Zahlen erscheinen nebeneinander** (`:136`, `:140`): *„`3` von `4` Geschossen bebaut" — die
rohe Zahl wird nicht versteckt, sie wird nur nicht als Leistung ausgegeben.*

> **Derselbe Bautyp wie W-20 und W-38:** *die Stufe-6-Bausteine sind **Ehrlichkeitskonstruktionen**.
> Wer sie als gewöhnliche Ansichtslogik beschreibt, verfehlt ihren Zweck.*

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| die elf Schritte | `Fahrschritt[]` | `GuidedView` über die Eigenschaft `schritte` |
| nur die Titel | `string[]` | `schrittTitel()` `:200-202`, für den Vergleich gegen die stillgelegten Demo-Daten |

## Reinheit — und das ist keine Formalie

```text
fahrschritte.ts:19-21, woertlich:
  "Kein Store-Zugriff, kein Date, kein Zufall. Dieselbe Szene ⇒ dasselbe Ergebnis, immer."
```

**Bewacht von `fahrschritte.test.ts` K3** *(„zweimal mit demselben Dokument ⇒ tief gleiches
Ergebnis")* **und K2** *(„die Ableitung LIEST das Dokument — sie ändert es nicht").*

## Kommando (für Rückgängig)

**Keines.** *W-34 schreibt nicht ins Dokument. Es gibt nichts zurückzunehmen.*

## Schichtzuordnung

| Schicht | W-34 | Beleg |
|---|---|---|
| 1 Domäne | **liest** | `import type { SceneDocument, SceneNode }` `:26` |
| 2 Geometrie | **nein** | keine F-Nummer, siehe `3-FORMELN.md` |
| 3 Anwendung | **ja** | `app/dashboard/fahrschritte.ts` |
| 4/5 Oberfläche | **ja** | `app/GuidedView.tsx` — Stepper, Schrittkarte, Prüfpunkte |

## Scope — was W-34 ist und was es nicht ist

```text
W-34 IST      app/dashboard/fahrschritte.ts  — statusAus, SCHRITTE_OHNE_GRUNDLAGE,
                                               ableitenSchritte, schrittTitel
              app/GuidedView.tsx             — die Darstellung der vier Stufen
                                               (badgeFarbe, checkFarbe)

W-34 IST NICHT
              app/studioDaten.ts   -> gehoert W-38 (BESCHRIEBEN). W-34 BENUTZT seine
                                      Typen per Import in GuidedView.tsx:4.
                                      Benutzen ist nicht besitzen.
              app/EngineFlaeche.tsx / app/dashboard/enginePanels.ts  -> gehoert W-37
```

**Hier liest es, wer weiterbaut.** *Keine Datei außerhalb dieser zwei wurde für W-34 angefasst, und
`studioDaten.ts` ist unberührt — es ist gerade abgenommen.*
