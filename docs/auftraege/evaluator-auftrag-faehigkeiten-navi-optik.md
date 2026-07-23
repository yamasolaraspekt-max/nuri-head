# Evaluator-Auftrag — Fähigkeiten-Navi: Optik/Styleguide/UX (2. Abnahme-Dimension)

**Rolle:** Evaluator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Gegenstand:** der neue sichtbare UI-Slice **FaehigkeitenNavi** (ersetzt die HausplanerApp-Attrappe Z.775).
**Warum getrennt:** Die Logik/Struktur/Existenz ist read-only bereits gemessen (eine Registry, kein
geometry/schema-Eingriff, Engines existieren inkl. `pvBelegung`, +7 Tests, offene Auflage = Registry-Guard-Test).
**Optik ist laut CLAUDE.md eine eigene Pflicht-Dimension** — die wird hier vollständig gefahren. Bindet als
**neue sichtbare Änderung** (CLAUDE.md Z.19/21, nicht rückwirkend auf alte Slices).

## Geltende Bauordnung (Quellen, vor der Prüfung lesen)
- `docs/agents/05-fachagenten-produkt-architektur-frontend.md` — die vier Pflicht-Fachagenten.
- `docs/architektur/ui-bauordnung.md` — Styleguide-Pflicht, Token-Disziplin, visuelle Regression.
- Referenz-Optik (Planner): Artefakt `hausplaner-navi-v9` (Zielbild in echten v9-Tokens).
- Token-Wahrheit der React-App: `resources/planner/hausplaner/app/studioDaten.ts` (`T`).

## Token-Grenze (Architektur-Klärung, verbindlich benennen)
Es gibt **zwei** Token-Welten: (a) Blade-Admin `--sa-*` mit `/admin/styleguide` (ui-bauordnung Z.91),
(b) React-Hausplaner `T` in `studioDaten.ts` (v9). Der FaehigkeitenNavi ist **React im Hausplaner** → seine
Token-Wahrheit ist **`T`**. `studioDaten.ts` ist damit die **eine erlaubte Stelle** für Hex (Analogon zu den
Blade-Token-Dateien); **jede** andere Datei — inkl. `FaehigkeitenNavi.tsx` — referenziert nur `T.*`,
**kein** hartkodierter Hex, **kein** loses `--sa-*`. Der Architektur-Agent bestätigt diese Grenze; der
Frontend-Agent misst die Einhaltung (grep).

## Die vier Pflicht-Fachagenten (jede Perspektive mit Beweis, nicht Behauptung)
1. **Konzeption-Agent:** Bildet die Navi das Konzept ab (`docs/konzept/hausplaner-navigation.md`)? — jede
   Landkarten-Fähigkeit sichtbar, Zustand angezeigt, **eine** Registry; Sortierung nach Phase/Gewerk erkennbar.
   Beweis: Registry-Inhalt gegen die Landkarte abgleichen (keine fehlt, keine erfunden).
2. **Workflow-Agent:** Nächster-Schritt-Führung — nächster sinnvoller Klick in **< 2 s** auffindbar? Kein
   Dead-End; aktive Werkzeuge setzen `activeToolId` unverändert; Tastatur/Tab-Reihenfolge sinnvoll, Enter
   aktiviert (wie in der Studio-Shell). Beweis: Klickpfad Engine→Panel real durchspielen, Fokus sichtbar.
3. **Architektur-Agent:** **Eine Wahrheit** — kein Zweit-Register; Studio-Shell + `T` **wiederverwendet**,
   nicht dupliziert; UI-2-SSOT `usePlannerUiStore` (activeToolId/setActiveTool/reset) unangetastet; kein
   geometry/schema/validation im Diff; String-Referenz-Regel (Guard-Test jetzt, getippter Import je Panel
   später). Beweis: git-Diff + grep.
4. **Frontend-Design-Agent (misst gegen die UX-Rubrik am echten Rendern):**
   - **Token-Disziplin:** nur `T`; `grep -nE "#[0-9a-fA-F]{3,6}" FaehigkeitenNavi.tsx` → **0 Treffer**
     (Hex nur in `studioDaten.ts`).
   - **CI-Rollen:** Teal-Akzent an Navigation, **Marken-Grün NUR** an der Primäraktion, Status semantisch
     `T.ok/T.warn/T.err` (nicht Marken-Grün als Status).
   - **Barrierefreiheit:** Kontrast **AA (≥ 4,5:1)** gemessen (Zahl, nicht Augenmaß); Zustand **Farbe UND
     Text** (🟢🟡🔴 nie nur Farbe); `:focus-visible` Teal sichtbar.
   - **Zustände:** Lade-/Leer-/Fehler-Fall der Navi bedacht (leere Gruppe sagt, was zu tun ist).

