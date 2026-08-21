# Z1-W1-5 · insulationType: der tote Zweig sagt, dass er tot ist

```yaml
zustand: ENTWURF              # Fassung 2 — revidiert 21.08. nach DoR-Verweigerung §147
basis_sha: 11f7c4c3           # Zeiger am HEAD 87dbbe77 NEU gemessen
herkunft: Befund K-1 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.5
entscheidung_offen: Y-3 (Dämmungs-Regler + Fachbedeutung) — NICHT Teil dieses Auftrags
gebaut: 9dde4d15 (21.08. 14:15) — VOR BEREIT gebaut, UND mit falscher Zahl im Code → NACHBESSERUNG (Klasse CODE)
baut: generator (Agent frontend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md (Zeile 100)
```

## Ziel
`projection/raumProjektion.ts` weist an der Lesestelle ehrlich aus, dass `insulationType` heute
von nichts gesetzt wird — nach dem Muster „ehrlich null" (`decke`/`boden` ebd.). **Verhalten
unverändert.** Und: **eine Zahl, die dauerhaft im Quelltext steht, muss stimmen** (§147).

## Ist-Beleg (HEAD `87dbbe77`, 21.08., Grundmenge `resources/`, ohne Tests)
`grep -rln insulationType resources/` → **vier** Dateien, **drei Deklarationsorte + eine Lesestelle**:
`domain/scene.types.ts:109` (Typ) · `domain/validation.ts:46` (Zod) ·
`domain/scene-document-v2.schema.json:142` (JSON-Schema — **fehlte in Fassung 1 und im Bau**) ·
`projection/raumProjektion.ts:102` (Lesestelle, nach dem Kommentar-Einschub verschoben).
Schreibstellen (Zuweisungsmuster): **null**. Einziger `construction`-Regler
`EigenschaftenPanel.tsx:324` schreibt nur `materialId`. Hinweis §147: `raumProjektion.ts` hat
keinen Ladeweg (33er-Liste) — Kriterium B ist eine Aussage auf Testebene.

## Scope · Dateien
- `projection/raumProjektion.ts` (Kommentar an der Lesestelle) · `__tests__/raumProjektion.test.ts`
  (Charakterisierung).
- **Nachbesserung am Bau `9dde4d15`:** der Kommentar (`:93-96`) behauptet „genau drei Fundstellen"
  — richtig sind **vier** (JSON-Schema `:142` fehlt). Korrigieren, und die Messvorschrift mit
  hinschreiben (Grundmenge, Datum), damit der Satz nachprüfbar bleibt statt brüchig.
**Nicht-Ziele:** KEIN Regler, kein neues Feld (Y-3); Typ, Zod **und JSON-Schema** bleiben (alle drei
Deklarationsorte — das Feld ist nicht falsch, nur unverdrahtet); der Ternary bleibt (Zweig nicht
entfernt, Haltung wie bei `decke`/`boden`).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Die Lesestelle benennt am Code, dass das Feld nirgends gesetzt wird — **mit den vier Fundstellen, Grundmenge und Messdatum**, Verweis Y-3 | Ausweis | `9dde4d15` | Ausweis an `:91` mit 3-Fundstellen-Messung + Y-3-Verweis |
| A2 (neu, aus §147): die im Code genannte Zahl stimmt mit `grep -rln insulationType resources/ \| grep -v __tests__ \| wc -l` überein | Richtigkeit | Nachbesserungs-SHA | grep-Rohausgabe |
| B: Projektion liefert vorher = nachher (Testebene, kein Ladeweg) | Schutz | `9dde4d15` | `Z1-W1-5 A` (heute konstant `wand`) + `B` (Zweig feuert bei gesetztem Feld) |
| C: Kein neues Feld, kein Regler, Typ/Zod/JSON-Schema unverändert | Grenze | `9dde4d15` | Diff der Produktivdatei: nur Kommentarzeilen + unveränderte `bauteil_typ`-Zeile |

**P1-Kriterium A war vor dem Bau wirksam rot.** **A2 ist heute rot** (Code sagt drei, gemessen vier).

## Rückweg
Ein Commit, Kommentar + Test, zurückdrehbar.

## Abweichung, offen benannt
Bau vor BEREIT (DoR §147 NICHT ERTEILT wegen „der Zahl") — und der Bau hat genau diese Zahl
falsch in den Code geschrieben. Klasse **CODE → Generator, `NACHBESSERN`** (§12.1): Kommentar auf
vier berichtigen + Messvorschrift; Umfang ist der Befund, nichts sonst. Danach Neu-DoR → `BEREIT`
→ `CODE_FERTIG` → Abnahme.
