# W-40 · Gültigkeitsstatus — GRENZEN

> **Dieses Blatt ist Pflicht** — *und bei einer Vorgabe ist es das wichtigste: hier steht, was die
> Quelle NICHT hergibt.*

## Der Befund, der die Prämisse berührt: die Achse existiert schon — woanders

**Das Auftragsblatt sagt „Es gibt KEINEN Code: die drei Stufen fehlen im Bestand."** *Für
`SchrittStatus` stimmt das. **Für die Insel nicht.*** Gemessen und **jede Stelle geöffnet**, weil
ein Wort kein Beleg ist:

```text
resources/planner/hausplaner/geometry/configuratorPackage.ts

  export type ConfiguratorStatus =
    | 'draft' | 'incomplete' | 'generated' | 'checked'
    | 'approved' | 'integrated' | 'outdated';          SIEBEN Stufen

  export const STATUS_UEBERGAENGE                       vollstaendige Uebergangstabelle
    Grundsatz woertlich im Dateikopf:
    „Bewusst streng: aus approved/integrated geht es nur ueber outdated zurueck
     in die Bearbeitung (Freigabe-Schutz — keine stille Rueckstufung)."

  statusUebergangErlaubt(von, zu)   der Waechter
  kannIntegrieren(paket)            nur approved darf uebernommen werden
  markiereVeraltet(paket, …)        die Invalidierung
```

**Gebaut, getestet und in Gebrauch:** *`configuratorPackage.test.ts:32-45` prüft die Übergänge in
beide Richtungen, `:48-51` das Tor, `:54-61` die Invalidierung — und
`geometry/integrationAbgleich.ts:13` und `:134` benutzt `kannIntegrieren` außerhalb der Tests.*

> **Drei Folgen, und keine davon entscheide ich:**
>
> 1. **`outdated` existiert bereits samt Übergängen.** *Eine zweite Tabelle daneben wäre genau die
>    zweite Wahrheit, die W-40 laut seinem eigenen tragenden Punkt verhindern soll.*
> 2. **`approved` spielt fachlich die Rolle, die die Quelle `confirmed` zuschreibt** — *nur ein
>    `approved`-Paket darf übernommen werden. Das ist „PV erst nach bestätigter Geometrie", eine
>    Ebene tiefer.*
> 3. **`markiereVeraltet` IST die Invalidierung** — *damit berührt der Befund auch W-41, dessen
>    Blatt ebenfalls „kein Code" sagt.*
>
> **Die Frage an Planner und Plan-Prüfer:** *trägt das Ziel `ENTWORFEN` noch, wenn eine
> Gültigkeitsachse mit Übergängen bereits gebaut ist — oder ist W-40 in Wahrheit eine **Ablesung mit
> Erweiterung**?* **Dieselbe Klasse wie die Abweichung, die der Planner bei W-42 selbst benannt
> hat.** *Gemeldet in `docs/STATUS.md`, W-40-Block.*

## Die Zahlenlücke: acht gegen sieben

```text
Zielbild 3.6      ACHT Stufen        Quelle :117
gebaut            VIER               open · prog · warn · ok
als fehlend       DREI               confirmed · outdated · blocked
                  4 + 3 = 7,  nicht 8.

Die achte ist review-required — Quelle :121 fuehrt sie mit einem
GEDANKENSTRICH, nicht mit „fehlt", und die Einordnung zaehlt sie nicht mit.
```

> **Die Frage wird GESTELLT und NICHT beantwortet — sie gehört Yama.** *Entweder ist
> `review-required` bewusst nicht Teil der Gültigkeitsachse, oder die Zahl DREI ist zu niedrig.*
> **Beides ist möglich, und ich erfinde keine Erklärung.**
>
> **Warum das Blatt die Frage überhaupt tragen muss:** *wer „drei Stufen" baut, verliert die achte
> stillschweigend.* **4 + 3 = 7 ist der Hinweis, dass eine Angabe fehlt — genau die Sorte
> Zahlenlücke, die heute mehrfach durch die Rollen gelaufen ist.**

## `blocked` — vier Wörter Quelle, keine Abgrenzung

**Die Quelle sagt: „`blocked` ist die Sperre."** *Mehr nicht.*

```text
NICHT belegt:  was blocked von DECISION_BLOCKED im PROZESS unterscheidet.
               §3 fuehrt DECISION_BLOCKED als „eine ausdruecklich Yama vorbehaltene
               Entscheidung fehlt". Ist blocked dasselbe eine Ebene tiefer, oder
               etwas anderes?
               Wer sperrt, wer entsperrt, und woran haengt die Sperre?
```

> **Das ist eine Lücke der Vorgabe, keine des Blattes.** *Sie muss beim Bau von Yama kommen, nicht
> vom Bauenden.*

## Was die Quelle sonst nicht hergibt

```text
WORAN der Status haengt   Schritt? Geschoss? Bauteil? Dokument?
                          Die Quelle nennt die Stufen, nicht ihren Traeger.
                          Der Praezedenzfall oben haengt am PAKET.

UEBERGAENGE               keine Tabelle. Was die Quelle erzwingt, steht in 2-FUNKTION;
                          alles Weitere waere erfunden.

RUECKNAHME                ob eine Bestaetigung zurueckgenommen werden kann, steht nicht da.

GRUND bei outdated        „niemals stille Loeschung" sagt, dass nichts verschwindet —
                          nicht, dass der Anwender den GRUND erfaehrt. Ohne Grund waere
                          outdated eine Absage ohne Erklaerung, und das ist der teuerste
                          Fehler dieses Projekts gewesen.
```

## Was dieses Blatt ausdrücklich NICHT entscheidet

| Frage | Gehört |
|---|---|
| Trägt `ENTWORFEN` noch, oder ist es eine Ablesung? | **Planner / Plan-Prüfer** |
| Gehört `review-required` zur Gültigkeitsachse? | **Yama** |
| Wie grenzt sich `blocked` von `DECISION_BLOCKED` ab? | **Yama** |
| Gilt die gebaute Übergangstabelle auch hier? | **Planner**, nach Prüfung des Präzedenzfalls |

> **Vier offene Fragen, alle benannt statt still angenommen.** *Ein Blatt, das sie beantwortet
> hätte, wäre schneller fertig gewesen und hätte vier Entscheidungen vorweggenommen, die ihm nicht
> gehören.*

## Was später kommen könnte

```text
- der BAU der Achse                    -> eigener Auftrag, Vorgaben in 6-PRUEFUNG als B-1..B-5
- die Invalidierungs-MECHANIK          -> W-41
- die Bedingung fuer die PV-Belegung   -> W-31 liest confirmed, W-40 liefert es nur
```
