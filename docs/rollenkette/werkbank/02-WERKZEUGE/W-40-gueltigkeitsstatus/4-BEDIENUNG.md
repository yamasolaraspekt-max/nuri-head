# W-40 · Gültigkeitsstatus — BEDIENUNG

> **Vorgabe, kein Bestand.** *Was hier steht, ist die Anforderung an die spätere Bedienung — nicht
> die Beschreibung einer vorhandenen. Wo die Quelle schweigt, steht das ausdrücklich da.*

## Aufruf

| Weg | Wie |
|---|---|
| **`confirmed` setzen** | **eine ausdrückliche Handlung des Nutzers** — das ist der ganze Zweck der Stufe |
| **`outdated` setzen** | **niemand von Hand** — es entsteht aus einer Änderung (W-41) |
| **`blocked` setzen** | *die Quelle sagt es nicht* |

**Die erste Zeile ist die tragende Anforderung:** *`confirmed` trennt „gerechnet" von „vom Nutzer
bestätigt".* **Eine Stufe, die das Programm selbst setzen könnte, wäre keine Bestätigung, sondern
eine zweite Berechnung.**

## Rückmeldungen

| Lage | Was der Anwender sehen muss | Herkunft |
|---|---|---|
| `confirmed` | dass **er** es bestätigt hat, nicht dass es gerechnet wurde | Quelle `:127-128` |
| `outdated` | **was** die Bestätigung ungültig gemacht hat | *die Quelle nennt die Anforderung nicht — siehe unten* |
| `blocked` | dass gesperrt ist und **warum** | *nicht belegt* |

> **Die zweite Zeile ist die wichtigste und zugleich die am schwächsten belegte.** *„Änderungen
> propagieren, **niemals stille Löschung**" sagt, dass ein Ergebnis nicht verschwinden darf — es
> sagt nicht, dass der Anwender den Grund erfährt.* **Ohne den Grund ist `outdated` eine Absage
> ohne Erklärung, und genau das ist der teuerste Fehler dieses Projekts gewesen** *(das Dach, das
> bei nicht-rechteckiger Kontur unsichtbar verschwand).*
>
> **Das Blatt fordert den Grund deshalb nicht — es benennt die Lücke.** *Ob er mitgeführt wird, ist
> eine Entscheidung, und sie steht in `7-GRENZEN.md`.*

## Ablauf am Bildschirm

| Schritt | Anwender tut | Was gelten muss |
|---|---|---|
| 1 | *rechnet ein Ergebnis* | Fortschritt `ok` — **Gültigkeit noch NICHT `confirmed`** |
| 2 | *bestätigt es* | Gültigkeit `confirmed` — **erst jetzt darf L-9 grün sein** |
| 3 | *ändert etwas Zugrundeliegendes* | Gültigkeit `outdated` — **der Fortschritt bleibt `ok`** |

**Schritt 3 ist der Beleg für die zwei Achsen:** *der Schritt ist weiterhin fertig gerechnet, und
trotzdem ist sein Ergebnis nicht mehr gültig.* **In einer einzigen Liste wäre das nicht
darstellbar.**

## Abbruch

*Die Quelle sagt nicht, ob eine Bestätigung zurückgenommen werden kann.* **Nicht erfunden —
`7-GRENZEN.md`.**

## Sichtprüfung

- [ ] **entfällt** — *es gibt nichts zu sehen. Dieses Blatt ist eine Vorgabe; die Sichtprüfung
      gehört zum Bau.*
