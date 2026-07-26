# ⇒ GENERATOR-AUFTRAG AUF-72 — Die Bühne ist höher als das Fenster

**Vom:** Planner · **26.07.2026, 07:55** · **Spur A** — es geht darum, welcher Teil der
Zeichenfläche den Nutzer überhaupt erreicht. **Heimat-App:** `ticket`.
**Anlass:** **Rückgabe des Generators aus AUF-62.** Er hat den Fehler gefunden, ihn ausdrücklich
**nicht** ausgeglichen und als eigenen Posten zurückgegeben. **Das war richtig** — und dieser
Auftrag existiert, weil er es so gemacht hat.

**Vorher gelesen:** HEAD `330ef00` · `git log -6` · Ledger „GENERATOR-BERICHT AUF-62" §6 ·
`app/HausplanerApp.tsx:1058` (die Zeile), `:705`, `:1064`, `:1429`, `:1440` (die vier Leser) ·
`app/dashboard/pan.ts` · **eigene Browser-Messung, 26.07.**

---

## 1. Zwei unabhängige Messungen, ein Befund

**Der Generator, 1440 × 900:**

```
Bühnenhöhe  804 px   ·  oben y=323  ·  unten y=1127  ⇒  227 px unter dem Fenster
sichtbar    72 %
```

**Ich, 1440 × 813, `studio?fixture=decke-treppe`, Expertenmodus:**

```
Bühne       oben y=369  ·  unten y=1086  ·  Höhe 717 px  ⇒  273 px unter dem Fenster
sichtbar    62 %
Seite scrollt 859 gegen 813 clientHeight — also 46 px Spielraum für 273 px Überstand
```

**Der Rest ist nicht wegzuscrollen.** Bei mir sind **38 %** der Zeichenfläche unerreichbar.
Je kürzer das Fenster, desto schlimmer — und Laptops sind kürzer als 900 px.

## 2. Die Ursache — eine Zahl, die einmal gestimmt hat

```ts
app/HausplanerApp.tsx:1058
const hoehe = typeof window !== 'undefined' ? window.innerHeight - 96 : 700;
```

**Die 96 stammt aus einer Zeit mit einer Leiste über der Bühne.** Seither sind dazugekommen: der
Arbeitsbereich-Wähler (AUF-34), die Werkzeugzeile, die Optionszeile. **Gemessen stehen heute
323–369 px über der Bühne, nicht 96.**

**Vier Stellen lesen `hoehe`:** `:705` (Einpassen), `:1064` (`weltHoehe`), `:1429` (Bühnenhöhe),
`:1440` (`panAus`). **Alle vier sind mitbetroffen — und alle vier liegen in derselben Datei.**
Das ist die gute Nachricht: **es gibt schon eine Wahrheit, sie ist nur falsch berechnet.**

**Was das für AUF-62 bedeutet:** Das Einpassen zentriert **korrekt in der Bühne** — K3 war gegen die
Bühne gerechnet und ist erfüllt. Der eingepasste Grundriss wirkt trotzdem unten angeschnitten, weil
die Bühne selbst über den Rand hinausragt. **AUF-62 ist nicht falsch; es ist das erste Werkzeug,
das den Bestandsfehler sichtbar macht.**

## 3. Was gebaut wird — entschieden

**Die Bühnenhöhe kommt aus dem Platz, den sie wirklich hat, nicht aus einer Subtraktion.**

Ein Maßband statt einer Schätzung: die Höhe wird an **dem Element gemessen, das die Bühne trägt**.
Damit stimmt sie automatisch, wenn oben eine Zeile dazukommt oder wegfällt — **und genau das ist in
den letzten Tagen dreimal passiert** (AUF-34, AUF-68, AUF-70). Jede dieser Änderungen hätte die
Konstante erneut verstellt, ohne dass es jemand gemerkt hätte.

**Die Messung muss auch auf Änderungen reagieren, die kein Fenster-Ereignis auslösen.** Ein
Fenster-Zuhörer allein reicht nicht: wenn eine Zeile über der Bühne erscheint, ändert sich das
Fenster nicht. **Ein Beobachter am tragenden Element deckt beide Fälle; ein Resize-Zuhörer deckt
einen.**

**Die Ersatzhöhe ohne DOM bleibt 700** — unverändert, damit der Testlauf ohne Fenster weiterläuft.

## 4. Die Kanten

1. **Erste Darstellung.** Beim ersten Rendern ist die gemessene Höhe oft **0**. **Eine Bühne mit
   Höhe 0 ist ein leerer Bildschirm** — dann gilt die Ersatzhöhe, nicht die 0.
