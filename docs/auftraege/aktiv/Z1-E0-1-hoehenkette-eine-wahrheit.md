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
dor_beleg: "ERTEILT — plan-pruefer 2026-08-22T19:30:30, Beleg 3ddf6a3e
            (plan-pruefer-DOR-Z1-E0-1-und-Z1-E2-1-ERTEILT.yaml, §436), OHNE Halbsaetze.
            Geprueft gegen mess_sha = basis_sha = fd2575ce, Blob identisch auf HEAD und
            rolle/planner — kein Drift. (Kopf hing bis 19:5x auf 'steht aus'.)"
basis_sha: fd2575ce
prioritaet: P0
ballbesitz: "generator (DoR erteilt — baubar; laut Dirigent 19:11:46 VOR Z1-W2-6 und Z1-W2-4)"
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

---

## ⚠ NACHTRAG 22.08. 19:3x — was E0 NICHT leistet (Nicht-Ziel, KEINE Kriterienänderung)

```yaml
anlass: "yama-lesesitzung-BEFUNDE-maurer-oeffnung-und-deckenanschluss.yaml (19:32:05), Befund 2 —
         am Code gegengeprueft, nicht uebernommen."
art: "NICHT-ZIEL ergaenzt. Die Kriterien a..e sind seit der DoR (19:30:30) UNVERAENDERT."
zeitangabe_berichtigt: "Ich hatte hier zweimal 19:30:48 geschrieben. Das Votum traegt zweimal
                        19:30:30 (zeit und gelesen_bis), der Hinweis des Plan-Pruefers vom
                        19:49:40 nennt es zweimal ebenso. VIER Nennungen gegen meine zwei —
                        19:30:30 gilt. Meine Zahl hatte keine Quelle, nur eine Erinnerung."
```

**Die Höhenkette hat zwei Hälften. E0 vereinheitlicht nur die eine.**

```
HAELFTE 1 — die ETAGEN-Kette   (E0 macht sie zur einen Wahrheit)
  elevation + defaultWallHeight + floorThickness/Decke

HAELFTE 2 — die WAND-Hoehe      (E0 fasst sie NICHT an)
  renderers/three-d/segmentierung.ts:60   const hoehe = wand.height
  -> JEDE Wand traegt ihre eigene Hoehe.
  Vergleich node.height gegen level.defaultWallHeight in commands/:  KEINER
    (defaultWallHeight dort nur :372 und :389 — beide in der ETAGEN-Kette)
  __tests__/decke.test.ts:261 schreibt das Muster /elevation \+ …defaultWallHeight/ FEST.

GEMESSEN AN DIESEM BLATT:  'defaultWallHeight' 4x  ·  'wand.height'/'node.height'  0x
```

> **Weicht eine einzelne Wand vom Geschoss-Standard ab, sitzt die Decke an ihr in der Luft oder
> schneidet hinein** — und E0 ändert daran nichts. *Das ist kein Mangel dieses Blattes, sondern
> seine Grenze; sie steht hier, damit niemand nach der Abnahme glaubt, die Höhenkette sei ganz.*

**Warum ich die Kriterien NICHT erweitere:** die DoR ist seit **19:30:30** (~~19:30:48~~) erteilt und der
Kriterienstand eingefroren; eine Erweiterung wäre eine neue Runde, und der Generator baut bereits.
**Ein Nicht-Ziel zu benennen ist keine Kriterienänderung** — es sagt, was das Blatt nicht zusagt.

**Ob die zweite Hälfte in E0 gehört oder ein eigenes Blatt wird, entscheidet der Dirigent.**
*Der Vorrat steht heute bei 7 bei einem Deckel von 6 — ein achtes Blatt schneide ich nicht von
selbst.*

---

## ⚠ NACHTRAG 22.08. 20:1x — zwei Mängel in Kriterium (a), beide meine

```yaml
anlass: "generator-CODE_FERTIG-Z1-E0-1.yaml (20:09:51, ergebnis_sha ad2ac724) meldet ZWEI
         Zahlenabweichungen gegen (a). Ich habe beide selbst am Stand d2890e85 nachgemessen."
art: "MANGELANZEIGE gegen das eigene Blatt. KEINE Kriterienaenderung: a..e bleiben fuenf,
      und ich setze KEINE neue Zielzahl — das waere ein nachtraegliches Verschieben des Ziels
      auf die gelieferte Zahl."
entscheidung_liegt_bei: "evaluator (Abnahme), plan-pruefer (ob der Kriterienstand traegt)."
```

### Mangel 1 — (a) und (e) sind zusammen nicht erfüllbar

Der Messbefehl von (a) verlangt **0**, schließt aber nur `__tests__` und `hoehenkette.ts` aus.
**Gemessen am Stand `d2890e85` trifft das Muster auch reine Kommentare:**

```
domain/scene.types.ts:327   traufhoeheMm: number;   // Default: level.elevation + defaultWallHeight
renderers/three-d/szene.ts:453                      // (level.elevation + defaultWallHeight). …
geometry/geschossVorlage.ts:41                      // Neue Höhenlage = elevation + defaultWallHeight …
```

> **Der Treffer in `domain/` ist ein Kommentar hinter einem Feld — keine Rechnung.** *Und (e)
> verlangt `git diff -- domain/` **LEER**.* **Wer (a) wörtlich erfüllt, verletzt (e); wer (e) hält,
> kann (a) nicht auf 0 bringen.** Mein Muster unterscheidet Code nicht von Kommentar — **das ist
> der Mangel, nicht die Lieferung.**

### Mangel 2 — (a) nennt einen Aufrufer, der zur anderen Funktion gehört

(a) verlangt, dass `Kopfrahmen.tsx:172`, `geschossVorlage.ts:54` **und `HausplanerApp.tsx:1008`**
aus `naechsteEtageElevationMm` lesen — Zielzahl **3**. **Gemessen:**

```
app/HausplanerApp.tsx:1008   ueberstandMm: 500, traufhoeheMm: deckenOberkanteMm(level),
                                                            ^^^^^^^^^^^^^^^^^^
deckenOberkanteMm(       Aufrufer: szene.ts:456 · szene.ts:483 · HausplanerApp.tsx:1008
naechsteEtageElevationMm( Aufrufer: KEINE (nur die Definition deckenMesh.ts:32)
```

> **Zeile 1008 setzt die Traufhöhe — das ist `deckenOberkanteMm`, nicht die nächste Etage.**
> *Und mein eigener Lücken-Block sagt es oben auf Zeile 42 korrekt:* „`deckenOberkanteMm` … 3
> Aufrufstellen: `szene.ts:456`, `:483`, `HausplanerApp.tsx:1008`". **Mein Kriterium hat den
> dritten Aufrufer der einen Funktion der anderen zugeschrieben.** *Dasselbe Blatt, zwei Aussagen,
> und das Kriterium hat die falsche genommen.*

### Was ich ausdrücklich NICHT tue

**Ich setze keine neue Zielzahl.** *Ob „1 statt 0" und „2 statt 3" die Zusage erfüllen, entscheidet
der Evaluator* — hätte ich die Zahlen jetzt auf die gelieferten geändert, wäre jede Lieferung
nachträglich richtig. **Was ich liefere, ist die Messung, die zeigt, woher die Abweichung kommt:
aus meinem Text, nicht aus dem Bau.**

**Lehre:** ein Messbefehl über Quelltext muss sagen, ob Kommentare zählen — und ein Kriterium darf
keinen Aufrufer nennen, ohne die Funktion mitzumessen, zu der er gehört.
