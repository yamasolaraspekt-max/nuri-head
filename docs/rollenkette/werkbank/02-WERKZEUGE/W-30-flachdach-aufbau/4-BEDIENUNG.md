# W-30 · Flachdach-Aufbau — BEDIENUNG

## Der Anwender kann ein Flachdach wählen — und bekommt Neigung NULL

```text
app/rahmen/EigenschaftenPanel.tsx:251   <option value="flach">Flachdach</option>
                                 :258   Neigung (Grad)  min={0} max={89}
geometry/dachVorlage.ts:23              { form: 'flach', label: 'Flachdach', neigungGrad: 0 }
```

> ***Die Insel setzt beim Flachdach die Neigung auf 0 und lässt im Eingabefeld 0 bis 89 zu.***
> **Eine Klemmung gibt es hier nicht:** `clampPitchGrad` *hat in `app/` und in `dachVorlage.ts`
> **null** Verbraucher.*

## Und daneben steht die andere Welt mit einem ganz anderen Band

```text
dachformVorlagen.ts:497   category 'flat'  ->  clampPitchGrad(pitch, 1.5, 8)
                                               + Warnung PITCH_GEKLEMMT
```

| | Insel (`dachVorlage`, Panel) | Vorlagen (`dachformVorlagen`) |
|---|---|---|
| **Flachdach heißt** | `roofType: 'flach'` | `category: 'flat'`, `engineShape: 'rect'` |
| **Neigung** | **0°**, Eingabe 0…89 frei | **geklemmt auf 1,5°…8°** |
| **bei Grenzverletzung** | nichts | `PITCH_GEKLEMMT`, „kein stilles Abschneiden" |
| **Berührung der beiden** | **keine** — kein Import, kein gemeinsamer Aufruf |

> ***Zwei Flachdächer, zwei Definitionen, kein Übergang.*** **Das eine hat per Vorgabe genau die
> Neigung, gegen die das andere klemmt** — *0° ist der Fall „Wasser steht", und `spannweiteHinweis`
> nennt ihn beim Namen: „Durchbiegung/Pfützenbildung beachten".*
>
> **Das ist die schärfste Stelle dieses Werkzeugs**, und sie ist nicht theoretisch: *wer in der
> Insel ein Flachdach anlegt, bekommt 0° und keine Warnung; wer dieselbe Sache über eine Vorlage
> anlegt, bekommt mindestens 1,5° und eine Meldung, falls er darunter wollte.*

## Was der Anwender NICHT bedienen kann

| | |
|---|---|
| **Attika** | kein Eingabefeld in der Insel — das Feld lebt in den Vorlagen (`:163`) |
| **Gefällerichtung** | kein Feld; `gefaelleRichtung` existiert nur für Pult in den Vorlagen (`:86`) |
| **Abläufe** | gibt es nicht — weder Feld noch Rechnung noch Anzeige |
| **Gefälledämmung** | nur als Text im `dachstuhltyp` |

## Was er stattdessen bekommt, und es ist brauchbar

**28 verfügbare Flachdach-Vorlagen** *(22 `rect` + 6 L/T/U, `dachformVorlagen.test.ts:271`)* — *mit
Sinnbild, Zimmerer-Angaben und Lastabtragsweg.* **Darunter benannte Aufbauten wie `flach-bitumen`
und `flach-gruendach`** (`:269`).

> *Für die Auswahl und die Beschreibung ist gesorgt; für die Maße des Aufbaus nicht.*
