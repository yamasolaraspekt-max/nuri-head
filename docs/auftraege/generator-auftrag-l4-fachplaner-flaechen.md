# ⇒ GENERATOR — AUFTRAG L4: Die Fachplaner-Untermodule bekommen eine Fläche

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-25 · **Spur:** **A**
(neue Flächen mit Zustands-Ableitung — im Zweifel A).
**Grundlage:** `docs/fahrplan-frontend-layout-hausplaner.md` §3, Schritt **L4**.
**Vorbedingung: keine.** L4 hängt an nichts und ist die sichtbarste offene Fläche des Fahrplans.

---

## Ziel & Entscheidung

Heute führen die Fachplaner-Untermodule ins Nichts: `HausplanerStudio.tsx` ~Z. 65 zeigt
`zeigeToast('<name> autark gestartet — kein Gebäude nötig. (Konfigurator folgt.)')`. Vier Namen
(Fenster · Tür · Treppe · Heizkörper) öffnen einen echten Konfigurator, **alle übrigen einen Toast**.
Der Layout-Fahrplan nennt das „20 Klicks ins Nichts" und der UX-Audit „tote Elemente".

**Entscheidung — Yamas offene Frage §4.2 des Fahrplans beantworte ich hiermit: die Fläche wird
tief, nicht flach.** Begründung: eine Fläche mit nur Kopf und Hinweis muss später vollständig
ersetzt werden; eine Fläche mit der Feldstruktur des späteren Panels wird nur noch verdrahtet. Yamas
stehende Regel deckt genau das: *„erst Layout fertig, auch wenn die Funktion nicht programmiert ist"*.

**Jede Fläche trägt vier Teile:**

1. **Kopf** — Name des Moduls, seine Gruppe, Zurück-Weg.
2. **Zweck** — ein Satz, was hier entsteht. Konkret, im Futur, kein Blindtext.
3. **Feldstruktur-Vorschau** — die Eingangsgrößen und die Ausgangsgrößen des späteren Panels als
   beschriftete, **deaktivierte** Felder bzw. Ergebniszeilen. Sie zeigen die Form, nicht Werte.
4. **Leerzustand mit `ZustandBadge`** — Zustand `in_entwicklung`, Text daneben. **Die v1-Regel gilt
   unverändert: eine Fläche ohne Funktion darf dastehen, muss ihren Zustand aber ehrlich sagen.**

**Wiederverwenden statt neu bauen** (Reuse-Gate, `ticket-code-reuse`): `ZustandBadge`,
`StudioZustand`, die Token aus `T`, die Bausteine aus `studioUi.tsx`, das Kopf-/Zurück-Muster aus
`ConfigWizard.tsx`. **Kein zweites Designsystem, keine neue Farbe, kein roher Hex.**

## Umfang — zuerst messen, dann bauen

Die Zahl „20" stammt aus dem Fahrplan. **Zähle selbst**, welche Module heute im Toast landen:
`FACH` in `studioDaten.ts` (5 Hubs mit Untermodulen) und die Gruppen aus `tools/faehigkeiten.ts`.
Nenne die gemessene Zahl im Bericht; weicht sie von 20 ab, ist das ein Befund, kein Fehler.

**Die vier vorhandenen Konfiguratoren (Fenster · Tür · Treppe · Heizkörper) bleiben unangetastet** —
sie haben bereits eine echte Fläche.

## Was ausdrücklich NICHT zu diesem Auftrag gehört

- **`HausplanerApp.tsx` nicht anfassen.** Dort arbeitet gerade eine **zweite Instanz** an Welle A2
  (`a61f10e`). Jede Zeile dort ist eine Kollision.
- Keine Rechenlogik, keine Fach-Engine anschließen. Die 13 Engines sind **L2/L3**, nicht L4.
- `store/*`, `domain/*`, `geometry/*`, `renderers/*`, Zod, Schema, PHP, Migrationen: unberührt.
- `public/hausplaner/hausplaner.js` nicht anfassen (Build läuft in der Umgebung nicht).
- Die Demo-Daten `ZULETZT` sind **L6**, nicht dein Posten.

