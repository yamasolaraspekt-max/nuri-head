# W-21 · Sparren und Lattung — PRÜFUNG

## Was eine Prüfung hier belegen muss

1. **Dass der Schiftsparren als Sparren zählt.** Der Code nennt den Fehler beim Namen:
   *„sonst Mengen-Unter-Count!"* (`resources/planner/hausplaner/geometry/holzMengen.ts:14`). **Eine Menge, die zu klein ist,
   sieht nicht falsch aus.**
2. **Dass ungültige Längen zu 0 werden und nie zu `NaN`.** `gueltigeLaenge()` prüft
   `typeof number && isFinite && > 0` — in **beiden** Aggregationsmodulen getrennt implementiert.
3. **Dass `OFFENE_HOLZBAUTEILE` mitgeführt wird**, wenn ein Bericht Mengen zeigt. *Eine Liste der
   Lücken nützt nur, wenn sie dort ankommt, wo die Zahlen stehen.*
4. **Dass die Vorbemessung als Vorbemessung ausgewiesen ist** — an jeder Stelle, die sie zitiert.

## Warum Punkt 4 in einer Prüfliste steht und nicht nur in `7-GRENZEN`

**Der Vorbehalt reist nicht mit der Zahl.** `berechneSparren()` liefert einen Querschnitt; der
Warnsatz steht im Dateikopf. *Wer das Ergebnis in ein Angebot übernimmt, überträgt die Zahl und
lässt den Satz zurück.*

## Was ich NICHT geprüft habe

**Ob die beiden `HolzStueckRef` heute feldgleich sind** (`schifterListe.ts:134` gegen
`holzBauteile.ts:22`). *Benannt, nicht gemessen.*
