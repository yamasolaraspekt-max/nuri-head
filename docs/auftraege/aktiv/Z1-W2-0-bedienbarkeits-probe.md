# Z1-W2-0 — Die Bedienbarkeits-Probe: ein Messgerät, das jedem Werkzeug nachgeht

**ZIEL:** Eine parametrisierte DOM-Probe beweist **je Eintrag aus `TOOL_DEFINITIONS`**, dass er
bedienbar ist — aktivieren, Wirkung, Zurückstellen —, und **fällt rot, sobald ein Eintrag ohne
Bedienweg hinzukommt**.

```yaml
auftrag: "Z1-W2-0"
spur: W
welle: "Anschlusswelle 1 — vorgezogen (WERKZEUGWEG-Entscheidung 14:46:11, Vorschlag 1)"
heimat_app: ticket
heimat_code: resources/planner/hausplaner
werkzeug: "— KEIN Werkzeug. Dies ist das MESSGERAET fuer alle Werkzeugblaetter."
zieldatei: "__domtests__/werkzeugBedienbar.dom.test.ts (neu)"
registry_kennung: "KEINE (siehe N4)."
art: "MESSGERAET — eine Probe, die andere Blaetter belegen. KEIN Produktcode,
      KEINE toolRegistry-Aenderung, kein neues Werkzeug."
mess_sha: 39260edd
kennung_geprueft: "Z1-W2-0 gemessen: docs/ 0 Treffer; git log --all --grep 1 Treffer, und das ist
                   die ENTSCHEIDUNG SELBST (9cea7297, WERKZEUGWEG), die die Kennung vergibt —
                   kein vergebenes Blatt. Frei und ausdruecklich zugewiesen."
dor_beleg: "steht aus — plan-pruefer"
basis_sha: 39260edd
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "WERKZEUGWEG-entscheidung-2026-08-22.md (Dirigent in Yamas Namen), Vorschlag 1:
                 'Dieses Blatt wird zum Pflicht-Messbefehl jeder Browser-/Bedienbarkeitsabnahme.'"
zielreifegrad: "— (Messgeraet; es wird selbst nicht bedient)"
```

## Warum dieses Blatt vor den anderen kommt

Jedes Anschlussblatt dieser Welle verlangt eine **Browserabnahme**. Bisher heißt das: **je Blatt
ein eigener Handgriff**, je Rolle eine eigene Bühne, je Abnahme eine neue Verabredung darüber, was
„bedienbar" belegt. **Dieses Blatt macht daraus einen Befehl.**

> *Ein Messgerät, das nur einmal benutzt wird, ist ein Handgriff. Eines, das bei jeder Abnahme
> läuft, ist eine Zusage.*

## Ausgangslage, gemessen am Stand `39260edd`

```
TOOL_DEFINITIONS (app/tools/toolRegistry.ts)          13 Eintraege
  davon art: 'werkzeug'                               10   auswahl wand fenster tuer dach
                                                           decke treppe bemassen flaeche-messen kontur
  davon art: 'aktion'                                  3   loeschen duplizieren trimmen
  MIT shortcut                                        10   V W F T D K R U · Delete · Ctrl+D
  OHNE shortcut                                        3   bemassen · flaeche-messen · trimmen
vorhandene DOM-Proben (__domtests__)                   5   dialogFokus · escapeStapel · objektkopf
                                                           reiterLeiste · schienen
Zieldatei werkzeugBedienbar.dom.test.ts               EXISTIERT NICHT
```

**Zwei Messungen prägen den Zuschnitt und stehen deshalb hier oben:**

1. **Drei Einträge sind `art: 'aktion'`, keine Werkzeuge.** Eine Aktion wird **ausgelöst**, nicht
   **aktiviert** — sie bleibt nicht aktiv, und `Escape` stellt sie nicht zurück. *Die fünfteilige
   Kette des Auftrags passt auf sie nicht unverändert.*
2. **Drei Einträge haben kein Kürzel** (`bemassen`, `flaeche-messen`, `trimmen`). *„Per Kürzel
   aktivieren" ist für sie nicht durchführbar* — sie brauchen den Weg über die Leiste.

