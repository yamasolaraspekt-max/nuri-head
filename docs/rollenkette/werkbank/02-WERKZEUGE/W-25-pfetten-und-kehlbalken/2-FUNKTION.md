# W-25 · Pfetten und Kehlbalken — FUNKTION

> **Ablesung des vorhandenen Codes, nicht Vorgabe.** *Jede Zeilenangabe einzeln geöffnet.*

## Eine Funktion, drei Summen, kein Rechenweg

```text
holzBauteile.ts:56  holzBauteileAusListe(holzliste)
                :59  pfetten     Laenge + Anzahl
                :60  gratsparren Laenge + Anzahl
                :61  kehlsparren Laenge + Anzahl
                :64  L = gueltigeLaenge(stk.laenge)
                :65  L <= 0  ->  ueberspringen
                :66  type === 'pfette'       -> summieren
                :69  type === 'gratsparren'  -> summieren
                :72  type === 'kehlsparren'  -> summieren
```

> ***Das Modul rechnet nichts, es ZAEHLT.*** *Die Längen kommen fertig aus der Engine; hier werden
> sie nach `type` getrennt und aufsummiert.* **Der ganze fachliche Gehalt steckt in der Trennung**
> — *Pfetten, Gratsparren und Kehlsparren sind drei verschiedene Bestellposten, und wer sie in
> einer Summe führt, kann kein Holz bestellen.*

## `gueltigeLaenge` ist die einzige Wache — und sie ist die richtige

```text
:52  typeof l === 'number' && Number.isFinite(l) && l > 0 ? l : 0
:65  L <= 0  ->  continue
```

> **Drei Prüfungen in einer Zeile: Typ, Endlichkeit, Vorzeichen.** *Ein `NaN` aus der Engine
> verschwindet lautlos statt die ganze Summe zu vergiften* — **und der Wächter fährt genau das**
> (`:58` *„ungültige/negative Längen → ignoriert (kein NaN/Infinity/negativ)").*

## Die vierte Ausfuhr ist eine LISTE VON LÜCKEN

```text
:45  OFFENE_HOLZBAUTEILE  — exportierte Konstante, vier Eintraege
```

> ***Ein Modul, das seine eigene Grenze exportiert, damit die Oberfläche sie anzeigen kann.***
> *Das ist keine Dokumentation im Kommentar, sondern ein **Liefergegenstand**.* **Und der Kopf sagt
> den Grund** (`:42-43`): *„bewusst NICHT als echte Mengen ausgegeben (keine erfundenen Werte)".*
