# Planner-Konzept T1 — Token-Konsolidierung `HausplanerApp.tsx`
**Status: KONZEPT — wartet auf Yamas Fach-/Design-Freigabe (Regel 4).** Noch **kein** Generator-Auftrag.
**Nicht Teil von A1** (dort ausdrücklich als „kein Beifang" ausgeschlossen).
Stand 2026-07-25 · Planner · alles unten ist **gemessen**, nicht geschätzt.

## 0. Befund (gemessen am Code, `HausplanerApp.tsx`)
- Die Datei **importiert `T` bereits**: `import { T } from './studioDaten';` (Z. 16).
- **Trotzdem** hält sie eine **zweite Farbwahrheit** als lokale Konstante `FARBEN` (Z. 42–47):
  `text #1f2937 · gedaempft #6b7280 · linie #9ca3af · raster #eef0f2 · rasterGrob #e2e4e7 · wand #374151 ·
  wandFuellung #4b5563 · auswahl #93c21c · raum rgba(147,194,28,0.06) · warnung #d97706 · gefahr #b91c1c ·
  erfolg #15803d`.
- Zusätzlich **roher Hex im JSX**: **31 verschiedene Hex-Werte in 79 Vorkommen auf 50 Zeilen**
  (Top: `#e5e7eb` 16×, `#fff` 8×, `#6b7280` 5×, `#d1d5db` 4×, `#9ca3af` 4×).
- **4×** `var(--sa-accent…)` mit Hex-Fallback (Z. 585–587, 759) — die React-Insel greift damit an `T` vorbei
  in die Blade-Welt (Token-Scope-ADR: `T` ist die EINE Hex-Quelle der Insel).

### Der eigentliche rote Punkt: **zwei verschiedene Grüns**
`FARBEN.auswahl = #93c21c` (und die Ableitungen `#f4fae7`, `#4d7c0f`, `#3f5a00`, `#1e2b00`,
`rgba(147,194,28,…)`) **≠** `T.brand = #7fae1c` / `T.brandInk = #496700`.
Das ist genau das Fehlerbild „falsches Grün" — kein Geschmacksthema, sondern eine verwechselte Token-Rolle.
Dazu kommt: **Marke ≠ Akzent.** In `T` ist der Akzent **Teal `#12807d`**, die Marke Grün `#7fae1c`. In
`HausplanerApp.tsx` wird Grün als **Akzent** benutzt (aktives Werkzeug, Primärbutton, Auswahl im Canvas).

## 1. Vorschlag (zur Freigabe, NICHT vorentschieden)
1. `FARBEN` **entfällt als eigene Wahrheit** und wird zu einem reinen **Alias-Objekt auf `T`** — ein einziger
   Ort, an dem die Zuordnung steht, damit der Rest der Datei unverändert `FARBEN.x` schreiben kann
   (kleiner Diff, kein Massen-Rename, kein Beifang).
2. Roher Hex im JSX → Token. Vorgeschlagene Zuordnung (neutral, unstrittig):
   `#fff`/`#ffffff` → `T.surface` · `#e5e7eb`/`#eef0f2`/`#f3f4f6` → `T.hair`/`T.hair2` ·
   `#d1d5db` → `T.hair` · `#9ca3af`/`#9aa0a8`/`#c7ccd2` → `T.faint` · `#6b7280` → `T.muted` ·
   `#1f2937`/`#232a31` → `T.ink` · `#f9fafb`/`#f6f7f8` → `T.bg` · `#fef2f2` → Fehler-Soft ·
   `#fff7ed` → `T.warnSoft` · `#ecfdf5`/`#f0fdf4` → `T.okSoft` · `#d97706` → `T.warn` ·
   `#b91c1c` → `T.err` · `#15803d` → `T.ok`.
3. **Die eine Frage an Yama** (alles andere hängt daran): das Auswahl-/Aktiv-Grün `#93c21c` —
   **(a)** auf `T.brand #7fae1c` ziehen (Marke bleibt Grün, eine Wahrheit, minimal andere Optik), oder
   **(b)** auf `T.accent #12807d` (Teal) ziehen, wie es der v9-Studio-Kopf schon tut (Marke bleibt dem Logo
   vorbehalten, Akzent wird Teal — konsistent mit `HausplanerStudio.tsx`, aber sichtbar andere Optik), oder
   **(c)** `#93c21c` als **neues Token in `T`** aufnehmen (dann ist es legal, aber wir haben drei Grüns).
   → **Planner-Empfehlung: (a)**, weil es die vorhandene Marken-Rolle trifft und die Optik am wenigsten
   verschiebt. Entschieden wird das von Yama.
4. Die 4× `var(--sa-…)`-Fallbacks: entweder ganz raus (Insel-Scope) oder der Fallback wird ein `T`-Wert.

## 2. Nahtstellen / Guardrails (wenn freigegeben)
- Nur `HausplanerApp.tsx` + ggf. eine Zeile in `studioDaten.ts` (falls (c)). **Keine** Änderung an
  `toolRegistry.ts`, `activation.ts`, `toolPresentation.ts` (A1 läuft dort gerade) — Konflikt vermeiden:
  **erst nach A1-Abnahme starten**.
- Rein optisch/deklarativ: **keine** Logik-, Command- oder Zod-Änderung → kein Schema-Regen, kein 422.
- Danach messbar: `grep -c "#[0-9a-fA-F]" HausplanerApp.tsx` **= 0** (Abnahmezahl), `tsc:hausplaner` grün,
  `test:hausplaner` unverändert grün, Kontrast der neuen Paare **gerechnet** (AA 4.5:1 Text / 3:1 UI-Rand).
- Zustand nie nur über Farbe (WCAG 1.4.1): das aktive Werkzeug behält Text + Rahmen, nicht nur Füllung.