**Die Schnittmenge ist nicht leer:** `trimmen` ist **beides** — Aktion *und* ohne Kürzel.

## N4 — Bedienweg

| | |
|---|---|
| **Bedienweg** | **keiner.** Dies ist ein Messgerät, kein Werkzeug. |
| **Auslöser** | der Lauf von `npm run test:hausplaner:dom`, und ab Erteilung **jede** Browser-/Bedienbarkeitsabnahme |
| **Ort** | `__domtests__/werkzeugBedienbar.dom.test.ts` |
| **tragendes Werkzeug** | **keines** — das Blatt trägt alle |
| **Zielreifegrad** | entfällt (es wird nicht bedient, es misst) |

---

## Abnahmekriterien

- **Z1-W2-0-a** · **DIE PROBE LÄUFT ÜBER `TOOL_DEFINITIONS`, NICHT ÜBER EINE LISTE.**

  **Verlangt:** Die Testdatei liest die Einträge **aus der Registry** und erzeugt daraus ihre Fälle.
  **Kein Handverzeichnis von Werkzeugnamen in der Testdatei.**

  **Messbefehl:**
  ```
  grep -c "TOOL_DEFINITIONS" __domtests__/werkzeugBedienbar.dom.test.ts   -> mindestens 1
  grep -cE "'(wand|dach|treppe|kontur)'" __domtests__/werkzeugBedienbar.dom.test.ts
      -> 0 ausserhalb der Ausnahmeliste aus (d)
  ```

  **Heutiges (rotes) Ergebnis:** Die Datei existiert nicht — `ls` → *No such file*.

  **Absage-Regel:** Ein hartcodiertes Array von Werkzeug-IDs erfüllt (a) **nicht**. *Dann misst die
  Probe die Liste und nicht die Registry, und ein neuer Eintrag bliebe ungeprüft — genau der
  Zustand, den dieses Blatt beendet.*

- **Z1-W2-0-b** · **DIE FÜNFTEILIGE KETTE, JE WERKZEUG AUSGELÖST.**

  **Verlangt:** Für jeden Eintrag mit `art: 'werkzeug'`:
  **aktivieren → Leiste zeigt aktiv → eine Aktion → Szene messbar geändert → `Escape` stellt zurück.**
  Jeder Schritt ist eine eigene Zusage; ein Schritt, der nicht messbar ist, macht den Fall rot.

  **Messbefehl:** `npm run test:hausplaner:dom`; im Bericht je Werkzeug die fünf Teilzusagen mit ihrem
  Ergebnis.

  **Heutiges (rotes) Ergebnis:** keine Probe vorhanden → **0 von 10** belegt.

  **Absage-Regel:** „Der Aufruf wirft keinen Fehler" erfüllt (b) nicht. **Verlangt ist eine
  messbare Änderung an der Szene** — vorher/nachher, nicht „hat nicht gekracht".

- **Z1-W2-0-c** · **AKTIVIEREN GEHT AUCH OHNE KÜRZEL.**

  **Verlangt:** Für die drei Einträge **ohne `shortcut`** (`bemassen`, `flaeche-messen`, `trimmen`)
  wird über die **Leiste** aktiviert. Die Probe wählt den Weg **je Eintrag aus dem Datenfeld**,
  nicht aus einer Sonderbehandlung im Testcode.

  **Messbefehl:**
  ```
  im Test: hat der Eintrag ein shortcut -> Tastenweg, sonst -> Leistenweg
  Beleg: alle drei kuerzellosen Eintraege sind im Ergebnis enthalten und gruen
  ```

  **Heutiges (rotes) Ergebnis:** `shortcut` fehlt bei **3 von 13**, gemessen an der Registry.

  **Absage-Regel:** Die drei zu überspringen erfüllt (c) **nicht** — dann wäre die Probe genau dort
  blind, wo der Bedienweg am wenigsten selbstverständlich ist.

