# Meldung — die Nachbesserung Z1-W1-5-1 ist mein Auftrag, aber mein Zweig ist eingefroren

> **Zwei Anordnungen stehen gegeneinander, und ich löse das nicht selbst auf.**

## 1 · Der Befund trifft, nachgemessen statt übernommen

Das Votum `a4144ff4` gibt Z1-W1-5 NACHBESSERN mit **einem** Punkt und reicht ihn an den Generator
weiter. Ich habe ihn nachgemessen, bevor ich ihn annehme:

```text
grep -rln insulationType resources/   (ohne __tests__)   ->  VIER
  domain/validation.ts · domain/scene.types.ts
  projection/raumProjektion.ts · domain/scene-document-v2.schema.json
```

Der Ausweis im Code (`projection/raumProjektion.ts`, Kommentarblock vor `bauteil_typ`) schreibt
**„genau drei Fundstellen"** und zählt drei auf. **Die vierte fehlt in der Aufzählung** —
`domain/scene-document-v2.schema.json`. Der Evaluator hat recht, und seine Begründung trägt: *ein
Ausweis, dessen Zweck die Ehrlichkeit über eine Messung ist, lädt mit falscher Zahl dazu ein, sich
auf ihn zu verlassen.*

**Die Sache selbst bleibt richtig:** der Zweig ist tot, das Feld wird nirgends geschrieben.

## 2 · Warum ich ihn nicht einfach baue

| Anordnung | Folge |
|---|---|
| **§12.1** — NACHBESSERN hat Vorrang, Weitergabe an den Generator | ich soll ihn bauen |
| **`4851ec6c`** — *„der Zweig wird bis zur Y-9-Entscheidung NICHT transportiert"* | was ich hier baue, erreicht niemanden |

**Ein Bau in meinem Zweig wäre eine Korrektur, die im Aufbewahrungsort liegen bleibt** — und
genau die Doppelarbeit, die heute schon einmal 99 Sekunden gekostet hat. *Deshalb baue ich nicht
in eine Sackgasse, sondern lege die Messung so hin, dass der Bau woanders ohne Nachmessen möglich
ist.*

## 3 · Die Nachbesserung, fertig ausgemessen — Umfang ist der Befund und nichts sonst

**Datei:** `resources/planner/hausplaner/projection/raumProjektion.ts`, Kommentarblock unmittelbar
vor `bauteil_typ:`.

- **„genau drei" → „genau vier"**
- **Aufzählung ergänzen:** `domain/scene-document-v2.schema.json` (Felddeklaration im Schema, vom
  Evaluator einzeln geöffnet und als echt bestätigt — kein Kommentar, kein Testpfad)
- **Messvorschrift dazuschreiben**, wie das Blatt sie verlangt: `grep -rln insulationType resources/`
  ohne `__tests__`, Messdatum, damit die Zahl beim nächsten Mal nachprüfbar altert
- **Nicht anfassen:** Wirkzeilen, `scene.types.ts`, `validation.ts`, das Schema selbst — Kriterien B
  und C sind erfüllt und dürfen es bleiben

## 4 · Ball

| an wen | was |
|---|---|
| **Release-Prüfer** | entweder eine gezielte Überführung dieses einen Punktes aus meinem Zweig, oder die ausdrückliche Freigabe, dass die Generator-Instanz im gemeinsamen Checkout ihn baut |
| **Evaluator** | zur Kenntnis: der Punkt ist angenommen und nachgemessen, nicht bestritten |
| **Yama** | Y-9 löst die Einfrierung auf; bis dahin ist dies der Engpass, nicht der Befund |
