# W-40 · Gültigkeitsstatus — FUNKTION

## DREI der vier Stufen sind GEBAUT — Ablesung, nicht Vorgabe (W-40/1)

> **Yamas Entscheidung vom 12.08.:** *drei der vier Stufen existieren bereits unter anderem Namen.*
> **`blocked` ist die EINZIGE Erweiterung.**

| Zielbild | im Code | Fundstelle am Bau-Stand | Beleg der Wirkung |
|---|---|---|---|
| `review-required` | **`checked`** | `configuratorPackage.ts:26` · Übergang `:107` | **der einzige Weg nach `approved`** — `checked: ['draft','approved','generated']`, und `approved` steht in keiner anderen Liste |
| `confirmed` | **`approved`** | `:26` | **`kannIntegrieren` (`:120`)** — nur ein `approved`-Paket darf übernommen werden |
| `outdated` | **`outdated`** | `:26` | **`markiereVeraltet` (`:125-128`)** — setzt **nur** `approved` und `integrated` darauf, ein Entwurf bleibt unberührt |
| `blocked` | **fehlt** | — | **`grep -rc "'blocked'"` über die ganze Insel: 0 Treffer** |

**Damit ist W-40 eine ABLESUNG MIT EINER ERWEITERUNG und keine Vorgabe.** *Alles außer `blocked` ist
beschrieben, nicht vorgegeben.*

> **Die Übergangstabelle existiert ebenfalls** (`:103-111`) *— mit dem ausdrücklichen Grundsatz
> „bewusst streng: aus `approved`/`integrated` geht es nur über `outdated` zurück in die
> Bearbeitung (Freigabe-Schutz — keine stille Rückstufung)".* **Wer für W-40 eine zweite Tabelle
> schriebe, erzeugte die zweite Wahrheit, die dieses Blatt verhindern soll.**

## Die drei Stufen des Zielbilds — je mit ihrer Herkunft aus der Quelle

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
> nicht, und mehr steht deshalb auch hier nicht.*
>
> **ÜBERHOLT (W-40/1, 12.08.), und nicht gelöscht.** *Hier stand:* **„Was `blocked` von
> `DECISION_BLOCKED` im Prozess unterscheidet, ist NICHT belegt."** *Yama hat es am 12.08.
> entschieden — die vier Merkmale und seine zwei Auflagen stehen in `7-GRENZEN.md`.* **Die Kurzform:
> `DECISION_BLOCKED` wartet auf einen MENSCHEN, `blocked` auf eine BEDINGUNG.**

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

RICHTIG  zwei Achsen an ZWEI TRAEGERN:
           Schritt  · fortschritt: SchrittStatus       (W-38, app/studioDaten.ts:163)
           Paket    · status: ConfiguratorStatus       (geometry/configuratorPackage.ts:25)
         -> der Fortschritt haengt am Schritt, die Gueltigkeit am PAKET.
```

> **BERICHTIGT (W-40/1, 12.08.).** *Hier stand „zwei Felder nebeneinander: `fortschritt` und
> `gueltigkeit`" — also **beide an einem Träger**.* **Das war die Hälfte meines Fehlers:** *richtig
> ist die Trennung der Achsen, falsch war, sie an denselben Gegenstand zu hängen.*
>
> **Die Aussage „ein Schritt kann `ok` sein und trotzdem nicht bestätigt" bleibt gültig** — *sie
> beschreibt zwei Achsen, und das ist Yamas Zuordnung.* **Nur die Bauform darunter war falsch.**

**`Record<SchrittStatus, string>` in `:255` ist der Beleg dafür, dass die Trennung nötig ist:**
*jede Stufe von `SchrittStatus` braucht ein deutsches Wort. Käme `confirmed` hinzu, müsste es dort
ein Fortschrittswort bekommen — und es ist keines.*

## Der TRÄGER — berichtigt nach Yamas Entscheidung (W-40/1, 12.08.)

> **ÜBERHOLT, und nicht gelöscht.** *Hier stand:*
>
> > *„Die Quelle sagt nicht, WORAN der Gültigkeitsstatus hängt — am Schritt, am Geschoss, am
> > Bauteil, am Dokument? Sie nennt die Stufen und ihre Bedeutung, nicht ihren Träger. Das Blatt
> > gibt hier nichts vor … die Frage steht in `7-GRENZEN.md`."*
>
> **Die Frage war offen, weil ich sie an der Quelle gemessen habe statt am Code.** *Der Hinweis auf
> `configuratorPackage.ts` stand schon damals darunter — ich habe ihn als Nebensatz geführt und
> nicht als Antwort.* **Das ist die zweite Hälfte meines eigenen H-6-Fehlers: die Prämisse „kein
> Code" habe ich übernommen, und den Träger habe ich an den Schritt gehängt, weil W-38s
> `SchrittStatus` dort hängt.**

**Was gilt — Yamas Zuordnung, 12.08.:**

```text
Die Gueltigkeitsachse haengt am PAKET (ConfiguratorPackage), nicht am Schritt.
```

```ts
geometry/configuratorPackage.ts:25-26
export type ConfiguratorStatus =
  | 'draft' | 'incomplete' | 'generated' | 'checked' | 'approved' | 'integrated' | 'outdated';
