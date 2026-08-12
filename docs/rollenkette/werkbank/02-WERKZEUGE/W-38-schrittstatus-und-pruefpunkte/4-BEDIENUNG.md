# W-38 · Schritt-Status und Prüfpunkte — BEDIENUNG

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | **keiner** |
| Tastenkürzel | **keines** |
| Kontextmenü | **nein** |

> **W-38 wird nicht aufgerufen, es wird gelesen.** *Der Anwender bedient es nie unmittelbar — er
> sieht seine vier Wörter an jedem Schritt der geführten Planung.* **Das macht es nicht
> bedienungsfrei: die vier Wörter SIND die Bedienoberfläche dieses Werkzeugs.**

## Die vier Beschriftungen — vollständig

```ts
resources/planner/hausplaner/app/studioDaten.ts:255-257
export const STATUS_LABEL: Record<SchrittStatus, string> = {
  ok: 'Vollständig', prog: 'In Bearbeitung', warn: 'Prüfung erforderlich', open: 'Offen',
};
```

| Stufe | Der Anwender liest | Was es heißt |
|---|---|---|
| `ok` | **„Vollständig"** | *alle Prüfpunkte dieses Schrittes sind erfüllt* |
| `prog` | **„In Bearbeitung"** | *begonnen, noch nicht durch* |
| `warn` | **„Prüfung erforderlich"** | *etwas ist auffällig und will angesehen werden* |
| `open` | **„Offen"** | *noch nicht begonnen* |

> **Alle vier Zuordnungen stehen hier, nicht drei und ein „usw."** — *`Record<SchrittStatus,
> string>` erzwingt die Vollständigkeit im Typ, und
> `__tests__/gefuehrteEhrlich.test.ts:38, :43, :44, :45` prüft jedes Wort einzeln und
> zeichengenau.*

**Warum „Vollständig" und nicht das frühere Freigabewort — `studioDaten.ts:245-254`:**

> *„`ok` trug hier ein Wort aus der Freigabe-Sprache. Es behauptet einen Vorgang, den es nicht
> gegeben hat — **niemand hat etwas geprüft und bestätigt**. Der Wert wird aus dem Dokument
> abgeleitet und heißt ‚alle Prüfpunkte dieses Schrittes sind erfüllt'."*

**Die Schlüssel blieben dabei unverändert.** *Geändert wurde das Wort, nicht der Wert — deshalb hat
kein Aufrufer etwas gemerkt und der Anwender genau das Richtige gelesen.*

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | *nichts — er arbeitet am Dokument* | die Plakette am Schritt trägt eines der vier Wörter |
| 2 | *er klappt einen Schritt auf* | die einzelnen `Pruefpunkt`e, jeder mit eigener Stufe |
| 3 | *er sieht eine `Aufgabe` mit `warn`* | sie ist als dringlich hervorgehoben |

**Gezeichnet wird das in `app/GuidedView.tsx` (W-34)** — *`:18` `badgeFarbe` für die Plakette, `:22`
`checkFarbe` für die Prüfpunkte, `:71` setzt den Text aus `STATUS_LABEL`.* **W-38 liefert die
Wörter, W-34 die Farben.**

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| Alles gut | **„Vollständig"** | sachlich |
| Eingabe unvollständig | **„In Bearbeitung"** / **„Offen"** | hinweisend |
| **Nicht möglich** | **entfällt** | — |

> **W-38 hat keine Absage, weil es nichts versucht.** *Eine Konstantentabelle kann nicht scheitern.*
> **Die eine Falschauskunft, die hier möglich war, ist bereits behoben und bewacht:** *ein
> Statuswort, das einen Vorgang behauptet, den es nicht gab.* **Deshalb steht in `7-GRENZEN.md`
> keine Absagekette, sondern eine Wortprüfung.**

## Abbruch

**Gegenstandslos** — es gibt keinen Vorgang, der abgebrochen werden könnte. *Esc wirkt auf das
Werkzeug, das gerade läuft, nicht auf eine Konstante.*

## Tastenkürzel während des Werkzeugs

| Taste | Wirkung |
|---|---|
| — | **keine** |
