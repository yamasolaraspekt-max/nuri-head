# VOTUM Z1-W1-2 — „Walmdach: ungültige Kontur wird abgelehnt statt still falsch gerechnet"

**evaluator · 21.08. · Bau `60c04eef` · Basis `11f7c4c3` · Prüfstand `52c861a3`**

## Ergebnis: KEIN abschließendes Votum — vier von fünf Kriterien erfüllt, **E ist `ENV_BLOCKED`**

| # | Ergebnis | Beleg (selbst gerechnet) |
|---|---|---|
| A | **erfüllt** | Walm 6×8 m/30° → **wirft** `DachGeometrieUngueltig`, `grund='walm_giebelbreite_ueber_laenge'` |
| B | **erfüllt** | Walm 4×10 m/30° → **wirft**, derselbe Grund |
| C | **erfüllt** | `git show --numstat`: 39+21 Anfügungen, **0 Löschungen**; Bestandstest 8×12 unverändert vorhanden |
| D | **erfüllt** | `walmIstKonsistent`: genau **1** Definition (`dachformVorlagen.ts:414`), 2 produktive Verbraucher, keine zweite Fassung |
| E | **`ENV_BLOCKED`** | Browserabnahme — geteilte Testablage, zweimal unter der Messung neu aufgesetzt (`d40adbf5`) |

## Die Fehlsperre ist vermieden — und das habe ich nachgerechnet, nicht geglaubt

Der Code behauptet an `:146-148`, das Zeltdach (`L = B`) rechne exakt: *„nachgemessen bei 30°, 35°
und 45° je ±0,0 % gegen den Erhaltungssatz."* **Unabhängig nachgerechnet** (Σ Facetten gegen
Grundriss / cos α):

| Fall | ist | soll | Abweichung | First |
|---|---|---|---|---|
| Walm 8×12 m 30° | 110,85 m² | 110,85 m² | **0,000 %** | 4000 mm |
| Zeltdach 8×8 m 30° | 73,90 m² | 73,90 m² | **0,000 %** | **0 mm** |
| Zeltdach 8×8 m 35° | 78,13 m² | 78,13 m² | **0,000 %** | 0 mm |
| Zeltdach 8×8 m 45° | 90,51 m² | 90,51 m² | **0,000 %** | 0 mm |

Auch die Sollwerte des Blattes stimmen: 6×8 m = 48 m² → 48/cos30° = **55,43 m²**; 4×10 m = 40 m² →
**46,19 m²**. Genau die Zahlen, die es nennt.

## Der Widerspruch, den der Gesamtauftrag mir aufgibt — aufgelöst

Der Plan-Prüfer meldete: dieselbe Regelfunktion, zwei Urteile bei `L = B`. **Zeichengenau belegt:**

```
dachformVorlagen.ts:415   lengthM > widthM            → L=B gilt als INKONSISTENT
dachformVorlagen.test.ts:177  walmIstKonsistent(8,8) === false   (Zusage im Test)
dachformVorlagen.ts:478   warnt bei !walmIstKonsistent → warnt also auch bei L=B
dachGeometrie.ts:150      !walmIstKonsistent(...) && laengeM !== spannM  → sperrt NICHT bei L=B
```

**Der Bau löst ihn richtig und begründet es:** Er nimmt `L = B` von der Sperre aus, weil dieser Fall
nachweislich exakt rechnet — meine Messung bestätigt das mit **0,000 %** bei drei Neigungen. Eine
Sperre dort wäre eine Fehlsperre gegen einen funktionierenden Fall. Der Bau schreibt genau das hin
und schafft **keine dritte Fassung der Regel** (Kriterium D grün).

**Was offen bleibt, gehört nicht diesem Bau:** `walmIstKonsistent` nennt `L = B` weiterhin
„inkonsistent", und `dachformVorlagen.ts:478` warnt bei einem Fall, der exakt rechnet. *Die Funktion
trägt einen Namen, den ihre eigene Bedingung nicht deckt.* **SPEC-Punkt, Ball beim Planner** — der
Gesamtauftrag führt ihn ausdrücklich.

## Warum ich hier nicht abschließe

Kriterium E verlangt die Browserabnahme („sichtbare Absage, kein stilles Ergebnis"), und der
Gesamtauftrag bindet mich wörtlich: *„offene Browserprüfung niemals als bestanden melden."* Die
Bühne lief, die Insel lud, das Werkzeug war gewählt — zweimal wurde die geteilte Testablage unter
der Messung neu aufgesetzt (20:44 und 21:03, `d40adbf5`).

**Sobald die Testablage getrennt ist, ist E in einem Zug nachzuholen**; A–D bleiben gültig, sie
hängen nicht am Browser.

## Weitergabe

**Offen beim Evaluator** (E), **SPEC-Punkt beim Planner** (`walmIstKonsistent` / `:478`),
**Testablage-Trennung bei Yama**. Zustand setze ich nicht — `docs/STATUS.md` ist mir nach A-37-6
gesperrt.
