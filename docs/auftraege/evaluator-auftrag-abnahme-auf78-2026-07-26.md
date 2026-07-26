# ⇒ EVALUATOR-AUFTRAG — Abnahme AUF-78, und die vertagte Messung wird hier eingelöst

**Vom:** Planner · **26.07.2026, 16:50** · **Abnahme.** Code `841865b` · Bundle `dcbe6ec`.
Ballbesitz: du. Zwei Dinge in einem Gang — der zweite ist der, den du selbst vertagt hast.

## 1. Was der Generator behauptet — und wo ich es nicht glaube

Sein Bericht ist ungewöhnlich sauber; **genau deshalb prüfst du ihn.** Die Stellen, an denen ich
eine unabhängige Messung will:

1. **Das Rechtegatter.** Er misst die Middleware am Router — `auth` ja,
   `permission:Hausplaner,read` **nein** — statt „unverändert" zu behaupten. **Miss es selbst am
   Router**, nicht an der Routendatei. Und führe **K11 als Mutation**: hänge die Liste an die
   Studio-Fläche, es müssen **2 Tests rot** werden (Verhalten **und** Quelltext). Wird nur einer
   rot, ist die Verriegelung halb.
2. **„Keine Kundendaten" durch Bauart.** Er sagt, die `lead`-Beziehung werde **gar nicht** geladen.
   Das ist stärker als „wir zeigen sie nicht" — **also belege es am Markup und an der Abfrage**:
   kein Kundenname im gerenderten HTML, und kein zweiter Query-Aufruf je Zeile.
3. **Die harte Grenze.** `limit 6`, keine Paginierung, „bei 3 000 Objekten gemessen". **Zahl gegen
   Zahl** — wie viele Zeilen kommen, wie viele Abfragen laufen.
4. **`public/*` im Code-Commit = 0** und der Bundle als **eigener zweiter** Commit (§8 2b).
   Gemessen habe ich schon: `841865b` trägt 0 Zeilen in `public/`, `dcbe6ec` trägt **nur**
   `public/hausplaner/hausplaner.js` (32/32). **Bestätige oder widerlege das.**
5. **Die Rückgabe.** Er hat den **Klick bewusst nicht verdrahtet** und begründet es damit, dass
   „Weiterarbeiten" sonst den geführten Ablauf startet statt das Projekt zu öffnen — dieselbe
   Unwahrheit, die AUF-40 Teil A entfernt hat. **Prüfe, ob die Kachel wirklich nichts verspricht:**
   keine Rolle, kein Fokus, kein Zeiger. Verspricht sie doch etwas, ist die Rückgabe keine.

## 2. Und jetzt die vertagte Messung — sie gehört hierher

Du hast heute den **Worst-Case-Überstand** vertagt, weil `serviert != gemessen` war. **Der Grund
ist weg:** `841865b` + `dcbe6ec` sind committet, der Arbeitsbaum ist leer, das Bundle gehört wieder
zu einem benannten Commit. **Miss jetzt gegen `dcbe6ec`.**

- Bauteil auswählen → Werkzeug mit **gefüllter** Optionen-Zeile (welches die vollste hat, ist selbst
  eine Messung — nenne die Zahl).
- **1440×900 · 1440×813 · 1024×768**, tatsächlicher Viewport gelesen, **Oberkante zuerst**.
- **Gefragt ist der Überstand**, nicht ob etwas umbricht.
- Bezug: deine Grundlinie **369 / 405** und 0 Überstand. **AUF-78 fasst `StartView` und `uiState`
  an — wenn sich hier etwas verschoben hat, ist das ein Befund zu diesem Posten und keine
  allgemeine Beobachtung.**

**Vor der Messung: `git status` auf `public/*`** (§13.6). Ist dort etwas offen, wird gemeldet statt
gemessen.

## 3. Zwei Dinge, die nicht deine Aufgabe sind

- **Den Klick nachfordern.** Er ist zurückgegeben; ob er gebaut wird, entscheidet Yama.
- **Den Bericht loben oder rügen.** Dein Votum gilt dem Gebauten, nicht der Darstellung.

## 4. Umfang

**Abnahme zuerst, Messung danach.** Reicht die Zeit nicht für beides: **Votum abgeben, Messung
melden als nicht gemessen.** Ein Votum, das auf eine Zusatzmessung wartet, blockiert die Kette —
und der Generator steht schon auf AUF-82.
