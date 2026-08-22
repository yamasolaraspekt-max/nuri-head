# Z1-W2-8 — Die Werkzeugleiste steht in Baureihenfolge, und die Bodenplatte bekommt einen Eintrag

**ZIEL:** Wer von oben nach unten durch die Leiste geht, **baut ein Haus in der Reihenfolge, in der
es entsteht** — Bodenplatte, Wand, Fenster, Tür, Treppe, Decke, Kontur, Dach.

```yaml
auftrag: "Z1-W2-8"
spur: W
art: "REIHENFOLGE + EIN REGISTRY-EINTRAG. KEIN neuer Knotentyp, KEINE Fachlogik,
      KEINE neue Bedienlogik — nur Anordnung und ein Eintrag auf vorhandenem Werkzeug."
heimat_app: ticket
heimat_code: resources/planner/hausplaner
mess_sha: 3ab3bb88
kennung_geprueft: "Z1-W2-8: docs/ 0 Treffer, git log --all --grep 0 (ausser dem Auftrag selbst).
                   Z1-W2-0..6 vergeben, -7 frei gelassen, -8 vom Dirigenten zugewiesen."
dor_beleg: "steht aus — plan-pruefer (EIN Durchgang, direkt nach Z1-V1-1)"
basis_sha: 3ab3bb88
prioritaet: "P0 — Yamas ausdrueckliche Anweisung 18:4x (Posten 24)"
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "dirigent-auftrag-Z1-W2-8-leiste-baureihenfolge.yaml (18:46:18)"
zielreifegrad: BROWSERABGENOMMEN
```

## Ausgangslage, gemessen am Stand `3ab3bb88`

```
TOOL_DEFINITIONS — heutige Reihenfolge (app/tools/toolRegistry.ts)
  :39 auswahl   :59 wand   :78 fenster   :96 tuer
  :114 DACH     :132 decke  :150 treppe
  :182 bemassen :196 flaeche-messen :230 kontur
  :249 loeschen :273 duplizieren :298 trimmen

Leiste                app/HausplanerApp.tsx:493  zoneTools('fix')  ->  app/tools/toolPresentation.ts
Gruppen               app/dashboard/werkzeugGruppen.ts (101 Z.)  WERKZEUG_GRUPPEN aus WERKZEUG_THEMEN
Menue                 app/dashboard/WerkzeugGruppenMenue.tsx
'bodenplatte' in der Registry                                        0 Treffer
```

> **Ein Befund, der den Zuschnitt trägt: die Bodenplatte ist bereits genannt — nur nicht als
> Eintrag.** `toolRegistry.ts:147` führt für das Deckenwerkzeug den Tooltip-Titel
> **„Decke / Bodenplatte"**. *Die beiden Dinge stehen heute unter einem Knopf, und der Tooltip sagt
> es. Dieses Blatt trennt die Bedienung, nicht das Modell.*

**Die Soll-Reihenfolge und was sie heute verletzt:**

```
SOLL   bodenplatte -> wand -> fenster -> tuer -> treppe -> decke -> kontur -> dach
IST    (kein Eintrag) wand -> fenster -> tuer -> DACH  -> decke -> treppe -> … -> kontur
                                                 ^^^^
         Dach steht VOR Decke und Treppe — gebaut wird es zuletzt.
```

---

## Abnahmekriterien

- **Z1-W2-8-a** · **DIE REIHENFOLGE STIMMT — IN LEISTE UND MENÜ.**

  **Verlangt:** In **beiden** Ansichten exakt: `bodenplatte → wand → fenster → tuer → treppe →
  decke → kontur → dach`. **Die übrigen Gruppen** (Auswahl/Messen/Bearbeiten/CAD) stehen
  **unverändert dahinter**.

  **Messbefehl:**
  ```
  grep -nE "^    id: '" app/tools/toolRegistry.ts        -> die acht in dieser Folge
  Leiste:  app/HausplanerApp.tsx:493 zoneTools('fix')    -> gerenderte Folge im Browser
  Menue:   app/dashboard/WerkzeugGruppenMenue.tsx        -> dieselbe Folge
  ```

  **Heutiges (rotes) Ergebnis:** `dach` steht auf `:114` **vor** `decke` (`:132`) und `treppe`
  (`:150`); `bodenplatte` hat **0 Treffer**.

  **Absage-Regel:** Nur die Registry umzusortieren erfüllt (a) **nicht**, wenn Leiste oder Menü
  ihre Folge woanders herleiten. **Beide Ansichten sind zu belegen** — *die Leiste rendert über
  `zoneTools`, das Menü über `WERKZEUG_GRUPPEN`; wer nur eine prüft, hat die halbe Zusage.*