2. **`panAus` und die Standardlage.** `standardPan` rechnet `y = hoehe − 80`. Ändert sich die Höhe,
   **soll die Standardlage folgen** — `pan.ts` sagt das selbst und startet deshalb mit `null`.
   **Ein vom Nutzer gesetzter Verschub darf dabei nicht zurückgesetzt werden.** Das ist die Kante,
   an der ein „Neuberechnen" die Arbeit des Nutzers wegwirft.
3. **Split.** Die Breite ist bereits richtig (`stageBreite`, AUF-62). **Die Höhe ist in beiden Modi
   dieselbe** — hier ändert sich nichts, und der Test hält fest, dass es so bleibt.
4. **Kein Flackern.** Die Messung darf keine Endlosschleife auslösen (messen ⇒ Zustand ⇒ Layout ⇒
   messen). Wenn sich das nicht sauber lösen lässt: **melden**, nicht mit einer Verzögerung zukleben.

## 5. Was **nicht** gebaut wird

- **Keine zweite Wahrheit über die Bühnenhöhe.** Nach diesem Posten gibt es genau **eine** Stelle,
  die sie bestimmt — so wie heute, nur richtig. *Der Generator hat in seiner Rückgabe geschrieben:
  „Zwei Wahrheiten über die Bühnenhöhe wären schlimmer als eine zu große Bühne." Das gilt.*
- **Kein Umbau des Seitenaufbaus.** Die Zeilen über der Bühne bleiben, wie sie sind, in Zahl und
  Reihenfolge. Wer bei der Gelegenheit Platz spart, baut zwei Posten in einem.
- **Kein Scrollen als Lösung.** Eine Zeichenfläche, die man erst scrollen muss, ist keine
  Zeichenfläche.
- **Kein Anfassen von `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types`** — K4.

## 6. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt** — null Zeilen.
3. **Die Konstante ist weg:** `grep -c "innerHeight - 96"` = **0**, und `grep` belegt, dass keine
   andere feste Zahl an ihre Stelle getreten ist.
4. **Die Bühne passt ins Fenster.** Sichtprobe im iframe fester Höhe, **drei** Formate:
   **1440 × 900 · 1440 × 813 · 1024 × 768**. Kriterium je Format: **Unterkante der Bühne ≤ Unterkante
   des Fensters**, Zahl nennen. *(Heute gemessen: 227 px bzw. 273 px darüber hinaus.)*
5. **Nichts wird abgeschnitten und nichts wird leer:** die Bühnenhöhe ist in allen drei Formaten
   **> 0** und größer als ein benannter Mindestwert.
6. **Der Verschub des Nutzers überlebt:** Test — Verschub setzen, Höhe ändern, Verschub ist
   **unverändert**. **Das ist das Kriterium, an dem ein „einfach neu berechnen" auffliegt.**
7. **Die Standardlage folgt weiterhin:** ohne eigenen Verschub ändert sich die Standardlage mit der
   Höhe — testverriegelt, wie `pan.ts` es beschreibt.
8. **Nach dem Einpassen ist alles sichtbar — im Fenster, nicht nur in der Bühne.** Sichtprobe:
   herauszoomen, „Ansicht einpassen", **der ganze Grundriss steht im sichtbaren Bereich**.
   *Das ist der Beleg, dass dieser Posten leistet, was AUF-62 versprochen hat.*
9. **Höhe 0 führt nicht zur leeren Bühne:** Test mit gemessener Höhe 0 ⇒ Ersatzhöhe greift.
10. **Mutations-Gegenbeweis:** die feste Subtraktion wieder einsetzen ⇒ mindestens ein Test rot.
    Zahl nennen.
11. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
12. **Klassifikation: `sichtbar`** — im wörtlichsten Sinn: 38 % der Fläche waren nicht erreichbar.

## 7. Was zurückgegeben wird

- **Lässt sich die Höhe nicht ohne Flackern messen:** melden, mit der Beobachtung. Eine Verzögerung
  als Pflaster ist keine Lösung, sondern ein Fehler mit Verspätung.
- **Zeigt sich, dass die Zeilen über der Bühne bei 1024 × 768 so viel Platz nehmen, dass kaum Bühne
  übrig bleibt:** benennen, mit der Zahl. **Dann ist das eine Willensfrage für Yama** — welche Zeile
  weicht — und keine, die im Vorbeigehen entschieden wird.
