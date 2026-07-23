# Generator-Auftrag — W-3b (#13): Dach-Vorlagen (187) + L/T/U anschließen

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`.
**Basis-Branch:** `auto/hausplaner-w3a` (`b7f83f0`, W-3a grün 623/623). Neuer Branch `auto/hausplaner-w3b`.
**Ausgestellt von:** Planner (Cowork), 2026-07-23.

## ⚠️ ENTSCHEIDUNGS-GATE (vor Ausführung)
Dieser Auftrag **ändert das PERSISTIERTE Schema** (die `roofType`-Enum). Das berührt Live-Daten
(die 422-Klasse). **Nicht ausführen, bevor Yama ausdrücklich „go" sagt.** Alles bleibt additiv
(bestehende 4 Formen unverändert gültig), aber die Änderung ist bewusst review-pflichtig.

## Warum (Befund)
Die Engine ist portiert (W-1/W-2), Gauben sind angeschlossen (W-3a). **Ungenutzt** bleiben:
`dachformVorlagen.ts` (≈187 Vorlagen) und `dachVerschneidung`/`dachUForm` (L/T/U). Grund: `roofType`
kennt nur `sattel|walm|pult|flach`; das Werkzeug hat keinen Vorlagen-Picker. Zusätzlich B1: zwei
Dachform-Vokabulare (Modell 4 Formen vs. Engine-`RoofShape` 7) — **eine Wahrheit** herstellen.

## Stufe 1 — Typ- & Schema-Konsolidierung (#13, review-pflichtig)
1. **Eine `RoofShape`-Wahrheit:** eine getypte Quelle (z. B. `domain/roofShape.ts`) mit
   `'sattel'|'walm'|'pult'|'flach'|'rect'|'l-shape'|'t-shape'|'u-shape'`. Engine (`dachformVorlagen`)
   und Modell referenzieren **dieselbe** — kein „gespiegelter" Zweit-Typ mehr.
2. **Persistierte Enum additiv erweitern:** `roofType` in `scene.types.ts` + Zod `validation.ts` um die
   neuen Werte ergänzen (die 4 alten unverändert → Bestands-Dächer bleiben gültig, **kein 422**).
   Danach zwingend `npm run schema:hausplaner` und das regenerierte `scene-document-v2.schema.json`
   **mitcommitten** (Lehre 970f0cc→aecc517).
3. **Lade-Migration:** alte Dokumente ohne die neuen Werte laufen unverändert; kein Zwang, kein Default-Umbau.
4. **Kontur-Wurf öffnen — kontrolliert:** `pruefeRechteckigeKontur` (`geometry/dachGeometrie.ts`) so
   erweitern, dass nicht-rechteckige Formen (l/t/u) NICHT mehr pauschal werfen, sondern an die
   Verschneidungs-Engine (`dachVerschneidung`/`dachUForm`) übergeben werden. Rechteckige Formen: Verhalten
   unverändert.

Abnahme Stufe 1: Bestands-Szene (4 Formen) lädt + validiert wie bisher; `schema:hausplaner:check` Exit 0;
`test:hausplaner` grün (Anzahl ≥ 623); ein Dach mit `roofType:'l-shape'` validiert jetzt (statt 422).

## Stufe 2 — Vorlagen-Picker + L/T/U-Render (UI + 3D)
5. **Vorlagen im Werkzeug:** das Dach-Werkzeug/den Wizard so erweitern, dass er die **`dachformVorlagen`**
   (≈187) anbietet — gruppiert/filterbar (nicht 187 flach). Auswahl schreibt Form + Parameter ins Modell
   (`ADD_ROOF`/`UPDATE_ROOF`), **eine Wahrheit** (kein zweiter Vorlagen-Satz neben `dachVorlage.ts` —
   `dachVorlage.ts` entweder ablösen oder als Teilmenge von `dachformVorlagen` führen).
6. **3D-Render L/T/U:** in `szene.ts`/`dachMesh` bei nicht-rechteckiger Form die Flächen/Kehl-/Gratlinien
   aus `dachVerschneidung`/`dachUForm` bauen (die reinen Module NUR aufrufen, nicht ändern). Ungültige
   Kontur ⇒ überspringen + markieren (bestehendes Kante-Muster), kein Crash.

Abnahme Stufe 2: der Vorlagen-Picker zeigt die Vorlagen mit Vorschau/Label; Auswahl eines L-/T-Dachs
erzeugt im 3D die korrekte Verschneidung (Kehle/Grat); `tsc`/`test`/`build` grün.

## Gate (Generator selbst, vor „umgesetzt")
`npm run tsc:hausplaner` (0) · `npm run schema:hausplaner:check` (0) · `npm run test:hausplaner`
(≥ vorher; Tests für: additive Enum, l-shape validiert, Vorlagen-Auswahl schreibt Modell, L/T-Render
liefert Kehl-/Gratlinien) · `npm run build:hausplaner`.

## Kantenliste
Schema-Desync (Zod → regen → JSON einchecken); B1-Dublette (eine RoofShape-Quelle, kein gespiegelter Typ);
`pruefeRechteckigeKontur` nur kontrolliert öffnen (rechteckig unverändert); 187 Vorlagen nicht flach
auflisten (gruppieren); `dachVorlage.ts`-Doppelung auflösen.

## Bauordnung / Guardrails
Additiv, Bestands-Dächer bleiben gültig; eine Modellwahrheit; die portierten `geometry/`-Module werden
NUR aufgerufen, nicht verändert (Byte-Treue). Nur `auto/`-Branch, **KEIN main-Merge/Push/Deploy ohne
Yamas Wort**. Meldung „umgesetzt" mit den vier Exit-Codes an Yama/Evaluator.

## Empfehlung Aufteilung
Stufe 1 zuerst als eigener Zyklus (schema-sensibel, klein, Evaluator-Abnahme), Stufe 2 danach — so ist die
Live-Daten-berührende Änderung isoliert prüfbar, bevor die UI-Arbeit draufsetzt.