- **Z1-W2-8-b** · **DIE BODENPLATTE IST EIN EINTRAG — AUF DEM VORHANDENEN DECKENWERKZEUG.**

  **Verlangt:** Neuer Registry-Eintrag `bodenplatte` mit **`bauteilKind: 'ceiling'`**, der die
  Decke **auf der untersten Ebene** setzt. **KEIN neuer Knotentyp, keine Modelländerung.**

  **Messbefehl:**
  ```
  grep -c "id: 'bodenplatte'" app/tools/toolRegistry.ts      0 -> 1
  im Eintrag: bauteilKind 'ceiling'
  git diff <basis>..<bau> -- domain/ commands/              -> LEER (kein Knotentyp)
  ```

  **Heutiges (rotes) Ergebnis:** 0 Einträge; die Bodenplatte existiert nur als Tooltip-Wort
  (`:147` „Decke / Bodenplatte").

  **Absage-Regel:** Ein neuer Knotentyp `floorSlab` o. ä. erfüllt (b) **nicht** — das wäre eine
  Modelländerung und gehört zu `GP-0`. **Kann das Deckenwerkzeug die unterste Ebene nicht ohne
  Modelländerung setzen: MELDEN, nicht basteln** — dann liefert der Bau die Reihenfolge **ohne**
  Bodenplatte und hängt sie als Folgeposten an `GP-0`.

- **Z1-W2-8-c** · **DER TOOLTIP IST EHRLICH.**

  **Verlangt:** Der Bodenplatten-Tooltip sagt, was das Werkzeug **wirklich** tut:
  *„heute als Decke der untersten Ebene geführt; eigener Bodenplatten-Knoten `GP-0` folgt."*
  **Der Decken-Eintrag bleibt EIN Eintrag** und nennt im Tooltip **beides** (Zwischen- **und**
  Abschlussdecke).

  **Messbefehl:** beide Tooltips im Browser sichtbar, Wortlaut im Bericht.

  **Heutiges (rotes) Ergebnis:** kein Bodenplatten-Tooltip; der Decken-Tooltip nennt „Decke /
  Bodenplatte" — **die Trennung, die dieses Blatt herstellt, ist dort noch nicht abgebildet.**

  **Absage-Regel:** *„Bodenplatte erzeugen"* ohne den Zusatz erfüllt (c) **nicht.** *Ein Werkzeug,
  das eine Decke setzt und Bodenplatte heißt, verspricht einen Knotentyp, den es nicht gibt.*

- **Z1-W2-8-d** · **ROT-PROBE UND BILDBELEG, ALT GEGEN NEU.**

  **Messbefehl:**
  ```
  ORT: die im Repo vorhandene Puppeteer-Buehne, Chrome HEADFUL
  ALT (Stand <basis>):  Bildbeleg Leiste + Menue  ->  Dach VOR Decke/Treppe, keine Bodenplatte
  NEU (Stand <bau>):    Bildbeleg Leiste + Menue  ->  die acht in Soll-Folge
  ```

  **Heutiges (rotes) Ergebnis:** ist die Rot-Lage selbst — der alte Stand zeigt `dach` vor
  `decke`/`treppe`.

  **Absage-Regel:** Ein Bildbeleg nur vom neuen Stand erfüllt (d) **nicht** — *ohne das Vorher ist
  nicht belegt, dass sich etwas geändert hat.*

- **Z1-W2-8-e** · **DIE LIEFERUNG IST GRÜN UND VOLLSTÄNDIG.**

  **Messbefehl:** `tsc:hausplaner` → **0** · `test:hausplaner` → 0 fail · **Bündel gebaut und
  mitcommittet** (`public/hausplaner/hausplaner.js`).

  **Heutiges (grünes) Ergebnis:** Schutzbeleg, am Bau zu messen.

  **Absage-Regel:** *Eine Reihenfolgeänderung ohne Bündel erreicht den Browser nicht* — der Fall ist
  eingetreten (Befund `db64c7ca`, zehn Commits ohne Bündel).

- **Z1-W2-8-f** · **DER DIFF BLEIBT AUF ZWEI DATEIEN UND IHRE TESTS.**

  **Verlangt:** Geändert werden **nur** `app/tools/toolRegistry.ts` und
  `app/dashboard/werkzeugGruppen.ts` — **plus die nachziehenden Tests**. **Fachlogik leer.**

  **Messbefehl:**
  ```
  git diff --name-only <basis_sha>..<endstand_sha>
      -> nur die zwei Dateien + __tests__/* + public/hausplaner/hausplaner.js
  git diff <basis_sha>..<endstand_sha> -- geometry/ domain/ commands/   -> LEER
  ```

  **Heutiges (grünes) Ergebnis:** Schutzbeleg.

  **Absage-Regel:** `git diff` **ohne beide SHA** erfüllt (f) **nicht** — *ohne Referenz vergleicht
  es Arbeitsbaum gegen Index und ist nach dem Commit immer leer* (Halbsatz 1 der Spur-V-DoR, §421;
  die Lehre gilt hier genauso).

---

## Nicht-Ziele

- **Kein neuer Knotentyp.** Die Bodenplatte ist ein **Bedieneintrag** auf `bauteilKind: 'ceiling'`.
  *Der eigene Knoten ist `GP-0` und nicht dieses Blatt.*
- **Keine Änderung an den übrigen Gruppen.** Auswahl/Messen/Bearbeiten/CAD bleiben, wie sie sind —
  **unverändert dahinter**.
- **Keine Fachlogik.** `geometry/`, `domain/`, `commands/` bleiben leer im Diff.
- **Kein zweiter Decken-Eintrag.** Die Decke bleibt **ein** Eintrag; der Tooltip nennt beide Fälle.

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-8-a Reihenfolge in Leiste **und** Menü | AP-1 Registry + Gruppen | n.U. | n.U. |
| Z1-W2-8-b `bodenplatte` als Eintrag, `ceiling` | AP-1 (Eintrag) | n.U. | n.U. |
| Z1-W2-8-c Tooltips ehrlich | AP-1 (Texte) | n.U. | n.U. |
| Z1-W2-8-d Rot-Probe + Bildbeleg alt/neu | AP-2 Browserabnahme | n.U. | n.U. |
| Z1-W2-8-e `tsc` 0, Suite grün, Bündel | AP-2 (Lieferung) | n.U. | n.U. |
| Z1-W2-8-f Diff auf zwei Dateien | AP-2 (Schutzbeleg) | n.U. | n.U. |

## N4 — Bedienweg

| | |
|---|---|
| **Auslöser** | die Leiste selbst — sie **ist** der Bedienweg |
| **Ort** | `app/HausplanerApp.tsx:493` (Leiste) · `app/dashboard/WerkzeugGruppenMenue.tsx` (Menü) |
| **neue Kennung** | **`bodenplatte`** — der erste neue `registry_id` seit `trimmen` (A-35) |
| **Zielreifegrad** | `BROWSERABGENOMMEN` |

## Rückweg

**Revert dieses einen Commits.** Es entsteht kein Zustand: keine Migration, kein Knotentyp, keine
Fachlogik — nur Anordnung und ein Eintrag. *Der Rückweg muss das Bündel mit zurücknehmen, sonst
zeigt der Browser die neue Leiste ohne die Registry dahinter.*

---

# ⚠ NACHTRAG 22.08. 18:5x — Kriterium (b) fällt: die Bodenplatte geht NICHT ohne Modelländerung

```yaml
anlass: "Fachlinsen-Vorlage der lesenden Sitzung (18:52:21, maurer/statiker/software-architekt)
         — am Code nachgeprueft, nicht uebernommen."
folge: "Die ABSAGE-REGEL von (b) greift: 'MELDEN, nicht basteln'."
```

**Ich habe die drei Gründe selbst am Code geprüft. Zwei davon sind harte Sperren:**

```
1  CeilingNode HAT KEIN HOEHENFELD                     domain/scene.types.ts
     Felder des Blocks: type · polygon · dickeMm · oeffnungen? · schichten?
     'eleva' IM CeilingNode-BLOCK   0        <- die tragende Zahl
     'eleva' in der GANZEN DATEI    3        :66 Level.elevation · :327 + :343 Kommentare
     Eine Bodenplatte auf der untersten Ebene waere von einer Zwischendecke
     NICHT UNTERSCHEIDBAR.

2  MAX. EINE DECKE JE LEVEL                            commands/applyCommand.ts:112-116
     function pruefeDeckeProLevel(draft, ceiling) {
       if ((draft.ceilings ?? []).some(c => c.id !== ceiling.id && c.levelId === ceiling.levelId))
         throw new CommandAbgelehnt('... hat bereits eine Decke (max. 1 je Level).');
     }
     ZWEI Aufrufstellen (:296, :315) + EINE Definition (:112) = DREI Zeilen mit Klammer.
     -> Wer die Bodenplatte als 'ceiling' auf das EG-Level setzt, SPERRT DORT DIE ZWISCHENDECKE.

3  DER CRM TRENNT SIE BEREITS                          app/Services/Heizlast/UWertService.php:26-27
     'decke' => [0.10, 0.04]   'boden' => [0.17, 0.00]   — getrennte Bauteiltypen
```

> **Kriterium (b) ist damit in seiner heutigen Form nicht erfüllbar.** *Ein Registry-Eintrag
> `bodenplatte` auf `bauteilKind: 'ceiling'` würde entweder die Zwischendecke desselben Geschosses
> verhindern oder von ihr ununterscheidbar sein. Beides ist schlechter als kein Eintrag.*

**Was baubar bleibt und was nicht:**

| Kriterium | Stand |
|---|---|
| **(a)** Reihenfolge in Leiste und Menü | **baubar** — ohne `bodenplatte`, mit sieben statt acht Einträgen |
| **(b)** `bodenplatte` als Eintrag | **FÄLLT** — braucht `GP-0` (eigener Knotentyp) |
| **(c)** Tooltip ehrlich | **baubar, verschoben** — gilt für den Decken-Tooltip (Zwischen-/Abschlussdecke) |
| **(d)–(f)** Rot-Probe, Lieferung, Diff | **baubar, unverändert** |

**Die Soll-Reihenfolge ohne Bodenplatte:**

```
wand -> fenster -> tuer -> treppe -> decke -> kontur -> dach
uebrige Gruppen unveraendert dahinter
```

> **Yamas eigene Modellentscheidung liegt bereits vor** (`docs/konzept/golden-path-bauwerksprozess.md:33-41`,
> 21.08.): *`CeilingNode` bleibt die Zwischendecke; additive `FoundationSlabNode`; fachlich getrennte
> Bauteile; Bestandsprojekte ohne Bodenplatte müssen unverändert laden.* **Dazu ein Planner-Konzept
> von heute 12:23** (`golden-path-gp0-modellplan-bodenplatte.md`, 240 Z.).
> **Es muss nichts neu erfunden werden — die Bodenplatte braucht GP-0, nicht dieses Blatt.**

**Was der Bau NICHT tun darf:** die Bodenplatte trotzdem auf `ceiling` legen. *Der Auftrag des
Dirigenten sagt es wörtlich: „MELDEN, nicht basteln — dann Reihenfolge ohne Bodenplatte liefern und
Bodenplatte als Folgeposten an `GP-0` hängen."*

> ### Zwei meiner Zahlen waren ohne Grundmenge — berichtigt, die Aussage bleibt
>
> **Der Plan-Prüfer hat alle drei Belege selbst nachgemessen und bestätigt** (§428) — und dabei zwei
> meiner Zahlen als ungenau benannt:
>
> ```
> 'eleva'                 ich: "0 Treffer"        genau: 0 IM BLOCK, 3 in der DATEI
> pruefeDeckeProLevel     ich: "3 Aufrufstellen"  genau: 2 Aufrufe + 1 Definition = 3 ZEILEN
> ```
>
> **Beide Sätze waren richtig, beide Zahlen zählten eine andere Menge als sie benannten.** *Das ist
> die Fehlerklasse, die ich heute selbst mehrfach gemeldet habe — diesmal an mir.* **Die Sperre
> bleibt unverändert: zwei echte Aufrufstellen genügen, und das Höhenfeld fehlt im Block.**
