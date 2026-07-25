# ⇒ EVALUATOR — AUFTRAG: Abnahme T1 (Token-Konsolidierung) + Fachzuordnung `decke → bau`

**Von:** Planner · **An:** Evaluator (führend: native Instanz) · **Stand:** 25.07.2026, 10:47
**Gegenstand:** Der Arbeitsbaum-Stand zu `## ⇒ GENERATOR-BERICHT — T1 Token-Konsolidierung +
Fachzuordnung decke → bau umgesetzt` (`docs/handoff-status.md`)
**Basis:** `c0ffe31` (A1, abgenommen) bzw. `d530da3` (Ledger/Aufträge)
**Ballbesitz nach Votum:** Planner

---

## 0. Voraussetzung — ohne die fängst du nicht an

Der Stand ist **nicht committet**. Ein Evaluator kann keinen Arbeitsbaum abnehmen: er ist nicht
reproduzierbar, nicht diffbar, und er ändert sich unter der Messung. **Erst Commit durch den
Generator (Freigabe steht im Ledger), dann Abnahme gegen die Commit-SHA.** Miss nichts vorher.

## 1. Grundhaltung

Der Bericht ist ungewöhnlich gut — er nennt Kontrastzahlen, eine Laufzeitmessung und dass der alte
Anker **rot gesehen** wurde. Das ist kein Grund zu glauben, sondern eine gute Vorlage zum
Nachrechnen. Kein Punkt gilt als geprüft, weil er im Bericht steht. **Kein Code-Fix durch dich.**

## 2. Gates — selbst fahren, Exit-Codes notieren

`tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` · `build:hausplaner`.
Behauptet: alle Exit 0, **696/696 pass, 0 fail**. Miss zusätzlich die Vorher-Zahl selbst auf
`c0ffe31` (behauptet 695) — nicht übernehmen, ausführen.
Kein Zod berührt ⇒ Schema-Check muss **ohne** Regen grün sein.

## 3. Token-Disziplin — die eigentliche Behauptung von T1

- **„0 rohe Farbwerte in `HausplanerApp.tsx`"**: selbst zählen, und zwar **alle vier Formen** —
  `#hex` (3-, 6- und 8-stellig), `rgb(`, `rgba(`, `var(--sa-`. Jeder Treffer ≠ 0 ist rot.
- **Weiter gefasst, vom Planner ergänzt:** zähle dieselben vier Formen über die **ganze React-Insel**
  (`resources/planner/hausplaner/app/**`) **außer** `studioDaten.ts`. Erwartung laut Token-Scope-ADR:
  `T` ist die EINE Hex-Stelle. Ist die Zahl > 0, ist das **kein T1-Fehlschlag** (T1 hatte nur
  `HausplanerApp.tsx` im Blick) — aber die Rest-Liste gehört ins Votum, damit sichtbar wird, wie weit
  die Token-Grenze wirklich trägt. Nenne Datei + Anzahl.
- **Kein Bestandswert darf sich geändert haben.** Der Planner hat das bereits read-only gemessen
  (23 Schlüssel alt → 37 neu, `comm` auf Schlüssel:Wert-Paaren, Schnittmenge unverändert, `brand`
  bleibt `#7fae1c`). **Wiederhole es unabhängig** — mit deinem eigenen Verfahren, nicht mit meinem.
  Ein geänderter Bestandswert verschiebt die gesamte Oberfläche und wäre rot.
- **Kein neues Grün.** Insbesondere darf `#93c21c` nirgends auftauchen. Die Grünton-Frage ist als
  Variante (a) entschieden (Planner-Entscheid, Yama-Bestätigung offen) — „entschieden" heißt hier:
  **kein drittes Grün**, nicht „neues Token gebaut".

## 4. Kontrast — nachrechnen, nicht übernehmen

Behauptet: `ink/brand` 5,51 · `brandInk/brandSoft` 6,03 · `okInk/okSoft` 4,77 ·
`warnInk/warnSoft` 4,81 · `errInk/errSoft` 5,91 · `muted/surface` 5,01 · `controlBorder/surface` 3,08.

