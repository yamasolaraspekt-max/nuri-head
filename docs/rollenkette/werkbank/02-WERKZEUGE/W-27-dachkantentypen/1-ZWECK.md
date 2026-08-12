# W-27 · Dachkantentypen — ZWECK

> **Reifegrad ENTWORFEN, nicht BESCHRIEBEN.** *Die Blätter **geben vor**, was gebaut werden soll —
> Quelle ist der Prototyp `DachplanerProPage.tsx`, nicht der Bestand. Beim Übergang
> `ENTWORFEN → GEBAUT` ist zu prüfen, ob die Vorgabe mit der Ablesung übereinstimmt.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will, dass sein L-förmiger Grundriss ein Dach bekommt** — und nicht abgelehnt wird.

## Wann greift der Anwender danach?

*In dem Moment, in dem die Kontur keine vier Ecken mehr hat:* ein Anbau, ein L, ein T. **Dort
entstehen Grate, Kehlen und Ortgänge — und das Werkzeug muss sagen können, welche wo.**

## Woran merkt er, dass es fehlt?

**Er bekommt eine Absage statt eines Dachs.**

```text
W-07 heute   pruefeRechteckigeKontur wirft bei allem, was nicht rechteckig ist
             (1 % Toleranz gegen die Bounding-Box) — L, T und U werden ABGELEHNT
W-27 waere   die Ecke wird KLASSIFIZIERT statt abgelehnt:
             innen -> Kehle · aussen -> Grat · Traufe-an-Giebel -> Ortgang
```

> **Das ist W-07s größte Grenze, und sie ist keine Fachgrenze, sondern eine Wissenslücke.** *Yama hat
> denselben Zusammenhang am 12.08. bei A-01 aufgehoben: „das Nicht-Ziel »keine L/T/U-Dächer« stammt
> aus **Unwissen über die Fähigkeit**."* **Die Fähigkeit liegt im Prototyp — dieses Blatt beschreibt
> sie, damit sie gebaut werden kann.**

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

| Nicht dieses Werkzeug | Sondern |
|---|---|
| Die Dachfläche berechnen | **W-08** (F-011) |
| Die Kontur prüfen und ablehnen | **W-07** — dessen V1-Grenze bleibt, bis W-27 gebaut ist |
| Sparren an Grat/Kehle zuschneiden | **W-21** — `klassifiziereSchifter` gibt es bereits, siehe unten |
| Die Ortganglänge rechnen | **existiert schon**: `ortgangFlaechenlaengeM`, siehe unten |

## Was die Insel HAT — und warum das hier steht

> **Die Auflage dieses Auftrags lautet: keine Lücke ohne die Zeile daneben, die zeigt, was da ist.**
> *Der Grund ist handfest — wer „gibt es nicht" schreibt, veranlasst den Nächsten, etwas neu zu
> bauen, das es gibt.*

| Sache | Wo sie steht | Was sie ist |
|---|---|---|
| **Ortganglänge** | `geometry/dachformVorlagen.ts:291` | exportierte Funktion `ortgangFlaechenlaengeM`, **mit Testzusage** (`dachformVorlagen.test.ts:151-154`) |
| **Ortgangausbildung** | `dachformVorlagen.ts:127`, `:1386`, `:1410` | Feld mit Werten je Dachform |
| **Grat/Kehle am Sparren** | `geometry/schifterListe.ts:58` | **`klassifiziereSchifter`** unterscheidet `'kehle' \| 'grat' \| 'voll' \| 'beidseitig'` |

**Was fehlt, ist die Klassifikation der ECKE — nicht die der Sache.** *Der Unterschied ist scharf:*

```text
VORHANDEN   klassifiziereSchifter(vStart, vEnd, vMax)   ->  fragt: reicht dieser SPARREN
            schifterListe.ts:58                             bis zur Traufe und zum First?
FEHLT       joinType aus Eckwinkel + Kantentypen        ->  fragt: was entsteht an DIESER
                                                            ECKE des Grundrisses?
```

*Beide heißen „Grat" und „Kehle" und meinen dasselbe Bauteil — aber die eine leitet es aus dem
**Sparrenverlauf** ab, die andere aus der **Grundrissgeometrie**.* **Die zweite gibt es in der Insel
nicht; siehe `7-GRENZEN.md` mit den Zahlen.**
