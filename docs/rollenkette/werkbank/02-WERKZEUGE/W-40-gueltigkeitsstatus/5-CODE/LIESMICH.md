# W-40 · CODE

## Wo der Code wirklich lebt

> **DIESES BLATT STAND NICHT AUF MEINER EIGENEN LISTE.** *Der Befund `ea418041` nennt dreizehn
> Stellen in **vier** Blättern; es sind **fünf**.* **Ich habe nach den zwei Fachfragen gesucht und
> nicht nach der Prämisse — und dieses Blatt trägt sie am dicksten: „noch keine" Code.** *Der
> Plan-Prüfer hat meine Liste Zeile für Zeile nachgemessen und bestätigt; niemand hat gefragt, ob sie
> vollständig ist. Ich melde die Lücke selbst, weil sie sonst als geprüft gälte.*

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 1 Domäne | **`geometry/configuratorPackage.ts`** | **die Gültigkeitsachse — gebaut**: `ConfiguratorStatus` (`:26`), `STATUS_UEBERGAENGE` (`:103-111`), `statusUebergangErlaubt`, `kannIntegrieren` (`:120`), `markiereVeraltet` |
| 1 Domäne | `geometry/integrationAbgleich.ts:13`, `:134` | **benutzt `kannIntegrieren`** außerhalb der Tests |
| — | `blocked` | **die einzige Erweiterung: 0 Treffer auf der Insel** |

**BERICHTIGT (W-40/1, 12.08.).** *Hier stand:* **„1–5 · noch keine · W-40 ist eine Vorgabe; für
`SchrittStatus` existieren die drei Stufen nicht."** *Der Nachsatz stimmt bis heute — für
`SchrittStatus` existieren sie nicht.* **Falsch war der Hauptsatz: „noch keine" gilt für die Insel
nicht, und die Trefferzeile darunter hat es die ganze Zeit gesagt.**

**Am Bau-Stand gemessen — dieselbe Messung, die schon damals hier stand:**

```text
grep -rE "'(confirmed|outdated|blocked)'" resources/planner/hausplaner
  app/studioDaten.ts                    0 Treffer   <- die Fortschrittsachse kennt sie nicht
  geometry/configuratorPackage.ts       4 Treffer   <- siehe unten, das ist NICHT nichts
  geometry/integrationAbgleich.ts       1 Treffer
```

> **„Es gibt keinen Code" gilt für W-38s Achse — nicht für die Insel.** *Ich habe jede Trefferzeile
> geöffnet, weil ein Wort kein Beleg ist (H-6), und dabei einen gebauten Präzedenzfall gefunden.*
> **Er steht in `7-GRENZEN.md` und ist die wichtigste Anschlussfrage dieses Blattes.**

## Die Schnittstelle — DREI Viertel davon sind gebaut

```ts
// GEBAUT, abgelesen: geometry/configuratorPackage.ts:25-26
export type ConfiguratorStatus =
  | 'draft' | 'incomplete' | 'generated' | 'checked'
  | 'approved' | 'integrated' | 'outdated';
//                  ^checked = review-required   ^approved = confirmed   ^outdated
```

```ts
// VORGABE, und nur noch DIESE eine: blocked existiert nicht (0 Treffer).
// Ob als achter Wert der Union oder als eigenes Feld, entscheidet der Bau — nicht dieses Blatt.
```

**BERICHTIGT (W-40/1, 12.08.).** *Hier stand:* **„VORGABE — existiert nicht. `export type
Gueltigkeitsstatus = 'confirmed' | 'outdated' | 'blocked'`."** *Ein neuer Typ neben dem gebauten wäre
genau die zweite Wahrheit, vor der dieses Werkzeug an drei anderen Stellen warnt.* **Der Fehler ist
nicht, dass die Vorgabe schlecht war — er ist, dass sie eine Vorgabe war, wo eine Ablesung
hingehörte.**