- **Z1-W2-0-d** · **ALLE 13 SIND ERFASST — GRÜN ODER JE BEGRÜNDET AUSGENOMMEN.**

  **Verlangt:** Das Ergebnis nennt **13 von 13**. Ausnahmen sind erlaubt, aber **einzeln benannt
  und begründet** — insbesondere die drei `art: 'aktion'`, für die „aktiv bleiben" und „`Escape`
  stellt zurück" fachlich nicht gelten.

  **Messbefehl:**
  ```
  Faelle im Ergebnis == Eintraege in TOOL_DEFINITIONS   (13 == 13)
  je Ausnahme: id · Grund · welche Teilzusage entfaellt
  ```

  **Heutiges (rotes) Ergebnis:** 0 von 13 erfasst.

  **Absage-Regel:** Eine stille Auslassung erfüllt (d) nicht. *Eine Probe, die zwölf misst und
  dreizehn behauptet, ist schlechter als keine.*

- **Z1-W2-0-e** · **DIE ROT-PROBE: EIN EINTRAG OHNE BEDIENWEG FÄLLT DURCH.**

  **Verlangt:** Ausgelöst nachgewiesen — ein Eintrag ohne Bedienweg wird **in einem Wegwerf-Aufbau**
  hinzugefügt, die Probe wird **rot**. **Danach wird der Aufbau verworfen; `toolRegistry.ts`
  bleibt unverändert.**

  **Messbefehl:**
  ```
  ORT: Wegwerf-Kopie unter TMPDIR (A-37-22d) — die echte toolRegistry.ts wird NICHT angefasst
  Lauf mit dem erfundenen Eintrag -> Exit != 0, und die Meldung nennt dessen id
  Gegenprobe: derselbe Lauf ohne den Eintrag -> Exit 0
  git diff --stat app/tools/toolRegistry.ts -> leer
  ```

  **Heutiges (rotes) Ergebnis:** nicht durchführbar — es gibt keine Probe, die rot werden könnte.

  **Absage-Regel:** Eine behauptete Rot-Lage erfüllt (e) nicht. **Ohne ausgelöste Rot-Probe ist
  nicht belegt, dass die Probe überhaupt etwas fangen kann** — sie könnte 13-mal grün melden, weil
  sie nichts prüft.

- **Z1-W2-0-f** · **EIN NEUES WERKZEUG WIRD AM TAG SEINER EINTRAGUNG ERFASST.**

  **Verlangt:** Aus (a) und (d) folgt: ein neuer Eintrag in `TOOL_DEFINITIONS` **erzeugt
  automatisch einen Fall**. Der Nachweis ist derselbe Wegwerf-Aufbau wie in (e) — mit einem
  Eintrag, der einen Bedienweg **hat**: die Probe zeigt **14 Fälle**, alle grün.

  **Messbefehl:** Wegwerf-Aufbau, ein gültiger 14. Eintrag → Fallzahl 14, Exit 0.

  **Heutiges (rotes) Ergebnis:** nicht durchführbar.

  **Absage-Regel:** *Dieses Kriterium ist der eigentliche Zweck des Blattes.* Eine Probe, die nur
  die heutigen 13 kennt, veraltet mit dem ersten neuen Werkzeug.

- **Z1-W2-0-g** · **KEIN PRODUKTCODE, KEINE REGISTRY-ÄNDERUNG.**

  **Messbefehl:**
  ```
  git diff --name-only <basis>..<bau>
      -> nur __domtests__/werkzeugBedienbar.dom.test.ts (+ ggf. Testhilfen unter __domtests__)
  git diff --name-only <basis>..<bau> -- app/tools/toolRegistry.ts   -> leer
  git diff --name-only <basis>..<bau> -- ':!resources/planner/hausplaner'  -> leer
  ```

  **Heutiges (grünes) Ergebnis:** kein Bau → leer. **Schutzbeleg** am Bau-Diff.

