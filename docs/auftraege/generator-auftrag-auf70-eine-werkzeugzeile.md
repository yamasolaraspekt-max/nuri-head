# ⇒ GENERATOR-AUFTRAG AUF-70 — Eine Werkzeugzeile, und die Zustände werden lesbar

**Vom:** Planner · **26.07.2026** · **Anlass:** Yama, wörtlich: *„die zwei Werkzeuge, die als Icon
zu sehen sind, aber noch nicht aktiv sind, sind Rückgängig und das Gegenteil — bitte beide
funktionstüchtig machen. Und bitte alle Werkzeuge auf eine Linie bringen, auch 2D, Split, 3D,
Rückgängig usw. unten, wo Zoomen-Icon, Papierkorb usw. sind."*

**Vorher gelesen:** HEAD `71834e6` · `git log -6` · Tafelzeile AUF-70 ·
`app/HausplanerApp.tsx:980-984` (`knopf`), `:1060-1061` (Rückgängig/Wiederholen),
`:1103-1105` (2D · Split · 3D), `:1142-1175` (die Icon-Zeile) ·
`store/hausplanerStore.ts:120-165` · `store/history.ts` (ganze Klasse) · AUF-59 (abgenommen).

**Alle Zahlen im Browser gemessen am 26.07., `studio?fixture=decke-treppe`, 1440 px.**

---

## 1. Der erste Teil des Auftrags entfällt — Rückgängig funktioniert

**Ich habe es im laufenden Programm durchgespielt, nicht im Quelltext nachgelesen:**

| Schritt | `Rückgängig` | `Wiederholen` |
|---|---|---|
| beim Laden der Testfläche | gesperrt | gesperrt |
| nach einem echten Befehl (Fang umgeschaltet) | **frei** | gesperrt |
| nach Klick auf Rückgängig | gesperrt | **frei** |
| nach Klick auf Wiederholen | **frei** | gesperrt |

**Die Umkehr arbeitet fehlerfrei** — über Immer-Patches, mit verworfenem Wiederholen-Stapel nach
einem neuen Befehl, genau wie die Spezifikation es verlangt. **Es ist nichts kaputt.**

## 2. Warum es trotzdem so aussieht — und das ist der eigentliche Befund

**Gemessen an denselben Knöpfen:**

| | `Rückgängig` (gesperrt) | `Split` (frei) |
|---|---|---|
| Deckkraft | `1` | `1` |
| Mauszeiger | `pointer` | `pointer` |
| Schrift | `rgb(55, 65, 81)` | `rgb(55, 65, 81)` |
| Rahmen | `rgb(139, 148, 158)` | `rgb(139, 148, 158)` |
| Hintergrund | `rgb(255, 255, 255)` | `rgb(255, 255, 255)` |

**Ein gesperrter Knopf sieht in dieser Zeile Pixel für Pixel aus wie ein freier.** `knopf()`
(`:980-984`) kennt **einen** Zustand. Wer die Testfläche öffnet, ohne etwas getan zu haben, sieht
zwei Knöpfe, die aussehen wie alle anderen und nicht reagieren. **Die einzig mögliche Deutung ist
„kaputt".** Yamas Meldung ist damit völlig richtig — nur liegt der Fehler in der Darstellung, nicht
in der Funktion.

**Das ist genau der Mangel, den AUF-59 behoben hat — für `OpBtn` in der Icon-Zeile. `knopf()` ist
dabei liegengeblieben.** Eine Regel, die nur die halbe Oberfläche erreicht, ist keine Regel.

## 3. Die Zeilen heute — gemessen

| y | Inhalt | Knöpfe |
|---|---|---|
| 179 | Rückgängig · Wiederholen · Geschosse · 2D · Split · 3D · Status · Speichern | 7 |
| 224 | Arbeitsbereiche | 5 |
| 269 | Zoom ×3 · Einpassen · Raster · Fang ⏐ Duplizieren · Löschen · Spiegeln ×2 ⏐ PNG | 11 |

**Drei Zeilen, und die Werkzeuge stehen in zweien davon.** Yamas Wunsch ist damit nicht Geschmack,
sondern die Auflösung einer Doppelung.

## 4. Was gebaut wird

### (a) `knopf()` bekommt dieselben drei Zustände wie `OpBtn`

**Nicht eine zweite Regel daneben — dieselbe.** Wo AUF-59 die Zustände beschreibt, wird gelesen;
`knopf()` wird darauf gezogen. **Am Ende gibt es eine Wahrheit darüber, wie ein gesperrter Knopf
aussieht, und nicht zwei.** Wenn das eine gemeinsame Grundlage verlangt: bauen. Wenn es zwei Kopien
verlangt: **melden statt kopieren.**

### (b) Rückgängig · Wiederholen · 2D · Split · 3D ziehen in die Icon-Zeile

Zielbild: **eine** Werkzeugzeile.

