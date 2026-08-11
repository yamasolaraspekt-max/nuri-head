# W-11 · Maß und Bemaßung — PRÜFUNG

## Was eine Prüfung hier belegen muss

1. **Dass keine 0-Segmente entstehen.** Die Entdopplung ist der Zweck, nicht ein Nebeneffekt.
2. **Dass `istBrauchbareLaenge()` von außen prüfbar ist** — nicht im Rumpf versteckt.
3. **Dass die Schichten getrennt bleiben.** Ein Import von `masseingabe` nach `masskette` (oder
   zurück) wäre ein Befund, kein Fortschritt.
4. **Dass `MassPunkt` auf beiden Seiten gleich bleibt** (siehe `7-GRENZEN`).

## Warum Punkt 2 eigens dasteht — es ist eine Lehre aus einer Mutationsprobe

Der Code selbst hält sie fest, bei `richtungAus()`:

> *„In der Mutationsprobe blieb „keine Richtung wird zugelassen" **BLIND**, solange die Bedingung
> innen stand: ohne sie kommt `0/0 = NaN` heraus, die Endlichkeitsprüfung weiter unten fängt es ab,
> und von außen sieht alles gleich aus. **Eine Zusage kann nicht halten, was nach außen nicht sichtbar
> ist** — der Vertrag „aufeinanderliegende Punkte ergeben keine Richtung" ist aber echt und soll nicht
> davon abhängen, dass `NaN` sich gutmütig verhält."* (`masseingabe.ts:48-53`)

**Deshalb sind `istBrauchbareLaenge()` und `richtungAus()` eigene Ausfuhren** — damit die Ansicht
dieselbe Frage stellen kann wie die Rechnung *„und nicht ihre eigene zweite Antwort baut"*.
