# W-10 · Decke und Boden — ZWECK

> ***EINORDNUNG: W-10 war eine ABLESUNG und kein Bau*** — *und das ist gemessen, nicht angenommen.*
> **Nach Yamas Verfahren für Klasse B gilt zuerst die Messung, dann die Einordnung**; hier steht sie,
> damit die nächste Rolle sie nicht wiederholt.

```text
OBERFLAECHE   GEBAUT   toolRegistry.ts:132  id 'decke'  — einer von ZWOELF
SCHEMA        GEBAUT   scene.types.ts:348 CeilingNode · :338 CeilingOeffnung
BEFEHLE       GEBAUT   applyCommand.ts:288 ADD · :305 UPDATE · :320 REMOVE
RECHNUNG      GEBAUT   applyCommand.ts:119 treppenDurchbrueche, aufgerufen :298
DARSTELLUNG   GEBAUT   szene.ts:482-483 zeichnet die Decke
              (deckenMesh.ts: 35 Z., DREI Ausfuhren, KEIN Verbraucher)
AUFRUF        GEBAUT   HausplanerApp.tsx:1027
PROBEDATEN    GEBAUT   studioFixtures.ts:63 deckeTreppe()
WAECHTER      GEBAUT   __tests__/decke.test.ts  242 Z., DREIZEHN Zusagen, alle gruen
```

> ***Sieben Schichten, alle gebaut — deshalb war hier nichts zu bauen, sondern zu lesen.***

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er hat einen Grundriss und will ein Geschoss darauf stapeln** — *und er will nicht das
Treppenloch von Hand aussparen.*

**Das Werkzeug verspricht wörtlich** (`toolRegistry.ts:141`):

```text
'Geschossdecke aus dem Grundriss aufsetzen (Treppen werden ausgespart) — Etagen-Basis.'
```

> ***Drei Zusagen in einem Satz***, *und jede ist eine andere Schicht:*
>
> | Zusage | wo sie eingelöst wird |
> |---|---|
> | **„aus dem Grundriss"** | `HausplanerApp.tsx:1031` — `polygon: ausKontur ? letzteKontur : gebaeudeUmriss()` |
> | **„Treppen werden ausgespart"** | `applyCommand.ts:119` `treppenDurchbrueche`, aufgerufen auf `:298` |
> | **„Etagen-Basis"** | **von Hand in `Kopfrahmen.tsx:172`** — *`deckenMesh.ts:32` kapselt dieselbe Rechnung und wird nicht gerufen, s. `5-CODE`* |

## Der tragende Punkt: die Aussparung ist AUTOMATISCH, aber nicht bedingungslos

**`applyCommand.ts:298`, selbst geöffnet:**

```text
const auto = (ceiling.oeffnungen && ceiling.oeffnungen.length > 0)
           ? ceiling.oeffnungen
           : treppenDurchbrueche(draft, ceiling.levelId);
```

> ***Wer Öffnungen mitgibt, schaltet die Automatik ab.*** *Das ist keine Nebenwirkung, sondern die
> Regel — und sie hat einen sichtbaren Zeugen:* **`studioFixtures.ts:59-61` sagt es im Klartext**,
> *„Fixtures umgehen den `ADD_CEILING`-Reducer, der es sonst aus der Lauflinie ableitet".*
>
> **Die Probedaten mussten das Loch selbst setzen, weil sie den Reducer nicht durchlaufen** — *und
> genau deshalb steht der Satz dort.*

## Was `boden` damit zu tun hat — der Befund, nicht die Entscheidung

**`decke`s Tooltip heißt „Decke / Bodenplatte"** (`toolRegistry.ts:147`). *Ein zweites Werkzeug
`boden` ist beschrieben und im Paket geführt — aber* **`'boden'` kommt in `toolRegistry.ts` NULL Mal
vor.** *Siehe `7-GRENZEN`; die Frage, ob W-24 ein eigenes Werkzeug braucht, hält dieses Blatt fest
und entscheidet sie nicht.*
