# GENERATOR-AUFTRAG — Wizard-Welle A1: Werkzeug-Präsentationsschicht (kuratieren als DATEN)

**Rolle:** Planner (vertritt Yama) · **Heimat-App:** ticket · **Branch:** `auto/hausplaner-integration` (Basis `9bcc9c3`)
**Datum:** 2026-07-25 · **Ballbesitz nach Umsetzung:** Evaluator

---

## 0) Gemessener Bestand (Beweis statt Behauptung — vor dem Auftrag am Code erhoben)

| Beleg | Messung |
|---|---|
| `app/tools/toolRegistry.ts` → `TOOL_DEFINITIONS` | **9 Einträge**: 7 × `art:'werkzeug'` (auswahl, wand, fenster, tuer, dach, decke, treppe) + 2 × `art:'aktion'` (loeschen, duplizieren) |
| `app/HausplanerApp.tsx:798` | `werkzeugTools().map(...)` — **die Werkzeugleiste rendert AUSSCHLIESSLICH aus der Registry** (7 Icons), heute als Liste in der linken Spalte (L1, 220 px) |
| `app/tools/toolCatalog.ts` → `TOOL_KATALOG` | **54 Einträge** (`grep -c` = 54). Der Dateikopf behauptet „DTP/Druck-Tools bewusst NICHT enthalten" — **das ist nachweislich falsch**: enthalten sind u. a. `type` (Textwerkzeug), `page` (Seitenwerkzeug), `rectangle-frame`/`ellipse-frame`/`polygon-frame` (Platzhalterrahmen), `preflight`, `swatches-panel` (Farbfelder), `pages-panel` (Seiten/Musterseiten), `object-style`, `note` (redaktionelle Notiz), `pen`/`pencil`/`smooth`/`erase-path`/`add-anchor`/`delete-anchor`/`convert-anchor`/`scissors` (Bézier), `fill`/`stroke`/`swap-fill-stroke`/`default-fill-stroke`, `normal-screen`/`preview-screen`, `format-container` |
| Konsumenten von `TOOL_KATALOG` | **nur** `app/tools/faehigkeiten.ts` (über `katalogTool`, Liste `CAD_TEILMENGE` = **15 ids**) + `__tests__/toolKatalog.test.ts`. **Kein einziger Katalog-Eintrag steht in der Werkzeugleiste.** |
| Shortcut-Kollisionen im Katalog | gemessen: `F`×6, `X`×4, `W`×4, `\`/`Z`/`V`/`U`/`T`×2 → ein ungefiltertes Hochheben in die Toolbar erzeugt sofort Tastatur-Konflikte. Die **Registry selbst ist kollisionsfrei** (V,W,F,T,D,K,R,Delete,Ctrl+D) |
| Deaktivierungs-Grund | existiert bereits: `activation.ts` → `resolveToolState(tool, ctx): WerkzeugZustand { enabled, reason }`, gespeist aus `ToolActivationRule.grund` (`toolTypes.ts`) |
| Anzeige-Metadaten | existieren bereits additiv in `ToolDefinition`: `meaning`, `usageArea`, `group`, `tooltip{title,body,usage,shortcut}` (UI-3b) |

**Konsequenz aus der Messung (korrigiert eine frühere Planner-Annahme):** Es gibt **keine** „54 Werkzeuge in
der Werkzeugleiste, die kuratiert werden müssen". Es gibt **zwei getrennte Listen** und **eine bereits
existierende, aber versteckte Kuratierung** (`CAD_TEILMENGE` in `faehigkeiten.ts`), die nur die Navi speist.
Kuratieren heißt hier deshalb **nicht löschen**, sondern: die Kuratierung aus dem Nebensatz einer
UI-Datei in **eine benannte, getestete Datenschicht** heben.

---

## 1) Ziel & Entscheidung (die eine Festlegung)

**Es entsteht eine additive Präsentationsschicht `app/tools/toolPresentation.ts`, die für JEDES Werkzeug
(Registry ODER Katalog) genau eine Zone und eine Begründung festlegt. Die Werkzeug-Wahrheit selbst
(`TOOL_DEFINITIONS`) und die Aktivierungs-Wahrheit (`resolveToolState`) bleiben unangetastet.**

Präzisierungen:
1. **Kein Eintrag wird aus `toolCatalog.ts` gelöscht.** Der Katalog bleibt Herkunfts-/Metadatenquelle; die
   Kuratierung ist eine **Regel**, kein Datenverlust. (Rückweg bleibt offen, falls Yama ein Werkzeug doch will.)
2. **Kein Katalog-Werkzeug wird in A1 in die Werkzeugleiste gehoben.** Welche CAD-Werkzeuge ein Bauplaner
   wirklich braucht, ist eine **Fachentscheidung von Yama** (bauplaner-3d Regel 4) — A1 baut nur den Mechanismus
   und legt den gemessenen Ist-Zustand als Default-Regeln ab.
3. **Kein UI-Umbau in A1.** `HausplanerApp.tsx` wird nicht angefasst. Das Rendern der Zonen (Rail
   Fix/Kontext/Weitere) kommt in A2/A3 zusammen mit Pin-Persistenz — eine halbe Rail ohne Anheften wäre eine
   zweite, widersprüchliche Wahrheit.

## 2) Umzusetzen

### A1.1 — neue Datei `resources/planner/hausplaner/app/tools/toolPresentation.ts` (reine Daten + reine Funktionen, kein React)
```ts
export type RailZone = 'fix' | 'kontext' | 'weitere' | 'versteckt';
export type ToolHerkunft = 'registry' | 'katalog';

