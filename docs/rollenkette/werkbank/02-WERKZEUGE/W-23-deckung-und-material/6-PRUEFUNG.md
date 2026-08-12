# W-23 · Deckung und Material — PRÜFUNG

> **Regel: jedes Kriterium muss VOR dem Bau wirksam rot sein.**
> Ein Kriterium, das schon grün ist, bevor gebaut wurde, prüft nichts.

**Diese Kriterien gelten für den BAU (Stufe 2).** *Alle sind heute rot, weil es keinen Code gibt —
der Rot-Beleg ist deshalb bei jedem derselbe und trotzdem echt.*

## Abnahmekriterien

| Nr | Kriterium | Rot-Beleg vor dem Bau | Wie gemessen |
|---|---|---|---|
| **K-1** | **Die Eingangsprüfung** — `Verschiebespiel_mm == Lattmass_max − Lattmass_min` | kein Code vorhanden | über alle Zeilen mit beiden Werten; **acht von acht gehen auf** (siehe unten) |
| **K-2** | Die **Neigungsschranke** greift: Neigung < Regeldachneigung → **keine Rechnung** | — | Zusage mit einem Modell à 25° und einem Dach à 22° |
| **K-3** | **Fehlende Schranke** führt zur eigenen Absage, nicht zum Durchrutschen | — | Zusage mit `Rubin 13V` (Regeldachneigung leer) |
| **K-4** | **`n_min > n_max` liefert KEINEN Wert**, sondern den Fall | — | Zusage mit `Harzer Pfanne 7` (Variante `Big`), `L = 1000` → 2 Reihen 500 mm, 3 Reihen 333 mm, erlaubt 372–405 |
| **K-5** | Jedes gelieferte Lattmaß liegt **im Bereich** `[min, max]` — auch nach der Anzeigerundung | — | Zusage über alle sieben Modelle × mehrere Sparrenlängen |
| **K-6** | Die **verworfene erste Fassung** ist nicht eingebaut | — | Gegenprobe: `n = aufrunden(L/max)` allein liefert bei `Harzer Pfanne 7`, `L=1000` **333,3** — dieser Wert darf nirgends herauskommen |
| **K-7** | **Kein Wert ohne Herkunft**: jeder übernommene Zahlenwert führt `Datenstatus` mit | — | Zahl der übernommenen Werte == Zahl der Statusangaben |

> **K-4 und K-6 sind dasselbe Kriterium von zwei Seiten**, und sie sind der Grund für dieses Blatt:
> *K-4 verlangt, dass der Fall **auftritt**; K-6 verlangt, dass die falsche Zahl **nicht auftritt**.*
> **Ohne K-6 könnte ein Werkzeug K-4 bestehen und trotzdem in 18,2 % der Fälle leise falsch rechnen.**

## Die Eingangsprüfung, heute schon durchgerechnet

*Sie kostet eine Subtraktion und ist die einzige Prüfung, die die Quelle **gegen sich selbst**
stellt: `Verschiebespiel` ist die Differenz der beiden Lattmaße — eine Doppelangabe, die
übereinstimmen muss.*

```text
Achat 12V        360 - 330 = 30   Spiel 30   ✓
Granat 11V       380 - 338 = 42   Spiel 42   ✓
Rubin 13V HA     360 - 330 = 30   Spiel 30   ✓
Rubin 13V OG     360 - 330 = 30   Spiel 30   ✓
Rubin 9V         400 - 370 = 30   Spiel 30   ✓
Topas 11V        380 - 320 = 60   Spiel 60   ✓
Topas 13V HA     360 - 320 = 40   Spiel 40   ✓
Topas 13V OG     360 - 320 = 40   Spiel 40   ✓
Harzer Pfanne 7  405 - 372 = 33   Spiel  —   fehlt, aus dem Bereich ABLEITBAR
```

**Acht von acht Zeilen mit beidem stimmen. Keine einzige Abweichung.** *Bei der neunten fehlt der
Wert und ist ableitbar (33).* **Wo beide stehen und nicht übereinstimmen, ist einer falsch — dann
darf das Werkzeug den Datensatz nicht verwenden.**

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| Schranke `>=` zu `>` ändern | K-2 — ein Dach mit **genau** der Regeldachneigung muss zulässig bleiben |
| `n_min > n_max` still auf `n_min` zurückfallen lassen | **K-4 und K-6** |
| Anzeigerundung ohne erneute Bereichsprüfung | **K-5** |
| Absage als `null` statt als benannter Fall zurückgeben | K-3 — *so verschwindet sie unterwegs, und das ist der Dach-Vorfall in neuer Gestalt* |

## Automatische Tests

| Datei | Prüft |
|---|---|
| `resources/planner/hausplaner/__tests__/lattmass.test.ts` *(neu in Stufe 2)* | K-1 bis K-7 |

*Der Dateiname ist Vorgabe, nicht Ablesung — heute existiert er nicht.*

## Sichtprüfung (falls die Oberfläche betroffen ist)

- [ ] 1440 px · 1024 px · 375 px
- [ ] **Die Absagetexte sind vollständig lesbar** — sie tragen drei Zahlen und sind lang

## Bestandsprobe

- [ ] Ein vor der Änderung gespeichertes Dokument lädt danach unverändert

*Betrifft Stufe 2 nur, wenn das gewählte Lattmaß im Modell abgelegt wird — **W-23 selbst schreibt
nichts.***
