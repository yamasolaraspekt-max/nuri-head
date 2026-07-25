# ⇒ GENERATOR-AUFTRAG AUF-63 — jsdom für Fokus und Tastatur, **nicht** für Geometrie

**Vom:** Planner · **26.07.2026** · **Anlass:** Bestandsmeldung des Evaluators, Frage 3 —
*„es gibt weiter kein DOM im Testlauf … Fokus-Falle und `getComputedStyle` kann ich nur im Browser
messen, nicht im Gate."*

**Vorher gelesen:** HEAD `f845d78` · `git log -5` · Tafelzeile AUF-63 ·
`package.json:10` (`test:hausplaner`) · `resources/planner/hausplaner/test-hooks.mjs` (47 Z.) ·
Ledger „EVALUATOR-BESTANDSMELDUNG" (26.07.).

**Ich habe den Preis vor dem Schreiben gemessen** — die Auflage, die ich mir bei der Anlage des
Postens selbst gegeben habe. Die Messung steht in §1 und hat den Zuschnitt bestimmt.

---

## 1. Die Messung — sie zieht die Grenze, nicht ich

**Probe gegen jsdom, 26.07., eigener Lauf:**

| geprüft | Ergebnis |
|---|---|
| Fokus wandert (`el.focus()` → `document.activeElement`) | **ja** |
| `getComputedStyle` liefert Werte | **ja** (`display: inline-block`) |
| Leertaste als `KeyboardEvent` messbar | **ja** |
| `getBoundingClientRect().width` | **0** |

**Der letzte Wert ist der entscheidende: jsdom hat keine Layout-Engine.** Jede Geometrie ist dort
**0** — Überlauf, Breiten, Zielgrößen, Panel-Kanten, `scrollWidth`. Alles, was heute per iframe
gemessen wird, bleibt Browser-Sache.

**Preis, gemessen:** jsdom **39 Pakete / 27 MB**. Zum Vergleich happy-dom **9 Pakete / 23 MB** —
schlanker in der Zahl, gleich schwer auf der Platte. Der Hausplaner-Testlauf trägt heute **keine**
Test-Abhängigkeit außer `esbuild`, das über Vite ohnehin liegt.

**Urteil: ja — mit gezogener Grenze.** Fokusfalle und Tastatur sind heute mit **null** Tests gedeckt
und haben real Fehler durchgelassen (AUF-49 war nur im Browser prüfbar). Das rechtfertigt die
Abhängigkeit. Was es **nicht** rechtfertigt, ist die Erwartung, das Gate ersetze die Sichtprobe.

## 2. Was gebaut wird

1. **jsdom als `devDependency`** — nicht als Laufzeit-Abhängigkeit, nicht ins Bundle.
2. **Ein zweiter Testlauf, nicht ein umgebauter erster.** `test:hausplaner` bleibt, wie es ist:
   schnell, ohne DOM, 108 Dateien. Daneben ein Lauf für DOM-Tests. Grund: ein DOM für **alle** Tests
   zu stellen macht 108 Dateien langsamer, damit ein Dutzend etwas prüfen kann.
3. **Die vorhandene `esbuild`-Übersetzung aus AUF-30 wird wiederverwendet**, nicht ersetzt.
4. **Erste echte Tests**, an denen sich der Nutzen zeigt — nicht als Demo, sondern für das, was
   heute ungedeckt ist:
   - **Fokusfalle** in `FachFlaeche` und `ConfigWizard`: Tab am Ende springt an den Anfang, nicht
     hinter den Dialog.
   - **Fokus-Rückgabe** nach Escape.
   - **Leertaste** auf den selbstgebauten Schaltflächen (heute 8× `role="button"`).

## 3. Die Grenze — sie gehört in den Code, nicht nur in diesen Auftrag

**Ein DOM-Test darf keine Geometrie behaupten.** In jsdom ist jede Breite 0; ein Test, der
`getBoundingClientRect()` prüft, ist dort **immer grün oder immer rot** — beides wertlos.

**Deshalb verbindlich:** eine Zusicherung im DOM-Testlauf, die fehlschlägt, sobald ein Test dort
`getBoundingClientRect`, `offsetWidth`, `scrollWidth` oder `clientWidth` benutzt. **Lieber ein
Testlauf, der die Grenze selbst durchsetzt, als eine Zeile in einem Dokument, die niemand liest.**

Und in der Kopfzeile der DOM-Testdatei ein Satz für den nächsten, der sie öffnet:
*„Kein Layout. Geometrie wird im Browser gemessen (iframe fester Breite), nicht hier."*

## 4. Was **nicht** gebaut wird

- **Kein Ersatz der Sichtprobe.** Sie bleibt Teil jeder `sichtbar`-Abnahme. Der Evaluator hat selbst
  benannt, dass eine **vertagte Sichtprobe eine offene Abnahme ist** — ein grünes DOM-Gate darf nicht
  zum neuen Grund werden, sie zu vertagen.
- **Kein Umbau der 108 vorhandenen Tests.** Sie laufen weiter ohne DOM.
- **Keine zweite Test-Bibliothek.** Kein Testing-Library, kein Vitest, kein Jest. `node:test` bleibt.
- **Kein jsdom im Bundle.** `devDependency`, und ein Test belegt, dass `hausplaner.js` es nicht enthält.

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
   **Der bestehende Lauf darf nicht langsamer werden** — Laufzeit vorher/nachher nennen.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
3. **jsdom ist `devDependency`:** `grep` in `package.json` belegt es; ein Test belegt, dass
   `jsdom` in `public/hausplaner/hausplaner.js` **nicht** vorkommt.
4. **Die Grenze greift:** ein absichtlich falscher Test, der `getBoundingClientRect` im DOM-Lauf
   benutzt, **schlägt fehl** — vorgeführt und wieder zurückgebaut.
5. **Die drei Fälle aus §2.4 sind gedeckt** und waren vorher ungedeckt: je ein Test, der **ohne**
   den zugehörigen Code rot wird (Mutations-Gegenbeweis, Zahl nennen).
6. **Fokus-Rückgabe testverriegelt** — der Fall, den der Playwright-Lauf am 25.07. als fehlend
   gemeldet hat.
7. **`public/*` im Code-Commit: null Zeilen.** *(Hier fällt der Bundle-Rebuild vermutlich aus —
   Testinfrastruktur ändert das Bundle nicht. Dann ist die Meldung „kein Rebuild nötig, weil kein
   ausgeliefertes Artefakt berührt" die richtige, und §8 ist erfüllt.)*
8. **Klassifikation: `Vorarbeit`.** Für den Nutzer ändert sich **nichts** — das ist hier die richtige
   Einstufung und keine Ausrede.

## 6. Was zurückgegeben wird

- **Bricht `--experimental-strip-types` mit jsdom** oder verträgt sich der Loader aus AUF-30 nicht:
  **melden, nicht umbauen.** Der bestehende Testlauf ist das teurere Gut.
- **Erweist sich happy-dom als der bessere Träger** (9 statt 39 Pakete, gleicher Funktionsumfang für
  Fokus und Tastatur): **das ist ein zulässiges Ergebnis.** Der Auftrag nennt jsdom, weil der
  Evaluator es genannt hat — nicht, weil ich es geprüft hätte. Wer die bessere Wahl misst, soll sie
  begründen und nehmen.
