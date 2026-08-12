# W-23 · Deckung und Material — FUNKTION

> **Jede Zahl auf diesem Blatt ist aus der Quelle abgelesen, nicht entworfen.**
> *Datei · Blatt · Spalte · Zeile stehen in `5-CODE/LIESMICH.md`; jede Angabe ist dort nachlesbar.*

## Eingabe

| Was | Typ | Einheit | Pflicht | Prüfung |
|---|---|---|---|---|
| `sparrenlaengeMm` | Zahl | mm | ja | > 0; Traufe→First, die zu teilende Strecke |
| `dachneigungGrad` | Zahl | ° | ja | wird gegen `Regeldachneigung_grad` des Modells geprüft |
| `modellId` | Kennung | — | ja | muss ein Modell mit **beiden** Lattmaßen sein — heute **sieben** |

**Aus dem Modell kommen drei Werte, alle aus der Tabelle:**

| Woher | Feld | Rolle |
|---|---|---|
| Spalte 26 · 27 | `Lattmass_min_mm` · `Lattmass_max_mm` | der erlaubte **Bereich** |
| Spalte 33 | `Regeldachneigung_grad` | die **Schranke** |
| Spalte 32 | `Verschiebespiel_mm` | die **Eingangsprüfung** (siehe `6-PRUEFUNG.md`) |

## Verarbeitung — der Zustandsautomat

```
Eingaben stehen
   │
   ├─ Neigung < Regeldachneigung ────────►  ABSAGE 1: Modell fuer diese Neigung nicht zulaessig
   │                                        (KEINE Rechnung, kein Wert)
   ├─ Regeldachneigung fehlt ─────────────►  ABSAGE 2: Schranke nicht pruefbar
   │                                        (betrifft heute Rubin 13V, beide Zeilen)
   └─ Neigung >= Regeldachneigung
        │
        n_min = aufrunden (L / Lattmass_max)
        n_max = abrunden  (L / Lattmass_min)
        │
        ├─ n_min >  n_max ────────────────►  ABSAGE 3: keine gleichmaessige Teilung
        │                                    (ECHTE Ausgabe, KEINE Zahl)
        └─ n_min <= n_max ────────────────►  ERGEBNIS: fuer jedes n im Bereich
                                             Lattmass = L / n
```

**Abbruch (Esc) führt in den Ausgangszustand zurück; es wird nichts geschrieben** — das Werkzeug
rechnet, es ändert das Modell nicht.

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| `reihen` | Liste von `{ n, lattmassMm }` | Anzeige und weiter an **W-21L** |
| `absage` | benannter Fall (1, 2 oder 3) statt einer Zahl | Anzeige, mit dem Anwendertext aus `4-BEDIENUNG.md` |
| `bereich` | `{ min, max }` des Modells | Anzeige, damit der Anwender sieht, woran es scheitert |

> **Die zweite Zeile ist der Kern des Werkzeugs.** *Ein Fall ohne gleichmäßige Teilung ist **kein
> Fehler**, sondern ein Ergebnis — und er muss als solches zurückkommen. **Wer hier eine Zahl
> zurückgibt, gibt eine erfundene zurück.***

## Kommando (für Rückgängig)

**Keines.** *Das Werkzeug schreibt nichts am Modell — es liest Maße und rechnet. Es gibt nichts
zurückzunehmen.* **Erst W-21L, das aus dem Ergebnis eine Lattung baut, braucht ein Kommando.**

## Schichtzuordnung

- **Schicht 1 (Domäne):** *nein* — keine Mutation.
- **Schicht 2 (Geometrie):** **ja, hier liegt die Rechnung** — `F-050`/`F-053`, siehe `3-FORMELN.md`.
- **Schicht 3 (Anwendung):** noch nicht gebaut; das Werkzeug ist Stufe 1 (`BESCHRIEBEN`).
- **Schicht 4/5:** der Anwender sieht den Bereich, die möglichen Reihenzahlen — **oder die Absage im
  Klartext.**
