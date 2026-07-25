# ⇒ EVALUATOR — AUFTRAG: Abnahme Wizard-Welle A1 (Werkzeug-Präsentationsschicht)

**Von:** Planner · **An:** Evaluator (unabhängige Instanz) · **Stand:** 25.07.2026
**Gegenstand:** Commit `c0ffe31` „Wizard-Welle A1: Werkzeug-Praesentationsschicht (Kuratierung als Daten)"
**Auftrag, gegen den geprüft wird:** `docs/auftraege/generator-auftrag-wizard-welle-a1-werkzeug-praesentation.md`
**Generator-Bericht:** `docs/handoff-status.md`, Block `⇒ GENERATOR-BERICHT (nativ) — Wizard-Welle A1 UMGESETZT`
**Heimat-App:** ticket · **Ballbesitz nach Votum:** zurück an Planner

---

## 0. Grundhaltung (bitte wörtlich nehmen)

Der Generator hat sauber und detailliert berichtet — **das ist kein Grund, ihm zu glauben.** Die Zahlen
unten sind **Behauptungen**, bis du sie selbst erzeugt hast. Kein Punkt gilt als geprüft, weil er im
Bericht steht. **Selbst ausführen, selbst zählen, Gegen-Beweis führen.** Wenn eine Zahl abweicht: rot,
mit Beleg — nicht „vermutlich Rundung".

Du nimmst ab, du merzt nicht aus: **kein Code-Fix durch den Evaluator.** Findest du etwas Rotes, wird es
als Befund zurückgegeben, nicht selbst repariert.

---

## 1. Gates — selbst ausführen, Exit-Codes notieren

```
npm run tsc:hausplaner
npm run schema:hausplaner:check
npm run test:hausplaner
npm run build:hausplaner
```

Behauptung des Generators: alle vier **Exit 0**, `test:hausplaner` **695/695 pass, 0 fail** (vorher 684, +11).

Prüfe zusätzlich, **dass die Zahl 684 stimmt**: Testlauf auf `HEAD~1` (`3229866`) in einer temporären
Arbeitskopie oder per `git worktree add`. Wenn du den Vorher-Stand nicht ausführst, schreibe das
ausdrücklich als „nicht verifiziert" hin — **nicht** als grün durchwinken.

Kein Zod im Auftrag berührt → `schema:hausplaner:check` muss grün sein **ohne** Regen. Falls du regenerieren
musst, damit es grün wird, ist das **rot** (dann fehlte ein Schema-Regen im Commit).

---

## 2. Die sieben Abnahme-Zahlen — selbst nachzählen

Nicht aus dem Testlauf ableiten, sondern aus dem Modul selbst erzeugen (z. B. per kleinem `tsx`/node-Skript
gegen `app/tools/toolPresentation.ts`):

| Größe | behauptet |
|---|---|
| `TOOL_PRESENTATION_RULES.length` | **63** |
| `zoneTools('fix').length` | **7** |
| `zoneTools('kontext').length` | **2** |
| `zoneTools('weitere').length` | **15** |
| `zoneTools('versteckt').length` | **39** |
| `verwaisteRegeln()` | **[]** |
| `regelloseWerkzeuge()` | **[]** |

Quer-Prüfung, die der Generator **nicht** genannt hat und die du selbst rechnen sollst:
7 + 2 + 15 + 39 = **63** — geht die Summe auf, und ist die Vereinigung von `TOOL_DEFINITIONS` (9) und
`TOOL_KATALOG` (54) **genau 63 verschiedene ids**, oder gibt es id-Überschneidungen zwischen Registry und
Katalog, die die Summe zufällig retten? Zähle `new Set([...registryIds, ...katalogIds]).size` selbst.
Wenn es Überschneidungen gibt, prüfe, dass `zoneTools` sie **einmal** liefert (Registry-Vorrang), nicht doppelt.

---

## 3. Gegen-Beweise — beide selbst nachstellen (nicht den Bericht zitieren)

Der Generator behauptet, beide Gegenproben **tatsächlich rot** gesehen zu haben:

- (a) `wand` → `zone:'versteckt'` gesetzt ⇒ **5 von 11** Tests rot.
- (b) erfundene id `erfunden-xyz` in den Regeln ⇒ **3 von 11** Tests rot.

