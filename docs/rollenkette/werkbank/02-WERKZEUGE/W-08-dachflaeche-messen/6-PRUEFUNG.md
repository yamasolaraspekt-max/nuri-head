# W-08 · Dachfläche messen — PRÜFUNG

## Was eine Prüfung hier belegen muss

1. **Dass eine polygonale Fläche kleiner herauskommt als ihr Rahmen.** Das war der ganze Anlass.
2. **Dass die Umlaufrichtung egal ist.** Der Code sagt es zu: *„umgekehrte Reihenfolge → identisches
   (positives) Ergebnis (Betrag)"* (Z.28).
3. **Dass niemals `NaN` oder `Infinity` herauskommt** (Z.29) — geprüft wird jeder Punkt, nicht nur
   der erste (Z.37-43).
4. **Dass `0` in allen drei Bedeutungen unterschieden wird** — siehe `7-GRENZEN`. *Eine Prüfung, die
   nur „kommt eine Zahl?" fragt, kann diesen Fall nicht sehen.*

## Der Prüfpunkt, den nur ein Vergleich findet

**Dieselbe Fläche, einmal durch die TS-Fassung und einmal durch die PHP-Fassung** — mit derselben
Geometrie, aber in der jeweils erwarteten Einheit. **Kommt beide Male dieselbe Zahl heraus?**
*Wenn nicht, ist eine der beiden Aufrufstellen falsch verdrahtet, und keine Zusage im Haus würde es
melden.* Siehe `7-GRENZEN`.

## Was ich NICHT geprüft habe

**Ob die vier Aufrufer wirklich Meter übergeben.** Ich habe die Zusage des Dateikopfs gelesen und die
Importe gezählt — **nicht** die Einheit an jeder der vier Aufrufstellen nachgerechnet.
*Als Frage notiert, nicht als Zusage.*
