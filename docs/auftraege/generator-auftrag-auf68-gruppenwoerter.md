# ⇒ GENERATOR-AUFTRAG AUF-68 — Die Gruppenwörter aus der Icon-Zeile

**Vom:** Planner · **26.07.2026** · **Anlass:** Yama, wörtlich: *„kannst du die Wörter Ansicht,
Messen & Export sowie Bearbeiten weg machen."* Dazu die frühere Ansage aus derselben Zeile:
*„die können auch als Icon dienen, nicht mit Worten ausgeschrieben — dafür haben wir Tooltip."*

**Vorher gelesen:** HEAD `e4f2a25` · `git log -5` · Tafelzeile AUF-68 ·
`app/HausplanerApp.tsx:651` (`opLbl`), `:1142-1175` (die Bedienleiste) · `:1144, 1160, 1170`
(die drei Aufrufe) · AUF-59 (die drei Zustände derselben Zeile, abgenommen).

**Alle Zahlen gemessen am 26.07.**

---

## 1. Der Befund — es sind genau drei Stellen und eine Hilfsfunktion

```
app/HausplanerApp.tsx:651   const opLbl = (t) => <span …>{t}</span>
app/HausplanerApp.tsx:1144  {opLbl('Ansicht')}
app/HausplanerApp.tsx:1160  {opLbl('Bearbeiten')}
app/HausplanerApp.tsx:1170  {opLbl('Messen & Export')}
```

**`opLbl` hat keinen weiteren Aufrufer** — gemessen, 3 Treffer, alle in dieser Zeile. Die Funktion
verschwindet mit ihnen; ein toter Helfer, den niemand ruft, ist genau die Sorte Rest, die später
jemand für Absicht hält.

**Die Trennstriche stehen schon da.** `opSep()` liegt heute zwischen Gruppe 1|2 und 2|3
(`:1159`, `:1169`). **Die Gruppierung geht also nicht verloren, wenn das Wort geht** — sie steht
dann in der Trennung statt in der Schrift. Das ist der Grund, warum dieser Posten klein ist.

**Gemessener Gewinn:** die drei Wörter belegen bei 1440 px zusammen rund **150 px** der Zeile — vor
den Icons, nicht neben ihnen. Nach dem Entfernen rücken die Knöpfe nach links, und die Zoom-Anzeige
rechts (`marginLeft: 'auto'`) behält ihren Platz.

## 2. Was gebaut wird

1. **Die drei `opLbl`-Aufrufe entfallen. `opLbl` selbst entfällt.** Nicht auskommentiert, nicht auf
   `''` gesetzt — entfernt.
2. **Der Name bleibt, er wird nur unsichtbar.** Jede der drei Gruppen bekommt eine Umhüllung mit
   `role="group"` und `aria-label` — „Ansicht", „Bearbeiten", „Messen & Export". **Wer die Zeile
   mit einem Vorleseprogramm bedient, darf die Gruppen nicht verlieren, nur weil sie für das Auge
   in den Trennstrichen stehen.** Das ist keine Zutat, das ist die Bedingung, unter der das Wort
   gehen darf.
3. **Sonst nichts.** Kein Knopf kommt dazu, keiner geht, keine Sperre ändert sich.

## 3. Was **nicht** gebaut wird

- **Kein Gruppen-Icon als Ersatz.** Yama hat die Wörter weggewünscht, nicht ein Bild dafür
  bestellt. Ein Icon, das eine Gruppe benennt, wäre ein vierter Knopf, der nichts tut — genau das,
  was AUF-44 gerade entfernt hat.
- **Keine neue Sortierung.** Die Reihenfolge der Knöpfe bleibt Zeichen für Zeichen dieselbe.
- **Kein Anfassen von `OpBtn`, `opSep`, der drei Zustände aus AUF-59** oder des „Ansicht
  einpassen"-Knopfes — der ist **AUF-62** und gehört nicht hierher.
- **Keine Änderung an der Themenzeile darunter** (`WerkzeugGruppenMenue`). Dort stehen andere
  Wörter; sie waren nicht gemeint.

## 4. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
3. **Die Wörter sind weg:** `grep -c "opLbl" app/HausplanerApp.tsx` = **0**.
4. **Die Knopfzahl ist unverändert:** Test zählt die Knöpfe der Bedienleiste vor und nach —
   **identisch**. *(Gemessen 25.07. im Browser: 11 Knöpfe, davon 3 gesperrt.)*
5. **Keine Sperre geändert:** die Menge der gesperrten Knöpfe ist dieselbe. Das ist dasselbe
   Kriterium, an dem AUF-59 gemessen wurde, und es gilt hier aus demselben Grund.
6. **Der Name überlebt unsichtbar:** Test belegt für alle drei Gruppen `role="group"` **und** ein
   nichtleeres `aria-label`. Ein Test, der das `aria-label` leert, wird rot — Zahl nennen.
7. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
8. **Klassifikation: `sichtbar`.** Sichtprobe gehört in die Abnahme: die Zeile im Expertenmodus,
   ein Bild oder eine gemessene Knopfliste — die Gruppen müssen **ohne die Wörter noch als Gruppen
   erkennbar** sein. Sind sie es nicht, ist das ein Befund und keine Kleinigkeit.

## 5. Was zurückgegeben wird

- **Wirkt die Zeile ohne die Wörter gedrängt**, weil die Trennstriche zu schwach sind: **melden mit
  Messung**, nicht heimlich einen Abstand erfinden. Ein stärkerer Trenner ist dann ein eigener,
  winziger Posten — und Yamas Entscheidung, nicht deine oder meine.