```
[↶ ↷] ⏐ [2D Split 3D] ⏐ [Zoom+ Zoom− Reset Einpassen Raster Fang] ⏐ [Dupl. Löschen Spiegeln×2] ⏐ [PNG]    Zoom %
```

Reihenfolge mit Grund: **Rückgängig zuerst** — es ist die Rettungsleine und gehört an den Anfang,
nicht in die Mitte. **Dann der Ansichtsmodus**, weil er bestimmt, worauf alle folgenden Werkzeuge
wirken. Dann die vorhandenen Gruppen in ihrer heutigen Reihenfolge, **unverändert**.

**Die obere Zeile behält Geschosse, Status und Speichern** — das ist die Dokumentzeile. Sie
verschwindet nicht, sie wird ehrlich: oben das Dokument, unten das Werkzeug.

### (c) 2D · Split · 3D behalten ihre Wörter

Drei bis fünf Zeichen, drei Zustände, keine gängige Bildsprache — hier wäre ein Icon eine
Ratearbeit. **Sie übernehmen aber die Darstellung der Zeile**, in die sie ziehen; wenn `OpBtn` dafür
eine kurze Beschriftung statt eines Icons tragen können muss, ist das die kleinere Änderung als ein
zweiter Knopftyp in derselben Zeile.

## 5. Was **nicht** gebaut wird

- **Keine Änderung an Undo/Redo selbst.** `store/history.ts` und `store/` bleiben unberührt — K4.
  Die Umkehr ist gemessen in Ordnung; wer sie „bei der Gelegenheit" anfasst, repariert etwas Heiles.
- **Keine Sperre wird gelöst.** Die Menge der gesperrten Knöpfe bleibt **identisch** — dasselbe
  Kriterium wie AUF-59, aus demselben Grund.
- **Keine neue Werkzeugleiste, kein Umbau der Arbeitsbereich-Zeile** (y = 224). Die bleibt, wo sie ist.
- **Kein Zusammenlegen mit AUF-68.** Zwei Posten, zwei Voten. **AUF-70 wird erst gezogen, wenn
  AUF-68 committet ist** — beide fassen dieselben Zeilen an, und zwei gleichzeitige Änderungen an
  derselben Stelle sind der Fehler, den wir heute schon einmal hatten.

## 6. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
   *(Ausdrücklich auch `store/history.ts`.)*
3. **Eine Werkzeugzeile:** Test belegt, dass Rückgängig, Wiederholen, 2D, Split und 3D in **derselben**
   Zeile stehen wie Zoom und Löschen. **Gemessen, nicht behauptet** — heute y = 179 gegen y = 269.
4. **Der gesperrte Zustand ist ablesbar:** für `Rückgängig` im Ruhezustand unterscheidet sich
   **mindestens ein** gemessener Wert (Deckkraft, Rahmen, Hintergrund, Schriftfarbe) vom freien
   Nachbarn. **Heute unterscheidet sich keiner** — das ist die Zahl, gegen die geprüft wird.
5. **Der Mauszeiger lügt nicht:** auf einem gesperrten Knopf **nicht** `pointer`.
6. **Eine Wahrheit über Zustände:** `grep` belegt, dass es **keine zweite** Beschreibung des
   gesperrten Aussehens neben der aus AUF-59 gibt.
7. **Keine Sperre geändert:** die Menge der gesperrten Knöpfe vorher/nachher **identisch**.
8. **Die Umkehr bleibt heil, testverriegelt:** Befehl ⇒ Rückgängig frei; Rückgängig ⇒ Wiederholen
   frei; neuer Befehl nach Rückgängig ⇒ Wiederholen-Stapel leer. *(Diese drei sind heute gemessen
   richtig — der Test hält fest, dass sie es bleiben.)*
9. **Kein waagerechter Überlauf:** die Zeile trägt 16 statt 11 Knöpfe. Sichtprobe im iframe fester
   Breite bei **1440** und **1024**: `scrollWidth` = `clientWidth`. **Läuft sie über: melden** —
   dann ist Umbrechen oder Gruppieren eine eigene Entscheidung und keine stille Notlösung.
10. **Mutations-Gegenbeweis:** den gesperrten Zustand auf den freien zurücksetzen ⇒ mindestens ein
    Test rot. Zahl nennen.
11. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
12. **Klassifikation: `sichtbar`.** Sichtprobe in die Abnahme, und zwar **mit** dem Fall, der Yama
    aufgefallen ist: Testfläche frisch laden ⇒ Rückgängig muss **als gesperrt erkennbar** sein,
    nicht als kaputt.

## 7. Was zurückgegeben wird

- **Verlangt die gemeinsame Zustandsregel einen Umbau, der über diesen Posten hinausgeht:**
  melden. Dann trägt AUF-70 den Umzug, und die gemeinsame Grundlage wird ein eigener Posten. **Ein
  halber Posten mit Begründung ist besser als ein ganzer mit einer zweiten Wahrheit.**
- **Reicht die Breite bei 1024 nicht:** benennen, mit Zahl. Nicht heimlich kleiner setzen.
