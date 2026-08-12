# W-40 · Gültigkeitsstatus — BEDIENUNG

> **Der ZUSTAND ist gebaut, die BEDIENUNG nicht.** *`ConfiguratorStatus` samt Übergängen und
> Wächtern existiert (`geometry/configuratorPackage.ts`) — was hier steht, ist die Anforderung an die
> spätere **Bedienung** dieser Achse.* **Wo die Quelle schweigt, steht das ausdrücklich da.**
>
> **BERICHTIGT (W-40/1, 12.08.).** *Hier stand „Vorgabe, kein Bestand" — den Bestand gibt es, nur
> keine Oberfläche dafür.*

## Aufruf

| Weg | Wie |
|---|---|
| **`approved` setzen** *(= `confirmed`)* | **eine ausdrückliche Handlung des Nutzers** — das ist der ganze Zweck der Stufe; der Weg dorthin führt laut `:107` **nur über `checked`** |
| **`outdated` setzen** | **niemand von Hand** — es entsteht aus einer Änderung (W-41); `markiereVeraltet` ist gebaut |
| **`blocked` setzen** | **GAR NICHT — Yamas Auflage vom 12.08.: *„`blocked` wird NIE von Hand gesetzt oder gelöst, wer das will meint `DECISION_BLOCKED`"*.** Es fällt automatisch, sobald die Vorbedingung messbar erfüllt ist |

**BERICHTIGT (W-40/1):** *in Zeile 3 stand* **„die Quelle sagt es nicht"** *— Yama sagt es jetzt, und
zwar als Verbot.* **Wer hier eine Schaltfläche baut, baut die falsche Sperre.**

**Die erste Zeile ist die tragende Anforderung:** *`confirmed` trennt „gerechnet" von „vom Nutzer
bestätigt".* **Eine Stufe, die das Programm selbst setzen könnte, wäre keine Bestätigung, sondern
eine zweite Berechnung.**

## Rückmeldungen

| Lage | Was der Anwender sehen muss | Herkunft |
|---|---|---|
| `confirmed` | dass **er** es bestätigt hat, nicht dass es gerechnet wurde | Quelle `:127-128` |
| `outdated` | **was** die Bestätigung ungültig gemacht hat | *die Quelle nennt die Anforderung nicht — siehe unten* |
| `blocked` | dass gesperrt ist und **warum** | **BELEGT (Yama, 12.08.): `blockiert_durch` ist Pflicht — *„ein `blocked` ohne `blockiert_durch` ist eine Absage ohne Erklärung"*** |

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
| 1 | *rechnet ein Ergebnis* | **Schritt** `ok` — **Paket noch nicht `approved`** |
| 2 | *bestätigt es* | **Paket** `approved` — **erst jetzt darf L-9 grün sein**, `kannIntegrieren` (`:120`) |
| 3 | *ändert etwas Zugrundeliegendes* | **Paket** `outdated` — **der Schritt bleibt `ok`** |

**Schritt 3 ist der Beleg für die zwei Achsen:** *der Schritt ist weiterhin fertig gerechnet, und
trotzdem ist das Paket nicht mehr gültig.* **In einer einzigen Liste an einem Träger wäre das nicht
darstellbar** — *und genau das war der Fehler der ersten Fassung dieses Blattes (W-40/1).*

## Abbruch

**BEANTWORTET, vom Code statt von der Quelle (W-40/1).** *Eine Bestätigung kann zurückgenommen
werden, aber **nur über `outdated`** und nie still — der Grundsatz steht wörtlich im Dateikopf von
`configuratorPackage.ts`: „bewusst streng … Freigabe-Schutz — keine stille Rückstufung", geprüft von
`configuratorPackage.test.ts:41`.*

> *Hier stand:* **„Die Quelle sagt nicht, ob eine Bestätigung zurückgenommen werden kann. Nicht
> erfunden."** *Erfunden hätte ich sie auch nicht — aber ich hätte sie **nachschlagen** können.*

## Sichtprüfung

- [ ] **entfällt** — *es gibt nichts zu sehen: die Achse ist gebaut, aber **unsichtbar**. Kein
      Bildschirm zeigt `approved` oder `outdated` an.*

> **Das ist nach W-40/1 der schärfste offene Punkt des Werkzeugs.** *Eine Invalidierung, die niemand
> sieht, ist eine stille Löschung mit anderem Namen* — **und „niemals stille Löschung" ist der Satz,
> aus dem `outdated` überhaupt hervorgeht.** *Gestellt, nicht behoben: die Oberfläche ist nicht
> Gegenstand dieses Auftrags.*
