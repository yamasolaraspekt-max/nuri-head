# W-41 · Abhängigkeitsgraph — BEDIENUNG

> **Vorgabe, kein Bestand.** *Was hier steht, ist die Anforderung an die spätere Bedienung. Wo die
> Quelle schweigt, steht das ausdrücklich da.*

## Aufruf

| Weg | Wie |
|---|---|
| **direkt** | **keiner** — W-41 wird nie aufgerufen, es läuft an |
| **ausgelöst** | durch jede Änderung an einer Angabe, auf der etwas beruht |

**Das ist die ganze Bedienung: es gibt keine.** *Ein Mechanismus, den der Anwender starten müsste,
wäre keine Zusage — er wäre eine Option.*

## Ablauf am Bildschirm

| Schritt | Anwender tut | Was er sehen muss |
|---|---|---|
| 1 | *ändert die Dachgeometrie* | — |
| 2 | *(nichts)* | **die davon abhängigen Ergebnisse sind jetzt `outdated`** |
| 3 | *sieht ein `outdated`-Ergebnis an* | **den alten Wert, den Zeitpunkt und den Grund** |
| 4 | *(nichts)* | **kein Ergebnis ist verschwunden** |

> **Schritt 4 ist die Zusage, und sie ist negativ formuliert — deshalb ist sie schwer zu zeigen und
> leicht zu brechen.** *Man sieht nicht, dass etwas nicht verschwunden ist. Genau deshalb braucht es
> Schritt 3: **der alte Wert, sichtbar und als ungültig gekennzeichnet**, ist der einzige Beweis,
> den ein Anwender führen kann.*

## Rückmeldungen

| Lage | Was der Anwender sehen muss | Herkunft |
|---|---|---|
| ein Wert wird `outdated` | **dass er es ist** | W-40, Register |
| er sieht ihn an | **warum** — welche Änderung es ausgelöst hat | *Vorgabe dieses Blattes, nicht der Quelle* |
| die Kette geht weiter | **dass auch Folgewerte betroffen sind** | Schritt 4 aus `2-FUNKTION.md` |
| nichts hängt daran | **keine Meldung** | *eine Invalidierung ohne Betroffene ist kein Ereignis* |

> **Die zweite Zeile ist die am schwächsten belegte und zugleich die wichtigste.** *„Niemals stille
> Löschung" sagt, dass nichts verschwindet — es sagt nicht, dass der Anwender den Grund erfährt.*
> **Ohne den Grund ist `outdated` eine Absage ohne Erklärung, und das ist die Form des teuersten
> Fehlers dieses Projekts.** *Als Vorgabe aufgenommen, als Beleglücke in `7-GRENZEN.md` benannt.*

## Was NICHT passieren darf

```text
NICHT  ein abgeleiteter Wert verschwindet                 <- das VERBOT
NICHT  ein Wert bleibt gueltig, dessen Grundlage
       schon ungueltig ist                                <- die Luege zweiter Ordnung
NICHT  der Anwender muss die Invalidierung anstossen      <- dann ist sie keine Zusage
NICHT  eine Meldung ohne Bezug („etwas hat sich
       geaendert")                                        <- das ist ein Hinweis, keine Auskunft
```

## Abbruch

**Es gibt keinen.** *Eine Invalidierung ist kein Vorgang, den man abbricht — sie ist eine Folge.*

> *Ob eine Invalidierung **zurückgenommen** werden kann, wenn die Änderung rückgängig gemacht wird,
> sagt die Quelle nicht.* **Nicht erfunden — `7-GRENZEN.md`.**

## Sichtprüfung

- [ ] **entfällt** — *eine Vorgabe zeigt nichts an. Die Sichtprüfung gehört zum Bau, und sie wird
      die schwerste sein: zu zeigen, dass etwas NICHT verschwunden ist.*
