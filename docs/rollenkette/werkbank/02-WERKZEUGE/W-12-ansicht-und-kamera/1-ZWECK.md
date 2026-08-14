# W-12 · Ansicht und Kamera — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will dasselbe Gebäude von zwei Seiten sehen** — als Grundriss zum Zeichnen und als Körper zum
Beurteilen — **und im 3D-Bild um das Haus herumgehen können.**

Vier Gegenstände, alle gebaut:

1. **Der Ansichtszustand** — `2d`, `split` oder `3d`.
2. **Die Kamera** samt Steuerung — Umkreisen, Zoomen, Schwenken.
3. **Das Raster** in beiden Ansichtsschichten.
4. **F-032** — die Transformation, mit der 3D-Geometrie an ihren Platz kommt.

## Der tragende Punkt: `modus` heißt ZWEIMAL etwas anderes

**Zwei Typen, zwei Träger, zwei Wertemengen — und beide heißen `modus`:**

```text
HausplanerModus  '2d' | 'split' | '3d'
                 store/hausplanerStore.ts:20   Typ
                                        :28    das Feld im Modell-Store
                                        :45    setModus in der Schnittstelle
                                        :126   setModus im Rumpf

StudioModus      'start' | 'guided' | 'expert'
                 app/studioDaten.ts:97          Typ
                 app/HausplanerStudio.tsx:23    const [modus, setModus] = useState<StudioModus>
                                       :87      modeBtn(m: StudioModus, …)
```

> ***Und `setModus` gibt es damit ebenfalls zweimal*** — *einmal als Store-Handlung
> (`hausplanerStore.ts:45`/`:126`), einmal als React-Zustandssetzer in der Studio-Hülle
> (`HausplanerStudio.tsx:23`).* **Sie haben nichts miteinander zu tun.**

**Ohne beide Träger hält die nächste Rolle sie für EINEN Zustand.** *Wer `modus` sucht und den
ersten Treffer nimmt, landet je nach Datei in einer anderen Sache — und ein `setModus`-Aufruf, der
im falschen Store landet, schaltet nichts oder das Falsche.*

> *Dieselbe Lehre wie bei W-36, wo vier Statusachsen an vier verschiedenen Trägern hingen.*

**Der Unterschied in einem Satz:** *`HausplanerModus` ist die **Ansicht auf das Gebäude**,
`StudioModus` ist die **Arbeitsweise des Anwenders** (Start · geführt · Experte). Das eine liegt im
Modell-Store, das andere in der Hülle darum.*

## Wann greift der Anwender danach?

**Ständig, ohne es zu merken.** *Er zeichnet in 2D, prüft in 3D, arbeitet in `split` nebeneinander.
Der Wechsel ist kein eigener Arbeitsschritt, sondern die Grundhaltung.*

## Woran merkt er, dass es fehlt?

**Er könnte nur zeichnen oder nur ansehen.** *Ein Grundriss ohne Körper lässt Höhenfehler
unentdeckt; ein Körper ohne Grundriss ist nicht bemaßbar.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

- **Es ist KEIN Werkzeug im Sinne der Registry.** *Die Ansicht ist eine **Eigenschaft**, an der sich
  Werkzeuge ausrichten — siehe `7-GRENZEN`. Dieselbe Lage wie W-01s Fang: er liegt **unter** den
  Werkzeugen und ist keines.*
- **Keine Beleuchtung, keine Verschattung.** *Das ist W-19.*
- **Keine Umbenennung `modus → viewMode`.** *Ein eigener Hygiene-Slice, ausdrücklich benannt in
  `app/state/uiState.ts:11` — dieses Blatt nennt ihn und ändert nichts.*
