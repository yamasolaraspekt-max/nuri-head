# ⇒ GENERATOR-AUFTRAG AUF-38 — Inline-Styles ablösen

**Vom:** Planner · **25.07.2026** · **Entscheidung Yama, 25.07.:** *jetzt ablösen* (aus AUF-14,
gegen meinen Rat — Tor 1 ist seins, ich setze um).

**Vorher gelesen:** HEAD `52b403f` · `git log -5` · Tafelzeile AUF-38 (§3a) ·
`vite.hausplaner.config.ts:24-28` · `resources/views/admin/hausplaner/{objekt,studio}.blade.php` ·
`app/studioDaten.ts` (Tokens `T`) · Zählung `grep -c 'style={{'` über `app/`.

---

## 1. Der Boden ist bereits gelegt — das ist der Grund, warum das überhaupt vertretbar ist

**Gemessen, drei Fakten:**

1. `vite.hausplaner.config.ts` bildet **jede** erzeugte CSS-Datei auf `hausplaner.css` ab:
   ```
   assetFileNames: (info) => info.name?.endsWith(".css") ? "hausplaner.css" : "hausplaner.[ext]"
   ```
   Es braucht **keine** Build-Änderung. Der erste `import './x.css'` erzeugt die Datei.
2. Beide Blades binden sie bereits ein, **bewacht**:
   `@if (file_exists(public_path('hausplaner/hausplaner.css'))) <link …> @endif`
   Es braucht **keine** Blade-Änderung. Der Link aktiviert sich von selbst, sobald die Datei entsteht.
3. `studioDaten.ts` führt die Tokens `T` als **einzige** Farbwahrheit (T1 abgenommen).

**Umfang:** 331 `style={{` in 35 Dateien / 6.660 Zeilen.

| Datei | Stellen |
|---|---|
| `HausplanerApp.tsx` | **132** |
| `GuidedView.tsx` | 41 |
| `ConfigWizard.tsx` | 39 |
| `HausplanerStudio.tsx` | 34 |
| `FachFlaeche.tsx` | 27 |
| `StartView.tsx` | 20 |
| Rest (29 Dateien) | 38 |

## 2. Der Weg — Tokens als CSS-Variablen, Klassen darüber

**Kein neues Werkzeug, keine neue Abhängigkeit.** Kein CSS-in-JS, keine CSS-Module, kein
Tailwind — das wären drei Entscheidungen, die niemand getroffen hat.

1. **Eine `hausplaner.css`**, importiert einmal in `main.tsx`.
2. **Die Tokens werden Variablen**, **erzeugt aus `T`**, nicht abgeschrieben: eine Funktion setzt
   beim Start `--hp-<token>` aus `studioDaten.ts` auf das Wurzelelement. Damit bleibt `T` die einzige
   Wahrheit; ein doppelter Farbwert in der CSS wäre genau die zweite Wahrheit, die T1 beseitigt hat.
3. **Klassen statt Objekte.** Präfix `hp-`, sprechend nach Rolle (`hp-schiene`, `hp-panel-kopf`),
   nicht nach Aussehen (`hp-grau-links`).
4. **Was dynamisch ist, bleibt inline.** Ein `style={{ width: breite }}` aus einer Messung oder ein
   Wert aus dem Store gehört **nicht** in die CSS. Ziel ist nicht „null Inline-Styles", sondern
   **null statische Inline-Styles**. Wer eine berechnete Breite in eine Klasse presst, baut einen Fehler.

## 3. Schnitt — sieben Scheiben, eine nach der anderen

| # | Scheibe | Stellen |
|---|---|---|
| 1 | Grundgerüst: `hausplaner.css`, Variablen aus `T`, Import in `main.tsx` — **ohne** eine einzige Umstellung | 0 |
| 2 | `StartView.tsx` | 20 |
| 3 | `FachFlaeche.tsx` | 27 |
| 4 | `HausplanerStudio.tsx` | 34 |
| 5 | `ConfigWizard.tsx` | 39 |
| 6 | `GuidedView.tsx` | 41 |
| 7 | `HausplanerApp.tsx` **zuletzt** | 132 |
| 8 | die 29 Restdateien | 38 |

**Scheibe 1 ist ein eigener Commit und wird eigens abgenommen.** Sie beweist die Mechanik —
CSS entsteht, Blade zieht sie, Variablen kommen an — **bevor** irgendetwas umgestellt wird. Geht
dabei etwas schief, ist nichts umgebaut.

**Nie zwei Scheiben gleichzeitig.** Scheibe 7 (`HausplanerApp.tsx`) darf **erst beginnen, wenn
AUF-35a abgenommen ist** — dieselbe Datei, 132 Stellen, das ist die einzige gemessene Kollision
unter allen Layout-Posten.

## 4. Was **nicht** passiert

- **Kein gerenderter Wert ändert sich.** Keine Farbe, kein Abstand, keine Schriftgröße wird „bei der
  Gelegenheit" verbessert. Wer eine `13.5px` für krumm hält, meldet es — er korrigiert sie nicht.
- **Keine Struktur-Änderung.** Kein `div` kommt dazu, keins fällt weg. Ändert sich der DOM, ist die
  Wertgleichheit nicht mehr prüfbar.
- **Kein `!important`.** Braucht es eins, stimmt die Reihenfolge nicht — dann melden.
- **Keine Medienabfragen „schon mal mit rein".** Responsive ist L7, nicht dieser Posten.

## 5. Abnahmekriterien (je Scheibe)

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Testzahl vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
3. **Wertgleichheit, nachgewiesen statt behauptet:** für jede umgestellte Stelle steht im Bericht das
   Paar *vorher-Wert → CSS-Regel*. Bei mehr als 20 Stellen genügt eine Tabelle mit **allen**
   Eigenschaften, die vorkamen, und je einem Beleg — aber keine Stelle ohne Zuordnung.
4. **Kein roher Farbwert in der CSS:** `grep -E '#[0-9a-fA-F]{3,8}|rgba?\('` auf `hausplaner.css`
   liefert **nur** in der Variablen-Definition Treffer, nirgends in einer Regel.
5. **Die Variablen stammen aus `T`:** ein Test belegt, dass jede `--hp-*`-Variable einen Wert aus
   `studioDaten.ts` trägt und keine Konstante daneben existiert.
6. **Dynamische Styles blieben inline:** `grep` belegt, dass keine Klasse einen Wert enthält, der
   vorher aus einer Variablen kam.
7. **Mutations-Gegenbeweis:** eine Variable verfälschen ⇒ mindestens ein Test rot. Zahl nennen.
8. **`public/*` im Code-Commit: null Zeilen.** Der **Bundle-Rebuild ist ein eigener, zweiter Commit**
   unmittelbar danach (§8 Punkt 2b in `docs/agents/06-laufzeiten-und-takt.md`). **Achtung, neu:** ab
   Scheibe 1 entsteht zusätzlich `public/hausplaner/hausplaner.css` — sie gehört in **denselben**
   Bundle-Commit wie die `.js`.
9. **Klassifikation: `sichtbar`.** Sichtprobe an 1440/1024/375 px gehört in die Abnahme. Bei Scheibe 1
   lautet die Frage ausdrücklich: *sieht die Seite exakt aus wie vorher?* Ein sichtbarer Unterschied
   ist hier ein **Fehler**, kein Fortschritt.
