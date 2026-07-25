# ⇒ DREI KLEINE GENERATOR-AUFTRÄGE — AUF-54 · AUF-55 · AUF-56

**Vom:** Planner · **25.07.2026** · **Grundlage:** Yamas Entscheidungen vom 25.07. (AUF-10/AUF-5,
AUF-13, AUF-23), teils in ausdrücklicher Vertretung getroffen und als solche gekennzeichnet.

**Vorher gelesen:** HEAD `ab7f2c1` · `git log -5` · Tafelzeilen AUF-54/55/56 (§3a) ·
`geometry/treppeSvg.ts` · `views/admin/hausplaner/objekt.blade.php:94` · `main.tsx:63` ·
`app/studioDaten.ts`.

**Warum drei in einer Datei:** Es sind drei kleine, voneinander unabhängige Posten. Sie werden
**einzeln gezogen, einzeln committet und einzeln abgenommen** — die gemeinsame Datei spart nur
Papier, nicht die Trennung.

**Für alle drei gilt** (und wird nicht je Posten wiederholt):
Gates `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` Exit 0 mit Zahlen
vorher/nachher · **K4 unberührt** · `public/*` im Code-Commit null Zeilen, Bundle-Rebuild als eigener
zweiter Commit (§8 Punkt 2b) · Mutations-Gegenbeweis mit genannter Zahl roter Tests.

---

## AUF-54 — Farbe als Parameter statt in `geometry/`

**Entscheidung Yama, 25.07.:** *„Farbe als Parameter."* Schließt AUF-5 und AUF-10.

**Gemessen:** `geometry/treppeSvg.ts` führt in den Zeilen 35–42 **sechs rohe Farbwerte**
(`#374151` · `#9ca3af` · `#93c21c` · `#6b7280` · `#e5e7eb` · `#ffffff`) und wird an **neun Stellen**
ohne Farbparameter aufgerufen. `#93c21c` ist die Treppen-Lauflinie — dieselbe Farbrolle, für die
`studioDaten.ts` `#7fae1c` führt und `szene.ts` `0xa3e635` rendert. **Drei Grüns für eine Rolle**,
belegt seit der T1-Härtung.

**Zu bauen:** Die Farben werden hereingereicht statt gekannt. Ein Standardwert bleibt zulässig, damit
die neun Aufrufstellen nicht alle gleichzeitig geändert werden müssen — **aber der Standardwert lebt
nicht in `geometry/`**, sondern kommt aus der aufrufenden Schicht.

**Nicht zu bauen:** Kein gerenderter Farbwert ändert sich. Dieser Posten verschiebt **Herkunft**,
nicht Aussehen. Ob die Lauflinie überhaupt Markenfarbe tragen soll, ist damit **nicht** entschieden —
sie behält vorerst ihren heutigen Wert.

**Kriterien zusätzlich zu den gemeinsamen:**
1. **`geometry/treppeSvg.ts` enthält keinen rohen Farbwert mehr** — `grep -E '#[0-9a-fA-F]{3,8}'` = 0
   Treffer außerhalb einer Standardwert-Angabe, die aus der Aufrufschicht stammt.
2. **Wertgleichheit:** Ein Test vergleicht das erzeugte SVG vorher/nachher **byte-genau** für
   mindestens zwei Treppenarten.
3. **`geometry/` bleibt sonst unberührt** — der Diff betrifft ausschließlich `treppeSvg.ts` und die
   neun Aufrufstellen.
4. **Klassifikation: `Vorarbeit`** — für den Nutzer ändert sich nichts.

---

## AUF-55 — Snapshot-Fläche ehrlich ausweisen

**Entscheidung 25.07.** (Planner in Yamas ausdrücklicher Vertretung): *ehrlich ausweisen, später
verbinden.*

**Gemessen:** `objekt.blade.php:94` setzt `data-snapshots-url`, `routes/web.php:5002-5008` liefern
**drei** Routen (erstellen · liste · wiederherstellen), und `main.tsx:63` liest **ausschließlich**
`dataset.speichernUrl` — **null** Snapshot-Verweise. Die Naht ist gelegt und nie verbunden worden.

**Zu bauen:** Wo im Studio ein Verlauf/Snapshot angedeutet wird, steht künftig ein **ehrlicher
Zustand** statt einer wirkungslosen Fläche: `ZustandBadge` mit `in Entwicklung` und ein Satz, der
sagt, was hier entstehen wird — nach demselben Muster wie die 19 Fachplaner-Flächen aus AUF-25.

**Ausdrücklich nicht zu bauen:**
- **Die tote URL im Blade bleibt stehen.** Sie ist die Naht, an der die spätere Anbindung ansetzt;
  wer sie entfernt, muss sie neu finden.
- **Die drei Routen bleiben unberührt.** `routes/` ist nicht Teil dieses Postens.
- **Keine Anbindung.** Wer „nur schnell" `snapshotListe` anschließt, hat einen Backend-Posten gebaut,
  den niemand beauftragt hat.

**Kriterien zusätzlich:**
1. **Kein Blindtext:** testverriegelt, dass der Hinweis nicht leer ist und nicht auf „folgt" /
   „in Kürze" / „demnächst" endet.
2. **Blade und Routen unberührt:** null Zeilen Diff in `resources/views/` und `routes/`.
3. **Klassifikation: `sichtbar`** — Sichtprobe in die Abnahme.

---

## AUF-56 — Zwei Elevation-Token einführen

**Entscheidung 25.07.** (Planner in Vertretung): *zwei Token ja, Angleichung nein.*

**Gemessen** in `app/**/*.tsx`:

```
rgba(28,40,48,.05)     9×      ← Kandidat 1
rgba(28,50,55,.10)     3×      ← Kandidat 2
rgba(28,50,55,.18)     2×
rgba(24,34,38,.30)     2×
rgba(255,255,255,.7)   1×
```

**Zu bauen:** Zwei Elevation-Rollen in `studioDaten.ts` (Vorschlag: eine flache und eine gehobene
Ebene), die die **zwölf** Vorkommen der beiden häufigsten Werte ablösen — **wertgleich**, Zeichen für
Zeichen.

**Ausdrücklich nicht zu bauen:**
- **Die rund acht „nah dran"-Werte werden nicht angeglichen.** Ihre Angleichung wäre eine **sichtbare**
  Farbänderung und bleibt Yamas Entscheidung — auch mit Vollmacht entscheidet der Planner sie nicht.
- **Die selteneren Schattenwerte** (`.18`, `.30`, das weiße) bleiben zunächst roh. Ein Token für einen
  einzigen Aufruf ist keine Rolle, sondern eine Umbenennung.

**Kriterien zusätzlich:**
1. **Wertgleichheit Zeichen für Zeichen:** ein Test belegt für beide Token, dass ihr Wert mit dem
   abgelösten Rohwert identisch ist.
2. **Zwölf Vorkommen abgelöst:** `grep -c` auf beide Rohwerte in `app/**` = **0**, ausgenommen
   `studioDaten.ts`.
3. **Kein weiterer Wert angefasst** — der Diff enthält keine andere Farbe.
4. **Klassifikation: `Vorarbeit`** — kein gerenderter Wert ändert sich, es ändert sich nur, woher er kommt.
