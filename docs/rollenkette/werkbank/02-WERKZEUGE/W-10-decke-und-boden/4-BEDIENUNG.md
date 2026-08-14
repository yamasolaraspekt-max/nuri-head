# W-10 · Decke und Boden — BEDIENUNG

## Wo der Anwender das Werkzeug findet

```text
toolRegistry.ts:132   id 'decke'
              :133    label 'Decke'
              :134    icon 'decke'
              :136    groupId 'gebaeude'
              :137    supportedWorkspaces [WORKSPACE_ARCHITEKTUR]
              :138    supportedViews ['2d', 'split']
              :139    shortcut 'K'
              :140    bauteilKind 'ceiling'
              :141    helpText  (die Zusage, s. u.)
              :147    tooltip.title 'Decke / Bodenplatte'
```

> ***`supportedViews` nennt `'2d'` und `'split'` — nicht `'3d'`.*** *Die Decke wird im Grundriss
> gesetzt und im Raum gesehen, nicht umgekehrt.*

## Die Zusage, die der Anwender liest — wörtlich

```text
:141  'Geschossdecke aus dem Grundriss aufsetzen (Treppen werden ausgespart) — Etagen-Basis.'
:147  tooltip.title  'Decke / Bodenplatte'
      tooltip.body   'Massive oder mehrschichtige Decke erzeugen.'
      tooltip.usage  'Einsatzbereich: Architektur, Statik, Heizlast.'
```

> **Die Zusage steht in `helpText` (`:141`) und NICHT im `tooltip` (`:147`).** *Zwei Felder, zwei
> Texte — wer nur den Tooltip liest, erfährt von der Aussparung nichts.*

## Was der Anwender tun muss — und was von selbst geschieht

| Schritt | wer tut es |
|---|---|
| Umriss festlegen | **wahlweise**: Kontur zeichnen **oder** nichts tun |
| Treppenloch aussparen | **das Werkzeug**, ohne Zutun |
| Dicke wählen | **das Werkzeug**: `level.floorThickness` (`HausplanerApp.tsx:1031`) |
| nächste Etage höhenrichtig stapeln | **das Werkzeug**: `naechsteEtageElevationMm` |

**Der Umriss-Fall ist ausdrücklich gewollt** — *der Kommentar auf `HausplanerApp.tsx:1018-1021` sagt
warum:*

```text
„Kein Zwang zum Konturzeichnen. Wer schnell ein Geschoss stapeln will, soll nicht erst
 sechs Punkte klicken müssen — für den rechteckigen Fall ist der Umriss richtig. Aber
 niemand darf glauben, er habe eine exakte Decke, wenn er eine Näherung hat, deshalb
 der Hinweis unten."
```

> ***Die Bequemlichkeit ist gebaut UND ihr Preis ist benannt.*** *Wer ohne Kontur arbeitet, bekommt
> die Bounding-Box und einen Hinweis dazu* — **die Herkunft wandert als `herkunftFuerNeueDecke(ausKontur)`
> in den Knoten** (`:1035`), *und ein Wächter hält fest, dass die Fußleiste ohne Kontur eine Näherung
> meldet und mit Kontur schweigt* (`decke.test.ts:169`).

## Was der Anwender NICHT kann

**Eine zweite Decke im selben Geschoss.** *`pruefeDeckeProLevel` lehnt ab* (`applyCommand.ts:296`),
*und der Wächter hält es fest* (`decke.test.ts:50`, *„zweite je Level wird abgelehnt (max. 1)"*).

**Eine Decke mit krummer Dicke.** *`pruefeDeckeGanzzahlig`* (`:300`), *Wächter `decke.test.ts:57`.*

**Einen `boden` setzen.** *Es gibt kein Werkzeug dafür* — *siehe `7-GRENZEN`.*

## Wer eigene Öffnungen mitgibt, schaltet die Automatik ab

**`applyCommand.ts:298`.** *Das betrifft heute keinen Anwenderweg, sondern Aufrufer* — **die
Probedaten tun es** (`studioFixtures.ts:59-61`) *und schreiben den Grund dazu.*

> ***Für die Bedienung heißt das: es gibt keinen Schalter „Aussparung aus".*** *Der Zustand entsteht
> ausschließlich dadurch, dass ein Aufrufer `oeffnungen` füllt.*
