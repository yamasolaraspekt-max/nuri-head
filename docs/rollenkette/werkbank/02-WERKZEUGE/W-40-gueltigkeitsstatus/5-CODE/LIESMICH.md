# W-40 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 1–5 | **noch keine** | *W-40 ist eine Vorgabe; für `SchrittStatus` existieren die drei Stufen nicht* |

**Am Bau-Stand gemessen:**

```text
grep -rE "'(confirmed|outdated|blocked)'" resources/planner/hausplaner
  app/studioDaten.ts                    0 Treffer   <- die Fortschrittsachse kennt sie nicht
  geometry/configuratorPackage.ts       4 Treffer   <- siehe unten, das ist NICHT nichts
  geometry/integrationAbgleich.ts       1 Treffer
```

> **„Es gibt keinen Code" gilt für W-38s Achse — nicht für die Insel.** *Ich habe jede Trefferzeile
> geöffnet, weil ein Wort kein Beleg ist (H-6), und dabei einen gebauten Präzedenzfall gefunden.*
> **Er steht in `7-GRENZEN.md` und ist die wichtigste Anschlussfrage dieses Blattes.**

## Die Schnittstelle, die gebaut werden soll

```ts
// VORGABE — existiert nicht. Die Namen folgen der Quelle, die Form folgt W-38.
export type Gueltigkeitsstatus = 'confirmed' | 'outdated' | 'blocked';
```

**Warum diese Form:** *W-38 führt seine Achse als String-Union mit einem
`Record<SchrittStatus, string>` für die Beschriftung (`studioDaten.ts:163` und `:255`).* **Dieselbe
Form hält die zweite Achse lesbar und erzwingt bei jeder neuen Stufe ein deutsches Wort.**

> **Was hier NICHT steht, und mit Absicht:** *wo der Typ liegen soll, woran er hängt, und welche
> Übergänge gelten.* **Die Quelle gibt das nicht her, und der Bestand hat für zwei dieser Fragen
> bereits eine Antwort an anderer Stelle** — *siehe `7-GRENZEN.md`.*

## Die Kernstelle, die es zu bauen gilt

```ts
// VORGABE. Der Kern ist NICHT der Typ, sondern die TRENNUNG:
interface Irgendein Traeger {
  fortschritt: SchrittStatus;      // W-38, gebaut
  gueltigkeit: Gueltigkeitsstatus; // W-40, Vorgabe
}
```

**Zwei Felder nebeneinander, nicht sieben Werte in einem.** *Das ist die ganze Aussage von W-40; wer
sie in ein Feld faltet, hat die zwei Achsen zu einer Liste gemacht.*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-38** `studioDaten.ts` | die Fortschrittsachse, **neben** die W-40 tritt | **ja** — W-38 importiert nichts |
| **W-41** | die Mechanik, die `outdated` setzt und propagiert | **noch nicht gebaut** |
| **W-31** (PV-Belegung) | *umgekehrt:* W-31 braucht `confirmed` als Bedingung | — |

> **Die zweite Zeile ist die Reihenfolge, die das Register schon führt:** *`W-41 braucht W-38,
> W-40`.* **W-40 sagt, DASS es `outdated` gibt; W-41 sagt, WIE es entsteht.**
