# W-13 · Auswahl und Griffe — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will genau das anfassen, was er meint** — auch wenn dort drei Dinge übereinanderliegen, und auch
wenn es mehrere sind.

## Der gemessene Mangel, aus dem es entstand

> *„Das Datenmodell kann seit jeher **mehrere** Objekte halten (`selectedNodeIds: string[]`), die
> Oberfläche konnte genau **eines** — an fünf Stellen."* (`resources/planner/hausplaner/app/tools/auswahlModus.ts:3-5`)

**Die Fähigkeit war da, die Bedienung fehlte.** *Das ist der Grund, warum es hier nicht um eine neue
Funktion geht, sondern um eine eingelöste.*

## Und die zweite Frage, ohne die ein Canvas nicht bedienbar ist

> *„Welches Objekt hat der Nutzer getroffen, wenn mehrere übereinanderliegen? **Ohne diese Antwort
> gewinnt in einem Canvas immer das zuletzt gezeichnete oder das zufällig …**"*
> (`resources/planner/hausplaner/app/tools/trefferSuche.ts:4`)

## Was W-13 von den anderen Klasse-A-Werkzeugen unterscheidet

**Es hat als einziges ein echtes Werkzeug** — `toolRegistry.ts:39` trägt `id: 'auswahl'`. Bei W-01,
W-05, W-21 und W-22 steht die Rechenschicht ohne Werkzeugschicht. *Hier ist beides da.*
