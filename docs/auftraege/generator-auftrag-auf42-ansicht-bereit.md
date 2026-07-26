# ⇒ GENERATOR-AUFTRAG AUF-42 — `viewport.ready` sagt heute immer ja

**Vom:** Planner · **26.07.2026, 19:20** · **Spur A** · **Heimat-App:** `ticket`
**Anlass:** Rückgabe 3 aus dem Generator-Bericht zu AUF-36. **Kein neuer Posten** — die Zeile stand
seit dem 25.07. ohne Auftragsdatei auf der Tafel (§14: nichts Neues, aber das Offene fertig).

**Vorher gemessen, 26.07.:**

```
app/HausplanerApp.tsx:435   FAEHIGKEIT_ANSICHT_BEREIT,      ← ohne Bedingung in der Liste
Kommentar daneben:          „Die Zeichenfläche ist gemountet, sobald diese Komponente rendert."
app/tools/werkzeugVertrag.ts   'viewport.ready'  →  5 Werkzeuge hängen daran
app/tools/vorbedingungen.ts:132  Grundtext: „Die Zeichenfläche ist noch nicht bereit."
```

**Der Befund in einem Satz: die Fähigkeit ist unbedingt.** Sie steht in der Liste, sobald die
Komponente rendert — also **immer**. Damit ist der Satz *„Die Zeichenfläche ist noch nicht bereit"*
ein Text, den **niemand jemals sieht**, und fünf Werkzeuge tragen eine Vorbedingung, die nichts
prüft.

---

## 1. Der erste Schritt ist eine Messung, nicht ein Bau

**Beantworte zuerst, bevor du irgendetwas änderst:**

> **Gibt es einen Zustand, in dem die Komponente rendert, die Zeichenfläche aber nicht benutzbar
> ist?**

Kandidaten, die ich für messbar halte — **prüfe sie, statt sie zu glauben:** die Bühne hat Breite
oder Höhe **0** (dieselbe Lage, an der `buehnenHoehe.ts` und `einpassen.ts` rechnen, und die im
Testläufer nachweislich vorkommt: `getBoundingClientRect().width` ist dort 0, siehe AUF-63) · der
erste Rahmen vor dem ersten Layout · ein Wechsel zwischen 2D · Split · 3D, bei dem die alte Fläche
schon weg und die neue noch nicht vermessen ist.

**Die drei möglichen Ausgänge, und alle drei sind erlaubt:**

1. **Es gibt so einen Zustand und er ist messbar** → bauen: die Fähigkeit wird an die Messung
   gebunden, und der Grundtext wird dadurch wahr.
2. **Es gibt ihn, aber er dauert nur einen Rahmen und ist nicht beobachtbar** → **melden und
   zurückgeben.** Eine Vorbedingung, die man nur in Zeitlupe sieht, macht die Oberfläche nicht
   ehrlicher, sondern nur zappeliger.
3. **Es gibt ihn nicht** → **melden und den Posten schließen.** Dann ist die richtige Antwort:
   `viewport.ready` bei den fünf Werkzeugen **streichen** und den Grundtext mit — **eine
   Vorbedingung, die nie eintritt, ist eine Lüge in die andere Richtung.**

**Ausgang 2 und 3 sind kein Scheitern.** Der Posten steht auf der Tafel, weil niemand die Antwort
kennt; ihn mit einer erfundenen Bedingung zu füllen wäre schlechter als ihn zu schließen.

## 2. Wenn gebaut wird — die Grenzen

- **Eine Wahrheit:** die Fähigkeit wird an **einer** Stelle bestimmt (dort, wo die Bühnenmaße
  ohnehin bekannt sind), nicht zweimal. **Kein zweiter Ort, der dasselbe erneut entscheidet.**
- **Kein Flackern:** wechselt der Zustand beim Zeichnen mehrfach, ist das ein Befund. **Werkzeuge,
  die im Sekundentakt auf- und zugehen, sind schlimmer als welche, die immer offen sind.**
- **`store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.**
- **Kein neues Feld im Dokument.** Das ist ein Laufzeitzustand, kein gespeicherter Wert.

## 3. Abnahmekriterien

1. Gates: `tsc` · `schema:check` · `test:hausplaner` · `build` — Exit 0, Zahlen vorher/nachher.
2. **Die Messung ist beantwortet** — welcher der drei Ausgänge, mit Beleg. **Das ist Kriterium 1
   dieses Postens, nicht eine Vorrede.**
3. **Bei Ausgang 1:** ein Test belegt **beide** Seiten — Fläche ohne Maß ⇒ die fünf Werkzeuge
   gesperrt **mit dem Grundtext**; Fläche mit Maß ⇒ frei. Zahl der roten Zusagen bei entfernter
   Bindung nennen.
4. **Bei Ausgang 3:** `grep -c "viewport.ready"` in `werkzeugVertrag.ts` = **0**, und die fünf
   Werkzeuge sind **unverändert erreichbar** — belegt, nicht behauptet.
5. **`public/*` im Code-Commit: null Zeilen**, Bundle als eigener zweiter Commit (§8 2b) —
   **oder** der Nachweis, dass das Artefakt bytegleich ist, wie in AUF-54.
6. Klassifikation: **`sichtbar`**, wenn gebaut wird (die fünf Werkzeuge ändern ihr Aussehen);
   **`unsichtbar`** bei Ausgang 3.
