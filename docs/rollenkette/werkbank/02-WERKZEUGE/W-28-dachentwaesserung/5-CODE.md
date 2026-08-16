# W-28 · Dachentwässerung — CODE / LIESMICH

## Die einzige Fundstelle, vollständig

```ts
// geometry/linienBauteile.ts:20-22
export type LinienBauteilArt =
  | 'schneefang' | 'laufrost' | 'trittstufe' | 'wartungsgang'
  | 'dachrinne' | 'firstlinie' | 'modulsperrlinie';
```

**Das ist alles.** *Der Befehl, der es belegt:*

```sh
grep -rn "dachrinne" resources/planner/hausplaner --include='*.ts' --include='*.tsx'
#  -> geometry/linienBauteile.ts:22   (eine Zeile, sonst nichts)
grep -rn "fallrohr" resources/planner/hausplaner --include='*.ts' --include='*.tsx'
#  -> keine Ausgabe
```

## Wo der Nachbar liegt, der die Bemessung kann

```text
geometry/abwassergefaelle.ts        80 Zeilen · 8 Ausfuhren     (Register: FG-02)
__tests__/abwassergefaelle.test.ts  5 Zusagen
app/dashboard/enginePanels.ts:30    import { pruefeAbwasser, type AbwasserEingabe }
app/tools/faehigkeiten.ts:78        { id: 'engine-abwasser', label: 'Abwasser-Gefaelle',
                                      gruppe: 'sanitaer' }
__tests__/zweiEnginesSchweigen.test.ts   A-17 — haelt fest, dass ein Flag
                                          EINE Engine stumm schalten darf,
                                          nicht negative Urteile allgemein
```

> ***Diese fünf Zeilen sind die Bauanleitung für W-28***, *falls es je gebaut wird:* **Modul mit
> Vorbehaltskonstante, eigener Wächter, Eintrag in `faehigkeiten`, Anschluss über `enginePanels`,
> und eine Zusage, die das Schweigen begrenzt.** *Der Weg ist im Haus einmal vollständig gegangen
> worden; er muss nicht erfunden werden.*

## Das Umfeld, in dem `dachrinne` steht

`geometry/linienBauteile.ts` — **167 Zeilen.** *Die tragende Rechnung ist `platziereSchneefang`
(`:83`) mit `SCHNEEFANG_HINWEIS` (`:64`) als exportiertem Vorbehalt.* **Verbraucher von
`platziereSchneefang`: 12.** *Das Modul ist also lebendig; es ist nur kein
Entwässerungsmodul.*

**Die zwei Funktionen, die eine spätere Dachrinne mitbenutzen würde:**

```ts
sperrzoneVRel(b, hoeheM)            // :127  Linienbauteil -> Sperrzone in v
istInSperrzone(b, yRelModul, hoehe) // :139  liegt ein Modul darin
```

> *Sie fragen nach der LAGE, nicht nach der MENGE* — **wer sie für den Anfang einer Bemessung
> hält, verwechselt zwei Aufgaben** (`2-FUNKTION`).

## Für den, der hier weiterliest

- **Nicht suchen nach:** `roofDrainage`, `rinneById`, `regenspende`, `abflussbeiwert` — *alle vier
  ergeben null.*
- **Nicht verwechseln:** `abwassergefaelle` ist **Sanitär**, nicht Dach. *Gleiche Rechenart,
  anderes Gewerk, andere Norm-Anwendung.*
- **Nicht aus dem Titel schließen:** *`W-30` heißt „Flachdach-Aufbau · Gefälle · Attika · Abläufe"
  und hat für die Abläufe ebenfalls nichts* — **dort ist es bereits gemessen und festgehalten.**