Rechne **jedes Paar selbst** nach der WCAG-2.1-Formel (relative Luminanz mit sRGB-Linearisierung,
`(L1+0.05)/(L2+0.05)`). Schwellen: Text **4,5:1**, Großtext und UI-/Fokus-Konturen **3:1**.
Abweichung > 0,05 gehört ins Votum. Ein Wert unter der Schwelle ist rot — auch knapp.

Zusätzlich, weil Farbe allein nie Zustand transportieren darf (WCAG 1.4.1): belege, dass das
**aktive Werkzeug** nicht nur farblich markiert ist (heute: Hintergrund **und** Schriftschnitt).

## 5. `decke → bau` — die Fachänderung, die den A1-Anker berührt

Das ist der heikelste Punkt, weil hier der **Regressionsanker aus A1 geändert wurde** — genau der
Test, auf dem die A1-Abnahme ruhte (`__tests__/toolPresentation.test.ts`, „Regressionsanker:
faehigkeitenNach(werkzeuge) …"). Deshalb:

1. Miss `faehigkeitenNach('werkzeuge')` und `faehigkeitenNach('bau')` **vorher** (`c0ffe31`) und
   **nachher**, als Liste **in Reihenfolge**. Erwartung: `werkzeuge` verliert **genau** `decke`,
   behält die übrigen **18** ids in unveränderter Reihenfolge; `bau` gewinnt **genau** `decke`.
   Behauptet: `bau = ["wand","decke","engine-uwert"]` — prüfe auch die **Position** von `decke`.
2. **Alle anderen Gruppen** (`dach-zimmerei`, `tga-heizung`, `energie-pv`, `sanitaer`, `kueche`,
   `fenster-tuer`, `treppe`) müssen **unverändert** sein. Eine mitgewanderte id wäre rot.
3. **Gegen-Beweis, selbst nachstellen:** setze den Ankertest auf die `c0ffe31`-Fassung zurück
   (mit `decke` in `werkzeuge`) und führe ihn gegen den neuen Code aus. Er **muss** rot werden.
   Wird er grün, fängt der Anker die Verhaltensänderung nicht mehr — dann ist die Verriegelung
   wertlos und der Punkt rot. Datei danach zurücksetzen, `git diff` leer.
4. **Zonen unberührt:** `decke` bleibt in Zone `fix` (Leiste), es ändert sich nur die
   Fähigkeiten-**Gruppe**. Beleg: `zoneTools('fix')` weiterhin 7 ids, Reihenfolge gleich.

## 6. Beleg-Hygiene (Planner-Befund, ausdrücklich mitprüfen)

Im Ankertest steht als Begründung „nach Yamas Fachentscheidung". **Yama hat nicht entschieden** —
die Entscheidung hat der Planner in seiner Vertretung getroffen und ausdrücklich als widerrufbar
gekennzeichnet. Ein Kommentar, der eine Freigabe behauptet, die es nicht gibt, wird in einem halben
Jahr als Beleg gelesen. **Prüfe, dass die Formulierung korrigiert ist** (z. B. „Planner-Entscheid
25.07., Bestätigung durch Yama offen"). Ist sie es nicht: rot, mit Verweis auf diesen Punkt.

## 7. Guardrails — byte-genau

Gegen `c0ffe31` unverändert: `tools/toolPresentation.ts`, `tools/toolRegistry.ts`,
`tools/toolTypes.ts`, `tools/activation.ts`, `tools/toolCatalog.ts`, `app/state/uiState.ts`,
`domain/*`, `geometry/*`, `renderers/*`, PHP (`app/Services/*`).
Erwartete Änderungen **nur**: `HausplanerApp.tsx`, `studioDaten.ts`, `tools/faehigkeiten.ts`,
`__tests__/faehigkeiten.test.ts`, `__tests__/toolPresentation.test.ts`,
`public/hausplaner/hausplaner.js`. **Jede weitere geänderte Datei ist ein Befund.**

Bundle: nach dem Commit `build:hausplaner` fahren und belegen, dass der **committete** Bundle mit
dem **frisch gebauten** übereinstimmt (Hash + `git diff --exit-code`). Behauptet:
SHA-256 `cadc4308361bf6e025d42d418cd001184be8cdfc3dd7f890ffa2eb37f249d011`.

## 8. Was du NICHT tust

Kein `main`-Merge, kein Deploy (Tor 2 = Yama). Kein Push zu `upstream`. Keine Code-Korrektur.
Den Grünton **nicht** neu entscheiden — nur belegen, dass kein drittes Grün entstanden ist.

## 9. Votum

Block `## ⇒ EVALUATOR-VOTUM — T1 + decke` mit: grün/rot in Zeile 1 · vier Gate-Exit-Codes und beide
Testzahlen · die vier Roh-Farb-Zählungen (Datei und Insel) · die eigene Token-Bestandsprüfung ·
die sieben selbst gerechneten Kontrastwerte · die vier Punkte aus §5 inkl. rot gesehenem Ankertest ·
Beleg-Hygiene §6 · die Guardrail-Diffs und die vollständige Liste geänderter Dateien · Bundle-Urteil ·
was offen bleibt und an wen der Ballbesitz geht.

---

## 10. NACHTRAG (Planner, 25.07. 09:0x) — Yamas bindende Ergänzungen E1/E2 gelten auch hier

Yama hat im Ledger zwei Ergänzungen als **bindend** gesetzt (Block „EVALUATOR (frische Instanz) —
ZWEI ERGÄNZUNGEN ZUR A1-ABNAHME"). Sie sind dort für die A1-Wiederholung formuliert, gelten der
Sache nach aber für **jede** Abnahme, also auch für diese. Sie ergänzen §§1–9, ersetzen nichts.

**E1 — erst messen, dann lesen.** Erhebe deine eigenen Zahlen, **bevor** du den Generator-Bericht
zu T1 (Ledger, Block `⇒ GENERATOR-BERICHT — T1 Token-Konsolidierung + Fachzuordnung decke → bau`)
liest. Das betrifft hier besonders die **sieben Kontrastwerte** und die **Token-Zählungen** — wer
„5,51" vorher liest, rechnet auf 5,51 zu. Im Votum ist **ausdrücklich anzugeben, in welcher
Reihenfolge du gelesen und gemessen hast**. Wer die Reihenfolge nicht mehr trennen kann, schreibt
das hin, statt es zu glätten.

**E2 — voller Prüfrahmen, nicht nur die Punkte dieses Auftrags.** Gehe `~/.claude/skills/
governance-zyklus/references/pruefrahmen.md` **§2 vollständig** durch (dort **neun** nummerierte
Punkte — Yamas Formulierung „zehn" zählt sinngemäß den **Wächter-Durchlauf §3** mit; fahre beides,
dann bist du in jedem Fall vollständig). Jeder Punkt wird abgehakt **oder** als „n.z." **mit
Begründung** markiert. Ausdrücklich benannt, weil bei A1 undokumentiert geblieben:

- **P6 Bestandsdaten** — T1 fasst Farben und eine Gruppenzuordnung an. Erwartung: **0** PHP,
  **0** Migrationen, **0** Zod-/Schema-Änderung, kein persistierter Wert. Selbst per
  `git show --name-only <SHA>` widerlegen versuchen, nicht annehmen.
- **P7 Nahtstellen** — sitzt die Änderung genau dort, wo §7 dieses Auftrags sie erlaubt (die sechs
  Dateien), und **nicht** darüber hinaus? Ist ein Erweiterungspunkt vorzeitig gebaut worden
  (z. B. Pin/Store-Feld, das erst nach A2 dran wäre)? Beides ist ein Befund.
- **P9 Code-Gesundheit** — „korrekt und langsam ist nicht grün". Der Generator hat hierzu selbst
  einen Punkt gemeldet (`zoneToolsIn` allokiert pro Aufruf über alle 63 Regeln); der betrifft
  **A2**, nicht T1. Prüfe für T1 die eigene Dimension: erzeugt die Token-Umstellung neue
  Berechnungen im Render-Pfad, oder sind es reine Konstanten-Zugriffe auf `T`?

**P8 Funktionstest durch den echten Stack** darf hier begründet „n.z." sein (reine React-Insel,
kein HTTP-Pfad) — aber **nur mit dieser Begründung im Votum**, nicht durch Weglassen.
