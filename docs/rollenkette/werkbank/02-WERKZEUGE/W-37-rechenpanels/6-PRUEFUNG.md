# W-37 · Rechenpanels — PRÜFUNG

## Zwei Klassen, getrennt geführt — IMPORT und NUR QUELLE

*Wer nur eine Klasse zählt, kommt zu einem falschen Schluss. Beide sind am Bau-Stand erhoben.*

### Klasse 1 — IMPORT (sechs Wächter)

| Wächter | Z | Zusagen | `enginePanel` | `startwerte` + `fehlendePflichtfelder` |
|---|---|---|---|---|
| `enginePanelTreppe.test.ts` | 199 | 11 | ✓ | ✓ |
| `enginePanelSparren.test.ts` | 150 | 9 | ✓ | ✓ |
| `enginePanelRest.test.ts` | 98 | 10 | ✓ | ✓ |
| `enginePanelTgaHeizung.test.ts` | 98 | 10 | ✓ | ✓ |
| `zweiEnginesSchweigen.test.ts` | 76 | 2 | ✓ | — |
| **`sparrenVorbehalt.test.ts`** | 34 | 1 | ✓ | — |

> **`enginePanel` steht in ALLEN SECHS** — *sie ist die breitest benutzte Ausfuhr des Moduls.*
> **`startwerte` und `fehlendePflichtfelder` stehen in VIEREN, und zwar immer zusammen** — *sie
> gehören zum selben Vorgang: vorbelegen, dann prüfen, was fehlt.*

### Klasse 2 — NUR QUELLE (drei Stellen)

```text
stilschicht.test.ts:584 :586 :603      verriegelt EngineFlaeche.tsx ueber ihren QUELLTEXT
gesperrtAppWeit.test.ts:41 :134        dieselbe Klasse
fussUndUeberlagerungen.test.ts:175     als Markenstring
```

> ***„Null Importe" ist richtig gezählt — und der Schluss „gehört nicht dazu" wäre falsch.*** *Diese
> drei verriegeln die Datei, ohne sie zu importieren.* **Ort ist nicht Wirkung.** *Wer die
> Wächterfrage nur über Importzeilen beantwortet, erklärt `EngineFlaeche.tsx` für ungeprüft.*

## Der wichtigste Wächter dieses Blattes: `sparrenVorbehalt.test.ts`

**34 Zeilen, EINE Zusage — und sie hält Yamas A-14-Auflage.**

**Sein Kopf sagt wörtlich:** *„A-14 — der N-003-Vorbehalt als **Zusage, nicht als Probelauf**."*

```text
:12  test('A-14-2: vorbehalt ueberlebt berechneSparren(...) as unknown as EngineErgebnis')
:27  assert.equal(ergebnis.vorbehalt, …)      der Wert kommt DURCH die Typwandlung
:32  assert.ok(panel.ergebnisFelder.some(f => f.schluessel === 'vorbehalt'))
                                              und das Panel ZEIGT ihn auch
```

> ***Die Zusage prüft ZWEI Dinge, und beide sind nötig:*** *dass der Vorbehalt die Wandlung
> `as unknown as EngineErgebnis` überlebt — **und** dass das Panel ein Ergebnisfeld dafür führt.*
> **Eines allein genügt nicht:** *ein durchgereichter Wert, den kein Feld anzeigt, erreicht den
> Anwender nicht; ein Feld ohne Wert bleibt leer.*

**A-14 ist betriebsbestätigt. Dieser eine Test ist heute das, was eine stille Entfernung des
Vorbehalts auffallen ließe.** *Wer ihn löscht, löscht die Durchsetzung einer Auflage — und dieses
Blatt ist der Ort, an dem das sichtbar ist.*

## Was `zweiEnginesSchweigen.test.ts` hält

**Zwei Zusagen über `ENGINE_PANELS` als Ganzes** — *es fährt jedes Panel mit seinen Vorgabewerten
(`werteAusVorgaben`, `:12`).* **Damit ist die Liste selbst verriegelt:** *ein Panel, dessen
`berechne` mit den eigenen Startwerten wirft, fällt hier auf, ohne dass jemand einen eigenen Test
dafür schreibt.*

## Was NICHT geprüft wird

- **Die Schwere-Anzeige hat keinen eigenen Wächter für Zeichen und Wort.** *Der Fehler in ihrer
  ersten Fassung — bestandene Prüfungen als „✕ Fehler" — wurde von der **Sichtprobe** gefunden, nicht
  von einer Zusage. Der Kommentar im Code sagt das selbst (`EngineFlaeche.tsx:174-176`).*
- **Keine Browserabnahme in diesen Wächtern.**
