# ⇒ GENERATOR — AUFTRAG: Dashboard v2 Nacharbeit (N1 · N2 · N3)

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-15a · AUF-16 · AUF-19
**Grundlage:** Evaluator-Votum Dashboard v2 Batch 1 (Befunde B1, B3, B4, K6) im Ledger.
**Heimat-App:** ticket. **Spur:** **A** — N1 und N3 wären für sich Spur B, N2 ändert Komponenten-
struktur. Nach der Regel „Zweifel heißt Spur A, Spurwechsel nur nach oben" läuft der ganze Auftrag
als Spur A mit unabhängiger Abnahme.

**Warum dieser Auftrag existiert:** Der Evaluator hat vier Befunde geliefert, von denen ich drei
fälschlich als Willensfragen geparkt hatte. Sie brauchen keine Entscheidung von Yama — sie brauchen
eine Umsetzung. Der Willensteil ist abgetrennt und bleibt bei ihm (AUF-15b, AUF-17).

---

## Ziel & Entscheidung

Drei Nacharbeiten, unabhängig voneinander, in dieser Reihenfolge.

### N1 — Die 30 rohen Farbwerte ablösen (AUF-15a)

**Entscheidung:** wertgleiche Ablösung auf die vorhandenen Tokens. **Kein Farbwert ändert sich** —
nur seine Herkunft. Betroffen, vom Evaluator gezählt:
`ConfigWizard.tsx` (2) · `StartView.tsx` (3) · `DreiDBereich.tsx` (4) · `GuidedView.tsx` (15) ·
`HausplanerStudio.tsx` (6).

**Nahtstelle:** die Tokens liegen in `app/studioDaten.ts` — dieselbe Quelle, die T1 für
`HausplanerApp.tsx` benutzt hat. `git show 9ec3b25` zeigt das Muster; folge ihm, erfinde kein zweites.

**Operanden-Gate:** Findest du einen rohen Wert, für den **kein wertgleiches Token existiert**, dann
**erfinde keines und ändere den Wert nicht**. Solche Fälle listest du im Bericht auf und gibst sie
zurück. Lieber 26 von 30 abgelöst und vier sauber benannt als 30 mit vier stillen Farbänderungen.

### N2 — B1 beheben: die Options-Leiste hört auf, sich neu zu mounten (AUF-16)

**Entscheidung — die treffe ich, es ist keine Geschmacksfrage:** `KontextOptionenLeiste` wird aus dem
Rumpf von `HausplanerApp` **herausgezogen** und auf Modulebene definiert; die benötigten Werte kommen
als **explizite Props**, nicht über Closure. Begründung: ein `<select>` ist fokussierbar, und
`:873 onMouseMove` rendert `HausplanerApp` in Mausbewegungs-Frequenz — die Komponente bekommt bei
jedem Render eine neue Typ-Identität und ihr Teilbaum wird abgerissen. Bei `OpBtn` ist das folgenlos,
hier nicht. Für Batch 2 hast du dieses Muster bereits bewusst vermieden; das ist derselbe Handgriff,
nachgezogen.

**Ausdrücklich nicht:** `OpBtn` und andere zustandslose Rumpf-Komponenten anfassen. Nur die, die ein
fokussierbares Steuerelement enthält.

### N3 — B3 + B4: das Reiter-Muster vervollständigen (AUF-19)

- **B3:** `role="tabpanel"`, `aria-controls` und die `id`-Verknüpfung zwischen Reiter und Inhalts-
  bereich fehlen; der Inhaltsbereich (`HausplanerApp.tsx:1176` ff.) trägt keine Rolle.
- **B4:** `tabIndex={aktivT ? 0 : -1}` ist korrekt gesetzt, aber die Pfeiltasten-Behandlung
  (`:1160`–`:1164`) zieht den DOM-Fokus nicht mit. Nach ArrowRight liegen sichtbarer Fokus und
  ausgewählter Reiter auseinander.

**Entscheidung:** beides schließen, per `ref` + `focus()` auf dem neu aktiven Reiter.

---

## Was ausdrücklich NICHT zu diesem Auftrag gehört

- `app/tools/toolPresentation.ts` — AUF-1-Sperrbereich, unverändert gesperrt.
- `store/*`, `domain/*`, `geometry/*`, `renderers/*`, Zod, Schema, Migrationen, PHP.
- `public/hausplaner/hausplaner.js` — **nicht anfassen**, der Build läuft hier nicht (aarch64).
- Die Willensfragen AUF-13, AUF-14, AUF-15b, AUF-17. Nicht beantworten, nicht vorwegnehmen.
- Die Inhalte der Reiter `beziehungen`/`historie`. Sie bleiben Fläche.

---

## Kantenliste

1. **N1:** ein roher Wert steht in einer `style`-Eigenschaft, die zur Laufzeit berechnet wird →
   Ablösung darf die Berechnung nicht in eine Konstante verwandeln.
2. **N1:** derselbe Hex kommt in zwei Dateien mit **unterschiedlicher Bedeutung** vor (einmal Rahmen,
   einmal Text) → beide auf das je **semantisch** richtige Token, nicht auf dasselbe.
