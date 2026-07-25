# ⇒ PLANNER — Inventur: Werkzeug-Paket gegen Bestand

**Angelegt:** 25.07.2026 · **Anlass:** Yama liefert `hausplaner_svg_toolkit.zip` — 110 SVG-Icons,
Tool-Registry als JSON und TS, Galerie, Sprite, Inventarliste. Auftrag: *„erst mal ein Inventur
machen was ist schon da was fehlt und was fehlt nachholen"*.
**Gemessen gegen** `37616cf`. **Keine Umsetzung** — das ist eine Bestandsaufnahme, kein Bau.

---

## 1. Drei Zahlen, die den Zustand erklären

| | Anzahl | Beleg |
|---|---|---|
| Werkzeuge, die die Leiste **wirklich rendert** | **9** | `app/tools/toolRegistry.ts` — `auswahl · wand · fenster · tuer · dach · decke · treppe · loeschen · duplizieren` |
| Einträge im **Katalog** | **54** | `app/tools/toolCatalog.ts` |
| Werkzeuge im **neuen Paket** | **110** | `src/tool-registry.json` |

## 2. Der Katalog ist zu 87 % fachfremd — das ist die eigentliche Nachricht

Von den **54** Katalogeinträgen haben **47 keinerlei fachliche Entsprechung** im Hausplaner. Es sind
DTP-/Layout-Werkzeuge aus dem Ursprungspaket: `page` (Seitenwerkzeug) · `type` (Textwerkzeug) · `pen`
· `add-anchor`/`delete-anchor`/`convert-anchor` · `scissors` · `eyedropper` · `fill`/`stroke`/
`swap-fill-stroke` · `swatches-panel` · `pages-panel` · `preflight` · `object-style` · acht
Ausrichtungs- und zwei Verteilungs-Werkzeuge · `normal-screen`/`preview-screen` …

Nur **7 IDs** sind in beiden Listen gleich: `line · polygon · rectangle · rotate · scale · search ·
settings`.

**Das erklärt einen Befund aus der Vorlagen-Messung:** `toolPresentation.ts` hat 63 Regeln, davon
**39 auf `versteckt`**. Die Zonen-Kuratierung versteckt nicht willkürlich — sie versteckt genau das
DTP-Erbe. Die Datei leistet damit stillschweigend die Arbeit, die eigentlich ein Katalog-Austausch
leisten müsste. Der Kommentar in `toolCatalog.ts` sagt es selbst: *„ihre Sichtbarkeit wird nicht
durch Löschen geregelt, sondern über `toolPresentation.ts` (Zone `versteckt`) — der Rückweg bleibt
damit offen."*

## 3. Was das Paket bringt: 94 Werkzeuge ohne jede Entsprechung im Repo

Und zwar **genau in den Domänen, die der Wizard als Abhängigkeitskette zeigt**:

| Domäne | fehlt bisher komplett |
|---|---|
| **Bauphysik** (4) | `u-value` · `thermal-envelope` · `insulation` · `ventilation` |
| **Heizung** (5) | `heat-pump` · `radiator` · `floor-heating` · `hydraulic-balance` · `pump` |
| **Architektur** (9) | `room` · `opening` · `floor` · `column` · `beam` · `section` · `elevation` · `dormer` · `roof-window` |
| **Import** (8) | `import-file` · `import-image` · `calibrate` · `recognize` · `approve-detection` · `crop` · `set-north` · `ai-assistant` |
| **Messen** (5) | `dimension` · `measure-distance` · `-area` · `-angle` · `-volume` |
| **CAD** (5) | `trim` · `extend` · `offset` · `join` · `split` |
| **Bearbeiten** (11) | `move` · `copy` · `mirror-h/v` · `align` · `distribute` · `group` · `lock`/`unlock` · `hide`/`show` |
| **Ansicht** (7) | `pan` · `orbit` · `zoom-in/-out` · `fit-view` · `grid` · `snap` |
| **Elektro** (5) · **PV** (3) · **Bad** (3) · **Küche** (3) · **Material** (3) · **Prüfung** (3) · **Zusammenarbeit** (3) · **Workflow** (4) · **Fassade** (2) · **TGA** (1) · **Sanitär** (1) | — |

**Die 9 heute gerenderten Werkzeuge sind alle im Paket enthalten** — unter englischen IDs
(`wand`→`wall`, `fenster`→`window`, `tuer`→`door`, `dach`→`roof`, `decke`→`slab`,
`treppe`→`stairs`, `auswahl`→`select`, `loeschen`→`delete`, `duplizieren`→`duplicate`).

