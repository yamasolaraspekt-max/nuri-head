# W-20 · Stückliste und Mengen — FUNKTION

> **Dieses Blatt ist eine ABLESUNG.** *Der Kern ist gebaut, getestet und im Dateikopf begründet —
> jede Angabe hier steht in `geometry/holzMengen.ts` mit Zeilennummer.*

## Eingabe

| Was | Typ | Einheit | Pflicht | Prüfung | Quelle |
|---|---|---|---|---|---|
| `holzliste` | `ReadonlyArray<HolzStueck>` \| `undefined` \| `null` | — | nein | `Array.isArray` — alles andere wird zur leeren Liste | `:44`, `:49` |
| ↳ `type` | `string?` | — | nein | `'sparren'` \| `'latte'` | `:24` |
| ↳ `name` | `string?` | — | nein | entscheidet die Bauteilart | `:25` |
| ↳ `laenge` | `number?` | lfm | nein | `gueltigeLaenge` — siehe unten | `:26`, `:40-42` |

**Die Quelle ist `RoofEngine.holzliste`** — *die ECHTE, bereits in der 3D-Geometrie erzeugte Liste
(`:11`).* **Nicht der Umriss, nicht die Anzahl mal Höhe.**

## Verarbeitung

```
fuer jedes Stueck der Liste:
   │
   ├─ Stueck ist null/undefined ──────────►  uebersprungen                        (:50)
   │
   ├─ type === 'latte' ───────────────────►  lattenLaenge += L                    (:52-53)
   ├─ name === 'Konterlatte' ─────────────►  konterLaenge += L                    (:54-55)
   ├─ name beginnt mit 'Sparren'
   │  ODER mit 'Schiftsparren' ───────────►  sparrenLaenge += L                   (:56-59)
   │                                         und sparrenAnzahl += 1, WENN L > 0   (:60)
   └─ sonst ──────────────────────────────►  faellt durch, zaehlt nirgends
```

**Die Gültigkeitsprüfung** (`gueltigeLaenge`, `:40-42`): *nur eine endliche Zahl größer null zählt;
alles andere wird zu **0**.* **Niemals `NaN`, niemals `Infinity`, niemals negativ** — der Dateikopf
sagt es als Zusage (`:18`).

> ### Warum Schiftsparren als Sparren mitzählen — die Begründung steht im Code, nicht in der Gewohnheit
>
> *`holzMengen.ts:57-58`, wörtlich:*
>
> > **„EA28: Schiftsparren sind Gemeinsparren (nur verkürzt/angeschnitten) — sie MÜSSEN hier
> > mitzählen, sonst fallen die an Kehle/Grat geclippten Sparren aus Bauholz-m³ und Lohn heraus
> > (Unter-Count)."**
>
> **Ein Unter-Count in der Stückliste ist ein Fehlbetrag im Angebot.** *Das ist der Grund für
> `name.startsWith("Sparren") || name.startsWith("Schiftsparren")` in `:56` — **keine Bequemlichkeit,
> sondern eine Geldfrage.***
>
> *Und die Reihenfolge der Bedingungen trägt mit: `type === 'latte'` steht **vor** der Namensprüfung.
> Eine Traglatte, die zufällig „Sparren…" hieße, zählte als Latte — die Art schlägt den Namen.*

## Ausgabe

| Was | Typ | Einheit | Quelle |
|---|---|---|---|
| `sparrenLaenge` | Zahl | lfm | `:31` — Summe der echten Sparrenlängen, **ohne** Konterlatten |
| `konterLaenge` | Zahl | lfm | `:33` |
| `lattenLaenge` | Zahl | lfm | `:35` — **Traglatten**, nicht Lattmaß |
| `sparrenAnzahl` | Zahl | Stück | `:37` — nur Stäbe mit `L > 0` |

## Kommando (für Rückgängig)

**Keines.** *Die Funktion ist rein: sie liest eine Liste und gibt vier Zahlen zurück — keine
Mutation, keine React-, keine THREE-Abhängigkeit (`:19`).* **Es gibt nichts zurückzunehmen.**

## Schichtzuordnung

- **Schicht 1 (Domäne):** nein — die Liste kommt aus der Engine, W-20 ändert sie nicht.
- **Schicht 2 (Geometrie):** **ja, hier liegt sie** — `geometry/holzMengen.ts`, 64 Zeilen, 3 Exporte.
- **Schicht 3 (Anwendung):** die Stückliste/Material-Ansicht ist der Verbraucher.
- **Schicht 4/5:** der Anwender sieht vier Kennzahlen, die zur Zeichnung passen — *das ist der Zweck.*
