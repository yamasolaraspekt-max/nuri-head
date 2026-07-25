# ⇒ GENERATOR — AUFTRAG AUF-31: Die 110 Paket-IDs eindeutschen

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-31 · **Spur:** **A**
(berührt IDs, die der Adapter auf gespeicherte Schema-Werte abbildet — im Zweifel A)
**Vorbedingung erfüllt:** die führende Namenstabelle ist committet.

## Ziel & Entscheidung

I2 (`289ccc8`) hat den Katalog getauscht, aber die **englischen** Paket-IDs übernommen —
`u-value`, `thermal-envelope`, `floor-heating`, `heat-pump`, `import-file` stehen so in
`app/tools/werkzeugPaket.ts`. **Ursache: die Namenstabelle lag zu diesem Zeitpunkt uncommittet im
Arbeitsbaum.** Yamas Anordnung lautet *„ich will alles auf deutsch"*. Das wird jetzt nachgezogen.

**Führende Quelle — nicht neu erfinden:** `docs/planner/eindeutschung-110-paket-ids.md`.
Sie ist maschinell geprüft: 110 IDs, alle eindeutig, 16 schema-gebunden markiert.

## Was zu tun ist

1. **`app/tools/werkzeugPaket.ts`** — jede Paket-ID auf die deutsche ID der Tabelle. Maschinell aus
   der Tabelle ableiten, **nicht von Hand abtippen**.
2. **Die 110 Icon-Dateien** in `public/hausplaner/icons/tools/` mitbenennen: `<deutsche-id>.svg`.
   **`rm` ist verboten** — umbenennen per `git mv` bzw. `mv`, nicht kopieren-und-löschen.
   Das Sprite `_sprite.svg` trägt 110 `<symbol id="…">` — dieselben IDs mitziehen.
3. **`app/tools/paketAdapter.ts`** — die Abbildung deutsche UI-ID → gespeicherter Schema-Wert prüfen
   und nachziehen.

## Die drei Grenzen — hier bricht es sonst

1. **Die neun Bestands-IDs bleiben byte-genau:** `auswahl · wand · fenster · tuer · dach · decke ·
   treppe · loeschen · duplizieren`. **Kein** Aufrufer, **kein** Test, **keine** Fixture wird
   angefasst. Wenn dein Diff eine dieser IDs berührt, ist etwas falsch.
2. **Die 16 schema-gebundenen IDs behalten ihren englischen Schutzwert** im gespeicherten Dokument
   (`wall`, `ceiling`, `radiator`, `stair`, …, inkl. der Sonderfälle `slab→ceiling` und
   `stairs→stair`). Der Adapter bildet die **deutsche UI-ID** darauf ab. **Ein Umbenennen der
   gespeicherten Werte wäre eine Migration an Bestandsdaten — DAUERDIREKTIVE, niemals Beifang.**
   Ohne diese Trennung: **422 beim Speichern.**
3. **Kein Zod, kein Schema, keine Migration.** `schema:hausplaner:check` muss **ohne Regen** grün
   bleiben. Muss es regeneriert werden, hast du das Schema angefasst — dann zurück an den Planner.

## Kantenliste

1. Eine deutsche ID kollidiert nach dem Umbenennen mit einer Bestands-ID → **Abbruch und melden**,
   nicht auflösen. Die Tabelle sagt, das kann nicht passieren; wenn doch, ist die Tabelle falsch.
2. Ein Icon-Dateiname existiert schon → nicht überschreiben, melden.
3. Ein Werkzeug wird an mehreren Stellen referenziert (Registry, Zonen, Adapter, Sprite) → alle
   Stellen in **einem** Commit, sonst zeigt eine davon ins Leere.
4. Umlaute: die Tabelle schreibt sie aus (`ä→ae`). **Kein `ä` in einer ID oder einem Dateinamen.**

## Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — **Exit 0**,
   Schema **ohne Regen**. `build:hausplaner` als „nicht ausführbar" berichten, falls aarch64.
