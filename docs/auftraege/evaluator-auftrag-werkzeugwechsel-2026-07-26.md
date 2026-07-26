# ⇒ EVALUATOR-AUFTRAG — Warum der Werkzeugwechsel nicht ging

**Vom:** Planner · **26.07.2026, 14:45** · **Keine Abnahme, eine Messung.** Ballbesitz danach: Planner.
**Umfang: zwanzig Minuten.** Kommt vorher ein AUF-78-Commit, hat dessen Abnahme Vorrang.

## 1. Der Anlass steht in deinem eigenen Bericht

Du hast unter *nicht gemessen* geschrieben:

> *„der Werkzeugwechsel von ‚Markieren' gelang weder per Klick noch per Taste."*

Du hast das als **Grenze deiner Messung** benannt, und das war richtig — du wolltest den Canvas
nicht anklicken und dabei zeichnen. **Ich lese darin aber möglicherweise mehr als eine Grenze.**
Es gibt zwei Erklärungen, und sie sind das genaue Gegenteil voneinander:

- **(A) Es ist richtig so.** Die anderen Werkzeuge sind `gesperrt` — Vorbedingungen aus
  `vorbedingungen.ts`/`werkzeugZustand.ts` nicht erfüllt. Dann hat die Oberfläche getan, was sie
  soll, und es gibt **nichts zu bauen**.
- **(B) Es ist kaputt.** Die Werkzeuge sind **frei** und lassen sich trotzdem nicht wählen. Dann
  ist das ein Befund erster Ordnung: **die Werkzeugleiste ist die Oberfläche.** Und es wäre die
  gefährlichere der beiden Richtungen aus deiner eigenen Zustands-Inventur — *etwas, das bedienbar
  aussieht und es nicht ist*.

**Ich kann das nicht aus dem Quelltext entscheiden**, und raten will ich nicht.

## 2. Die Frage, eng gefasst

**Für die Fläche deiner Grundlinie** (`objekt/203`, Expertenmodus, Arbeitsbereich Heizung,
Erdgeschoss, gegen `f9c837e`): **welche der 16 Knöpfe der Werkzeugleiste sind in diesem Zustand
frei, und welche gesperrt?** Zahl gegen Zahl, nach deinem Verfahren aus AUF-68/AUF-71.

Dann, und nur dann: **nimm einen Knopf, der nachweislich frei ist, und wechsle darauf.**
- **Geht es** → Erklärung (A), und dein Bericht war eine Messgrenze, mehr nicht. **Das ist ein
  vollwertiges Ergebnis, kein Nullergebnis.**
- **Geht es nicht** → Erklärung (B). Dann brauche ich: **welcher Knopf**, **was passiert
  stattdessen** (nichts? Zustand ändert sich, Anzeige nicht?), und **eine Konsolenmeldung oder ihr
  belegtes Fehlen** — dein Markerverfahren von heute hat gezeigt, dass du echte Nullbefunde von
  Erfassungsdefekten unterscheiden kannst.

## 3. Zum Zeichen-Risiko

**Das ist ein berechtigter Einwand, und du sollst ihn nicht überfahren.** Zwei Wege, beide ohne
Strich auf der Fläche: der Wechsel wird an `werkzeugZustand`/der Leiste **abgelesen**, ohne den
Canvas zu berühren — und falls doch etwas entsteht, ist `objekt/203` ein **Prüf-Objekt**, kein
Kundendokument, und **nichts wird gespeichert**. **Speichere nicht.**

## 4. Ausdrücklich

- **Nichts reparieren**, auch nicht bei (B). Der Posten wird geschrieben, nicht gebaut.
- **Fällt (A) heraus, ist der Auftrag in zehn Minuten fertig** — dann melde das kurz und ohne
  Ausschmückung. **Ein „alles in Ordnung" mit Zahlen ist mir mehr wert als ein gesuchter Befund.**
