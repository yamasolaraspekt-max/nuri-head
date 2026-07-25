# ⇒ EVALUATOR — AUFTRAG: Abnahme Dashboard v2 Batch 2 (v2.3 + v2.4 + v2.5)

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Gegenstand:** Commit `5092b10`
**Tafel:** AUF-12 Batch 2 · **Rolle:** Evaluator, **andere Instanz als der Generator**

## 0. Voraussetzung

Es gilt **derselbe Rahmen wie für Batch 1** — lies `evaluator-auftrag-dashboard-v2-batch1.md` §0–§2
und §6–§8 und wende sie sinngemäß an: E1 (**erst messen, dann den Bericht lesen**), E2 (voller
Prüfrahmen), Gates selbst fahren, Testzahl per `git archive 5092b10^` nach `/tmp` selbst erzeugen,
**kein Produktivcode, kein Commit, kein Push**, Mutationen nur in `/tmp`-Kopien, `git status` am
Anfang und am Ende vergleichen.

Zusätzlich zu lesen: `generator-auftrag-dashboard-v2-flaechen.md` §4 (Nahtstellen Batch 2), §6
(Kanten 1–10), §7 Kriterien **7–12** und **§11 (Planner-Nachtrag — er geht dem Haupttext vor)**.

## 1. Was hier anders ist als bei Batch 1

- **Kriterium 7 ist auf drei Gates reduziert.** `build:hausplaner` ist auf aarch64 nicht ausführbar.
  Bestätige die Nicht-Ausführbarkeit selbst; führe sie **nicht** als grün.
- **Kriterium 6 ist neu geschnitten** (§11 c): 0 rohe Farbwerte **in den geänderten Zeilen**, nicht im
  Gesamtbaum. Die 30 Altwerte sind AUF-15 und **kein** Befund gegen diesen Commit.
- **`public/` darf nicht im Commit sein** (§11 b). Prüfe das, und prüfe die Aussage des Generators,
  dass das Bundle ab jetzt hinter dem Quellstand liegt.

## 2. Die Kriterien 8–12 — je Kriterium ein Gegen-Beweis

| Nr | Behauptung | Wie du sie zu widerlegen versuchst |
|---|---|---|
| 8 | je ein Test für `projektBaum`, `befunde`, `palette` mit den in §7 genannten Fällen | Fälle einzeln nachlesen und gegen die Testdatei halten. Fehlt einer, ist 8 rot — auch wenn die Suite grün ist. |
| 9 | Mutation `enabled: true` macht mindestens einen Test rot | **Selbst wiederholen** in `/tmp`. Der Generator meldet fünf rote Tests — bestätige oder widerlege die Zahl. |
| 10 | `PANEL_TABS['pruefungen'].zustand === 'verfuegbar']`, Leerzustand wörtlich *„Keine offenen Befunde."* | Wortlaut byte-genau, nicht sinngemäß. |
| 11 | `Strg/⌘+K` kollisionsfrei, `Strg+S` speichert weiter | **Der Generator hat selbst offengelegt, dass `Strg+K` vorher „Decke" setzte.** Prüfe die Zweig-Reihenfolge in `taste()` und belege, dass `Strg+S` **zuerst** getroffen wird und `K` ohne Modifikator unverändert wirkt. |
| 12 | jede neue leere Fläche trägt ein `ZustandBadge` | Der Generator führt **drei** Zeilen ohne Badge (Filterergebnis, Deaktivierungsgrund, Umfangs-Hinweis) mit der Begründung, ein Badge behaupte den Zustand *einer Fläche*. **Urteile ausdrücklich**, ob dieser Schnitt trägt. |

## 3. Zusätzliche Planner-Auflagen

1. **Der umbenannte Test.** Ein Testname ist verschwunden. Der Generator sagt: umbenannt und
   verschärft, weil v2.4 die Prämisse aufhebt. **Prüfe nach**, dass die Zusicherung nicht schwächer
   geworden ist — das ist der Fall, in dem eine steigende Gesamtzahl eine Lücke verdeckt.
2. **B1-Nachfolge.** Der Generator hat Projektbrowser und Palette bewusst **nicht** als
   Rumpf-Komponenten gebaut. Bestätige am Code, dass kein fokussierbares Steuerelement in einer im
   Rumpf definierten Komponente sitzt.
3. **Kanten 6 und 8.** `GRUPPEN_GRENZE = 200` und der `paletteOffenRef`-Wächter in `taste()` —
   belege beide am Test, nicht am Kommentar.

## 4. Votum

Form wie Batch 1 (§8 dort): `## ⇒ EVALUATOR-VOTUM — Dashboard v2 Batch 2 (5092b10)`, je Kriterium
grün/rot mit Rohausgabe, Freigabe / Freigabe mit Auflage / Rot. **Rot braucht einen reproduzierbaren
Fall** — fehlt der, ist es eine Rückfrage und wird so benannt.
