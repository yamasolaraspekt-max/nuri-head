# W-20 · Stückliste und Mengen — BEDIENUNG

> **Ablesung, kein Entwurf.** *Die Rechenschicht ist gebaut; eine eigene Werkzeug-Oberfläche hat sie
> nicht. Sie wird von der Material-/Holzliste **verbraucht**, und was dort steht, ist heute nicht
> Gegenstand dieses Blattes.*

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | **keiner** — W-20 ist kein Werkzeug mit Modus, sondern eine Aggregation |
| Tastenkürzel | **keines** |
| Automatisch | **ja** — die Mengen entstehen aus der vorhandenen Holzliste, sobald das Dach steht |

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | zeichnet das Dach | die Engine erzeugt die Holzliste mit den **echten**, geclippten Stablängen |
| 2 | öffnet die Material-/Holzliste | vier Kennzahlen: Sparrenlänge, Sparrenanzahl, Konterlattenlänge, Traglattenlänge |
| 3 | vergleicht mit der Zeichnung | **beide Zahlen stimmen überein** — das ist der ganze Zweck |

*Schritt 3 ist keine Bedienhandlung, sondern die Probe: **stimmen Liste und Zeichnung nicht überein,
ist die zweite Wahrheit zurück.***

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| Alles gut | „Sparren 148,20 lfm (37 Stück) · Konterlatten 96,40 lfm · Traglatten 412,80 lfm" | sachlich |
| **Keine Holzliste** (kein Dach) | „Noch kein Dach vorhanden — es gibt keine Mengen zu zeigen." | hinweisend |
| **Stab mit ungültiger Länge** | **heute: keine Anzeige.** *Der Stab wird still mit 0 gezählt* — siehe `7-GRENZEN.md` | **Lücke, benannt** |

> **Die dritte Zeile ist ein Befund und keine Beschreibung.** *`gueltigeLaenge` macht aus einer
> kaputten Länge eine `0` (`holzMengen.ts:40-42`) — richtig, damit die Liste weiterläuft. **Aber
> niemand erfährt es.*** *In einer Stückliste, aus der ein Angebot wird, ist eine stille Null die
> unangenehmste Zahl von allen.*

## Abbruch

**Entfällt.** *Es gibt keinen Modus, den man abbrechen könnte; die Funktion ist rein und ändert
nichts.*

## Tastenkürzel während des Werkzeugs

**Keine.** *Die Vorlage fragt danach, weil die meisten Werkzeuge einen Modus haben — dieses hat
keinen. Ein erfundenes Kürzel wäre hier eine Erfindung ohne Gegenstand.*
