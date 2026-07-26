# ⇒ GENERATOR-AUFTRAG AUF-71 — Eine Beschreibung für „gesperrt", app-weit

**Vom:** Planner · **26.07.2026, 07:50** · **Spur A** — ein falsch angefasster Zustand lässt einen
freien Knopf gesperrt aussehen; das ist die gefährlichere Richtung und kein Markup-Fehler.
**Heimat-App:** `ticket`. **Grundlage:** **Zustands-Inventur des Evaluators** (`21b016a`), von mir
beauftragt, von ihm gemessen. **Alle Stellen und Zahlen unten stammen aus seiner Messung, nicht aus
meiner Vermutung.**

**Vorher gelesen:** HEAD `21b016a` · `git log -6` · Ledger „EVALUATOR — ZUSTANDS-INVENTUR" ·
Votum AUF-70 (`031b07d`) · `app/dashboard/opKnopfZustand.ts:60`.

---

## 1. Der Befund — vier Beschreibungen, nicht eine

| # | wo | wie „gesperrt" heute aussieht |
|---|---|---|
| 1 | `dashboard/opKnopfZustand.ts:60` (`opKnopfBild`) — Icon-Zeile, `OpBtn`, `knopf()` | Deckkraft **0,6** · Grund `hair2` · Icon `faint` · `not-allowed` |
| 2 | `HausplanerApp.tsx:1339` (Werkzeug-Navi) · `GeschossFlaeche.tsx:169` (Geschoss löschen) | Deckkraft **0,4** · `not-allowed` — **kein** Grund-, **kein** Icon-Token |
| 3 | `EngineFlaeche.tsx:101-102` („Berechnen") | **keine** Deckkraft — Grund `hair2` · Text `muted` · `not-allowed` |
| 4 | `FachFlaeche.tsx:71` (Felder) · `HausplanerApp.tsx:2175` (Listen-Eintrag) | Farbe `faint`/`muted` · `not-allowed` — **keine** Deckkraft |

**Die „eine Wahrheit" aus AUF-59/70 deckt nur die Icon-Zeile.** Der auffälligste Bruch ist der
leiseste: **Deckkraft 0,6 gegen 0,4** — zwei Flächen derselben Anwendung sagen dasselbe mit
verschiedener Lautstärke, und niemand hat das je entschieden.

**Was der Evaluator ausdrücklich festhält, und was diesen Posten klein hält:** **Kein offener
Defekt.** Auf **allen** geprüften Flächen unterscheidet sich gesperrt messbar von frei — mindestens
`not-allowed` plus ein sichtbares Merkmal. **Kein einziger pixelgleicher Fall ist übrig.** Es geht um
Einheitlichkeit und um eine WCAG-Härtung, nicht um eine Reparatur. **Deshalb steht dieser Posten
nicht vorn.**

## 2. Wo ich mich geirrt habe — gehört hierher, damit es nicht als Generator-Fehler gelesen wird

Ich hatte dem Evaluator geschrieben: *„Nach AUF-70 soll es eine Beschreibung geben. Findest du zwei,
ist AUF-70 unvollständig."* **Das war falsch, und zwar meinerseits.** Mein AUF-70-Auftrag hat den
Umfang selbst auf **eine Zeile** begrenzt („Kein Anfassen von … der Themenzeile darunter"). Der
Generator hat genau das gebaut, und er hat es richtig gebaut: **`knopf()` liest heute `opKnopfBild`,
statt eine zweite Beschreibung danebenzustellen** — vom Evaluator testverriegelt bestätigt.

**Die Unvollständigkeit liegt im Zuschnitt meines Auftrags, nicht in seiner Ausführung.**
AUF-70 ist zu Recht freigegeben.

## 3. Was gebaut wird

**Eine Beschreibung, aus der alle vier Stellen lesen.** Nicht vier Stellen, die zufällig dieselben
Zahlen tragen — **eine Quelle, vier Leser.** `opKnopfBild` ist die vorhandene und bleibt es; sie
wird so weit verallgemeinert, dass die anderen drei sie benutzen können, **ohne dass die Icon-Zeile
ihr Aussehen ändert.**

**Reihenfolge, weil sie den Aufwand bestimmt:**

1. **Die 0,4-gegen-0,6-Spaltung auflösen** (#2). Reine Zahl, zwei Stellen, kein neues Muster —
   und der Bruch, der am ehesten auffällt, weil beide Flächen nebeneinander sichtbar sind.
2. **Die farb-lastigen Flächen an dieselbe Quelle hängen** (#3, #4).
3. **Erst wenn 1 und 2 stehen:** prüfen, ob eine Variante für **Eingabefelder** nötig ist. Ein Feld
   ist kein Knopf; eine Deckkraft von 0,6 auf einem Feld kann Text unleserlich machen. **Ist eine
   Variante nötig, wird sie aus derselben Quelle abgeleitet, nicht daneben erfunden** — und sie
   trägt einen Namen, der sagt, wofür sie gilt.

## 4. Die WCAG-Härtung — der einzige Punkt, an dem sich etwas *ändert*

Der Evaluator nennt einen Wachpunkt: **#3 und #4 kodieren den Zustand vor allem über Farbe.**
`not-allowed` kommt dazu — **aber ein Mauszeiger existiert für Tastatur- und Touch-Bedienung nicht.**

**Deshalb verbindlich:** Jede gesperrte Fläche trägt **mindestens ein nicht-farbliches, nicht vom
Zeiger abhängiges Merkmal** — Deckkraft, Rahmen oder ein Zustandsattribut, das Vorleseprogramme
lesen (`disabled` bzw. `aria-disabled`). **Welches, entscheidet die Fläche; dass eines da ist, ist
nicht verhandelbar.**

## 5. Was **nicht** gebaut wird

- **Keine Änderung am Aussehen der Icon-Zeile.** Sie ist gerade zweimal abgenommen worden
  (AUF-59, AUF-70). Ändert sich dort **ein** gemessener Wert, ist der Posten rot.
- **Keine neue Sperre, keine gelöste Sperre.** Die Menge der gesperrten Elemente bleibt
  **identisch** — dasselbe Kriterium wie bei AUF-59, AUF-68 und AUF-70.
- **Kein Design-System-Umbau.** Es geht um **einen** Zustand, nicht um alle. Wer bei der Gelegenheit
  `aktiv` und `frei` mitvereinheitlicht, hat drei Posten in einem gebaut.
- **Kein Anfassen von `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types`** — K4.

## 6. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt** — null Zeilen.
3. **Eine Quelle:** `grep` belegt, dass **keine** Datei außer der einen Beschreibung eine
   Deckkraft, Grund- oder Textfarbe für den gesperrten Zustand selbst festlegt. **Erwartete Zahl der
   Fundstellen: 1.**
4. **Die Icon-Zeile ist unverändert:** Test vergleicht die vier gemessenen Werte für
   `Rückgängig` (gesperrt) — **0,6 · `not-allowed` · rgb(167,174,183) · rgb(242,244,246)** — vorher
   und nachher. **Identisch.**
5. **Die Spaltung ist weg:** `grep` auf `0.4` als Sperr-Deckkraft = **0**.
6. **Nicht allein über Farbe:** je Fläche ein Test, der belegt, dass ein nicht-farbliches, nicht
   zeigerabhängiges Merkmal vorhanden ist.
7. **Keine Sperre geändert:** die Menge der gesperrten Elemente vorher/nachher identisch.
8. **Mutations-Gegenbeweis:** die gemeinsame Beschreibung so ändern, dass gesperrt wie frei
   aussieht ⇒ Tests auf **allen vier** Flächen rot, nicht nur auf einer. **Das ist der Beweis, dass
   sie wirklich aus einer Quelle lesen.** Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
10. **Klassifikation: `sichtbar`.** Sichtprobe über die betroffenen Flächen, mit gemessenen Werten —
    nicht mit Eindruck.

## 7. Was zurückgegeben wird

- **Braucht eine Fläche zwingend eine eigene Kodierung** (z. B. weil Deckkraft dort Text unleserlich
  macht): **melden mit der Messung.** Dann ist die Variante begründet und Teil der einen Quelle —
  eine zweite Quelle wird sie nicht.
- **Zeigt sich, dass eine der vier Stellen gar nicht gesperrt werden kann** (toter Zweig): sagen.
  Dann fällt sie aus dem Posten, statt „vereinheitlicht" zu werden, was nie eintritt.
