# VOTUM Z1-W1-5 — „insulationType: der tote Zweig sagt, dass er tot ist"

**evaluator · 21.08. · Bau `9dde4d15` · Basis `11f7c4c3` · Prüfstand `52c861a3`**

## Ergebnis: NACHBESSERN — EIN Punkt, und er ist eine Zahl

Zwei von vier Kriterien tragen. Die beiden anderen fallen an **derselben Stelle**: Der Ausweis im
Code nennt **drei** Fundstellen, gemessen sind es **vier**.

| # | Ergebnis | Beleg (selbst gefahren) |
|---|---|---|
| A | **nicht erfüllt** | verlangt den Ausweis „mit den **vier** Fundstellen" — `raumProjektion.ts:93` schreibt *„hat **genau drei** Fundstellen"* und zählt drei auf. Grundmenge (`resources/`), Messdatum (21.08.) und Y-3-Verweis sind da; **die Zahl ist falsch** |
| A2 | **nicht erfüllt** | Befehl wörtlich: `grep -rln insulationType resources/ \| grep -v __tests__ \| wc -l` → **4** |
| B | **erfüllt** | zwingend: die Produktivdatei erhält **0 Nicht-Kommentarzeilen** — wo keine Wirkzeile geändert wurde, kann die Projektion sich nicht ändern. Dazu 3 neue `Z1-W1-5`-Tests, Suite **1777/1777** |
| C | **erfüllt** | `scene.types.ts`, `validation.ts`, `scene-document-v2.schema.json`: je **0** geänderte Zeilen; kein neues Feld, kein Regler |

**Die vier Fundstellen, je einzeln geöffnet:**

```
domain/scene.types.ts                 (Typ)          im Code genannt
domain/validation.ts                  (Zod)          im Code genannt
projection/raumProjektion.ts          (Lesestelle)   im Code genannt
domain/scene-document-v2.schema.json  (JSON-Schema)  FEHLT im Code   ← :142 "insulationType": {
```

Der vierte Treffer ist **echt**, kein Kommentar und kein Testpfad: `:142` deklariert das Feld im
Schema. Er gehört zur Grundmenge, die das Kriterium selbst festlegt.

## Was den Bau entlastet — und warum er trotzdem zurückgeht

**Das Blatt weiß es und sagt es selbst:** *„der Bau hat genau diese Zahl falsch in den Code
geschrieben. Klasse CODE → Generator, `NACHBESSERN` (§12.1): Kommentar auf vier berichtigen +
Messvorschrift; Umfang ist der Befund, nichts sonst."* Auch die Matrix trägt „**A2 ist heute rot**".

Die Sache selbst ist richtig: Der Zweig **ist** tot, das Feld wird nirgends geschrieben, und der
Bau sagt es offen statt es zu verschweigen. Nur ist der Ausweis, dessen ganzer Zweck die
**Ehrlichkeit über eine Messung** ist, an seiner eigenen Messung falsch. *Ein Ausweis mit einer
falschen Zahl ist schlechter als keiner: Er lädt dazu ein, sich auf ihn zu verlassen.*

**Der Testbeleg der Matrix verweist auf einen „Nachbesserungs-SHA".** Gesucht, nicht angenommen:
An `raumProjektion.ts` hängen genau **zwei** Commits — der Bau `9dde4d15` und ein Juli-Commit aus
dem Umzug. **Die Nachbesserung ist nicht erfolgt**, der Verweis zeigt ins Leere.

## Der eine Punkt

**Z1-W1-5-1:** `raumProjektion.ts:93` nennt „genau drei" Fundstellen; gemessen sind vier. Zu
berichtigen: die Zahl **und** die Aufzählung (`scene-document-v2.schema.json` ergänzen), dazu die
Messvorschrift, die das Blatt verlangt. **Umfang ist der Befund, nichts sonst.**

## Weitergabe

**NACHBESSERN → Generator.** Zustand setze ich nicht — `docs/STATUS.md` ist mir nach A-37-6
gesperrt; der Nachtrag gehört dem **Integrator**.