3. **N2:** Props vollständig? Ein vergessener Wert wird sonst zur stillen `undefined`-Anzeige.
   Der `<select>` muss nach dem Umbau denselben Wert zeigen und denselben `onChange` auslösen.
4. **N2:** die Komponente darf nach dem Herausziehen **nicht** bei jedem Render neue Props-Objekte
   bekommen, die den Effekt wiederherstellen (Objekt-/Array-Literale in den Props).
5. **N3:** `id`-Kollision, wenn das Panel mehrfach im Baum stünde → Präfix verwenden.
6. **N3:** `focus()` darf nicht laufen, wenn der Reiterwechsel per Maus kam — sonst springt der
   Fokusring bei jedem Klick auf.

---

## Rückweg & Entdeckung

**Rückweg:** drei getrennte Commits, jeder ohne Datenmigration zurückdrehbar. Keine DB, kein Schema,
kein Bestandsdatum berührt.
**Entdeckung:** N1 wird sichtbar, wenn eine Fläche ihre Farbe ändert — deshalb ist Wertgleichheit
Kriterium, nicht Absicht. N2 wird sichtbar, wenn der `<select>` seinen Wert oder Fokus verliert.
N3 wird sichtbar, wenn ArrowRight den Fokusring stehen lässt.

---

## Abnahmekriterien (prüfbare Aussagen)

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — **Exit 0**.
   `build:hausplaner` als **„nicht ausführbar, Grund: …"** berichten, nie als grün.
2. Testzahl vorher/nachher genannt, nachher ≥ vorher, **kein** Test von grün nach rot, und die
   **Namen-Mengen** verglichen (eine steigende Summe verdeckt sonst einen verschwundenen Test).
3. **N1:** Grep über die fünf Dateien nach `#[0-9a-fA-F]{3,6}` und `rgba?\(` — Trefferzahl vorher und
   nachher als Rohausgabe. Jeder verbliebene Treffer ist im Bericht **einzeln** begründet
   (Operanden-Gate) und wird zurückgegeben.
4. **N1, Wertgleichheit:** für jeden abgelösten Wert die Gegenüberstellung `alter Hex → Token → Wert
   des Tokens`. Weichen sie ab, ist das ein Rot, kein Detail.
5. **N2:** `KontextOptionenLeiste` steht nachweislich **außerhalb** der `HausplanerApp`-Funktion
   (`git diff` zeigt die verschobene Definition). Ein Test belegt, dass die Komponenten-Identität
   über zwei Renders **gleich** bleibt — genau die Messung, mit der der Evaluator B1 belegt hat
   (`false` vorher, `true` erwartet).
6. **N2:** Optionswerte, `value`, `onChange` und die Quelllisten byte-identisch zu vorher
   (`git diff` beider Stellen).
7. **N3:** ein Test belegt, dass jeder Reiter ein `aria-controls` trägt, das auf eine existierende
   `id` zeigt, und dass der Inhaltsbereich `role="tabpanel"` hat.
8. **N3:** die Fokusnachführung ist per Test oder — falls ohne DOM nicht messbar — per `git diff`
   und ausdrücklicher Nennung als **„nicht testbar ohne DOM"** belegt. Nicht behaupten.
9. **Kein Beifang:** `git diff` zeigt null Zeilen in `app/tools/*`, `store/*`, `domain/*`,
   `geometry/*`, `renderers/*` und `public/*`.
10. **0 rohe Farbwerte in den von dir geänderten Zeilen** (Schnitt aus §11 c des v2-Auftrags).

---

## Guardrails

- **Drei Commits**, N1 · N2 · N3 getrennt, jeder mit Pfadangabe: `git commit -m "…" -- <pfade>`.
  **Nie `-A`, nie `.`**, `-m` **vor** dem `--`.
- `.git/*.lock` niemals mit `rm` — nur `mv` nach `.git/_locks_beiseite/<datum>/`.
- **Kein Push, kein `main`-Merge, kein Deploy.** Tor 2 gehört Yama.
- **Du meldest „umgesetzt", nie „abgenommen".**
- Taucht Nötiges außerhalb des Umfangs auf: **zurückgeben**, nicht mitbauen.
- **Hinweis zur Parallelität:** `5092b10` (Batch 2) wartet noch auf seine Abnahme. Der Evaluator prüft
  einen **festen Hash**, deine Commits stören ihn nicht — aber halte den Arbeitsbaum sauber und
  committe zügig, damit kein wandernder Baum entsteht.

## Bericht

Ein Block in `docs/handoff-status.md`:
`## ⇒ GENERATOR-BERICHT — Dashboard v2 Nacharbeit N1/N2/N3 UMGESETZT`, mit den Exit-Codes, den
Testzahlen, den drei Commit-Hashes, der Wertgleichheits-Tabelle aus Kriterium 4, den zurückgegebenen
Fällen aus Kriterium 3 und allem, was außerhalb des Umfangs auftauchte.
