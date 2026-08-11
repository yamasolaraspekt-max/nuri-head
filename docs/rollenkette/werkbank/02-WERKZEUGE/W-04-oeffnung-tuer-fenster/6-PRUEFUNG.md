# W-04 · Öffnung (Tür/Fenster) — PRÜFUNG

## Was eine Prüfung hier belegen muss

1. **Dass die vier Lookups sich unterschiedlich verhalten** — und zwar absichtlich (siehe `7-GRENZEN`).
2. **Dass die Kataloge zu ihren Typunionen passen.** `TuerTyp` nennt 5 Werte, `TUER_TYPEN` hat 5
   Einträge; `FensterTyp` nennt 7, `FENSTER_TYPEN` hat 7. *Ein Typ ohne Vorlage wäre eine Auswahl,
   die ins Leere greift.*
3. **Dass jede Bauart ihre SVG-Datei hat.** Die `datei`-Angabe zeigt auf
   `public/hausplaner/icons/` — 48 Verweise, die stimmen müssen.
4. **Dass die Reihenfolge stabil bleibt.** Sie ist Anzeigereihenfolge **und** Rückfallwert; wer
   sortiert, ändert beides.

## Warum Punkt 4 eigens geprüft wird

`tuerTyp()` fällt auf `TUER_TYPEN[0]` zurück, `fensterTyp()` auf `FENSTER_TYPEN[0]`. **Wer den
Katalog umsortiert, ändert damit stillschweigend den Rückfallwert.** *Eine Sortierung sieht wie eine
Anzeigefrage aus und ist eine Verhaltensfrage.*

## Was hier NICHT zu prüfen ist

**Die Türgeometrie.** Sie gehört W-02; eine Prüfung an dieser Stelle würde eine zweite Wahrheit prüfen.
