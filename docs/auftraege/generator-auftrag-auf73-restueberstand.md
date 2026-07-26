# ⇒ GENERATOR-AUFTRAG AUF-73 — Die letzten 18 px, und zwar im ungünstigsten Zustand

**Vom:** Planner · **26.07.2026, 08:15** · **Spur A** · **Heimat-App:** `ticket`
**Anlass:** **Auflage aus dem AUF-72-Votum** (`fe2eb6b`, FREIGABE MIT AUFLAGE). Der Evaluator hat
„Überstand 0" widerlegt — nicht im Kern, aber in der Aussage.

**Vorher gelesen:** HEAD `fe2eb6b` · `git log -6` · Votum AUF-72 · Bericht `c23ec6a` ·
`app/dashboard/buehnenHoehe.ts` · `app/HausplanerApp.tsx` (Container-Kette) ·
**eigene Browser-Messung 26.07., 1440 × 813: Canvas-Oberkante y = 369.**

---

## 1. Der Befund — und warum er nicht „Kleinkram" ist

**Der Generator meldete Überstand 0 bei 1440×900, 1440×813 und 1024×768. Der Evaluator misst
konstant 18 px Überstand bei 900 **und** 813.**

**Der Unterschied ist nicht das Fenster, sondern der Zustand:**

| | Canvas-Oberkante |
|---|---|
| Generator | ~**323** px |
| Evaluator | **369** px |
| Planner (unabhängig, 1440 × 813) | **369** px |

**Zwei von drei Messungen sagen 369.** Der Unterschied sind ~46 px: die
**Werkzeug-Optionen-Zeile** („Markieren — braucht keine Optionen"). Sie erscheint, sobald ein
Werkzeug gewählt ist — also im **gewöhnlichen** Arbeitszustand, nicht in einem Sonderfall.

**Dass der Überstand über alle Fensterhöhen konstant 18 px beträgt, ist der Beweis:** ein festes,
nicht eingerechnetes Element, kein Zeitproblem und kein Rundungsfehler.

**Die Einordnung des Evaluators übernehme ich unverändert: kein Blocker.** Der Kernfehler ist von
227 px auf 18 px zurück — **92 %** —, und der Rest ist über „Ansicht einpassen" und den Verschub
**erreichbar**. Der Grundriss ist nicht verloren. **Es geht um die Aussage, nicht um die Substanz.**

## 2. Die Entscheidung — Weg A, nicht Weg B

Der Evaluator bot zwei: **(A)** auf das echte Eltern-Element des Canvas messen statt auf einen
Träger darüber, oder **(B)** belegen, dass „Ansicht einpassen" den Rest immer abfängt und die
Aussage „Überstand 0" entsprechend einschränken.

**Es wird A.** Begründung: **B macht die Richtigkeit davon abhängig, dass der Nutzer einen Knopf
drückt.** Eine Zeichenfläche, die erst nach einem Klick vollständig ist, ist nicht vollständig —
das ist derselbe Gedanke wie „kein Scrollen als Lösung" in AUF-72 §5.

**Und A ist die Fortsetzung derselben Entscheidung, nicht eine neue:** AUF-72 hat beschlossen, die
Höhe **zu messen statt zu schätzen**. Auf einen Träger *über* dem Canvas zu messen, ist noch immer
eine halbe Schätzung — es unterstellt, dazwischen liege nichts. **Genau das lag dazwischen.**

## 3. Was gebaut wird

**Gemessen wird an dem Element, das den Canvas wirklich enthält** — nicht an einem Vorfahren, der
zusätzlich andere Zeilen trägt. Alles andere aus AUF-72 bleibt unverändert: derselbe Beobachter,
dieselbe Ersatzhöhe **700**, dieselbe Mindesthöhe **200**, dieselbe reine Funktion.

**Nichts wird ersetzt, es wird nur richtig angehängt.**

## 4. Was **nicht** gebaut wird

- **Keine zweite Messstelle.** Es bleibt bei **einer** Wahrheit über die Bühnenhöhe.
- **Kein Ausgleich per fester Zahl.** Wer 18 abzieht, hat die 96 aus AUF-72 durch eine kleinere
  ersetzt — **und in vier Wochen steht dieselbe Sitzung wieder an.**
- **Kein Umbau der Optionen-Zeile.** Sie bleibt, wo sie ist. Ob sie Platz kostet, den sie nicht
  verdient, ist eine eigene Frage und Yamas.
- **Kein Anfassen von `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types`** — K4.

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt** — null Zeilen.
3. **Überstand 0 im ungünstigsten Zustand** — und der ist ausdrücklich definiert:
   **Architektur-Bereich · ein Werkzeug gewählt, so dass die Optionen-Zeile steht · Expertenmodus.**
   Gemessen bei **1440 × 900** und **1440 × 813**, je mit Zahl. *(Heute: konstant 18 px.)*
4. **Auch im günstigsten Zustand 0** — ohne Optionen-Zeile darf nichts zurückfallen.
5. **Die Canvas-Oberkante wird mitgenannt**, nicht nur der Überstand. **Das ist die Zahl, an der die
   beiden Messungen auseinandergingen** (323 gegen 369); sie gehört von jetzt an in jeden Bericht zu
   dieser Fläche.
6. **Keine feste Zahl:** `grep` belegt, dass keine Pixelkonstante zur Höhenkorrektur eingeführt wurde.
7. **Die Zusagen aus AUF-72 bleiben grün** — namentlich: **der Verschub des Nutzers überlebt jede
   Höhenänderung**, Ersatzhöhe 700, Mindesthöhe 200. Testzahlen vorher/nachher, **keine Zusage
   verschwunden**.
8. **Mutations-Gegenbeweis:** wieder auf den Vorfahren messen ⇒ mindestens ein Test rot. Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
10. **Klassifikation: `sichtbar`.**

## 6. Zum Ablauf, ohne Vorwurf

**Der Bericht zu AUF-72 war gründlich und die Zahlen darin sind richtig gemessen** — die
Canvas-*Höhe* stimmt mit der des Evaluators exakt überein. Falsch war nicht die Messung, sondern
**der Zustand, in dem gemessen wurde**: einer mit weniger Leisten als der gewöhnliche.

**Daraus die Regel, die ich mir selbst aufschreibe (§11): Eine Sichtprobe wird im ungünstigsten
Zustand gemessen, nicht im nächstbesten.** Wer im leichteren Zustand misst, bekommt eine Zahl, die
schmeichelt — und niemand merkt es, weil sie stimmt.

*Mich trifft dasselbe: Ich habe AUF-70 zuerst an einer Datei aus dem Browser-Zwischenspeicher
gemessen und hätte beinahe den alten Stand freigegeben.*
