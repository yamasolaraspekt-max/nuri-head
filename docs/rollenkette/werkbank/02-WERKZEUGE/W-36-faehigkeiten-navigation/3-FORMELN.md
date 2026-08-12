# W-36 · Fähigkeiten-Navigation — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**Ein Verzeichnis rechnet nicht.** *W-36 zeigt, welche Rechnungen es gibt — und ruft keine davon
auf.* **Die dreizehn Rechen-Engines werden `referenziert`, nicht ausgeführt:** *Dateikopf `:13`,
„nur referenziert/aufgerufen, nie geändert (Byte-Treue)".*

## Die vier Funktionen sind Auswahl, nicht Rechnung

```ts
:106   faehigkeitenNach(g)   FAEHIGKEITEN.filter(f => f.gruppe === g)
:111   alleFaehigkeiten()    [...FAEHIGKEITEN]
:127   faehigkeitNach(id)    FAEHIGKEITEN.find(f => f.id === id)
```

**Drei Zeilen, drei Standardoperationen.** *`alleFaehigkeiten` gibt eine **Kopie** zurück (`[...]`) —
die Liste selbst ist `readonly`, und die Kopie verhindert, dass ein Aufrufer sie umsortiert.*

> **`faehigkeitenNach` bewahrt die Anzeigereihenfolge**, *und der Kommentar sagt das ausdrücklich
> („Anzeigereihenfolge").* **`filter` erhält die Reihenfolge des Ausgangsfeldes — die Reihenfolge in
> `FAEHIGKEITEN` ist also die Reihenfolge auf dem Bildschirm.** *Wer die Liste umsortiert, sortiert
> die Navi um.*

## Die einzige Stelle mit einer eigenen Regel: `doppelteIds`

```ts
:116-124
export function doppelteIds(): string[] {
  const gesehen = new Set<string>();
  const doppelt = new Set<string>();
  for (const f of FAEHIGKEITEN) {
    if (gesehen.has(f.id)) doppelt.add(f.id);
    gesehen.add(f.id);
  }
  return [...doppelt];
}
```

**Zwei Mengen, ein Durchgang.** *Der Kommentar nennt den Zweck: „Doppelte ids
(Konsolidierungs-Schutz). **Leere Liste = eine Wahrheit ohne Kollision.**"*

```text
Was sie liefert   JEDE Kennung, die mehr als einmal vorkommt — EINMAL.
                  Das zweite `gesehen.add` ist unschaedlich, und `doppelt` ist
                  ein Set: eine dreifach vergebene Kennung erscheint trotzdem
                  nur einmal in der Ausgabe.
Was sie NICHT     wie oft. Und nicht, WELCHE der drei Quellen kollidiert.
liefert
```

> **Das ist keine Feinheit, sondern der Kern des Werkzeugs.** *Die Liste entsteht aus drei Quellen —
> `:99` `export const FAEHIGKEITEN: readonly Faehigkeit[] = [` und darunter `:100-102` die drei
> Spreizungen; ohne diesen Schutz könnte ein Werkzeug aus `TOOL_DEFINITIONS` dieselbe Kennung tragen
> wie eine Engine, und die Navi zeigte zwei Zeilen, von denen `faehigkeitNach` nur die erste findet.*
> **„EINE Wahrheit" ist der Anspruch des Dateikopfes — `doppelteIds` ist der einzige Beleg, dass er
> eingehalten wird.**

**Und er wird geprüft, zweimal:** *`faehigkeiten.test.ts:10` und `toolPresentation.test.ts:180`,
beide `assert.deepEqual(doppelteIds(), [])`.*

## Die Zählung, die NICHT im Werkzeug steht

**Die Gesamtzahl der Fähigkeiten wird nirgends im Produktivcode berechnet** — *sie steht als
Zusage in einem Test:*

```ts
schienenReiter.test.ts:95-98
assert.equal(alleFaehigkeiten().length, 22 + EIGENE_WERKZEUGE.length + AUS_PAKET_GEHOBEN.length);
```

**`22 + 1 + 2 = 25`** — *`EIGENE_WERKZEUGE` ist `['kontur']` (`toolRegistry.ts:335`),
`AUS_PAKET_GEHOBEN` ist `['bemassen', 'flaeche-messen']` (`:332`).*

> **Die Zerlegung in drei Summanden ist Absicht, und der Testkommentar begründet sie:** *„Drei
> Summanden statt zwei, damit die Zusage weiterhin sagt, WELCHE Gruppe sich verändert hat. **Eine
> Gesamtzahl hätte gedeckt, dass ein Paket-Werkzeug verschwindet, während ein gehobenes
> dazukommt.**"*
>
> **Das ist dieselbe Regel, die diese Werkbank für Zählungen führt — hier von einem Test
> durchgesetzt statt von einer Rolle.** *Eine Summe ohne Menge ist kein Beleg (B6).*

## Genauigkeit

**Gegenstandslos.** *Keine Zahl verlässt dieses Werkzeug — es liefert Zeichenketten und
Objektlisten.* **Die einzigen Zahlen sind Längen, und die entstehen aus `length`.**