## Styleguide-Pflicht (ui-bauordnung)
- **Erst prüfen, dann bauen:** Existiert ein Baustein (Karte/Pille/Icon `Ikon`, Panel) schon im Studio/
  `studioUi.tsx`? Dann **wiederverwenden**, nicht neu erfinden. Neu Angelegtes muss ein wiederverwendbarer
  Baustein sein (kein Einweg-Markup).
- **Visuelle Regression:** Screenshot je Viewport als Referenzfläche; künftige Wellen diffen dagegen.

## 3-Viewport-Sichtprüfung mit Echtdaten-Extremfällen (PFLICHT)
Browser (Chrome), **Echtdaten-Extremfall**: viele Fähigkeiten, **lange Labels**, alle Hubs offen, ein
Panel geöffnet. Je Viewport **Screenshot + Urteil**:
- **1440** (Büro-Desktop): volle Navi + Panel, nichts abgeschnitten.
- **1024** (Laptop): Navi + Inhalt tragfähig, keine Überlappung.
- **375** (Handy/Baustelle): Navi klappt korrekt ein (< 900px Auto-Einklappen der Studio-Shell greift),
  Inhalt nicht in einen Reststreifen gedrängt, Panel bedienbar.
Kein Dialog/Alert auslösen (blockiert die Automatisierung).

## Urteil (grün nur mit Beleg)
- Grün je Fachagent = mit Screenshot/Messwert/Diff belegt. **Rot blockiert** — kein Weiterrücken auf Zuruf.
- Offene Logik-Auflage (Registry-Guard-Test) läuft parallel; **beide** Dimensionen müssen grün sein, bevor
  der Slice als abgenommen gilt.
- Kein main-Merge/Push/Deploy ohne Yamas Wort; nur `auto/`-Branch.

## Ballbesitz danach
Rot in einer Dimension → zurück an den **Generator** (Fix), dann Re-Abnahme genau der roten Punkte.
Beide grün → Meldung an **Planner** (Batch 0 fertig → Freigabe für Batch 1: Haustechnik-Panels).

---

## ABLAUF SICHT-RUNDE — App am Tip lauffähig machen (Vorbedingung der Browser-Prüfung)
Es gibt **kein** `dev:hausplaner`; die App wird gebaut (`build:hausplaner`) und über ihre Laravel-
Einbettungsseite geöffnet. Der Build läuft auf der **ARM-Geräte-VM nicht** (rollup-Bug) → **nativ auf dem
Mac**. Die Rollen bleiben getrennt: **eine bauende Instanz (Generator) ODER Yama** stellt den Baum auf den
Tip und baut; der **Evaluator fährt dann den Browser** (kein checkout durch den Evaluator).

**Zwei sichtbare Dimensionen, zwei Tips:**
1. **Batch-0-Navi-Optik** @ `c553fbc` (`auto/hausplaner-navi-batch0`): die FaehigkeitenNavi — gegen diesen
   Auftrag (vier Fachagenten, Token-Disziplin, 3 Viewports).
2. **U-Dach-Optik** @ `4b8eb04` (`auto/hausplaner-w3b-2`): die **einzige** offene Auflage des L/T/U-Slices —
   ist die U-Form im 3D **richtig platziert/orientiert** (Generator-Flag: Schwerpunkt-Näherung → evtl.
   versetzt)? 3 Viewports, Screenshot. Geometrie (nicht-leere Flächen, Firsthöhe, Kanten) ist bereits
   test-belegt — hier zählt nur die visuelle Lage.

**Sequenz je Tip:** `git checkout <tip>` → `npm run build:hausplaner` (nativ, Exit 0) → Einbettungsseite
im Chrome öffnen → 3-Viewport-Pass (1440/1024/375) mit Screenshot je Viewport → Votum je Slice.
**Live-638-Beleg** (offene L/T/U-Auflage 1) fällt bei diesem Schritt mit ab: die bauende Instanz fährt am
`4b8eb04`-Tip `npm run test:hausplaner` und legt die 638/638-Ausgabe als Beleg bei.
