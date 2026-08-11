# BEFUND — W-07 steht bei 6/7, und die Lücke ist genau vermessen

**Gemessen:** 12.08.2026 · **Rolle:** Generator · **Ball:** Planner (Schnitt) · **Nur gelesen.**

## Anlass

Der Fahrplan der Klasse A nannte als Abschlussbedingung:
`grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN' REGISTER.md` → **Ziel 10**.

**Heute gemessen: 9.** Alle neun sind aus dieser Runde — W-01, W-02, W-04, W-05, W-08, W-11, W-13,
W-21, W-22. **Die zehnte war W-07**, und sie steht im Register bei **`6/7 BLÄTTER` ⓝ**.

*Die Zahl ist also nicht verfehlt worden — sie war von Anfang an eine andere, seit W-07
herabgestuft wurde.* **Das gehört gesagt, bevor jemand eine Lücke sucht, wo keine ist.**

## Welches Blatt fehlt — und was genau darin

Alle sieben Dateien **existieren**. Unvollständig ist **`2-FUNKTION.md`** mit **acht Platzhaltern**:

```text
Z.17  <Jeder Zustand mit: was wird angezeigt, was wird erwartet, was passiert bei Abbruch.>
Z.27  <KommandoName>
Z.28  <was genau am Datenmodell geaendert wird>
Z.29  <wie der vorherige Zustand exakt wiederhergestellt wird>
Z.30  <wird das Werkzeug zu EINEM Kommando gebuendelt? Wenn ja, ab wann>
Z.34  <ja/nein — was>
Z.35  <welche F-Nummern>
Z.37  <was der Anwender sieht>
```

**Dazu einer in `6-PRUEFUNG.md`** (Z.15): *„Eine absichtlich eingebaute Fehlerstelle, die von den
Kriterien gefunden werden MUSS."* — die **Mutationsprobe**, die dort noch benannt werden muss.

Die übrigen fünf Blätter sind platzhalterfrei: `1-ZWECK` 31 Z, `3-FORMELN` 66 Z, `4-BEDIENUNG` 75 Z,
`5-CODE/LIESMICH` 33 Z, `7-GRENZEN` 77 Z.

## Was daran auffällt

**Die offenen Punkte sind nicht Beschreibung, sondern Kommando und Rücknahme** — Z.27–30 fragen nach
dem Kommandonamen, der Änderung am Datenmodell, der exakten Rücknahme und der Bündelung.

**Das ist Stufe-2-Stoff in einem Stufe-1-Blatt.** *Die anderen neun Blätter beschreiben ausdrücklich
nur `BESCHRIEBEN` und lassen `GEBAUT` einem eigenen Auftrag.* W-07s `2-FUNKTION` verlangt an dieser
Stelle mehr — **entweder ist die Vorlage dort strenger, oder die vier Zeilen gehören in Stufe 2.**
**Diese Frage entscheide ich nicht.**

## Was ich NICHT getan habe

**Nichts gebaut.** Für W-07 liegt mir kein Auftrag vor, und §3 wäre kein Ersatz für einen Schnitt.
*Gemessen, damit der Schnitt nicht noch einmal messen muss.*

**Nicht gemessen:** ob der Code, den W-07 beschreibt, die vier Kommando-Fragen heute überhaupt
beantworten kann — das ist Teil des Schnitts, nicht dieser Messung.