## 4. Icons: der Katalog zeigt seit jeher ins Leere

`toolCatalog.ts` verweist für jeden der 54 Einträge auf `/icons/<id>.svg`. **Keine einzige dieser
Dateien existiert.** `public/hausplaner/icons/` enthält vier Unterordner (`fenster`, `heizkoerper`,
`treppe`, `tuer`) mit Bauteil-Grafiken — keine Werkzeug-Icons. Der Katalog-Kommentar sagt es offen:
*„Assets folgen separat."* **Das Paket ist genau diese fehlende Lieferung.**

## 5. Zwei Felder im Paket, die im Repo eine offene Lücke schließen

Das Paket führt je Werkzeug: `function` · `usage` · `shortcut` · `views` · `priority` · `canPin` ·
`variant`. Zwei davon sind mehr als Beiwerk:

- **`canPin`** ist genau der Werkzeug-Zustand **„angeheftet"**, der in der Vorlagen-Messung als
  fehlend geführt wurde (`grep -rnE 'angeheftet|pinned|Favorit'` → 0 Treffer im Code).
- **`priority`** (`primary`/…) bildet ab, was `toolPresentation.ts` heute als Zone `fix`/`kontext`/
  `weitere` von Hand pflegt.

Die übrigen Felder decken sich mit `ToolDefinition`: `views` ↔ `supportedViews`, `function` ↔
`meaning`/`helpText`, `usage` ↔ `usageArea`. **Ein Adapter ist möglich, ein Umbau des Schemas nicht
nötig** — die Konflikt-Regel („der neue Code passt sich dem Bestand an") ist einhaltbar.

## 6. Was fehlt dem Paket

- **Keine Zonen-Zuordnung.** `priority` ist kein Ersatz für `fix`/`kontext`/`weitere`/`versteckt`;
  die vier Zonen sind kontextabhängig, `priority` ist es nicht.
- **Keine Aktivierungsregeln.** `resolveToolState` braucht Voraussetzungen („Heizflächen vollständig",
  „Rohrnetz verbunden"). Das Paket kennt sie nicht — es ist eine Icon- und Beschreibungs-Bibliothek,
  keine Fachlogik. **Das ist kein Mangel, sondern die richtige Grenze.**
- **Kein Werkzeug erzeugt Fachdaten.** Ein `heat-pump`-Icon macht keine Wärmepumpe. Jedes der 94
  neuen Werkzeuge braucht später einen Command, ein Domänen-Objekt und eine Prüfung. **Die Icons sind
  die Fläche, nicht die Funktion** — und Yamas stehende Regel deckt das ausdrücklich: *„erst Layout
  fertig, auch wenn die Funktion nicht programmiert ist"*. Bedingung bleibt die v1-Regel: eine Fläche
  ohne Funktion **muss `in_entwicklung` ehrlich sagen**.

## 7. Was ich als Planner daraus mache — und was ich nicht entscheide

**Vorgeschlagene Schnittfolge** (jeweils eigener Auftrag, nichts davon jetzt gebaut):

- **I1** — 110 SVGs nach `public/hausplaner/icons/tools/` legen, Sprite und Galerie unter `docs/`.
  Rein additiv, kein Code. Danach zeigt der Katalog zum ersten Mal auf existierende Dateien.
- **I2** — Katalog ersetzen: die 47 DTP-Reste stilllegen (Trail erhalten, Drop als eigener Posten,
  wie es die Regel „eine Wahrheit je Sachverhalt" verlangt), die 110 Fach-Werkzeuge als neuen
  Katalog. **Adapter** vom Paket-Schema auf `ToolDefinition`, nicht umgekehrt.
- **I3** — `canPin` und `priority` in die Zonen-Kuratierung führen; das schließt den fehlenden
  Werkzeug-Zustand „angeheftet" aus den Vorlagen.
- **I4** — Aktivierungsregeln je Domäne, gestaffelt nach der Wizard-Kette. **Erst nach I2.**

**Die eine Frage, die ich nicht in Yamas Vertretung entscheide:** **ID-Sprache.** Die gerenderte
Registry ist deutsch (`wand`, `fenster`, `tuer`), Katalog und Paket sind englisch (`wall`, `window`,
`door`). Es kann nur **eine** Wahrheit geben — und die Entscheidung bestimmt, ob 9 Registry-IDs
umbenannt werden (berührt Commands, Tests, Fixtures) oder 110 Paket-IDs. **→ AUF-20, bei Yama.**
Vor dieser Entscheidung wird kein Icon einsortiert, sonst entstehen 110 Dateinamen, die man
anschließend wieder anfassen muss.