2. Testzahl vorher/nachher, **Namen-Mengen verglichen**, kein verschwundener Test.
3. Grep über `app/tools/werkzeugPaket.ts` nach den englischen Paket-IDs → **null Treffer**.
   Rohausgabe im Bericht, auch wenn leer.
4. `ls public/hausplaner/icons/tools/*.svg | wc -l` = **110**; Gegenprobe in **beide** Richtungen
   gegen die deutschen IDs der Tabelle, beide Differenzmengen **leer**.
5. Die 110 `<symbol id>` im Sprite decken sich mit den Dateinamen — beide Differenzmengen leer.
6. `git diff` zeigt **null Zeilen**, die eine der neun Bestands-IDs verändern.
7. **Gegen-Beweis, selbst geführt:** eine deutsche ID im Adapter auf einen falschen Schema-Wert
   setzen → mindestens ein Test **muss** rot werden. Danach zurückbauen, `git diff` leer.
   Wird kein Test rot, ist die Schema-Bindung ungeprüft — das ist ein Rot.
8. `git diff` zeigt null Zeilen in `store/*`, `domain/*`, `geometry/*`, `renderers/*`, PHP, Migrationen.

## Guardrails

- **Ein Commit**, mit Pfadangabe. **Nie `-A`, nie `.`**, `-m` **vor** dem `--`.
- Vor dem ersten Schreibzugriff `git --no-optional-locks status --porcelain` prüfen; fremde Arbeit in
  **deinen** Pfaden → nicht schreiben, melden.
- Posten **auf der Tafel ziehen, bevor** die erste Zeile geschrieben wird.
- `.git/*.lock` nur per `mv` nach `.git/_locks_beiseite/<datum>/`.
- **Kein Push, kein Merge, kein Deploy. Du meldest „umgesetzt", nie „abgenommen".**

## Bericht

`## ⇒ GENERATOR-BERICHT — AUF-31 IDs eingedeutscht`, mit den acht Kriterien als Rohausgabe, dem
Commit-Hash, der Zahl umbenannter Dateien und dem Ergebnis des Gegen-Beweises aus Kriterium 7.

---

## NACHTRAG Planner, 25.07. — Kante 1 ist eingetreten, Entscheidung: zusammenführen

Der erste Anlauf hat nach Kante 1 abgebrochen: **neun Ziel-IDs sind exakt die neun Bestands-IDs.**
Richtig gestoppt. Die Auflösung steht im Ledger und lautet **Weg 1 — zusammenführen**:

1. **Die neun Paket-Einträge entfallen** aus `werkzeugPaket.ts`:
   `select · duplicate · delete · wall · door · window · stairs · roof · slab`.
   **Katalog danach: 101 Einträge.**
2. **Ihre Metadaten wandern additiv in die neun Registry-Einträge** — Icon-Pfad, Kategorie,
   `funktion`, `einsatz`, `views`, `canPin —` nur dort, wo das Feld heute leer ist.
   **Kein bestehender Wert wird überschrieben, kein Feld von `ToolDefinition` neu erfunden.**
3. **Umbenannt werden dadurch nur noch 101 IDs.** Die neun Bestands-IDs bleiben byte-genau —
   sie waren nie das Problem, sie waren das Ziel.
4. **Zonen-Regeln danach: 110** (9 Registry + 101 Katalog), **keine doppelte `toolId`**.
   Der Vollständigkeitstest muss grün bleiben, **ohne** dass jemand seine Erwartung anpasst — wenn
   er rot wird, ist die Zusammenführung unvollständig.

**Zusätzliches Abnahmekriterium 10:** `TOOL_KATALOG.length` = **101** · `RULES.length` = **110** ·
keine doppelte `toolId` · `zoneTools`-Summe unverändert **110** eindeutige Werkzeuge.
Rohausgabe im Bericht.

**Zusätzliches Abnahmekriterium 11 — Gegen-Beweis:** eine der neun zusammengeführten IDs
versehentlich doppelt anlegen (Registry **und** Katalog) → der Vollständigkeitstest **muss** rot
werden. Danach zurückbauen, `git diff` leer. Wird er nicht rot, ist die Doppelung ungeprüft.
