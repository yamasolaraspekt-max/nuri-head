# W-40 · Gültigkeitsstatus — FUNKTION

## Die drei Stufen — je mit ihrer Herkunft aus der Quelle

> **Kein Satz geht über die Quelle hinaus.** *Was dort nicht steht, steht in `7-GRENZEN.md` als
> offene Frage — nicht hier als stille Annahme.*

| Stufe | Was sie leistet | Herkunft |
|---|---|---|
| **`confirmed`** | *trennt „gerechnet" von „vom Nutzer **bestätigt**"* — **ohne sie ist L-9 nicht prüfbar: PV erst nach bestätigter Dachgeometrie** | `BERICHT-PROZESSEBENE-DREI-FRAGEN.md:127-129` |
| **`outdated`** | **die INVALIDIERUNG** — *der Kern von „Änderungen propagieren, niemals stille Löschung"* | `:129-130` |
| **`blocked`** | **die SPERRE** | `:130` |

**`confirmed` ist die fachlich schwerste der drei** — *sie entscheidet, ob eine PV-Belegung
überhaupt starten darf.* **`outdated` ist der Anschluss an W-41**, den Abhängigkeitsgraphen.

> **Zu `blocked` sagt die Quelle genau vier Wörter: „`blocked` ist die Sperre."** *Mehr steht dort
> nicht, und mehr steht deshalb auch hier nicht.* **Was `blocked` von `DECISION_BLOCKED` im Prozess
> unterscheidet, ist NICHT belegt** — *das ist eine Lücke der Vorgabe, keine des Blattes, und sie
> steht in `7-GRENZEN.md`.*

## Der Bezug zu W-38 — daneben, nicht hinein

**Am Bau-Stand gemessen:**

```ts
resources/planner/hausplaner/app/studioDaten.ts:163
export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';

resources/planner/hausplaner/app/studioDaten.ts:255
export const STATUS_LABEL: Record<SchrittStatus, string>
```

**`SchrittStatus` trägt VIER Stufen, und W-40 tritt NEBEN sie und nicht in sie hinein.**

```text
FALSCH   type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open'
                            | 'confirmed' | 'outdated' | 'blocked'
         -> eine Liste aus sieben Stufen, die zwei verschiedene Dinge mischt.
            Ein Schritt waere dann entweder gerechnet ODER bestaetigt, nie beides.

RICHTIG  zwei Felder nebeneinander:
           fortschritt: SchrittStatus        (W-38, gebaut)
           gueltigkeit: Gueltigkeitsstatus   (W-40, Vorgabe)
         -> ein Schritt kann ok UND confirmed sein, oder ok UND outdated.
```

> **Der zweite Fall ist der, den es ohne W-40 nicht geben kann:** *fertig gerechnet, aber durch eine
> spätere Änderung ungültig geworden.* **Genau das ist „Änderungen propagieren, niemals stille
> Löschung".**

**`Record<SchrittStatus, string>` in `:255` ist der Beleg dafür, dass die Trennung nötig ist:**
*jede Stufe von `SchrittStatus` braucht ein deutsches Wort. Käme `confirmed` hinzu, müsste es dort
ein Fortschrittswort bekommen — und es ist keines.*

## Eingabe und Ausgabe

| Was | Typ | Pflicht |
|---|---|---|
| **noch nicht festgelegt** | — | — |

> **Die Quelle sagt nicht, WORAN der Gültigkeitsstatus hängt** — *am Schritt, am Geschoss, am
> Bauteil, am Dokument?* **Sie nennt die Stufen und ihre Bedeutung, nicht ihren Träger.** *Das
> Blatt gibt hier nichts vor, weil es sonst über die Quelle hinausginge; die Frage steht in
> `7-GRENZEN.md`.*
>
> *Ein Hinweis, der aus dem Bestand kommt und keine Erfindung ist: die bereits gebaute
> Gültigkeitsachse in `geometry/configuratorPackage.ts` hängt am **Paket** — nicht am Schritt.*

## Die Übergänge

**Die Quelle gibt sie NICHT her.** *Sie nennt drei Stufen und ihre Bedeutung; eine Übergangstabelle
steht dort nicht.*

> **Und eine erfundene wäre schlimmer als ein benanntes Fehlen** — *das sagt W-40-5 wörtlich.*
> **Deshalb steht hier keine.**

**Was die Quelle logisch erzwingt, und nur das:**

```text
outdated ist die INVALIDIERUNG        -> es muss aus einem gueltigen Zustand erreichbar sein,
                                         sonst gibt es nichts zu invalidieren.
confirmed traegt L-9 als BEDINGUNG    -> es muss PRUEFBAR sein, bevor eine PV-Belegung startet.
```

**Mehr folgt aus der Quelle nicht.** *Insbesondere folgt **nicht**, ob aus `outdated` heraus wieder
`confirmed` erreichbar ist, ob `blocked` von überall erreichbar ist, und ob ein Übergang eine
Begründung braucht.*

> **Im Bestand gibt es dafür einen Präzedenzfall mit einer ausdrücklichen Entwurfsregel** —
> `geometry/configuratorPackage.ts`. **Er gehört gelesen, bevor irgendjemand hier eine Tabelle
> schreibt**, und steht in `7-GRENZEN.md`.

## Schichtzuordnung

| Schicht | W-40 | Begründung |
|---|---|---|
| 1 Domäne | **vermutlich** | ein Gültigkeitsstatus gehört zum Dokument, nicht zur Ansicht — *die Quelle sagt es nicht* |
| 2 Geometrie | **nein** | keine Rechnung |
| 3 Anwendung | **offen** | hängt daran, woran der Status hängt |
| 4/5 Oberfläche | **ja, mittelbar** | der Planer muss `outdated` sehen können, sonst wirkt die Invalidierung nicht |

**Die Unsicherheit ist benannt und nicht überspielt** — *die Quelle liefert die Stufen, nicht die
Verortung.*

## Scope

```text
W-40 IST      die VORGABE der Gueltigkeitsachse: welche Stufen es gibt, was jede
              bedeutet, und wie sie sich zur Fortschrittsachse aus W-38 verhaelt.

W-40 IST NICHT
              der BAU — kein Produktivcode, keine Aenderung an studioDaten.ts
              die Invalidierungs-MECHANIK — das ist W-41
              die PV-Belegung — das ist W-31; W-40 liefert die Bedingung
              eine zweite Uebergangstabelle neben der gebauten
```