Stelle beide selbst her (Datei vorher kopieren, danach per Kopie zurück, `git diff` muss am Ende **leer**
sein) und notiere die **tatsächlich** roten Testnamen. Weicht die Anzahl ab, ist das nicht automatisch rot —
aber die Abweichung gehört ins Votum.

**Dritter Gegen-Beweis, den du zusätzlich führst (vom Planner ergänzt):** entferne eine beliebige id aus
`TOOL_PRESENTATION_RULES` (z. B. eine `versteckt`-id). Erwartung: `regelloseWerkzeuge()` ist nicht mehr leer
und mindestens ein Test wird rot. Wird er **nicht** rot, deckt die Testsuite den Vollständigkeits-Anspruch
nicht ab → **rot**.

---

## 4. Verhaltens-Gleichheit (der eigentliche Risikopunkt von A1.2)

`faehigkeiten.ts` liest die 15 ids jetzt aus `zoneTools('weitere')` statt aus der lokalen `CAD_TEILMENGE`.
Der Generator behauptet, `faehigkeitenNach('werkzeuge')` liefere **dieselben 19 ids in gleicher Reihenfolge**
wie vorher, und er habe das nicht abgeleitet, sondern die alte Datei aus `git show HEAD:…` **ausgeführt**.

Führe das **unabhängig** aus: hole `git show 3229866:resources/planner/hausplaner/…/faehigkeiten.ts`,
lass beide Fassungen laufen, vergleiche die Listen **als Reihenfolge**, nicht als Menge. Prüfe dabei auch
die anderen Gruppen (`bau`, `dach-zimmerei`, …), nicht nur `'werkzeuge'` — der Bericht spricht nur über eine
Gruppe. Ist irgendeine andere Gruppe gewandert, ist das eine unberichtete Verhaltensänderung → **rot**.

---

## 5. Guardrails — byte-genau prüfen, nicht nach Bericht

Alle folgenden Dateien müssen gegenüber `3229866` **unverändert** sein. Beleg per
`git diff 3229866 c0ffe31 -- <pfad>` (leer = grün):

- `resources/planner/hausplaner/app/HausplanerApp.tsx` (die rohen Hex-Werte bleiben bewusst drin → Posten T1)
- `app/tools/toolTypes.ts`, `activation.ts` (`resolveToolState`), `toolRegistry.ts`, `toolContext.ts`
- `__tests__/toolKatalog.test.ts`
- `domain/*`, `geometry/*`, `renderers/*`, PHP (`app/Services/*`)

Zusätzlich inhaltlich:
- **`TOOL_DEFINITIONS` unverändert** (9 Einträge, keine id umbenannt).
- **Kein Katalog-Eintrag gelöscht**: `TOOL_KATALOG.length` ist weiterhin **54**; die Änderung in
  `toolCatalog.ts` (13 Zeilen) ist **nur Kommentar** — lies den Diff und bestätige das ausdrücklich.
- **Kein Katalog-Werkzeug in die Leiste gehoben**: keine `herkunft:'katalog'`-Regel steht in Zone `fix`
  oder `kontext`. Selbst filtern, nicht glauben.
- **Kein zweiter Deaktivierungs-Mechanismus** neben `resolveToolState`.

---

## 6. Der gebaute Bundle im Commit (`public/hausplaner/hausplaner.js`, 408 Zeilen)

Der Commit enthält den gebauten Bundle. Das ist in diesem Repo **etablierte Praxis** (`050f55f`, `a1215a3`,
`4cde0be`, `176aa48` enthalten ihn ebenfalls), also kein Regelbruch — aber es ist die einzige Stelle im
Commit, die **niemand gelesen** hat. Deshalb ausdrücklich prüfen:

- `npm run build:hausplaner` **zweimal** laufen lassen und `md5` vergleichen. Behauptung:
  beide Male `be0f864c0b722573acdda978c1e6cd70` (deterministisch, keine Drift).
- Entscheidend: stimmt der **frisch gebaute** Bundle mit dem **committeten** überein (bauen, dann
  `git diff --stat -- public/hausplaner/hausplaner.js`)? Weicht er ab, wurde ein anderer Quellstand
  eingecheckt als der, der im Commit liegt → **rot**.
