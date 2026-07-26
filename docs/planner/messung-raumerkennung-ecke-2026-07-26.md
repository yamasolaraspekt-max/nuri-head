# Raumerkennung nach geloester Ecke — gemessen, nicht geschaetzt

**26.07., 22:20. Planner.** Im Bedienprobe-Papier stand: *"Ob eine solche Luecke die Raumerkennung
still kippen laesst — das habe ich nicht gemessen."* Jetzt ist es gemessen.

**Verfahren:** `geometry/roomDetection.ts` im Wortlaut aus dem Arbeitsbaum genommen, ausserhalb des
Repos uebersetzt und mit vier Grundrissen gefahren. **Kein nachgezeichneter Algorithmus** — das
echte Modul. Der Arbeitsbaum des Generators wurde nicht angefasst (`node_modules` sind
macOS-Binaerdateien, deshalb lief die Messung im Cowork-Container).

## Ergebnis

| Fall | Grundriss | Raeume | Flaechen |
|---|---|---|---|
| A0 | Rechteck 8000 x 5000, geschlossen | 1 | **40,00 m²** |
| A1 | Wandlaenge 8000 -> 6000 (Luecke 2000 mm) | **0** | — |
| A2 | Wandlaenge 8000 -> **7999** (Luecke **1 mm**) | **0** | — |
| B0 | dasselbe Rechteck mit Trennwand bei x=4000 | 2 | 20,00 m² · 20,00 m² |
| B1 | untere Aussenwand 8000 -> 6000 | **1** | **20,00 m²** |

## Was daraus folgt — zwei Dinge, und das zweite ist das schlimmere

**1. Es gibt keine Toleranz. Eine Luecke von 1 mm wirkt wie eine von 2 Metern.** Das ist kein
Fehler, das ist die dokumentierte Absicht (*"mm-Integer-Welt, keine Toleranz-Magie"*), und die
Absicht ist richtig: eine Toleranz waere geraten. Aber sie macht den Fehler **binaer** — es gibt
kein "fast geschlossen", und am Bildschirm sieht 1 mm bei jedem realistischen Zoom aus wie null.

**2. Fall B1 ist der gefaehrliche.** Nicht weil viel verschwindet, sondern weil **wenig**
verschwindet: Ein Raum bleibt uebrig, mit **20,00 m²** — einer glatten, plausiblen, richtig
gerechneten Zahl. Nichts ist rot, nichts fehlt sichtbar, kein Hinweis erscheint. Der zweite Raum
ist einfach weg, und die Flaechenliste ist um 20 m² zu kurz, ohne dass irgendwo eine Luecke steht.

**Das ist genau der Satz, den ich heute Mittag noch als Frage formuliert hatte: falsche Zahlen aus
richtig aussehender Geometrie.** Er ist jetzt keine Sorge mehr, sondern ein Messwert.

## Was das Modul richtig macht — und warum das nicht reicht

Der Kommentar im Modul sagt zu, dass offene Wandzuege **keine falschen** Raeume erzeugen: sie
entarten und fallen heraus. **Das stimmt** — A1 und A2 liefern 0 Raeume, keinen falschen. Die
Zusage ist eingehalten.

Sie deckt nur nicht den Fall ab, der zaehlt. **Der Schaden entsteht nicht im Modul, sondern in der
Liste daneben:** eine Flaechenaufstellung, die einen Raum nicht enthaelt, ist selbst dann falsch,
wenn jeder einzelne enthaltene Wert stimmt. *Ein Modul kann korrekt sein und trotzdem ein falsches
Bild erzeugen, wenn niemand die Abwesenheit bemerkt.*

## Rangfolge — und was ich nicht tue

Damit steigt Befund 1 aus der Bedienprobe von einer **Bedienfrage** zu einer **Richtigkeitsfrage**.
Er gehoert an die Spitze der Befundliste.

**Trotzdem entsteht daraus nach §14 kein Posten.** Der Test lautet *"welchen offenen Posten kann
ich ohne diesen hier nicht abschliessen?"* — die Antwort bleibt: keinen. Die Entscheidung, ob eine
Richtigkeitsfrage die Reihe unterbricht, gehoert **Yama**, nicht dem Planner. Ich lege sie ihm mit
der Messung vor, statt sie selbst zu treffen.

**Was die Behebung waere — als Skizze, nicht als Auftrag:** Es braucht keine Toleranz und keine
Automatik. Es reicht, dass die Anwendung **die Abwesenheit anzeigt**: ein offener Wandzug ist
messbar (jeder Knoten mit genau einer Halbkante ist ein loses Ende), und ein loses Ende an einer
Stelle, an der vorher zwei Waende zusammenliefen, ist die Meldung wert. **Zuerst sehen, dann
verhindern** — eine Ecke, die sich nicht mehr loesen laesst, ist eine groessere Aenderung als eine
Ecke, deren Loesung auffaellt.
