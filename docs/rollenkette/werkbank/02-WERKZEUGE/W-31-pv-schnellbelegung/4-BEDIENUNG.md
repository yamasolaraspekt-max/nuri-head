# W-31 · PV-Schnellbelegung — BEDIENUNG

## Der Bedienweg — FÜNF Stellen, alle am Bau-Stand geöffnet

*Ein früherer Stand dieses Auftrags nannte **vier** und hielt den Weg für vollständig. Es sind
fünf, und die fünfte ist nicht irgendeine — siehe unten.*

```text
1  app/dashboard/enginePanels.ts:32    import { pvSchnellBelegung, type PvEingabe }
2  app/dashboard/enginePanels.ts:380   engineId: 'engine-pv'
                                :381   titel:   'PV-Schnellbelegung'
3  app/dashboard/enginePanels.ts:403   berechne: pvSchnellBelegung(alsPvEingabe(werte))
   app/dashboard/enginePanels.ts:509   alsPvEingabe(...)  — der Adapter (W-37)
4  app/tools/faehigkeiten.ts:80        Registry-Eintrag, zustand 'verfuegbar'
5  app/dashboard/fachFlaechen.ts:240-258   Fachflaeche 'fach-pv-module',
                                           engine 'engine-pv'
```

**Der Registry-Eintrag trägt `zustand: 'verfuegbar'`** — *nicht `gesperrt`, nicht
`in_entwicklung`.* **Das Werkzeug ist im Programm erreichbar, während seine Registerzeile `LEER`
sagt.** *Genau diese Spannung beendet dieses Blatt.*

## Was der Anwender ausfüllt — das Engine-Panel

**Titel:** „PV-Schnellbelegung" · **Zweck** (`:382`): *„Legt Module auf eine Dachfläche und nennt
Anzahl, Leistung und Flächennutzung."* · **Grundlage** (`:383`): *„Rasterbelegung mit Randabstand
und Modulspalt; hoch- und querformatig verglichen"*.

**Sieben Felder, in der Reihenfolge des Panels**, mit `dachLaenge` als Pflichtfeld und **Vorgabe
10 000 mm** (`:385`). *Die zwei optionalen Felder — Randabstand und Modulabstand — dürfen leer
bleiben; das Modul setzt dann 300 und 20.*

## Die Regel, die man beim Ausfüllen nicht sieht: LEER ist nicht NULL

**`alsPvEingabe()` (`enginePanels.ts:509`) behandelt fehlende Felder in ZWEI verschiedenen Weisen —
und das ist Absicht:**

```text
Pflichtfelder      feldZahl(...) ?? 0
                   -> leer wird 0, und 0 laeuft in die Rechnung
optionale Felder   ...(feldZahl(...) !== undefined ? { randabstand: ... } : {})
                   -> leer wird WEGGELASSEN, damit die Vorgabe des Moduls greift
```

> **Der Unterschied ist der ganze Punkt.** *Würde ein leeres Randabstand-Feld als `0` eingesetzt,
> läge die Vorgabe von 300 mm brach und der Anwender bekäme Module bis an die Traufkante —
> **rechnerisch mehr, baulich falsch.*** Die Auslassung ist die einzige Schreibweise, die „nicht
> angegeben" von „ausdrücklich null" unterscheidet.

*Der Kommentar über der Nachbarfunktion sagt dieselbe Regel für die Sparren-Eingabe:* **„leere
Felder werden weggelassen, nicht auf 0 gesetzt. Eine 0 wäre eine erfundene Angabe."**

## Was er zurückbekommt

```text
Orientierung · Spalten × Reihen · Module gesamt · kWp
Dachflaeche (m²) · Belegte Flaeche (m²) · Flaechennutzung (%)
```

## Der zweite Ort: die Fachfläche `fach-pv-module`

**`app/dashboard/fachFlaechen.ts:240-258`** führt dieselbe Engine als Fachfläche in der Gruppe
**PV-Planer** — mit Zweck, Eingängen und Ausgängen, und mit den Typnamen als Beleg: `PvEingabe`
(`:248`) und `PvBelegung` (`:255`).

**Sie ist GERENDERT und nicht nur vorhanden:** *eingeführt von `app/FachFlaeche.tsx` und
`HausplanerStudio.tsx:18`.*

> ***Und unter ihren Eingängen steht bei `:252`:*** `{ label: 'Ausrichtung und Neigung', einheit: '°' }`
>
> **Das ist die einzige Stelle im ganzen Bedienweg, an der eine RICHTUNG vorkommt.** *Sie steht in
> einer **Vorschau** und nicht in `PvEingabe`.* **Was daraus folgt, steht in `7-GRENZEN` — dieses
> Blatt benennt die Spannung und entscheidet sie nicht.**

## Abbruch

**Keiner nötig.** *Die Rechnung hat keinen Zwischenzustand: Felder ändern, neu rechnen. Nichts wird
ins Modell geschrieben.*
