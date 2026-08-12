# W-23 · Deckung und Material — BEDIENUNG

> **Stufe 1 = `BESCHRIEBEN`.** *Es gibt heute keine Oberfläche; die Angaben unten sind Vorgabe für
> den Bau. Wo die Quelle schweigt, steht „offen" statt einer erfundenen Taste.*

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | **offen** — der Zugang gehört zur Dachfläche; ob eigenes Werkzeug oder Reiter im Dach-Konfigurator, entscheidet der Bau |
| Tastenkürzel | **offen** — keines belegt |
| Kontextmenü | **ja, sinnvoll** — an einer ausgewählten Dachfläche, die ihre Sparrenlänge und Neigung bereits kennt |

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | wählt die Dachfläche | Sparrenlänge und Neigung sind übernommen, nicht eingetippt |
| 2 | wählt das Ziegelmodell | **den erlaubten Bereich** des Modells (`min`–`max`) und seine Regeldachneigung |
| 3 | — | die möglichen Reihenzahlen mit dem jeweiligen Lattmaß, **oder** die Absage im Klartext |
| 4 | wählt eine Reihenzahl | das gewählte Lattmaß steht fest und geht an **W-21L** |

*Schritt 2 zeigt den Bereich **bevor** gerechnet wird — damit der Anwender bei einer Absage sieht,
woran es lag, statt nur dass es nicht ging.*

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| Alles gut | „3 Reihen à 383 mm — im erlaubten Bereich 372–405." | sachlich |
| Eingabe unvollständig | „Bitte zuerst ein Ziegelmodell wählen." | hinweisend |
| **Absage 1 — Neigung zu gering** | **„Dieses Modell ist ab 25° Dachneigung zugelassen, dein Dach hat 22°. Wähle ein Modell mit geringerer Regeldachneigung, oder prüfe die Neigung."** | **erklärend** |
| **Absage 2 — Schranke unbekannt** | **„Für dieses Modell ist keine Regeldachneigung hinterlegt. Ohne sie lässt sich nicht prüfen, ob es auf dieses Dach darf — die Rechnung bleibt deshalb aus."** | **erklärend** |
| **Absage 3 — keine gleichmäßige Teilung** | **„Für 1000 mm Sparrenlänge gibt es mit diesem Ziegel keine gleichmäßige Reihenteilung: 2 Reihen ergäben 500 mm, 3 Reihen 333 mm — erlaubt sind 372 bis 405 mm. Mögliche Wege: anderes Modell, oder die Restreihe am First ausgleichen (nicht von diesem Werkzeug erfasst)."** | **erklärend** |
| **Modell ohne Lattmaß** | **„Für dieses Modell sind keine Lattmaße hinterlegt. Belegt sind heute sieben Braas-Modelle; für alle anderen kann das Werkzeug nichts sagen."** | **erklärend** |

> **Absage 3 ist die wichtigste Meldung des ganzen Werkzeugs.** *Sie nennt **beide** Nachbarwerte und
> den erlaubten Bereich — damit der Anwender sieht, dass es keine knappe Sache ist, sondern eine
> Lücke.* **Eine Meldung „nicht möglich" ohne diese drei Zahlen wäre wertlos.**

## Abbruch

- **Esc** bricht ab; danach ist der Zustand **exakt** wie vorher.
- Das Werkzeug schreibt ohnehin nichts — **halbfertig gibt es hier nicht.**

## Tastenkürzel während des Werkzeugs

| Taste | Wirkung |
|---|---|
| Esc | abbrechen |
| Eingabe | die markierte Reihenzahl übernehmen |
| ↑ ↓ | zwischen den möglichen Reihenzahlen wechseln |
| Umschalt · Alt | **offen** — es gibt keinen Winkelzwang und keinen Fang, den man aussetzen könnte |
