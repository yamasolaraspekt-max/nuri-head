# W-04 · Öffnung (Tür/Fenster) — BEDIENUNG

## Zwei Werkzeuge in der Registry

```text
app/tools/toolRegistry.ts:78   id: 'fenster'
app/tools/paketAdapter.ts:186  'fenster' -> feld 'type', wert 'window'
app/tools/paketAdapter.ts:195  'tuer'    -> feld 'type', wert 'door'
```

**Die Trennung liegt in den DATEN, nicht im Code.** Beide Werkzeuge laufen durch dieselben
Funktionen; was sie unterscheidet, sind die Tabellen, die sie lesen — `TUER_TYPEN` gegen
`FENSTER_TYPEN`, `TUER_BAUARTEN` gegen `FENSTER_BAUARTEN`. *Es gibt keinen Tür-Zweig und keinen
Fenster-Zweig in der Logik.*

## Was der Anwender wählt

| Schritt | Woraus | Was er bekommt |
|---|---|---|
| **Bauart** (Aussehen) | 24 bzw. 24 SVG-Bauarten | Icon im Panel; SVGs unter `public/hausplaner/icons/` |
| **Typ** (Größe) | 5 Tür- bzw. 7 Fenster-Vorlagen | lichte Breite, lichte Höhe, bei Fenstern die Brüstung |

**Nur Fenster haben eine Brüstungshöhe** — `bruestung?` ist optional (`oeffnungsTypen.ts:17`).

## Reihenfolge ist Absicht

*„Reihenfolge = Anzeigereihenfolge; Drehtür 1-flg. zuerst = häufigste"* (`oeffnungsTypen.ts:21`).
**Der erste Eintrag ist der häufigste Fall — und zugleich der Rückfallwert** (siehe `7-GRENZEN`).

## Die Vorgabe-Öffnungsart darf fehlen

`oeffnungsArt?` ist optional, und der Code sagt warum: *„Vorgabe-Öffnungsart, sofern eindeutig; sonst
undefined (Nutzer wählt)"* (`oeffnungsBauarten.ts:12`). **Der Katalog rät nicht.**