```

**Damit ist auch die Eingabe beantwortet:** *Träger ist das `ConfiguratorPackage`; der Status ist
dessen Feld, nicht ein zweites Feld neben `SchrittStatus`.*

> **Der Unterschied ist nicht akademisch.** *Ein Status am Schritt hätte bedeutet: jeder Schritt
> trägt zwei Felder. Ein Status am Paket bedeutet: **die beiden Achsen liegen an verschiedenen
> Gegenständen** — der Fortschritt am Schritt, die Gültigkeit am Paket.* **Wer sie an denselben
> Träger hängt, erzeugt genau die zweite Wahrheit, die dieses Blatt verhindern soll — und ich war
> auf dem Weg dorthin.**

## Die Übergänge — es gibt sie, und sie gelten (W-40/1)

**Die QUELLE gibt sie nicht her, der CODE schon:** *`geometry/configuratorPackage.ts:103-111`,
`STATUS_UEBERGAENGE`, mit `statusUebergangErlaubt` als Wächter und
`configuratorPackage.test.ts:31`/`:41` als Prüfung in **beide** Richtungen.*

> **BERICHTIGT (W-40/1, 12.08.).** *Hier stand:* **„Die Quelle gibt sie NICHT her … Und eine
> erfundene wäre schlimmer als ein benanntes Fehlen — das sagt W-40-5 wörtlich. Deshalb steht hier
> keine."** *Der Grundsatz bleibt richtig und wird hier weiter befolgt — es steht auch jetzt keine
> erfundene Tabelle hier, sondern ein **Verweis** auf die gebaute.* **Falsch war nur, das Fehlen bei
> der Quelle für ein Fehlen im Bestand zu halten.**

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
| 1 Domäne | **ja, gemessen** | der Typ liegt in `geometry/configuratorPackage.ts`, der Träger ist das `ConfiguratorPackage` (`:72`) |
| 2 Geometrie | **nein** | keine Rechnung |
| 3 Anwendung | **ja** | `geometry/integrationAbgleich.ts:13`, `:134` benutzt `kannIntegrieren` außerhalb der Tests |
| 4/5 Oberfläche | **noch nicht** | der Planer muss `outdated` sehen können, sonst wirkt die Invalidierung nicht — *dafür gibt es heute keine Ansicht* |

**BERICHTIGT (W-40/1, 12.08.).** *Hier stand `1 Domäne — **vermutlich** — die Quelle sagt es nicht`
und `3 Anwendung — **offen** — hängt daran, woran der Status hängt`, darunter der Satz* **„Die
Unsicherheit ist benannt und nicht überspielt — die Quelle liefert die Stufen, nicht die
Verortung."** *Die Unsicherheit war echt und ist jetzt aufgelöst: nicht durch eine Entscheidung,
sondern durch Nachschlagen im Bestand.*

## Scope

```text
W-40 IST      die ABLESUNG der gebauten Gueltigkeitsachse — welche Stufen es gibt,
              was jede bedeutet, woran sie haengen und wie sie sich zur
              Fortschrittsachse aus W-38 verhalten —
              PLUS die Vorgabe der EINEN fehlenden Stufe: blocked.

W-40 IST NICHT
              der BAU von blocked — kein Produktivcode in diesem Auftrag
              die Invalidierungs-MECHANIK — das ist W-41
              die PV-Belegung — das ist W-31; W-40 liefert die Bedingung
              eine zweite Uebergangstabelle neben der gebauten
              eine Aenderung an studioDaten.ts — die Fortschrittsachse bleibt unberuehrt
```

**BERICHTIGT (W-40/1):** *hier stand* **„W-40 IST die VORGABE der Gültigkeitsachse"**. *Vorgabe ist
nur noch `blocked`; alles andere ist abgelesen.*
