# ⇒ GENERATOR-AUFTRAG AUF-36 — Funktionsvertrag der 110 Werkzeuge einhängen

**Vom:** Planner · **25.07.2026** · **Nachgereicht.** Der Generator hat AUF-36 mit `ebffad7` gezogen
und dabei offengelegt, dass **keine Auftragsdatei existiert** und er die Kriterien sonst selbst
setzen müsste. Das ist meine Bringschuld, nicht seine Improvisationspflicht — hier ist sie.

**Vorher gelesen:** HEAD `ebffad7` · `git log -5` · Tafelzeile AUF-36 (§3a, ⚡AKTIV) ·
`app/tools/activation.ts:87` · `commands/applyCommand.ts:141` · `domain/commands.types.ts:70` ·
`app/tools/paketAdapter.ts` (176 Z.) · Paket `hausplaner_svg_tool_functions.zip` (`src/functions/*.ts`).

**Wenn der Generator schon anders begonnen hat:** Dieser Auftrag ersetzt nichts, was bereits belegt
gebaut ist. Weicht sein Schnitt ab und ist er begründet, gilt seiner — dann bitte die Abweichung im
Bericht benennen. Was hier **nicht** verhandelbar ist, sind §3 (was nicht gebaut wird) und §5.

---

## 1. Ziel in einem Satz

Die 110 Werkzeuge tragen ab sofort ihren **Funktionsvertrag** als Daten — Vorbedingungen, Command-Id,
Undo-Fähigkeit, Audit-Pflicht, Seiteneffekte — damit ein gesperrtes Werkzeug **sagen kann, warum**,
statt still nichts zu tun.

## 2. Gemessener Bestand (nicht neu bauen, was da ist)

| Datei | Zeilen | Was sie heute schon tut |
|---|---|---|
| `app/tools/activation.ts:87` | — | `resolveToolState(tool, ctx)` — **die** Aktivierungs-Engine. Reihenfolge Workspace → Ansicht → Rechte → Fähigkeiten → Auswahlanzahl → Auswahltyp → freie Regeln; die **erste verletzte Bedingung liefert den Grund** |
| `app/tools/paketAdapter.ts` | 176 | bildet das Paket auf `ToolDefinition` ab. Kennt heute **keines** der Vertragsfelder (`grep` auf `commandId\|precondition\|sideEffect\|undoable\|auditRequired` = 0 Treffer) |
| `commands/applyCommand.ts:141` | — | führt aus. 19 Command-Typen, Undo über inverse Immer-Patches |
| `domain/commands.types.ts:70` | — | `CommandAbgelehnt` — wird **vor** jeder Mutation geworfen |
| `app/tools/werkzeugPaket.ts` | 439 | die 110 Werkzeuge, IDs seit AUF-31 deutsch |

**Vokabular im Paket, ausgezählt** (110 Werkzeuge):

```
preconditions          sideEffects                        Flags
110  project.open       77 autosave.markDirty              undoable      true 77 / false 33
 69  permission.edit    69 renderer.refreshAffectedObjects auditRequired true 92 / false 18
 40  activeLevel.exists 69 model.revision.increment
 29  selection.count>=1 69 dependentResults.invalidate
  8  permission.import  15 workflowState.update
  5  viewport.ready     15 auditLog.append
  3  selection.hasRoofFace   8 referenceLayer.update
  2  hostWall.exists         7 viewport.update
  1  heatingNetwork.connected    5 measurementOverlay.update
  1  heatingLoad.approved        4 ui.selection.update
  1  heatEmitters.sized          4 propertiesPanel.refresh
  1  component.thermalRelevant
```

## 3. Was **nicht** gebaut wird — die drei Grenzen

**(a) Keine zweite Aktivierungs-Engine.** Das Paket bringt `tool-engine.ts` mit einem eigenen
`runTool(...)` mit. Es wird **nicht** übernommen. Die zwölf Vorbedingungen werden **Daten** für
`resolveToolState` — kein `resolveDisabledReasons` daneben. Zwei Wahrheiten über „darf ich klicken"
ist derselbe Fehler, den I2/I3 beim Katalog vermieden haben.

**(b) Keine zweite Ausführungsschicht.** `commandId` ist ein **Metadatum am Werkzeug**, kein Aufruf.
Ausgeführt wird weiter über `applyCommand`. Wer am Paket-Adapter vorbei ausführt, verliert Undo
(inverse Patches) und `CommandAbgelehnt` — beides ist nicht ersetzbar und nicht nachrüstbar.

