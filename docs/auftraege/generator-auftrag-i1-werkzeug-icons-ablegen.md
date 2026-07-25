# ⇒ GENERATOR — AUFTRAG I1: Die 110 Werkzeug-Icons ablegen

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-21 / I1 · **Spur:** **B**
(reine Assets + eine Doku-Seite — kein Datenpfad, keine Logik, keine Query, kein abgeleiteter Wert).
Trotzdem gilt: eine Zeile im Ledger ist Pflicht, und der Umfang wird nicht überschritten.

**Quelle:** `~/Downloads/hausplaner_svg_toolkit.zip` (liegt bereits auf der Maschine).
**Grundlage:** `docs/planner/inventur-werkzeug-icons-2026-07-25.md` und
`docs/planner/entscheidung-id-sprache-werkzeuge.md`.

---

## Ziel & Entscheidung

Die 110 SVGs werden abgelegt, **ohne** dass Code sie schon liest. Danach existieren die Dateien, auf
die `toolCatalog.ts` seit jeher zeigt — heute ins Leere (`/icons/<id>.svg`, 0 vorhanden).

**Zielort:** `public/hausplaner/icons/tools/<id>.svg` — ein eigener Unterordner, damit die vier
vorhandenen Bauteil-Ordner (`fenster`, `heizkoerper`, `treppe`, `tuer`) unberührt bleiben und nichts
kollidiert. `vite.hausplaner.config.ts` hat `emptyOutDir: false`, der Ordner überlebt einen Build.

**Dateinamen bleiben die Paket-IDs, unverändert englisch** (Entscheidung AUF-20). Keine Umbenennung,
auch nicht für `slab`/`stairs` — die beiden Schema-Konflikte löst später der Adapter in I2, nicht der
Dateiname.

**Zusätzlich abzulegen** (Nachschlagewerk, kein Code):
- `docs/planner/werkzeug-galerie.html` — die filterbare Galerie aus dem Paket
- `docs/planner/werkzeug-inventar.md` — die Inventarliste aus dem Paket
- `public/hausplaner/icons/tools/_sprite.svg` — das Sprite
- die Registry als **Referenz** unter `docs/planner/tool-registry-paket.json` — **nicht** unter
  `resources/planner/`, damit niemand sie versehentlich importiert, bevor I2 den Adapter gebaut hat.

## Was ausdrücklich NICHT zu diesem Auftrag gehört

- **Kein Code.** `toolCatalog.ts`, `toolRegistry.ts`, `toolPresentation.ts`, `toolTypes.ts` und jede
  `.tsx` bleiben **unberührt**. Kein Import, kein Adapter, keine ID-Umbenennung — das sind I2 und AUF-24.
- Kein Zod, kein Schema, keine Migration, kein PHP.
- `public/hausplaner/hausplaner.js` nicht anfassen (Build läuft hier nicht).
- Die vier bestehenden Icon-Ordner nicht anfassen.

## Kantenliste

1. Ein Paket-Icon heißt wie eine bestehende Datei → darf nicht überschreiben. Prüfe vor dem Kopieren
   auf Kollision und **melde** sie, statt zu überschreiben.
2. Ein SVG enthält eine eingebettete Schrift, eine externe Referenz oder ein `<script>` → aussortieren
   und melden, nicht ablegen. Erwartet sind reine Pfade mit `currentColor`.
3. Die Anzahl weicht von 110 ab → melden, nicht stillschweigend hinnehmen.
4. `viewBox` ist nicht `0 0 24 24` → melden. Nicht korrigieren.

## Abnahmekriterien

1. `ls public/hausplaner/icons/tools/*.svg | wc -l` = **110** (plus `_sprite.svg`).
2. Jeder Dateiname entspricht **genau** einer `id` aus `tool-registry.json`; Gegenprobe in beide
   Richtungen, beide Differenzmengen **leer** — als Rohausgabe im Bericht.
3. Grep über alle abgelegten SVGs: **0** Treffer für `<script`, `@font-face`, `xlink:href="http`,
   `<image`. Rohausgabe, auch wenn leer.
4. Anzahl SVGs mit `viewBox="0 0 24 24"` genannt; jede Abweichung einzeln aufgeführt.
5. Anzahl SVGs, die `currentColor` verwenden, genannt; jede Datei mit hartem Farbwert einzeln
   aufgeführt (**nicht korrigieren** — das ist ein Befund, keine Aufgabe).
6. `git diff` zeigt **null Zeilen** in `resources/`, `app/`, `routes/`, `database/` und in
   `public/hausplaner/hausplaner.js`.
7. Die drei Gates `tsc:hausplaner`, `schema:hausplaner:check`, `test:hausplaner` weiterhin **Exit 0**
   (sie dürfen sich nicht ändern — wenn doch, hast du Code angefasst). `build:hausplaner` als
   „nicht ausführbar" berichten.

## Guardrails

- **Ein Commit**, mit Pfadangabe: `git commit -m "…" -- public/hausplaner/icons/tools docs/planner`.
  **Nie `-A`, nie `.`**, `-m` **vor** dem `--`.
- **Vor dem ersten Schreibzugriff:** `git --no-optional-locks status --porcelain` prüfen. Liegen
  **fremde untracked Dateien** im Baum, arbeitet eine zweite Instanz — dann **nicht schreiben,
  sondern melden** (Lehre aus der Kollision vom 25.07., AUF-22).
- **Ziehe den Posten zuerst auf der Tafel** (`AUF-21` → `IN ARBEIT — Generator I1`) und committe
  **nur** diese Datei, bevor du anfängst.
- `.git/*.lock` niemals mit `rm` — nur `mv` nach `.git/_locks_beiseite/<datum>/`.
- **Kein Push, kein Merge, kein Deploy.** **Du meldest „umgesetzt", nie „abgenommen".**

## Bericht

Block in `docs/handoff-status.md`: `## ⇒ GENERATOR-BERICHT — I1 Werkzeug-Icons abgelegt`, mit den
sieben Kriterien als Rohausgabe, dem Commit-Hash, der Dateizahl und allem, was nach der Kantenliste
aussortiert oder gemeldet wurde.
