# ENTSCHEIDUNG — die 7 Dissens-Fälle aus AUF-50-S1 (Planner, 30.07.2026, 20:25)

**Ball vom Evaluator, seit dem S1-Prüfstand offen.** Sein Befund: die Werkzeug-Landkarte
markiert 7 Verträge als `ohne-modell`, obwohl deren Vertrag `model.revision.increment` als
Seiteneffekt führt. Er hat ausdrücklich **kein Rot** daraus gemacht — *„ein Befund über die
Landkarte, nicht gegen sie"* — und dem Planner zwei Wege vorgelegt: **A)** den Dissens
kennzeichnen, **B)** die Verträge korrigieren.

**Ich wähle weder A noch B pauschal.** Beide Wege setzen voraus, dass man schon weiß, wer irrt.
Das war nicht gemessen. Also habe ich zuerst gemessen.

## Die Gegenprobe, die die Frage entscheidet

Die Landkarte nennt `kuechenplanung` und `elektroplanung` **ausdrücklich dieselbe Klasse** —
wörtlich in der Begründung: *„Ein Arbeitsbereich, kein Bauteil — dieselbe Klasse wie
`kuechenplanung`."* Im Vertrag stehen sie aber verschieden:

```text
kuechenplanung   familie=create   increment=True    ergebnisse=kitchenPlanId
elektroplanung   familie=domain   increment=False   ergebnisse=electricalPlanId
```

**Zwei Werkzeuge, von der Landkarte als gleiche Klasse benannt, mit strukturgleichem Ergebnis
(`kitchenPlanId` / `electricalPlanId`) — aber unterschiedlicher Familie und unterschiedlichem
Seiteneffekt.** *Wäre die Landkarte die fehlerhafte Seite, müsste `elektroplanung` denselben
Widerspruch zeigen. Es tut es nicht.* **Der Vertrag ist an dieser Stelle uneinheitlich, nicht
die Landkarte.**

Vollständig gemessen: von **42** `ohne-modell`-Marken tragen genau **7** ein `revision.increment`,
**35** nicht, und **keine einzige** Marke steht ohne Vertragseintrag da. Die Landkarte ist also
im Ganzen konsistent; die sieben sind Ausreißer, keine Systematik.

## Drei Töpfe statt einer Regel

| Topf | Werkzeuge | Vertrag | Entscheidung |
|---|---|---|---|
| **1 — belegt uneinheitlich** | `kuechenplanung` | `familie: create`, aber `ergebnisse: kitchenPlanId` (**nicht** `createdObjectIds`) | **Vertrag korrigieren** auf `familie: domain`, `increment` streichen — angeglichen an `elektroplanung`. Die Landkarte behält recht. |
| **2 — fachlich tragend** | `kopieren` · `material-aufnehmen` · `thermische-huelle` · `u-wert` | `kopieren` gibt `clipboardPayload`, `u-wert` gibt `uValue` — **keines gibt Objekt-IDs zurück** | **Vertrag korrigieren**, `increment` streichen. *Kopieren legt nichts an, erst das Einfügen; ein U-Wert ist eine Rechnung.* Der Landkarten-Grund trägt. |
| **3 — offen, nicht am Papier entscheidbar** | `aufriss` · `schnitt` | `familie: create` **und** `ergebnisse: ['createdObjectIds']` — unmissverständlich | **Weder noch — messen.** Eine Ansichts- oder Schnittdefinition *kann* legitim als Knoten im Modell liegen. Braucht den Blick in `ElevationCommand` / den Schnittbefehl. |

**Warum die Trennung wichtig ist:** Topf 3 ist die einzige Stelle, an der die Landkarte
tatsächlich falsch sein *könnte*. Sie pauschal mit den anderen fünf zu behandeln, hieße eine
gemessene Sache und eine ungemessene mit derselben Sicherheit zu behaupten — genau der Fehler,
den ich heute mehrfach gemacht habe.

## Folge für die Zahl 21

Der Evaluator warnt zu Recht: **`fehlt: 21` ist der Bauvorrat für Stufe 3**, und die Zahl ist
nur „unter dieser Lesart" belastbar. Nach dieser Entscheidung gilt:

- **Töpfe 1 und 2 (fünf Werkzeuge) berühren die 21 nicht.** Sie bleiben `ohne-modell`; korrigiert
  wird der Vertrag, nicht die Marke.
- **Topf 3 (zwei Werkzeuge) ist der einzige Rest.** Ergibt die Messung, dass `aufriss` und
  `schnitt` Knoten anlegen, wandern sie von `ohne-modell` nach `deckt` oder `fehlt` — im
  ungünstigsten Fall wird der Vorrat **21 → 23**.
- **`fehlt: 21` ist damit ab jetzt eine Zahl mit benannter Unschärfe von höchstens zwei**, statt
  einer Zahl mit unbekannter Unschärfe. *Das ist der eigentliche Gewinn dieser Runde.*

## Was daraus an Arbeit folgt — beides NACH AUF-48 (Yamas Fokus)

- **AUF-50-D1** *(klein, Spur A — der Vertrag ist die Wahrheitsquelle für Stufe 3)*: die fünf
  Verträge aus Topf 1 und 2 angleichen. Ein Kriterium: nach der Änderung tragen von 42
  `ohne-modell`-Marken **zwei** ein `revision.increment` statt sieben — und die zwei sind genau
  `aufriss` und `schnitt`.
- **AUF-50-D2** *(Messauftrag)*: legen `ElevationCommand` und der Schnittbefehl Knoten an?
  Ergebnis entscheidet Topf 3 und die Zahl 21.

**Kein Produktivcode aus dieser Entscheidung** — Cowork/Planner schreibt nur `docs/`.
Ballbesitz danach: **Generator** (D1, D2 nach AUF-48) · **Yama** (Reihenfolge, falls er sie
anders will).
