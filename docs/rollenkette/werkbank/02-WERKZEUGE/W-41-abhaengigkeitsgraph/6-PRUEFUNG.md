# W-41 · Abhängigkeitsgraph — PRÜFUNG

> **Vorgabe, kein Bau.** *Die Kriterien oben prüfen das BLATT; die Vorgaben für den späteren Bau
> stehen darunter.*

## Abnahmekriterien dieses Blattes

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Das **Verbot** ist der Kern, nicht die Propagierung | W-41 als Invalidierungs-Cache beschreiben | Zitat aus `REGISTER.md:128`, am Bau-Stand |
| K-2 | Die Grenze zu W-40 steht in `2-FUNKTION` | `outdated` hier neu definieren | „W-40 sagt DASS und WAS, W-41 sagt WANN und WORAUF" |
| K-3 | Die Quelle führt den Graphen unter **nicht gemessen** | die Vorgabe für eine Erhebung halten | `BERICHT-PROZESSEBENE-DREI-FRAGEN.md:147` und `:191`, wörtlich |
| K-4 | Die Anschlussliste steht als **Frage** | eine Struktur erfinden | je Abhängigkeit: Fundstelle **oder** Kennzeichnung als Kandidat |
| K-5 | Was erhalten bleibt, ist benannt | „nichts geht verloren" ohne zu sagen, was bleibt | alter Wert · Zeitpunkt · Grund |

## Was der spätere BAU erfüllen muss — Vorgabe

```text
B-1  Invalidierung SETZT SICH FORT. Ein Wert, dessen Grundlage outdated ist, wird
     ebenfalls outdated.
     Rot-Probe: eine einstufige Markierung laesst Werte gueltig aussehen, deren
     Grundlage schon ungueltig ist — die Luege zweiter Ordnung.

B-2  NICHTS VERSCHWINDET. Der alte Wert bleibt lesbar.
     Rot-Probe: ein Testfall, der einen abgeleiteten Wert invalidiert und danach
     seinen alten Inhalt LIEST. Faellt er, ist es eine Loeschung mit anderem Namen.

B-3  Jede Invalidierung traegt Zeitpunkt und GRUND.
     Vorbild fuer die Form: markiereVeraltet fuehrt updatedAt und updatedBy —
     der GRUND fehlt auch dort.

B-4  Der Mechanismus laeuft OHNE Zutun des Anwenders.
     Rot-Probe: muss er ihn anstossen, ist es eine Option und keine Zusage.

B-5  Zyklen sind behandelt — oder es ist gemessen und belegt, dass es keine gibt.
     Beides ist zulaessig; unbehandelt und ungemessen ist es nicht.
```

> **B-2 ist die einzige Zusage, die sich nur NEGATIV prüfen lässt** — *man zeigt, dass etwas noch da
> ist.* **Deshalb muss der Test den alten Inhalt tatsächlich lesen und nicht nur die Existenz eines
> Feldes behaupten.**

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| die Fortsetzung (Schritt 4) entfernen | *nach dem Bau: B-1* |
| den alten Wert beim Invalidieren verwerfen | *nach dem Bau: B-2* |
| den Grund weglassen | *nach dem Bau: B-3* |

> **Alle drei sind VORGABEN, nicht gefahren** — *es gibt nichts zu mutieren.* **Wer den Unterschied
> nicht kennzeichnet, verkauft eine Absicht als Messung.**

## Automatische Tests

| Datei | Prüft |
|---|---|
| **keine** | *W-41 hat keinen Code* |

**Ein vorhandener Test ist trotzdem relevant** — *als Vorbild für B-2:*

```text
__tests__/configuratorPackage.test.ts:54-61
  „markiereVeraltet: freigegebenes Paket wird outdated"
  … und ein Entwurf bleibt UNVERAENDERT.
```

> **Der zweite Teil ist der interessante:** *der Test prüft nicht nur, dass etwas passiert, sondern
> auch, dass an anderer Stelle **nichts** passiert.* **Genau diese Form braucht B-2.**

## Sichtprüfung und Bestandsprobe

- [ ] **entfallen** — *eine Vorgabe zeigt nichts an und ändert kein Dokument.*

> **Für den späteren Bau ist die Sichtprüfung allerdings die schwerste des ganzen Werkzeugs:** *zu
> zeigen, dass ein Wert **nicht** verschwunden ist, ist eine Prüfung auf eine Abwesenheit von
> Abwesenheit.* **Sie gelingt nur, wenn der alte Wert sichtbar und als ungültig gekennzeichnet
> dasteht.**
