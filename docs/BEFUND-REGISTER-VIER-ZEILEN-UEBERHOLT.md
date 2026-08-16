# Vier Registerzeilen sagen LEER, im Bestand liegen sieben Blätter

> **Release-Prüfer, 16.08. ~19:2x.** Ausgelöst durch die Konfliktmeldung des Planners
> (`7e9d2566`, 19:10) — und mein eigener Transport hat den Zustand weitergetragen, den sie
> beschreibt.

## Der Befund

Alle 43 Registerzeilen gegen den Bestand geprüft, Reifegrad gegen tatsächlich vorhandene Blätter:

```
Registerzeilen geprüft                        43
Reifegrad passt NICHT zu den Blättern          4

  W-43   Register=LEER   Blätter=7   -> BESCHRIEBEN
  W-26   Register=LEER   Blätter=7   -> BESCHRIEBEN
  W-28   Register=LEER   Blätter=7   -> BESCHRIEBEN
  W-30   Register=LEER   Blätter=7   -> BESCHRIEBEN
```

Die Legende dieser Datei ist eindeutig: **`BESCHRIEBEN` (alle sieben)**. Es sind sieben. Der
Reifegrad ist überholt, nicht strittig.

## Wie es dazu kam — die Klasse des Tages zum sechsten Mal

```
Blätter je Stand:       W-43   W-26   W-28
  rolle/planner            0      0      0
  rolle/generator          7      7      7
  auto/…integration        7      7      7
  mein Stand (HEAD)        7      7      7
```

**Der Planner hat auf einem Zweig gemessen, auf dem die Blätter nicht liegen.** Und er hat es
sorgfältig getan — sein eigener Commit nennt drei unabhängige Wege:

> *„DREIFACH BELEGT, und der dritte Weg ist der wichtige: kein Verzeichnis unter dem erwarteten
> Namen; null Dateien bei einer Suche über den WERKZEUGNAMEN statt über den Pfad — abbund,
> dachschicht, entwaesserung, flachdach, je 0 Treffer in der ganzen Werkbank; und die Legende ist
> eindeutig."*

**Alle drei Wege waren richtig — für seinen Zweig.** Sie hatten denselben blinden Fleck, und keine
Verdreifachung der Methode findet ihn: **der Zweig war das Problem, nicht die Messung.** Das ist
dasselbe Muster wie heute beim Evaluator (sah seinen Ball nicht), beim Integrator (sah die Freigabe
nicht) und bei meinem eigenen `zweige.py` (maß gegen einen 151 Commits alten Bezug).

Er hat es um 19:10 selbst bemerkt und gemeldet, statt es zu überspielen — mit der richtigen
Richtung: *„SACHLICH GEWINNT SEINE SEITE… Meine Messung war zum Zeitpunkt richtig und ist es
seitdem nicht mehr."*

## Was mich daran trifft

**Erstens: mein Transport hat den überholten Stand verbreitet.** Der Merge lief konfliktfrei durch
— Git merged zeilenweise, und die Register-Zeilen kollidierten an meinem Stand nicht mehr. Genau
davor warnt der Planner: *„ohne ihn hätte mein LEER beim nächsten Merge vier fertige Ablesungen
STILL überschrieben."* Bei mir gab es den schützenden Konflikt nicht mehr, weil ich seine Fassung
eine Runde vorher schon übernommen hatte. **Der Schutz greift genau einmal; danach reist der Fehler
lautlos mit.**

**Zweitens: meine Bauvorrat-Messung von 17:5x steht auf dieser Zahl.** Ich hatte gemeldet
*„BESCHRIEBEN 37"* — deckungsgleich mit der Planner-Tafel, und beide zählten dieselben vier Zeilen
falsch. **Richtig sind es 41**, sobald die vier nachgezogen sind. Die Aussage darüber, wie viele
davon *baubereit* sind, ändert sich dadurch nicht (Bedingung 3 bleibt ungeführt), aber die
Ausgangsmenge war zu klein.

## Was zu tun ist — und von wem

**Nicht von mir.** Das Register ist Planner-Eigentum, und er hat ausdrücklich gesagt: *„Die
Entscheidung gehört ihm [dem Integrator]; ich nenne die Richtung und löse nicht auf."* Ich messe
und melde; ich schreibe keine fremden Registerzeilen.

**Der Handgriff ist klein**, und weil er klein ist, benenne ich ihn genau: die vier Zeilen tragen
`LEER`, sie gehören auf `BESCHRIEBEN`, Beleg ist je ein `find` über das Werkzeugverzeichnis mit
Ergebnis 7. Der Prüfbefehl danach:

```
für jede Registerzeile: Reifegrad gegen die Zahl der Blätter im zugehörigen Verzeichnis
Erwartung: 0 Abweichungen   (heute: 4)
```

**Dringlichkeit:** Es blockiert nichts, aber es verfälscht **Yamas Baufrage** — die Ausgangsmenge
für „was wird zuerst gebaut" ist heute um vier Zeilen zu klein, und vier fertige Ablesungen sehen
aus wie nie begonnene Arbeit. Das ist derselbe Schaden, den der Planner heute Nachmittag bei den
drei `LEER`-Zeilen selbst beschrieben hat, nur mit umgekehrtem Vorzeichen.

## Eine Beobachtung zur Methode, die über diesen Fall hinausgeht

Der Planner hat gegen seinen Fehler **drei** Prüfwege gefahren und ist trotzdem hineingelaufen.
Nicht weil die Wege schlecht waren, sondern weil sie **alle drei denselben Datenstand lasen**.
Redundanz in der Methode schützt nicht gegen einen Fehler in der Grundlage.

Der einzige Weg, der ihn gefunden hätte, ist der, den heute keiner der Wege enthielt: **dieselbe
Messung auf einem zweiten Zweig fahren.** Genau das leistet `scripts/zweiglage.py` seit heute für
Zustände — für Dateibestände gibt es das Gegenstück noch nicht.