- Größe 1.287,29 kB → **1.292,69 kB** selbst nachmessen.
- Stichprobe im Diff: enthält der Bundle-Delta **nur** die Präsentationsschicht (Zonen-Strings,
  Begründungen, die 63 ids) — oder auch Fremdes (andere Module, andere Farben, andere Texte)? Ein
  Bundle-Delta, das mehr enthält als der Quell-Delta erklärt, ist ein Befund.

---

## 7. Fachliche Linsen (kurz, mit Beleg)

- **Software-Architekt:** Ist die Kuratierung jetzt **eine** Wahrheit (`TOOL_PRESENTATION_RULES`) oder gibt
  es irgendwo noch eine zweite Liste, die dasselbe entscheidet? Ist die Änderung **additiv** (keine
  Pflichtfelder, kein Schema, keine Migration)? Ist Registry-Vorrang (`toolNach() ?? katalogTool()`) an
  **einer** Stelle implementiert?
- **Frontend:** A1 durfte die UI nicht anfassen — bestätige, dass **kein** UI-Verhalten geändert wurde
  (`HausplanerApp.tsx` byte-gleich, kein neuer Store-State, kein zweiter `activeToolId`). Die 31 rohen
  Hex-Werte in `HausplanerApp.tsx` sind **bekannt und bewusst ausgeklammert** (Posten T1) — nicht als
  A1-Befund werten, aber im Votum als weiter offen führen.
- **Bauplaner:** Sind die 7 Fix- und 2 Kontext-Werkzeuge fachlich **Bau**-Werkzeuge (kein DTP-Rückfall)?
  Nenne die 9 ids im Votum ausdrücklich, damit Yama sie fachlich sehen kann.

---

## 8. Was du ausdrücklich NICHT tust

- **Kein `main`-Merge, kein Deploy** — Tor 2 gehört Yama. Ein echter upstream-/Hetzner-Deploy (3000 Kunden)
  bleibt sein bewusster, separater Schritt.
- **Kein Push zu `upstream`** (`raminsadid2021/nuri-head.git` = fremdes Konto). Push nur `fork` +
  `backup-private`, ausschließlich über `push-integration-sicher.command`, nie `--force`.
- **Keine Code-Korrektur.** Auch nicht „schnell die eine Zeile". Befund → Planner.
- **`decke` in `WERKZEUG_GRUPPE` nicht entscheiden.** Das ist eine offene **Fachfrage an Yama**
  (heute fällt `decke` auf `'werkzeuge'`, während `wand → 'bau'`, `dach → 'dach-zimmerei'`). Nur bestätigen,
  dass der Generator sie korrekt offengelassen hat.

---

## 9. Votum (so hinterlegen, in `docs/handoff-status.md`)

Ein Block `## ⇒ EVALUATOR-VOTUM — Wizard-Welle A1` mit:

1. **Grün oder Rot**, in der ersten Zeile, ohne Weichmacher.
2. Die vier Gate-Exit-Codes, **selbst** erzeugt, mit der Testzahl (und ob 684 verifiziert wurde).
3. Die sieben Zahlen aus §2, **selbst** gemessen, plus die Set-Größen-Querprobe.
4. Die drei Gegen-Beweise aus §3 mit den tatsächlich roten Testnamen.
5. Das Ergebnis des Reihenfolge-Vergleichs aus §4 — **alle** Gruppen, nicht nur `'werkzeuge'`.
6. Die Guardrail-Diffs aus §5 (welche `git diff` leer waren).
7. Das Bundle-Urteil aus §6 (md5, Größe, „Bundle = frischer Build ja/nein", Fremd-Delta ja/nein).
8. Die 9 Fix-/Kontext-ids im Klartext für Yamas Fachblick.
9. Was offen bleibt (T1, `decke`, Push-Stand) und **an wen der Ballbesitz geht**.

**Rot blockiert die nächste Welle** (A2 Pin/Anheften). Bei Grün geht der Ballbesitz an den Planner, der
A2 erdet und T1 nach Yamas Design-Entscheidung als Auftrag schreibt.