**Warum diese Form:** *W-38 führt seine Achse als String-Union mit einem
`Record<SchrittStatus, string>` für die Beschriftung (`studioDaten.ts:163` und `:255`).* **Dieselbe
Form hält die zweite Achse lesbar und erzwingt bei jeder neuen Stufe ein deutsches Wort.**

> **BERICHTIGT (W-40/1).** *Hier stand:* **„Was hier NICHT steht, und mit Absicht: wo der Typ liegen
> soll, woran er hängt, und welche Übergänge gelten."** *Alle drei stehen jetzt oben — der Typ in
> `geometry/configuratorPackage.ts`, der Träger ist das `ConfiguratorPackage` (`:72`), die Übergänge
> `:103-111`.* **Der Nachsatz von damals ist der Grund, warum das Blatt heute berichtigt werden
> konnte: „der Bestand hat für zwei dieser Fragen bereits eine Antwort an anderer Stelle".** *Ich
> habe die Antwort benannt und trotzdem nicht eingesetzt.*

## Die Kernstelle — ZWEI TRÄGER, nicht zwei Felder

```ts
// ABGELESEN. Der Kern ist NICHT der Typ, sondern dass die Achsen an
// VERSCHIEDENEN GEGENSTAENDEN haengen:
Schritt   fortschritt: SchrittStatus       // W-38   app/studioDaten.ts:163
Paket     status:      ConfiguratorStatus  // W-40   geometry/configuratorPackage.ts:72
```

**BERICHTIGT (W-40/1, 12.08.) — das ist die zweite Hälfte meines Fehlers.** *Hier stand:*

```ts
interface Irgendein Traeger {          // <- ein Traeger, zwei Felder
  fortschritt: SchrittStatus;
  gueltigkeit: Gueltigkeitsstatus;
}
```

*mit dem Satz* **„Zwei Felder nebeneinander, nicht sieben Werte in einem."**

> **Der Satz war halb richtig, und die falsche Hälfte ist die teurere.** *Richtig: nicht sieben Werte
> in einem Feld.* **Falsch: „nebeneinander" an EINEM Träger.** *Ich habe die Achse an den Schritt
> gehängt, weil W-38s `SchrittStatus` dort hängt — ohne zu prüfen, woran die **vorhandene** Achse
> hängt.* **Wer nach dieser Vorlage baut, baut die Gültigkeitsachse ein zweites Mal am falschen
> Gegenstand. Genau das ist der Grund, warum W-40/1 P1 trägt.**

## Zwei Fundstellen geradegezogen (W-40/1-6)

**Beide Angaben stimmen im INHALT zeichengenau; nur der Weg dorthin hielt auf.** *Selbst
nachgemessen, jede Zeile geöffnet:*

```text
statusAus            genannt   app/fahrschritte.ts
                     richtig   app/dashboard/fahrschritte.ts:43
                               der dashboard-Teil fehlte im Pfad

STATUS_UEBERGAENGE   genannt   configuratorPackage.ts:101
                     richtig   configuratorPackage.ts:103, Block bis :111
```

> **Warum eine Fundstelle mehr wert ist als ihr Inhalt:** *ein falscher Pfad kostet den Nächsten die
> Suche, und wer nicht findet, was belegt ist, hält es für unbelegt.* **Genau so ist die Prämisse
> „kein Code" entstanden, die dieser Auftrag repariert.**

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-38** `studioDaten.ts` | die Fortschrittsachse, **neben** die W-40 tritt | **ja** — W-38 importiert nichts |
| **W-41** | die Mechanik, die `outdated` setzt und propagiert | **noch nicht gebaut** |
| **W-31** (PV-Belegung) | *umgekehrt:* W-31 braucht `confirmed` als Bedingung | — |

> **Die zweite Zeile ist die Reihenfolge, die das Register schon führt:** *`W-41 braucht W-38,
> W-40`.* **W-40 sagt, DASS es `outdated` gibt; W-41 sagt, WIE es entsteht.**