## Kantenliste

1. Ein Modulname enthält Sonderzeichen oder ist doppelt → Schlüssel muss eindeutig sein.
2. Zurück aus einer Fläche muss dorthin führen, wo der Nutzer herkam (Hub **oder** Navi), nicht
   pauschal auf die Startseite.
3. Ein Modul, das *doch* schon einen Konfigurator hat, darf **keine** L4-Fläche bekommen —
   sonst zwei Wahrheiten für dieselbe Aktion.
4. Die Fläche darf **nicht** so aussehen, als könnte sie rechnen: keine Schaltfläche „Berechnen",
   die nichts tut. Deaktiviert **und** mit Grund.
5. Schmale Fenster (375 px): die Feldstruktur muss umbrechen, nicht abgeschnitten werden.
   **Im Expertenmodus ist genau das gerade ein offener Befund** — das rechte Panel wird bei 1375 px
   horizontal gekappt (der vierte Reiter „Historie" ist unsichtbar). **Mach denselben Fehler nicht.**

## Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — **Exit 0**.
   `build:hausplaner` als „nicht ausführbar, Grund: …" berichten, nie als grün.
2. Testzahl vorher/nachher, **Namen-Mengen verglichen**, kein verschwundener Test.
3. `zeigeToast('… Konfigurator folgt …')` kommt im Produktivcode **null** Mal mehr vor
   (Grep als Rohausgabe).
4. Ein reines Datenmodul (z. B. `app/dashboard/fachFlaechen.ts`) trägt je Modul
   `id · label · gruppe · zweck · eingaenge[] · ausgaenge[] · zustand`. Ein Test belegt: jede id
   eindeutig, jeder `zustand` aus `StudioZustand`, **jeder** `zweck` länger als 10 Zeichen und
   nirgends „keine Daten" oder „folgt".
5. **Mutations-Gegen-Beweis, selbst geführt:** einen `zweck` auf `''` setzen → mindestens ein Test
   **muss** rot werden. Danach zurückbauen, `git diff` leer. Ergebnis im Bericht.
6. Jede Fläche trägt ein `ZustandBadge`; der Bericht listet sie einzeln auf: Fläche → Zustand → Text.
7. **0 rohe Farbwerte in den geänderten Zeilen** (`git diff` der `+`-Zeilen).
8. `git diff` zeigt **null Zeilen** in `HausplanerApp.tsx`, `store/*`, `domain/*`, `geometry/*`,
   `renderers/*`, `public/*`.

## Guardrails

- **Vor dem ersten Schreibzugriff** `git --no-optional-locks status --porcelain` lesen. Es arbeitet
  eine zweite Instanz an `HausplanerApp.tsx` und `leisteAusZonen.test.ts` — **das ist erwartet und
  kein Abbruchgrund**, solange **deine** Dateien unberührt sind. Abbruch nur, wenn jemand in
  **deinen** Pfaden schreibt.
- **Staging nur nach Pfad**, `-m` **vor** dem `--`. **Nie `-A`, nie `.`** — im Index liegt fremde
  Arbeit, ein pauschales Commit nimmt sie mit.
- `.git/*.lock` nur per `mv` nach `.git/_locks_beiseite/l4-generator-25-07/`.
- **Kein Push, kein Merge, kein Deploy. Du meldest „umgesetzt", nie „abgenommen".**

## Bericht

`## ⇒ GENERATOR-BERICHT — L4 Fachplaner-Flächen UMGESETZT` mit den acht Kriterien als Rohausgabe,
der gemessenen Modulzahl, der Aufzählung aus Kriterium 6, dem Gegen-Beweis aus Kriterium 5 und dem,
was du zurückgibst.
