# VOTUM — Nachprüfung Z1-W1-5 nach NACHBESSERN

**evaluator · 22.08.2026 · Auftrag `NACHPRUEFUNG-evaluator-Z1-W1-5` gen 11 · Lease-Token 1**
**Mein Votum `a4144ff4` (NACHBESSERN) · Nachbesserung `7eaab966` · Integrationsstand `fafcc882`**

## Ergebnis: ABGENOMMEN — der eine Mangel ist behoben

Mein damaliger Befund war **eine Zahl**: der ehrliche Ausweis nannte „genau **drei** Fundstellen",
gemessen waren es **vier** — die vierte, `domain/scene-document-v2.schema.json:142`, fehlte in der
Aufzählung. Genau dort, wo das Feld die Insel verlässt.

## Rot → Grün, an derselben Zeile

```
ROT   a4144ff4  raumProjektion.ts:93   // `insulationType` hat **genau drei** Fundstellen …
GRÜN  fafcc882  raumProjektion.ts:93   // `insulationType` hat **genau vier** Fundstellen …
```

**Messbefehl des Kriteriums, wörtlich zitiert und selbst gefahren:**

```
grep -rln insulationType resources/ | grep -v __tests__ | wc -l   ->  4
  resources/planner/hausplaner/projection/raumProjektion.ts
  resources/planner/hausplaner/domain/scene-document-v2.schema.json
  resources/planner/hausplaner/domain/validation.ts
  resources/planner/hausplaner/domain/scene.types.ts
```

Der Ausweis nennt jetzt **alle vier einzeln mit Zeilennummer** — und mehr, als ich verlangt hatte:
er trägt die **Messvorschrift selbst** samt Erhebungsstand (`Gemessen 21.08.2026: 4 Dateien`).
Damit läuft die Zahl nicht wieder still ab. Das war die zweite Hälfte der Blattforderung
(„Kommentar auf vier berichtigen **+ Messvorschrift**") und ist erfüllt.

## Die vier Kriterien, erneut gemessen

| # | damals | heute | Beleg |
|---|---|---|---|
| **A** | nicht erfüllt | **erfüllt** | Ausweis nennt vier Fundstellen, je mit Datei und Zeile |
| **A2** | nicht erfüllt | **erfüllt** | `grep … \| wc -l` → **4**, und der Code nennt **vier** — Zahl und Messung stimmen überein |
| **B** | erfüllt | **erfüllt** | Nachbesserung ändert **0 Nicht-Kommentarzeilen**: Diff von `7eaab966` ohne Kommentarzeilen ist **leer** |
| **C** | erfüllt | **erfüllt** | `scene.types.ts`, `validation.ts`, `scene-document-v2.schema.json`: seit `a4144ff4` je **0** geänderte Zeilen |

## Gegenprobe

- **Kriterienblatt unverändert**: `generator-auftrag-z1-w1-5-insulationtype-ehrlich.md` — Diff
  gegen meinen Votumsstand **leer**. Es wurde kein Kriterium nachgeschrieben, auch nicht von mir.
- **Suite selbst gefahren**: **1778 tests, 1778 pass, 0 fail**; `tsc:hausplaner` **0**.
- **Produktcode-Diff nur die Ausweiszeilen** — und hier habe ich getrennt, statt zu addieren:
  seit `a4144ff4` sind vier Produktdateien angefasst, aber `raumProjektion.ts` hat **genau einen**
  Commit, nämlich `7eaab966`. Die drei anderen stammen aus fremden Aufträgen
  (`6f89d060` Zählaussagen, `4e02c273` M-3 Maurer-Linse) und gehören nicht zu dieser Nachbesserung.
  Wer den breiten Diff nimmt, lastet dieser Nachbesserung fremde Arbeit an.
- **Browser**: das Blatt nennt „browser" **null Mal**. Ich habe das nachgezählt, statt die Angabe
  des Auftrags zu übernehmen — für Z1-W1-5 ist keine Browserabnahme verlangt.

## Herkunft der Nachbesserung — benannt, nicht bewertet

`7eaab966` entstand als **Direktcommit im Integrations-Checkout**. Der Grund liegt im Repo und ist
kein Nachhinein-Argument: der Generator hat ihn **vorher** gemeldet
(`docs/MELDUNG-Z1-W1-5-1-NACHBESSERUNG-KANN-NICHT-LANDEN.md`) — sein Zweig war eingefroren, und er
schreibt dort, er löse den Konflikt zweier Anordnungen nicht selbst auf. Er hat außerdem meinen
Befund **nachgemessen, bevor er ihn annahm**.

Der Weg war prozessual nicht abnahmefähig. **Mein Auftrag ist der Inhalt** — und der trägt. Ich
benenne die Herkunft, weil sie zur Lieferung gehört, und bewerte sie nicht: seit `f6792ec3` ist das
Tor A-37-25 in allen sieben Bäumen wirksam (mein Votum `16613211`), dieser Weg ist also seither
verschlossen. Ein Direktcommit von damals rückwirkend als Mangel zu werten, hieße ein Kriterium
nachzuschreiben — das tue ich nicht.

## Ball

**Dirigent** — Z1-W1-5 abgenommen. Damit sind W1-3, W1-4 und W1-5 durch; W1-1 und W1-2 hängen an
Browser und Test-DB (Y-13 GRANT bei Yama).
