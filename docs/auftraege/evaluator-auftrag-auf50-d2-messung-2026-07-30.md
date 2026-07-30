# AUF-50-D2 — Messauftrag an den EVALUATOR: legen `aufriss` und `schnitt` Knoten an?

**Geschnitten 30.07. 20:38, weil Yama gefragt hat, ob Generator und Evaluator Arbeit haben.**
*Gemessen: der Generator hat drei Blätter liegen (S4a 225 Z. · S4b 133 Z. · S4c 93 Z.), der
Evaluator hat **alle neun** Produktivcommits des Tages quittiert — `262de870` fünf Minuten nach
dem Commit. **Er hätte gerade nichts.** Das ist der Grund für dieses Blatt.*

## Korrektur meiner eigenen Zuordnung

In `entscheidung-auf50-dissens-2026-07-30.md` habe ich geschrieben: *„Ballbesitz danach:
**Generator** (D1, D2 nach AUF-48)."* **Das war falsch für D2.** D2 ändert nichts — es ist eine
reine Tatsachenfrage am bestehenden Code. **Eine Messung gehört dem Evaluator**, und sie kann
**sofort** laufen, parallel zu AUF-48, ohne irgendetwas zu blockieren.

## Die Frage

Meine Dissens-Entscheidung hat sieben Fälle in drei Töpfe sortiert. **Topf 3 blieb offen, weil er
am Papier nicht entscheidbar ist:**

```text
aufriss   familie=create   ergebnisse=['createdObjectIds']   seiteneffekt model.revision.increment
schnitt   familie=create   ergebnisse=['createdObjectIds']   seiteneffekt model.revision.increment
```

Die Landkarte markiert beide als **`ohne-modell`** mit der Begründung *„Eine Schnittansicht ist
eine Darstellung, kein Knoten."* **Der Vertrag sagt das Gegenteil, und zwar unmissverständlich** —
`createdObjectIds` ist keine auslegbare Formulierung.

**Eine Ansichts- oder Schnittdefinition *kann* legitim als Knoten im Modell liegen** (mit
Schnittebene, Blickrichtung, Massstab). Dann hätte der Vertrag recht und die Landkarte irrte.
*Ich weiss es nicht, und ich will es nicht raten.*

## Was zu messen ist

1. **`ElevationCommand` und der Schnittbefehl aufsuchen** — der Vertrag nennt
   `services.architektur.execute('elevation', input)` als Dienstmethode.
2. **Legen sie einen Knoten an?** Konkret: erreicht die Ausführung ein `ADD_NODE` (oder einen
   gleichwertigen Befehl), und steht danach ein Eintrag im Modell?
3. **Wenn ja: welchen `objectType`?** *Vorsicht — `ObjectNode.objectType` ist eine persistierte
   Schema-Wahrheit. Fehlt der Typ, ist das Ergebnis `fehlt`, nicht `deckt`.*
4. **Gegenprobe an einem Fall, bei dem die Antwort feststeht:** `fenster` ist `deckt/ADD_NODE`.
   Läuft die Messung dort ins Leere, misst sie nicht, was sie zu messen behauptet.

## Was das Ergebnis entscheidet

```text
legen Knoten an       ->  Marke wandert von `ohne-modell` nach `deckt` oder `fehlt`
                          Bauvorrat fuer Stufe 3:  fehlt 21  ->  bis zu 23
legen keine an        ->  die Landkarte hat recht, die zwei Vertraege gehoeren zu Topf 2
                          (increment streichen), Bauvorrat bleibt 21
```

**`fehlt: 21` ist der Bauvorrat, gegen den Stufe 3 geplant wird.** Nach dieser Messung ist die
Zahl entweder bestätigt oder korrigiert — heute ist sie eine Zahl mit einer Unschärfe von zwei.

## Auflagen

- **Kein Produktivcode.** Wird beim Messen klar, dass etwas zu ändern ist, geht das als D1/D3 an
  den Generator zurück.
- **Artefakt statt Behauptung:** der Pfad zum Befehl, die Fundstelle, die Rohausgabe. *„Ich habe
  nachgesehen, sie legen keine an" ist keine Messung.*
- **Kein Merge, kein Push** — wie immer.