**(c) Kein erfundener Kontext.** Vier Vorbedingungen (`heatingNetwork.connected`,
`heatingLoad.approved`, `heatEmitters.sized`, `component.thermalRelevant`, je 1×) hängen an
Ergebnissen der Fach-Engines, **die es heute nicht gibt**. Sie werden **nicht** auf `true`
verdrahtet, damit die Kachel klickbar aussieht. Sie werden als **unerfüllt mit ehrlichem Grund**
geführt — genau das ist der Zweck des Vertrags. Blindtext-Verbot wie in AUF-25.

## 4. Schnitt — in dieser Reihenfolge

1. **Vertragsfelder ans Werkzeug.** `paketAdapter.ts` trägt `commandId`, `preconditions`,
   `sideEffects`, `undoable`, `auditRequired`, `serviceMethod` additiv weiter. **Kein vorhandenes
   Feld von `ToolDefinition` ändert seine Bedeutung** (Konflikt-Regel aus I2).
2. **Zuordnungstabelle Vorbedingung → vorhandene Regel.** Jede der zwölf bekommt genau eine Zeile:
   auf welche bestehende Regelart sie abgebildet wird, oder — falls der Kontext heute fehlt — welches
   Feld in `AktivierungsKontext` neu dazukommt. `permission.edit`/`permission.import` gehen auf
   `requiredPermissions`, `selection.count >= 1` auf die Auswahlanzahl-Regel mit `greater-than`,
   `selection.hasRoofFace` auf Auswahltyp. Die Tabelle gehört in den Bericht, nicht nur in den Code.
3. **Gründe auf Deutsch, werkzeugbezogen.** „Kein aktives Geschoss." schlägt „Vorbedingung
   `activeLevel.exists` nicht erfüllt." — der Nutzer liest den Grund, nicht das Vokabular.
4. **Tests.** Je Vorbedingung mindestens ein Fall erfüllt / ein Fall verletzt, und der **Grund** wird
   mitgeprüft, nicht nur das Boolean.

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Testzahl vorher/nachher
   genannt, **keine verschwundenen Tests**.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen Diff.
3. **Genau eine Aktivierungs-Engine:** `grep` belegt, dass außer `resolveToolState` keine zweite
   Funktion einen Sperrgrund erzeugt.
4. **Genau eine Ausführungsschicht:** `runTool` aus dem Paket kommt im Repo **nicht** vor.
5. **Bijektion hält:** 9 + 101 = 110, `verwaisteRegeln()` leer, GEGENPROBE (erfundene id → verwaist).
6. **Alle zwölf Vorbedingungen zugeordnet** — die Tabelle aus §4.2 liegt im Bericht, keine Zeile
   „sonstige".
7. **Die vier Fach-Vorbedingungen sind unerfüllt und begründet** — testverriegelt, dass ihr Grund
   weder leer ist noch auf „folgt"/„in Kürze" endet (Muster aus AUF-25).
8. **Mutations-Gegenbeweis:** eine Vorbedingung entfernen ⇒ mindestens ein Test rot. Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen.** Der **Bundle-Rebuild ist ein eigener, zweiter Commit**
   unmittelbar danach, ausschließlich mit dem Artefakt — er ist nicht optional, sondern Teil der
   Lieferung (§8 Punkt 2b in `docs/agents/06-laufzeiten-und-takt.md`). *Diese Formulierung ersetzt das
   alte „null Zeilen in `public/*`", das den Rebuild wörtlich verboten und dreimal das Bundle-Loch
   erzeugt hat.*
10. **Klassifikation: `sichtbar`.** Werkzeuge, die heute klickbar aussehen, werden künftig mit Grund
    gesperrt — das sieht Yama. Also: Rebuild-Beleg (`grep -c` auf eine neue Zeichenkette) im Bericht,
    und die Sichtprobe gehört in die Abnahme, nicht danach.

## 6. Was zurückgegeben wird statt heimlich mitgebaut

Taucht beim Zuordnen auf, dass eine Vorbedingung einen Kontext braucht, den zu bauen ein eigener
Posten wäre (Beispiel: `viewport.ready` verlangt einen Renderer-Zustand, den der Store nicht führt) —
**zurückgeben, benennen, nicht mitbauen.** Kein Beifang. Der Posten wandert auf die Tafel.