- **Z1-W2-0-h** · **DIE PROBE IST EIN BEFEHL, KEIN HANDGRIFF.**

  **Verlangt:** Der Aufruf steht **mit Ort** im Blatt und im Bericht, sodass jede Rolle ihn
  unverändert fahren kann.

  **Messbefehl:**
  ```
  ORT: npm run test:hausplaner:dom   (der im Repo VORHANDENE Laeufer)
       = ./scripts/node-runtime.sh --experimental-strip-types
         --import ./resources/planner/hausplaner/dom-register.mjs
         --test "resources/planner/hausplaner/__domtests__/*.test.ts"
       Zieldatei: __domtests__/werkzeugBedienbar.dom.test.ts
  Bericht nennt: Befehl · Fallzahl · Exit · Stand-SHA
  ```

  > **Berichtigung (Generator-Befund 15:35:07, selbst nachgemessen):** hier stand **„Vitest"**.
  > **Vitest gibt es in diesem Repo nicht** — `grep -c vitest package.json` → **0**,
  > `node_modules/vitest` fehlt. Die fünf vorhandenen DOM-Proben laufen über `node --test` mit
  > Typen-Strip und einem DOM-Register.
  > *Ich habe ein Werkzeug benannt, ohne zu messen, ob es existiert* — dieselbe Klasse wie der
  > `$`-Anker und das fehlende `-E`: **ein Messbefehl, den niemand ausführen kann.**
  > **Der Generator hat richtig gehandelt:** gemeldet statt nachgebaut. *Vitest einzuführen wäre
  > eine Abhängigkeit im Wurzel-`package.json` gewesen — Code außerhalb der Insel (gegen `-g`) und
  > ein zweites Testwerkzeug neben dem vorhandenen.*

  **Heutiges (rotes) Ergebnis:** kein Befehl vorhanden.

  **Absage-Regel:** „läuft bei mir" erfüllt (h) nicht. *Der Zweck ist, dass niemand mehr fragen
  muss, wie Bedienbarkeit belegt wird.*

---

## Nicht-Ziele

- **Kein neues Werkzeug**, keine Änderung an `toolRegistry.ts` (g).
- **Kein Ersatz der Browserabnahme mit echtem WebGL.** *Die DOM-Probe misst den Bedienweg, nicht
  das Rendern.* Wo ein Blatt eine Szene sehen muss, bleibt die Puppeteer-Bühne (headful) zuständig.
- **Keine Aussage über Fachlogik.** Die Probe belegt, dass ein Werkzeug **erreichbar und wirksam**
  ist — nicht, dass es **richtig rechnet**.
- **Keine Bewertung der drei kürzellosen Einträge.** Ob `bemassen`, `flaeche-messen` und `trimmen`
  ein Kürzel *bekommen sollen*, entscheidet dieses Blatt nicht.

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-0-a über TOOL_DEFINITIONS | AP-1 Probengerüst | n.U. | n.U. |
| Z1-W2-0-b fünfteilige Kette | AP-2 Teilzusagen | n.U. | n.U. |
| Z1-W2-0-c ohne Kürzel über die Leiste | AP-2 (Wegwahl aus dem Feld) | n.U. | n.U. |
| Z1-W2-0-d 13 von 13 erfasst | AP-3 Vollzähligkeit + Ausnahmen | n.U. | n.U. |
| Z1-W2-0-e Rot-Probe ausgelöst | AP-4 Wegwerf-Aufbau | n.U. | n.U. |
| Z1-W2-0-f neuer Eintrag wird erfasst | AP-4 (14. Eintrag) | n.U. | n.U. |
| Z1-W2-0-g kein Produktcode | AP-5 Diff-Beleg | n.U. | n.U. |
| Z1-W2-0-h Befehl mit Ort | AP-5 (Bericht) | n.U. | n.U. |

## Rückweg

**Revert dieses einen Commits.** Es entsteht **eine neue Testdatei** und sonst nichts; `toolRegistry.ts`
und der Produktivpfad bleiben unberührt (g). *Ein Messgerät, das man zurücknimmt, hinterlässt keine
Lücke im Produkt — nur eine im Wissen.*

## Wirkung über dieses Blatt hinaus

Nach Erteilung ist dieser Befehl der **Pflicht-Messbefehl jeder Browser- und
Bedienbarkeitsabnahme** (WERKZEUGWEG-Entscheidung). **Die Blätter Z1-W2-1 bis -3 nennen ihn dann in
ihrem Kriterium (e) bzw. (f) mit** — *heute steht dort die Puppeteer-Bühne, weil es diese Probe noch
nicht gibt.* **Das ist ein Nachtrag an drei Blättern, sobald Z1-W2-0 gebaut ist**, und gehört
ausdrücklich **nicht** in diesen Bau.
