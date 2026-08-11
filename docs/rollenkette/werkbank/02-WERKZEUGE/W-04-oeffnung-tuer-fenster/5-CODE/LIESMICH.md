# W-04 · Öffnung (Tür/Fenster) — CODE

**Angebunden aus zwei vorhandenen Modulen** — dieses Blatt ist aus dem Code abgeleitet, nicht umgekehrt.

## `resources/planner/hausplaner/geometry/oeffnungsBauarten.ts` — 75 Zeilen, 5 Ausfuhren

`OeffnungsBauart` · `FENSTER_BAUARTEN` · `TUER_BAUARTEN` · `fensterBauartNach()` · `tuerBauartNach()`

## `resources/planner/hausplaner/geometry/oeffnungsTypen.ts` — 49 Zeilen, 7 Ausfuhren

`TuerTyp` · `FensterTyp` · `TypVorlage` · `TUER_TYPEN` · `FENSTER_TYPEN` · `tuerTyp()` · `fensterTyp()`

## AUSSCHLUSS — mit einer Ausnahme, die wirklich besteht

```text
resources/planner/hausplaner/geometry/fensterProdukt.ts   NICHT Gegenstand dieses Blattes
```

**Aber der Ausschluss ist nicht pauschal.** Gemessen:

```text
oeffnungsBauarten.ts:3   import type { OeffnungsArt } from './fensterProdukt';
```

**Ein reiner Typ-Import** (`import type`) — er verschwindet beim Übersetzen und bringt keinen
Laufzeitcode mit. `OeffnungsArt` ist damit die **einzige** Berührung; `oeffnungsTypen.ts` nennt
`fensterProdukt` gar nicht. *Ein pauschales „hat nichts damit zu tun" wäre nachweislich falsch.*

## Was gebaut ist und was nicht

**Gebaut:** beide Kataloge und vier Nachschlagefunktionen, rein — keine Szene-Mutation, kein Rendern.
**Nicht Gegenstand dieser Stufe:** die Werkzeugschicht. *Stufe 2 (`GEBAUT`) folgt als eigener Auftrag.*
