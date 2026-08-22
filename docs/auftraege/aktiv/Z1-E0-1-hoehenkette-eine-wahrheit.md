# Z1-E0-1 — Höhenkette = eine Wahrheit: drei Rechnungen werden eine, und eine davon ist tot

**ZIEL:** Genau **eine** Funktion rechnet Etagen-Elevation, Deckenoberkante und Traufhöhe. „Geschoss
anlegen" und die Dach-Traufhöhe lesen daraus.

```yaml
auftrag: "Z1-E0-1"
scheibe: "E0 — Hoehenkette = eine Wahrheit"
spur: A
art: "SSOT — drei Rechnungen werden eine. KEIN Modell, KEIN Schema, KEINE Bedienaenderung."
heimat_app: ticket
heimat_code: resources/planner/hausplaner
mess_sha: fd2575ce
konzept: "docs/konzept/etagenweiser-aufbau.md @ 8e4bb918 (Dirigent, 19:1x) — Scheibe E0, Luecke L1"
kennung_geprueft: "Z1-E0-1: docs/ 0 Treffer, git log --all --grep 0. Kennungsraum Z1-E* neu."
dor_beleg: "steht aus — plan-pruefer"
basis_sha: fd2575ce
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
zielreifegrad: "ABGENOMMEN (BROWSER)"
```

## Die Lücke L1, am Stand `fd2575ce` selbst nachgemessen

```
DREI Rechnungen fuer dieselbe Groesse — Elevation der naechsten Etage:

1  renderers/three-d/deckenMesh.ts:32-37   naechsteEtageElevationMm(level, decke)
     return Math.round(level.elevation + level.defaultWallHeight + deckeDickeMm)
     deckeDickeMm = decke ? decke.dickeMm : level.floorThickness      <- BERUECKSICHTIGT DIE DECKE
     AUFRUFSTELLEN MIT KLAMMER (ohne Definitionszeile):  0            <- SIE IST TOT

2  app/dashboard/Kopfrahmen.tsx:172         "Geschoss anlegen"
     elevation: oben.elevation + oben.defaultWallHeight + oben.<floorThickness>
     eigene Rechnung, kennt die Decke NICHT

3  geometry/geschossVorlage.ts:54           Duplizieren
     elevation: quelle.elevation + quelle.defaultWallHeight + quelle.floorThickness
     eigene Rechnung, kennt die Decke NICHT

geometry/hoehenkette.ts                     EXISTIERT NICHT
deckenOberkanteMm (deckenMesh.ts:10)        3 Aufrufstellen: szene.ts:456, :483, HausplanerApp.tsx:1008
```

> **Die Funktion, die es richtig macht, ruft niemand.** *Die beiden, die aufgerufen werden, rechnen
> ohne die Decke — daher der Bruch 2700 gegen 2740.* **Das ist keine Ungenauigkeit, sondern eine
> zweite Wahrheit über die Höhenlage jeder Etage.**

---

## Abnahmekriterien (aus dem Konzept; Messbefehle ergänzt, nichts abgeschwächt)

- **Z1-E0-1-a** · **GENAU EINE ERZEUGERFUNKTION, DREI AUFRUFER.**

  **Verlangt:** `geometry/hoehenkette.ts` nach GP-0 §3 ist die **einzige** Stelle, die die nächste
  Elevation erzeugt. `Kopfrahmen.tsx:172`, `geschossVorlage.ts:54` und `HausplanerApp.tsx:1008`
  lesen daraus.

  **Messbefehl:**
  ```
  grep -rnE '\belevation \+ .*defaultWallHeight' --include='*.ts' --include='*.tsx' \
    | grep -v '__tests__' | grep -v 'geometry/hoehenkette.ts:'      ->  0
  grep -rnE '\bnaechsteEtageElevationMm\(' … ohne Definitionszeile  ->  3
  ```

  **Heutiges (rotes) Ergebnis:** **drei** eigene Rechnungen (`deckenMesh.ts:37`,
  `Kopfrahmen.tsx:172`, `geschossVorlage.ts:54`), und die richtige hat **0 Aufrufer**.

  **Absage-Regel:** Eine neue Funktion **neben** den drei alten erfüllt (a) **nicht** — dann sind es
  vier. *Verlangt ist, dass die alten Stellen lesen, nicht dass eine vierte entsteht.*

- **Z1-E0-1-b** · **ROT-PROBE 2700 → 2740, IM BROWSER.**

  **Verlangt:** EG mit `floorThickness` 200 und einer Decke von 240 → „Geschoss anlegen" ergibt
  **2740**, nicht 2700. **Bildbeleg, beide Zahlen im Bericht.**

  **Heutiges (rotes) Ergebnis:** `Kopfrahmen.tsx:172` addiert `floorThickness` (200) → **2700**;
  die Decke (240) wird nicht gelesen.

  **Absage-Regel:** Ein Testlauf ohne Browser erfüllt (b) **nicht** — *der Bruch entsteht im
  Bedienweg „Geschoss anlegen", und dort ist er zu zeigen.*

- **Z1-E0-1-c** · **BESTAND BITGLEICH, WO KEINE DECKE MODELLIERT IST.**

  **Verlangt:** Referenzhaus-Fixture: **alle drei alten Werte bitgleich**, solange keine Decke
  existiert. *Die Vereinheitlichung darf nur dort etwas ändern, wo heute falsch gerechnet wird.*

  **Messbefehl:** Fixture vorher/nachher, Elevationen je Level zeichengleich.

  **Absage-Regel:** Eine Abweichung ohne Decke erfüllt (c) **nicht** — *dann ändert der Umbau mehr
  als die eine Sache, und niemand könnte sagen, welche Zahl warum wandert.*

- **Z1-E0-1-d** · **DIE LIEFERUNG IST GRÜN UND VOLLSTÄNDIG.**
  `tsc:hausplaner` → **0** · `test:hausplaner` → 0 fail · **Bündel gebaut und mitcommittet.**

- **Z1-E0-1-e** · **KEIN MODELL-, KEIN SCHEMA-DIFF.**

  **Messbefehl:**
  ```
  git diff <basis_sha>..<endstand_sha> -- domain/ commands/                 -> LEER
  git diff <basis_sha>..<endstand_sha> -- '*scene-document-v2.schema.json'  -> LEER
  ```
  **Absage-Regel:** `git diff` **ohne beide SHA** erfüllt (e) nicht — *ohne Referenz ist es nach dem
  Commit immer leer* (Halbsatz 1 der Spur-V-DoR, §421).

## Nicht-Ziele

- **Kein neues Feld, kein Schema.** E0 rechnet nur, es speichert nichts.
- **Keine Bedienänderung.** „Geschoss anlegen" bleibt derselbe Knopf am selben Ort.
- **Keine Bodenplatte.** Die Höhenkette lernt sie erst in **E4** als unteres Ende kennen.

## Nachvollzugs-Matrix (§5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| a eine Erzeugerfunktion, drei Aufrufer | AP-1 `hoehenkette.ts` + umhängen | n.U. | n.U. |
| b Rot-Probe 2700 → 2740 im Browser | AP-2 Browserabnahme | n.U. | n.U. |
| c Bestand bitgleich ohne Decke | AP-2 Fixture | n.U. | n.U. |
| d `tsc`/Suite/Bündel | AP-3 Lieferung | n.U. | n.U. |
| e kein Modell-/Schema-Diff | AP-3 Schutzbeleg | n.U. | n.U. |

## Rückweg

**Revert eines Commits.** Bestandsdokumente unberührt — es entsteht kein Zustand, nur eine
Rechenquelle weniger.