export interface ToolPresentationRule {
  toolId: string;
  zone: RailZone;
  /** Anzeigereihenfolge innerhalb der Zone (aufsteigend, stabil). */
  ordnung: number;
  herkunft: ToolHerkunft;
  /** Warum diese Zone — erscheint im Konfigurationsdialog (A3) und in der Abnahme. */
  begruendung: string;
}
export const TOOL_PRESENTATION_RULES: readonly ToolPresentationRule[];
export function praesentation(toolId: string): ToolPresentationRule | undefined;
/** Löst die ids einer Zone gegen Registry (Vorrang) und Katalog auf. Unbekannte id ⇒ ausgelassen, kein throw. */
export function zoneTools(zone: RailZone): ToolDefinition[];
/** ids in Regeln, die weder in der Registry noch im Katalog existieren (muss leer sein). */
export function verwaisteRegeln(): string[];
```
Default-Regelsatz (exakt der gemessene Ist-Zustand, keine neue Fachentscheidung):
- die **7** `art:'werkzeug'`-ids der Registry → `zone:'fix'`, `herkunft:'registry'`, `ordnung` = heutige
  Registry-Reihenfolge (auswahl, wand, fenster, tuer, dach, decke, treppe), Begründung „Bau-Werkzeug, Bestand".
- die **2** `art:'aktion'`-ids (loeschen, duplizieren) → `zone:'kontext'`, Begründung „Sofortbefehl auf die Auswahl".
- die **15** ids der heutigen `CAD_TEILMENGE` → `zone:'weitere'`, `herkunft:'katalog'`, Begründung
  „CAD-tauglich, Handler folgt (A2/A3)".
- die **39** übrigen Katalog-ids (54 − 15) → `zone:'versteckt'`, Begründung
  „DTP/Layout — kein Bau-Werkzeug (Produkt-Scope)". Jede dieser ids wird **einzeln aufgeführt**, keine
  Negativ-Ableitung zur Laufzeit (die Liste ist der prüfbare Beweis).

### A1.2 — `faehigkeiten.ts`: Kuratierung aus der neuen Schicht lesen
Die lokale Konstante `CAD_TEILMENGE` entfällt; die Datei bezieht ihre 15 ids aus
`zoneTools('weitere')` bzw. aus den Regeln. **Verhalten muss identisch bleiben** (gleiche ids, gleiche
Reihenfolge, gleiche `id: 'cad-<id>'`-Bildung). Damit gibt es **eine** Kuratierungs-Wahrheit statt zweier
Stellen (Katalog-Kopfkommentar + `CAD_TEILMENGE`).

### A1.3 — Kopfkommentar in `toolCatalog.ts` korrigieren
Die Behauptung „DTP/Druck-Tools bewusst NICHT enthalten" ist falsch und hat schon einmal zu einer falschen
Planner-Annahme geführt. Neu, wahrheitsgemäß: „Rohbestand aus dem 65-Tool-Paket (54 Einträge, inkl. DTP).
Was davon angezeigt wird, entscheidet `toolPresentation.ts` — diese Datei filtert nicht." **Nur Kommentar,
keine Datenänderung.**

### A1.4 — Tests `resources/planner/hausplaner/__tests__/toolPresentation.test.ts`
1. **Vollständigkeit:** jede id aus `TOOL_DEFINITIONS` (9) und aus `TOOL_KATALOG` (54) hat genau eine Regel;
   Summe der Regeln = 63; keine doppelte `toolId`.
2. **`verwaisteRegeln()` ist leer** (Rot-Gegenprobe: eine erfundene id in einer lokalen Kopie der Regeln ⇒
   `verwaisteRegeln()` liefert sie ⇒ Test würde rot).
3. **Invariante Fix-Zone:** `zoneTools('fix')` enthält genau die 7 `art:'werkzeug'`-Registry-ids in
   Registry-Reihenfolge; **keine** Registry-id liegt in `versteckt` (Gegenprobe: `wand` auf `versteckt`
   gesetzt ⇒ Test rot).
4. **Kuratierungs-Beweis:** `zoneTools('versteckt')` enthält mindestens `type`, `page`, `preflight`,
   `swatches-panel`, `pages-panel`, `rectangle-frame`, `pen`, `note`, `object-style`; Anzahl `versteckt` = 39.
5. **Regressionsanker Navi:** `faehigkeitenNach('werkzeuge')` liefert **exakt dieselben ids in derselben
   Reihenfolge** wie vor der Änderung (Liste im Test hart hinterlegt) — Beweis, dass A1.2 verhaltensneutral ist.
6. `doppelteIds()` aus `faehigkeiten.ts` bleibt leer.

## 3) Nahtstellen — und wo bewusst NICHT

- **Berührt:** neue Datei `toolPresentation.ts`; `faehigkeiten.ts` (nur Bezugsquelle der 15 ids);
  Kopfkommentar `toolCatalog.ts`; neue Testdatei.
- **Bewusst NICHT berührt:** `activation.ts`, `toolTypes.ts` (Semantik), `toolRegistry.ts` (Datenbestand),
  `toolContext.ts`, `HausplanerApp.tsx`, `FaehigkeitenNavi.tsx`, `domain/*`, `geometry/*`, `renderers/*`,
  alles PHP.
- **Erweiterungspunkt für A2 (Pinnen):** `ToolPresentationRule` ist der System-Default. Die persönliche
  Ebene (`UserPinnedTool`/`UserToolLayout`) legt sich in A2 **darüber**, sie ersetzt diese Datei nicht —
  Auflösung wird dann `persönlich → Workspace-Preset → System-Default` sein. In A1 nur den Default bauen.

## 4) Kantenliste (hier bricht es erfahrungsgemäß)

1. Regel-id existiert weder in Registry noch Katalog → `zoneTools` überspringt, `verwaisteRegeln()` meldet.
2. id-Kollision Registry ↔ Katalog (z. B. Registry `auswahl` vs. Katalog `selection` — heute **verschiedene**
   ids, das darf nicht durch „Vereinheitlichung" heimlich zusammengeführt werden). Bei echter Namensgleichheit
   gilt: **Registry hat Vorrang**, Katalog-Eintrag wird ignoriert — und der Test macht das sichtbar.
3. Leere Zone (`kontext` ohne Auswahl) → leeres Array, kein `undefined`, kein Absturz.
4. Reihenfolge: `ordnung` muss stabil und lückentolerant sein (Sortierung nach `ordnung`, bei Gleichstand
   nach Regel-Index — nicht nach `id`, sonst springt die Leiste).
5. Shortcut-Kollisionen: Katalog-Tools in `weitere` dürfen **keine** Tastenkürzel beanspruchen, solange sie
   nicht in der Toolbar sind (`toolFuerShortcut` liest weiterhin nur die Registry — **nicht ändern**).
6. **Gemessene Unstimmigkeit, NICHT eigenmächtig beheben:** in `faehigkeiten.ts` fehlt `decke` in
   `WERKZEUG_GRUPPE` und fällt auf `'werkzeuge'`, während `wand → 'bau'` und `dach → 'dach-zimmerei'` gehen.
   Vermutlich gehört `decke → 'bau'`. **Das ist eine Fachzuordnung → Frage an Yama, in A1 unverändert lassen**
   (und im Bericht als offenen Punkt melden).

## 5) Abnahmekriterien (der Evaluator misst selbst nach)

- `npm run tsc:hausplaner` → 0 Fehler. `npm run schema:hausplaner:check` → grün (es wird **keine** Zod-Änderung
  erwartet; falls doch eine nötig wird: `npm run schema:hausplaner` **zwingend**, sonst 422).
- `npm run test:hausplaner` → grün, Testanzahl ≥ bisheriger Stand (684) + die neuen Fälle.
- `npm run build:hausplaner` → Exit 0.
- Zahlen-Beweis im Bericht: `TOOL_PRESENTATION_RULES.length` = 63 · `zoneTools('fix').length` = 7 ·
  `zoneTools('kontext').length` = 2 · `zoneTools('weitere').length` = 15 · `zoneTools('versteckt').length` = 39 ·
  `verwaisteRegeln()` = `[]`.
- Gegen-Beweis (mindestens diese zwei, als tatsächlich rot gesehene Läufe dokumentieren): (a) `wand` auf
  `versteckt` ⇒ Invarianten-Test rot; (b) erfundene id in den Regeln ⇒ `verwaisteRegeln()`-Test rot.

## 6) Guardrails

- **Keine zweite Wahrheit:** kein neuer Deaktivierungs-Grund-Mechanismus (der ist `resolveToolState`), keine
  Kopie von Werkzeug-Metadaten (die stehen in `ToolDefinition`), keine zweite Kuratierungsliste.
- **Additiv:** keine bestehende Signatur brechen, kein Katalog-Eintrag löschen, kein Verhalten der Navi ändern.
- **Kein Beifang.** Insbesondere: die in `HausplanerApp.tsx` (~Z.796–812) gemessenen **rohen Hex-Werte**
  (`#fff`, `#e5e7eb`, `#9ca3af`) verletzen die Token-Regel (`T` in `studioDaten.ts` = die EINE Hex-Stelle) —
  das ist ein **eigener Planner-Posten**, NICHT Teil von A1. Nicht mitreparieren.
- **BuildingModel-Guardrail** (unverändert gültig): keine neue Hash-/Snapshot-/Version-/Projektions-Klasse.
  Hier ohnehin nicht berührt.
- **Byte-Treue:** `geometry/*` wird nur referenziert, nie geändert.

## 7) Berichtskette

Generator: umsetzen → **Bericht in `docs/handoff-status.md`** (Zahlen aus §5, beide Gegen-Beweise, der offene
Punkt `decke`-Gruppe) → committen → **Ballbesitz Evaluator** ausdrücklich benennen. Der Generator meldet
„umgesetzt", nie „abgenommen". Evaluator: selbst nachmessen, Gegen-Beweis führen, Votum ins Ledger,
Ballbesitz zurück an den Planner.
